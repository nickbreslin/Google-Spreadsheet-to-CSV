<?php

class Googledocs extends Abstract_Class
{	
	//http://code.google.com/apis/documents/docs/2.0/developers_guide_protocol.html#API
	private static $base = "";
	
    function __construct()
    {
		parent::__construct();
		$this->_init();
    }

	private function _init()
	{
		self::$base = "";
	}
	
	public function fetchXML($url, $token)
	{
		$ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL,            $url);		// URL to Scrape
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// Return string from curl_exec
	    curl_setopt($ch, CURLOPT_HEADER,         false); // Include the header info
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
			"GData-Version: 2.0",
			"Authorization: AuthSub token=\"$token\""
		)); 

	    $results = curl_exec($ch);
	    curl_close($ch);
		
		$string = simplexml_load_string($results);
		
		if ($string===FALSE)
		{
			Debug::error("Not XML");
			return false;
		}
		else
		{
			$oXML = new SimpleXMLElement($results);
			
			return $oXML;
		}
	}

	public function getDoc($src, $token, $gid = 1)
	{
	
		$url = $src."&exportFormat=csv&gid=$gid";

		$ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL,            $url);		// URL to Scrape
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// Return string from curl_exec
	    curl_setopt($ch, CURLOPT_HEADER,         false); // Include the header info
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
			"GData-Version: 2.0",
			"Authorization: AuthSub token=\"$token\""
		));
		

	    $results = curl_exec($ch);
	    curl_close($ch);
		$results=utf8_decode($results);
		$arr = str_getcsv($results);
		Debug::info($arr);
		
		$fh = fopen(ROOT_PATH . 'missions.csv', 'w');
		fwrite($fh, $results);
		fclose($fh);
	}
	
	public function getDocs($token)
	{
		
		$url = "https://docs.google.com/feeds/documents/private/full";

		$results = self::fetchXML($url, $token);
		if(!$results)
		{
			return array();
		}
		
		$return = array();
		
		foreach($results as $result)
		{
			if($result->{'id'} == "" || $result->{'id'} == "https://docs.google.com/feeds/documents/private/full")
			{
				//Debug::error("No Id.");
				continue;
			}
			//echo "<pre>".var_dump($result, true)."</pre>";
			$data                  = array();
			$data['id']            = $result->{'id'};
			$data['title']        = $result->{'title'};
			$content             = $result->{'content'};
			$data['src']        = $content['src'];
			$data['space-id']    = $result->{'space-id'};
			$return[] = $data;
		}

		return $return;
	}
			
	public function getFolders($token)
	{
		
		$url = "https://docs.google.com/feeds/documents/private/full/-/folder?showfolders=true";

		$results = self::fetchXML($url, $token);
		Debug::info($results );
		if(!$results)
		{
			return array();
		}
		
		$return = array();
		
		foreach($results as $result)
		{
			$data                  = array();
			$data['id']            = $result->{'id'};
			$data['title']        = $result->{'title'};
			//$data['due-date']      = $result->{'due-date'};
			//$data['space-id']    = $result->{'space-id'};
			Debug::info($data['title']);
			$return[] = $data;
		}

		return $return;
	}
}