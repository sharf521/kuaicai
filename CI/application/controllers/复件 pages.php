<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pages extends CI_Controller 
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('pages_model');
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		
		echo base_url();
		
		exit();

               if($_POST)
			   { 
               //print_r($_POST); 
			   //exit();
			   }



                for($i=1;$i<2;$i++)
                {
                    $this->db1 = $this->load->database('ecmall', TRUE); 		
                    $query = $this->db1->query("select * from ecm_member limit 0,10");
                    foreach ($query->result_array() as $row)
                    {
                      // echo iconv('gbk', 'utf-8', $row['user_name']);
                       echo $row['user_id'];
                    }		

                    $this->db = $this->load->database('default', TRUE);
                    //$query1 = $this->db->query("select * from admins");
                    $query1 = $this->db->query("select * from admins");
                    foreach ($query1->result_array() as $row)
                    {
                       //echo $row['username'];
                      // echo $row['userid'];
                    }
                    echo 1111;
                }
                
                
                $row=$this->pages_model->get_one();
          
		print_r($row);
                
                $this->load->view('pages');
	}
	public function view($page = 'home')
	{
		
		echo $page;
				
		print_r($this->input->get());
		//print_r(parse_str($page));
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */