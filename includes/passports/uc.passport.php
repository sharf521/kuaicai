<?php
include (ROOT_PATH . '/uc_client/client.php');

/**
 *    UCenter连接接口
 *
 *    @author    Garbin
 *    @usage    none
 */
class UcPassport extends BasePassport
{
    var $_name = 'uc';
    function tag_get($tag)
    {
        $cache_server = &cache_server();
        $cache_key    = 'uc_app_list';
        $uc_app_list  = $cache_server->get($cache_key);
        if ($uc_app_list === false)
        {
            $uc_app_list = outer_call('uc_app_ls');
            $cache_server->set($cache_key, $uc_app_list, 86400);
        }
        $nums = array();
        $related_info = array('count' => 0);
        foreach ($uc_app_list as $app_id => $app_info)
        {
            $nums[$app_id] = 10;
            $related_info['list'][$app_id] = array(
                'app_name' => $app_info['name'],
                'app_type' => $app_info['type'],
                'app_url'  => $app_info['url'],
                'data'     => array(),
            );
        }
        $data_list = outer_call('uc_tag_get', array($tag, $nums));
        if ($data_list)
        {
            foreach ($data_list as $_data_app_id => $data)
            {
                foreach ($data['data'] as $value)
                {
                    $data_key = array_keys($value);
                    array_walk($data_key, create_function('&$item, $key', '$item=\'{\' . $item . \'}\';'));
                    $item = str_replace($data_key, $value, $uc_app_list[$_data_app_id]['tagtemplates']['template']);
                    $related_info['count']++;
                    $related_info['list'][$_data_app_id]['data'][] = $item;
                }
            }
        }

        return $related_info;
    }    
}

/**
 *    UCenter的用户操作
 *
 *    @author    Garbin
 *    @usage    none
 */
class UcPassportUser extends BasePassportUser
{

    /* 注册 */
    function register($user_name, $password, $email,$owner_card, $city,$yaoqing_id, $local_data = array(),$web_id,$weiboid,$openid)
    {
        /* 到UCenter注册 */
        $user_id = outer_call('uc_user_register', array($user_name, $password, $email));
        if ($user_id < 0)
        {
            switch ($user_id)
            {
                case -1:
                    $this->_error('invalid_user_name');
                    break;
                case -2:
                    $this->_error('blocked_user_name');
                    break;
                case -3:
                    $this->_error('user_exists');
                    break;
                case -4:
                    $this->_error('email_error');
                    break;
                case -5:
                    $this->_error('blocked_email');
                    break;
                case -6:
                    $this->_error('email_exists');
                    break;
            }

            return false;
        }

		$this->kaiguan_mod =& m('kaiguan');
		$row_kaiguan=$this->kaiguan_mod->getRow("select webservice from ".DB_PREFIX."kaiguan");
		$webservice=$row_kaiguan['webservice'];
		if($webservice=='yes')	
		{	
			//webservice对接 无推荐人 注册 
			$web_id= webService('Regist',array()); 
			//结束 
		}

        /* 同步到本地 */
        $local_data['user_name']    = $user_name;
        $local_data['password']     = md5($password);
        $local_data['email']        = $email;
		$local_data['city']        = $city;
		$local_data['owner_card']        = $owner_card;
		$local_data['yaoqing_id']        = $yaoqing_id;
		$local_data['web_id']        = $web_id;
		$local_data['weiboid']        = $weiboid;
		$local_data['openid']        = $openid;
        $local_data['reg_time']     = time();
        $local_data['user_id']      = $user_id;

        /* 添加到用户系统 */
        $user_id=$this->_local_add($local_data);

        return $user_id;
    }

    /* 编辑用户数据 */
    function edit($user_id, $old_password, $items, $force = false)
    {
        $new_pwd = $new_email = '';
        if (isset($items['password']))
        {
            $new_pwd  = $items['password'];
        }
        if (isset($items['email']))
        {
            $new_email = $items['email'];
        }
		
		
		if (isset($items['city']))
        {
            $new_city = $items['city'];
        }
		
		
		$this->member_mod =& m('member');
		$re=$this->member_mod->getRow("select user_name from ".DB_PREFIX."member where user_id=$user_id limit 1");
		$user_name = $re['user_name'];
		
        $info = $this->get($user_name,true);
        if (empty($info))
        {
            $this->_error('no_such_user');

            return false;
        }

        /* 先到UCenter修改 */
        $result = outer_call('uc_user_edit', array($info['user_name'], $old_password, $new_pwd, $new_email, $force));
        if ($result != 1)
        {
            switch ($result)
            {
                case 0:
                case -7:
                    return true;
                    break;
                case -1:
                    $this->_error('auth_failed');

                    return false;
                    break;
                case -4:
                    $this->_error('email_error');

                    return false;
                    break;
                case -5:
                    $this->_error('blocked_email');

                    return false;
                    break;
                case -6:
                    $this->_error('email_exists');

                    return false;
                    break;
                case -8:
                    $this->_error('user_protected');

                    return false;
                    break;
                default:
                    $this->_error('unknow_error');

                    return false;
                    break;
            }
        }

        /* 成功后编辑本地数据 */
        $local_data = array();
        if ($new_pwd)
        {
            $local_data['password'] = md5(time() .  rand(100000, 999999));
        }
        if ($new_email)
        {
            $local_data['email']    = $new_email;
        }
		if ($new_city)
        {
            $local_data['city']    = $new_city;
        }

        //编辑本地数据
        $this->_local_edit($user_id, $local_data);

        return true;
    }

    /* 删除用户 */
    function drop($user_id)
    {
        if (empty($user_id))
        {
            $this->_error('no_such_user');

            return false;
        }

        /* 先到UCenter中删除 */
        $result = outer_call('uc_user_delete', array($user_id));
        outer_call('uc_user_deleteavatar', array($user_id));
        if (!$result)
        {
            $this->_error('uc_drop_user_failed');

            return false;
        }

        /* 再删除本地的 */
        return $this->_local_drop($user_id);
    }

    /* 获取用户信息 */
    function get($flag, $is_name = false)
    {
        /* 至UCenter取用户 */
        $user_info = outer_call('uc_get_user', array($flag, !$is_name));
        if (empty($user_info))
        {
            $this->_error('no_such_user');

            return false;
        }
        list($user_id, $user_name, $email) = $user_info;

        /* 同步至本地 */
        //$this->_local_sync($user_id, $user_name, $email);

        return array(
                'user_id'   =>  $user_id,
                'user_name' =>  $user_name,
                'email'     =>  $email,
                'portrait'  =>  portrait($user_id, '')
                );
    }

    /**
     *    验证用户登录
     *
     *    @author    Garbin
     *    @param     $string $user_name
     *    @param     $string $password
     *    @return    int    用户ID
     */
    function auth($user_name, $password)
    {
        register_shutdown_function('restore_error_handler'); // 恢复PHP系统默认的错误处理
        $result = outer_call('uc_user_login', array($user_name, $password));

		$_SESSION['pass']=$password;
        if ($result[0] < 0)
        {
            switch ($result[0])
            {
                case -1:
                    $this->_error('no_such_user');
                    break;
                case -2:
                    $this->_error('password_error');
                    break;
                case -3:
                    $this->_error('answer_error');
                    break;
                default:
                    $this->_error('unknow_error');
                    break;
            }

            return false;
        }

        /* 同步到本地 */
        $this->_local_sync($result[0], $result[1], $result[3]);

        /* 返回用户ID */
        return $result[0];
    }

    /**
     *    同步登录
     *
     *    @author    Garbin
     *    @param     int $user_id
     *    @return    string
     */
    function synlogin($user_id)
    {
        return outer_call('uc_user_synlogin', array($user_id));
    }

    /**
     *    同步退出
     *
     *    @author    Garbin
     *    @return    string
     */
    function synlogout()
    {

        return outer_call('uc_user_synlogout');
    }

    /**
     *    检查电子邮件是否唯一
     *
     *    @author    Garbin
     *    @param     string $email
     *    @return    bool
     */
    function check_email($email)
    {
        $result = outer_call('uc_user_checkemail', array($email));
        if ($result < 0)
        {
            switch ($result)
            {
                case -4:
                    $this->_error('email_error');
                    break;
                case -5:
                    $this->_error('blocked_email');
                    break;
                case -6:
                    $this->_error('email_exists');
                    break;
                default:
                    $this->_error('unknow_error');
                    break;
            }

            return false;
        }

        return true;
    }

    /**
     *    检查用户名是否唯一
     *
     *    @author    Garbin
     *    @param     string $user_name
     *    @return    bool
     */
    function check_username($user_name)
    {
        $result = outer_call('uc_user_checkname', array($user_name));
        if ($result < 0)
        {
            switch ($result)
            {
                case -1:
                    $this->_error('invalid_user_name');
                    break;
                case -2:
                    $this->_error('blocked_user_name');
                    break;
                case -3:
                    $this->_error('user_exists');
                    break;
                default:
                    $this->_error('unknow_error');
                    break;
            }
            return false;
        }

        return true;
    }

    /**
     *    设置头像
     *
     *    @author    Garbin
     *    @param     int $user_id
     *    @return    string
     */
    function set_avatar($user_id = 0)
    {
        return outer_call('uc_avatar', array($user_id));
    }

    /**
     *    删除头像
     *
     *    @author    Garbin
     *    @param     int $user_id
     *    @return    bool
     */
    function drop_avatar($user_id)
    {
        return outer_call('uc_user_deleteavatar', array($user_id));
    }
}

/**
 *    内置用户中心的短信操作
 *
 *    @author    Garbin
 *    @usage    none
 */
 class UcPassportPM extends BasePassportPM
{
    var $show_announce;
    function __construct()
    {
        $this->UcPassportPM();
    }
    function UcPassportPM()
    {
        $this->show_announce = false;
        Lang::load(ROOT_PATH . '/includes/passports/' . MEMBER_TYPE . '/' . LANG . '/common.lang.php');
        if (file_exists(ROOT_PATH . '/data/msg.lang.php'))
        {
            Lang::load(ROOT_PATH . '/data/msg.lang.php');
        }
    }
    /**
     *    发送短消息
     *
     *    @author    Garbin
     *    @param     int $sender        发送者
     *    @param     array $recipient     接收者
     *    @param     string $subject    标题
     *    @param     string $message    内容
     *    @param     int $replyto       回复主题
     *    @return    false:失败   true:成功
     */
    function send($sender, $recipient, $subject, $message, $replyto = 0)
    {
        $model_message =& m('message');
        $msg_id = $model_message->send($sender, $recipient, '', $message, $replyto);
        if (!$msg_id)
        {
            $this->_errors = $model_message->get_error();

            return 0;
        }

        return $msg_id;
    }

    /**
     *    获取短消息内容
     *
     *    @author    Garbin
     *    @param     int  $user_id  拥有者
     *    @param     int  $pm_id    短消息标识
     *    @param     bool $full     是否包括回复 false:不包括 true包括
     *    @return    false:没有消息 array:消息内容
     */
    function get($user_id, $pm_id, $full = false)
    {
        $model_message =& m('message');
        $topic = $model_message->get(array(
            'fields'     => 'this.*',
            'conditions' => 'msg_id=' . $pm_id . ' AND parent_id=0 AND ((status IN (1,3) AND to_id = ' . $user_id . ') OR (status IN (2,3) AND from_id = ' . $user_id . '))',
        ));
        if (empty($topic))
        {
            return array();
        }
        if ($topic['from_id'] == MSG_SYSTEM)
        {
            $topic['user_name'] = Lang::get('system_message');
            $topic['system'] = 1;
        }
        $topic['new'] = (($topic['from_id'] == $user_id && $topic['new'] == 2)||($topic['to_id'] == $user_id && $topic['new'] == 1 )) ? 1 : 0;
        $topic['portrait'] = portrait($topic['from_id'], $topic['portrait']);
        if ($full)
        {
            $replies = $model_message->find(array(
                'fields'     => 'this.*',
                'conditions' => 'parent_id=' . $pm_id,
            ));
        }

        return array(
            'topic' => $topic,
            'replies' => $replies
        );
    }

    /**
     *    获取消息列表
     *
     *    @author    Garbin
     *    @param     int    $user_id
     *    @param     string $limit
     *    @param     string $folder 可选值:inbox, outbox
     *    @return    array:消息列表
     */
    function get_list($user_id, $page, $folder = 'privatepm')
    {
        $limit = $page['limit'];
        $condition = '';
        switch ($folder)
        {
            case 'privatepm':
                $condition = '((from_id = ' . $user_id . ' AND status IN (2,3)) OR (to_id = ' . $user_id . ' AND status IN (1,3)) AND from_id > 0)';
            break;
            case 'systempm':
                $condition = 'from_id = ' . MSG_SYSTEM . ' AND to_id = ' . $user_id;
            break;
            case 'announcepm':
                $condition = 'from_id = 0 AND to_id = 0';
            break;
            default:
                $condition = '((new = 1 AND status IN (1,3) AND to_id = ' . $user_id . ') OR (new =2 AND status IN (2,3) AND from_id = ' . $user_id . '))';
            break;
        }
        $model_message =& m('message');
        $messages = $model_message->find(array(
            'fields'        =>'this.*',
            'conditions'    =>  $condition .' AND parent_id=0 ',
            'count'         => true,
            'limit'         => $limit,
            'order'         => 'last_update DESC',
        ));
        $subject = '';
        if (!empty($messages))
        {
            foreach ($messages as $key => $message)
            {
                $messages[$key]['new'] = (($message['from_id'] == $user_id && $message['new'] == 2)||($message['to_id'] == $user_id && $message['new'] == 1 )) ? 1 : 0; //判断是否是新消息
                $subject = $this->removecode($messages[$key]['content']);
	
               // $messages[$key]['content'] = htmlspecialchars($subject);
				 $messages[$key]['content'] = ($subject);
                $message['from_id'] == MSG_SYSTEM && $messages[$key]['user_name'] = Lang::get('system_message'); //判断是否是系统消息
            }
        }
        return array(
            'count' => $model_message->getCount(),
            'data' => $messages
        );
    }
    
    function removecode($str) {
        $rs = trim(preg_replace(array(
            "/\[(img)=?.*\].*?\[\/(img)\]/siU",
            "/\[\/?(url)=?.*\]/siU",
            "/\r\n/",
            ), '', $str));
        return $rs;
    }


    
    /**
     *    检查是否有短消息
     *
     *    @author    Garbin
     *    @param     int $user_id
     *    @return    false:无新短消息 ture:有新短消息
     */
    function check_new($user_id)
    {
        $model_message =& m('message');
        
        $new = $model_message->check_new($user_id);
        return $new['total'];
    }

    /**
     *    删除短消息
     *
     *    @author    Garbin
     *    @param     int        $user_id 短消息拥有者
     *    @param     array      $pm_ids  欲删除的短消息
     *    @param     string     $foloder    可选值:inbox,outbox
     *    @return    false:失败   true:成功
     */
    function drop($user_id, $pm_ids)
    {
        $model_message =& m('message');
        if (!$model_message->msg_drop($pm_ids, $user_id))
        {
            $this->_errors = $model_message->get_error();

            return false;
        }

        return true;
    }

    /**
     *    标记阅读状态
     *
     *    @author    Garbin
     *    @param     int   $user_id   短消息拥有者
     *    @param     array $pm_ids    欲标记的短消息ID数组
     *    @param     int   $status    标记成的状态，0为已读，1为未读
     *    @return    false:标记失败  true:标记成功
     */
    function mark($user_id, $pm_ids, $status = 0)
    {
        
        $model_message =& m('message');
        $model_message->edit($pm_ids, array(
            'new'   => $status,
        ));

        return (!$model_message->has_error());
    }
    
    /**
     *  短消息过滤
     *
     *  @return string 
     */
    function msg_filter($message)
    {
        $message = htmlspecialchars($message);
        if(strpos($message, '[/url]') !== FALSE)
        {
            $message = preg_replace("/\[url(=((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k|thunder|synacast){1}:\/\/|www\.)([^\[\"']+?))?\](.+?)\[\/url\]/ies", "\$this->parseurl('\\1', '\\5')", $message);
        }
        if(strpos($message, '[/img]') !== FALSE)
        {
            $message = preg_replace(array(
                "/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies",
                "/\[img=(\d{1,4})[x|\,](\d{1,4})\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies"
                ), array(
                "\$this->bbcodeurl('\\1', '<img src=\"%s\" border=\"0\" alt=\"\" />')",
                "\$this->bbcodeurl('\\3', '<img width=\"\\1\" height=\"\\2\" src=\"%s\" border=\"0\" alt=\"\" />')"),
                $message);
        }
        return nl2br(str_replace(array("\t", '   ', '  '), array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'), $message));
    }
         
    function bbcodeurl($url, $tags) 
    {
        if(!preg_match("/<.+?>/s", $url)) 
        {
            if(!in_array(strtolower(substr($url, 0, 6)), array('http:/', 'https:', 'ftp://', 'rtsp:/', 'mms://'))) 
            {
                $url = 'http://'.$url;
            }
            return str_replace(array('submit', 'logging.php'), array('', ''), sprintf($tags, $url, addslashes($url)));
        } 
        else 
        {
            return '&nbsp;'.$url;
        }
    }
    
    function parseurl($url, $text) 
    {
        if(!$url && preg_match("/((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|ed2k|thunder|synacast){1}:\/\/|www\.)[^\[\"']+/i", trim($text), $matches))
        {
            $url = $matches[0];
            $length = 65;
            if(strlen($url) > $length)
            {
                $text = substr($url, 0, intval($length * 0.5)).' ... '.substr($url, - intval($length * 0.3));
            }
            return '<a href="'.(substr(strtolower($url), 0, 4) == 'www.' ? 'http://'.$url : $url).'" target="_blank">'.$text.'</a>';
        }
        else
        {
            $url = substr($url, 1);
            if(substr(strtolower($url), 0, 4) == 'www.')
            {
                $url = 'http://'.$url;
            }
            return '<a href="'.$url.'" target="_blank">'.$text.'</a>';
        }
    }
}
 

/**
 *    内置用户中心的好友操作
 *
 *    @author    Garbin
 *    @usage    none
 */
 class UcPassportFriend extends BasePassportFriend
{
    /**
     *    新增一个好友
     *
     *    @author    Garbin
     *    @param     int $user_id       好友拥有者
     *    @param     array $friend_ids    好友
     *    @return    false:失败 true:成功
     */
    function add($user_id, $friend_ids)
    {
        $model_member =& m('member');
        $user_data = array();
        foreach ($friend_ids as $friend_id)
        {
            if ($friend_id == $user_id)
            {
                $this->_error('cannot_add_myself');

                return false;
            }
            $user_data[$friend_id] = array(
                'add_time'  => gmtime()
            );
        }

        return $model_member->createRelation('has_friend', $user_id ,$user_data);
    }

    /**
     *    删除一个好友
     *
     *    @author    Garbin
     *    @param     int $user_id       好友拥有者
     *    @param     array $friend_id     好友
     *    @return    false:失败   true:成功
     */
    function drop($user_id, $friend_ids)
    {
        $model_member =& m('member');

        return $model_member->unlinkRelation('has_friend', $user_id ,$friend_ids);
    }

    /**
     *    获取好友总数
     *
     *    @author    Garbin
     *    @param     int $user_id       好友拥有者
     *    @return    int    好友总数
     */
    function get_count($user_id)
    {
        $model_member =& m('member');

        return count($model_member->getRelatedData('has_friend', array($user_id)));
    }

    /**
     *    获取好友列表
     *
     *    @author    Garbin
     *    @param     int $user_id       好友拥有者
     *    @param     string $limit      条数
     *    @return    array  好友列表
     */
    function get_list($user_id, $limit = '0, 10')
    {
        $model_member =& m('member');
        $friends = $model_member->getRelatedData('has_friend', array($user_id), array(
            'limit' => $limit,
            'order' => 'add_time DESC',
        ));
        if (empty($friends))
        {
            $friends = array();
        }
        else
        {
            foreach ($friends as $_k => $f)
            {
                $friends[$_k]['portrait'] = portrait($f['user_id'], $f['portrait']);
            }
        }

        return $friends;
    }
    
}

/**
 *    UCenter的事件操作
 *
 *    @author    Garbin
 *    @usage    none
 */
class UcPassportFeed extends BasePassportFeed
{
    /**
     *    添加事件
     *
     *    @author    Garbin
     *    @param     array $feed    事件
     *    @return    false:失败   true:成功
     */
    function add($event, $data)
    {
        $feed_info = $this->_get_feed_info($event, $data);
        return outer_call('uc_feed_add', array($feed_info['icon'], $feed_info['user_id'], $feed_info['user_name'], $feed_info['title']['template'], $feed_info['title']['data'], $feed_info['body']['template'], $feed_info['body']['data'], $feed_info['body_general'], $feed_info['target_ids'], $feed_info['images']));
    }

    /**
     * 通过事件和数据获取feed详细内容
     *
     * @author Garbin
     * @param
     * @return void
     **/
    function _get_feed_info($event, $data)
    {
        $mall_name = '<a href="' . SITE_URL . '">' . Conf::get('site_name') . '</a>';
        switch ($event)
        {
            case 'order_created':
                $feed = array(
                    'icon'  => 'goods',
                    'user_id'  => $data['user_id'],
                    'user_name'  => $data['user_name'],
                    'title'  => array(
                        'template'  => Lang::get('feed_order_created.title'),
                        'data'      => array(
                            'store'    => '<a href="' . $data['store_url'] . '">' . $data['seller_name'] . '</a>',
                            ),
                        ),
                    'body'  => array(
                        'template'  => Lang::get('feed_order_created.body'),
                        ),
                    'images' => $data['images'],
                );
                break;
            case 'store_created':
                $feed = array(
                    'icon'  => 'profile',
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'title' => array(
                        'template' => Lang::get('feed_store_created.title'),
                        'data' => array(
                            'mall_name' => $mall_name,
                            'store' => '<a href="' . $data['store_url'] . '">' . $data['seller_name'] . '</a>',

                        ),
                    ),
                    'body'  => array(
                        'template'  => Lang::get('feed_store_created.body'),
                        'data' => array(),
                    ),
                );
                break;
            case 'goods_created':
                $feed = array(
                    'icon' => 'goods',
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'title' => array(
                        'template' => Lang::get('feed_goods_created.title'),
                        'data' => array(
                            'goods' => '<a href="' . $data['goods_url'] . '">' . $data['goods_name'] . '</a>'
                        ),
                    ),
                    'body' => array(
                        'template' => Lang::get('feed_goods_created.body'),
                        'data' => array(),
                    ),
                    'images' => $data['images']
                );
                break;
            case 'groupbuy_created':
                $feed = array(
                    'icon' => 'goods',
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'title' => array(
                        'template' => Lang::get('feed_groupbuy_created.title'),
                        'data' => array(
                            'groupbuy' => '<a href="' . $data['groupbuy_url'] . '">' . $data['groupbuy_name'] . '</a>'
                        ),
                    ),
                    'body' => array(
                        'template' => Lang::get('feed_groupbuy_created.body'),
                        'data' => array(
                            'groupbuy_message' => $data['message']
                        ),
                    ),
                    'images' => $data['images']
                );
                break;
            case 'goods_collected':
                $feed = array(
                    'icon' => 'goods',
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'title' => array(
                        'template' => Lang::get('feed_goods_collected.title'),
                        'data' => array(
                            'goods' => '<a href="' . $data['goods_url'] . '">' . $data['goods_name'] . '</a>'
                        ),
                    ),
                    'body' => array(
                        'template' => Lang::get('feed_goods_collected.body'),
                        'data' => array(),
                    ),
                    'images' => $data['images']
                );
                break;
            case 'store_collected':
                $feed = array(
                    'icon' => 'goods',
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'title' => array(
                        'template' => Lang::get('feed_store_collected.title'),
                        'data' => array(
                            'store' => '<a href="' . $data['store_url'] . '">' . $data['store_name'] . '</a>'
                        ),
                    ),
                    'body' => array(
                        'template' => Lang::get('feed_store_collected.body'),
                        'data' => array(),
                    ),
                    'images' => $data['images']
                );
                break;
            case 'goods_evaluated':
                $feed = array(
                    'icon' => 'goods',
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'title' => array(
                        'template' => Lang::get('feed_goods_evaluated.title'),
                        'data' => array(
                            'goods' => '<a href="' . $data['goods_url'] . '">' . $data['goods_name'] . '</a>',
                            'evaluation' => $data['evaluation'],
                        ),
                    ),
                    'body' => array(
                        'template' => Lang::get('feed_goods_evaluated.body'),
                        'data' => array(
                            'comment' => $data['comment'],
                        ),
                    ),
                    'images' => $data['images']
                );
                break;
            case 'groupbuy_joined':
                $feed = array(
                    'icon' => 'goods',
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'title' => array(
                        'template' => Lang::get('feed_groupbuy_joined.title'),
                        'data' => array(
                            'groupbuy' => '<a href="' . $data['groupbuy_url'] . '">' . $data['groupbuy_name'] . '</a>'
                        ),
                    ),
                    'body' => array(
                        'template' => Lang::get('feed_groupbuy_joined.body'),
                        'data' => array(),
                    ),
                    'images' => $data['images']
                );
                break;
        }

        return $feed;
    }

    /**
     *    获取事件
     *
     *    @author    Garbin
     *    @param     int $limit     条数
     *    @return    array
     */
    function get($limit) {}

    /**
     * 判断feed是否启用
     *
     * @author Garbin
     * @return bool
     **/
    function feed_enabled()
    {
        $feed_enabled = null;
        if ($feed_enabled === null)
        {
            $cache_server =& cache_server();
            $cache_key = 'feed_enabled';
            $feed_enabled = $cache_server->get($cache_key);
            if ($feed_enabled === false)
            {
                $feed_enabled = 0;
                $app_list = outer_call('uc_app_ls');
                if ($app_list)
                {
                    foreach ($app_list as $app)
                    {
                        if ($app['type'] == 'UCHOME')
                        {
                            $feed_enabled = $app;
                        }
                    }
                }
                $cache_server->set($cache_key, $feed_enabled, 86400);
            }
        }

        return $feed_enabled;
    }
}

?>
