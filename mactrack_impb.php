<?php
 // +-------------------------------------------------------------------------+
 // | Copyright (C) 2007 Susanin                                          |
 // |                                                                         |
 // | This program is free software; you can redistribute it and/or           |
 // | modify it under the terms of the GNU General Public License             |
 // | as published by the Free Software Foundation; either version 2          |
 // | of the License, or (at your option) any later version.                  |
 // |                                                                         |
 // | This program is distributed in the hope that it will be useful,         |
 // | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 // | GNU General Public License for more details.                            |
 // +-------------------------------------------------------------------------+


function mactrack_impb_recent_data() {
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
		
		
}


?>