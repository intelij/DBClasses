<?     
  
Class database {  
	
		function confirmQuery($result_set) {
			if(!$result_set) { 
				die("Database Query Failure: ". mysql_error());
			} 
			
		} 

		function mysqlPrepare($value) {
			$magic_quotes_active = get_magic_quotes_gpc();
			$hasMagic = function_exists("mysql_real_escape_string");
			if ($hasMagic) {
				if ($magic_quotes_active){
					$value = stripslashes($value);
				}
				$value = mysql_real_escape_string($value);
			} else {
				if (!$magic_quotes_active) { 
					$value = addslashes($value);
				}
			}
			return $value;
		}

		function setLogs($username, $browser_string, $session_data, $ip) { 
			global $connection;         
			$sql = "INSERT INTO logs (`id`, `timestamp`, `username`, `browser_string`, `session_data`, `ip`)
			 		VALUES (NULL, NOW() , '$username', '$browser_string', '$session_data', '$ip');";
			
			$result = mysql_query($sql);			
			database::confirmQuery($result); 		
		}
		
		function isInDB($url) {      //check url provided by user
			global $connection;
			$query = "Select * from links where destination=\"{$url}\"";
			$result  = mysql_query($query, $connection);
			database::confirmQuery($result);
	
			return mysql_num_rows($result);
		}
		
		function getAllClients($locale) {
			global $connection; 
			$sql = "SELECT * FROM xxxxx_xxxxx WHERE `locale` = $locale";
			$result = mysql_query($sql);
			database::confirmQuery($result);                                                         
			
			return $result;
			
		} 
		
	
		
		function getCampaignManagers() {
			global $connection;
			$sql = "SELECT * 
					FROM  `xxxxx_xxxxx`";                //FROM  `campaign_managers`"; 
			
			$results = mysql_query($sql);  
			
			database::confirmQuery($results);
			
			return $results;
			
		}   
		
		function getDistinctUsers() {
			global $connection;
			$sql = "SELECT DISTINCT   `username`, `password`, `user_id`, `firstname`, `lastname`, `access_level`  
					FROM  `xxxxx_xxxxx` ORDER BY  `user_id` DESC ";                //FROM  `campaign_managers`";  
			
			$results = mysql_query($sql);  
			
			database::confirmQuery($results);
			
			return $results;
		} 
		
		function getDisUsers($locale) {
			global $connection;
			$sql = "SELECT DISTINCT   `username`, `password`, `user_id`, `firstname`, `lastname`, `access_level`   
					FROM  `xxxxx_xxxxx` WHERE `locale` = '$locale'  ORDER BY  `user_id` DESC ";                //FROM  `campaign_managers`";  
			
			$results = mysql_query($sql);  
			
			database::confirmQuery($results);
			
			return $results;
		}               
		                           
		 
		function getUserRights($userid) {
			global $connection;
			$sql = "SELECT * 
					FROM  `xxxxx_xxxxx`                 
					WHERE  `user_id` = '$userid'
					ORDER BY `client_id` ASC";            //FROM  `campaign_managers`"; 
			
			$results = mysql_query($sql);  
			
			database::confirmQuery($results);
			
			return $results;
		}      
		
		function setUserRights($userid, $clientid) {
			global $connection;   
			
			$sql = "INSERT INTO  `xxxxx_xxxxx` (  `id` ,  `user_id` ,  `client_id` ) 
					VALUES (NULL ,  '$userid',  '$clientid');";   
			                    
			$results = mysql_query($sql);  

			database::confirmQuery($results);

			return $results;		
		} 
		
		function updateUserRights($userid, $clientid) {
			global $connection;   
			
			$sql = "DELETE FROM  `xxxxx_xxxxx`  
					WHERE `user_id` = '$userid' 
					AND `client_id` = '$clientid';";   
			                    
			$results = mysql_query($sql);  

			database::confirmQuery($results);

			return $results;		
		} 
		
		function updateAllUserRights($userid) {
			global $connection;   
			
			$sql = "DELETE FROM  `xxxxx_xxxxx`  
					WHERE `user_id` = '$userid';";   
			                    
			$results = mysql_query($sql);  

			database::confirmQuery($results);

			return $results;		
		} 
		
		function enableAllUserRights($userid, $clientid) {   
			database::setUserRights($userid, $clientid);			
		}  
		
		function disableAllUserRights($userid) {   
			database::updateAllUserRights($userid);			
		}
						                                                                                      
}  

Class createFolders {    
	
	function newFolderStructure($clientID,$campaignID) {
		// change this path variable to the folder these files will be kept in
		$path = "assets/";
		$old = umask(0); 
		//print mkdir($path.$clientID."/clientlogos",0777,true); 
		//print 
		mkdir($path.$clientID."/".$campaignID."/images",0777,true);   
		//print 
		mkdir($path.$clientID."/".$campaignID."/video",0777);   
		//print 
		mkdir($path.$clientID."/".$campaignID."/docs",0777); 
		//print mkdir($path.$clientID."/".$campaignID."/campaignlogo",0777); 
		umask($old); 
		
		//pre populate database fields   
		
		$db = new database();
		
	} 
	
	function file_size($size) {
	    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	    return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
	}  
	
	// make an error handler which will be used if the upload fails
	function error($error, $location, $seconds = 5) {
		header("Refresh: $seconds; URL=\"$location\"");
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'."\n".
		'"http://www.w3.org/TR/html4/strict.dtd">'."\n\n".
		'<html lang="en">'."\n".
		'	<head>'."\n".
		'		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">'."\n\n".
		'		<link rel="stylesheet" type="text/css" href="stylesheet.css">'."\n\n".
		'	<title>Upload error</title>'."\n\n".
		'	</head>'."\n\n".
		'	<body>'."\n\n".
		'	<div id="Upload">'."\n\n".
		'		<h1>Upload failure</h1>'."\n\n".
		'		<p>An error has occured: '."\n\n".
		'		<span class="red">' . $error . '...</span>'."\n\n".
		'	 	The upload form is reloading</p>'."\n\n".
		'	 </div>'."\n\n".
		'</html>';
		exit;
	} // end error handler
	
	function file_extension($filename)
	{
	    return end(explode(".", $filename));
	}	

}   

Class genYoutubeThumbnail {    
	
	function isYoutubeURL($string) {
		if(eregi('youtube', $string)) {
			return true;
		}
	}   

	function getUTubeImage($url) { 
		$a=explode('=',$url);
		$url = $a[1];
		$image = "http://img.youtube.com/vi/$url/1.jpg";
		return $image;	
	}   
	
}  


Class passwordGen {       
	
	function generatePassword ($length = 8)  {
                                            
	    $password = ""; 
	    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ"; 
	    $maxlength = strlen($possible);  
	   
	    if ($length > $maxlength) {
	      	$length = $maxlength;
	    } 
	
	    $i = 0;    
		while ($i < $length) {    
			
			 $char = substr($possible, mt_rand(0, $maxlength-1), 1); 
			 if (!strstr($password, $char)) {                                        
				$password .= $char;      
				$i++;  
			 } 

	    }

	    // done!
	    return $password;

	}
}   

Class genThumbsnail {      
	
	function create_square_image($original_file, $destination_file=NULL, $square_size = 90) { 
		//echo "<h1>Its here.....</h1>";
		
		if(isset($destination_file) and $destination_file!=NULL){
			if(!is_writable($destination_file)){
				//echo '<p style="color:#FF0000">Oops, the destination path is not writable. Make that file or its parent folder wirtable.</p>';  
				
			}
		}
	   
	$destination_file = str_replace("../..", $_SERVER['DOCUMENT_ROOT'], $destination_file); 
	$original_file = str_replace("../..", $_SERVER['DOCUMENT_ROOT'], $original_file); 
	  
	   // add shell command
	  $test = "convert '$original_file'  -strip -quality 80% -resize 90x90^  -gravity center -extent 90x90  '$destination_file'";  
      $root = $_SERVER['DOCUMENT_ROOT'];
    

	$fp = fopen('/var/www/vhosts/dpk.thinkjam.com/httpdocs/_temp/data.txt', 'w');
	fwrite($fp, $test );
	fwrite($fp, "\n\r Root: ".$root);
	fclose($fp);   
	
	$output = exec($test);
         // exec("convert assets/20th_century_fox/get_the_gringo/images/SV-04425.JPG  -strip -quality 80% -resize 90x90^  -gravity center -extent 90x90 assets/20th_century_fox/get_the_gringo/images/THUMBS2_SV-04425.JPG", $blaArray, $responsev);
	
	echo $output; 
	
	// echo "<script>javascript:alert('Generating Thumbs!');</script>";

	}   
	
	//create_square_image("sample.jpg","thumbs_sample.jpg",90);   
	
}

 
Class dirContent {  
	
	function getList($dirname) {  
		
		$currentdir = opendir($dirname);
		while(false !== ($file = readdir($currentdir))) {          

			echo "<br/> $file  		-> 		thumb_$file";    

		}
	}
}

Class getDateDiff { 

	function dateTimeDiff($data_ref)
	{ 

		date_default_timezone_set('Europe/London');

		// Get the current date
		$current_date = date('Y-m-d H:i:s');

		// Extract from $current_date
		$current_year = substr($current_date,0,4);
		$current_month = substr($current_date,5,2);
		$current_day = substr($current_date,8,2);

		// Extract from $data_ref
		$ref_year = substr($data_ref,0,4);
		$ref_month = substr($data_ref,5,2);
		$ref_day = substr($data_ref,8,2);

		// create a string yyyymmdd 20071021
		$tempMaxDate = $current_year . $current_month . $current_day;
		$tempDataRef = $ref_year . $ref_month . $ref_day;

		$tempDifference = $tempMaxDate-$tempDataRef;  
	
		if($tempDifference >= 7){                                  
			 $option = "";   
		} else {  
			 $option = "<i class=\"newasset\">( New Items )</i>";   
		} 
	
		return $option;

	}  
	
} 



?>