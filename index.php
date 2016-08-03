<?php


error_reporting(7);
//session_cache_limiter('private, must-revalidate');//返回页面不清空缓存
define('ROOT_PATH', dirname(__FILE__));
include(ROOT_PATH . '/eccore/ecmall.php');
$_S=array();

date_default_timezone_set('Asia/Shanghai');//时区配置
/* 定义配置信息 */
ecm_define(ROOT_PATH . '/data/config.inc.php');
//$webserv_ip1='116.255.156.154:5858';
/*$webserv_ip1='192.168.1.123:8000';*/
//$p2purl="http://www.ilaijin.com";

//$MEMBER_TYPE='uc';
if(extension_loaded('zlib'))
{
	@ini_set('zlib.output_compression', 'On');
	@ini_set('zlib.output_compression_level', '9');
}
/* 启动ECMall */
ECMall::startup(array(
    'default_app'   =>  'default',
    'default_act'   =>  'index',
    'app_root'      =>  ROOT_PATH . '/app',
    'external_libs' =>  array(
        ROOT_PATH . '/includes/global.lib.php',
        ROOT_PATH . '/includes/libraries/time.lib.php',
        ROOT_PATH . '/includes/ecapp.base.php',
        ROOT_PATH . '/includes/plugin.base.php',
        ROOT_PATH . '/app/frontend.base.php',
        ROOT_PATH . '/includes/subdomain.inc.php',
    ),
));


?>
