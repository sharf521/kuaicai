<?php
$modulename='商城返利';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';


if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='del')
	{
		$id=intval($_GET['id']);
		//$db->query("update {webservice_list} set status='-1' where id=$id limit 1");
	}
	elseif($func=='close')
	{
		$id=$_GET['id'];
		$listid=$_GET['listid'];
		$money=webService('C_Query',array('ID'=>$listid));
		$status=webService('C_Consume_Close',array('ID'=>$listid));
		$db->query("update {webservice_list} set status='$status',jifen='$money' where id=$id limit 1");
	}
	elseif($func=='add' || $func=='edit')
	{
		
		if($func=='add')
		{
			if(empty($_POST['user_name']) && empty($_POST['user_id']))
			{
				showMsg('用户不能为空!');
				exit();	
			} 
			if(empty($_POST['jifen']))
			{
				showMsg('返还积分不能为空!');
				exit();	
			}	
			if(empty($_POST['user_name']))
			{
				$member=$db->get_one("select web_id,user_id,user_name,city from {member} where user_id='".$_POST['user_id']."' limit 1");
			}
			else
			{
				$member=$db->get_one("select web_id,user_id,user_name,city from {member} where user_name='".$_POST['user_name']."' limit 1");
			}
			if(!$member)
			{
				showMsg('用户不存在！');exit();	 
			}
			else
			{
				if(empty($member['web_id']))
				{
					showMsg('用户web_id不存在！');exit();	 	
				}					
			}
				
			$post_data=array("ID"=>$member['web_id'],"Money"=>$_POST['jifen'],'MoneyType'=>$_POST['type'],'Count'=>1);
			$consume_id=webService('C_Consume',$post_data);
			
			$arr=array(
				'cai_id'=>0,
				'gong_id'=>$member['user_id'],
				'consume_id'=>$consume_id,
				'type'=>$_POST['type'],
				'time'=>date('Y-m-d H:i:s'),
				'status'=>0,
				'money'=>$_POST['jifen'],
				'jifen'=>0,
				'city'=>$member['city'],
				'gh_id'=>0
			);
			$db->insert('{webservice_list}',$arr);			
		}
		
	}
		
	header("location:$url");
	exit();
}

if($_GET['status']!='')
{	
	$status=$_GET['status'];	
	$sqlW.=' and status='.$status;
	$url.='&status='.$status;
}	
if(!empty($_GET['word']))  
{
	$word=checkPost(strip_tags($_GET['word']));
	$sqlW.=" and (m.user_name like '%$word%' or m.real_name='%$word%') ";
	$url.='&word='.$word;
}   
if(!empty($_GET['c']))
{
	$c=checkPost(strip_tags($_GET['c']));
	$sqlW.=" and m.city='$c'";
	$url.='&c='.$c;
}
if(!empty($_GET['user_id']))
{
	$user_id=intval($_GET['user_id']);
	$sqlW.=" and w.gong_id='$user_id'";
	$url.='&user_id='.$user_id;
}

if(!empty($_GET['real_name']))
{
	$real_name=trim(checkPost(strip_tags($_GET['real_name'])));
	$sqlW.=" and m.real_name like '%$real_name%'";
	$url.='&real_name='.$real_name;
}
if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and m.user_name='$user_name'";
	$url.='&user_name='.$user_name;
}
if(!empty($_GET['starttime']))
{
	$starttime=checkPost(strip_tags($_GET['starttime']));
	$sqlW.=" and w.time>='".($starttime)."'";
	$url.='&starttime='.$starttime;
}
if(!empty($_GET['endtime']))
{
	$endtime=checkPost(strip_tags($_GET['endtime']));
	$sqlW.=" and w.time<='".($endtime)."'";
	$url.='&endtime='.$endtime;
}


pageTop($modulename);

?>
<script language="javascript" src="include/js/jquery.js"></script>
<script language="javascript">
function getmon(listid)
{
	$.post("ajax.php?func=C_Query",{listid:listid},function(result){
		alert(result);
	});	
}
function tt(v)
{
	document.getElementById('jifen').value=v*252/100;
}
</script>
<?

$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}

if(empty($_GET['ui']))
{
		
?>
<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;<a href="?act=<?=$act?>&ui=add">添加</a></div>	
<script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>
	<div style="margin-bottom:5px;">
	<form method="GET">	
    	会员ID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
        用户名：<input type="text" name="user_name" value="<?=$user_name?>" size="8"/>
        姓名：<input type="text" name="real_name" value="<?=$real_name?>" size="8"/>
        <select name="c">
        <option value="">选择分站</option>
        <?
        foreach($city as $i=>$k)
		{
			$ch=($c==$i)?'selected':'';
			echo "<option value='$i' $ch>$k</option>";
		}
		?>        
        </select>
        <input id="starttime"  name="starttime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'starttime'});" value="<?=$starttime?>">
        <input id="endtime"  name="endtime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'endtime'});" value="<?=$endtime?>">
        
        <input type="text" name="word" value="<?=$word?>">&nbsp;
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
	<?	
	$PageSize = 15;  //每页显示记录数	

	$row=$db->get_one("select count(w.id) count from {webservice_list} w left join {member} m on w.gong_id=m.user_id where $sqlW ");
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
		$sql="select w.*,m.user_name,m.web_id from {webservice_list} w left join {member} m on w.gong_id=m.user_id where $sqlW order by w.id desc limit $StartRow,$PageSize";
		$result=$db->get_all($sql);	
		$arr_type=array('12%','16%','双队列');	
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('id','会员id','会员名','类型','排队积分','队列id','应返还','时间','最终返还','队列状态','操作','web_id'));	
		
		
		foreach ($result as $row)
		{
			$id=$row['id'];
			$listid=$row['consume_id'];
		
			?>
			<tr <?=getChangeTr()?>>
            	<td><?=$row['id']?></td>
            	<td align='center'><?=$row['gong_id']?></td>
                <td align='center'><?=$row['user_name']?></td>
                
                
                <td align='left'>&nbsp;&nbsp;<?=$arr_type[$row['type']]?></td>
                
                <td align='left'> <?
				
				if($row['type']==0)
				{
					echo $row['money']*0.15;
				}
				elseif($row['type']==1)
				{
				 	echo $row['money']*0.16;
				}
				elseif($row['type']==2)
				{
					echo $row['money']*0.31;	
				}
				?></td>
                <td align='center'><?=$listid?></td>
                
                <td align='center'><?=$row['money']?></td><td align="center"><?=$row['time']?></td>
                <td align='center'><?=$row['jifen']?></td>
                
				<td align="center"><?=$row['status']==1?'己结束':$row['status']?></td>
				<td align="center">	
                <a onClick="return confirm('确定要结束吗？')" href="<?=$url?>&func=close&id=<?=$id?>&listid=<?=$listid?>">结束队列</a>				
                <a href="javascript:getmon(<?=$listid?>)">查询返还积分</a>     
                 </td>
                 <td align='center'><?=$row['web_id']?></td>
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
	$id=intval($_GET['id']);
	echo '<form method="POST"  enctype="multipart/form-data">';
	echo '<input type="hidden" name="url" value="'.$url.'">';
	if(empty($id))
	{
		$arr=array($modulename=>$url,'添加'.$modulename=>'');
		echo '<input type="hidden" name="func" value="add">';
	}
	else
	{
		$arr=array($modulename.'管理'=>$url,'编辑'.$modulename=>'');
		echo '<input type="hidden" name="func" value="edit">';
		echo "<input type='hidden' name='id' value='$id'>";
		
		$tclass->id=$id;
		$row=$tclass->getone();
		$user_id=$row['user_id'];
		$mem=$db->get_one("select money_dj,money,suoding_money from {my_money} where user_id='$user_id' limit 1");
		$user_money=$mem['money'];
		$user_money_dj=$mem['money_dj'];
		$user_suoding_money=$mem['suoding_money'];
		$mem=null;
		
		echo "<input type='hidden' name='user_id' value='$user_id'>";
		
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="<?=$url?>">返回管理</a></div>
		
        
        
     
    <table class="infoTable">
      <tbody><tr>
        <th class="paddingT15"> 会员ID:</th>
        <td class="paddingT15 wordSpacing5"><input name="user_id" value="<?=$row['user_id']?>"> </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> 会员名:</th>
        <td class="paddingT15 wordSpacing5"><input name="user_name" value="<?=$row['user_name']?>"> </td>
      </tr>
      <tr>
        <th class="paddingT15"> 金钱:</th>
        <td class="paddingT15 wordSpacing5"><input id="money" value=""  onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this);tt(this.value)"/></td>
      </tr>
      <tr>
        <th class="paddingT15"> 返还积分:</th>
        <td class="paddingT15 wordSpacing5"><input name="jifen" id="jifen" value="<?=$row['jifen']?>"> </td>
      </tr>
      
      <tr><input type="hidden" name="web_id" value="<?=$row['web_id']?>" />
        <th class="paddingT15"> 返还类型:</th>
        <td class="paddingT15 wordSpacing5"><select name="type">
        
        <option value="0" <? if($row['type']==0){echo 'selected';}?>>12%</option>
        <option value="1" <? if($row['type']==1){echo 'selected';}?> >16%></option>
        <option value="2" <? if($row['type']==2){echo 'selected';}?> selected="selected">双队列</option>
        </select></td>
      </tr>
     
<tr>
        <th></th>
        
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交">
          <input class="formbtn" type="reset" name="Reset" value="重置">        </td>
      </tr>
   </table>

        
		</form>		
		<?
}
?>