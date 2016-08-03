<?
/******************************
 * $File: mysql.inc.php
 * $Description: ���ݿ⴦���ļ�
 * $Author: ahui 
 * $Time:2010-03-09
 * $Update:None 
 * $UpdateDate:None 
******************************/

class Mysql {
	var $db_link;//���ݿ�������Ϣ
	var $db_show_error;//�Ƿ񽫴�����Ϣ��ӡ����
	var $db_prefix;//���ݿ�ǰ׺��
	
	
	/**
	* ���캯��
	**/
	function Mysql($db_config){
		$this->db_show_error = false;
		$this->db_prefix =$db_config['prefix'];
		$this->db_link = $this->db_connect($db_config);
		$this->mysql_error_path = "data";
		///$this->ip = ip_address();
	}
		
	/**
	* �������ݿ�
	**/
	function db_connect($db_config) {
		//�����������Ƿ��Ѿ���װ���������ݿ�Ĵ˺���
		if (!function_exists('mysql_connect')) {
			$this->db_error_msg('�������ݿ⻹δ��װ����չ');
		}
		
		$db_config['host'] = urldecode($db_config['host']);
		$db_config['user'] = urldecode($db_config['user']);
		$db_config['pwd'] = isset($db_config['pwd']) ? urldecode($db_config['pwd']) : '';
		$db_config['name'] = urldecode($db_config['name']);
		$db_config['language'] = urldecode($db_config['language']);
		//�Ƿ��ж˿ڴ���
		if (isset($db_config['port'])) {
		$db_config['host'] = $db_config['host'] .':'. $db_config['port'];
		}
		
		//�������ݿ�
		$db_link = @mysql_connect($db_config['host'], $db_config['user'], $db_config['pwd'], TRUE, 2);
		if (!$db_link || !mysql_select_db($db_config['name'], $db_link)) {
			$this->db_error_msg("���ݿ�����ʧ�ܣ�mysql_error:".mysql_error());
		}
		
		mysql_query('SET NAMES "'.$db_config['language'].'"', $db_link);
		return $db_link;
	}

	/**
	* sql����
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
			$this->db_error_msg(mysql_error()."ִ��SQL������!".$sql);
		} 
		return $result;
	}
	
	/**
	* �������sql
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
			$this->db_error_msg(mysql_error()."ִ��SQL������!".$sql);
		} 
		return $result;
	}
	
	/**
	* ����memcache
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
	* ִ��һ��SQL���,����ǰһ����¼�������һ����¼
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
						//$_res[$key] = htmlspecialchars($value);//ֱ��ת��
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
					//$_res[$key] = htmlspecialchars($value);//ֱ��ת��
					$_res[$key] =$value;
				}
			}
		}
		return $_res;
		
	}
	
	/**
	* ��ȡȫ���ļ�¼
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
						$_res[$i][$key] = $value;//ֱ��ת��
					}
					$i++;
				}
				$this->db_free_result($result);//�ͷ���Դ
				$memcache->set($key, $_res , 0, $memcachelife);
			}
		}else{
			$result = $this->query($sql);
			$i = 0;
			
			while($res = mysql_fetch_array($result,MYSQL_ASSOC)) {
				foreach ($res as $key => $value){
					$_res[$i][$key] = $value;//ֱ��ת��
				}
				$i++;
			}
			$this->db_free_result($result);//�ͷ���Դ
		}
		return $_res;
		
	}
	
	/**
	 * �ͷż�¼��ռ�õ���Դ
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
	 * ��ȡ�����ȥ��ID
	**/
	function get_insert_id(){
		return mysql_insert_id();
	}
	
	/**
	* ���ط�������mysql�İ汾.
	**/
	function db_version() {
		list($version) = explode('-', mysql_get_server_info());
		return $version;
	}
	

	/**
	* �ر����ݿ�
	**/
	function db_close()	{	
		@mysql_close($this->db_link);
	}
	
	/**
	* �Ƿ��ӡ������Ϣ
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
	* ��ʾ�������Ӵ�����
	**/
	function db_error_msg($msg) 
	{
		die($msg);
		return false;
	}
	
	
	 /**
  * ����: insert($table,$dataArray)
  * ����: ����һ����¼������
  * ����: 
  * $table  ��Ҫ����ı���
  * $dataArray ��Ҫ�����ֶκ�ֵ�����飬��Ϊ�ֶ�����ֵΪ�ֶ�ֵ�����磺array("user_name"=>"����", "user_age"=>"20��");
  * ����   ��������û�����������Ϊ20, insert("users", array("user_name"=>"����", "user_age"=>"20��"))
  *
  * ����: �����¼�ɹ�����True��ʧ�ܷ���False
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
  * ����: update($talbe, $dataArray, $where)
  * ����: ����һ����¼
  * ����: 
  * $table  ��Ҫ���µı���
  * $dataArray ��Ҫ�����ֶκ�ֵ�����飬��Ϊ�ֶ�����ֵΪ�ֶ�ֵ�����磺array("user_name"=>"����", "user_age"=>"20��");
  * $where  �������
  * ����   �����������Ϊ�������û�Ϊ���ģ�����Ϊ21
  *    update("users",  array("user_name"=>"����", "user_age"=>"20��"),  "user_name='����'")
  *
  * ����: ���³ɹ�����True��ʧ�ܷ���False
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
