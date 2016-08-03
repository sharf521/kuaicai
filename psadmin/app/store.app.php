<?php

/* 店铺控制器 */
class StoreApp extends BackendApp
{
    var $_store_mod;

    function __construct()
    {
        $this->StoreApp();
    }

    function StoreApp()
    {
        parent::__construct();
        $this->_store_mod =& m('store');
		$this->member_mod =& m('member');
		$this->canshu_mod =& m('canshu');
		$this->userpriv_mod =& m('userpriv');
		$this->gonghuo_mod =& m('gonghuo');
		$this->payment_mod =& m('payment');
		$this->storelog_mod =& m('storelog');
		$this->moneylog_mod =& m('moneylog');
		$this->accountlog_mod =& m('accountlog');
		$this->my_money_mod =& m('my_money');
		
    }

    function index()
    {
	$user=$this->visitor->get('user_name');
	$userid=$this->visitor->get('user_id');
	/*echo $user_id;*/
$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row', $priv_row);
	$region_mod =& m('region');
	$this->assign('scategories', $this->_get_scategory_options());
	$this->assign('regions', $region_mod->get_options(0));
	//$region_name=$_GET['region_name'];
        $conditions = empty($_GET['wait_verify']) ? "state <> '" . STORE_APPLYING . "'" : "state = '" . STORE_APPLYING . "'";
        $filter = $this->_get_query_conditions(array(
            array(
                'field' => 'store_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'sgrade',
            ),
			 array(
                'field' => 'cityid',
                'name'  => 'suoshuzhan',
                'equal' => '=',
            ),
			array(
                'field' => 'add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),
			array(
                'field' => 'end_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
			),
			array(
                'field' => 'recommended',
                'name'  => 'tuijian',
                'equal' => '=',
			),
			 array(
                'field' => 'region_name',
                'equal' => 'like',
            ),	
			array(
                'field' => 'state',
				'name'  => 'store_state',
                'equal' => '=',
            ),	
			array(
                'field' => 'cate_id',
				'name'  => 'fenlei',
                'equal' => '=',
            ),	
		
        ));

        $owner_name = trim($_GET['owner_name']);
		$storename = trim($_GET['store_name']);
		$sgr = trim($_GET['sgrade']);
		$scategory = trim($_GET['scategory']);
		$suoshuzhan = trim($_GET['suoshuzhan']);
		$scate=$this->userpriv_mod->getAll("select * from ".DB_PREFIX."category_store where cate_id = '$scategory'");
		
		
        if ($owner_name)
        {

            $filter .= " AND (user_name LIKE '%{$owner_name}%' OR owner_name LIKE '%{$owner_name}%') ";
        }
        //更新排序
		//print_r($filter);
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'sort_order';
                $order = '';
            }
        }
        else
        {
            $sort  = 'store_id';
            $order = 'desc';
        }
		
        $wait=$_GET['wait_verify'];
        $this->assign('filter', $filter);
		$this->assign('scategory', $scategory);
		$this->assign('suoshuzhan', $suoshuzhan);
        $conditions .= $filter;
		
        $page = $this->_get_page();
	
		
 		if($privs=='all')
		{	
        $stores = $this->_store_mod->find(array(
            'conditions' => $conditions,
            'join'  => 'belongs_to_user,has_scategory',
            'fields'=> 'this.*,member.user_name,member.level',
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order"
        ));
		
		}
		else
		{
		$stores = $this->_store_mod->find(array(
            'conditions' => $conditions.'and cityid='.$city,
            'join'  => 'belongs_to_user,has_scategory',
            'fields'=> 'this.*,member.user_name,member.level',
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order"
        ));
		}


        $sgrade_mod =& m('sgrade');
        $grades = $sgrade_mod->get_options();
        $this->assign('sgrades', $grades);

        $states = array(
            STORE_APPLYING  => LANG::get('wait_verify'),
            STORE_OPEN      => Lang::get('open'),
            STORE_CLOSED    => Lang::get('close'),
        );
	
		$city_row=array();
		$result=$this->member_mod->getAll("select * from ".DB_PREFIX."city");
		 $this->assign('result', $result);
		foreach ($result as $var )
		{
		  $row=explode('-',$var['city_name']);
		  $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
        foreach ($stores as $key => $store)
        {
            $stores[$key]['sgrade'] = $grades[$store['sgrade']];
            $stores[$key]['state'] = $states[$store['state']];
			$stores[$key]['city_name'] = $city_row[$store['cityid']];
            $certs = empty($store['certification']) ? array() : explode(',', $store['certification']);
            for ($i = 0; $i < count($certs); $i++)
            {
                $certs[$i] = Lang::get($certs[$i]);
            }
            $stores[$key]['certification'] = join('<br />', $certs);
			
        }
	
        $this->assign('stores', $stores);


/*$index=$this->_store_mod->find(array(
	'conditions' => 'cityid='.$city,
	'limit' => $page['limit'],
	'order' => "$sort $order",
	'count' => true));	
		 
     
            $this->assign('index', $index);*/
		/*if(!empty($wait) || !empty($owner_name) || !empty($storename) || !empty($scategory))
		{	
			$page['item_count'] =count($stores);
			
		}
		else*/
		/*{*/
			$page['item_count'] = $this->_store_mod->getCount();
			
		/*}*/
		

		//print_r($page['item_count']);
        //$this->import_resource(array('script' => 'inline_edit.js'));
		$this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->_format_page($page);
		//print_r($page);
        $this->assign('filtered', $filter? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		$this->display('store.index.html');
    }
    function test()
    {
	$this->member_mod =& m('member');
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	$city=$row_member['city'];
	
        if (!IS_POST)
        {
            $sgrade_mod =& m('sgrade');
            $grades = $sgrade_mod->find();
            if (!$grades)
            {
                $this->show_warning('set_grade_first');
                return;
            }
            $this->display('store.test.html');
        }
        else
        {
            $user_name = trim($_POST['user_name']);
            $password  = $_POST['password'];
			

            /* 连接到用户系统 */
            $ms =& ms();
            $user = $ms->user->get($user_name, true);
            if (empty($user))
            {
                $this->show_warning('user_not_exist');
                return;
            }
            if ($_POST['need_password'] && !$ms->user->auth($user_name, $password))
            {
                $this->show_warning('invalid_password');

                return;
            }

            $store = $this->_store_mod->get_info($user['user_id']);
            if ($store)
            {
                if ($store['state'] == STORE_APPLYING)
                {
                    $this->show_warning('user_has_application');
                    return;
                }
                else
                {
                    $this->show_warning('user_has_store');
                    return;
                }
            }
            else
            {
                header("Location:index.php?app=store&act=add&user_id=" . $user['user_id']);
            }
        }
    }

    function add()
    {
	$this->member_mod =& m('member');
	$user=$this->visitor->get('user_name');
	$user_id=$this->visitor->get('user_id');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	$city=$row_member['city'];
        $user_id = $_GET['user_id'];
        if (!$user_id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        if (!IS_POST)
        {
            /* 取得会员信息 */
            $user_mod =& m('member');
            $user = $user_mod->get_info($user_id);
            $this->assign('user', $user);

            $this->assign('store', array('state' => STORE_OPEN, 'recommended' => 0, 'sort_order' => 65535, 'end_time' => 0));

            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $this->assign('states', array(
                STORE_OPEN   => Lang::get('open'),
                STORE_CLOSED => Lang::get('close'),
            ));

            $this->assign('recommended_options', array(
                '1' => Lang::get('yes'),
                '0' => Lang::get('no'),
            ));

            $this->assign('scategories', $this->_get_scategory_options());

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
            ));
            $this->assign('enabled_subdomain', ENABLED_SUBDOMAIN);
            $this->display('store.form.html');
        }
        else
        {
            /* 检查名称是否已存在 */
            if (!$this->_store_mod->unique(trim($_POST['store_name'])))
            {
                $this->show_warning('name_exist');
                return;
            }
            $domain = empty($_POST['domain']) ? '' : trim($_POST['domain']);
            if (!$this->_store_mod->check_domain($domain, Conf::get('subdomain_reserved'), Conf::get('subdomain_length')))
            {
                $this->show_warning($this->_store_mod->get_error());

                return;
            }
            $data = array(
                'store_id'     => $user_id,
                'store_name'   => $_POST['store_name'],
                'owner_name'   => $_POST['owner_name'],
                'owner_card'   => $_POST['owner_card'],
                'region_id'    => $_POST['region_id'],
                'region_name'  => $_POST['region_name'],
                'address'      => $_POST['address'],
                'zipcode'      => $_POST['zipcode'],
                'tel'          => $_POST['tel'],
                'sgrade'       => $_POST['sgrade'],
                'end_time'     => empty($_POST['end_time']) ? 0 : gmstr2time(trim($_POST['end_time'])),
                'state'        => $_POST['state'],
                'recommended'  => $_POST['recommended'],
                'sort_order'   => $_POST['sort_order'],
			    'erweima'   => $_POST['erweima'],
                'add_time'     => gmtime(),
                'domain'       => $domain,
				'cityid'       => $city,
            );
            $certs = array();
            isset($_POST['autonym']) && $certs[] = 'autonym';
            isset($_POST['material']) && $certs[] = 'material';
            $data['certification'] = join(',', $certs);

            if ($this->_store_mod->add($data) === false)
            {
                $this->show_warning($this->_store_mod->get_error());
                return false;
            }

            $this->_store_mod->unlinkRelation('has_scategory', $user_id);
            $cate_id = intval($_POST['cate_id']);
            if ($cate_id > 0)
            {
                $this->_store_mod->createRelation('has_scategory', $user_id, $cate_id);
            }

            $this->show_message('add_ok',
                'back_list',    'index.php?app=store',
                'continue_add', 'index.php?app=store&amp;act=test'
            );
        }
    }

    function edit()
    {
		$this->message_mod=& m('message');
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
		$pag = empty($_GET['page']) ? 0 : intval($_GET['page']);
		$dengji=$_GET['dj'];
		$jingying=Lang::get('jingyingban');
		$fuchi=Lang::get('fuchiban');
		$result=$this->message_mod->getRow("select user_name from ".DB_PREFIX."member where user_id='$id' limit 1");
		$username=$result['user_name'];
		
		
		$userid=$this->visitor->get('user_id');
		$priv_row=$this->message_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
		$this->assign('priv_row', $priv_row);
		
		
        if (!IS_POST)
        {
            /* 是否存在 */
            $store = $this->_store_mod->get_info($id);
	
            if (!$store)
            {
                $this->show_warning('store_empty');
                return;
            }
            if ($store['certification'])
            {
                $certs = explode(',', $store['certification']);
                foreach ($certs as $cert)
                {
                    $store['cert_' . $cert] = 1;
                }
            }
            $this->assign('store', $store);

            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $this->assign('states', array(
                STORE_OPEN   => Lang::get('open'),
                STORE_CLOSED => Lang::get('close'),
            ));

            $this->assign('recommended_options', array(
                '1' => Lang::get('yes'),
                '0' => Lang::get('no'),
            ));

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            $this->assign('scategories', $this->_get_scategory_options());

            $scates = $this->_store_mod->getRelatedData('has_scategory', $id);
            $this->assign('scates', array_values($scates));

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
            ));
			
			$this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
			
            $this->assign('enabled_subdomain', ENABLED_SUBDOMAIN);
            $this->display('store.form.html');
        }
        else
        {
            /* 检查名称是否已存在 */
            if (!$this->_store_mod->unique(trim($_POST['store_name']), $id))
            {
                $this->show_warning('name_exist');
                return;
            }
            $store_info = $this->_store_mod->get_info($id);
            $domain = empty($_POST['domain']) ? '' : trim($_POST['domain']);
            if ($domain && $domain != $store_info['domain'])
            {
                if (!$this->_store_mod->check_domain($domain, Conf::get('subdomain_reserved'), Conf::get('subdomain_length')))
                {
                    $this->show_warning($this->_store_mod->get_error());

                    return;
                }
            }
		
            $data = array(
                'store_name'   => $_POST['store_name'],
                'owner_name'   => $_POST['owner_name'],
                'owner_card'   => $_POST['owner_card'],
                'region_id'    => $_POST['region_id'],
                'region_name'  => $_POST['region_name'],
                'address'      => $_POST['address'],
                'zipcode'      => $_POST['zipcode'],
                'tel'          => $_POST['tel'],
                'sgrade'       => $_POST['sgrade'],
                'end_time'     => empty($_POST['end_time']) ? 0 : gmstr2time(trim($_POST['end_time'])),
                'state'        => $_POST['state'],
                'sort_order'   => $_POST['sort_order'],
                'recommended'  => $_POST['recommended'],
				'erweima'  => $_POST['erweima'],
                'domain'       => $domain,
            );
            $data['state'] == STORE_CLOSED && $data['close_reason'] = $_POST['close_reason'];
            $certs = array();
            isset($_POST['autonym']) && $certs[] = 'autonym';
            isset($_POST['material']) && $certs[] = 'material';
            $data['certification'] = join(',', $certs);

            $old_info = $this->_store_mod->get_info($id); // 修改前的店铺信息
            $this->_store_mod->edit($id, $data);

            $this->_store_mod->unlinkRelation('has_scategory', $id);
            $cate_id = intval($_POST['cate_id']);
            if ($cate_id > 0)
            {
                $this->_store_mod->createRelation('has_scategory', $id, $cate_id);
            }

            /* 如果修改了店铺状态，通知店主 */
            if ($old_info['state'] != $data['state'])
            {
                $ms =& ms();
                if ($data['state'] == STORE_CLOSED)
                {
                    // 关闭店铺
                    $subject = Lang::get('close_store_notice');
                    //$content = sprintf(Lang::get(), $data['close_reason']);
                    $content = get_msg('toseller_store_closed_notify',array('reason' => $data['close_reason']));
                }
                else
                {
                    // 开启店铺
                    $subject = Lang::get('open_store_notice');
                    $content = Lang::get('toseller_store_opened_notify');
                }
                $ms->pm->send(MSG_SYSTEM, $old_info['store_id'], '', $content);
                $this->_mailto($old_info['email'], $subject, $content);
            }

			if($dengji==$fuchi && $_POST['sgrade']==2)
			{
				
				$notice=Lang::get('dianpusj');
				$notice=str_replace('{1}',$username,$notice);
				$add_notice=array(
				'from_id'=>0,
				'to_id'=>$id,
				'content'=>$notice,  
				'add_time'=>gmtime(),
				'last_update'=>gmtime(),
				'new'=>1,
				'parent_id'=>0,
				'status'=>3,
				);				
				$this->message_mod->add($add_notice);
			}



            $this->show_message('edit_ok',
                'back_list',    'index.php?app=store&amp;page=' . $pag,
                'edit_again',   'index.php?app=store&amp;act=edit&amp;id=' . $id
            );
        }
    }

    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();
       if (in_array($column ,array('recommended','sort_order')))
       {
           $data[$column] = $value;
           if($this->_store_mod->edit($id, $data))
           {
               echo ecm_json_encode(true);
           }
       }
       else
       {
           return ;
       }
       return ;
   }

    function drop()
    {
	$user_mod =& m('member');
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_store_to_drop');
            return;
        }

        $ids = explode(',', $id);
        foreach ($ids as $id)
        {
            $this->_drop_store_image($id); // 注意这里要先删除图片，再删除店铺，因为删除图片时要查店铺信息
			$sql="delete from ".DB_PREFIX."gonghuo where user_id = '$id'";
			$user_mod->db->query($sql);
        }
        if (!$this->_store_mod->drop($ids))
        {
            $this->show_warning($this->_store_mod->get_error());
            return;
        }
        
        /* 通知店主 */
        
        $users = $user_mod->find(array(
            'conditions' => "user_id" . db_create_in($ids),
            'fields'     => 'user_id, user_name, email',
        ));
        foreach ($users as $user)
        {
            $ms =& ms();
            $subject = Lang::get('drop_store_notice');
            $content = get_msg('toseller_store_droped_notify');
            $ms->pm->send(MSG_SYSTEM, $user['user_id'], $subject, $content);
            $this->_mailto($user['email'], $subject, $content);
        }

        $this->show_message('drop_ok');
    }

    /* 更新排序 */
    function update_order()
    {
        if (empty($_GET['id']))
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        $ids = explode(',', $_GET['id']);
        $sort_orders = explode(',', $_GET['sort_order']);
        foreach ($ids as $key => $id)
        {
            $this->_store_mod->edit($id, array('sort_order' => $sort_orders[$key]));
        }

        $this->show_message('update_order_ok');
    }

    /* 查看并处理店铺申请 */
    function view()
    {
	 $this->message_mod=& m('message');
	$userid=$this->visitor->get('user_id');
		$priv_row=$this->message_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
		$this->assign('priv_row', $priv_row);
		
	   
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
		$dengji=$_GET['dj'];
	
        if (!IS_POST)
        {
            /* 是否存在 */
            $store = $this->_store_mod->get_info($id);
            if (!$store)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $sgrade_mod =& m('sgrade');
            $sgrades = $sgrade_mod->get_options();
            $store['sgrade'] = $sgrades[$store['sgrade']];
            $this->assign('store', $store);

            $scates = $this->_store_mod->getRelatedData('has_scategory', $id);
            $this->assign('scates', $scates);

            $this->display('store.view.html');
        }
        else
        {
            /* 批准 */
            if (isset($_POST['agree']))
            {
                $this->_store_mod->edit($id, array(
                    'state'      => STORE_OPEN,
                    'add_time'   => gmtime(),
                    'sort_order' => 65535,
                ));

                $content = get_msg('toseller_store_passed_notify');
                $ms =& ms();
                $ms->pm->send(MSG_SYSTEM, $id, '', $content);
                $store_info = $this->_store_mod->get_info($id);
                $this->send_feed('store_created', array(
                    'user_id'   =>  $store_info['store_id'],
                    'user_name'   => $store_info['user_name'],
                    'store_url'   => SITE_URL . '/' . url('app=store&id=' . $store_info['store_id']),
                    'seller_name'   => $store_info['store_name'],
                ));
                $this->_hook('after_opening', array('user_id' => $id));
				
				$name=Lang::get('shangfutong');
				 $data = array(
                'store_id'      => $id,
                'payment_name'  => $name,
                'payment_code'  => 'sft',
                'payment_desc'  => $_POST['payment_desc'],
                'config'        => $_POST['config'],
                'is_online'     => 0,
                'enabled'       => 1,
                'sort_order'    => 0
            );
			
				$this->payment_mod->add($data);
				
				/*$notice=Lang::get('dianpuyikaitong');
				$notice=str_replace('{1}',$store_info['user_name'],$notice);		
				$add_notice=array(
				'from_id'=>0,
				'to_id'=>$store_info['store_id'],
				'content'=>$notice,  
				'add_time'=>time(),
				'last_update'=>time(),
				'new'=>1,
				'parent_id'=>0,
				'status'=>3,
				);
				$this->message_mod->add($add_notice);*/

                $this->show_message('agree_ok',
                    //'edit_the_store', 'index.php?app=store&amp;act=edit&amp;id=' . $id,
                    'back_list', 'index.php?app=store'
                );
            }
            /* 拒绝 */
            elseif (isset($_POST['reject']))
            {
                $reject_reason = trim($_POST['reject_reason']);
                if (!$reject_reason)
                {
                    $this->show_warning('input_reason');
                    return;
                }

                $content = get_msg('toseller_store_refused_notify', array('reason' => $reject_reason));
                $ms =& ms();
                $ms->pm->send(MSG_SYSTEM, $id, '', $content);

                $this->_drop_store_image($id); // 注意这里要先删除图片，再删除店铺，因为删除图片时要查店铺信息
                $this->_store_mod->drop($id);

                $this->show_message('reject_ok',
                    'back_list', 'index.php?app=store'
                );
            }
            else
            {
                $this->show_warning('Hacking Attempt');
                return;
            }
        }
    }

    function batch_edit()
    {
        if (!IS_POST)
        {
            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            $this->headtag('<script type="text/javascript" src="{lib file=mlselection.js}"></script>');
            $this->display('store.batch.html');
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
            $data = array();
            if ($_POST['region_id'] > 0)
            {
                $data['region_id'] = $_POST['region_id'];
                $data['region_name'] = $_POST['region_name'];
            }
            if ($_POST['sgrade'] > 0)
            {
                $data['sgrade'] = $_POST['sgrade'];
            }
            if ($_POST['certification'])
            {
                $certs = array();
                if ($_POST['autonym'])
                {
                    $certs[] = 'autonym';
                }
                if ($_POST['material'])
                {
                    $certs[] = 'material';
                }
                $data['certification'] = join(',', $certs);
            }
            if ($_POST['recommended'] > -1)
            {
                $data['recommended'] = $_POST['recommended'];
            }
            if (trim($_POST['sort_order']))
            {
                $data['sort_order'] = intval(trim($_POST['sort_order']));
            }

            if (empty($data))
            {
                $this->show_warning('no_change_set');
                return;
            }

            $this->_store_mod->edit($ids, $data);

            $this->show_message('edit_ok',
                'back_list', 'index.php?app=store');
        }
    }

    function check_name()
    {
        $id         = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $store_name = empty($_GET['store_name']) ? '' : trim($_GET['store_name']);

        if (!$this->_store_mod->unique($store_name, $id))
        {
            echo ecm_json_encode(false);
            return;
        }
        echo ecm_json_encode(true);
    }

    /* 删除店铺相关图片 */
    function _drop_store_image($store_id)
    {
        $files = array();

        /* 申请店铺时上传的图片 */
        $store = $this->_store_mod->get_info($store_id);
        for ($i = 1; $i <= 3; $i++)
        {
            if ($store['image_' . $i])
            {
                $files[] = $store['image_' . $i];
            }
        }

        /* 店铺设置中的图片 */
        if ($store['store_banner'])
        {
            $files[] = $store['store_banner'];
        }
        if ($store['store_logo'])
        {
            $files[] = $store['store_logo'];
        }

        /* 删除 */
        foreach ($files as $file)
        {
            $filename = ROOT_PATH . '/' . $file;
            if (file_exists($filename))
            {
                @unlink($filename);
            }
        }
    }

    /* 取得店铺分类 */
    function _get_scategory_options()
    {
        $mod =& m('scategory');
        $scategories = $mod->get_list();
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($scategories, 'cate_id', 'parent_id', 'cate_name');

        return $tree->getOptions();
    }
	
	function jifenbili()
    {
	$log_id=1;
	$jifenbili=trim($_POST['jifenbili']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'jifenbili'=>$jifenbili,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('reject_ok',
   
    'fanhui',    'index.php?app=store&act=jifenbili');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('jifenbili.html');
	    return;
	}
	}
	
function xiaobao_jian()
{
	if($_POST)
	{
	$storeid= trim($_POST['store_id']);
	$row=$this->_store_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$storeid' limit 1");
	$row1=$this->_store_mod->getRow("select * from ".DB_PREFIX."store where store_id='$storeid' limit 1");
	$canshu=$this->_store_mod->can();
	$post_money= trim($_POST['post_money']);
	if($post_money<=0)
	{
		$this->show_warning('caozuobunengxiaoyu');
		return;
	}	
	if($row1['xiaobao']<$post_money)
	{
		$this->show_warning('xiaobaobuzu');
		return;
	}
	$riqi=date('Y-m-d H:i:s');
		$addlog1=array(
			'money_dj'=>'-'.$post_money,//负数
			'time'=>$riqi,
			'user_name'=>$row['user_name'],
			'user_id'=>$storeid,
			'zcity'=>$row['city'],
			'type'=>44,
			's_and_z'=>2,
			//'beizhu'=>$beizhu,
			'dq_money'=>$row['money'],
			'dq_money_dj'=>$row['money_dj']-$post_money,
			'dq_jifen'=>$row['duihuanjifen'],
			'dq_jifen_dj'=>$row['dongjiejifen']		
		);
		 $this->moneylog_mod->add($addlog1);		
//添加accountlog日志
		$addaccoun=array(
			'money'=>$post_money,
			'time'=>$riqi,
			'user_name'=>$row['user_name'],
			'user_id'=>$storeid,
			'zcity'=>$row['city'],
			'type'=>44,
			's_and_z'=>1,
			//'beizhu'=>$beizhu,
			'dq_money'=>$canshu['zong_money']+$post_money,
			'dq_jifen'=>$canshu['zong_jifen'],
		);
		 $this->accountlog_mod->add($addaccoun);
		 $this->my_money_mod->edit('user_id='.$storeid,array('money_dj'=>$row['money_dj']-$post_money));
		 $this->canshu_mod->edit('id=1',array('zong_money'=>$canshu['zong_money']+$post_money));
		 $this->_store_mod->edit('store_id='.$storeid,array('xiaobao'=>$row1['xiaobao']-$post_money));
		 $this->show_message('kouchuchenggong');
	}
	else
	{
	 $store_id = isset($_GET['id']) ? trim($_GET['id']) : '';
	 if(!empty($store_id))
     {
     $index=$this->_store_mod->getRow("select * from ".DB_PREFIX."store where store_id='$store_id' limit 1");
	 }
	 $this->assign('index', $index);
     $this->display('xiaobao_jian.html'); 
	}
}

function mxstore()
	{
		$store_id = isset($_GET['id']) ? trim($_GET['id']) : '';
		$this->_store_mod ->edit('store_id='.$store_id,array('dengji'=>1));
		$this->show_message('tianjiachenggong');
	}
	function qxmxstore()
	{
		$store_id = isset($_GET['id']) ? trim($_GET['id']) : '';
		$this->_store_mod ->edit('store_id='.$store_id,array('dengji'=>0));
		$this->show_message('quxiaochenggong');
	}
	
	
}

?>
