<?php

declare(strict_types=1);
    class UdomiGateway extends IPSModule
    {
        public function Create()
        {
            //Never delete this line!
            parent::Create();

            $this->RegisterPropertyString('User', 'demo');
            $this->RegisterPropertyString('Password', 'demo');
           
 }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges(); 
            $this->Connect();
        }

       

        public function Connect()
        {
            $user = $this->ReadPropertyString('User');
            $pass = $this->ReadPropertyString('Password');
           

            $curl = curl_init();

            curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.m2mgate.de/udomi/rest/api/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => "user=$user&pass=$pass",
            CURLOPT_HTTPHEADER     => [
            'Cache-Control: no-cache',
            'Content-Type: application/x-www-form-urlencoded',
            ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo 'cURL Error #:'.$err;
                $this->SetStatus(201);

                return;
            }

            $obj = json_decode($response, true);

            if (array_key_exists('type', $obj) && $obj['type'] == 'error') {
                echo $this->Translate($obj['message']);
                IPS_LogMessage('Symcon_Udomi', 'Fehler: '.$this->Translate($obj['message']));
				$this->SendDebug("Connect", 'Error: '.$obj['message'], 0);
				$this->SendDebug("Connect", "Token=> ", 0);
				$this->SetBuffer("Token", "");
                $this->SetStatus(202); // Authentication failed
                return false;
            }

            $token = $obj['token'];
			
			$this->SetBuffer("Token", $token);
			$this->SendDebug("Connect", "Token=> ". $token, 0);
 
            $this->SetStatus(102); // Instanz aktiv
			return true;
        }
		
		 public function GetDevices()
        {
			for ($i = 1; $i <= 3; $i++) 
			{
				
			$token = $this->GetBuffer("Token");
		   
            $curl = curl_init();

            curl_setopt_array($curl, [
            CURLOPT_URL            => "https://www.m2mgate.de/udomi/rest/api/devices",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => "token=$token",
            CURLOPT_HTTPHEADER     => [
                'Cache-Control: no-cache',
                'Content-Type: application/x-www-form-urlencoded',
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            

			if (!curl_errno($curl)) {
				$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if($http_code !=200) 
			{
				$this->SendDebug("GetDevices", "StatusCode=> ". $http_code, 0);
				if(!$err)
				{
					$err ="HTTP StatusCode: ".$http_code;
				}
			}
			}
			
			curl_close($curl);
			
            if($err) 
			{
                
                $this->SetStatus(201);
				$this->SendDebug("GetDevices", "cURL Error=> ". $err, 0);
				if ($this->Connect())
				{
					continue;
	
                }	
				echo 'cURL Error #:'.$err;
				return;
            }

            $obj = json_decode($response, true);

            
			if (array_key_exists('type', $obj) && $obj['type'] == 'error') {
				
				$this->SendDebug("GetDevices", 'Error: '.$obj['message'], 0);
                
				if ($obj['message'] == 'The used token is invalid.') {
                    if($run>0)
					{
						
					$this->SetStatus(201);
					}
					else
					{
						if ($this->Connect())
						{
							continue;
						}
					}	
                }
                return; 
            }
		
            print_r( $obj );
			
            IPS_LogMessage('Symcon_UdomiGateway', $response);
			$this->SendDebug("GetDevices", "Response => ". $response, 0);
			
			return $obj;
            
        }
		}
		
		public function GetData(string $action)
        {
           for ($i = 1; $i <= 3; $i++) 
			{
			$token = $this->GetBuffer("Token");
		   
			
            $curl = curl_init();

            curl_setopt_array($curl, [
            CURLOPT_URL            => "https://www.m2mgate.de/udomi/rest/api/".$action,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => "token=$token",
            CURLOPT_HTTPHEADER     => [
                'Cache-Control: no-cache',
                'Content-Type: application/x-www-form-urlencoded',
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo 'cURL Error #:'.$err;
                $this->SetStatus(201);

                return;
            }

            $obj = json_decode($response, true);

            if (array_key_exists('type', $obj) && $obj['type'] == 'error') {
                echo $obj['message'];
                IPS_LogMessage('Symcon_Udomi', 'Error: '.$obj['message']);
                if ($obj['message'] == 'IMEI is not assigned to user or does not exist.') {
                    $this->SetStatus(203);
                } // IMEI is not assigned to user or does not exist.
                else {
                    $this->SetStatus(204);
                } // IMEI is not assigned to user or does not exist.
                return;
            }

            //print_r( $obj );
            IPS_LogMessage('Symcon_UdomiGateway', $response);
			return;
            }

        }
		
    }
