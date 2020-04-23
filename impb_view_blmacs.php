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

 $title = __("IMPB - Blocked MAC's View");
 
 //***********************************************************

 $blmacs_actions = array(
 	1 => "Прописать блок",
 	2 => "Удалить блок"
 	);
 
 
/* check actions */
switch (get_request_var('action')) {
	case 'actions_':
		form_actions_blmacs();

		break;
	default:
		impb_redirect();
		general_header();
		impb_view_blmacs();
		bottom_footer();
		break;
}




function impb_view_get_blmacs_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
     /* form the 'where' clause for our main sql query */
     if (strlen(get_request_var('mac_filter')) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
  
     switch (get_request_var('mac_filter_type_id')) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_blmacs.blmac_macaddr='" . get_request_var('mac_filter') . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_blmacs.blmac_macaddr LIKE '%%" . get_request_var('mac_filter') . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_blmacs.blmac_macaddr LIKE '" . get_request_var('mac_filter') . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_blmacs.blmac_macaddr NOT LIKE '" . get_request_var('mac_filter') . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_blmacs.blmac_macaddr NOT LIKE '" . get_request_var('mac_filter') . "%%'";
         }
     }
 
     if ((strlen(get_request_var('ip_filter')) > 0)||(get_request_var('ip_filter_type_id') > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch (get_request_var('ip_filter_type_id')) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip='" . get_request_var('ip_filter') . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip LIKE '%%" . get_request_var('ip_filter') . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip NOT LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip NOT LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip != ''";
         }
         $sql_where .= " OR NULL ";
     }

	 
     if (strlen(get_request_var('port_filter')) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_blmacs.blmac_port=" . get_request_var('port_filter');
     }
     
     
     if (!(get_request_var('device_id') == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_blmacs.device_id=" . get_request_var('b_device_id');
     }

	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}
	
 	$sql_order = get_order_string();
	
 	
         $query_string = "SELECT If (lbs.segment is null, if(host.hostname is null,'ip_noo','ip_cacti'),CONCAT('ipb_',lb_vgroups_s.blocked)) as sig,
								 lb_vgroups_s.f_addr,
								 If (lbs.segment is null, if(host.hostname is null,'IP нигде не зарегистрирован','Служебный IP'),CASE lb_vgroups_s.blocked WHEN 0 THEN 'Все ОК' WHEN 1 THEN CONCAT('Минусовой баланс = ',ROUND(lb_vgroups_s.balance/100,2), ' c ', date(lb_vgroups_s.block_date)) WHEN 2 THEN CONCAT('Блок пользователя c ', date(lb_vgroups_s.acc_ondate)) WHEN 3 THEN CONCAT('Админ Блок c ', date(lb_vgroups_s.acc_ondate)) END ) as sig2,
								 If (t1.segment is null, 'ip_noo',CONCAT('ipb_',t2.blocked)) as sig1,
								 t2.f_addr as f_addr1,
				imb_devices.description, 
 				imb_devices.hostname, 
 				imb_ports.port_name,
 				imb_blmacs.*, 
 				imb_temp_blmacinfo.* ,
				h.id as cid
               FROM imb_blmacs 
 			  left join imb_devices on imb_blmacs.device_id=imb_devices.device_id 
 			  left join imb_ports on (imb_blmacs.device_id=imb_ports.device_id and imb_blmacs.blmac_port=imb_ports.port_number)
 			  left join imb_temp_blmacinfo on imb_blmacs.blmac_id=imb_temp_blmacinfo.blmacinfo_info_id

			LEFT JOIN (SELECT segment, MAX(vg_id) as vg_id FROM lb_staff group by segment) lbs ON INET_ATON(imb_blmacs.blmac_blocked_ip) = lbs.segment
			left JOIN lb_vgroups_s             ON lbs.vg_id = lb_vgroups_s.vg_id
			left JOIN host             ON imb_blmacs.blmac_blocked_ip = host.hostname
			left JOIN lb_staff  as t1  ON INET_ATON(blmacinfo_cor_ip) = t1.segment
			left JOIN lb_vgroups_s  as t2           ON t1.vg_id = t2.vg_id
			left JOIN host   h          ON imb_devices.hostname = h.hostname
            $sql_where
			$sql_order
			$sql_limit";
 
         return db_fetch_assoc($query_string);
     
 }

function impb_blmacs_request_validation() {
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
			'default' => 'blmac_id',
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
		'ip_filter_type_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
		'ip_filter' => array(
			'filter' => FILTER_CALLBACK,
			'default' => '',
			'options' => array('options' => 'sanitize_search_string')
			),
		'mac_filter_type_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
		'mac_filter' => array(
			'filter' => FILTER_CALLBACK,
			'default' => '',
			'options' => array('options' => 'sanitize_search_string')
			),
		'port_filter_type_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
		'port_filter' => array(
			'filter' => FILTER_CALLBACK,
			'default' => '',
			'options' => array('options' => 'sanitize_search_string')
			),				
			
	);

	validate_store_request_vars($filters, 'sess_impb_blmacs');
	/* ================= input validation ================= */
}

 
function impb_view_blmacs() {
	global $title, $report, $impb_search_types, $config , $blmacs_actions;
 
   
	impb_blmacs_request_validation();

	if (get_request_var('rows') == -1) {
		$rows = read_config_option('num_rows_table');
	}elseif (get_request_var('rows') == -2) {
		$rows = 999999;
	}else{
		$rows = get_request_var('rows');
	}
	
	$webroot = $config['url_path'] . 'plugins/impb/';

	impb_tabs();
	html_start_box($title, '100%', '', '3', 'center', '');
	impb_blmacs_filter();
	html_end_box(); 
 
    $sql_where = "";
    $bmacs_results = impb_view_get_blmacs_records($sql_where, true, $rows);
    $rows_query_string = "SELECT
             COUNT(imb_blmacs.device_id)
             FROM imb_blmacs
             $sql_where";	
    $total_rows = db_fetch_cell($rows_query_string);
	
	$nav = html_nav_bar('impb_view_blmacs.php?report=bindings', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 14, __('Blocked MACs'), 'page', 'main');

	form_start('impb_view_blmacs.php', 'chk');
	
	//print $nav;
	
	html_start_box('', '100%', '', '3', 'center', '');

 
	$display_text = array(
		'description'      		=> array(__('Device'), 'ASC'),
		'hostname'        		=> array(__('IP(имя)'), 'ASC'),
		'blmac_macaddr'      	=> array(__('MAC Address'), 'ASC'),
		'blmacinfo_banned' 		=> array(__('BANNED'), "ASC"),
 		"blmac_blocked_ip" 		=> array(__('Blocked<br>IP Address'), "ASC"),
		"info" 					=> array(__('Info'), "ASC"),		
		'blmac_port' 			=> array(__('Blocked<br>On port'),"DESC"),
		'blmac_vid' 			=> array(__('Blocked<br>Vlan ID'),"DESC"),
		'blmacinfo_cor_ip' 		=> array(__('Correct<br>IP'),"DESC"),
		'blmacinfo_cor_portlist' => array(__('Correct<br>Port'),"DESC"),
		'blmac_first_scan_date' => array(__('Время<br>Блока'),'DESC'),
		'blmac_scan_date' 		=> array(__('Last<br>Scan Date'), 'DESC'));	
 
	html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false);
 
    $i = 0;

 	$mac_font_size=read_config_option("dimpb_mac_addr_font_size");
     if (sizeof($bmacs_results) > 0) {
         foreach ($bmacs_results as $bmacs_result) {
 		  if ($bmacs_result["blmac_online"] == 1)  {
 			$color_line_date="<span style='font-weight: bold;'>";
 		  }else{
 			$color_line_date="";
 		  }	
		  
		  
		$str_info = " <span style='font-weight: bold; color: red;'>[";		  
			if ($bmacs_result["blmac_type"] == 1)  {
				$str_info .= "PL";
			}elseif ($bmacs_result["blmac_type"] == 2) {
				$str_info .= "PL";
			}elseif ($bmacs_result["blmac_type"] == 3) {
				$str_info .= "SL";
			}			
			
			if (!(strrpos($bmacs_result["blmac_info"], "flood") === false)) {
				$str_info .= ",FL";
			}
			switch ($bmacs_result["blmac_done"]) {
				case 1:
					$str_info .= ",aDEL";
					break;
				case 2:
					$str_info .= ",aCRT";
					break;
				case 3:
					$str_info .= ",aCHG";					
					break;
				case 4:
					$str_info .= ",aMOV";					
					break;					
			}	
			

		$str_info .= "]</span>";  
		
            form_alternate_row('line' . $bmacs_result["blmac_id"], true);
 			form_selectable_cell($bmacs_result["description"], $bmacs_result["blmac_id"] );
 			
 			form_selectable_cell($bmacs_result["hostname"], $bmacs_result["blmac_id"] );
 			
 			form_selectable_cell("<a class='linkEditMain' href='impb_view_info.php?report=info&device_id=-1&ip_filter_type_id=1&ip_filter=&mac_filter_type_id=2&mac_filter=" . $bmacs_result["blmac_macaddr"] . "&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=info&x=14&y=6'><font size='" . $mac_font_size . "' face='Courier'>" . 
 				(strlen(get_request_var('mac_filter')) ? strtoupper(preg_replace("/(" . preg_quote(get_request_var('mac_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_macaddr"])) : $bmacs_result["blmac_macaddr"] ). "</font></a>", $bmacs_result["blmac_id"]);
 
 			form_selectable_cell(imp_convert_banned_state_2str($bmacs_result["blmacinfo_banned"]), $bmacs_result["blmac_id"] );
 			
 			form_selectable_cell("<img src='" . $config['url_path'] . "plugins/impb/images/" . $bmacs_result["sig"] . ".png' TITLE='" . $bmacs_result["sig2"] . "' align='absmiddle'><a class='linkEditMain' TITLE='" . $bmacs_result["sig2"] . ' Адр:' . $bmacs_result["f_addr"] . "' href='impb_view_info.php?report=info&device_id=-1&ip_filter_type_id=2&ip_filter=" . $bmacs_result["blmac_blocked_ip"] . "&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=info&x=14&y=6'><font size='" . $mac_font_size . "' face='Courier'>" .
 				(strlen(get_request_var('mac_filter')) ? strtoupper(preg_replace("/(" . preg_quote(get_request_var('ip_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_blocked_ip"])) : $bmacs_result["blmac_blocked_ip"]) , $bmacs_result["blmac_id"]);
 			
			form_selectable_cell($str_info, $bmacs_result["blmac_id"] );
			
 			//port (port_name)
			form_selectable_cell((strlen(get_request_var('port_filter')) ? preg_replace("/(" . preg_quote(get_request_var('port_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_port"]) : $bmacs_result["blmac_port"]) . " (" . $bmacs_result["port_name"] . ")" . " <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_ion_view.php?action=preview&style=&host_id=" . $bmacs_result['cid'] . "&snmp_index=" . $bmacs_result['blmac_port'] . "&graph_template_id=-1&rfilter=") . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' alt='' title='View Graph' align='absmiddle'></a>", $bmacs_result["blmac_id"] );
 			
			
			
			
 			//form_selectable_cell(imp_convert_blmac_state_2str($bmacs_result["blmac_type"]), $bmacs_result["blmac_id"] );
 			//form_selectable_cell($bmacs_result["port_name"], $bmacs_result["blmac_id"] );
 			//vlan (vlan_name)
			form_selectable_cell($bmacs_result["blmac_vid"] . " (" . $bmacs_result["blmac_vlanname"] . ")", $bmacs_result["blmac_id"] );
 			//form_selectable_cell($bmacs_result["blmac_vlanname"], $bmacs_result["blmac_id"] );
			//dobavit
 			form_selectable_cell("<img src='" . $config['url_path'] . "plugins/impb/images/" . $bmacs_result["sig1"] . ".png' align='absmiddle'><a class='linkEditMain' TITLE='" . 'Адр:' . $bmacs_result["f_addr1"] . "' href='impb_view_info.php?report=info&device_id=-1&ip_filter_type_id=2&ip_filter=" . $bmacs_result["blmacinfo_cor_ip"] . "&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=info&x=14&y=6'><font size='" . $mac_font_size . "' face='Courier'>" .
 				(strlen(get_request_var('mac_filter')) ? strtoupper(preg_replace("/(" . preg_quote(get_request_var('ip_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmacinfo_cor_ip"])) : $bmacs_result["blmacinfo_cor_ip"]) . "</font></a>", $bmacs_result["blmac_id"]);
 			form_selectable_cell($bmacs_result["blmacinfo_cor_portlist"], $bmacs_result["blmac_id"] );
 			form_selectable_cell(date('H:i:s',strtotime($bmacs_result["blmac_first_scan_date"])) . " ( " .  impb_DateTimeDiff($bmacs_result["blmac_first_scan_date"]) . ")", $bmacs_result["blmac_id"] );
 			
 			form_selectable_cell((strlen(get_request_var('port_filter')) ? $color_line_date . " " .preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>",  $bmacs_result["blmac_scan_date"])  : $color_line_date . " " . $bmacs_result["blmac_scan_date"]), $bmacs_result["blmac_id"] );			

 			if ($bmacs_result["blmac_online"] == 1) {
 				form_checkbox_cell($bmacs_result["blmac_macaddr"], $bmacs_result["blmac_id"]);
 			} else {
 				print "<td></td>";
 			}					
 				
         }
 
		 /*update count of view for done blmacs row. More 9 time of view - delete*/
		 db_execute("UPDATE `imb_blmacs` SET `blmac_done_view_count`=`blmac_done_view_count`+1 WHERE `blmac_done`>0;");			
		 db_execute("DELETE FROM imb_blmacs WHERE `blmac_done_view_count`>5;");
		 db_execute("UPDATE `imb_devices` SET `ip_mac_blocked_total`=(SELECT count(*) FROM imb_blmacs where `device_id`='" . $bmacs_result["device_id"] . "') WHERE `device_id`='" . $bmacs_result["device_id"] . "';");
         /* put the nav bar on the bottom as well */
         print $nav;
		 
     }else{
         print "<tr><td><em>No D-Link IP-Mac-Port Binding  Blocked MAC  Results</em></td></tr>";
     }
	html_end_box(false);

	if (sizeof($bmacs_results)) {
		print $nav;
	}

    impb_draw_actions_dropdown($blmacs_actions, "");
	form_end();
 }
 
function form_actions_blmacs() {
     global $config, $blmacs_actions, $fields_macipport_edit, $impb_imp_mode;
 
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
		$device_ids=db_fetch_assoc("SELECT device_id FROM imb_blmacs where blmac_id in (" . $str_ids . ") group by device_id;");
		$blmacs=db_fetch_assoc("SELECT * FROM imb_blmacs where blmac_id in (" . $str_ids . ") ;");
		$blmacs_devices=dimpb_array_rekey(db_fetch_assoc("SELECT `d`.`description` as dev_name , d.*, dt.* FROM imb_blmacs b LEFT JOIN imb_devices d on (b.device_id=d.device_id) LEFT JOIN imb_device_types dt on (d.device_type_id = dt.device_type_id) WHERE `b`.`blmac_id` in (" . $str_ids . ") GROUP by b.device_id;"), "device_id");
		
 		         
 		
 		
 		if (get_request_var('drp_action') == "1") { /* Прописать заблокированный мак. */
 		   $blmacs_user = array();
		   for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
 				$cur_blmac_id = $selected_items[$i];
 				$blmacs_user[$cur_blmac_id]["blmac_id"] = $cur_blmac_id;
 				$blmacs_user[$cur_blmac_id]["blmac_port"] = form_input_validate(get_request_var('t_' . $cur_blmac_id . '_port'), 't_' . $cur_blmac_id . '_port', '^([0-5]{0,1}[0-9]{1}(,|-){1})*[0-5]{0,1}[0-9]{1}$', false, 3);
 				$blmacs_user[$cur_blmac_id]["blmac_ip_adrress"] = form_input_validate(impb_translate_ip_address(get_request_var('t_' . $cur_blmac_id . '_ip_adrress')), 't_' . $cur_blmac_id . '_ip_adrress', '^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$', false, 3);
 				$blmacs_user[$cur_blmac_id]["blmac_acl_mode"] = $impb_imp_mode[form_input_validate(get_request_var('t_' . $cur_blmac_id . '_use_acl'), 't_' . $cur_blmac_id . '_use_acl', '[^0]', false, 3) ];
            }
                 
 			if (!is_error_message()) {
 				if (sizeof($blmacs) > 0) {
 					foreach ($blmacs as $blmac) {	
 						//$blmac_record = db_fetch_row ("SELECT * FROM imb_blmacs WHERE blmac_id=" . $blmac["blmac_id"] . ";");
 						imb_create_imp_record_from_block($blmacs_devices[$blmac["device_id"]], $blmac["blmac_macaddr"], $blmacs_user[$blmac["blmac_id"]]["blmac_ip_adrress"], $blmacs_user[$blmac["blmac_id"]]["blmac_port"], $blmac, $blmacs_user[$blmac["blmac_id"]]["blmac_acl_mode"]);
						//imb_create_imp_record_from_block($blmacs_devices[$blmac["device_id"]], $blmac_record["blmac_macaddr"], $blmac["blmac_ip_adrress"], $blmac["blmac_port"], $blmac_record, $blmac["blmac_acl_mode"]);
 					}
 				}
 			}
 		}elseif (get_request_var('drp_action') == "2") { /* delete blocked macs */
			if (sizeof($blmacs) > 0) {
				foreach ($blmacs as $blmac) {	
					if (isset($blmacs_devices[$blmac["device_id"]])) {
						//$port_record = db_fetch_row ("SELECT * FROM imb_ports WHERE port_id=" . $port_name["port_id"] . ";");
						api_imp_delete_blmacs($blmac, $blmacs_devices[$blmac["device_id"]]);
					}
				}
			}
            header("Location: impb_view_blmacs.php?header=false");
         };
 
 		if (isset_request_var('save_config')) { 
 				imp_save_config($device_ids);
 		}
 	if (!is_error_message()) {
 		header("Location: impb_view_blmacs.php?header=false");
 	}else{
 		header("Location: impb_view_blmacs.php?header=false&action=" . get_request_var('action') . "&drp_action=" . get_request_var('drp_action') . "&post_error=" . get_request_var('selected_items'));
 		$_REQUEST["selected_items"]="";
 	}
 		
         exit;
     }
 
     /* setup some variables */
    $row_list = ""; $i = 0;
 	$row_ids = ""; $i = 0;
	$colspan = 2;
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     if (!isset_request_var('post_error')) { /*Если установлено это значение - значит страница перезагружаеться из-за ошибки при вводе, и данные нужно брать не из POST, а из спец. переменной.*/
	foreach ($_REQUEST as $var => $val) {
         if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
             /* ================= input validation ================= */
             input_validate_input_number($matches[1]);
             /* ==================================================== */
 			$row_info = db_fetch_row("SELECT imb_blmacs.*, imb_devices.hostname, imb_devices.description FROM imb_blmacs left join imb_devices on (imb_blmacs.device_id = imb_devices.device_id) WHERE imb_blmacs.blmac_id=" . $matches[1]);
 			$row_list .= "<li>" . $row_info["description"] . " PORT:" . $row_info["blmac_port"] . " MAC [" . $row_info["blmac_macaddr"] . "]<br>";
             $row_array[$i] = $matches[1];
 			$row_ids = $row_ids . "'" . $matches[1] . "', ";
         $i++;
		 }                                  
     }
 	}else{
 		$row_array=unserialize(stripslashes(get_request_var('post_error')));
 		if (isset($row_array) && is_array($row_array) && (count($row_array) > 0)) {
 			foreach ($row_array as $row_id) {
 	            $row_info = db_fetch_row("SELECT imb_blmacs.*, imb_devices.hostname, imb_devices.description FROM imb_blmacs left join imb_devices on (imb_blmacs.device_id = imb_devices.device_id) WHERE imb_blmacs.blmac_id=" . $row_id);
 				$row_list .= "<li>" . $row_info["description"] . " PORT:" . $row_info["blmac_port"] . " MAC [" . $row_info["blmac_macaddr"] . "]<br>";
 				$row_ids = $row_ids . "'" . $row_id . "', ";		
 			}
 		}
 	}
 	
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 
 	top_header();

	form_start('impb_view_blmacs.php?header=false');

	html_start_box($blmacs_actions[get_request_var('drp_action')], '60%', '', '3', 'center', '');
    
	
	if (!isset($row_array) or (!sizeof($row_array))) {
		print "<tr><td class='even'><span class='textError'>" . __('You must select at least one device.') . "</span></td></tr>\n";
		$save_html = "";
	}else{
	
		$save_html = "<input type='submit' value='" . __('Yes') . "' name='save'>";	
		
		if (((isset_request_var('drp_action')) && (get_request_var('drp_action') == "1")) || ((isset_request_var('post_error')) && (get_request_var('drp_action') == "1")))  {
			$blmacs_rows=db_fetch_assoc("SELECT imb_blmacs.*, imb_devices.hostname, imb_devices.description, imb_device_types.setting_imb_def_mode " .
											" FROM imb_blmacs " .
											" left join imb_devices on (imb_blmacs.device_id = imb_devices.device_id) " .
											" left join imb_device_types on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
											" WHERE imb_blmacs.blmac_id in (" . $row_ids . ");");
			html_start_box("Для создания записи IP-MAC-PORT проверьте/измените следующие поля.", "100%", '', "4", "center", "");
			html_header(array("","Host<br>Description","Hostname<br>", "IP-адресс", "MAC-адресс",  "Порт", "Режим"));
			$i = 0;
			if (sizeof($blmacs_rows) > 0) {
				foreach ($blmacs_rows as $blmacs_row) {
					$blmac_id = $blmacs_row["blmac_id"];
					form_alternate_row();
						?>
						<td><?php form_hidden_box("t_" . $blmac_id . "_blmac_id", $blmac_id, "form_default_value");?></td>
						<td><?php print $blmacs_row["description"];?></td>
						<td><?php print $blmacs_row["hostname"];?></td>
						<td><?php form_text_box("t_" . $blmac_id . "_ip_adrress", $blmacs_row["blmac_blocked_ip"], "", 15, 15, "text", 1) ;?></td>
						<td><?php print $blmacs_row["blmac_macaddr"];?></td>
						<td><?php form_text_box("t_" . $blmac_id . "_port", $blmacs_row["blmac_port"], "", 2, 2, "text", 1) ;?></td>
						<td><?php form_dropdown("t_" . $blmac_id . "_use_acl",  $impb_imp_mode, "", "", $blmacs_row["setting_imb_def_mode"], "", "") ;?></td>				
					</tr>
					<?php
				}
			}
			$colspan = 7;
	 
	 
		}elseif (((isset_request_var('drp_action'))) && (get_request_var('drp_action') == "2") ) {  /* delete blocked macs*/
				print "<tr>
					<td class='textArea'>
						<p>" . __('Are you sure you want to DEL the following rows?') . "</p>
						<p><ul>$row_list</ul></p>
					</td>
				</tr>";         
		};
	};

	if (((isset_request_var('drp_action')) && (get_request_var('drp_action') == "1")) || ((isset_request_var('post_error')) && (get_request_var('drp_action') == "1"))) {
		print "<tr>
				<br>
				<td colspan='2' align='left' bgcolor='#eaeaea'>";
					form_checkbox("save_config", "", "Сохранить конфигурацию ?", "");
		print "</td>
			</tr>\n";
	}



	print "<tr>
		<td colspan='$colspan' align='right' class='saveRow'>
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
 
 
function impb_blmacs_filter() {
	global $item_rows, $impb_search_types;

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
						<?php print __('Device');?>
					</td>
					<td>
						 <select id='device_id' onChange='applyFilter()'>
						<option value='-1'<?php if (get_request_var('device_id') == '-1') {?> selected<?php }?>><?php print __('Any');?></option>
						 <?php
						$devices = db_fetch_assoc_prepared('SELECT device_id, description, hostname FROM imb_devices ORDER BY order_id');
						 if (sizeof($devices) > 0) {
							 foreach ($devices as $device) {
								print '<option value=" ' . $device["device_id"] . '"'; if (get_request_var('device_id') == $device["device_id"]) { print " selected"; } print ">" . $device["description"] . "(" . $device["hostname"] . ")" .  "</option>\n";
							 }
						 }
						 ?>
						 </select>					
					</td>
				</tr>
				<tr>
			</table>
			<table class='filterTable'>				
 				<tr>
 					<td>
						<?php print __('IP Address');?>
 					</td>
 					<td width="1">
 						<select id='ip_filter_type_id'>
 						<?php
 						for($i=1;$i<=sizeof($impb_search_types);$i++) {
 							print "<option value='" . $i . "'"; if (get_request_var('ip_filter_type_id') == $i) { print " selected"; } print ">" . $impb_search_types[$i] . "</option>\n";
 						}
 						?>
 						</select>
 					</td>
 					<td width="1">
 						<input type='text' id='ip_filter' size='25' value='<?php print get_request_var('ip_filter');?>'>
 					</td>
 				</tr>				
 				<tr>
 					<td>
 						<?php print __('Mac Address');?>
 					</td>
 					<td width="1">
 						<select id="mac_filter_type_id">
 						<?php
 						for($i=1;$i<=sizeof($impb_search_types)-2;$i++) {
 							print "<option value='" . $i . "'"; if (get_request_var('mac_filter_type_id') == $i) { print " selected"; } print ">" . $impb_search_types[$i] . "</option>\n";
 						}
 						?>
 						</select>
 					</td>
 					<td width="1">
 						<input type="text" id="mac_filter" size="20" value="<?php print get_request_var('mac_filter');?>">
 					</td>
 
 				</tr>
 				<tr>
 					<td>
 						<?php print __('Port');?>
 					</td>
 					<td width="1">
 						<select id="port_filter_type_id">
 						<?php
 						for($i=1;$i<=sizeof($impb_search_types);$i++) {
 							print "<option value='" . $i . "'"; if (get_request_var('port_filter_type_id') == $i) { print " selected"; } print ">" . $impb_search_types[$i] . "</option>\n";
 						}
 						?>
 						</select>
 					</td>
 					<td width="1">
 						<input type="text" id="port_filter" size="20" value="<?php print get_request_var('port_filter');?>">
 					</td>
					<td>
						<input type='submit' id='clearport' value='<?php print __('Clear');?>'>
					</td>					
 				</tr>
			</table>				
		</form>
		<script type='text/javascript'>
		function applyFilter() {
			strURL  = urlPath+'plugins/impb/impb_view_blmacs.php?header=false';
			strURL += '&device_id=' + $('#device_id').val();
			strURL += '&ip_filter_type_id=' + $('#ip_filter_type_id').val();
			strURL += '&ip_filter=' + $('#ip_filter').val();
			strURL += '&mac_filter_type_id=' + $('#mac_filter_type_id').val();
			strURL += '&mac_filter=' + $('#mac_filter').val();
			strURL += '&port_filter_type_id=' + $('#port_filter_type_id').val();
			strURL += '&port_filter=' + $('#port_filter').val();			
			strURL += '&filter=' + $('#filter').val();
			strURL += '&rows=' + $('#rows').val();
			loadPageNoHeader(strURL);
		}

		function clearFilter() {
			strURL  = urlPath+'plugins/impb/impb_view_blmacs.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}

		function clearPort() {
			strURL  = urlPath+'plugins/impb/impb_view_blmacs.php?header=false&port_filter_type_id=1&port_filter=';
			strURL += '&device_id=' + $('#device_id').val();
			strURL += '&ip_filter_type_id=' + $('#ip_filter_type_id').val();
			strURL += '&ip_filter=' + $('#ip_filter').val();
			strURL += '&mac_filter_type_id=' + $('#mac_filter_type_id').val();
			strURL += '&mac_filter=' + $('#mac_filter').val();
			strURL += '&filter=' + $('#filter').val();
			strURL += '&rows=' + $('#rows').val();
			loadPageNoHeader(strURL);
		}
		
		function exportRows() {
			strURL  = urlPath+'plugins/impb/impb_view_blmacs.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_view_blmacs.php?import=true';
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

			$('#clearport').click(function() {
				clearPort();
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
