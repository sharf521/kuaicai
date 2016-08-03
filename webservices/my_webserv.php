<?php
require('./include/my_webserv.class.php');
$tclass=new my_webserv();
require('./include/member.class.php');
$member=new member();
$modulename='购买套餐审核';
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
	elseif($func=='edit')
	{
		if($func=='edit')
		{
			if($_S['kaiguan']['webservice']!='yes')
			{
				showMsg('WebServices接口开关没有开启！');exit();		
			}			
			$id=$_POST['id'];
			$user_id=$_POST['user_id'];
			$money=$_POST['money']*10000;
			$zmonth=intval($_POST['zmonth']);
			$paytype=intval($_POST['paytype']);//优惠
			$buytype=intval($_POST['buytype']);//购买	
			
		

			$member=$db->get_one("select a.web_id,a.user_name,a.city,b.buytype from {member} a join {my_webserv} b on a.user_id=b.user_id where a.user_id=$user_id limit 1");
			if(!$member)
			{
				showMsg($user_id.'用户不存在！');exit();	 
			}
			else
			{
				$web_id=$member['web_id'];
				$user_name=$member['user_name'];	
				$city=$member['city'];	
				$_S['buydj']=$_S['buytype_dj'][$member['buytype']];		
			}
			unset($member);		
			
			$level=$_POST['level'];
			if($level)	
			{
				$level=implode(',',$level);
				$db->query("update {member} set level='$level' where user_id=$user_id limit 1");
			}	
			
			$row=$db->get_one("select money_dj,money,suoding_money,duihuanjifen,dongjiejifen from {my_money} where user_id='$user_id' limit 1");
			$user_money		=$row['money'];
			$user_money_dj	=$row['money_dj'];	
			$user_jifen		=$row['duihuanjifen'];
			$user_jifen_dj	=$row['dongjiejifen'];
			unset($row);
			
			
			$paymoney=$money-$_S['buydj'];//还需付金额
			if($paymoney<0)
			{
				//showMsg('购买金额小于订金！');
				//exit();
			}
			if($paymoney > $user_money)//套餐费 大于 余额
			{
				showMsg('余额不足，请充值！');
				exit();
			}
			
/*			if(empty($_POST['tuijianid']))
			{
				showMsg('推荐人不能为空！');exit();	
			}
			else
			{
				$result=$db->get_one("select web_id from {member} where user_id='".$_POST['tuijianid']."' limit 1 ");				
				$post_data=array("PID"=>$result['web_id'],"ID"=>$web_id);		
				$pid_s=webService('RegistAddParent',$post_data);				
				$result=null;
			}*/
			if(!empty($_POST['tuijianid']))
			{
				$result=$db->get_one("select web_id from {member} where user_id='".$_POST['tuijianid']."' limit 1 ");				
				$post_data=array("PID"=>$result['web_id'],"ID"=>$web_id);		
				$pid_s=webService('RegistAddParent',$post_data);				
				$result=null;	
			}
			
			$lishuid=$_POST['lishuid'];//id;
/*			if($buytype!=7)
			{
				if(empty($lishuid))//添加隶属人
				{
					showMsg('隶属人不能为空！');exit();	
				}
				else
				{
					$result=$db->get_one("select count(*) as count from {member} where lishuid='$lishuid'");
					//$result=$db->get_one("select count(*) as count from {my_webserv} a join {member} b on a.user_id=b.user_id where a.status=1 and b.lishuid='$lishuid'");
					if($result['count']>2)
					{
						showMsg("用户{$lishuid}只能有两个隶属!");exit();	
					}	
					$result=null;
					
					$result=$db->get_one("select web_id from {member} where user_id='$lishuid' limit 1 ");
					$post_data=array("ID"=>$web_id,"DPID"=>$result['web_id'],'Weights'=>$user_id);
					$dpid_s=webService('RegistAddDParent',$post_data);
					$result=null;
				}
			}*/			
			if(!empty($lishuid))
			{				
				$result=$db->get_one("select count(*) as count from {member} where lishuid='$lishuid'");
				if($result['count']>2)
				{
					showMsg("用户{$lishuid}只能有两个隶属!");exit();	
				}	
				$result=null;
				
				$result=$db->get_one("select web_id from {member} where user_id='$lishuid' limit 1 ");
				$post_data=array("ID"=>$web_id,"DPID"=>$result['web_id'],'Weights'=>$user_id);
				$dpid_s=webService('RegistAddDParent',$post_data);
				$result=null;
			}
			
			//更改帐户余额
			$db->query("update {my_money} set money_dj=money_dj-".$_S['buydj']." ,money=money-$paymoney where user_id='$user_id' limit 1");
			//用户金钱流水
			$dq_money=$user_money-$paymoney;
			$dq_money_dj=$user_money_dj-$_S['buydj'];
			$arr=array(
				'money'=>'-'.$money,
				'jifen'=>0,
				'money_dj'=>0,
				'jifen_dj'=>0,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>36,
				's_and_z'=>2,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$dq_money,
				'dq_money_dj'=>$dq_money_dj,
				'dq_jifen'=>$user_jifen,			
				'dq_jifen_dj'=>$user_jifen_dj,
				'beizhu'=>'购买套餐'
			);			
			$db->insert('{moneylog}',$arr);	
			
			if($paytype<4)
			{
				$type=2;//双队列
			}
			else
			{
				$type=1;//16%
			}			
			if($buytype<3)//帝国版(1550万)  企业版(550万)  行业龙头(350万
			{
				$returnjifen=getjifen($_S['buytype'][$buytype]['price']*10000);//6折也是返回全款  210-->350
			}
			else
			{
				$returnjifen=getjifen($money);	
			}	
			$consume_id=webService('C_Consume',array("ID"=>$web_id,"Money"=>$returnjifen,"MoneyType"=>$type,"Count"=>1));//返利全部
			$arr=array(
				'cai_id'=>0,
				'gong_id'=>$user_id,
				'consume_id'=>$consume_id,
				'type'=>$type,
				'time'=>date('Y-m-d H:i:s'),
				'status'=>0,
				'money'=>$returnjifen,
				'jifen'=>0,
				'city'=>$city,
				'gh_id'=>0
			);
			$db->insert('{webservice_list}',$arr);
			
			$arr_acc=array(
				'money'=>$money,
				'jifen'=>0,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>36,
				's_and_z'=>1,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$_S['canshu']['zong_money']+$money,
				'dq_jifen'=>$_S['canshu']['zong_jifen'],
				'beizhu'=>''
			);			
			$db->insert('{accountlog}',$arr_acc);					
			$db->query("update {canshu} set zong_money=zong_money+$money where id=1 limit 1");//更新总账户资金
			
			$remark=$a_username.'|'.$a_userid."审核会员".$user_name."购买套餐";
			if($buytype<3)
			{				
				$arr=array(
					'paymoney'=>$money,
					'buytype'=>$buytype,
					'paytype'=>$paytype,
					'fbb'=>0,
					'zhuo'=>0,
					'zmonth'=>0,
					'zengjin'=>0,
					'liubao'=>0,
					'checktime'=>date('Y-m-d H:i:s'),
					'status'=>1,
					'remark'=>$remark
				);
				$db->update('{my_webserv}',$arr,"id=$id");				
				/*
				$ar['fbb_s']=webService('Fbb_Regist_Money',array("ID"=>$web_id,"Money"=>20000));				
				$ar['zhuo_s']=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>20000,'Month'=>$zmonth));						
				$ar['liubao_s']=webService('JD_Regist_Money',array("ID"=>$web_id,"Money"=>5500));//六保	
				$ar['zengjin_s']=webService('Vip_Money',array("ID"=>$web_id,"Money"=>2000));//增进
				$db->update('{my_webserv}',$ar,"id=$id");
				*/	
			}
			elseif($buytype==3)
			{							
				$arr=array(
					'paymoney'=>$money,
					'buytype'=>$buytype,
					'paytype'=>$paytype,
					'fbb'=>20000,
					'zhuo'=>0,
					'zmonth'=>$zmonth,
					'zengjin'=>2000,
					'liubao'=>5500,
					'checktime'=>date('Y-m-d H:i:s'),
					'status'=>1,
					'remark'=>$remark
				);
				$db->update('{my_webserv}',$arr,"id=$id");				
				$ar['fbb_s']=webService('Fbb_Regist_Money',array("ID"=>$web_id,"Money"=>20000));				
				//$ar['zhuo_s']=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>2000,'Month'=>$zmonth));	
				$ar['liubao_s']=webService('JD_Regist_Money',array("ID"=>$web_id,"Money"=>5500));
				$ar['zengjin_s']=webService('Vip_Money',array("ID"=>$web_id,"Money"=>2000));	
				$db->update('{my_webserv}',$ar,"id=$id");			
			}
			elseif($buytype==4)
			{				
				$arr=array(
					'paymoney'=>$money,
					'buytype'=>$buytype,
					'paytype'=>$paytype,
					'fbb'=>2000,
					'zhuo'=>0,
					'zmonth'=>$zmonth,
					'zengjin'=>2000,
					'liubao'=>0,
					'checktime'=>date('Y-m-d H:i:s'),
					'status'=>1,
					'remark'=>$remark
				);
				$db->update('{my_webserv}',$arr,"id=$id");				
				$ar['fbb_s']=webService('Fbb_Regist_Money',array("ID"=>$web_id,"Money"=>2000));		
				//$ar['zhuo_s']=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>2000,'Month'=>$zmonth));
				$ar['liubao_s']=webService('JD_Regist_Money',array("ID"=>$web_id,"Money"=>5500));
				$ar['zengjin_s']=webService('Vip_Money',array("ID"=>$web_id,"Money"=>2000));	
				$db->update('{my_webserv}',$ar,"id=$id");
			}
			elseif($buytype==5)
			{
				$arr=array(
					'paymoney'=>$money,
					'buytype'=>$buytype,
					'paytype'=>$paytype,
					'zengjin'=>2000,
					'liubao'=>5500,
					'checktime'=>date('Y-m-d H:i:s'),
					'status'=>1,
					'remark'=>$remark
				);
				$db->update('{my_webserv}',$arr,"id=$id");
				
				$ar['liubao_s']=webService('JD_Regist_Money',array("ID"=>$web_id,"Money"=>5500));
				$ar['zengjin_s']=webService('Vip_Money',array("ID"=>$web_id,"Money"=>2000));	
				$db->update('{my_webserv}',$ar,"id=$id");
			}
			elseif($buytype==6)
			{
				$arr=array(
					'paymoney'=>$money,
					'buytype'=>$buytype,
					'paytype'=>$paytype,
					'liubao'=>5500,
					'checktime'=>date('Y-m-d H:i:s'),
					'status'=>1,
					'remark'=>$remark
				);
				$db->update('{my_webserv}',$arr,"id=$id");
				$ar['liubao_s']=webService('JD_Regist_Money',array("ID"=>$web_id,"Money"=>5500));
				$db->update('{my_webserv}',$ar,"id=$id");
			}
			elseif($buytype==7)
			{					
				$arr=array(
					'paymoney'=>$money,
					'buytype'=>$buytype,
					'paytype'=>$paytype,
					'zengjin'=>2000,
					'checktime'=>date('Y-m-d H:i:s'),
					'status'=>1,
					'remark'=>$remark
				);
				$db->update('{my_webserv}',$arr,"id=$id");	
				$ar['zengjin_s']=webService('Vip_Money',array("ID"=>$web_id,"Money"=>2000));//增进				
				$db->update('{my_webserv}',$ar,"id=$id");	
				//$db->query("update {member} set lishuid='0' where user_id=$user_id limit 1");	
			}
			elseif($buytype==8)
			{
				$arr=array(
					'paymoney'=>$money,
					'buytype'=>$buytype,
					'paytype'=>$paytype,
					'zhuo'=>20000,
					'zmonth'=>$zmonth,
					'checktime'=>date('Y-m-d H:i:s'),
					'status'=>1,
					'remark'=>$remark
				);
				$db->update('{my_webserv}',$arr,"id=$id");				
				$ar['zhuo_s']=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>20000,'Month'=>$zmonth));
				$db->update('{my_webserv}',$ar,"id=$id");
			}
			elseif($buytype==9)
			{
				$arr=array(
					'paymoney'=>$money,
					'buytype'=>$buytype,
					'paytype'=>$paytype,
					'zhuo'=>2000,
					'zmonth'=>$zmonth,
					'checktime'=>date('Y-m-d H:i:s'),
					'status'=>1,
					'remark'=>$remark
				);
				$db->update('{my_webserv}',$arr,"id=$id");				
				$ar['zhuo_s']=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>2000,'Month'=>$zmonth));
				$db->update('{my_webserv}',$ar,"id=$id");
			}
			
			
			adminlog("审核会员{$user_name}[{$user_id}]购买套餐！");
			
			$add_notice1=array(
				'from_id'=>0,
				'to_id'=>$user_id,
				'content'=>"亲爱的用户$user_name：您好，我们很高兴的通知您，您已成功购买套餐！",  
				'add_time'=>time(),
				'last_update'=>time(),
				'new'=>1,
				'parent_id'=>0,
				'status'=>3
			);
			$db->insert('{message}',$add_notice1);	
			
			showMsg('操作成功！',$url);exit();	
		}
	}	
	header("location:$url");
	exit();
}

if($_GET['status']!='')
{
	$status=(int)($_GET['status']);
	$sqlW.=" and status='$status'";
	$url.='&status='.$status;	
}	
if($_GET['fbb']!='')
{
	$fbb=(int)($_GET['fbb']);
	$sqlW.=" and fbb='$fbb'";
	$url.='&fbb='.$fbb;	
}

if($_GET['zhuo']!='')
{
	$zhuo=(int)($_GET['zhuo']);
	$sqlW.=" and zhuo='$zhuo'";
	$url.='&zhuo='.$zhuo;	
}

if($_GET['liubao']!='')
{
	$liubao=(int)($_GET['liubao']);
	$sqlW.=" and liubao='$liubao'";
	$url.='&liubao='.$liubao;	
}
if($_GET['zengjin']!='')
{
	$zengjin=(int)($_GET['zengjin']);
	$sqlW.=" and zengjin='$zengjin'";
	$url.='&zengjin='.$zengjin;	
}

if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and user_name='$user_name'";
	$url.='&user_name='.$user_name;
}
if(!empty($_GET['user_id']))
{
	$user_id=(int)($_GET['user_id']);
	$sqlW.=" and user_id='$user_id'";
	$url.='&user_id='.$user_id;
}
pageTop($modulename);
//webService('Z_Static_Regist',array("ID"=>'753bbdea-de35-4ec6-b8e5-87d15a50076b',"Money"=>20000,'Month'=>9));

$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}


foreach($_S['buytype'] as $row)//套餐类型
{
	$buyname[$row['buytype']]=$row['name'];
	$buyprice[$row['buytype']]=$row['price'];
}
?>
<script language="javascript" src="include/js/jquery.js"></script>
<script language="javascript">
function rereg(id,type)
{
	$.post("ajax.php?func=rereg",{id:id,type:type},function(result){
		alert(result);
	})
}
    var arr_buy=new Array();
  	arr_buy[0]=new Array(<?=$buyprice[0]?>,<?=$_S['buytype_dj'][0]/10000?>);
	arr_buy[1]=new Array(<?=$buyprice[1]?>,<?=$_S['buytype_dj'][1]/10000?>);
	arr_buy[2]=new Array(<?=$buyprice[2]?>,<?=$_S['buytype_dj'][2]/10000?>);
	arr_buy[3]=new Array(<?=$buyprice[3]?>,7.2,4.9,4.4,4.3,<?=$_S['buytype_dj'][3]/10000?>);
	arr_buy[4]=new Array(<?=$buyprice[4]?>,2.5,1.7,1.6,1.5,<?=$_S['buytype_dj'][4]/10000?>);
	arr_buy[5]=new Array(<?=$buyprice[5]?>,1.97,1.32,1.2,1.16,<?=$_S['buytype_dj'][5]/10000?>);
	arr_buy[6]=new Array(<?=$buyprice[6]?>,1.45,0.97,0.88,0.85,<?=$_S['buytype_dj'][6]/10000?>);
	arr_buy[7]=new Array(<?=$buyprice[7]?>,0.53,0.352,0.32,0.31,<?=$_S['buytype_dj'][7]/10000?>);
	arr_buy[8]=new Array(<?=$buyprice[8]?>,5.3,3.52,3.2,3.1,<?=$_S['buytype_dj'][8]/10000?>);
	arr_buy[9]=new Array(<?=$buyprice[9]?>,0.53,0.352,0.32,0.31,<?=$_S['buytype_dj'][9]/10000?>);
	//改变优惠类型
	function paytype_click(buytype)
	{
		if(buytype<3)
		{
			html='<input type="radio" name="paytype" value="0" checked="checked" onclick="checkYH()"/>1-特殊B';
        	html+='<input type="radio" name="paytype" value="1" onclick="checkYH()"/>6折优惠';         	
		}
		else
		{
			html='<input type="radio" name="paytype" value="0" checked="checked" onclick="checkYH()"/>1-特殊B';
        	html+='<input type="radio" name="paytype" value="1" onclick="checkYH()"/>1-特殊A'; 
        	html+='<input type="radio" name="paytype" value="2" onclick="checkYH()"/>2-特殊B';
        	html+='<input type="radio" name="paytype" value="3" onclick="checkYH()"/>2-特殊A';
        	html+='<input type="radio" name="paytype" value="4" onclick="checkYH()"/>保障';
        	html+='<input type="radio" name="paytype" value="5" onclick="checkYH()"/>标配';
		}
		document.getElementById('div_paytype').innerHTML=html;
		checkYH();	
	}	
	//改变优惠类型
	function checkYH()
	{
		var temp=document.getElementsByName("buytype");		
		for (i=0;i<temp.length;i++)
		{
			if(temp[i].checked)		
			{
				buytype=temp[i].value;
			}
		}		
		var temp=document.getElementsByName("paytype");		
		for (i=0;i<temp.length;i++)
		{
			if(temp[i].checked)		
			{
				v=temp[i].value
			}
		}
		document.getElementById('money').value=arr_buy[buytype][v];		
	}
	
	function checkform()
	{
		if(document.forms[0].tuijianid.value==0 || (document.forms[0].lishuid.value==0 && document.forms[0].buytype[6].checked==false))
		{
			if(window.confirm('推荐人或隶属人为空！确定要提交吗？'))
			{			
				document.getElementById('btn_sub').disabled=true;
				return true;
			}
			else
			{
				return false;	
			}	
		}
		document.getElementById('btn_sub').disabled=true;	
		return true;
	}
</script>
<?
if(empty($_GET['ui']))
{
		
?>
<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>
	<div style="margin-bottom:5px;">
	<form method="GET">		
        用户ID<input type="text" name="user_id" size="8" value="<?=$user_id?>">&nbsp;
        用户名<input type="text" name="user_name" size="8" value="<?=$user_name?>">&nbsp;
        <select name="status">
        	<option value="" selected="selected">未/己审</option>
            <option value="0" <? if($_GET['status']==='0'){echo "selected";}?>>未审核</option>
            <option value="1" <? if($status==1){echo "selected";}?>>己审核</option>
        </select>
        
        <select name="fbb">
        	<option value="" selected="selected">FBB请选择</option>
            <option value="2000" <? if($fbb==2000){echo "selected";}?>>小FBB</option>
            <option value="20000" <? if($fbb==20000){echo "selected";}?>>大FBB</option>
        </select>
        
        <select name="zhuo">
        	<option value="" selected="selected">卓100请选择</option>
            <option value="2000" <? if($zhuo==2000){echo "selected";}?>>小卓</option>
            <option value="20000" <? if($zhuo==20000){echo "selected";}?>>大卓</option>
        </select>
        
        <select name="liubao">
        	<option value="" selected="selected">六保请选择</option>
            <option value="0" <? if($_GET['liubao']==='0'){echo "selected";}?>>未买</option>
            <option value="5500" <? if($liubao==5500){echo "selected";}?>>己买</option>
        </select>
        
        <select name="zengjin">
        	<option value="" selected="selected">增进请选择</option>
            <option value="0" <? if($_GET['zengjin']==='0'){echo "selected";}?>>未买</option>
            <option value="2000" <? if($zengjin==2000){echo "selected";}?>>己买</option>
        </select>
        
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

		$result=$tclass->getall($StartRow,$PageSize,'id desc',$sqlW);		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('会员id','会员名','套餐','现在缴纳订金','申请时间','审核时间','缴费','所属站','FBB','z100','六保','增进','FBB状态','Z100状态','六保','增进','状态','操作'));	
		$is_array=array('不','是');
		
		foreach ($result as $row)
		{
			$id=$row['id'];
			
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='center'><?=getuserno($row['user_id'])?></td>
            	<td align='center'><?=$row['user_name']?></td>
				<td align='left'>&nbsp;&nbsp;<?=$buyname[$row['buytype']]?>（<?=$buyprice[$row['buytype']]?>万）</td>
               
                <td align='left'><?=$is_array[$row['ispaydingjin']]?></td>
                <td align="center"><?=$row['createdate']?></td>
                <td align="center"><?=$row['checktime']?></td>
                <td align="center"><?=$row['paymoney']/10000?>万</td>
 
				<td align="center">&nbsp;&nbsp;<?=$city[$row['city']];?></td>
                <td><?
                	if(empty($row['fbb']))
					{
		
						echo " <a href='?act=my_webserv_up&user_id=".$row['user_id']."&type=1&s=0'>购买</a>";	
					}
					elseif($row['fbb']==2000)
					{
						echo "小FBB";
						echo " <a href='?act=my_webserv_up&user_id=".$row['user_id']."&type=1&s=1'>升级</a>";	
					}
					elseif($row['fbb']==20000)
					{
						echo '大FBB';
					}
				?></td>
                <td><?
                	if(empty($row['zhuo']))
					{
						
						echo " <a href='?act=my_webserv_up&user_id=".$row['user_id']."&type=2&s=0'>购买</a>";
					}
					elseif($row['zhuo']==2000)
					{
						echo "小卓";
						echo " <a href='?act=my_webserv_up&user_id=".$row['user_id']."&type=2&s=1'>升级</a>";	
					}
					elseif($row['zhuo']==20000)
					{
						echo '大卓';
					}
				?></td>
                <td><?
                	if($row['liubao']!=0)
					{
						echo '已购';
					}
					else
					{
						//if(empty($row['zengjin']))
						{
							echo " <a href='?act=my_webserv_up&user_id=".$row['user_id']."&type=3'>购买</a>";
						}
					}
				?></td>
                <td><?
                	if(empty($row['zengjin']))
					{
						echo " <a href='?act=my_webserv_up&user_id=".$row['user_id']."&type=4'>购买</a>";
					}
					else
					{
						echo '已购';
					}
				?></td>
                <?
                if($row['status']==1)
				{
				?>
                    <td align="center">
                    <? 
                        if($row['fbb_s']==1)
                        {
                            echo '正常';
                        }
                        elseif($row['fbb']>0)
                        {
                            echo "<a href=\"javascript:rereg($id,'fbb')\">重新注册</a>";
                        }
                    ?>
                    </td>
                    <td align="center">
                    <? 
                        if($row['zhuo_s']==1)
                        {
                            echo '正常';
                        }
                        elseif($row['zhuo']>0)
                        {
                            echo "<a href=\"javascript:rereg($id,'zhuo')\">重新注册</a>";
                        }
                    ?>
                    </td>
                    <td align="center">
                    <? 
                        if($row['liubao_s']==1)
                        {
                            echo '正常';
                        }
                        elseif($row['liubao']>0)
                        {
                            echo "<a href=\"javascript:rereg($id,'liubao')\">重新注册</a>";
                        }
                    ?>
                    </td>
                    <td align="center">
                    <? 
                        if($row['zengjin_s']==1)
                        {
                            echo '正常';
                        }
                        elseif($row['zengjin']>0)
                        {
                            echo "<a href=\"javascript:rereg($id,'zengjin')\">重新注册</a>";
                        }
                    ?>
                    </td>                
                <?
				}
				else
				{
					echo '<td colspan="4"> </td>';	
				}
				?>
				<td align="center"><?
                	if($row['status']==0)
					{
						echo '未审核';	
					}
					else
					{
						echo '己审核';	
					}
				?></td>
				<td align="center">					
                    <?
                    if($row['status']==0)
					{
					?>
                    <a href='<?=$url?>&ui=edit&id=<?=$row['id']?>'>审核</a>   
                    <?
					}
					?>                 
                    <a onclick="return confirm('确定要删除吗？')" href="<?=$url?>&func=del&id=<?=$row["id"]?>"></a>                    
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
	echo '<form method="POST"  enctype="multipart/form-data" id="form1" onsubmit="return checkform();">';
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
		$mem=$db->get_one("select a.money_dj,a.money,a.suoding_money,b.tuijianid,b.lishuid from {my_money} a join {member} b on a.user_id=b.user_id  where a.user_id='$user_id' limit 1");
		$user_money=$mem['money'];
		$user_money_dj=$mem['money_dj'];
		$user_suoding_money=$mem['suoding_money'];
		$tuijianid=(int)$mem['tuijianid'];
		$lishuid=(int)$mem['lishuid'];
		$mem=null;
		
		echo "<input type='hidden' name='user_id' value='$user_id'>";
		
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="<?=$url?>">返回管理</a></div>
		
        
        
     
    <table class="infoTable" cellpadding="5">
      <tbody><tr>
        <th class="paddingT15"> 会员名:</th>
        <td class="paddingT15 wordSpacing5"><?=$row['user_name']?>  资金：<?=$user_money?>  冻结资金：<?=$user_money_dj?>  锁定资金：<?=$user_suoding_money?>
          
                  </td>
      </tr>
      

	  <tr>
        <th class="paddingT15"> 所属站:</th>
        <td class="paddingT15 wordSpacing5">
        <?
        foreach($city_result as $c)
		{
			$sel='';
			if($c['city_id']==$row['city']) echo $c['city_name'];

		}
		$city_result=null;
		?>

          </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> 推荐人ID:</th>
        <td class="paddingT15 wordSpacing5">
        <?
        echo getuserno($tuijianid);
		$row1=$member->getone("user_id=".$tuijianid);
		echo '&nbsp;&nbsp;（'.$row1['user_name']."）";
		echo "<input type='hidden' name='tuijianid' value='$tuijianid'>";
		$row1=null;
		?>

          </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> 隶属人ID:</th>
        <td class="paddingT15 wordSpacing5">
        <?
        echo getuserno($lishuid);
		$row1=$member->getone("user_id=".$lishuid);
		echo '&nbsp;&nbsp;（'.$row1['user_name']."）";
		echo "<input type='hidden' name='lishuid' value='$lishuid'>";
		$row1=null;
		?>

          </td>
      </tr>

      <tr>
        <th class="paddingT15"> 套餐名称:</th>
        <td class="paddingT15 wordSpacing5"><?
        foreach($_S['buytype'] as $i=>$bu)
		{
			$chk='';
			if($bu['buytype']==$row['buytype']) $chk='checked';
			?>
            <span id="sp_bt_<?=$i?>"><input type="radio" name="buytype" onclick="paytype_click(this.value)"  value="<?=$bu['buytype']?>" <?=$chk?>/><?=$bu['name']?>(<?=$bu['price']?>万)</span>
            <?
		}
		?></td>
      </tr>
      <tr><th>优惠：</th><td><div id="div_paytype"></div></td></tr>
      <tr><th>价格：</th><td><input type="text" id="money" name="money" value="<?=$buyprice[$row['buytype']]?>" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)"/>万&nbsp; &nbsp;审核为付费商家<input type="checkbox" value="1" name="level[]" checked="checked"/> 
      审核为代理商<input type="checkbox" value="2" name="level[]"/></td></tr>
      <tr><th>大小卓月数：</th><td><select name="zmonth">
      <option value="9">9个月</option>
      <option value="12">12个月</option>
      </select>
      <!--是否排队-->
      </td></tr>
      
        
<tr>
        <th></th>
        
        <td class="ptb20"><input type="submit" value="提交" id="btn_sub">
          <input class="formbtn" type="reset" name="Reset" value="重置" onclick="checkform()">        </td>
      </tr>
   </table>
<script>
paytype_click(<?=$row['buytype']?>);
</script>
        
		</form>		
		<?
}
?>
<div style="clear:both"><br /><br />
白金	大F、内增、六保<br />
金牌	小F、内增、六保<br />
银牌	内增、六保<br />
基础	六保<br />
铜牌	内增<br /></div>


<table border="1" cellpadding="4" cellspacing="1">
<tr><td></td><td>1-特殊B</td><td>1-特殊A</td><td>2-特殊B</td><td>2-特殊A</td><td>保障</td><td>标配</td></tr>
<?
foreach($_S['buytype'] as $index=>$i)
{
	?>
    <tr>
    	<td><?=$i['name']?></td>
    	<?
        for($j=0;$j<6;$j++)
		{
			?>
            <td><script>document.write(arr_buy[<?=$index?>][<?=$j?>]);</script></td>
            <?	
		}			
		?>
    </tr>
    <?	
}
?>
</table><br /><br />

<?
exit();
$arr_txt=array('超级','皇冠','白金','金牌','银牌','基础','铜牌','六保3900','大卓','小卓');
$arr_i1=array(40000+5500+2000,40000+5500+2000,2000+20000+5500+2000,2000+2000+5500+2000,5500+2000,5500,2000,3900);
$arr_i=array(2100000,20000+5500+2000,20000+5500+2000,2000+5500+2000,5500+2000,5500,2000,3900,20000,2000);
$arr_j=array(2.16*1.1*1.1*1.1 , 2.16*1.1*1.1 , 1.32*1.1*1.1*1.1  , 1.32*1.1*1.1 ,1.16*1.1*1.1*1.1,1.16*1.1*1.1);
?>
<table border="1" cellpadding="4" cellspacing="1">
<tr><td>旧</td><td>1-特殊B</td><td>1-特殊A</td><td>2-特殊B</td><td>2-特殊A</td><td>保障</td><td>标配</td></tr>
<?
foreach($arr_i1 as $index=>$i)
{
	?>
    <tr><td><?=$arr_txt[$index]?></td>
    	<?
        foreach($arr_j as $j)
		{
			?>
            <td><?=$i*$j?></td>
            <?	
		}			
		?>
    </tr>
    <?	
}
?>
</table><br /><br />

<table border="1" cellpadding="4" cellspacing="1">
<tr><td>新</td><td>1-特殊B</td><td>1-特殊A</td><td>2-特殊B</td><td>2-特殊A</td><td>保障</td><td>标配</td></tr>
<?
foreach($arr_i as $index=>$i)
{
	?>
    <tr><td><?=$arr_txt[$index]?></td>
    	<?
        foreach($arr_j as $j)
		{
			?>
            <td><?=$i*$j?></td>
            <?	
		}			
		?>
    </tr>
    <?	
}
?>
</table>