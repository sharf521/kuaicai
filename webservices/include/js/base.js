function getObj(xid){ return document.getElementById(xid);  } 

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
	if(arr != null) return unescape(arr[2]); return null;

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
function checklogin()
{
	if(document.getElementById('userid').value=='')
	{
		alert('请输入用户名！');
		document.getElementById('userid').focus();
		return false;
	}
	if(document.getElementById('password').value=='')
	{
		alert('请输入密码！');
		document.getElementById('password').focus();
		return false;
	}
	$.ajax({
			url:'ajax.php',
			data:{func:'checklogin',userid:$('#userid').val(),password:$('#password').val()},
			type:'POST',
			cache:false,
			success:function(html)
			{
				if(html==10)
				{	
					window.location='admin/?act=manage';				
				}
				else if(html==1)
				{
					alert('帐号没有审核,请联系客服');	
				}
				else
				{
					alert('用户名或密码错误！');	
				}
			}
	})
	return false;
}
function logout()
{
	$.ajax({
			url:'ajax.php',
			data:{func:'logout'},
			type:'POST',
			cache:false,
			success:function(tt)
			{
				$('#top_login').css('display','');
				$('#top_logout').css('display','none');
			}
	})	
}
function dosearch()
{
	if(document.getElementById('keyword').value=='')
	{
		alert('请输入关键字！');
		document.getElementById('keyword').focus();
		return false;
	}
}
function changeSub(num)
{
	for(var i=1;i<8;i++)
	{
		if(num==i)
			document.getElementById('sub_'+num).className='sub';
		else	
			document.getElementById('sub_'+i).className='sub_none';
	}
}
function changeNews(num)
{
	if(num==1)
	{		
		getObj('gg_1').style.background='url(/templates/default/images/tp_5.png) repeat-x 0px 0px';
		getObj('gg_2').style.background='';
		getObj('ul_news1').style.display='';
		getObj('ul_news2').style.display='none';
	}
	else
	{
		getObj('gg_1').style.background='';
		getObj('gg_2').style.background='url(/templates/default/images/tp_5.png) repeat-x 0px 0px';
		getObj('ul_news2').style.display='';
		getObj('ul_news1').style.display='none';
	}
}

function setab(name,cursel,n,link){
	  for(i=1;i<=n;i++){
	  var menu=document.getElementById(name+i);
	  var con=document.getElementById("con"+name+i);
	  menu.className=i==cursel?"hover":"";
	  con.style.display=i==cursel?"block":"none";
	}
		if(link!=null && link!="")document.getElementById("TabLink"+name).href=link;
  }
