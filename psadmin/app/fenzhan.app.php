<?php

/* 管理员控制器 */
class FenzhanApp extends BackendApp
{
   
	var $city_mod;

    function __construct()
    {
        $this->FenzhanApp();
    }

    function FenzhanApp()
    {
        parent::__construct();
      
		 $this->city_mod = & m('city');
		  $this->member_mod = & m('member');
		  $this->kaiguan_mod =& m('kaiguan');
    }
	 
    function index()
    {
       
	 $page = $this->_get_page();
	  $sort  = 'city_id';
      $order = 'desc';
	
        $city_row = $this->city_mod->find(array(
         
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));

		  $this->assign('city_row', $city_row);
        $page['item_count'] = $this->city_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
		//print_r($page);
        $this->assign('page_info', $page);
		 $this->display('fenzhan.index.html');

    }
	
	 function add()
    {
	
	$user=$this->visitor->get('user_name');
        if (!IS_POST)
        {
          
            $this->display('fenzhan_add.html');
        }
        else
        {
		
			
			if((float)$_POST['recharge']<0 || (float)$_POST['recharge']>0.1)
			{
				$this->show_warning('tianxie');
				return;
			}
            $data = array(
                'city_name' => trim($_POST['city_name']),
				'city_yuming'  => trim($_POST['city_yuming']),
                'city_title'  => trim($_POST['city_title']),
                'city_desc'  => trim($_POST['city_desc']),
				'city_keywords'  => trim($_POST['city_keywords']),
                'lianxiren'  => trim($_POST['lianxiren']),
				'guanliyuan'  => trim($_POST['guanliyuan']),
				'splb'  => trim($_POST['splb']),
				'beizhu'  => trim($_POST['beizhu']),
				'icp_num'  => trim($_POST['icp_num']),
				'lltj'  => trim($_POST['lltj']),
				'qq1'    => trim($_POST['qq1']),
			    'qq2'    => trim($_POST['qq2']),
				'qq3'    => trim($_POST['qq3']),
				'qq4'    => trim($_POST['qq4']),
				'user_id'    => trim($_POST['user_id']),
				'erweima'    => trim($_POST['erweima']),
				'banquan'    => trim($_POST['banquan']),
				'zhifufangshi'    => trim($_POST['zhifufangshi']),
				'tuijianren_id'    => trim($_POST['tuijianren_id']),
				'p2p_url'    => trim($_POST['p2p_url']),
				'mall_url'    => trim($_POST['mall_url']),
				'mall2_url'    => trim($_POST['mall2_url']),
				'city_account'    => trim($_POST['city_account']),
				'recharge'    =>  (float)$_POST['recharge'],
				'phone'    => trim($_POST['phone']),
				'qq_login' =>$_POST['qq_login'],
            );

           if(trim($_POST['city_name'])=="")
		   {
		    $this->show_warning('fenzhanmingchengbunengweikong');
		   return;
		   }
		   if(trim($_POST['city_yuming'])=="")
		   {
		    $this->show_warning('fenzhanyumingbunengweikong');
		   return;
		   }
		  
           if (!$city_id = $this->city_mod->add($data))  //获取brand_id
            {
                $this->show_warning($this->city_mod->get_error());

                return;
            }


            /* 处理上传的图片 */
            $logo       =   $this->_upload_logo($city_id);
            if ($logo === false)
            {
                return;
            }
            
            $logo && $this->city_mod->edit($city_id, array('city_logo' => $logo)); 

			$dongji=Lang::get('dongji');
			$xinkuan=Lang::get('xinkuan');
			$ershoushijie=Lang::get('ershoushijie');
			$jingpin=Lang::get('jingpin');
			$xinpin=Lang::get('xinpin');
			$sql1="INSERT INTO ".DB_PREFIX."recommend (`recom_id`, `recom_name`, `store_id`, `recity`, `keywords`) VALUES (24, '$dongji', 0,'$city_id', 'NULL');";
			$sql2="INSERT INTO ".DB_PREFIX."recommend (`recom_id`, `recom_name`, `store_id`, `recity`, `keywords`) VALUES (23, '$xinkuan', 0, '$city_id', 'NULL');";
			$sql3="INSERT INTO ".DB_PREFIX."recommend (`recom_id`, `recom_name`, `store_id`, `recity`, `keywords`) VALUES (22, '$ershoushijie', 0, '$city_id', 'NULL');";
			$sql4="INSERT INTO ".DB_PREFIX."recommend (`recom_id`, `recom_name`, `store_id`, `recity`, `keywords`) VALUES (21, '$jingpin', 0, '$city_id', 'NULL');";
			$sql5="INSERT INTO ".DB_PREFIX."recommend (`recom_id`, `recom_name`, `store_id`, `recity`, `keywords`) VALUES (20, '$xinpin', 0, '$city_id', 'NULL');";
			$this->city_mod->db->query($sql1);
			$this->city_mod->db->query($sql2);
			$this->city_mod->db->query($sql3);
			$this->city_mod->db->query($sql4);
			$this->city_mod->db->query($sql5);
			
            //$this->_clear_cache();
            $this->show_message('add_successed',
                'back_list',    'index.php?app=fenzhan',
                'continue_add', 'index.php?app=fenzhan&amp;act=add'
            );
        }
    }
 function _upload_logo($city_id)
    {
        $file = $_FILES['city_logo'];
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES['city_logo']);//上传logo
        if (!$uploader->file_info())
        {
            $this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=fenzhan&amp;act=edit&amp;city_id=' . $city_id);
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/mall/city', $city_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        }
        else
        {
            return false;
        }
    }

function status_edit()
	{
	
	$city_id = isset($_GET['city_id']) ? trim($_GET['city_id']) : '';
	
	$status=trim($_POST['status']);
	$beizhu=trim($_POST['beizhu']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'status'=>$status,	
			'beizhu'=>$beizhu,																			
    );
	$this->city_mod->edit('city_id='.$city_id,$edit_kaiguan);
    $this->show_message('caozuochenggong',
    'fanhuiliebiao',    'index.php?app=fenzhan');
	}
	else
	{
	    $logs_data=$this->city_mod->find('city_id='.$city_id);
		$this->assign('log', $logs_data);
        $this->display('fenzhan_kaiguan.html');
	    return;
	}
	}
	
	function edit()
	{
     $city_id = empty($_GET['city_id']) ? 0 : $_GET['city_id'];
	 $pag = empty($_GET['page']) ? 0 : $_GET['page'];
 $row_city=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
  $this->assign('row_city', $row_city);
         if (!IS_POST)
        {
		
            $find_data     = $this->city_mod->find($city_id);
            if (empty($find_data))
            {
                $this->show_warning('no_fenzhan');

                return;
            }
            $city    =   current($find_data);
            if ($city['city_logo'])
            {
                $city['city_logo']  =   dirname(site_url()) . "/" . $city['city_logo'];
            }
            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('city', $city);
            $this->display('fenzhan_edit.html');
        }
        else
        {
		
		/*foreach($_POST[fu] as $var)//通过foreach循环取出多选框中的值
        {
         $str=$str.",".$var;
        }*/


/*echo "<br>".'--------------------------'."<br>";
for ($i=0;$_POST[fu][$i]!="";$i++)//通过for循环取值
{
echo $_POST[fu][$i].'@';
}*/
			if((float)$_POST['recharge']<0 || (float)$_POST['recharge']>0.1)
			{
				$this->show_warning('tianxie');
				return;
			}
		 
            $data = array();
            $data['city_name']     =   $_POST['city_name'];
            $data['city_yuming']     =   $_POST['city_yuming'];
            $data['city_title']    =   $_POST['city_title'];
            $data['city_desc'] = $_POST['city_desc'];
			$data['city_keywords'] = $_POST['city_keywords'];
			$data['status']    =   $_POST['status'];
			$data['lianxiren']    =   $_POST['lianxiren'];
			$data['guanliyuan']    =   $_POST['guanliyuan'];
			$data['splb']    =   $_POST['splb'];
			$data['zjf']    =   $_POST['zjf'];
			$data['icp_num']    =   $_POST['icp_num'];
			$data['huiyuan']    =    $_POST['huiyuan'];
			$data['lltj']    =    $_POST['lltj'];
			$data['qq1']    =    $_POST['qq1'];
			$data['qq2']    =    $_POST['qq2'];
			$data['qq3']    =    $_POST['qq3'];
			$data['qq4']    =    $_POST['qq4'];
			$data['user_id']    =    $_POST['user_id'];
			$data['banquan']    =    $_POST['banquan'];
			//$data['zhifufangshi']    =    $_POST['zhifufangshi'];
			$data['tuijianren_id']    =    $_POST['tuijianren_id'];
			$data['p2p_url']     =   $_POST['p2p_url'];
			$data['mall_url']     =   $_POST['mall_url'];
			$data['mall2_url']     =   $_POST['mall2_url'];
			$data['city_account']    =    $_POST['city_account'];
			$data['recharge']    =    (float)$_POST['recharge'];
			$data['phone']    = $_POST['phone'];
			$data['qq_login']    = $_POST['qq_login'];
			/*$data['erweima']    =    $_POST['erweima'];*/
            $city_logo               =   $this->_upload_logo($city_id);
            $city_logo && $data['city_logo'] = $city_logo;
            if ($city_logo === false)
            {
                return;
            }
           
            $rows=$this->city_mod->edit($city_id, $data);
            if ($this->city_mod->has_error())
            {
                $this->show_warning($this->city_mod->get_error());

                return;
            }

            $this->show_message('edit_successed',
                'back_list',        'index.php?app=fenzhan&amp;page= '. $pag,
                'edit_again',    'index.php?app=fenzhan&amp;act=edit&amp;city_id=' . $city_id);
        }
    }

    function drop()
    {
     
        $city_ids = isset($_GET['id']) ? trim($_GET['id']) : 0;
        if (!$city_ids)
        {
            $this->show_warning('no_such_navigation');

            return;
        }
        $city_ids=explode(',',$city_ids);
        if (!$this->city_mod->drop($city_ids))    //删除
        {
            $this->show_warning($this->city_mod->get_error());

            return;
        }

        $this->show_message('drop_successed');
    }
	
	function web()
    {
	$log_id=1;
	$webservice=trim($_POST['webservice']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'webservice'=>$webservice,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('caozuochenggong',
    'fanhui',    'index.php?app=fenzhan&act=web');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('webservice.html');
	    return;
	}
	}		
   
}

?>
