<?php
require('./include/member.class.php');
$tclass=new member();
$modulename='会员结构图';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

$func=$_REQUEST['func'];
$type=intval($_GET['type']);
$user_id=intval($_GET['user_id']);
$user_name=checkPost(strip_tags($_GET['user_name']));
$plevel=intval($_GET['plevel']);
$nlevel=intval($_GET['nlevel'])?intval($_GET['nlevel']):10;


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
<script language="javascript" src="js/mxgrapth.js"></script>
<script language="javascript">
var func='<?=$func?>';
var type='<?=$type?>';
var user_id='<?=$user_id?>';
var user_name='<?=$user_name?>';
var plevel='<?=$plevel?>';
var nlevel='<?=$nlevel?>';
$(document).ready(function () 
{
	if(func=='show')
	{
    	main();
	}
});
</script>


	<div style="margin-bottom:5px;">
		<form method="get">
    	会员ID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
        用户名：<input type="text" name="user_name" value="<?=$user_name?>" size="15"/>
        上层数：<input type="text" size="4" name="plevel" value="<?=$plevel?>"/>
        下层数：<input type="text" size="4" name="nlevel" value="<?=$nlevel?>" />
    	<select name="type">
        	<option value="0" <? if($type=='0'){echo 'selected';}?>>推荐关系</option>
            <option value="1" <? if($type=='1'){echo 'selected';}?>>隶属关系</option>
        </select>

		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="show" />
        </form>
	</div>
    <div><div class="drawContent" id="drawContent"></div></div>
	<?		
}

?>