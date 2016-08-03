<?php

/**

 * $Id: inc.menu.php 16 2007-12-23 15:36:24Z Redstone $
 */

if (!defined('IN_ECM'))
{
    trigger_error('Hacking attempt', E_USER_ERROR);
}

$menu_data = array
(
    'mall_setting' => array
    (
        'default'     => 'default|all',//后台登录
        'setting'     => 'setting|all',//网站设置
        'region'       => 'region|all',//地区设置
        'payment'    => 'payment|all',//支付方式
        'theme'     => 'theme|all',//主题设置
        'mailtemplate'   => 'mailtemplate|all',//邮件模板
        'template'  => 'template|all',//模板编辑
    ),
    'goods_admin' => array
    (
        'gcategory'    => 'gcategory|all',//分类管理
        'brand' => 'brand|all',//品牌管理
        'goods'    => 'goods|all',//商品管理
        'recommend'    => 'recommend|all',//推荐类型
    ),
    'store_admin' => array
    (
        'sgrade'    => 'sgrade|all',//店铺等级
        'scategory'     => 'scategory|all',//店铺分类
        'store'   => 'store|all',//店铺管理
		'storelog'   => 'sgrade|all',//店铺流水
		'article_sto'   => 'article_sto|all',//明星店铺介绍
		/*'store_jifen'   => 'store|all',*///消费积分换算成店铺信用比例
    ),
    'member' => array
    (
        'user'  => 'user|all',//会员管理
        'admin' => 'admin|all',//管理员管理
        'notice' => 'notice|all',//会员通知
	    'invite' => 'invite|all',//邀请好友
		'rongyu' => 'rongyu|all',//邀请好友
		'jiekuan' => 'jiekuan|all',//借款管理
		//'fenzhan' => 'fenzhan|all',//分站管理
//		'liushui' => 'liushui|all',//资金流水
//		'addm' => 'addm|all',//增加资金
//		'tixian' => 'tixian|all',//提现管理 
    ),
    'order' => array
    (
        'order'   => 'order|all',//订单管理
    ),
    'website' => array
    (
        'acategory'    => 'acategory|all',//文章分类
        'article'      => array('article' => 'article|all', 'upload' => array('comupload' => 'comupload|all', 'swfupload' => 'swfupload|all')),//文章管理
        'partner'      => 'partner|all',//合作伙伴
        'navigation'   => 'navigation|all',//页面导航
        'db'           => 'db|all',//数据库
        'groupbuy'     => 'groupbuy|all',//团购
        'consulting'   => 'consulting|all',//咨询
        'share_link'   => 'goods_share|all',//分享管理
		'adv'   => 'adv|all',//广告管理
		'coupon'   => 'coupon|all',//优惠券管理
		'fenzhan'   => 'fenzhan|all',//站点管理
		'webs'   => 'fenzhan|all',//webservice管理
		'gonghuo'   => 'gonghuo|all',//webservice管理
		'tousu'   => 'tousu|all',//投诉管理
		'site_system'   => 'site_system|all',//企业管理

    ),

    'external' => array
    (
        'plugin' => 'plugin|all',//插件管理
        'module'   => 'module|all',//模块管理
        'widget'   => 'widget|all',//挂件管理
    ),
    'clear_cache' =>array
    (
        'clear_cache' => 'clear_cache|all',//清空缓存
    )
);
?>