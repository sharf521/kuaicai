<?php
require('./include/member.class.php');
$tclass=new member();
$modulename='��Ա�ṹͼ';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

$func=$_REQUEST['func'];
$type=intval($_GET['type']);
$user_id=intval($_GET['user_id']);
$user_name=checkPost(strip_tags($_GET['user_name']));
$plevel=intval($_GET['plevel']);
$nlevel=intval($_GET['nlevel'])?intval($_GET['nlevel']):10;


pageTop($modulename.'����');


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
    	��ԱID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
        �û�����<input type="text" name="user_name" value="<?=$user_name?>" size="15"/>
        �ϲ�����<input type="text" size="4" name="plevel" value="<?=$plevel?>"/>
        �²�����<input type="text" size="4" name="nlevel" value="<?=$nlevel?>" />
    	<select name="type">
        	<option value="0" <? if($type=='0'){echo 'selected';}?>>�Ƽ���ϵ</option>
            <option value="1" <? if($type=='1'){echo 'selected';}?>>������ϵ</option>
        </select>

		<input type="submit" value="ɸѡ����">
		<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="show" />
        </form>
	</div>
    <div><div class="drawContent" id="drawContent"></div></div>
	<?		
}

?>