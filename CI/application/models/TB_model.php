<?php
class TB_model extends CI_Model 
{
	var $fields=array();
	var $table='';
	var $mysql;
    public function __construct()
    {		
        $this->mysql= $this->load->database('fenecll', TRUE);
		$this->table=$this->mysql->dbprefix.$this->table;
    }
	
	function execute($sql)
	{
		if($this->mysql->dbprefix!='')
		{
			while (ereg ('{([a-zA-Z0-9_-]+)}', $sql, $regs)) 
			{
			  $found = $regs[1];
			  $sql = ereg_replace("\{".$found."\}",$this->mysql->dbprefix.$found, $sql);
			}
		}
		else
		{
			$sql = str_replace(array('{','}'),'',$sql);
		}
		$query=$this->mysql->query($sql);
		return $query;	
	}

    function get_one($data=array())
    {
		if(isset($data['sql']))
		{
			$query=$this->execute($data['sql']);	
		}
		else
		{
			$limit=isset($data['limit'])?" limit ".$data['limit']:'limit 0,1';
			$where=isset($data['where'])?" where ".$data['where']:'';
			$order=isset($data['order'])?' order by '.$data['order']:'';		
			$field=implode(',',$this->fields);
			$sql="select $field from $this->table $where $order $limit";
			$query=$this->mysql->query($sql);
		}
        return $query->row_array();
    }
	
	function get_all($data=array())
	{
		if(isset($data['sql']))
		{
			$query=$this->execute($data['sql']);	
		}
		else
		{			
			$limit=isset($data['limit'])?" limit ".$data['limit']:'limit 0,5000';
			$where=isset($data['where'])?" where ".$data['where']:'';
			$order=isset($data['order'])?' order by '.$data['order']:'';		
			$field=implode(',',$this->fields);
			$sql="select $field from $this->table $where $order $limit";
			$query=$this->mysql->query($sql);
		}		
		return $query->result_array();
	}	
	
	function add($post)
	{
		$sqlk = $sqlv = '';
		foreach($post as $k=>$v) 
		{
			if(in_array($k, $this->fields)) { $sqlk .= ','.$k; $sqlv .= ",'$v'"; }
		}
		$sqlk = substr($sqlk, 1);
		$sqlv = substr($sqlv, 1);
		//echo "INSERT INTO $this->table ($sqlk) VALUES ($sqlv)";		
		$this->mysql->query("INSERT INTO $this->table ($sqlk) VALUES ($sqlv)");	
	}
	function edit($post,$where='')
	{
		foreach($post as $k=>$v) 
		{
			if(in_array($k, $this->fields)) $sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->mysql->query("UPDATE $this->table SET $sql WHERE $where limit 1");
	}
}
?>