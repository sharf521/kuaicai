var isIE = (document.all && window.ActiveXObject && !window.opera) ? true : false;
var isChrome = navigator.userAgent.indexOf('Chrome') != -1;
var ROOT = document.location.protocol+'//'+location.hostname+(location.port ? ':'+location.port : '')+'/';
if(isIE) try {document.execCommand("BackgroundImageCache", false, true);} catch(e) {}
function getObj(i)
{
	if(typeof i=='string')
		return document.getElementById(i);
	else
	 	return i; 
}
function show(i) {getObj(i).style.display = '';}
function hide(i) {getObj(i).style.display = 'none';}
function ext(v) {return v.substring(v.lastIndexOf('.')+1, v.length);}
function Drag(content,title)
{	
	if(typeof content=='string'){content=document.getElementById(content);}
	if(typeof title=='string'){title=document.getElementById(title);}
	var tHeight,lWidth;
	title.onmousedown =start; 
	function start(e)
	{
		var event = window.event || e;
		tHeight = event.clientY  - parseInt(content.style.top);
		lWidth  = event.clientX - parseInt(content.style.left);
		document.onmousemove = move;
		document.onmouseup   = end;
		return false;
	}
	function move(e)
	{
		var event = window.event || e;
		var top = event.clientY - tHeight;
		var left = event.clientX - lWidth;
		content.style.top  = top + "px";
		content.style.left = left +"px";
		lastMouseX=event.clientX;
		lastMouseY=event.clientY;
		return false;
	}
	function end()
	{
		document.onmousemove = null;
		document.onmouseup=null;
	}
}	
function changeImg(obj,width,height) 
{
	if ( obj.width > width || obj.height > height )
	{
	var scale;
	var scale1 = obj.width / width;
	var scale2 = obj.height / height;
	if(scale1 > scale2)
		scale = scale1;
	else
		scale = scale2;
	obj.width = obj.width / scale;
	}
}
function alpha_img(o)
{
	o.style.filter="alpha(opacity=50)";	o.style.opacity="0.5";
	setTimeout(function(){o.style.filter="alpha(opacity=100)";	o.style.opacity="1.0";},80);
}
function showSWF(src,width,height)
{
	document.write("<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"../download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width="+width+" height="+height+"><param name=movie value="+src+"><param name=quality value=high ><param name=\"wmode\" value=\"transparent\"><embed src="+src+" quality=high  wmode=\"transparent\" type=\"application/x-shockwave-flash\" width="+width+" height="+height+"></embed></object>");	
}
function setCookie(name,value)//两个参数，一个是cookie的名子，一个是值
{
	var Days = 1; //此 cookie 将被保存 30 天
	var exp  = new Date();    //new Date("December 31, 9998");
	exp.setTime(exp.getTime() + Days*24*60*60*1000);
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getCookie(name)//取cookies函数
{
	var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
	if(arr != null) return unescape(arr[2]); return "";
}
// 屏蔽FusionCharts的右键菜单
function noRightClick(pid)
{
 //pid:flash's parentNode id  说明：flash父容器的id
 var el = document.getElementById(pid);
 if(el.addEventListener)
 {
  el.addEventListener("mousedown",function(event){
  if(event.button == 2){
   event.stopPropagation(); //for firefox
   event.preventDefault();  //for chrome
   }
  },true);
 }
 else
 {
  el.attachEvent("onmousedown",function(){if(event.button == 2){el.setCapture();}});
  el.attachEvent("onmouseup",function(){ el.releaseCapture();});
  el.oncontextmenu = function(){ return false;};
 }
}
function addBookmark(title,url)
{
	if (window.sidebar) { window.sidebar.addPanel(title, url,""); }
	else if( document.all ) {window.external.AddFavorite( url, title);}
	else if( window.opera && window.print ) {return true;}
}
function setHomepage(url)
{
	if (document.all){document.body.style.behavior='url(#default#homepage)'; document.body.setHomePage(url);}
	else if (window.sidebar)
	{
		if(window.netscape)
		{
			try
			{  
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");  
			}
			catch (e)  
			{  
				alert( "该操作被浏览器拒绝，如果想启用该功能，请在地址栏内输入 about:config,然后将项 signed.applets.codebase_principal_support 值该为true" );  
			}
		} 
		var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components. interfaces.nsIPrefBranch);
		prefs.setCharPref('browser.startup.homepage',url);
	}
}
function Eh(t) {
	var t = t ? t : 'select';
	if(isIE) {
		var arVersion = navigator.appVersion.split("MSIE"); var IEversion = parseFloat(arVersion[1]);		
		if(IEversion >= 7 || IEversion < 5) return;
		var ss = document.body.getElementsByTagName(t);					
		for(var i=0;i<ss.length;i++) {ss[i].style.visibility = 'hidden';}
	}
}
function Es(t) {
	var t = t ? t : 'select';
	if(isIE) {
		var arVersion = navigator.appVersion.split("MSIE"); var IEversion = parseFloat(arVersion[1]);		
		if(IEversion >= 7 || IEversion < 5) return;
		var ss = document.body.getElementsByTagName(t);					
		for(var i=0;i<ss.length;i++) {ss[i].style.visibility = 'visible';}
	}
}
//渐显
function fadeIn(o, v)
{
	if(typeof o=='string'){o=document.getElementById(o);}	
	v = v ? v :0;
	v += 5;
	if (v > 100) return;
	if(isIE)
		o.style.filter = 'Alpha(Opacity='+v+')';
	else
		o.style.opacity = v/100;
	setTimeout(function (){fadeIn(o, v);}, 30);
}


function dragstart(i, e) { dgDiv = getObj(i);if (!e) {e = window.event;} dgX = e.clientX - parseInt(dgDiv.style.left); dgY = e.clientY - parseInt(dgDiv.style.top);document.onmousemove = dragmove;}
function dragmove(e) { if (!e) {e = window.event;}dgDiv.style.left = (e.clientX - dgX) + 'px'; dgDiv.style.top = (e.clientY - dgY) + 'px';}
function dragstop() {    dgX = dgY = 0; document.onmousemove = null;}

function sDialog(t,c,w,left,top)
{
	var t=t?t:'上传图片：';
	var w=w?w:300;
	var body = document.documentElement || document.body;
	if(isChrome) body = document.body;
	var cw = body.clientWidth;
	var ch = body.clientHeight;
	var bsw = body.scrollWidth;
	var bsh = body.scrollHeight;
	var bw = parseInt((bsw < cw) ? cw : bsw);
	var bh = parseInt((bsh < ch) ? ch : bsh);
	Eh();
	//遮罩层
	if(getObj('sharf_Dbg'))
	{
		show('sharf_Dbg');	
	}
	else
	{
		var sharf_Dbg = document.createElement("div");
		with(sharf_Dbg.style){zIndex = 998; position = 'absolute'; width = '100%'; height = bh+'px'; overflow = 'hidden'; top = 0; left = 0; border = "0px"; backgroundColor = '#eeeeee';if(isIE){filter = " Alpha(Opacity=50)";}else{opacity = 50/100;}}
		sharf_Dbg.id = "sharf_Dbg";
		document.body.appendChild(sharf_Dbg);
	}	
	var sl = left ? left : body.scrollLeft + parseInt((cw-w)/2);
	var st = top ? top : body.scrollTop + parseInt(ch/2) - 100;
	var sharf_Dtop = document.createElement("div");
	with(sharf_Dtop.style){zIndex = 999; backgroundColor = '#efefef'; position = 'absolute'; width = w+'px'; left = sl+'px'; top = st+'px';}
	sharf_Dtop.id = 'sharf_Dtop';
	sharf_Dtop.innerHTML = '<div class="dbody"><div id="sharf_dhead" class="dhead" ondblclick="cDialog();"  onmousedown="dragstart(\'sharf_Dtop\', event);"  onmouseup="dragstop(event);" onselectstart="return false;"><span onclick="cDialog();">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;'+t+'</div><div class="dbox" id="sharf_dbox">'+c+'</div></div>';	
	document.body.appendChild(sharf_Dtop);	
}
function cDialog()
{	 
	hide('sharf_Dbg');
	Es();
	document.body.removeChild(getObj('sharf_Dtop'));
}
function isImg(i, e) 
{
	var v = getObj(i).value;
	if(v == '') {confirm('请选择文件!'); return false;}
	var t = ext(v);
	t = t.toLowerCase();
	var a = typeof e == 'undefined' ? 'jpg|gif|png|jpeg|bmp|tif' : e;
	//var a=e?e:'jpg|gif|png|jpeg|bmp';
	if(a.length > 2 && a.indexOf(t) == -1) {confirm('限制为:'+a); return false;}
	return true;
}

function sUploadFile(title,thumb,imgw,imgh,text,module)
{	
	var c='<iframe name="UploadFile" style="display: none;" src=""></iframe>';
	c+='<form method="post" target="UploadFile" enctype="multipart/form-data" action="/include/upload.php" onsubmit="return isImg(\'filename\');">';
	c+='<input name="thumb" value="'+thumb+'" type="hidden">';
	c+='<input name="width" value="'+imgw+'" type="hidden">';
	c+='<input name="height" value="'+imgh+'" type="hidden">';
	c+='<input name="text" value="'+text+'" type="hidden">';
	c+='<input name="module" value="'+module+'" type="hidden">';
	c+='<table cellpadding="10"><tr><td><input id="filename" size="20" name="filename" type="file"></td></tr>';
	c+='<tr><td>&nbsp;&nbsp;<input value=" 上 传 " type="submit">&nbsp;&nbsp;<input value=" 取 消 " onclick="cDialog();" type="button"></td></tr>';
	c+='</table>';
	c+='</form>	';
	sDialog(title,c,250,'','250');
}
function sUploadImgSwf(title,text,module)
{
	var c='<iframe name="UploadFile" style="display: none;" src=""></iframe>';
	c+='<form method="post" target="UploadFile" enctype="multipart/form-data" action="include/upload.php" onsubmit="return isImg(\'filename\',"jpg|gif|png|jpeg|bmp|swf");">';
	c+='<input name="thumb" value="0" type="hidden">';
	c+='<input name="width" value="0" type="hidden">';
	c+='<input name="height" value="0" type="hidden">';
	c+='<input name="text" value="'+text+'" type="hidden">';
	c+='<input name="module" value="'+module+'" type="hidden">';
	c+='<table cellpadding="10"><tr><td><input id="filename" size="20" name="filename" type="file"></td></tr>';
	c+='<tr><td>&nbsp;&nbsp;<input value=" 上 传 " type="submit">&nbsp;&nbsp;<input value=" 取 消 " onclick="cDialog();" type="button"></td></tr>';
	c+='</table>';
	c+='</form>	';
	sDialog(title,c,250,'','');
}
function OpenNewWin(url) 
{ 
	var frm=document.createElement("form"); 
	frm.method="POST";
	frm.target="_blank"; 
	frm.action=url; 
	document.body.appendChild(frm); 
	frm.submit(); 
}