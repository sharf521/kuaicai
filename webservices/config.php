<?php
if (!defined('ROOT'))  die('Access Denied');//��ֱֹ�ӷ���

require_once('../data/config.inc.php');

/*$db_config['host']     = 'localhost';      //���ݿ�����	
$db_config['user']     = 'root';      //���ݿ��û���	
$db_config['pwd']      = 'root';  //���ݿ��û�����	
$db_config['name']     = 'kuaicai';      //���ݿ���	*/
$db_config['port']     = '3306';      //�˿�		
$db_config['prefix']   = 'ecm_'; //CMS����ǰ׺	
$db_config['language'] = 'gbk'; //���ݿ��ַ��� gbk,latin1,utf8,utf8..

require_once(ROOT.'/include/mysql.class.php');
$db = new Mysql($db_config);


define("SiteName",'WebServices��̨');
$type_article=array('һ��','��ҳ');





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
	array('buytype'=>0,'name'=>'�۹���','price'=>1550),
	array('buytype'=>1,'name'=>'��ҵ��','price'=>550),
	array('buytype'=>2,'name'=>'��ҵ��ͷ','price'=>350),
	array('buytype'=>3,'name'=>'�׽�','price'=>8),
	array('buytype'=>4,'name'=>'����','price'=>2.75),
	array('buytype'=>5,'name'=>'����','price'=>2.2),
	array('buytype'=>6,'name'=>'����','price'=>1.58),
	array('buytype'=>7,'name'=>'ͭ��','price'=>0.58),
	
	array('buytype'=>8,'name'=>'��ֵ��������','price'=>5.8),	
	array('buytype'=>9,'name'=>'��ֵ����վ','price'=>0.58),		
);
$_S['buytype_dj']=array(9300000,3300000,2100000,39000,14000,10600,7800,2820,28200,2820);

?>