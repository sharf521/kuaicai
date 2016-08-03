<?php
date_default_timezone_set('Asia/Shanghai');//ʱ������
define('ROOT', realpath(dirname(__FILE__).'/../'));
require_once(ROOT.'/data/config.inc.php');

$db_config['port']     = '3306';      //�˿�		
$db_config['prefix']   = 'ecm_'; //CMS����ǰ׺	
$db_config['language'] = 'gbk'; //���ݿ��ַ��� gbk,latin1,utf8,utf8..

session_cache_limiter('private, must-revalidate');//����ҳ�治��ջ��� 
session_start();

require_once('mysql.class.php');
$db = new Mysql($db_config);

function chongzhi_award($money,$uid,$OrdId,$host)
{
	global $db;	
	
	$row=$db->get_one("select city from {member} where user_id='$uid' limit 1");
	$city=$row['city'];
	$row=null;
	
	//$row=$db->get_one("select user_id,tuijianren_id from {city}  where city_yuming like '%".$host."%'  limit 1");
	$row=$db->get_one("select user_id,tuijianren_id from {city}  where city_id='$city' limit 1");
		
	$user_id=$row['user_id'];	
	$tuijianren=explode(',',$row['tuijianren_id']);
	$row=null;	
	//��վ����
	$amoney=$money*0.0004;
	$row=$db->get_one("select user_name,money,duihuanjifen,dongjiejifen,money_dj,qianbiku,city from {my_money} where user_id='$user_id' limit 1");
	if($row)
	{
		$user_name=$row['user_name'];
		$dq_money=$row['money'];
		$dq_money_dj=$row['money_dj'];
		$dq_jifen=$row['duihuanjifen'];
		$dq_jifen_dj=$row['dongjiejifen'];
		$city=$row['city'];	
		//��ֵ��ˮ��־
		$arr=array(
			'money'=>$amoney,
			'jifen'=>0,
			'money_dj'=>0,
			'jifen_dj'=>0,
			'user_id'=>$user_id,
			'user_name'=>$user_name,
			'type'=>102,
			's_and_z'=>1,
			'time'=>date('Y-m-d H:i:s'),
			'zcity'=>$city,
			'dq_money'=>$dq_money+$amoney,
			'dq_money_dj'=>$dq_money_dj,
			'dq_jifen'=>$dq_jifen,				
			'dq_jifen_dj'=>$dq_jifen_dj,
			'orderid'=>$OrdId,
			'beizhu'=>"��ֵ��{$uid}"
		);		
		$db->insert('{moneylog}',$arr);
		$db->query("update {my_money} set money=money+$amoney where user_id='$user_id' limit 1");//�����ʻ��ʽ�		
	}  
	$row=null; 
	
	$amoney=$money*0.0003;
	foreach($tuijianren as $i=>$user_id)
	{
		if($i>1){break;}
		if((int)$user_id!=0)
		{
			$row=$db->get_one("select user_name,money,duihuanjifen,dongjiejifen,money_dj,qianbiku,city from {my_money} where user_id='$user_id' limit 1");
				$user_name=$row['user_name'];
				$dq_money=$row['money'];
				$dq_money_dj=$row['money_dj'];
				$dq_jifen=$row['duihuanjifen'];
				$dq_jifen_dj=$row['dongjiejifen'];
				$city=$row['city'];
			$row=null;
			//��ֵ��ˮ��־
			$arr=array(
				'money'=>$amoney,
				'jifen'=>0,
				'money_dj'=>0,
				'jifen_dj'=>0,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>103,
				's_and_z'=>1,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$dq_money+$amoney,
				'dq_money_dj'=>$dq_money_dj,
				'dq_jifen'=>$dq_jifen,				
				'dq_jifen_dj'=>$dq_jifen_dj,
				'orderid'=>$OrdId,
				'beizhu'=>"��ֵ��{$uid}"
			);			
			$db->insert('{moneylog}',$arr);
			$db->query("update {my_money} set money=money+$amoney where user_id='$user_id' limit 1");//�����ʻ��ʽ�
		}
	}
}