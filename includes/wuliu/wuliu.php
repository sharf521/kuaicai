<?
date_default_timezone_set('Asia/Shanghai');//ʱ������

require_once('../../data/config.inc.php');

$db_config['port']     = '3306';      //�˿�		
$db_config['prefix']   = 'ecm_'; //CMS����ǰ׺	
$db_config['language'] = 'gbk'; //���ݿ��ַ��� gbk,latin1,utf8,utf8..
require_once('../../chinapnr/mysql.class.php');
$db = new Mysql($db_config);
session_cache_limiter('private, must-revalidate');//����ҳ�治��ջ��� 
session_start();


$shipping_id=(int)$_REQUEST['shipping_id'];
$user_id=(int)$_SESSION['user_info']['user_id'];
function setdata($post)
{
	//$shipping_name=$post['shipping_name'];	
	$ships=array();
	$post['v_val_tr0']='default';
	$post['v_txt_tr0']='ȫ��';
	foreach($post['one'] as $i=>$v)
	{
		$t=trim($post['v_val_tr'.$i]);
		$tt=$post['v_txt_tr'.$i];
		if($t!='')
		{
		
			if($post['one'][$i]<=0)
			{
				$post['one'][$i]=1;
			}
			if($post['next'][$i]<=0)
			{
				$post['next'][$i]=1;
			}
		
		
			$ship=array(
				  'areaid'	=>$t,
				  'areaname'	=>$tt,
				  'one'	=>abs((int)$post['one'][$i]),
				  'price'	=>abs((float)$post['price'][$i]),
				  'next'	=>abs((int)$post['next'][$i]),
				  'nprice'=>abs((float)$post['nprice'][$i])
			  );
			  array_push($ships,$ship);
		}
	}
	$post['cod_regions']=serialize($ships);//ת�����ַ���
	$post['typeid']=(int)$post['typeid'];
	return $post;
}
if(!empty($_POST['act']))
{
	if($_POST['act']=='add')
	{
		$post=setdata($_POST);
		$arr=array(
			  'store_id'=>$user_id,
			  'shipping_name'=>$post['shipping_name'],
			  'cod_regions'=>$post['cod_regions'],
			  'enabled'=>0,
			  'typeid'=>$post['typeid'],
			  'riqi'=>date('Y-m-d H:i:s')
		);	
		$db->insert('{shippings}',$arr);
	}
	elseif($_POST['act']=='edit')
	{
		$post=setdata($_POST);
		$arr=array(
			  'store_id'=>$user_id,
			  'shipping_name'=>$post['shipping_name'],
			  'cod_regions'=>$post['cod_regions'],
			  'enabled'=>0,
			  'typeid'=>$post['typeid'],
			  'riqi'=>date('Y-m-d H:i:s')
		);
		$db->update('{shippings}',$arr,"shipping_id=$shipping_id limit 1");
		
	}
	?>
	<script language="javascript">
        //window.parent.c_close();
        window.parent.location.reload();
    </script>
    <?
	exit();
}



$area['����']=$db->get_all("select region_id,region_name from {region} where parent_id=2 and region_id in(41,180,194,206,234)");
$area['����']=$db->get_all("select region_id,region_name from {region} where parent_id=2 and region_id in(3,22,116,246,104,167)");
$area['����']=$db->get_all("select region_id,region_name from {region} where parent_id=2 and region_id in(301,283,264)");
$area['����']=$db->get_all("select region_id,region_name from {region} where parent_id=2 and region_id in(317,342,224,339)");
$area['����']=$db->get_all("select region_id,region_name from {region} where parent_id=2 and region_id in(128,143,153)");
$area['����']=$db->get_all("select region_id,region_name from {region} where parent_id=2 and region_id in(414,425,449,440,455)");
$area['����']=$db->get_all("select region_id,region_name from {region} where parent_id=2 and region_id in(61,389,379,406,357)");
$area['�۰�̨']=$db->get_all("select region_id,region_name from {region} where parent_id=2 and region_id in(474,475,476)");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>��ݱ��</title>
<link href="wuliu.css" rel="stylesheet" />
<script type="text/javascript" src="/includes/libraries/javascript/jquery.js" charset="utf-8"></script>
<script language="javascript" src="wuliu.js" charset="UTF-8"></script>
</head>

<body>
<form name="form1" method="post" onSubmit="return chkform()">
<?
$ships=array();
if($shipping_id!=0)
{
	$row=$db->get_one("select shipping_name,cod_regions,typeid from {shippings} where shipping_id=$shipping_id  limit 1");

	$ships=unserialize($row['cod_regions']);

	$typeid=$row['typeid'];
	$shipping_name=$row['shipping_name'];
	
	echo '<input type="hidden" name="act" value="edit"/>';	
	echo '<input type="hidden" name="shipping_id" value="'.$shipping_id.'"/>';
}
else
{
	$ships[0]=array(
		  'one'	=>1,
		  'price'	=>10,
		  'next'	=>1,
		  'nprice'=>5
	);	
	echo '<input type="hidden" name="act" value="add"/>';	
}

if(empty($ships[1]))
{
	/*$ships[1]=array(
		  'areaid'	=>'',
		  'areaname'	=>'δ��ӵ���',
		  'one'	=>1,
		  'price'	=>10,
		  'next'	=>1,
		  'nprice'=>5
	);*/	
}
?>
<p class="pxcss">
���ͷ�ʽ��<input type="text" name="shipping_name" class="wuliucss" value="<?=$shipping_name?>"><br />
</p>
<p class="pxcss">
�Ƽ۷�ʽ��
<label><input type="radio" name="typeid" checked="checked" value="1" />������</label>
<label><input type="radio" name="typeid" value="2" <? if($typeid==2){echo 'checked';}?>/>������</label>
<label><input type="radio" name="typeid" value="3" <? if($typeid==3){echo 'checked';}?>/>�����</label><br />
 </p>
 <p class="pxcss"> 
���ͷ�ʽ����ָ�������⣬����������˷Ѳ���"Ĭ���˷�"<br /></p>
<div class="tablebox">
  <div class="entity">
			<div class="default">
				 Ĭ���˷ѣ�
				<input class="inputtext " type="text" maxlength="6" value="<?=$ships[0]['one']?>" name="one[]" onKeyUp="value=value.replace(/[^0-9]/g,'')"><span name='unit'>��</span>�ڣ�
				<input class="inputtext " type="text" maxlength="6" value="<?=$ships[0]['price']?>"  name="price[]" onKeyUp="value=value.replace(/[^0-9.]/g,'')">Ԫ��ÿ����
				<input class="inputtext " type="text" maxlength="6"  value="<?=$ships[0]['next']?>"  name="next[]" onKeyUp="value=value.replace(/[^0-9]/g,'')"><span name='unit'>��</span>�������˷�
				<input class="inputtext " type="text" maxlength="6"  value="<?=$ships[0]['nprice']?>" name="nprice[]" onKeyUp="value=value.replace(/[^0-9.]/g,'')">Ԫ
			</div>
			
			<div class="yfbox">
				<table width="720" id="yltable" style="display:<? if(count($ships)==1){echo 'none';}?>">
                			
						<tr style="background:#f5f5f5">
							<td width="300">���͵�</td><td>��<span name='unitname'>��</span>(<span name='unit'>��</span>)</td><td>�׷�(Ԫ)</td><td>��<span name='unitname'>��</span>(<span name='unit'>��</span>)</td><td>����(Ԫ)</td><td>����</td>
						</tr>
                        <?
						array_shift($ships);
                        foreach($ships as $i=>$ship)
						{
							$j=$i+1;
 						?>			
						<tr id="tr<?=$j?>">
							<td width="300">					
                           <a href="javascript:showArea(<?=$j?>)">�༭</a>
                            <p><?=$ship['areaname']?></p>	
                            <input type="hidden" name="v_txt_tr<?=$j?>" id="v_txt_tr<?=$j?>" value="<?=$ship['areaname']?>"/>
                            <input type="hidden" name="v_val_tr<?=$j?>" id="v_val_tr<?=$j?>" value="<?=$ship['areaid']?>"/>
                            </td>
							<td><input class="textcss " type="text" maxlength="6" value="<?=$ship['one']?>" onKeyUp="value=value.replace(/[^0-9]/g,'')"  name="one[]"></td>
							<td><input class="textcss " type="text" maxlength="6" value="<?=$ship['price']?>" onKeyUp="value=value.replace(/[^0-9.]/g,'')"  name="price[]"></td>
							<td><input class="textcss " type="text" maxlength="6" value="<?=$ship['next']?>" onKeyUp="value=value.replace(/[^0-9]/g,'')" name="next[]"></td>
							<td><input class="textcss " type="text" maxlength="6" value="<?=$ship['nprice']?>" onKeyUp="value=value.replace(/[^0-9.]/g,'')" name="nprice[]"></td>
							<td><input type="button" value="ɾ��" onClick="deleteRow(this)"></td>
						</tr>					
						<?
						}
						?>			
				</table>
			</div>
			<div class="zdbox"><a href="javascript:addRow()">Ϊָ���������������˷�</a></div>
		</div>
        <div class="clear"></div>        
	</div>
    <div class="bbtncss"><input type="submit" value="����"/>&nbsp; <input type="button" value="ȡ��" onClick="window.parent.c_close();"/></div>
	</form>
    
    
    

	<div class="aqbox" id="divArea">
		<div class="aqalbox">
			<div class="topdq"><div class="title">ѡ�����</div><a href="javascript:hideArea()">x</a></div>
		
				<ul  class="plabox">
						<input type="hidden" value="1" id="tr_num" />
						<?
						$j=0;
                        foreach($area as $i=>$result)
						{
							$j++;
							?>
                        <li class="choosbox <? if($j%2==0){echo 'bgcss';}?>">
							<div class="ffbox"><label><input type="checkbox" title="<?=$i?>" value="<?=$i?>" name='area' onClick="chxclick(this)"/><b><?=$i?></b></label></div>
							<div class="elsbox"> 
                            	<?
                                foreach($result as $provs)
								{
									?>
                        <div class="fwbox ">                          
                                <div  class="greas"><label><input type="checkbox" title="<?=$provs['region_name']?>" name='province' onClick="chxclick(this)" value="<?=$provs['region_id']?>"/><?=$provs['region_name']?></label><span></span><img src="jt.jpg" onClick="subarea(this)"/></div>
                       
                            <div class="citys">
                            	<?
                                $citys=$db->get_all("select region_id,region_name from {region} where parent_id={$provs['region_id']}");
								foreach($citys as $city)
								{
									?>
                                    <label><input type="checkbox" title="<?=$city['region_name']?>" value="<?=$city['region_id']?>"  onclick="chxclick(this)"/><?=$city['region_name']?></label>
                                    <?
								}
								?>                               
                                <p align="right"><input type="button" value="�ر�" onClick="subhide(this)"/></p>
                            </div>
                        </div>
                                    <?
								}
								?> 
							</div>							
						</li>
                            <?
						}
						?>
				</ul>
				<p><input type="button" value="ȷ��" onClick="saveArea()"/> <input type="button" value="ȡ��" onClick="hideArea()"/></p>
	
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>
