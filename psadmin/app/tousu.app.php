<?php

/* 管理员控制器 */

class TousuApp extends BackendApp
{


    function __construct()
    {
        $this->TousuApp();
    }

    function TousuApp()
    {
        parent::__construct();

        $this->city_mod = &m('city');
        $this->member_mod = &m('member');
        $this->kaiguan_mod =& m('kaiguan');
        $this->complain_mod =& m('complain');
        $this->userpriv_mod =& m('userpriv');

    }

    function index()
    {

        $userid = $this->visitor->get('user_id');
        $priv_row = $this->userpriv_mod->getRow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0 limit 1");
        $privs = $priv_row['privs'];
        $city = $priv_row['city'];
        $this->assign('priv_row', $priv_row);
        /*echo $city;*/
        $page = $this->_get_page();
        $sort = 'id';
        $order = 'desc';

        if ($privs == "all") {
            $comp = $this->complain_mod->find(array(
                'conditions' => 'ts_id=0',
                'limit' => $page['limit'],
                'order' => "$sort $order",
                'count' => true,
            ));
        } else {
            $comp = $this->complain_mod->find(array(
                'conditions' => 'ts_id=0 and city=' . $city,
                'limit' => $page['limit'],
                'order' => "$sort $order",
                'count' => true,
            ));
        }
        $city_row = array();
        $result = $this->complain_mod->getAll("select * from " . DB_PREFIX . "city");
        foreach ($result as $var) {
            $row = explode('-', $var['city_name']);
            $city_row[$var['city_id']] = $row[0];
        }
        $result = null;
        foreach ($comp as $key => $val) {
            $comp[$key]['city_name'] = $city_row[$val['city']];
        }
        $page['item_count'] = $this->complain_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions ? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        $this->assign('comp', $comp);
        $this->display('complain.index.html');

    }

    function ts_weishenhe()
    {
        $userid = $this->visitor->get('user_id');
        $priv_row = $this->userpriv_mod->getRow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0 limit 1");
        $privs = $priv_row['privs'];
        $city = $priv_row['city'];
        $page = $this->_get_page();
        if ($privs == "all") {
            $ts = $this->complain_mod->find(array(
                'conditions' => "status1=0 and ts_id=0",
                'limit' => $page['limit'],
                'order' => 'id DESC',
                'count' => true
            ));
        } else {
            $ts = $this->complain_mod->find(array(
                'conditions' => "status1=0 and city='$city' and ts_id=0",
                'limit' => $page['limit'],
                'order' => 'id DESC',
                'count' => true
            ));
        }
        $city_row = array();
        $result = $this->complain_mod->getAll("select * from " . DB_PREFIX . "city");
        foreach ($result as $var) {
            $row = explode('-', $var['city_name']);
            $city_row[$var['city_id']] = $row[0];
        }
        $result = null;
        foreach ($ts as $key => $val) {
            $ts[$key]['city_name'] = $city_row[$val['city']];
        }

        $page['item_count'] = $this->complain_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('ts', $ts);//传递到风格里
        $this->display('ts_weishenhe.html');
        return;

    }

    function ts_shenhe()
    {
        $this->message_mod =& m('message');
        $id = empty($_GET['id']) ? null : trim($_GET['id']);
        $order_id = empty($_GET['order_id']) ? null : trim($_GET['order_id']);
        $status1 = trim($_POST['status1']);
        $yijian = trim($_POST['yijian']);
        $com = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain where id = '$id' limit 1");
        $recid = $com['rec_id'];
        $or = $this->complain_mod->getAll("select * from " . DB_PREFIX . "order_goods where order_id = '$order_id' and rec_id in ($recid) ");
        $shs = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain where ts_id = '$id' limit 1");


        if ($_POST) {

            $new_gh = array(
                'status1' => $status1,
                'yijian' => $yijian,
            );
            $this->complain_mod->edit('id=' . $id, $new_gh);
            $this->complain_mod->edit('ts_id=' . $id, $new_gh);

            $content = Lang::get('tousuyichuli');
            $content = str_replace('{1}', $com['complainant'], $content);
            $add_notice1 = array(
                'from_id' => 0,
                'to_id' => $com['complainant'],
                'content' => $content,
                'add_time' => gmtime(),
                'last_update' => gmtime(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3,
            );
            $this->message_mod->add($add_notice1);

            $content = Lang::get('shensuyichuli');
            $content = str_replace('{1}', $com['respondent'], $content);
            $add_notice = array(
                'from_id' => 0,
                'to_id' => $com['respondent_id'],
                'content' => $content,
                'add_time' => gmtime(),
                'last_update' => gmtime(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3,
            );
            $this->message_mod->add($add_notice);

            $this->show_message('shenhechenggong',
                /* 'caozuochenggong', 'index.php?module=my_money&act=cz_shenhe_user&user_id='.$user_id.'&log_id='.$log_id,*/
                'fanhui', 'index.php?app=tousu');
        } else {

            foreach ($or as $i=>$v){
                if(strtolower(substr($v['goods_image'],0,4))!='http'){
                    $or[$i]['goods_image']='/'.$v;
                }
            }

            if($shs['imag_1']!='' && strtolower(substr($shs['imag_1'],0,4))!='http'){
                $shs['imag_1']='/'.$shs['imag_1'];
            }
            if($shs['imag_2']!='' && strtolower(substr($shs['imag_2'],0,4))!='http'){
                $shs['imag_2']='/'.$shs['imag_2'];
            }
            if($shs['imag_3']!='' && strtolower(substr($shs['imag_3'],0,4))!='http'){
                $shs['imag_3']='/'.$shs['imag_3'];
            }

            if($com['imag_1']!='' && strtolower(substr($com['imag_1'],0,4))!='http'){
                $com['imag_1']='/'.$com['imag_1'];
            }
            if($com['imag_2']!='' && strtolower(substr($com['imag_2'],0,4))!='http'){
                $com['imag_2']='/'.$com['imag_2'];
            }
            if($com['imag_3']!='' && strtolower(substr($com['imag_3'],0,4))!='http'){
                $com['imag_3']='/'.$com['imag_3'];
            }

            $this->assign('shs', $shs);
            $this->assign('or', $or);
            $this->assign('com', $com);
            $this->display('ts_shenhe.html');
            return;
        }
    }

    function ts_edit()
    {
        $id = empty($_GET['id']) ? 0 : $_GET['id'];

        if (!IS_POST) {
            $find_data = $this->complain_mod->find($id);
            $com = current($find_data);


            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('com', $com);
            $this->display('ts_edit.html');
        } else {

            $data = array();
            $data['yijian'] = $_POST['yijian'];

            $rows = $this->complain_mod->edit($id, $data);
            if ($this->complain_mod->has_error()) {
                $this->show_warning($this->complain_mod->get_error());

                return;
            }

            $this->show_message('edit_successed',
                'fanhui', 'index.php?app=tousu'
            );
        }
    }


    function drop()
    {
        $id = intval($_GET['id']);//供货id
        $sql = "delete from " . DB_PREFIX . "complain where id = '$id'";
        $sql1 = "delete from " . DB_PREFIX . "complain where ts_id = '$id'";
        $this->complain_mod->db->query($sql);
        $this->complain_mod->db->query($sql1);
        $this->show_message('delete', 'fanhui', 'index.php?app=tousu');
    }

    function ts_xiangqing()
    {
        $id = empty($_GET['id']) ? 0 : $_GET['id'];
        $order_id = empty($_GET['order_id']) ? 0 : $_GET['order_id'];
        $com = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain where id = '$id' limit 1");
        $recid = $com['rec_id'];
        $or = $this->complain_mod->getAll("select * from " . DB_PREFIX . "order_goods where order_id = '$order_id' and rec_id in ($recid)");

        $shs = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain where ts_id = '$id' limit 1");

        /* 显示新增表单 */
        $yes_or_no = array(
            1 => Lang::get('yes'),
            0 => Lang::get('no'),
        );
        $this->import_resource(array(
            'script' => 'jquery.plugins/jquery.validate.js'
        ));

        foreach ($or as $i=>$v){
            if(strtolower(substr($v['goods_image'],0,4))!='http'){
                $or[$i]['goods_image']='/'.$v;
            }
        }

        if($shs['imag_1']!='' && strtolower(substr($shs['imag_1'],0,4))!='http'){
            $shs['imag_1']='/'.$shs['imag_1'];
        }
        if($shs['imag_2']!='' && strtolower(substr($shs['imag_2'],0,4))!='http'){
            $shs['imag_2']='/'.$shs['imag_2'];
        }
        if($shs['imag_3']!='' && strtolower(substr($shs['imag_3'],0,4))!='http'){
            $shs['imag_3']='/'.$shs['imag_3'];
        }

        if($com['imag_1']!='' && strtolower(substr($com['imag_1'],0,4))!='http'){
            $com['imag_1']='/'.$com['imag_1'];
        }
        if($com['imag_2']!='' && strtolower(substr($com['imag_2'],0,4))!='http'){
            $com['imag_2']='/'.$com['imag_2'];
        }
        if($com['imag_3']!='' && strtolower(substr($com['imag_3'],0,4))!='http'){
            $com['imag_3']='/'.$com['imag_3'];
        }

        $this->assign('yes_or_no', $yes_or_no);
        $this->assign('com', $com);
        $this->assign('shs', $shs);
        $this->assign('or', $or);
        $this->display('ts_xiangqing.html');


    }

}

?>
