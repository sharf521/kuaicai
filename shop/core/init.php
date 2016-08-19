<?php

//error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(7);
# 基础抬头 其中第三项释放的信息在浏览器debug时可见.
header('Content-language: zh');  
header('Content-type: text/html; charset=utf-8');
header('X-Powered-By: JAVA');

# 设置php文件永远不缓存. 可以在后面进行叠加影响的.
header('Pragma: no-cache');
header('Cache-Control: private',false); // required for certain browsers 
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');  
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

#获取当前一级域名
$domin=strtolower($_SERVER['HTTP_HOST']);
$domin=explode('.',$domin);
unset($domin[0]);
$domin=implode('.',$domin);

//多主机共享保存 SESSION ID 的 COOKIE
ini_set('session.cookie_domain',$domin);

session_cache_limiter('private,must-revalidate');
session_start();

date_default_timezone_set('Asia/Shanghai');//时区配置

# 设置执行时间,内部字符编码.
set_time_limit($set_time = 3600);
//mb_internal_encoding('utf-8');
# 核心设置
/*
@ini_set('session.name','PHPSESSID');
@ini_set('max_execution_time',$set_time);
@ini_set('max_input_time',$set_time);
@ini_set('zend.ze1_compatibility_mode', false);
@ini_set('precision', 72);
@ini_set('session.gc_maxlifetime',3600);    //设置垃圾回收最大生存时间
@ini_set('session.gc_probability',30);      //和session.gc_divisor一起构成清除垃圾的执行几率
@ini_set('session.gc_divisor',100);
@ini_set('display_errors', 'On');


if(extension_loaded('zlib'))
{
	@ini_set('zlib.output_compression', 'On');
	@ini_set('zlib.output_compression_level', '9');
}
# 判断对引入字符的转入判断. 都设置为假.
if (version_compare(PHP_VERSION, '5.3.0', '<') && function_exists('set_magic_quotes_runtime')) {
	set_magic_quotes_runtime(false);
}

ini_http_server(); // this href
*/





# 清空$_ENV数组, 释放掉$_SERVER数组中几个关键性数值. 
unset($_ENV, $_SERVER['MIBDIRS'],$_SERVER['MYSQL_HOME'],$_SERVER['OPENSSL_CONF'],$_SERVER['PHP_PEAR_SYSCONF_DIR'],$_SERVER['PHPRC'],$_SERVER['SystemRoot'],$_SERVER['COMSPEC'],$_SERVER['PATHEXT'], $_SERVER['WINDIR'],$_SERVER['PATH']);

function ini_http_server()
{
    if (!$_SERVER['REQUEST_URI']) { // IIS 5 compatibility
    	$_SERVER['REQUEST_URI'] = $_SERVER['ORIG_PATH_INFO'];
    }
    if (!strpos($_SERVER['REQUEST_URI'], '?') && $_SERVER['QUERY_STRING'] != '') { // IIS 7 compatibility
    	$_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
    }
    $_SERVER['REQUEST_URI'] = strtr($_SERVER['REQUEST_URI'], array('&&'=>'&'));
    $HTTPS = $_SERVER['HTTPS'] && strcasecmp($_SERVER['HTTPS'], 'off');
    
    $php_selfs = rawurlencode(dirname($_SERVER['SCRIPT_NAME']));
    $php_selfs = strtr($php_selfs, array('%2F'=>'/','%5C'=>'/'));
    $php_selfs = trim($php_selfs, '/');
 
    if($php_selfs)
        $_SERVER['REQUEST_URI'] = strtr($_SERVER['REQUEST_URI'], array($php_selfs.'/'=>''));
    
    $httppre = (!$HTTPS)?'http':'https';
    if($_SERVER['SERVER_PORT'] !== '80' && $_SERVER['SERVER_PORT'] !== '443'){
        $host  = trim($httppre.'://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].'/'.$php_selfs,'/').'/';
    }else{
        $php_selfs && $php_selfs ='/'.$php_selfs;
        $host  = trim($httppre.'://'.$_SERVER['HTTP_HOST'].$php_selfs,'/').'/';
    }
    
    $files = trim($_SERVER['REQUEST_URI'], './');
    $_SERVER['HTTP_URL'] = $host.$files;
    $_SERVER['HTTP_PATH'] = $_SERVER['DOCUMENT_ROOT'].'/';
    $_SERVER['HTTP_IP'] = max(getenv('SERVER_ADDR'),getenv('REMOTE_ADDR'),getenv('HTTP_X_FORWARDED_FOR'),getenv('HTTP_CLIENT_IP'));
    $_SERVER['HTTP_TIME_FLOAT'] = microtime(true);
    $_SERVER['HTTP_MEMORY_PEAK_USAGE'] = memory_get_peak_usage();
    $_SERVER['HTTP_MEMORY_USAGE'] = memory_get_usage();
}
# 设置一个结束时调用的函数. 请自行修改函数名.
function_exists('register_shutdown_function') && register_shutdown_function('ini_end');
function ini_end(){}

/*$request_uri = explode("?",$_SERVER['REQUEST_URI']);
if(isset($request_uri[1])){
	$rewrite_url = explode("&",$request_uri[1]);
	foreach ($rewrite_url as $key => $value){
		$_value = explode("=",$value);
		if (isset($_value[1])){
			$_REQUEST[$_value[0]] = addslashes($_value[1]);
		}
	}
}*/
//define('ROOT', dirname(__FILE__));