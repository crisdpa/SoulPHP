<?php
	 
	/**
	 * PHP 5
	 *
	 * SoulPHP : Mini-Framework (http://soulphp.com)
	 * Copyright 2013, Christopher Díaz Pantoja
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @copyright     Copyright 2013, Copyright 2013, Christopher Díaz Pantoja
	 * @link          http://soulphp.com SoulPHP Mini-Framework
	 * @since         SoulPHP v 1.0.0
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 */
	 
	 session_start();
	
	
	require_once("configuration.php");
	require_once("classes/query.class.php");
	require_once("classes/core.class.php");
	require_once("classes/xtemplate.class.php");
	require_once("classes/class.phpmailer.php");
	
	date_default_timezone_set($config->timezone);

	$tplSite = new XTemplate("templates/default.html");
	$config = new Configuration();
	
	$core = new Core();
	$db = new QueryClass(array("hostname"=>$config->host,"database"=>$config->db,"username"=>$config->user,"password"=>$config->password));
	
	$core->createSession();
	
	$qs_action = $_GET["action"];
	$qs_section = !empty($_GET["section"])?$_GET["section"]:$config->section;
	$qs_module = $_GET["module"];
	$qs_module_response = $_GET["response"];
	
	
	if(empty($qs_action)){
	
		if(!empty($qs_module)){
		
			
			if(!file_exists("modules/".$qs_module."/index.php")){
				echo "fail loading module";
			}
			else{
			
				$tplModule = new XTemplate("modules/".$qs_module."/templates/default.html");
				
				require_once("modules/".$qs_module."/index.php");
				
				if($qs_module_response != "json"){
				
					$tplModule -> parse("main");
					echo $tplModule -> render("main");
				
				}
				
			}
		
		}
		else{
		
			$tplSite -> assign("__META_AUTHOR__",  $config->author);
			$tplSite -> assign("__SITE_INDEX__", $config->index);
			
			if($core -> getMessage()){
				
				$message = $core -> getMessage();
				
				$tplSite -> assign("__MESSAGE_TEXT__", $message["text"]);
				$tplSite -> assign("__MESSAGE_TYPE__", $message["type"]);
				
				$tplSite -> parse("main.message");
				$core -> clearSession("message");
				
			}


			/**********************************************
			* Customer Rules
			**********************************************/
			
			$tplSite -> assign("__SITE_DOMAIN__", $config -> domain);
			
			
			/**********************************************/
			
			if(!file_exists("sections/".$qs_section."/index.php")){
				header("Location: {$config -> domain}error404");
			}
			
	
			
			$tplSection = new XTemplate("sections/".$qs_section."/templates/default.html");
			require_once("sections/".$qs_section."/index.php");
			$tplSection -> parse("main");
			
			$tplSite -> assign("__CONTENT__", $tplSection -> render("main"));
			$tplSite -> parse("main");
			$tplSite -> out("main");
		
		}
		
		
	}
	else{
		
		if(!file_exists("actions/".$qs_action.".php")){
			header("Location: {$config -> site}error404");
		}
		else{
			require_once("actions/".$qs_action.".php");
		}
		
		
	}

?>