<?php
	

    /**
     * Created by JetBrains PhpStorm.
     * User: taoqili
     * Date: 12-7-18
     * Time: ����10:42
     */
	session_start(); 
    header("Content-Type: text/html; charset=gbk");
    error_reporting(E_ERROR | E_WARNING);
    date_default_timezone_set("Asia/chongqing");
    include "Uploader.class.php";
	
    //�ϴ�ͼƬ���е����������ƣ�
	
	$title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
    $path = htmlspecialchars($_POST['dir'], ENT_QUOTES);
 	$id=$_SESSION['user_info']['user_id'];
	$u="../../../data/files/store_".$id;
	
	//$u=$_SERVER['DOCUMENT_ROOT']."/data/files/";
    //�ϴ�����
    $config = array(
        "savePath" => ($path == "1" ? $u."/up_".$id."/" : $u."/up1_".$id."/"),
        "maxSize" => 1000, //��λKB
        "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")
    );

    //�����ϴ�ʵ����������ϴ�
    $up = new Uploader("upfile", $config);

    /**
     * �õ��ϴ��ļ�����Ӧ�ĸ�������,����ṹ
     * array(
     *     "originalName" => "",   //ԭʼ�ļ���
     *     "name" => "",           //���ļ���
     *     "url" => "",            //���صĵ�ַ
     *     "size" => "",           //�ļ���С
     *     "type" => "" ,          //�ļ�����
     *     "state" => ""           //�ϴ�״̬���ϴ��ɹ�ʱ���뷵��"SUCCESS"
     * )
     */
    $info = $up->getFileInfo();
	$lujing=explode('..',$info["url"]);
	
    /**
     * ���������������json����
     * {
     *   'url'      :'a.jpg',   //�������ļ�·��
     *   'title'    :'hello',   //�ļ���������ͼƬ��˵��ǰ�˻���ӵ�title������
     *   'original' :'b.jpg',   //ԭʼ�ļ���
     *   'state'    :'SUCCESS'  //�ϴ�״̬���ɹ�ʱ����SUCCESS,�����κ�ֵ��ԭ��������ͼƬ�ϴ�����
     * }
     */
    echo "{'url':'" . $lujing[3] . "','title':'" . $title . "','original':'" . $info["originalName"] . "','state':'" . $info["state"] . "'}";

