<?php

class QqqApp extends MallbaseApp
{
    function index()
    {
  $userid = empty($_GET['id']) ? 0 : $_GET['id'];	
  $mysite = empty($_GET['mysite']) ? 0 : $_GET['mysite'];	
  $host=$_GET['host'];
 //应用的APPID
  $app_id = "100341961";
  //应用的APPKEY
  $app_secret = "896d3ccf38b3fa58dfcbfd8ae3c64cfa";
  //成功授权后的回调地址
  $my_url = "http://www.imm023.com/index.php?app=qqq&id=$userid&mysite=$host";


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

header("location:http://".$mysite."/index.php?app=cart&act=qq&openid=$openid&userid=$userid");
exit;	




  
    }
		
	
}

?>