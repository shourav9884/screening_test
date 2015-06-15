<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Greetings extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->library('screen_LIB');
	}
	
	public function index()
	{
		$lib=new screen_LIB();
		$q=$this->input->get('q');
		$answer=$lib->getGreetings($q);
		$this->output->set_content_type('application/json')->set_output(json_encode(array("answer"=>$answer)));
		
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
