<?php

/**
 *    普通订单类型
 *
 *    @author    Garbin
 *    @usage    none
 */
class NormalOrder extends BaseOrder
{
    var $_name = 'normal';

    /**
     *    查看订单
     *
     *    @author    Garbin
     *    @param     int $order_id
     *    @param     array $order_info
     *    @return    array
     */
    function get_order_detail($order_id, $order_info)
    {
        if (!$order_id)
        {
            return array();
        }

        /* 获取商品列表 */
        $data['goods_list'] =   $this->_get_goods_list($order_id);

        /* 配关信息 */
        $data['order_extm'] =   $this->_get_order_extm($order_id);

        /* 支付方式信息 */
        if ($order_info['payment_id'])
        {
            $payment_model      =& m('payment');
            $payment_info       =  $payment_model->get("payment_id={$order_info['payment_id']}");
            $data['payment_info']   =   $payment_info;
        }

        /* 订单操作日志 */
        $data['order_logs'] =   $this->_get_order_logs($order_id);

        return array('data' => $data);
    }

    /* 显示订单表单 */
    function get_order_form($store_id)
    {
        $data = array();
        $template = 'order.form.html';

        $visitor =& env('visitor');

        /* 获取我的收货地址 */
        $data['my_address']         = $this->_get_my_address($visitor->get('user_id'));
        $data['addresses']          =   ecm_json_encode($data['my_address']);
        $data['regions']            = $this->_get_regions();

        /* 配送方式 */
        $data['shipping_methods']   = $this->_get_shipping_methods($store_id);
		
        if (empty($data['shipping_methods']))
        {
			//echo "该店铺还没有安装配送方式，请先选购其他店铺的商品";
           $this->_error('no_shipping_methods');
            return false;
			//return $data;
			
        }
		
        $data['shippings']=$data['shipping_methods'];
		//$data['shippings']          = ecm_json_encode($ship);
		
        foreach ($data['shipping_methods'] as $shipping)
        {
            $data['shipping_options'][$shipping['shipping_id']] = $shipping['shipping_name'];
        }

        return array('data' => $data, 'template' => $template);
    }

    /**
     *    提交生成订单，外部告诉我要下的单的商品类型及用户填写的表单数据以及商品数据，我生成好订单后返回订单ID
     *
     *    @author    Garbin
     *    @param     array $data
     *    @return    int
     */
    function submit_order($data)
    {
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
	// $row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
	
	
        /* 释放goods_info和post两个变量 */
        extract($data);
        /* 处理订单基本信息 */
        $base_info = $this->_handle_order_info($goods_info, $post);
	  

        if (!$base_info)
        {
            /* 基本信息验证不通过 */

            return 0;
        }
		$user_id=$base_info['buyer_id'];
		$mem=$this->_city_mod->getRow("select vip from ".DB_PREFIX."member where user_id='$user_id' limit 1");
        $vip=$mem['vip'];
		$stor=$this->_city_mod->getRow("select is_cai from ".DB_PREFIX."store where store_id='$user_id' limit 1");
		$canshu=$this->_city_mod->can();
		$jifenxianjin=$canshu['jifenxianjin'];
		$lv21=$canshu['lv21'];
		$lv31=$canshu['lv31'];
        /* 处理订单收货人信息 */
        $consignee_info = $this->_handle_consignee_info($goods_info, $post);
        if (!$consignee_info)
        {
            /* 收货人信息验证不通过 */
            return 0;
        }
		/*if($vip==1)
		{*/
		$jifen_fee=$consignee_info['shipping_fee']*$jifenxianjin;
		
		/*}
		else
		{
		$jifen_fee=$consignee_info['shipping_fee']*$jifenxianjin*(1+$lv31);
		}*/

        /* 至此说明订单的信息都是可靠的，可以开始入库了 */

        /* 插入订单基本信息 */
        //订单总实际总金额，可能还会在此减去折扣等费用
		if($base_info['extension']=="groupbuy")
		{
         $base_info['order_amount']  =   $base_info['goods_amount'];
		 $base_info['order_amount_m']  =   $base_info['goods_amount_m'];
		 $base_info['order_jifen']  =   $base_info['goods_jifen'];
		 $base_info['fanhuan_jia']  =   $base_info['order_amount']*$jifenxianjin;
        }
		else
		{
		 $base_info['order_amount']  =   $base_info['goods_amount'] + $consignee_info['shipping_fee'] - $base_info['discount']-$base_info['youhuidiscount'];
		 $base_info['order_amount_m']  =   $base_info['goods_amount_m'] + $consignee_info['shipping_fee'] - $base_info['discount']-$base_info['youhuidiscount'];
		  $base_info['order_jifen']  =   $base_info['goods_jifen'] + $jifen_fee - $base_info['discount_jifen']-$base_info['youhuidiscount_jifen'];
		  $base_info['fanhuan_jia']  =   $base_info['order_amount']*$jifenxianjin;
		}
        $order_model =& m('order');
		if($base_info['daishou']==2 && $stor['is_cai']!=1)
		{
			
				if($base_info['order_amount_m']<500)
				{
					$this->_error('caigoubudiyu');
					return false;
				}
			
		}
		
        $order_id    = $order_model->add($base_info);
		
        if (!$order_id)
        {
            /* 插入基本信息失败 */
            $this->_error('create_order_failed');

            return 0;
        }

        /* 插入收货人信息 */
        $consignee_info['order_id'] = $order_id;
        $order_extm_model =& m('orderextm');
        $order_extm_model->add($consignee_info);

        /* 插入商品信息 */
        $goods_items = array();
        foreach ($goods_info['items'] as $key => $value)
        {
			
			$jifen=$value['price']*$jifenxianjin*(1+$lv21);
			$pricem=m_21($value['price']);
			if($value['daishou']==3)
			{
				$jifen=$value['price']*$jifenxianjin;
				$pricem=$value['price'];
			}
			
            $goods_items[] = array(
                'order_id'      =>  $order_id,
                'goods_id'      =>  $value['goods_id'],
                'goods_name'    =>  $value['goods_name'],
                'spec_id'       =>  $value['spec_id'],
                'specification' =>  $value['specification'],
                'price'         =>  $value['price'],
				'price_m'         =>$pricem  ,
				'jifen'         =>  $jifen,
                'quantity'      =>  $value['quantity'],
                'goods_image'   =>  $value['goods_image'],
				'gh_id'      =>  $value['gh_id'],
				'ordercity'      =>  $city_id
            );
        }
	
        $order_goods_model =& m('ordergoods');
        $order_goods_model->add(addslashes_deep($goods_items)); //防止二次注入

        return $order_id;
    }
}

?>