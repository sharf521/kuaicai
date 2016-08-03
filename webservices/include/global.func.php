<?php 
function checkPost($data)
{
	if(!get_magic_quotes_gpc()) {
		return is_array($data)?array_map('rAddSlashes',$data):addslashes($data);
	} else {
		Return $data;
	}
}
function goUrl($url)
{
	echo "<script>window.location='$url';</script>";
	exit();
}


// 分页函数
function page($num, $perpage, $curpage, $mpurl) {
	$multipage = '';
	//$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	if(strpos($mpurl,'?')===false)
		$mpurl .='?';
	else
		$mpurl .='&amp;';
	if($num > $perpage) {
		$page = 10;
		$offset = 5;
		$pages = @ceil($num / $perpage);
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $curpage + $page - $offset - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $curpage - $pages + $to;
				$to = $pages;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$from = $pages - $page + 1;
				}
			}
		}
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="p_redirect">首页</a>' : '').($curpage > 1 ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="p_redirect">上一页</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<span class="p_curpage">'.$i.'</span>' : '<a href="'.$mpurl.'page='.$i.'" class="p_num">'.$i.'</a>';
		}
		$multipage .= ($curpage < $pages ? '<a href="'.$mpurl.'page='.($curpage + 1).'" class="p_redirect">下一页</a>' : '').($to < $pages ? '<a href="'.$mpurl.'page='.$pages.'" class="p_redirect">尾页</a>' : '');
		$multipage = $multipage ? '<div class="p_bar"><span class="p_info">总记录:'.$num.'</span>'.$multipage.'</div>' : '';
	}
	return $multipage;
}

function rewritepage($num, $perpage, $curpage, $mpurl) {
	$multipage = '';
	//$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	if($num > $perpage) {
		$page = 10;
		$offset = 5;
		$pages = @ceil($num / $perpage);
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $curpage + $page - $offset - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $curpage - $pages + $to;
				$to = $pages;
				if(($to - $from) < $page && ($to - $from) < $pages) {
					$from = $pages - $page + 1;
				}
			}
		}
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'-1.html" class="p_redirect">&laquo;</a>' : '').($curpage > 1 ? '<a href="'.$mpurl.'-'.($curpage - 1).'.html" class="p_redirect">&#8249;</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<span class="p_curpage">'.$i.'</span>' : '<a href="'.$mpurl.'-'.$i.'.html" class="p_num">'.$i.'</a>';
		}
		$multipage .= ($curpage < $pages ? '<a href="'.$mpurl.'-'.($curpage + 1).'.html" class="p_redirect">&#8250;</a>' : '').($to < $pages ? '<a href="'.$mpurl.'-'.$pages.'.html" class="p_redirect">&raquo;</a>' : '');
		$multipage = $multipage ? '<div class="p_bar"><span class="p_info">总记录:'.$num.'</span>'.$multipage.'</div>' : '';
	}
	return $multipage;
}

// 清除HTML代码
function html_clean($content) {
	$content = htmlspecialchars($content);
	$content = str_replace("\n", "<br />", $content);
	$content = str_replace("  ", "&nbsp;&nbsp;", $content);
	$content = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $content);
	return $content;	
}
function htmltojs($str)
{
  $re='';
  $str= split("\r\n",$str);
  foreach($str as $v)
  {
	  $re.="document.writeln('".trim($v)."');\r\n";
  }  
  return $re;
}
//根据path得文件名
function file_basename($file)  
{ 
    if($file=="")      return null;    
    $file= explode('?', $file); 
    $file= explode('/', $file[0]); 
    $basename= $file[count($file)-1]; 
    return $basename;    
} 
//获取文件名
function getmainfilename($filename) 
{ 
    return substr($filename,0,strrpos($filename,".")); 
} 
//获取扩展名
function getextension($filename) 
{ 
    return substr(strrchr($filename, "."), 1); 
} 
//切割字符串
function truncate($String,$Length,$Append ='') 
{
    if (strlen($String) <=$Length ) 
    { 
        return $String; 
    } 
    else
    { 
        $I = 0; 
        while ($I < $Length) 
        { 
            $StringTMP = substr($String,$I,1); 
            if ( ord($StringTMP) >=224 ) 
            { 
                $StringTMP = substr($String,$I,3); 
                $I = $I + 3; 
            } 
            elseif( ord($StringTMP) >=192 ) 
            { 
                $StringTMP = substr($String,$I,2); 
                $I = $I + 2; 
            } 
            else 
            { 
                $I = $I + 1; 
            } 
            $StringLast[] = $StringTMP; 
        } 
        $StringLast = implode("",$StringLast); 
        if(!empty($Append)) 
        { 
            $StringLast .= $Append; 
        } 
        return $StringLast; 
    } 
}

//前台错误信息
function showMsg($msg, $url="",$target="") {
	if(empty($url)) {
		$url = "javascript:history.go(-1);";
	}
	if(empty($target)) {
		$target = "";
	} else {
		$target = "target=\"".$target."\"";
	}
	echo "<br><br>";
	echo "<table width=\"350\" border=\"0\" align=\"center\" cellpadding=\"5\" cellspacing=\"1\" bgcolor=\"#CCCCCC\" >";
	echo "<tr>";
	echo "<td bgcolor=\"#FFFFFF\"> ";
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\" style='font-size:12px'>";
	echo "<tr> ";
	echo "<td bgcolor=\"#F3F3F3\"><strong>返回信息:</strong></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"center\"><br>".$msg."<br><br><a href=".$url." ".$target.">点击这里返回</a><br><br></td>\n";
	echo "</tr>";
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
}
//分词 $text：文本//$num:词个数
function getTags($text,$num=10)
{
	/*include(ROOT.'/include/pscws4/pscws4.class.php'); 
	$cws = new PSCWS4('utf8'); 
	$cws->set_charset('utf-8'); 
	$cws->set_dict(ROOT.'/include/pscws4/etc/dict.utf8.xdb'); 
	$cws->set_rule(ROOT.'/include/pscws4/etc/rules.utf8.ini'); 
	//$cws->set_multi(3); 
	//$cws->set_ignore(true); 
	//$cws->set_debug(true); 
	//$cws->set_duality(true); 
	$cws->send_text($text); 
	$ret = array(); 
	$ret = $cws->get_tops($num,'r,v,p'); 
	foreach ($ret as $tmp) 
	{ 
		if(empty($str))
			$str=$tmp['word'];
		else 
			$str.=','.$tmp['word'];
	} 
	$cws->close(); 
	return $str;*/
}
//链接加 rel='nofollow'
function href_rel_nofollow( $text ) {
	//$text = stripslashes($text);
	$text = preg_replace_callback('|<a (.+?)>|i', 'rel_nofollow_callback', $text);
	return $text;
}

function rel_nofollow_callback( $matches )
{
	$text = $matches[1];
	$text = str_replace(array(' rel="nofollow"', " rel='nofollow'",'rel=\"nofollow\"'), '', $text);
	if(strpos($text,$_SERVER['SERVER_NAME'])===false)		
		return "<a $text rel=\"nofollow\">";	
	else
		return 	"<a $text >";
}
//只替换第二次出现
function replace2tag($str,$tag,$href='')
{
	$pos = strpos($str, $tag);
    if ($pos === false) 
    {
       return $str;
    }
	else
	{
		$t1=substr($str,0,$pos+1);
		$t2=substr($str,$pos+1);
		$pos=strpos($t2,$tag);
		if($pos===false)
		{
			return $t1.$t2;	
		}
		else
		{
			if(empty($href))
				return $t1.substr_replace($t2,"<strong>$tag</strong>", $pos, strlen($tag));	
			else
				return $t1.substr_replace($t2,"<a href='$href' target='_blank'><b>$tag</b></a>", $pos, strlen($tag));	
		}
	}
}

function send_mail($mail_to, $mail_subject, $mail_body, $mail_from = '', $mail_sign = true) {
	require_once ROOT.'/include/mail.func.php';
	return dmail(trim($mail_to), $mail_subject, $mail_body, $mail_from, $mail_sign);
}
function mkdirm($path) 
{ 
	if (!file_exists($path)) 
	{ 
		mkdirm(dirname($path)); 
		mkdir($path, 0777); 
	} 
}
function writefile($filename,$str)
{
	$fp=fopen($filename,'w');
	fputs($fp,$str);
	fclose($fp);
}

function getHTML($page,$host='keyword.discuz.com')
{
	/*$cnt=0;
	$result=file_get_contents($page);
	while($cnt < 3 && empty($result))
	{
	 	$cnt++;
		$result=file_get_contents($page);
	}
	return $result;*/
	$fp = fsockopen($host, 80, $errno, $errstr, 30); 
	if (!$fp)
	{ 
		echo "$errstr ($errno)<br />\n"; 
	} 
	else 
	{ 
		$out = "GET $page HTTP/1.0\r\n"; 
		$out .= "Host: $host\r\n"; 
		$out .= "Connection: Close\r\n\r\n"; 
		fputs($fp, $out); 
		$status = stream_get_meta_data($fp);
		while (!feof($fp)) {
		 if(($header = fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
		  break;
		 }
		}
		$tmp = ""; 
		while (!feof($fp))
		{ 
			$tmp .= fgets($fp, 128); 
		} 
		fclose($fp); 
	} 
	return $tmp;
}

//解析xml函数
	function getXmlData ($strXml) {
		$pos = strpos($strXml, 'xml');
		if ($pos) {
			$xmlCode=simplexml_load_string($strXml,'SimpleXMLElement', LIBXML_NOCDATA);
			$arrayCode=get_object_vars_final($xmlCode);
			return $arrayCode ;
		} else {
			return '';
		}
	}
	
	function get_object_vars_final($obj){
		if(is_object($obj)){
			$obj=get_object_vars($obj);
		}
		if(is_array($obj)){
			foreach ($obj as $key=>$value){
				$obj[$key]=get_object_vars_final($value);
			}
		}
		return $obj;
	}
	
function getkeys($str,$num=10)//新分词
{ 
	if(strlen($str)>1024) $str=substr($str,0,1024);
	require_once ROOT.'/include/splitword/phpanalysis.class.php';
    $do_multi = $do_prop = $pri_dict = false;
    $do_fork =  true; //岐义处理
    //新词识别
    $do_unit =  true;
    //多元切分
    $do_multi = false;
    //词性标注
    $do_prop =false;
    //是否预载全部词条
    $pri_dict = false ;    
    //初始化类    
    PhpAnalysis::$loadInit = false;
    $pa = new PhpAnalysis('utf-8', 'utf-8', $pri_dict);
    
    $pa->LoadDict(); //载入词典 
    //执行分词
    $pa->SetSource($str);
    $pa->differMax = $do_multi;
    $pa->unitWord = $do_unit;
    $pa->resultType = 2;
    $pa->StartAnalysis( $do_fork );    
    
    $okresult = $pa->GetFinallyResult(' ', $do_prop);
    //$okresult = PhpAnalysis::GetIndexText( $str, ',', 20);
    $result=explode(' ',$okresult);
	$result=array_filter($result,'split_filter');
	$str='';
	$i=1;
	foreach($result as $row)
	{
		if(!empty($row) && (strpos($row,'的')===false))
		{
			if($str=='')
				$str=$row;
			else
				$str.=','.$row;
			$i++;
			if($i>$num) break;
		}
	}
	return $str;
}
//删除指定目录（文件夹）中的所有文件函数 
function del_file($dir) { 
	if (is_dir($dir)) { 
		$dh=opendir($dir);//打开目录 //列出目录中的所有文件并去掉 . 和 .. 
		while (false !== ( $file = readdir ($dh))) { 
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file; 
				if(!is_dir($fullpath)) { 
					unlink($fullpath);
				} else { 
					del_file($fullpath); 
				} 
			}
		}
		closedir($dh); 
	} 
}
function mk_dir($dir,$dir_perms=0775){
	/* 循环创建目录 */
	if (DIRECTORY_SEPARATOR!='/') {
		$dir = str_replace('\\','/', $dir);
	}
	
	
	if (is_dir($dir)){
		return true;
	}
	
	if (@ mkdir($dir, $dir_perms)){
		return true;
	}

	if (!mk_dir(dirname($dir))){
		return false;
	}
	
	return mkdir($dir, $dir_perms);
	
}
function mk_file($dir,$contents){
	$dirs = explode('/',$dir);
	if($dirs[0]==""){
		$dir = substr($dir,1);
	}
	mk_dir(dirname($dir));
	@chmod($dir, 0777);
	if (!($fd = @fopen($dir, 'wb'))) {
		$_tmp_file = $dir . DIRECTORY_SEPARATOR . uniqid('wrt');
		if (!($fd = @fopen($_tmp_file, 'wb'))) {
			trigger_error("系统无法写入文件'$_tmp_file'");
			return false;
		}
	}
	fwrite($fd, $contents);
	fclose($fd);
	@chmod($dir, 0777);
	return true;
}
function read_file($filename) 
{
        if ( file_exists($filename) && is_readable($filename) && ($fd = @fopen($filename, 'rb')) ) {
            $contents = '';
            while (!feof($fd)) {
                $contents .= fread($fd, 8192);
            }
            fclose($fd);
            return $contents;
        } else {
            return false;
        }
}
function get_file($dir,$type='dir'){
	$result = "";
	if (is_dir($dir)) {
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				$_file = $dir."/".$file;
				if ($file !="." && $file != ".." && filetype($_file)==$type ){
					$result[] = $file;
				}
			}
			closedir($dh);
		}
	}
	return $result;
}
/**
 * 验证输入的邮件地址是否合法
 *
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
    {
        if (preg_match($chars, $user_email))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

/**
 * 检查是否为一个合法的时间格式
 *
 * @param   string  $time
 * @return  void
 */
function is_time($time)
{
    $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';

    return preg_match($pattern, $time);
}
//obj 转 数组	
function objtoarr($obj)
{
	$ret = array();
	foreach($obj as $key =>$value){
		if(gettype($value) == 'array' || gettype($value) == 'object'){
			$ret[$key] = objtoarr($value);
		}
		else{
			$ret[$key] = $value;
		}
	}
	return $ret;
}
/*function webService($func,$post_data=array())  
{
	global $_S;
	//$url='http://116.255.156.154:6666/Algorithm.asmx/'.$func;	
	//$url='http://192.168.1.150:888/Algorithm.asmx/'.$func;	
	$url='http://'.$_S['canshu']['webservip'].'/Algorithm.asmx/'.$func;	
	$ch = curl_init();  		
	$o="";  
	foreach ($post_data as $k=>$v)  
	{  
	$o.= "$k=".urlencode($v)."&";  
	}  
	$post_data=substr($o,0,-1); 
	curl_setopt($ch, CURLOPT_URL,$url); 
		
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 	
	
	$result = curl_exec($ch); 	
	curl_close ($ch); 
	unset($ch);
	$xml = simplexml_load_string($result);
	//echo $url;
	//echo iconv('utf-8','gb2312',$result);
	return trim($xml[0]);
}*/

 /** 
     * curl 多线程 
	 curl_http(array('Regist','Regist','Regist','Regist'))	 
	 $array = array(
		'http://zhuzhan.cn/test/1.php?id=1&t=aa',
		'http://zhuzhan.cn/test/1.php?id=2&t=bb'
	);
	$data = curl_http($array,'post');
	print_r($data);//输出

 function curl_http($array,$method='post',$timeout=10)
 {
	global $_S;
	$u='http://'.$_S['canshu']['webservip'].'/Algorithm.asmx/';	
 	$res = array();
 	$mh = curl_multi_init();//创建多个curl语柄
 	foreach($array as $k=>$f)
	{
		$url=$u.$f;
 		$conn[$k]=curl_init($url); 		
        curl_setopt($conn[$k], CURLOPT_TIMEOUT, $timeout);//设置超时时间
        curl_setopt($conn[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($conn[$k], CURLOPT_MAXREDIRS, 7);//HTTp定向级别
        curl_setopt($conn[$k], CURLOPT_HEADER, 0);//这里不要header，加块效率
        curl_setopt($conn[$k], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($conn[$k], CURLOPT_RETURNTRANSFER,1);
		if($method=='post')
		{
			$urlarr = parse_url($url);
			parse_str($urlarr['query'],$post_data);
			curl_setopt($conn[$k], CURLOPT_POST,1);
			curl_setopt($conn[$k], CURLOPT_POSTFIELDS, $post_data);		
		}
        curl_multi_add_handle ($mh,$conn[$k]);		
 	}
	 //防止死循环耗死cpu 这段是根据网上的写法
		do {
			$mrc = curl_multi_exec($mh,$active);//当无数据，active=true
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);//当正在接受数据时
		while ($active and $mrc == CURLM_OK) {//当无数据时或请求暂停时，active=true
			if (curl_multi_select($mh) != -1) 
			{
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		} 	
	foreach ($array as $k => $url) 
	{
		curl_error($conn[$k]);
		$res[$k]=curl_multi_getcontent($conn[$k]);//获得返回信息
		$header[$k]=curl_getinfo($conn[$k]);//返回头信息
		curl_close($conn[$k]);//关闭语柄
		curl_multi_remove_handle($mh  , $conn[$k]);   //释放资源  
	}		
	curl_multi_close($mh);	
	foreach($res as $i=>$v)
	{
		//echo iconv('utf-8','gb2312',$v);
		$xml = simplexml_load_string($v);
		$res[$i]=trim($xml[0]);
	}		
	return $res;  
}
*/

function webService($func,$post_data=array()) 
{
	global $_S;	
	//$client = new SoapClient('http://'.$_S['canshu']['webservip'].'/Algorithm.asmx?WSDL',array("trace"=>true));
	$client = new SoapClient('http://'.$_S['canshu']['webservip'].'/Algorithm.asmx?WSDL');	 
	$client->soap_defencoding = 'utf-8'; 
	$client->decode_utf8 = false;  
	$client->xml_encoding = 'utf-8';		
	$headers = new SoapHeader('http://localhost/','header',array('name'=>'web','password'=>'112233'));
	$client->__setSoapHeaders(array($headers));
	$result = $client->__Call($func, array($post_data));
	if (is_soap_fault($result))
	{
	    trigger_error("SOAP Fault: ", E_USER_ERROR);
		//echo $client->__getLastRequest();  
   		// echo $client->__getLastResponse();
		return array();
	}
	else
	{	
		$result=objtoarr($result);//只返回一个，但数组下标不一样
		foreach($result as $re)
		{	
			/*if($func=='JD_Regist_Money' && $re==1)
			{
				require_once(ROOT."/include/approvedpoint.class.php");
				$app=new approvedpoint();
				$app->doapproved($post_data['ID'],2);
			}*/		
			return $re;	
		}
	}	
}

function webService2($func,$post_data=array()) 
{
	global $_S;	
	require_once(ROOT."/include/nusoap/lib/nusoap.php");	
	$client = new nusoap_client('http://'.$_S['canshu']['webservip'].'/Algorithm.asmx?WSDL',true);	
	$client->soap_defencoding = 'utf-8';   
	$client->decode_utf8 = false;   
	$client->xml_encoding = 'utf-8'; 	
	$client-> setHeaders("<header xmlns='http://localhost/'><name>web</name><password>112233</password></header>");
	$result=$client->call($func,$post_data);
	//echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';

		//echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
	if (!$err=$client->getError()) 
	{
		if(is_array($result))
		{
			foreach($result as $re)
			{			
				return $re;	
			}
		}
		else
		{
			return array();	
		}
	}
	else
	{
		echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';

		echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
		echo " 错误 :",$err;
	}
	
}

/*//GetListInfo(int Start, int Num)
$lis=webServiceList('GetListInfo',array("Start"=>0,'Num'=>100));
print_r($lis);
exit();*/
//echo webService1('Regist');
//$result=webService('GetListInfo',array('Start'=>0,'Num'=>500));

//echo webService("Z_Static_Update_Regist",array('ID'=>'b159d95e-aab7-406d-8769-6ce606ebe589','Month'=>12));
/*echo '<br>';
echo webService('C_Consume',array("ID"=>'7b101e61-8181-40ea-ac72-a24ec96fbe2f',"Money"=>100,"MoneyType"=>2,"Count"=>1));
echo '<br>';
echo webService('C_Consume',array("ID"=>'7b101e61-8181-40ea-ac72-a24ec96fbe2f',"Money"=>100,"MoneyType"=>2,"Count"=>2));
exit();*/
function c_cal()//计算返利
{
	global $_S;
	$bl=explode(',',$_S['canshu']['fenhongbili']);
	$post_data=array();
	$post_data['dividends']=explode(',',$_S['canshu']['guquandaxiao']);
	$post_data['type16']=$bl[0];
	$post_data['type12016']=$bl[1];
	$post_data['Probability']=explode(',',$_S['canshu']['Probability']);
	$post_data['Probability12016']=explode(',',$_S['canshu']['Probability']);
	return webService('C_Cal',$post_data);
}

function getjifen($money)
{
	global $_S;
	$lv=$_S['canshu']['jifenxianjin'];	
	return format_price($money * $lv);	
}
function getmoney($jifen)
{
	global $_S;
	$lv=$_S['canshu']['jifenxianjin'];	
	return $jifen/$lv;
}
function getuserno($user_id)
{
	$len=strlen($user_id);
	if($len<8)
	{	$str='';
		for($i=0;$i<8-$len;$i++)
		{
			$str.='0'; 	
		}
		return $str.$user_id;
	}
	else
	{
		return $user_id;
	}
}
function outer_call($func, $params=null)
{
	include_once("../uc_client/client.php");	
    restore_error_handler();

    $res = call_user_func_array($func, $params);

    set_error_handler('exception_handler');

    return $res;
}

function format_price($money,$len=5,$type=1)//type 1是四舍五舍 2是四入五入
{
	if($type==1)
	{	
		//$pri=substr(sprintf("%.6f", $mun), 0, -1);
		$_arr=explode('.',$money);
		if(isset($_arr[1]))
		{
			$_a=substr($_arr[1],0,5);
			$pri=$_arr[0].'.'.$_a;
		}
		else
		{
			$pri=$_arr[0];	
		}
	}
	else
	{
		$pri=ceil($money*100000)/100000;		
	}
	return $pri;
}

?>