<?php

/**
 *    购物车控制器，负责会员购物车的管理工作，她与下一步售货员的接口是：购物车告诉售货员，我要买的商品是我购物车内的商品
 *
 *    @author    Garbin
 */

class CartApp extends MallbaseApp
{
    /**
     *    列出购物车中的商品
     *
     *    @author    Garbin
     *    @return    void
     */
    function index()
    {
        $store_id = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
        $carts = $this->_get_carts1($store_id);
		
		//print_r($carts);
		
		$this->kaiguan_mod=& m('kaiguan');
		$kaiguan=$this->kaiguan_mod->kg();
		$this->assign('kaiguan',$kaiguan);
		/* foreach ($carts as  $cart)
        {
            
		 $carts['am_price']=round($cart['am_price'],2); 
		
        }*/
        $this->_curlocal(
            LANG::get('cart')
        );
        $this->assign('page_title', Lang::get('confirm_goods') . ' - ' . Conf::get('site_title'));

        if (empty($carts))
        {
            $this->_cart_empty();

            return;
        }

        $this->assign('carts', $carts);
        $this->display('cart.index.html');
    }

    /**
     *    放入商品(根据不同的请求方式给出不同的返回结果)
     *
     *    @author    Garbin
     *    @return    void
     */
    function add()
    {
        $spec_id   = isset($_GET['spec_id']) ? intval($_GET['spec_id']) : 0;
        $quantity   = isset($_GET['quantity']) ? intval($_GET['quantity']) : 0;
        if (!$spec_id || !$quantity)
        {
            return;
        }

        /* 是否有商品 */
        $spec_model =& m('goodsspec');
        $spec_info  =  $spec_model->get(array(
            'fields'        => 'g.store_id, g.goods_id, g.goods_name, g.spec_name_1, g.spec_name_2, g.default_image,g.gong_id,g.ger_id, gs.spec_1, gs.spec_2, gs.stock, gs.price,gs.jifen_price,gs.vip_price,gs.price_m,g.daishou',
            'conditions'    => $spec_id,
            'join'          => 'belongs_to_goods',
        ));
				$userid=$this->visitor->get('user_id');
		
		if(empty($userid))
		{
			$this->json_error('qingdenglu');
			return;
		}
				
		if($spec_info['daishou']==2 && empty($userid))
		{
			$this->json_error('dengluhoucai');
            /* 商品不存在 */
            return;
		}
	$this->_store_id  = intval($this->visitor->get('manage_store'));
	if($spec_info['daishou']==2 && empty($this->_store_id))
		{
			$this->json_error('kaidianhoucai');
            /* 商品不存在 */
            return;
		}
		$stor=$spec_model->getRow("select is_cai from ".DB_PREFIX."store where store_id='$userid' limit 1");
		if($stor['is_cai']!=1 && $spec_info['daishou']==2)
		{
			$this->json_error('ninmeiyoucaigou');
            return;
		}
		
        if (!$spec_info)
        {
            $this->json_error('no_such_goods');
            /* 商品不存在 */
            return;
        }

        /* 如果是自己店铺的商品，则不能购买 */
        if ($this->visitor->get('manage_store'))
        {
            if ($spec_info['store_id'] == $this->visitor->get('manage_store'))
            {
                $this->json_error('can_not_buy_yourself');

                return;
            }
        }

        /* 是否添加过 */
        $model_cart =& m('cart');
       // $item_info  = $model_cart->get("spec_id={$spec_id} AND session_id='" . SESS_ID . "'");
		//$item_info=$model_cart->getAll("select daishou,spec_id from ".DB_PREFIX."cart where session_id='".SESS_ID."'");
		$item_info=$model_cart->getAll("select daishou,spec_id from ".DB_PREFIX."cart where user_id='$userid'");
		if(!empty($item_info))
		{
			foreach($item_info as $v)
			{
				if($v['spec_id']==$spec_id)
				{
					 $this->json_error('goods_already_in_cart');
					 return;	
				}
				if($v['daishou']!=$spec_info['daishou'] && ($v['daishou']==2 || $spec_info['daishou']==2))
				{
					$this->json_error('bunengtongshi');
					return;
				}
			}
		}
        if ($quantity > $spec_info['stock'])
        {
            $this->json_error('no_enough_goods');
            return;
        }
        $spec_1 = $spec_info['spec_name_1'] ? $spec_info['spec_name_1'] . ':' . $spec_info['spec_1'] : $spec_info['spec_1'];
        $spec_2 = $spec_info['spec_name_2'] ? $spec_info['spec_name_2'] . ':' . $spec_info['spec_2'] : $spec_info['spec_2'];

        $specification = $spec_1 . ' ' . $spec_2;
		$riqi=date('Y-m-d H:i:s');
        /* 将商品加入购物车 */
        $cart_item = array(
            'user_id'       => $this->visitor->get('user_id'),
            'session_id'    => SESS_ID,
            'store_id'      => $spec_info['store_id'],
            'spec_id'       => $spec_id,
            'goods_id'      => $spec_info['goods_id'],
            'goods_name'    => addslashes($spec_info['goods_name']),
            'specification' => addslashes(trim($specification)),
            'price'         => $spec_info['price'],
			'price_m'         => $spec_info['price_m'],
			'jifen_price'   => $spec_info['jifen_price'],
			'vip_price'     => $spec_info['vip_price'],
            'quantity'      => $quantity,
            'goods_image'   => addslashes($spec_info['default_image']),
			'gh_id'        => $spec_info['gong_id'],
			'ger_id'        => $spec_info['ger_id'],
			'daishou'        => $spec_info['daishou'],
			'riqi'         =>$riqi 
        );

        /* 添加并返回购物车统计即可 */
        $cart_model =&  m('cart');
        $cart_model->add($cart_item);
        $cart_status = $this->_get_cart_status();
		

        /* 更新被添加进购物车的次数 */
        $model_goodsstatistics =& m('goodsstatistics');
        $model_goodsstatistics->edit($spec_info['goods_id'], 'carts=carts+1');

        $this->json_result(array(
            'cart'      =>  $cart_status['status'],  //返回购物车状态
        ), 'addto_cart_successed');
    }

    /**
     *    丢弃商品
     *
     *    @author    Garbin
     *    @return    void
     */
    function drop()
    {
        /* 传入rec_id，删除并返回购物车统计即可 */
        $rec_id = isset($_GET['rec_id']) ? intval($_GET['rec_id']) : 0;
        if (!$rec_id)
        {
            return;
        }

        /* 从购物车中删除 */
        $model_cart =& m('cart');
        $droped_rows = $model_cart->drop('rec_id=' . $rec_id . ' AND session_id=\'' . SESS_ID . '\'', 'store_id');
        if (!$droped_rows)
        {
            return;
        }

        /* 返回结果 */
        $dropped_data = $model_cart->getDroppedData();
        $store_id     = $dropped_data[$rec_id]['store_id'];
        $cart_status = $this->_get_cart_status();
        $this->json_result(array(
            'cart'  =>  $cart_status['status'],                      //返回总的购物车状态
            'amount'=>  $cart_status['carts'][$store_id]['amount']   //返回指定店铺的购物车状态
        ),'drop_item_successed');
    }

    /**
     *    更新购物车中商品的数量，以商品为单位，AJAX更新
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function update()
    {
	    $user_id=$this->visitor->get('user_id');
        $spec_id  = isset($_GET['spec_id']) ? intval($_GET['spec_id']) : 0;
        $quantity = isset($_GET['quantity'])? intval($_GET['quantity']): 0;
        if (!$spec_id || !$quantity)
        {
            /* 不合法的请求 */
            return;
        }

        /* 判断库存是否足够 */
        $model_spec =& m('goodsspec');
		$memb=$model_spec->getRow("select vip from ".DB_PREFIX."member where user_id = '$user_id' limit 1");
		$vip=$memb['vip'];
		$canshu=$model_spec->can();
		$jifenxianjin=$canshu['jifenxianjin'];
		$lv21=$canshu['lv21'];
		$lv31=$canshu['lv31'];
        $spec_info  =  $model_spec->get($spec_id);
	
        if (empty($spec_info))
        {
            /* 没有该规格 */
            $this->json_error('no_such_spec');
            return;
        }

        if ($quantity > $spec_info['stock'])
        {
            /* 数量有限 */
            $this->json_error('no_enough_goods');
            return;
        }

        /* 修改数量 */
        $where = "spec_id={$spec_id} AND session_id='" . SESS_ID . "'";
        $model_cart =& m('cart');

        /* 获取购物车中的信息，用于获取价格并计算小计 */
        $cart_spec_info = $model_cart->get($where);
		
        if (empty($cart_spec_info))
        {
            /* 并没有添加该商品到购物车 */
            return;
        }

        $store_id = $cart_spec_info['store_id'];

        /* 修改数量 */
        $model_cart->edit($where, array(
            'quantity'  =>  $quantity,
        ));
 
        /* 小计 */
        $subtotal   =   $quantity * $cart_spec_info['price'];	
		$subtotal_m   =   m_21($subtotal);
		
        $subjifen=$subtotal*$jifenxianjin;
		$subjifen=$subjifen*(1+$lv21);
		if($cart_spec_info['daishou']==3)
		{
			$subtotal_m   =  $subtotal;
        	$subjifen=$subtotal*$jifenxianjin;
		}
		
	
        /* 返回JSON结果 */
        $cart_status = $this->_get_cart_status();
		$arr=array(
            'cart'      =>  $cart_status['status'],                     //返回总的购物车状态
            'subtotal'  =>  $subtotal, 
			'subtotal_m'  =>  $subtotal_m, 
			'subjifen'  =>  $subjifen, 
            'amount'    =>  $cart_status['carts'][$store_id]['amount'],  //店铺购物车总计
			'amount_m'    =>  $cart_status['carts'][$store_id]['amount_m'],  //店铺购物车总计
			'am_jifen'    =>  $cart_status['carts'][$store_id]['am_jifen']//小计
        );
		
		
		
		
		
        $this->json_result($arr, 'update_item_successed');
    }

    /**
     *    获取购物车状态
     *
     *    @author    Garbin
     *    @return    array
     */
    function _get_cart_status()
    {
        /* 默认的返回格式 */
        $data = array(
            'status'    =>  array(
                'quantity'  =>  0,      //总数量
                'amount'    =>  0,      //总金额
				'amount_m'    =>  0,      //总金额
                'kinds'     =>  0,      //总种类
				'am_jifen'    =>  0,   
            ),
            'carts'     =>  array(),    //购物车列表，包含每个购物车的状态
        );

        /* 获取所有购物车 */
        $carts = $this->_get_carts();
		
        if (empty($carts))
        {
            return $data;
        }
        $data['carts']  =   $carts;
		
        foreach ($carts as $store_id => $cart)
        {
            $data['status']['quantity'] += $cart['quantity'];
            $data['status']['amount']   += $cart['amount'];
			
			
            $data['status']['kinds']    += $cart['kinds'];
			$data['status']['am_jifen']   += $cart['am_jifen'];
			$data['status']['amount_m']   += $cart['amount_m'];
			
        }

        return $data;
    }

    /**
     *    购物车为空
     *
     *    @author    Garbin
     *    @return    void
     */
    function _cart_empty()
    {
        $this->display('cart.empty.html');
    }

    /**
     *    以购物车为单位获取购物车列表及商品项
     *
     *    @author    Garbin
     *    @return    void
     */
    function _get_carts($store_id = 0)
    {
        $carts = array();

        /* 获取所有购物车中的内容 */
        $where_store_id = $store_id ? ' AND cart.store_id=' . $store_id : '';

        /* 只有是自己购物车的项目才能购买 */
        $where_user_id = $this->visitor->get('user_id') ? " AND cart.user_id=" . $this->visitor->get('user_id') : '';
        $cart_model =& m('cart');
        $cart_items = $cart_model->find(array(
            'conditions'    => 'session_id = \'' . SESS_ID . "'" . $where_store_id . $where_user_id,
            'fields'        => 'this.*,store.store_name',
            'join'          => 'belongs_to_store',
        ));
        if (empty($cart_items))
        {
            return $carts;
        }
		$user_id=$this->visitor->get('user_id');
$memb=$cart_model->getRow("select vip from ".DB_PREFIX."member where user_id = '$user_id' limit 1");
		$vip=$memb['vip'];
$canshu=$cart_model->can();
$jifenxianjin=$canshu['jifenxianjin'];
$lv31=$canshu['lv31'];
$lv21=$canshu['lv21'];
		
		
        $kinds = array();
        foreach ($cart_items as $item)
        {
            /* 小计 */
            $item['subtotal']   = $item['price'] * $item['quantity'];
			$item['subtotal_m']   = m_21($item['subtotal'] );
			
			$item['subjifen']   =round($item['subtotal']*$jifenxianjin*(1+$lv21),5);
			if($item['daishou']==3)
			{
				$item['subtotal_m']   =$item['subtotal'];
				$item['subjifen']   =round($item['subtotal']*$jifenxianjin,5);
			}
            $kinds[$item['store_id']][$item['goods_id']] = 1;

            /* 以店铺ID为索引 */
            empty($item['goods_image']) && $item['goods_image'] = Conf::get('default_goods_image');
            $carts[$item['store_id']]['store_name'] = $item['store_name'];
            $carts[$item['store_id']]['amount']     += $item['subtotal'];   //各店铺的总金额
			$carts[$item['store_id']]['amount_m']     += $item['subtotal_m'];   //各店铺的总金额
			$carts[$item['store_id']]['am_jifen']     += $item['subjifen']; 
            $carts[$item['store_id']]['quantity']   += $item['quantity'];   //各店铺的总数量
            $carts[$item['store_id']]['goods'][]    = $item;
        }

        foreach ($carts as $_store_id => $cart)
        {
            $carts[$_store_id]['kinds'] =   count(array_keys($kinds[$_store_id]));  //各店铺的商品种类数
        }

        return $carts;
		
    }
	 function _get_carts1($store_id = 0)
    {
        $carts = array();

        /* 获取所有购物车中的内容 */
        $where_store_id = $store_id ? ' AND cart.store_id=' . $store_id : '';

        /* 只有是自己购物车的项目才能购买 */
        $where_user_id = $this->visitor->get('user_id') ? " AND cart.user_id=" . $this->visitor->get('user_id') : '';
        $cart_model =& m('cart');
        $cart_items = $cart_model->find(array(
            'conditions'    => 'session_id = \'' . SESS_ID . "'" . $where_store_id . $where_user_id,
            'fields'        => 'this.*,store.store_name',
            'join'          => 'belongs_to_store',
        ));
$user_id=$this->visitor->get('user_id');
$memb=$cart_model->getRow("select vip from ".DB_PREFIX."member where user_id = '$user_id' limit 1");
		$vip=$memb['vip'];
		$memb=$this->assign('memb',$memb);
$canshu=$cart_model->can();
$jifenxianjin=$canshu['jifenxianjin'];
$lv31=$canshu['lv31'];
$lv21=$canshu['lv21'];
        if (empty($cart_items))
        {
            return $carts;
        }
        $kinds = array();
        foreach ($cart_items as $item)
        {
            /* 小计 */
            $item['subtotal']   = $item['price'] * $item['quantity'];
			$item['subtotal_m']   = m_21($item['subtotal']);
			$item['subjifen']   =$item['subtotal']*$jifenxianjin*(1+$lv21);
			if($item['daishou']==3)
			{
				$item['subtotal_m']   =$item['subtotal'];
				$item['subjifen']   =$item['subtotal']*$jifenxianjin;
			}
            $kinds[$item['store_id']][$item['goods_id']] = 1;

            /* 以店铺ID为索引 */
            empty($item['goods_image']) && $item['goods_image'] = Conf::get('default_goods_image');
            $carts[$item['store_id']][$item['ger_id']]['store_name'] = $item['store_name'];
            $carts[$item['store_id']][$item['ger_id']]['amount']     += $item['subtotal'];   //各店铺的总金额
			$carts[$item['store_id']][$item['ger_id']]['amount_m']     += $item['subtotal_m'];   //各店铺的总金额(抬高价钱)
            $carts[$item['store_id']][$item['ger_id']]['quantity']   += $item['quantity'];   //各店铺的总数量
			$carts[$item['store_id']][$item['ger_id']]['am_jifen']     += $item['subjifen'];   //各店铺的总积分
            $carts[$item['store_id']][$item['ger_id']]['goods'][]    = $item;
			//$carts['jifen_price']=round($item['jifen_price'],2); 
			
        }

        /*foreach ($carts as $_store_id => $cart)
        {
            $carts[$_store_id][$item['ger_id']]['kinds'] =   count(array_keys($kinds[$_store_id]));  //各店铺的商品种类数
        }*/
		
		$arr_or=array();
		foreach($carts as $sid=>$cart)
		{
			foreach($cart as $i=>$v)
			{
				$v['ger_id']=$i;
				$v['store_id']=$sid;
				array_push($arr_or,$v);
			}
		}
		
		return $arr_or;
       // return $carts;
		
    }
	
	function xinlang()
	{
	$uid = empty($_GET['uid']) ? 0 : $_GET['uid'];	
	$userid = empty($_GET['userid']) ? 0 : $_GET['userid'];
	$this->member_mod=& m('member');
$mem=$this->member_mod->getRow("select * from ".DB_PREFIX."member where weiboid = '$uid' limit 1");


	if($userid)//绑定微博
	{
	$dd=array('weiboid'=>$uid);
	$wei=$this->member_mod->getRow("select * from ".DB_PREFIX."member where weiboid = '$uid' limit 1");
		if($wei)
		{
			$this->show_warning('yibang');
			return;
		}
		else
		{
			$this->member_mod->edit('user_id='.$userid,$dd);
		}
	$this->show_message('bangdingweibo','','index.php?app=member&act=profile');
	}
	else
	{
		if($mem)
		{
		ecm_setcookie('weiboid', "");
		$user_id=$mem['user_id'];
		$this->_do_login($user_id);
		header("location:index.php?app=member");
		}
		else
		{ 
		ecm_setcookie('weiboid', $uid);
		header("location:index.php?app=member&act=register");
				//$this->register($uid);
		}
	}
		
}
	
	function qq()
	{
	$openid = empty($_GET['openid']) ? 0 : $_GET['openid'];	
	$userid = empty($_GET['userid']) ? 0 : $_GET['userid'];
	
	$this->member_mod=& m('member');
	$mem=$this->member_mod->getRow("select * from ".DB_PREFIX."member where openid = '$openid' limit 1");
	

	if($userid)//绑定qq
	{
	$dd=array('openid'=>$openid);
		if($mem)
		{ 
			$this->show_warning('yibangguo');
			return;
		}
		else
		{
			$this->member_mod->edit('user_id='.$userid,$dd);
		}
	$this->show_message('bangding','','index.php?app=member&act=profile');
	}
	else
	{
		if($mem)
		{
		ecm_setcookie('openid', "");
		$user_id=$mem['user_id'];
		$this->_do_login($user_id);
		header("location:index.php?app=member");
		}
		else
		{ 
		ecm_setcookie('openid', $openid);
		header("location:index.php?app=member&act=register");
				//$this->register($uid);
		}
	}
		
	}
	
	
	
}

?>
