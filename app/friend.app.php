<?php

class FriendApp extends MemberbaseApp
{
    /**
     *    好友列表
     *
     *    @author    Hyber
     *    @return    void
     */
    function index()
    {
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('friend'),         'index.php?app=friend',
                         LANG::get('friend_list')
                         );

        /* 当前所处子菜单 */
        $this->_curmenu('friend_list');
        /* 当前用户中心菜单 */
        $this->_curitem('friend');
        $page = $this->_get_page(10);

        $ms =& ms();
        $friends = $ms->friend->get_list($this->visitor->get('user_id'), $page['limit']);

        $page['item_count'] = $ms->friend->get_count($this->visitor->get('user_id'));   //获取统计的数据
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
        $this->_format_page($page);
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条
        $this->assign('friends', $friends);
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('friend'));
		
		$this->member_mod=& m('member');
		$ddd=$this->member_mod->getAll("select g.*,gs.stock from ".DB_PREFIX."goods g left join ".DB_PREFIX."goods_spec gs on g.goods_id=gs.goods_id where g.store_id='720'");
		$sql='';
		foreach($ddd as $key=>$val)
		{
		$riqi=date('Y-m-d H:i:s');
		$content=addslashes($val['description']);
		$jifen=$val['price']*2.52;
		$username=Lang::get('xiaoer');
		$sql.="INSERT INTO  ". DB_PREFIX ."gonghuo (`goods_id`, `user_id`, `user_name`, `goods_name`, `goods_brand`, `tujing`, `lingshou_price`, `jifen_price`, `source`, `ziliao`, `chanpin`, `status`, `zong_kucun`, `yu_kucun`, `gh_city`, `riqi`, `beizhu`) VALUES ('', '4180', '$username', '{$val['goods_name']}', '{$val['brand']}', '', '{$val['price']}', '$jifen', '', '', '{$val['default_image']}', '3', '{$val['stock']}', '{$val['stock']}', '{$val['cityhao']}', '$riqi', '$content');";
		}
		/*$fp = fopen('ceshishuchu.txt', 'w');
		fwrite($fp, $sql);
		fclose($fp); */
		
		
        $this->display('friend.index.html');
    }

    /**
     *    添加好友
     *
     *    @author    Hyber
     *    @return    void
     */
    function add()
    {
        if (!IS_POST){
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                             LANG::get('friend'),         'index.php?app=friend',
                             LANG::get('add_friend')
                             );
             header('Content-Type:text/html;charset=' . CHARSET);
            /* 当前所处子菜单 */
            $this->_curmenu('add_friend');
            /* 当前用户中心菜单 */
            $this->_curitem('friend');
            $this->display('friend.form.html');
        }
        else
        {
            $user_name = str_replace(Lang::get('comma'), ',', $_POST['user_name']); //替换中文格式的逗号
            if (!$user_name)
            {
                $this->pop_warning('input_username');
                return;
            }
            $user_names = explode(',',$user_name); //将逗号分割的用户名转换成数组
            $mod_member = &m('member');
            $members = $mod_member->find("user_name " . db_create_in($user_names));
            $friend_ids = array_keys($members);
            if (!$friend_ids)
            {
                $this->pop_warning('no_such_user');
                return;
            }

            $ms =& ms();
            $result = $ms->friend->add($this->visitor->get('user_id'), $friend_ids);
            if (!$result)
            {
                $msg = current($ms->friend->get_error());
                $this->pop_warning($msg['msg']);

                return;
            }
            $this->pop_warning('ok', APP.'_'.ACT);
            /*$this->show_message('add_friend_successed',
                'back_list',    'index.php?app=friend',
                'continue_add', 'index.php?app=friend&amp;act=add'
            );*/
        }
    }
	
	
	function invite()
    {
       //echo $_SERVER['SERVER_NAME'];
	   $url=$_SERVER['HTTP_HOST'];//获得当前网址
	 
		//$city_id=$cityrow['city_id'];
	$this->assign('cityurl', $url);   
	   
	   
	    
  $user_id = $this->visitor->get('user_id');   
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('invite'),         'index.php?app=friend&act=invite',
						 LANG::get('yaoqinglianjie')
					
                         
                         );
        /* 当前用户中心菜单 */
            $this->_curmenu('invite');
            $this->_curitem('yaoqinglianjie');	
			
			
			$regurl="http://".$_SERVER['HTTP_HOST']."/index.php?app=member&id=".$user_id."&act=register";
			$regurl_codeimg=qrcode($regurl,"./data/files/store_{$user_id}/",$user_id.'_regurl.png');
			$this->assign('regurl_codeimg', $regurl_codeimg);
			
        $this->display('invite.index.html');
    }
	
	
	function invite_list()
    {
        $user_name = $this->visitor->get('user_name');   
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('invite'),         'index.php?app=friend&act=invite',
						 LANG::get('yaoqing_list')
					
                         
                         );
        /* 当前用户中心菜单 */
            $this->_curmenu('invite');
            $this->_curitem('yaoqing_list');	
        
	
	    $page = $this->_get_page();		
		 $this->_user_mod =& m('member');
$mem=$this->_user_mod->find(array(
            'conditions' => "yaoqing_id='$user_name'" ,
            'limit' => $page['limit'],
			'order' => "user_id desc",
			'count' => true,
        ));	

		$page['item_count'] = $this->_user_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
		$this->assign('mem', $mem);
        $this->display('invite.list.html');
	
    }
		
	

    /**
     *    删除好友
     *
     *    @author    Hyber
     *    @return    void
     */
    function drop()
    {
         $user_ids = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
        if (!$user_ids)
        {
            $this->show_warning('no_such_friend');
            return;
        }
        $user_ids = explode(',',$user_ids);

        $ms =& ms();
        $result = $ms->friend->drop($this->visitor->get('user_id'), $user_ids);
        if (!$result)    //删除
        {
            $this->show_warning($ms->friend->get_error());

            return;
        }

        /* 删除成功返回 */
        $this->show_message('drop_friend_successed');
    }

     /**
     *    三级菜单
     *
     *    @author    Hyber
     *    @return    void
     */
    function _get_member_submenu()
    {
        return array(
            array(
                'name'  => 'friend_list',
                'url'   => 'index.php?app=friend',
            ),
        );
    }
	function drop_friend()
	{
	$this->my_moneylog_mod =& m('my_moneylog');
	$id = intval($_GET['id']);//供货id
		$sql="delete from ".DB_PREFIX."my_moneylog where id = '$id'";
		$this->my_moneylog_mod->db->query($sql);
		$this->show_message('delete','back_list','index.php?app=my_money&act=shouru');

	}
	
	
}
?>
