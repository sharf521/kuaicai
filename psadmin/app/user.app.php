<?php


/* 会员控制器 */
class UserApp extends BackendApp
{

    var $_user_mod;
	var $_city_mod;

    function __construct()
    {
        $this->UserApp();
    }

    function UserApp()
    {
        parent::__construct();
        $this->_user_mod =& m('member');
		$this->_city_mod =& m('city');
		$this->userpriv_mod =& m('userpriv');
		$this->my_webserv_mod =& m('my_webserv');
		
    }

    function index()
    {
	
	$user_id=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$user_id' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	 $this->assign('priv_row', $priv_row);
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),
			 array(
                'field' => 'member.city',
                'name'  => 'suoshuzhan',
                'equal' => '=',
            ),
			array(
                'field' => 'reg_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'reg_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            ),
        ));
        //更新排序
		
        //if (isset($_GET['sort']) && isset($_GET['order']))
		if (isset($_GET['sort']))
        {
            $sort  = strtolower(trim($_GET['sort']));
			
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
				//echo $sort;
             $sort  = $sort;
             //$order = 'desc';
            }
			$this->assign('sort', $sort);
        }
        else
        {
            $sort  = 'user_id desc';
           // $order = 'desc';
        }
        $page = $this->_get_page();
		if($privs=='all')
		{
        $users = $this->_user_mod->find(array(
            'join' => 'has_store,manage_mall',
            'fields' => 'this.*,store.store_id,userpriv.store_id as priv_store_id,userpriv.privs',
			'conditions' => '1=1 '.$conditions,
            'limit' => $page['limit'],
            'order' => "$sort",
            'count' => true,
        ));
		}
		else
		{
		$users = $this->_user_mod->find(array(
            'join' => 'has_store,manage_mall',
            'fields' => 'this.*,store.store_id,userpriv.store_id as priv_store_id,userpriv.privs',
            /*'conditions' => '1=1' . $conditions .'and city='.$city,*/
			'conditions' => '1=1 '.$conditions .'and member.city='.$city,
            'limit' => $page['limit'],
            'order' => "$sort",
            'count' => true,
        ));
		}
		$city_row=array();
		$result=$this->_user_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		  $row=explode('-',$var['city_name']);
		  $city_row[$var['city_id']]=$row[0];
		
		}
		$this->assign('result', $result);
		$result=null;
        foreach ($users as $key => $val)
        {
            if ($val['priv_store_id'] == 0 && $val['privs'] != '')
            {
                $users[$key]['if_admin'] = true;
            }
			$users[$key]['city_name'] = $city_row[$val['city']];
			$users[$key]['reg_time'] = date('Y-m-d',$val['reg_time']);
        }
	
		
		/*$index=$this->_user_mod->find(array(
	'conditions' => 'city='.$city,
	'limit' => $page['limit'],
	'order' => "$sort $order",
	'count' => true));	
		 
     
            $this->assign('index', $index);*/
		
        $this->assign('users', $users);
        $page['item_count'] = $this->_user_mod->getCount();
		//print_r($page['item_count']);
		
        $this->_format_page($page);
	   // print_r($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style'  => 'res:style/jqtreetable.css,jquery.ui/themes/ui-lightness/jquery.ui.css'
        ));
        $this->assign('query_fields', array(
            'user_name' => LANG::get('user_name'),
            'email'     => LANG::get('email'),
            'real_name' => LANG::get('real_name'),
			'member.user_id' => LANG::get('user_id'),
//            'phone_tel' => LANG::get('phone_tel'),
//            'phone_mob' => LANG::get('phone_mob'),
        ));
        $this->assign('sort_options', array(
            'reg_time DESC'   => LANG::get('reg_time'),
            'last_login DESC' => LANG::get('last_login'),
            'logins DESC'     => LANG::get('logins'),
        ));
        $this->display('user.index.html');
    }

function add()
    {
	$this->city_mod=& m('city');
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$cityid=$priv_row['city'];
	
        if (!IS_POST)
        {
		
		if($privs=="all")
		{
		$city_row=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
		}
		else
		{
		$city_row=$this->city_mod->getAll("select * from ".DB_PREFIX."city where city_id='$cityid'");
		}
		$this->assign('city_row', $city_row);
		//print_r($city_row);
		
            $this->assign('user', array(
                'gender' => 0,
            ));
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $ms =& ms();
            $this->assign('set_avatar', $ms->user->set_avatar());
            $this->display('user.form.html');
        }
        else
        {
            $user_name = trim($_POST['user_name']);
            $password  = trim($_POST['password']);
            $email     = trim($_POST['email']);
            $real_name = trim($_POST['real_name']);
            $gender    = trim($_POST['gender']);
            $im_qq     = trim($_POST['im_qq']);
            $im_msn    = trim($_POST['im_msn']);
			$city      = trim($_POST['city']);
			$owner_card      = trim($_POST['owner_card']);
			$yaoqing_id      = trim($_POST['yaoqing_id']);
            //echo $city;
            if (strlen($user_name) < 3 || strlen($user_name) > 25)
            {
                $this->show_warning('user_name_length_error');

                return;
            }

            if (strlen($password) < 6 || strlen($password) > 20)
            {
                $this->show_warning('password_length_error');

                return;
            }

            if (!is_email($email))
            {
                $this->show_warning('email_error');

                return;
            }

            /* 连接用户系统 */
            $ms =& ms();

            /* 检查名称是否已存在 */
            if (!$ms->user->check_username($user_name))
            {
                $this->show_warning($ms->user->get_error());

                return;
            }
			
			if (empty($owner_card))
            {
                $this->show_warning('shenfenzhengbunengweikong');
                return;
            }
			include_once(ROOT_PATH. '/includes/idcheck.class.php');
			$chk=new IDCheck($owner_card);
			if(($chk->Part())==False)
			{
			$this->show_warning('shurushenfenzheng');
			return;
			}

            /* 保存本地资料 */
            $data = array(
                'real_name' => $_POST['real_name'],
                'gender'    => $_POST['gender'],
//                'phone_tel' => join('-', $_POST['phone_tel']),
//                'phone_mob' => $_POST['phone_mob'],
                'im_qq'     => $_POST['im_qq'],
                'im_msn'    => $_POST['im_msn'],
				'city'    => $city,
//                'im_skype'  => $_POST['im_skype'],
//                'im_yahoo'  => $_POST['im_yahoo'],
//                'im_aliww'  => $_POST['im_aliww'],
                'reg_time'  => gmtime(),
            );

            /* 到用户系统中注册 */
            $user_id = $ms->user->register($user_name, $password, $email,$owner_card,$city,$yaoqing_id, $data,$web_id,$weiboid,$openid);
            if (!$user_id)
            {
                $this->show_warning($ms->user->get_error());

                return;
            }
	

            if (!empty($_FILES['portrait']))
            {
                $portrait = $this->_upload_portrait($user_id);
                if ($portrait === false)
                {
                    return;
                }

                $portrait && $this->_user_mod->edit($user_id, array('portrait' => $portrait));
            }


            $this->show_message('add_ok',
                'back_list',    'index.php?app=user',
                'continue_add', 'index.php?app=user&amp;act=add'
            );
        }
    }

    /*检查会员名称的唯一性*/
    function  check_user()
    {
          $user_name = empty($_GET['user_name']) ? null : trim($_GET['user_name']);
          if (!$user_name)
          {
              echo ecm_json_encode(false);
              return ;
          }

          /* 连接到用户系统 */
          $ms =& ms();
          echo ecm_json_encode($ms->user->check_username($user_name));
    }

  function  check_email()
    {
          $email = empty($_GET['email']) ? null : trim($_GET['email']);
          if (!$email)
          {
              echo ecm_json_encode(false);
              return ;
          }

          /* 连接到用户系统 */
          $ms =& ms();
          echo ecm_json_encode($ms->user->check_email($email));
    }
    function edit()
    {
    $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	$pag = empty($_GET['page']) ? 0 : intval($_GET['page']);
	$this->city_mod=& m('city');
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$cityid=$priv_row['city'];
	 $us=$this->userpriv_mod->getRow("select city from ".DB_PREFIX."member where user_id = '$id' limit 1");
        if (!IS_POST)
        {
		if($privs=="all")
		{
		$city_row=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
		}
		else
		{
		$city_row=$this->city_mod->getAll("select * from ".DB_PREFIX."city where city_id='$cityid'");
		}
		$this->assign('city_row', $city_row);
		$this->assign('us', $us);
		
            /* 是否存在 */
            $user = $this->_user_mod->get_info($id);
            if (!$user)
            {
                $this->show_warning('user_empty');
                return;
            }

            $ms =& ms();
            $this->assign('set_avatar', $ms->user->set_avatar($id));
            $this->assign('user', $user);
            $this->assign('phone_tel', explode('-', $user['phone_tel']));
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('user.form.html');
        }
        else
        {
		 $row_user=$this->_user_mod->getAll("select email from ".DB_PREFIX."member");

			$owner_card=$_POST['owner_card'];
            $email1=trim($_POST['email']);
            $data = array(
                'real_name' => $_POST['real_name'],
                'gender'    => $_POST['gender'],
//                'phone_tel' => join('-', $_POST['phone_tel']),
//                'phone_mob' => $_POST['phone_mob'],
                'im_qq'     => $_POST['im_qq'],
                'im_msn'    => $_POST['im_msn'],
				'city'    => $_POST['city'],
				'owner_card'    => $_POST['owner_card'],
//                'im_skype'  => $_POST['im_skype'],
//                'im_yahoo'  => $_POST['im_yahoo'],
//                'im_aliww'  => $_POST['im_aliww'],
            );
            if (!empty($_POST['password']))
            {
                $password = trim($_POST['password']);
                if (strlen($password) < 6 || strlen($password) > 20)
                {
                    $this->show_warning('password_length_error');

                    return;
                }
            }
          /*  if (!is_email(trim($_POST['email'])))
            {
                $this->show_warning('email_error');

                return;
            }*/

           /*  foreach ($row_user as $key => $user)
            {
          if ($email1==$user['email'])
          {
		  $this->show_warning('email_err');
            return false;
          }
		  }
		  */
		  if (empty($owner_card))
            {
                $this->show_warning('shenfenzhengbunengweikong');
                return;
            }
			include_once(ROOT_PATH. '/includes/idcheck.class.php');
			$chk=new IDCheck($owner_card);
			if(($chk->Part())==False)
			{
			$this->show_warning('shurushenfenzheng');
			return;
			}

            if (!empty($_FILES['portrait']))
            {
                $portrait = $this->_upload_portrait($id);
                if ($portrait === false)
                {
                    return;
                }
                $data['portrait'] = $portrait;
            }

            /* 修改本地数据 */
            $this->_user_mod->edit($id, $data);

            /* 修改用户系统数据 */
            $user_data = array();
            !empty($_POST['password']) && $user_data['password'] = trim($_POST['password']);
            !empty($_POST['email'])    && $user_data['email']    = trim($_POST['email']);
            if (!empty($user_data))
            {
                $ms =& ms();
                $ms->user->edit($id, '', $user_data, true);
            }

            $this->show_message('edit_ok',
                'back_list',    'index.php?app=user&amp;page=' . $pag,
                'edit_again',   'index.php?app=user&amp;act=edit&amp;id=' . $id
            );
        }
    }

    function drop()
    {
	exit;
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_user_to_drop');
            return;
        }
        $admin_mod =& m('userpriv');
        if(!$admin_mod->check_admin($id))
        {
            $this->show_message('cannot_drop_admin',
                'drop_admin', 'index.php?app=admin');
            return;
        }

        $ids = explode(',', $id);

        /* 连接用户系统，从用户系统中删除会员 */
        $ms =& ms();
        if (!$ms->user->drop($ids))
        {
            $this->show_warning($ms->user->get_error());

            return;
        }
		foreach ($ids as $id)
		{
			$tab=array('my_money','moneylog','gonghuo','gonghuo_xinxi','groupbuy_log','my_moneylog','qiandao','goods_qa','my_webserv');
			foreach($tab as $t)
			{
				$this->_user_mod->db->query("delete  from ".DB_PREFIX.$t." where user_id='$id'");	
			}
			$tabb=array('goods','goods_qa','uploaded_file','store','coupon','groupbuy');
			foreach($tabb as $tt)
			{
				$this->_user_mod->db->query("delete  from ".DB_PREFIX.$tt." where store_id='$id'");	
			}
			
			$this->_user_mod->db->query("delete  from ".DB_PREFIX."caigou where cai_id='$id'");
				
		}

        $this->show_message('drop_ok');
    }

    /**
     * 上传头像
     *
     * @param int $user_id
     * @return mix false表示上传失败,空串表示没有上传,string表示上传文件地址
     */
    function _upload_portrait($user_id)
    {
        $file = $_FILES['portrait'];
        if ($file['error'] != UPLOAD_ERR_OK)
        {
            return '';
        }

        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false)
        {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=user&amp;act=edit&amp;id=' . $user_id);
            return false;
        }

        $uploader->root_dir(ROOT_PATH);
        return $uploader->save('data/files/mall/portrait/' . ceil($user_id / 500), $user_id);
    }
	
	
	
	function zhuanzhang()
	{
		$conditions=" and 1=1 ";
		$soname=$_GET['soname'];
		$this->assign('soname',$soname);
		$leixing=$_GET['leixing'];
		$this->assign('leixing',$leixing);
		$sotime=$_GET['sotime'];
		$endtime=$_GET['endtime'];
		$this->assign('sotime',$sotime);
		$this->assign('endtime',$endtime);
		$status=$_GET['status'];
		$this->assign('status',$status);
		$suoshuzhan=$_GET['suoshuzhan'];
		$this->assign('suoshuzhan',$suoshuzhan);
		if(!empty($soname))
		{
			$conditions.=" and user_name like '%$soname%' ";
		}
		if(!empty($sotime))
		{
			$conditions.=" and riqi>='$sotime' ";
		}
		if(!empty($endtime))
		{
			$conditions.=" and riqi<='$endtime 24:59:59' ";
		}
		if(!empty($leixing))
		{
			$conditions.=" and leixing='$leixing' ";
		}
		if(!empty($status))
		{
			$conditions.=" and status='$status' ";
		}
		if(!empty($suoshuzhan))
		{
			$conditions.=" and city='$suoshuzhan' ";
		}
		$this->my_moneylog_mod=& m('my_moneylog');		
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' => '(leixing=1 or leixing=2 or leixing=3 or leixing=4)'.$conditions,
            'limit' => 10,
			'order' => "id desc",
			'count' => true));
		$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		$this->assign('result',$result);
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];
			$index[$key]['money'] = abs($val['money']);	
        }
		
		$this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
		//$page['item_count'] = $this->my_moneylog_mod->getCount();
        //$this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//传递到风格里
        $this->display('zhuanzhang.html'); 
	    return;	 
	}
	function shenhe_user()
	{
		global $p2purl;
		$log_id=$_GET['log_id'];
		$this->my_moneylog_mod=& m('my_moneylog');
		$this->my_money_mod=& m('my_money');
		$this->moneylog_mod=& m('moneylog');
		$money_row=$this->my_moneylog_mod->getrow("select * from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");
		$moneydj=$money_row['money_dj'];
		$userid=$money_row['user_id'];
		$zhuanzhang=$money_row['leixing'];
		if($_POST)
		{
			$status=$_POST['status'];
			$beizhu=$_POST['beizhu'];
			$user_row=$this->my_moneylog_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$userid' limit 1");	
			$user_name=$user_row['user_name'];
			$user_money=$user_row['money'];
			$user_money_dongjie=$user_row['money_dj'];
			$duihuanjifen=$user_row['duihuanjifen'];
			$dongjiejifen=$user_row['dongjiejifen'];
			$city=$user_row['city'];
			$status=$_POST['status'];
			$beizhu=$_POST['beizhu'];
			
			$edit_moneylog=array(
			'status'=>$status,		
			'beizhu'=>$beizhu,																	
    		);
			$this->my_moneylog_mod->edit('id='.$log_id,$edit_moneylog);
			
			if($status==3)//审核不通过
			{
				$new_user_money=$user_money+$moneydj;
				$new_user_money_dongjie=$user_money_dongjie-$moneydj;
				$edit_money=array(
				'money'=>$new_user_money,
				'money_dj'=>$new_user_money_dongjie
				);
			   	$this->my_money_mod->edit('user_id='.$userid,$edit_money);
				$this->show_warning('caozuochenggong','fanhui','index.php?app=user&act=zhuanzhang');
			}
			if($status==2)//审核通过
			{
				$op_id=date('Ymdhis').rand(100,999);
				$data=array(
				'user_id'=>DeCode($userid,'E'),
				'money'=>$moneydj,
				'op_id'=>$op_id,
				'mall_key'=>DeCode($op_id,'E')
				);
				if($zhuanzhang==1)//向借贷平台转账
				{
					$result=getHTML("http://zz.test.cn/index.php?user&q=code/account/i_accountm2l",$data);
					$typee=111;
				}
				if($zhuanzhang==3)//向积分商城转账
				{
					$result=getHTML("http://zhuzhan.cn/index.php?app=user&act=i_accountl2m&lei=4",$data);
					$typee=118;
				}
				if($zhuanzhang==4)//向易货商城转账
				{
					$result=getHTML("http://yh.cn/index.php?app=user&act=i_accountl2m&lei=6",$data);
					$typee=127;
				}
				$res=json_decode($result,true);
				if($res['result']==1)
				{
					//资金流水
					$array_log=array(					
						'jifen'=>0,
						'money'=>'-'.$moneydj,
						'time'=>date('Y-m-d H:i:s'),
						'user_name'=>$user_name,
						'user_id'=>$userid,
						'zcity'=>$city,
						'type'=>$typee,
						's_and_z'=>2,
						'beizhu'=>'',
						'orderid'=>'',
						'dq_money'=>$user_money,
						'dq_money_dj'=>$user_money_dongjie-$moneydj,
						'dq_jifen'=>$duihuanjifen,
						'dq_jifen_dj'=>$dongjiejifen	
					);
					$this->moneylog_mod->add($array_log);//资金流水					
					$sql="update ".DB_PREFIX."my_money set money_dj=money_dj-$moneydj where user_id='$userid' limit 1";	
					$this->moneylog_mod->db->query($sql);
					$this->my_moneylog_mod->edit('id='.$log_id,array('status1'=>1,));
					$this->show_message('zhuanzhangchenggong','fanhui','index.php?app=user&act=zhuanzhang');
				}
				else
				{
					$new_user_money=$user_money+$moneydj;
					$new_user_money_dongjie=$user_money_dongjie-$moneydj;
					$edit_money=array(
					'money'=>$new_user_money,
					'money_dj'=>$new_user_money_dongjie
					);
			   		$this->my_money_mod->edit('user_id='.$userid,$edit_money);
					$this->my_moneylog_mod->edit('id='.$log_id,array('status1'=>2,));
					$this->show_warning('zhuanzhangshibai','fanhui','index.php?app=user&act=zhuanzhang');
					return ;
				}
				
			}
			
		}
		else
		{
			$money_row['money']=abs($money_row['money']);
			$this->assign('money_row',$money_row);
			$this->display('zhuanzhang_shenhe.html');
	    	return;
		}
		
	}
	
	

}

?>
