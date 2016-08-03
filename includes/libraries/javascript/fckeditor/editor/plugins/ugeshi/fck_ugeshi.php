<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>UGeSHi Properties</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="noindex,nofollow" name="robots">
<link href="../common/fck_dialog_common.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var oEditor=window.parent.InnerDialogLoaded();
var FCK=oEditor.FCK;
var FCKLang=oEditor.FCKLang;
var FCKConfig=oEditor.FCKConfig;

window.onload = function(){
	oEditor.FCKLanguageManager.TranslatePage(document);
	window.parent.SetOkButton(true);
}

function Ok(){
	var UGC=document.getElementById('UGCode').value;
	var UGL=document.getElementById('UGLang').value;
	UGC = UGC.replace(/\+/g, "%2B");
	UGC = UGC.replace(/\&/g, "%26");
	postDataReturnText(FCKConfig.PluginsPath+'ugeshi/index.php?'+Math.random(),"UGL="+UGL+"&UGC="+UGC,highlight);
}

function postDataReturnText(url,data,callback){
	var XMLHttpRequestObject = false;
	if(window.XMLHttpRequest){
		XMLHttpRequestObject=new XMLHttpRequest();
	}else if(window.ActiveXObject){
		XMLHttpRequestObject=new ActiveXObject("Microsoft.XMLHTTP");
	}
	if(XMLHttpRequestObject){
		XMLHttpRequestObject.open("POST",url);
		XMLHttpRequestObject.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		XMLHttpRequestObject.onreadystatechange=function(){
			if(XMLHttpRequestObject.readyState==4&&XMLHttpRequestObject.status==200){
				callback(XMLHttpRequestObject.responseText);
				delete XMLHttpRequestObject;
				XMLHttpRequestObject = null;
			}
		}
		XMLHttpRequestObject.send(data);
	}
}

function highlight(data){
	UGT=document.getElementById('UGTitle').value;
	var UGHtml="<div class='UGCode'>\n";
	UGHtml+="<div class='UGTitle'>"+UGT+"</div>\n";
	UGHtml+=data;
	UGHtml+="</div>\n";
	FCK.InsertHtml(UGHtml);
	window.parent.CloseDialog();
}
</script>
</head>
<body scroll="no" style="overflow:hidden;">
<table height="100%" cellSpacing="1" cellPadding="5" width="100%" border="0">
  <tr>
    <td><table align="center" border="0" cellPadding="5">
    <form name="UGForm" id="UGForm">
        <tr>
          <td style="vertical-align: top;"><span fckLang="UGeSHiTitle">Title</span></td>
          <td><input type="input" id="UGTitle" style="width:400px;" value="代码如下" /></td>
        </tr>
        <tr>
          <td style="vertical-align:top;"><span fckLang="UGeSHiCode">Code</span></td>
          <td><textarea id="UGCode" style="width:400px;height:200px;font-family:'Courier New',Monospace;border:1px solid #ccc;padding:5px;"></textarea></td>
        </tr>
        <tr>
          <td style="vertical-align: top;"><span fckLang="UGeSHiLang">Lang</span></td>
          <td><select id="UGLang">
          <?php
          	$GeSHiDir = dirname(__FILE__) . '/geshi';
          	if (is_dir($GeSHiDir)) {
          		if ($handle = @opendir($GeSHiDir)) {
          			while ($file = @readdir($handle)) {
          				if ($file[0] == '.') continue;
          				if (is_file($GeSHiDir . '/' . $file)) {
          					$filename = substr($file, 0, strpos($file, '.'));
          					$selected = $filename == "php" ? " selected=selected" : '';
          					echo "<option value='{$filename}'{$selected}>{$filename}</option>\n";
          				}
          			}
					closedir($handle);
          		}
          	}
          ?>
            </select>
          </td>
        </tr>
        </form>
      </table></td>
  </tr>
</table>
</body>
</html>