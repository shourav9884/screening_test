

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testmodel extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}
	public function get()
	{
		$data=$this->db->select('*')->get('test')->result_array();


		return $data;
	}
	public function insert()
	{
		$data=array('name' => 'new_name','address'=>'new_address' );
		$this->db->insert('test', $data);
	}

}

/* End of file testmodel.php */
/* Location: ./application/models/testmodel.php */