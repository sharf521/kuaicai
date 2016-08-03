<?
	if(isset($_REQUEST["func"]))
	{
		require './include/admin.class.php';
		$admin=new admin();
		if($_POST["func"]=="login")
		{
			
			if($_SESSION["code"]!=$_POST["randcode"])
			{
				$error="您输入的验证码错误！";
			} 
			else
			{ 
				$check=$admin->login($_POST["userid"],$_POST["password"]);				
				if($check>0)
				{
					echo "<script>window.top.location='?act=manage';</script>";
					exit();
				}
				else
					$error= "用户名或密码错误！";	
			}
		}
		if($_GET["func"]=="logout")
		{
			$admin->logout();
			echo "<script>window.parent.location='?act=login';</script>";
			exit();
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>网站管理后台用户登录</title>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<script type="text/javascript" src="js/js.js"></script>
</head>
<body onload="document.getElementById('userid').focus()">
<div id="top"> </div>
<form method="post" id="login">
  <div id="center">
    <div id="center_left"></div>
    <div id="center_middle">
      <div class="user">
      
        <label>用户名：
        <input type="text" class="INPUT" name="userid" id="userid"/>
        </label>
      </div>
      <div class="user">
        <label>密　码：
        <input type="password" class="INPUT" name="password" id="password" />
        </label>
      </div>
      <div class="chknumber">
        <label>验证码：
        <input name="randcode" type="text" id="randcode" maxlength="4" class="chknumber_input" />
        </label>
        <img src="./include/code.php" align="middle" height="18"/>
      </div>
    </div>
    <div id="center_middle_right"></div>
    <div id="center_submit">
      <div class="button"><input type="submit" value=" " style="width:57px; border:0px; cursor:pointer; height:20px; background-image:url(images/dl.gif)" width="57" height="20" /> </div>
      <div class="button"> <img src="images/cz.gif" width="57" height="20" onclick="form_reset()"> </div>
    </div>
    <div id="center_right"></div>
  </div> <input type="hidden" name="func" value="login"/>
</form>
<div id="footer"><? if($error!="") echo $error;?></div>
<script language="javascript">
	function init()
	{
		document.getElementById('userid').focus();
	}
</script>
</body>
</html>
