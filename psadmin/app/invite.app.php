<?php


/* 会员控制器 */
class InviteApp extends BackendApp
{

    var $_user_mod;
	var $_city_mod;

    function __construct()
    {
        $this->InviteApp();
    }

    function InviteApp()
    {
        parent::__construct();
        $this->_user_mod =& m('member');
		$this->_city_mod =& m('city');
		$this->userpriv_mod =& m('userpriv');
		$this->qiandao_mod =& m('qiandao');
		$this->jiekuan_mod =& m('jiekuan');
    }

    function index()
    {
	
	$user=$this->visitor->get('user_name');
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	/*$row_member=$this->_user_mod->getRow("select * from ".DB_PREFIX."member where user_name = '$user'");*/
	$user_id=$row_member['user_id'];
	
	$yaoqing = trim($_GET['yaoqing']);
	if($yaoqing!="")
	{
		$conditions=" and yaoqing_id='$yaoqing'";
	}
	$suoshuzhan = trim($_GET['suoshuzhan']);
	
        $page = $this->_get_page();	
		if($privs=='all')
		{	
		$index=$this->_user_mod->find(array(
	        'conditions' => "yaoqing_id!=''" . $conditions,
            'limit' => $page['limit'],
			'order' => "user_id desc",
			'count' => true));
			}
			else
			{
			$index=$this->_user_mod->find(array(
	         'conditions' => 'yaoqing_id!="" and city='.$city . $conditions,
            'limit' => $page['limit'],
			'order' => "user_id desc",
			'count' => true));
			}
			$city_row=array();
		$result=$this->_user_mod->getAll("select * from ".DB_PREFIX."city");
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
			
		$page['item_count'] = $this->_user_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
	
        $this->assign('index', $index);//传递到风格里

        $this->display('invite.index.html');
    }

   function rongyu()
   {
	   
	$user=$this->visitor->get('user_name');
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row', $priv_row);
	$username = trim($_GET['username']);
	$conditions='1=1';
	$cond='1=1';
	if($username!="")
	{
		$conditions.=" and m.user_name like '%$username%'";
	}
	$suoshuzhan = trim($_GET['suoshuzhan']);
	$paixu = trim($_GET['paixu']);
	$this->assign('username', $username);
	$this->assign('suoshuzhan', $suoshuzhan);
	$this->assign('paixu', $paixu);
	if($suoshuzhan!="")
	{
		
			$conditions.=" and m.city='$suoshuzhan'";
		
			
	}
	if($paixu!="")
	{
		$cond=" $paixu";
	}
	else
	{
		$cond="id desc";
	}
        $page = $this->_get_page();	
		if($privs=='all')
		{	
		
		 	$index=$this->qiandao_mod->getAll("SELECT q.*,m.user_name,m.city " .
                    "FROM " . DB_PREFIX . "qiandao AS q " .
                    "   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
					" where " . $conditions .
					" order by  " . $cond .
                    " LIMIT {$page['limit']} "
					);	
		}
		else
		{
			 $index=$this->qiandao_mod->getAll("SELECT q.*,m.* " .
                    " FROM " . DB_PREFIX . "qiandao AS q " .
                    "   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
                    " where ". $conditions . " and m.city='$city' " .
					" order by " . $cond .
                    " LIMIT {$page['limit']} "
					
					);	
		}
		//print_r($index);
		$city_row=array();
		$result=$this->qiandao_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		  $row=explode('-',$var['city_name']);
		  $city_row[$var['city_id']]=$row[0];
		}
		$this->assign('result', $result);
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }	
		
		if($privs=='all')
		{	
		 $qian=$this->qiandao_mod->getAll("SELECT q.*,m.user_name,m.city " .
                    "FROM " . DB_PREFIX . "qiandao AS q " .
                    "   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id ".
					" where " . $conditions 
					
					);	
		}
		else
		{
			 $qian=$this->qiandao_mod->getAll("SELECT q.*,m.user_name,m.city " .
                    "FROM " . DB_PREFIX . "qiandao AS q " .
                    "   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
                    "where ".$conditions." and m.city='$city' " 
					
					);	
		}
		
		
			
		$page['item_count'] = count($qian);
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
	
        $this->assign('index', $index);//传递到风格里

        $this->display('rongyu.index.html');
	   }
	function jifenliushui()
	{
		   	$user=$this->visitor->get('user_name');
			$userid=$this->visitor->get('user_id');
			$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
			$privs=$priv_row['privs'];
			$city=$priv_row['city'];
			$this->assign('priv_row', $priv_row);
			$page = $this->_get_page();	
			if($privs=='all')
			{	
		
		 	$index=$this->qiandao_mod->getAll("SELECT * " .
                    " FROM " . DB_PREFIX . "qiandao_log AS q " .
					//" where " . $conditions .
					" order by  q.riqi desc" .
                    " LIMIT {$page['limit']} "
					);	
			}
			else
			{
			 $index=$this->qiandao_mod->getAll("SELECT q.* " .
                    " FROM " . DB_PREFIX . "qiandao_log AS q " .
                    //"   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
                    " where  q.city='$city' " .
					" order by  q.riqi desc" .
                    " LIMIT {$page['limit']} "
					
					);	
			}
		
		if($privs=='all')
			{	
		
		 	$qian=$this->qiandao_mod->getAll("SELECT * " .
                    " FROM " . DB_PREFIX . "qiandao_log AS q " .
					//" where " . $conditions .
					" order by  q.riqi desc" 
                    
					);	
			}
			else
			{
			 $qian=$this->qiandao_mod->getAll("SELECT q.* " .
                    " FROM " . DB_PREFIX . "qiandao_log AS q " .
                    //"   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
                    " where  q.city='$city' " .
					" order by  q.riqi desc" 
                   
					
					);	
			}
		
		
		
			
			$city_row=array();
			$result=$this->qiandao_mod->getAll("select * from ".DB_PREFIX."city");
			foreach ($result as $var )
			{
			  $row=explode('-',$var['city_name']);
		      $city_row[$var['city_id']]=$row[0];
			}
			$this->assign('result', $result);
			$result=null;
			 foreach ($index as $key => $val)
			{
				$index[$key]['city_name'] = $city_row[$val['city']];	
			}
			$page['item_count'] = count($qian);
			
        	$this->_format_page($page);
        	$this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        	$this->assign('page_info', $page);
	
        	$this->assign('index', $index);//传递到风格里

        	$this->display('jifenliushui.index.html');
				
	}
	
	
	

   function jiekuan()
   {  
	$user=$this->visitor->get('user_name');
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row', $priv_row);
	$username = trim($_GET['username']);
	$riqi=date('Y-m-d');
	$this->assign('riqi',$riqi);
	
	
	$conditions='1=1';
	$cond='1=1';
	if($username!="")
	{
		$conditions.=" and user_name like '%$username%'";
	}
	$suoshuzhan = trim($_GET['suoshuzhan']);
	$paixu = trim($_GET['paixu']);
	$this->assign('username', $username);
	$this->assign('suoshuzhan', $suoshuzhan);
	$this->assign('paixu', $paixu);
	if($suoshuzhan!="")
	{
		
			$conditions.=" and city='$suoshuzhan'";
		
			
	}
	if($paixu!="")
	{
		$cond=" $paixu";
	}
	else
	{
		$cond="id desc";
	}
        $page = $this->_get_page();	
		if($privs=='all')
		{	
		
		 	$index=$this->qiandao_mod->getAll("SELECT jk.* " .
                    "FROM " . DB_PREFIX . "jiekuan AS jk " .
                   // "   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
					" where " . $conditions . " and status!=1 ".
					" order by  " . $cond .
                    " LIMIT {$page['limit']} "
					);	
		}
		else
		{
			
			 $index=$this->qiandao_mod->getAll("SELECT * " .
                    " FROM " . DB_PREFIX . "jiekuan AS jk " .
                    //"   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
                    " where ". $conditions . " and city='$city' and jk.status!=1 " .
					" order by " . $cond .
                    " LIMIT {$page['limit']} "
					
					);	
					
		}
		//print_r($index);
		$city_row=array();
		$result=$this->qiandao_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		  $row=explode('-',$var['city_name']);
		  $city_row[$var['city_id']]=$row[0];
		}
		$this->assign('result', $result);
		$result=null;
		foreach ($index as $key => $val)
        {
		     $index[$key]['city_name'] = $city_row[$val['city']];
			 $index[$key]['start_time1']=substr($val['start_time'],0,10);
  			 $index[$key]['daoqi_time']=substr($val['jieshu_time'],0,10);
			 $index[$key]['htid']=gethtnum($val['id']);
			 $index[$key]['yh']=$val['money_j']-$val['money_j']*1/10;
			 if($val['status1']==2)
			 {
				$index[$key]['faxi']=$val['money_h']-$index[$key]['yh'];	 
			 }
			 else if($val['status']==2)
			 {
				$index[$key]['faxi']=$this->qiandao_mod->lixi($val['money_j'],$val['rate'],$val['jieshu_time']); 
			 }

		}	
		
		if($privs=='all')
		{	
		 $qian=$this->qiandao_mod->getAll("SELECT jk.* " .
                    "FROM " . DB_PREFIX . "jiekuan AS jk " .
                    //"   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id ".
					" where " . $conditions ." and status!=1 "
					
					);	
		}
		else
		{
			 $qian=$this->qiandao_mod->getAll("SELECT jk.* " .
                    "FROM " . DB_PREFIX . "jiekuan AS jk " .
                    //"   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
                    "where ".$conditions." and city='$city' and status!=1 " 
					
					);	
		}
		$page['item_count'] = count($qian);
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
	
        $this->assign('index', $index);//传递到风格里

        $this->display('jiekuan.index.html');
	   }
	   
   function jk_weishenhe()
   {  
	$user=$this->visitor->get('user_name');
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getRow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0 limit 1");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row', $priv_row);
	$username = trim($_GET['username']);
	$conditions='1=1';
	$cond='1=1';
	if($username!="")
	{
		$conditions.=" and user_name like '%$username%'";
	}
	$suoshuzhan = trim($_GET['suoshuzhan']);
	$paixu = trim($_GET['paixu']);
	$this->assign('username', $username);
	$this->assign('suoshuzhan', $suoshuzhan);
	$this->assign('paixu', $paixu);
	if($suoshuzhan!="")
	{
		
			$conditions.=" and city='$suoshuzhan'";
		
			
	}
	if($paixu!="")
	{
		$cond=" $paixu";
	}
	else
	{
		$cond="id desc";
	}
        $page = $this->_get_page();	
		if($privs=='all')
		{	
		
		 	$index=$this->qiandao_mod->getAll("SELECT jk.* " .
                    "FROM " . DB_PREFIX . "jiekuan AS jk " .
                   // "   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
					" where " . $conditions . " and status=1 ".
					" order by  " . $cond .
                    " LIMIT {$page['limit']} "
					);	
		}
		else
		{
			 $index=$this->qiandao_mod->getAll("SELECT jk.* " .
                    " FROM " . DB_PREFIX . "jiekuan AS jk " .
                    //"   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
                    " where ". $conditions . " and city='$city' and status=1 " .
					" order by " . $cond .
                    " LIMIT {$page['limit']} "
					
					);	
		}
		//print_r($index);
		$city_row=array();
		$result=$this->qiandao_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
		    $row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$this->assign('result', $result);
		$result=null;
		 foreach ($index as $key => $val)
        {
			$index[$key]['city_name'] = $city_row[$val['city']];	
        }	
		
		if($privs=='all')
		{	
		 $qian=$this->qiandao_mod->getAll("SELECT jk.* " .
                    "FROM " . DB_PREFIX . "jiekuan AS jk " .
                    //"   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id ".
					" where " . $conditions ." and status=1 "
					
					);	
		}
		else
		{
			 $qian=$this->qiandao_mod->getAll("SELECT jk.* " .
                    "FROM " . DB_PREFIX . "jiekuan AS jk " .
                    //"   LEFT JOIN " . DB_PREFIX . "member AS m ON q.user_id = m.user_id " .
                    "where ".$conditions." and city='$city' and status=1 " 
					
					);	
		}
		
		$page['item_count'] = count($qian);
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        $this->assign('index', $index);//传递到风格里
        $this->display('jiekuan_weishenhe.html');
	   }
	   
	function jk_shenhe()
	{
	$this->message_mod=& m('message'); 	
	$this->moneylog_mod=& m('moneylog');
	$this->accountlog_mod=& m('accountlog');
	$this->canshu_mod=& m('canshu');
	$this->my_money_mod=& m('my_money');
	$id = empty($_REQUEST['id']) ? null : trim($_REQUEST['id']);
	$bianhao=gethtnum($id);
	$find_data     = $this->jiekuan_mod->find($id);
	$jie   =   current($find_data);
	$money_j=$jie['money_j'];
	$this->assign('jie', $jie);
	$status=trim($_POST['status']);
	$user_id=$jie['user_id'];
	
	$riqi=date('Y-m-d H:i:s'); 
	$ber=$this->message_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");
	
	if($jie['isday']==1)
		{
			$jieshu_time=time($riqi)+3600*24*$jie['time'];
			$jieshu_time=date("Y-m-d H:i:s",$jieshu_time);			
		}
		else
		{
			$jieshu_time=time($riqi)+3600*24*$jie['time']*30;
			$jieshu_time=date("Y-m-d H:i:s",strtotime("+".$jie['time'].'months',strtotime($riqi)));
			
		}
 
	
	$canshu=$this->jiekuan_mod->can();
	//$userid=$jie['user_id'];
	$user_name=$jie['user_name'];
	$my_money=$this->message_mod->getAll("select * from ".DB_PREFIX."my_money where user_id='$user_id'");
	
		foreach ($my_money as $key=>$my)
		{
			$my_money[$key]['zengjin']=round($my['zengjin']/$canshu['jifenxianjin'],2);
			$dengji=$my['t'];
			$le=dengji($dengji);
			$jk=jiekuan($dengji);
		}
		
		  $this->assign('my_money', $my_money); 
		  $this->assign('jk', $jk); 
	$j=$this->message_mod->getAll("select * from ".DB_PREFIX."jiekuan where user_id='$user_id'");
	$count=count($j);
	$this->assign('count',$count);
	                          
	if($_POST)
	{
	if($status==2)//审核通过
	{
		$zong_money=$canshu['zong_money'];
		$zong_jifen=$canshu['zong_jifen'];
		if($zong_money<$money_j)
		{
			$this->show_warning('bunengjiekuan');
			return;
		}
		
	$data=array('status'=>$status,'start_time'=>$riqi,'status1'=>1,'jieshu_time'=>$jieshu_time);
	$result=$this->jiekuan_mod->getRow("select * from ".DB_PREFIX."my_money where user_id = '$user_id' limit 1");
	$city=$result['city'];
	$money=$result['money'];
	$money_dj=$result['money_dj'];
	$duihuanjifen=$result['duihuanjifen'];
	$dongjiejifen=$result['dongjiejifen'];
	
	
	$moneydj=$money_j*1/10;//冻结借款金额的1/10
	$new_money_dj=$money_dj+$moneydj;
	
	$lixi=$jie['lixi'];//借款利息
	$shiji_money=$money_j-$lixi-$moneydj;//实际到账金额
	$benxi=$money_j+$lixi;//本息合计
	$new_money=$money+$shiji_money;
	
	
	$beizhu=Lang::get('jiekuantong');	
	$beizhu=str_replace('{1}',$user_name,$beizhu);
	$beizhu=str_replace('{2}',$money_j,$beizhu);
	$beizhu=str_replace('{3}',$lixi,$beizhu);
	$beizhu=str_replace('{4}',$moneydj,$beizhu);
	
	$text=Lang::get('jiekuantongguo');
	$text=str_replace('{1}',$money_j,$text);
	$text=str_replace('{2}',$lixi,$text);
	$text=str_replace('{3}',$moneydj,$text);
	
	
	//添加用户资金流水	 
		$addlog=array(
			'money'=>$shiji_money,
			'money_dj'=>$moneydj,
			'time'=>$riqi,
			'user_name'=>$user_name,
			'user_id'=>$user_id,
			'zcity'=>$city,
			'type'=>41,
			's_and_z'=>1,
			'beizhu'=>$text,
			'dq_money'=>$new_money,
			'dq_money_dj'=>$new_money_dj,
			'dq_jifen'=>$duihuanjifen,
			'dq_jifen_dj'=>$dongjiejifen
		);
		 $this->moneylog_mod->add($addlog);	 
	
	//$acc_money=$money_j-$lixi;//从平台实际扣除的金额给用户
	$new_zong_money=$zong_money-$acc_money;
	//添加总账户资金流水
		$addaccoun=array(
			'money'=>'-'.$shiji_money,
			'time'=>$riqi,
			'user_name'=>$user_name,
			'user_id'=>$user_id,
			'zcity'=>$city,
			'type'=>41,
			's_and_z'=>2,
			//'beizhu'=>$beizhu,
			'dq_money'=>$new_zong_money,
			'dq_jifen'=>$zong_jifen,
		);
		 $this->accountlog_mod->add($addaccoun);
		
		 $this->my_money_mod->edit('user_id='.$user_id,array('money'=>$new_money,'money_dj'=>$new_money_dj));
		$this->canshu_mod->edit('id=1',array('zong_money'=>$new_zong_money));
		
		
		$str=file_get_contents('../data/jiekuanhetong/333.xml');
		//$str=iconv('UTF-8','GBK',$str);
		$str=str_replace('{1}',$bianhao,$str);
		$str=str_replace('{2}',$riqi,$str);
		$name=$jie['name'];
		$name=gb23122uni($name);
		$str=str_replace('{3}',$name,$str);
		$user_name=gb23122uni($user_name);
		$str=str_replace('{4}',$user_name,$str);
		$str=str_replace('{5}',$ber['owner_card'],$str);
		$str=str_replace('{6}',$jie['lxfs1'],$str);
		$addre=gb23122uni($jie['address']);
		$str=str_replace('{7}',$addre,$str);
		$bei=gb23122uni($jie['beizhu']);
		$str=str_replace('{8}',$bei,$str);
		$str=str_replace('{9}',$jie['money_j'],$str);
		if($jie['isday']==1)
		{
			$tian=Lang::get('tian');
			$tian=gb23122uni($tian);
			$qixian=$jie['time'].$tian;
		}
		else
		{
			$yue=Lang::get('yue');
			$yue=gb23122uni($yue);
			$qixian=$jie['time'].$yue;
		}
		$str=str_replace('{10}',$qixian,$str);
		$str=str_replace('{11}',$jie['rate'],$str);
		$str=str_replace('{12}',$riqi,$str);
		$str=str_replace('{13}',$benxi,$str);
		$fp = fopen('../data/jiekuanhetong/psht/'.$bianhao.'.wps', 'w');
		fwrite($fp, $str);
		fclose($fp);

	}
	if($status==3)//审核不通过
	{
	$beizhu=Lang::get('jiekuantongzhi');	
	$beizhu=str_replace('{1}',$user_name,$beizhu);
	$data=array('status'=>$status,'start_time'=>$riqi,'status1'=>3);
	}
			$add_notice=array(
					'from_id'=>0,
					'to_id'=>$user_id,
					'content'=>$beizhu,  
					'add_time'=>gmtime(),
					'last_update'=>gmtime(),
					'new'=>1,
					'parent_id'=>0,
					'status'=>3,
					);
					
					$this->message_mod->add($add_notice);	
	
   $this->jiekuan_mod->edit('id='.$id,$data);
	
	 $this->show_message('shenhechenggong',
    'fanhui',    'index.php?app=invite&act=jiekuan');
	}
     else
	{
        $this->display('jk_shenhe.html');
	    return;
	}
	}
	   
	function jk_xiangqing()
	{
		$this->jiekuan_mod=& m('jiekuan');
		$id = empty($_GET['id']) ? null : trim($_GET['id']);
		$jie=$this->jiekuan_mod->getRow("select * from ".DB_PREFIX."jiekuan where id = '$id' limit 1");
	$riqi=date('Y-m-d');	
	$this->assign('riqi',$riqi);	 
 $jie['start_time1']=substr($jie['start_time'],0,10);
 $jie['daoqi_time']=substr($jie['jieshu_time'],0,10);
 $jie['yh']=$jie['money_j']-$jie['money_j']*1/10;
  			if($jie['status1']==2)
			 {
				$jie['faxi']=$jie['money_h']-$jie['yh'];	 
			 }
			 else if($jie['status']==2)
			 {
				$jie['faxi']=$this->jiekuan_mod->lixi($jie['money_j'],$jie['rate'],$jie['jieshu_time']); 
			 }
		
	$jie['dai']=$jie['money_j']-$jie['money_j']*1/10-$jie['money_h'];
		if($jie['dai']<0)
		{
			$jie['dai_money']=0;
		}
		else
		{
			$jie['dai_money']=$jie['dai'];
		}
		$jie['z_money']=$jie['lixi']+$jie['money_j'];
		$userid=$jie['user_id'];
		$jk=$this->jiekuan_mod->getAll("select * from ".DB_PREFIX."jiekuan where user_id = '$userid'");
		
		$count=count($jk);
	$canshu=$this->jiekuan_mod->can();
	$my_money=$this->jiekuan_mod->getRow("select * from ".DB_PREFIX."my_money where user_id=$userid limit 1");
	
			$my_money['zengjin']=round($my_money['zengjin']/$canshu['jifenxianjin'],2);
			$dengji=$my_money['t'];
			$le=dengji($dengji);
			$jkuan=jiekuan($dengji);
		
	      $this->assign('my_money', $my_money);
		  $this->assign('jkuan', $jkuan); 
		
		
		
		$this->assign('count',$count);
		$this->assign('jie',$jie);
        $this->display('jk_xiangqing.html');	
	}
	
	function qiangzhi_huankuan()
	{
		$this->jiekuan_mod=& m('jiekuan');
		$this->moneylog_mod=& m('moneylog');
		$this->accountlog_mod=& m('accountlog');
		$this->canshu_mod=& m('canshu');
		$this->my_money_mod=& m('my_money');
		
		$id = empty($_GET['id']) ? null : trim($_GET['id']);
		$userid = empty($_GET['userid']) ? null : trim($_GET['userid']);

	    $jie=$this->jiekuan_mod->getRow("select * from ".DB_PREFIX."jiekuan where id = '$id' limit 1");
			 $money_yh=$jie['money_h'];//已还金额
			 $money_j=$jie['money_j'];//借款金额
			 $result=$this->jiekuan_mod->getRow("select * from ".DB_PREFIX."my_money where user_id = '$userid' limit 1");
			 $city=$result['city'];
			 $user_name=$result['user_name'];
			 $riqi=date('Y-m-d H:i:s');
			 $money=$result['money'];
			 $money_dj=$result['money_dj'];
			 $duihuanjifen=$result['duihuanjifen'];
			 $dongjiejifen=$result['dongjiejifen'];
 			 $suoding_jifen=$result['suoding_jifen'];
			 
	 if($result['money']>0)
	 {	
	 $rate=$jie['rate'];	 
	 $zong_lixi=$this->jiekuan_mod->lixi($money_j,$rate,$jie['jieshu_time']);//逾期利息
	 $yinghuan_money=$money_j-$money_j*1/10-$money_yh+$zong_lixi;//应还金额
	 $riqi=date('Y-m-d H:i:s');
	 if($money>=$yinghuan_money)//可以还清
	 {
		$shiji_moneyh=$yinghuan_money;
		$new_moneyh=$money_yh+$shiji_moneyh;//已还款总额
		
		$data=array('money_h'=>$new_moneyh,'end_time'=>$riqi,'status1'=>2,'faxi'=>$zonglixi,'is_suoding'=>0);
		$new_moneydj=$money_dj-$money_j*1/10;
		$jie_money=$money_j*1/10;
		
		if($jie['is_suoding']==1)
		{
			$text=Lang::get('jiechusuo');
			$text=str_replace('{1}',$shiji_moneyh,$text);
			$text=str_replace('{2}',$jie_money,$text);
			$new_suodingjifen=$suoding_jifen-100000;
		}
		else
		{
			$text=Lang::get('jiedongbaozheng');
			$text=str_replace('{1}',$shiji_moneyh,$text);
			$text=str_replace('{2}',$jie_money,$text);
			$new_suodingjifen=$suoding_jifen;
		}
		
	 }
	 else
	 {
		$shiji_moneyh=$money;		
		$new_moneyh=$money_yh+$shiji_moneyh;//已还款总额
		$data=array('money_h'=>$new_moneyh,'end_time'=>$riqi,'status1'=>1,'faxi'=>$zonglixi,'is_suoding'=>1);
		$new_moneydj=$money_dj;
		$jie_money=0;
		if($jie['is_suoding']==1)
		{
			$text=Lang::get('jiebaozhengjin');
			$text=str_replace('{1}',$shiji_moneyh,$text);
			$new_suodingjifen=$suoding_jifen;
		}
		else
		{
			$text=Lang::get('jiebaozheng');
			$text=str_replace('{1}',$shiji_moneyh,$text);
			$new_suodingjifen=$suoding_jifen+100000;
		}
		
	 }
	 $this->jiekuan_mod->edit('id='.$jie['id'],$data);
	 $new_money=$money-$shiji_moneyh;//用户账号剩余金额
	 $can=$this->jiekuan_mod->can();
	 $zong_money=$can['zong_money'];
	 $zong_jifen=$can['zong_jifen'];
	 $new_zong_money=$zong_money+$shiji_moneyh;//总账户余额
	
	//添加用户资金流水	 
	$addlog=array(
		'money'=>'-'.$shiji_moneyh,
		'money_dj'=>'-'.$jie_money,
		'time'=>date('Y-m-d H:i:s'),
		'user_name'=>$user_name,
		'user_id'=>$user_id,
		'zcity'=>$city,
		'type'=>42,
		's_and_z'=>2,
		'beizhu'=>$text,
		'dq_money'=>$new_money,
		'dq_money_dj'=>$new_moneydj,
		'dq_jifen'=>$duihuanjifen,
		'dq_jifen_dj'=>$dongjiejifen
	);
	$this->moneylog_mod->add($addlog);	 
	$beizhu=Lang::get('zidong');
	//添加总账户资金流水
	$addaccoun=array(
		'money'=>$shiji_moneyh,
		'time'=>date('Y-m-d H:i:s'),
		'user_name'=>$user_name,
		'user_id'=>$user_id,
		'zcity'=>$city,
		'type'=>42,
		's_and_z'=>1,
		'beizhu'=>$beizhu,
		'dq_money'=>$new_zong_money,
		'dq_jifen'=>$zong_jifen,
	);
	$this->accountlog_mod->add($addaccoun);	 
	$this->my_money_mod->edit('user_id='.$userid,array('money'=>$new_money,'money_dj'=>$new_moneydj,'suoding_jifen'=>$new_suodingjifen));
	$this->canshu_mod->edit('id=1',array('zong_money'=>$new_zong_money));
	
	 }
	 else if($jie['is_suoding']!=1)
	 {
	$new_suodingjifen=$suoding_jifen+100000;
	$this->my_money_mod->edit('user_id='.$userid,array('suoding_jifen'=>$new_suodingjifen));
	$this->jiekuan_mod->edit('id='.$jie['id'],array('is_suoding'=>1));
	 }
	$this->show_message('huankuanchenggong','fan','index.php?app=invite&act=jiekuan');
	
	}	   
function excel()
{
  $savename = date("Y-m-d H:i:s");   
  header("Content-type: application/vnd.ms-excel");
  header("Content-disposition: attachment; filename="  . $savename . ".xls");
  $this->jiekuan_mod=& m('jiekuan');
  $conditions="1=1";
  $nameuser=trim($_GET['username']);
  if($nameuser!="")
  {
	  $conditions.=" and user_name like '%$nameuser%'";
  }
  $suoshuzhan=trim($_GET['suoshuzhan']);
  if($suoshuzhan!="")
  {
	  $conditions.=" and city='$suoshuzhan'";
  }
  $paixu=trim($_GET['paixu']);
  if($paixu!="")
  {
	  $conditions.=" and status1='$paixu'";
  }
  
  $data=$this->jiekuan_mod->getAll("SELECT jk.* " .
		  "FROM " . DB_PREFIX . "jiekuan AS jk " .
		  " where " . $conditions . " and status=2 ".
		  " order by id desc " 
		  );	

  $userid=Lang::get('yonghuid');
  $username=Lang::get('yonghuming');
  $moneyh=Lang::get('yihuanjine');
  $moneyj=Lang::get('jiekuanjine');
  $jklixi=Lang::get('jiekuanlixi');
  $faxi=Lang::get('jiekuanfaxi');
  $yh_money=Lang::get('yinghuanjine');
  $start_time=Lang::get('start_time');
  $end_time=Lang::get('end_time');
  $hk_time=Lang::get('hk_time');
  $jiekuanqixian=Lang::get('jiekuanqixian');
  $suoshuzhan=Lang::get('suoshuzhan');
  $jiekuan_status=Lang::get('jk_status');
  $title=array($userid,$username,$moneyj,$jklixi,$faxi,$yh_money,$moneyh,$jiekuanqixian,$start_time,$end_time,$hk_time,$jiekuan_status,$suoshuzhan,);
			
  if (is_array($title))
  {
  	foreach ($title as $value)
  	{
  		echo $value."\t";
  	}
  }
  echo "\n";
	
 $sep = "\t";   
 if (is_array($data))
 {
  	$city_row=array();
  	$result=$this->jiekuan_mod->getAll("select * from ".DB_PREFIX."city");
  	foreach ($result as $var )
  	{
		$row=explode('-',$var['city_name']);
		$city_row[$var['city_id']]=$row[0];
  	}
 	$result=null;
    foreach ($data as $key=>$row)
	{
	 $yinghuan_money=$row['money_j']-$row['money_j']*1/10;
	 if($row['status1']==2)
	 {
		$jk_faxi=$row['money_h']-$yinghuan_money;	 
	 }
	 else if($val['status']==2)
	 {
		$jkfaxi=$this->jiekuan_mod->lixi($row['money_j'],$row['rate'],$row['jieshu_time']); 
		if($jkfaxi<0)
		{
			$jk_faxi=0;
		}
		else
		{
			$jk_faxi=$jkfaxi;
		}
	 }
			 
	 if($row['isday']==1)
	 {
		 $qixian=$row['time'].Lang::get('tian');
	 }
	 else
	 {
		 $qixian=$row['time'].Lang::get('yue');
	 }
	 $city_name = $city_row[$row['city']];	
			  
	$kaishi_time=substr($row['start_time'],0,10);
	$jieshu_time=substr($row['jieshu_time'],0,10);
	if($row['status1']==1)
	{
		$status=Lang::get('daihuankuan');
	}
	if($row['status1']==2)
	{
		$status=Lang::get('yihuankuan');
	}
	$ex='';
	$ex	.=$row['user_id'].$sep;
	$ex	.=$row['user_name'].$sep;
	$ex	.=$row['money_j'].$sep;
	$ex	.=$row['lixi'].$sep;
	$ex	.=$jk_faxi.$sep;
	$ex	.=$yinghuan_money.$sep;
	$ex	.=$row['money_h'].$sep;
	$ex	.=$qixian.$sep;
	$ex	.=$kaishi_time.$sep;
	$ex	.=$jieshu_time.$sep;
	$ex	.=$row['end_time'].$sep;
	$ex	.=$status.$sep;
	$ex	.=$city_name.$sep;
	//$ex = str_replace($sep."$", "", $ex);       
	$ex .= "\t";       
	echo $ex;  
	echo "\n";
	}
}
				
}


}


function  gb23122uni($text)   
{   
$rtext= " ";   
$max=strlen($text);   
for($i=0;$i <$max;$i++)
{   
$h=ord($text[$i]);   
if($h>=160   &&   $i <$max-1)
{   
$rtext.= "&#".base_convert(bin2hex(iconv( "gb2312", "ucs-2",substr($text,$i,2))),16,10). ";";   
$i++;   
}
else{   
$rtext.=$text[$i];   
}   
}   
return   $rtext;   
}

function exportData($filename,$title,$data){
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename="  . $filename . ".xls");
	if (is_array($title)){
		foreach ($title as $key => $value){
			echo $value."\t";
		}
	}
	echo "\n";
	if (is_array($data)){
		foreach ($data as $key => $value){
			foreach ($value as $_key => $_value){
				echo $_value."\t";
			}
			echo "\n";
		}
	}
}



?>
