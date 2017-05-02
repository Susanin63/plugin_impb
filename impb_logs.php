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
 chdir('../../');
 include("./include/auth.php");
  include_once($config["base_path"] . "/plugins/impblinding/lib/impblinding_functions.php");
 
 define("MAX_DISPLAY_PAGES", 21);
 $logs_actions = array(
     1 => "Удалить запись"
 	);
 
 /* set default action */
 if (!isset($_REQUEST["action"])) { $_REQUEST["action"] = ""; }
 
 switch ($_REQUEST["action"]) {
     case 'actions_logs':
         form_actions_logs();
         exit;        
 }
 
 switch ($_REQUEST["action"]) {
   default:
 	$title = "D-Link IP-MAC-Port Blinding  -  Просмотр Журнала операций.";
 	include_once("./include/top_header.php");
 	impblinding_view_logs();
 	include_once("./include/bottom_footer.php");
 }
 
 function impblinding_view_logs() {
     global $title, $colors, $config, $logs_actions, $realm_id, $imp_timespans;
 
     /* ================= input validation ================= */
     input_validate_input_number(get_request_var_request("l_device_id"));
     input_validate_input_number(get_request_var_request("l_page"));
     input_validate_input_number(get_request_var_request("l_user_id"));
     input_validate_input_number(get_request_var_request("l_date"));
 	input_validate_input_number(get_request_var_request("l_save"));
     
     /* ==================================================== */
 
     /* clean up search string */
     if (isset($_REQUEST["l_filter"])) {
         $_REQUEST["l_filter"] = sanitize_search_string(get_request_var("l_filter"));
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
     if (isset($_REQUEST["clear_logs_x"])) {
 		kill_session_var("sess_impb_view_logs_current_page");
         kill_session_var("sess_impb_view_logs_filter");
         kill_session_var("sess_impb_view_logs_device_id");
 		kill_session_var("sess_impb_view_logs_user_id");
 		kill_session_var("sess_impb_view_logs_date");
 		kill_session_var("sess_impb_view_logs_save");
         kill_session_var("sess_impb_view_macs_sort_column");
         kill_session_var("sess_impb_view_macs_sort_direction");		
 
         $_REQUEST["page"] = 1;
         unset($_REQUEST["l_filter"]);
         unset($_REQUEST["l_device_id"]);
 		unset($_REQUEST["l_user_id"]);
         unset($_REQUEST["l_date"]);
 		unset($_REQUEST["l_save"]);
 		unset($_REQUEST["sort_column"]);
         unset($_REQUEST["sort_direction"]);
     }else{
         /* if any of the settings changed, reset the page number */
         $changed = 0;
         $changed += impblinding_check_changed("l_device_id", "sess_impb_view_logs_device_id");
 		$changed += impblinding_check_changed("l_user_id", "sess_impb_view_logs_user_id");
 		$changed += impblinding_check_changed("l_date", "sess_impb_view_logs_date");
 		$changed += impblinding_check_changed("l_save", "sess_impb_view_logs_save");
 		$changed += impblinding_check_changed("l_filter", "sess_impb_view_logs_filter");
 
         if ($changed) {
             $_REQUEST["page"] = "1";
             $_REQUEST["l_page"] = $_REQUEST["page"];
         }else{
             if (isset($_REQUEST["page"])) {
                 $_REQUEST["l_page"] = $_REQUEST["page"];
             }
         }
     }
 
     /* remember these search fields in session vars so we don't have to keep passing them around */
     load_current_session_value("l_page", "sess_impb_view_logs_current_page", "1");
 	load_current_session_value("l_filter", "sess_impb_view_logs_filter", "");
     load_current_session_value("l_device_id", "sess_impb_view_logs_device_id", "-1");
     load_current_session_value("l_date", "sess_impb_view_logs_filter", "");
 	load_current_session_value("l_save", "sess_impb_view_logs_save", "-1");
 	load_current_session_value("l_user_id", "sess_impb_view_logs_user_id", "-1");
 	load_current_session_value("l_filter", "sess_impb_view_logs_filter", "");
     load_current_session_value("sort_column", "sess_impb_view_logs_sort_column", "description");
     load_current_session_value("sort_direction", "sess_impb_view_logs_sort_direction", "ASC");	
         
     //impblinding_view_header();
 
     //impblinding_view_footer();
 
 	
 	html_start_box("<strong>D-Link IP-MAC-Port Blinding Logs Filters</strong>", "98%", $colors["header"], "3", "center","");
 
 	include("plugins/impblinding/html/inc_impblinding_log_filter_table.php");
 
 	html_end_box();	
 	
 	
     $sql_where = "";
 
     $logs = impblinding_view_get_logs_records($sql_where);
         $total_rows = db_fetch_cell("SELECT
             COUNT(imb_log.log_id)
             FROM imb_log
             $sql_where");
 
 html_start_box("", "98%", $colors["header"], "3", "center", "");
     /* generate page list */
     $url_page_select = str_replace("&page", "?page", get_page_list($_REQUEST["l_page"], MAX_DISPLAY_PAGES, read_config_option("dimpb_num_rows"), $total_rows, "impblinding_logs.php"));
 
     $nav = "<tr bgcolor='#" . $colors["header"] . "'>
             <td colspan='10'>
                 <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                     <tr>
                         <td align='left' class='textHeaderDark'>
                             <strong>&lt;&lt; "; if ($_REQUEST["l_page"] > 1) { $nav .= "<a class='linkOverDark' href='impblinding_logs.php?page=" . ($_REQUEST["l_page"]-1) . "'>"; } $nav .= "Previous"; if ($_REQUEST["l_page"] > 1) { $nav .= "</a>"; } $nav .= "</strong>
                         </td>\n
                         <td align='center' class='textHeaderDark'>
                             Showing Rows " . ((read_config_option("dimpb_num_rows")*($_REQUEST["l_page"]-1))+1) . " to " . ((($total_rows < read_config_option("dimpb_num_rows")) || ($total_rows < (read_config_option("dimpb_num_rows")*$_REQUEST["l_page"]))) ? $total_rows : (read_config_option("dimpb_num_rows")*$_REQUEST["l_page"])) . " of $total_rows [$url_page_select]
                         </td>\n
                         <td align='right' class='textHeaderDark'>
                             <strong>"; if (($_REQUEST["l_page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "<a class='linkOverDark' href='impblinding_logs.php?page=" . ($_REQUEST["l_page"]+1) . "'>"; } $nav .= "Next"; if (($_REQUEST["l_page"] * read_config_option("dimpb_num_rows")) < $total_rows) { $nav .= "</a>"; } $nav .= " &gt;&gt;</strong>
                         </td>\n
                     </tr>
                 </table>
             </td>
         </tr>\n";
 
     print $nav;
 
 	        $display_text = array(
                 "log_date" => array("Дата", "ASC"),
                 "log_user_full_name" => array("Пользователь", "ASC"),
                 "log_object" => array("Объект", "ASC"),
                 "log_operation" => array("Тип<br>Операции", "ASC"),
                 "description" => array("Устройство", "DESC"),
                 "log_message" => array("Сообщение", "ASC"),
 				"log_saved" => array("Сохранено", "ASC"),
 				"log_rezult" => array("Результат", "ASC"),
                 "log_check_rezult" => array("Проверка", "DESC"));
         html_header_sort_checkbox($display_text, $_REQUEST["sort_column"], $_REQUEST["sort_direction"]);
 		
         $i = 0;
         $port_imp_state = '';
         $port_imp_state_color = '';
         if (sizeof($logs) > 0) {
             foreach ($logs as $log) {
 
 
                 form_alternate_row_color($colors["alternate"],$colors["light"],$i); $i++;
                     ?>
                     <td><?php print ($log["log_date"]);?></td>
 					<td><?php print ($log["log_user_full_name"]);?></td>
 					<td><?php print ($log["log_object"]);?></td>
 					
 					<td><?php print ($log["log_operation"]);?></td>
 					<td width=350>
                         <?php print "<p class='linkEditMain'><strong>" . eregi_replace("(" . preg_quote($_REQUEST["l_filter"]) . ")", "<span style='background-color: #F8D93D;'>\\1</span>", $log["description"]) . "</strong></p>";?>
                     </td>
 					<td><?php print ($log["log_message"]);?></td>
 					<td><?php print ($log["log_saved"]);?></td>
 					<td><?php print ($log["log_rezult"]);?></td>
 					<td><?php print ($log["log_check_rezult"]);?></td>
 					<td style="<?php print get_checkbox_style();?>" width="1%" align="right">
 						<input type='checkbox' style='margin: 0px;' name='chk_<?php print $log["log_id"];?>' title="<?php print $log["log_id"];?>">
 					</td>
                 </tr>
                 <?php
             }
 
             /* put the nav bar on the bottom as well */
             print $nav;
         }else{
             print "<tr><td><em>Журнал операций пуст</em></td></tr>";
         }
         html_end_box(false);
 		lm_draw_actions_dropdown($logs_actions, "logs");
         //lm_draw_actions_dropdown($port_actions, "logs");
 
 }
 
 function impblinding_view_get_logs_records(&$sql_where, $apply_limits = TRUE) {
     /* create SQL where clause */
 
     $sql_where = "";
 
     if ($_REQUEST["l_device_id"] != "-1") {
         if (!strlen($sql_where)) {
             $sql_where = "WHERE (imb_log.log_device_id=" . $_REQUEST["l_device_id"] . ")";
         }else{
             $sql_where .= " AND (imb_log.log_device_id=" . $_REQUEST["l_device_id"] . ")";
         }
     }
 
 	if ($_REQUEST["l_user_id"] != "-1") {
         if (!strlen($sql_where)) {
             $sql_where = "WHERE (imb_log.log_user_id=" . $_REQUEST["l_user_id"] . ")";
         }else{
             $sql_where .= " AND (imb_log.log_user_id=" . $_REQUEST["l_user_id"] . ")";
         }
     }
 	
 	if ($_REQUEST["l_save"] != "-1") {
 	
 	    if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
         switch ($_REQUEST["l_save"]) {
             case "0": 
                 $sql_where .= " (imb_log.log_saved='0') ";
 				break;	
             case "1": /* is null */
                 $sql_where .= " (imb_log.log_saved='1') ";
         }	
 
     }
 if ($_REQUEST["l_date"] != "-1") {
         if (strlen($sql_where) > 0) {
             $sql_where .= " AND ";
         }else{
             $sql_where = " WHERE ";
         }
 
         switch ($_REQUEST["l_date"]) {
             case "1": /* do not filter */
                 $sql_where .= " UNIX_TIMESTAMP()-UNIX_TIMESTAMP(imb_log.log_date) < '1800'";
 				break;
             case "2": /* matches */
                 $sql_where .= " UNIX_TIMESTAMP()-UNIX_TIMESTAMP(imb_log.log_date) < '3600'";
                 break;
             case "3": /* contains */
                 $sql_where .= " UNIX_TIMESTAMP()-UNIX_TIMESTAMP(imb_log.log_date) < '7200'";
                 break;
             case "4": /* begins with */
                 $sql_where .= " UNIX_TIMESTAMP()-UNIX_TIMESTAMP(imb_log.log_date) < '86400'";
                 break;
             case "5": /* does not contain */
                 $sql_where .= " UNIX_TIMESTAMP()-UNIX_TIMESTAMP(imb_log.log_date) < '604800'";
                 break;
             case "6": /* does not begin with */
                 $sql_where .= " DATEDIFF(CURRENT_DATE,imb_log.log_date); < '30'";
                 break;
             case "7": /* is null */
                 $sql_where .= " DATEDIFF(CURRENT_DATE,imb_log.log_date); < '365'";
         }
     }
 
 $query_string = "SELECT
             imb_log.log_id,
 			imb_log.log_user_id,
 			imb_log.log_user_full_name,
 			imb_log.log_date,
 			imb_log.log_object,
 			imb_log.log_operation,
 			imb_log.log_device_id,
 			imb_log.log_message,
 			imb_log.log_rezult,
 			imb_log.log_check_rezult,
 			imb_log.log_read_this_user,
 			imb_log.log_read_admin,
 			imb_log.log_saved,
             imb_devices.hostname,
             imb_devices.description
             FROM (imb_log  LEFT JOIN imb_devices on imb_log.log_device_id=imb_devices.device_id )  
             $sql_where
             ORDER BY imb_log.log_id";
 
         if ($apply_limits) {
             $query_string .= " LIMIT " . (read_config_option("dimpb_num_rows")*($_REQUEST["l_page"]-1)) . "," . read_config_option("dimpb_num_rows");
         }
         
         
     return db_fetch_assoc($query_string);
 }
 
 
 function form_actions_logs() {
     global $colors, $config, $logs_actions;
 
     /* if we are to save this form, instead of display it */
     if (isset($_POST["selected_items"])) {
         $selected_items = unserialize(stripslashes($_POST["selected_items"]));
 		$str_ids = '';
     
 		
 		if ($_POST["drp_action"] == "1") { /* удаление записи log */
             for ($i=0;($i<count($selected_items));$i++) {
                 /* ================= input validation ================= */
                 input_validate_input_number($selected_items[$i]);
                 /* ==================================================== */
                 imp_delete_logs($selected_items[$i]);
             }
             header("Location: impblinding_logs.php");
         } 
 		
 	header("Location: impblinding_logs.php");
 		
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
             $row_info = db_fetch_row("SELECT imb_log.*, imb_devices.hostname, imb_devices.description FROM imb_log left join imb_devices on (imb_log.log_device_id = imb_devices.device_id) WHERE imb_log.log_id=" . $matches[1]);
 			$row_list .= "<li>" . $row_info["description"] . "    Date:" . $row_info["log_date"] . "      " . $row_info["log_message"] .   "<br>";
             $row_array[$i] = $matches[1];
 			$row_ids = $row_ids . "'" . $matches[1] . "', ";
         }                                  
         $i++;
     }
 	$row_ids = substr($row_ids, 0, strlen($row_ids) -2);
 
     include_once("./include/top_header.php");
 
     html_start_box("<strong>" . $logs_actions{$_POST["drp_action"]} . "</strong>", "60%", $colors["header_panel"], "3", "center", "");
 
     print "<form action='impblinding_logs.php' method='post'>\n";
 
     if ($_POST["drp_action"] == "1") {  /* удаление записей */
         print "    <tr>
                 <td class='textArea' bgcolor='#" . $colors["form_alternate1"]. "'>
                     <p>Подтверждаете удаление следующих записей ?</p>
                     <p>$row_list</p>
                 </td>
             </tr>\n
             ";
     };
 
 	
     if (!isset($row_array)) {
         print "<tr><td bgcolor='#" . $colors["form_alternate1"]. "'><span class='textError'>Вы должны выбрать минимум одну запись.</span></td></tr>\n";
         $save_html = "";
     }else{
 		$save_html = "<input type='image' src='" . $config['url_path'] . "images/button_yes.gif' alt='Save' align='absmiddle'>";
     }
 	
     print "    <tr>
             <td colspan='2' align='right' bgcolor='#eaeaea'>
                 <input type='hidden' name='action' value='actions_logs'>
                 <input type='hidden' name='selected_items' value='" . (isset($row_array) ? serialize($row_array) : '') . "'>
                 <input type='hidden' name='drp_action' value='" . $_POST["drp_action"] . "'>
                 <a href='impblinding_logs.php'><img src='" . $config['url_path'] . "images/button_no.gif' alt='Cancel' align='absmiddle' border='0'></a>
                 $save_html
             </td>
         </tr>
         ";
 
     html_end_box();
 }
 
 
 ?>
