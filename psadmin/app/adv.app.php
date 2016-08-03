<?php

/**
 *    商品分享管理控制器
 *
 *    @author    Hyber
 *    @usage    none
 */
class AdvApp extends BackendApp
{
    var $_m_share;

    function __construct()
    {
        $this->AdvApp();
    }

    function AdvApp()
    {
        parent::BackendApp();

        $this->adv_mod =& m('adv');
		$this->member_mod =& m('member');
		$this->userpriv_mod =& m('userpriv');
		$this->advtype_mod =& m('advtype');
		$this->city_mod =& m('city');
    }

    /**
     *    商品分享索引
     *
     *    @author    Hyber
     *    @return    void
     */
    function index()
    {
	$user=$this->visitor->get('user_name');
$row_member=$this->member_mod->getrow("select * from ".DB_PREFIX."member where user_name = '$user'");
	//$city=$row_member['city'];

    $userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
	$privs=$priv_row['privs'];
	$city=$priv_row['city'];
	$this->assign('priv_row', $priv_row);
	 $adv_row=$this->advtype_mod->getAll("select * from ".DB_PREFIX."adv_type");
		   $this->assign('adv_row', $adv_row);
	 $conditions = $this->_get_query_conditions(array(array(
                'field' => 'type',
                'name'  => 'leixing',
                'equal'  => '=',
            ),
			array(
                'field' => 'adv_city',
                'name'  => 'suoshuzhan',
                'equal' => '=',
            ),
			array(
                'field' => 'start_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
               // 'handler'=> 'gmstr2time',
            ),
			array(
                'field' => 'end_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                //'handler'   => 'gmstr2time_end',
			),
			));
			//print_r($conditions);
	 $page = $this->_get_page();
	  $sort  = 'adv_id';
      $order = 'desc';
		if($privs=='all')
		{
        $adv = $this->adv_mod->find(array(
		 'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
			'conditions' => '1=1 '.$conditions,
        ));
		}
		else{
		$adv = $this->adv_mod->find(array(
		 'conditions' => 'adv_city='.$city .$conditions,
		 'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
		}
		
		$city_row=array();
		$result=$this->adv_mod->getAll("select * from ".DB_PREFIX."city");
		foreach ($result as $var )
		{
			$row=explode('-',$var['city_name']);
		    $city_row[$var['city_id']]=$row[0];
		}
		$this->assign('result', $result);
		$result=null;
		 foreach ($adv as $key => $val)
        {
			$adv[$key]['city_name'] = $city_row[$val['adv_city']];	
        }
		
		$advrow=array();
		$result=$this->adv_mod->getAll("select * from ".DB_PREFIX."adv_type");
		foreach ($result as $var )
		{
			
		    $advrow[$var['id']]=$var['type'];
		}
		$result=null;
		 foreach ($adv as $key => $val)
        {
			$adv[$key]['type_name'] = $advrow[$val['type']];	
        }
	   
        $page['item_count'] = $this->adv_mod->getCount();
		$this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		  $this->assign('adv', $adv);
        $this->display('adv.index.html');
    }

	function add()
    {
		$riqi=date('Y-m-d H:i:s');
		$this->webservice_list_mod =& m('webservice_list');
		$this->my_money_mod =& m('my_money');
		$this->moneylog_mod =& m('moneylog');
        if (!IS_POST)
        {
		    $userid=$this->visitor->get('user_id');
			$priv_row=$this->userpriv_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
			$cityid=$priv_row['city'];
			 if($priv_row['privs']=="all")
			 {
				$city_row=$this->member_mod->getAll("select * from ".DB_PREFIX."city");
			 }
			 else
			 {
				$city_row=$this->member_mod->getAll("select * from ".DB_PREFIX."city where city_id='$cityid'");
			 }
	     $adv_row=$this->advtype_mod->getAll("select * from ".DB_PREFIX."adv_type");
		  $this->assign('adv_row', $adv_row);
	      $this->assign('city_row', $city_row);

		  $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
          $this->display('adv.form.html');
        }
        else
        {	
			if(empty($_POST['title']))
			{
				$this->show_warning('mingchengbuneng');
				return;
			}
			$canshu=$this->adv_mod->getRow("select adv_tj,jifenxianjin from ".DB_PREFIX."canshu limit 1");
			$riqi=date('Y-m-d H:i:s');
			$type=trim($_POST['type']);
			//$adv_row=$this->adv_mod->getRow("select bianhao from ".DB_PREFIX."adv where type = '$type' order by bianhao desc limit 1");
			//$bh=$adv_row['bianhao'];
			//$bianhao=$bh+1;
			$username_g=$_POST['username_g'];//购买人
			$username_tj=$_POST['username_tj'];//购买人的介绍人
			$real_zf=(float)$_POST['real_zf'];
			if(!empty($username_g) && !empty($real_zf))
			{
				$memg=$this->adv_mod->getRow("select user_id,web_id,city from ".DB_PREFIX."member where user_name = '$username_g'  limit 1");
				if(empty($memg['user_id']))
				{
					$this->show_warning('gaigoumai');
					return;
				}
				$mem=$this->adv_mod->getRow("select * from ".DB_PREFIX."my_money where user_name = '$username_g'  limit 1");
				if($real_zf > $mem['money'])
				{
					$this->show_warning('zijinbuzu');
					return;
				}
				$new_money=$mem['money']-$real_zf;
				$web_id=$memg['web_id'];
				$city_g=$memg['city'];
				$row_kaiguan=$this->adv_mod->getRow("select webservice from ".DB_PREFIX."kaiguan limit 1");
				$webservice=$row_kaiguan['webservice'];
			
				if(!empty($username_tj))
				{
					$memtj=$this->adv_mod->getRow("select user_id,city from ".DB_PREFIX."member where user_name = '$username_tj'  limit 1");
					if(empty($memtj['user_id']))
					{
						$this->show_warning('gaijieshao');
						return;
					}
					$ticheng=$real_zf*$canshu['adv_tj'];
					$jieshao=$this->adv_mod->getRow("select * from ".DB_PREFIX."my_money where user_name = '$username_tj'  limit 1");
					$new_moneytj=$jieshao['money']+$ticheng;
				}
			}
			$tupian=$_POST['ima'];
			$tupian1=$_POST['ima_1'];
            $data = array(
                'title' => trim($_POST['title']),
                'type'  => trim($_POST['type']),
                'content'  => trim($_POST['content']),
				'lianjie'  => trim($_POST['lianjie']),
				'lianjie_1'  => trim($_POST['lianjie_1']),
                //'image'  => trim($_POST['image']),
				//'image_1'  => trim($_POST['image_1']),
				'start_time'  => trim($_POST['start_time']),
			    'end_time'  => trim($_POST['end_time']),
				'adv_city'=> trim($_POST['adv_city']),
				'riqi'=>$riqi,
				//'price'=>trim($_POST['price']),
				'real_zf'=>trim($_POST['real_zf']),
				'username_g'=>trim($_POST['username_g']),
				'username_tj'=>trim($_POST['username_tj']),
				//'bianhao'=>$bianhao,
            );


			if($tupian!='')
			$data['image']=$tupian;
			if($tupian1!='')
			$data['image_1']=$tupian1;
			
            if (!$adv_id = $this->adv_mod->add($data))  //获取brand_id
            {
                $this->show_warning($this->adv_mod->get_error());
                return;
            }
            //$logo       =   $this->_upload_logo($adv_id);
			/* 处理上传的图片 */
			
			if($tupian=='')
			{
            $logo       =   $this->_upload_logo($adv_id,'image');
            $logo && $this->adv_mod->edit($adv_id, array('image' => $logo)); 
			}
			if($tupian1=='')
			{
				$logo1       =   $this->_upload_logo($adv_id,'image_1');
				$logo1 && $this->adv_mod->edit($adv_id, array('image_1' => $logo1));
			}
			
			
			if(!empty($username_g) && !empty($real_zf))
			{
			//更新购买人广告位moneylog表
				$beizhu =Lang::get('goumaiguanggaowei');
				$beizhu=str_replace('{1}',$username_g,$beizhu);
				$beizhu=str_replace('{2}',$adv_id,$beizhu);
				$addlog=array(
				'money'=>'-'.$real_zf,
				'time'=>$riqi,
				'user_name'=>$username_g,
				'user_id'=>$memg['user_id'],
				'zcity'=>$city_g,
				'type'=>121,
				's_and_z'=>2,
				'beizhu'=>$beizhu,
				'dq_money'=>$new_money,
				'dq_money_dj'=>$mem['money_dj'],
				'dq_jifen'=>$mem['duihuanjifen'],
				'dq_jifen_dj'=>$mem['dongjiejifen']);
				 $this->moneylog_mod->add($addlog);
				 	 
				if($webservice=="yes")
			  	{
					$post_data = array(
					"ID"=>$web_d,
					"Money"=>$real_zf,
					"MoneyType"=>2,
					"Count"=>1
						); 
					$web_id= webService('C_Consume',$post_data);
					$daa=array(
					"gong_id"=>$memg['user_id'],
					"type"=>2,
					"money"=>$real_zf,
					"consume_id"=>$web_id,
					"time"=>$riqi,
					"status"=>0,
					"city"=>$city_g
					);
					$this->webservice_list_mod->add($daa);
					
					if(!empty($username_tj))
					{
					//更新推荐购买人去买广告位moneylog表
						$beizhu =Lang::get('tuijianguanggaowei');
						$beizhu=str_replace('{1}',$username_tj,$beizhu);
						$beizhu=str_replace('{2}',$username_g,$beizhu);
						$beizhu=str_replace('{3}',$adv_id,$beizhu);
						$addlog=array(
						'money'=>$ticheng,
						'time'=>$riqi,
						'user_name'=>$username_tj,
						'user_id'=>$memtj['user_id'],
						'zcity'=>$memtj['city'],
						'type'=>122,
						's_and_z'=>1,
						'beizhu'=>$beizhu,
						'dq_money'=>$new_moneytj,
						'dq_money_dj'=>$jieshao['money_dj'],
						'dq_jifen'=>$jieshao['duihuanjifen'],
						'dq_jifen_dj'=>$jieshao['dongjiejifen']);
						 $this->moneylog_mod->add($addlog);
					}
					
				}
			  $this->my_money_mod->edit('user_id='.$memg['user_id'],array('money'=>$new_money));
			  $this->my_money_mod->edit('user_id='.$memtj['user_id'],array('money'=>$new_moneytj));	
			  $this->my_money_mod->tuijian($real_zf*$canshu['jifenxianjin'],$memg['user_id'],0,0,0);		 
			}
			
			
			
            //$this->_clear_cache();
            $this->show_message('add_successed',
                'back_list',    'index.php?app=adv',
                'continue_add', 'index.php?app=adv&amp;act=add'
            );
        }
    }
 function _upload_logo($adv_id,$can)
    {
        //$file = $_FILES['image'];
		$file = $_FILES[$can];
		$riqi=time().rand(100,999);
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES[$can]);//上传logo
        if (!$uploader->file_info())
        {
            $this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=adv&amp;act=edit&amp;id=' . $adv_id);
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/mall/adv', $riqi.$adv_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        }
        else
        {
            return false;
        }
    }

   
    function drop()
    {
        $adv_id = isset($_GET['id']) ? trim($_GET['id']) : 0;
        
       
        if (!$this->adv_mod->drop($adv_id))    //删除
        {
            $this->show_warning($this->adv_mod->get_error());

            return;
        }

        $this->show_message('drop_successed');
    }

    function edit()
    {
        $adv_id = empty($_GET['id']) ? 0 : $_GET['id'];
		$pag = empty($_GET['page']) ? 0 : $_GET['page'];
    
        if (!$adv_id)
        {
            $this->show_warning('no_adv');
            return;
        }
         if (!IS_POST)
        {
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
	$cityid=$priv_row['city'];
	$advter=$this->userpriv_mod->getrow("select adv_city from ".DB_PREFIX."adv where adv_id = '$adv_id'");
	$adv_row=$this->advtype_mod->getAll("select * from ".DB_PREFIX."adv_type");
	$this->assign('adv_row', $adv_row);
	 if($priv_row['privs']=="all")
	 {
	   $city_row=$this->city_mod->getAll("select * from ".DB_PREFIX."city");
	 }
	 else
	 {
	  $city_row=$this->city_mod->getAll("select * from ".DB_PREFIX."city where city_id='$cityid'");
	 }

            $find_data     = $this->adv_mod->find($adv_id);
            if (empty($find_data))
            {
                $this->show_warning('no_adv');

                return;
            }
            $adv    =   current($find_data);
            
            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
           $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
			
			
            $this->assign('yes_or_no', $yes_or_no);
			$this->assign('advter', $advter);
			$this->assign('city_row', $city_row);
            $this->assign('adv', $adv);
            $this->display('adv.edit.html');
        }
        else
        {
		$adv_id=$_POST['adv_id'];

			$data['adv_id']     =   $_POST['adv_id'];
            $data['title']     =   $_POST['title'];
            $data['type']     =   $_POST['type'];
			$data['lianjie_1']     =   $_POST['lianjie_1'];
			$data['lianjie']     =   $_POST['lianjie'];
            $data['start_time']    =   $_POST['start_time'];
            $data['end_time'] = $_POST['end_time'];
			$data['content']    =   $_POST['content'];
			$data['adv_city']    =   $_POST['adv_city'];
			
			
			
			$tupian=$_POST['ima'];
			$tupian1=$_POST['ima_1'];
			if($tupian!='')
			{
				$data['image']=$tupian;
			}
			else
			{
				$logo       =   $this->_upload_logo($adv_id,'image');
				$logo && $this->adv_mod->edit($adv_id, array('image' => $logo));
			}
			if($tupian1!='')
			{
				$data['image_1']=$tupian1;
			}
			else
			{
				$logo1       =   $this->_upload_logo($adv_id,'image_1');
				$logo1 && $this->adv_mod->edit($adv_id, array('image_1' => $logo1));
			}
			
            $rows=$this->adv_mod->edit($adv_id, $data);
            
   
			  $this->show_message('edit_successed',
                'back_list',        'index.php?app=adv&page= '. $pag,
                'edit_again',    'index.php?app=adv&amp;act=edit&amp;id=' . $adv_id);
			
        }
    }

 function type_add()
    {
	     $user=$this->visitor->get('user_name');
		 $adv_row=$this->member_mod->getrow("select city from ".DB_PREFIX."member where user_name = '$user'");
	     $adv_city=$adv_row['city'];
		 $riqi=date('Y-m-d H:i:s');
        if (!IS_POST)
        {
		
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
	 $this->assign('priv_row', $priv_row);
           $city_row=$this->member_mod->getAll("select * from ".DB_PREFIX."city");
		   $this->assign('city_row', $city_row);
		    $this->display('adv.type.html');
		}
		 else
        {
		
		$type=trim($_POST['type']);
            $data = array(
                'type'  => trim($_POST['type']),
                'danjia'  => trim($_POST['danjia']), 
            );
			$this->advtype_mod->add($data);
            $this->show_message('add_successed',
                'back_list',    'index.php?app=adv&amp;act=adv_type',
                'continue_add', 'index.php?app=adv&amp;act=type_add'
            );
        }
    }   
	function adv_type()
	{
	
	$userid=$this->visitor->get('user_id');
	$priv_row=$this->userpriv_mod->getrow("select * from ".DB_PREFIX."user_priv where user_id = '$userid' and store_id=0");
	 $this->assign('priv_row', $priv_row);
	 $page = $this->_get_page();
	 $adv_row = $this->advtype_mod->find(array(
		 'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));
	 $this->assign('adv_row', $adv_row);
	 $page['item_count'] = $this->advtype_mod->getCount();
     $this->_format_page($page);
     $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
     $this->assign('page_info', $page);
    $this->display('adv.type_index.html');
	
	}   

 function type_edit()
    {
        $id = empty($_GET['id']) ? 0 : $_GET['id'];
         if (!IS_POST)
        {
             $adv_row=$this->advtype_mod->getrow("select type from ".DB_PREFIX."adv_type where id = '$id'");
	        $this->assign('adv_row', $adv_row);
            $this->display('adv.edit_type.html');
        }
        else
        {
            $data = array();
            $data['type']     =   $_POST['type'];
			$data['danjia']     =   $_POST['danjia'];
            $rows=$this->advtype_mod->edit($id, $data);
            $this->show_message('edit_successed',
                'back_list',        'index.php?app=adv&act=adv_type',
                'edit_again',    'index.php?app=adv&amp;act=type_edit&amp;id=' . $id);
        }
    }



}

?>
