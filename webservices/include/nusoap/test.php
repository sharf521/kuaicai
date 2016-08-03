<?php
require_once("lib/nusoap.php");


//初始化客户端对象，这个对象是类 soapclient 的一个实例，

//把服务程序的 URL 地址传递给soapclient类的构造函数。

//$client = new soapclient('http://192.168.1.123/nusoap/nusoap_server1.php?wsdl',true);
$client = new nusoap_client('http://192.168.1.150:888/Algorithm.asmx?WSDL',true);


$client->soap_defencoding = 'utf-8';   
$client->decode_utf8 = false;   
$client->xml_encoding = 'utf-8'; 
 

//利用客户端对象的 call 方法调用 WEB 服务的程序

//$str=$client->call('concatenate',array('str1'=>'sdf','str2'=>'dfd'));

//$str=$client->call('hello');


//$parameters=array('字符串1','字符串2');

/*
$str=$client->call('concatenate',$parameters);
if (!$err=$client->getError()) {

 echo " 程序返回 :",$str;

} else {

echo " 错误 :",$err;

}
*/

//$proxy=$client -> getProxy(); // 创建代理对象 (soap_proxy 类 )
//$str=$proxy->Regist(array());
//$str=$client->call('Regist');

//$result=$proxy->GetListInfo(array('Start'=>0,'Num'=>1));
//print_r($result);
//exit();

//设置客户端调用函数发送的头
$client -> setHeaders("<header xmlns='http://localhost/'><name>admin</name><password>123456</password></header>");




$result=$client->call('Regist',array('Start'=>0,'Num'=>1));

print_r(iconv('utf-8','gb2312',$result['RegistResult']));
 echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';

echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';


//$str=$proxy->concatenate(" 参数 1"," 参数 2"); // 直接调用 WEB 服务
//echo $proxy->hello();

if (!$err=$client->getError()) {

echo " 程序返回 :".print_r($str);

} else {

echo " 错误 :",$err;

}




?>