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
 if(file_exists($config["base_path"] . '/plugins/bdcom/lib/bdcom_functions_ext.php')) {
	include_once($config["base_path"] . '/plugins/bdcom/lib/bdcom_functions_ext.php');
 }
 //ini_set('memory_limit', '256M');

 $title = __('IMPB - Info');
 
/* check actions */
switch (get_request_var('action')) {
	default:
		impb_redirect();
		general_header();
		impb_view_info();
		bottom_footer();
		break;
}

 

 
function impb_view_get_info_macips_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
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
                 "imb_macip.macip_port_list LIKE '%" . get_request_var('m_filter') . "%')";
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
	
    $query_string = "SELECT  imb_devices.description, imb_devices.hostname, imb_devices.device_id, imb_devices.last_rundate,
             imb_macip.macip_id, imb_macip.macip_macaddr, imb_macip.macip_ipaddr, imb_macip.macip_banned, imb_macip.macip_port_list, imb_macip.macip_port_view, imb_macip.macip_imb_status, imb_macip.macip_mode, macip_online, macip_first_scan_date, macip_lastchange_date, imb_macip.macip_scan_date, macip_count_scan, 
             lbs.f_flat
			 FROM  imb_macip
             LEFT JOIN imb_devices ON imb_macip.device_id = imb_devices.device_id
			 LEFT JOIN (SELECT l.segment,  v.*  FROM lb_staff l left JOIN lb_vgroups_s v ON l.vg_id = v.vg_id WHERE v.`archive`=0) lbs ON INET_ATON(imb_macip.macip_ipaddr) = lbs.segment
 			$sql_where
 			ORDER BY " . $sortby . " " . get_request_var('sort_direction') .
			$sql_limit;
             
                                                                                  

    return db_fetch_assoc($query_string);
     
 }
 function impb_view_get_info_onu_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
    
	if (api_plugin_installed('bdcom')) {
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
					 $sql_where .= " plugin_bdcom_onu.onu_macaddr='" . get_request_var('mac_filter') . "'";
					 break;
				 case "3": /* contains */
					 $sql_where .= " plugin_bdcom_onu.onu_macaddr LIKE '%%" . get_request_var('mac_filter') . "%%'";
					 break;
				 case "4": /* begins with */
					 $sql_where .= " plugin_bdcom_onu.onu_macaddr LIKE '" . get_request_var('mac_filter') . "%%'";
					 break;
				 case "5": /* does not contain */
					 $sql_where .= " plugin_bdcom_onu.onu_macaddr NOT LIKE '" . get_request_var('mac_filter') . "%%'";
					 break;
				 case "6": /* does not begin with */
					 $sql_where .= " plugin_bdcom_onu.onu_macaddr NOT LIKE '" . get_request_var('mac_filter') . "%%'";
			 }
		}
	
		if ((strlen(get_request_var('ip_filter')) > 0)||(get_request_var('ip_filter_type_id') > 5)) {
			 if (strlen($sql_where) > 0) {
				 $sql_where .= " AND ";
			 }else{
				 $sql_where = " WHERE ";
			 }
	 
			switch (get_request_var('ip_filter_type_id')) {
				 case "1": // do not filter 
					 break;
				 case "2": // matches 
					 $sql_where .= " plugin_bdcom_onu.onu_ipaddr='" . get_request_var('ip_filter') . "'";
					 break;
				 case "3": // contains 
					 $sql_where .= " plugin_bdcom_onu.onu_ipaddr LIKE '%%" . get_request_var('ip_filter') . "%%'";
					 break;
				 case "4": // begins with 
					 $sql_where .= " plugin_bdcom_onu.onu_ipaddr LIKE '" . get_request_var('ip_filter') . "%%'";
					 break;
				 case "5": // does not contain 
					 $sql_where .= " plugin_bdcom_onu.onu_ipaddr NOT LIKE '" . get_request_var('ip_filter') . "%%'";
					 break;
				 case "6": // does not begin with 
					 $sql_where .= " plugin_bdcom_onu.onu_ipaddr NOT LIKE '" . get_request_var('ip_filter') . "%%'";
					 break;
				 case "7": // is null 
					 $sql_where .= " plugin_bdcom_onu.onu_ipaddr = ''";
					 break;
				 case "8": // is not null 
					 $sql_where .= " plugin_bdcom_onu.onu_ipaddr != ''";
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
 				$sql_where .= " FIND_IN_SET('" . get_request_var('port_filter') . "',`macio_port_list`)";
                 break;
             case "3": /* не состоит */
 				$sql_where .= " NOT FIND_IN_SET('" . get_request_var('port_filter') . "',`macio_port_list`)";
 
         }
     }	
 	
     if (strlen(get_request_var('filter'))) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
             $sql_where .= " (plugin_bdcom_onu.onu_macaddr LIKE '%" . get_request_var('filter') . "%' OR " .
                 "plugin_bdcom_onu.onu_name LIKE '%" . get_request_var('filter') . "%' OR " .
				"plugin_bdcom_onu.onu_descr LIKE '%" . get_request_var('filter') . "%' OR " .
				"plugin_bdcom_onu.onu_agrement LIKE '%" . get_request_var('filter') . "%')";
    }
 
	
    if (!(get_request_var('device_id') == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " plugin_bdcom_onu.device_id=" . get_request_var('device_id');
    }

 	$sortby = get_request_var('sort_column');
 	
	$sql_order = ' ORDER BY ' . $sortby;
	
	if ($sortby=="onu_index") {
		$sql_order = ' ORDER BY INET_ATON(onu_ipaddr) ';
 	}
 	if ($sortby=="macip_ipaddr") {
		$sql_order = ' ORDER BY INET_ATON(onu_ipaddr) ';
 	}	
 	if ($sortby=="f_flat") {
 		$sortby = "ABS(f_flat)";
		$sql_order = ' ORDER BY ABS(f_flat) ';
 	}	


	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}	
 	
 		$query_string = "SELECT 
					if (lbv.blocked is null, 'ip_noo' ,CONCAT('ipb_',lbv.blocked)) as sig,  If (lbs.segment is null, 'IP нигде не зарегистрирован', concat ('[', lbv.ag_num , '], ' , 
					CASE lbv.blocked  
					WHEN 0 THEN CONCAT('Баланс = ',ROUND(lbv.balance,2))  
					WHEN 1 THEN CONCAT('Минусовой баланс = ',ROUND(lbv.balance/100,2), ' c ', date(lbv.block_date))  
					WHEN 2 THEN CONCAT('Блок пользователя c ', date(lbv.acc_ondate))  
					WHEN 3 THEN CONCAT('Админ Блок c ', date(lbv.acc_ondate)) END )) as sig2,  
					f_addr, h.id as cid,   plugin_bdcom_devices.description, plugin_bdcom_devices.hostname, 
					plugin_bdcom_devices.last_rundate,            
					plugin_bdcom_onu.device_id, plugin_bdcom_onu.onu_id, plugin_bdcom_onu.onu_txpower, plugin_bdcom_onu.onu_rxpower, plugin_bdcom_onu.onu_distance, if (onu_done_view_count > 8 , '' , plugin_bdcom_onu.onu_done_reason) as onu_done_reason,
					plugin_bdcom_onu.onu_macaddr, plugin_bdcom_onu.onu_ipaddr, plugin_bdcom_onu.onu_name, plugin_bdcom_onu.onu_descr, plugin_bdcom_onu.onu_operstatus, plugin_bdcom_onu.onu_adminstatus, plugin_bdcom_onu.onu_dereg_status, 
					onu_done_view_count, onu_online, onu_first_scan_date, onu_lastchange_date, plugin_bdcom_onu.onu_scan_date, 
					lbv.f_flat, lbv.equipm_pon,  if(gl_ip.id is null,'0',gl_ip.id) as ip_local_graph_id,             
					lbv.login, plugin_bdcom_epons.epon_name, plugin_bdcom_epons.epon_index,
					h.id
				FROM  plugin_bdcom_onu             
				left JOIN plugin_bdcom_epons             ON (plugin_bdcom_onu.onu_bindepon = plugin_bdcom_epons.epon_index and plugin_bdcom_onu.device_id = plugin_bdcom_epons.device_id )
				left JOIN plugin_bdcom_devices             ON plugin_bdcom_onu.device_id = plugin_bdcom_devices.device_id    
				left JOIN (SELECT * FROM lb_vgroups_s WHERE lb_vgroups_s.id is null or lb_vgroups_s.id =1)  lbv  ON plugin_bdcom_onu.onu_agrm_id = lbv.agrm_id	
				LEFT JOIN lb_staff lbs ON  lbv.vg_id= lbs.vg_id   
				LEFT JOIN graph_local gl_ip ON gl_ip.snmp_index=inet_aton(plugin_bdcom_onu.onu_ipaddr) and gl_ip.graph_template_id=43
				left JOIN host   h          ON plugin_bdcom_devices.hostname = h.hostname  		
 			$sql_where
			$sql_order
			$sql_limit";
		
		
         return db_fetch_assoc($query_string);
    } 
}
 
 
function impb_view_get_info_bmacips_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
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
 				$sql_where .= " imb_blmacs.blmac_port='" . get_request_var('port_filter') . "'";
                 break;
             case "3": /* не состоит */
 				$sql_where .= " imb_blmacs.blmac_port NOT LIKE '" . get_request_var('port_filter') . "'";
 
         }
     }
     
     
     if (!(get_request_var('device_id') == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         $sql_where .= " imb_blmacs.device_id=" . get_request_var('device_id');
     }
    
	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}	
	$sql_order = ' ORDER BY blmac_id ASC ';
	
	$query_string = "SELECT imb_devices.description, 
 			imb_devices.hostname, 
 			imb_blmacs.*, imb_temp_blmacinfo.* 
            FROM imb_blmacs left join imb_devices on imb_blmacs.device_id=imb_devices.device_id left join
            imb_temp_blmacinfo on imb_blmacs.blmac_id=imb_temp_blmacinfo.blmacinfo_info_id
		$sql_where
		$sql_order
		$sql_limit";
               
    return db_fetch_assoc($query_string);
     
 }

 
function impb_get_info_recent_macips_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
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
                 $sql_where .= " mac_track_ports.mac_address='" . get_request_var('mac_filter') . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " mac_track_ports.mac_address LIKE '%%" . get_request_var('mac_filter') . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " mac_track_ports.mac_address LIKE '" . get_request_var('mac_filter') . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " mac_track_ports.mac_address NOT LIKE '" . get_request_var('mac_filter') . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " mac_track_ports.mac_address NOT LIKE '" . get_request_var('mac_filter') . "%%'";
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
                 $sql_where .= " mac_track_ports.ip_address='" . get_request_var('ip_filter') . "'";
                 break;
             case "3": /* contains */
                 $sql_where .= " mac_track_ports.ip_address LIKE '%%" . get_request_var('ip_filter') . "%%'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " mac_track_ports.ip_address LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " mac_track_ports.ip_address NOT LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " mac_track_ports.ip_address NOT LIKE '" . get_request_var('ip_filter') . "%%'";
                 break;
             case "7": /* is null */
                 $sql_where .= " mac_track_ports.ip_address = ''";
                 break;
             case "8": /* is not null */
                 $sql_where .= " mac_track_ports.ip_address != ''";
         }
     }
 
     if (!(get_request_var('device_id') == "-1")) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 		$imb_mac_track_ids = db_fetch_cell("SELECT mac_track_devices.device_id FROM mac_track_devices " .
 											" LEFT JOIN imb_devices " .
 											" ON (mac_track_devices.hostname = imb_devices.hostname) " .
 											" where imb_devices.device_id = '" . get_request_var('device_id') . "';");
        $sql_where .= " mac_track_ports.device_id='" . $imb_mac_track_ids . "'";
     }
	
	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}		
	$sql_order = ' ORDER BY row_id ASC ';
 
     //***************************************************************************
         $query_string = "SELECT
             row_id, site_name, description, hostname, mac_address, vendor_name, ip_address, dns_hostname, port_number,
             port_name, vlan_id, vlan_name, date_last as max_scan_date, count_rec, active_last
             FROM imb_mactrack_recent_ports
             LEFT JOIN mac_track_sites ON (imb_mactrack_recent_ports.site_id = mac_track_sites.site_id) " .
			str_replace("mac_track_ports", "imb_mactrack_recent_ports", $sql_where) . "
			$sql_order
			$sql_limit";

     if (strlen($sql_where) == 0) {
         return array();
     }else{
         return db_fetch_assoc($query_string);
     }
     
 }
 


function impb_info_request_validation() {
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
			
	);

	validate_store_request_vars($filters, 'sess_impb_info');
	/* ================= input validation ================= */
}

 

function impb_view_info() {
     global $title, $report, $impb_search_types, $impb_port_search_types, $rows_selector, $config, $macips_actions;
 
   
	impb_info_request_validation();

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
	impb_info_filter();
	html_end_box(); 
 
    
// ---------------------------------- MacIP INFO	
	
	$sql_where = "";

 	$mac_font_size=read_config_option("dimpb_mac_addr_font_size");
 	
    $macips_results = impb_view_get_info_macips_records($sql_where, true, $rows);
    $rows_query_string = "SELECT
             COUNT(imb_macip.device_id)
             FROM imb_macip
             $sql_where";
 
    $total_rows = db_fetch_cell($rows_query_string);	
	
	if (sizeof($macips_results) == 1) {
		$ip_full_info = db_fetch_assoc("SELECT login, blocked,balance, ag_num, f_addr, f_flat, equipm_rtr, mobile, l.vg_id, macip_ipaddr FROM imb_macip i " .
				" LEFT JOIN lb_staff l ON (l.ip=i.macip_ipaddr ) " .
				" LEFT JOIN lb_vgroups_s lv ON (lv.vg_id=l.vg_id) " .
				" WHERE macip_id="  . $macips_results[0]["macip_id"] . ";");
			if (sizeof($ip_full_info) == 1) {
				html_start_box("Информация по IP", "98%", '', "1", "center", "");
				?>
				<tr><td><?php print ("VG = " . $ip_full_info[0]["vg_id"] . "\n");?></td></tr>
				<tr><td><?php print ("IP = " . $ip_full_info[0]["macip_ipaddr"] . "\n");?></td></tr>
				<tr><td><?php print ("Login = " . $ip_full_info[0]["login"] . "\n");?></td></tr>
				<tr><td><?php print ("Status = " . $ip_full_info[0]["blocked"] . "\n");?></td></tr>
				<tr><td><?php print ("AG = " . $ip_full_info[0]["ag_num"] . "\n");?></td></tr>
				<tr><td><?php print ("Balance = " . $ip_full_info[0]["balance"] );?></td></tr>
				<tr><td><?php print ("Addr = " . $ip_full_info[0]["f_addr"] . "\n");?></td></tr>
				<tr><td><?php print ("equipm_rtr = " . $ip_full_info[0]["equipm_rtr"] . "\n");?></td></tr>
				<tr><td><?php print ("mobile = " . $ip_full_info[0]["mobile"] . "\n");?></td></tr>
				<?php

		
				html_end_box();
			}
	}
 
 
 	
	$nav = html_nav_bar('impb_view_info.php?report=info', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 12, __('Bindings'), 'page', 'main');

	//print $nav;

	html_start_box('Show IMP Bindings', '100%', '', '3', 'center', '');

	$display_text = array(
		"macip_id" => array(__('ID'), 'ASC'),
		'description'      => array(__('Device'), 'ASC'),
		'hostname'        => array(__('IP(имя)'), 'ASC'),
		'macip_ipaddr'      => array(__('IP Address'), 'ASC'),
		'macip_macaddr'      => array(__('MAC Address'), 'ASC'),
		'f_flat'      => array(__('Komn'), 'ASC'),
		'macip_port_view'        => array(__('Port<br>List'), 'ASC'),
		'macip_imb_status'      => array(__('Record<br>status'), 'ASC'),
		'macip_mode'     => array(__('Mode'), 'ASC'),
		'macip_lastchange_date'     => array(__('Дата<br>Изменения'), 'ASC'),
		'macip_scan_date'      => array(__('Scan Date'), 'DESC'));

	html_header_sort($display_text, get_request_var('sort_column'), get_request_var('sort_direction'));
	
    $i = 0;
     if (sizeof($macips_results) > 0) {
         foreach ($macips_results as $macips_result) {
             $scan_date = $macips_result["macip_scan_date"];
 
             form_alternate_row();
             ?>
            <td >
 				<a class="linkEditMain" href="impb_view_bindings.php?report=bindings&device_id=%20<?php print $macips_result["device_id"];?>&rows=-1&mac_filter_type_id=1&mac_filter=&filter=&ip_filter_type_id=2&ip_filter=<?php print $macips_result["macip_ipaddr"];?>&port_filter_type_id=1&port_filter="><font face="Courier"><?php print  $macips_result["macip_id"];?></font></a>
            </td >			
            <td><?php print $macips_result["description"];?></td>
            <td><?php print $macips_result["hostname"];?></td>
            <td >
                 <a class="linkEditMain" href="impb_view_info.php?report=info&device_id=-1&ip_filter_type_id=2&ip_filter=<?php print $macips_result["macip_ipaddr"];?>&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows_selector=-1&filter=&page=1&report=info&x=23&y=10"><?php print preg_replace("/(" . preg_quote(get_request_var('ip_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_ipaddr"]);?></a>
            </td >            
 			<td >
 				<a class="linkEditMain" href="impb_view_info.php?report=info&device_id=-1&ip_filter_type_id=8&ip_filter=&mac_filter_type_id=2&mac_filter=<?php print $macips_result["macip_macaddr"];?>&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=info&x=14&y=6"><font size="<?php print $mac_font_size; ?>" face="Courier"><?php print strtoupper(preg_replace("/(" . preg_quote(get_request_var('mac_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $macips_result["macip_macaddr"]));?></font></a>
 			</td >	
 			<td><?php print $macips_result["f_flat"];?></td>
			<td><?php print filter_value($macips_result["macip_port_view"], get_request_var('filter'));?>
            <td><?php print imp_convert_macip_state_2str($macips_result["macip_imb_status"]);?></td>
 			<td><?php print $macips_result["macip_mode"];?></td>
			<td><?php print $macips_result["macip_lastchange_date"];?></td>
            <td><?php print filter_value($macips_result["macip_scan_date"], get_request_var('filter'));?>
 			</tr>
             <?php
         }
 
         /* put the nav bar on the bottom as well */
         print $nav;
     }else{
         print "<tr><td><em>No IMP Bindings Results</em></td></tr>";
     }
     html_end_box(false);
//=========================================================
	 

	 
 // ---------------------------------- ONU INFO
 	if 	(api_plugin_installed('bdcom')){
	
    $sql_where="";
    $onus_results = impb_view_get_info_onu_records($sql_where, TRUE, $rows);
	$rows_query_string = "SELECT
			 COUNT(plugin_bdcom_onu.device_id)
			 FROM plugin_bdcom_onu
			 $sql_where";
 
	$total_rows = db_fetch_cell($rows_query_string);
	
	
	if (count($onus_results) > 0) {
		print "<br><br>";
		
		
		$nav1 = html_nav_bar('impb_view_info.php?report=info', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 12, __('ONU'), 'page', 'main');	 
		
		html_start_box('Поиск по записям BDCOM', '100%', '', '3', 'center', '');

		
		$display_text = array(
			"onu_id" => array(__('ID'), 'ASC'),
			"description" => array(__('Network<br>Device'), 'ASC'),
			"hostname" => array(__('Network<br>Hostname'), 'ASC'),
			"epon" => array(__('epon<br>name'), "DESC"),
			"onu_ipaddr" => array(__('Abon<br>IP Address'), 'ASC'),
			"onu_done_reason" => array(__(''), 'ASC'),
			"onu_macaddr" => array(__('ONU<br>MAC Address'), 'ASC'),
			"onu_name" => array(__('ONU<br>NAME'), 'ASC'),
			"onu_descr" => array(__('ONU<br>Descr'), 'ASC'),
			"f_flat" => array(__('Komn'), "DESC"),
			"dist" => array(__('dist'), "DESC"),
			"power" => array(__('power'), "DESC"),
			"status" => array(__('ONU<br>status'), 'ASC'),
			"onu_lastchange_date" => array(__('Дата<br>Создания'), 'ASC'),
			"onu_scan_date" => array(__('Last<br>Scan Date'), "DESC"));
			 
		html_header_sort($display_text, get_request_var('sort_column'), get_request_var('sort_direction'));

		$i = 0;
		$webroot = $config['url_path'] . 'plugins/bdcom/'; 
			foreach ($onus_results as $onu) {
				$scan_date = $onu["onu_scan_date"];
				form_alternate_row();
	 
             ?>
			 <td >
 				<a href='<?php print htmlspecialchars($webroot . 'bdcom_view.php?report=info&o_device_id=' . $onu['device_id'] . '&o_ip_filter=' . $onu['onu_ipaddr']);?>' ><?php print $onu["onu_id"]?></a>
             </td>
				<td class='hyperLink'>
					<?php print filter_value($onu["description"], get_request_var('filter'));?>
				</td>
				<td><?php print $onu["hostname"];?></td>				
				<td><?php print filter_value($onu['epon_name'], get_request_var('filter'));?>
				<td><?php print filter_value($onu['onu_ipaddr'], get_request_var('ip_filter'));?>
				<td><?php print $onu['onu_done_reason'];?></td>
				<td><?php print filter_value($onu['onu_macaddr'], get_request_var('mac_filter'));?>
				<td><?php print filter_value($onu['onu_name'], get_request_var('filter'));?>
				<td><?php print filter_value($onu['onu_name'], get_request_var('filter'));?>
				<td><?php print filter_value($onu['f_flat'], get_request_var('filter'));?>
				<td><?php print $onu['onu_distance'];?></td>
				<td><?php print round($onu["onu_txpower"]*0.1,1) . "/" . round($onu["onu_rxpower"]*0.1,1);?></td>
				<td><?php print bdcom_convert_status_dereg_2str($onu["onu_operstatus"], $onu["onu_dereg_status"])  . " [" . bdcom_convert_status_2str($onu["onu_adminstatus"]) . "]";?></td>
				<td><?php print $onu['onu_first_scan_date'];?></td>
				<td><?php print $onu['onu_scan_date'];?></td>

				<?php
			}
	 
			 /* put the nav bar on the bottom as well */
		print $nav1;
		html_end_box(false);	 
	}
	 
	 
	 
	} 
	 
	 
	 
	 
 // ---------------------------------- BlockMac INFO
 print "<br><br>";
 
    $sql_where="";
    $bmacs_results = impb_view_get_info_bmacips_records($sql_where, true, $rows);
 
    $rows_query_string = "SELECT
             COUNT(imb_blmacs.device_id)
             FROM imb_blmacs
			 left join imb_temp_blmacinfo on imb_blmacs.blmac_id=imb_temp_blmacinfo.blmacinfo_info_id
             $sql_where";
 
    $total_rows = db_fetch_cell($rows_query_string);

	$nav = html_nav_bar('impb_view_info.php?report=info', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 13, __('Blocks'), 'page', 'main');

	print $nav;

	html_start_box('Show IPMB Blocks', '100%', '', '3', 'center', '');

	$display_text = array(
		'blmac_id'=> array(__('ID'), 'ASC'),
		'description'      => array(__('Device'), 'ASC'),
		'hostname'        => array(__('IP(имя)'), 'ASC'),
		'blmac_macaddr'      => array(__('MAC Address'), 'ASC'),
		'blmacinfo_banned' => array(__('BANNED'), 'ASC'),
		'blmac_port' => array(__('Blocked<br>On port'),"DESC"),
		'blmac_type' => array(__('Block<br>Type'),"DESC"),
		'blmac_vid' => array(__('Blocked<br>Vlan ID'),"DESC"),
		'blmacinfo_cor_ip' => array(__('Correct<br>IP'),"DESC"),
		'blmacinfo_cor_portlist' => array(__('Correct<br>Port'),"DESC"),
		'blmac_first_scan_date' => array(__('Время<br>Блока'),'DESC'),
		'blmac_scan_date' => array(__('Last<br>Scan Date'), 'DESC'));		
	
    $i = 0;
    if (sizeof($bmacs_results) > 0) {
         foreach ($bmacs_results as $bmacs_result) {
 
             form_alternate_row();
             ?>
             <td >
                 <a class="linkEditMain" href="impb_view_blmacs.php?report=blmacs&device_id=%20<?php print $bmacs_result["device_id"];?>&rows=-1&mac_filter_type_id=1&mac_filter=&filter=&ip_filter_type_id=1&ip_filter="><font face="Courier"><?php print  $bmacs_result["blmac_id"];?></font></a>
             </td >
 			<td><?php print $bmacs_result["description"];?></td>
             <td><?php print $bmacs_result["hostname"];?></td>
             <td >
                 <a class="linkEditMain" href="impb_view_info.php?report=info&device_id=-1&ip_filter_type_id=8&ip_filter=&mac_filter_type_id=2&mac_filter=<?php print $bmacs_result["blmac_macaddr"];?>&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=info&x=14&y=6"><font size="<?php print $mac_font_size; ?>" face="Courier"><?php print strtoupper(preg_replace("/(" . preg_quote(get_request_var('mac_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmac_macaddr"]));?></font></a>
             </td >	
 			<td><?php print imp_convert_banned_state_2str($bmacs_result["blmacinfo_banned"]);?></td>
			 <td><?php print filter_value($bmacs_result['blmac_port'], get_request_var('filter'));?>
             <td><?php print imp_convert_blmac_state_2str($bmacs_result["blmac_type"]);?></td>
             <td><?php print $bmacs_result["blmac_vid"];?></td> 
             <td>
 				<a class="linkEditMain" href="impb_view_info.php?report=info&device_id=-1&ip_filter_type_id=2&ip_filter=<?php print $bmacs_result["blmacinfo_cor_ip"];?>&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=info&x=14&y=6"><font size="<?php print $mac_font_size; ?>" face="Courier"><?php print strtoupper(preg_replace("/(" . preg_quote(get_request_var('ip_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $bmacs_result["blmacinfo_cor_ip"]));?></font></a>
 			</td> 			
 			<td><?php print $bmacs_result["blmacinfo_cor_portlist"];?></td>
 			<td><?php print date('H:i:s',strtotime($bmacs_result["blmac_first_scan_date"])) . " ( " .  impb_DateTimeDiff($bmacs_result["blmac_first_scan_date"]) . ")";?></td>
			 <td><?php print filter_value($bmacs_result['blmac_scan_date'], get_request_var('filter'));?>
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
	if (api_plugin_installed('macktrack')){
		print "<br><br>";
		$sql_where="";
		$port_results = impb_get_info_recent_macips_records($sql_where, true, $rows);
	 
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

		$nav = html_nav_bar('impb_view_info.php?report=info', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 13, __('Info'), 'page', 'main');

		print $nav;

		html_start_box('', '100%', '', '3', 'center', '');

		$display_text = array(
			'description' => array(__('Описание<br>Устройства'), 'ASC'),
			'hostname' => array(__('IP<br>(Hostname)'), 'ASC'),
			'ip_address' => array(__('IP Адресс'), 'ASC'),
			'mac_address' => array(__('MAC Адресс'),'DESC'),
			'vendor_name' => array(__('Vendor Name'),'DESC'),
			'port_number' => array(__('Номер<br>Порта'),'DESC'),
			'port_name' => array(__('Имя<br>Порта'),'DESC'),
			'vlan_id' => array(__('VLAN<br>ID'),'DESC'),
			'vlan_name' => array(__('VLAN<br>Name'),'DESC'),
			'date_last' => array(__('Время<br>последнего<br>сканирования'),'DESC'),
			'count_rec' => array(__('Количество<br>сканирований'), 'DESC'));		
		
		$i = 0;

		if (sizeof($port_results) > 0) {
			 foreach ($port_results as $port_result) {
				 $scan_date = $port_result["max_scan_date"];
	 
			   if ($port_result["active_last"] == 1)  {
			   $color_line_date="<span style='font-weight: bold;'>";
			   }else{
			   $color_line_date="";
			   }
	   
				 form_alternate_row();
				 ?>
				 <td><?php print $port_result["description"];?></td>
				 <td><?php print $port_result["hostname"];?></td>
				 <td >
					 <a class="linkEditMain" href="impb_view_info.php?report=info&device_id=-1&ip_filter_type_id=2&ip_filter=<?php print $port_result["ip_address"];?>&mac_filter_type_id=1&mac_filter=&port_filter_type_id=&port_filter=&rows=-1&filter=&page=1&report=info&x=23&y=10"><?php print preg_replace("/(" . preg_quote(get_request_var('ip_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["ip_address"]);?></a>
				 </td > 
				
				<?php
				 if (strlen(read_config_option("mt_reverse_dns")) > 0) {?>
				 <td><?php print preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["dns_hostname"]);?></td>
				 <?php }?>
				<td><font size="<?php print $mac_font_size; ?>" face="Courier"><?php print strtoupper(preg_replace("/(" . preg_quote(get_request_var('mac_filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["mac_address"]));?></font></td>
				 
				 <td><?php print $port_result["vendor_name"];?></td>
				<td><?php print $port_result["port_number"];?></td>
				 <td><?php print preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["port_name"]);?></td>
				 <td><?php print $port_result["vlan_id"];?></td>
				 <td><?php print preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $port_result["vlan_name"]);?></td>
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
 		
}



function impb_info_filter() {
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
			strURL  = urlPath+'plugins/impb/impb_view_info.php?header=false';
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
			strURL  = urlPath+'plugins/impb/impb_view_info.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}

		function clearPort() {
			strURL  = urlPath+'plugins/impb/impb_view_info.php?header=false&port_filter_type_id=1&port_filter=';
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
			strURL  = urlPath+'plugins/impb/impb_view_info.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_view_info.php?import=true';
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
