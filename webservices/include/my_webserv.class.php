<?
if (!defined('ROOT'))  die('Access Denied');//·ÀÖ¹Ö±½Ó·ÃÎÊ
require_once ROOT.'/include/tb.class.php';
class my_webserv extends tb
{	
	function my_webserv()
	{
		global $db;
		$this->db=$db;	
		$this->table='{my_webserv}';
		$this->fields=array('id','user_id','user_name','buytype','ispayprice','ispaydingjin','paymoney','paytype','fbb','zhuo','zmonth','zengjin','liubao','city','createdate','checktime','status','remark','fbb_s','zhuo_s','liubao_s','zengjin_s');
	}

	

	function edit($post)
	{			
		$post=$this->set($post);
	
		$user_id=intval($post['id']);
		
		$this->update($post,'id='.$id);
	}
	function delete($id)
	{
		$this->db->query("delete from $this->table where id=$id limit 1");
	}
}
?>