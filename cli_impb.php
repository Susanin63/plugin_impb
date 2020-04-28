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
/* do NOT run this script through a web browser */
if (!isset($_SERVER["argv"][0]) || isset($_SERVER['REQUEST_METHOD'])  || isset($_SERVER['REMOTE_ADDR'])) {
	//die("<br><strong>This script is only meant to run at the command line.</strong>");
}

  /* We are not talking to the browser */
 $no_http_headers = true;
 

 /* Start Initialization Section */
include(dirname(__FILE__)."/../../include/global.php");
 //include_once($config["base_path"] . "/lib/poller.php");
include_once($config["base_path"] . "/plugins/impb/lib/impb_functions.php");

global $impb_debug;
$impb_debug = false;


//разберем сообщение
if (isset($_SERVER["argv"][1])) {
	impb_debug("IMPB:  ERROR got syslog IMPB message =[" . $_SERVER["argv"][1] . "]");	
	$str_binding = $_SERVER["argv"][1];
	//exit;
}else{
	//$str_binding = "172.20.0.32,20131128165034, Unauthenticated IP-MAC address and discarded by ip mac port binding (IP: 192.168.0.87, MAC: 44-6D-57-3D-3A-5F, port: 5)";
	//$str_binding = "172.20.0.3,20140910170154, Unauthenticated IP-MAC address and discarded by IMPB(IP:<172.20.1.20>, MAC:<AC-F1-DF-0E-B2-D3>, Port<1:1>)";
	//$str_binding = "172.20.0.4,20131206121723, Unauthenticated IP-MAC address and discarded by ip mac port binding (IP: 172.20.14.2, MAC: 00-0A-E4-59-25-99, Port: 4)";

	//$str_binding = "172.20.0.30,20130406161618, Unauthenticated IP-MAC address and discarded by ip mac port binding (IP: 172.20.13.76, MAC: 54-04-a6-29-a7-bb, Port: 2)";
	//$str_binding = "172.20.0.54,20130416154458, Unauthenticated IP-MAC address and discarded by ip mac port binding (IP: 172.20.14.65, MAC: 80-C1-6E-55-C1-41, Port: 17)";
	//$str_binding = "172.20.0.77,20180705152058, Unauthenticated IP-MAC address and discarded by ip mac port binding (IP: 172.20.29.129, MAC: 38-D5-47-CF-40-D5, Port: 5)";
	impb_debug("IMPB:  ERROR NOT Goe syslog IMPB message ");	
}
$str_hostip = trim(strstr($str_binding, ',',true));
$str_binding = str_replace(")","",str_replace("(","",strstr($str_binding, '(')));
//IP: 172.20.13.255, MAC: E8-03-9A-93-94-22, port: 237

//preg_match("/IP:\ *([0-9.]*),\ *MAC\:\ *([0-9a-fA-F(?:\:|\-)]*),\ *Port\:\ *([0-9]{1,2})/i", $str_binding, $matches);
preg_match("/IP:(?:\ |\<)*([0-9.]*)\>?,\ *MAC\:(?:\ |\<)*([0-9a-fA-F(?:\:|\-)]*)\>?,\ *Port\:?(?:\ |\<)*(?:[0-9]{1}\:)?([0-9]{1,2})\>?/i", $str_binding, $matches);



//Проверим что такой хост существует и известны все три параметра сообщения
if (isset($matches[1]) and isset($matches[2]) and isset($matches[3])) {
	$matches["ip"] = $matches[1];
	$matches["mac"] = str_replace("-",":",$matches[2]);
	$matches["port"] = $matches[3];

	if (strlen($matches[1]) > 9 and strlen($matches[2]) > 16 and strlen($matches[3]) > 0) {
		$device =  db_fetch_row ("SELECT `imb_devices`.`description` as dev_name , imb_devices.*, imb_device_types.* FROM imb_devices " .
			" LEFT JOIN imb_device_types " .
			" on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
			" WHERE hostname='" . $str_hostip . "';");		
		 if (sizeof($device) > 0) {
			 //все исходные данные есть. для удаления блока (при автоудалении или после автосоздания) нужен номер влана. 
			$matches["device_id"]=$device["device_id"];
			$xformArray = impb_standard_indexed_data_oid($device["snmp_oid_MacBindingBlockedMac"], $device);
				//$xformArray["303.0.25.91.16.108.240"]="54-04-a6-29-a7-bb"
				
				//продолжаем если только есть хоть одна строка.
				if (sizeof($xformArray) > 0) {
				/* $xformArray
				: array = 
				  301.0.25.91.16.108.240: string = "00:19:5B:10:6C:F0"
				  303.172.241.223.14.178.211: string = "AC:F1:DF:0E:B2:D3"*/
					//из всех возможных блоков найдем наш.
					foreach($xformArray as $oid => $value ) {
						$blmacMac = dimpb_xform_mac_address($value);
						if (strtoupper(str_replace(":","-",dimpb_xform_mac_address($value))) == strtoupper(str_replace(":","-",$matches["mac"]))) {
							$oid = str_replace(".enterprises","",$oid);
							$oid = str_replace(str_replace(strstr($device["snmp_oid_MacBindingBlockedMac"], '.171',true),"",$device["snmp_oid_MacBindingBlockedMac"] . "."),"",$oid);
							 
							
							$matches["oid"] = $oid;
							$matches["vlan"] = strstr($oid, '.',true);
						}
					}
					if (isset($matches["vlan"]) and is_numeric($matches["vlan"])) {
						$matches["vlan_name"] = cacti_snmp_get($device["hostname"], $device["snmp_get_community"], $device["snmp_oid_MacBindingBlockedVlanName"] . "." . $matches["oid"], $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"]);
						impb_debug("IMPB:  Block is  DEV_ID=[" . $device["device_id"] . "], IP=[" . $matches["ip"] . "], MAC=[" . $matches["mac"] . "], PORT=[" . $matches["port"] . "], VID=[" . $matches["vlan"] . "]");					
						$rezult = false;
						$flood = true;
						$matches["type_op"]=0;//тип операции - автоудаление/изменение/создание
						//$matches["type_op_str"]=0;//тип операции строковый - автоудаление/изменение/создание
						
						//Вставим запись о блоке
						db_execute("INSERT INTO `imb_blmacs` (blmac_active,blmac_online, device_id,blmac_temp_id,blmac_index,blmac_macaddr,blmac_port,blmac_type,blmac_vid,blmac_vlanname,blmac_blocked_ip,blmac_first_scan_date,blmac_scan_date,blmac_done,blmac_info) " .
										" VALUES ('1','1', '" . $matches["device_id"] . "','0','" . $matches["oid"] . "', '" . $matches["mac"] . "', '" . $matches["port"] . "','3', '" . $matches["vlan"] . "',  '" . $matches["vlan_name"] . "', '" . $matches["ip"] . "',NOW(), NOW()," . $matches["type_op"] . ",'') " .
										" ON DUPLICATE KEY UPDATE `blmac_active`=VALUES(`blmac_active`),`blmac_online`=VALUES(`blmac_online`),`blmac_type`=VALUES(`blmac_type`),`device_id`=VALUES(`device_id`),`blmac_vlanname`=VALUES(`blmac_vlanname`),`blmac_blocked_ip`=VALUES(`blmac_blocked_ip`),blmac_first_scan_date=NOW(),blmac_scan_date=NOW(),`blmac_done`=VALUES(`blmac_done`),`blmac_info`=VALUES(`blmac_info`) ; ");
						$new_row_id=db_fetch_insert_id();
						db_execute("UPDATE `imb_devices` SET `ip_mac_blocked_total`=(SELECT count(*) FROM imb_blmacs where `device_id`='" . $matches["device_id"] . "') WHERE `device_id`='" . $matches["device_id"] . "';");					
						
						
							 //1. Проверяем - если заблокированный ИП из сети автоудаления (если устройство поддерживает автоудаление) - удалим этот блок
							if (isset($device["setting_imb_use_auto_unblock"]) and $device["setting_imb_use_auto_unblock"] == 1) {
								$net_id = db_fetch_cell("SELECT net_id FROM `imb_auto_updated_nets`  where ((inet_aton('" . $matches["ip"] . "') & `net_mask`)  = `net_ipaddr`) and `net_type`='1' ;");
								if (isset($net_id) and $net_id > 0) {
									// Проверим на флуд. Максимум 5 изменений в 2 минуты и 10 за час.
									$matches["type_op"]=1;
									$flood = imp_check_for_flood ($matches, true);
									
									if (!($flood)) { /*noo flood*/
										//$rezult = api_cli_imp_delete_blmacs($device,$matches);
										$blmacs=db_fetch_assoc("SELECT * FROM imb_blmacs where blmac_id in (" . $new_row_id . ") ;");
										$blmacs_devices=dimpb_array_rekey(db_fetch_assoc("SELECT `d`.`description` as dev_name , d.*, dt.* FROM imb_blmacs b LEFT JOIN imb_devices d on (b.device_id=d.device_id) LEFT JOIN imb_device_types dt on (d.device_type_id = dt.device_type_id) WHERE `b`.`blmac_id` in (" . $new_row_id . ") GROUP by b.device_id;"), "device_id");
										//$rezult = api_imp_delete_blmacs($new_row_id, true);
										foreach ($blmacs as $blmac) {	
											if (isset($blmacs_devices[$blmac["device_id"]])) {
												$rezult = api_imp_delete_blmacs($blmac, $blmacs_devices[$blmac["device_id"]]);
											}
										}
									}
									$log_message = "Auto delete block  Device_ID=[" . $matches["device_id"] . "], IP=[" . $matches["ip"] . "], MAC=[" . $matches["mac"] . "], PORT=[" . $matches["port"] . "], VID=[" . $matches["vlan"] . "], rezult=[" . $rezult . "]";
									if ($rezult == "OK") {
										impb_debug("IMPB: " . $log_message);
										$rezult = true;
									}
									db_store_imp_log("1", $matches["device_id"], "block_del", "0", "0", "0", $log_message, $rezult, 0, $rezult, 0);
								}
							}
							
							 //2. Если устройство поддерживает автосоздание и блок совпадает  - создадим его.
							if (isset($device["setting_imb_use_auto_add"]) and $device["setting_imb_use_auto_add"] == 1) {
								$net_id = db_fetch_cell("SELECT net_id FROM `imb_auto_updated_nets`  where ((inet_aton('" . $matches["ip"] . "') & `net_mask`)  = `net_ipaddr`) and `net_type`='2';");
								//exit;
								if (isset($net_id) and $net_id > 0) {
									// Проверим на флуд. Максимум 5 изменений в 2 минуты и 10 за час.
									$matches["type_op"]=2;
									$flood = imp_check_for_flood ($matches, true);
										
									if (!($flood)) { /*noo flood*/
										//$rezult = api_cli_imp_create_from_blmacs($device,$matches);
										$blmac_row =  db_fetch_row ("SELECT * FROM imb_blmacs WHERE blmac_id=" . $new_row_id . ";");
										
										$rezult = imb_create_imp_record_from_block($matches["device_id"], $matches["mac"], $matches["ip"], $matches["port"], $blmac_row, $impb_imp_mode[$device["setting_imb_def_mode"]],true);
										//если создание привязки - проверим баланс
										
									}
									
									$log_message = "Auto create from block Device_ID=[" . $matches["device_id"] . "], IP=[" . $matches["ip"] . "], MAC=[" . $matches["mac"] . "], PORT=[" . $matches["port"] . "], VID=[" . $matches["vlan"] . "], rezult=[" . ($rezult == "OK" ?  "OK" : "ERROR") . "]";
									if ($rezult == "OK") {
										impb_debug("IMPB: " . $log_message);
										$rezult = true;
										$impb_debug = true;
										$uid = db_fetch_cell(" SELECT lbv.uid FROM imb_auto_updated_nets i LEFT JOIN lb_staff lbs ON (i.net_ipaddr=lbs.segment) " .
										" LEFT JOIN lb_vgroups_s lbv ON (lbs.vg_id=lbv.vg_id) where i.net_id='" . $net_id . "';");
										if (api_plugin_is_enabled('ion') and file_exists($config["base_path"] . '/plugins/ion/ion_functions.php')) {
											include_once($config["base_path"] . '/plugins/ion/ion_functions.php');
											impb_debug("IMPB: Restore balance for net_id=" . $net_id . ", uid=" . $uid);
											$rest_balance = ion_activate_uid($uid);
											impb_debug("IMPB: Poller rezult [" . print_r($rest_balance) . "]");
										}else{
											impb_debug("IMPB: Cancel Restore balance for net_id=" . $net_id . ", uid=" . $uid);
										}										
										db_store_imp_log("1", $matches["device_id"], "balance", "0", "0", "0", print_r($rest_balance), $rezult, 0, $rezult, 0);
										$impb_debug = false;
										db_execute("DELETE FROM `imb_auto_updated_nets` WHERE `net_id`='" . $net_id . "';");
									}else{
										impb_debug("IMPB Error: " . $log_message . " REZ=[" . print_r($rezult) . "]");
									}							
									db_store_imp_log("1", $matches["device_id"], "block_crt", "0", "0", "0", $log_message, $rezult, 0, $rezult, 0);
									//$rezult = cli_imb_create_imp_record_from_block($blmac_record["device_id"], $blmac_record["blmac_macaddr"], $blmac_record["blmac_blocked_ip"], $blmac_record["blmac_port"], $blmac_record, $impb_imp_mode[$block["setting_imb_def_mode"]]);				
								}

							}
							//3. если устройство поддерживает АвтоИзменение привязки и совпадает заблокированный ИП и Порт - изменим привязку - прозрачная смена оборудования у абонента
							if (isset($device["setting_imb_use_auto_change"]) and $device["setting_imb_use_auto_change"] == 1) {
								
							$block = db_fetch_row("SELECT `im`.`macip_id`, `im`.`macip_ipaddr`,`im`.`macip_macaddr`,`ip`.`count_macip_record`, `ib`.`blmac_id`,  `blmac_macaddr`, `blmac_blocked_ip`, `im`.`macip_port_list`, `ib`.`blmac_port` FROM `imb_blmacs` ib " .
								" JOIN imb_macip im ON (`im`.`macip_ipaddr`=`ib`.`blmac_blocked_ip` and `im`.`macip_port_list`=`ib`.`blmac_port` and `im`.`device_id`=`ib`.`device_id`)  " .
								" LEFT JOIN imb_devices id ON (`id`.`device_id`=`ib`.`device_id`)  " .
								" LEFT JOIN imb_device_types idt ON (`idt`.device_type_id=`id`.device_type_id)  " .
								" LEFT JOIN imb_ports ip ON (`id`.`device_id`=`ip`.`device_id` and `ip`.`port_number` = `im`.`macip_port_list`)  " .
								" where `idt`.setting_imb_use_auto_change=1  " .
								" and `blmac_blocked_ip` = '" . $matches["ip"]  . "' " .
								" and `im`.`macip_ipaddr` = '" . $matches["ip"]  . "' " .
								" and (`ip`.`count_macip_record` <= '" . read_config_option("dimpb_max_count_rec_for_auto_change") . "' or LCASE(ip.port_name) like '%ach%')  " .
								" and `blmac_online`='1' ;");							
								
								if (isset($block["macip_id"]) and $block["macip_id"] > 0) {
									// Проверим на флуд. Максимум 5 изменений в 2 минуты и 10 за час.
									$matches["type_op"]=3;
									$flood = imp_check_for_flood ($matches, true);
										
									if (!($flood)) { /*noo flood*/
										//$rezult = api_cli_imp_create_from_blmacs($device,$matches);
										$blmac_row =  db_fetch_row ("SELECT * FROM imb_blmacs WHERE blmac_id=" . $new_row_id . ";");
										$rezult = imb_create_imp_record_from_block($matches["device_id"], $matches["mac"], $matches["ip"], $matches["port"], $blmac_row, $impb_imp_mode[$device["setting_imb_def_mode"]],true);
									}
									
									$log_message = "Auto Change MAC from block  Device_ID=[" . $matches["device_id"] . "], IP=[" . $matches["ip"] . "], MAC=[" . $matches["mac"] . "], PORT=[" . $matches["port"] . "], VID=[" . $matches["vlan"] . "], rezult=[" . $rezult . "]";
									if ($rezult == "OK") {
										impb_debug("IMPB: " . $log_message);
										$rezult = true;
									}
									db_store_imp_log("1", $matches["device_id"], "block_chng", $block["macip_id"], $block["macip_macaddr"], $matches["mac"], $log_message, $rezult, 0, $rezult, 0);
									
									//$rezult = cli_imb_create_imp_record_from_block($blmac_record["device_id"], $blmac_record["blmac_macaddr"], $blmac_record["blmac_blocked_ip"], $blmac_record["blmac_port"], $blmac_record, $impb_imp_mode[$block["setting_imb_def_mode"]]);				
								}

							}
							//4. АвтоПеренос привязки на её порт. Если привязка свободна - перенесем и изменим свободу.
							if (!($rezult)) {
							$block = db_fetch_row("SELECT `im`.`macip_id`, `ib`.`blmac_id`,  `blmac_macaddr`, `blmac_blocked_ip`, `im`.`macip_port_list`, `ib`.`blmac_port` FROM `imb_blmacs` ib " .
								" JOIN imb_macip im ON (`im`.`macip_ipaddr`=`ib`.`blmac_blocked_ip` and `im`.`macip_macaddr`=`ib`.`blmac_macaddr` and `im`.`device_id`=`ib`.`device_id`) " .
								" LEFT JOIN imb_devices id ON (`id`.`device_id`=`ib`.`device_id`) " .
								" LEFT JOIN imb_device_types idt ON (`idt`.device_type_id=`id`.device_type_id) " .
								" WHERE `blmac_blocked_ip` is not null " .
								" and `blmac_blocked_ip` = '" . $matches["ip"]  . "' " .
								" and `im`.`macip_ipaddr` = '" . $matches["ip"]  . "' " .
								" and `blmac_online`='1' " .
								" and `im`.`macip_may_move`='1' ;");					
								if (isset($block["macip_id"]) and $block["macip_id"] > 0) {
									// Проверим на флуд. Максимум 5 изменений в 2 минуты и 10 за час.
									$matches["type_op"]=4;
									$flood = imp_check_for_flood ($matches, true);
										
									if (!($flood)) { /*noo flood*/
										//$rezult = api_cli_imp_create_from_blmacs($device,$matches);
										$blmac_row =  db_fetch_row ("SELECT * FROM imb_blmacs WHERE blmac_id=" . $new_row_id . ";");
										$rezult = imb_create_imp_record_from_block($matches["device_id"], $matches["mac"], $matches["ip"], $matches["port"], $blmac_row, $impb_imp_mode[$device["setting_imb_def_mode"]],true);
									}
									
									
									$log_message = "Auto Change Port from FREE Device_ID=[" . $matches["device_id"] . "], IP=[" . $matches["ip"] . "], MAC=[" . $matches["mac"] . "], PORT=[" . $matches["port"] . "], VID=[" . $matches["vlan"] . "], rezult=[" . $rezult . "]";
									if ($rezult == "OK") {
										db_execute("UPDATE `imb_macip` SET `macip_may_move`=0 WHERE `macip_id`='" . $block["macip_id"] . "';");
										impb_debug("IMPB: " . $log_message);
										$rezult = true;
									}
									db_store_imp_log("1", $matches["device_id"], "block_move", $block["macip_id"], $block["macip_port_list"], $matches["port"], $log_message, $rezult, 0, $rezult, 0);
									
									
								}
							}
							
							
							
						//изменим строку - удален ли блок или нет
						db_execute("UPDATE `imb_blmacs` SET `blmac_done` = " . $matches["type_op"] . ", `blmac_online`=" . ($rezult ? '0':'1') . ", `blmac_info`='" . ($flood ? "flood" : "") . "' WHERE `blmac_id` = '" . $new_row_id . "';");
						db_execute("INSERT INTO imb_cli (device_id, cli_index, cli_ip, cli_mac,cli_port,cli_type,cli_vid) VALUES ('" . $device["device_id"]  . "', '" . $matches["oid"] . "', '" . $matches["ip"] . "', '" . $matches["mac"] . "', '" . $matches["port"] . "', '" . $matches["type_op"] . "', '" . $matches["vlan"] . "'); ");					
						
					
					}

				}//нет информации снмп о блоках со свичи
			 
			 
		}else{
			 impb_debug("IMPB: ERROR: Device with hostname of '$str_hostip' not found in database.  Can not continue.");					
		}
	}
}else{
	cacti_log("IMPB: incorrect parsing. [" . $str_binding . "] stop.", TRUE);
}

$impb_debug = false;
 
?>
