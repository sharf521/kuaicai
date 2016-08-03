<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Uploader Properties</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="noindex, nofollow" name="robots">
<?php
if ($_SERVER['REQUEST_METHOD']=='POST')
{
	if($_FILES['filename']['size']>1048576*2)//2M
	{
		echo "<script>alert('大小限制：2M！');</script>";$tab=2;
	}
	else
	{
		$destination_folder="uploadfiles/files/";      //上传文件路径
		$path_parts=pathinfo($_SERVER['PHP_SELF']);     //取得当前路径
		$num = substr_count($path_parts["dirname"],'/');
		$destination_folder_temp = $destination_folder;
		while($num>0)
		{
		   $destination_folder_temp = '../'.$destination_folder_temp;
		   $num--;
		}
		if(!file_exists($destination_folder_temp))	   mkdir($destination_folder_temp);
		$pinfo=pathinfo($_FILES['filename']['name']);
		$ftype=$pinfo["extension"];
		$destination = $destination_folder_temp.time().".".$ftype;
		if(!move_uploaded_file ($_FILES['filename']['tmp_name'], $destination))
		{
		   echo "<script>alert('移动文件出错！');</script>"; $tab=2;
		}
		else
		{
			$pinfo=pathinfo($destination);
			$fname=$pinfo["basename"];
			$link="/".str_replace('./','',$destination_folder).$fname;
		}
	}
}
?>
<script language="javascript">
var isIE = (document.all && window.ActiveXObject && !window.opera) ? true : false;
var dialog	= window.parent ;
var oEditor = dialog.InnerDialogLoaded() ;
var FCK		= oEditor.FCK ;
var FCKLang	= oEditor.FCKLang ;

dialog.AddTab( 'file', '附件' ) ;
dialog.AddTab( 'upload','上传' ) ;
function OnDialogTabChange( tabCode )
{
	document.getElementById('divfile').style.display	=(tabCode == 'file')?'':'none';
	document.getElementById('divupload').style.display	=(tabCode == 'upload')?'':'none';
}
var oLink = dialog.Selection.GetSelection().MoveToAncestorNode( 'A' ) ;	
window.onload = function ()
{	
    oEditor.FCKLanguageManager.TranslatePage( document ) ;
    window.parent.SetOkButton( true ) ;
	if(oLink)
	{
		<? if(empty($link)){?>
		document.getElementById('link').value= oLink.getAttribute('href') ;
		<? }?>
		if(isIE)
			document.getElementById('name').value=oLink.innerText;		
		else
			document.getElementById('name').value=oLink.textContent;
	}
	<?
	if($tab==2)
		echo 'window.parent.SetSelectedTab("upload");';//第二个tab
	else
		echo 'window.parent.SetSelectedTab("file");';
	?>
}
function Ok()
{
	var name=document.getElementById('name').value;
	var link=document.getElementById('link').value;
	if(name=='')
	{
		alert('名称不能为空！');return false;	
	}
	if(link=='')
	{
		alert('附件位置不能为空！');return false;	
	}
	if (oLink) FCK.Selection.SelectNode( oLink ) ;
	href=window.location.pathname;//  /include/
	src=href.substring(0,href.lastIndexOf('/'))+'/'+'uploader.gif';	
	var img="<img src='"+src+"' border='0'/>";
	oEditor.FCK.InsertHtml("<a href='"+link+"'>"+img+name+"</a>");
    return true ;
}
function checkform()
{
	var v = document.getElementById('filename').value;
	if(v == '') {alert('请选择文件!'); return false;}
	var t = v.substring(v.lastIndexOf('.')+1, v.length);
	t = t.toLowerCase();
	var a ='jpg|jpeg|gif|bmp|png|swf|mp3|wma|zip|rar|doc';
	if(a.indexOf(t) == -1) {alert('限制为:'+a); return false;}
	return true;	
}
</script>
</head>
<body scroll="no" style="OVERFLOW: hidden;">
<form enctype="multipart/form-data" method="post" name="upform" onSubmit="return checkform()">
<div id='divfile'><br>
	<span fckLang="DLgUploderName"></span>：<br>
    <input type="text" value="<?=$_POST['name']?>" id="name" style="width:330px"><br><br>
    <span fckLang="DLgUploderLink"></span>：<br>
    <input type="text" id="link" value="<?=$link?>" style="width:330px"><br>
</div>
<div id="divupload" style=" display:none">	<br>
    <span fcklang="DlgLnkUpload">Upload</span><br />
    <input name="filename" id="filename" type="file" style="width:330px"><br /><br>
    <input type="submit" value="Send it to the Server" fcklang="DlgLnkBtnUpload"><br /><br />
    <span>允许上传的文件类型为:<br />jpg | jpeg | gif | bmp | png | swf | mp3 | wma | zip | rar | doc</span>
</div>
</form>
</body>
</html>

