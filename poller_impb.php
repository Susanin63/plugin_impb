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
 
 /* do NOT run this script through a web browser */
 if (!isset($_SERVER["argv"][0])) {
 //	die("<br><strong>This script is only meant to run at the command line.</strong>");
 }
 
 /* We are not talking to the browser */
 $no_http_headers = true;
 
 $dir = dirname(__FILE__);
 chdir($dir);
 
 if (strpos($dir, 'impb') !== false) {
 	chdir('../../');
 }
 
 /* Start Initialization Section */
 include("./include/global.php");
 include_once($config["base_path"] . "/lib/poller.php");
 include_once($config["base_path"] . "/plugins/impb/lib/impb_functions.php");
 
 
 
 /* get the max script runtime and kill old scripts. Удаляються все процессы, который работают больше положенного срока (5 минут)*/
 
 $max_script_runtime = read_config_option("dimpb_script_runtime");
 if (is_numeric($max_script_runtime)) {
     /* let PHP a 5 minutes less than the rerun frequency */
     $max_run_duration = ($max_script_runtime * 60) ;
     //$max_run_duration = ($max_run_duration * 60) - 300;
     ini_set("max_execution_time", $max_run_duration);
 }
 
 $delete_time = date("Y-m-d H:i:s", strtotime("-" . $max_script_runtime . " Minutes"));
 db_execute("delete from imb_processes where start_date < '" . $delete_time . "'");
 
 /* Disable Mib File Loading */
 putenv("MIBS=RFC-1215");

	if (read_config_option("dimpb_use_camm_syslog") == "on") {
		dimpb_poller_process_camm_syslog(0);
	}elseif(read_config_option("dimpb_use_snmptt_plugin") == "on"){
		process_snmptt_traps(0);
		//cimpb_poller_process_camm_traps($device_id_arr, $str_devices_id);
	}
			
 
 if (read_config_option("mt_collection_timing") != "disabled" ||  true) {
 	/* initialize variables */
 	$site_id = "";
 
 	/* process calling arguments */
 	$parms = $_SERVER["argv"];
 	array_shift($parms);
 
 	$debug = FALSE;
 	$forcerun = FALSE;
    $device_id=0;
    print_r($parms); 
 	if (sizeof($parms) > 0 ){
		foreach($parms as $parameter) {
			@list($arg, $value) = @explode("=", $parameter);
	 
			switch ($arg) {
			 case "-id":
				 $device_id = $value;
				 break;
			case "-sid":
				$site_id = $value;
				break;
			case "-d":
				$debug = TRUE;
				break;
			case "-h":
				display_help();
				exit;
			case "-f":
				$forcerun = TRUE;
				break;
			case "-v":
				display_help();
				exit;
			case "--version":
				display_help();
				exit;
			case "--help":
				display_help();
				exit;
			default:
				print "ERROR: Invalid Parameter " . $parameter . "\n\n";
				display_help();
				exit;
			}
		}
	}
 	impb_debug("About to enter IpMacPort Blinding poller processing");
 	$seconds_offset = read_config_option("mt_collection_timing");
 	if (($seconds_offset <> "disabled" || true) || $forcerun) {
 		impb_debug("Into Processing.  Checking to determine if it's time to run.");
 		$seconds_offset = $seconds_offset * 60;
 		/* find out if it's time to collect device information */
 		$base_start_time = read_config_option("mt_base_time");
 		$last_run_time = read_config_option("mt_last_run_time");
 		$previous_base_start_time = read_config_option("mt_prev_base_time");
 		
 
 
 
 
 			impb_debug("It's time to check for IpMacPort Blinding");
 			/* take time and log performance data */
 
			list($micro,$seconds) = explode(" ", microtime());
 			$start = $seconds + $micro;
             $current_time = strtotime("now");
             
 
 			db_execute("REPLACE INTO settings (name, value) VALUES ('dimpb_last_run_time', '$current_time')");
 			$running_processes = db_fetch_cell("SELECT count(*) FROM imb_processes");
 
 			if ($running_processes) {
 				cacti_log("ERROR: Can not start D-Link IP-Mac-Port Blinding process.  There is already one in progress", TRUE);
                 print ("ERROR: Can not start D-Link IP-Mac-Port Blinding Trackinging process.  There is already one in progress");
 			}else{
 			  db_execute("REPLACE INTO settings (name, value) VALUES ('impb_finish', '0')");
               collect_impb_data($start, $site_id, $device_id);
                 
 				db_execute("REPLACE INTO settings (name, value) VALUES ('impb_finish', '1')");
 				log_impb_statistics("collect");
 				update_banip_records();

				if (read_config_option("dimpb_use_camm_syslog") == "on") {
					dimpb_poller_process_camm_syslog($device_id);
				}elseif(read_config_option("dimpb_use_snmptt_plugin") == "on"){
					process_snmptt_traps($device_id);
					//cimpb_poller_process_camm_traps($device_id_arr, $str_devices_id);
				}				
				process_auto_delete_blocks($device_id);
				process_auto_add_binding($device_id);
				process_auto_change_free_binding($device_id);
				process_auto_change_binding($device_id);
				
 				//kill_session_var("imp_output_messages");
 
 			}
 		
 
 	}
 }
 
 /*	display_help - displays the usage of the function */
 function display_help () {
 	print "D-Link IP_Mac-Port Blinding Master Process Control Version 1.0, Copyright 2005 - Larry Adams\n\n";
 	print "usage: poller_impb.php [-d] [-h] [--help] [-v] [--version]\n\n";
 	print "-f            - Force the execution of a collection process\n";
 	print "-d            - Display verbose output during execution\n";
 	print "-v --version  - Display this help message\n";
 	print "-h --help     - display this help message\n";
 }
 
 function collect_impb_data($start, $site_id = 0, $only_device) {
 	global $max_run_duration, $config, $debug;
 	/* reset the processes table */
 	if ($only_device == 0) { /*Если запущен процесс сканирования всех устройств, то удаляем данные со всех таблиц, кроме блоков (что бы сохранить информацию о времени появления записи о блоке*/
       db_execute("TRUNCATE TABLE imb_temp_ports");
 	    db_execute("TRUNCATE TABLE imb_temp_macip");
 	    db_execute("TRUNCATE TABLE imb_temp_blmacs");
       db_execute("TRUNCATE TABLE imb_temp_blmacinfo");
     } else {
         db_execute("DELETE imb_temp_ports.* FROM imb_temp_ports where device_id='" . $only_device . "'");
         db_execute("DELETE imb_temp_macip.* FROM imb_temp_macip where device_id='" . $only_device . "'");
         db_execute("DELETE imb_temp_blmacinfo.* FROM imb_temp_blmacinfo left join imb_temp_blmacs on imb_temp_blmacinfo.blmacinfo_info_id = imb_temp_blmacs.blmac_id where device_id='" . $only_device . "'");
         db_execute("DELETE imb_temp_blmacs.* FROM imb_temp_blmacs where device_id='" . $only_device . "'");
     }
     /* dns resolver binary */
 
 	/* get php binary path */
 	$command_string = read_config_option("path_php_binary");
 
 	/* save the scan date information */
 	$scan_date = date("Y-m-d H:i:s");
 	db_execute("REPLACE INTO settings (name, value) VALUES ('dimpb_scan_date', '$scan_date')");
 
 	/* just in case we've run too long */
 	$exit_impblinding = FALSE;
 
 	/* start mainline processing, order by site_id to keep routers grouped with switches */
 	//$device_ids = db_fetch_assoc("SELECT device_id FROM imb_devices WHERE disabled='' ORDER BY device_id");
 
     if ($site_id > 0) {
         $device_ids = db_fetch_assoc("SELECT device_id FROM imb_devices WHERE site_id='" . $site_id . "' and disabled='' ORDER BY device_id");
     }else{
         if ($only_device > 0) {
             $device_ids = db_fetch_assoc("SELECT device_id FROM imb_devices WHERE disabled='' and device_id='" . $only_device . "' ORDER BY device_id");
         } else {
             $device_ids = db_fetch_assoc("SELECT device_id FROM imb_devices WHERE disabled='' ORDER BY device_id");
         }
     }
     //db_execute("REPLACE INTO settings (name, value) VALUES ('imb_test', '" . $only_device . "')"); 
 
     $total_devices = sizeof($device_ids);
 
 	$concurrent_processes = read_config_option("dimpb_processes");
 
 	if ($debug == TRUE) {
 		$e_debug = " -d";
 	}else{
 		$e_debug = "";
 	}
 
 	/* add the parent process to the process list */
 	
 	if ($total_devices > 0) {
 		dimpb_db_process_add("-1");
		/* scan through all devices */
 		$j = 0;
 		$i = 0;
 		$last_time = strtotime("now");
 		$processes_available = $concurrent_processes;
 		while ($j < $total_devices) {
 			/* retreive the number of concurrent mac_track processes to run */
 			/* default to 10 for now */
 			$concurrent_processes = db_fetch_cell("SELECT value FROM settings WHERE name='dimpb_processes'");
 
 			for ($i = 0; $i < $processes_available; $i++) {
 				if (($j+$i) >= $total_devices) break;
 
 				$extra_args = " -q " . $config["base_path"] . "/plugins/impb/impb_scanner.php -id=" . $device_ids[$i+$j]["device_id"] . $e_debug;
 				//impb_debug("ppp------>>CMD: " . $command_string . $extra_args);
 				exec_background($command_string, $extra_args);
 			}
 			$j = $j + $i;
 
 
 			impb_debug("A process cycle launch just completed.");
 			
 
 			/* wait the correct number of seconds for proccesses prior to
 			   attempting to update records */
 			sleep(2);
 			$current_time = strtotime("now");
 			if (($current_time - $last_time) > read_config_option("mt_dns_prime_interval")) {
 				$last_time = $current_time;
 			}
 
 			$processes_running = db_fetch_cell("SELECT count(*) FROM imb_processes");
 
 
 			/* take time to check for an exit condition */
 			list($micro,$seconds) = explode(" ", microtime());
 			$current = $seconds + $micro;
 
 			/* exit if we've run too long */
 			if (($current - $start) > $max_run_duration) {
 				$exit_impblinding = TRUE;
 				cacti_log("ERROR: IpMacPort Blinding timed out during main script processing.\n");
 				dimpb_db_process_remove("-1");
 				break;
 			}
 		}
 
 		/* wait for last process to exit */
 		$processes_running = db_fetch_cell("SELECT count(*) FROM imb_processes WHERE device_id > 0");
 		while (($processes_running > 0) && (!$exit_impblinding)) {
 			$processes_running = db_fetch_cell("SELECT count(*) FROM imb_processes WHERE device_id > 0");
 
 			/* wait the correct number of seconds for proccesses prior to
 			   attempting to update records */
 			sleep(2);
 
 			/* take time to check for an exit condition */
 			list($micro,$seconds) = explode(" ", microtime());
 			$current = $seconds + $micro;
 
 			/* exit if we've run too long */
 			if (($current - $start) > $max_run_duration) {
 				$exit_impblinding = TRUE;
 				cacti_log("ERROR: IpMacPort Blinding timed out during main script processing.\n");
 				break;
 			}
 
 			impb_debug("Waiting on " . $processes_running . " to complete prior to exiting.");
 		}
 
 
 		/* let the resolver know that the parent process is finished and then wait
 		   for the resolver if applicable */
 		dimpb_db_process_remove("-1");
 		
 
 $processes_running_1 = db_fetch_cell("SELECT count(*) FROM imb_processes WHERE device_id > 0");
 impb_debug("ppp------>> ALL FINISH START transferring scan results to main table. Count processes=" . $processes_running_1 . "] [" . db_fetch_cell("SELECT device_id FROM mac_track_processes WHERE device_id > 0") );
         db_execute("update imb_ports  " .
             " set imb_ports.count_macip_record = (SELECT count(*)  " .
             " FROM imb_macip where imb_ports.device_id=imb_macip.device_id  " .
             " and find_in_set(imb_ports.port_number, imb_macip.macip_port_list));  ");
 
         db_execute("truncate table imb_temp_ports_stat;");
 
         db_execute("insert into imb_temp_ports_stat SELECT  imb_ports.device_id ,imb_ports.port_number, " .
             " (select count(*) from imb_mactrack_recent_ports where imb_mactrack_recent_ports.device_id=mac_track_devices.device_id " .
             " and imb_mactrack_recent_ports.port_number =imb_ports.port_number) as max_count " .
             " from (imb_ports left join imb_devices  on imb_ports.device_id=imb_devices.device_id) " .
             " left join mac_track_devices on imb_devices.hostname=mac_track_devices.hostname; ");
 
         db_execute("update imb_ports ".
             " set imb_ports.count_scanmac_record_max = (SELECT count_rec " .
             " FROM imb_temp_ports_stat where imb_temp_ports_stat.device_id=imb_ports.device_id " .
             " and imb_temp_ports_stat.port_number=imb_ports.port_number);");
 	
 	db_execute("UPDATE imb_macip set `macip_active_last_poll`=0;");
 	db_execute("UPDATE imb_macip,imb_mactrack_temp_ports " .
 		" SET imb_macip.macip_active_last_poll=1, " .
		" imb_macip.macip_scan_date=NOW() " .
 		" WHERE imb_macip.device_id=imb_mactrack_temp_ports.imb_device_id " .
 		" and imb_macip.macip_macaddr=imb_mactrack_temp_ports.mac_address " .
 		" and imb_macip.macip_ipaddr=imb_mactrack_temp_ports.ip_address " .		
 		" and imb_macip.macip_port_view=imb_mactrack_temp_ports.port_number;");	        
 		
 		if ($only_device > 0) {
 			//db_store_imp_log("Завершен процесс опроса устройства [" . $only_device . "]", "device", $only_device, "poll",$only_device, !$exit_impb, !$exit_impb, !$exit_impb, !$exit_impb);
 		}
         
 	}else{
		cacti_log('ERROR: Can not start IP-Mac-Port Binding process.  NO Devices with ID=[' . $only_device . '] found!', TRUE);		
	}
	
 }
 function log_impb_statistics($type = "collect") {
 	global $start;
 
 	/* let's get the number of devices */
 		$devices = db_fetch_cell("SELECT Count(*) FROM imb_devices");
 		$ipmacs = db_fetch_cell("SELECT Count(*) FROM imb_macip");
 		$blmacs = db_fetch_cell("SELECT Count(*) FROM imb_blmacs");
 		$Active_ports = db_fetch_cell("SELECT Count(*) FROM imb_ports where port_imb_state = 2");
 
 	$concurrent_processes = read_config_option("dimpb_processes");
 
 	/* take time and log performance data */
 	list($micro,$seconds) = explode(" ", microtime());
 	$end = $seconds + $micro;
 
 
 		$imb_stats_general = sprintf(
 			"Time:%01.4f " .
 			"ConcurrentProcesses:%s " .
 			"Devices:%s ",
 			round($end-$start,4),
 			$concurrent_processes,
 			$devices);
 		/* log to the database */
 		db_execute("REPLACE INTO settings (name,value) VALUES ('dimpb_stats_general', '" . $imb_stats_general . "')");
 		$imb_stats = sprintf(
 			"ipmacs:%s " .
 			"Blockedmacs:%s " .
 			"Active_ports:%s " ,      			
 			$ipmacs,
 			$blmacs,
 			$Active_ports);
 		/* log to the database */
 		db_execute("REPLACE INTO settings (name,value) VALUES ('dimpb_stats', '" . $imb_stats . "')");
 		/* log to the logfile */
 		cacti_log("D-Link IP-Mac-Port Blinding STATS: " . $imb_stats_general . "; " . $imb_stats ,true,"SYSTEM");
 		
 
 }
 
 function update_banip_records() {
 //STEP_1 Удаление уже устаревших записей банов от скрипта. Удаляем любую запись - как примененную. так и нет. Просто уже нет причины для автоматического бана.
 $banip_ips = db_fetch_assoc("SELECT `banip_id`, `banip_ipaddr` FROM imb_banip where `banip_delete`='1' AND `banip_manual`='0';");
 	foreach($banip_ips as $key => $banip_ip) {
 		imb_ban_delete($banip_ip["banip_id"],$banip_ip["banip_ipaddr"]);
 	}
 //STEP_2 Удаление записей, у которых истек срок бана.
 $banip_ips = db_fetch_assoc("SELECT * FROM imb_banip where  `banip_manual`='0' AND `banip_aproved`=1 AND `banip_aplled`=1 AND (UNIX_TIMESTAMP(`banip_expiration_date`)>0) AND (UNIX_TIMESTAMP()>UNIX_TIMESTAMP(`banip_expiration_date`));");
 	foreach($banip_ips as $key => $banip_ip) {
 		imb_ban_delete($banip_ip["banip_id"]);
 	}
 //STEP_3 Установка реальных банов из тех, что были подтверждены  и созданы скриптом, 
 $banip_ips = db_fetch_assoc("SELECT * FROM imb_banip where  `banip_manual`='0' AND `banip_aproved`=1 AND `banip_aplled`=0 ;");
 	foreach($banip_ips as $key => $banip_ip) {
 		imb_ban_aplly($banip_ip["banip_id"], true);
 	}
 if (read_config_option("dimpb_check_new_records_for_ban")=="on") { //Проверка новых записей на баны.
 $new_macip_records = db_fetch_assoc("SELECT imb_macip.macip_id,imb_macip.macip_ipaddr, imb_banip.banip_id " .
 		" FROM imb_macip  join imb_banip " .
 		" on (INET_ATON(imb_banip.banip_ipaddr) = INET_ATON(imb_macip.macip_ipaddr)) " .
 		" where imb_macip.macip_active=1 and imb_banip.banip_aplled=1 and imb_macip.macip_banned=0");
 	foreach($new_macip_records as $key => $macip_ip) {
 		imb_ban_aplly($macip_ip["banip_id"], true);
 	}
 
 }		
 
 }
 
 function  process_snmptt_traps($device_id = 0) {
 
 	// if ($device_id == 0) { /*Если запущен процесс сканирования всех устройств, то удаляем данные со всех таблиц, кроме блоков (что бы сохранить информацию о времени появления записи о блоке*/
 		// db_execute("TRUNCATE TABLE imb_traps_blocked");
     // } else {
         // $ipaddr = db_fetch_cell("SELECT `hostname` FROM `imb_devices` where `device_id` = '" . $only_device . "';");
 		// db_execute("DELETE imb_traps_blocked.* FROM imb_traps_blocked where device_id='" . $ipaddr . "'");
     // }
	 
	//db_execute("DELETE imb_traps_blocked.* FROM imb_traps_blocked where device_id='" . $ipaddr . "'");
	
 	$evenids = db_fetch_assoc("SELECT distinct `imb_device_types`.`snmp_oid_Trap_eventid` FROM `imb_devices` " .
 					" left join `imb_device_types` on `imb_devices`.`device_type_id`=`imb_device_types`.`device_type_id` " .
 					" where `imb_devices`.`disabled` = ''");
 	$str_eventids = "";
 	if (sizeof($evenids)) {
 		foreach($evenids as $key => $evenid) {
 			$str_eventids = $str_eventids . "'" . $evenid["snmp_oid_Trap_eventid"] . "', ";
 		}
 		$str_eventids = substr($str_eventids, 0, strlen($str_eventids) -2);
 		$traps=db_fetch_assoc("SELECT * FROM plugin_camm_snmptt where traptime > '" .  read_config_option("dimpb_scan_date"). "' and " .
 		//$traps=db_fetch_assoc("SELECT * FROM plugin_camm_snmptt where traptime > '2008-03-16 14:42:06' and " .
 			" eventid in (" . $str_eventids . ") " .
 			" order by traptime;");
 		if (sizeof($traps)) {
 			$sql_replace = "REPLACE INTO `imb_traps_blocked` (`traps_hostname`,`traps_time`,`traps_macaddr`,`traps_ipaddr`,`traps_port`) VALUES " ;
			foreach($traps as $key => $trap) {
 			$matches = array();
 			preg_match("/IP:\ *([0-9.]*),\ *MAC\:\ *([0-9a-fA-F\:]*),\ *Port\:\ *([0-9]{1,2})/", $trap["formatline"], $matches);
 			//preg_match("/\[port=([0-9]{1,2})\ *ip\=([0-9\.]*)\ *mac\=([0-9a-fA-F\:]*)\]/", $trap["formatline"], $matches);
 				if (sizeof($matches)) {
 					$sql_replace .= " ('" . $trap["hostname"] . "','" . $trap["traptime"] . "','" . $matches[2] . "','" . $matches[1] . "','" . $matches[3] . "'),";
 				}
 			}
			$sql_replace = substr($sql_replace, 0, strlen($sql_replace) - 1);
			$sql_replace .= ";";
			db_execute($sql_replace);
			db_execute("UPDATE `imb_traps_blocked`,`imb_devices` SET `imb_traps_blocked`.`traps_device_id`=`imb_devices`.`device_id` " .
 					"WHERE `imb_traps_blocked`.`traps_hostname`=`imb_devices`.`hostname`; ");
 		
 			
 		}
 	}
 	db_execute("UPDATE imb_blmacs,imb_traps_blocked SET imb_blmacs.blmac_blocked_ip=imb_traps_blocked.traps_ipaddr " .
 			"WHERE (imb_blmacs.device_id=imb_traps_blocked.traps_device_id and " .
 			"imb_blmacs.blmac_port=imb_traps_blocked.traps_port and " .
 			"imb_blmacs.blmac_macaddr=imb_traps_blocked.traps_macaddr);");
	
	db_execute("DELETE FROM `imb_traps_blocked` WHERE DATE_ADD(`traps_time`, INTERVAL  10 DAY) < NOW() ;");
 
 }
 

 function  dimpb_poller_process_camm_syslog($device_id = 0) {
  	global $plugins, $config;
	
 $sql_device_hostname = '';
 
 $plugin_camm_status = db_fetch_cell("SELECT `status`  FROM `plugin_config` WHERE `directory`='camm'; ");
 
	// if camm plugin installed
 	if ($plugin_camm_status == '1') {
		// if syslog component enabled ? and syslog_db name is set
		if ((read_config_option("camm_use_syslog", true)==1) && (strlen(trim("camm_syslog_db_name")) > 0)) {
			// check for use syslog pre table 
	 		if ((strlen(trim(read_config_option("camm_syslog_pretable_name"))) > 0) && (read_config_option("camm_syslog_pretable_name") != "plugin_camm_syslog")) {
	 			$pre_table = read_config_option("camm_syslog_pretable_name");
				$table = "plugin_camm_syslog";
				$use_pre_table = true;
	 		}else{
	 			//$table = '`' . read_config_option("camm_syslog_db_name") . '`.`plugin_camm_syslog`';
				$table = "plugin_camm_syslog";
				$use_pre_table = false;
	 		}			
			
			//if table exist and accecable ...
			$result = db_fetch_assoc("show tables from `" . read_config_option("camm_syslog_db_name") . "`;");
  		  	$tables = array();
		  	if (count($result) > 1) {
		  		foreach($result as $index => $arr) {
		  			foreach ($arr as $t) {
		  				$tables[] = $t;
		  			}
		  		}
		  	}
		  	if ((($use_pre_table == false) && in_array($table, $tables)) || (($use_pre_table == true) && in_array($table, $tables) && in_array($pre_table, $tables))) {
		 		// table exist. Now work
				$table = '`' . read_config_option("camm_syslog_db_name") . '`.`' . $table . '`';
				
				if ($device_id != 0) {
				// создадим строку с именами/ип устройств
					$arr_hostnames = db_fetch_cell("SELECT `hostname` FROM `imb_devices` where `device_id`='" . $device_id . "';");
			 		$sql_device_hostname = " AND `host` = '" . $arr_hostnames . "'";
				}
				
				// возьмем с запасом в 5 минут (возьмем максимальное время выполнения скрипта)
				 $max_script_runtime = read_config_option("dimpb_script_runtime");
				 if (is_numeric($max_script_runtime)) {
				     /* let PHP a 5 minutes less than the rerun frequency */
				     $max_run_duration = (($max_script_runtime + 1) * 60 + 5*60*60) ;
				 }					
				$sys_time = date("Y-m-d H:i:s", strtotime(read_config_option("dimpb_scan_date", true))- $max_run_duration);
		
				$str_sql =  "SELECT host, sys_date,message FROM " . $table . " WHERE `message` like '%Unauthenticated%' AND `sys_date` > '" .  $sys_time  . "' " .  $sql_device_hostname ;
				
				if ($use_pre_table) {
						$str_sql = $str_sql . " UNION SELECT host, sys_date,message FROM `" . read_config_option("camm_syslog_db_name") . '`.`' . read_config_option("camm_syslog_pretable_name") .  "`  WHERE `message` like '%WARN%Unauthenticated%' AND `sys_date` > '" .  $sys_time  . "' " .  $sql_device_hostname ;
				}
				$str_sql = $str_sql . ";";
				
				$records=db_fetch_assoc($str_sql);
		 		if (sizeof($records)) {
					$sql_replace = "REPLACE INTO `imb_traps_blocked` (`traps_hostname`,`traps_time`,`traps_macaddr`,`traps_ipaddr`,`traps_port`) VALUES " ;
					foreach($records as $key => $record) {
		 			$matches = array();
		 			preg_match("/IP:(?:\ |\<)*([0-9.]*)\>?,\ *MAC\:(?:\ |\<)*([0-9a-fA-F(?:\:|\-)]*)\>?,\ *Port\:?(?:\ |\<)*(?:[0-9]{1}\:)?([0-9]{1,2})\>?/i", $record["message"], $matches);
					//preg_match("/IP:\ *([0-9.]*),\ *MAC\:\ *([0-9a-fA-F(?:\:|\-)]*),\ *Port\:\ *([0-9]{1,2})/i", $record["message"], $matches);
		 			//preg_match("/\[port=([0-9]{1,2})\ *ip\=([0-9\.]*)\ *mac\=([0-9a-fA-F\:]*)\]/", $trap["formatline"], $matches);
		 				if (sizeof($matches)) {
							$matches[2] = str_replace("-",":",$matches[2]);
		 					$sql_replace .= " ('" . $record["host"] . "','" . $record["sys_date"] . "','" . $matches[2] . "','" . $matches[1] . "','" . $matches[3] . "'),";
		 				}
		 			}
					$sql_replace = substr($sql_replace, 0, strlen($sql_replace) - 1);
					$sql_replace .= ";";
					db_execute($sql_replace);
					db_execute("UPDATE `imb_traps_blocked`,`imb_devices` SET `imb_traps_blocked`.`traps_device_id`=`imb_devices`.`device_id` " .
							"WHERE `imb_traps_blocked`.`traps_hostname`=`imb_devices`.`hostname`; ");
		 		
		 			
		 		}				
			
			}
		db_execute("UPDATE imb_blmacs,imb_traps_blocked SET imb_blmacs.blmac_blocked_ip=imb_traps_blocked.traps_ipaddr " .
				"WHERE (imb_blmacs.device_id=imb_traps_blocked.traps_device_id and " .
				"imb_blmacs.blmac_port=imb_traps_blocked.traps_port and " .
				"imb_blmacs.blmac_macaddr=imb_traps_blocked.traps_macaddr);");
		
		db_execute("DELETE FROM `imb_traps_blocked` WHERE DATE_ADD(`traps_time`, INTERVAL  10 DAY) < NOW() ;");
		
		}
	}

 }
 
 
 function  process_auto_delete_blocks($device_id = 0) {
 
//$flood_minute = db_fetch_cell("SELECT count(*) FROM `imb_cli`  where `device_id`='" . $row["device_id"] . "' and `cli_index`='" . $row["oid"] . "' and `cli_ip`='" . $row["ip"] . "' and `cli_port`='" . $row["port"] . "' and `cli_type`='" . $row["type_op"] . "' and `cli_date` > DATE_SUB(NOW(),INTERVAL 2 MINUTE) ;");
	$str_ids ='';	
		
	$blocks = db_fetch_assoc("SELECT `imb_devices`.`device_id`, `blmac_id`, `blmac_blocked_ip`, `blmac_macaddr` ,`blmac_index` ,`blmac_port`, `blmac_vid` , '1' as type_op,`net_id`  FROM `imb_blmacs` " .
		" join imb_auto_updated_nets ON ((inet_aton(`blmac_blocked_ip`) & `net_mask`) = `net_ipaddr`)  " .
		" left join imb_devices ON (`imb_devices`.`device_id`=`imb_blmacs`.`device_id`) " . 
		" left join imb_device_types dt ON (dt.device_type_id=imb_devices.device_type_id) " . 
		" where dt.setting_imb_use_auto_unblock=1 and blmac_blocked_ip is not null and `net_type`='1' and `blmac_online`='1'  ;");

		
 	if (sizeof($blocks)) {
		//based in blocks find devices
		foreach ($blocks as $block) {
	
			$str_ids = $str_ids . "'" . $block["blmac_id"] . "', ";
		}
		$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
		$blmacs_devices=dimpb_array_rekey(db_fetch_assoc("SELECT `d`.`description` as dev_name , d.*, dt.* FROM imb_blmacs b LEFT JOIN imb_devices d on (b.device_id=d.device_id) LEFT JOIN imb_device_types dt on (d.device_type_id = dt.device_type_id) WHERE `b`.`blmac_id` in (" . $str_ids . ") GROUP by b.device_id;"), "device_id");		
		
		foreach($blocks as $key => $block) {
			//check for flood
			$rezult = false;
			$flood = imp_check_for_flood ($block, false);
			if (!($flood)) {
				//$rezult = api_imp_delete_blmacs($block["blmac_id"], $block);
				$rezult = api_imp_delete_blmacs($block, $blmacs_devices[$block["device_id"]]);
			}
			$log_message = "Auto delete block  Device_ID=[" . $block["device_id"] . "], IP=[" . $block["blmac_blocked_ip"] . "], MAC=[" . $block["blmac_macaddr"] . "], PORT=[" . $block["blmac_port"] . "], VID=[" . $block["blmac_vid"] . "], rezult=[" . ($rezult == "OK" ?  "OK" : "ERROR") . "]";
			if ($rezult == "OK")  {
				db_execute("UPDATE `imb_auto_updated_nets` SET `net_trigger_count`=`net_trigger_count` + 1 WHERE `net_id`='" . $block["net_id"] . "'");
			}
			db_store_imp_log("2", $block["device_id"], "block_del", "0", "0", "0", $log_message, $rezult, 0, $rezult, 0);
		}
	} 
 };
 
function  process_auto_add_binding($device_id = 0) {
	global $impb_imp_mode;
	
	$blocks = db_fetch_assoc("SELECT `imb_blmacs`.`blmac_id`, `net_id`, INET_NTOA(`net_ipaddr`) as `anet_ipaddr`,`setting_imb_def_mode`, `blmac_port` as port, '2' as type_op FROM `imb_blmacs` " .
		" join imb_auto_updated_nets ON ((inet_aton(`blmac_blocked_ip`) & `net_mask`) = `net_ipaddr`)  " .
		" left join imb_devices ON (`imb_devices`.`device_id`=`imb_blmacs`.`device_id`) " . 
		" left join imb_device_types ON (imb_device_types.device_type_id=imb_devices.device_type_id) " . 
		" where imb_device_types.setting_imb_use_auto_add=1 and blmac_blocked_ip is not null and `net_type`='2' and `blmac_online`='1' " .
		" and (`imb_devices`.`device_id`=`net_device_id` OR `net_device_id` = '0' )  " .
		" and  (imb_auto_updated_nets.net_device_id=imb_blmacs.device_id or imb_auto_updated_nets.net_device_id=0) ;");

	if (sizeof($blocks)) {
		foreach($blocks as $key => $block) {
			$blmac_record = db_fetch_row ("SELECT * FROM `imb_blmacs` WHERE `blmac_id`='" . $block["blmac_id"] . "';");
			
			$rezult == false;
			$flood = imp_check_for_flood ($block, false);
			if (!($flood)) {
				$rezult = imb_create_imp_record_from_block($blmac_record["device_id"], $blmac_record["blmac_macaddr"], $blmac_record["blmac_blocked_ip"], $blmac_record["blmac_port"], $blmac_record, $impb_imp_mode[$block["setting_imb_def_mode"]], true);
			}
			$log_message = "Auto create from block on Device_ID=[" . $blmac_record["device_id"] . "], IP=[" .  $blmac_record["blmac_blocked_ip"] . "], MAC=[" . $blmac_record["blmac_macaddr"] . "], PORT=[" . $blmac_record["blmac_port"] . "], VID=[" . $blmac_record["blmac_vid"] . "], rezult=[" . ($rezult == "OK" ?  "OK" : "ERROR") . "]";
			cacti_log($log_message, TRUE);
			if ($rezult == "OK")  {
				$rezult = true;
				$impb_debug = true;
				$uid = db_fetch_cell(" SELECT lbv.uid FROM imb_auto_updated_nets i LEFT JOIN lb_staff lbs ON (i.net_ipaddr=lbs.segment) " .
				" LEFT JOIN lb_vgroups_s lbv ON (lbs.vg_id=lbv.vg_id) where i.net_id='" . $block["net_id"]. "';");
				impb_debug("IMPB: POller Restore balance for net_id=" . $block["net_id"] . ", uid=" . $uid);
				cacti_log("IMPB_: POller Restore balance for net_id=" . $block["net_id"] . ", uid=" . $uid, TRUE);
				$arrContextOptions=array(
					"ssl"=>array(
						"verify_peer"=>false,
						"verify_peer_name"=>false,
					),
				);
				$rest_balance = file_get_contents('https://iserver.ion63.ru/admin/_cacti/cacti.php?uid=' . $uid , false, stream_context_create($arrContextOptions));
				impb_debug("IMPB: Poller rezult [" . print_r($rest_balance) . "]");
				$impb_debug = false;				
				db_execute("DELETE FROM `imb_auto_updated_nets` WHERE `net_id`='" . $block["net_id"] . "';");
			}
			db_store_imp_log("1", $blmac_record["device_id"], "block_crt", "0", "0", "0", $log_message, $rezult, 0, $rezult, 0);
		}
	}
	
	db_execute("DELETE FROM `imb_auto_updated_nets` WHERE `net_ttl`<>0 and `net_type`='2' and DATE_ADD(imb_auto_updated_nets.net_change_time, INTERVAL  `net_ttl` HOUR) < NOW() ;");
}
function  process_auto_change_free_binding($device_id = 0) {
	global $impb_imp_mode;
	
	$blocks = db_fetch_assoc("SELECT `im`.`macip_id`, `ib`.`blmac_id`,  `blmac_macaddr`, `blmac_blocked_ip`, `im`.`macip_port_list`, `ib`.`blmac_port`, `idt`.`setting_imb_def_mode` FROM `imb_blmacs` ib " .
		" LEFT JOIN imb_macip im ON (`im`.`macip_ipaddr`=`ib`.`blmac_blocked_ip` and `im`.`macip_macaddr`=`ib`.`blmac_macaddr` and `im`.`device_id`=`ib`.`device_id`) " .
		" LEFT JOIN imb_devices id ON (`id`.`device_id`=`ib`.`device_id`) " .
		" LEFT JOIN imb_device_types idt ON (`idt`.device_type_id=`id`.device_type_id) " .
		" where `idt`.setting_imb_use_auto_add=1 " .
		" and `blmac_blocked_ip` is not null " .
        " and `blmac_online`='1' " .
		" and `im`.`macip_may_move`='1' ;");

	if (sizeof($blocks)) {
		foreach($blocks as $key => $block) {
			$blmac_record = db_fetch_row ("SELECT * FROM `imb_blmacs` WHERE `blmac_id`='" . $block["blmac_id"] . "';");
			$rezult = imb_create_imp_record_from_block($blmac_record["device_id"], $blmac_record["blmac_macaddr"], $blmac_record["blmac_blocked_ip"], $blmac_record["blmac_port"], $blmac_record, $impb_imp_mode[$block["setting_imb_def_mode"]].true);
			
			$log_message = "Auto Change Port (move) from block Device_ID=[" . $blmac_record["device_id"] . "], IP=[" . $blmac_record["blmac_blocked_ip"] . "], MAC=[" . $blmac_record["blmac_macaddr"] . "], PORT=[" . $blmac_record["blmac_port"] . "], VID=[" . $blmac_record["blmac_vid"] . "], rezult=[" . ($rezult == "OK" ?  "OK" : "ERROR") . "]";
			cacti_log($log_message, TRUE);
			if ($rezult == "OK")  {
				db_execute("UPDATE `imb_macip` SET `macip_may_move`=0 WHERE `macip_id`='" . $block["macip_id"] . "';");
			}
			db_store_imp_log("1", $blmac_record["device_id"], "block_move", $block["macip_id"], $block["macip_port_list"], $blmac_record["port"], $log_message, $rezult, 0, $rezult, 0);
		}
	}
	
	//db_execute("DELETE FROM `imb_auto_updated_nets` WHERE `net_ttl`<>0 and `net_type`='2' and DATE_ADD(imb_auto_updated_nets.net_change_time, INTERVAL  `net_ttl` HOUR) < NOW() ;");
}

function  process_auto_change_binding($device_id = 0) {
	global $impb_imp_mode;

	$blocks = db_fetch_assoc("SELECT `im`.`macip_id`, `im`.`macip_ipaddr`,`im`.`macip_macaddr`,`ip`.`count_macip_record`, `ib`.`blmac_id`,  `blmac_macaddr`, `blmac_blocked_ip`, `im`.`macip_port_list`, `ib`.`blmac_port`, `idt`.`setting_imb_def_mode` FROM `imb_blmacs` ib " .
		" LEFT JOIN imb_macip im ON (`im`.`macip_ipaddr`=`ib`.`blmac_blocked_ip` and `im`.`macip_port_list`=`ib`.`blmac_port` and `im`.`device_id`=`ib`.`device_id`)  " .
		" LEFT JOIN imb_devices id ON (`id`.`device_id`=`ib`.`device_id`)  " .
		" LEFT JOIN imb_device_types idt ON (`idt`.device_type_id=`id`.device_type_id)  " .
		" LEFT JOIN imb_ports ip ON (`id`.`device_id`=`ip`.`device_id` and `ip`.`port_number` = `im`.`macip_port_list`)  " .
		" where `idt`.setting_imb_use_auto_change=1  " .
		" and `blmac_blocked_ip` is not null  " .
		" and `im`.`macip_ipaddr` is not null  " .
		" and (`ip`.`count_macip_record` <= '" . read_config_option("dimpb_max_count_rec_for_auto_change") . "' or LCASE(ip.port_name) like '%ach%')  " .
		" and `blmac_online`='1' ;");

	if (sizeof($blocks)) {
		foreach($blocks as $key => $block) {
			$blmac_record = db_fetch_row ("SELECT * FROM `imb_blmacs` WHERE `blmac_id`='" . $block["blmac_id"] . "';");
			$rezult = imb_create_imp_record_from_block($blmac_record["device_id"], $blmac_record["blmac_macaddr"], $blmac_record["blmac_blocked_ip"], $blmac_record["blmac_port"], $blmac_record, $impb_imp_mode[$block["setting_imb_def_mode"]], true);
			
			$log_message = "Auto changing binding record Device_ID=[" . $blmac_record["device_id"] . "], IP=[" . $blmac_record["blmac_blocked_ip"] . "], NEW MAC=[" . $blmac_record["blmac_macaddr"] . "] , OLD MAC=[" . $block["macip_macaddr"] . "] on device_id=[" . $blmac_record["device_id"] . "], rezult=[" . ($rezult == "OK" ?  "OK" : "ERROR") . "]";
			cacti_log("ATTENTION: DIMPB " . $log_message , TRUE);			
			if ($rezult == "OK")  {
				//db_execute("UPDATE `imb_macip` SET `macip_may_move`=0 WHERE `macip_id`='" . $block["macip_id"] . "';");
				cacti_log("AutoChange was successful", TRUE);
			}else{
				cacti_log("AutoChange ERROR", TRUE);			
			}
			//db_store_imp_log("2", $blmac_record["device_id"], "block_chng", $blmac_record["macip_id"], $block["macip_macaddr"], $blmac_record["blmac_macaddr"], $log_message, $rezult, 0, $rezult, 0);
		}
	}
	
	//db_execute("DELETE FROM `imb_auto_updated_nets` WHERE `net_ttl`<>0 and `net_type`='2' and DATE_ADD(imb_auto_updated_nets.net_change_time, INTERVAL  `net_ttl` HOUR) < NOW() ;");
}

 ?>
