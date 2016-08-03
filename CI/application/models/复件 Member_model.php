<?php
class Member_model extends CI_Model 
{
    public function __construct()
    {
        $this->db1 = $this->load->database('fenecll', TRUE);
        $this->db2 = $this->load->database('kuaicai', TRUE);
        $this->p2p = $this->load->database('p2p', TRUE);
    }
    
    public function get_one($data=array())
    {  
        $where='1=1';
        $where1='1=1';
        if(!empty($data['user_id']))
        {
            $where.=" and user_id='".$data['user_id']."'";
            $where1.=" and user_id='".$data['user_id']."'";
        }
        if(!empty($data['user_name']))
        {
            $where.=" and user_name='".$data['user_name']."'";
            $where1.=" and username='".$data['user_name']."'";
        }
        
       $query = $this->db1->get_where('member', $data,1);
       // $query=$this->db1->query("select * from {$this->db1->dbprefix}member where $where limit 1");
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        }
       
        $query = $this->db2->get_where('member', $data,1);
        //$query=$this->db2->query("select * from {$this->db2->dbprefix}member where $where limit 1");
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        }
        
        if(!empty($data['user_name']))
        {
            $data['username']=$data['user_name'];
        }
        $query = $this->p2p->get_where('user', $data,1);
       // $query=$this->p2p->query("select * from {$this->p2p->dbprefix}user where $where1 limit 1");
        if ($query->num_rows() > 0)
        {
            $row=$query->row_array();
            $row['web_id']=$row['ws_user_id'];
            return $row;
        }
    }


}
?>