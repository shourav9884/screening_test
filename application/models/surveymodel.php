<?php
class SurveyModel extends CI_Model{
	
		
	function __construct(){
		parent::__construct();
	}
	function getSurvey($sid,$pub_id)
	{
		$q=$this->db->select('survey_id')
				 ->where('survey_id',$sid)
				 ->where('share_code',$pub_id)
				 ->get('share')->result_array();
		if(count($q)>0)
		{
			$survey_id=$q[0]['survey_id'];
			$q=$this->db->where('survey_id',$survey_id)
						->where('published',1)
						->get('survey')->result_array();
			if(count($q)>0)
			{
				return $q[0];	
			}
			else
			{
				return "notAvailable";
			}
			
		}
		else
		{
			return "notAvailable";
		}


	}
	function saveResponse($sid)
	{
		$insertArray=array(
			'result_id'=>NULL,
			'survey_id'=>$sid,
			'answer'=>json_encode($this->input->post()),
			'date'=>NULL,
			'ip'=>$this->input->post('ip')
			);
		$q=$this->db->insert('results',$insertArray);
		return $q;
	}
}
?>
