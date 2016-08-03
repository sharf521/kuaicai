<?php
$modulename='参数管理';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='fbb')
	{
		$t=webService('FBB_Cal');
		if($t==1)
			showMsg('FBB收益计算完成！');
		else
			showMsg('FBB收益计算失败！');
		exit();
	}
	elseif($func=='zhuo')
	{
		$t1=webService('Z_Static_Cal');	
		$t2=webService('Z_Dynamic_Cal');
		if($t1==1 && $t2==1)
			showMsg('FBB收益计算完成！');
		else
			showMsg('FBB收益计算失败！');
		exit();
	}
	else
	{
		$t=webService('JD_Cal');	//先增进后六保	
		if($t==1)
			showMsg('增进和六保收益计算完成！');
		else
			showMsg('计算失败！');
		exit();	
	}
	header("location:$url");
	exit();
}
pageTop($modulename.'管理');

$arr=array('ID'=>'dfd3f4dc-2b0b-45f5-96e5-6f350abcaf55',
	'start'=>0,
	'pageTotal'=>20,
	'type'=>0,
	'starts'=>'2012-01-10 00:00:00',
	'end'=>'2012-11-10 00:00:00');
webServiceList('Query_List',$arr);
exit();
if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename.'管理'=>''))?>&nbsp;&nbsp;</div>
    
    <br><br>

	<a href="?act=<?=$act?>&func=fbb">计算FBB收益</a>
    
    <a href="?act=<?=$act?>&func=zhuo">计算大小卓收益</a>
    
    <a href="?act=<?=$act?>&func=liubao">计算六保和增进收益</a>
   
    
<?
}
?>