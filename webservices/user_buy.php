<?php
$modulename='�û������ײ�';
$page=intval($_GET['page']);
$user_id=intval($_GET['user_id']);
$url="?act=$act&page=$page";
$sqlW='1=1';

$row=$db->get_one("select money,money_dj,duihuanjifen,dongjiejifen,suoding_money,suoding_jifen,user_name,city from {my_money} where user_id=$user_id limit 1");
	$money=$row['money'];;
	$money_dj=$row['money_dj'];
	$duihuanjifen=$row['duihuanjifen'];
	$dongjiejifen=$row['dongjiejifen'];
	$suoding_jifen=$row['suoding_jifen'];
	$suoding_money=$row['suoding_money'];
	$user_name=$row['user_name'];
	$city=$row['city'];
	$keyongmoney=$money-$suoding_money;
$row=null;
if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='add')
	{
		$buytype=intval($_POST['buytype']);
		if(empty($buytype))
		{
			showMsg('��ѡ���ײͣ�');exit();	
		}
		else
		{
			$ispayprice		=0;
			$ispaydingjin	=intval($_POST['ispaydingjin_'.$buytype]);	
			
/*			if(empty($_POST['tuijianid']))
			{
				showMsg('�Ƽ��˲���Ϊ�գ�');exit();	
			}
			else
			{
				$row=$db->get_one("select user_id from {my_webserv} where user_name='".$_POST['tuijianid']."' limit 1 ");
				if(empty($row))
				{
					showMsg('�Ƽ��˲����ڣ�');exit();		
				}
				else
				{
					$_POST['tuijianid']=$row['user_id'];	
				}
				$row=null;
			}*/
			if(!empty($_POST['tuijianid']))
			{
				if($buytype==7)
				{
					$row=$db->get_one("select user_id from {member} where user_name='".$_POST['tuijianid']."' limit 1 ");	
				}
				else
				{
					$row=$db->get_one("select user_id from {my_webserv} where user_name='".$_POST['tuijianid']."' limit 1 ");	
				}
				if(empty($row))
				{
					showMsg('�Ƽ��˲����ڣ�');exit();		
				}
				else
				{
					$_POST['tuijianid']=$row['user_id'];	
				}
				$row=null;
				
				if($_POST['tuijianid']==$user_id)
				{
					showMsg('�Ƽ��˲�����д�Լ���');exit();	
				}
			}			
			
			$lishuid=$_POST['lishuid'];//�û���
			if(!empty($lishuid))
			{
				$row=$db->get_one("select user_id from {my_webserv} where user_name='$lishuid' limit 1 ");
				if(empty($row))
				{
					showMsg('�����˲����ڣ�');exit();		
				}
				else
				{
					$lishuid=$row['user_id'];
				}				
				$row=null;				
				$row=$db->get_one("select count(*) as count from {member} where lishuid='$lishuid' and user_id !=$user_id");
				if($row['count']>=2)
				{
					showMsg("�û���".$_POST['lishuid']."ֻ������������!");exit();	
				}	
				$row=null;	
				
				if($lishuid==$user_id)
				{
					showMsg('�����˲�����д�Լ���');exit();	
				}
			}

			
			$_S['buydj']=$_S['buytype_dj'][$buytype];

			
			if($keyongmoney < $_S['buydj'])
			{
				showMsg('�������㣡');exit();	
			}
			else
			{
				$db->query("update {my_money} set money=money-".$_S['buydj'].",money_dj=money_dj+".$_S['buydj']." where user_id=$user_id limit 1");
				$db->query("update {member} set tuijianid='".$_POST['tuijianid']."',lishuid='$lishuid' where user_id=$user_id limit 1");
				
				/*$arr=array(
					'money'=>'-'.$_S['buydj'],
					'jifen'=>0,
					'money_dj'=>$_S['buydj'],
					'jifen_dj'=>0,
					'user_id'=>$user_id,
					'user_name'=>$user_name,
					'type'=>29,
					's_and_z'=>2,
					'time'=>date('Y-m-d H:i:s'),
					'zcity'=>$city,
					'dq_money'=>$money-$_S['buydj'],
					'dq_money_dj'=>$money_dj+$_S['buydj'],
					'dq_jifen'=>$duihuanjifen,			
					'dq_jifen_dj'=>$dongjiejifen,
					'beizhu'=>''
				);			
				$db->insert('{moneylog}',$arr);		*/
								
				$db->query("insert into {my_webserv}(user_id,user_name,buytype,ispayprice,ispaydingjin,city,createdate,status)values('$user_id','$user_name','$buytype','$ispayprice','$ispaydingjin',$city,now(),0)");	
				showMsg('����ɹ����ȴ���ˣ�',"?act=member&page=$page");exit();					
			}	           
		}
	}	
	header("location:$url");
	exit();
}
pageTop($modulename.'����');

require './include/member.class.php';
$member=new member();
$result=$member->getone("user_id=".$user_id);

if(empty($_GET['ui']))
{
	//$money=$db->get_one("select ");
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>
   
    <br><br>
    <?=$user_name?> �ʽ�<?=$money?> ���᣺<?=$money_dj?>  �����ʽ�<?=$suoding_money?>
    
    <br>
    <form method="post">
    <table  height="146" border="0" cellpadding="0" cellspacing="1" bgcolor="#a8c7ce">    
    	<input type="hidden" value="<?=$act?>" name="act">
        <input type="hidden" name="func" value="add">
        <input type="hidden" name="page" value="<?=$page?>" />
        <tr>
          <td width="59" height="20" align="center" bgcolor="#FFFFFF" ><strong>ѡ��</strong></td>
          <td width="200" height="61" align="center" bgcolor="#FFFFFF"><strong>�ײ�����</strong></td>
          <td width="129" align="center" bgcolor="#FFFFFF"><strong>�۸�</strong></td>
 
          <td width="121" align="center" bgcolor="#FFFFFF"><strong>����ɶ���</strong></td>
          <td width="127" align="center" bgcolor="#FFFFFF"><strong>�Ƿ����ڽ���</strong></td>
        </tr>
        <?
		foreach($_S['buytype'] as $row)
		{			
			?>
            <tr>            
            <td height="20" align="center" bgcolor="#FFFFFF" ><div align="center"><input type="radio" name="buytype" value="<?=$row['buytype']?>" onclick="selrad(this)" <? if($row['buytype']==0){echo 'checked';}?>/></div></td>
            <td align="center" bgcolor="#FFFFFF" ><table  border="0" cellpadding="0" cellspacing="0" bgcolor="#a8c7ce">
            <tr>
            <td width="167" height="40" bgcolor="#FFFFFF" background="images/menu_bg.jpg"><div align="center"><strong><?=$row['name']?></strong></div></td>
            </tr>
            </table></td>
            <td align="center" bgcolor="#FFFFFF"><?=$row['price']?>��Ԫ</td>   
            <td align="center" bgcolor="#FFFFFF"><p>2��Ԫ</p></td>
            <td align="center" bgcolor="#FFFFFF"><input type="checkbox" value="1" name="ispaydingjin_<?=$row['buytype']?>"/></td>
            </tr>
            <?	
		}
		?>
        <tr><td align="center" bgcolor="#ffffff">�Ƽ����û�����</td><td bgcolor="#ffffff" colspan="4">&nbsp;��&nbsp;<input type="text" value="<?=$member->get_username($result['tuijianid']);?>" name="tuijianid" /></td></tr>
        <tr id="tr_7"><td align="center" bgcolor="#ffffff" >�������û�����</td><td bgcolor="#ffffff" colspan="4">&nbsp;��&nbsp;<input type="text" value="<?=$member->get_username($result['lishuid']);?>" name="lishuid" /></td></tr>
       
        <tr>
          <td height="40" colspan="6" align="center" bgcolor="#FFFFFF">
          <?
          $row=$db->get_one("select id from {my_webserv} where user_id=$user_id limit 1");
		  if($row)
		  {
			?>
            	<input type="submit" value="  �� �� " disabled="disabled"/>�벻Ҫ�ظ��ύ��
            <?
		  }
		  else
		  {
			  ?>
               <input type="submit"  value=" �� �� " /> 
              <?  
		  }
		  ?>
          
         </td>
          </tr>
          <script language="javascript">
          function selrad(o)
		  {
			  if(o.value==7)
			  {
				  document.getElementById('tr_7').style.display='none';
			  }
			  else
			  {
				  document.getElementById('tr_7').style.display='';
			  }
		  }
          
          </script>
        </table></form>
        
 
<?
}
?>
