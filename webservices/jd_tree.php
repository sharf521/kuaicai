<?php
require('./include/member.class.php');
$tclass=new member();
$modulename='增进结构图';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";


$func=$_REQUEST['func'];
$type=intval($_GET['type']);
$user_id=intval($_GET['user_id']);
$user_name=checkPost(strip_tags($_GET['user_name']));
$plevel=intval($_GET['plevel']);
$nlevel=intval($_GET['nlevel'])?intval($_GET['nlevel']):10;
$pan=intval($_GET['pan'])?intval($_GET['pan']):1;


$sqlW="a.T=$pan";
if(!empty($user_name))
{
	
	$sqlW.=" and b.user_name ='$user_name' ";
	$url.='&user_name='.$user_name;
}
if(!empty($user_id))
{	
	$sqlW.=" and b.user_id='$user_id'";
	$url.='&user_id='.$user_id;
}
pageTop($modulename.'管理');


if(empty($_GET['ui']))
{
?>
<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
<script type="text/javascript">	
	mxBasePath = 'js/mxgraph/src';
</script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/mxgraph/src/js/mxClient.js"></script>
<script language="javascript" src="js/JDTree.js"></script>
<script language="javascript">
var func='<?=$func?>';
var user_id='<?=$user_id?>';
var user_name='<?=$user_name?>';
var plevel='<?=$plevel?>';
var nlevel='<?=$nlevel?>';
var pan='<?=$pan?>';
$(document).ready(function () 
{
	if(func=='show')
	{
    	main();
	}
});
function GetJDTree(t)
{
	$.post("ajax.php?func=GetJDTree",{t:t},function(result){
		  alert(result);
	  });
}
function DelJDTree()
{
	$.post("ajax.php?func=DelJDTree",{},function(result){
		  alert(result);
	  });	
}
</script>


	<div style="margin-bottom:5px;">
    <input type="button" value="获取VIP1" onClick="GetJDTree(1)">
    <input type="button" value="获取VIP2" onClick="GetJDTree(2)">
    <input type="button" value="获取VIP3" onClick="GetJDTree(3)">
    <input type="button" value="获取VIP4" onClick="GetJDTree(4)">
    <input type="button" value="获取VIP5" onClick="GetJDTree(5)">
    <input type="button" value="获取VIP6" onClick="GetJDTree(6)">
    <input type="button" value="获取VIP7" onClick="GetJDTree(7)">
    <input type="button" value="获取VIP8" onClick="GetJDTree(8)">
    <input type="button" value="获取VIP9" onClick="GetJDTree(9)">
    <input type="button" value="获取VIP10" onClick="GetJDTree(10)">
    <input type="button" value="清除数据" onclick="DelJDTree()"/>
		<form method="get">
    	会员ID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
        用户名：<input type="text" name="user_name" value="<?=$user_name?>" size="15"/>
        上层数：<input type="text" size="4" name="plevel" value="<?=$plevel?>"/>
        下层数：<input type="text" size="4" name="nlevel" value="<?=$nlevel?>" />
        盘数：<input type="text" size="4" name="pan" value="<?=$pan?>" />
    	

		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="show" />
        </form>
	</div>
    <?	
	$PageSize = 10;  //每页显示记录数	
	
	$row=$db->get_one("select count(*) as count from {jdtree} a left join {member} b on a.UserID=b.web_id where $sqlW");
	
	$RecordCount = $row['count'];//获取总记录数
	
	if(!empty($page))
	{
		$StartRow=($page-1)*$PageSize;
	}
	else
	{
		$StartRow=0;
		$page=1;
	}
	if($RecordCount>0)
	{
		
		$sql="select a.*,b.user_id,b.user_name,b.web_id,b.city from {jdtree} a left join {member} b on a.UserID=b.web_id where $sqlW order by a.ID limit $StartRow,$PageSize";
		
		$result=$db->get_all($sql);	
		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('会员id','会员名','web_id','时间'));	
		
		
		foreach ($result as $row)
		{
			if(empty($row['user_name'])) $row['user_name']=$row['UserID'];			
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='center'><?=getuserno($row['user_id'])?></td>
            	<td align='center'><?=$row['user_name']?></td>
				
                <td align='left'><?=$row['web_id']?></td>
                <td align="center"><?=$row['InComeTime']?></td>
  
               
			</tr>
			<?		
		}
		?>
        </form></table>
		<div class="line"><?=page($RecordCount,$PageSize,$page,$url)?></div>
		<?php
	}
	else
	{
		echo "<div><br>&nbsp;&nbsp;无数据！</div>";
	}

?>
    <br />
    
    <div><div class="drawContent" id="drawContent"></div></div>
	<?		
}
//$result=$db->get_all("select * from {message} where msg_id=1378 order by msg_id desc limit 0,10");
//print_r($result);

?>