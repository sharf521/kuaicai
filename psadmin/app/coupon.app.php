<?php

/**
 *    商品分享管理控制器
 *
 *    @author    Hyber
 *    @usage    none
 */
class CouponApp extends BackendApp
{
    var $_m_share;

    function __construct()
    {
        $this->CouponApp();
    }

    function CouponApp()
    {
        parent::BackendApp();

        $this->coupon_mod =& m('coupon');
		$this->_user_mod =& m('member');
		$this->youhuiquan_mod =& m('youhuiquan');
		$this->userpriv_mod =& m('userpriv');
    }

    /**
     *    商品分享索引
     *
     *    @author    Hyber
     *    @return    void
     */
    function index()
    {
	
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row', $priv_row);
      /* $row_member=$this->_user_mod->getrow("select * from ".DB_PREFIX."member where user_name = '$user'");
	$city=$row_member['city'];*/
	 $page = $this->_get_page();
	  $sort  = 'coupon_id';
      $order = 'desc';
	  if($privs=="all")
	  {
	    $users=$this->coupon_mod->getAll("SELECT c.*, s.* " .
                    "FROM " . DB_PREFIX . "coupon AS c " .
					//"FROM " . DB_PREFIX . "recomgoods AS rgs " .
                    "   LEFT JOIN " . DB_PREFIX . "store AS s ON s.store_id = c.store_id " .
                   /* "WHERE s.cityid='$city'" .*/
					"ORDER BY coupon_id desc " .
                    " LIMIT {$page['limit']}"
					);	
	  }
	  else
	  {
	  $users=$this->coupon_mod->getAll("SELECT c.*, s.* " .
                    "FROM " . DB_PREFIX . "coupon AS c " .
					//"FROM " . DB_PREFIX . "recomgoods AS rgs " .
                    "   LEFT JOIN " . DB_PREFIX . "store AS s ON s.store_id = c.store_id " .
                    "WHERE s.cityid='$city'" .
					"ORDER BY coupon_id desc" .
                    " LIMIT {$page['limit']}"
					);	
	  
	  }

        $result=$this->coupon_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($users as $key => $val)
        {
			$users[$key]['city_name'] = $city_row[$val['city']];
        }
     if($privs=="all")
	  {
	    $coun=$this->coupon_mod->getAll("SELECT c.*, s.* " .
                    "FROM " . DB_PREFIX . "coupon AS c " .
					//"FROM " . DB_PREFIX . "recomgoods AS rgs " .
                    "   LEFT JOIN " . DB_PREFIX . "store AS s ON s.store_id = c.store_id " .
                   /* "WHERE s.cityid='$city'" .*/
					"ORDER BY coupon_id desc " 
                   
					);	
	  }
	  else
	  {
	  $coun=$this->coupon_mod->getAll("SELECT c.*, s.* " .
                    "FROM " . DB_PREFIX . "coupon AS c " .
					//"FROM " . DB_PREFIX . "recomgoods AS rgs " .
                    "   LEFT JOIN " . DB_PREFIX . "store AS s ON s.store_id = c.store_id " .
                    "WHERE s.cityid='$city'" .
					"ORDER BY coupon_id desc" 
                    
					);	
	  
	  }
		  
        //$page['item_count'] = $this->coupon_mod->getCount();
		$page['item_count'] = count($coun);
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		$this->assign('users', $users);
		 $this->display('coupon.index.html');

    }

    function add()
    {
	
	$user=$this->visitor->get('user_name');
	$youhui=$this->_user_mod->getAll("select city from ".DB_PREFIX."member where user_name = '$user'");
    $this->assign('youhui', $youhui);
	
	
        if (!IS_POST)
        {
		
		$this->city_mod=& m('city');
		$city=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
		$canshu=$this->city_mod->getRow("select * from ".DB_PREFIX."canshu");
		$this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
		$this->assign('city', $city);
		$this->assign('canshu', $canshu);
        $this->display('coupon.form.html');
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
		
		
            $data = array(
                'youhui_name' => trim($_POST['youhui_name']),
				'youhui_image'  => trim($_POST['youhui_image']),
                'youhui_jine'  => trim($_POST['youhui_jine']),
                'start_time'  => trim($_POST['start_time']),
				'end_time'  => trim($_POST['end_time']),
				/*'yhcity'  => trim($_POST['yhcity']),*/
				'yhcity'  =>$str,
				'goumai'  => trim($_POST['goumai']),
                'beizhu'  => trim($_POST['beizhu']),
				'youhui_jifen'  => trim($_POST['youhui_jifen']),
				'goumai_jifen'  => trim($_POST['goumai_jifen']),
				
            );

          if(trim($_POST['youhui_name'])=="")
		  {
		   $this->show_warning('youhuiquanmingchengbunengweikong');
		   return;
		  }
		  
		   if(trim($_POST['youhui_jine'])=="")
		  {
		   $this->show_warning('youhuiquanjinebunengweikong');
		   return;
		  }
		   if(trim($_POST['start_time'])=="")
		  {
		   $this->show_warning('kaishishijianbunengweikong');
		   return;
		  }
		   if(trim($_POST['end_time'])=="")
		  {
		   $this->show_warning('jieshushijianbunengweikong');
		   return;
		  }

             if (!$youhui_id = $this->youhuiquan_mod->add($data))  //获取brand_id
            {
                $this->show_warning($this->youhuiquan_mod->get_error());

                return;
            }

        // $this->youhuiquan_mod->db->query("update ".DB_PREFIX."youhuiquan set yhcity='$str' where youhui_id='$youhui_id'");
            /* 处理上传的图片 */
            $logo       =   $this->_upload_logo($youhui_id);
            if ($logo === false)
            {
                return;
            }
            
            $logo && $this->youhuiquan_mod->edit($youhui_id, array(
			'youhui_image' => $logo,
			)); 

            //$this->_clear_cache();
            $this->show_message('add_successed',
                'back_list',    'index.php?app=coupon&act=fufei',
                'continue_add', 'index.php?app=coupon&amp;act=add'
            );
        }
    }
 function _upload_logo($youhui_id)
    {
        $file = $_FILES['youhui_image'];
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES['youhui_image']);//上传logo
        if (!$uploader->file_info())
        {
            $this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=coupon&amp;act=edit&amp;id=' . $youhui_id);
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/mall/coupon', $youhui_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        }
        else
        {
            return false;
        }
    }


 function fufei()
    {
      $user=$this->visitor->get('user_name');
$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	/*echo $city;*/
	 $this->assign('priv_row', $priv_row);
	 $add_time_from=$_GET['add_time_from'];
	 $add_time_to=$_GET['add_time_to'];
	 $yh_name=$_GET['yh_name'];
	 $cond='1=1';
	 
	 if($add_time_from!="")
	 {
		 $cond.=" and start_time>='$add_time_from'  ";
	 }
	 if($add_time_to!="")
	 {
		 $cond.=" and end_time<='$add_time_to 24:59:59'";
	 }
	 
	 if($yh_name!="")
	 {
		 $cond.=" and youhui_name like '%$yh_name%'  ";
	 }

	 $conditions = $this->_get_query_conditions(array(array(
                'field' => 'type',
                'name'  => 'leixing',
                'equal'  => '=',
            ),
			array(
                'field' => 'yhcity',
                'name'  => 'suoshuzhan',
                'equal' => '=',
            ),
			array(
                'field' => 'start_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
               // 'handler'=> 'gmstr2time',
            ),
			array(
                'field' => 'end_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                //'handler'   => 'gmstr2time_end',
			),
			array(
                'field' => 'youhui_name',
                'name'  => 'yh_name',
                'equal' => 'like',
			),
			));
	 $page = $this->_get_page();

	  $sort  = 'youhui_id';
      $order = 'desc';
		if($privs=='all')
		{
        $users = $this->youhuiquan_mod->find(array(
			'conditions' => '1=1 '.$conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
		}
		else
		{
		
		 /* $users = $this->youhuiquan_mod->find(array(
			'conditions' => "yhcity like '%$city,%",
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));*/
		 $users=$this->youhuiquan_mod->getAll("SELECT * " .
                    "FROM " . DB_PREFIX . "youhuiquan " .
                    "WHERE ". $cond ." and yhcity like '%$city,%'" .
				    //"AND rgs.recom_id = '$recom_id'" .
					//"AND g.cityhao = '$city_id' " .
					" ORDER BY youhui_id desc " .
                    " LIMIT {$page['limit']}"
					);	
		
		}
		
		$result=$this->youhuiquan_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$this->assign('result', $result);
		$result=null;
		  $this->assign('users', $users);
        $page['item_count'] = $this->youhuiquan_mod->getCount();
		$this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		 $this->display('youhui.index.html');

    }




    function edit()
    {
		$this->city_mod=& m('city');
        $city=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
        $youhui_id = empty($_GET['id']) ? 0 : $_GET['id'];
 
        if (!$youhui_id)
        {
            $this->show_warning('no_youhuiquan');
            return;
        }
         if (!IS_POST)
        {
            $find_data     = $this->youhuiquan_mod->find($youhui_id);
			$canshu=$this->city_mod->getRow("select * from ".DB_PREFIX."canshu");
            if (empty($find_data))
            {
                $this->show_warning('no_youhuiquan');

                return;
            }
            $youhui    =   current($find_data);
            if ($youhui['youhui_image'])
            {
                $youhui['youhui_image']  =   dirname(site_url()) . "/" . $youhui['youhui_image'];
            }
			
			$str='';
		$yh=explode(',',$youhui['yhcity']);	
		foreach ($city as $val)
		{
			$che='';
			if(in_array($val['city_id'],$yh))
			//if(strpos($youhui['yhcity'],$val['city_id'])) 
			{
				$che='checked';
			}
			$str.='<input type="checkbox" name="fu[]" value="'.$val['city_id'].'" '.$che.'/>'.$val['city_name'];
		}
		$this->assign('city', $str);

            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
           $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('youhui', $youhui);
			$this->assign('canshu', $canshu);
            $this->display('youhui.form.html');
        }
        else
        {
				  $str=  implode( ', ',   $_POST[fu]).','; 

            $data = array();
            $data['youhui_name']     =   $_POST['youhui_name'];
            $data['youhui_jine']     =   $_POST['youhui_jine'];
            $data['start_time']    =   $_POST['start_time'];
            $data['end_time'] = $_POST['end_time'];
			$data['goumai']    =   $_POST['goumai'];
			$data['beizhu']    =   $_POST['beizhu'];
			$data['yhcity']    =   $str;
            $youhui_image               =   $this->_upload_logo($youhui_id);
            $youhui_image && $data['youhui_image'] = $youhui_image;
            if ($youhui_image === false)
            {
                return;
            }
           
            $rows=$this->youhuiquan_mod->edit($youhui_id, $data);
            if ($this->youhuiquan_mod->has_error())
            {
                $this->show_warning($this->youhuiquan_mod->get_error());
                return;
            }

            $this->show_message('edit_successed',
                'back_list',        'index.php?app=coupon&act=fufei',
                'edit_again',    'index.php?app=coupon&amp;act=edit&amp;id=' . $youhui_id);
        }
    }


function youhui_edit()
    {
        $coupon_id = empty($_GET['id']) ? 0 : $_GET['id'];
     // echo $youhui_id;
        if (!$coupon_id)
        {
            $this->show_warning('no_youhuiquan');
            return;
        }
         if (!IS_POST)
        {
            $find_data     = $this->coupon_mod->find($coupon_id);
            if (empty($find_data))
            {
                $this->show_warning('no_youhuiquan');

                return;
            }
                $coupon    =   current($find_data);
            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('coupon', $coupon);
            $this->display('sjyh.form.html');
        }
        else
        {
            $data = array();
            $data['coupon_name']     =   $_POST['coupon_name'];
            $data['coupon_value']     =   $_POST['coupon_value'];
            $data['use_times']    =   $_POST['use_times'];
            $data['end_time'] = $_POST['end_time'];
			$data['store_id']    =   $_POST['store_id'];
			$data['start_time'] = $_POST['start_time'];
			$data['min_amount'] = $_POST['min_amount'];
           
           
            $rows=$this->coupon_mod->edit($coupon_id, $data);
            if ($this->coupon_mod->has_error())
            {
                $this->show_warning($this->coupon_mod->get_error());

                return;
            }

            $this->show_message('edit_successed',
                'back_list',        'index.php?app=coupon',
                'edit_again',    'index.php?app=coupon&amp;act=youhui_edit&amp;id=' . $coupon_id);
        }
    }



    function drop()
    {
        $youhui_ids = isset($_GET['id']) ? trim($_GET['id']) : 0;
        if (!$youhui_ids)
        {
            $this->show_warning('no_such_navigation');

            return;
        }
        $youhui_ids=explode(',',$youhui_ids);
        if (!$this->youhuiquan_mod->drop($youhui_ids))    //删除
        {
            $this->show_warning($this->youhuiquan_mod->get_error());

            return;
        }

        $this->show_message('drop_successed');
    }

 function youhui_drop()
    {
        $coupon_ids = isset($_GET['id']) ? trim($_GET['id']) : 0;
        if (!$coupon_ids)
        {
            $this->show_warning('no_such_navigation');

            return;
        }
        $coupon_ids=explode(',',$coupon_ids);
        if (!$this->coupon_mod->drop($coupon_ids))    //删除
        {
            $this->show_warning($this->coupon_mod->get_error());

            return;
        }

        $this->show_message('drop_successed');
    }


    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = $this->_m_share->getAll();

       if (in_array($column ,array('title', 'sort_order')))
       {
           $data[$id][$column] = $value;
           if($this->_m_share->setAll($data))
           {
               $this->_clear_cache();
               echo ecm_json_encode(true);
           }
       }
       else
       {
           return ;
       }
       return ;
   }

    function _get_share_type()
    {
        return array(
            'share'   => Lang::get('share'),
            'collect' => Lang::get('collect'),
        );
    }

       /**
     *    处理上传标志
     *
     *    @author    Hyber
     *    @param     int $brand_id
     *    @return    string
     */
    
}

?>
