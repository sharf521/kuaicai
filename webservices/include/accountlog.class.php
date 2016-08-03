<?
if (!defined('ROOT'))  die('Access Denied');//·ÀÖ¹Ö±½Ó·ÃÎÊ
require_once ROOT.'/include/tb.class.php';
class accountlog extends tb
{	
	function accountlog()
	{
		global $db;
		$this->db=$db;	
		$this->table='{accountlog}';
		$this->fields=array('account_id','money','jifen','time','user_name','user_id','zcity','type','s_and_z','beizhu','dq_money','dq_jifen');
	}
	

	function edit($post)
	{			
		$post=$this->set($post);
	
		$user_id=intval($post['id']);
		
		$this->update($post,'id='.$id);
	}
	function delete($user_id)
	{
		//$this->db->query("delete from $this->table where user_id=$user_id limit 1");
	}
}
?>