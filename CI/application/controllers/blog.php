<?php
class Blog extends CI_Controller {

 function __construct()
 {
  parent::__construct();
 }

 public function index()
 {
 // $this->load->view('blogview');
 $data['todo_list'] = array('Clean House', 'Call Mom', 'Run Errands');

  $data['title'] = "My Real Title";
  $data['heading'] = "My Real Heading";
  
  $this->load->view('blogview', $data);

 }
public function comments($a)
 {
  echo '看这里！';
  echo '<br>';
  echo $a;
 }
//public function _remap($method)
//{
   // if ($method == 'some_method')
    //{
    //    $this->$method();
    //}
   // else
    //{
    //    $this->comments(111);
    //}
//}

//public function _output($output)
//{
   // echo $output;
//}

private function _utility()
{
  echo '看这里!!!!!！';
}

}
?>