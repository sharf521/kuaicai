<?php

/* 管理员控制器 */
class AdminApp extends BackendApp
{
    var $_admin_mod;
    var $_user_mod;
	var $_city_mod;

    function __construct()
    {
        $this->AdminApp();
    }

    function AdminApp()
    {
        parent::__construct();
        $this->_admin_mod = & m('userpriv');
        $this->_user_mod = & m('member');
		 $this->_city_mod = & m('city');
    }
    function index()
    {
	
	$user_id=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$user_id' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
        $conditions = ' AND store_id = 0';
        //更新排序
        $sort  = 'userpriv.user_id';
        $order = 'asc';
        $page = $this->_get_page();
		if($privs=="all")
		{
        $admin_info = $this->_admin_mod->find(array(
            'conditions' => '1=1' . $conditions,
            'join' => 'mall_be_manage',
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
		}
		else
		{
		$admin_info = $this->_admin_mod->find(array(
            'conditions' => " user_priv.city='$city' and 1=1" . $conditions,
            'join' => 'mall_be_manage',
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
		}
		
		$city_row=array();
		$result=$this->_admin_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		  $row=explode('-',$var['city_name']);
		  $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
        foreach ($admin_info as $key => $val)
        {
			$admin_info[$key]['city_name'] = $city_row[$val['city']];
        }
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

	
	
	
	
//编辑管理员权限
    function edit()
    {
        $id = (isset($_GET['id']) && $_GET['id'] !='') ? intval($_GET['id']) : '';
		$pag = empty($_GET['page']) ? 0 : intval($_GET['page']);
		$czsh=trim($_POST['czsh']);
		$dhjfsh=trim($_POST['dhjfsh']);
		$dhxjsh=trim($_POST['dhxjsh']);
		$txsh=trim($_POST['txsh']);
		$tjfz=trim($_POST['tjfz']);
		$zjf=trim($_POST['zjf']);
		$city=trim($_POST['city']);

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
                'fields' => 'privs,city,czsh,dhjfsh,dhxjsh,txsh,tjfz,zjf',
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
					'tjfz' => $tjfz,
					'zjf' => $zjf,
					'city' => $city,
               );
            if(!$this->_admin_mod->edit('user_id='.$id.' and store_id=0', $data))
            {
                 $this->show_warning($this->_admin_mod->get_error());
                 return;
             }
             else
            {
                $this->show_message('edit_admin_ok','back_list','index.php?app=admin&page= '. $pag);
                return true;
             }
        }
    }
	
	
	
	
    function add()
    {
        $id = (isset($_GET['id']) && $_GET['id'] != '') ? intval($_GET['id']) : '';
		//echo $id;
		$city = (isset($_GET['city']) && $_GET['city'] != '') ? intval($_GET['city']) : '';
		//echo $city;
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
			$city = (isset($_GET['city']) && $_GET['city'] != '') ? intval($_GET['city']) : '';
			//echo $city;
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
