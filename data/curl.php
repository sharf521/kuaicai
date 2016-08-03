
<?



$post=array();
		$post['act']		='getlist';
		$post['sign']		='deyumall_upload_img';	
		$post['filepath']	='data/files/0/shop_350';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://picture.5-58.com/upload.php');
		curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($curl);
		curl_close($curl);
		echo $result;
?>