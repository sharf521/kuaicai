<?
if (!defined('ROOT'))  die('Access Denied');//·ÀÖ¹Ö±½Ó·ÃÎÊ
require_once ROOT.'/include/tb.class.php';
class bikulog extends tb
{	
	function bikulog()
	{
		global $db;
		$this->db=$db;	
		$this->table='{bikulog}';
		$this->fields=array('biku_id','money','user_id','user_name','type','s_and_z','riqi','biku_city','beizhu','dq_jinbi',	'dq_yujinbi');
	}
	

	function add($post)
	{
		
	}
	function delete($user_id)
	{
		//$this->db->query("delete from $this->table where user_id=$user_id limit 1");
	}
}
?>