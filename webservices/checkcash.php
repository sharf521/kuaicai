<?php
require('./include/my_moneylog.class.php');
$tclass=new my_moneylog();
$modulename='�������';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='type=3';

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
	$sqlW.=" and user_name ='$user_name' ";
	$url.='&user_name='.$user_name;
}


if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='edit')
	{
		
		if($_POST['caozuo']==1)
		{
			$id=$_POST['id'];
			$city=$_POST['city'];
			$level=$_POST['level'];
			$web_id=$_POST['web_id'];
			$user_id=$_POST['user_id'];
			$user_name=$_POST['user_name'];
			$money_dj=$_POST['money_dj'];
			$money_feiyong=$_POST['money_feiyong'];		
			
			$user=$db->get_one("select * from {my_money} where user_id='$user_id' limit 1");
			if(!$user)
			{
				showMsg("��Ա{$user_id}�����ڣ�");exit();	
			}			
			$db->query("update {my_moneylog} set status='�����' where id=$id limit 1");//����״̬			
			$db->query("update {my_money} set money_dj=money_dj-$money_dj where user_id='$user_id' limit 1");//�����û������ʽ�
			
			$lv31=$money_dj*$_S['canshu']['duihuanxianjinfeilv'];//31% �ķ���
			
			$arr=array(
				'money'=>0,
				'jifen'=>0,
				'money_dj'=>'-'.$money_dj,
				'jifen_dj'=>0,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>14,
				's_and_z'=>2,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$user['money'],
				'dq_money_dj'=>$user['money_dj']-$money_dj,
				'dq_jifen'=>$user['duihuanjifen'],				
				'dq_jifen_dj'=>$user['dongjiejifen'],
				'beizhu'=>"������ɣ�������־ID".$id
			);			
			$db->insert('{moneylog}',$arr);
			
			
			if(!empty($money_feiyong))
			{
				//�������ʻ�
				$arr=array(
					'money'=>$money_feiyong,
					'user_id'=>$user_id,
					'user_name'=>$user_name,
					'type'=>14,
					's_and_z'=>1,
					'time'=>date('Y-m-d H:i:s'),
					'zcity'=>$city,
					'dq_money'=>$_S['canshu']['zong_money']+$money_feiyong,
					'dq_jifen'=>$_S['canshu']['zong_jifen'],
					'beizhu'=>$a_username.'|'.$a_userid."��Ա{$user_name}���ַ��ã�������־ID".$id
				);
				$db->insert('{accountlog}',$arr);
				$db->query("update {canshu} set zong_money=zong_money+$money_feiyong where id=1");//�������ʻ�		
			}
			//�������ʻ�
			$arr=array(
				'money'=>$lv31,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>14,
				's_and_z'=>1,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$_S['canshu']['zong_money']+$lv31,
				'dq_jifen'=>$_S['canshu']['zong_jifen'],
				'beizhu'=>$a_username.'|'.$a_userid."��Ա{$user_name}����31%���ã�������־ID".$id
			);
			$db->insert('{accountlog}',$arr);
			$db->query("update {canshu} set zong_money=zong_money+$lv31 where id=1");//�������ʻ�
			
			
			
			adminlog("������ֳɹ�����Ա[{$user_name}]���֣�{$money_dj}Ԫ��������־ID��{$id}");	
			
			webService('C_Consume',array("ID"=>$web_id,"Money"=>getjifen($money_dj),"MoneyType"=>2,"Count"=>1));//����
			c_cal();
				
		}
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


if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
	<div style="margin-bottom:5px;">
	<form method="GET">
    	�û�����<input type="text" name="user_name" value="<?=$user_name?>"/>

        
 
    	
        <input type="text" name="word" value="<?=$word?>">&nbsp;
		<input type="submit" value="ɸѡ����">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
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

		$result=$tclass->getall($StartRow,$PageSize,'id desc',$sqlW);		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('�û���[�û�ID]','�ʽ�','�����ʽ�','����','�������','����ʱ��','��ǰ���ʽ�','��ǰ�ܶ����ʽ�','��ǰ�ܻ���','��ǰ�ܶ������','���ַ���','����վ','��ע','״̬','����'));	
		
		foreach ($result as $row)
		{
			$user_id=$row['user_id'];
			
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<?=$row['user_name']?> [<?=$row['user_id']?>]</td>
            	
                <td align='left'><?=$row['money']?></td>
                <td align='left'><?=$row['money_dj']?></td>
                 <td align='left'><?=$row['duihuanjifen']?></td>
                 <td align='left'><?=$row['dongjiejifen']?></td>
                 <td align="center"><?=$row['riqi']?><br /><?=date('Y-m-d H:i:s',$row['add_time'])?></td>
                 <td align='left'><?=$row['dq_money']?></td>
                 <td align='left'><?=$row['dq_money_dj']?></td>
                 <td align='left'><?=$row['dq_jifen']?></td>
                 <td align='left'><?=$row['dq_jifen_dj']?></td>
                <td align='left'><?=$row['money_feiyong']?>Ԫ</td>
               <!-- <td align='left'><?=$row['jifen_feiyong']?></td>-->
               
                
                <td align="left">&nbsp;&nbsp;<?=$city[$row['city']];?></td>
                 
				
				<td align='left'><?=$row['log_text']?></td>
                
                <td align='left'><?=$row['status']?></td>
				<td align='left'><?
                if($row['status']=='�ȴ����')
				{
					echo "<a href='?act=$act&ui=check&id=".$row['id']."&user_id=".$row['user_id']."'>���</a>";	
				}
				
				?></td>
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
		echo "<div><br>&nbsp;&nbsp;�����ݣ�</div>";
	}
}
else
{
	$id=$_GET['id'];
	$user_id=$_GET['user_id'];
$row=$db->get_one("select a.*,b.bank_name,b.bank_add,b.bank_sn,b.bank_username from {my_moneylog} a left join {my_money} b on a.user_id=b.user_id where a.id=$id limit 1");
	?>
    <div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;<a href="?act=<?=$act?>">����</a></div>	
    <form method="post">
    <table class="infoTable">
 
            <tbody><tr>        
              <th class="paddingT15">������</th>
              <td class="paddingT15 wordSpacing5"><?=$row['user_name']?></td>
            </tr>
<input type="hidden" name="user_id" value="<?=$row['user_id']?>" />
<input type="hidden" name="user_name" value="<?=$row['user_name']?>" />
<input type="hidden" name="id" value="<?=$row['id']?>" />
<input type="hidden" name="city" value="<?=$row['city']?>" />
<input type="hidden" name="level" value="<?=$row['level']?>" />
<input type="hidden" name="web_id" value="<?=$row['web_id']?>" />
            <tr>
              <th class="paddingT15">������</th>
              <td class="paddingT15 wordSpacing5"><font color="#FF0000"><?=abs($row['money_dj'])?> Ԫ</font>
              <input name="money_dj" type="hidden" value="<?=abs($row['money_dj'])?>"></td>
            </tr>
            <tr>
              <th class="paddingT15">����ʱ��</th>
              <td class="paddingT15 wordSpacing5"><?=$row['riqi']?><br /><?=date('Y-m-d H:i:s',$row['add_time'])?></td>
            </tr>
			<tr>
              <th class="paddingT15">���ַ���</th>
              <td class="paddingT15 wordSpacing5"><font color="#FF0000"><?=abs($row['money_feiyong'])?> Ԫ</font></td>
              <input type="hidden" name="money_feiyong" value="<?=abs($row['money_feiyong'])?>"/>
            </tr>
			<tr>
              <th class="paddingT15">&nbsp;</th>
            </tr>

 
            <tr>
                <th class="paddingT15">�û��������:</th>
                <td class="paddingT15 wordSpacing5"><?=$row['dq_money']?></td>
            </tr>	
			<tr>
                <th class="paddingT15">�û�������:</th>
                <td class="paddingT15 wordSpacing5"><?=$row['dq_money_dj']?></td>
            </tr>
			<tr>
                <th class="paddingT15">��������:</th>
                <td class="paddingT15 wordSpacing5"><?=$row['bank_name'];?></td>
            </tr>
            <tr>
                <th class="paddingT15">��������:</th>
                <td class="paddingT15 wordSpacing5"><?=$row['bank_add'];?></td>
            </tr>
           
			<tr>
                <th class="paddingT15">�����ʺ�: </th>
                <td class="paddingT15 wordSpacing5"><?=$row['bank_sn']?></td>
            </tr>
			<tr>
                <th class="paddingT15">���ֻ���:</th>
                <td class="paddingT15 wordSpacing5"><?=$row['bank_username'];?></td>
            </tr>
	
			<tr>
                <th class="paddingT15">ת�ʳɹ����׺�:</th>
                <td class="paddingT15 wordSpacing5">
				<input name="order_id" type="text" id="order_id" value="0" size="30">
                </td>
            </tr>
			<tr>
                <th class="paddingT15">������־˵��: </th>
                <td class="paddingT15 wordSpacing5">
				<input name="log_text" type="text" id="log_text" value="zhan����������<?=abs($row['money_dj'])?>Ԫ" size="60">
                </td>
            </tr>
			<tr>
                <th class="paddingT15">�Ƿ����:</th>
                <td class="paddingT15 wordSpacing5">
						<input name="caozuo" type="radio" value="1">���
						
						<input name="caozuo" type="radio" value="0" checked="checked">�ݲ����
			
                </td>
            </tr>	
					
        <tr>
            <th></th>
            <td class="ptb20">
                <input class="formbtn" type="submit" name="Submit" value="���" onclick="this.disabled=true;this.form.submit()">
                <input class="formbtn" type="reset" name="Submit2" value="����">
                <input type="hidden" name="func" value="edit" />
            </td>
        </tr>

        </tbody></table>
    
    </form>
    <?
}
?>