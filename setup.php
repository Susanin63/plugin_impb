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
 
 /*******************************************************************************
 
     Author ......... Jean-Michel Pepin plugin for Cacti (PEPJ in forum.cacti.net)
     Program ........ IP subnet Calculator
     Version ........ 0.0.01b
     Purpose ........ IMP Blinding
 
 *******************************************************************************/

 

 
function plugin_impb_install() {
	api_plugin_register_hook('impb', 'top_header_tabs',       'impb_show_tab',             'setup.php');
	api_plugin_register_hook('impb', 'top_graph_header_tabs', 'impb_show_tab',             'setup.php');
	api_plugin_register_hook('impb', 'config_arrays',         'impb_config_arrays',        'setup.php');
	api_plugin_register_hook('impb', 'draw_navigation_text',  'impb_draw_navigation_text', 'setup.php');
	api_plugin_register_hook('impb', 'config_form',           'impb_config_form',          'setup.php');
	api_plugin_register_hook('impb', 'config_settings',       'impb_config_settings',      'setup.php');
	api_plugin_register_hook('impb', 'poller_bottom',         'impb_poller_bottom',        'setup.php');
	api_plugin_register_hook('impb', 'page_head',             'impb_page_head',            'setup.php');
	api_plugin_register_hook('impb', 'mac_track_finish_scan', 'impb_recent_data',          'lib/impb_functions.php');

	# device hook: intercept on device save
	//api_plugin_register_hook('impb', 'api_device_save', 'sync_cacti_to_impb', 'impb_actions.php');
	# device hook: Add a new dropdown Action for Device Management
	//api_plugin_register_hook('impb', 'device_action_array', 'impb_device_action_array', 'impb_actions.php');
	# device hook: Device Management Action dropdown selected: prepare the list of devices for a confirmation request
	//api_plugin_register_hook('impb', 'device_action_prepare', 'impb_device_action_prepare', 'impb_actions.php');
	# device hook: Device Management Action dropdown selected: execute list of device
	//api_plugin_register_hook('impb', 'device_action_execute', 'impb_device_action_execute', 'impb_actions.php');

	# Register our realms
	api_plugin_register_realm('impb', 'impb_view.php,impb_view_ports.php,impb_view_devices.php,impb_view_bindings.php,impb_view_blmacs.php,impb_view_netdel.php,impb_view_netadd.php,impb_view_recentmacs.php,impb_view_info.php,impb_ajax.php,graph_ion_view.php', 'IMPB Viewer', 1);
	api_plugin_register_realm('impb', 'impblinding.php,impb_devices.php,impb_logs.php,impb_device_types.php,impb_utilities.php', 'IMPb Administrator', 1);

	impb_setup_table ();
}
 
 function plugin_impb_uninstall () {
	// db_execute('DROP TABLE IF EXISTS `imb_auto_updated_nets`');
	// db_execute('DROP TABLE IF EXISTS `imb_banip`');
	// db_execute('DROP TABLE IF EXISTS `imb_blmacs`');
	// db_execute('DROP TABLE IF EXISTS `imb_cli`');
	// db_execute('DROP TABLE IF EXISTS `imb_device_types`');
	// db_execute('DROP TABLE IF EXISTS `imb_devices`');
	// db_execute('DROP TABLE IF EXISTS `imb_log`');
	// db_execute('DROP TABLE IF EXISTS `imb_macip`');
	// db_execute('DROP TABLE IF EXISTS `imb_mactrack_recent_ports`');
	// db_execute('DROP TABLE IF EXISTS `imb_mactrack_temp_ports`');
	// db_execute('DROP TABLE IF EXISTS `imb_ports`');
	// db_execute('DROP TABLE IF EXISTS `imb_processes`');
	// db_execute('DROP TABLE IF EXISTS `imb_scanning_functions`');
	// db_execute('DROP TABLE IF EXISTS `imb_tab_dev`');
	// db_execute('DROP TABLE IF EXISTS `imb_tabs`');
	// db_execute('DROP TABLE IF EXISTS `imb_temp_blmacinfo`');
	// db_execute('DROP TABLE IF EXISTS `imb_temp_blmacs`');
	// db_execute('DROP TABLE IF EXISTS `imb_temp_macip`');
	// db_execute('DROP TABLE IF EXISTS `imb_temp_portname`');
	// db_execute('DROP TABLE IF EXISTS `imb_temp_ports`');
	// db_execute('DROP TABLE IF EXISTS `imb_temp_ports_stat`');
	// db_execute('DROP TABLE IF EXISTS `imb_traps_blocked`');
	// db_execute('DELETE FROM `settings` where name = "impb_version";');

}


function plugin_impb_version () {
	global $config;
	$info = parse_ini_file($config['base_path'] . '/plugins/impb/INFO', true);
	return $info['info'];
}

function plugin_impb_check_config () {
	/* Here we will check to ensure everything is configured */
	impb_check_upgrade();
	return true;
}

function plugin_impb_upgrade () {
	/* Here we will upgrade to the newest version */
	impb_check_upgrade();
	return false;
}


 
function impb_show_tab () {
	global $config, $user_auth_realm_filenames;

	if (api_user_realm_auth('impb_view_devices.php')) {
		if (substr_count($_SERVER['REQUEST_URI'], 'impb_view')) {
			print '<a href="' . $config['url_path'] . 'plugins/impb/impb_view_devices.php"><img src="' . $config['url_path'] . 'plugins/impb/images/tab_dimpb_red.png" alt="' . __('IMPB') . '"></a>';
		}else{
			print '<a href="' . $config['url_path'] . 'plugins/impb/impb_view_devices.php"><img src="' . $config['url_path'] . 'plugins/impb/images/tab_dimpb.png" alt="' . __('IMPB') . '"></a>';
		}
	}
}


 
 
 function impb_config_arrays () {
 	global $user_auth_realms, $menu,$user_auth_realm_filenames, $impb_snmp_versions;
 	global $impb_search_types, $impb_operation_macip_types, $impb_port_search_types, $impb_search_recent_date;
 	global $imp_timespans, $impb_type_port_num_conversion, $impb_imp_mode, $impb_value_save_cfg;
 	global $impb_imp_mode_type, $impb_imp_action_type, $impb_imb_create_macip_type, $impb_imb_banip_type, $impb_imp_zerrostate_mode_type, $impb_imb_yes_no, $impb_func_version;
 	global $imp_port_state_2str_t2, $imp_port_state_2str_t3, $imp_port_state_color, $impb_revision, $impb_imp_MacBindingPortState, $impb_imp_net_ttl;
	global $impb_port_state_2html, $impb_port_zerro_state_2html;

 
   $impb_snmp_versions = array(1 =>
 	"Version 1",
 	"Version 2",
 	"Version 3");
 	
 
 $snmp_priv_protocols = array(
 	"[None]" => "[None]",
 	"DES" => "DES (default)",
 	"AES128" => "AES");  
   	
	
 	$menu2 = array ();
 	foreach ($menu as $temp => $temp2 ) {
 		$menu2[$temp] = $temp2;
 		if ($temp == __('Management')) {
 			$menu2[__('IMPB Binding')]["plugins/impb/impb_devices.php"] = __('Devices');
 			$menu2[__('IMPB Binding')]["plugins/impb/impb_device_types.php"] = __('Device Types');
 			$menu2[__('IMPB Binding')]["plugins/impb/impb_logs.php"] = __('IMPB Logs');
 			$menu2[__('IMPB Binding')]["plugins/impb/impb_utilities.php"] = __('IMPB Utilities');
 		}
 	}
 	$menu = $menu2;
     
     $impb_search_types = array(
     1 => "",
     2 => "Matches",
     3 => "Contains",
     4 => "Begins With",
     5 => "Does Not Contain",
     6 => "Does Not Begin With",
     7 => "Is Null",
     8 => "Is Not Null");	  
 
 	    
     $impb_port_search_types = array(
     1 => "",
     2 => "Состоит",
     3 => "НЕ состоит");
 	
 	$impb_value_save_cfg = array (
     2 => "DES-3028(2) config only",
     3 => "default (3) config only",
	 1 => "DES-1210-28ME (1) config_1"
 	);
				$imp_port_state_2str_t2 = array (
				 0 => "null(0)",
				 1 => "other(1)",
				 2 => "en(2)",
				 3 => "dis(3)",
				 4 => "ERROR (4)"
				);
				
				
				$imp_port_state_2str_t3 = array (
				0 => "null(0)",
				1 => "other(1)",
				2 => "en-strict(2)",
				3 => "dis(3)",
				4 => "en-loose(4)",
				5 => "ERROR (5)"
				);

 	$impb_port_state_2html = array (
	1 => array (
		 0 => "<strong> <span style='color: #EA8F00;'>Non</span></strong>",
		 1 => "<strong> <span style='color: #EA8F00;'>Ot</span></strong>",
		 2 => "<strong> <span style='color: #00BD27;'>En</span></strong>",
		 3 => "<strong> <span style='color: #FF0000;'>Dis</span></strong>",
		 4 => "<strong> <span style='color: #00BD27;'>Er</span></strong>"		
		),
	2 => array (
		 0 => "<strong> <span style='color: #EA8F00;'>Non</span></strong>",
		 1 => "<strong> <span style='color: #EA8F00;'>Ot</span></strong>",
		 2 => "<strong> <span style='color: #00BD27;'>Str</span></strong>",
		 3 => "<strong> <span style='color: #FF0000;'>Dis</span></strong>",
		 4 => "<strong> <span style='color: #EA8F00;'>Los</span></strong>",
		 5 => "<strong> <span style='color: #FF0000;'>Er</span></strong>"
	),
	3 => array ( //swIpMacBindingPortARPInspection
		 0 => "<strong> <span style='color: #FF0000;'>ARP</span></strong>", //other
		 1 => "<strong> <span style='color: #FF0000;'>ARP</span></strong>", //dis
		 2 => "<strong> <span style='color: #00BD27;'>ARP</span></strong>", //strict
		 3 => "<strong> <span style='color: #EA8F00;'>ARP</span></strong>" //loose
	),
	4 => array ( //swIpMacBindingPortIPInspection
		 0 => "<strong> <span style='color: #FF0000;'>IP</span></strong>", //other
		 1 => "<strong> <span style='color: #00BD27;'>IP</span></strong>", //en
		 2 => "<strong> <span style='color: #FF0000;'>IP</span></strong>" //dis
	),
	5 => array ( //swIpMacBindingPortARPInspection DES-1210-28
		 0 => "<strong> <span style='color: #FF0000;'>ARP</span></strong>", //dis
		 1 => "<strong> <span style='color: #00BD27;'>ARP</span></strong>", //strict
		 2 => "<strong> <span style='color: #EA8F00;'>ARP</span></strong>" //loose
	),
	6 => array (
		 1 => "<strong> <span style='color: #00BD27;'>En</span></strong>",
		 0 => "<strong> <span style='color: #FF0000;'>Dis</span></strong>",
		 2 => "<strong> <span style='color: #00BD27;'>En</span></strong>", //3010G
		 3 => "<strong> <span style='color: #FF0000;'>Dis</span></strong>"  //3010G
	),
	7 => array ( //swIpMacBindingPortIPInspection
		 1 => "<strong> <span style='color: #00BD27;'>IP</span></strong>", //en
		 0 => "<strong> <span style='color: #FF0000;'>IP</span></strong>" //dis
	),
	71 => array ( //swIpMacBindingPortIPInspection 12-10-28 ME
		 1 => "<strong> <span style='color: #00BD27;'>EN</span></strong>", //en
		 0 => "<strong> <span style='color: #FF0000;'>Dis</span></strong>" //dis
	),		
	"arp_str2int" => array ( //swIpMacBindingPortARPInspection
		 "disable" => "1",
		 "strict" => "2", 
		 "loose" => "3" 
	),
	"arp_str2int_1210" => array ( //swIpMacBindingPortARPInspection
		 "disable" => "0",
		 "strict" => "1", 
		 "loose" => "2" 
	),	
	"arp_int2str" => array ( //swIpMacBindingPortARPInspection
		 "1" => "disable", 
		 "2" => "strict", 
		 "3" => "loose" 
	),
	"arp_int2str_1210" => array ( //swIpMacBindingPortARPInspection
		 "0" => "disable", 
		 "1" => "strict", 
		 "2" => "loose" 
	),	
	"ip_str2int" => array ( //swIpMacBindingPortIPInspection
		 "enable" => "1",
		 "disable" => "2"
	),
	"ip_int2str" => array ( //swIpMacBindingPortIPInspection
		 "1" => "enable", 
		 "2" => "disable" 
	)	
 	);	

 	$impb_port_zerro_state_2html = array (
	1 => array (
		 0 => "<strong> <span style='color: #EA8F00;'>NonZ</span></strong>",
		 1 => "<strong> <span style='color: #00BD27;'>Zer</span></strong>",
		 2 => "<strong> <span style='color: #FF0000;'>Zer</span></strong>"
		),
	2 => array (
		 0 => "<strong> <span style='color: #EA8F00;'>NonZ</span></strong>",
		 2 => "<strong> <span style='color: #00BD27;'>Zer</span></strong>",
		 3 => "<strong> <span style='color: #FF0000;'>Zer</span></strong>"
	),
	3 => array (
		 2 => "<strong> <span style='color: #EA8F00;'>NonZ</span></strong>",
		 1 => "<strong> <span style='color: #00BD27;'>Zer</span></strong>",
		 0 => "<strong> <span style='color: #FF0000;'>Zer</span></strong>"
	)		
 	);	

	
 	$imp_port_state_color = array (
    0 => "EA8F00",
	1 => "EA8F00",
 	2 => "00BD27",
    3 => "FF0000",
 	4 => "00BD27"
 	);
 	$impb_imp_MacBindingPortState = array (
    1 => "Port - Enable(4), Disable(3)",
 	2 => "Port - En-Strict, En-Loose, Dis",	
	3 => "Port - Other(1), En-Strict(2), Dis(3), En-Loose(4)",
	4 => "Port and (ARP/IP) - Enable (Strict, Loose)",
	5 => "ARP/IP - Strict, Loose",
	6 => "Port - Enable(2), Disable(3)",
	71 => "Port - Enable(1), Disable(0)",
 	);
 	
	
 	$impb_revision = array (
    1 => "A/B",
 	2 => "C",	
 	);	
    	$impb_operation_macip_types = array(
 		1 => "Изменение с копированием",
 		2 => "Изменение с перемещением");
     
 	$impb_search_recent_date = array(
 	1 => "All",
 	2 => "Current only",
 	3 => "Last 10 minute",
 	4 => "Last 30 minute",
 	5 =>"Last Hour",
 	6 =>"Last Day",
 	7 =>"Last Week",
 	8 =>"Last Month"
 	);
 	
 	$imp_timespans = array(
 	1 => "Last Half Hour",
 	2 => "Last Hour",
 	3 => "Last 2 Hours",
 	4 =>"Last Day",
 	5 =>"Last Week",
 	6 =>"Last Month",
 	7 =>"Last Year"
 	);
 	
 	$impb_type_port_num_conversion = array(
 	1 => "from start (2=[40000000]; 20-23=[00001E00])",
	2 => "from end   (2=[00000002]; 20-23=[00780000])",
 	3 => "as is"
 	);
 	
 	$impb_imp_mode = array(
 	1 => "ARP",
 	2 => "ACL"	
 	);
 
 	$impb_imp_mode_type = array(
 	1 => "1=>arp, 2=>acl",
 	2 => "0=>arp, 1=>acl"
 	);
 
 	$impb_imp_action_type = array(
 	1 => "1=>inactive, 2=>active",
 	2 => "0=>inactive, 1=>active"
 	);	
 
 	$impb_imb_create_macip_type = array(
 	1 => "Status, Mac, Port, Mode",
 	2 => "Mac, Status, Port, Mode",
	3 => "[Mac, Status], Port, Mode",
	4 => "[4.IP.MAC] Status, Port, Status"
 	);	
 	$impb_imp_zerrostate_mode_type = array(
 	1 => "1=>Enable, 2=>Disable",
 	2 => "2=>Enable, 3=>Disable",
	3 => "1=>Enable, 0=>Disable"	
 	);
 $impb_imb_banip_type = array(
 	0 => "",
 	1 => "Balance",
 	2 => "Adm. block",
 	3 => "NO_Billing",
	4 => "Other"
 	);
 $impb_imb_yes_no = array(
 	0 => "NO",
 	1 => "YES"
 	);
 $impb_func_version = array(
 	33 => "<= 3.3",
 	35 => "3.6 >= X >= 3.3",
	39 => ">= 3.9"
 );
 
 $refresh_interval = array(
 		5 => "5 Seconds",
 		10 => "10 Seconds",
 		20 => "20 Seconds",
 		30 => "30 Seconds",
 		60 => "1 Minute",
 		300 => "5 Minutes");	
 
 $impb_imp_net_ttl = array(
 		1 => "1 Час",
 		3 => "3 Часа",
 		12 => "12 Часов",
 		24 => "1 Сутки",
 		36 => "3 Суток",
		168 => "1 Неделя",
		744 => "1 Месяц",
 		0 => "Постоянно");	

 };
 
 function impb_config_settings () {
 	global $tabs, $settings, $impb_snmp_versions;
 	global $snmp_auth_protocols, $snmp_priv_protocols;
 
 	$tabs["impblinding"] = "Dimpb";
 
 	$settings["impblinding"] = array(
 		"impb_hdr_timing" => array(
 			"friendly_name" => "D-Link IP-Mac-Port Blinding General Settings",
 			"method" => "spacer",
 			),
 		"dimpb_processes" => array(
 			"friendly_name" => "Number of Concurrent Processes",
 			"description" => "Specify how many devices will be polled simultaneously until all devices have been polled.",
 			"default" => "7",
 			"method" => "textbox",
 			"max_length" => "10"
 			),
 		"dimpb_path_snmpset" => array(
 			"friendly_name" => "snmpset Binary Path",
 			"description" => "The path to your snmpset binary.",
 			"default" => "",
 			"method" => "textbox",
 			"max_length" => "100"
 			),
 		"dimpb_autosave_count" => array(
 			"friendly_name" => "Max count unsaved operations",
 			"description" => "Count unsaved operations for autosave start.",
 			"default" => "0",
 			"method" => "textbox",
 			"max_length" => "100"
 			),			
 		"impb_hdr_general" => array(
 			"friendly_name" => "D-Link IP-Mac-Port Blinding SNMP General Settings",
 			"method" => "spacer",
 			),
 		"dimpb_snmp_port" => array(
 			"friendly_name" => "SNMP Port",
 			"description" => "The UDP/TCP Port to poll the SNMP agent on.",
 			"method" => "textbox",
 			"default" => "161",
 			"max_length" => "100"
 			),			
 		"dimpb_snmp_timeout" => array(
 			"friendly_name" => "SNMP Timeout",
 			"description" => "Default SNMP timeout in milli-seconds.",
 			"method" => "textbox",
 			"default" => "500",
 			"max_length" => "100"
 			),
 		"dimpb_snmp_retries" => array(
 			"friendly_name" => "SNMP Retries",
 			"description" => "The number times the SNMP poller will attempt to reach the host before failing.",
 			"method" => "textbox",
 			"default" => "3",
 			"max_length" => "100"
 			),			
 		"impb_hdr_read_snmp" => array(
 			"friendly_name" => "D-Link IP-Mac-Port Blinding SNMP READ Settings",
 			"method" => "spacer",
 			),
 		"dimpb_read_snmp_ver" => array(
 			"friendly_name" => "SNMP Version",
 			"description" => "Default SNMP version for all new hosts.",
 			"method" => "drop_array",
 			"default" => "Version 2",
 			"array" => $impb_snmp_versions,
 			),
 		"dimpb_read_snmp_community" => array(
 			"friendly_name" => "SNMP Community",
 			"description" => "Default SNMP read community for all new hosts.",
 			"method" => "textbox",
 			"default" => "public",
 			"max_length" => "100"
 			),
 		"dimpb_read_snmp_username" => array(
 			"friendly_name" => "SNMP Username (v3)",
 			"description" => "Default SNMP v3 username for all new hosts.",
 			"method" => "textbox",
 			"default" => "public",
 			"max_length" => "100"
 			),
 		"dimpb_read_snmp_password" => array(
 			"friendly_name" => "SNMP Password (v3)",
 			"description" => "Default SNMP v3 password for all new hosts.",
 			"method" => "textbox",
 			"default" => "public",
 			"max_length" => "100"
 			),
 		"dimpb_snmp_get_auth_protocol" => array(
 			"method" => "drop_array",
 			"friendly_name" => "SNMP Auth Protocol (v3)",
 			"description" => "Choose the SNMPv3 Authorization Protocol.",
 			"default" => "MD5 (default)",
 			"array" => $snmp_auth_protocols,
 			),
 		"dimpb_snmp_get_priv_passphrase" => array(
 			"method" => "textbox",
 			"friendly_name" => "SNMP Privacy Passphrase (v3)",
 			"description" => "Choose the SNMPv3 Privacy Passphrase.",
 			"default" => "",
 			"max_length" => "200",
 			"size" => "40"
 			),
 		"dimpb_snmp_get_priv_protocol" => array(
 			"method" => "drop_array",
 			"friendly_name" => "SNMP Privacy Protocol (v3)",
 			"description" => "Choose the SNMPv3 Privacy Protocol.",
 			"default" => "DES (default)",
 			"array" => $snmp_priv_protocols,
 			),
 		"impb_hdr_write_snmp" => array(
 			"friendly_name" => "D-Link IP-Mac-Port Blinding SNMP WRITE Settings",
 			"method" => "spacer",
 			),
 		"dimpb_write_snmp_ver" => array(
 			"friendly_name" => "SNMP Version",
 			"description" => "Default SNMP version for all new hosts.",
 			"method" => "drop_array",
 			"default" => "Version 2",
 			"array" => $impb_snmp_versions,
 			),
 		"dimpb_write_snmp_community" => array(
 			"friendly_name" => "SNMP Community",
 			"description" => "Default SNMP read community for all new hosts.",
 			"method" => "textbox",
 			"default" => "private",
 			"max_length" => "100",
 			"size" => "15"
 			),
 		"dimpb_write_snmp_username" => array(
 			"friendly_name" => "SNMP Username (v3)",
 			"description" => "Default SNMP v3 username for all new hosts.",
 			"method" => "textbox",
 			"default" => "private",
 			"max_length" => "50",
 			"size" => "15"
 			),
 		"dimpb_write_snmp_password" => array(
 			"friendly_name" => "SNMP Password (v3)",
 			"description" => "Default SNMP v3 password for all new hosts.",
 			"method" => "textbox",
 			"default" => "",
 			"max_length" => "50",
 			"size" => "15"
 			),
 		"dimpb_snmp_set_auth_protocol" => array(
 			"method" => "drop_array",
 			"friendly_name" => "SNMP Auth Protocol (v3)",
 			"description" => "Choose the SNMPv3 Authorization Protocol.",
 			"default" => "MD5 (default)",
 			"array" => $snmp_auth_protocols,
 			),
 		"dimpb_snmp_set_priv_passphrase" => array(
 			"method" => "textbox",
 			"friendly_name" => "SNMP Privacy Passphrase (v3)",
 			"description" => "Choose the SNMPv3 Privacy Passphrase.",
 			"default" => "",
 			"max_length" => "200",
 			"size" => "40"
 			),
 		"dimpb_snmp_set_priv_protocol" => array(
 			"method" => "drop_array",
 			"friendly_name" => "SNMP Privacy Protocol (v3)",
 			"description" => "Choose the SNMPv3 Privacy Protocol.",
 			"default" => "DES (default)",
 			"array" => $snmp_priv_protocols,
 			),
 		"impb_hdr_configs" => array(
 			"friendly_name" => "D-Link IP-Mac-Port Blinding Configs Settings",
 			"method" => "spacer",
 			),
 		"dimpb_default_ip_mask" => array(
 			"method" => "textbox",
 			"friendly_name" => "Default IP Mask. ",
 			"description" => "Put here default ip mask (192.168.xxx.xxx)",
 			"default" => "",
 			"max_length" => "15",
 			"size" => "15"
 			),			
 		"dimpb_recheck_delete_blmacs" => array(
 			"method" => "checkbox",
 			"friendly_name" => "Do ReCheck when deleting blmacs ?",
 			"description" => "Если флаг установлен, то во время удаления записи необходимо двойное подтверждение удаления. Если не установлен - то достаточно проверки на существование.",
 			"default" => "no"
 			),		
 		"dimpb_check_new_records_for_ban" => array(
 			"method" => "checkbox",
 			"friendly_name" => "When new record create do check for ban ?",
 			"description" => "Если флаг установлен, то при обнаружении новой записи она будет проверна на бан.",
 			"default" => "on"
 			),	
 		"dimpb_use_snmptt_plugin" => array(
 			"method" => "checkbox",
 			"friendly_name" => "Try to use info from SNMPTT plugin for cacti ?",
 			"description" => "Если флаг установлен, то при каждом опросе будет производиться попытка получить данные SNMP Trap's для определения IP-адреса, с которым пришел заблокированный пакет.",
 			"default" => ""
 			),
 		"dimpb_use_camm_syslog" => array(
 			"method" => "checkbox",
 			"friendly_name" => "Try to use info from CAMM plugin for cacti ?",
 			"description" => "Если флаг установлен, то при каждом опросе будет производиться попытка получить данные SYSLOG для определения IP-адреса, с которым пришел заблокированный пакет. Имеет больший приоритет перед SNMPTT",
 			"default" => ""
 			),			
 		"dimpb_mac_addr_font_size" => array(
 			"method" => "textbox",
 			"friendly_name" => "Font size for mac-address",
 			"description" => "Размер шрифта по умолчанию для отображения mac-address.",
 			"default" => "2",
 			"max_length" => "2",
 			"size" => "15"
 			),
 		"dimpb_max_count_rec_for_auto_change" => array(
 			"method" => "textbox",
 			"friendly_name" => "Max count records",
 			"description" => "Максимальное количество записей на порту, при котором еще можно делать автоизменение записи",
 			"default" => "2",
 			"max_length" => "2",
 			"size" => "15"
 			),			
 		);
 
 		$settings["visual"]["impb_header"] = array(
 			"friendly_name" => "D-Link IP-Mac-Port Blinding",
 			"method" => "spacer",
 			);
 		$settings["visual"]["dimpb_num_rows"] = array(
 			"friendly_name" => "Rows Per Page",
 			"description" => "The number of rows to display on a single page for D-Link IP-Mac-Port Blinding devices and reports.",
 			"method" => "textbox",
 			"default" => "30",
 			"max_length" => "10"
 			);
	impb_check_upgrade();
 }
 
 
 function impb_draw_navigation_text ($nav) {
  // $nav["impblinding.php:"] = array("title" => "IpMacPort Blinding", "mapping" => "index.php:", "url" => "impblinding.php", "level" => "1");
   
   $nav["impb_devices.php:"] = array("title" => "IpMacPort Blinding", "mapping" => "index.php:", "url" => "impb_devices.php", "level" => "1");
   $nav["impb_devices.php:actions"] = array("title" => "Actions", "mapping" => "index.php:,impb_devices.php:", "url" => "", "level" => "2");  
   $nav["impb_devices.php:edit"] = array("title" => "(Edit)", "mapping" => "index.php:,impb_devices.php:", "url" => "", "level" => "2");
   $nav["impb_device_types.php:"] = array("title" => "IpMacPort Blinding Device Types", "mapping" => "index.php:", "url" => "impb_device_types.php", "level" => "1");
   $nav["impb_device_types.php:edit"] = array("title" => "(Edit)", "mapping" => "index.php:,impb_device_types.php:", "url" => "", "level" => "2");
   $nav["impb_device_types.php:import"] = array("title" => "(Import)", "mapping" => "index.php:,impb_device_types.php:", "url" => "", "level" => "2");
   $nav["impb_device_types.php:actions"] = array("title" => "Actions", "mapping" => "index.php:,impb_device_types.php:", "url" => "", "level" => "2");
   $nav["impb_utilities.php:"] = array("title" => "Ip-Mac_port Blinding Utilities", "mapping" => "index.php:", "url" => "impb_utilities.php", "level" => "1");
   $nav["impb_utilities.php:impb_utilities_purge_scanning_funcs"] = array("title" => "Refresh Scanning Functions", "mapping" => "index.php:,impb_utilities.php:", "url" => "impb_utilities.php", "level" => "2");   
   $nav["impb_utilities.php:impb_view_proc_status"] = array("title" => "Show Poller Status", "mapping" => "index.php:,impb_utilities.php:", "url" => "impb_utilities.php", "level" => "2");   
   $nav["impb_logs.php:"] = array("title" => "D-Link IP-Mac-Port Blinding Logs", "mapping" => "index.php:", "url" => "impb_logs.php", "level" => "1");
   $nav["impb_logs.php:actions_logs"] = array("title" => "Delete Dimpb LOGS", "mapping" => "index.php:,impb_logs.php:", "url" => "impb_logs.php", "level" => "2");   
   $nav["impb_view.php:"] = array("title" => "IpMacPort Blinding Viewer", "mapping" => "index.php:", "url" => "impb_view.php", "level" => "1");
   $nav["impb_view.php:actions"] = array("title" => "Actions", "mapping" => "index.php:,impb_view.php:", "url" => "", "level" => "2");
   
   
   $nav["impb_view_ports.php:"] = array("title" => __('IMPB View Ports'), "mapping" => "", "url" => "impb_view_ports.php", "level" => "0");
   $nav["impb_view_ports.php:actions"] = array("title" => __('Actions'), "mapping" => "index.php:,impb_view_ports.php:", "url" => "", "level" => "2");
   $nav["impb_view_devices.php:"] = array("title" => __('IMPB View Devices'), "mapping" => "", "url" => "impb_view_devices.php", "level" => "0");
   $nav["impb_view_devices.php:actions"] = array("title" => __('Actions'), "mapping" => "index.php:,impb_view_devices.php:", "url" => "", "level" => "2");   
   $nav["impb_view_bindings.php:"] = array("title" => __('IMPB View Bindings'), "mapping" => "", "url" => "impb_view_bindings.php", "level" => "0");
   $nav["impb_view_bindings.php:actions"] = array("title" => __('Actions'), "mapping" => "index.php:,impb_view_bindings.php:", "url" => "", "level" => "2");   

 
   
    return $nav;
 }
 
 function impb_page_head() {
	global $config;

	if (substr_count(get_current_page(), 'impb_')) {
		if (!isset($config['base_path'])) {
			print "<script type='text/javascript' src='" . URL_PATH . "plugins/impb/impb.js'></script>\n";
		}else{
			if (file_exists($config['base_path'] . '/plugins/impb/themes/' . get_selected_theme() . '/impb.css')) {
				print "<link type='text/css' href='" . $config['url_path'] . "plugins/impb/themes/" . get_selected_theme() . "/impb.css' rel='stylesheet'>\n";
			}else{
				print "<link type='text/css' href='" . $config['url_path'] . "plugins/impb/impb.css' rel='stylesheet'>\n";
			}
		}
		print "<script type='text/javascript' src='" . $config['url_path'] . "plugins/impb/jquery.bpopup.min.js'></script>\n";
		print "<script type='text/javascript' src='" . $config['url_path'] . "plugins/impb/impb.js'></script>\n";
		print "<script type='text/javascript' src='" . $config['url_path'] . "plugins/impb/impb_snmp.js'></script>\n";
	}
}


 function impb_poller_bottom () {
 	global $config;
 	include_once($config["base_path"] . "/lib/poller.php");
 	include_once($config["base_path"] . "/lib/data_query.php");
 	//include_once($config["base_path"] . "/lib/graph_export.php");
 	//include_once($config["base_path"] . "/lib/rrd.php");
 
 	$command_string = read_config_option("path_php_binary");
 	$extra_args = "-q " . $config["base_path"] . "/plugins/impb/poller_impb.php";
 	exec_background($command_string, "$extra_args");
 }
 

 function impb_config_form () {
 	global $fields_impb_device_type_edit, $impb_device_types, $fields_impb_device_edit;
 	global $impb_snmp_versions, $fields_macipport_edit, $fields_impb_macip_group_edit, $impb_revision, $impb_imp_MacBindingPortState;
 	global $impb_operation_macip_types, $impb_type_port_num_conversion, $impb_value_save_cfg;
 	global $impb_imp_mode_type, $impb_imp_action_type, $impb_imb_create_macip_type;
 	global $snmp_auth_protocols, $snmp_priv_protocols, $impb_imp_zerrostate_mode_type, $impb_imp_mode,$impb_imb_yes_no, $impb_func_version;
 
 	/* file: impb_device_types.php, action: edit */
 	$fields_impb_device_type_edit = array(
 	"spacer0" => array(
 		"method" => "spacer",
 		"friendly_name" => "General Device Type Options"
 		),
 	"description" => array(
 		"method" => "textbox",
 		"friendly_name" => "Description",
 		"description" => "Give this device type a meaningful description.",
 		"value" => "|arg1:description|",
 		"max_length" => "250"
 		)	,
 	"scanning_function" => array(
 		"method" => "drop_sql",
 		"friendly_name" => "Scanning Function",
 		"description" => "The Ip-Mac_port scanning function to call in order to obtain and store rows details.  The function name is all that is required. ",
 		"value" => "|arg1:scanning_function|",
 		"default" => 1,
 		"sql" => "select scanning_function as id, scanning_function as name from imb_scanning_functions order by scanning_function"
 		),
 	"type_port_num_conversion" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Тип преобразования номера портов.",
 		"description" => "Тип преобразования номера портов.",
 		"value" => "|arg1:type_port_num_conversion|",
 		"default" => 1,
 		"array" => $impb_type_port_num_conversion
 		),
 	"type_port_use_long" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Use long port hex",
 		"description" => "Используеться ли длинное обозначение в номерах портов ([00 00 00 00 00 00 40 00] заместо [00 00 40 00]). DES-3028",
 		"value" => "|arg1:type_port_use_long|",
 		"default" => 0,
 		"array" => $impb_imb_yes_no
 		),
 	"type_use_more_32x_port" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Port numbers more > 32",
 		"description" => "Количество портов > 32",
 		"value" => "|arg1:type_use_more_32x_port|",
 		"default" => 0,
 		"array" => $impb_imb_yes_no
 		),
 	"impb_func_version" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Address_binding Function Version",
 		"description" => "Address_binding Function Version",
 		"value" => "|arg1:impb_func_version|",
 		"default" => 0,
 		"array" => $impb_func_version
 		),		
 	"spacer1" => array(
 		"method" => "spacer",
 		"friendly_name" => "General Device Type SNMP Options"
 		),
 	"snmp_oid_MacBindingACLMode" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingACLMode [Read/Write]",
 		"description" => "SNMP OID для включения/выключения режима ACL IP MAC Port Binding . <br> Оставьте поле пустым, если этот функционал недоступен/неиспользуеться. <br>" . 
         "other(1)<br>enable(2)<br>disable(3)",
 		"value" => "|arg1:snmp_oid_MacBindingACLMode|",
 		"max_length" => "250"
 		)	,
 	"snmp_oid_MacBindingTrapLogState" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingTrapLogState [Read/Write]",
 		"description" => "When enable(2),whenever there's a new MAC that violates the pre-defined  Ip Mac Binding configuration, trap will be sent out and the relevant information will be logged in system.<br> Оставьте поле пустым, если этот функционал недоступен/неиспользуеться. <br>" . 
         "other(1)<br>enable(2)<br>disable(3)",
 		"value" => "|arg1:snmp_oid_MacBindingTrapLogState|",
 		"max_length" => "250"
 		)	,
 	"snmp_oid_Trap_eventid" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Trap Eventid",
 		"description" => "The value is used to find traps when MAC bloked", 
 		"value" => "|arg1:snmp_oid_Trap_eventid|",
 		"max_length" => "250"
 		),		
 	"snmp_oid_agentSaveCfg" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID agentSaveCfg [Read/Write]",
 		"description" => "As the object is set to 'set(3)', the current device configuration will be saved into to NV-RAM <br>",
 		"value" => "|arg1:snmp_oid_agentSaveCfg|",
 		"max_length" => "250"
 		)	,
 	"snmp_value_save_cfg" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Value for save config command",
 		"description" => "Какое значение установить для выполнения команды сохранения конфигурации",
 		"value" => "|arg1:snmp_value_save_cfg|",
 		"default" => 3,
 		"array" => $impb_value_save_cfg
 		)	,		
 	"snmp_timeout_agentSaveCfg" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP time Out for Save Config",
 		"description" => "SNMP Timeout for run command to store current configuration into to NV-RAM <br> For DES-30xx = 5 <br> For DES-35xx = 20",
 		"value" => "|arg1:snmp_timeout_agentSaveCfg|",
 		"max_length" => "3"
 		)	,	
 
 	"snmp_oid_swL2IpMacBindingFwdDCHPPackState" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingFwdDCHPPackState [Read/Write]",
 		"description" => "IMP Global Settings/DHCP Snoop State.<br> Оставьте поле пустым, если этот функционал недоступен/неиспользуеться. <br>" . 
         ".1.3.6.1.4.1.171.12.23.1.4.0<br>enable(1)<br>disable(2)",
 		"value" => "|arg1:snmp_oid_swL2IpMacBindingFwdDCHPPackState|",
 		"max_length" => "250"
 		)	,
 
 		
 	"spacer3" => array(
 		"method" => "spacer",
 		"friendly_name" => "[SNMP] ifInterfaces options"
 		),
 	"type_revision" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Ревизия устройства",
 		"description" => "Ревизия устройства",
 		"value" => "|arg1:type_revision|",
 		"default" => 1,
 		"array" => $impb_revision
 		),			
 	"snmp_oid_ifIndex" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID ifIndex [Read]",
 		"description" => "A unique value for each interface.", 
 		"value" => "|arg1:snmp_oid_ifIndex|",
 		"default" => '.1.3.6.1.2.1.2.2.1.1',
 		"max_length" => "250"
 		),
 	"snmp_oid_ifDescr" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID ifDescr [Read]",
 		"description" => "A textual string containing information about the interface.  This string should include the name of the manufacturer, the product name and the version of the hardware interface.", 
 		"value" => "|arg1:snmp_oid_ifZerroIPStates|",
 		"default" => '.1.3.6.1.2.1.2.2.1.2',
 		"max_length" => "250"
 		),
 	"snmp_oid_ifType" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID ifType [Read]",
 		"description" => "The type of interface, distinguished according to	the physical/link protocol(s) immediately `below'	the network layer in the protocol stack.", 
 		"value" => "|arg1:snmp_oid_ifType|",
 		"default" => '.1.3.6.1.2.1.2.2.1.3',
 		"max_length" => "250"
 		),
 	"snmp_oid_swL2PortCtrlAdminState" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2PortCtrlAdminState [Read/write]",
 		"description" => "The admin operational state of the interface. INTEGER  { other ( 1 ) , disabled ( 2 ) , enabled ( 3 ) } ", 
 		"value" => "|arg1:snmp_oid_swL2PortCtrlAdminState|",
 		"default" => '',
 		"max_length" => "250"
 		),
 	"snmp_oid_ifOperStatus" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID ifOperStatus [Read]",
 		"description" => "The current operational state of the interface. The testing(3) state indicates that no operational packets can be passed.", 
 		"value" => "|arg1:snmp_oid_ifOperStatus|",
 		"default" => '.1.3.6.1.2.1.2.2.1.8',
 		"max_length" => "250"
 		),
 	"snmp_oid_swL2PortCtrlSpeedState" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID admin swL2PortCtrlNwayState [Read/write]",
 		"description" => "Choose the port speed, duplex mode, and N-Way function mode. INTEGER  { nway-auto ( 1 ) , nway-disabled-10Mbps-Half ( 2 ) , nway-disabled-10Mbps-Full ( 3 ) , nway-disabled-100Mbps-Half ( 4 ) , nway-disabled-100Mbps-Full ( 5 ) , nway-disabled-1Gigabps-Full ( 7 ) , nway-disabled-1Gigabps-Full-Master ( 8 ) , nway-disabled-1Gigabps-Full-Slave ( 9 ) } .", 
 		"value" => "|arg1:snmp_oid_swL2PortCtrlSpeedState|",
 		"default" => '',
 		"max_length" => "250"
 		),
 	"snmp_oid_swL2PortSpeedStatus" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID admin swL2PortInfoNwayStatus [Read]",
 		"description" => "This object indicates the port speed and duplex mode. INTEGER  { auto ( 1 ) , half-10Mbps ( 2 ) , full-10Mbps ( 3 ) , half-100Mbps ( 4 ) , full-100Mbps ( 5 ) , full-1Gigabps ( 7 ) } .", 
 		"value" => "|arg1:snmp_oid_swL2PortSpeedStatus|",
 		"default" => '',
 		"max_length" => "250"
 		),		
 	"snmp_oid_ifSpeed" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID ifSpeed [Read]",
 		"description" => "An estimate of the interface's current bandwidth	in bits per second.  For interfaces which do not vary in bandwidth or for those where no accurate estimation can be made, this object should contain the nominal bandwidth.", 
 		"value" => "|arg1:snmp_oid_ifSpeed|",
 		"default" => '.1.3.6.1.2.1.2.2.1.5',
 		"max_length" => "250"
 		),

 	"snmp_oid_swL2LoopDetectPortState" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2LoopDetectPortState [Read/Write]",
 		"description" => "This object indicates the loopback detection function state on the port. INTEGER  { enabled ( 1 ) , disabled ( 2 ) } ",
 		"value" => "|arg1:snmp_oid_swL2LoopDetectPortState|",
 		"max_length" => "250"
 		),
 	"snmp_oid_swL2LoopDetectPortLoopVLAN" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2LoopDetectPortLoopVLAN [Read]",
 		"description" => "This object indicates the VLAN list that has detected a loopback.",
 		"value" => "|arg1:snmp_oid_swL2LoopDetectPortLoopVLAN|",
 		"max_length" => "250"
 		),		
 	"swL2PortErrPortReason" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2PortErrPortReason [Read]",
 		"description" => "This object decides whether the PortStatus STP is LBD or Storm control",
 		"value" => "|arg1:swL2PortErrPortReason|",
 		"max_length" => "250"
 		),		
		
		
 	"snmp_oid_MacBindingPortState" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingPortState [Read/Write]",
 		"description" => "SNMP OID для включение/выключение IP MAC Port Binding на определённом порту.<br>",
 		"value" => "|arg1:snmp_oid_MacBindingPortState|",
 		"max_length" => "250"
 		),
 	"type_imb_MacBindingPortState" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Тип привязки на порту.",
 		"description" => "Используеться простое или двойственное включение привязки",
 		"value" => "|arg1:type_imb_MacBindingPortState|",
 		"default" => 1,
 		"array" => $impb_imp_MacBindingPortState
 		),		
 	"snmp_oid_en_MacBindingZerroIpPortState" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingZerroIpPortState [Read/Write]",
 		"description" => "SNMP OID для включение/выключение режима Zerro IP на определённом порту. <br> Оставьте поле пустым, если этот функционал недоступен/неиспользуеться. <br>" . 
         "Смотри тип преобразования состояния ниже",
 		"value" => "|arg1:snmp_oid_en_MacBindingZerroIpPortState|",
 		"max_length" => "250"
 		)	,			
 	"type_imb_zerrostate_mode" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Тип преобразования режима ZerroIP.",
 		"description" => "Тип преобразования режима ZerroIP на порту",
 		"value" => "|arg1:type_imb_zerrostate_mode|",
 		"default" => 1,
 		"array" => $impb_imp_zerrostate_mode_type
 		),
 	"snmp_oid_en_fwd_dhcp_packets_state" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingForwardDHCPPacket  [Read/Write]",
 		"description" => "SNMP OID для включение/выключение режима Forward DHCP Packet на определённом порту. <br> Оставьте поле пустым, если этот функционал недоступен/неиспользуеться. <br>" . 
         ".1.3.6.1.4.1.171.12.23.3.2.1.4<br>enabled(1)<br>disabled(2)",
 		"value" => "|arg1:snmp_oid_en_fwd_dhcp_packets_state|",
 		"max_length" => "250"
 		)	,		
 	"snmp_oid_max_entry_count" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingMaxCountEntry  [Read/Write]",
 		"description" => "SNMP OID для определения максимального количества привязок на определённом порту. <br> Оставьте поле пустым, если этот функционал недоступен/неиспользуеться. <br>" . 
         ".1.3.6.1.4.1.171.12.23.3.2.1.5<br>(1-10) or (0 for [No Limit])",
 		"value" => "|arg1:snmp_oid_max_entry_count|",
 		"max_length" => "250"
 		)	,
 
 	"snmp_oid_ifAlias" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID ifAlias [Read/Write]",
 		"description" => "SNMP OID для изменения имени порта. <br> Оставьте поле пустым, если этот функционал недоступен/неиспользуеться. <br>" ,
 		"value" => "|arg1:snmp_oid_ifAlias|",
 		"default" => '.1.3.6.1.2.1.31.1.1.1.18',
 		"max_length" => "250"
 		),		
 	"spacer4" => array(
 		"method" => "spacer",
 		"friendly_name" => "[SNMP] DGS options"
 		),		
 	"snmp_oid_en_swIpMacBindingPortARPInspection" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swIpMacBindingPortARPInspection [Read/Write]",
 		"description" => "This object used to set ARP inspection state on the specified port. When ARP inspection is enabled on the port, the legal ARP packets will be forward, while the illegal packets will be dropped. strict : In this mode, all packets are dropped by default until a legal ARP or broadcast IP packets are detected. loose: In this mode, all packets are forwarded by default until an illegal ARP or broadcast IP packets are detected. <br> Оставьте поле пустым, если этот функционал недоступен/неиспользуеться. <br>",
 		"value" => "|arg1:snmp_oid_en_swIpMacBindingPortARPInspection|",
 		"max_length" => "250"
 		)	,
 	"snmp_oid_en_swIpMacBindingPortIPInspection" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swIpMacBindingPortIPInspection [Read/Write]",
 		"description" => "This object used to set the IP inspection state on the specified port. When IP inspection is enabled on the port, the legal IP packets will be forwarded, while the illegal IP packets will be dropped", 
 		"value" => "|arg1:snmp_oid_en_swIpMacBindingPortIPInspection|",
 		"max_length" => "250"
 		),
 	"snmp_oid_en_swIpMacBindingPortIPProtocol" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swIpMacBindingPortIPProtocol [Read/Write]",
 		"description" => "This object is used to set which kind of IP packets need to be checked by IP-MAC-PORT binding on the specified port. ipv4: Only IPv4 packets will be checked. ipv6: Only IPv6 packets will be checked. all: Both IPv4 and IPv6 packets will be checked.", 
 		"value" => "|arg1:snmp_oid_en_swIpMacBindingPortIPProtocol|",
 		"max_length" => "250"
 		)	,		

 	"spacer5" => array(
 		"method" => "spacer",
 		"friendly_name" => "[SNMP] Ip-Mac-Port Binding options"
 		),	
 	"type_imb_create_macip" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Очередность этапов при создании привязки",
 		"description" => "",
 		"value" => "|arg1:type_imb_create_macip|",
 		"default" => 1,
 		"array" => $impb_imb_create_macip_type
 		),
 
 	"snmp_oid_MacBindingIpIndex" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingIpIndex [Read/Write]",
 		"description" => "The IP address of IP-MAC binding. 1.3.6.1.4.1.171.11.64.1.2.7.2.1.1", 
 		"value" => "|arg1:snmp_oid_MacBindingIpIndex|",
 		"max_length" => "250"
 		),
 	"snmp_oid_MacBindingMac" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingMac [Read/Write]",
 		"description" => "The MAC address of IP-MAC binding. 1.3.6.1.4.1.171.11.64.1.2.7.2.1.2", 
 		"value" => "|arg1:snmp_oid_MacBindingMac|",
 		"max_length" => "250"
 		),		
 	"snmp_oid_MacBindingStatus" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingStatus [Read/Write]",
 		"description" => "Статус привязки. 1.3.6.1.4.1.171.11.64.1.2.7.2.1.2", 
 		"value" => "|arg1:snmp_oid_MacBindingStatus|",
 		"max_length" => "250"
 		),
 	"snmp_oid_MacBindingPorts" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingPorts [Read/Write]",
 		"description" => "Список портов, на которых включенна данная привязка. 1.3.6.1.4.1.171.11.64.1.2.7.2.1.4", 
 		"value" => "|arg1:snmp_oid_MacBindingPorts|",
 		"max_length" => "250"
 		),		
 	"snmp_oid_MacBindingAction" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingAction [Read/Write]",
 		"description" => "Состояние активности записи 1.3.6.1.4.1.171.11.64.1.2.7.2.1.5", 
 		"value" => "|arg1:snmp_oid_MacBindingAction|",
 		"max_length" => "250"
 		),
 
 	"type_imb_action" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Тип преобразования активности.",
 		"description" => "Тип преобразования состояния активности.",
 		"value" => "|arg1:type_imb_mode|",
 		"default" => 1,
 		"array" => $impb_imp_action_type
 		),
 	"snmp_oid_MacBindingMode" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingMode [Read/Write]",
 		"description" => "Режим данной привязки: 1.3.6.1.4.1.171.11.64.1.2.7.2.1.6", 
 		"value" => "|arg1:snmp_oid_MacBindingMode|",
 		"max_length" => "250"
 		),		
 
 	"type_imb_mode" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Тип преобразования режима.",
 		"description" => "Тип преобразования состояния режима записи.",
 		"value" => "|arg1:type_imb_mode|",
 		"default" => 1,
 		"array" => $impb_imp_mode_type
 		),
 		
 	"spacer6" => array(
 		"method" => "spacer",
 		"friendly_name" => "[SNMP] Ip-Mac-Port Blocked Macs options"
 		),		
 	"snmp_oid_MacBindingBlockedVID" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingBlockedVID [Read/Write]",
 		"description" => "The object specifies VLAN ID.", 
 		"value" => "|arg1:snmp_oid_MacBindingBlockedVID|",
 		"max_length" => "250"
 		),
 	"snmp_oid_MacBindingBlockedMac" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingBlockedMac [Read/Write]",
 		"description" => "The MAC address which was blocked.", 
 		"value" => "|arg1:snmp_oid_MacBindingBlockedMac|",
 		"max_length" => "250"
 		),
 	"snmp_oid_MacBindingBlockedIP" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingBlockedIP [Read/Write]",
 		"description" => "The MAC address which was blocked.", 
 		"value" => "|arg1:snmp_oid_MacBindingBlockedIP|",
 		"max_length" => "250"
 		),		
 	"snmp_oid_MacBindingBlockedVlanName" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingBlockedVlanName [Read/Write]",
 		"description" => "This object specifies VLAN name.", 
 		"value" => "|arg1:snmp_oid_MacBindingBlockedVlanName|",
 		"max_length" => "250"
 		),		
 	"snmp_oid_MacBindingBlockedPort" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingBlockedPort [Read/Write]",
 		"description" => "The port with which the MAC is associated.", 
 		"value" => "|arg1:snmp_oid_MacBindingBlockedPort|",
 		"max_length" => "250"
 		),		
 	"snmp_oid_BindingBlockedType" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP OID swL2IpMacBindingBlockedType [Read/Write]",
 		"description" => "The value is always blockByAddrBind. This entry will be delete by setting value delete(3). <br>other(1),<br>blockByAddrBind(2),<br>delete(3)", 
 		"value" => "|arg1:snmp_oid_BindingBlockedType|",
 		"max_length" => "250"
 		),	
 	"spacer7" => array(
 		"method" => "spacer",
 		"friendly_name" => "IP-MAC Binding Settings"
 		),
 	"setting_imb_def_mode" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Default IP-MAC Binding Mode",
 		"description" => "Тип привязки, который будет устанавливаться по умолчанию при создании записи на основе блока.",
 		"value" => "|arg1:setting_imb_def_mode|",
 		"default" => 1,
 		"array" => $impb_imp_mode
 		),
 	"setting_imb_use_autoban" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Can use utoban (from script) ?",
 		"description" => "Разрешено ли использовать автоматическую установку банов (от скриптов) ?",
 		"value" => "|arg1:setting_imb_use_autoban|",
 		"default" => 1,
 		"array" => $impb_imb_yes_no
 		),
 	"setting_imb_use_auto_unblock" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Использовать Автоснятие блоков ?",
 		"description" => "Разрешено ли использовать автоматическое удаление блоков, если ИП попавший в блок, принадлежит специально описанным сетям ?",
 		"value" => "|arg1:setting_imb_use_auto_unblock|",
 		"default" => 1,
 		"array" => $impb_imb_yes_no
 		),
 	"setting_imb_use_auto_add" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Использовать Автоматическое создание привязки ?",
 		"description" => "Использовать автоматическое создание привязки, если ИП, попавший в блок заранее прописан в таблице [На подключение]",
 		"value" => "|arg1:setting_imb_use_auto_add|",
 		"default" => 1,
 		"array" => $impb_imb_yes_no
 		),
 	"setting_imb_use_auto_change" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Использовать Автоматическое изменение привязки ?",
 		"description" => "Использовать автоматическое изменение привязки, если MAC, попал в блок с IP, который прописан на этом-же порту",
 		"value" => "|arg1:setting_imb_use_auto_change|",
 		"default" => 1,
 		"array" => $impb_imb_yes_no
 		),		
 	"setting_imb_use_reenable_onport" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Использовать повторное включение привязки ?",
 		"description" => "В некоторых случаях при удалении привязки, её уже нет на свиче, но она действует (нет прохождения пакетов). Отключение и повторное включение привязки на этом порту решает эту проблему.",
 		"value" => "|arg1:setting_imb_use_reenable_onport|",
 		"default" => 1,
 		"array" => $impb_imb_yes_no
 		),		


 		
 	"device_type_id" => array(
 		"method" => "hidden_zero",
 		"value" => "|arg1:device_type_id|"
 		),
 	"_device_type_id" => array(
 		"method" => "hidden_zero",
 		"value" => "|arg1:device_type_id|"
 		),		
 	"save_component_device_type" => array(
 		"method" => "hidden",
 		"value" => "1"
 		)
 	);
 
 	$fields_impb_device_edit = array(
 	"spacer0" => array(
 		"method" => "spacer",
 		"friendly_name" => "General Device Settings"
 		),
 	"description" => array(
 		"method" => "textbox",
 		"friendly_name" => "Description",
 		"description" => "Give this device a meaningful description.",
 		"value" => "|arg1:description|",
 		"max_length" => "250"
 		),
 	"hostname" => array(
 		"method" => "textbox",
 		"friendly_name" => "Hostname",
 		"description" => "Fill in the fully qualified hostname for this device.",
 		"value" => "|arg1:hostname|",
 		"max_length" => "250"
 		),
 	"device_type_id" => array(
 		"method" => "drop_sql",
 		"friendly_name" => "Device Type",
 		"description" => "Choose the Device Type to associate with this device.",
 		"value" => "|arg1:device_type_id|",
 		"none_value" => "None",
 		"sql" => "select device_type_id as id,description as name from imb_device_types order by name"
 		),		
 	"disabled" => array(
 		"method" => "checkbox",
 		"friendly_name" => "Disable Device",
 		"description" => "Check this box to disable all checks for this host.",
 		"value" => "|arg1:disabled|",
 		"default" => "",
 		"form_id" => false
 		),
 	"order_id" => array(
 		"method" => "textbox",
 		"friendly_name" => "Sort order ID",
 		"description" => "Fill in the sort order id for this device.",
 		"value" => "|arg1:order_id|",
 		"max_length" => "3"
 		),	
 	"color_row" => array(
 		"method" => "drop_color",
 		"friendly_name" => "Row color",
 		"description" => "Fill in the color row for this device.",
 		"value" => "|arg1:color_row|",
 		"default" => 0,
 		),		
 	"spacer2" => array(
 		"method" => "spacer",
 		"friendly_name" => "SNMP Default Settings"
 		),
 	"snmp_port" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Port",
 		"description" => "The UDP/TCP Port to poll the SNMP agent on.",
 		"value" => "|arg1:snmp_port|",
 		"max_length" => "8",
 		"default" => read_config_option("dimpb_snmp_port"),
 		"size" => "15"
 		),
 	"snmp_timeout" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Timeout",
 		"description" => "The maximum number of milliseconds Cacti will wait for an SNMP response (does not work with php-snmp support).",
 		"value" => "|arg1:snmp_timeout|",
 		"max_length" => "8",
 		"default" => read_config_option("dimpb_snmp_timeout"),
 		"size" => "15"
 		),
 	"snmp_retries" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Retries",
 		"description" => "The maximum number of attempts to reach a device via an SNMP readstring prior to giving up.",
 		"value" => "|arg1:snmp_retries|",
 		"max_length" => "8",
 		"default" => read_config_option("dimpb_snmp_retries"),
 		"size" => "15"
 		),
 	"snmp_max_oids" => array(
 		"method" => "textbox",
 		"friendly_name" => "Maximum OID's Per Get Request",
 		"description" => "Specified the number of OID's that can be obtained in a single SNMP Get request.  <br><i>NOTE: This feature only works when using Spine</i>",
 		"value" => "|arg1:snmp_max_oids|",
 		"max_length" => "8",
 		"default" => read_config_option("max_get_size"),
 		"size" => "15"
 		),		
 	"spacer3" => array(
 		"method" => "spacer",
 		"friendly_name" => "SNMP READ Settings"
 		),
 	"snmp_get_version" => array(
 		"method" => "drop_array",
 		"friendly_name" => "SNMP Version",
 		"description" => "Choose the SNMP version for this device.",
 		"on_change" => "changeDimpbHostForm()",
 		"value" => "|arg1:snmp_get_version|",
 		"default" => read_config_option("dimpb_read_snmp_ver"),
 		"array" => $impb_snmp_versions,
 		),	
 	"snmp_get_community" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Community",
 		"description" => "Fill in the SNMP read community for this device.",
 		"value" => "|arg1:snmp_get_community|",
 		"form_id" => "|arg1:id|",
 		"default" => read_config_option("dimpb_read_snmp_community"),
 		"max_length" => "100",
 		"size" => "40"
 		),
 	"snmp_get_username" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Username (v3)",
 		"description" => "Fill in the SNMP v3 username for this device.",
 		"value" => "|arg1:snmp_get_username|",
 		"default" => read_config_option("dimpb_read_snmp_username"),
 		"max_length" => "50",
 		"size" => "40"
 		),
 	"snmp_get_password" => array(
 		"method" => "textbox_password",
 		"friendly_name" => "SNMP Password (v3)",
 		"description" => "Fill in the SNMP v3 password for this device.",
 		"value" => "|arg1:snmp_get_password|",
 		"default" => read_config_option("dimpb_read_snmp_password"),
 		"max_length" => "50",
 		"size" => "40"
 		),
 	"snmp_get_auth_protocol" => array(
 		"method" => "drop_array",
 		"friendly_name" => "SNMP Auth Protocol (v3)",
 		"description" => "Choose the SNMPv3 Authorization Protocol.",
 		"value" => "|arg1:snmp_get_auth_protocol|",
 		"default" => read_config_option("dimpb_snmp_get_auth_protocol"),
 		"array" => $snmp_auth_protocols,
 		),
 	"snmp_get_priv_passphrase" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Privacy Passphrase (v3)",
 		"description" => "Choose the SNMPv3 Privacy Passphrase.",
 		"value" => "|arg1:snmp_get_priv_passphrase|",
 		"default" => read_config_option("dimpb_snmp_get_priv_passphrase"),
 		"max_length" => "200",
 		"size" => "40"
 		),
 	"snmp_get_priv_protocol" => array(
 		"method" => "drop_array",
 		"friendly_name" => "SNMP Privacy Protocol (v3)",
 		"description" => "Choose the SNMPv3 Privacy Protocol.",
 		"value" => "|arg1:snmp_get_priv_protocol|",
 		"default" => read_config_option("dimpb_snmp_get_priv_protocol"),
 		"array" => $snmp_priv_protocols,
 		),
 	"snmp_get_context" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Context",
 		"description" => "Enter the SNMP Context to use for this device.",
 		"value" => "|arg1:snmp_get_context|",
 		"default" => "",
 		"max_length" => "64",
 		"size" => "40"
 		),
 	"spacer4" => array(
 		"method" => "spacer",
 		"friendly_name" => "SNMP WRITE Settings"
 		),
 	"snmp_set_version" => array(
 		"method" => "drop_array",
 		"friendly_name" => "SNMP Version",
 		"description" => "Choose the SNMP version for this device.",
 		"on_change" => "changeDimpbHostForm()",
 		"value" => "|arg1:snmp_set_version|",
 		"default" => read_config_option("dimpb_write_snmp_ver"),
 		"array" => $impb_snmp_versions,
 		),	
 	"snmp_set_community" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Community",
 		"description" => "Fill in the SNMP read community for this device.",
 		"value" => "|arg1:snmp_set_community|",
 		"form_id" => "|arg1:id|",
 		"default" => read_config_option("dimpb_write_snmp_community"),
 		"max_length" => "100",
 		"size" => "40"
 		),
 	"snmp_set_username" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Username (v3)",
 		"description" => "Fill in the SNMP v3 username for this device.",
 		"value" => "|arg1:snmp_set_username|",
 		"default" => read_config_option("dimpb_write_snmp_username"),
 		"max_length" => "50",
 		"size" => "40"
 		),
 	"snmp_set_password" => array(
 		"method" => "textbox_password",
 		"friendly_name" => "SNMP Password (v3)",
 		"description" => "Fill in the SNMP v3 password for this device.",
 		"value" => "|arg1:snmp_set_password|",
 		"default" => read_config_option("dimpb_write_snmp_password"),
 		"max_length" => "50",
 		"size" => "40"
 		),
 	"snmp_set_auth_protocol" => array(
 		"method" => "drop_array",
 		"friendly_name" => "SNMP Auth Protocol (v3)",
 		"description" => "Choose the SNMPv3 Authorization Protocol.",
 		"value" => "|arg1:snmp_set_auth_protocol|",
 		"default" => read_config_option("dimpb_snmp_set_auth_protocol"),
 		"array" => $snmp_auth_protocols,
 		),
 	"snmp_set_priv_passphrase" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Privacy Passphrase (v3)",
 		"description" => "Choose the SNMPv3 Privacy Passphrase.",
 		"value" => "|arg1:snmp_set_priv_passphrase|",
 		"default" => read_config_option("dimpb_snmp_set_priv_passphrase"),
 		"max_length" => "200",
 		"size" => "40"
 		),
 	"snmp_set_priv_protocol" => array(
 		"method" => "drop_array",
 		"friendly_name" => "SNMP Privacy Protocol (v3)",
 		"description" => "Choose the SNMPv3 Privacy Protocol.",
 		"value" => "|arg1:snmp_set_priv_protocol|",
 		"default" => read_config_option("dimpb_snmp_set_priv_protocol"),
 		"array" => $snmp_priv_protocols,
 		),
 	"snmp_set_context" => array(
 		"method" => "textbox",
 		"friendly_name" => "SNMP Context",
 		"description" => "Enter the SNMP Context to use for this device.",
 		"value" => "|arg1:snmp_set_context|",
 		"default" => "",
 		"max_length" => "64",
 		"size" => "40"
 		),
 		
 	"device_id" => array(
 		"method" => "hidden_zero",
 		"value" => "|arg1:device_id|"
 		),
 	"_device_id" => array(
 		"method" => "hidden_zero",
 		"value" => "|arg1:device_id|"
 		),
 	"save_component_device" => array(
 		"method" => "hidden",
 		"value" => "1"
 		)
 	);	
 	$fields_macipport_edit = array(
 	"device_id" => array(
 		"method" => "hidden_zero",
 		"value" => "|arg1:device_id|"
 		),
 	"description" => array(
 		"method" => "textbox",
 		"friendly_name" => "Description",
 		"description" => "Give this device a meaningful description.",
 		"max_length" => "250"
 		),
 	"mac_address" => array(
 		"method" => "textbox",
 		"friendly_name" => "MAC_адресс",
 		"description" => "MAC_адресс",
 		"value" => "|arg1:macip_macaddr|",
 		"max_length" => "17"
 		),
 	"ip_address" => array(
 		"method" => "textbox",
 		"friendly_name" => "IP_адресс",
 		"description" => "IP_адресс",
 		"value" => "|arg1:macip_ipaddr|",
 		"max_length" => "15"
 		),		
 	"macip_port_list" => array(
 		"method" => "textbox",
 		"friendly_name" => "macip_port_list",
 		"description" => "macip_port_list",
 		"value" => "|arg1:macip_port_list|",
 		"max_length" => "250"
 		)
 	);
 	
 
 	$fields_impb_macip_group_edit = array(
 	"spacer0" => array(
 		"method" => "spacer",
 		"friendly_name" => "Изменяемые параметры"
 		),
 	"device_id" => array(
 		"method" => "drop_sql",
 		"friendly_name" => "Устройство назначения",
 		"description" => "Выберите устройство, на которое нужно скопировать/перенести записи",
 		"value" => "|arg1:device_id|",
 		"none_value" => "None",
 		"sql" => "select device_id as id, description as name from imb_devices order by name"
 		),
 	"port_number" => array(
 		"method" => "textbox",
 		"friendly_name" => "Номер порта",
 		"description" => "Выберите номер порта для изменения.",
 		"value" => "|arg1:port_number|",
 		"max_length" => "250"
 		),	
 	"operation_type" => array(
 		"method" => "drop_array",
 		"friendly_name" => "Тип операции",
 		"description" => "Выберите тип операции, которую необходимо совершить над записями",
 		"value" => "|arg1:operation_type|",
 		"default" => 1,
 		"array" => $impb_operation_macip_types
 		)	
 	);
 	}
 
 function impb_check_upgrade () {
 	// Let's only run this check if we are on a page that actually needs the data
 	$files = array('impb_view.php', 'impb_devices.php', 'impb_device_types.php', 'impb_logs.php', 'poller.php', 'impb_utilities.php', 'impb_view.php',);
	if (!in_array(get_current_page(), $files)) {
 		return;
	}
 
 	$current = plugin_impb_version ();
 	$current = $current['version'];
 	$old = db_fetch_cell("SELECT `value` FROM `settings` where name = 'impb_version'");
 	//if ($current != $old)
 		//impb_setup_table ();
 }
 
 function impb_check_dependencies() {
 	global $plugins, $config;
 	if (!in_array('settings', $plugins))
 		return false;
 	$v = settings_version();
 	if ($v['version'] < 0.2)
 		return false;
 	return true;
 }
 
 
 	
function impb_setup_table () {
 	global $config, $database_default;;
 
 	include_once($config["library_path"] . "/database.php");
	include_once($config['base_path'] . '/plugins/impb/lib/impb_functions.php');
 
 	// Set the new version
 	$new = plugin_impb_version();
 	$new = $new['version'];
 	$old = db_fetch_cell('SELECT `value` FROM `settings` where name = "impb_version"');
 	
 	if (trim($old) == '') {
 		$old = '0.0.1b';
 	}
 	$sql = "show tables from `" . $database_default . "`";
 	$result = db_fetch_assoc($sql) or die (mysql_error());
 
 	$tables = array();
 	$sql = array();
 
 	if (count($result) > 1) {
 		foreach($result as $index => $arr) {
 			foreach ($arr as $t) {
 				$tables[] = $t;
 			}
 		}
 	}
 	$result = db_fetch_assoc("SELECT `name` FROM `settings` where name like 'dim%%' order by name");
 	foreach($result as $row) {
 		$result_new[] =$row['name'];
 	}
 	
 if (!in_array("dimpb_num_rows", $result_new))
 	$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_num_rows]","INSERT INTO settings VALUES ('dimpb_num_rows',50);");	
 if (!in_array("dimpb_path_snmpset", $result_new))
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_path_snmpset]","INSERT INTO settings VALUES ('dimpb_path_snmpset','C:\\usr\\bin\\snmpset.exe');");
 if (!in_array("dimpb_last_run_time", $result_new))		
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_last_run_time]","INSERT INTO settings VALUES ('dimpb_last_run_time',0);");
 if (!in_array("dimpb_scan_date", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_scan_date]","INSERT INTO settings VALUES ('dimpb_scan_date',0);");
 if (!in_array("dimpb_read_snmp_community", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_read_snmp_community]","INSERT INTO settings VALUES ('dimpb_read_snmp_community','public');");
 if (!in_array("dimpb_stats_general", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_stats_general]","INSERT INTO settings VALUES ('dimpb_stats_general','Time:0 ConcurrentProcesses:0 Devices:0');");
 if (!in_array("dimpb_processes", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_get_priv_protocol]","INSERT INTO settings VALUES ('dimpb_processes',5);");
 if (!in_array("dimpb_script_runtime", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_processes]","INSERT INTO settings VALUES ('dimpb_script_runtime',5);");
 if (!in_array("impb_finish", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [impb_finish]","INSERT IGNORE INTO settings VALUES ('impb_finish',1);");
 if (!in_array("dimpb_stats", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_stats]","INSERT INTO settings VALUES ('dimpb_stats','ipmacs:0 Blockedmacs:0 Active_ports:0');");
 if (!in_array("dimpb_read_snmp_communities", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_read_snmp_communities]","INSERT INTO settings VALUES ('dimpb_read_snmp_communities','public:private:secret');");
 if (!in_array("dimpb_read_snmp_ver", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_read_snmp_ver]","INSERT INTO settings VALUES ('dimpb_read_snmp_ver',2);");
 if (!in_array("dimpb_read_snmp_port", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_read_snmp_port]","INSERT INTO settings VALUES ('dimpb_read_snmp_port',161);");
 if (!in_array("dimpb_read_snmp_timeout", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_read_snmp_timeout]","INSERT INTO settings VALUES ('dimpb_read_snmp_timeout',500);");
 if (!in_array("dimpb_read_snmp_retries", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_read_snmp_retries]","INSERT INTO settings VALUES ('dimpb_read_snmp_retries',3);");
 if (!in_array("dimpb_write_snmp_ver", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_write_snmp_ver]","INSERT INTO settings VALUES ('dimpb_write_snmp_ver',2);");
 if (!in_array("dimpb_write_snmp_community", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_write_snmp_community]","INSERT INTO settings VALUES ('dimpb_write_snmp_community','private');");
 if (!in_array("dimpb_write_snmp_communities", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_write_snmp_communities]","INSERT INTO settings VALUES ('dimpb_write_snmp_communities','public:private:secret');");
 if (!in_array("dimpb_write_snmp_port", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_write_snmp_port]","INSERT INTO settings VALUES ('dimpb_write_snmp_port',161);");
 if (!in_array("dimpb_write_snmp_timeout", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_write_snmp_timeout]","INSERT INTO settings VALUES ('dimpb_write_snmp_timeout',500);");
 if (!in_array("dimpb_write_snmp_retries", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_write_snmp_retries]","INSERT INTO settings VALUES ('dimpb_write_snmp_retries',3);");
 if (!in_array("dimpb_snmp_port", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_port]","INSERT INTO settings VALUES ('dimpb_snmp_port',161);");
 if (!in_array("dimpb_snmp_timeout", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_timeout]","INSERT INTO settings VALUES ('dimpb_snmp_timeout',500);");
 if (!in_array("dimpb_snmp_retries", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_retries]","INSERT INTO settings VALUES ('dimpb_snmp_retries',3);");
 if (!in_array("dimpb_read_snmp_username", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_read_snmp_username]","INSERT INTO settings VALUES ('dimpb_read_snmp_username','public');");
 if (!in_array("dimpb_read_snmp_password", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_read_snmp_password]","INSERT INTO settings VALUES ('dimpb_read_snmp_password','public');");
 if (!in_array("dimpb_write_snmp_username", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_write_snmp_username]","INSERT INTO settings VALUES ('dimpb_write_snmp_username','private');");
 if (!in_array("dimpb_write_snmp_password", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_write_snmp_password]","INSERT INTO settings VALUES ('dimpb_write_snmp_password','private');");
 if (!in_array("dimpb_autosave_count", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_autosave_count]","INSERT INTO settings VALUES ('dimpb_autosave_count',75);");
 if (!in_array("dimpb_snmp_set_auth_protocol", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_set_auth_protocol]","INSERT INTO settings VALUES ('dimpb_snmp_set_auth_protocol','MD5');");
 if (!in_array("dimpb_snmp_set_priv_passphrase", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_set_priv_passphrase]","INSERT INTO settings VALUES ('dimpb_snmp_set_priv_passphrase','');");
 if (!in_array("dimpb_snmp_set_priv_protocol", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_set_priv_protocol]","INSERT INTO settings VALUES ('dimpb_snmp_set_priv_protocol','DES');");
 if (!in_array("dimpb_snmp_get_auth_protocol", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_get_auth_protocol]","INSERT INTO settings VALUES ('dimpb_snmp_get_auth_protocol','MD5');");
 if (!in_array("dimpb_snmp_get_priv_passphrase", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_get_priv_passphrase]","INSERT INTO settings VALUES ('dimpb_snmp_get_priv_passphrase','');");
 if (!in_array("dimpb_snmp_get_priv_protocol", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_snmp_get_priv_protocol]","INSERT INTO settings VALUES ('dimpb_snmp_get_priv_protocol','DES');");
 if (!in_array("dimpb_default_ip_mask", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_default_ip_mask]","INSERT INTO settings VALUES ('dimpb_default_ip_mask','192.168.000.000');");
 if (!in_array("dimpb_mac_addr_font_size", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_mac_addr_font_size]","INSERT INTO settings VALUES ('dimpb_mac_addr_font_size','2');");
 if (!in_array("dimpb_max_count_rec_for_auto_change", $result_new))	
 		$sql[] = array("dimpb_execute_sql","Insert into [settings] new parametr [dimpb_max_count_rec_for_auto_change]","INSERT INTO settings VALUES ('dimpb_max_count_rec_for_auto_change','2');");
		
 	

	// If are realms are not present in plugin_realms recreate them with the old realm ids (minus 100) so that upgraded installs are not broken
	if (!db_fetch_cell("SELECT id FROM plugin_realms WHERE plugin = 'impb'")) {
		db_execute("INSERT INTO plugin_realms (id, plugin, file, display) VALUES (7777, 'impb', 'impb_view.php,impb_view_ports.php,impb_view_devices.php,impb_view_bindings.php,impb_view_blmacs.php,impb_view_netdel.php,impb_view_netadd.php,impb_view_recentmacs.php,impb_view_info.php', 'IMPB Viewer')");
		db_execute("INSERT INTO plugin_realms (id, plugin, file, display) VALUES (7778, 'impb', 'impblinding.php,impb_devices.php,impb_logs.php,impb_device_types.php,impb_utilities.php', 'IMPB Administrator')");
	}

 	if (!in_array('imb_blmacs', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_blmacs","CREATE TABLE `imb_blmacs` (
 		  `blmac_id` int(10) unsigned NOT NULL auto_increment,
 		  `blmac_active` int(2) NOT NULL default '0',
 		  `blmac_online` int(2) NOT NULL default '0',
 		  `device_id` int(11) NOT NULL default '0',
 		  `blmac_temp_id` int(10) NOT NULL default '0',
 		  `blmac_index` varchar(40) NOT NULL default '',
 		  `blmac_macaddr` varchar(20) NOT NULL default '',
 		  `blmac_port` varchar(4) NOT NULL default '',
 		  `blmac_type` char(2) default '',
 		  `blmac_vid` varchar(5) NOT NULL default '0',
 		  `blmac_vlanname` varchar(50) NOT NULL default '',
 		  `blmac_first_scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `blmac_scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `blmac_count_scan` int(11) NOT NULL default '0',
		  `blmac_blocked_ip` varchar(20) NOT NULL DEFAULT '',
		  `blmac_done` tinyint(1) NOT NULL DEFAULT '0',
		  `blmac_done_view_count` tinyint(1) NOT NULL DEFAULT '0',
		  `blmac_info` varchar(60) DEFAULT '',		  
 		  PRIMARY KEY  (`device_id`,`blmac_index`,`blmac_port`) USING BTREE,
 		  KEY `blmac_id` (`blmac_id`)
 		) ENGINE=InnoDB ;");
 	}
 


 	if (!in_array('imb_device_types', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_device_types","CREATE TABLE `imb_device_types` (
		  `device_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `cacti_host_template_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
		  `scanning_function` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `impb_func_version` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
		  `type_port_num_conversion` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
		  `type_port_use_long` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
		  `snmp_oid_MacBindingACLMode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
		  `snmp_oid_MacBindingTrapLogState` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
		  `snmp_oid_Trap_eventid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.1.3.6.1.4.1.171.11.64.1.2.15.3.0.3',
		  `snmp_oid_agentSaveCfg` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
		  `snmp_value_save_cfg` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '3',
		  `snmp_timeout_agentSaveCfg` tinyint(4) NOT NULL DEFAULT '2',
		  `snmp_oid_swL2IpMacBindingFwdDCHPPackState` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
		  `snmp_oid_ifIndex` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.1.3.6.1.2.1.2.2.1.1',
		  `snmp_oid_ifDescr` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.1.3.6.1.2.1.2.2.1.2',
		  `snmp_oid_ifType` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.1.3.6.1.2.1.2.2.1.3',
		  `snmp_oid_ifSpeed` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.1.3.6.1.2.1.2.2.1.5',
		  `snmp_oid_ifOperStatus` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.1.3.6.1.2.1.2.2.1.8',
		  `snmp_oid_MacBindingPortState` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `type_imb_MacBindingPortState` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
		  `snmp_oid_en_MacBindingZerroIpPortState` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `type_imb_zerrostate_mode` tinyint(4) NOT NULL DEFAULT '1',
		  `snmp_oid_en_swIpMacBindingPortARPInspection` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_en_swIpMacBindingPortIPInspection` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_en_swIpMacBindingPortIPProtocol` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_en_fwd_dhcp_packets_state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
		  `snmp_oid_max_entry_count` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
		  `snmp_oid_ifAlias` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.1.3.6.1.2.1.31.1.1.1.18',
		  `type_imb_create_macip` tinyint(4) NOT NULL DEFAULT '1',
		  `snmp_oid_MacBindingIpIndex` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_MacBindingMac` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_MacBindingStatus` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_MacBindingPorts` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_MacBindingAction` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `type_imb_action` tinyint(4) NOT NULL DEFAULT '1',
		  `snmp_oid_MacBindingMode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
		  `type_imb_mode` tinyint(4) NOT NULL DEFAULT '1',
		  `snmp_oid_MacBindingBlockedVID` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_MacBindingBlockedMac` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_MacBindingBlockedIP` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_MacBindingBlockedVlanName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_MacBindingBlockedPort` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_BindingBlockedType` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `setting_imb_def_mode` tinyint(4) NOT NULL DEFAULT '1',
		  `setting_imb_use_autoban` tinyint(4) NOT NULL DEFAULT '0',
		  `setting_imb_use_auto_unblock` tinyint(1) NOT NULL DEFAULT '0',
		  `setting_imb_use_auto_add` tinyint(1) NOT NULL DEFAULT '0',
		  `setting_imb_use_reenable_onport` tinyint(1) NOT NULL DEFAULT '0',
		  `type_use_more_32x_port` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
		  `setting_imb_use_auto_change` tinyint(1) NOT NULL DEFAULT '0',
		  `snmp_oid_swL2PortCtrlAdminState` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_swL2PortCtrlSpeedState` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_swL2PortSpeedStatus` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_swL2LoopDetectPortState` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `swL2PortErrPortReason` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `snmp_oid_swL2LoopDetectPortLoopVLAN` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
		  `type_revision` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
		  `snmp_oid_Dot1qVlanStaticEntry` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '.1.3.6.1.2.1.17.7.1.4.3.1',
		  PRIMARY KEY (`device_type_id`),
		  UNIQUE KEY `description` (`description`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
		
		$sql[] = array("dimpb_execute_sql","INSERT INTO `imb_device_types` VALUES 
			(2,'DES-3526',0,'scan_dlink_universal','33','1','0','.1.3.6.1.4.1.171.11.64.1.2.7.6.0','.1.3.6.1.4.1.171.11.64.1.2.7.5.0','.1.3.6.1.4.1.171.11.64.1.2.15.3.0.3','.1.3.6.1.4.1.171.11.64.1.2.15.3.0.3','3',45,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.11.64.1.2.7.1.1.2','1','.1.3.6.1.4.1.171.11.64.1.2.7.1.1.3',2,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.11.64.1.2.7.2.1.1','.1.3.6.1.4.1.171.11.64.1.2.7.2.1.2','.1.3.6.1.4.1.171.11.64.1.2.7.2.1.3','.1.3.6.1.4.1.171.11.64.1.2.7.2.1.4','.1.3.6.1.4.1.171.11.64.1.2.7.2.1.5',2,'.1.3.6.1.4.1.171.11.64.1.2.7.2.1.6',2,'.1.3.6.1.4.1.171.11.64.1.2.7.3.1.1','.1.3.6.1.4.1.171.11.64.1.2.7.3.1.2','','.1.3.6.1.4.1.171.11.64.1.2.7.3.1.3','.1.3.6.1.4.1.171.11.64.1.2.7.3.1.4','.1.3.6.1.4.1.171.11.64.1.2.7.3.1.5',2,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.64.1.2.4.2.1.3','.1.3.6.1.4.1.171.11.64.1.2.4.2.1.4','.1.3.6.1.4.1.171.11.64.1.2.4.4.1.6','.1.3.6.1.4.1.171.11.64.1.2.12.2.1.1.2','','.1.3.6.1.4.1.171.11.64.1.2.12.2.1.1.3','2','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(6,'DES-3828',0,'scan_dlink_universal','0','2','0','.1.3.6.1.4.1.171.12.23.1.2.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.1.2.6.0','3',32,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','1','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','.1.3.6.1.4.1.171.12.23.4.1.1.5',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,0,0,0,'0',0,'','','','','','','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(7,'DES-3028',0,'scan_dlink_universal','33','1','1','.1.3.6.1.4.1.171.12.23.1.5.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.6.0','2',2,'.1.3.6.1.4.1.171.12.23.1.4.0','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','2','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',3,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','.1.3.6.1.4.1.171.12.23.4.1.1.5',1,'',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.63.6.2.2.2.1.3','.1.3.6.1.4.1.171.11.63.6.2.2.2.1.4','.1.3.6.1.4.1.171.11.63.6.2.2.1.1.5','.1.3.6.1.4.1.171.11.63.6.2.21.2.1.1.2','','','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(8,'DES-30xx NEW0012',0,'scan_dlink_universal','33','1','0','','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.1','.1.3.6.1.4.1.171.12.23.5.0.1','3',4,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','1','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',2,'',2,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,0,0,0,'0',0,'','','','','','','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(9,'DGS-34xx',0,'scan_dlink_universal','39','1','0','','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.1.2.18.4.0','2',15,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','5','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','.1.3.6.1.4.1.171.12.23.4.1.1.5',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,0,0,'0',0,'.1.3.6.1.2.1.2.2.1.7','.1.3.6.1.4.1.171.11.119.6.2.3.2.1.5','.1.3.6.1.4.1.171.11.119.6.2.3.1.1.6','.1.3.6.1.4.1.171.12.41.3.1.1.2','','.1.3.6.1.4.1.171.12.41.3.1.1.3','2','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(10,'DES-3200-26',0,'scan_dlink_universal','33','1','1','.1.3.6.1.4.1.171.12.23.1.5.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.6.0','2',10,'.1.3.6.1.4.1.171.12.23.1.4.0','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','2','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',3,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3','.1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4','.1.3.6.1.4.1.171.11.113.1.5.2.2.1.1.5','.1.3.6.1.4.1.171.11.113.1.5.2.21.2.1.1.2','.1.3.6.1.4.1.171.11.113.1.5.2.2.3.1.4','','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(11,'DES-35520',0,'scan_dlink_universal','33','1','1','.1.3.6.1.4.1.171.12.23.1.2.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.23.5.0.5','3',32,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','1','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','.1.3.6.1.4.1.171.12.23.4.1.1.5',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,1,0,'0',1,'','','','','','','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(12,'DES-3550',0,'scan_dlink_universal','0','1','0','.1.3.6.1.4.1.171.11.64.2.2.7.6.0','.1.3.6.1.4.1.171.11.64.2.2.7.5.0','.1.3.6.1.4.1.171.11.64.2.2.15.3.0.3','.1.3.6.1.4.1.171.12.1.2.6.0','3',55,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.11.64.2.2.7.1.1.2','1','.1.3.6.1.4.1.171.11.64.2.2.7.1.1.3',2,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.11.64.2.2.7.2.1.1','.1.3.6.1.4.1.171.11.64.2.2.7.2.1.2','.1.3.6.1.4.1.171.11.64.2.2.7.2.1.3','.1.3.6.1.4.1.171.11.64.2.2.7.2.1.4','.1.3.6.1.4.1.171.11.64.2.2.7.2.1.5',2,'.1.3.6.1.4.1.171.11.64.2.2.7.2.1.6',2,'.1.3.6.1.4.1.171.11.64.2.2.7.3.1.1','.1.3.6.1.4.1.171.11.64.2.2.7.3.1.2','','.1.3.6.1.4.1.171.11.64.2.2.7.3.1.3','.1.3.6.1.4.1.171.11.64.2.2.7.3.1.4','.1.3.6.1.4.1.171.11.64.2.2.7.3.1.5',2,0,1,1,0,'1',1,'','','','','','','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(13,'DES-1228ME',0,'scan_dlink_universal','33','1','1','.1.3.6.1.4.1.171.12.23.1.5.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.6.0','2',10,'.1.3.6.1.4.1.171.12.23.1.4.0','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','2','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',3,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','.1.3.6.1.4.1.171.12.23.4.1.1.5',1,'',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.116.2.2.2.2.1.3','.1.3.6.1.4.1.171.11.116.2.2.2.2.1.4','.1.3.6.1.4.1.171.11.116.2.2.2.1.1.5','.1.3.6.1.4.1.171.11.116.2.2.21.2.1.1.2','','.1.3.6.1.4.1.171.11.116.2.2.21.2.1.1.3','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(14,'DGS-3120-24SC А1/2',0,'scan_dlink_universal','39','1','0','','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.1.2.18.4.0','2',15,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','','5','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'.1.3.6.1.4.1.171.12.23.3.2.1.16','.1.3.6.1.4.1.171.12.23.3.2.1.17','.1.3.6.1.4.1.171.12.23.3.2.1.18','.1.3.6.1.4.1.171.12.23.3.2.1.4','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.7',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.117.1.3.2.3.2.1.4','.1.3.6.1.4.1.171.11.117.1.3.2.3.2.1.5','.1.3.6.1.4.1.171.11.117.1.3.2.3.1.1.6','.1.3.6.1.4.1.171.12.41.3.1.1.2','','.1.3.6.1.4.1.171.12.41.3.1.1.3','2','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(15,'DES-3200-52 С1',0,'scan_dlink_universal','39','1','0','.1.3.6.1.4.1.171.12.23.1.5.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.18.4.0','2',10,'.1.3.6.1.4.1.171.12.23.1.4.0','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','','3','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'.1.3.6.1.4.1.171.12.23.3.2.1.16','.1.3.6.1.4.1.171.12.23.3.2.1.17','.1.3.6.1.4.1.171.12.23.3.2.1.18','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.7',1,0,1,1,0,'1',1,'.1.3.6.1.4.1.171.11.113.9.1.2.3.2.1.4','.1.3.6.1.4.1.171.11.113.9.1.2.3.2.1.5','.1.3.6.1.4.1.171.11.113.9.1.2.3.1.1.6','.1.3.6.1.4.1.171.11.113.9.1.2.18.2.1.1.2','.1.3.6.1.4.1.171.11.113.9.1.2.3.7.1.4','.1.3.6.1.4.1.171.11.113.9.1.2.18.2.1.1.3','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(16,'DES-3200-26 C1',0,'scan_dlink_universal','39','1','1','','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.18.4.0','2',10,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','','4','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'.1.3.6.1.4.1.171.12.23.3.2.1.16','.1.3.6.1.4.1.171.12.23.3.2.1.17','.1.3.6.1.4.1.171.12.23.3.2.1.18','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.7',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.113.4.1.2.3.2.1.4','.1.3.6.1.4.1.171.11.113.4.1.2.3.2.1.5','.1.3.6.1.4.1.171.11.113.4.1.2.3.1.1.6','.1.3.6.1.4.1.171.11.113.4.1.2.18.2.1.1.2','.1.3.6.1.4.1.171.11.113.4.1.2.3.7.1.4','.1.3.6.1.4.1.171.11.113.4.1.2.18.2.1.1.3','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(17,'DES-1210-28',0,'scan_dlink_universal','39','3','1','.1.3.6.1.4.1.171.12.23.1.5.0','.1.3.6.1.4.1.171.10.75.15.2.14.10.14','.1.3.6.1.4.1.171.10.75.4.1.1.1','.1.3.6.1.4.1.171.10.75.15.2.1.10.0','3',10,'.1.3.6.1.4.1.171.10.75.15.2.14.10.1.1.8','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.10.75.15.2.14.10.1.1.2','71','.1.3.6.1.4.1.171.10.75.15.2.14.10.1.1.7',3,'.1.3.6.1.4.1.171.10.75.15.2.14.10.1.1.5','.1.3.6.1.4.1.171.10.75.15.2.14.10.1.1.6','.1.3.6.1.4.1.171.10.75.15.2.14.10.1.1.12','.1.3.6.1.4.1.171.10.75.15.2.14.10.1.1.4','','.1.3.6.1.2.1.31.1.1.1.18',4,'.1.3.6.1.4.1.171.10.75.15.2.14.10.3.1.1','.1.3.6.1.4.1.171.10.75.15.2.14.10.3.1.2','.1.3.6.1.4.1.171.10.75.15.2.14.10.3.1.4','.1.3.6.1.4.1.171.10.75.15.2.14.10.3.1.3','',1,'',1,'.1.3.6.1.4.1.171.10.75.15.2.14.10.4.1.2','.1.3.6.1.4.1.171.10.75.15.2.14.10.4.1.1','.1.3.6.1.4.1.171.10.75.15.2.14.10.4.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.10.75.15.2.14.10.4.1.3','.1.3.6.1.4.1.171.10.75.15.2.14.10.4.1.5',1,0,1,1,0,'0',1,'','','','','','','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(18,'DES-3010G',0,'scan_dlink_universal','33','1','0','','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.1','.1.3.6.1.4.1.171.12.1.2.6.0','3',5,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','6','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.63.1.2.2.2.2.1.2','.1.3.6.1.4.1.171.11.63.1.2.2.2.2.1.3','.1.3.6.1.4.1.171.11.63.1.2.2.2.1.1.5','.1.3.6.1.4.1.171.11.63.1.2.2.18.2.1.1.2','','.1.3.6.1.4.1.171.11.63.1.2.2.18.2.1.1.3','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(19,'DGS-3627',0,'scan_dlink_universal','33','1','1','.1.3.6.1.4.1.171.12.23.1.2.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.1.2.6.0','3',40,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','2','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',2,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',2,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.70.8.2.3.2.1.4','','','','','','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(20,'DES-3200-28F A/B',0,'scan_dlink_universal','33','1','1','.1.3.6.1.4.1.171.12.23.1.5.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.6.0','2',10,'.1.3.6.1.4.1.171.12.23.1.4.0','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','2','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',3,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.113.1.4.2.2.2.1.3','.1.3.6.1.4.1.171.11.113.1.4.2.2.2.1.4','.1.3.6.1.4.1.171.11.113.1.4.2.2.1.1.5','.1.3.6.1.4.1.171.11.113.1.4.2.21.2.1.1.2','.1.3.6.1.4.1.171.11.113.1.4.2.2.3.1.4','.1.3.6.1.4.1.171.11.113.1.4.2.21.2.1.1.3','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(21,'DES-3200-18',0,'scan_dlink_universal','33','1','1','.1.3.6.1.4.1.171.12.23.1.5.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.6.0','2',10,'.1.3.6.1.4.1.171.12.23.1.4.0','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','2','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',3,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.113.1.2.2.2.2.1.3','.1.3.6.1.4.1.171.11.113.1.2.2.2.2.1.4','.1.3.6.1.4.1.171.11.113.1.2.2.2.1.1.5','.1.3.6.1.4.1.171.11.113.1.2.2.21.2.1.1.2','.1.3.6.1.4.1.171.11.113.1.2.2.2.3.1.4','.1.3.6.1.4.1.171.11.113.1.2.2.21.2.1.1.3','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(22,'DES-3200-28F C1',0,'scan_dlink_universal','39','1','1','.1.3.6.1.4.1.171.12.23.1.2','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.18.4.0','2',10,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','','4','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'.1.3.6.1.4.1.171.12.23.3.2.1.16','.1.3.6.1.4.1.171.12.23.3.2.1.17','.1.3.6.1.4.1.171.12.23.3.2.1.18','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.7',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.113.6.1.2.3.2.1.4','.1.3.6.1.4.1.171.11.113.6.1.2.3.2.1.5','.1.3.6.1.4.1.171.11.113.6.1.2.3.1.1.6','.1.3.6.1.4.1.171.11.113.6.1.2.18.2.1.1.2','.1.3.6.1.4.1.171.11.113.6.1.2.3.7.1.4','.1.3.6.1.4.1.171.11.113.6.1.2.18.2.1.1.3','2','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(23,'DGS-3120 B1',0,'scan_dlink_universal','39','1','0','','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.1.2.18.4.0','2',15,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','','5','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'.1.3.6.1.4.1.171.12.23.3.2.1.16','.1.3.6.1.4.1.171.12.23.3.2.1.17','.1.3.6.1.4.1.171.12.23.3.2.1.18','.1.3.6.1.4.1.171.12.23.3.2.1.4','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.7',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.117.4.1.2.3.2.1.4','.1.3.6.1.4.1.171.11.117.1.1.2.3.2.1.5','.1.3.6.1.4.1.171.11.117.1.1.2.3.2.1.6','.1.3.6.1.4.1.171.12.41.3.1.1.2','','.1.3.6.1.4.1.171.12.41.3.1.1.3','2','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(24,'DGS-3120-24TC B1',0,'scan_dlink_universal','39','1','0','.1.3.6.1.4.1.171.12.23.1.2.0','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.1.2.18.4.0','2',15,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','','5','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'.1.3.6.1.4.1.171.12.23.3.2.1.16','.1.3.6.1.4.1.171.12.23.3.2.1.17','.1.3.6.1.4.1.171.12.23.3.2.1.18','.1.3.6.1.4.1.171.12.23.3.2.1.4','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.7',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.117.1.1.2.3.2.1.4','.1.3.6.1.4.1.171.11.117.1.1.2.3.2.1.5','.1.3.6.1.4.1.171.11.117.1.1.2.3.1.1.6','.1.3.6.1.4.1.171.12.41.3.1.1.2','','.1.3.6.1.4.1.171.12.41.3.1.1.3','2','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(25,'Cisco 2950',0,'scan_cisco_universal','','','','','','','','',0,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','','','',0,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',0,'','','','','',0,'',0,'','','','','','',0,0,0,0,0,'',0,'','','','','','','','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(26,'DGS-36xx',0,'scan_dlink_universal','33','1','0','','.1.3.6.1.4.1.171.12.23.1.1','','','3',0,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','1','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,0,1,0,'0',1,'.1.3.6.1.4.1.171.11.118.2.2.3.2.1.4','.1.3.6.1.4.1.171.11.118.2.2.3.2.1.5','.1.3.6.1.4.1.171.11.118.2.2.3.1.1.6','.1.3.6.1.4.1.171.12.41.3.1.1.2','','.1.3.6.1.4.1.171.12.41.3.1.1.3','2','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(27,'DES-3200-10',0,'scan_dlink_universal','33','1','1','.1.3.6.1.4.1.171.12.23.1.5.0','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.1.2.6.0','2',10,'.1.3.6.1.4.1.171.12.23.1.4.0','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','2','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','.1.3.6.1.4.1.171.12.23.3.2.1.4','.1.3.6.1.4.1.171.12.23.3.2.1.5','.1.3.6.1.2.1.31.1.1.1.18',2,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','',1,'',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,1,0,'0',1,'.1.3.6.1.4.1.171.11.113.1.1.2.2.2.1.3','.1.3.6.1.4.1.171.11.113.1.1.2.2.2.1.4','.1.3.6.1.4.1.171.11.113.1.1.2.2.2.1.5','.1.3.6.1.4.1.171.11.113.1.1.2.21.2.1.1.2','.1.3.6.1.4.1.171.11.113.1.1.2.2.3.1.4','.1.3.6.1.4.1.171.11.113.1.1.2.21.2.1.1.3','1','.1.3.6.1.2.1.17.7.1.4.3.1'),
			(28,'DGS-3420-52Т',0,'scan_dlink_universal','39','1','0','','.1.3.6.1.4.1.171.12.23.1.1.0','.1.3.6.1.4.1.171.12.23.5.0.5','.1.3.6.1.4.1.171.12.1.2.18.4.0','2',15,'','.1.3.6.1.2.1.2.2.1.1','.1.3.6.1.2.1.2.2.1.2','.1.3.6.1.2.1.2.2.1.3','.1.3.6.1.2.1.2.2.1.5','.1.3.6.1.2.1.2.2.1.8','.1.3.6.1.4.1.171.12.23.3.2.1.2','5','.1.3.6.1.4.1.171.12.23.3.2.1.3',1,'','','','','','.1.3.6.1.2.1.31.1.1.1.18',1,'.1.3.6.1.4.1.171.12.23.4.1.1.1','.1.3.6.1.4.1.171.12.23.4.1.1.2','.1.3.6.1.4.1.171.12.23.4.1.1.3','.1.3.6.1.4.1.171.12.23.4.1.1.4','.1.3.6.1.4.1.171.12.23.4.1.1.5',1,'.1.3.6.1.4.1.171.12.23.4.1.1.6',1,'.1.3.6.1.4.1.171.12.23.4.2.1.1','.1.3.6.1.4.1.171.12.23.4.2.1.2','','.1.3.6.1.4.1.171.12.23.4.2.1.3','.1.3.6.1.4.1.171.12.23.4.2.1.4','.1.3.6.1.4.1.171.12.23.4.2.1.5',1,0,1,0,0,'0',0,'.1.3.6.1.2.1.2.2.1.7','.1.3.6.1.4.1.171.11.119.4.2.3.2.1.5','.1.3.6.1.4.1.171.11.119.4.2.3.1.1.6','.1.3.6.1.4.1.171.12.41.3.1.1.2','','.1.3.6.1.4.1.171.12.41.3.1.1.3','2','.1.3.6.1.2.1.17.7.1.4.3.1'); ");

	}



 	if (!in_array('imb_devices', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_devices","CREATE TABLE `imb_devices` (
 		  `device_id` int(10) unsigned NOT NULL auto_increment,
 		  `device_type_id` int(10) unsigned default '0',
 		  `hostname` varchar(40) NOT NULL default '',
 		  `description` varchar(100) NOT NULL default '',
 		  `disabled` char(2) default '',
 		  `ip_mac_total` int(10) unsigned NOT NULL default '0',
 		  `ip_mac_blocked_total` int(10) unsigned NOT NULL default '0',
 		  `ports_total` int(10) unsigned NOT NULL default '0',
 		  `ports_enable_total` int(10) unsigned NOT NULL default '0',
 		  `ports_enable_zerroip_total` int(10) unsigned NOT NULL default '0',
 		  `ports_active` int(10) unsigned NOT NULL default '0',
 		  `ip_mac_blocked_offline_total` int(10) unsigned NOT NULL default '0',
 		  `ip_mac_offline_total` int(10) unsigned NOT NULL default '0',
 		  `ports_offline_enable_zerroip_total` int(10) unsigned NOT NULL default '0',
 		  `ports_offline_enable_total` int(10) unsigned NOT NULL default '0',
 		  `ports_offline_total` int(10) unsigned NOT NULL default '0',
 		  `count_unsaved_actions` int(10) unsigned NOT NULL default '0',
 		  `scan_type` tinyint(11) NOT NULL default '1',
 		  `snmp_port` int(10) NOT NULL default '161',
 		  `snmp_timeout` int(10) unsigned NOT NULL default '500',
 		  `snmp_retries` tinyint(11) unsigned NOT NULL default '3',
 		  `snmp_sysName` varchar(100) default '',
 		  `snmp_sysLocation` varchar(100) default '',
 		  `snmp_sysContact` varchar(100) default '',
 		  `snmp_sysObjectID` varchar(100) default NULL,
 		  `snmp_sysDescr` varchar(100) default NULL,
 		  `snmp_sysUptime` varchar(100) default NULL,
 		  `snmp_status` int(10) unsigned NOT NULL default '0',
 		  `last_runmessage` varchar(100) default '',
 		  `last_rundate` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `last_runduration` decimal(10,5) NOT NULL default '0.00000',
 		  `snmp_get_community` varchar(100) default NULL,
 		  `snmp_get_version` tinyint(1) unsigned NOT NULL default '1',
 		  `snmp_get_username` varchar(50) default NULL,
 		  `snmp_get_password` varchar(50) default NULL,
 		  `snmp_set_community` varchar(100) default NULL,
 		  `snmp_set_version` tinyint(1) unsigned NOT NULL default '1',
 		  `snmp_set_username` varchar(50) default NULL,
 		  `snmp_set_password` varchar(50) default NULL,
 		  PRIMARY KEY  (`hostname`,`snmp_port`),
 		  KEY `device_id` (`device_id`),
 		  KEY `snmp_sysDescr` (`snmp_sysDescr`),
 		  KEY `snmp_sysObjectID` (`snmp_sysObjectID`),
 		  KEY `device_type_id` (`device_type_id`)
 		) ENGINE=InnoDB   COMMENT='Devices to be scanned for Ip-Mac-Port rows';");
 	}
 
 	if (!in_array('imb_log', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_log","CREATE TABLE `imb_log` (
 		  `log_id` int(10) unsigned NOT NULL auto_increment,
 		  `log_user_id` int(11) NOT NULL default '0',
 		  `log_user_full_name` varchar(60) NOT NULL default '',
 		  `log_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `log_object` varchar(20) NOT NULL default '',
 		  `log_object_id` varchar(20) NOT NULL default '',
 		  `log_operation` varchar(20) NOT NULL default '',
 		  `log_device_id` int(11) NOT NULL default '0',
 		  `log_message` text NOT NULL,
 		  `log_rezult_short` varchar(10) NOT NULL default '',
 		  `log_rezult` varchar(80) NOT NULL default '',
 		  `log_check_rezult_short` varchar(10) NOT NULL default '',
 		  `log_check_rezult` varchar(80) NOT NULL default '',
 		  `log_read_this_user` char(2) NOT NULL default '0',
 		  `log_read_admin` char(2) NOT NULL default '0',
 		  `log_saved` char(2) NOT NULL default '0',
 		  PRIMARY KEY  (`log_id`)
 		) ENGINE=InnoDB  ;");
 	}
 
 	if (!in_array('imb_macip', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_macip","CREATE TABLE `imb_macip` (
 		  `macip_id` int(10) unsigned NOT NULL auto_increment,
 		  `device_id` int(11) NOT NULL default '0',
 		  `macip_index` varchar(20) NOT NULL default '',
 		  `macip_macaddr` varchar(20) NOT NULL default '',
 		  `macip_ipaddr` varchar(20) NOT NULL default '',
 		  `macip_port_hex` varchar(36) NOT NULL default '',
 		  `macip_port_list` varchar(80) NOT NULL default '',
 		  `macip_port_view` varchar(80) NOT NULL default '',
 		  `macip_imb_state` char(2) default '',
 		  `macip_working_now` char(2) default '',
 		  `macip_active` int(2) NOT NULL default '0',
 		  `macip_temp_id` int(10) NOT NULL default '0',
 		  `macip_online` int(2) NOT NULL default '0',
 		  `macip_first_scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `macip_scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `macip_lastchange_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `macip_count_scan` int(11) NOT NULL default '0',
		  `macip_flat` varchar(5) NOT NULL DEFAULT '0',
 		  PRIMARY KEY  (`device_id`,`macip_ipaddr`,`macip_macaddr`,`macip_port_hex`),
 		  KEY `macip_id` (`macip_id`)
 		) ENGINE=InnoDB  ;");
 	}
 
 	
 	if (!in_array('imb_mactrack_recent_ports', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_mactrack_recent_ports","CREATE TABLE `imb_mactrack_recent_ports` (
 		  `row_id` int(10) unsigned NOT NULL auto_increment,
 		  `site_id` int(10) unsigned NOT NULL default '0',
 		  `device_id` int(10) unsigned NOT NULL default '0',
 		  `hostname` varchar(40) NOT NULL default '',
 		  `description` varchar(100) NOT NULL default '',
 		  `vlan_id` varchar(5) NOT NULL default 'N/A',
 		  `vlan_name` varchar(50) NOT NULL default '',
 		  `mac_address` varchar(20) NOT NULL default '',
 		  `ip_address` varchar(20) NOT NULL default '',
 		  `dns_hostname` varchar(200) default '',
 		  `port_number` varchar(10) NOT NULL default '',
 		  `port_name` varchar(50) NOT NULL default '',
 		  `date_last` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
 		  `first_scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `count_rec` int(10) unsigned NOT NULL default '0',
 		  `active_last` int(2) unsigned NOT NULL default '0',
 		  PRIMARY KEY  (`row_id`),
 		  UNIQUE KEY `port_number` (`port_number`,`ip_address`,`mac_address`,`device_id`,`vlan_id`),
 		  KEY `site_id` (`site_id`),
 		  KEY `description` (`description`),
 		  KEY `mac` (`mac_address`),
 		  KEY `hostname` (`hostname`),
 		  KEY `device_id` (`device_id`),
 		  KEY `ip_address` (`ip_address`)
 		) ENGINE=InnoDB   COMMENT='Database for Recent Tracking Device MAC''s';");
 	}	
 	
 	if (!in_array('imb_ports', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_ports","CREATE TABLE `imb_ports` (
 		  `port_id` int(10) unsigned NOT NULL auto_increment,
 		  `device_id` int(11) NOT NULL default '0',
 		  `port_number` int(2) NOT NULL default '0',
 		  `port_name` varchar(50) NOT NULL default '',
 		  `port_imb_state` char(2) default '',
 		  `port_zerroip_state` char(2) default '',
 		  `port_type` int(10) unsigned NOT NULL default '0',
 		  `port_status` char(2) default '',
 		  `port_active` char(2) default '',
 		  `port_online` char(2) default '',
 		  `macip_temp_id` int(10) NOT NULL default '0',
 		  `scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
		  `port_status_last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 		  `count_macip_record` int(11) NOT NULL default '0',
 		  `count_scanmac_record_max` int(11) NOT NULL default '0',
 		  `count_scanmac_record_cur` int(11) NOT NULL default '0',
		  `port_LoopVLAN` varchar(40) NOT NULL DEFAULT '0',
		  `port_unt_vl` varchar(40) NOT NULL DEFAULT '0',
		  `port_t_vl` varchar(150) NOT NULL DEFAULT '0',		  
 		  PRIMARY KEY  (`device_id`,`port_number`),
 		  KEY `port_id` (`port_id`)
 		) ENGINE=InnoDB   ;");
 	}
 	
 	if (!in_array('imb_processes', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_processes","CREATE TABLE `imb_processes` (
 		  `device_id` int(11) NOT NULL default '0',
 		  `process_id` int(10) unsigned default NULL,
 		  `status` varchar(20) NOT NULL default 'Queued',
 		  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  PRIMARY KEY  (`device_id`)
 		) ENGINE=InnoDB   ;");
 	}
 	
 
 	if (!in_array('imb_scanning_functions', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_scanning_functions","CREATE TABLE `imb_scanning_functions` (
 		  `scanning_function` varchar(100) NOT NULL default '',
 		  `description` varchar(200) NOT NULL default '',
 		  PRIMARY KEY  (`scanning_function`)
 		) ENGINE=InnoDB    COMMENT='Registered Scanning Functions';");		

 	}
 	
 	if (!in_array('imb_temp_blmacinfo', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_temp_blmacinfo","CREATE TABLE `imb_temp_blmacinfo` (
 		  `blmacinfo_id` int(10) unsigned NOT NULL auto_increment,
 		  `blmacinfo_info_id` int(10) NOT NULL default '0',
 		  `blmacinfo_cor_portlist` varchar(30) NOT NULL default '',
 		  `blmacinfo_cor_ip` varchar(20) NOT NULL default '',
 		  `blmacinfo_scan_ip` varchar(20) NOT NULL default '',
 		  `blmacinfo_scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `blmacinfo_scan_port` char(3) NOT NULL default '',
 		  PRIMARY KEY  (`blmacinfo_info_id`,`blmacinfo_cor_portlist`,`blmacinfo_cor_ip`),
 		  KEY `blmacinfo_id` (`blmacinfo_id`)
 		) ENGINE=InnoDB  ;");
 	}
 	
 	if (!in_array('imb_temp_blmacs', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_temp_blmacs","CREATE TABLE `imb_temp_blmacs` (
 		  `blmac_id` int(10) unsigned NOT NULL auto_increment,
 		  `device_id` int(11) NOT NULL default '0',
 		  `blmac_index` varchar(40) NOT NULL default '',
 		  `blmac_macaddr` varchar(20) NOT NULL default '',
 		  `blmac_port` varchar(4) NOT NULL default '',
 		  `blmac_type` char(2) default '',
 		  `blmac_vid` varchar(5) NOT NULL default '0',
 		  `blmac_vlanname` varchar(50) NOT NULL default '',
 		  `blmac_scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  PRIMARY KEY  (`device_id`,`blmac_index`),
 		  KEY `blmac_id` (`blmac_id`)
 		) ENGINE=InnoDB  ;");
 	}
 	if (!in_array('imb_temp_macip', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_temp_macip","CREATE TABLE `imb_temp_macip` (
 		  `macip_id` int(10) unsigned NOT NULL auto_increment,
 		  `device_id` int(11) NOT NULL default '0',
 		  `macip_index` varchar(20) NOT NULL default '',
 		  `macip_macaddr` varchar(20) NOT NULL default '',
 		  `macip_ipaddr` varchar(20) NOT NULL default '',
 		  `macip_port_hex` varchar(36) NOT NULL default '',
 		  `macip_port_list` varchar(80) NOT NULL default '',
 		  `macip_port_view` varchar(80) NOT NULL default '',
 		  `macip_imb_state` char(2) default '',
 		  `scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  PRIMARY KEY  (`device_id`,`macip_index`),
 		  KEY `port_id` (`macip_id`)
 		) ENGINE=InnoDB  ;");
 	}
 	
 	if (!in_array('imb_temp_ports', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_temp_ports","CREATE TABLE `imb_temp_ports` (
 		  `port_id` int(10)  unsigned NOT NULL auto_increment,
 		  `device_id` int(11) NOT NULL default '0',
 		  `port_number` int(2) NOT NULL default '0',
 		  `port_name` varchar(50) NOT NULL default '',
 		  `port_imb_state` char(2) default '',
 		  `port_zerroip_state` char(2) default '',
 		  `port_type` int(10) unsigned NOT NULL default '0',
 		  `port_active` char(2) default '',
 		  `scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 		  `count_macip_record` int(11) NOT NULL default '0',
 		  `count_scanmac_record_max` int(11) NOT NULL default '0',
 		  `count_scanmac_record_cur` int(11) NOT NULL default '0',
		`port_LoopVLAN` varchar(40) NOT NULL DEFAULT '0',
		`port_unt_vl` varchar(40) NOT NULL DEFAULT '0',
		`port_t_vl` varchar(150) NOT NULL DEFAULT '0',		  
 		  PRIMARY KEY  (`device_id`,`port_number`),
 		  KEY `port_id` (`port_id`)
 		) ENGINE=InnoDB  ;");
 	}
 	
 	if (!in_array('imb_temp_ports_stat', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_temp_ports_stat","CREATE TABLE `imb_temp_ports_stat` (
 		  `device_id` int(10) NOT NULL default '0',
 		  `port_number` int(4) NOT NULL default '0',
 		  `count_rec` int(11) NOT NULL default '0',
 		  PRIMARY KEY  (`device_id`,`port_number`)
 		) ENGINE=InnoDB  ;");
 	}
 
 	if (!in_array('imb_mactrack_temp_ports', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_mactrack_temp_ports","CREATE TABLE  `imb_mactrack_temp_ports` (
 			  `site_id` int(10) unsigned NOT NULL default '0',
 			  `device_id` int(10) unsigned NOT NULL default '0',
 			  `imb_device_id` int(10) unsigned NOT NULL default '0',
 			  `hostname` varchar(40) NOT NULL default '',
 			  `device_name` varchar(100) NOT NULL default '',
 			  `vlan_id` varchar(5) NOT NULL default 'N/A',
 			  `vlan_name` varchar(50) NOT NULL default '',
 			  `mac_address` varchar(20) NOT NULL default '',
 			  `vendor_mac` varchar(8) default NULL,
 			  `ip_address` varchar(20) NOT NULL default '',
 			  `dns_hostname` varchar(200) default '',
 			  `port_number` varchar(10) NOT NULL default '',
 			  `port_name` varchar(50) NOT NULL default '',
 			  `scan_date` datetime NOT NULL default '0000-00-00 00:00:00',
 			  `updated` tinyint(3) unsigned NOT NULL default '0',
 			  `authorized` tinyint(3) unsigned NOT NULL default '0',
 			  PRIMARY KEY  (`port_number`,`scan_date`,`mac_address`,`device_id`),
 			  KEY `site_id` (`site_id`),
 			  KEY `description` (`device_name`),
 			  KEY `ip_address` (`ip_address`),
 			  KEY `hostname` (`hostname`),
 			  KEY `vlan_name` (`vlan_name`),
 			  KEY `vlan_id` (`vlan_id`),
 			  KEY `device_id` (`device_id`),
 			  KEY `mac` (`mac_address`),
 			  KEY `updated` (`updated`),
 			  KEY `vendor_mac` (`vendor_mac`),
 			  KEY `authorized` (`authorized`)
 			) ENGINE=InnoDB ;");
 		}
 	if (!in_array('imb_cli', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_cli","CREATE TABLE  `imb_cli` (		
			  `cli_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `device_id` int(11) NOT NULL DEFAULT '0',
			  `cli_index` varchar(40) NOT NULL DEFAULT '',
			  `cli_ip` varchar(20) NOT NULL DEFAULT '',
			  `cli_mac` varchar(20) NOT NULL DEFAULT '',
			  `cli_port` varchar(4) NOT NULL DEFAULT '',
			  `cli_type` char(2) DEFAULT '', 
			  `cli_vid` int(5) NOT NULL DEFAULT '0',
			  `cli_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`cli_id`)
			) ENGINE=InnoDB ;");		
		
		}
 	if (!in_array('imb_tabs', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_tabs","CREATE TABLE  `imb_tabs` (		
			  `tab_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `tab_name` varchar(45) NOT NULL DEFAULT '',
			  PRIMARY KEY (`tab_id`)
			) ENGINE=InnoDB ;");		
		}
		$sql[] = array("dimpb_execute_sql","INSERT IGNORE INTO [imb_tabs] data for some tabs","INSERT INTO `imb_tabs` (`tab_id`,`tab_name`) VALUES " .
			" (1,'ALL'), " .
			" (2,'Active'), " .
			" (13,'FTTH'),(14,'Server'),(15,'Doma'),(16,'Cisco'); ");
					



 	if (!in_array('imb_tab_dev', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_tab_dev","CREATE TABLE  `imb_tab_dev` (		
				`row_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`tab_id` int(10) unsigned NOT NULL,
				`dev_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`row_id`),
				UNIQUE KEY `dev_on_tab` (`tab_id`,`dev_id`)
			) ENGINE=InnoDB ;");		
		}

 	if (!in_array('imb_vlans', $tables)) {
 		$sql[] = array("dimpb_create_table","imb_vlans","CREATE TABLE  `imb_vlans` (		
			  `row_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `device_id` int(11) NOT NULL DEFAULT '0',
			  `vlan_name` varchar(40) NOT NULL DEFAULT '',
			  `vlan_id` int(10) NOT NULL,
			  `members_ports` char(40) NOT NULL DEFAULT '',
			  `uttagget_ports` char(40) NOT NULL DEFAULT '',
			  `tagget_ports` char(40) NOT NULL DEFAULT '',
			  `forbidden_ports` char(40) NOT NULL DEFAULT '',
			  `uttagget_ports_list` char(100) NOT NULL DEFAULT '',
			  `vlans_scan_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `vlans_active` int(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`row_id`),
			  UNIQUE KEY `vlan_on_device` (`device_id`,`vlan_id`),
			  KEY `device_id` (`device_id`),
			  KEY `vlan_id` (`vlan_id`)
			) ENGINE=InnoDB COMMENT='vlans';");		
		}		
		
 	switch($old) {
        case '0.0.1b': 
 		  $sql[] = array("dimpb_modify_column","imb_devices","device_id", "ALTER TABLE `imb_devices` MODIFY COLUMN `device_id` INTEGER UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT;");
 		  $old = '0.0.14b';                                                                                                      
        case '0.0.14b': 
 		  $sql[] = array("dimpb_add_column","imb_devices","enable_acl_mode","ALTER TABLE `imb_devices` ADD COLUMN `enable_acl_mode` ENUM('Other','Enable','Disable','not_use') NOT NULL DEFAULT 'not_use'");
 		  $sql[] = array("dimpb_add_column","imb_devices","enable_log_trap","ALTER TABLE `imb_devices` ADD COLUMN `enable_log_trap` ENUM('Other','Enable','Disable','not_use') NOT NULL DEFAULT 'not_use'");
 			$old = '0.0.15b'; 		  
        case '0.0.15b': 
 		  $sql[] = array("dimpb_add_column","imb_macip","macip_mode","ALTER TABLE `imb_macip` ADD COLUMN `macip_mode` enum('ARP','ACL') NOT NULL default 'ARP' after macip_imb_state;");
 		  $sql[] = array("dimpb_add_column","imb_temp_macip","macip_mode","ALTER TABLE `imb_temp_macip` ADD COLUMN `macip_mode` enum('ARP','ACL') NOT NULL default 'ARP' after macip_imb_state;");
 			$old = '0.0.19b'; 		  
 	case '0.0.19b': 
 		$sql[] = array("dimpb_modify_column","imb_temp_ports","port_active", "ALTER TABLE `imb_temp_ports` MODIFY COLUMN `port_active` CHAR(2) NOT NULL DEFAULT 0;");
		if (!impb_db_column_exists('imb_macip','macip_imb_state')) {
			$sql[] = array("dimpb_execute_sql","Change colmun name macip_imb_state => macip_imb_status in table [imb_macip]", "ALTER TABLE `imb_macip` CHANGE COLUMN `macip_imb_state` `macip_imb_status`  TINYINT NOT NULL DEFAULT '-1'");
		}
 		if (!impb_db_column_exists('imb_temp_macip','macip_imb_state')) {
			$sql[] = array("dimpb_execute_sql","Change colmun name macip_imb_state => macip_imb_status in table [imb_temp_macip]", "ALTER TABLE `imb_temp_macip` CHANGE COLUMN `macip_imb_state` `macip_imb_status`  TINYINT NOT NULL DEFAULT '-1'");
		}
 		$sql[] = array("dimpb_add_column","imb_macip","macip_mode","ALTER TABLE `imb_macip` ADD COLUMN `macip_mode` TINYINT NOT NULL DEFAULT '-1' after macip_imb_status;");
 		$sql[] = array("dimpb_add_column","imb_temp_macip","macip_mode","ALTER TABLE `imb_temp_macip` ADD COLUMN `macip_mode` TINYINT NOT NULL DEFAULT '-1' after macip_imb_status;");
 		$sql[] = array("dimpb_add_column","imb_macip","macip_imb_action","ALTER TABLE `imb_macip` ADD COLUMN `macip_imb_action` TINYINT NOT NULL DEFAULT '-1' after macip_imb_status;");
 		$sql[] = array("dimpb_add_column","imb_temp_macip","macip_imb_action","ALTER TABLE `imb_temp_macip` ADD COLUMN `macip_imb_action` TINYINT NOT NULL DEFAULT '-1' after macip_imb_status;");
 		
 		$old = '0.0.192b'; 
     case '0.0.192b': 
 		$sql[] = array("dimpb_modify_column","imb_ports","port_imb_state", "ALTER TABLE `imb_ports` MODIFY COLUMN `port_imb_state` TINYINT(4) DEFAULT '-1';");
 		$sql[] = array("dimpb_modify_column","imb_ports","port_zerroip_state", "ALTER TABLE `imb_ports` MODIFY COLUMN `port_zerroip_state` TINYINT(4) DEFAULT '-1';");
 		$old = '0.0.193b'; 			
     case '0.0.193b': 
 		$sql[] = array("dimpb_add_column","imb_mactrack_recent_ports","vendor_mac","alter table `imb_mactrack_recent_ports` add column `vendor_mac` varchar(8) default NULL after `mac_address`;");
 		$sql[] = array("dimpb_add_column","imb_mactrack_recent_ports","vendor_name","alter table `imb_mactrack_recent_ports` add column `vendor_name`  varchar(100) default '' after `vendor_mac`;");
 		$sql[] = array("dimpb_add_column","imb_mactrack_recent_ports","authorized","alter table `imb_mactrack_recent_ports` add column `authorized` tinyint(3) unsigned NOT NULL default '0' after `date_last`;");
 		$old = '0.0.194b'; 	
 	case '0.0.194b':
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_get_auth_protocol","alter table `imb_devices` add column `snmp_get_auth_protocol` varchar(5) default '' after `snmp_get_password`;");
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_get_priv_passphrase","alter table `imb_devices` add column `snmp_get_priv_passphrase` varchar(200) default '' after `snmp_get_auth_protocol`;");
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_get_priv_protocol","alter table `imb_devices` add column `snmp_get_priv_protocol` varchar(6) default '' after `snmp_get_priv_passphrase`;");
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_get_context","alter table `imb_devices` add column `snmp_get_context` varchar(64) default '' after `snmp_get_priv_protocol`;");
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_set_auth_protocol","alter table `imb_devices` add column `snmp_set_auth_protocol` varchar(5) default '' after `snmp_set_password`;");
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_set_priv_passphrase","alter table `imb_devices` add column `snmp_set_priv_passphrase` varchar(200) default '' after `snmp_set_auth_protocol`;");
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_set_priv_protocol","alter table `imb_devices` add column `snmp_set_priv_protocol` varchar(6) default '' after `snmp_set_priv_passphrase`;");
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_set_context","alter table `imb_devices` add column `snmp_set_context` varchar(64) default '' after `snmp_set_priv_protocol`;");
 		$sql[] = array("dimpb_add_column","imb_devices","snmp_max_oids","alter table `imb_devices` add column `snmp_max_oids` int(12) unsigned default '10' after `snmp_retries`;");
 		$sql[] = array("dimpb_modify_column","imb_ports","port_zerroip_state", "ALTER TABLE `imb_ports` MODIFY COLUMN `port_zerroip_state` TINYINT(4) DEFAULT 0;");
 		$sql[] = array("dimpb_modify_column","imb_temp_ports","port_zerroip_state", "ALTER TABLE `imb_temp_ports` MODIFY COLUMN `port_zerroip_state` TINYINT(4) DEFAULT 0;");
 		$old = '0.0.195b'; 	
     case '0.0.196b': 
 		$sql[] = array("dimpb_delete_index","imb_mactrack_recent_ports",  "port_number", "ALTER TABLE `imb_mactrack_recent_ports`  DROP INDEX `port_number`;");
 		$sql[] = array("dimpb_add_index", "imb_mactrack_recent_ports",  "row_id", "ALTER TABLE `imb_mactrack_recent_ports` ADD UNIQUE INDEX `row_id` USING BTREE(`row_id`);");
 		$sql[] = array("dimpb_execute_sql","Change key for table [imb_mactrack_recent_ports]", "ALTER TABLE `imb_mactrack_recent_ports` DROP PRIMARY KEY,  ADD PRIMARY KEY  USING BTREE(`vlan_id`, `device_id`, `mac_address`, `ip_address`, `port_number`);");
 		$sql[] = array("dimpb_create_table","imb_temp_portname","CREATE TABLE `imb_temp_portname` ( " .
 			"`site_id` int(10) unsigned NOT NULL default '0', " .
 			"`device_id` int(10) unsigned NOT NULL default '0', " .
 			"`port_number` varchar(10) NOT NULL default '', " .
 			"`port_name` varchar(50) NOT NULL default '', " .
 			"PRIMARY KEY  (`site_id`,`device_id`,`port_number`,`port_name`) " .
 			") ENGINE=MyISAM DEFAULT CHARSET=latin1;");
 		$old = '0.0.197b'; 
     case '0.0.197b': 
 		$sql[] = array("dimpb_modify_column","imb_macip","macip_mode","ALTER TABLE `imb_macip` MODIFY COLUMN `macip_mode` tinyint(4) NOT NULL DEFAULT '-1';");
 		$sql[] = array("dimpb_modify_column","imb_temp_macip","macip_mode","ALTER TABLE `imb_temp_macip` MODIFY COLUMN `macip_mode` tinyint(4) NOT NULL DEFAULT '-1';");
 		$sql[] = array("dimpb_modify_column","imb_mactrack_recent_ports","port_name","ALTER TABLE `imb_mactrack_recent_ports` MODIFY COLUMN `port_name` VARCHAR(50)  DEFAULT NULL;");
 		$sql[] = array("dimpb_modify_column","imb_temp_ports","port_id","ALTER TABLE `imb_temp_ports` MODIFY COLUMN `port_id` INTEGER UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT;");
 		$sql[] = array("dimpb_execute_sql","RE-CHECK for Change key for table [imb_mactrack_recent_ports]", "ALTER TABLE `imb_mactrack_recent_ports` DROP PRIMARY KEY,  ADD PRIMARY KEY  USING BTREE(`vlan_id`, `device_id`, `mac_address`, `ip_address`, `port_number`);");
 		$old = '0.0.198b'; 
     case '0.0.198b': 
 		$sql[] = array("dimpb_add_column","imb_macip","macip_banned","ALTER TABLE `imb_macip` ADD COLUMN `macip_banned` TINYINT NOT NULL DEFAULT '0' after macip_imb_status;");
 		$sql[] = array("dimpb_add_column","imb_temp_blmacinfo","blmacinfo_banned","ALTER TABLE `imb_temp_blmacinfo` ADD COLUMN `blmacinfo_banned` TINYINT NOT NULL DEFAULT '0' after blmacinfo_cor_ip;");
 		$old = '0.0.2b'; 
     case '0.0.2b': 
 		$sql[] = array("dimpb_add_column","imb_mactrack_recent_ports","imb_device_id","ALTER TABLE `imb_mactrack_recent_ports` ADD COLUMN `imb_device_id` int(10) unsigned NOT NULL default '0' after `device_id`;");
 		$sql[] = array("dimpb_add_column","imb_macip","macip_active_last_poll","ALTER TABLE `imb_macip` ADD COLUMN `macip_active_last_poll`  tinyint(4) NOT NULL DEFAULT '0';");
 		$old = '0.0.21b'; 
     case '0.0.21b': 
 		$sql[] = array("dimpb_create_table","imb_banip","CREATE TABLE  `imb_banip` ( " .
 			" `banip_id` int(10) unsigned NOT NULL auto_increment, " .
 			" `banip_ipaddr` varchar(20) NOT NULL default '', " .
 			" `banip_aplled` tinyint(4) NOT NULL default '0', " .
 			" `banip_aproved` tinyint(4) NOT NULL default '0', " .
 			" `banip_type` tinyint(4) NOT NULL default '0', " .
 			" `banip_manual` tinyint(4) NOT NULL default '0', " .
 			" `banip_balance` int(10) NOT NULL default '0', " .
 			" `banip_author_id` int(10) unsigned NOT NULL default '0', " .
 			" `banip_install_date` datetime NOT NULL default '0000-00-00 00:00:00', " .
 			" `banip_expiration_date` datetime NOT NULL default '0000-00-00 00:00:00', " .
 			" `banip_delete` tinyint(4) NOT NULL default '0', " .
 			" `banip_counts` tinyint(4) NOT NULL default '0', " .
 			" `banip_mac_active_last_poll` tinyint(4) NOT NULL default '0', " .
 			" `banip_message` varchar(255) default '', " .
 			" `banip_active` tinyint(4) NOT NULL default '0', " .
 			" PRIMARY KEY  (`banip_ipaddr`), " .
 			" KEY `banip_id` (`banip_id`) " .
 			" ) ENGINE=MyISAM;");
 		$old = '0.0.25b'; 
    case '0.0.25b': 
 		$sql[] = array("dimpb_add_index", "imb_macip",  "ip_on_device", "ALTER TABLE `imb_macip` ADD UNIQUE KEY `ip_on_device` (`device_id`,`macip_ipaddr`);");
 		$sql[] = array("dimpb_add_index", "imb_macip",  "macip_ipaddr", "ALTER TABLE `imb_macip` ADD INDEX `macip_ipaddr`(`macip_ipaddr`);");
 		$old = '0.0.26b';	
     case '0.0.26b': 
 		$sql[] = array("dimpb_create_table","imb_traps_blocked","CREATE TABLE  `imb_traps_blocked` ( " .
 			"`traps_id` int(10) unsigned NOT NULL auto_increment, " .
 			"`traps_hostname` varchar(20) NOT NULL default '', " .
 			"`traps_macaddr` varchar(20) NOT NULL default '', " .
 			"`traps_ipaddr` varchar(20) NOT NULL default '', " .
 			"`traps_port` int(2) NOT NULL default '0', " .
 			"`traps_time` datetime default NULL, " .
 			"PRIMARY KEY  (`traps_id`), " .
 			"UNIQUE KEY `ip_mac_on_device` (`traps_hostname`,`traps_macaddr`,`traps_port`) " .
 			") ENGINE=MyISAM;");
 		$sql[] = array("dimpb_add_column","imb_blmacs","blmac_blocked_ip","alter table `imb_blmacs` add column `blmac_blocked_ip` varchar(20) NOT NULL default '' after `blmac_count_scan`;");
 		$old = '0.0.27b';	
 	case '0.0.27b': 
 		$sql[] = array("dimpb_add_column","imb_traps_blocked","traps_device_id","alter table `imb_traps_blocked` add column `traps_device_id` int(10) unsigned NOT NULL default '0';");				
 		$old = '0.0.28b';	
 	case '0.0.28b': 
 		$sql[] = array("dimpb_add_column","imb_temp_ports","port_max_entry","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_max_entry` INTEGER(2) UNSIGNED NOT NULL DEFAULT '0' ;");		
 		$sql[] = array("dimpb_add_column","imb_ports","port_max_entry","ALTER TABLE `imb_ports` ADD COLUMN `port_max_entry` INTEGER(2) UNSIGNED NOT NULL DEFAULT '0' ;");		
 		$sql[] = array("dimpb_add_column","imb_temp_ports","port_enab_dhcp_fwd","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_enab_dhcp_fwd` INTEGER(2) UNSIGNED NOT NULL DEFAULT '0' AFTER `port_zerroip_state`;");		
 		$sql[] = array("dimpb_add_column","imb_ports","port_enab_dhcp_fwd","ALTER TABLE `imb_ports` ADD COLUMN `port_enab_dhcp_fwd` INTEGER(2) UNSIGNED NOT NULL DEFAULT '0' AFTER `port_zerroip_state`;");		
 		$sql[] = array("dimpb_add_column","imb_devices","enable_dhcp_forw","ALTER TABLE `imb_devices` ADD COLUMN `enable_dhcp_forw` enum('Other','Enable','Disable','not_use') NOT NULL default 'not_use' after `enable_log_trap`;");		
 		$old = '0.1.01b';	
 	case '0.1.01b': 
 		$sql[] = array("dimpb_add_index", "imb_mactrack_recent_ports",  "device_port", "ALTER TABLE `imb_mactrack_recent_ports` ADD INDEX `device_port`(`device_id`, `port_number`);");

 		
 		$old = '0.1.02b';	
 	case '0.1.02b': 
 		$sql[] = array("dimpb_create_table","imb_auto_updated_nets","CREATE TABLE  `imb_auto_updated_nets` ( " .
 			"`net_id` int(10) unsigned NOT NULL auto_increment, " .
 			"`net_ipaddr` int  UNSIGNED  NOT NULL default 0, " .
 			"`net_mask`  int  UNSIGNED  NOT NULL default 0, " .
 			"`net_type` tinyint(1) NOT NULL default '0', " .
			"`net_ttl` tinyint(2) NOT NULL default '0', " .
			"`net_device_id` int(10) unsigned NOT NULL default '0', " .
			"`net_description` varchar(255) NOT NULL default '', " .
 			"`net_trigger_count` int(10) NOT NULL default '0', " .
 			"`net_change_time` datetime default NULL, " .
			"`net_change_user` int(10) unsigned NOT NULL , " .
 			"PRIMARY KEY  (`net_id`) " .
 			") ENGINE=MyISAM;");
 		
		$old = '0.1.3b';
 	case '0.1.3b': 
 		$sql[] = array("dimpb_add_column","imb_auto_updated_nets","net_ttl",  "ALTER TABLE `imb_auto_updated_nets` ADD COLUMN `net_ttl`   tinyint(2) NOT NULL default '0';");			
 		$old = '0.1.47b';
     case '0.1.47b': 
 		$sql[] = array("dimpb_add_column","imb_macip","macip_may_move","ALTER TABLE `imb_macip` ADD COLUMN `macip_may_move`  tinyint(1) NOT NULL DEFAULT '0';");
 		$old = '0.1.6'; 
     case '0.1.6': 
 		$sql[] = array("dimpb_add_column","imb_blmacs","blmac_done","alter table `imb_blmacs` add column `blmac_done` tinyint(1) NOT NULL default '0';");
		$sql[] = array("dimpb_add_column","imb_blmacs","blmac_info","alter table `imb_blmacs` add column `blmac_info` varchar(60) default '';");
 		$old = '0.1.8'; 
     case '0.1.8': 
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_arp_inspection","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_arp_inspection` tinyint(1) DEFAULT '0' after port_zerroip_state ;");
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_ip_inspection","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_ip_inspection` tinyint(1) DEFAULT '0' after port_arp_inspection ;");
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_ip_protocol","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_ip_protocol` tinyint(1) DEFAULT '0' after port_ip_inspection ;");
		$sql[] = array("dimpb_add_column","imb_ports","port_arp_inspection","ALTER TABLE `imb_ports` ADD COLUMN `port_arp_inspection` tinyint(1) DEFAULT '0' after port_zerroip_state ;");
		$sql[] = array("dimpb_add_column","imb_ports","port_ip_inspection","ALTER TABLE `imb_ports` ADD COLUMN `port_ip_inspection` tinyint(1) DEFAULT '0' after port_arp_inspection ;");
		$sql[] = array("dimpb_add_column","imb_ports","port_ip_protocol","ALTER TABLE `imb_ports` ADD COLUMN `port_ip_protocol` tinyint(1) DEFAULT '0' after port_ip_inspection ;");
		$old = '0.1.9'; 		
	case '0.1.9':
		$sql[] = array("dimpb_modify_column","imb_macip","macip_index","ALTER TABLE `imb_macip` MODIFY COLUMN `macip_index` VARCHAR(40) ;");
		$sql[] = array("dimpb_modify_column","imb_temp_macip","macip_index","ALTER TABLE `imb_temp_macip` MODIFY COLUMN `macip_index` VARCHAR(40) ;");
		$sql[] = array("dimpb_add_column","imb_temp_blmacs","blmac_ip","alter table `imb_temp_blmacs` add column `blmac_ip` varchar(20) NOT NULL default '' after blmac_macaddr;");
		$old = '0.2.0'; 		
	case '0.2.0':
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_status_last_change","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_status_last_change` datetime NOT NULL default '0000-00-00 00:00:00' after `scan_date`;");
		$sql[] = array("dimpb_add_column","imb_ports","port_status_last_change","ALTER TABLE `imb_ports` ADD COLUMN `port_status_last_change` datetime NOT NULL default '0000-00-00 00:00:00' after `scan_date`;");
		$old = '0.2.1'; 		
	case '0.2.1':
		$sql[] = array("dimpb_modify_column","imb_auto_updated_nets","net_ttl","ALTER TABLE `imb_auto_updated_nets` MODIFY COLUMN `net_ttl` INT(10) UNSIGNED NOT NULL DEFAULT 0;");
		$old = '0.2.2'; 		
	case '0.2.2':
		$sql[] = array("dimpb_add_column","imb_devices","order_id","ALTER TABLE `imb_devices` ADD COLUMN `order_id` tinyint(3) DEFAULT '0';");	
		$sql[] = array("dimpb_add_column","imb_devices","color_row","ALTER TABLE `imb_devices` ADD COLUMN `color_row` INT(10) DEFAULT '0';");	
		$old = '0.3'; 		
	case '0.3':
		#INTEGER  { other ( 1 ) , disabled ( 2 ) , enabled ( 3 ) } 
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_adm_state","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_adm_state` tinyint(1) NOT NULL DEFAULT '0';");
		$sql[] = array("dimpb_add_column","imb_ports","port_adm_state","ALTER TABLE `imb_ports` ADD COLUMN `port_adm_state` tinyint(1) NOT NULL DEFAULT '0';");
		#.1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4    INTEGER  { auto ( 1 ) , half-10Mbps ( 2 ) , full-10Mbps ( 3 ) , half-100Mbps ( 4 ) , full-100Mbps ( 5 ) , full-1Gigabps ( 7 ) } 
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_adm_speed","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_adm_speed` tinyint(2) NOT NULL DEFAULT '0';");
		$sql[] = array("dimpb_add_column","imb_ports","port_adm_speed","ALTER TABLE `imb_ports` ADD COLUMN `port_adm_speed` tinyint(2) NOT NULL DEFAULT '0';");
		#.1.3.6.1.4.1.171.11.113.1.5.2.2.1.1.5    INTEGER  { nway-auto ( 1 ) , nway-disabled-10Mbps-Half ( 2 ) , nway-disabled-10Mbps-Full ( 3 ) , nway-disabled-100Mbps-Half ( 4 ) , nway-disabled-100Mbps-Full ( 5 ) , nway-disabled-1Gigabps-Full ( 7 ) , nway-disabled-1Gigabps-Full-Master ( 8 ) , nway-disabled-1Gigabps-Full-Slave ( 9 ) } 
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_speed","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_speed` tinyint(2) NOT NULL DEFAULT '0';");
		$sql[] = array("dimpb_add_column","imb_ports","port_speed","ALTER TABLE `imb_ports` ADD COLUMN `port_speed` tinyint(2) NOT NULL DEFAULT '0';");
		#.1.3.6.1.4.1.171.11.113.1.4.2.21.2.1.1.2  INTEGER  { enabled ( 1 ) , disabled ( 2 ) } 
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_adm_LoopPortState","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_adm_LoopPortState` tinyint(1) NOT NULL DEFAULT '0';");
		$sql[] = array("dimpb_add_column","imb_ports","port_adm_LoopPortState","ALTER TABLE `imb_ports` ADD COLUMN `port_adm_LoopPortState` tinyint(1) NOT NULL DEFAULT '0';");
		#swL2PortErrPortReason   .1.3.6.1.4.1.171.11.113.1.4.2.2.3.1.4 INTEGER  { none ( 0 ) , stp-lbd ( 1 ) , storm-control ( 2 ) , storm-control-lbd ( 3 ) , loopdetect ( 4 ) } 
		$sql[] = array("dimpb_add_column","imb_temp_ports","port_ErrPortReason","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_ErrPortReason` tinyint(1) NOT NULL DEFAULT '0';");
		$sql[] = array("dimpb_add_column","imb_ports","port_ErrPortReason","ALTER TABLE `imb_ports` ADD COLUMN `port_ErrPortReason` tinyint(1) NOT NULL DEFAULT '0';");

		$sql[] = array("dimpb_add_column","imb_temp_ports","port_LoopVLAN","ALTER TABLE `imb_temp_ports` ADD COLUMN `port_LoopVLAN` VARCHAR(40) NOT NULL DEFAULT '0';");
		$sql[] = array("dimpb_add_column","imb_ports","port_LoopVLAN","ALTER TABLE `imb_ports` ADD COLUMN `port_LoopVLAN` VARCHAR(40) NOT NULL DEFAULT '0';");
		$old = '0.5'; 
 	}		

	
 
 	if (!empty($sql)) {
 		for ($a = 0; $a < count($sql); $a++) {
 			$step_sql = $sql[$a];
 			$rezult = "";
 			switch ($step_sql[0]) {
 				case 'dimpb_execute_sql':
 					$rezult = dimpb_execute_sql ($step_sql[1], $step_sql[2]);
 					break;
 				case 'dimpb_create_table':
 					$rezult = dimpb_create_table ($step_sql[1], $step_sql[2]);
 					break;
 				case 'dimpb_add_column':
 					$rezult = dimpb_add_column ($step_sql[1], $step_sql[2],$step_sql[3]);
 					break;				
 				case 'dimpb_modify_column':
 					$rezult = dimpb_modify_column ($step_sql[1], $step_sql[2],$step_sql[3]);
 					break;
 				case 'dimpb_delete_column':
 					$rezult = dimpb_delete_column ($step_sql[1], $step_sql[2],$step_sql[3]);
 					break;
 				case 'dimpb_add_index':
 					$rezult = dimpb_add_index ($step_sql[1], $step_sql[2],$step_sql[3]);
 					break;
 				case 'dimpb_delete_index':
 					$rezult = dimpb_delete_index ($step_sql[1], $step_sql[2],$step_sql[3]);
 					break;
 			}
 			imp_raise_message3(array("device_descr" => "Обновление до версии [" . $new . "]" , "type" => "update_db", "object"=> "update","cellpading" => false, "message" => $rezult["message"], "step_rezult" => $rezult["step_rezult"], "step_data" => $rezult["step_data"]));     
 			//$result = db_execute($sql[$a]);
 			//imp_raise_message3(array("device_descr" => "Обновление до версии [" . $new . "]" , "type" => "title_count", "object"=> "update","cellpading" => false, "message" => $sql[$a], "count_rez" => ($result == 1) ));     
 		}
 	}
 
 db_execute('REPLACE INTO settings (name, value) VALUES ("impb_version", "' .  $new . '")');
 }
 
 function dimpb_execute_sql($message, $syntax) {
 	$result = db_execute($syntax);
 	$return_rezult = array();
 	
 	if ($result) {
 		$return_rezult["message"] =  "SUCCESS: Execute SQL,   $message";
 		$return_rezult["step_rezult"] = "OK";
 	}else{
 		$return_rezult["message"] =  "ERROR: Execute SQL,   $message";
 		$return_rezult["step_rezult"] = "Error";
 	}
 	$return_rezult["step_data"] = $return_rezult["step_rezult"] ;
 	return $return_rezult;
 }
 
 function dimpb_create_table($table, $syntax) {
 	$tables = db_fetch_assoc("SHOW TABLES LIKE '$table'");
 	$return_rezult = array();
 
 	if (!sizeof($tables)) {
 		$result = db_execute($syntax);
 		if ($result) {
 			$return_rezult["message"] =  "SUCCESS: Create Table,  Table -> $table";
 			$return_rezult["step_rezult"] = "OK";
 		}else{
 			$return_rezult["message"] =  "ERROR: Create Table,  Table -> $table";
 			$return_rezult["step_rezult"] = "Error";
 		}
 		$return_rezult["step_data"] = $return_rezult["step_rezult"] ;
 	}else{
 		$return_rezult["message"] =  "SUCCESS: Create Table,  Table -> $table";
 		$return_rezult["step_rezult"] = "OK";
 		$return_rezult["step_data"] = "Already Exists";
 	}
 	return $return_rezult;
 }
 
 function dimpb_add_column($table, $column, $syntax) {
 	$return_rezult = array();
 	$columns = db_fetch_assoc("SHOW COLUMNS FROM $table LIKE '$column'");
 
 	if (sizeof($columns)) {
 		$return_rezult["message"] = "SUCCESS: Add Column,    Table -> $table, Column -> $column";
 		$return_rezult["step_rezult"] = "OK";
 		$return_rezult["step_data"] = "Already Exists";
 	}else{
 		$result = db_execute($syntax);
 
 		if ($result) {
 			$return_rezult["message"] ="SUCCESS: Add Column,    Table -> $table, Column -> $column";
 			$return_rezult["step_rezult"] = "OK";
 		}else{
 			$return_rezult["message"] ="ERROR: Add Column,    Table -> $table, Column -> $column";
 			$return_rezult["step_rezult"] = "Error";
 		}
 		$return_rezult["step_data"] = $return_rezult["step_rezult"] ;
 	}
 	return $return_rezult;
 }
 
 function dimpb_add_index($table, $index, $syntax) {
 	$tables = db_fetch_assoc("SHOW TABLES LIKE '$table'");
 	$return_rezult = array();
 
 	if (sizeof($tables)) {
 		$indexes = db_fetch_assoc("SHOW INDEXES FROM $table");
 
 		$index_exists = FALSE;
 		if (sizeof($indexes)) {
 			foreach($indexes as $index_array) {
 				if ($index == $index_array["Key_name"]) {
 					$index_exists = TRUE;
 					break;
 				}
 			}
 		}
 
 		if ($index_exists) {
 			$return_rezult["message"] =  "SUCCESS: Add Index,     Table -> $table, Index -> $index";
 			$return_rezult["step_rezult"] = "OK";
 			$return_rezult["step_data"] = "Already Exists";
 		}else{
 			$result = db_execute($syntax);
 
 			if ($result) {
 				$return_rezult["message"] =  "SUCCESS: Add Index,     Table -> $table, Index -> $index";
 				$return_rezult["step_rezult"] = "OK";
 			}else{
 				$return_rezult["message"] =  "ERROR: Add Index,     Table -> $table, Index -> $index";
 				$return_rezult["step_rezult"] = "Error";
 			}
 			$return_rezult["step_data"] = $return_rezult["step_rezult"] ;
 		}
 	}else{
 		$return_rezult["message"] ="ERROR: Add Index,     Table -> $table, Index -> $index";
 		$return_rezult["step_rezult"] = "Error";
 		$return_rezult["step_data"] = "Table Does NOT Exist";
 	}
 	return $return_rezult;
 }
 
 function dimpb_modify_column($table, $column, $syntax) {
 	$tables = db_fetch_assoc("SHOW TABLES LIKE '$table'");
 	$return_rezult = array();
 
 	if (sizeof($tables)) {
 		$columns = db_fetch_assoc("SHOW COLUMNS FROM $table LIKE '$column'");
 
 		if (sizeof($columns)) {
 			$result = db_execute($syntax);
 
 			if ($result) {
 				$return_rezult["message"] =  "SUCCESS: Modify Column, Table -> $table, Column -> $column";
 				$return_rezult["step_rezult"] = "OK";
 			}else{
 				$return_rezult["message"] =  "ERROR: Modify Column, Table -> $table, Column -> $column";
 				$return_rezult["step_rezult"] = "Error";
 			}
 			$return_rezult["step_data"] = $return_rezult["step_rezult"] ;
 		}else{
 			$return_rezult["message"] =  "ERROR: Modify Column, Table -> $table, Column -> $column";
 			$return_rezult["step_rezult"] = "Error";
 			$return_rezult["step_data"] = "Column Does NOT Exist";
 		}
 	}else{
 		$return_rezult["message"] =  "ERROR: Modify Column, Table -> $table, Column -> $column";
 		$return_rezult["step_rezult"] = "Error";
 		$return_rezult["step_data"] = "Table Does NOT Exist";
 	}
 	return $return_rezult;
 }
 
 function dimpb_delete_column($table, $column, $syntax) {
 	$tables = db_fetch_assoc("SHOW TABLES LIKE '$table'");
 	$return_rezult = array();
 
 	if (sizeof($tables)) {
 		$columns = db_fetch_assoc("SHOW COLUMNS FROM $table LIKE '$column'");
 
 		if (sizeof($columns)) {
 			$result = db_execute($syntax);
 
 			if ($result) {
 				$return_rezult["message"] =  "SUCCESS: Delete Column, Table -> $table, Column -> $column";
 				$return_rezult["step_rezult"] = "OK";
 			}else{
 				$return_rezult["message"] =  "ERROR: Delete Column, Table -> $table, Column -> $column";
 				$return_rezult["step_rezult"] = "Error";
 			}
 			$return_rezult["step_data"] = $return_rezult["step_rezult"] ;
 		}else{
 			$return_rezult["message"] =  "SUCCESS: Delete Column, Table -> $table, Column -> $column";
 			$return_rezult["step_rezult"] = "Error";
 			$return_rezult["step_data"] = "Column Does NOT Exist";			
 		}
 	}else{
 		$return_rezult["message"] =  "SUCCESS: Delete Column, Table -> $table, Column -> $column";
 		$return_rezult["step_rezult"] = "Error";
 		$return_rezult["step_data"] = "Table Does NOT Exist";
 	}
 	return $return_rezult;
 }
 
 function dimpb_delete_index($table, $index, $syntax) {
 	$tables = db_fetch_assoc("SHOW TABLES LIKE '$table'");
 	$return_rezult = array();
 
 	if (sizeof($tables)) {
 		$indexes = db_fetch_assoc("SHOW INDEXES FROM $table");
 
 		$index_exists = FALSE;
 		if (sizeof($indexes)) {
 			foreach($indexes as $index_array) {
 				if ($index == $index_array["Key_name"]) {
 					$index_exists = TRUE;
 					break;
 				}
 			}
 		}
 
 		if (!$index_exists) {
 			$return_rezult["message"] =  "SUCCESS: Delete Index,     Table -> $table, Index -> $index";
 			$return_rezult["step_rezult"] = "OK";
 			$return_rezult["step_data"] = "Index Does NOT Exist!";
 		}else{
 			$result = db_execute($syntax);
 
 			if ($result) {
 				$return_rezult["message"] =  "SUCCESS: Delete Index,     Table -> $table, Index -> $index";
 				$return_rezult["step_rezult"] = "OK";
 			}else{
 				$return_rezult["message"] =  "ERROR: Delete Index,     Table -> $table, Index -> $index";
 				$return_rezult["step_rezult"] = "Error";
 			}
 			$return_rezult["step_data"] = $return_rezult["step_rezult"] ;
 		}
 	}else{
 		$return_rezult["message"] ="ERROR: Delete Index,     Table -> $table, Index -> $index";
 		$return_rezult["step_rezult"] = "Error";
 		$return_rezult["step_data"] = "Table Does NOT Exist";
 	}
 	return $return_rezult;
 }
 	
function impb_db_table_exists($table) {
	return sizeof(db_fetch_assoc("SHOW TABLES LIKE '$table'"));
}

function impb_db_column_exists($table, $column) {
	$found = false;

	if (impb_db_table_exists($table)) {
		$columns  = db_fetch_assoc("SHOW COLUMNS FROM $table");
		if (cacti_sizeof($columns)) {
			foreach($columns as $row) {
				if ($row['Field'] == $column) {
					$found = true;
					break;
				}
			}
		}
	}

	return $found;
}
 	
 	?>
