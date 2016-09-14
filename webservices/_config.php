<?php
if (!defined('ROOT'))  die('Access Denied');//防止直接访问

require_once('../data/config.inc.php');

/*$db_config['host']     = 'localhost';      //数据库主机	
$db_config['user']     = 'root';      //数据库用户名	
$db_config['pwd']      = 'root';  //数据库用户密码	
$db_config['name']     = 'kuaicai';      //数据库名	*/
$db_config['port']     = '3306';      //端口		
$db_config['prefix']   = 'ecm_'; //CMS表名前缀	
$db_config['language'] = 'gbk'; //数据库字符集 gbk,latin1,utf8,utf8..

require_once(ROOT.'/include/mysql.class.php');
$db = new Mysql($db_config);


define("SiteName",'WebServices控台');
$type_article=array('一般','首页');





define('UC_CONNECT', '');
define('UC_DBHOST', 'localhost');
define('UC_DBUSER', 'root');
define('UC_DBPW', 'root');
define('UC_DBNAME', 'ucenter');
define('UC_DBCHARSET', 'gbk');
define('UC_DBTABLEPRE', '`ucenter`.uc_');
define('UC_DBCONNECT', '0');
define('UC_KEY', '!@#1234567890*()');
define('UC_API', 'http://uc.zhuzhan.cn');
define('UC_CHARSET', 'gbk');
define('UC_IP', '');
define('UC_APPID', '1');
define('UC_PPP', '20');





$_S['buytype']=array(
	array('buytype'=>0,'name'=>'帝国版','price'=>1550),
	array('buytype'=>1,'name'=>'企业版','price'=>550),
	array('buytype'=>2,'name'=>'行业龙头','price'=>350),
	array('buytype'=>3,'name'=>'白金','price'=>8),
	array('buytype'=>4,'name'=>'金牌','price'=>2.75),
	array('buytype'=>5,'name'=>'银牌','price'=>2.2),
	array('buytype'=>6,'name'=>'基础','price'=>1.58),
	array('buytype'=>7,'name'=>'铜牌','price'=>0.58),
	
	array('buytype'=>8,'name'=>'增值服务中心','price'=>5.8),	
	array('buytype'=>9,'name'=>'增值服务站','price'=>0.58),		
);
$_S['buytype_dj']=array(9300000,3300000,2100000,39000,14000,10600,7800,2820,28200,2820);

?>