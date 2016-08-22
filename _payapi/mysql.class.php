<?
/******************************
 * $File: mysql.inc.php
 * $Description: 数据库处理文件
 * $Author: ahui 
 * $Time:2010-03-09
 * $Update:None 
 * $UpdateDate:None 
******************************/

class Mysql {
	var $db_link;//数据库连接信息
	var $db_show_error;//是否将错误信息打印出来
	var $db_prefix;//数据库前缀名
	
	
	/**
	* 构造函数
	**/
	function Mysql($db_config){
		$this->db_show_error = false;
		$this->db_prefix =$db_config['prefix'];
		$this->db_link = $this->db_connect($db_config);
		$this->mysql_error_path = "data";
		///$this->ip = ip_address();
	}
		
	/**
	* 连接数据库
	**/
	function db_connect($db_config) {
		//检查服务器上是否已经安装了连接数据库的此函数
		if (!function_exists('mysql_connect')) {
			$this->db_error_msg('您的数据库还未安装此扩展');
		}
		
		$db_config['host'] = urldecode($db_config['host']);
		$db_config['user'] = urldecode($db_config['user']);
		$db_config['pwd'] = isset($db_config['pwd']) ? urldecode($db_config['pwd']) : '';
		$db_config['name'] = urldecode($db_config['name']);
		$db_config['language'] = urldecode($db_config['language']);
		//是否有端口存在
		if (isset($db_config['port'])) {
		$db_config['host'] = $db_config['host'] .':'. $db_config['port'];
		}
		
		//连接数据库
		$db_link = @mysql_connect($db_config['host'], $db_config['user'], $db_config['pwd'], TRUE, 2);
		if (!$db_link || !mysql_select_db($db_config['name'], $db_link)) {
			$this->db_error_msg("数据库连接失败：mysql_error:".mysql_error());
		}
		
		mysql_query('SET NAMES "'.$db_config['language'].'"', $db_link);
		return $db_link;
	}

	/**
	* sql处理
	**/
	function query($sql="",$noreplace="")
	{
		if($this->db_prefix!='')
		{
			if ($noreplace == ""){
				while (ereg ('{([a-zA-Z0-9_-]+)}', $sql, $regs)) {
				  $found = $regs[1];
				  $sql = ereg_replace("\{".$found."\}",$this->db_prefix.$found, $sql);
				}
			}
		}
		else
		{
			$sql = str_replace(array('{','}'),'',$sql);
		}
		$result = mysql_query($sql);
		if(!$result){
			$this->db_error_msg(mysql_error()."执行SQL语句错误!".$sql);
		} 
		return $result;
	}
	
	/**
	* 处理多条sql
	**/
	function querys($sql="",$noreplace=""){
		$_sql = explode(";",$sql);
		foreach($_sql as $value){
			$value = trim($value);
			if (!empty($value)){
				$result = $this->query($value.";",$noreplace);
			}
		}
		if(!$result){
			$this->db_error_msg(mysql_error()."执行SQL语句错误!".$sql);
		} 
		return $result;
	}
	
	/**
	* 处理memcache
	**/
	function query_memcache($sql, $type = '') {
		global $memcache,$memcachelife;
		$key = md5($sql);
		if(!($query = $memcache->get($key))) {
			$query = $this->query($sql, $type);
			while($item  = $this->fetch_array($query)) {
				$res[] = $item;
			}
			$query = $res;
			$memcache->set($key, $query , 0, $memcachelife);
		}
		return $query;
	}
	

	/**
	* 执行一个SQL语句,返回前一条记录或仅返回一条记录
	**/
	function get_one($sql) {
		global $memcache,$memcachelife,$memcache_result;
		$_res=array();
		if($memcache_result!=0){
			$key = md5($sql."one");
			if(!($query = $memcache->get($key))) {
				$result = $this->query($sql);
				$res = mysql_fetch_array($result,MYSQL_ASSOC);
				if (is_array($res)){
					foreach ($res as $key => $value){
						//$_res[$key] = htmlspecialchars($value);//直接转义
						$_res[$key] =$value;
					}
				}
				$memcache->set($key, $_res , 0, $memcachelife);
			}
		}else{
			$result = $this->query($sql);
			$res = mysql_fetch_array($result,MYSQL_ASSOC);
			if (is_array($res)){
				foreach ($res as $key => $value){
					//$_res[$key] = htmlspecialchars($value);//直接转义
					$_res[$key] =$value;
				}
			}
		}
		return $_res;
		
	}
	
	/**
	* 获取全部的记录
	**/
	function get_all($sql) 	{
		global $memcache,$memcachelife,$memcache_result;
		$_res = array();
		if($memcache_result!=0){
			$key = md5($sql."more");
			if(!($query = $memcache->get($key))) {
				$result = $this->query($sql);
				$i = 0;
				while($res = mysql_fetch_array($result,MYSQL_ASSOC)) {
					foreach ($res as $key => $value){
						$_res[$i][$key] = $value;//直接转义
					}
					$i++;
				}
				$this->db_free_result($result);//释放资源
				$memcache->set($key, $_res , 0, $memcachelife);
			}
		}else{
			$result = $this->query($sql);
			$i = 0;
			
			while($res = mysql_fetch_array($result,MYSQL_ASSOC)) {
				foreach ($res as $key => $value){
					$_res[$i][$key] = $value;//直接转义
				}
				$i++;
			}
			$this->db_free_result($result);//释放资源
		}
		return $_res;
		
	}
	
	/**
	 * 释放记录集占用的资源
	**/
	function db_free_result($result){
		if(is_array($result)) {
			foreach($result as $key => $value){
				if($value) @mysql_free_result($value);
			}
		}else{
			@mysql_free_result($result);
		}
	}

	
	/**
	 * 获取插入进去的ID
	**/
	function get_insert_id(){
		return mysql_insert_id();
	}
	
	/**
	* 返回服务器中mysql的版本.
	**/
	function db_version() {
		list($version) = explode('-', mysql_get_server_info());
		return $version;
	}
	

	/**
	* 关闭数据库
	**/
	function db_close()	{	
		@mysql_close($this->db_link);
	}
	
	/**
	* 是否打印错误信息
	**/
	function db_show_msg($i=false){
		$this->db_show_error = $i;
	}
	
	function db_add($code,$var,$notime="",$fields=""){
		$sql = "insert into `{".$code."}` set ";
		foreach ($var as $key =>$value){
			$_sql[] = "`$key`='$value'";
		}
		$sql = $sql.join(",",$_sql);
		if ($notime==""){
			$sql .= ",addtime='".time()."',addip='$this->ip'";
		}
		$result = $this->query($sql);
		$id = $this->get_insert_id();
		if ($result!=false && $fields!=""){
			$this->db_add_fields($code,$id,$fields);
		}
		
		return $result;
	}
	
	function db_add_fields($code,$id,$fields){
		$sql = "insert into `{".$code."_fields}` set ";
		if (is_array($fields)){
			foreach ($fields as $key =>$value){
				if ($key!=""){
					$sql .= "`$key`='$value',";
				}
			}
		}
		$sql .= "id=$id";
		$this->query($sql);
	}
	
	function db_select($table,$where=""){
		$sql = "select * from `{".$table."}` ";
		if ($where !="") $sql .= " where $where";
		return $this->get_one($sql);
	}
	function db_show_fields($table){
		$sql = "SHOW COLUMNS FROM  `{".$table."}` ";
		$result = $this->get_all($sql);
		foreach ($result as $key => $value){
			$_result[] = $value['Field'];
		}
		return $_result;
	}
	
	function db_selects($table,$where="",$order=""){
		$sql = "select * from `{".$table."}` ";
		if ($where !="") $sql .= " where $where";
		if ($order !="") $sql .= " order by $order";
		return $this->get_all($sql);
	}
	function db_list($table,$where="",$page="",$epage=10,$order=""){
		$_sql="";
		$sql = "select count(*) as num from `{".$table."}` ";
		if ($where !="") $_sql .= " where $where";
		$_result = $this->get_one($sql.$_sql);
		if ($page !=""){
			$vpage = ($page-1)*$epage;
		}
		$sql = "select * from `{".$table."}` $_sql order by ";
		if ($order !="") $sql .= "$order,";
		$sql .= " addtime desc ";
		if ($page !="") $sql .= " limit $vpage,$epage";
		$result = $this->get_all($sql);
		return array("result"=>$result,"num"=>$_result['num']);
	}
	function db_list_res($sql,$page="",$epage=10){
		if ($page !=""){
			$vpage = ($page-1)*$epage;
			$sql .= " limit $vpage,$epage";
		}
		return $this->get_all($sql);
	}
	function db_num($sql){
		$_result = $this->get_one($sql);
		return $_result['num'];
	}
	function db_count($table,$where=""){
		$sql = "select count(*) as num from `{".$table."}` ";
		if ($where !="") $sql .= " where $where";
		$_result = $this->get_one($sql);
		return $_result['num'];
	}
	function db_update($code,$data,$where,$fields=""){
		
		if ($fields!=""){
			$sql = "select * from `{".$code."_fields}` where $where";
			$result = $this->get_one($sql);
			if  ($result==false){
				$sql = "insert into `{".$code."_fields}` set $where";
				$this->query($sql);
			}
			$_sql = array();
			if (is_array($fields)){
				$sql = "update `{".$code."_fields}` set ";
				foreach ($fields as $key =>$value){
					if ($key!=""){
					$_sql[] = "`$key`='$value'";
					}
				}
				$sql .= join(",",$_sql)." where $where";
				$this->query($sql);
			}
			
		}
	
		$sql = "update `{".$code."}` set ";
		$_sql = "";
		foreach ($data as $key =>$value){
			$_sql[] = "`$key`='$value'";
		}
		$sql .= join(",",$_sql)." where $where";
		
		return $this->query($sql);
	}
	

	
	function db_order($table,$order,$where,$type){
		foreach ($type as $key => $id){
			$sql = "update `{".$table."}` set `order`='".$order[$key]."' where `$where`=$id";
			$this->query($sql);
		}
		return true;
	}
	
	function db_delete($table,$where){
		$sql = "delete from `{".$table."}` where $where ";
		return $this->query($sql);
	}
	/**
	* 显示数据链接错误信
	**/
	function db_error_msg($msg) 
	{
		die($msg);
		return false;
	}
	
	
	 /**
  * 方法: insert($table,$dataArray)
  * 功能: 插入一条记录到表里
  * 参数: 
  * $table  需要插入的表名
  * $dataArray 需要插入字段和值的数组，键为字段名，值为字段值，例如：array("user_name"=>"张三", "user_age"=>"20岁");
  * 例如   比如插入用户张三，年龄为20, insert("users", array("user_name"=>"张三", "user_age"=>"20岁"))
  *
  * 返回: 插入记录成功返回True，失败返回False
  */
 function insert($table,$dataArray)
 {
	if (!is_array($dataArray) || count($dataArray)<=0)
	{
		echo ("Invalid parameter");exit();
	}
	foreach($dataArray as $key=>$val)
	{
		$field .= "$key,";
		$value .= "'$val',";	
	}
	$field = substr($field, 0, -1);
	$value = substr($value, 0, -1);
	$sql = "INSERT INTO {$table} ($field) VALUES ($value)";
	if (!$this->query($sql))
	{
		return false;
	}
	return true;
 }

 /**
  * 方法: update($talbe, $dataArray, $where)
  * 功能: 更新一条记录
  * 参数: 
  * $table  需要更新的表名
  * $dataArray 需要更新字段和值的数组，键为字段名，值为字段值，例如：array("user_name"=>"张三", "user_age"=>"20岁");
  * $where  条件语句
  * 例如   比如更新姓名为张三的用户为李四，年龄为21
  *    update("users",  array("user_name"=>"张三", "user_age"=>"20岁"),  "user_name='张三'")
  *
  * 返回: 更新成功返回True，失败返回False
  */
 function update($talbe, $dataArray, $where)
 {
	if (!is_array($dataArray) || count($dataArray)<=0)
	{
		$this->error("Invalid parameter");
	}
	$_sql = array();
	foreach ($dataArray as $key =>$value)
	{
	  	$_sql[] = "`$key`='$value'";
	}
	$value=implode(',',$_sql);
	$sql = "UPDATE {$talbe} SET $value WHERE $where";
	if (!$this->query($sql))
	{
		return false;
	}
	return true;
 }
}
?>
