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
 define("MAX_DISPLAY_PAGES", 21);
 
 //***********************************************************
 $device_actions = array(
     5 => "Обновить информацию!",
 	7 => "Сохранить конфигурацию"
 	);
 $banip_actions = array(
     1 => "Создать",
 	2 => "Удалить",
 	3 => "Разрешить к преминению",
 	4 => "Снять разрешение",
 	5 => "Применить сейчас",
 	6 => "Снять применение",
	7 => "Удалить связанные привязки"
 	);	
 $logs_actions = array(
     1 => "Удалить запись"
 	);
 	
 $port_actions = array(
 	1 => "Enable Ip-Mac",
 	2 => "Disable Ip-Mac",
 	3 => "Изменить описание порта",
 	4 => "Включить порт",
 	5 => "Отключить порт",
 	);
 
 $blmacs_actions = array(
 	1 => "Прописать блок",
 	2 => "Удалить блок"
 	);
 
 $recentmacs_actions = array(
 	1 => "Удалить запись",
 	2 => "Прописать запись"
 	);
 
 $macips_actions = array(
 	1 => "Удалить запись",
 	2 => "Изменить запись",
 	3 => "Групповое изменение",
 	4 => "Создать запись",
	5 => "Изменить свободу",
	6 => "Отправить СМС"
 	);

 $net_del_actions = array(
 	1 => "Удалить запись",
 	2 => "Изменить запись",
 	3 => "Создать запись"
 	);

 $net_add_actions = array(
 	1 => "Удалить запись",
 	2 => "Изменить запись",
 	3 => "Создать запись"
 	);
 	
 //convert_Xport_to_view_string("03EA1AF5","");    
 if (!isset($_REQUEST["action"])) { $_REQUEST["action"] = ""; }
 //db_store_imp_log2(array(log => "log1",count => 'count2'));     
 //imp_raise_message3(array("device_descr" => "Свича", "type" => "title","leftmargin" => '0', "message" => "Старт Старт Старт Старт СтартСтартСтартСтартСтартСтартСтартСтартСтартСтартСтартСтартСтарт "));     
 //imp_raise_message3(array("device_descr" => "Свича", "type" => "action_check","leftmargin" => "10", "message" => "шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1шаг 1", "step_data" => "10", "step_rezult" => "OK", "check_data" => "20", "check_rezult" => "Error"));     
 //$ttt = db_fetch_assoc("SELECT imb_macip.* FROM imb_macip  left join imb_devices  on (imb_macip.device_id = imb_devices.device_id) WHERE macip_id in ('4907','17044');");
 
 if (isset($_REQUEST["cancel"]) || isset($_REQUEST["Cancel"])) { $_REQUEST["action"] = ""; }
 
 switch ($_REQUEST["action"]) {
     case 'actions_ports':
         form_actions_ports();
         exit;
     case 'actions_blmacs':
         form_actions_blmacs();
         exit;
     case 'actions_devices':
         form_actions_devices();
         exit;
     case 'actions_recentmacs':
         form_actions_recentmacs();
         exit;		
 	case 'actions_macips':
         form_actions_macips();
         exit;	
     case 'actions_logs':
         form_actions_logs();
         exit;        
     case 'device_query':
         host_device_query();
         header("Location: impblinding_view.php");
 		exit; 
     case 'actions_banips':
         form_actions_banips();
         exit; 
     case 'actions_net_del':
         form_actions_net_del();
         exit; 	
     case 'actions_net_add':
         form_actions_net_add();
         exit; 		 
 }
 
 //***********************************************************
 load_current_session_value("report", "sess_impb_view_report", "macs");
 
 if (isset($_REQUEST["export_macs_x"])) {
     impblinding_view_export_macs();
 }elseif (isset($_REQUEST["export_devices_x"])) {
     impblinding_view_export_devices();
 }elseif (isset($_REQUEST["export_sites_x"])) {
     impblinding_view_export_sites();
 }elseif (isset($_REQUEST["export_ips_x"])) {
     impblinding_view_export_ip_ranges();
 }else{
     switch ($_REQUEST["report"]) {
         case "blmacs":
             $title = "D-Link IP-Mac-Port Binding - Blocked MAC Report View";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include($config['base_path'] . "/include/bottom_footer.php");
             impblinding_view_blmacs();
             break;
         case "ports":
             $title = "D-Link IP-Mac-Port Binding  -  Ports Report View";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include_once($config['base_path'] . "/include/bottom_footer.php");
             impblinding_view_ports();
             break;
         case "banips":
             $title = "D-Link IP-Mac-Port Binding  -  BAN IP`s View";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include_once($config['base_path'] . "/include/bottom_footer.php");
             impblinding_view_banips();
             break;
 		case "devices":
             $title = "D-Link IP-Mac-Port Binding  -  Device Report View";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include($config['base_path'] . "/include/bottom_footer.php");
			 impblinding_view_devices();
             
             break;
         case "recent_macips":
             $title = "D-Link IP-Mac-Port Binding  -  Просмотр группировки сканированных результатов MackTrack";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include($config['base_path'] . "/include/bottom_footer.php");
             impblinding_view_recent_macips();
             break;
         case "info":
             $title = "D-Link IP-Mac-Port Binding  -  Просмотр результатов быстрого поиска.";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include($config['base_path'] . "/include/bottom_footer.php");
             impblinding_view_info();
             break;
 		case "net_del":
             $title = "D-Link IP-Mac-Port Binding  -  авто удаляемые блоки";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include($config['base_path'] . "/include/bottom_footer.php");
             impblinding_view_net_del();
             break;		
 		case "net_add":
             $title = "D-Link IP-Mac-Port Binding  -  авто прописываемые ИП (на подключение)";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include($config['base_path'] . "/include/bottom_footer.php");
             impblinding_view_net_add();
             break;	
 		default:
             $title = "D-Link IP-Mac-Port Binding  -  MAC to IP Report View";
             include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
             include($config['base_path'] . "/include/bottom_footer.php");
			 impblinding_view_macips();
             
     }
 }
 
 /*impblinding_view_get_ip_range_records
 Делает выборку данных 
 */
 function host_device_query() {
 	/* ================= input validation ================= */
 	input_validate_input_number(get_request_var("id"));
 	input_validate_input_number(get_request_var("host_id"));
 	/* ==================================================== */
 
 	run_poller_impblinding($_GET["host_id"]);
 }
 
 function impblinding_view_get_ip_range_records(&$sql_where, $apply_limits = TRUE) {
     if ($_REQUEST["i_site_id"] != "-1") {
         $sql_where = "WHERE mac_track_ip_ranges.site_id='" . $_REQUEST["i_site_id"] . "'";
     }else{
         $sql_where = "";
     }
 
     $ip_ranges = "SELECT
         mac_track_sites.site_id,
         mac_track_sites.site_name,
         mac_track_ip_ranges.ip_range,
         mac_track_ip_ranges.ips_max,
         mac_track_ip_ranges.ips_current,
         mac_track_ip_ranges.ips_max_date,
         mac_track_ip_ranges.ips_current_date
         FROM mac_track_ip_ranges
         INNER JOIN mac_track_sites ON (mac_track_ip_ranges.site_id=mac_track_sites.site_id)
         $sql_where";
 
     if ($apply_limits) {
         $ip_ranges .= " LIMIT " . (read_config_option("dimpb_num_rows")*($_REQUEST["i_page"]-1)) . "," . read_config_option("dimpb_num_rows");
     }
 
     return db_fetch_assoc($ip_ranges);
 }
/*impblinding_view_get_ports_records
 Делает выборку данных для показа страницы информации по портам.
 */
function impblinding_view_get_ports_records(&$sql_where, $apply_limits = TRUE) {
     /* create SQL where clause */
    // $device_type_info = db_fetch_row("SELECT * FROM mac_track_device_types WHERE device_type_id = '" . $_REQUEST["p_device_type_id"] . "'");
 
     $sql_where = "";
 
     if ($_REQUEST["p_device_id"] != "-1") {
         if (!strlen($sql_where)) {
             $sql_where = "WHERE (imb_ports.device_id=" . $_REQUEST["p_device_id"] . ")";
         }else{
             $sql_where .= " AND (imb_ports.device_id=" . $_REQUEST["p_device_id"] . ")";
         }
     }
 
     if ($_REQUEST["p_status"] != "-1") {
         if (!strlen($sql_where)) {
             $sql_where = "WHERE (imb_ports.port_imb_state=" . $_REQUEST["p_status"] . ")";
         }else{
             $sql_where .= " AND (imb_ports.port_imb_state=" . $_REQUEST["p_status"] . ")";
         }
     }

     if (strlen($_REQUEST["p_port_number"]) > 0) {
		 if ($_REQUEST["p_port_number"] != "-1") {
			 if (!strlen($sql_where)) {
				 $sql_where = "WHERE (imb_ports.port_number=" . $_REQUEST["p_port_number"] . ")";
			 }else{
				 $sql_where .= " AND (imb_ports.port_number=" . $_REQUEST["p_port_number"] . ")";
			 }
		 }
		}
 
 $query_string = "SELECT
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
             ORDER BY imb_ports.device_id, imb_ports.port_number";
 
         if ($apply_limits) {
             $query_string .= " LIMIT " . (read_config_option("dimpb_num_rows")*($_REQUEST["p_page"]-1)) . "," . read_config_option("dimpb_num_rows");
         }
         
         
     return db_fetch_assoc($query_string);
 }
 
 /*impblinding_view_get_macips_records
 Делает выборку данных для показа страницы информации по записям МАК-ИП-ПОРТ.
 */
function impblinding_view_get_macips_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
     /* form the 'where' clause for our main sql query */
     if (strlen($_REQUEST["m_mac_filter"]) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
 	switch ($_REQUEST["m_mac_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_macip.macip_macaddr='" . $_REQUEST["m_mac_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_macip.macip_macaddr LIKE '%%" . $_REQUEST["m_mac_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_macip.macip_macaddr LIKE '" . $_REQUEST["m_mac_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_macip.macip_macaddr NOT LIKE '" . $_REQUEST["m_mac_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_macip.macip_macaddr NOT LIKE '" . $_REQUEST["m_mac_filter"] . "%%'";
         }
     }
 
     if ((strlen($_REQUEST["m_ip_filter"]) > 0)||($_REQUEST["m_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["m_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_macip.macip_ipaddr='" . $_REQUEST["m_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_macip.macip_ipaddr LIKE '%%" . $_REQUEST["m_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_macip.macip_ipaddr LIKE '" . $_REQUEST["m_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_macip.macip_ipaddr NOT LIKE '" . $_REQUEST["m_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_macip.macip_ipaddr NOT LIKE '" . $_REQUEST["m_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_macip.macip_ipaddr = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_macip.macip_ipaddr != ''";
         }
     }
 
     if ((strlen($_REQUEST["m_port_filter"]) > 0)||($_REQUEST["m_port_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["m_port_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* состоит */
 				$sql_where .= " FIND_IN_SET('" . $_REQUEST["m_port_filter"] . "',`macip_port_list`)";
                 break;
             case "3": /* не состоит */
 				$sql_where .= " NOT FIND_IN_SET('" . $_REQUEST["m_port_filter"] . "',`macip_port_list`)";
 
         }
     }	
 	
     if (strlen($_REQUEST["m_filter"])) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
             $sql_where .= " (imb_macip.macip_port_hex LIKE '%" . $_REQUEST["m_filter"] . "%' OR " .
                 "imb_macip.macip_port_list LIKE '%" . $_REQUEST["m_filter"] . "%' OR " .
 				"lbs.login LIKE '%" . $_REQUEST["m_filter"] . "%' OR " .
				"imb_macip.macip_lastchange_date LIKE '%" . $_REQUEST["m_filter"] . "%' OR " .
 				"f_flat LIKE '%" . $_REQUEST["m_filter"] . "%' OR " .
				"imb_macip.macip_scan_date LIKE '%" . $_REQUEST["m_filter"] . "%')";
    }
 
     if ((strlen($_REQUEST["m_sost"]) > 0) and ($_REQUEST["m_sost"] > -1)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["m_sost"]) {
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
 				$sql_where .= " lbs.equipm is not null ";
				break;				
         }
    }
	
     if (!($_REQUEST["m_device_id"] == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_macip.device_id=" . $_REQUEST["m_device_id"];
     }
 
 		$sortby = $_REQUEST["sort_column"];
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
			 lbs.f_flat, lbs.equipm,  if(gl_ip.id is null,'0',gl_ip.id) as ip_local_graph_id,
             lbs.login
			 FROM  imb_macip
             left JOIN imb_devices
             ON imb_macip.device_id = imb_devices.device_id
             JOIN imb_device_types ON imb_devices.device_type_id = imb_device_types.device_type_id 
			 LEFT JOIN (SELECT l.segment,  v.*  FROM lb_staff l left JOIN lb_vgroups_s v ON l.vg_id = v.vg_id WHERE v.`archive`=0) lbs ON INET_ATON(imb_macip.macip_ipaddr) = lbs.segment
			left JOIN host             ON imb_macip.macip_ipaddr = host.hostname		
			LEFT JOIN graph_local gl_ip ON gl_ip.snmp_index=inet_aton(imb_macip.macip_ipaddr) and gl_ip.graph_template_id=43
			left JOIN host   h          ON imb_devices.hostname = h.hostname
 			$sql_where
 			ORDER BY " . $sortby . " " . $_REQUEST["sort_direction"];;
 			
                                                                                  
         if (($apply_limits) && ($row_limit != 999999)) {
             $query_string .= " LIMIT " . ($row_limit*($_REQUEST["m_page"]-1)) . "," . $row_limit;
         }
 //db_execute('SET NAMES utf8;');
         return db_fetch_assoc($query_string);
     
 }
 
 
function impblinding_view_get_bmacips_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
     /* form the 'where' clause for our main sql query */
     if (strlen($_REQUEST["b_mac_filter"]) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
  
     switch ($_REQUEST["b_mac_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_blmacs.blmac_macaddr='" . $_REQUEST["b_mac_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_blmacs.blmac_macaddr LIKE '%%" . $_REQUEST["b_mac_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_blmacs.blmac_macaddr LIKE '" . $_REQUEST["b_mac_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_blmacs.blmac_macaddr NOT LIKE '" . $_REQUEST["b_mac_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_blmacs.blmac_macaddr NOT LIKE '" . $_REQUEST["b_mac_filter"] . "%%'";
         }
     }
 
     if ((strlen($_REQUEST["b_ip_filter"]) > 0)||($_REQUEST["b_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["b_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip='" . $_REQUEST["b_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip LIKE '%%" . $_REQUEST["b_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip LIKE '" . $_REQUEST["m_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip NOT LIKE '" . $_REQUEST["b_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip NOT LIKE '" . $_REQUEST["b_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip != ''";
         }
         $sql_where .= " OR NULL ";
     }
 
     if (strlen($_REQUEST["b_port_filter"]) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_blmacs.blmac_port=" . $_REQUEST["b_port_filter"];
     }
     
     
     if (!($_REQUEST["b_device_id"] == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_blmacs.device_id=" . $_REQUEST["b_device_id"];
     }
 
 	
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
               ORDER BY " . $_REQUEST["sort_column"] . " " . $_REQUEST["sort_direction"];
               
                                                                        
         if (($apply_limits) && ($row_limit != 999999)) {
             $query_string .= " LIMIT " . ($row_limit*($_REQUEST["b_page"]-1)) . "," . $row_limit;
         }
 
         return db_fetch_assoc($query_string);
     
 }
 
 
function impblinding_view_get_device_records(&$sql_where, $apply_limits = TRUE) {
     $device_type_info = db_fetch_row("SELECT * FROM imb_devices WHERE device_type_id = '" . $_REQUEST["d_device_type_id"] . "'");
 
         if ($_REQUEST["d_device_type_id"] == 0) {
             $device_type_info = array("device_type_id" => 0, "description" => "Unknown Device Type");
         }
 
     /* form the 'where' clause for our main sql query */
     $sql_where = "WHERE (imb_devices.hostname LIKE '%" . $_REQUEST["d_filter"] . "%' OR " .
                     "imb_devices.description LIKE '%" . $_REQUEST["d_filter"] . "%')";
 
     if (sizeof($device_type_info)) {
         $sql_where .= " AND (imb_devices.device_type_id=" . $device_type_info["device_type_id"] . ")";
     }
 
     if ($_REQUEST["d_status"] == "-1") {
         /* Show all items */
     }elseif ($_REQUEST["d_status"] == "-2") {
         $sql_where .= " AND (imb_devices.disabled='on')";
     }else {
         $sql_where .= " AND (imb_devices.snmp_status=" . $_REQUEST["d_status"] . ") AND (imb_devices.disabled = '')";
     }
 
  		$sortby = $_REQUEST["sort_column"];
 	if ($sortby=="hostname") {
 		$sortby = "INET_ATON(hostname)";
 	}
	//tabbed interface
	if (strlen($_REQUEST["dtab"])) {
        $int_tab = intval($_REQUEST["dtab"]);

		if ($int_tab == 1) {
			$sql_where .= " ";
		}elseif ($int_tab == 2) {
			$tabs_dimpb = db_fetch_cell("SELECT GROUP_CONCAT(`device_id` SEPARATOR ', ') FROM `imb_blmacs`;");
			$sql_where .= " AND imb_devices.device_id IN (" . $tabs_dimpb . ") ";
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
        ORDER BY " . $sortby . " " . $_REQUEST["sort_direction"];
 
     if ($apply_limits) {
         $sql_query .= " LIMIT " . (read_config_option("dimpb_num_rows")*($_REQUEST["d_page"]-1)) . "," . read_config_option("dimpb_num_rows");
     }
 
     return db_fetch_assoc($sql_query);
 }
 
 
function impblinding_view_get_banip_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
 
     if ((strlen($_REQUEST["bn_ip_filter"]) > 0)||($_REQUEST["bn_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["bn_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_banip.`banip_ipaddr`='" . $_REQUEST["bn_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_banip.`banip_ipaddr` LIKE '%%" . $_REQUEST["bn_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_banip.`banip_ipaddr` LIKE '" . $_REQUEST["bn_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_banip.`banip_ipaddr` NOT LIKE '" . $_REQUEST["bn_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_banip.`banip_ipaddr` NOT LIKE '" . $_REQUEST["bn_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_banip.`banip_ipaddr` = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_banip.`banip_ipaddr` != ''";
         }
     }
 
 	if ($_REQUEST["bn_banip_aproved"] == "-1") {
         /* Show all items */
     }else {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }	
         $sql_where .= " (imb_banip.banip_aproved=" . $_REQUEST["bn_banip_aproved"] . ") ";
     }
 	
 	if ($_REQUEST["bn_banip_aplled"] == "-1") {
         /* Show all items */
     }else {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }	
         $sql_where .= " (imb_banip.banip_aplled=" . $_REQUEST["bn_banip_aplled"] . ") ";
     }
 	if ($_REQUEST["bn_banip_manual"] == "-1") {
         /* Show all items */
     }else {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }	
         $sql_where .= " (imb_banip.banip_manual=" . $_REQUEST["bn_banip_manual"] . ") ";
     }
 	if ($_REQUEST["bn_banip_type"] == "0") {
         /* Show all items */
     }else {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }	
         $sql_where .= " (imb_banip.banip_type=" . $_REQUEST["bn_banip_type"] . ") ";
     }
 	
     if (strlen($_REQUEST["bn_filter"])) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
             $sql_where .= " (imb_banip.banip_message LIKE '%" . $_REQUEST["bn_filter"] . "%')";
    }	
 
 
 
 
 
     $sql_query = "SELECT
         imb_banip.banip_id,
         imb_banip.banip_ipaddr,
         imb_banip.banip_aplled,
         imb_banip.banip_aproved,
         imb_banip.banip_type,
 		imb_banip.banip_manual,
 		imb_banip.banip_balance,
 		imb_banip.banip_author_id,
         imb_banip.banip_install_date,
         imb_banip.banip_expiration_date,
 		imb_banip.banip_counts,
 		imb_banip.banip_delete,
 		imb_banip.banip_mac_active_last_poll,
         imb_banip.banip_message
         FROM imb_banip
         $sql_where
         ORDER BY " . $_REQUEST["sort_column"] . " " . $_REQUEST["sort_direction"];
 
         if (($apply_limits) && ($row_limit != 999999)) {
             $sql_query .= " LIMIT " . ($row_limit*($_REQUEST["bn_page"]-1)) . "," . $row_limit;
         }
 
     return db_fetch_assoc($sql_query);
 }
 
 
function impblinding_view_get_recent_macips_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
     /* form the 'where' clause for our main sql query */
     if (strlen($_REQUEST["r_mac_filter"]) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
    
     switch ($_REQUEST["r_mac_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " mac_track_ports.mac_address='" . $_REQUEST["r_mac_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " mac_track_ports.mac_address LIKE '%%" . $_REQUEST["r_mac_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " mac_track_ports.mac_address LIKE '" . $_REQUEST["r_mac_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " mac_track_ports.mac_address NOT LIKE '" . $_REQUEST["r_mac_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " mac_track_ports.mac_address NOT LIKE '" . $_REQUEST["r_mac_filter"] . "%%'";
         }
     }
 
     if ((strlen($_REQUEST["r_ip_filter"]) > 0)||($_REQUEST["r_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["r_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " mac_track_ports.ip_address='" . $_REQUEST["r_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " mac_track_ports.ip_address LIKE '%%" . $_REQUEST["r_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " mac_track_ports.ip_address LIKE '" . $_REQUEST["r_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " mac_track_ports.ip_address NOT LIKE '" . $_REQUEST["r_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " mac_track_ports.ip_address NOT LIKE '" . $_REQUEST["r_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " mac_track_ports.ip_address = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " mac_track_ports.ip_address != ''";
         }
     }
 
 	if (strlen($_REQUEST["r_port_filter"]) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
         switch ($_REQUEST["r_port_filter_type_id"]) {
             case "1": /* matches */
                 $sql_where .= " mac_track_ports.port_number='" . $_REQUEST["r_port_filter"] . "'";
                 break;
             case "2": /* contains */
                 $sql_where .= " mac_track_ports.port_number <>'" . $_REQUEST["r_port_filter"] . "'";
         }
     }
 	
     if (!($_REQUEST["r_device_id"] == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " mac_track_ports.device_id=" . $_REQUEST["r_device_id"];
     }
 
 
 
 	
      if (!($_REQUEST["r_date_id"] == "1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 		$data_search="";
 		if ($_REQUEST["r_date_id"] == 2) {
 			$sql_where .= " mac_track_ports.active_last = '1'";
 		}else{
 	        switch ($_REQUEST["r_date_id"]) {
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
 			
 			$sql_where .= " mac_track_ports.date_last >= '" . $data_search . "'";
 		}
     }	
 	$sortby = $_REQUEST["sort_column"];
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
             LEFT JOIN mac_track_sites ON (imb_mactrack_recent_ports.site_id = mac_track_sites.site_id) " .
             str_replace("mac_track_ports", "imb_mactrack_recent_ports", $sql_where) .
             //" ORDER BY imb_mactrack_recent_ports.hostname,  imb_mactrack_recent_ports.ip_address, imb_mactrack_recent_ports.count_rec desc, imb_mactrack_recent_ports.port_number";
 			" ORDER BY " . $sortby . " " . $_REQUEST["sort_direction"];
 //******************************************************************************
         if (($apply_limits) && ($row_limit != 999999)) {
             $query_string .= " LIMIT " . ($row_limit*($_REQUEST["r_page"]-1)) . "," . $row_limit;
         }
 
     if (strlen($sql_where) == 0) {
         return array();
     }else{
         return db_fetch_assoc($query_string);
     }
     
 }
 
function impblinding_view_get_info_macips_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
     /* form the 'where' clause for our main sql query */
     if (strlen($_REQUEST["i_mac_filter"]) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
    
     switch ($_REQUEST["i_mac_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_macip.macip_macaddr='" . $_REQUEST["i_mac_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_macip.macip_macaddr LIKE '%%" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_macip.macip_macaddr LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_macip.macip_macaddr NOT LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_macip.macip_macaddr NOT LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
         }
     }
 
     if ((strlen($_REQUEST["i_ip_filter"]) > 0)||($_REQUEST["i_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["i_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_macip.macip_ipaddr='" . $_REQUEST["i_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_macip.macip_ipaddr LIKE '%%" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_macip.macip_ipaddr LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_macip.macip_ipaddr NOT LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_macip.macip_ipaddr NOT LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_macip.macip_ipaddr = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_macip.macip_ipaddr != ''";
         }
     }
 
     if ((strlen($_REQUEST["i_port_filter"]) > 0)||($_REQUEST["i_port_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["i_port_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* состоит */
 				$sql_where .= " imb_macip.macip_port_list='" . $_REQUEST["i_port_filter"] . "'";
                 break;
             case "3": /* не состоит */
 				$sql_where .= " imb_macip.macip_port_list NOT LIKE '" . $_REQUEST["i_port_filter"] . "'";
 
         }
     }	
 	
     if (strlen($_REQUEST["i_filter"])) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
             $sql_where .= " (imb_macip.macip_port_hex LIKE '%" . $_REQUEST["i_filter"] . "%' OR " .
                 "imb_macip.macip_port_list LIKE '%" . $_REQUEST["i_filter"] . "%')";
    }
 
     if (!($_REQUEST["i_device_id"] == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_macip.device_id=" . $_REQUEST["i_device_id"];
     }
         $query_string = "SELECT  imb_devices.description, imb_devices.hostname, imb_devices.device_id, imb_devices.last_rundate,
             imb_macip.macip_id, imb_macip.macip_macaddr, imb_macip.macip_ipaddr, imb_macip.macip_banned, imb_macip.macip_port_list, imb_macip.macip_port_view, imb_macip.macip_imb_status, imb_macip.macip_mode, macip_online, macip_first_scan_date, macip_lastchange_date, imb_macip.macip_scan_date, macip_count_scan
             FROM  imb_macip
             LEFT JOIN imb_devices
             ON imb_macip.device_id = imb_devices.device_id
             $sql_where
             ORDER BY " . $_REQUEST["sort_column"] . " " . $_REQUEST["sort_direction"];
             
                                                                                  
         if (($apply_limits) && ($row_limit != 999999)) {
             $query_string .= " LIMIT " . ($row_limit*($_REQUEST["i_page"]-1)) . "," . $row_limit;
         }
         return db_fetch_assoc($query_string);
     
 }
 
function impblinding_view_get_info_bmacips_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
     /* form the 'where' clause for our main sql query */
     if (strlen($_REQUEST["i_mac_filter"]) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
   
     switch ($_REQUEST["i_mac_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_blmacs.blmac_macaddr='" . $_REQUEST["i_mac_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_blmacs.blmac_macaddr LIKE '%%" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_blmacs.blmac_macaddr LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_blmacs.blmac_macaddr NOT LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_blmacs.blmac_macaddr NOT LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
         }
     }
 
     if ((strlen($_REQUEST["i_ip_filter"]) > 0)||($_REQUEST["i_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["i_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip='" . $_REQUEST["i_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip LIKE '%%" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip NOT LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip NOT LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_temp_blmacinfo.blmacinfo_cor_ip != ''";
         }
         $sql_where .= " OR NULL ";
     }
 
     if ((strlen($_REQUEST["i_port_filter"]) > 0) ||($_REQUEST["i_port_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
         switch ($_REQUEST["i_port_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* состоит */
 				$sql_where .= " imb_blmacs.blmac_port='" . $_REQUEST["i_port_filter"] . "'";
                 break;
             case "3": /* не состоит */
 				$sql_where .= " imb_blmacs.blmac_port NOT LIKE '" . $_REQUEST["i_port_filter"] . "'";
 
         }
         //$sql_where .= " imb_blmacs.blmac_port=" . $_REQUEST["i_port_filter"];
     }
     
     
     if (!($_REQUEST["i_device_id"] == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_blmacs.device_id=" . $_REQUEST["i_device_id"];
     }
         $query_string = "SELECT imb_devices.description, 
 				imb_devices.hostname, 
 				imb_blmacs.*, imb_temp_blmacinfo.* 
               FROM imb_blmacs left join imb_devices on imb_blmacs.device_id=imb_devices.device_id left join
               imb_temp_blmacinfo on imb_blmacs.blmac_id=imb_temp_blmacinfo.blmacinfo_info_id
               $sql_where
               ORDER BY " . $_REQUEST["sort_column"] . " " . $_REQUEST["sort_direction"];
               
                                                                        
         if (($apply_limits) && ($row_limit != 999999)) {
             $query_string .= " LIMIT " . ($row_limit*($_REQUEST["i_page"]-1)) . "," . $row_limit;
         }
 
         return db_fetch_assoc($query_string);
     
 }

 
function impblinding_view_get_info_recent_macips_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
     /* form the 'where' clause for our main sql query */
     if (strlen($_REQUEST["i_mac_filter"]) > 0) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
     switch ($_REQUEST["i_mac_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " mac_track_ports.mac_address='" . $_REQUEST["i_mac_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " mac_track_ports.mac_address LIKE '%%" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " mac_track_ports.mac_address LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " mac_track_ports.mac_address NOT LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " mac_track_ports.mac_address NOT LIKE '" . $_REQUEST["i_mac_filter"] . "%%'";
         }
     }
 
     if ((strlen($_REQUEST["i_ip_filter"]) > 0)||($_REQUEST["i_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["i_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " mac_track_ports.ip_address='" . $_REQUEST["i_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " mac_track_ports.ip_address LIKE '%%" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " mac_track_ports.ip_address LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " mac_track_ports.ip_address NOT LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " mac_track_ports.ip_address NOT LIKE '" . $_REQUEST["i_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " mac_track_ports.ip_address = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " mac_track_ports.ip_address != ''";
         }
     }
 
     if (!($_REQUEST["i_device_id"] == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 		$imb_mac_track_ids = db_fetch_cell("SELECT mac_track_devices.device_id FROM mac_track_devices " .
 											" LEFT JOIN imb_devices " .
 											" ON (mac_track_devices.hostname = imb_devices.hostname) " .
 											" where imb_devices.device_id = '" . $_REQUEST["i_device_id"] . "';");
         $sql_where .= " mac_track_ports.device_id='" . $imb_mac_track_ids . "'";
     }
 
 
     //***************************************************************************
         $query_string = "SELECT
             row_id, site_name, description, hostname, mac_address, vendor_name, ip_address, dns_hostname, port_number,
             port_name, vlan_id, vlan_name, date_last as max_scan_date, count_rec, active_last
             FROM imb_mactrack_recent_ports
             LEFT JOIN mac_track_sites ON (imb_mactrack_recent_ports.site_id = mac_track_sites.site_id) " .
             str_replace("mac_track_ports", "imb_mactrack_recent_ports", $sql_where) .
             //" ORDER BY imb_mactrack_recent_ports.hostname,  imb_mactrack_recent_ports.ip_address, imb_mactrack_recent_ports.count_rec desc, imb_mactrack_recent_ports.port_number";
 			" ORDER BY " . $_REQUEST["sort_column"] . " " . $_REQUEST["sort_direction"];
 //******************************************************************************
         if (($apply_limits) && ($row_limit != 999999)) {
             $query_string .= " LIMIT " . ($row_limit*($_REQUEST["i_page"]-1)) . "," . $row_limit;
         }
 
     if (strlen($sql_where) == 0) {
         return array();
     }else{
         return db_fetch_assoc($query_string);
     }
     
 }
 


 function impblinding_view_get_net_del_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
     /* form the 'where' clause for our main sql query */

     if ((strlen($_REQUEST["nt_ip_filter"]) > 0)||($_REQUEST["nt_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["nt_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_macip.macip_ipaddr='" . $_REQUEST["nt_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_macip.macip_ipaddr LIKE '%%" . $_REQUEST["nt_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_macip.macip_ipaddr LIKE '" . $_REQUEST["nt_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_macip.macip_ipaddr NOT LIKE '" . $_REQUEST["nt_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_macip.macip_ipaddr NOT LIKE '" . $_REQUEST["nt_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_macip.macip_ipaddr = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_macip.macip_ipaddr != ''";
         }
     }
 
     if (strlen($_REQUEST["nt_filter"])) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
             $sql_where .= " (imb_macip.macip_port_hex LIKE '%" . $_REQUEST["nt_filter"] . "%' OR " .
                 "imb_macip.macip_port_list LIKE '%" . $_REQUEST["nt_filter"] . "%' OR " .
 				"imb_macip.macip_lastchange_date LIKE '%" . $_REQUEST["nt_filter"] . "%' OR " .
 				"imb_macip.macip_scan_date LIKE '%" . $_REQUEST["nt_filter"] . "%')";
    }
 
 
 
 		$sortby = $_REQUEST["sort_column"];
 	if ($sortby=="net_ipaddr") {
 		$sortby = "INET_ATON(net_ipaddr)";
 	}
 	
 		$query_string = "SELECT  imb_auto_updated_nets.*,user_auth.username as net_change_user_name from imb_auto_updated_nets left join user_auth on (imb_auto_updated_nets.net_change_user=user_auth.id)" .
			" where `net_type`=1 " .
 			" ORDER BY " . $sortby . " " . $_REQUEST["sort_direction"];
 			
                                                                                  
         if (($apply_limits) && ($row_limit != 999999)) {
             $query_string .= " LIMIT " . ($row_limit*($_REQUEST["nt_page"]-1)) . "," . $row_limit;
         }
 
         return db_fetch_assoc($query_string);
     
 }
 
 function impblinding_view_get_net_add_records(&$sql_where, $apply_limits = TRUE, $row_limit = -1) {
     /* form the 'where' clause for our main sql query */

     if ((strlen($_REQUEST["na_ip_filter"]) > 0)||($_REQUEST["na_ip_filter_type_id"] > 5)) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["na_ip_filter_type_id"]) {
             case "1": /* do not filter */
                 break;
             case "2": /* matches */
                 $sql_where .= " imb_auto_updated_nets.net_ipaddr='" . $_REQUEST["na_ip_filter"] . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " imb_auto_updated_nets.net_ipaddr LIKE '%%" . $_REQUEST["na_ip_filter"] . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " imb_auto_updated_nets.net_ipaddr LIKE '" . $_REQUEST["na_ip_filter"] . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " imb_auto_updated_nets.net_ipaddr NOT LIKE '" . $_REQUEST["na_ip_filter"] . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " imb_auto_updated_nets.net_ipaddr NOT LIKE '" . $_REQUEST["na_ip_filter"] . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " imb_auto_updated_nets.net_ipaddr = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " imb_auto_updated_nets.net_ipaddr != ''";
         }
     }
 
     if (strlen($_REQUEST["na_filter"])) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
             $sql_where .= " ( net_change_time LIKE '%" . $_REQUEST["na_filter"] . "%' OR " .
 				" net_description LIKE '%" . $_REQUEST["na_filter"] . "%' OR " .
 				" net_mask LIKE '%" . $_REQUEST["na_filter"] . "%')";
    }
 
 
 
 		$sortby = $_REQUEST["sort_column"];
 	if ($sortby=="net_ipaddr") {
 		$sortby = "INET_ATON(net_ipaddr)";
 	}
 	
 		$query_string = "SELECT  imb_auto_updated_nets.*, " .
		" IF(`net_ttl`='0', 'Постоянно',DATE_ADD(imb_auto_updated_nets.net_change_time, INTERVAL  `net_ttl` HOUR)) as net_ttl_date,user_auth.username as net_change_user_name, IF(imb_auto_updated_nets.net_device_id=0,'<== Any Device ==>',imb_devices.description) as description " .
		" from imb_auto_updated_nets " . 
		" left join user_auth on (imb_auto_updated_nets.net_change_user=user_auth.id)" .
		" left join imb_devices on (imb_auto_updated_nets.net_device_id=imb_devices.device_id) " .
			" where `net_type`=2 " .
 			" ORDER BY " . $sortby . " " . $_REQUEST["sort_direction"];
 			
                                                                                  
         if (($apply_limits) && ($row_limit != 999999)) {
             $query_string .= " LIMIT " . ($row_limit*($_REQUEST["na_page"]-1)) . "," . $row_limit;
         }
 
         return db_fetch_assoc($query_string);
     
 }
 
 
 
 
 function impblinding_view_header() {
     global $title, $colors, $config;
 ?>
 <script type="text/javascript">
 <!--
 function applyReportFilterChange(objForm) {
     strURL = '?report=' + objForm.report[objForm.report.selectedIndex].value;
     document.location = strURL;
 }
 
 
 function applyPortFilterChange(objForm) {
     strURL = '?report=ports';
 //    strURL = strURL + '&d_site_id=' + objForm.d_site_id[objForm.d_site_id.selectedIndex].value;
     strURL = strURL + '&p_status=' + objForm.p_status[objForm.p_status.selectedIndex].value;
 	strURL = strURL + '&p_zerro_status=' + objForm.p_zerro_status[objForm.p_zerro_status.selectedIndex].value;
     strURL = strURL + '&p_device_id=' + objForm.p_device_id[objForm.p_device_id.selectedIndex].value;
     strURL = strURL + '&p_device_type_id=' + objForm.p_device_type_id[objForm.p_device_type_id.selectedIndex].value;
     strURL = strURL + '&p_filter=' + objForm.p_filter.value;
     document.location = strURL;
 }
 
 function applyDeviceFilterChange(objForm) {
     strURL = '?report=devices';
 //    strURL = strURL + '&d_site_id=' + objForm.d_site_id[objForm.d_site_id.selectedIndex].value;
     strURL = strURL + '&d_status=' + objForm.d_status[objForm.d_status.selectedIndex].value;
 //  strURL = strURL + '&d_type_id=' + objForm.d_type_id[objForm.d_type_id.selectedIndex].value;
     strURL = strURL + '&d_device_type_id=' + objForm.d_device_type_id[objForm.d_device_type_id.selectedIndex].value;
     strURL = strURL + '&d_filter=' + objForm.d_filter.value;
     document.location = strURL;
 }
 
 function applyMacFilterChange(objForm) {
     strURL = '?report=macs';
     strURL = strURL + '&m_device_id=' + objForm.m_device_id[objForm.m_device_id.selectedIndex].value;
 //    strURL = strURL + '&m_rowstoshow=' + objForm.m_rowstoshow[objForm.m_rowstoshow.selectedIndex].value;
     strURL = strURL + '&m_rows_selector=' + objForm.m_rows_selector[objForm.m_rows_selector.selectedIndex].value;
     strURL = strURL + '&m_mac_filter_type_id=' + objForm.m_mac_filter_type_id.value;
     strURL = strURL + '&m_mac_filter=' + objForm.m_mac_filter.value;
     strURL = strURL + '&m_filter=' + objForm.m_filter.value;
     strURL = strURL + '&m_ip_filter_type_id=' + objForm.m_ip_filter_type_id.value;
     strURL = strURL + '&m_ip_filter=' + objForm.m_ip_filter.value;
 	strURL = strURL + '&m_port_filter_type_id=' + objForm.m_port_filter_type_id.value;
 	strURL = strURL + '&m_port_filter=' + objForm.m_port_filter.value;
	strURL = strURL + '&m_sost=' + objForm.m_sost.value;
     document.location = strURL;
 }
 
 function applyRecentMacFilterChange(objForm) {
     strURL = '?report=recent_macips';
 //    strURL = strURL + '&m_site_id=' + objForm.m_site_id[objForm.m_site_id.selectedIndex].value;
     strURL = strURL + '&r_device_id=' + objForm.r_device_id[objForm.r_device_id.selectedIndex].value;
 //    strURL = strURL + '&m_rowstoshow=' + objForm.m_rowstoshow[objForm.m_rowstoshow.selectedIndex].value;
     strURL = strURL + '&r_rows_selector=' + objForm.r_rows_selector[objForm.r_rows_selector.selectedIndex].value;
     strURL = strURL + '&r_mac_filter_type_id=' + objForm.r_mac_filter_type_id.value;
     strURL = strURL + '&r_mac_filter=' + objForm.r_mac_filter.value;
     strURL = strURL + '&r_filter=' + objForm.r_filter.value;
     strURL = strURL + '&r_ip_filter_type_id=' + objForm.r_ip_filter_type_id.value;
     strURL = strURL + '&r_ip_filter=' + objForm.r_ip_filter.value;
 	strURL = strURL + '&r_date_id=' + objForm.r_date_id.value;
     document.location = strURL;
 }
 
 function applyBMacFilterChange(objForm) {
     strURL = '?report=blmacs';
 //    strURL = strURL + '&b_site_id=' + objForm.b_site_id[objForm.b_site_id.selectedIndex].value;
     strURL = strURL + '&b_device_id=' + objForm.b_device_id[objForm.b_device_id.selectedIndex].value;
 //    strURL = strURL + '&b_rowstoshow=' + objForm.b_rowstoshow[objForm.b_rowstoshow.selectedIndex].value;
     strURL = strURL + '&b_rows_selector=' + objForm.b_rows_selector[objForm.b_rows_selector.selectedIndex].value;
     strURL = strURL + '&b_mac_filter_type_id=' + objForm.b_mac_filter_type_id.value;
     strURL = strURL + '&b_mac_filter=' + objForm.b_mac_filter.value;
     strURL = strURL + '&b_filter=' + objForm.b_filter.value;
     strURL = strURL + '&b_ip_filter_type_id=' + objForm.b_ip_filter_type_id.value;
     strURL = strURL + '&b_ip_filter=' + objForm.b_ip_filter.value;
     document.location = strURL;
 }
 function applyInfoFilterChange(objForm) {
     strURL = '?report=info';
     strURL = strURL + '&i_device_id=' + objForm.i_device_id[objForm.i_device_id.selectedIndex].value;
 //    strURL = strURL + '&m_rowstoshow=' + objForm.m_rowstoshow[objForm.m_rowstoshow.selectedIndex].value;
     strURL = strURL + '&i_rows_selector=' + objForm.i_rows_selector[objForm.i_rows_selector.selectedIndex].value;
     strURL = strURL + '&i_mac_filter_type_id=' + objForm.i_mac_filter_type_id.value;
     strURL = strURL + '&i_mac_filter=' + objForm.i_mac_filter.value;
     strURL = strURL + '&i_filter=' + objForm.i_filter.value;
     strURL = strURL + '&i_ip_filter_type_id=' + objForm.i_ip_filter_type_id.value;
     strURL = strURL + '&i_ip_filter=' + objForm.i_ip_filter.value;
 	strURL = strURL + '&i_port_filter_type_id=' + objForm.i_port_filter_type_id.value;
 	strURL = strURL + '&i_port_filter=' + objForm.i_port_filter.value;
     document.location = strURL;
 }
 function applyBanipFilterChange(objForm) {
     strURL = '?report=banips';
 //    strURL = strURL + '&bn_device_id=' + objForm.bn_device_id[objForm.bn_device_id.selectedIndex].value;
 //    strURL = strURL + '&m_rowstoshow=' + objForm.m_rowstoshow[objForm.m_rowstoshow.selectedIndex].value;
     strURL = strURL + '&bn_rows_selector=' + objForm.bn_rows_selector[objForm.bn_rows_selector.selectedIndex].value;
 //    strURL = strURL + '&bn_mac_filter_type_id=' + objForm.bn_mac_filter_type_id.value;
 //    strURL = strURL + '&bn_mac_filter=' + objForm.bn_mac_filter.value;
 //    strURL = strURL + '&bn_filter=' + objForm.bn_filter.value;
     strURL = strURL + '&bn_ip_filter_type_id=' + objForm.bn_ip_filter_type_id.value;
     strURL = strURL + '&bn_ip_filter=' + objForm.bn_ip_filter.value;
 	strURL = strURL + '&bn_banip_aproved=' + objForm.bn_banip_aproved.value;
 	strURL = strURL + '&bn_banip_aplled=' + objForm.bn_banip_aplled.value;	
 	strURL = strURL + '&bn_banip_manual=' + objForm.bn_banip_manual.value;	
 	strURL = strURL + '&bn_banip_type=' + objForm.bn_banip_type.value;
 //	strURL = strURL + '&bn_port_filter=' + objForm.bn_port_filter.value;
     document.location = strURL;
 }
 -->
 </script>
 <table align="center" width="98%" cellpadding=1 cellspacing=0 border=0 bgcolor="#<?php print $colors["header"];?>">
     <tr>
         <td>
             <table cellpadding=1 cellspacing=0 border=0 bgcolor="#<?php print $colors["form_background_dark"];?>" width="100%">
                 <form name="form_impblinding_view_reports">
                 <tr>
                     <td bgcolor="#<?php print $colors["header"];?>" style="padding: 3px;" colspan="12">
                         <table width="100%" cellpadding="0" cellspacing="0">
                             <tr>
                                 <td bgcolor="#<?php print $colors["header"];?>" class="textHeaderDark"><strong><?php print $title;?></strong></td>
                                 <td width="1" align="right">
									<button type='' VALUE="Alert"  onclick="document.forms['form_impblinding_view_reports'].report.value='devices';document.forms[1].submit();" ><img src=" <?php print($config['url_path']); ?>plugins/impblinding/images/switches1.gif" alt="Быстрый переход к устройствам" ></button></p>
								</td>
                                 <td width="1" align="right">
                                     <select name="report" onChange="applyReportFilterChange(document.form_impblinding_view_reports)">
                                     <option value="macips"<?php if ($_REQUEST["report"] == "macips") {?> selected<?php }?>>Управление привязками</option>
                                     <option value="blmacs"<?php if ($_REQUEST["report"] == "blmacs") {?> selected<?php }?>>Заблокированные МАС-и</option>
 									<option value="banips"<?php if ($_REQUEST["report"] == "banips") {?> selected<?php }?>>Управление банами</option>
                                     <option value="ports"<?php if ($_REQUEST["report"] == "ports") {?> selected<?php }?>>Управление портами</option>
                                     <option value="devices"<?php if ($_REQUEST["report"] == "devices") {?> selected<?php }?>>Устройства</option>
									 <option value="net_del"<?php if ($_REQUEST["report"] == "net_del") {?> selected<?php }?>>Авто Удаление</option>
									 <option value="net_add"<?php if ($_REQUEST["report"] == "net_add") {?> selected<?php }?>>Авто Создание (Подключение)</option>
 									<option value="recent_macips"<?php if ($_REQUEST["report"] == "recent_macips") {?> selected<?php }?>>Сводное сканированное</option>
 									<option value="info"<?php if ($_REQUEST["report"] == "info") {?> selected<?php }?>>Быстрый поиск</option>
                                     </select>
                                 </td>
                             </tr>
                         </table>
                     </td>
                 </tr>
                 </form>
 <?php
 }
 
 
 function impblinding_view_footer() {
 ?>
                             </table>
                         </td>
                     </tr>
                 </table>
             </td>
         </tr>
     </table>
 <br>
 <?php
 }
 
 
 function impblinding_view_calendar() {
     global $title, $colors;
 ?>
 <script type="text/javascript">
 <!--
 	// Initialize the calendar
 	calendar=null;
 
 	// This function displays the calendar associated to the input field 'id'
 	function showCalendar(id) {
 		var el = document.getElementById(id);
 		if (calendar != null) {
 			// we already have some calendar created
 			calendar.hide();  // so we hide it first.
 		} else {
 			// first-time call, create the calendar.
 			var cal = new Calendar(true, null, selected, closeHandler);
 			cal.weekNumbers = false;  // Do not display the week number
 			cal.showsTime = true;     // Display the time
 			cal.time24 = true;        // Hours have a 24 hours format
 			cal.showsOtherMonths = false;    // Just the current month is displayed
 			calendar = cal;                  // remember it in the global var
 			cal.setRange(1900, 2070);        // min/max year allowed.
 			cal.create();
 		}
 
 		calendar.setDateFormat('%Y-%m-%d %H:%M');    // set the specified date format
 		calendar.parseDate(el.value);                // try to parse the text in field
 		calendar.sel = el;                           // inform it what input field we use
 
 		// Display the calendar below the input field
 		calendar.showAtElement(el, "Br");        // show the calendar
 
 		return false;
 	}
 
 	// This function update the date in the input field when selected
 	function selected(cal, date) {
 		cal.sel.value = date;      // just update the date in the input field.
 	}
 
 	// This function gets called when the end-user clicks on the 'Close' button.
 	// It just hides the calendar without destroying it.
 	function closeHandler(cal) {
 		cal.hide();                        // hide the calendar
 		calendar = null;
 	}
 -->
 </script>
 <?php
 }
 
 function impblinding_view_ports() {
     global $title, $colors, $config, $port_actions, $imp_port_state_color;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("p_device_id"));
     input_validate_input_number(get_request_var_request("page"));
     input_validate_input_number(get_request_var_request("p_device_type_id"));
     input_validate_input_number(get_request_var_request("p_status"));
	 input_validate_input_number(get_request_var_request("p_port_number"));
 	input_validate_input_number(get_request_var_request("p_zerro_status"));
     
     /* ==================================================== */
 
     /* clean up search string */
     if (isset($_REQUEST["detail"])) {
         $_REQUEST["detail"] = sanitize_search_string(get_request_var("detail"));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["p_filter"])) {
         $_REQUEST["p_filter"] = sanitize_search_string(get_request_var("p_filter"));
     }
 
     /* if the user pushed the 'clear' button */
     if (isset($_REQUEST["clear_ports_x"])) {
         kill_session_var("sess_impb_view_ports_current_page");
         kill_session_var("sess_impb_view_ports_detail");
         kill_session_var("sess_impb_view_ports_device_type_id");
         kill_session_var("sess_impb_view_ports_filter");
         kill_session_var("sess_impb_view_ports_device_id");
         kill_session_var("sess_impb_view_ports_status");
		 kill_session_var("sess_impb_view_ports_number");
         //kill_session_var("sess_impb_view_ports_report");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["p_filter"]);
         unset($_REQUEST["p_device_type_id"]);
         unset($_REQUEST["detail"]);
         unset($_REQUEST["p_device_id"]);
         unset($_REQUEST["p_status"]);
		 unset($_REQUEST["p_port_number"]);
 		unset($_REQUEST["p_zerro_status"]);
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("p_device_type_id", "sess_impb_view_ports_device_type_id");
         $changed += impblinding_check_changed("p_filter", "sess_impb_view_ports_filter");
         $changed += impblinding_check_changed("detail", "sess_impb_view_ports_detail");
         $changed += impblinding_check_changed("p_device_id", "sess_impb_view_ports_device_id");
         $changed += impblinding_check_changed("p_status", "sess_impb_view_ports_status");
		 $changed += impblinding_check_changed("p_port_number", "sess_impb_view_ports_number");
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["p_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["p_page"] = $_REQUEST["page"];
             }
         }
     }
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("p_page", "sess_impb_view_ports_current_page", "1");
     load_current_session_value("page", "sess_impb_view_ports_current_page", "1");
     load_current_session_value("detail", "sess_impb_view_ports_detail", "false");
     load_current_session_value("p_device_type_id", "sess_impb_view_ports_device_type_id", "-1");
     load_current_session_value("p_filter", "sess_impb_view_ports_filter", "");
     load_current_session_value("p_device_id", "sess_impb_view_ports_device_id", "-1");
     load_current_session_value("p_status", "sess_impb_view_ports_status", "-1");
	 load_current_session_value("p_port_number", "sess_impb_view_ports_number", "");
     load_current_session_value("report", "sess_impb_view_ports_report", "ports"); 
         
     impblinding_view_header();
 
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_port_filter_table.php");
 
     impblinding_view_footer();
 
     html_start_box("<strong>Режим просмотра портов</strong>", "98%", $colors["header"], "3", "center", "");
 
     $sql_where = "";
     $ports = impblinding_view_get_ports_records($sql_where);
         $total_rows = db_fetch_cell("SELECT
             COUNT(imb_ports.port_number)
             FROM imb_ports
             $sql_where");
 
 
 
     /* generate page list */
     $url_page_select = str_replace("&page", "?page", get_page_list($_REQUEST["p_page"], MAX_DISPLAY_PAGES, read_config_option("dimpb_num_rows"), $total_rows, "impblinding_view.php"));
 
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
             <td colspan='11'>
                 <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                     <tr>
                         <td align='left' class='textHeaderDark'>
                             <strong>&lt;&lt; "; if ($_REQUEST["p_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?page=" . ($_REQUEST["p_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["p_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                         </td>\n
                         <td align='center' class='textHeaderDark'>
                             Showing Rows " . ((read_config_option("dimpb_num_rows")*($_REQUEST["p_page"]-1))+1) . " to " . ((($total_rows < read_config_option("dimpb_num_rows")) || ($total_rows < (read_config_option("dimpb_num_rows")*$_REQUEST["p_page"]))) ? $total_rows : (read_config_option("dimpb_num_rows")*$_REQUEST["p_page"])) . " of $total_rows [$url_page_select]
                         </td>\n
                         <td align='right' class='textHeaderDark'>
                             <strong>"; if (($_REQUEST["p_page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?page=" . ($_REQUEST["p_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["p_page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                         </td>\n
                     </tr>
                 </table>
             </td>
         </tr>\n";
 
     print $nav;
 
         //html_header_checkbox(array("<br>Description", "<br>Hostname", "Port<br>number", "Port<br>name", "Port imblinding<br>state", "Режим нулевого<br>IP (Vista)", "Count imblinding<br>records", "Count scan<br>IP-MAC-PORT","Port<br>type", "Port<br>state", "Scan<br>Date"));
 		html_header_checkbox(array("<br>Описание", "<br>IP(имя)", "Номер<br>порта", "Имя<br>порта", "Режим<br>привязки", "Кол-во записей<br>IP-MAC на порту", "Кол-во IP-MAC<br>насканировано","Тип<br>порта", "Состояниие<br>порта", "Время последнего<br>изменения"));
 
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
				$port_imp_active_color = '00BD27';
				if ($port["port_status"] == "0") {
					$port_imp_active_color = 'FF0000';
				}

				
				if ($port["port_LoopVLAN"] == "0") {
					form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $port["port_id"]); $i++;
				}else{
					impb_form_alternate_row_color("#EDA9A9", "#EDA9A9", $i, 'line' . $port["port_id"], "background-color:#EDA9A9"); $i++;
					$port_imb_active = $port_imb_active . " <span style='color: #FF0000;'>LOOP " . $port["port_LoopVLAN"] . "</span>";
				};				

 			form_selectable_cell("<a class='linkEditMain' href='impblinding_devices.php?action=edit&device_id=" . $port["device_id"] . "'>" . 
 				(strlen($_REQUEST["p_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["p_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port["description"]) : $port["description"]) . "</strong></a>", $port["port_id"]);			
 			form_selectable_cell($port["hostname"], $port["port_id"] );
 			//form_selectable_cell(number_format($port["port_number"]), $port["port_id"] );

 			form_selectable_cell(number_format($port["port_number"]) . " <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_view.php?action=preview&host_id=" . $port['cid'] . "&snmp_index=" . $port['port_number'] . "&graph_template_id=0&filter=") . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' border='0' alt='' title='View Graph' align='absmiddle'></a>", $port["port_id"]);
																																								//http://sys.ion63.ru/graph_view.php?action=preview&host_id=45&rows=25&graph_template_id=0&filter=
			form_selectable_cell($port["port_name"], $port["port_id"] );
			form_selectable_cell(dimpb_convert_port_state_2_html($port), $port["port_id"]);
			//form_selectable_cell("<strong> <span style='color: $port_imp_state_color;'>$port_imp_state</span></strong>", $port["port_id"] );
 			
			
			//form_selectable_cell(imp_convert_port_zerroip_state_2str_full($port["port_zerroip_state"], $port["device_id"]), $port["port_id"]  );
 			//form_selectable_cell("<strong> <span style='color: $port_imp_zerroip_state_color;'>$port_imp_zerroip_state</span></strong>", $port["port_id"] );
 
 			form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?m_device_id=+" . $port["device_id"] . "&m_ip_filter_type_id=1&m_ip_filter=&m_mac_filter_type_id=1&m_mac_filter=&m_port_filter_type_id=2&m_port_filter=" . $port["port_number"] . "&m_rows_selector=-1&m_filter=&m_page=1&report=macs&x=22&y=4'>" . $port["count_macip_record"]  , $port["port_id"]);			
 			
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
             print "<tr><td><em>No IpMacPorts Ports found</em></td></tr>";
         }
         html_end_box(false);
         lm_draw_actions_dropdown($port_actions, "ports");
 
 }
 
 function impblinding_view_devices() {
     global $title, $report, $colors, $impblinding_search_types, $impblinding_device_types, $rows_selector, $config, $device_actions;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("d_device_id"));
     input_validate_input_number(get_request_var_request("d_device_type_id"));
     input_validate_input_number(get_request_var_request("d_status"));
     input_validate_input_number(get_request_var_request("page"));
	 input_validate_input_number(get_request_var_request("stab"));
     /* ==================================================== */
 
     /* clean up search string */
     if (isset($_REQUEST["d_filter"])) {
         $_REQUEST["d_filter"] = sanitize_search_string(get_request_var("d_filter"));
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
     if (isset($_REQUEST["clear_devices_x"])) {
         kill_session_var("sess_impb_view_device_current_page");
         kill_session_var("sess_impb_view_device_filter");
         kill_session_var("sess_impb_view_device_device_type_id");
         kill_session_var("sess_impb_view_device_status");
 		kill_session_var("sess_impb_view_device_sort_column");
         kill_session_var("sess_impb_view_device_sort_direction");
		 kill_session_var("sess_impb_view_device_dtab");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["d_filter"]);
         unset($_REQUEST["d_device_type_id"]);
         unset($_REQUEST["d_status"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);		
		 unset($_REQUEST["dtab"]);
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("d_filter", "sess_impb_view_device_filter");
         $changed += impblinding_check_changed("d_device_type_id", "sess_impb_view_device_device_type_id");
         $changed += impblinding_check_changed("d_status", "sess_impb_view_device_status");
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["d_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["d_page"] = $_REQUEST["page"];
             }
         }
     }
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("d_page", "sess_impb_view_device_current_page", "1");
     load_current_session_value("page", "sess_impb_view_device_current_page", "1");
     load_current_session_value("d_filter", "sess_impb_view_device_filter", "");
     load_current_session_value("d_device_type_id", "sess_impb_view_device_device_type_id", "-1");
     load_current_session_value("d_status", "sess_impb_view_device_status", "-1");
     load_current_session_value("sort_column", "sess_impb_view_device_sort_column", "order_id");
     load_current_session_value("sort_direction", "sess_impb_view_device_sort_direction", "ASC");
	 load_current_session_value("dtab", "sess_impb_view_device_dtab", "1");
 	
     impblinding_view_header();
 
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_device_filter_table.php");
	 
	 
	 
     impblinding_view_footer();
 	
	dimpb_tabs();
	
     $sql_where = "";
 
    $devices = impblinding_view_get_device_records($sql_where);
 	$devices_sum = impblinding_view_get_device_records($sql_where);
 	
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
 	
     $total_rows = db_fetch_cell("SELECT
         COUNT(imb_devices.device_id)
         FROM imb_devices
         $sql_where");
     html_start_box("", "98%", $colors["header"], "3", "center", "");
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["d_page"], MAX_DISPLAY_PAGES, read_config_option("dimpb_num_rows"), $total_rows, "impblinding_view.php?report=devices&d_filter=" . $_REQUEST["d_filter"] . "&d_status=" . $_REQUEST["d_status"]."&");
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
             <td colspan='18'>
                 <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                     <tr>
                         <td align='left' class='textHeaderDark'>
                             <strong>&lt;&lt; "; if ($_REQUEST["d_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?report=devices&d_filter=" . $_REQUEST["d_filter"] . "&d_status=" . $_REQUEST["d_status"] . "&page=" . ($_REQUEST["d_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["d_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                         </td>\n
                         <td align='center' class='textHeaderDark'>
                             Showing Rows " . ((read_config_option("dimpb_num_rows")*($_REQUEST["d_page"]-1))+1) . " to " . ((($total_rows < read_config_option("dimpb_num_rows")) || ($total_rows < (read_config_option("dimpb_num_rows")*$_REQUEST["d_page"]))) ? $total_rows : (read_config_option("dimpb_num_rows")*$_REQUEST["d_page"])) . " of $total_rows [$url_page_select]
                         </td>\n
                         <td align='right' class='textHeaderDark'>
                             <strong>"; if (($_REQUEST["d_page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?report=devices&d_filter=" . $_REQUEST["d_filter"] . "&d_status=" . $_REQUEST["d_status"] . "&page=" . ($_REQUEST["d_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["d_page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                         </td>\n
                     </tr>
                 </table>
             </td>
         </tr>\n";
 
 	print $nav;
 
 	$display_text = array(
 		"description" => array("Устройство", "ASC"),
		"order_id" => array("ord", "ASC"),
 		"snmp_status" => array("Статус", "ASC"),
 		"enable_acl_mode" => array("ARP<br>ACL", "ASC"),
 		"enable_log_trap" => array("Log<br>Trap", "ASC"),
 		"disabled" => array("Enable", "ASC"),
 		"hostname" => array("IP-Адрес", "ASC"),
 		"dev_type_description" => array("Тип<br>устройства","DESC"),
 		//"count_unsaved_actions" => array("Нес.<br>опер.","DESC"),
 		"ip_mac_total" => array("Всего записей<br>IP-MAC-Port","DESC"),
 		"ip_mac_blocked_total" => array("Блоки","DESC"),
 		"ports_total" => array("Порты","DESC"),
 		"ports_enable_total" => array("Портов с<br>привязкой","DESC"),
 		"ports_enable_zerroip_total" => array("Zerro<br>IP(Vista)", "DESC"),
 		"last_rundate" => array("Время<br>последнего<br>опроса","DESC"),
 		"last_runduration" => array("Опрос","DESC"),
 		" " => array(" ","DESC"));
 
 
     html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 	
 	
 	
 	
     $i = 0;
 	$str_count_ipmac_total = 0;
 	$str_blmac_blmac_total = 0;
 	$str_count_ports_total = 0;
 	$str_count_ports_en_total = 0;
 	$str_count_ports_zerr_en_total = 0;	
     if (sizeof($devices) > 0) {
         foreach ($devices as $device) {
             //form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 			$bgc = db_fetch_cell("SELECT hex FROM colors WHERE id='" . $device["color_row"] . "'");
			form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $device["device_id"]); $i++;
                 if ($device["snmp_status"] == 3) {
 					$str_count_ipmac = $device["ip_mac_total"];
 					$str_blmac_blmac = $device["ip_mac_blocked_total"];
 					$str_count_ports = $device["ports_total"];
 					$str_count_ports_en = $device["ports_enable_total"];
 					$str_count_ports_zerr_en = $device["ports_enable_zerroip_total"];
 				} else {
 					$str_count_ipmac = "[" . $device["ip_mac_offline_total"] . "]";
 					$str_blmac_blmac = "[" . $device["ip_mac_blocked_offline_total"] . "]";
 					$str_count_ports = "[" . $device["ports_offline_total"] . "]";
 					$str_count_ports_en = "[" . $device["ports_offline_enable_total"] . "]";
 					$str_count_ports_zerr_en = "[" . $device["ports_offline_enable_zerroip_total"] . "]";
 				}
 				$str_count_ipmac_total = $str_count_ipmac_total + $str_count_ipmac;
 				$str_blmac_blmac_total = $str_blmac_blmac_total+$str_blmac_blmac;
 				$str_count_ports_total = $str_count_ports_total+$str_count_ports;
 				$str_count_ports_en_total = $str_count_ports_en_total+$str_count_ports_en;
 				$str_count_ports_zerr_en_total = $str_count_ports_zerr_en_total+$str_count_ports_zerr_en;	
 				
 				
 				//form_selectable_cell("<a class='linkEditMain' href='host.php?action=edit&id=" . $host["id"] . "'>" .
 				//(strlen($_REQUEST["filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $host["description"]) : $host["description"]) . "</a>", $host["id"], 250);
 				
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_devices.php?action=edit&device_id=" . $device["device_id"] . "'>" .
 					(strlen($_REQUEST["d_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["d_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $device["description"]) : $device["description"]) . "</a>", $device["device_id"],250,"background-color: #" . $bgc . ";");
 				form_selectable_cell( $device["order_id"], $device["order_id"] ,"","background-color: #" . $bgc . ";");
				
 				form_selectable_cell(get_colored_device_status(($device["disabled"] == "on" ? true : false), $device["snmp_status"]), $device["device_id"] ,"","background-color: #" . $bgc . ";");
 				form_selectable_cell(get_colored_status($device["enable_acl_mode"]), $device["device_id"] );
 				form_selectable_cell(get_colored_status($device["enable_log_trap"]), $device["device_id"] );
 				form_selectable_cell(get_colored_device_status(($device["disabled"] == "on" ? true : false), $device["snmp_status"]), $device["device_id"] );
 				form_selectable_cell((strlen($_REQUEST["d_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["d_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $device["hostname"]) : $device["hostname"]), $device["device_id"]);
 				
 				form_selectable_cell($device["dev_type_description"], $device["device_id"],"","background-color: #" . $bgc . ";" );
 
 
 				//form_selectable_cell("<a class='linkEditMain' href='impblinding_logs.php?l_user_id=-1&l_device_id=+" . $device["device_id"] . "&l_date=-1&l_save=0&l_filter=&l_page=1&report=logs&x=17&y=10'>" . $device["count_unsaved_actions"] . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=macs&m_device_id=+" . $device["device_id"] . "&m_ip_filter_type_id=1&m_ip_filter=&m_mac_filter_type_id=1&m_mac_filter=&m_port_filter_type_id=&m_port_filter=&m_rows_selector=-1&m_filter=&m_page=1&report=macs&x=22&y=4'>" . $str_count_ipmac . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=blmacs&b_device_id=+" . $device["device_id"] . "&b_ip_filter_type_id=1&b_ip_filter=&b_port_filter=&b_mac_filter_type_id=1&b_mac_filter=&m_port_filter_type_id=&m_port_filter=&b_rows_selector=-1&b_filter=&b_page=1&report=blmacs&x=15&y=8'>" . $str_blmac_blmac . "</a>", $device["device_id"]);
 				
 
 
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=ports&p_device_type_id=-1&p_device_id=+" . $device["device_id"] . "&p_status=-1&p_zerro_status=-1&p_filter=&p_page=1&report=ports&x=11&y=7'>" . $str_count_ports . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=ports&p_device_type_id=-1&p_device_id=+" . $device["device_id"] . "&p_status=2&p_zerro_status=-1&p_filter=&p_page=1&report=ports&x=11&y=7'>" . $str_count_ports_en . "</a>", $device["device_id"]);
 				form_selectable_cell("<a class='linkEditMain' title='title-title
 				second' href='impblinding_view.php?report=ports&p_device_type_id=-1&p_device_id=+" . $device["device_id"] . "&p_status=2&p_zerro_status=1&p_filter=&p_page=1&report=ports&x=11&y=7'>" . $str_count_ports_zerr_en . "</a>", $device["device_id"]);
 				
 				form_selectable_cell(imb_fromat_datetime($device["last_rundate"]), $device["device_id"] );
 				
 				form_selectable_cell(number_format($device["last_runduration"]), $device["device_id"] );
 				
 				form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?action=device_query&id=1&host_id=" . $device["device_id"] . "'><img src='../../images/reload_icon_small.gif' alt='Reload Data Query' border='0' align='absmiddle'></a>", $device["device_id"]);
 
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
                     <a class="linkEditMain" href="impblinding_view.php?m_device_id-1=&m_ip_filter_type_id=1&m_ip_filter=&m_mac_filter_type_id=1&m_mac_filter=&m_port_filter_type_id=&m_port_filter=&m_rows_selector=-1&m_filter=&m_page=1&report=macs&x=22&y=4"><?php print $str_count_ipmac_total ;?></a>
                 </td>
                 <td >
                     <a class="linkEditMain" href="impblinding_view.php?b_device_id=-1&b_ip_filter_type_id=1&b_ip_filter=&b_port_filter=&b_mac_filter_type_id=1&b_mac_filter=&m_port_filter_type_id=&m_port_filter=&b_rows_selector=-1&b_filter=&b_page=1&report=blmacs&x=15&y=8"><?php print $str_blmac_blmac_total ;?></a>
                 </td >	
                 <td >
                     <a class="linkEditMain" href="impblinding_view.php?p_device_type_id=-1&p_device_id=-1&p_status=-1&p_filter=&p_page=1&report=ports&x=11&y=7"><?php print $str_count_ports_total;?></a>
                 </td >
                 <td >
                     <a class="linkEditMain" href="impblinding_view.php?p_device_type_id=-1&p_device_id=-1&p_status=2&p_zerro_status=-1&p_filter=&p_page=1&report=ports&x=11&y=7"><?php print $str_count_ports_en_total ;?></a>
                 </td > 
                 <td >
                     <a class="linkEditMain" href="impblinding_view.php?p_device_type_id=-1&p_device_id=-1&p_status=2&p_zerro_status=1&p_filter=&p_page=1&report=ports&x=11&y=7"><?php print $str_count_ports_zerr_en_total;?></a>
                 </td > 				
 			</tr>
 			<?php
 		
 		
 		
 		
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No D-Link IP-MAC-Port Binding Devices</em></td></tr>";
     }
     html_end_box(false);
 	lm_draw_actions_dropdown($device_actions, "devices");
 }
 
 
 
 
 
 
 function impblinding_view_macips() {
     global $title, $report, $colors, $impblinding_search_types, $impblinding_port_search_types, $rows_selector, $config, $macips_actions;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("m_device_id"));
     input_validate_input_number(get_request_var_request("m_mac_filter_type_id"));
     input_validate_input_number(get_request_var_request("m_ip_filter_type_id"));
 	 input_validate_input_number(get_request_var_request("m_port_filter_type_id"));
     input_validate_input_number(get_request_var_request("m_rows_selector"));
     input_validate_input_number(get_request_var_request("page"));
	 input_validate_input_number(get_request_var_request("m_sost"));
     /* ==================================================== */
 
 
     /* clean up filter string */
     if (isset($_REQUEST["m_filter"])) {
         $_REQUEST["m_filter"] = sanitize_search_string(get_request_var("m_filter"));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["m_ip_filter"])) {
         $_REQUEST["m_ip_filter"] = translate_ip_address(sanitize_search_string(get_request_var("m_ip_filter")));
 	}
     
 	/* clean up search string */
     if (isset($_REQUEST["m_mac_filter"])) {
         $_REQUEST["m_mac_filter"] = translate_mac_address(sanitize_search_string(get_request_var("m_mac_filter")));
     }
 
 	/* clean up search string */
     if (isset($_REQUEST["m_port_filter"])) {
         $_REQUEST["m_port_filter"] = sanitize_search_string(get_request_var("m_port_filter"));
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
     if (isset($_REQUEST["clear_macs_x"])) {
         kill_session_var("sess_impb_view_macs_current_page");
         kill_session_var("sess_impb_view_macs_filter");
         kill_session_var("sess_impb_view_macs_mac_filter_type_id");
         kill_session_var("sess_impb_view_macs_mac_filter");
         kill_session_var("sess_impb_view_macs_ip_filter_type_id");
         kill_session_var("sess_impb_view_macs_ip_filter");
         kill_session_var("sess_impb_view_macs_port_filter_type_id");
         kill_session_var("sess_impb_view_macs_port_filter");
         kill_session_var("sess_impb_view_macs_rows_selector");
         kill_session_var("sess_impb_view_macs_device_id");
		 kill_session_var("sess_impb_view_macs_sost");
         kill_session_var("sess_impb_view_macs_sort_column");
         kill_session_var("sess_impb_view_macs_sort_direction");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["m_filter"]);
         unset($_REQUEST["m_mac_filter_type_id"]);
         unset($_REQUEST["m_mac_filter"]);
         unset($_REQUEST["m_ip_filter_type_id"]);
         unset($_REQUEST["m_rows_selector"]);
         unset($_REQUEST["m_ip_filter"]);
         unset($_REQUEST["m_port_filter_type_id"]);
         unset($_REQUEST["m_port_filter"]);
         unset($_REQUEST["m_device_id"]);
		 unset($_REQUEST["m_sost"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("m_filter", "sess_impb_view_macs_filter");
         $changed += impblinding_check_changed("m_mac_filter_type_id", "sess_impb_view_macs_mac_filter_type_id");
         $changed += impblinding_check_changed("m_mac_filter", "sess_impb_view_macs_mac_filter");
         $changed += impblinding_check_changed("m_ip_filter_type_id", "sess_impb_view_macs_ip_filter_type_id");
         $changed += impblinding_check_changed("m_ip_filter", "sess_impb_view_macs_ip_filter");
         $changed += impblinding_check_changed("m_port_filter_type_id", "sess_impb_view_macs_port_filter_type_id");
         $changed += impblinding_check_changed("m_port_filter", "sess_impb_view_macs_port_filter");
         $changed += impblinding_check_changed("m_rows_selector", "sess_impb_view_macs_rows_selector");
         $changed += impblinding_check_changed("m_device_id", "sess_impb_view_macs_device_id");
		 $changed += impblinding_check_changed("m_sost", "sess_impb_view_macs_sost");
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["m_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["m_page"] = $_REQUEST["page"];
             }
         }
     }
 
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("report", "sess_impb_view_report", "macs");
     load_current_session_value("page", "sess_impb_view_macs_current_page", "1");
     load_current_session_value("m_page", "sess_impb_view_macs_current_page", "1");
     load_current_session_value("m_rowstoshow", "sess_impb_view_macs_rowstoshow", "2");
     load_current_session_value("m_filter", "sess_impb_view_macs_filter", "");
     load_current_session_value("m_mac_filter_type_id", "sess_impb_view_macs_mac_filter_type_id", "1");
     load_current_session_value("m_mac_filter", "sess_impb_view_macs_mac_filter", "");
     load_current_session_value("m_ip_filter_type_id", "sess_impb_view_macs_ip_filter_type_id", "1");
     load_current_session_value("m_ip_filter", "sess_impb_view_macs_ip_filter", "");
     load_current_session_value("m_port_filter_type_id", "sess_impb_view_macs_port_filter_type_id", "1");
     load_current_session_value("m_port_filter", "sess_impb_view_macs_port_filter", "");
     load_current_session_value("m_rows_selector", "sess_impb_view_macs_rows_selector", "-1");
     load_current_session_value("m_device_id", "sess_impb_view_macs_device_id", "-1");
	 load_current_session_value("m_sost", "sess_impb_view_macs_sost", "-1");
     load_current_session_value("sort_column", "sess_impb_view_macs_sort_column", "description");
     load_current_session_value("sort_direction", "sess_impb_view_macs_sort_direction", "ASC");
 
     /* set m_page variable */
     $_REQUEST["m_page"] = $_REQUEST["page"];
 
     impblinding_view_header();
 
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_mac_filter_table.php");
 
     impblinding_view_footer();
 
     $sql_where = "";
 
     if ($_REQUEST["m_rows_selector"] == -1) {
         $row_limit = read_config_option("dimpb_num_rows");
     }elseif ($_REQUEST["m_rows_selector"] == -2) {
         $row_limit = 999999;
     }else{
         $row_limit = $_REQUEST["m_rows_selector"];
     }
 
     $macips_results = impblinding_view_get_macips_records($sql_where, TRUE, $row_limit);
 
     html_start_box("", "98%", $colors["header"], "3", "center", "");
 
         $rows_query_string = "SELECT
             COUNT(imb_macip.device_id)
             FROM imb_macip
			 LEFT JOIN (SELECT l.segment,  v.*  FROM lb_staff l left JOIN lb_vgroups_s v ON l.vg_id = v.vg_id WHERE v.`archive`=0) lbs ON INET_ATON(imb_macip.macip_ipaddr) = lbs.segment 
             $sql_where";
 
      $total_rows = db_fetch_cell($rows_query_string);
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["m_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?m_device_id=" . $_REQUEST["m_device_id"] . "&amp;m_ip_filter_type_id=" . $_REQUEST["m_ip_filter_type_id"] . "&amp;m_ip_filter=" . $_REQUEST["m_ip_filter"] . "&amp;m_mac_filter_type_id=" . $_REQUEST["m_mac_filter_type_id"] . "&amp;m_mac_filter=" . $_REQUEST["m_mac_filter"] . "&");
 
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='13'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["m_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?m_device_id=" . $_REQUEST["m_device_id"] . "&amp;m_ip_filter_type_id=" . $_REQUEST["m_ip_filter_type_id"] . "&amp;m_ip_filter=" . $_REQUEST["m_ip_filter"] . "&amp;m_mac_filter_type_id=" . $_REQUEST["m_mac_filter_type_id"] . "&m_mac_filter=" . $_REQUEST["m_mac_filter"] .  "&amp;page=" . ($_REQUEST["m_page"]-1) . "&amp;'>"; } $nav .= "Previous"; if ($_REQUEST["m_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["m_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["m_page"]))) ? $total_rows : ($row_limit*$_REQUEST["m_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["m_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?m_device_id=" . $_REQUEST["m_device_id"] . "&amp;m_ip_filter_type_id=" . $_REQUEST["m_ip_filter_type_id"] . "&amp;m_ip_filter=" . $_REQUEST["m_ip_filter"] . "&amp;m_mac_filter_type_id=" . $_REQUEST["m_mac_filter_type_id"] . "&amp;m_mac_filter=" . $_REQUEST["m_mac_filter"] . "&amp;page=" . ($_REQUEST["m_page"]+1) . "&amp;'>"; } $nav .= "Next"; if (($_REQUEST["m_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
 
                $display_text = array(
                 "description" => array("Network<br>Device", "ASC"),
                 "hostname" => array("Network<br>Hostname", "ASC"),
                 "macip_ipaddr" => array("End Device<br>IP Address", "ASC"),
                 "macip_macaddr" => array("End Device<br>MAC Address", "ASC"),
                 "f_flat" => array("Komn", "DESC"),
 				"macip_port_list" => array("Port<br>List", "DESC"),
                 "macip_imb_status" => array("Record<br>status", "ASC"),
 				"macip_imb_action" => array("Record<br>action", "ASC"),
                 "macip_mode" => array("Record<br>Mode", "ASC"),
				 "macip_may_move" => array("Free", "DESC"),
 				"macip_lastchange_date" => array("Дата<br>Изменения", "ASC"),
                 "macip_scan_date" => array("Last<br>Scan Date", "DESC"));
				 
 
         html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 
     $i = 0;
 	$mac_font_size=read_config_option("dimpb_mac_addr_font_size");
     if (sizeof($macips_results) > 0) {
         foreach ($macips_results as $macips_result) {
             $scan_date = $macips_result["macip_scan_date"];
 
 		  if ($macips_result["macip_active_last_poll"] == 1)  {
 			$color_line_date="<span style='font-weight: bold;'>";
 		  }else{
 			$color_line_date="";
 		  }			
 			
            form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $macips_result["macip_id"]); $i++;
 			form_selectable_cell("<a class='linkEditMain' href='impblinding_devices.php?action=edit&amp;device_id=" . $macips_result["device_id"] . "'>" . 
 				(strlen($_REQUEST["m_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["m_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["description"]) : $macips_result["description"]) . "</a>", $macips_result["macip_id"]);
 			form_selectable_cell($macips_result["hostname"], $macips_result["macip_id"] );
 			
 			//<img src='../../images/reload_icon_small.gif' alt='Reload Data Query' border='0' align='absmiddle'>
			
			form_selectable_cell("<img src='" . $config['url_path'] . "plugins/impblinding/images/" . $macips_result["sig"] . ".png' TITLE='" . $macips_result["sig2"] . "' align='absmiddle'><a class='inkEditMain' TITLE='" . $macips_result["sig2"] . ' Адр:' . $macips_result["f_addr"] . "' href='impblinding_view.php?report=info&amp;i_device_id=-1&amp;i_ip_filter_type_id=2&amp;i_ip_filter=" . $macips_result["macip_ipaddr"] . "&amp;i_mac_filter_type_id=1&amp;i_mac_filter=&amp;i_port_filter_type_id=&amp;i_port_filter=&amp;i_rows_selector=-1&amp;i_filter=&amp;i_page=1&amp;report=info&amp;x=23&amp;y=10'>" . 
 				 (strlen($_REQUEST["m_ip_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["m_ip_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_ipaddr"]) : $macips_result["macip_ipaddr"]) . "</a>" . ($macips_result["ip_local_graph_id"]==0 ? '' : " <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_view.php?action=preview&host_id=62&graph_template_id=0&snmp_index=&filter=" . $macips_result['macip_ipaddr'] ) . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' border='0' alt='' title='View Graph' align='absmiddle'></a>") . (strlen($macips_result["equipm"])==0 ? '' : ' (R)') , $macips_result["macip_id"]);
 			
			
 			form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=info&amp;i_device_id=-1&amp;i_ip_filter_type_id=8&amp;i_ip_filter=&amp;i_mac_filter_type_id=2&amp;i_mac_filter=" . $macips_result["macip_macaddr"] . "&amp;i_port_filter_type_id=&amp;i_port_filter=&amp;i_rows_selector=-1&amp;i_filter=&amp;i_page=1&amp;report=info&amp;x=14&amp;y=6'><font size='" . $mac_font_size . "' face='Courier'>" . 
 				(strlen($_REQUEST["m_mac_filter"]) ? strtoupper(preg_replace("/(" . preg_quote($_REQUEST["m_mac_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_macaddr"])) : $macips_result["macip_macaddr"]) . "</font></a>", $macips_result["macip_id"]);
 				
 			//http://localhost/cacti/plugins/impblinding/impblinding_view.php?bn_ip_filter_type_id=2&bn_ip_filter=172.18.7.8&bn_banip_aproved=-1&bn_banip_aplled=-1&bn_banip_type=0&bn_banip_manual=-1&bn_filter=&bn_rows_selector=-1&bn_page=1&report=banips&x=13&y=9
 			// if ($macips_result["macip_banned"] == 1) {
 			// form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?bn_ip_filter_type_id=2&amp;bn_ip_filter=" . $macips_result["macip_ipaddr"] . "&amp;bn_banip_aproved=-1&amp;bn_banip_aplled=-1&amp;bn_banip_type=0&amp;bn_banip_manual=-1&amp;bn_filter=&amp;bn_rows_selector=-1&amp;bn_page=1&amp;report=banips&amp;x=13&amp;y=9'><font size='" . $mac_font_size . "' face='Courier'>" . 
 				// "BANNED(1)" . "</font></a>", $macips_result["macip_id"]);
 			// }else{
 				// form_selectable_cell("", $macips_result["macip_id"]);
 			// }
 			
			//form_selectable_cell($macips_result["f_flat"], $macips_result["macip_id"] );
			form_selectable_cell((strlen($_REQUEST["m_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["m_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["f_flat"]) : $macips_result["f_flat"]), $macips_result["macip_id"] );
			
 			//form_selectable_cell(imp_convert_banned_state_2str($macips_result["macip_banned"]), $macips_result["macip_id"]);
 			//form_selectable_cell((strlen($_REQUEST["m_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["m_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_port_view"]) : $macips_result["macip_port_view"]), $macips_result["macip_id"] );
			//form_selectable_cell((strlen($_REQUEST["m_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["m_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_port_view"]) : $macips_result["macip_port_view"]) .  " <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_view.php?action=preview&host_id=" . $macips_result['cid'] . "&snmp_index=" . $macips_result["macip_port_view"] . "&graph_template_id=0&filter=") . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' border='0' alt='' title='View Graph' align='absmiddle'></a>", $macips_result["macip_id"] );
			form_selectable_cell((strlen($_REQUEST["m_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["m_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_port_view"]) : " <a class='linkEditMain' href=impblinding_view.php?report=ports&p_device_id=" . $macips_result['device_id'] . "&p_port_number=" . $macips_result["macip_port_view"]) . ">" . $macips_result["macip_port_view"] . 
				" <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_view.php?action=preview&host_id=" . $macips_result['cid'] . "&snmp_index=" . $macips_result["macip_port_view"] . "&graph_template_id=0&filter=") . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' border='0' alt='' title='View Graph' align='absmiddle'></a>", $macips_result["macip_id"] );
			

 			
 			form_selectable_cell(imp_convert_macip_state_2str($macips_result["macip_imb_status"]), $macips_result["macip_id"] );
 			form_selectable_cell(imp_convert_macip_action_2str($macips_result["macip_imb_action"], $macips_result["type_imb_action"]), $macips_result["macip_id"]  );
 			form_selectable_cell(imp_convert_macip_mode_2str_full($macips_result["macip_mode"], $macips_result["device_id"]), $macips_result["macip_id"]  );
 			
			form_selectable_cell(imp_convert_free_2str($macips_result["macip_may_move"]), $macips_result["macip_id"] );
						
			form_selectable_cell($macips_result["macip_lastchange_date"], $macips_result["macip_id"] );
 			
 			form_selectable_cell((strlen($_REQUEST["m_filter"]) ? $color_line_date . " " .preg_replace("/(" . preg_quote($_REQUEST["m_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>" , $macips_result["macip_scan_date"]) : $color_line_date . " " . $macips_result["macip_scan_date"]), $macips_result["macip_id"] );
 			
 			
			if ($macips_result["macip_online"] == 1) {
 				form_checkbox_cell($macips_result["macip_ipaddr"], $macips_result["macip_id"]);
 			} else {
 				print "<td></td>";
 			}
 			form_end_row();			
         }
 
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No Mac Track Port Results</em></td></tr>";
     }
     html_end_box(false);
 	lm_draw_actions_dropdown($macips_actions, "macips");
 }
 
 
 //******************************************************************************
 
 function impblinding_view_banips() {
     global $title, $report, $colors, $impblinding_search_types, $impblinding_device_types, $rows_selector, $config, $banip_actions, $impblinding_imb_banip_type, $impblinding_imb_yes_no;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("bn_banip_id"));
 	input_validate_input_number(get_request_var_request("bn_ip_filter_type_id"));
 	input_validate_input_number(get_request_var_request("bn_banip_aplled"));
 	input_validate_input_number(get_request_var_request("bn_banip_type"));
 	input_validate_input_number(get_request_var_request("banip_manual"));
 	input_validate_input_number(get_request_var_request("bn_banip_balance"));
 	input_validate_input_number(get_request_var_request("bn_banip_mac_active"));
 	input_validate_input_number(get_request_var_request("bn_banip_aproved"));
 	input_validate_input_number(get_request_var_request("bn_rows_selector"));
     input_validate_input_number(get_request_var_request("page"));
     /* ==================================================== */
 
     /* clean up search string */
     if (isset($_REQUEST["bn_filter"])) {
         $_REQUEST["bn_filter"] = sanitize_search_string(get_request_var("bn_filter"));
     }
     /* clean up search string */
     if (isset($_REQUEST["bn_banip_balance"])) {
         $_REQUEST["bn_banip_balance"] = sanitize_search_string(get_request_var("bn_banip_balance"));
     } 
     /* clean up search string */
     if (isset($_REQUEST["bn_ip_filter"])) {
         $_REQUEST["bn_ip_filter"] = translate_ip_address(sanitize_search_string(get_request_var("bn_ip_filter")));
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
     if (isset($_REQUEST["clear_banip_x"])) {
         kill_session_var("sess_impb_view_banip_current_page");
         kill_session_var("sess_impb_view_banip_filter");
         kill_session_var("sess_impb_view_banip_id");
 		kill_session_var("sess_impb_view_banip_ip_filter_type_id");
         kill_session_var("sess_impb_view_banip_ip_filter");
 		kill_session_var("sess_impb_view_banip_aplled");
 		kill_session_var("sess_impb_view_banip_type");
 		kill_session_var("sess_impb_view_banip_manual");
 		kill_session_var("sess_impb_view_banip_balance");
 		kill_session_var("sess_impb_view_banip_active");
 		kill_session_var("sess_impb_view_banip_aproved");
 		kill_session_var("sess_impb_view_rows_selector");
 		kill_session_var("sess_impb_view_banip_sort_column");
         kill_session_var("sess_impb_view_banip_sort_direction");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["bn_filter"]);
         unset($_REQUEST["bn_banip_id"]);
         unset($_REQUEST["bn_ip_filter_type_id"]);
         unset($_REQUEST["bn_ip_filter"]);
 		unset($_REQUEST["bn_banip_aplled"]);
 		unset($_REQUEST["bn_banip_manual"]);
 		unset($_REQUEST["bn_banip_type"]);
 		unset($_REQUEST["bn_banip_balance"]);
 		unset($_REQUEST["bn_banip_active"]);
 		unset($_REQUEST["bn_banip_aproved"]);
 		unset($_REQUEST["bn_rows_selector"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);		
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("bn_banip_id", "sess_impb_view_banip_id");
         $changed += impblinding_check_changed("bn_ip_filter_type_id", "sess_impb_view_banip_ip_filter_type_id");
         $changed += impblinding_check_changed("bn_ip_filter", "sess_impb_view_banip_ip_filter");
 		$changed += impblinding_check_changed("bn_banip_aplled", "sess_impb_view_banip_aplled");
 		$changed += impblinding_check_changed("bn_banip_manual", "sess_impb_view_banip_manual");
 		$changed += impblinding_check_changed("bn_banip_type", "sess_impb_view_banip_type");
 		$changed += impblinding_check_changed("bn_banip_balance", "sess_impb_view_banip_balance");
 		$changed += impblinding_check_changed("bn_banip_active", "sess_impb_view_banip_active");
 		$changed += impblinding_check_changed("bn_banip_aproved", "sess_impb_view_banip_aproved");
 		$changed += impblinding_check_changed("bn_rows_selector", "sess_impb_view_rows_selector");
 
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["bn_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["bn_page"] = $_REQUEST["page"];
             }
         }
     }
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("bn_page", "sess_impb_view_banip_current_page", "1");
     load_current_session_value("page", "sess_impb_view_banip_current_page", "1");
     load_current_session_value("bn_filter", "sess_impb_view_banip_filter", "");
     load_current_session_value("bn_banip_id", "sess_impb_view_banip_id", "-1");
     load_current_session_value("bn_ip_filter_type_id", "sess_impb_view_banip_ip_filter_type_id", "1");
     load_current_session_value("bn_ip_filter", "sess_impb_view_banip_ip_filter", "");
 	load_current_session_value("bn_banip_aplled", "sess_impb_view_banip_aplled", "-1");
 	load_current_session_value("bn_banip_type", "sess_impb_view_banip_type", "0");
 	load_current_session_value("bn_banip_manual", "sess_impb_view_banip_manual", "-1");
 	load_current_session_value("bn_banip_balance", "sess_impb_view_banip_balance", "");
 	load_current_session_value("bn_banip_active", "sess_impb_view_banip_active", "");
 	load_current_session_value("bn_banip_aproved", "sess_impb_view_banip_aproved", "-1");
 	load_current_session_value("bn_rows_selector", "sess_impb_view_rows_selector", "-1");
     load_current_session_value("sort_column", "sess_impb_view_banip_sort_column", "banip_id");
     load_current_session_value("sort_direction", "sess_impb_view_banip_sort_direction", "ASC");
 	
     impblinding_view_header();
 
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_banip_filter_table.php");
 
     impblinding_view_footer();
 
     $sql_where = "";
 	
     if ($_REQUEST["bn_rows_selector"] == -1) {
         $row_limit = read_config_option("dimpb_num_rows");
     }elseif ($_REQUEST["bn_rows_selector"] == -2) {
         $row_limit = 999999;
     }else{
         $row_limit = $_REQUEST["bn_rows_selector"];
     }
 
     $banips = impblinding_view_get_banip_records($sql_where,TRUE, $row_limit);
 	//$devices_sum = impblinding_view_get_device_records($sql_where);
 	
     $total_rows = db_fetch_cell("SELECT
         COUNT(imb_banip.banip_id)
         FROM imb_banip
         $sql_where");
 
     html_start_box("", "98%", $colors["header"], "3", "center", "");
 
     /* generate page list */
     //$url_page_select = get_page_list($_REQUEST["bn_page"], MAX_DISPLAY_PAGES, read_config_option("dimpb_num_rows"), $total_rows, "impblinding_view.php?report=banips&bn_filter=" . $_REQUEST["bn_filter"] . "&bn_banip_aplled=" . $_REQUEST["bn_banip_aplled"]);
     $url_page_select = get_page_list($_REQUEST["bn_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?bn_ip_filter_type_id=" . $_REQUEST["bn_ip_filter_type_id"] . "&bn_ip_filter=" . $_REQUEST["bn_ip_filter"]);
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='13'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["bn_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?bn_ip_filter_type_id=" . $_REQUEST["bn_ip_filter_type_id"] . "&bn_ip_filter=" . $_REQUEST["bn_ip_filter"] . "&page=" . ($_REQUEST["bn_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["bn_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["bn_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["bn_page"]))) ? $total_rows : ($row_limit*$_REQUEST["bn_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["bn_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?bn_ip_filter_type_id=" . $_REQUEST["bn_ip_filter_type_id"] . "&bn_ip_filter=" . $_REQUEST["bn_ip_filter"] . "&page=" . ($_REQUEST["bn_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["bn_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
     //html_header_checkbox(array("Host<br>Description",  "<br>Status", "<br>Hostname", "Device<br>Type", "Несохраненные<br>операции", "Total<br>Ip-Mac's", "Blocked<br>Ip-Mac's", "User<br>Ports", "Ip-Mac-Ports<br>Enable", "Портов с разре-<br>шенным нуле-<br>вым IP(Vista)", "Last<br>Run date","Last<br>Duration"));
 
 
 	$display_text = array(
 		"banip_id" => array("№", "ASC"),
 		"banip_ipaddr" => array("IP", "ASC"),
 		"banip_aplled" => array("Применен", "ASC"),
 		"banip_aproved" => array("Разрешен", "ASC"),
 		"banip_type" => array("Тип", "ASC"),
 		"banip_manual" => array("Вручную", "ASC"),
 		"banip_balance" => array("Баланс", "ASC"),
 		"banip_install_date" => array("Дата установки","DESC"),
 		"banip_expiration_date" => array("Дата истечения","DESC"),
 		"banip_counts" => array("Количество","DESC"),
 		"banip_delete" => array("К удалению","DESC"),
 		"banip_mac_active_last_poll" => array("Активен","DESC"));
 
 
     html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 	
 	
 	
 	
     $i = 0;
 	$str_count_ipmac_total = 0;
 	$str_blmac_blmac_total = 0;
 	$str_count_ports_total = 0;
 	$str_count_ports_en_total = 0;
 	$str_count_ports_zerr_en_total = 0;	
     if (sizeof($banips) > 0) {
         foreach ($banips as $banip) {
             //form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 			form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $banip["banip_id"]); $i++;
 				
 				form_selectable_cell($banip["banip_id"], $banip["banip_id"] );
 				//form_selectable_cell($banip["banip_ipaddr"], $banip["banip_id"] );
 				form_selectable_cell("<a class='inkEditMain' href='impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=2&i_ip_filter=" . $banip["banip_ipaddr"] . "&i_mac_filter_type_id=1&i_mac_filter=&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=23&y=10'>" . 
 					(strlen($_REQUEST["bn_ip_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["bn_ip_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $banip["banip_ipaddr"]) : $banip["banip_ipaddr"]) . "</a>", $banip["banip_id"]);
 				
 				form_selectable_cell($impblinding_imb_yes_no[$banip["banip_aplled"]], $banip["banip_id"] );
 				form_selectable_cell($impblinding_imb_yes_no[$banip["banip_aproved"]], $banip["banip_id"] );
 				form_selectable_cell(imp_convert_banned_type_2str($banip["banip_type"]), $banip["banip_id"] );
 				form_selectable_cell($impblinding_imb_yes_no[$banip["banip_manual"]], $banip["banip_id"] );
 				form_selectable_cell($banip["banip_balance"], $banip["banip_id"] );
 				form_selectable_cell($banip["banip_install_date"], $banip["banip_id"] );
 				form_selectable_cell($banip["banip_expiration_date"], $banip["banip_id"] );
 				form_selectable_cell($banip["banip_counts"], $banip["banip_id"] );
 				form_selectable_cell($impblinding_imb_yes_no[$banip["banip_delete"]], $banip["banip_id"] );
 				form_selectable_cell($banip["banip_mac_active_last_poll"], $banip["banip_id"] );
 
 				form_checkbox_cell($banip["banip_ipaddr"], $banip["banip_id"]);
 
         }
 
 		
 		
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No D-Link IP-MAC-Port BAN's IP Found</em></td></tr>";
     }
     html_end_box(false);
 	lm_draw_actions_dropdown($banip_actions, "banips");
 }
 
 
 
 
 
 
 function impblinding_view_blmacs() {
     global $title, $report, $colors, $impblinding_search_types, $rows_selector, $config , $blmacs_actions;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("b_device_id"));
     input_validate_input_number(get_request_var_request("b_mac_filter_type_id"));
     input_validate_input_number(get_request_var_request("b_ip_filter_type_id"));
     input_validate_input_number(get_request_var_request("b_rows_selector"));
     input_validate_input_number(get_request_var_request("b_port_filter"));
     input_validate_input_number(get_request_var_request("page"));
     /* ==================================================== */
 
     /* clean up filter string */
     if (isset($_REQUEST["b_filter"])) {
         $_REQUEST["b_filter"] = sanitize_search_string(get_request_var("b_filter"));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["b_mac_filter"])) {
         $_REQUEST["b_mac_filter"] = translate_mac_address(sanitize_search_string(get_request_var("b_mac_filter")));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["b_port_filter"])) {
         $_REQUEST["b_port_filter"] = sanitize_search_string(get_request_var("b_port_filter"));
     }
 
     /* clean up sort_column */
     if (isset($_REQUEST["sort_column"])) {
         $_REQUEST["sort_column"] = sanitize_search_string(get_request_var("sort_column"));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["sort_direction"])) {
         $_REQUEST["sort_direction"] = sanitize_search_string(get_request_var("sort_direction"));
     }
 
     if (isset($_REQUEST["b_mac_filter_type_id"])) {
         if ($_REQUEST["b_mac_filter_type_id"] == 1) {
             unset($_REQUEST["b_mac_filter"]);
         }
     }
 
     /* clean up search string */
     if (isset($_REQUEST["b_ip_filter"])) {
         $_REQUEST["b_ip_filter"] = translate_ip_address(sanitize_search_string(get_request_var("b_ip_filter")));
     }
 
     if (isset($_REQUEST["b_ip_filter_type_id"])) {
         if ($_REQUEST["b_ip_filter_type_id"] == 1) {
             unset($_REQUEST["b_ip_filter"]);
         }
     }
 
     /* if the user pushed the 'clear' button */
     if (isset($_REQUEST["clear_bmacs_x"])) {
         kill_session_var("sess_impb_view_bmacs_current_page");
         kill_session_var("sess_impb_view_bmacs_filter");
         kill_session_var("sess_impb_view_bmacs_mac_filter_type_id");
         kill_session_var("sess_impb_view_bmacs_mac_filter");
         kill_session_var("sess_impb_view_bmacs_ip_filter_type_id");
         kill_session_var("sess_impb_view_bmacs_ip_filter");
         kill_session_var("sess_impb_view_bmacs_port_filter");
         kill_session_var("sess_impb_view_bmacs_rows_selector");
         kill_session_var("sess_impb_view_bmacs_device_id");
         kill_session_var("sess_impb_view_bmacs_sort_column");
         kill_session_var("sess_impb_view_bmacs_sort_direction");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["b_filter"]);
         unset($_REQUEST["b_mac_filter_type_id"]);
         unset($_REQUEST["b_mac_filter"]);
         unset($_REQUEST["b_ip_filter_type_id"]);
         unset($_REQUEST["b_rows_selector"]);
         unset($_REQUEST["b_ip_filter"]);
         unset($_REQUEST["b_port_filter"]);
         unset($_REQUEST["b_device_id"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("b_filter", "sess_impb_view_bmacs_filter");
         $changed += impblinding_check_changed("b_mac_filter_type_id", "sess_impb_view_bmacs_mac_filter_type_id");
         $changed += impblinding_check_changed("b_mac_filter", "sess_impb_view_bmacs_mac_filter");
         $changed += impblinding_check_changed("b_ip_filter_type_id", "sess_impb_view_bmacs_ip_filter_type_id");
         $changed += impblinding_check_changed("b_ip_filter", "sess_impb_view_bmacs_ip_filter");
         $changed += impblinding_check_changed("b_port_filter", "sess_impb_view_bmacs_port_filter");
         $changed += impblinding_check_changed("b_rows_selector", "sess_impb_view_bmacs_rows_selector");
         $changed += impblinding_check_changed("b_device_id", "sess_impb_view_bmacs_device_id");
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["b_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["b_page"] = $_REQUEST["page"];
             }
         }
     }
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("report", "sess_impb_view_report", "blmacs");
     load_current_session_value("page", "sess_impb_view_bmacs_current_page", "1");
     load_current_session_value("b_page", "sess_impb_view_bmacs_current_page", "1");
     load_current_session_value("b_rowstoshow", "sess_impb_view_bmacs_rowstoshow", "2");
     load_current_session_value("b_filter", "sess_impb_view_bmacs_filter", "");
     load_current_session_value("b_mac_filter_type_id", "sess_impb_view_bmacs_mac_filter_type_id", "1");
     load_current_session_value("b_mac_filter", "sess_impb_view_bmacs_mac_filter", "");
     load_current_session_value("b_ip_filter_type_id", "sess_impb_view_bmacs_ip_filter_type_id", "1");
     load_current_session_value("b_ip_filter", "sess_impb_view_bmacs_ip_filter", "");
     load_current_session_value("b_port_filter", "sess_impb_view_bmacs_port_filter", "");
     load_current_session_value("b_rows_selector", "sess_impb_view_bmacs_rows_selector", "-1");
     load_current_session_value("b_device_id", "sess_impb_view_bmacs_device_id", "-1");
     load_current_session_value("sort_column", "sess_impb_view_bmacs_sort_column", "description");
     load_current_session_value("sort_direction", "sess_impb_view_bmacs_sort_direction", "ASC");
 
     /* set m_page variable */
     $_REQUEST["b_page"] = $_REQUEST["page"];
 
     impblinding_view_header();
 
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_bmac_filter_table.php");
 
     impblinding_view_footer();
 
     $sql_where = "";
 
     if ($_REQUEST["b_rows_selector"] == -1) {
         $row_limit = read_config_option("dimpb_num_rows");
     }elseif ($_REQUEST["b_rows_selector"] == -2) {
         $row_limit = 999999;
     }else{
         $row_limit = $_REQUEST["b_rows_selector"];
     }
     $bmacs_results = impblinding_view_get_bmacips_records($sql_where, TRUE, $row_limit);
 
     html_start_box("", "98%", $colors["header"], "3", "center", "");
 
         $rows_query_string = "SELECT
             COUNT(imb_blmacs.device_id)
             FROM imb_blmacs
             $sql_where";
 
      $total_rows = db_fetch_cell($rows_query_string);
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["b_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?b_device_id=" . $_REQUEST["b_device_id"] . "&b_ip_filter_type_id=" . $_REQUEST["b_ip_filter_type_id"] . "&b_ip_filter=" . $_REQUEST["b_ip_filter"] . "&b_mac_filter_type_id=" . $_REQUEST["b_mac_filter_type_id"] . "&b_mac_filter=" . $_REQUEST["b_mac_filter"]);
 
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='13'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["b_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?b_device_id=" . $_REQUEST["b_device_id"] . "&b_ip_filter_type_id=" . $_REQUEST["b_ip_filter_type_id"] . "&b_ip_filter=" . $_REQUEST["b_ip_filter"] . "&b_mac_filter_type_id=" . $_REQUEST["b_mac_filter_type_id"] . "&b_mac_filter=" . $_REQUEST["b_mac_filter"] .  "&page=" . ($_REQUEST["b_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["b_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["b_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["b_page"]))) ? $total_rows : ($row_limit*$_REQUEST["b_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["b_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?b_device_id=" . $_REQUEST["b_device_id"] . "&b_ip_filter_type_id=" . $_REQUEST["b_ip_filter_type_id"] . "&b_ip_filter=" . $_REQUEST["b_ip_filter"] . "&b_mac_filter_type_id=" . $_REQUEST["b_mac_filter_type_id"] . "&b_mac_filter=" . $_REQUEST["b_mac_filter"] . "&page=" . ($_REQUEST["b_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["b_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
 
                $display_text = array(
                "description" => array("Network<br>Device", "ASC"),
                "hostname" => array("Network<br>Hostname", "ASC"),
                "blmac_macaddr" => array("Blocked<br>MAC Address", "ASC"),
 				"blmacinfo_banned" => array("Banned", "ASC"),
 				"blmac_blocked_ip" => array("Blocked<br>IP Address", "ASC"),
				"info" => array("Info", "ASC"),
                "blmac_port" => array("Blocked<br>On port","DESC"),
                "blmac_vid" => array("Blocked<br>Vlan","DESC"),
                "blmacinfo_cor_ip" => array("Correct<br>IP","DESC"),
                "blmacinfo_cor_portlist" => array("Correct<br>Port","DESC"),
 				"blmac_first_scan_date" => array("Время<br>Блока","DESC"),
                 "blmac_scan_date" => array("Last<br>Scan Date", "DESC"));
 
 
         html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 
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
		
            impb_form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $bmacs_result["blmac_id"], ($bmacs_result["blmac_done"] == 0 ? "":"background-color:#AEB4B7")); $i++;
 			form_selectable_cell($bmacs_result["description"], $bmacs_result["blmac_id"] );
 			
 			form_selectable_cell($bmacs_result["hostname"], $bmacs_result["blmac_id"] );
 			
 			form_selectable_cell("<a class='linkEditMain' href='impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=1&i_ip_filter=&i_mac_filter_type_id=2&i_mac_filter=" . $bmacs_result["blmac_macaddr"] . "&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=14&y=6'><font size='" . $mac_font_size . "' face='Courier'>" . 
 				(strlen($_REQUEST["b_mac_filter"]) ? strtoupper(preg_replace("/(" . preg_quote($_REQUEST["b_mac_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_macaddr"])) : $bmacs_result["blmac_macaddr"] ). "</font></a>", $bmacs_result["blmac_id"]);
 
 			form_selectable_cell(imp_convert_banned_state_2str($bmacs_result["blmacinfo_banned"]), $bmacs_result["blmac_id"] );
 			
 			form_selectable_cell("<img src='" . $config['url_path'] . "plugins/impblinding/images/" . $bmacs_result["sig"] . ".png' TITLE='" . $bmacs_result["sig2"] . "' align='absmiddle'><a class='linkEditMain' TITLE='" . $bmacs_result["sig2"] . ' Адр:' . $bmacs_result["f_addr"] . "' href='impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=2&i_ip_filter=" . $bmacs_result["blmac_blocked_ip"] . "&i_mac_filter_type_id=1&i_mac_filter=&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=14&y=6'><font size='" . $mac_font_size . "' face='Courier'>" .
 				(strlen($_REQUEST["b_mac_filter"]) ? strtoupper(preg_replace("/(" . preg_quote($_REQUEST["b_ip_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_blocked_ip"])) : $bmacs_result["blmac_blocked_ip"]) , $bmacs_result["blmac_id"]);
 			
			form_selectable_cell($str_info, $bmacs_result["blmac_id"] );
			
 			//port (port_name)
			form_selectable_cell((strlen($_REQUEST["b_port_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["b_port_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_port"]) : $bmacs_result["blmac_port"]) . " (" . $bmacs_result["port_name"] . ")" . " <a class='linkEditMain' href='". htmlspecialchars($config['url_path'] . "graph_view.php?action=preview&host_id=" . $bmacs_result['cid'] . "&snmp_index=" . $bmacs_result['blmac_port'] . "&graph_template_id=0&filter=") . "'><img src='" . $config['url_path'] . "plugins/thold/images/view_graphs.gif' border='0' alt='' title='View Graph' align='absmiddle'></a>", $bmacs_result["blmac_id"] );
 			
			
			
			
 			//form_selectable_cell(imp_convert_blmac_state_2str($bmacs_result["blmac_type"]), $bmacs_result["blmac_id"] );
 			//form_selectable_cell($bmacs_result["port_name"], $bmacs_result["blmac_id"] );
 			//vlan (vlan_name)
			form_selectable_cell($bmacs_result["blmac_vid"] . " (" . $bmacs_result["blmac_vlanname"] . ")", $bmacs_result["blmac_id"] );
 			//form_selectable_cell($bmacs_result["blmac_vlanname"], $bmacs_result["blmac_id"] );
			//dobavit
 			form_selectable_cell("<img src='" . $config['url_path'] . "plugins/impblinding/images/" . $bmacs_result["sig1"] . ".png' align='absmiddle'><a class='linkEditMain' TITLE='" . 'Адр:' . $bmacs_result["f_addr1"] . "' href='impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=2&i_ip_filter=" . $bmacs_result["blmacinfo_cor_ip"] . "&i_mac_filter_type_id=1&i_mac_filter=&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=14&y=6'><font size='" . $mac_font_size . "' face='Courier'>" .
 				(strlen($_REQUEST["b_mac_filter"]) ? strtoupper(preg_replace("/(" . preg_quote($_REQUEST["b_ip_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmacinfo_cor_ip"])) : $bmacs_result["blmacinfo_cor_ip"]) . "</font></a>", $bmacs_result["blmac_id"]);
 			form_selectable_cell($bmacs_result["blmacinfo_cor_portlist"], $bmacs_result["blmac_id"] );
 			form_selectable_cell(date('H:i:s',strtotime($bmacs_result["blmac_first_scan_date"])) . " ( " .  DateTimeDiff($bmacs_result["blmac_first_scan_date"]) . ")", $bmacs_result["blmac_id"] );
 			
 			form_selectable_cell((strlen($_REQUEST["b_port_filter"]) ? $color_line_date . " " .preg_replace("/(" . preg_quote($_REQUEST["b_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>",  $bmacs_result["blmac_scan_date"])  : $color_line_date . " " . $bmacs_result["blmac_scan_date"]), $bmacs_result["blmac_id"] );			

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
 	lm_draw_actions_dropdown($blmacs_actions, "blmacs");
 }
 
 
 function impblinding_view_recent_macips() {
     global $title, $report, $colors, $impblinding_search_types, $rows_selector, $recentmacs_actions, $config, $impblinding_search_recent_date;
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("r_site_id"));
     input_validate_input_number(get_request_var_request("r_device_id"));
     input_validate_input_number(get_request_var_request("r_mac_filter_type_id"));
     input_validate_input_number(get_request_var_request("r_ip_filter_type_id"));
 	input_validate_input_number(get_request_var_request("r_port_filter_type_id"));
     input_validate_input_number(get_request_var_request("r_rows_selector"));
 	input_validate_input_number(get_request_var_request("r_date_id"));
     input_validate_input_number(get_request_var_request("page"));
     /* ==================================================== */
 
     /* clean up filter string */
     if (isset($_REQUEST["r_filter"])) {
         $_REQUEST["r_filter"] = sanitize_search_string(get_request_var("r_filter"));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["r_mac_filter"])) {
         $_REQUEST["r_mac_filter"] = translate_mac_address(sanitize_search_string(get_request_var("r_mac_filter")));
     }
 
     if (isset($_REQUEST["r_mac_filter_type_id"])) {
         if ($_REQUEST["r_mac_filter_type_id"] == 1) {
             kill_session_var("sess_impb_view_recent_macips_mac_filter");
             unset($_REQUEST["r_mac_filter"]);
         }
     }
 
     /* clean up search string */
     if (isset($_REQUEST["r_ip_filter"])) {
         $_REQUEST["r_ip_filter"] = translate_ip_address(sanitize_search_string(get_request_var("r_ip_filter")));
     }
     if (isset($_REQUEST["r_ip_filter_type_id"])) {
         if ($_REQUEST["r_ip_filter_type_id"] == 1) {
             unset($_REQUEST["r_ip_filter"]);
         }
     }
     if (isset($_REQUEST["r_port_filter"])) {
         $_REQUEST["r_port_filter"] = sanitize_search_string(get_request_var("r_port_filter"));
 	}
 
 	    if (isset($_REQUEST["r_port_filter_type_id"])) {
         if ($_REQUEST["r_port_filter_type_id"] == -1) {
             unset($_REQUEST["r_port_filter"]);
         }
     }
 
     /* clean up sort_column */
     if (isset($_REQUEST["r_sort_column"])) {
         $_REQUEST["r_sort_column"] = sanitize_search_string(get_request_var("r_sort_column"));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["r_sort_direction"])) {
         $_REQUEST["r_sort_direction"] = sanitize_search_string(get_request_var("r_sort_direction"));
     }
     /* if the user pushed the 'clear' button */
     if (isset($_REQUEST["clear_recent_macips_x"])) {
         kill_session_var("sess_impb_view_recent_macips_current_page");
         kill_session_var("sess_impb_view_recent_macips_filter");
         kill_session_var("sess_impb_view_recent_macips_mac_filter_type_id");
         kill_session_var("sess_impb_view_recent_macips_mac_filter");
         kill_session_var("sess_impb_view_recent_macips_ip_filter_type_id");
         kill_session_var("sess_impb_view_recent_macips_ip_filter");
         kill_session_var("sess_impb_view_recent_macips_port_filter_type_id");
         kill_session_var("sess_impb_view_recent_macips_port_filter");
         kill_session_var("sess_impb_view_recent_macips_rows_selector");
 		kill_session_var("sess_impb_view_recent_macips_r_date_id");
         kill_session_var("sess_impb_view_recent_macips_site_id");
         kill_session_var("sess_impb_view_recent_macips_device_id");
 		kill_session_var("sess_impb_view_recent_macips_sort_column");
         kill_session_var("sess_impb_view_recent_macips_sort_direction");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["r_filter"]);
         unset($_REQUEST["r_mac_filter_type_id"]);
         unset($_REQUEST["r_mac_filter"]);
         unset($_REQUEST["r_ip_filter_type_id"]);
         unset($_REQUEST["r_ip_filter"]);
         unset($_REQUEST["r_port_filter_type_id"]);
         unset($_REQUEST["r_port_filter"]);
         unset($_REQUEST["r_rows_selector"]);
 		unset($_REQUEST["r_date_id"]);
         unset($_REQUEST["r_site_id"]);
         unset($_REQUEST["r_device_id"]);
 		unset($_REQUEST["r_sort_column"]);
         unset($_REQUEST["r_sort_direction"]);
 
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("r_filter", "sess_impb_view_recent_macips_filter");
         $changed += impblinding_check_changed("r_mac_filter_type_id", "sess_impb_view_recent_macips_mac_filter_type_id");
         $changed += impblinding_check_changed("r_mac_filter", "sess_impb_view_recent_macips_mac_filter");
         $changed += impblinding_check_changed("r_ip_filter_type_id", "sess_impb_view_recent_macips_ip_filter_type_id");
         $changed += impblinding_check_changed("r_ip_filter", "sess_impb_view_recent_macips_ip_filter");
         $changed += impblinding_check_changed("r_port_filter_type_id", "sess_impb_view_recent_macips_port_filter_type_id");
         $changed += impblinding_check_changed("r_port_filter", "sess_impb_view_recent_macips_port_filter");
         $changed += impblinding_check_changed("r_rows_selector", "sess_impb_view_recent_macips_rows_selector");
 		$changed += impblinding_check_changed("r_date_id", "sess_impb_view_recent_macips_r_date_id");
         $changed += impblinding_check_changed("r_site_id", "sess_impb_view_recent_macips_site_id");
         $changed += impblinding_check_changed("r_device_id", "sess_impb_view_recent_macips_device_id");
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["r_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["r_page"] = $_REQUEST["page"];
             }
         }
     }
 
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("report", "sess_impb_view_report", "recent_macips");
     load_current_session_value("page", "sess_impb_view_recent_macips_current_page", "1");
     load_current_session_value("r_page", "sess_impb_view_recent_macips_current_page", "1");
     load_current_session_value("r_filter", "sess_impb_view_recent_macips_filter", "");
     load_current_session_value("r_mac_filter_type_id", "sess_impb_view_recent_macips_mac_filter_type_id", "1");
     load_current_session_value("r_mac_filter", "sess_impb_view_recent_macips_mac_filter", "");
     load_current_session_value("r_ip_filter_type_id", "sess_impb_view_recent_macips_ip_filter_type_id", "8");
     load_current_session_value("r_ip_filter", "sess_impb_view_recent_macips_ip_filter", "");
     load_current_session_value("r_port_filter_type_id", "sess_impb_view_recent_macips_port_filter_type_id", "8");
     load_current_session_value("r_port_filter", "sess_impb_view_recent_macips_port_filter", "");
     load_current_session_value("r_rows_selector", "sess_impb_view_recent_macips_rows_selector", "-1");
 	load_current_session_value("r_date_id", "sess_impb_view_recent_macips_r_date_id", "-1");
     load_current_session_value("r_site_id", "sess_impb_view_recent_macips_site_id", "-1");
     load_current_session_value("r_device_id", "sess_impb_view_recent_macips_device_id", "-1");
     load_current_session_value("sort_column", "sess_impb_view_recent_macips_sort_column", "description");
     load_current_session_value("sort_direction", "sess_impb_view_recent_macips_sort_direction", "ASC");	
 
     /* set m_page variable */
     $_REQUEST["m_page"] = $_REQUEST["page"];
 
     impblinding_view_header();
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_recentmac_filter_table.php");
     impblinding_view_footer();
 
 
     $sql_where = "";
 
     if ($_REQUEST["r_rows_selector"] == -1) {
         $row_limit = read_config_option("dimpb_num_rows");
     }elseif ($_REQUEST["r_rows_selector"] == -2) {
         $row_limit = 999999;
     }else{
         $row_limit = $_REQUEST["r_rows_selector"];
     }
     $port_results = impblinding_view_get_recent_macips_records($sql_where, TRUE, $row_limit);
     
     html_start_box("", "98%", $colors["header"], "3", "center", "");
 
         $rows_query_string = "SELECT
             COUNT(DISTINCT device_id, mac_address, port_number, ip_address)
             FROM mac_track_ports
             $sql_where";
 
             $rows_query_string = str_replace("mac_track_ports", "imb_mactrack_recent_ports", $rows_query_string);
 
         if (strlen($sql_where) == 0) {
             $total_rows = 0;
         }else{
             $total_rows = db_fetch_cell($rows_query_string);
         }
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["r_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?r_device_id=" . $_REQUEST["r_device_id"] . "&r_ip_filter_type_id=" . $_REQUEST["r_ip_filter_type_id"] . "&r_ip_filter=" . $_REQUEST["r_ip_filter"] . "&r_mac_filter_type_id=" . $_REQUEST["r_mac_filter_type_id"] . "&r_mac_filter=" . $_REQUEST["r_mac_filter"] . "&r_site_id=" . $_REQUEST["r_site_id"]);
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='12'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["r_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?r_device_id=" . $_REQUEST["r_device_id"] . "&r_ip_filter_type_id=" . $_REQUEST["r_ip_filter_type_id"] . "&r_ip_filter=" . $_REQUEST["r_ip_filter"] . "&r_mac_filter_type_id=" . $_REQUEST["r_mac_filter_type_id"] . "&r_mac_filter=" . $_REQUEST["r_mac_filter"] . "&r_site_id=" . $_REQUEST["r_site_id"] . "&page=" . ($_REQUEST["r_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["r_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["r_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["r_page"]))) ? $total_rows : ($row_limit*$_REQUEST["r_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["m_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?r_device_id=" . $_REQUEST["r_device_id"] . "&r_ip_filter_type_id=" . $_REQUEST["r_ip_filter_type_id"] . "&r_ip_filter=" . $_REQUEST["r_ip_filter"] . "&r_mac_filter_type_id=" . $_REQUEST["r_mac_filter_type_id"] . "&r_mac_filter=" . $_REQUEST["r_mac_filter"] . "&r_site_id=" . $_REQUEST["r_site_id"] . "&page=" . ($_REQUEST["r_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["r_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
 
 				
 
 			$display_text = array(
                 //"site_id" => array("САЙТ", "ASC"),
 				"description" => array("Описание<br>Устройства", "ASC"),
                 "hostname" => array("IP<br>(Hostname)", "ASC"),
                 "ip_address" => array("IP Адресс", "ASC"),
                 "mac_address" => array("MAC Адресс","DESC"),
 				"vendor_name" => array("Vendor","DESC"),
                 "port_number" => array("Номер<br>Порта","DESC"),
                 "port_name" => array("Имя<br>Порта","DESC"),
                 "vlan_id" => array("VLAN<br>ID","DESC"),
                 "vlan_name" => array("VLAN<br>Name","DESC"),
 				"date_last" => array("Время<br>последнего<br>сканирования","DESC"),
                 "count_rec" => array("Количество<br>сканирований", "DESC"));
 
 	
   
 	html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 	
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
   
             
             form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' .$port_result["row_id"]); $i++;
 			//form_selectable_cell("<a class='linkEditMain' href='impblinding_devices.php?action=edit&device_id=" . $port["device_id"] . "'>" . 
 			//	(strlen($_REQUEST["p_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["p_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port["description"]) : $port["description"]) . "</strong></a>", $port["port_id"]);			
 			form_selectable_cell($port_result["description"], $port_result["row_id"] );			
 			form_selectable_cell($port_result["hostname"], $port_result["row_id"] );	
 			form_selectable_cell((strlen($_REQUEST["r_ip_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["r_ip_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["ip_address"]) : $port_result["ip_address"]),$port_result["row_id"]);			
 			if (strlen(read_config_option("mt_reverse_dns")) > 0) {
 				form_selectable_cell((strlen($_REQUEST["r_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["r_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["dns_hostname"]) : $port_result["dns_hostname"]),$port_result["row_id"]);			
 			}
 			form_selectable_cell(strtoupper(strlen($_REQUEST["r_mac_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["r_mac_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["mac_address"]) : $port_result["mac_address"]),$port_result["row_id"]);			
 			form_selectable_cell($port_result["vendor_name"], $port_result["row_id"] );	
 			form_selectable_cell($port_result["port_number"], $port_result["row_id"] );		
 			form_selectable_cell((strlen($_REQUEST["r_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["r_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["port_name"]) : $port_result["port_name"]),$port_result["row_id"]);						
 			
 			form_selectable_cell($port_result["vlan_id"], $port_result["row_id"] );			
 			form_selectable_cell((strlen($_REQUEST["r_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["r_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["vlan_name"]) : $port_result["vlan_name"]),$port_result["row_id"]);						
 			
 			form_selectable_cell($color_line_date . " " . $scan_date, $port_result["row_id"] );			
 			form_selectable_cell($port_result["count_rec"], $port_result["row_id"] );	
 			form_checkbox_cell($port_result["description"], $port_result["row_id"]);
 			
         }
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No Mac Track Port Results</em></td></tr>";
     }
 
     html_end_box(false); 
     /* draw the dropdown containing a list of available actions for this form */
 	lm_draw_actions_dropdown($recentmacs_actions, "recentmacs");
 
     
 }
 
 function impblinding_view_info() {
     global $title, $report, $colors, $impblinding_search_types, $impblinding_port_search_types, $rows_selector, $config, $macips_actions;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("i_device_id"));
     input_validate_input_number(get_request_var_request("i_mac_filter_type_id"));
     input_validate_input_number(get_request_var_request("i_ip_filter_type_id"));
 	input_validate_input_number(get_request_var_request("i_port_filter_type_id"));
     input_validate_input_number(get_request_var_request("i_rows_selector"));
     input_validate_input_number(get_request_var_request("page"));
     /* ==================================================== */
 
     /* clean up report string */
     if (isset($_REQUEST["report"])) {
         $_REQUEST["report"] = sanitize_search_string(get_request_var("report"));
     }
 
     /* clean up filter string */
     if (isset($_REQUEST["i_filter"])) {
         $_REQUEST["i_filter"] = sanitize_search_string(get_request_var("i_filter"));
     }
 
     /* clean up search string */
     if (isset($_REQUEST["i_ip_filter"])) {
         $_REQUEST["i_ip_filter"] = translate_ip_address(sanitize_search_string(get_request_var("i_ip_filter")));
 	}
     
 	/* clean up search string */
     if (isset($_REQUEST["i_mac_filter"])) {
         $_REQUEST["i_mac_filter"] = translate_mac_address(sanitize_search_string(get_request_var("i_mac_filter")));
     }
 
 	/* clean up search string */
     if (isset($_REQUEST["i_port_filter"])) {
         $_REQUEST["i_port_filter"] = sanitize_search_string(get_request_var("i_port_filter"));
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
     if (isset($_REQUEST["clear_info_x"])) {
         kill_session_var("sess_impb_view_info_current_page");
         kill_session_var("sess_impb_view_info_filter");
         kill_session_var("sess_impb_view_info_mac_filter_type_id");
         kill_session_var("sess_impb_view_info_mac_filter");
         kill_session_var("sess_impb_view_info_ip_filter_type_id");
         kill_session_var("sess_impb_view_info_ip_filter");
         kill_session_var("sess_impb_view_info_port_filter_type_id");
         kill_session_var("sess_impb_view_info_port_filter");
         kill_session_var("sess_impb_view_info_rows_selector");
         kill_session_var("sess_impb_view_info_device_id");
         kill_session_var("sess_impb_view_info_sort_column");
         kill_session_var("sess_impb_view_info_sort_direction");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["i_filter"]);
         unset($_REQUEST["i_mac_filter_type_id"]);
         unset($_REQUEST["i_mac_filter"]);
         unset($_REQUEST["i_ip_filter_type_id"]);
         unset($_REQUEST["i_rows_selector"]);
         unset($_REQUEST["i_ip_filter"]);
         unset($_REQUEST["i_port_filter_type_id"]);
         unset($_REQUEST["i_port_filter"]);
         unset($_REQUEST["i_device_id"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("i_filter", "sess_impb_view_info_filter");
         $changed += impblinding_check_changed("i_mac_filter_type_id", "sess_impb_view_info_filter_type_id");
         $changed += impblinding_check_changed("i_mac_filter", "sess_impb_view_info_mac_filter");
         $changed += impblinding_check_changed("i_ip_filter_type_id", "sess_impb_view_info_ip_filter_type_id");
         $changed += impblinding_check_changed("i_ip_filter", "sess_impb_view_info_ip_filter");
         $changed += impblinding_check_changed("i_port_filter_type_id", "sess_impb_view_info_port_filter_type_id");
         $changed += impblinding_check_changed("i_port_filter", "sess_impb_view_info_port_filter");
         $changed += impblinding_check_changed("i_rows_selector", "sess_impb_view_info_rows_selector");
         $changed += impblinding_check_changed("i_device_id", "sess_impb_view_info_device_id");
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["i_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["i_page"] = $_REQUEST["page"];
             }
         }
     }
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("report", "sess_impb_view_report", "info");
     load_current_session_value("page", "sess_impb_view_info_current_page", "1");
     load_current_session_value("i_page", "sess_impb_view_info_current_page", "1");
     load_current_session_value("i_rowstoshow", "sess_impb_view_info_rowstoshow", "2");
     load_current_session_value("i_filter", "sess_impb_view_info_filter", "");
     load_current_session_value("i_mac_filter_type_id", "sess_impb_view_info_mac_filter_type_id", "1");
     load_current_session_value("i_mac_filter", "sess_impb_view_info_mac_filter", "");
     load_current_session_value("i_ip_filter_type_id", "sess_impb_view_info_ip_filter_type_id", "1");
     load_current_session_value("i_ip_filter", "sess_impb_view_info_ip_filter", "");
     load_current_session_value("i_port_filter_type_id", "sess_impb_view_info_port_filter_type_id", "1");
     load_current_session_value("i_port_filter", "sess_impb_view_info_port_filter", "");
     load_current_session_value("i_rows_selector", "sess_impb_view_info_rows_selector", "-1");
     load_current_session_value("i_device_id", "sess_impb_view_info_device_id", "-1");
     load_current_session_value("sort_column", "sess_impb_view_info_sort_column", "description");
     load_current_session_value("sort_direction", "sess_impb_view_info_sort_direction", "ASC");
 
     /* set i_page variable */
     $_REQUEST["i_page"] = $_REQUEST["page"];
 
     impblinding_view_header();
 
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_info_filter_table.php");
 
     impblinding_view_footer();
 
     $sql_where = "";
 
     if ($_REQUEST["i_rows_selector"] == -1) {
         $row_limit = read_config_option("dimpb_num_rows");
     }elseif ($_REQUEST["i_rows_selector"] == -2) {
         $row_limit = 999999;
     }else{
         $row_limit = $_REQUEST["i_rows_selector"];
     }
 	
 	$mac_font_size=read_config_option("dimpb_mac_addr_font_size");
 	
    $macips_results = impblinding_view_get_info_macips_records($sql_where, TRUE, $row_limit);
	
	if (sizeof($macips_results) == 1) {
		$ip_full_info = db_fetch_assoc("SELECT login, blocked,balance, ag_num, f_addr, f_flat, equipm, mobile, l.vg_id, macip_ipaddr FROM imb_macip i " .
				" LEFT JOIN lb_staff l ON (l.ip=i.macip_ipaddr ) " .
				" LEFT JOIN lb_vgroups_s lv ON (lv.vg_id=l.vg_id) " .
				" WHERE macip_id="  . $macips_results[0]["macip_id"] . ";");
			if (sizeof($ip_full_info) == 1) {
				html_start_box("Информация по IP", "98%", $colors["header"], "1", "center", "");
				?>
				<tr><td><?php print ("VG = " . $ip_full_info[0]["vg_id"] . "\n");?></td></tr>
				<tr><td><?php print ("IP = " . $ip_full_info[0]["macip_ipaddr"] . "\n");?></td></tr>
				<tr><td><?php print ("Login = " . $ip_full_info[0]["login"] . "\n");?></td></tr>
				<tr><td><?php print ("Status = " . $ip_full_info[0]["blocked"] . "\n");?></td></tr>
				<tr><td><?php print ("AG = " . $ip_full_info[0]["ag_num"] . "\n");?></td></tr>
				<tr><td><?php print ("Balance = " . $ip_full_info[0]["balance"] );?></td></tr>
				<tr><td><?php print ("Addr = " . $ip_full_info[0]["f_addr"] . "\n");?></td></tr>
				<tr><td><?php print ("Equipm = " . $ip_full_info[0]["equipm"] . "\n");?></td></tr>
				<tr><td><?php print ("mobile = " . $ip_full_info[0]["mobile"] . "\n");?></td></tr>
				<?php

		
				html_end_box();
			}
	}
 
 // ---------------------------------- MacIP INFO
 	
     html_start_box("Поиск по записям Ip-Mac-Port", "98%", $colors["header"], "3", "center", "");
 
         $rows_query_string = "SELECT
             COUNT(imb_macip.device_id)
             FROM imb_macip
             $sql_where";
 
      $total_rows = db_fetch_cell($rows_query_string);
 
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["i_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"]);
 
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='11'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["i_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"] .  "&page=" . ($_REQUEST["i_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["i_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["i_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["i_page"]))) ? $total_rows : ($row_limit*$_REQUEST["i_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["i_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"] . "&page=" . ($_REQUEST["i_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["i_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
                $display_text = array(
                 "macip_id" => array("ID", "ASC"),
 				"description" => array("Network<br>Device", "ASC"),
                 "hostname" => array("Network<br>Hostname", "ASC"),
                 "macip_ipaddr" => array("End Device<br>IP Address", "ASC"),
                 "macip_macaddr" => array("End Device<br>MAC Address", "ASC"),
 				"macip_banned" => array("BANNED", "ASC"),
                 "macip_port_view" => array("Port<br>List", "DESC"),
                 "macip_imb_status" => array("Ip-Mac-Port<br>Record state", "ASC"),
                 "macip_mode" => array("Ip-Mac-Port<br>Record Mode", "ASC"),
 				"macip_lastchange_date" => array("Дата<br>Изменения", "ASC"),
                 "scan_date" => array("Last<br>Scan Date", "DESC"));
         html_header_sort($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 
     $i = 0;
     if (sizeof($macips_results) > 0) {
         foreach ($macips_results as $macips_result) {
             $scan_date = $macips_result["macip_scan_date"];
 
             form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
             ?>
             <td >
 				<a class="linkEditMain" href="impblinding_view.php?report=macs&m_device_id=%20<?php print $macips_result["device_id"];?>&m_rows_selector=-1&m_mac_filter_type_id=1&m_mac_filter=&m_filter=&m_ip_filter_type_id=2&m_ip_filter=<?php print $macips_result["macip_ipaddr"];?>&m_port_filter_type_id=1&m_port_filter="><font face="Courier"><?php print  $macips_result["macip_id"];?></font></a>
             </td >			
             <td><?php print $macips_result["description"];?></td>
             <td><?php print $macips_result["hostname"];?></td>
            <td >
                 <a class="linkEditMain" href="impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=2&i_ip_filter=<?php print $macips_result["macip_ipaddr"];?>&i_mac_filter_type_id=1&i_mac_filter=&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=23&y=10"><?php print preg_replace("/(" . preg_quote($_REQUEST["i_ip_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_ipaddr"]);?></a>
             </td >            
 			<td >
 				<a class="linkEditMain" href="impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=8&i_ip_filter=&i_mac_filter_type_id=2&i_mac_filter=<?php print $macips_result["macip_macaddr"];?>&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=14&y=6"><font size="<?php print $mac_font_size; ?>" face="Courier"><?php print strtoupper(preg_replace("/(" . preg_quote($_REQUEST["i_mac_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_macaddr"]));?></font></a>
 			</td >	
 			<td><?php print imp_convert_banned_state_2str($macips_result["macip_banned"]);?></td>
             <td><?php print preg_replace("/(" . preg_quote($_REQUEST["i_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_port_view"]);?></td>
             <td><?php print imp_convert_macip_state_2str($macips_result["macip_imb_status"]);?></td>
 			<td><?php print $macips_result["macip_mode"];?></td>
       <td><?php print $macips_result["macip_lastchange_date"];?></td>
             <td><?php print preg_replace("/(" . preg_quote($_REQUEST["i_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_scan_date"]);?></td>
 			</tr>
             <?php
         }
 
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No Mac Track Port Results</em></td></tr>";
     }
     html_end_box(false);
 // ---------------------------------- BlockMac INFO
 print "<br><br>";
 
     $sql_where="";
     $bmacs_results = impblinding_view_get_info_bmacips_records($sql_where, TRUE, $row_limit);
     html_start_box("Поиск по записям заблокированных МАС", "98%", $colors["header"], "3", "center", "");
 
         $rows_query_string = "SELECT
             COUNT(imb_blmacs.device_id)
             FROM imb_blmacs
             $sql_where";
 
      $total_rows = db_fetch_cell($rows_query_string);
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["i_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"]);
 
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='12'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["i_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"] .  "&page=" . ($_REQUEST["i_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["i_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["i_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["i_page"]))) ? $total_rows : ($row_limit*$_REQUEST["i_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["i_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"] . "&page=" . ($_REQUEST["i_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["i_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
 
                $display_text = array(
                 "blmac_id" => array("ID", "ASC"),
 				"description" => array("Network<br>Device", "ASC"),
                 "hostname" => array("Network<br>Hostname", "ASC"),
                 "blmac_macaddr" => array("Blocked<br>MAC Address", "ASC"),
 				"blmacinfo_banned" => array("BANNED", "ASC"),
                 "blmac_port" => array("Blocked<br>On port","DESC"),
                 "blmac_type" => array("Block<br>Type","DESC"),
                 "blmac_vid" => array("Blocked<br>Vlan ID","DESC"),
                 "blmacinfo_cor_ip" => array("Correct<br>IP","DESC"),
                 "blmacinfo_cor_portlist" => array("Correct<br>Port","DESC"),
 				"blmac_first_scan_date" => array("Время<br>Блока","DESC"),
                 "blmac_scan_date" => array("Last<br>Scan Date", "DESC"));
 
 
         html_header_sort($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 
     $i = 0;
     if (sizeof($bmacs_results) > 0) {
         foreach ($bmacs_results as $bmacs_result) {
 
             form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
             ?>
             <td >
                 <a class="linkEditMain" href="impblinding_view.php?report=blmacs&b_device_id=%20<?php print $bmacs_result["device_id"];?>&b_rows_selector=-1&b_mac_filter_type_id=1&b_mac_filter=&b_filter=&b_ip_filter_type_id=1&b_ip_filter="><font face="Courier"><?php print  $bmacs_result["blmac_id"];?></font></a>
             </td >
 			<td><?php print $bmacs_result["description"];?></td>
             <td><?php print $bmacs_result["hostname"];?></td>
             <td >
                 <a class="linkEditMain" href="impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=8&i_ip_filter=&i_mac_filter_type_id=2&i_mac_filter=<?php print $bmacs_result["blmac_macaddr"];?>&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=14&y=6"><font size="<?php print $mac_font_size; ?>" face="Courier"><?php print strtoupper(preg_replace("/(" . preg_quote($_REQUEST["i_mac_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_macaddr"]));?></font></a>
             </td >	
 			<td><?php print imp_convert_banned_state_2str($bmacs_result["blmacinfo_banned"]);?></td>
             <td><?php print preg_replace("/(" . preg_quote($_REQUEST["i_port_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_port"]);?></td>
             <td><?php print imp_convert_blmac_state_2str($bmacs_result["blmac_type"]);?></td>
             <td><?php print $bmacs_result["blmac_vid"];?></td> 
             <td>
 				<a class="linkEditMain" href="impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=2&i_ip_filter=<?php print $bmacs_result["blmacinfo_cor_ip"];?>&i_mac_filter_type_id=1&i_mac_filter=&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=14&y=6"><font size="<?php print $mac_font_size; ?>" face="Courier"><?php print strtoupper(preg_replace("/(" . preg_quote($_REQUEST["i_ip_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmacinfo_cor_ip"]));?></font></a>
 			</td> 			
 			<td><?php print $bmacs_result["blmacinfo_cor_portlist"];?></td>
 			<td><?php print date('H:i:s',strtotime($bmacs_result["blmac_first_scan_date"])) . " ( " .  DateTimeDiff($bmacs_result["blmac_first_scan_date"]) . ")";?></td>
             <td><?php print preg_replace("/(" . preg_quote($_REQUEST["i_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_scan_date"]);?></td>
             </tr>
             <?php
         }
 
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No D-Link IP-Mac-Port Binding  Blocked MAC  Results</em></td></tr>";
     }
 	html_end_box(false);
 
 // ---------------------------------- Mac Track INFO
 print "<br><br>";
 
     $sql_where="";
    $port_results = impblinding_view_get_info_recent_macips_records($sql_where, TRUE, $row_limit);
     
     html_start_box("Поиск по записям Mac Track plugin", "98%", $colors["header"], "3", "center", "");
 
         $rows_query_string = "SELECT
             COUNT(DISTINCT device_id, mac_address, port_number, ip_address)
             FROM mac_track_ports
             $sql_where";
 
             $rows_query_string = str_replace("mac_track_ports", "imb_mactrack_recent_ports", $rows_query_string);
 
         if (strlen($sql_where) == 0) {
             $total_rows = 0;
         }else{
             $total_rows = db_fetch_cell($rows_query_string);
         }
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["i_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"]);
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='11'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["i_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"] . "&page=" . ($_REQUEST["i_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["i_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["i_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["i_page"]))) ? $total_rows : ($row_limit*$_REQUEST["i_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["i_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?i_device_id=" . $_REQUEST["i_device_id"] . "&i_ip_filter_type_id=" . $_REQUEST["i_ip_filter_type_id"] . "&i_ip_filter=" . $_REQUEST["i_ip_filter"] . "&i_mac_filter_type_id=" . $_REQUEST["i_mac_filter_type_id"] . "&i_mac_filter=" . $_REQUEST["i_mac_filter"] . "&page=" . ($_REQUEST["i_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["i_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
 
 				
 
 			$display_text = array(
                 //"site_id" => array("САЙТ", "ASC"),
 				"description" => array("Описание<br>Устройства", "ASC"),
                 "hostname" => array("IP<br>(Hostname)", "ASC"),
                 "ip_address" => array("IP Адресс", "ASC"),
                 "mac_address" => array("MAC Адресс","DESC"),
 				"vendor_name" => array("Vendor Name","DESC"),
                 "port_number" => array("Номер<br>Порта","DESC"),
                 "port_name" => array("Имя<br>Порта","DESC"),
                 "vlan_id" => array("VLAN<br>ID","DESC"),
                 "vlan_name" => array("VLAN<br>Name","DESC"),
 				"date_last" => array("Время<br>последнего<br>сканирования","DESC"),
                 "count_rec" => array("Количество<br>сканирований", "DESC"));
 
 	
   
 	html_header_sort($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 	
     $i = 0;
     if (sizeof($port_results) > 0) {
         foreach ($port_results as $port_result) {
             $scan_date = $port_result["max_scan_date"];
 
   if ($port_result["active_last"] == 1)  {
   $color_line_date="<span style='font-weight: bold;'>";
   }else{
   $color_line_date="";
   }
   
             form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
             ?>
             <td><?php print $port_result["description"];?></td>
             <td><?php print $port_result["hostname"];?></td>
             <td >
                 <a class="linkEditMain" href="impblinding_view.php?report=info&i_device_id=-1&i_ip_filter_type_id=2&i_ip_filter=<?php print $port_result["ip_address"];?>&i_mac_filter_type_id=1&i_mac_filter=&i_port_filter_type_id=&i_port_filter=&i_rows_selector=-1&i_filter=&i_page=1&report=info&x=23&y=10"><?php print preg_replace("/(" . preg_quote($_REQUEST["i_ip_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["ip_address"]);?></a>
             </td > 
 			
 			<?php
             if (strlen(read_config_option("mt_reverse_dns")) > 0) {?>
             <td><?php print preg_replace("/(" . preg_quote($_REQUEST["i_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["dns_hostname"]);?></td>
             <?php }?>
 			<td><font size="<?php print $mac_font_size; ?>" face="Courier"><?php print strtoupper(preg_replace("/(" . preg_quote($_REQUEST["i_mac_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["mac_address"]));?></font></td>
             
             <td><?php print $port_result["vendor_name"];?></td>
 			<td><?php print $port_result["port_number"];?></td>
             <td><?php print preg_replace("/(" . preg_quote($_REQUEST["i_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["port_name"]);?></td>
             <td><?php print $port_result["vlan_id"];?></td>
             <td><?php print preg_replace("/(" . preg_quote($_REQUEST["i_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["vlan_name"]);?></td>
             <td><?php print $color_line_date . " " . $scan_date;?></td>
             <td><?php print $port_result["count_rec"];?></td>
             </tr>
             <?php
         }
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No Mac Track Port Results</em></td></tr>";
     }
 
     html_end_box(false); 
 		
 }
 




 
 function impblinding_view_net_del() {
     global $title, $report, $colors, $impblinding_search_types, $impblinding_port_search_types, $rows_selector, $config, $net_del_actions;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("nt_device_id"));
     input_validate_input_number(get_request_var_request("nt_ip_filter_type_id"));
     input_validate_input_number(get_request_var_request("nt_rows_selector"));
     input_validate_input_number(get_request_var_request("page"));
     /* ==================================================== */
 
 
     /* clean up search string */
     if (isset($_REQUEST["nt_ip_filter"])) {
         $_REQUEST["nt_ip_filter"] = translate_ip_address(sanitize_search_string(get_request_var("nt_ip_filter")));
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
     if (isset($_REQUEST["clear_net_del_x"])) {
         kill_session_var("sess_impb_view_nt_current_page");
         kill_session_var("sess_impb_view_nt_filter");
         kill_session_var("sess_impb_view_nt_ip_filter_type_id");
         kill_session_var("sess_impb_view_nt_ip_filter");
         kill_session_var("sess_impb_view_nt_rows_selector");
         kill_session_var("sess_impb_view_nt_device_id");
         kill_session_var("sess_impb_view_nt_sort_column");
         kill_session_var("sess_impb_view_nt_sort_direction");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["nt_filter"]);
         unset($_REQUEST["nt_ip_filter_type_id"]);
         unset($_REQUEST["nt_rows_selector"]);
         unset($_REQUEST["nt_ip_filter"]);
         unset($_REQUEST["nt_device_id"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("nt_filter", "sess_impb_view_nt_filter");
         $changed += impblinding_check_changed("nt_ip_filter_type_id", "sess_impb_view_nt_ip_filter_type_id");
         $changed += impblinding_check_changed("nt_ip_filter", "sess_impb_view_nt_ip_filter");
         $changed += impblinding_check_changed("nt_rows_selector", "sess_impb_view_nt_rows_selector");
         $changed += impblinding_check_changed("nt_device_id", "sess_impb_view_nt_device_id");
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["nt_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["nt_page"] = $_REQUEST["page"];
             }
         }
     }
 
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("report", "sess_impb_view_report", "net_del");
     load_current_session_value("page", "sess_impb_view_nt_current_page", "1");
     load_current_session_value("nt_page", "sess_impb_view_nt_current_page", "1");
     load_current_session_value("nt_rowstoshow", "sess_impb_view_nt_rowstoshow", "2");
     load_current_session_value("nt_filter", "sess_impb_view_nt_filter", "");
     load_current_session_value("nt_ip_filter_type_id", "sess_impb_view_nt_ip_filter_type_id", "1");
     load_current_session_value("nt_ip_filter", "sess_impb_view_nt_ip_filter", "");
     load_current_session_value("nt_rows_selector", "sess_impb_view_nt_rows_selector", "-1");
     load_current_session_value("nt_device_id", "sess_impb_view_nt_device_id", "-1");
     load_current_session_value("sort_column", "sess_impb_view_nt_sort_column", "net_ipaddr");
     load_current_session_value("sort_direction", "sess_impb_view_nt_sort_direction", "ASC");
 
     /* set nt_page variable */
     $_REQUEST["nt_page"] = $_REQUEST["page"];
 
     impblinding_view_header();
 
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_net_del_filter_table.php");
 
     impblinding_view_footer();
 
     $sql_where = "";
 
     if ($_REQUEST["nt_rows_selector"] == -1) {
         $row_limit = read_config_option("dimpb_num_rows");
     }elseif ($_REQUEST["nt_rows_selector"] == -2) {
         $row_limit = 999999;
     }else{
         $row_limit = $_REQUEST["nt_rows_selector"];
     }
 
     $netdel_results = impblinding_view_get_net_del_records($sql_where, TRUE, $row_limit);
 
     html_start_box("", "98%", $colors["header"], "3", "center", "");
 
     $rows_query_string = "SELECT
             COUNT(*)
             FROM imb_auto_updated_nets
             $sql_where";
 
      $total_rows = db_fetch_cell($rows_query_string);
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["nt_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?nt_device_id=" . $_REQUEST["nt_device_id"] . "&amp;nt_ip_filter_type_id=" . $_REQUEST["nt_ip_filter_type_id"] . "&amp;nt_ip_filter=" . $_REQUEST["nt_ip_filter"]);
 
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='6'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["nt_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?nt_device_id=" . $_REQUEST["nt_device_id"] . "&amp;nt_ip_filter_type_id=" . $_REQUEST["nt_ip_filter_type_id"] . "&amp;nt_ip_filter=" . $_REQUEST["nt_ip_filter"] . "&amp;page=" . ($_REQUEST["nt_page"]-1) . "&amp;'>"; } $nav .= "Previous"; if ($_REQUEST["nt_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["nt_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["nt_page"]))) ? $total_rows : ($row_limit*$_REQUEST["nt_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["nt_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?nt_device_id=" . $_REQUEST["nt_device_id"] . "&amp;nt_ip_filter_type_id=" . $_REQUEST["nt_ip_filter_type_id"] . "&amp;nt_ip_filter=" . $_REQUEST["nt_ip_filter"] . "&amp;page=" . ($_REQUEST["nt_page"]+1) . "&amp;'>"; } $nav .= "Next"; if (($_REQUEST["nt_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
 
                $display_text = array(
                 "net_ipaddr" => array("Адрес подсети", "ASC"),
                 "net_mask" => array("Маска подсети", "ASC"),
                 "net_description" => array("Описание", "ASC"),
				 "net_change_user_name" => array("Автор", "ASC"),
				 "net_trigger_count" => array("Triggered count", "ASC"));
 
         html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 
     $i = 0;

     if (sizeof($netdel_results) > 0) {
         foreach ($netdel_results as $netdel_result) {

            form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $netdel_result["net_id"]); $i++;
 			
 			form_selectable_cell((strlen($_REQUEST["nt_ip_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["nt_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", long2ip($netdel_result["net_ipaddr"])) : long2ip($netdel_result["net_ipaddr"])), $netdel_result["net_id"] );			
 			
 			form_selectable_cell((strlen($_REQUEST["nt_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["nt_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", long2ip($netdel_result["net_mask"])) : long2ip($netdel_result["net_mask"])), $netdel_result["net_id"] );			
			
 			form_selectable_cell((strlen($_REQUEST["nt_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["nt_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $netdel_result["net_description"]) : $netdel_result["net_description"]), $netdel_result["net_id"] );
			
			form_selectable_cell( $netdel_result["net_change_user_name"], $netdel_result["net_id"] );
			
			form_selectable_cell( $netdel_result["net_trigger_count"], $netdel_result["net_id"] );	
			
			form_checkbox_cell($netdel_result["net_ipaddr"], $netdel_result["net_id"]);
 			form_end_row();			
         }
 
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No DIMPB Auto-Deleted NETS Results</em></td></tr>";
     }
     html_end_box(false);
 	lm_draw_actions_dropdown($net_del_actions, "net_del");
 }
 




 function impblinding_view_net_add() {
     global $title, $report, $colors, $impblinding_search_types, $rows_selector, $config, $net_add_actions;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("na_device_id"));
     input_validate_input_number(get_request_var_request("na_ip_filter_type_id"));
     input_validate_input_number(get_request_var_request("na_rows_selector"));
     input_validate_input_number(get_request_var_request("page"));
     /* ==================================================== */
 
 
     /* clean up search string */
     if (isset($_REQUEST["na_ip_filter"])) {
         $_REQUEST["na_ip_filter"] = translate_ip_address(sanitize_search_string(get_request_var("na_ip_filter")));
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
     if (isset($_REQUEST["clear_net_del_x"])) {
         kill_session_var("sess_impb_view_na_currena_page");
         kill_session_var("sess_impb_view_na_filter");
         kill_session_var("sess_impb_view_na_ip_filter_type_id");
         kill_session_var("sess_impb_view_na_ip_filter");
         kill_session_var("sess_impb_view_na_rows_selector");
         kill_session_var("sess_impb_view_na_device_id");
         kill_session_var("sess_impb_view_na_sort_column");
         kill_session_var("sess_impb_view_na_sort_direction");
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["na_filter"]);
         unset($_REQUEST["na_ip_filter_type_id"]);
         unset($_REQUEST["na_rows_selector"]);
         unset($_REQUEST["na_ip_filter"]);
         unset($_REQUEST["na_device_id"]);
         unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("na_filter", "sess_impb_view_na_filter");
         $changed += impblinding_check_changed("na_ip_filter_type_id", "sess_impb_view_na_ip_filter_type_id");
         $changed += impblinding_check_changed("na_ip_filter", "sess_impb_view_na_ip_filter");
         $changed += impblinding_check_changed("nt_rows_selector", "sess_impb_view_na_rows_selector");
         $changed += impblinding_check_changed("na_device_id", "sess_impb_view_na_device_id");
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["na_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["na_page"] = $_REQUEST["page"];
             }
         }
     }
 
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("report", "sess_impb_view_report", "net_add");
     load_current_session_value("page", "sess_impb_view_na_current_page", "1");
     load_current_session_value("na_page", "sess_impb_view_na_current_page", "1");
     load_current_session_value("na_rowstoshow", "sess_impb_view_na_rowstoshow", "2");
     load_current_session_value("na_filter", "sess_impb_view_na_filter", "");
     load_current_session_value("na_ip_filter_type_id", "sess_impb_view_na_ip_filter_type_id", "1");
     load_current_session_value("na_ip_filter", "sess_impb_view_na_ip_filter", "");
     load_current_session_value("na_rows_selector", "sess_impb_view_na_rows_selector", "-1");
     load_current_session_value("na_device_id", "sess_impb_view_na_device_id", "-1");
     load_current_session_value("sort_column", "sess_impb_view_na_sort_column", "net_ipaddr");
     load_current_session_value("sort_direction", "sess_impb_view_na_sort_direction", "ASC");
 
     /* set nt_page variable */
     $_REQUEST["na_page"] = $_REQUEST["page"];
 
     impblinding_view_header();
 
     include($config['base_path'] . "/plugins/impblinding/html/inc_impblinding_view_net_add_filter_table.php");
 
     impblinding_view_footer();
 
     $sql_where = "";
 
     if ($_REQUEST["na_rows_selector"] == -1) {
         $row_limit = read_config_option("dimpb_num_rows");
     }elseif ($_REQUEST["na_rows_selector"] == -2) {
         $row_limit = 999999;
     }else{
         $row_limit = $_REQUEST["na_rows_selector"];
     }
 
     $netdel_results = impblinding_view_get_net_add_records($sql_where, TRUE, $row_limit);
 
     html_start_box("", "98%", $colors["header"], "3", "center", "");
 
	 $rows_query_string = "SELECT COUNT(*) FROM imb_auto_updated_nets " .
             (trim($sql_where) == "" ? " where `net_type`='2'" : $sql_where . " and `net_type`='2'");
 
      $total_rows = db_fetch_cell($rows_query_string);
 
     /* generate page list */
     $url_page_select = get_page_list($_REQUEST["na_page"], MAX_DISPLAY_PAGES, $row_limit, $total_rows, "impblinding_view.php?na_device_id=" . $_REQUEST["na_device_id"] . "&amp;na_ip_filter_type_id=" . $_REQUEST["na_ip_filter_type_id"] . "&amp;na_ip_filter=" . $_REQUEST["na_ip_filter"]);
 
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
                 <td colspan='6'>
                     <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                         <tr>
                             <td align='left' class='textHeaderDark'>
                                 <strong>&lt;&lt; "; if ($_REQUEST["na_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?na_device_id=" . $_REQUEST["na_device_id"] . "&amp;na_ip_filter_type_id=" . $_REQUEST["na_ip_filter_type_id"] . "&amp;na_ip_filter=" . $_REQUEST["na_ip_filter"] . "&amp;page=" . ($_REQUEST["na_page"]-1) . "&amp;'>"; } $nav .= "Previous"; if ($_REQUEST["na_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                             </td>\n
                             <td align='center' class='textHeaderDark'>
                                 Showing Rows " . (($row_limit*($_REQUEST["na_page"]-1))+1) . " to " . ((($total_rows < $row_limit) || ($total_rows < ($row_limit*$_REQUEST["na_page"]))) ? $total_rows : ($row_limit*$_REQUEST["na_page"])) . " of $total_rows [$url_page_select]
                             </td>\n
                             <td align='right' class='textHeaderDark'>
                                 <strong>"; if (($_REQUEST["na_page"] * $row_limit) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_view.php?na_device_id=" . $_REQUEST["na_device_id"] . "&amp;na_ip_filter_type_id=" . $_REQUEST["na_ip_filter_type_id"] . "&amp;na_ip_filter=" . $_REQUEST["na_ip_filter"] . "&amp;page=" . ($_REQUEST["na_page"]+1) . "&amp;'>"; } $nav .= "Next"; if (($_REQUEST["na_page"] * $row_limit) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                             </td>\n
                         </tr>
                     </table>
                 </td>
             </tr>\n";
 
     print $nav;
 
                $display_text = array(
                 "net_ipaddr" => array("Адрес подсети", "ASC"),
                 "description" => array("Устройство", "ASC"),
				 "net_ttl" => array("Время действия", "ASC"),
                 "net_description" => array("Описание", "ASC"),
				 "net_change_user_name" => array("Автор", "ASC"));
 
         html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 
     $i = 0;

     if (sizeof($netdel_results) > 0) {
         foreach ($netdel_results as $netdel_result) {

            form_alternate_row_color($colors["alternate"], $colors["light"], $i, 'line' . $netdel_result["net_id"]); $i++;
 			
 			form_selectable_cell((strlen($_REQUEST["na_ip_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["na_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", long2ip($netdel_result["net_ipaddr"])) : long2ip($netdel_result["net_ipaddr"])), $netdel_result["net_id"] );			
 			
 			form_selectable_cell( $netdel_result["description"], $netdel_result["net_id"] );
			
			form_selectable_cell( ($netdel_result["net_ttl"]=='0' ? "" :"До ") . $netdel_result["net_ttl_date"], $netdel_result["net_id"] );
			
 			form_selectable_cell((strlen($_REQUEST["na_filter"]) ? preg_replace("/(" . preg_quote($_REQUEST["na_filter"]) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $netdel_result["net_description"]) : $netdel_result["net_description"]), $netdel_result["net_id"] );
			
			form_selectable_cell( $netdel_result["net_change_user_name"], $netdel_result["net_id"] );
			
			form_checkbox_cell($netdel_result["net_ipaddr"], $netdel_result["net_id"]);
 			form_end_row();			
         }
 
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No DIMPB Auto-Deleted NETS Results</em></td></tr>";
     }
     html_end_box(false);
 	lm_draw_actions_dropdown($net_add_actions, "net_add", 3);
 }
 
  
 //******************************************************************************










 
 
 function form_actions_ports() {
     global $colors, $config, $port_actions;
 
     /* if we are to save this form, instead of display it */
     if (isset($_POST["selected_items"])) {
        $selected_items = unserialize(stripslashes($_POST["selected_items"]));
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
		$use_strict_mode = (isset($_REQUEST["use_strict_mode"]));
		$use_zerroip = (isset($_REQUEST["use_zerroip"]));
		$use_ip_inspect = (isset($_REQUEST["use_ip_inspect"]));
			
 		if ($_POST["drp_action"] == "1") { /* Enable IP-MAC-PORT */
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
             header("Location: impblinding_view.php");
 
         } elseif ($_POST["drp_action"] == "2") { /* Disable IP-MAC-PORT */

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
 		} elseif ($_POST["drp_action"] == "3") { /* Изменить описание порта*/
			if (sizeof($ports) > 0) {
				foreach ($ports as $port) {	
					//$port_record = db_fetch_row ("SELECT * FROM imb_ports WHERE port_id=" . $port_name["port_id"] . ";");
					imb_change_port_name($port, $port_devices[$port["device_id"]], $_POST["t_" . $port["port_id"] . "_port_name"]);
				}
			}			 
 		} elseif ($_POST["drp_action"] == "4") { /* Включение порта */

			for ($i=0;($i<count($ports));$i++) {	
				api_imp_change_port_state($ports[$i], $port_devices[$ports[$i]["device_id"]], "3");
            }		
 		} elseif ($_POST["drp_action"] == "5") { /* ВЫКлючение порта */

			for ($i=0;($i<count($ports));$i++) {	
				api_imp_change_port_state($ports[$i], $port_devices[$ports[$i]["device_id"]], "2");
            }		
 		}
 		
 		 if (isset($_REQUEST["save_config"])) { 
 				imp_save_config($device_ids);
 		}
 	header("Location: impblinding_view.php");
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = "";
 
    /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
	while (list($var,$val) = each($_POST)) {
        if (ereg("^chk_([0-9]+)$", $var, $matches)) {
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
	 
 
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
 
     html_start_box("<strong>" . $port_actions{$_POST["drp_action"]} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_view.php' method='post'>\n";
 
     if ($_POST["drp_action"] == "1") {  /* Enable Ip-Mac on port */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Are you sure you want to ENABLE IP-MAC-PORT Binding on the following ports?</p>
                     <p>$row_list</p>";
			if ($cnt_devs_v3_9 > 0) {
				form_checkbox("use_ip_inspect", "", "Также изменить режим <strong>IP Inspection</strong>", "",$current_id = 0, $class = "", $on_change = "");print "<br>";
			} 
 			form_checkbox("use_zerroip", "on", "Также изменить режим <strong>нулевого IP (Allow Zero IP)</strong>", "",$current_id = 0, $class = "", $on_change = "");print "<br>";
 			print "<br>";
			form_checkbox("use_strict_mode", "", "При возможности использовать режим Enabled-Strict. По умолчанию будет использоваться режим Enabled-Loose", "",$current_id = 0, $class = "", $on_change = "");print "<br>";
            print "</td>
             </tr>\n
             ";
     } else if ($_POST["drp_action"] == "2") {
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Are you sure you want to DISABLE IP-MAC-PORT Binding on the following ports?</p>
                     <p>$row_list</p>";
			if ($cnt_devs_v3_9 > 0) {
				form_checkbox("use_ip_inspect", "on", "Также изменить режим <strong>IP Inspection</strong>", "",$current_id = 0, $class = "", $on_change = "");print "<br>";
			}					 
 			form_checkbox("use_zerroip", "on", "Также применить режим <strong>нулевого IP (Allow Zero IP)</strong>", "",$current_id = 0, $class = "", $on_change = "");print "<br>";					
            print "</td>
             </tr>\n
             ";	
 	}else if ($_POST["drp_action"] == "3") { /*Изменить описание порта*/
 
 
 	$port_rows=db_fetch_assoc("SELECT imb_ports.*, imb_devices.hostname, imb_devices.description FROM imb_ports left join imb_devices on (imb_ports.device_id = imb_devices.device_id) WHERE imb_ports.port_id in (" . $row_ids . ");");
 
 	html_start_box("Для изменения описания порта проверьте/измените следующие поля.", "98%", $colors["header"], "3", "center", "");
 
    html_header(array("","Host<br>Description","Hostname<br>", "№ порта", "Описание порта"));
 
     $i = 0;
     if (sizeof($port_rows) > 0) {
         foreach ($port_rows as $port_row) {
 			$port_id = $port_row["port_id"];
             form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
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
     html_end_box(false);
 
 
 	} else if ($_POST["drp_action"] == "4") { // включить порт
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Вы уверенны что хотите ВКЛючить следующие порты ?</p>
                     <p>$row_list</p>";
            print "</td>
             </tr>\n
             ";	
 	} else if ($_POST["drp_action"] == "5") { // включить порт
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Вы уверенны что хотите ВЫКЛючить следующие порты ?</p>
                     <p>$row_list</p>";
            print "</td>
             </tr>\n
             ";	
 	};
 
 	
     if (!isset($row_array)) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>You must select at least one port.</span></td></tr>\n";
         $save_html = "";
     }else{
 		print "<tr>
 				<br>
 				<td colspan='2' align='left' bgcolor='#eaeaea'>";
 					form_checkbox("save_config", "", "Сохранить конфигурацию ?", "");
 		print "</td>
 			</tr>\n";
 
		$save_html = "<input type='submit' name='Save' value='Применить'>";
     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_ports'>
                 <input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
                 <input type='hidden' name='drp_action' value='" . $_POST["drp_action"] . "'>
                 <input type='submit' name='Cancel' value='Отмена'>
                 $save_html
             </td>
         </tr>
         ";
 
     html_end_box();
 
 }
 
 
 
 
 function form_actions_blmacs() {
     global $colors, $config, $blmacs_actions, $fields_macipport_edit, $impblinding_imp_mode;
 
     /* if we are to save this form, instead of display it */
     if (isset($_REQUEST["selected_items"])) {
        $selected_items = unserialize(stripslashes($_REQUEST["selected_items"]));
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
		
 		         
 		
 		
 		if ($_REQUEST["drp_action"] == "1") { /* Прописать заблокированный мак. */
 		   $blmacs_user = array();
		   for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
 				$cur_blmac_id = $selected_items[$i];
 				$blmacs_user[$cur_blmac_id]["blmac_id"] = $cur_blmac_id;
 				$blmacs_user[$cur_blmac_id]["blmac_port"] = form_input_validate($_REQUEST["t_" . $cur_blmac_id . "_port"], "t_" . $cur_blmac_id . "_port", "^([0-5]{0,1}[0-9]{1}(,|-){1})*[0-5]{0,1}[0-9]{1}$", false, 3);
 				$blmacs_user[$cur_blmac_id]["blmac_ip_adrress"] = form_input_validate(translate_ip_address($_REQUEST["t_" . $cur_blmac_id . "_ip_adrress"]), "t_" . $cur_blmac_id . "_ip_adrress", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
 				$blmacs_user[$cur_blmac_id]["blmac_acl_mode"] = $impblinding_imp_mode[form_input_validate($_REQUEST["t_" . $cur_blmac_id . "_use_acl"], "t_" . $cur_blmac_id . "_use_acl", "[^0]", false, 3) ];
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
 		}elseif ($_REQUEST["drp_action"] == "2") { /* delete blocked macs */
			if (sizeof($blmacs) > 0) {
				foreach ($blmacs as $blmac) {	
					if (isset($blmacs_devices[$blmac["device_id"]])) {
						//$port_record = db_fetch_row ("SELECT * FROM imb_ports WHERE port_id=" . $port_name["port_id"] . ";");
						api_imp_delete_blmacs($blmac, $blmacs_devices[$blmac["device_id"]]);
					}
				}
			}
            header("Location: impblinding_view.php");
         };
 
 		if (isset($_REQUEST["save_config"])) { 
 				imp_save_config($device_ids);
 		}
 	if (!is_error_message()) {
 		header("Location: impblinding_view.php");
 	}else{
 		header("Location: impblinding_view.php?action=" . $_REQUEST["action"] . "&drp_action=" . $_REQUEST["drp_action"] . "&post_error=" . $_REQUEST["selected_items"]);
 		$_REQUEST["selected_items"]="";
 	}
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0;
 	$row_ids = ""; $i = 0;
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     if (!isset($_GET["post_error"])) { /*Если установлено это значение - значит страница перезагружаеться из-за ошибки при вводе, и данные нужно брать не из POST, а из спец. переменной.*/
 	while (list($var,$val) = each($_REQUEST)) {
         if (ereg("^chk_([0-9]+)$", $var, $matches)) {
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
 		$row_array=unserialize(stripslashes($_GET["post_error"]));
 		if (isset($row_array) && is_array($row_array) && (count($row_array) > 0)) {
 			foreach ($row_array as $row_id) {
 	            $row_info = db_fetch_row("SELECT imb_blmacs.*, imb_devices.hostname, imb_devices.description FROM imb_blmacs left join imb_devices on (imb_blmacs.device_id = imb_devices.device_id) WHERE imb_blmacs.blmac_id=" . $row_id);
 				$row_list .= "<li>" . $row_info["description"] . " PORT:" . $row_info["blmac_port"] . " MAC [" . $row_info["blmac_macaddr"] . "]<br>";
 				$row_ids = $row_ids . "'" . $row_id . "', ";		
 			}
 		}
 	}
 	
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
 
     html_start_box("<strong>" . $blmacs_actions{(isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"])} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_view.php' method='post'>\n";
 
 	
 	
 	if (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "1")) || ((isset($_GET["post_error"])) && ($_GET["drp_action"] == "1")))  {
 		$blmacs_rows=db_fetch_assoc("SELECT imb_blmacs.*, imb_devices.hostname, imb_devices.description, imb_device_types.setting_imb_def_mode " .
 										" FROM imb_blmacs " .
 										" left join imb_devices on (imb_blmacs.device_id = imb_devices.device_id) " .
 										" left join imb_device_types on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
 										" WHERE imb_blmacs.blmac_id in (" . $row_ids . ");");
 		html_start_box("Для создания записи IP-MAC-PORT проверьте/измените следующие поля.", "98%", $colors["header"], "4", "center", "");
 	    html_header(array("","Host<br>Description","Hostname<br>", "IP-адресс", "MAC-адресс",  "Порт", "Режим"));
 	    $i = 0;
 	    if (sizeof($blmacs_rows) > 0) {
 	        foreach ($blmacs_rows as $blmacs_row) {
 				$blmac_id = $blmacs_row["blmac_id"];
 	            form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	                ?>
 					<td><?php form_hidden_box("t_" . $blmac_id . "_blmac_id", $blmac_id, "form_default_value");?></td>
 					<td><?php print $blmacs_row["description"];?></td>
 					<td><?php print $blmacs_row["hostname"];?></td>
 					<td><?php form_text_box("t_" . $blmac_id . "_ip_adrress", $blmacs_row["blmac_blocked_ip"], "", 15, 15, "text", 1) ;?></td>
 					<td><?php print $blmacs_row["blmac_macaddr"];?></td>
 					<td><?php form_text_box("t_" . $blmac_id . "_port", $blmacs_row["blmac_port"], "", 2, 2, "text", 1) ;?></td>
 					<td><?php form_dropdown("t_" . $blmac_id . "_use_acl",  $impblinding_imp_mode, "", "", $blmacs_row["setting_imb_def_mode"], "", "") ;?></td>				
 	            </tr>
 	            <?php
 	        }
 	    }
 		html_end_box(false);
 
 
 	}elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "2") ) {  /* delete blocked macs*/
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Поддтверждаете удаление следующих блоков ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
     };
 
 	
     if (!isset($row_array)) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>You must select at least one port.</span></td></tr>\n";
         $save_html = "";
     }else{
 		if ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "1") || ((isset($_GET["post_error"])) && ($_GET["drp_action"] == "1"))) {
 			print "<tr>
 					<td colspan='2' align='left' bgcolor='#eaeaea'>
 					<br>";
 						form_checkbox("save_config", "", "Сохранить конфигурацию ?", ""); print "<br>";
 			print " </td>
 				</tr>\n";
 		}
		$save_html = "<input type='submit' name='Save' value='Save (сохранить)'>";
     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_blmacs'>
                 <input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
                 <input type='hidden' name='drp_action' value='" . (isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"]) . "'>
                 <input type='submit' name='Cancel' value='Cancel (отмена)'>
                 $save_html
             </td>
         </tr>
         ";
     html_end_box();
 }
 
 
 function form_actions_devices() {
     global $colors, $config, $device_actions;
 
     /* if we are to save this form, instead of display it */
     if (isset($_POST["selected_items"])) {
         $selected_items = unserialize(stripslashes($_POST["selected_items"]));
 		$str_blmacs_ids = '';
         if ($_POST["drp_action"] == "5") { /* Опросить устройство */
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
 
 				run_poller_impblinding($selected_items[$i]);
             }
             header("Location: impblinding_view.php");
 
         } elseif ($_POST["drp_action"] == "7") { /* Сохранить конфигурацию */
            for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
 				
 				imp_save_config_main($selected_items[$i]);
             }		
 		}
 	header("Location: impblinding_view.php");
         exit;
     }
 
     /* setup some variables */
     $device_list = ""; $i = 0;
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     while (list($var,$val) = each($_POST)) {
         if (ereg("^chk_([0-9]+)$", $var, $matches)) {
             /* ================= input validation ================= */
             input_validate_input_number($matches[1]);
             /* ==================================================== */
 			$device_info = db_fetch_row("SELECT hostname, description FROM imb_devices WHERE device_id=" . $matches[1]);
 			$device_list .= "<li>" . $device_info["description"] . " (" . $device_info["hostname"] . ")<br>";
 			$device_array[$i] = $matches[1];
         $i++;
		 }                                  
 
         
     }
 
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
 
     html_start_box("<strong>" . $device_actions{$_POST["drp_action"]} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_view.php' method='post'>\n";
 
     if ($_POST["drp_action"] == "5") {  /* Update Info */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Обновить информацию со следующих устройств ?</p>
                     <p>$device_list</p>
                 </td>
             </tr>\n
             ";
     } else if ($_POST["drp_action"] == "7") { /*Сохранить конфигурацию*/
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете сохранение конфигурации на следующих устройствах в NV-RAM ?</p>
                     <p>$device_list</p>
                 </td>
             </tr>\n
             ";	
 	}
 
 	
     if (!isset($device_array)) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>Вы должны выбрать хотябы одно устройство.</span></td></tr>\n";
         $save_html = "";
     }else{
		$save_html = "<input type='submit' name='Save' value='Продолжить'>";
     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_devices'>
                 <input type='hidden' name='selected_items' value='" . (isset($device_array) ? serialize($device_array) : '') . "'>
                 <input type='hidden' name='drp_action' value='" . $_POST["drp_action"] . "'>
                 <input type='submit' name='Cancel' value='Отменить'>
                 $save_html
             </td>
         </tr>
         ";
 
     html_end_box();
 }
 
 function form_actions_recentmacs() {
     global $colors, $config, $recentmacs_actions, $impblinding_imp_mode;
 
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
 				$recents[$cur_recent_row_id]["recent_mac_address"] = translate_mac_address($_POST["rm_" . $cur_recent_row_id . "_mac_address"]);
 				$recents[$cur_recent_row_id]["recent_ip_address"] = translate_ip_address($_POST["rm_" . $cur_recent_row_id . "_ip_address"]);
 				$recents[$cur_recent_row_id]["recent_port"] = $_POST["rm_" . $cur_recent_row_id . "_port"];
 				$recents[$cur_recent_row_id]["recent_acl_mode"] = $impblinding_imp_mode[$_REQUEST["rm_" . $cur_recent_row_id . "_use_acl"]];
            }
                 
 			if (sizeof($recents) > 0) {
 				foreach ($recents as $recent) {	
 					imb_create_imp_record($recent["recent_device_id"],$recent["recent_mac_address"], $recent["recent_ip_address"], $recent["recent_port"] , $recent["recent_acl_mode"], false);
 				//	imb_create_imp_record($device_id,                 $mac_adrress,                 $ip_adrress,                   $port,                 $acl_mode, $ban = false)
 				}
 			}	
 		} 
 		
 
 		
 	header("Location: impblinding_view.php");
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = "";
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     while (list($var,$val) = each($_POST)) {
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
 	
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
 
     html_start_box("<strong>" . $recentmacs_actions{$_POST["drp_action"]} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_view.php' method='post'>\n";
 
     if ($_POST["drp_action"] == "1") {  /*удалить запись */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
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
 
 		html_start_box("Для создания записи IP-MAC-PORT проверьте/измените следующие поля.", "98%", $colors["header"], "3", "center", "");
 
 
 	    html_header(array("","","","Host Description","Hostname<br>", "Номер порта","Описание порта","IP-адресс", "MAC-адресс",  "Порт",  "Режим"));
 
 	    $i = 0;
 	    if (sizeof($recent_macips_rows) > 0) {
 	        foreach ($recent_macips_rows as $recent_macips_row) {
 				$recent_macips_id = $recent_macips_row["row_id"];
 	            form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
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
 					<td><?php form_dropdown("rm_" . $recent_macips_id . "_use_acl",  $impblinding_imp_mode, "", "", "ARP", "", "") ;?></td>				
 	            </tr>
 	            <?php
 	        }
 	    }
 		html_end_box(false);
 	
 	
 	
 	
 
 	
 	}
 
 	
     if (!isset($row_array)) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>Вы должны выбрать хотябы одну строку</span></td></tr>\n";
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
 
 
 
 function form_actions_macips() {
     global $colors, $config, $macips_actions, $fields_impblinding_macip_group_edit, $impblinding_operation_macip_types, $impblinding_imp_mode ;
 
     /* if we are to save this form, instead of display it */
     if (isset($_POST["selected_items"])) {
        $selected_items = unserialize(stripslashes($_REQUEST["selected_items"]));
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

 		if ($_POST["drp_action"] == "1") { /* удаление записи ип-мак-порт */
			if (sizeof($macips) > 0) {
				foreach ($macips as $macip) {	
					if (isset($macips_devices[$macip["device_id"]])) {
						//$port_record = db_fetch_row ("SELECT * FROM imb_ports WHERE port_id=" . $port_name["port_id"] . ";");
						//api_imp_delete_blmacs($blmac, $blmacs_devices[$blmac["device_id"]]);
						api_imp_delete_macip($macip, $macips_devices[$macip["device_id"]], false);
					}
				}
			}			 
             //header("Location: impblinding_view.php");
 
        } elseif ($_POST["drp_action"] == "2") { /* изменить запись */
            			//$save_data[$cur_macip_id]["macip_id"] = form_input_validate($_REQUEST["tma_ip"], "tma_ip", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
			$macips_users = array();
		    for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 				$cur_macip_id = $selected_items[$i];
 				$macips_users[$cur_macip_id]["macip_id"] = $cur_macip_id;
 				$macips_users[$cur_macip_id]["macip_mac_adrress"] = form_input_validate(translate_mac_address($_REQUEST["tm_" . $cur_macip_id . "_mac_adrress"]), "tm_" . $cur_macip_id . "_mac_adrress", "^(([0-9]|[a-f]|[A-F]){2}\:){5}([0-9]|[a-f]|[A-F]){2}$", false,3 );
 				$macips_users[$cur_macip_id]["macip_port"] = form_input_validate($_REQUEST["tm_" . $cur_macip_id . "_port"], "tm_" . $cur_macip_id . "_port", "^([0-5]{0,1}[0-9]{1}(,|-){1})*[0-5]{0,1}[0-9]{1}$", false, 3);
 				$macips_users[$cur_macip_id]["macip_mode"] = $impblinding_imp_mode[form_input_validate($_REQUEST["tm_" . $cur_macip_id . "_use_acl"], "tm_" . $cur_macip_id . "_use_acl", "[^0]", false, 3)];
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
 		} elseif ($_POST["drp_action"] == "3") { /* Групповое изменение */
 			$temp_macips_to_change = array();
 			$operation_type = form_input_validate($_POST["tmg_operation_type"], "tmg_operation_type", "", false, 3);
 				
 				
 				for ($i=0;($i<count($selected_items));$i++) {
 					$str_ids = $str_ids . "'" . $selected_items[$i] . "', ";
 					$row_array[$i] = $selected_items[$i];
 				}
 				$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 				
 				$temp_macips_to_change = db_fetch_assoc("SELECT imb_macip.* FROM imb_macip  left join imb_devices  on (imb_macip.device_id = imb_devices.device_id) WHERE macip_id in (" . $str_ids . ");");
 				$new_device_id = form_input_validate($_POST["tmg_device_id"], "tmg_device_id", "", false, 3);
 				$new_port = form_input_validate($_POST["tmg_port_number"], "tmg_port_number", "^([0-5]{0,1}[0-9]{1}(,|-){1})*[0-5]{0,1}[0-9]{1}$", false, 3);
 				$new_acl_mode = form_input_validate($_POST["tmg_use_acl"], "tmg_use_acl", "", false, 3);
 				
 				if (!is_error_message()) {
 				for($i=0;$i<=(sizeof($temp_macips_to_change)-1);$i++) {
 					$temp_macips_to_change[$i]["_change"] = 0;
 					if ($_REQUEST["tmg_device_id"] != 0) {
 						$temp_macips_to_change[$i]["_device_id"] = $new_device_id;
 					} else {
 						$temp_macips_to_change[$i]["_device_id"] = $temp_macips_to_change[$i]["device_id"];
 					};
 					
                     if (($_REQUEST["tmg_port_number"] != "") && ($temp_macips_to_change[$i]["macip_port_view"] != $new_port)) {
 						$temp_macips_to_change[$i]["_port"] = $new_port;
                         $temp_macips_to_change[$i]["_change"] = $temp_macips_to_change[$i]["_change"] +1;
                     } else {
                         $temp_macips_to_change[$i]["_port"]  = $temp_macips_to_change[$i]["macip_port_view"];
                     };					
 					
 					if ($_REQUEST["tmg_use_acl"] != 0) {
 						$temp_macips_to_change[$i]["_acl_mode"]  = $impblinding_imp_mode[$new_acl_mode];
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
 			
 			
 
 		}elseif ($_POST["drp_action"] == "4") { /* создание привязки */
 			$save_data = array();
 			$save_data["tma_device_id"] = form_input_validate($_POST["tma_device_id"], "tma_device_id", "[^0]", false, 3);
 			$save_data["tma_ip"] = form_input_validate(translate_ip_address($_POST["tma_ip"]), "tma_ip", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
 			//'^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$',
 			$save_data["tma_mac"] = form_input_validate(translate_mac_address($_POST["tma_mac"]), "tma_mac", "^(([0-9]|[a-f]|[A-F]){2}\:){5}([0-9]|[a-f]|[A-F]){2}$", false, 3);
 			$save_data["tma_port_number"] = form_input_validate($_POST["tma_port_number"], "tma_port_number", "^([0-5]{0,1}[0-9]{1}(,|-){1})*[0-5]{0,1}[0-9]{1}$", false, 3);
 			$save_data["tma_mode"] = $impblinding_imp_mode[form_input_validate($_POST["tma_use_acl"], "tma_use_acl", "[^0]", false, 3)] ;
 			if (!is_error_message()) {
 
 				$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 					imb_create_imp_record($save_data["tma_device_id"] ,$save_data["tma_mac"], $save_data["tma_ip"], $save_data["tma_port_number"] , $save_data["tma_mode"]);
 			}
 		}elseif ($_POST["drp_action"] == "5") { /* освобождение записи */
             // for ($i=0;($i<count($selected_items));$i++) {
                 // /* ================= input validation ================= */
                 // input_validate_input_number($selected_items[$i]);
                 // /* ==================================================== */
                 // api_imp_change_free_macip($selected_items[$i], false);
             // }
			db_execute("UPDATE `imb_macip` SET `macip_may_move`=IF(`macip_may_move`=1,0,1) where `macip_id` in (" . $str_ids . ");");
             //header("Location: impblinding_view.php");
 
        }elseif ($_POST["drp_action"] == "6") { /* СМС рассылка */

 		$macips_mobile_rows=db_fetch_assoc("SELECT mobile, i.macip_ipaddr, CONCAT(ag_num,'  ', REPLACE(f_addr , 'Россия,обл Самарская,,г Кинель,', '')) as addr FROM lb_vgroups_s l " .
 			 " LEFT JOIN lb_staff lb ON (lb.vg_id=l.vg_id) " .
 			 " LEFT JOIN imb_macip i ON (i.macip_ipaddr=lb.ip) " .
 			 " WHERE i.macip_id in (" . $str_ids . ");");
		$mobils = "";
		foreach ($macips_mobile_rows as $macips_mobile_row) {
			$mobils = $mobils . " " . $macips_mobile_row["mobile"] . ", ";		
		}		
		
			$_SESSION["ar_ssms_num"] = serialize($mobils);
 
 		}
 		 if (isset($_REQUEST["save_config"])) { 
 				imp_save_config_main($save_data["tma_device_id"]);
 		}
 	
 	if (!is_error_message()) {
		if ($_POST["drp_action"] == "6"){
			header("Location: ../gammu/gammu_view.php?report=sendsms");
		}else{
			header("Location: impblinding_view.php");
		}
 	}else{
 		header("Location: impblinding_view.php?action=" . $_POST["action"] . "&drp_action=" . $_POST["drp_action"] . "&post_error=" . serialize($selected_items));
 		$_REQUEST["selected_items"]="";
 	}
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = ""; $post_if_error = "";
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     if (!isset($_GET["post_error"])) { /*Если установлено это значение - значит страница перезагружаеться из-за ошибки при вводе, и данные нужно брать не из POST, а из спец. переменной.*/
 		while (list($var,$val) = each($_POST)) {
 	        if (ereg("^chk_([0-9]+)$", $var, $matches)) {
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
 		$row_array=unserialize(stripslashes($_GET["post_error"]));
 		if (isset($row_array) && is_array($row_array) && (count($row_array) > 0)) {
 			foreach ($row_array as $row_id) {
 	            $row_info = db_fetch_row("SELECT imb_macip.*, imb_devices.hostname, imb_devices.description FROM imb_macip left join imb_devices on (imb_macip.device_id = imb_devices.device_id) WHERE imb_macip.macip_id=" . $row_id);
 				$row_list .= "<li>" . $row_info["description"] . "      IP1:" . $row_info["macip_ipaddr"] . "    MAC:" . $row_info["macip_macaddr"] . "      PORT:" . $row_info["macip_port_view"] . "<br>";
 				$row_ids = $row_ids . "'" . $row_id . "', ";		
 			}
 		}
 	}
 	
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
 
     html_start_box("<strong>" . $macips_actions{(isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"])} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_view.php' method='post'>\n";
 	
 	// if ((!isset($_REQUEST["drp_action"])) && isset($_REQUEST["drp_action"])) {
 		// $_REQUEST["drp_action"] = $_REQUEST["drp_action"];
 	// }
 	
     if ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "1")) {  /* удаление записей */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете удаление следующих записей ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
     } elseif (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "2")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] == "2"))) { /*Изменение записи*/
 		$macips_rows=db_fetch_assoc("SELECT imb_macip.*,  imb_devices.hostname, imb_devices.description" . 
 			" FROM imb_macip left join imb_devices on (imb_macip.device_id = imb_devices.device_id) " . 
 			" WHERE imb_macip.macip_id in (" . $row_ids . ");");
 
 		html_start_box("Для изменения записи IP-MAC-PORT проверьте/измените следующие поля.", "98%", $colors["header"], "3", "center", "");
 
 
 	    html_header(array("","Host<br>Description","Hostname<br>", "Номер порта","Описание порта","IP-адресс", "MAC-адресс",  "Порт", "Режим"));
 
 	    $i = 0;
 	    if (sizeof($macips_rows) > 0) {
 	        foreach ($macips_rows as $macips_row) {
 				$macips_id = $macips_row["macip_id"];
 	            form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
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
 					<td><?php form_dropdown("tm_" . $macips_id . "_use_acl",  $impblinding_imp_mode, "", "", $int_default_mode, "", "") ;?></td>				
 	            </tr>
 	            <?php
 	        }
 	    }
 		html_end_box(false);
 
 
 	} elseif (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "3")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] == "3"))) { /*Групповое изменение параметра*/
         print "<tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Проверьте группу записей, у которых необходимо произвести изменения</p>
                     <p>$row_list</p>
                 </td><td></td>
             </tr>\n
             ";	
 		html_start_box("Проверьте значения изменяемых полей", "98%", $colors["header"], "4", "center", "");
 
 
 	    html_header(array("Устройство назначения", "Номер порта","Режим","Тип операции"));
 		
 			?>
 				<td><?php form_dropdown("tmg_device_id", db_fetch_assoc("select device_id as id, description as name from imb_devices where snmp_status <> 1 order by INET_ATON(hostname) ASC"), "name", "id", "none", "Не изменять", "") ;?></td>
 				<td><?php form_text_box("tmg_port_number", "", "", 250, 15, "text", 1) ;?></td>
 				<td><?php form_dropdown("tmg_use_acl",  $impblinding_imp_mode, "", "", 3, "Не изменять", "") ;?></td>				
 				<td><?php form_dropdown("tmg_operation_type", $impblinding_operation_macip_types, "", "", "none", "", "") ;?></td>
 			</tr>
 			<?php
 			
 			
 		html_end_box();
 	
 	}elseif (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "4")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] == "4"))) { /*Добавление привязки*/
 
 		html_start_box("Проверьте значения полей", "98%", $colors["header"], "4", "center", "");
 
 
 	    html_header(array("Устройство назначения","Номер порта", "IP-адрес", "MAC-адрес", "Режим"));
 		
 			?>
 				<td><?php form_dropdown("tma_device_id", db_fetch_assoc("select device_id as id, description as name from imb_devices order by INET_ATON(hostname) ASC"), "name", "id", "none", "Выберите ....", "") ;?></td>
 				<td><?php form_text_box("tma_port_number", "", "", 250, 12, "text", 1) ;?></td>
 				<td><?php form_text_box("tma_ip",read_config_option("dimpb_default_ip_mask") , "", 15, 12, "text", 1) ;?></td>
 				<td><?php form_text_box("tma_mac", "00:00:00:00:00:00", "", 17, 15, "text", 1) ;?></td>
 				<td><?php form_dropdown("tma_use_acl",  $impblinding_imp_mode, "", "", "ARP", "", "") ;?></td>				
 			</tr>
 			<?php
 			
 			
 		html_end_box();
 	}elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "5")) {  /* Освобождение записей */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете изменение следующих записей ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
     }elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "6")) {  /* СМС Рассылка */
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
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете создание рассылки для следующих записей ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
     };
 
 	
     if (!isset($row_array) && (((isset($_POST["drp_action"])) && ($_POST["drp_action"] != "4")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] != "4")))) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>Вы должны выбрать хотя-бы одну запись.</span></td></tr>\n";
        $save_html = "";
     }else{
 		if ((isset($_POST["drp_action"])) && ($_POST["drp_action"] != "6")) {
		print "<tr>
 				<td colspan='2' align='left' bgcolor='#eaeaea'>
 					<p><input type='checkbox' name='save_config'  >Сохранить конфигурацию ?</p>
 				</td>
 			</tr>\n";
			$save_html = "<input type='submit' name='Save' value='Применить'>";
		}else{
			$save_html = "<input type='submit' name='Save' value='Перейти к отравке'>";
		}
		

     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_macips'>
                 <input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
 				<input type='hidden' name='post_if_error' value='" . $post_if_error . "'>
                 <input type='hidden' name='drp_action' value='" . (isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"]) . "'>
				 <input type='submit' name='Cancel' value='Отмена'>
                 $save_html
             </td>
         </tr>
         ";
 
     html_end_box();
 }
 
 
 function form_actions_banips() {
     global $colors, $config, $banip_actions, $impblinding_imb_banip_type, $impblinding_imb_yes_no ;
 
     /* if we are to save this form, instead of display it */
     if (isset($_POST["selected_items"])) {
         $selected_items = unserialize(stripslashes($_POST["selected_items"]));
 		$str_ids = '';
 		 // if (isset($_REQUEST["save_config"])) { 
 			 for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */			 
 				 $str_ids = $str_ids . "'" . $selected_items[$i] . "', ";
 			 }
 			 $str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 			// $device_ids=db_fetch_assoc("SELECT device_id FROM imb_macip where port_id in (" . $str_ids . ") group by device_id;");
 		// }        
 		
 		if ($_POST["drp_action"] == "1") { /* создание записи бана */
 			$save_data = array();
 			$save_data["bna_ip"] = form_input_validate(translate_ip_address($_REQUEST["bna_ip"]), "bna_ip", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
 			$save_data["bna_type"] = $impblinding_imb_banip_type[form_input_validate($_REQUEST["bna_type"], "bna_type", "[^0]", false, 3)] ;
 			$save_data["bna_expiration_date"] = form_input_validate($_REQUEST["bna_expiration_date"], "bna_expiration_date", "", false, 3);
 			$save_data["bna_message"] = form_input_validate($_REQUEST["bna_message"], "bna_message", "", true, 3);
 			// if (!is_error_message()) {
 
 				// $str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 					imb_ban_create_record($save_data["bna_ip"], $save_data["bna_type"], $banip_manual = true, $save_data["bna_expiration_date"], $save_data["bna_message"]);
 			// }
 		} elseif ($_POST["drp_action"] == "2") { /* Удалить  бан */
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
                 imb_ban_delete($selected_items[$i], 1);
             }
 		} elseif ($_POST["drp_action"] == "3") { /* Разрешить бан */
             $ban_rows=db_fetch_assoc("SELECT banip_id,banip_ipaddr,banip_aplled FROM imb_banip where banip_id in (" . $str_ids . ");");
 			foreach($ban_rows as $key => $ban_row) {
 				imb_ban_approv($ban_row["banip_id"],$ban_row["banip_ipaddr"],$ban_row["banip_aplled"], 1);
 			}
 		} elseif ($_POST["drp_action"] == "4") { /* Запретить бан */
           $ban_rows=db_fetch_assoc("SELECT banip_id,banip_ipaddr,banip_aplled FROM imb_banip where banip_id in (" . $str_ids . ");");
 			foreach($ban_rows as $key => $ban_row) {
 				imb_ban_approv($ban_row["banip_id"],$ban_row["banip_ipaddr"],$ban_row["banip_aplled"], 0);
 			}
 		}elseif ($_POST["drp_action"] == "5") { /* Примеение бана*/
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
                 imb_ban_aplly($selected_items[$i], true);
             }		
 		}elseif ($_POST["drp_action"] == "6") { /* Отмена бана*/
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
                 imb_ban_aplly($selected_items[$i], false);
             }		
 				
 		}elseif ($_POST["drp_action"] == "7") { /* delete impb*/
            $imbp_rows=db_fetch_assoc("SELECT banip_id,banip_ipaddr,macip_id FROM imb_banip left join imb_macip on (imb_banip.banip_ipaddr=imb_macip.macip_index) where banip_id in (" . $str_ids . ");");
 			
			foreach($imbp_rows as $key => $imbp_row) {
				api_imp_delete_macip($imbp_row["macip_id"], false);
 			}			 
 		}
 		
 		 if (isset($_POST["save_config"])) { 
 				imp_save_config_main($save_data["tma_device_id"]);
 		}
 	
 	if (!is_error_message()) {
 		header("Location: impblinding_view.php");
 	}else{
 		header("Location: impblinding_view.php?action=" . $_POST["action"] . "&drp_action=" . $_POST["drp_action"] . "&post_error=" . serialize($selected_items));
 		$_REQUEST["selected_items"]="";
 	}
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = ""; $post_if_error = "";
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     if (!isset($_GET["post_error"])) { /*Если установлено это значение - значит страница перезагружаеться из-за ошибки при вводе, и данные нужно брать не из POST, а из спец. переменной.*/
 		while (list($var,$val) = each($_POST)) {
 	        if (ereg("^chk_([0-9]+)$", $var, $matches)) {
 	            /* ================= input validation ================= */
 	            input_validate_input_number($matches[1]);
 	            /* ==================================================== */
 
 	            $row_info = db_fetch_row("SELECT imb_banip.* FROM imb_banip WHERE imb_banip.banip_id=" . $matches[1]);
 				$row_list .= "<li> IP:" . $row_info["banip_ipaddr"] . "     TYPE:" . imp_convert_banned_type_2str($row_info["banip_type"]) . "     MESSAGE:" . $row_info["banip_message"] . "<br>";
 	            $row_array[$i] = $matches[1];
 				$row_ids = $row_ids . "'" . $matches[1] . "', ";
			$i++;
			}                                  
 	    }
 	}else{
 		$row_array=unserialize(stripslashes($_GET["post_error"]));
 		if (isset($row_array) && is_array($row_array) && (count($row_array) > 0)) {
 			foreach ($row_array as $row_id) {
 	            $row_info = db_fetch_row("SELECT imb_banip.* FROM imb_banip WHERE imb_banip.banip_id=" . $row_id);
 				$row_list .= "<li> IP:" . $row_info["banip_ipaddr"] . "     MESSAGE:" . $row_info["banip_message"] . "<br>";
 				$row_ids = $row_ids . "'" . $row_id . "', ";		
 			}
 		}
 	}
 	
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
 
     html_start_box("<strong>" . $banip_actions{(isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"])} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_view.php' method='post'>\n";
 	
 	// if ((!isset($_REQUEST["drp_action"])) && isset($_REQUEST["drp_action"])) {
 		// $_REQUEST["drp_action"] = $_REQUEST["drp_action"];
 	// }
 	
     if (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "1")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] == "1"))) {  /*создание записи бана */
 		html_start_box("Проверьте/заполните следующие поля для создания новой записи ", "98%", $colors["header"], "4", "center", "");
 
 		impblinding_view_calendar();
 	    html_header(array("IP-адресс","Тип бана","Применить ?", "Дата снятия", "Описание"));
 		
 			?>
 				<td><?php form_text_box("bna_ip",read_config_option("dimpb_default_ip_mask") , "", 15, 12, "text", 1) ;?></td>
 				<td><?php form_dropdown("bna_type",  $impblinding_imb_banip_type, "", "", "Other", "", "") ;?></td>
 				<td><?php form_dropdown("bna_aplly",  $impblinding_imb_yes_no, "", "", "", "", "1") ;?></td>
 				<td width='150' nowrap style='white-space: nowrap;'>
 					<input type='text' name='bna_expiration_date' id='bna_expiration_date' title='Graph Begin Timestamp' size='14' value='0000-00-00 00:00:00'>
 					&nbsp;<input type='image' src='<?php echo $config['url_path']; ?>images/calendar.gif' alt='Start date selector' title='Start date selector' border='0' align='absmiddle' onclick="return showCalendar('bna_expiration_date');">&nbsp;
 				</td>
 				<td><?php form_text_box("bna_message",  "", "", 200, 100, "", "") ;?></td>
 			</tr>
 			<?php
 			
 			
 		html_end_box();
     } elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "2")) { /*Удаление записи*/
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете удаление следующих записей банов ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n";
 
 
 	} elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "3")) { /*Разрешение записи*/
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете разрешение следующих записей банов ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n";
 	
 	}elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "4")) { /*Снятия разрешения */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете запрещение следующих записей банов ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n";
 	}elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "5")) {  /* Применение бана */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете немедленное применение банов по следующим записям ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n";
 	}elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "6")) {  /* Снятия  бана */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете отмену банов по следующим записям ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n";
 	}elseif ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "7")) {  /* Удаление связанной привязки */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете удаление связанных с банами привязок ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n";			 
      };     
 
 	
     if (!isset($row_array) && (((isset($_POST["drp_action"])) && ($_POST["drp_action"] != "1")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] != "1")))) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>Вы должны выбрать хотя-бы одну запись.</span></td></tr>\n";
         $save_html = "";
     }else{
 		if ($_REQUEST["drp_action"] == "5") {
 		print "<tr>
 				<td colspan='2' align='left' bgcolor='#eaeaea'>
 					<p><input type='checkbox' name='save_config'  >Сохранить конфигурацию ?</p>
 				</td>
 			</tr>\n";
 		}
		$save_html = "<input type='submit' name='Save' value='Save (сохранить)'>";
     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_banips'>
                 <input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
 				<input type='hidden' name='post_if_error' value='" . $post_if_error . "'>
                 <input type='hidden' name='drp_action' value='" . (isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"]) . "'>
                 <input type='submit' name='Cancel' value='Cancel (отмена)'>
                 $save_html
             </td>
         </tr>
         ";
 
     html_end_box();
 }










 
 function form_actions_net_del() {
     global $colors, $config, $net_del_actions ;
 
     /* if we are to save this form, instead of display it */
     if (isset($_POST["selected_items"])) {
        $selected_items = unserialize(stripslashes($_REQUEST["selected_items"]));
 		$str_ids = '';
    
 		
 		if ($_POST["drp_action"] == "1") { /* удаление записи ип-мак-порт */
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
                 api_dimpb_delete_net($selected_items[$i], false);
             }
             //header("Location: impblinding_view.php");
 
         } elseif ($_POST["drp_action"] == "2") { /* изменить запись */
            			//$save_data[$cur_net_id]["net_id"] = form_input_validate($_REQUEST["tma_ip"], "tma_ip", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
 		   for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 				$cur_net_id = $selected_items[$i];
 				$netdels[$cur_net_id]["net_id"] = $cur_net_id;
				$netdels[$cur_net_id]["net_ipaddr"] = form_input_validate(translate_ip_address($_REQUEST["nde_" . $cur_net_id . "_ipaddr"]), "nde_" . $cur_net_id . "_ipaddr", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false,3 );
				$netdels[$cur_net_id]["net_mask"] = form_input_validate(translate_ip_address($_REQUEST["nde_" . $cur_net_id . "_mask"]), "nde_" . $cur_net_id . "_mask", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false,3 );
 				$netdels[$cur_net_id]["net_description"] = form_input_validate($_REQUEST["nde_" . $cur_net_id . "_description"], "nde_" . $cur_net_id . "_description", "", true, 3);

 		   }
 	        if (!is_error_message()) {
 				if (sizeof($netdels) > 0) {
 					foreach ($netdels as $netdel) {	
 						imb_change_net_record($netdel["net_id"], $netdel["net_ipaddr"],$netdel["net_mask"],$netdel["net_description"], '1','0');
 					}
 				}
 			}
 		}elseif ($_POST["drp_action"] == "3") { /* создание привязки */
 			$save_data = array();
 			$save_data["nda_ipaddr"] = form_input_validate(translate_ip_address($_POST["nda_ipaddr"]), "nda_ipaddr", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
			$save_data["nda_mask"] = form_input_validate(translate_ip_address($_POST["nda_mask"]), "nda_mask", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
 			$save_data["nda_description"] = form_input_validate($_POST["nda_description"], "nda_description", "", false, 3) ;
 			if (!is_error_message()) {
 
 				$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 					imb_create_net_record($save_data["nda_ipaddr"] ,$save_data["nda_mask"], $save_data["nda_description"], '1','0');
 			}
 		}
 		
	
 	if (!is_error_message()) {
 		header("Location: impblinding_view.php");
 	}else{
 		header("Location: impblinding_view.php?action=" . $_POST["action"] . "&drp_action=" . $_POST["drp_action"] . "&post_error=" . serialize($selected_items));
 		$_REQUEST["selected_items"]="";
 	}
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = ""; $post_if_error = "";
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     if (!isset($_GET["post_error"])) { /*Если установлено это значение - значит страница перезагружаеться из-за ошибки при вводе, и данные нужно брать не из POST, а из спец. переменной.*/
 		while (list($var,$val) = each($_POST)) {
 	        if (ereg("^chk_([0-9]+)$", $var, $matches)) {
 	            /* ================= input validation ================= */
 	            input_validate_input_number($matches[1]);
 	            /* ==================================================== */
 
 	            $row_info = db_fetch_row("SELECT *, INET_NTOA(`net_ipaddr`) as anet_ipaddr , INET_NTOA(`net_mask`) as anet_mask FROM imb_auto_updated_nets WHERE `net_id`=" . $matches[1]);
 				$row_list .= "<li>" . $row_info["net_description"] . "     NET:" . $row_info["anet_ipaddr"] . "    MASK:" . $row_info["anet_mask"] . "<br>";
 	            $row_array[$i] = $matches[1];
 				$row_ids = $row_ids . "'" . $matches[1] . "', ";
			$i++;
			}                                  
 	    }
 	}else{
 		$row_array=unserialize(stripslashes($_GET["post_error"]));
 		if (isset($row_array) && is_array($row_array) && (count($row_array) > 0)) {
 			foreach ($row_array as $row_id) {
 	            $row_info = db_fetch_row("SELECT *, INET_NTOA(`net_ipaddr`) as anet_ipaddr , INET_NTOA(`net_mask`) as anet_mask FROM imb_auto_updated_nets WHERE `net_id`=" . $row_id);
 				$row_list .= "<li>" . $row_info["net_description"] . "     NET:" . $row_info["anet_ipaddr"] . "    MASK:" . $row_info["anet_mask"] . "<br>";
 				$row_ids = $row_ids . "'" . $row_id . "', ";		
 			}
 		}
 	}
 	
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
 
     html_start_box("<strong>" . $net_del_actions{(isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"])} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_view.php' method='post'>\n";
 	
 	// if ((!isset($_REQUEST["drp_action"])) && isset($_REQUEST["drp_action"])) {
 		// $_REQUEST["drp_action"] = $_REQUEST["drp_action"];
 	// }
 	
     if ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "1")) {  /* удаление записей */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете удаление следующих записей ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
     } elseif (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "2")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] == "2"))) { /*Изменение записи*/
 		$netdels_rows=db_fetch_assoc("SELECT *, INET_NTOA(`net_ipaddr`) as anet_ipaddr , INET_NTOA(`net_mask`) as anet_mask " .
			" FROM imb_auto_updated_nets " .
			" WHERE `net_id` in (" . $row_ids . ");");
		
 		html_start_box("Для изменения записи проверьте/измените следующие поля.", "98%", $colors["header"], "3", "center", "");
 
 	    html_header(array("","NET addres","NET mask", "Описание"));
 
 	    $i = 0;
 	    if (sizeof($netdels_rows) > 0) {
 	        foreach ($netdels_rows as $netdels_row) {
 				$netdel_id = $netdels_row["net_id"];
 	            form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	                ?>
 					<td><?php form_hidden_box("nde_" . $netdel_id . "_id", $netdel_id, "");?></td>
 					<td><?php form_text_box("nde_" . $netdel_id . "_ipaddr", $netdels_row["anet_ipaddr"], "", 15, 15, "text", 1) ;?></td>
					<td><?php form_text_box("nde_" . $netdel_id . "_mask", $netdels_row["anet_mask"], "", 15, 15, "text", 1) ;?></td>
 					<td><?php form_text_box("nde_" . $netdel_id . "_description", $netdels_row["net_description"], "", 250, 70, "text", 1) ;?></td>
 	            </tr>
 	            <?php
 	        }
 	    }
 		html_end_box(false);
 
 
 	}elseif (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "3")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] == "4"))) { /*Добавление привязки*/
 
 		html_start_box("Проверьте значения полей", "98%", $colors["header"], "4", "center", "");
 
 
 	    html_header(array("Адрес Сети","Маска", "Описание"));
 		
 			?>
 				<td><?php form_text_box("nda_ipaddr", "000.000.000.000", "", 15, 15, "text", 1) ;?></td>
				<td><?php form_text_box("nda_mask", "255.255.255.0", "", 15, 15, "text", 1) ;?></td>
				<td><?php form_text_box("nda_description", "", "", 250, 70, "text", 1) ;?></td>
		
 			</tr>
 			<?php
 			
 			
 		html_end_box();
 	};
 
 	
     if (!isset($row_array) && (((isset($_POST["drp_action"])) && ($_POST["drp_action"] != "3")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] != "3")))) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>Вы должны выбрать хотя-бы одну запись.</span></td></tr>\n";
         $save_html = "";
     }else{
		$save_html = "<input type='submit' name='Save' value='Save (сохранить)'>";
     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_net_del'>
                 <input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
 				 <input type='hidden' name='post_if_error' value='" . $post_if_error . "'>
                 <input type='hidden' name='drp_action' value='" . (isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"]) . "'>
                 <input type='submit' name='Cancel' value='Cancel (отмена)'>
                 $save_html
             </td>
         </tr>
         ";
 
     html_end_box();
 }
 
 

 
 
 
 
 
 
 
 
  function form_actions_net_add() {
     global $colors, $config, $net_add_actions, $impblinding_imp_net_ttl ;
 
     /* if we are to save this form, instead of display it */
     if (isset($_POST["selected_items"])) {
        $selected_items = unserialize(stripslashes($_REQUEST["selected_items"]));
 		$str_ids = '';
    
 		
 		if ($_POST["drp_action"] == "1") { /* удаление записи ип-мак-порт */
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
                 api_dimpb_delete_net($selected_items[$i], false);
             }
             //header("Location: impblinding_view.php");
 
         } elseif ($_POST["drp_action"] == "2") { /* изменить запись */
            			//$save_data[$cur_net_id]["net_id"] = form_input_validate($_REQUEST["tma_ip"], "tma_ip", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
 		   for ($i=0;($i<count($selected_items));$i++) {
 				/* ================= input validation ================= */
 				input_validate_input_number($selected_items[$i]);
 				/* ==================================================== */
 				$cur_net_id = $selected_items[$i];
 				$netadds[$cur_net_id]["net_id"] = $cur_net_id;
				$netadds[$cur_net_id]["net_ipaddr"] = form_input_validate(translate_ip_address($_REQUEST["nae_" . $cur_net_id . "_ipaddr"]), "nae_" . $cur_net_id . "_ipaddr", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false,3 );
				$netadds[$cur_net_id]["net_device_id"] = form_input_validate($_REQUEST["nae_" . $cur_net_id . "_device_id"], "nae_" . $cur_net_id . "_device_id", "^[0-9]{1,}$", false,3 );
				$netadds[$cur_net_id]["net_ttl"] = form_input_validate($_REQUEST["nae_" . $cur_net_id . "_ttl"], "nae_" . $cur_net_id . "_ttl", "^[0-9]{1,}$", false,3 );
 				$netadds[$cur_net_id]["net_description"] = form_input_validate($_REQUEST["nae_" . $cur_net_id . "_description"], "nae_" . $cur_net_id . "_description", "", true, 3);

 		   }
 	        if (!is_error_message()) {
 				if (sizeof($netadds) > 0) {
 					foreach ($netadds as $netadd) {	
 						imb_change_net_record($netadd["net_id"], $netadd["net_ipaddr"],"255.255.255.255",$netadd["net_description"], '2',$netadd["net_device_id"],$netadd["net_ttl"]);
 					}
 				}
 			}
 		}elseif ($_POST["drp_action"] == "3") { /* создание привязки */
 			$save_data = array();
 			$save_data["naa_ipaddr"] = form_input_validate(translate_ip_address($_POST["naa_ipaddr"]), "naa_ipaddr", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
			$save_data["naa_device_id"] = form_input_validate($_POST["naa_device_id"], "naa_device_id", "^[0-9]{1,}$", false, 3);
			$save_data["naa_ttl"] = form_input_validate($_POST["naa_ttl"], "naa_ttl", "^[0-9]{1,}$", false, 3);
 			$save_data["naa_description"] = form_input_validate($_POST["naa_description"], "naa_description", "", false, 3) ;
 			if (!is_error_message()) {
 
 				$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 					imb_create_net_record($save_data["naa_ipaddr"] ,"255.255.255.255", $save_data["naa_description"], '2',$save_data["naa_device_id"],$save_data["naa_ttl"]);
 			}
 		}
 		
	
 	if (!is_error_message()) {
 		header("Location: impblinding_view.php");
 	}else{
 		header("Location: impblinding_view.php?action=" . $_POST["action"] . "&drp_action=" . $_POST["drp_action"] . "&post_error=" . serialize($selected_items));
 		$_REQUEST["selected_items"]="";
 	}
 		
         exit;
     }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = ""; $post_if_error = "";
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     if (!isset($_GET["post_error"])) { /*Если установлено это значение - значит страница перезагружаеться из-за ошибки при вводе, и данные нужно брать не из POST, а из спец. переменной.*/
 		while (list($var,$val) = each($_POST)) {
 	        if (ereg("^chk_([0-9]+)$", $var, $matches)) {
 	            /* ================= input validation ================= */
 	            input_validate_input_number($matches[1]);
 	            /* ==================================================== */
 
 	            $row_info = db_fetch_row("SELECT *, INET_NTOA(`net_ipaddr`) as anet_ipaddr , INET_NTOA(`net_mask`) as anet_mask FROM imb_auto_updated_nets WHERE `net_id`=" . $matches[1]);
 				$row_list .= "<li>" . $row_info["net_description"] . "     NET:" . $row_info["anet_ipaddr"] . "    MASK:" . $row_info["anet_mask"] . "<br>";
 	            $row_array[$i] = $matches[1];
 				$row_ids = $row_ids . "'" . $matches[1] . "', ";
 	        $i++;
			}                                  
 	    }
 	}else{
 		$row_array=unserialize(stripslashes($_GET["post_error"]));
 		if (isset($row_array) && is_array($row_array) && (count($row_array) > 0)) {
 			foreach ($row_array as $row_id) {
 	            $row_info = db_fetch_row("SELECT *, INET_NTOA(`net_ipaddr`) as anet_ipaddr , INET_NTOA(`net_mask`) as anet_mask FROM imb_auto_updated_nets WHERE `net_id`=" . $row_id);
 				$row_list .= "<li>" . $row_info["net_description"] . "     NET:" . $row_info["anet_ipaddr"] . "    MASK:" . $row_info["anet_mask"] . "<br>";
 				$row_ids = $row_ids . "'" . $row_id . "', ";		
 			}
 		}
 	}
 	
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 
     include_once($config['base_path'] . "/plugins/impblinding/include/top_impblinding_header.php");
 
     html_start_box("<strong>" . $net_add_actions{(isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"])} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_view.php' method='post'>\n";
 	
 	// if ((!isset($_REQUEST["drp_action"])) && isset($_REQUEST["drp_action"])) {
 		// $_REQUEST["drp_action"] = $_REQUEST["drp_action"];
 	// }
 	
     if ((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "1")) {  /* удаление записей */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете удаление следующих записей ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
     } elseif (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "2")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] == "2"))) { /*Изменение записи*/
 		$netadds_rows=db_fetch_assoc("SELECT *, INET_NTOA(`net_ipaddr`) as anet_ipaddr , INET_NTOA(`net_mask`) as anet_mask " .
			" FROM imb_auto_updated_nets " .
			" WHERE `net_id` in (" . $row_ids . ");");
		
 		html_start_box("Для изменения записи проверьте/измените следующие поля.", "98%", $colors["header"], "3", "center", "");
 
 	    html_header(array("","ИП адрес","Устройство", "Ограничить<br>по времени", "Описание"));
 
 	    $i = 0;
 	    if (sizeof($netadds_rows) > 0) {
 	        foreach ($netadds_rows as $netadds_row) {
 				$netadd_id = $netadds_row["net_id"];
 	            form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	                ?>
 					<td><?php form_hidden_box("nae_" . $netadd_id . "_id", $netadd_id, "");?></td>
 					<td><?php form_text_box("nae_" . $netadd_id . "_ipaddr", $netadds_row["anet_ipaddr"], "", 15, 15, "text", 1) ;?></td>
					<td><?php form_dropdown("nae_" . $netadd_id . "_device_id", dimpb_add_row_any("select device_id as id, description as name from imb_devices order by INET_ATON(hostname) ASC"), "name", "id", $netadds_row["net_device_id"],"", "") ;?></td>
					<td><?php form_dropdown("nae_" . $netadd_id . "_ttl",  $impblinding_imp_net_ttl, "", "", $netadds_row["net_ttl"], "", "") ;?></td>				
 					<td><?php form_text_box("nae_" . $netadd_id . "_description", $netadds_row["net_description"], "", 250, 70, "text", 1) ;?></td>
 	            </tr>
 	            <?php
 	        }
 	    }
 		html_end_box(false);
 
 
 	}elseif (((isset($_POST["drp_action"])) && ($_POST["drp_action"] == "3")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] == "3"))) { /*Добавление привязки*/
 
 		html_start_box("Проверьте значения полей", "98%", $colors["header"], "5", "center", "");
 
 
 	    html_header(array("ИП адрес","Устройство", "Ограничить<br>по времени", "Описание"));
 		
 			?>
 				<td><?php form_text_box("naa_ipaddr",read_config_option("dimpb_default_ip_mask"), "", 15, 15, "text", 1) ;?></td>
				<td><?php form_dropdown("naa_device_id", dimpb_add_row_any("select device_id as id, description as name from imb_devices  order by INET_ATON(hostname) ASC"), "name", "id", "","", "") ;?></td>
				<td><?php form_dropdown("naa_ttl",  $impblinding_imp_net_ttl, "", "", "0", "", "") ;?></td>				
				<td><?php form_text_box("naa_description", "", "", 250, 70, "text", 1) ;?></td>
		
 			</tr>
 			<?php
 			
 			
 		html_end_box();
 	};
 
 	
     if (!isset($row_array) && (((isset($_POST["drp_action"])) && ($_POST["drp_action"] != "3")) || ((isset($_GET["post_error"])) && (isset($_GET["drp_action"])) && ($_GET["drp_action"] != "3")))) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>Вы должны выбрать хотя-бы одну запись.</span></td></tr>\n";
         $save_html = "";
     }else{
		$save_html = "<input type='submit' name='Save' value='Save (сохранить)'>";
     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_net_add'>
                 <input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
 				 <input type='hidden' name='post_if_error' value='" . $post_if_error . "'>
                 <input type='hidden' name='drp_action' value='" . (isset($_POST["drp_action"]) ? $_POST["drp_action"] : $_GET["drp_action"]) . "'>
                 <input type='submit' name='Cancel' value='Cancel (отмена)'>
                 $save_html
             </td>
         </tr>
         ";
 
     html_end_box();
 }
 


 
 ?>
