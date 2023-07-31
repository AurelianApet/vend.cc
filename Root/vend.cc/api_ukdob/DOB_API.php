<?php

/*
	Client API
	DON'T MODIFY IT
	
	v0.1
*/

require_once 'DOB_API_Exceptions.php';

class DOB_API {

	protected static $_instance;
	
	private $_api_url;
	private $_api_key;

	public function __construct($api_url, $api_key) {
		
		if(!function_exists('curl_init')) {
			throw new API_Client_Exception('Please install CURL extension!');
		}
		
		if(!function_exists('json_encode')) {
			throw new API_Client_Exception('Please install JSON extension!');
		}
		
		$this->_api_url = $api_url;
		$this->_api_key = $api_key;
		
	}
	
	public static function get_instance($api_url, $api_key) {
		if(is_null(self::$_instance)) {
			if(!empty($api_url) && !empty($api_key)) {
				$class_name = __CLASS__;
				self::$_instance = new $class_name($api_url, $api_key);
			} else {
				throw new API_Client_Exception('Please provide API key!');
			}
		}
		
		return self::$_instance;
	}
	
	public function search($params = array()) {
	
		if(!isset($params['name']) || empty($params['name'])) throw new API_Client_Exception('At least "name" must be passed as part of $params array in ' . __FUNCTION__ . '()!');
	
		$_params = array(
			'method' => __FUNCTION__,
			'name' => $params['name'],
			'city' => (isset($params['city']) && !empty($params['city'])) ? $params['city'] : '',
			'zip' =>  (isset($params['zip'])  && !empty($params['zip']))  ? $params['zip'] : '',
		);
		
		$result = $this->_request('post', $_params);
	
		return $this->_handle_result($result);
	
	}
	
	public function buy_result($dob_ids) {
		
		if(isset($dob_ids) && !empty($dob_ids)) {
		
			if(!is_array($dob_ids)) $dob_ids = array($dob_ids); // if $dob_ids was passed as string, not array
			
			$_params = array(
				'method' => __FUNCTION__,
				'dob_ids' => $dob_ids
			);
			
			$result = $this->_request('post', $_params);
			
			return $this->_handle_result($result);
		} else {
			throw new API_Client_Exception('Please specify IDs to buy!');
		}
		
	}

	public function get_available_funds() {
		$_params = array(
			'method' => __FUNCTION__,
		);
		
		$result = $this->_request('post', $_params);
		
		return $this->_handle_result($result);
	}

	public function get_dob_price() {
		$_params = array(
			'method' => __FUNCTION__,
		);
		
		$result = $this->_request('post', $_params);
		
		return $this->_handle_result($result);
	}
	
	private function _handle_result($data) {
		if(!empty($data)) {
			$data = json_decode($data, true);
			
			if(isset($data['error']) && !empty($data['error'])) {
				throw new API_Server_Exception($data['error']);
			} else {
				return $data;
			}
		}
		
		return array();
	}
	
	private function _request($type = '', $params = array()) {
	
		if(substr($this->_api_url, -1) == '/') $url = $this->_api_url . $this->_api_key; // if trailing slash is set
		else $url = $this->_api_url . '/' . $this->_api_key; // otherwise
		
		$_req_options = array(
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => __CLASS__ . ' ' . __FUNCTION__
		);
		
		$_handle = curl_init($url);
		
		switch(strtolower($type)) {
		
			case 'get':
			
			break;
			
			case 'post':
				$_req_options[CURLOPT_POST] = true;
				$_req_options[CURLOPT_POSTFIELDS] = http_build_query($params); // if params has array type, we can't pass it to CURLOPT_POSTFIELDS, we MUST convert it to valid query string
				
			break;
			
			default:
				throw new API_Client_Exception('Please specify request type ("get" or "post")!');
			break;
		
		}
		
		curl_setopt_array($_handle, $_req_options);
		
		$result = curl_exec($_handle);
		
		if($result === false) {
			throw new API_Client_Exception(curl_errno($_handle) . ': ' . curl_error($_handle));
		} else {
			return $result;
		}
	
	}

}

?>