<?	
	$CmdId = $_POST['CmdId'];				//消息类型
	$OrdId = $_POST['OrdId'];				//退款订单号
	$OldOrdId = $_POST['OldOrdId'];			//原交易订单号
	$RespCode = $_POST['RespCode']; 	 	//应答码
	$ErrMsg = $_POST['ErrMsg'];				//应答错误描述
	$ChkValue =$_POST['ChkValue'];			//验证码
	
	//验证签名
	$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $CmdId.$OrdId.$OldOrdId.$RespCode.$ErrMsg;  		//参数顺序不能错
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/PgPubk.key";			//商户验签公钥文件
	$SignData = $SignObject->VeriSignMsg0($MerFile,$MsgData,strlen($MsgData),$ChkValue);
		
	if($SignData == "0"){
		if($RespCode == "000000"){
			//退款成功
			//根据退款订单号 进行相应业务操作
			//在些插入代码
			echo "退款成功";
		}else{
			//退款失败
			//根据退款订单号 进行相应业务操作
			//在些插入代码
			echo "退款失败";
		}
		echo "RECV_ORD_ID_".$OldOrdId;
	}else{
		//验签失败
		echo "验签失败[".$SignData."]";
	}
?>