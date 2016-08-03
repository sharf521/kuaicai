<?php
/**
 * 后台团购管理控制器
 *
 */

class GroupbuyApp extends BackendApp
{
    var $_groupbuy_mod;

    function __construct()
    {
        $this->GroupbuyApp();
    }

    function GroupbuyApp()
    {
        parent::BackendApp();
        $this->_groupbuy_mod =& m('groupbuy');
		$this->member_mod =& m('member');
		$this->userpriv_mod =& m('userpriv');
		$this->money_mod =& m('my_money');
		$this->store_mod =& m('store');
		$this->_spec_mod =& m('goodsspec');
		$this->accountlog_mod =& m('accountlog');
		$this->zongjine_mod =& m('zongjine');
		$this->my_moneylog_mod =& m('my_moneylog');
		$this->canshu_mod =& m('canshu');
		$this->moneylog_mod =& m('moneylog');
    }

    function index()
    {
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'gb.group_name',
                'equal' => 'LIKE',
                'assoc' => 'AND',
                'name'  => 'group_name',
                'type'  => 'string',
            ),
            array(
                'field' => 'gb.state',
                'name'  => 'type',
                'assoc' => 'AND',
                'handler' => 'groupbuy_state_translator',
            ),
			array(
                'field' => 'grcity',
                'name'  => 'suoshuzhan',
                'equal' => '=',
            ),
        ));
        $page = $this->_get_page(10);
		
		$user=$this->visitor->get('user_name');
/*$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");
*/	//$city=$row_member['city'];

$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row', $priv_row);	
	if($privs=='all')
	{
	 $groupbuys_list = $this->_groupbuy_mod->find(array(
            'conditions' => "1 = 1" . $conditions,
            'join'  => 'belong_store',
            'fields'=> 'this.*,s.store_name',
            'limit' => $page['limit'],
            'order' => 'group_id DESC',
            'count' => true
        ));
	}	
	else
	{
	$groupbuys_list = $this->_groupbuy_mod->find(array(
            'conditions' =>'1=1 and grcity='.$city . $conditions,
            'join'  => 'belong_store',
            'fields'=> 'this.*,s.store_name',
            'limit' => $page['limit'],
            'order' => 'group_id DESC',
            'count' => true
        ));
	
	}
		$city_row=array();
		$result=$this->_groupbuy_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
			$row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$this->assign('result', $result);
		$result=null;
		
        $groupbuys = array();
        if ($ids = array_keys($groupbuys_list))
        {
            $quantity = $this->_groupbuy_mod->db->getAllWithIndex("SELECT group_id, sum(quantity) as quantity FROM ". DB_PREFIX ."groupbuy_log  WHERE group_id " . db_create_in($ids) . "GROUP BY group_id", array('group_id'));
        }
		
        foreach ($groupbuys_list as $key => $val)
        {
            $groupbuys[$key] = $val;
            $groupbuys[$key]['count'] = empty($quantity[$key]['quantity']) ? 0 : $quantity[$key]['quantity'];
			$groupbuys[$key]['city_name'] = $city_row[$val['grcity']];	
        }
		
		
        $page['item_count'] = $this->_groupbuy_mod->getCount();
        $this->_format_page($page);
        $this->assign('types', array(
            'all'       => Lang::get('group_all'),
            'pending'   => Lang::get('group_pending'),
            'on'        => Lang::get('group_on'),
            'end'       => Lang::get('group_end'),
            'finished'  => Lang::get('group_finished'),
            'canceled'  => Lang::get('group_canceled')
        ));
        $this->import_resource(array(
            'script' => 'inline_edit.js',
        ));
        $this->assign('type', $_GET['type']);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);   //将分页信息传递给视图，用于形成分页条
        $this->assign('groupbuys', $groupbuys);
        $this->display('groupbuy.index.html');
    }

    function recommended()
    {
        $id = trim($_GET['id']);
        $ids = explode(',', $id);
        $this->_groupbuy_mod->edit(db_create_in($ids, 'group_id') . ' AND state = ' . GROUP_ON, array('recommended' => 1));
        if ($this->_groupbuy_mod->has_error())
        {
            $this->show_warning($this->_groupbuy_mod->get_error());
            exit;
        }
        $this->show_warning('recommended_success', 'back_list' , 'index.php?app=groupbuy');
    }
	
	function wei_shenhe()
	{
	$user=$this->visitor->get('user_name');
	/*$row_member=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");*/
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$page = $this->_get_page();	
	$deng=Lang::get('dengdaishenhe');
	if($privs=="all")
	{
	$groups = $this->_groupbuy_mod->find(array(
        'conditions' => "status=0",
            'join'  => 'belong_store',
           'fields'=> 'this.*,s.store_name',
            'limit' => $page['limit'],
            'order' => 'gb.group_id DESC',
            'count' => true
        ));
		}
		else
		{
		$groups = $this->_groupbuy_mod->find(array(
        'conditions' => "status=0 and grcity='$city'",
            'join'  => 'belong_store',
           'fields'=> 'this.*,s.store_name',
            'limit' => $page['limit'],
            'order' => 'gb.group_id DESC',
            'count' => true
        ));
		}
		$city_row=array();
		$result=$this->_groupbuy_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($groups as $key => $val)
        {
			$groups[$key]['city_name'] = $city_row[$val['grcity']];	
        }
		
	    $page['item_count'] = $this->_groupbuy_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('groups', $groups);//传递到风格里
        $this->display('wei_shenhe.html'); 
	   return;
	
	}

    function drop()
    {
        $id = trim($_GET['id']);
        $ids = explode(',', $id);
        if (empty($ids))
        {
            $this->show_warning("no_valid_data");
            exit;
        }
        $this->_groupbuy_mod->drop(db_create_in($ids, 'group_id'));
        if ($this->_groupbuy_mod->has_error())
        {
            $this->show_warning($this->_groupbuy_mod->get_error());
            exit;
        }
        $this->show_warning('drop_success',
            'back_list' , 'index.php?app=groupbuy');
    }

   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

       if (in_array($column ,array('recommended')))
       {
           $data[$column] = $value;
           if($this->_groupbuy_mod->edit("group_id = " . $id . " AND state = " . GROUP_ON, $data))
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

   function shenhe()
    {
	$this->message_mod=& m('message');
	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);//团购编号
	//echo $id;
	$store_id = empty($_GET['store_id']) ? 0 : intval($_GET['store_id']);
	$store_name = empty($_GET['store_name']) ? null : trim($_GET['store_name']);
	//echo $store_name;
	$status=trim($_POST['status']);
	$user_row=$this->member_mod->getRow("select user_name,city from ".DB_PREFIX."member where user_id='$store_id' limit 1");	
    $user_name=$user_row['user_name'];
	$city=$user_row['city'];
	$canshu_row=$this->canshu_mod->getRow("select tg_baozhengjin,tg_fei from ".DB_PREFIX."canshu");	
   $baozhengjin=$canshu_row['tg_baozhengjin'];//保证金
   $gb_fei=$canshu_row['tg_fei'];//发起团购费用
    $riqi=date('Y-m-d H:i:s');
   $zgb_fei=$baozhengjin+$gb_fei;
$gbuy_row=$this->_groupbuy_mod->getRow("select * from ".DB_PREFIX."groupbuy where group_id='$id' limit 1");	
    $goods_id=$gbuy_row['goods_id'];
	$min_quantity=$gbuy_row['min_quantity'];
	$money_row=$this->money_mod->getRow("select money,money_dj,duihuanjifen,dongjiejifen from ".DB_PREFIX."my_money where user_id='$store_id' limit 1");	
	$money=$money_row['money'];
	$money_dj=$money_row['money_dj'];
	$duihuanjifen=$money_row['duihuanjifen'];
	$dongjiejifen=$money_row['dongjiejifen'];
	$new_money=$money-$baozhengjin-$gb_fei;
	$new_moneydj=$money_dj+$baozhengjin;
	if($_POST)
	{
		$beizhu=Lang::get('faqichenggong');
	//$status=Lang::get('shenhetongguo');
	       $edit_groupbuy=array(
			'status'=>$status,		
			'beizhu'=>$beizhu,																		
    );
	$this->_groupbuy_mod->edit('group_id='.$id,$edit_groupbuy);
	if($status=="1")
	{

	/*if($stock<$min_quantity)//判断成团数是否大于库存数
	{
	$beizhu=Lang::get('bunengfaqi');
	$status=Lang::get('shenhebutongguo');
	$edit_groupbuy=array(
			'status'=>$status,		
			'beizhu'=>$beizhu,																		
    );
	$this->_groupbuy_mod->edit('goods_id='.$goods_id,$edit_groupbuy);
	}*/
	
	//更新团购表
	
	//添加mymoneylog日志
	$log_text =$user_name.Lang::get('faqituangou').$gb_fei.Lang::get('yuan').Lang::get('dongjiele').$baozhengjin.Lang::get('yuan');
	$add_mymoneylog=array(
	'user_id'=>$store_id,
	'user_name'=>$user_name,
	'riqi'=>$riqi,
	'money_dj'=>$baozhengjin,//冻结了保证金
	'money_feiyong'=>'-'.$gb_fei,
	'leixing'=>53,
	'type'=>8,
	'city'=>$city,
	'log_text'=>$log_text,
	'dq_money'=>$new_money,
	'dq_money_dj'=>$new_moneydj,
	'dq_jifen'=>$duihuanjifen,
	'dq_jifen_dj'=>$dongjiejifen,
	);
	//写入日志
    $this->my_moneylog_mod->add($add_mymoneylog);
	
    //	添加moneylog冻结保证金日志
//$beizhu =$user_name.Lang::get('faqituangou').$gb_fei.Lang::get('yuan').Lang::get('dongjiele').$baozhengjin.Lang::get('yuan');
$beizhu=Lang::get('tuangouid').$goods_id;
$addlog=array(
	'money'=>'-'.$baozhengjin,
	'money_dj'=>$baozhengjin,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$store_id,
	'zcity'=>$city,
	'type'=>12,
	's_and_z'=>2,
	'beizhu'=>$beizhu,
	'dq_money'=>$new_money,
	'dq_money_dj'=>$new_moneydj,
	'dq_jifen'=>$duihuanjifen,
	'dq_jifen_dj'=>$dongjiejifen,
);
 $this->moneylog_mod->add($addlog);
	//添加moneylog团购费用
	//$beizhu =$user_name.Lang::get('faqituangou').$gb_fei.Lang::get('yuan').Lang::get('dongjiele').$baozhengjin.Lang::get('yuan');
	$beizhu=Lang::get('tuangouid').$goods_id;
$addlog=array(
	'money'=>'-'.$gb_fei,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$store_id,
	'zcity'=>$city,
	'type'=>13,
	's_and_z'=>2,
	'beizhu'=>$beizhu,
	'dq_money'=>$new_money,
	'dq_money_dj'=>$new_moneydj,
	'dq_jifen'=>$duihuanjifen,
	'dq_jifen_dj'=>$dongjiejifen,
);
 $this->moneylog_mod->add($addlog);
	
	
	//更新用户资金
	
	$edit_money=array(
	'money'=>$new_money,
	'money_dj'=>$new_moneydj,
	);
	$this->money_mod->edit('user_id='.$store_id,$edit_money);

 //更新总账户资金
$this->canshu_mod=& m('canshu');
	$jinbi_row=$this->canshu_mod->getRow("select zong_money,zong_jifen from ".DB_PREFIX."canshu");
	$zong_money=$jinbi_row['zong_money'];
	$zong_jifen=$jinbi_row['zong_jifen'];
    $can_id=1;
	$new_zong_money=$zong_money+$gb_fei;//从总账户加发起团购的费用
	$edit_canshu=array(
	'zong_money'=>$new_zong_money,
	);
	$this->canshu_mod->edit('id='.$can_id,$edit_canshu);



 //accountlog添加团购费用日志
	//$beizhu =Lang::get('shoudao').$user_name.Lang::get('tuangoufeiyong').$gb_fei.Lang::get('yuan');
	$beizhu=Lang::get('tuangouid').$goods_id;
$addfei=array(
	'money'=>'+'.$gb_fei,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$store_id,
	'zcity'=>$city,
	'type'=>13,
	's_and_z'=>1,
	'beizhu'=>$beizhu,
	'dq_money'=>$new_zong_money,
	'dq_jifen'=>$zong_jifen,
);
 $this->accountlog_mod->add($addfei);

	$content=Lang::get('tuangoushenqing');
	$content=str_replace('{1}',$user_name,$content);		
	$add_notice1=array(
	'from_id'=>0,
	'to_id'=>$store_id,
	'content'=>$content,  
	'add_time'=>gmtime(),
	'last_update'=>gmtime(),
	'new'=>1,
	'parent_id'=>0,
	'status'=>3,
	);
	$this->message_mod->add($add_notice1);
  }
  if($status=="2")
  {
  	$content=Lang::get('tuangoushenqingbu');
	$content=str_replace('{1}',$user_name,$content);		
	$add_notice1=array(
	'from_id'=>0,
	'to_id'=>$store_id,
	'content'=>$content,  
	'add_time'=>gmtime(),
	'last_update'=>gmtime(),
	'new'=>1,
	'parent_id'=>0,
	'status'=>3,
	);
	$this->message_mod->add($add_notice1);
  }
		 $this->show_message('shenhechenggong',
    'fanhui',    'index.php?app=groupbuy');
	
	}
	else
	{
	
	    $logs_data=$this->_groupbuy_mod->find('group_id='.$id);
		
	   $store=$this->store_mod->getAll("select store_name from ".DB_PREFIX."store where store_id='$store_id'");	
		$this->assign('log', $logs_data);
		$this->assign('store', $store);
        $this->display('shenhe.html');
	    return;

	}
   
}
function wancheng()
{

$user=$this->visitor->get('user_name');
	
	//$city=$row_member['city'];
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$page = $this->_get_page();	
	//$status=Lang::get('dengdaishenhe');
	if($privs=="all")
	{
	$wan = $this->_groupbuy_mod->find(array(
          'conditions' => 'gb.state=3',
            'join'  => 'belong_store',
            'fields'=> 'this.*,s.store_name',
            'limit' => $page['limit'],
            'order' => 'gb.group_id DESC',
            'count' => true
        ));
		}
		else
		{
		$wan = $this->_groupbuy_mod->find(array(
          'conditions' => 'gb.state=3 and grcity='.$city,
            'join'  => 'belong_store',
            'fields'=> 'this.*,s.store_name',
            'limit' => $page['limit'],
            'order' => 'gb.group_id DESC',
            'count' => true
        ));
		}
		$city_row=array();
		$result=$this->_groupbuy_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$result=null;
		 foreach ($wan as $key => $val)
        {
			$wan[$key]['city_name'] = $city_row[$val['grcity']];	
        }
		
	    $page['item_count'] = $this->_groupbuy_mod->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('wan', $wan);//传递到风格里
        $this->display('tg_wancheng.html'); 
	   return;
	   }
	 function jiedong()
	   {
	   $id = empty($_GET['id']) ? 0 : intval($_GET['id']);//团购编号
	   $store_id = empty($_GET['store_id']) ? 0 : intval($_GET['store_id']);
	   $user_row=$this->member_mod->getRow("select user_name,city from ".DB_PREFIX."member where user_id='$store_id' limit 1");	
    $user_name=$user_row['user_name'];
	$city=$user_row['city'];
    $riqi=date('Y-m-d H:i:s');
   $zgb_fei=$baozhengjin+gb_fei;
$gbuy_row=$this->_groupbuy_mod->getRow("select * from ".DB_PREFIX."groupbuy where group_id='$id' limit 1");	
    $goods_id=$gbuy_row['goods_id'];
	$money_row=$this->money_mod->getRow("select money,money_dj,duihuanjifen,dongjiejifen from ".DB_PREFIX."my_money where user_id='$store_id' limit 1");	
	$money=$money_row['money'];
	$money_dj=$money_row['money_dj'];
	$duihuanjifen=$money_row['duihuanjifen'];
	$dongjiejifen=$money_row['dongjiejifen'];
	
	   if($_POST)
	   {
	   $baozhengjin=trim($_POST['baozhengjin']);
	   $jiedong=trim($_POST['jiedong']);
	   if($jiedong=="yes")
	   {
	   //更新团购表
	
	$edit_groupbuy=array(
			'jiedong'=>$jiedong,		
																		
    );
	$this->_groupbuy_mod->edit('group_id='.$id,$edit_groupbuy);   
	   
	   //更新用户资金
	$new_djmoney=$money_dj-$baozhengjin;
	$new_money=$money+$baozhengjin;
	 $edit_money=array(
	'money'=>$new_money,
	'money_dj'=>$new_djmoney,
	);
	$this->money_mod->edit('user_id='.$store_id,$edit_money);
	   
	   //添加mymoneylog日志
	   $log_text =$user_name.Lang::get('shoudaolejiedong').$baozhengjin.Lang::get('yuan');
	
	$add_mymoneylog=array(
	'user_id'=>$store_id,
	'user_name'=>$user_name,
	'riqi'=>$riqi,
	'money_dj'=>'-'.$baozhengjin,
	'money'=>$baozhengjin,
	'leixing'=>53,
	'city'=>$city,
	'log_text'=>$log_text,
	'dq_money'=>$new_money,
	'dq_money_dj'=>$new_djmoney,
	'dq_jifen'=>$duihuanjifen,
	'dq_jifen_dj'=>$dongjiejifen,
	);
	//写入日志
    $this->my_moneylog_mod->add($add_mymoneylog);
//添加moneylog日志
//$beizhu =$user_name.Lang::get('shoudaolejiedong').$baozhengjin.Lang::get('yuan');
$beizhu=Lang::get('tuangouid').$goods_id;
$addlog=array(
	'money'=>$baozhengjin,
	'money_dj'=>'-'.$baozhengjin,
	'time'=>$riqi,
	'user_name'=>$user_name,
	'user_id'=>$store_id,
	'zcity'=>$city,
	'type'=>14,
	's_and_z'=>1,
	'beizhu'=>$beizhu,
	'dq_money'=>$new_money,
	'dq_money_dj'=>$new_djmoney,
	'dq_jifen'=>$duihuanjifen,
	'dq_jifen_dj'=>$dongjiejifen,
);
 $this->moneylog_mod->add($addlog);
  }

	   
	  $this->show_message('caozuochenggong',
    'fanhui',    'index.php?app=groupbuy');  
	   
	   
	   }
	   else
	   {
	    $logs_data=$this->_groupbuy_mod->find('group_id='.$id);
	   $store=$this->store_mod->getAll("select store_name from ".DB_PREFIX."store where store_id='$store_id'");	
	   $canshu=$this->canshu_mod->getAll("select tg_baozhengjin from ".DB_PREFIX."canshu");	
		$this->assign('log', $logs_data);
		$this->assign('store', $store);
		$this->assign('canshu', $canshu);
        $this->display('tg_jiedong.html');
	    return;
}
	   
	   
	   }
	 function tg_fei()
	 {
	 $log_id=1;
	$tg_fei=trim($_POST['tg_fei']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'tg_fei'=>$tg_fei,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('caozuochenggong',
    'fanhui',    'index.php?module=my_money');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('tg_fei.html');
	    return;
	}
	 }  
	 
	 function tg_baozhengjin()
	 {
	 $log_id=1;
	$tg_baozhengjin=trim($_POST['tg_baozhengjin']);
	
	if($_POST)
	{
	$edit_canshu=array(
			'tg_baozhengjin'=>$tg_baozhengjin,																				
    );
	$this->canshu_mod->edit('id='.$log_id,$edit_canshu);
    $this->show_message('caozuochenggong',
    'fanhui',    'index.php?module=my_money');
	}
	else
	{
	    $logs_data=$this->canshu_mod->find('id='.$log_id);
		$this->assign('log', $logs_data);
        $this->display('tg_baozhengjin.html');
	    return;
	}
	 }  
	   
}


?>