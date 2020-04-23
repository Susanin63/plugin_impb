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

 $title = __('IMPB - MT Info');
 
 $recentmacs_actions = array(
 	1 => "Удалить запись",
 	2 => "Прописать запись"
 	); 

/* check actions */
switch (get_request_var('action')) {
	default:
		impb_redirect();
		general_header();
		impb_view_recentmacs();
		bottom_footer();
		break;
}

 



function impb_view_get_recentmacs_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
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
                 $sql_where .= " imb_mactrack_recent_ports.mac_address='" . get_request_var('mac_filter') . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_mactrack_recent_ports.mac_address LIKE '%%" . get_request_var('mac_filter') . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_mactrack_recent_ports.mac_address LIKE '" . get_request_var('mac_filter') . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_mactrack_recent_ports.mac_address NOT LIKE '" . get_request_var('mac_filter') . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_mactrack_recent_ports.mac_address NOT LIKE '" . get_request_var('mac_filter') . "%%'";
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
                 $sql_where .= " imb_mactrack_recent_ports.ip_address='" . get_request_var('ip_filter') . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_mactrack_recent_ports.ip_address LIKE '%%" . get_request_var('ip_filter') . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_mactrack_recent_ports.ip_address LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_mactrack_recent_ports.ip_address NOT LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_mactrack_recent_ports.ip_address NOT LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_mactrack_recent_ports.ip_address = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_mactrack_recent_ports.ip_address != ''";
         }
     }
 
 	if ((strlen(get_request_var('port_filter')) > 0)||(get_request_var('port_filter_type_id') > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
         switch (get_request_var('port_filter_type_id')) {
             case "1": /* matches */
                 $sql_where .= " imb_mactrack_recent_ports.port_number='" . get_request_var('port_filter') . "'";
                 break;
             case "2": /* contains */
                 $sql_where .= " imb_mactrack_recent_ports.port_number <>'" . get_request_var('port_filter') . "'";
         }
     }
 	
     if (!(get_request_var('device_id') == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_mactrack_recent_ports.device_id=" . get_request_var('device_id');
     }
 
 
 
 	
      if (!(get_request_var('date_id') == "1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 		$data_search="";
 		if (get_request_var('date_id') == 2) {
 			$sql_where .= " imb_mactrack_recent_ports.active_last = '1'";
 		}else{
 	        switch (get_request_var('r_date_id')) {
 	            case "2": /* Current only */
 	                $data_search = date('Y-m-d H:i:s' ,strtotime("now"));
 	                break;
 	            case "3": /* Last 10 minute */
 	                $data_search = date('Y-m-d H:i:s' ,strtotime("-10 minute"));
 	                break;
 	            case "4": /* Last 30 minute */
 	                $data_search = date('Y-m-d H:i:s' ,strtotime("-30 minute"));
 	                break;
 	            case "5": /* Last Hour */
 	                $data_search = date('Y-m-d H:i:s' , strtotime("-1 hours"));
 	                break;
 	            case "6": /* Last Day*/
 	                $data_search = date('Y-m-d H:i:s' , strtotime("-1 day"));
 	                break;
 	            case "7": /*Last Week */
 	                $data_search = date('Y-m-d H:i:s' , strtotime("-1 week"));
 	                break;
 	            case "8": /*Last Month */
 	                $data_search = date('Y-m-d H:i:s' , strtotime("-1 month"));
 	        }
 			
 			$sql_where .= " imb_mactrack_recent_ports.date_last >= '" . $data_search . "'";
 		}
     }	
	 
	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}
	
 	$sortby = get_request_var('sort_column');
 	if ($sortby=="hostname") {
 		$sortby = "INET_ATON(hostname)";
 	}elseif ($sortby=="ip_address") {
 		$sortby = "INET_ATON(ip_address)";
 	}	 
	 
	 
 //***************************************************************************
    $query_string = "SELECT
             row_id, site_name, description, hostname, mac_address, vendor_name, ip_address, dns_hostname, port_number,
             port_name, vlan_id, vlan_name, date_last as max_scan_date, count_rec, active_last
             FROM imb_mactrack_recent_ports
             LEFT JOIN mac_track_sites ON (imb_mactrack_recent_ports.site_id = mac_track_sites.site_id) 
 			$sql_where
 			ORDER BY " . $sortby . " " . get_request_var('sort_direction') .
			$sql_limit;
 //******************************************************************************
 
 
     if (strlen($sql_where) == 0) {
         return array();
     }else{
         return db_fetch_assoc($query_string);
     }
     
 }
 

function impb_recentmacs_request_validation() {
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
			'default' => 'description',
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
		'date_id' => array(
			'filter' => FILTER_VALIDATE_INT,
			'default' => '-1',
			'pageset' => true
			),
			
	);

	validate_store_request_vars($filters, 'sess_impb_recentmacs');
	/* ================= input validation ================= */
}


 function impb_view_recentmacs() {
     global $title, $report, $impb_search_types, $recentmacs_actions, $config;
 
   
	impb_recentmacs_request_validation();

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
	impb_recentmacs_filter();
	html_end_box(); 
 
    $sql_where = "";
    $port_results = impb_view_get_recentmacs_records($sql_where, true, $rows);
	$rows_query_string = "SELECT
		COUNT(DISTINCT device_id, mac_address, port_number, ip_address)
		FROM imb_mactrack_recent_ports
		$sql_where";

	if (strlen($sql_where) == 0) {
		$total_rows = 0;
	}else{
		$total_rows = db_fetch_cell($rows_query_string);
	}
	
	$nav = html_nav_bar('impb_view_recentmacs.php?report=recentmacs', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 14, __('Bindings'), 'page', 'main');

	form_start('impb_view_recentmacs.php', 'chk');
	
	//print $nav;
	
	html_start_box('', '100%', '', '3', 'center', '');

 
	$display_text = array(
		'description'      	=> array(__('Device'), 'ASC'),
		'hostname'        	=> array(__('IP(имя)'), 'ASC'),
		'ip_address'      	=> array(__('IP Address'), 'ASC'),
		'mac_address'      	=> array(__('MAC Address'), 'ASC'),
		"vendor_name" 		=> array(__('Vendor'),'DESC'),
		"port_number" 		=> array(__('Номер<br>Порта'),'DESC'),
		"port_name" 		=> array(__('Имя<br>Порта'),'DESC'),
		"vlan_id" 			=> array(__('VLAN<br>ID'),'DESC'),
		"vlan_name" 		=> array(__('VLAN<br>Name'),'DESC'),
		"date_last" 		=> array(__('Время<br>последнего<br>сканирования'),'DESC'),
		"count_rec" 		=> array(__('Количество<br>сканирований'), 'DESC'));		
 
	html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false);
 
    $i = 0;
    
	if (sizeof($port_results) > 0) {
         foreach ($port_results as $port_result) {
             $scan_date = $port_result["max_scan_date"];
 
 		  if ($port_result["active_last"] == 1)  {
 		  //$color_line_date="<span style='background-color: #F8D93D;'>";
 		  $color_line_date="<span style='font-weight: bold;'>";
 		  }else{
 		  $color_line_date="";
 		  }
   
             
            form_alternate_row('line' . $port_result["row_id"], true);

 			//form_selectable_cell("<a class='linkEditMain' href='impb_devices.php?action=edit&device_id=" . $port["device_id"] . "'>" . 
 			//	(strlen($_REQUEST["p_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["p_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port["description"]) : $port["description"]) . "</strong></a>", $port["port_id"]);			
 			form_selectable_cell($port_result["description"], $port_result["row_id"] );			
 			form_selectable_cell($port_result["hostname"], $port_result["row_id"] );	
 			form_selectable_cell((strlen(get_request_var('ip_filter')) ? preg_replace("/(" . preg_quote(get_request_var('ip_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["ip_address"]) : $port_result["ip_address"]),$port_result["row_id"]);			
 			if (strlen(read_config_option("mt_reverse_dns")) > 0) {
 				form_selectable_cell((strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["dns_hostname"]) : $port_result["dns_hostname"]),$port_result["row_id"]);			
 			}
 			form_selectable_cell(strtoupper(strlen(get_request_var('mac_filter')) ? preg_replace("/(" . preg_quote(get_request_var('mac_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["mac_address"]) : $port_result["mac_address"]),$port_result["row_id"]);			
 			form_selectable_cell($port_result["vendor_name"], $port_result["row_id"] );	
 			form_selectable_cell($port_result["port_number"], $port_result["row_id"] );		
 			form_selectable_cell((strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["port_name"]) : $port_result["port_name"]),$port_result["row_id"]);						
 			
 			form_selectable_cell($port_result["vlan_id"], $port_result["row_id"] );			
 			form_selectable_cell((strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["vlan_name"]) : $port_result["vlan_name"]),$port_result["row_id"]);						
 			
 			form_selectable_cell($color_line_date . " " . $scan_date, $port_result["row_id"] );			
 			form_selectable_cell($port_result["count_rec"], $port_result["row_id"] );	
 			form_checkbox_cell($port_result["description"], $port_result["row_id"]);
 			
         }
         /* put the nav bar on the bottom as well */

         print $nav;
     }else{
         print "<tr><td><em>No MackTrack Recent Results</em></td></tr>";
     }
	html_end_box(false);

    impb_draw_actions_dropdown($recentmacs_actions, "");
	form_end();

}
 

 


 function form_actions_recentmacs() {
     global $config, $recentmacs_actions, $impb_imp_mode;
 
     /* if we are to save this form, instead of display it */
     if (isset($_POST["selected_items"])) {
         $selected_items = unserialize(stripslashes($_POST["selected_items"]));
         if ($_POST["drp_action"] == "1") { /* удалить запись */
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
 				db_execute("DELETE FROM imb_mactrack_recent_ports WHERE row_id=" . $selected_items[$i]);
             }
 		}elseif ($_POST["drp_action"] == "2") { /* создать запись */
            for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
 				$cur_recent_row_id = $selected_items[$i];
 				$recents[$cur_recent_row_id]["recent_row_id"] = $cur_recent_row_id;
 				$recents[$cur_recent_row_id]["recent_device_id"] = $_POST["rm_" . $cur_recent_row_id . "_device_id"];
 				$recents[$cur_recent_row_id]["recent_mac_address"] = imp_translate_mac_address($_POST["rm_" . $cur_recent_row_id . "_mac_address"]);
 				$recents[$cur_recent_row_id]["recent_ip_address"] = impb_translate_ip_address($_POST["rm_" . $cur_recent_row_id . "_ip_address"]);
 				$recents[$cur_recent_row_id]["recent_port"] = $_POST["rm_" . $cur_recent_row_id . "_port"];
 				$recents[$cur_recent_row_id]["recent_acl_mode"] = $impb_imp_mode[$_REQUEST["rm_" . $cur_recent_row_id . "_use_acl"]];
            }
                 
 			if (sizeof($recents) > 0) {
 				foreach ($recents as $recent) {	
 					imb_create_imp_record($recent["recent_device_id"],$recent["recent_mac_address"], $recent["recent_ip_address"], $recent["recent_port"] , $recent["recent_acl_mode"], false);
 				//	imb_create_imp_record($device_id,                 $mac_adrress,                 $ip_adrress,                   $port,                 $acl_mode, $ban = false)
 				}
 			}	
 		} 
 		
 
 		
 	header("Location: impb_view.php");
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = "";
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     foreach ($_POST as $var => $val) {
         if (ereg("^chk_([0-9]+)$", $var, $matches)) {
             /* ================= input validation ================= */
             input_validate_input_number($matches[1]);
             /* ==================================================== */
             $row_info = db_fetch_row("SELECT description, ip_address, mac_address, date_last FROM imb_mactrack_recent_ports WHERE row_id=" . $matches[1]);
             $row_list .= "<li>" . $row_info["description"] . " (IP:" . $row_info["ip_address"] . "   Mac:" . $row_info["mac_address"] . "   date:" . $row_info["date_last"] . ")<br>";
             $row_array[$i] = $matches[1];
 			$row_ids = $row_ids . "'" . $matches[1] . "', ";
         $i++;
		 }                                  
         
     }
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 	
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impb_header.php");
 
     html_start_box("<strong>" . $recentmacs_actions{$_POST["drp_action"]} . "</strong>", "60%", '', "3", "center", "");
 
     print "<form action='impb_view.php' method='post'>\n";
 
     if ($_POST["drp_action"] == "1") {  /*удалить запись */
         print "    <tr>
                 <td class='textArea'>
                     <p>Подтверждаете удаление следующих строк ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
 	} else if ($_POST["drp_action"] == "2") { /*Прописать запись*/
 
 
 
 
 	
 	
 		$recent_macips_rows=db_fetch_assoc("SELECT imb_mactrack_recent_ports.*, imb_devices.device_id, imb_devices.hostname, imb_devices.description, imb_devices.device_id as imb_device_id, mac_track_devices.hostname, mac_track_devices.device_id, imb_ports.port_name  " .
 			" FROM imb_mactrack_recent_ports" .
 			" join mac_track_devices on (imb_mactrack_recent_ports.device_id = mac_track_devices.device_id) " .
 			" join imb_devices on (mac_track_devices.hostname = imb_devices.hostname) " .
 			" join imb_ports on (imb_mactrack_recent_ports.port_number = imb_ports.port_number and imb_devices.device_id = imb_ports.device_id) " .
 			" WHERE imb_mactrack_recent_ports.row_id in (" . $row_ids . ") and imb_mactrack_recent_ports.ip_address is not null and imb_mactrack_recent_ports.ip_address <> '';");
 
 		html_start_box("Для создания записи IP-MAC-PORT проверьте/измените следующие поля.", "98%", '', "3", "center", "");
 
 
 	    html_header(array("","","","Host Description","Hostname<br>", "Номер порта","Описание порта","IP-адресс", "MAC-адресс",  "Порт",  "Режим"));
 
 	    $i = 0;
 	    if (sizeof($recent_macips_rows) > 0) {
 	        foreach ($recent_macips_rows as $recent_macips_row) {
 				$recent_macips_id = $recent_macips_row["row_id"];
 	            form_alternate_row();
 	                ?>
 					<td><?php form_hidden_box("rm_" . $recent_macips_id . "_macip_id", $recent_macips_id, "form_default_value");?></td>
 					<td><?php form_hidden_box("rm_" . $recent_macips_id . "_device_id", $recent_macips_row["imb_device_id"], "form_default_value");?></td>
 					<td><?php form_hidden_box("rm_" . $recent_macips_id . "_ip_address", $recent_macips_row["ip_address"], "form_default_value");?></td>
 					<td><?php print $recent_macips_row["description"];?></td>
 					<td><?php print $recent_macips_row["hostname"];?></td>
 					<td><?php print $recent_macips_row["port_number"];?></td>
 					<td><?php print $recent_macips_row["port_name"];?></td>
 					<td><?php print $recent_macips_row["ip_address"];?></td>
 					<td><?php form_text_box("rm_" . $recent_macips_id . "_mac_address", $recent_macips_row["mac_address"], "", 17, 15, "text", 1) ;?></td>
 					<td><?php form_text_box("rm_" . $recent_macips_id . "_port", $recent_macips_row["port_number"], "", 250, 10, "text", 1) ;?></td>
 					<td><?php form_dropdown("rm_" . $recent_macips_id . "_use_acl",  $impb_imp_mode, "", "", "ARP", "", "") ;?></td>				
 	            </tr>
 	            <?php
 	        }
 	    }
 		html_end_box(false);
 	
 	
 	
 	
 
 	
 	}
 
 	
     if (!isset($row_array)) {
         print "<tr><td ><span class='textError'>Вы должны выбрать хотябы одну строку</span></td></tr>\n";
         $save_html = "";
     }else{
		$save_html = "<input type='submit' name='Save' value='Save (сохранить)'>";		
     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_recentmacs'>
                 <input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
                 <input type='hidden' name='drp_action' value='" . $_POST["drp_action"] . "'>
                 <input type='submit' name='Cancel' value='Cancel (отмена)'>
                 $save_html
             </td>
         </tr>
         ";
 
     html_end_box();
 
 }
 
 
function impb_recentmacs_filter() {
	global $item_rows, $impb_search_types, $impb_search_recent_date;

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
				<tr>
 					<td width="80">
 						&nbsp;Data:
 					</td>
 					<td width="1">
 						<select id="date_id">
 						<?php
 						for($i=1;$i<=sizeof($impb_search_recent_date);$i++) {
 							print "<option value='" . $i . "'"; if (get_request_var('date_id') == $i) { print " selected"; } print ">" . $impb_search_recent_date[$i] . "</option>\n";
 						}
 						?>
 						</select>
 					</td>
				</tr>					
			</table>				
		</form>
		<script type='text/javascript'>
		function applyFilter() {
			strURL  = urlPath+'plugins/impb/impb_view_recentmacs.php?header=false';
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
			strURL  = urlPath+'plugins/impb/impb_view_recentmacs.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}

		function clearPort() {
			strURL  = urlPath+'plugins/impb/impb_view_recentmacs.php?header=false&port_filter_type_id=1&port_filter=';
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
			strURL  = urlPath+'plugins/impb/impb_view_recentmacs.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_view_recentmacs.php?import=true';
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



?>
