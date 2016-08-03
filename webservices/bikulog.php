<?php
require('./include/bikulog.class.php');
$tclass=new bikulog();
$modulename='币库管理';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if($_GET['status']!='')
{	
	$status=$_GET['status'];	
	$sqlW.=' and status='.$status;
	$url.='&status='.$status;
}	
if(!empty($_GET['word']))
{
	$word=checkPost(strip_tags($_GET['word']));
	$sqlW.=" and beizhu like '%$word%' ";
	$url.='&word='.$word;
}
if(!empty($_GET['s_and_z']))
{
	$s_and_z=intval($_GET['s_and_z']);
	$sqlW.=" and s_and_z=$s_and_z";
	$url.='&s_and_z='.$s_and_z;
}
if(!empty($_GET['type']))
{
	$type=intval($_GET['type']);
	$sqlW.=" and type=$type";
	$url.='&type='.$type;
}
if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='add')
	{
		$money=intval($_POST['money']);
		$db->query("update {canshu} set zong_jinbi=zong_jinbi+$money,yu_jinbi=yu_jinbi+$money where id=1");
		
		$row=$db->get_one("select zong_jinbi,yu_jinbi from {canshu} where id=1");
		$arr=array(
			'money'=>$money,
			'user_id'=>'',
			'user_name'=>'',
			'type'=>25,
			's_and_z'=>1,
			'riqi'=>date('Y-m-d H:i:s'),
			'biku_city'=>0,
			'dq_jinbi'=>$row['zong_jinbi'],
			'dq_yujinbi'=>$row['yu_jinbi'],
			'beizhu'=>$a_username.'|'.$a_userid.'控台管理员增加币库'
		);
		$db->insert('{bikulog}',$arr);
	}
	
	header("location:$url");
	exit();
}
pageTop($modulename.'管理');

$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}

$arr_zs=array('选择收/支','收入','支出');

if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename.'管理'=>''))?>&nbsp;&nbsp;<a href="?act=<?=$act?>&ui=add">添加</a></div>	
    
    总币库：<?=$_S['canshu']['zong_jinbi']?>
    剩余币库：<?=$_S['canshu']['yu_jinbi']?>
	<div style="margin-bottom:5px;">
	<form method="GET">	
    	<select name="s_and_z">
            <?
            foreach($arr_zs as $i=>$v)
			{
				$ch='';
				if($s_and_z==$i)  $ch='selected'; 
				?>
                <option value="<?=$i?>" <?=$ch?>><?=$arr_zs[$i]?></option>
                <?	
			}
			?>
        </select>
        
        <select name="type">
            <?
            foreach($arr_type as $i=>$v)
			{
				$ch='';
				if($type==$i)  $ch='selected'; 
				?>
                <option value="<?=$i?>" <?=$ch?>><?=$arr_type[$i]?></option>
                <?	
			}
			?>
        </select>
    	
        <input type="text" name="word" value="<?=$word?>">&nbsp;
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
	<?	
	$PageSize = 15;  //每页显示记录数	
	$RecordCount = $tclass->getcount($sqlW);//获取总记录数
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

		$result=$tclass->getall($StartRow,$PageSize,'biku_id desc',$sqlW);		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('编号','操作数','当前总币库','当前剩余币库','操作用户名[用户ID]','操作时间','所属站','类型','收/支','备注'));	
		
		foreach ($result as $row)
		{
			$user_id=$row['user_id'];
			
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='center'><?=$row['biku_id']?></td>
                <td align='left'><?=$row['money']?></td>
                <td align='left'><?=$row['dq_jinbi']?></td>
                 <td align='left'><?=$row['dq_yujinbi']?></td>
                
                <td align='left'>&nbsp;&nbsp;<?=$row['user_name']?> [<?=$row['user_id']?>]</td>
               
                <td align="center"><?=$row['riqi']?></td>
                <td align="left">&nbsp;&nbsp;<?=$city[$row['biku_city']];?></td>
                 <td align='left'><?=$arr_type[$row['type']]?></td>
                <td align='left'>&nbsp;&nbsp;<?=$arr_zs[$row['s_and_z']]?></td>
				
				<td align='left'><?=$row['beizhu']?></td>
				
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
}
else
{
	$user_id=intval($_GET['user_id']);
	echo '<form method="POST"  enctype="multipart/form-data">';
	echo '<input type="hidden" name="url" value="'.$url.'">';
	if(empty($user_id))
	{
		$arr=array($modulename=>$url,'添加'.$modulename=>'');
		echo '<input type="hidden" name="func" value="add">';
	}
	else
	{
		$arr=array($modulename.'管理'=>$url,'编辑'.$modulename=>'');
		echo '<input type="hidden" name="func" value="edit">';
		
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="<?=$url?>">返回管理</a></div>
		
        
        
     
    <table class="infoTable">
      <tbody><tr>
        <th class="paddingT15"> 增加币库:</th>
        <td class="paddingT15 wordSpacing5"><input type="text" name="money" value="">
          
                  </td>
      </tr>
     
      
      


        <th></th>
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交">
          <input class="formbtn" type="reset" name="Reset" value="重置">        </td>
      </tr>
   </table>
 
        
		</form>		
		<?
}
?>