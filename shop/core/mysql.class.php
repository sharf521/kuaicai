<?php
class Mysql
{
	private $config;
	private $conn=null;
	private $dbfix;
	function Mysql($db_config)
	{
		$this->config = $db_config;
		$this->dbfix=$db_config['prefix'];
	}
	private function connect($cnf)
	{
		if (!function_exists('mysql_connect')) {
			$this->error_msg('您的数据库还未安装此扩展');
		}
		$conn = mysql_connect($cnf['host'].':'. $cnf['port'], $cnf['user'], $cnf['pwd'], TRUE, 2);
		if (!$conn || !mysql_select_db($cnf['name'], $conn)) {
			$this->error_msg("数据库连接失败");
		}		
		mysql_query('SET NAMES "'.$cnf['language'].'"', $conn);
		return $conn;
	}
	
	private function get_link() 
	{
        if ($this->conn==null)
		{
			$this->conn = $this->connect($this->config);
		}
		return $this->conn;
	}

	function query($sql)
	{		
		$result = mysql_query($sql, $this->get_link());
        if (!$result)
		{
			$this->error_msg($sql);
		}
        return $result;
	}

	function get_one($sql)
	{			
		$result=$this->query($sql);		
		if (empty($result)) return array();		
		$res = mysql_fetch_array($result,MYSQL_ASSOC);	
		return $res;
	}
	
	function get_all($sql) 	
	{
		$result=$this->query($sql);				
		if (empty($result))  return array();
		
		$data = array();
		while($r = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$data[] = $r;
		}
        mysql_free_result($result);	
		return $data;
	}
	
	//返回单字段信息（表中单元格）
	function field($table,$field,$condition=null)
	{
		if ($condition===null)
			$sql="select $field from {$this->dbfix}$table limit 0,1";
		else
			$sql="select $field from {$this->dbfix}$table where $condition limit 0,1";
		$result=$this->query($sql);
		$rs=mysql_fetch_array($result);
		return $rs[$field];
	}
	
	//构造sql语句
	/*
	$query_field='nickname,age,address,sign';
	$sql=sql($query_field,'member','user_id=32');
	
	$option['select']='nickname,age,address,sign';
	$option['from']='user_info';
	$option['where']='sex=2 && age>30';
	$option['order']='age desc';
	$option['limit']='0,20';
	$sql=sql($option);
	*/
	function sql($option,$from='',$where=null)
	{
		$from=$this->dbfix.$from;
		if (is_array($option))
		{
			$sql='';
			$key_arr=array('select','from','where','group','having','order','limit');
			foreach ($key_arr as $key)
			{
				$value=($key=='group' || $key=='order')?$key.' by':$key;
				$sql.=(isset($option[$key]) && trim($option[$key]))?' '.$value.' '.trim($option[$key]):'';
			}
			return $sql;
		}
		$sql='select '.$option.' from '.$from;
		if ($where!==null) $sql.=' where '.$where;
		return $sql;
	}

	function insert($table,$dataArray)
	{
		foreach($dataArray as $key=>$val)
		{
			$field .= "`$key`,";
			$value .= "'$val',";	
		}
		$field = substr($field, 0, -1);
		$value = substr($value, 0, -1);
		$sql = "INSERT INTO ".$this->dbfix.$table." ($field) VALUES ($value)";		
		return $this->query($sql);
	}
	function update($talbe, $dataArray, $where)
	{
		$_sql = array();
		foreach ($dataArray as $key =>$value)
		{
			$_sql[] = "`$key`='$value'";
		}
		$value=implode(',',$_sql);		
		$sql = "UPDATE ".$this->dbfix.$talbe." SET $value WHERE $where";	
		return $this->query($sql);
	}
	function delete($table,$where)
	{
		if(is_numeric($where))
		{
			$str= "id=$where limit 1";	
		}
		elseif(is_array($where))
		{
			$str=' 1=1 ';
			foreach($where as $k=>$v)
			{
				$str.=" and `$k`='$v'";	
			}
		}
		$sql="delete from {$this->dbfix}$table where $str";
		return $this->query($sql);
	}
	
	function one($table,$array=array())
	{
		$str=' where 1=1';
		foreach($array as $k=>$v)
		{
			$str.=" and `$k`='$v'";	
		}
		$sql='select * from '.$this->dbfix.$table.$str.' limit 1';
		//echo $sql;
		return $this->get_one($sql);
	}
	
	function insert_id()
	{
		return mysql_insert_id();
	}
	
	function affected_rows()
	{
		mysql_affected_rows();	
	}
	
	function ver(){
		list($version) = explode('-', mysql_get_server_info());
		return $version;
	}

	function error_msg($msg) {
		$mysql_dir = 'data';
		$dtime=date("Y-m-d",time()); 
		$ip =$this->ip();
		$file = "http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]; 
		if(!file_exists($mysql_dir."/mysql_error")){
		  mkdir($mysql_dir."/mysql_error",0777);   
		}    
		$fp =   @fopen($mysql_dir."/mysql_error/".$dtime.".log","a+");
		$time=date("H:i:s");			
		//debug_print_backtrace();			
		$str="{time:$time}\t{ip:".$ip."}\t{error:".$msg."}\t{file:".$file."}\t\r\n";
		@fputs($fp,$str);
		@fclose($fp);
		echo $str;
		return false;
	}
	function ip() {
		if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip_address = $_SERVER["HTTP_CLIENT_IP"];
		}else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$ip_address = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}else if(!empty($_SERVER["REMOTE_ADDR"])){
			$ip_address = $_SERVER["REMOTE_ADDR"];
		}else{
			$ip_address = '';
		}
		return $ip_address;
	}
	//禁止克隆
	final public function __clone(){}	
	//析构函数-资源回收
	function __destruct()
	{
		if (is_resource($this->conn)) 
		{
			mysql_close($this->conn);
		}
		$this->conn=null;
	}
}