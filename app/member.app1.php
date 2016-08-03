<?php

/**
 *    Desc
 *
 *    @author    Garbin
 *    @usage    none
 */
 

class MemberApp extends MemberbaseApp
{
    var $_feed_enabled = false;
    function __construct()
    {
        $this->MemberApp();
    }
    function MemberApp()
    {
        parent::__construct();
        $ms =& ms();
        $this->_feed_enabled = $ms->feed->feed_enabled();
        $this->assign('feed_enabled', $this->_feed_enabled);
    }
    function index()
    {
	  $this->my_money_mod =& m('my_money');
	  $this->member_mod =& m('member');
	 $user_id = $this->visitor->get('user_id'); 
	 $canshu=$this->member_mod->can();

$my_money=$this->my_money_mod->getAll("select * from ".DB_PREFIX."my_money where user_id=$user_id");
   $jie=$this->my_money_mod->getRow("select * from ".DB_PREFIX."jiekuan where user_id = '$user_id'");
		$this->assign('jie',$jie);
		foreach ($my_money as $key=>$my)
		{
	
			$my_money[$key]['zengjin']=round($my['zengjin']/$canshu['jifenxianjin'],2);
			$dengji=$my['t'];
			$le=dengji($dengji);
			$jk=jiekuan($dengji);
		
		}
		  $this->assign('my_money', $my_money); 
$qiandao=$this->my_money_mod->getRow("select riqi,times,status from ".DB_PREFIX."qiandao where user_id=$user_id");
	$riqi=date('Y-m-d');
	 $mfbb=$this->member_mod->getRow("select fbb,daxiaozhuo,level,vip from ".DB_PREFIX."member where user_id=$user_id limit 1");
	$this->assign('mfbb', $mfbb); 
	$this->assign('qiandao', $qiandao); 
	$this->assign('riqi', $riqi); 
    $eve=$mfbb['level'];

	//$le=level($eve);
	$this->assign('le',$le);
	$this->assign('dengji',$dengji);
	$this->assign('jk',$jk);

	

        /* 清除新短消息缓存 */
        $cache_server =& cache_server();
        $cache_server->delete('new_pm_of_user_' . $this->visitor->get('user_id'));

        $user = $this->visitor->get();
        $user_mod =& m('member');
        $info = $user_mod->get_info($user['user_id']);
        $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
        $this->assign('user', $user);

        /* 店铺信用和好评率 */
        if ($user['has_store'])
        {
            $store_mod =& m('store');
            $store = $store_mod->get_info($user['has_store']);
            $step = intval(Conf::get('upgrade_required'));
            $step < 1 && $step = 5;
            $store['credit_image'] = $this->_view->res_base . '/images/' . $store_mod->compute_credit($store['credit_value'], $step);
            $this->assign('store', $store);
            $this->assign('store_closed', STORE_CLOSED);
        }
        $goodsqa_mod = & m('goodsqa');
        $groupbuy_mod = & m('groupbuy');
        /* 买家提醒：待付款、待确认、待评价订单数 */
        $order_mod =& m('order');
        $sql1 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE buyer_id = '{$user['user_id']}' AND status = '" . ORDER_PENDING . "'";
        $sql2 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE buyer_id = '{$user['user_id']}' AND status = '" . ORDER_SHIPPED . "'";
        $sql3 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE buyer_id = '{$user['user_id']}' AND status = '" . ORDER_FINISHED . "' AND evaluation_status = 0";
        $sql4 = "SELECT COUNT(*) FROM {$goodsqa_mod->table} WHERE user_id = '{$user['user_id']}' AND reply_content !='' AND if_new = '1' ";
        $sql5 = "SELECT COUNT(*) FROM " . DB_PREFIX ."groupbuy_log AS log LEFT JOIN {$groupbuy_mod->table} AS gb ON gb.group_id = log.group_id WHERE log.user_id='{$user['user_id']}' AND gb.state = " .GROUP_CANCELED;
        $sql6 = "SELECT COUNT(*) FROM " . DB_PREFIX ."groupbuy_log AS log LEFT JOIN {$groupbuy_mod->table} AS gb ON gb.group_id = log.group_id WHERE log.user_id='{$user['user_id']}' AND gb.state = " .GROUP_FINISHED;
        $buyer_stat = array(
            'pending'  => $order_mod->getOne($sql1),
            'shipped'  => $order_mod->getOne($sql2),
            'finished' => $order_mod->getOne($sql3),
            'my_question' => $goodsqa_mod->getOne($sql4),
            'groupbuy_canceled' => $groupbuy_mod->getOne($sql5),
            'groupbuy_finished' => $groupbuy_mod->getOne($sql6),
        );
        $sum = array_sum($buyer_stat);
        $buyer_stat['sum'] = $sum;
        $this->assign('buyer_stat', $buyer_stat);

        /* 卖家提醒：待处理订单和待发货订单 */
        if ($user['has_store'])
        {

            $sql7 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE seller_id = '{$user['user_id']}' AND status = '" . ORDER_SUBMITTED . "'";
            $sql8 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE seller_id = '{$user['user_id']}' AND status = '" . ORDER_ACCEPTED . "'";
            $sql9 = "SELECT COUNT(*) FROM {$goodsqa_mod->table} WHERE store_id = '{$user['user_id']}' AND reply_content ='' ";
            $sql10 = "SELECT COUNT(*) FROM {$groupbuy_mod->table} WHERE store_id='{$user['user_id']}' AND state = " .GROUP_END;
            $seller_stat = array(
                'submitted' => $order_mod->getOne($sql7),
                'accepted'  => $order_mod->getOne($sql8),
                'replied'   => $goodsqa_mod->getOne($sql9),
                'groupbuy_end'   => $goodsqa_mod->getOne($sql10),
            );

            $this->assign('seller_stat', $seller_stat);
        }
        /* 卖家提醒： 店铺等级、有效期、商品数、空间 */
        if ($user['has_store'])
        {
            $store_mod =& m('store');
            $store = $store_mod->get_info($user['has_store']);

            $grade_mod = & m('sgrade');
            $grade = $grade_mod->get_info($store['sgrade']);

            $goods_mod = &m('goods');
            $goods_num = $goods_mod->get_count_of_store($user['has_store']);
            $uploadedfile_mod = &m('uploadedfile');
            $space_num = $uploadedfile_mod->get_file_size($user['has_store']);
            $sgrade = array(
                'grade_name' => $grade['grade_name'],
                'add_time' => empty($store['end_time']) ? 0 : sprintf('%.2f', ($store['end_time'] - gmtime())/86400),
                'goods' => array(
                    'used' => $goods_num,
                    'total' => $grade['goods_limit']),
                'space' => array(
                    'used' => sprintf("%.2f", floatval($space_num)/(1024 * 1024)),
                    'total' => $grade['space_limit']),
                    );
            $this->assign('sgrade', $sgrade);

        }
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    url('app=member'),
                         LANG::get('overview'));

        /* 当前用户中心菜单 */
        $this->_curitem('overview');
        $this->assign('page_title', Lang::get('member_center'));
        $this->display('member.index.html');
    }



    /**
     *    注册一个新用户
     *
     *    @author    Garbin
     *    @return    void
     */
    function register()
    {
	
	$weiboid=ecm_getcookie('weiboid');
	$openid=ecm_getcookie('openid');
	$this->assign('weiboid',$weiboid);
	$this->assign('openid',$openid);
	//$tuijian = empty($_GET['id']) ? null : trim($_GET['id']);
	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);//推荐人的id
	//echo $id;
	 $this->member_mod =& m('member');
	 $us=$this->member_mod->getRow("select user_name from ".DB_PREFIX."member where user_id = '$id' limit 1");
	 $this->assign('us', $us);
	 $user_name=$us['user_name'];//推荐人的用户名
	 //echo $user_name;
	 
	 
	// $url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	
	
        if ($this->visitor->has_login)
        {
            $this->show_warning('has_login');

            return;
        }
        if (!IS_POST)
        {
            if (!empty($_GET['ret_url']))
            {
                $ret_url = trim($_GET['ret_url']);
            }
            else
            {
                if (isset($_SERVER['HTTP_REFERER']))
                {
                    $ret_url = $_SERVER['HTTP_REFERER'];
                }
                else
                {
                    $ret_url = SITE_URL . '/index.php';
                }
            }
		
            $this->assign('ret_url', rawurlencode($ret_url));
            $this->_curlocal(LANG::get('user_register'));
            $this->assign('page_title', Lang::get('user_register') . ' - ' . Conf::get('site_title'));

            if (Conf::get('captcha_status.register'))
            {
                $this->assign('captcha', 1);
            }

            /* 导入jQuery的表单验证插件 */
            $this->import_resource('jquery.plugins/jquery.validate.js');                                                     
            $this->display('member.register.html');
        }
        else
        {
            if (!$_POST['agree'])
            {
                $this->show_warning('agree_first');

                return;
            }
            if (Conf::get('captcha_status.register') && base64_decode($_SESSION['captcha']) != strtolower($_POST['captcha']))
            {
                $this->show_warning('captcha_failed');
                return;
            }
            if ($_POST['password'] != $_POST['password_confirm'])
            {
                /* 两次输入的密码不一致 */
                $this->show_warning('inconsistent_password');
                return;
            }

            /* 注册并登录 */
            $user_name = trim($_POST['user_name']);
			$yaoqing_id = trim($_POST['yaoqing_id']);
			$password  = $_POST['password'];
            $email     = trim($_POST['email']);
			$owner_card     = trim($_POST['owner_card']);
			/*$city     = trim($_POST['city']);*/
			$city=$city_id;
            $passlen = strlen($password);
            $user_name_len = strlen($user_name);
			
            if ($user_name_len < 3 || $user_name_len > 25)
            {
                $this->show_warning('user_name_length_error');

                return;
            }
            if ($passlen < 6 || $passlen > 20)
            {
                $this->show_warning('password_length_error');

                return;
            }
            if (!is_email($email))
            {
                $this->show_warning('email_error');

                return;
            }

			if (empty($owner_card))
            {
                $this->show_warning('shenfenzhengbunengweikong');

                return;
            }
			include_once(ROOT_PATH. '/includes/idcheck.class.php');
	
			$chk=new IDCheck($owner_card);
			if(($chk->Part())==False)
			{
			$this->show_warning('shurushenfenzheng');
			return;
			}
			
            $ms =& ms(); //连接用户中心
            $user_id = $ms->user->register($user_name, $password, $email,$owner_card,$city,$yaoqing_id,array(),$web_id,$weiboid,$openid);

            if (!$user_id)
            {
                $this->show_warning($ms->user->get_error());

                return;
            }
            $this->_hook('after_register', array('user_id' => $user_id));
            //登录
            $this->_do_login($user_id);
			//更新推荐人的用户资金
			$this->canshu_mod =& m('canshu');
			$this->message_mod =& m('message');
$can=$this->canshu_mod->can();
$jiang=$can['tuijianjiangli'];//奖励的荣誉积分

$mber=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id'");//新注册的用户id

$tuijianren=$mber['yaoqing_id'];//推荐人
$xin_user_name=$mber['user_name'];//新注册的用户名
$riqi=date('Y-m-d H:i:s');
if($tuijianren!='' && $jiang!=0)  //推荐奖励
{
$this->_money_mod =& m('my_money');
$this->qiandao_mod =& m('qiandao');
$this->qiandao_log_mod =& m('qiandao_log');
$tuijian=$this->_money_mod->getrow("select * from ".DB_PREFIX."member where user_name='$tuijianren'");
$tjuser_id=$tuijian['user_id'];//推荐人的用户id
$tjcity=$tuijian['city'];

$qiandao=$this->_money_mod->getrow("select * from ".DB_PREFIX."qiandao where user_id='$tjuser_id'");
$times=$qiandao['times'];
$new_times=$times+$jiang;

if(empty($qiandao))
{
	$qd=array(
	'user_id'=>$tjuser_id,
	'riqi'=>$riqi,
	'times'=>$new_times
	);
 $this->qiandao_mod->add($qd);	
}
else
{
	 $qd=array('times'=>$new_times);
	 $this->qiandao_mod->edit('user_id='.$tjuser_id,$qd);	
}


//添加qiandao_log日志

$beizhu=Lang::get('huodejiangli').$jiang.Lang::get('rongyujifen');
	$add_mylog=array(
	'user_id'=>$tuijian['user_id'],
	'user_name'=>$tuijianren,
    'jifen'=>$jiang,
	'beizhu'=>$beizhu,
	'riqi'=>$riqi,	
	'city'=>$tjcity																			
    );
    $this->qiandao_log_mod->add($add_mylog);

	$notice=Lang::get('tuijianrongyujifen');
	$notice=str_replace('{1}',$tuijianren,$notice);
	$notice=str_replace('{2}',$xin_user_name,$notice);
	$notice=str_replace('{3}',$jiang,$notice);
				
	$add_notice=array(
	'from_id'=>0,
	'to_id'=>$tuijian['user_id'],
	'content'=>$notice,  
	'add_time'=>gmtime(),
	'last_update'=>gmtime(),
	'new'=>1,
	'parent_id'=>0,
	'status'=>3,
	);				
	$this->message_mod->add($add_notice);
}

            #TODO 可能还会发送欢迎邮件

            $this->show_message('register_successed',
               // 'back_before_register', rawurldecode($_POST['ret_url']),
                'enter_member_center', 'index.php?app=member'
                //'apply_store', 'index.php?app=apply'
            );
        }
    }


    
    /**
     *    检查用户是否存在
     *
     *    @author    Garbin
     *    @return    void
     */
    function check_user()
    {
        $user_name = empty($_GET['user_name']) ? null : trim($_GET['user_name']);
		
        if (!$user_name)
        {
            echo ecm_json_encode(false);

            return;
        }
		
        $ms =& ms();
	    echo ecm_json_encode($ms->user->check_username($user_name));
		
    }
	 function check_email()
    {
        $email = empty($_GET['email']) ? null : trim($_GET['email']);
		
        if (!$email)
        {
            echo ecm_json_encode(false);

            return;
        }
		
        $ms =& ms();
	    echo ecm_json_encode($ms->user->check_email($email));
		
    }

    /**
     *    修改基本信息
     *
     *    @author    Hyber
     *    @usage    none
     */
    function profile(){

        $user_id = $this->visitor->get('user_id');
        if (!IS_POST)
        {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),  'index.php?app=member',
                             LANG::get('basic_information'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('basic_information');

            $ms =& ms();    //连接用户系统
            $edit_avatar = $ms->user->set_avatar($this->visitor->get('user_id')); //获取头像设置方式

            $model_user =& m('member');
            $profile    = $model_user->get_info(intval($user_id));
            $profile['portrait'] = portrait($profile['user_id'], $profile['portrait'], 'middle');
            $this->assign('profile',$profile);
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));
			
			$ur=$_SERVER['HTTP_HOST'];
			$this->assign('ur',$ur);
			$this->assign('edit_avatar', $edit_avatar);
            $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('my_profile'));
            $this->display('member.profile.html');
        }
        else
        {
            $data = array(
                'real_name' => $_POST['real_name'],
                'gender'    => $_POST['gender'],
                'birthday'  => $_POST['birthday'],
                'im_msn'    => $_POST['im_msn'],
                'im_qq'     => $_POST['im_qq'],
            );

            if (!empty($_FILES['portrait']))
            {
                $portrait = $this->_upload_portrait($user_id);
                if ($portrait === false)
                {
                    return;
                }
                $data['portrait'] = $portrait;
            }

            $model_user =& m('member');
            $model_user->edit($user_id , $data);
            if ($model_user->has_error())
            {
                $this->show_warning($model_user->get_error());

                return;
            }

            $this->show_message('edit_profile_successed');
        }
    }
    /**
     *    修改密码
     *
     *    @author    Hyber
     *    @usage    none
     */
    function password(){
        $user_id = $this->visitor->get('user_id');
		$user_name = $this->visitor->get('user_name');
		$this->message_mod=& m('message');
        if (!IS_POST)
        {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),  'index.php?app=member',
                             LANG::get('edit_password'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('edit_password');
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));
            $this->assign('page_title', Lang::get('user_center') . ' - ' . Lang::get('edit_password'));
            $this->display('member.password.html');
        }
        else
        {
            /* 两次密码输入必须相同 */
            $orig_password      = $_POST['orig_password'];
            $new_password       = $_POST['new_password'];
            $confirm_password   = $_POST['confirm_password'];
            if ($new_password != $confirm_password)
            {
                $this->show_warning('twice_pass_not_match');

                return;
            }
            if (!$new_password)
            {
                $this->show_warning('no_new_pass');

                return;
            }
            $passlen = strlen($new_password);
            if ($passlen < 6 || $passlen > 20)
            {
                $this->show_warning('password_length_error');
                return;
            }

            /* 修改密码 */
            $ms =& ms();    //连接用户系统
            $result = $ms->user->edit($this->visitor->get('user_id'), $orig_password, array(
                'password'  => $new_password
            ));
            if (!$result)
            {
                /* 修改不成功，显示原因 */
                $this->show_warning($ms->user->get_error());

                return;
            }
			$content=Lang::get('denglu');
			$content=str_replace('{1}',$user_name,$content);		
			$add_notice1=array(
			'from_id'=>0,
			'to_id'=>$user_id,
			'content'=>$content,  
			'add_time'=>time(),
			'last_update'=>time(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3,
			);
			$this->message_mod->add($add_notice1);
			$mem=$this->message_mod->getrow("select email from ".DB_PREFIX."member where user_id='$user_id'");
			$email=$mem['email'];
			$subject=Lang::get('mimaxiugai');
			$body=$content;
   			sendmail($subject,$body,$email);
            $this->show_message('edit_password_successed');
        }
    }
    /**
     *    修改电子邮箱
     *
     *    @author    Hyber
     *    @usage    none
     */
    function email(){
        $user_id = $this->visitor->get('user_id');
		$user_name = $this->visitor->get('user_name');
		$this->message_mod=& m('message');
        if (!IS_POST)
        {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),  'index.php?app=member',
                             LANG::get('edit_email'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('edit_email');
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));
            $this->assign('page_title', Lang::get('user_center') . ' - ' . Lang::get('edit_email'));
            $this->display('member.email.html');
        }
        else
        {
		$this->user_mod=& m('member');
		$row_user=$this->user_mod->getAll("select email from ".DB_PREFIX."member");
		
            $orig_password  = $_POST['orig_password'];
            $email          = isset($_POST['email']) ? trim($_POST['email']) : '';
            if (!$email)
            {
                $this->show_warning('email_required');

                return;
            }
            if (!is_email($email))
            {
                $this->show_warning('email_error');

                return;
            }
			
			   foreach ($row_user as $key => $user)
            {
          if ($email==$user['email'])
          {
		  $this->show_warning('cunzai');
            return ;
          }
		  }

            $ms =& ms();    //连接用户系统
            $result = $ms->user->edit($this->visitor->get('user_id'), $orig_password, array(
                'email' => $email
            ));
            if (!$result)
            {
                $this->show_warning($ms->user->get_error());

                return;
            }


			$content=Lang::get('youxiang');
			$content=str_replace('{1}',$user_name,$content);		
			$add_notice1=array(
			'from_id'=>0,
			'to_id'=>$user_id,
			'content'=>$content,  
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3,
			);
			$this->message_mod->add($add_notice1);

            $this->show_message('edit_email_successed','','index.php?app=member&act=email');
        }
    }

    /**
     * Feed设置
     *
     * @author Garbin
     * @param
     * @return void
     **/
    function feed_settings()
    {
        if (!$this->_feed_enabled)
        {
            $this->show_warning('feed_disabled');
            return;
        }
        if (!IS_POST)
        {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),  'index.php?app=member',
                             LANG::get('feed_settings'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('feed_settings');
            $this->assign('page_title', Lang::get('user_center') . ' - ' . Lang::get('feed_settings'));

            $user_feed_config = $this->visitor->get('feed_config');
            $default_feed_config = Conf::get('default_feed_config');
            $feed_config = !$user_feed_config ? $default_feed_config : unserialize($user_feed_config);

            $buyer_feed_items = array(
                'store_created' => Lang::get('feed_store_created.name'),
                'order_created' => Lang::get('feed_order_created.name'),
                'goods_collected' => Lang::get('feed_goods_collected.name'),
                'store_collected' => Lang::get('feed_store_collected.name'),
                'goods_evaluated' => Lang::get('feed_goods_evaluated.name'),
                'groupbuy_joined' => Lang::get('feed_groupbuy_joined.name')
            );
            $seller_feed_items = array(
                'goods_created' => Lang::get('feed_goods_created.name'),
                'groupbuy_created' => Lang::get('feed_groupbuy_created.name'),
            );
            $feed_items = $buyer_feed_items;
            if ($this->visitor->get('manage_store'))
            {
                $feed_items = array_merge($feed_items, $seller_feed_items);
            }
            $this->assign('feed_items', $feed_items);
            $this->assign('feed_config', $feed_config);
            $this->display('member.feed_settings.html');
        }
        else
        {
            $feed_settings = serialize($_POST['feed_config']);
            $m_member = &m('member');
            $m_member->edit($this->visitor->get('user_id'), array(
                'feed_config' => $feed_settings,
            ));
            $this->show_message('feed_settings_successfully');
        }
    }

     /**
     *    三级菜单
     *
     *    @author    Hyber
     *    @return    void
     */
    function _get_member_submenu()
    {
        $submenus =  array(
            array(
                'name'  => 'basic_information',
                'url'   => 'index.php?app=member&amp;act=profile',
            ),
            array(
                'name'  => 'edit_password',
                'url'   => 'index.php?app=member&amp;act=password',
            ),
            array(
                'name'  => 'edit_email',
                'url'   => 'index.php?app=member&amp;act=email',
            ),
        );
        if ($this->_feed_enabled)
        {
            $submenus[] = array(
                'name'  => 'feed_settings',
                'url'   => 'index.php?app=member&amp;act=feed_settings',
            );
        }

        return $submenus;
    }

    /**
     * 上传头像
     *
     * @param int $user_id
     * @return mix false表示上传失败,空串表示没有上传,string表示上传文件地址
     */
    function _upload_portrait($user_id)
    {
        $file = $_FILES['portrait'];
        if ($file['error'] != UPLOAD_ERR_OK)
        {
            return '';
        }
        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false)
        {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=member&amp;act=profile');
            return false;
        }
        $uploader->root_dir(ROOT_PATH);
        return $uploader->save('data/files/mall/portrait/' . ceil($user_id / 500), $user_id);
    }
	function qiandao()
	{
	
	$this->qiandao_mod=& m('qiandao');
	$this->qiandao_log_mod=& m('qiandao_log');
	$user_id = empty($_GET['user_id']) ? null : trim($_GET['user_id']);
    $riqi=date('Y-m-d');
$qd=$this->qiandao_mod->getrow("select * from ".DB_PREFIX."qiandao where user_id='$user_id' limit 1");
$can=$this->qiandao_mod->can();
$qiandao_jifen=$can['qiandao_jifen'];
$reg_jifen=$can['reg_jifen'];
$z_jifen=$qiandao_jifen+$reg_jifen;

$mm=$this->qiandao_mod->getrow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");

	if($qd['riqi']==$riqi && $qd['status']==1)
	{
	$this->show_warning('ninyiqiandao');
	return;
	}

	/*if(empty($qd))
	{
	$dao=array(
		'user_id'=>$user_id,
		'riqi'=>$riqi,
		'times'=>$z_jifen
		     );
	$this->qiandao_mod->add($dao);
	}
	else
	{*/
	$times=$qd['times'];
		$new_times=$times+$qiandao_jifen;
		$dao=array(
		'user_id'=>$user_id,
		'riqi'=>$riqi,
		'times'=>$new_times,
		'status'=>1
		);
		$this->qiandao_mod->edit('user_id='.$user_id,$dao);
/*	}*/

$riqi1=date('Y-m-d H:i:s');
$beizhu=Lang::get('huoderongyu');
$beizhu=str_replace('{1}',$qiandao_jifen,$beizhu);
	$add_mylog=array(
	'user_id'=>$user_id,
	'user_name'=>$mm['user_name'],
    'jifen'=>$qiandao_jifen,
	'beizhu'=>$beizhu,
	'riqi'=>$riqi1,	
	'city'=>$mm['city']																			
    );
    $this->qiandao_log_mod->add($add_mylog);



	$this->show_message('qiandaochenggong',
    'fanhuiliebiao',    'index.php?app=member');
 }
 
 //购买套餐
function goumaitaocan()
    {  
	  $this->my_money_mod =& m('my_money'); 
	  $this->kaiguan_mod =& m('kaiguan');
	  $this->canshu_mod =& m('canshu');
	  $this->member_mod =& m('member');  
	  $this->my_webserv_mod =& m('my_webserv'); 
	  $this->moneylog_mod =& m('moneylog');
	  $user_id = $this->visitor->get('user_id');   
	  $user_name=$this->visitor->get('user_name');
	  $userrow=$this->my_money_mod->getrow("select * from ".DB_PREFIX."my_money where user_id='$user_id'");	
	  $city=$userrow['city'];
	  $us_money=$userrow['money'];
	  $us_money_dj=$userrow['money_dj'];
	  $duihuanjifen=$userrow['duihuanjifen'];
	  $dongjiejifen=$userrow['dongjiejifen'];
	  $suoding_money=$userrow['suoding_money'];
	  $keyong_money=$us_money-$suoding_money;

	$riqi=date('Y-m-d H:i:s');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('shangfutong'),         'index.php?app=my_money&act=index',
                         LANG::get('taocan')
                         );
        /* 当前用户中心菜单 */
	$this->assign('page_title',Lang::get('member_center'). ' - ' .Lang::get('taocan'));
        $this->_curitem('taocan');	

        $this->assign('userrow', $userrow); 
		$chaoji=Lang::get('chaoji');
		$huangguan=Lang::get('huangguan');
		$baijin=Lang::get('baijin');
		$jinpai=Lang::get('jinpai');
		$yinpai=Lang::get('yinpai');
		$jichu=Lang::get('jichu');
		$tongpai=Lang::get('tongpai');
		
	if($_POST)
	{
	$tuijianren = trim($_POST['tuijianren']);	
	$lishuren = trim($_POST['lishuren']);
	$buytype = (int)($_POST['buytype']);	
	$buytype_dj=array('',66700,66700,42000,16200,10600,7800,2820);
	$dongjie=$buytype_dj[$buytype];
	
	if(empty($buytype))
	{
			$this->show_warning('xuanzetaocan');
			return;	
	}
	else
	{
		$ispayprice		=0;
		$ispaydingjin=trim(($_POST['ispay']));	
	}
	if(empty($_POST['tuijianren']))
	{
		$this->show_warning('tuijianrenbunengweikong');
			return;	
	}
	else
	{
		$row=$this->my_money_mod->getrow("select user_id from ".DB_PREFIX."my_webserv where user_name='$tuijianren'");	
		if(empty($row))
		{
			$this->show_warning('tuijianbucunzai');
				return;
		}
		else
		{
			$tj_userid=$row['user_id'];	
		}
			$row=null;
		}
		if($buytype!=7)
			{
				$lishuren=trim($_POST['lishuren']);//用户名
				if(empty($lishuren))
				{
				
					$this->show_warning('lishurenbunengweikong！');
					return;	
				}
				else
				{
				$row=$this->my_money_mod->getrow("select user_id from ".DB_PREFIX."my_webserv where user_name='$lishuren'");	
					if(empty($row))
					{
						$this->show_warning('lishubucunzai');
						return;	
					}
					else
					{
						$ls_userid=$row['user_id'];
					}				
					$row=null;				
					$row=$this->my_money_mod->getrow("select count(*) as count from ".DB_PREFIX."member where lishuid='$ls_userid'");	
					if($row['count']>=2)
					{
						$lishu=Lang::get('zhinengyouliangge');
						$lishu=str_replace('{1}',$lishuren,$lishu);
						$this->show_warning($lishu);
						return;	
					}	
					$row=null;
				}	
			}	
	
		if($keyong_money<$dongjie)
		{
			$this->show_warning('nindezijinbuzu');
			//$this->show_message('nindezijinbuzu',
    //'fanhuiliebiao',    'index.php?app=member&act=goumaitaocan');
	        return;
		}
		
		$da=array('tuijianid'=>$tj_userid,'lishuid'=>$ls_userid);
		$this->member_mod->edit('user_id='.$user_id,$da);
		
		$riqi=date('Y-m-d H:i:s');
		$name = trim($_POST['name']);
		$price = trim($_POST['price']);	
		//$ispayprice= trim($_POST['ispayprice']);
		//$ispaydingjin = trim($_POST['ispaydingjin']);
		$data=array(
		'user_id'=>$user_id,
		'user_name'=>$user_name,
		'buytype'=>$buytype,
		'ispayprice'=>$ispayprice,
		'ispaydingjin'=>$ispaydingjin,
		'status'=>0,
		'createdate'=>$riqi,
		'city'=>$city
		);	
		$this->my_webserv_mod->add($data);
		$beizhu=$user_name.Lang::get('shenqinggoumai');
		$arr=array(
				'money'=>'-'.$dongjie,
				'jifen'=>0,
				'money_dj'=>$dongjie,
				'jifen_dj'=>0,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>29,
				's_and_z'=>2,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$us_money-$dongjie,
				'dq_money_dj'=>$us_money_dj+$dongjie,
				'dq_jifen'=>$duihuanjifen,			
				'dq_jifen_dj'=>$dongjiejifen,
				'beizhu'=>$beizhu
			);			
			$this->moneylog_mod->add($arr);
$new_user_money=$us_money-$dongjie;
$new_user_moneydj=$us_money_dj+$dongjie;
$da=array(
'money'=>$new_user_money,
'money_dj'=>$new_user_moneydj
);
$this->my_money_mod->edit('user_id='.$user_id,$da);

		$this->show_message('goumaichenggong',
    'fanhuiliebiao',    'index.php?app=member');

	}
		
	
	else
	{	
	
	$taocan_name=array(
	array('buytype'=>1,'name'=>$chaoji,'price'=>13.7),
	array('buytype'=>2,'name'=>$huangguan,'price'=>13.7),
	array('buytype'=>3,'name'=>$baijin,'price'=>8.5),
	array('buytype'=>4,'name'=>$jinpai,'price'=>3.35),
	array('buytype'=>5,'name'=>$yinpai,'price'=>2.2),
	array('buytype'=>6,'name'=>$jichu,'price'=>1.58),
	array('buytype'=>7,'name'=>$tongpai,'price'=>0.58)			
	);
	
	
	
	$row=$this->my_webserv_mod->getrow("select id from " .DB_PREFIX. "my_webserv where user_id='$user_id' limit 1");
	$this->assign('row', $row); 
	$this->assign('buytype_dj', $buytype_dj); 
	
	$this->assign('taocan_name', $taocan_name);  
    $this->display('goumaitaocan.html');
	}
   
}
 
 //成长积分
function chengzhangjifen()
    {  
	  
	  $this->moneylog_mod =& m('moneylog');
	  $user_id = $this->visitor->get('user_id');   
	  $user_name=$this->visitor->get('user_name');
	 
	
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('shangfutong'),         'index.php?app=my_money&act=index',
                         LANG::get('shourulog')
                         );
        /* 当前用户中心菜单 */
	$this->assign('page_title',Lang::get('member_center'). ' - ' .Lang::get('shourulog'));
        $this->_curitem('shourulog');	


 $page = $this->_get_page();		
		
		$userrow=$this->moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and type=104" ,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true,
        ));	
    $page['item_count'] = $this->moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
        $this->assign('userrow', $userrow); 
    	$this->display('chengzhangjifen.html');
	} 
	
	//推荐奖励
function tjjl()
{  
	  
	  $this->moneylog_mod =& m('moneylog');
	  $user_id = $this->visitor->get('user_id');   
	  $user_name=$this->visitor->get('user_name');
	 
	
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('shangfutong'),         'index.php?app=my_money&act=index',
                         LANG::get('shourulog')
                         );
        /* 当前用户中心菜单 */
	$this->assign('page_title',Lang::get('member_center'). ' - ' .Lang::get('shourulog'));
        $this->_curitem('shourulog');	


 $page = $this->_get_page();		
		
		$userrow=$this->moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and (type=38 or type=39 or type=40)" ,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true,
        ));	
    $page['item_count'] = $this->moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
        $this->assign('userrow', $userrow); 
    	$this->display('tuijianjiangli.html');

	} 
	function my_jiekuan()
	{
		$user_id=$this->visitor->get('user_id');
		$user_name=$this->visitor->get('user_name');
		$this->assign('user_name',$user_name);
		$this->jiekuan_mod=& m('jiekuan');
		$mem=$this->jiekuan_mod->getrow("select * from ".DB_PREFIX."member where user_id = '$user_id'");
		$city=$mem['city'];
		$jie=$this->jiekuan_mod->getrow("select * from ".DB_PREFIX."jiekuan where user_id = '$user_id' order by createdate desc ");
		$this->assign('jie',$jie);
		if($_POST)
		{
			
			 $money_j = trim($_POST['money_j']);
			 $time = trim($_POST['time']);
			 $name = trim($_POST['name']);
			 $bank = trim($_POST['bank']);
			 $bank_hao = trim($_POST['bank_hao']);
			 $beizhu = trim($_POST['beizhu']);
			 $lixi=$money_j*5/1000*$time;
			 $data=array(
			 'user_id'=>$user_id,
			 'user_name'=>$user_name,
			 'money_j'=>$money_j,
			 'time'=>$time,
			 'createdate'=>date('Y-m-d H:i:s'),
			 'city'=>$city,
			 'status'=>1,
			 'lixi'=>$lixi,
			 'name'=>$name,
			 'bank'=>$bank,
			 'bank_hao'=>$bank_hao,
			 'beizhu'=>$beizhu,
			 );
			$this->jiekuan_mod->add($data); 
			$this->show_message('tijiaochenggong','','index.php?app=member&act=jiekuanjilu');
		}
		else
		{
		
	   /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    url('app=member'),
                         LANG::get('overview'));

        /* 当前用户中心菜单 */
        $this->_curitem('overview');
        $this->assign('page_title', Lang::get('member_center'));
		
        	$this->display('my_jiekuan.html');	
		
		}
		
	}
	
	function jiekuanjilu()
	{
		$this->jiekuan_mod=& m('jiekuan');
		/* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    url('app=member'),
                         LANG::get('overview'));

        /* 当前用户中心菜单 */
        $this->_curitem('overview');
        $this->assign('page_title', Lang::get('member_center'));
		$user_id=$this->visitor->get('user_id');
		$jie=$this->jiekuan_mod->getAll("select * from ".DB_PREFIX."jiekuan where user_id = '$user_id'");
		
		foreach($jie as $key=>$kuan)
		{
			$jie[$key]['z_money']=$kuan['lixi']+$kuan['money_j'];
		}
		
		$this->assign('jie',$jie);
        	$this->display('jiekuanjilu.html');	
	}
	function jk_xiangqing()
	{
		$this->jiekuan_mod=& m('jiekuan');
		/* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    url('app=member'),
                         LANG::get('overview'));
        /* 当前用户中心菜单 */
        $this->_curitem('overview');
		$id=$_GET['id'];
        $this->assign('page_title', Lang::get('member_center'));
		$user_id=$this->visitor->get('user_id');
		$jie=$this->jiekuan_mod->getrow("select * from ".DB_PREFIX."jiekuan where id = '$id'");
		
		foreach($jie as $key=>$kuan)
		{
			$jie[$key]['z_money']=$kuan['lixi']+$kuan['money_j'];
		}
		print_r($jie);
		$this->assign('jie',$jie);
        	$this->display('jk_xiangqing.html');	
	}
	
	
	
	
}

?>
