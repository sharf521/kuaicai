<?php

/**
 * 公告栏挂件
 *
 * @param   string  $ad_image_url   广告图片地址
 * @param   string  $ad_link_url    广告链接地址
 * @return  array
 */
class Gcategory_navWidget extends BaseWidget
{
    var $_name = 'gcategory_nav';
    var $_ttl  = 0;

    function _get_data()
    {
        $cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if($data === false)
        {
            $gcategory_mod =& bm('gcategory');
            $data = $gcategory_mod->get_list(0, true,1);
            foreach ( $data as $key => $val)
            {
                $children = $gcategory_mod->get_list($val['cate_id'], true,1);
                foreach ( $children as $k => $value)
                {
                    $third_children = $gcategory_mod->get_list($value['cate_id'], true,1);
                    $children[$k]['children'] = $third_children;
                    unset($third_children);
                }
                $data[$key]['children'] = $children;
                unset($children);
            }
            $cache_server->set($key, $data, $this->_ttl);
        }

        return $data;
    }
}

?>