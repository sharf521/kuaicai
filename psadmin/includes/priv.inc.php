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
        'default'     => 'default|all',//��̨��¼
        'setting'     => 'setting|all',//��վ����
        'region'       => 'region|all',//��������
        'payment'    => 'payment|all',//֧����ʽ
        'theme'     => 'theme|all',//��������
        'mailtemplate'   => 'mailtemplate|all',//�ʼ�ģ��
        'template'  => 'template|all',//ģ��༭
    ),
    'goods_admin' => array
    (
        'gcategory'    => 'gcategory|all',//�������
        'brand' => 'brand|all',//Ʒ�ƹ���
        'goods'    => 'goods|all',//��Ʒ����
        'recommend'    => 'recommend|all',//�Ƽ�����
    ),
    'store_admin' => array
    (
        'sgrade'    => 'sgrade|all',//���̵ȼ�
        'scategory'     => 'scategory|all',//���̷���
        'store'   => 'store|all',//���̹���
		'storelog'   => 'sgrade|all',//������ˮ
		'article_sto'   => 'article_sto|all',//���ǵ��̽���
		/*'store_jifen'   => 'store|all',*///���ѻ��ֻ���ɵ������ñ���
    ),
    'member' => array
    (
        'user'  => 'user|all',//��Ա����
        'admin' => 'admin|all',//����Ա����
        'notice' => 'notice|all',//��Ա֪ͨ
	    'invite' => 'invite|all',//�������
		'rongyu' => 'rongyu|all',//�������
		'jiekuan' => 'jiekuan|all',//������
		//'fenzhan' => 'fenzhan|all',//��վ����
//		'liushui' => 'liushui|all',//�ʽ���ˮ
//		'addm' => 'addm|all',//�����ʽ�
//		'tixian' => 'tixian|all',//���ֹ��� 
    ),
    'order' => array
    (
        'order'   => 'order|all',//��������
    ),
    'website' => array
    (
        'acategory'    => 'acategory|all',//���·���
        'article'      => array('article' => 'article|all', 'upload' => array('comupload' => 'comupload|all', 'swfupload' => 'swfupload|all')),//���¹���
        'partner'      => 'partner|all',//�������
        'navigation'   => 'navigation|all',//ҳ�浼��
        'db'           => 'db|all',//���ݿ�
        'groupbuy'     => 'groupbuy|all',//�Ź�
        'consulting'   => 'consulting|all',//��ѯ
        'share_link'   => 'goods_share|all',//�������
		'adv'   => 'adv|all',//������
		'coupon'   => 'coupon|all',//�Ż�ȯ����
		'fenzhan'   => 'fenzhan|all',//վ�����
		'webs'   => 'fenzhan|all',//webservice����
		'gonghuo'   => 'gonghuo|all',//webservice����
		'tousu'   => 'tousu|all',//Ͷ�߹���
		'site_system'   => 'site_system|all',//��ҵ����

    ),

    'external' => array
    (
        'plugin' => 'plugin|all',//�������
        'module'   => 'module|all',//ģ�����
        'widget'   => 'widget|all',//�Ҽ�����
    ),
    'clear_cache' =>array
    (
        'clear_cache' => 'clear_cache|all',//��ջ���
    )
);
?>