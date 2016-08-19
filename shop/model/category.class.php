<?php
class categoryClass extends Model
{
	function __construct()
	{
		parent::__construct();		
	}
	function getlist($data)
	{
		$where="where 1=1";
		if(!empty($data['store_id']))
		{
			$where.=" and store_id={$data['store_id']}";
		}
		$sql="select * from {$this->dbfix}gcategory {$where} order by `showorder`,id";
		return $this->mysql->get_all($sql);
	}
}
?>