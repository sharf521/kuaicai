<?
if (!defined('ROOT'))  die('Access Denied');//防止直接访问
require_once ROOT.'/include/tb.class.php';
class proxy extends tb
{	
	function proxy()
	{
		global $db;
		$this->db=$db;	
		$this->table='{proxy}';
		$this->fields=array('id','user_id','user_name','level','area','areaid','name','address','tel','status','createdate');  
	}

	function pass($post)
	{ 
		
		if(empty($post['user_name']) && empty($post['user_id']))			  return $this->_('用户不能为空!'); 
		
		$post=$this->set($post);
		if($post['func']=='add')
		{
			$row=$this->db->get_one("select id from $this->table where areaid=".$post['areaid']." limit 1");
		}
		else
		{
			$row=$this->db->get_one("select id from $this->table where areaid=".$post['areaid']." and id!=".$post['id']." limit 1");
		}
		if($row) return $this->_('地区代理不能重复！');
		
		return true;
	}
	function set($post)
	{
		if($post['level']==0) 
		{
			$area=explode('|',$post['county']);			
			$post['areaid']=$area[0];
			$post['area']=$area[1];
		}
		elseif($post['level']==1)
		{
			$area=explode('|',$post['city']);			
			$post['areaid']=$area[0];
			$post['area']=$area[1];
		}
		elseif($post['level']==2)
		{
			$area=explode('|',$post['province']);			
			$post['areaid']=$area[0];
			$post['area']=$area[1];	
		}
		return $post;
	}
	function add($post)
	{
		global $_S;
		$post=$this->set($post);
		
		
		

		$post['createdate']=date('Y-m-d H:i:s');
		$insertid=$this->insert($post);	
		adminlog('');
		
	}

	function edit($post)
	{			
		$post=$this->set($post);
	
		$id=intval($post['id']);
		
		
		
		$this->update($post,'id='.$id);
	}
	function delete($id)
	{
		$this->db->query("delete from $this->table where id=$id limit 1");
	}
}
?>