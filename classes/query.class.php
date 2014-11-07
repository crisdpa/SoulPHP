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
		
		
		public function QueryClass($db_connection){
		
			$this->_hostname = $db_connection["hostname"]; 
			$this->_username = $db_connection["username"];
			$this->_password = $db_connection["password"];
			$this->_database = $db_connection["database"];
			
		}
		
		
		public function connect(){
	
			
			$this->_connection = new mysqli('p:'.$this->_hostname, $this->_username, $this->_password, $this->_database);
			
			if (mysqli_connect_error()) {
				die('SoulPHP - BD Error: ('.mysqli_connect_errno().') '.mysqli_connect_error());
			}
			
			$this->_connection -> query("SET NAMES utf8");
	
			
		}
		
		
		public function query($query, $type = "", $die = true){
			
			$this -> connect();
			
			if ($result = $this->_connection -> query($query)){
				
				
				if(empty($type)){
					
					if($result === TRUE){ return true; }
					else { return mysqli_fetch_object($result); }
				
				}
				
				else if($type == "list"){
					
					$list = array();
					
					while($row = mysqli_fetch_object($result)){
						$list[] = $row;
					}
					
					return $list;
					
				}
				
				$result -> close();
				
			}
			else{
				if($die){
					die('SoulPHP - BD Error: '.$this->_connection -> error);
				}
				else{
					return false;	
				}
			}
			
			$this->_connection -> close();
		
			
		}
		
		
		
		public function select($table, $params = array(), $die = true){
			
			if(count($params) == 0){
				
				$params['fields'] = '*';
				$params['limit'] = '';
				$params['order'] = '';
				$params['conditions'] = '';
				
			}
			else{
				
				$params['fields'] = array_key_exists('fields',$params)?$params['fields']:'*';
				$params['limit'] = array_key_exists('limit',$params)?$params['limit']:'';
				$params['order'] = array_key_exists('order',$params)?$params['order']:'';
				$params['conditions'] = array_key_exists('conditions',$params)?$params['conditions']:'';
				
			}
			
			
			//Limit
			if(empty($params['limit'])){ $limit_query = ''; }
			else{  $limit_query = " LIMIT {$params['limit']}";}
			
			//Fields
			if(is_array($params['fields'])){ $fields_query = join(",",$params['fields']); }
			else if($params['fields'] == 'count'){ $fields_query = 'COUNT(*) as total'; $limit_query = ' LIMIT 1'; }
			else{ $fields_query = $params['fields'];}
					
			//Ordering
			if(is_array($params['order'])){ 
				
				$ordering_fields = array();
				
				foreach($params['order'] as $field => $ordering){
					$ordering_fields[] = $field." ".$ordering;
				}
				
				$order_query = ' ORDER BY '.join(',',$ordering_fields);
				
			}
			else{  $order_query = '';}
			
			
			//Conditions
			if(is_array($params['conditions'])){ 
				
				$conditions_fields = array();
				$counter = 0;
				
				foreach($params['conditions'] as $conditional => $condition){
					
					if($counter == 0){
						$conditions_query .= "WHERE ".$condition.' ';
					}
					else{
						$conditions_query .= $conditional." ".$condition.' ';
					}
					
					$counter++;
				}
				
				
			}
			else{  $conditions_query = '';}
			
			//Perform query
			
			$sqlTable = "SELECT {$fields_query}
						 FROM {$table}
						 {$conditions_query}
						 {$order_query}
						 {$limit_query}";
			
			if($params['fields'] == 'count'){
				$result_tmp = $this -> query($sqlTable,'',$die);
				$result = $result_tmp -> total;
			}
			else if($params['limit'] == '1'){
				$result = $this -> query($sqlTable,'',$die);
			}
			else{
				$result = $this -> query($sqlTable,'list',$die);	
			}
				 
			return $result;		 
			
			
		}
		
		
		
		public function delete($table, $params = array(), $die = true){
			
			if(count($params) == 0){
				
				$params['conditions'] = '';
				$params['limit'] = '';
				
			}
			else{
				
				$params['limit'] = array_key_exists('limit',$params)?$params['limit']:'';
				$params['conditions'] = array_key_exists('conditions',$params)?$params['conditions']:'';
				
			}
			
			//Limit
			if(empty($params['limit'])){ $limit_query = ''; }
			else{  $limit_query = " LIMIT {$params['limit']}";}
			
			
			//Conditions
			if(is_array($params['conditions'])){ 
				
				$conditions_fields = array();
				$counter = 0;
				
				foreach($params['conditions'] as $conditional => $condition){
					
					if($counter == 0){
						$conditions_query .= "WHERE ".$condition.' ';
					}
					else{
						$conditions_query .= $conditional." ".$condition.' ';
					}
					
					$counter++;
				}
				
				
			}
			else{  $conditions_query = '';}
			
			//Perform query
			
			$sqlTable = "DELETE FROM {$table}
						 {$conditions_query}
						 {$limit_query}";
						 
			$result = $this -> query($sqlTable,'',$die);
			
			return $result;
			
			
		}
		
		
	}
	

?>
