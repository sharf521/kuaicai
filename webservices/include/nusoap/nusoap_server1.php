<?php

require_once("lib/nusoap.php");

//����������

function hello() {return 'Hello World!';}

//��ʼ��������� , ����������� soap_server ��һ��ʵ��

$soap = new soap_server;

 

//���÷������� register ����ע����Ҫ���ͻ��˷��ʵĳ���

//ֻ��ע����ĳ��򣬲��ܱ�Զ�̿ͻ��˷��ʵ���

$soap->register('hello');

 

//���һ�����ѿͻ���ͨ�� post ��ʽ�ύ�����ݣ����ݸ��������� service ������

//service ����������������ݣ�������Ӧ�ĺ����򷽷�������������ȷ�ķ��������ظ��ͻ��ˡ�

$soap->service($HTTP_RAW_POST_DATA);

?>