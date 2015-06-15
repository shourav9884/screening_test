<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qa extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->library('screen_LIB');
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
		
		$lib=new screen_LIB();
		$answer=$lib->getQA($this->input->get('q'));
		$this->output->set_content_type('application/json')->set_output(json_encode(array("answer"=>$answer)));
		// $this->load->view('welcome_message');
	}
	public function test()
	{
		$result=$this->Testmodel->get();
		var_dump($result);
	}
	public function insert()
	{
		$this->Testmodel->insert();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
