<?php
$web_id=checkPost(strip_tags($_GET['web_id']));
$user_name=checkPost(strip_tags($_GET['user_name']));
$user_id=intval($_GET['user_id']);
$starttime=checkPost(strip_tags($_GET['starttime']));
$endtime=checkPost(strip_tags($_GET['endtime']));
$page = empty($_GET['page']) ? 1 : $_GET['page'];
$type=$_REQUEST['type'];
$url="?act=$act&starttime=$starttime&endtime=$endtime&page=$page&web_id=$web_id&type=$type";
$sqlW='1=1';

$PageSize = 10;  //每页显示记录数	
if($user_id!=0)
{
	$row=$db->get_one("select web_id from {member} where user_id='{$user_id}' limit 1");
	$web_id=$row['web_id'];
}
if($user_name!=0)
{
	$row=$db->get_one("select web_id from {member} where user_name='{$user_name}' limit 1");
	$web_id=$row['web_id'];
}
$data=array();
$openurl='http://'.$_S['canshu']['webservip']."/connstr.asp?starttime=$starttime&endtime=$endtime&page=$page&web_id=$web_id&type=$type&rp=10";
//echo $openurl;
$str=sock_open($openurl,$data);

pageTop($modulename.'管理');?>
<link href="flexigrid/css/flexigrid.css" type="text/css" rel="stylesheet" />
<script type="text/javascript"	src="flexigrid/js/jquery.min.js"></script>
<script language="javascript" charset="utf-8" src="flexigrid/js/flexigrid.js"></script>
<div class="div_title"><?=getHeadTitle(array('WebServices队列列表'=>''))?>&nbsp;&nbsp;</div>	
<script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>
<form method="GET">
  Web_ID:<input type="text" name="web_id" value="<?php echo $web_id;?>"/>	
  会员ID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="5"/>	
  用户名：<input type="text" name="user_name" value="<?=$user_name?>" size="5"/>

  <input id="starttime"  name="starttime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'starttime'});" value="<?=$starttime?>">
  <input id="endtime"  name="endtime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'endtime'});" value="<?=$endtime?>">
  
  <select name="type">
    <option value="" selected>选择队列</option>
  	<option value="0" <? if($_REQUEST['type']==='0'){echo "selected";}?>>12%</option>
    <option value="1" <? if($_REQUEST['type']=='1'){echo "selected";}?>>16%</option>
    <option value="2" <? if($_REQUEST['type']=='2'){echo "selected";}?>>31%</option>
  </select>
  <input type="submit" value="筛选条件">
  <input type="hidden" name="act" value="<?=$act?>">   

</form>

<?

$str=explode('[#]',$str);

$list=explode('<br>',$str[0]);
$result=array();
foreach ($list as $i=>$v)  //转换成数组
{
	$tem=explode('|',$v);
	foreach($tem as $_i=>$_v)
	{
		if($_i==0)	$result[$i]['listid']=$_v;
		if($_i==1)	$result[$i]['web_id']=$_v;
		if($_i==2)	$result[$i]['money']=$_v;
		if($_i==3)	$result[$i]['type']=$_v;
		if($_i==4)	$result[$i]['addtime']=$_v;
		if($_i==5)	$result[$i]['RebatesMoney']=$_v;
		if($_i==6)	$result[$i]['RebatesStatus']=$_v;				
		if($_i==10)	$result[$i]['Aside4']=$_v;
		if($_i==11)	$result[$i]['Aside5']=$_v;			
	}
}
$arr_user=array();
foreach($result as $i=>$v)
{
	if ($v['type'] == 0)
	{
		$result[$i]['inmoney'] = $v['money'] * 0.15;
	}
	elseif ($v['type'] == 1)
	{
		$result[$i]['inmoney'] = $v['money'] * 0.16;
	}
	elseif ($v['type'] == 2)
	{
		$result[$i]['inmoney'] = $v['money'] * 0.31;
	}
	if($v['RebatesStatus']==1)
	{
		$result[$i]['RebatesStatus']='己结束';	
	}
	else
	{
		$result[$i]['RebatesStatus']='正常';
		$result[$i]['Aside4']='';
	}
	if(array_key_exists($v['web_id'],$arr_user))
	{
		$tem=explode('[#]',$arr_user[$v['web_id']]);
		$result[$i]['user_id'] = $tem[0];
		$result[$i]['user_name'] = $tem[1];
	}
	else
	{
		$row=$db->get_one("select user_id,user_name from {member} where web_id='{$v['web_id']}' limit 1");
		if($row)
		{
			$result[$i]['user_id'] = $row['user_id'];
			$result[$i]['user_name'] = $row['user_name'];
			$arr_user[$v['web_id']]=$row['user_id'].'[#]'.$row['user_name'];		
		}
	}
}



	
	$RecordCount = $str[1];//获取总记录数
	if(!empty($page))
	{
		$StartRow=($page-1)*$PageSize;
	}
	else
	{
		$StartRow=0;
		$page=1;
	}
	if($RecordCount>0)
	{
		$arr_type=array('12%','16%','双队列');
				?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('队列id','会员id','会员名','类型','排队积分','加入时间','应返还','己返还','状态','结束时间','Web_ID'));
		foreach ($result as $row)
		{
			?>
			<tr <?=getChangeTr()?>>
            	<td><?=$row['listid']?></td>
            	<td align='center'><?=$row['user_id']?></td>
                <td align='center'><?=$row['user_name']?></td>
                <td align='left'>&nbsp;&nbsp;<?=$arr_type[$row['type']]?></td>
                <td align='center'><?=$row['inmoney']?></td>
                 <td align="center"><?=$row['addtime']?></td>
                <td align='center'><?=$row['money']?></td>                
                <td align='left'>&nbsp;&nbsp;<?=$row['RebatesMoney']?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['RebatesStatus']?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['Aside4']?></td>			
				<td align='center'><?=$row['web_id']?></td>
			</tr>
			<?		
		}
		?>
        <tr <?=getChangeTr()?>><td>共：<?=$str[1]?>个队列：</td><td colspan="4"><?
		$typelist=explode('<br>',$str[2]);
		foreach($typelist as $list)
		{
			$v=explode('|',$list);
			if($v[0]==0)
			{
				echo '12队列：';	
			}
			elseif($v[0]==1)
			{
				echo '16队列：';
			}
			elseif($v[0]==2)
			{
				echo '31队列：';
			}
			echo "共：{$v[1]}条，共应返：{$v[2]}，己返：{$v[3]}<br>";
		}
		
		 print_r($data1);?></td></tr>
        </form></table>
		<div class="line"><?=page($RecordCount,$PageSize,$page,$url)?></div>
        <br /><br />

        
		<?php
	}
	else
	{
		echo "<div><br>&nbsp;&nbsp;无数据！</div>";
	}




function sock_open($url,$data=array())
{	
	$row = parse_url($url);
	$host = $row['host'];
	$port = isset($row['port']) ? $row['port']:80;
	
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
		$header = "GET $url HTTP/1.1\r\n"; 
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
	}
	return $tmp;
}
?>