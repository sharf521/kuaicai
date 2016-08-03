<?php

/* 定义like语句转换为in语句的条件 */
define('MAX_ID_NUM_OF_IN', 10000); // IN语句的最大ID数
define('MAX_HIT_RATE', 0.05);      // 最大命中率（满足条件的记录数除以总记录数）
define('MAX_STAT_PRICE', 10000);   // 最大统计价格
define('PRICE_INTERVAL_NUM', 5);   // 价格区间个数
define('MIN_STAT_STEP', 50);       // 价格区间最小间隔
define('NUM_PER_PAGE', 16);        // 每页显示数量
define('ENABLE_SEARCH_CACHE', true); // 启用商品搜索缓存
//define('ENABLE_SEARCH_CACHE', false); // 启用商品搜索缓
define('SEARCH_CACHE_TTL', 3600);  // 商品搜索缓存时间

class SearchApp extends MallbaseApp
{
    /* 搜索商品 */
    function index()
    {
	
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	//$splb=$row_city['splb'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$splb=$cityrow['splb'];
	$kaiguan=$this->_city_mod->kg();
	$recomid=$_GET['recom_id'];
	
        // 查询参数
        $param = $this->_get_query_param();

        if (empty($param))
        {
            header('Location: index.php?app=category');
            exit;
        }

        /* 筛选条件 */
        $this->assign('filters', $this->_get_filter($param));

        /* 按分类、品牌、地区、价格区间统计商品数量 */
        $stats = $this->_get_group_by_info($param, ENABLE_SEARCH_CACHE,$recomid);
//print_r($stats);
	
        $this->assign('categories', $stats['by_category']);
        $this->assign('category_count', count($stats['by_category']));

        $this->assign('brands', $stats['by_brand']);
        $this->assign('brand_count', count($stats['by_brand']));

        $this->assign('price_intervals', $stats['by_price']);
		
		$this->assign('jfprice', $stats['by_jfprice']);
		$this->assign('vipprice', $stats['by_vipprice']);

        $this->assign('regions', $stats['by_region']);
        $this->assign('region_count', count($stats['by_region']));

        /* 排序 */
        $orders = $this->_get_orders();
        $this->assign('orders', $orders);

        /* 分页信息 */
        $page = $this->_get_page(NUM_PER_PAGE);
        $page['item_count'] = $stats['total_count'];
        $this->_format_page($page);
        $this->assign('page_info', $page);

        /* 商品列表 */
        $sgrade_mod =& m('sgrade');
        $sgrades    = $sgrade_mod->get_options();

        $conditions = $this->_get_goods_conditions($param,$recomid);


	/*if (!empty($recomid))
	{
		$conditions.=" and rg.rcity like '%$city_id,%'";
	}*/
	
        $goods_mod  =& m('goods');
		$orde=isset($_GET['order']) && isset($orders[$_GET['order']]) ? $_GET['order'] : '';
	
		if($orde=='')
		{
	     $goods_list=$goods_mod->getAll("SELECT *,g.goods_id, case g.cityhao 
					when '$city_id' then 10
					else 1
					end as c " .
                    "FROM " . DB_PREFIX . "goods AS g " .
                    "   LEFT JOIN " . DB_PREFIX . "store AS s ON s.store_id = g.store_id " .
					"   LEFT JOIN " . DB_PREFIX . "goods_statistics AS gss ON gss.goods_id = g.goods_id " .
					"   LEFT JOIN " . DB_PREFIX . "recommended_goods AS rg ON rg.goods_id = g.goods_id " .
                    "WHERE " . $conditions . 
                    "ORDER BY c desc " .
					"LIMIT {$page['limit']}"
					);	
		}
		else 
		{
		$goods_list=$goods_mod->getAll("SELECT *,g.goods_id case g.cityhao 
					when '$city_id' then 10
					else 1
					end as c " .
                    "FROM " . DB_PREFIX . "goods AS g " .
                    "   LEFT JOIN " . DB_PREFIX . "store AS s ON s.store_id = g.store_id " .
					"   LEFT JOIN " . DB_PREFIX . "goods_statistics AS gss ON gss.goods_id = g.goods_id " .
					"   LEFT JOIN " . DB_PREFIX . "recommended_goods AS rg ON rg.goods_id = g.goods_id " .
                    " WHERE " . $conditions . 
                    " ORDER BY ". $orde .
					" LIMIT {$page['limit']}"
					);	
		}
	

/*		 $goods_list = $goods_mod->get_list(array(
            'conditions' =>$conditions.' and g.cityhao='.$city_id,
            'order'      => isset($_GET['order']) && isset($orders[$_GET['order']]) ? $_GET['order'] : '	',
            'limit'      => $page['limit'],
        ));
*/	
		//修改开始
		
		/*if($splb==yes)
		{
		 $good = $goods_mod->get_list(array(
            'conditions' =>$conditions.' and cityhao!='.$city_id,
            'order'      => isset($_GET['order']) && isset($orders[$_GET['order']]) ? $_GET['order'] : '',
            'limit'      => $page['limit'],
        ));
		
}*/
		//修改结束
		foreach ($goods_list as $key => $goods)
        {
            $step = intval(Conf::get('upgrade_required'));
            $step < 1 && $step = 5;
            $store_mod =& m('store');
            $goods_list[$key]['credit_image'] = $this->_view->res_base . '/images/' . $store_mod->compute_credit($goods['credit_value'], $step);
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
            $goods_list[$key]['grade_name'] = $sgrades[$goods['sgrade']];
			$goods_list[$key]['jifen_price']=round($goods['jifen_price'],2);
			$goods_list[$key]['vip_price']=round($goods['vip_price'],2);
			
        }
        $this->assign('goods_list', $goods_list);
		$this->assign('good', $good);

        /* 商品展示方式 */
        $display_mode = ecm_getcookie('goodsDisplayMode');
        if (empty($display_mode) || !in_array($display_mode, array('list', 'squares')))
        {
            $display_mode = 'squares'; // 默认格子方式
        }
        $this->assign('display_mode', $display_mode);
		$this->assign('kaiguan', $kaiguan);

        /* 取得导航 */
        $this->assign('navs', $this->_get_navs());

        /* 当前位置 */
        $cate_id = isset($param['cate_id']) ? $param['cate_id'] : 0;
		$recom_id = isset($param['recom_id']) ? $param['recom_id'] : 0;
		$is_ershou = isset($param['is_ershou']) ? $param['is_ershou'] : 0;


		if($cate_id==0)
		{
		$this->_curlocal($this->get_goods_curl($recom_id,$is_ershou));
		}
		else
		{
		$this->_curlocal($this->_get_goods_curlocal($cate_id));
		}
		
        
		
        $this->assign('page_title', Conf::get('site_title'));
        $this->display('search.goods.html');
    }

    /* 搜索店铺 */
    function store()
    {
	
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
	 //$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
        /* 取得导航 */
        $this->assign('navs', $this->_get_navs());

        /* 取得该分类及子分类cate_id */
        $cate_id = empty($_GET['cate_id']) ? 0 : intval($_GET['cate_id']);
        $cate_ids=array();
        $condition_id='';
        if ($cate_id > 0)
        {
            $scategory_mod =& m('scategory');
            $cate_ids = $scategory_mod->get_descendant($cate_id);
        }

        /* 店铺分类检索条件 */
        $condition_id=implode(',',$cate_ids);
        $condition_id && $condition_id = ' AND cate_id IN(' . $condition_id . ')';

        /* 其他检索条件 */
        $conditions = $this->_get_query_conditions(array(
            array( //店铺名称
                'field' => 'store_name',
                'equal' => 'LIKE',
                'assoc' => 'AND',
                'name'  => 'keyword',
                'type'  => 'string',
            ),
            array( //地区名称
                'field' => 'region_name',
                'equal' => 'LIKE',
                'assoc' => 'AND',
                'name'  => 'region_name',
                'type'  => 'string',
            ),
            array( //地区id
                'field' => 'region_id',
                'equal' => '=',
                'assoc' => 'AND',
                'name'  => 'region_id',
                'type'  => 'string',
            ),
            array( //商家用户名
                'field' => 'user_name',
                'equal' => 'LIKE',
                'assoc' => 'AND',
                'name'  => 'user_name',
                'type'  => 'string',
            ),
		
        ));

        $model_store =& m('store');
        $regions = $model_store->list_regions();
        $page   =   $this->_get_page(10);   //获取分页信息
        $stores = $model_store->find(array(
            'conditions'  => 'cityid='.$city_id.' and state = ' . STORE_OPEN . $condition_id . $conditions,
            'limit'   =>$page['limit'],
            'order'   => empty($_GET['order']) || !in_array($_GET['order'], array('credit_value desc')) ? 'sort_order' : $_GET['order'],
            'join'    => 'belongs_to_user,has_scategory',

            'count'   => true   //允许统计
        ));

        $model_goods = &m('goods');

        foreach ($stores as $key => $store)
        {
            //店铺logo
            empty($store['store_logo']) && $stores[$key]['store_logo'] = Conf::get('default_store_logo');

            //商品数量
            $stores[$key]['goods_count'] = $model_goods->get_count_of_store($store['store_id']);

            //等级图片
            $step = intval(Conf::get('upgrade_required'));
            $step < 1 && $step = 5;
            $stores[$key]['credit_image'] = $this->_view->res_base . '/images/' . $model_store->compute_credit($store['credit_value'], $step);

        }
        $page['item_count']=$model_store->getCount();   //获取统计数据
        $this->_format_page($page);

        /* 当前位置 */
        $this->_curlocal($this->_get_store_curlocal($cate_id));
        $scategorys = $this->_list_scategory();
        $this->assign('stores', $stores);
        $this->assign('regions', $regions);
        $this->assign('cate_id', $cate_id);
        $this->assign('scategorys', $scategorys);
        $this->assign('page_info', $page);
        $this->assign('page_title', Conf::get('site_title'));
        $this->display('search.store.html');
    }

    function groupbuy()
    {
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
	 //$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
        empty($_GET['state']) &&  $_GET['state'] = 'on';
        $conditions = '1=1';
		$ri=time();

$canshu=$this->_city_mod->can();
$kaiguan=$this->_city_mod->kg();
$this->assign('kaiguan',$kaiguan);
        // 排序
        $orders = array(
            'group_id desc'          => Lang::get('select_pls'),
            'views desc'     => Lang::get('views'),
        );

        if ($_GET['state'] == 'on')
        {
            $orders['end_time asc'] = Lang::get('lefttime');
            $conditions .= ' AND gb.state ='. GROUP_ON .' AND gb.end_time>' . gmtime();
        }
        elseif ($_GET['state'] == 'end')
        {
            $conditions .= ' AND (gb.state=' . GROUP_ON . ' OR gb.state=' . GROUP_END . ') AND gb.end_time<=' . gmtime();
        }
        else
        {
            $conditions .= $this->_get_query_conditions(array(
                array(      //按团购状态搜索
                    'field' => 'gb.state',
                    'name'  => 'state',
                    'handler' => 'groupbuy_state_translator',
                )
            ));
        }
        $conditions .= $this->_get_query_conditions(array(
            array( //活动名称
                'field' => 'group_name',
                'equal' => 'LIKE',
                'assoc' => 'AND',
                'name'  => 'keyword',
                'type'  => 'string',
            ),
        ));
        $page = $this->_get_page(NUM_PER_PAGE);   //获取分页信息
        $groupbuy_mod = &m('groupbuy');
		$shenhe=Lang::get('shenhetongguo');
        $groupbuy_list = $groupbuy_mod->find(array(
		'conditions'    => $conditions." and status=1 and grcity='$city_id'",
		   /* 'conditions'    => 'grcity=0 and '.$conditions,*/  //修改之后的
            'fields'        => 'gb.group_name,gb.spec_price,gb.min_quantity,gb.store_id,gb.state,gb.end_time,gb.goods_id,g.default_image,default_spec,s.store_name',
            'join'          => 'belong_store, belong_goods',
            'limit'         => $page['limit'],
            'count'         => true,   //允许统计
            'order'         => isset($_GET['order']) && isset($orders[$_GET['order']]) ? $_GET['order'] : 'group_id desc',
        ));

        if ($ids = array_keys($groupbuy_list))
        {
            $quantity = $groupbuy_mod->get_join_quantity($ids);
        }
        foreach ($groupbuy_list as $key => $groupbuy)
        {
            $groupbuy_list[$key]['quantity'] = empty($quantity[$key]['quantity']) ? 0 : $quantity[$key]['quantity'];
            $groupbuy['default_image'] || $groupbuy_list[$key]['default_image'] = Conf::get('default_goods_image');
            $groupbuy['spec_price'] = unserialize($groupbuy['spec_price']);
            $groupbuy_list[$key]['group_price'] = $groupbuy['spec_price'][$groupbuy['default_spec']]['price'];
            $groupbuy['state'] == GROUP_ON && $groupbuy_list[$key]['lefttime'] = lefttime($groupbuy['end_time']);
			$sql="select * from ecm_order_goods og,ecm_order o,ecm_groupbuy g where g.goods_id=og.goods_id and og.order_id=o.order_id and g.goods_id='$groupbuy[goods_id]' and o.extension='groupbuy' and o.add_time>=g.start_time and o.add_time<=g.end_time";
			
			$grou=$groupbuy_mod->getAll($sql);
			$groupbuy_list[$key]['coun']=count($grou);
			$groupbuy_list[$key]['jifen_price'] = $groupbuy['spec_price'][$groupbuy['default_spec']]['price']*$canshu['jifenxianjin']*(1+$canshu['lv31']);
			$groupbuy_list[$key]['vip_price'] = $groupbuy['spec_price'][$groupbuy['default_spec']]['price']*$canshu['jifenxianjin']*(1+$canshu['lv21']);
        }
		
        $this->assign('state', array(
             'on' => Lang::get('group_on'),
             'end' => Lang::get('group_end'),
             'finished' => Lang::get('group_finished'),
             'canceled' => Lang::get('group_canceled'))
        );
        $this->assign('orders', $orders);
        // 当前位置
        $this->_curlocal(array(array('text' => Lang::get('groupbuy'))));
        $this->assign('page_title', Lang::get('groupbuy') . ' - ' . Conf::get('site_title'));
        $page['item_count'] = $groupbuy_mod->getCount();   //获取统计数据
        $this->_format_page($page);
        $this->assign('nav_groupbuy', 1); // 标识当前页面是团购列表，用于设置导航状态
        $this->assign('page_info', $page);
        $this->assign('groupbuy_list',$groupbuy_list);
        $this->assign('recommended_groupbuy', $this->_recommended_groupbuy(2));
        $this->assign('last_join_groupbuy', $this->_last_join_groupbuy(5));
        $this->display('search.groupbuy.html');
    }

    // 推荐团购活动
    function _recommended_groupbuy($_num)
    {
	
        $model_groupbuy =& m('groupbuy');
		$canshu=$model_groupbuy->can();
        $data = $model_groupbuy->find(array(
            'join'          => 'belong_goods',
			'conditions'    => 'gb.recommended=1 AND gb.state=' . GROUP_ON . ' AND gb.end_time>' . gmtime(),
            /*'conditions'    => 'gb.grcity=0 and gb.recommended=1 AND gb.state=' . GROUP_ON . ' AND gb.end_time>' . gmtime(),*/
            'fields'        => 'group_id, goods.default_image, group_name, end_time, spec_price,gb.grcity',
            'order'         => 'group_id DESC',
            'limit'         => $_num,
        ));
        foreach ($data as $gb_id => $gb_info)
        {
            $price = current(unserialize($gb_info['spec_price']));
            empty($gb_info['default_image']) && $data[$gb_id]['default_image'] = Conf::get('default_goods_image');
            $data[$gb_id]['lefttime']   = lefttime($gb_info['end_time']);
            $data[$gb_id]['price']      = $price['price'];
			$data[$gb_id]['jifen_price']=$price['price']*$canshu['jifenxianjin']*(1+$canshu['lv31']);
        }
        return $data;
    }

    // 最新参加的团购
    function _last_join_groupbuy($_num)
    {
        $model_groupbuy =& m('groupbuy');
		$canshu=$model_groupbuy->can();
        $data = $model_groupbuy->find(array(
            'join' => 'be_join,belong_goods',
            'fields' => 'gb.group_id,gb.group_name,gb.group_id,groupbuy_log.add_time,gb.spec_price,goods.default_image,gb.grcity',
            'conditions' => 'groupbuy_log.user_id > 0',
            'order' => 'groupbuy_log.add_time DESC',
            'limit' => $_num,
        ));
        foreach ($data as $gb_id => $gb_info)
        {
            $price = current(unserialize($gb_info['spec_price']));
            empty($gb_info['default_image']) && $data[$gb_id]['default_image'] = Conf::get('default_goods_image');
            $data[$gb_id]['price']      = $price['price'];
			$data[$gb_id]['jifen_price']=$price['price']*$canshu['jifenxianjin']*(1+$canshu['lv31']);
        }
        return $data;
    }
	
	function youhui()
    {
	//$url=$_SERVER['HTTP_HOST'];
	/*	echo $url;*/
	   $this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$kaiguan=$this->_city_mod->kg();
	$this->assign('kaiguan',$kaiguan);
	 $this->_curlocal(array(array('text' => Lang::get('youhui'))));
        $this->assign('page_title', Lang::get('groupbuy') . ' - ' . Conf::get('site_title'));
        $this->youhuiquan_mod =& m('youhuiquan');
		
		 $page = $this->_get_page(8); 
       $riqi=date('Y-m-d');
	    $youhui=$this->youhuiquan_mod->getAll("SELECT * " .
                    "FROM " . DB_PREFIX . "youhuiquan " .
                    "WHERE yhcity like '%$city_id,%'" .
				    "AND start_time<='$riqi' " .
					"AND end_time>= '$riqi 24:59:59' " .
					"ORDER BY youhui_id desc " .
                    " LIMIT {$page['limit']}"
					);	
      
	 
	   $you=$this->youhuiquan_mod->getAll("SELECT * " .
                    "FROM " . DB_PREFIX . "youhuiquan " .
                    "WHERE yhcity like '%$city_id,%' " .
				    "AND start_time<='$riqi' " .
					"AND end_time>= '$riqi 24:59:59' " .
					"ORDER BY youhui_id desc "
					);	
	  
         $page['item_count'] =  count($you);   //获取统计数据
		
        $this->_format_page($page);
		 $this->assign('page_info', $page);
         $this->assign('youhui',$youhui);
        $this->assign('guanggaowei', $this->guanggaowei(3,9));
        $this->assign('_last_goumai_youhui', $this->_last_goumai_youhui(5));
        $this->display('search.youhui.html');
    }
	//最近购买的优惠券

	function _last_goumai_youhui($_num)
    {
	//$url=$_SERVER['HTTP_HOST'];
	/*	echo $url;*/
	   $this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	
        $this->youhuilist_mod =& m('youhuilist');
        $data = $this->youhuilist_mod->find(array(
            'fields' => '*',
            'conditions' => 'youhui_id > 0 and ycity='.$city_id,
            'order' => 'riqi DESC',
            'limit' => $_num,
        ));
   
        return $data;
    }
	
	
function guanggaowei($_num,$type)
    {
	//$url=$_SERVER['HTTP_HOST'];
	$this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$time=date('Y-m-d H:i:s');
	 $this->adv_mod =& m('adv');
	$advs=$this->adv_mod->getAll("select * from ".DB_PREFIX."adv where type = '$type'");
	
        $data = $this->adv_mod->find(array(
            //'join' => 'be_join,belong_goods',
            'fields' => '*',
            'conditions' => "adv_city='$city_id' and type='$type' and start_time<='$time' and end_time>='$time'",
            'order' => 'riqi DESC',
            'limit' => $_num,
        ));
   
        return $data;
    }
		
	

                /* 取得店铺分类 */
    function _list_scategory()
    {
        $scategory_mod =& m('scategory');
        $scategories = $scategory_mod->get_list(-1,true);

        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($scategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree->getArrayList(0);
    }

    function _get_goods_curlocal($cate_id)
    {
        $parents = array();
        if ($cate_id)
        {
            $gcategory_mod =& bm('gcategory');
            $parents = $gcategory_mod->get_ancestor($cate_id, false);
	
        }
        $curlocal = array(
            array('text' => LANG::get('all_categories'), 'url' => "javascript:dropParam('cate_id')"),
        );
        foreach ($parents as $category)
        {
            $curlocal[] = array('text' => $category['cate_name'], 'url' => "javascript:replaceParam('cate_id', '" . $category['cate_id'] . "')");
        }
        unset($curlocal[count($curlocal) - 1]['url']);
        return $curlocal;
    }
	
	
	 function get_goods_curl($recom_id,$is_ershou=0)
    {
     $this->city_mod=& m('city');
	 $result=$this->city_mod->get_cityrow();   
	 $cityid=$result['city_id'];
$rec=$this->city_mod->getRow("select * from ".DB_PREFIX."recommend where recity = '$cityid' and recom_id=$recom_id");
		if($is_ershou==0)
		{
        $curlocal = array(
            array('text' => LANG::get('all_categories'), 'url' => "/index.php?app=category"),
			array('text' => $rec['recom_name']),
        );
		}
		else
		{
		 $curlocal = array(
            array('text' => LANG::get('all_categories'), 'url' => "/index.php?app=category"),
			array('text' => LANG::get('ershoushangpin')),
        );
		}
		
		return $curlocal;
    }
	

    function _get_store_curlocal($cate_id)
    {
        $parents = array();
	
        if ($cate_id)
        {
            $scategory_mod =& m('scategory');
            $scategory_mod->get_parents($parents, $cate_id);
        }

        $curlocal = array(
            array('text' => LANG::get('all_categories'), 'url' => url('app=category&act=store')),
        );
        foreach ($parents as $category)
        {
            $curlocal[] = array('text' => $category['cate_name'], 'url' => url('app=search&act=store&cate_id=' . $category['cate_id']));
        }
        unset($curlocal[count($curlocal) - 1]['url']);
        return $curlocal;
    }

    /**
     * 取得查询参数（有值才返回）
     *
     * @return  array(
     *              'keyword'   => array('aa', 'bb'),
     *              'cate_id'   => 2,
     *              'layer'     => 2, // 分类层级
     *              'brand'     => 'ibm',
     *              'region_id' => 23,
     *              'price'     => array('min' => 10, 'max' => 100),
     *          )
     */
    function _get_query_param()
    {
        $res = array();

        // keyword
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        if ($keyword != '')
        {
            //$keyword = preg_split("/[\s," . Lang::get('comma') . Lang::get('whitespace') . "]+/", $keyword);
            $tmp = str_replace(array(Lang::get('comma'),Lang::get('whitespace'),' '),',', $keyword);
            $keyword = explode(',',$tmp);
            sort($keyword);
            $res['keyword'] = $keyword;
        }

        // cate_id
        if (isset($_GET['cate_id']) && intval($_GET['cate_id']) > 0)
        {
            $res['cate_id'] = $cate_id = intval($_GET['cate_id']);
            $gcategory_mod  =& bm('gcategory');
            $res['layer']   = $gcategory_mod->get_layer($cate_id, true);
        }

        // brand
        if (isset($_GET['brand']))
        {
            $brand = trim($_GET['brand']);
            $res['brand'] = $brand;
        }

        // region_id
        if (isset($_GET['region_id']) && intval($_GET['region_id']) > 0)
        {
            $res['region_id'] = intval($_GET['region_id']);
        }
		
        if (isset($_GET['is_ershou']) && intval($_GET['is_ershou']) > 0)
        {
            $res['is_ershou'] = intval($_GET['is_ershou']);
        }
		if (isset($_GET['recom_id']) && intval($_GET['recom_id']) > 0)
        {
            $res['recom_id'] = intval($_GET['recom_id']);
        }


 // price
        if (isset($_GET['price']))
        {
            $arr = explode('-', $_GET['price']);
            $min = abs(floatval($arr[0]));
            $max = abs(floatval($arr[1]));
            if ($min * $max > 0 && $min > $max)
            {
                list($min, $max) = array($max, $min);
            }

            $res['price'] = array(
                'min' => $min,
                'max' => $max
            );
        }

        // jifen_price
        if (isset($_GET['jifen_price']))
        {
            $arr = explode('-', $_GET['jifen_price']);
            $min = abs(floatval($arr[0]));
            $max = abs(floatval($arr[1]));
            if ($min * $max > 0 && $min > $max)
            {
                list($min, $max) = array($max, $min);
            }

            $res['jifen_price'] = array(
                'min' => $min,
                'max' => $max
            );
        }
		
		 // vip_price
        if (isset($_GET['vip_price']))
        {
            $arr = explode('-', $_GET['vip_price']);
            $min = abs(floatval($arr[0]));
            $max = abs(floatval($arr[1]));
            if ($min * $max > 0 && $min > $max)
            {
                list($min, $max) = array($max, $min);
            }

            $res['vip_price'] = array(
                'min' => $min,
                'max' => $max
            );
        }
        return $res;
		

    }

    /**
     * 取得过滤条件
     */
    function _get_filter($param)
    {
        $filters = array();
        if (isset($param['keyword']))
        {
            $keyword = join(' ', $param['keyword']);
            $filters['keyword'] = array('key' => 'keyword', 'name' => LANG::get('keyword'), 'value' => $keyword);
        }
        isset($param['brand']) && $filters['brand'] = array('key' => 'brand', 'name' => LANG::get('brand'), 'value' => $param['brand']);
        if (isset($param['region_id']))
        {
            // todo 从地区缓存中取
            $region_mod =& m('region');
            $row = $region_mod->get(array(
                'conditions' => $param['region_id'],
                'fields' => 'region_name'
            ));
            $filters['region_id'] = array('key' => 'region_id', 'name' => LANG::get('region'), 'value' => $row['region_name']);
        }
        if (isset($param['price']))
        {
            $min = $param['price']['min'];
            $max = $param['price']['max'];
            if ($min <= 0)
            {
                $filters['price'] = array('key' => 'price', 'name' => LANG::get('price'), 'value' => LANG::get('le') . ' ' . price_format($max));
            }
            elseif ($max <= 0)
            {
                $filters['price'] = array('key' => 'price', 'name' => LANG::get('price'), 'value' => LANG::get('ge') . ' ' . price_format($min));
            }
            else
            {
                $filters['price'] = array('key' => 'price', 'name' => LANG::get('price'), 'value' => price_format($min) . ' - ' . price_format($max));
            }
        }


if (isset($param['jifen_price']))
        {
            $min = $param['jifen_price']['min'];
            $max = $param['jifen_price']['max'];
            if ($min <= 0)
            {
                $filters['jifen_price'] = array('key' => 'jifen_price', 'name' => LANG::get('jifenprice'), 'value' => LANG::get('le') . ' ' . price_format($max));
            }
            elseif ($max <= 0)
            {
                $filters['jifen_price'] = array('key' => 'jifen_price', 'name' => LANG::get('jifenprice'), 'value' => LANG::get('ge') . ' ' . price_format($min));
            }
            else
            {
                $filters['jifen_price'] = array('key' => 'jifen_price', 'name' => LANG::get('jifenprice'), 'value' => $min . ' - ' .$max);
            }
        }
		
		if (isset($param['vip_price']))
        {
            $min = $param['vip_price']['min'];
            $max = $param['vip_price']['max'];
            if ($min <= 0)
            {
                $filters['vip_price'] = array('key' => 'vip_price', 'name' => LANG::get('vipprice'), 'value' => LANG::get('le') . ' ' . price_format($max));
            }
            elseif ($max <= 0)
            {
                $filters['vip_price'] = array('key' => 'vip_price', 'name' => LANG::get('vipprice'), 'value' => LANG::get('ge') . ' ' . price_format($min));
            }
            else
            {
                $filters['vip_price'] = array('key' => 'vip_price', 'name' => LANG::get('vipprice'), 'value' => $min . ' - ' .$max);
            }
        }

		


        return $filters;
    }

    /**
     * 取得查询条件语句
     *
     * @param   array   $param  查询参数（参加函数_get_query_param的返回值说明）
     * @return  string  where语句
     */
    function _get_goods_conditions($param,$recom_id="")
    {
	$this->_city_mod =& m('city');
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	

        /* 组成查询条件 */
		if(empty($recom_id))
		{
        $conditions = " g.if_show = 1 AND g.closed = 0 AND s.state = 1"; // 上架且没有被禁售，店铺是开启状态,
		}
		else
		{
		 $conditions = " g.if_show = 1 AND g.closed = 0 AND s.state = 1 and rg.rcity like '%$city_id,%'";
		}
        if (isset($param['keyword']))
        {
            $conditions .= $this->_get_conditions_by_keyword($param['keyword'], ENABLE_SEARCH_CACHE);
        }
        if (isset($param['cate_id']))
        {	 if($param['layer']==0)
			{ 
			$param['layer']=1;
			}
            $conditions .= " AND g.cate_id_{$param['layer']} = '" . $param['cate_id'] . "'";
        }
        if (isset($param['brand']))
        {
            $conditions .= " AND g.brand = '" . $param['brand'] . "'";
        }
        if (isset($param['region_id']))
        {
            $conditions .= " AND s.region_id = '" . $param['region_id'] . "'";
        }
		if (isset($param['is_ershou']))
        {
            $conditions .= " AND g.is_ershou = '" . $param['is_ershou'] . "'";
        }
		if (isset($param['recom_id']))
        {
            $conditions .= " AND rg.recom_id = '" . $param['recom_id'] . "'";
        }
        if (isset($param['price']))
        {
            $min = $param['price']['min'];
            $max = $param['price']['max'];
            $min > 0 && $conditions .= " AND g.price >= '$min'";
            $max > 0 && $conditions .= " AND g.price <= '$max'";
        }
		 if (isset($param['jifen_price']))
        {
            $min = $param['jifen_price']['min'];
            $max = $param['jifen_price']['max'];
            $min > 0 && $conditions .= " AND g.jifen_price >= '$min'";
            $max > 0 && $conditions .= " AND g.jifen_price <= '$max'";
        }
		if (isset($param['vip_price']))
        {
            $min = $param['vip_price']['min'];
            $max = $param['vip_price']['max'];
            $min > 0 && $conditions .= " AND g.vip_price >= '$min'";
            $max > 0 && $conditions .= " AND g.vip_price <= '$max'";
        }

        return $conditions;
    }
    /**
     * 根据查询条件取得分组统计信息
     *
     * @param   array   $param  查询参数（参加函数_get_query_param的返回值说明）
     * @param   bool    $cached 是否缓存
     * @return  array(
     *              'total_count' => 10,
     *              'by_category' => array(id => array('cate_id' => 1, 'cate_name' => 'haha', 'count' => 10))
     *              'by_brand'    => array(array('brand' => brand, 'count' => count))
     *              'by_region'   => array(array('region_id' => region_id, 'region_name' => region_name, 'count' => count))
     *              'by_price'    => array(array('min' => 10, 'max' => 50, 'count' => 10))
     *          )
     */
    function _get_group_by_info($param, $cached,$recom_id="")
    {
        $data = false;
	//$cached=false;
        if ($cached)
        {
            $cache_server =& cache_server();
            $key = 'group_by_info_' . var_export($param, true);
            $data = $cache_server->get($key);
			//print_r($data);
        }

        if ($data === false)
        {
            $data = array(
                'total_count' => 0,
                'by_category' => array(),
                'by_brand'    => array(),
                'by_region'   => array(),
                'by_price'    => array(),
				'by_jfprice'    => array(),
				'by_vipprice'    => array()
            );

            $goods_mod =& m('goods');
            $store_mod =& m('store');
			$recommended_goods_mod =& m('recommendedgoods');
            $table = " {$goods_mod->table} g LEFT JOIN {$store_mod->table} s ON g.store_id = s.store_id left join {$recommended_goods_mod->table} rg on rg.goods_id=g.goods_id";
            $conditions = $this->_get_goods_conditions($param,$recom_id);

            $sql = "SELECT COUNT(*) FROM {$table} WHERE" . $conditions;
	
            $total_count = $goods_mod->getOne($sql);
            if ($total_count > 0)
            {
                $data['total_count'] = $total_count;
                /* 按分类统计 */
                $cate_id = isset($param['cate_id']) ? $param['cate_id'] : 0;
                $sql = "";
                if ($cate_id > 0)
                {
                    $layer = $param['layer'];
                    if ($layer < 4)
                    {
                        $sql = "SELECT g.cate_id_" . ($layer + 1) . " AS id, COUNT(*) AS count FROM {$table} WHERE" . $conditions . " AND g.cate_id_" . ($layer + 1) . " > 0 GROUP BY g.cate_id_" . ($layer + 1) . " ORDER BY count DESC";
                    }
                }
                else
                {
                    $sql = "SELECT g.cate_id_1 AS id, COUNT(*) AS count FROM {$table} WHERE" . $conditions . " AND g.cate_id_1 > 0 GROUP BY g.cate_id_1 ORDER BY count DESC";
                }

                if ($sql)
                {
				
                    $category_mod =& bm('gcategory');
                    $children = $category_mod->get_children($cate_id, true);
					//print_r($children);
                    $res = $goods_mod->db->query($sql);
					//print_r($res);
                    while ($row = $goods_mod->db->fetchRow($res))
                    {
					//print_r($row);
                        $data['by_category'][$row['id']] = array(
                            'cate_id'   => $row['id'],
                            'cate_name' => $children[$row['id']]['cate_name'],
                            'count'     => $row['count']
                        );
                    }
				
                }

                /* 按品牌统计 */
                $sql = "SELECT g.brand, COUNT(*) AS count FROM {$table} WHERE" . $conditions . " AND g.brand > '' GROUP BY g.brand ORDER BY count DESC";
                $data['by_brand'] = $goods_mod->getAll($sql);

                /* 按地区统计 */
                $sql = "SELECT s.region_id, s.region_name, COUNT(*) AS count FROM {$table} WHERE" . $conditions . " AND s.region_id > 0 GROUP BY s.region_id ORDER BY count DESC";
                $data['by_region'] = $goods_mod->getAll($sql);

                /* 按价格统计 */
                if ($total_count > NUM_PER_PAGE)
                {
                    $sql = "SELECT MIN(g.price) AS min, MAX(g.price) AS max FROM {$table} WHERE" . $conditions;
					
				
                    $row = $goods_mod->getRow($sql);
                    $min = $row['min'];
                    $max = min($row['max'], MAX_STAT_PRICE);
                    $step = max(ceil(($max - $min) / PRICE_INTERVAL_NUM), MIN_STAT_STEP);
                    $sql = "SELECT FLOOR((g.price - '$min') / '$step') AS i, count(*) AS count FROM {$table} WHERE " . $conditions . " GROUP BY i ORDER BY i";
				
                    $res = $goods_mod->db->query($sql);
                    while ($row = $goods_mod->db->fetchRow($res))
                    {
                        $data['by_price'][] = array(
                            'count' => $row['count'],
                            'min'   => $min + $row['i'] * $step,
                            'max'   => $min + ($row['i'] + 1) * $step,
                        );
                    }
                }
				//按积分价格查询
                if ($total_count > NUM_PER_PAGE)
                {
                    $sql = "SELECT MIN(g.jifen_price) AS min, MAX(g.jifen_price) AS max FROM {$table} WHERE" . $conditions;
					
                    $row = $goods_mod->getRow($sql);
                    $min = $row['min'];
                    $max = min($row['max'], MAX_STAT_PRICE);
                    $step = max(ceil(($max - $min) / PRICE_INTERVAL_NUM), MIN_STAT_STEP);
                    $sql = "SELECT FLOOR((g.jifen_price - '$min') / '$step') AS i, count(*) AS count FROM {$table} WHERE " . $conditions . " GROUP BY i ORDER BY i";
					
                    $res = $goods_mod->db->query($sql);
                    while ($row = $goods_mod->db->fetchRow($res))
                    {
                        $data['by_jfprice'][] = array(
                            'count' => $row['count'],
                            'min'   => $min + $row['i'] * $step,
                            'max'   => $min + ($row['i'] + 1) * $step,
                        );
                    }
                }
				
				//按vip价格查询
                if ($total_count > NUM_PER_PAGE)
                {
                    $sql = "SELECT MIN(g.vip_price) AS min, MAX(g.vip_price) AS max FROM {$table} WHERE" . $conditions;
					
				
                    $row = $goods_mod->getRow($sql);
                    $min = $row['min'];
                    $max = min($row['max'], MAX_STAT_PRICE);
                    $step = max(ceil(($max - $min) / PRICE_INTERVAL_NUM), MIN_STAT_STEP);
                    $sql = "SELECT FLOOR((g.vip_price - '$min') / '$step') AS i, count(*) AS count FROM {$table} WHERE " . $conditions . " GROUP BY i ORDER BY i";
					
                    $res = $goods_mod->db->query($sql);
                    while ($row = $goods_mod->db->fetchRow($res))
                    {
                        $data['by_vipprice'][] = array(
                            'count' => $row['count'],
                            'min'   => $min + $row['i'] * $step,
                            'max'   => $min + ($row['i'] + 1) * $step,
                        );
                    }
                }
				
            }

            if ($cached)
            {
                $cache_server->set($key, $data, SEARCH_CACHE_TTL);
            }
        }

        return $data;
    }

 

    /**
     * 根据关键词取得查询条件（可能是like，也可能是in）
     *
     * @param   array       $keyword    关键词
     * @param   bool        $cached     是否缓存
     * @return  string      " AND (0)"
     *                      " AND (goods_name LIKE '%a%' AND goods_name LIKE '%b%')"
     *                      " AND (goods_id IN (1,2,3))"
     */
    function _get_conditions_by_keyword($keyword, $cached)
    {
        $conditions = false;

        if ($cached)
        {
            $cache_server =& cache_server();
            $key1 = 'query_conditions_of_keyword_' . join("\t", $keyword);
            $conditions = $cache_server->get($key1);
        }

        if ($conditions === false)
        {
            /* 组成查询条件 */
            $conditions = array();
            foreach ($keyword as $word)
            {
                $conditions[] = "g.goods_name LIKE '%{$word}%'";
            }
            $conditions = join(' AND ', $conditions);

            /* 取得满足条件的商品数 */
            $goods_mod =& m('goods');
            $sql = "SELECT COUNT(*) FROM {$goods_mod->table} g WHERE " . $conditions;
            $current_count = $goods_mod->getOne($sql);
            if ($current_count > 0)
            {
                if ($current_count < MAX_ID_NUM_OF_IN)
                {
                    /* 取得商品表记录总数 */
                    $cache_server =& cache_server();
                    $key2 = 'record_count_of_goods';
                    $total_count = $cache_server->get($key2);
                    if ($total_count === false)
                    {
                        $sql = "SELECT COUNT(*) FROM {$goods_mod->table}";
                        $total_count = $goods_mod->getOne($sql);
                        $cache_server->set($key2, $total_count, SEARCH_CACHE_TTL);
                    }

                    /* 不满足条件，返回like */
                    if (($current_count / $total_count) < MAX_HIT_RATE)
                    {
                        /* 取得满足条件的商品id */
                        $sql = "SELECT goods_id FROM {$goods_mod->table} g WHERE " . $conditions;
                        $ids = $goods_mod->getCol($sql);
                        $conditions = 'g.goods_id' . db_create_in($ids);
                    }
                }
            }
            else
            {
                /* 没有满足条件的记录，返回0 */
                $conditions = "0";
            }

            if ($cached)
            {
                $cache_server->set($key1, $conditions, SEARCH_CACHE_TTL);
            }
        }

        return ' AND (' . $conditions . ')';
    }

    /* 商品排序方式 */
    function _get_orders()
    {
        return array(
            ''                  => Lang::get('select_pls'),
			'c desc'                  => Lang::get('zhan'),
            'sales desc'        => Lang::get('sales_desc'),
            'credit_value desc' => Lang::get('credit_value_desc'),
            //'price asc'         => Lang::get('price_asc'),
            //'price desc'        => Lang::get('price_desc'),
			'jifen_price asc'         => Lang::get('jifen_price_asc'),
            'jifen_price desc'        => Lang::get('jifen_price_desc'),
			'vip_price asc'         => Lang::get('vip_price_asc'),
            'vip_price desc'        => Lang::get('vip_price_desc'),
            'views desc'        => Lang::get('views_desc'),
            'g.add_time desc'     => Lang::get('add_time_desc'),
        );
    }
	
function gonghuo()
 {
	   $this->_city_mod =& m('city');
	   $this->kaiguan_mod =& m('kaiguan');
	   $row_kaiguan=$this->kaiguan_mod->kg();
	   //$row_kaiguan=$this->kaiguan_mod->getrow("select gonghuo from ".DB_PREFIX."kaiguan");
	$gonghuo_kaiguan=$row_kaiguan['gonghuo'];
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$this->assign('kaiguan',$kaiguan);
	
	 /* 搜索条件 */
        $conditions = "1 = 1 and ";
		$goodsname=trim($_GET['goodsname']);
		$brand=trim($_GET['brand']);
        if (trim($_GET['goodsname']))
        {
            $str = "LIKE '%" . trim($_GET['goodsname']) . "%'";
            $conditions .= " (goods_name {$str}) and ";
        }
		if (trim($_GET['brand']))
        {
            $str = "LIKE '%" . trim($_GET['brand']) . "%'";
            $conditions .= " (goods_brand {$str}) and ";
        }
	 $this->assign('goodsname',$goodsname);
	 $this->assign('brand',$brand);
	
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	 $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
    $this->assign('build_editor', $this->_build_editor(array('name' => 'beizhu')));
        $this->gonghuo_mod =& m('gonghuo');
		  $this->_curlocal(array(array('text' => Lang::get('caigou'))));
		 $page = $this->_get_page(8); 
       $deng=Lang::get('shenhetongguo');
	   if($gonghuo_kaiguan=='yes')
	   {
        $gonghuo = $this->gonghuo_mod->find(array(
            'order'         => 'gh_id DESC',
            'limit'      => $page['limit'],
			'count'         => true,   //允许统计
			'conditions'         => $conditions . 'status=1 and yu_kucun>0',
        ));
		}
		else
		{
		 $gonghuo = $this->gonghuo_mod->find(array(
            'order'         => 'gh_id DESC',
            'limit'      => $page['limit'],
			'count'         => true,   //允许统计
			'conditions'         =>$conditions. ' gh_city='.$city_id.' and status=1 and yu_kucun>0',
        ));
		}
         $page['item_count'] =  $this->gonghuo_mod->getCount();   //获取统计数据
        $this->_format_page($page);
		$this->assign('page_info', $page);
        $this->assign('gonghuo',$gonghuo);
        $this->assign('guanggaowei', $this->guanggaowei(3,10));
        $this->assign('last_caigou', $this->_last_caigou(6));
        $this->display('search.gonghuo.html');
    }
	
	function _last_caigou($_num)
    {
	//$url=$_SERVER['HTTP_HOST'];
	/*	echo $url;*/
	   $this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$shenhe=Lang::get('shenhetongguo');
	
        $this->caigou_mod =& m('caigou');
        $data = $this->caigou_mod->find(array(
            'fields' => '*',
            'conditions' => "city='$city_id' and status=1",
            'order' => 'riqi DESC',
            'limit' => $_num,
        ));
   
        return $data;
    }
	
	function more()
	{

	$this->_city_mod =& m('city');
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$this->recom_mod =& m('recommend');
	$this->recommm_mod =& m('recommendedgoods');
	$recom_id = empty($_GET['recom_id']) ? null : trim($_GET['recom_id']);
	if ($recom_id==20)
	{
	 $this->_curlocal(array(array('text' => Lang::get('xinpinshangshi'))));
	}
	if ($recom_id==21)
	{
	 $this->_curlocal(array(array('text' => Lang::get('jingpintuijian'))));
	}
	if ($recom_id==22)
	{
	 $this->_curlocal(array(array('text' => Lang::get('ershoushangpin'))));
	}
	if ($recom_id==23)
	{
	 $this->_curlocal(array(array('text' => Lang::get('qiujixinkuan'))));
	}
	if ($recom_id==24)
	{
	 $this->_curlocal(array(array('text' => Lang::get('liangdianshipin'))));
	}
	
	
	  $param = $this->_get_query_param();

       /* if (empty($param))
        {
            header('Location: index.php?app=category');
            exit;
        }
*/
        /* 筛选条件 */
        $this->assign('filters', $this->_get_filter($param));

        /* 按分类、品牌、地区、价格区间统计商品数量 */
        $stats = $this->_get_group_by_info($param, ENABLE_SEARCH_CACHE);
	  

        $this->assign('categories', $stats['by_category']);
        $this->assign('category_count', count($stats['by_category']));

        $this->assign('brands', $stats['by_brand']);
        $this->assign('brand_count', count($stats['by_brand']));

        $this->assign('price_intervals', $stats['by_price']);
		
		$this->assign('jfprice', $stats['by_jfprice']);
		$this->assign('vipprice', $stats['by_vipprice']);

        $this->assign('regions', $stats['by_region']);
        $this->assign('region_count', count($stats['by_region']));

        /* 排序 */
        $orders = $this->_get_orders();
        $this->assign('orders', $orders);

        /* 分页信息 */
        $page = $this->_get_page(NUM_PER_PAGE);
        $page['item_count'] = $stats['total_count'];
        $this->_format_page($page);
        $this->assign('page_info', $page);
	
        $this->assign('page_title', Lang::get('groupbuy') . ' - ' . Conf::get('site_title'));
	
        /* 商品列表 */
        $sgrade_mod =& m('sgrade');
        $sgrades    = $sgrade_mod->get_options();
        $conditions = $this->_get_goods_conditions($param,$recom_id);
	
        $goods_mod  =& m('goods');
		$orde=isset($_GET['order']) && isset($orders[$_GET['order']]) ? $_GET['order'] : '';
	
	//$sql = "SELECT count(*) AS count FROM {$table} WHERE recom_id='$recom_id'";
                   // $row = $recommm_mod->getRow($sql);
                    //$count = $row['count'];
	$go=$this->recom_mod->getRow("SELECT count(*) AS count " .
		"FROM " . DB_PREFIX . "recommended_goods AS rg " .
		"   LEFT JOIN " . DB_PREFIX . "goods AS g ON rg.goods_id = g.goods_id " .
		"WHERE rg.recom_id='$recom_id' and rg.rcity like '%$city_id,%'"
					);	
					//echo $go['count'];	
	//$img_goods = $this->recom_mod->get_recommended_goods($recom_id,$go['count'], true);
		/* 分页信息 */
        $page = $this->_get_page(NUM_PER_PAGE);
        $page['item_count'] = $go['count'];
        $this->_format_page($page);
        $this->assign('page_info', $page);
	     $img_goods=$this->recom_mod->getAll("SELECT g.goods_id, g.goods_name, g.default_image, gs.price, gs.stock,gs.vip_price,gs.jifen_price,g.cityhao " .
                    "FROM " . DB_PREFIX . "recommended_goods AS rg " .
					//"FROM " . DB_PREFIX . "recomgoods AS rgs " .
                    "   LEFT JOIN " . DB_PREFIX . "goods AS g ON rg.goods_id = g.goods_id " .
                    "   LEFT JOIN " . DB_PREFIX . "goods_spec AS gs ON g.default_spec = gs.spec_id " .
                    "   LEFT JOIN " . DB_PREFIX . "store AS s ON g.store_id = s.store_id " .
                    "WHERE " . $conditions .
				    " AND rg.recom_id = '$recom_id'" .
					"AND rg.rcity like '%$city_id,%' " .
                    "LIMIT {$page['limit']}"
					);	
					
		foreach($img_goods as $key=>$val)
		{
			$img_goods[$key]['jifen_price']=round($val['jifen_price'],2);
			$img_goods[$key]['vip_price']=round($val['vip_price'],2);
		}
		
		$this->assign('img_goods', $img_goods);
		 $display_mode = ecm_getcookie('goodsDisplayMode');
        if (empty($display_mode) || !in_array($display_mode, array('list', 'squares')))
        {
            $display_mode = 'squares'; // 默认格子方式
        }
        $this->assign('display_mode', $display_mode);

        /* 取得导航 */
        $this->assign('navs', $this->_get_navs());

        /* 当前位置 */
        $cate_id = isset($param['cate_id']) ? $param['cate_id'] : 0;
        //$this->_curlocal($this->_get_goods_curlocal($cate_id));

        $this->assign('page_title', Conf::get('site_title'));
		//$this->assign('txt_goods_list3', $txt_goods_list3);
	 $this->display('search.recom.html');
	}
	
	function news()
    {
		//$url=$_SERVER['HTTP_HOST'];
		/*	echo $url;*/
		$this->_city_mod =& m('city');
		//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
		//$city_id=$row_city['city_id'];
		$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
		$this->_curlocal(array(array('text' => Lang::get('news'))));
		$this->assign('page_title', Lang::get('groupbuy') . ' - ' . Conf::get('site_title'));
	$time=date('Y-m-d H:i:s');
		
		
		$this->assign('news1', getarticle(17,8));
		$this->assign('news2', getarticle(18,8));
		
		$this->assign('news3', getarticle(19,8));
		$this->assign('news4', getarticle(20,8));
		$this->assign('news5', getarticle(21,8));
		
$adv_list11=$this->_city_mod->getAll("select * from ".DB_PREFIX."adv where 
	   type='11' and adv_city='$city_id' and start_time<='$time' and end_time>='$time' order by riqi desc limit 0,2
	   ");
	   $i=1;
		foreach($adv_list11 as $adv)
		{
			if($i==1)
			{
				$this->assign('adv_list11', $adv['image']);
			}
			else
			{
				$this->assign('adv_list12', $adv['image']);
			}
			$i++;
		}
	
		
		$pics=$this->_city_mod->getAll("select a.article_id,b.file_path from ".DB_PREFIX."article a join ".DB_PREFIX."uploaded_file b on a.article_id=b.item_id where a.cate_id in(17,18,19,20,21)  and a.if_show=1 and a.city=1  and b.store_id=0 order by  a.sort_order ASC, a.add_time DESC limit 0,5");
		$bannerAD='';
		$bannerADlink='';
		
		foreach($pics as $i=>$v)
		{
			$bannerAD.="bannerAD[$i]='".$v['file_path']."';";
			$bannerADlink.="bannerADlink[$i]='".'?app=article&act=view&article_id='.$v['article_id']."';";
}
		$this->assign('bannerAD', $bannerAD);
		$this->assign('bannerADlink', $bannerADlink);
		
		$this->display('search.news.html');
    }
	
	
	
	
	
}
function getarticle($cid,$num=10)
{


	$article_mod =& m('article');
	$cityrow=$article_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$news1 = $article_mod->find(array(
		'conditions'    => 'cate_id='.$cid.' AND if_show = 1 and (city=1 or city='.$city_id.')',
		'order'         => 'sort_order ASC, add_time DESC',
		'fields'        => 'article_id, title, add_time',
		'limit'         => ' 0,'.$num,
	));
	return $news1;
}
?>
