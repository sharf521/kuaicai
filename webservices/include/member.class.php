<?
if (!defined('ROOT'))  die('Access Denied');//��ֱֹ�ӷ���
require_once ROOT.'/include/tb.class.php';
class member extends tb
{	
	function member()
	{
		global $db;
		$this->db=$db;	
		$this->table='{member}';
		$this->fields=array('user_id','user_name','password','email','real_name','gender','im_qq','im_msn','city','portrait','tuijianid','lishuid','yaoqing_id','web_id','reg_time','last_login','last_ip','logins','web_id','level');
	}
	function get_userid($user_name)
	{
		$sql="select user_id from $this->table where user_name='$user_name' limit 1";
		$row=$this->db->get_one($sql);
		return $row['user_id'];			
	}
	function checkuserid($user_id)
	{
		$sql="select user_id from $this->table where user_id='$user_id' limit 1";
		$row=$this->db->get_one($sql);	
		return $row['user_id'];	
	}
	function get_username($user_id)
	{
		$sql="select user_name from $this->table where user_id='$user_id' limit 1";
		$row=$this->db->get_one($sql);	
		return $row['user_name'];		
	}
	function pass($post)
	{ 
		$post['email']=checkPost(strip_tags($post['email']));
		$post['tuijianid']=checkPost(strip_tags($post['tuijianid']));
		
		if(empty($post['user_name']))			  return $this->_('�û�������Ϊ��!'); 
/*		if (strlen($post['user_name']) <3)
		{
			return $this->_('�û�������Ϊ6λ!');  	
		}*/
		if($post['func']=='edit')
		{
			
		}
		else
		{
			if($this->get_userid($post['user_name'])!=0)
			{
				return $this->_('�û���������!');
			}
			
			if (strlen($post['password']) < 6 || strlen($post['password']) > 20)
			{
				return $this->_('���볤��1---20λ֮��!');  
			}
		
			if (strlen($post['zf_pass']) < 6 || strlen($post['zf_pass']) > 20)
			{
				return $this->_('֧�����볤��1---20λ֮��!');  
			}
		}

		if (!is_email($post['email']))
		{
			return $this->_('����������Բ���ȷ!');
		}
		if(!empty($post['yaoqing_id']))
		{
			if($this->get_userid($post['yaoqing_id'])==0)
			{
				return $this->_('�����˲�����!');
			}	
		}
		if(!empty($post['tuijianid']))
		{
			$tjid=$this->checkuserid($post['tuijianid']);
			if(empty($tjid))
			{
				return $this->_('�Ƽ���ID������!');
			}
			if($post['tuijianid']==$post['user_id'])
			{
				return $this->_('�Ƽ��˲������Լ�!');		
			}
		}		
		if(!empty($post['lishuid']))
		{
			$lsid=$this->checkuserid($post['lishuid']);
			if(empty($lsid))
			{
				return $this->_('������ID������!');
			}
			
			if($post['func']=='add')
			{
				$sql="select count(*) as count from {member} where lishuid='".$post['lishuid']."'";
			}
			else
			{
				$sql="select count(*) as count from {member} where lishuid='".$post['lishuid']."' and user_id!=".$post['user_id'];
			}
			$row=$this->db->get_one($sql);
			if($row['count']>=2)
			{
				return $this->_('�û�'.$post['lishuid'].'ֻ������������!');	
			}	
			$row=null;
			
			if($post['lishuid']==$post['user_id'])
			{
				return $this->_('�����˲������Լ�!');		
			}
		}		
		return true;
	}
	function set($post)
	{
		if(!empty($post['zf_pass']))
		{
			$post['zf_pass']=md5($post['zf_pass']);
		}
		
		$post['user_name']=checkPost(strip_tags($post['user_name']));
		$post['email']=checkPost(strip_tags($post['email']));
		$post['real_name']=checkPost(strip_tags($post['real_name']));
		if($post['level'])
		{
			$post['level']=implode(',',$post['level']);
		}
		return $post;
	}
	function add($post)
	{
		global $_S;
		$post=$this->set($post);

		$post['reg_time']=time();
		$post['logins']=0;	
		
		$post['user_id'] = outer_call('uc_user_register', array($post['user_name'], $post['password'], $post['email']));		
		if ($post['user_id'] < 0)
		{
			switch ($post['user_id'])
			{
				case -1:
				   showMsg('�û������Ϸ���');
				   break;
				case -2:
					showMsg('����Ҫ����ע��Ĵ��');
					break;
				case -3:
					showMsg('�û����Ѿ����ڣ�');
					break;
				case -4:
					showMsg('Email ��ʽ����');
					break;
				case -5:
					showMsg('Email ������ע�ᣡ');
					break;
				case -6:
					showMsg('�� Email �Ѿ���ע�ᣡ');
					break;
				default:
					showMsg('δ�������');
					break;
			}
			exit();
		}
		$post['password']=md5($post['password']);
		$insertid=$this->insert($post);
		$insertid=$post['user_id'];
		//д��money		
		$arr=array(
			'add_time'=>time(),
			'user_id'=>$insertid,
			'user_name'=>$post['user_name'],
			'city'=>$post['city'],
			'zf_pass'=>$post['zf_pass'],
			'money'=>0,
			'bank_name'=>$post['bank_name'],
			'bank_add'=>$post['bank_add'],
			'bank_sn'=>$post['bank_sn'],
			'bank_username'=>$post['bank_username']
		);
		$this->db->insert('{my_money}',$arr);		
		
		if($_S['kaiguan']['webservice']=='yes')//д��webservices
		{
			$web_id= webService('Regist');
			$this->db->query("update $this->table set web_id='$web_id' where user_id=$insertid limit 1");				
			
			/*if(!empty($post['tuijianid']))
			{
				$result=$this->db->get_one("select web_id from $this->table where user_id='".$post['tuijianid']."' limit 1 ");				
				$post_data=array("PID"=>$result['web_id'],"ID"=>$web_id);		
				$pid_s=webService('RegistAddParent',$post_data);				
				$result=null;
			}
			if(!empty($post['lishuid']))//���������
			{
				$result=$this->db->get_one("select web_id from $this->table where user_id='".$post['lishuid']."' limit 1 ");
				$post_data=array("ID"=>$web_id,"DPID"=>$result['web_id'],'Weights'=>$insertid);
				$dpid_s=webService('RegistAddDParent',$post_data);
				$result=null;
			}*/
		}		
		adminlog("�½���Ա[".$post['user_name']."]",1);
	}
	function edit($post)
	{
		global $_S;
		$post=$this->set($post);	
		$user_id=intval($post['user_id']);
		
		$ucresult = outer_call('uc_user_edit', array($post['user_name'], 'oldpwd', $post['password'], $post['email'],1));
		

		if ($ucresult != 1)
		{
			switch ($ucresult)
            {               
                case -1:
                    showMsg('�����벻��ȷ��');exit();
                    break;
                case -4:
                    showMsg('Email ��ʽ����');exit();
                    break;
                case -5:
                    showMsg('Email ������ע�ᣡ');exit();
                    break;
                case -6:
                    showMsg('�� Email �Ѿ���ע�ᣡ');exit();
                    break;
                case -8:
                    showMsg('���û��ܱ�����Ȩ�޸��ģ�');exit();
                    break;      
            }
			
		}
		if(!empty($post['password']))
		{
			$post['password']=md5($post['password']);
		}
		else
		{
			unset($post['password']);	
		}
		$this->update($post,'user_id='.$user_id);
		
		
		$arr=array(				
			'bank_name'=>$post['bank_name'],
			'bank_add'=>$post['bank_add'],
			'bank_sn'=>$post['bank_sn'],
			'bank_username'=>$post['bank_username']
		);
		
		if(!empty($post['zf_pass']))
		{
			$post['zf_pass']=md5($post['zf_pass']);
		}
		else
		{
			unset($post['zf_pass']);	
		}
		
		$this->db->update('{my_money}',$arr,"user_id='$user_id' limit 1");		
		
		if($_S['kaiguan']['webservice']=='yes')//д��webservices
		{
			if(!empty($post['tuijianid']))
			{		
				$result=$this->db->get_one("select web_id from $this->table where user_id='".$post['tuijianid']."' limit 1 ");				
				$post_data=array("PID"=>$result['web_id'],"ID"=>$post['web_id']);	
				$pid_s=webService('RegistAddParent',$post_data);
						
				$result=null;
			}
			
			if(!empty($post['lishuid']))//���������
			{
				$result=$this->db->get_one("select web_id from $this->table where user_id='".$post['lishuid']."' limit 1 ");
				$post_data=array("ID"=>$post['web_id'],"DPID"=>$result['web_id'],'Weights'=>$user_id);
				$dpid_s=webService('RegistAddDParent',$post_data);
				$result=null;
			}
		}
		
		adminlog("�༭��Ա[".$post['user_name']."]",2);
	}
	function delete($user_id)
	{
		$this->db->query("delete from $this->table where user_id=$user_id limit 1");
		$this->db->query("delete from {my_money} where user_id=$user_id limit 1");
		adminlog("ɾ����ԱID[$user_id]",3);
	}
}
?>