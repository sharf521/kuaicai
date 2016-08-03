<?php

class My_moneyModule extends AdminbaseModule
{
    function __construct()
    {
        $this->My_moneyModule();
    }

    function My_moneyModule()
    {
        parent::__construct();
		
        $this->my_money_mod =& m('my_money');
		$this->my_moneylog_mod =& m('my_moneylog');
		$this->my_mibao_mod =& m('my_mibao');
		$this->my_card_mod =& m('my_card');
		$this->my_jifen_mod =& m('my_jifen');
		$this->my_paysetup_mod =& m('my_paysetup');	
		$this->canshu_mod =& m('canshu');
		$this->type_mod =& m('type');
		$this->kaiguan_mod =& m('kaiguan');	
		$this->member_mod =& m('member');
		$this->_admin_mod = & m('userpriv');	
		$this->accountlog_mod =& m('accountlog');
		$this->zongjine_mod =& m('zongjine');
		$this->city_mod =& m('city');
		$this->bikulog_mod =& m('bikulog');
		$this->moneylog_mod =& m('moneylog');
		$this->webservicelist_mod =& m('webservice_list');
		$this->my_webserv_mod =& m('my_webserv');
		
		$user=$this->visitor->get('user_name');
		$user_id=$this->visitor->get('user_id');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id = '$user_id' limit 1");
	$city=$row_member['city'];	
    }

 	function index()
    {
	$user=$this->visitor->get('user_name');
	$user_id=$this->visitor->get('user_id');
	/*echo $user_id;*/
	$index=$this->member_mod->getAll("select * from ".DB_PREFIX."member where user_name = '$user'");
    $this->assign('index', $index);
	
	$priv=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$user_id' and store_id=0 limit 1");
    $this->assign('priv', $priv);
	
      $this->display('index_index.html');
	   return;
	}
//�û��ʽ��б� ������
 	function user_money_list()
	{
	
	$user=$this->visitor->get('user_name');//��ǰ��¼���û�
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
	/*echo $city;*/
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
	$so_user_name=$_GET["soname"];	
	$somoney=$_GET["somoney"];	
	$endmoney=$_GET["endmoney"];	
	
    $page = $this->_get_page();

	
	//�����û�Ϊ�վ�����ȫ��	
	if(empty($so_user_name))
	{
    //��� ��ʼ��� ������� ��Ϊ��
	
    if(empty($somoney) and empty($endmoney)) 
    {
	if($privs=="all")
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => '',//����
    'limit' => $page['limit'],
	'order' => "money desc",
	'count' => true));	
	//������Ȼ�г������û���������С����
	}
	else
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => "city = '$city'",//����
    'limit' => $page['limit'],
	'order' => "money desc",
	'count' => true));	
	//������Ȼ�г������û���������С����
	}
	}
	
	//print_r($index);
	
	//�������� �û�������ʼ���-�������
	else
	{
	if(empty($somoney)){$somoney=0;}//��ʼ���Ϊ�վ�=0
	if(empty($endmoney)){$endmoney=9999999;}//�������Ϊ�վ�=9999999
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$so_user_name%' and money>='$somoney' and money<='$endmoney'",//����
    'limit' => $page['limit'],
	'order' => "money desc",
	'count' => true));	
	}
	}
	else
	{//�����û�����Ϊ��
	
    //�û���Ϊ�� ˫ʱ��Ϊ��
    if(empty($somoney) and empty($endmoney)) 
    {
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$so_user_name%'",//����
    'limit' => $page['limit'],
	'order' => "money desc",
	'count' => true));	
	}
	//�û���Ϊ�� ˫ʱ��Ҳ��Ϊ��
	else
	{
	if(empty($somoney)){$somoney=0;}
	if(empty($endmoney)){$endmoney=999999999;}
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$so_user_name%' and money>='$somoney' and money<='$endmoney'",//����
    'limit' => $page['limit'],
	'order' => "money desc",
	'count' => true));	
	}
	}
	//print_r($index);
	    $city_row=array();
		$result=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
		
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];
			$user_id=$index[$key]['user_id'];
        }	
		
		$page['item_count'] = $this->my_money_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('user_money_list.html'); 
	    return;
	}


//�����û��ʽ�   
 	function user_money_add()
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	$city=$row_member['city'];
	$pag = empty($_GET['page']) ? 0 : $_GET['page'];
	   if($_POST)
	   {
	   $user_name= trim($_POST['user_name']);
	   $post_money= trim($_POST['post_money']);
	   $jia_or_jian= trim($_POST['jia_or_jian']);
	   $riqi=date('Y-m-d H:i:s');
	  // $time_edit= trim($_POST['time_edit']);
	   $log_text= trim($_POST['log_text']);	   
	   if(empty($user_name) or empty($post_money) or empty($jia_or_jian))
       {
	   		$this->show_warning('user_money_add_nizongdeshurudianshenmeba');
	   		return;
	   }
	   if (preg_match("/[^0.-9]/",$post_money))
       {
	   $this->show_warning('cuowu_nishurudebushishuzilei'); 
       return;
       }
$money_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_name='$user_name'");	
$user_ids=$money_row['user_id'];  
$my_money=$money_row['money'];
$city=$money_row['city'];
$my_money_dj=$money_row['money_dj'];
$duihuanjifen=$money_row['duihuanjifen'];
$dongjiejifen=$money_row['dongjiejifen'];

$canshu_row=$this->canshu_mod->getRow("select * from ".DB_PREFIX."canshu ");	
$zong_jinbi=$canshu_row['zong_jinbi'];
$yu_jinbi=$canshu_row['yu_jinbi'];
$zong_money=$canshu_row['zong_money'];
$zong_jifen=$canshu_row['zong_jifen'];

	   if(empty($user_ids))
       {
	   		$this->show_warning('user_money_add_cuowugaiyonghubucunzai');
	   		return;
	   }
	   if($jia_or_jian=="jia")
	   {
	   $money=$my_money+$post_money;
	   $dj_money=$my_money_dj;
	   }
	   if($jia_or_jian=="jian")
	   {
	   if($my_money>=$post_money)
	   {	   
	    $money=$my_money-$post_money;
	    $dj_money=$my_money_dj;
	   }
       else
	   {
	   		$this->show_warning('user_money_add_cuowugaiyonghudangqianyuebuzukouchu');
	        return;
	   }
	   } 
	   if($jia_or_jian=="dong")
	   {
	   		if($my_money>=$post_money)
			{
				$dj_money=$my_money_dj+$post_money;
				$money=$my_money-$post_money;
			}
			else
			{
				$this->show_warning('xiaoyudongjiejine');
	        	return;
			}
	   }
	   if($jia_or_jian=="jie")
	   {
	   		if($my_money_dj>=$post_money)
			{
				$dj_money=$my_money_dj-$post_money;
				$money=$my_money+$post_money;
			}
			else
			{
				$this->show_warning('xiaoyujiedongjine');
	        	return;
			}
	   }
	   //д��LOG��¼
	   $dq_time=date("Y-m-d-His",time());
	   
	   if($jia_or_jian=="jian")
	   {
	   $logs_array=array(
	   'user_id'=>$user_ids,
	   'user_name'=>$user_name,
	   'log_text'=>$log_text,
	   'leixing'=>30,
	   'add_time'=>time(),
	   'admin_name' =>$this->visitor->get('user_name'),
	   'order_sn'=>$dq_time,
	   'money'=>"-".$post_money, 
	   'caozuo'=>50,
	   's_and_z'=>1,
	   'city'=>$city,
	   'type'=>24,
	   'riqi'=>$riqi,
	   'dq_money'=>$money,//�ӳ�ֵ�Ľ��
	   'dq_money_dj'=>$my_money_dj,
	   'dq_jifen'=>$duihuanjifen,
	   'dq_jifen_dj'=>$dongjiejifen,
	    );
	//��ӱҿ���־	
	$new_yu_jinbi=$yu_jinbi+$post_money;
	$new_zong_money=$zong_money-$post_money;
$addbiku=array(
	'money'=>'+'.$post_money,
	'riqi'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_ids,
	'biku_city'=>$city,
	'type'=>28,
	's_and_z'=>1,
	'beizhu'=>$log_text,
	'dq_jinbi'=>$zong_jinbi,
	'dq_yujinbi'=>$new_yu_jinbi,
);
 $this->bikulog_mod->add($addbiku);

//�����ٽ�����ӵ�accountlog��

/*$addaccount=array(
	'money'=>'-'.$post_money,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_ids,
	'zcity'=>$city,
	'type'=>28,
	's_and_z'=>2,
	'beizhu'=>$log_text,
	'dq_money'=>$new_zong_money,
	'dq_jifen'=>$zong_jifen,
);
 $this->accountlog_mod->add($addaccount);*/
	//�������˻�
	
	$can_id=1;
	$edit_canshu=array(
	'yu_jinbi'=>$new_yu_jinbi,
	//'zong_money'=>$new_zong_money,
	);
	$this->canshu_mod->edit('id='.$can_id,$edit_canshu);	
	
	//���û����ٵĽ�����moneylog��־
	$addlog=array(
	'money'=>'-'.$post_money,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_ids,
	'zcity'=>$city,
	'type'=>24,
	's_and_z'=>2,
	'beizhu'=>$log_text,
	'dq_money'=>$money,
    'dq_money_dj'=>$my_money_dj,
    'dq_jifen'=>$duihuanjifen,
    'dq_jifen_dj'=>$dongjiejifen,
);
	   }
	   if($jia_or_jian=="jia")
	   {
	   $logs_array=array(
	   'user_id'=>$user_ids,
	   'user_name'=>$user_name,
	   'log_text'=>$log_text,
	   'leixing'=>30,
	   'add_time'=>time(),
	   'admin_name' =>$this->visitor->get('user_name'),
	   'order_sn'=>$dq_time,
	   'money'=>$post_money, 
	   'caozuo'=>50,
	   's_and_z'=>1,
	   'type'=>24,
	   'city'=>$city,
	   'riqi'=>$riqi,
	   'dq_money'=>$money,//�ӳ�ֵ�Ľ��
	   'dq_money_dj'=>$my_money_dj,
	   'dq_jifen'=>$duihuanjifen,
	   'dq_jifen_dj'=>$dongjiejifen,
	   );
	   //��ӱҿ���־	
	$new_yu_jinbi=$yu_jinbi-$post_money;
	$new_zong_money=$zong_money+$post_money;
$addbiku=array(
	'money'=>'-'.$post_money,
	'riqi'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_ids,
	'biku_city'=>$city,
	'type'=>27,
	's_and_z'=>2,
	'beizhu'=>$log_text,
	'dq_jinbi'=>$zong_jinbi,
	'dq_yujinbi'=>$new_yu_jinbi,
);
 $this->bikulog_mod->add($addbiku);

//�����ٽ�����ӵ�accountlog��

/*$addaccount=array(
	'money'=>'+'.$post_money,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_ids,
	'zcity'=>$city,
	'type'=>27,
	's_and_z'=>1,
	'beizhu'=>$log_text,
	'dq_money'=>$new_zong_money,
	'dq_jifen'=>$zong_jifen,
);
 $this->accountlog_mod->add($addaccount);*/
	//�������˻�
	
	$can_id=1;
	$edit_canshu=array(
	'yu_jinbi'=>$new_yu_jinbi,
	//'zong_money'=>$new_zong_money,
	);
	$this->canshu_mod->edit('id='.$can_id,$edit_canshu);
	
	//���û����ӵĽ�����moneylog��־
	$addlog=array(
	'money'=>$post_money,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_ids,
	'zcity'=>$city,
	'type'=>24,
	's_and_z'=>1,
	'beizhu'=>$log_text,
	'dq_money'=>$money,//�ӳ�ֵ�Ľ��
    'dq_money_dj'=>$my_money_dj,
    'dq_jifen'=>$duihuanjifen,
    'dq_jifen_dj'=>$dongjiejifen,
);   
}

  if($jia_or_jian=="dong")
	{
	//���û�����Ľ�����moneylog��־
	$addlog=array(
	'money'=>'-'.$post_money,
	'money_dj'=>$post_money,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_ids,
	'zcity'=>$city,
	'type'=>19,
	'beizhu'=>$log_text,
	'dq_money'=>$money,//����
    'dq_money_dj'=>$dj_money,
    'dq_jifen'=>$duihuanjifen,
    'dq_jifen_dj'=>$dongjiejifen,
    );
	}
if($jia_or_jian=="jie")
	{
	//���û�����Ľ�����moneylog��־
	$addlog=array(
	'money'=>$post_money,
	'money_dj'=>'-'.$post_money,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_ids,
	'zcity'=>$city,
	'type'=>20,
	'beizhu'=>$log_text,
	'dq_money'=>$money,//�ⶳ
    'dq_money_dj'=>$dj_money,
    'dq_jifen'=>$duihuanjifen,
    'dq_jifen_dj'=>$dongjiejifen,
    );
	}
	   $this->my_moneylog_mod->add($logs_array);
	   $this->moneylog_mod->add($addlog);
	   //д��LOG��¼
	   $money_array=array(
	   'money'=>$money,
	   'money_dj'=>$dj_money,
	   );
	   $this->my_money_mod->edit('user_id='.$user_ids,$money_array);
		if($jia_or_jian=="jia")
		{
	   	$this->show_message('user_money_add_zengjiayonghujinechenggong','fanhui','index.php?module=my_money&act=user_money_list');
	        return;
		}
		if($jia_or_jian=="jian")
		{
	   	$this->show_message('user_money_add_jianshaoyonghujinechenggong','fanhui','index.php?module=my_money&act=user_money_list');
	        return;
		}
		if($jia_or_jian=="dong")
		{
	   	$this->show_message('user_money_add_dongjieyonghujinechenggong','fanhui','index.php?module=my_money&act=user_money_list');
	        return;
		}
		if($jia_or_jian=="jie")
		{
	   	$this->show_message('user_money_add_jiedongyonghujinechenggong','fanhui','index.php?module=my_money&act=user_money_list');
	        return;
		}
	   }
	   else
	   {
	   $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
	   $user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';
	   if(!empty($user_id))
       {
       $index=$this->my_money_mod->find('user_id='.$user_id);
	   }
	   $this->assign('index', $index);
       $this->display('user_money_add.html'); 
	   }
	   return;
	}

//�༭ҳ��
function user_money_edit()
{

/* $id = empty($_GET['id']) ? 0 : intval($_GET['id']);*/
 
    
	   
	   if($_POST)
	   {
	   $id= trim($_POST['id']);
	   $user_id= trim($_POST['user_id']);
	   $user_name= trim($_POST['user_name']);
	   $money= trim($_POST['money']);
	   $bank_name= trim($_POST['bank_name']);
	   $bank_username= trim($_POST['bank_username']);
	   $type= trim($_POST['type']);
	   $status= trim($_POST['status']);
	  // $time_edit= trim($_POST['time_edit']);
	   $riqi= trim($_POST['riqi']);	   
	   if(empty($type) or empty($status))
       {
	   		$this->show_warning('user_money_edit_nizongdeshurudianshenmeba');
	   		return;
	   }
/*	   
$money_row=$this->my_money_mod->getrow("select * from ".DB_PREFIX."my_moneylog where id='29'");	
$id=$money_row['id'];  
$user_id=$money_row['user_id']; 
$money=$money_row['money'];
$bank_name=$money_row['bank_name'];
$bank_username=$money_row['bank_username'];
$riqi=$money_row['riqi'];
$type=$money_row['type'];
$status=$money_row['status'];
$id=29;*/
	   if(empty($id))
       {
	   		$this->show_warning('user_money_add_cuowugaiyonghubucunzai');
	   		return;
	   }
	   
	   //д��LOG��¼
	   $dq_time=date("Y-m-d-His",time());
	   
	   
	   
	  /* $logs_array=array(
	   'user_name'=>$user_name,
	   'money'=>$money,
	   'bank_name'=>$bank_name,
	   'bank_username'=>$bank_username,
	   'riqi' =>$riqi,
	   'type'=>$type,
	   'status'=>$status, 
	  
	   );
	
	   $this->my_moneylog_mod->edit('id='.$id,$logs_array);*/
	   //д��LOG��¼
	   $money_array=array(
	   'user_name'=>$user_name,
	 /*  'money'=>$money,
	   'bank_name'=>$bank_name,
	   'bank_username'=>$bank_username,
	   'riqi' =>$riqi,*/
	   'type'=>$type,
	   'status'=>$status, 
	  
	   );
	   
	   $this->my_moneylog_mod->edit('id='.$id,$money_array);

	   		$this->show_message('�༭�ɹ�','�����б�','index.php?module=my_money&act=user_money_list');
	        return;
	   }
	   else
	   {
	   $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	   $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
	   $user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';
	   if(!empty($id))
       {
       $index=$this->my_moneylog_mod->find('id='.$id);
	   }
	   $this->assign('index', $index);
       $this->display('user_money_edit.html'); 
	   }
	   return;
	



}


//�鿴�������Ӽ����ʽ�log
 	function user_money_log()
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$so_user_name=$_GET["soname"];
	$sotime=$_GET["sotime"];
	$endtime=$_GET["endtime"];
	if(!empty($so_user_name))
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
	
	
	$page = $this->_get_page();	
	if($privs=="all")
	{
		if(!empty($so_user_name))
		{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime'";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59'";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%'";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime'";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59'";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59'";
		}
		
	}
		
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('user_money_log.html'); 
	   return;
	}

//���˻��ʽ���ˮ

function zijinliushui()
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
    $city=$priv_row['city'];
	$this->assign('priv_row',$priv_row);
	$s_and_z=$_GET["s_and_z"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];
	$suoshuzhan=$_GET["suoshuzhan"];	
	$this->assign('s_and_z',$s_and_z);
	$this->assign('sotime',$sotime);
	$this->assign('endtime',$endtime);
	$this->assign('suoshuzhan',$suoshuzhan);
	$conditions="1=1";
	if(!empty($s_and_z))
	{ 
		$conditions.=" and s_and_z='$s_and_z'";
	}
	if(!empty($sotime))
	{
		$conditions.=" and time>='$sotime'";
	}
	if(!empty($endtime))
	{
		$conditions.=" and time<='$endtime 24:59:59'";
    }
	if(!empty($suoshuzhan))
	{
		$conditions.=" and zcity='$suoshuzhan'";
	}
	
	$page = $this->_get_page();	
	if($privs=="all")
	{
		$index=$this->accountlog_mod->find(array(
	       'conditions' => $conditions,
            'limit' => $page['limit'],
			'order' => "account_id desc",
			'count' => true));
	}
	else
	{
			
		$index=$this->accountlog_mod->find(array(
		'conditions' =>$conditions. 'zcity='.$city,
		'limit' => $page['limit'],
		'order' => "account_id desc",
		'count' => true));
	}
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
			$index[$key]['city_name'] = $city_row[$val['zcity']];	
        }
		$page['item_count'] = $this->accountlog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('account_list.html'); 
	   return;
	}
	
	//�ҿ��ʽ���ˮ

function bikuliushui()
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
	$s_and_z=$_GET["s_and_z"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
if(!empty($s_and_z))
	   {
			if(!empty($sotime) and empty($endtime))
			{
				$conditions="s_and_z='$s_and_z' and riqi>='$sotime' ";
			}
			if(empty($sotime) and !empty($endtime))
			{
				$conditions="s_and_z='$s_and_z' and riqi<='$endtime 24:59:59' ";
			}
			if(!empty($sotime) and !empty($endtime))
			{
				$conditions="s_and_z='$s_and_z' and riqi>='$sotime' and riqi<='$endtime 24:59:59' ";
			}
			if(empty($sotime) and empty($endtime))
			{
				$conditions="s_and_z='$s_and_z' ";
			}
	}
	else
	{
		  if(!empty($sotime) and empty($endtime))
		  {
			  $conditions="riqi>='$sotime'";
		  }
		  if(empty($sotime) and !empty($endtime))
		  {
			  $conditions="riqi<='$endtime 24:59:59'";
		  }
		  if(!empty($sotime) and !empty($endtime))
		  {
			  $conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59'";
		  }
		
	}
	$page = $this->_get_page();	
	if($privs=="all")
	{
		if(!empty($s_and_z))
	   {
			if(!empty($sotime) and empty($endtime))
			{
				$conditions="s_and_z='$s_and_z' and riqi>='$sotime' ";
			}
			if(empty($sotime) and !empty($endtime))
			{
				$conditions="s_and_z='$s_and_z' and riqi<='$endtime 24:59:59' ";
			}
			if(!empty($sotime) and !empty($endtime))
			{
				$conditions="s_and_z='$s_and_z' and riqi>='$sotime' and riqi<='$endtime 24:59:59' ";
			}
			if(empty($sotime) and empty($endtime))
			{
				$conditions="s_and_z='$s_and_z' ";
			}
	}
	else
	{
		  if(!empty($sotime) and empty($endtime))
		  {
			  $conditions="riqi>='$sotime'";
		  }
		  if(empty($sotime) and !empty($endtime))
		  {
			  $conditions="riqi<='$endtime 24:59:59'";
		  }
		  if(!empty($sotime) and !empty($endtime))
		  {
			  $conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59'";
		  }
		
	}
		$index=$this->bikulog_mod->find(array(
	        'conditions' => $conditions,
            'limit' => $page['limit'],
			'order' => "biku_id desc",
			'count' => true));
			}
			else
			{
			
			$index=$this->bikulog_mod->find(array(
	        'conditions' =>$conditions. 'biku_city='.$city,
            'limit' => $page['limit'],
			'order' => "biku_id desc",
			'count' => true));
			}
			$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['biku_city']];	
        }
		$page['item_count'] = $this->bikulog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('biku_list.html'); 
	   return;
	}

//���˻��ʽ���ˮ�༭
function zmoney_edit()
{
if($_POST)
	   {
	   $id= trim($_POST['id']);
	   echo $id;
	   $user_id= trim($_POST['user_id']);
	   $user_name= trim($_POST['user_name']);
	   echo user_name;
	 $money= trim($_POST['money']);
	   $bank_name= trim($_POST['bank_name']);
	   echo $bank_name;
	   $bank_username= trim($_POST['bank_username']);
	   echo $bank_username;
	   $type= trim($_POST['type']);
	   $status= trim($_POST['status']);
	  // $time_edit= trim($_POST['time_edit']);
	   $riqi= trim($_POST['riqi']);	   
	   if(empty($type) or empty($status))
       {
	   		$this->show_warning('user_money_edit_nizongdeshurudianshenmeba');
	   		return;
	   }

	   if(empty($id))
       {
	   		$this->show_warning('user_money_add_cuowugaiyonghubucunzai');
	   		return;
	   }
	   
	   //д��LOG��¼
	   $dq_time=date("Y-m-d-His",time());

	   $money_array=array(
	   'user_name'=>$user_name,
	
	   'type'=>$type,
	   'status'=>$status, 
	  
	   );
	   
	   $this->my_moneylog_mod->edit('id='.$id,$money_array);

	   		$this->show_message('�༭�ɹ�','�����б�','index.php?module=my_money&act=user_money_list');
	        return;
	   }
	   else
	   {
	   $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	   $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
	   $user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';
	   if(!empty($id))
       {
       $index=$this->my_moneylog_mod->find('id='.$id);
	   }
	   $this->assign('index', $index);
       $this->display('user_money_edit.html'); 
	   }
	   return;
	}
	
	function zhanghuzonge()
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$page = $this->_get_page();	
	if($privs=="all")
	{
		$index=$this->canshu_mod->find(array(
	        /*'conditions' => 'leixing=30 and caozuo=50 or leixing=11 or caozuo=11',*/
            'limit' => $page['limit'],
			'order' => "id asc",
			'count' => true));
	}
			
		$page['item_count'] = $this->canshu_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('zhanghuzonge.html'); 
	   return;
	}
	



//�鿴����
 	function logs_shouru()
    {
	   $this->show_warning('logs_kaifatishi');
       //$this->display('logs_shouru.html'); 
	   return;
	}
//�鿴֧��
 	function logs_zhichu()
    {
	   $this->show_warning('logs_kaifatishi');
       //$this->display('logs_zhichu.html'); 
	   return;
	}
//�鿴ת��
 	function logs_zhuanru()
    {
	   $this->show_warning('logs_kaifatishi');
       //$this->display('logs_zhuanru.html'); 
	   return;
	}
//�鿴ת��
 	function logs_zhuanchu()
    {
	   $this->show_warning('logs_kaifatishi');
       //$this->display('logs_zhuanchu.html'); 
	   return;
	}
//�鿴��ֵ
 	function logs_chongzhi()
    {
	   $this->show_warning('logs_kaifatishi');
       //$this->display('logs_chongzhi.html'); 
	   return;
	}
	
	//�����޸�
 	function tixianfeilv_xiugai()
    {
	$log_id=1;
	$tixianfeilv=trim($_POST['tixianfeilv']);
	$ks_txfeilv=trim($_POST['ks_txfeilv']);
	$ks_fei=trim($_POST['ks_fei']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'tixianfeilv'=>$tixianfeilv,
			'ks_txfeilv'=>$ks_txfeilv,
			'ks_fei'=>$ks_fei,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=tixianfeilv_xiugai',
    'fanhui',    'index.php?module=my_money&act=tixianfeilv_xiugai');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('tixianfeilv_xiugai.html');
	    return;
	}
	}

	function chongzhifeilv_xiugai()
    {
	$log_id=1;
	$chongzhifeilv=trim($_POST['chongzhifeilv']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'chongzhifeilv'=>$chongzhifeilv,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=chongzhifeilv_xiugai',
    'fanhui',    'index.php?module=my_money&act=chongzhifeilv_xiugai');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('chongzhifeilv_xiugai.html');
	    return;
	}
	}
	
	function duihuanjifenfeilv_xiugai()
    {
	$log_id=1;
	$duihuanjifenfeilv=trim($_POST['duihuanjifenfeilv']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'duihuanjifenfeilv'=>$duihuanjifenfeilv,
			'adv_tj'=>$_POST['adv_tj'],																					
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=duihuanjifenfeilv_xiugai',
    'fanhui',    'index.php?module=my_money&act=duihuanjifenfeilv_xiugai');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('duihuanjifenfeilv_xiugai.html');
	    return;
	}
	}
	
	
	function duihuanxianjinfeilv_xiugai()
    {
	$log_id=1;
	$duihuanxianjinfeilv=trim($_POST['duihuanxianjinfeilv']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'duihuanxianjinfeilv'=>$duihuanxianjinfeilv,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=duihuanxianjinfeilv_xiugai',
    'fanhui',    'index.php?module=my_money&act=duihuanxianjinfeilv_xiugai');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('duihuanxianjinfeilv_xiugai.html');
	    return;
	}
	}
	function jifenxianjinbili()
    {
	$log_id=1;
	$jifenxianjin=trim($_POST['jifenxianjin']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'jifenxianjin'=>$jifenxianjin,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=jifenxianjinbili',
    'fanhui',    'index.php?module=my_money&act=jifenxianjinbili');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('jifenxianjin.html');
	    return;
	}
	}
	//���۱���
	function daishou()
    {
	$log_id=1;
	$daishou=trim($_POST['daishou']);
	$zhe_jifen=trim($_POST['zhe_jifen']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'daishou'=>$daishou,
			'zhe_jifen'=>$zhe_jifen,																					
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=daishou',
    'fanhui',    'index.php?module=my_money&act=daishou');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('daishou.html');
	    return;
	}
	}
	//�̳��ܱҿ�
	function biku()
    {
	$log_id=1;
	
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('zong_jinbi.html');
	
	}
function zengjia()
    {
	$user_name=$this->visitor->get('user_name');
	$user_id=$this->visitor->get('user_id');
	$log_id=1;
	$zong_jinbi=trim($_POST['zong_jinbi']);
	$jinbi_row=$this->canshu_mod->getRow("select zong_jinbi,yu_jinbi from ".DB_PREFIX."canshu");
	$yu_jinbi=$jinbi_row['yu_jinbi'];
	$zong_jinbi_old=$jinbi_row['zong_jinbi'];
	$new_zongjinbi=$zong_jinbi_old+$zong_jinbi;
	$new_yujinbi=$yu_jinbi+$zong_jinbi;
	$riqi=date('Y-m-d H:i:s');
	
	if($_POST)
	{
	
	//���²�����
	$edit_canshu=array(
			'zong_jinbi'=>$new_zongjinbi,
			'yu_jinbi'=>$new_yujinbi,																				
    );
	
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
	
	//���±ҿ���־��
	$beizhu =$this->visitor->get('user_name').Lang::get('zengjiabiku').$zong_jinbi.Lang::get('yuan');
$add_biku=array(
	'money'=>'+'.$zong_jinbi,
	'user_id'=>$user_id,
	'user_name'=>$user_name,
	'riqi'=>$riqi,
	'type'=>25,
	's_and_z'=>1,
	'beizhu'=>$beizhu,
	'dq_jinbi'=>$new_zongjinbi,
    'dq_yujinbi'=>$new_yujinbi,
);
 $this->bikulog_mod->add($add_biku);

    $this->show_message('zengjiachenggong',
    'caozuochenggong', 'index.php?module=my_money&act=biku',
    'fanhui',    'index.php?module=my_money&act=biku');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('zengjia_jinbi.html');
	    return;
	}
	}
	
	//�Ƽ�����
	function tuijianjiangli()
    {
	$log_id=1;
	$tuijianjiangli=trim($_POST['tuijianjiangli']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'tuijianjiangli'=>$tuijianjiangli,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=tuijianjiangli',
    'fanhui',    'index.php?module=my_money&act=tuijianjiangli');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('tuijianjiangli.html');
	    return;
	}
	}
	
	
	//�Ƽ�����˰��
	function tuijianjianglishuishou()
    {
	$log_id=1;
	$jlshuishou=trim($_POST['jlshuishou']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'jlshuishou'=>$jlshuishou,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=tuijianjianglishuishou',
    'fanhui',    'index.php?module=my_money&act=tuijianjianglishuishou');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('tuijianjiangli_shuishou.html');
	    return;
	}
	}
	//�Ƽ���������
	
	function tuijianjifen()
    {
	$log_id=1;
	$tuijianjifen=trim($_POST['tuijianjifen']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'tuijianjifen'=>$tuijianjifen,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=tuijianjifen',
    'fanhui',    'index.php?module=my_money&act=tuijianjifen');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('tuijianjifen.html');
	    return;
	}
	}
	
	
	//�Ƽ�����˰��
	function tuijianjifenshuishou()
    {
	$log_id=1;
	$jfshuishou=trim($_POST['jfshuishou']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'jfshuishou'=>$jfshuishou,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=tuijianjifenshuishou',
    'fanhui',    'index.php?module=my_money&act=tuijianjifenshuishou');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('tuijianjifen_shuishou.html');
	    return;
	}
	}
	
	
	
//��������	
function duihuanjifen_kaiguan()
    {
	$log_id=1;
	$dhjf=trim($_POST['dhjf']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'dhjf'=>$dhjf,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=duihuanjifen_kaiguan',
    'fanhui',    'index.php?module=my_money&act=duihuanjifen_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('duihuanjifen_kaiguan.html');
	    return;
	}
	}
	
		
function chongzhi_kaiguan()
    {
	$log_id=1;
	$czkg=trim($_POST['czkg']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'czkg'=>$czkg,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=chongzhi_kaiguan',
    'fanhui',    'index.php?module=my_money&act=chongzhi_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('chongzhi_kaiguan.html');
	    return;
	}
	}		
	
function jifenzhifu_kaiguan()
    {
	$log_id=1;
	$jfzf=trim($_POST['jfzf']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'jfzf'=>$jfzf,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=jifenzhifu_kaiguan',
    'fanhui',    'index.php?module=my_money&act=jifenzhifu_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('jifenzhifu_kaiguan.html');
	    return;
	}
	}	
	
	
	function xianjinzhifu_kaiguan()
    {
	$log_id=1;
	$xjzf=trim($_POST['xjzf']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'xjzf'=>$xjzf,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=xianjinzhifu_kaiguan',
    'fanhui',    'index.php?module=my_money&act=xianjinzhifu_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('xianjinzhifu_kaiguan.html');
	    return;
	}
	}					
	function gonghuo_kaiguan()
    {
	$log_id=1;
	$gonghuo=trim($_POST['gonghuo']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'gonghuo'=>$gonghuo,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=gonghuo_kaiguan',
    'fanhui',    'index.php?module=my_money&act=gonghuo_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('gonghuo_kaiguan.html');
	    return;
	}
	}					
	function gcategory_kaiguan()
    {
	$log_id=1;
	$gcategory=trim($_POST['gcategory']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'gcategory'=>$gcategory,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=gcategory_kaiguan',
    'fanhui',    'index.php?module=my_money&act=gcategory_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('gcategory_kaiguan.html');
	    return;
	}
	}				
	
	
	function xianjinjiangli_kaiguan()
    {
	$log_id=1;
	$xjjl=trim($_POST['xjjl']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'xjjl'=>$xjjl,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=xianjinjiangli_kaiguan',
    'fanhui',    'index.php?module=my_money&act=xianjinjiangli_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('xianjinjiangli_kaiguan.html');
	    return;
	}
	}					
	
	function jifenjiangli_kaiguan()
    {
	$log_id=1;
	$jfjl=trim($_POST['jfjl']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'jfjl'=>$jfjl,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=jifenjiangli_kaiguan',
    'fanhui',    'index.php?module=my_money&act=jifenjiangli_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('jifenjiangli_kaiguan.html');
	    return;
	}
	}	
	
	function jifen_kaiguan()
    {
	$log_id=1;
	$jifen_zhuan=trim($_POST['jifen_zhuan']);
	
	if($_POST)
	{
	$edit_kaiguan=array(
			'jifen_zhuan'=>$jifen_zhuan,																				
    );
	$this->kaiguan_mod->edit('id='.$log_id,$edit_kaiguan);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=jifen_kaiguan',
    'fanhui',    'index.php?module=my_money&act=jifen_kaiguan');
	}
	else
	{
	    $logs_data=$this->kaiguan_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('jifen_kaiguan.html');
	    return;
	}
	}					
	
	
	
			
//�鿴�����������

function cz_wei_shenhe()//caozuo=60
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row',$priv_row);
	//��������
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	$suoshuzhan=$_GET["suoshuzhan"];
	$this->assign('so_user_name',$so_user_name);
	$this->assign('sotime',$sotime);
	$this->assign('endtime',$endtime);
	$this->assign('suoshuzhan',$suoshuzhan);
	$conditions="1=1";
	if(!empty($so_user_name))
	{ 
		$conditions.=" and user_name like '%$so_user_name%'";
	}
	if(!empty($sotime))
	{ 
		$conditions.=" and riqi >= '$sotime'";
	}
	if(!empty($endtime))
	{
		$conditions.=" and riqi<='$endtime 24:59:59'";
	}
	if(!empty($suoshuzhan))
	{
		$conditions.=" and city='$suoshuzhan'";
	}
	
	
	      $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. ' and leixing=30 and status=0',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. ' and leixing=30 and status=0 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			
		$city_row=array();
		$result=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
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
        }		
			
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('cz_wei_shenhe.html'); 
	   return;
	}



function cz_shenhe_user()
    {
	$log_id=$_GET['log_id'];
	$user_id=$_GET['user_id'];
	$user_name=$_GET['user_name'];
	$status=trim($_POST['status']);
	$type=trim($_POST['type']);
	$log_text=trim($_POST['log_text']);
	$riqi=date('Y-m-d H:i:s');
	$user_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_name='$user_name'");	
    $user_money=$user_row['money'];
	$user_money_dongjie=$user_row['money_dj'];
	$duihuanjifen=$user_row['duihuanjifen'];
	$dongjiejifen=$user_row['dongjiejifen'];
	$this->canshu_mod=& m('canshu');
	$jinbi_row=$this->canshu_mod->getRow("select yu_jinbi,zong_money,chongzhifeilv,zong_jinbi,zong_jifen,chongzhifeimin,chongzhifeimax from ".DB_PREFIX."canshu");
	if($_POST)
	{
	$edit_moneylog=array(
			'status'=>$status,		
			'type'=>$type,
			'log_text'=>$log_text,																	
    );
	$this->my_moneylog_mod->edit('id='.$log_id,$edit_moneylog);

if($status=="1")
									
{	
$money_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");
$shijichongzhi=$money_row['money'];//ʵ�ʳ�ֵ��
$money_feiyong=$money_row['money_feiyong'];//��ֵ����(����)
$city=$money_row['city'];
$row_money_zs=$shijichongzhi+$money_feiyong;//�۳�����ʣ�µ�Ǯ
//������־��
$new_money=$user_money+$row_money_zs;
$edit_moneyl=array(
			'dq_money'=>$new_money,//�ӳ�ֵ�Ľ��
	        'dq_money_dj'=>$user_money_dongjie,
	        'dq_jifen'=>$duihuanjifen,
	        'dq_jifen_dj'=>$dongjiejifen,																	
    );
	$this->my_moneylog_mod->edit('id='.$log_id,$edit_moneyl);

//���moneylog��ֵ�����־
	
//$beizhu =$user_name.Lang::get('chongzhijine').$shijichongzhi.Lang::get('yuan');
$addlog=array(
	'money'=>'+'.$shijichongzhi,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_id,
	'zcity'=>$city,
	'type'=>1,
	's_and_z'=>1,
	'beizhu'=>$beizhu,
	'dq_money'=>$new_money,//�ӳ�ֵ�Ľ��
	'dq_money_dj'=>$user_money_dongjie,
	'dq_jifen'=>$duihuanjifen,
	'dq_jifen_dj'=>$dongjiejifen,	
);
 $this->moneylog_mod->add($addlog);
//���moneylog��ֵ������־
//$beizhu =Lang::get('kouchu').$user_name.Lang::get('chongzhifeiyong').abs($money_feiyong).Lang::get('yuan');
if($money_feiyong!=0)
{
$addlog1=array(
	'money'=>$money_feiyong,//����
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_id,
	'zcity'=>$city,
	'type'=>2,
	's_and_z'=>2,
	'beizhu'=>$beizhu,
	'dq_money'=>$new_money,//�ӳ�ֵ�Ľ��
	'dq_money_dj'=>$user_money_dongjie,
	'dq_jifen'=>$duihuanjifen,
	'dq_jifen_dj'=>$dongjiejifen,	
);
 $this->moneylog_mod->add($addlog1);		
}



//���²�����

	$yu_jinbi=$jinbi_row['yu_jinbi'];
	$zong_jinbi=$jinbi_row['zong_jinbi'];
	$chongzhifeilv=$jinbi_row['chongzhifeilv'];
	$zong_money=$jinbi_row['zong_money'];
	$zong_jifen=$jinbi_row['zong_jifen'];
    $can_id=1;
	$new_yu_jinbi=$yu_jinbi-$shijichongzhi;//�ӱҿ����ȥ��ֵ���
	//$new_zong_money=$zong_money+$shijichongzhi;//����ֵ������ӵ����˻��ʽ�(��������)
	$new_zong_money=$zong_money+abs($money_feiyong);//����ֵ�������ӵ����˻��ʽ�
	$edit_canshu=array(
	'yu_jinbi'=>$new_yu_jinbi,
	'zong_money'=>$new_zong_money,
	);
	$this->canshu_mod->edit('id='.$can_id,$edit_canshu);

//��ӱҿ���־
//$beizhu =Lang::get('bikulijianqu').$shijichongzhi.Lang::get('yuan');
$addbiku=array(
	'money'=>'-'.$shijichongzhi,
	'riqi'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_id,
	'biku_city'=>$city,
	'type'=>26,
	's_and_z'=>2,
	'beizhu'=>$beizhu,
	'dq_jinbi'=>$zong_jinbi,
	'dq_yujinbi'=>$new_yu_jinbi,
	
);
 $this->bikulog_mod->add($addbiku);


//�����û�money��
$new_money_zs=$row_money_zs+$user_money;
	$new_money=array(
			'money'=>$new_money_zs																	
    );
	$this->my_money_mod->edit('user_id='.$user_id,$new_money);//��ȡ�������ݿ�
	
//��˳ɹ�����ֵ������ӵ�accountlog��
	
/*$beizhu =Lang::get('shouqule').$user_name.Lang::get('chongzhijine').$row_money_zs.Lang::get('yuan');
$addaccount=array(
	'money'=>'+'.$row_money_zs,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_id,
	'zcity'=>$city,
	'type'=>11,
	's_and_z'=>1,
	'beizhu'=>$beizhu,
	'dq_money'=>$new_zong_money,
	'dq_jifen'=>$zong_jifen,
);
 $this->accountlog_mod->add($addaccount);*/
//��˳ɹ�����ֵ�������ӵ�accountlog��
	
//$beizhu =Lang::get('shouqule').$user_name.Lang::get('chongzhifeiyong').abs($money_feiyong).Lang::get('yuan');
if($money_feiyong!=0)
{
$addaccount1=array(
	'money'=>abs($money_feiyong),
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_id,
	'zcity'=>$city,
	'type'=>2,
	's_and_z'=>1,
	'beizhu'=>$beizhu,
	'dq_money'=>$new_zong_money,
	'dq_jifen'=>$zong_jifen,
);
 $this->accountlog_mod->add($addaccount1);		
}
	
}
  $this->show_message('shenhechenggong',
   /* 'caozuochenggong', 'index.php?module=my_money&act=cz_shenhe_user&user_id='.$user_id.'&log_id='.$log_id,*/
    'fanhuiliebiao',    'index.php?module=my_money&act=cz_yi_shenhe');
	}
	else
	{
	if(empty($log_id) or empty($user_id))
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	    $logs_data=$this->my_moneylog_mod->find('id='.$log_id);
	    $user_data=$this->my_money_mod->find('user_id='.$user_id);
		$this->assign('log', $logs_data);
		$this->assign('user', $user_data);
        $this->display('cz_shenhe_user.html');
	    return;
	}
	}

function cz_yi_shenhe()//caozuo=61
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	if(!empty($so_user_name))
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
        $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. "leixing=30 and status!=0",
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. "leixing=30 and status!=0 and city='$city'",
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			 $city_row=array();
		$result=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }	
			
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('cz_yi_shenhe.html'); 
	   return;
	}


function duihuan_wei_shenhe()//caozuo=60
    {
	
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	if(!empty($so_user_name))
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
	
        $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'leixing=11 and status=0',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'leixing=11 and status=0 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			
			}
		foreach ($index as $mon=>$mone)
		{
		$index[$mon]['money']=abs($mone['money']);
		}
			
			 $city_row=array();
		$result=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }	
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('duihuan_wei_shenhe.html'); 
	   return;
	}

function duihuan_shenhe_user()
    {
	$log_id=$_GET['log_id'];
	$user_id=$_GET['user_id'];
	$user_name=$_GET['user_name'];
	$zmoney=trim($_POST['money_zs']);
	$status=trim($_POST['status']);
	$log_text=trim($_POST['log_text']);
	$riqi=date('Y-m-d H:i:s');
	$user_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");	
    $user_money=$user_row['money'];//�û���ǰ��Ǯ
	$user_money_dj=$user_row['money_dj'];//�û���ǰ�Ķ�����
	$user_duihuanjifen=$user_row['duihuanjifen'];//�û���ǰ�Ļ���
	$user_dongjiejifen=$user_row['dongjiejifen'];//�û���ǰ�Ķ������
	
	$canshu_row=$this->canshu_mod->getRow("select * from ".DB_PREFIX."canshu");
    $jifen_xianjin=$canshu_row['jifenxianjin'];
	$duihuanjifenfeilv=$canshu_row['duihuanjifenfeilv'];

	if($_POST)
	{
	$edit_moneylog=array(
			'status'=>$status,		
			'log_text'=>$log_text,																		
    );
	$this->my_moneylog_mod->edit('id='.$log_id,$edit_moneylog);
$money_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");
$row_money_zs=$money_row['money'];//Ҫ�һ��Ľ�Ǯ(����)

if($status=="1")//���ͨ��
{

$row_duihuanjifen=$money_row['duihuanjifen'];//�һ��Ļ���(û�п۳�����)
$zhesuan_duihuanjifen=abs($row_money_zs)*$jifen_xianjin;//�����֮��Ļ���
$duihuanjifen_feiyong=$zhesuan_duihuanjifen*$duihuanjifenfeilv;//�һ����ַ���(����)
$shiji_duihuanjifen=$row_duihuanjifen-$duihuanjifen_feiyong;//ʵ�ʶһ��Ļ���
$city=$money_row['city'];
/*if($row_money_dj<$money_djs)
{
		$this->show_warning('feifacanshu');
	    return;
}*/

$this->kaiguan_mod =& m('kaiguan');
    $row_kaiguan=$this->kaiguan_mod->getRow("select webservice from ".DB_PREFIX."kaiguan");
	$webservice=$row_kaiguan['webservice'];
	$this->member_mod =& m('member');
	$user_row=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");	
$new_duihuanjifen=$shiji_duihuanjifen+$user_duihuanjifen;
$new_money_dj=$user_money_dj+$row_money_zs;
//�����û��ʽ�
	$new_money=array(
			'money_dj'=>$new_money_dj,	
			'duihuanjifen'=>$new_duihuanjifen,	
																			
    );
	$this->my_money_mod->edit('user_id='.$user_id,$new_money);//��ȡ�������ݿ�
	
	//���²�����
$this->canshu_mod=& m('canshu');
	$jinbi_row=$this->canshu_mod->getRow("select zong_jifen,zong_money from ".DB_PREFIX."canshu");
	$zong_jifen=$jinbi_row['zong_jifen'];
	$zong_money=$jinbi_row['zong_money'];
    $can_id=1;
	
	$add_account=array(
	'money'=>abs($row_money_zs),
	'jifen'=>'-'.$shiji_duihuanjifen,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_id,
	'zcity'=>$city,
	'type'=>3,
	's_and_z'=>1,
	'beizhu'=>$beizhu,
	'dq_money'=>$zong_money+abs($row_money_zs),
	'dq_jifen'=>$zong_jifen-$shiji_duihuanjifen
);
 $this->accountlog_mod->add($add_account);
	
$this->canshu_mod->edit('id='.$can_id,array('zong_money'=>$zong_money+abs($row_money_zs),'zong_jifen'=>$zong_jifen-$shiji_duihuanjifen));	
	
	
	//���moneylog�һ�����õĻ���
//$beizhu =$user_name.Lang::get('duihuanle').abs($row_money_zs).Lang::get('yuan').Lang::get('huodejifen').$shiji_duihuanjifen.Lang::get('jifen').Lang::get('kouchujifenfeiyong').$duihuanjifen_feiyong.Lang::get('jifen');
$addmoneylog1=array(
	'jifen'=>'+'.$shiji_duihuanjifen,
	'money'=>$row_money_zs,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$user_id,
	'zcity'=>$city,
	'type'=>3,
	's_and_z'=>1,
	'beizhu'=>$beizhu,
	'dq_money'=>$user_money,
	'dq_money_dj'=>$new_money_dj,
	'dq_jifen'=>$new_duihuanjifen,
	'dq_jifen_dj'=>$user_dongjiejifen,
);
 $this->moneylog_mod->add($addmoneylog1);	


}
if($status==2)//��˲�ͨ��
{
	$new_user_money=$user_money+abs($row_money_zs);
	$new_user_moneydj=$user_money_dj-abs($row_money_zs);
	$da=array(
	'money'=>$new_user_money,	
	'money_dj'=>$new_user_moneydj,	
	);
	$this->my_money_mod->edit('user_id='.$user_id,$da);//��ȡ�������ݿ�
	
}

    $this->show_message('shenhechenggong',
   
    'fanhuiliebiao',    'index.php?module=my_money&act=duihuan_yi_shenhe');
	}
	else
	{
	if(empty($log_id) or empty($user_id))
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	    $logs_data=$this->my_moneylog_mod->find('id='.$log_id);
	    $user_data=$this->my_money_mod->find('user_id='.$user_id);
		foreach ($logs_data as $key=>$val)
		{
			$logs_data[$key]['money']=abs($val['money']);
		}
		$this->assign('log', $logs_data);
		$this->assign('user', $user_data);
        $this->display('duihuan_shenhe_user.html');
	    return;
	}
	}

function duihuan_yi_shenhe()//caozuo=61
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	if(!empty($so_user_name))
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
	
	
        $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'leixing=11 and status!=0',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'leixing=11 and status!=0 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			foreach ($index as $mon=>$mone)
		{
		$index[$mon]['money']=abs($mone['money']);
		}
			 $city_row=array();
		$result=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }	
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('duihuan_yi_shenhe.html'); 
	   return;
	}


function duihuanxianjin_wei_shenhe()//caozuo=60
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];
$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	if(!empty($so_user_name))
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
	
	
        $page = $this->_get_page();	
		if($privs=="all")
		{
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'leixing=12 and status=0',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'leixing=12 and status=0 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
		$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];
			$index[$key]['duihuanjifen']=abs($val['duihuanjifen']);	
        }
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('duihuanxianjin_wei_shenhe.html'); 
	   return;
	}

function duihuanxianjin_shenhe_user()
    {
	$log_id=$_GET['log_id'];
	$user_id=$_GET['user_id'];
	$user_name=$_GET['user_name'];
	$duihuanjifen=trim($_POST['duihuanjifen']);
	$status=trim($_POST['status']);
	$log_text=trim($_POST['log_text']);
	$riqi=date('Y-m-d H:i:s');
	$user_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");	
    $user_money=$user_row['money'];
	$user_money_dj=$user_row['money_dj'];
	$user_duihuanjifen=$user_row['duihuanjifen'];
	$user_dongjiejifen=$user_row['dongjiejifen'];
	
	$canshu_row=$this->canshu_mod->getRow("select * from ".DB_PREFIX."canshu");
    $jifen_xianjin=$canshu_row['jifenxianjin'];
	$duihuanxianjinfeilv=$canshu_row['duihuanxianjinfeilv'];
	$lv31=$canshu_row['lv31'];
	
	if($_POST)
	{
		$res=$this->my_money_mod->getRow("select status from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");
		if($res['status']==0)
		{
			$edit_moneylog=array(
					'status'=>$status,		
					'log_text'=>$log_text,																		
			);
			$this->my_moneylog_mod->edit('id='.$log_id,$edit_moneylog);
			if($status==2)//��˲�ͨ��
			{
				$res=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");
				$duijifen=$res['duihuanjifen'];//����
				$newjifen=$user_duihuanjifen+abs($duijifen);
				$newdjjifen=$user_dongjiejifen-abs($duijifen);
				$edit_mon=array(
				'duihuanjifen'=>$newjifen,
				'dongjiejifen'=>$newdjjifen
				);
				$this->my_money_mod->edit('user_id='.$user_id,$edit_mon);
			}

			if($status==1)//���ͨ��
			{
			$money_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");
			$row_money_zs=$money_row['money'];//�һ��Ľ�Ǯ
			$row_duihuanjifen=$money_row['duihuanjifen'];//�һ��Ļ��֣��۳����õĻ��֣�������
			$city=$money_row['city'];
			$duihuanxianjin_feiyong=$money_row['jifen_feiyong'];// (���֣����� ����Ϊ0)
			$shiji_jifen=$row_duihuanjifen+$duihuanxianjin_feiyong;//ʵ��Ҫ�һ��Ļ��֣�������
			$shiji_jifen1=abs($shiji_jifen);
			$jifen31=abs($shiji_jifen)*$lv31;//�һ����ֵ�31%
			$yu_jifen=abs($shiji_jifen)-$jifen31;//��31%�Ļ��ֿ۳���ʵ�ʶһ��Ļ���
			$duihuan_money1=$yu_jifen/$jifen_xianjin;//�һ��Ľ��
			
			$duihuan_money=((int)($duihuan_money1*100))/100;
			
			$riqi=date('Y-m-d H:i:s');
		//�Խ�webservice��ʼ
			$this->kaiguan_mod =& m('kaiguan');
			$row_kaiguan=$this->kaiguan_mod->getRow("select webservice from ".DB_PREFIX."kaiguan");
			$webservice=$row_kaiguan['webservice'];
			$this->member_mod =& m('member');
			$user_row=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");	
			$web_id=$user_row['web_id'];
			$city=$user_row['city']; 
		   if($webservice=="yes")
		   {
			   $post_data = array(
					"ID"=>$web_id,
					"Money"=>format_price($shiji_jifen1/$jifen_xianjin),
					"MoneyType"=>2,
					"Count"=>1
				); 
				$web_id= webService('C_Consume',$post_data);
				$_S['canshu']=$this->member_mod->can();
						$web_list=array(
						'cai_id'=>0,
						'gong_id'=>$user_id,
						'time'=>$riqi,
						'type'=>2,
						'status'=>0,
						'money'=>format_price($shiji_jifen1/$jifen_xianjin),
						"consume_id"=>$web_id,
						'city'=>$city
						);
					$this->webservicelist_mod->add($web_list);	
			}	
	//���webservice_list
	//�Խ�webservice����	
		//�����û��ʽ��
		$new_dongjiejifen=$user_dongjiejifen-abs($shiji_jifen);
		$new_money_zs=$user_money+$duihuan_money;
			$new_money=array(
					'money'=>$new_money_zs,	
					'dongjiejifen'=>$new_dongjiejifen,																		
			);
			$this->my_money_mod->edit('user_id='.$user_id,$new_money);//��ȡ�������ݿ�
	//���²�����
			$this->canshu_mod=& m('canshu');
			$jinbi_row=$this->canshu_mod->getRow("select zong_jifen,zong_money from ".DB_PREFIX."canshu limit 1");
			$zong_jifen=$jinbi_row['zong_jifen'];
			$zong_money=$jinbi_row['zong_money'];
			$can_id=1;
			$new_zong_jifen=$zong_jifen+$jifen31;//���һ����ַ������ӵ����˻��Ļ���
			/*$edit_canshu=array(
			'zong_jifen'=>$new_zong_jifen,
			);*/
			$edit_canshu=array(
			'zong_jifen'=>$zong_jifen+$shiji_jifen1,
			'zong_money'=>$zong_money-$duihuan_money1
			);
			$this->canshu_mod->edit('id='.$can_id,$edit_canshu);	
			//���accountlog��־���һ����ֵ�31%
	
			//$beizhu =Lang::get('shouqule').$user_name.Lang::get('duihuanxianjinfeiyong').$jifen31.Lang::get('jifen');
			$add_account=array(
				//'jifen'=>$jifen31,
				'money'=>'-'.$duihuan_money1,
				'jifen'=>$shiji_jifen1,
				'time'=>$riqi,
				'user_name'=>$user_name,
				'user_id'=>$user_id,
				'zcity'=>$city,
				'type'=>4,
				's_and_z'=>1,
				'beizhu'=>$beizhu,
				//'dq_money'=>$zong_money,
				//'dq_jifen'=>$new_zong_jifen,
				'dq_money'=>$zong_money-$duihuan_money1,
				'dq_jifen'=>$zong_jifen+$shiji_jifen1
			);
			 $this->accountlog_mod->add($add_account);		


			//���moneylog�һ��ֽ����
			/*$beizhu =$user_name.Lang::get('duihuanle').abs($shiji_jifen).Lang::get('jifen').Lang::get('huodexianjin').$row_money_zs.Lang::get('yuan').Lang::get('kouchuxianjinfeiyong').abs($duihuanxianjin_feiyong).Lang::get('jifen');*/
			/*$beizhu =$user_name.Lang::get('duihuanle').abs($shiji_jifen).Lang::get('jifen').Lang::get('huodexianjin').$row_money_zs.Lang::get('yuan');
			$addmoneylog=array(
				'jifen'=>$duihuanxianjin_feiyong,
				'time'=>$riqi,
				'user_name'=>$user_name,
				'user_id'=>$user_id,
				'zcity'=>$city,
				'type'=>10,
				's_and_z'=>2,
				'beizhu'=>$beizhu,
				'dq_money'=>$new_money_zs,
				'dq_money_dj'=>$user_money_dj,
				'dq_jifen'=>$user_duihuanjifen,
				'dq_jifen_dj'=>$new_dongjiejifen,
			);
			 $this->moneylog_mod->add($addmoneylog);	*/


			/*$beizhu =$user_name.Lang::get('duihuanle').abs($shiji_jifen).Lang::get('jifen').Lang::get('huodexianjin').$row_money_zs.Lang::get('yuan').Lang::get('kouchuxianjinfeiyong').abs($duihuanxianjin_feiyong).Lang::get('jifen');*/
			
			//���moneylog�һ��ֽ��õĽ��
			//$beizhu =$user_name.Lang::get('duihuanle').abs($shiji_jifen).Lang::get('jifen').Lang::get('huodexianjin').$duihuan_money.Lang::get('yuan');
			$addmoneylog1=array(
				'money'=>'+'.$duihuan_money,
				'jifen'=>$shiji_jifen,
				'time'=>$riqi,
				'user_name'=>$user_name,
				'user_id'=>$user_id,
				'zcity'=>$city,
				'type'=>4,
				's_and_z'=>1,
				'beizhu'=>$beizhu,
				'dq_money'=>$new_money_zs,
				'dq_money_dj'=>$user_money_dj,
				'dq_jifen'=>$user_duihuanjifen,
				'dq_jifen_dj'=>$new_dongjiejifen,
			);
 			$this->moneylog_mod->add($addmoneylog1);	
		}
	}
    $this->show_message('shenhechenggong',
   /* 'caozuochenggong',  'index.php?module=my_money&act=duihuanxianjin_yi_shenhe',*/
    'fanhuiliebiao',    'index.php?module=my_money&act=duihuanxianjin_yi_shenhe');
	}
	else
	{
	if(empty($log_id) or empty($user_id))
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	    $logs_data=$this->my_moneylog_mod->find('id='.$log_id);
	    $user_data=$this->my_money_mod->find('user_id='.$user_id);
		foreach($logs_data as $key=>$val)
		{
		$logs_data[$key]['duihuanjifen']=abs($val['duihuanjifen']);
		}
		
		
		$this->assign('log', $logs_data);
		$this->assign('user', $user_data);
        $this->display('duihuanxianjin_shenhe_user.html');
	    return;
	}
	}
	
	
	function duihuanxianjin_yi_shenhe()//caozuo=61
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	if(!empty($so_user_name))
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
	
	
	
        $page = $this->_get_page();	
		if($privs==all)
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'leixing=12 and status!=0',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. 'leixing=12 and status!=0 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
			$index[$key]['duihuanjifen'] = abs($val['duihuanjifen']);
        }
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('duihuanxianjin_yi_shenhe.html'); 
	   return;
	}

 	function tx_index_shenhe()
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row',$priv_row);
        $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' => 'leixing=40',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' => 'leixing=40 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			
			}
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
			$index[$key]['money_feiyong'] = abs($val['money_feiyong']);			
        }
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('tx_index_shenhe.html'); 
	   return;
	}
//�鿴���� δ���	
	function tx_wei_shenhe()//caozuo=60
    {
	$user=$this->visitor->get('user_name'); 
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row',$priv_row);
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	$suoshuzhan=$_GET["suoshuzhan"];	
	$this->assign('so_user_name',$so_user_name);
	$this->assign('sotime',$sotime);
	$this->assign('endtime',$endtime);
	$this->assign('suoshuzhan',$suoshuzhan);
	$conditions="1=1";
	
	if(!empty($so_user_name))
	{
		$conditions.=" and user_name like '%$so_user_name%'";
	}
	if(!empty($sotime))
	{
		$conditions.=" and riqi>='$sotime'";
	}
	if(!empty($endtime))
	{
		$conditions.=" and riqi<='$endtime 24:59:59'";
	}
	if(!empty($suoshuzhan))
	{ 
		$conditions.=" and city='$suoshuzhan'";
	}

        $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. ' and leixing=40 and caozuo=60 and status1=1',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
		}
		else
		{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. ' and leixing=40 and caozuo=60 and status1=1 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			
		}
			
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
			$index[$key]['money']=abs($val['money']);
			$index[$key]['money_feiyong']=abs($val['money_feiyong']);	
        }
		
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('tx_wei_shenhe.html'); 
	   return;
	}
//�鿴���� �����
	function tx_yi_shenhe()//caozuo=61
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row',$priv_row);
        $page = $this->_get_page();	
		if($privs=="all")
		{		
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' => 'leixing=40 and status1!=1 and (caozuo=60 or caozuo=61)',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
		else
		{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' => 'leixing=40 and (caozuo=60 or caozuo=61) and status1!=1 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			
		}
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
			$index[$key]['money']=abs($val['money']);
			$index[$key]['money_feiyong']=abs($val['money_feiyong']);		
        }
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('tx_yi_shenhe.html'); 
	   return;
	}
//��������
	function tx_soso()
    {
	$soname=$_GET["soname"];
	$sotime=$_GET["sotime"];
	$endtime=$_GET["endtime"];
	$state=$_GET["state"];
	$suoshuzhan=$_GET["suoshuzhan"];
	$conditions='1=1 and ';
		if(empty($soname) and empty($sotime) and empty($endtime) and empty($state) and empty($suoshuzhan))
        {
		$this->show_warning('user_money_add_nizongdeshurudianshenmeba');
	    return;
		}
		$page = $this->_get_page();	
	$this->assign('soname',$soname);
	$this->assign('sotime',$sotime);	
	$this->assign('endtime',$endtime);	
	$this->assign('state',$state);	
	$this->assign('suoshuzhan',$suoshuzhan);		
	if(!empty($soname))//���û�����Ϊ��
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions .=" user_name like '%$soname%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions .=" user_name like '%$soname%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions .=" user_name like '%$soname%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions .=" user_name like '%$soname%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions .=" riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions .=" riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions .=" riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
	if($state==1)
	{
		$conditions .=" status=1 and ";
	}
	if($state==2)
	{
		$conditions .=" status1=2 and (status is null or status=2) and ";
	}

$conditions .=" city='$suoshuzhan' and ";
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];	
		
		if($privs=="all")
		{
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. "leixing=40",
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
     
		}
		else
		{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. "leixing=40 and city='$city'",
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
		}
	   
		$city_row=array();
		$result=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
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
			$index[$key]['money_feiyong'] = abs($val['money_feiyong']);	
        }		
		
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('tx_index_shenhe.html'); 
	   return;
	}
	
	
//�ܱ�����
	function mb_soso()//Ĭ����ʾ
    {
	$sombsn=$_GET["sombsn"];
	$sotime=$_GET["sotime"];
	$endtime=$_GET["endtime"];
	$ztai=$_GET["ztai"];

		$sotimes= strtotime("$sotime");
		$endtimes= strtotime("$endtime")+86399;// ����23Сʱ59��59��
		if(empty($sombsn) and empty($sotime) and empty($endtime))
        {
		$this->show_warning('user_money_add_nizongdeshurudianshenmeba');
	    return;
		}		
		$page = $this->_get_page();	
		//��������Ϊ��
        if (isset($sombsn) and isset($sotime) and isset($endtime)) 
        { 
		$index=$this->my_mibao_mod->find(array(
	    'conditions' => "mibao_sn LIKE '%$sombsn%' and bd_time>='$sotimes' and bd_time<'$endtimes' and ztai='$ztai'",
        'limit' => $page['limit'],
		'count' => true));
        }
	   	//��ʼʱ�� �� ����ʱ�� Ϊ�գ��������������û�			
	    if(empty($sotime) or empty($endtime))
        {
		if(empty($sotimes))  $sotimes=1;
	    if(empty($endtimes)) $endtimes=1300000000;	
		$index=$this->my_mibao_mod->find(array(
	        'conditions' => "bd_time>='$sotimes' and bd_time<'$endtimes' and ztai='$ztai'",
            'limit' => $page['limit'],
			'count' => true));
		}
		//�û�Ϊ�գ�����������ʼʱ��-����ʱ��
	    else
        {
		$index=$this->my_mibao_mod->find(array(
	        'conditions' => "mibao_sn LIKE '%$sombsn%' and ztai='$ztai'",
            'limit' => $page['limit'],
			'count' => true));
		}	

		$page['item_count'] = $this->my_moneylog_mod->getCount('user_name='.$soname);
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);
		if(empty($ztai)) $this->display('mibao_xinka.html'); 
		if($ztai==1)$this->display('mibao_zhengchang.html'); 
		if($ztai==2)$this->display('mibao_zanting.html'); 
		if($ztai==3)$this->display('mibao_guoqi.html'); 
	    return;

	}
//����л�caozuo 60ת61
 	function caozuo_yes()
    {
	$id=$_GET['id'];
	$caozuo=$_GET['caozuo'];
	if($caozuo<>60)
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	if(empty($id))
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	$this->my_moneylog_mod->edit('id='.$id,'caozuo=61');
	$lailu =$_SERVER['HTTP_REFERER'];	
header("Location: $lailu");
	   return;
	}
//����л�caozuo 61ת60	
 	function caozuo_no()
    {
	$id=$_GET['id'];
	$caozuo=$_GET['caozuo'];
	if($caozuo<>61)
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	if(empty($id))
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	$this->my_moneylog_mod->edit('id='.$id,'caozuo=60');	
	$lailu =$_SERVER['HTTP_REFERER'];	
header("Location: $lailu");
	   return;
	}
//��˲��� �༭LOGS���۳���Ӧ���ֽ��	
function tx_shenhe_user()
    {
	$log_id=$_GET['log_id'];
	$user_id=$_GET['user_id'];
	$order_id=trim($_POST['order_id']);
	$log_text=trim($_POST['log_text']);
	$money_djs=trim($_POST['money_djs']);
	$money_chu=trim($_POST['money_chu']);
	$status1=trim($_POST['status1']);
	$admin_time = time();
	$riqi=date('Y-m-d H:i:s');
	$jujue=trim($_POST['beizhu']);

$user_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");	
    $user_name=$user_row['user_name'];
	$user_money=$user_row['money'];
	$user_money_dongjie=$user_row['money_dj'];
	$duihuanjifen=$user_row['duihuanjifen'];
	$dongjiejifen=$user_row['dongjiejifen'];
	$city=$user_row['city'];


	if($_POST)
	{
		$money_row=$this->my_money_mod->getrow("select * from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");
		$moneydj=$money_row['money_dj'];
		
		if($money_row['status']!='' ||  $money_row['status1']!=1)
            {
                echo iconv('utf-8','gb2312','�����ظ�������');
                echo " <a href='javascript:history.go(-1);'>".iconv('utf-8','gb2312','����')."</a>";
                return;
            }
		
		if($status1==3)//����˲�ͨ������ⶳ���ֽ��
		{
			$new_user_money=$user_money+$moneydj;
			$new_user_money_dongjie=$user_money_dongjie-$moneydj;
			$edit_money=array(
			'money'=>$new_user_money,
			'money_dj'=>$new_user_money_dongjie
			);
			$beizhu =Lang::get('jiedongdongjiejine');
			$beizhu=str_replace('{1}',$user_name,$beizhu);
			$beizhu=str_replace('{2}',$moneydj,$beizhu);
			
		  $addlog=array(
			  'money_dj'=>'-'.$moneydj,//����
			  'money'=>$moneydj,
			  'time'=>$riqi,
			  'user_name'=>$user_name,
			  'user_id'=>$user_id,
			  'zcity'=>$city,
			  'type'=>35,
			  's_and_z'=>1,
			  'beizhu'=>$beizhu,
			  'dq_money'=>$new_user_money,//�۳����ֵĽ��
			  'dq_money_dj'=>$new_user_money_dongjie,
			  'dq_jifen'=>$duihuanjifen,
			  'dq_jifen_dj'=>$dongjiejifen,	
		  );
		   //$this->moneylog_mod->add($addlog);
		   $this->my_money_mod->edit('user_id='.$user_id,$edit_money);
		   
		$this->message_mod=& m('message');
		$yuan=Lang::get('tixianbu');
		$yuan=str_replace('{1}',$jujue,$yuan);
		$adda1=array(
			'from_id'=>0,
			'to_id'=>$user_id,
			'title'=>$title,
			'content'=>$yuan,
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3,
		);
		 $this->message_mod->add($adda1);	
		   
		   
		}
		
	$edit_moneylog=array(
			'status1'=>$status1,		
			'log_text'=>$log_text,																	
    );
	$this->my_moneylog_mod->edit('id='.$log_id,$edit_moneylog);

	
    $this->show_message('shenhechenggong',
    'fanhuiliebiao',    'index.php?module=my_money&act=tx_yi_shenhe');
	}
	else
	{
	if(empty($log_id) or empty($user_id))
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	    $logs_data=$this->my_moneylog_mod->find('id='.$log_id);
	foreach ($logs_data as $key=>$val)
	{
		$logs_data[$key]['money']=abs($val['money']);
		$logs_data[$key]['money_feiyong']=abs($val['money_feiyong']);
	}
		
	    $user_data=$this->my_money_mod->find('user_id='.$user_id);
		$this->assign('log', $logs_data);
		$this->assign('user', $user_data);
        $this->display('tx_shenhe_user.html');
	    return;
	}
	}
//�鿴�û�������ת�롢��ֵ
	function logs_user_shouru()
    {	
	$user_name=$_GET["user_name"];
	$sotime=$_GET["sotime"];
	$endtime=$_GET["endtime"];
	
	if (!empty($sotime) or !empty($endtime)) 
    {
	$soso="xiaohei"; 
	}
	
	if(empty($user_name))
    {
		$this->show_warning('feifacanshu');
	    return;
	}
        $page = $this->_get_page();	
	   
	   
	    if (isset($user_name) and isset($sotime) and isset($endtime)) 
        { 
		$sotimes= strtotime("$sotime");
		$endtimes= strtotime("$endtime")+86399;// 86399����23Сʱ59��59����
	    $index=$this->my_moneylog_mod->find(array(
	    'conditions' => "user_name='$user_name' and add_time>='$sotimes' and add_time<'$endtimes' and s_and_z=1",
        'limit' => $page['limit'],
	    'order' => "money desc",
	    'count' => true));
        }   
	   
        if(empty($sotime) or empty($endtime))
        {
		$sotimes= strtotime("$sotime");
		$endtimes= strtotime("$endtime");
		if(empty($sotimes))  $sotimes=1;
	    if(empty($endtimes)) $endtimes=1300000000;	
	    $index=$this->my_moneylog_mod->find(array(
	    'conditions' => "user_name='$user_name' and add_time>='$sotimes' and add_time<'$endtimes' and s_and_z=1",
        'limit' => $page['limit'],
	    'order' => "money desc",
	    'count' => true));
		}

/*
	   $index=$this->my_moneylog_mod->find(array(
	   'conditions' => "user_name='$user_name' and s_and_z=1",
       'limit' => $page['limit'],
	   'order' => "money_zs desc",
	   'count' => true));
*/
	   $page['item_count'] = $this->my_moneylog_mod->getCount();
       $this->_format_page($page);
	   $this->assign('page_info', $page);
	   $this->assign('soso', $soso);
	   $this->assign('index', $index);
       $this->display('logs_user_shouru.html'); 
	   return;
    }


//�������� �ܱ���	
function mibao_sn_pi()
{
$snprefix=$_GET['snprefix'];
$ctype=$_GET['ctype'];
$mnum=$_GET['mnum'];
$pwdgr=$_GET['pwdgr'];
$pwdlen=$_GET['pwdlen'];

if(!empty($mnum))//����Ƿ��ύ
{
$begin=microtime(true);//��ѯ ��ʱ��ʼ

$sql="select id from ".DB_PREFIX."my_mibao order by id desc LIMIT 1";
$res=mysql_query($sql);
$date=mysql_fetch_assoc($res);  
$mibaoid=$date['id'];
		if(empty($mibaoid))
		{
		$mibaoid=0;
        }
		$startid=$mibaoid+1001;
	
		$startid=$startid++;
	    $endid = $startid+$mnum;
		$add_time = time();

	for(;$startid<$endid;$startid++)
	{
		$cardid = $snprefix.$startid.'-';
		for($p=0;$p<$pwdgr;$p++)
		{
			for($i=0; $i < $pwdlen; $i++)
			{
				if($ctype==1)
				{
					$c = mt_rand(49,57); 
					$c = chr($c);
				}
				else
				{
					$c = mt_rand(65,90);
					if($c==79)//=O
					{
					$c = 'M';
					}
					else
					{
					$c = chr($c);
					}
				
				}
				$cardid .= $c;
			}
			if($p<$pwdgr-1)
			{
				$cardid .= '-';
			}
		}
			$mibao_sn_add=array(
			'mibao_sn'   =>$cardid,
			'add_time' =>$add_time,	
			'admin_name' =>$this->visitor->get('user_name'),
  'A1' => rand(100,999),
  'B1' => rand(100,999),
  'C1' => rand(100,999),
  'D1' => rand(100,999),
  'E1' => rand(100,999),
  'F1' => rand(100,999),
  'G1' => rand(100,999),
  'H1' => rand(100,999),
  
  'A2' => rand(100,999),
  'B2' => rand(100,999),
  'C2' => rand(100,999),
  'D2' => rand(100,999),
  'E2' => rand(100,999),
  'F2' => rand(100,999),
  'G2' => rand(100,999),
  'H2' => rand(100,999),
  
  'A3' => rand(100,999),
  'B3' => rand(100,999),
  'C3' => rand(100,999),
  'D3' => rand(100,999),
  'E3' => rand(100,999),
  'F3' => rand(100,999),
  'G3' => rand(100,999),
  'H3' => rand(100,999),
  
  'A4' => rand(100,999),
  'B4' => rand(100,999),
  'C4' => rand(100,999),
  'D4' => rand(100,999),
  'E4' => rand(100,999),
  'F4' => rand(100,999),
  'G4' => rand(100,999),
  'H4' => rand(100,999),
  
  'A5' => rand(100,999),
  'B5' => rand(100,999),
  'C5' => rand(100,999),
  'D5' => rand(100,999),
  'E5' => rand(100,999),
  'F5' => rand(100,999),
  'G5' => rand(100,999),
  'H5' => rand(100,999),
  
  'A6' => rand(100,999),
  'B6' => rand(100,999),
  'C6' => rand(100,999),
  'D6' => rand(100,999),
  'E6' => rand(100,999),
  'F6' => rand(100,999),
  'G6' => rand(100,999),
  'H6' => rand(100,999),
  
  'A7' => rand(100,999),
  'B7' => rand(100,999),
  'C7' => rand(100,999),
  'D7' => rand(100,999),
  'E7' => rand(100,999),
  'F7' => rand(100,999),
  'G7' => rand(100,999),
  'H7' => rand(100,999),
  
  'A8' => rand(100,999),
  'B8' => rand(100,999),
  'C8' => rand(100,999),
  'D8' => rand(100,999),
  'E8' => rand(100,999),
  'F8' => rand(100,999),
  'G8' => rand(100,999),
  'H8' => rand(100,999),
  
  'A9' => rand(100,999),
  'B9' => rand(100,999),
  'C9' => rand(100,999),
  'D9' => rand(100,999),
  'E9' => rand(100,999),
  'F9' => rand(100,999),
  'G9' => rand(100,999),
  'H9' => rand(100,999),																				
  );
    	$this->my_mibao_mod->add($mibao_sn_add);
		echo Lang::get('chenggongshengchengdongtaimibaoka').$cardid."<br/>";
		
				echo "&nbsp;&nbsp;&nbsp;A&nbsp;&nbsp;&nbsp;";
				echo "B&nbsp;&nbsp;&nbsp;";
				echo "C&nbsp;&nbsp;&nbsp;";
				echo "D&nbsp;&nbsp;&nbsp;";
				echo "E&nbsp;&nbsp;&nbsp;";
				echo "F&nbsp;&nbsp;&nbsp;";
				echo "G&nbsp;&nbsp;&nbsp;";
				echo "H     ";
				echo "<br>";
				
				echo "1:".$mibao_sn_add['A1'];echo " ";
				echo $mibao_sn_add['B1'];echo " ";
				echo $mibao_sn_add['C1'];echo " ";
				echo $mibao_sn_add['D1'];echo " ";
				echo $mibao_sn_add['E1'];echo " ";
				echo $mibao_sn_add['F1'];echo " ";
				echo $mibao_sn_add['G1'];echo " ";
				echo $mibao_sn_add['H1'];echo "<br>";
			
				echo "2:".$mibao_sn_add['A2'];echo " ";
				echo $mibao_sn_add['B2'];echo " ";
				echo $mibao_sn_add['C2'];echo " ";
				echo $mibao_sn_add['D2'];echo " ";
				echo $mibao_sn_add['E2'];echo " ";
				echo $mibao_sn_add['F2'];echo " ";
				echo $mibao_sn_add['G2'];echo " ";
				echo $mibao_sn_add['H2'];echo "<br>";
			

				echo "3:".$mibao_sn_add['A3'];echo " ";
				echo $mibao_sn_add['B3'];echo " ";
				echo $mibao_sn_add['C3'];echo " ";
				echo $mibao_sn_add['D3'];echo " ";
				echo $mibao_sn_add['E3'];echo " ";
				echo $mibao_sn_add['F3'];echo " ";
				echo $mibao_sn_add['G3'];echo " ";
				echo $mibao_sn_add['H3'];echo "<br>";
				
				echo "4:".$mibao_sn_add['A4'];echo " ";
				echo $mibao_sn_add['B4'];echo " ";
				echo $mibao_sn_add['C4'];echo " ";
				echo $mibao_sn_add['D4'];echo " ";
				echo $mibao_sn_add['E4'];echo " ";
				echo $mibao_sn_add['F4'];echo " ";
				echo $mibao_sn_add['G4'];echo " ";
				echo $mibao_sn_add['H4'];echo "<br>";
				
				echo "5:".$mibao_sn_add['A5'];echo " ";
				echo $mibao_sn_add['B5'];echo " ";
				echo $mibao_sn_add['C5'];echo " ";
				echo $mibao_sn_add['D5'];echo " ";
				echo $mibao_sn_add['E5'];echo " ";
				echo $mibao_sn_add['F5'];echo " ";
				echo $mibao_sn_add['G5'];echo " ";
				echo $mibao_sn_add['H5'];echo "<br>";
				
				echo "6:".$mibao_sn_add['A6'];echo " ";
				echo $mibao_sn_add['B6'];echo " ";
				echo $mibao_sn_add['C6'];echo " ";
				echo $mibao_sn_add['D6'];echo " ";
				echo $mibao_sn_add['E6'];echo " ";
				echo $mibao_sn_add['F6'];echo " ";
				echo $mibao_sn_add['G6'];echo " ";
				echo $mibao_sn_add['H6'];echo "<br>";
			
				echo "7:".$mibao_sn_add['A7'];echo " ";
				echo $mibao_sn_add['B7'];echo " ";
				echo $mibao_sn_add['C7'];echo " ";
				echo $mibao_sn_add['D7'];echo " ";
				echo $mibao_sn_add['E7'];echo " ";
				echo $mibao_sn_add['F7'];echo " ";
				echo $mibao_sn_add['G7'];echo " ";
				echo $mibao_sn_add['H7'];echo "<br>";
			
				echo "8:".$mibao_sn_add['A8'];echo " ";
				echo $mibao_sn_add['B8'];echo " ";
				echo $mibao_sn_add['C8'];echo " ";
				echo $mibao_sn_add['D8'];echo " ";
				echo $mibao_sn_add['E8'];echo " ";
				echo $mibao_sn_add['F8'];echo " ";
				echo $mibao_sn_add['G8'];echo " ";
				echo $mibao_sn_add['H8'];echo "<br>";
				
				echo "9:".$mibao_sn_add['A9'];echo " ";
				echo $mibao_sn_add['B9'];echo " ";
				echo $mibao_sn_add['C9'];echo " ";
				echo $mibao_sn_add['D9'];echo " ";
				echo $mibao_sn_add['E9'];echo " ";
				echo $mibao_sn_add['F9'];echo " ";
				echo $mibao_sn_add['G9'];echo " ";
				echo $mibao_sn_add['H9'];echo "<br>";
				
				echo "<br>";

		
	}

$stop=microtime(true); //��ȡ����ִ�н�����ʱ��
$runtime=round(($stop-$begin),4);


	echo Lang::get('bencigongchenggongshengcheng').$mnum.Lang::get('shengcheng_zhang')."&nbsp;&nbsp;&nbsp;&nbsp;��ʱ".$runtime."&nbsp;s<br/><br/>";
	echo Lang::get('shengcheng_xitong')."<br/>";	
	echo Lang::get('shengcheng_byxiaohei');








}
else
{
		$this->display('mibao_shengcheng.index.html'); 
	    return;	
}
}


//�������� ��ֵ��	
function card_add_pi()
{
$snprefix=$_GET['snprefix'];
$ctype=$_GET['ctype'];
$mnum=$_GET['mnum'];
$pwdgr=$_GET['pwdgr'];
$pwdlen=$_GET['pwdlen'];

$m_pwdgr=$_GET['m_pwdgr'];
$m_pwdlen=$_GET['m_pwdlen'];


$money=$_GET['money'];//����ֵ

$guoqi_times=$_GET['guoqi_time'];//����ʱ��
$guoqi_time= strtotime("$guoqi_times");//ת������ʱ���ʽ

if(!empty($mnum))//����Ƿ��ύ
{

$sql="select id from ".DB_PREFIX."my_card order by id desc LIMIT 1";
$res=mysql_query($sql);
$date=mysql_fetch_assoc($res);  
$ids=$date['id'];
		if(empty($ids))
		{
		$ids=0;
        }
		$startid=$ids+1001;

	
		$startid=$startid++;
	    $endid = $startid+$mnum;
		$add_time = time();

	for(;$startid<$endid;$startid++)
	{
	$card_pass=$startid;//�������ID 4λ����ʼ
	$cardid = $snprefix.$startid.'-';
		for($p=0;$p<$pwdgr;$p++)
		{
			for($i=0; $i < $pwdlen; $i++)
			{
				if($ctype==1)//ʹ������
				{
					$c = mt_rand(49,57); 
					$c = chr($c);
				}
				else//ʹ������
				{
					$c = mt_rand(65,90);
					if($c==79)//=O�ͻ���M
					{
					$c = 'M';
					}
					else
					{
					$c = chr($c);
					}
				
				}
				$cardid .= $c;
			}
			if($p<$pwdgr-1)//���һ�ּӡ�-��
			{
				$cardid .= '-';
			}
	
		}
		//���벿�ֿ�ʼ

			for($ii=0; $ii < $m_pwdlen; $ii++)
			{

					$cc = mt_rand(49,57); 
					$cc = chr($cc);


				$card_pass .= $cc;
			}



			$card_add=array(
			'card_sn' =>$cardid,
			'card_pass' =>$card_pass,
			'add_time' =>$add_time,
			'admin_name' =>$this->visitor->get('user_name'),
			'guoqi_time' =>$guoqi_time,
			'money' =>$money,
			);
    	$this->my_card_mod->add($card_add);
		echo "���ţ�".$cardid."         ���룺".$card_pass."         ��ֵ��".$money."Ԫ         ����ʱ�䣺".$guoqi_times."<br/>";
	}
	echo "<br/>���ι����ɳ�ֵ����:".$mnum.Lang::get('shengcheng_zhang')."<br/><br/>";
	echo Lang::get('shengcheng_xitong')."<br/>";	
	echo Lang::get('shengcheng_byxiaohei');		
}
else
{
		$this->display('card_shengcheng.index.html'); 
	    return;	
}
}

//�Ѱ��û����ܱ���1
function mibao_zhengchang()
    {
	$xz_time = time();
    $page = $this->_get_page();
	$index=$this->my_mibao_mod->find(array(
	        'conditions' => "dq_time>$xz_time and ztai=1",//���� �磺where
            'limit' => $page['limit'],
			'count' => true,
        ));	
	$page['item_count'] = $this->my_mibao_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);//���ݵ������
	$this->display('mibao_zhengchang.html'); 
	return;
	}
//δ���� ����ͣ2
function mibao_zanting()
    {
	$xz_time = time();
    $page = $this->_get_page();
	$index=$this->my_mibao_mod->find(array(
	        'conditions' =>"dq_time>$xz_time and ztai=2",//����ʹ����˫����
            'limit' => $page['limit'],
			'count' => true,
        ));	
	$page['item_count'] = $this->my_mibao_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('mibao_zanting.html'); 
	return;
	}
	
	
	
//�ѵ��� ����ͣ3
function mibao_guoqi()
    {
	$xz_time = time();
    $page = $this->_get_page();
	$index=$this->my_mibao_mod->find(array(
	        'conditions' => "dq_time<$xz_time and ztai=3",//����ʹ����˫����
            'limit' => $page['limit'],
			'count' => true,
        ));	
	$page['item_count'] = $this->my_mibao_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('mibao_guoqi.html'); 
	return;
	}	
//�ܱ��¿�0
function mibao_xinka()
    {
    $page = $this->_get_page();
	$index=$this->my_mibao_mod->find(array(
            'conditions' => 'user_id=0 and ztai=0' ,//���� �磺where
            'limit' => $page['limit'],
			'count' => true,
        ));	
	$page['item_count'] = $this->my_mibao_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('mibao_xinka.html'); 
	return;
	}
//�༭�Ѱ�mibao_edit_mibao
function mibao_edit_mibao()
{
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';

    if (!$id)
    {
            $this->show_warning('feifacanshu');
            return;
    }
	if($_POST)//����Ƿ��ύ
	{
	$time_edit = trim($_POST['time_edit']);
	$dq_time = trim($_POST['dq_time']);
	$yes_sn_edit = trim($_POST['yes_sn_edit']);
		
	if($time_edit=="YES")
	{
	$dq_time = strtotime("$dq_time");
	if($dq_time<time())
	{
	$ztai=3;
	}
	else
	{
	$ztai=1;
	}
	$mibao_time=array(
	'dq_time'=>$dq_time,
	'ztai'=>$ztai,
	);
	$this->my_mibao_mod->edit($id,$mibao_time);	
	}
	
	
	if($yes_sn_edit=="YES")
	{
  $A1= trim($_POST['A1']);
  $B1= trim($_POST['B1']);
  $C1= trim($_POST['C1']);
  $D1= trim($_POST['D1']);
  $E1= trim($_POST['E1']);
  $F1= trim($_POST['F1']);
  $G1= trim($_POST['G1']);
  $H1= trim($_POST['H1']);

  $A2= trim($_POST['A2']);
  $B2= trim($_POST['B2']);
  $C2= trim($_POST['C2']);
  $D2= trim($_POST['D2']);
  $E2= trim($_POST['E2']);
  $F2= trim($_POST['F2']);
  $G2= trim($_POST['G2']);
  $H2= trim($_POST['H2']);

  $A3= trim($_POST['A3']);
  $B3= trim($_POST['B3']);
  $C3= trim($_POST['C3']);
  $D3= trim($_POST['D3']);
  $E3= trim($_POST['E3']);
  $F3= trim($_POST['F3']);
  $G3= trim($_POST['G3']);
  $H3= trim($_POST['H3']);

  $A4= trim($_POST['A4']);
  $B4= trim($_POST['B4']);
  $C4= trim($_POST['C4']);
  $D4= trim($_POST['D4']);
  $E4= trim($_POST['E4']);
  $F4= trim($_POST['F4']);
  $G4= trim($_POST['G4']);
  $H4= trim($_POST['H4']);

  $A5= trim($_POST['A5']);
  $B5= trim($_POST['B5']);
  $C5= trim($_POST['C5']);
  $D5= trim($_POST['D5']);
  $E5= trim($_POST['E5']);
  $F5= trim($_POST['F5']);
  $G5= trim($_POST['G5']);
  $H5= trim($_POST['H5']);

  $A6= trim($_POST['A6']);
  $B6= trim($_POST['B6']);
  $C6= trim($_POST['C6']);
  $D6= trim($_POST['D6']);
  $E6= trim($_POST['E6']);
  $F6= trim($_POST['F6']);
  $G6= trim($_POST['G6']);
  $H6= trim($_POST['H6']);

  $A7= trim($_POST['A7']);
  $B7= trim($_POST['B7']);
  $C7= trim($_POST['C7']);
  $D7= trim($_POST['D7']);
  $E7= trim($_POST['E7']);
  $F7= trim($_POST['F7']);
  $G7= trim($_POST['G7']);
  $H7= trim($_POST['H7']);

  $A8= trim($_POST['A8']);
  $B8= trim($_POST['B8']);
  $C8= trim($_POST['C8']);
  $D8= trim($_POST['D8']);
  $E8= trim($_POST['E8']);
  $F8= trim($_POST['F8']);
  $G8= trim($_POST['G8']);
  $H8= trim($_POST['H8']);


  $A9= trim($_POST['A9']);
  $B9= trim($_POST['B9']);
  $C9= trim($_POST['C9']);
  $D9= trim($_POST['D9']);
  $E9= trim($_POST['E9']);
  $F9= trim($_POST['F9']);
  $G9= trim($_POST['G9']);
  $H9= trim($_POST['H9']);

  $mibao_shuzi=array(
  'A1'=>$A1,
  'B1'=>$B1,
  'C1'=>$C1,
  'D1'=>$D1,
  'E1'=>$E1,
  'F1'=>$F1,
  'G1'=>$G1,
  'H1'=>$H1,
  
  'A2'=>$A2,
  'B2'=>$B2,
  'C2'=>$C2,
  'D2'=>$D2,
  'E2'=>$E2,
  'F2'=>$F2,
  'G2'=>$G2,
  'H2'=>$H2,
  
  'A3'=>$A3,
  'B3'=>$B3,
  'C3'=>$C3,
  'D3'=>$D3,
  'E3'=>$E3,
  'F3'=>$F3,
  'G3'=>$G3,
  'H3'=>$H3,
  
  'A4'=>$A4,
  'B4'=>$B4,
  'C4'=>$C4,
  'D4'=>$D4,
  'E4'=>$E4,
  'F4'=>$F4,
  'G4'=>$G4,
  'H4'=>$H4,
  
  'A5'=>$A5,
  'B5'=>$B5,
  'C5'=>$C5,
  'D5'=>$D5,
  'E5'=>$E5,
  'F5'=>$F5,
  'G5'=>$G5,
  'H5'=>$H5,
  
  'A6'=>$A6,
  'B6'=>$B6,
  'C6'=>$C6,
  'D6'=>$D6,
  'E6'=>$E6,
  'F6'=>$F6,
  'G6'=>$G6,
  'H6'=>$H6,
  
  'A7'=>$A7,
  'B7'=>$B7,
  'C7'=>$C7,
  'D7'=>$D7,
  'E7'=>$E7,
  'F7'=>$F7,
  'G7'=>$G7,
  'H7'=>$H7,
  
  'A8'=>$A8,
  'B8'=>$B8,
  'C8'=>$C8,
  'D8'=>$D8,
  'E8'=>$E8,
  'F8'=>$F8,
  'G8'=>$G8,
  'H8'=>$H8,
  
  'A9'=>$A9,
  'B9'=>$B9,
  'C9'=>$C9,
  'D9'=>$D9,
  'E9'=>$E9,
  'F9'=>$F9,
  'G9'=>$G9,
  'H9'=>$H9,																
    );
	$this->my_mibao_mod->edit($id,$mibao_shuzi);
	}
	$this->show_message('mibao_edit_mibao_bianjichenggong',
    'mibao_edit_mibao_fanhumibaoliebiao','index.php?module=my_money&act=mibao_zhengchang'
    );
	return;
	}
    else
    {
    $index=$this->my_mibao_mod->find($id);//��ȡ�������ݿ�
    $this->assign('index', $index);//���ݵ������
    $this->display('mibao_edit_mibao.html'); 
    return;
    }	
}
//������ͣ	mibao_zantings
function mibao_zantings()
{
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
	$pi = isset($_GET['pi']) ? trim($_GET['pi']) : '';
    if (!$id)
    {
            $this->show_warning('feifacanshu');
            return;
    }
	$ztai=array(
	'ztai'=>2,
	);
    $ids = explode(',', $id);
    $this->my_mibao_mod->edit($ids,$ztai);
	if($pi=="pi")
	{
    $this->show_message('mibao_zantings_piliangcaozuozantingshiyong');
    }
	else
	{
    $this->show_message('mibao_zantings_genggaizantingshiyong');
	}
	return;
}
//�ָ�ʹ��mibao_huifu
function mibao_huifu()
{
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    if (!$id)
    {
            $this->show_warning('feifacanshu');
            return;
    }
	
$mibao_row=$this->my_mibao_mod->getRow("select dq_time from ".DB_PREFIX."my_mibao where id='$id' limit 1");
if($mibao_row['dq_time'] >time())
{
	$ztai=array(
	'ztai'=>1,
	);
}
else
{
	$ztai=array(
	'ztai'=>1,
	'dq_time'=>time()+31536000,
	);
}	
    $this->my_mibao_mod->edit($id,$ztai);
    $this->show_message('mibao_huifu_huifuzhengchangshiyong');
	return;
}
//����ɾ�����ܱ���mibao_drop_pi
function mibao_drop_pi()
{
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('feifacanshu');
            return;
        }
        
        $ids = explode(',', $id);
       
        $this->my_mibao_mod->drop($ids);
		
        $this->show_message('mibao_drop_pi_piliangshanchumibaokachenggong');

        return;
    }		

    function mibao_edit_xinka()
    {
	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
    if (!$id)
    {
            $this->show_warning('feifacanshu');
            return;
    }
	if($_POST)//����Ƿ��ύ
	{	
  $A1= trim($_POST['A1']);
  $B1= trim($_POST['B1']);
  $C1= trim($_POST['C1']);
  $D1= trim($_POST['D1']);
  $E1= trim($_POST['E1']);
  $F1= trim($_POST['F1']);
  $G1= trim($_POST['G1']);
  $H1= trim($_POST['H1']);

  $A2= trim($_POST['A2']);
  $B2= trim($_POST['B2']);
  $C2= trim($_POST['C2']);
  $D2= trim($_POST['D2']);
  $E2= trim($_POST['E2']);
  $F2= trim($_POST['F2']);
  $G2= trim($_POST['G2']);
  $H2= trim($_POST['H2']);

  $A3= trim($_POST['A3']);
  $B3= trim($_POST['B3']);
  $C3= trim($_POST['C3']);
  $D3= trim($_POST['D3']);
  $E3= trim($_POST['E3']);
  $F3= trim($_POST['F3']);
  $G3= trim($_POST['G3']);
  $H3= trim($_POST['H3']);

  $A4= trim($_POST['A4']);
  $B4= trim($_POST['B4']);
  $C4= trim($_POST['C4']);
  $D4= trim($_POST['D4']);
  $E4= trim($_POST['E4']);
  $F4= trim($_POST['F4']);
  $G4= trim($_POST['G4']);
  $H4= trim($_POST['H4']);

  $A5= trim($_POST['A5']);
  $B5= trim($_POST['B5']);
  $C5= trim($_POST['C5']);
  $D5= trim($_POST['D5']);
  $E5= trim($_POST['E5']);
  $F5= trim($_POST['F5']);
  $G5= trim($_POST['G5']);
  $H5= trim($_POST['H5']);

  $A6= trim($_POST['A6']);
  $B6= trim($_POST['B6']);
  $C6= trim($_POST['C6']);
  $D6= trim($_POST['D6']);
  $E6= trim($_POST['E6']);
  $F6= trim($_POST['F6']);
  $G6= trim($_POST['G6']);
  $H6= trim($_POST['H6']);

  $A7= trim($_POST['A7']);
  $B7= trim($_POST['B7']);
  $C7= trim($_POST['C7']);
  $D7= trim($_POST['D7']);
  $E7= trim($_POST['E7']);
  $F7= trim($_POST['F7']);
  $G7= trim($_POST['G7']);
  $H7= trim($_POST['H7']);

  $A8= trim($_POST['A8']);
  $B8= trim($_POST['B8']);
  $C8= trim($_POST['C8']);
  $D8= trim($_POST['D8']);
  $E8= trim($_POST['E8']);
  $F8= trim($_POST['F8']);
  $G8= trim($_POST['G8']);
  $H8= trim($_POST['H8']);

  $A9= trim($_POST['A9']);
  $B9= trim($_POST['B9']);
  $C9= trim($_POST['C9']);
  $D9= trim($_POST['D9']);
  $E9= trim($_POST['E9']);
  $F9= trim($_POST['F9']);
  $G9= trim($_POST['G9']);
  $H9= trim($_POST['H9']);

  $edit_xinka=array(
  'A1'=>$A1,
  'B1'=>$B1,
  'C1'=>$C1,
  'D1'=>$D1,
  'E1'=>$E1,
  'F1'=>$F1,
  'G1'=>$G1,
  'H1'=>$H1,
  
  'A2'=>$A2,
  'B2'=>$B2,
  'C2'=>$C2,
  'D2'=>$D2,
  'E2'=>$E2,
  'F2'=>$F2,
  'G2'=>$G2,
  'H2'=>$H2,
  
  'A3'=>$A3,
  'B3'=>$B3,
  'C3'=>$C3,
  'D3'=>$D3,
  'E3'=>$E3,
  'F3'=>$F3,
  'G3'=>$G3,
  'H3'=>$H3,
  
  'A4'=>$A4,
  'B4'=>$B4,
  'C4'=>$C4,
  'D4'=>$D4,
  'E4'=>$E4,
  'F4'=>$F4,
  'G4'=>$G4,
  'H4'=>$H4,
  
  'A5'=>$A5,
  'B5'=>$B5,
  'C5'=>$C5,
  'D5'=>$D5,
  'E5'=>$E5,
  'F5'=>$F5,
  'G5'=>$G5,
  'H5'=>$H5,
  
  'A6'=>$A6,
  'B6'=>$B6,
  'C6'=>$C6,
  'D6'=>$D6,
  'E6'=>$E6,
  'F6'=>$F6,
  'G6'=>$G6,
  'H6'=>$H6,
  
  'A7'=>$A7,
  'B7'=>$B7,
  'C7'=>$C7,
  'D7'=>$D7,
  'E7'=>$E7,
  'F7'=>$F7,
  'G7'=>$G7,
  'H7'=>$H7,
  
  'A8'=>$A8,
  'B8'=>$B8,
  'C8'=>$C8,
  'D8'=>$D8,
  'E8'=>$E8,
  'F8'=>$F8,
  'G8'=>$G8,
  'H8'=>$H8,
  
  'A9'=>$A9,
  'B9'=>$B9,
  'C9'=>$C9,
  'D9'=>$D9,
  'E9'=>$E9,
  'F9'=>$F9,
  'G9'=>$G9,
  'H9'=>$H9,																
    );
  $this->my_mibao_mod->edit($id,$edit_xinka);
 
  $this->show_message('mibao_edit_xinka_bianjixinmibaokachenggong',
  'mibao_edit_xinka_fanhuxinkaliebiao','index.php?module=my_money&act=mibao_xinka',
  'caozuoshiwu_fanhuchongxinbianji','index.php?module=my_money&act=mibao_edit_xinka&id='.$id
  );
  return;  
  }
  else
  {
  $index=$this->my_mibao_mod->find($id);//��ȡ�������ݿ�
  $this->assign('index', $index);//���ݵ������
  $this->display('mibao_edit_xinka.html'); 
  return;
  }
}

//�����û�mibao_bangding

    function mibao_bangding()
    {
	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
    if (!$id)
    {
            $this->show_warning('feifacanshu');
            return;
    }
	if($_POST)//����Ƿ��ύ
	{
	$mibao_sn = trim($_POST['mibao_sn']);
	$user_name = trim($_POST['user_name']);
	$time_edit = trim($_POST['time_edit']);

	if(empty($user_name))
    {
		$this->show_warning('mibao_bangding_bangdingyonghumingbunengweikong');
	    return;
	}
    $money_row=$this->my_money_mod->getRow("select user_id,mibao_id from ".DB_PREFIX."my_money where user_name='$user_name' limit 1");
    if($money_row['mibao_id'] <>0)
    {
        $this->show_warning('mibao_bangding_gaiyonghuyijingbangdinglemibao');
        return;
    }

	if($time_edit=="YES")
	{
	$dq_time = strtotime("$dq_time");
	}
	else
	{
	$dq_time = time()+63072000;
	}

	$bd_mibao=array(
	'user_id'=>$money_row['user_id'],
	'user_name'=>$user_name,
	'bd_time'=>time(),
	'dq_time'=>$dq_time,
	'ztai'=>1, 
	);
	$bd_money=array(
	'mibao_id'=>$id,
	'mibao_sn'=>$mibao_sn,
	);
	$this->my_mibao_mod->edit($id,$bd_mibao);//�����ܱ���
	$this->my_money_mod->edit('user_id='.$money_row['user_id'],$bd_money);//�����ܱ���
	$this->show_message('mibao_bangding_bangdingchenggong',
	'mibao_bangding_fanhuixinkaliebiao','index.php?module=my_money&act=mibao_xinka');
	}
	else
	{
	$index=$this->my_mibao_mod->find($id);//��ȡ�������ݿ�
    $this->assign('index', $index);//���ݵ������
    $this->display('mibao_bangding.html'); 
    return;
	}
	}	
	
	//����ܱ���mibao_sn_del
    function mibao_sn_del()
    {
	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
    if (!$id)
    {
            $this->show_warning('feifacanshu');
            return;
    }
	$mobai_edit=array(
	'user_id'=>0,
	'user_name'=>"",
    'bd_time'=>"",
	'dq_time'=>"",
	'ztai'=>0,
    );
	$user_edit=array(
    'mibao_id'=>0,
	'mibao_sn'=>"",
    );
	$this->my_mibao_mod->edit($id,$mobai_edit);//�����ܱ���
	$this->my_money_mod->edit('mibao_id='.$id,$user_edit);//�����ܱ���
    $this->show_message('mibao_sn_del_jiechuchenggong');
	}
	
	
/*��ֵ��ʼ----------------------------------------------------------��ֵ��ʼ*/
    //�ѳ�ֵ�б�
    function card_yichongzhi()
    {
	
	
	$cardname = trim($_GET['cardname']);
	$conditions ='and user_id>0';
	$by="id";
	$sc="desc";
    $page = $this->_get_page();
	$index=$this->my_card_mod->find(array(
            'conditions' => '1=1 '.$conditions ,//���� �磺where
            'limit' => $page['limit'],
			'order' => "$by $sc",
			'count' => true,
        ));	
	/*
	$index=$this->my_card_mod->find(array(
            'conditions' => 'user_id>0' ,//���� �磺where
            'limit' => $page['limit'],
			'count' => true,
        ));	
	*/	
	$page['item_count'] = $this->my_card_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('card_yichongzhi.html'); 
	return;
	}
    //�ѹ����б�
    function card_guoqi()
    {
	$xz_time=time();
    $page = $this->_get_page();
	$index=$this->my_card_mod->find(array(
            'conditions' => "user_id=0 and guoqi_time<'$xz_time'" ,//���� �磺where
            'limit' => $page['limit'],
			'count' => true,
        ));	
	$page['item_count'] = $this->my_card_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('card_guoqi.html'); 
	return;
	}
    //δ��ֵ�б�
    function card_weichongzhi()
    {
	$xz_time=time();
    $page = $this->_get_page();
	$index=$this->my_card_mod->find(array(
            'conditions' => "user_id=0 and guoqi_time>'$xz_time'",//���� �磺where
            'limit' => $page['limit'],
			'count' => true,
        ));	
	$page['item_count'] = $this->my_card_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('card_weiguoqi.html'); 
	return;
	}							
    //����ɾ����ֵ��
    function card_drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('feifacanshu');
            return;
        }
        
        $ids = explode(',', $id);
       
        $this->my_card_mod->drop($ids);
		
        $this->show_message('card_drop_pi_piliangshanchuchongzhikachenggong');
        return;
    }	
    //ɾ����ֵ��
    function card_del()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('feifacanshu');
            return;
        }
        $this->my_card_mod->drop($id);
		
        $this->show_message('card_drop_shanchuchongzhikachenggong');
        return;
    }		
	
    //�ܱ�����
	function card_soso()//Ĭ����ʾ
    {
	$sombsn=$_GET["sombsn"];
	$sotime=$_GET["sotime"];
	$endtime=$_GET["endtime"];
	$ztai=$_GET["ztai"];
		if(empty($ztai)) $ztai=1;
		$sotimes= strtotime("$sotime");
		$endtimes= strtotime("$endtime")+86399;// ����23Сʱ59��59��
		if(empty($sombsn) and empty($sotime) and empty($endtime))
        {
		$this->show_warning('user_money_add_nizongdeshurudianshenmeba');
	    return;
		}		
		$page = $this->_get_page();	
		//��������Ϊ��
        if (isset($sombsn) and isset($sotime) and isset($endtime)) 
        { 
		$index=$this->my_mibao_mod->find(array(
	    'conditions' => "mibao_sn LIKE '%$sombsn%' and bd_time>='$sotimes' and bd_time<'$endtimes' and ztai='$ztai'",
        'limit' => $page['limit'],
		'count' => true));
        }
	   	//��ʼʱ�� �� ����ʱ�� Ϊ�գ��������������û�			
	    if(empty($sotime) or empty($endtime))
        {
		if(empty($sotimes))  $sotimes=1;
	    if(empty($endtimes)) $endtimes=1300000000;	
		$index=$this->my_mibao_mod->find(array(
	        'conditions' => "bd_time>='$sotimes' and bd_time<'$endtimes' and ztai='$ztai'",
            'limit' => $page['limit'],
			'count' => true));
		}
		//�û�Ϊ�գ�����������ʼʱ��-����ʱ��
	    else
        {
		$index=$this->my_mibao_mod->find(array(
	        'conditions' => "mibao_sn LIKE '%$sombsn%' and ztai='$ztai'",
            'limit' => $page['limit'],
			'count' => true));
		}	

		$page['item_count'] = $this->my_moneylog_mod->getCount('user_name='.$soname);
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);
		if($ztai==1)$this->display('mibao_zhengchang.html'); 
		if($ztai==2)$this->display('mibao_zanting.html'); 
		if($ztai==3)$this->display('mibao_guoqi.html'); 
	    return;

	}
	
	function setup()//ϵͳ����
    {
	   if($_POST)
	   {
	   $chinabank_key = trim($_POST['chinabank_key']);
	   $chinabank_mid = trim($_POST['chinabank_mid']);
	   $chinabank_url = trim($_POST['chinabank_url']);
 
	   if(empty($chinabank_key) or empty($chinabank_mid) or empty($chinabank_url))
       {
	   		$this->show_warning('user_money_add_nizongdeshurudianshenmeba');
	   		return;
	   }
	   if (preg_match("/[^0.-9]/",$chinabank_mid))
       {
	   $this->show_warning('����MID����Ϊ����'); 
       return;
       }

	   //д��LOG��¼
	   //$dq_time=date("Y-m-d-His",time());
	   $setup_array=array(
	   'chinabank_key'=>$chinabank_key,
	   'chinabank_mid'=>$chinabank_mid,
	   'chinabank_url'=>$chinabank_url,
	   );
	   /*
	   $user_id = $this->visitor->get('user_id');
            if (!empty($_FILES['chinabank_url']))
            {
                $chinabank_url = $this->_upload_jifen_img($user_id);
                if ($chinabank_url === false)
                {
                    return;
                }
                $data['chinabank_url'] = $chinabank_url;
            }	   
	   */
	   $this->my_paysetup_mod->edit('id=1',$setup_array);

	   $this->show_message('�ɹ���������!','���ص���','index.php?module=my_money&act=index');
	        return;
	   }
	   else
	   {
       $index=$this->my_paysetup_mod->find('id=1');
	   $this->assign('index', $index);
       $this->display('paysetup.html'); 
	   }
	   return;
	}

    function jifen_chaxun1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111()
    {
    $page = $this->_get_page();
	$index=$this->my_money_mod->find(array(
            'conditions' => '',//���� �磺where
            'limit' => $page['limit'],
			'order' => "jifen desc",
			'count' => true,
        ));	
	$page['item_count'] = $this->my_jifen_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('jifen_chaxun.html');  
	return;
	}

    function jifen_chaxun()
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
	$soname=$_GET["soname"];	
	$sojifen=$_GET["sojifen"];	
	$endjifen=$_GET["endjifen"];	
	
    $page = $this->_get_page();
	//�����û�Ϊ�վ�����ȫ��	
	if(empty($soname))
	{
    //��� ��ʼ��� ������� ��Ϊ��
    if(empty($sojifen) and empty($endjifen)) 
    {
	if($privs=="all")
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => '',//����
    'limit' => $page['limit'],
	'order' => "jifen desc",
	'count' => true));	
	}
    else
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => "city='$city'",//����
    'limit' => $page['limit'],
	'order' => "jifen desc",
	'count' => true));	
	}
	//������Ȼ�г������û���������С����
	}
	//�������� �û�������ʼ���-�������
	else
	{
	if(empty($sojifen)){$sojifen=0;}//��ʼ���Ϊ�վ�=0
	if(empty($endjifen)){$endjifen=9999999;}//�������Ϊ�վ�=9999999
	
	if($privs=="all")
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$soname%' and jifen>='$sojifen' and jifen<='$endjifen'",//����
    'limit' => $page['limit'],
	'order' => "jifen desc",
	'count' => true));	
	}
	else
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$soname%' and jifen>='$sojifen' and jifen<='$endjifen' and city='$city'",//����
    'limit' => $page['limit'],
	'order' => "jifen desc",
	'count' => true));	
	}
	}
	}
	else
	{//�����û�����Ϊ��
	
    //�û���Ϊ�� ˫ʱ��Ϊ��
    if(empty($sojifen) and empty($endjifen)) 
    {
	if($privs=="all")
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$soname%'",//����
    'limit' => $page['limit'],
	'order' => "jifen desc",
	'count' => true));	
	}
	else
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$soname%' and city='$city'",//����
    'limit' => $page['limit'],
	'order' => "jifen desc",
	'count' => true));	
	}
	}
	//�û���Ϊ�� ˫ʱ��Ҳ��Ϊ��
	else
	{
	if(empty($sojifen)){$sojifen=0;}
	if(empty($endjifen)){$endjifen=999999999;}
	if($privs=="all")
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$soname%' and jifen>='$sojifen' and jifen<='$endjifen'",//����
    'limit' => $page['limit'],
	'order' => "jifen desc",
	'count' => true));	
	}
	else
	{
	$index=$this->my_money_mod->find(array(
	'conditions' => "user_name LIKE '%$soname%' and jifen>='$sojifen' and jifen<='$endjifen' and city='$city'",//����
    'limit' => $page['limit'],
	'order' => "jifen desc",
	'count' => true));	
	}
	}
	}
		$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }	
		$page['item_count'] = $this->my_money_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('jifen_chaxun.html');
	    return;
	}	
	
    function jifen_shezhi()
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
    $page = $this->_get_page();
	if($privs=="all")
	{
	$index=$this->my_jifen_mod->find(array(
            'conditions' => 'yes_no=1',//���� �磺where
            'limit' => $page['limit'],
			'order' => "ids desc",
			'count' => true,
        ));	
		}
		else
		{
		$index=$this->my_jifen_mod->find(array(
            'conditions' => "yes_no=1 and jf_city='$city'",//���� �磺where
            'limit' => $page['limit'],
			'order' => "ids desc",
			'count' => true,
        ));	
		}
		$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['jf_city']];	
        }
	$page['item_count'] = $this->my_jifen_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('jifen_shezhi.html');  
	return;
	}
			
	function jifen_add()//������� ORDER BY `ecm_my_jifen`.`id` ASC 
    {
		$this->city_mod=& m('city');
		$userid=$this->visitor->get('user_id');
		$priv_row=$this->city_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
		$privs=$priv_row['privs'];
		$cityid=$priv_row['city'];
	
	   if($_POST)
	   {
	   $yes_no = 1;
	   $ids = trim($_POST['ids']);
	   $jifen = trim($_POST['jifen']);

       $add_time = time();
	   $wupin_name = trim($_POST['wupin_name']);
	   $wupin_img = trim($_POST['wupin_img']);
	   $jiazhi = trim($_POST['jiazhi']);
	   $shuliang = trim($_POST['shuliang']);
	   $yiduihuan = trim($_POST['yiduihuan']);
	   $log_text = trim($_POST['log_text']);
	   $jf_city = trim($_POST['jf_city']);
	   if(empty($ids))
       {
	   		$ids=255;
	   }
       if($ids >255)
	   {
	   		$ids=255;	   
	   }
	   if(empty($wupin_name) or empty($jifen) or empty($shuliang))
       {
	   		$this->show_warning('user_money_add_nizongdeshurudianshenmeba');
	   		return;
	   }
	   if (preg_match("/[^0.-9]/",$shuliang))
       {
	   $this->show_warning('��������Ϊ����'); 
       return;
       }

	   $setup_array =array(
	   'yes_no'  =>    $yes_no,
	   'ids'     =>    $ids,
	   'add_time' =>   $add_time,
	   'jifen'    =>   $jifen,
	   'wupin_name' => $wupin_name,
	   'wupin_img'  => $wupin_img,
	   'jiazhi'     => $jiazhi,
	   'shuliang'   => $shuliang,
	   'yiduihuan'  => $yiduihuan,
	   'log_text'   => $log_text,
	   'jf_city'   => $jf_city,
       );

	   $ida=$this->my_jifen_mod->getRow("select * from ".DB_PREFIX."my_jifen ORDER BY id desc limit 1");
	   $idb=++$ida['id'];
            if (!empty($_FILES['wupin_img']))
            {
                $wupin_img = $this->_upload_jifen_img($idb);
                if ($wupin_img === false)
                {
                    return;
                }
                $setup_array['wupin_img'] = $wupin_img;
            }	   

	   $this->my_jifen_mod->add($setup_array);

	   $this->show_message('��Ʒ���óɹ�!','�����б�','index.php?module=my_money&act=jifen_shezhi');
	        return;
	   }
	   else
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
       		$this->display('jifen_add.html'); 
	   }
	   return;
	}
	function jifen_yiduihuan()//�����Ѷһ�
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
    $page = $this->_get_page();
	if($privs=="all")
	{
	$index=$this->my_jifen_mod->find(array(
            'conditions' => 'yes_no=0',//���� �磺where
            'limit' => $page['limit'],
			'order' => "riqi desc",
			'count' => true,
        ));	
		}
		else
		{
		$index=$this->my_jifen_mod->find(array(
            'conditions' => "yes_no=0 and jf_city='$city'",//���� �磺where
            'limit' => $page['limit'],
			'order' => "riqi desc",
			'count' => true,
        ));	
		}
		$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['jf_city']];	
        }
	$page['item_count'] = $this->my_jifen_mod->getCount();
    $this->_format_page($page);
	$this->assign('page_info', $page);
	$this->assign('index', $index);
	$this->display('jifen_yiduihuan.html');  
	return;
	}
	
	function jifen_id()//�����Ѷһ�
    {
	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
	if($_POST)
	{
	   $setup_array =array(
	   'wuliu_name'   => trim($_POST['wuliu_name']),
	   'wuliu_danhao'   => trim($_POST['wuliu_danhao']),
	   'shenhe'   => trim($_POST['shenhe']),
       );
	$this->my_jifen_mod->edit($id,$setup_array);
	$this->show_message('��˳ɹ�!','�����б�','index.php?module=my_money&act=jifen_yiduihuan');
	        return;	
	}
	else
	{
	$index=$this->my_jifen_mod->find($id);//��ȡ�������ݿ�
    $this->assign('index', $index);//���ݵ������
    $this->display('jifen_id.html'); 
    return;
    }
	}
    /**
     * �ϴ�����ͼ��
     *
     * @param int $user_id
     * @return mix false��ʾ�ϴ�ʧ��,�մ���ʾû���ϴ�,string��ʾ�ϴ��ļ���ַ
     */
    function _upload_jifen_img($idb)
    {
        $file = $_FILES['wupin_img'];
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
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?module=my_money&act=setup');
            return false;
        }
        $uploader->root_dir(ROOT_PATH);
        return $uploader->save('data/files/mall/jifen_img', $idb);
		
    }	
	
	function jifen_del()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('feifacanshu');
            return;
        }
        $this->my_jifen_mod->drop($id);
		
        $this->show_message('card_drop_shanchulipinduihuanchenggong');
        return;
    }		
	
	function suoding()
	{
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getrow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];

	   if($_POST)
	   {
	   $user_name= trim($_POST['user_name']);
	   $suoding= trim($_POST['suoding']);
	   $jia_or_jian= trim($_POST['jia_or_jian']);
	   $riqi=date('Y-m-d H:i:s');
	  // $time_edit= trim($_POST['time_edit']);
	   $log_text= trim($_POST['log_text']);	   
	   if(empty($user_name) or empty($suoding))
       {
	   		$this->show_warning('user_money_add_nizongdeshurudianshenmeba');
	   		return;
	   }
	   if (preg_match("/[^0.-9]/",$suoding))
       {
	   $this->show_warning('cuowu_nishurudebushishuzilei'); 
       return;
       }
$money_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_name='$user_name' limit 1");	
$user_ids=$money_row['user_id'];  
$my_money=$money_row['money'];
$city=$money_row['city'];
$my_money_dj=$money_row['money_dj'];
$duihuanjifen=$money_row['duihuanjifen'];
$dongjiejifen=$money_row['dongjiejifen'];
$zhanghu_suoding=$money_row['suoding_money'];
$canshu_row=$this->canshu_mod->getrow("select * from ".DB_PREFIX."canshu ");	
$zong_jinbi=$canshu_row['zong_jinbi'];
$yu_jinbi=$canshu_row['yu_jinbi'];
$zong_money=$canshu_row['zong_money'];
if($jia_or_jian=='jia')
{
$new_suoding=$zhanghu_suoding+$suoding;
}
if($jia_or_jian=='jian')
{
 if($zhanghu_suoding>=$suoding)
	   {	   
	  $new_suoding=$zhanghu_suoding-$suoding;
	   }
       else
	   {
	   		$this->show_warning('xianjinxiaoyu');
	        return;
	   }

}

//�����û��ʽ�
  $money_array=array(
	   'suoding_money'=>$new_suoding,
	   );
 $this->my_money_mod->edit('user_id='.$user_ids,$money_array);
//�����û��ʽ���ˮ
if($jia_or_jian=='jia')
{
$beizhu=$this->visitor->get('user_name').Lang::get('suodingjine').$user_name.Lang::get('dejine').$suoding.Lang::get('yuan');
	 $logs_array=array(
		   'user_id'=>$user_ids,
		   'user_name'=>$user_name,
		   'log_text'=>$beizhu,
		   'admin_name' =>$this->visitor->get('user_name'),
		   'suoding_money'=>$suoding,
		   'city'=>$city,
		   'type'=>15,
		   'dq_money'=>$my_money,
		   'dq_money_dj'=>$my_money_dj,
		   'dq_jifen'=>$duihuanjifen,
		   'dq_jifen_dj'=>$dongjiejifen,
		   );
		   $addlog=array(
						'time'=>$riqi,
						'user_name'=>$user_name,
						'user_id'=>$user_ids,
						'zcity'=>$city,
						'type'=>31,
						's_and_z'=>0,
						'beizhu'=>$beizhu,
						'dq_money'=>$my_money,
					    'dq_money_dj'=>$my_money_dj,
					    'dq_jifen'=>$duihuanjifen,
					    'dq_jifen_dj'=>$dongjiejifen,
                      );
		   
}
if($jia_or_jian=='jian')
{
$beizhu=$this->visitor->get('user_name').Lang::get('jiesuojine').$user_name.Lang::get('dejine').$suoding.Lang::get('yuan');

	 $logs_array=array(
		   'user_id'=>$user_ids,
		   'user_name'=>$user_name,
		   'log_text'=>$beizhu,
		   'admin_name' =>$this->visitor->get('user_name'),
		   'suoding_money'=>'-'.$suoding,
		   'city'=>$city,
		   'type'=>23,
		   'dq_money'=>$my_money,//�ӳ�ֵ�Ľ��
		   'dq_money_dj'=>$my_money_dj,
		   'dq_jifen'=>$duihuanjifen,
		   'dq_jifen_dj'=>$dongjiejifen,
		   );
		   $addlog=array(
						'time'=>$riqi,
						'user_name'=>$user_name,
						'user_id'=>$user_ids,
						'zcity'=>$city,
						'type'=>33,
						's_and_z'=>0,
						'beizhu'=>$beizhu,
						'dq_money'=>$my_money,
					    'dq_money_dj'=>$my_money_dj,
					    'dq_jifen'=>$duihuanjifen,
					    'dq_jifen_dj'=>$dongjiejifen,
                      ); 
	   
}
	   
   
  $this->my_moneylog_mod->add($logs_array);
  $this->moneylog_mod->add($addlog);
   $this->show_message('caozuochenggong');
        return;

}
 else
	   {
	   $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
	   $user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';
	   if(!empty($user_id))
       {
       $index=$this->my_money_mod->find('user_id='.$user_id);
	   }
	   $this->assign('index', $index);
       $this->display('user_money_suoding.html'); 
	   }
	   return;
	
	
	}
	function suoding_jifen()
	{
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];

	   if($_POST)
	   {
	   $user_name= trim($_POST['user_name']);
	   $suoding= trim($_POST['suoding_jifen']);
	   $jia_or_jian= trim($_POST['jia_or_jian']);
	   $riqi=date('Y-m-d H:i:s');
	  // $time_edit= trim($_POST['time_edit']);
	   $log_text= trim($_POST['log_text']);	   
	   if(empty($user_name) or empty($suoding))
       {
	   		$this->show_warning('user_money_add_nizongdeshurudianshenmeba');
	   		return;
	   }
	   if (preg_match("/[^0.-9]/",$suoding))
       {
	   $this->show_warning('cuowu_nishurudebushishuzilei'); 
       return;
       }
$money_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_name='$user_name' limit 1");	
$user_ids=$money_row['user_id'];  
$my_money=$money_row['money'];
$city=$money_row['city'];
$my_money_dj=$money_row['money_dj'];
$duihuanjifen=$money_row['duihuanjifen'];
$dongjiejifen=$money_row['dongjiejifen'];
$zhanghu_suoding=$money_row['suoding_jifen'];

$canshu_row=$this->canshu_mod->getrow("select * from ".DB_PREFIX."canshu ");	
$zong_jinbi=$canshu_row['zong_jinbi'];
$yu_jinbi=$canshu_row['yu_jinbi'];
$zong_money=$canshu_row['zong_money'];
if($jia_or_jian=='jia')
{
$new_suoding=$zhanghu_suoding+$suoding;
}
if($jia_or_jian=='jian')
{
  if($zhanghu_suoding>=$suoding)
	   {	   
	  $new_suoding=$zhanghu_suoding-$suoding;
	   }
       else
	   {
	   		$this->show_warning('jifenxiaoyu');
	        return;
	   }





}//�����û��ʽ�
  $money_array=array(
	   'suoding_jifen'=>$new_suoding,
	   );
 $this->my_money_mod->edit('user_id='.$user_ids,$money_array);
//�����û��ʽ���ˮ
if($jia_or_jian=='jia')
{
$beizhu=$this->visitor->get('user_name').Lang::get('suodingjine').$user_name.Lang::get('dejifen').$suoding.Lang::get('jifen');

 $logs_array=array(
	   'user_id'=>$user_ids,
	   'user_name'=>$user_name,
	   'log_text'=>$beizhu,
	   'admin_name' =>$this->visitor->get('user_name'),
	   'suoding_jifen'=>$suoding,
	   'city'=>$city,
	   'type'=>21,
	   'dq_money'=>$my_money,//�ӳ�ֵ�Ľ��
	   'dq_money_dj'=>$my_money_dj,
	   'dq_jifen'=>$duihuanjifen,
	   'dq_jifen_dj'=>$dongjiejifen,
	   );
	   
	    $addlog=array(
						'time'=>$riqi,
						'user_name'=>$user_name,
						'user_id'=>$user_ids,
						'zcity'=>$city,
						'type'=>32,
						's_and_z'=>0,
						'beizhu'=>$beizhu,
						'dq_money'=>$my_money,//�ӳ�ֵ�Ľ��
					    'dq_money_dj'=>$my_money_dj,
					    'dq_jifen'=>$duihuanjifen,
					    'dq_jifen_dj'=>$dongjiejifen,
                      ); 
	   
}
if($jia_or_jian=='jian')
{
$beizhu=$this->visitor->get('user_name').Lang::get('jiesuojine').$user_name.Lang::get('dejifen').$suoding.Lang::get('jifen');

 $logs_array=array(
	   'user_id'=>$user_ids,
	   'user_name'=>$user_name,
	   'log_text'=>$beizhu,
	   'admin_name' =>$this->visitor->get('user_name'),
	   'suoding_jifen'=>'-'.$suoding,
	   'city'=>$city,
	   'type'=>22,
	   'dq_money'=>$my_money,//�ӳ�ֵ�Ľ��
	   'dq_money_dj'=>$my_money_dj,
	   'dq_jifen'=>$duihuanjifen,
	   'dq_jifen_dj'=>$dongjiejifen,
	   );
	    $addlog=array(
						'time'=>$riqi,
						'user_name'=>$user_name,
						'user_id'=>$user_ids,
						'zcity'=>$city,
						'type'=>33,
						's_and_z'=>0,
						'beizhu'=>$beizhu,
						'dq_money'=>$my_money,//�ӳ�ֵ�Ľ��
					    'dq_money_dj'=>$my_money_dj,
					    'dq_jifen'=>$duihuanjifen,
					    'dq_jifen_dj'=>$dongjiejifen,
                      ); 
	   
	   
}

  $this->my_moneylog_mod->add($logs_array);
  $this->moneylog_mod->add($addlog);
   $this->show_message('caozuochenggong');
        return;

}
 else
	   {
	   $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
	   $user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';
	   if(!empty($user_id))
       {
       $index=$this->my_money_mod->find('user_id='.$user_id);
	   }
	   $this->assign('index', $index);
       $this->display('user_jifen_suoding.html'); 
	   }
	   return;
	
	
	}
	function shangxiaxian()
    {
	$log_id=1;
	$tx_min=trim($_POST['tx_min']);
	$tx_max=trim($_POST['tx_max']);
	$cz_min=trim($_POST['cz_min']);
	$cz_max=trim($_POST['cz_max']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'tx_min'=>$tx_min,	
			'tx_max'=>$tx_max,	
			'cz_min'=>$cz_min,	
			'cz_max'=>$cz_max,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=shangxiaxian',
    'fanhui',    'index.php?module=my_money&act=shangxiaxian');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('shangxiaxian.html');
	    return;
	}
	}
function user_moneylog()
  {
	$user=$this->visitor->get('user_name');
    $userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];
	$endtime=$_GET["endtime"];	
	$suoshuzhan=$_GET["suoshuzhan"];	
	$riqi=date('Y-m-d');
	$this->assign('priv_row',$priv_row);
	$this->assign('soname',$so_user_name);
	$this->assign('endtime',$endtime);
	$this->assign('suoshuzhan',$suoshuzhan);
	$this->assign('sotime',$sotime);

	$conditions='1=1';
	if(!empty($sotime))
	{
		$conditions.=" and time>='$sotime'";
	}
	 if(!empty($endtime))
	{
		$conditions.=" and time<= '$endtime 24:59:59'";
	}
	if(!empty($so_user_name))
	{
		$conditions.=" and user_name like '%$so_user_name%'";
	}
	if(!empty($suoshuzhan))
	{
	 $conditions.=" and zcity=$suoshuzhan";
	}
	$page = $this->_get_page();	

	if($privs=="all")
	{
		$index=$this->moneylog_mod->find(array(
	        'conditions' => $conditions ,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
	}
	else
	{
	$index=$this->moneylog_mod->find(array(
	'conditions' =>$conditions. " and  zcity='$city'",
	'limit' => $page['limit'],
	'order' => "id desc",
	'count' => true));
	}

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
			$index[$key]['city_name'] = $city_row[$val['zcity']];	
        }
		$page['item_count'] = $this->moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('user_moneylog.html'); 
	   return;
	}
	
	function reg_jifen()
    {
	$log_id=1;
	$reg_jifen=trim($_POST['reg_jifen']);
	$qiandao_jifen=trim($_POST['qiandao_jifen']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'reg_jifen'=>$reg_jifen,
			'qiandao_jifen'=>$qiandao_jifen																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('xiugaichenggong',
    'caozuochenggong', 'index.php?module=my_money&act=reg_jifen',
    'fanhui',    'index.php?module=my_money&act=reg_jifen');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('reg_jifen.html');
	    return;
	}
	}

function dakuan_wei_shenhe()//caozuo=60
    {
	$user=$this->visitor->get('user_name');
	$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user' limit 1");
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row',$priv_row);
	//��������
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	$suoshuzhan=$_GET["suoshuzhan"];	
	$this->assign('so_user_name',$so_user_name);
	$this->assign('sotime',$sotime);
	$this->assign('endtime',$endtime);
	$this->assign('suoshuzhan',$suoshuzhan);
	$conditions="1=1";
	
	if(!empty($so_user_name))
	{
		$conditions.=" and user_name like '%$so_user_name%'";
	}
	if(!empty($sotime))
	{
		$conditions.=" and riqi>='$sotime'";
	}
	if(!empty($endtime))
	{
		$conditions.=" and riqi<='$endtime 24:59:59'";
	}
	if(!empty($suoshuzhan))
	{ 
		$conditions.=" and city='$suoshuzhan'";
	}
		
	
	       $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. ' and leixing=40 and status1=2 and caozuo=60',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. ' and leixing=40 and status1=2 and caozuo=60 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			
		$city_row=array();
		$result=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
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
			
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('dakuan_wei_shenhe.html'); 
	   return;
	}
	function dakuan_shenhe_user()
    {
	$log_id=$_GET['log_id'];
	$user_id=$_GET['user_id'];
	$user_name=$_GET['user_name'];
	$status=trim($_POST['status']);
	$log_text=trim($_POST['log_text']);
	$riqi=date('Y-m-d H:i:s');
	$order_id=trim($_POST['order_id']);
	$money_djs=trim($_POST['money_djs']);
	$money_chu=trim($_POST['money_chu']);
	$admin_time = time();

$money_row=$this->my_money_mod->getRow("select money_dj,money,duihuanjifen,dongjiejifen from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");
$row_money_dj=$money_row['money_dj'];
$row_money_money=$money_row['money'];
$row_money_duihuanjifen=$money_row['duihuanjifen'];
$row_money_dongjiejifen=$money_row['dongjiejifen'];
	if($_POST)
	{
			$moneylog_row=$this->my_moneylog_mod->getRow("select * from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");

            if($moneylog_row['status']!='' ||  $moneylog_row['status1']!=2)
            {
                echo iconv('utf-8','gb2312','�����ظ�������');
                echo " <a href='javascript:history.go(-1);'>".iconv('utf-8','gb2312','����')."</a>";
                return;
            }
	if($status=="1")//���ɹ�
	{
		$edit_moneylog=array(
			'order_id'=>$order_id,
			'admin_time'=>$admin_time,		
			'caozuo'=>61,
			'status'=>$status,
			);
	$this->my_moneylog_mod->edit('id='.$log_id,$edit_moneylog);
	
		$this->canshu_mod=& m('canshu');
		$jinbi_row=$this->canshu_mod->getRow("select zong_money,zong_jifen,duihuanxianjinfeilv,jifenxianjin from ".DB_PREFIX."canshu");
		$zong_money=$jinbi_row['zong_money'];
		$zong_jifen=$jinbi_row['zong_jifen'];
		$duihuanxianjin_feilv=$jinbi_row['lv31'];//���ֵ�31%
		$jifenxianjin_bili=$jinbi_row['jifenxianjin'];

		
		$row_money=$moneylog_row['money'];//�۳����ַ��õ�Ǯ(����)
		$city=$moneylog_row['city'];
		$r_moneydj=$moneylog_row['money_dj'];//���ֽ��
		$username=$moneylog_row['user_name'];
		$userid=$moneylog_row['user_id'];
		$lev=$this->my_moneylog_mod->getRow("select level from ".DB_PREFIX."member where user_id='$user_id' limit 1");

		//$row_money_zs=$moneylog_row['money_zs'];//ʵ�����ֵ�Ǯ
		$row_money_feiyong=$moneylog_row['money_feiyong'];//���ַ���(����)
		//$z_money=$row_money_zs-$row_money_feiyong;//���˻�ʵ�ʼ��ٵ�Ǯ
		$tx_shiji=$row_money+$row_money_feiyong;//���ֵĽ�������û�п۳����õ�Ǯ��
		$tx_jifen=$r_moneydj*$jifenxianjin_bili;//���ֵĻ���
		//$tx_bili=$r_moneydj*$duihuanxianjin_feilv;//���ֵ�31%,
		$shiji_tixian=$r_moneydj-abs($row_money_feiyong);//�۳�����ʣ�µ�Ǯ

//�����û���money��
		$new_money_dj=$row_money_dj-$r_moneydj;
			$new_money=array(
					'money_dj'=>$new_money_dj,																	
			);
			$this->my_money_mod->edit('user_id='.$user_id,$new_money);//��ȡ�������ݿ�
			//�����û�my_moneylog��
			$edit_log=array(
					'money'=>'-'.$shiji_tixian,		//ʵ�����ֵ�Ǯ														
			);
			$this->my_moneylog_mod->edit('id='.$log_id,$edit_log);

	//�������˻��ʽ�
   
			$can_id=1;
			//$new_zong_money=$zong_money+$row_money;//�����˻���ȥ���ֽ��
			$new_zong_money=$zong_money+abs($row_money_feiyong);
			$edit_canshu=array(
			'zong_money'=>$new_zong_money,
			);
			$this->canshu_mod->edit('id='.$can_id,$edit_canshu);	

	//���moneylog���ֽ����־
	
		$beizhu1 =Lang::get('tixianfei');
		$beizhu1=str_replace('{1}',abs($row_money_feiyong),$beizhu1);
		$addlog=array(
			//'money_dj'=>'-'.$shiji_tixian,//����
			'money'=>$tx_shiji,
			'time'=>$riqi,
			'user_name'=>$username,
			'user_id'=>$user_id,
			'zcity'=>$city,
			'type'=>5,
			's_and_z'=>2,
			'beizhu'=>$beizhu1,
			'dq_money'=>$row_money_money,
			'dq_money_dj'=>$new_money_dj,
			'dq_jifen'=>$row_money_duihuanjifen,
			'dq_jifen_dj'=>$row_money_dongjiejifen
		);
		 $this->moneylog_mod->add($addlog);
		//���moneylog���ַ�����־
		$bb=explode(',',$lev['level']);
		/*if(!in_array(1,$bb))
		{*/
		//$beizhu =Lang::get('kouchu').$username.Lang::get('tixianfeiyong').abs($row_money_feiyong).Lang::get('yuan');
		$addlog1=array(
			'money_dj'=>$row_money_feiyong,//����
			'time'=>$riqi,
			'user_name'=>$username,
			'user_id'=>$user_id,
			'zcity'=>$city,
			'type'=>7,
			's_and_z'=>2,
			'beizhu'=>$beizhu,
			'dq_money'=>$row_money_money,
			'dq_money_dj'=>$new_money_dj,
			'dq_jifen'=>$row_money_duihuanjifen,
			'dq_jifen_dj'=>$row_money_dongjiejifen		
		);
		 //$this->moneylog_mod->add($addlog1);		
//���accountlog��־
		//$beizhu =Lang::get('shouqule').$username.Lang::get('tixianfeiyong').abs($row_money_feiyong).Lang::get('yuan');
		$addaccoun=array(
			'money'=>abs($row_money_feiyong),
			'time'=>$riqi,
			'user_name'=>$username,
			'user_id'=>$user_id,
			'zcity'=>$city,
			'type'=>7,
			's_and_z'=>1,
			'beizhu'=>$beizhu,
			'dq_money'=>$new_zong_money,
			'dq_jifen'=>$zong_jifen,
		);
		 $this->accountlog_mod->add($addaccoun);	
		/*}*/


		//���ͨ������֪ͨ���û�
		$this->message_mod=& m('message');
		$beizhu =Lang::get('tixiancheng');
		$beizhu=str_replace('{1}',$username,$beizhu);
		$adda1=array(
			'from_id'=>0,
			'to_id'=>$user_id,
			'title'=>$title,
			'content'=>$beizhu,
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3,
		);
		 $this->message_mod->add($adda1);	
}

	if($status==2)//û�д��
	{
		$this->my_moneylog_mod->edit('id='.$log_id,array("status1"=>1,"caozuo"=>60));
	}

    $this->show_message('shenhechenggong',
   /* 'caozuochenggong', 'index.php?module=my_money&act=cz_shenhe_user&user_id='.$user_id.'&log_id='.$log_id,*/
    'fanhuiliebiao',    'index.php?module=my_money&act=tx_yi_shenhe');
	}
	else
	{
	if(empty($log_id) or empty($user_id))
    {
		$this->show_warning('feifacanshu');
	    return;
	}
	    $logs_data=$this->my_moneylog_mod->find('id='.$log_id);
		$log=$this->my_moneylog_mod->getRow("select * from ".DB_PREFIX."my_moneylog where id='$log_id' limit 1");
		$log['money']=abs($log['money']);
		$log['money_feiyong']=abs($log['money_feiyong']);
		
	    $user_data=$this->my_money_mod->find('user_id='.$user_id);
		$mon=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");
		$mem=$this->my_money_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");
		/*foreach($logs_data as $key=>$val)
		{
			$logs_data[$key]['money']=abs($val['money']);
			$logs_data[$key]['money_feiyong']=abs($val['money_feiyong']);
		}*/
		$this->assign('log', $logs_data);
		$this->assign('user', $user_data);
		$this->assign('log', $log);
	    $this->assign('mem', $mem);
		$this->assign('mon', $mon);
        $this->display('dakuan_shenhe_user.html');
	    return;
	}
	}
	function dakuan_yi_shenhe()//caozuo=61
    {
	$user=$this->visitor->get('user_name');
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->_admin_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	if(!empty($so_user_name))
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
        $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. "leixing=40 and caozuo=61 and status1!=1",
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_moneylog_mod->find(array(
	        'conditions' =>$conditions. "leixing=40 and status1!=1 and caozuo=61 and city='$city'",
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			 $city_row=array();
		$result=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }	
			
		$page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('dakuan_yi_shenhe.html'); 
	   return;
	}

function taocan_wei_shenhe()//caozuo=60
    {
	$user=$this->visitor->get('user_name');
	
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->my_moneylog_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	//��������
	$so_user_name=$_GET["soname"];	
	$sotime=$_GET["sotime"];	
	$endtime=$_GET["endtime"];	
	if(!empty($so_user_name))
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		if(empty($sotime) and empty($endtime))
		{
			$conditions="user_name like '%$so_user_name%' and ";
		}
	}
	else
	{
		if(!empty($sotime) and empty($endtime))
		{
			$conditions="riqi>='$sotime' and ";
		}
		if(empty($sotime) and !empty($endtime))
		{
			$conditions="riqi<='$endtime 24:59:59' and ";
		}
		if(!empty($sotime) and !empty($endtime))
		{
			$conditions="riqi>='$sotime' and riqi<='$endtime 24:59:59' and ";
		}
		
	}
	       $page = $this->_get_page();	
		if($privs=="all")
		{	
		$index=$this->my_webserv_mod->find(array(
	        'conditions' =>$conditions. 'status=0',
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			else
			{
			$index=$this->my_webserv_mod->find(array(
	        'conditions' =>$conditions. 'status=0 and city='.$city,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
			}
			
		$city_row=array();
		$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }		
			
		$page['item_count'] = $this->my_webserv_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//���ݵ������
        $this->display('taocan_wei_shenhe.html'); 
	   return;
	}


}
?>