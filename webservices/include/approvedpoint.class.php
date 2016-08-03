<?
if (!defined('ROOT'))  die('Access Denied');//防止直接访问
require_once ROOT.'/include/tb.class.php';
class approvedpoint extends tb
{	
	function approvedpoint()
	{
		global $db;
		$this->db=$db;	
		$this->table='{approvedpoint}';
		$this->fields=array('id','sid','title','type','approved','award','total','usednum','layer','starttime','endtime','createdate','approved_web','award_web');
				
	}	
	function pass($post)
	{ 
		$post['title']=checkPost(strip_tags($post['title']));
		$post['type']=intval($post['type']);
		$post['approved']=checkPost(strip_tags($post['approved']));
		
		
		if(empty($post['title']))	 return $this->_('标题不能为空!'); 		

		if(empty($post['approved'])) return $this->_('核定点不能为空！');		
		
		if(empty($post['award'])) 	return $this->_('奖励人不能为空！');
		
		$post['approved_web']=$this->get_webid($post['approved']);
		if(empty($post['approved_web']))   return $this->_('核定点不存在！');
		
		return true;
	}
	function get_webid($user_id)
	{
		$user_id=intval($user_id);
		$row=$this->db->get_one("select a.web_id from {member} a join {my_webserv} b on a.user_id=b.user_id where a.user_id='$user_id' and b.status=1 limit 1");
		return 	$row['web_id'];
	}
	function get_webid1($user_id)
	{
		$user_id=intval($user_id);
		$row=$this->db->get_one("select web_id from {member} where user_id='$user_id' limit 1");
		return 	$row['web_id'];
	}
	function doapproved($web_id,$type)
	{
		$date=date('Y-m-d H:i:s');
		if($type==2)
		{
			$result=$this->db->get_all("select id,approved,award,total,usednum,approved_web from $this->table where type=2 and starttime<='$date' and endtime>='$date'");
			//print_r($result);
			foreach($result as $row)
			{
				$id=$row['id'];
				$approved=$row['approved'];
				$award=explode(';',$row['award']);//奖励id,money
				if($row['total']>$row['usednum'])
				{
					if($this->is_sub($approved,$web_id))
					{
						foreach($award as $aw)
						{
							$aw=explode(',',$aw);
							/*$row_1=$this->db->get_one("select web_id from {member} where user_id='".$aw[0]."' limit 1");
							$this->db->query("insert into {process}(UserID,FromUserID,PlateNum,Mony,IncomeTime,Aside1,Aside2,Aside3,Aside4,Aside5, Aside6,Aside7,Aside8,Aside9,Aside10)values('".$row_1['web_id']."','$web_id',1,'".$aw[1]."','$date','6','5','','','','','','','','')");
							$row_1=null;*/
							$this->jiesuan($aw[0],$aw[1]);
						}
						$this->db->query("update $this->table set usednum=usednum+1 where id=$id limit 1");
					}
				}
			}				
		}
	}
	function jiesuan($user_id,$money)
	{
		global $_S;
		$jifen=getjifen($money);
		$row1=$this->db->get_one("select a.user_name,a.city,b.money,b.money_dj,b.duihuanjifen,b.dongjiejifen from {member} a join {my_money} b on a.user_id=b.user_id where a.user_id=$user_id limit 1");
		if($row1)
		{
			$user_name=$row1['user_name'];
			$city=$row1['city'];
			$dq_jifen=$row1['duihuanjifen']+$jifen;
			$arr=array(
				'money'=>0,
				'jifen'=>$jifen,
				'money_dj'=>0,
				'jifen_dj'=>0,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>106,
				's_and_z'=>1,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$row1['money'],
				'dq_money_dj'=>$row1['money_dj'],
				'dq_jifen'=>$dq_jifen,			
				'dq_jifen_dj'=>$row1['dongjiejifen'],
				'beizhu'=>'核定点'
			);			
			$this->db->insert('{moneylog}',$arr);					
			$this->db->query("update {my_money} set duihuanjifen=$dq_jifen where user_id=$user_id limit 1");//更新用户账户资金
			
			$dq_jifen=$_S['canshu']['zong_jifen']-$jifen;
			$arr=array(
				'jifen'=>'-'.$jifen,
				'money'=>0,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>106,
				's_and_z'=>2,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$_S['canshu']['zong_money'],
				'dq_jifen'=>$dq_jifen,
				'beizhu'=>'核定点'
			);
			$this->db->insert('{accountlog}',$arr);					
			$this->db->query("update {canshu} set zong_jifen=$dq_jifen where id=1 limit 1");//更新总账户资金	
		}
		$row1=null;	
			
	}
	function is_sub($pid,$web_id)
	{
		$row=$this->db->get_one("select user_id from {member} where web_id='$web_id' limit 1");	
		//echo "select user_id from {member} where web_id='$web_id' limit 1";
		$u_id=$row['user_id'];
		//获取最上层id
		for($i=0;$i<10000;$i++)
		{
			$sql="select lishuid as u_id from {my_webserv} a join {member} b on a.user_id=b.user_id where a.user_id='$u_id' limit 1";
			$row=$this->db->get_one($sql);
			//echo ($sql).'<br>';
			if(!empty($row['u_id']))
			{				
				$u_id=$row['u_id'];
				if($u_id==$pid)
				{
					return true;	
				}
			}
			else
			{
				break;
			}
		}
		return false;
	}
	function set($post)
	{		
		$post['title']=checkPost(strip_tags($post['title']));
		$post['type']=intval($post['type']);
		$post['layer']=intval($post['layer']);
		$post['total']=intval($post['total']);
		$post['sid']=intval($post['sid']);
		if(empty($post['starttime'])) $post['starttime']=date('Y-m-d H:i:s');
		if(empty($post['endtime']))   $post['endtime']='2020-12-31 12:59:59';
		
		$post['approved_web']=$this->get_webid($post['approved']);
		$str='';
		$webstr='';
		foreach($post['award'] as $i=>$v)
		{
			if(!empty($post['award'][$i]) && !empty($post['money'][$i]))
			{	
				$web_id=$this->get_webid1($post['award'][$i]);
				if(!empty($web_id))
				{		
					if($str=='')
					{
						$str=$post['award'][$i].','.$post['money'][$i];
						$webstr=$web_id.','.$post['money'][$i];
					}
					else
					{
						$str.=';'.$post['award'][$i].','.$post['money'][$i];
						$webstr.=';'.$web_id.','.$post['money'][$i];
					}
				}
			}
		}		
		$post['award']=$str;
		$post['award_web']=$webstr;		
		return $post;
	}
	function add($post)
	{
		global $_S;
		$post=$this->set($post);

		$post['createdate']=date('Y-m-d H:i:s');
		
		
		$arr=array(
			"ApprovedUserID"=>$post['approved_web'],
			"AllocationUserID"=>$post['award_web'],
			"SingularNum"=>$post['total'],
			"ApprovedType"=>$post['type'],
			"JDPlatNum"=>$post['layer'],
			"StartTime"=>str_replace(' ','T',$post['starttime']),
			"EndTime"=>str_replace(' ','T',$post['endtime'])
		);

		$sid=webService('Set_Approved',$arr);
		
		if(intval($sid)<0)
		{
			showMsg('操作失败！返回标识：'.$sid);exit();
		}
		else
		{
			$post['sid']=$sid;
			$this->insert($post);	
			adminlog("新建核定点[".$post['title']."]",1);
		}	
	}
	function edit($post)
	{			
		$post=$this->set($post);	
		$id=intval($post['id']);		
		
		$arr=array(
			"ApprovedID"=>$post['sid'],
			"ApprovedUserID"=>$post['approved_web'],
			"AllocationUserID"=>$post['award_web'],
			"SingularNum"=>$post['total'],
			"ApprovedType"=>$post['type'],
			"JDPlatNum"=>$post['layer'],
			"StartTime"=>str_replace(' ','T',$post['starttime']),
			"EndTime"=>str_replace(' ','T',$post['endtime'])
		);
		$s=webService('Alert_Approved',$arr);
		if($s>0)
		{
			$this->update($post,'id='.$id);		
			adminlog("编辑核定点[".$post['title']."]",2);	
		}
		else
		{
			showMsg('操作失败！返回标识：'.$s);exit();	
		}
		
	}
	function delete($id)
	{
		$row=$this->getone($id);
		$title=$row['title'];
		$sid=$row['sid'];
		
		$s=webService('Delete_Approved',array('ApprovedID'=>$sid));
		if($s==1)
		{
			$this->db->query("delete from $this->table where id=$id limit 1");
			adminlog("删除核定点{$title}ID[$id]",3);
		}
		else
		{
			showMsg('操作失败！返回标识：'.$s);exit();	
		}
	}
}
?>