<?php
require('./include/approvedpoint.class.php');
$tclass=new approvedpoint();
$modulename='核定点管理';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';
	
if(!empty($_GET['word']))
{
	$word=checkPost(strip_tags($_GET['word']));
	$sqlW.=" and (approved like '%$word%' or title='%$word%') ";
	$url.='&word='.$word;
}

if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='del')
	{
		$tclass->delete(intval($_GET['id']));
	}
	elseif($func=='add' || $func=='edit')
	{
		if($tclass->pass($_POST))
		{
			if($func=='add')	
				$tclass->add($_POST);
			elseif($func=='edit')
				$tclass->edit($_POST);
		}
		else
		{
			showMsg($tclass->errmsg);exit();	
		}
	}	
	header("location:$url");
	exit();
}
pageTop($modulename.'管理');

$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}
$a_type=array('FBB','大小卓','六保');
if(empty($_GET['ui']))
{
?>
<div class="div_title"><?=getHeadTitle(array($modulename.'管理'=>''))?>&nbsp;&nbsp;<a href="?act=<?=$act?>&ui=add">添加</a></div>	
	<div style="margin-bottom:5px;">
	<form method="GET">		
        
        <input type="text" name="word" value="<?=$word?>">&nbsp;
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
	<?	
	$PageSize = 15;  //每页显示记录数	

	$RecordCount = $tclass->getcount($sqlW);//获取总记录
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

		$result=$tclass->getall($StartRow,$PageSize,'id desc',$sqlW);	
		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('标题','类型','核定点','设置单数','日期范围','添加时间','操作'));	
		foreach ($result as $row)
		{
			
			
		
			?>
			<tr <?=getChangeTr()?>>
				<td align='left'>&nbsp;&nbsp;<?=$row['title']?></td>
				<td align='left'>&nbsp;&nbsp;<?=$a_type[$row['type']]?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['approved']?></td>
               
                <td align="center"><?=$row['total']?></td>
                <td align='center'><?=$row['starttime']?> 至 <?=$row['endtime'];?></td>
				
			 
                <td align="center"><?=$row['createdate'];?></td>                
				<td align="center">
					
					
                    <a href='<?=$url?>&ui=edit&id=<?=$row['id']?>'>编辑</a>
                  
                    
                    <a onclick="return confirm('确定要删除吗？')" href="<?=$url?>&func=del&id=<?=$row["id"]?>">删除</a>
                    
                    </td>
			</tr>
			<?		
		}
		?>
        </form></table>
		<div class="line"><?=page($RecordCount,$PageSize,$page,$url)?></div>
		<?php
	}
	else
	{
		echo "<div><br>&nbsp;&nbsp;无数据！,点击'<a href='$url&ui=add'>添加</a>'新记录！</div>";
	}
}
else
{
	$id=intval($_GET['id']);
	echo '<form method="POST"  enctype="multipart/form-data">';
	echo '<input type="hidden" name="url" value="'.$url.'">';
	if(empty($id))
	{
		$arr=array($modulename=>$url,'添加'.$modulename=>'');
		echo '<input type="hidden" name="func" value="add">';
	}
	else
	{
		$arr=array($modulename.'管理'=>$url,'编辑'.$modulename=>'');
		echo '<input type="hidden" name="func" value="edit">';
		echo "<input type='hidden' name='id' value='$id'>";
		
		$row=$db->get_one("select * from {approvedpoint} where id=$id limit 1");
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="<?=$url?>">返回管理</a></div>
		<script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>
        
     <input type="hidden" name="sid" value="<?=$row['sid']?>" />
    <table class="infoTable">
      <tbody><tr>
        <th class="paddingT15"> 标题:</th>
        <td class="paddingT15 wordSpacing5"><input  type="text" name="title" value="<?=$row['title']?>">
          
                  </td>
      </tr>
     
      
      
      <tr>
        <th class="paddingT15"> 类型:</th>
        <td class="paddingT15 wordSpacing5"><select name="type">
                <?
                foreach($a_type as $i=>$v)
				{
					$sel='';
					if($i==$row['type']) $sel='selected';
					echo "<option value='$i' $sel>$v</option>";	
				}				
				?>
                </select>
                 </td>
      </tr>
      <tr>
        <th class="paddingT15"> 核定点:</th>
        <td class="paddingT15 wordSpacing5"><input  name="approved" type="text" value="<?=$row['approved']?>">  (用户ID)      </td>
      </tr>
      
      <tr>
        <th class="paddingT15"> 设置单数:</th>
        <td class="paddingT15 wordSpacing5"><input  name="total" type="text" id="total" value="<?=$row['total']?>">        </td>
      </tr>

      
      
      <tr>
        <th class="paddingT15"> 时间范围:</th>
        <td class="paddingT15 wordSpacing5"> 
      
        <input id="starttime"  name="starttime" style="width:140px" type="text"  class="Wdate" onfocus="javascript:WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'});" value="<?=$row['starttime']?>"> 至  <input id="endtime" name="endtime" style="width:140px" type="text"  value="<?=$row['endtime']?>" class="Wdate" onfocus="javascript:WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'});">    </td>
      </tr>
      
      <tr style="display:none">
        <th class="paddingT15" > 设定盘数:</th>
        <td class="paddingT15 wordSpacing5"><input  name="layer" type="text" value="<?=$row['layer']?>">       </td>
      </tr>
      
	<tr>
        <th class="paddingT15"> 奖励人员:</th>
        <td class="paddingT15 wordSpacing5">
        <table id='yltable'>
        	<tbody>
        	<tr><td>奖励用户</td><td>奖励金额</td></tr>
            <?
            $award=explode(';',$row['award']);
			foreach($award as $i=>$v)
			{
				$m=explode(',',$v);
			?>
            <tr><td><input type="text" name="award[]" size="15" value="<?=$m[0]?>"></td><td><input type="text" name="money[]" size="5" value="<?=$m[1]?>" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)"></td></tr>
            <?
			}
			?>
            <tr><td><input  type="text" name="award[]" size="15"></td><td><input type="text" name="money[]" size="5" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)"></td></tr>
            
            </tbody>
        </table>
        <a href="javascript:addRow()">添加一行</a>
       <script language="javascript">
function addRow() {
        var tbl = document.getElementById("yltable");
        var newTR = tbl.insertRow(tbl.rows.length);
        var newNameTD = newTR.insertCell(0);
        newNameTD.innerHTML = "<input  type='text' name='award[]' size='15'>";
        var newNameTD = newTR.insertCell(1);
        newNameTD.innerHTML = '<input type="text" name="money[]" size="5" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)">';
       
        if (tbl.rows.length > 2) {
            var newNameTD = newTR.insertCell(2);
            newNameTD.innerHTML = "<a href='javascript:deleteRow(" + tbl.rows.length + ")'>删除</a>";
            if (tbl.rows[tbl.rows.length - 2].cells[2] != null) {
                tbl.rows[tbl.rows.length - 2].deleteCell(2);
            }
        }
    }
	
	function deleteRow(ids) {
        var tbl = document.getElementById("yltable");

        tbl.deleteRow(ids - 1);
        if (tbl.rows.length > 2) {
            var newNameTD = tbl.rows[ids - 2].insertCell(2);
           newNameTD.innerHTML = "<a href='javascript:deleteRow(" + tbl.rows.length + ")'>删除</a>";
        }
    }
	   </script>
        </td>
      </tr>

        <th></th>
        <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交">
          <input class="formbtn" type="reset" name="Reset" value="重置">        </td>
      </tr>
   </table>
  核定点：<?=$row['approved_web']?><br />
  奖励人：<?=$row['award_web']?>
        
		</form>		
		<?
}
?>