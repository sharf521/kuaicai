<?php

/**
 *    Desc
 *
 * @author    Garbin
 * @usage    none
 */
class MemberApp extends MemberbaseApp
{
    var $_feed_enabled = false;

    function __construct()
    {
        $this->MemberApp();
    }

    function MemberApp()
    {
        parent::__construct();
        $ms =& ms();
        $this->_feed_enabled = $ms->feed->feed_enabled();
        $this->assign('feed_enabled', $this->_feed_enabled);
    }

    function index()
    {
        $this->my_money_mod =& m('my_money');
        $this->member_mod =& m('member');
        $user_id = $this->visitor->get('user_id');
        $canshu = $this->member_mod->can();
        $cityrow = $this->member_mod->get_cityrow();
        $usid = DeCode($user_id, 'E');

        //$bian=$this->get_p2p_account($user_id);
        $my_money = $this->my_money_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id='$user_id'");
        $jie = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "jiekuan where user_id = '$user_id' order by id desc limit 1");
        $sto = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "store where store_id = '$user_id' limit 1");
        $riqi1 = time(date('Y-m-d')) - 15 * 3600 * 24;
        $orde = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "order where status = 30 and ship_time <= $riqi1 limit 1");
        $riqi2 = time(date('Y-m-d')) - 3 * 3600 * 24;
        $ord = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "order where status = 11 and add_time < $riqi2 limit 1");

        $this->assign('jie', $jie);
        $this->assign('usid', $usid);
        $this->assign('ord', $ord);
        $this->assign('orde', $orde);
        $this->assign('sto', $sto);
        //$this->assign('bian',$bian);
        $this->assign('cityrow', $cityrow);
        foreach ($my_money as $key => $my) {
            $my_money[$key]['zengjin'] = round($my['zengjin'] / $canshu['jifenxianjin'], 2);
            $dengji = $my['t'];
            $le = dengji($dengji);
            $jk = jiekuan($dengji);
        }
        $this->assign('my_money', $my_money);
        $qiandao = $this->my_money_mod->getRow("select riqi,times,status from " . DB_PREFIX . "qiandao where user_id='$user_id' limit 1");
        $riqi = date('Y-m-d');
        $mfbb = $this->member_mod->getRow("select fbb,daxiaozhuo,level,vip from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
        $this->assign('mfbb', $mfbb);
        $this->assign('qiandao', $qiandao);
        $this->assign('riqi', $riqi);
        $eve = $mfbb['level'];
        $bb = explode(',', $eve);
        if (in_array(1, $bb)) {
            $fufei = 1;
            $this->assign('fufei', $fufei);
        }

        //$le=level($eve);

        $this->assign('le', $le);
        $this->assign('dengji', $dengji);
        $this->assign('jk', $jk);

        /* 清除新短消息缓存 */
        $cache_server =& cache_server();
        $cache_server->delete('new_pm_of_user_' . $this->visitor->get('user_id'));

        $user = $this->visitor->get();
        $user_mod =& m('member');
        $info = $user_mod->get_info($user['user_id']);
        if (empty($info['portrait']))
            $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
        else
            $user['portrait'] = $info['portrait'];
        $this->assign('user', $user);

        /* 店铺信用和好评率 */
        if ($user['has_store']) {
            $store_mod =& m('store');
            $store = $store_mod->get_info($user['has_store']);
            $step = intval(Conf::get('upgrade_required'));
            $step < 1 && $step = 5;
            $store['credit_image'] = $this->_view->res_base . '/images/' . $store_mod->compute_credit($store['credit_value'], $step);
            $this->assign('store', $store);
            $this->assign('store_closed', STORE_CLOSED);
        }
        $goodsqa_mod = &m('goodsqa');
        $groupbuy_mod = &m('groupbuy');
        /* 买家提醒：待付款、待确认、待评价订单数 */
        $order_mod =& m('order');
        $sql1 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE buyer_id = '{$user['user_id']}' AND status = '" . ORDER_PENDING . "'";
        $sql2 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE buyer_id = '{$user['user_id']}' AND status = '" . ORDER_SHIPPED . "'";
        $sql3 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE buyer_id = '{$user['user_id']}' AND status = '" . ORDER_FINISHED . "' AND evaluation_status = 0";
        $sql4 = "SELECT COUNT(*) FROM {$goodsqa_mod->table} WHERE user_id = '{$user['user_id']}' AND reply_content !='' AND if_new = '1' ";
        $sql5 = "SELECT COUNT(*) FROM " . DB_PREFIX . "groupbuy_log AS log LEFT JOIN {$groupbuy_mod->table} AS gb ON gb.group_id = log.group_id WHERE log.user_id='{$user['user_id']}' AND gb.state = " . GROUP_CANCELED;
        $sql6 = "SELECT COUNT(*) FROM " . DB_PREFIX . "groupbuy_log AS log LEFT JOIN {$groupbuy_mod->table} AS gb ON gb.group_id = log.group_id WHERE log.user_id='{$user['user_id']}' AND gb.state = " . GROUP_FINISHED;
        $buyer_stat = array(
            'pending' => $order_mod->getOne($sql1),
            'shipped' => $order_mod->getOne($sql2),
            'finished' => $order_mod->getOne($sql3),
            'my_question' => $goodsqa_mod->getOne($sql4),
            'groupbuy_canceled' => $groupbuy_mod->getOne($sql5),
            'groupbuy_finished' => $groupbuy_mod->getOne($sql6),
        );
        $sum = array_sum($buyer_stat);
        $buyer_stat['sum'] = $sum;
        $this->assign('buyer_stat', $buyer_stat);

        /* 卖家提醒：待处理订单和待发货订单 */
        if ($user['has_store']) {

            $sql7 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE seller_id = '{$user['user_id']}' AND status = '" . ORDER_SUBMITTED . "'";
            $sql8 = "SELECT COUNT(*) FROM {$order_mod->table} WHERE seller_id = '{$user['user_id']}' AND status = '" . ORDER_ACCEPTED . "'";
            $sql9 = "SELECT COUNT(*) FROM {$goodsqa_mod->table} WHERE store_id = '{$user['user_id']}' AND reply_content ='' ";
            $sql10 = "SELECT COUNT(*) FROM {$groupbuy_mod->table} WHERE store_id='{$user['user_id']}' AND state = " . GROUP_END;
            $seller_stat = array(
                'submitted' => $order_mod->getOne($sql7),
                'accepted' => $order_mod->getOne($sql8),
                'replied' => $goodsqa_mod->getOne($sql9),
                'groupbuy_end' => $goodsqa_mod->getOne($sql10),
            );

            $this->assign('seller_stat', $seller_stat);
        }
        /* 卖家提醒： 店铺等级、有效期、商品数、空间 */
        if ($user['has_store']) {
            $store_mod =& m('store');
            $store = $store_mod->get_info($user['has_store']);

            $grade_mod = &m('sgrade');
            $grade = $grade_mod->get_info($store['sgrade']);

            $goods_mod = &m('goods');
            $goods_num = $goods_mod->get_count_of_store($user['has_store']);
            $uploadedfile_mod = &m('uploadedfile');
            $space_num = $uploadedfile_mod->get_file_size($user['has_store']);
            $sgrade = array(
                'grade_name' => $grade['grade_name'],
                'add_time' => empty($store['end_time']) ? 0 : sprintf('%.2f', ($store['end_time'] - gmtime()) / 86400),
                'goods' => array(
                    'used' => $goods_num,
                    'total' => $grade['goods_limit']),
                'space' => array(
                    'used' => sprintf("%.2f", floatval($space_num) / (1024 * 1024)),
                    'total' => $grade['space_limit']),
            );
            $this->assign('sgrade', $sgrade);

        }
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), url('app=member'),
            LANG::get('overview'));

        /* 当前用户中心菜单 */
        $this->_curitem('overview');
        $this->assign('page_title', Lang::get('member_center'));


        $this->display('member.index.html');
    }


    /**
     *    注册一个新用户
     *
     * @author    Garbin
     * @return    void
     */
    function register()
    {
        $this->member_mod =& m('member');
        $weiboid = ecm_getcookie('weiboid');
        $openid = ecm_getcookie('openid');
        if (!empty($openid)) {
            $qq = $this->member_mod->getRow("select user_id from " . DB_PREFIX . "member where openid = '$openid' limit 1");
            if ($qq) {
                ecm_setcookie('openid', "");
            }
        }
        if (!empty($weiboid)) {
            $weibo = $this->member_mod->getRow("select user_id from " . DB_PREFIX . "member where weiboid = '$weiboid' limit 1");
            if ($weiboid) {
                ecm_setcookie('weiboid', "");
            }
        }

        $this->assign('weiboid', $weiboid);
        $this->assign('openid', $openid);
        //$tuijian = empty($_GET['id']) ? null : trim($_GET['id']);
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);//推荐人的id
        //echo $id;

        $us = $this->member_mod->getRow("select user_name from " . DB_PREFIX . "member where user_id = '$id' limit 1");
        $this->assign('us', $us);
        $user_name = $us['user_name'];//推荐人的用户名
        //echo $user_name;


        // $url=$_SERVER['HTTP_HOST'];//获得当前网址
        /*echo $url;*/
        $this->_city_mod =& m('city');
        $cityrow = $this->_city_mod->get_cityrow();
        $city_id = $cityrow['city_id'];


        if ($this->visitor->has_login) {
            $this->show_warning('has_login');

            return;
        }
        if (!IS_POST) {
            if (!empty($_GET['ret_url'])) {
                $ret_url = trim($_GET['ret_url']);
            } else {
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $ret_url = $_SERVER['HTTP_REFERER'];
                } else {
                    $ret_url = SITE_URL . '/index.php';
                }
            }

            $this->assign('ret_url', rawurlencode($ret_url));
            $this->_curlocal(LANG::get('user_register'));
            $this->assign('page_title', Lang::get('user_register') . ' - ' . Conf::get('site_title'));

            if (Conf::get('captcha_status.register')) {
                $this->assign('captcha', 1);
            }

            /* 导入jQuery的表单验证插件 */
            $this->import_resource('jquery.plugins/jquery.validate.js');
            $this->display('member.register.html');
        } else {
            if (!$_POST['agree']) {
                $this->show_warning('agree_first');

                return;
            }
            if (Conf::get('captcha_status.register') && base64_decode($_SESSION['captcha']) != strtolower($_POST['captcha'])) {
                $this->show_warning('captcha_failed');
                return;
            }
            if ($_POST['password'] != $_POST['password_confirm']) {
                /* 两次输入的密码不一致 */
                $this->show_warning('inconsistent_password');
                return;
            }

            /* 注册并登录 */
            $user_name = trim($_POST['user_name']);
            $yaoqing_id = trim($_POST['yaoqing_id']);
            $password = $_POST['password'];
            $email = trim($_POST['email']);
            $owner_card = trim($_POST['owner_card']);
            /*$city     = trim($_POST['city']);*/
            $city = $city_id;
            $passlen = strlen($password);
            $user_name_len = strlen($user_name);

            if ($user_name_len < 3 || $user_name_len > 25) {
                $this->show_warning('user_name_length_error');

                return;
            }
            if ($passlen < 6 || $passlen > 20) {
                $this->show_warning('password_length_error');

                return;
            }
            if (!is_email($email)) {
                $this->show_warning('email_error');

                return;
            }

            if ($yaoqing_id != '') {
                $member_row = $this->member_mod->getRow("select user_name from " . DB_PREFIX . "member where user_name = '$yaoqing_id' limit 1");
                $yaoqingid = $member_row['user_name'];
                if ($yaoqingid == '') {
                    $this->show_warning('gaiyonghubucunzai');
                    return;
                }
            }

            /*
			if (empty($owner_card))
            {
                $this->show_warning('shenfenzhengbunengweikong');

                return;
            }
			include_once(ROOT_PATH. '/includes/idcheck.class.php');
			$chk=new IDCheck($owner_card);
			if(($chk->Part())==False)
			{
				$this->show_warning('shurushenfenzheng');
				return;
			}*/

            $ms =& ms(); //连接用户中心
            $user_id = $ms->user->register($user_name, $password, $email, $owner_card, $city, $yaoqing_id, array(), $web_id, $weiboid, $openid);

            if (!$user_id) {
                $this->show_warning($ms->user->get_error());

                return;
            }
            $this->_hook('after_register', array('user_id' => $user_id));
            //登录
            $this->_do_login($user_id);
            //更新推荐人的用户资金
            $this->canshu_mod =& m('canshu');
            $this->message_mod =& m('message');
            $can = $this->canshu_mod->can();
            $jiang = $can['tuijianjiangli'];//奖励的荣誉积分

            $mber = $this->member_mod->getRow("select * from " . DB_PREFIX . "member where user_id='$user_id' limit 1");//新注册的用户id

            $tuijianren = $mber['yaoqing_id'];//推荐人
            $xin_user_name = $mber['user_name'];//新注册的用户名
            $riqi = date('Y-m-d H:i:s');
            if ($tuijianren != '' && $jiang != 0)  //推荐奖励
            {
                $this->_money_mod =& m('my_money');
                $this->qiandao_mod =& m('qiandao');
                $this->qiandao_log_mod =& m('qiandao_log');
                $tuijian = $this->_money_mod->getRow("select * from " . DB_PREFIX . "member where user_name='$tuijianren' limit 1");
                $tjuser_id = $tuijian['user_id'];//推荐人的用户id
                $tjcity = $tuijian['city'];

                $qiandao = $this->_money_mod->getRow("select * from " . DB_PREFIX . "qiandao where user_id='$tjuser_id' limit 1");
                $times = $qiandao['times'];
                $new_times = $times + $jiang;

                if (empty($qiandao)) {
                    $qd = array(
                        'user_id' => $tjuser_id,
                        'riqi' => $riqi,
                        'times' => $new_times
                    );
                    $this->qiandao_mod->add($qd);
                } else {
                    $qd = array('times' => $new_times);
                    $this->qiandao_mod->edit('user_id=' . $tjuser_id, $qd);
                }


//添加qiandao_log日志

                $beizhu = Lang::get('huodejiangli') . $jiang . Lang::get('rongyujifen');
                $add_mylog = array(
                    'user_id' => $tuijian['user_id'],
                    'user_name' => $tuijianren,
                    'jifen' => $jiang,
                    'beizhu' => $beizhu,
                    'riqi' => $riqi,
                    'city' => $tjcity
                );
                $this->qiandao_log_mod->add($add_mylog);

                $notice = Lang::get('tuijianrongyujifen');
                $notice = str_replace('{1}', $tuijianren, $notice);
                $notice = str_replace('{2}', $xin_user_name, $notice);
                $notice = str_replace('{3}', $jiang, $notice);

                $add_notice = array(
                    'from_id' => 0,
                    'to_id' => $tuijian['user_id'],
                    'content' => $notice,
                    'add_time' => gmtime(),
                    'last_update' => gmtime(),
                    'new' => 1,
                    'parent_id' => 0,
                    'status' => 3,
                );
                $this->message_mod->add($add_notice);
            }

            #TODO 可能还会发送欢迎邮件

            $this->show_message('register_successed',
                // 'back_before_register', rawurldecode($_POST['ret_url']),
                'enter_member_center', 'index.php?app=member'
            //'apply_store', 'index.php?app=apply'
            );
        }
    }


    /**
     *    检查用户是否存在
     *
     * @author    Garbin
     * @return    void
     */
    function check_user()
    {
        $user_name = empty($_GET['user_name']) ? null : trim($_GET['user_name']);

        if (!$user_name) {
            echo ecm_json_encode(false);
            return;
        }

        $ms =& ms();


        echo ecm_json_encode($ms->user->check_username($user_name));
        exit();
    }

    function check_email()
    {
        $email = empty($_GET['email']) ? null : trim($_GET['email']);
        if (!$email) {
            echo ecm_json_encode(false);

            return;
        }

        $ms =& ms();
        echo ecm_json_encode($ms->user->check_email($email));

    }

    /**
     *    修改基本信息
     *
     * @author    Hyber
     * @usage    none
     */
    function profile()
    {

        $user_id = $this->visitor->get('user_id');
        if (!IS_POST) {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('basic_information'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('basic_information');

            $ms =& ms();    //连接用户系统
            $edit_avatar = $ms->user->set_avatar($this->visitor->get('user_id')); //获取头像设置方式

            $model_user =& m('member');
            $profile = $model_user->get_info(intval($user_id));
            $profile['portrait'] = portrait($profile['user_id'], $profile['portrait'], 'middle');


            $this->assign('profile', $profile);
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));

            $ur = $_SERVER['HTTP_HOST'];
            $this->assign('ur', $ur);

            $this->assign('edit_avatar', $edit_avatar);
            $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('my_profile'));
            $this->display('member.profile.html');
        } else {
            $owner_card = $_POST['owner_card'];
            include_once(ROOT_PATH . '/includes/idcheck.class.php');
            $chk = new IDCheck($owner_card);
            if (($chk->Part()) == False) {
                $this->show_warning('shurushenfenzheng');
                return;
            }

            $data = array(
                'real_name' => $_POST['real_name'],
                'owner_card' => $_POST['owner_card'],
                'gender' => $_POST['gender'],
                'birthday' => $_POST['birthday'],
                'im_msn' => $_POST['im_msn'],
                'im_qq' => $_POST['im_qq'],
            );

            if (!empty($_FILES['portrait'])) {
                $portrait = $this->_upload_portrait($user_id);
                if ($portrait === false) {
                    return;
                }
                $data['portrait'] = $portrait;
            }

            $model_user =& m('member');
            $model_user->edit($user_id, $data);
            if ($model_user->has_error()) {
                $this->show_warning($model_user->get_error());

                return;
            }

            $this->show_message('edit_profile_successed');
        }
    }

    /**
     *    修改密码
     *
     * @author    Hyber
     * @usage    none
     */
    function password()
    {
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');
        $this->message_mod =& m('message');
        if (!IS_POST) {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('edit_password'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('edit_password');
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));
            $this->assign('page_title', Lang::get('user_center') . ' - ' . Lang::get('edit_password'));
            $this->display('member.password.html');
        } else {
            /* 两次密码输入必须相同 */
            $orig_password = $_POST['orig_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            if ($new_password != $confirm_password) {
                $this->show_warning('twice_pass_not_match');

                return;
            }
            if (!$new_password) {
                $this->show_warning('no_new_pass');

                return;
            }
            $passlen = strlen($new_password);
            if ($passlen < 6 || $passlen > 20) {
                $this->show_warning('password_length_error');
                return;
            }

            /* 修改密码 */
            $ms =& ms();    //连接用户系统
            $result = $ms->user->edit($this->visitor->get('user_id'), $orig_password, array(
                'password' => $new_password
            ));
            if (!$result) {
                /* 修改不成功，显示原因 */
                $this->show_warning($ms->user->get_error());

                return;
            }
            $content = Lang::get('denglu');
            $content = str_replace('{1}', $user_name, $content);
            $add_notice1 = array(
                'from_id' => 0,
                'to_id' => $user_id,
                'content' => $content,
                'add_time' => time(),
                'last_update' => time(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3,
            );
            $this->message_mod->add($add_notice1);
            $mem = $this->message_mod->getRow("select email from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
            $email = $mem['email'];
            $subject = Lang::get('mimaxiugai');
            $body = $content;
            $this->show_message('edit_password_successed', '', 'index.php?app=member&act=password');
            sendmail($subject, $body, $email);

        }
    }

    /**
     *    修改电子邮箱
     *
     * @author    Hyber
     * @usage    none
     */
    function email()
    {
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');
        $this->message_mod =& m('message');
        if (!IS_POST) {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('edit_email'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('edit_email');
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));
            $this->assign('page_title', Lang::get('user_center') . ' - ' . Lang::get('edit_email'));
            $this->display('member.email.html');
        } else {
            $this->user_mod =& m('member');
            $row_user = $this->user_mod->getAll("select email from " . DB_PREFIX . "member");

            $orig_password = $_POST['orig_password'];
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            if (!$email) {
                $this->show_warning('email_required');

                return;
            }
            if (!is_email($email)) {
                $this->show_warning('email_error');

                return;
            }

            foreach ($row_user as $key => $user) {
                if ($email == $user['email']) {
                    $this->show_warning('cunzai');
                    return;
                }
            }

            $ms =& ms();    //连接用户系统
            $result = $ms->user->edit($this->visitor->get('user_id'), $orig_password, array(
                'email' => $email
            ));
            if (!$result) {
                $this->show_warning($ms->user->get_error());

                return;
            }


            $content = Lang::get('youxiang');
            $content = str_replace('{1}', $user_name, $content);
            $add_notice1 = array(
                'from_id' => 0,
                'to_id' => $user_id,
                'content' => $content,
                'add_time' => gmtime(),
                'last_update' => gmtime(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3,
            );
            $this->message_mod->add($add_notice1);

            $this->show_message('edit_email_successed', '', 'index.php?app=member&act=email');
        }
    }

    /**
     * Feed设置
     *
     * @author Garbin
     * @param
     * @return void
     **/
    function feed_settings()
    {
        if (!$this->_feed_enabled) {
            $this->show_warning('feed_disabled');
            return;
        }
        if (!IS_POST) {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('feed_settings'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('feed_settings');
            $this->assign('page_title', Lang::get('user_center') . ' - ' . Lang::get('feed_settings'));

            $user_feed_config = $this->visitor->get('feed_config');
            $default_feed_config = Conf::get('default_feed_config');
            $feed_config = !$user_feed_config ? $default_feed_config : unserialize($user_feed_config);

            $buyer_feed_items = array(
                'store_created' => Lang::get('feed_store_created.name'),
                'order_created' => Lang::get('feed_order_created.name'),
                'goods_collected' => Lang::get('feed_goods_collected.name'),
                'store_collected' => Lang::get('feed_store_collected.name'),
                'goods_evaluated' => Lang::get('feed_goods_evaluated.name'),
                'groupbuy_joined' => Lang::get('feed_groupbuy_joined.name')
            );
            $seller_feed_items = array(
                'goods_created' => Lang::get('feed_goods_created.name'),
                'groupbuy_created' => Lang::get('feed_groupbuy_created.name'),
            );
            $feed_items = $buyer_feed_items;
            if ($this->visitor->get('manage_store')) {
                $feed_items = array_merge($feed_items, $seller_feed_items);
            }
            $this->assign('feed_items', $feed_items);
            $this->assign('feed_config', $feed_config);
            $this->display('member.feed_settings.html');
        } else {
            $feed_settings = serialize($_POST['feed_config']);
            $m_member = &m('member');
            $m_member->edit($this->visitor->get('user_id'), array(
                'feed_config' => $feed_settings,
            ));
            $this->show_message('feed_settings_successfully');
        }
    }

    /**
     *    三级菜单
     *
     * @author    Hyber
     * @return    void
     */
    function _get_member_submenu()
    {
        $submenus = array(
            array(
                'name' => 'basic_information',
                'url' => 'index.php?app=member&amp;act=profile',
            ),
            array(
                'name' => 'edit_password',
                'url' => 'index.php?app=member&amp;act=password',
            ),
            array(
                'name' => 'edit_email',
                'url' => 'index.php?app=member&amp;act=email',
            ),
        );
        if ($this->_feed_enabled) {
            $submenus[] = array(
                'name' => 'feed_settings',
                'url' => 'index.php?app=member&amp;act=feed_settings',
            );
        }

        return $submenus;
    }

    /**
     * 上传头像
     *
     * @param int $user_id
     * @return mix false表示上传失败,空串表示没有上传,string表示上传文件地址
     */
    function _upload_portrait($user_id)
    {
        $file = $_FILES['portrait'];
        if ($file['error'] != UPLOAD_ERR_OK) {
            return '';
        }
        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false) {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=member&amp;act=profile');
            return false;
        }
        $uploader->root_dir(ROOT_PATH);
        return $uploader->save('data/files/mall/portrait/' . ceil($user_id / 500), $user_id);
    }

    function qiandao()
    {

        $this->qiandao_mod =& m('qiandao');
        $this->qiandao_log_mod =& m('qiandao_log');
        $user_id = empty($_GET['user_id']) ? null : trim($_GET['user_id']);
        $riqi = date('Y-m-d');
        $qd = $this->qiandao_mod->getRow("select * from " . DB_PREFIX . "qiandao where user_id='$user_id' limit 1");
        $can = $this->qiandao_mod->can();
        $qiandao_jifen = $can['qiandao_jifen'];
        $reg_jifen = $can['reg_jifen'];
        $z_jifen = $qiandao_jifen + $reg_jifen;

        $mm = $this->qiandao_mod->getRow("select * from " . DB_PREFIX . "member where user_id='$user_id' limit 1");

        if ($qd['riqi'] == $riqi && $qd['status'] == 1) {
            $this->show_warning('ninyiqiandao');
            return;
        }

        /*if(empty($qd))
        {
        $dao=array(
            'user_id'=>$user_id,
            'riqi'=>$riqi,
            'times'=>$z_jifen
                 );
        $this->qiandao_mod->add($dao);
        }
        else
        {*/
        $times = $qd['times'];
        $new_times = $times + $qiandao_jifen;
        $dao = array(
            'user_id' => $user_id,
            'riqi' => $riqi,
            'times' => $new_times,
            'status' => 1
        );
        $this->qiandao_mod->edit('user_id=' . $user_id, $dao);
        /*	}*/

        $riqi1 = date('Y-m-d H:i:s');
        $beizhu = Lang::get('huoderongyu');
        $beizhu = str_replace('{1}', $qiandao_jifen, $beizhu);
        $add_mylog = array(
            'user_id' => $user_id,
            'user_name' => $mm['user_name'],
            'jifen' => $qiandao_jifen,
            'beizhu' => $beizhu,
            'riqi' => $riqi1,
            'city' => $mm['city']
        );
        $this->qiandao_log_mod->add($add_mylog);


        $this->show_message('qiandaochenggong',
            'fanhuiliebiao', 'index.php?app=member');
    }

    //购买套餐
    function goumaitaocan()
    {
        $this->my_money_mod =& m('my_money');
        $this->kaiguan_mod =& m('kaiguan');
        $this->canshu_mod =& m('canshu');
        $this->member_mod =& m('member');
        $this->my_webserv_mod =& m('my_webserv');
        $this->moneylog_mod =& m('moneylog');
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');
        $userrow = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
        $city = $userrow['city'];
        $us_money = $userrow['money'];
        $us_money_dj = $userrow['money_dj'];
        $duihuanjifen = $userrow['duihuanjifen'];
        $dongjiejifen = $userrow['dongjiejifen'];
        $suoding_money = $userrow['suoding_money'];
        $keyong_money = $us_money - $suoding_money;

        $riqi = date('Y-m-d H:i:s');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('taocan')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('taocan'));
        $this->_curitem('taocan');

        $this->assign('userrow', $userrow);
        $chaoji = Lang::get('chaoji');
        $huangguan = Lang::get('huangguan');
        $baijin = Lang::get('baijin');
        $jinpai = Lang::get('jinpai');
        $yinpai = Lang::get('yinpai');
        $jichu = Lang::get('jichu');
        $tongpai = Lang::get('tongpai');
        $diguo = Lang::get('diguo');
        $qiye = Lang::get('qiye');
        $longtou = Lang::get('longtou');
        $fuwuzhongxin = Lang::get('fuwuzhongxin');
        $fuwuzhan = Lang::get('fuwuzhan');

        if ($_POST) {
            $tuijianren = trim($_POST['tuijianren']);
            $lishuren = trim($_POST['lishuren']);
            $buytype = (int)($_POST['buytype']);
            $buytype_dj = array(9300000, 3300000, 3500000, 39000, 14000, 10600, 7800, 2820, 28200, 2820);
            $dongjie = $buytype_dj[$buytype];

            /*if($buytype=='')
            {
                    $this->show_warning('xuanzetaocan');
                    return;
            }
            else
            {*/
            $ispayprice = 0;
            $ispaydingjin = trim(($_POST['ispay']));
            /*}*/
            if (!empty($_POST['tuijianren'])) {
                $row = $this->my_money_mod->getRow("select user_id from " . DB_PREFIX . "my_webserv where user_name='$tuijianren' limit 1");
                if (empty($row)) {
                    $this->show_warning('tuijianbucunzai');
                    return;
                } else {
                    $tj_userid = $row['user_id'];
                }
                $row = null;
            }

            $lishuren = trim($_POST['lishuren']);//用户名
            if (!empty($lishuren)) {
                $row = $this->my_money_mod->getRow("select user_id from " . DB_PREFIX . "my_webserv where user_name='$lishuren'");
                if (empty($row)) {
                    $this->show_warning('lishubucunzai');
                    return;
                } else {
                    $ls_userid = $row['user_id'];
                }
                $row = null;
                $row = $this->my_money_mod->getRow("select count(*) as count from " . DB_PREFIX . "member where lishuid='$ls_userid'");
                if ($row['count'] >= 2) {
                    $lishu = Lang::get('zhinengyouliangge');
                    $lishu = str_replace('{1}', $lishuren, $lishu);
                    $this->show_warning($lishu);
                    return;
                }
                $row = null;
            }


            if ($keyong_money < $dongjie) {
                $this->show_warning('nindezijinbuzu');
                //$this->show_message('nindezijinbuzu',
                //'fanhuiliebiao',    'index.php?app=member&act=goumaitaocan');
                return;
            }

            $da = array('tuijianid' => $tj_userid, 'lishuid' => $ls_userid);
            $this->member_mod->edit('user_id=' . $user_id, $da);

            $riqi = date('Y-m-d H:i:s');
            $name = trim($_POST['name']);
            $price = trim($_POST['price']);
            //$ispayprice= trim($_POST['ispayprice']);
            //$ispaydingjin = trim($_POST['ispaydingjin']);
            $data = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'buytype' => $buytype,
                'ispayprice' => $ispayprice,
                'ispaydingjin' => $ispaydingjin,
                'status' => 0,
                'createdate' => $riqi,
                'city' => $city
            );
            $this->my_webserv_mod->add($data);
            $beizhu = $user_name . Lang::get('shenqinggoumai');
            $arr = array(
                'money' => '-' . $dongjie,
                'jifen' => 0,
                'money_dj' => $dongjie,
                'jifen_dj' => 0,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'type' => 29,
                's_and_z' => 2,
                'time' => date('Y-m-d H:i:s'),
                'zcity' => $city,
                'dq_money' => $us_money - $dongjie,
                'dq_money_dj' => $us_money_dj + $dongjie,
                'dq_jifen' => $duihuanjifen,
                'dq_jifen_dj' => $dongjiejifen,
                'beizhu' => $beizhu
            );
            $this->moneylog_mod->add($arr);
            $new_user_money = $us_money - $dongjie;
            $new_user_moneydj = $us_money_dj + $dongjie;
            $da = array(
                'money' => $new_user_money,
                'money_dj' => $new_user_moneydj
            );
            $this->my_money_mod->edit('user_id=' . $user_id, $da);

            $this->show_message('goumaichenggong',
                'fanhuiliebiao', 'index.php?app=member');

        } else {

            $taocan_name = array(
                array('buytype' => 0, 'name' => $diguo, 'price' => 1550),
                array('buytype' => 1, 'name' => $qiye, 'price' => 550),
                array('buytype' => 2, 'name' => $longtou, 'price' => 350),
                array('buytype' => 3, 'name' => $baijin, 'price' => 8),
                array('buytype' => 4, 'name' => $jinpai, 'price' => 2.75),
                array('buytype' => 5, 'name' => $yinpai, 'price' => 2.2),
                array('buytype' => 6, 'name' => $jichu, 'price' => 1.58),
                array('buytype' => 7, 'name' => $tongpai, 'price' => 0.58),
                array('buytype' => 8, 'name' => $fuwuzhongxin, 'price' => 5.8),
                array('buytype' => 9, 'name' => $fuwuzhan, 'price' => 0.58),
            );


            $row = $this->my_webserv_mod->getRow("select id from " . DB_PREFIX . "my_webserv where user_id='$user_id' limit 1");
            $this->assign('row', $row);
            $this->assign('buytype_dj', $buytype_dj);

            $this->assign('taocan_name', $taocan_name);
            $this->display('goumaitaocan.html');
        }

    }

    //成长积分
    function chengzhangjifen()
    {

        $this->moneylog_mod =& m('moneylog');
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');


        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('shourulog')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shourulog'));
        $this->_curitem('shourulog');


        $page = $this->_get_page();

        $userrow = $this->moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and type=104",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));
        $page['item_count'] = $this->moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('userrow', $userrow);
        $this->display('chengzhangjifen.html');
    }

    //推荐奖励
    function tjjl()
    {

        $this->moneylog_mod =& m('moneylog');
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');


        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('shourulog')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shourulog'));
        $this->_curitem('shourulog');


        $page = $this->_get_page();

        $userrow = $this->moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and (type=38 or type=39 or type=40)",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));
        $page['item_count'] = $this->moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('userrow', $userrow);
        $this->display('tuijianjiangli.html');

    }

    //交易收入
    function jiaoyi_log()
    {

        $this->moneylog_mod =& m('moneylog');
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');


        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('shourulog')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shourulog'));
        $this->_curitem('shourulog');


        $page = $this->_get_page();

        $userrow = $this->moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and (type=16 or type=17)",
            'limit' => 10,
            'order' => "id desc",
            'count' => true,
        ));
        foreach ($userrow as $key => $val) {
            $userrow[$key]['order_sn'] = substr($val['beizhu'], 7, 100);
        }

        //$page['item_count'] = $this->moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('userrow', $userrow);
        $this->display('jiaoyi_log.html');
    }


    function my_jiekuan()
    {
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');
        $this->assign('user_name', $user_name);
        $this->jiekuan_mod =& m('jiekuan');
        $canshu = $this->jiekuan_mod->can();
        $mem = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "member where user_id = '$user_id' limit 1");
        $city = $mem['city'];
        $level = $mem['level'];
        $bb = explode(',', $level);
        $jie = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "jiekuan where user_id = '$user_id' order by id desc ");

        $jie['daoqi_time'] = substr($jie['jieshu_time'], 0, 10);
        $jie['start_time1'] = substr($jie['start_time'], 0, 10);

        $jie['yh'] = $jie['money_j'] - $jie['money_j'] * 1 / 10;
        $jie['baozheng'] = $jie['money_j'] * 1 / 10;
        $this->assign('jie', $jie);

        $this->assign('mem', $mem);

        $my_money = $this->jiekuan_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id='$user_id'");
        foreach ($my_money as $key => $my) {

            $my_money[$key]['zengjin'] = round($my['zengjin'] / $canshu['jifenxianjin'], 2);
            $dengji = $my['t'];
            $le = dengji($dengji);
            $jk = jiekuan($dengji);

        }
        $this->assign('my_money', $my_money);
        $this->assign('jk', $jk);


        if ($_POST) {

            $name = trim($_POST['name']);
            if (empty($name)) {
                $this->show_message('jinxingwanshan', 'go_back', 'index.php?app=member&act=profile');
                return;
            }
            $money_j = trim($_POST['money_j']);//借款的钱
            $money_j = (int)($money_j * 100) / 100;
            $kejie = Lang::get('nindekejie');
            $kejie = str_replace('{1}', $jk, $kejie);
            if ($money_j > $jk) {
                $this->show_warning($kejie);
                return;
            }
            if ($money_j <= 0) {
                $this->show_warning($jiekuanjinebuneng);
                return;
            }
            $time = (int)$_POST['time'];
            if ($time == 0) {
                $this->show_warning('jiekuanqixianbuneng');
                return;
            }

            $bank = trim($_POST['bank']);
            $address = trim($_POST['address']);
            $danwei = trim($_POST['danwei']);
            $lxfs1 = trim($_POST['lxfs1']);
            $lxfs2 = trim($_POST['lxfs2']);
            $bank_hao = trim($_POST['bank_hao']);
            $beizhu = trim($_POST['beizhu']);
            $image1 = trim($_POST['image_1']);
            $image2 = trim($_POST['image_2']);
            if ($_POST['rate'] < 2) {
                $this->show_warning('bunengxiaoyu');
                return;
            }

            $notice = trim($_POST['notice']);
            if (empty($notice)) {
                $this->show_warning('jiekuantiaokuan');
                return;
            }


            $rate = ((float)$_POST['rate']) / 100;
            $isday = (int)$_POST['isday'];
            if ($isday == 1) {
                $rate = $rate / 30;
            }

            $lixi = ceil($money_j * $time * $rate * 100) / 100;
            $data = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'money_j' => $money_j,
                'time' => $time,
                'createdate' => date('Y-m-d H:i:s'),
                'city' => $city,
                'status' => 1,
                'lixi' => $lixi,
                'name' => $name,
                //'bank'=>$bank,
                //'bank_hao'=>$bank_hao,
                'address' => $address,
                'danwei' => $danwei,
                'lxfs1' => $lxfs1,
                'lxfs2' => $lxfs2,
                'beizhu' => $beizhu,
                'isday' => (int)$_POST['isday'],
                'rate' => (float)$_POST['rate']
            );
            $jk_id = $this->jiekuan_mod->add($data);

            if (empty($image1) || empty($image2)) {

                $logo = $this->_upload_logo($jk_id, 'image_1');
                $logo1 = $this->_upload_logo($jk_id, 'image_2');
                if (empty($logo) || empty($logo1)) {
                    $sql = "delete from " . DB_PREFIX . "jiekuan where id = '$jk_id'";
                    $this->jiekuan_mod->db->query($sql);
                    $this->show_warning('zhengbunengweikong', 'go_back', 'index.php?app=member&amp;act=my_jiekuan');
                    return;
                }
                if ($logo === false && $logo1 === false) {
                    return;
                }

                $logo && $this->jiekuan_mod->edit($jk_id, array('image_1' => $logo));
                $logo1 && $this->jiekuan_mod->edit($jk_id, array('image_2' => $logo1));
            } else {
                $this->jiekuan_mod->edit($jk_id, array('image_1' => $image1, 'image_2' => $image2));
            }

            $this->show_message('tijiaochenggong', '', 'index.php?app=member&act=jiekuanjilu');
        } else {

            if (in_array(1, $bb)) {
                /* 当前位置 */
                $this->_curlocal(LANG::get('member_center'), url('app=member'),
                    LANG::get('overview'));


                $jiekuan = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "jiekuan where user_id = '$user_id' limit 1 ");

                $this->assign('jiekuan', $jiekuan);

                $cityrow = $this->jiekuan_mod->get_cityrow();
                $this->assign('cityrow', $cityrow);
                $cityid = $cityrow['city_id'];
                $artic = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "article where cate_id = 26 and city='$cityid' limit 1 ");
                if ($artic) {
                    $notice = Lang::get('notice');
                    $notice = str_replace('{1}', $artic['article_id'], $notice);
                } else {
                    $notice = Lang::get('notice');
                    $notice = str_replace('{1}', 744, $notice);
                }
                $this->assign('notice', $notice);
                /* 当前用户中心菜单 */
                $this->_curitem('overview');
                $this->assign('page_title', Lang::get('member_center'));
                $this->display('my_jiekuan.html');

            } else {
                $this->show_warning('meiyouci');
                return;
            }


        }

    }

    function jiekuanjilu()
    {
        $this->jiekuan_mod =& m('jiekuan');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), url('app=member'),
            LANG::get('overview'));

        $cityrow = $this->jiekuan_mod->get_cityrow();
        $this->assign('cityrow', $cityrow);
        /* 当前用户中心菜单 */
        $this->_curitem('overview');
        $this->assign('page_title', Lang::get('member_center'));
        $user_id = $this->visitor->get('user_id');

        $jie = $this->jiekuan_mod->getAll("select * from " . DB_PREFIX . "jiekuan 
		where user_id = '$user_id' order by createdate desc");

        foreach ($jie as $key => $kuan) {
            $jie[$key]['z_money'] = $kuan['lixi'] + $kuan['money_j'];

            $jie[$key]['daoqi_time'] = substr($kuan['jieshu_time'], 0, 10);
            $jie[$key]['start_time1'] = substr($kuan['start_time'], 0, 10);
            $jie[$key]['baozheng'] = $kuan['money_j'] * 1 / 10;

        }

        $this->assign('jie', $jie);
        $this->display('jiekuanjilu.html');
    }

    function jk_xiangqing()
    {
        $this->jiekuan_mod =& m('jiekuan');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), url('app=member'),
            LANG::get('overview'));
        /* 当前用户中心菜单 */
        $this->_curitem('overview');
        $id = empty($_GET['id']) ? null : trim($_GET['id']);
        //$id=$_GET['id'];
        $this->assign('page_title', Lang::get('member_center'));
        $user_id = $this->visitor->get('user_id');
        $jie = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "jiekuan where id = '$id'");

        $jie['z_money'] = $jie['money_j'] - $jie['money_j'] * 1 / 10;

        $jie['daoqi_time'] = substr($jie['jieshu_time'], 0, 10);
        $jie['start_time1'] = substr($jie['start_time'], 0, 10);

        if ($jie['status1'] == 2) {
            $jie['faxi'] = $jie['money_h'] - $jie['money_j'] + $jie['money_j'] * 1 / 10;
        } else if ($jie['status'] == 2) {
            $jie['faxi'] = $this->jiekuan_mod->lixi($jie['money_j'], $jie['rate'], $jie['jieshu_time']);
        }

        $this->assign('jie', $jie);
        $this->display('jk_xiangqing.html');
    }

    function huankuan()
    {
        $this->jiekuan_mod =& m('jiekuan');
        $this->moneylog_mod =& m('moneylog');
        $this->accountlog_mod =& m('accountlog');
        $this->canshu_mod =& m('canshu');
        $this->my_money_mod =& m('my_money');
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');
        if ($_POST) {
            $money_h = trim($_POST['money_h']);//现在提交的钱
            $money_h = (int)($money_h * 100) / 100;
            if ($money_h <= 0) {
                $this->show_warning('huankuandayu');
                return;
            }

            $jk_id = trim($_POST['id']);
            $jie = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "jiekuan where id = '$jk_id' limit 1");
            $money_yh = $jie['money_h'];//已还金额
            $money_j = $jie['money_j'];//借款金额
            $yinghuan_money = $money_j - $money_j * 1 / 10 - $money_yh;//应还金额
            $yingh_money = $money_j - $money_j * 1 / 10;//实际应还金额

            if ($money_h > $yinghuan_money) {
                $shiji_moneyh = $yinghuan_money;
            }
            if ($money_h <= $yinghuan_money) {
                $shiji_moneyh = $money_h;
            }

            $result = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "my_money 
			 where user_id = '$user_id' limit 1");
            $city = $result['city'];
            $riqi = date('Y-m-d H:i:s');
            $money = $result['money'];
            $money_dj = $result['money_dj'];
            $duihuanjifen = $result['duihuanjifen'];
            $dongjiejifen = $result['dongjiejifen'];

            $new_moneyh = $money_yh + $shiji_moneyh;//已还款总额
            $new_money = $money - $shiji_moneyh;//用户账号剩余金额

            $can = $this->jiekuan_mod->can();
            $zong_money = $can['zong_money'];
            $zong_jifen = $can['zong_jifen'];
            $new_zong_money = $zong_money + $shiji_moneyh;//总账户余额
            if ($money < $shiji_moneyh) {
                $this->show_warning('nindeyue');
                return;
            }

            if ($yingh_money <= $new_moneyh)//可以还清
            {
                $data = array('money_h' => $new_moneyh, 'end_time' => $riqi, 'status1' => 2);
                $new_moneydj = $money_dj - $money_j * 1 / 10;
                $jie_money = $money_j * 1 / 10;
                $text = Lang::get('jiedongbaozhengjin');
                $text = str_replace('{1}', $jie_money, $text);
            } else {
                $data = array('money_h' => $new_moneyh, 'end_time' => $riqi, 'status1' => 1);
                $new_moneydj = $money_dj;
                $jie_money = 0;
            }

            //添加用户资金流水
            $addlog = array(
                'money' => '-' . $shiji_moneyh,
                'money_dj' => '-' . $jie_money,
                'time' => $riqi,
                'user_name' => $user_name,
                'user_id' => $user_id,
                'zcity' => $city,
                'type' => 42,
                's_and_z' => 2,
                'beizhu' => $text,
                'dq_money' => $new_money,
                'dq_money_dj' => $new_moneydj,
                'dq_jifen' => $duihuanjifen,
                'dq_jifen_dj' => $dongjiejifen
            );
            $this->moneylog_mod->add($addlog);

            //添加总账户资金流水
            $addaccoun = array(
                'money' => $shiji_moneyh,
                'time' => $riqi,
                'user_name' => $user_name,
                'user_id' => $user_id,
                'zcity' => $city,
                'type' => 42,
                's_and_z' => 1,
                //'beizhu'=>$beizhu,
                'dq_money' => $new_zong_money,
                'dq_jifen' => $zong_jifen,
            );
            $this->accountlog_mod->add($addaccoun);

            $this->my_money_mod->edit('user_id=' . $user_id, array('money' => $new_money, 'money_dj' => $new_moneydj));
            $this->canshu_mod->edit('id=1', array('zong_money' => $new_zong_money));
            $this->jiekuan_mod->edit('id=' . $jk_id, $data);

            $this->show_message('huankuanchenggong', '', 'index.php?app=member&act=jiekuanjilu');

        } else {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'), url('app=member'),
                LANG::get('overview'));
            /* 当前用户中心菜单 */
            $this->_curitem('overview');
            $id = empty($_GET['id']) ? null : trim($_GET['id']);

            $this->assign('page_title', Lang::get('member_center'));

            $jie = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "jiekuan where id = '$id' limit 1");
            $jie['z_money'] = $jie['lixi'] + $jie['money_j'];

            if ($jie['isday'] == 1) {
                $jie['daoqi_time'] = time($jie['start_time']) + 3600 * 24 * $jie['time'];
                $jie['daoqi_time'] = date('Y-m-d', $jie['daoqi_time']);
            } else {
                $jie['daoqi_time'] = time($jie['start_time']) + 3600 * 24 * $jie['time'] * 30;
                $jie['daoqi_time'] = date("Y-m-d", strtotime("+" . $jie['time'] . 'months', strtotime($jie['start_time'])));
            }

            $jie['start_time1'] = substr($jie['start_time'], 0, 10);

            $jie['dai_mone'] = $jie['money_j'] - $jie['money_h'] - $jie['money_j'] * 1 / 10;
            if ($jie['dai_mone'] < 0) {
                $jie['dai_money'] = 0;
            } else {
                $jie['dai_money'] = $jie['dai_mone'];
            }
            $jie['yh_money'] = $jie['money_j'] - $jie['money_j'] * 1 / 10;

            if ($jie['status1'] == 2) {
                $jie['faxi'] = $jie['money_h'] - $jie['yh'];
            } else if ($jie['status'] == 2) {
                $jie['faxi'] = $this->jiekuan_mod->lixi($jie['money_j'], $jie['rate'], $jie['jieshu_time']);
            }
            $this->assign('jie', $jie);
            $this->display('huankuan.html');
        }

    }

    function jk_edit()
    {
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');
        $this->jiekuan_mod =& m('jiekuan');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), url('app=member'),
            LANG::get('overview'));

        /* 当前用户中心菜单 */
        $this->_curitem('overview');
        $this->assign('page_title', Lang::get('member_center'));

        $id = empty($_GET['id']) ? 0 : $_GET['id'];
        $userid = empty($_GET['userid']) ? 0 : $_GET['userid'];

        $mem = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "member where user_id = '$user_id' limit 1");
        $this->assign('mem', $mem);
        $my_money = $this->jiekuan_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id='$user_id'");
        $canshu = $this->jiekuan_mod->can();
        foreach ($my_money as $key => $my) {

            $my_money[$key]['zengjin'] = round($my['zengjin'] / $canshu['jifenxianjin'], 2);
            $dengji = $my['t'];
            $le = dengji($dengji);
            $jk = jiekuan($dengji);

        }
        $this->assign('my_money', $my_money);
        $this->assign('jk', $jk);

        if (!IS_POST) {
            $jiekuan = $this->jiekuan_mod->getRow("select * from " . DB_PREFIX . "jiekuan where id = '$id' and user_id='$userid' limit 1 ");
            $this->assign('jiekuan', $jiekuan);

            if (empty($jiekuan)) {
                $this->show_warning('meiyou');
                return;
            }

            $this->display('jk_edit.html');
        } else {

            $name = trim($_POST['name']);

            $money_j = trim($_POST['money_j']);
            $kejie = Lang::get('nindekejie');
            $kejie = str_replace('{1}', $jk, $kejie);
            if ($money_j > $jk) {
                $this->show_warning($kejie);
                return;
            }
            if ($money_j <= 0) {
                $this->show_warning($jiekuanjinebuneng);
                return;
            }
            $time = (int)$_POST['time'];
            if ($time == 0) {
                $this->show_warning('jiekuanqixianbuneng');
                return;
            }

            $city = trim($_POST['city']);
            $bank = trim($_POST['bank']);
            $address = trim($_POST['address']);
            $danwei = trim($_POST['danwei']);
            $lxfs1 = trim($_POST['lxfs1']);
            $lxfs2 = trim($_POST['lxfs2']);
            $bank_hao = trim($_POST['bank_hao']);
            $beizhu = trim($_POST['beizhu']);
            $image1 = trim($_POST['image_1']);
            $image2 = trim($_POST['image_2']);
            $jk_id = trim($_POST['id']);
            $rate = ((float)$_POST['rate']) / 100;
            $isday = (int)$_POST['isday'];
            if ($isday == 1) {
                $rate = $rate / 30;
            }

            $lixi = $money_j * $time * $rate;
            $data = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'money_j' => $money_j,
                'time' => $time,
                'createdate' => date('Y-m-d H:i:s'),
                'city' => $city,
                'status' => 1,
                'lixi' => $lixi,
                'name' => $name,
                //'bank'=>$bank,
                //'bank_hao'=>$bank_hao,
                'address' => $address,
                'danwei' => $danwei,
                'lxfs1' => $lxfs1,
                'lxfs2' => $lxfs2,
                'beizhu' => $beizhu,
                'isday' => (int)$_POST['isday'],
                'rate' => (float)$_POST['rate']
            );

            $this->jiekuan_mod->edit('id=' . $jk_id, $data);

            $logo = $this->_upload_logo($jk_id, 'image_1');
            $logo1 = $this->_upload_logo($jk_id, 'image_2');

            $logo && $this->jiekuan_mod->edit($jk_id, array('image_1' => $logo));
            $logo1 && $this->jiekuan_mod->edit($jk_id, array('image_2' => $logo1));

            $this->show_message('edit_successed',
                'back_list', 'index.php?app=member&act=my_jiekuan');

        }


    }

    function jk_drop()
    {
        $this->jiekuan_mod =& m('jiekuan');
        $id = intval($_GET['id']);//供货id
        $userid = intval($_GET['userid']);//供货id
        $sql = "delete from " . DB_PREFIX . "jiekuan where id = '$id' and user_id='$userid'";
        $this->jiekuan_mod->db->query($sql);
        $this->show_message('delete', 'back_list', 'index.php?app=member&act=jiekuanjilu');
    }

    function _upload_logo($jk_id, $can)
    {
        $this->jiekuan_mod =& m('jiekuan');
        $file = $_FILES[$can];
        $riqi = time() . rand(100, 999);
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return;
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES[$can]);//上传logo

        if (!$uploader->file_info()) {

            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=member&amp;act=my_jiekuan');
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/sfz_' . $this->visitor->get('user_id') . '/goods_' . (time() % 200), $riqi . $jk_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        } else {
            return false;
        }
    }

    function denglu()
    {
        $user_id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $ms =& ms(); //连接用户中心
        $this->_do_login($user_id);
        $synlogin = $ms->user->synlogin($user_id);
        $this->show_message(Lang::get('login_successed') . $synlogin,
            //'back_before_login', rawurldecode($_POST['ret_url']),
            'enter_member_center', 'index.php?app=member'
        );
    }


    function zhuanzhang()
    {
        $user_id = $_GET['user_id'];
        $type = $_GET['type'];
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('zhuanzhang')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('zhuanzhang');
        $this->assign('type', $type);
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('zhuanzhang'));
        $this->display('jiedai_zhuanzhang.html');

    }


    function zhuanzhanglog()
    {
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('zhuanzhang')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('zhuanzhang');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('zhuanzhang'));
        $userid = $this->visitor->get('user_id');
        $this->my_moneylog_mod =& m('my_moneylog');
        $page = $this->_get_page();
        $index = $this->my_moneylog_mod->find(array(
            'conditions' => '(leixing=1 or leixing=2 or leixing=3 or leixing=4) and user_id=' . $userid,
            'limit' => 10,
            'order' => "id desc",
            'count' => true));
        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        foreach ($index as $key => $var) {
            $index[$key]['money'] = abs($var['money']);
        }

        $this->assign('index', $index);
        $this->display('zhuanzhanglog.html');

    }


}

?>
