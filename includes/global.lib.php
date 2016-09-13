<?php

function &cache_server()
{
    import('cache.lib');
    static $CS = null;
    if ($CS === null)
    {
        switch (CACHE_SERVER)
        {
            case 'memcached':
                $CS = new MemcacheServer(array(
                    'host'  => MEMCACHE_HOST,
                    'port'  => MEMCACHE_PORT,
                ));
            break;
            default:
                $CS = new PhpCacheServer;
                $CS->set_cache_dir(ROOT_PATH . '/temp/caches');
            break;
        }
    }

    return $CS;
}

/**
 *    获取商品类型对象
 *
 *    @author    Garbin
 *    @param     string $type
 *    @param     array  $params
 *    @return    void
 */
function &gt($type, $params = array())
{
    static $types = array();
    if (!isset($types[$type]))
    {
        /* 加载订单类型基础类 */
        include_once(ROOT_PATH . '/includes/goods.base.php');
        include(ROOT_PATH . '/includes/goodstypes/' . $type . '.gtype.php');
        $class_name = ucfirst($type) . 'Goods';
        $types[$type]   =   new $class_name($params);
    }

    return $types[$type];
}

/**
 *    获取订单类型对象
 *
 *    @author    Garbin
 *    @param    none
 *    @return    void
 */
function &ot($type, $params = array())
{
    static $order_type = null;
    if ($order_type === null)
    {
        /* 加载订单类型基础类 */
        include_once(ROOT_PATH . '/includes/order.base.php');
        include(ROOT_PATH . '/includes/ordertypes/' . $type . '.otype.php');
        $class_name = ucfirst($type) . 'Order';
        $order_type = new $class_name($params);
    }

    return $order_type;
}

/**
 *    获取数组文件对象
 *
 *    @author    Garbin
 *    @param     string $type
 *    @param     array  $params
 *    @return    void
 */
function &af($type, $params = array())
{
    static $types = array();
    if (!isset($types[$type]))
    {
        /* 加载数据文件基础类 */
        include_once(ROOT_PATH . '/includes/arrayfile.base.php');
        include(ROOT_PATH . '/includes/arrayfiles/' . $type . '.arrayfile.php');
        $class_name = ucfirst($type) . 'Arrayfile';
        $types[$type]   =   new $class_name($params);
    }

    return $types[$type];
}

/**
 *    连接会员系统
 *
 *    @author    Garbin
 *    @return    Passport 会员系统连接接口
 */
function &ms()
{
    static $ms = null;
    if ($ms === null)
    {
        include(ROOT_PATH . '/includes/passport.base.php');
        include(ROOT_PATH . '/includes/passports/' . MEMBER_TYPE . '.passport.php');
        $class_name  = ucfirst(MEMBER_TYPE) . 'Passport';
		
        $ms = new $class_name();
    }

    return $ms;
}


/**
 *    获取用户头像地址
 *
 *    @author    Garbin
 *    @param     string $portrait
 *    @return    void
 */
function portrait($user_id, $portrait, $size = 'small')
{
    switch (MEMBER_TYPE)
    {
        case 'uc':
            return UC_API . '/avatar.php?uid=' . $user_id . '&amp;size=' . $size;
        break;
        default:
            return empty($portrait) ? Conf::get('default_user_portrait') : $portrait;
        break;
    }
}

/**
 *    获取环境变量
 *
 *    @author    Garbin
 *    @param     string $key
 *    @param     mixed  $val
 *    @return    mixed
 */
function &env($key, $val = null)
{
    $vkey = $key ? strtokey("{$key}", '$GLOBALS[\'EC_ENV\']') : '$GLOBALS[\'EC_ENV\']';
    if ($val === null)
    {
        /* 返回该指定环境变量 */
        $v = eval('return ' . $vkey . ';');

        return $v;
    }
    else
    {
        /* 设置指定环境变量 */
        eval($vkey . ' = $val;');

        return $val;
    }
}

/**
 *    获取订单状态相应的文字表述
 *
 *    @author    Garbin
 *    @param     int $order_status
 *    @return    string
 */
function order_status($order_status)
{
    $lang_key = '';
    switch ($order_status)
    {
        case ORDER_PENDING:
            $lang_key = 'order_pending';
        break;
        case ORDER_SUBMITTED:
            $lang_key = 'order_submitted';
        break;
        case ORDER_ACCEPTED:
            $lang_key = 'order_accepted';
        break;
        case ORDER_SHIPPED:
            $lang_key = 'order_shipped';
        break;
        case ORDER_FINISHED:
            $lang_key = 'order_finished';
        break;
        case ORDER_CANCELED:
            $lang_key = 'order_canceled';
        break;
    }

    return $lang_key  ? Lang::get($lang_key) : $lang_key;
}

/**
 *    转换订单状态值
 *
 *    @author    Garbin
 *    @param     string $order_status_text
 *    @return    void
 */
function order_status_translator($order_status_text)
{
    switch ($order_status_text)
    {
        case 'canceled':    //已取消的订单
            return ORDER_CANCELED;
        break;
        case 'all':         //所有订单
            return '';
        break;
        case 'pending':     //待付款的订单
            return ORDER_PENDING;
        break;
        case 'submitted':   //已提交的订单
            return ORDER_SUBMITTED;
        break;
        case 'accepted':    //已确认的订单，待发货的订单
            return ORDER_ACCEPTED;
        break;
        case 'shipped':     //已发货的订单
            return ORDER_SHIPPED;
        break;
        case 'finished':    //已完成的订单
            return ORDER_FINISHED;
        break;
        default:            //所有订单
            return '';
        break;
    }
}

/**
 *    获取邮件内容
 *
 *    @author    Garbin
 *    @param     string $mail_tpl
 *    @param     array  $var
 *    @return    array
 */
function get_mail($mail_tpl, $var = array())
{
    $subject = '';
    $message = '';

    /* 获取邮件模板 */
    $model_mailtemplate =& af('mailtemplate');
    $tpl_info   =   $model_mailtemplate->getOne($mail_tpl);
    if (!$tpl_info)
    {
        return false;
    }

    /* 解析其中变量 */
    $tpl =& v(true);
    $tpl->direct_output = true;
    $tpl->assign('site_name', Conf::get('site_name'));
    $tpl->assign('site_url', SITE_URL);
    $tpl->assign('mail_send_time', local_date('Y-m-d H:i', gmtime()));
    foreach ($var as $key => $val)
    {
        $tpl->assign($key, $val);
    }
    $subject = $tpl->fetch('str:' . $tpl_info['subject']);
    $message = $tpl->fetch('str:' . $tpl_info['content']);

    /* 返回邮件 */

    return array(
        'subject'   => $subject,
        'message'   => $message
    );
}

/**
 *    获取消息内容
 *
 *    @author    Garbin
 *    @param     string $msg_tpl
 *    @param     array  $var
 *    @return    string
 */
function get_msg($msg_tpl, $var = array())
{
    /* 获取消息模板 */
    $ms = &ms();
    $msg_content = Lang::get($msg_tpl);

    $search = array_keys($var);
    $replace = array_values($var);

    /* 解析其中变量 */
    array_walk($search, create_function('&$str', '$str = "{\$" . $str. "}";'));
    $msg_content = str_replace($search, $replace, $msg_content);
    return $msg_content;
}

/**
 *    获取邮件发送网关
 *
 *    @author    Garbin
 *    @return    object
 */
function &get_mailer()
{
    static $mailer = null;
    if ($mailer === null)
    {
        /* 使用mailer类 */
        import('mailer.lib');
        $sender     = Conf::get('site_name');
        $from       = Conf::get('email_addr');
        $protocol   = Conf::get('email_type');
        $host       = Conf::get('email_host');
        $port       = Conf::get('email_port');
        $username   = Conf::get('email_id');
        $password   = Conf::get('email_pass');
        $mailer = new Mailer($sender, $from, $protocol, $host, $port, $username, $password);
    }

    return $mailer;
}

/**
 *    模板列表
 *
 *    @author    Garbin
 *    @param     strong $who
 *    @return    array
 */
function list_template($who)
{
    $theme_dir = ROOT_PATH . '/themes/' . $who;
    $dir = dir($theme_dir);
    $array = array();
    while (($item  = $dir->read()) !== false)
    {
        if (in_array($item, array('.', '..')) || $item{0} == '.' || $item{0} == '$')
        {
            continue;
        }
        $theme_path = $theme_dir . '/' . $item;
        if (is_dir($theme_path))
        {
            if (is_file($theme_path . '/theme.info.php'))
            {
                $array[] = $item;
            }
        }
    }

    return $array;
}

/**
 *    列表风格
 *
 *    @author    Garbin
 *    @param     string $who
 *    @return    array
 */
function list_style($who, $template = 'default')
{
    $style_dir = ROOT_PATH . '/themes/' . $who . '/' . $template . '/styles';
    $dir = dir($style_dir);
    $array = array();
    while (($item  = $dir->read()) !== false)
    {
        if (in_array($item, array('.', '..')) || $item{0} == '.' || $item{0} == '$')
        {
            continue;
        }
        $style_path = $style_dir . '/' . $item;
        if (is_dir($style_path))
        {
            if (is_file($style_path . '/style.info.php'))
            {
                $array[] = $item;
            }
        }
    }

    return $array;
}


/**
 *    获取挂件列表
 *
 *    @author    Garbin
 *    @return    array
 */
function list_widget()
{
    $widget_dir = ROOT_PATH . '/external/widgets';
    static $widgets    = null;
    if ($widgets === null)
    {
        $widgets = array();
        if (!is_dir($widget_dir))
        {
            return $widgets;
        }
        $dir = dir($widget_dir);
        while (false !== ($entry = $dir->read()))
        {
            if (in_array($entry, array('.', '..')) || $entry{0} == '.' || $entry{0} == '$')
            {
                continue;
            }
            if (!is_dir($widget_dir . '/' . $entry))
            {
                continue;
            }
            $info = get_widget_info($entry);
            $widgets[$entry] = $info;
        }
    }

    return $widgets;
}

/**
 *    获取挂件信息
 *
 *    @author    Garbin
 *    @param     string $id
 *    @return    array
 */
function get_widget_info($name)
{
    $widget_info_path = ROOT_PATH . '/external/widgets/' . $name . '/widget.info.php';

    return include($widget_info_path);
}

function i18n_code()
{
    $code = 'zh-CN';
    $lang_code = substr(LANG, 0, 2);
    switch ($lang_code)
    {
        case 'sc':
            $code = 'zh-CN';
        break;
        case 'tc':
            $code = 'zh-TW';
        break;
        default:
            $code = 'zh-CN';
        break;
    }

    return $code;
}

/**
 *    从字符串获取指定日期的结束时间(24:00)
 *
 *    @author    Garbin
 *    @param     string $str
 *    @return    int
 */
function gmstr2time_end($str)
{
    return gmstr2time($str) + 86400;
}

/**
 *    获取URL地址
 *
 *    @author    Garbin
 *    @param     mixed $query
 *    @param     string $rewrite_name
 *    @return    string
 */
function url($query, $rewrite_name = null)
{
    $re_on  = Conf::get('rewrite_enabled');
    $url = '';
    if (!$re_on)
    {
        /* Rewrite未开启 */
        $url = 'index.php?' . $query;
    }
    else
    {
        /* Rewrite已开启 */
        $re =& rewrite_engine();
        $rewrite = $re->get($query, $rewrite_name);

        $url = ($rewrite !== false) ? $rewrite : 'index.php?' . $query;
    }

    return str_replace('&', '&amp;', $url);
}

/**
 *    获取rewrite engine
 *
 *    @author    Garbin
 *    @return    Object
 */
function &rewrite_engine()
{
    $re_name= Conf::get('rewrite_engine');
    static $re = null;
    if ($re === null)
    {
        include(ROOT_PATH . '/includes/rewrite.base.php');
        include(ROOT_PATH . '/includes/rewrite_engines/' . $re_name . '.rewrite.php');
        $re_class_name = ucfirst($re_name) . 'Rewrite';
        $re = new $re_class_name();
    }

    return $re;
}

/**
 *    转换团购活动状态值
 *
 *    @author    Garbin
 *    @param     string $status_text
 *    @return    void
 */
function groupbuy_state_translator($state_text)
{
    switch ($state_text)
    {
        case 'all':         //全部团购活动
            return '';
        break;
        case 'on':         //进行中的团购活动
            return GROUP_ON;
        break;
        case 'canceled':    //已取消的团购活动
            return GROUP_CANCELED;
        break;
        case 'pending':     //未发布的团购活动
            return GROUP_PENDING;
        break;
        case 'finished':     //已完成的团购活动
            return GROUP_FINISHED;
        break;
        case 'end':     //已完成的团购活动
            return GROUP_END;
        break;
        default:            //全部团购活动
            return '';
        break;
    }
}

/**
 *    获取团购状态相应的文字表述
 *
 *    @author    Garbin
 *    @param     int $group_state
 *    @return    string
 */
function group_state($group_state)
{
    $lang_key = '';
    switch ($group_state)
    {
        case GROUP_PENDING:
            $lang_key = 'group_pending';
        break;
        case GROUP_ON:
            $lang_key = 'group_on';
        break;
        case GROUP_CANCELED:
            $lang_key = 'group_canceled';
        break;
        case GROUP_FINISHED:
            $lang_key = 'group_finished';
        break;
        case GROUP_END:
            $lang_key = 'group_end';
        break;
    }

    return $lang_key  ? Lang::get($lang_key) : $lang_key;
}


/**
 *    计算剩余时间
 *
 *    @author    Garbin
 *    @param     string $format
 *    @param     int $time;
 *    @return    string
 */
function lefttime($time, $format = null)
{
    $lefttime = $time - gmtime();
    if ($lefttime < 0)
    {
        return '';
    }
    if ($format === null)
    {
        if ($lefttime < 3600)
        {
            $format = Lang::get('lefttime_format_1');
        }
        elseif ($lefttime < 86400)
        {
            $format = Lang::get('lefttime_format_2');
        }
        else
        {
            $format = Lang::get('lefttime_format_3');
        }
    }
    $d = intval($lefttime / 86400);
    $lefttime -= $d * 86400;
    $h = intval($lefttime / 3600);
    $lefttime -= $h * 3600;
    $m = intval($lefttime / 60);
    $lefttime -= $m * 60;
    $s = $lefttime;

    return str_replace(array('%d', '%h', '%i', '%s'),array($d, $h,$m, $s), $format);
}


/**
 * 多维数组排序（多用于文件数组数据）
 *
 * @author Hyber
 * @param array $array
 * @param array $cols
 * @return array
 *
 * e.g. $data = array_msort($data, array('sort_order'=>SORT_ASC, 'add_time'=>SORT_DESC));
 */
function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;
}

/**
 * 短消息过滤
 *
 * @return string
 */
function short_msg_filter($string)
{
    $ms = & ms();
    return $ms->pm->msg_filter($string);
}

//webservice对接函数
/*function webService($func,$post_data=array())  
{

$url='http://192.168.1.150:888/Algorithm.asmx/'.$func;	
	$ch = curl_init();  		
	$o="";  
	foreach ($post_data as $k=>$v)  
	{  
	$o.= "$k=".urlencode($v)."&";  
	}  
	$post_data=substr($o,0,-1); 
	curl_setopt($ch, CURLOPT_URL,$url); 
		
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
	
	$result = curl_exec($ch); 	
	
	curl_close ($ch); 
	unset($ch); 
//echo iconv("utf-8",'gb2312',$result);
	//echo $result;
	
	//$result=getXmlData($result);
	//print_r($result);
	$xml = simplexml_load_string($result);
	

//print_r($xml);
//$login = $xml->login;//这里返回的依然是个SimpleXMLElement对象
//print_r($login);
//$login = (string) $xml->login;//在做数据比较时，注意要先强制转换
//print_r($login);
	return trim($xml[0]);

}*/

function webService($func,$post_data=array()) 
{
	/*global $webserv_ip1;*/	
	//global $_S;	
	//$client = new SoapClient('http://'.$_S['canshu']['webservip'].'/Algorithm.asmx?WSDL');	 
	$client = new SoapClient('http://'.WEBSERV_IP1.'/Algorithm.asmx?WSDL');	 
	$client->soap_defencoding = 'utf-8'; 
	$client->decode_utf8 = false;  
	$client->xml_encoding = 'utf-8';	 
	$headers = new SoapHeader('http://localhost/','header',array('name'=>'web','password'=>'112233'));
	$client->__setSoapHeaders(array($headers));
	$result = $client->__Call($func, array($post_data));	
	if (is_soap_fault($result))
	{
	    trigger_error("SOAP Fault: ", E_USER_ERROR);
		return array();
	}
	else
	{		
		$result=objtoarr($result);//只返回一个，但数组下标不一样
		foreach($result as $re)
		{			
			return $re;	
		}
	}	
}
function c_cal()//计算返利
{
	/*global $_S;
	$bl=explode(',',$_S['canshu']['fenhongbili']);
	$post_data=array();
	$post_data['dividends']=explode(',',$_S['canshu']['guquandaxiao']);
	$post_data['Probability']=explode(',',$_S['canshu']['Probability']);
	$post_data['Probability12016']=explode(',',$_S['canshu']['Probability']);
	$post_data['type16']=$bl[0];
	$post_data['type12016']=$bl[1];
	return webService('C_Cal',$post_data);*/
}
/*function getjifen($money)
{
	global $_S;
	$lv=$_S['canshu']['jifenxianjin'];	
	return $money*$lv;	
}
*/
function objtoarr($obj)
{
	$ret = array();
	foreach($obj as $key =>$value){
		if(gettype($value) == 'array' || gettype($value) == 'object'){
			$ret[$key] = objtoarr($value);
		}
		else{
			$ret[$key] = $value;
		}
	}
	return $ret;
}

function level($level)
{
if ($level==1)
{
$str="<img src='/themes/mall/default/styles/default/images/fu1.jpg'>";
}
/*else
{
$str="<img src='/themes/mall/default/styles/default/images/fu.jpg'>";
}*/
return $str;
}

function dengji($dengji)
{	
	$dengji=$dengji-1;
	if($dengji>0)
	{	
		$str="<img src='/images/vip".$dengji.".gif' width=70px>";
		return $str;
	}

} 

function jiekuan($dengji)
{
	$str=2900*($dengji-1);
	if($str<0)
	{
		$str=0;
	}
	return $str;
} 

function m_21($money)
{
	return ceil($money*121)/100;
	
}


 
 function getSFZArea($ID)
{
	include_once(ROOT_PATH. '/includes/idcheck.class.php');
	
	$chk=new IDCheck($ID);
	if(($data=$chk->Part())==False)
	{
		return array();
	}
	else
	{
		return $data;
	}
}
function sendmail($subject,$body,$to)
{
	include_once(ROOT_PATH.'/includes/mail/mail.php');
	return Mail::send($subject, $body,  $to);
}
/*function sendmail($subject,$body,$to)
{
	include_once(ROOT_PATH.'/mail1/test.php');
	return Mail::send($subject, $body,  $to);
}*/

//生成二维码
function qrcode($data,$dir,$filename,$level='L',$size=4,$marg=0,$col='#000000')
{   
	global $color;
	$color=$col;
	if(empty($data))	return '';
	if(!in_array($level,array('L','M','Q','H'))) $level='L';
	$size= min(max((int)$size, 1), 10);
	echo $dir;
	if (!file_exists($dir))      mkdir($dir);
 	$filename = $dir.$filename;
	if (!file_exists($filename))
	{
		include_once(ROOT_PATH."/includes/phpqrcode/qrlib.php");
		QRcode::png($data, $filename,$level, $size,$marg); 
	}
	return $filename; 
}
//DeCode(密文,'D',密钥); 解密
//DeCode(明文,'E',密钥); 加密

function DeCode($string,$operation,$key='cgqhcYpp')
{
	$key=md5($key);
	$key_length=strlen($key);
	$string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;

	$string_length=strlen($string);
	$rndkey=$box=array();
	$result='';
	for($i=0;$i<=255;$i++)
	{
		$rndkey[$i]=ord($key[$i%$key_length]);
		$box[$i]=$i;
	}
	for($j=$i=0;$i<256;$i++)
	{
		$j=($j+$box[$i]+$rndkey[$i])%256;
		$tmp=$box[$i];
		$box[$i]=$box[$j];
		$box[$j]=$tmp;
	}
	for($a=$j=$i=0;$i<$string_length;$i++)
	{
		$a=($a+1)%256;
		$j=($j+$box[$a])%256;
		$tmp=$box[$a];
		$box[$a]=$box[$j];
		$box[$j]=$tmp;
		$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
	}
	if($operation=='D')
	{
		if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
		{
			return substr($result,8);
		}
		else
		{
			return'';
		}
	}
	else
	{
		return str_replace('=','',base64_encode($result));
	}
}
function getHTML($url,$data=array())
{
	//require_once ("HttpClient.class.php");	
	
	$row = parse_url($url);
	if(!isset($row['port'])) $row['port']=80;
	$host = $row['host'];
	$port = $row['port'] ? $row['port']:80;
	
	$post='';//要提交的内容.
	foreach($data as $k=>$v)
	{
		//$post.=$k.'='.$v.'&';
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
	}
	$fp = fsockopen($host, $port, $errno, $errstr, 30); 
	if (!$fp)
	{ 
		echo "$errstr ($errno)<br />\n"; 
	} 
	else 
	{
		$header = "POST $url HTTP/1.1\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent: MSIE\r\n";
		$header .= "Host: $host\r\n"; 
		$header .= "Content-Length: ".strlen($post)."\r\n";
		$header .= "Connection: Close\r\n\r\n"; 
		$header .= $post."\r\n\r\n";		
		fputs($fp, $header); 
		//$status = stream_get_meta_data($fp);
		
		while (!feof($fp)) 
		{
			$tmp .= fgets($fp, 128);
		}
		fclose($fp);
		$tmp = explode("\r\n\r\n",$tmp);
		unset($tmp[0]);
		$tmp= implode("",$tmp);
		
		/*while (!feof($fp)) 
		{
		 if(($header = fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
			break;
		 }
		}
		$tmp = ""; 
		while (!feof($fp))
		{ 
			$tmp .= fgets($fp, 128); 
		}
		fclose($fp); */
	}
	return $tmp;
}

function gethtnum($user_id)
{
	$len=strlen($user_id);
	if($len<6)
	{	$str='';
		for($i=0;$i<6-$len;$i++)
		{
			$str.='0'; 	
		}
		return 'psht'.$str.$user_id;
	}
	else
	{
		return 'psht'.$user_id;
	}
}


function format_price($money,$len=5,$type=1)//type 1是四舍五舍 2是四入五入
{
	if($type==1)
	{	
		//$pri=substr(sprintf("%.6f", $mun), 0, -1);
		$_arr=explode('.',$money);
		if(isset($_arr[1]))
		{
			$_a=substr($_arr[1],0,5);
			$pri=$_arr[0].'.'.$_a;
		}
		else
		{
			$pri=$_arr[0];	
		}
	}
	else
	{
		$pri=ceil($money*100000)/100000;		
	}
	return $pri;
}


function bianma($str)
{
	return iconv('utf-8','gb2312',$str);
}

?>
