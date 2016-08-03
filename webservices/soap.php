<?php 
ini_set("soap.wsdl_cache_enabled", "0");//soap缓存
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
*/
 

/*//GetListInfo(int Start, int Num)
$lis=webServiceList('GetListInfo',array("Start"=>0,'Num'=>100));
print_r($lis);
exit();*/
function webService($func,$post_data=array()) 
{
	$client = new SoapClient('http://192.168.1.150:888/Algorithm.asmx?WSDL');	 
	$client->soap_defencoding = 'gb2312'; 
	$client->decode_utf8 = false;  
	$client->xml_encoding = 'utf-8';	 
	


	
	
$headers = new SoapHeader('http://localhost/','header',array('name'=>'admin','password'=>'123456'));
//设置客户端调用函数发送的头
$client->__setSoapHeaders(array($headers));

	$result = $client->__Call($func, array($post_data));
	
	if (is_soap_fault($result))
	{
	    trigger_error("SOAP Fault: ", E_USER_ERROR);
		return '';
	}
	else
	{		

		$result=objtoarr($result);//只返回一个，但数组下标不一样
		
		foreach($result as $re)
		{			
			return $re;	
		}
	}	
}
echo webService('Regist');
?>