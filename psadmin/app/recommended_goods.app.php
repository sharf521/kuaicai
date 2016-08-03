<?php

/* 管理员控制器 */
class Recommended_goodsApp extends BackendApp
{
    var $_admin_mod;
    var $_user_mod;
	var $_recommended_mod;

    function __construct()
    {
        $this->Recommended_goodsApp();
    }

    function Recommended_goodsApp()
    {
        parent::__construct();
        $this->_admin_mod = & m('userpriv');
        $this->_user_mod = & m('member');
		 $this->_recommended_mod = & m('recommendedgoods');
    }
    function index()
    {
        $conditions = ' AND store_id = 0';
        //更新排序
        $sort  = 'userpriv.user_id';
        $order = 'asc';
        $page = $this->_get_page();
        $admin_info = $this->_admin_mod->find(array(
            'conditions' => '1=1' . $conditions,
            'join' => 'mall_be_manage',
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
        $page['item_count'] = $this->_admin_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info',$page);
        $this->assign('admins',$admin_info);
        $this->display('admin.index.html');
    }
    function drop()
    {
        $id = (isset($_GET['id']) && $_GET['id'] !='') ? trim($_GET['id']) : '';
        //判断是否选择管理员
        $ids = explode(',',$id);
        if (!$id||$this->_admin_mod->check_admin($id))
        {
            $this->show_warning('choose_admin');
            return;
        }
        //判断是否是系统初始管理员
        if ($this->_admin_mod->check_system_manager($id))
        {
            $this->show_warning('system_admin_drop');
            return;
         }
         //删除管理员
        $conditions = "store_id = 0 AND user_id " . db_create_in($ids);
        if (!$res = $this->_admin_mod->drop($conditions))
        {
            $this->show_warning('drop_failed');
            return;
        }
        $this->show_message('drop_ok', 'admin_list', 'index.php?app=admin');
    }
	
	
	function fenzhan()
	{
	
	    $sort  = 'city_id';
        $order = 'asc';
        $page = $this->_get_page();
	/*	$city=1;*/
		$index=$this->_city_mod->find(array(
/*	'conditions' => "city_id = '$city'",*///条件
	'limit' => $page['limit'],
	'order' => "$sort $order",
	'count' => true));	
	 $this->assign('index',$index);
	 $this->display('fenzhan.index.html');
	return;
	}
	
	function addfz()
    {
	   if($_POST)
	   {
	   $city_name= trim($_POST['city_name']);
	   $city_yuming= trim($_POST['city_yuming']);
	   $city_title= trim($_POST['city_title']);
	   $city_desc= trim($_POST['city_desc']);
	   $city_logo= trim($_POST['city_logo']);
	   $lianxiren= trim($_POST['lianxiren']);
	    $guanliyuan= trim($_POST['guanliyuan']);
	  // $time_edit= trim($_POST['time_edit']);
	   $beizhu= trim($_POST['beizhu']);	   
	   
	   
/*$money_row=$this->my_money_mod->getrow("select * from ".DB_PREFIX."my_money where user_name='$user_name'");	
$user_ids=$money_row['user_id'];  
$my_money=$money_row['money'];
	  $dq_time=date("Y-m-d-His",time());
	   */
	  
	   //写入LOG记录
	   $city_array=array(
	   'city_name'=>$city_name,
	   'city_yuming'=>$city_yuming,
	   'city_title'=>$city_title,
	   'city_desc'=>$city_desc,
	   'city_logo'=>$city_logo,
	   'lianxiren'=>$lianxiren,
	   'guanliyuan'=>$guanliyuan,
	   );
	   
	   $this->_city_mod->add($city_array);
	/*   $this->my_money_mod->edit('user_id='.$user_ids,$money_array);*/

	   		$this->show_message('zengjiafenzhanchenggong','fanhuiliebiao','index.php?app=admin&act=fenzhan');
	        return;
	   }
	   else
	   {
	   $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
	   $user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';
	   /*if(!empty($user_id))
       {
       $index=$this->my_money_mod->find('user_id='.$user_id);
	   }*/
	   $this->assign('index', $index);
       $this->display('fenzhan_add.html'); 
	   }
	   
	   return;
	}
	
	function recommendedit()
	{
	    
	   if($_POST)
	   {
	   $city_id= trim($_POST['city_id']);
	   echo $city_id;
	  
	   $city_name= trim($_POST['city_name']);
	   $city_yuming= trim($_POST['city_yuming']);
	
	 $city_title= trim($_POST['city_title']);
	   $city_desc= trim($_POST['city_desc']);
	   echo $bank_name;
	   $city_logo= trim($_POST['city_logo']);
	   echo $bank_username;
	   $lianxiren= trim($_POST['lianxiren']);
	   $guanliyuan= trim($_POST['guanliyuan']);
	   
	  $city_array=array(
	   'city_name'=>$city_name,
	   'city_yuming'=>$city_yuming,
	   'city_title'=>$city_title,
	   'city_desc'=>$city_desc,
	   'city_logo' =>$city_logo,
	   'lianxiren'=>$lianxiren,
	   'guanliyuan'=>$guanliyuan, 
	  
	   );
	   
	   $this->_city_mod->edit('city_id='.$city_id,$city_array);

	   		$this->show_message('bianjichenggong','fanhuiliebiao','index.php?app=admin&amp;act=fenzhan');
	        return;
	   }
	   else
	   {
	   $id = empty($_GET['city_id']) ? 0 : intval($_GET['city_id']);
	   $city_id = isset($_GET['city_id']) ? trim($_GET['city_id']) : '';
	   $city_name = isset($_GET['city_name']) ? trim($_GET['city_name']) : '';
	   if(!empty($id))
       {
       $index=$this->_city_mod->find('city_id='.$city_id);
	   }
	   $this->assign('index', $index);
       $this->display('fenzhan_edit.html'); 
	   }
	   return;
	}
	
	
	
    function city_drop()
    {
        $city_id = isset($_GET['city_id']) ? trim($_GET['city_id']) : '';
        if (!$city_id)
        {
            $this->show_warning('no_fenzhan_to_drop');
            return;
        }
        $ids = explode(',', $city_id);
        $conditions = "city_id " . db_create_in($ids);
        if (!$res = $this->_city_mod->drop($conditions))
        {
            $this->show_warning('drop_failed');
            return;
        }
        $this->show_message('drop_ok', 'fanhuiliebiao', 'index.php?app=admin&amp;act=fenzhan');
    }
	
	
	
	
	function status_edit()
	{
	
	$city_id = isset($_GET['city_id']) ? trim($_GET['city_id']) : '';
	$city_name = isset($_GET['city_name']) ? trim($_GET['city_name']) : '';
	$status=trim($_POST['status']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'status'=>$status,																				
    );
	$this->_city_mod->edit('city_id='.$city_id,$edit_kaiguan);
    $this->show_message('caozuochenggong',
    'caozuochenggong', 'index.php?app=admin&amp;act=fenzhan',
    'fanhuiliebiao',    'index.php?app=admin&amp;act=fenzhan');
	}
	else
	{
	    $logs_data=$this->_city_mod->find('city_id='.$city_id);
		$this->assign('log', $logs_data);
        $this->display('fenzhan_kaiguan.html');
	    return;
	}
		}
	
	
	
	
	
    function edit()
    {
        $id = (isset($_GET['id']) && $_GET['id'] !='') ? intval($_GET['id']) : '';
		$czsh=trim($_POST['czsh']);
		$dhjfsh=trim($_POST['dhjfsh']);
		$dhxjsh=trim($_POST['dhxjsh']);
		$txsh=trim($_POST['txsh']);
		
			
        $row=$this->_admin_mod->getAll("select * from ".DB_PREFIX."user_priv where user_id = '$id'");
		/*$priv=$this->_admin_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$id'");
		$czsh=$priv['czsh'];*/
	
			$this->assign('row', $row);	
		
        //判断是否选择了管理员
        if (!$id || $this->_admin_mod->check_admin($id))
        {
            $this->show_warning('choose_admin');
            return;
        }
        //判断是否是系统初始管理员
         if ($this->_admin_mod->check_system_manager($id))
        {
            $this->show_warning('system_admin_edit');
            return;
        }
        if (!IS_POST)
        {
            //获取当前管理员权限
            $privs = $this->_admin_mod->get(array(
                'conditions' => '1=1 AND  store_id =0 AND user_id = '.$id,
                'fields' => 'privs,city,czsh,dhjfsh,dhxjsh,txsh',
            ));
           $admins = $this->_user_mod->get(array(
                    'conditions' => '1=1 AND user_id ='.$id,
                    'fields' => 'user_name,real_name,city',
                ));
            $priv=explode(',', $privs['privs']);
            include(ROOT_PATH.'/psadmin/includes/priv.inc.php');
            $act = 'edit';
            $this->assign('act',$act);
            $this->assign('admin',$admins);
            $this->assign('checked_priv',$priv);
            $this->assign('priv',$menu_data);
            $this->display('admin.form.html');
        }
        else
        {
            //更新管理员权限
            $privs = (isset($_POST['priv']) && $_POST['priv']!='priv') ? $_POST['priv']: '';
            $priv = '';
            if ($privs == '')
            {
                $this->show_warning('add_priv');
                return;
            }
            else
            {
                $priv = implode(',', $privs);
            }
            $data = array(
                    'user_id' => $id,
                    'store_id' => '0',
                    'privs' => $priv,
					'czsh' => $czsh,
					'dhjfsh' => $dhjfsh,
					'dhxjsh' => $dhxjsh,
					'txsh' => $txsh,
               );
            if(!$this->_admin_mod->edit($id, $data))
            {
                 $this->show_warning($this->_admin_mod->get_error());
                 return;
             }
             else
            {
                $this->show_message('edit_admin_ok');
                return true;
             }
        }
    }
    function add()
    {
        $id = (isset($_GET['id']) && $_GET['id'] != '') ? intval($_GET['id']) : '';
		$czsh=trim($_POST['czsh']);
		$dhjfsh=trim($_POST['dhjfsh']);
		$dhxjsh=trim($_POST['dhxjsh']);
		$txsh=trim($_POST['txsh']);
        if (empty($_POST['priv']))
        {
           if ($id != '')
           {
                $condition = ' AND  user_id = '.$id;
                $admin = $this->_user_mod->get(array(
                    'conditions' => '1=1' . $condition,
                    'fields' => 'user_name,real_name,city',
                ));
                //查询是否是管理员
                if (!$admin)
                {
                    $this->show_warning('choose_admin');
                    return;
                }
                //查询是否已是管理员
                if (!$this->_admin_mod->check_admin($id))
                {
                    $this->show_warning('already_admin');
                    return;
                }
                $this->assign('admin',$admin);
                include(ROOT_PATH.'/psadmin/includes/priv.inc.php');
                $this->assign('priv', $menu_data);
                $this->display('admin.form.html');
            }
            else
            {
                if(!IS_POST)
                {
                    $this->display('admin.test.html');
                }
                else
                {
                    $user_name = (isset($_POST['user_name'])&&$_POST['user_name']!='') ? $_POST['user_name']:'';
					$city = (isset($_POST['city'])&&$_POST['city']!='') ? $_POST['city']:'';
					$czsh=trim($_POST['czsh']);
		            $dhjfsh=trim($_POST['dhjfsh']);
		            $dhxjsh=trim($_POST['dhxjsh']);
		            $txsh=trim($_POST['txsh']);

                    /* 连接用户系统 */
                    $ms =& ms();
                    $info = $ms->user->get($user_name, true);
                    if (empty($info))
                    {
                        $this->show_message('add_member', 'go_back', 'index.php?app=admin&amp;act=add', 'to_add_member', 'index.php?app=user&amp;act=add');
                        return;
                    }
                    else
                    {
                        $id = $info['user_id'];
                        header("Location: index.php?app=admin&act=add&id=".$id." ");
                     }
                }
            }
        }
        else
        {
            //获取权限并处理
            $privs = (isset($_POST['priv']) && $_POST['priv'] != 'priv') ? $_POST['priv'] : '';
			$city = (isset($_POST['city']) && $_POST['city'] != 'city') ? $_POST['city'] : '';
			$czsh=trim($_POST['czsh']);
		    $dhjfsh=trim($_POST['dhjfsh']);
		    $dhxjsh=trim($_POST['dhxjsh']);
		    $txsh=trim($_POST['txsh']);
            $priv = 'default|all,';
            if ($privs == '')
            {
                $this->show_warning('add_priv');
                return;
            }
            else
            {
                $priv .= implode(',', $privs);
            }
             //判断是否已是管理员
             if (!$this->_admin_mod->check_admin($id))
                {
                    $this->show_warning('already_admin');
                    return;
                }
             $data = array(
                    'user_id' => $id,
                    'store_id' => '0',
                    'privs' => $priv,
					'city' => $city,
					'czsh' => $czsh,
					'dhjfsh' => $dhjfsh,
					'dhxjsh' => $dhxjsh,
					'txsh' => $txsh,
                );
             if ($this->_admin_mod->add($data) === fasle)
             {
                 $this->show_warning($this->_admin_mod->get_error());
                 return;
             }
             else
            {
                $this->show_message('add_admin_ok', 'admin_list', 'index.php?app=admin', 'user_list', 'index.php?app=user');
             }
        }
    }
}

?>
