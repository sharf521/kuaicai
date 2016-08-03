<?php
//1郑州，2成都，3，运城
class SinaApp extends MallbaseApp
{
    function index()
    {
$userid = empty($_GET['id']) ? 0 : $_GET['id'];	
$mysite = empty($_GET['mysite']) ? 0 : $_GET['mysite'];	
$host=$_GET['host'];


define( "WB_AKEY" , '3480479496' );
define( "WB_SKEY" , 'd94b16cdb1aa652c9e0863d6896be3d1' );

$my_url = "http://www.fuyuandai.com/index.php?app=sina&id=$userid&mysite=$host";
define( "WB_CALLBACK_URL" , $my_url );

include(ROOT_PATH . '/eccore/saetv2.ex.class.php');
$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

//$url=$o->getAuthorizeURL( WB_CALLBACK_URL );



if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

else
{
$url=$o->getAuthorizeURL( WB_CALLBACK_URL );
 echo("<script> top.location.href='" . $url . "'</script>");
}


if ($token) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
$uid=$token['uid'];

header("location:http://".$mysite."/index.php?app=sina1&uid=$uid");


ecm_setcookie('weiboid', $uid);

$this->member_mod=& m('member');
$mem=$this->member_mod->getrow("select * from ".DB_PREFIX."member where weiboid = '$uid'");
$cityrow=$this->member_mod->get_cityrow();
$city_id=$cityrow['city_id'];

if($userid)//绑定微博
{
$dd=array('weiboid'=>$uid);
$this->member_mod->edit('user_id='.$userid,$dd);
$this->show_message('bangdingweibo','','http://'.$mysite.'/index.php?app=member&act=profile');
}
else
{
if($mem)
{
$user_id=$mem['user_id'];
$this->_do_login($user_id);
header("location:http://".$mysite."/index.php");
}
else
{ 
/*$date=time();
$username=$uid;
$me=$this->member_mod->getrow("select * from ".DB_PREFIX."member where user_name = '$username'");
		if($me)
		{
			$user_name=$username.rand(1000,9999);
			$user_name=iconv('utf-8','gb2312',$user_name);
		}
		else
		{
			$user_name=$username;
			$user_name=iconv('utf-8','gb2312',$user_name);
		}
			$password='fuyuan123';
			$email=time().'@qq.com';
			$city=$city_id;
		 $ms =& ms(); //连接用户中心
         $user_id = $ms->user->register($user_name, $password, $email,$owner_card,$city,$yaoqing_id,array(),$web_id);
		 $this->_hook('after_register', array('user_id' => $user_id));
		 
		$da=array('weiboid'=>$uid);
		$this->member_mod->edit('user_id='.$user_id,$da);
            //登录
        $this->_do_login($user_id);			
		
		header("location:index.php");//跳转*/
		header("location:http://".$mysite."/index.php?app=member&act=register");
		
	 }
}
}
}





}
?>