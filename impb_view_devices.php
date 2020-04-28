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

 $title = __('IMPB - Interfaces View');
 
 //***********************************************************
 $device_actions = array(
    5 => __('Обновить конфигурацию'),
	7 => __('Сохранить конфигурацию')
 	);

/* check actions */
switch (get_request_var('action')) {
	case 'actions_':
		form_actions_devices();

		break;
	default:
		impb_redirect();
		general_header();
		impb_view_devices();
		bottom_footer();
		break;
}

	

 
function host_device_query() {
 	/* ================= input validation ================= */
 	input_validate_input_number(get_request_var("id"));
 	input_validate_input_number(get_request_var("host_id"));
 	/* ==================================================== */
 
 	run_poller_impb(get_request_var('host_id'));
}
 

function impb_view_get_device_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
     $device_type_info = db_fetch_row("SELECT * FROM imb_devices WHERE device_type_id = '" . get_request_var('device_type_id') . "'");
 
         if (get_request_var('device_type_id') == 0) {
             $device_type_info = array("device_type_id" => 0, "description" => "Unknown Device Type");
         }
 
     /* form the 'where' clause for our main sql query */
     $sql_where = "WHERE (imb_devices.hostname LIKE '%" . get_request_var('filter') . "%' OR " .
                     "imb_devices.description LIKE '%" . get_request_var('filter') . "%')";
 
     if (sizeof($device_type_info)) {
         $sql_where .= " AND (imb_devices.device_type_id=" . $device_type_info["device_type_id"] . ")";
     }
 
     if (get_request_var('status') == "-1") {
         /* Show all items */
     }elseif (get_request_var('status') == "-2") {
         $sql_where .= " AND (imb_devices.disabled='on')";
     }else {
         $sql_where .= " AND (imb_devices.snmp_status=" . get_request_var('status') . ") AND (imb_devices.disabled = '')";
     }
 
	$sql_order = get_order_string();
	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}	 

	
	$sortby = get_request_var('sort_column');
 	if ($sortby=="hostname") {
 		$sortby = "INET_ATON(hostname)";
 	}
	
	//tabbed interface
	if (strlen(get_request_var('dtab'))) {
        $int_tab = intval(get_request_var('dtab'));

		if ($int_tab == 1) {
			$sql_where .= " ";
		}elseif ($int_tab == 2) {
			$tabs_dimpb = db_fetch_cell("SELECT GROUP_CONCAT(`device_id` SEPARATOR ', ') FROM `imb_blmacs`;");
			if (isset($tabs_dimpb)){
				$sql_where .= " AND imb_devices.device_id IN (" . $tabs_dimpb . ") ";
			}else{
				$sql_where .= " AND imb_devices.device_id IN (0) ";
			}
		}elseif ($int_tab > 10) {
			$tabs_dimpb = db_fetch_cell("SELECT GROUP_CONCAT(`dev_id` SEPARATOR ', ') FROM `imb_tab_dev` WHERE `tab_id`=" . $int_tab . " group by tab_id;");
			$sql_where .= " AND imb_devices.device_id IN (" . $tabs_dimpb . ") ";
		}
		

    }
	
	 
	 
     $sql_query = "SELECT
        imb_devices.device_id,
        imb_device_types.description as dev_type_description,
        imb_devices.description,
		imb_devices.order_id,
		imb_devices.color_row,
        imb_devices.hostname,
        imb_devices.snmp_get_community,
        imb_devices.snmp_get_version,
 		imb_devices.snmp_get_username,
 		imb_devices.snmp_get_password,
        imb_devices.snmp_set_community,
        imb_devices.snmp_set_version,
 		imb_devices.snmp_set_username,
 		imb_devices.snmp_set_password,
        imb_devices.snmp_port,
        imb_devices.snmp_timeout,
        imb_devices.snmp_retries,
        imb_devices.snmp_status,
        imb_devices.disabled,
 		imb_devices.enable_acl_mode,
 		imb_devices.enable_log_trap,		
        imb_devices.ip_mac_total,
 		imb_devices.count_unsaved_actions,
        imb_devices.ip_mac_blocked_total,
 		imb_devices.ports_total,
        imb_devices.ports_enable_total,
 		imb_devices.ports_enable_zerroip_total,
        imb_devices.ports_offline_total,
        imb_devices.ports_offline_enable_total,
 		imb_devices.ports_offline_enable_zerroip_total,
        imb_devices.ip_mac_offline_total,
 		imb_devices.ip_mac_blocked_offline_total,
        imb_devices.ports_active,
        imb_devices.last_rundate,
        imb_devices.last_runmessage,
        imb_devices.last_runduration
        FROM imb_device_types
        RIGHT JOIN imb_devices ON imb_device_types.device_type_id = imb_devices.device_type_id
        $sql_where
		$sql_order
		$sql_limit";
 

 
     return db_fetch_assoc($sql_query);
 }
 
 
function impb_devices_request_validation() {
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
			'default' => 'device_id',
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
		'dtab' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),			
	);

	validate_store_request_vars($filters, 'sess_impb_v_device');
	/* ================= input validation ================= */
}



function impb_view_devices() {
	global $title, $impb_search_types, $impb_device_types, $rows_selector, $config, $device_actions;
 
    
	impb_devices_request_validation();

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
	impb_device_filter();
	html_end_box(); 

	impb_group_tabs();
	
    $sql_where = "";
    $devices = impb_view_get_device_records($sql_where, true, $rows);
    $total_rows = db_fetch_cell("SELECT
         COUNT(imb_devices.device_id)
         FROM imb_devices
         $sql_where");	
	
	$nav = html_nav_bar('impb_view_devices.php?report=devices', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 14, __('Devices'), 'page', 'main');

	form_start('impb_view_devices.php', 'chk');
	
	print $nav;

	html_start_box('', '100%', '', '3', 'center', '');
	
	
 	$display_text = array(
 		"description" => array(__('Устройство'), 'ASC'),
		"order_id" => array(__('ord'), 'ASC'),
 		"snmp_status" => array(__('Статус'), 'ASC'),
 		"enable_acl_mode" => array(__('ARP<br>ACL'), 'ASC'),
 		"enable_log_trap" => array(__('Log<br>Trap'), 'ASC'),
 		"disabled" => array(__('Enable'), 'ASC'),
 		"hostname" => array(__('IP-Адрес'), 'ASC'),
 		"dev_type_description" => array(__('Тип<br>устройства'),'DESC'),
 		//"count_unsaved_actions" => array(__('Нес.<br>опер.'),'DESC'),
 		"ip_mac_total" => array(__('Всего записей<br>IP-MAC-Port'),'DESC'),
 		"ip_mac_blocked_total" => array(__('Блоки'),'DESC'),
 		"ports_total" => array(__('Порты'),'DESC'),
 		"ports_enable_total" => array(__('Портов с<br>привязкой'),'DESC'),
 		"ports_enable_zerroip_total" => array(__('Zerro<br>IP(Vista)'), 'DESC'),
 		"last_rundate" => array(__('Время<br>последнего<br>опроса'),'DESC'),
 		"last_runduration" => array(__('Опрос'),'DESC'),
 		" " => array(__(' '),'DESC'));

	html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false);


 	$devices_sum = db_fetch_row("SELECT        count(imb_devices.device_id),
               sum(imb_devices.ip_mac_total),
               sum(imb_devices.count_unsaved_actions),
               sum(imb_devices.ip_mac_blocked_total),
               sum(imb_devices.ports_total),
               sum(imb_devices.ports_enable_total),
               sum(imb_devices.ports_enable_zerroip_total),
               sum(imb_devices.ports_offline_total),
               sum(imb_devices.ports_offline_enable_total),
               sum(imb_devices.ports_offline_enable_zerroip_total),
               sum(imb_devices.ip_mac_offline_total),
               sum(imb_devices.ip_mac_blocked_offline_total),
               sum(imb_devices.ports_active),
               sum(imb_devices.last_runduration)
 			FROM imb_device_types
 			RIGHT JOIN imb_devices ON imb_device_types.device_type_id = imb_devices.device_type_id
 			$sql_where
 			ORDER BY imb_devices.hostname LIMIT 0,45");
 	
    $i = 0;
 	$str_count_ipmac_total = 0;
 	$str_blmac_blmac_total = 0;
 	$str_count_ports_total = 0;
 	$str_count_ports_en_total = 0;
 	$str_count_ports_zerr_en_total = 0;	
    if (sizeof($devices) > 0) {
         foreach ($devices as $device) {
 			$bgc = db_fetch_cell("SELECT hex FROM colors WHERE id='" . $device["color_row"] . "'");
			form_alternate_row('line' . $device["device_id"], true);
                 if ($device["snmp_status"] == 3) {
 					$str_count_ipmac = $device["ip_mac_total"];
 					$str_blmac_blmac = $device["ip_mac_blocked_total"];
 					$str_count_ports = $device["ports_total"];
 					$str_count_ports_en = $device["ports_enable_total"];
 					$str_count_ports_zerr_en = $device["ports_enable_zerroip_total"];
					$str_count_ipmac_total = $str_count_ipmac_total + $str_count_ipmac;
					$str_blmac_blmac_total = $str_blmac_blmac_total+$str_blmac_blmac;
					$str_count_ports_total = $str_count_ports_total+$str_count_ports;
					$str_count_ports_en_total = $str_count_ports_en_total+$str_count_ports_en;
					$str_count_ports_zerr_en_total = $str_count_ports_zerr_en_total+$str_count_ports_zerr_en;					
 				} else {
					$str_count_ipmac_total = $device["ip_mac_offline_total"];
					$str_blmac_blmac_total = $device["ip_mac_blocked_offline_total"];
					$str_count_ports_total = $device["ports_offline_total"];
					$str_count_ports_en_total = $device["ports_offline_enable_total"];
					$str_count_ports_zerr_en_total = $device["ports_offline_enable_zerroip_total"];
					$str_count_ipmac = "[" . $device["ip_mac_offline_total"] . "]";
 					$str_blmac_blmac = "[" . $device["ip_mac_blocked_offline_total"] . "]";
 					$str_count_ports = "[" . $device["ports_offline_total"] . "]";
 					$str_count_ports_en = "[" . $device["ports_offline_enable_total"] . "]";
 					$str_count_ports_zerr_en = "[" . $device["ports_offline_enable_zerroip_total"] . "]";				
 				}

 				//form_selectable_cell("<a class='linkEditMain' href='host.php?action=edit&id=" . $host["id"] . "'>" .
 				//(strlen($_REQUEST["filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $host["description"]) : $host["description"]) . "</a>", $host["id"], 250);
 				
 				form_selectable_cell("<a class='linkEditMain' href='" . htmlspecialchars($webroot . 'impb_devices.php?action=edit&device_id=' . $device["device_id"]) . "'>'" . 
 					(strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $device["description"]) : $device["description"]) . "</a>", $device["device_id"],250,"background-color: #" . $bgc . ";");
 				form_selectable_cell( $device["order_id"], $device["order_id"] ,"","background-color: #" . $bgc . ";");
				
 				form_selectable_cell(get_colored_device_status(($device["disabled"] == "on" ? true : false), $device["snmp_status"]), $device["device_id"] ,"","background-color: #" . $bgc . ";");
 				form_selectable_cell(get_colored_status($device["enable_acl_mode"]), $device["device_id"] );
 				form_selectable_cell(get_colored_status($device["enable_log_trap"]), $device["device_id"] );
 				form_selectable_cell(get_colored_device_status(($device["disabled"] == "on" ? true : false), $device["snmp_status"]), $device["device_id"] );
 				form_selectable_cell((strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $device["hostname"]) : $device["hostname"]), $device["device_id"]);
 				
 				form_selectable_cell($device["dev_type_description"], $device["device_id"],"","background-color: #" . $bgc . ";" );
 
 
 				form_selectable_cell("<a class='linkEditMain' href='impb_view_bindings.php?report=bindings&device_id=+" . $device["device_id"] . "&ip_filter_type_id=1&ip_filter=&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=bindings&x=22&y=4'>" . $str_count_ipmac . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' href='impb_view_blmacs.php?report=blmacs&b_device_id=+" . $device["device_id"] . "&ip_filter_type_id=1&ip_filter=&b_port_filter=&b_mac_filter_type_id=1&b_mac_filter=&port_filter_type_id=&port_filter=&b_rows_selector=-1&b_filter=&b_page=1&report=blmacs&x=15&y=8'>" . $str_blmac_blmac . "</a>", $device["device_id"]);
 				
 
 
 				form_selectable_cell("<a class='linkEditMain' href='impb_view_ports.php?report=ports&device_type_id=-1&device_id=+" . $device["device_id"] . "&status=-1&zerro_status=-1&filter=&page=1&report=ports&x=11&y=7'>" . $str_count_ports . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' href='impb_view_ports.php?report=ports&device_type_id=-1&device_id=+" . $device["device_id"] . "&status=2&zerro_status=-1&filter=&page=1&report=ports&x=11&y=7'>" . $str_count_ports_en . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' title='title-title
 				second' href='impb_view_ports.php?report=ports&device_type_id=-1&device_id=+" . $device["device_id"] . "&status=2&zerro_status=1&filter=&page=1&report=ports&x=11&y=7'>" . $str_count_ports_zerr_en . "</a>", $device["device_id"]);
 				
 				form_selectable_cell(imb_fromat_datetime($device["last_rundate"]), $device["device_id"] );
 				
 				form_selectable_cell(number_format($device["last_runduration"]), $device["device_id"] );
 				
 				form_selectable_cell("<a class='linkEditMain' href='impb_view_devices.php?action=actions_&drp_action=5&id=1&selected_items=" . serialize(array(1=>$device["device_id"])) . "'><img src='../../images/reload_icon_small.gif' alt='Reload Data Query' align='absmiddle'></a>", $device["device_id"]);
 
 				form_checkbox_cell($device["description"], $device["device_id"]);
 
 				?>
             </tr>
             <?php
 			form_end_row();
         }
 
 		?>
 			<tr>
 				<td>ИТОГО:</td>
 				<td></td>
				<td></td>
 				<td></td>
 				<td></td>
 				<td></td>
 				<td></td>
 				<td></td>
 
                 <td >
					 <a class="linkEditMain" href="impb_view_bindings.php?report=bindings&device_id=-1&ip_filter_type_id=1&ip_filter=&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1"><?php print $str_count_ipmac_total ;?></a>
                 </td>
                 <td >
                     <a class="linkEditMain" href="impb_view_blmacs.php?device_id=-1&ip_filter_type_id=1&ip_filter=&port_filter=&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=blmacs&x=15&y=8"><?php print $str_blmac_blmac_total ;?></a>
                 </td >	
                 <td >
                     <a class="linkEditMain" href="impb_view_ports.php?device_type_id=-1&device_id=-1&status=-1&filter=&page=1&report=ports&x=11&y=7"><?php print $str_count_ports_total;?></a>
                 </td >
                 <td >
                     <a class="linkEditMain" href="impb_view_ports.php?device_type_id=-1&device_id=-1&status=2&zerro_status=-1&filter=&page=1&report=ports&x=11&y=7"><?php print $str_count_ports_en_total ;?></a>
                 </td > 
                 <td >
                     <a class="linkEditMain" href="impb_view_ports.php?device_type_id=-1&device_id=-1&status=2&zerro_status=1&filter=&page=1&report=ports&x=11&y=7"><?php print $str_count_ports_zerr_en_total;?></a>
                 </td > 				
 			</tr>
 			<?php
 		
 		
 		
 		
         /* put the nav bar on the bottom as well */
     }else{
         print "<tr><td><em>No D-Link IP-MAC-Port Binding Devices</em></td></tr>";
     }
    html_end_box(false);
	if (sizeof($devices)) {
		print $nav;
	}	
 	impb_draw_actions_dropdown($device_actions, "");
	form_end();
 }
 
 

 function form_actions_devices() {
	 global $config, $device_actions;
 
 	/* ================= input validation ================= */
	get_filter_request_var('drp_action');
	/* ==================================================== */
	 
 
     /* if we are to save this form, instead of display it */
     if (isset_request_var('selected_items')) {
        $selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
         if (get_request_var('drp_action') == "5") { /* Опросить устройство */
			foreach ($selected_items as $selected_item) {
 				run_poller_impb($selected_item);
             }
             header("Location: impb_view_devices.php?header=false");
 
         } elseif (get_request_var('drp_action') == "7") { /* Сохранить конфигурацию */
            foreach ($selected_items as $selected_item) {
 				imp_save_config_main($selected_item);
             }		
 		}
		header("Location: impb_view_devices.php?header=false");
        exit;
     }
 
     /* setup some variables */
     $device_list = ""; $i = 0;
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     foreach ($_POST as $var => $val) {
         if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
             /* ================= input validation ================= */
             input_validate_input_number($matches[1]);
             /* ==================================================== */
 			$device_info = db_fetch_row("SELECT hostname, description FROM imb_devices WHERE device_id=" . $matches[1]);
 			$device_list .= "<li>" . $device_info["description"] . " (" . $device_info["hostname"] . ")<br>";
 			$device_array[$i] = $matches[1];
         $i++;
		 }                                  
 
         
     }
 
 	top_header();

	form_start('impb_view_devices.php?header=false');

	html_start_box($device_actions[get_request_var('drp_action')], '60%', '', '3', 'center', '');


	if (!isset($device_array) or (!sizeof($device_array))) {
		print "<tr><td class='even'><span class='textError'>" . __('You must select at least one device.') . "</span></td></tr>\n";
		$save_html = "";
	}else{
	
		$save_html = "<input type='submit' value='" . __('Yes') . "' name='save'>";	
		
		if (get_request_var('drp_action') == "5") {  /* Update Info */
			print "<tr>
				<td class='textArea'>
					<p>" . __('Are you sure you want to update info on the following device?') . "</p>
					<p><ul>$device_list</ul></p>
				</td>
			</tr>";			
		}elseif (get_request_var('drp_action') == "7") { /*Сохранить конфигурацию*/
			print "<tr>
				<td class='textArea'>
					<p>" . __('Are you sure you want to save config on the following device?') . "</p>
					<p><ul>$device_list</ul></p>
				</td>
			</tr>";				 
		}
	}

 	

	print "<tr>
		<td colspan='2' align='right' class='saveRow'>
			<input type='hidden' name='action' value='actions_'>
			<input type='hidden' name='selected_items' value='" . (isset($device_array) ? serialize($device_array) : '') . "'>
			<input type='hidden' name='drp_action' value='" . get_request_var('drp_action') . "'>" . (strlen($save_html) ? "
			<input type='button' name='cancel' onClick='cactiReturnTo()' value='" . __('No') . "'>
			$save_html" : "<input type='button' onClick='cactiReturnTo()' name='cancel' value='" . __('Return') . "'>") . "
		</td>
	</tr>";
 	

	html_end_box();

	form_end();

	bottom_footer();
	
}
 
 



function impb_device_filter() {
	global $item_rows;

	?>
	<td width="100%" valign="top"><?php imp_display_output_messages();?>
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
			strURL  = urlPath+'plugins/impb/impb_view_devices.php?header=false';
			strURL += '&status=' + $('#status').val();
			strURL += '&device_type_id=' + $('#device_type_id').val();
			strURL += '&filter=' + $('#filter').val();
			strURL += '&rows=' + $('#rows').val();
			loadPageNoHeader(strURL);
		}

		function clearFilter() {
			strURL  = urlPath+'plugins/impb/impb_view_devices.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}

		function exportRows() {
			strURL  = urlPath+'plugins/impb/impb_view_devices.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_view_devices.php?import=true';
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
 
 
 
 
 


?>
