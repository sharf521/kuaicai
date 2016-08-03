<?

require './init.php';
echo date('Y-m-d H:i:s');
exit();
header("location:../index.php?app=my_money&act=paylog");

function aa()
{
	global $db;	
	$host=$_SERVER['HTTP_HOST'];
	$row=$db->get_one("select user_id,tuijianren_id from {city}  where city_yuming like '%$host%'  limit 1");
	print_r($row);
}
aa();
exit();

$_S['canshu']=$db->get_one("select * from {canshu} where id=1");

	$CmdId = $_POST['CmdId'];			//消息类型
	$MerId = $_POST['MerId']; 	 		//商户号
	$RespCode = $_POST['RespCode']; 	//应答返回码
	$TrxId = '12343374834343';  			//钱管家交易唯一标识
	$OrdAmt = 200; 		//金额
	$CurCode = $_POST['CurCode']; 		//币种
	$Pid = $_POST['Pid'];  				//商品编号
	$OrdId = $_POST['OrdId'];  			//订单号
	$MerPriv = 350;  		//商户私有域
	$RetType = $_POST['RetType'];  		//返回类型
	$DivDetails = $_POST['DivDetails']; //分账明细
	$GateId = $_POST['GateId'];  		//银行ID
	$ChkValue = $_POST['ChkValue']; 	//签名信息
	
	//验证签名
		//商户验签公钥文件
	$SignData = "0";
	$RespCode = "000000";
	
	if($SignData == "0"){
		if($RespCode == "000000"){
			$user_id=(int)$MerPriv;				
			$row=$db->get_one("select id from {moneylog} where user_id=$user_id and orderid='$TrxId' limit 1");
			if($row)
			{
				echo "RECV_ORD_ID_".$OrdId;
				echo '重复了吧';	
			}
			else
			{
				$money=$OrdAmt;	
				if($money<200)
				{
					$fei	=$money*0.01;	
					$amoney =0;			
				}
				else
				{
					$amoney	=$money*0.001;//充值奖励
					if($GateId=='61')//PNR钱管家
					{
						$fei=$money*0.009;	
					}
					elseif(in_array($GateId,array(25,29,27,28,12,13,33)))
					{
						$fei=$money*0.006;
					}
					else
					{
						$fei=$money*0.008;	
					}
					chongzhi_award($money,$user_id,$TrxId);	//奖励平台推荐人			
				}
				$gate_ar=array(69,70,71,72,74,75,76,78,81);
				if(in_array($GateId,$gate_ar))
				{
					if($fei<6) $fei=6;
				}			
				$relmoney=$money-$fei;			
				
				
				$row=$db->get_one("select user_name,money,duihuanjifen,dongjiejifen,money_dj,qianbiku,city from {my_money} where user_id='$user_id' limit 1");
					$user_name=$row['user_name'];
					$dq_money=$row['money'];
					$dq_money_dj=$row['money_dj'];
					$dq_jifen=$row['duihuanjifen'];
					$dq_jifen_dj=$row['dongjiejifen'];
					$qianbiku=$row['qianbiku'];
					$city=$row['city'];
				$row=null;
				//冲值流水日志
				$arr=array(
					'money'=>$relmoney,
					'jifen'=>0,
					'money_dj'=>0,
					'jifen_dj'=>0,
					'user_id'=>$user_id,
					'user_name'=>$user_name,
					'type'=>100,
					's_and_z'=>1,
					'time'=>date('Y-m-d H:i:s'),
					'zcity'=>$city,
					'dq_money'=>$dq_money+$relmoney,
					'dq_money_dj'=>$dq_money_dj,
					'dq_jifen'=>$dq_jifen,				
					'dq_jifen_dj'=>$dq_jifen_dj,
					'orderid'=>$TrxId,
					'beizhu'=>"充值{$money}元"
				);			
				$db->insert('{moneylog}',$arr);
				if(empty($amoney))
				{				
					$db->query("update {my_money} set money=money+$relmoney where user_id='$user_id' limit 1");//更新帐户资金
				}
				else
				{				
					$arr=array(
						'money'=>$amoney,
						'jifen'=>0,
						'money_dj'=>0,
						'jifen_dj'=>0,
						'user_id'=>$user_id,
						'user_name'=>$user_name,
						'type'=>101,
						's_and_z'=>1,
						'time'=>date('Y-m-d H:i:s'),
						'zcity'=>$city,
						'dq_money'=>$dq_money+$relmoney+$amoney,
						'dq_money_dj'=>$dq_money_dj,
						'dq_jifen'=>$dq_jifen,				
						'dq_jifen_dj'=>$dq_jifen_dj,
						'orderid'=>$TrxId,
						'beizhu'=>"充值奖励"
					);			
					$db->insert('{moneylog}',$arr);//充值奖励				
					$db->query("update {my_money} set money=money+$relmoney+$amoney where user_id='$user_id' limit 1");//更新帐户资金
				}
				
				if($qianbiku>0)//如果以前购买套参的时候多划拨了币库,不在划拨币库
				{
					if($money>=$qianbiku)
					{
						$db->query("update {my_money} set qianbiku=0 where user_id='$user_id' limit 1");						
						$q=$money-$qianbiku;//充的钱大于欠的币库，还要划拨币库
						$db->query("update {canshu} set yu_jinbi=yu_jinbi-$q where id=1");							
						$arr=array(
							'money'=>'-'.$q,
							'user_id'=>$user_id,
							'user_name'=>$user_name,
							'type'=>100,
							's_and_z'=>2,
							'riqi'=>date('Y-m-d H:i:s'),
							'biku_city'=>$city,
							'dq_jinbi'=>$_S['canshu']['zong_jinbi'],
							'dq_yujinbi'=>$_S['canshu']['yu_jinbi']-$q,
							'beizhu'=>'订单号：'.$TrxId
						);
						$db->insert('{bikulog}',$arr);						
					}
					else
					{
						$q=$qianbiku-$money;
						$db->query("update {my_money} set qianbiku=$q where user_id='$user_id' limit 1");
					}
				}
				else
				{	
					$db->query("update {canshu} set yu_jinbi=yu_jinbi-$money where id=1");							
					$arr=array(
						'money'=>'-'.$money,
						'user_id'=>$user_id,
						'user_name'=>$user_name,
						'type'=>100,
						's_and_z'=>2,
						'riqi'=>date('Y-m-d H:i:s'),
						'biku_city'=>$city,
						'dq_jinbi'=>$_S['canshu']['zong_jinbi'],
						'dq_yujinbi'=>$_S['canshu']['yu_jinbi']-$money,
						'beizhu'=>'订单号：'.$TrxId
					);
					$db->insert('{bikulog}',$arr);					
				}
			}
			echo "支付成功";
		}else{
			//交易失败
			//根据订单号 进行相应业务操作
			//在些插入代码
			echo "支付失败";
		}
		echo "RECV_ORD_ID_".$OrdId;
	}else{
		//验签失败
		echo "验签失败[".$SignData."]";
	}
?>