<?php
$modulename='��������';
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
			showMsg('FBB���������ɣ�');
		else
			showMsg('FBB�������ʧ�ܣ�');
		exit();
	}
	elseif($func=='zhuo')
	{
		$t1=webService('Z_Static_Cal');	
		$t2=webService('Z_Dynamic_Cal');
		if($t1==1 && $t2==1)
			showMsg('FBB���������ɣ�');
		else
			showMsg('FBB�������ʧ�ܣ�');
		exit();
	}
	else
	{
		$t=webService('JD_Cal');	//������������	
		if($t==1)
			showMsg('�������������������ɣ�');
		else
			showMsg('����ʧ�ܣ�');
		exit();	
	}
	header("location:$url");
	exit();
}
pageTop($modulename.'����');

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
	<div class="div_title"><?=getHeadTitle(array($modulename.'����'=>''))?>&nbsp;&nbsp;</div>
    
    <br><br>

	<a href="?act=<?=$act?>&func=fbb">����FBB����</a>
    
    <a href="?act=<?=$act?>&func=zhuo">�����С׿����</a>
    
    <a href="?act=<?=$act?>&func=liubao">������������������</a>
   
    
<?
}
?>