<?php

/**
 *    商品分享管理控制器
 *
 *    @author    Hyber
 *    @usage    none
 */
class Site_systemApp extends BackendApp
{
    var $_m_share;

    function __construct()
    {
        $this->Site_systemApp();
    }

    function Site_systemApp()
    {
        parent::BackendApp();

        $this->site_system_mod =& m('site_system');
		$this->site_advtype_mod =& m('site_advtype');
		$this->site_skin_mod =& m('site_skin');
    }

  
    function index()
    {
    $userid=$this->visitor->get('user_id');
	 $page = $this->_get_page();
	 $site_type = $this->site_advtype_mod->find(array(
		 'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true
        ));
	 $this->assign('site_type', $site_type);
	 $page['item_count'] = $this->site_advtype_mod->getCount();
     $this->_format_page($page);
     $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
     $this->assign('page_info', $page);
    $this->display('site.advtype.html');
    }

	
 	function type_add()
    {
	     
		 $riqi=date('Y-m-d H:i:s');
        if (!IS_POST)
        {
			$res=$this->site_advtype_mod->getAll("select * from ".DB_PREFIX."site_skin ");
	        $this->assign('res', $res);
	
		    $this->display('site.type.html');
		}
		 else
        {
		
		$type=trim($_POST['type']);
            $data = array(
                'type'  => trim($_POST['type']),
                'danjia'  => trim($_POST['danjia']), 
				'code'  => trim($_POST['code']), 
            );
			$this->site_advtype_mod->add($data);
            $this->show_message('add_successed',
                'back_list',    'index.php?app=site_system',
                'continue_add', 'index.php?app=site_system&amp;act=type_add'
            );
        }
    }   
 	
	function type_edit()
    {
        $id = empty($_GET['id']) ? 0 : $_GET['id'];
         if (!IS_POST)
        {
		
			$res=$this->site_advtype_mod->getAll("select * from ".DB_PREFIX."site_skin ");
	        $this->assign('res', $res);
            $adv=$this->site_advtype_mod->getrow("select * from ".DB_PREFIX."site_advtype where id = '$id'");
	        $this->assign('adv', $adv);
            $this->display('site.type.html');
        }
        else
        {
            $data = array();
            $data['type']     =   $_POST['type'];
			$data['danjia']     =   $_POST['danjia'];
			$data['code']     =   $_POST['code'];
            $rows=$this->site_advtype_mod->edit($id, $data);
            $this->show_message('edit_successed',
                'back_list',        'index.php?app=site_system',
                'edit_again',    'index.php?app=site_system&amp;act=type_edit&amp;id=' . $id);
        }
    }
   
    function drop()
    {
        $adv_id = isset($_GET['id']) ? trim($_GET['id']) : 0;
        
       
        if (!$this->site_advtype_mod->drop($adv_id))    //删除
        {
            $this->show_warning($this->site_advtype_mod->get_error());

            return;
        }

        $this->show_message('drop_successed');
    }

   function site_skin()
    {
    $userid=$this->visitor->get('user_id');
	 $page = $this->_get_page();
	 $site_skin = $this->site_skin_mod->find(array(
		 	'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true
        ));
	 $this->assign('site_skin', $site_skin);
	 $page['item_count'] = $this->site_skin_mod->getCount();
     $this->_format_page($page);
     $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
     $this->assign('page_info', $page);
    $this->display('site.skin.html');
    }
 
	function skin_add()
    {
	     
		$riqi=date('Y-m-d H:i:s');
        if (!IS_POST)
        {
			
	
		    $this->display('site.skinadd.html');
		}
		 else
        {
		
			$type=trim($_POST['type']);
            $data = array(
                'code'  => trim($_POST['code']),
            );
			$id=$this->site_skin_mod->add($data);
			
			$logo       =   $this->_upload_logo($id,'image');
			if ($logo === false)
			{
				return;
			}
			$logo && $this->site_skin_mod->edit($id, array('image' => $logo));
		
            $this->show_message('add_successed',
                'back_list',    'index.php?app=site_system&act=site_skin',
                'continue_add', 'index.php?app=site_system&amp;act=skin_add'
            );
        }
    }   
 	
	
	
	function skin_edit()
	{
		if($_POST)
		{
			$id=$_POST['id'];
			$data=array(
						'code'=>$_POST['code'],
						);
			$this->site_skin_mod->edit('id='.$id,$data);
					
			$logo       =   $this->_upload_logo($id,'image');
			if ($logo === false)
			{
				return;
			}
			$logo && $this->site_skin_mod->edit($id, array('image' => $logo));
			$this->show_message('edit_successed','','index.php?app=site_system&act=site_skin');
			
		}
		else
		{
			$id=$_GET['id'];
			$row=$this->site_skin_mod->getRow("select * from ".DB_PREFIX."site_skin where id='$id' limit 1");		
			$this->assign('row',$row);
			$this->display('site.skinadd.html');
		}
	} 
	
	 function skin_drop()
    {
       $adv_id = isset($_GET['id']) ? trim($_GET['id']) : 0;
        
        if (!$this->site_skin_mod->drop($adv_id))    //删除
        {
            $this->show_warning($this->site_skin_mod->get_error());
            return;
        }
        $this->show_message('drop_successed');

    }
	
	
	function site_sys()
    {
    $userid=$this->visitor->get('user_id');
	 $page = $this->_get_page();
	 $site_sys = $this->site_system_mod->find(array(
		 'limit' => $page['limit'],
            'order' => "user_id desc",
            'count' => true
        ));
	 $this->assign('site_sys', $site_sys);
	 $page['item_count'] = $this->site_system_mod->getCount();
     $this->_format_page($page);
     $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
     $this->assign('page_info', $page);
    $this->display('site.system.html');
    }
	
	
	function sys_edit()
	{
		if($_POST)
		{
			$id=$_POST['id'];
			$data=array(
						'name'=>$_POST['name'],
						'yuming'=>$_POST['yuming'],
						'address'=>$_POST['address'],
						'tel'=>$_POST['tel'],
						'icp'=>$_POST['icp'],
						'fax'=>$_POST['fax'],
						'banquan'=>$_POST['banquan'],
						'rexian'=>$_POST['rexian'],
						'status'=>$_POST['status'],
						);
			$this->site_system_mod->edit('user_id='.$id,$data);
					
			
			$this->show_message('edit_successed','','index.php?app=site_system&act=site_sys');
			
		}
		else
		{
			$id=$_GET['id'];
			$row=$this->site_system_mod->getRow("select * from ".DB_PREFIX."site_system where user_id='$id' limit 1");		
			$this->assign('row',$row);
			$this->display('site.sysedit.html');
		}
	} 
	
	
 function _upload_logo($user_id,$can)
    {
        $file = $_FILES[$can];
		$riqi=time().rand(100,999);
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES[$can]);//上传logo
		
        if (!$uploader->file_info())
        {
            $this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=site_system&act=site_skin');
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/site_skin', $riqi.$user_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
			return $file_path;
        }
        else
        {
            return false;
        }
    } 
	
	

}

?>
