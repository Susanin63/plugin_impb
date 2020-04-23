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
 
 define("SNMP_METHOD_PHP_SET", 1);
 define("SNMP_METHOD_BINARY_SET", 2);
 
 include_once($config["base_path"] . "/lib/poller.php");
 include_once($config["base_path"] . "/lib/snmp.php");
 

 if ($config["cacti_server_os"] == "unix") {
 	define("SNMP_SET_ESCAPE_CHARACTER", "'");
 }else{
 	define("SNMP_SET_ESCAPE_CHARACTER", "\"");
 }
 
 if (phpversion () < "5"){ // define PHP5 functions if server uses PHP4
 
 function str_split($text, $split = 1) {
 if (!is_string($text)) return false;
 if (!is_numeric($split) && $split < 1) return false;
 $len = strlen($text);
 $array = array();
 $s = 0;
 $e=$split;
 while ($s <$len)
     {
         $e=($e <$len)?$e:$len;
         $array[] = substr($text, $s,$e);
         $s = $s+$e;
     }
 return $array;
 }
 }
 if (! function_exists("array_fill_keys")) {
 	function array_fill_keys($array, $values) {
 	    $arraydisplay = array();
		if(is_array($array)) {
 	        foreach($array as $key => $value) {
 	            $arraydisplay[$array[$key]] = $values;
 	        }
			return $arraydisplay;
 	    }
 	} 
 }
 
 function impb_debug($message) {
	global $debug, $web, $config, $impb_debug;
	include_once($config['base_path'] . '/lib/functions.php');

	if (isset($web) && $web && !substr_count($message, 'SQL')) {
		print('<p>' . $message . '</p>');
	}elseif ($debug) {
		print("DIMPB_DEBUG (" . date("H:i:s") . "): [" . $message . "]\n");
	}

	if ((substr_count($message, "ERROR:")) or ($impb_debug)) {
		cacti_log($message, false, 'IMPB');
	}
}

function impb_check_user_realm($realm_id) {
	if (empty($_SESSION['sess_user_id'])) {
		return FALSE;
	}elseif (!empty($_SESSION['sess_user_id'])) {
		if ((!db_fetch_assoc("select
			user_auth_realm.realm_id
			from
			user_auth_realm
			where user_auth_realm.user_id='" . $_SESSION['sess_user_id'] . "'
			and user_auth_realm.realm_id='$realm_id'")) || (empty($realm_id))) {
			return FALSE;
		}else{
			return TRUE;
		}
	}
}

 function impb_check_changed($request, $session) {
     if ((isset($_REQUEST[$request])) && (isset($_SESSION[$session]))) {
         if ($_REQUEST[$request] != $_SESSION[$session]) {
             return 1;
         }
     }
 }
 
 /* form_alternate_row_color - starts an HTML row with an alternating color scheme
   @arg $row_color1 - the first color to use
   @arg $row_color2 - the second color to use
   @arg $row_value - the value of the row which will be used to evaluate which color
     to display for this particular row. must be an integer
   @arg $row_id - used to allow js and ajax actions on this object
   @returns - the background color used for this particular row */
   
 function impb_form_alternate_row_color($row_color1, $row_color2, $row_value, $row_id = "", $style = "") {
	if (($row_value % 2) == 1) {
			$class='odd';
			$current_color = $row_color1;
	}else{
		if ($row_color2 == '' || $row_color2 == "#E5E5E5") {
			$class = 'even';
		}else{
			$class = 'even-alternate';
		}
		$current_color = $row_color1;
	}

	if (strlen($row_id)) {
		print "<tr class='$class' id='$row_id' " . (strlen($style) ? " style='$style;'" : "") . " >\n";
	}else{
		print "<tr class='$class' " . (strlen($style) ? " style='$style;'" : "") . " >\n";
	}

	return $current_color;
}

  /*	dimpb_valid_snmp_device - This function validates that the device is reachable via snmp.
   It first attempts	to utilize the default snmp readstring.  If it's not valid, it
   attempts to find the correct read string and then updates several system
   information variable. it returns the status	of the host (up=true, down=false)
 */
 /* we must use an apostrophe to escape community names under Unix in case the user uses
 characters that the shell might interpret. the ucd-snmp binaries on Windows flip out when
 you do this, but are perfectly happy with a quotation mark. */
 
 function dimpb_valid_snmp_device(&$device) {
     /* initialize variable */
     $host_up = FALSE;
     $device["snmp_status"] = HOST_DOWN;
 

	/* force php to return numeric oid's */
	cacti_oid_numeric_format();


	$session = cacti_snmp_session($device['hostname'], $device['snmp_get_community'], $device['snmp_get_version'],
		$device['snmp_get_username'], $device['snmp_get_password'], $device['snmp_get_auth_protocol'], $device['snmp_get_priv_passphrase'],
		$device['snmp_get_priv_protocol'], $device['snmp_get_context'], '', $device['snmp_port'],
		$device['snmp_timeout']);

		if ($session !== false) {
			/* Community string is not used for v3 */
			$snmp_sysObjectID = cacti_snmp_session_get($session, '.1.3.6.1.2.1.1.2.0');

			if ($snmp_sysObjectID != 'U') {
				$snmp_sysObjectID = str_replace('enterprises', '.1.3.6.1.4.1', $snmp_sysObjectID);
				$snmp_sysObjectID = str_replace('OID: ', '', $snmp_sysObjectID);
				$snmp_sysObjectID = str_replace('.iso', '.1', $snmp_sysObjectID);

				if ((strlen($snmp_sysObjectID)) &&
					(!substr_count($snmp_sysObjectID, 'No Such Object')) &&
					(!substr_count($snmp_sysObjectID, 'Error In'))) {
					$snmp_sysObjectID = trim(str_replace('"', '', $snmp_sysObjectID));
					$device['snmp_status'] = HOST_UP;
					$host_up = true;
				}
			}
		}

     if ($host_up) {
         $device["snmp_sysObjectID"] = $snmp_sysObjectID;
 
         /* get system name */
 		$snmp_sysName = @dimpb_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.5.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);		
 
         if (strlen($snmp_sysName) > 0) {
             $snmp_sysName = trim(strtr($snmp_sysName,"\""," "));
             $device["snmp_sysName"] = $snmp_sysName;
         }
 
         /* get system location */
 		$snmp_sysLocation = @dimpb_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.6.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);				
 
         if (strlen($snmp_sysLocation) > 0) {
             $snmp_sysLocation = trim(strtr($snmp_sysLocation,"\""," "));
             $device["snmp_sysLocation"] = $snmp_sysLocation;
         }
 
         /* get system contact */
 		$snmp_sysContact = @dimpb_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.4.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);				
 
         if (strlen($snmp_sysContact) > 0) {
             $snmp_sysContact = trim(strtr($snmp_sysContact,"\""," "));
             $device["snmp_sysContact"] = $snmp_sysContact;
         }
 
         /* get system description */
 		$snmp_sysDescr = @dimpb_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.1.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);				
 
         if (strlen($snmp_sysDescr) > 0) {
             $snmp_sysDescr = trim(strtr($snmp_sysDescr,"\""," "));
             $device["snmp_sysDescr"] = $snmp_sysDescr;
         }
 
         /* get system uptime */
 		$snmp_sysUptime = @dimpb_snmp_get($device["hostname"], $device["snmp_get_community"], ".1.3.6.1.2.1.1.3.0", $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);				
 
         if (strlen($snmp_sysUptime) > 0) {
             $snmp_sysUptime = trim(strtr($snmp_sysUptime,"\""," "));
             $device["snmp_sysUptime"] = $snmp_sysUptime;
         }
		 $session->close();
    }else{
		impb_debug('No response');
	}
 
     return $host_up;
 }
 
 function dimpb_find_scanning_function(&$device, &$device_types) {
     $sysDescr_match = FALSE;
     $sysObjectID_match = FALSE;
 
     /* scan all device_types to determine the function to call */
     foreach($device_types as $device_type) {
         /* search for a matching snmp_sysDescr */
 
         if ($device["device_type_id"] == $device_type["device_type_id"])  {
             $device["device_type_id"] = $device_type["device_type_id"];
             return $device_type;
         }
     }
 
     return array();
 }

function dimpb_snmp_get($hostname, $community, $oid, $version, $username, $password, $auth_proto, $priv_pass, $priv_proto, $context, $port = 161, $timeout = 500, $retries = 0, $environ = SNMP_POLLER) {
	global $config;

	/* determine default retries */
	if (($retries == 0) || (!is_numeric($retries))) {
		$retries = read_config_option("snmp_retries");
		if ($retries == "") $retries = 3;
	}

	/* do not attempt to poll invalid combinations */
	if (($version == 0) || (!is_numeric($version)) ||
		(!is_numeric($port)) ||
		(!is_numeric($retries)) ||
		(!is_numeric($timeout)) ||
		(($community == "") && ($version != 3))
		) {
		return "U";
	}

	if ((snmp_get_method($version) == SNMP_METHOD_PHP) &&
		(!strlen($context) || ($version != 3))) {
		/* make sure snmp* is verbose so we can see what types of data
		we are getting back */
		snmp_set_quick_print(0);

		if ($version == "1") {
			$snmp_value = @snmpget("$hostname:$port", "$community", "$oid", ($timeout * 1000), $retries);
		}elseif ($version == "2") {
			$snmp_value = @snmp2_get("$hostname:$port", "$community", "$oid", ($timeout * 1000), $retries);
		}else{
			if ($priv_proto == "[None]" || $priv_pass == '') {
				$proto = "authNoPriv";
				$priv_proto = "";
			}else{
				$proto = "authPriv";
			}

			$snmp_value = @snmp3_get("$hostname:$port", "$username", $proto, $auth_proto, "$password", $priv_proto, "$priv_pass", "$oid", ($timeout * 1000), $retries);
		}

		if ($snmp_value === false) {
			cacti_log("WARNING: DIMPB SNMP Get Timeout for Host:'$hostname', and OID:'$oid'", false);
		}
	}else {
		$snmp_value = '';
		/* ucd/net snmp want the timeout in seconds */
		$timeout = ceil($timeout / 1000);

		if ($version == "1") {
			$snmp_auth = (read_config_option("snmp_version") == "ucd-snmp") ? cacti_escapeshellarg($community): "-c " . cacti_escapeshellarg($community); /* v1/v2 - community string */
		}elseif ($version == "2") {
			$snmp_auth = (read_config_option("snmp_version") == "ucd-snmp") ? cacti_escapeshellarg($community) : "-c " . cacti_escapeshellarg($community); /* v1/v2 - community string */
			$version = "2c"; /* ucd/net snmp prefers this over '2' */
		}elseif ($version == "3") {
			if ($priv_proto == "[None]" || $priv_pass == '') {
				$proto = "authNoPriv";
				$priv_proto = "";
			}else{
				$proto = "authPriv";
			}

			if (strlen($priv_pass)) {
				$priv_pass = "-X " . cacti_escapeshellarg($priv_pass) . " -x " . cacti_escapeshellarg($priv_proto);
			}else{
				$priv_pass = "";
			}

			if (strlen($context)) {
				$context = "-n " . cacti_escapeshellarg($context);
			}else{
				$context = "";
			}

			$snmp_auth = trim("-u " . cacti_escapeshellarg($username) .
				" -l " . cacti_escapeshellarg($proto) .
				" -a " . cacti_escapeshellarg($auth_proto) .
				" -A " . cacti_escapeshellarg($password) .
				" "    . $priv_pass .
				" "    . $context); /* v3 - username/password */
		}

		/* no valid snmp version has been set, get out */
		if (empty($snmp_auth)) { return; }

		if (read_config_option("snmp_version") == "ucd-snmp") {
			/* escape the command to be executed and vulnerable parameters
			 * numeric parameters are not subject to command injection
			 * snmp_auth is treated seperately, see above */
			exec(cacti_escapeshellcmd(read_config_option("path_snmpget")) . " -O vt -v$version -t $timeout -r $retries " . cacti_escapeshellarg($hostname) . ":$port $snmp_auth " . cacti_escapeshellarg($oid), $snmp_value);
		}else {
			exec(cacti_escapeshellcmd(read_config_option("path_snmpget")) . " -O fntevU " . $snmp_auth . " -v $version -t $timeout -r $retries " . cacti_escapeshellarg($hostname) . ":$port " . cacti_escapeshellarg($oid), $snmp_value);
		}

		/* fix for multi-line snmp output */
		if (is_array($snmp_value)) {
			$snmp_value = implode(" ", $snmp_value);
		}
	}

	/* fix for multi-line snmp output */
	if (isset($snmp_value)) {
		if (is_array($snmp_value)) {
			$snmp_value = implode(" ", $snmp_value);
		}
	}

	if (substr_count($snmp_value, "Timeout:")) {
		cacti_log("WARNING: DIMPB SNMP Get Timeout for Host:'$hostname', and OID:'$oid'", false);
	}

	/* strip out non-snmp data */
	$snmp_value = impb_format_snmp_string($snmp_value, false);

	return $snmp_value;
}

 
function dimpb_snmp_walk2($hostname, $community, $oid, $version, $username, $password, $auth_proto, $priv_pass, $priv_proto, $context, $port = 161, $timeout = 500, $retries = 0, $max_oids = 10, $environ = SNMP_POLLER) {
	global $config, $banned_snmp_strings;

	$snmp_oid_included = true;
	$snmp_auth	       = '';
	$snmp_array        = array();
	$temp_array        = array();

	/* determine default retries */
	if (($retries == 0) || (!is_numeric($retries))) {
		$retries = read_config_option("snmp_retries");
		if ($retries == "") $retries = 3;
	}

	/* determine default max_oids */
	if (($max_oids == 0) || (!is_numeric($max_oids))) {
		$max_oids = read_config_option("max_get_size");

		if ($max_oids == "") $max_oids = 10;
	}

	/* do not attempt to poll invalid combinations */
	if (($version == 0) || (!is_numeric($version)) ||
		(!is_numeric($max_oids)) ||
		(!is_numeric($port)) ||
		(!is_numeric($retries)) ||
		(!is_numeric($timeout)) ||
		(($community == "") && ($version != 3))
		) {
		return array();
	}

	if (function_exists("snmp_set_oid_output_format")) {
			snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
	}
	
	$path_snmpbulkwalk = read_config_option("path_snmpbulkwalk");

	if ((snmp_get_method($version) == SNMP_METHOD_PHP) &&
		(!strlen($context) || ($version != 3)) &&
		(($version == 1) ||
		(version_compare(phpversion(), "5.1") >= 0) ||
		(!file_exists($path_snmpbulkwalk)))) {
		/* make sure snmp* is verbose so we can see what types of data
		we are getting back */

		/* force php to return numeric oid's */
		cacti_oid_numeric_format();

		if (function_exists("snmprealwalk")) {
			$snmp_oid_included = false;
		}

		snmp_set_quick_print(0);

		if ($version == "1") {
			$temp_array = @snmprealwalk("$hostname:$port", "$community", "$oid", ($timeout * 1000), $retries);
		}elseif ($version == "2") {
			$temp_array = @snmp2_real_walk("$hostname:$port", "$community", "$oid", ($timeout * 1000), $retries);
		}else{
			if ($priv_proto == "[None]" || $priv_pass == '') {
				$proto = "authNoPriv";
				$priv_proto = "";
			}else{
				$proto = "authPriv";
			}

			$temp_array = @snmp3_real_walk("$hostname:$port", "$username", $proto, $auth_proto, "$password", $priv_proto, "$priv_pass", "$oid", ($timeout * 1000), $retries);
		}

		if ($temp_array === false) {
			cacti_log("WARNING: DIMPB SNMP Walk Timeout for Host:'$hostname', and OID:'$oid'", false);
		}

		/* check for bad entries */
		if (is_array($temp_array) && sizeof($temp_array)) {
		foreach($temp_array as $key => $value) {
			foreach($banned_snmp_strings as $item) {
				if(strstr($value, $item) != "") {
					unset($temp_array[$key]);
					continue 2;
				}
			}
		}
		}

		$o = 0;
		for (@reset($temp_array); $i = @key($temp_array); next($temp_array)) {
			if ($temp_array[$i] != "NULL") {
				$snmp_array[$o]["oid"] = preg_replace("/^\./", "", $i);
				$snmp_array[$o]["value"] = impb_format_snmp_string($temp_array[$i], $snmp_oid_included);
			}
			$o++;
		}
	}else{
		/* ucd/net snmp want the timeout in seconds */
		$timeout = ceil($timeout / 1000);

		if ($version == "1") {
			$snmp_auth = (read_config_option("snmp_version") == "ucd-snmp") ? cacti_escapeshellarg($community): "-c " . cacti_escapeshellarg($community); /* v1/v2 - community string */
		}elseif ($version == "2") {
			$snmp_auth = (read_config_option("snmp_version") == "ucd-snmp") ? cacti_escapeshellarg($community): "-c " . cacti_escapeshellarg($community); /* v1/v2 - community string */
			$version = "2c"; /* ucd/net snmp prefers this over '2' */
		}elseif ($version == "3") {
			if ($priv_proto == "[None]" || $priv_pass == '') {
				$proto = "authNoPriv";
				$priv_proto = "";
			}else{
				$proto = "authPriv";
			}

			if (strlen($priv_pass)) {
				$priv_pass = "-X " . cacti_escapeshellarg($priv_pass) . " -x " . cacti_escapeshellarg($priv_proto);
			}else{
				$priv_pass = "";
			}

			if (strlen($context)) {
				$context = "-n " . cacti_escapeshellarg($context);
			}else{
				$context = "";
			}

			$snmp_auth = trim("-u " . cacti_escapeshellarg($username) .
				" -l " . cacti_escapeshellarg($proto) .
				" -a " . cacti_escapeshellarg($auth_proto) .
				" -A " . cacti_escapeshellarg($password) .
				" "    . $priv_pass .
				" "    . $context); /* v3 - username/password */
		}

		if (read_config_option("snmp_version") == "ucd-snmp") {
			/* escape the command to be executed and vulnerable parameters
			 * numeric parameters are not subject to command injection
			 * snmp_auth is treated seperately, see above */
			$temp_array = exec_into_array(cacti_escapeshellcmd(read_config_option("path_snmpwalk")) . " -v$version -t $timeout -r $retries " . cacti_escapeshellarg($hostname) . ":$port $snmp_auth " . cacti_escapeshellarg($oid));
		}else {
			if (file_exists($path_snmpbulkwalk) && ($version > 1) && ($max_oids > 1)) {
				$temp_array = exec_into_array(cacti_escapeshellcmd($path_snmpbulkwalk) . " -O Qn $snmp_auth -v $version -t $timeout -r $retries -Cr$max_oids " . cacti_escapeshellarg($hostname) . ":$port " . cacti_escapeshellarg($oid));
			}else{
				$temp_array = exec_into_array(cacti_escapeshellcmd(read_config_option("path_snmpwalk")) . " -O Qn $snmp_auth -v $version -t $timeout -r $retries " . cacti_escapeshellarg($hostname) . ":$port " . cacti_escapeshellarg($oid));
			}
		}

		if (substr_count(implode(" ", $temp_array), "Timeout:")) {
			cacti_log("WARNING: DIMPB SNMP Walk Timeout for Host:'$hostname', and OID:'$oid'", false);
		}

		/* check for bad entries */
		if (is_array($temp_array) && sizeof($temp_array)) {
		foreach($temp_array as $key => $value) {
			foreach($banned_snmp_strings as $item) {
				if(strstr($value, $item) != "") {
					unset($temp_array[$key]);
					continue 2;
				}
			}
		}
		}

		for ($i=0; $i < count($temp_array); $i++) {
			if ($temp_array[$i] != "NULL") {
				/* returned SNMP string e.g. 
				 * .1.3.6.1.2.1.31.1.1.1.18.1 = STRING: === bla ===
				 * split off first chunk before the "="; this is the OID
				 */
				if (!(strpos($temp_array[$i], "=") === false)){
					list($oid, $value) = explode("=", $temp_array[$i], 2);
					$snmp_array[$i]["oid"]   = trim($oid);
					$snmp_array[$i]["value"] = impb_format_snmp_string($temp_array[$i], true);
				}
			}
		}
	}

	return $snmp_array;
}



function dimpb_snmp_walk($hostname, $community, $oid, $version, $auth_user = '', $auth_pass = '', $auth_proto = '', $priv_pass = '', $priv_proto = '', $context = '', $port = 161, $timeout = 500, $retries = 0, $max_oids = 10, $environ = SNMP_POLLER,	$engineid = '', $value_output_format = SNMP_STRING_OUTPUT_GUESS) {

	global $config, $banned_snmp_strings, $snmp_error;

	$snmp_error        = '';
	$snmp_oid_included = true;
	$snmp_auth	       = '';
	$snmp_array        = array();
	$temp_array        = array();

	if (!cacti_snmp_options_sanitize($version, $community, $port, $timeout, $retries, $max_oids)) {
		return array();
	}

	$path_snmpbulkwalk = read_config_option('path_snmpbulkwalk');

	if (snmp_get_method('walk', $version, $context, $engineid, $value_output_format) == SNMP_METHOD_PHP) {
		/* make sure snmp* is verbose so we can see what types of data
		we are getting back */

		/* force php to return numeric oid's */
		cacti_oid_numeric_format();

		if (function_exists('snmprealwalk')) {
			$snmp_oid_included = false;
		}

		snmp_set_quick_print(0);

		if ($version == '1') {
			$temp_array = snmprealwalk($hostname . ':' . $port, $community, $oid, ($timeout * 1000), $retries);
		} elseif ($version == 2) {
			$temp_array = snmp2_real_walk($hostname . ':' . $port, $community, $oid, ($timeout * 1000), $retries);
		} else {
			if ($priv_proto == '[None]' || $priv_pass == '') {
				if ($auth_pass == '') {
					$sec_level   = 'noAuthNoPriv';
				} else {
					$sec_level   = 'authNoPriv';
				}
				$priv_proto = '';
			} else {
				$sec_level = 'authPriv';
			}

			$temp_array = snmp3_real_walk($hostname . ':' . $port, $auth_user, $sec_level, $auth_proto, $auth_pass, $priv_proto, $priv_pass, $oid, ($timeout * 1000), $retries);
		}

		/* check for bad entries */
		if ($temp_array !== false && cacti_sizeof($temp_array)) {
			foreach($temp_array as $key => $value) {
				foreach($banned_snmp_strings as $item) {
					if (strstr($value, $item) != '') {
						unset($temp_array[$key]);
						continue 2;
					}
				}
			}

			$o = 0;
			for (reset($temp_array); $i = key($temp_array); next($temp_array)) {
				if ($temp_array[$i] != 'NULL') {
					$snmp_array[$o]['oid'] = preg_replace('/^\./', '', $i);
					$snmp_array[$o]['value'] = format_snmp_string($temp_array[$i], $snmp_oid_included, $value_output_format);
				}
				$o++;
			}
		}
	} else {
		/* ucd/net snmp want the timeout in seconds */
		$timeout = ceil($timeout / 1000);

		if ($version == '1') {
			$snmp_auth = '-c ' . snmp_escape_string($community); /* v1/v2 - community string */
		} elseif ($version == '2') {
			$snmp_auth = '-c ' . snmp_escape_string($community); /* v1/v2 - community string */
			$version = '2c'; /* ucd/net snmp prefers this over '2' */
		} elseif ($version == '3') {
			$snmp_auth = cacti_get_snmpv3_auth($auth_proto, $auth_user, $auth_pass, $priv_proto, $priv_pass, $context, $engineid);
		}

		if (read_config_option('oid_increasing_check_disable') == 'on') {
			$oidCheck = '-Cc';
		} else {
			$oidCheck = '';
		}

		$max_oids = read_config_option('snmp_bulk_walk_size');

		if (file_exists($path_snmpbulkwalk) && ($version > 1) && ($max_oids > 1)) {
			$temp_array = exec_into_array(cacti_escapeshellcmd($path_snmpbulkwalk) .
				' -O QnU'  . ($value_output_format == SNMP_STRING_OUTPUT_HEX ? 'x ':' ') . $snmp_auth .
				' -v '     . $version .
				' -t '     . $timeout .
				' -r '     . $retries .
				' -Cr'     . $max_oids .
				' '        . $oidCheck . ' ' .
				cacti_escapeshellarg($hostname) . ':' . $port . ' ' .
				cacti_escapeshellarg($oid));
		} else {
			$temp_array = exec_into_array(cacti_escapeshellcmd(read_config_option('path_snmpwalk')) .
				' -O QnU' . ($value_output_format == SNMP_STRING_OUTPUT_HEX ? 'x ':' ') . $snmp_auth .
				' -v '     . $version .
				' -t '     . $timeout .
				' -r '     . $retries .
				' '        . $oidCheck . ' ' .
				' '        . cacti_escapeshellarg($hostname) . ':' . $port .
				' '        . cacti_escapeshellarg($oid));
		}

		if (strpos(implode(' ', $temp_array), 'Timeout') !== false) {
			cacti_log("WARNING: SNMP Error:'Timeout', Device:'$hostname', OID:'$oid'", false, 'SNMP', POLLER_VERBOSITY_HIGH);
		}

		/* check for bad entries */
		if (is_array($temp_array) && cacti_sizeof($temp_array)) {
			foreach($temp_array as $key => $value) {
				foreach($banned_snmp_strings as $item) {
					if (strstr($value, $item) != '') {
						unset($temp_array[$key]);
						continue 2;
					}
				}
			}

			$i = 0;
			foreach($temp_array as $index => $value) {
				if (preg_match('/(.*) =.*/', $value)) {
					$snmp_array[$i]['oid']   = trim(preg_replace('/(.*) =.*/', "\\1", $value));
					$snmp_array[$i]['value'] = format_snmp_string($value, true, $value_output_format);
					$i++;
				} else {
					$snmp_array[$i-1]['value'] .= $value;
				}
			}
		}
	}

	return $snmp_array;
}

 
 
 /*    dimpb_xform_standard_indexed_data - This function takes an OID, and a device, and
   optionally an alternate snmp_readstring as input parameters and then walks the
   OID and returns the data in array[index] = value format.
 */
 function dimpb_xform_standard_indexed_data($xformOID, $device, $snmp_readstring = "") {
     /* get raw index data */
     //print ("=== [START1]\n");
 //  if ($snmp_readstring == "") {
 //        $snmp_readstring = $device["snmp_readstring"];
 //        print ("=== snmp_readstring=[". $device["snmp_timeout"] . "]\n");
 //    }
 
     $xformArray = dimpb_snmp_walk($device["hostname"], $device["snmp_get_community"], $xformOID, $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"], $device["snmp_port"], $device["snmp_timeout"]);
 
     $i = 0;
     foreach($xformArray as $xformItem) {
         $perPos = strrpos($xformItem["oid"], ".");
         $xformItemID = substr($xformItem["oid"], $perPos+1);
         $xformArray[$i]["oid"] = $xformItemID;
         //print ("=]=[". $xformArray[$i]["oid"] . "--" . $xformArray[$i]["value"] ."]\n");
		 $xformArray[$i]["value"] = trim(preg_replace ("/^((HEX\-00|HEX\-)\:?)/", "",$xformItem["value"]));
		 
         $i++;
     }
 
     return array_rekey($xformArray, "oid", "value");
 }
 
 function impb_standard_indexed_data_oid($xformOID, &$device, $snmp_readstring = "") {
     /* get raw index data */
     //print ("=== [START1]\n");
 //  if ($snmp_readstring == "") {
 //        $snmp_readstring = $device["snmp_readstring"];
 //        print ("=== snmp_readstring=[". $device["snmp_timeout"] . "]\n");
 //    }
 
     $xformArray = dimpb_snmp_walk($device["hostname"], $device["snmp_get_community"],$xformOID, $device["snmp_get_version"], $device["snmp_get_username"], $device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"], $device["snmp_port"], $device["snmp_timeout"]);
 
                     
                            
     $i = 0;
     foreach($xformArray as $xformItem) {
 
         
         if (substr($xformItem["oid"], 0,1) != ".") {
             $xformItem["oid"] = "." . $xformItem["oid"] ;
         }
 
         if (substr($xformOID, 0,1) != ".") {
             $xformOID = "." . $xformOID ;
         }
         
		// replace output like ".iso.3.6.1.4.1.171.12.23.4.1.1.1.172.19.16.194"  ==>  ".1.3.6.1.4.1.171.12.23.4.1.1.1.172.19.16.194"
         $xformItem["oid"] = str_replace("iso","1",$xformItem["oid"]);
		 //$perPos = strrpos($xformItem["oid"], ".");

		if (substr($xformItem["oid"],0,16) == '.enterprises.171' ) {
			//oid: string = "enterprises.171.11.116.2.2.2.2.1.3.1.100"  ==> ".1.3.6.1.4.1.171.11.116.2.2.2.2.1.3.1.100"
			$xformItem["oid"] = str_replace('.enterprises.171', '.1.3.6.1.4.1.171', $xformItem["oid"]);
		}
		
		$xformItemID = str_replace($xformOID . ".", "", $xformItem["oid"]);
 
 
         $xformArray[$i]["oid"] = $xformItemID;
		 $xformArray[$i]["value"] = trim(preg_replace("/^((HEX\-00|HEX\-)\:?)/", "",$xformItem["value"]));
 		//$xformArray[$i]["value"] = $xformItemID;
         //impb_debug ("=]oid=[". $xformArray[$i]["oid"] . "--=" . $xformArray[$i]["value"] ."]\n");
         $i++;
     }
 	//print_r($xformArray);
     return array_rekey($xformArray, "oid", "value");
 }
 
 
 //создает массив со значением = индексу
 function impb_standard_indexed_data_oid_index($xformOID, &$device, $snmp_readstring = "") {
     /* get raw index data */
     //print ("=== [START1]\n");
 //  if ($snmp_readstring == "") {
 //        $snmp_readstring = $device["snmp_readstring"];
 //        print ("=== snmp_readstring=[". $device["snmp_timeout"] . "]\n");
 //    }
 
     $xformArray = dimpb_snmp_walk($device["hostname"], $device["snmp_get_community"], $xformOID, $device["snmp_get_version"], $device["snmp_get_username"],$device["snmp_get_password"], $device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"], $device["snmp_port"], $device["snmp_timeout"]);
 
                     
                            
     $i = 0;
     foreach($xformArray as $xformItem) {
         
         if (substr($xformItem["oid"], 0,1) != ".") {
             $xformItem["oid"] = "." . $xformItem["oid"] ;
         }
         if (substr($xformOID, 0,1) != ".") {
             $xformOID = "." . $xformOID ;
         }
         //$perPos = strrpos($xformItem["oid"], ".");
        // $xformItemID = substr($xformItem["oid"], $perPos+1);
		// replace output like ".iso.3.6.1.4.1.171.12.23.4.1.1.1.172.19.16.194"  ==>  ".1.3.6.1.4.1.171.12.23.4.1.1.1.172.19.16.194"
         $xformItem["oid"] = str_replace("iso","1",$xformItem["oid"]);
		 
         $xformItemID = str_replace($xformOID . ".", "", $xformItem["oid"]);
         $xformArray[$i]["oid"] = $xformItemID;
         $xformArray[$i]["value"] = $xformItemID;
 //        print ("=]=[". $xformArray[$i]["oid"] . "--" . $xformArray[$i]["value"] ."]\n");
         $i++;
     }
 
     return array_rekey($xformArray, "oid", "value");
 }
 
 function dimpb_array_rekey($array, $key) {
	$ret_array = array();

	if (sizeof($array) > 0) {
	foreach ($array as $item) {
		$ret_array[$item[$key]] = $item;
	}
	}

	return $ret_array;
}

 function dimpb_array_compress_strip($array) {
	$ret_array = array();

	if (sizeof($array) > 0) {
		foreach ($array as $item => $key) {
			$ind = substr ($item, 0, strpos ($item,"."));
			if ((strpos($item, ".100") > 0) or (strpos($item, ".101") > 0)) {
				if (isset($array[$ind . ".101"])){
					$ret_array[$ind] = $array[$ind . ".101"];
				}else{
					$ret_array[$ind] = $array[$ind . ".100"];			
				}			
			}elseif((strpos($item, ".1") > 0) or (strpos($item, ".2") > 0)) {
				if (isset($array[$ind . ".2"])){
					$ret_array[$ind] = $array[$ind . ".2"];
				}else{
					$ret_array[$ind] = $array[$ind . ".1"];			
				}			
			}else{
				$ret_array[$item] = $key;
			}
			
		}
	}

	return $ret_array;
}
 /* 
 function convert_Xport_des_30xx_old($xport) {
     $len_xport=strlen($xport);
     $port_list="";
     for ($i=1;$i<$len_xport;$i++)  {
         $piece=substr($xport, $len_xport-$i, 1);
         if ($piece <> "0") {
         $port=1+4*($i-1);
             if ($piece == "4") {
             $port = $port-1;}
             switch ($piece) {
                 case "1":
                     $port_list .=  $port . ",";                
                     break;
                 case "2":
                     $port_list .=  $port+1 . ",";                
                     break;
                 case "3":
                     $port_list .=  $port . ",";                
                     $port_list .=  $port+1 . ",";                    
                     break;
                 case "4":
                     $port_list .=  $port+3 . ",";                
                     break;
                 case "5":
                     $port_list .=  $port . ",";                
                     $port_list .=  $port+2 . ",";                    
                     break;
                 case "6":
                     $port_list .=  $port+1 . ",";
                     $port_list .=  $port+2 . ",";                                    
                     break;
                 case "7":
                     $port_list .=  $port . ",";                
                     $port_list .=  $port+1 . ",";
                     $port_list .=  $port+3 . ",";                                        
                     break;
                 case "8":
                     $port_list .=  $port+3 . ",";                
                     break;
                 case "9":
                     $port_list .=  $port . ",";                
                     $port_list .=  $port+3 . ",";                    
                     break;
                 case "A":
                     $port_list .=  $port+1 . ",";
                     $port_list .=  $port+3 . ",";                                    
                     break;
                 case "B":
                     $port_list .=  $port . ",";
                     $port_list .=  $port+1 . ",";
 					$port_list .=  $port+3 . ",";
                     break;
 				case "C":
                     $port_list .=  $port+2 . ",";
                     $port_list .=  $port+3 . ",";
                     break;
                 case "D":
                     $port_list .=  $port . ",";
                     $port_list .=  $port+2 . ",";
 					$port_list .=  $port+3 . ",";
                     break;
 				case "E":
                     $port_list .=  $port+1 . ",";
                     $port_list .=  $port+2 . ",";                
                     $port_list .=  $port+3 . ",";                     
                     break;
                 case "F":
                     $port_list .=  $port . ",";
                     $port_list .=  $port+1 . ",";
                     $port_list .=  $port+2 . ",";                
                     $port_list .=  $port+3 . ",";
                     break;
            
             }
         }
     }    
     $port_list = substr($port_list,0,strlen($port_list)-1);
     return $port_list;
 }
  */



/*	dimpb_xform_mac_address - This function will take a variable that is either formated as
  hex or as a string representing hex and convert it to what the mactrack scanning
  function expects.
*/
function dimpb_xform_mac_address($mac_address) {
	if (strlen($mac_address) == 0) {
		$mac_address = "NOT USER";
	}else{
		if (strlen($mac_address) > 10) { /* return is in ascii */
			//$mac_address = str_replace("HEX-00:", "", strtoupper($mac_address));
			//$mac_address = str_replace("HEX-", "", strtoupper($mac_address));
			
			$mac_address = trim(str_replace("\"", "", $mac_address));
			$mac_address = str_replace(" ", ':', $mac_address);
		}else{ /* return is hex */
			$mac = "";
			for ($j = 0; $j < strlen($mac_address); $j++) {
				$mac .= bin2hex($mac_address[$j]) . ':';
			}
			if ((strlen($mac) == 18 ) and (substr($mac, -1) == ":")) {
				$mac = substr($mac, 0, -1);
			}	
			$mac_address = $mac;
		}
	}

	return $mac_address;
}


function dimpb_mac_16_to_10($mac_address) {
	if (strlen($mac_address) == 0) {
		$rez = "0";
	}else{
		$mac_address = str_replace(".", ':', $mac_address);
		$ar = explode (":",$mac_address);
		if (is_array($ar)){
			$rez = "";
			foreach ($ar as $arp) {
				$rez = $rez . "." . base_convert ( $arp , 16 , 10 );
			}
			$rez = substr($rez, 1);			
		}

	}

	return $rez;
}



  
 function imb_convert_port_to_hex($portlist, $type_port_num_conversion, $type_port_use_long, $type_use_more_32x_port = 0) {
 $bol_reverse = true;
 if ($type_port_num_conversion ==  "1") {
 	$bol_reverse = false;
 }
 $portlist = translate_port_view($portlist);
 $str_ports = explode(",", $portlist);
 $rezult = "";
 //$new_str_ports = array();
$port_max = 8 + 8 * $type_use_more_32x_port;

 $arr_ports = array_fill(1, $port_max*4, 0);
 foreach ($str_ports as $key => $str_port) {
     if (substr_count($str_port, "-") > 0) {
 		$temp_ports_string = str_replace("-", ",", trim($str_port));
 		$arr_diapazon = explode(",", $temp_ports_string);
 		for ($i=$arr_diapazon[0];$i<=$arr_diapazon[1];$i++)  {
             $arr_ports[$i]='1';
 		}
 	}else {
 		$arr_ports[$str_port]='1';
 	}
 }
 for ($i=1;$i<=$port_max;$i++)  {
 	$j=(($i-1)*4);
 	if ($bol_reverse) {
 		$port_summ_bin = $arr_ports[$j+4] . $arr_ports[$j+3] . $arr_ports[$j+2] . $arr_ports[$j+1];
 		$rezult = sprintf("%X", bindec($port_summ_bin)) . $rezult;
 	}else{
 		$port_summ_bin = $arr_ports[$j+1] . $arr_ports[$j+2] . $arr_ports[$j+3] . $arr_ports[$j+4];
 		$rezult = $rezult . sprintf("%X", bindec($port_summ_bin));
 	
 	}
 }	
 if ($type_port_use_long ==  "1") {
 	//в зависимости о типа  преобразования портов - вперед или назад добавлять нули
	if ($type_port_num_conversion ==  "1") {
	 	$rezult = $rezult . "00000000"  ;
	}else{
		$rezult = "00000000" . $rezult ;
	}
 }
 return $rezult;
 }
 
 function convert_Xport_to_view_string($xport, $type_port_num_conversion) {
 $bol_reverse = true;
 if ($type_port_num_conversion ==  "3") {
	$port_view=$xport;
	$port_list=$xport;
 }else{
	 if ($type_port_num_conversion ==  "1") {
		$bol_reverse = false;
	 }
	 $arr_xport = str_split($xport);
	 if ($bol_reverse) { /*for DES-30xx*/
		 $arr_xport = array_reverse($arr_xport);
	 }
	 
	 $port_string = "";
	 if ($bol_reverse) { /*for DES-30xx*/ 
		 foreach ($arr_xport as $str_xport) {
				 $port_string = $port_string . strrev(sprintf("%04b", hexdec($str_xport)));
		 }
	 }else{    /*for DES-38xx*/
		 foreach ($arr_xport as $str_xport) {
				 $port_string = $port_string . sprintf("%04b", hexdec($str_xport));
		 }
	 }
	 
	 $arr_port = str_split($port_string);
	 
	 //$arr_port = array_splice($arr_port,27);
	 $arr_rezult=array();
	 foreach ($arr_port as $key => $value) {
		if ($value == 1){
			array_push($arr_rezult, $key+1);
		}
	 }
	 $port_list = implode(",", $arr_rezult);
	 /*next, create port View*/
	 array_push($arr_rezult, 255);
	 $size_arr_rezult = sizeof($arr_rezult)-1;
	 $i = 0;
	 $str_rezult = "";
	 $last_symbol="";
	 while ($i < $size_arr_rezult):
		if ($arr_rezult[$i] == ($arr_rezult[$i+1]-1)) {
			if (($last_symbol == ",") || ($last_symbol == "")) {
				$str_rezult = $str_rezult . $arr_rezult[$i] . "-";
				$last_symbol = "-";
			}
		}else{
			$str_rezult = $str_rezult . $arr_rezult[$i] . ",";
			$last_symbol = ",";
		}
		
		$i++;
	 endwhile;
	 $port_view = substr($str_rezult, 0, strlen($str_rezult)-1);
	 $arr_finish=array();
	 }
 $arr_finish["port_view"]=$port_view;
 $arr_finish["port_list"]=$port_list;
 
 return $arr_finish;
 }
 
 
 /*    impb_db_update_device_status - This function is used by the scanner to save the status
   of the current device including the number of ports, it's readstring, etc.
 */
   function impb_db_update_device_status(&$device, $host_up, $scan_date, $start_time) {
     global $debug;
 
     //list($micro,$seconds) = preg_split("/ /", microtime());
	 list($micro,$seconds) = explode(" ", microtime());
     $end_time = $seconds + $micro;
     $runduration = $end_time - $start_time;
 	
 	//$count_ban_ipmac = db_fetch_cell("SELECT count(*) FROM imb_macip where `device_id`='" . $device["device_id"] . "' and `macip_banned`=1;");
	$count_ban_ipmac = 0;
 
     if ($host_up == TRUE) {
         $update_string = "UPDATE imb_devices " .
            "SET device_type_id='" . $device["device_type_id"] . "'," .
            "ip_mac_total='" . ($device["ip_mac_total"]+$count_ban_ipmac) . "'," .
 			"ip_mac_offline_total='" . ($device["ip_mac_total"]+$count_ban_ipmac)  . "'," .
            "ip_mac_blocked_total='" . $device["ip_mac_blocked_total"] . "'," .            
 			"ip_mac_blocked_offline_total='" . $device["ip_mac_blocked_total"] . "'," .            
            "enable_log_trap='" . $device["enable_log_trap"] . "'," .
 			"enable_dhcp_forw='" . $device["enable_dhcp"] . "'," .
 			"enable_acl_mode='" . $device["enable_acl"] . "'," .
 			"ports_total='" . $device["ports_total"] . "'," .
 			"ports_offline_total='" . $device["ports_total"] . "'," .
            "ports_enable_total='" . $device["ports_enable_total"] . "'," .
 			"ports_offline_enable_total='" . $device["ports_enable_total"] . "'," .
 			"ports_enable_zerroip_total='" . $device["ports_enable_zerroip_total"] . "'," .
            "ports_offline_enable_zerroip_total='" . $device["ports_enable_zerroip_total"] . "'," .
 			"ports_active='" . $device["ports_active"] . "'," .                    
            "snmp_sysName='" . addslashes($device["snmp_sysName"]) . "'," .
            "snmp_sysLocation='" . addslashes($device["snmp_sysLocation"]) . "'," .
            "snmp_sysContact='" . addslashes($device["snmp_sysContact"]) . "'," .
            "snmp_sysObjectID='" . $device["snmp_sysObjectID"] . "'," .
            "snmp_sysDescr='" . addslashes($device["snmp_sysDescr"]) . "'," .
            "snmp_sysUptime='" . $device["snmp_sysUptime"] . "'," .
            "snmp_status='" . $device["snmp_status"] . "'," .
            "last_runmessage='" . $device["last_runmessage"] . "'," .
            "last_rundate='" . $scan_date . "'," .
            "last_runduration='" . round($runduration,4) . "' " .
            "WHERE device_id ='" . $device["device_id"] . "'";
     }else{
         $update_string = "UPDATE imb_devices " .
             "SET snmp_status='" . $device["snmp_status"] . "'," .
             "device_type_id='" . $device["device_type_id"] . "'," .
             "ip_mac_total='0'," .
             "ip_mac_blocked_total='0'," .
 			"ports_enable_zerroip_total='0'," .
             "ports_total='0'," .
             "ports_enable_total='0'," .
             "ports_active='0'," .
             "last_runmessage='Device Unreachable', " .
             "last_runduration='" . round($runduration,4) . "' " .
             "WHERE device_id ='" . $device["device_id"] . "'";
     }
 
     //impb_debug("SQL: " . $update_string);
 
     db_execute($update_string);
 }
 
 
 /*    dimpb_db_process_add - This function adds a process to the process table with the entry
   with the device_id as key.
 */
 function dimpb_db_process_add($device_id, $storepid = FALSE) {
     /* store the PID if required */
     if ($storepid) {
         $pid = getmypid();
     }else{
         $pid = 0;
     }
 
     /* store pseudo process id in the database */
     db_execute("INSERT INTO imb_processes (device_id, process_id, status, start_date) VALUES ('" . $device_id . "', '" . $pid . "', 'Running', NOW())");
 }
 
 /*    dimpb_db_process_remove - This function removes a devices entry from the processes
   table indicating that the device is done processing and the next device may start.
 */
 function dimpb_db_process_remove($device_id) {
     db_execute("DELETE FROM imb_processes WHERE device_id='" . $device_id . "'");
 }
 
 function dimpb_db_store_device_port_results(&$device, $port_array, $scan_date) {
     global $debug;
  $first_row=0;
 
     //$insert_string = "delete from imb_temp_ports where device_id='" .  $device["device_id"] . "'";
     //db_execute($insert_string);
  
     /* output details to database */
             $insert_string = "REPLACE INTO imb_temp_ports " .
                 "(device_id,port_number,port_name,port_adm_state,port_imb_state, port_adm_speed,port_speed,port_adm_LoopPortState,port_LoopVLAN,port_ErrPortReason,port_zerroip_state, port_enab_dhcp_fwd, port_type,port_active,port_max_entry,port_arp_inspection,port_ip_inspection,port_ip_protocol,scan_date)  VALUES ";
                    
     foreach($port_array as $port_value) {
         if ($first_row == 1) {
          $insert_string .= ", ";
         }else{
              $first_row=1;
         }
             
         $insert_string .= "('" .
                $device["device_id"] . "','" .
                $port_value["ifIndex"] . "','" .
                $port_value["ifName"] . "','" .
                $port_value["ifAdminState"] . "','" .
				$port_value["ifState"] . "','" .
				$port_value["ifAdminSpeed"] . "','" .
				$port_value["ifSpeed"] . "','" .
				$port_value["ifLoopState"] . "','" .
				$port_value["ifLoopVlan"] . "','" .
				$port_value["ifErrorReason"] . "','" .
 				$port_value["ifZerroIpState"] . "','" .
 				$port_value["ifDHCPFwdPcktsStates"] . "','" .
                $port_value["ifType"] . "','" .                
                $port_value["ifActive"] . "','" .                                
 				$port_value["ifMaxEntryCount"] . "','" .			
				$port_value["ifARPIspetion"] . "','" .
				$port_value["ifIPIspetion"] . "','" .
				$port_value["ifIPProtocol"] . "','" .
                $scan_date . "')";
 
            // impb_debug("SQL: " . $insert_string);
         }
         $insert_string .= ";";
         db_execute($insert_string);
         //impb_debug("SQL: " . $insert_string);
		db_execute("UPDATE imb_temp_ports tp LEFT JOIN imb_ports p ON (tp.device_id=p.device_id and tp.port_number=p.port_number and tp.port_active<>p.port_status) " .
					"SET tp.port_status_last_change=tp.scan_date " .
					"WHERE p.device_id=" . $device["device_id"] . ";");
 
 		db_execute("UPDATE imb_ports SET port_active=0 WHERE device_id=" . $device["device_id"]);
		
 		$insert_string="INSERT INTO imb_ports (device_id,port_number,port_name,port_adm_state,port_adm_speed,port_speed,port_adm_LoopPortState,port_LoopVLAN,port_ErrPortReason,port_imb_state,port_zerroip_state, port_enab_dhcp_fwd, " .
 			"port_type,port_status,port_active, port_online,macip_temp_id,scan_date,port_status_last_change,count_macip_record,count_scanmac_record_max,count_scanmac_record_cur,port_max_entry,port_arp_inspection,port_ip_inspection,port_ip_protocol ) " .
 			"SELECT " . $device["device_id"] . ", port_number,  imb_temp_ports.port_name,port_adm_state,port_adm_speed,port_speed,port_adm_LoopPortState,port_LoopVLAN,port_ErrPortReason,imb_temp_ports.port_imb_state,imb_temp_ports.port_zerroip_state, imb_temp_ports.port_enab_dhcp_fwd,  " .
 			"port_type, port_active, 1, 1, port_id, IF (port_active=1,`scan_date`,'0000-00-00 00:00:00'), port_status_last_change,0,0,0,port_max_entry,port_arp_inspection,port_ip_inspection,port_ip_protocol  " .
 			"FROM imb_temp_ports " .
 			"WHERE imb_temp_ports.device_id=" . $device["device_id"] . " " . 
 			"ON DUPLICATE KEY UPDATE " .
 			"imb_ports.port_active=1, " .
 			"imb_ports.port_online=1, " .
 			"imb_ports.port_name=imb_temp_ports.port_name, " .
			"imb_ports.port_adm_state=imb_temp_ports.port_adm_state, " .
			"imb_ports.port_adm_speed=imb_temp_ports.port_adm_speed, " .
			"imb_ports.port_speed=imb_temp_ports.port_speed, " .
			"imb_ports.port_adm_LoopPortState=imb_temp_ports.port_adm_LoopPortState, " . 
			"imb_ports.port_LoopVLAN=imb_temp_ports.port_LoopVLAN, " .
			"imb_ports.port_ErrPortReason=imb_temp_ports.port_ErrPortReason, " .
 			"imb_ports.port_imb_state=imb_temp_ports.port_imb_state, " .
 			"imb_ports.port_zerroip_state=imb_temp_ports.port_zerroip_state, " .
 			"imb_ports.port_enab_dhcp_fwd=imb_temp_ports.port_enab_dhcp_fwd, " .
 			"imb_ports.port_type=imb_temp_ports.port_type, " .
 			"imb_ports.port_status=imb_temp_ports.port_active, " .
 			"imb_ports.macip_temp_id=imb_temp_ports.port_id, " .
 			"imb_ports.scan_date=IF(imb_temp_ports.port_active=1,imb_temp_ports.scan_date,imb_ports.scan_date), " .
			"imb_ports.port_status_last_change=IF (imb_temp_ports.port_status_last_change=0,imb_ports.port_status_last_change,imb_temp_ports.port_status_last_change), " .
 			"imb_ports.port_arp_inspection=imb_temp_ports.port_arp_inspection, " .
			"imb_ports.port_ip_inspection=imb_temp_ports.port_ip_inspection, " .
			"imb_ports.port_ip_protocol=imb_temp_ports.port_ip_protocol, " .
			"imb_ports.port_max_entry=imb_temp_ports.port_max_entry; ";
 		db_execute($insert_string);
 		db_execute("DELETE FROM imb_ports WHERE device_id=" . $device["device_id"] . " and port_active=0;");
 
 }
 
function imp_check_for_flood ($row, $cli = false){
global $debug;

	$rezult = true;
	if ($cli) {
		$str_cli = "from CLI";
	}else{
		$str_cli = "from Poller";	
	}


	switch ($row["type_op"]) {
		case "1":
			$str_op =  " AutoDelete ";                
			break;
		case "2":
			$str_op =  " AutoCreate ";                
			break;			
		case "3":
			$str_op =  " AutoChange ";                
			break;			
	}

	 cacti_log("ERROR: IpMacPort imp_check_for_flood " . print_r($row,true) . ".\n");
	$flood_hour = db_fetch_row("SELECT count(*) as cnt, UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(max(`cli_date`)) as lst  FROM `imb_cli`  where `device_id`='" . $row["device_id"] . "' and `cli_index`='" . $row["oid"] . "' and `cli_ip`='" . $row["ip"] . "' and `cli_port`='" . $row["port"] . "' and `cli_type`='" . $row["type_op"] . "' and `cli_date` > DATE_SUB(NOW(),INTERVAL 1 HOUR) ;");
	if ($flood_hour["cnt"] <= 10) {
		$flood_minute = db_fetch_cell("SELECT count(*) FROM `imb_cli`  where `device_id`='" . $row["device_id"] . "' and `cli_index`='" . $row["oid"] . "' and `cli_ip`='" . $row["ip"] . "' and `cli_port`='" . $row["port"] . "' and `cli_type`='" . $row["type_op"] . "' and `cli_date` > DATE_SUB(NOW(),INTERVAL 2 MINUTE) ;");
		if ($flood_minute <= 5) {
			$rezult = false;
		}else{
			impb_debug("IMPB:  ERROR: with " . $str_op . " block " . $str_cli . " IP=[" . $row["ip"] . "], MAC=[" . $row["mac"] . "] - minute flood detect with count=[" . $flood_minute . "]");
		}
	}else{
		if ($flood_hour["lst"] > 1800) {
			//c последнейго изменения прошло 30 минут
			$rezult = false;
		}else{
			impb_debug("IMPB:  ERROR: with " . $str_op . " block " . $str_cli . " IP=[" . $row["ip"] . "], MAC=[" . $row["mac"] . "] - hour flood detect with count=[" . $flood_hour["cnt"] . "] last was = [" . $flood_hour["lst"] . "]");
		}
	} 
	 
 return $rezult;
 }
 
 function db_store_device_imp_results(&$device, $macip_array, $scan_date) {
     global $debug;
  $first_row=0;
 
     //$insert_string = "delete from imb_temp_macip where device_id='" .  $device["device_id"] . "'";
     //db_execute($insert_string);
 
 
 	
     /* output details to database */
             $insert_string = "REPLACE INTO imb_temp_macip " .
                 "(device_id,macip_index,macip_macaddr,macip_ipaddr,macip_port_hex,macip_port_list,macip_port_view,macip_imb_status,macip_imb_action,macip_mode,scan_date)  VALUES ";
                    
     foreach($macip_array as $macip_value) {
         if ($first_row == 1) {
          $insert_string .= ", ";
         }else{
              $first_row=1;
         }
         $insert_string .= "('" .
                 $device["device_id"] . "','" .
                 $macip_value["macIndex"] . "','" .
                 $macip_value["macMac"] . "','" .
                 $macip_value["macIp"] . "','" .
                 $macip_value["macPort"] . "','" .                
                 $macip_value["macPortList"] . "','" .                                
 				$macip_value["macPortListView"] . "','" .                                
                 $macip_value["macStatus"] . "','" .
 				$macip_value["macAction"] . "','" .
                 $macip_value["macMode"] . "','" .
                 $scan_date . "')";
 
            // impb_debug("SQL: " . $insert_string);
         }
         $insert_string .= ";";
         db_execute($insert_string);
         //impb_debug("SQL: " . $insert_string);
 
 		//Проверка на наличие записей на устройстве, которые должны быть в бане. 
 		$banip_ips = db_fetch_assoc("SELECT imb_macip.macip_id,imb_macip.macip_ipaddr, imb_macip.device_id, imb_temp_macip.macip_id  as tempmacip_id FROM imb_macip  join imb_temp_macip " .
 			" on (INET_ATON(imb_temp_macip.macip_ipaddr) = INET_ATON(imb_macip.macip_ipaddr)) and (imb_temp_macip.device_id=imb_macip.device_id) " .
 			" where  imb_macip.macip_banned=1 and imb_temp_macip.device_id=" . $device["device_id"]  . ";");
 		foreach($banip_ips as $key => $banip_ip) {
 			api_imp_delete_temp_macip($banip_ip["tempmacip_id"]);
 		}
 	
 		db_execute("UPDATE imb_macip SET macip_active=0 WHERE device_id=" . $device["device_id"] . " and `macip_banned` = '0';");
 		$insert_string="INSERT INTO imb_macip (macip_active,device_id,macip_index,macip_macaddr,macip_ipaddr, " .
 			"macip_port_hex,macip_port_list,macip_port_view,macip_imb_status,macip_imb_action,macip_mode,macip_working_now,macip_temp_id,macip_online, " .
 			"macip_first_scan_date,macip_count_scan) " .
 			"SELECT 1," . $device["device_id"] . ", imb_temp_macip.macip_index,imb_temp_macip.macip_macaddr,imb_temp_macip.macip_ipaddr, " .
 			"imb_temp_macip.macip_port_hex,imb_temp_macip.macip_port_list,imb_temp_macip.macip_port_view,imb_temp_macip.macip_imb_status,imb_temp_macip.macip_imb_action,imb_temp_macip.macip_mode,0,imb_temp_macip.macip_id,1, " .
 			"imb_temp_macip.scan_date,1 " .
 			"FROM imb_temp_macip " .
 			"WHERE imb_temp_macip.device_id=" . $device["device_id"] . " " . 
 			"ON DUPLICATE KEY UPDATE " .
 			"macip_active=1, " .
 			"macip_online=1, " .
 			"macip_macaddr=imb_temp_macip.macip_macaddr, " .
 			"macip_port_view=imb_temp_macip.macip_port_view, " .
 			"macip_port_hex=imb_temp_macip.macip_port_hex, " .
 			"macip_port_list=imb_temp_macip.macip_port_list, " .
 			"macip_count_scan=macip_count_scan+1, " .
 			"macip_imb_status=imb_temp_macip.macip_imb_status, " .
 			"macip_imb_action=imb_temp_macip.macip_imb_action, " .
 			"macip_mode=imb_temp_macip.macip_mode, " .
 			"macip_temp_id=imb_temp_macip.macip_id;";
 		db_execute($insert_string);
 		db_execute("UPDATE imb_macip SET macip_online=1 WHERE device_id=" . $device["device_id"] . " and `macip_banned` = '1';");
 		db_execute("DELETE FROM imb_macip WHERE device_id=" . $device["device_id"] . " and macip_active=0;");
 		
 }
 function db_store_device_blocked_imp_results(&$device, $blocked_macip_array, $scan_date) {
     global $debug;
 
  $first_row=0;
  
     //$insert_string = "delete from imb_temp_blmacs where device_id='" .  $device["device_id"] . "'";
     //db_execute($insert_string);
     /* output details to database */
 	//$insert_string = "UPDATE imb_temp_blmacs SET blmac_active=0 WHERE device_id=" . $device["device_id"] . ";";
 	//db_execute($insert_string);
 	
 	$insert_string = "INSERT INTO imb_temp_blmacs " .
                 "(device_id,blmac_index,blmac_macaddr,blmac_ip,blmac_port,blmac_type,blmac_vid,blmac_vlanname,blmac_scan_date)  VALUES ";
                    
     foreach($blocked_macip_array as $blocked_macip_value) {
         if ($first_row == 1) {
          $insert_string .= ", ";
         }else{
              $first_row=1;
         }
         $insert_string .= "('" .
 				        $device["device_id"] . "','" .
                 $blocked_macip_value["blMacIndex"] . "','" .
                 $blocked_macip_value["blMacMac"] . "','" .
                 $blocked_macip_value["blMacIP"] . "','" .
				 $blocked_macip_value["blMacPort"] . "','" .
                 $blocked_macip_value["blMacType"] . "','" .                
                 $blocked_macip_value["blMacVid"] . "','" .                                
                 $blocked_macip_value["blMacVlanName"] . "','" .
 				$scan_date . "')";
         }
         $insert_string .= ";";
         db_execute($insert_string);
 		
 		//db_execute("DELETE FROM imb_temp_blmacs WHERE device_id=" . $device["device_id"] . " and blmac_active=0;");
         //impb_debug("SQL: " . $insert_string);         
 		
 		db_execute("UPDATE imb_blmacs SET blmac_active=0 WHERE device_id=" . $device["device_id"]);
 		$insert_string="INSERT INTO imb_blmacs (blmac_active, blmac_online, device_id, blmac_temp_id, " .
 			"blmac_index, blmac_macaddr, blmac_port, blmac_type, blmac_vid, blmac_vlanname, " .
 			"blmac_first_scan_date, blmac_scan_date, blmac_count_scan) " .
 			"SELECT 1,1," . $device["device_id"] . ", imb_temp_blmacs.blmac_id, " .
 			"imb_temp_blmacs.blmac_index, imb_temp_blmacs.blmac_macaddr, imb_temp_blmacs.blmac_port, imb_temp_blmacs.blmac_type, imb_temp_blmacs.blmac_vid, imb_temp_blmacs.blmac_vlanname, " .
 			"imb_temp_blmacs.blmac_scan_date, imb_temp_blmacs.blmac_scan_date, 1 " .
 			"FROM imb_temp_blmacs " .
 			"WHERE imb_temp_blmacs.device_id=" . $device["device_id"] . " " . 
 			"ON DUPLICATE KEY UPDATE " .
 			"imb_blmacs.blmac_active=1, " . 
 			"imb_blmacs.blmac_online=1, " . 
 			"imb_blmacs.blmac_port=imb_temp_blmacs.blmac_port, " . 
 			"imb_blmacs.blmac_vlanname=imb_temp_blmacs.blmac_vlanname, " . 
 			"imb_blmacs.blmac_count_scan=imb_blmacs.blmac_count_scan+1, " . 
			"imb_blmacs.blmac_type=1, " .
			"imb_blmacs.blmac_done=0, " .
			//"imb_blmacs.blmac_info=REPLACE(imb_blmacs.blmac_info,'syslog', 'poller'), " .
 			"imb_blmacs.blmac_scan_date=imb_temp_blmacs.blmac_scan_date;";
 		db_execute($insert_string);
 		db_execute("DELETE FROM imb_blmacs WHERE device_id=" . $device["device_id"] . " and blmac_active=0 and blmac_done=0;");
 		$insert_string = "insert into imb_temp_blmacinfo (blmacinfo_info_id, blmacinfo_cor_ip, blmacinfo_cor_portlist, blmacinfo_banned) " .
             " select imb_blmacs.blmac_id, imb_macip.macip_ipaddr ,  imb_macip.macip_port_list,imb_macip.macip_banned   from imb_blmacs LEFT JOIN imb_macip " .
             " ON imb_blmacs.device_id = imb_macip.device_id and imb_blmacs.blmac_macaddr = imb_macip.macip_macaddr " .
             " where imb_macip.macip_macaddr is not null and imb_macip.device_id= " .  $device["device_id"]  . ";";
 		db_execute($insert_string);		
 }
 

   function impb_db_store_vlans_results(&$device, $vlans_array, $scan_date) {
     global $debug;
    $first_row=0;
 
     //$insert_string = "delete from imb_temp_macip where device_id='" .  $device["device_id"] . "'";
     //db_execute($insert_string);
 
	
	db_execute("UPDATE imb_vlans SET vlans_active=0 WHERE device_id=" . $device["device_id"] . " ;");
	
	$insert_string="INSERT INTO imb_vlans (device_id,vlan_name,vlan_id,members_ports, " .
		"uttagget_ports,tagget_ports,forbidden_ports,vlans_scan_date,vlans_active) VALUES "; 
	foreach($vlans_array as $vl_id => $vlan) {
		 if ($first_row == 1) {
		  $insert_string .= ", ";
		 }else{
			  $first_row=1;
		 }
		 $insert_string .= "('" .
				$device["device_id"] . "','" .
				$vlan["name"] . "','" .
				$vlan["id"] . "','" .
				$vlan["m_p"] . "','" .
				$vlan["u_p"] . "','" .                
				$vlan["t_p"] . "','" .                                
				$vlan["f_p"] . "','" .                                
				$scan_date . "','1')";
 
			// impb_debug("SQL: " . $insert_string);
	}			
		
	$insert_string .= " ON DUPLICATE KEY UPDATE  vlan_name=VALUES(vlan_name),members_ports=VALUES(members_ports),uttagget_ports=VALUES(uttagget_ports),tagget_ports=VALUES(tagget_ports),forbidden_ports=VALUES(forbidden_ports);";
	db_execute($insert_string);			
		
	db_execute("DELETE FROM imb_vlans WHERE device_id=" . $device["device_id"] . " and vlans_active=0;");
 		
 }
 
 function db_store_imp_log($log_poller,$log_device_id,$log_object,$log_object_id,$log_old_value,$log_new_value,$log_message,$log_rezult_short=0,$log_rezult=0,$log_check_rezult_short=0,$log_check_rezult=0) {
 //РµСЃР»Рё РїРѕР»Р»РµСЂ
 if (!(isset($_SESSION["sess_user_id"]))) {
	$_SESSION["sess_user_id"] = 0;
 }
 
 if(($log_object_id === NULL) or (is_null($log_object_id))) {
	$log_object_id = 0;
 }
 
 $user_full_name = db_fetch_cell("SELECT full_name FROM user_auth WHERE id='" . $_SESSION["sess_user_id"] . "';");
 
 $insert_string="INSERT INTO imb_log (log_user_id,log_poller,log_device_id,log_object,log_object_id,log_old_value,log_new_value,log_message,log_rezult_short,log_rezult,log_check_rezult_short,log_check_rezult) " . 
 "VALUES ('" . $_SESSION["sess_user_id"] . "', '" . $log_poller . "', '" . $log_device_id  . "', '" . $log_object  . "', '" . $log_object_id . "', '" . $log_old_value . "', '" . $log_new_value . "', '" . $log_message . "', '" . $log_rezult_short . "', '" . $log_rezult . "', '" . $log_check_rezult_short . "', '" . $log_check_rezult . "')";
 
 db_execute($insert_string);
 
 }
 
 
 
 
 
 
 function run_poller_impb($device_id) {
 global  $config;
 
 $exit_impblinding = FALSE;
 $current=0;
 
     $command_string = read_config_option("path_php_binary"); 
     $extra_args = " -q " . $config["base_path"] . "/plugins/impb/poller_impb.php -id=" . $device_id ;
     exec_background($command_string, $extra_args);
     sleep(2);
     /* wait for last process to exit */
         $processes_running = db_fetch_cell("SELECT count(*) FROM imb_processes WHERE device_id = '" . $device_id . "'");
         while (($processes_running > 0) && (!$exit_impblinding)) {
             $processes_running = db_fetch_cell("SELECT count(*) FROM imb_processes WHERE device_id = '" . $device_id . "'");
 
             /* wait the correct number of seconds for proccesses prior to
                attempting to update records */
             sleep(2);
 
             /* take time to check for an exit condition */
             //list($micro,$seconds) = preg_split("/ /", microtime());
			 list($micro,$seconds) = explode(" ", microtime());
             $current = $seconds + $micro;
 
             /* exit if we've run too long */
  //           if (($current - $start) > $max_run_duration) {
  //              $exit_impblinding = TRUE;
  //               cacti_log("ERROR: IpMacPort Blinding timed out during main script processing.\n");
  //               break;
  //           }
 
             impb_debug("Waiting on " . $processes_running . " to complete prior to exiting.");
             //Print("Waiting on " . $processes_running . " to complete prior to exiting.");
         }
         
 }
 
 function impb_draw_actions_dropdown($actions_array, $actions_type, $def_choice="1") {
     global $config;
     ?>
     <table align='center' width='98%'>
         <tr>
             <td width='1' valign='top'>
                 <img src='<?php echo $config['url_path']; ?>images/arrow.gif' alt='' align='absmiddle'>&nbsp;
             </td>
             <td align='right'>
                 Выберите действие:
                 <?php form_dropdown("drp_action",$actions_array,"","",$def_choice,"","");?>
             </td>
             <td width='1' align='right'>
				 <input type='submit'  style="width: 85px"  name='go' value='Далее >>'>
             </td>
         </tr>
     </table>
     <input type='hidden' name='action' value='actions_<?php echo $actions_type; ?>'>
     <?php
 }
 
 
 function imp_snmp_set($hostname, $community, $oid, $val_type, $value, $version, $username, $password, $auth_proto, $priv_pass, $priv_proto, $context, $port = 161, $timeout = 500, $retries = 0, $environ = SNMP_POLLER) {
 	global $config;
 
 	/* determine default retries */
 	if (($retries == 0) || (!is_numeric($retries))) {
 		$retries = read_config_option("snmp_retries");
 		if ($retries == "") $retries = 3;
 	}
 
 	/* do not attempt to poll invalid combinations */
 	if (($version == 0) || (($community == "") && ($version != 3))) {
 		return "U";
 	}
 
 	if (snmp_set_method($version) == SNMP_METHOD_PHP_SET) {
 		/* make sure snmp* is verbose so we can see what types of data
 		we are getting back */
 		snmp_set_quick_print(0);
 
 		if ($version == "1") {
 			$snmp_value = @snmpset("$hostname:$port", "$community", "$oid", "$val_type", "$value", ($timeout * 1000), $retries);
 		}elseif ($version == "2") {
 			$snmp_value = snmp2_set("$hostname:$port", "$community", "$oid", "$val_type", "$value", ($timeout * 1000), $retries);
 		}else{
 			if ($priv_proto == "[None]") {
 				$proto = "authNoPriv";
 				$priv_proto = "";
 			}else{
 				$proto = "authPriv";
 			}
 			$snmp_value = @snmp3_set("$hostname:$port", "$username", $proto, $auth_proto, "$password", $priv_proto, "$priv_pass", "$oid",  $val_type, $value, ($timeout * 1000), $retries);
 		}
 	}else {
 		/* ucd/net snmp want the timeout in seconds */
 		$timeout = ceil($timeout / 1000);
 
 		if ($version == "1") {
 			$snmp_auth = (read_config_option("snmp_version") == "ucd-snmp") ? snmp_escape_string($community): "-c " . snmp_escape_string($community); /* v1/v2 - community string */			
 		}elseif ($version == "2") {
 			$snmp_auth = (read_config_option("snmp_version") == "ucd-snmp") ? snmp_escape_string($community) : "-c " . snmp_escape_string($community); /* v1/v2 - community string */			
 			$version = "2c"; /* ucd/net snmp prefers this over '2' */
 		}elseif ($version == "3") {
 			if ($priv_proto == "[None]") {
 				$proto = "authNoPriv";
 				$priv_proto = "";
 			}else{
 				$proto = "authPriv";
 			}
 
 			if (strlen($priv_pass)) {
 				$priv_pass = "-X " . snmp_escape_string($priv_pass) . " -x " . snmp_escape_string($priv_proto);
 			}else{
 				$priv_pass = "";
 			}
 
 			if (strlen($context)) {
 				$context = "-n " . snmp_escape_string($context);
 			}else{
 				$context = "";
 			}
 
 			$snmp_auth = trim("-u " . snmp_escape_string($username) .
 				" -l " . snmp_escape_string($proto) .
 				" -a " . snmp_escape_string($auth_proto) .
 				" -A " . snmp_escape_string($password) .
 				" "    . $priv_pass .
 				" "    . $context); /* v3 - username/password */
 		}			
 
 		/* no valid snmp version has been set, get out */
 		if (empty($snmp_auth)) { return; }
 
 		if (read_config_option("snmp_version") == "ucd-snmp") {
 			exec(read_config_option("dimpb_path_snmpset") . " -O vt -v$version -t $timeout -r $retries $hostname:$port $snmp_auth $oid $val_type $value", $snmp_value);
 		}else {
 			exec(read_config_option("dimpb_path_snmpset") . " -O fntev $snmp_auth -v $version -t $timeout -r $retries $hostname:$port $oid $val_type $value", $snmp_value);
 		}
 	}
 
 	if (isset($snmp_value)) {
 		/* fix for multi-line snmp output */
 		if (is_array($snmp_value)) {
 			$snmp_value = implode(" ", $snmp_value);
 		}
 	}
 
 	/* strip out non-snmp data */
 	$snmp_value = impb_format_snmp_string($snmp_value, true);
 
 	return $snmp_value;
 }
 
 
 
  function imp_snmp_set_combo($hostname, $community, $oid1, $val_type1, $value1, $oid2, $val_type2, $value2, $version, $username, $password, $auth_proto, $priv_pass, $priv_proto, $context, $port = 161, $timeout = 500, $retries = 0, $environ = SNMP_POLLER) {
 	global $config;
 
 	/* determine default retries */
 	if (($retries == 0) || (!is_numeric($retries))) {
 		$retries = read_config_option("snmp_retries");
 		if ($retries == "") $retries = 3;
 	}
 
 	/* do not attempt to poll invalid combinations */
 	if (($version == 0) || (($community == "") && ($version != 3))) {
 		return "U";
 	}
 
 		/* ucd/net snmp want the timeout in seconds */
 		$timeout = ceil($timeout / 1000);
 
 		if ($version == "1") {
 			$snmp_auth = (read_config_option("snmp_version") == "ucd-snmp") ? snmp_escape_string($community): "-c " . snmp_escape_string($community); /* v1/v2 - community string */			
 		}elseif ($version == "2") {
 			$snmp_auth = (read_config_option("snmp_version") == "ucd-snmp") ? snmp_escape_string($community) : "-c " . snmp_escape_string($community); /* v1/v2 - community string */			
 			$version = "2c"; /* ucd/net snmp prefers this over '2' */
 		}elseif ($version == "3") {
 			if ($priv_proto == "[None]") {
 				$proto = "authNoPriv";
 				$priv_proto = "";
 			}else{
 				$proto = "authPriv";
 			}
 
 			if (strlen($priv_pass)) {
 				$priv_pass = "-X " . snmp_escape_string($priv_pass) . " -x " . snmp_escape_string($priv_proto);
 			}else{
 				$priv_pass = "";
 			}
 
 			if (strlen($context)) {
 				$context = "-n " . snmp_escape_string($context);
 			}else{
 				$context = "";
 			}
 
 			$snmp_auth = trim("-u " . snmp_escape_string($username) .
 				" -l " . snmp_escape_string($proto) .
 				" -a " . snmp_escape_string($auth_proto) .
 				" -A " . snmp_escape_string($password) .
 				" "    . $priv_pass .
 				" "    . $context); /* v3 - username/password */
 		}			
 
 		/* no valid snmp version has been set, get out */
 		if (empty($snmp_auth)) { return; }
 
 		if (read_config_option("snmp_version") == "ucd-snmp") {
 			exec(read_config_option("dimpb_path_snmpset") . " -O vt -v$version -t $timeout -r $retries $hostname:$port $snmp_auth $oid1 $val_type1 $value1 $oid2 $val_type2 $value2", $snmp_value);
 		}else {
 			exec(read_config_option("dimpb_path_snmpset") . " -O fntev $snmp_auth -v $version -t $timeout -r $retries $hostname:$port $oid1 $val_type1 $value1 $oid2 $val_type2 $value2", $snmp_value);
 		}

 	if (isset($snmp_value)) {
 		/* fix for multi-line snmp output */
 		if (is_array($snmp_value)) {
 			$snmp_value = implode(" ", $snmp_value);
 		}
 	}
 
 	/* strip out non-snmp data */
 	$snmp_value = impb_format_snmp_string($snmp_value, true);
 
 	return $snmp_value;
 }
 
 
 
 function snmp_set_method($version = 1) {
 	if ((function_exists("snmpgset")) && ($version == 1)) {
 		return SNMP_METHOD_PHP_SET;
 	}else if ((function_exists("snmp2_set")) && ($version == 2) && (PHP_VERSION_ID < 50417)) {
 		return SNMP_METHOD_PHP_SET; //not working on php 5.4

 	}else if ((function_exists("snmp3_set")) && ($version == 3)) {
 		return SNMP_METHOD_PHP_SET;
 	}else if ((($version == 2) || ($version == 3)) && (file_exists(read_config_option("dimpb_path_snmpset")))) {
 		return SNMP_METHOD_BINARY_SET;
 	}else if (function_exists("snmpset")) {
 		/* last resort (hopefully it isn't a 64-bit result) */
 		return SNMP_METHOD_PHP_SET;
 	}else if (file_exists(read_config_option("dimpb_path_snmpset"))) {
 		return SNMP_METHOD_BINARY_SET;
 	}else{
 		/* looks like snmp is broken */
 		return SNMP_METHOD_BINARY_SET;
 	}
 }
 
if (! function_exists("snmp_escape_string")) { 
	function snmp_escape_string($string) {
		global $config;

		if (! defined("SNMP_ESCAPE_CHARACTER")) {
			if ($config["cacti_server_os"] == "win32") {
				define("SNMP_ESCAPE_CHARACTER", "\"");
			}else{
				define("SNMP_ESCAPE_CHARACTER", "'");
			}
		}

		if (substr_count($string, SNMP_ESCAPE_CHARACTER)) {
			$string = substr_replace(SNMP_ESCAPE_CHARACTER, "\\" . SNMP_ESCAPE_CHARACTER, $string);
		}

		return SNMP_ESCAPE_CHARACTER . $string . SNMP_ESCAPE_CHARACTER;
	}
}

 function imb_set_and_check($device, $oid, $val_type, $value, $type_change, $message, $need_check = true, $cellpading = true, $banned = false){
 $rezult = array();
 $rezult["step_rez"] = "Error";
 $rezult["check_rez"] = "Error";
 $rezult["rezult_final"] = "Error";
 
 
 	if ($banned == false) {
 		//Во время сохранения конфига свитч не отвечает и происходит автоматический повтор команд. В результате уходят 4 команды на сохранение и оно длиться очень долго.
 		if ($type_change == "save_config") {
 			$retries=1;
 			$snmp_timeout = $device["snmp_timeout"]*$device["snmp_timeout_agentSaveCfg"];
 		}else{
 			$retries=0;
 			$snmp_timeout = $device["snmp_timeout"];
 		}
 			
 			$rezult["step_data"] = imp_snmp_set($device["hostname"], $device["snmp_set_community"], $oid, $val_type, $value, $device["snmp_set_version"],$device["snmp_set_username"],$device["snmp_set_password"],$device["snmp_set_auth_protocol"], $device["snmp_set_priv_passphrase"], $device["snmp_set_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $snmp_timeout, $retries);
 			switch ($type_change){
 				case 'mac':
 					$rezult["step_rez"] = (((strtolower(str_replace(":", "",$rezult["step_data"] )) == strtolower($value)) || ($rezult["step_data"] == '1'))? "OK" : "Error");
 					break;
 				case 'status':
 					$rezult["step_rez"] = ((($rezult["step_data"] == $value) || ($rezult["step_data"] == '1')) ? "OK" : "Error");
 					break;			
 				case 'port':
 					$rezult["step_rez"] = (((strtolower(str_replace(":", "",$rezult["step_data"] )) == strtolower($value)) || ($rezult["step_data"] == '1')) ? "OK" : "Error");
 					break;
 				case 'mode':
 					$rezult["step_rez"] = ((($rezult["step_data"] == $value) || ($rezult["step_data"] == '1')) ? "OK" : "Error");
 					break;			
 				case 'port_state':
 					$rezult["step_rez"] =  ((($rezult["step_data"] == impb_format_snmp_string($value, true)) || ($rezult["step_data"] == '1')) ? "OK" : "Error");
 					break;						
 				case 'port_name':
 					$rezult["step_rez"] = ((($rezult["step_data"] == impb_format_snmp_string($value, true)) || ($rezult["step_data"] == '1')) ? "OK" : "Error");
 					break;	
 				case 'save_config':
 					$rezult["step_rez"] = ((($rezult["step_data"] == impb_format_snmp_string($value, true)) || ($rezult["step_data"] == '1')) ? "OK" : "Error");
 					sleep(2);
 					break;	
 				case 'del_macip':
 					$rezult["step_rez"] = ((($rezult["step_data"] == impb_format_snmp_string($value, true) || ($rezult["step_data"] == '1'))) ? "OK" : "Error");
 					break;
 				case 'del_blmac':
 					$rezult["step_rez"] = ((($rezult["step_data"] == impb_format_snmp_string($value, true)) || ($rezult["step_data"] == '1')) ? "OK" : "Error");
 					break;					
 			}
 			
 			//if ((($need_check == true) && ($rezult["step"] == "OK") ) || ()) {
 			if ($need_check == true)  {
 				$rezult["check_data"] = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$oid, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
				$rezult["check_data"] = trim(preg_replace("/^((HEX\-00|HEX\-)\:?)/", "",$rezult["check_data"]));
				
				switch ($type_change){
 					case 'mac':
 						$rezult["check_rez"] = ((strtolower(str_replace(":", "",$rezult["check_data"] )) == strtolower($value)) ? "OK" : "Error");
 						break;
 					case 'status':
 						$rezult["check_rez"] = (($rezult["check_data"] == "1") ? "OK" : "Error");					
 						break;			
 					case 'port':
						$rezult["check_data"] = str_replace(" ", "", $rezult["check_data"]);
 						$rezult["check_rez"] = ((strtolower(str_replace(":", "",$rezult["check_data"] )) == strtolower($value)) ? "OK" : "Error");
						//DGS check
						if (($rezult["check_rez"] == "Error") and ($device["impb_func_version"] >= 39)) {
							$temp_ports = "";
							//check for DGS-3120 to long answer
							if (strlen($rezult["check_data"]) > 47) {
								$rezult["check_data"] = substr($rezult["check_data"],0,47);
								$rezult["check_data"] = str_replace(":", "", str_replace(" ","",str_replace("Hex- ", "", $rezult["check_data"])));
							}
							
							$temp_ports = convert_Xport_to_view_string($rezult["check_data"], $device["type_port_num_conversion"] );
							$temp_ports = imb_convert_port_to_hex($temp_ports["port_list"], $device["type_port_num_conversion"], $device["type_port_use_long"],$device["type_use_more_32x_port"]);
							if (strtolower($temp_ports) == strtolower($value)) {
								$rezult["check_data"] = $temp_ports;
								$rezult["check_rez"] = ((strtolower($temp_ports) == strtolower($value)) ? "OK" : "Error");
							}
							
						}elseif(($device["snmp_sysObjectID"] == ".1.3.6.1.4.1.171.10.75.15.2") and ($rezult["check_rez"] == "OK")){  //1210-28ME
							$rezult["step_rez"] = (($rezult["check_data"] == $value) ? "OK" : "Error");
						}
 						break;
 					case 'mode':
 						$rezult["check_rez"] = (($rezult["check_data"] == $value) ? "OK" : "Error");
						//DGS check
						if (($rezult["check_rez"] == "Error") and ($rezult["step_data"] === "") and ($rezult["check_data"] === "4") and ($device["impb_func_version"] >= 39)) {
							$rezult["step_rez"] = "OK";
							$rezult["check_rez"] = "OK";
							$rezult["step_data"] = "";
							$rezult["check_data"] = "4";
						}						
 						break;
 					case 'port_state':
 						$rezult["check_rez"] = (($rezult["check_data"] == impb_format_snmp_string($value, true)) ? "OK" : "Error");					
 						break;						
 					case 'port_name':
 						$rezult["check_rez"] = (($rezult["check_data"] == impb_format_snmp_string($value, true)) ? "OK" : "Error");
 						break;	
 					case 'save_config':
 							if ($device["snmp_sysObjectID"] == ".1.3.6.1.4.1.171.10.69.1") {
 								$need_output = 2;
 							}elseif ($device["snmp_sysObjectID"] == ".1.3.6.1.4.1.171.10.64.1") {
 								$need_output = 2;
 							}elseif ($device["snmp_sysObjectID"] == ".1.3.6.1.4.1.171.10.64.2") {
 								$need_output = 2;								
 							}elseif ($device["snmp_sysObjectID"] == ".1.3.6.1.4.1.171.10.75.15.2") {
 								$need_output = 1;								
 							}else {
 								$need_output = 1;
 							}						
 
 						if (strlen(trim($rezult["check_data"])) == 0) {
 							if ($device["snmp_sysObjectID"] ==      ".1.3.6.1.4.1.171.10.69.1") {
 								$int_timeount = 5;
 							}elseif ($device["snmp_sysObjectID"] == ".1.3.6.1.4.1.171.10.64.1") {
 								$int_timeount = 7;
 							}else {
 								$int_timeount = 1;
 							}
 							$i = 0;
 							do {
 							    sleep ($int_timeount);
 								$rezult["check_data"] = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$oid, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
 								$i =$i + 1;
 							} while ((strlen(trim($rezult["check_data"])) == 0) && ($i<6));
 						}
 						$rezult["check_rez"] = (($rezult["check_data"] == $need_output) ? "OK" : "Error");
 						
 						if (($rezult["check_rez"] == "OK") && (!$rezult["step_rez"]  == "OK")) {
 							$rezult["step_rez"] = "OK";
 							$rezult["step_data"] = $value;
 						}
 						break;							
 					case 'del_macip':
 						$rezult["check_rez"] = ((($rezult["check_data"] == 'U') || (substr_count($rezult["check_data"], "No Such Instance currently")) || ($rezult["check_data"] == '')) ? "OK" : "Error");
 						break;	
 					case 'del_blmac':
 						$rezult["check_rez"] = ((($rezult["check_data"] == 'U') || (substr_count($rezult["check_data"], "No Such Instance currently")) || ($rezult["check_data"] == '')) ? "OK" : "Error");
 						if (($rezult["step_rez"] != "OK") && (read_config_option("dimpb_recheck_delete_blmacs") != "on") && ($rezult["check_rez"] == "OK")) {
 							$rezult["step_rez"] = "OK";
 						}
 						break;							
 				}
 				if (($rezult["step_rez"] == "OK") && ($rezult["check_rez"] == "OK") ) {
 					$rezult["rezult_final"] = "OK";
 				}			
 			}
 		
 	}else{ //Запись забанена, изменения только в базе.
 		$rezult["step_rez"] = "OK";
 		$rezult["step_data"] = "OK";
 		$rezult["check_rez"] = "OK";
 		$rezult["check_data"] = "OK";
 		$rezult["rezult_final"] = "OK";
 		$message = "[OFFLINE-BAN] " . $message;
 	}
 		//imp_raise_message2($message, $device, $message_rezult_short, $message_rezult, $check_rezult_short, $check_rezult) {
 		//imp_raise_message2 ($message , $device, $rezult["rez_step"], $rezult["step_data"], $rezult["check_rez"], $rezult["check_data"]);
 		if (isset($device["dev_name"])) {
 			$dev_name = $device["dev_name"];
 		}else{
 			$dev_name = $device["description"];
 		}
 		$rezult["mes_id"] = imp_raise_message3(array("device_descr" => $dev_name, "type" => "action_check", "object"=>$type_change,"cellpading" => $cellpading, "message" => $message, "step_data" => $rezult["step_data"], "step_rezult" => $rezult["step_rez"], "check_data" => $rezult["check_data"], "check_rezult" => $rezult["check_rez"]));     
 
 		//db_store_imp_log($message, "ipmac", $macip_id, "change",$device_id, imb_check_mes_create_ipmac_s1_check($step1,$mac_adrress), $step1, imb_check_mes_create_ipmac_s1($check_step1, $mac_adrress ), $check_step1);
 	return $rezult;
 }
 
 
 
 
 
 
 function imb_set_and_check_combo($device, $oid1, $val_type1, $value1,  $oid2, $val_type2, $value2, $type_change, $message, $need_check = true, $cellpading = true, $banned = false){
 $rezult = array();
 $rezult["step_rez"] = "Error";
 $rezult["check_rez"] = "Error";
 $rezult["rezult_final"] = "Error";
 
 
 	if ($banned == false) {
		$retries=0;
		$snmp_timeout = $device["snmp_timeout"];
 			
 			$rezult["step_data"] = imp_snmp_set_combo($device["hostname"], $device["snmp_set_community"], $oid1, $val_type1, $value1, $oid2, $val_type2, $value2, $device["snmp_set_version"],$device["snmp_set_username"],$device["snmp_set_password"],$device["snmp_set_auth_protocol"], $device["snmp_set_priv_passphrase"], $device["snmp_set_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $snmp_timeout, $retries);
 			switch ($type_change){
 				case 'mac_status':
 					$rezult["step_rez"] = (((strtolower(str_replace(":", "",$rezult["step_data"] )) == strtolower($value1) . "0" . strtolower($value2)) || ($rezult["step_data"] == '1'))? "OK" : "Error");
 					break;
 			}
 			
 			//if ((($need_check == true) && ($rezult["step"] == "OK") ) || ()) {
 			if ($need_check == true)  {
 				$rezult["check_data"] = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$oid1, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
 				$rezult["check_data"] = trim(preg_replace("/^((HEX\-00|HEX\-)\:?)/", "",$rezult["check_data"]));
				switch ($type_change){
 					case 'mac_status':
 						$rezult["check_rez"] = ((strtolower(str_replace(":", "",$rezult["check_data"] )) == strtolower($value1)) ? "OK" : "Error");
 						break;
 				}
 				if (($rezult["step_rez"] == "OK") && ($rezult["check_rez"] == "OK") ) {
 					$rezult["rezult_final"] = "OK";
 				}			
 			}
 		
 	}else{ //Запись забанена, изменения только в базе.
 		$rezult["step_rez"] = "OK";
 		$rezult["step_data"] = "OK";
 		$rezult["check_rez"] = "OK";
 		$rezult["check_data"] = "OK";
 		$rezult["rezult_final"] = "OK";
 		$message = "[OFFLINE-BAN] " . $message;
 	}
 		//imp_raise_message2($message, $device, $message_rezult_short, $message_rezult, $check_rezult_short, $check_rezult) {
 		//imp_raise_message2 ($message , $device, $rezult["rez_step"], $rezult["step_data"], $rezult["check_rez"], $rezult["check_data"]);
 		if (isset($device["dev_name"])) {
 			$dev_name = $device["dev_name"];
 		}else{
 			$dev_name = $device["description"];
 		}
 		$rezult["mes_id"] = imp_raise_message3(array("device_descr" => $dev_name, "type" => "action_check", "object"=>$type_change,"cellpading" => $cellpading, "message" => $message, "step_data" => $rezult["step_data"], "step_rezult" => $rezult["step_rez"], "check_data" => $rezult["check_data"], "check_rezult" => $rezult["check_rez"]));     
 
 		//db_store_imp_log($message, "ipmac", $macip_id, "change",$device_id, imb_check_mes_create_ipmac_s1_check($step1,$mac_adrress), $step1, imb_check_mes_create_ipmac_s1($check_step1, $mac_adrress ), $check_step1);
 	return $rezult;
 } 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 /* raise_message - mark a message to be displayed to the user once display_output_messages() is called
    @arg $message_id - the ID of the message to raise as defined in $messages in 'include/config_arrays.php' */
 function imp_raise_message($message, $device, $message_rezult, $check_rezult) {
     $mes_id = 0;
 	if (isset($_SESSION["imp_output_messages"])) {
         $mes_id = count($_SESSION["imp_output_messages"]) + 1;
     }
 	$_SESSION["imp_output_messages"][$mes_id]["message"] = $message;
 	$_SESSION["imp_output_messages"][$mes_id]["device_hostname"] = $device["hostname"];
 	$_SESSION["imp_output_messages"][$mes_id]["message_rezult"] = $message_rezult;
 	$_SESSION["imp_output_messages"][$mes_id]["check_rezult"] = $check_rezult;
 }
 
 function imp_raise_message2($message, $device, $message_rezult_short, $message_rezult, $check_rezult_short, $check_rezult) {
     $mes_id = 0;
 	if (isset($_SESSION["imp_output_messages"])) {
         $mes_id = count($_SESSION["imp_output_messages"]) + 1;
     }
 	$_SESSION["imp_output_messages"][$mes_id]["message"] = $message;
 	$_SESSION["imp_output_messages"][$mes_id]["device_hostname"] = $device["hostname"];
 	$_SESSION["imp_output_messages"][$mes_id]["message_rezult_short"] = $message_rezult_short;
 	$_SESSION["imp_output_messages"][$mes_id]["message_rezult"] = $message_rezult;
 	$_SESSION["imp_output_messages"][$mes_id]["check_rezult_short"] = $check_rezult_short;
 	$_SESSION["imp_output_messages"][$mes_id]["check_rezult"] = $check_rezult;
 	
 }
 
 
 //imp_raise_message3(array(type => "title",leftmargin => '0', message => 'Старт'));     
 //imp_raise_message3(array(device_descr => "Свича", type => "action_check",leftmargin => "10", message => "шаг 1", step_data => "10", step_rezult => "OK", check_data => "20", check_rezult => "Error"));     
 
 function imp_raise_message3($args) {
 	
 	if (count($args) > 0){
 		if (!isset($args["mes_id"])) {
 			if (isset($_SESSION["imp_output_messages"])) {
 		        $mes_id = count($_SESSION["imp_output_messages"]) + 1;
 		    }else{
 			$mes_id = 1;
 			}	
 		}else{
 			$mes_id = $args["mes_id"];
 		}
 		foreach($args as $arg => $value) {
 			$_SESSION["imp_output_messages"][$mes_id][$arg] = $value;
 		}
 	}
 return $mes_id;
 
 // $user_full_name = db_fetch_cell("SELECT full_name FROM user_auth WHERE id='" . $_SESSION["sess_user_id"] . "';");
 // $insert_string="INSERT INTO imb_log (log_user_id,log_user_full_name,log_date,log_object,log_object_id, log_operation,log_device_id,log_message,log_rezult_short,log_rezult,log_check_rezult_short,log_check_rezult,log_saved) " . 
 // "VALUES ('" . $_SESSION["sess_user_id"] . "', '" . $user_full_name . "', '" .  date("Y-m-d H:i:s") . "', '" . $log_object . "', '" . $log_object_id . "', '" . $log_operation . "', '" . $log_device_id . "', '" . $log_message . "', '" . $log_rezult_short . "', '" . $log_rezult . "', '" . $log_check_rezult_short . "', '" . $log_rezult_check . "', 0);";
 // db_execute($insert_string);
 }
 
 
 
 function imp_display_output_messages() {
 global $config;
 	if (isset($_SESSION["imp_output_messages"])) {
 		//$error_message = is_error_message();
 		$i = 0;
 		if (is_array($_SESSION["imp_output_messages"])) {
        
		html_start_box("Результаты выполнения", '100%', '', '', 'center', '');

		html_header(array("Устройство","","Действие",   "Результат", "Проверка"));

 			foreach ($_SESSION["imp_output_messages"] as $current_message) {
 				//eval ('$message = "' . $current_message["message"] . '";');
 				form_alternate_row();
 					?>
 					<td><?php print $current_message["device_descr"];?></td>
 					<?php 
 					if ($current_message["type"] != 'title') {
 						if ($current_message["cellpading"] == "true")  {
 							print "<td width=2%></td>";
 							?><td COLSPAN="1"><?php print $current_message["message"];?></td><?php
 						}else {
 							?><td COLSPAN="2"><?php print $current_message["message"];?></td><?php
 						}
 					}
 					
 				switch ($current_message["type"]) {
 					case 'title' :
 						?>
 							<td COLSPAN="4"><?php print $current_message["message"];?></td>
 						<?php	
 						break;
 					case 'title_count' :
 						?>
 							<td COLSPAN="2" ALIGN="CENTER" BGCOLOR="#<?php print imb_covert_rezult_2_color($current_message["count_rez"]) ?>"><?php print "Выполнено " . ((isset($current_message["count_done"]) ) ? $current_message["count_done"] : "-") . " из " . ((isset($current_message["count_all"]) ) ? $current_message["count_all"] : "-");?></td>
 						<?php	
 						break;
 					case 'action_check':
 						?>
 							<td ALIGN="CENTER" BGCOLOR="#<?php print imb_covert_rezult_2_color($current_message["step_rezult"] ) ?>"><?php print $current_message["step_rezult"] . " [" . $current_message["step_data"] . "]";?></td>
 							<td ALIGN="CENTER" BGCOLOR="#<?php print imb_covert_rezult_2_color($current_message["check_rezult"]) ?>"><?php print $current_message["check_rezult"] . " [" . $current_message["check_data"] . "]";?></td>							
 						<?php					
 						break;
 					case 'action_check2':
 						?>
 							<td  COLSPAN="2" ALIGN="CENTER" BGCOLOR="#<?php print imb_covert_rezult_2_color($current_message["step_rezult"] ) ?>"><?php print $current_message["step_rezult"] . " [" . $current_message["step_data"] . "]";?></td>
 						<?php					
 						break;
 					case 'update_db':
 						?>
 							<td ALIGN="CENTER" BGCOLOR="#<?php print imb_covert_rezult_2_color($current_message["step_rezult"] ) ?>"><?php print $current_message["step_rezult"] ;?></td>
 							<td ALIGN="CENTER" BGCOLOR="#<?php print imb_covert_rezult_2_color($current_message["step_rezult"]) ?>"><?php print $current_message["step_data"];?></td>
 						<?php					
 						break;
 				}		
 			}
 		//print "</table><br>";
 		html_end_box();
 		//print "<br>";
 		}
 	}
 kill_session_var("imp_output_messages");
 }
 
 
 
 /* display_output_messages - displays all of the cached messages from the raise_message() function and clears
      the message cache */
 function imp_display_output_messages_old() {
 	global $config, $messages;
 
 	if (isset($_SESSION["imp_output_messages"])) {
 		//$error_message = is_error_message();
 		$i = 0;
 		if (is_array($_SESSION["imp_output_messages"])) {
         html_start_box("<strong>Результаты выполнения</strong>", "98%", "009F67" , "3", "center", ""); 
 			    $nav = "<tr bgcolor='#" . "009F67" . "'>
 					<td colspan='4'>
 	                <table width='100%' cellspacing='0' cellpadding='0'>
 	                    
 	                </table>
 					</td>
 					</tr>\n";
 			print $nav;
 			html_header(array("Действие",  "Устройство", "Результат", "Проверка"));
 			
 			foreach ($_SESSION["imp_output_messages"] as $current_message) {
 				eval ('$message = "' . $current_message["message"] . '";');
						form_alternate_row();
 		                ?>
 						<td><?php print $message;?></td>
 		                <td><?php print $current_message["device_hostname"];?></td>
 		                <?php if (isset($current_message["message_rezult_short"])) {
 							print "<td ALIGN=CENTER BGCOLOR=\"#" . imb_covert_rezult_2_color($current_message["message_rezult_short"]) . "\">" . $current_message["message_rezult_short"] . " (" .  $current_message["message_rezult"] . ")" . "</td>" ;
 						} else {
 							print "<td>" . $current_message["message_rezult"] . "</td>" ;
 						};
 
 						if (isset($current_message["check_rezult_short"])) {
 							print "<td ALIGN=CENTER BGCOLOR=\"#" . imb_covert_rezult_2_color($current_message["check_rezult_short"]) . "\">" . $current_message["check_rezult_short"] . " (" . $current_message["check_rezult"] . ")" . "</td>" ;
 						} else {
 							print "<td>" . $current_message["check_rezult"] . "</td>" ;
 						};?>
 		            </tr>
 		            <?php			
 			}
 			print "</table><br>";
 		html_end_box(false);
 		print "<br>";
 		}
 	}
 
 	kill_session_var("imp_output_messages");
 }
 
 function imp_convert_port_state_2str($port_state, $type) {
 global $imp_port_state_2str_t2, $imp_port_state_2str_t3;
 
 	if ($type == 1) {
 		$str_port_state = $imp_port_state_2str_t2[$port_state];
 	}elseif ($type == 2) {
 		$str_port_state = $imp_port_state_2str_t3[$port_state];
 	}
 	else {
 		$str_port_state = "other(" . $port_state . ")";
 	}
 		return $str_port_state;
 }
 
 function imp_convert_port_zerroip_state_2str($port_state, $device_id, $conversion_mode = "") {
 
 if ((isset($conversion_mode)) && (strlen(trim($conversion_mode)) > 0) ) {
 	$type_conversion_mode = $conversion_mode;
 } else {
 $type_conversion_mode = db_fetch_cell ("SELECT imb_device_types.type_imb_zerrostate_mode FROM imb_devices " . 
 	" left JOIN imb_device_types ON imb_devices.device_type_id = imb_device_types.device_type_id " .
 	" where device_id=" . $device_id . ";");
 }
 
 switch($type_conversion_mode) {
 case 1:
 	if ($port_state == 1) {
 		$str_port_state = "enable";
 	} elseif ($port_state == 2) {
 		$str_port_state = "disable";
 	} else {
 		$str_port_state = "other";
 	}
 break;
 case 2:
 	if ($port_state == 2) {
 		$str_port_state = "enable";
 	} elseif ($port_state == 3) {
 		$str_port_state = "disable";
 	} else {
 		$str_port_state = "other";
 	}		
 break;
 }
 return $str_port_state;
 
 }
 
 function imp_convert_port_zerroip_state_2str_full($port_state, $device_id, $conversion_mode = "") {
 $str_port_state = imp_convert_port_zerroip_state_2str($port_state, $device_id, $conversion_mode);
 switch($str_port_state) {
 	case 'enable':
 		$imp_convert_port_zerroip_state_2str_full = "<span style='color: #198e32'>Enable(" . $port_state . ")</span>";
 		break;
 	case 'disable':
 		$imp_convert_port_zerroip_state_2str_full = "<span style='color: #FF0000'>Disable(" . $port_state . ")</span>";
 		break;
 	case 'other':
 		$imp_convert_port_zerroip_state_2str_full = "<span style='color: #750F7D'>Other</span>";
 		break;
 	default:
 		$imp_convert_port_zerroip_state_2str_full = "<span style='color: #750F7D'>unk(" . $port_state .")</span>";
 		break;		
 }
 return $imp_convert_port_zerroip_state_2str_full;
 }
 
 function imp_convert_macip_state_2str($macip_state) {
 	switch($macip_state) {
 	case 1:
       $str_macip_state = "<span style='color: #198e32'>active(1)</span>";
 	  break;
 	case 2:
       $str_macip_state = "<span style='color: #a1a1a1'>notInService(2)</span>";
 	  break;	  
 	case 3:
       $str_macip_state = "<span style='color: #a1a1a1'>notReady(3)</span>";
 	  break;
 	case 4:
       $str_macip_state = "<span style='color: #750F7D'>createAndGo(4)</span>";
 	  break;
 	case 5:
       $str_macip_state = "<span style='color: #a1a1a1'>createAndWait(5)</span>";
 	  break;
 	case 6:
       $str_macip_state = "<span style='color: #750F7D'>destroy(6)</span>";
 	  break;
 	default:
 		$str_macip_state = "<span style='color: #750F7D'>unk(" . $macip_state .")</span>";
 		break;	
 	}
 		return $str_macip_state;
 }

 function imp_convert_free_2str($macip_free) {
 	switch($macip_free) {
 	case 0:
       $str_macip_state = "<span style='color: #750F7D'>off(0)</span>";
 	  break;
 	case 1:
       $str_macip_state = "<span style='color: #198e32'>ON(1)</span>";
 	  break;	  
 	default:
 		$str_macip_state = "<span style='color: #750F7D'>unk(" . $macip_state .")</span>";
 		break;	
 	}
 		return $str_macip_state;
 }
 
 function imp_convert_macip_action_2str($macip_action, $type_conversion_action) {
 	if ($macip_action == -1) {
 		$str_macip_action = "<span style='color: #a1a1a1'>unUse</span>";
 	}else{
 		switch($type_conversion_action) {
 		case 1:
 				switch($macip_action) {
 					case 1:
 						$str_macip_action = "<span style='color: #750F7D'>inactive(1)</span>";
 						break;
 					case 2:
 						$str_macip_action = "<span style='color: #198e32'>active(2)</span>";
 						break;
 					default:
 						$str_macip_action = "<span style='color: #750F7D'>unk(" . $macip_action .")</span>";
 						break;						
 				}
 		break;
 		case 2:
 				switch($macip_action) {
 					case 0:
 						$str_macip_action = "<span style='color: #750F7D'>inactive(0)</span>";
 						break;
 					case 1:
 						$str_macip_action = "<span style='color: #198e32'>active(1)</span>";
 						break;
 					default:
 						$str_macip_action = "<span style='color: #750F7D'>unk(" . $macip_action .")</span>";
 						break;						
 				}		
 		break;
 		default:
 			$str_macip_action = "<span style='color: #750F7D'>unk(" . $macip_action .")</span>";
 			break;
 		}
 	}
 		return $str_macip_action;
 }
 
 
 function imp_convert_macip_mode_2str($macip_mode, $device_id) {
 $type_conversion_mode = db_fetch_cell ("SELECT imb_device_types.type_imb_mode FROM imb_devices " . 
 	" left JOIN imb_device_types ON imb_devices.device_type_id = imb_device_types.device_type_id " .
 	" where device_id=" . $device_id . ";");
 	if ($macip_mode == -1) {
 		$str_macip_mode = "unUse";
 	}else{
 		switch($type_conversion_mode) {
 		case 1:
 				switch($macip_mode) {
 					case 1:
 						$str_macip_mode = "ARP";
 						break;
 					case 2:
 						$str_macip_mode = "ACL";
 						break;
 					default:
 						$str_macip_mode = "unk";
 						break;						
 				}
 		break;
 		case 2:
 				switch($macip_mode) {
 					case 0:
 						$str_macip_mode = "ARP";
 						break;
 					case 1:
 						$str_macip_mode = "ACL";
 						break;
 					default:
 						$str_macip_mode = "unk";
 						break;						
 				}		
 		break;
 		default:
 			$str_macip_mode = "unk";
 			break;
 		}
 	}
 		return $str_macip_mode;
 }
 function imp_convert_macip_mode_2str_full($macip_mode, $device_id) {
 $macip_mode_str = imp_convert_macip_mode_2str($macip_mode, $device_id);
 switch($macip_mode_str) {
 	case 'ARP':
 		$imp_convert_macip_mode_2str_full = "<span style='color: #198e32'>ARP(" . $macip_mode . ")</span>";
 		break;
 	case 'ACL':
 		$imp_convert_macip_mode_2str_full = "<span style='color: #750F7D'>ACL(" . $macip_mode . ")</span>";
 		break;
 	case 'unUse':
 		$imp_convert_macip_mode_2str_full = "<span style='color: #a1a1a1'>unUse</span>";
 		break;
 	default:
 		$imp_convert_macip_mode_2str_full = "<span style='color: #750F7D'>unk(" . $macip_mode .")</span>";
 		break;		
 }
 return $imp_convert_macip_mode_2str_full;
 }
 
 
 
 function impb_convert_port_state_2_html($port, $device_id=0) {
 global $impb_port_state_2html, $impb_port_zerro_state_2html;
 $rezult="";
	if (isset($port["snmp_oid_MacBindingPortState"]) and strlen($port["snmp_oid_MacBindingPortState"]) > 0 and (!($port["type_imb_MacBindingPortState"] == 5)) ) {
		if (!(isset($port["snmp_oid_en_swIpMacBindingPortARPInspection"]) and strlen($port["snmp_oid_en_swIpMacBindingPortARPInspection"]) > 0)) {
		//DES-3028 3200  enable port in loose/strict mode
			if (isset($impb_port_state_2html[$port["type_imb_MacBindingPortState"]][$port["port_imb_state"]])) {
				$rezult=$impb_port_state_2html[$port["type_imb_MacBindingPortState"]][$port["port_imb_state"]];
			}else{
				$rezult="<strong> <span style='color: #EA8F00;'>-</span></strong>";
			}
		}else{
		//DES-1210-28 enable port and enable ARP/IP inspection
			if (isset($impb_port_state_2html[6][$port["port_imb_state"]])) {
				$rezult=$impb_port_state_2html[6][$port["port_imb_state"]];
			}else{
				$rezult="<strong> <span style='color: #EA8F00;'>-</span></strong>";
			}
			
			$rezult=$rezult . " (" . $impb_port_state_2html["5"][$port["port_arp_inspection"]] . "," . $impb_port_state_2html["7"][$port["port_ip_inspection"]] . ")";			
		
		}
	}elseif ($port["type_imb_MacBindingPortState"] = 5){
		//DGS  enable ARP/IP in strict/loose
		$rezult=$impb_port_state_2html["3"][$port["port_arp_inspection"]] . "," . $impb_port_state_2html["4"][$port["port_ip_inspection"]];;	
	}

	 if (isset($port["port_zerroip_state"]) and ($port["port_zerroip_state"] > 0) and ($port["type_imb_MacBindingPortState"] = 5)) {
		$rezult = $rezult . "," . $impb_port_zerro_state_2html[$port["type_imb_zerrostate_mode"]][$port["port_zerroip_state"]];
	 }
	 
return $rezult;
 }
 
 
 
 function imp_convert_macip_mode_2integer($macip_mode_string, $device_id, $conversion_mode = "") {
 if ((isset($conversion_mode)) && (strlen(trim($conversion_mode)) > 0) ) {
 	$type_conversion_mode = $conversion_mode;
 } else {
 $type_conversion_mode = db_fetch_cell ("SELECT imb_device_types.type_imb_mode FROM imb_devices " . 
 	" left JOIN imb_device_types ON imb_devices.device_type_id = imb_device_types.device_type_id " .
 	" where device_id=" . $device_id . ";");
 }
 	
 		switch($type_conversion_mode) {
 		case 1:
 				switch($macip_mode_string) {
 					case 'ACL':
 						$int_macip_mode = 2;
 						break;
 					case 'ARP':
 						$int_macip_mode = 1;
 						break;
 				}
 		break;
 		case 2:
 				switch($macip_mode_string) {
 					case 'ACL':
 						$int_macip_mode = 1;
 						break;
 					case 'ARP':
 						$int_macip_mode = 0;
 						break;
 				}		
 		break;
 		}
 		return $int_macip_mode;
 }
        
 	function imp_convert_blmac_state_2str($blmac_state) {
 	switch($blmac_state) {
 	case 1:
       $str_blmac_state = "other(1)";
 	  break;
 	case 2:
       $str_blmac_state = "blockByAddrBind(2)";
 	  break;	  
 	case 3:
       $str_blmac_state = "delete(3)";
 	  break;
 	default:
 		$str_blmac_state = "unk(" . $blmac_state .")";
 		break;	
 	}
 		return $str_blmac_state;
 }
 
 function imp_convert_banned_state_2str($macip_banned) {
 	switch($macip_banned) {
 	case 0:
       $str_macip_banned = "";
 	  break;
 	case 1:
       $str_macip_banned = "<span style='color: #750F7D'>BANNED(1)</span>";
 	  break;	  
 	default:
 		$str_macip_banned = "<span style='color: #750F7D'>unk(" . $macip_banned .")</span>";
 		break;	
 	}
 		return $str_macip_banned;
 }
 
 function imp_convert_banned_type_2str($banip_type) {
 global $impb_imb_banip_type ;
 if (isset($impb_imb_banip_type[$banip_type])) {
 	$str_banip_type = $impb_imb_banip_type[$banip_type] . " (" . $banip_type . ")";
 }else{
 	$str_banip_type = "unk (" . $banip_type . ")";
 }
 
 	return $str_banip_type;
 }
 
 function imp_check_port_state($device, $port_number) {		
 	$check_rezult = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["device_type_global"]["snmp_oid_MacBindingPortState"] . "." . $port_number, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"]);
 	return $check_rezult;
 }
 
 function imp_check_port_zerroip_state($device, $port_number) {		
 	$check_rezult = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["device_type_global"]["snmp_oid_en_MacBindingZerroIpPortState"] . "."  . $port_number, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"]);
 	return $check_rezult;
 }
 
 //function api_imp_change_imp_port_state($row_id, $port_state, $use_strict_mode = false){
 function api_imp_change_imp_port_state($port, $port_state, $device, $use_strict_mode = false){
 
 	if ($port_state == "enable") {
 		if ($use_strict_mode and ($device["type_imb_MacBindingPortState"] == "2")) {
 			$snmp_port_state = 2;
 		}elseif ($device["type_imb_MacBindingPortState"] == "6") {   //des-3010g
			$snmp_port_state = 2;
		}elseif ($device["type_imb_MacBindingPortState"] == "71"){ //1210-28me
			$snmp_port_state = 1;
		}else{
 			$snmp_port_state = 4;
 		}
 	} else {
		if ($device["type_imb_MacBindingPortState"] == "71"){ //1210-28me
			$snmp_port_state = 0;
		}else{ 		
			$snmp_port_state = 3;
		}
 	}
 	
 	$ar_step1 = imb_set_and_check($device, $device["snmp_oid_MacBindingPortState"] . "." . $port["port_number"] , "i", $snmp_port_state, "port_state", "Изменение статуса привязки на порту [" . $port["port_number"] . "]  с [" . imp_convert_port_state_2str($port["port_imb_state"], $device["type_imb_MacBindingPortState"]) . "] на [" . imp_convert_port_state_2str($snmp_port_state,$device["type_imb_MacBindingPortState"]) . "]", $need_check = true);
 		if ($ar_step1["rezult_final"] == "OK") {
 			$str_check_rezult = "OK";
 			// если и проверка прошла успешна - то изменяем значение статуса порта в таблице портов
 			db_execute("UPDATE `imb_ports` SET port_imb_state=" . $snmp_port_state . " where port_id=" . $port["port_id"] );
 			//теперь именим количество  портов у устройства, на которых включена привязка
 			db_execute("UPDATE `imb_devices` SET ports_enable_total=(SELECT count(*) FROM imb_ports where device_id=" . $port["device_id"] . " and port_imb_state=2) where device_id=" . $port["device_id"] );
 			//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
 			increment_unsaved_count($port["device_id"], '1');
 			//db_execute("UPDATE `imb_devices` SET count_unsaved_actions=count_unsaved_actions + 1 where device_id=" . $device["device_id"] );		
 		}
 	
 }
 
 function api_imp_change_zerroip_port_state($port, $port_state, $device){
 
 	if ((isset($device["snmp_oid_en_MacBindingZerroIpPortState"])) && (trim($device["snmp_oid_en_MacBindingZerroIpPortState"]) <> "") ){	
		switch($device["type_imb_zerrostate_mode"]) {
		case 1:
				if ($port_state == "enable") {
					$snmp_port_state = 1;
				} else {
					$snmp_port_state = 2;
				}
		break;
		case 2:
				if ($port_state == "enable") {
					$snmp_port_state = 2;
				} else {
					$snmp_port_state = 3;
				}		
		break;
		case 3:
				if ($port_state == "enable") {
					$snmp_port_state = 1;
				} else {
					$snmp_port_state = 0;
				}		
		break;		
		}		
 		
 		$ar_step = imb_set_and_check($device, $device["snmp_oid_en_MacBindingZerroIpPortState"] . "."  . $port["port_number"], "i", $snmp_port_state, "port_state", "Изменение режима нулевого IP на порту [" . $port["port_number"] . "]  c [" . imp_convert_port_zerroip_state_2str($port["port_imb_state"], $port["device_id"], $device["type_imb_zerrostate_mode"]) . "] на [" . imp_convert_port_zerroip_state_2str($snmp_port_state, $port["device_id"], $device["type_imb_zerrostate_mode"]) . "]", $need_check = true);
 			if ($ar_step["rezult_final"] == "OK") {
 				// если и проверка прошла успешна - то изменяем значение статуса порта в таблице портов
 				db_execute("UPDATE `imb_ports` SET port_zerroip_state=" . $snmp_port_state . " where port_id=" . $port["port_id"] );
 				//теперь именим количество  портов у устройства, на которых включена привязка
 				db_execute("UPDATE `imb_devices` SET ports_enable_zerroip_total=(SELECT count(*) FROM imb_ports where device_id=" . $device["device_id"] . " and port_zerroip_state=1) where device_id=" . $device["device_id"] );
 				//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
 				increment_unsaved_count($device["device_id"], '1');
 				//db_execute("UPDATE `imb_devices` SET count_unsaved_actions=count_unsaved_actions + 1 where device_id=" . $device["device_id"] );	
 			}	
 	}else{
 		imp_raise_message3(array("device_descr" => $device["dev_name"], "type" => "action_check2", "cellpading" => "true", "step_rezult" => "OK","step_data" => "OK", "message" => "Изменение режима нулевого IP на порту [" . $port["port_number"] . "]  не поддерживаеться устройством.", ));     
 	}
 }
 
function api_imp_change_arpinsp_port_state($port, $port_state, $device){
global  $impb_port_state_2html;
if($device["type_imb_MacBindingPortState"] != 71){
	if (isset($impb_port_state_2html["arp_str2int"][$port_state])) {
		$ar_step1 = imb_set_and_check($device, $device["snmp_oid_en_swIpMacBindingPortARPInspection"] . "." . $port["port_number"], "i", $impb_port_state_2html["arp_str2int"][$port_state], "port_state", "Изменение статуса привязки на порту [" . $port["port_number"] . "]  с [" . $impb_port_state_2html["arp_int2str"][$port["port_arp_inspection"]] . "] на [" . $port_state . "]", $need_check = true);
			if ($ar_step1["rezult_final"] == "OK") {
				$str_check_rezult = "OK";
				// если и проверка прошла успешна - то изменяем значение статуса порта в таблице портов
				db_execute("UPDATE `imb_ports` SET port_arp_inspection=" . $impb_port_state_2html["arp_str2int"][$port_state] . " where port_id=" . $port["port_id"] );
				//теперь именим количество  портов у устройства, на которых включена привязка
				db_execute("UPDATE `imb_devices` SET ports_enable_total=(SELECT count(*) FROM imb_ports where device_id=" . $device["device_id"] . " and (port_ip_inspection = 1 or port_arp_inspection > 1 )) where device_id=" . $device["device_id"] );
				//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
				increment_unsaved_count($device["device_id"], '1');
				//db_execute("UPDATE `imb_devices` SET count_unsaved_actions=count_unsaved_actions + 1 where device_id=" . $device["device_id"] );		
			}
	}
}else{
	if (isset($impb_port_state_2html["arp_str2int_1210"][$port_state])) {
		$ar_step1 = imb_set_and_check($device, $device["snmp_oid_en_swIpMacBindingPortARPInspection"] . "." . $port["port_number"], "i", $impb_port_state_2html["arp_str2int_1210"][$port_state], "port_state", "Изменение статуса привязки на порту [" . $port["port_number"] . "]  с [" . $impb_port_state_2html["arp_int2str_1210"][$port["port_arp_inspection"]] . "] на [" . $port_state . "]", $need_check = true);
			if ($ar_step1["rezult_final"] == "OK") {
				$str_check_rezult = "OK";
				// если и проверка прошла успешна - то изменяем значение статуса порта в таблице портов
				db_execute("UPDATE `imb_ports` SET port_arp_inspection=" . $impb_port_state_2html["arp_str2int_1210"][$port_state] . " where port_id=" . $port["port_id"] );
				//теперь именим количество  портов у устройства, на которых включена привязка
				db_execute("UPDATE `imb_devices` SET ports_enable_total=(SELECT count(*) FROM imb_ports where device_id=" . $device["device_id"] . " and (port_ip_inspection = 1 or port_arp_inspection > 1 )) where device_id=" . $device["device_id"] );
				//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
				increment_unsaved_count($device["device_id"], '1');
				//db_execute("UPDATE `imb_devices` SET count_unsaved_actions=count_unsaved_actions + 1 where device_id=" . $device["device_id"] );		
			}
	}
}	
}
 
 //api_imp_change_ipinsp_port_state($ports[$i], ($use_strict_mode ? "strict" : "loose"), $port_devices[$ports[$i]["device_id"]] );
function api_imp_change_ipinsp_port_state($port, $port_state, $device){
global  $impb_port_state_2html;
if($device["type_imb_MacBindingPortState"] != 71){
	if (isset($impb_port_state_2html["ip_str2int"][$port_state])) {
		$ar_step1 = imb_set_and_check($device, $device["snmp_oid_en_swIpMacBindingPortIPInspection"] . "." . $port["port_number"], "i", $impb_port_state_2html["ip_str2int"][$port_state], "port_state", "Изменение статуса привязки на порту [" . $port["port_number"] . "]  с [" . $impb_port_state_2html["arp_int2str"][$port["port_ip_inspection"]] . "] на [" . $port_state . "]", $need_check = true);
			if ($ar_step1["rezult_final"] == "OK") {
				$str_check_rezult = "OK";
				// если и проверка прошла успешна - то изменяем значение статуса порта в таблице портов
				db_execute("UPDATE `imb_ports` SET port_ip_inspection=" . $impb_port_state_2html["ip_str2int"][$port_state] . " where port_id=" . $port["port_id"] );
				//теперь именим количество  портов у устройства, на которых включена привязка
				db_execute("UPDATE `imb_devices` SET ports_enable_total=(SELECT count(*) FROM imb_ports where device_id=" . $device["device_id"] . " and (port_ip_inspection = 1 or  port_arp_inspection > 1 )) where device_id=" . $device["device_id"] );
				//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
				increment_unsaved_count($device["device_id"], '1');
			}
	} 
}else{
	if (isset($impb_port_state_2html["arp_str2int_1210"][$port_state])) {
		$ar_step1 = imb_set_and_check($device, $device["snmp_oid_en_swIpMacBindingPortIPInspection"] . "." . $port["port_number"], "i", $impb_port_state_2html["arp_str2int_1210"][$port_state], "port_state", "Изменение статуса привязки на порту [" . $port["port_number"] . "]  с [" . $impb_port_state_2html["arp_int2str_1210"][$port["port_ip_inspection"]] . "] на [" . $port_state . "]", $need_check = true);
			if ($ar_step1["rezult_final"] == "OK") {
				$str_check_rezult = "OK";
				// если и проверка прошла успешна - то изменяем значение статуса порта в таблице портов
				db_execute("UPDATE `imb_ports` SET port_ip_inspection=" . $impb_port_state_2html["arp_str2int_1210"][$port_state] . " where port_id=" . $port["port_id"] );
				//теперь именим количество  портов у устройства, на которых включена привязка
				db_execute("UPDATE `imb_devices` SET ports_enable_total=(SELECT count(*) FROM imb_ports where device_id=" . $device["device_id"] . " and (port_ip_inspection = 1 or port_arp_inspection > 1 )) where device_id=" . $device["device_id"] );
				//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
				increment_unsaved_count($device["device_id"], '1');
			}
	} 
}
}

 
 function imp_save_config($devices_array) {
 	foreach($devices_array as $device_id) {
 		imp_save_config_main($device_id["device_id"]);
 	}
 }
 
 function imp_save_config_main($device_id) {
 	$device =  db_fetch_row ("SELECT `imb_devices`.`description` as dev_name, imb_devices.*, imb_device_types.* FROM imb_devices " .
 				" LEFT JOIN imb_device_types " .
 				" on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
 				" WHERE device_id='" . $device_id. "';");
 	
 	
 	$ar_step1 = imb_set_and_check($device, $device["snmp_oid_agentSaveCfg"],"i", $device["snmp_value_save_cfg"], "save_config", "Сохранение текущей кофигурации в NV-RAM", true, false);
 	if ($ar_step1["rezult_final"] == "OK") {
 			db_execute("UPDATE `imb_devices` SET count_unsaved_actions=0 where device_id=" . $device_id );
 			db_execute("UPDATE `imb_log` SET `log_saved`='1' where log_device_id=" . $device_id );
 	}
 return $ar_step1["rezult_final"];
 }
 
 //api_imp_delete_blmacs($blmac, $port_devices[$blmac["device_id"]]);
 function api_imp_delete_blmacs($blmac_row, $device, $auto_mode=false){
 	
 	
	$ar_step1 = imb_set_and_check($device, $device["snmp_oid_BindingBlockedType"]. "."  . $blmac_row["blmac_index"], "i", ($device["impb_func_version"] >= 39 ? 6 : 3), "del_blmac", "Удаление блока  MAC=[" . $blmac_row["blmac_macaddr"] . "], PORT=[" . $blmac_row["blmac_port"] . "], VID=[" . $blmac_row["blmac_vid"] . "]", true);
 	if ($ar_step1["rezult_final"] == "OK") {
 			// если и проверка прошла успешна - то удаляем запись о блоке из таблицы
 			
 			//если удаляем автоматически - то запись в таблице не удаляем - оставим её на 5 просмотров
			if (!($auto_mode)) {
				db_execute("DELETE FROM `imb_blmacs` where blmac_id=" . $blmac_row["blmac_id"] );
				db_execute("DELETE FROM `imb_temp_blmacinfo` where blmacinfo_info_id=" . $blmac_row["blmac_id"] );
			}else{
				db_execute("UPDATE `imb_blmacs` SET `blmac_done` = '1', `blmac_online`='0' WHERE `blmac_id` = '" . $blmac_row["blmac_id"] . "';");
			}
 			//теперь именим количество блоков у устройства, на которых включена привязка
 			db_execute("UPDATE `imb_devices` SET ip_mac_blocked_total=(SELECT count(*) FROM imb_blmacs where device_id=" . $device["device_id"] . " ) where device_id=" . $device["device_id"] );
 	}
	
 return $ar_step1["rezult_final"];
}

 //удаление блока через cli скрипт, без занесения в базу
 //$rezult = api_cli_imp_delete_blmacs($device,$matches);
 function api_cli_imp_delete_blmacs($device,$matches){
 	// для удаления нам нужен blmac_index = 317.0.27.252.32.23.218 где в первом октете идет номер влана блока.
 	
	$ar_step1 = imb_set_and_check($device, $device["snmp_oid_BindingBlockedType"]. "."  . $matches["oid"], "i", ($device["impb_func_version"] >= 38 ? 6 : 3), "del_blmac", "delete from cli", true);

 return $ar_step1["rezult_final"];
 
 
 }
 
 Function impb_DateTimeDiff ($date_start) {
     // получает количество секунд между двумя датами 
     $timedifference =  strtotime("now") - strtotime($date_start);
 	$days = 0;
 	$hours = 0;
 	$minutes = 0;
 	$seconds = 0;
 	$str_rezult = "";
 	
 	if ($timedifference > 86400) {
 		$days = bcdiv($timedifference,86400);
 		$str_rezult = $days . "дн. ";
 	}
 
 	if ($timedifference > 3600) {
 		$hours = bcdiv(($timedifference - $days*86400),3600);
 		$str_rezult = $str_rezult . $hours . ":";
 	}
 
 	if ($timedifference > 60) {
 		$minutes = bcdiv(($timedifference - $days*86400 - $hours*3600),60);
 		$str_rezult = $str_rezult . sprintf("%02u",$minutes) . ":";
 	}
 	
 	$seconds = ($timedifference - $days*86400 - $hours*3600 - $minutes*60);
 	$str_rezult = $str_rezult . sprintf("%02u",$seconds);
 	
 	return $str_rezult;
 
 }
 
 function imb_fromat_datetime($date_src){
 $today=date('Y-m-d');
 if ($today == date('Y-m-d',strtotime($date_src))) {
 	//$TimeDiff = calc_period($date_src, $current_time);
 	//if ($TimeDiff[3] > 0) {
 	//	$str_timediff = $TimeDiff[3] . "h :";
 	//}
 	//$TimeDiff = "- " . date('i:s',strtotime("now") - strtotime($date_src)) ;
 	
 	$date_return = date('H:i:s',strtotime($date_src)) . " ( " .  impb_DateTimeDiff($date_src) . ")";
 	//$daysDiff=date2Days($t2)-date2Days($t1);
 	} else {
 	$date_return = $date_src;
 }
 return $date_return;
 }
 
 
 
 function imb_clear_macadrress($mac_adrress){
 	$rezilt = str_replace(":", "", $mac_adrress);
 	$rezilt = str_replace("-", "", $rezilt);
 return $rezilt;
 }
 
 function imb_create_imp_record($device_id, $mac_adrress, $ip_adrress, $port, $acl_mode, $ban = false) {
 #First - Check if that IP in BAN!
 $check_for_ban = db_fetch_cell ("SELECT count(`banip_id`)  FROM `imb_banip` where INET_ATON(`banip_ipaddr`)=INET_ATON('" . $ip_adrress . "') and `banip_aplled`=1;");
 if (($check_for_ban == 0) || ($ban == true)) { 
 	#Second - check if that mac exist on that port and device - may be in block
 	$check_for_exist_block = db_fetch_cell ("SELECT `blmac_id` FROM imb_blmacs WHERE `device_id`='" . $device_id . "' AND UPPER(`blmac_macaddr`) = '" . strtoupper($mac_adrress) . "' and `blmac_port`='" . $port . "';");
 	if ( (isset($check_for_exist_block)) && (trim($check_for_exist_block) !="") && (is_numeric($check_for_exist_block)) ) {
 		$rezult = imb_create_imp_record_from_block($device_id, $mac_adrress, $ip_adrress, $port, $check_for_exist_block,  $acl_mode);
 	}else{
 		$check_for_exist_row = db_fetch_cell ("SELECT `macip_id` FROM imb_macip WHERE `device_id`='" . $device_id . "' AND `macip_ipaddr` = '" . $ip_adrress . "';");
 		if ( (isset($check_for_exist_row)) && (trim($check_for_exist_row) !="") && (is_numeric($check_for_exist_row)) && ($ban == false)) {
 			$rezult =  imb_change_macip_record2 ($check_for_exist_row, $mac_adrress, $port, $acl_mode, $new = true);
 		}else{
 			$rezult = imb_create_imp_record_real($device_id, $mac_adrress, $ip_adrress, $port, $acl_mode, true, $ban);
 		}
 	}
 }else{ //Этот ИП в примененных банах.
 	imp_raise_message3(array("device_descr" => "BANs", "cellpading" => "false","type" => "action_check2", "step_rezult" => "Warning","step_data" => "IP Забанен","message" => "IP [" . $ip_adrress . "] Забанен. Изменение невозможны."));    
 	$rezult = "Warning";
 }
 return $rezult;
 }
 
 function imb_create_imp_record_real($device_id, $mac_adrress, $ip_adrress, $port, $acl_mode, $new = true, $ban = false) {
 
 	$device =  db_fetch_row ("SELECT `imb_devices`.`description` as dev_name , imb_devices.*, imb_device_types.* FROM imb_devices " .
 			" LEFT JOIN imb_device_types " .
 			" on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
 			" WHERE device_id='" . $device_id. "';");
 	$ar_actions["count"] = 4;
 	$ar_actions["done"] = 0;
 	$ar_actions["global_rezult"] = "Error";
	$acl_int = '-1';
	
 	if ($new == true) {
 		$ar_actions["mes_id"] = imp_raise_message3(array("device_descr" => $device["dev_name"], "cellpading" => "false","type" => "title_count", "message" => "Cоздание записи IP-MAC"));    
 	}else{
 		$ar_actions["mes_id"] = imp_raise_message3(array("device_descr" => $device["dev_name"], "cellpading" => "false","type" => "title_count", "message" => "Cоздание записи IP-MAC на основе блока"));    
 	}

	//проверим - может такой МАК уже есть в этом сегменте.
	$existed_macs = db_fetch_assoc("SELECT * FROM imb_macip where macip_macaddr='" . $mac_adrress . "' and macip_ipaddr <> '" . $ip_adrress . "' and (inet_aton('" . $ip_adrress . "') & INET_ATON('255.255.255.0')) = (inet_aton(`macip_ipaddr`) & INET_ATON('255.255.255.0'));");
	if (sizeof($existed_macs)>0) {

		imb_send_message2admin($mac_adrress, $ip_adrress);
		
	}else{ // продолжаем если только мак уникальный в этом сегменте
		if ($device["type_imb_create_macip"] == 1) { //first create status, mac
			$ar_step1 = imb_set_and_check($device, $device["snmp_oid_MacBindingStatus"]. "." . $ip_adrress , "i", 4, "status", "Шаг 1. Активирование созданной записи IP-MAC", true);
		}elseif ($device["type_imb_create_macip"] == 2) { //first create  mac
			$ar_step1 = imb_set_and_check($device, $device["snmp_oid_MacBindingMac"]. "." . $ip_adrress , "x", imb_clear_macadrress($mac_adrress), "mac", "Шаг 1. создание записи IP-MAC. IP [" . $ip_adrress . "] для MAC [" . $mac_adrress . "] ", true);
		}elseif ($device["type_imb_create_macip"] == 3) { //combocreate mac+status
			$ar_step1 = imb_set_and_check_combo($device, $device["snmp_oid_MacBindingMac"]. "." . $ip_adrress , "x", imb_clear_macadrress($mac_adrress), $device["snmp_oid_MacBindingStatus"]. "." . $ip_adrress , "i", 4, "mac_status","Шаг 1. Комбо создание записи IP-MAC. IP [" . $ip_adrress . "] для MAC [" . $mac_adrress . "] ", true);	
		}elseif ($device["type_imb_create_macip"] == 4) { //combocreate IP.MAC status+port+status   1210-28ME
			$ar_step1 = imb_set_and_check($device, $device["snmp_oid_MacBindingStatus"] . ".4." . $ip_adrress . "." . dimpb_mac_16_to_10($mac_adrress) , "i", 5, "status", "Шаг 1. Активирование созданной записи IP-MAC", true);
		}
				
		//Не проверяем результаты первого шага, так как некоторые свичи не отдают ответ (вуы-30хх с прошивкой < 4 версии.
		//	$ar_actions["done"] = $ar_actions["done"] +1;
			if ($device["type_imb_create_macip"] == 1) { //first create status, mac
				$ar_step2 = imb_set_and_check($device, $device["snmp_oid_MacBindingMac"]. "." . $ip_adrress , "x", imb_clear_macadrress($mac_adrress), "mac", "Шаг 2. создание записи IP-MAC. IP [" . $ip_adrress . "] для MAC [" . $mac_adrress . "] ", true);
			}elseif ($device["type_imb_create_macip"] == 2){ //first create mac, status
				$ar_step2 = imb_set_and_check($device, $device["snmp_oid_MacBindingStatus"]. "." . $ip_adrress , "i", 4, "status", "Шаг 2. Активирование созданной записи IP-MAC", true);
			}elseif ($device["type_imb_create_macip"] == 3) {
				$ar_step2 = imb_set_and_check($device, $device["snmp_oid_MacBindingStatus"]. "." . $ip_adrress , "i", 1, "status", "Шаг 2. Активирование созданной записи IP-MAC", true);		
			}elseif ($device["type_imb_create_macip"] == 4) { //1210-28me
				$ar_step2 = imb_set_and_check($device, $device["snmp_oid_MacBindingStatus"] . ".4." . $ip_adrress . "." . dimpb_mac_16_to_10($mac_adrress) , "i", 1, "status", "Шаг 2. Активирование созданной записи IP-MAC", true);		
			}
			
			if ($ar_step2["rezult_final"] == "OK") {
				$ar_actions["done"] = $ar_actions["done"] +1;
				if ($ar_step1["rezult_final"] != "OK") {
					//Проверяем результаты первого шага после выполнения второго. если изначально первый не выдал результатов
					if ($device["type_imb_create_macip"] == 1) { //first create status, mac
						$ar_step1["check_data"] = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["snmp_oid_MacBindingStatus"]. "." . $ip_adrress, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
						$ar_step1["check_rez"] = ((strtolower(str_replace(":", "",$ar_step1["check_data"] )) == "1") ? "OK" : "Error");
					}elseif($device["type_imb_create_macip"] == 4){ //1210-28me
						$ar_step1["check_data"] = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["snmp_oid_MacBindingStatus"] . ".4." . $ip_adrress . "." . dimpb_mac_16_to_10($mac_adrress) , $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
						$ar_step1["check_rez"] = ((strtolower(str_replace(":", "",$ar_step1["check_data"] )) == "1") ? "OK" : "Error");					
					}else { //first create mac, status
						$ar_step1["check_data"] = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["snmp_oid_MacBindingMac"]. "." . $ip_adrress, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
						$ar_step1["check_data"] = trim(preg_replace("/^((HEX\-00|HEX\-)\:?)/", "",$ar_step1["check_data"]));
						$ar_step1["check_rez"] = ((strtolower(str_replace(":", "",$ar_step1["check_data"] )) == strtolower(imb_clear_macadrress($mac_adrress))) ? "OK" : "Error");
					}
					if ($ar_step1["check_rez"] == "OK") {
						$ar_step1["step_data"] = $ar_step1["check_data"];
						$ar_step1["step_rez"] = "OK";
						$ar_step1["rezult_final"] = "OK";
						//$ar_actions["done"] = $ar_actions["done"] +1;
						imp_raise_message3(array("mes_id" => $ar_step1["mes_id"], "step_data" => $ar_step1["step_data"], "step_rezult" => $ar_step1["step_rez"], "check_data" => $ar_step1["check_data"], "check_rezult" => $ar_step1["check_rez"]));     
					}
					
					
				} 
				// Теперь у нас етсь выполненный второй и дополнительно проверенные первый
				if ($ar_step1["rezult_final"] == "OK") {
				$ar_actions["done"] = $ar_actions["done"] +1;
					if ($device["type_imb_create_macip"] != 4) {
						$temp_port_hex = imb_convert_port_to_hex($port, $device["type_port_num_conversion"], $device["type_port_use_long"],$device["type_use_more_32x_port"]);
						$arr_ports = convert_Xport_to_view_string($temp_port_hex, $device["type_port_num_conversion"] );
						$ar_step3 = imb_set_and_check($device, $device["snmp_oid_MacBindingPorts"] . "." . $ip_adrress, "x", $temp_port_hex, "port", "Шаг 3. Установка номера порта [" . $arr_ports["port_view"] . "] для созданной записи.",  true);
					}else{
						$arr_ports["port_view"] = $port;
						$arr_ports["port_list"] = $port;
						$ar_step3 = imb_set_and_check($device, $device["snmp_oid_MacBindingPorts"] . ".4." . $ip_adrress . "." . dimpb_mac_16_to_10($mac_adrress), "i", $port, "port", "Шаг 3. Установка номера порта [" . $arr_ports["port_view"] . "] для созданной записи.",  true);
					}
					
					if ($ar_step3["rezult_final"] == "OK") {
						$ar_actions["done"] = $ar_actions["done"] +1;
						
						
					if ((isset($device["snmp_oid_MacBindingMode"])) && (trim($device["snmp_oid_MacBindingMode"]) <> "") ){
						$acl_int = imp_convert_macip_mode_2integer($acl_mode, $device_id, $device["type_imb_mode"]);
						$ar_step4 = imb_set_and_check($device, $device["snmp_oid_MacBindingMode"] . "." . $ip_adrress, "i", $acl_int, "mode", "Шаг 4. Задание режима записи " . $acl_mode . "." ,  true);
						if ($ar_step4["rezult_final"] == "OK") {
							$ar_actions["done"] = $ar_actions["done"] + 1;
							$ar_actions["global_rezult"] = "OK";
						}
					}else{
						//imp_raise_message2 ("  |--> Шаг " . $step . ". изменение режима записи не выполнено." , $device, $step4, $step4, $check_step4, $check_step4);
						//ERROR [No Such Object available on this agent at this OID]
						imp_raise_message3(array("device_descr" => $device["dev_name"], "type" => "action_check2", "cellpading" => "true", "step_rezult" => "OK","step_data" => "OK", "message" => "Шаг 4. Задание режима записи не поддерживаеться устройством.", ));     
						$ar_actions["done"] = $ar_actions["done"] + 1;
						$ar_actions["global_rezult"] = "OK";
						$acl_int = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["snmp_oid_MacBindingMode"]. "." . $ip_adrress, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
					}
						
						
						if ($ar_actions["global_rezult"] == "OK") {
							$new_action = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["snmp_oid_MacBindingAction"]. "." . $ip_adrress, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
							if (strlen(trim($new_action)) == 0) {
								$new_action = -1;
							}
							//Если снимаеться бан, тогда в базе не создаем запись.
							if ($ban == false) {						
								//$mew_status = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["snmp_oid_MacBindingStatus"]. "." . $ip_adrress, $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
								// так как все прошло нормально, запись создана, то добавляем её сразу в таблицу и увеличиваем все счетчики
								db_execute("INSERT into imb_macip (device_id, macip_index, macip_macaddr, macip_ipaddr, " . 
								"macip_port_hex, macip_port_list, macip_port_view, macip_imb_status, macip_imb_action, macip_mode, macip_working_now, macip_active, macip_online, macip_count_scan, macip_first_scan_date,  macip_lastchange_date) " . 
								"VALUES (" . $device["device_id"] . ",'" . $ip_adrress . "','" .  $mac_adrress . "','" . $ip_adrress . "','" . $temp_port_hex . "'," .  $arr_ports["port_list"] . ",'" . $arr_ports["port_view"] . "', 1,'" . $new_action . "', '" . $acl_int . "' ,0,1,1,1, '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "');");
								//теперь именим количество ИП-МАК записей  у устройства, на устройстве
								db_execute("UPDATE `imb_devices` SET ip_mac_total=(SELECT count(*) FROM imb_macip where device_id=" . $device["device_id"] . " ) where device_id=" . $device["device_id"] );
								//db_execute("UPDATE `imb_devices` SET count_unsaved_actions=count_unsaved_actions + 1 where device_id=" . $device["device_id"] );
								//теперь именим количество активных записей  у  порта устройства, 
								db_execute("UPDATE `imb_ports` SET count_macip_record=(SELECT count(*) FROM imb_macip where device_id=" . $device["device_id"] . " and  macip_port_list = " . $port . ") where device_id=" . $device["device_id"] . " and  port_number = " . $port );											
							}
								//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
								increment_unsaved_count($device["device_id"], '1');						
						
						}
					}
				}
			
			}
			
		imp_raise_message3(array("mes_id" => $ar_actions["mes_id"], "type" => "title_count",  "count_all" => $ar_actions["count"],"count_done" => $ar_actions["done"],"count_rez" => ($ar_actions["done"] == $ar_actions["count"])));    				
 	}
	return $ar_actions["global_rezult"];
 
 
 }
 
 
 function imb_create_imp_record_from_block($device_id, $mac_adrress, $ip_adrress, $port, $blmac_record,  $acl_mode, $auto_mode = false) {
 		
         if (isset($device_id) and is_array($device_id)) {
			$device =  $device_id;
		 }else{
			$device =  db_fetch_row ("SELECT `imb_devices`.`description` as dev_name , imb_devices.*, imb_device_types.* FROM imb_devices " .
					 " LEFT JOIN imb_device_types " .
					 " on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
					 " WHERE device_id='" . $device_id. "';");
		}
 		//проверяем на наличие данной записи на устройстве (ищем только среди реальных записей, а не банов)
		$step0 = db_fetch_cell ("SELECT count(*) FROM imb_macip WHERE device_id=" . $device["device_id"] . " and macip_ipaddr='" . $ip_adrress . "' AND `macip_banned`='0';");
 		if ($step0 == 0) {
 			$rezult = imb_create_imp_record_real ($device["device_id"], $mac_adrress, $ip_adrress, $port, $acl_mode, false);
 
 		} else {
 			//значит запись о таком ИП на этом порту этого устройства уже существует. Значит просто смена мака.
 			//imp_raise_message3(array("device_descr" => $device["dev_name"], "type" => "action_check2", "cellpading" => "true", "step_rezult" => "OK","step_data" => "OK", "message" => "Шаг 4. Задание режима записи не поддерживаеться устройством.", ));     
 			//imp_raise_message ("Обнаружена существующая запись IP-Port-Устройстлво. Вместо создания новой записи, меняем параметры у существующей." , $device, "", "");
 			$cur_macip_row = db_fetch_row ("SELECT * FROM imb_macip WHERE device_id=" . $device["device_id"] . " and macip_ipaddr='" . $ip_adrress . "';");
 			$rezult = imb_change_macip_record2 ($cur_macip_row["macip_id"], $mac_adrress, $port, $acl_mode, true);
 			
 		}
 
 		// проверяем - удалился ли автоматически блок с устройства. Если да - то удаляем его из из таблиц.
 		
		$check_step4 =          dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["snmp_oid_MacBindingBlockedPort"] . "." . $blmac_record["blmac_index"] , $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
 		if ($check_step4 == $port) {
			//подождем секунду и еще раз проверим
			sleep(2);
			$check_step4 = dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["snmp_oid_MacBindingBlockedPort"] . "." . $blmac_record["blmac_index"] , $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"],$device["snmp_get_auth_protocol"], $device["snmp_get_priv_passphrase"], $device["snmp_get_priv_protocol"],  $device["snmp_get_context"],$device["snmp_port"], $device["snmp_timeout"], $device["snmp_retries"], SNMP_WEBUI);
		}
		if ($check_step4 !== $port) {
			if (!($auto_mode)) {
					db_execute("DELETE FROM `imb_blmacs` where blmac_id=" . $blmac_record["blmac_id"] );
					db_execute("DELETE FROM `imb_temp_blmacinfo` where blmacinfo_info_id=" . $blmac_record["blmac_id"] );	
				}else{
					// если и проверка прошла успешна (т.е. запись о блоке удалило само устройство)  - то помещаем запись как отработанную и удалим после 5 просмотров.
					if ($step0 == 0) {
						db_execute("UPDATE `imb_blmacs` SET `blmac_done` = '2', `blmac_online`='0' WHERE `blmac_id` = '" . $blmac_record["blmac_id"] . "';");
					}else {
						db_execute("UPDATE `imb_blmacs` SET `blmac_done` = '3', `blmac_online`='0' WHERE `blmac_id` = '" . $blmac_record["blmac_id"] . "';");
					}
					
					
				}
				//теперь именим количество блоков у устройства
				db_execute("UPDATE `imb_devices` SET ip_mac_blocked_total=(SELECT count(*) FROM imb_blmacs where device_id=" . $blmac_record["device_id"] . " ) where device_id=" . $blmac_record["device_id"] );
				//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
				increment_unsaved_count($blmac_record["device_id"], '1');
				//db_execute("UPDATE `imb_devices` SET count_unsaved_actions=count_unsaved_actions + 1 where device_id=" . $blmac_record["device_id"] );
	 
 		} else {
 			//запись о блоке не была удалена автоматически. возможно или старая версия прошивки, или мы прописали не тот мак..
 			//api_imp_delete_blmacs ($blmac_record["blmac_id"]);
 			imp_raise_message3(array("device_descr" => $device["dev_name"], "cellpading" => "true","type" => "action_check2", "step_rezult" => "Внимание!","step_data" => "Внимание!","message" => "Создание записи не привело к автоматическому удалению блока - возможно что запись неправильная. "));    
 		}
 return $rezult;
 }
 
 function api_imp_delete_macip($macip_row, $device_id, $ban = false){

	if (isset($device_id) and is_array($device_id)) {
		$device =  $device_id;
	}else{
		$device =  db_fetch_row ("SELECT `imb_devices`.`description` as dev_name , imb_devices.*, imb_device_types.* FROM imb_devices " .
				 " LEFT JOIN imb_device_types " .
				 " on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
				 " WHERE device_id='" . $device_id . "';");
	}				 
	if ($device["type_imb_create_macip"] != 4) {
		$ar_step1 = imb_set_and_check($device, $device["snmp_oid_MacBindingStatus"]. "." . $macip_row["macip_index"], "i", 6, "del_macip", "Удаление записи  IP [" . $macip_row["macip_ipaddr"] . "], МАС [" . $macip_row["macip_macaddr"] . "] ", true, false, ($macip_row["macip_banned"] == 1));
	}else{
		$ar_step1 = imb_set_and_check($device, $device["snmp_oid_MacBindingStatus"] . ".4." . $macip_row["macip_ipaddr"] . "." . dimpb_mac_16_to_10($macip_row["macip_macaddr"]), "i", 6, "del_macip", "Удаление записи  IP [" . $macip_row["macip_ipaddr"] . "], МАС [" . $macip_row["macip_macaddr"] . "] ", true, false, ($macip_row["macip_banned"] == 1));
	}
 	if ($ar_step1["rezult_final"] == "OK") {
 			//Если устанавливаеться бан, тогда с базы не удаляем запись.
 			if ($ban == false) {
 				cacti_log("REAL DELETE FROM db !!!!!!!!!!!!!!!!!", false, "DIMPB");
 				// если и проверка прошла успешна - то удаляем запись о мак-ип из таблицы
 				db_execute("DELETE FROM `imb_macip` where macip_id=" . $macip_row["macip_id"] );
 				//db_execute("DELETE FROM `imb_temp_blmacs` where blmac_id=" . $row_id );
 				//теперь именим количество блоков у устройства, на которых включена привязка
 				db_execute("UPDATE `imb_devices` SET ip_mac_total=(SELECT count(*) FROM imb_macip where device_id=" . $device["device_id"] . " ) where device_id=" . $device["device_id"] );
 				//теперь именим количество активных записей  у порта устройства, 
 				db_execute("UPDATE `imb_ports` SET count_macip_record=(SELECT count(*) FROM imb_macip where device_id=" . $device["device_id"] . " and  macip_port_list = " . $macip_row["macip_port_list"] . ") where device_id=" . $device["device_id"] . " and  port_number = " . $macip_row["macip_port_list"] );			
 			}
 				//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
 				increment_unsaved_count($device["device_id"], '1');			
 	}
 return $ar_step1["rezult_final"];
 }
 
 function api_imp_delete_temp_macip($temprow_id){
 	$tempmacip_row =  db_fetch_row ("SELECT * FROM imb_temp_macip WHERE macip_id=" . $temprow_id . ";");
 	$tempdevice =  db_fetch_row ("SELECT `imb_devices`.`description` as dev_name,  imb_devices.*, imb_device_types.* FROM imb_devices " .
 				" LEFT JOIN imb_device_types " .
 				" on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
 				" WHERE device_id='" . $tempmacip_row["device_id"]. "';");
 	
 	$ar_step1 = imb_set_and_check($tempdevice, $tempdevice["snmp_oid_MacBindingStatus"]. "." . $tempmacip_row["macip_ipaddr"], "i", 6, "del_macip", "Удаление записи  IP [" . $tempmacip_row["macip_ipaddr"] . "], МАС [" . $tempmacip_row["macip_macaddr"] . "] ", true);
 	if ($ar_step1["rezult_final"] == "OK") {
 				// если и проверка прошла успешна - то удаляем запись о мак-ип из таблицы
 				db_execute("DELETE FROM `imb_temp_macip` where macip_id=" . $temprow_id );
 	}
 return $ar_step1["rezult_final"];
 }
 
 function imp_delete_logs($log_id) {
 
 db_execute("DELETE FROM `imb_log` where log_id='" . $log_id . "';");
 
 
 }

  function api_imp_change_free_macip($row_id, $ban = false){
 	


 	$macip_row =  db_fetch_row ("SELECT * FROM imb_macip WHERE macip_id=" . $row_id . ";");
 	$device =  db_fetch_row ("SELECT `imb_devices`.`description` as dev_name,  imb_devices.*, imb_device_types.* FROM imb_devices " .
 				" LEFT JOIN imb_device_types " .
 				" on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
 				" WHERE device_id='" . $macip_row["device_id"]. "';");
 	//$ar_step1 = imb_set_and_check($device, $device["snmp_oid_MacBindingStatus"]. "." . $macip_row["macip_ipaddr"], "i", 6, "del_macip", "Удаление записи  IP [" . $macip_row["macip_ipaddr"] . "], МАС [" . $macip_row["macip_macaddr"] . "] ", true, false, ($macip_row["macip_banned"] == 1));
 	//if ($ar_step1["rezult_final"] == "OK") {
 			//Если устанавливаеться бан, тогда с базы не удаляем запись.
 			if ($macip_row["macip_may_move"] == 0) {
 				db_execute("UPDATE `imb_macip` SET `macip_may_move`=1 where macip_id=" . $row_id );
 			}else{
 				db_execute("UPDATE `imb_macip` SET `macip_may_move`=0 where macip_id=" . $row_id );			
			}
 				//увеличиваем количество несохраненных операций у устройства, или проверяем на автосохранение..
 				increment_unsaved_count($device["device_id"], '1');	
 	imp_raise_message3(array("device_descr" => $device["description"], "type" => "action_check2", "object"=>"free","cellpading" => true, "message" => "Изменение свободы у привязки " . $macip_row["macip_ipaddr"] . " c " . $macip_row["macip_may_move"] . " на обратное", "step_data" => "OK", "step_rezult" => "OK", "check_data" => "OK", "check_rezult" => ""));     
	
 return "OK";
 }
 
function api_dimpb_delete_net($net_id) {
 
	db_execute("DELETE FROM `imb_auto_updated_nets` where `net_id`='" . $net_id . "';");
 
}
 

function imb_create_net_record($net_ip, $net_mask, $net_descr, $net_type = '1', $net_device_id = '0', $net_ttl = 0 ) {
 
	db_execute("INSERT INTO  `imb_auto_updated_nets` (`net_ipaddr`,`net_mask`,`net_type`,`net_device_id`,`net_description`,`net_change_time`,`net_ttl`,`net_change_user`) " .
				" VALUES (" . 
				" inet_aton(inet_ntoa(inet_aton('" . $net_ip . "') & inet_aton('" . $net_mask . "'))) , " .
				" inet_aton('" . $net_mask . "') , " .
				" '" . $net_type . "' , " .
				" '" . $net_device_id . "' , " .
				" '" . $net_descr . "' , " .
				" NOW() , " .
				" '" . $net_ttl . "' , " .
				" '" . $_SESSION["sess_user_id"] . "'  " .
				" )");
 
}


function imb_change_net_record($net_id,$net_ip, $net_mask, $net_descr, $net_type = '1', $net_device_id = '0', $net_ttl = 0 ) {
 
	db_execute("UPDATE `imb_auto_updated_nets` SET " .
				" `net_ipaddr`=inet_aton(inet_ntoa(inet_aton('" . $net_ip . "') & inet_aton('" . $net_mask . "'))) , " .
				" `net_mask`=inet_aton('" . $net_mask . "') , " .
				" `net_type`='" . $net_type . "' , " .
				" `net_device_id`='" . $net_device_id . "' , " .
				" `net_description`='" . $net_descr . "' , " .
				" `net_ttl`='" . $net_ttl . "' , " .
				" `net_change_user`='" . $_SESSION["sess_user_id"] . "'  " .
				" WHERE `net_id`='" . $net_id . "' ;");
 
}
  
 
 
 function imb_change_port_name($port_row, $device, $str_port_name){
 	
 	if (snmp_set_method($device["snmp_set_version"]) == 1) {
 		$str_new_port_name=  $str_port_name ;
 	} else {
 		$str_new_port_name= html_entity_decode("&quot;") . $str_port_name . html_entity_decode("&quot;");
 	}
 	
 	$ar_step1 = imb_set_and_check($device, $device["snmp_oid_ifAlias"] . "." . $port_row["port_number"], "s", $str_new_port_name, "port_name", "Изменение описание порта № " . $port_row["port_number"] . "  на [" . $str_port_name . "]", true);
 	if ($ar_step1["rezult_final"] == "OK") {
 			db_execute("UPDATE `imb_ports` set port_name = '" . $str_port_name . "' where port_id=" . $port_row["port_id"] );
 			increment_unsaved_count($device["device_id"], '1');
 	}
 return $ar_step1["rezult_final"];
 }
 
 
 function imb_check_for_oid ($oid) {
 
 if (substr($oid, 0,1) == ".") {
 } else {
 	$oid="." . $oid;
 }
 
 if (substr($oid, -1) == ".") {
 	$oid=substr($oid, 0, -1);
 } 
 
 return $oid;
 };
 
 function imb_covert_rezult_2_color ($str_rezult) {
 $rezult="";
 
 if ( $str_rezult == "OK" ) {
 	$rezult="00BF47";
 } elseif ($str_rezult == "OK") {
 	$rezult="";
 } else {
 	$rezult="ff7d00";
 }
 return $rezult;
 };
 
 //function imb_change_macip_record2 ($macip_id, $mac_adrress, $port, $acl_mode, $new = false) {
 function imb_change_macip_record2 ($macip_id, $mac_adrress, $port, $acl_mode, $new = false) {
 $step=1;	
 $ar_actions = array(); /*массив хранит данные о том, сколько и каких изменений нужно сделать.*/
 
 		$device_id =  db_fetch_cell ("SELECT device_id FROM imb_macip WHERE macip_id=" . $macip_id . ";");
 		//$device =  db_fetch_row ("SELECT * FROM imb_devices WHERE device_id=" . $device_id . ";");
 		//$device["device_type_global"] =  db_fetch_row ("SELECT * FROM imb_device_types WHERE device_type_id=" . $device["device_type_id"] . ";");
 		$macip_row =  db_fetch_row ("SELECT * FROM imb_macip WHERE macip_id=" . $macip_id . ";");
 		$device =  db_fetch_row ("SELECT `imb_devices`.`description` as dev_name , imb_devices.*, imb_device_types.* FROM imb_devices " .
 				" LEFT JOIN imb_device_types " .
 				" on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
 				" WHERE device_id='" . $device_id. "';");
 
 if ($mac_adrress != $macip_row["macip_macaddr"]) {
 	$ar_actions["mac"] = 1;}
 if ($port != $macip_row["macip_port_list"]) {
 	$ar_actions["port"] = 1;}
 if ($acl_mode != (imp_convert_macip_mode_2str($macip_row["macip_mode"], $device_id))) {
 	$ar_actions["mode"] = 1;}
 $ar_actions["count"] = count($ar_actions);
 $ar_actions["done"] = 0;
 	
 if ($ar_actions["count"] > 0) {
 		if ($new) {
 			$message = "Обнаружена существующая запись Устройство-IP-Mac-Port. Вместо создания новой записи, меняем параметры у существующей. ";
 		}else{
 			$message = "Изменение параметров у существующей записи Устройство-IP-Mac-Port. ";
 		}
 		//"Обнаружена существующая запись IP-Port-Устройство. Вместо создания новой записи, меняем параметры у существующей."
 		$ar_actions["mes_id"] = imp_raise_message3(array("device_descr" => $device["dev_name"], "cellpading" => "false","type" => "title_count", "message" => $message));    
 		
 		if ((isset($ar_actions["mac"])) && ($ar_actions["mac"] == 1)) {
			//Проверим на уникальность MAC  с этом сегменте
		$existed_macs = db_fetch_assoc("SELECT * FROM imb_macip where macip_macaddr='" . $mac_adrress . "' and macip_ipaddr <> '" . $macip_row["macip_ipaddr"] . "' and (inet_aton('" . $macip_row["macip_ipaddr"] . "') & INET_ATON('255.255.255.0')) = (inet_aton(`macip_ipaddr`) & INET_ATON('255.255.255.0'));");
		if (sizeof($existed_macs)>0) {
					
			imb_send_message2admin($mac_adrress, $macip_row["macip_ipaddr"]);
		}else{	
		
 			$ar_step1 = imb_set_and_check($device, $device["snmp_oid_MacBindingMac"] . "." . $macip_row["macip_ipaddr"], "x", imb_clear_macadrress($mac_adrress), "mac", "Шаг " . $step . ". Изменение MAC-адреса с [" . $macip_row["macip_macaddr"] . "] на [" . $mac_adrress . "] ", true, true,($macip_row["macip_banned"] == 1));
 			if ($ar_step1["rezult_final"] == "OK") {
 				$ar_actions["done"] = $ar_actions["done"] + 1;
 				db_execute("UPDATE `imb_macip` SET macip_macaddr='" . $mac_adrress . "', macip_lastchange_date='" . date("Y-m-d H:i:s") . "' where macip_id=" . $macip_row["macip_id"] . ";");
 				//$log_message, $log_object, $log_object_id, $log_operation,$log_device_id, $log_rezult, $log_rezult_check
 			};
			//imp_raise_message2 ("|--> Шаг " . $step . ". Изменение MAC-адреса с [" . $macip_row["macip_macaddr"] . "] на [" . $mac_adrress . "] " , $device, imb_check_mes_create_ipmac_s1_check($step1,$mac_adrress), $step1, imb_check_mes_create_ipmac_s1($check_step1, $mac_adrress ), $check_step1);
			//db_store_imp_log("Изменение MAC-адреса с [" . $macip_row["macip_macaddr"] . "] на [" . $mac_adrress . "] для IP=[" . $macip_row["macip_ipaddr"] . "]", "ipmac", $macip_id, "change",$device_id, imb_check_mes_create_ipmac_s1_check($step1,$mac_adrress), $step1, imb_check_mes_create_ipmac_s1($check_step1, $mac_adrress ), $check_step1);
			$step = $step + 1;
		}
 		}
 		
 		if ((isset($ar_actions["port"])) && ($ar_actions["port"] == 1)) {
 		//if ($port != $macip_row["macip_port_list"]) {
 			if ($device["type_imb_create_macip"] != 4) {
				$temp_port_hex = imb_convert_port_to_hex($port, $device["type_port_num_conversion"], $device["type_port_use_long"],$device["type_use_more_32x_port"]);
				$arr_ports = convert_Xport_to_view_string($temp_port_hex, $device["type_port_num_conversion"] );
				$ar_step2 = imb_set_and_check($device, $device["snmp_oid_MacBindingPorts"] . "." . $macip_row["macip_ipaddr"], "x", $temp_port_hex, "port", "Шаг " . $step . ". Изменение номера порта с [" . $macip_row["macip_port_view"] . "] на [" . $arr_ports["port_view"] . "] ",  true, true,($macip_row["macip_banned"] == 1));
 			}else{
				$arr_ports["port_view"] = $port;
				$arr_ports["port_list"] = $port;
				$ar_step2 = imb_set_and_check($device, $device["snmp_oid_MacBindingPorts"] . ".4." . $macip_row["macip_ipaddr"] . "." . dimpb_mac_16_to_10($macip_row["macip_macaddr"]), "i", $port, "port", "Шаг " . $step . ". Изменение номера порта с [" . $macip_row["macip_port_view"] . "] на [" . $arr_ports["port_view"] . "] ",  true, true,($macip_row["macip_banned"] == 1));

			}
			if ($ar_step2["rezult_final"] == "OK") {
 				$ar_actions["done"] = $ar_actions["done"] + 1;
 				//imp_raise_message ("____|--> Шаг 2. Изменение номера порта с [" . $macip_row["macip_port_list"] . "] на [" . $port . "] " , $device, $step2, $check_step2);
 				db_execute("UPDATE `imb_macip` SET macip_port_list='" . $arr_ports["port_list"] . "', macip_port_hex='" . $temp_port_hex .  "', macip_port_view='" . $arr_ports["port_view"] .  "', macip_lastchange_date='" . date("Y-m-d H:i:s") . "' where macip_id=" . $macip_row["macip_id"] . ";");
 				//теперь именим количество активных записей  у нового порта устройства, 
 				db_execute("UPDATE `imb_ports` SET count_macip_record=(SELECT count(*) FROM imb_macip where device_id=" . $device["device_id"] . " and  macip_port_list = " . $port . ") where device_id=" . $device["device_id"] . " and  port_number = " . $port );			
 				//теперь именим количество активных записей  у старого порта устройства, 
 				db_execute("UPDATE `imb_ports` SET count_macip_record=(SELECT count(*) FROM imb_macip where device_id=" . $device["device_id"] . " and  macip_port_list = " . $macip_row["macip_port_list"] . ") where device_id=" . $device["device_id"] . " and  port_number = " . $macip_row["macip_port_list"] );						
 			}
 		//imp_raise_message2 ("|--> Шаг " . $step . ". Изменение номера порта с [" . $macip_row["macip_port_view"] . "] на [" . $arr_ports["port_view"] . "] " , $device, imb_check_mes_create_ipmac_s3($step2, $temp_port_hex), $step2, imb_check_mes_create_ipmac_s3($check_step2, $temp_port_hex), $check_step2 );
 		//db_store_imp_log("Изменение номера порта с [" . $macip_row["macip_port_view"] . "] на [" . $arr_ports["port_view"]  . "]  для IP=[" . $macip_row["macip_ipaddr"] . "]", "ipmac", $macip_id, "change",$device_id, imb_check_mes_create_ipmac_s3($step2, $temp_port_hex), $step2, imb_check_mes_create_ipmac_s3($check_step2, $temp_port_hex), $check_step2 );
 		$step = $step + 1;
 		}	
 		
 		
 		if ((isset($ar_actions["mode"])) && ($ar_actions["mode"] == 1)) {
 			if ((isset($device["snmp_oid_MacBindingMode"])) && (trim($device["snmp_oid_MacBindingMode"]) <> "") ){
 			//if ((isset($device["device_type_global"]["snmp_oid_MacBindingMode"])) && (trim($device["device_type_global"]["snmp_oid_MacBindingMode"]) <> "") ){
 				$acl_int = imp_convert_macip_mode_2integer($acl_mode, $device_id);
 				$ar_step3 = imb_set_and_check($device, $device["snmp_oid_MacBindingMode"] . "." . $macip_row["macip_ipaddr"], "i", $acl_int, "mode", "Шаг " . $step . ". задание режима записи " . $acl_mode . "." ,  true, true,($macip_row["macip_banned"] == 1));
 				//$step4 = imp_snmp_set($device["hostname"], $device["snmp_set_community"],$device["device_type_global"]["snmp_oid_MacBindingMode"] . "." . $macip_row["macip_ipaddr"] , "i", $acl_int, $device["snmp_set_version"],$device["snmp_set_username"],$device["snmp_set_password"],161,1000);
 				//$check_step4 = (dimpb_snmp_get($device["hostname"], $device["snmp_get_community"],$device["device_type_global"]["snmp_oid_MacBindingMode"] . "." . $macip_row["macip_ipaddr"] , $device["snmp_get_version"],$device["snmp_get_username"],$device["snmp_get_password"]) == $acl_int);
 				//imp_raise_message2 ("|--> Шаг " . $step . ". задание режима записи " . $acl_string . "." , $device, $step4, $step4, $check_step4, $check_step4);
 				if ($ar_step3["rezult_final"] == "OK") {
 					$ar_actions["done"] = $ar_actions["done"] + 1;
 					db_execute("UPDATE `imb_macip` SET macip_mode='" . $acl_int . "', macip_lastchange_date='" . date("Y-m-d H:i:s") . "' where macip_id=" . $macip_row["macip_id"] . ";");
 				}
 			}else{
 				imp_raise_message3(array("device_descr" => $device["dev_name"], "type" => "action_check2", "cellpading" => "true", "step_rezult" => "OK","step_data" => "OK", "message" => "Шаг " . $step . ". Задание режима записи не поддерживаеться устройством.", ));     
 				$ar_actions["done"] = $ar_actions["done"] + 1;
 				//imp_raise_message2 ("  |--> Шаг " . $step . ". изменение режима записи не выполнено." , $device, $step4, $step4, $check_step4, $check_step4);
 				//ERROR [No Such Object available on this agent at this OID]
 				//imp_raise_message3(array("device_descr" => $device["description"], "type" => "action_check2", "cellpading" => "true", "step_rezult" => "ERROR","step_data" => "No Such Object available on this agent at this OID", "message" => "Шаг " . $step . ". изменение режима записи не выполнено.", ));     
 			}
 			
 			
 		}
 if ($step > 1) {
     increment_unsaved_count($device["device_id"], '1');
 };
 	imp_raise_message3(array("mes_id" => $ar_actions["mes_id"], "type" => "title_count",  "count_all" => $ar_actions["count"],"count_done" => $ar_actions["done"],"count_rez" => ($ar_actions["done"] == $ar_actions["count"])));    
 }else{
 	imp_raise_message3(array("device_descr" => $device["description"], "type" => "title", "message" => "Изменение параметров у существующей записи не требуется."));    
 }		
 	return ($ar_actions["done"] == $ar_actions["count"]);	
 		
 
 
 };
 
   function print_array($a,$btag="",$etag="") {
     if(is_array($a)) {
       printf("<table cellpadding=0 cellspacing=0>");
		foreach ($a as $one => $two) {
         printf("\n<tr valign=baseline><td>$btag$one$etag</td><td>".
                "&nbsp;$btag=>$etag</td>".
                "<td align=right>&nbsp;%s</td></tr>\n"
                ,sprint_array($two,$btag,$etag));
       }
       printf("</table>");
     } 
     else {
       printf("%s%s%s",$btag,$a,$etag);
     } 
   }
     
   
     
       
   function sprint_array($a,$btag="",$etag="") {
     if(is_array($a)) {
       $out=sprintf("<table cellpadding=0 cellspacing=0>");
	   foreach ($a as $one => $two) {
         $out .= sprintf("\n<tr valign=baseline><td>$btag$one$etag</td><td>".
                         "&nbsp;$btag=>$etag</td>".
                         "<td align=right>&nbsp;%s</td></tr>\n"
                         ,sprint_array($two,$btag,$etag));
       }
       $out .= "</table>";
       return $out;
     }
     else {
       return sprintf("%s%s%s",$btag,$a,$etag);
     }
   }
 
 function imp_print_last_poll_stat() {
 	$last_poll_date = db_fetch_cell ("SELECT `value` FROM settings WHERE `name` = 'dimpb_scan_date';");
     print ("Последние опрос был в:" . imb_fromat_datetime($last_poll_date));
 }
 function convert_dlink_port_type($old_port_type) {
 		if (substr_count($old_port_type, "(") > 0) {
 			$pos1 = strpos($old_port_type, "(");
 			$pos2 = strpos($old_port_type, ")");
 			$rezult = substr($old_port_type, $pos1+1, $pos2-$pos1-1);
 		} else{
 			$rezult=old_port_type;
 		}
 		
 return $rezult;  
 }
 function imp_translate_mac_address($old_mac_address) {
 	$old_mac_address = str_replace("-", ":", $old_mac_address);
 	
 	//$old_mac_address = str_replace("-", ":", $old_mac_address);
 return $old_mac_address;  
 }
 
 /*impb_translate_ip_address*/
 function impb_translate_ip_address($old_ip_address) {
 //ereg("^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", "172.005.5.5")
 	//[172.18.1.1  ]  => [172.18.1.1]
 	$old_ip_address = trim($old_ip_address);
 	//[172.18-1-1]  => [172.18.1.1]
 	$old_ip_address = str_replace("-", ".", $old_ip_address);
 	//[172,18,1.1]  => [17218.1.1]
 	$old_ip_address = str_replace(",", ".", $old_ip_address);
 	//[172 18 1-1]  => [172.18.1.1]
 	$old_ip_address = str_replace(" ", ".", $old_ip_address);
 	
 	$new_ip_address = $old_ip_address;
 	//[172.018.001.1]  => [172.18.1.1]
 		$pieces = explode(".", $old_ip_address);
 		$new_pieces = array();
 	if (count($pieces) == 4) {
 		$new_pieces[0] = intval($pieces[0]);
 		$new_pieces[1] = intval($pieces[1]);
 		$new_pieces[2] = intval($pieces[2]);
 		$new_pieces[3] = intval($pieces[3]);
 		$new_ip_address = implode(".", $new_pieces);
 	}
 return $new_ip_address; 
 }
 function translate_port_view ($port_view){
 $port_view = str_replace(" ", ",", trim($port_view));
 $port_view = str_replace(".", ",", $port_view);
 return $port_view;
 }
 function increment_unsaved_count($device_id, $count_opertions=1) {
 	
 	$max_autosave_count = read_config_option("dimpb_autosave_count");
 	if ($max_autosave_count > 0){
 		$unsaved_operations = db_fetch_cell ("SELECT count_unsaved_actions FROM imb_devices WHERE device_id=" . $device_id . ";");
 	  	if (($unsaved_operations + $count_opertions) >= $max_autosave_count) {
 	  		db_store_imp_log("Выполняеться автосохранение текущей кофигурации", "device", $device_id, "auto_save",$device_id, "OK","OK", "OK", "OK");
 	      imp_save_config_main($device_id);
 	  	} else {
 	  		db_execute("UPDATE `imb_devices` SET count_unsaved_actions=count_unsaved_actions + " . $count_opertions . " where device_id=" . $device_id );
 	  	}
   }
 }
 
 function duplicate_device_type($_device_type_id, $device_type_title) {
 	global $fields_impb_device_type_edit;
 
 	$device_type_template = db_fetch_row("select * from imb_device_types where device_type_id=$_device_type_id");
 	//$device_type_template_graphs = db_fetch_assoc("select * from device_type_template_graph where device_type_template_id=$_device_type_template_id");
 	//$device_type_template_data_queries = db_fetch_assoc("select * from device_type_template_snmp_query where device_type_template_id=$_device_type_template_id");
 
 	/* substitute the title variable */
 	$device_type_template["description"] = str_replace("<template_title>", $device_type_template["description"], $device_type_title);
 
 	/* create new entry: host_template */
 	$save["device_type_id"] = 0;
 
 	reset($fields_impb_device_type_edit);
	foreach ($fields_impb_device_type_edit as $field => $array) {
 		//if ((!ereg("^hidden", $array["method"])) && (!ereg("^spacer", $array["method"]))) {
		if ((!preg_match("/^hidden/", $array["method"])) && (!preg_match("/^spacer/", $array["method"]))) {
 			$save[$field] = $device_type_template[$field];
 		}
 	}
 
 	$device_type_id = sql_save($save, "imb_device_types", "device_type_id");
 
 }
 
 /* get_colored_status - given a status, return the colored text in HTML
      format suitable for display
    @arg $status - the status type of the device as defined in config_constants.php
    @returns - a string containing html that represents the device's current status */
 function get_colored_status($status) {
 
 	switch ($status) {
 		case "other":
 			return "<span style='color: #750F7D'>other</span>"; break;
 		case "Enable":
 			return "<span style='color: #198e32'>Enable</span>"; break;
 		case "Disable":
 			return "<span style='color: #a1a1a1'>Disable</span>"; break;
 		case "not_use":
 			return "<span style='color: #a1a1a1'>not_use</span>"; break;
 		default:
 			return "<span style='color: #0000ff'>unk</span>"; break;
 	}
 }
 
 function imb_ban_create_record($banip_ipaddr, $banip_type, $banip_manual = true, $banip_expiration_date, $banip_message) {
 #First - check if that ip exist in ban
 $check_for_exist_ban = db_fetch_cell ("SELECT count(*) FROM imb_banip WHERE `banip_ipaddr`='" . $banip_ipaddr . "';");
 
 if ($check_for_exist_ban == 0) {
 	db_execute("INSERT `imb_banip`(banip_ipaddr,banip_aplled,banip_aproved,banip_type,banip_manual,banip_author_id,banip_install_date,banip_expiration_date,banip_message) values('" . $banip_ipaddr . "',0,0,'" . $banip_type . "',1,1,NOW(),'" . $banip_expiration_date . "','" . $banip_message . "');" );
 	imp_raise_message3(array("device_descr" => "BANs IP Table", "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => "ОК","message" => "Содание записи бана IP [" . $banip_ipaddr . "] с причиной [" . $banip_message . "]"));    
 }
 imp_raise_message3(array("device_descr" => "BANs IP Table", "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => "Запись уже существует","message" => "Содание записи бана IP [" . $banip_ipaddr . "] с причиной [" . $banip_message . "]"));    
 }
 
 function imb_ban_approv($banip_id, $banip_ip,$banip_aplled,$banip_aproved) {
	 if (($banip_aproved==0) && ($banip_aplled==1)) {
	 imb_ban_aplly ($banip_id, 0) ;
	 }
	 db_execute("UPDATE `imb_banip` SET banip_aproved='" . $banip_aproved . "' WHERE banip_id='" . $banip_id . "';" );
	 imp_raise_message3(array("device_descr" => "BANs IP Table", "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => "ОК","message" => "Установка разрешения бана IP [" . $banip_ip . "] в [" . $banip_aproved . "]"));    
	 
 }
 
 function imb_ban_delete($banip_id, $banip_ip) {
 imb_ban_aplly ($banip_id, false);
 db_execute("DELETE FROM `imb_banip` WHERE banip_id='" . $banip_id . "';" );
 imp_raise_message3(array("device_descr" => "BANs IP Table", "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => "ОК","message" => "Удаление записи бана для IP [" . $banip_ip . "]"));    
 }
 
 function imb_ban_do_real_ban ($macip_id, $ban) {
 $int_ban = (($ban == true) ? 1 : 0);
 $str_ban = (($ban == true) ? "BANNED(1)" : "UNbanned(0)");    
 $macip_row =  db_fetch_row ("SELECT imb_macip.*, `imb_devices`.`description` as dev_name FROM imb_macip LEFT JOIN imb_devices on (imb_macip.device_id = imb_devices.device_id)  WHERE macip_id=" . $macip_id . ";");
 $cur_ban_state =  $macip_row["macip_banned"];
 if ($cur_ban_state != $int_ban) {
 	if ($ban) {
 		$rezult = api_imp_delete_macip($macip_row, $macip_row["device_id"], true);
 	} else {
 		$rezult = imb_create_imp_record($macip_row["device_id"], $macip_row["macip_macaddr"], $macip_row["macip_ipaddr"], $macip_row["macip_port_view"], imp_convert_macip_mode_2str($macip_row["macip_mode"],$macip_row["device_id"]), true) ;
 	}
 	if ($rezult == true) {
 		db_execute("UPDATE `imb_macip` SET `macip_banned`='" . $int_ban . "' where macip_id='" . $macip_id . "';" );
 	}
 }else {
 	imp_raise_message3(array("device_descr" => $macip_row["dev_name"], "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => "Нет изменений режима","message" => "Установка режима бана для IP [" . $macip_row["macip_ipaddr"] . "] в [" . $str_ban . "]"));    
 	$rezult = true;
 }
 return $rezult;
 }
 
 function imb_ban_aplly ($banip_id, $aplly_status) {
 
 $banip_ips = db_fetch_assoc("SELECT macip_id, macip_ipaddr, banip_id, banip_aplled, banip_aproved,banip_ipaddr, banip_manual, imb_macip.device_id, imb_devices.description as dev_name,  imb_device_types.setting_imb_use_autoban " .
 	" FROM imb_macip " .
 	" left join imb_banip " .
 	"  on (INET_ATON(imb_macip.macip_ipaddr) = INET_ATON(imb_banip.banip_ipaddr)) " .
 	"left join imb_devices " .
 	"  on (imb_macip.device_id = imb_devices.device_id) " .
 	"left join imb_device_types " .
 	"  on (imb_devices.device_type_id = imb_device_types.device_type_id) " .
 	"where imb_banip.banip_id in ('" . $banip_id . "') and  imb_macip.macip_banned=" . (($aplly_status) ? "0" : "1") . ";");
 $rezult=false;	
 if (sizeof($banip_ips)>0) {	
 	foreach($banip_ips as $key => $banip_ip) {
 		$rezult=false;
 		if ($aplly_status) {
 			if ($banip_ip["banip_aproved"]==0) {
 				imb_ban_approv($banip_ip["banip_id"], $banip_ip["banip_ipaddr"],$banip_ip["banip_aplled"],1);
 			}
 			if ($banip_ip["banip_manual"]==1) {
 				$rezult=imb_ban_do_real_ban($banip_ip["macip_id"],$aplly_status);
 				imp_raise_message3(array("device_descr" => $banip_ip["dev_name"], "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => $rezult,"message" => "Установка бана для IP [" . $banip_ip["macip_ipaddr"] . "] в [" . $aplly_status . "]"));    
 			}else{
 				if ($banip_ip["setting_imb_use_autoban"]==1) {
 					$rezult=imb_ban_do_real_ban($banip_ip["macip_id"],$aplly_status);
 					imp_raise_message3(array("device_descr" => $banip_ip["dev_name"], "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => $rezult,"message" => "Установка бана для IP [" . $banip_ip["macip_ipaddr"] . "] в [" . $aplly_status . "]"));    
 				}else{
 					imp_raise_message3(array("device_descr" => $banip_ip["dev_name"], "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => "Установка автобана на этом устройстве запрещена","message" => "Установка  бана для IP [" . $banip_ip["macip_ipaddr"] . "] в [" . $aplly_status . "]"));    
 					$rezult=true;
 				}
 			}
 		}else{ //Если происходит снятие бана, то тогда все равно запускаем процедуру, на всякий случай...
 			$rezult=imb_ban_do_real_ban($banip_ip["macip_id"],$aplly_status);
 			imp_raise_message3(array("device_descr" => $banip_ip["dev_name"], "cellpading" => "false","type" => "action_check2", "step_rezult" => "OK","step_data" => $rezult,"message" => "Установка бана для IP [" . $banip_ip["macip_ipaddr"] . "] в [" . $aplly_status . "]"));    
 		}
 //		imb_ban_do_real_ban($banip_ip["macip_id"],$aplly_status);
 		//$arraydisplay[$array[$key]] = $values;
 	}
 
 }else{
 	imp_raise_message3(array("device_descr" => "BANs IP Table", "cellpading" => "false","type" => "action_check2", "step_rezult" => "Warning","step_data" => "NO IP-MAC-PORT","message" => "Снятие бана для ID [" . $banip_id . "]"));    
 }
 if ($rezult==true) {
 	db_execute("UPDATE `imb_banip` SET `banip_aplled`='" . $aplly_status . "', `banip_counts`='" . sizeof($banip_ips) . "' where banip_id='" . $banip_id . "';");
 }
 return $rezult;
 }
 
 
 function test1 ($pattern, $subject) {
 preg_match($pattern, $subject, $matches);
 return $matches;
 }

 function dimpb_add_row_any ($sql) {
 	 $rezult=db_fetch_assoc($sql);
	 
	 if (is_array($rezult)) {
		array_unshift($rezult, array("id"=>"0", "name"=>"Any Device"));
	 }
	  return $rezult;
 } 
 
function imb_send_message2admin($error_mac, $error_ip) {
 
	$existed_macs = db_fetch_assoc("SELECT lv.ag_num, lv.f_flat, l.vg_id, i.macip_ipaddr FROM imb_macip i " .
		" LEFT JOIN lb_staff l on (inet_aton(`macip_ipaddr`)=l.segment) " .
		" LEFT JOIN lb_vgroups_s lv ON (l.vg_id=lv.vg_id) " .
		" where macip_macaddr='" . $error_mac . "' " .
		" and (inet_aton('" . $error_ip . "') & INET_ATON('255.255.255.0')) = (inet_aton(`macip_ipaddr`) & INET_ATON('255.255.255.0')) " .
		" and l.vg_id<>3000;");
	
	if (sizeof($existed_macs)>0) {
		
		
		$str_existed_ips="";
		foreach($existed_macs as $key => $existed_mac) {
			if ($str_existed_ips == "") {
				$str_existed_ips = $str_existed_ips;
			}else{
				$str_existed_ips = $str_existed_ips . "\r\n";
			}
			$str_existed_ips = $str_existed_ips . "(" . $existed_mac["ag_num"] . ", " . $existed_mac["macip_ipaddr"] . ", kom." . $existed_mac["f_flat"] . ")";
		}
		$str_sms="DUB " . $error_ip . "=" . substr($error_mac, -5) . " [" . $str_existed_ips . "].";
		db_store_imp_log("2",  "0", "error_dblt", "0", "0", "0", "Создание записи для IP=" . $error_ip . "=" . $error_mac . " невозможно из-за дубликата [" . $str_existed_ips . "].", "error", 0, "error", 0);
		imp_raise_message3(array("device_descr" => "null", "type" => "action_check2", "cellpading" => "true", "step_rezult" => "ERROR","step_data" => "ERROR", "message" => "Создание записи для MAC=" . $error_mac . " невозможно из-за дубликата [" . $str_existed_ips . "].", ));     	
		
		//dimpb_sendemail('gthe72@yandex.ru','root','error_dblt',"Создание записи для IP=" . $error_ip . "=" . $error_mac. " невозможно из-за дубликата [" . $str_existed_ips . "].", 0);
		//dimpb_sendemail('te7y9yru@sms.ru','root','error_dblt',"DUB " . $error_ip . "=" . substr($error_mac, -5) . " [" . $str_existed_ips . "].", 1);
		
		//защита от мак-спама - не более 1 смс за 60 минут
		$cnt_sms = db_fetch_cell("SELECT count(*) FROM sms.sentitems where CreatorID='sys_impb' and TextDecoded like '" . $str_sms . "%' and InsertIntoDB > DATE_ADD(CURRENT_TIMESTAMP, INTERVAL -60 MINUTE);");
		//cacti_log("ATTENTION: ERROR DIMPB " . "SELECT count(*) FROM sms.sentitems where CreatorID='sys_impb' and TextDecoded='" . $str_sms . "' and InsertIntoDB > DATE_ADD(CURRENT_TIMESTAMP, INTERVAL -60 MINUTE);" , TRUE);	
		
		if ($cnt_sms == 0) {
			dimpb_sendemail('gthe72@yandex.ru','root','error_dblt',"Создание записи для IP=" . $error_ip . "=" . $error_mac. " невозможно из-за дубликата [" . $str_existed_ips . "]. cnt_sms=" . $cnt_sms, 0);
			//проверим - может в очереди уже есть такие смс
			$cnt_sms_outbox = db_fetch_cell("SELECT count(*) FROM sms.outbox where CreatorID='sys_impb' and TextDecoded like '" . $str_sms . "%';");
			if ($cnt_sms_outbox < 2) {
				db_execute("INSERT INTO sms.outbox (SendBefore,SendAfter,DestinationNumber, TextDecoded, CreatorID, Coding) VALUES ('21:00:00','8:55:00','+79377999153' , '" . $str_sms . "' , 'sys_impb', 'Default_No_Compression');");
				db_execute("INSERT INTO sms.outbox (SendBefore,SendAfter,DestinationNumber, TextDecoded, CreatorID, Coding) VALUES ('21:00:00','9:00:00','+79377999152' , '" . $str_sms . "' , 'sys_impb', 'Default_No_Compression');");	
			}			
		}else{
			dimpb_sendemail('gthe72@yandex.ru','root','error_dblt',"Создание записи для IP=" . $error_ip . "=" . $error_mac. " невозможно из-за дубликата [" . $str_existed_ips . "]. СМС-спам защита", 0);
		}
		

	}
	 
 }
 
 
function dimpb_sendemail($to, $from, $subject, $message, $email_format) {
 	
	if (read_config_option("camm_dependencies")) {
 		impb_debug("  Sending Alert email to '" . $to . "'\n");
		if ($email_format == 1) {
			send_mail($to, $from, $subject, $message);
		}else{
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
			send_mail($to, $from, $subject, "<html><body>$message</body></html>", $headers); 
		}
 	} else {
 		impb_debug("  Error: Could not send alert, you are missing the Settings plugin\n");
 	}
 }

function impb_group_tabs() {
	global $config;

	/* present a tabbed interface */
	$tabs_dimpb = db_fetch_assoc("SELECT * FROM imb_tabs order by tab_id;");
	
	if (sizeof($tabs_dimpb)>0) {

/*		$tabs_dimpb = array(
			"sites" => "Sites",
			"devices" => "Devices",
			"ips" => "IP Ranges",
			"arp" => "IP Addresses",
			"macs" => "MAC Addresses",
			"interfaces" => "Interfaces",
			"graphs" => "Graphs");
*/
		/* set the default tab */
		$current_tab = $_REQUEST["dtab"];

		if (!isset($config["base_path"])) {
			/* draw the tabs */
			print "<div class='tabs'>\n";

			if (sizeof($tabs_dimpb)) {
			foreach (array_keys($tabs_dimpb) as $tab_short_name) {
				if (!isset($config["base_path"])) {
					print "<div class='tabDefault'><a " . (($tab_short_name == $current_tab) ? "class='tabSelected'" : "class='tabDefault'") . " href='" . $config['url_path'] .
						"plugins/impb/impb_view_devices.php?" .
						"report=devices&dtab=" . $tab_short_name .
						"'>$tabs_dimpb[$tab_short_name]</a></div>\n";
				}
			}
			}
			print "</div>\n";
		}else{
			/* draw the tabs */
			print "<table class='dtab' width='98%' cellspacing='0' cellpadding='3' align='center'><tr>\n";

			if (sizeof($tabs_dimpb)) {
			foreach ($tabs_dimpb as $tab) {
				$tab_short_name=$tab['tab_name'] ;
				print "<td style='padding:3px 10px 2px 5px;background-color:" . (($tab['tab_id'] == $current_tab) ? "silver;" : "#DFDFDF;") .
					"white-space:nowrap;'" .
					" nowrap width='1%'" .
					"' align='center' class='tab'>
					<span class='textHeader'><a href='" . $config['url_path'] .
					"plugins/impb/impb_view_devices.php?" .
					"report=devices&dtab=" . $tab['tab_id'] .
					"'>$tab_short_name</a></span>
				</td>\n
				<td width='1'></td>\n";
			}
			}
			print "<td></td><td></td>\n</tr></table>\n";
		}		
	
	
	}

	

}

function dimpb_format_port_status($port) {
 	
	switch ($port["port_status"]) {
	 case "1":
		$port_status = 'UP';
		$port_imp_active_color = '#00BD27';
		if ($port["type_revision"] == "1") {  //A1, B1
			switch ($port["port_speed"]) {
				 case "0":
					 break; 				
				 case "1":
					 $port_status = $port_status . ", Auto";
					 break;
				 case "2":
					 $port_status = $port_status . ", 10H";
					 break;                        
				 case "3":
					 $port_status = $port_status . ", 10F";
					 break;
				 case "4":
					 $port_status = $port_status . ", 100H";
					 break;
				 case "5":
					 $port_status = $port_status . ", 100F";
					 break;
				 case "7":
					 $port_status = $port_status . ", 1G";
					 break;   						 
				 default:
					 $port_status = $port_status . "," . $port["port_speed"];
					 break;
			};
		}else{  //C1  INTEGER  { other ( 1 ) , nway-enabled ( 2 ) , nway-disabled-10Mbps-Half ( 3 ) , nway-disabled-10Mbps-Full ( 4 ) , nway-disabled-100Mbps-Half ( 5 ) , nway-disabled-100Mbps-Full ( 6 ) , nway-disabled-1Gigabps-Half ( 7 ) , nway-disabled-1Gigabps-Full ( 8 ) , nway-disabled-1Gigabps-Full-master ( 9 ) , nway-disabled-1Gigabps-Full-slave ( 10 ) } 
			switch ($port["port_speed"]) {
				 case "0":
					 break; 				
				 case "1":
					 $port_status = $port_status . ", other";
					 break;
				 case "2":
					 $port_status = $port_status . ", enabled";
					 break;                        
				 case "3":
					 $port_status = $port_status . ", 10H";
					 break;
				 case "4":
					 $port_status = $port_status . ", 10F";
					 break;
				 case "5":
					 $port_status = $port_status . ", 100H";
					 break;
				 case "6":
					 $port_status = $port_status . ", 100F";
					 break;
				 case "7":
					 $port_status = $port_status . ", 1Gh";
					 break;
				 case "8":
					 $port_status = $port_status . ", 1Gf";
					 break; 
				 case "9":
					 $port_status = $port_status . ", 1Gf";
					 break;
				 case "10":
					 $port_status = $port_status . ", 1Gf";
					 break; 					 
				 default:
					 $port_status = $port_status . "," . $port["port_speed"];
					 break;
			};						
		};
		break;
	 case "0":
		 $port_status = 'DOWN';
		 $port_imp_active_color = '#FF0000';
		 break;                        
	 default:
		 $port_status = $port["port_status"];
		 break;
	};
return $port_status;
} 

function api_find_comboports_ids($device, $port_number){
	$ret_ids=0;
	
    if ((isset($device["snmp_oid_swL2PortCtrlAdminState"])) && (trim($device["snmp_oid_swL2PortCtrlAdminState"]) <> "")){
		$ifAdminStates = impb_standard_indexed_data_oid($device["snmp_oid_swL2PortCtrlAdminState"], $device);
		if (is_array($ifAdminStates) and (sizeof($ifAdminStates) > 0)){
			$ret_ids = array();
			foreach ($ifAdminStates as $item => $key) {
				if ((strpos ($item,".")) > 0 ){
					$ind = substr ($item, 0, strpos ($item,"."));
					if ($ind == $port_number) {
						array_push($ret_ids, substr ($item, strpos ($item,".") + 1));
					}
				}

				
			}
		
		}
 	} 
	return $ret_ids;
	
}

function api_imp_change_port_state($port_row, $device, $new_state){
 	//сначала нужно определить все индексы комбо-портов.
	$ids = api_find_comboports_ids($device, $port_row["port_number"]);
	if ($new_state == "3"){
		$str_state = "Включено";
	}else{
		$str_state = "Отключено";
	};
	if (is_array($ids) ){
		if ((sizeof($ids) > 0)){
			foreach ($ids as $item => $key) {
				$ar_step1 = imb_set_and_check($device, $device["snmp_oid_swL2PortCtrlAdminState"] . "." . $port_row["port_number"] . "." . $key, "i", $new_state, "port_state", "Изменение состояния порта № " . $port_row["port_number"] . "." . $key . "  на [" . $str_state . "]", true);
				if ($ar_step1["rezult_final"] == "OK") {
						db_execute("UPDATE `imb_ports` set port_adm_state = '" . $new_state . "' where port_id=" . $port_row["port_id"] );
						increment_unsaved_count($device["device_id"], '1');
				}		
			}
			
		}else{ // один порт индекс (3526)
			$ar_step1 = imb_set_and_check($device, $device["snmp_oid_swL2PortCtrlAdminState"] . "." . $port_row["port_number"] , "i", $new_state, "port_state", "Изменение состояния порта № " . $port_row["port_number"] . "  на [" . $str_state . "]", true);
			if ($ar_step1["rezult_final"] == "OK") {
					db_execute("UPDATE `imb_ports` set port_adm_state = '" . $new_state . "' where port_id=" . $port_row["port_id"] );
					increment_unsaved_count($device["device_id"], '1');
			}			
		}
	}


 return $ar_step1["rezult_final"];
}


function impb_tabs() {
	global $config;

	/* present a tabbed interface */
	$tabs_impb = array(
		//'sites'    	=> __('Sites'),
		'devices'    	=> __('Devices'),
		'bindings'    	=> __('Bindings'),
		'blmacs'     	=> __('Blocks'),
		//'banips'     	=> __('Bans'),
		'ports'      	=> __('Ports'),
		'netdel'    	=> __('Auto Deleting'),
		'netadd'    	=> __('Auto Create'),
		'recentmacs' 	=> __('Scans'),
		'info'     		=> __('Search')
	);

	/* set the default tab */
	$current_tab = get_request_var('report');

	/* draw the tabs */
	print "<div class='tabs'><nav><ul>\n";

	if (sizeof($tabs_impb)) {
		foreach ($tabs_impb as $tab_short_name => $tab_name) {
			print '<li><a class="tab' . (($tab_short_name == $current_tab) ? ' selected"' : '"') . " href='" . htmlspecialchars($config['url_path'] .
				'plugins/impb/impb_view_' . $tab_short_name . '.php?' .
				//'plugins/impb/impb_view.php?' .
				'report=' . $tab_short_name) .
				"'>$tab_name</a></li>\n";
		}
	}
	print "<td width='100%' valign='top'>" . imp_display_output_messages();
	print "</ul></nav></div>\n";
}

function impb_format_snmp_string($string, $snmp_oid_included) {
	global $banned_snmp_strings;

	$string = preg_replace(REGEXP_SNMP_TRIM, "", trim($string));

	if (substr($string, 0, 7) == "No Such") {
		return "";
	}

	if ($snmp_oid_included) {
		/* returned SNMP string e.g. 
		 * .1.3.6.1.2.1.31.1.1.1.18.1 = STRING: === bla ===
		 * strip off all leading junk (the oid and stuff) */
		$string_array = explode("=", $string, 2);
		if (sizeof($string_array) == 1) {
			/* trim excess first */
			$string = trim($string);
		}else if ((substr($string, 0, 1) == ".") || (strpos($string, "::") !== false)) {
			/* drop the OID from the array */
			array_shift($string_array);
			$string = trim(implode("=", $string_array));
		}else {
			$string = trim(implode("=", $string_array));
		}
	}

	/* return the easiest value */
	if ($string == "") {
		return $string;
	}

	/* now check for the second most obvious */
	if (is_numeric($string)) {
		return trim($string);
	}

	/* remove ALL quotes, and other special delimiters */
	$string = str_replace("\"", "", $string);
	$string = str_replace("'", "", $string);
	$string = str_replace(">", "", $string);
	$string = str_replace("<", "", $string);
	$string = str_replace("\\", "", $string);
	$string = str_replace("\n", " ", $string);
	$string = str_replace("\r", " ", $string);

	/* account for invalid MIB files */
	if (substr_count($string, "Wrong Type")) {
		$string = strrev($string);
		if ($position = strpos($string, ":")) {
			$string = trim(strrev(substr($string, 0, $position)));
		}else{
			$string = trim(strrev($string));
		}
	}

	/* Remove invalid chars */
	$k = strlen($string);
	for ($i=0; $i < $k; $i++) {
		if ((ord($string[$i]) <= 31) || (ord($string[$i]) >= 127)) {
			$string[$i] = " ";
		}
	}
	$string = trim($string);

	if ((substr_count($string, "Hex-STRING:")) ||
		(substr_count($string, "Hex-")) ||
		(substr_count($string, "Hex:"))) {
		/* strip of the 'Hex-STRING:' */
		$string = preg_replace("/Hex-STRING: ?/i", "", $string);
		$string = preg_replace("/Hex: ?/i", "", $string);
		$string = preg_replace("/Hex- ?/i", "", $string);

		$string_array = explode(" ", $string);

		/* loop through each string character and make ascii */
		$string = "";
		$hexval = "";
		$ishex  = false;
		for ($i=0;($i<sizeof($string_array));$i++) {
			if (strlen($string_array[$i])) {
				$string .= chr(hexdec($string_array[$i]));

				$hexval .= str_pad($string_array[$i], 2, "0", STR_PAD_LEFT);

				if (($i+1) < count($string_array)) {
					$hexval .= ":";
				}

				if ((hexdec($string_array[$i]) <= 31) || (hexdec($string_array[$i]) >= 127)) {
					if ((($i+1) == sizeof($string_array)) && ($string_array[$i] == 0)) {
						/* do nothing */
					}else{
						$ishex = true;
					}
				}
			}
		}

		if ($ishex) $string = $hexval;
	}elseif (preg_match("/(hex:\?)?([a-fA-F0-9]{1,2}(:|\s)){5}/i", $string)) {
		$octet = "";

		/* strip off the 'hex:' */
		$string = preg_replace("/hex: ?/i", "", $string);

		/* split the hex on the delimiter */
		$octets = preg_split("/\s|:/", $string);

		/* loop through each octet and format it accordingly */
		for ($i=0;($i<count($octets));$i++) {
			$octet .= str_pad($octets[$i], 2, "0", STR_PAD_LEFT);

			if (($i+1) < count($octets)) {
				$octet .= ":";
			}
		}

		/* copy the final result and make it upper case */
		$string = strtoupper($octet);
	}elseif (preg_match("/Timeticks:\s\((\d+)\)\s/", $string, $matches)) {
		$string = $matches[1];
	}

	foreach($banned_snmp_strings as $item) {
		if(strstr($string, $item) != "") {
			$string = "";
			break;
		}
	}

	return $string;
}

function impb_redirect() {
	/* set the default tab */
    get_filter_request_var('report', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^([a-zA-Z]+)$/')));

	load_current_session_value('report', 'sess_impb_view_report', 'devices');
	$current_tab = get_nfilter_request_var('report');

	$current_page = str_replace('impb_', '', str_replace('view_', '', str_replace('.php', '', basename($_SERVER['PHP_SELF']))));
	$current_dir  = dirname($_SERVER['PHP_SELF']);

	if ($current_page != $current_tab) {
		header('Location: ' . $current_dir . '/impb_view_' . $current_tab . '.php');
	}
}

 //convert HEX to STR
function member_ports($string)
{
	$hex = array('Hex-STRING: ',' ', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
	$bin = array('', '', '0000', '0001', '0010', '0011', '0100', '0101', '0110', '0111', '1000', '1001', '1010', '1011', '1100', '1101', '1110', '1111');
	return str_replace($hex, $bin, $string);
} 

//invert STR "0010" to "1101"
function impb_invert_string($in_str){
	
	 $in_arr = str_split($in_str);
	 
	 //$arr_port = array_splice($arr_port,27);
	 $arr_rezult=array();
	 foreach ($in_arr as $key => $v) {
		if ($v == 1){
			array_push($arr_rezult, "0");
		}else{
			array_push($arr_rezult, "1");
		}
	}	
	return  implode("", $arr_rezult);
}

/* ipmb_form_unselectable_cell - format's a table row such that it CANN`T be highlighted using cacti's js actions
   @arg $contents - the readable portion of the
   @arg $id - the id of the object that will be highlighted
   @arg $width - the width of the table element
   @arg $style - the style to apply to the table element */
function ipmb_form_unselectable_cell($contents, $id, $width="", $style="") {
	print "\t<td" . (strlen($width) ? " width='$width'" : "") . (strlen($style) ? " style='$style;'" : "") . " >" . $contents . "</td>\n";
}

function impb_recent_data() {
	cacti_log("NOTE: MACTRACK ==> IMPB Data transfer started\n");
    db_execute("UPDATE imb_mactrack_recent_ports SET active_last=0;");  
    db_execute("TRUNCATE table imb_mactrack_temp_ports;");
	db_execute("INSERT INTO imb_mactrack_temp_ports " .
					    " (site_id, device_id, hostname, device_name,  " .
					    " vlan_id, vlan_name, mac_address, vendor_mac, ip_address, " .
					    " dns_hostname, port_number, port_name, scan_date, updated, authorized) " .
					    " SELECT site_id, device_id, hostname, device_name,  " .
					    " vlan_id, vlan_name, mac_address, vendor_mac, ip_address, " .
					    " dns_hostname, port_number, port_name, scan_date, updated, authorized " .
					    " FROM mac_track_temp_ports; ");
	db_execute("UPDATE imb_mactrack_temp_ports,imb_devices " .
		" SET imb_mactrack_temp_ports.imb_device_id=imb_devices.device_id " .
		" WHERE imb_mactrack_temp_ports.hostname=imb_devices.hostname;");

	db_execute("INSERT INTO imb_mactrack_recent_ports " .
					    " (site_id, device_id,imb_device_id, hostname, dns_hostname, description, " .
					    " vlan_id, vlan_name, mac_address, vendor_mac, vendor_name, ip_address, " .
					    " port_number, port_name, date_last, first_scan_date, count_rec, active_last, authorized) " .
					    " SELECT site_id, device_id, imb_device_id, hostname, dns_hostname, device_name, " .
					    " vlan_id, vlan_name, mac_address, imb_mactrack_temp_ports.vendor_mac, mac_track_oui_database.vendor_name , ip_address, " .
					    " port_number, port_name, scan_date, min(scan_date), 1,1, authorized " .
					    " FROM imb_mactrack_temp_ports " .
						"  LEFT JOIN mac_track_oui_database ON (mac_track_oui_database.vendor_mac = imb_mactrack_temp_ports.vendor_mac) " .
            " GROUP BY site_id, device_id, mac_address, port_number, ip_address, vlan_id " .
			" ON DUPLICATE KEY UPDATE " .
			" count_rec=count_rec+1, " .
			" imb_device_id=VALUES(imb_device_id), " .
			" active_last=1, " .
			" vendor_name=VALUES(vendor_name), " .
			" active_last=1;");


			
    //db_execute("UPDATE imb_mactrack_recent_ports,mac_track_temp_ports SET imb_mactrack_recent_ports.port_name=mac_track_temp_ports.port_name " .
     //          " WHERE imb_mactrack_recent_ports.site_id=mac_track_temp_ports.site_id and imb_mactrack_recent_ports.device_id=mac_track_temp_ports.device_id and imb_mactrack_recent_ports.port_number=mac_track_temp_ports.port_number;");  
		   
	db_execute("TRUNCATE table imb_temp_portname;");
	db_execute("insert into imb_temp_portname (select site_id, device_id,port_number,port_name " .
		" from  mac_track_temp_ports " .
		" group by site_id, device_id,port_number,port_name); ");

	db_execute("UPDATE imb_mactrack_recent_ports,imb_temp_portname " .
		" SET imb_mactrack_recent_ports.port_name=imb_temp_portname.port_name " .
		" WHERE imb_mactrack_recent_ports.site_id=imb_temp_portname.site_id " .
		" and imb_mactrack_recent_ports.device_id=imb_temp_portname.device_id " .
		" and imb_mactrack_recent_ports.port_number=imb_temp_portname.port_number;");



	$result = db_fetch_assoc("show tables  like 'plugin_cimpb_mactrack_%';");
	//change to 1 for real work - disabled now for big poller runtime
	if (count($result) > 100) {
		//use cimpb plugin too
		
    db_execute("UPDATE plugin_cimpb_mactrack_recent_ports SET active_last=0;");  
    db_execute("TRUNCATE table plugin_cimpb_mactrack_temp_ports;");
	//во временную таблицу переносим данные с последнего опроса плагина mactrack
  	db_execute("INSERT INTO plugin_cimpb_mactrack_temp_ports " .
  					    " (site_id, device_id, hostname, device_name,  " .
  					    " vlan_id, vlan_name, mac_address, vendor_mac, ip_address, " .
  					    " dns_hostname, port_number, port_name, scan_date, updated, authorized) " .
  					    " SELECT site_id, device_id, hostname, device_name,  " .
  					    " vlan_id, vlan_name, mac_address, vendor_mac, ip_address, " .
  					    " dns_hostname, port_number, port_name, scan_date, updated, authorized " .
  					    " FROM mac_track_temp_ports; ");
	//теперь на основании совпадения полей hostname (ип-адрес) определяем ИД устройства в plugin_cimpb_mactrack_temp_ports
  	db_execute("UPDATE plugin_cimpb_mactrack_temp_ports,host " .
  		" SET plugin_cimpb_mactrack_temp_ports.imb_device_id=host.id " .
  		" WHERE plugin_cimpb_mactrack_temp_ports.hostname=host.hostname;");		
		
	
	//теперь данные переносим в таблицу recent_ports и добавляем информацию о вендоре по полю vendor_mac + устнавливаем ключ активности в данный момент + увеличиваем счетчик сканирований
	db_execute("INSERT INTO plugin_cimpb_mactrack_recent_ports " .
				" (site_id, device_id,imb_device_id, hostname, dns_hostname, description, " .
				" vlan_id, vlan_name, mac_address, vendor_mac, vendor_name, ip_address, " .
				" port_number, port_name, date_last, first_scan_date, count_rec, authorized) " .
				" SELECT site_id, device_id, imb_device_id, hostname, dns_hostname, device_name, " .
				" vlan_id, vlan_name, mac_address, plugin_cimpb_mactrack_temp_ports.vendor_mac, mac_track_oui_database.vendor_name , ip_address, " .
				" port_number, port_name, scan_date, min(scan_date), 1, authorized " .
				" FROM plugin_cimpb_mactrack_temp_ports " .
				"  LEFT JOIN mac_track_oui_database ON (mac_track_oui_database.vendor_mac = plugin_cimpb_mactrack_temp_ports.vendor_mac) " .
	" GROUP BY site_id, device_id, mac_address, port_number, ip_address, vlan_id " .
	" ON DUPLICATE KEY UPDATE " .
	" count_rec=count_rec+1, " .
	" imb_device_id=VALUES(imb_device_id), " .
	" active_last=1, " .
	" vendor_name=VALUES(vendor_name), " .
	" active_last=1;");

	//теперь нужно обновить информацию об именовании портов. Даже если потр не учавствовал в последнем опросе.
  	db_execute("TRUNCATE table plugin_cimpb_temp_portname;");
	//во временную таблицу пишем информация и именах портов
  	db_execute("insert into plugin_cimpb_temp_portname (select site_id, device_id,port_number,port_name " .
  		" from  plugin_cimpb_mactrack_temp_ports " .
  		" group by site_id, device_id,port_number,port_name); ");
	//теперь обновляем сами имена
  	db_execute("UPDATE plugin_cimpb_mactrack_recent_ports,plugin_cimpb_temp_portname " .
  		" SET plugin_cimpb_mactrack_recent_ports.port_name=plugin_cimpb_temp_portname.port_name " .
  		" WHERE plugin_cimpb_mactrack_recent_ports.site_id=plugin_cimpb_temp_portname.site_id " .
  		" and plugin_cimpb_mactrack_recent_ports.device_id=plugin_cimpb_temp_portname.device_id " .
  		" and plugin_cimpb_mactrack_recent_ports.port_number=plugin_cimpb_temp_portname.port_number;");		
		
		
  	}
		
	cacti_log("NOTE: MACTRACK ==> IMPB Data transfer ended\n");		
}


?>
