<?php

/**
 *    导航管理控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class CompanyApp extends MemberbaseApp
{

    function __construct()
    {
        $this->CompanyApp();
    }

    function CompanyApp()
    {
        parent::__construct();
        $this->site_news_mod = &m('site_news');
		$this->site_system_mod = &m('site_system');
		$this->site_advt_mod = &m('site_advt');
		$this->site_link_mod = &m('site_link');
    }

    function index()
    {
		$user_id=$this->visitor->get('user_id');
		$row=$this->site_system_mod->getRow("select * from ".DB_PREFIX."site_system where user_id='$user_id' limit 1");
		
		$this->assign('row',$row);
		if(!$_POST)
		{
			 
            $resl=$this->site_system_mod->getAll("select * from ".DB_PREFIX."site_skin ");
		    /* 当前页面信息 */
            $this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('jieshao'));
            $this->_curitem('jieshao');
            $this->_curmenu('jieshao');
            $this->import_resource('jquery.plugins/jquery.validate.js,mlselection.js');
           
            $this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('jieshao'));
			$this->assign('resl',$resl);
			$this->display('company_jieshao.html');
		}
		else
		{
				
				$data=array(
					'user_id'=>$user_id,
					'yuming'=>$_POST['yuming'],
					'name'=>$_POST['name'],
					'address'=>$_POST['address'],
					'fax'=>$_POST['fax'],
					'tel'=>$_POST['tel'],
					'icp'=>$_POST['icp'],
					'banquan'=>$_POST['banquan'],
					'tongji'=>$_POST['tongji'],
					'code'=>$_POST['code'],
					'rexian'=>$_POST['rexian'],
					'qq'=>$_POST['qq']
					);
				if(empty($row))
				{
					$data['status']=0;
					$this->site_system_mod->add($data);
				}
				else
				{
				
					$this->site_system_mod->edit('user_id='.$user_id,$data);
					
				}
				$logo       =   $this->_upload_logo($user_id,'logo');
				if ($logo === false)
				{
					return;
				}
				$logo && $this->site_system_mod->edit($user_id, array('logo' => $logo));
				$this->show_message('xiugai');
			}
	
    }
	
	function news()
	{
			$user_id=$this->visitor->get('user_id');
			$type=$_GET['type'];
			if($type==5)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('fengcai'));
				$this->_curitem('fengcai');
            	$this->_curmenu('fengcai');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('fengcai'));
			}
			if($type==6)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('news'));
				$this->_curitem('news');
            	$this->_curmenu('news');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('news'));
			}
		
		$page = $this->_get_page(10);
		
		$news = $this->site_news_mod->findAll(array(
            'conditions'    => "status!=-1 and categoryid=$type and user_id=$user_id ",
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'id DESC',
           
        ));
		
		$page['item_count'] = $this->site_news_mod->getCount();
		$this->_format_page($page);
        $this->assign('page_info', $page);
		$this->assign('news',$news);
		
		$this->assign('type',$type);
		$this->display('company_news.html');
	}
	
	
	function add_news()
	{
		$user_id=$this->visitor->get('user_id');
		$type=$_GET['type'];
		$this->assign('type',$type);
		if(!$_POST)
		{
           
            /* 当前页面信息 */
			if($type==5)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('fengcai'));
				$this->_curitem('fengcai');
            	$this->_curmenu('fengcai');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('fengcai'));
			}
			if($type==6)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('news'));
				$this->_curitem('news');
            	$this->_curmenu('news');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('news'));
			}
			
            $this->import_resource('jquery.plugins/jquery.validate.js,mlselection.js');

			$this->display('company_fengcai.html');
			}
			else
			{
				$data=array(
					'user_id'=>$user_id,
					'categoryid'=>$type,
					'createdate'=>date('Y-m-d H:i:s'),
					'content'=>$_POST['content'],
					'status'=>1,
					'showtimes'=>1,
					'title'=>$_POST['title'],
					);

				$id=$this->site_news_mod->add($data);
				if($type==5)
				{
					$logo       =   $this->_upload_logo($id,'image');
					if ($logo === false)
					{
						return;
					}
					$logo && $this->site_news_mod->edit($id, array('image' => $logo));
				}
				
				$this->show_message('tianjia','','index.php?app=company&act=news&type='.$type);
				
			}
	
	}
	
	function news_edit()
	{
		$user_id=$this->visitor->get('user_id');
		
		$id=$_GET['id'];
		$row=$this->site_news_mod->getRow("select * from ".DB_PREFIX."site_news where id='$id' limit 1");
		$type=$row['categoryid'];
		$this->assign('row',$row);
		
		$this->assign('type',$type);
		
		if(!$_POST)
		{
           
            /* 当前页面信息 */
			if($type==5)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('fengcai'));
				$this->_curitem('fengcai');
            	$this->_curmenu('fengcai');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('fengcai'));
			}
			if($type==6)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('news'));
				$this->_curitem('news');
            	$this->_curmenu('news');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('news'));
			}
			
			
            $this->import_resource('jquery.plugins/jquery.validate.js,mlselection.js');

			$this->display('company_fengcai.html');
			}
			else
			{
				$data=array(
					'content'=>$_POST['content'],
					'title'=>$_POST['title'],
					);
				$ty=$_POST['categoryid'];
				$this->site_news_mod->edit('id='.$id,$data);
				
				if($ty==5)
				{
					$logo       =   $this->_upload_logo($id,'image');
					if ($logo === false)
					{
						return;
					}
					$logo && $this->site_news_mod->edit($id, array('image' => $logo));
				}
				
				
				
				$this->show_message('xiugai','','index.php?app=company&act=news&type='.$ty);
				
			}
	}
	

    /**
     *    添加地址
     *
     *    @author    Garbin
     *    @return    void
     */
    function fenlei()
    {
		$user_id=$this->visitor->get('user_id');
		$type=$_GET['type'];
		
		$row=$this->site_news_mod->getRow("select * from ".DB_PREFIX."site_news where user_id='$user_id' and  categoryid='$type' and status!=-1 limit 1");
		$this->assign('row',$row);
		
		$this->assign('type',$type);
		if(!$_POST)
		{
           
            /* 当前页面信息 */
            
            if($type==1)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('abountus'));
				$this->_curitem('abountus');
            	$this->_curmenu('abountus');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('abountus'));
			}
			if($type==2)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('zizhi'));
				$this->_curitem('zizhi');
            	$this->_curmenu('zizhi');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('zizhi'));
			}
			if($type==3)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('licheng'));
				$this->_curitem('licheng');
            	$this->_curmenu('licheng');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('licheng'));
			}
			if($type==4)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('zhaopin'));
				$this->_curitem('zhaopin');
            	$this->_curmenu('zhaopin');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('zhaopin'));
			}
			if($type==7)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('contact'));
				$this->_curitem('contact');
            	$this->_curmenu('contact');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('contact'));
			}
			if($type==8)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('notice'));
				$this->_curitem('notice');
            	$this->_curmenu('notice');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('notice'));
			}
			if($type==9)
			{
				$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('rongyu'));
				$this->_curitem('rongyu');
            	$this->_curmenu('rongyu');
				$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('rongyu'));
			}
			
            $this->import_resource('jquery.plugins/jquery.validate.js,mlselection.js');

			$this->display('company_fenlei.html');
		}
		else
		{
				$id=$_POST['id'];
					$data=array(
						'user_id'=>$user_id,
						'categoryid'=>$type,
						'createdate'=>date('Y-m-d H:i:s'),
						'content'=>$_POST['content'],
						'status'=>1,
						'showtimes'=>1
						);
				if(empty($row))
				{
					
					$this->site_news_mod->add($data);
					$this->show_message('tianjia');
				}
				else
				{
					
					unset($data['createdate']);
					unset($data['showtimes']);
					unset($data['categoryid']);
					unset($data['user_id']);
					$this->site_news_mod->edit('id='.$id,$data);
					$this->show_message('xiugai');
				}
				
					
				
			}
	
    }
   
   	function adv()
	{
		$user_id=$this->visitor->get('user_id');
		$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('advt'));
		$this->_curitem('advt');
        $this->_curmenu('advt');
		$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('advt'));
		
		
		$page = $this->_get_page(10);
		
		$advt = $this->site_advt_mod->findAll(array(
            'conditions'    => "status!=-1 and user_id='$user_id' ",
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'id DESC',
           
        ));
		
		$city_row=array();
		$advtype=$this->site_advt_mod->getAll("select * from ".DB_PREFIX."site_advtype ");
		foreach ($advtype as $var )
		{
		    $city_row[$var['id']]=$var['type'];
		}
		
		foreach($advt as $key=>$var)
		{
			$advt[$key]['typename']=$city_row[$var['typeid']];
		}
		
		$page['item_count'] = $this->site_advt_mod->getCount();
		$this->_format_page($page);
        $this->assign('page_info', $page);
		$this->assign('advt',$advt);
		
		$this->display('company_advt.html');
	} 
	
	function add_adv()
	{
		
		$user_id=$this->visitor->get('user_id');
		$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('advt'));
		$this->_curitem('advt');
        $this->_curmenu('advt');
		$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('advt'));
		
		
		 $this->import_resource(array(
            'script' => array(
      
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
		
		
		
		if($_POST)
		{
			
			if(empty($_POST['typeid']))
			{
				$this->show_warning('guanggaoleixing');
				return;
			}
			/*if(empty($_POST['code']))
			{
				$this->show_warning('shezhemoban');
				return;
			}*/
			
			$data=array(
						'title'=>$_POST['title'],
						'link'=>$_POST['link'],
						'addtime'=>$_POST['addtime'],
						'endtime'=>$_POST['endtime'],
						'beizhu'=>$_POST['beizhu'],
						'typeid'=>$_POST['typeid'],
						'status'=>1,
						'user_id'=>$user_id,
						'code'=>$_POST['code']
						);
			$id=$this->site_advt_mod->add($data);
					
			$logo       =   $this->_upload_logo($id,'image');
			if ($logo === false)
			{
				return;
			}
			$logo && $this->site_advt_mod->edit($id, array('image' => $logo));
			$this->show_message('tianjia','','index.php?app=company&act=adv');
			
		}
		else
		{
			$one=$this->site_advt_mod->getRow("select code from ".DB_PREFIX."site_system where user_id='$user_id' limit 1 ");
			$code=$one['code'];
			$res=$this->site_advt_mod->getAll("select * from ".DB_PREFIX."site_advtype");
			$this->assign('res',$res);
			$this->assign('one',$one);
			$this->display('company_advadd.html');
		}
	} 
	
	function advedit()
	{
		$user_id=$this->visitor->get('user_id');
		$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('news'));
		$this->_curitem('news');
        $this->_curmenu('news');
		$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('news'));
		
		

		if($_POST)
		{
			
			if(empty($_POST['code']))
			{
				$this->show_warning('guanggaoleixing');
				return;
			}
			/*if(empty($_POST['code']))
			{
				$this->show_warning('shezhemoban');
				return;
			}*/
			
			$id=$_POST['id'];
			$data=array(
						'title'=>$_POST['title'],
						'link'=>$_POST['link'],
						'addtime'=>$_POST['addtime'],
						'endtime'=>$_POST['endtime'],
						'beizhu'=>$_POST['beizhu'],
						'typeid'=>$_POST['typeid'],
						'status'=>1,
						'user_id'=>$user_id,
						'code'=>$_POST['code']
						);
			$this->site_advt_mod->edit('id='.$id,$data);
					
			$logo       =   $this->_upload_logo($id,'image');
			if ($logo === false)
			{
				return;
			}
			$logo && $this->site_advt_mod->edit($id, array('image' => $logo));
			$this->show_message('xiugai','','index.php?app=company&act=adv');
			
		}
		else
		{
			
			$this->import_resource(array(
            'script' => array(
      
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
		
			
			$id=$_GET['id'];
			$row=$this->site_advt_mod->getRow("select * from ".DB_PREFIX."site_advt where id='$id' limit 1");
			$this->assign('row',$row);
			
			$one=$this->site_advt_mod->getRow("select code from ".DB_PREFIX."site_system where user_id='$user_id' limit 1 ");
			$code=$one['code'];
			$res=$this->site_advt_mod->getAll("select * from ".DB_PREFIX."site_advtype ");
			$this->assign('res',$res);
			$this->assign('one',$one);
			$this->display('company_advadd.html');
		}
	} 
   
   
    function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : 0;
		$riqi=date('Y-m-d H:i:s');
        if (!$id)
        {
            $this->show_warning('no-article');
            return;
        }
        $sql="update ".DB_PREFIX."site_news set status=-1,deltime='$riqi' where id=$id limit 1";
		$this->site_news_mod->db->query($sql);
        $this->show_message('drop_successed');
    }


	function adv_drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : 0;
		$riqi=date('Y-m-d H:i:s');
        if (!$id)
        {
            $this->show_warning('no-adv');
            return;
        }
        $sql="update ".DB_PREFIX."site_advt set status=-1,deltime='$riqi' where id=$id limit 1";
		$this->site_advt_mod->db->query($sql);
        $this->show_message('drop_successed');
    }
	
	function part()
	{
	
		$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('link'));
		$this->_curitem('link');
        $this->_curmenu('link');
		$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('link'));
		$user_id=$this->visitor->get('user_id');
		
		$page = $this->_get_page(10);
		
		$link = $this->site_link_mod->findAll(array(
            'conditions'    => 'store_id='.$user_id,
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'id DESC',
           
        ));
		
		$page['item_count'] = $this->site_link_mod->getCount();
		$this->_format_page($page);
        $this->assign('page_info', $page);
		$this->assign('link',$link);
		
		
		$this->display('company_link.html');
	} 
	
	
	function add_link()
	{
		
		$user_id=$this->visitor->get('user_id');
		$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('link'));
		$this->_curitem('link');
        $this->_curmenu('link');
		$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('link'));
		
		
		if($_POST)
		{
			$data=array(
						'title'=>$_POST['title'],
						'link'=>$_POST['link'],
						'riqi'=>date('Y-m-d H:i:s'),
						'store_id'=>$user_id
						);
			$id=$this->site_link_mod->add($data);
					
			$logo       =   $this->_upload_logo($id,'logo');
			if ($logo === false)
			{
				return;
			}
			$logo && $this->site_link_mod->edit($id, array('logo' => $logo));
			$this->show_message('tianjia','','index.php?app=company&act=part');
			
		}
		else
		{
			$this->display('company_linkadd.html');
		}
	} 
	
	
	
	function link_edit()
	{
		$user_id=$this->visitor->get('user_id');
		$this->_curlocal(LANG::get('im_qiye'), 'index.php?app=company', LANG::get('link'));
		$this->_curitem('link');
        $this->_curmenu('link');
		$this->assign('page_title', Lang::get('im_qiye') . ' - ' . Lang::get('link'));
		
		

		if($_POST)
		{
			$id=$_POST['id'];
			$data=array(
						'title'=>$_POST['title'],
						'link'=>$_POST['link'],
						'store_id'=>$user_id
						);
			$this->site_link_mod->edit('id='.$id,$data);
					
			$logo       =   $this->_upload_logo($id,'logo');
			if ($logo === false)
			{
				return;
			}
			$logo && $this->site_link_mod->edit($id, array('logo' => $logo));
			$this->show_message('xiugai','','index.php?app=company&act=part');
			
		}
		else
		{
			$id=$_GET['id'];
			$row=$this->site_link_mod->getRow("select * from ".DB_PREFIX."site_link where id='$id' limit 1");		
			$this->assign('row',$row);
			$this->display('company_linkadd.html');
		}
	} 
   
   function link_drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : 0;
		$riqi=date('Y-m-d H:i:s');
        if (!$id)
        {
            $this->show_warning('no-link');
            return;
        }
        $sql="delete from ".DB_PREFIX."site_link  where id=$id ";
		$this->site_link_mod->db->query($sql);
        $this->show_message('drop_successed');
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
            $this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=company');
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/store_' . $this->visitor->get('user_id'), $riqi.$user_id))   //保存到指定目录，并以指定文件名$brand_id存储
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