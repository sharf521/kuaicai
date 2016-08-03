<?php

/**
 * api控制器基类
 */
class ApiApp extends ECBaseApp
{
    function _init_visitor()
    {
        $this->visitor =& env('visitor', new ApiVisitor());
    }

    /**
     * 执行操作
     * 这个函数要跟 frontend.base.php 中的 _do_login 保持一致
     */
    function _do_login($user_id)
    {
	//$url=$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];//获得当前网址
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	$this->member_mod =& m('member');
	$this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$huiyuan=$cityrow['huiyuan'];
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id = '$user_id' limit 1");
			$city=$row_member['city'];
	
	if($city!=$city_id && $huiyuan=='no' )
	{
		$this->show_warning('ninbushibenzhanhuiyuan');
		return;
	}
	else{
        $mod_user =& m('member');

        $user_info = $mod_user->get(array(
            'conditions'    => "user_id = '{$user_id}'",
            'join'          => 'has_store',
            'fields'        => 'user_id, user_name, reg_time, last_login, last_ip, store_id',
        ));
}
        /* 店铺ID */
        $my_store = empty($user_info['store_id']) ? 0 : $user_info['store_id'];

        /* 保证基础数据整洁 */
        unset($user_info['store_id']);

        /* 分派身份 */
        $this->visitor->assign($user_info);

        /* 更新用户登录信息 */
        $mod_user->edit("user_id = '{$user_id}'", "last_login = '" . gmtime()  . "', last_ip = '" . real_ip() . "', logins = logins + 1");

        /* 更新购物车中的数据 */
        $mod_cart =& m('cart');
        $mod_cart->edit("(user_id = '{$user_id}' OR session_id = '" . SESS_ID . "') AND store_id <> '{$my_store}'", array(
            'user_id'    => $user_id,
            'session_id' => SESS_ID,
        ));
    }

    /**
     * 执行退出操作
     */
    function _do_logout()
    {
        $this->visitor->logout();
    }
}

/**
 *    api访问者
 */
class ApiVisitor extends BaseVisitor
{
    var $_info_key = 'user_info';
}

?>