!window.jQuery && document.write("<script src=\"\/includes/libraries/javascript/jquery.js\">"+"</scr"+"ipt>");

function setCookie(name,value)//����������һ����cookie�����ӣ�һ����ֵ
{
	var exp  = new Date();    //new Date("December 31, 9998");
	exp.setTime(exp.getTime() + 60*60*1000);//1Сʱ
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getCookie(name)//ȡcookies����
{
	var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
	if(arr != null) return unescape(arr[2]); return null;

}

$(document).ready(function(){
  $("#win_pop .close").click(function(){
		$('#win_pop').hide('slow');
  });
  window.setTimeout("showPop()",15000);
});
String.prototype.Trim = function(){return this.replace(/(^\s*)|(\s*$)/g, "");}
function showPop()
{
	var first=getCookie('winpop3')==1?0:1;//�ǲ��ǵ�һ�ε���
	$.post("/psadmin/?app=order&act=message",{location:document.location.href,first:first},function(result){
		if(result.Trim()!='')
		{
			result=result.split('[#]');
			if(result[0]=='OK' && result[1]!='')
			{
				document.getElementById('pop_txt').innerHTML=result[1];
				$('#win_pop').show('slow');
			}
		}
	});
	if(getCookie('winpop3')==null)
	{
		setCookie('winpop3',1);
	}	
	window.setTimeout("showPop()",120000);
}





