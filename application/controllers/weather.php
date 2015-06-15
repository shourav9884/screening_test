<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weather extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('screen_LIB');
	}
	
	public function index()
	{
		
		$lib=new screen_LIB();
		$q=$this->input->get('q');
		
		$r=$lib->getWeather($q);
		if($r=="No answer found")
			$this->output->set_status_header('404');
		else 
			$this->output->set_status_header('200');
		$this->output->set_content_type('application/json')->set_output(json_encode(array("answer"=>$r)));
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
