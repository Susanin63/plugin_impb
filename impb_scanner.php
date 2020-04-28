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
 
 /* do NOT run this script through a web browser 
 if (!isset($_SERVER["argv"][0])) {
 	die("<br><strong>This script is only meant to run at the command line.</strong>");
 }
   */
   
 
 $no_http_headers = true;
 
 $dir = dirname(__FILE__);
 chdir($dir);
 
 if (strpos($dir, 'impb') !== false) {
 	chdir('../../');
 }
 
 include("./include/global.php");
  include_once($config["base_path"] . "/lib/snmp.php");
 include_once($config["base_path"] . "/plugins/impb/lib/impb_functions.php");
 include_once($config["base_path"] . "/plugins/impb/lib/impb_vendors.php");
 
 
 
 $scan_date = read_config_option("dimpb_scan_date");
<<<<<<< HEAD
 list($micro,$seconds) = explode(" ", microtime());
=======
 list($micro,$seconds) = split(" ", microtime());
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 $start_time = $seconds + $micro;
 
 /* drop a few environment variables to minimize net-snmp load times */
 putenv("MIBS=RFC-1215");
 ini_set("max_execution_time", "0");
<<<<<<< HEAD
 ini_set("memory_limit", "64M");
=======
 ini_set("memory_limit", "32M");
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 /* process calling arguments */
 $parms = $_SERVER["argv"];
 array_shift($parms);
 
 /* utility requires input parameters */
 if (sizeof($parms) == 0) {
     print "ERROR: You must supply input parameters\n\n";
     display_help();
     //exit;
 }
 
 $device=array(); 
 $device_id = "0";
 
 $lm_debug=FALSE;
 $test_mode = FALSE; 
 //$mt_scan_date = read_config_option("dimpb_scan_date");
 
 
 foreach($parms as $parameter) {
     @list($arg, $value) = @explode("=", $parameter);
 
     switch ($arg) {
     case "-id":
         $device_id = $value;
         break;
     case "-d":
         $debug = TRUE;
         break;
     case "-h":
         display_help();
         exit;
     case "-v":
         display_help();
         exit;
     case "-t":
         $test_mode = TRUE;
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
//$device_id = 3;
 if (!$test_mode) {
     dimpb_db_process_add($device_id, TRUE);
 }
       
 //$mt_scan_date = db_fetch_assoc("SELECT max(scan_date) FROM mac_track_ports");
 //lanmanagement_debug("mt_scan_date = [" . $mt_scan_date . "]");
 //$mt_scan_date = $mt_scan_date["scan_date"];
 //impb_debug("mt_scan_date = [" . $mt_scan_date . "]");
 
 
   /* get device information */
   $device = db_fetch_row("SELECT * FROM imb_devices WHERE device_id =" . $device_id);
 if (sizeof($device) == 0) {
     impb_debug("ERROR: Device with Id of '$device_id' not found in database.  Can not continue.");
     dimpb_db_process_remove($device_id);
     exit;
 }
 
 /* get device types */  
 $device_types = db_fetch_assoc("SELECT * FROM imb_device_types WHERE device_type_id =" . $device["device_type_id"]);
 
 if (sizeof($device_types) == 0) {
     impb_debug("ERROR: No device types with id=[" . $device["device_type_id"] . "] for device [" . $device["device_id"] . "] have been found.");
     dimpb_db_process_remove($device_id);
     exit;
 }
 
 
 /* check the devices read string for validity, set to new if changed */
 if (dimpb_valid_snmp_device($device)) {
     impb_debug("HOST: " . $device["hostname"] . " is alive, processing has begun.");
     $host_up = TRUE;
 $device_type = dimpb_find_scanning_function($device, $device_types); 
     
         /* verify that the scanning function is not null and call it as applicable */
         if (isset($device_type["scanning_function"])) {
             if (strlen($device_type["scanning_function"]) > 0) {
                 if (function_exists($device_type["scanning_function"])) {
                     impb_debug("Scanning function is '" . $device_type["scanning_function"] . "'");
                     $device["device_type_id"] = $device_type["device_type_id"];
					 $device = call_user_func_array($device_type["scanning_function"], array(&$device));
                     impb_debug("ppp------>>call_user_func_array: " . $device_type["scanning_function"] . " dev=" . $device["hostname"] );
                 }else{
                     impb_debug("WARNING: SITE: " . $site . ", IP: " . $device["hostname"] . ", TYPE: " . substr($device["snmp_sysDescr"],0,40) . ", Scanning Function Does Not Exist.");
                     $device["last_runmessage"] = "WARNING: Scanning Function Does Not Exist.";
                     $device["snmp_status"] = HOST_ERROR;
                 }
             }else{
                 impb_debug("WARNING: SITE: " . $site . ", IP: " . $device["hostname"] . ", TYPE: " . substr($device["snmp_sysDescr"],0,40) . ", Scanning Function in Device Type Table Is Null.");
                 $device["last_runmessage"] = "WARNING: Scanning Function in Device Type Table Is Null.";
                 $device["snmp_status"] = HOST_ERROR;
             }
         }else{
             impb_debug("WARNING: IP: " . $device["hostname"] . ", TYPE: " . substr($device["snmp_sysDescr"],0,40) . ", Device Type Not Found in Device Type Table.");
             $device["last_runmessage"] = "WARNING: Device Type Not Found in Device Type Table.";
             $device["snmp_status"] = HOST_ERROR;
         }
 
 }else{
     impb_debug("WARNING: IP: " . $device["hostname"] . ", TYPE: " . substr($device["snmp_sysDescr"],0,40) . ", Device unreachable.");
     $device["last_runmessage"] = "Device unreachable.";
     $host_up = FALSE;
 	db_execute("UPDATE `imb_macip` SET `macip_online`=0 WHERE `device_id`=" . $device["device_id"]);
 	db_execute("UPDATE `imb_ports` SET `port_online`=0 WHERE `device_id`=" . $device["device_id"]);
 	db_execute("UPDATE `imb_blmacs` SET `blmac_online`=0 WHERE `device_id`=" . $device["device_id"]);
 }
 
<<<<<<< HEAD
 impb_db_update_device_status($device, $host_up, $scan_date, $start_time);
=======
 dimpb_db_update_device_status($device, $host_up, $scan_date, $start_time);
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 dimpb_db_process_remove($device_id);
 exit;
 
 /*    display_help - displays the usage of the function */
 function display_help () {
     print "D-Link IP-MAC-Port Blinding Tracker Version 1.0, Copyright 2005 - Susanin\n\n";
     print "usage: xxxxx.php -id=host_id [-d] [-h] [--help] [-v] [--version]\n\n";
     print "-id=host_id   - the mac_track_devices host_id to scan\n";
     print "-d            - Display verbose output during execution\n";
     print "-t            - Test mode, don't log a process id and interfere with system\n";
     print "-v --version  - Display this help message\n";
     print "-h --help     - display this help message\n";
 }
 
 
 ?>
