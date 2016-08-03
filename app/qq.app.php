<?php

class QqApp extends MallbaseApp
{
    function index()
    {
  $userid = empty($_GET['id']) ? 0 : $_GET['id'];	
 //应用的APPID
  $app_id = "100341961";
  //应用的APPKEY
  $app_secret = "896d3ccf38b3fa58dfcbfd8ae3c64cfa";
  //成功授权后的回调地址
  $my_url = "http://".$_SERVER['HTTP_HOST']."/index.php?app=qq&id=$userid";


	function get_curl_contents($url, $second = 30)
	{
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);//设置cURL允许执行的最长秒数
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);//当此项为true时,curl_exec($ch)返回的是内容;为false时,curl_exec($ch)返回的是true/false
		
		//以下两项设置为FALSE时,$url可以为"https://login.yahoo.com"协议
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  FALSE);
	
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}

//Step1：获取Authorization Code
  $code = $_REQUEST["code"];
  if(empty($code)) 
  { 
     //state参数用于防止CSRF攻击，成功授权后回调时会原样带回
     $_SESSION['state'] = md5(uniqid(rand(), TRUE)); 
     //拼接URL     
     $dialog_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
        . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
        . $_SESSION['state'];
     echo("<script> top.location.href='" . $dialog_url . "'</script>");
  }


  //Step2：通过Authorization Code获取Access Token
  if($_REQUEST['state'] == $_SESSION['state']) 
  {
     //拼接URL   
     $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
     . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
     . "&client_secret=" . $app_secret . "&code=" . $code."&state=".$_SESSION['state'] ;
     $response = get_curl_contents($token_url);   

	 
     if (strpos($response, "callback") !== false)
     {
        $lpos = strpos($response, "(");
        $rpos = strrpos($response, ")");
        $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
        $msg = json_decode($response);
        if (isset($msg->error))
        {
           echo "<h3>error:</h3>" . $msg->error;
           echo "<h3>msg  :</h3>" . $msg->error_description;
           exit;
        }
     }	 
	 
	 //Step3：使用Access Token来获取用户的OpenID
     $params = array();
     parse_str($response, $params);
     $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$params['access_token'];
     $str  = get_curl_contents($graph_url);
     if (strpos($str, "callback") !== false)
     {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
     }
     $user = json_decode($str);
     if (isset($user->error))
     {
        echo "<h3>error:</h3>" . $user->error;
        echo "<h3>msg  :</h3>" . $user->error_description;
        exit;
     }
	 $openid=$user->openid;
	 //$_SESSION['openid']=$openid;
	
ecm_setcookie('openid', $openid);
$this->member_mod=& m('member');
$mem=$this->member_mod->getRow("select * from ".DB_PREFIX."member where openid = '$openid'");
$cityrow=$this->member_mod->get_cityrow();
$city_id=$cityrow['city_id'];

if($userid)//绑定qq
{
$dd=array('openid'=>$openid);
$this->member_mod->edit('user_id='.$userid,$dd);
$this->show_message('bangding','','index.php?app=member&act=profile');
}
else
{
if($mem)
{
$user_id=$mem['user_id'];
$this->_do_login($user_id);
header("location:index.php");
}
else
{ 
	 //取信息
	 $url="https://graph.qq.com/user/get_user_info?access_token=".$params['access_token']."&oauth_consumer_key=$app_id&openid=".$openid;
	 $str=get_curl_contents($url);	
	
	 if (strpos($str, "callback") !== false)
     {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
     }
	// echo $str;
     $user = json_decode($str);

     if (isset($user->error))
     {
        echo "<h3>error:</h3>" . $user->error;
        echo "<h3>msg  :</h3>" . $user->error_description;
        exit;
     }
	 else
	 {
			/*$picture=$user->figureurl_2;
			$username=$user->nickname;	 
			$sex=($user->gender=='男')?1:2;
			//1注册  2.登录
			$date=time();
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
		 
		 $da=array('openid'=>$openid);
		 $this->member_mod->edit('user_id='.$user_id,$da);
            //登录
            $this->_do_login($user_id);			
		header("location:index.php");//跳转*/
		header("location:index.php?app=member&act=register");
	 }
}

}
  }
  else 
  {
  	echo("The state does not match. You may be a victim of CSRF.");
  }
  
    }
		
	
}

?>