<?php
require('./include/process.class.php');
$tclass=new process();
$modulename='WebServices������ˮ';
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
	$sqlW.=" and log_text like '%$word%' ";
	$url.='&word='.$word;
}
if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and b.user_name ='$user_name' ";
	$url.='&user_name='.$user_name;
}
if(!empty($_GET['user_id']))
{
	$user_id=intval($_GET['user_id']);
	$sqlW.=" and b.user_id='$user_id'";
	$url.='&user_id='.$user_id;
}
if(!empty($_GET['fid']))
{
	$fid=intval($_GET['fid']);
	$row1=$db->get_one("select web_id from {member} where user_id='$fid' limit 1");	
	$sqlW.=" and a.FromUserID='".$row1['web_id']."'";
	$row1=null;
	$url.='&fid='.$fid;
}
if(!empty($_GET['c']))
{
	$c=checkPost(strip_tags($_GET['c']));
	$sqlW.=" and b.city='$c'";
	$url.='&c='.$c;
}
if(!empty($_GET['real_name']))
{
	$real_name=trim(checkPost(strip_tags($_GET['real_name'])));
	$sqlW.=" and b.real_name like '%$real_name%'";
	$url.='&real_name='.$real_name;
}
if(!empty($_GET['money']))
{
	$money=trim(checkPost(strip_tags($_GET['money'])));
	$sqlW.=" and a.Mony=".$money;
	$url.='&money='.$money;
}
if(!empty($_GET['list']))
{
	$list=(int)$_GET['list'];
	$sqlW.=" and a.Aside1<4";
	$url.='&list='.$list;
}
if(!empty($_GET['starttime']))
{
	$starttime=checkPost(strip_tags($_GET['starttime']));
	$sqlW.=" and a.IncomeTime>='".($starttime)."'";
	$url.='&starttime='.$starttime;
}
if(!empty($_GET['endtime']))
{
	$endtime=checkPost(strip_tags($_GET['endtime']));
	$sqlW.=" and a.IncomeTime<='".($endtime)."'";
	$url.='&endtime='.$endtime;
}


if(!empty($_GET['Aside2']))
{
	$Aside2=intval($_GET['Aside2']);
	$sqlW.=" and a.Aside2=$Aside2";
	$url.='&Aside2='.$Aside2;
}
if(!empty($_GET['Aside1']))
{
	$Aside1=intval($_GET['Aside1']);
	$sqlW.=" and a.Aside1=$Aside1";
	$url.='&Aside1='.$Aside1;
}
if(!empty($_GET['PlateNum']))
{
	$PlateNum=intval($_GET['PlateNum']);
	$sqlW.=" and a.PlateNum=$PlateNum";
	$url.='&PlateNum='.$PlateNum;
}





$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}

$arr_status=array('δ����','������');
$arr_Aside1=array('��ѡ��','ƽ̨�ֺ�','���췵��','�Ŷӷ���','Fbb��������','Z100��������','������������');
$arr_tty=array();

$arr_tty[1]=array('��ѡ��','120 ƽ̨�ֺ�','160 ƽ̨�ֺ�','���ֶ��зֺ�');
$arr_tty[2]=array('��ѡ��','160 ���췵','���ֶ������췵');
$arr_tty[3]=array('��ѡ��','120 �Ŷ�','160 �Ŷ�','���ֶ����Ŷ�');
$arr_tty[4]=array('��ѡ��','FBB1--15�㽱��','FBB�Ƽ�����','FBB�˶�������');
$arr_tty[5]=array('��ѡ��','��̬��������','�Ƽ�����','��꽱��','1--45��','1---50��','�˶�������');
$arr_tty[6]=array('��ѡ��','��������','��������','25��ƻ�����','��������','�˶�������');



if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='excel')
	{
		$savename = date("Y-m-j H:i:s");
		$file_type = "vnd.ms-excel";      
		$file_ending = "xls";  
		header("Content-Type: application/$file_type;charset=big5");   
		header("Content-Disposition: attachment; filename=".$savename.".$file_ending");      
		//header("Pragma: no-cache");		  
		$title = "WebService������ˮ"; 			   
		echo("$title\n");       
		$sep = "\t";   
		$fields=array('�û�ID','�û���','����','����','����','ʱ��');
		foreach($fields as $v)
		{
		  echo $v."\t";	
		}      
		echo ("\n");	
		$sql="select a.*,b.* from {process} a join {member} b on a.UserID=b.web_id where $sqlW order by a.IncomeTime desc,ProcessID desc";  
		
		$result=$db->get_all($sql);
		foreach($result as $row)
		{
			$Aside1=$row['Aside1'];
			$Aside2=$row['Aside2'];
			if($Aside1<4)
			{
				$money=	$row['Mony'];
			}
			else
			{
				$money=getmoney($row['Mony']);	
			}		
			$schema_insert = "";
			$schema_insert .= '��'.getuserno($row['user_id']).$sep; 	
			$schema_insert .= '��'.$row["user_name"].$sep; 		  
			$schema_insert .= $money.$sep; 		  
			$schema_insert .= $arr_Aside1[$Aside1].$sep; 
			$schema_insert .= $arr_tty[$Aside1][$Aside2].$sep; 
			$schema_insert .= $row["IncomeTime"].$sep; 		  
			$schema_insert = str_replace($sep."$", "", $schema_insert);       
			$schema_insert .= "\t";       
			echo $schema_insert;       
			echo "\n";       
		}            	
		$result=null;
		exit();
	}
}

pageTop($modulename.'����');
echo "<script>";
echo 'var arr_tty = new Array();';
foreach($arr_tty as $i=>$v)
{	
	$str=implode("','",$v);
	echo "arr_tty[$i]=new Array('$str');\r\n";
}
echo "</script>";
if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
    <script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>
    
    <div style="margin-bottom:5px;">
    
    <input type="button" value="����FBB" onClick="C_Cal(1,this)">
    <input type="button" value="����Z100" onClick="C_Cal(2,this)">
    <input type="button" value="������������" onClick="C_Cal(3,this)">
    <input type="button" value="���㷵��" onClick="C_Cal(4,this)">
    <input type="button" value="��ȡFBB" onClick="GetListInfo(1)">
    <input type="button" value="��ȡZ100" onClick="GetListInfo(2)">
    <input type="button" value="��ȡ��������" onClick="GetListInfo(3)">
    <input type="button" value="��ȡ����" onClick="GetListInfo(4)">
     <!--<input type="button" value="�������" onClick="clearprocess()">
     <input type="button" value="���WEBServices����" onClick="window.open('http://<?=$_S['canshu']['webservip']?>/cclear.asp')">-->
     
    
    <a href="<?=$url?>&func=excel">����EXCEL</a>
    </div>
	<div style="margin-bottom:5px;">
	<form method="GET">   
    
        ��ԱID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="5"/>	
    	�û�����<input type="text" name="user_name" value="<?=$user_name?>" size="5"/>
        ���룺<input type="test" name="money" size="4" value="<?=$money?>"/>
        
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
        ��ԴID��<input type="text" name="fid" value="<? if(!empty($fid)){echo getuserno($fid);}?>" size="3"/>
    	<select name="list">
        	<option value="">ѡ���Ŷ�</option>
            <option value="1" <? if($list==1){echo 'selected';}?>>�Ŷ�����</option>
        </select>
        <select name="Aside1" onchange="selAside2(this.value)">
            <?
            foreach($arr_Aside1 as $i=>$v)
			{
				$ch='';
				if($Aside1==$i)  $ch='selected'; 
				?>
                <option value="<?=$i?>" <?=$ch?>><?=$arr_Aside1[$i]?></option>
                <?	
			}
			?>
        </select>
        <select id='Aside2' name="Aside2"></select>
        
        �ڼ���<input type="text" size="3" name="PlateNum" value="<?=$PlateNum?>"/>
    	<input id="starttime"  name="starttime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'starttime'});" value="<?=$starttime?>">
        <input id="endtime"  name="endtime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'endtime'});" value="<?=$endtime?>">
        
		<input type="submit" value="ɸѡ����">
		<input type="hidden" name="act" value="<?=$act?>">   
   
     </form>
    <script language="javascript" src="include/js/jquery.js"></script>
    <script language="javascript">
    	function selAside2(val)
		{
			sel=document.getElementById('Aside2');
			if(val!='0')
			{		
			    sel.options.length=0;		
				for(v in arr_tty[val])
				{
					sel.options.add(new Option(arr_tty[val][v],v));				
				}
			}
			else
			{
				sel.options.length=0;	
			}
		}
		<?
		if(!empty($Aside1))
		{
			echo "selAside2($Aside1);";	
			if(!empty($Aside2))
			{
				echo "document.getElementById('Aside2').value=$Aside2;";	
			}
		}
		?>
    	function GetListInfo(type)
		{
			$.post("ajax.php?func=GetListInfo",{type:type},function(result){
				alert(result);
				window.location.reload();
			});
		}
		function C_Cal(t,o)
		{
			$.post("ajax.php?func=C_Cal",{t:t},function(result){
				alert(result);
			})
			o.disabled = true;

		}
		function clearprocess()
		{
			if(window.confirm('ȷ��Ҫ�����������'))
			{
				$.post("ajax.php?func=clearprocess",{suggest:1},function(result){
					alert(result);
				});	
			}
		}
    </script>

    </div>
	<?	
	$PageSize = 15;  //ÿҳ��ʾ��¼��	
	$row=$db->get_one("select count(*) as count,sum(Mony) as moneys from {process} a join {member} b on a.UserID=b.web_id where $sqlW");
	//echo "select count(*) as count from {process} a join {member} b on a.UserID=b.web_id where $sqlW";
	$RecordCount = $row['count'];//��ȡ�ܼ�¼��
	$moneys=$row['moneys'];
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
		$sql="select a.*,b.* from {process} a join {member} b on a.UserID=b.web_id where $sqlW order by a.IncomeTime desc,ProcessID desc limit $StartRow,$PageSize";
		
		
		$result=$db->get_all($sql);	
		
		/*
		Aside3  �̳����ѵ� �ʽ�ID
Aside2 Ϊ ������

		*/
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('�û�ID','�û���','����','������Դ','����','����','�ڼ���','ʱ��','״̬','��վ'));	
		$si=0;
		foreach ($result as $row)
		{
			$si++;
			$user_id=$row['user_id'];
			$Aside1=$row['Aside1'];
			$Aside2=$row['Aside2'];
			if($Aside1<4)
			{
				$money=	$row['Mony'].'����';
			}
			else
			{
				$money=getmoney($row['Mony']).'Ԫ';	
			}
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<?=getuserno($row['user_id'])?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['user_name']?></td>
            	
                <td align='left'><?=$money?></td>
                <td><?
				//echo $row['FromUserID'];
				if(!empty($row['FromUserID']))
				{
					$row1=$db->get_one("select user_id,user_name,tuijianid from {member} where web_id='".$row['FromUserID']."' limit 1");
					echo $row1['user_name'].'['.$row1["user_id"].']';
					echo '<---'.$row1['tuijianid'];
					$row1=null;
				}
				?></td>
                <td align='left'><?=$arr_Aside1[$Aside1]?></td>
     			<td align='left'><?=$arr_tty[$Aside1][$Aside2]?>(<?=$row['Aside1']?>.<?=$row['Aside2']?>,<?=$row['Aside3']?>)</td>
                <td align='left'><? if($row['Aside1']==6){echo $row['PlateNum'];}?></td>
                
                <td align='center'><?=$row['IncomeTime']?></td>
  
                 <td align='center'><?=$arr_status[$row['status']]?></td>              
                
                <td align="center"><?=$city[$row['city']];?></td>
				
			</tr>
			<?		
		}
		?>
        </form></table>
		<div class="line">
        <?
        if($si<$PageSize) {echo "��{$si}����";}
		echo "{$moneys}���֣�".getmoney($moneys).'Ԫ';
		?>
        <?=page($RecordCount,$PageSize,$page,$url)?></div>
		<?php
	}
	else
	{
		echo "<div><br>&nbsp;&nbsp;�����ݣ�</div>";
	}
}
else
{
	
}
?>