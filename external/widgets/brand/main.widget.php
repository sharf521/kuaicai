<?php

/**
 * Ʒ�ƹҼ�
 *
 * @return  array
 */
class BrandWidget extends BaseWidget
{
    var $_name = 'brand';
   // var $_ttl  = 86400;
   var $_ttl  = 1;
    var $_num  = 10;

    function _get_data()
    {
	
	//$url=$_SERVER['HTTP_HOST'];
	/*	echo $url;*/
	   $this->_city_mod =& m('city');
	// $row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
	//echo $city_id;
        $cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if($data === false)
        {
            $brand_mod =& m('brand');
            $data = $brand_mod->find(array(
                'conditions' => 'recommended = 1 and city='.$city_id,
                'order' => 'brand_id desc',
                'limit' => $this->_num,
            ));
            $cache_server->set($key, $data, $this->_ttl);
        }

        return $data;
    }
}

?>