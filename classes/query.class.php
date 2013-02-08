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

	class QueryClass{
	
		 var $_hostname;
		 var $_database; 
		 var $_username;
		 var $_password;
		 var $_connection;
		
		 var $_recordset;
		 var $_row;
		
		
		public function QueryClass($db_connection){
		
			$this->_hostname = $db_connection["hostname"]; 
			$this->_username = $db_connection["username"];
			$this->_password = $db_connection["password"];
			$this->_database = $db_connection["database"];
			
		}
		
		
		public function connect(){
		
			$this->_connection = mysql_pconnect(
													$this->_hostname, 
													$this->_username,
													$this->_password) 
													or trigger_error(mysql_error(), E_USER_ERROR
												);
			
			mysql_select_db($this->_database, $this->_connection) or trigger_error(mysql_error(), E_USER_ERROR);
			mysql_query("SET NAMES utf8", $this->_connection) or die(mysql_error());
			
		}
		
		
		public function query($query,$type=""){
		
			$this->_recordset = mysql_query($query, $this->_connection) or die(mysql_error());
			
			if($type == "select"){
			
				$this->_row = mysql_fetch_object($this->_recordset);	
				return $this->_row;
				
			}
			
			else if($type == "list"){
				
				$list = array();
				
				while($this->_row = mysql_fetch_object($this->_recordset)){
					$list[] = $this->_row;
				}
				
				return $list;
				
			}
			
			mysql_free_result($this->_recordset);
			
			
		}
		
	}
?>
