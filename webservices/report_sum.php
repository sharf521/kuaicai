<?php
require('./include/process.class.php');
$tclass=new process();
$modulename='WebService������ܱ���';
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
if(!empty($_GET['real_name']))
{
	$real_name=trim(checkPost(strip_tags($_GET['real_name'])));
	$sqlW.=" and b.real_name like '%$real_name%'";
	$url.='&real_name='.$real_name;
}
if(!empty($_GET['c']))
{
	$c=checkPost(strip_tags($_GET['c']));
	$sqlW.=" and b.city='$c'";
	$url.='&c='.$c;
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
if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='edit')
	{
		
		
		
	}
	
	header("location:$url");
	exit();
}
pageTop($modulename.'����');

$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}

$arr_status=array('δ����','������');
$arr_Aside1=array('��ѡ��','ƽ̨���� 5%  �ֺ���','���췵��','�Ŷӷ���','Fbb��������','Z100��������','���ƽ̨��������');
$arr_Aside2=array(
	0=>'��ѡ��',
	1=>'Fbb �û����̽��',
	2=>'Fbb �û�������',
	3=>'Fbb �û����ѻ����ֽ��',
	4=>'׿100 �û���̬���̽��',
	5=>'׿100 �û���̬������',
	6=>'׿100 �û���̬�������ѻ�����',
	7=>'׿100 �û���̬���̽��',
	8=>'�����̳��û����ѽ��',
	9=>'�����̳��û��������',
	10=>'׿100 �û���̬������',
	11=>'׿100 �û���̬�������ѻ�����',
	12=>'���ƽ̨ע���ʽ�',
	13=>'���ƽ̨������',
	14=>'���ƽ̨25��ƻ�',
	15=>'vip ����'
);


$arr_tty=array();

$arr_tty[1]=array('��ѡ��','120 ƽ̨�ֺ�','160 ƽ̨�ֺ�','���ֶ��зֺ�');
$arr_tty[2]=array('��ѡ��','160 ���췵','���ֶ������췵');
$arr_tty[3]=array('��ѡ��','120 �Ŷ�','160 �Ŷ�','���ֶ����Ŷ�');
$arr_tty[4]=array('��ѡ��','FBB1--15�㽱��','FBB�Ƽ�����','FBB�˶�������');
$arr_tty[5]=array('��ѡ��','��̬��������','�Ƽ�����','��꽱��','1--45��','1---50��','�˶�������');
$arr_tty[6]=array('��ѡ��','��������','��������','25��ƻ�����','��������','�˶�������');
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
	<form method="GET">   
    
        ��ԱID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
    	�û�����<input type="text" name="user_name" value="<?=$user_name?>" size="15"/>
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
        ��ԴID��<input type="text" name="fid" value="<? if(!empty($fid)){echo getuserno($fid);}?>" size="8"/>
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

    	<input id="starttime"  name="starttime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'starttime'});" value="<?=$starttime?>">
        <input id="endtime"  name="endtime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'endtime'});" value="<?=$endtime?>">
        
		<input type="submit" value="ɸѡ����">
		<input type="hidden" name="act" value="<?=$act?>">   
     </form>
    </div>
    
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
    	
    </script>
    
	<?	
	{		
		
		$sql="select a.*,b.*,sum(a.Mony) as Mony from {process} a join {member} b on a.UserID=b.web_id where $sqlW group by a.Aside1,a.Aside2";
		echo "<!--". $sql.'-->';
		//exit();
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
		echoTh(array('�û�ID','�û���','����','����','����','ʱ��','״̬','��վ'));	
		
		$money_sum=0;
		foreach ($result as $row)
		{
			$money_sum+=$row['Mony'];
			$Aside1=$row['Aside1'];
			$Aside2=$row['Aside2'];
			if($Aside1<4)
			{
				$money=	$row['Mony'].'����';
			}
			else
			{
				$money=$row['Mony']/$_S['canshu']['jifenxianjin'].'Ԫ';	
			}
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<? if(!empty($user_id)){echo getuserno($user_id);}?></td>
                <td align='left'>&nbsp;&nbsp;<?=$user_name?></td>            	
                <td align='left'><?=$money?></td>                
                <td align='left'><?=$arr_Aside1[$Aside1]?></td>      
                <td align='left'><?=$arr_tty[$Aside1][$Aside2]?>(<?=$row['Aside1']?>.<?=$row['Aside2']?>,<?=$row['Aside3']?>)</td>           
                <td align='center'><?=$starttime?> �� <?=$endtime?></td>  
                <td align='center'><?=$arr_status[$row['status']]?></td>                
                <td align="center"><?=$city[$c];?></td>
			</tr>
			<?		
		}
		?>
        </form></table>
        <?=$money_sum?>���֣�<?=$money_sum/$_S['canshu']['jifenxianjin']?>Ԫ����
		<?php
		
	}

}

?>