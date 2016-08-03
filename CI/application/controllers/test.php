<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class test extends CI_Controller 
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('test_model');
		$this->load->model('test2_model');
	}

	public function index()
	{		

		echo  111; 
		//$a=$this->test_model->test();
		print_r($this->test_model->get_one());
		print_r($this->test2_model->fields);
		//print_r($a);

               
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */