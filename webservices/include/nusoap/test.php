<?php
require_once("lib/nusoap.php");


//��ʼ���ͻ��˶�������������� soapclient ��һ��ʵ����

//�ѷ������� URL ��ַ���ݸ�soapclient��Ĺ��캯����

//$client = new soapclient('http://192.168.1.123/nusoap/nusoap_server1.php?wsdl',true);
$client = new nusoap_client('http://192.168.1.150:888/Algorithm.asmx?WSDL',true);


$client->soap_defencoding = 'utf-8';   
$client->decode_utf8 = false;   
$client->xml_encoding = 'utf-8'; 
 

//���ÿͻ��˶���� call �������� WEB ����ĳ���

//$str=$client->call('concatenate',array('str1'=>'sdf','str2'=>'dfd'));

//$str=$client->call('hello');


//$parameters=array('�ַ���1','�ַ���2');

/*
$str=$client->call('concatenate',$parameters);
if (!$err=$client->getError()) {

 echo " ���򷵻� :",$str;

} else {

echo " ���� :",$err;

}
*/

//$proxy=$client -> getProxy(); // ����������� (soap_proxy �� )
//$str=$proxy->Regist(array());
//$str=$client->call('Regist');

//$result=$proxy->GetListInfo(array('Start'=>0,'Num'=>1));
//print_r($result);
//exit();

//���ÿͻ��˵��ú������͵�ͷ
$client -> setHeaders("<header xmlns='http://localhost/'><name>admin</name><password>123456</password></header>");




$result=$client->call('Regist',array('Start'=>0,'Num'=>1));

print_r(iconv('utf-8','gb2312',$result['RegistResult']));
 echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';

echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';


//$str=$proxy->concatenate(" ���� 1"," ���� 2"); // ֱ�ӵ��� WEB ����
//echo $proxy->hello();

if (!$err=$client->getError()) {

echo " ���򷵻� :".print_r($str);

} else {

echo " ���� :",$err;

}




?>