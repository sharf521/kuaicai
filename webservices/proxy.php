<?php
require('./include/proxy.class.php');
$tclass=new proxy();
$modulename='代理';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';


if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='del')
	{
		$tclass->delete(intval($_GET['id']));
	}
	elseif($func=='add' || $func=='edit')
	{
		if($tclass->pass($_POST))
		{
			if($func=='add')
			{
				if(empty($_POST['user_name']))
				{
					$member=$db->get_one("select web_id,user_id,user_name from {member} where user_id='".$_POST['user_id']."' limit 1");
				}
				else
				{
					$member=$db->get_one("select web_id,user_id,user_name from {member} where user_name='".$_POST['user_name']."' limit 1");
				}
				if(!$member)
				{
					showMsg('用户不存在！');exit();	 
				}
				else
				{
					$_POST['user_name']=$member['user_name'];	
					$_POST['user_id']=$member['user_id'];	
				}
				unset($member);	
				$tclass->add($_POST);
			}
			elseif($func=='edit')
				$tclass->edit($_POST);
		}
		else
		{
			showMsg($tclass->errmsg);exit();	
		}
	}	
	header("location:$url");
	exit();
}
pageTop($modulename);
//webService('Z_Static_Regist',array("ID"=>'753bbdea-de35-4ec6-b8e5-87d15a50076b',"Money"=>20000,'Month'=>9));

?>
<script language="javascript" src="include/js/jquery.js"></script>

<?


if(empty($_GET['ui']))
{
		
?>
<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;<a href="?act=<?=$act?>&ui=add">添加</a></div>	
	<div style="margin-bottom:5px;">
	</div>
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
		$arr_type=array('县','市','省');
		$result=$tclass->getall($StartRow,$PageSize,'id desc',$sqlW);		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('id','会员id','会员名','姓名','地址','电话','等级','代理地区','添加时间','状态','操作'));	
		
		
		foreach ($result as $row)
		{
			$id=$row['id'];
			$listid=$row['listid'];
		
			?>
			<tr <?=getChangeTr()?>>
            	<td><?=$row['id']?></td>
            	<td align='center'><?=$row['user_id']?></td>
                <td align='center'><?=$row['user_name']?></td>
                <td align='center'><?=$row['name']?></td>
                <td align='center'><?=$row['address']?></td>
                <td align='center'><?=$tel?></td>
                <td align='left'>&nbsp;&nbsp;<?=$arr_type[$row['level']]?></td>
                <td align="center"><?=$row['area']?></td>
                <td align="center"><?=$row['createdate']?></td>
				<td align="center"><?=$row['status']==1?'己结束':$row['status']?></td>
				<td align="center">	 
                <a href="<?=$url?>&ui=edit&id=<?=$id?>">编辑</a>    
                <a onClick="return confirm('确定要删除吗？')" href="<?=$url?>&func=del&id=<?=$id?>">删除</a>      
                 </td>
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
        <th class="paddingT15"> 姓名:</th>
        <td class="paddingT15 wordSpacing5"><input name="name" value="<?=$row['name']?>"></td>
      </tr>
      <tr>
        <th class="paddingT15"> 地址:</th>
        <td class="paddingT15 wordSpacing5"><input name="address" value="<?=$row['address']?>"> </td>
      </tr>
      <tr>
        <th class="paddingT15"> 电话:</th>
        <td class="paddingT15 wordSpacing5"><input name="tel" value="<?=$row['tel']?>"> </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> 等级:</th>
        <td class="paddingT15 wordSpacing5"><select name="level">
        
       
       
        <option value="0" <? if($row['level']==0){echo 'selected';}?>>县</option>
        <option value="1" <? if($row['level']==1){echo 'selected';}?> >市</option> 
        <option value="2" <? if($row['level']==2){echo 'selected';}?>>省</option>
        </select></td>
      </tr>
       <tr>
        <th class="paddingT15"> 代理地区:</th>
        <script>
			var province=new Array();
			var city=new Array();
			var county=new Array();
        <?
require_once './include/areas.php';
foreach($areas as $a)
{
	$key=substr($a,0,6);
	$name=trim(substr($a,6));
	if(strrpos($a,'0000')!==false)
	{		
		echo "province[$key]='$name';\r\n";
	}
	elseif(substr($a,4,2)=='00')
	{
		echo "city[$key]='$name';\r\n";	
	}
	else
	{
		echo "county[$key]='$name';\r\n";		
	}
}	
?>
function selProvince(val)
{
	sel=document.getElementById('city');
	if(val!='0')
	{	
		var f=val.substring(0,2);
		sel.options.length=0;	
		document.getElementById('county').options.length=0;	
		sel.options.add(new Option('请选择',0));	
		for(v in city)
		{
			if(v.substring(0,2)==f)
				sel.options.add(new Option(city[v],v+'|'+city[v]));				
		}
	}
	else
	{
		sel.options.length=0;	
	}
}
function selCity(val)
{
	sel=document.getElementById('county');
	if(val!='0')
	{		
		var f=val.substring(0,4);	
		sel.options.length=0;
		sel.options.add(new Option('请选择',0));
		for(v in county)
		{
			if(v.substring(0,4)==f)
				sel.options.add(new Option(county[v],v+'|'+county[v]));				
		}
	}
	else
	{
		sel.options.length=0;	
	}
}
  </script>      
        <td class="paddingT15 wordSpacing5">
        <select id="province" name="province" onchange="selProvince(this.value)">
   	<option value="0">请选择</option>
   </select>
   <select id="city" name="city" onchange="selCity(this.value)"></select>
   <select id="county" name="county"></select>
        <?=$row['area']?> 身份证前六位：<?=$row['areaid']?> </td>
      </tr>
     
<tr>
        <th></th>
        
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交">
          <input class="formbtn" type="reset" name="Reset" value="重置">        </td>
      </tr>
   </table>
   
<script language="javascript">
	for(v in province)
	{
		document.getElementById('province').options.add(new Option(province[v],v+'|'+province[v]));				
	}
	<?
	if(!empty($row['areaid']))
	{
		$areaid=$row['areaid'];
		if($row['level']==0)
		{			
			?>
			document.getElementById('province').value="<?=substr($areaid,0,2)?>0000|"+province[<?=substr($areaid,0,2)?>0000];
			
			selProvince('<?=substr($areaid,0,2)?>"0000');			
			document.getElementById('city').value="<?=substr($areaid,0,4)?>00|"+city[<?=substr($areaid,0,4)?>00];
			
			selCity('<?=substr($areaid,0,4)?>00');	
			document.getElementById('county').value='<?=$areaid?>|<?=$row['area']?>';			
			<?
		}
		if($row['level']==1)
		{
			?>					
			document.getElementById('province').value="<?=substr($areaid,0,2)?>0000|"+province[<?=substr($areaid,0,2)?>0000];
			selProvince('<?=substr($areaid,0,2)?>0000');	
			document.getElementById('city').value='<?=$areaid?>|<?=$row['area']?>';
			selCity('<?=substr($areaid,0,4)?>00');	
			<?
		}
		if($row['level']==2)
		{	
			?>
			document.getElementById('province').value='<?=$areaid?>|<?=$row['area']?>';
			selProvince('<?=substr($areaid,0,2)?>0000');	
			<?
		}
	}
	?>
</script>
  
        
		</form>		
		<?
}
?>