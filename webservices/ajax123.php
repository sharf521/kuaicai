<?php
	require('./init.php');	
	require('./include/global.func.php');
	require_once('./include/admin.func.php');
	
	//if($_SESSION["admin_id"]=="" || $_SESSION["admin_userid"]=="")
	{
		//exit();
	}
	
	//开关
	$_S['kaiguan']=$db->get_one("select * from {kaiguan} where id=1");
	//参数
	$_S['canshu']=$db->get_one("select * from {canshu} where id=1");		
	//$_S['canshu']['webservip']='192.168.1.150:888';
	

	$func=$_REQUEST['func'];
	if($func=="asdf")
	{
/*		echo "update ecm_my_money set duihuanjifen=196224.2887 where user_name='999999' limit 1;<br>";
		for($i=900001;$i<=900029;$i++)
		{
			echo "update ecm_my_money set duihuanjifen=39576.57457 where user_name='$i' limit 1;<br>";
		}
		exit();*/
		$result=$db->get_all("select id,user_id,user_name,jifen,zengjin from {webservlog} where status=1 and date>'2012-12-13'");
		foreach($result as $row)
		{
			$id=$row['id'];
			
			$db->query("update {webservlog} set status=0 where id=$id limit 1");
			
			$money='-'.$row['jifen'];
			$zengjin='-'.$row['zengjin'];
			$user_id=$row['user_id'];
			$user_name=$row['user_name'];
			$row1=$db->get_one("select money,money_dj,duihuanjifen,dongjiejifen from {my_money} where user_id=$user_id limit 1");	
				
			$dq_jifen=$row1['duihuanjifen']+$money;
				
			if($money!=0)
			{	
				$arr=array(
					'money'=>0,
					'jifen'=>$money,
					'money_dj'=>0,
					'jifen_dj'=>0,
					'user_id'=>$user_id,
					'user_name'=>$user_name,
					'type'=>104,
					's_and_z'=>1,
					'time'=>date('Y-m-d H:i:s'),
					'zcity'=>$city,
					'dq_money'=>$row1['money'],
					'dq_money_dj'=>$row1['money_dj'],
					'dq_jifen'=>$dq_jifen,			
					'dq_jifen_dj'=>$row1['dongjiejifen'],
					'beizhu'=>$date
				);	
				$db->insert('{moneylog}',$arr);					
				$db->query("update {my_money} set duihuanjifen=$dq_jifen where user_id=$user_id limit 1");
			}//更新用户账户资金
		}
	}
	elseif($func=='usql')
	{
		$result=$db->get_all("select user_name,password,email,last_ip,last_login from {member}");
		foreach($result as $row)
		{
			echo "INSERT INTO `uc_members` (`username`, `password`, `email`, `myid`, `myidkey`, `regip`, `regdate`, `lastloginip`, `lastlogintime`, `salt`, `secques`) VALUES ('".$row['user_name']."', '".md5($row['password'])."', '".$row['email']."', '', '', '".$row['last_ip']."', '".$row['last_login']."', 0, 0, '', '');<br>";
			
			
		}
		exit();	
	}
	elseif($func=='excel1')
	{
		$DB_Server = "localhost";      
   $DB_Username = "root";      
   $DB_Password = "123";      
   $DB_DBName = "fenecll";      
   $DB_TBLName = "ecm_store";          
   $savename = date("Y-m-j H:i:s");   
   $Connect = @mysql_connect($DB_Server, $DB_Username, $DB_Password) or die("Couldn't connect.");  
       
   mysql_query("Set Names 'gbk'");  
   $file_type = "vnd.ms-excel";      
   $file_ending = "xls";  
   header("Content-Type: application/$file_type;charset=big5");   
   header("Content-Disposition: attachment; filename=".$savename.".$file_ending");      
        //header("Pragma: no-cache");         
        $now_date = date("Y-m-j H:i:s");       
        //$title = "数据库名:$DB_DBName,数据表:$DB_TBLName,备份日期:$now_date";  
        //$title = "所属站,店铺名称,店主姓名,所在地区"; 
        $sql = "select user_id,user_name,money,money_dj,duihuanjifen,dongjiejifen,city from ecm_my_money where money<>0 or money_dj<>0 or duihuanjifen<>0 or dongjiejifen<>0";       
        $ALT_Db = @mysql_select_db($DB_DBName, $Connect) or die("Couldn't select database");      
        $result = @mysql_query($sql,$Connect) or die(mysql_error());    
             
        echo("$title\n");       
        $sep = "\t";       
        for ($i = 0; $i < mysql_num_fields($result); $i++) {  
            echo mysql_field_name($result,$i) . "\t";       
        }       
        print("\n");       
      //  $i = 0;       
        while($row = mysql_fetch_row($result)) {       
            $schema_insert = "";  
            for($j=0; $j< mysql_num_fields($result);$j++) {       
                if(!isset($row[$j]))       
                    $schema_insert .= "NULL".$sep;       
                else if ($row[$j] != "")       
                    $schema_insert .= "$row[$j]".$sep;  
                else       
                    $schema_insert .= "".$sep;       
            }       
            $schema_insert = str_replace($sep."$", "", $schema_insert);       
            $schema_insert .= "\t";       
            print(trim($schema_insert));       
            print "\n";       
           // $i++;       
        }       
        return (true);     
		exit();
	}
	elseif($func=='up_paymoney')//更改日封顶
	{
		exit();
		$result=$db->get_all("select * from {my_webserv}");
		foreach($result as $row)
		{
			$id=$row['id'];
			//fbb 小FBB	zhuo 小卓	zmonth 大小卓月数	zengjin 增进	liubao	
			$money=0;
			if($row['fbb']==2000)
			{
				$money+=2000*2.16*1.1*1.1*1.1;	
			}
			if($row['zhuo']==2000)
			{
				$money+=2000*2.16*1.1*1.1*1.1;	
			}
			if($row['liubao']==5500)
			{
				$money+=7800;	
			}
			if($row['zengjin']==2000)
			{
				$money+=2820;	
			}
			$db->query("update {my_webserv} set paymoney=$money where id=$id limit 1");
		}
	}
	elseif($func=='rereg')//购买单项
	{
		$id=$_REQUEST['id'];
		$type=$_REQUEST['type'];
		$row=$db->get_one("select * from {my_webserv} where id=$id limit 1");
		$user_id=$row['user_id'];
		$fbb=intval($row['fbb']);
		$zhuo=intval($row['zhuo']);
		$liubao=intval($row['liubao']);
		$zengjin=intval($row['zengjin']);
		$zmonth=intval($row['zmonth']);
		$row=$db->get_one("select web_id from {member} where user_id='$user_id' limit 1");
		$web_id=$row['web_id'];	
		if(empty($web_id))
		{
			echo '用户不存在！';exit();	
		}
		if($type=='fbb')
		{
			$status=webService('Fbb_Regist_Money',array("ID"=>$web_id,"Money"=>$fbb));	
			$sql="update {my_webserv} set fbb_s=1 where id=$id limit 1";
		}
		elseif($type=='zhuo')
		{
			$status=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>$zhuo,'Month'=>$zmonth));	
			$sql="update {my_webserv} set zhuo_s=1 where id=$id limit 1";
		}
		elseif($type=='liubao')
		{
			$status=webService('JD_Regist_Money',array("ID"=>$web_id,"Money"=>$liubao));//六保	
			$sql="update {my_webserv} set liubao_s=1 where id=$id limit 1";
		}
		elseif($type=='zengjin')
		{
			$status=webService('Vip_Money',array("ID"=>$web_id,"Money"=>$zengjin));//增进	]
			$sql="update {my_webserv} set zengjin_s=1 where id=$id limit 1";
		}
		if($status==1)
		{
			$db->query($sql);
			echo '操作成功！';	
		}
		elseif($type=='fbb' && $status=-4)
		{
			$db->query($sql);
			echo '己注册！';
		}
		elseif($type=='zhuo' && $status=-5)
		{
			$db->query($sql);
			echo '己注册！';
		}
		elseif($type=='liubao' && $status=-4)
		{
			$db->query($sql);
			echo '己注册！';
		}
		elseif($type=='zengjin' && $status=-2)
		{
			$db->query($sql);
			echo '己注册！';
		}
		else
		{
			echo '失败！错误代码：'.$status;	
		}
	}
	elseif($func=='C_Query')
	{
		$listid=intval($_REQUEST['listid']);
		echo webService('C_Query',array("ID"=>$listid));//增进	
	}
	elseif($func=='doRegJD')
	{
		$num=intval($_REQUEST['num']);
		$pre=$_REQUEST['pre'];
		$user_name=$pre.$num;
		$web_id= webService('Regist');
		$db->query("insert into {member}(web_id,user_name) values('$web_id','$user_name')");
		$status=webService('Vip_Money',array("ID"=>$web_id,"Money"=>2000));
		echo '用户'.$user_name.'注册结果：'.$status;		
	}
	elseif($func=='vip_up')
	{
		$sql="select b.user_id,max(a.PlateNum) as t from {process} a join {member} b on a.UserID=b.web_id group by b.user_id";
		$result=$db->get_all($sql);
		foreach($result as $row)
		{
			$user_id=$row['user_id'];
			$t=(int)$row['t'];
			$db->query("update {my_money} set t=$t where user_id=$user_id limit 1");	
		}
		$result=null;
		echo '更新完成!';
	}
	elseif($func=='jiesuan')
	{
		global $db,$_S;	;
		$date=$_REQUEST['date'];
		//增进不结算
		if(empty($date))
		{
			return '';
		}		
		$sqlW="a.status=0 and a.IncomeTime>='".($date)."' and a.IncomeTime<='".$date." 23:59:59'";			
		
		//增进
		$result=$db->get_all("select a.UserID,b.user_id,sum(a.Mony) as money_zj from {process} a join {member} b on a.UserID=b.web_id where $sqlW and a.PlateNum>1 group by a.UserID");
		$arr_zj=array();
		foreach($result as $row)
		{
			$user_id	=$row['user_id'];
			$money_zj	=$row['money_zj'];		
			$db->query("update {my_money} set zengjin=zengjin+$money_zj where user_id=$user_id limit 1");
			
			$arr_zj[$row['UserID']]=$money_zj;//下面结算日志要用
		}
		$result=null;		
		
		$sql="select b.user_id,b.user_name,b.city,b.web_id,sum(a.Mony) as Mony from {process} a join {member} b on a.UserID=b.web_id where $sqlW and a.PlateNum<2 group by a.UserID";
		$result=$db->get_all($sql);
		$money_sum=0;
		foreach($result as $row)
		{
			$user_id=$row['user_id'];
			$web_id=$row['web_id'];
			$user_name=$row['user_name'];
			$money=((int)($row['Mony']*100))/100;//保留2位小数
			
			$money_zj=(float)$arr_zj[$web_id];
			
			$city=$row['city'];
			$yujifen=0;//日封顶扣除积分
			$row1=$db->get_one("select paymoney from {my_webserv} where user_id=$user_id limit 1");
			if($row1)
			{
				$paymoney=$row1['paymoney']*$_S['canshu']['jifenxianjin'];				
				if($money>$paymoney)
				{					
					$yujifen=$money-$paymoney;
					$money=$paymoney;
				}	
			}
			$row1=null;

			$row1=$db->get_one("select money,money_dj,duihuanjifen,dongjiejifen from {my_money} where user_id=$user_id limit 1");
			if($row1)
			{
				/*$qianbiku=(float)$row1['qianbiku'];
				if(!empty($qianbiku))
				{
					if($money>=$qianbiku)//欠原排队数据
					{
						$money=$money-$qianbiku;
						$sql1="update {my_money} set qianbiku=0 where user_id=$user_id limit 1";	
					}
					else
					{
						$qianbiku=$qianbiku-$money;
						$sql1="update {my_money} set qianbiku=$qianbiku where user_id=$user_id limit 1";
					}
					$db->query($sql1);
				}*/				
				$sql="insert into {webservlog}(user_id,user_name,date,jifen,zengjin,yujifen,createdate,status)values($user_id,'$user_name','$date','$money','$money_zj','$yujifen',now(),1)";
				$db->query($sql);
				$db->query("update {process} a set status=1 where a.UserID='$web_id' and $sqlW");				
				
				$dq_jifen=$row1['duihuanjifen']+$money;
					
				if($money!=0)
				{	
					$arr=array(
						'money'=>0,
						'jifen'=>$money,
						'money_dj'=>0,
						'jifen_dj'=>0,
						'user_id'=>$user_id,
						'user_name'=>$user_name,
						'type'=>104,
						's_and_z'=>1,
						'time'=>date('Y-m-d H:i:s'),
						'zcity'=>$city,
						'dq_money'=>$row1['money'],
						'dq_money_dj'=>$row1['money_dj'],
						'dq_jifen'=>$dq_jifen,			
						'dq_jifen_dj'=>$row1['dongjiejifen'],
						'beizhu'=>$date
					);	
					$db->insert('{moneylog}',$arr);					
					$db->query("update {my_money} set duihuanjifen=$dq_jifen where user_id=$user_id limit 1");
				}//更新用户账户资金
				
				if($yujifen>0)//平台收益
				{
					$canshu=$db->get_one("select zong_jifen from {canshu}");
					$dq_jifen=$canshu['zong_jifen']+$yujifen;
					$canshu=null;
					$arr=array(
						'jifen'=>$yujifen,
						'money'=>0,
						'user_id'=>$user_id,
						'user_name'=>$user_name,
						'type'=>105,
						's_and_z'=>1,
						'time'=>date('Y-m-d H:i:s'),
						'zcity'=>$city,
						'dq_money'=>$_S['canshu']['zong_money'],
						'dq_jifen'=>$dq_jifen,
						'beizhu'=>$date
					);
					$db->insert('{accountlog}',$arr);					
					$db->query("update {canshu} set zong_jifen=$dq_jifen where id=1 limit 1");//更新总账户资金		
				}
				$money_sum=$money_sum+$money;//总支出
			}
			$row1=null;			
		}
		$result=null;
		
		$canshu=$db->get_one("select zong_jifen from {canshu}");
		$dq_jifen=$canshu['zong_jifen']-$money_sum;
		$canshu=null;
		
		$arr=array(
			'money'=>0,
			'jifen'=>'-'.$money_sum,
			'user_id'=>0,
			'user_name'=>0,
			'type'=>104,
			's_and_z'=>2,
			'time'=>date('Y-m-d H:i:s'),
			'zcity'=>$city,
			'dq_money'=>$_S['canshu']['zong_money'],
			'dq_jifen'=>$dq_jifen,
			'beizhu'=>$date
		);
		$db->insert('{accountlog}',$arr);					
		$db->query("update {canshu} set zong_jifen=$dq_jifen where id=1 limit 1");//更新总账户资金	
		adminlog("结算$date成长积分！");
		echo '完成!';
		exit();	
	}
	elseif($func=="GetListInfo")
	{		
		$arr=array();
		$row=$db->get_one("select max(ProcessId) proid from {process}");
		$proid=$row['proid'];
		if(empty($proid)) $proid=0;
		$result=webService('GetListInfo',array('Start'=>$proid,'Num'=>500));

		$count=count($result);
		if($count==0)
		{
			echo '接收完成';
		}
		else
		{
			$result=$result['Process'];
			$i=0;
			if(!empty($result['ProcessID']))
			{
				if($result['Aside1']>3)//fbb,100,zeng..钱转积分
				{
					$result['Mony']=getjifen($result['Mony']);	
				}
				//$result['Mony']=((int)($result['Mony']*10000))/10000;//保留4位小数
				$db->insert('{process}',$result);
				$i++;
			}
			else
			{
				foreach($result as $v)
				{
					if($v['Aside1']>3)//fbb,100,zeng..钱转积分
					{
						$v['Mony']=getjifen($v['Mony']);	
					}
					$db->insert('{process}',$v);
					$i++;
				}	
			}
			echo '接收 '.$i.' 条数据！';
		}	
		exit();
	}
	elseif($func=='GetJDTree')
	{
		$db->query("TRUNCATE TABLE  {jdtree}");	
		$t=intval($_REQUEST['t']);
		$row=$db->get_one("select count(id) as proid from {jdtree} where T=$t");
		$proid=$row['proid'];
		if(empty($proid)) $proid=0;
		
		$result=webService('GetJDTree',array('Start'=>$proid,'Num'=>1000,'T'=>$t));

		$count=count($result);
		if($count==0)
		{
			echo '接收完成';
		}
		else
		{
			$result=$result['ThreeTreeFile'];
			
			$i=0;
			if(!empty($result['UserID']))
			{
				$result['T']=$t;	
				
				$db->insert('{jdtree}',$result);
				$i++;
			}
			else
			{
				foreach($result as $v)
				{		
					$v['T']=$t;
		
					$db->insert('{jdtree}',$v);
					$i++;
				}	
			}
			echo '接收 '.$i.' 条数据！';
		}
		exit();
	}
	elseif($func=='DelJDTree')
	{
		$db->query("TRUNCATE TABLE  {jdtree}");	
		echo 'JDTree清除完成！';
	}
	elseif($func=='clearprocess')
	{	
	exit();	
		$db->query("TRUNCATE TABLE  {process}");		
		$db->query("TRUNCATE TABLE  {test}");//测试返利表
		$db->query("TRUNCATE TABLE  {my_webserv}");	
		$db->query("TRUNCATE TABLE  {adminlog}");	
		$db->query("TRUNCATE TABLE  {accountlog}");
//		$db->query("TRUNCATE TABLE  {member}");	
		$db->query("TRUNCATE TABLE  {moneylog}");	
		$db->query("TRUNCATE TABLE  {my_money}");	
		$db->query("TRUNCATE TABLE  {my_moneylog}");
		$db->query("TRUNCATE TABLE  {bikulog}");
		$db->query("TRUNCATE TABLE  {my_webserv}");		
		$db->query("update {canshu} set zong_jinbi=5000000,yu_jinbi=5000000,zong_money=0,zong_jifen=0");
		
		
		 
		echo '清除完成！';

	}
	elseif($func=='getJDTreeJson')
	{
		$pan=intval($_REQUEST['pan']);
		$user_id=intval($_REQUEST['user_id']);
		$plevel=intval($_REQUEST['plevel']);
		$nlevel=intval($_REQUEST['nlevel']);
		$user_name=$_REQUEST['user_name'];
		if(empty($user_id) && empty($user_name))
		{
			$sql="select b.ID,a.user_id,a.user_name,a.web_id,b.Parent,b.UserID from {jdtree} b left join {member} a on a.web_id=b.UserID where b.T=$pan  order by b.id";
		}
		else
		{	
			if(empty($user_id))
			{
				$row=$db->get_one("select user_id,web_id from {member} where user_name='$user_name' limit 1");
				
			}
			else
			{
				$row=$db->get_one("select user_id,web_id from {member} where user_id='$user_id' limit 1");
			}
			if($row)
			{
				$user_id=$row['user_id'];
				$web_id=$row['web_id'];
			}
			else
				exit();
				
			$sql="select ID as p_id from {jdtree} where UserID='$web_id' limit 1";
			$row=$db->get_one($sql);
			$p_id=$row['p_id'];
			//获取最上层id	
			for($i=0;$i<$plevel;$i++)
			{				
				$sql="select Parent as p_id,UserID as w_id from {jdtree} where ID='$p_id' limit 1";
				$row=$db->get_one($sql);
				//print_r($row);
				if(!empty($row['p_id']))
				{				
					$p_id=$row['p_id'];
				}
				else
				{
					break;
				}				
			}
			$sql="select UserID as w_id from {jdtree} where ID='$p_id' limit 1";
			$row=$db->get_one($sql);
			$web_id=$row['w_id'];				
	
			$arr_ui=array($web_id);

			getSubIDs($p_id);
			$str=implode("','",$arr_ui);		
			$sql="select b.ID,a.user_id,a.user_name,a.web_id,b.Parent,b.UserID from {jdtree} b left join {member} a on a.web_id=b.UserID where b.T=$pan and b.UserID in('$str')  order by b.id";
		}
		//echo $sql;
		$result=$db->get_all($sql);	

		foreach($result as $k=>$v)
		{
			if(empty($v['user_name']))
			{
				$result[$k]['user_name']=$v['UserID'];
			}
		}

		echo json_encode($result);
		exit();
	}
	elseif($func=='getuserjson')
	{
		$type=intval($_REQUEST['type']);
		$user_id=intval($_REQUEST['user_id']);
		$plevel=intval($_REQUEST['plevel']);
		$nlevel=intval($_REQUEST['nlevel']);
		$user_name=$_REQUEST['user_name'];
		if(empty($user_id))
		{
			$row=$db->get_one("select user_id from {member} where user_name='$user_name' limit 1");
			if($row)
				$user_id=$row['user_id'];
			else
				exit();
		}	
		//获取最上层id
		$u_id=$user_id;
		for($i=0;$i<$plevel;$i++)
		{
			if($type==0)
				$sql="select tuijianid as u_id from {member} where user_id=$u_id limit 1";
			else
				$sql="select lishuid as u_id from {member} where user_id='$u_id' limit 1";
			$row=$db->get_one($sql);
			if(!empty($row['u_id']))
			{				
				$u_id=$row['u_id'];
			}
			else
			{
				break;
			}				
		}
		
		$arr_ui=array($u_id);
		getarrid($type,$u_id);
		$str=implode(',',$arr_ui);
	
		$sql="select a.user_id,a.user_name,a.tuijianid,a.lishuid,b.checktime from {member} a join {my_webserv} b on a.user_id=b.user_id where a.user_id in($str) order by b.checktime";
		$result=$db->get_all($sql);
		
		if($type==1)
		{
			foreach($result as $k=>$v)
			{
				$result[$k]['tuijianid']=$v['lishuid'];	
			}	
		}
		echo json_encode($result);
		exit();
		
	}
	function getSubIDs($p_id,$floor=0)
	{
		global $db,$arr_ui,$nlevel,$plevel;

		if($floor>=$nlevel+$plevel){return;}

		$sql="select UserID as w_id,ID as p_id from {jdtree} where Parent='$p_id' ";	

		$result=$db->get_all($sql);
		foreach($result as $i=>$row)
		{				
			array_push($arr_ui,$row['w_id']);
			getSubIDs($row['p_id'],$floor+1);
			//print_r($arr_ui);
		}
	}
	
	function getarrid($type,$u_id,$floor=0)
	{
		global $db,$arr_ui,$nlevel,$plevel;
		if($floor>=$nlevel+$plevel){return;}
		
		if($type==0)
			$sql="select user_id  from {member} where tuijianid=$u_id";
		else
			$sql="select user_id  from {member} where lishuid='$u_id'";	
			
		$result=$db->get_all($sql);
		foreach($result as $i=>$row)
		{				
			array_push($arr_ui,$row['user_id']);
			getarrid($type,$row['user_id'],$floor+1);
		}
			
	}
	
	function checkSild($x)
	{
		$a0 = 0;
		if ($x == $a0 || $x == $a0 + 1) return true;
		$an = 0;
		$flag = false;
		while (true)
		{
			$an = ($a0 + 1) * 3;
			if ($x == $an || $x == $an + 1)
			{
				$flag = true;
				break;
			}
			if ($x < $an) break;
			$a0 = $an;
		}
		return $flag;
	}

	


	

	

	
?>