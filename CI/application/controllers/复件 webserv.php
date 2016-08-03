<?php
if (!defined('BASEPATH'))    exit('No direct script access allowed');

class webserv extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->db1 = $this->load->database('fenecll', TRUE);
        $this->db2 = $this->load->database('kuaicai', TRUE);
        $this->db3 = $this->load->database('mssql', TRUE);
        $this->load->model('member_model');
    }

    public function index()
    {
        
    }

    public function mallrebates()
    {
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        $_GET = array_map('urldecode', $_GET);
        $user_name = iconv('gbk', 'utf-8', $_GET['user_name']);
        $user_id=(int)$_GET['user_id'];
        $starttime=$_GET['starttime'];
        $endtime=$_GET['endtime'];
        $where='1=1';        
        if(!empty($starttime))
        {
            $where.=" and ConsumptionTime>'$starttime'";
        }        
        if(!empty($endtime))
        {
            $where.=" and ConsumptionTime<'$endtime'";
        }     
        
        $data=array();
        if(!empty($user_name))
        {
            $data['user_name']=$user_name;
        }
        if(!empty($user_id))
        {
            $data['user_id']=$user_id;
        }
        if(!empty($data))
        {
            $row=$this->member_model->get_one($data);
            if(!empty($row['web_id']))
            {
                $where.=" and UserID='".$row['web_id']."'";
            }
        }
        global $_S;       

        $page = empty($_POST['page']) ? 1 : $_POST['page'];
        $rp = empty($_POST['rp']) ? 10 : $_POST['rp'];
        $sortname = empty($_POST['sortname']) ? 'ConsumptionTime' : $_POST['sortname'];
        $sortorder = empty($_POST['sortorder']) ? 'desc' : $_POST['sortorder'];

        $fields = " MallRebatesID,UserID,Money,MoneyType,RebatesMoney,RebatesStatus";
        
        if($where=='1=1')
        {
            $sql = "SELECT TOP $rp $fields,convert(char,ConsumptionTime,120) as ConsumptionTime FROM MallRebates WHERE MallRebatesID NOT IN (SELECT TOP " . $rp * ($page - 1) . "MallRebatesID FROM MallRebates ORDER BY $sortname $sortorder)ORDER BY $sortname $sortorder";
            $result = $this->db3->query($sql);
          /*  $query = $this->db3->query("select count(*) as total from MallRebates");
            $row = $query->row_array();
            $total = $row['total'];*/        
            $total=$this->db3->count_all('MallRebates');
        }
        else 
        {
              $rp=1000;
              $sql="select top $rp $fields,convert(char,ConsumptionTime,120) as ConsumptionTime FROM MallRebates WHERE $where ORDER BY $sortname $sortorder";
              $result = $this->db3->query($sql);
              $total=$result->num_rows();              
        }
        
        $jsonData = array('page' => $page, 'total' => $total, 'rows' => array());

        $list = $result->result_array();
        $list = $this->change_user_fenecll($list, $rp);
        $list = $this->change_user_kuaicai($list, $rp);
        $arr_type = array('12%', '16%', '双队列');
        $arr_status = array('正常', '己结束');
        foreach ($list as $row)
        {
            if ($row['MoneyType'] == 0)
            {
                $inmoney = $row['Money'] * 0.15;
            } elseif ($row['MoneyType'] == 1)
            {
                $inmoney = $row['Money'] * 0.16;
            } elseif ($row['MoneyType'] == 2)
            {
                $inmoney = $row['Money'] * 0.31;
            }
            $entry = array('id' => $row['MallRebatesID'],
                'cell' => array(
                    'user_id' => $row['user_id'],
                    'user_name' => $row['user_name'],
                    'MoneyType' => $arr_type[$row['MoneyType']],
                    'inMoney' => $inmoney,
                    'sitename' => $_S['site'][$row['siteid']],
                    'Money' => $row['Money'],
                    'RebatesMoney' => $row['RebatesMoney'],
                    'MallRebatesID' => $row['MallRebatesID'],
                    'ConsumptionTime' => $row['ConsumptionTime'],
                    'RebatesStatus' => $arr_status[$row['RebatesStatus']],
                    'UserID' => addslashes($row['UserID'])
                ),
            );
            $jsonData['rows'][] = $entry;
        }
        echo json_encode($jsonData);
    }

    function change_user_kuaicai($list, $rp)
    {
        $users = '';
        foreach ($list as $row)
        {
            if (empty($row['user_id']))
            {//过滤掉己经查询过的
                if ($users == '')
                    $users = "'" . $row['UserID'] . "'";
                else
                    $users.=',' . "'" . $row['UserID'] . "'";
            }
        }
        if ($users != '')
        {
            $query = $this->db2->query("select user_id,user_name,web_id from  {$this->db2->dbprefix}member where web_id in($users) limit 0,$rp");
            foreach ($list as $i => $v)
            {
                foreach ($query->result_array() as $row)
                {
                    if ($v['UserID'] == $row['web_id'])
                    {
                        $list[$i]['user_id'] = getuserno($row['user_id']);
                        $list[$i]['user_name'] = $row['user_name'];
                        $list[$i]['siteid'] = 2;
                        break;
                    }
                }
            }
        }
        return $list;
    }

    function change_user_fenecll($list, $rp)
    {
        $users = '';
        foreach ($list as $row)
        {
            if (empty($row['user_id']))
            {//过滤掉己经查询过的
                if ($users == '')
                    $users = "'" . $row['UserID'] . "'";
                else
                    $users.=',' . "'" . $row['UserID'] . "'";
            }
        }
        if ($users != '')
        {           
            $query = $this->db1->query("select user_id,user_name,web_id from {$this->db1->dbprefix}member where web_id in($users) limit 0,$rp");
            foreach ($list as $i => $v)
            {
                foreach ($query->result_array() as $row)
                {
                    if ($v['UserID'] == $row['web_id'])
                    {
                        $list[$i]['user_id'] = getuserno($row['user_id']);
                        $list[$i]['user_name'] = $row['user_name'];
                        $list[$i]['siteid'] = 1;
                        break;
                    }
                    
                }
            }
        }
        return $list;
    }

}