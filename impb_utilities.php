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
<<<<<<< HEAD
chdir('../../');
include('./include/auth.php');

/* set default action */
set_default_action();

/* add more memory for import */
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '300');

switch (get_request_var('action')) {
	case 'impb_utilities_purge_scanning_funcs':
		
		impb_utilities();
		impb_utilities_purge_scanning_funcs();

		break;
	case 'impb_view_proc_status':
		/* ================= input validation ================= */
		get_filter_request_var('refresh');
		/* ==================================================== */

		load_current_session_value('refresh', 'sess_impb_utilities_refresh', '30');

		$refresh['seconds'] = get_request_var('refresh');
		$refresh['page'] = 'impb_utilities.php?action=impb_view_proc_status';

		top_header();

		impb_display_run_status();

		bottom_footer();
		break;
	default:
		top_header();

		impb_utilities();

		bottom_footer();
		break;
}

=======
 chdir('../../');
 include("./include/auth.php");
 
 /* set default action */
 if (!isset($_REQUEST["action"])) { $_REQUEST["action"] = ""; }
 
 switch ($_REQUEST["action"]) {
 	case 'impblinding_utilities_purge_scanning_funcs':
 		include_once($config['base_path'] . "/include/top_header.php");
 		include_once($config['base_path'] . "/plugins/impblinding/lib/impblinding_functions.php");
 
 		impblinding_utilities();
 		impblinding_utilities_purge_scanning_funcs();
 
 		include_once($config['base_path'] . "/include/bottom_footer.php");
 		break;
 	case 'impblinding_view_proc_status':
 		/* ================= input validation ================= */
 		input_validate_input_number(get_request_var_request("refresh"));
 		/* ==================================================== */
 
 		load_current_session_value("refresh", "sess_impb_utilities_refresh", "10");
 
 		$refresh["seconds"] = $_REQUEST["refresh"];
 		$refresh["page"] = "impblinding_utilities.php?action=impblinding_view_proc_status";
 
 		include_once($config['base_path'] . "/include/top_header.php");
 
 		impblinding_display_run_status();
 
 		include_once($config['base_path'] . "/include/bottom_footer.php");
 		break;		
 	default:
 		include_once($config['base_path'] . "/include/top_header.php");
 
 		impblinding_utilities();
 
 		include_once($config['base_path'] . "/include/bottom_footer.php");
 		break;
 }
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 /* -----------------------
     Utilities Functions
    ----------------------- */
 
<<<<<<< HEAD
 function impb_utilities_purge_scanning_funcs() {
 	global $config;
 
 	db_execute("TRUNCATE TABLE imb_scanning_functions");
 	include_once($config["base_path"] . "/plugins/impblinding/lib/impb_functions.php");
 	include_once($config["base_path"] . "/plugins/impblinding/lib/impb_vendors.php");
 
 	/* store the list of registered impblinding scanning functions */
 	foreach($impb_scanning_functions as $scanning_function) {
 		db_execute("REPLACE INTO imb_scanning_functions (scanning_function) VALUES ('" . $scanning_function . "')");
 	}

	html_start_box('IP-MAC-Port Blinding Scanning Function Refresh Results', '100%', '', '3', 'center', '');
	?>
	<td>
		The IP-MAC-Port Blinding functions have been recreated.
	</td>
	<?php
	html_end_box();

}
 
 
 
 function impb_display_run_status() {
 	global $config, $refresh_interval;
=======
 function impblinding_utilities_purge_scanning_funcs() {
 	global $config, $colors;
 
 	db_execute("TRUNCATE TABLE imb_scanning_functions");
 	include_once($config["base_path"] . "/plugins/impblinding/lib/impblinding_functions.php");
 	include_once($config["base_path"] . "/plugins/impblinding/lib/impblinding_vendors.php");
 
 	/* store the list of registered impblinding scanning functions */
 	foreach($impblinding_scanning_functions as $scanning_function) {
 		db_execute("REPLACE INTO imb_scanning_functions (scanning_function) VALUES ('" . $scanning_function . "')");
 	}
 
 	html_start_box("<strong>D-Link IP-MAC-Port Blinding Scanning Function Refresh Results</strong>", "98%", $colors["header"], "3", "center", "");
 	?>
 	<td>
 		The D-Link IP-MAC-Port Blinding scanning functions have been purged.  They will be recreated once you either edit a device or device type.
 	</td>
 	<?php
 	html_end_box();
 }
 
 
 
 function impblinding_display_run_status() {
 	global $colors, $config, $refresh_interval;
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 		//$seconds_offset = $seconds_offset * 60;
 		/* find out if it's time to collect device information */
 		$base_start_time = read_config_option("mt_base_time", TRUE);
 		$database_maint_time = read_config_option("mt_maint_time", TRUE);
 		$last_run_time = read_config_option("mt_last_run_time", TRUE);
 		$last_db_maint_time = read_config_option("mt_last_db_maint_time", TRUE);
 		$previous_base_start_time = read_config_option("mt_prev_base_time", TRUE);
 		$previous_db_maint_time = read_config_option("mt_prev_db_maint_time", TRUE);
 
 		/* see if the user desires a new start time */
 		if (!empty($previous_base_start_time)) {
 			if ($base_start_time <> $previous_base_start_time) {
 				unset($last_run_time);
 			}
 		}
 
 		/* see if the user desires a new db maintenance time */
 		if (!empty($previous_db_maint_time)) {
 			if ($database_maint_time <> $previous_db_maint_time) {
 				unset($last_db_maint_time);
 			}
 		}
 
 		/* determine the next start time */
 		$current_time = strtotime("now");
 		if (empty($last_run_time)) {
 			$collection_never_completed = TRUE;
 			if ($current_time > strtotime($base_start_time)) {
 				/* if timer expired within a polling interval, then poll */
 				if (($current_time - 300) < strtotime($base_start_time)) {
 					$next_run_time = strtotime(date("Y-m-d") . " " . $base_start_time);
 				}else{
 					$next_run_time = strtotime(date("Y-m-d") . " " . $base_start_time) + 3600*24;
 				}
 			}else{
 				$next_run_time = strtotime(date("Y-m-d") . " " . $base_start_time);
 			}
 		}else{
 			$collection_never_completed = FALSE;
 			//$next_run_time = $last_run_time + $seconds_offset;
 		}
 
 		if (empty($last_db_maint_time)) {
 			if (strtotime($base_start_time) < $current_time) {
 				$next_db_maint_time = strtotime(date("Y-m-d") . " " . $database_maint_time) + 3600*24;
 			}else{
 				$next_db_maint_time = strtotime(date("Y-m-d") . " " . $database_maint_time);
 			}
 		}else{
 			$next_db_maint_time = $last_db_maint_time + 24*3600;
 		}
 
 		//$time_till_next_run = $next_run_time - $current_time;
 		$time_till_next_db_maint = $next_db_maint_time - $current_time;
 
<<<<<<< HEAD
 	html_start_box(__('IP-MAC-PORT Binding Process Status'), '100%', '', '1', 'center', '');
 	?>
 	<script type="text/javascript">
 	<!--
	function applyFilter() {
		strURL = 'impb_utilities.php?action=impb_view_proc_status&refresh='+$('#refresh').val();
		loadPageNoHeader(strURL);
	}

	$(function() {
		$('#refresh').click(function() {
			applyFilter();
		});
	});	
 	-->
 	</script>
	<tr class='even'>
		<form name='form_impb_utilities_stats' method='post'>	
 		<td>
 			<table class='filterTable'>
 				<tr>
					<td>
						<?php print __('Refresh');?>
					</td>
 					<td>
 						<select id='refresh' onChange='applyFilter()'>
 						<?php
 						foreach ($refresh_interval as $key => $interval) {
 							print '<option value="' . $key . '"'; if (get_request_var('refresh') == $key) { print " selected"; } print ">" . $interval . "</option>";
=======
 	html_start_box("<strong>IP-MAC-PORT Binding Process Status</strong>", "98%", $colors["header"], "1", "center", "");
 	?>
 	<script type="text/javascript">
 	<!--
 	function applyStatsRefresh(objForm) {
 		strURL = '?action=impblinding_view_proc_status&refresh=' + objForm.refresh[objForm.refresh.selectedIndex].value;
 		document.location = strURL;
 	}
 	-->
 	</script>
 	<tr bgcolor="<?php print $colors["panel"];?>">
 		<form name="form_impblinding_utilities_stats" method="post">
 		<td>
 			<table cellpadding="1" cellspacing="0">
 				<tr>
 					<td width="100">
 						&nbsp;Refresh Interval:
 					</td>
 					<td width="1">
 						<select name="refresh" onChange="applyStatsRefresh(document.form_impblinding_utilities_stats)">
 						<?php
 						foreach ($refresh_interval as $key => $interval) {
 							print '<option value="' . $key . '"'; if ($_REQUEST["refresh"] == $key) { print " selected"; } print ">" . $interval . "</option>";
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 						}
 						?>
 					</td>
 					<td>
<<<<<<< HEAD
 						<input type='button' value='<?php print __('Refresh');?>' id='refresh'>
=======
 						&nbsp;<input type="image" src="<?php print $config['url_path']; ?>images/button_refresh.gif" alt="Refresh" border="0" align="absmiddle">&nbsp;
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 					</td>
 				</tr>
 			</table>
 		</td>
 		</form>
 	</tr>
 	<?php
<<<<<<< HEAD

	html_end_box(TRUE);

	html_start_box('', '100%', '', '1', 'center', '');	
=======
 	html_end_box(TRUE);
 	html_start_box("", "98%", $colors["header"], "1", "center", "");
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 	/* get information on running processes */
 	$running_processes = db_fetch_assoc("SELECT
 		imb_processes.process_id,
 		imb_devices.description as device_name,
 		imb_processes.device_id,
 		imb_processes.start_date
 		FROM imb_devices
 		INNER JOIN imb_processes ON (imb_devices.device_id = imb_processes.device_id)
 		WHERE imb_processes.device_id != '0'");
 
 	$resolver_running = db_fetch_cell("SELECT COUNT(*) FROM mac_track_processes WHERE device_id='0'");
 	$total_processes = sizeof($running_processes);
 
 	$run_status = db_fetch_assoc("SELECT last_rundate,
 		COUNT(last_rundate) AS devices
 		FROM imb_devices
 		WHERE disabled = ''
 		GROUP BY last_rundate
 		ORDER BY last_rundate DESC;");
 
 	$total_devices = db_fetch_cell("SELECT count(*) FROM imb_devices");
 
 	$disabled_devices = db_fetch_cell("SELECT count(*) FROM imb_devices");
 
<<<<<<< HEAD
	html_header(array(__('Current Process Status')), 2);
	form_alternate_row();
 	print '<td>' . __('The IP-MAC-Port Blinding Poller is:') . '</td><td>' . ($total_processes > 0 ? __('RUNNING') : __('IDLE')) . '</td>';

 	if ($total_processes > 0) {
 		form_alternate_row();
		print '<td>' . __('Running Processes:') . '</td><td>' . $total_processes . '</td>';
 	}
 	form_alternate_row();
	print '<td width=200>' . __('Last Time Poller Started:') . '</td><td>' . read_config_option('dimpb_scan_date', TRUE) . '</td>';

	html_header(array(__('Run Time Details')), 2);
 	form_alternate_row();
	print '<td width=200>' . __('Last Poller Runtime:') . '</td><td>' . read_config_option('dimpb_stats_general', TRUE) . '</td>';
 	form_alternate_row();
	print '<td width=200>' . __('Last Poller Stat:') . '</td><td>' . read_config_option('dimpb_stats', TRUE) . '</td>';
 	form_alternate_row();
	print '<td width=200>' . __('Maximum Concurrent Processes:') . '</td><td>' . read_config_option('dimpb_processes', TRUE) . '</td>';
 	form_alternate_row();
 	print '<td width=200>' . __('Maximum Per Device Scan Time:') . '</td><td>' . read_config_option('dimpb_script_runtime', TRUE) . ' minutes</td>';


 	if ($total_processes > 0) {
		html_start_box(__('Running Process Summary'), '100%', '', '3', 'center', '');
 
		html_header(array(__('Status'), __('Devices'), __('Date Started')), 3);
=======
 	html_header(array("Current Process Status"), 2);
 	$i = 0;
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td><strong>The IP-MAC-Port Blinding Poller is:</td><td>" . ($total_processes > 0 ? "RUNNING" :  "IDLE") . "</strong></td>";
 	if ($total_processes > 0) {
 		form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 		print "<td><strong>Running Processes:</strong></td><td>" . $total_processes . "</td>";
 	}
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>Last Time Poller Started:</strong></td><td>" . read_config_option("dimpb_scan_date", TRUE) . "</td>";
 
 	html_header(array("Run Time Details"), 2);
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>Last Poller Runtime:</strong></td><td>" . read_config_option("dimpb_stats_general", TRUE) . "</td>";
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>Last Poller Stat:</strong></td><td>" . read_config_option("dimpb_stats", TRUE) . "</td>";
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>Maximum Concurrent Processes:</strong></td><td>" . read_config_option("dimpb_processes", TRUE) . " processes</td>";
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>Maximum Per Device Scan Time:</strong></td><td>" . read_config_option("dimpb_script_runtime", TRUE) . " minutes</td>";
 
 	html_header(array("DNS Configuration Information"), 2);
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>Reverse DNS Resolution is</strong></td><td>" . (read_config_option("mt_reverse_dns", TRUE) == "on" ? "ENABLED" : "DISABLED") . "</td>";
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>Primary DNS Server:</strong></td><td>" . read_config_option("mt_dns_primary", TRUE) . "</td>";
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>Secondary DNS Server:</strong></td><td>" . read_config_option("mt_dns_secondary", TRUE) . "</td>";
 	form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
 	print "<td width=200><strong>DNS Resoution Timeout:</strong></td><td>" . read_config_option("mt_dns_timeout", TRUE) . " milliseconds</td>";
 	html_end_box(TRUE);
 
 	if ($total_processes > 0) {
 		html_start_box("<strong>Running Process Summary</strong>", "98%", $colors["header"], "3", "center", "");
 
 		html_header(array("Status", "Devices", "Date Started"), 3);
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 
 		$other_processes = 0;
 		$other_date = 0;
 		if (sizeof($run_status) == 1) {
 			$waiting_processes = $total_devices - $total_processes;
 			$waiting_date = $run_status[0]["last_rundate"];
 			$completed_processes = 0;
 			$completed_date = "";
 			$running_processes = $total_processes;
 			$running_date = read_config_option("dimpb_scan_date", TRUE);
 		}else{
 			$i = 0;
 			foreach($run_status as $key => $run) {
 			switch ($key) {
 			case 0:
 				$completed_processes = $run["devices"];
 				$completed_date = $run["last_rundate"];
 				break;
 			case 1:
 				$waiting_processes = $run["devices"] - $total_processes;
 				$waiting_date = $run["last_rundate"];
 				$running_processes = $total_processes;
 				$running_date = read_config_option("dimpb_scan_date", TRUE);
 				break;
 			default;
 				$other_processes += $run["devices"];
 				$other_rundate = $run["last_rundate"];
 			}
 			}
 		}
 
 		$i = 0;
<<<<<<< HEAD
 		form_alternate_row();
=======
 		form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 		?>
 		<td><?php print "Completed";?></td>
 		<td><?php print $completed_processes;?></td>
 		<td><?php print $completed_date;?></td>
 		<?php
<<<<<<< HEAD
 		form_alternate_row();
=======
 		form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 		?>
 		<td><?php print "Running";?></td>
 		<td><?php print $running_processes;?></td>
 		<td><?php print $running_date;?></td>
 		<?php
<<<<<<< HEAD
 		form_alternate_row();
=======
 		form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 		?>
 		<td><?php print "Waiting";?></td>
 		<td><?php print $waiting_processes;?></td>
 		<td><?php print $waiting_date;?></td>
 		<?php
<<<<<<< HEAD
 		form_alternate_row();
=======
 		form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 		if ($other_processes > 0) {
 			?>
 			<td><?php print "Other";?></td>
 			<td><?php print $other_processes;?></td>
 			<td><?php print $other_date;?></td>
 			<?php
 		}
 
 		html_end_box(TRUE);
 	}
 
 }
 
 
 
<<<<<<< HEAD
function impb_utilities() {
	
	html_start_box('Cacti IP-MAC-Port System Utilities', '100%', '', '3', 'center', '');

	html_header(array('Process Status Information'), 2);
 
 
 	?>
 	<colgroup span="3">
		<col class="nowrap" style="vertical-align:top;width:20%;">
		<col style="vertical-align:top;width:80%;">
	</colgroup>
	<tr class='even'>
		<td class='textArea'>
			<a class='hyperLink' href='impb_utilities.php?action=impb_view_proc_status'>View IP-MAC-Port Binding Process Status</a>
		</td>
		<td class='textArea'>
			This option will let you show and set process information associated with the IP-MAC-Port Binding polling process.
		</td>
	</tr>
 
 	<?php html_header(array("Database Administration"), 2);?>

	<tr class='odd'>
		<td class='textArea'>
			<a class='hyperLink' href='impb_utilities.php?action=impb_utilities_purge_scanning_funcs'>Refresh Scanning Functions</a>
		</td>
		<td class='textArea'>
			Deletes old and potentially stale IP-Mac-Port Binding scanning functions from the drop-down
				you receive when you edit a device type.
		</td>
	</tr>

=======
 function impblinding_utilities() {
 	global $colors;
 
 	html_start_box("<strong>D-Link IP-MAC-Port Blinding System Utilities</strong>", "98%", $colors["header"], "3", "center", "");
 
 	html_header(array("Process Status Information"), 2);
 
 	?>
 
 	<tr bgcolor="#<?php print $colors["form_alternate1"];?>">
 		<td class="textArea" width="320" valign="top">
 			<p><a href='impblinding_utilities.php?action=impblinding_view_proc_status'>View IP-MAC-Port Blinding Process Status</a></p>
 		</td>
 		<td class="textArea" valign="top">
 			<p>This option will let you show and set process information associated with the IP-MAC-Port Blinding polling process.</p>
 		</td>
 	</tr>
 
 
 	<?php html_header(array("Database Administration"), 2);?>
 
 	<tr bgcolor="#<?php print $colors["form_alternate2"];?>">
 		<td class="textArea" width="320" valign="top">
 			<p><a href='impblinding_utilities.php?action=impblinding_utilities_purge_scanning_funcs'>Refresh Scanning Functions</a></p>
 		</td>
 		<td class="textArea" valign="top">
 			<p>Deletes old and potentially stale Ip-Mac-Port Blinding scanning functions from the drop-down
 				you receive when you edit a device type.</p>
 		</td>
 	</tr>
 
 
>>>>>>> ed470b904e341c5135d8bf38b24011ac7bfc7e63
 	<?php
 
 	html_end_box();
 }
 
 ?>
