!window.jQuery && document.write("<script src=\"\/includes/libraries/javascript/jquery.js\">"+"</scr"+"ipt>");
//QQ客服弹出对话框

/*Kefu = function(id, _top, _left) {
    var me = id.charAt ? document.getElementById(id) : id,
    d1 = document.body,
    d2 = document.documentElement;
    d1.style.height = d2.style.height = '100%';
    me.style.top = _top ? _top + 'px': 0;
    me.style.right = _left + "px";
    me.style.position = 'absolute';
    setInterval(function() {
        me.style.top = parseInt(me.style.top) + (Math.max(d1.scrollTop, d2.scrollTop) + _top - parseInt(me.style.top)) * 0.1 + 'px'
    },
    10 + parseInt(Math.random() * 20));
    return arguments.callee
};
window.onload = function() {
    Kefu('floatTools', 200, 1)
};*/

function close1()
{
	$('#divFloatToolsView').animate({width: 'hide', opacity: 'hide'}, 'normal',function()
	{ 
		$('#divFloatToolsView').hide();
	});
	$('#aFloatTools_Show').attr('style','display:block');
	$('#aFloatTools_Hide').attr('style','display:none');
}

function open1()
{
	javascript:$('#divFloatToolsView').animate({width: 'show', opacity: 'show'}, 'normal',function(){ $('#divFloatToolsView').show();
	 });
	$('#aFloatTools_Show').attr('style','display:none');
	$('#aFloatTools_Hide').attr('style','display:block');	
}