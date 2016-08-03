<?php
	

    /**
     * Created by JetBrains PhpStorm.
     * User: taoqili
     * Date: 12-7-18
     * Time: 上午10:42
     */
	session_start(); 
    header("Content-Type: text/html; charset=gbk");
    error_reporting(E_ERROR | E_WARNING);
    date_default_timezone_set("Asia/chongqing");
    
	
	//获取存储目录
    if ( isset( $_GET[ 'fetch' ] ) ) 
	{
		echo 'updateSavePath(["upload1","upload2","upload3"]);';
        return;
    }
	
    //上传图片框中的描述表单名称，
	
	$title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
    $path = htmlspecialchars($_POST['dir'], ENT_QUOTES);
 	$id=$_SESSION['user_info']['user_id'];
	$type=$_GET['type'];
	if($type==1)
	{
		$id=$_SESSION['admin_info']['city'];
		$u="data/files/mall/upload_".$id.'/'.date('Ym');
	}
	else
	{
		$id=$_SESSION['user_info']['user_id'];
		//$u="data/files/store_".$id;
		$u="data/files/".intval($id/2000)."/shop_".$id.'/'.date('Ym');;
	}
	include '../../upload.class.php';
	$data=array('field'=>'upfile',
		'path'=>$u,
		'name'=>''
		);
	$up=new upload($data);
	$arr=$up->save();
	if($arr['status']==1)
	{	
		echo "{'url':'" . $arr['file'] . "','title':'" . $title . "','original':'','state':'SUCCESS'}";
	}
	else
	{
		echo '{"state":"'.$arr['error'].'"}';
        return;
	}