<?php
/******************************
 * $File: system.class.php
 * $Description: 模块类处理文件

******************************/

class system {	
	
	/**
	 * 获得数据表
	 * 
	 * @return Array
	 */
	function GetSystemTables($data = array()){
		global $db;
		$_result = "";
		$sql = "show tables";
		$result = $db->get_all($sql);
		foreach ($result as $key => $value){
			foreach($value as $val){
				$_val = explode("_",$val);
				if($db->db_prefix!="" && $_val[0]."_"==$db->db_prefix){
					$num = $db->db_count(str_replace($db->db_prefix,"",$val));
					$_result[$key]['name'] = $val;
					$_result[$key]['num'] = $num;
				}else{
					$num = $db->db_count($val);
					$_result[$key]['name'] = $val;
					$_result[$key]['num'] = $num;
				}
			}
		}
		return  $_result;
	
	}
	
	
	/**
	 * 备份数据表
	 * 
	 * @return Array
	 */
	public  function BackupTables($data = array() ){
		global $db;
		$filedir = $data['filedir'];
		$tables = $data['table'];
		$size = $data['size'];
		$tid = $data['tid'];//读取哪个表
		$limit = $data['limit'];//表读取到那几行
		$table_page = $data['table_page'];//文件的分页
		$table = $tables[$tid];
		if ($tables == "")
		{
			showMsg('tables is empty！');exit();	
		}		
		/*
		 *备份表结构
		*/
		if ($tid==0)
		{
			$sql = "";
			$filename = $filedir."/show_table.sql";
			foreach ($tables as $key => $tbl)
			{
				//$sql .="# 数据表　".$tbl."　的结构;\r\n";	
				$sql .="DROP TABLE IF EXISTS `$tbl`;\r\n";//如果表存在就删除存在的表
				$_sql = "show create table $tbl";
				$result = $db->get_one($_sql);
				$sql .= $result['Create Table'].";\r\n\r\n";
				mk_file($filename,$sql);
			}
		}
		
		if ($table != "")
		{
			$file = $filedir."/".$table."_".$table_page.".sql";
			$text = read_file($file);
			if (strlen($text) > $size * 1024) 
			{
				 $file = $filedir."/".$table."_".($table_page+1).".sql";
				 $text = read_file($file);
			}
			
			/*
			 *获取表的所有字段
			*/
			$fields = $db->db_show_fields(str_replace($db->db_prefix,"",$table));
			$_fields = join(",",$fields);			
			$sql = "select *  from `$table` limit $limit,100";			
			$result= $db->get_all($sql)  ; 
			if (count($result)>0)
			{
				foreach ($result as $key => $value)
				{
					$text .= "insert into `$table` ( ";
					foreach ($fields as $fkey => $fvalue)
					{
						$_value[$fkey] ="\"".mysql_escape_string($value[$fvalue])."\"";
						$_fie[$fkey] ="`$fvalue`";
					}
					$text .= join(",",$_fie).") values (".join(",",$_value).");\r\n\r\n";
					$limit++;
				}
				mk_file($file,$text);
				$data['limit'] = $limit;
				$data['table_page'] = $table_page;
				$data['tid'] = $tid;
			}
			else
			{
				$data['limit'] = 0;
				$data['table_page'] = 0;
				$data['tid'] = $tid+1;
			}
			return $data;
		}
		return "";
	}
	
	/**
	 * 备份数据表
	 * 
	 * @return Array
	 */
	public function RevertTables($data = array() ){
		global $db;
		
		$tables = $data['table'];
		$nameid = $data['nameid'];
		if (isset($tables[$nameid]) && $tables[$nameid]!="")
		{
			$value = $tables[$nameid];
			if ($value!="show_table.sql")
			{
				$sql = file_get_contents($data['filedir']."/".$value);
				$_sql = explode("\r\n",$sql);
				foreach ($_sql as $val){
					if ($val!="")
					{
						$db->query($val,"true");
					}
				}
			}
			return $value;
		}else{
			return "";
		}
	}
	

	
}
?>
