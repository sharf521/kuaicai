<?php
    /**
     * Created by JetBrains PhpStorm.
     * User: taoqili
     * Date: 12-7-26
     * Time: ����10:32
     */
	session_start(); 
    header("Content-Type: text/html; charset=gbk");
    error_reporting( E_ERROR | E_WARNING );
    include "Uploader.class.php";
	$type=$_GET['type'];
    //�ϴ�����
	if($type==1)
	{
		$id=$_SESSION['admin_info']['city'];
		$u="../../../data/files/mall/upload_".$id;
	}
	else
	{
		$id=$_SESSION['user_info']['user_id'];
		$u="../../../data/files/store_".$id."/upload_".$id;
	}
	
    $config = array(
        "savePath" => $u."/" , //����·��
        "allowFiles" => array( ".rar" , ".doc" , ".docx" , ".zip" , ".pdf" , ".txt" , ".swf" , ".wmv" ) , //�ļ������ʽ
        "maxSize" => 100000 //�ļ���С���ƣ���λKB
    );
    //�����ϴ�ʵ����������ϴ�
    $up = new Uploader( "upfile" , $config );

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

    /**
     * ���������������json����
     * {
     *   'url'      :'a.rar',        //�������ļ�·��
     *   'fileType' :'.rar',         //�ļ���������ͼƬ��˵��ǰ�˻���ӵ�title������
     *   'original' :'�༭��.jpg',   //ԭʼ�ļ���
     *   'state'    :'SUCCESS'       //�ϴ�״̬���ɹ�ʱ����SUCCESS,�����κ�ֵ��ԭ��������ͼƬ�ϴ�����
     * }
     */
    echo '{"url":"' .$info[ "url" ] . '","fileType":"' . $info[ "type" ] . '","original":"' . $info[ "originalName" ] . '","state":"' . $info["state"] . '"}';

