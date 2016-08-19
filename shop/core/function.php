<?php
function outer_call($func, $params=null)
{
	require ROOT."uc_client/client.php";
    restore_error_handler();
    $res = call_user_func_array($func, $params);
    set_error_handler('exception_handler');
    return $res;
}

function ip()
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip_address = $_SERVER["HTTP_CLIENT_IP"];
	}else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$ip_address = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
	}else if(!empty($_SERVER["REMOTE_ADDR"])){
		$ip_address = $_SERVER["REMOTE_ADDR"];
	}else{
		$ip_address = '';
	}
	return $ip_address;
}
/**
* 将数据格式化成树形结构
* @author Xuefen.Tong
* @param array $items
* @return array 
*/
function genTree5($items) { 
    foreach ($items as $item) 
        $items[$item['pid']]['son'][$item['id']] = &$items[$item['id']]; 
    return isset($items[0]['son']) ? $items[0]['son'] : array(); 
}
function genTree9($items) {
    $tree = array(); //格式化好的树
    foreach ($items as $item)
        if (isset($items[$item['pid']]))
            $items[$item['pid']]['son'][] = &$items[$item['id']];
        else
            $tree[] = &$items[$item['id']];
    return $tree;
}


function check_var($post,$fields)
{
	foreach($post as $i=>$v)
	{
		if(!in_array($i,$fields))
		{
			unset($post[$i]);	
		}
	}
	return $post;
}
function linkpage($code,$val='',$data=array())
{
	global $_G;
	$name	=isset($data['name'])?$data['name']:$code;
	$title	=isset($data['title'])?$data['title']:'请选择';
	$attr	=isset($data['attr'])?$data['attr']:'';	
	if($data['type']=='checkbox')
	{
		foreach($_G['linkpage'][$code] as $i=>$v)
		{
			$_chk='';
			if(is_array($val) && in_array($i,$val))
			{
				$_chk='checked';
			}
			echo "<label><input type='checkbox' name='{$name}[]' {$_chk} value='{$i}'>{$v}</label>&nbsp;";
		}
	}
	else
	{
		echo "<select name='{$name}' {$attr}><option value=''>{$title}</option>";
		foreach($_G['linkpage'][$code] as $i=>$v)
		{
			if("$i"=="$val")
				echo "<option value='{$i}' selected>{$v}</option>";
			else
				echo "<option value='{$i}'>{$v}</option>";
		}
		echo "</select>";
	}
}
//验证权限
//permission=$class.'_'.$func
function check_cmvalue($permission)
{
	$CI =& get_instance();
	$permission_id=$CI->session->userdata("permission_id");
	if($permission_id!='ALL')
	{
		$permission_id=unserialize($permission_id);
		if(empty($permission_id['func']))
		{
			$permission_id['func']=array();	
		}		
		if(!in_array($permission,$permission_id['func']))
		{
			return false;
		}
	}
	return true;	
}

//找回密码邮件内容
function GetpwdMsg($data = array())
{
	global $_G;
	$user_id = $data['user_id'];
	$username = $data['username'];
	$webname = $_G['subsite']['name'];
	$email = $data['email'];
	$active_id = urlencode(authcode($user_id.",".time(),"ENCODE"));
	if($data['type']=='getpwd')
	{
		$_url = "http://{$_SERVER['HTTP_HOST']}/index.php/getpwd/updatepwd?id={$active_id}";
		$tital="修改登录密码";
	}
	elseif($data['type']=='getpaypwd')
	{
		$_url = "http://{$_SERVER['HTTP_HOST']}/index.php/member/user/resetpaypwd?id={$active_id}";
		$tital="修改支付密码";
	}
	elseif($data['type']=='sure_email')
	{
		$_url = "http://{$_SERVER['HTTP_HOST']}/index.php/register/sure_email?id={$active_id}";
		$tital="验证邮箱";
	}
	$send_email_msg = '
	<div style="font-size:14px; ">
	<div style="padding: 10px 0px;">
		<h1 style="padding: 0px 15px; margin: 0px;">
			<a title="'.$webname.'用户中心" href="http://'.$_SERVER['HTTP_HOST'].'/" target="_blank" swaped="true">'.$webname.'</a>
		</h1>

		<div style="padding: 2px 20px 30px;">
			<p>亲爱的 <span style="color: rgb(196, 0, 0);">'.$username.'</span> , 您好！</p>
			<p>请点击下面的链接'.$tital.'。</p>
			<p style="overflow: hidden; width: 100%; word-wrap: break-word;"><a title="点击'.$tital.'" href="'.$_url.'" target="_blank" swaped="true">'.$_url.'</a>
			<br><span style="color: rgb(153, 153, 153);">(如果链接无法点击，请将它拷贝到浏览器的地址栏中)</span></p>
			
			<p style="text-align: right;"><br>'.$webname.'用户中心 敬启</p>
			<p><br>此为自动发送邮件，请勿直接回复！如您有任何疑问，请点击<a title="点击联系我们" style="color: rgb(15, 136, 221);" href="http://'.$_SERVER['HTTP_HOST'].'/" target="_blank" >联系我们</a></p>
		</div>
	</div>
</div>
		';
	return $send_email_msg;

}

//PHP加密解密函数 AUTHCODE
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
	$ckey_length = 4;
	// 密匙
	$key = md5($key ? $key : "jsiw982sjwo29swoir8743652dnapfmfka");
	// 密匙a会参与加解密
	$keya = md5(substr($key, 0, 16));
	// 密匙b会用来做数据完整性验证
	$keyb = md5(substr($key, 16, 16));
	// 密匙c用于变化生成的密文
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	// 参与运算的密匙
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
	// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	
	// 产生密匙簿
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	
	// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	
	// 核心加解密部分
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		// 从密匙簿得出密匙进行异或，再转成字符
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	
	
	if($operation == 'DECODE') {
		// substr($result, 0, 10) == 0 验证数据有效性
		// substr($result, 0, 10) - time() > 0 验证数据有效性
		// substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
		// 验证数据有效性，请看未加密明文的格式
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
		// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
//发送EMAIL
function mail_send($to,$title,$body)
{
	require (ROOT.'core/PHPMailer/class.phpmailer.php');
	$mail = new PHPMailer();
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->SMTPDebug  = false;                     // enables SMTP debug information (for testing)
											   // 1 = errors and messages
											   // 2 = messages only
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Host       = "smtp.qq.com"; // sets the SMTP server
	$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
	$mail->Username   = "353889718@qq.com"; // SMTP account username
	$mail->Password   = "qqww1122";        // SMTP account password
	$mail->SetFrom('353889718@qq.com', '服务平台');	
	$mail->AddReplyTo("353889718@qq.com","AddReplyTo");	
	$mail->Subject = "=?utf-8?B?" . base64_encode($title) . "?=";
	$mail->MsgHTML($body);		
	$mail->AddAddress($to, ""); 
	if(!$mail->Send()) 
	{
		//$str = "错误代码: " . $mail->ErrorInfo;
		return false;
	}
	return true;
}

function sock_open($url,$data=array())
{	
	$row = parse_url($url);
	$host = $row['host'];
	$port = isset($row['port']) ? $row['port']:80;	
	$post='';//要提交的内容.
	foreach($data as $k=>$v)
	{
		//$post.=$k.'='.$v.'&';
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
	}
	$fp = fsockopen($host, $port, $errno, $errstr, 30); 
	if (!$fp)
	{ 
		echo "$errstr ($errno)<br />\n"; 
	} 
	else 
	{
		$header = "POST $url HTTP/1.1\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent: MSIE\r\n";
		$header .= "Host: $host\r\n"; 
		$header .= "Content-Length: ".strlen($post)."\r\n";
		$header .= "Connection: Close\r\n\r\n"; 
		$header .= $post."\r\n\r\n";		
		fputs($fp, $header); 
		//$status = stream_get_meta_data($fp);		
		while (!feof($fp)) 
		{
			$tmp .= fgets($fp, 128);
		}
		fclose($fp);
		$tmp = explode("\r\n\r\n",$tmp);
		unset($tmp[0]);
		$tmp= implode("",$tmp);		
		/*while (!feof($fp)) 
		{
		 if(($header = fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
			break;
		 }
		}
		$tmp = ""; 
		while (!feof($fp))
		{ 
			$tmp .= fgets($fp, 128); 
		}
		fclose($fp); */
	}
	return $tmp;
}
//curl请求函数
function curl_url($url, $data=array())
{
	$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_HEADER,0);		
	if($data)
	{		
		if(is_array($data))
		{
			curl_setopt($ch,CURLOPT_POST,1);			
		}
		else
		{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                 
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data))         
			);	
		}
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	}
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_TIMEOUT,30);
	if($ssl)
	{
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE );
	}
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function binary_to_file($file)
{
	$content = "";
	if(empty($content))
	{
		$content = file_get_contents('php://input');
	}
	$ret = file_put_contents($file, $content, true);
	return $ret;
}

//输出提示框
//show_msg(array('不能为空'));
//show_msg(array('不能为空','去百度','http://www.baidu.com','_blank'));
function show_msg($msg=array())
{
	global $_G;
	echo '<!DOCTYPE html><html><head><meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>返回信息_'.$_G['system']['webname'].'</title>
	<style>
	.backbox{ border:2px solid #ededed; padding:0px;width:320px;margin:0 auto}
	.backbox h1{ font-size:18px; font-family:微软雅黑; background-color:#f5f4f4; padding-left:20px; font-weight:normal; line-height:38px; margin:4px}
	.backbox li{list-style:none; text-align:center;font-size:14px; font-family:微软雅黑; line-height:32px;}
	.backbox li a{ color:#015e8e;}
	</style>
	</head><body>';
	$target='';
	$atxt = empty($msg[1])?"返回上一页":$msg[1];			
	if(empty($msg[2]))	$msg[2] = "javascript:history.go(-1);";	
	if(!empty($msg[3]))	$target = "target=\"".$msg[3]."\"";
	$url="<a href=".$msg[2]." ".$target.">".$atxt."</a>";
	echo '
	<ul class="backbox">
		<h1>返回信息</h1>
		<li>'.$msg[0].'</li>
		<li>'.$url.'</li>
	</ul>
	</body></html>';
}
/**************************************************************
 *
 *    将数组转换为JSON字符串（兼容中文）
 *    @param  array   $array      要转换的数组
 *    @return string      转换得到的json字符串
 *    @access public
 *
 *************************************************************/
function JSON($array) {
	arrayRecursive($array, 'urlencode', true);
	$json = json_encode($array);
	return urldecode($json);
}

//此函数完成带汉字的字符串取串
function substr_cn($str,$mylen)
{                                                 
	$len=strlen($str);
	$content='';
	$count=0;
	for($i=0;$i<$len;$i++){
	   if(ord(substr($str,$i,1))>127){
		$content.=substr($str,$i,2);
		$i++;
	   }else{
		$content.=substr($str,$i,1);
	   }
	   if(++$count==$mylen){
		break;
	   }
	}
	return $content;
}

//处理小数位
function round_money($money,$type=1)
{
	$money=(float)$money;
	if($type==1)//舍去第3位
	{	
		//$pri=substr(sprintf("%.3f", $money), 0, -1);			
		$_arr=explode('.',$money);
		if(isset($_arr[1]))
		{
			$_a=substr($_arr[1],0,2);
			$pri=$_arr[0].'.'.$_a;
		}
		else
		{
			$pri=$_arr[0];	
		}		
	}
	else
	{
		$pri=ceil($money*100)/100;	
	}
	return $pri;
}

#/*/
/*/
# 函数功能：计算身份证号码中的检校码
# 函数名称：idcard_verify_number
# 参数表 ：string $idcard_base 身份证号码的前十七位
# 返回值 ：string 检校码
# 更新时间：Fri Mar 28 09:50:19 CST 2008
/*/
function idcard_verify_number($idcard_base){
if (strlen($idcard_base) != 17){
   return false;
}
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); //debug 加权因子
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); //debug 校验码对应值
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++){
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}
/*/
# 函数功能：将15位身份证升级到18位
# 函数名称：idcard_15to18
# 参数表 ：string $idcard 十五位身份证号码
# 返回值 ：string
# 更新时间：Fri Mar 28 09:49:13 CST 2008
/*/
function idcard_15to18($idcard){
    if (strlen($idcard) != 15){
        return false;
    }else{// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
            $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
        }else{
            $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
        }
    }
    $idcard = $idcard . idcard_verify_number($idcard);
    return $idcard;
}
/*/
# 函数功能：18位身份证校验码有效性检查
# 函数名称：idcard_checksum18
# 参数表 ：string $idcard 十八位身份证号码
# 返回值 ：bool
# 更新时间：Fri Mar 28 09:48:36 CST 2008
/*/
function idcard_checksum18($idcard){
    if (strlen($idcard) != 18){ return false; }
    $idcard_base = substr($idcard, 0, 17);
    if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))){
        return false;
    }else{
        return true;
    }
}
/*/
# 函数功能：身份证号码检查接口函数
# 函数名称：check_id
# 参数表 ：string $idcard 身份证号码
# 返回值 ：bool 是否正确
# 更新时间：Fri Mar 28 09:47:43 CST 2008
/*/
function isIdCard($idcard) {
if(strlen($idcard) == 15 || strlen($idcard) == 18){
   if(strlen($idcard) == 15){
    $idcard = idcard_15to18($idcard);
   }
   if(idcard_checksum18($idcard)){
    return true;
   }else{
    return false;
   }
}else{
   return false;
}
}


/**
 * utf8转gbk
 * @param $utfstr
 */
function utf8_to_gbk($utfstr) {
	global $UC2GBTABLE;
	$okstr = '';
	if(empty($UC2GBTABLE)) {
		$filename = ROOT.'core/encoding/gb-unicode.table';
		$fp = fopen($filename, 'rb');
		while($l = fgets($fp,15)) {        
			$UC2GBTABLE[hexdec(substr($l, 7, 6))] = hexdec(substr($l, 0, 6));
		}
		fclose($fp);
	}
	$okstr = '';
	$ulen = strlen($utfstr);
	for($i=0; $i<$ulen; $i++) {
		$c = $utfstr[$i];
		$cb = decbin(ord($utfstr[$i]));
		if(strlen($cb)==8) { 
			$csize = strpos(decbin(ord($cb)),'0');
			for($j = 0; $j < $csize; $j++) {
				$i++; 
				$c .= $utfstr[$i];
			}
			$c = utf8_to_unicode($c);
			if(isset($UC2GBTABLE[$c])) {
				$c = dechex($UC2GBTABLE[$c]+0x8080);
				$okstr .= chr(hexdec($c[0].$c[1])).chr(hexdec($c[2].$c[3]));
			} else {
				$okstr .= '&#'.$c.';';
			}
		} else {
			$okstr .= $c;
		}
	}
	$okstr = trim($okstr);
	return $okstr;
}
/**
 * utf8转unicode
 * @param  $c
 */
function utf8_to_unicode($c) {
	switch(strlen($c)) {
		case 1:
		  return ord($c);
		case 2:
		  $n = (ord($c[0]) & 0x3f) << 6;
		  $n += ord($c[1]) & 0x3f;
		  return $n;
		case 3:
		  $n = (ord($c[0]) & 0x1f) << 12;
		  $n += (ord($c[1]) & 0x3f) << 6;
		  $n += ord($c[2]) & 0x3f;
		  return $n;
		case 4:
		  $n = (ord($c[0]) & 0x0f) << 18;
		  $n += (ord($c[1]) & 0x3f) << 12;
		  $n += (ord($c[2]) & 0x3f) << 6;
		  $n += ord($c[3]) & 0x3f;
		  return $n;
	}
}


//导出excel格式表
function excel($filename,$title,$data)
{
	$filename.='.xls';
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename={$filename}");
	header("Pragma: no-cache");
	header("Expires: 0");
	if (is_array($title)){
		foreach ($title as $key => $value){
			echo iconv('utf-8','gb2312',$value)."\t";
			//echo utf8_to_gbk($value)."\t";
		}
	}
	echo "\n";
	if (is_array($data)){
		foreach ($data as $key => $value){
			foreach ($value as $_key => $_value){
				$_value = str_replace(array("\r\n", "\r", "\n"), " ", $_value);
				if(!empty($_value)) echo iconv('utf-8','gb2312',$_value);//echo utf8_to_gbk($_value);
				echo "\t";
			}
			echo "\n";
		}
	}
}

//生成二维码
function qrcode($data,$dir,$filename,$level='L',$size=4,$marg=0,$col='#000000')
{   
	global $color;
	$color=$col;
	if(empty($data))	return '';
	if(!in_array($level,array('L','M','Q','H'))) $level='L';
	$size= min(max((int)$size, 1), 10);
	
	if (!file_exists($dir))      mkdir($dir);
 	$filename = $dir.$filename;
	if (!file_exists($filename))
	{
		include_once(ROOT."/core/phpqrcode/qrlib.php");
		QRcode::png($data, $filename,$level, $size,$marg); 
	}
	return $filename; 
}

//DeCode(密文,'D',密钥); 解密
//DeCode(明文,'E',密钥); 加密
function DeCode($string,$operation,$key='cgqhcYpp')
{
	$key=md5($key);
	$key_length=strlen($key);
	$string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;

	$string_length=strlen($string);
	$rndkey=$box=array();
	$result='';
	for($i=0;$i<=255;$i++)
	{
		$rndkey[$i]=ord($key[$i%$key_length]);
		$box[$i]=$i;
	}
	for($j=$i=0;$i<256;$i++)
	{
		$j=($j+$box[$i]+$rndkey[$i])%256;
		$tmp=$box[$i];
		$box[$i]=$box[$j];
		$box[$j]=$tmp;
	}
	for($a=$j=$i=0;$i<$string_length;$i++)
	{
		$a=($a+1)%256;
		$j=($j+$box[$a])%256;
		$tmp=$box[$a];
		$box[$a]=$box[$j];
		$box[$j]=$tmp;
		$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
	}
	if($operation=='D')
	{
		if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
		{
			return substr($result,8);
		}
		else
		{
			return'';
		}
	}
	else
	{
		return str_replace('=','',base64_encode($result));
	}
} 
function setSession($key,$val)
{
	$_key=base64_encode($key.'ac');
	$_SESSION[$_key]=DeCode($val,'E');
	
}
function getSession($key)
{
	$_key=base64_encode($key.'ac');
	$_val=$_SESSION[$_key];
	return 	DeCode($_val,'D');
}

function editor($data=array())
{
	$name=isset($data['name'])?$data['name']:'content';
	$value=isset($data['value'])?$data['value']:'';
	$height=isset($data['height'])?$data['height']:'200';
	?>
<link href="/plugin/umeditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="/plugin/umeditor/third-party/jquery.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/plugin/umeditor/umeditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/plugin/umeditor/umeditor.min.js"></script>
<script type="text/javascript" src="/plugin/umeditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/plain" id="ueEditor" name='<?=$name?>' style="width:100%;"><?=$value?></script>
<script type="text/javascript">
//实例化编辑器
    var um = UM.getEditor('ueEditor',{
		toolbar:[
            'emotion | undo redo | bold italic underline strikethrough | removeformat | image' 
        ],
        wordCount:false,
        elementPathEnabled:false,
        initialFrameHeight:<?=$height?>
    });
</script>
<?
}
function ueditor($data=array())
{
	$name=isset($data['name'])?$data['name']:'content';
	$value=isset($data['value'])?$data['value']:'';
	?>
    
	<!-- 加载编辑器的容器 -->
    <script id="container" name="<?=$name?>" type="text/plain" style="width:900px;">
        <?=$value?>
    </script>
    <!-- 配置文件 -->
    <script type="text/javascript" src="/plugin/ueditor/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="/plugin/ueditor/ueditor.all.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container', {
			/*toolbars: [
				['fullscreen', 'source', 'undo', 'redo', 'bold']
			],*/
			autoHeightEnabled: true,
			autoFloatEnabled: true
		});
    </script>
<?
}
function ueditor_shop($data=array())
{
	$name=isset($data['name'])?$data['name']:'content';
	$value=isset($data['value'])?$data['value']:'';
	?>
    
	<!-- 加载编辑器的容器 -->
    <script id="container" name="<?=$name?>" type="text/plain" style="width:758px;">
        <?=$value?>
    </script>
    <!-- 配置文件 -->
    <script type="text/javascript" src="/plugin/ueditor/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="/plugin/ueditor/ueditor.all.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container', {
			/*toolbars: [
				['fullscreen', 'source', 'undo', 'redo', 'bold']
			],*/
			autoHeightEnabled: true,
			autoFloatEnabled: true
		});
    </script>
<?
}
//获取字符串首字符
function getfirstchar($zh)
{
	$ret = "";
	$s1 = iconv("UTF-8","gb2312", $zh);
	$s2 = iconv("gb2312","UTF-8", $s1);
	if($s2 == $zh){$zh = $s1;}
	$s1 = substr($zh,0,1);
	$p = ord($s1);
	if($p > 160)
	{
		$s2 = substr($zh,$i++,2);
		$ret = getcharacter($s2);
	}
	else
	{
		$ret = $s1;
	}
	return $ret;
}
//返回首字母
function getcharacter($s0)
{	
	$fchar = ord($s0{0});
	if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
	$s1 = iconv("UTF-8","gb2312", $s0);
	$s2 = iconv("gb2312","UTF-8", $s1);
	if($s2 == $s0){$s = $s1;}else{$s = $s0;}
	$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
	if($asc >= -20319 and $asc <= -20284) return "A";
	if($asc >= -20283 and $asc <= -19776) return "B";
	if($asc >= -19775 and $asc <= -19219) return "C";
	if($asc >= -19218 and $asc <= -18711) return "D";
	if($asc >= -18710 and $asc <= -18527) return "E";
	if($asc >= -18526 and $asc <= -18240) return "F";
	if($asc >= -18239 and $asc <= -17760) return "G";
	if($asc >= -17759 and $asc <= -17248) return "H";
	if($asc >= -17247 and $asc <= -17418) return "H";
	if($asc >= -17417 and $asc <= -16475) return "J";
	if($asc >= -16474 and $asc <= -16213) return "K";
	if($asc >= -16212 and $asc <= -15641) return "L";
	if($asc >= -15640 and $asc <= -15166) return "M";
	if($asc >= -15165 and $asc <= -14923) return "N";
	if($asc >= -14922 and $asc <= -14915) return "O";
	if($asc >= -14914 and $asc <= -14631) return "P";
	if($asc >= -14630 and $asc <= -14150) return "Q";
	if($asc >= -14149 and $asc <= -14091) return "R";
	if($asc >= -14090 and $asc <= -13319) return "S";
	if($asc >= -13318 and $asc <= -12839) return "T";
	if($asc >= -12838 and $asc <= -12557) return "W";
	if($asc >= -12556 and $asc <= -11848) return "X";
	if($asc >= -11847 and $asc <= -11056) return "Y";
	if($asc >= -11055 and $asc <= -10247) return "Z";
	return null;
}
