<?php

/**
 * 4个图片广告挂件
 *
 * @param   string  $ad_image_url   广告图片地址1-4
 * @param   string  $ad_link_url    广告链接地址1-4
 * @return  array
 */
class Four_image_adsWidget extends BaseWidget
{
    var $_name = 'four_image_ads';

    function _get_data()
    {
	
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
	// $row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
	 $this->adv_mod =& m('adv');
        $data = $this->adv_mod->find(array(
            //'join' => 'be_join,belong_goods',
            'fields' => '*',
            'conditions' => "adv_city= '$city_id' and type='4个图片'",
            'order' => 'bianhao DESC',
			'limit' => '4',
			
           
        ));
   
           $this->assign('data', $data);
	

        return array(
            'ad1_image_url'  => $this->options['ad1_image_url'],
            'ad1_link_url'   => $this->options['ad1_link_url'],
            'ad2_image_url'  => $this->options['ad2_image_url'],
            'ad2_link_url'   => $this->options['ad2_link_url'],
            'ad3_image_url'  => $this->options['ad3_image_url'],
            'ad3_link_url'   => $this->options['ad3_link_url'],
            'ad4_image_url'  => $this->options['ad4_image_url'],
            'ad4_link_url'   => $this->options['ad4_link_url'],
        );
    }

    function parse_config($input)
    {
        $images = $this->_upload_image();
        if ($images)
        {
            foreach ($images as $key => $image)
            {
                $input['ad' . $key . '_image_url'] = $image;
            }
        }

        return $input;
    }

    function _upload_image()
    {
        import('uploader.lib');
        $images = array();
        for ($i = 1; $i <= 4; $i++)
        {
            $file = $_FILES['ad' . $i . '_image_file'];
            if ($file['error'] == UPLOAD_ERR_OK)
            {
                $uploader = new Uploader();
                $uploader->allowed_type(IMAGE_FILE_TYPE);
                $uploader->addFile($file);
                $uploader->root_dir(ROOT_PATH);
                $images[$i] = $uploader->save('data/files/mall/template', $uploader->random_filename());
            }
        }

        return $images;
    }
}

?>