<?php
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(7);
//define('ROOT', dirname(__FILE__).'/');
define('ROOT', dirname($_SERVER['SCRIPT_FILENAME']).'/');
$_G=array();
require 'core/init.php';
require 'data/config.php';
require 'core/function.php';
require 'core/URI.php';
$uriClass=new URI();
require 'core/page.class.php';
$pager=new Page();
require 'core/mysql.class.php';
$mysql = new Mysql($db_config);
require 'core/Controller.php';
$_G['class']=($uriClass->get(0)!='')?$uriClass->get(0):'index';
$_G['func']=($uriClass->get(1)!='')?$uriClass->get(1):'index';


//域名处理
$domain=strtolower($_SERVER['HTTP_HOST']);
$domain=explode('.',$domain);
//店铺域名
$_G['domain_shop']=$domain[0];
//判断是否存在二级域名
$_G['storeid_shop']=0;
if(strpos($_G['domain_shop'], 'shop-') !== FALSE)
{
    $_G['storeid_shop']=(int)substr($_G['domain_shop'],5);
}

//主站域名
unset($domain[0]);
$_G['domain_city']=implode(".",$domain);
unset($domain);

//主站信息
$_G['city']=m('city/getcity',array('city_yuming'=>$_G['domain_city']));
$_G['domain_city']='http://www.'.$_G['domain_city'];
//主站logo处理
if(strpos(strtolower($_G['city']['city_logo']),'http://') ===false)
{
    $_G['city']['city_logo']=$_G['domain_city'].'/'.$_G['city']['city_logo'];
}

//店铺信息

if($_G['storeid_shop']>0)
{
    $_G['shop']=m('city/getshop',array('store_id'=>$_G['storeid_shop']));
}
else
{
    $_G['shop']=m('city/getshop',array('domain'=>$_G['domain_shop']));
}
if(!$_G['shop'])
{
    echo '信息错误，无此店铺！';exit;
}else{
    echo $_G['domain_city'].'/store/'.$_G['storeid_shop'];
}
$_G['shop']['store_owner']=m('city/getowner',array('user_name'=>$_G['shop']['owner_name']));
$_G['shop']['goods_count']=m('city/getcount',array('store_id'=>$_G['shop']['store_id']));
$_G['shop']['goods_countall']=m('city/getcountall',array('store_id'=>$_G['shop']['store_id']));
//店铺logo处理
if($_G['shop']['store_logo'])
{
    if(strpos(strtolower($_G['shop']['store_logo']),'http://') ===false)
    {
        $_G['shop']['store_logo']=$_G['domain_city'].'/'.$_G['shop']['store_logo'];
    }
}

//店铺横幅处理
if($_G['shop']['store_banner'])
{
    if(strpos(strtolower($_G['shop']['store_banner']),'http://') ===false)
    {
        $_G['shop']['store_banner']=$_G['domain_city'].'/'.$_G['shop']['store_banner'];
    }
}

//店铺导航
$_G['navs']=m('city/getnavs',array('store_id'=>$_G['shop']['store_id']));

//店铺分类
$_G['cate']=array();
$cate=m('city/getcate',array('store_id'=>$_G['shop']['store_id']));
foreach($cate as $value)
{
    if($value['parent_id']==0)
    {
        $_G['cate'][$value['cate_id']]=$value;
    }
    else
    {
        $_G['cate'][$value['parent_id']]['son'][$value['cate_id']]=$value;
    }
}

//店铺承诺
$_G['promise']=m('city/getpromise',array('store_id'=>$_G['shop']['store_id']));

//print_r($_G['city']);
//print_r($_SESSION);


//联动值
//$_G['linkpage']=m('linkpage/getlinkpage');
//参数
//$_G['system']=m('system/lists');

/*if($_G['class']==$_G['system']['houtai'])
{
	$_G['class']=($uriClass->get(1)!='')?$uriClass->get(1):'index';
	$_G['func']	=($uriClass->get(2)!='')?$uriClass->get(2):'index';
	require 'control/admin/index.php';
	exit;
}
elseif($_G['class']=='member')
{
	$_G['class']=($uriClass->get(1)!='')?$uriClass->get(1):'index';
	$_G['func']	=($uriClass->get(2)!='')?$uriClass->get(2):'index';
	require 'control/member/index.php';
	exit;
}
else*/
if(file_exists('control/'.$_G['class'].'.php'))
{	
	require ROOT.'control/'.$_G['class'].'.php';
	$class   = new $_G['class'];
	if($class)
	{
		if(method_exists($class,$_G['func']))
		{
			return call_user_func(array($class,$_G['func']),array());
		}
		else
			return call_user_func(array($class,'error'),array());
	}
	else
	{
		//return false;
		die("error class({$_G['class']}) method({$_G['func']})");
	}
}
else
{
	echo 'page error';	
}