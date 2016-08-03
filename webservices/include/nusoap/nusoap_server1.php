<?php

require_once("lib/nusoap.php");

//定义服务程序

function hello() {return 'Hello World!';}

//初始化服务对象 , 这个对象是类 soap_server 的一个实例

$soap = new soap_server;

 

//调用服务对象的 register 方法注册需要被客户端访问的程序。

//只有注册过的程序，才能被远程客户端访问到。

$soap->register('hello');

 

//最后一步，把客户端通过 post 方式提交的数据，传递给服务对象的 service 方法。

//service 方法处理输入的数据，调用相应的函数或方法，并且生成正确的反馈，传回给客户端。

$soap->service($HTTP_RAW_POST_DATA);

?>