<?php

/**
 *    售货员控制器，其扮演实际交易中柜台售货员的角色，你可以这么理解她：你告诉我（售货员）要买什么东西，我会询问你你要的收货地址是什么之类的问题
 ＊        并根据你的回答来生成一张单子，这张单子就是“订单”
 *
 *    @author    Garbin
 *    @param    none
 *    @return    void
 */
class OrderApp extends ShoppingbaseApp
{
    /**
     *    填写收货人信息，选择配送，支付方式。
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function index()
    {
	
	$this->kaiguan_mod=& m('kaiguan');
	$kaiguan=$this->kaiguan_mod->kg();
	$this->assign('kaiguan',$kaiguan);
	$canshu=$this->kaiguan_mod->can();
        if (!IS_POST)
        {
            $goods_info = $this->_get_goods_info();
			//print_r($goods_info);
			//exit;
            if ($goods_info === false)
            {
                /* 购物车是空的 */
                $this->show_warning('goods_empty');

                return;
            }

            /* 根据商品类型获取对应订单类型 */
            $goods_type     =&  gt($goods_info['type']);
            $order_type     =&  ot($goods_info['otype']);
			

            /* 显示订单表单 */
            $form = $order_type->get_order_form($goods_info['store_id']);
			//print_r($form);
			//print_r($form['data']['shippings']);
			if($form['data']['shippings']=="")
			{
				$this->show_warning('gaidianpu');
                return;
			}
			if($goods_info['daishou']==1 || $goods_info['otype']=="groupbuy")
			{
				foreach($form['data']['shippings'] as $i=>$ship)
				{
					$form['data']['shippings'][$i]['first_price']=0;
					$form['data']['shippings'][$i]['step_price']=0;
				}
			}
			$form['data']['shippings']=ecm_json_encode($form['data']['shippings']);

		
            if ($form === false)
            {
                $this->show_warning($order_type->get_error());

                return;
            }
            $this->_curlocal(
                LANG::get('create_order')
            );
           $this->assign('page_title', Lang::get('confirm_order') . ' - ' . Conf::get('site_title'));
			//print_r($form);
            $this->assign('goods_info', $goods_info);
            $this->assign($form['data']);
            $this->display($form['template']);
        }
        else
        {
            /* 在此获取生成订单的两个基本要素：用户提交的数据（POST），商品信息（包含商品列表，商品总价，商品总数量，类型），所属店铺 */
            $goods_info = $this->_get_goods_info();
			
			
            $store_id = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
            if ($goods_info === false)
            {
                /* 购物车是空的 */
                $this->show_warning('goods_empty');
                return;
            }
			
			
			
			/* 根据商品类型获取对应订单类型 */
            $goods_type     =&  gt($goods_info['type']);
            $order_type     =&  ot($goods_info['otype']);
			  /* 显示订单表单 */
            $form = $order_type->get_order_form($goods_info['store_id']);
			
			$userid=$this->visitor->get('user_id');
			$mem=$this->kaiguan_mod->getRow("select vip from ".DB_PREFIX."member where user_id='$userid' limit 1");
			$vip=$mem['vip'];
			
			
			$good_ship=array();	
			foreach($goods_info['items'] as $key=>$good)//循环购买商品
			{
				$shipping_id=$good['shipping_id'];
				$baoyou_type=$good['baoyou_type'];
				$baoyou_money=$good['baoyou_money'];
				$baoyou_jifen=$baoyou_money*$canshu['jifenxianjin'];
				$baoyou_jian=$good['baoyou_jian'];
				$quantity=$good['quantity'];
				$weight=$good['weight'];
				$volume=$good['volume'];
				$sub=$good['subtotal_m'];//商品总价格
	
				$daishou=$good['daishou'];
				/*if($daishou==3)//快采售出
				{
				 	$subjifen=$quantity*$good['price']*$canshu['jifenxianjin'];
				}
				else
				{
					$subjifen=$quantity * $good['price']*$canshu['jifenxianjin']*(1+$canshu['lv21']);				                }*/
				
				
				if($good['baoyou_type']==0 || ($good['baoyou_type']!=0 &&($quantity<$baoyou_jian || $good['subjifen']<$baoyou_jifen)))//不包邮
				{
					foreach($form['data']['shipping_methods'] as $key2=>$ships)
					{
						if($shipping_id==$ships['shipping_id'])
						{
								$typeid=$ships['typeid'];
								if($typeid==2 && $weight!='')//按重量
								{
									$quantity=$quantity*$weight;
								}
								if($typeid==3 && $volume!='')//按体积
								{
									$quantity=$quantity*$volume;
								}
								$cod_regions=unserialize($ships['cod_regions']);
								$regions_def=array_shift($cod_regions);//默认地区	
								foreach($cod_regions as $key3=>$var)//除去默认地区
								{
									$bb=explode(',',$var['areaid']);
									if(in_array($_POST['region_id'],$bb))
									{	
										if($quantity<=$var['one'])
										{
											$good_ship[$key]['fee_money']=$var['price'];
											$goods_info['items'][$key]['fee_money']=$var['price'];
										}
										else
										{
											$good_ship[$key]['fee_money']=$var['price']+$var['nprice']*($quantity-$var['one']);
											$goods_info['items'][$key]['fee_money']=$var['price']+ceil(($quantity-$var['one'])/$var['next'])*$var['nprice'];
										}
									}
								}
								if(empty($good_ship[$key]))//默认地区
								{
									 	if($quantity<=$regions_def['one'])
										{
											$goods_info['items'][$key]['fee_money']=$regions_def['price'];
										}
										else
										{
											$goods_info['items'][$key]['fee_money']=$regions_def['price']+ceil(($quantity-$regions_def['one'])/$regions_def['next'])*$regions_def['nprice'];
											
										}
								}
				       }
				   }
			   }
			   else//几件包邮
			   {
					$goods_info['items'][$key]['fee_money']=0;
			   }
				
				$goods_info['items'][$key]['fee_jifen']=$goods_info['items'][$key]['fee_money']*$canshu['jifenxianjin'];
			}
			
            /* 优惠券数据处理 */
           if ($goods_info['allow_coupon'] && isset($_POST['coupon_sn']) && !empty($_POST['coupon_sn']))
            {
                $coupon_sn = trim($_POST['coupon_sn']);
                $coupon_mod =& m('couponsn');
                $coupon = $coupon_mod->get(array(
                    'fields' => 'coupon.*,couponsn.remain_times,couponsn.coupon_sn',
                    'conditions' => "coupon_sn.coupon_sn = '{$coupon_sn}' AND coupon.store_id = " . $store_id,
                    'join'  => 'belongs_to_coupon'));
                if (empty($coupon))
                {
                    $this->show_warning('involid_couponsn');
                    exit;
                }
                if ($coupon['remain_times'] < 1)
                {
                    $this->show_warning("times_full");
                    exit;
                }
                $time = gmtime();
                if ($coupon['start_time'] > $time)
                {
                    $this->show_warning("coupon_time");
                    exit;
                }

                if ($coupon['end_time'] < $time)
                {
                    $this->show_warning("coupon_expired");
                    exit;
                }
                if ($coupon['min_amount'] > $goods_info['amount'])
                {
                    $this->show_warning("amount_short");
                    exit;
                }
                unset($time);
                $goods_info['discount'] = $coupon['coupon_value'];
				$goods_info['discount_jifen'] = $coupon['coupon_jifen'];
				$goods_info['coupon_id'] = $coupon['coupon_id'];
				$goods_info['coupon_sn'] = $coupon['coupon_sn'];
            }
		
			//付费优惠券处理
			  //$user_name = $_GET['user_name'];
			 // echo $user_name;
				$this->youhuilist_mod =& m('youhuilist');
				$this->youhuiquan_mod =& m('youhuiquan');
			if ($goods_info['allow_coupon'] && isset($_POST['bianhao']) && !empty($_POST['bianhao']))
            {
             $bianhao = trim($_POST['bianhao']);
             
			 $youhuilist_row=$this->youhuilist_mod->getRow("select * from ".DB_PREFIX."youhuilist where bianhao='$bianhao' limit 1");
		$youhui_id=$youhuilist_row['youhui_id'];
		$user_name=$youhuilist_row['user_name'];
		//echo $user_name;
		$youhuiquan_row=$this->youhuiquan_mod->getRow("select * from ".DB_PREFIX."youhuiquan where youhui_id='$youhui_id' limit 1");
		$start_time=$youhuiquan_row['start_time'];
		$end_time=$youhuiquan_row['end_time'];
			 
			 
                $youhui = $this->youhuilist_mod->get(array(
            'fields' => '*',
            'conditions' => "youhuilist.bianhao = '{$bianhao}' AND youhuilist.user_name = '$user_name'"
            ));
                if (empty($youhui))
                {
                    $this->show_warning('involid_couponsn');
                    exit;
                }
             
               $time = date('Y-m-d');
		
		
                    if ($start_time> $time)
                {
                    $this->show_warning("coupon_time");
                    exit;
                }

                if ($end_time < $time)
                {
                    $this->show_warning("coupon_expired");
                    exit;
                }
               
                unset($time);
                $goods_info['youhuidiscount'] = $youhui['youhui_jine'];
				$goods_info['youhuidiscount_jifen'] = $youhui['youhui_jifen'];
				$goods_info['youhui_name'] = $youhui['youhui_name'];
				$goods_info['youhui_id'] = $youhui['youhui_id'];
				$goods_info['bianhao'] = $bianhao;
				
				$status='no'; //改变优惠券状态
				$status=array(
				'status'=>$status,													
				);
    			$this->youhuilist_mod->edit('bianhao='.$bianhao,$status);
				
            }
			
			
			
			/* 根据商品类型获取对应的订单类型 */
            $goods_type =& gt($goods_info['type']);
            $order_type =& ot($goods_info['otype']);
            /* 将这些信息传递给订单类型处理类生成订单(你根据我提供的信息生成一张订单) */
            $order_id = $order_type->submit_order(array(
                'goods_info'    =>  $goods_info,      //商品信息（包括列表，总价，总量，所属店铺，类型）,可靠的!
                'post'          =>  $_POST,           //用户填写的订单信息
            ));


            if (!$order_id)
            {
                $this->show_warning($order_type->get_error());

                return;
            }

            /*  检查是否添加收货人地址  */
            if (isset($_POST['save_address']) && (intval(trim($_POST['save_address'])) == 1))
            {
                 $data = array(
                    'user_id'       => $this->visitor->get('user_id'),
                    'consignee'     => trim($_POST['consignee']),
                    'region_id'     => $_POST['region_id'],
                    'region_name'   => $_POST['region_name'],
                    'address'       => trim($_POST['address']),
                    'zipcode'       => trim($_POST['zipcode']),
                    'phone_tel'     => trim($_POST['phone_tel']),
                    'phone_mob'     => trim($_POST['phone_mob']),
                );
                $model_address =& m('address');
                $model_address->add($data);
            }
            /* 下单完成后清理商品，如清空购物车，或将团购拍卖的状态转为已下单之类的 */
            $this->_clear_goods($order_id);

            /* 发送邮件 */
            $model_order =& m('order');

            /* 减去商品库存 */
            $model_order->change_stock('-', $order_id);

            /* 获取订单信息 */
            $order_info = $model_order->get($order_id);

            /* 发送事件 */
            $feed_images = array();
            foreach ($goods_info['items'] as $_gi)
            {
                $feed_images[] = array(
                    'url'   => SITE_URL . '/' . $_gi['goods_image'],
                    'link'  => SITE_URL . '/' . url('app=goods&id=' . $_gi['goods_id']),
                );
            }
            $this->send_feed('order_created', array(
                'user_id'   => $this->visitor->get('user_id'),
                'user_name' => addslashes($this->visitor->get('user_name')),
                'seller_id' => $order_info['seller_id'],
                'seller_name' => $order_info['seller_name'],
                'store_url' => SITE_URL . '/' . url('app=store&id=' . $order_info['seller_id']),
                'images'    => $feed_images,
            ));

            $buyer_address = $this->visitor->get('email');
            $model_member =& m('member');
            $member_info  = $model_member->get($goods_info['store_id']);
            $seller_address= $member_info['email'];

            /* 发送给买家下单通知 */
            $buyer_mail = get_mail('tobuyer_new_order_notify', array('order' => $order_info));
           // $this->_mailto($buyer_address, addslashes($buyer_mail['subject']), addslashes($buyer_mail['message']));
			//sendmail(addslashes($buyer_mail['subject']),addslashes($buyer_mail['message']),$buyer_address);
            /* 发送给卖家新订单通知 */
            $seller_mail = get_mail('toseller_new_order_notify', array('order' => $order_info));
            //$this->_mailto($seller_address, addslashes($seller_mail['subject']), addslashes($seller_mail['message']));

       		//sendmail(addslashes($seller_mail['subject']),addslashes($seller_mail['message']),$seller_address);
            /* 更新下单次数 */
            $model_goodsstatistics =& m('goodsstatistics');
            $goods_ids = array();
            foreach ($goods_info['items'] as $goods)
            {
                $goods_ids[] = $goods['goods_id'];
            }
            $model_goodsstatistics->edit($goods_ids, 'orders=orders+1');

			$this->email_mod=& m('email');
			$email_log=array(
				'user_name'=>$this->visitor->get('user_name'),
				'order_id'=>$order_id,
				'subject'=>$buyer_mail['subject'],
				'message'=>$buyer_mail['message'],
				'status'=>2,
				'address'=>$buyer_address,
			);
			$this->email_mod->add($email_log);	
            /* 到收银台付款 */
            header('Location:index.php?app=cashier&can=1&order_id=' . $order_id);
        }
    }



	function send_email()
	{
		 
		 /* 发送邮件 */
            $this->email_mod =& m('email');
			$order_id=$_POST['order_id'];
            /* 获取订单信息 */
            $riqi=date('Y-m-d H:i:s');
			$row=$this->email_mod->getRow("select * from ".DB_PREFIX."email where order_id='$order_id' limit 1");
		 /* 发送给买家下单通知 */
		    if($row['status']==2)
			{
				sendmail(addslashes($row['subject']),addslashes($row['message']),$row['address']);
				$this->email_mod->edit('order_id='.$order_id,array('status'=>1,'riqi'=>$riqi));
			}	
            
	}
	
    /**
     *    获取外部传递过来的商品
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _get_goods_info()
    {
	$this->goods_mod=& m(goods);
	$this->goodsspec_mod=& m(goodsspec);
        $return = array(
            'items'     =>  array(),    //商品列表
            'quantity'  =>  0,          //商品总量
            'amount'    =>  0,          //商品总价
			'amount_m'    =>  0,          //商品总价
			'am_jifen'    =>  0,          //商品积分
			'am_jifen1'    =>  0,          //商品积分
            'store_id'  =>  0,          //所属店铺
            'store_name'=>  '',         //店铺名称
            'type'      =>  null,       //商品类型
            'otype'     =>  'normal',   //订单类型
            'allow_coupon'  => true,    //是否允许使用优惠券
			'daishou'  => '',
			'vip'  =>  0,          //用户是否是vip
			'jifenxianjin'  =>  0,          
			'lv31'  =>  0,          
			'lv21'  =>  0,          
			        );
					
			$canshu=$this->goods_mod->can();
			$lv21=$canshu['lv21'];
			$lv31=$canshu['lv31'];
			$jifenxianjin=$canshu['jifenxianjin'];
        switch ($_GET['goods'])
        {
            case 'groupbuy':
                /* 团购的商品 */
                $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
                $user_id  = $this->visitor->get('user_id');
				$mem=$this->goods_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");
				
				$vip=intval($mem['vip']);
                if (!$group_id || !$user_id)
                {
                    return false;
                }
                /* 获取团购记录详细信息 */
                $model_groupbuy =& m('groupbuy');
                $groupbuy_info = $model_groupbuy->get(array(
                    'join'  => 'be_join, belong_store, belong_goods',
                    'conditions'    => $model_groupbuy->getRealFields("groupbuy_log.user_id={$user_id} AND groupbuy_log.group_id={$group_id} AND groupbuy_log.order_id=0 "),
                    'fields'    => 'store.store_id, store.store_name, goods.goods_id, goods.goods_name, goods.default_image,goods.daishou, groupbuy_log.quantity, groupbuy_log.spec_quantity, this.spec_price',
                ));


                /*if (empty($groupbuy_info))
                {
				echo 111;
                    return false;
                }*/

                /* 获取商品信息 */
				if(empty($groupbuy_info['spec_quantity']))
				{
				header("Location:index.php?app=groupbuy&id=$group_id");
				exit;
				}
				if(empty($groupbuy_info['spec_price']))
				{
				$groupbuy_info=array();
				}
                $spec_quantity = unserialize($groupbuy_info['spec_quantity']);
                $spec_price    = unserialize($groupbuy_info['spec_price']);
                $amount = 0;
                $groupbuy_items = array();
                $goods_image = empty($groupbuy_info['default_image']) ? Conf::get('default_goods_image') : $groupbuy_info['default_image'];
                foreach ($spec_quantity as $spec_id => $spec_info)
                {
                    $the_price = $spec_price[$spec_id]['price'];
                    $subtotal = $spec_info['qty'] * $the_price;
					/*if($vip==1)
					{*/
						$subjifen=$spec_info['qty'] * $the_price*$jifenxianjin*(1+$lv21);
					/*}
					else
					{
						$subjifen=$spec_info['qty'] * $the_price*$jifenxianjin*(1+$lv31);
					}*/
                    $groupbuy_items[] = array(
                        'goods_id'  => $groupbuy_info['goods_id'],
                        'goods_name'  => $groupbuy_info['goods_name'],
                        'spec_id'  => $spec_id,
                        'specification'  => $spec_info['spec'],
                        'price'  => $the_price,
                        'quantity'  => $spec_info['qty'],
                        'goods_image'  => $goods_image,
                        'subtotal'  => $subtotal,
						'subtotal_m'  => m_21($subtotal),
						'subjifen'  => $subjifen,
						'daishou'=>$groupbuy_info['daishou']
                    );
                    $amount += $subtotal;
					$amount_m=m_21($amount);
                }
				
				
				/*if($vip==1)
				{*/
				$return['am_jifen']=$amount*$jifenxianjin*(1+$lv21);
				/*}
				else
				{
				$return['am_jifen']=$amount*$jifenxianjin*(1+$lv31);
				}*/
              $spec_row=$this->goodsspec_mod->getRow("select * from ".DB_PREFIX."goods_spec where spec_id='$spec_id'");

if($groupbuy_info['quantity']>$spec_row['stock'])
{
            $this->show_warning('no_enough_goods'); 
			/*$this->json_error('no_enough_goods');*/
            return;
}  
				

                $return['items']        =   $groupbuy_items;
                $return['quantity']     =   $groupbuy_info['quantity'];
                $return['amount']       =   $amount;
				$return['amount_m']       =   $amount_m;
                $return['store_id']     =   $groupbuy_info['store_id'];
                $return['store_name']   =   $groupbuy_info['store_name'];
                $return['type']         =   'material';
                $return['otype']        =   'groupbuy';
                $return['allow_coupon'] =   false;
				$return['daishou']        =   $groupbuy_info['daishou'];
            break;
            default:
                /* 从购物车中取商品 */
                $_GET['store_id'] = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
                $store_id = $_GET['store_id'];
				//print_r($store_id);
				$_GET['ger_id'] = isset($_GET['ger_id']) ? intval($_GET['ger_id']) : 0;
                $ger_id = $_GET['ger_id'];
                if (!$store_id)
                {
                    return false;
                }


                $cart_model =& m('cart');
$userid=$this->visitor->get('user_id');
                $cart_items      =  $cart_model->find("user_id = " . $userid . " AND store_id = {$store_id} AND ger_id={$ger_id} and session_id='" . SESS_ID . "'");
				
				$mem=$this->goods_mod->getRow("select * from ".DB_PREFIX."member where user_id='$userid' limit 1");
				
				$vip=intval($mem['vip']);
				
                if (empty($cart_items))
                {
                    return false;
                }

                $store_model =& m('store');
                $store_info = $store_model->get($store_id);

                foreach ($cart_items as $rec_id => $goods)
                {
				
					if($goods['quantity']<1 || $goods['price']<=0)
					{
						$this->show_warning('error');
						exit;
					}
                    $return['quantity'] += $goods['quantity'];                      //商品总量
                    $return['amount']   += $goods['quantity'] * $goods['price'];    //商品总价
					
                    $cart_items[$rec_id]['subtotal']    =   $goods['quantity'] * $goods['price'];   //小计
					
					$cart_items[$rec_id]['subtotal_m']    =   m_21($goods['quantity'] * $goods['price']);   //小计
                    empty($goods['goods_image']) && $cart_items[$rec_id]['goods_image'] = Conf::get('default_goods_image');
					$zong=$return['amount'];
					
						 $cart_items[$rec_id]['subjifen']    =   $goods['quantity'] * $goods['price']*$jifenxianjin*(1+$lv21); 
					if($goods['daishou']==3)
					{
					$cart_items[$rec_id]['subtotal_m']    =  $goods['quantity'] * $goods['price'];   
					$cart_items[$rec_id]['subjifen']    =   $cart_items[$rec_id]['subtotal_m']*$jifenxianjin; 
					}
					
					$goodsid=$goods['goods_id'];
					$goo=$cart_model->getrow("select if_show,baoyou_type,baoyou_jian,baoyou_money,shipping_id,weight,volume from ".DB_PREFIX."goods where goods_id ='$goodsid' limit 1");
					$cart_items[$rec_id]['if_show']    =   $goo['if_show']; 
					$cart_items[$rec_id]['baoyou_type']    =   $goo['baoyou_type']; 
					$cart_items[$rec_id]['baoyou_jian']    =   $goo['baoyou_jian']; 
					$cart_items[$rec_id]['baoyou_money']    =   $goo['baoyou_money'];
					$cart_items[$rec_id]['shipping_id']    =   $goo['shipping_id']; 
					$cart_items[$rec_id]['weight']    =   $goo['weight']; 
					$cart_items[$rec_id]['volume']    =   $goo['volume']; 	
                }
$goodid=$cart_items[$rec_id]['goods_id'];
$goods_row=$this->goods_mod->getRow("select * from ".DB_PREFIX."goods where goods_id='$goodid' limit 1");
$daishou=$goods_row['daishou'];

	$return['am_jifen']=$zong*$jifenxianjin*(1+$lv21);
	$return['am_jifen1']=round($zong*$jifenxianjin*(1+$lv21),2);
	$return['amount_m'] = m_21($zong);    //商品总价


if($daishou==3)
{
$return['am_jifen']=$zong*$jifenxianjin;
$return['am_jifen1']=round($zong*$jifenxianjin,2);
$return['amount_m'] = $zong;    //商品总价	
}

                $return['items']        =   $cart_items;
                $return['store_id']     =   $store_id;
                $return['store_name']   =   $store_info['store_name'];
                $return['type']         =   'material';
                $return['otype']        =   'normal';
				$return['daishou']        =   $daishou;
				$return['vip']        =   $vip;
				$return['jifenxianjin']        =   $jifenxianjin;
				$return['lv31']        =   $lv31;
				$return['lv21']        =   $lv21;
            break;
        }

        return $return;
    }

    /**
     *    下单完成后清理商品
     *
     *    @author    Garbin
     *    @return    void
     */
    function _clear_goods($order_id)
    {
        switch ($_GET['goods'])
        {
            case 'groupbuy':
                /* 团购的商品 */
                $model_groupbuy =& m('groupbuy');
                $model_groupbuy->updateRelation('be_join', $_GET['group_id'], $this->visitor->get('user_id'), array(
                    'order_id'  => $order_id,
                ));
            break;
            default://购物车中的商品
                /* 订单下完后清空指定购物车 */
                $_GET['store_id'] = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
                $store_id = $_GET['store_id'];
                if (!$store_id)
                {
                    return false;
                }
                $model_cart =& m('cart');
                $model_cart->drop("store_id = {$store_id} AND session_id='" . SESS_ID . "'");
                //优惠券信息处理
                if (isset($_POST['coupon_sn']) && !empty($_POST['coupon_sn']))
                {
                    $sn = trim($_POST['coupon_sn']);
                    $couponsn_mod =& m('couponsn');
                    $couponsn = $couponsn_mod->get("coupon_sn = '{$sn}'");
                    if ($couponsn['remain_times'] > 0)
                    {
                        $couponsn_mod->edit("coupon_sn = '{$sn}'", "remain_times= remain_times - 1");
                    }
                }
            break;
        }
    }
    /**
     * 检查优惠券有效性
     */
    function check_coupon()
    {
        $coupon_sn = $_GET['coupon_sn'];
        $store_id = $_GET['store_id'];
        if (empty($coupon_sn))
        {
            $this->js_result(false);
        }
        $coupon_mod =& m('couponsn');
        $coupon = $coupon_mod->get(array(
            'fields' => 'coupon.*,couponsn.remain_times',
            'conditions' => "coupon_sn.coupon_sn = '{$coupon_sn}' AND coupon.store_id = " . $store_id,
            'join'  => 'belongs_to_coupon'));
        if (empty($coupon))
        {
            $this->json_result(false);
            exit;
        }
        if ($coupon['remain_times'] < 1)
        {
            $this->json_result(false);
            exit;
        }
        $time = gmtime();
        if ($coupon['start_time'] > $time)
        {
            $this->json_result(false);
            exit;
        }


        if ($coupon['end_time'] < $time)
        {
            $this->json_result(false);
            exit;
        }

        // 检查商品价格与优惠券要求的价格
        $model_cart =& m('cart');
        $item_info  = $model_cart->find("store_id={$store_id} AND session_id='" . SESS_ID . "'");
        $price = 0;
        foreach ($item_info as $val)
        {
            $price = $price + $val['price'] * $val['quantity'];
        }
        if ($price < $coupon['min_amount'])
        {
            $this->json_result(false);
            exit;
        }
        $this->json_result(array('res' => true, 'price' => $coupon['coupon_value'],'jifen_price'=>$coupon['coupon_jifen']));
        exit;

    }
	
	
	 function check_youhuiquan()
    {
	$this->youhuiquan_mod =& m('youhuiquan');
	$this->youhuilist_mod =& m('youhuilist');
        $bianhao = $_GET['bianhao'];
		
        $user_name = $_GET['user_name'];

		$youhuilist_row=$this->youhuilist_mod->getRow("select * from ".DB_PREFIX."youhuilist where bianhao='$bianhao' limit 1");
		$youhui_id=$youhuilist_row['youhui_id'];
		$status=$youhuilist_row['status'];
				
		//$youhui_jine=$youhuilist_row['youhui_jine'];
		$youhuiquan_row=$this->youhuiquan_mod->getRow("select * from ".DB_PREFIX."youhuiquan where youhui_id='$youhui_id' limit 1");
		$start_time=$youhuiquan_row['start_time'];
		$end_time=$youhuiquan_row['end_time'];
		
		
        if (empty($bianhao))
        {
            $this->js_result(false);
        }
		 
       //$youhui=$this->youhuilist_mod->getrow("select * from ".DB_PREFIX."youhuilist where bianhao='$bianhao' and user_name='$user_name'");
        $youhui = $this->youhuilist_mod->get(array(
            'fields' => 'youhuilist.*',
            //'conditions' => "youhuilist.bianhao = '$bianhao' AND youhuilist.user_name ='$user_name'"
			'conditions' => "youhuilist.bianhao = '$bianhao'"  
            ));
			
        if (empty($youhui))
        {
            $this->json_result(false);
            exit;
        }
	
        $riqi = date('Y-m-d');

        if ($start_time> $riqi)
        {
            $this->json_result(false);
            exit;
        }


        if ($end_time < $riqi)
        {
            $this->json_result(false);
            exit;
        }
		
		 if ($status != "yes")
        {
            $this->json_result(false);
            exit;
        }

       
       $this->json_result(array('res' => true, 'price' => $youhui['youhui_jine'],'jifen_price'=>$youhui['youhui_jifen']));
        exit; 

    }
	
	
	function get_yunfei()
	{
		$cart_model =& m('cart');
		$userid=$this->visitor->get('user_id');
		$region_id=$_POST['region_id'];	
		$store_id=$_POST['store_id'];
		//$cangkuid=$_POST['cangkuid'];
		//$cart_items=$this->_get_goods_info();
		
		$cart_items      =  $cart_model->find("user_id = " . $userid . " AND store_id = '$store_id'   and session_id='" . SESS_ID . "'");
		
		$shipping_methods=$cart_model->getAll("select * from ".DB_PREFIX."shippings where store_id='$store_id'");
		
		$mem=$cart_model->getRow("select vip from ".DB_PREFIX."member where user_id='$userid' limit 1");		
		$canshu=$cart_model->can();
		$vip=intval($mem['vip']);                                           
		
		$good_ship=array();
		
		foreach($cart_items as $key=>$good)//循环购买商品
		{
			$goodsid=$good['goods_id'];
			$goo=$cart_model->getrow("select baoyou_type,baoyou_jian,baoyou_money,shipping_id,weight,volume,daishou from ".DB_PREFIX."goods where goods_id ='$goodsid' limit 1");
			$baoyou_type    =   $goo['baoyou_type']; 
			$baoyou_jian    =   $goo['baoyou_jian']; 
			$baoyou_money    =   $goo['baoyou_money'];
			$baoyou_jifen = $baoyou_money*$canshu['jifenxianjin']; 
			$shipping_id    =   $goo['shipping_id']; 	
			$quantity=$good['quantity'];
			$volume=$goo['volume'];
			$weight=$goo['weight'];
			$daishou=$goo['daishou'];
			if($daishou==3)//快采售出
			{
				 $subjifen=$quantity*$good['price']*$canshu['jifenxianjin'];
			}
			else
			{
				$subjifen=$quantity * $good['price']*$canshu['jifenxianjin']*(1+$canshu['lv21']); 
			}
			
				
			if($baoyou_type==0 || ($baoyou_type!=0 &&($quantity<$baoyou_jian || $subjifen <$baoyou_jifen)))//不包邮
			{
				foreach($shipping_methods as $key2=>$ships)
				{
					 
					if($shipping_id==$ships['shipping_id'])
					{	
						$typeid=$ships['typeid'];//1是件2是重量3是体积
						
						if($typeid==2 && !empty($weight))
						{
							 $quantity=$quantity*$weight;
						}
						if($typeid==3 && !empty($volume))
						{
							 $quantity=$quantity*$volume;
						}
					
						$cod_regions=unserialize($ships['cod_regions']);
						$regions_def=array_shift($cod_regions);//默认地区
						
						foreach($cod_regions as $key3=>$var)//除去默认地区
						{	
							$bb=explode(',',$var['areaid']);
							if(in_array($region_id,$bb))
							{
											
								if($quantity<=$var['one'])
								{
									$good_ship[$key]['fee_money']=$var['price'];
									
								}
								else
								{
									$good_ship[$key]['fee_money']=$var['price']+ceil(($quantity-$var['one'])/$var['next'])*$var['nprice'];
									
								}
												
							}
										
						}
								
						if(empty($good_ship[$key]))
						{
							//默认地区
							
								if($quantity<=$regions_def['one'])
								{
									$good_ship[$key]['fee_money']=$regions_def['price'];
								}
								else
								{
									$good_ship[$key]['fee_money']=$regions_def['price']+ceil(($quantity-$regions_def['one'])/$regions_def['next'])*$regions_def['nprice'];
											
								}		
						}	
				   }
			   }
			}
			else//几件包邮
			{
				$good_ship[$key]['fee_money']=0;	
			}
			
				$good_ship[$key]['fee_jifen']=$good_ship[$key]['fee_money']*$canshu['jifenxianjin'];
			
			$good_ship[$key]['goods_name']=iconv('gb2312','utf-8',$good['goods_name']);
			$good_ship[$key]['goods_id']=$good['goods_id'];
		}
		//echo 111;
		//print_r($quantity);
	
	//总运费
	foreach($good_ship as $key=>$var)
	{
		$zong_jifen+=$var['fee_jifen'];
		$zong_money+=$var['fee_money'];
	}
	
	//$good_ship['zong_jifen']=$zong_jifen;
	//$good_ship['zong_money']=$zong_money;
	//print_r($good_ship);
	$good_ship=ecm_json_encode($good_ship);
	//$good_ship=unserialize($good_ship);
	echo $good_ship;
	
	}
	
	
}
?>
