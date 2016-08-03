<?
if (!defined('ROOT'))  die('Access Denied');//防止直接访问
require_once ROOT.'/include/tb.class.php';
class test extends tb
{	
	function test()
	{
		global $db;
		$this->db=$db;	
		$this->table='{test}';
		$this->fields=array('id','user_id','user_name','web_id','jifen','type','listid','status','createdate');
	}

	function pass($post)
	{ 
		
		if(empty($post['user_name']) && empty($post['user_id']))			  return $this->_('用户不能为空!'); 

		if(empty($post['jifen'])) return $this->_('返还积分不能为空！');
		
		return true;
	}
	function set($post)
	{
		
		
		return $post;
	}
	function add($post)
	{
		global $_S;
		$post=$this->set($post);
		
		
		$post_data=array("ID"=>$post['web_id'],"Money"=>$post['jifen'],'MoneyType'=>$post['type'],'Count'=>1);
		$post['listid']=webService('C_Consume',$post_data);

		$post['createdate']=date('Y-m-d H:i:s');
		$insertid=$this->insert($post);	
		
		
	}

	function edit($post)
	{			
		$post=$this->set($post);
	
		$id=intval($post['id']);
		
		$post_data=array("ID"=>$post['web_id'],"Money"=>$post['jifen'],'MoneyType'=>$post['type'],'Count'=>1);
		$post['listid']=webService('C_Consume',$post_data);
		
		$this->update($post,'id='.$id);
	}
	function delete($id)
	{
		$this->db->query("delete from $this->table where id=$id limit 1");
	}
}
?>