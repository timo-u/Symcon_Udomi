<?php

declare(strict_types=1);
    class UdomiEnergyManager extends IPSModule
    {
        public function Create()
        {
            //Never delete this line!
            parent::Create();

            $this->RegisterPropertyString('User', 'demo');
            $this->RegisterPropertyString('Password', 'demo');
            $this->RegisterPropertyString('IMEI', '357299070039042');
            $this->RegisterPropertyBoolean('Logging', false);
            $this->RegisterPropertyInteger('UpdateInterval', 3600);
			$this->RegisterPropertyInteger('ConnectionWarningInterval', 7200);
			
            $this->RegisterVariableProfiles();

            $this->RegisterVariableString('Timestamp', $this->Translate('Last Connection'),"",4);

            $this->RegisterVariableFloat('BatteryVoltage', $this->Translate('Battery Voltage'), 'Udomi_VoltageFine',10);
            $this->RegisterVariableFloat('OutputCurrent', $this->Translate('Output Current'), 'Udomi_CurrentFine',10);
            $this->RegisterVariableFloat('OperationTime', $this->Translate('Operation Time'), 'Udomi_UperationTime',50);
            $this->RegisterVariableFloat('OutputEnergy', $this->Translate('Output Energy Efoy'), 'Udomi_Energy',50);
            $this->RegisterVariableFloat('MethanolConsumed', $this->Translate('Methanol Consumed'), 'Udomi_MethanolLiter',20);
			$this->RegisterVariableFloat('TemperatureHeatsink', $this->Translate('Temperature Heatsink'), '~Temperature',30);
			$this->RegisterVariableFloat('TemperatureBattery', $this->Translate('Temperature Battery'), '~Temperature',30);
			$this->RegisterVariableFloat('ArrayVoltage', $this->Translate('Array Voltage'), 'Udomi_VoltageFine',30);
			$this->RegisterVariableFloat('SolarChargeCurrent', $this->Translate('Solar Charge Current'), 'Udomi_CurrentFine',30);
			$this->RegisterVariableFloat('SolarLoadCurrent', $this->Translate('Solar Load Current'), 'Udomi_CurrentFine',30);
			$this->RegisterVariableFloat('DaylySolarCharge', $this->Translate('Dayly Solar Charge'), 'Udomi_Energy',30);
			$this->RegisterVariableFloat('DaylyLoadCharge', $this->Translate('Dayly Load Charge'), 'Udomi_Energy',30);

			
			
            $this->RegisterVariableInteger('OperatingMode', $this->Translate('Operating Mode'), 'Udomi_OperatingMode',5);
            $this->RegisterVariableInteger('OperatingState', $this->Translate('Operating State'), 'Udomi_OperatingState',6);
            $this->RegisterVariableInteger('Cartridge', $this->Translate('Cartridge'),"",20);

            $this->RegisterVariableBoolean('HasProblem', $this->Translate('Issue'), 'Udomi_YesNo',1);
            $this->RegisterVariableBoolean('CartridgeLow', $this->Translate('Cartridge Low'), 'Udomi_CartridgeLow',3);
            $this->RegisterVariableBoolean('AlarmSolar', $this->Translate('Alarm Solar'), 'Udomi_YesNo',2);
			$this->RegisterVariableBoolean('ConnectionError', $this->Translate('Connection Error'), 'Udomi_YesNo',2);

            $this->RegisterTimer('Update', $this->ReadPropertyInteger('UpdateInterval') * 1000, 'Udomi_UpdateEnergyManager($_IPS[\'TARGET\']);');
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();
            $this->SetTimerInterval('Update', $this->ReadPropertyInteger('UpdateInterval') * 1000);

            if ($this->ReadPropertyBoolean('Logging')) {
                $archiveId = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
			
				
                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('BatteryVoltage'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('BatteryVoltage'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('BatteryVoltage'), true);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('MethanolConsumed'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('MethanolConsumed'), 1); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('MethanolConsumed'), true);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('CartridgeLow'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('CartridgeLow'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('CartridgeLow'), true);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('HasProblem'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('HasProblem'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('HasProblem'), true);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('OutputCurrent'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('OutputCurrent'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('OutputCurrent'), true);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('OperationTime'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('OperationTime'), 1); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('OperationTime'), true);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('OutputEnergy'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('OutputEnergy'), 1); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('OutputEnergy'), true);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('Cartridge'), true);
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('AlarmSolar'), true);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('TemperatureHeatsink'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('TemperatureHeatsink'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('TemperatureHeatsink'), true);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('TemperatureBattery'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('TemperatureBattery'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('TemperatureBattery'), true);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('ArrayVoltage'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('ArrayVoltage'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('ArrayVoltage'), true);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('SolarChargeCurrent'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('SolarChargeCurrent'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('SolarChargeCurrent'), true);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('SolarLoadCurrent'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('SolarLoadCurrent'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('SolarLoadCurrent'), true);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('DaylySolarCharge'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('DaylySolarCharge'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('DaylySolarCharge'), true);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('DaylyLoadCharge'), true);
                AC_SetAggregationType($archiveId, $this->GetIDForIdent('DaylyLoadCharge'), 0); // 0 Standard, 1 Zähler
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('DaylyLoadCharge'), true);
				
				
				
				
                IPS_ApplyChanges($archiveId);
            } else {
                $archiveId = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('BatteryVoltage'), false);

                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('BatteryVoltage'), false);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('MethanolConsumed'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('MethanolConsumed'), false);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('CartridgeLow'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('CartridgeLow'), false);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('HasProblem'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('HasProblem'), false);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('OutputCurrent'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('OutputCurrent'), false);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('OperationTime'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('OperationTime'), false);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('OutputEnergy'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('OutputEnergy'), false);

                AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('Cartridge'), false);
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('AlarmSolar'), false);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('TemperatureHeatsink'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('TemperatureHeatsink'), false);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('TemperatureBattery'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('TemperatureBattery'), false);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('ArrayVoltage'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('ArrayVoltage'), false);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('SolarChargeCurrent'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('SolarChargeCurrent'), false);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('SolarLoadCurrent'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('SolarLoadCurrent'), false);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('DaylySolarCharge'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('DaylySolarCharge'), false);
				
				AC_SetLoggingStatus($archiveId, $this->GetIDForIdent('DaylyLoadCharge'), false);
                AC_SetGraphStatus($archiveId, $this->GetIDForIdent('DaylyLoadCharge'), false);
				
				

                IPS_ApplyChanges($archiveId);
            }
        }

        private function RegisterVariableProfiles()
        {
            if (!IPS_VariableProfileExists('Udomi_OperatingState')) {
                IPS_CreateVariableProfile('Udomi_OperatingState', 1);
                IPS_SetVariableProfileAssociation('Udomi_OperatingState', 0, $this->Translate('Unknown'), '', -1);
                IPS_SetVariableProfileAssociation('Udomi_OperatingState', 1, $this->Translate('Auto Off'), '', 0x00FF00);
                IPS_SetVariableProfileAssociation('Udomi_OperatingState', 2, $this->Translate('Auto On'), '', 0x00FF0000);
                IPS_SetVariableProfileAssociation('Udomi_OperatingState', 3, $this->Translate('Off'), '', 0xFF0000);
                IPS_SetVariableProfileAssociation('Udomi_OperatingState', 4, $this->Translate('On'), '', 0xFF0000);
            }

            if (!IPS_VariableProfileExists('Udomi_OperatingMode')) {
                IPS_CreateVariableProfile('Udomi_OperatingMode', 1);
                IPS_SetVariableProfileAssociation('Udomi_OperatingMode', 0, $this->Translate('Unknown'), '', -1);
                IPS_SetVariableProfileAssociation('Udomi_OperatingMode', 1, $this->Translate('Auto'), '', 0x00FF00);
                IPS_SetVariableProfileAssociation('Udomi_OperatingMode', 2, $this->Translate('On'), '', 0xFFFF00);
                IPS_SetVariableProfileAssociation('Udomi_OperatingMode', 3, $this->Translate('Off'), '', 0xFF0000);
            }
            if (!IPS_VariableProfileExists('Udomi_MethanolLiter')) {
                IPS_CreateVariableProfile('Udomi_MethanolLiter', 2);
                IPS_SetVariableProfileDigits('Udomi_MethanolLiter', 2);
                IPS_SetVariableProfileText('Udomi_MethanolLiter', '', ' l');
            }
            if (!IPS_VariableProfileExists('Udomi_VoltageFine')) {
                IPS_CreateVariableProfile('Udomi_VoltageFine', 2);
                IPS_SetVariableProfileDigits('Udomi_VoltageFine', 2);
                IPS_SetVariableProfileText('Udomi_VoltageFine', '', ' V');
            }
            if (!IPS_VariableProfileExists('Udomi_CurrentFine')) {
                IPS_CreateVariableProfile('Udomi_CurrentFine', 2);
                IPS_SetVariableProfileDigits('Udomi_CurrentFine', 2);
                IPS_SetVariableProfileText('Udomi_CurrentFine', '', ' A');
            }
            if (!IPS_VariableProfileExists('Udomi_Energy')) {
                IPS_CreateVariableProfile('Udomi_Energy', 2);
                IPS_SetVariableProfileDigits('Udomi_Energy', 2);
                IPS_SetVariableProfileText('Udomi_Energy', '', ' Wh');
            }

            if (!IPS_VariableProfileExists('Udomi_UperationTime')) {
                IPS_CreateVariableProfile('Udomi_UperationTime', 2);
                IPS_SetVariableProfileDigits('Udomi_UperationTime', 1);
                IPS_SetVariableProfileText('Udomi_UperationTime', '', ' h');
            }
            if (!IPS_VariableProfileExists('Udomi_CartridgeLow')) {
                IPS_CreateVariableProfile('Udomi_CartridgeLow', 0);
                IPS_SetVariableProfileAssociation('Udomi_CartridgeLow', 0, $this->Translate('OK'), '', 0x00FF00);
                IPS_SetVariableProfileAssociation('Udomi_CartridgeLow', 1, $this->Translate('Low'), '', 0xFF0000);
            }
            if (!IPS_VariableProfileExists('Udomi_YesNo')) {
                IPS_CreateVariableProfile('Udomi_YesNo', 0);
                IPS_SetVariableProfileAssociation('Udomi_YesNo', 0, $this->Translate('No'), '', 0x00FF00);
                IPS_SetVariableProfileAssociation('Udomi_YesNo', 1, $this->Translate('Yes'), '', 0xFF0000);
            }
        }

        public function UpdateEnergyManager()
        {
            $user = $this->ReadPropertyString('User');
            $pass = $this->ReadPropertyString('Password');
            $imei = $this->ReadPropertyString('IMEI');

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
                IPS_LogMessage('Symcon_Udomi', 'Error: '.$this->Translate($obj['message']));
                $this->SetStatus(202); // Authentication failed
                return;
            }

            $token = $obj['token'];

            $curl = curl_init();

            curl_setopt_array($curl, [
            CURLOPT_URL            => "https://www.m2mgate.de/udomi/rest/api/energymanager/$imei",
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
            IPS_LogMessage('Symcon_Udomi', $response);

            SetValue($this->GetIDForIdent('BatteryVoltage'), $obj['battery_voltage_efoy']);
            SetValue($this->GetIDForIdent('MethanolConsumed'), $obj['methanol_consumed_efoy']);
            SetValue($this->GetIDForIdent('CartridgeLow'), $obj['cartridge_low_efoy']);
            SetValue($this->GetIDForIdent('HasProblem'), ($obj['error_efoy'] != 'no error' || $obj['cartridge_low_efoy'] != 0));

            SetValue($this->GetIDForIdent('OutputCurrent'), $obj['output_current_efoy']);
            SetValue($this->GetIDForIdent('OperationTime'), $obj['operation_time_efoy']);
            SetValue($this->GetIDForIdent('Timestamp'), $obj['timestamp']);
            SetValue($this->GetIDForIdent('Cartridge'), $obj['cartridge_efoy']);
            SetValue($this->GetIDForIdent('OutputEnergy'), $obj['cumulative_output_energy_efoy']);

			SetValue($this->GetIDForIdent('TemperatureHeatsink'), $obj['t_heatsink_solar']);
			SetValue($this->GetIDForIdent('TemperatureBattery'), $obj['t_batt_solar']);
			SetValue($this->GetIDForIdent('ArrayVoltage'), $obj['array_voltage_solar']);
			SetValue($this->GetIDForIdent('SolarChargeCurrent'), $obj['i_charge_solar']);
			SetValue($this->GetIDForIdent('SolarLoadCurrent'), $obj['i_load_solar']);
			SetValue($this->GetIDForIdent('DaylySolarCharge'), $obj['charge_daily_solar']);
			SetValue($this->GetIDForIdent('DaylyLoadCharge'), $obj['load_daily_solar']);
			SetValue($this->GetIDForIdent('AlarmSolar'), $obj['alarm_solar']);
			
            $state = 0;
            switch ($obj['operating_state_efoy']) {
    case 'auto off':
        $state = 1;
        break;
    case 'auto on':
        $state = 2;
        break;
    case 'off':
        $state = 3;
        break;
    case 'on':
        $state = 4;
        break;
    default:
        echo $obj['operating_state_efoy'];
}

            $mode = 0;
            switch ($obj['operating_mode_efoy']) {
    case 'auto':
        $mode = 1;
        break;
    case 'on':
        $mode = 2;
        break;
    case 'off':
        $mode = 3;
        break;
    default:
        echo $obj['operating_mode_efoy'];
}

            SetValue($this->GetIDForIdent('OperatingState'), $state);
            SetValue($this->GetIDForIdent('OperatingMode'), $mode);

			$difference =  time() -strtotime($obj['timestamp']);
			SetValue($this->GetIDForIdent('ConnectionError'), ($difference > $this->ReadPropertyInteger('ConnectionWarningInterval'))&& $this->ReadPropertyInteger('ConnectionWarningInterval')> 0 );
			
            /*
            Sample:

            "imei": "357299070039042",
			"alias": "udomi - LTE - 39042",
			"timestamp": "2018-09-24 09:40:37",
			"battery_voltage_efoy": 13.22,
			"output_current_efoy": 0,
			"operation_time_efoy": 7915.3,
			"operating_state_efoy": "auto off",
			"operating_mode_efoy": "auto",
			"cumulative_output_energy_efoy": 454058,
			"cartridge_efoy": "1",
			"cartridge_low_efoy": 0,
			"methanol_consumed_efoy": 2.55,
			"error_efoy": "no error",
			"battery_voltage_solar": 13.28,
			"array_voltage_solar": 14.22,
			"load_voltage_solar": 0,
			"u_lvd_solar": 12,
			"i_charge_solar": 0,
			"i_load_solar": 0,
			"t_heatsink_solar": 11,
			"t_batt_solar": 12,
			"status_charge_solar": 5,
			"status_load_solar": 5,
			"alarm_solar": 1,
			"charge_daily_solar": 0,
			"load_daily_solar": 0
            */

            $this->SetStatus(102); // Instanz aktiv
        }
    }
