<?php

/**
 *    合作伙伴控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class OrderApp extends BackendApp
{
    /**
     *    管理
     *           
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function index()
    {
	$this->member_mod =& m('member');
	$this->userpriv_mod =& m('userpriv');
	
	$user=$this->visitor->get('user_name');
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$this->assign('priv_row',$priv_row);
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
	//$city=$row_member['city'];
	
        $search_options = array(
            'seller_name'   => Lang::get('store_name'),
            'buyer_name'   => Lang::get('buyer_name'),
            'payment_name'   => Lang::get('payment_name'),
            'order_sn'   => Lang::get('order_sn'),
        );
        /* 默认搜索的字段是店铺名 */
        $field = 'seller_name';
        array_key_exists($_GET['field'], $search_options) && $field = $_GET['field'];
        $conditions = $this->_get_query_conditions(array(array(
                'field' => $field,       //按用户名,店铺名,支付方式名称进行搜索
                'equal' => 'LIKE',
                'name'  => 'search_name',
            ),array(
                'field' => 'status',
                'equal' => '=',
                'type'  => 'numeric',
            ),array(
                'field' => 'add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            ),array(
                'field' => 'order_amount',
                'name'  => 'order_amount_from',
                'equal' => '>=',
                'type'  => 'numeric',
            ),array(
                'field' => 'order_amount',
                'name'  => 'order_amount_to',
                'equal' => '<=',
                'type'  => 'numeric',
            ),
			 array(
                'field' => 'city',
                'name'  => 'suoshuzhan',
                'equal' => '=',
            ),
        ));
        $model_order =& m('order');
        $page   =   $this->_get_page(10);    //获取分页信息
        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'add_time';
             $order = 'desc';
            }
        }
        else
        {
            $sort  = 'add_time';
            $order = 'desc';
        }
		if($privs=='all')
		{
        $orders = $model_order->find(array(
            'conditions'    => '1=1'. $conditions,
            'limit'         => $page['limit'],  //获取当前页的数据
            'order'         => "$sort $order",
            'count'         => true             //允许统计
        ));
		} //找出所有商城的合作伙伴
		else{
		 $orders = $model_order->find(array(
            'conditions'    => '1=1 and city='.$city . $conditions,
            'limit'         => $page['limit'],  //获取当前页的数据
            'order'         => "$sort $order",
            'count'         => true             //允许统计
        ));
		}
		
		$city_row=array();
		$result=$model_order->getAll("select * from ".DB_PREFIX."city");
		$this->assign('result',$result);
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		  	$city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($orders as $key => $val)
        {
			$sellerid=$val['seller_id'];
			$ro=$model_order->getRow("select * from ".DB_PREFIX."member where user_id='$sellerid' limit 1");
			$orders[$key]['city_name'] = $city_row[$val['city']];
			$orders[$key]['user_name'] = $ro['user_name'];	
			$orders[$key]['real_name'] = $ro['real_name'];			
        }	
			
		
        $page['item_count'] = $model_order->getCount();   //获取统计的数据
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('order_status_list', array(
            ORDER_PENDING => Lang::get('order_pending'),
            ORDER_SUBMITTED => Lang::get('order_submitted'),
            ORDER_ACCEPTED => Lang::get('order_accepted'),
            ORDER_SHIPPED => Lang::get('order_shipped'),
            ORDER_FINISHED => Lang::get('order_finished'),
            ORDER_CANCELED => Lang::get('order_canceled'),
        ));
        $this->assign('search_options', $search_options);
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条
        $this->assign('orders', $orders);
        $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->display('order.index.html');
    }
	
	
	function chaxun()
	{
		$this->userpriv_mod =& m('userpriv');
		$userid=$this->visitor->get('user_id');
		$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
		$this->assign('priv_row',$priv_row);
		$privs=$priv_row['privs'];
		
		$this->display('order.index.html');
	}

    /**
     *    查看
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function view()
    {
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$order_id)
        {
            $this->show_warning('no_such_order');

            return;
        }

        /* 获取订单信息 */
        $model_order =& m('order');
		$canshu=$model_order->can();
        $order_info = $model_order->get(array(
            'conditions'    => $order_id,
            'join'          => 'has_orderextm',
            'include'       => array(
                'has_ordergoods',   //取出订单商品
            ),
        ));
		$buyer_id=$order_info['buyer_id'];
$result=$model_order->getRow("select * from ".DB_PREFIX."member where user_id='$buyer_id' limit 1");
$vip=$result['vip'];
$this->assign('result',$result);
/*if($vip==1)
{
$order_info['fee']=$order_info['shipping_fee']*$canshu['jifenxianjin']*(1+$canshu['lv21']);
}
else
{
$order_info['fee']=$order_info['shipping_fee']*$canshu['jifenxianjin']*(1+$canshu['lv31']);	
}*/


        if (!$order_info)
        {
            $this->show_warning('no_such_order');
            return;
        }
        $order_type =& ot($order_info['extension']);
        $order_detail = $order_type->get_order_detail($order_id, $order_info);
        $order_info['group_id'] = 0;
        if ($order_info['extension'] == 'groupbuy')
        {
            $groupbuy_mod =& m('groupbuy');
            $groupbuy = $groupbuy_mod->get(array(
                'fields' => 'groupbuy.group_id',
                'join' => 'be_join',
                'conditions' => "order_id = {$order_info['order_id']} ",
                )
            );
            $order_info['group_id'] = $groupbuy['group_id'];
        }
	
        foreach ($order_detail['data']['goods_list'] as $key => $goods)
        {
            if (substr($goods['goods_image'], 0, 7) != 'http://')
            {
                $order_detail['data']['goods_list'][$key]['goods_image'] = SITE_URL . '/' . $goods['goods_image'];
            }
			$goodsid=$goods['goods_id'];
			$row=$model_order->getRow("select * from ".DB_PREFIX."goods where goods_id='$goodsid' limit 1");	
			
			$order_info['dan']=$order_info['goods_jifen']/$goods['quantity'];
			$order_info['danprice']=$order_info['goods_amount_m']/$goods['quantity'];
			$order_info['er']=$goods['price']*$canshu['jifenxianjin'];
			
        }
		
	
		$sell_id=$order_info['seller_id'];
		

		$mem=$model_order->getRow("select * from ".DB_PREFIX."member where user_id = '$sell_id' limit 1");
		
		$stor=$model_order->getRow("select * from ".DB_PREFIX."store where store_id = '$sell_id' limit 1");
		
		$leve=$mem['level'];
		$bb=explode(',',$leve);
		if(in_array(1,$bb))
		{
			$order_info['ghs']=$order_info['goods_amount']*$canshu['jifenxianjin']*(1-$canshu['daishou']);
		}
		else
		{
			$order_info['ghs']=$order_info['goods_amount']*$canshu['jifenxianjin'];
		}
		
		
		/*if($order_info['discount_jifen']==0.00)
		{
		$order_info['jifen']=$order_info['youhuidiscount_jifen'];
		$order_info['pricemm']=$order_info['youhuidiscount'];
		}
		else
		{*/
		$order_info['jifen']=$order_info['discount_jifen']+$order_info['youhuidiscount_jifen'];
		$order_info['pricemm']=$order_info['discount']+$order_info['youhuidiscount'];
		/*}*/
		if(!empty($order_info['zhe_jifen']))//是否参与打折
		{
			$order_info['dazhe']=$order_info['zhe_jifen']*$order_info['order_jifen'];
			$order_info['zhe_jifen']=$order_info['zhe_jifen']*10;
		}	
		
		if($order_info['pay_time']!="")
		{
		$order_info['pay_time']=date('Y-m-d H:i:s',$order_info['pay_time']);
		}
	    $this->assign('stor', $stor);
		$this->assign('bb', $bb);
		$this->assign('row', $row);
		$this->assign('mem', $mem);
		$gh_id=$order_info['gh_id'];
		$gong=$model_order->getRow("select * from ".DB_PREFIX."gonghuo where gh_id = '$gh_id' limit 1");
		
	$or=$model_order->getRow("select * from ".DB_PREFIX."order_log where order_id = '$order_id' limit 1");
		$this->assign('or', $or);
		$this->assign('gong', $gong);
        $this->assign('order', $order_info);
        $this->assign($order_detail['data']);
        $this->display('order.view.html');
    }
	
	function message()
	{
	$this->_admin_mod=& m('member');
		$admin_id=(int)$this->visitor->get('user_id');
		$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$admin_id' and store_id=0 limit 1");
		$privs=$priv_row['privs'];
		$city=(int)$priv_row['city'];
		$conditions=" and 1=1";

		if ($privs!="all")
		{
		 $conditions=" and city='$city'";
		 $conditions1=" and gh_city='$city'";
		 $conditions2=" and grcity='$city'";
		 $conditions3=" and cityid='$city' and sgrade=1";
		}
		
if($admin_id!=0)
{
			
	$first=1;
	$time=60;
	$message =& m('message');
	echo 'OK[#]';
	if($first==1)//webservices队列问题
	{
		$row1=$this->_admin_mod->getRow("SELECT user_id FROM ".DB_PREFIX."member WHERE length( web_id ) <>36 limit 1");
		$row=$this->_admin_mod->getRow("select id  from ".DB_PREFIX."webservice_list where  consume_id='0' limit 1");
		if($row || $row1)
		{
			echo "<font color='#FF0000'>重要提示，数据有异常，请联系总部！</font><br>";	
		}
	}
	if ($privs=="all")
	{
		//充值未审核
		$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."my_moneylog where leixing=30 and status=0 ");
		$count=$row['count'];
		
		if($count>0)
		{
			echo "您有{$count}条新的充值消息，<a href='#' onclick=\"win_open('index.php?module=my_money&act=cz_wei_shenhe')\">点击查看</a><br>";	
		}
				
		
		//提现未审核
		$result=$this->_admin_mod->getAll("select tx_type from ".DB_PREFIX."my_moneylog where leixing=40 and status1=1 and caozuo=60");
		$pt=0;
		$ks=0;
		foreach($result as $row)
		{
			if($row['tx_type']==1)
				$ks++;
			else
				$pt++;
		}					
		if($pt>0)
		{
			echo "您有{$pt}条新的普通提现消息，<a href='#' onclick=\"win_open('index.php?module=my_money&act=tx_wei_shenhe')\">点击查看</a><br>";	
		}
		if($ks>0)
		{
			echo "您有{$ks}条新的快速提现消息，<a href='#' onclick=\"win_open('index.php?module=my_money&act=tx_wei_shenhe')\" style='color:red'>点击查看</a><br>";	
		}
			
		//打款未审核
		$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."my_moneylog where leixing=40 and status1=2 and caozuo=60 ");
		$count=$row['count'];
		
		if($count>0)
		{
			echo "您有{$count}条新的打款未审核消息，<a href='#' onclick=\"win_open('index.php?module=my_money&act=tx_yi_shenhe')\">点击查看</a><br>";	
		}
				
		//兑换现金未审核	
		$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."my_moneylog where leixing=12 and status=0 ");
		$count=$row['count'];
		
		if($count>0)
		{
			echo "您有{$count}条新的兑换现金消息，<a href='#' onclick=\"win_open('index.php?module=my_money&act=duihuanxianjin_wei_shenhe')\">点击查看</a><br>";	
		}
		
		//供货人申请未审核
		$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."gonghuo_xinxi where status=1 ");
		$count=$row['count'];
		
		if($count>0)
		{
			echo "您有{$count}条新的供货商申请消息，<a href='#' onclick=\"win_open('index.php?app=gonghuo&act=sq_weishenhe')\">点击查看</a><br>";	
		}
		
		//投诉审核
		$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."complain where status1=0 and ts_id=0 ");
		$count=$row['count'];
		if($count>0)
		{
			echo "您有{$count}条新的投诉消息，<a href='#' onclick=\"win_open('index.php?app=tousu&act=ts_weishenhe')\">点击查看</a><br>";	
		}
			
		//品牌审核
		$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."brand where if_show=0 ");
		$count=$row['count'];
		if($count>0)
		{
			echo "您有{$count}条新的品牌申请消息，<a href='#' onclick=\"win_open('index.php?app=brand&wait_verify=1')\">点击查看</a><br>";	
		}
		
		//借款未审核
		$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."jiekuan where  status=1 ");
		$count=$row['count'];
		if($count>0)
		{
			echo "您有{$count}条新的借款消息，<a href='#' onclick=\"win_open('index.php?app=invite&act=jk_weishenhe')\">点击查看</a><br>";	
		}
	}
	
	//供货商品未审核   分站操作  
	$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."gonghuo where status=0 ".$conditions1);
	$count=$row['count'];
	if($count>0)
	{
		echo "您有{$count}条新的供货商品消息，<a href='#' onclick=\"win_open('index.php?app=gonghuo&act=ghwei_shenhe')\">点击查看</a><br>";	
	}
	//团购未审核		
	$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."groupbuy where status=0 ".$conditions2);
	$count=$row['count'];
	if($count>0)
	{
		echo "您有{$count}条新的团购审核消息，<a href='#' onclick=\"win_open('index.php?app=groupbuy&act=wei_shenhe')\">点击查看</a><br>";	
	}
			
	//团购已完成
	$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."groupbuy where state=3 ".$conditions2);
	$count=$row['count'];
	if($count>0)
	{
		echo "您有{$count}条新的团购已完成消息，<a href='#' onclick=\"win_open('index.php?app=groupbuy&act=wancheng')\">点击查看</a><br>";	
	}
	
	//店铺审核			
	$row=$this->_admin_mod->getRow("select count(*) count from ".DB_PREFIX."store where state=0 ".$conditions3);
	$count=$row['count'];
	if($count>0)
	{
		echo "您有{$count}条新的店铺审核消息，<a href='#' onclick=\"win_open('index.php?app=store&wait_verify=1')\">点击查看</a><br>";	
	}
		
}
}
	
	
	
}
?>
