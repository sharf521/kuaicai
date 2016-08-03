<?php
class UserApp extends MallbaseApp
{   
	//var $p2purl="http://zz.test.cn";
	var $p2purl="http://www.ilaijin.com";
    function __construct()
    {
        $this->UserApp();
    }
    function UserApp()
    {
        parent::__construct(); 		 
    }
    function index()
    {   
	//loaner8 
		//$user_id = DeCode(3285,'E');
		$user_id = DeCode(3285,'E');
		$user_id='wvmEtJgwZwo8E6Io';
		//echo $user_id;
		$data=array('user_id'=>$user_id);	
		
		
		//$this->i_accountl2m(3285,100);
		//$this->i_accountm2l(3285,1);
		//查询用户在借贷平台的帐户情况
		//http://zhuzhan.cn/index.php?app=user&act=get_p2p_account
		//l2m
		//http://zhuzhan.cn/index.php?app=user&act=i_accountl2m&user_id=wvmEtJgwZwo8E6Io&money=10
		
		$this->i_awardl2m(3285,1);
		
		//echo $this->i_accountm2l(350,100);

		//echo getHTML('http://zhuzhan.cn/index.php?app=user&act=account',$data); 
    }
	
    function account()
    {
		$user_id=$_REQUEST['user_id'];
		$user_id=(int)DeCode($user_id,'D');	
		$member_mod = &m('my_money');
		$result=$member_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");		
		$user_id=$result['user_id'];	
		$row=array();
		$row['user_id']=DeCode($user_id,'E');
		$row['user_name']=urlencode($result['user_name']);
		$row['money']=$result['money'];
		$row['money_dj']=$result['money_dj'];
		$row['jifen']=$result['duihuanjifen'];
		$row['jifen_dj']=$result['dongjiejifen'];			
		$row=json_encode($row);
		echo urldecode($row);
		exit();
    }
	/*
查询用户在借贷平台的帐户情况：
请求链接：http://hndai.p2p.com/index.php?user&q=code/account/i_user_info
传入数据：
user_id:注册时通过web_service生成的用户id
target:操作完成后转向的链接

返回数据：
result：为1时表示操作成功，其它值都表示出现错误
user_id
account_total：帐户余额
account_cash：可提现（可转入商城）金额
award：充值奖励已赚利息（可单向转入商城）
*/
	function get_p2p_account()
	{		
		$user_id = DeCode(3285,'E');
		$data=array('user_id'=>$user_id);
		$res=getHTML($this->p2purl.'/index.php?user&q=code/account/i_user_info',$data);
		$res=json_decode($res,true);
		if($res['result']==1)
		{
			print_r($res);
			return $res;	
		}
		else
		{
			return 0;	
		}
	}

	/*
	2.从借贷平台向商城转出资金：
请求链接：http://hndai.p2p.com/index.php?user&q=code/account/i_accountl2m
传入数据：
user_id:注册时通过web_service生成的用户id
target:操作完成后转向的链接
op_id：商城的交易流水
mall_key：交易流水的加密值
money：操作金额
	*/	
	function i_accountl2m($user_id=0,$money=0)
	{
		if($user_id==0 || $money==0)
		{
			$user_id=$_REQUEST['user_id'];
			$money=(float)$_REQUEST['money'];
		}
		else
		{
			$user_id=DeCode($user_id,'E');
		}
		if($money>0)
		{
			$op_id=date('Ymdhis').rand(100,999);
			$data=array(
				'user_id'=>$user_id,
				'money'=>$money,
				'op_id'=>$op_id,
				'mall_key'=>DeCode($op_id,'E')	
			);
			//echo $this->p2purl.'/index.php?user&q=code/account/i_accountl2m';
			$result=getHTML($this->p2purl.'/index.php?user&q=code/account/i_accountl2m',$data);
			$res=json_decode($result,true);
			if($res['result']==1)
			{
				$user_id=(int)DeCode($user_id,'D');								
				$moneylog_mod =& m('moneylog');				
				$row=$moneylog_mod->getRow("select * from ".DB_PREFIX."my_money where user_id=$user_id limit 1");
				if($row)
				{
					$array_log=array(					
						'jifen'=>0,
						'money'=>$money,
						'time'=>date('Y-m-d H:i:s'),
						'user_name'=>$row['user_name'],
						'user_id'=>$user_id,
						'zcity'=>$row['city'],
						'type'=>110,
						's_and_z'=>1,
						'beizhu'=>'',
						'orderid'=>'',
						'dq_money'=>$row['money']+$money,
						'dq_money_dj'=>$row['money_dj'],
						'dq_jifen'=>$row['duihuanjifen'],
						'dq_jifen_dj'=>$row['dongjiejifen']	
					);			
					$moneylog_mod->add($array_log);//资金流水					
					$sql="update ".DB_PREFIX."my_money set money=money+$money where user_id='$user_id' limit 1";		
					$moneylog_mod->db->query($sql);
				}
				$row=null;
				echo $result;
			}
			else
			{
				echo 0;	
			}
		}
	}


	/*
	从商城向借贷平台转入资金：
请求链接：http://hndai.p2p.com/index.php?user&q=code/account/i_accountm2l
传入数据：
user_id:注册时通过web_service生成的用户id
target:操作完成后转向的链接
op_id：商城的交易流水
mall_key：交易流水的加密值
money：操作金额
	*/
	function i_accountm2l($user_id,$money)
	{
		$money=(float)$money;
		if($money<=0) return 0;
		$op_id=date('Ymdhis').rand(100,999);
		$data=array(
			'user_id'=>DeCode($user_id,'E'),
			'money'=>$money,
			'op_id'=>$op_id,
			'mall_key'=>DeCode($op_id,'E')		
		);
		//验证帐号余额
		$moneylog_mod =& m('moneylog');	
		$row=$moneylog_mod->getRow("select * from ".DB_PREFIX."my_money where user_id=$user_id limit 1");
		if($row)
		{
			if($money>$row['money'])
			{
				return 0;//余额不足	
			}
			else
			{
				$result=getHTML($this->p2purl.'/index.php?user&q=code/account/i_accountm2l',$data);
				$res=json_decode($result,true);
				if($res['result']==1)
				{
					//资金流水
					$array_log=array(					
						'jifen'=>0,
						'money'=>'-'.$money,
						'time'=>date('Y-m-d H:i:s'),
						'user_name'=>$row['user_name'],
						'user_id'=>$user_id,
						'zcity'=>$row['city'],
						'type'=>111,
						's_and_z'=>2,
						'beizhu'=>'',
						'orderid'=>'',
						'dq_money'=>$row['money']-$money,
						'dq_money_dj'=>$row['money_dj'],
						'dq_jifen'=>$row['duihuanjifen'],
						'dq_jifen_dj'=>$row['dongjiejifen']	
					);
					$moneylog_mod->add($array_log);//资金流水					
					$sql="update ".DB_PREFIX."my_money set money=money-$money where user_id='$user_id' limit 1";	
					$moneylog_mod->db->query($sql);
					echo $result;
				}
				else
				{
					echo 0;
				}	
			}
			
		}
		
	}
	
	/*
	4.将充值奖励所赚利息转入商城：
请求链接：http://hndai.p2p.com/index.php?user&q=code/account/i_awardl2m
传入数据：
user_id:注册时通过web_service生成的用户id
target:操作完成后转向的链接
op_id：商城的交易流水
mall_key：交易流水的加密值
money：操作金额
	*/

    function i_awardl2m($user_id=0,$money=0)
	{
		if($user_id==0 || $money==0)
		{
			$user_id=$_REQUEST['user_id'];
			$money=(float)$_REQUEST['money'];
		}
		else
		{
			$user_id=DeCode($user_id,'E');
		}
		$op_id=date('Ymdhis').rand(100,999);
		$data=array(
			'user_id'=>$user_id,
			'money'=>$money,
			'op_id'=>$op_id,
			'mall_key'=>DeCode($op_id,'E')		
		);
		$result=getHTML($this->p2purl.'/index.php?user&q=code/account/i_awardl2m',$data);
		$res=json_decode($result,true);
		if($res['result']==1)
		{
			$user_id=(int)DeCode($user_id,'D');								
			$moneylog_mod =& m('moneylog');				
			$row=$moneylog_mod->getRow("select * from ".DB_PREFIX."my_money where user_id=$user_id limit 1");
			if($row)
			{
				$array_log=array(					
					'jifen'=>0,
					'money'=>$money,
					'time'=>date('Y-m-d H:i:s'),
					'user_name'=>$row['user_name'],
					'user_id'=>$user_id,
					'zcity'=>$row['city'],
					'type'=>112,
					's_and_z'=>1,
					'beizhu'=>'',
					'orderid'=>'',
					'dq_money'=>$row['money']+$money,
					'dq_money_dj'=>$row['money_dj'],
					'dq_jifen'=>$row['duihuanjifen'],
					'dq_jifen_dj'=>$row['dongjiejifen']	
				);		
				//$moneylog_mod->add($array_log);//资金流水					
				$sql="update ".DB_PREFIX."my_money set money=money+$money where user_id='$user_id' limit 1";		
				//$moneylog_mod->db->query($sql);
				echo 'OK';
			}
			$row=null;
			echo $result;
		}	
	}
}

?>
