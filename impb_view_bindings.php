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
  //ini_set('memory_limit', '256M');

 $title = __('IMPB - Bindings View');
 
 
 //***********************************************************

 
 $binding_actions = array(
 	1 => "Удалить запись",
 	2 => "Изменить запись",
 	3 => "Групповое изменение",
 	4 => "Создать запись",
	5 => "Изменить свободу",
	6 => "Отправить СМС"
 	);

/* check actions */
switch (get_request_var('action')) {
	case 'actions_':
		form_actions_bindings();

		break;
	default:
		impb_redirect();
		general_header();
	
		impb_view_bindings();
		bottom_footer();
		break;
}



function impb_view_get_bindings_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
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
                 $sql_where .= " imb_macip.macip_macaddr='" . get_request_var('mac_filter') . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_macip.macip_macaddr LIKE '%%" . get_request_var('mac_filter') . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_macip.macip_macaddr LIKE '" . get_request_var('mac_filter') . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_macip.macip_macaddr NOT LIKE '" . get_request_var('mac_filter') . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_macip.macip_macaddr NOT LIKE '" . get_request_var('mac_filter') . "%%'";
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
                 $sql_where .= " imb_macip.macip_ipaddr='" . get_request_var('ip_filter') . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_macip.macip_ipaddr LIKE '%%" . get_request_var('ip_filter') . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_macip.macip_ipaddr LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_macip.macip_ipaddr NOT LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_macip.macip_ipaddr NOT LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_macip.macip_ipaddr = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_macip.macip_ipaddr != ''";
         }
     }
 
     if ((strlen(get_request_var('port_filter')) > 0)||(get_request_var('port_filter_type_id') > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch (get_request_var('port_filter_type_id')) {
             case "1": /* do not filter */
                 break;
             case "2": /* состоит */
 				$sql_where .= " FIND_IN_SET('" . get_request_var('port_filter') . "',`macip_port_list`)";
                 break;
             case "3": /* не состоит */
 				$sql_where .= " NOT FIND_IN_SET('" . get_request_var('port_filter') . "',`macip_port_list`)";
 
         }
     }	
 	
     if (strlen(get_request_var('m_filter'))) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
             $sql_where .= " (imb_macip.macip_port_hex LIKE '%" . get_request_var('m_filter') . "%' OR " .
                 "imb_macip.macip_port_list LIKE '%" . get_request_var('m_filter') . "%' OR " .
 				"lbs.login LIKE '%" . get_request_var('m_filter') . "%' OR " .
				"imb_macip.macip_lastchange_date LIKE '%" . get_request_var('m_filter') . "%' OR " .
 				"f_flat LIKE '%" . get_request_var('m_filter') . "%' OR " .
				"imb_macip.macip_scan_date LIKE '%" . get_request_var('m_filter') . "%')";
    }
 
     if ((strlen(get_request_var('billstatus')) > 0) and (get_request_var('billstatus') > -1)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch (get_request_var('billstatus')) {
             case "-1": /* do not filter */
				break;
             case "0": /* положительный баланс */
 				$sql_where .= " lbs.blocked = 0 ";
                 break;
             case "1": /* отрицательный баланс */
 				$sql_where .= " lbs.blocked = 1 ";
				break;
             case "3": /* заблокирован */
 				$sql_where .= " lbs.blocked = 3 ";
				break;
             case "4": /* несуществ */
 				$sql_where .= " lbs.segment is null and host.hostname is null ";
				break;					
             case "5": /* служебн */
 				$sql_where .= " lbs.segment is null and host.hostname is not null ";
				break; 
             case "6": /* оборуд по акции */
 				$sql_where .= " lbs.equipm_rtr is not null ";
				break;				
         }
    }
	
     if (!(get_request_var('device_id') == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_macip.device_id=" . get_request_var('device_id');
     }


	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}
	
 	$sortby = get_request_var('sort_column');
 	if ($sortby=="macip_ipaddr") {
 		$sortby = "INET_ATON(macip_ipaddr)";
 	}
 	if ($sortby=="f_flat") {
 		$sortby = "ABS(f_flat)";
 	}	
//								 If (lbs.segment is null, if(host.hostname is null,'IP нигде не зарегистрирован','Служебный IP'),if(lbs.blocked = 0,'Все ОК',if(lbs.blocked = 1,'Минусовой баланс',)) as sig1,
 	
 		$query_string = "SELECT  If (lbs.segment is null, if(host.hostname is null,'ip_noo','ip_cacti'),CONCAT('ipb_',lbs.blocked)) as sig, " .

		
								//" If (lbs.segment is null, if(host.hostname is null,'IP нигде не зарегистрирован','Служебный IP'),CASE lbs.blocked WHEN 0 THEN 'Все ОК' WHEN 1 THEN CONCAT('Минусовой баланс = ',ROUND(lbs.balance/100,2), ' c ', date(lbs.acc_ondate)) WHEN 2 THEN CONCAT('Блок пользователя c ', date(lbs.acc_ondate)) WHEN 3 THEN CONCAT('Админ Блок c ', date(lbs.acc_ondate)) END ) as sig2, " .
								" If (lbs.segment is null, if(host.hostname is null,'IP нигде не зарегистрирован','Служебный IP'), " . 
									"concat ('[', lbs.ag_num , '], ' , " .
									"CASE lbs.blocked " .
										" WHEN 0 THEN CONCAT('Баланс = ',ROUND(lbs.balance,2)) " .
										" WHEN 1 THEN CONCAT('Минусовой баланс = ',ROUND(lbs.balance/100,2), ' c ', date(lbs.block_date)) " .
										" WHEN 2 THEN CONCAT('Блок пользователя c ', date(lbs.acc_ondate)) " .
										" WHEN 3 THEN CONCAT('Админ Блок c ', date(lbs.acc_ondate)) END )) as sig2, " .
								
			" f_addr, h.id as cid,
			imb_device_types.device_type_id, imb_device_types.type_imb_action, imb_device_types.type_imb_mode, imb_devices.description, imb_devices.hostname, imb_devices.last_rundate,
            imb_macip.device_id, imb_macip.macip_id, imb_macip.macip_macaddr, imb_macip.macip_ipaddr, imb_macip.macip_port_list, imb_macip.macip_port_view, imb_macip.macip_imb_status, imb_macip.macip_banned, imb_macip.macip_imb_action,imb_macip.macip_mode, macip_online, macip_first_scan_date, macip_lastchange_date, imb_macip.macip_scan_date, macip_count_scan, imb_macip.macip_active_last_poll, imb_macip.macip_may_move,
			 lbs.f_flat, lbs.equipm_rtr,  if(gl_ip.id is null,if(gl_ping.id is null,'0',gl_ping.id),gl_ip.id) as ip_local_graph_id,
             lbs.login
			 FROM  imb_macip
             left JOIN imb_devices
             ON imb_macip.device_id = imb_devices.device_id
             JOIN imb_device_types ON imb_devices.device_type_id = imb_device_types.device_type_id 
			 LEFT JOIN (SELECT l.segment,  v.*  FROM lb_staff l left JOIN lb_vgroups_s v ON l.vg_id = v.vg_id WHERE v.`archive`=0) lbs ON INET_ATON(imb_macip.macip_ipaddr) = lbs.segment
			left JOIN host             ON imb_macip.macip_ipaddr = host.hostname		
			LEFT JOIN graph_local gl_ip ON gl_ip.snmp_index=inet_aton(imb_macip.macip_ipaddr) and gl_ip.graph_template_id=43
			LEFT JOIN plugin_fping fp ON (imb_macip.macip_ipaddr=fp.host)   
			LEFT JOIN graph_local gl_ping ON gl_ping.snmp_index=fp.id and gl_ping.graph_template_id=82
			left JOIN host   h          ON imb_devices.hostname = h.hostname
 			$sql_where
 			ORDER BY " . $sortby . " " . get_request_var('sort_direction') .
			$sql_limit;
 			
                                                                                  
         return db_fetch_assoc($query_string);
     
 }
 



function impb_bindings_request_validation() {
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
		'device_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
		'billstatus' => array(
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

	validate_store_request_vars($filters, 'sess_impb_bindings');
	/* ================= input validation ================= */
}


function impb_view_bindings() {
     global $title, $report, $impb_search_types, $impb_port_search_types, $rows_selector, $config, $binding_actions;
 
	print "<div id='element_to_pop_ping'>
			<a class='b-close'>x<a/>
			Ping Host
		  </div>
		";
		
	impb_bindings_request_validation();

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
	impb_bindings_filter();
	html_end_box(); 
 
    $sql_where = "";
    $bindings = impb_view_get_bindings_records($sql_where, true, $rows);
    $total_rows = db_fetch_cell("SELECT
             COUNT(imb_macip.device_id)
             FROM imb_macip
			 LEFT JOIN (SELECT l.segment,  v.*  FROM lb_staff l left JOIN lb_vgroups_s v ON l.vg_id = v.vg_id WHERE v.`archive`=0) lbs ON INET_ATON(imb_macip.macip_ipaddr) = lbs.segment 
             $sql_where");	
	
	$nav = html_nav_bar('impb_view_bindings.php?report=bindings', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 14, __('Bindings'), 'page', 'main');

	form_start('impb_view_bindings.php', 'chk');
	
	//print $nav;

		
	html_start_box('', '100%', '', '3', 'center', '');

 
	$display_text = array(
		'description'      => array(__('Device'), 'ASC'),
		'hostname'        => array(__('IP(имя)'), 'ASC'),
		'macip_ipaddr'      => array(__('IP Address'), 'ASC'),
		'macip_macaddr'      => array(__('MAC Address'), 'ASC'),
		'f_flat'      => array(__('Komn'), 'ASC'),
		'macip_port_view'        => array(__('Port<br>List'), 'ASC'),
		'macip_imb_status'      => array(__('Record<br>status'), 'ASC'),
		'macip_imb_action'     => array(__('Record<br>action'), 'ASC'),
		'macip_mode'     => array(__('Mode'), 'ASC'),
		'macip_may_move'     => array(__('Free'), 'DESC'),
		'macip_lastchange_date'     => array(__('Дата<br>Изменения'), 'ASC'),
		'macip_scan_date'      => array(__('Scan Date'), 'DESC'));
 
	html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false);

    $i = 0;
 	$mac_font_size=read_config_option("dimpb_mac_addr_font_size");
     if (sizeof($bindings) > 0) {
         foreach ($bindings as $binding) {
			$scan_date = $binding["macip_scan_date"];

			if ($binding["macip_active_last_poll"] == 1)  {
				$color_line_date="<span style='font-weight: bold;'>";
			}else{
				$color_line_date="";
			}			
 			
			form_alternate_row('line' . $binding["macip_id"], true);
			form_selectable_cell("<a class='linkEditMain' href='" . htmlspecialchars($webroot . 'impb_devices.php?action=edit&device_id=' . $binding["device_id"]) . "'>'" . 
 				(strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $binding["description"]) : $binding["description"]) . "</strong></a>", $binding["macip_id"]);			
				
 			form_selectable_cell($binding["hostname"], $binding["macip_id"] );
 			
			
			//ip
			form_selectable_cell("<img src='" . $config['url_path'] . "plugins/impb/images/term.png' onClick='show_ping_w(" . '"' . $binding["macip_ipaddr"] . '"' . ")' onMouseOver='style.cursor=" . '"' . "pointer" . '"' . "' align='absmiddle' /img> " .
								 "<img src='" . $config['url_path'] . "plugins/impb/images/" . $binding["sig"] . ".png' TITLE='" . $binding["sig2"] . "' align='absmiddle'><a class='inkEditMain' TITLE='" . $binding["sig2"] . ' Адр:' . $binding["f_addr"] . "' href='impb_view_info.php?report=info&amp;device_id=-1&amp;ip_filter_type_id=2&amp;ip_filter=" . $binding["macip_ipaddr"] . "&amp;mac_filter_type_id=1&amp;mac_filter=&amp;port_filter_type_id=&amp;port_filter=&amp;rows=-1&amp;filter=&amp;page=1&amp;report=info&amp;x=23&amp;y=10'>" . 
 				 (strlen(get_request_var('ip_filter')) ? preg_replace("/(" . preg_quote(get_request_var('ip_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $binding["macip_ipaddr"]) : $binding["macip_ipaddr"]) . "</a>" . ($binding["ip_local_graph_id"]==0 ? '' : " <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_ion_view.php?action=preview&style=&host_id=-1&graph_template_id=-1&rfilter=" . $binding['macip_ipaddr'] ) . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' alt='' title='View Graph' align='absmiddle'></a>") . (strlen($binding["equipm_rtr"])==0 ? '' : ' (R)') , $binding["macip_id"]);
 			
				 
			
 			form_selectable_cell("<a class='linkEditMain' href='impb_view_info.php?report=info&amp;device_id=-1&amp;ip_filter_type_id=8&amp;ip_filter=&amp;mac_filter_type_id=2&amp;mac_filter=" . $binding["macip_macaddr"] . "&amp;port_filter_type_id=&amp;port_filter=&amp;rows=-1&amp;filter=&amp;page=1&amp;report=info&amp;x=14&amp;y=6'><font size='" . $mac_font_size . "' face='Courier'>" . 
 				(strlen(get_request_var('mac_filter')) ? strtoupper(preg_replace("/(" . preg_quote(get_request_var('mac_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $binding["macip_macaddr"])) : $binding["macip_macaddr"]) . "</font></a>", $binding["macip_id"]);
 				
			form_selectable_cell((strlen(get_request_var('m_filter')) ? preg_replace("/(" . preg_quote(get_request_var('m_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $binding["f_flat"]) : $binding["f_flat"]), $binding["macip_id"] );
			
			form_selectable_cell((strlen(get_request_var('m_filter')) ? preg_replace("/(" . preg_quote(get_request_var('m_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $binding["macip_port_view"]) : " <a class='linkEditMain' href=impb_view_ports.php?report=ports&device_id=" . $binding['device_id'] . "&port_number=" . $binding["macip_port_view"]) . ">" . $binding["macip_port_view"] . 
				" <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_ion_view.php?action=preview&style=&host_id=" . $binding['cid'] . "&snmp_index=" . $binding["macip_port_view"] . "&graph_template_id=-1&rfilter=") . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' alt='' title='View Graph' align='absmiddle'></a>", $binding["macip_id"] );
			
 			form_selectable_cell(imp_convert_macip_state_2str($binding["macip_imb_status"]), $binding["macip_id"] );
 			form_selectable_cell(imp_convert_macip_action_2str($binding["macip_imb_action"], $binding["type_imb_action"]), $binding["macip_id"]  );
 			form_selectable_cell(imp_convert_macip_mode_2str_full($binding["macip_mode"], $binding["device_id"]), $binding["macip_id"]  );
 			
			form_selectable_cell(imp_convert_free_2str($binding["macip_may_move"]), $binding["macip_id"] );
						
			form_selectable_cell($binding["macip_lastchange_date"], $binding["macip_id"] );
 			
 			form_selectable_cell((strlen(get_request_var('m_filter')) ? $color_line_date . " " .preg_replace("/(" . preg_quote(get_request_var('m_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>" , $binding["macip_scan_date"]) : $color_line_date . " " . $binding["macip_scan_date"]), $binding["macip_id"] );
 			
 			
			if ($binding["macip_online"] == 1) {
 				form_checkbox_cell($binding["macip_ipaddr"], $binding["macip_id"]);
 			} else {
 				print "<td></td>";
 			}
 			form_end_row();			
         }
 
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No IMP Bindings found</em></td></tr>";
     }

	html_end_box(false);

	if (sizeof($bindings)) {
		print $nav;
	}

    impb_draw_actions_dropdown($binding_actions, "");
	form_end();
}
 
 

function form_actions_bindings() {
     global $config, $binding_actions, $fields_impb_macip_group_edit, $impb_operation_macip_types, $impb_imp_mode ;
 
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
		$device_ids=db_fetch_assoc("SELECT device_id FROM imb_macip where macip_id in (" . $str_ids . ") group by device_id;");
		$macips=db_fetch_assoc("SELECT * FROM imb_macip where macip_id in (" . $str_ids . ") ;");
		$macips_devices=dimpb_array_rekey(db_fetch_assoc("SELECT `d`.`description` as dev_name , d.*, dt.* FROM imb_macip m LEFT JOIN imb_devices d on (m.device_id=d.device_id) LEFT JOIN imb_device_types dt on (d.device_type_id = dt.device_type_id) WHERE `m`.`macip_id` in (" . $str_ids . ") GROUP by m.device_id;"), "device_id");

 		if (get_request_var('drp_action') == "1") { /* удаление записи ип-мак-порт */
			if (sizeof($macips) > 0) {
				foreach ($macips as $macip) {	
					if (isset($macips_devices[$macip["device_id"]])) {
						//$port_record = db_fetch_row ("SELECT * FROM imb_ports WHERE port_id=" . $port_name["port_id"] . ";");
						//api_imp_delete_blmacs($blmac, $blmacs_devices[$blmac["device_id"]]);
						api_imp_delete_macip($macip, $macips_devices[$macip["device_id"]], false);
					}
				}
			}			 
             //header("Location: impb_view.php");
 
        }elseif (get_request_var('drp_action') == "2") { /* изменить запись */
            			//$save_data[$cur_macip_id]["macip_id"] = form_input_validate($_REQUEST["tma_ip"], "tma_ip", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
			$macips_users = array();
		    for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 				$cur_macip_id = $selected_items[$i];
 				$macips_users[$cur_macip_id]["macip_id"] = $cur_macip_id;
 				$macips_users[$cur_macip_id]["macip_mac_adrress"] = form_input_validate(imp_translate_mac_address(get_request_var('tm_' . $cur_macip_id . '_mac_adrress')), "tm_" . $cur_macip_id . "_mac_adrress", "^(([0-9]|[a-f]|[A-F]){2}\:){5}([0-9]|[a-f]|[A-F]){2}$", false,3 );
 				$macips_users[$cur_macip_id]["macip_port"] = form_input_validate(get_request_var('tm_' . $cur_macip_id . '_port'), "tm_" . $cur_macip_id . "_port", "^([0-5]{0,1}[0-9]{1}(,|-){1})*[0-5]{0,1}[0-9]{1}$", false, 3);
 				$macips_users[$cur_macip_id]["macip_mode"] = $impb_imp_mode[form_input_validate(get_request_var('tm_' . $cur_macip_id . '_use_acl'), "tm_" . $cur_macip_id . "_use_acl", "[^0]", false, 3)];
 				//$macips[$cur_macip_id]["macip_port"] = $_POST["tm_" . $cur_macip_id . "_port"];
 		   }
 	        if (!is_error_message()) {
 				if (sizeof($macips) > 0) {
 					foreach ($macips_users as $macips_user) {	
 						//imb_change_macip_record2($macip["macip_id"], $macip["macip_mac_adrress"],$macip["macip_port"],$macip["macip_mode"]);
						imb_change_macip_record2($macips_user["macip_id"], $macips_user["macip_mac_adrress"],$macips_user["macip_port"],$macips_user["macip_mode"]);
 					}
 				}
 			}
 		}elseif (get_request_var('drp_action') == "3") { /* Групповое изменение */
 			$temp_macips_to_change = array();
 			$operation_type = form_input_validate(get_request_var('tmg_operation_type'), "tmg_operation_type", "", false, 3);
 				
 				
 				for ($i=0;($i<count($selected_items));$i++) {
 					$str_ids = $str_ids . "'" . $selected_items[$i] . "', ";
 					$row_array[$i] = $selected_items[$i];
 				}
 				$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 				
 				$temp_macips_to_change = db_fetch_assoc("SELECT imb_macip.* FROM imb_macip  left join imb_devices  on (imb_macip.device_id = imb_devices.device_id) WHERE macip_id in (" . $str_ids . ");");
 				$new_device_id = form_input_validate(get_request_var('tmg_device_id'), "tmg_device_id", "", false, 3);
 				$new_port = form_input_validate(get_request_var('tmg_port_number'), "tmg_port_number", "^([0-5]{0,1}[0-9]{1}(,|-){1})*[0-5]{0,1}[0-9]{1}$", false, 3);
 				$new_acl_mode = form_input_validate(get_request_var('tmg_use_acl'), "tmg_use_acl", "", false, 3);
 				
 				if (!is_error_message()) {
 				for($i=0;$i<=(sizeof($temp_macips_to_change)-1);$i++) {
 					$temp_macips_to_change[$i]["_change"] = 0;
 					if (get_request_var('tmg_device_id') != 0) {
 						$temp_macips_to_change[$i]["_device_id"] = $new_device_id;
 					} else {
 						$temp_macips_to_change[$i]["_device_id"] = $temp_macips_to_change[$i]["device_id"];
 					};
 					
                     if ((get_request_var('tmg_port_number') != "") && ($temp_macips_to_change[$i]["macip_port_view"] != $new_port)) {
 						$temp_macips_to_change[$i]["_port"] = $new_port;
                         $temp_macips_to_change[$i]["_change"] = $temp_macips_to_change[$i]["_change"] +1;
                     } else {
                         $temp_macips_to_change[$i]["_port"]  = $temp_macips_to_change[$i]["macip_port_view"];
                     };					
 					
 					if (get_request_var('tmg_use_acl') != 0) {
 						$temp_macips_to_change[$i]["_acl_mode"]  = $impb_imp_mode[$new_acl_mode];
                         if ($new_acl_mode != imp_convert_macip_mode_2str($temp_macips_to_change[$i]["macip_mode"], $temp_macips_to_change[$i]["device_id"] )) {
 							$temp_macips_to_change[$i]["_change"] = $temp_macips_to_change[$i]["_change"] +1;
 						}else{
 							$temp_macips_to_change[$i]["_acl_mode"] = imp_convert_macip_mode_2str($temp_macips_to_change[$i]["macip_mode"], $temp_macips_to_change[$i]["device_id"] );
 						}
 					} else {
 						$temp_macips_to_change[$i]["_acl_mode"] = imp_convert_macip_mode_2str($temp_macips_to_change[$i]["macip_mode"], $temp_macips_to_change[$i]["device_id"] );
 						//$macip_row["tmg_use_acl"];
 					}					
 				}
 					
 					
 					
 					
 					for($i=0;$i<=(sizeof($temp_macips_to_change)-1);$i++) {
 						if ($temp_macips_to_change[$i]["_device_id"] != $temp_macips_to_change[$i]["device_id"]) {
 							/*запись переноситься/дублируеться на другое устройство, значит его нужно сначал создать на новом устройстве*/
 							$rezult = imb_create_imp_record($temp_macips_to_change[$i]["_device_id"], $temp_macips_to_change[$i]["macip_macaddr"], $temp_macips_to_change[$i]["macip_ipaddr"], $temp_macips_to_change[$i]["_port"], $temp_macips_to_change[$i]["_acl_mode"]);
 							if (($operation_type == 2) & ($rezult == "OK" )) { /*Операция с перемещением, значит удаляем исходную запись*/
 								api_imp_delete_macip($selected_items[$i], false);
 							};//1111/					
 							
 						} elseif ($temp_macips_to_change[$i]["_change"] > 0) {/*меняем порт или режим у текущей записи*/
 								imb_change_macip_record2 ($temp_macips_to_change[$i]["macip_id"], $temp_macips_to_change[$i]["macip_macaddr"], $temp_macips_to_change[$i]["_port"], $temp_macips_to_change[$i]["_acl_mode"]);
 						/**/
 						};
 					}
 				}
 			
 			
 
 		}elseif (get_request_var('drp_action') == "4") { /* создание привязки */
 			$save_data = array();
 			$save_data["tma_device_id"] = form_input_validate(get_request_var('tma_device_id'), "tma_device_id", "[^0]", false, 3);
 			$save_data["tma_ip"] = form_input_validate(impb_translate_ip_address(get_request_var('tma_ip')), "tma_ip", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
 			//'^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$',
 			$save_data["tma_mac"] = form_input_validate(imp_translate_mac_address(get_request_var('tma_mac')), "tma_mac", "^(([0-9]|[a-f]|[A-F]){2}\:){5}([0-9]|[a-f]|[A-F]){2}$", false, 3);
 			$save_data["tma_port_number"] = form_input_validate(get_request_var('tma_port_number'), "tma_port_number", "^([0-5]{0,1}[0-9]{1}(,|-){1})*[0-5]{0,1}[0-9]{1}$", false, 3);
 			$save_data["tma_mode"] = $impb_imp_mode[form_input_validate(get_request_var('tma_use_acl'), "tma_use_acl", "[^0]", false, 3)] ;
 			if (!is_error_message()) {
 
 				$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 					imb_create_imp_record($save_data["tma_device_id"] ,$save_data["tma_mac"], $save_data["tma_ip"], $save_data["tma_port_number"] , $save_data["tma_mode"]);
 			}
 		}elseif (get_request_var('drp_action') == "5") { /* освобождение записи */
             // for ($i=0;($i<count($selected_items));$i++) {
                 // /* ================= input validation ================= */
                 // input_validate_input_number($selected_items[$i]);
                 // /* ==================================================== */
                 // api_imp_change_free_macip($selected_items[$i], false);
             // }
			db_execute("UPDATE `imb_macip` SET `macip_may_move`=IF(`macip_may_move`=1,0,1) where `macip_id` in (" . $str_ids . ");");
             //header("Location: impb_view.php");
 
        }elseif (get_request_var('drp_action') == "6") { /* СМС рассылка */

 		$macips_mobile_rows=db_fetch_assoc("SELECT mobile, i.macip_ipaddr, CONCAT(ag_num,'  ', REPLACE(f_addr , 'Россия,обл Самарская,,г Кинель,', '')) as addr FROM lb_vgroups_s l " .
 			 " LEFT JOIN lb_staff lb ON (lb.vg_id=l.vg_id) " .
 			 " LEFT JOIN imb_macip i ON (i.macip_ipaddr=lb.ip) " .
 			 " WHERE i.macip_id in (" . $str_ids . ");");
		$mobils = "";
		foreach ($macips_mobile_rows as $macips_mobile_row) {
			$mobils = $mobils . " " . $macips_mobile_row["mobile"] . ", ";		
		}		
		
			set_request_var('ar_ssms_num', serialize($mobils));
			$_SESSION["ar_ssms_num"] = serialize($mobils);
 
 		}
 		 if (isset_request_var('save_config')) { 
 				imp_save_config_main($save_data["tma_device_id"]);
 		}
 	
 	if (!is_error_message()) {
		if ($_POST["drp_action"] == "6"){
			header("Location: ../gammu/gammu_view_send.php?header=false&report=send");
		}else{
			header("Location: impb_view_bindings.php?header=false");
		}
 	}else{
 		header("Location: impb_view_bindings.php?header=false&action=" . get_request_var('action') . "&drp_action=" . get_request_var('drp_action') . "&post_error=" . serialize($selected_items));
 		$_REQUEST["selected_items"]="";
 	}
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = ""; $post_if_error = ""; $colspan = 2;
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     if (!isset_request_var('post_error')) { /*Если установлено это значение - значит страница перезагружаеться из-за ошибки при вводе, и данные нужно брать не из POST, а из спец. переменной.*/
 		foreach ($_POST as $var => $val) {
 	        if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
 	            /* ================= input validation ================= */
 	            input_validate_input_number($matches[1]);
 	            /* ==================================================== */
 
 	            $row_info = db_fetch_row("SELECT imb_macip.*, imb_devices.hostname, imb_devices.description  FROM imb_macip left join imb_devices on (imb_macip.device_id = imb_devices.device_id) WHERE imb_macip.macip_id=" . $matches[1]);
 				$row_list .= "<li>" . $row_info["description"] . "      IP:" . $row_info["macip_ipaddr"] . "    MAC:" . $row_info["macip_macaddr"] . "      PORT:" . $row_info["macip_port_view"] . "      Режим:" . imp_convert_macip_mode_2str($row_info["macip_mode"], $row_info["device_id"]) . "<br>";
 	            $row_array[$i] = $matches[1];
 				$row_ids = $row_ids . "'" . $matches[1] . "', ";
 	        $i++;
			}                                  
 	    }
 	}else{
 		$row_array=unserialize(stripslashes(get_request_var('post_error')));
 		if (isset($row_array) && is_array($row_array) && (count($row_array) > 0)) {
 			foreach ($row_array as $row_id) {
 	            $row_info = db_fetch_row("SELECT imb_macip.*, imb_devices.hostname, imb_devices.description FROM imb_macip left join imb_devices on (imb_macip.device_id = imb_devices.device_id) WHERE imb_macip.macip_id=" . $row_id);
 				$row_list .= "<li>" . $row_info["description"] . "      IP1:" . $row_info["macip_ipaddr"] . "    MAC:" . $row_info["macip_macaddr"] . "      PORT:" . $row_info["macip_port_view"] . "<br>";
 				$row_ids = $row_ids . "'" . $row_id . "', ";		
 			}
 		}
 	}
 	
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 
 	top_header();

	form_start('impb_view_bindings.php?header=false');

	html_start_box($binding_actions[get_request_var('drp_action')], '60%', '', '3', 'center', '');    
	
	
		
	if ((!isset($row_array) or (!sizeof($row_array))) && (((isset_request_var('drp_action')) && (get_request_var('drp_action') != "4")) || ((isset_request_var('post_error') && (isset_request_var('drp_action')) && (get_request_var('drp_action') != "4"))))) {
		print "<tr><td class='even'><span class='textError'>" . __('You must select at least one row.') . "</span></td></tr>\n";
		$save_html = "";
	}else{
		
		$save_html = "<input type='submit' value='" . __('Yes') . "' name='save'>";	
 	
     if ((isset_request_var('drp_action')) && (get_request_var('drp_action') == "1")) {  /* удаление записей */
			print "<tr>
				<td class='textArea'>
					<p>" . __('Are you sure you want to DEL the following rows?') . "</p>
					<p><ul>$row_list</ul></p>
				</td>
			</tr>";
     } elseif (((isset_request_var('drp_action')) && (get_request_var('drp_action') == "2")) || ((isset_request_var('post_error')) && (isset_request_var('drp_action')) && (get_request_var('drp_action') == "2"))) { /*Изменение записи*/
 		$macips_rows=db_fetch_assoc("SELECT imb_macip.*,  imb_devices.hostname, imb_devices.description" . 
 			" FROM imb_macip left join imb_devices on (imb_macip.device_id = imb_devices.device_id) " . 
 			" WHERE imb_macip.macip_id in (" . $row_ids . ");");
 
 		html_start_box("Для изменения записи IP-MAC-PORT проверьте/измените следующие поля.", "100%", 'true', "", "center", "");
		
 
 	    html_header(array("","Host<br>Description","Hostname<br>", "Номер порта","Описание порта","IP-адресс", "MAC-адресс",  "Порт", "Режим"));
 	    $i = 0;
 	    if (sizeof($macips_rows) > 0) {
 	        foreach ($macips_rows as $macips_row) {
 				$macips_id = $macips_row["macip_id"];
				form_alternate_row();
 					if (imp_convert_macip_mode_2str($macips_row["macip_mode"], $macips_row["device_id"] ) == "ACL")  {
 						$int_default_mode = 2;
 					}else{
 						$int_default_mode = 1;
 					}			
 	                ?>
 					<td><?php form_hidden_box("tm_" . $macips_id . "_macip_id", $macips_id, "form_default_value");?></td>
 					<td><?php print $macips_row["description"];?></td>
 					<td><?php print $macips_row["hostname"];?></td>
 					<td><?php print $macips_row["macip_port_view"];?></td>
 					<td><?php print $macips_row["macip_port_list"];?></td>
 					<td><?php print $macips_row["macip_ipaddr"];?></td>
 					<td><?php form_text_box("tm_" . $macips_id . "_mac_adrress", $macips_row["macip_macaddr"], "", 17, 15, "text", 1) ;?></td>
 					<td><?php form_text_box("tm_" . $macips_id . "_port", $macips_row["macip_port_view"], "", 250, 10, "text", 1) ;?></td>
 					<td><?php form_dropdown("tm_" . $macips_id . "_use_acl",  $impb_imp_mode, "", "", $int_default_mode, "", "") ;?></td>				
 	            </tr>
 	            <?php
 	        }
 	    }
		$colspan = 9;

 
 	} elseif (((isset_request_var('drp_action')) && (get_request_var('drp_action') == "3")) || ((isset_request_var('post_error')) && (isset_request_var('drp_action')) && (get_request_var('drp_action') == "3"))) { /*Групповое изменение параметра*/
         print "<tr>
                 <td class='textArea'>
                     <p>Проверьте группу записей, у которых необходимо произвести изменения</p>
                     <p>$row_list</p>
                 </td><td></td>
             </tr>\n
             ";	
 		html_start_box("Проверьте значения изменяемых полей", "100%", '', "", "center", "");
 
 
 	    html_header(array("Устройство назначения", "Номер порта","Режим","Тип операции"));
 		
 			?>
 				<td><?php form_dropdown("tmg_device_id", db_fetch_assoc("select device_id as id, description as name from imb_devices where snmp_status <> 1 order by INET_ATON(hostname) ASC"), "name", "id", "none", "Не изменять", "") ;?></td>
 				<td><?php form_text_box("tmg_port_number", "", "", 250, 15, "text", 1) ;?></td>
 				<td><?php form_dropdown("tmg_use_acl",  $impb_imp_mode, "", "", 3, "Не изменять", "") ;?></td>				
 				<td><?php form_dropdown("tmg_operation_type", $impb_operation_macip_types, "", "", "none", "", "") ;?></td>
 			</tr>
 			<?php
 			
 			
		$colspan = 4;
 	
 	} elseif (((isset_request_var('drp_action')) && (get_request_var('drp_action') == "4")) || ((isset_request_var('post_error')) && (isset_request_var('drp_action')) && (get_request_var('drp_action') == "4"))) { /*Добавление привязки*/
 
 		html_start_box("Проверьте значения полей", "100%", '', "", "center", "");
 
 
 	    html_header(array("Устройство назначения","Номер порта", "IP-адрес", "MAC-адрес", "Режим"));
 		
 			?>
 				<td><?php form_dropdown("tma_device_id", db_fetch_assoc("select device_id as id, description as name from imb_devices order by INET_ATON(hostname) ASC"), "name", "id", "none", "Выберите ....", "") ;?></td>
 				<td><?php form_text_box("tma_port_number", "", "", 250, 12, "text", 1) ;?></td>
 				<td><?php form_text_box("tma_ip",read_config_option("dimpb_default_ip_mask") , "", 15, 12, "text", 1) ;?></td>
 				<td><?php form_text_box("tma_mac", "00:00:00:00:00:00", "", 17, 15, "text", 1) ;?></td>
 				<td><?php form_dropdown("tma_use_acl",  $impb_imp_mode, "", "", "ARP", "", "") ;?></td>				
 			</tr>
 			<?php
		$colspan = 5;
		
 	}elseif ((isset_request_var('drp_action')) && (get_request_var('drp_action') == "5"))  {  /* Освобождение записей */
         print "    <tr>
                 <td class='textArea'>
                     <p>Подтверждаете изменение следующих записей ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
    }elseif ((isset_request_var('drp_action')) && (get_request_var('drp_action') == "6"))  {  /* СМС Рассылка */
 		$macips_mobile_rows=db_fetch_assoc("SELECT mobile, i.macip_ipaddr, CONCAT(ag_num,'  ', REPLACE(f_addr, 'Россия,обл Самарская,,г Кинель,', '')) as addr FROM lb_vgroups_s l " .
 			 " LEFT JOIN lb_staff lb ON (lb.vg_id=l.vg_id) " .
 			 " LEFT JOIN imb_macip i ON (i.macip_ipaddr=lb.ip) " .
 			 " WHERE i.macip_id in (" . $row_ids . ");");
		$row_list = "";
		$mobils = "";
		foreach ($macips_mobile_rows as $macips_mobile_row) {
			//$row_info = db_fetch_row("SELECT imb_macip.*, imb_devices.hostname, imb_devices.description FROM imb_macip left join imb_devices on (imb_macip.device_id = imb_devices.device_id) WHERE imb_macip.macip_id=" . $row_id);
			$row_list .= "<li>IP:" . $macips_mobile_row["macip_ipaddr"] . "      Mobile:" . $macips_mobile_row["mobile"] . "    DESC:" . $macips_mobile_row["addr"] . "<br>";
			$mobils = $mobils . " " . $macips_mobile_row["mobile"] . ", ";		
		} 
		$mobils = substr($mobils, 0, strlen($mobils) -2);		
		print "    <tr>
                 <td class='textArea' >
                     <p>Подтверждаете создание рассылки для следующих записей ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
     };
	};

 		if ((isset_request_var('drp_action')) && (get_request_var('drp_action') != "6")) {
			print "<tr>
					<br>
					<td colspan='2' align='left' bgcolor='#eaeaea'>";
						form_checkbox("save_config", "", "Сохранить конфигурацию ?", "");
			print "</td>
				</tr>\n";
		}else{
			$save_html = "<input type='submit' name='Save' value='Перейти к отравке'>";
		}



	print "<tr>
		<td colspan='$colspan' align='right' class='saveRow'>
			<input type='hidden' name='action' value='actions_'>
			<input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
			<input type='hidden' name='post_if_error' value='" . $post_if_error . "'>
			<input type='hidden' name='drp_action' value='" . get_request_var('drp_action') . "'>" . (strlen($save_html) ? "
			<input type='button' name='cancel' onClick='cactiReturnTo()' value='" . __('No') . "'>
			$save_html" : "<input type='button' onClick='cactiReturnTo()' name='cancel' value='" . __('Return') . "'>") . "
		</td>
	</tr>";
 	

	html_end_box();

	form_end();

	bottom_footer();



 }
 
function impb_bindings_filter() {
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
					<td>
						<?php print __('Bill Status');?>
					</td>
					<td>
						<select id='billstatus' onChange='applyFilter()'>
							 <option value="-1"<?php if (get_request_var('billstatus') == "-1") {?> selected<?php }?>><?php print __('Any');?></option>
							 <option value="0"<?php if (get_request_var('billstatus') == "0") {?> selected<?php }?>><?php print __('Положительный баланс');?></option>
							 <option value="1"<?php if (get_request_var('billstatus') == "1") {?> selected<?php }?>><?php print __('Отрицательный баланс');?></option>
							 <option value="3"<?php if (get_request_var('billstatus') == "3") {?> selected<?php }?>><?php print __('Заблокирован Адм');?></option>
							 <option value="4"<?php if (get_request_var('billstatus') == "4") {?> selected<?php }?>><?php print __('Несуществует');?></option>
							 <option value="5"<?php if (get_request_var('billstatus') == "5") {?> selected<?php }?>><?php print __('Служебный');?></option>
							 <option value="6"<?php if (get_request_var('billstatus') == "6") {?> selected<?php }?>><?php print __('Оборудование по акции');?></option>							
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
			strURL  = urlPath+'plugins/impb/impb_view_bindings.php?header=false';
			strURL += '&device_id=' + $('#device_id').val();
			strURL += '&billstatus=' + $('#billstatus').val();
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
			strURL  = urlPath+'plugins/impb/impb_view_bindings.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}

		function clearPort() {
			strURL  = urlPath+'plugins/impb/impb_view_bindings.php?header=false&port_filter_type_id=1&port_filter=';
			strURL += '&device_id=' + $('#device_id').val();
			strURL += '&billstatus=' + $('#billstatus').val();
			strURL += '&ip_filter_type_id=' + $('#ip_filter_type_id').val();
			strURL += '&ip_filter=' + $('#ip_filter').val();
			strURL += '&mac_filter_type_id=' + $('#mac_filter_type_id').val();
			strURL += '&mac_filter=' + $('#mac_filter').val();
			strURL += '&filter=' + $('#filter').val();
			strURL += '&rows=' + $('#rows').val();
			loadPageNoHeader(strURL);
		}
		
		function exportRows() {
			strURL  = urlPath+'plugins/impb/impb_view_bindings.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_view_bindings.php?import=true';
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
