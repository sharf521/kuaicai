<?
require './init.php';
$_S['canshu']=$db->get_one("select * from {canshu} where id=1");	

$CmdId = $_POST['CmdId'];			//消息类型
$MerId = $_POST['MerId']; 	 		//商户号
$RespCode = $_POST['RespCode']; 	//应答返回码
$TrxId = $_POST['TrxId'];  			//钱管家交易唯一标识
$OrdAmt = $_POST['OrdAmt']; 		//金额
$CurCode = $_POST['CurCode']; 		//币种
$Pid = $_POST['Pid'];  				//商品编号
$OrdId = $_POST['OrdId'];  			//订单号
$MerPriv = $_POST['MerPriv'];  		//商户私有域	
$RetType = $_POST['RetType'];  		//返回类型
$DivDetails = $_POST['DivDetails']; //分账明细
$GateId = $_POST['GateId'];  		//银行ID
$ChkValue = $_POST['ChkValue']; 	//签名信息


//验证签名
/*$SignObject = new COM("CHINAPNR.NetpayClient");
$MsgData = $CmdId.$MerId.$RespCode.$TrxId.$OrdAmt.$CurCode.$Pid.$OrdId.$MerPriv.$RetType.$DivDetails.$GateId;  	//参数顺序不能错
$MerFile = $_SERVER["DOCUMENT_ROOT"]."/PgPubk.key";			//商户验签公钥文件
$SignData = $SignObject->VeriSignMsg0($MerFile,$MsgData,strlen($MsgData),$ChkValue);*/

$fp = fsockopen("127.0.0.1", '8733', $errno, $errstr, 10);
if (!$fp) 
{
	echo "$errstr ($errno)<br />\n";
}
else 
{
	$MsgData = $CmdId.$MerId.$RespCode.$TrxId.$OrdAmt.$CurCode.$Pid.$OrdId.$MerPriv.$RetType.$DivDetails.$GateId;
	 
	$MsgData_len =strlen($MsgData);
	if($MsgData_len < 100 ){
		$MsgData_len = '00'.$MsgData_len;
	}
	elseif($MsgData_len < 1000 ){
		$MsgData_len = '0'.$MsgData_len;
	}

	$out = 'V'.$MerId.$MsgData_len.$MsgData.$ChkValue;
	
	$out_len = strlen($out);
	if($out_len < 100 ){
		$out_len = '00'.$out_len;
	}
	elseif($out_len < 1000 ){
		$out_len = '0'.$out_len;
	}
	$out =$out_len.$out;

	//echo $MsgData_len;exit;
	//$out = '0021S87052400101234567890';
	fputs($fp, $out);

	$ChkValue ='';
	while (!feof($fp)) {
		$ChkValue .= fgets($fp, 128);
	}
	fclose($fp);
	//echo $ChkValue;
}
$SignData = $ChkValue;
	

	
if($SignData == "0011V8717460000"){
	if($RespCode == "000000")
	{	
	  //判断缓存中是否有 创建交易cache缓存文件	
	  $path="../data/pay_cache/";
	  if (!file_exists($path)) 
	  { 
		  mkdir($path, 0777);
	  }
	  $file =$path.$OrdId;
	  $fp = fopen($file , 'w+');
	  chmod($file, 0777);	  
	  if(flock($fp , LOCK_EX | LOCK_NB)) //设定模式独占锁定和不堵塞锁定
	  {
		////////////start处理////////////////////////////
		
		$MerPriv=explode('#',$MerPriv);
		$user_id=(int)$MerPriv[0];
		$host=$MerPriv[1];	
		$row=$db->get_one("select id from {moneylog} where user_id=$user_id and orderid='$TrxId' limit 1");
		if($row)
		{
			echo "<!--";
			echo "RECV_ORD_ID_".$OrdId;
			echo "-->";
			echo "<script>window.location='../index.php?app=my_money&act=paylog';</script>";
			exit();
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
				
				$row=$db->get_one("select recharge from {city}  where city_yuming like '%".$host."%'  limit 1");
				
				$amoney	=$money*(float)$row['recharge'];//充值奖励
				
				chongzhi_award($money,$user_id,$TrxId,$host);	//奖励平台推荐人			
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
			
			//更新my_moneylog表				
			$add_mymoneylog=array(
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'add_time'=>time(),
				'leixing'=>30,
				'log_text'=>"充值{$money}元",
				//'caozuo'=>50,
				's_and_z'=>1,	
				'money'=>$relmoney,
				'riqi'=>date('Y-m-d H:i:s'),
				's_and_z'=>1,
				'type'=>2,	
				'status'=>1,	
				'money_feiyong'=>'-'.$fei,
				'city'=>$city,
				'dq_money'=>$dq_money+$relmoney,
				'dq_money_dj'=>$dq_money_dj,
				'dq_jifen'=>$dq_jifen,				
				'dq_jifen_dj'=>$dq_jifen_dj												
			);						
			$db->insert('{my_moneylog}',$add_mymoneylog);
			
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
		echo "<!--";
		echo "RECV_ORD_ID_".$OrdId;
		echo "-->";
		//header("location:../index.php?app=my_money&act=paylog");		
		//exit();echo "支付成功";
		echo "<script>window.location='../index.php?app=my_money&act=paylog';</script>";
		exit();
		
		///////////结束处理/////////////////////////////
	  flock($fp , LOCK_UN);     
  }
  fclose($fp);
			
			
			
			
	}
	else
	{
		//交易失败
		//根据订单号 进行相应业务操作
		//在些插入代码
		echo "支付失败";
	}
}else{
	//验签失败
	echo "验签失败[".$SignData."]";
}
?>