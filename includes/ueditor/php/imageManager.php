<?php
    /**
     * Created by JetBrains PhpStorm.
     * User: taoqili
     * Date: 12-1-16
     * Time: ����11:44
     * To change this template use File | Settings | File Templates.
     */
	session_start();
    header("Content-Type: text/html; charset=gbk");
    error_reporting( E_ERROR | E_WARNING );
	$type=$_GET['type'];
	if($type==1)
	{
		$id=$_SESSION['admin_info']['city'];
		$u="data/files/mall/upload_".$id.'/'.date('Ym');
	}
	else
	{
		$id=$_SESSION['user_info']['user_id'];
		$u="data/files/".intval($id/2000)."/shop_".$id;
	}
	
    //��Ҫ������Ŀ¼�б����ʹ������ͼ��ַ������������ʱ���ܻ�������ص���ʱ
    $paths = array($u);
	
    $action = htmlspecialchars( $_POST[ "action" ] );
    if ( $action == "get" ) {
        include '../../upload.class.php';
		$data=array(
			'path'=>$u
			);
		$up=new upload($data);
		$arr_file=$up->getfilelist();		
      	echo implode('ue_separate_ue',$arr_file);
    }

    /**
     * ������ȡĿ¼�µ�ָ�����͵��ļ�
     * @param $path
     * @param array $files
     * @return array
     */
    function getfiles( $path , &$files = array() )
    {
        if ( !is_dir( $path ) ) return null;
        $handle = opendir( $path );
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( $file != '.' && $file != '..' ) {
               // $path2 = $path . '/' . $file;
			   $path2 = $path . '/' . $file;
                if ( is_dir( $path2 ) ) {
                    getfiles( $path2 , $files );
                } else {
                    if ( preg_match( "/\.(gif|jpeg|jpg|png|bmp)$/i" , $file ) ) {
                        $files[] = $path2;
                    }
                }
            }
        }
        return $files;
    }
?>