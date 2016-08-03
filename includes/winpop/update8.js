!window.jQuery && document.write("<script src=\"\/includes/libraries/javascript/jquery.js\">"+"</scr"+"ipt>");
document.writeln("<div class=\"float_layer\" id=\"miaov_float_layer\">");
document.writeln("    <h2>");
document.writeln("        <strong>温馨提示：</strong>");
document.writeln("        <a id=\"btn_min\" href=\"javascript:;\" class=\"min\"></a>");
document.writeln("        <a id=\"btn_close\" href=\"javascript:;\" class=\"close\"></a>");
document.writeln("    </h2>");
document.writeln("    <div class=\"content\">");
document.writeln("		<div class=\"wrap\" id=\"pop_txt\"></div>");
document.writeln("   	</div>");
document.writeln("</div>");
function setCookie(name,value)//两个参数，一个是cookie的名子，一个是值
{
	var exp  = new Date();    //new Date("December 31, 9998");
	exp.setTime(exp.getTime() + 60*60*1000);//1小时
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getCookie(name)//取cookies函数
{
	var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
	if(arr != null) return unescape(arr[2]); return null;

}
function miaovAddEvent(oEle, sEventName, fnHandler)
{
	if(oEle.attachEvent)
	{
		oEle.attachEvent('on'+sEventName, fnHandler);
	}
	else
	{
		oEle.addEventListener(sEventName, fnHandler, false);
	}
}
//setCookie('winpop',111);


String.prototype.Trim = function(){return this.replace(/(^\s*)|(\s*$)/g, "");}

window.onload = function()
{	
	window.setTimeout("showPop()",5000);
}
function showPop()
{
	var first=getCookie('winpop')==1?0:1;//是不是第一次调用
	$.post("/?app=sendmail&act=message",{location:document.location.href,first:first},function(result){
		if(result.Trim()!='')
		{
			document.getElementById('pop_txt').innerHTML=result;
			winpop();
		}
	});
	if(getCookie('winpop')==null)
	{
		setCookie('winpop',1);
	}	
	window.setTimeout("showPop()",60000);
}
function winpop(content)
{
	var oDiv=document.getElementById('miaov_float_layer');
	var oBtnMin=document.getElementById('btn_min');
	var oBtnClose=document.getElementById('btn_close');
	var oDivContent=oDiv.getElementsByTagName('div')[0];
	
	var iMaxHeight=0;
	//document.getElementById('pop_txt').innerHTML=content;
	var isIE6=window.navigator.userAgent.match(/MSIE 6/ig) && !window.navigator.userAgent.match(/MSIE 7|8/ig);
	
	oDiv.style.display='block';
	iMaxHeight=oDivContent.offsetHeight;
	
	if(isIE6)
	{
		oDiv.style.position='absolute';
		repositionAbsolute();
		miaovAddEvent(window, 'scroll', repositionAbsolute);
		miaovAddEvent(window, 'resize', repositionAbsolute);
	}
	else
	{
		oDiv.style.position='fixed';
		repositionFixed();
		miaovAddEvent(window, 'resize', repositionFixed);
	}
	
	oBtnMin.timer=null;
	oBtnMin.isMax=true;
	oBtnMin.onclick=function ()
	{
		startMove
		(
			oDivContent, (this.isMax=!this.isMax)?iMaxHeight:0,
			function ()
			{
				oBtnMin.className=oBtnMin.className=='min'?'max':'min';
			}
		);
	};
	
	oBtnClose.onclick=function ()
	{
		oDiv.style.display='none';
	};
};

function startMove(obj, iTarget, fnCallBackEnd)
{
	if(obj.timer)
	{
		clearInterval(obj.timer);
	}
	obj.timer=setInterval
	(
		function ()
		{
			doMove(obj, iTarget, fnCallBackEnd);
		},30
	);
}

function doMove(obj, iTarget, fnCallBackEnd)
{
	var iSpeed=(iTarget-obj.offsetHeight)/8;
	
	if(obj.offsetHeight==iTarget)
	{
		clearInterval(obj.timer);
		obj.timer=null;
		if(fnCallBackEnd)
		{
			fnCallBackEnd();
		}
	}
	else
	{
		iSpeed=iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);
		obj.style.height=obj.offsetHeight+iSpeed+'px';
		
		((window.navigator.userAgent.match(/MSIE 6/ig) && window.navigator.userAgent.match(/MSIE 6/ig).length==2)?repositionAbsolute:repositionFixed)()
	}
}

function repositionAbsolute()
{
	var oDiv=document.getElementById('miaov_float_layer');
	var left=document.body.scrollLeft||document.documentElement.scrollLeft;
	var top=document.body.scrollTop||document.documentElement.scrollTop;
	var width=document.documentElement.clientWidth;
	var height=document.documentElement.clientHeight;
	
	oDiv.style.left=left+width-oDiv.offsetWidth+'px';
	oDiv.style.top=top+height-oDiv.offsetHeight+'px';
}

function repositionFixed()
{
	var oDiv=document.getElementById('miaov_float_layer');
	var width=document.documentElement.clientWidth;
	var height=document.documentElement.clientHeight;
	
	oDiv.style.left=width-oDiv.offsetWidth+'px';
	oDiv.style.top=height-oDiv.offsetHeight+'px';
}

//一流素材网收藏整理：www.16sucai.com 代码来源妙味课堂www.miaov.com