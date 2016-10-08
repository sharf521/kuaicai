<?php
session_start();
/**
 *    前台控制器基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class FrontendApp extends ECBaseApp 
{
    function __construct()
    {
        $this->FrontendApp();
    }
    function FrontendApp()
    {
        Lang::load(lang_file('common'));
        Lang::load(lang_file(APP));
        parent::__construct();


        // 判断商城是否关闭
       /* if (!Conf::get('site_status'))
        {
            $this->show_warning(Conf::get('closed_reason'));
            exit;
        }*/
		//$url=$_SERVER['HTTP_HOST'];//获得当前网址
		$this->city_mod=& m(city);
		//$city=$this->city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
		//$status=$city['status'];
		//$beizhu=$city['beizhu'];
		$cityrow=$this->city_mod->get_cityrow();
		$status=$cityrow['status'];
		$beizhu=$cityrow['beizhu'];
	
		if($status=='no')
		{
		 $this->show_warning($beizhu);
            exit;
		}
		
        # 在运行action之前，无法访问到visitor对象
$user_id=$this->visitor->get('user_id');

if($user_id)//判断是否有用户登录
{
	$this->jiekuan_mod=& m('jiekuan');
	$jie=$this->city_mod->getRow("select * from ".DB_PREFIX."jiekuan where user_id = '$user_id' and status=2 and status1=1 and jieshu_time<now() limit 1");	
	if($jie)//判断是否有符合条件的借款记录
	{
	  $this->moneylog_mod=& m('moneylog');
	  $this->accountlog_mod=& m('accountlog');
	  $this->canshu_mod=& m('canshu');
	  $this->my_money_mod=& m('my_money');
	
	 $result=$this->jiekuan_mod->getRow("select * from ".DB_PREFIX."my_money where user_id = '$user_id' limit 1");
	 $suoding_jifen=$result['suoding_jifen'];
	 if($result['money']>0)
	 {	
	 $money_yh=$jie['money_h'];//已还金额
	 $money_j=$jie['money_j'];//借款金额
	 $rate=$jie['rate'];	 
	 $zong_lixi=$this->jiekuan_mod->lixi($money_j,$rate,$jie['jieshu_time']);//逾期利息
	 $yinghuan_money=$money_j-$money_j*1/10-$money_yh+$zong_lixi;//应还金额
	
	 
	 $city=$result['city'];
	 $user_name=$result['user_name'];	 
	 $money=$result['money'];
	
	 $money_dj=$result['money_dj'];
	 $duihuanjifen=$result['duihuanjifen'];
	 $dongjiejifen=$result['dongjiejifen'];
	 $riqi=date('Y-m-d H:i:s');
	 if($money>=$yinghuan_money)//可以还清
	 {
		$shiji_moneyh=$yinghuan_money;
		$new_moneyh=$money_yh+$shiji_moneyh;//已还款总额
		
		$data=array('money_h'=>$new_moneyh,'end_time'=>$riqi,'status1'=>2,'faxi'=>$zonglixi,'is_suoding'=>0);
		$new_moneydj=$money_dj-$money_j*1/10;
		$jie_money=$money_j*1/10;
		
		if($jie['is_suoding']==1)
		{
			$text=Lang::get('jiechusuo');
			$text=str_replace('{1}',$shiji_moneyh,$text);
			$text=str_replace('{2}',$jie_money,$text);
			$new_suodingjifen=$suoding_jifen-100000;
		}
		else
		{
			$text=Lang::get('jiedongbaozheng');
			$text=str_replace('{1}',$shiji_moneyh,$text);
			$text=str_replace('{2}',$jie_money,$text);
			$new_suodingjifen=$suoding_jifen;
		}
		
	 }
	 else
	 {
		$shiji_moneyh=$money;		
		$new_moneyh=$money_yh+$shiji_moneyh;//已还款总额
		$data=array('money_h'=>$new_moneyh,'end_time'=>$riqi,'status1'=>1,'faxi'=>$zonglixi,'is_suoding'=>1);
		$new_moneydj=$money_dj;
		
		
		$jie_money=0;
		if($jie['is_suoding']==1)
		{
			$text=Lang::get('jiebaozhengjin');
			$text=str_replace('{1}',$shiji_moneyh,$text);
			$new_suodingjifen=$suoding_jifen;
		}
		else
		{
			$text=Lang::get('jiebaozheng');
			$text=str_replace('{1}',$shiji_moneyh,$text);
			$new_suodingjifen=$suoding_jifen+100000;
		}
		
	 }
	 $this->jiekuan_mod->edit('id='.$jie['id'],$data);
	 
	 
	 $new_money=$money-$shiji_moneyh;//用户账号剩余金额
	 
	 $can=$this->jiekuan_mod->can();
	 $zong_money=$can['zong_money'];
	 $zong_jifen=$can['zong_jifen'];
	 $new_zong_money=$zong_money+$shiji_moneyh;//总账户余额
	
	//添加用户资金流水	 
	$addlog=array(
		'money'=>'-'.$shiji_moneyh,
		'money_dj'=>'-'.$jie_money,
		'time'=>date('Y-m-d H:i:s'),
		'user_name'=>$user_name,
		'user_id'=>$user_id,
		'zcity'=>$city,
		'type'=>42,
		's_and_z'=>2,
		'beizhu'=>$text,
		'dq_money'=>$new_money,
		'dq_money_dj'=>$new_moneydj,
		'dq_jifen'=>$duihuanjifen,
		'dq_jifen_dj'=>$dongjiejifen
	);
	$this->moneylog_mod->add($addlog);	 
	$beizhu=Lang::get('zidong');
	//添加总账户资金流水
	$addaccoun=array(
		'money'=>$shiji_moneyh,
		'time'=>date('Y-m-d H:i:s'),
		'user_name'=>$user_name,
		'user_id'=>$user_id,
		'zcity'=>$city,
		'type'=>42,
		's_and_z'=>1,
		'beizhu'=>$beizhu,
		'dq_money'=>$new_zong_money,
		'dq_jifen'=>$zong_jifen,
	);
	$this->accountlog_mod->add($addaccoun);	 
	
	$this->my_money_mod->edit('user_id='.$user_id,array('money'=>$new_money,'money_dj'=>$new_moneydj,'suoding_jifen'=>$new_suodingjifen));
	$this->canshu_mod->edit('id=1',array('zong_money'=>$new_zong_money));	

	}
	else if($jie['is_suoding']!=1)
	{
	$new_suodingjifen=$suoding_jifen+100000;
	$this->my_money_mod->edit('user_id='.$user_id,array('suoding_jifen'=>$new_suodingjifen));
	$this->jiekuan_mod->edit('id='.$jie['id'],array('is_suoding'=>1));
	 }
	}
	}
}
    function _config_view()
    {
        parent::_config_view();
        $this->_view->template_dir  = ROOT_PATH . '/themes';
        $this->_view->compile_dir   = ROOT_PATH . '/temp/compiled/mall';
        $this->_view->res_base      = SITE_URL . '/themes';
    }
    function display($tpl)
    {
	
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	$this->_city_mod =& m('city');
	//$thiscity=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$thiscity=$this->_city_mod->get_cityrow();
	$city_id=$thiscity['city_id'];
		$ur=$_SERVER['HTTP_HOST'];
        $cart =& m('cart');
		
		if(isset($_GET['sub']))
		{
			$_arr_sub=explode('|',$_GET['sub']);
			if (file_exists("data/city/{$_arr_sub[0]}/{$_arr_sub[1]}.jpg"))
			{
				 
				$thiscity['city_logo']="data/city/{$_arr_sub[0]}/{$_arr_sub[1]}.jpg";
			}
		}
		
        $this->assign('cart_goods_kinds', $cart->get_kinds(SESS_ID, $this->visitor->get('user_id')));
        $this->assign('navs', $this->_get_navs());  // 自定义导航
        $this->assign('acc_help', ACC_HELP);        // 帮助中心分类code
		$this->assign('thiscity',$thiscity); 
		$this->assign('ur',$ur); 
		$this->assign('qrcode',qrcode('http://'.$_SERVER['HTTP_HOST'],'images/','city'.$city_id.'.png','L',3,0,'#CC0000'));
        /*$this->assign('site_title', Conf::get('site_title'));
        $this->assign('site_logo', Conf::get('site_logo'));*/
        $this->assign('statistics_code', Conf::get('statistics_code')); // 统计代码
        $current_url = explode('/', $_SERVER['REQUEST_URI']);
        $count = count($current_url);
        $this->assign('current_url',  $count > 1 ? $current_url[$count-1] : $_SERVER['REQUEST_URI']);// 用于设置导航状态(以后可能会有问题)
        parent::display($tpl);
		
    }
    function login()
    {
	
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	$this->member_mod =& m('member');
	$this->_city_mod =& m('city');
	
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$huiyuan=$cityrow['huiyuan'];

	$weiboid=ecm_getcookie('weiboid');
	$openid=ecm_getcookie('openid');
	if(!empty($openid))
	{
	$qq=$this->_city_mod->getRow("select user_id from ".DB_PREFIX."member where openid = '$openid' limit 1");
		if($qq)
		{
			ecm_setcookie('openid', "");
		}
	}
	if(!empty($weiboid))
	{
	$weibo=$this->_city_mod->getRow("select user_id from ".DB_PREFIX."member where weiboid = '$weiboid' limit 1");
		if($weiboid)
		{
			ecm_setcookie('weiboid', "");
		}
	}
	
        if ($this->visitor->has_login)
        {
            $this->show_warning('has_login');

            return;
        }
        if (!IS_POST)
        {
            if (!empty($_GET['ret_url']))
            {
                $ret_url = trim($_GET['ret_url']);
            }
            else
            {
                if (isset($_SERVER['HTTP_REFERER']))
                {
                    $ret_url = $_SERVER['HTTP_REFERER'];
                }
                else
                {
                    $ret_url = SITE_URL . '/index.php';
                }
            }

            if (Conf::get('captcha_status.login'))
            {
                $this->assign('captcha', 1);
            }
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js'));
            $this->assign('ret_url', rawurlencode($ret_url));
            $this->_curlocal(LANG::get('user_login'));
			$this->assign('logo', $logo);
            $this->assign('page_title', Lang::get('user_login') . ' - ' . Conf::get('site_title'));
            $this->display('login.html');
            /* 同步退出外部系统 */
            if (!empty($_GET['synlogout']))
            {
                $ms =& ms();
                echo $synlogout = $ms->user->synlogout();
            }
        }
        else
        {
            if (Conf::get('captcha_status.login') && base64_decode($_SESSION['captcha']) != strtolower($_POST['captcha']))
            {
                $this->show_warning('captcha_failed');

                return;
            }

            $user_name = trim($_POST['user_name']);
            $password  = $_POST['password'];			

            $ms =& ms();
            $user_id = $ms->user->auth($user_name, $password);	
            if (!$user_id)
            {
                /* 未通过验证，提示错误信息 */
                $this->show_warning($ms->user->get_error());
                return;
            }
			else
			{			
				$row_member=$this->member_mod->getRow("select user_id,city from ".DB_PREFIX."member where user_name = '$user_name' limit 1");
				$city=$row_member['city'];
				$user_id=$row_member['user_id'];
				
				if($city!=$city_id && $huiyuan=='no')
				{
					$this->show_warning('ninbushibenzhanhuiyuan');
					return;
				}
				else
				{
					/* 通过验证，执行登录操作 */
					$this->_do_login($user_id);
					/* 同步登录外部系统 */
					$synlogin = $ms->user->synlogin($user_id);
				}
			}
            $this->show_message(Lang::get('login_successed') . $synlogin,
                //'back_before_login', rawurldecode($_POST['ret_url']),
                'enter_member_center', 'index.php?app=member'
            );
        }
    }

    function pop_warning ($msg, $dialog_id = '',$url = '')
    {
        if($msg == 'ok')
        {
            if(empty($dialog_id))
            {
                $dialog_id = APP . '_' . ACT;
            }
            if (!empty($url))
            {
                echo "<script type='text/javascript'>window.parent.location.href='".$url."';</script>";
            }
            echo "<script type='text/javascript'>window.parent.js_success('" . $dialog_id ."');</script>";
            exit;
        }
        else
        {
            header("Content-Type:text/html;charset=".CHARSET);
            echo "<script type='text/javascript'>window.parent.js_fail('" . Lang::get($msg) . "');</script>";
            exit;
        }
    }

    function logout()
    {
        $this->visitor->logout();

        /* 跳转到登录页，执行同步退出操作 */
        header("Location: index.php?app=member&act=login&synlogout=1");
        return;
    }

    /* 执行登录动作 */
    function _do_login($user_id)
    {
	$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	$this->member_mod =& m('member');
	$this->_city_mod =& m('city');
	$this->qiandao_log_mod=& m('qiandao_log');
	/*if($city_id!=$city)
	{
	$this->show_warning('ninbushibenzhanhuiyuan');
	return;
	}
	else{*/
	//$weibo_id='2353329133';
	//$my=$this->_city_mod->getrow("select * from ".DB_PREFIX."member where weibo_id='$weibo_id'");
  $mod_user =& m('member');
$weiboid=ecm_getcookie('weiboid');
$openid=ecm_getcookie('openid');

$dd=array();
if(!empty($weiboid))
{
	$dd['weiboid']=$weiboid;	
}
if(!empty($openid))
{
	$dd['openid']=$openid;	
}
if(!empty($dd))
{  
	$mod_user->edit('user_id='.$user_id,$dd);
}
	
        $user_info = $mod_user->get(array(
            'conditions'    => "user_id = '{$user_id}'",
            'join'          => 'has_store',                 //关联查找看看是否有店铺
            'fields'        => 'user_id, user_name, reg_time, last_login, last_ip, store_id',
        ));
/*}*/
        /* 店铺ID */
        $my_store = empty($user_info['store_id']) ? 0 : $user_info['store_id'];

        /* 保证基础数据整洁 */
        unset($user_info['store_id']);

        /* 分派身份 */
        $this->visitor->assign($user_info);
		
		        /* 更新用户登录信息 */
        $mod_user->edit("user_id = '{$user_id}'", "last_login = '" . gmtime()  . "', last_ip = '" . real_ip() . "', logins = logins + 1");
/*by:xiaohei 商付通自动注册开通 开始******************************/
$db=&db();
$my_money_row=$db->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");
if(empty($my_money_row))
{
	$member_row=$db->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");
	//echo $member_row['user_id'];
	//商付通 添加自动开通
	$this->my_money_mod =& m('my_money');
	$money_data=array(
	'user_id'=>$member_row['user_id'],
	'user_name'=>$member_row['user_name'],
	'zf_pass'=>$member_row['password'],
	'city'=>$member_row['city'],//注册时将密码和city添加到my_money表
	'add_time'=>time(),
	);
	$this->my_money_mod->add($money_data);	
}
$mem_user_name=$member_row['user_name'];
$mem_city=$member_row['city'];
$qd=$db->getRow("select * from ".DB_PREFIX."qiandao where user_id='$user_id' limit 1");
$can=$db->getRow("select * from ".DB_PREFIX."canshu limit 1");
$reg_jifen=$can['reg_jifen'];
//$qiandao_jifen=$can['qiandao_jifen'];
//$z_jifen=$reg_jifen+$qiandao_jifen;

	$this->message_mod =& m('message');



if(empty($qd))
{

$content=Lang::get('zhucecheng');
	$content=str_replace('{1}',$member_row['user_name'],$content);		
	$add_notice1=array(
	'from_id'=>0,
	'to_id'=>$user_id,
	'content'=>$content,  
	'add_time'=>gmtime(),
	'last_update'=>gmtime(),
	'new'=>1,
	'parent_id'=>0,
	'status'=>3,
	);
	$this->message_mod->add($add_notice1);


$this->qiandao_mod=& m('qiandao');
$riqi=date('Y-m-d H:i:s');
$dao=array(
		'user_id'=>$user_id,
		'riqi'=>$riqi,
		'times'=>$reg_jifen
		    );
	$this->qiandao_mod->add($dao);

	
	$notice=Lang::get('zhuce');
	$notice=str_replace('{1}',$member_row['user_name'],$notice);		
	$add_notice=array(
	'from_id'=>0,
	'to_id'=>$user_id,
	'content'=>$notice,  
	'add_time'=>gmtime(),
	'last_update'=>gmtime(),
	'new'=>1,
	'parent_id'=>0,
	'status'=>3,
	);
	$this->message_mod->add($add_notice);
					
	
$beizhu=Lang::get('zhucerongyu').$reg_jifen.Lang::get('fen');
	$add_mylog=array(
	'user_id'=>$user_id,
	'user_name'=>$mem_user_name,
    'jifen'=>$reg_jifen,
	'beizhu'=>$beizhu,
	'riqi'=>$riqi,	
	'city'=>$mem_city																			
    );
    $this->qiandao_log_mod->add($add_mylog);

}
/*by:xiaohei 商付通自动注册开通 结束******************************/

        /* 更新购物车中的数据 */
        $mod_cart =& m('cart');
        $mod_cart->edit("(user_id = '{$user_id}' OR session_id = '" . SESS_ID . "') AND store_id <> '{$my_store}'", array(
            'user_id'    => $user_id,
            'session_id' => SESS_ID,
        ));

        /* 去掉重复的项 */
        $cart_items = $mod_cart->find(array(
            'conditions'    => "user_id='{$user_id}' GROUP BY spec_id",
            'fields'        => 'COUNT(spec_id) as spec_count, spec_id, rec_id',
        ));
        if (!empty($cart_items))
        {
            foreach ($cart_items as $rec_id => $cart_item)
            {
                if ($cart_item['spec_count'] > 1)
                {
                    $mod_cart->drop("user_id='{$user_id}' AND spec_id='{$cart_item['spec_id']}' AND rec_id <> {$cart_item['rec_id']}");
                }
            }
        }
    }

    /* 取得导航 */
    function _get_navs()
    {
        $cache_server =& cache_server();
        $key = 'common.navigation';
        $data = $cache_server->get($key);
        if($data === false)
        {
            $data = array(
                'header' => array(),
                'middle' => array(),
                'footer' => array(),
            );
            $nav_mod =& m('navigation');
            $rows = $nav_mod->find(array(
                'order' => 'type, sort_order',
            ));
            foreach ($rows as $row)
            {
                $data[$row['type']][] = $row;
            }
            $cache_server->set($key, $data, 86400);
        }

        return $data;
    }

    /**
     *    获取JS语言项
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function jslang()
    {
        $lang = Lang::fetch(lang_file('jslang'));
        parent::jslang($lang);
    }

    /**
     *    视图回调函数[显示小挂件]
     *
     *    @author    Garbin
     *    @param     array $options
     *    @return    void
     */
    function display_widgets($options)
    {
        $area = isset($options['area']) ? $options['area'] : '';
        $page = isset($options['page']) ? $options['page'] : '';
        if (!$area || !$page)
        {
            return;
        }
        include_once(ROOT_PATH . '/includes/widget.base.php');

        /* 获取该页面的挂件配置信息 */
        $widgets = get_widget_config($this->_get_template_name(), $page);

        /* 如果没有该区域 */
        if (!isset($widgets['config'][$area]))
        {
            return;
        }

        /* 将该区域内的挂件依次显示出来 */
        foreach ($widgets['config'][$area] as $widget_id)
        {
            $widget_info = $widgets['widgets'][$widget_id];
            $wn     =   $widget_info['name'];
            $options=   $widget_info['options'];

            $widget =& widget($widget_id, $wn, $options);
            $widget->display();
        }
    }

    /**
     *    获取当前使用的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        return 'default';
    }

    /**
     *    获取当前使用的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        return 'default';
    }

    /**
     *    当前位置
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _curlocal($arr)
    {
        $curlocal = array(array(
            'text'  => Lang::get('index'),
            'url'   => SITE_URL . '/index.php',
        ));
        if (is_array($arr))
        {
            $curlocal = array_merge($curlocal, $arr);
        }
        else
        {
            $args = func_get_args();
            if (!empty($args))
            {
                $len = count($args);
                for ($i = 0; $i < $len; $i += 2)
                {
                    $curlocal[] = array(
                        'text'  =>  $args[$i],
                        'url'   =>  $args[$i+1],
                    );
                }
            }
        }

        $this->assign('_curlocal', $curlocal);
    }
    function _init_visitor()
    {
        $this->visitor =& env('visitor', new UserVisitor());
    }
}
/**
 *    前台访问者
 *
 *    @author    Garbin
 *    @usage    none
 */
class UserVisitor extends BaseVisitor
{
    var $_info_key = 'user_info';


    /**
     *    退出登录
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function logout()
    {
        /* 将购物车中的相关项的session_id置为空 */
        $mod_cart =& m('cart');
        $mod_cart->edit("user_id = '" . $this->get('user_id') . "'", array(
            'session_id' => '',
        ));

        /* 退出登录 */
        parent::logout();
    }
}
/**
 *    商城控制器基类
 *
 *    @author    Garbin
 *    @usage    none
 */
class MallbaseApp extends FrontendApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && in_array(APP, array('apply')))
        {
            header('Location: index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

            return;
        }

        parent::_run_action();
    }

    function _config_view()
    {
        parent::_config_view();

        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();

        $this->_view->template_dir = ROOT_PATH . "/themes/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}";
    }

    /* 取得支付方式实例 */
    function _get_payment($code, $payment_info)
    {
        include_once(ROOT_PATH . '/includes/payment.base.php');
        include(ROOT_PATH . '/includes/payments/' . $code . '/' . $code . '.payment.php');
        $class_name = ucfirst($code) . 'Payment';

        return new $class_name($payment_info);
    }

    /**
     *   获取当前所使用的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        $template_name = Conf::get('template_name');
        if (!$template_name)
        {
            $template_name = 'default';
        }

        return $template_name;
    }

    /**
     *    获取当前模板中所使用的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        $style_name = Conf::get('style_name');
        if (!$style_name)
        {
            $style_name = 'default';
        }

        return $style_name;
    }
}

/**
 *    购物流程子系统基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class ShoppingbaseApp extends MallbaseApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && !in_array(ACT, array('login', 'register', 'check_user','check_email')))
        {
            if (!IS_AJAX)
            {
                header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

                return;
            }
            else
            {
                $this->json_error('login_please');
                return;
            }
        }

        parent::_run_action();
    }
}

/**
 *    用户中心子系统基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class MemberbaseApp extends MallbaseApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && !in_array(ACT, array('login', 'register', 'check_user','check_email')))
        {
            if (!IS_AJAX)
            {
                header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

                return;
            }
            else
            {
                $this->json_error('login_please');
                return;
            }
        }

        parent::_run_action();
    }
    /**
     *    当前选中的菜单项
     *
     *    @author    Garbin
     *    @param     string $item
     *    @return    void
     */
    function _curitem($item)
    {
        $this->assign('has_store', $this->visitor->get('has_store'));
        $this->assign('_member_menu', $this->_get_member_menu());
        $this->assign('_curitem', $item);
    }
    /**
     *    当前选中的子菜单
     *
     *    @author    Garbin
     *    @param     string $item
     *    @return    void
     */
    function _curmenu($item)
    {
        $_member_submenu = $this->_get_member_submenu();
        foreach ($_member_submenu as $key => $value)
        {
            $_member_submenu[$key]['text'] = $value['text'] ? $value['text'] : Lang::get($value['name']);
        }
        $this->assign('_member_submenu', $_member_submenu);
        $this->assign('_curmenu', $item);
    }
    /**
     *    获取子菜单列表
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _get_member_submenu()
    {
        return array();
    }
    /**
     *    获取用户中心全局菜单列表
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _get_member_menu()
    {
        
	   $this->site_system_mod=& m(site_system); 
	   $userid=$this->visitor->get('user_id');
	   $row=$this->site_system_mod->getRow("select status from ".DB_PREFIX."site_system where user_id=$userid limit 1 ");
		
		$menu = array();

        /* 我的ECMall */
        $menu['my_ecmall'] = array(
            'name'  => 'my_ecmall',
            'text'  => Lang::get('my_ecmall'),
            'submenu'   => array(
                'overview'  => array(
                    'text'  => Lang::get('overview'),
                    'url'   => 'index.php?app=member',
                    'name'  => 'overview',
                    'icon'  => 'ico1',
                ),
                'my_profile'  => array(
                    'text'  => Lang::get('my_profile'),
                    'url'   => 'index.php?app=member&act=profile',
                    'name'  => 'my_profile',
                    'icon'  => 'ico2',
                ),
                'message'  => array(
                    'text'  => Lang::get('message'),
                    'url'   => 'index.php?app=message&act=newpm',
                    'name'  => 'message',
                    'icon'  => 'ico3',
                ),
                'friend'  => array(
                    'text'  => Lang::get('friend'),
                    'url'   => 'index.php?app=friend',
                    'name'  => 'friend',
                    'icon'  => 'ico4',
                ),
				'invite'  => array(
                    'text'  => Lang::get('invite'),
                    'url'   => 'index.php?app=friend&act=invite',
                    'name'  => 'invite',
                    'icon'  => 'ico20',
                ),
				'complain_manage'  => array(
                    'text'  => Lang::get('complain_manage'),
                    'url'   => 'index.php?app=buyer_order&act=my_tousu',
                    'name'  => 'complain_manage',
                    'icon'  => 'ico21',
                ),
                /*
                'my_credit'  => array(
                    'text'  => Lang::get('my_credit'),
                    'url'   => 'index.php?app=member&act=credit',
                    'name'  => 'my_credit',
                ),*/
            ),
        );


        /* 商付通 导航开始 */
        $menu['shangfutong'] = array(
            'name'  => 'shangfutong',
            'text'  => Lang::get('shangfutong'),
            'submenu'   => array(
			 'chongzhichaxun'  => array(
                    'text'  => Lang::get('chongzhichaxun'),
                    'url'   => 'index.php?app=my_money&act=paylist',
                    'name'  => 'chongzhichaxun',
                    'icon'  => 'ico13',
                ),

                'jiaoyichaxun'  => array(
                    'text'  => Lang::get('jiaoyichaxun'),
                    'url'   => 'index.php?app=my_money&act=loglist',
                    'name'  => 'jiaoyichaxun',
                    'icon'  => 'ico5',
                ),

                'tixianshenqing'  => array(
                    'text'  => Lang::get('tixianshenqing'),
                    'url'   => 'index.php?app=my_money&act=txlist',
                    'name'  => 'tixianshenqing',
                    'icon'  => 'ico6',
                ),

                'zhanghushezhi'  => array(
                    'text'  => Lang::get('zhanghushezhi'),
                    'url'   => 'index.php?app=my_money&act=mylist',
                    'name'  => 'zhanghushezhi',
                    'icon'  => 'ico11',
                ),
               /* 'jifenduihuan'  => array(
                    'text'  => Lang::get('jifenduihuan'),
                    'url'   => 'index.php?app=my_money&act=jifen',
                    'name'  => 'jifenduihuan',
                    'icon'  => 'ico20',
                ),*/
				'xianjinjifen'  => array(
                    'text'  => Lang::get('xianjinjifen'),
                    'url'   => 'index.php?app=my_money&act=duihuanxianjinjifen',
                    'name'  => 'xianjinjifen',
                    'icon'  => 'ico7',
                ),
				/*'fbb'  => array(
                    'text'  => Lang::get('fbb'),
                    'url'   => 'index.php?app=my_money&act=goumaifbb',
                    'name'  => 'fbb',
                    'icon'  => 'ico6',
                ),
				'daxiaozhuo'  => array(
                    'text'  => Lang::get('daxiaozhuo'),
                    'url'   => 'index.php?app=my_money&act=goumaidaxiaozhuo',
                    'name'  => 'daxiaozhuo',
                    'icon'  => 'ico20',
                ),
				'taocan'  => array(
                    'text'  => Lang::get('taocan'),
                    'url'   => 'index.php?app=member&act=goumaitaocan',
                    'name'  => 'taocan',
                    'icon'  => 'ico6',
                ),*/
				'shourulog'  => array(
                    'text'  => Lang::get('shourulog'),
                    'url'   => 'index.php?app=member&act=chengzhangjifen',
                    'name'  => 'shourulog',
                    'icon'  => 'ico20',
                ),
            ),
        );
        /* 商付通 导航结束 */

        /* 我是买家 */
        $menu['im_buyer'] = array(
            'name'  => 'im_buyer',
            'text'  => Lang::get('im_buyer'),
            'submenu'   => array(
                'my_order'  => array(
                    'text'  => Lang::get('my_order'),
                    'url'   => 'index.php?app=buyer_order',
                    'name'  => 'my_order',
                    'icon'  => 'ico5',
                ),
                'my_groupbuy'  => array(
                    'text'  => Lang::get('my_groupbuy'),
                    'url'   => 'index.php?app=buyer_groupbuy',
                    'name'  => 'my_groupbuy',
                    'icon'  => 'ico21',
                ),
                'my_question' =>array(
                    'text'  => Lang::get('my_question'),
                    'url'   => 'index.php?app=my_question',
                    'name'  => 'my_question',
                    'icon'  => 'ico17',

                ),
                'my_favorite'  => array(
                    'text'  => Lang::get('my_favorite'),
                    'url'   => 'index.php?app=my_favorite',
                    'name'  => 'my_favorite',
                    'icon'  => 'ico6',
                ),
                'my_address'  => array(
                    'text'  => Lang::get('my_address'),
                    'url'   => 'index.php?app=my_address',
                    'name'  => 'my_address',
                    'icon'  => 'ico7',
                ),
                'my_coupon'  => array(
                    'text'  => Lang::get('my_coupon'),
                    'url'   => 'index.php?app=my_coupon',
                    'name'  => 'my_coupon',
                    'icon'  => 'ico20',
                ),
				
            ),
        );

        if (!$this->visitor->get('has_store') && Conf::get('store_allow'))
        {
            /* 没有拥有店铺，且开放申请，则显示申请开店链接 */
            /*$menu['im_seller'] = array(
                'name'  => 'im_seller',
                'text'  => Lang::get('im_seller'),
                'submenu'   => array(),
            );

            $menu['im_seller']['submenu']['overview'] = array(
                'text'  => Lang::get('apply_store'),
                'url'   => 'index.php?app=apply',
                'name'  => 'apply_store',
            );*/
            $menu['overview'] = array(
                'text' => Lang::get('apply_store'),
                'url'  => 'index.php?app=apply',
            );
        }
        if ($this->visitor->get('manage_store'))
        {
            /* 指定了要管理的店铺 */
            $menu['im_seller'] = array(
                'name'  => 'im_seller',
                'text'  => Lang::get('im_seller'),
                'submenu'   => array(),
            );

            $menu['im_seller']['submenu']['my_goods'] = array(
                    'text'  => Lang::get('my_goods'),
                    'url'   => 'index.php?app=my_goods',
                    'name'  => 'my_goods',
                    'icon'  => 'ico8',
            );
            $menu['im_seller']['submenu']['groupbuy_manage'] = array(
                    'text'  => Lang::get('groupbuy_manage'),
                    'url'   => 'index.php?app=seller_groupbuy',
                    'name'  => 'groupbuy_manage',
                    'icon'  => 'ico22',
            );
            $menu['im_seller']['submenu']['my_qa'] = array(
                    'text'  => Lang::get('my_qa'),
                    'url'   => 'index.php?app=my_qa',
                    'name'  => 'my_qa',
                    'icon'  => 'ico18',
            );
            $menu['im_seller']['submenu']['my_category'] = array(
                    'text'  => Lang::get('my_category'),
                    'url'   => 'index.php?app=my_category',
                    'name'  => 'my_category',
                    'icon'  => 'ico9',
            );
            $menu['im_seller']['submenu']['order_manage'] = array(
                    'text'  => Lang::get('order_manage'),
                    'url'   => 'index.php?app=seller_order',
                    'name'  => 'order_manage',
                    'icon'  => 'ico10',
            );
            $menu['im_seller']['submenu']['my_store']  = array(
                    'text'  => Lang::get('my_store'),
                    'url'   => 'index.php?app=my_store',
                    'name'  => 'my_store',
                    'icon'  => 'ico11',
            );
			$menu['im_seller']['submenu']['my_theme']  = array(
                    'text'  => Lang::get('my_theme'),
                    'url'   => 'index.php?app=my_theme',
                    'name'  => 'my_theme',
                    'icon'  => 'ico12',
            );
            /*$menu['im_seller']['submenu']['my_payment'] =  array(
                    'text'  => Lang::get('my_payment'),
                    'url'   => 'index.php?app=my_payment',
                    'name'  => 'my_payment',
                    'icon'  => 'ico13',
            );*/
            $menu['im_seller']['submenu']['my_shipping'] = array(
                    'text'  => Lang::get('my_shipping'),
                    'url'   => 'index.php?app=my_shipping',
                    'name'  => 'my_shipping',
                    'icon'  => 'ico14',
            );
            $menu['im_seller']['submenu']['my_navigation'] = array(
                    'text'  => Lang::get('my_navigation'),
                    'url'   => 'index.php?app=my_navigation',
                    'name'  => 'my_navigation',
                    'icon'  => 'ico15',
            );
            $menu['im_seller']['submenu']['my_partner']  = array(
                    'text'  => Lang::get('my_partner'),
                    'url'   => 'index.php?app=my_partner',
                    'name'  => 'my_partner',
                    'icon'  => 'ico16',
            );
            $menu['im_seller']['submenu']['coupon']  = array(
                    'text'  => Lang::get('coupon'),
                    'url'   => 'index.php?app=coupon',
                    'name'  => 'coupon',
                    'icon'  => 'ico19',
            );
			 $menu['im_seller']['submenu']['gonghuo']  = array(
                    'text'  => Lang::get('gonghuo'),
                    'url'   => 'index.php?app=my_theme&act=shangjiaxinxi',
                    'name'  => 'gonghuo',
                    'icon'  => 'ico20',
            );
			 $menu['im_seller']['submenu']['mycaigou']  = array(
                    'text'  => Lang::get('mycaigou'),
                    'url'   => 'index.php?app=my_theme&act=my_caigou_k',
                    'name'  => 'mycaigou',
                    'icon'  => 'ico6',
            );
			 $menu['im_seller']['submenu']['gonghuoorder']  = array(
                    'text'  => Lang::get('gonghuoorder'),
                    'url'   => 'index.php?app=seller_order&act=gonghuo_order',
                    'name'  => 'gonghuoorder',
                    'icon'  => 'ico5',
            );
			$menu['im_seller']['submenu']['chengnuo']  = array(
                    'text'  => Lang::get('chengnuo'),
                    'url'   => 'index.php?app=my_navigation&act=chengnuo',
                    'name'  => 'chengnuo',
                    'icon'  => 'ico13',
            );
			/* $menu['im_seller']['submenu']['xiaobao']  = array(
                    'text'  => Lang::get('xiaobao'),
                    'url'   => 'index.php?app=my_store&act=xiaobao',
                    'name'  => 'xiaobao',
                    'icon'  => 'ico21',
            );*/
			
			
			if($row['status']==1)
	{
		 $menu['im_qiye'] = array(
            'name'  => 'im_qiye',
            'text'  => Lang::get('im_qiye'),
            'submenu'   => array(
				'jieshao'  => array(
                    'text'  => iconv('utf-8','gbk','站点设置'),
                    'url'   => 'index.php?app=company',
                    'name'  => 'abountus',
                    'icon'  => 'ico7',
                ),
                'abountus'  => array(
                    'text'  => iconv('utf-8','gbk','关于我们'),
                    'url'   => 'index.php?app=company&act=fenlei&type=1',
                    'name'  => 'abountus',
                    'icon'  => 'ico5',
                ),
                'zizhi'  => array(
                    'text'  => iconv('utf-8','gbk','公司资质'),
                    'url'   => 'index.php?app=company&act=fenlei&type=2',
                    'name'  => 'zizhi',
                    'icon'  => 'ico21',
                ),
                'licheng' =>array(
                    'text'  => iconv('utf-8','gbk','公司历程'),
                    'url'   => 'index.php?app=company&act=fenlei&type=3',
                    'name'  => 'licheng',
                    'icon'  => 'ico17',

                ),
                'zhaopin'  => array(
                    'text'  => iconv('utf-8','gbk','招贤纳士'),
                    'url'   => 'index.php?app=company&act=fenlei&type=4',
                    'name'  => 'zhaopin',
                    'icon'  => 'ico6',
                ),
				'fengcai'  => array(
                    'text'  => iconv('utf-8','gbk','企业风采'),
                    'url'   => 'index.php?app=company&act=news&type=5',
                    'name'  => 'fengcai',
                    'icon'  => 'ico3',
                ),
				'news'  => array(
                    'text'  => iconv('utf-8','gbk','新闻中心'),
                    'url'   => 'index.php?app=company&act=news&type=6',
                    'name'  => 'news',
                    'icon'  => 'ico8',
                ),
				'contact'  => array(
                    'text'  => iconv('utf-8','gbk','联系我们'),
                    'url'   => 'index.php?app=company&act=fenlei&type=7',
                    'name'  => 'contact',
                    'icon'  => 'ico13',
                ),
				'notice'  => array(
                    'text'  => iconv('utf-8','gbk','最新公告'),
                     'url'   => 'index.php?app=company&act=fenlei&type=8',
                    'name'  => 'notice',
                    'icon'  => 'ico8',
                ),
				'rongyu'  => array(
                    'text'  => iconv('utf-8','gbk','证书荣誉'),
                    'url'   => 'index.php?app=company&act=fenlei&type=9',
                    'name'  => 'rongyu',
                    'icon'  => 'ico21',
                ),
				'advt'  => array(
                    'text'  => iconv('utf-8','gbk','广告管理'),
                    'url'   => 'index.php?app=company&act=adv',
                    'name'  => 'advt',
                    'icon'  => 'ico20',
                ),
				'link'  => array(
                    'text'  => iconv('utf-8','gbk','友情链接'),
                    'url'   => 'index.php?app=company&act=part',
                    'name'  => 'link',
                    'icon'  => 'ico8',
                ),
            ),
        );
	}
	else
	{
		$menu['im_qiye'] = array(
            'name'  => 'im_qiye',
            'text'  => Lang::get('im_qiye'),
            'submenu'   => array(
				'jieshao'  => array(
                    'text'  => iconv('utf-8','gbk','申请站点'),
                    'url'   => 'index.php?app=company',
                    'name'  => 'abountus',
                    'icon'  => 'ico7',
                ),
			),
		);		
	}
			
			
			
			
        }

	



        return $menu;
    }
}

/**
 *    店铺管理子系统基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class StoreadminbaseApp extends MemberbaseApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && !in_array(ACT, array('login', 'register', 'check_user','check_email')))
        {
            if (!IS_AJAX)
            {
                header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

                return;
            }
            else
            {
                $this->json_error('login_please');
                return;
            }
        }

        /* 检查是否是店铺管理员 */
        if (!$this->visitor->get('manage_store'))
        {
            /* 您不是店铺管理员 */
            $this->show_warning(
                'not_storeadmin',
                'apply_now', 'index.php?app=apply',
                'go_back'
            );

            return;
        }

        /* 检查是否被授权 */
        $privileges = $this->_get_privileges();

		if (!$this->visitor->i_can('do_action', $privileges))
        {

            $this->show_warning('no_permission');

            return;
        }

        /* 检查店铺开启状态 */
        $state = $this->visitor->get('state');
        if ($state == 0)
        {
            $this->show_warning('apply_not_agree');

            return;
        }
        elseif ($state == 2)
        {
            $this->show_warning('store_is_closed');

            return;
        }

        /* 检查附加功能 */
        if (!$this->_check_add_functions())
        {
            $this->show_warning('not_support_function','','goumaitaocan.html');
            return;
        }

        parent::_run_action();
    }
    function _get_privileges()
    {
        $store_id = $this->visitor->get('manage_store');
        $privs = $this->visitor->get('s');

        if (empty($privs))
        {
            return '';
        }

        foreach ($privs as $key => $admin_store)
        {
            if ($admin_store['store_id'] == $store_id)
            {
                return $admin_store['privs'];
		

		
            }
        }
    }

    function _check_add_functions()
    {
        $apps_functions = array( // app与function对应关系
            'seller_groupbuy' => 'groupbuy',
            'coupon' => 'coupon',
        );
        if (isset($apps_functions[APP]))
        {
            $store_mod =& m('store');
            $settings = $store_mod->get_settings($this->_store_id);
            $add_functions = isset($settings['functions']) ? $settings['functions'] : ''; // 附加功能
            if (!in_array($apps_functions[APP], explode(',', $add_functions)))
            {
                return false;
            }
        }
        return true;
    }
}

/**
 *    店铺控制器基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class StorebaseApp extends FrontendApp
{
    var $_store_id;

    /**
     * 设置店铺id
     *
     * @param int $store_id
     */
    function set_store($store_id)
    {
        $this->_store_id = intval($store_id);

        /* 有了store id后对视图进行二次配置 */
        $this->_init_view();
        $this->_config_view();
    }

    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();

        $this->_view->template_dir = ROOT_PATH . "/themes/store/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/store/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/themes/store/{$template_name}/styles/{$style_name}";
    }

    /**
     * 取得店铺信息
     */
    function get_store_data()
    {
	
	$this->member_mod=& m('member');
	$userid=$this->_store_id;
	$mfbb=$this->member_mod->getRow("select level from ".DB_PREFIX."member where user_id='$userid' limit 1");
    $eve=$mfbb['level'];
	$le=level($eve);

		$cityrow=$this->member_mod->get_cityrow();
		$city_id=$cityrow['city_id'];



        $cache_server =& cache_server();
        //$key = 'function_get_store_data_' . $this->_store_id;
        $store = $cache_server->get($key);
        if ($store === false)
        {
			
            $store = $this->_get_store_info();
            if (empty($store))
            {
                $this->show_warning('the_store_not_exist');
                exit;
            }
            if ($store['state'] == 2)
            {
                $this->show_warning('the_store_is_closed');
                exit;
            }
            $step = intval(Conf::get('upgrade_required'));
            $step < 1 && $step = 5;
            $store_mod =& m('store');
            $store['credit_image'] = $this->_view->res_base . '/images/' . $store_mod->compute_credit($store['credit_value'], $step);

            empty($store['store_logo']) && $store['store_logo'] = Conf::get('default_store_logo');
            $store['store_owner'] = $this->_get_store_owner();
            $store['store_navs']  = $this->_get_store_nav();
            $goods_mod =& m('goods');
            $store['goods_count'] = $goods_mod->get_count_of_store($this->_store_id);
            $store['store_gcates']= $this->_get_store_gcategory();
            $store['sgrade'] = $this->_get_store_grade();
			
			$st=$this->member_mod->getRow("select erweima from ".DB_PREFIX."store where store_id='$this->_store_id' limit 1");
			$this->assign('st',$st);
			if($st['erweima']==1)
			{
			$imgsrc=qrcode("http://".$_SERVER['HTTP_HOST']."/index.php?app=store&id=".$this->_store_id,"./data/files/store_$this->_store_id/",$city_id.'erweima.png');
			$store['qrcode']="<img src='$imgsrc' width='160'>";
			}
			
            $cache_server->set($key, $store, 1800);
        }
        $store['le'] = level($eve);
	
        return $store;
    }

    /* 取得店铺信息 */
    function _get_store_info()
    {
        if (!$this->_store_id)
        {
            /* 未设置前返回空 */
            return array();
        }
        static $store_info = null;
        if ($store_info === null)
        {
            $store_mod  =& m('store');
            $store_info = $store_mod->get_info($this->_store_id);
        }

        return $store_info;
    }

    /* 取得店主信息 */
    function _get_store_owner()
    {
        $user_mod =& m('member');
        $user = $user_mod->get($this->_store_id);

        return $user;
    }

    /* 取得店铺导航 */
    function _get_store_nav()
    {
        $article_mod =& m('article');
        return $article_mod->find(array(
            'conditions' => "store_id = '{$this->_store_id}' AND cate_id = '" . STORE_NAV . "' AND if_show = 1",
            'order' => 'sort_order',
            'fields' => 'title,article.link',
        ));
		
    }
    /*  取的店铺等级   */

    function _get_store_grade()
    {
        $store_info = $store_info = $this->_get_store_info();
        $sgrade_mod =& m('sgrade');
        $result = $sgrade_mod->get_info($store_info['sgrade']);
        return $result['grade_name'];
    }
    /* 取得店铺分类 */
    function _get_store_gcategory()
    {
        $gcategory_mod =& bm('gcategory', array('_store_id' => $this->_store_id));
        $gcategories = $gcategory_mod->get_list(-1, true);
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree->getArrayList(0);
    }

    /**
     *    获取当前店铺所设定的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        $store_info = $this->_get_store_info();
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($template_name, $style_name) = explode('|', $theme);

        return $template_name;
    }

    /**
     *    获取当前店铺所设定的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        $store_info = $this->_get_store_info();
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($template_name, $style_name) = explode('|', $theme);

        return $style_name;
    }
}

/* 实现消息基础类接口 */
class MessageBase extends MallbaseApp {};

/* 实现模块基础类接口 */
class BaseModule  extends FrontendApp {};

/* 消息处理器 */
require(ROOT_PATH . '/eccore/controller/message.base.php');


?>
