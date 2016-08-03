<?
/**********************************
*	  表操作基类
* @file			include/tb.class.php
* @author		乔少锋
* @creatdedate  2010-08-01
* @modifydate	2010-09-24
**********************************/
if (!defined('ROOT'))  die('Access Denied');//防止直接访问
class tb
{	
	var $db;
	var $fields=array();
	var $errmsg;
	var $table;
	var $id=0;	
	function getcount($where='')
	{
		$strw='1=1';
		if(!empty($where))	$strw.=' and '.$where;
		$sql="select count(*) as count from $this->table where $strw";
		$row=$this->db->get_one($sql);
		return $row['count'];
	}
	function getone($where='')
	{
		if($where=='')	$where='id='.$this->id;
		$field=implode(',',$this->fields);
		if($id==0) $id=$this->id;
		$sql="select $field from $this->table where $where limit 0,1";
		return $this->db->get_one($sql);
	}
	function getall($limit=0,$num=20,$order='id desc',$where='')
	{
		$strw='1=1';
		if(!empty($where))	$strw.=' and '.$where;
		$field=implode(',',$this->fields);
		$sql="select $field from $this->table where $strw order by $order limit $limit,$num";
		return $this->db->get_all($sql);
	}	
	function status($status)
	{
		$this->db->query("update $this->table set status=$status  where id=$this->id limit 1");
	}
	function insert($post)
	{
		$sqlk = $sqlv = '';
		foreach($post as $k=>$v) 
		{
			if(in_array($k, $this->fields)) { $sqlk .= ','.$k; $sqlv .= ",'$v'"; }
		}
		$sqlk = substr($sqlk, 1);
		$sqlv = substr($sqlv, 1);
		//echo "INSERT INTO $this->table ($sqlk) VALUES ($sqlv)";
		
		$this->db->query("INSERT INTO $this->table ($sqlk) VALUES ($sqlv)");
		return $this->db->get_insert_id();
	}

	function update($post,$where='')
	{	
		if($where=='')	$where='id='.$this->id;
		foreach($post as $k=>$v) 
		{
			if(in_array($k, $this->fields)) $sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE $this->table SET $sql WHERE $where limit 1");
	}
	
	//返回一个数组
	function getkeyArray($value,$key='id',$where='status=1')
	{
		$result=$this->db->get_all("select $key,$value from $this->table where $where");
		$arr=array();
		foreach($result as $row)
		{
			$arr[$row[$key]]=$row[$value];	
		}	
		$result=null;
		return $arr;
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
	
		//排序
	function setorder($array)
	{
		if(!is_array($array)) exit();
		foreach($array as $key=>$val)
		{
			$val=intval($val);
			$key=intval($key);
		//	echo "UPDATE $this->table SET `showorder` = '".$val."' WHERE `id` = $key limit 1";
			$this->db->query("UPDATE $this->table SET `showorder` = '".$val."' WHERE `id` = $key limit 1");
		}	
	}	
}
?>