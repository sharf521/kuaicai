<?
ini_set("max_execution_time", "1800000"); 
ini_set('default_socket_timeout',600000);
error_reporting(7);
//error_reporting(E_ALL || ~E_NOTICE);
//error_reporting(E_ALL );//�������д���

header("content-Type: text/html; charset=GBK");

define('ROOT', dirname(__FILE__));
//define('ROOT_PATH', dirname(__FILE__) . '/../');
require_once(ROOT.'/config.php');



/* ��ʼ������ */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

/* �жϲ�ͬϵͳ�ָ��� */
if (DIRECTORY_SEPARATOR == '\\'){
    @ini_set('include_path','.;' . ROOT_PATH);
}else{
    @ini_set('include_path','.:' . ROOT_PATH);
}

date_default_timezone_set('Asia/Shanghai');//ʱ������

//memcache ��ʹ��
$memcache_result  = "0";
$memcache = "";
$memcachelife = "60";


/*//����ɫsession id������
ini_set('session.name', 'sid');
//��ʹ�� GET/POST ������ʽ
ini_set('session.use_trans_sid', 0);
//�������������������ʱ��
ini_set('session.gc_maxlifetime', 3600);
//ʹ�� COOKIE ���� SESSION ID �ķ�ʽ
ini_set('session.use_cookies', 1);
ini_set('session.cookie_path', '/');
//������������ SESSION ID �� COOKIE,ע��˴�����Ϊһ������
ini_set('session.cookie_domain', '.nb.cn');*/

session_cache_limiter('private, must-revalidate');//����ҳ�治��ջ��� 
session_start();
?>