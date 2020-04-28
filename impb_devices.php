<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright (C) 2007 Susanin                                         |
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
 include("./include/auth.php");
 include_once("./lib/snmp.php");
<<<<<<< HEAD
 include_once($config["base_path"] . "/plugins/impb/lib/impb_functions.php");
  
 
 //define("MAX_DISPLAY_PAGES", 21);
 

$device_actions = array(
	1 => __('Delete'),
	2 => __('Enable'),
	3 => __('Disable'),
	4 => __('Change SNMP Options'),
	5 => __('Update info'),
	6 => __('Save configs')
);
	
set_default_action();

switch (get_request_var('action')) {
	case 'save':
		form_impb_save();

		break;
	case 'actions':
		form_device_actions();

		break;
	case 'edit':
		top_header();
		impb_device_edit();
		bottom_footer();

		break;
	case 'import':
		top_header();
		impb_device_import();
		bottom_footer();

		break;
	default:
		if (isset_request_var('import')) {
			header('Location: impb_devices.php?action=import');
		}elseif (isset_request_var('export')) {
			impb_device_export();
		}else{
			top_header();
			impb_device();
			bottom_footer();
		}

		break;
}

 
=======
  include_once($config["base_path"] . "/plugins/impblinding/lib/impblinding_functions.php");
 //include_once($config["base_path"] . "/plugins/impblinding/poller_impblinding.php"); 
 
 define("MAX_DISPLAY_PAGES", 21);
 
 $device_actions = array(
 	1 => "Delete",
 	2 => "Enable",
 	3 => "Disable",
 	4 => "Change SNMP Options",
     5 => "Poll NOW!",
 	6 => "Сохранить конфигурацию"
 //	5 => "Change Device Port Values"
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
 
 		impblinding_device_edit();
 
 		include_once("./include/bottom_footer.php");
 		break;
 	case 'import':
 		include_once("./include/top_header.php");
 
 		impblinding_device_import();
 
 		include_once("./include/bottom_footer.php");
 		break;
 	default:
 		if (isset($_REQUEST["import_x"])) {
 			header("Location: impblinding_devices.php?action=import");
 		}elseif (isset($_REQUEST["export_x"])) {
 			impblinding_device_export();
 		}else{
 			include_once("./include/top_header.php");
 
 			impblinding_device();
 
 			include_once("./include/bottom_footer.php");
 		}
 		break;
 }
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 /* --------------------------
     The Save Function
    -------------------------- */
<<<<<<< HEAD
function form_impb_save() {
	global $config;

 	if ((isset_request_var('save_component_device')) && (isempty_request_var('add_dq_y'))) {
 		$device_id = api_impb_device_save(get_nfilter_request_var('device_id'), 
 			get_nfilter_request_var('hostname'), get_nfilter_request_var('device_type_id'),get_nfilter_request_var('description'),
			get_nfilter_request_var('order_id'),get_nfilter_request_var('color_row'),get_nfilter_request_var('snmp_max_oids'),
 			get_nfilter_request_var('snmp_get_version'), get_nfilter_request_var('snmp_get_community'),get_nfilter_request_var('snmp_get_username'),
			get_nfilter_request_var('snmp_get_password'),get_nfilter_request_var('snmp_get_auth_protocol'),get_nfilter_request_var('snmp_get_priv_passphrase'),
			get_nfilter_request_var('snmp_get_priv_protocol'),get_nfilter_request_var('snmp_get_context'),
			get_nfilter_request_var('snmp_set_version'), get_nfilter_request_var('snmp_set_community'),get_nfilter_request_var('snmp_set_username'),
			get_nfilter_request_var('snmp_set_password'), get_nfilter_request_var('snmp_set_auth_protocol'),get_nfilter_request_var('snmp_set_priv_passphrase'),
			get_nfilter_request_var('snmp_set_priv_protocol'), get_nfilter_request_var('snmp_set_context'),get_nfilter_request_var('snmp_port'),
			get_nfilter_request_var('snmp_timeout'), get_nfilter_request_var('snmp_retries'),
 			(isset_request_var('disabled') ? get_nfilter_request_var('disabled') : ''));
 
 		header('Location: impb_devices.php?header=false&action=edit&device_id=' . (empty($device_id) ? get_filter_request_var('device_id') : $device_id));
		
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
			header('Location: impb_devices.php?action=import'); exit;
		}

		header('Location: impb_devices.php?action=import');
	}
}
 

 
function api_impb_device_save($device_id, $hostname, $device_type_id, $description, $order_id, $color_row, $snmp_max_oids,
=======
 
 function form_save() {
 	if ((isset($_POST["save_component_device"])) && (empty($_POST["add_dq_y"]))) {
 		$device_id = api_impblinding_device_save($_POST["device_id"], 
 			$_POST["hostname"], $_POST["device_type_id"], $_POST["description"],$_POST["order_id"],$_POST["color_row"],  $_POST["snmp_max_oids"], 
 			$_POST["snmp_get_version"], $_POST["snmp_get_community"],  $_POST["snmp_get_username"], $_POST["snmp_get_password"], $_POST["snmp_get_auth_protocol"],$_POST["snmp_get_priv_passphrase"],$_POST["snmp_get_priv_protocol"],$_POST["snmp_get_context"],
 			$_POST["snmp_set_version"], $_POST["snmp_set_community"], $_POST["snmp_set_username"], $_POST["snmp_set_password"],$_POST["snmp_set_auth_protocol"],$_POST["snmp_set_priv_passphrase"],$_POST["snmp_set_priv_protocol"],$_POST["snmp_set_context"],
 			$_POST["snmp_port"], $_POST["snmp_timeout"],
 			$_POST["snmp_retries"], 
 			(isset($_POST["disabled"]) ? $_POST["disabled"] : ""));
 
 		if ((is_error_message()) || ($_POST["device_id"] != $_POST["_device_id"])) {
 			header("Location: impblinding_devices.php?action=edit&device_id=" . (empty($device_id) ? $_POST["device_id"] : $device_id));
 		}else{
 			header("Location: impblinding_devices.php");
 		}
 	}
 
 	if (isset($_POST["save_component_import"])) {
 		if (($_FILES["import_file"]["tmp_name"] != "none") && ($_FILES["import_file"]["tmp_name"] != "")) {
 			/* file upload */
 			$csv_data = file($_FILES["import_file"]["tmp_name"]);
 
 			/* obtain debug information if it's set */
 			$debug_data = impblinding_device_import_processor($csv_data);
 			if(sizeof($debug_data) > 0) {
 				$_SESSION["import_debug_info"] = $debug_data;
 			}
 		}else{
 			header("Location: impblinding_devices.php?action=import"); exit;
 		}
 
 		header("Location: impblinding_devices.php?action=import");
 	}
 }
 
 function api_impblinding_device_remove($device_id){
 	db_execute("DELETE FROM imb_devices WHERE device_id=" . $device_id);
 	db_execute("DELETE FROM imb_macip WHERE device_id=" . $device_id);
 	db_execute("DELETE FROM imb_ports WHERE device_id=" . $device_id);
 	db_execute("DELETE FROM imb_mactrack_recent_ports WHERE device_id=" . $device_id);
 }
 
 function api_impblinding_device_save($device_id, $hostname, $device_type_id, $description, $order_id, $color_row, $snmp_max_oids,
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 			$snmp_get_version,$snmp_get_community,  $snmp_get_username, $snmp_get_password,$snmp_get_auth_protocol,$snmp_get_priv_passphrase,$snmp_get_priv_protocol,$snmp_get_context,
 			$snmp_set_version,$snmp_set_community,  $snmp_set_username, $snmp_set_password,$snmp_set_auth_protocol,$snmp_set_priv_passphrase,$snmp_set_priv_protocol,$snmp_set_context,
 			$snmp_port, $snmp_timeout, $snmp_retries,
 			$disabled) {
 			
<<<<<<< HEAD
 	$save['device_id'] 					= $device_id;
 	$save['hostname'] 					= form_input_validate($hostname, 'hostname', '', false, 3);
 	$save['device_type_id'] 			= form_input_validate($device_type_id, 'device_type_id', '', false, 3);
 	$save['description'] 				= form_input_validate($description, 'description', '', false, 3);
	$save['order_id'] 					= form_input_validate($order_id, 'order_id', '', false, 3);
	$save['color_row'] 					= form_input_validate($color_row, 'color_row', '', false, 3);
 	
 	$save['snmp_get_community'] 		= form_input_validate($snmp_get_community, 'snmp_get_community', '', true, 3);
 	$save['snmp_get_version'] 			= form_input_validate($snmp_get_version, 'snmp_get_version', '', true, 3);
 	$save['snmp_get_username'] 			= form_input_validate($snmp_get_username, 'snmp_get_username', '', true, 3);
 	$save['snmp_get_password'] 			= form_input_validate($snmp_get_password, 'snmp_get_password', '', true, 3);
 	$save['snmp_get_auth_protocol']   	= form_input_validate($snmp_get_auth_protocol, 'snmp_get_auth_protocol', '', true, 3);
 	$save['snmp_get_priv_passphrase'] 	= form_input_validate($snmp_get_priv_passphrase, 'snmp_get_priv_passphrase', '', true, 3);
 	$save['snmp_get_priv_protocol']   	= form_input_validate($snmp_get_priv_protocol, 'snmp_get_priv_protocol', '', true, 3);
 	$save['snmp_get_context']         	= form_input_validate($snmp_get_context, 'snmp_get_context', '', true, 3);	
 	
 	$save['snmp_set_community'] 		= form_input_validate($snmp_set_community, 'snmp_set_community', '', true, 3);
 	$save['snmp_set_version'] 			= form_input_validate($snmp_set_version, 'snmp_set_version', '', true, 3);
 	$save['snmp_set_username'] 			= form_input_validate($snmp_set_username, 'snmp_set_username', '', true, 3);
 	$save['snmp_set_password'] 			= form_input_validate($snmp_set_password, 'snmp_set_password', '', true, 3);
 	$save['snmp_set_auth_protocol']   	= form_input_validate($snmp_set_auth_protocol, 'snmp_set_auth_protocol', '', true, 3);
 	$save['snmp_set_priv_passphrase'] 	= form_input_validate($snmp_set_priv_passphrase, 'snmp_set_priv_passphrase', '', true, 3);
 	$save['snmp_set_priv_protocol']   	= form_input_validate($snmp_set_priv_protocol, 'snmp_set_priv_protocol', '', true, 3);
 	$save['snmp_set_context']         	= form_input_validate($snmp_set_context, 'snmp_set_context', '', true, 3);	
 	
 	$save['snmp_port'] 					= form_input_validate($snmp_port, 'snmp_port', '^[0-9]+$', false, 3);
 	$save['snmp_timeout'] 				= form_input_validate($snmp_timeout, 'snmp_timeout', '^[0-9]+$', false, 3);
 	$save['snmp_retries'] 				= form_input_validate($snmp_retries, 'snmp_retries', '^[0-9]+$', false, 3);
 	$save['snmp_max_oids'] 				= form_input_validate($snmp_max_oids, 'snmp_max_oids', '^[0-9]+$', false, 3);
 	$save['disabled'] 					= form_input_validate($disabled, 'disabled', '', true, 3);
 
 	$device_id = 0;
 	if (!is_error_message()) {
 		$device_id = sql_save($save, 'imb_devices', 'device_id');
=======
 	$save["device_id"] = $device_id;
 	$save["hostname"] = form_input_validate($hostname, "hostname", "", false, 3);
 	$save["device_type_id"] = form_input_validate($device_type_id, "device_type_id", "", false, 3);
 	$save["description"] = form_input_validate($description, "description", "", false, 3);
	$save["order_id"] = form_input_validate($order_id, "order_id", "", false, 3);
	$save["color_row"] = form_input_validate($color_row, "color_row", "", false, 3);
 	
 	$save["snmp_get_community"] = form_input_validate($snmp_get_community, "snmp_get_community", "", true, 3);
 	$save["snmp_get_version"] = form_input_validate($snmp_get_version, "snmp_get_version", "", true, 3);
 	$save["snmp_get_username"] = form_input_validate($snmp_get_username, "snmp_get_username", "", true, 3);
 	$save["snmp_get_password"] = form_input_validate($snmp_get_password, "snmp_get_password", "", true, 3);
 	$save["snmp_get_auth_protocol"]   = form_input_validate($snmp_get_auth_protocol, "snmp_get_auth_protocol", "", true, 3);
 	$save["snmp_get_priv_passphrase"] = form_input_validate($snmp_get_priv_passphrase, "snmp_get_priv_passphrase", "", true, 3);
 	$save["snmp_get_priv_protocol"]   = form_input_validate($snmp_get_priv_protocol, "snmp_get_priv_protocol", "", true, 3);
 	$save["snmp_get_context"]         = form_input_validate($snmp_get_context, "snmp_get_context", "", true, 3);	
 	
 	$save["snmp_set_community"] = form_input_validate($snmp_set_community, "snmp_set_community", "", true, 3);
 	$save["snmp_set_version"] = form_input_validate($snmp_set_version, "snmp_set_version", "", true, 3);
 	$save["snmp_set_username"] = form_input_validate($snmp_set_username, "snmp_set_username", "", true, 3);
 	$save["snmp_set_password"] = form_input_validate($snmp_set_password, "snmp_set_password", "", true, 3);
 	$save["snmp_set_auth_protocol"]   = form_input_validate($snmp_set_auth_protocol, "snmp_set_auth_protocol", "", true, 3);
 	$save["snmp_set_priv_passphrase"] = form_input_validate($snmp_set_priv_passphrase, "snmp_set_priv_passphrase", "", true, 3);
 	$save["snmp_set_priv_protocol"]   = form_input_validate($snmp_set_priv_protocol, "snmp_set_priv_protocol", "", true, 3);
 	$save["snmp_set_context"]         = form_input_validate($snmp_set_context, "snmp_set_context", "", true, 3);	
 	
 	$save["snmp_port"] = form_input_validate($snmp_port, "snmp_port", "^[0-9]+$", false, 3);
 	$save["snmp_timeout"] = form_input_validate($snmp_timeout, "snmp_timeout", "^[0-9]+$", false, 3);
 	$save["snmp_retries"] = form_input_validate($snmp_retries, "snmp_retries", "^[0-9]+$", false, 3);
 	$save["snmp_max_oids"] = form_input_validate($snmp_max_oids, "snmp_max_oids", "^[0-9]+$", false, 3);
 	$save["disabled"] = form_input_validate($disabled, "disabled", "", true, 3);
 
 	$device_id = 0;
 	if (!is_error_message()) {
 		$device_id = sql_save($save, "imb_devices", "device_id");
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 		if ($device_id) {
 			raise_message(1);
 		}else{
 			raise_message(2);
<<<<<<< HEAD
			impb_debug("ERROR: Cacti Device: ($device_id): $hostname, error on save: " . serialize($save));
 		}
	} else {
		impb_debug("ERROR: Cacti Device: ($device_id): $hostname, error on verify: " . serialize($save));
	}

=======
 		}
 	}
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 	return $device_id;
 }
 
<<<<<<< HEAD
function api_impb_device_remove($device_id){
	db_execute('DELETE FROM imb_devices WHERE device_id=' . $device_id);
	db_execute('DELETE FROM imb_ports WHERE device_id=' . $device_id);
	db_execute('DELETE FROM imb_macip WHERE device_id=' . $device_id);
	db_execute('DELETE FROM imb_blmacs WHERE device_id=' . $device_id);
	db_execute('DELETE FROM imb_log WHERE device_id=' . $device_id);
} 
=======
 
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 /* ------------------------
     The "actions" function
    ------------------------ */
 
<<<<<<< HEAD
 function form_device_actions() {
 	global $config, $device_actions, $fields_impb_device_edit;
	
 	/* ================= input validation ================= */
	get_filter_request_var('drp_action');
	/* ==================================================== */

 	/* if we are to save this form, instead of display it */
 	if (isset_request_var('selected_items')) {
 		$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
 
 		if ($selected_items != false) {
 		if (get_request_var('drp_action') == "2") { /* Enable Selected Devices */
 			foreach ($selected_items as $selected_item) {
 				db_execute("update imb_devices set disabled='' where device_id='" . $selected_item . "'");
 			}
 		}elseif (get_request_var('drp_action') == "3") { /* Disable Selected Devices */
 			foreach ($selected_items as $selected_item) {
 				db_execute("update imb_devices set disabled='on' where device_id='" . $selected_item . "'");
 			}
 		}elseif (get_request_var('drp_action') == "4") { /* change snmp options */
 			foreach ($selected_items as $selected_item) {

 				reset($fields_impb_device_edit);
				foreach ($fields_impb_device_edit as $field_name => $field_array) {
 					if (isset($_POST["t_$field_name"])) {
						db_execute_prepared("UPDATE imb_devices 
							SET $field_name = ?
							WHERE device_id = ?",
							array(get_request_var($field_name), $selected_item)); 						
 					}
 				}
 			}
         }elseif (get_request_var('drp_action') == "5") { /* Poll NOW */
             foreach ($selected_items as $selected_item) {
                 run_poller_impb($selected_item);
             }
         
         }elseif (get_request_var('drp_action') == "6") { /* change port settngs for multiple devices */
 			foreach ($selected_items as $selected_item) {
 				reset($fields_impb_device_edit);
				foreach ($fields_host_edit as $field_name => $field_array){
 					if (isset($_POST["t_$field_name"])) {
						db_execute_prepared("UPDATE imb_devices 
							SET $field_name = ? WHERE id = ?", 
							array(get_request_var($field_name), $selected_item));					
 					}
 				}
 			}
 		}elseif (get_request_var('drp_action') == "1") { /* delete */
 			foreach ($selected_items as $selected_item) {
 				api_impb_device_remove($selected_item);
 			}
 		}
 
 		header("Location: impb_devices.php?header=false");
 		exit;
		}
 	}
 
 
=======
 function form_actions() {
 	global $colors, $config, $device_actions, $fields_impblinding_device_edit;
 
 	/* if we are to save this form, instead of display it */
 	if (isset($_POST["selected_items"])) {
 		$selected_items = unserialize(stripslashes($_POST["selected_items"]));
 
 		if ($_POST["drp_action"] == "2") { /* Enable Selected Devices */
 			for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 
 				db_execute("update imb_devices set disabled='' where device_id='" . $selected_items[$i] . "'");
 			}
 		}elseif ($_POST["drp_action"] == "3") { /* Disable Selected Devices */
 			for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 
 				db_execute("update imb_devices set disabled='on' where device_id='" . $selected_items[$i] . "'");
 			}
 		}elseif ($_POST["drp_action"] == "4") { /* change snmp options */
 			for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 
 				reset($fields_impblinding_device_edit);
 				while (list($field_name, $field_array) = each($fields_impblinding_device_edit)) {
 					if (isset($_POST["t_$field_name"])) {
 						db_execute("update imb_devices set $field_name = '" . $_POST[$field_name] . "' where device_id='" . $selected_items[$i] . "'");
 					}
 				}
 			}
         }elseif ($_POST["drp_action"] == "5") { /* Poll NOW */
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
                 run_poller_impblinding($selected_items[$i]);
             }
         
         
         }elseif ($_POST["drp_action"] == "6") { /* change port settngs for multiple devices */
 			for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 
 				reset($fields_impblinding_device_edit);
 				while (list($field_name, $field_array) = each($fields_host_edit)) {
 					if (isset($_POST["t_$field_name"])) {
 						db_execute("update imb_devices set $field_name = '" . $_POST[$field_name] . "' where id='" . $selected_items[$i] . "'");
 					}
 				}
 			}
 		}elseif ($_POST["drp_action"] == "1") { /* delete */
 			for ($i=0; $i<count($selected_items); $i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 
 				api_impblinding_device_remove($selected_items[$i+1]);
 			}
 		}
 
 		header("Location: impblinding_devices.php");
 		exit;
 	}
 
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 	/* setup some variables */
 	$device_list = ""; $i = 0;
 
 	/* loop through each of the host templates selected on the previous page and get more info about them */
<<<<<<< HEAD
 	foreach ($_POST as $var => $val) {
 		if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
=======
 	while (list($var,$val) = each($_POST)) {
 		if (ereg("^chk_([0-9]+)$", $var, $matches)) {
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 			/* ================= input validation ================= */
 			input_validate_input_number($matches[1]);
 			/* ==================================================== */
 
 			$device_info = db_fetch_row("SELECT hostname, description FROM imb_devices WHERE device_id=" . $matches[1]);
 			$device_list .= "<li>" . $device_info["description"] . " (" . $device_info["hostname"] . ")<br>";
 			$device_array[$i] = $matches[1];
 		}
 
 		$i++;
 	}
 
<<<<<<< HEAD
 	top_header();

	form_start('impb_devices.php?header=false');

	html_start_box($device_actions[get_request_var('drp_action')], '60%', '', '3', 'center', '');

	if (!sizeof($device_array)) {
		print "<tr><td class='even'><span class='textError'>" . __('You must select at least one device.') . "</span></td></tr>\n";
		$save_html = "";
	}else{
		$save_html = "<input type='submit' value='" . __('Yes') . "' name='save'>";	
		
	if (get_request_var('drp_action') == "2") { /* Enable Devices */
 		print "	<tr>
 				<td colspan='2' class='textArea'>
 					<p>To enable the following devices, press the \"yes\" button below.</p>
 					<p><ul>$device_list</ul></p>
 				</td>
 				</tr>";
 	}elseif (get_request_var('drp_action') == "3") { /* Disable Devices */
 		print "	<tr>
 				<td colspan='2' class='textArea'>
 					<p>To disable the following devices, press the \"yes\" button below.</p>
 					<p><ul>$device_list</ul></p>
 				</td>
 				</tr>";
 	}elseif (get_request_var('drp_action') == "4") { /* change snmp options */
 		print "	<tr>
 				<td colspan='2' class='textArea'>
 					<p>To change SNMP parameters for the following devices, check the box next to the fields
 					you want to update, fill in the new value, and click Save.</p>
 					<p><ul>$device_list</ul></p>
 				</td>
 				</tr>";
 				$form_array = array();
				foreach ($fields_impb_device_edit as $field_name => $field_array) {
 					if (ereg("^snmp_", $field_name)) {
 						$form_array += array($field_name => $fields_impb_device_edit[$field_name]);
=======
 	include_once("./include/top_header.php");
 
 	html_start_box("<strong>" . $device_actions{$_POST["drp_action"]} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
 	print "<form action='impblinding_devices.php' method='post'>\n";
 
 	if ($_POST["drp_action"] == "2") { /* Enable Devices */
 		print "	<tr>
 				<td colspan='2' class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
 					<p>To enable the following devices, press the \"yes\" button below.</p>
 					<p>$device_list</p>
 				</td>
 				</tr>";
 	}elseif ($_POST["drp_action"] == "3") { /* Disable Devices */
 		print "	<tr>
 				<td colspan='2' class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
 					<p>To disable the following devices, press the \"yes\" button below.</p>
 					<p>$device_list</p>
 				</td>
 				</tr>";
 	}elseif ($_POST["drp_action"] == "4") { /* change snmp options */
 		print "	<tr>
 				<td colspan='2' class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
 					<p>To change SNMP parameters for the following devices, check the box next to the fields
 					you want to update, fill in the new value, and click Save.</p>
 					<p>$device_list</p>
 				</td>
 				</tr>";
 				$form_array = array();
 				while (list($field_name, $field_array) = each($fields_impblinding_device_edit)) {
 					if (ereg("^snmp_", $field_name)) {
 						$form_array += array($field_name => $fields_impblinding_device_edit[$field_name]);
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 						$form_array[$field_name]["value"] = "";
 						$form_array[$field_name]["description"] = "";
 						$form_array[$field_name]["form_id"] = 0;
 						$form_array[$field_name]["sub_checkbox"] = array(
 							"name" => "t_" . $field_name,
 							"friendly_name" => "Update this Field",
 							"value" => ""
 							);
 					}
 				}
 
 				draw_edit_form(
 					array(
 						"config" => array("no_form_tag" => true),
 						"fields" => $form_array
 						)
 					);
<<<<<<< HEAD
 	}elseif (get_request_var('drp_action') == "5") { /* change port settngs for multiple devices */
 		print "	<tr>
 				<td colspan='2' class='textArea'>
 					<p>To poll the following devices, press the \"yes\" button below.</p>
 					<p><ul>$device_list</ul></p>
 				</td>
 				</tr>";
 	}elseif (get_request_var('drp_action') == "1") { /* delete */
 		print "	<tr>
 				<td colspan='2' class='textArea'>
 					<p>Are you sure you want to delete the following devices?</p>
 					<p><ul>$device_list</ul></p>
=======
 	}elseif ($_POST["drp_action"] == "5") { /* change port settngs for multiple devices */
 		print "	<tr>
 				<td colspan='2' class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
 					<p>To change upper or lower port parameters for the following devices, check the box next to the fields
 					you want to update, fill in the new value, and click Save.</p>
 					<p>$device_list</p>
 				</td>
 				</tr>";
 				$form_array = array();
 				while (list($field_name, $field_array) = each($fields_impblinding_device_edit)) {
 					if (ereg("^port_", $field_name)) {
 						$form_array += array($field_name => $fields_impblinding_device_edit[$field_name]);
 
 						$form_array[$field_name]["value"] = "";
 						$form_array[$field_name]["description"] = "";
 						$form_array[$field_name]["form_id"] = 0;
 						$form_array[$field_name]["sub_checkbox"] = array(
 							"name" => "t_" . $field_name,
 							"friendly_name" => "Update this Field",
 							"value" => ""
 							);
 					}
 				}
 
 				draw_edit_form(
 					array(
 						"config" => array("no_form_tag" => true),
 						"fields" => $form_array
 						)
 					);
 	}elseif ($_POST["drp_action"] == "1") { /* delete */
 		print "	<tr>
 				<td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
 					<p>Are you sure you want to delete the following devices?</p>
 					<p>$device_list</p>
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 				</td>
 			</tr>\n
 			";
 	}
<<<<<<< HEAD
	}
 
	print "	<tr>
 			<td colspan='2' align='right' bgcolor='#eaeaea'>
 				<input type='hidden' name='action' value='actions'>
				<input type='hidden' name='selected_items' value='" . (isset($device_array) ? serialize($device_array) : '') . "'>
				<input type='hidden' name='drp_action' value='" . get_request_var('drp_action') . "'>" . (strlen($save_html) ? "
				<input type='button' name='cancel' onClick='cactiReturnTo()' value='" . __('No') . "'>
				$save_html" : "<input type='button' onClick='cactiReturnTo()' name='cancel' value='" . __('Return') . "'>") . "
 			</td>
 		</tr>
 		";

	html_end_box();

	form_end();

	bottom_footer();		
 }
 
 /* ---------------------
     IMPB  Device Functions
    --------------------- */
 
 function impb_device_remove() {
	global $config;

	/* ================= input validation ================= */
	get_filter_request_var('device_id');
	get_filter_request_var('type_id');
	/* ==================================================== */

	if ((read_config_option('remove_verification') == 'on') && (!isset_request_var('confirm'))) {
		top_header();
		form_confirm(__('Are You Sure?'), __('Are you sure you want to delete the host [%s]', db_fetch_cell_prepared('SELECT description FROM imb_devices WHERE id = ?', array(get_request_var('device_id')))), 'impb_devices.php', 'impb_devices.php?action=remove&id=' . get_request_var('device_id'));
		bottom_footer();
		exit;
	}

	if ((read_config_option('remove_verification') == '') || (isset_request_var('confirm'))) {
		api_impb_device_remove(get_request_var('device_id'));
	}
 }

function impb_device_request_validation() {
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
			'default' => 'order_id',
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

	);

	validate_store_request_vars($filters, 'sess_impb_device');
	/* ================= input validation ================= */
}
 
 function impb_device_edit() {
 	global $config, $fields_impb_device_edit;

	/* ================= input validation ================= */
	get_filter_request_var('device_id');
	/* ==================================================== */
 

	if (!isempty_request_var('device_id')) {
		$device = db_fetch_row_prepared('SELECT * FROM imb_devices WHERE device_id = ?', array(get_request_var('device_id')));
		$header_label = __('IMPB Devices [edit: %s]', $device['description']);
	}else{
		$device = array();
		$header_label = __('IMPB Devices [new]');
	}	
 

 	if (!empty($device['device_id'])) {
 		?>
 		<table width='100%' align='center'>
=======
 
 	if (!isset($device_array)) {
 		print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>You must select at least one device.</span></td></tr>\n";
 		$save_html = "";
 	}else{
 		$save_html = "<input type='image' src='" . $config['url_path'] . "images/button_yes.gif' alt='Save' align='absmiddle'>";
 	}
 
 	print "	<tr>
 			<td colspan='2' align='right' bgcolor='#eaeaea'>
 				<input type='hidden' name='action' value='actions'>
 				<input type='hidden' name='selected_items' value='" . (isset($device_array) ? serialize($device_array) : '') . "'>
 				<input type='hidden' name='drp_action' value='" . $_POST["drp_action"] . "'>
 				<a href='impblinding_devices.php'><img src='" . $config['url_path'] . "images/button_no.gif' alt='Cancel' align='absmiddle' border='0'></a>
 				$save_html
 			</td>
 		</tr>
 		";
 
 	html_end_box();
 
 	include_once("./include/bottom_footer.php");
 }
 
 /* ---------------------
     impblinding Device Functions
    --------------------- */
 
 function impblinding_device_remove() {
 	global $config;
 
 	/* ================= input validation ================= */
 	input_validate_input_number(get_request_var("device_id"));
 	input_validate_input_number(get_request_var("type_id"));
 	/* ==================================================== */
 
 	if ((read_config_option("remove_verification") == "on") && (!isset($_GET["confirm"]))) {
 		include("./include/top_header.php");
 		form_confirm("Are You Sure?", "Are you sure you want to delete the host <strong>'" . db_fetch_cell("select description from host where id=" . $_GET["device_id"]) . "'</strong>?", "impblinding_devices.php", "impblinding_devices.php?action=remove&id=" . $_GET["device_id"]);
 		include("./include/bottom_footer.php");
 		exit;
 	}
 
 	if ((read_config_option("remove_verification") == "") || (isset($_GET["confirm"]))) {
 		api_impblinding_device_remove($_GET["device_id"]);
 	}
 }
 
 function impblinding_device_edit() {
 	global $colors, $fields_impblinding_device_edit;
 
 	/* ================= input validation ================= */
 	input_validate_input_number(get_request_var("device_id"));
 	/* ==================================================== */
 
 	display_output_messages();
 
 	if (!empty($_GET["device_id"])) {
 		$device = db_fetch_row("select * from imb_devices where device_id=" . $_GET["device_id"]);
 		$header_label = "[edit: " . $device["description"] . "]";
 	}else{
 		$header_label = "[new]";
 	}
 
 	if (!empty($device["device_id"])) {
 		?>
 		<table width="98%" align="center">
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 			<tr>
 				<td class="textInfo" colspan="2">
 					<?php print $device["description"];?> (<?php print $device["hostname"];?>)
 				</td>
 			</tr>
 			<tr>
 				<td class="textHeader">
 					SNMP Information<br>
 
<<<<<<< HEAD
 					<span style='font-size: 10px; font-weight: normal; font-family: monospace;'>
 					<?php
 					/* force php to return numeric oid's */
 					cacti_oid_numeric_format();
					
					$snmp_system = cacti_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.1.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
=======
 					<span style="font-size: 10px; font-weight: normal; font-family: monospace;">
 					<?php
 					/* force php to return numeric oid's */
 					if (function_exists("snmp_set_oid_numeric_print")) {
 						snmp_set_oid_numeric_print(TRUE);
 					}
					#http://bugs.cacti.net/view.php?id=2296
					if (function_exists("snmp_set_oid_output_format")) {
							snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
					}	
					
 					$snmp_system = cacti_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.1.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 					if ($snmp_system == "") {
 						print "<span style='color: #ff0000; font-weight: bold;'>SNMP error</span>\n";
 					}else{
 
 						$snmp_uptime = cacti_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.3.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
 						$snmp_hostname = cacti_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.5.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
 						$snmp_objid = cacti_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.2.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
						$snmp_serial = cacti_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.4.1.171.12.1.1.12.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
 						
 						
 						$snmp_objid = str_replace("enterprises", ".1.3.6.1.4.1", $snmp_objid);
 						$snmp_objid = str_replace("OID: ", "", $snmp_objid);
 						$snmp_objid = str_replace(".iso", ".1", $snmp_objid);
						

						
						
						if (is_numeric(substr($snmp_serial, 5,1))) {
							$snmp_serial_y= "200" . substr($snmp_serial, 5,1);
<<<<<<< HEAD
							$snmp_serial_m = "not_set";
=======
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
						}else{
							if (strlen(trim($snmp_serial)) > 0) {
								switch (substr($snmp_serial, 5,1)) {
									case "A":
										$snmp_serial_y= "2010";
										break;
									case "B":
										$snmp_serial_y= "2011";
										break;
									case "C":
										$snmp_serial_y= "2012";
										break;
									case "D":
										$snmp_serial_y= "2013";
										break;									
								}
								$mons = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", "A" => "Oct", "B" => "Nov", "C" => "Dec");
								$snmp_serial_m= $mons[substr($snmp_serial, 6,1)];
							}else{
								$snmp_serial_y = "not_set";
								$snmp_serial_m = "not_set";
							}
						}

 
 						print "<strong>System:</strong> $snmp_system<br>\n";
 							$days = intval($snmp_uptime / (60*60*24*100));
 							$remainder = $snmp_uptime % (60*60*24*100);
 							$hours = intval($remainder / (60*60*100));
 							$remainder = $remainder % (60*60*100);
 							$minutes = intval($remainder / (60*100));
 						print "<strong>Uptime:</strong> $snmp_uptime";
 						print "&nbsp;($days days, $hours hours, $minutes minutes)<br>\n";							
 						print "<strong>Hostname:</strong> $snmp_hostname<br>\n";
 						print "<strong>ObjectID:</strong> $snmp_objid<br>\n";
						print "<strong>SerialN:</strong> $snmp_serial ($snmp_serial_m, $snmp_serial_y)<br>\n";
 					}
 					?>
 					</span>
 				</td>
 			</tr>
 		</table>
 		<br>
 		<?php
 	}
 
<<<<<<< HEAD
 	form_start('impb_devices.php');
	html_start_box($header_label, '100%', true, '3', 'center', '');
	
	/* preserve the devices site id between refreshes via a GET variable */
	if (!isempty_request_var('site_id')) {
		$fields_impb_device_edit['site_id']['value'] = get_request_var('site_id');
	}
 
	draw_edit_form(
		array(
			'config' => array('no_form_tag' => true),
			'fields' => inject_form_variables($fields_impb_device_edit, (isset($device) ? $device : array()))
		)
	);

	html_end_box();

	form_save_button('impb_devices.php', 'return', 'device_id');
 }
 
 function impb_get_devices(&$sql_where, $rows, $apply_limits = TRUE) {
 	/* form the 'where' clause for our main sql query */
	if (get_request_var('filter') != '') {
		$sql_where = (strlen($sql_where) ? ' AND ': 'WHERE ') . "(imb_devices.hostname like '%%" . get_request_var('filter') . "%%'
			OR imb_devices.description like '%%" . get_request_var('filter') . "%%')";
	}	
	

 
 	if (get_request_var('status') == "-1") {
 		/* Show all items */
 	}elseif (get_request_var('status') == "-2") {
 		$sql_where .= (strlen($sql_where) ? ' AND ': 'WHERE ') . " imb_devices.disabled='on'";
 	}else {
 		$sql_where .= (strlen($sql_where) ? ' AND ': 'WHERE ') . " (imb_devices.snmp_status=" . get_request_var('status') . " AND imb_devices.disabled = '')";
 	}
 
 	if (get_request_var('type_id') == "-1") {
 		/* Show all items */
 	}else {
 		$sql_where .= (strlen($sql_where) ? ' AND ': 'WHERE ') . get_request_var('type_id');
 	}
 
 	if (get_request_var('device_type_id') == "-1") {
 		/* Show all items */
 	}else{
 		$sql_where .= (strlen($sql_where) ? ' AND ': 'WHERE ') . " (imb_devices.device_type_id=" . get_request_var('device_type_id') . ")";
 	}

	$sql_order = get_order_string();
	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}
	
 	$query_string = "SELECT
=======
 	html_start_box("<strong>D-Link IP-MAC-Port Binding Devices</strong> $header_label", "98%", $colors["header"], "3", "center", "");
 
 	/* preserve the devices site id between refreshes via a GET variable */
 	if (!empty($_GET["site_id"])) {
 		$fields_host_edit["site_id"]["value"] = $_GET["site_id"];
 	}
 
 	draw_edit_form(array(
 		"config" => array("form_name" => "chk"),
 		"fields" => inject_form_variables($fields_impblinding_device_edit, (isset($device) ? $device : array()))
 		));
 
 	html_end_box();
 	?>
 	<script type="text/javascript">
 	<!--
 
 	// default snmp information
 	var snmp_get_community       = document.getElementById('snmp_get_community').value;
 	var snmp_get_username        = document.getElementById('snmp_get_username').value;
 	var snmp_get_password        = document.getElementById('snmp_get_password').value;
 	var snmp_get_auth_protocol   = document.getElementById('snmp_get_auth_protocol').value;
 	var snmp_get_priv_passphrase = document.getElementById('snmp_get_priv_passphrase').value;
 	var snmp_get_priv_protocol   = document.getElementById('snmp_get_priv_protocol').value;
 	var snmp_get_context         = document.getElementById('snmp_get_context').value;
 
 	var snmp_set_community       = document.getElementById('snmp_set_community').value;
 	var snmp_set_username        = document.getElementById('snmp_set_username').value;
 	var snmp_set_password        = document.getElementById('snmp_set_password').value;
 	var snmp_set_auth_protocol   = document.getElementById('snmp_set_auth_protocol').value;
 	var snmp_set_priv_passphrase = document.getElementById('snmp_set_priv_passphrase').value;
 	var snmp_set_priv_protocol   = document.getElementById('snmp_set_priv_protocol').value;
 	var snmp_set_context         = document.getElementById('snmp_set_context').value;
 
 
 
 	function changeDimpbHostForm() {
 		snmp_get_version        = document.getElementById('snmp_get_version').value;
 		snmp_set_version        = document.getElementById('snmp_set_version').value;
 
 
 		switch(snmp_get_version) {
 		case "1":
 		case "2":
 			setSNMP("v1v2", "get");
 
 			break;
 		case "3":
 			setSNMP("v3", "get");
 
 			break;
 		}
 		switch(snmp_set_version) {
 		case "1":
 		case "2":
 			setSNMP("v1v2", "set");
 
 			break;
 		case "3":
 			setSNMP("v3", "set");
 
 			break;
 		}		
 	}
 
 	function setSNMP(snmp_type, snmp_t) {
 		switch(snmp_type) {
 		case "v1v2":
 			document.getElementById('row_snmp_' + snmp_t + '_username').style.display        = "none";
 			document.getElementById('row_snmp_' + snmp_t + '_password').style.display        = "none";
 			document.getElementById('row_snmp_' + snmp_t + '_community').style.display       = "";
 			document.getElementById('row_snmp_' + snmp_t + '_auth_protocol').style.display   = "none";
 			document.getElementById('row_snmp_' + snmp_t + '_priv_passphrase').style.display = "none";
 			document.getElementById('row_snmp_' + snmp_t + '_priv_protocol').style.display   = "none";
 			document.getElementById('row_snmp_' + snmp_t + '_context').style.display         = "none";
 
 
 			break;
 		case "v3":
 			document.getElementById('row_snmp_' + snmp_t + '_username').style.display        = "";
 			document.getElementById('row_snmp_' + snmp_t + '_password').style.display        = "";
 			document.getElementById('row_snmp_' + snmp_t + '_community').style.display       = "none";
 			document.getElementById('row_snmp_' + snmp_t + '_auth_protocol').style.display   = "";
 			document.getElementById('row_snmp_' + snmp_t + '_priv_passphrase').style.display = "";
 			document.getElementById('row_snmp_' + snmp_t + '_priv_protocol').style.display   = "";
 			document.getElementById('row_snmp_' + snmp_t + '_context').style.display         = "";
 
 
 			break;
 		}
 	}
 
 	window.onload = changeDimpbHostForm();
 
 	-->
 	</script>
 	<?php
 
 	form_save_button("impblinding_devices.php", "", "device_id");
 }
 
 function impblinding_get_devices(&$sql_where) {
 	/* form the 'where' clause for our main sql query */
 	$sql_where = "WHERE ((imb_devices.hostname like '%%" . $_REQUEST["filter"] . "%%' OR imb_devices.description like '%%" . $_REQUEST["filter"] . "%%')";
 
 	if ($_REQUEST["status"] == "-1") {
 		/* Show all items */
 	}elseif ($_REQUEST["status"] == "-2") {
 		$sql_where .= " AND imb_devices.disabled='on'";
 	}else {
 		$sql_where .= " AND (imb_devices.snmp_status=" . $_REQUEST["status"] . " AND imb_devices.disabled = '')";
 	}
 
 	if ($_REQUEST["type_id"] == "-1") {
 		/* Show all items */
 	}else {
 		$sql_where .= " AND imb_devices.scan_type=" . $_REQUEST["type_id"];
 	}
 
 	if ($_REQUEST["device_type_id"] == "-1") {
 		/* Show all items */
 	}else{
 		$sql_where .= " AND (imb_devices.device_type_id=" . $_REQUEST["device_type_id"] . ")";
 	}
 
 		$sql_where .= ")";
 		
 	return db_fetch_assoc("SELECT
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 		imb_devices.device_id,
 		imb_device_types.description as dev_type_description,
 		imb_devices.description,
 		imb_devices.hostname,
		imb_devices.order_id,
		imb_devices.color_row,
 		imb_devices.count_unsaved_actions,
 		imb_devices.snmp_port,
 		imb_devices.snmp_timeout,
 		imb_devices.snmp_retries,
 		imb_devices.snmp_status,
 		imb_devices.disabled,
 		imb_devices.enable_acl_mode,
 		imb_devices.enable_log_trap,
 		imb_devices.ip_mac_total,
 		imb_devices.ip_mac_blocked_total,
   	    imb_devices.ports_total,
 		imb_devices.ports_enable_total,
 		imb_devices.ports_enable_zerroip_total,
 		imb_devices.ports_active,
 		imb_devices.last_rundate,
 		imb_devices.last_runmessage,
 		imb_devices.last_runduration,
<<<<<<< HEAD
        imb_devices.snmp_get_community,
        imb_devices.snmp_get_version,
        imb_devices.snmp_get_username,
        imb_devices.snmp_get_password,
        imb_devices.snmp_set_community,
        imb_devices.snmp_set_version,
        imb_devices.snmp_set_username,
        imb_devices.snmp_set_password
 		FROM imb_device_types
 		RIGHT JOIN imb_devices ON imb_device_types.device_type_id = imb_devices.device_type_id 
		$sql_where
		$sql_order
		$sql_limit";
		
		return db_fetch_assoc($query_string);
 }
 
 function impb_device() {
 	global $device_actions, $impb_device_types, $item_rows, $config;
	
	impb_device_request_validation();

	if (get_request_var('rows') == -1) {
		$rows = read_config_option('num_rows_table');
	}elseif (get_request_var('rows') == -2) {
		$rows = 999999;
	}else{
		$rows = get_request_var('rows');
	}	

	html_start_box('IMPB Device Filters', '100%', true, '3', 'center', 'impb_devices.php?action=edit&status=' . get_request_var('status'));
	impb_device_filter();
	html_end_box();

	$sql_where = '';

	$devices = impb_get_devices($sql_where, $rows);

	
=======
         imb_devices.snmp_get_community,
         imb_devices.snmp_get_version,
         imb_devices.snmp_get_username,
         imb_devices.snmp_get_password,
         imb_devices.snmp_set_community,
         imb_devices.snmp_set_version,
         imb_devices.snmp_set_username,
         imb_devices.snmp_set_password
 		FROM imb_device_types
 		RIGHT JOIN imb_devices ON imb_device_types.device_type_id = imb_devices.device_type_id
 		$sql_where
 		ORDER BY " . $_REQUEST["sort_column"] . " " . $_REQUEST["sort_direction"] . "
 		LIMIT " . (read_config_option("dimpb_num_rows")*($_REQUEST["page"]-1)) . "," . read_config_option("dimpb_num_rows"));
 }
 
 function impblinding_device() {
 	global $colors, $device_actions, $impblinding_device_types, $config;
 
 	/* ================= input validation ================= */
 	input_validate_input_number(get_request_var_request("type_id"));
 	input_validate_input_number(get_request_var_request("device_type_id"));
 	input_validate_input_number(get_request_var_request("page"));
 	input_validate_input_number(get_request_var_request("status"));
 	/* ==================================================== */
 
 	/* clean up search string */
 	if (isset($_REQUEST["filter"])) {
 		$_REQUEST["filter"] = sanitize_search_string(get_request_var("filter"));
 	}
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
 		kill_session_var("sess_impb_device_type_id");
 		kill_session_var("sess_impb_device_device_type_id");
 		kill_session_var("sess_impb_device_status");
 		kill_session_var("sess_impb_device_sort_column");
         kill_session_var("sess_impb_device_sort_direction");		
 
 		unset($_REQUEST["page"]);
 		unset($_REQUEST["filter"]);
 		unset($_REQUEST["type_id"]);
 		unset($_REQUEST["device_type_id"]);
 		unset($_REQUEST["status"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);			
 	}
 
 	/* remember these search fields in session vars so we don't have to keep passing them around */
 	load_current_session_value("page", "sess_impb_device_current_page", "1");
 	load_current_session_value("filter", "sess_impb_device_filter", "");
 	load_current_session_value("type_id", "sess_impb_device_type_id", "-1");
 	load_current_session_value("device_type_id", "sess_impb_device_device_type_id", "-1");
 	load_current_session_value("status", "sess_impb_device_status", "-1");
    load_current_session_value("sort_column", "sess_impb_device_sort_column", "order_id");
    load_current_session_value("sort_direction", "sess_impb_device_sort_direction", "DESC");	
 	
 	html_start_box("<strong>D-Link IP-MAC-Port Blinding Device Filters</strong>", "98%", $colors["header"], "3", "center", "impblinding_devices.php?action=edit&status=" . $_REQUEST["status"]);
 
 	include("plugins/impblinding/html/inc_impblinding_device_filter_table.php");
 
 	html_end_box();
 
 	$sql_where = "";
 
 	$devices = impblinding_get_devices($sql_where);
 
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 	$total_rows = db_fetch_cell("SELECT
 		COUNT(imb_devices.device_id)
 		FROM imb_devices
 		$sql_where");
 
<<<<<<< HEAD
 	$nav = html_nav_bar('impb_devices.php?filter=' . get_request_var('filter'), MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 17, __('Devices'), 'page', 'main');

	form_start('impb_devices.php', 'chk');	

	print $nav;

	html_start_box('', '100%', '', '3', 'center', '');

	$display_text = array(
		'description'      => array(__('Host<br>Description'), 'ASC'),
		'order_id'        => array(__('orf'), 'ASC'),
		'disabled'      => array(__('Status'), 'ASC'),
		'enable_acl_mode'         => array(__('ARP<br>ACL'), 'ASC'),
		'enable_log_trap'      => array(__('Log<br>Trap'), 'ASC'),
		'hostname'        => array(__('Hostname'), 'DESC'),
		'dev_type_description'      => array(__('Device<br>Type'), 'DESC'),
		'count_unsaved_actions'     => array(__('Unsave<br>count'), 'DESC'),
		'ip_mac_total'      => array(__('Total<br>Ip-Macs'), 'DESC'),
		'ip_mac_blocked_total'      => array(__('Blocked<br>Ip-Macs'), 'DESC'),
		'ports_total' => array(__('User<br>Ports'), 'DESC'),
		'ports_enable_total' => array(__('Ip-Mac-Ports<br>Enable'), 'DESC'),
		'ports_enable_zerroip_total' => array(__('Zerro IP<br>Ports'), 'DESC'),
		'last_rundate' => array(__('Last<br>Run date'), 'DESC'),
		'last_runduration' => array(__('Last<br>Duration'), 'DESC'),
		" " => array(__(' '), ''));

 
	html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false);

	if (sizeof($devices)) {
		foreach ($devices as $device) {
			form_alternate_row('line' . $device['device_id'], true);
			impb_format_device_row($device);
		}
	}else{
		print '<tr><td colspan="10"><em>' . __('No IMPB Devices') . '</em></td></tr>';
	}    

	html_end_box(false);

	if (sizeof($devices)) {
		print $nav;
	}

	/* draw the dropdown containing a list of available actions for this form */
	draw_actions_dropdown($device_actions);

	form_end();



}

function impb_format_device_row($device, $actions=false) {
	global $config, $impb_device_types;

	
	
	
	
	/* viewer level */
	if ($actions) {
		$row = "<a href='" . htmlspecialchars($config['url_path'] . 'plugins/impb/impb_devices.php?device_id=' . $device['device_id'] . '&issues=0&page=1') . "'><img src='" . $config['url_path'] . "plugins/mactrack/images/view_interfaces.gif' alt='' title='" . __('View Interfaces') . "' align='middle' border='0'></a>";

		/* admin level */
		if (api_user_realm_auth('mactrack_sites.php')) {
			if ($device['disabled'] == '') {
				$row .= "<img id='r_" . $device['device_id'] . "' src='" . $config['url_path'] . "plugins/impb/images/rescan_device.gif' alt='' onClick='scan_device(" . $device['device_id'] . ")' title='" . __('Rescan Device') . "' align='middle' border='0'>";
			}else{
				$row .= "<img src='" . $config['url_path'] . "plugins/impb/images/view_none.gif' alt='' align='middle' border='0'>";
			}
		}

		print "<td style='width:40px;'>" . $row . "</td>";
	}

	
	$bgc = db_fetch_cell("SELECT hex FROM colors WHERE id='" . $device["color_row"] . "'");
	form_selectable_cell("<a class='linkEditMain' href='impb_devices.php?action=edit&device_id=" . $device["device_id"] . "'>" .
	(strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'> \${1}</span>", $device["description"]) : $device["description"]) . "</a>", $device["device_id"],250,"background-color: #" . $bgc . ";");
	form_selectable_cell($device["order_id"], $device["order_id"]);
	form_selectable_cell(get_colored_device_status(($device["disabled"] == "on" ? true : false), $device["snmp_status"]), $device["device_id"] );
	form_selectable_cell(get_colored_status($device["enable_acl_mode"]), $device["device_id"] );
	form_selectable_cell(get_colored_status($device["enable_log_trap"]), $device["device_id"] );				
	form_selectable_cell((strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $device["hostname"]) : $device["hostname"]), $device["device_id"]);				
	form_selectable_cell($device["dev_type_description"], $device["device_id"] );				
	form_selectable_cell($device["count_unsaved_actions"], $device["device_id"] );
	form_selectable_cell("<a class='linkEditMain' href='impb_view_bindings.php?device_id=+" . $device["device_id"] . "&ip_filter_type_id=1&ip_filter=&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=bindings&x=22&y=4'>" . $device["ip_mac_total"] . "</a>", $device["device_id"]);
	form_selectable_cell("<a class='linkEditMain' href='impb_view.php?report=blmacs&device_id=+" . $device["device_id"] . "&ip_filter_type_id=1&ip_filter=&port_filter=&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=blmacs&x=15&y=8'>" . $device["ip_mac_blocked_total"] . "</a>", $device["device_id"]);
	form_selectable_cell("<a class='linkEditMain' href='impb_view_ports.php?device_type_id=-1&device_id=+" . $device["device_id"] . "&status=-1&filter=&page=1&x=11&y=7'>" . $device["ports_total"] . "</a>", $device["device_id"]);
	form_selectable_cell("<a class='linkEditMain' href='impb_view_ports.php?device_type_id=-1&device_id=+" . $device["device_id"] . "&status=2&filter=&page=1&x=11&y=7'>" . $device["ports_enable_total"] . "</a>", $device["device_id"]);							
	form_selectable_cell("<a class='linkEditMain' title='title-title
	second' href='impb_view.php?report=ports&p_device_type_id=-1&p_device_id=+" . $device["device_id"] . "&p_status=2&p_zerro_status=1&p_filter=&p_page=1&report=ports&x=11&y=7'>" . $device["ports_enable_zerroip_total"] . "</a>", $device["device_id"]);
	form_selectable_cell(imb_fromat_datetime($device["last_rundate"]), $device["device_id"] );
	form_selectable_cell(number_format($device["last_runduration"]), $device["device_id"] );				
	form_selectable_cell("<a class='linkEditMain' href='impb_devices.php?action=actions&drp_action=5&id=1&selected_items=" . serialize(array(1=>$device["device_id"])) . "'><img src='../../images/reload_icon_small.gif' alt='Reload Data Query' border='0' align='absmiddle'></a>", $device["device_id"]);
	form_checkbox_cell($device["description"], $device["device_id"]);	
	

}


function impb_device_filter() {
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
						<?php print __('Site');?>
					</td>
					<td>
						<select id='site_id' onChange='applyFilter()'>
							<option value='-1'<?php if (get_request_var('site_id') == '-1') {?> selected<?php }?>><?php print __('All');?></option>
							<option value='-2'<?php if (get_request_var('site_id') == '-2') {?> selected<?php }?>><?php print __('None');?></option>
							<?php
							$sites = db_fetch_assoc('SELECT site_id, site_name FROM mac_track_sites ORDER BY site_name');
							if (sizeof($sites)) {
							foreach ($sites as $site) {
								print '<option value="'. $site['site_id'] . '"';if (get_request_var('site_id') == $site['site_id']) { print ' selected'; } print '>' . $site['site_name'] . '</option>';
							}
							}
							?>
						</select>
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
			<table class='filterTable'>
				<tr>
					<td>
						<?php print __('DevicesTypes');?>
					</td>
					<td>
						 <select id='device_type_id' onChange='applyFilter()'>
						<option value='-1'<?php if (get_request_var('device_type_id') == '-1') {?> selected<?php }?>><?php print __('Any');?></option>
						 <?php
							if (get_request_var('device_type_id') != -1) {
								$device_types = db_fetch_assoc_prepared('SELECT device_type_id, description FROM imb_device_types i ' .
									' WHERE device_type_id = ? ', array(get_request_var('device_type_id')));
							}else{
								$device_types = db_fetch_assoc('SELECT device_type_id, description FROM imb_device_types i;');
							}
						 if (sizeof($device_types) > 0) {
							 foreach ($device_types as $device_type) {
								 if ($device_type["device_type_id"] == 0) {
									 $display_text = "Unknown Device Type";
								 }else{
									 $display_text = $device_type["description"];
								 }
								print '<option value="' . $device_type['device_type_id'] . '"'; if (get_request_var('device_type_id') == $device_type['device_type_id']) { print ' selected'; } print '>' . $display_text . '</option>';
							 }
						 }
						 ?>
						 </select>					
					</td>
					<td>
						<?php print __('Status');?>
					</td>
					<td>
						<select id='status' onChange='applyFilter()'>
							<option value='-1'<?php if (get_request_var('status') == '-1') {?> selected<?php }?>><?php print __('Any');?></option>
							<option value='3'<?php if (get_request_var('status') == '3') {?> selected<?php }?>><?php print __('Up');?></option>
							<option value='-2'<?php if (get_request_var('status') == '-2') {?> selected<?php }?>><?php print __('Disabled');?></option>
							<option value='1'<?php if (get_request_var('status') == '1') {?> selected<?php }?>><?php print __('Down');?></option>
							<option value='0'<?php if (get_request_var('status') == '0') {?> selected<?php }?>><?php print __('Unknown');?></option>
							<option value='4'<?php if (get_request_var('status') == '4') {?> selected<?php }?>><?php print __('Error');?></option>
							<option value='5'<?php if (get_request_var('status') == '5') {?> selected<?php }?>><?php print __('No Cacti Link');?></option>
						</select>
					</td>					
				</tr>
			</table>
		</form>
		<script type='text/javascript'>
		function applyFilter() {
			strURL  = urlPath+'plugins/impb/impb_devices.php?header=false';
			strURL += '&site_id=' + $('#site_id').val();
			strURL += '&status=' + $('#status').val();
			strURL += '&type_id=' + $('#type_id').val();
			strURL += '&device_type_id=' + $('#device_type_id').val();
			strURL += '&filter=' + $('#filter').val();
			strURL += '&rows=' + $('#rows').val();
			loadPageNoHeader(strURL);
		}

		function clearFilter() {
			strURL  = urlPath+'plugins/impb/impb_devices.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}

		function exportRows() {
			strURL  = urlPath+'plugins/impb/impb_devices.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_devices.php?import=true';
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
 	html_start_box("", "98%", $colors["header"], "3", "center", "");
 
 	/* generate page list */
 	$url_page_select = get_page_list($_REQUEST["page"], MAX_DISPLAY_PAGES, read_config_option("dimpb_num_rows"), $total_rows, "impblinding_devices.php?filter=" . $_REQUEST["filter"] .  "&status=" . $_REQUEST["status"]);
 
 	$nav = "<tr bgcolor='#" . $colors["header"] . "'>
 			<td colspan='17'>
 				<table width='100%' cellspacing='0' cellpadding='0' border='0'>
 					<tr>
 						<td align='left' class='textHeaderDark'>
 							<strong>&lt;&lt; "; if ($_REQUEST["page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_devices.php?filter=" . $_REQUEST["filter"] .  "&status=" . $_REQUEST["status"] . "&page=" . ($_REQUEST["page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
 						</td>\n
 						<td align='center' class='textHeaderDark'>
 							Showing Rows " . ((read_config_option("dimpb_num_rows")*($_REQUEST["page"]-1))+1) . " to " . ((($total_rows < read_config_option("dimpb_num_rows")) || ($total_rows < (read_config_option("dimpb_num_rows")*$_REQUEST["page"]))) ? $total_rows : (read_config_option("dimpb_num_rows")*$_REQUEST["page"])) . " of $total_rows [$url_page_select]
 						</td>\n
 						<td align='right' class='textHeaderDark'>
 							<strong>"; if (($_REQUEST["page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_devices.php?filter=" . $_REQUEST["filter"] .  "&status=" . $_REQUEST["status"] . "&page=" . ($_REQUEST["page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
 						</td>\n
 					</tr>
 				</table>
 			</td>
 		</tr>\n";
 
 	print $nav;
 	$display_text = array(
 		"description" => array("Host<br>Description", "ASC"),
		"order_id" => array("ord", "ASC"),
 		"disabled" => array("<br>Status", "ASC"),
 		"enable_acl_mode" => array("ARP<br>ACL", "ASC"),
 		"enable_log_trap" => array("Log<br>Trap", "ASC"),
 		"hostname" => array("<br>Hostname", "ASC"),
 		"dev_type_description" => array("Device<br>Type", "ASC"),
 		"count_unsaved_actions" => array("Unsave<br>count", "ASC"),
 		"ip_mac_total" => array("Total<br>Ip-Mac's", "ASC"),
 		"ip_mac_blocked_total" => array("Blocked<br>Ip-Mac's", "ASC"),
 		"ports_total" => array("User<br>Ports", "ASC"),
 		"ports_enable_total" => array("Ip-Mac-Ports<br>Enable", "ASC"),
 		"ports_enable_zerroip_total" => array("Zerro IP<br>Ports", "ASC"),
 		"last_rundate" => array("Last<br>Run date", "ASC"),
 		"last_runduration" => array("Last<br>Duration", "ASC"),
		" " => array(" ","DESC"));
 
 
     html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 	
 	
 	//html_header_checkbox(array( "Last<br>Run date","Last<br>Duration"));
 
 	$i = 0;
 	if (sizeof($devices) > 0) {
 		foreach ($devices as $device) {
		$bgc = db_fetch_cell("SELECT hex FROM colors WHERE id='" . $device["color_row"] . "'");
 			form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $device["device_id"]); $i++;
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_devices.php?action=edit&device_id=" . $device["device_id"] . "'>" .
 					(strlen($_REQUEST["filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["filter"]) . ")/i", "<span style='background-color: #F8D93D;'> \${1}</span>", $device["description"]) : $device["description"]) . "</a>", $device["device_id"],250,"background-color: #" . $bgc . ";");
 				form_selectable_cell($device["order_id"], $device["order_id"]);
				form_selectable_cell(get_colored_device_status(($device["disabled"] == "on" ? true : false), $device["snmp_status"]), $device["device_id"] );
 				form_selectable_cell(get_colored_status($device["enable_acl_mode"]), $device["device_id"] );
 				form_selectable_cell(get_colored_status($device["enable_log_trap"]), $device["device_id"] );				
 				form_selectable_cell((strlen($_REQUEST["filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $device["hostname"]) : $device["hostname"]), $device["device_id"]);				
 				form_selectable_cell($device["dev_type_description"], $device["device_id"] );				
 				form_selectable_cell($device["count_unsaved_actions"], $device["device_id"] );
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=macs&m_device_id=+" . $device["device_id"] . "&m_ip_filter_type_id=1&m_ip_filter=&m_mac_filter_type_id=1&m_mac_filter=&i_port_filter_type_id=&i_port_filter=&m_rows_selector=-1&m_filter=&m_page=1&report=macs&x=22&y=4'>" . $device["ip_mac_total"] . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=blmacs&b_device_id=+" . $device["device_id"] . "&b_ip_filter_type_id=1&b_ip_filter=&b_port_filter=&b_mac_filter_type_id=1&b_mac_filter=&i_port_filter_type_id=&i_port_filter=&b_rows_selector=-1&b_filter=&b_page=1&report=blmacs&x=15&y=8'>" . $device["ip_mac_blocked_total"] . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=ports&p_device_type_id=-1&p_device_id=+" . $device["device_id"] . "&p_status=-1&p_zerro_status=-1&p_filter=&p_page=1&report=ports&x=11&y=7'>" . $device["ports_total"] . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=ports&p_device_type_id=-1&p_device_id=+" . $device["device_id"] . "&p_status=2&p_zerro_status=-1&p_filter=&p_page=1&report=ports&x=11&y=7'>" . $device["ports_enable_total"] . "</a>", $device["device_id"]);							
 				form_selectable_cell("<a class='linkEditMain' title='title-title
 				second' href='impblinding_view.php?report=ports&p_device_type_id=-1&p_device_id=+" . $device["device_id"] . "&p_status=2&p_zerro_status=1&p_filter=&p_page=1&report=ports&x=11&y=7'>" . $device["ports_enable_zerroip_total"] . "</a>", $device["device_id"]);
 				form_selectable_cell(imb_fromat_datetime($device["last_rundate"]), $device["device_id"] );
 				form_selectable_cell(number_format($device["last_runduration"]), $device["device_id"] );				
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?action=device_query&id=1&host_id=" . $device["device_id"] . "'><img src='../../images/reload_icon_small.gif' alt='Reload Data Query' border='0' align='absmiddle'></a>", $device["device_id"]);
 				form_checkbox_cell($device["description"], $device["device_id"]);				
 
 		}
 
 		
 /* 			form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 				?>
 				<td >
 					<a class="linkEditMain" href="impblinding_devices.php?action=edit&device_id=<?php print $device["device_id"];?>"><?php print eregi_replace("(" . preg_quote($_REQUEST["filter"]) . ")", "<span style='background-color: #F8D93D;'>\\1</span>", $device["description"]);?></a>
 				</td>
 				<td><?php print get_colored_device_status(($device["disabled"] == "on" ? true : false), $device["snmp_status"]);?></td>
 				<td><?php print get_colored_status($device["enable_acl_mode"]);?></td>
 				<td><?php print get_colored_status($device["enable_log_trap"]);?></td>
 				<td><?php print eregi_replace("(" . preg_quote($_REQUEST["filter"]) . ")", "<span style='background-color: #F8D93D;'>\\1</span>", $device["hostname"]);?></td>
 				<td><?php print $device["dev_type_description"];?></td>
 				<td><?php print $device["count_unsaved_actions"];?></td>
 				<td><?php print $device["ip_mac_total"];?></td>
 				<td><?php print $device["ip_mac_blocked_total"];?></td>
 				<td><?php print $device["ports_total"];?></td>
 				<td><?php print $device["ports_enable_total"];?></td> 
 				<td><?php print $device["ports_enable_zerroip_total"];?></td>
                 <td><?php print $device["last_rundate"];?></td>      				
 				<td><?php print number_format($device["last_runduration"], 1);?></td>
 				<td style="<?php print get_checkbox_style();?>" width="1%" align="right">
 					<input type='checkbox' style='margin: 0px;' name='chk_<?php print $device["device_id"];?>' title="<?php print $device["description"];?>">
 				</td>
 			</tr>
 			<?php
 		}	 */	
 		
 		
 		/* put the nav bar on the bottom as well */
 		print $nav;
 	}else{
 		print "<tr><td><em>No D-Link IP-MAC-Port Blinding Devices</em></td></tr>";
 	}
 	html_end_box(false);
 
 	/* draw the dropdown containing a list of available actions for this form */
 	draw_actions_dropdown($device_actions);
 }
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 ?>
