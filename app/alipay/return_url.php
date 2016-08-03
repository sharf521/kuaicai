<?php
/*
	*功能：付完款后跳转的页面（返回页）
	*版本：3.0
	*日期：2010-06-29
	'说明：
	'以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
	'该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
	
*/
///////////页面功能说明///////////////
//该页面可在本机电脑测试
//该页面称作“返回页”，是由支付宝服务器同步调用，可当作是支付完成后的提示信息页，如“您的某某某订单，多少金额已支付成功”。
//可放入HTML等美化页面的代码和订单交易完成后的数据库更新程序代码
//该页面可以使用PHP开发工具调试，也可以使用写文本函数log_result进行调试，该函数已被默认关闭，见alipay_notify.php中的函数return_verify
//WAIT_SELLER_SEND_GOODS(表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货);
///////////////////////////////////




require_once("class/alipay_notify.php");
require_once("alipay_config.php");

//构造通知函数信息
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
//计算得出通知验证结果
$verify_result = $alipay->return_verify();
print_r($alipay);
if($verify_result) {

    //验证成功
    //获取支付宝的通知返回参数
    $dingdan           = $_GET['out_trade_no'];		//获取订单号
    $total_fee         = $_GET['price'];			//获取总价格
    $sOld_trade_status = 0;							//获取商户数据库中查询得到该笔交易当前的交易状态
	$verify_resultShow = "验证成功";

    /*假设：
	sOld_trade_status="0"	表示订单未处理；
	sOld_trade_status="1"	表示买家已在支付宝交易管理中产生了交易记录，但没有付款
	sOld_trade_status="2"	表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
	sOld_trade_status="3"	表示卖家已经发了货，但买家还没有做确认收货的操作
	sOld_trade_status="4"	表示买家已经确认收货，这笔交易完成
    */

    if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS' || $_GET['trade_status'] == 'TRADE_FINISHED') {

        //放入订单交易完成后的数据库更新程序代码，请务必保证echo出来的信息只有success
        //为了保证不被重复调用，或重复执行数据库更新程序，请判断该笔交易状态是否是订单未处理状态
        if ($sOld_trade_status < 2) {
			//当$_GET['trade_status'] 为WAIT_SELLER_SEND_GOODS，则说明买家用的支付方式是担保交易付款
			//当$_GET['trade_status'] 为TRADE_FINISHED，则说明买家用的支付方式是即时到帐付款
            //根据订单号更新订单，把订单处理成交易成功
        }
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的return_verify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
    echo "fail";
	$verify_resultShow = "验证失败";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>支付宝双功能（担保/即时）付款</title>
<style type="text/css">
            .font_content{
                font-family:"宋体";
                font-size:14px;
                color:#FF6600;
            }
            .font_title{
                font-family:"宋体";
                font-size:16px;
                color:#FF0000;
                font-weight:bold;
            }
            table{
                border: 1px solid #CCCCCC;
            }
        </style>
</head>
<body>
<table align="center" width="350" cellpadding="5" cellspacing="0">
  <tr>
    <td align="center" class="font_title" colspan="2">通知返回</td>
  </tr>
  <tr>
    <td class="font_content" align="right">支付宝交易号：</td>
    <td class="font_content" align="left"><?php echo $_GET['trade_no']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">订单号：</td>
    <td class="font_content" align="left"><?php echo $_GET['out_trade_no']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">付款总金额：</td>
    <td class="font_content" align="left"><?php echo $_GET['price']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">商品标题：</td>
    <td class="font_content" align="left"><?php echo $_GET['subject']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">商品描述：</td>
    <td class="font_content" align="left"><?php echo $_GET['body']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">买家账号：</td>
    <td class="font_content" align="left"><?php echo $_GET['buyer_email']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">收货人姓名：</td>
    <td class="font_content" align="left"><?php echo $_GET['receive_name']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">收货人地址：</td>
    <td class="font_content" align="left"><?php echo $_GET['receive_address']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">收货人邮编：</td>
    <td class="font_content" align="left"><?php echo $_GET['receive_zip']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">收货人电话：</td>
    <td class="font_content" align="left"><?php echo $_GET['receive_phone']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">收货人手机：</td>
    <td class="font_content" align="left"><?php echo $_GET['receive_mobile']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">交易状态：</td>
    <td class="font_content" align="left"><?php echo $_GET['trade_status']; ?></td>
  </tr>
  <tr>
    <td class="font_content" align="right">验证是否成功：</td>
    <td class="font_content" align="left"><?php echo $verify_resultShow; ?></td>
  </tr>
</table>
</body>
</html>
