$.ajaxSetup({async: false});
function saveAllHTML()
{
	$("input[name='checkid[]']").each(function(){
		if($(this).attr('checked')==true)
		{
			saveHTML($(this).val());
		}	   
	})
}
function getsel(idnum,id)
{
	str=cate_arr[id];
	if(str!='' && str!=undefined)
	{							
		var sel=appendSelect(idnum);
		var arr =str.split("[SER]");
		sel.options.length=0;
		for(v in arr)
		{
			var v=arr[v].split("#");
			sel.options.add(new Option(v[1],v[0]));				
		}		
	}
	else
	{
		removeSelect(idnum);				
	}
	document.getElementById('category'+idnum).value=id;
}
//获取下一个select  id命名规格category+编号
function appendSelect(idnum)
{
	var thisnum=idnum+1;
	if(document.getElementById('category'+thisnum))
	{
		sel = document.getElementById('category'+thisnum);
		removeSelect(thisnum);//移除后面的分类
	}
	else 
	{
		sel = document.createElement("select");
		sel.id = 'category'+thisnum;
		sel.name='categoryid[]';
		sel.multiple='multiple';
		sel.className='multiple';
		sel.onchange=function(){getsel(thisnum,this.value);}
		document.getElementById('div_category').appendChild(sel);
	}
	return sel;
}
//移除其它的select
function removeSelect(idnum)
{
	var thisnum=idnum+1;
	while(document.getElementById('category'+thisnum))
	{
		document.getElementById('div_category').removeChild(document.getElementById('category'+thisnum));
		thisnum=thisnum+1;
	}
} 
//全选
$("#checkAll").click(function(){ 
	$("input[type='checkbox']").attr("checked",$(this).attr("checked")); 
}); 
function setChecked()
{
	var form1=document.getElementById('form1');
	form1.func.value='checked';
	form1.submit();
}	
function setNoChecked()
{
	var form1=document.getElementById('form1');
	form1.func.value='nochecked';
	form1.submit();
}