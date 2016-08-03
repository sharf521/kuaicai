<?php
$modulename='参数管理';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='editkaiguan')
	{
		$webservice=$_POST['webservice'];
		$db->query("update {kaiguan} set webservice='$webservice' where id=1");
		adminlog('修改开关配置');
	}
	elseif($func=='editcanshu')
	{
		unset($_POST['act']);
		unset($_POST['func']);
		$db->update('{canshu}',$_POST,'id=1');
		adminlog('修改参数配置');
		showMsg('保存成功！',$url);exit();	
	}
	header("location:$url");
	exit();
}
pageTop($modulename.'管理');


if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename.'管理'=>''))?>&nbsp;&nbsp;</div>
    
    <br><br>
    <h2>开关管理</h2>
	<?
    	$kaiguan=$db->get_one("select * from {kaiguan} where id=1");		
	?>
    <table style=" margin-left:50px">
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="editkaiguan">
    	<tr><td>是否开启WebServices</td><td>
        	<input type="radio" name="webservice" value="yes" checked>开启
            <input type="radio" name="webservice" value="no" <? if($kaiguan['webservice']=='no'){echo 'checked';}?>>不天启
         </td></tr>
         

         
         <tr><td colspan="2"><input type="submit" value="保存"></td></tr>
    </form>
    </table>
    
    
    <h2>参数管理</h2>
	<?
	//duihuanjifenfeilv 兑换积分的费率	 duihuanxianjinfeilv 兑换现金的费率	 tixianfeilv 提现费率	 chongzhifeilv 充值费率	 tuijianjiangli 推荐人获得的奖励	 jlshuishou 推荐奖励现金税收比例	 tuijianjifen 推荐奖励积分	
	// jfshuishou 推荐奖励积分税收比例	 jifenbili 将消费积分转换信用积分比例	 tg_fei 团购费用	 tg_baozhengjin 团购保证金	 zong_jinbi 总币库	 yu_jinbi 剩余币库	 jifenxianjin 现金积分折算比例	 daishou 代售货物，交易时与供货商的比例
    	$row=$db->get_one("select * from {canshu} where id=1");		
	?>
    <table style=" margin-left:50px">
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="editcanshu">
        
    	<tr><td>WebService地址：</td><td><input type="text" name="webservip" value="<?=$row['webservip']?>" /></td></tr>
        <tr><td>兑换积分的费率</td><td><input type="text" name="duihuanjifenfeilv" value="<?=$row['duihuanjifenfeilv']?>" /></td></tr>
        <tr><td>兑换现金扣除比例</td><td><input type="text" name="duihuanxianjinfeilv" value="<?=$row['duihuanxianjinfeilv']?>" /></td></tr>
        
        <tr><td>提现费率</td><td><input type="text" name="tixianfeilv" value="<?=$row['tixianfeilv']?>" /></td></tr>
        <tr><td>提现最低费用</td><td><input type="text" name="tixianfeimin" value="<?=$row['tixianfeimin']?>" /></td></tr>
        <tr><td>提现最高费用</td><td><input type="text" name="tixianfeimax" value="<?=$row['tixianfeimax']?>" /></td></tr>
        <tr><td>充值费率</td><td><input type="text" name="chongzhifeilv" value="<?=$row['chongzhifeilv']?>" /></td></tr>
        <tr><td>充值最低费用</td><td><input type="text" name="chongzhifeimin" value="<?=$row['chongzhifeimin']?>" /></td></tr>
        <tr><td>充值最高费用</td><td><input type="text" name="chongzhifeimax" value="<?=$row['chongzhifeimax']?>" /></td></tr>
        <tr><td>推荐人获得的奖励</td><td><input type="text" name="tuijianjiangli" value="<?=$row['tuijianjiangli']?>" /></td></tr>
        <tr><td>将消费积分转换信用积分比例</td><td><input type="text" name="jifenbili" value="<?=$row['jifenbili']?>" /></td></tr>
        <tr><td>推荐奖励积分</td><td><input type="text" name="tuijianjifen" value="<?=$row['tuijianjifen']?>" /></td></tr>
        <tr><td>团购费用</td><td><input type="text" name="tg_fei" value="<?=$row['tg_fei']?>" /></td></tr>
        <tr><td>团购保证金</td><td><input type="text" name="tg_baozhengjin" value="<?=$row['tg_baozhengjin']?>" /></td></tr>
        
        <tr><td>现金积分折算比例</td><td><input type="text" name="jifenxianjin" value="<?=$row['jifenxianjin']?>" /></td></tr>
        <tr><td>代售货物，交易时与供货商的比例</td><td><input type="text" name="daishou" value="<?=$row['daishou']?>" /></td></tr>
        <tr><td>允许提现的最小金额</td><td><input type="text" name="tx_min" value="<?=$row['tx_min']?>" /></td></tr>
        <tr><td>允许提现的最大金额</td><td><input type="text" name="tx_max" value="<?=$row['tx_max']?>" /></td></tr>

 	  


	<tr style="display:none"><td>算法类型</td><td><input type="text" name="plantype" value="<?=$row['plantype']?>" /></td></tr>
    <tr><td>分红比例</td><td><input type="text" name="fenhongbili" value="<?=$row['fenhongbili']?>" />
       <!-- ///  单独16算法分红比例<br />
        ///     1 平台昨日收益 * 0.05m * 0.16m;<br />
        ///     2 平台昨日收益 * 0.3m * 0.16m;<br />
        ///     3 平台昨日收益 * 0.3333333m * 0.16m;<br />
 
        ///  混合算法中16算法分红比例<br />
        ///     1 平台昨日收益 * 0.05m * 0.16m;<br />
        ///     2 平台昨日收益 * 0.3m * 0.16m;<br />
        ///     3 平台昨日收益 * 0.3333333m * 0.16m;<br />-->
        
        16算法分红比例
        1 平台昨日收益 * 0.005        
        2 平台昨日收益 * 0.0083        
        3 平台昨日收益 * 0.025
        
        
</td></tr>    
    <tr><td>股权大小</td><td><input type="text" name="guquandaxiao" value="<?=$row['guquandaxiao']?>" />
    
    [0] 单独120算法 股权大小；
        ///  [1] 单独16算法 股权大小；
        ///  [2] 混合算法中 120 股权大小；
        ///  [3] 混合算法中 16 股权大小；

    </td></tr>
    <tr><td>概率值</td><td><input type="text" name="Probability" value="<?=$row['Probability']?>" />
    
    16%60返2排队中60倍数返还概率(有效值:1,2,3,4)多个逗号分隔

    </td></tr>
    
    
    
    <tr style="display:none"><td>日封顶金额</td><td><input type="text" name="daymaxmoney" value="<?=$row['daymaxmoney']?>" /></td></tr>
    <tr style="display:none"><td>日封顶金额类型</td><td>
    <input type="radio" name="daymaxmoneytype" value="1" checked="checked"/>对所有用户
    <input type="radio" name="daymaxmoneytype" value="2" <? if($row['daymaxmoneytype']==2){echo 'checked';}?>/>用户购买金额</td></tr>
      

        
         
         <tr><td colspan="2"><input type="submit" value="保存"></td></tr>
    </form>
    </table>
    
<?
}
?>