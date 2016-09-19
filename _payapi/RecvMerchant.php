<?php
require './init.php';
require './func.php';

//双乾MD5加密
function getSignature_sq($MerNo, $BillNo, $Amount, $ReturnURL, $MD5key){
	$_SESSION['MerNo'] = $MerNo;
	$_SESSION['MD5key'] = $MD5key;
	$sign_params  = array(
		'MerNo'       => $MerNo,
		'BillNo'       => $BillNo,
		'Amount'         => $Amount,
		'ReturnURL'       => $ReturnURL
	);
	$sign_str = "";
	ksort($sign_params);
	foreach ($sign_params as $key => $val) {
		$sign_str .= sprintf("%s=%s&", $key, $val);
	}
	return strtoupper(md5($sign_str. strtoupper(md5($MD5key))));
}

$user_id=(int)$_POST['user_id'];
$host=$_SERVER['HTTP_HOST'];
$money=(float)trim($_POST['cz_money']);
if(empty($user_id))
{
	header("location:/");
	echo '参数错误';
	exit();
}

$MerPriv = $user_id.'#'.$host;//商户私有数据项
$OrdAmt = sprintf("%.2f",$money);
$GateId = $_POST['GateId'];//网关号

$para=array(
	"OrdAmt"=>$OrdAmt,
	"Pid"=>18,
	"MerPriv"=>$MerPriv,
	"UsrSn"=>time().rand(1000,9999)
);
if(strstr($host,"jinhoudai"))
{
	$MD5key = "dHSqC}SS";  			                       //MD5key值
	$para['MerNo'] = "181823"; 			                   //商户ID
	$para['BillNo'] = time().rand(1000,9999);              //订单编号
	$para['Amount'] = sprintf("%.2f",$money); 			   //支付金额 【以分为单位】
	$para['orderTime'] = date('Y-m-d H:i:s',time()); 	   //下单日期 交易时间：YYYYMMDD
	$para['ReturnURL'] = 'http://'.$_SERVER['HTTP_HOST'].'/payapi/95epayreturn.php';  //同步通知url
	$para['NotifyURL'] = 'http://'.$_SERVER['HTTP_HOST'].'/payapi/95epayreturn.php';  //支付完成后，后台接收支付结果，可用来更新数据库值
	$para['MD5info'] = getSignature_sq($para['MerNo'], $para['BillNo'], $para['Amount'], $para['ReturnURL'], $MD5key);
	$para['PayType']="CSPAY";                              //交易类型
	$para['PaymentType']="";                               //银行代码
	switch($_POST['GateId'])
	{
		case 25: $para['PaymentType']="ICBC";break;
		case 29: $para['PaymentType']="ABC";break;
		case 27: $para['PaymentType']="CCB";break;
		case 28: $para['PaymentType']="CMB";break;
		case 12: $para['PaymentType']="CMBC";break;
		case 13: $para['PaymentType']="HXB";break;
		case 33: $para['PaymentType']="CNCB";break;
		case 16: $para['PaymentType']="SPDB";break;
		case "GDB": $para['PaymentType']="GDB";break;
		case 21: $para['PaymentType']="BOCOM";break;
		case "PSBC": $para['PaymentType']="PSBC";break;
		case 36: $para['PaymentType']="CEB";break;
		case "09": $para['PaymentType']="CIB";break;
		case 45: $para['PaymentType']="BOCSH";break;
		default: $para['PaymentType']="";
	}
	$para['MerRemark']=$MerPriv;
	$para['products']="";

	$sHtml = "<form id='95epay' name='95epay' action='https://www.95epay.cn/sslpayment' method='post' style='display:none'>";
	while (list ($key, $val) = each ($para)) {
		$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
	}
	//submit按钮控件请不要含有name属性
	$sHtml = $sHtml."<input type='submit'></form>";
	$sHtml = $sHtml."<script>document.forms['95epay'].submit();</script>";
	echo $sHtml;
	exit;
}

$para['Sign']=md5_sign($para,'79a9897555e4a51cfb77494d2e62fb34');
$para['GateId']=$_POST['GateId'];
$para['returl']='http://'.$_SERVER['HTTP_HOST'].'/payapi/return.php';
$para['bgreturl']='http://'.$_SERVER['HTTP_HOST'].'/payapi/return.php';

$sHtml = "<form id='fupaysubmit' name='fupaysubmit' action='http://pay.fuyuandai.com/gar/RecvMerchant.php' method='post' style='display:none'>";
while (list ($key, $val) = each ($para)) {
	$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
}
//submit按钮控件请不要含有name属性
$sHtml = $sHtml."<input type='submit'></form>";

$sHtml = $sHtml."<script>document.forms['fupaysubmit'].submit();</script>";

echo $sHtml;

?>