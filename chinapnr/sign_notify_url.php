<?

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
	$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $CmdId.$MerId.$RespCode.$TrxId.$OrdAmt.$CurCode.$Pid.$OrdId.$MerPriv.$RetType.$DivDetails.$GateId;  	//参数顺序不能错
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/PgPubk.key";			//商户验签公钥文件
	$SignData = $SignObject->VeriSignMsg0($MerFile,$MsgData,strlen($MsgData),$ChkValue);
	
	if($SignData == "0"){
		if($RespCode == "000000"){

			echo "签约成功";
		}else{
			//交易失败
			//根据订单号 进行相应业务操作
			//在些插入代码
			echo "签约失败";
		}
		echo "RECV_ORD_ID_".$OrdId;
	}else{
		//验签失败
		echo "验签失败[".$SignData."]";
	}
?>