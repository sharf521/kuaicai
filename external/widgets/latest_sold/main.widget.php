<?php

/**
 * 最新成交挂件
 *
 * @param   int     $num    数量
 * @return  array   $data
 */
class Latest_soldWidget extends BaseWidget
{
    var $_name = 'latest_sold';
    var $_ttl  = 1;

    function _get_data()
    {
	
	
	//$url=$_SERVER['HTTP_HOST'];
	/*	echo $url;*/
	   $this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	    $cityrow=$this->_city_mod->get_cityrow();
		$kaiguan=$this->_city_mod->kg();
		
		$this->assign('kaiguan',$kaiguan);
		$city_id=$cityrow['city_id'];
        if (empty($this->options['num']) || intval($this->options['num']) <= 0)
        {
            $this->options['num'] = 4;
        }

        $cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if($data === false)
        {
            $order_goods_mod =& m('ordergoods');
            $data = $order_goods_mod->find(array(
                'conditions' => "status = '" . ORDER_FINISHED . "' and ordercity=".$city_id,
                'order' => 'finished_time desc',
                'fields' => 'goods_id, goods_name, price_m,jifen, goods_image,order_alias.zhifufangshi,price ',
                'join' => 'belongs_to_order',
                'limit' => $this->options['num'],
            ));
			
            foreach ($data as $key => $goods)
            {
                empty($goods['goods_image']) && $data[$key]['goods_image'] = Conf::get('default_goods_image');
            }
            $cache_server->set($key, $data, $this->_ttl);
        }

        return $data;
    }
}

?>