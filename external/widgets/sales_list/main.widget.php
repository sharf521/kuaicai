<?php

/**
 * 销售排行前十挂件
 *
 * @return  array   $goods_list
 */
class Sales_listWidget extends BaseWidget
{
    var $_name = 'sales_list';
    //var $_ttl  = 86400;
	var $_ttl  = 1;

    function _get_data()
    {
	//$url=$_SERVER['HTTP_HOST'];
		/*echo $url;*/
	   $this->_city_mod =& m('city');
	 //$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
	
        $cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if($data === false)
        {
            $goods_mod =& m('goods');
            $data = $goods_mod->find(array(
                'conditions' => 'if_show = 1 AND closed = 0 and cityhao='.$city_id,
                'order' => 'sales',
                'fields' => 'g.goods_id, g.goods_name',
                'join' => 'has_goodsstatistics',
                'limit' => 10,
            ));
            $cache_server->set($key, $data, $this->_ttl);
        }

        return $data;
    }
}

?>