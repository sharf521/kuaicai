String.prototype.RTrim = function(){ return this.replace(/(\s*$)/g, ""); } 
function checkuser()
{
	var userid=$("#userid").val();
	if(!userid.match(/^[a-zA-Z0-9]{5,18}$/))
	{
		$("#spn_userid").html('字母和数字长度控制在5-18位');
		$('#spn_userid').css('color','red');
	}
	else
	{	
		$.ajax({
			url: "ajax.php",
			data:{func:"checkuserid",userid:userid},
			type: 'POST',
			cache: false,
			success: function(html){
				$("#spn_userid").html(html);
			}
		});	
	}	
}
function getUserId(v)
{
	if(v.length<2 || v.length>18)
	{
		$('#spn_username').html('输入真实的姓名!');
		$('#spn_username').css('color','red');
	}
	else
	{
		$('#spn_username').html('√');
		$('#spn_username').css('color','green');
		$.ajax({
			url:'ajax.php',
			data:{func:'getuserid',name:v},
			type:'POST',
			cache:false,
			success:function(html)
			{
				$("#userid").val(html.RTrim());
			}
		})
	}		
}
function checkpwd1(v)
{
	if(v.length<6 || v.length>18)
	{
		$('#spn_pwd1').html('长度限制在6-18个字符!');
		$('#spn_pwd1').css('color','red');
	}
	else
	{
		$('#spn_pwd1').html('√');
		$('#spn_pwd1').css('color','green');
	}
}
function checkpwd2(v)
{
	if(v.length<6 || v.length>18)
	{
		$('#spn_pwd2').html('长度限制在6-18个字符!');	
		$('#spn_pwd2').css('color','red');
		return;
	}			
	if(v!=$('#password1').val())
	{
		$('#spn_pwd2').html('输入的两次密码不一致!');
		$('#spn_pwd2').css('color','red');
	}
	else
	{
		$('#spn_pwd2').html('√');
		$('#spn_pwd2').css('color','green');
	}
}
function checkmail(v)
{
	if((v.indexOf("@")==-1)||(v.indexOf(".")==-1))
	{
		$('#spn_mail').html('输入正确的电子邮箱！');
		$('#spn_mail').css('color','red');
	}
	else
	{
		$('#spn_mail').html('√');
		$('#spn_mail').css('color','green');	
	}
}
function checkregfrom()
{
	if(document.getElementById('randcode').value=='')
	{
		alert('请输入的验证码！');
		document.getElementById('randcode').focus();
		return false;
	}
	if(!document.getElementById('checkbox_1').checked)
	{
		alert('请接受服务条款！');
		return false;
	}
}