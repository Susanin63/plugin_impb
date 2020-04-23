<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright (C) 2007 Susanin                                          |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
 */
 
 
 /* register this functions scanning functions */
 if (!isset($impb_scanning_functions)) { $impb_scanning_functions = array(); }
 array_push($impb_scanning_functions, "scan_dlink_universal");
 
 
 /*	get_generic_switch_ports - This is a basic function that will scan the dot1d
   OID tree for all switch port to MAC address association and stores in the
   mac_track_temp_ports table for future processing in the finalization steps of the
   scanning process.
 */
 function scan_dlink_universal($device, $imb_debug = 0) {
 	global $scan_date;
 
	/* initialize port counters */
	$device['ports_total'] = 0;
	$device["ports_active"] = 0;
 	$device["ports_trunk"] = 0;
     $ports_total=0;
     $ports_active=0;
     $ports_enable_total=0;
 	$ports_enable_zerroip_total=0;
     $store_to_db = TRUE;
	 $imb_debug = 0;
 	
	
	$device["device_type_global"] =  db_fetch_row ("SELECT * FROM imb_device_types WHERE device_type_id=" . $device["device_type_id"] . ";");
 	
 	/* get the ifIndexes for the device */
 	$ifIndexes = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_ifIndex"], $device);
 	//impb_debug("ifIndexes data collection complete" . sizeof($ifIndexes));
 
     /* get the portstate for the device */
    if ((isset($device["device_type_global"]["snmp_oid_MacBindingPortState"])) && (trim($device["device_type_global"]["snmp_oid_MacBindingPortState"]) <> "")){
		$ifStates = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_MacBindingPortState"], $device);
 	}else{
 		$ifStates = array_fill_keys($ifIndexes, '0');
 	} 
     //impb_debug("PortStates data collection complete." . sizeof($ifStates));

      /* get the port admin state for the device */
    if ((isset($device["device_type_global"]["snmp_oid_swL2PortCtrlAdminState"])) && (trim($device["device_type_global"]["snmp_oid_swL2PortCtrlAdminState"]) <> "")){
		$ifAdminStates = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_swL2PortCtrlAdminState"], $device);
		$ifAdminStates=dimpb_array_compress_strip($ifAdminStates);
 	}else{
 		$ifAdminStates = array_fill_keys($ifIndexes, '0');
 	} 

      /* get the port admin speed for the device */
    if ((isset($device["device_type_global"]["snmp_oid_swL2PortCtrlSpeedState"])) && (trim($device["device_type_global"]["snmp_oid_swL2PortCtrlSpeedState"]) <> "")){
		$ifAdminSpeeds = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_swL2PortCtrlSpeedState"], $device);
		$ifAdminSpeeds=dimpb_array_compress_strip($ifAdminSpeeds);
 	}else{
 		$ifAdminSpeeds = array_fill_keys($ifIndexes, '0');
 	} 	

      /* get the port  speed for the device */
    if ((isset($device["device_type_global"]["snmp_oid_swL2PortSpeedStatus"])) && (trim($device["device_type_global"]["snmp_oid_swL2PortSpeedStatus"]) <> "")){
		$ifSpeeds = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_swL2PortSpeedStatus"], $device);
		$ifSpeeds=dimpb_array_compress_strip($ifSpeeds);
 	}else{
 		$ifSpeeds = array_fill_keys($ifIndexes, '0');
 	} 

      /* get the port  loop setings  for the device */
    if ((isset($device["device_type_global"]["snmp_oid_swL2LoopDetectPortState"])) && (trim($device["device_type_global"]["snmp_oid_swL2LoopDetectPortState"]) <> "")){
 	$ifLoopStates = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_swL2LoopDetectPortState"], $device);
     //impb_debug("PortZerroIPStates data collection complete." . sizeof($ifZerroIPStates));
 	}else{
 		$ifLoopStates = array_fill_keys($ifIndexes, '0');
 	}

      /* get the port  loop setings  for the device */
    if ((isset($device["device_type_global"]["swL2PortErrPortReason"])) && (trim($device["device_type_global"]["swL2PortErrPortReason"]) <> "")){
 	$ifErrorReasons = dimpb_xform_standard_indexed_data($device["device_type_global"]["swL2PortErrPortReason"], $device);
     //impb_debug("PortZerroIPStates data collection complete." . sizeof($ifZerroIPStates));
 	}else{
 		$ifErrorReasons = array_fill_keys($ifIndexes, '0');
 	}
	
      /* get the port  loop setings  for the device */
    if ((isset($device["device_type_global"]["snmp_oid_swL2LoopDetectPortLoopVLAN"])) && (trim($device["device_type_global"]["snmp_oid_swL2LoopDetectPortLoopVLAN"]) <> "")){
 	$ifLoopVlans = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_swL2LoopDetectPortLoopVLAN"], $device);
     //impb_debug("PortZerroIPStates data collection complete." . sizeof($ifZerroIPStates));
 	}else{
 		$ifLoopVlans = array_fill_keys($ifIndexes, '0');
 	}	
	
      /* get the portstate for the device */
    if ((isset($device["device_type_global"]["snmp_oid_en_MacBindingZerroIpPortState"])) && (trim($device["device_type_global"]["snmp_oid_en_MacBindingZerroIpPortState"]) <> "")){
 	$ifZerroIPStates = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_en_MacBindingZerroIpPortState"], $device);
     //impb_debug("PortZerroIPStates data collection complete." . sizeof($ifZerroIPStates));
 	}else{
 		$ifZerroIPStates = array_fill_keys($ifIndexes, '0');
 	}   
      /* get the portstate for the device */
     if ((isset($device["device_type_global"]["snmp_oid_en_fwd_dhcp_packets_state"])) && (trim($device["device_type_global"]["snmp_oid_en_fwd_dhcp_packets_state"]) <> "")){
 	$ifDHCPFwdPcktsStates = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_en_fwd_dhcp_packets_state"], $device);
     //impb_debug("PortDHCPFwdPckts states data collection complete." . sizeof($ifDHCPFwdPcktsStates));
 	}else{
 		$ifDHCPFwdPcktsStates = array_fill_keys($ifIndexes, '0');
 	}  	
      /* get the portstate for the device */
     if ((isset($device["device_type_global"]["snmp_oid_max_entry_count"])) && (trim($device["device_type_global"]["snmp_oid_max_entry_count"]) <> "")){
 	$ifMaxEntryCount = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_max_entry_count"], $device);
     //impb_debug("ifMaxEntryCount states data collection complete." . sizeof($ifMaxEntryCount));
 	}else{
 		$ifMaxEntryCount = array_fill_keys($ifIndexes, '0');
 	}

//DGS
      /* get the portstate for the device */
    if ((isset($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortARPInspection"])) && (trim($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortARPInspection"]) <> "")){
 	$ifARPIspections = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortARPInspection"], $device);
     //impb_debug("PortZerroIPStates data collection complete." . sizeof($ifARPIspections));
 	}else{
 		$ifARPIspections = array_fill_keys($ifIndexes, '0');
 	}
      /* get the portstate for the device */
    if ((isset($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortIPInspection"])) && (trim($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortIPInspection"]) <> "")){
 	$ifIPIspections = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortIPInspection"], $device);
     //impb_debug("PortZerroIPStates data collection complete." . sizeof($ifIPIspections));
 	}else{
 		$ifIPIspections = array_fill_keys($ifIndexes, '0');
 	}
      /* get the portstate for the device */
    if ((isset($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortIPProtocol"])) && (trim($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortIPProtocol"]) <> "")){
 	$ifIPProtocols = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_en_swIpMacBindingPortIPProtocol"], $device);
     //impb_debug("PortZerroIPStates data collection complete." . sizeof($ifIPProtocols));
 	}else{
 		$ifIPProtocols = array_fill_keys($ifIndexes, '0');
 	}	
 	
     /* get the ifNames for the device */
     $ifNames = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_ifAlias"], $device);
     //impb_debug("ifNames data collection complete." . sizeof($ifNames));
         
                          
     /* get the ifTypes for the device */
     $ifTypes = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_ifType"], $device);
     //impb_debug("ifTypes data collection complete." . sizeof($ifTypes));
     
     $ifInterfaces=array();
 
     foreach($ifIndexes as $ifIndex) {
         $ifInterfaces[$ifIndex]["ifIndex"] = $ifIndex;
         $ifInterfaces[$ifIndex]["ifName"] = @$ifNames[$ifIndex];
         if (isset($ifStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifState"] = $ifStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifState"] = '0';
         }

         if (isset($ifLoopStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifLoopState"] = $ifLoopStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifLoopState"] = '0';
         }

         if (isset($ifLoopVlans[$ifIndex])) {
             if ($ifLoopVlans[$ifIndex] == "None") {
				$ifInterfaces[$ifIndex]["ifLoopVlan"] = "0";
			 }else{
				$ifInterfaces[$ifIndex]["ifLoopVlan"] = $ifLoopVlans[$ifIndex];
			 }
         } else {
             $ifInterfaces[$ifIndex]["ifLoopVlan"] = '0';
         }
		 
         if (isset($ifErrorReasons[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifErrorReason"] = $ifErrorReasons[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifErrorReason"] = '0';
         }

         if (isset($ifAdminSpeeds[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifAdminSpeed"] = $ifAdminSpeeds[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifAdminSpeed"] = '0';
         }		 

         if (isset($ifSpeeds[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifSpeed"] = $ifSpeeds[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifSpeed"] = '0';
         }		 
		 
         if (isset($ifAdminStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifAdminState"] = $ifAdminStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifAdminState"] = '0';
         }
		 
         if (isset($ifZerroIPStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifZerroIpState"] = $ifZerroIPStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifZerroIpState"] = '0';
         }

         if (isset($ifDHCPFwdPcktsStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifDHCPFwdPcktsStates"] = $ifDHCPFwdPcktsStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifDHCPFwdPcktsStates"] = '0';
         }
 		
         if (isset($ifMaxEntryCount[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifMaxEntryCount"] = $ifMaxEntryCount[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifMaxEntryCount"] = '0';
         }

         if (isset($ifARPIspections[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifARPIspetion"] = $ifARPIspections[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifARPIspetion"] = '0';
         }
         if (isset($ifIPIspections[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifIPIspetion"] = $ifIPIspections[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifIPIspetion"] = '0';
         }
         if (isset($ifIPProtocols[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifIPProtocol"] = $ifIPProtocols[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifIPProtocol"] = '0';
         }
		 
         if (isset($ifTypes[$ifIndex])) {
             if (substr_count($ifTypes[$ifIndex], "(")>0) {
                $ifInterfaces[$ifIndex]["ifType"] = substr ( $ifTypes[$ifIndex], strpos($ifTypes[$ifIndex], "(" )+1, (strpos( $ifTypes[$ifIndex], ")" ) - strpos( $ifTypes[$ifIndex], "(") )-1 );
             }else{
             $ifInterfaces[$ifIndex]["ifType"] = $ifTypes[$ifIndex];
             }
         }else{
             $ifInterfaces[$ifIndex]["ifType"] = '0';
         }
         if (isset($ifStates[$ifIndex])) {
             if ($ifStates[$ifIndex] == 2) {
                 $ports_enable_total=$ports_enable_total+1;
             }
         }
 		if ($ifInterfaces[$ifIndex]["ifZerroIpState"] == 1) {
             $ports_enable_zerroip_total=$ports_enable_zerroip_total+1;
         }
     }    
    // impb_debug("ifInterfaces assembly complete. - " . sizeof($ifInterfaces));
     /* get the vlans data for the device */
    $vlans = impb_standard_indexed_data_oid(".1.3.6.1.2.1.17.7.1.4.3.1", $device);
	foreach($vlans as $key => $v) {
		if (substr($key, 0, 2) == "1.") {
			$vl_id=substr($key, 2);
			$vl[$vl_id]["id"] = $vl_id;
			$vl[$vl_id]["name"] = $vlans["1." . $vl_id]; //descr
			$vl[$vl_id]["m_p"]  = substr(str_replace(" ","",str_replace(":","",$vlans["2." . $vl_id])),0,32); //member port
			$vl[$vl_id]["u_p"]  = substr(str_replace(" ","",str_replace(":","",$vlans["4." . $vl_id])),0,32); //untagged ports
			if (isset($vlans["3." . $vl_id])){
				$vl[$vl_id]["f_p"]  = substr(str_replace(" ","",str_replace(":","",$vlans["3." . $vl_id])),0,32); //forbid ports
			}else{
				$vl[$vl_id]["f_p"]  = '0'; //forbid ports
			}
			$vl[$vl_id]["t_p"]  = substr(strtoupper(str_pad(base_convert(member_ports($vl[$vl_id]["m_p"]) & impb_invert_string(member_ports($vl[$vl_id]["u_p"])),2,16) , strlen($vl[$vl_id]["m_p"]) , "0" , STR_PAD_LEFT)),0,32); //tagged ports
			$vl[$vl_id]["a_t"]  = array_merge(array(0=>"0"),str_split(member_ports($vl[$vl_id]["t_p"])));
			$vl[$vl_id]["a_u"]  = array_merge(array(0=>"0"),str_split(member_ports($vl[$vl_id]["u_p"])));
			$vl[$vl_id]["u_pl"] = convert_Xport_to_view_string($vlans["4." . $vl_id], $device["device_type_global"]["type_port_num_conversion"] ); //untagged ports list
		}
	}    
	
     
     
     /* get the operational status of the ports */
     $active_ports_array = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_ifOperStatus"], $device);
     $indexes = array_keys($active_ports_array);
 
     $i = 1;
     $ifInterfaces_temp=array();
     foreach($ifInterfaces as $ifInterface) {
         if (isset($ifInterfaces[$indexes[$i-1]]))  {        
         if ((($ifInterfaces[$indexes[$i-1]]["ifType"] >= 6) &&
             ($ifInterfaces[$indexes[$i-1]]["ifType"] <= 9)) ||
 			($ifInterfaces[$indexes[$i-1]]["ifType"] == 117))  {
             if (substr_count($active_ports_array[$i], "(")>0) {
               $active_ports_array[$i] = substr ( $active_ports_array[$i], strpos($active_ports_array[$i], "(" )+1, (strpos( $active_ports_array[$i], ")" ) - strpos( $active_ports_array[$i], "(") )-1 );
               }
             if ($active_ports_array[$i] == 1) {
                 $ports_active++;
                 $ifInterfaces[$indexes[$i-1]]["ifActive"] = "1";
       
             }else{
                 $ifInterfaces[$indexes[$i-1]]["ifActive"] = "0";
             }
             $ports_total++;
             $ifInterfaces_temp[$indexes[$i-1]] = $ifInterfaces[$indexes[$i-1]]; 


			//untagged vlans
			$ifInterfaces_temp[$indexes[$i-1]]["unt_vl"] = "0";
			$ar_unt=array();
			foreach($vl as $id => $v) {
				if ($v["a_u"][$indexes[$i-1]] == "1"){
					$ar_unt[] = $id;
				}
			}
			if (count($ar_unt) > 0){
				$ifInterfaces_temp[$indexes[$i-1]]["unt_vl"] = implode(",", $ar_unt);
			}
			//tagged vlans
			$ifInterfaces_temp[$indexes[$i-1]]["t_vl"] = "0";
			$ar_t=array();
			foreach($vl as $id => $v) {
				if ($v["a_t"][$indexes[$i-1]] == "1"){
					$ar_t[] = $id;
				}
			}
			if (count($ar_t) > 0){
				$ifInterfaces_temp[$indexes[$i-1]]["t_vl"] = implode(",", $ar_t);
			}
			 
		 
         }
             $i++;
         }
     }
     $ifInterfaces = $ifInterfaces_temp;
     
     impb_debug("INFO: HOST: " . $device["hostname"] . ", TYPE: " . substr($device["snmp_sysDescr"],0,40) . ", TOTAL PORTS: " . $ports_total . ", OPER PORTS: " . $ports_active);
 
         
     /* get the ifMacs index for the device */
    $macIps = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingIpIndex"], $device);
     //impb_debug("ifMacsIP index data collection for device [" . $device["hostname"]  . "] complete." . sizeof($macIps));
	
	$macIDs = array_keys($macIps);
 	
     /* get the ifMacs for the device */
     $macMacs = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingMac"], $device);
     //impb_debug("ifMacs data collection for device [" . $device["hostname"]  . "] complete." . sizeof($macMacs));
	//$keys = array_keys($macMacs);
	$i = 0;	
	foreach($macMacs as $atAddress) {
		$macMacs[$macIDs[$i]] = dimpb_xform_mac_address($atAddress);
		$i++;
	}     
         /* get the ifMac status state for the device */
     $macStatus = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingStatus"], $device);
    // impb_debug("ifMacsState data collection for device [" . $device["hostname"]  . "] complete." . sizeof($macStatus));
     
     /* get the ifMacs port for the device */
     $macPort = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingPorts"], $device);
     //impb_debug("ifMacs port data collection for device [" . $device["hostname"]  . "] complete." . sizeof($macPort));
 
         /* get the ifMac action state for the device */
    if ((isset($device["device_type_global"]["snmp_oid_MacBindingAction"])) && (trim($device["device_type_global"]["snmp_oid_MacBindingAction"]) <> "")){
		//disable by incorrect value error in MySQL
		//$macAction = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingAction"], $device);
		//impb_debug("ifMacsState data collection for device [" . $device["hostname"]  . "] complete." . sizeof($macAction));
		$macAction = array_fill_keys($macIDs, '-1');
 	}else{
 		$macAction = array_fill_keys($macIDs, '-1');
 	}    
 	
     /* get the ifMacs modefor the device */
     if ((isset($device["device_type_global"]["snmp_oid_MacBindingMode"])) && (trim($device["device_type_global"]["snmp_oid_MacBindingMode"]) <> "")){
 		$macMode = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingMode"], $device);
 	    //impb_debug("ifMacs mode data collection for device [" . $device["hostname"]  . "] complete." . sizeof($macMode));    
 	}else{
 		#default mode ARP.
 		if (isset($device["device_type_global"]["type_imb_mode"])) {
 			if (trim($device["device_type_global"]["type_imb_mode"]) == 1) {
 				$macMode = array_fill_keys($macIDs, '1');
 			}else{
 				$macMode = array_fill_keys($macIDs, '0');
 			}
 		}else{
 			$macMode = array_fill_keys($macIDs, '0');
 		}
 	}
 	
    $macAddress=array();
    foreach($macIDs as $macID) {
 			
        $macAddress[$macID]["macIndex"] = $macID;
		if (filter_var($macIps[$macID], FILTER_VALIDATE_IP)) {
			$macAddress[$macID]["macIp"] = $macIps[$macID]; 
		}else{		
			if (filter_var(long2ip(hexdec($macIps[$macID])), FILTER_VALIDATE_IP)){
				$macAddress[$macID]["macIp"] = long2ip(hexdec($macIps[$macID]));  
			}else{
				$macAddress[$macID]["macIp"] = $macIps[$macID];  
			}
		}
        $macAddress[$macID]["macMac"] = strtolower(str_replace("HEX-00:", "", $macMacs[$macID]));
        $macAddress[$macID]["macStatus"] = $macStatus[$macID];
 		$macAddress[$macID]["macAction"] = $macAction[$macID];
        $macAddress[$macID]["macPort"] = str_replace(":", "", str_replace(" ","",str_replace("Hex- ", "", $macPort[$macID])));
   		$arr_ports = convert_Xport_to_view_string($macAddress[$macID]["macPort"], $device["device_type_global"]["type_port_num_conversion"] );
 		$macAddress[$macID]["macPortList"] = $arr_ports["port_list"];
 		$macAddress[$macID]["macPortListView"] = $arr_ports["port_view"];
 		$macAddress[$macID]["macMode"] = $macMode[$macID];
 
 		//print_r($macAddress);
 		//impb_debug ("----->" . $macAddress[$macIp]["macIndex"] . "_" . $macIps[$macIp["oid"]]["value"] . "_" . $macIps[$macIp] . "_/n");
     }  
     
 	
 	//$device["device_type_global"]["snmp_oid_ifOperStatus"]
 	
 	//print_r($macAddress);  
     //impb_debug("macMacIp for device [" . $device["hostname"]  . "] assembly complete. Size=[" . sizeof($macAddress) . "]");    
 

     $blMacs=array();
	 
	 /* get the blocked mac index for the device */
     $blmacIndexs = impb_standard_indexed_data_oid_index($device["device_type_global"]["snmp_oid_MacBindingBlockedVID"], $device);
     //impb_debug("blmacIndex data collection complete." . sizeof($blmacIndexs));
	if (is_array($blmacIndexs) and count($blmacIndexs) > 0) {
		$bmacIDs = array_keys($blmacIndexs);
		/* get the blocked mac VID for the device */
		 $blmacVids = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingBlockedVID"], $device);
		 //impb_debug("blmacVid data collection complete." . sizeof($blmacVids));
	 
		 /* get the blocked mac MAC for the device */
		 $blmacMacs = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingBlockedMac"], $device);
		 //impb_debug("blmacMAC data collection complete." . sizeof($blmacMacs));    
		//$keys = array_keys($blmacMacs);
		$i = 0;	
		foreach($blmacMacs as $atAddress) {
			$blmacMacs[$bmacIDs[$i]] = dimpb_xform_mac_address($atAddress);
			$i++;
		}       

		 if ((isset($device["device_type_global"]["snmp_oid_MacBindingBlockedIP"])) && (trim($device["device_type_global"]["snmp_oid_MacBindingBlockedIP"]) <> "")){
		$blmacIPs = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingBlockedIP"], $device);
		 //impb_debug("ifMacsState data collection for device [" . $device["hostname"]  . "] complete." . sizeof($macAction));
		}else{
			$blmacIPs = array_fill_keys($bmacIDs, '');
		}   
	
		 /* get the blocked mac Vlan NAME for the device */
		 $blmacVlanNames = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingBlockedVlanName"], $device);
		 if (!(isset($blmacVlanNames) and (count($blmacVlanNames) > 0))) {
			$blmacVlanNames = array_fill_keys($bmacIDs, '-');
		 }
		 //impb_debug("blmacVlanName data collection complete." . sizeof($blmacVlanNames));
	 
		 /* get the blocked mac Port for the device */
		 $blmacPorts = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_MacBindingBlockedPort"], $device);
		 //impb_debug("blmacPort data collection complete." . sizeof($blmacPorts));      
		 
		 /* get the blocked mac Type for the device */
		 $blmacTypes = impb_standard_indexed_data_oid($device["device_type_global"]["snmp_oid_BindingBlockedType"], $device);
		 //impb_debug("blmacTypes data collection complete." . sizeof($blmacTypes));     
	 
		 
		 foreach($bmacIDs as $bmacID) {
			 $blMacs[$bmacID]["blMacIndex"] = $bmacID;
			 $blMacs[$bmacID]["blMacVid"] = $blmacVids[$bmacID];  
			 $blMacs[$bmacID]["blMacMac"] = strtolower(str_replace("HEX-00:", "", $blmacMacs[$bmacID]));
			 if (!(isset($blmacIPs[$bmacID]))) {
				$blMacs[$bmacID]["blMacIP"] = "";  		 
			 }else{
				if (filter_var(long2ip(hexdec($blmacIPs[$bmacID])), FILTER_VALIDATE_IP)){
					$blMacs[$bmacID]["blMacIP"] = long2ip(hexdec($blmacIPs[$bmacID]));  
				}else{
					$blMacs[$bmacID]["blMacIP"] = "";  		 
				}	
			 }
			 $blMacs[$bmacID]["blMacMac"] = strtolower(str_replace("HEX-00:", "", $blmacMacs[$bmacID]));
			 $blMacs[$bmacID]["blMacVlanName"] = $blmacVlanNames[$bmacID];
			 $blMacs[$bmacID]["blMacPort"] = $blmacPorts[$bmacID];
			 $blMacs[$bmacID]["blMacType"] = $blmacTypes[$bmacID];
		 }
	}
 
     $device["arrports"] = $ifInterfaces;
     $device["arrmacs"] = $macAddress;
     $device["arrblmacs"] = $blMacs;
     
     
    //$device["ip_mac_total"] = sizeof($macAddress);
    $device["ip_mac_blocked_total"] = sizeof($blMacs);
    $device["ports_total"] = $ports_total;
    $device["ports_enable_total"] = $ports_enable_total;
 	$device["ports_enable_zerroip_total"] = $ports_enable_zerroip_total;
    $device["ports_active"] = $ports_active;
 	
 	$enable_log_trap = '0';
 	$enable_acl = '0';
 	
 	if (isset($device["device_type_global"]["snmp_oid_MacBindingACLMode"])  && (trim($device["device_type_global"]["snmp_oid_MacBindingACLMode"]) <> "")) {
 		$enable_acl=cacti_snmp_get($device["hostname"], $device["snmp_get_community"], $device["device_type_global"]["snmp_oid_MacBindingACLMode"], $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
 		switch ($enable_acl) {
 			case "1":
 				$enable_acl =  'Other';                
 				break;		
 			case "2":
 				$enable_acl =  'Enable';                
 				break;
 			case "3":
 				$enable_acl =  'Disable';                
 				break;
 			default:
 				$enable_acl =  'not_use';                
 				break;				
 		}
 	} else {
 		$enable_acl = 'not_use';
 	}
 	$device["enable_acl"] = $enable_acl;
 
 	if (isset($device["device_type_global"]["snmp_oid_MacBindingTrapLogState"]) && (trim($device["device_type_global"]["snmp_oid_MacBindingTrapLogState"]) <> "")) {
 		$enable_log_trap=cacti_snmp_get($device["hostname"], $device["snmp_get_community"], $device["device_type_global"]["snmp_oid_MacBindingTrapLogState"], $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"]);
 		switch ($enable_log_trap) {
 			case "1":
 				$enable_log_trap =  'Other';                
 				break;		
 			case "2":
 				$enable_log_trap =  'Enable';                
 				break;
 			case "3":
 				$enable_log_trap =  'Disable';                
 				break;
 			default:
 				$enable_log_trap =  'not_use';                
 				break;				
 		}
 	} else {
 		$enable_log_trap = 'not_use';
 	}
 	$device["enable_log_trap"] = $enable_log_trap;
 
 	if (isset($device["device_type_global"]["snmp_oid_swL2IpMacBindingFwdDCHPPackState"])  && (trim($device["device_type_global"]["snmp_oid_swL2IpMacBindingFwdDCHPPackState"]) <> "")) {
 		$enable_dhcp=cacti_snmp_get($device["hostname"], $device["snmp_get_community"], $device["device_type_global"]["snmp_oid_swL2IpMacBindingFwdDCHPPackState"], $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
 		switch ($enable_dhcp) {
 			case "1":
 				$enable_dhcp =  'Enable';                
 				break;		
 			case "2":
 				$enable_dhcp =  'Disable';                
 				break;
 			default:
 				$enable_dhcp =  'not_use';                
 				break;				
 		}
 	} else {
 		$enable_dhcp = 'not_use';
 	}
 	$device["enable_dhcp"] = $enable_dhcp;	
 	
 	//impb_debug("ppp------>>finish function get_dlink_l2_switch_ports for dev=: " . " dev=" . $device["hostname"] );
 
     if ($store_to_db) {
         if ($ports_active <= 0) {
             $device["last_runmessage"] = "Data collection completed ok with no active ports";
             db_execute("UPDATE imb_ports SET port_online=0 WHERE device_id=" . $device["device_id"]);
             db_execute("UPDATE imb_macip SET macip_online=0 WHERE device_id=" . $device["device_id"]);
             db_execute("UPDATE imb_blmacs SET blmac_online=0 WHERE device_id=" . $device["device_id"]);
         }elseif (sizeof($ifInterfaces) > 0) {
             $device["last_runmessage"] = "Data collection completed ok";
             if (sizeof($ifInterfaces)>0) {
               dimpb_db_store_device_port_results($device, $ifInterfaces, $scan_date);
             }else{
               //db_execute("UPDATE imb_ports SET port_online=0 WHERE device_id=" . $device["device_id"]);
 			  db_execute("DELETE FROM imb_ports where device_id='" . $device["device_id"] . "'");
             }
             if (sizeof($macAddress)>0) {
              db_store_device_imp_results($device, $macAddress, $scan_date);
             }else{
               //Проверим, возможно на этом устройстве есть забаненные записи, вернее все записи забанены.
 			  $count_banned_ips = db_fetch_cell("SELECT count(*) FROM imb_macip where `device_id`='" . $device["device_id"] . "' and `macip_banned`=1;");
 			  if ($count_banned_ips==0) {
 				db_execute("DELETE FROM imb_macip where device_id='" . $device["device_id"] . "'");
 				}
             }
			 $device["ip_mac_total"] = db_fetch_cell("SELECT count(*) FROM imb_macip where `device_id`='" . $device["device_id"] . "';");
             if (sizeof($blMacs)>0) {
 				db_store_device_blocked_imp_results($device, $blMacs, $scan_date); 
 			}else{
 				//db_execute("UPDATE imb_blmacs SET blmac_online=0 WHERE device_id=" . $device["device_id"]);
 				db_execute("DELETE imb_temp_blmacinfo.* FROM imb_temp_blmacinfo left join imb_blmacs on imb_temp_blmacinfo.blmacinfo_info_id = imb_blmacs.blmac_id where device_id='" . $device["device_id"] . "'");
 				db_execute("DELETE FROM imb_blmacs where device_id='" . $device["device_id"] . "' and ((`blmac_done`=0) or (`blmac_done`>0 and `blmac_done_view_count`>5));");
         //db_execute("DELETE FROM imb_blmacs WHERE device_id=" . $device["device_id"] . ";");
 			}
 			if (sizeof($vl)>0) {
				impb_db_store_vlans_results($device, $vl, $scan_date);
            }
 
            /* $update_string="UPDATE imb_temp_blmacs LEFT JOIN imb_temp_macip " .
                 " ON imb_temp_blmacs.device_id = imb_temp_macip.device_id and imb_temp_blmacs.blmac_macaddr = imb_temp_macip.macip_macaddr " .
                 " SET imb_temp_blmacs.blmac_existrule = (select count(imb_temp_macip.macip_macaddr) as mac_count from imb_temp_macip where " . 
                 " imb_temp_macip.macip_macaddr=imb_temp_blmacs.blmac_macaddr group by imb_temp_blmacs.blmac_macaddr);";
              $update_string="UPDATE imb_temp_blmacs LEFT JOIN imb_temp_macip " .
                 " ON imb_temp_blmacs.device_id = imb_temp_macip.device_id and imb_temp_blmacs.blmac_macaddr = imb_temp_macip.macip_macaddr " .
                 " SET imb_temp_blmacs.blmac_correct_ip = (select imb_temp_macip.macip_ipaddr from imb_temp_macip where imb_temp_macip.macip_macaddr=imb_temp_blmacs.blmac_macaddr group by imb_temp_blmacs.blmac_macaddr), ".
                 " imb_temp_blmacs.blmac_correct_port_list = (select imb_temp_macip.macip_port_list from imb_temp_macip where " .
                 " imb_temp_macip.macip_macaddr=imb_temp_blmacs.blmac_macaddr group by imb_temp_blmacs.blmac_macaddr);";
             */
             
              
         }else{
       $device["last_runmessage"] = "WARNING: Poller did not find active ports on this device.";
 			db_execute("DELETE FROM imb_temp_ports where device_id='" . $device["device_id"] . "'");
 			db_execute("DELETE FROM imb_temp_macip where device_id='" . $device["device_id"] . "'");
 			db_execute("DELETE FROM imb_temp_blmacs where device_id='" . $device["device_id"] . "'");
 			db_execute("UPDATE imb_macip SET macip_online=0 WHERE device_id=" . $device["device_id"]);
 			db_execute("UPDATE imb_ports SET port_online=0 WHERE device_id=" . $device["device_id"]);
 			db_execute("UPDATE imb_blmacs SET blmac_online=0 WHERE device_id=" . $device["device_id"]);
       //db_execute("DELETE imb_temp_blmacinfo.* FROM imb_temp_blmacinfo left join imb_temp_blmacs on imb_temp_blmacinfo.blmacinfo_info_id = imb_temp_blmacs.blmac_id where device_id='" . $device["device_id"] . "'");
 			
         }
 
         if(!$imb_debug) {
             print(" - Complete\n");
         }
     }else{
         return $new_port_key_array;
     }
 
      return $device;
 
 }
 
 
 

 function scan_cisco_universal($device, $imb_debug = 0) {
 	global $scan_date;
 
	/* initialize port counters */
	$device['ports_total'] = 0;
	$device["ports_active"] = 0;
 	$device["ports_trunk"] = 0;
    $ports_total=0;
    $ports_active=0;
    $ports_enable_total=0;
 	$ports_enable_zerroip_total=0;
    $store_to_db = TRUE;
	$imb_debug = 0;
 	
	
	$device["device_type_global"] =  db_fetch_row ("SELECT * FROM imb_device_types WHERE device_type_id=" . $device["device_type_id"] . ";");
 	
 	/* get the ifIndexes for the device */
 	$ifIndexes = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_ifIndex"], $device);
 	//impb_debug("ifIndexes data collection complete" . sizeof($ifIndexes));
 
     /* get the fOperStatus for the device */
    $ifStates = dimpb_xform_standard_indexed_data(".1.3.6.1.2.1.2.2.1.8", $device);
     //impb_debug("PortStates data collection complete." . sizeof($ifStates));

      /* get the port admin state for the device */
	$ifAdminStates = impb_standard_indexed_data_oid(".1.3.6.1.2.1.2.2.1.7", $device);
	$ifAdminStates=dimpb_array_compress_strip($ifAdminStates);


      /* get the port admin speed for the device */
 	$ifAdminSpeeds = array_fill_keys($ifIndexes, '0');

      /* get the port  speed for the device */
	$ifSpeeds = impb_standard_indexed_data_oid(".1.3.6.1.2.1.2.2.1.5", $device);
	$ifSpeeds=dimpb_array_compress_strip($ifSpeeds);

      /* get the port  loop setings  for the device */
 	$ifLoopStates = array_fill_keys($ifIndexes, '0');


      /* get the port  loop setings  for the device */
 	$ifErrorReasons = array_fill_keys($ifIndexes, '0');
	
      /* get the port  loop setings  for the device */
 	$ifLoopVlans = array_fill_keys($ifIndexes, '0');
	
      /* get the portstate for the device */
	$ifZerroIPStates = array_fill_keys($ifIndexes, '0');
 	
      /* get the portstate for the device */
	$ifDHCPFwdPcktsStates = array_fill_keys($ifIndexes, '0');

      /* get the portstate for the device */
	$ifMaxEntryCount = array_fill_keys($ifIndexes, '0');


//DGS
      /* get the portstate for the device */
	$ifARPIspections = array_fill_keys($ifIndexes, '0');

      /* get the portstate for the device */
	$ifIPIspections = array_fill_keys($ifIndexes, '0');

		/* get the portstate for the device */
	$ifIPProtocols = array_fill_keys($ifIndexes, '0');

 	
     /* get the ifNames for the device */
     $ifNames = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_ifAlias"], $device);
     //impb_debug("ifNames data collection complete." . sizeof($ifNames));
         
                          
     /* get the ifTypes for the device */
     $ifTypes = dimpb_xform_standard_indexed_data($device["device_type_global"]["snmp_oid_ifType"], $device);
     //impb_debug("ifTypes data collection complete." . sizeof($ifTypes));
     
     $ifInterfaces=array();
 
     foreach($ifIndexes as $ifIndex) {
         $ifInterfaces[$ifIndex]["ifIndex"] = $ifIndex;
         $ifInterfaces[$ifIndex]["ifName"] = @$ifNames[$ifIndex];
         if (isset($ifStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifState"] = $ifStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifState"] = '0';
         }

         if (isset($ifLoopStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifLoopState"] = $ifLoopStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifLoopState"] = '0';
         }

         if (isset($ifLoopVlans[$ifIndex])) {
             if ($ifLoopVlans[$ifIndex] == "None") {
				$ifInterfaces[$ifIndex]["ifLoopVlan"] = "0";
			 }else{
				$ifInterfaces[$ifIndex]["ifLoopVlan"] = $ifLoopVlans[$ifIndex];
			 }
         } else {
             $ifInterfaces[$ifIndex]["ifLoopVlan"] = '0';
         }
		 
         if (isset($ifErrorReasons[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifErrorReason"] = $ifErrorReasons[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifErrorReason"] = '0';
         }

         if (isset($ifAdminSpeeds[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifAdminSpeed"] = $ifAdminSpeeds[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifAdminSpeed"] = '0';
         }		 

         if (isset($ifSpeeds[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifSpeed"] = $ifSpeeds[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifSpeed"] = '0';
         }		 
		 
         if (isset($ifAdminStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifAdminState"] = $ifAdminStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifAdminState"] = '0';
         }
		 
         if (isset($ifZerroIPStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifZerroIpState"] = $ifZerroIPStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifZerroIpState"] = '0';
         }

         if (isset($ifDHCPFwdPcktsStates[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifDHCPFwdPcktsStates"] = $ifDHCPFwdPcktsStates[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifDHCPFwdPcktsStates"] = '0';
         }
 		
         if (isset($ifMaxEntryCount[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifMaxEntryCount"] = $ifMaxEntryCount[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifMaxEntryCount"] = '0';
         }

         if (isset($ifARPIspections[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifARPIspetion"] = $ifARPIspections[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifARPIspetion"] = '0';
         }
         if (isset($ifIPIspections[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifIPIspetion"] = $ifIPIspections[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifIPIspetion"] = '0';
         }
         if (isset($ifIPProtocols[$ifIndex])) {
             $ifInterfaces[$ifIndex]["ifIPProtocol"] = $ifIPProtocols[$ifIndex];
         } else {
             $ifInterfaces[$ifIndex]["ifIPProtocol"] = '0';
         }
		 
         if (isset($ifTypes[$ifIndex])) {
             if (substr_count($ifTypes[$ifIndex], "(")>0) {
                $ifInterfaces[$ifIndex]["ifType"] = substr ( $ifTypes[$ifIndex], strpos($ifTypes[$ifIndex], "(" )+1, (strpos( $ifTypes[$ifIndex], ")" ) - strpos( $ifTypes[$ifIndex], "(") )-1 );
             }else{
             $ifInterfaces[$ifIndex]["ifType"] = $ifTypes[$ifIndex];
             }
         }else{
             $ifInterfaces[$ifIndex]["ifType"] = '0';
         }
         if (isset($ifStates[$ifIndex])) {
             if ($ifStates[$ifIndex] == 2) {
                 $ports_enable_total=$ports_enable_total+1;
             }
         }
 		if ($ifInterfaces[$ifIndex]["ifZerroIpState"] == 1) {
             $ports_enable_zerroip_total=$ports_enable_zerroip_total+1;
         }
     }    
    // impb_debug("ifInterfaces assembly complete. - " . sizeof($ifInterfaces));
     

     /* get the vlans data for the device */
    $vlans = impb_standard_indexed_data_oid(".1.3.6.1.2.1.17.7.1.4.3.1", $device);
	foreach($vlans as $key => $v) {
		if (substr($key, 0, 2) == "1.") {
			$vl_id=substr($key, 2);
			$vl[$vl_id]["id"] = $vl_id;
			$vl[$vl_id]["name"] = $vlans["1." . $vl_id]; //descr
			$vl[$vl_id]["m_p"]  = str_replace(" ","",str_replace(":","",$vlans["2." . $vl_id])); //member port
			$vl[$vl_id]["u_p"]  = str_replace(" ","",str_replace(":","",$vlans["4." . $vl_id])); //untagged ports
			$vl[$vl_id]["f_p"]  = str_replace(" ","",str_replace(":","",$vlans["3." . $vl_id])); //forbid ports
			$vl[$vl_id]["t_p"]  = strtoupper(str_pad(base_convert(member_ports($vl[$vl_id]["m_p"]) & impb_invert_string(member_ports($vl[$vl_id]["u_p"])),2,16) , strlen($vl[$vl_id]["m_p"]) , "0" , STR_PAD_LEFT)); //tagged ports
			$vl[$vl_id]["a_t"]  = array_merge(array(0=>"0"),str_split(member_ports($vl[$vl_id]["t_p"])));
			$vl[$vl_id]["a_u"]  = array_merge(array(0=>"0"),str_split(member_ports($vl[$vl_id]["u_p"])));
			$vl[$vl_id]["u_pl"] = convert_Xport_to_view_string($vlans["4." . $vl_id], $device["device_type_global"]["type_port_num_conversion"] ); //untagged ports list
		}
	}    
	

	
     /* get the operational status of the ports */
     $active_ports_array = $ifStates;
     $indexes = array_keys($active_ports_array);
 
     $i = 1;
     $ifInterfaces_temp=array();
     foreach($ifInterfaces as $ifInterface) {
         if (isset($ifInterfaces[$indexes[$i-1]]))  {        
         if ((($ifInterfaces[$indexes[$i-1]]["ifType"] >= 6) &&
             ($ifInterfaces[$indexes[$i-1]]["ifType"] <= 9)) ||
 			($ifInterfaces[$indexes[$i-1]]["ifType"] == 117))  {
             if (substr_count($active_ports_array[$i], "(")>0) {
               $active_ports_array[$i] = substr ( $active_ports_array[$i], strpos($active_ports_array[$i], "(" )+1, (strpos( $active_ports_array[$i], ")" ) - strpos( $active_ports_array[$i], "(") )-1 );
               }
             if ($active_ports_array[$i] == 1) {
                 $ports_active++;
                 $ifInterfaces[$indexes[$i-1]]["ifActive"] = "1";
       
             }else{
                 $ifInterfaces[$indexes[$i-1]]["ifActive"] = "0";
             }
             $ports_total++;
             $ifInterfaces_temp[$indexes[$i-1]] = $ifInterfaces[$indexes[$i-1]]; 
			 
         }
       
		

        $i++;
		}
     }
    $ifInterfaces = $ifInterfaces_temp;
     
	impb_debug("INFO: HOST: " . $device["hostname"] . ", TYPE: " . substr($device["snmp_sysDescr"],0,40) . ", TOTAL PORTS: " . $ports_total . ", OPER PORTS: " . $ports_active);

 	
    $device["arrports"] = $ifInterfaces;
     
     
     //$device["ip_mac_total"] = sizeof($macAddress);
    $device["ports_total"] = $ports_total;
    $device["ports_enable_total"] = $ports_enable_total;
    $device["ports_active"] = $ports_active;
	$device["enable_dhcp"] = 'not_use';
	$device["enable_acl"] = 'not_use';


 	//impb_debug("ppp------>>finish function get_dlink_l2_switch_ports for dev=: " . " dev=" . $device["hostname"] );
 
     if ($store_to_db) {
        if ($ports_active <= 0) {
             $device["last_runmessage"] = "Data collection completed ok with no active ports";
             db_execute("UPDATE imb_ports SET port_online=0 WHERE device_id=" . $device["device_id"]);
         }elseif (sizeof($ifInterfaces) > 0) {
             $device["last_runmessage"] = "Data collection completed ok";
             if (sizeof($ifInterfaces)>0) {
               dimpb_db_store_device_port_results($device, $ifInterfaces, $scan_date);
             }else{
               //db_execute("UPDATE imb_ports SET port_online=0 WHERE device_id=" . $device["device_id"]);
 			  db_execute("DELETE FROM imb_ports where device_id='" . $device["device_id"] . "'");
             }

			if (isset($vl) and sizeof($vl)>0) {
				impb_db_store_vlans_results($device, $vl, $scan_date);
            }
 
              
        }else{
       $device["last_runmessage"] = "WARNING: Poller did not find active ports on this device.";
 			db_execute("DELETE FROM imb_temp_ports where device_id='" . $device["device_id"] . "'");
 			db_execute("UPDATE imb_ports SET port_online=0 WHERE device_id=" . $device["device_id"]);
        }
 
         if(!$imb_debug) {
             print(" - Complete\n");
         }
     }else{
         return $new_port_key_array;
     }
 
      return $device;
 
 }
 
 ?>
