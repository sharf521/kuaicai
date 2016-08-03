<?php

/* 店铺控制器 */
class Article_stoApp extends BackendApp
{
    
	function __construct()
    {
        $this->Article_stoApp();
    }

    function Article_stoApp()
    {
        parent::BackendApp();

        $this->article_sto_mod =& m('article_sto');
        $this->_uploadedfile_mod = &m('uploadedfile');
		$this->_city_mod =& m('city');
		$this->member_mod =& m('member');
		$this->userpriv_mod =& m('userpriv');
    }
	

    function index()
    {
	
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->article_sto_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$conditions=" and 1=1 ";
	$type=$_GET['type'];
	$suoshuzhan=$_GET['suoshuzhan'];
	$title=$_GET['title'];
	$this->assign('title',$title);
	$this->assign('suoshuzhan',$suoshuzhan);
	$this->assign('type',$type);
	 $page   =   $this->_get_page(10);   //获取分页信息
	if(!empty($type))
	{
		$conditions.=" and type='$type'";
	}
	if(!empty($suoshuzhan))
	{
		$conditions.=" and city='$suoshuzhan'";
	}
	if(!empty($title))
	{
		$conditions.=" and title like '%$title%'";
	}
	if($privs=='all')
	{
		$artic=$this->article_sto_mod->getAll("select * from ".DB_PREFIX."article_sto where 1=1 ". $conditions ."limit {$page['limit']}");
		
		$ar=$this->article_sto_mod->getAll("select article_id from ".DB_PREFIX."article_sto where 1=1 ". $conditions);
	}
	else
	{
		$artic=$this->article_sto_mod->getAll("select * from ".DB_PREFIX."article_sto where city='$city' ".$conditions ."limit {$page['limit']}");
		$ar=$this->article_sto_mod->getAll("select article_id from ".DB_PREFIX."article_sto where city='$city' ".$conditions);
	}	
		$city_row=array();
		$result=$this->article_sto_mod->getAll("select * from ".DB_PREFIX."city");
		$this->assign('result',$result);
		foreach ($result as $var )
		{
			$row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		foreach ($artic as $key => $val)
        {
			$artic[$key]['city_name'] = $city_row[$val['city']];
			$storeid=$val['store_id'];	
			$sto=$this->article_sto_mod->getRow("select store_name from ".DB_PREFIX."store where store_id='$storeid' limit 1");
			$artic[$key]['store_name']=$sto['store_name'];
        }
		 
		$page['item_count']=count($ar);
		$this->_format_page($page);
		 $this->assign('page_info', $page);	
		$this->assign('artic',$artic);
		$this->assign('priv_row',$priv_row);
		$this->display('article_sto.index.html');
	}
	function add()
	{
	
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
       
		if(!$_POST)
		{
		 $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            
            //$this->assign('files_belong_article', $files_belong_article);
         $this->assign('build_editor', $this->_build_editor(array('name' => 'content')));
         $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_ARTICLE, 'item_id' => 0))); // 构建swfupload上传组件
		
		$this->display('articlesto.form.html');
		}
		else
		{
			$store_id=$_GET['id'];
			$cityid=$_GET['cityid'];
			$data = array();
            $data['title']      =   $_POST['title'];
			$data['store_id']      =   $store_id;
            $data['content'] =   $_POST['content'];
            $data['add_time']   =  date('Y-m-d H:i:s');
			$data['city']        =   $cityid;
			$data['type']   =   $_POST['type'];
			
			$this->article_sto_mod->add($data);
			$this->show_message('tianjiaziliao','fanhui','index.php?app=store');

		}
	}
	function edit()
	{
		$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$pag = empty($_GET['page']) ? 0 : intval($_GET['page']);
        if (!$article_id)
        {
            $this->show_warning('no_such_article');
            return;
        }
         if (!IS_POST)
        {
            $find_data     = $this->article_sto_mod->find($article_id);
            if (empty($find_data))
            {
                $this->show_warning('no_such_article');
                return;
            }
            $article    =   current($find_data);
            
            $this->assign("id", $article_id);
            $this->assign("belong", BELONG_ARTICLE);
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $this->assign('article', $article);
            $this->assign('build_editor', $this->_build_editor(array('name' => 'content')));
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_ARTICLE, 'item_id' => $article_id))); // 构建swfupload上传组件
            $this->display('articlesto.form.html');
        }
        else
        {
            $data = array();
            $data['title']          =   $_POST['title'];
            $data['type']          =   $_POST['type'];
            
            $data['content']        =   $_POST['content'];

            $rows=$this->article_sto_mod->edit($article_id, $data);
            if ($this->article_sto_mod->has_error())
            {
                $this->show_warning($this->_article_mod->get_error());

                return;
            }

            $this->show_message('edit_article_successed',
                'back_list',        'index.php?app=article_sto ',
                'edit_again',    'index.php?app=article_sto&amp;act=edit&amp;id=' . $article_id);
        }
	}
	
	function drop()
	{
		$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$sql="delete from ".DB_PREFIX."article_sto where article_id = '$article_id'";
		$this->article_sto_mod->db->query($sql);
		$this->show_message('delete','fanhui','index.php?app=article_sto');
		
	}
}

?>
