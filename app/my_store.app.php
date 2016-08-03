<?php

class My_storeApp extends StoreadminbaseApp
{
    var $_store_id;
    var $_store_mod;
    var $_uploadedfile_mod;

    function __construct()
    {
        $this->My_storeApp();
    }
    function My_storeApp()
    {
        parent::__construct();
        $this->_store_id  = intval($this->visitor->get('manage_store'));
        $this->_store_mod =& m('store');
        $this->_uploadedfile_mod = &m('uploadedfile');
		$this->storelog_mod=& m('storelog');
    }

    function index()
    {
		$host=$_SERVER['HTTP_HOST'];
		$hostt=explode('.',$host);
		if(count($hostt)>2)
		{
			$host=$hostt[1].'.'.$hostt[2];
		}
		$this->assign('host',$host);
        $tmp_info = $this->_store_mod->get(array(
            'conditions' => $this->_store_id,
            'join'       => 'belongs_to_sgrade',
            'fields'     => 'domain, functions',
        ));
        $subdomain_enable = false;
        if (ENABLED_SUBDOMAIN && in_array('subdomain', explode(',', $tmp_info['functions'])))
        {
            $subdomain_enable = true;
        }
        if (!IS_POST)
        {
            //传给iframe参数belong, item_id
            $this->assign('belong', BELONG_STORE);
            $this->assign('id', $this->_store_id);

            $store = $this->_store_mod->get_info($this->_store_id);
            $this->assign('store', $store);
            $this->assign('editor_upload', $this->_build_upload(array(
                'obj' => 'EDITOR_SWFU',
                'belong' => BELONG_STORE,
                'item_id' => $this->_store_id,
                'button_text' => Lang::get('bat_upload'),
                'button_id' => 'editor_upload_button',
                'progress_id' => 'editor_upload_progress',
                'upload_url' => 'index.php?app=swfupload',
                'if_multirow' => 1,
            )));
            $this->assign('build_editor', $this->_build_editor(array('name' => 'description')));

            $msn_active_url = 'http://settings.messenger.live.com/applications/websignup.aspx?returnurl=' .
                SITE_URL . '/index.php' . urlencode('?app=my_store&act=update_im_msn') . '&amp;privacyurl=' . SITE_URL . '/index.php' . urlencode('?app=article&act=system&code=msn_privacy');
            $this->assign('msn_active_url', $msn_active_url);

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));
            //$this->headtag('<script type="text/javascript" src="{lib file=mlselection.js}">
			/*</script>');*/

            /* 属于店铺的附件 */
            $files_belong_store = $this->_uploadedfile_mod->find(array(
                'conditions' => 'store_id = ' . $this->visitor->get('manage_store') . ' AND belong = ' . BELONG_STORE . ' AND item_id =' . $this->visitor->get('manage_store'),
                'fields' => 'this.file_id, this.file_name, this.file_path',
                'order' => 'add_time DESC'
            ));
            /* 当前页面信息 */
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member', LANG::get('my_store'));
            $this->_curitem('my_store');
            $this->_curmenu('my_store');
            $this->import_resource('jquery.plugins/jquery.validate.js,mlselection.js');
            $this->assign('files_belong_store', $files_belong_store);
            $this->assign('subdomain_enable', $subdomain_enable);
            $this->assign('domain_length', Conf::get('subdomain_length'));
            $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('my_store'));
            $this->display('my_store.index.html');
        }
        else
        {
            $subdomain = $tmp_info['domain'];
            if ($subdomain_enable && !$tmp_info['domain'])
            {
                $subdomain = empty($_POST['domain']) ? '' : trim($_POST['domain']);
                if (!$this->_store_mod->check_domain($subdomain, Conf::get('subdomain_reserved'), Conf::get('subdomain_length')))
                {
                    $this->show_warning($this->_store_mod->get_error());
                    return;
                }
            }
            $data = $this->__upload();
            if ($data === false)
            {
                return;
            }
            $data = array_merge($data, array(
                'store_name' => $_POST['store_name'],
                'region_id'  => $_POST['region_id'],
                'region_name'=> $_POST['region_name'],
                'description'=> $_POST['description'],
                'address'    => $_POST['address'],
                'tel'        => $_POST['tel'],
                'im_qq'      => $_POST['im_qq'],
                'im_ww'      => $_POST['im_ww'],
                'domain'     => $subdomain,
				'gettype'        => $_POST['gettype'],
				'public_account'        => $_POST['public_account'],
            ));
            $this->_store_mod->edit($this->_store_id, $data);
			
			$row=$this->_store_mod->getRow("select cityid from ".DB_PREFIX."store where store_id = '$this->_store_id' limit 1");
			$daa=array(
			'store_id'=>$this->_store_id,
			'store_name'=>$_POST['store_name'],
			'gettype'=>$_POST['gettype'],
			'riqi'=>date('Y-m-d H:i:s'),
			'city'=>$row['cityid']
			);
			
			$this->storelog_mod->add($daa);
            $this->show_message('edit_ok');
        }
    }

    function update_im_msn()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $this->_store_mod->edit($this->_store_id, array('im_msn' => $id));
        header("Location: index.php?app=my_store");
        exit;
    }

    function drop_im_msn()
    {
        $this->_store_mod->edit($this->_store_id, array('im_msn' => ''));
        header("Location: index.php?app=my_store");
        exit;
    }

    function _get_member_submenu()
    {
        return array(
            array(
                'name' => 'my_store',
                'url'  => 'index.php?app=my_store',
            ),
        );
    }

    /**
     * 上传文件
     *
     */
	 
	 function __upload()
	{
		$return=array();
		include 'includes/upload.class.php';
		if($_FILES['store_logo']!='')
		{
			$data=array('field'=>'store_logo',
				'path'=>'data/files/'.intval($_shopid/2000).'/shop_' . $this->_store_id . '/other',
				'name'=>'store_logo'
			);
			$up=new upload($data);
			$arr=$up->save();
			if($arr['status']==1)
			{
				$return['store_logo']=$arr['file'];		
			}
			else
			{
				$this->show_warning($arr['error']);
				return false;
			}
		}
		
		if($_FILES['store_banner']!='')
		{
			$data=array('field'=>'store_banner',
				'path'=>'data/files/'.intval($_shopid/2000).'/shop_' . $this->_store_id . '/other',
				'name'=>'store_banner'
			);
			$up=new upload($data);
			$arr=$up->save();
			if($arr['status']==1)
			{
				$return['store_banner']=$arr['file'];		
			}
			else
			{
				$this->show_warning($arr['error']);
				return false;
			}
		}
		return $return;	
	} 
    function _upload_files()
    {
        import('uploader.lib');
        $data      = array();
        /* store_logo */
        $file = $_FILES['store_logo'];
        if ($file['error'] == UPLOAD_ERR_OK && $file !='')
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            $uploader->allowed_size(SIZE_STORE_LOGO); // 200KB
            $uploader->addFile($file);
            if ($uploader->file_info() === false)
            {
                $this->show_warning($uploader->get_error());
                return false;
            }
            $uploader->root_dir(ROOT_PATH);
            $data['store_logo'] = $uploader->save('data/files/store_' . $this->_store_id . '/other', 'store_logo');
        }

        /* store_banner */
        $file = $_FILES['store_banner'];
        if ($file['error'] == UPLOAD_ERR_OK && $file !='')
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            $uploader->allowed_size(SIZE_STORE_BANNER); // 200KB
            $uploader->addFile($file);
            if ($uploader->file_info() === false)
            {
                $this->show_warning($uploader->get_error());
                return false;
            }
            $uploader->root_dir(ROOT_PATH);
            $data['store_banner'] = $uploader->save('data/files/store_' . $this->_store_id . '/other', 'store_banner');
        }

        return $data;
    }
        /* 异步删除附件 */
    function drop_uploadedfile()
    {
        $file_id = isset($_GET['file_id']) ? intval($_GET['file_id']) : 0;

        $file = $this->_uploadedfile_mod->get($file_id);

        if ($file_id && $file['store_id'] == $this->visitor->get('manage_store') && $this->_uploadedfile_mod->drop1($file_id))
        {
            $this->json_result('drop_ok');
            return;
        }
        else
        {
            $this->json_error('drop_error');
            return;
        }
    }
	
	function caigou()
	{
		
/*	   if (!$this->_applyible()) {
            return;
        }
*/	$this->gonghuo_mod =& m('gonghuo');
	$this->store_mod =& m('store');
	$this->member_mod =& m('member');
	$this->caigou_mod =& m('caigou');
	$this->moneylog_mod =& m('moneylog');
	$this->my_money_mod =& m('my_money');
	$this->message_mod =& m('message');
	$this->my_moneylog_mod =& m('my_moneylog');
	 $user_id = $this->visitor->get('user_id');	
	 $user_name = $this->visitor->get('user_name');
	$gonghuo_id = empty($_GET['id']) ? null : trim($_GET['id']);
	$canshu=$this->member_mod->can();
	$lv31=$canshu['lv31'];
	$jifenxianjin=$canshu['jifenxianjin'];
	// echo $gh_id;
	 $num= trim($_POST['num']);
	 $gh_id= trim($_POST['gh_id']);
	 $gong_id= trim($_POST['gong_id']);
	 $gong_name= trim($_POST['gong_name']);
	 $chanpin= trim($_POST['chanpin']);
	 $zhifufangshi= trim($_POST['zhifufangshi']);
	 $goods_name= trim($_POST['goods_name']);
	 $lingshou_price= trim($_POST['lingshou_price']);
	 $jifen_price= trim($_POST['jifen_price']);
	 $zong_jiage=$lingshou_price*$num;//采购的总价格
	 $jiage=$zong_jiage*$lv31;//采购价格的31%（金额）
	 $jifen_jiage=$zong_jiage*$jifenxianjin*$lv31;//采购价格的31%（积分）
 	 $member_row=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");
	  $mcity=$member_row['city'];
	  $lev=$member_row['level'];
      $riqi=date('Y-m-d H:i:s');
	  $gonghuo_row=$this->member_mod->getRow("select yu_kucun,zong_kucun from ".DB_PREFIX."gonghuo where gh_id='$gh_id' limit 1");
	 $zong_kucun=$gonghuo_row['zong_kucun'];
	 $yu_kucun=$gonghuo_row['yu_kucun'];
	  
	  $this->_curlocal(array(array('text' => Lang::get('woyaocaigou'))));
	$deng=Lang::get('dengdaishenhe');
	$store_row=$this->store_mod->getRow("select store_id from ".DB_PREFIX."store where store_id='$user_id' limit 1");

 $money_row=$this->member_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");
 $money=$money_row['money'];
 $money_dj=$money_row['money_dj'];
 $duihuanjifen=$money_row['duihuanjifen'];
 $dongjiejifen=$money_row['dongjiejifen'];
 $suoding_money=$money_row['suoding_money'];
 $suoding_jifen=$money_row['suoding_jifen'];
 $new_money=$money-$jiage;
 $new_money_dj=$money_dj+$jiage;
 $new_duihuanjifen=$duihuanjifen-$jifen_jiage;
 $new_dongjiejifen=$dongjiejifen+$jifen_jiage;
 $keyong_money=$money-$suoding_money;
 $keyong_jifen=$duihuanjifen-$suoding_jifen;
 $riqi=date('Y-m-d H:i:s');
 $kaiguan=$this->caigou_mod->kg();
 $this->assign('kaiguan',$kaiguan);
 
if($store_row['store_id']=='')
{
$this->show_message('meiyoukaidian','back_list','index.php?app=member');
return;
}

    if($user_id==$gong_id)
	{
	$this->show_message('bunengcaigou','back_list','index.php?act=gonghuo&keyword=&app=search');
	return;
	}
	

	if($yu_kucun<$num)
	{
		$this->show_warning('nindekucunbuzu');
	    return;
	}

	if($_POST)
	{
	
	if(empty($num))
	{
		$this->show_warning('tianxiecaigou');
	    return;
	}
	
	$bb=explode(',',$lev['level']);
	if (!in_array(1,$bb))//若是免费商家，则交定金
	{
	    if($zhifufangshi=="xianjinzhifu")
		{
			if($jiage>$keyong_money)
			{
				$this->show_warning('nindeyuebuzu');
				return;
			}
		}
		else
		{
			if($jifen_jiage>$keyong_jifen)
			{
				$this->show_warning('nindejifenbuzu');
				return;
			}
		}	
	
	
		$add_caigou=array(
		'gong_id'=>$gong_id,//供货人的用户id
		'gong_name'=>$gong_name,//供货人的用户名
		'cai_id'=>$user_id,//采购人的用户id
		'cai_name'=>$user_name,//采购人的用户名
		'gh_id'=>$gh_id,// 供货id
		'num'=>$num,	
		'goods_name'=>$goods_name,	
		'lingshou_price'=>$lingshou_price,
		'jifen_price'=>$jifen_price,
		'city'=>$mcity,
		'riqi'=>$riqi,		
		'status'=>0,
		'chanpin'=>$chanpin,
		'zhifufangshi'=>$zhifufangshi																		
		);
		$this->caigou_mod->add($add_caigou); 
	
	if($zhifufangshi=="xianjinzhifu")
	{
	 //添加my_moneylog日志
		$log_text =$user_name.Lang::get('caigoushangpin').$jiage.Lang::get('yuan');
		$add_mymoneylog=array(
		'user_id'=>$user_id,
		'user_name'=>$user_name,
		'add_time'=>time(),
		'money_dj'=>$jiage,		

		'money'=>'-'.$jiage,
		'log_text'=>$log_text,
		//'feilv'=>$txfeilv,
		'status'=>0,
		'riqi'=>$riqi,	
		'type'=>14,	
		'city'=>$mcity,
		'dq_money'=>$new_money,
		'dq_money_dj'=>$new_money_dj,
		'dq_jifen'=>$duihuanjifen,
		'dq_jifen_dj'=>$dongjiejifen																			
		);
		

		//更新用户moneylog日志
		$beizhu =Lang::get('gonghuoshang');
		$beizhu=str_replace('{1}',$gong_name,$beizhu);
		$beizhu=str_replace('{2}',$gh_id,$beizhu);
	$addlog=array(
		'money_dj'=>$jiage,
		'money'=>'-'.$jiage,
		'time'=>$riqi,
		'user_name'=>$user_name,
		'user_id'=>$user_id,
		'zcity'=>$mcity,
		'type'=>9,
		's_and_z'=>2,
		'beizhu'=>$beizhu,
		'dq_money'=>$new_money,
		'dq_money_dj'=>$new_money_dj,
		'dq_jifen'=>$duihuanjifen,
		'dq_jifen_dj'=>$dongjiejifen
	   );
	   $money_new=array(
	 	'money'=>$new_money,
		'money_dj'=>$new_money_dj
		);
   }
   else
   {
   //添加my_moneylog日志
		$log_text =$user_name.Lang::get('caigoushangpin').$jifen_jiage.Lang::get('jifen');
		$add_mymoneylog=array(
		'user_id'=>$user_id,
		'user_name'=>$user_name,
		'add_time'=>time(),
		'dongjiejifen'=>$jifen_jiage,		
		'duihuanjifen'=>'-'.$jifen_jiage,
		'log_text'=>$log_text,
		//'feilv'=>$txfeilv,
		'status'=>0,
		'riqi'=>$riqi,	
		'type'=>14,	
		'city'=>$mcity,
		'dq_money'=>$money,
		'dq_money_dj'=>$money_dj,
		'dq_jifen'=>$new_duihuanjifen,
		'dq_jifen_dj'=>$new_dongjiejifen																			
		);
		

		//更新用户moneylog日志
		//$beizhu =$user_name.Lang::get('caigoushangpin').$jifen_jiage.Lang::get('jifen');
		$beizhu =Lang::get('gonghuoshang');
		$beizhu=str_replace('{1}',$gong_name,$beizhu);
		$beizhu=str_replace('{2}',$gh_id,$beizhu);
	$addlog=array(
		'jifen_dj'=>$jifen_jiage,
		'jifen'=>'-'.$jifen_jiage,
		'time'=>$riqi,
		'user_name'=>$user_name,
		'user_id'=>$user_id,
		'zcity'=>$mcity,
		'type'=>9,
		's_and_z'=>2,
		'beizhu'=>$beizhu,
		'dq_money'=>$money,
		'dq_money_dj'=>$money_dj,
		'dq_jifen'=>$new_duihuanjifen,
		'dq_jifen_dj'=>$new_dongjiejifen
	   );
	   $money_new=array(
		'duihuanjifen'=>$new_duihuanjifen,
		'dongjiejifen'=>$new_dongjiejifen
		);
   
   }	
	 $this->my_moneylog_mod->add($add_mymoneylog);
	// $this->moneylog_mod->add($addlog);
	//更新用户money表
	$this->my_money_mod->edit('user_id='.$user_id,$money_new);
	
	
		$beizhu=Lang::get('yicaigou');	
		$beizhu=str_replace('{1}',$gong_name,$beizhu);
		$add_notice=array(
			'from_id'=>0,
			'to_id'=>$gong_id,
			'content'=>$beizhu,  
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3,
			);
					
		$this->message_mod->add($add_notice);	
	
	}
	else
	{
		$add_caigou=array(
		'gong_id'=>$gong_id,//供货人的用户id
		'gong_name'=>$gong_name,//供货人的用户名
		'cai_id'=>$user_id,//采购人的用户id
		'cai_name'=>$user_name,//采购人的用户名
		'gh_id'=>$gh_id,// 供货id
		'num'=>$num,	
		'goods_name'=>$goods_name,	
		'lingshou_price'=>$lingshou_price,
		'jifen_price'=>$jifen_price,
		'city'=>$mcity,
		'riqi'=>$riqi,		
		'status'=>0,
		'chanpin'=>$chanpin,
		'zhifufangshi'=>$zhifufangshi																		
		);
		$this->caigou_mod->add($add_caigou); 
		
		$beizhu=Lang::get('yicaigou');	
		$beizhu=str_replace('{1}',$gong_name,$beizhu);
		$add_notice=array(
			'from_id'=>0,
			'to_id'=>$gong_id,
			'content'=>$beizhu,  
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3,
			);
					
		$this->message_mod->add($add_notice);		
	}
	$this->show_message('caigou','','index.php?app=my_theme&act=my_caigou');
    }
    else
    {	
	
	 $kaiguan=$this->gonghuo_mod->kg();
	 $huo=$this->gonghuo_mod->getRow("select * from ".DB_PREFIX."gonghuo where gh_id = '$gonghuo_id' limit 1");
        $this->assign('kaiguan',$kaiguan);
		$this->assign('huo',$huo);
        $this->assign('guanggaowei', $this->guanggaowei(3,10));
        $this->assign('last_caigou', $this->_last_caigou(6));
        $this->display('caigou.html');
    }
	}
	
	function gh_xiangqing()
	{
	$this->gonghuo_mod=& m('gonghuo');
	$gh_id = empty($_GET['id']) ? 0 : $_GET['id'];
      $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
    $this->assign('build_editor', $this->_build_editor(array('name' => 'beizhu')));
	 $this->_curlocal(array(array('text' => Lang::get('woyaocaigou'))));
            $find_data     = $this->gonghuo_mod->find($gh_id);
            if (empty($find_data))
            {
                $this->show_warning('meiyoucishangpin');

                return;
            }
            $gonghuo    =   current($find_data);
			$user_id=$gonghuo['user_id'];
			$xinxi=$this->gonghuo_mod->getRow("select * from ".DB_PREFIX."gonghuo_xinxi where user_id = '$user_id' limit 1");
            if ($gonghuo['ziliao'])
            {
                $gonghuo['ziliao']  =   dirname(site_url()) . "/" . $gonghuo['ziliao'];
            }
			 if ($gonghuo['chanpin'])
            {
                $gonghuo['chanpin']  =   dirname(site_url()) . "/" . $gonghuo['chanpin'];
            }
			 if ($gonghuo['changjia'])
            {
                $gonghuo['changjia']  =   dirname(site_url()) . "/" . $gonghuo['changjia'];
            }
			 if ($gonghuo['jigou'])
            {
                $gonghuo['jigou']  =   dirname(site_url()) . "/" . $gonghuo['jigou'];
            }
			 if ($gonghuo['shuiwu'])
            {
                $gonghuo['shuiwu']  =   dirname(site_url()) . "/" . $gonghuo['shuiwu'];
            }
			 if ($gonghuo['zhizhao'])
            {
                $gonghuo['zhizhao']  =   dirname(site_url()) . "/" . $gonghuo['zhizhao'];
            }
            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('gonghuo', $gonghuo);
			$this->assign('xinxi', $xinxi);
			 $this->assign('guanggaowei', $this->guanggaowei(3,10));
        $this->assign('last_caigou', $this->_last_caigou(6));
            $this->display('gh_xiangqing.html');
	}
	
	function _last_caigou($_num)
    {
	$url=$_SERVER['HTTP_HOST'];
	$this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$shenhe=Lang::get('shenhetongguo');
        $this->caigou_mod =& m('caigou');
        $data = $this->caigou_mod->find(array(
            'fields' => '*',
            'conditions' => "city='$city_id' and status=1",
            'order' => 'riqi DESC',
            'limit' => $_num,
        ));
   
        return $data;
    }
		
function guanggaowei($_num,$type)
    {
	//$url=$_SERVER['HTTP_HOST'];
	$this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$time=date('Y-m-d H:i:s');
	 $this->adv_mod =& m('adv');
	$advs=$this->adv_mod->getAll("select * from ".DB_PREFIX."adv where type = '$type'");
	
        $data = $this->adv_mod->find(array(
            //'join' => 'be_join,belong_goods',
            'fields' => '*',
            'conditions' => "adv_city='$city_id' and type='$type' and start_time<='$time' and end_time>='$time'",
            'order' => 'riqi DESC',
            'limit' => $_num,
        ));
   
        return $data;
    }
	function xiaobao()
	{
		$this->xiaobaolog_mod=& m('xiaobaolog');
		$this->store_mod=& m('store');
		$this->my_money_mod=& m('my_money');
		$this->moneylog_mod=& m('moneylog');
		$user_id = $this->visitor->get('user_id');	
		$user_name = $this->visitor->get('user_name');	
		if($_POST)
		{
			$xiaobao_pay=trim($_POST['xiaobao_pay']);
			$xiaobao_pay=(int)($xiaobao_pay*100)/100;
			$row=$this->xiaobaolog_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1"); 
			$money=$row['money'];
			$money_dj=$row['money_dj'];
			$duihuanjifen=$row['duihuanjifen'];
			$dongjiejifen=$row['dongjiejifen'];
			if($money<$xiaobao_pay)
			{
				$this->show_warning('yuebuzu');
				return;
			}
			if($xiaobao_pay<100)
			{
				$this->show_warning('xiaobaobunengxiaoyu');
				return;
			}
			
			$notice = trim($_POST['notice']);
			if(empty($notice))
			{
				$this->show_warning('tongyixiaofeizhefuwu');
				return;
			}
			$new_money=$money-$xiaobao_pay;
			$new_moneydj=$money_dj+$xiaobao_pay;
			$result=$this->xiaobaolog_mod->getRow("select * from ".DB_PREFIX."store where store_id='$user_id' limit 1"); 
			$data=array(
			'store_id'=>$user_id,
			'store_name'=>$result['store_name'],
			'money'=>$xiaobao_pay,
			'riqi'=>date('Y-m-d H:i:s'),
			'city'=>$result['cityid']
			);
			$new_xiaobao=$result['xiaobao']+$xiaobao_pay;
			$new_xiaobao_pay=$result['xiaobao_pay']+$xiaobao_pay;
			$daa=array('xiaobao'=>$new_xiaobao,'xiaobao_pay'=>$new_xiaobao_pay);
			 $addlog=array(
			  'money_dj'=>'+'.$xiaobao_pay,//负数
			  'money'=>'-'.$xiaobao_pay,
			  'time'=>date('Y-m-d H:i:s'),
			  'user_name'=>$user_name,
			  'user_id'=>$user_id,
			  'zcity'=>$result['cityid'],
			  'type'=>43,
			  's_and_z'=>1,
			  //'beizhu'=>$beizhu,
			  'dq_money'=>$new_money,//扣除提现的金额
			  'dq_money_dj'=>$new_moneydj,
			  'dq_jifen'=>$duihuanjifen,
			  'dq_jifen_dj'=>$dongjiejifen,	
		  );
		   $this->moneylog_mod->add($addlog);
			$this->xiaobaolog_mod->add($data);
			$this->store_mod->edit('store_id='.$user_id,$daa);
			$this->my_money_mod->edit('user_id='.$user_id,array('money'=>$new_money,'money_dj'=>$new_moneydj));
			$this->show_message('xiaobaochenggong','','index.php?app=member');
			
		}
		else
		{
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('xiaobao')
                         );
        /* 当前用户中心菜单 */
	    $this->assign('page_title',Lang::get('member_center'). ' - ' .Lang::get('xiaobao').' - '.Lang::get('zaixianchongzhi'));
        $this->_curitem('xiaobao');		
        $my_money=$this->xiaobaolog_mod->getRow("select * from ".DB_PREFIX."xiaobaolog where store_id='$user_id' limit 1");
		
		$cityrow=$this->jiekuan_mod->get_cityrow();
		$cityid=$cityrow['city_id']; 
        $str=file_get_contents('./data/jiekuanhetong/xiaobaoxieyi'.$cityid.'.htm');
		//$str=iconv('UTF-8','GBK',$str);
		$this->assign('str',$str);
		$this->display('xiaobaolog.html');
		}
	}
	
	
}

?>
