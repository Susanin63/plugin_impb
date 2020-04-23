 function show_ping_w(device_id) {
	//$('#element_to_pop_up').bPopup({appending : false, modalColor: 'greenYellow',position: [550, 400]});
	var post_data = {type:"start_html_ping"};
	
	$('#element_to_pop_ping').bPopup({
		//position: [‘auto’,’auto’],
        content: 'ajax',
		scrollBar  : true,
        contentContainer: '#element_to_pop_ping',
        loadData: post_data,
        loadUrl: 'impb_ajax.php'
		//loadCallback: function(){ update(device_id); }
    });
	setTimeout(get_info, 50, device_id);

}


function ping_host(device_id){
    var post_data = {type:"iping",ip:device_id};
	$.ajax({
        url: "impb_ajax.php",
		type: 'post',
		data: post_data,
        success: 
          function(result){
			rez  = "";
			var div = document.getElementById('i_ping');
			if ('null' != div && div !== null){
				if (div.childNodes.length > 13){
					for (var i = 2; i < 14; i++) {
					  rez = rez + div.childNodes[i].outerHTML; // Text, DIV, Text, UL, ..., SCRIPT
					}					
					div.innerHTML = rez + result;
					
					
				}else{
					div.innerHTML = div.innerHTML + result;
				}
				setTimeout(function(){
					ping_host(device_id); //this will send request again and again;
				}, 1000);				
			}

        }});
}

function get_info(device_id){
    var post_data = {type:"get_info",ip:device_id};
	$.ajax({
        url: "impb_ajax.php",
		type: 'post',
		data: post_data,
        success: 
          function(result){
			rez  = "";
			var div = document.getElementById('a_info');
			if ('null' != div && div !== null){
				div.innerHTML = div.innerHTML + result;
				//start ping
				setTimeout(ping_host, 500, device_id);
			}

        }});
}

var url

function scan_device(device_id) {
	url=urlPath+'plugins/impb/impb_ajax_admin.php?action=rescan&device_id='+device_id
	$('#r_'+device_id).attr('src', 'images/view_busy.gif');
	$.get(url, function(data) {
		reply     = data.split('!!!!')
		type      = reply[0]
		device_id = reply[1]
		content   = reply[2]
		$('#r_'+device_id).attr('src', 'images/rescan_site.gif');
		$('#response').html(content);
	});
}

function site_scan(site_id) {
	url=urlPath+'plugins/impb/impb_ajax_admin.php?action=site_scan&site_id='+site_id;
	$('#r_'+site_id).attr('src', urlPath+'plugins/impb/images/view_busy.gif');
	$.get(url, function(data) {
		reply     = data.split('!!!!')
		type      = reply[0]
		site_id   = reply[1]
		content   = reply[2]
		$('#r_'+site_id).attr('src', 'images/rescan_site.gif');
		$('#response').html(content);
	});
}

function scan_device_interface(device_id, ifName) {
	url=urlPath+'plugins/impb/impb_ajax_admin.php?action=rescan&device_id='+device_id+'&ifName='+ifName;
	$('#r_'+device_id+'_'+ifName).attr('src', urlPath+'plugins/impb/images/view_busy.gif');
	$.get(url, function(data) {
		reply     = data.split('!!!!')
		type      = reply[0]
		device_id = reply[1]
		ifName    = reply[2]
		content   = reply[3]
		$('#r_'+device_id+'_'+ifName).attr('src', 'images/rescan_device.gif');
		$('#response').html(content);
	});
}

function clearScanResults() {
	$('#response').html('');
}

function disable_device(device_id) {
	url=urlPath+'plugins/impb/impb_ajax_admin.php?action=disable&device_id='+device_id;
	$.get(url, function(data) {
		reply     = data.split('!!!!')
		type      = reply[0]
		device_id = reply[1]
		content   = reply[2]
		$('#row_'+device_id).html(content);
	});
}

function enable_device(device_id) {
	url=urlPath+'plugins/impb/impb_ajax_admin.php?action=enable&device_id='+device_id;
	$.get(url, function(data) {
		reply     = data.split('!!!!')
		type      = reply[0]
		device_id = reply[1]
		content   = reply[2]
		$('#row_'+device_id).html(content);
	});
}


