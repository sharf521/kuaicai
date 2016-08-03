<?
ini_set("max_execution_time", "1800000"); 
ini_set('default_socket_timeout',600000);
error_reporting(7);
//error_reporting(E_ALL || ~E_NOTICE);
//error_reporting(E_ALL );//报告所有错误

header("content-Type: text/html; charset=GBK");

define('ROOT', dirname(__FILE__));
//define('ROOT_PATH', dirname(__FILE__) . '/../');
require_once(ROOT.'/config.php');



/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

/* 判断不同系统分隔符 */
if (DIRECTORY_SEPARATOR == '\\'){
    @ini_set('include_path','.;' . ROOT_PATH);
}else{
    @ini_set('include_path','.:' . ROOT_PATH);
}

date_default_timezone_set('Asia/Shanghai');//时区配置

//memcache 的使用
$memcache_result  = "0";
$memcache = "";
$memcachelife = "60";


/*//设置色session id的名字
ini_set('session.name', 'sid');
//不使用 GET/POST 变量方式
ini_set('session.use_trans_sid', 0);
//设置垃圾回收最大生存时间
ini_set('session.gc_maxlifetime', 3600);
//使用 COOKIE 保存 SESSION ID 的方式
ini_set('session.use_cookies', 1);
ini_set('session.cookie_path', '/');
//多主机共享保存 SESSION ID 的 COOKIE,注意此处域名为一级域名
ini_set('session.cookie_domain', '.nb.cn');*/

session_cache_limiter('private, must-revalidate');//返回页面不清空缓存 
session_start();
?>