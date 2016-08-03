<?php
/**
 *    商品管理控制器
 */
class GoodsApp extends BackendApp
{
    var $_goods_mod;

    function __construct()
    {
        $this->GoodsApp();
    }
    function GoodsApp()
    {
        parent::BackendApp();

        $this->_goods_mod =& m('goods');
		$this->_recommended_mod =& m('recommendedgoods');
		$this->userpriv_mod =& m('userpriv');
		$this->message_mod =& m('message');
    }

    /* 商品列表 */
    function index()
    {
	
	$this->member_mod =& m('member');
	$user=$this->visitor->get('user_name');
	$userid=$this->visitor->get('user_id');
$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
//	//$city=$row_member['city'];
	$userid=$row_member['user_id'];
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$this->assign('priv_row', $priv_row);
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'goods_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'store_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'brand',
                'equal' => 'like',
            ),
            array(
                'field' => 'closed',
                'type'  => 'int',
            ),array(
                'field' => 'g.add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'g.add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
			),array(
                'field' => 'views',
                'name'  => 'view_from',
                'equal' => '>=',
                
            ),array(
                'field' => 'views',
                'name'  => 'view_to',
                'equal' => '<=',
                
			),array(
                'field' => 'cityhao',
                'name'  => 'suoshuzhan',
                'equal' => '=',
                
			),array(
                'field' => 'daishou',
                'name'  => 'leixing',
                'equal' => '=',
                
			),
		
        ));

		$ship=$_GET['shipping_id'];
		$this->assign('ship',$ship);
		if($ship==2)//没有配送方式
		{
			$conditions.=" and shipping_id=0 ";
		}
		if($ship==1)//有配送方式
		{
			$conditions.=" and shipping_id!=0 ";
		}


        // 分类
        $cate_id = empty($_GET['cate_id']) ? 0 : intval($_GET['cate_id']);
        if ($cate_id > 0)
        {
            $cate_mod =& bm('gcategory');
            $cate_ids = $cate_mod->get_descendant_ids($cate_id);
            $conditions .= " AND cate_id" . db_create_in($cate_ids);
        }


        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'goods_id';
             $order = 'desc';
            }
        }
        else
        {
            $sort  = 'goods_id';
            $order = 'desc';
        }

        $page = $this->_get_page();
		if($privs=='all')
		{
        $goods_list = $this->_goods_mod->get_list(array(
            'conditions' => '1 = 1' . $conditions,
            'count' => true,
            'order' => "$sort $order",
            'limit' => $page['limit'],
        ));
		}
		else
		{
		$goods_list = $this->_goods_mod->get_list(array(
            'conditions' => 'cityhao='.$city.' and 1 = 1' . $conditions,
            'count' => true,
            'order' => "$sort $order",
            'limit' => $page['limit'],
        ));
		}

		
		$city_row=array();
		$result=$this->userpriv_mod->getAll("select * from ".DB_PREFIX."city");
		 $this->assign('result', $result);
		foreach ($result as $var )
		{
		  $row=explode('-',$var['city_name']);
		  $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
        foreach ($goods_list as $key => $goods)
        {
		
            $goods_list[$key]['cate_name'] = $this->_goods_mod->format_cate_name($goods['cate_name']);
			$goods_list[$key]['city_name'] = $city_row[$goods['cityhao']];
			
			$shippingid=$goods['shipping_id'];
			
			$ship=$this->_goods_mod->getRow("select cod_regions from ".DB_PREFIX."shippings where shipping_id='$shippingid' limit 1");
			
			if($ship)
			{
				$cod_regions=unserialize($ship['cod_regions']);
				foreach($cod_regions as $key1=>$var )
				{
					if($key1==0)
					{
						$goods_list[$key]['one_price']=$var['price'];
					}
				}
			}
			
        }
		
        $this->assign('goods_list', $goods_list);

        $page['item_count'] = $this->_goods_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        // 第一级分类
        $cate_mod =& bm('gcategory', array('_store_id' => 0));
        $this->assign('gcategories', $cate_mod->get_options(0, true));
        //$this->import_resource(array('script' => 'mlselection.js,inline_edit.js'));
		$this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->display('goods.index.html');
    }

    /* 推荐商品到 */
    function recommend()
    {
     
	
        if (!IS_POST)
        {
		
		 $userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
		     $this->city_mod=& m('city');
            /* 取得推荐类型 */
            $recommend_mod =& bm('recommend', array('_store_id' => 0));
            $recommends = $recommend_mod->get_options($city);
			if ($privs=="all")
			{
			$row_city=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
			}
			else
			{
			$row_city=$this->city_mod->getAll("select * from ".DB_PREFIX."city where city_id='$city'");
			}
            if (!$recommends)
            {
                $this->show_warning('no_recommends', 'go_back', 'javascript:history.go(-1);', 'set_recommend', 'index.php?app=recommend');
                return;
            }
			$this->assign('row_city', $row_city);
            $this->assign('recommends', $recommends);
            $this->display('goods.batch.html');
        }
        else
        {
         $str1=$_POST[fu];
       
		  if($str1=="")
		  {
		   $this->show_warning('xuanzefenzhan');
                return;
		  }
		  $str=  implode( ', ',   $_POST[fu]).','; 
		  //echo $str;

		/*foreach($_POST[fu] as $var)//通过foreach循环取出多选框中的值
        {
         $str=$str.",".$var;
        }*/
		//echo $str;

            $id = isset($_POST['id']) ? trim($_POST['id']) : '';
			//$rcity=isset($_POST['city']) ? trim($_POST['city']) : '';
			//echo $rcity;
            if (!$id)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $recom_id = empty($_POST['recom_id']) ? 0 : intval($_POST['recom_id']);
			//$rcity = empty($_POST['rcity']) ? 0 : intval($_POST['rcity']);
		
            if (!$recom_id)
            {
                $this->show_warning('recommend_required');
                return;
            }


            $ids = explode(',', $id);
			$data = array(
                'recom_id'     => $recom_id,
                'goods_id'   => $id,
				'rcity'   => 1,
            );
			
				   
		
            $recom_mod =& bm('recommend', array('_store_id' => 0));
		   // $this->_recommended_mod->add($data);
            $recom_mod->createRelation('recommend_goods', $recom_id, $ids);
			foreach ($ids as $var)
			{
			$recom_mod->db->query("update ".DB_PREFIX."recommended_goods set rcity='$str' where goods_id='$var'");
			}
			
            $this->show_message('recommend_ok',
                'back_list', 'index.php?app=goods',
                'view_recommended_goods', 'index.php?app=recommend&amp;act=view_goods&amp;id=' . $recom_id. '&city='.$str);
        }
    }

    /* 编辑商品 */
    function edit()
    {
        if (!IS_POST)
        {
            // 第一级分类
            $cate_mod =& bm('gcategory', array('_store_id' => 0));
            $this->assign('gcategories', $cate_mod->get_options(0, true));

            $this->headtag('<script type="text/javascript" src="{lib file=mlselection.js}"></script>');
            $this->display('goods.batch.html');
        }
        else
        {
            $id = isset($_POST['id']) ? trim($_POST['id']) : '';
			$pag = empty($_GET['page']) ? 0 : $_GET['page'];
            if (!$id)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $ids = explode(',', $id);
            $data = array();
            if ($_POST['cate_id'] > 0)
            {
                $data['cate_id'] = $_POST['cate_id'];
                $data['cate_name'] = $_POST['cate_name'];
            }
            if (trim($_POST['brand']))
            {
                $data['brand'] = trim($_POST['brand']);
            }
            if ($_POST['closed'] >= 0)
            {
                $data['closed'] = $_POST['closed'] ? 1 : 0;
                $data['close_reason'] = $_POST['closed'] ? $_POST['close_reason'] : '';
            }

            if (empty($data))
            {
                $this->show_warning('no_change_set');
                return;
            }

            $this->_goods_mod->edit($ids, $data);

            $this->show_message('edit_ok',
                'back_list', 'index.php?app=goods&page= '. $pag);
        }
    }
	
	/* 编辑商品 */
    function bianji()
    {
	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
	$pag = empty($_GET['page']) ? 0 : $_GET['page'];
        if (!IS_POST)
        {
		
           $goods_list=$this->_goods_mod->getRow("SELECT *,g.erweima,s.store_name " .
                    " FROM " . DB_PREFIX . "goods AS g " .
                    "   LEFT JOIN " . DB_PREFIX . "store AS s ON s.store_id = g.store_id " .
                    " WHERE g.goods_id='$id'"
					);		
			$this->assign('goods_list',$goods_list);
            $this->display('goods.edit.html');
        }
        else
        {
           
			$erweima=trim($_POST['erweima']);
			
		$data=array('erweima'=>$erweima);
            $this->_goods_mod->edit('goods_id='.$id, $data);

            $this->show_message('edit_ok',
                'back_list', 'index.php?app=goods&page= '. $pag);
        }
    }


    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

$result=$this->message_mod->getRow("select * from ".DB_PREFIX."goods where goods_id='$id' limit 1");
$goodname=$result['goods_name'];
$userid=$result['store_id'];
$result1=$this->message_mod->getRow("select * from ".DB_PREFIX."member where user_id='$userid' limit 1");
$user_name=$result1['user_name'];
       if (in_array($column ,array('goods_name', 'brand', 'closed')))
       {
           $data[$column] = $value;
           if($this->_goods_mod->edit($id, $data))
           {
               echo ecm_json_encode(true);
           }
		   if($value==1)
		   {
		   $notice=Lang::get('chanpinxiajia');
			$notice=str_replace('{1}',$user_name,$notice);
			$notice=str_replace('{2}',$goodname,$notice);
			$add_notice=array(
			'from_id'=>0,
			'to_id'=>$userid,
			'content'=>$notice,  
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3
			);
			}
			else
			{
			$notice=Lang::get('chanpinshang');
			$notice=str_replace('{1}',$user_name,$notice);
			$notice=str_replace('{2}',$goodname,$notice);
			$add_notice=array(
			'from_id'=>0,
			'to_id'=>$userid,
			'content'=>$notice,  
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3
			);		
			}				
			$this->message_mod->add($add_notice);
		   
		   
       }
       else
       {
	   
			/*$notice=Lang::get('chanpinshang');
			$notice=str_replace('{1}',$user_name,$notice);
			$notice=str_replace('{2}',$goodname,$notice);
			$add_notice=array(
			'from_id'=>0,
			'to_id'=>$userid,
			'content'=>$notice,  
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3
			);				
			$this->message_mod->add($add_notice);   */
	   
           return ;
       }
       return ;
   }

    /* 删除商品 */
    function drop()
    {
        if (!IS_POST)
        {
            $this->display('goods.batch.html');
        }
        else
        {
            $id = isset($_POST['id']) ? trim($_POST['id']) : '';
            if (!$id)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }
            $ids = explode(',', $id);

            // notify store owner
            $ms =& ms();
            $goods_list = $this->_goods_mod->find(array(
                "conditions" => $ids,
                "fields" => "goods_name, store_id",
            ));
            foreach ($goods_list as $goods)
            {
                //$content = sprintf(LANG::get('toseller_goods_droped_notify'), );
                $content = get_msg('toseller_goods_droped_notify', array('reason' => trim($_POST['drop_reason']),
                    'goods_name' => addslashes($goods['goods_name'])));
                $ms->pm->send(MSG_SYSTEM, $goods['store_id'], '', $content);
            }

            // drop
            $this->_goods_mod->drop_data($ids);
            $this->_goods_mod->drop($ids);

            $this->show_message('drop_ok',
                'back_list', 'index.php?app=goods');
        }
    }
}

?>
