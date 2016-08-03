<?php

class Member_model extends CI_Model
{

    public function __construct()
    {
        $this->db1 = $this->load->database('fenecll', TRUE);
        $this->db2 = $this->load->database('kuaicai', TRUE);
        $this->p2p = $this->load->database('p2p', TRUE);
    }
    public function get_webids($data=array())
    {
        $webids=array();
        $query = $this->db1->get_where('member', $data, 1);
        $row=$query->row_array();
        if (!empty($row['web_id']))
        {
            array_push($webids, $row['web_id']);
        }

        $query = $this->db2->get_where('member', $data, 1);
        $row=$query->row_array();
        if (!empty($row['web_id']))
        {
            array_push($webids, $row['web_id']);
        }

        if (!empty($data['user_name']))
        {
            $data['username'] = $data['user_name'];
            unset($data['user_name']);
        }
        $query = $this->p2p->get_where('user', $data, 1);
        $row=$query->row_array();
        if (!empty($row['ws_user_id']))
        {
            array_push($webids, $row['ws_user_id']);
        }
        return $webids;
    }

    public function get_one($data = array())
    {
        $query = $this->db1->get_where('member', $data, 1);
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        }

        $query = $this->db2->get_where('member', $data, 1);
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        }

        if (!empty($data['user_name']))
        {
            $data['username'] = $data['user_name'];
        }
        $query = $this->p2p->get_where('user', $data, 1);
        // $query=$this->p2p->query("select * from {$this->p2p->dbprefix}user where $where1 limit 1");
        if ($query->num_rows() > 0)
        {
            $row = $query->row_array();
            $row['web_id'] = $row['ws_user_id'];
            return $row;
        }
    } 

    function replacelist_fenecll($list, $rp)
    {
        $users = $this->get_userstr_noreplace($list);
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
    
    function replacelist_kuaicai($list, $rp)
    {
        $users = $this->get_userstr_noreplace($list);
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
    //获取list中没有user_id 和user_name 的UserID
    function get_userstr_noreplace($list)
    {
        $users='';
        foreach ($list as $row)
        {
            if (empty($row['user_id']))
            {
                if ($users == '')
                    $users = "'" . $row['UserID'] . "'";
                else
                    $users.=',' . "'" . $row['UserID'] . "'";
            }
        }   
        return $users;
    }
}

?>