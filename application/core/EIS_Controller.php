<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class EIS_Controller extends CI_Controller{

		public function __construct()
	{
		parent::__construct();
		if(!isset($_SESSION)) 
		{ 
				session_start(); 
		} 
		$this->ipAddress = $_SERVER['REMOTE_ADDR'];
		$json_obj = (object) array();
		$postBody = file_get_contents("php://input");
		$postBody = $this->cleanMe($postBody);
		$this->json_obj = json_decode($postBody);
		$http_origin = @$_SERVER['HTTP_ORIGIN'];
		if ($http_origin == "https://api.superdice.bet/" || $http_origin == "https://www.api.superdice.bet/")
		{  
			header("Access-Control-Allow-Origin: $http_origin");
		}
		header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
		header("Content-Security-Policy: default-src 'self'");
		header('X-Content-Type-Options: nosniff');
		header("Referrer-Policy: no-referrer");
		header('X-XSS-Protection: 1; mode=block');
		header('X-Frame-Options: DENY');
		header("Strict-Transport-Security: max-age=600");
	}

	/* clean raw data */
		public function cleanMe($input) 
	{
	    $input = htmlspecialchars($input, ENT_IGNORE, 'utf-8');
	    $input = strip_tags($input);
		$input = stripslashes($input);
		$input = str_replace("'", "''", $input);
	    return $input;
	}

	/* clean special charecter */
		public function cleanSpecialChar($input) 
	{
		$input = str_replace(' ', '-', $input); // Replaces all spaces with hyphens.
		$input = preg_replace('/[^A-Za-z0-9\-]/', '', $input); // Removes special chars.
	    return $input;
	}

	/* jsonData */
		public function jsonData($input='')
	{
		if ( isset( $this->json_obj->$input )) {
			return $this->json_obj->$input;
		} else {
			return '';
		}
	}

	/* Required Fields */
		public function checkRequiredFields($fields) 
	{
		foreach($fields as $field){
			if(@$this->json_obj->$field==''){
				$result = array(
					'success' => false,
					'message' => 'Missing param '.$field
					);
				echo json_encode($result);
				exit;
			}
		}
		return true;
	}

		public function display($data, $return = FALSE)
	{	
		$data['page']= $this->uri->segment(1);
		if(!isset($data['page_location'])){
			$page_path = '';
		} else {
			$page_path = $data['page_location'].'/';
		}
		$header = "";		
		$footer = "";	
		$body = $this->load->view($page_path.$data['page_name'], $data, TRUE);
		
		$output = $header . $body . $footer;
		
		if($return)
			return $output;
		else
			echo $output;
	}

		public function getRealIpAddr()
  {
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //Check ip from share internet
		{
				$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   // To check ip is pass from proxy
		{
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
				$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

		public function generateKey()
	{
		$chars = array(
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
			'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
		);
		shuffle($chars);
		$num_chars = count($chars) - 1;
		$token = '';
		for ($i = 0; $i < $num_chars; $i++){
			$token .= $chars[mt_rand(0, $num_chars)];
		}
		return $token;
	}

		public function shuffleDice()
	{
		$draw = [];
		for ($i = 0; $i < 3; $i++){
			array_push($draw, rand(1,6));
		}
		return $draw;
	}

		public function getTransactionStatus($network, $txn_token)
	{
		$url  = "";
		if($network == "ETHEREUM"){
			$url = 'https://api.etherscan.io/api?module=transaction&action=gettxreceiptstatus&txhash='.$txn_token.'&apikey=TWPQUQZBYWQI632GBJW32WE4NXZ925YKVF';
		}else if($network == "BINANCE"){
			$url = 'https://api.bscscan.com/api?module=transaction&action=gettxreceiptstatus&txhash='.$txn_token.'&apikey=SHAHPJYVJRZEE4FIW8AWJ8QR1QYZAXPTWK';
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
		$curl_response = curl_exec($curl);
		curl_close($curl);
		$decoded = json_decode($curl_response);
		if(@$decoded->result->status){
			return;
		}else{
			$result = array(
				'success' => false,
				'error' => $decoded,
				'message' =>"invalid transaction"
			);
			echo json_encode($result);
			exit;
		}
	}

	
}

/* End of file EIS_Controller.php */
/* Location: ./application/core/EIS_Controller.php */