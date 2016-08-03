<?php

/**
 * 广告挂件
 *
 */
class AdvtWidget extends BaseWidget
{
    var $_name = 'advt';
   

    function _get_data()
    {
	
	$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
	 //$row_city=$this->_city_mod->getrow("select city_id from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
	/* $this->adv_mod =& m('adv');
        $data = $this->adv_mod->find(array(
            'limit' => '2',
            'fields' => '*',
            'conditions' => "adv_city= '$city_id' and type='图片广告'",
            'order' => 'bianhao DESC',
           
        ));
   
           $this->assign('data', $data);
*/
$this->adv_mod=& m('adv');
	$time=date('Y-m-d H:i:s');
	$adv_list8=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='8' and start_time<='$time' and end_time>='time' order by riqi desc limit 1
	   ");
	   $this->assign('adv_list8', $adv_list8);
	
	
        return array(
            'ad_image_url'  => $this->options['ad_image_url'],
            'ad_link_url'   => $this->options['ad_link_url'],
        );

        $this->options = stripslashes_deep($this->options);
        $today = local_date('Y-m-d');
        $this->options['is_valid'] = (empty($this->options['start_date']) || $this->options['start_date'] <= $today) && 
            (empty($this->options['end_date']) || $this->options['end_date'] >= $today);
			$url=$_SERVER['HTTP_HOST'];
			//echo $url;


        return $this->options;
    }
    
    function get_config_datasrc()
    {
        $this->options = stripslashes_deep($this->options);
        $this->assign('options', $this->options);
    }

    function parse_config($input)
    {
        $result = array();
		 $city = $result['city'] = $input['city'];
		 if($city!='')
		 {
        $result['city'] = $input['city'];
		}
        if (!empty($input['start_date']))
        {
            $start_date = strtotime($input['start_date']);
            if ($start_date)
            {
                $result['start_date'] = date('Y-m-d', $start_date);
            }
        }
        if (!empty($input['end_date']))
        {
            $end_date = strtotime($input['end_date']);
            if ($end_date)
            {
                $result['end_date'] = date('Y-m-d', $end_date);
            }
        }
        $style = $result['style'] = $input['style'];
        if ($style == 'code')
        {
            $result['html'] = $input['html'];
        }
        elseif ($style == 'text')
        {
            $result['title'] = $input['title'];
            $result['link1'] = $input['link1'];
            $result['size']  = $input['size'];
        }
        elseif ($style == 'image')
        {
            $result['url1']   = $input['url1'];
            $result['link2']  = $input['link2'];
            $result['width1'] = $input['width1'];
            $result['height1']= $input['height1'];
            $result['alt']    = $input['alt'];
        }
        elseif ($style == 'flash')
        {
            $result['url2']   = $input['url2'];
            $result['width2'] = $input['width2'];
            $result['height2']= $input['height2'];
        }
        return $result;
    }
}

?>