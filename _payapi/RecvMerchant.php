<?php
require './init.php';
require './func.php';

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
$para['returl']='http://'.$_SERVER['HTTP_HOST'].'/payapi/return.php';
$para['bgreturl']='http://'.$_SERVER['HTTP_HOST'].'/payapi/return.php';
$para=array(
	"OrdAmt"=>$OrdAmt,
	"Pid"=>19,
	"MerPriv"=>$MerPriv,
	"UsrSn"=>time().rand(1000,9999)
);

$para['Sign']=md5_sign($para,'620b979f83bb60bceaa0fc033e212f2a');
$para['GateId']=$_POST['GateId'];

$sHtml = "<form id='fupaysubmit' name='fupaysubmit' action='http://pay.fuyuandai.com/gar/RecvMerchant.php' method='post' style='display:none'>";
while (list ($key, $val) = each ($para)) {
	$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
}
//submit按钮控件请不要含有name属性
$sHtml = $sHtml."<input type='submit'></form>";

$sHtml = $sHtml."<script>document.forms['fupaysubmit'].submit();</script>";

echo $sHtml;

?>