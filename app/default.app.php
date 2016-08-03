<?php

class DefaultApp extends MallbaseApp
{
    function index()
    {
//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	  $this->_city_mod =& m('city');
	  $this->adv_mod =& m('adv');
	  $tplurl='/themes/mall/default/styles/default';
	  $this->assign('tplurl',$tplurl);
	// $city_row=$this->_city_mod->getrow("select city_id from ".DB_PREFIX."city where city_yuming = '$url'");
	 //$city_id=$city_row['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$kaiguan=$this->_city_mod->kg();
    $this->assign('kaiguan',$kaiguan);
		$city_id=$cityrow['city_id'];
	$recom20=$this->_city_mod->getRow("select recom_name from ".DB_PREFIX."recommend where recity = '$city_id' and recom_id=20 limit 1");
	$recom21=$this->_city_mod->getRow("select recom_name from ".DB_PREFIX."recommend where recity = '$city_id' and recom_id=21 limit 1");
	$recom22=$this->_city_mod->getRow("select recom_name from ".DB_PREFIX."recommend where recity = '$city_id' and recom_id=22 limit 1");
	$recom23=$this->_city_mod->getRow("select recom_name from ".DB_PREFIX."recommend where recity = '$city_id' and recom_id=23 limit 1");
	$recom24=$this->_city_mod->getRow("select recom_name from ".DB_PREFIX."recommend where recity = '$city_id' and recom_id=24 limit 1");	
	
        $this->assign('index', 1); // 标识当前页面是首页，用于设置导航状态
        $this->assign('icp_number', Conf::get('icp_number'));

$this->assign('recom24', $recom24);
$this->assign('recom23', $recom23);
$this->assign('recom22', $recom22);
$this->assign('recom21', $recom21);
$this->assign('recom20', $recom20);

        /* 热门搜素 */
        $this->assign('hot_keywords', $this->_get_hot_keywords());
		$this->assign('isdefault',1);
		$recom_mod =& m('recommend');
		$time=date('Y-m-d H:i:s');
		//横幅广告
		 $adv_list7=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='7' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $this->assign('adv_list7', $adv_list7);
		
		$adv_list8=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='8' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $this->assign('adv_list8', $adv_list8);
	   
	   
	   
	   $adv_list12=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='12' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $this->assign('adv_list12', $adv_list12);
	   
	   $adv_list13=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='13' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $this->assign('adv_list13', $adv_list13);
	   

		/*秋季新款*/
	   $adv_list2=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='2' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
		$img_goods_list1 = $recom_mod->get_recommended_goods(23, 5, true);
		$txt_goods_list1 = $recom_mod->get_recommended_goods(23, 8, true);
		//$adv_img1 = $this->adv_mod->adv_img(2, 1, true);
		foreach ($img_goods_list1 as $key1 => $good)
        {
			$img_goods_list1[$key1]['jifen_price']=round($good['jifen_price'],2); 
			$img_goods_list1[$key1]['vip_price']=round($good['vip_price'],2); 
			if($key1==0)
			{
			$img_goods1[$key1]=$img_goods_list1[$key1];
			$this->assign('img_goods1', $img_goods1); 
			}
			else
			{
			$img_goods2[$key1]=$img_goods_list1[$key1];
			$this->assign('img_goods2', $img_goods2); 
			}			
		}
		
		$this->assign('img_goods_list1', $img_goods_list1);
		$this->assign('txt_goods_list1', $txt_goods_list1);
		$this->assign('adv_list2', $adv_list2);
		
		
		/*新品上市*/
	   $adv_list3=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='3' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
		$img_goods_list2 = $recom_mod->get_recommended_goods(20, 14, true);
		$txt_goods_list2 = $recom_mod->get_recommended_goods(20, 8, true);
		foreach ($img_goods_list2 as $key1 => $good)
        {
			$img_goods_list2[$key1]['jifen_price']=round($good['jifen_price'],2); 
			$img_goods_list2[$key1]['vip_price']=round($good['vip_price'],2); 
		}
		$this->assign('img_goods_list2', $img_goods_list2);
		$this->assign('txt_goods_list2', $txt_goods_list2);
		$this->assign('adv_list3', $adv_list3);
		
		/*靓点饰品*/
		$adv_list4=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='4' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
		$img_goods_list3 = $recom_mod->get_recommended_goods(24, 5, true);
		$txt_goods_list3 = $recom_mod->get_recommended_goods(24, 8, true);
		foreach ($img_goods_list3 as $key1 => $good)
        {
			$img_goods_list3[$key1]['jifen_price']=round($good['jifen_price'],2); 
			$img_goods_list3[$key1]['vip_price']=round($good['vip_price'],2); 
			if($key1==0)
			{
			$img_goods3[$key1]=$img_goods_list3[$key1];
			$this->assign('img_goods3', $img_goods3); 
			}
			else
			{
			$img_goods4[$key1]=$img_goods_list3[$key1];
			$this->assign('img_goods4', $img_goods4); 
			}	
		}
		$this->assign('img_goods_list3', $img_goods_list3);
		$this->assign('txt_goods_list3', $txt_goods_list3);
		$this->assign('adv_list4', $adv_list4);
		
		/*精品推荐*/
		$adv_list5=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='5' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $this->assign('adv_list5', $adv_list5);
		$img_goods_list4 = $recom_mod->get_recommended_goods(21, 14, true);
		$txt_goods_list4 = $recom_mod->get_recommended_goods(21, 8, true);
		foreach ($img_goods_list4 as $key1 => $good)
        {
			$img_goods_list4[$key1]['jifen_price']=round($good['jifen_price'],2); 
			$img_goods_list4[$key1]['vip_price']=round($good['vip_price'],2); 
		}
		$this->assign('img_goods_list4', $img_goods_list4);
		$this->assign('txt_goods_list4', $txt_goods_list4);
		
		/*二手商品*/
		$adv_list6=$this->adv_mod->getRow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='6' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	    $this->assign('adv_list6', $adv_list6);
		$img_goods_list5 = $recom_mod->get_recommended_goods(22, 5, true);
		$txt_goods_list5 = $recom_mod->get_recommended_goods(22, 8, true);
	
	    foreach ($img_goods_list5 as $key1 => $good)
        {
			$img_goods_list5[$key1]['jifen_price']=round($good['jifen_price'],2); 
			$img_goods_list5[$key1]['vip_price']=round($good['vip_price'],2);
			if($key1==0)
			{
			$img_goods5[$key1]=$img_goods_list5[$key1];
			$this->assign('img_goods5', $img_goods5); 
			}
			else
			{
			$img_goods6[$key1]=$img_goods_list5[$key1];
			$this->assign('img_goods6', $img_goods6); 
			}	
			 
		}
		$this->assign('img_goods_list5', $img_goods_list5);
		$this->assign('txt_goods_list5', $txt_goods_list5);
		
		
		/*$img_goods_list1 = $recom_mod->get_recommended_goods(-100, 4, true, 272);
		$txt_goods_list1 = $recom_mod->get_recommended_goods(-100, 8, true, 272);
		$this->assign('img_goods_list1', $img_goods_list1);
		$this->assign('txt_goods_list1', $txt_goods_list1);
		
		
	*/
		
	//轮播广告		
	$time=date('Y-m-d H:i:s');
	 $this->adv_mod =& m('adv');
        $data = $this->adv_mod->find(array(
            //'join' => 'be_join,belong_goods',
            'fields' => '*',
             'conditions' => "adv_city= '$city_id' and type='1' and start_time<='$time' and end_time>='$time'",
            'order' => 'riqi DESC',
           
        ));
		//$this->_do_login(2890);
		foreach($data as $d)
		{
			$data1['link']=$d['lianjie'];
			$data1['img']=$d['image'];
			break;
		}

           $this->assign('data', $data);
		   $this->assign('data1', $data1);
		
		//公告
	        $acategory_mod =& m('acategory');
            $article_mod =& m('article');
            $news = $article_mod->find(array(
                'conditions'    => 'cate_id=' . $acategory_mod->get_ACC(ACC_NOTICE) . ' AND if_show = 1 and (city=1 or city ='.$city_id .')',
                'order'         => 'add_time DESC,sort_order ASC ',
                'fields'        => 'article_id, title, add_time',
                'limit'         => 6,
            ));
            
            $i=1;
			foreach ($news as $key=>$val)
			{
				
				if($i<2)
				{
					$i++;
					$news[$key]['title']=substr($val['title'],0,26);
					$news[$key]['diyi']=1;
				}
			}
		
		
		//明星店铺
		$star=$this->adv_mod->getAll(
	   "select st.*,m.user_name from ".DB_PREFIX."store st left join ".DB_PREFIX."member m on st.store_id=m.user_id where 
	   st.cityid='$city_id' and st.dengji=1 and st.state=1 order by st.add_time desc limit 8
	   ");
	  
	   $goods_mod =& m('goods');
	   foreach($star as $key=>$var)
	   {
	   		$star[$key]['goods_count'] = $goods_mod->get_count_of_store($var['store_id']);
	   }
 		//店铺推荐
	    $store_mod =& m('store');
            $rec_store = $store_mod->find(array(
			    'conditions' => 'state = 1 AND  recommended = 1 and cityid = '.$city_id,
               /* 'conditions' => "state = 1 AND recommended = 1",*/
                'order' => 'sort_order',
                'fields' => 'store_id, store_name, store_logo, praise_rate, user_name',
                'join' => 'belongs_to_user',
                'limit' => 2,
            ));
			//print_r($data);
            $goods_mod =& m('goods');
            foreach ($rec_store as $key => $store)
            {
                $rec_store[$key]['goods_count'] = $goods_mod->get_count_of_store($store['store_id']);
                empty($store['store_logo']) && $rec_store[$key]['store_logo'] = Conf::get('default_store_logo');
            }
			
	   //最新成交
	   $order_goods_mod =& m('ordergoods');
            $latestgood = $order_goods_mod->find(array(
                'conditions' => "status = '" . ORDER_FINISHED . "' and ordercity=".$city_id,
                'order' => 'finished_time desc',
                'fields' => 'goods_id, goods_name, price,jifen, goods_image',
                'join' => 'belongs_to_order',
                'limit' => 3,
            ));
            foreach ($latestgood as $key => $goods)
            {
                empty($goods['goods_image']) && $latestgood[$key]['goods_image'] = Conf::get('default_goods_image');
            }
			
			//品牌
			$brand_mod =& m('brand');
            $brand_rec = $brand_mod->find(array(
                'conditions' => 'recommended = 1 and city='.$city_id,
                'order' => 'brand_id desc',
                'limit' => 10,
            ));
		//友情链接
			$partner_mod =& m('partner');
            $parter = $partner_mod->find(array(
                'conditions' => "store_id = 0 and pcity='$city_id'",
                'order' => 'sort_order',
                'limit' => 10,
            ));
			//商品分类
			$gcategory_mod =& bm('gcategory');
            $fenlei = $gcategory_mod->get_list(0, true,1);
            foreach ( $fenlei as $key => $val)
            {
                $children = $gcategory_mod->get_list($val['cate_id'], true,1);
                foreach ( $children as $k => $value)
                {
                    $third_children = $gcategory_mod->get_list($value['cate_id'], true,1);
                    $children[$k]['children'] = $third_children;
                    unset($third_children);
                }
                $fenlei[$key]['children'] = $children;
                unset($children);
            }
			
			//首页收缩广告
		$adv_list13=$this->adv_mod->getrow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='13' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $this->assign('adv_list13', $adv_list13);
		
		//对联广告
		$adv_list14=$this->adv_mod->getrow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type='14' and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $this->assign('adv_list14', $adv_list14);
			
			
			
			
			
		$this->assign('fenlei',$fenlei);	
		$this->assign('parter',$parter);
		$this->assign('brand_rec',$brand_rec);
		$this->assign('rec_store',$rec_store);
		$this->assign('latestgood',$latestgood);
		$this->assign('star',$star);
		$this->assign('news',$news);
        $this->assign('page_title', Conf::get('site_title'));
        $this->assign('page_description', Conf::get('site_description'));
        $this->assign('page_keywords', Conf::get('site_keywords'));
	
		

        $this->display('index.html');
    }

    function _get_hot_keywords()
    {
        $keywords = explode(',', conf::get('hot_search'));
        return $keywords;
    }
	function head()
	{
	 
	 $this->display('header.html');
	 
	}
	
	
	
}

?>