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
 ini_set('memory_limit', '256M');

 $title = __("IMPB - Blocked MAC's View");
 
 //***********************************************************

 $netdel_actions = array(
 	1 => "Удалить запись",
 	2 => "Изменить запись",
 	3 => "Создать запись"
 	);
 
 
/* check actions */
switch (get_request_var('action')) {
	case 'actions_':
		form_actions_netdel();

		break;
	default:
		impb_redirect();
		general_header();
		impb_view_netdel();
		bottom_footer();
		break;
}


function impb_view_get_netdel_records(&$sql_where, $apply_limits = TRUE, $rows = '30') {
     /* form the 'where' clause for our main sql query */

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
 
     if (strlen(get_request_var('filter'))) {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
             $sql_where .= " (imb_macip.macip_port_hex LIKE '%" . get_request_var('filter') . "%' OR " .
                 "imb_macip.macip_port_list LIKE '%" . get_request_var('filter') . "%' OR " .
 				"imb_macip.macip_lastchange_date LIKE '%" . get_request_var('filter') . "%' OR " .
 				"imb_macip.macip_scan_date LIKE '%" . get_request_var('filter') . "%')";
    }
 
 
 
	if ($apply_limits) {
		$sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ', ' . $rows;
	}else{
		$sql_limit = '';
	}
 	$sql_order = get_order_string();
	
 	
 		$query_string = "SELECT  imb_auto_updated_nets.*,user_auth.username as net_change_user_name from imb_auto_updated_nets left join user_auth on (imb_auto_updated_nets.net_change_user=user_auth.id)" .
			" where `net_type`=1 
			$sql_order
			$sql_limit";
 			
                                                                                  

 
         return db_fetch_assoc($query_string);
     
 }
 
function impb_netdel_request_validation() {
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
			'default' => 'net_id',
			'options' => array('options' => 'sanitize_search_string')
			),
		'sort_direction' => array(
			'filter' => FILTER_CALLBACK,
			'default' => 'ASC',
			'options' => array('options' => 'sanitize_search_string')
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
				
			
	);

	validate_store_request_vars($filters, 'sess_impb_netdel');
	/* ================= input validation ================= */
}


function impb_view_netdel() {
	global $title, $report, $impb_search_types, $config , $netdel_actions;

   
	impb_netdel_request_validation();

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
	impb_netdel_filter();
	html_end_box(); 
 
    $sql_where = "";
    $netdel_results = impb_view_get_netdel_records($sql_where, true, $rows);
    $rows_query_string = "SELECT
             COUNT(*)
             FROM imb_auto_updated_nets
             $sql_where";	
    $total_rows = db_fetch_cell($rows_query_string);
	
	$nav = html_nav_bar('impb_view_netdel.php?report=bindings', MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 14, __('NETs to del'), 'page', 'main');

	form_start('impb_view_netdel.php', 'chk');
	
	//print $nav;
	
	html_start_box('', '100%', '', '3', 'center', '');

 
	$display_text = array(
		"net_ipaddr" => array(__('Адрес подсети'), "ASC"),
		"net_mask" => array(__('Маска подсети'), "ASC"),
		"net_description" => array(__('Описание'), "ASC"),
		"net_change_user_name" => array(__('Автор'), "ASC"),
		"net_trigger_count" => array(__('Triggered count'), "ASC"));
		
	html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false);
 
    $i = 0;
	
    if (sizeof($netdel_results) > 0) {
         foreach ($netdel_results as $netdel_result) {

            form_alternate_row('line' . $netdel_result["net_id"], true);
 			
 			form_selectable_cell((strlen(get_request_var('ip_filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", long2ip($netdel_result["net_ipaddr"])) : long2ip($netdel_result["net_ipaddr"])), $netdel_result["net_id"] );			
 			
 			form_selectable_cell((strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", long2ip($netdel_result["net_mask"])) : long2ip($netdel_result["net_mask"])), $netdel_result["net_id"] );			
			
 			form_selectable_cell((strlen(get_request_var('filter')) ? preg_replace("/(" . preg_quote(get_request_var('filter')) . ")/i", "<span style='background-color: #F8D93D;'>\\1</span>", $netdel_result["net_description"]) : $netdel_result["net_description"]), $netdel_result["net_id"] );
			
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

	if (sizeof($netdel_results)) {
		print $nav;
	}

    impb_draw_actions_dropdown($netdel_actions, "");
	form_end();
 }
 

function form_actions_netdel() {
     global $config, $netdel_actions ;
 
 	/* ================= input validation ================= */
	get_filter_request_var('drp_action');
	/* ==================================================== */

    
	/* if we are to save this form, instead of display it */
 	if (isset_request_var('selected_items')) {
        $selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
 		$str_ids = '';
    
 		
 		if (get_request_var('drp_action') == "1") { /* удаление записи ип-мак-порт */
             foreach ($selected_items as $sel_item) {
                 /* ================= input validation ================= */
                 input_validate_input_number($sel_item);
                 /* ==================================================== */
                 api_dimpb_delete_net($sel_item, false);
             }
             //header("Location: impb_view.php");
 
         } elseif (get_request_var('drp_action') == "2") { /* изменить запись */
            			//$save_data[$cur_net_id]["net_id"] = form_input_validate($_REQUEST["tma_ip"], "tma_ip", "^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$", false, 3);
 		   foreach ($selected_items as $sel_item) {
 				/* ================= input validation ================= */
 				input_validate_input_number($sel_item);
 				/* ==================================================== */
 				$cur_net_id = $sel_item;
 				$netdels[$cur_net_id]['net_id'] = $cur_net_id;
				$netdels[$cur_net_id]['net_ipaddr'] = form_input_validate(impb_translate_ip_address(get_request_var('nde_' . $cur_net_id . '_ipaddr')), 'nde_' . $cur_net_id . '_ipaddr', '^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$', false,3 );
				$netdels[$cur_net_id]['net_mask'] = form_input_validate(impb_translate_ip_address(get_request_var('nde_' . $cur_net_id . '_mask')), 'nde_' . $cur_net_id . '_mask', '^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$', false,3 );
 				$netdels[$cur_net_id]['net_description'] = form_input_validate(get_request_var('nde_' . $cur_net_id . '_description'), 'nde_' . $cur_net_id . '_description', '', true, 3);

 		   }
 	        if (!is_error_message()) {
 				if (sizeof($netdels) > 0) {
 					foreach ($netdels as $netdel) {	
 						imb_change_net_record($netdel["net_id"], $netdel["net_ipaddr"],$netdel["net_mask"],$netdel["net_description"], '1','0');
 					}
 				}
 			}
 		}elseif (get_request_var('drp_action') == "3") { /* создание привязки */
 			$save_data = array();
 			$save_data["nda_ipaddr"] = form_input_validate(impb_translate_ip_address(get_request_var('nda_ipaddr')), 'nda_ipaddr', '^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$', false, 3);
			$save_data["nda_mask"] = form_input_validate(impb_translate_ip_address(get_request_var('nda_mask')), 'nda_mask', '^([0-2]{0,1}[0-9]{1,2}\.){3}([0-2]{0,1}[0-9]{1,2})$', false, 3);
 			$save_data["nda_description"] = form_input_validate(get_request_var('nda_description'), 'nda_description', '', false, 3) ;
 			if (!is_error_message()) {
 
 				$str_ids = substr($str_ids, 0, strlen($str_ids) -2);
 					imb_create_net_record($save_data["nda_ipaddr"] ,$save_data["nda_mask"], $save_data["nda_description"], '1','0');
 			}
 		}
 		
	
 	if (!is_error_message()) {
 		header("Location: impb_view_netdel.php?header=false");
 	}else{
 		header("Location: impb_view_netdel.php?header=false&action=" . get_request_var('action') . "&drp_action=" . get_request_var('drp_action') . "&post_error=" . serialize($selected_items));
 		$_REQUEST["selected_items"]="";
 	}
 		
         exit;
    }
 
     /* setup some variables */
     $row_list = ""; $i = 0; $row_ids = ""; $colspan = 2;
 
     /* loop through each of the ports selected on the previous page and get more info about them для создания первой страницы типа [Вы действительно хотите ....]*/
     if (!isset_request_var('post_error')) { /*Если установлено это значение - значит страница перезагружаеться из-за ошибки при вводе, и данные нужно брать не из POST, а из спец. переменной.*/
 		foreach ($_POST as $var => $val) {
 	        if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
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
 		$row_array=unserialize(stripslashes(get_request_var('post_error')));
 		if (isset($row_array) && is_array($row_array) && (count($row_array) > 0)) {
 			foreach ($row_array as $row_id) {
 	            $row_info = db_fetch_row("SELECT *, INET_NTOA(`net_ipaddr`) as anet_ipaddr , INET_NTOA(`net_mask`) as anet_mask FROM imb_auto_updated_nets WHERE `net_id`=" . $row_id);
 				$row_list .= "<li>" . $row_info["net_description"] . "     NET:" . $row_info["anet_ipaddr"] . "    MASK:" . $row_info["anet_mask"] . "<br>";
 				$row_ids = $row_ids . "'" . $row_id . "', ";		
 			}
 		}
 	}
 	
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);

 	top_header();

	form_start('impb_view_netdel.php?header=false');

	html_start_box($netdel_actions[get_request_var('drp_action')], '60%', '', '3', 'center', '');

	if ((!isset($row_array) or (!sizeof($row_array)))   &&     (((isset_request_var('drp_action')) && (get_request_var('drp_action') != "3")) || ((isset_request_var('post_error')) && (isset_request_var('drp_action')) && (get_request_var('drp_action') != "3")))) {
		print "<tr><td class='even'><span class='textError'>" . __('You must select at least one device.') . "</span></td></tr>\n";
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
		}elseif (((isset_request_var('drp_action')) && (get_request_var('drp_action') == "2")) || ((isset_request_var('post_error')) && (isset_request_var('drp_action'))  && (get_request_var('drp_action') == "2"))) { /*Изменение записи*/
			$netdels_rows=db_fetch_assoc("SELECT *, INET_NTOA(`net_ipaddr`) as anet_ipaddr , INET_NTOA(`net_mask`) as anet_mask " .
				" FROM imb_auto_updated_nets " .
				" WHERE `net_id` in (" . $row_ids . ");");
			
			html_start_box("Для изменения записи проверьте/измените следующие поля.", "100%", '', "3", "center", "");
	 
			html_header(array("","NET addres","NET mask", "Описание"));
	 
			$i = 0;
			if (sizeof($netdels_rows) > 0) {
				foreach ($netdels_rows as $netdels_row) {
					$netdel_id = $netdels_row["net_id"];
					form_alternate_row();
						?>
						<td><?php form_hidden_box('nde_' . $netdel_id . '_id', $netdel_id, '');?></td>
						<td><?php form_text_box('nde_' . $netdel_id . '_ipaddr', $netdels_row['anet_ipaddr'], '', 15, 15, 'text', 1) ;?></td>
						<td><?php form_text_box('nde_' . $netdel_id . '_mask', $netdels_row['anet_mask'], '', 15, 15, 'text', 1) ;?></td>
						<td><?php form_text_box('nde_' . $netdel_id . '_description', $netdels_row['net_description'], '', 250, 70, 'text', 1) ;?></td>
					</tr>
					<?php
				}
			}
			$colspan = 5;
	 
	 
		}elseif (((isset_request_var('drp_action')) && (get_request_var('drp_action') == "3")) || ((isset_request_var('post_error')) && (isset_request_var('drp_action'))  && (get_request_var('drp_action') == "3"))) { /*Добавление привязки*/
	 
			html_start_box("Проверьте значения полей", "100%", '', "4", "center", "");
			html_header(array("Адрес Сети","Маска", "Описание"));
			
				?>
					<td><?php form_text_box("nda_ipaddr", "000.000.000.000", "", 15, 15, "text", 1) ;?></td>
					<td><?php form_text_box("nda_mask", "255.255.255.0", "", 15, 15, "text", 1) ;?></td>
					<td><?php form_text_box("nda_description", "", "", 250, 70, "text", 1) ;?></td>
			
				</tr>
				<?php
				
				
			$colspan = 4;
		};
	};
 

	print "<tr>
		<td colspan='$colspan' align='right' class='saveRow'>
			<input type='hidden' name='action' value='actions_'>
			<input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
			<input type='hidden' name='drp_action' value='" . get_request_var('drp_action') . "'>" . (strlen($save_html) ? "
			<input type='button' name='cancel' onClick='cactiReturnTo()' value='" . __('No') . "'>
			$save_html" : "<input type='button' onClick='cactiReturnTo()' name='cancel' value='" . __('Return') . "'>") . "
		</td>
	</tr>";
 	
	html_end_box();

	form_end();

	bottom_footer();
}
 
 
 
function impb_netdel_filter() {
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
			</table>				
		</form>
		<script type='text/javascript'>
		function applyFilter() {
			strURL  = urlPath+'plugins/impb/impb_view_netdel.php?header=false';
			strURL += '&ip_filter_type_id=' + $('#ip_filter_type_id').val();
			strURL += '&ip_filter=' + $('#ip_filter').val();
			strURL += '&filter=' + $('#filter').val();
			strURL += '&rows=' + $('#rows').val();
			loadPageNoHeader(strURL);
		}

		function clearFilter() {
			strURL  = urlPath+'plugins/impb/impb_view_netdel.php?header=false&clear=true';
			loadPageNoHeader(strURL);
		}
	
		function exportRows() {
			strURL  = urlPath+'plugins/impb/impb_view_netdel.php?export=true';
			document.location = strURL;
		}

		function importRows() {
			strURL  = urlPath+'plugins/impb/impb_view_netdel.php?import=true';
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
