<?php

/**
 * 图片广告挂件
 *
 * @param   string  $image_url  图片地址
 * @param   string  $link_url   链接地址
 * @param   int     $width      图片宽度
 * @param   int     $height     图片高度
 * @return  array   $options    设置
 */
class Image_adWidget extends BaseWidget
{
    var $_name = 'image_ad';

    function _get_data()
    {
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
	// $row_city=$this->_city_mod->getrow("select city_id from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
	$this->adv_mod=& m('adv');
	$time=date('Y-m-d H:i:s');
	/*$adv_list8=$this->adv_mod->getrow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='8' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");*/
	// $this->assign('adv_list8', $adv_list8);
	
	
       /* return array(
            'ad_image_url'  => $this->options['ad_image_url'],
            'ad_link_url'   => $this->options['ad_link_url'],
        );*/
    }

    function parse_config($input)
    {
        $image = $this->_upload_image();
        if ($image)
        {
            $input['ad_image_url'] = $image;
        }

        return $input;
    }

    function _upload_image()
    {
        import('uploader.lib');
        $file = $_FILES['ad_image_file'];
        if ($file['error'] == UPLOAD_ERR_OK)
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            $uploader->addFile($file);
            $uploader->root_dir(ROOT_PATH);
            return $uploader->save('data/files/mall/template', $uploader->random_filename());
        }

        return '';
    }
}

?>