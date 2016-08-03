<?php 
return array (
  'touser_send_coupon' => '优惠积分：{$price} \\r\\n有效期：{$start_time} 至{$end_time} \\r\\n优惠券号码：{$coupon_sn} \\r\\n使用条件：购物满 {$min_jifen}积分 即可使用 \\r\\n店铺地址：<a href="/index.php?app=store&id={$storeid}">{$store_name}</a>',
  'toseller_groupbuy_end_notify' => '请尽快到“已结束的团购”完成该团购活动，以便买家可以完成交易，如结束后{$cancel_days}天未确认完成，该活动将被自动取消,查看<a href="index.php?app=seller_groupbuy&state=end">已结束的团购</a>',
  'tobuyer_groupbuy_cancel_notify' => '团购活动被卖家取消,原因如下：
{$reason}
<a href="{$url}">查看详情</a>',
  'tobuyer_group_auto_cancel_notify' => '团购活动结束{$cancel_days}天后卖家未确认完成，活动自动取消，<a href="{$url}">查看详情</a>',
  'tobuyer_groupbuy_finished_notify' => '“{$group_name}”活动成功完成，请尽快购买活动商品。<a href="index.php?app=order&goods=groupbuy&group_id={$id}">点此购买</a>',
);
?>