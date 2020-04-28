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
 chdir('../../');
 /* include cacti base functions */
 include("./include/auth.php");
 include_once("./lib/snmp.php");
 
 /* include base and vendor functions to obtain a list of registered scanning functions */
<<<<<<< HEAD
 include_once($config['base_path'] . "/plugins/impb/lib/impb_functions.php");
 include_once($config['base_path'] . "/plugins/impb/lib/impb_vendors.php");
 
 /* store the list of registered impb scanning functions */
 foreach($impb_scanning_functions as $scanning_function) {
 	db_execute("REPLACE INTO imb_scanning_functions (scanning_function) VALUES ('" . $scanning_function . "')");
 }
 
  
 $device_types_actions = array(
	1 => __('Delete'),
	2 => __('Duplicate')
 	);
 
 set_default_action();
 
 
switch (get_request_var('action')) {
 	case 'save':
 		form_impb_dt_save();
 
 		break;
 	case 'actions':
 		form_impb_dt_actions();
 
 		break;
 	case 'edit':
		top_header();
		impb_device_type_edit();
		bottom_footer();
 		break;
 	case 'import':
		top_header();
		impb_device_type_import();
		bottom_footer();
 
 		break;
 	default:
 		if (isset_request_var('import')) {
 			header("Location: impb_device_types.php?action=import");
 		}elseif (isset_request_var('export')) {
 			impb_device_type_export();
 		}else{
			top_header();
			impb_device_type();
			bottom_footer();
=======
 include_once($config['base_path'] . "/plugins/impblinding/lib/impblinding_functions.php");
 include_once($config['base_path'] . "/plugins/impblinding/lib/impblinding_vendors.php");
 
 /* store the list of registered impblinding scanning functions */
 foreach($impblinding_scanning_functions as $scanning_function) {
 	db_execute("REPLACE INTO imb_scanning_functions (scanning_function) VALUES ('" . $scanning_function . "')");
 }
 
 define("MAX_DISPLAY_PAGES", 21);
 
 $device_types_actions = array(
 	1 => "Delete",
 	2 => "Duplicate"
 	);
 
 /* set default action */
 if (!isset($_REQUEST["action"])) { $_REQUEST["action"] = ""; }
 
 switch ($_REQUEST["action"]) {
 	case 'save':
 		form_save();
 
 		break;
 	case 'actions':
 		form_actions();
 
 		break;
 	case 'edit':
 		include_once("./include/top_header.php");
 
 		impblinding_device_type_edit();
 
 		include_once("./include/bottom_footer.php");
 		break;
 	case 'import':
 		include_once("./include/top_header.php");
 
 		impblinding_device_type_import();
 
 		include_once("./include/bottom_footer.php");
 		break;
 	default:
 		if (isset($_REQUEST["import_x"])) {
 			header("Location: impblinding_device_types.php?action=import");
 		}elseif (isset($_REQUEST["export_x"])) {
 			impblinding_device_type_export();
 		}else{
 			include_once("./include/top_header.php");
 
 			impblinding_device_type();
 
 			include_once("./include/bottom_footer.php");
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 		}
 		break;
 }
 
 /* --------------------------
     The Save Function
    -------------------------- */
 
<<<<<<< HEAD
function form_impb_dt_save() {
	global $config;

	if ((isset_request_var('save_component_device_type')) && (isempty_request_var('add_dq_y'))) {
 		$device_type_id = api_impb_device_type_save(get_nfilter_request_var('device_type_id'), get_nfilter_request_var('description'),get_nfilter_request_var('scanning_function'),get_nfilter_request_var('type_port_num_conversion'),get_nfilter_request_var('type_port_use_long'),get_nfilter_request_var('type_use_more_32x_port'),get_nfilter_request_var('impb_func_version'),get_nfilter_request_var('snmp_oid_MacBindingACLMode'),get_nfilter_request_var('snmp_oid_MacBindingTrapLogState'),get_nfilter_request_var('snmp_oid_Trap_eventid'),
		get_nfilter_request_var('snmp_oid_Trap_eventid'), get_nfilter_request_var('snmp_value_save_cfg'),get_nfilter_request_var('snmp_timeout_agentSaveCfg'),
		get_nfilter_request_var('snmp_oid_swL2IpMacBindingFwdDCHPPackState'), get_nfilter_request_var('snmp_oid_ifIndex'),get_nfilter_request_var('snmp_oid_ifDescr'),
		get_nfilter_request_var('snmp_oid_ifType'), get_nfilter_request_var('snmp_oid_ifSpeed'),get_nfilter_request_var('snmp_oid_ifOperStatus'),
		get_nfilter_request_var('snmp_oid_MacBindingPortState'), get_nfilter_request_var('type_revision'),get_nfilter_request_var('type_imb_MacBindingPortState'),
		get_nfilter_request_var('snmp_oid_en_MacBindingZerroIpPortState'), get_nfilter_request_var('snmp_oid_en_swIpMacBindingPortARPInspection'),get_nfilter_request_var('snmp_oid_en_swIpMacBindingPortIPInspection'),
		get_nfilter_request_var('snmp_oid_en_swIpMacBindingPortIPProtocol'), get_nfilter_request_var('type_imb_zerrostate_mode'),get_nfilter_request_var('snmp_oid_en_fwd_dhcp_packets_state'),
		get_nfilter_request_var('snmp_oid_max_entry_count'), get_nfilter_request_var('snmp_oid_ifAlias'),get_nfilter_request_var('type_imb_create_macip'),
		get_nfilter_request_var('snmp_oid_MacBindingIpIndex'), get_nfilter_request_var('snmp_oid_MacBindingMac'),get_nfilter_request_var('snmp_oid_MacBindingStatus'),
		get_nfilter_request_var('snmp_oid_MacBindingPorts'), get_nfilter_request_var('snmp_oid_MacBindingAction'),get_nfilter_request_var('type_imb_action'),
		get_nfilter_request_var('snmp_oid_MacBindingMode'), get_nfilter_request_var('type_imb_mode'),get_nfilter_request_var('snmp_oid_MacBindingBlockedVID'),
		get_nfilter_request_var('snmp_oid_MacBindingBlockedMac'), get_nfilter_request_var('snmp_oid_MacBindingBlockedIP'),get_nfilter_request_var('snmp_oid_MacBindingBlockedVlanName'),
		get_nfilter_request_var('snmp_oid_MacBindingBlockedPort'), get_nfilter_request_var('snmp_oid_BindingBlockedType'),get_nfilter_request_var('setting_imb_def_mode'),
		get_nfilter_request_var('setting_imb_use_autoban'), get_nfilter_request_var('setting_imb_use_auto_unblock'),get_nfilter_request_var('setting_imb_use_auto_add'),
		get_nfilter_request_var('setting_imb_use_auto_change'), get_nfilter_request_var('setting_imb_use_reenable_onport'),get_nfilter_request_var('snmp_oid_swL2PortCtrlAdminState'),
		get_nfilter_request_var('snmp_oid_swL2PortCtrlSpeedState'), get_nfilter_request_var('snmp_oid_swL2PortSpeedStatus'),get_nfilter_request_var('snmp_oid_swL2LoopDetectPortState'),
		get_nfilter_request_var('snmp_oid_swL2LoopDetectPortLoopVLAN'), get_nfilter_request_var('swL2PortErrPortReason'));
 
		if ($device_type_id) {
			raise_message(1);
		} else {
			raise_message(2);
		}
		header('Location: impb_device_types.php?action=edit&header=false&device_type_id=' . (empty($device_type_id) ? get_nfilter_request_var('device_type_id') : $device_type_id)); 		


 	}

	if (isset_request_var('save_component_import')) {
		if (($_FILES['import_file']['tmp_name'] != 'none') && ($_FILES['import_file']['tmp_name'] != '')) {
			/* file upload */
			$csv_data = file($_FILES['import_file']['tmp_name']);

			/* obtain debug information if it's set */
			$debug_data = impb_device_import_processor($csv_data);
			if(sizeof($debug_data) > 0) {
				$_SESSION['import_debug_info'] = $debug_data;
			}
		}else{
			header('Location: impb_device_types.php?action=import'); exit;
		}

		header('Location: impb_device_types.php?action=import');
	}

}
 
 
function api_impb_device_type_remove($device_type_id){
 	db_execute("DELETE FROM imb_device_types WHERE device_type_id='" . $device_type_id . "'");
}
 
  
function api_impb_device_type_save($device_type_id, $description, $scanning_function, $type_port_num_conversion, $type_port_use_long, $type_use_more_32x_port,$impb_func_version,$snmp_oid_MacBindingACLMode, $snmp_oid_MacBindingTrapLogState, $snmp_oid_Trap_eventid, $snmp_oid_agentSaveCfg, $snmp_value_save_cfg, $snmp_timeout_agentSaveCfg,  $snmp_oid_swL2IpMacBindingFwdDCHPPackState,
=======
 function form_save() {
 	if ((isset($_POST["save_component_device_type"])) && (empty($_POST["add_dq_y"]))) {
 		$device_type_id = api_impblinding_device_type_save($_POST["device_type_id"], $_POST["description"], $_POST["scanning_function"],$_POST["type_port_num_conversion"],$_POST["type_port_use_long"],$_POST["type_use_more_32x_port"],$_POST["impb_func_version"], $_POST["snmp_oid_MacBindingACLMode"], $_POST["snmp_oid_MacBindingTrapLogState"], $_POST["snmp_oid_Trap_eventid"],
 		$_POST["snmp_oid_agentSaveCfg"],$_POST["snmp_value_save_cfg"],$_POST["snmp_timeout_agentSaveCfg"],$_POST["snmp_oid_swL2IpMacBindingFwdDCHPPackState"],$_POST["snmp_oid_ifIndex"],$_POST["snmp_oid_ifDescr"],$_POST["snmp_oid_ifType"],$_POST["snmp_oid_ifSpeed"],$_POST["snmp_oid_ifOperStatus"],$_POST["snmp_oid_MacBindingPortState"],$_POST["type_revision"],$_POST["type_imb_MacBindingPortState"],
 		$_POST["snmp_oid_en_MacBindingZerroIpPortState"],$_POST["snmp_oid_en_swIpMacBindingPortARPInspection"],$_POST["snmp_oid_en_swIpMacBindingPortIPInspection"],$_POST["snmp_oid_en_swIpMacBindingPortIPProtocol"],$_POST["type_imb_zerrostate_mode"],$_POST["snmp_oid_en_fwd_dhcp_packets_state"],$_POST["snmp_oid_max_entry_count"],$_POST["snmp_oid_ifAlias"],$_POST["type_imb_create_macip"],$_POST["snmp_oid_MacBindingIpIndex"],$_POST["snmp_oid_MacBindingMac"],$_POST["snmp_oid_MacBindingStatus"],$_POST["snmp_oid_MacBindingPorts"],
 		$_POST["snmp_oid_MacBindingAction"],$_POST["type_imb_action"],$_POST["snmp_oid_MacBindingMode"],$_POST["type_imb_mode"],$_POST["snmp_oid_MacBindingBlockedVID"],$_POST["snmp_oid_MacBindingBlockedMac"],$_POST["snmp_oid_MacBindingBlockedIP"],$_POST["snmp_oid_MacBindingBlockedVlanName"],$_POST["snmp_oid_MacBindingBlockedPort"],$_POST["snmp_oid_BindingBlockedType"],
		$_POST["setting_imb_def_mode"],$_POST["setting_imb_use_autoban"],$_POST["setting_imb_use_auto_unblock"],$_POST["setting_imb_use_auto_add"],$_POST["setting_imb_use_auto_change"],$_POST["setting_imb_use_reenable_onport"],
		$_POST["snmp_oid_swL2PortCtrlAdminState"],$_POST["snmp_oid_swL2PortCtrlSpeedState"],$_POST["snmp_oid_swL2PortSpeedStatus"],$_POST["snmp_oid_swL2LoopDetectPortState"],$_POST["snmp_oid_swL2LoopDetectPortLoopVLAN"],$_POST["swL2PortErrPortReason"]);
 
 		if ((is_error_message()) || ($_POST["device_type_id"] != $_POST["_device_type_id"])) {
 			header("Location: impblinding_device_types.php?action=edit&device_type_id=" . (empty($device_type_id) ? $_POST["device_type_id"] : $device_type_id));
 		}else{
 			header("Location: impblinding_device_types.php");
 		}
 	}
 
 	if (isset($_POST["save_component_import"])) {
 		if (($_FILES["import_file"]["tmp_name"] != "none") && ($_FILES["import_file"]["tmp_name"] != "")) {
 			/* file upload */
 			$csv_data = file($_FILES["import_file"]["tmp_name"]);
 
 			/* obtain debug information if it's set */
 			$debug_data = impblinding_device_type_import_processor($csv_data);
 			if(sizeof($debug_data) > 0) {
 				$_SESSION["import_debug_info"] = $debug_data;
 			}
 		}else{
 			header("Location: impblinding_device_types.php?action=import"); exit;
 		}
 
 		header("Location: impblinding_device_types.php?action=import");
 	}
 }
 
 
 function api_impblinding_device_type_remove($device_type_id){
 	db_execute("DELETE FROM imb_device_types WHERE device_type_id='" . $device_type_id . "'");
 }
 
  
 function api_impblinding_device_type_save($device_type_id, $description, $scanning_function, $type_port_num_conversion, $type_port_use_long, $type_use_more_32x_port,$impb_func_version,$snmp_oid_MacBindingACLMode, $snmp_oid_MacBindingTrapLogState, $snmp_oid_Trap_eventid, $snmp_oid_agentSaveCfg, $snmp_value_save_cfg, $snmp_timeout_agentSaveCfg,  $snmp_oid_swL2IpMacBindingFwdDCHPPackState,
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
  $snmp_oid_ifIndex, $snmp_oid_ifDescr, $snmp_oid_ifType, $snmp_oid_ifSpeed, $snmp_oid_ifOperStatus, $snmp_oid_MacBindingPortState,$type_revision,$type_imb_MacBindingPortState, $snmp_oid_en_MacBindingZerroIpPortState,$snmp_oid_en_swIpMacBindingPortARPInspection,$snmp_oid_en_swIpMacBindingPortIPInspection,$snmp_oid_en_swIpMacBindingPortIPProtocol, $type_imb_zerrostate_mode, $snmp_oid_en_fwd_dhcp_packets_state,$snmp_oid_max_entry_count,
  $snmp_oid_ifAlias, $type_imb_create_macip,$snmp_oid_MacBindingIpIndex,$snmp_oid_MacBindingMac,$snmp_oid_MacBindingStatus,$snmp_oid_MacBindingPorts,$snmp_oid_MacBindingAction, $type_imb_action,
  $snmp_oid_MacBindingMode, $type_imb_mode, $snmp_oid_MacBindingBlockedVID,$snmp_oid_MacBindingBlockedMac,$snmp_oid_MacBindingBlockedIP,$snmp_oid_MacBindingBlockedVlanName,$snmp_oid_MacBindingBlockedPort,$snmp_oid_BindingBlockedType,
  $setting_imb_def_mode, $setting_imb_use_autoban, $setting_imb_use_auto_unblock, $setting_imb_use_auto_add, $setting_imb_use_auto_change, $setting_imb_use_reenable_onport,
  $snmp_oid_swL2PortCtrlAdminState,$snmp_oid_swL2PortCtrlSpeedState,$snmp_oid_swL2PortSpeedStatus,$snmp_oid_swL2LoopDetectPortState,$snmp_oid_swL2LoopDetectPortLoopVLAN,$swL2PortErrPortReason) {
 
<<<<<<< HEAD
 	$save["device_type_id"] 							= $device_type_id;
 	$save["description"] 								= form_input_validate($description, "description", "", false, 3);
 	$save["scanning_function"] 							= form_input_validate($scanning_function, "scanning_function", "", true, 3);
 	$save["type_port_num_conversion"] 					= form_input_validate($type_port_num_conversion, "type_port_num_conversion", "", true, 3);
 	$save["type_port_use_long"] 						= form_input_validate($type_port_use_long, "type_port_use_long", "", true, 3);
	$save["type_use_more_32x_port"] 					= form_input_validate($type_use_more_32x_port, "type_use_more_32x_port", "", true, 3);
	$save["impb_func_version"] 							= form_input_validate($impb_func_version, "impb_func_version", "", true, 2);
 	$save["snmp_oid_MacBindingACLMode"] 				= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingACLMode), "snmp_oid_MacBindingACLMode", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_MacBindingTrapLogState"] 			= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingTrapLogState), "snmp_oid_MacBindingTrapLogState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_Trap_eventid"] 						= form_input_validate(imb_check_for_oid($snmp_oid_Trap_eventid), "snmp_oid_MacBindingTrapLogState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_agentSaveCfg"] 						= form_input_validate(imb_check_for_oid($snmp_oid_agentSaveCfg), "snmp_oid_agentSaveCfg", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_value_save_cfg"] 						= form_input_validate($snmp_value_save_cfg, "snmp_value_save_cfg", "", true, 3);
 	$save["snmp_timeout_agentSaveCfg"] 					= form_input_validate($snmp_timeout_agentSaveCfg, "snmp_timeout_agentSaveCfg", "^[0-9]{1,3}$", true, 3);
 	$save["snmp_oid_swL2IpMacBindingFwdDCHPPackState"] 	= form_input_validate(imb_check_for_oid($snmp_oid_swL2IpMacBindingFwdDCHPPackState), "snmp_oid_swL2IpMacBindingFwdDCHPPackState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_ifIndex"] 							= form_input_validate(imb_check_for_oid($snmp_oid_ifIndex), "snmp_oid_ifIndex", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_ifDescr"] 							= form_input_validate(imb_check_for_oid($snmp_oid_ifDescr), "snmp_oid_ifDescr", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_ifType"] 							= form_input_validate(imb_check_for_oid($snmp_oid_ifType), "snmp_oid_ifType", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_ifSpeed"] 							= form_input_validate(imb_check_for_oid($snmp_oid_ifSpeed), "snmp_oid_ifSpeed", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_ifOperStatus"] 						= form_input_validate(imb_check_for_oid($snmp_oid_ifOperStatus), "snmp_oid_ifOperStatus", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingPortState"] 				= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingPortState), "snmp_oid_MacBindingPortState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["type_revision"] 								= form_input_validate($type_revision, "type_revision", "", true, 3);
	$save["type_imb_MacBindingPortState"] 				= form_input_validate($type_imb_MacBindingPortState, "type_imb_MacBindingPortState", "", true, 3);
 	$save["snmp_oid_en_MacBindingZerroIpPortState"] 	= form_input_validate(imb_check_for_oid($snmp_oid_en_MacBindingZerroIpPortState), "snmp_oid_en_MacBindingZerroIpPortState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_en_swIpMacBindingPortARPInspection"] = form_input_validate(imb_check_for_oid($snmp_oid_en_swIpMacBindingPortARPInspection), "snmp_oid_en_swIpMacBindingPortARPInspection", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_en_swIpMacBindingPortIPInspection"] = form_input_validate(imb_check_for_oid($snmp_oid_en_swIpMacBindingPortIPInspection), "snmp_oid_en_swIpMacBindingPortIPInspection", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_en_swIpMacBindingPortIPProtocol"] 	= form_input_validate(imb_check_for_oid($snmp_oid_en_swIpMacBindingPortIPProtocol), "snmp_oid_en_swIpMacBindingPortIPProtocol", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["type_imb_zerrostate_mode"] 					= form_input_validate($type_imb_zerrostate_mode, "type_imb_zerrostate_mode", "", false, 3);
 	$save["snmp_oid_en_fwd_dhcp_packets_state"] 		= form_input_validate(imb_check_for_oid($snmp_oid_en_fwd_dhcp_packets_state), "snmp_oid_en_fwd_dhcp_packets_state", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_max_entry_count"] 					= form_input_validate(imb_check_for_oid($snmp_oid_max_entry_count), "snmp_oid_max_entry_count", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_ifAlias"] 							= form_input_validate(imb_check_for_oid($snmp_oid_ifAlias), "snmp_oid_ifAlias", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["type_imb_create_macip"] 						= form_input_validate($type_imb_create_macip, "type_imb_create_macip", "", false, 3);
 	$save["snmp_oid_MacBindingIpIndex"] 				= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingIpIndex), "snmp_oid_MacBindingIpIndex", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingMac"]						= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingMac), "snmp_oid_MacBindingMac", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingStatus"] 					= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingStatus), "snmp_oid_MacBindingStatus", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingPorts"] 					= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingPorts), "snmp_oid_MacBindingPorts", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingAction"] 					= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingAction), "snmp_oid_MacBindingAction", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["type_imb_action"] 							= form_input_validate($type_imb_action, "type_imb_action", "", false, 3);
 	$save["snmp_oid_MacBindingMode"] 					= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingMode), "snmp_oid_MacBindingMode", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["type_imb_mode"] 								= form_input_validate($type_imb_mode, "type_imb_mode", "", false, 3);
 	$save["snmp_oid_MacBindingBlockedVID"] 				= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedVID), "snmp_oid_MacBindingBlockedVID", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingBlockedMac"] 				= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedMac), "snmp_oid_MacBindingBlockedMac", "^(\.[0-9]{1,3}){7,20}$", false, 3);
	$save["snmp_oid_MacBindingBlockedIP"] 				= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedIP), "snmp_oid_MacBindingBlockedIP", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_MacBindingBlockedVlanName"] 		= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedVlanName), "snmp_oid_MacBindingBlockedVlanName", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingBlockedPort"] 			= form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedPort), "snmp_oid_MacBindingBlockedPort", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_BindingBlockedType"] 				= form_input_validate(imb_check_for_oid($snmp_oid_BindingBlockedType), "snmp_oid_BindingBlockedType", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["setting_imb_def_mode"] 						= form_input_validate($setting_imb_def_mode, "setting_imb_def_mode", "", false, 3);
 	$save["setting_imb_use_autoban"] 					= form_input_validate($setting_imb_use_autoban, "setting_imb_use_autoban", "", false, 3);
	$save["setting_imb_use_auto_unblock"] 				= form_input_validate($setting_imb_use_auto_unblock, "setting_imb_use_auto_unblock", "", false, 3);
	$save["setting_imb_use_auto_add"] 					= form_input_validate($setting_imb_use_auto_add, "setting_imb_use_auto_add", "", false, 3);
	$save["setting_imb_use_auto_change"] 				= form_input_validate($setting_imb_use_auto_change, "setting_imb_use_auto_change", "", false, 3);
	$save["setting_imb_use_reenable_onport"] 			= form_input_validate($setting_imb_use_reenable_onport, "setting_imb_use_reenable_onport", "", false, 3);
 	
	$save["snmp_oid_swL2PortCtrlAdminState"] 			= form_input_validate(imb_check_for_oid($snmp_oid_swL2PortCtrlAdminState), "snmp_oid_swL2PortCtrlAdminState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_swL2PortCtrlSpeedState"] 			= form_input_validate(imb_check_for_oid($snmp_oid_swL2PortCtrlSpeedState), "snmp_oid_swL2PortCtrlSpeedState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_swL2PortSpeedStatus"] 				= form_input_validate(imb_check_for_oid($snmp_oid_swL2PortSpeedStatus), "snmp_oid_swL2PortSpeedStatus", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_swL2LoopDetectPortState"] 			= form_input_validate(imb_check_for_oid($snmp_oid_swL2LoopDetectPortState), "snmp_oid_swL2LoopDetectPortState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_swL2LoopDetectPortLoopVLAN"] 		= form_input_validate(imb_check_for_oid($snmp_oid_swL2LoopDetectPortLoopVLAN), "snmp_oid_swL2LoopDetectPortLoopVLAN", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["swL2PortErrPortReason"] 						= form_input_validate(imb_check_for_oid($swL2PortErrPortReason), "swL2PortErrPortReason", "^(\.[0-9]{1,3}){7,20}$", true, 3);
=======
 	$save["device_type_id"] = $device_type_id;
 	$save["description"] = form_input_validate($description, "description", "", false, 3);
 	$save["scanning_function"] = form_input_validate($scanning_function, "scanning_function", "", true, 3);
 	$save["type_port_num_conversion"] = form_input_validate($type_port_num_conversion, "type_port_num_conversion", "", true, 3);
 	$save["type_port_use_long"] = form_input_validate($type_port_use_long, "type_port_use_long", "", true, 3);
	$save["type_use_more_32x_port"] = form_input_validate($type_use_more_32x_port, "type_use_more_32x_port", "", true, 3);
	$save["impb_func_version"] = form_input_validate($impb_func_version, "impb_func_version", "", true, 2);
 	$save["snmp_oid_MacBindingACLMode"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingACLMode), "snmp_oid_MacBindingACLMode", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_MacBindingTrapLogState"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingTrapLogState), "snmp_oid_MacBindingTrapLogState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_Trap_eventid"] = form_input_validate(imb_check_for_oid($snmp_oid_Trap_eventid), "snmp_oid_MacBindingTrapLogState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_agentSaveCfg"] = form_input_validate(imb_check_for_oid($snmp_oid_agentSaveCfg), "snmp_oid_agentSaveCfg", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_value_save_cfg"] = form_input_validate($snmp_value_save_cfg, "snmp_value_save_cfg", "", true, 3);
 	$save["snmp_timeout_agentSaveCfg"] = form_input_validate($snmp_timeout_agentSaveCfg, "snmp_timeout_agentSaveCfg", "^[0-9]{1,3}$", true, 3);
 	$save["snmp_oid_swL2IpMacBindingFwdDCHPPackState"] = form_input_validate(imb_check_for_oid($snmp_oid_swL2IpMacBindingFwdDCHPPackState), "snmp_oid_swL2IpMacBindingFwdDCHPPackState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_ifIndex"] = form_input_validate(imb_check_for_oid($snmp_oid_ifIndex), "snmp_oid_ifIndex", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_ifDescr"] = form_input_validate(imb_check_for_oid($snmp_oid_ifDescr), "snmp_oid_ifDescr", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_ifType"] = form_input_validate(imb_check_for_oid($snmp_oid_ifType), "snmp_oid_ifType", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_ifSpeed"] = form_input_validate(imb_check_for_oid($snmp_oid_ifSpeed), "snmp_oid_ifSpeed", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_ifOperStatus"] = form_input_validate(imb_check_for_oid($snmp_oid_ifOperStatus), "snmp_oid_ifOperStatus", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingPortState"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingPortState), "snmp_oid_MacBindingPortState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["type_revision"] = form_input_validate($type_revision, "type_revision", "", true, 3);
	$save["type_imb_MacBindingPortState"] = form_input_validate($type_imb_MacBindingPortState, "type_imb_MacBindingPortState", "", true, 3);
 	$save["snmp_oid_en_MacBindingZerroIpPortState"] = form_input_validate(imb_check_for_oid($snmp_oid_en_MacBindingZerroIpPortState), "snmp_oid_en_MacBindingZerroIpPortState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_en_swIpMacBindingPortARPInspection"] = form_input_validate(imb_check_for_oid($snmp_oid_en_swIpMacBindingPortARPInspection), "snmp_oid_en_swIpMacBindingPortARPInspection", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_en_swIpMacBindingPortIPInspection"] = form_input_validate(imb_check_for_oid($snmp_oid_en_swIpMacBindingPortIPInspection), "snmp_oid_en_swIpMacBindingPortIPInspection", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_en_swIpMacBindingPortIPProtocol"] = form_input_validate(imb_check_for_oid($snmp_oid_en_swIpMacBindingPortIPProtocol), "snmp_oid_en_swIpMacBindingPortIPProtocol", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["type_imb_zerrostate_mode"] = form_input_validate($type_imb_zerrostate_mode, "type_imb_zerrostate_mode", "", false, 3);
 	$save["snmp_oid_en_fwd_dhcp_packets_state"] = form_input_validate(imb_check_for_oid($snmp_oid_en_fwd_dhcp_packets_state), "snmp_oid_en_fwd_dhcp_packets_state", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_max_entry_count"] = form_input_validate(imb_check_for_oid($snmp_oid_max_entry_count), "snmp_oid_max_entry_count", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_ifAlias"] = form_input_validate(imb_check_for_oid($snmp_oid_ifAlias), "snmp_oid_ifAlias", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["type_imb_create_macip"] = form_input_validate($type_imb_create_macip, "type_imb_create_macip", "", false, 3);
 	$save["snmp_oid_MacBindingIpIndex"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingIpIndex), "snmp_oid_MacBindingIpIndex", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingMac"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingMac), "snmp_oid_MacBindingMac", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingStatus"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingStatus), "snmp_oid_MacBindingStatus", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingPorts"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingPorts), "snmp_oid_MacBindingPorts", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingAction"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingAction), "snmp_oid_MacBindingAction", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["type_imb_action"] = form_input_validate($type_imb_action, "type_imb_action", "", false, 3);
 	$save["snmp_oid_MacBindingMode"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingMode), "snmp_oid_MacBindingMode", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["type_imb_mode"] = form_input_validate($type_imb_mode, "type_imb_mode", "", false, 3);
 	$save["snmp_oid_MacBindingBlockedVID"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedVID), "snmp_oid_MacBindingBlockedVID", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingBlockedMac"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedMac), "snmp_oid_MacBindingBlockedMac", "^(\.[0-9]{1,3}){7,20}$", false, 3);
	$save["snmp_oid_MacBindingBlockedIP"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedIP), "snmp_oid_MacBindingBlockedIP", "^(\.[0-9]{1,3}){7,20}$", true, 3);
 	$save["snmp_oid_MacBindingBlockedVlanName"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedVlanName), "snmp_oid_MacBindingBlockedVlanName", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_MacBindingBlockedPort"] = form_input_validate(imb_check_for_oid($snmp_oid_MacBindingBlockedPort), "snmp_oid_MacBindingBlockedPort", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["snmp_oid_BindingBlockedType"] = form_input_validate(imb_check_for_oid($snmp_oid_BindingBlockedType), "snmp_oid_BindingBlockedType", "^(\.[0-9]{1,3}){7,20}$", false, 3);
 	$save["setting_imb_def_mode"] = form_input_validate($setting_imb_def_mode, "setting_imb_def_mode", "", false, 3);
 	$save["setting_imb_use_autoban"] = form_input_validate($setting_imb_use_autoban, "setting_imb_use_autoban", "", false, 3);
	$save["setting_imb_use_auto_unblock"] = form_input_validate($setting_imb_use_auto_unblock, "setting_imb_use_auto_unblock", "", false, 3);
	$save["setting_imb_use_auto_add"] = form_input_validate($setting_imb_use_auto_add, "setting_imb_use_auto_add", "", false, 3);
	$save["setting_imb_use_auto_change"] = form_input_validate($setting_imb_use_auto_change, "setting_imb_use_auto_change", "", false, 3);
	$save["setting_imb_use_reenable_onport"] = form_input_validate($setting_imb_use_reenable_onport, "setting_imb_use_reenable_onport", "", false, 3);
 	
	$save["snmp_oid_swL2PortCtrlAdminState"] = form_input_validate(imb_check_for_oid($snmp_oid_swL2PortCtrlAdminState), "snmp_oid_swL2PortCtrlAdminState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_swL2PortCtrlSpeedState"] = form_input_validate(imb_check_for_oid($snmp_oid_swL2PortCtrlSpeedState), "snmp_oid_swL2PortCtrlSpeedState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_swL2PortSpeedStatus"] = form_input_validate(imb_check_for_oid($snmp_oid_swL2PortSpeedStatus), "snmp_oid_swL2PortSpeedStatus", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_swL2LoopDetectPortState"] = form_input_validate(imb_check_for_oid($snmp_oid_swL2LoopDetectPortState), "snmp_oid_swL2LoopDetectPortState", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["snmp_oid_swL2LoopDetectPortLoopVLAN"] = form_input_validate(imb_check_for_oid($snmp_oid_swL2LoopDetectPortLoopVLAN), "snmp_oid_swL2LoopDetectPortLoopVLAN", "^(\.[0-9]{1,3}){7,20}$", true, 3);
	$save["swL2PortErrPortReason"] = form_input_validate(imb_check_for_oid($swL2PortErrPortReason), "swL2PortErrPortReason", "^(\.[0-9]{1,3}){7,20}$", true, 3);
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 	$device_type_id = 0;
 	if (!is_error_message()) {
 		$device_type_id = sql_save($save, "imb_device_types", "device_type_id");
 
 		if ($device_type_id) {
 			raise_message(1);
 		}else{
 			raise_message(2);
<<<<<<< HEAD
			impb_debug("ERROR: IMPB Device: ($device_id): $hostname, error on save: " . serialize($save));
 		}
	} else {
	impb_debug("ERROR: IMPB Device: ($device_id): $hostname, error on verify: " . serialize($save));
=======
 		}
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 	}
 
 	return $device_type_id;
 }
 
 /* ------------------------
     The "actions" function
    ------------------------ */
 
<<<<<<< HEAD
 function form_impb_dt_actions() {
 	global $config, $device_types_actions, $fields_impb_device_types_edit;

	/* ================= input validation ================= */
	get_filter_request_var('drp_action');
	/* ==================================================== */

 
 	/* if we are to save this form, instead of display it */
 	if (isset_request_var('selected_items')) {
 		$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
 
 		if ($selected_items != false) {
		if (get_request_var('drp_action') == "1") { /* delete */
			foreach ($selected_items as $selected_item) {
				api_impb_device_type_remove($selected_item);
			} 			
 		}elseif (get_request_var('drp_action') == "2") { /* duplicate */
			foreach ($selected_items as $selected_item) {
				duplicate_device_type($selected_item, $_POST["title_format"]);
			} 			
 		}
 
 		header("Location: impb_device_types.php?header=false");
 		exit;
		}
 	}
 
 	/* setup some variables */
 	$device_types_list = ''; $i = 0;
 
 	/* loop through each of the device types selected on the previous page and get more info about them */
 	foreach ($_POST as $var => $val) {
=======
 function form_actions() {
 	global $colors, $config, $device_types_actions, $fields_impblinding_device_types_edit;
 
 	/* if we are to save this form, instead of display it */
 	if (isset($_POST["selected_items"])) {
 		$selected_items = unserialize(stripslashes($_POST["selected_items"]));
 
 		if ($_POST["drp_action"] == "1") { /* delete */
 			for ($i=0; $i<count($selected_items); $i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 
 				api_impblinding_device_type_remove($selected_items[$i]);
 			}
 		}elseif ($_POST["drp_action"] == "2") { /* duplicate */
 			for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 
 				duplicate_device_type($selected_items[$i], $_POST["title_format"]);
 			}
 		}
 
 		header("Location: impblinding_device_types.php");
 		exit;
 	}
 
 	/* setup some variables */
 	$device_types_list = ""; $i = 0;
 
 	/* loop through each of the device types selected on the previous page and get more info about them */
 	while (list($var,$val) = each($_POST)) {
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 		if (ereg("^chk_([0-9]+)$", $var, $matches)) {
 			/* ================= input validation ================= */
 			input_validate_input_number($matches[1]);
 			/* ==================================================== */
 
 			$device_types_info = db_fetch_row("SELECT description FROM imb_device_types WHERE device_type_id=" . $matches[1]);
 			$device_types_list .= "<li>" . $device_types_info["description"] . "<br>";
 			$device_types_array[$i] = $matches[1];
 		}
 
 		$i++;
 	}
 
<<<<<<< HEAD
	top_header();

	form_start('impb_device_types.php?header=false');

	html_start_box($device_types_actions[get_request_var('drp_action')], '60%', true, '3', 'center', '');

	if (!sizeof($device_types_array)) {
		print "<tr><td class='even'><span class='textError'>" . __('You must select at least one device.') . "</span></td></tr>\n";
		$save_html = "";
	}else{
		$save_html = "<input type='submit' value='" . __('Yes') . "' name='save'>";	
		
		if (get_request_var('drp_action') == '1') { /* delete */
			print "	<tr>
					<td colspan='2' class='textArea'>
						<p>Are you sure you want to use the following device types?</p>
						<p><ul>$device_types_list</ul></p>
					</td>
				</tr>\n
				";		
		}elseif (get_request_var('drp_action') == '2') { /* duplicate */
			print "	<tr>
					<td colspan='2' class='textArea'>
						<p>Когда Вы нажмете Save, следующее тип устройства будет дублирован. Вы можете
						опциональноизменить изменить название для нового типа устройств</p>
						<p><ul>$device_types_list</ul></p>
						<p><strong>Title Format:</strong><br>"; form_text_box("title_format", "<template_title> (1)", "", "255", "30", "text"); print "</p>
					</td>
				</tr>\n
				";	
 	 	}	
	}
	print "<tr>
		<td colspan='2' align='right' class='saveRow'>
			<input type='hidden' name='action' value='actions'>
			<input type='hidden' name='selected_items' value='" . (isset($device_types_array) ? serialize($device_types_array) : '') . "'>
			<input type='hidden' name='drp_action' value='" . get_request_var('drp_action') . "'>" . (strlen($save_html) ? "
			<input type='button' name='cancel' onClick='cactiReturnTo()' value='" . __('No') . "'>
			$save_html" : "<input type='button' onClick='cactiReturnTo()' name='cancel' value='" . __('Return') . "'>") . "
		</td>
	</tr>";

	html_end_box();

	form_end();

	bottom_footer();
}
 
 /* ---------------------
     impb Device Type Functions
    --------------------- */
 
 
function impb_device_type_remove() {
	global $config;

	/* ================= input validation ================= */

	get_filter_request_var('device_type_id');
	/* ==================================================== */

	if ((read_config_option('remove_verification') == 'on') && (!isset_request_var('confirm'))) {
		top_header();
		form_confirm(__('Are You Sure?'), __('Are you sure you want to delete the device type %s', db_fetch_cell_prepared('SELECT description FROM imb_device_types WHERE device_type_id = ?', array(get_request_var('device_type_id')))), 'impb_device_types.php', 'impb_device_types.php?action=remove&id=' . get_request_var('device_id'));
		bottom_footer();
		exit;
	}

	if ((read_config_option('remove_verification') == '') || (isset_request_var('confirm'))) {
		api_impb_device_type_remove(get_request_var('device_type_id'));
	}
}
 
function impb_device_type_edit() {
 	global $fields_impb_device_type_edit;
=======
 	include_once("./include/top_header.php");
 
 	html_start_box("<strong>" . $device_types_actions{$_POST["drp_action"]} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
 	print "<form action='impblinding_device_types.php' method='post'>\n";
 
 	if ($_POST["drp_action"] == "1") { /* delete */
 		print "	<tr>
 				<td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
 					<p>Are you sure you want to use the following device types?</p>
 					<p>$device_types_list</p>
 				</td>
 			</tr>\n
 			";
 	}elseif ($_POST["drp_action"] == "2") { /* clone */
 		print "	<tr>
 				<td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
 					<p>Когда Вы нажмете Save, следующее тип устройства будет дублирован. Вы можете
 					опциональноизменить изменить название для нового типа устройств</p>
 					<p>$device_types_list</p>
 					<p><strong>Title Format:</strong><br>"; form_text_box("title_format", "<template_title> (1)", "", "255", "30", "text"); print "</p>
 				</td>
 			</tr>\n
 			";	
 	
 	}
 
 	if (!isset($device_types_array)) {
 		print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>You must select at least one device type.</span></td></tr>\n";
 		$save_html = "";
 	}else{
 		$save_html = "<input type='image' src='" . $config['url_path'] . "images/button_yes.gif' alt='Save' align='absmiddle'>";
 	}
 
 	print "	<tr>
 			<td colspan='2' align='right' bgcolor='#eaeaea'>
 				<input type='hidden' name='action' value='actions'>
 				<input type='hidden' name='selected_items' value='" . (isset($device_types_array) ? serialize($device_types_array) : '') . "'>
 				<input type='hidden' name='drp_action' value='" . $_POST["drp_action"] . "'>
 				<a href='impblinding_device_types.php'><img src='" . $config['url_path'] . "images/button_no.gif' alt='Cancel' align='absmiddle' border='0'></a>
 				$save_html
 			</td>
 		</tr>
 		";
 
 	html_end_box();
 
 	include_once("./include/bottom_footer.php");
 }
 
 /* ---------------------
     impblinding Device Type Functions
    --------------------- */
 
 
 function impblinding_device_type_remove() {
 	global $config;
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 	/* ================= input validation ================= */
 	input_validate_input_number(get_request_var("device_type_id"));
 	/* ==================================================== */
 
<<<<<<< HEAD
=======
 	if ((read_config_option("remove_verification") == "on") && (!isset($_GET["confirm"]))) {
 		include("./include/top_header.php");
 		form_confirm("Are You Sure?", "Are you sure you want to delete the device type<strong>'" . db_fetch_cell("select description from host where id=" . $_GET["device_id"]) . "'</strong>?", "impblinding_device_types.php", "impblinding_device_types.php?action=remove&id=" . $_GET["device_type_id"]);
 		include("./include/bottom_footer.php");
 		exit;
 	}
 
 	if ((read_config_option("remove_verification") == "") || (isset($_GET["confirm"]))) {
 		api_impblinding_device_type_remove($_GET["device_type_id"]);
 	}
 }
 
 function impblinding_device_type_edit() {
 	global $colors, $fields_impblinding_device_type_edit;
 
 	/* ================= input validation ================= */
 	input_validate_input_number(get_request_var("device_type_id"));
 	/* ==================================================== */
 
 	display_output_messages();
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 	if (!empty($_GET["device_type_id"])) {
 		$device_type = db_fetch_row("select * from imb_device_types where device_type_id=" . $_GET["device_type_id"]);
 		$header_label = "[edit: " . $device_type["description"] . "]";
 	}else{
 		$header_label = "[new]";
 	}
 
<<<<<<< HEAD
 	
	form_start('impb_device_types.php');
	html_start_box($header_label, '100%', true, '3', 'center', '');

 
 	draw_edit_form(array(
 		"config" => array("form_name" => "chk"),
		'config' => array('no_form_tag' => true),
 		"fields" => inject_form_variables($fields_impb_device_type_edit, (isset($device_type) ? $device_type : array()))
=======
 	html_start_box("<strong>D-Link IP-MAC-Port Blinding Device Types</strong> $header_label", "98%", $colors["header"], "3", "center", "");
 
 	draw_edit_form(array(
 		"config" => array("form_name" => "chk"),
 		"fields" => inject_form_variables($fields_impblinding_device_type_edit, (isset($device_type) ? $device_type : array()))
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 		));
 
 	html_end_box();
 
<<<<<<< HEAD
 	form_save_button("impb_device_types.php", "", "device_type_id");
 }
 
function impb_get_device_types(&$sql_where, $rows, $apply_limits = TRUE) {
 	/* form the 'where' clause for our main sql query */
	if (get_request_var('filter') != '') {
		$sql_where = (strlen($sql_where) ? ' AND ': 'WHERE ') . "(imb_device_types.description like '%%" . get_request_var('filter') . "%%'
			OR imb_device_types.description like '%%" . get_request_var('filter') . "%%')";
	}

	$sql_order = get_order_string();
	
	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}
	
	$query_string = "SELECT 
		imb_device_types.*, 
		count(imb_devices.device_id) as count_devices
		FROM imb_device_types 
		LEFT JOIN imb_devices 
		ON (imb_devices.device_type_id=imb_device_types.device_type_id)
		group by device_type_id 
		$sql_where
		$sql_order
		$sql_limit ";
	
		return db_fetch_assoc($query_string);
 
 }
 
function impb_device_type_request_validation() {
	/* ================= input validation and session storage ================= */
	$filters = array(
		'rows' => array(
			'filter' => FILTER_VALIDATE_INT,
			'pageset' => true,
			'default' => '-1'
			),
		'page' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '1'
			),
		'filter' => array(
			'filter' => FILTER_CALLBACK,
			'pageset' => true,
			'default' => '',
			'options' => array('options' => 'sanitize_search_string')
			),
		'sort_column' => array(
			'filter' => FILTER_CALLBACK,
			'default' => 'device_type_id',
			'options' => array('options' => 'sanitize_search_string')
			),
		'sort_direction' => array(
			'filter' => FILTER_CALLBACK,
			'default' => 'ASC',
			'options' => array('options' => 'sanitize_search_string')
			),
		'site_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
		'type_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
		'status' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
		'device_type_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
		'detail' => array(
			'filter' => FILTER_CALLBACK,
			'default' => 'false',
			'options' => array('options' => 'sanitize_search_string')
			),
	);

	validate_store_request_vars($filters, 'sess_impb_device_type');
	/* ================= input validation ================= */
}
 
 
function impb_device_type() {
 	global $device_types_actions, $impb_device_types, $config, $impb_imb_yes_no, $item_rows;

 
 	impb_device_type_request_validation();

	if (get_request_var('rows') == -1) {
		$rows = read_config_option('num_rows_table');
	}elseif (get_request_var('rows') == -2) {
		$rows = 999999;
	}else{
		$rows = get_request_var('rows');
	}	

	html_start_box('IMPB Device Type Filters', '100%', '', '3', 'center', 'impb_device_types.php?action=edit&status=' . get_request_var('status'));
	impb_device_type_filter();
	html_end_box();

	$sql_where = '';

 	$device_types = impb_get_device_types($sql_where, $rows);
 
 	$total_rows = db_fetch_cell("SELECT
 		COUNT(*)
 		FROM imb_device_types
 		$sql_where");
 
 	$nav = html_nav_bar('impb_device_types.php?filter=' . get_request_var('filter'), MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 6, __('Devices Types'), 'page', 'main');

	form_start('impb_device_types.php', 'chk');	

	print $nav;

	html_start_box('', '100%', '', '3', 'center', '');

	$display_text = array(
		
		'description'      => array(__('Device Type<br>Description'), 'ASC'),
		'scanning_function' => array(__('Scanning<br>function'), 'ASC'),
 		'count_devices' => array(__('Количество устройств'), 'ASC'),
 		'setting_imb_use_autoban' => array(__('Возможность автобана'), 'ASC'));

 
	html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false);

	if (sizeof($device_types)) {
		foreach ($device_types as $device_type) {
			form_alternate_row('line' . $device_type['device_type_id'], true);
 			form_selectable_cell("<a class='linkEditMain' href='impb_device_types.php?action=edit&device_type_id=" . $device_type["device_type_id"] . "'>" . $device_type["description"] . "</a>", $device_type["device_type_id"],250);
 			form_selectable_cell($device_type["scanning_function"], $device_type["device_type_id"] );				
 			form_selectable_cell($device_type["count_devices"], $device_type["device_type_id"] );	
 			form_selectable_cell($impb_imb_yes_no[$device_type["setting_imb_use_autoban"]], $device_type["device_type_id"] );	
 			form_checkbox_cell($device_type["description"], $device_type["device_type_id"]);			
		}
	}else{
		print '<tr><td colspan="10"><em>' . __('No D-Link IP-MAC-Port Blinding Device Types') . '</em></td></tr>';
	}    

	html_end_box(false);

	if (sizeof($device_types)) {
		print $nav;
	}

	/* draw the dropdown containing a list of available actions for this form */
	draw_actions_dropdown($device_types_actions);

	form_end();


}


function impb_device_type_filter() {
	global $item_rows;

	?>
	<tr class='even'>
		<td>
		<form id='impb'>
			<table class='filterTable'>
				<tr>
					<td>
						<?php print __('Search');?>
					</td>
					<td>
						<input type='text' id='filter' size='25' value='<?php print get_request_var('filter');?>'>
					</td>
					<td>
						<input type='submit' id='go' value='<?php print __('Go');?>'>
					</td>
					<td>
						<input type='button' id='clear' value='<?php print __('Clear');?>'>
					</td>
					<td>
						<input type='button' id='import' value='<?php print __('Import');?>'>
					</td>
					<td>
						<input type='submit' id='export' value='<?php print __('Export');?>'>
					</td>
				</tr>
			</table>
		</form>
		<script type='text/javascript'>
		function applyFilter() {
			strURL  = urlPath+'plugins/impb/impb_devices_types.php?header=false';
			strURL += '&filter=' + $('#filter').val();
			strURL += '&rows=' + $('#rows').val();
			loadPageNoHeader(strURL);
		}

		function clearFilter() {
			strURL  = urlPath+'plugins/impb/impb_devices_types.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}

		function exportRows() {
			strURL  = urlPath+'plugins/impb/impb_devices_types.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_devices_types.php?import=true';
			loadPageNoHeader(strURL);
		}

		$(function() {
			$('#impb').submit(function(event) {
				event.preventDefault();
				applyFilter();
			});

			$('#clear').click(function() {
				clearFilter();
			});

			$('#export').click(function() {
				exportRows();
			});

			$('#import').click(function() {
				importRows();
			});
		});
		</script>
		</td>
	</tr>
	<?php
}

=======
 	form_save_button("impblinding_device_types.php", "", "device_type_id");
 }
 
 function impblinding_get_device_types(&$sql_where) {
 	return db_fetch_assoc("SELECT imb_device_types.*, count(imb_devices.device_id) as count_devices  from imb_device_types  " .
 		" left join imb_devices " .
 		" on (imb_devices.device_type_id=imb_device_types.device_type_id) group by device_type_id " .
 		" ORDER BY " . $_REQUEST["sort_column"] . " " . $_REQUEST["sort_direction"] . ";");
 
 }
 
 
 
 function impblinding_device_type() {
 	global $colors, $device_types_actions, $impblinding_device_types, $config, $impblinding_imb_yes_no;
 
 	/* ================= input validation ================= */
 	input_validate_input_number(get_request_var_request("page"));
 //	input_validate_input_number(get_request_var_request("type_id"));
 	/* ==================================================== */
 
  /* clean up sort_column */
     if (isset($_REQUEST["sort_column"])) {
         $_REQUEST["sort_column"] = sanitize_search_string(get_request_var("sort_column"));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["sort_direction"])) {
         $_REQUEST["sort_direction"] = sanitize_search_string(get_request_var("sort_direction"));
     }
 	
 	/* if the user pushed the 'clear' button */
 	if (isset($_REQUEST["clear_x"])) {
 		kill_session_var("sess_impb_device_current_page");
 		kill_session_var("sess_impb_device_filter");
 		kill_session_var("sess_impb_device_type_vendor");
 		kill_session_var("sess_impb_device_type_type_id");
 		kill_session_var("sess_impb_device_type_type_sort_column");
         kill_session_var("sess_impb_device_type_type_sort_direction");		
 
 		unset($_REQUEST["page"]);
 		unset($_REQUEST["vendor"]);
 		unset($_REQUEST["type_id"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);				
 	}
 
 	/* remember these search fields in session vars so we don't have to keep passing them around */
 	load_current_session_value("page", "sess_impb_device_type_current_page", "1");
 	load_current_session_value("vendor", "sess_impb_device_type_vendor", "All");
 	load_current_session_value("type_id", "sess_impb_device_type_type_id", "-1");
     load_current_session_value("sort_column", "sess_impb_device_type_type_sort_column", "description");
     load_current_session_value("sort_direction", "sess_impb_device_type_type_sort_direction", "ASC");	
 
 	html_start_box("<strong>D-Link IP-MAC-Port Blinding Device Types</strong>", "98%", $colors["header"], "3", "center", "impblinding_device_types.php?action=edit");
 
 	html_end_box();
 
 	$sql_where = "";
 
 	$device_types = impblinding_get_device_types($sql_where);
 
 	html_start_box("", "98%", $colors["header"], "5", "center", "");
 
 	$total_rows = db_fetch_cell("SELECT count(*) FROM imb_device_types ;");
 
 	/* generate page list */
 	$url_page_select = get_page_list($_REQUEST["page"], MAX_DISPLAY_PAGES, read_config_option("dimpb_num_rows"), $total_rows, "impblinding_device_types.php?");
 
 	$nav = "<tr bgcolor='#" . $colors["header"] . "'>
 			<td colspan='5'>
 				<table width='100%' cellspacing='0' cellpadding='0' border='0'>
 					<tr>
 						<td align='left' class='textHeaderDark'>
 							<strong>&lt;&lt; "; if ($_REQUEST["page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_device_types.php?page=" . ($_REQUEST["page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
 						</td>\n
 						<td align='center' class='textHeaderDark'>
 							Showing Rows " . ((read_config_option("dimpb_num_rows")*($_REQUEST["page"]-1))+1) . " to " . ((($total_rows < read_config_option("dimpb_num_rows")) || ($total_rows < (read_config_option("dimpb_num_rows")*$_REQUEST["page"]))) ? $total_rows : (read_config_option("dimpb_num_rows")*$_REQUEST["page"])) . " of $total_rows [$url_page_select]
 						</td>\n
 						<td align='right' class='textHeaderDark'>
 							<strong>"; if (($_REQUEST["page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_device_types.php?page=" . ($_REQUEST["page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
 						</td>\n
 					</tr>
 				</table>
 			</td>
 		</tr>\n";
 
 	print $nav;
 	$display_text = array(
 		"description" => array("Device Type<br>Description", "ASC"),
 		"scanning_function" => array("Scanning<br>function", "ASC"),
 		"count_devices" => array("Количество устройств", "ASC"),
 		"setting_imb_use_autoban" => array("Возможность автобана", "ASC"));
 
 
     html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 	
 	//html_header_checkbox(array( "Device Type<br>Description","Scanning<br>function"));
 
 	$i = 0;
 	if (sizeof($device_types) > 0) {
 		foreach ($device_types as $device_type) {
 			form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $device_type["device_type_id"]); $i++;
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_device_types.php?action=edit&device_type_id=" . $device_type["device_type_id"] . "'>" . $device_type["description"] . "</a>", $device_type["device_type_id"],250);
 				form_selectable_cell($device_type["scanning_function"], $device_type["device_type_id"] );				
 				form_selectable_cell($device_type["count_devices"], $device_type["device_type_id"] );	
 				form_selectable_cell($impblinding_imb_yes_no[$device_type["setting_imb_use_autoban"]], $device_type["device_type_id"] );	
 				form_checkbox_cell($device_type["description"], $device_type["device_type_id"]);
 				
 		}
 
 		
 /* 		foreach ($device_types as $device_type) {
 			form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 				?>
 				<td width=170>
 					<a class="linkEditMain" href="impblinding_device_types.php?action=edit&device_type_id=<?php print $device_type["device_type_id"];?>"><?php print $device_type["description"];?></a>
 				</td>
 				<td><?php print $device_type["scanning_function"];?></td>
 				<td style="<?php print get_checkbox_style();?>" width="1%" align="right">
 					<input type='checkbox' style='margin: 0px;' name='chk_<?php print $device_type["device_type_id"];?>' title="<?php print $device_type["description"];?>">
 				</td>
 			</tr>
 			<?php
 		} */		
 		
 		
 		
 		/* put the nav bar on the bottom as well */
 		print $nav;
 	}else{
 		print "<tr><td><em>No D-Link IP-MAC-Port Blinding Device Types</em></td></tr>";
 	}
 	html_end_box(false);
 
 	/* draw the dropdown containing a list of available actions for this form */
 	draw_actions_dropdown($device_types_actions);
 }
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 ?>
