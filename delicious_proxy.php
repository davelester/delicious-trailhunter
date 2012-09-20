<?php
        class DeliciousProxy {
			var $username = 'iolabtrailhunter'; 
			var $password = 'bobglushko';
			var $key = 'oeWKP--W6XeYKRHrgeKVvzBUzhucluq_ZBdxbKlhupU=';
			var $url = '';
			
			function __construct($theUrl) {
			   $this->url = $theUrl;
		    }
		   
		    function get_url() {
				return $this->url;
		    }
			
		    function set_url($newUrl) {
				$this->url = $newUrl;
		    }
        
			function authenticated_post($postData = "") {
                $process = curl_init($this->url);
				curl_setopt($process, CURLOPT_USERPWD, $this->username . ":" . $this->password);
				curl_setopt($process, CURLOPT_TIMEOUT, 30);
				curl_setopt($process, CURLOPT_POST, 1);
				curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($process, CURLOPT_POSTFIELDS, $postData);
				curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				$jsonData = curl_exec($process);
				return json_decode($jsonData, true);
            }
			
			function public_get($isPrivate) {
			
				$newUrl = $this->url . $this->username;
				if ($isPrivate) {
					$newUrl = $newUrl . '?private=' . $this->KEY;
				} 
				$jsonData = file_get_contents($newUrl);
				return json_decode($jsonData);
			}
		
		
		}
?>