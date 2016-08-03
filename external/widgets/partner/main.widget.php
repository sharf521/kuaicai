<?php

/**
 * ºÏ×÷»ï°é¹Ò¼þ
 *
 * @return  array
 */
class PartnerWidget extends BaseWidget
{
    var $_name = 'partner';
   // var $_ttl  = 86400;
   var $_ttl  = 1;

    function _get_data()
    {
	//$url=$_SERVER['HTTP_HOST'];
	/*	echo $url;*/
	   $this->_city_mod =& m('city');
	 //$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
        if (empty($this->options['num']) || intval($this->options['num']) <= 0)
        {
            $this->options['num'] = 10;
        }

        $cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if($data === false)
        {
            $partner_mod =& m('partner');
            $data = $partner_mod->find(array(
                'conditions' => "store_id = 0 and pcity='$city_id'",
                'order' => 'sort_order',
                'limit' => $this->options['num'],
            ));
            $cache_server->set($key, $data, $this->_ttl);
        }

        return $data;
    }
}

?>