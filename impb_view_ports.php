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
 
 $guest_account = true;
 
 chdir('../../');
 include("./include/auth.php");
 
 include_once($config["base_path"] . "/plugins/impb/lib/impb_functions.php");
 ini_set('memory_limit', '256M');

 $title = __('IMPB - Interfaces View');
 
 //***********************************************************

 	
 $port_actions = array(
 	1 => "Enable Ip-Mac",
 	2 => "Disable Ip-Mac",
 	3 => "Изменить описание порта",
 	4 => "Включить порт",
 	5 => "Отключить порт",
 	);
 
/* check actions */
switch (get_request_var('action')) {
	case 'actions_':
		form_actions_ports();

		break;
	default:
		impb_redirect();
		general_header();
		impb_view_ports();
		bottom_footer();
		break;
}




function impb_view_get_ports_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
     /* create SQL where clause */
 
     $sql_where = "";
 
     if (get_request_var('device_id') != "-1") {
         $sql_where .= (strlen($sql_where) ? ' AND ' : 'WHERE ') . ' (imb_ports.device_id=' . get_request_var('device_id') . ')';
     }

     if (get_request_var('status') != "-1") {
         $sql_where .= (strlen($sql_where) ? ' AND ' : 'WHERE ') . ' (imb_ports.port_imb_state=' . get_request_var('status') . ')';
     }
	 
     if (get_request_var('port_number') != "") {
         $sql_where .= (strlen($sql_where) ? ' AND ' : 'WHERE ') . ' (imb_ports.port_number=' . get_request_var('port_number') . ')';
     }

	 	/* form the 'where' clause for our main sql query */
	if (get_request_var('filter') != '') {
		$sql_where .= ($sql_where != '' ? ' AND ':'WHERE ') . "(imb_ports.port_name LIKE '%" . get_request_var('filter') . "%' OR " .
			"imb_devices.hostname LIKE '%" . get_request_var('filter') . "%' OR " .
			"imb_devices.description LIKE '%" . get_request_var('filter') . "%')";
	}
	
	$sql_order = get_order_string();
	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}	 
 
	$sql_query = "SELECT
            imb_ports.port_id,
            imb_ports.device_id,
            imb_ports.port_number,
            imb_ports.port_name,
			imb_ports.port_adm_state,
			imb_ports.port_adm_speed,
			imb_ports.port_speed,
			imb_ports.port_adm_LoopPortState,
			imb_ports.port_LoopVLAN,
			imb_ports.port_ErrPortReason,
            imb_ports.port_imb_state,
 			imb_ports.port_zerroip_state,
			imb_ports.port_arp_inspection,
			imb_ports.port_ip_inspection,
			imb_ports.port_ip_protocol,
            imb_ports.port_type,
            imb_ports.port_status,
 			imb_ports.port_online,
            imb_ports.scan_date,
			imb_ports.port_status_last_change,
            imb_ports.count_macip_record,
            imb_ports.count_scanmac_record_max,
            imb_ports.count_scanmac_record_cur,
            imb_devices.hostname,
			imb_device_types.type_revision,
            imb_devices.description,
			h.id as cid,
 			imb_device_types.type_imb_MacBindingPortState, imb_device_types.type_imb_zerrostate_mode,imb_device_types.snmp_oid_MacBindingPortState, imb_device_types.snmp_oid_en_swIpMacBindingPortARPInspection
            FROM (imb_ports  
			LEFT JOIN imb_devices on imb_ports.device_id=imb_devices.device_id )  
 			LEFT JOIN imb_device_types ON imb_devices.device_type_id = imb_device_types.device_type_id
			LEFT JOIN host h ON imb_devices.hostname = h.hostname
            $sql_where
            Group by imb_ports.device_id, imb_ports.port_number
			$sql_order
			$sql_limit";

         
         
	 return db_fetch_assoc($sql_query);
 }
 


 
function impb_port_request_validation() {
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
			'default' => 'port_number',
			'options' => array('options' => 'sanitize_search_string')
			),
		'sort_direction' => array(
			'filter' => FILTER_CALLBACK,
			'default' => 'ASC',
			'options' => array('options' => 'sanitize_search_string')
			),
		'device_id' => array(
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
		'port_number' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '',
			'pageset' => true
			),			
	);

	validate_store_request_vars($filters, 'sess_impb_ports');
	/* ================= input validation ================= */
}
 
 
function impb_view_ports() {
     global $title, $config, $port_actions, $imp_port_state_color;
 
	impb_port_request_validation();
	
	if (get_request_var('rows') == -1) {
		$rows = read_config_option('num_rows_table');
	}elseif (get_request_var('rows') == -2) {
		$rows = 999999;
	}else{
		$rows = get_request_var('rows');
	}
	
	$webroot = $config['url_path'] . 'plugins/impb/';
     //impb_view_header();
 
	impb_tabs();
	html_start_box($title, '100%', '', '3', 'center', '');
	impb_ports_filter();
	html_end_box();     
 
    $sql_where = "";
    $ports = impb_view_get_ports_records($sql_where, true, $rows);
    $total_rows = db_fetch_cell("SELECT
		COUNT(imb_ports.port_number)
        FROM imb_ports
        $sql_where");
	
	$nav = html_nav_bar('impb_view_ports.php?report=ports', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 14, __('Ports'), 'page', 'main');

	form_start('impb_view_ports.php', 'chk');
	
	//print $nav;

	html_start_box('', '100%', '', '3', 'center', '');
	
	
	$display_text = array(
		'device_name'      => array(__('Описание'), 'ASC'),
		'hostname'        => array(__('IP(имя)'), 'ASC'),
		'port_number'      => array(__('Номер<br>порта'), 'ASC'),
		'port_name'      => array(__('Имя<br>порта'), 'ASC'),
		''      => array(__('Режим<br>привязки'), 'ASC'),
		'ips_total'        => array(__('Bindings'), 'DESC'),
		'count_scanmac_record_max'      => array(__('IP-MAC scans'), 'DESC'),
		'port_adm_state'     => array(__('Type'), 'DESC'),
		'port_speed'     => array(__('Status'), 'DESC'),
		'port_status_last_change'      => array(__('Время последнего<br>изменения'), 'DESC'));

	html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false);


         $i = 0;
         $port_imp_state = '';
         $port_imp_state_color = '';
         if (sizeof($ports) > 0) {
             foreach ($ports as $port) {
			
                switch ($port["port_type"]) {
                     case "6":
                         $port_imp_type = 'Eth';
                         break;
                     case "117":
                         $port_imp_type = 'GEth';
                         break;
                     default:
                         $port_imp_type = $port["port_type"];
                         break;
                }                

                switch ($port["port_adm_state"]) {
                     case "0":
                         break;
					case "3":
                         $port_imp_type = $port_imp_type . ",<span style='color: #00BD27;'>UP</span>";
                         break;
                     case "2":
                         $port_imp_type = $port_imp_type . ",<span style='color: #FF0000;'>DWN</span>";
                         break;
                     default:
                         $port_imp_type = $port_imp_type . "," . $port["port_adm_state"];
                         break;
                }
				
				
                switch ($port["port_adm_LoopPortState"]) {
                     case "0":
                         break;
					case "1":
                         $port_imp_type = $port_imp_type . ",<span style='color: #00BD27;'>Lp</span>";
                         break;
                     case "2":
                         $port_imp_type = $port_imp_type . ",<span style='color: #FF0000;'>Lp</span>";
                         break;
                }				
				
				
                $port_imb_active = dimpb_format_port_status($port);
				$port_imp_active_color = '#00BD27';
				if ($port["port_status"] == "0") {
					$port_imp_active_color = '#FF0000';
				}

				
				if ($port["port_LoopVLAN"] == "0") {
					form_alternate_row('line' . $port["port_id"], true);
				}else{
					impb_form_alternate_row_color("#EDA9A9", "#EDA9A9", $i, 'line' . $port["port_id"], "background-color:#EDA9A9"); $i++;
					$port_imb_active = $port_imb_active . " <span style='color: #FF0000;'>LOOP " . $port["port_LoopVLAN"] . "</span>";
				};				

 			/*<a href='<?php print htmlspecialchars($webroot . 'mactrack_devices.php?action=edit&device_id=' . $device['device_id']);?>' title='<?php print __('Edit Device');?>'><img border='0' src='<?php print $webroot;?>images/edit_object.png'></a>
			form_selectable_cell("<a class='linkEditMain' href='impb_devices.php?header=true&action=edit&device_id=" . $port["device_id"] . "'>" . */
			
			
			form_selectable_cell("<a class='linkEditMain' href='" . htmlspecialchars($webroot . 'impb_devices.php?action=edit&device_id=' . $port["device_id"]) . "'>'" . 
 				(strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port["description"]) : $port["description"]) . "</strong></a>", $port["port_id"]);			
 			form_selectable_cell($port["hostname"], $port["port_id"] );
 			//form_selectable_cell(number_format($port["port_number"]), $port["port_id"] );

 			form_selectable_cell(number_format($port["port_number"]) . " <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_ion_view.php?action=preview&style=&host_id=" . $port['cid'] . "&snmp_index=" . $port['port_number'] . "&graph_template_id=-1&rfilter=") . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' alt='' title='View Graph' align='absmiddle'></a>", $port["port_id"]);
			form_selectable_cell($port["port_name"], $port["port_id"] );
			form_selectable_cell(impb_convert_port_state_2_html($port), $port["port_id"]);
			//form_selectable_cell("<strong> <span style='color: $port_imp_state_color;'>$port_imp_state</span></strong>", $port["port_id"] );
 			
			
			//form_selectable_cell(imp_convert_port_zerroip_state_2str_full($port["port_zerroip_state"], $port["device_id"]), $port["port_id"]  );
 			//form_selectable_cell("<strong> <span style='color: $port_imp_zerroip_state_color;'>$port_imp_zerroip_state</span></strong>", $port["port_id"] );
 
 			form_selectable_cell("<a class='linkEditMain' href='impb_view_bindings.php?device_id=+" . $port["device_id"] . "&ip_filter_type_id=1&ip_filter=&mac_filter_type_id=1&mac_filter=&port_filter_type_id=2&port_filter=" . $port["port_number"] . "&rows=-1&filter=&page=1&report=bindings&x=22&y=4'>" . $port["count_macip_record"]  , $port["port_id"]);			
 			
 			form_selectable_cell(number_format($port["count_scanmac_record_max"]), $port["port_id"] );
 			form_selectable_cell("<strong>" . $port_imp_type . "</strong>", $port["port_id"] );
 			form_selectable_cell("<strong> <span style='color: $port_imp_active_color;'>$port_imb_active</span></strong>", $port["port_id"] );
 			form_selectable_cell($port["port_status_last_change"], $port["port_id"] );
 			
 			if ($port["port_online"] == 1) {
 				form_checkbox_cell($port["port_name"], $port["port_id"]);
 			} else {
 				print "<td></td>";
 			}
 			form_end_row();
             }
 
             /* put the nav bar on the bottom as well */
             print $nav;
         }else{
             print "<tr><td><em>No IMPB Ports found</em></td></tr>";
         }

	html_end_box(false);

	if (sizeof($ports)) {
		print $nav;
	}

    impb_draw_actions_dropdown($port_actions, "");
	form_end();
 }

 
 function form_actions_ports() {
	 global $config, $port_actions;
 
 	/* ================= input validation ================= */
	get_filter_request_var('drp_action');
	/* ==================================================== */

    
	/* if we are to save this form, instead of display it */
 	if (isset_request_var('selected_items')) {
 		$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
 		$str_ids = '';
		for ($i=0;($i<count($selected_items));$i++) {
			 /* ================= input validation ================= */
			 input_validate_input_number($selected_items[$i]);
			 /* ==================================================== */			
			$str_ids = $str_ids . "'" . $selected_items[$i] . "', ";
		}
		$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
		$device_ids=db_fetch_assoc("SELECT device_id FROM imb_ports where port_id in (" . $str_ids . ") group by device_id;");
		$ports=db_fetch_assoc("SELECT * FROM imb_ports where port_id in (" . $str_ids . ") ;");
		$port_devices=dimpb_array_rekey(db_fetch_assoc("SELECT `d`.`description` as dev_name , d.*, dt.* FROM imb_ports p LEFT JOIN imb_devices d on (p.device_id=d.device_id) LEFT JOIN imb_device_types dt on (d.device_type_id = dt.device_type_id) WHERE `p`.`port_id` in (" . $str_ids . ") GROUP by p.device_id;"), "device_id");
		$use_strict_mode = isset_request_var('use_strict_mode');
		$use_zerroip = isset_request_var('use_zerroip');
		$use_ip_inspect = isset_request_var('use_ip_inspect');
			
 		if (get_request_var('drp_action') == "1") { /* Enable IP-MAC-PORT */
			for ($i=0;($i<count($ports));$i++) {
				//DGS check
				if (($port_devices[$ports[$i]["device_id"]]["type_imb_MacBindingPortState"] < 4) or ($port_devices[$ports[$i]["device_id"]]["type_imb_MacBindingPortState"] == 6)) {
					api_imp_change_imp_port_state($ports[$i], "enable", $port_devices[$ports[$i]["device_id"]], $use_strict_mode );
				}elseif($port_devices[$ports[$i]["device_id"]]["type_imb_MacBindingPortState"] == 5){
					api_imp_change_arpinsp_port_state($ports[$i], ($use_strict_mode ? "strict" : "loose"), $port_devices[$ports[$i]["device_id"]] );
					if ($use_ip_inspect and (isset($port_devices[$ports[$i]["device_id"]]["snmp_oid_en_swIpMacBindingPortIPInspection"]) and (strlen(trim($port_devices[$ports[$i]["device_id"]]["snmp_oid_en_swIpMacBindingPortIPInspection"])) > 0))) {
						api_imp_change_ipinsp_port_state($ports[$i], "enable", $port_devices[$ports[$i]["device_id"]] );
					}
				}elseif($port_devices[$ports[$i]["device_id"]]["type_imb_MacBindingPortState"] == 71){ //DES-1210-28ME
					api_imp_change_imp_port_state($ports[$i], "enable", $port_devices[$ports[$i]["device_id"]], $use_strict_mode );
					api_imp_change_arpinsp_port_state($ports[$i], ($use_strict_mode ? "strict" : "loose"), $port_devices[$ports[$i]["device_id"]] );
					if ($use_ip_inspect and (isset($port_devices[$ports[$i]["device_id"]]["snmp_oid_en_swIpMacBindingPortIPInspection"]) and (strlen(trim($port_devices[$ports[$i]["device_id"]]["snmp_oid_en_swIpMacBindingPortIPInspection"])) > 0))) {
						api_imp_change_ipinsp_port_state($ports[$i], "enable", $port_devices[$ports[$i]["device_id"]] );
					}					
				}elseif($port_devices[$ports[$i]["device_id"]]["type_imb_MacBindingPortState"] == 4){
					//use port and ARP/IP state
					api_imp_change_imp_port_state($ports[$i], "enable", $port_devices[$ports[$i]["device_id"]], $use_strict_mode );
					
					api_imp_change_arpinsp_port_state($ports[$i], ($use_strict_mode ? "strict" : "loose"), $port_devices[$ports[$i]["device_id"]] );
					if ($use_ip_inspect and (isset($port_devices[$ports[$i]["device_id"]]["snmp_oid_en_swIpMacBindingPortIPInspection"]) and (strlen(trim($port_devices[$ports[$i]["device_id"]]["snmp_oid_en_swIpMacBindingPortIPInspection"])) > 0))) {
						api_imp_change_ipinsp_port_state($ports[$i], "enable", $port_devices[$ports[$i]["device_id"]] );
					}				
				}
				
 				if ($use_zerroip) { 
 					api_imp_change_zerroip_port_state($ports[$i], "enable", $port_devices[$ports[$i]["device_id"]] );
 				}
             }
             header("Location: impb_view_ports.php?header=false");
 
         }elseif (get_request_var('drp_action') == "2") { /* Disable IP-MAC-PORT */

			for ($i=0;($i<count($ports));$i++) {	
				//DGS check
				if (($port_devices[$ports[$i]["device_id"]]["impb_func_version"] < 39) and (!(isset($port_devices[$ports[$i]["device_id"]]["snmp_oid_en_swIpMacBindingPortARPInspection"]) and (strlen(trim($port_devices[$ports[$i]["device_id"]]["snmp_oid_en_swIpMacBindingPortARPInspection"])) > 0)))) {
					api_imp_change_imp_port_state($ports[$i], "disable", $port_devices[$ports[$i]["device_id"]] );
				}elseif($port_devices[$ports[$i]["device_id"]]["type_imb_MacBindingPortState"] == 71){
					//1210-28ME
					api_imp_change_imp_port_state($ports[$i], "disable", $port_devices[$ports[$i]["device_id"]] );
					api_imp_change_arpinsp_port_state($ports[$i], "disable", $port_devices[$ports[$i]["device_id"]] );
					if ($use_ip_inspect) {
						api_imp_change_ipinsp_port_state($ports[$i], "disable", $port_devices[$ports[$i]["device_id"]] );
					}					
				}else{
					api_imp_change_arpinsp_port_state($ports[$i], "disable", $port_devices[$ports[$i]["device_id"]] );
					if ($use_ip_inspect) {
						api_imp_change_ipinsp_port_state($ports[$i], "disable", $port_devices[$ports[$i]["device_id"]] );
					}						
				}				
 				if ($use_zerroip) { 
 					api_imp_change_zerroip_port_state($ports[$i], "disable", $port_devices[$ports[$i]["device_id"]]);
 				}
            }		
 		}elseif (get_request_var('drp_action') == "3") { /* Изменить описание порта*/
			if (sizeof($ports) > 0) {
				foreach ($ports as $port) {	
					//$port_record = db_fetch_row ("SELECT * FROM imb_ports WHERE port_id=" . $port_name["port_id"] . ";");
					imb_change_port_name($port, $port_devices[$port["device_id"]], get_request_var("t_" . $port["port_id"] . "_port_name"));
				}
			}			 
 		}elseif (get_request_var('drp_action') == "4") { /* Включение порта */

			for ($i=0;($i<count($ports));$i++) {	
				api_imp_change_port_state($ports[$i], $port_devices[$ports[$i]["device_id"]], "3");
            }		
 		}elseif (get_request_var('drp_action') == "5") {  /* ВЫКлючение порта */

			for ($i=0;($i<count($ports));$i++) {	
				api_imp_change_port_state($ports[$i], $port_devices[$ports[$i]["device_id"]], "2");
            }		
 		}
 		
 		 if (isset_request_var('save_config')) { 
 				imp_save_config($device_ids);
 		}
		header("Location: impb_view_ports.php?header=false");
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = ""; $collspan=2;
 
    /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
	foreach ($_POST as $var => $val) {
        if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
             /* ================= input validation ================= */
             input_validate_input_number($matches[1]);
             /* ==================================================== */
 			$row_ids = $row_ids . "'" . $matches[1] . "', ";
			$row_array[$i] = $matches[1];
            $i++;
		}                                  
    }
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);    
	$ports=db_fetch_assoc("SELECT * FROM imb_ports where port_id in (" . $row_ids . ") ;");
	$port_devices=dimpb_array_rekey(db_fetch_assoc("SELECT `d`.`description` as dev_name , d.*, dt.* FROM imb_ports p LEFT JOIN imb_devices d on (p.device_id=d.device_id) LEFT JOIN imb_device_types dt on (d.device_type_id = dt.device_type_id) WHERE `p`.`port_id` in (" . $row_ids . ") GROUP by p.device_id;"), "device_id");
	$cnt_devs_v3_9=db_fetch_cell("SELECT count(`d`.`device_id`) FROM imb_ports p LEFT JOIN imb_devices d on (p.device_id=d.device_id) LEFT JOIN imb_device_types dt on (d.device_type_id = dt.device_type_id) WHERE `p`.`port_id` in (" . $row_ids . ") and `dt`.`impb_func_version` >= 39 GROUP by p.device_id;");	
		
	foreach ($ports as $port) {
		if ( $port["port_imb_state"] == "2" ) {
			$str_port_state = "Enable";
		}elseif ( $port["port_imb_state"] == "3" ){
			$str_port_state = "Disable";
		} else {
			$str_port_state = "Other";
		}	
		$row_list .= "<li>" . $port_devices[$port["device_id"]]["dev_name"] . " PORT:" . $port["port_number"] . " Ip-Mac State [" . $str_port_state . "]<br>";
	}
	 

 	top_header();

	form_start('impb_view_ports.php?header=false');

	html_start_box($port_actions[get_request_var('drp_action')], '60%', '', '3', 'center', '');

 
	if (!isset($row_array) or (!sizeof($row_array))) {
		print "<tr><td class='even'><span class='textError'>" . __('You must select at least one device.') . "</span></td></tr>\n";
		$save_html = "";
	}else{
	
		$save_html = "<input type='submit' value='" . __('Yes') . "' name='save'>";	
	
		if (get_request_var('drp_action') == "1") {  /* Enable Ip-Mac on port */
			print "<tr>
				<td class='textArea'>
					<p>" . __('Are you sure you want to ENABLE IP-MAC-PORT Binding on the following ports?') . "</p>
					<p><ul>$row_list</ul></p>";
					if ($cnt_devs_v3_9 > 0) {
						form_checkbox("use_ip_inspect", "", "Также изменить режим <strong>IP Inspection</strong>", "",$current_id = 0, $class = "", $on_change = "");print "<br>";
					} 
					form_checkbox("use_zerroip", "on", "Также изменить режим <strong>нулевого IP (Allow Zero IP)</strong>", "",$current_id = 0, $class = "", $on_change = "");print "<br>";
					print "<br>";
					form_checkbox("use_strict_mode", "", "При возможности использовать режим Enabled-Strict. По умолчанию будет использоваться режим Enabled-Loose", "",$current_id = 0, $class = "", $on_change = "");print "<br>";					
				print "</td>
			</tr>";			
		 }elseif (get_request_var('drp_action') == "2") {
			 print "    <tr>
					 <td class='textArea'>
						 <p>Are you sure you want to DISABLE IP-MAC-PORT Binding on the following ports?</p>
						 <p>$row_list</p>";
				if ($cnt_devs_v3_9 > 0) {
					form_checkbox("use_ip_inspect", "on", "Также изменить режим <strong>IP Inspection</strong>", "",$current_id = 0, $class = "", $on_change = "");print "<br>";
				}					 
				form_checkbox("use_zerroip", "on", "Также применить режим <strong>нулевого IP (Allow Zero IP)</strong>", "",$current_id = 0, $class = "", $on_change = "");print "<br>";					
				print "</td>
				 </tr>\n";	
		}elseif (get_request_var('drp_action') == "3") { /*Изменить описание порта*/
	 
	 
		$port_rows=db_fetch_assoc("SELECT imb_ports.*, imb_devices.hostname, imb_devices.description FROM imb_ports left join imb_devices on (imb_ports.device_id = imb_devices.device_id) WHERE imb_ports.port_id in (" . $row_ids . ");");
	 
			html_start_box("Для изменения описания порта проверьте/измените следующие поля", '100%', '', '3', 'center', '');

			html_header(array("","Host<br>Description","Hostname<br>", "№ порта", "Описание порта"));
		 
			 $i = 0;
			 if (sizeof($port_rows) > 0) {
				 foreach ($port_rows as $port_row) {
					$port_id = $port_row["port_id"];
					 form_alternate_row('line' . $port_id, true);
						 ?>
						<td><?php form_hidden_box("t_" . $port_id . "_port_id", $port_id, "form_default_value");?></td>
						<td><?php print $port_row["description"];?></td>
						<td><?php print $port_row["hostname"];?></td>
						<td><?php print $port_row["port_number"];?></td>
						<td><?php form_text_box("t_" . $port_id . "_port_name", $port_row["port_name"], "", 100, 100, "text", 1) ;?></td>
					 </tr>
					 <?php
				 }
			 }
			 $collspan = 5;
	 
	 
		}elseif (get_request_var('drp_action') == "4") { // включить порт
			print "<tr>
				<td class='textArea'>
					<p>" . __('Are you sure you want to ON the following ports?') . "</p>
					<p><ul>$row_list</ul></p>
				</td>
			</tr>";
		}elseif (get_request_var('drp_action') == "5") { // включить порт
			print "<tr>
				<td class='textArea'>
					<p>" . __('Are you sure you want to OFF the following ports?') . "</p>
					<p><ul>$row_list</ul></p>
				</td>
			</tr>";
		};
 		print "<tr>
 				<br>
 				<td colspan='2' align='left' bgcolor='#eaeaea'>";
 					form_checkbox("save_config", "", "Сохранить конфигурацию ?", "");
 		print "</td>
 			</tr>\n";			
	};
 
 	

	print "<tr>
		<td colspan='" . $collspan . "' align='right' class='saveRow'>
			<input type='hidden' name='action' value='actions_'>
			<input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
			<input type='hidden' name='drp_action' value='" . get_request_var('drp_action') . "'>" . (strlen($save_html) ? "
			<input type='button' name='cancel' onClick='cactiReturnTo()' value='" . __('No') . "'>
			$save_html" : "<input type='button' onClick='cactiReturnTo()' name='cancel' value='" . __('Return') . "'>") . "
		</td>
	</tr>";
 	

	html_end_box();

	form_end();

	bottom_footer();
 
 }
 
 
 
 
 
function impb_ports_filter() {
	global $item_rows;

	?>
	<tr class='even'>
		<td>
		<form id='impbp'>
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
					<td>
						<?php print __('Rows');?>
					</td>					
					<td>
						<select id='rows' onChange='applyFilter()'>
							<option value='-1'<?php if (get_request_var('rows') == '-1') {?> selected<?php }?>><?php print __('Default');?></option>
							<?php
							if (sizeof($item_rows)) {
								foreach ($item_rows as $key => $value) {
									print "<option value='" . $key . "'"; if (get_request_var('rows') == $key) { print ' selected'; } print '>' . $value . '</option>';
								}
							}
							?>
						</select>
					</td>					
				</tr>
			</table>
			<table class='filterTable'>
				<tr>
					<td>
						<?php print __('DevType');?>
					</td>
					<td>
						<select id='device_type_id' onChange='applyFilter()'>
							<option value='-1'<?php if (get_request_var('device_type_id') == '-1') {?> selected<?php }?>><?php print __('Any');?></option>
							<?php
							if (get_request_var('device_type_id') != -1) {
								$device_types = db_fetch_assoc_prepared('SELECT device_type_id, description FROM imb_device_types i;
									WHERE device_type_id = ? ', array(get_request_var('device_type_id')));
							}else{
								$device_types = db_fetch_assoc('SELECT device_type_id, description FROM imb_device_types i;');
							}
							if (sizeof($device_types) > 0) {
							foreach ($device_types as $device_type) {
								if ($device_type['device_type_id'] == 0) {
									$display_text = 'Unknown Device Type';
								}else{
									$display_text = $device_type['description'];
								}
								print '<option value="' . $device_type['device_type_id'] . '"'; if (get_request_var('device_type_id') == $device_type['device_type_id']) { print ' selected'; } print '>' . $display_text . '</option>';
							}
							}
							?>
						</select>
					</td>
					<td>
						<?php print __('Device');?>
					</td>
					<td>
						<select id='device_id' onChange='applyFilter()'>
							<option value='-1'<?php if (get_request_var('device_id') == '-1') {?> selected<?php }?>><?php print __('All');?></option>
							<?php
							$sql_where = '';

							if (get_request_var('device_type_id') != '-1') {
								$sql_where .= (strlen($sql_where) ? ' AND ' : 'WHERE ') . '(device_type_id=' . get_request_var('device_type_id') . ') ';
							}

							$devices = array_rekey(db_fetch_assoc("SELECT device_id, description, hostname FROM imb_devices  $sql_where ORDER BY device_id"), "device_id", "description");
							if (sizeof($devices)) {
								foreach ($devices as $device_id => $description) {
									print '<option value="' . $device_id .'"'; if (get_request_var('device_id') == $device_id) { print " selected"; } print ">" . $description . "</option>";
								}
							}
							?>
						</select>
					</td>					
				</tr>
			</table>
			<table class='filterTable'>
				<tr>
					<td>
						<?php print __('Port IMPB Status');?>
					</td>
					<td>
						<select id='status' onChange='applyFilter()'>
                             <option value="-1"<?php if (get_request_var('status') == "-1") {?> selected<?php }?>>Any</option>
                             <option value="2"<?php if (get_request_var('status') == "2") {?> selected<?php }?>>Enable</option>
                             <option value="3"<?php if (get_request_var('status') == "3") {?> selected<?php }?>>Disabled</option>
                             <option value="1"<?php if (get_request_var('status') == "1") {?> selected<?php }?>>Other</option>
						</select>
					</td>
					<td>
						<?php print __('Rows');?>
					</td>
					<td>
						<select id='rows' onChange='applyFilter()'>
							<option value='-1'<?php if (get_request_var('rows') == '-1') {?> selected<?php }?>><?php print __('Default');?></option>
							<?php
							if (sizeof($item_rows)) {
							foreach ($item_rows as $key => $value) {
								print "<option value='" . $key . "'"; if (get_request_var('rows') == $key) { print ' selected'; } print '>' . $value . '</option>';
							}
							}
							?>
						</select>
					</td>
					<td>
						<?php print __('Port');?>
					</td>
					<td>
						<input type='text' id='port_number' size='25' value='<?php print get_request_var('port_number');?>'>
					</td>					
				</tr>
			</table>
		</form>
		<script type='text/javascript'>
		function applyFilter() {
			strURL  = urlPath+'plugins/impb/impb_view_ports.php?header=false';
			strURL += '&status=' + $('#status').val();
			strURL += '&device_id=' + $('#device_id').val();
			strURL += '&device_type_id=' + $('#device_type_id').val();
			strURL += '&filter=' + $('#filter').val();
			strURL += '&port_number=' + $('#port_number').val();
			strURL += '&rows=' + $('#rows').val();
			loadPageNoHeader(strURL);
		}

		function clearFilter() {
			strURL  = urlPath+'plugins/impb/impb_view_ports.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}

		function exportRows() {
			strURL  = urlPath+'plugins/impb/impb_view_ports.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_view_ports.php?import=true';
			loadPageNoHeader(strURL);
		}

		$(function() {
			$('#impbp').submit(function(event) {
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
  
 
 
 


 
 

?>
