<?php

/**
 *    默认控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class DefaultApp extends BackendApp
{
    /**
     *    后台首页
     *
     *    @author    Garbin
     *    @return    void
     */
    function index()
    {
        $back_nav = $menu = $this->_get_menu();
        /*unset($back_nav['dashboard']);
		foreach($menu as $kk=>$v)
		{
			if(empty($menu[$kk]['children']))
			{
			 	unset($menu[$kk]);
			}
		}*/
        $this->_hook('on_load_adminmenu', array('menu' => &$menu));
        $this->assign('menu', $menu);
        $this->assign('back_nav', $back_nav);
        $this->assign('menu_json', ecm_json_encode($menu));
		//print_r(ecm_json_encode($menu));
        $this->display('index.html');
    }

    /**
     *    后台欢迎页
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function welcome()
    {
        $this->assign('admin', $this->visitor->get());

        $ms =& ms();
        $this->assign('new', $ms->pm->check_new($this->visitor->get('user_id')));

        // 一周动态
        $this->assign('news_in_a_week', $this->_get_news_in_a_week());

        // 统计信息
        $stats = $this->_get_stats();
        $this->assign('stats', $stats);

        // 系统信息
        $sys_info = $this->_get_sys_info();
        $this->assign('sys_info', $sys_info);

        // 提示信息
        $remind_info = $this->_get_remind_info();
        $this->assign('remind_info', $remind_info);
        $dangerous_apps  = false;
        if (is_file(ROOT_PATH . '/initdata/index.php'))
        {
            $dangerous_apps[] = Lang::get('dangerous_initdata');
        }
        if (is_file(ROOT_PATH . '/integrate/index.php'))
        {
            $dangerous_apps[] = Lang::get('dangerous_integrate');
        }

        $this->assign('dangerous_apps', $dangerous_apps);

        // 当前语言
        $this->assign('cur_lang', LANG);

		$userid=$this->visitor->get('user_id');
		$this->cart_mod=& m('cart');
		$priv_row=$this->cart_mod->getrow("select privs from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
		$this->assign('priv_row',$priv_row);
		
        $this->_update_store_state();
        $this->_update_site_information($stats, $sys_info);
        $this->display('welcome.html');
    }

    /**
     *    关于我们页面
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function aboutus()
    {
        $this->headtag('<base target="_blank" />');
        $this->display('aboutus.html');
    }

    function _get_menu()
    {
		$this->_admin_mod=& m('member');
        $menu = include(APP_ROOT . '/includes/menu.inc.php');
		$row=$this->_admin_mod->getRow('select * from '.DB_PREFIX.'user_priv where user_id = '.$this->visitor->get('user_id'));
		
		$priv=$row['privs'];
		$priv=str_replace('|all','',$priv);
		$pr=explode(',',$priv);
		$me=array();
		
		if($priv=="all")
		{
			return $menu;
		}

		foreach($menu as $kk=>$vale)
		{
			foreach($vale['children'] as $k=>$v)
			{				
				$vv=str_replace('index.php?','',$v['url']);
				$vv1=explode('=',$vv);
				$vvv=explode('&',$vv1[1]);
				
				if(!in_array($vvv[0],$pr))
				{			
					if($vvv[0]!='aboutus' && $vvv[0]!='welcome')
						unset($menu[$kk]['children'][$k]);
				}
			}
			//print_r($menu[$kk]);
			/*if(empty($menu[$kk]['children']))
			{
				unset($menu[$kk]);
			}*/
		}
		return $menu;			   
    }

    function _get_news_in_a_week()
    {
        $a_week_ago = gmtime() - 7 * 24 * 3600;
        $user_mod =& m('member');
        return array(
            'new_user_qty'  => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "member WHERE reg_time > '$a_week_ago'"),
            'new_store_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE add_time > '$a_week_ago' AND state = 1"),
            'new_apply_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE add_time > '$a_week_ago' AND state = 0"),
            'new_goods_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "goods WHERE add_time > '$a_week_ago' AND if_show = 1 AND closed = 0"),
            'new_order_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "order WHERE finished_time > '$a_week_ago' AND status = '" . ORDER_FINISHED . "'"),
        );
    }

    function _get_stats()
    {
        $user_mod =& m('member');
        return array(
            'user_qty'  => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "member"),
            'store_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE state = 1"),
            'apply_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "store WHERE state = 0"),
            'goods_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "goods WHERE if_show = 1 AND closed = 0"),
            'order_qty' => $user_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "order WHERE status = '" . ORDER_FINISHED . "'"),
            'order_amount' => $user_mod->getOne("SELECT SUM(order_amount) FROM " . DB_PREFIX . "order WHERE status = '" . ORDER_FINISHED . "'"),
        );
    }

    function _get_sys_info()
    {
        $user_mod =& m('member');
        $filename = ROOT_PATH . '/data/install.lock';
        return array(
            'server_os'     => PHP_OS,
            'web_server'    => $_SERVER['SERVER_SOFTWARE'],
            'php_version'   => PHP_VERSION,
            'mysql_version' => $user_mod->db->version(),
            'ecmall_version'=> VERSION . ' ' . RELEASE,
            'install_date'  => file_exists($filename) ? date('Y-m-d', fileatime($filename)) : date('Y-m-d'),
        );
    }

    function _update_site_information($stats, $sys_info)
    {
        $update = array(
            'uniqueid'  => MALL_SITE_ID,
            'version'   => VERSION,
            'release'   => RELEASE,
            'php'       => PHP_VERSION,
            'mysql'     => $sys_info['mysql_version'],
            'charset'   => CHARSET,
            'url'       => SITE_URL,
        );

        $update_time = 0;
        $update_file = ROOT_PATH . '/data/update_time.lock';
        if (file_exists($update_file))
        {
            $update_time = filemtime($update_file);
        }

        $timestamp = time();
        if(empty($update_time) || ($timestamp - $update_time > 3600 * 4))
        {
            touch($update_file);
            $stat_info = array();
            $stat_info['page_view']    = 1; // todo: no data
            $stat_info['order_amount'] = $stats['order_amount'];
            $stat_info['order_count']  = $stats['order_qty'];
            $stat_info['store_count']  = $stats['store_qty'];
            $stat_info['member_count'] = $stats['user_qty']; // differ from 1.1
            $stat_info['goods_count']  = $stats['goods_qty']; // differ from 1.1
            $stat_info['admin_last_login_time'] = date('Y-m-d H:i:s');
            foreach($stat_info AS $key => $value)
            {
                $update[$key] = $value;
            }
        }

        $data = '';
        foreach($update as $key => $value)
        {
            $data .= $key.'='.rawurlencode($value).'&';
        }

        $this->assign('spt', 'ht'. 'tp:/' . '/e' .'cmal' . 'l.sho' . 'pe' . 'x.c' . 'n/sy' . 'stem'. '/ecm' . 'all' . '_in' . 'stal' . 'l.p' . 'hp?'.'update='.rawurlencode(base64_encode($data)).'&md5hash='.substr(md5($_SERVER['HTTP_USER_AGENT'].implode('', $update).$timestamp), 8, 8).'&timestamp='.$timestamp);
    }

    function clear_cache()
    {
        $cache_server =& cache_server();
        $cache_server->clear();
        $this->json_result('', Lang::get('clear_cache_ok'));
    }

    /* 更新店铺状态：过期的关闭 */
    function _update_store_state()
    {
        $store_mod =& m('store');
        $stores = $store_mod->find(array(
            'conditions' => "state = '" . STORE_OPEN . "' AND end_time > 0 AND end_time < '" . gmtime() . "'",
            'join'       => 'belongs_to_user',
            'fields'     => 'store_id, user_id, user_name, email',
        ));
        foreach ($stores as $store)
        {
            $subject = Lang::get('close_store_notice');
            $content = get_msg('toseller_store_closed_notify', array('reason' => Lang::get('close_reason')));
            /* 连接用户系统 */
            $ms =& ms();
            $ms->pm->send(MSG_SYSTEM, $store['user_id'], '', $content);

            $this->_mailto($store['email'], $subject, $content);
            $store_mod->edit($store['store_id'], array('state' => STORE_CLOSED, 'close_reason' => Lang::get('close_reason')));
        }
    }

    /* 取得提醒信息 */
    function _get_remind_info()
    {
        $remind_info = array();
        $mod =& m('store');

        // 地区
        $region_count = $mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "region WHERE parent_id = 0");
        $region_count == 0 && $remind_info[] = Lang::get('reminds.region');

        // 支付方式
        $filename = ROOT_PATH . '/data/payments.inc.php';
        $payments = array();
        if (file_exists($filename))
        {
            $payments = include_once $filename;
        }
        empty($payments) && $remind_info[] = Lang::get('reminds.payment');

        // 商品分类
        $gcate_count = $mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "gcategory WHERE store_id = 0 AND parent_id = 0 AND if_show = 1");
        $gcate_count == 0 && $remind_info[] = Lang::get('reminds.gcategory');

        // 店铺分类
        $scate_count = $mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "scategory WHERE parent_id = 0");
        $scate_count == 0 && $remind_info[] = Lang::get('reminds.scategory');

        return $remind_info;
    }
	
	
	function tongji()
    {
        $user_mod =& m('member');
		$user_id=$this->visitor->get('user_id');
		
		 // 一周动态
        $this->assign('news_in_a_week', $this->_get_news_in_a_week());

        // 统计信息
        $stats = $this->_get_stats();
        $this->assign('stats', $stats);

		
	 $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	$priv_row=$user_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$user_id' and store_id=0");
	 $cate_mod =& bm('gcategory', array('_store_id' => 0));
     $this->assign('gcategories', $cate_mod->get_options(0, true));
		$privs=$priv_row['privs'];
		$city=$priv_row['city'];
	 	$this->assign('priv_row', $priv_row);
		$leixing=$_GET['leixing'];
		$add_time_from=$_GET['add_time_from'];
		
		$add_time_to=$_GET['add_time_to'];
		$leixing=$_GET['leixing'];
		$this->assign('leixing',$leixing);
		$condtions=" and 1=1 ";
		
		$add=strtotime($add_time_from)-28800;
		if($privs=="all")
		{
			if($leixing==1 or $leixing==4 or $leixing==5 or $leixing==6 or $leixing==7)
			$condition= " group by city";
			if($leixing==2)
			$condition= " group by cityhao";
			if($leixing==3)
			$condition= " group by cityid";
		}
		else
		{
			if($leixing==1 or $leixing==4 or $leixing==5 or $leixing==6 or $leixing==7)
			$condition= " city= ".$city;
			if($leixing==2)
			$condition= " cityhao= ".$city;
			if($leixing==3)
			$condition= " cityid= ".$city;
		}
		if($add_time_from!='')
		{
			$start_time=strtotime($add_time_from)-28800;
			if($leixing==1)
			$conditions.= " and reg_time>='$start_time' ";
			if($leixing==2 or $leixing==3 or $leixing==4)
			$conditions.= " and add_time>='$start_time' ";
			if($leixing==5 or $leixing==6)
			$conditions.= " and riqi>='$add_time_from' ";
			if($leixing==7)
			$conditions.= " and add_time>='$add_time_from' ";
		}
		if($add_time_to!='')
		{	
			$end_time=strtotime($add_time_to)+86400-28800;
			if($leixing==1)
			$conditions.= " and reg_time<='$end_time' ";
			if($leixing==2 or $leixing==3 or $leixing==4)
			$conditions.= " and add_time<='$end_time' ";
			if($leixing==5 or $leixing==6)
			$conditions.= " and riqi<='$add_time_to 24:59:59' ";
			if($leixing==7)
			$conditions.= " and add_time<='$add_time_to 24:59:59' ";
		}
		
		$city_row=array();
		$result=$user_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		  $row=explode('-',$var['city_name']);
		  $city_row[$var['city_id']]=$row[0];
		}
		$this->assign('result', $result);

		$result=null;
		if($leixing==1)//会员
		{
			if($privs!='all')
			{
				$conditions.=" and city='$city'";
			}
			$sex=$_GET['sex'];
			if($sex!='')
			{
				$conditions.=" and gender='$sex'";
			}
			
			$kaidian=$_GET['kaidian'];
			
			if($kaidian!='')
			{
				$userqty=$user_mod->getAll("SELECT COUNT(st.store_id) cou,st.cityid,mem.city,mem.level FROM " . DB_PREFIX . "member mem left join ". DB_PREFIX ."store st on mem.user_id=st.store_id where 1=1 ".$conditions . $condition);
			}
			else
			{	
				$userqty=$user_mod->getAll("SELECT COUNT(user_id) cou,city FROM " . DB_PREFIX . "member where 1=1 ".$conditions . $condition);
			}
			$aa=0;
			foreach ($userqty as $key => $var)
			{
				$userqty[$key]['city_name'] = $city_row[$var['city']];
				$aa=$aa+$var['cou'];
			}
			$this->assign('aa',$aa);
			$this->assign('userqty',$userqty);
		}
		if($leixing==2)//商品
		{
			$brand=$_GET['brand'];
			if($brand!='')
			{
				$conditions.=" and brand like '%$brand%'";
			}
			 // 分类
        $cate_id = empty($_GET['cate_id']) ? 0 : intval($_GET['cate_id']);
        if ($cate_id > 0)
        {
            $cate_mod =& bm('gcategory');
            $cate_ids = $cate_mod->get_descendant_ids($cate_id);
            $conditions .= " AND cate_id" . db_create_in($cate_ids);
        }
		
		$erweima=$_GET['erweima'];
		if($erweima!='')
		{
			$conditions.= " and erweima='$erweima'";
		}
		$xianshi=$_GET['xianshi'];
			if($xianshi!='')
			{
				$conditions.=" and if_show='$xianshi'";
			}
			if($privs!='all')
			{
				$conditions.= " and cityhao='$city'";
			}
			$goodsqty=$user_mod->getAll("SELECT COUNT(goods_id) cou,cityhao FROM " . DB_PREFIX . "goods where 1=1 ".$conditions . $condition);
			$aa=0;
			foreach ($goodsqty as $key => $var)
			{
				$goodsqty[$key]['city_name'] = $city_row[$var['cityhao']];
				$aa=$aa+$var['cou'];
			}
			$this->assign('aa',$aa);
			$this->assign('goodsqty',$goodsqty);
		}
		if($leixing==3)//店铺
		{
			if($privs!='all')
			{
				$conditions.=" and cityid='$city'";
			}
			$sgrade=$_GET['sgrade'];
			if($sgrade!='')
			{
				$conditions.=" and sgrade='$sgrade'";
			}
			$state=$_GET['state'];
			if($state!='')
			{
				$conditions.=" and state='$state'";
			}
			$dengji=$_GET['dengji'];
			if($dengji!='')
			{
				$conditions.=" and dengji='$dengji'";
			}
			$yuming=$_GET['yuming'];
			if($yuming!='')
			{	
				if($yuming==1)
				$conditions.=" and domain!=''";
				if($yuming==0)
				$conditions.=" and (domain='' or domain is NULL) ";
			}
			$leve=$_GET['leve'];
			if($leve==1)
			{
				$conditions.=" and mem.level!=''";
			}
			if($leve==2)
			{
				$conditions.=" and (mem.level is NULL)";
			}
			
			if($leve!='')
			{
				$storeqty=$user_mod->getAll("SELECT COUNT(st.store_id) cou,st.cityid,mem.level FROM " . DB_PREFIX . "member mem left join ". DB_PREFIX ."store st on mem.user_id=st.store_id where 1=1 ".$conditions . $condition);	
			}
			else
			{
				$storeqty=$user_mod->getAll("SELECT COUNT(store_id) cou,cityid FROM " . DB_PREFIX . "store where 1=1 ".$conditions . $condition);
			}
			$aa=0;
			foreach ($storeqty as $key => $var)
			{
				$storeqty[$key]['city_name'] = $city_row[$var['cityid']];
				$aa=$aa+$var['cou'];
			}
			$this->assign('aa',$aa);
			$this->assign('storeqty',$storeqty);
		}
		
		if($leixing==4)//交易
		{
			if($privs!='all')
			{
				$conditions.=" and city='$city'";
			}
			$status=$_GET['stat'];
			if($status!= "")
			{
				$conditions.=" and status='$status'";
			}
			$orderqty=$user_mod->getAll("SELECT COUNT(order_id) cou,SUM(order_jifen) jf,SUM(order_amount) am,MAX(order_jifen) maxjf,MAX(order_amount) maxam,city FROM " . DB_PREFIX . "order where 1=1 ".$conditions . $condition);
			
			$aa=0;
			$bb=0;
			$cc=0;
			foreach ($orderqty as $key => $var)
			{
				$orderqty[$key]['city_name'] = $city_row[$var['city']];
				$aa=$aa+$var['cou'];
				$bb=$bb+$var['jf'];
				$cc=$cc+$var['am'];
			}
			$this->assign('aa',$aa);
			$this->assign('bb',$bb);
			$this->assign('cc',$cc);
			$this->assign('orderqty',$orderqty);
		}
		
		if($leixing==5)//充值
		{
			if($privs!='all')
			{
				$conditions.=" and city='$city'";
			}
			$status=$_GET['status'];
			if($status!="")
			{
				$conditions.=" and status='$status'";
			}
			
			$chongzhiqty=$user_mod->getAll("SELECT COUNT(id) cou, SUM(money) z_money,city,MAX(money) max,MIN(money) min FROM " . DB_PREFIX . "my_moneylog where leixing=30 ".$conditions ." group by city");
			$aa=0;
			$bb=0;
			foreach ($chongzhiqty as $key => $var)
			{
				$chongzhiqty[$key]['city_name'] = $city_row[$var['city']];
				$aa=$aa+$var['cou'];
				$bb=$bb+$var['z_money'];
			}
			$this->assign('aa',$aa);
			$this->assign('bb',$bb);
			$this->assign('chongzhiqty',$chongzhiqty);
			
		}
		if($leixing==6)//提现
		{
			if($privs!='all')
			{
			$conditions.=" and city='$city'";
			}
			$status=$_GET['status'];
			if($status!="")
			{
				if($status==4)
				$conditions.=" and status=1 and status1=2";
				else
				$conditions.=" and status1='$status'";
			}
			$tixianqty=$user_mod->getAll("SELECT COUNT(id) cou, SUM(money_dj) z_money,city FROM " . DB_PREFIX . "my_moneylog where leixing=40 ".$conditions . $condition);
			
			$aa=0;
			$bb=0;
			foreach ($tixianqty as $key => $var)
			{
				$tixianqty[$key]['city_name'] = $city_row[$var['city']];
				$aa=$aa+$var['cou'];
				$bb=$bb+$var['z_money'];
			}
			$this->assign('aa',$aa);
			$this->assign('bb',$bb);
			$this->assign('tixianqty',$tixianqty);
			
		}
		if($leixing==7)
		{
			$artuser=$user_mod->getAll("SELECT COUNT(user_id) cou,city FROM " . DB_PREFIX . "article_user where 1=1 ".$conditions . $condition);
			foreach ($artuser as $key => $var)
			{
				$artuser[$key]['city_name'] = $city_row[$var['city']];
			}
			$this->assign('artuser',$artuser);
		}
		
		 $this->display('tongji.html');
    }
	function caiwubaobiao()
	{
		$user_mod =& m('member');
		 $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
		$arr_type=array(	
	'0'=>iconv('utf-8','gbk','选择类型'),
	'1'=>iconv('utf-8','gbk','线下充值金额'),
	'2'=>iconv('utf-8','gbk','线下充值费用'),
	'3'=>iconv('utf-8','gbk','兑换积分'),
	'4'=>iconv('utf-8','gbk','兑换现金'),
	'5'=>iconv('utf-8','gbk','提现申请金额'),
	'6'=>iconv('utf-8','gbk','提现金额'),
	'7'=>iconv('utf-8','gbk','提现费用'),
	'8'=>iconv('utf-8','gbk','购买优惠券'),
	'9'=>iconv('utf-8','gbk','采购申请'),
	'10'=>iconv('utf-8','gbk','采购商品'),
	'11'=>iconv('utf-8','gbk','采购不通过'),
	'12'=>iconv('utf-8','gbk','团购保证金'),
	'13'=>iconv('utf-8','gbk','团购费用'),
	'14'=>iconv('utf-8','gbk','解冻团购保证金'),
	'15'=>iconv('utf-8','gbk','购买商品'),
	'16'=>iconv('utf-8','gbk','出售商品'),
	'17'=>iconv('utf-8','gbk','出售供货商品'),
	'18'=>iconv('utf-8','gbk','取消订单'),
	'24'=>iconv('utf-8','gbk','后台增减用户资金'),
	'25'=>iconv('utf-8','gbk','增加币库资金'),
	'26'=>iconv('utf-8','gbk','充值成功，减少币库'),
	'27'=>iconv('utf-8','gbk','增加用户资金，减少币库'),
	'28'=>iconv('utf-8','gbk','减少用户资金，增加库币'),	
	'29'=>iconv('utf-8','gbk','购买套餐申请'),
	'31'=>iconv('utf-8','gbk','锁定用户资金'),
	'32'=>iconv('utf-8','gbk','锁定用户积分'),
	'36'=>iconv('utf-8','gbk','购买套餐'),
	'37'=>iconv('utf-8','gbk','套餐升级'),
	'38'=>iconv('utf-8','gbk','推荐人获得的奖励'),
	'39'=>iconv('utf-8','gbk','分站获得推荐奖励'),
	'40'=>iconv('utf-8','gbk','区域代理获得的推荐奖励'),
	'41'=>iconv('utf-8','gbk','借款'),
	'42'=>iconv('utf-8','gbk','还款'),
	'100'=>iconv('utf-8','gbk','在线充值'),
	'101'=>iconv('utf-8','gbk','在线充值奖励'),
	'102'=>iconv('utf-8','gbk','在线充值奖励平台'),
	'103'=>iconv('utf-8','gbk','在线充值奖励平台推荐人'),
	'104'=>iconv('utf-8','gbk','成长积分'),
	'105'=>iconv('utf-8','gbk','日封顶收益'),
	'110'=>iconv('utf-8','gbk','从借贷转入'),
	'111'=>iconv('utf-8','gbk','商城转入借贷'),
	'112'=>iconv('utf-8','gbk','借贷充值奖励利息转向商城'),
	//'106'=>'核定点',
);
		
		
		$arr_month=array(
		'1'=>iconv('utf-8','gbk','1月'),
		'2'=>iconv('utf-8','gbk','2月'),
		'3'=>iconv('utf-8','gbk','3月'),
		'4'=>iconv('utf-8','gbk','4月'),
		'5'=>iconv('utf-8','gbk','5月'),
		'6'=>iconv('utf-8','gbk','6月'),
		'7'=>iconv('utf-8','gbk','7月'),
		'8'=>iconv('utf-8','gbk','8月'),
		'9'=>iconv('utf-8','gbk','9月'),
		'10'=>iconv('utf-8','gbk','10月'),
		'11'=>iconv('utf-8','gbk','11月'),
		'12'=>iconv('utf-8','gbk','12月'),
		);
		
		$arr_year=array(
		'2012'=>iconv('utf-8','gbk','2012年'),
		'2013'=>iconv('utf-8','gbk','2013年'),
		'2014'=>iconv('utf-8','gbk','2014年'),
		'2015'=>iconv('utf-8','gbk','2015年'),
		'2016'=>iconv('utf-8','gbk','2016年'),
		'2017'=>iconv('utf-8','gbk','2017年'),
		'2018'=>iconv('utf-8','gbk','2018年'),
		'2019'=>iconv('utf-8','gbk','2019年'),
		'2020'=>iconv('utf-8','gbk','2020年'),
		
		);

		$conditions=" and 1=1";
		$cond=" and 1=1";
		$type=$_GET['leixing'];
		$time=$_GET['time'];
		$this->assign('type',$type);
		if(!empty($type))
		{
			$conditions.=" and type='$type'";
		}
		
	if($time==3)//按日
	{
		$add_time_from=$_GET['add_time_from'];
		$add_time_to=$_GET['add_time_to'];
		if($add_time_from!='' or $add_time_to!='')
		{
			$startdate=strtotime($add_time_from);
			$enddate=strtotime($add_time_to);
			$days=round(($enddate-$startdate)/3600/24) ;
			$this->assign('days',$days);
			$day=$days+1;
		}
		if(!empty($day))
			{
				$i=0;
				$aa=0;
				$bb=0;
				$cc=0;
				$dd=0;
				$ee=0;
				$row=array();
				for($i;$i<$days+1;$i++)

				{	
					$starttime=strtotime($add_time_from)+$i*24*3600;
					$start_time=date('Y-m-d',$starttime);
					$endtime=$starttime+86400;
					$end_time=date('Y-m-d',$endtime);
					$cond.= " and time>='$start_time' ";
					$cond.= " and time<'$end_time' ";
					$qty=$user_mod->getRow("SELECT COUNT(id) cou,SUM(money) money,SUM(jifen) jifen,SUM(money_dj) mdj,SUM(jifen_dj) jdj FROM " . DB_PREFIX . "moneylog where 1=1 ".$conditions . $cond);
					
					$row[$i]['riqi']=$start_time;
					$row[$i]['cou']=$qty['cou'];
					$row[$i]['z_money']=abs($qty['money']);
					$row[$i]['z_jifen']=abs($qty['jifen']);
					$row[$i]['z_moneydj']=abs($qty['mdj']);
					$row[$i]['z_jifendj']=abs($qty['jdj']);
					$aa=$aa+$qty['cou'];
					$bb=$bb+abs($qty['money']);
					$cc=$cc+abs($qty['jifen']);
					$dd=$dd+abs($qty['mdj']);
					$ee=$ee+abs($qty['jdj']);
					$this->assign('row',$row);
					$cond =' and 1=1';
				}
			}
		
	}
	
	if($time==1)//按年
	{
		$start_year=$_GET['year_from'];
		
		$end_year=$_GET['year_to'];
		
		$years=$end_year-$start_year;
		
		$i=0;
		$aa=0;
		$bb=0;
		$cc=0;
		$dd=0;
		$ee=0;
		$row=array();
		for($i;$i<$years+1;$i++)
		{
			$kaishi_year=$start_year+$i;
			$styear=($start_year+$i).'-'. 1 .'-' . 1; 
			$endyear=($start_year+$i+1).'-'. 1 .'-'. 1;
			$cond.= " and time>='$styear' ";
			$cond.= " and time<'$endyear' ";
			
			$qty=$user_mod->getRow("SELECT COUNT(id) cou,SUM(money) money,SUM(jifen) jifen,SUM(money_dj) mdj,SUM(jifen_dj) jdj FROM " . DB_PREFIX . "moneylog where 1=1 ".$conditions . $cond);
				
					$row[$i]['riqi']=$kaishi_year.iconv('utf-8','gbk','年');
					$row[$i]['cou']=$qty['cou'];
					$row[$i]['z_money']=abs($qty['money']);
					$row[$i]['z_jifen']=abs($qty['jifen']);
					$row[$i]['z_moneydj']=abs($qty['mdj']);
					$row[$i]['z_jifendj']=abs($qty['jdj']);
					$aa=$aa+$qty['cou'];
					$bb=$bb+abs($qty['money']);
					$cc=$cc+abs($qty['jifen']);
					$dd=$dd+abs($qty['mdj']);
					$ee=$ee+abs($qty['jdj']);
					$this->assign('row',$row);
					$cond =' and 1=1';
					
		}
	}
	
	if($time==2)//按月
	{
		$start_year=$_GET['ym_from'];
		$start_month=$_GET['month_from'];
		$end_month=$_GET['month_to'];
		$end_year=$_GET['ym_to'];
		$years=$end_year-$start_year;
		if($years==0)
		{
			$months=$end_month-$start_month;
		}
		else
		{
			$months=$years*12-$start_month+$end_month;
		}
		$j=0;
		$i=0;
		$aa=0;
		$bb=0;
		$cc=0;
		$dd=0;
		$ee=0;
		$row=array();
		for($i;$i<$months+1;$i++)
		{
				
				$kaishi_month=$start_month+$i;
			
				if($kaishi_month>12)
				{
				$startmonth=$start_year.'-'.($start_month+$i-12*$j).'-'. 1 ;
				$mon= $start_month+$i-12*$j;
				$st=$start_year;	
				}
				else
				{
				$startmonth=$start_year.'-'.($start_month+$i).'-'. 1 ; 
				$mon= $start_month+$i;
				$st=$start_year;	
				}
				
				if($start_month+$i+1>12)
				{
					if(12*($j+1)%($start_month+$i)==0)
					{
					$j++;
					$start_year=$start_year+1;
					}
				$endmonth=$start_year.'-'. ($start_month+$i-12*$j+1) .'-'. 1 ; 
				}	
				else
				$endmonth=$start_year.'-'.($start_month+$i+1).'-'. 1 ; 	
			
			$cond.= " and time>='$startmonth' ";
			$cond.= " and time<'$endmonth' ";
			
			$qty=$user_mod->getRow("SELECT COUNT(id) cou,SUM(money) money,SUM(jifen) jifen,SUM(money_dj) mdj,SUM(jifen_dj) jdj FROM " . DB_PREFIX . "moneylog where 1=1 ".$conditions . $cond);
					
					$row[$i]['riqi']=$st.iconv('utf-8','gbk','年').$mon.iconv('utf-8','gbk','月');
					$row[$i]['cou']=$qty['cou'];
					$row[$i]['z_money']=abs($qty['money']);
					$row[$i]['z_jifen']=abs($qty['jifen']);
					$row[$i]['z_moneydj']=abs($qty['mdj']);
					$row[$i]['z_jifendj']=abs($qty['jdj']);
					$aa=$aa+$qty['cou'];
					$bb=$bb+abs($qty['money']);
					$cc=$cc+abs($qty['jifen']);
					$dd=$dd+abs($qty['mdj']);
					$ee=$ee+abs($qty['jdj']);
					$this->assign('row',$row);
					$cond =' and 1=1';
					
		}
	}
		$this->assign('aa',$aa);
		$this->assign('bb',$bb);
		$this->assign('cc',$cc);
		$this->assign('dd',$dd);
		$this->assign('ee',$ee);
		$this->assign('arr_type',$arr_type);
		$this->assign('arr_month',$arr_month);
		$this->assign('arr_year',$arr_year);
		$this->display('baobiao.html');
	}
	
	function drop_cart()
	{
		$this->cart_mod=& m('cart');
		
		$riqi=date('Y-m-d H:i:s',strtotime("-1 weeks"));//一个星期之前
		$sql="delete from ".DB_PREFIX."cart where riqi<='$riqi'";
		$this->cart_mod->db->query($sql);
		$this->cart_mod->db->query("OPTIMIZE TABLE ".DB_PREFIX."cart");
		$this->show_message('caozuochenggong');
	}
	
	function drop_sessions()
	{
		$this->cart_mod=& m('cart');
		
		$riqi=date('Y-m-d H:i:s',strtotime("-2 weeks"));
		$dat=time($riqi);
		$sql="delete from ".DB_PREFIX."sessions where expiry<='$dat' limit 5000";
		$this->cart_mod->db->query($sql);
		$this->cart_mod->db->query("OPTIMIZE TABLE ".DB_PREFIX."sessions ");
		$this->show_message('caozuochenggong');
	}
	
	
	
	
}
?>