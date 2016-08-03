<?php
class Pages_model extends CI_Model 
{
	var $fields=array();
    public function __construct()
    {
        $this->load->database();
	$this->fields=array('id','typeid','userid','password','username','status','times','purview','createdate','deltime');
    }
    
    public function get_one()
    {
        $query = $this->db->get_where('admins', array('id' => 1));
        return $query->row_array();
    }
	
	public function tbs()
	{
		echo $this->table;	
	}
	
	
	function add($post)
	{
		print_r($this->fields);
		return ;
		$sqlk = $sqlv = '';
		foreach($post as $k=>$v) 
		{
			if(in_array($k, $this->fields)) { $sqlk .= ','.$k; $sqlv .= ",'$v'"; }
		}
		$sqlk = substr($sqlk, 1);
		$sqlv = substr($sqlv, 1);
		//echo "INSERT INTO $this->table ($sqlk) VALUES ($sqlv)";
		
		$this->db->query("INSERT INTO $this->table ($sqlk) VALUES ($sqlv)");
		//$this->db->insert('mytable', $post); 
		
	}
	function edit($post,$where='')
	{
		$data = array(
               'title' => $title,
               'name' => $name,
               'date' => $date
            );

		$this->db->where('id', $id);
		$this->db->update('mytable', $data); 

			
		if($where=='')	$where='id='.$this->id;
		foreach($post as $k=>$v) 
		{
			if(in_array($k, $this->fields)) $sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE $this->table SET $sql WHERE $where limit 1");
	}

}
?>