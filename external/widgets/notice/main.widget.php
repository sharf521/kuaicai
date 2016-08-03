<?php

/**
 * 公告栏挂件
 *
 * @param   string  $ad_image_url   广告图片地址
 * @param   string  $ad_link_url    广告链接地址
 * @return  array
 */
class NoticeWidget extends BaseWidget
{
    var $_name = 'notice';
  //  var $_ttl  = 86400;
    var $_ttl  = 1;
    var $_num  = 8;

    function _get_data()
    {
	
	//$url=$_SERVER['HTTP_HOST'];
	/*echo $url;*/
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
            $acategory_mod =& m('acategory');
            $article_mod =& m('article');
            $data = $article_mod->find(array(
                'conditions'    => 'cate_id=' . $acategory_mod->get_ACC(ACC_NOTICE) . ' AND if_show = 1 and (city=1 or city ='.$city_id .')',
                'order'         => 'add_time DESC,sort_order ASC ',
                'fields'        => 'article_id, title, add_time',
                'limit'         => 8,
            ));
            $cache_server->set($key, $data, $this->_ttl);
        }

            $i=1;
			
			foreach ($data as $key=>$val)
			{
				
				if($i<2)
				{
					$i++;
					$data[$key]['title']=substr($val['title'],0,26);
					$data[$key]['diyi']=1;
				}
			}
			


        return array(
            'notices'       => $data,
            'ad_image_url'  => $this->options['ad_image_url'],
            'ad_link_url'   => $this->options['ad_link_url'],
        );
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