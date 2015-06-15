<?php
/**
* 
*/
class Screen_lib
{
	
	

	function CurlConnect($url,$request,$username=null)
    {
        $length=strlen($request);
        $ch = curl_init($url);
        $options = array(
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_HEADER         => false,        // don't return headers
                CURLOPT_FOLLOWLOCATION => true,  
                CURLOPT_MAXREDIRS      =>10,       // follow redirects
                CURLOPT_ENCODING       => "utf-8",           // handle all encodings
                CURLOPT_AUTOREFERER    => true,         // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 20,          // timeout on connect
                CURLOPT_TIMEOUT        => 20,          // timeout on response
                CURLOPT_POST            => 0,            // i am sending post data
                CURLOPT_POSTFIELDS     => $request,    // this are my post vars
                CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
                CURLOPT_SSL_VERIFYPEER => false,        //
                CURLOPT_VERBOSE        => 1,
                CURLOPT_HTTPGET		   => true
               
                

        ); 
        if($username!=null)
        {
            // echo "string";
            $options[CURLOPT_COOKIEJAR]=dirname(__FILE__)."/".$username.'_cookie.txt';
            $options[CURLOPT_COOKIEFILE]=dirname(__FILE__)."/".$username.'_cookie.txt';
        }
        // print_r($options);
        curl_setopt_array($ch,$options);
        $data = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        // echo $curl_errno;
        // echo $curl_error;
        
        // var_dump(curl_getinfo($ch));
        curl_close($ch);
        return $data;
    }
	function getLocation($array)
	{
		$preposition=array("at","in","on",'into',"from","onto","of","for");
		$verb=array("is","are");
		$prep_pos=false;
		$verb_pos=false;
		for($i=1;$i<count($array);$i++)
		{
			$prep_key=array_search($array[$i], $preposition);
			$verb_key=array_search($array[$i], $verb);
			if(is_int($prep_key)) 
			{
				$prep_pos=$i;
				
			}
			if(is_int($verb_key))
			{
				$verb_pos=$i;
			}
		}
		$loc="";
		for ($i=$prep_pos+1; $i < count($array) ; $i++) { 
			$loc.=$array[$i]." ";
		}
		$loc=trim($loc);
		return $loc;
	}
	function getTag($array,$flag=false)
	{
		$tag="";
		if($flag==false)
		{
			
			$tags=array("humidity","pressure",'temperature');
			for ($i=0; $i < count($array) ; $i++) { 
				$key=array_search($array[$i], $tags);
				if(is_int($key))
				{
					$tag=$tags[$key];
					break;
				}
			}
		}
		else
		{
			$tags=array("weather");
			for ($i=0; $i < count($array) ; $i++) { 
				$key=array_search($array[$i], $tags);
				if(is_int($key))
				{
					$tag=$array[$i-1];
					break;
				}
			}
		}
		return $tag;

	}
	function getWeather($question)
	{
		// $question="What Is the Temperature today in Dhaka?";
		$question=strtolower($question);
		$question=trim($question);
		$question=str_replace("?","",$question);

		$q_array=explode(" ", $question);
		$wh_tags=array("what","where","how","when","which","who","why","whom","whose");
		$key=array_search($q_array[0], $wh_tags);


		$loc=$this->getLocation($q_array);

		$tag="";
		$url="api.openweathermap.org/data/2.5/weather?q=".$loc."&type=like&units=metric";
		$re=$this->CurlConnect($url,null,null);
		$answer_arr=json_decode($re,true);
		if($answer_arr['cod']==200)
		{
			$answer="";
			if(is_int($key))
			{
				$tag=$this->getTag($q_array);
				if($tag=="temperature")
				{
					$answer="Temperature is on average ".$answer_arr['main']['temp']." C";
				}
				else if($tag=="pressure")
				{
					$answer="Pressure is on average ".$answer_arr['main']['pressure']." kPa";
				}
				else if($tag=="humidity")
				{
					$answer="humidity is on average ".$answer_arr['main']['humidity']."%";
				}

			}
			else
			{
				$tag=$this->getTag($q_array,true);
				if($tag=="rain")
				{
					if($answer_arr['weather'][0]['main']=='Rain')
						$answer="Yes";
					else
						$answer="No";
				}
				else if($tag=="clear")
				{
					if($answer_arr['weather'][0]['main']=='Clear')
						$answer="Yes";
					else
						$answer="No";
					// var_dump($answer_arr['weather']);
				}
				else if($tag=="clouds")
				{
					if($answer_arr['weather'][0]['main']=='Clouds')
						$answer="Yes";
					else
						$answer="No";
				}
				$answer.=",".$answer_arr['weather'][0]['description'];
			}
		}
		else
		{
			$answer="No answer found";
		}
		return $answer;
	}
	function getQA($question)
	{
		$question=urlencode($question);
		$url="http://quepy.machinalis.com/engine/get_query?question=".$question;
		$r=$this->CurlConnect($url,null);
		$ar=json_decode($r,true);

		// var_dump($ar);

		$q=$ar['queries'][0]['query'];
		$final_answer="Your majesty! Jon Snow knows nothing! So do I!";
		// $final_answer="";
		if($q!=NULL)
		{
			$temp=explode("PREFIX dbpedia-owl: ", $q);
			// echo "<pre>";
			$q=$temp[1];
			$q=str_replace("<http://dbpedia.org/ontology/>", "", $q);
			$q=trim($q);

			
			
			$q=urlencode($q);
			// echo $q;
			$url="http://dbpedia.org/sparql?debug=on&timeout=0&query=".$q."&default-graph-uri=&format=application%2Fsparql-results%2Bjson";
			
			$ttt=$this->CurlConnect($url,null);
			$arr=json_decode($ttt,true);

			$answer=$arr['results']['bindings'];
			$var_name=$arr['head']['vars'][0];
			if(count($answer)>0)
			{
				// var_dump($answer);
				foreach ($answer as $a) {
					if(array_key_exists('xml:lang', $a[$var_name]))
					{
						if($a[$var_name]['xml:lang']=="en")
							$final_answer=$a[$var_name]['value'];
					}
					else if(array_key_exists("value", $a[$var_name]))
					{
						$final_answer=$a[$var_name]["value"];
					}
				}
				
			}

		}
		// if($final_answer=="")
		// {
		// 	 $query=$ar['queries'][1]['query'];
		// 	 $query=str_replace("\n", "", $query);
		// 	 $query=str_replace(" ", "+", $query);
		// 	 $url="https://www.googleapis.com/freebase/v1/mqlread?query=".$query;
		// 	 // echo $url;
		// 	 $r=$this->CurlConnect($url,null);
		// 	 $r_arr=json_decode($r,true);
		// 	 if(array_key_exists("result", $r_arr))
		// 	 {
		// 	 	print_r($r_arr);
		// 	 }
		// }
		return $final_answer;

	}
	function getGreetings($question)
	{
		$preposition=array("at","in","on",'into',"from","onto","of","for");
		$verb=array("is","are");
		$wh_tags=array("what","where","how","when","which","who","why","whom","whose");
		$question=strtolower($question);
		$t=explode("!", $question);
		$greet=$t[0];
		$say=trim($t[1]);
		$say_arr=explode(" ", $say);

		$answer_greet="Hello, Kitty!";
		// echo $answer_greet;

		$wh_key=array_search($say_arr[0], $wh_tags);
		$answer_say="";
		
		if(is_int($wh_key))
		{
			$tag=$wh_tags[$wh_key];
			$ar=explode($tag, $say);
			if(count($ar)>1)
			{	
				if($tag=="what")
				{
					$t=trim($ar[1]);
					if($t=="is your name?" || $t=="is ur name?" || $t=="are you?")
							$answer_say="I am Shourav Nath. Thank you for asking me.";
					
				}
				else if($tag=="how")
				{
					$t=trim($ar[1]);
					if($t=="are you?" || $t=="r u?")
						$answer_say="I am fine. Thank you for asking me.";
				} 
				else if($tag=="where")
				{
					$t=trim($ar[1]);
					if($t=="are you?" || $t=="do you live in?")
						$answer_say="I am in Dhaka, Bangladesh";
				}
			}
		}
		if($answer_say=="")
		{
			$answer_say="I am Shourav Nath. Nice to meet you.";
			if(count($say_arr)>1)
				$answer_say.=" I am sorry I could not understand your question.";
		}
		
		return $answer_greet." ".$answer_say;
	}
}