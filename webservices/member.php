<?php
require('./include/member.class.php');
$tclass=new member();
$modulename='��Ա����';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='del')
	{
		$tclass->delete(intval($_GET['user_id']));
	}
	elseif($func=='webservices')
	{
		$user_id=$_REQUEST['user_id'];
		$web_id= webService('Regist');
		if($web_id= webService('Regist'))
		{
			$db->query("update {member} set web_id='$web_id' where user_id=$user_id limit 1");	
			showMsg('ע��ɹ���'); exit();
		}
		else
		{
			showMsg('ʧ�ܣ�');exit();
		}	
	}
	elseif($func=='add' || $func=='edit')
	{
		if($tclass->pass($_POST))
		{
			if($func=='add')	
				$tclass->add($_POST);
			elseif($func=='edit')
				$tclass->edit($_POST);
		}
		else
		{
			showMsg($tclass->errmsg);exit();	
		}
	}
	if($func=='excel')
	{
		$savename = date("Y-m-j H:i:s");
		$file_type = "vnd.ms-excel";      
		$file_ending = "xls";  
		header("Content-Type: application/$file_type;charset=big5");   
		header("Content-Disposition: attachment; filename=".$savename.".$file_ending");      
		//header("Pragma: no-cache");		  
		$title = "��Ա�ʽ��"; 			   
		echo("$title\n");       
		$sep = "\t";   
		$fields=array('�û�ID','�û���','�ʽ�','�����ʽ�','����','�����ʽ�','�����ʽ�','��������','���ö��','����');
		foreach($fields as $v)
		{
		  echo $v."\t";	
		}      
		echo ("\n");	

		
		$sql="select user_id,user_name, money,money_dj,duihuanjifen,dongjiejifen,suoding_money,suoding_jifen ,city,zengjin,t from {my_money} where money<>0 or money_dj<>0  or duihuanjifen<>0 or dongjiejifen<>0 or suoding_money<>0 or suoding_jifen<>0  or zengjin<>0 order by user_id";
		$result=$db->get_all($sql);
		foreach($result as $row)
		{		
			$schema_insert = "";
			$schema_insert .= '��'.getuserno($row['user_id']).$sep; 	
			$schema_insert .= '��'.$row["user_name"].$sep; 		  
			$schema_insert .= $row["money"].$sep; 	
			$schema_insert .= $row["money_dj"].$sep; 	
			$schema_insert .= $row["duihuanjifen"].$sep; 	
			$schema_insert .= $row["dongjiejifen"].$sep;
			
			$schema_insert .= $row["suoding_money"].$sep;
			$schema_insert .= $row["suoding_jifen"].$sep; 	
			
			$schema_insert .= $row["zengjin"]/2.52.$sep; 	
			
			$schema_insert .= $row["t"].$sep; 		  
			
			$schema_insert = str_replace($sep."$", "", $schema_insert);       
			$schema_insert .= "\t";       
			echo $schema_insert;       
			echo "\n";       
		}            	
		$result=null;
		exit();
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
	$sqlW.=" and (a.user_name like '%$word%' or a.email='%$word%' or a.real_name='%$word%') ";
	$url.='&word='.$word;
}   
if(!empty($_GET['c']))
{
	$c=checkPost(strip_tags($_GET['c']));
	$sqlW.=" and a.city='$c'";
	$url.='&c='.$c;
}
if(!empty($_GET['user_id']))
{
	$user_id=intval($_GET['user_id']);
	$sqlW.=" and a.user_id='$user_id'";
	$url.='&user_id='.$user_id;
}

if(!empty($_GET['real_name']))
{
	$real_name=trim(checkPost(strip_tags($_GET['real_name'])));
	$sqlW.=" and a.real_name like '%$real_name%'";
	$url.='&real_name='.$real_name;
}
if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and a.user_name='$user_name'";
	$url.='&user_name='.$user_name;
}
if(!empty($_GET['tuijianid']))
{
	$tuijianid=(int)($_GET['tuijianid']);
	$sqlW.=" and a.tuijianid='$tuijianid'";
	$url.='&tuijianid='.$tuijianid;
}
if(!empty($_GET['lishuid']))
{
	$lishuid=(int)($_GET['lishuid']);
	$sqlW.=" and a.lishuid='$lishuid'";
	$url.='&lishuid='.$lishuid;
}
if(!empty($_GET['starttime']))
{
	$starttime=checkPost(strip_tags($_GET['starttime']));
	$sqlW.=" and a.reg_time>='".strtotime($starttime)."'";
	$url.='&starttime='.$starttime;
}
if(!empty($_GET['endtime']))
{
	$endtime=checkPost(strip_tags($_GET['endtime']));
	$sqlW.=" and a.reg_time<='".strtotime($endtime)."'";
	$url.='&endtime='.$endtime;
}
$arr_le=array();
if(!empty($_GET['level1']))
{
	if(is_array($_GET['level1']))
	{
		$level1=implode(',',$_GET['level1']);
	}
	else
	{
		$level1=$_GET['level1'];	
	}
	$sqlW.=" and a.level like '%$level1%'";
	$url.='&level1='.$level1;
	$arr_le=explode(',',$level1);
}
$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}


pageTop($modulename.'����');
/*$result=$db->get_all("select user_id,lishuid from {member} where lishuid!=''");
foreach($result as $row)
{
	$row_1=$db->get_one("select user_id from {member} where user_name='".$row['lishuid']."' limit 1");
	if($row_1)
	{
		$db->query("update {member} set lishuid=".$row_1['user_id']." where user_id=".$row['user_id']." limit 1");	
	}	
}*/

//$post_data=array("ID"=>'0c092667-5074-4ac1-94f5-9eda26cf68f8',"DPID"=>'1d5bbd22-2fa4-4bc6-99b1-f6c88d033dff','Weights'=>236);
//echo webService('RegistAddDParent',$post_data);
/*
 RegistAddDParent(string ID, string DPID, int Weights)
*/

if(empty($_GET['ui']))
{
?>
<div class="div_title"><?=getHeadTitle(array($modulename.'����'=>''))?>&nbsp;&nbsp;<a href="?act=<?=$act?>&ui=add">���</a> <a href="?act=member_jh">�鿴�ṹͼ</a>  <a href="<?=$url?>&func=excel">�����ǿ��ʽ��ʺ�</a></div>	
<link href="include/js/flexigrid/css/flexigrid.css" type="text/css" rel="stylesheet" />
<script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" charset="utf-8" src="include/js/jquery.js"></script>
<script language="javascript" charset="utf-8" src="include/js/flexigrid/flexigrid.pack.js"></script>
<script language="javascript">
$("document").ready(function() 
{
	//$('#table1').flexigrid({height:'auto'});
});
</script>
	<div style="margin-bottom:5px;">
	<form method="GET">	
    	��ԱID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
        �û�����<input type="text" name="user_name" value="<?=$user_name?>" size="15"/>
        
        �Ƽ���ID��<input type="text" name="tuijianid" value="<?=$tuijianid?>" size="4"/>
        ������ID��<input type="text" name="lishuid" value="<?=$lishuid?>" size="4"/>
        ������<input type="text" name="real_name" value="<?=$real_name?>" size="15"/>
        <select name="c">
        <option value="">ѡ���վ</option>
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
        <br />
        �û�����Email��������<input type="text" name="word" value="<?=$word?>">&nbsp;
        <input type="checkbox" value="1" name="level1[]" <? if(in_array(1,$arr_le)){echo 'checked';}?>/>����
        <input type="checkbox" value="2" name="level1[]" <? if(in_array(2,$arr_le)){echo 'checked';}?>/>����
		<input type="submit" value="ɸѡ����">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
	<?	
	$PageSize = 15;  //ÿҳ��ʾ��¼��	

	$row=$db->get_one("select count(*) as count from {member} a where $sqlW");
	//$RecordCount = $tclass->getcount($sqlW);//��ȡ�ܼ�¼��
	$RecordCount = $row['count'];//��ȡ�ܼ�¼��
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

		//$result=$tclass->getall($StartRow,$PageSize,'user_id desc',$sqlW);	
		$sql="select a.*,b.bank_name,b.bank_add,b.bank_sn,b.bank_username,b.money,b.zengjin,b.money_dj,b.duihuanjifen,b.dongjiejifen,b.suoding_money,b.suoding_jifen,b.qianbiku,b.t from {member} a left join {my_money} b on a.user_id=b.user_id where $sqlW order by a.user_id desc limit $StartRow,$PageSize";
		$result=$db->get_all($sql);	
		?>	
		<table cellpadding="4" cellspacing="1" width="3000" bgcolor="#CCCCCC" id="table1">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('��Աid','�û���','��ʵ����','ע��ʱ��','�Ƽ���id','������id','�ʽ�','�����ʽ�','����','�������','����','vip','�����ʽ�','��������','����','web_id','����¼','��¼����','����վ','��������','��ʱͨѶ','��������','��������','�����ʻ�','�ʻ�����'));	
		foreach ($result as $row)
		{
			$user_id=$row['user_id'];		
			if((int)($row['t']))
			{
				$t='VIP'.($row['t']-1);
			}
			?>
			<tr <?=getChangeTr()?>>
            	<td align='center'><?=getuserno($row['user_id'])?></td>
                <td align='center'><?=$row['user_name']?></td>
				<td align='left'>&nbsp;&nbsp;<?=$row['real_name']?></td>
                
                <td align="center"><?=date('Y-m-d H:i:s',$row['reg_time'])?></td>
                
                <td align="left"><?=$row['tuijianid'];?></td>
                <td align="left"><?=$row['lishuid'];?></td>
				
                
                <td align="left"><?=$row['money'];?></td>
                <td align="left"><?=$row['money_dj'];?></td>
                <td align="left"><?=$row['duihuanjifen'];?></td>
                <td align="left"><?=$row['dongjiejifen'];?></td>
                <td align="left"><?=$row['zengjin']/$_S['canshu']['jifenxianjin']?>Ԫ</td>
                <td align="left"><?=$t?></td>
                <td align="left"><?=$row['suoding_money'];?></td>
                <td align="left"><?=$row['suoding_jifen'];?></td>
                
				<td align="center"><nobr>
					
					
                    <a href='<?=$url?>&ui=edit&user_id=<?=$row['user_id']?>'>�༭</a>
                    <?
                    if(empty($row['web_id']))
					{
					?>
                    <a href='<?=$url?>&func=webservices&user_id=<?=$row['user_id']?>'>ע��WebID</a>
                    <?	
					}
					?>
                   <!-- <a href='<?=$url?>&func=webservices&user_id=<?=$row['user_id']?>'>ע��WebServices</a><a href='?act=user_moneyadd&user_id=<?=$row['user_id']?>'>�����û��ʽ�</a>
                    <a href='?act=my_moneylog&user_name=<?=$row['user_name']?>'>�ʽ���ˮ</a>-->
                    <?
                    	$row1=$db->get_one("select id from {my_webserv} where user_id=".$row['user_id']." limit 1");
				
			       
						if(!$row1)
						{
							echo "<a href='?act=user_buy&user_id=".$row['user_id']."'>�����ײ�</a>";
						}
						$row1=null;
						
					?>
                    
                    
                    
                    <a onclick="return confirm('ȷ��Ҫɾ����')" href="<?=$url?>&func=del&user_id=<?=$row["user_id"]?>"></a>
                    </nobr>
                    </td>
                    
                    <td align="left"><?=$row['web_id'];?></td>
                    <td align='left'><? if(!empty($row['last_login'])){echo date('Y-m-d H:i:s',$row['last_login']+3600*8);}?><br><?=$row['last_ip']?></td>
                <td align='left'><?=$row['logins']?></td>
				<td align="left"><?=$city[$row['city']];?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['email']?></td>
                <td align='left'>QQ:<?=$row['im_qq']?><br>MSN:<?=$row['im_msn']?></td>
                    <td align="left"><?=$row['bank_name'];?></td>
                <td align="left"><?=$row['bank_add'];?></td>
                <td align="left"><?=$row['bank_sn'];?></td>
                <td align="left"><?=$row['bank_username'];?></td>
                
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
		echo "<div><br>&nbsp;&nbsp;�����ݣ�,���'<a href='$url&ui=add'>���</a>'�¼�¼��</div>";
	}
}
else
{
	$user_id=intval($_GET['user_id']);
	echo '<form method="POST"  enctype="multipart/form-data">';
	echo '<input type="hidden" name="url" value="'.$url.'">';
	if(empty($user_id))
	{
		$arr=array($modulename=>$url,'���'.$modulename=>'');
		echo '<input type="hidden" name="func" value="add">';
		$email='1234@163.com';
	}
	else
	{
		$arr=array($modulename.'����'=>$url,'�༭'.$modulename=>'');
		echo '<input type="hidden" name="func" value="edit">';
		echo "<input type='hidden' name='user_id' value='$user_id'>";
		
		//$row=$db->get_one("select * from {member} where user_id=$user_id limit 1");
		$sql="select a.*,b.bank_name,b.bank_add,b.bank_sn,b.bank_username from {member} a left join {my_money} b on a.user_id=b.user_id where a.user_id=$user_id limit 1";
		$row=$db->get_one($sql);
		
		$readonly='readonly';//�Ƽ��˲��ɱ༭
		$level=$row['level'];
		$arr_level=explode(',',$level);
		if(in_array(1,$arr_level)) $level1='checked';
		if(in_array(2,$arr_level)) $level2='checked';
		
		$email=$row['email'];
		if(empty($email)){$email='1234@163.com';}
		echo "<input type='hidden' name='web_id' value='".$row['web_id']."'>";
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="<?=$url?>">���ع���</a></div>
		
        
        
     
    <table class="infoTable">
      <tbody><tr>
        <th class="paddingT15"> ��Ա��:</th>
        <td class="paddingT15 wordSpacing5"><input <?=$readonly?>  id="user_name" type="text" name="user_name" value="<?=$row['user_name']?>">
          
                  </td>
      </tr>
      <tr>
        <th class="paddingT15"> ����:</th>
        <td class="paddingT15 wordSpacing5"><input  name="password" value="" type="password" id="password">
                  </td>
      </tr>
      <tr>
        <th class="paddingT15"> ֧������:</th><!--fu66311183sc-->
        <td class="paddingT15 wordSpacing5"><input  name="zf_pass" value="" type="password" id="zf_pass">
                  </td>
      </tr>
      
      
      <tr>
        <th class="paddingT15"> ��������:</th>
        <td class="paddingT15 wordSpacing5"><input  name="email" type="text" id="email" value="<?=$email?>">
                 </td>
      </tr>
      <tr>
        <th class="paddingT15"> ��ʵ����:</th>
        <td class="paddingT15 wordSpacing5"><input  name="real_name" type="text" id="real_name" value="<?=$row['real_name']?>">        </td>
      </tr>
      <tr>
        <th class="paddingT15"> �Ա�:</th>
        <td class="paddingT15 wordSpacing5"><p>
            <label>
            <input name="gender" type="radio" value="0" checked="checked">
            ����</label>
            <label>
            <input type="radio" name="gender" value="1" <? if($row['gender']==1){echo 'checked';}?>>
            ��</label>
            <label>
            <input type="radio" name="gender" value="2" <? if($row['gender']==2){echo 'checked';}?>>
            Ů</label>
          </p></td>
      </tr>
      <tr>
        <th class="paddingT15"> QQ:</th>
        <td class="paddingT15 wordSpacing5"><input  name="im_qq" type="text" id="im_qq" value="<?=$row['im_qq']?>">        </td>
      </tr>
      <tr>
        <th class="paddingT15"> MSN:</th>
        <td class="paddingT15 wordSpacing5"><input  name="im_msn" type="text" id="im_msn" value="<?=$row['im_msn']?>">        </td>
      </tr>
	  <tr>
        <th class="paddingT15"> ����վ:</th>
        <td class="paddingT15 wordSpacing5"><select name="city">   
        <?
        foreach($city_result as $c)
		{
			$sel='';
			if($c['city_id']==$row['city']) $sel='selected';
			echo "<option value='".$c['city_id']."' $sel>".$c['city_name']."</option>";
		}
		$city_result=null;
		?>
        </select>
          </td>
      </tr>
      <tr>
        <th class="paddingT15"> �Ƽ���ID:</th>
        <td class="paddingT15 wordSpacing5">
        	
        	<input  name="tuijianid" type="text"  id="tuijianid" <? if(!empty($row['tuijianid'])){;}?> value="<?=$row['tuijianid']?>">   </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> ������ID:</th>
        <td class="paddingT15 wordSpacing5">
        	
        	<input  name="lishuid" type="text" <? if(!empty($row['lishuid'])){;}?>  id="lishuid" value="<?=$row['lishuid']?>">        </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> ע���������û���:</th>
        <td class="paddingT15 wordSpacing5">
        	
        	<input  name="yaoqing_id" type="text" <? if(!empty($row['lishuid'])){;}?>  value="<?=$row['yaoqing_id']?>">        </td>
      </tr>
      
      
      
      <tr>
        <th class="paddingT15"> ��������:</th>
        <td class="paddingT15 wordSpacing5"> <input  name="bank_name" type="text"  value="<?=$row['bank_name']?>">        </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> ��������:</th>
        <td class="paddingT15 wordSpacing5"> <input  name="bank_add" type="text"  value="<?=$row['bank_add']?>">        </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> �����ʻ���</th>
        <td class="paddingT15 wordSpacing5"> <input  name="bank_sn" type="text"  value="<?=$row['bank_sn']?>">        </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> �ʻ�����:</th>
        <td class="paddingT15 wordSpacing5"> <input  name="bank_username" type="text"  value="<?=$row['bank_username']?>">        </td>
      </tr>
      <tr>
      	<th>���ͣ�</th><td>�����̼�<input type="checkbox" value="1" name="level[]" <?=$level1?> />
      ������<input type="checkbox" value="2" name="level[]" <?=$level2?>/></td>
      </tr>


        <th></th>
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="�ύ">
          <input class="formbtn" type="reset" name="Reset" value="����">        </td>
      </tr>
   </table>
 
        
		</form>		
		<?
}
?>