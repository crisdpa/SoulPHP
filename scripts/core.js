// Core Javascript

function loadModule(module,layer,params){
	
	if(module != "" && layer != ''){
		
		$("#"+layer).append("<div class=\"loader\"><img src=\"/images/loader-icon.gif\" alt=\"loading...\"></div>");
		
		$.post("/index.php?module="+module, { params: params }, 
				function(data){ 
					$("#"+layer).replaceWith("<div id=\""+layer+"\">"+data+"</div>");
					$('#'+layer).hide();
					$('#'+layer).fadeIn("slow");
		});
		
	}
	
}