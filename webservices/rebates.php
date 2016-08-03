<?php
require('./include/rebates.class.php');
$tclass=new rebates();
$modulename='����';
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
	elseif($func=='close')
	{
		$id=$_GET['id'];
		$listid=$_GET['listid'];
		$status=webService('C_Consume_Close',array('ID'=>$listid));
		$db->query("update {rebates} set status=$status where id=$id limit 1");
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
					showMsg('�û������ڣ�');exit();	 
				}
				else
				{
					if(empty($member['web_id']))
					{
						showMsg('�û�web_id�����ڣ�');exit();	 	
					}
					$_POST['web_id']=$member['web_id'];
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
<script language="javascript">
function tt(v)
{
	document.getElementById('jifen').value=v*252/100;
}
function getmon(listid)
{
	$.post("ajax.php?func=C_Query",{listid:listid},function(result){
		alert(result);
	});	
}
function doRegJD()
{
	var num=$('#num').val();
	var pre=$('#pre').val();

	$('#num').val(num-1);
	if(num>0)
	{
		$.post("ajax.php?func=doRegJD",{num:num,pre:pre},function(result){
			$('#div_rebates').html(result);
			doRegJD();
		});	
	}
}
</script>
<?


if(empty($_GET['ui']))
{
		
?>
<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;<a href="?act=<?=$act?>&ui=add">���</a></div>	
	<div style="margin-bottom:5px;">
	</div>
	<?	
	$PageSize = 15;  //ÿҳ��ʾ��¼��	
	$RecordCount = $tclass->getcount($sqlW);//��ȡ�ܼ�¼��
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
		$arr_type=array('12%','16%','˫����');
		$result=$tclass->getall($StartRow,$PageSize,'id desc',$sqlW);		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('id','��Աid','��Ա��','web_id','����','����','����id','ʱ��','����״̬','����'));	
		
		
		foreach ($result as $row)
		{
			$id=$row['id'];
			$listid=$row['listid'];
		
			?>
			<tr <?=getChangeTr()?>>
            	<td><?=$row['id']?></td>
            	<td align='center'><?=$row['user_id']?></td>
                <td align='center'><?=$row['user_name']?></td>
                <td align='center'><?=$row['web_id']?></td>
                <td align='center'><?=$row['jifen']?></td>
                <td align='left'>&nbsp;&nbsp;<?=$arr_type[$row['type']]?></td>
                <td align='center'><?=$listid?></td>
                <td align="center"><?=$row['createdate']?></td>
                
                
				<td align="center"><?=$row['status']==1?'������':$row['status']?></td>
				<td align="center">	
                <a onClick="return confirm('ȷ��Ҫ������')" href="<?=$url?>&func=close&id=<?=$id?>&listid=<?=$listid?>">��������</a>				
                <a href="javascript:getmon(<?=$listid?>)">��ѯ����</a>   
                <a href="<?=$url?>&ui=edit&id=<?=$id?>">�༭</a>      
                 </td>
			</tr>
			<?		
		}
		?>
        </form></table>
		<div class="line"><?=page($RecordCount,$PageSize,$page,$url)?></div>
        <br /><br />
        
        ע��������<input type="text" id="num" /> �û���ǰ׺��<input type="text" id="pre" size="3" value="JD"/><input type="submit" value="��ʼע��" onclick="doRegJD()"/>
        <div id="div_rebates"></div>
        
		<?php
	}
	else
	{
		echo "<div><br>&nbsp;&nbsp;�����ݣ�</div>";
	}
}
else
{
	$id=intval($_GET['id']);
	echo '<form method="POST"  enctype="multipart/form-data">';
	echo '<input type="hidden" name="url" value="'.$url.'">';
	if(empty($id))
	{
		$arr=array($modulename=>$url,'���'.$modulename=>'');
		echo '<input type="hidden" name="func" value="add">';
	}
	else
	{
		$arr=array($modulename.'����'=>$url,'�༭'.$modulename=>'');
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
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="<?=$url?>">���ع���</a></div>
		
        
        
     
    <table class="infoTable">
      <tbody><tr>
        <th class="paddingT15"> ��ԱID:</th>
        <td class="paddingT15 wordSpacing5"><input name="user_id" value="<?=$row['user_id']?>"> </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> ��Ա��:</th>
        <td class="paddingT15 wordSpacing5"><input name="user_name" value="<?=$row['user_name']?>"> </td>
      </tr>
      <tr>
        <th class="paddingT15"> ��Ǯ:</th>
        <td class="paddingT15 wordSpacing5"><input id="money" value=""  onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this);tt(this.value)"/></td>
      </tr>
      <tr>
        <th class="paddingT15"> ��������:</th>
        <td class="paddingT15 wordSpacing5"><input name="jifen" id="jifen" value="<?=$row['jifen']?>"> </td>
      </tr>
      
      <tr><input type="hidden" name="web_id" value="<?=$row['web_id']?>" />
        <th class="paddingT15"> ��������:</th>
        <td class="paddingT15 wordSpacing5"><select name="type">
        
        <option value="0" <? if($row['type']==0){echo 'selected';}?>>12%</option>
        <option value="1" <? if($row['type']==1){echo 'selected';}?> selected="selected">16%></option>
        <option value="2" <? if($row['type']==2){echo 'selected';}?>>˫����</option>
        </select></td>
      </tr>
     
<tr>
        <th></th>
        
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="�ύ">
          <input class="formbtn" type="reset" name="Reset" value="����">        </td>
      </tr>
   </table>

        
		</form>		
		<?
}
?>