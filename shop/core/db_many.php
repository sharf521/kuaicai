<?php
class db_many
{
    
    private static $object = array();
    protected static $conn = array();
    private $i;
    
    //构造函数-初始化db
    protected function __construct($i = 1)
    {
        
        global $db_conf;
        $this->i = $i;
        
        
        self::$conn[$this->i] = mysql_connect($db_conf[$i]['host'] . ':' . $db_conf[$i]['port'], $db_conf[$i]['user'], $db_conf[$i]['pwd'], true);
        
        if (!self::$conn[$this->i]) {
            exit('db_' . $this->i . ' connect failed !');
        }
        
        if (!mysql_select_db($db_conf[$i]['name'], self::$conn[$this->i])) {
            exit('db_' . $this->i . ' select failed');
        }
        
        mysql_query('set names ' . $db_conf[$i]['language'], self::$conn[$this->i]);
    }
    
    //禁止克隆
    final public function __clone()
    {
    }
    
    //返回一个数据库操作的唯一实例对象
    static function get($i)
    {
        if (!(isset(self::$object[$i]) && self::$object[$i] instanceof self)) {
            self::$object[$i] = new self($i);
        }
        return isset(self::$object[$i]) ? self::$object[$i] : null;
    }
    
    //析构函数-资源回收
    function __destruct()
    {
        if (is_resource(self::$conn)) {
            mysql_close(self::$conn);
        }
        self::$conn   = null;
        self::$object = null;
    }
    
    
    
    /********************************
    ///////以下方法可对外访问////////
    ********************************/
    
    //执行SQL语句
    function query($sql, $is_read = true)
    {
        $result = mysql_query($sql, self::$conn[$this->i]);
        
        if ($result)
            return $result;
        
        $info = $sql . ' {' . mysql_error() . '}';
        
        exit($info);
    }
    
    //记录的添加、删除、修改、查询、事务
    function add($table, $data, $option = '') //添加数据记录
    {
        //以数组形式写入
        if (is_array($data)) {
            $field = '(' . implode(",", array_keys($data)) . ')';
            $value = "('" . implode("','", array_values($data)) . "')";
            $this->query("insert into $table $field values $value", false);
            return mysql_insert_id();
        }
        
        //批量写入
        $value = is_array($option) ? implode(',', $option) : $option;
        $this->query("insert into $table $data values $value", false);
        return mysql_insert_id();
    }
    
    function del($table, $condition) //删除数据记录
    {
        return $this->query("delete from $table where $condition", false);
    }
    
    function mod($table, $data, $condition, $limit = '') //修改数据记录
    {
        //以数组形式修改
        if (is_array($data)) {
            $feild_info = '';
            foreach ($data as $key => $value) {
                $feild_info .= $key . "='" . $value . "',";
            }
            $data = trim($feild_info, ',');
        }
        
        if ($limit != '')
            $limit = "limit $limit";
        return $this->query("update $table set $data where $condition $limit", false);
    }
    
    function field($table, $field, $condition = null) //返回单字段信息（表中单元格）
    {
        if ($condition === null) {
            $execute_sql = "select $field from $table limit 0,1";
        } else {
            $execute_sql = "select $field from $table where $condition limit 0,1";
        }
        $result = $this->query($execute_sql);
        $rs     = mysql_fetch_array($result);
        return $rs[$field];
    }
    
    function rs($sql, $mode = false) //查询一条数据记录（数字、关联数组）
    {
        $result = $this->query($sql . ' limit 0,1');
        return $mode ? mysql_fetch_object($result) : mysql_fetch_array($result, MYSQL_ASSOC);
    }
    
    function rs_list($sql, $mode = false) //查询多条数据记录（数组-数组）模式
    {
        $result = $this->query($sql);
        $arr    = null;
        while ($row = $mode ? mysql_fetch_object($result) : mysql_fetch_array($result, MYSQL_ASSOC)) {
            $arr[] = $row;
        }
        mysql_free_result($result);
        return $arr;
    }
    
    function page_list($sql, $rs_count, $page, $page_size, $mode = false) //分页查询方法
    {
        if (!$page_size)
            $page_size = config('db_0|page_size', 'db_many');
        $page_count = ceil($rs_count / $page_size);
        $page       = $page ? $page : 1;
        $page       = ($page > $page_count) ? $page_count : $page;
        $start_rs   = ($page_size > $rs_count) ? 0 : (($page - 1) * $page_size);
        $data       = $this->rs_list($sql . " limit $start_rs,$page_size", $mode);
        return array(
            (int) $page_count,
            $data
        );
    }
    
    function transaction($result) //事务处理
    {
        if ($result) {
            $this->query("commit");
            return true;
        }
        $this->query("rollback");
    }
    
    //取得指定库/表字段
    function get_field($table_name = null)
    {
        if ($table_name) {
            $result = $this->query('select * from ' . $table_name . ' limit 0,1');
            while ($field = mysql_fetch_field($result)) {
                $name_list[] = $field->name;
            }
        } else {
            $result = $this->query("show tables");
            while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
                $name_list[] = $row[0];
            }
        }
        return $name_list;
    }
    
    //db类方法帮助提示
    function __toString()
    {
        $info = '<br><font color="green">执行请求：query($sql,$is_read=true)<br>';
        $info .= '指定字段：field($det_name,$table,$condition)<br>';
        $info .= '一条记录：rs($sql,$mode=false)<br>';
        $info .= '多条记录：rs_list($sql,$mode=false)<br>';
        $info .= '记录分页：page_list($sql,$rs_count,$page,$page_size,$mode=false)<br>';
        $info .= '插入记录：add($table,$data,$option="")<br>';
        $info .= '修改记录：mod($table,$data,$condition)<br>';
        $info .= '删除记录：del($table,$condition)<br>';
        $info .= '事务处理：transaction($result)<br>';
        $info .= '查表字段：get_field($table_name=null)</font><br><br>';
        return $info;
    }
    
    
}

/*
$db_conf[1]['host']     = 'localhost';      //数据库主机	
$db_conf[1]['user']     = 'root';      //数据库用户名	
$db_conf[1]['pwd']      = 'root';  //数据库用户密码	
$db_conf[1]['name']     = 'pay11';      //数据库名	

$db_conf[1]['port']     = '3306';      //端口		
$db_conf[1]['prefix']   = 'pay_'; //表名前缀	
$db_conf[1]['language'] = 'utf8'; //数据库字符集 gbk,latin1,utf8
require ROOT.'/include/db_many.php';
$db1=db_many::get(1);
$ta=$db1->get_field();
print_r($ta);
exit;
*/