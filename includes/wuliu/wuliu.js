// JavaScript Document
Array.prototype.in_array = function(e)  
{  
	for(i=0;i<this.length;i++)  
	{  
		if(this[i] == e)  
		return true;  
	}  
	return false;  
}
function chkform()
{
	if(document.forms['form1'].shipping_name.value=='')
	{
		alert('名称不能为空！');
		document.forms['form1'].shipping_name.focus();
		return false;	
	}
	return true;	
}
function changUnit()
{
	if ($("input[name='typeid']:checked").val() == "1") 
	{
		$("span[name='unit']").html('件');
		$("span[name='unitname']").html('件');
	}
	else if ($("input[name='typeid']:checked").val() == "2")
	{
		$("span[name='unit']").html('kg');
		$("span[name='unitname']").html('重');
	}
	else if ($("input[name='typeid']:checked").val() == "3")
	{
		$("span[name='unit']").html('m3');
		$("span[name='unitname']").html('体积');
	}	
}
$(document).ready(function(){
	changUnit();
   	$("input[name='typeid']").click(function (){changUnit();});
});
function addRow() 
{
	var tbl = document.getElementById("yltable");
	if(document.getElementById("yltable").style.display=='none')
	{
		tbl.style.display='';
	}
	//else
	{
		var num=parseInt($('#yltable tr:last').attr('id').substring(2))+1;//可能为空
		if(isNaN(num)){num=1;};	
		var newTR = tbl.insertRow(tbl.rows.length);
		//var num=tbl.rows.length-1;//删除中间的行，就会出现重复的num
		
		newTR.id="tr"+num;		
		cell1='<a href="javascript:showArea('+num+')">编辑</a>';
		cell1+='<p>未添加地区</p>';
		cell1+='<input type="hidden" name="v_txt_tr'+num+'" id="v_txt_tr'+num+'"/>';
		cell1+='<input type="hidden" name="v_val_tr'+num+'" id="v_val_tr'+num+'"/>';
		
		newTR.insertCell(0).innerHTML = cell1;
		//newTR.insertCell(1).innerHTML = tbl.rows[1].cells[1].innerHTML;
		newTR.insertCell(1).innerHTML = '<input class="textcss " type="text" maxlength="6" value="1" onKeyUp="value=value.replace(/[^0-9]/g,\'\')"  name="one[]">';
		newTR.insertCell(2).innerHTML = '<input class="textcss " type="text" maxlength="6" value="10" onKeyUp="value=value.replace(/[^0-9.]/g,\'\')"  name="price[]">';
		newTR.insertCell(3).innerHTML = '<input class="textcss " type="text" maxlength="6" value="1" onKeyUp="value=value.replace(/[^0-9]/g,\'\')" name="next[]">';
		newTR.insertCell(4).innerHTML = '<input class="textcss " type="text" maxlength="6" value="5" onKeyUp="value=value.replace(/[^0-9.]/g,\'\')" name="nprice[]">';
		newTR.insertCell(5).innerHTML = '<input type="button" value="删除" onClick="deleteRow(this)">';
	}
 }	
function deleteRow(r)
{
	var i=r.parentNode.parentNode.rowIndex
	document.getElementById('yltable').deleteRow(i)
}
function showArea(num)
{
	$('#tr_num').val(num);	
	$("#divArea").css("left",$('#tr'+num).offset().left);
    $("#divArea").css("top",$('#tr'+num).offset().top + $('#tr'+num).outerHeight());            
    $("#divArea").show();
	
	//关闭其它己经展开的
	$('.plabox .fwbox').removeClass('showbox');
	$(".plabox .citys").hide();	
	$('input[type=checkbox]','.plabox').each(function(i,c){
		$(c).attr('checked',false);
		$(c).attr('disabled',false);		
	});	
	$('span','.greas').html('');
	
	var otherIds='';
	for(j=1;j<document.getElementById("yltable").rows.length;j++)
	{
		if(j!=num)
		{
			t=document.getElementById('v_val_tr'+j).value;
			if(t!='')
				otherIds+=','+t;	
		}
	}
	var arr_otherIds=otherIds.substring(1).split(',');
	
	var vid=$('#v_val_tr'+num).val();
	var arr_vid=vid.split(',');
	if(vid=='' && arr_otherIds=='') return;
	
	//初始化
	$('.choosbox','.plabox').each(function (i,area){
		//地区复选框状态
		var achk=$('.ffbox input[type=checkbox]',area);			
		if(arr_vid.in_array($(achk).attr('value')))
		{
			$(achk).attr('checked',true);
		}
				
		$('.elsbox .fwbox',area).each(function(i,g){
			//省级复选框状态
			var gchk=$('.greas input[type=checkbox]',g);			
			if(arr_vid.in_array($(gchk).attr('value')))
			{
				$(gchk).attr('checked',true);
			}			
			var t=0;//计数
			$('.citys',g).each(function(i,c){
				$('input[type=checkbox]',c).each(function (i,o){
					if(arr_vid.in_array($(o).attr('value')))
					{
						$(o).attr('checked',true);
						t++;
					}					
					if(arr_otherIds.in_array($(o).attr('value')))
					{
						$(o).attr('disabled',true);						
						$(gchk).attr('disabled',true);//省级不可用			
						$(achk).attr('disabled',true);//地区不可用					
					}					
				});				
			});
			if(t>0)
			{
				$('span',g).html('('+t+')');	
			}
		});	
	});	
}
function hideArea()
{
	$('#divArea').hide();	
}

function subarea(v)
{	
	var subdiv=$(v).parent().next("div");
	if($(subdiv).is(":hidden"))
	{
		//关闭其它己经展开的
		$('.fwbox').removeClass('showbox');
		$(".citys").hide();	
		
		$(v).parents('.fwbox').addClass('showbox');
		$(subdiv).show();	
	}
	else
	{
		$(v).parents('.fwbox').removeClass('showbox');
		$(subdiv).hide();	
	}
}
function subhide(v)
{	
	$(v).parents('.fwbox').find('img').click();
}
function chxclick(v)
{	
	var area=$(v).parents('li');
	if($(v).attr('name')=='area')
	{		
		$('input[type=checkbox]',area).attr('checked',$(v).attr('checked'));		
		$('.elsbox .fwbox',area).each(function(i,o){			
			$('input[type=checkbox]',$('.citys',o)).attr('checked',$(v).attr('checked'));
			i=$('input[type=checkbox]:checked',$('.citys',o)).size();
			$('span',$("div:first",o)).html('('+i+')');//勾选数
		});
	}
	else
	{	
		var prodiv=$(v).parents('.fwbox');//省块div
		var province=$("div:first",prodiv);//列表div上面的div	
		var citys=$('.citys',prodiv);//列表div		
		
		if($(v).attr('name')=='province')
		{
			$('input[type=checkbox]',citys).attr('checked',$(v).attr('checked'));	
		}
	
		var count=$('input[type=checkbox]',citys).size();
		var i=$('input[type=checkbox]:checked',citys).size();
		$('span',province).html('('+i+')');//勾选数
		if(count==i)
		{
			$('input[type=checkbox]',province).attr('checked',true);
		}
		else
		{
			$('input[type=checkbox]',province).attr('checked',false);	
		}
		
		//进区勾是否选中
		count=$('input[type=checkbox]',area).size();
		i=$('input[type=checkbox]:checked',area).size();
		if(count==i+1)
		{
			$('.ffbox input[type=checkbox]',area).attr('checked',true);
		}
		else
		{
			$('.ffbox input[type=checkbox]',area).attr('checked',false);	
		}
	}
}
function saveArea()
{
	var num=$('#tr_num').val();//标记第几行
	var vname='';//显示
	var vid='';//值
	$('.choosbox','.plabox').each(function (i,o){		
		if($('.ffbox input[type=checkbox]',o).attr('checked')==true)//地区是否选
		{
			vname+=','+$('.ffbox input[type=checkbox]',o).attr('title');
		}
		else
		{
			$('.elsbox .fwbox',o).each(function(i,g){
				if($('.greas input[type=checkbox]',g).attr('checked')==true)//省
				{
					vname+=','+$('input[type=checkbox]',g).attr('title');
				}
				else
				{
					$('.citys input[type=checkbox]:checked',g).each(function(i,c){
						vname+=','+$(c).attr('title');
					});
				}
			});
		}	
	});	
	
	$('input[type=checkbox]:checked','.plabox').each(function(i,c){
		vid+=','+$(c).attr('value');	
	});
	
	vid=vid.substring(1);	
	vname=vname.substring(1);
	$('#v_val_tr'+num).val(vid);
	$('#v_txt_tr'+num).val(vname);
	if(vname!='')
	{
		$('p','#tr'+num).html(vname);
	}
	else
	{
		$('p','#tr'+num).html('未添加地区');	
	}
	hideArea();
} 



