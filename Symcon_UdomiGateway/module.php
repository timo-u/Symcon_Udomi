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
			$this->SendDebug("ApplyChanges()", "Save settings and reconnect.", 0);
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
                $this->SetStatus(201);
				$this->SendDebug("Connect()", 'cURL Error #:'.$err, 0);
                return;
            }

            $obj = json_decode($response, true);

            if (array_key_exists('type', $obj) && $obj['type'] == 'error') {
                IPS_LogMessage('Symcon_Udomi', 'Fehler: '.$this->Translate($obj['message']));
				$this->SendDebug("Connect()", 'Error: '.$obj['message'], 0);
				$this->SendDebug("Connect()", "Token=> ", 0);
				$this->SetBuffer("Token", "");
                $this->SetStatus(202); // Authentication failed
                return false;
            }

            $token = $obj['token'];
			$this->SetBuffer("Token", $token);
			$this->SendDebug("Connect()", "Connection successfull: Token=> ". $token, 0);
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
				$this->SendDebug("GetDevices()", "StatusCode=> ". $http_code, 0);
				if(!$err)
				{
					$err ="HTTP StatusCode: ".$http_code;
				}
			}
			}
			
			curl_close($curl);
			
            if($err) 
			{
               
				$this->SendDebug("GetDevices()", "cURL Error=> ". $err, 0);
				if ($this->Connect())
				{
					continue;
	
                }	
				return;
            }

            $obj = json_decode($response, true);

            
			if (array_key_exists('type', $obj) && $obj['type'] == 'error') {
				
				$this->SendDebug("GetDevices()", 'Error: '.$obj['message'], 0);
                
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

			$this->SendDebug("GetDevices()", "Response => ". $response, 0);
			
			return $obj;
            
        }
		}
		
		public function ForwardData($JSONString)
        {
			$this->SendDebug("ForwardData()", $JSONString, 0);
			// Receive data from child
            $data = json_decode($JSONString);
			$data = $data->Buffer;
            $action = $data->action;
			$imei = $data->imei;
            return $this->GetData($action,$imei);
        }
		
		public function GetData(string $action, string $imei)
        {
           for ($i = 1; $i <= 3; $i++) 
			{
			if($i>1)		
			{
				$this->SendDebug("GetData()", $i . " run", 0);
			}
			
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
                $this->SendDebug("GetData()", 'cURL Error #:'.$err, 0);
				$data = [
					'error' => 'cURL Error #:'.$err,
					'imei'=> $imei,
					'response'=> null
					];
				$this->SendDataToChildren(json_encode(['DataID' => '{50E8C73F-2C16-4CBB-A484-AEEA1DDFE52F}', 'Buffer' => $data]));
                return;
            }

            $obj = json_decode($response, true);

            if (array_key_exists('type', $obj) && $obj['type'] == 'error') {
				if($obj['message'] == "The used token is invalid.")
				{
					$this->SendDebug("GetData()", "The used token is invalid. Reconnect and retry API-call.", 0);     
					if ($this->Connect())
						{
							continue;
						}					
				}
				
				$this->SendDebug("GetData()", 'Error: '.$obj['message'], 0);           
				$data = [
					'error' => $obj['message'],
					'imei'=> $imei,
					'response'=> null
					];
				$this->SendDataToChildren(json_encode(['DataID' => '{50E8C73F-2C16-4CBB-A484-AEEA1DDFE52F}', 'Buffer' => $data]));
                return;
            }
			$data = [
			'error' => null,
            'response' => $obj,
			'imei'=> $imei
            ];
			
			$this->SendDebug("GetData()", "SendDataToChildren Data: ". $response, 0);
			$this->SendDataToChildren(json_encode(['DataID' => '{50E8C73F-2C16-4CBB-A484-AEEA1DDFE52F}', 'Buffer' => $data]));
			return;
            }

        }
		
    }
