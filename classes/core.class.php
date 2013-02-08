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
	
	class Core{
		
		public $__prefix = "soulphp_";
		public $__session_id;
		
		public function __construct(){
			
			global $config;
			$this->__session_id = $this ->__prefix.md5($config -> domain);
			
		}
		
		
		
		public function setTitle($title){
		
			global $tplSite;
			global $config;
			
			$tplSite->assign("__LABEL_TITLE__", $title);
		}
		
		
		
		public function setMetaDescription($description){
		
			global $tplSite;
			global $config;
			
			$tplSite->assign("__META_DESCRIPTION__",$description);
		}
		
		
		
		public function setMetaKeywords($keywords){
		
			global $tplSite;
			global $config;
			
			$tplSite->assign("__META_KEYWORDS__",$keywords);
		}
		
		
		
		
		
		public function setStyle($style, $type = "screen" ){
		
			global $tplSite;
			
			$tplSite->assign("__STYLE_URL__", $style);
			$tplSite->assign("__STYLE_MEDIA__", $type);
			$tplSite->parse("main.style");
				
		}
		
		
		
		public function setScript($script){
		
			global $tplSite;
			
			$tplSite->assign("__SCRIPT_URL__", $script);
			$tplSite->parse("main.script");
				
		}
		
		
		
		public function loadModule($module){
			
			global $core;
			global $tplSite;
			global $config;
			global $db;
			
			$tplModule = new XTemplate("modules/".$module."/templates/default.html");
			require_once("modules/".$module."/index.php");
			$tplModule -> parse("main");
			return $tplModule -> render("main");
			
		}
		
		
		
		
		
		
		
		public function createSession(){
		
			if(!is_array($this->getSession("site"))){
							
				$site = array(
								"url" => ""
							  );
							  
				$this -> setSession("site",$site);
							  
			}
			
			
			if(!is_array($this->getSession("user"))){
							
				$user = array(
								"ip" => $_SERVER['REMOTE_ADDR'],
								"time" => date("Y-m-d H:i:s"),
								"user" => "",
							  );
							  
				$this -> setSession("user",$user);
							  
			}
			
			if(!is_array($this -> getSession("message"))){
						  
				$message = array(
								"text" => "",
								"type" => ""
								);
							  
				$this -> setSession("message",$message);
				
			}
										
			
		}
		
		
		
		
		
		
		
		public function setSession($name,$value){
			
			$_SESSION[$this->__session_id][$name] = $value;
		
		}
		
		
		
		
		public function getSession($name){
			
			return $_SESSION[$this->__session_id][$name];
		
		}
		
		
		
		
		public function clearSession($name){
		
			unset($_SESSION[$this->__session_id][$name]);
			
		}
		
		
		
		
		
		
		public function setCurrentURL(){
			
			global $config;
			
			$url_domain = $config->domain;
			$url_params = $_SERVER['QUERY_STRING'];
			$url_protocol = empty($_SERVER['HTTPS'])?"http://":"https://";
			$action = $_GET["action"];
			
			$default_params = array("section","item","option","page");
			
			if(empty($url_params)){
				$current_url = $url_domain;
			}
			else{
				
				$qs = explode("&", $url_params);
				
				foreach($qs as $param){
					
					$var = explode("=",$param);
					
					if(in_array($var[0],$default_params)){
						
						$friendly_url[array_search($var[0], $default_params)] = $var[1];
						$url_default = true;
						
					}
					else{
						$url_default = false;
						break;
					}
					
				}
				
				if($url_default){
					$current_url = $url_domain.join("/", $friendly_url);
				}
				else{
					$current_url = $url_domain.$config->index."?".$url_params;
				}
				
				
			}
			
			$session_site = $this->getSession("site");
			$session_site["url"] = $current_url;
			
			$this->setSession("site",$session_site);
			
			return $current_url;
			
		}
		
		
		
		
		
		public function getURL(){
			
			$session_site = $this->getSession("site");
			return $session_site["url"];
		
		}
		
		
		
		
		
		public function redirect($url){
			
			header("Location: $url");
			
		}
		
		
		
		
		
		public function setMessage($message,$type="advice"){
			
			$amessage["text"] = $message;
			$amessage["type"] = $type;
			
			$this -> setSession("message",$amessage);
			
		}
		
		
		
		
		public function getMessage(){
		
			$message = $this -> getSession("message");
			
			if(empty($message["text"])){
				return false;
			}
			else{
				return $message; 
			}
		
		}
		
		
		
		
		
		public function getRequest($request){
			
			return $this -> cleanVar($_REQUEST[$request]);
			
		}
		
		
		
		
		public function cleanVar($var){
		
			$var = trim($var);
			$var = addslashes($var);
			
			return $var;
		}
		
		
		
		
		
		public function pagerResults($totalRecords,$resPerPage,$crntPage,$page_range=3){
		
		
			$currentPage = (empty($crntPage) or $crntPage == 0) ? 1 : $crntPage;
			$totalpages =(integer)($totalRecords/$resPerPage);
			$totalmodule = $totalRecords%$resPerPage; 
			
			$totalpages = ($totalRecords <= $resPerPage || $totalmodule == 0) && $totalpages !== 0? $totalpages : $totalpages + 1;
			$recordBegin = ($currentPage * $resPerPage) - $resPerPage;
			
			/*******************************
			* Pager Get Numbers Range
			*******************************/
			
			if($totalpages > 1){
			
				if($currentPage == 1){
					if($totalpages <=($page_range * 2) ){
					
						$range["begin"] = 1;
						$range["end"] = $totalpages;
						$range["next"] = "2";
						$range["prev"] = false;
									 
					}
					else{
					
						$range["begin"] = 1;
						$range["end"] = (($page_range * 2) + 1);
						$range["next"] = "2";
						$range["prev"] = false;
					
					}
				}
				else if($currentPage == $totalpages){
				
					if($totalpages <=($page_range * 2) ){
					
						$range["begin"] = 1;
						$range["end"] = $totalpages;
						$range["next"] = false;
						$range["prev"] = ($totalpages - 1);
									 
					}
					else{
					
						$range["begin"] = $totalpages - ($page_range * 2);
						$range["end"] = $totalpages;
						$range["next"] = false;
						$range["prev"] = ($totalpages - 1);
					
					}
				
				}
				else{
					
					if(($currentPage - $page_range) < 1){
						
						$range["begin"] = 1;
						$range["prev"] = ($currentPage - 1);
						
					}
					else{
					
						$range["begin"] = $currentPage - $page_range;
						$range["prev"] = ($currentPage - 1);
						
					}
					
					if(($currentPage + $page_range) > $totalpages){
						
						$range["end"] = $totalpages;
						$range["next"] = ($currentPage + 1);
						
					}
					else{
						
						$range["end"] = $currentPage + $page_range;
						$range["next"] = ($currentPage + 1);
						
					}
					
					if(abs($range["begin"] - $range["end"]) < ($page_range * 2)){
					
						$ro = abs($range["begin"] - $range["end"]);
						$ra = $page_range * 2;
						$rd = abs($ro - $ra);
						
						if($range["begin"] == 1){
							$range["end"] = (($range["end"] + $rd) > $totalpages)?$totalpages:$range["end"] + $rd;
						}
						else if($range["end"] == $totalpages){
							$range["begin"] = (($range["begin"] - $rd) < 1)?1:$range["begin"] - $rd;
						}
					}
					
					
					
					
				
				}
				
				/*******************************
				* Get Numbers Link
				*******************************/
				
				$links = array();
				
				for($i = $range["begin"]; $i <= $range["end"]; $i++){
					$links[$i] = $i;
				}
				
				
				
			}
			else{
				$range["begin"] = 0;
				$range["end"] = 0;
				$range["next"] = false;
				$range["prev"] = false;
			}
			
			/*******************************
			* Pager Array
			*******************************/
				
				$pager = array(
							"pages" => $totalpages,
							"begin" => $recordBegin,
							"range" => $range,
							"links" => $links
						   );
			
			/******************************/
			
			
			return $pager;
		
		}
		
		
		
		
		
		
	}
	
?>