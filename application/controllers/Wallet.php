<?php
class Wallet extends EIS_Controller{

        public function __construct()
    {
        parent::__construct();
        $this->load->model('Wallet_model','wallet_model');
        $this->load->helper(array('url','html','form'));
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
        header("Content-Type: text/html; charset=utf-8");
    }

      public function submitOrder()
    {
      $this->checkRequiredFields(array('wallet_address','transaction_token','type','server_type','ip_address'));
      if($this->jsonData('type',true) == "default"){
        $this->checkRequiredFields(array('plan_name'));
      }
      $this->order_model->existTransaction($this->jsonData('transaction_token',true));
      $data['wallet_address'] = $this->jsonData('wallet_address',true);
      $data['transaction_token'] = $this->jsonData('transaction_token',true);
      $data['type'] = $this->jsonData('type',true);
      $data['plan_name'] = $this->jsonData('plan_name',true);
      $data['server_type'] = $this->jsonData('server_type',true);
      $data['ip_address'] = $this->jsonData('ip_address',true);
      $data['hours'] = $this->getHours($data['type'], $data['plan_name']);
      $records = $this->order_model->getWhitelistRecords($data['server_type']);
      $server = $this->ipWhitelisting($data['wallet_address'], $data['transaction_token'], $data['type'], $data['plan_name'], $data['server_type'], $records, $data['ip_address'], $data['hours']);
      if ($server) {
              $result = array(
                  'success' => true,
                  'data' => $server,
                  'message' =>"Succesfully whitelisted"
              );
              echo json_encode($result);
              exit;
      } else {
              $result = array(
                  'success' => false,
                  'message' =>"Error in submit whitelist form"
              );
              echo json_encode($result);
              exit;
      }
    }

      public function getHours($type, $plan)
    {
      if($type == "default"){
        if($plan == "Daily"){
          return 24;
        }else if($plan == "Weekly"){
          return 168;
        }else if($plan == "Monthly"){
          return 720;
        }else if($plan == "3months"){
          return 2160;
        }else if($plan == "6months"){
          return 4320;
        }else if($plan == "Yearly"){
          return 8640;
        }
      }else if($type == "metamask"){
        if($plan == "Monthly"){
          return 720;
        }
      }
      return 0;
    }

      public function ipWhitelisting($wallet_address, $transaction_token, $type, $plan_name, $server_type, $records, $ip_address, $hours)
    {
      $now = new DateTime();
      if($type == "default"){
        if($server_type == "CRO"){
          if($records <= 100){
            $output = $this->sshConnect($wallet_address, $transaction_token, $type, $plan_name, $server_type, '5.9.22.103', 22, 'root', 'RentN0dePriv4teS3rv3r987123', $ip_address, $hours, 8545);
            return $output;
          }else if ($records > 100 && $records <= 200){
            return false;
          }else if ($records > 200 && $records <= 300){
            return false;
          }
        }else if($server_type == "BSC"){
          if($records <= 100){
            $output = $this->sshConnect($wallet_address, $transaction_token, $type, $plan_name, $server_type, '88.198.48.29', 22, 'root', 'RentN0dePriv4teS3rv3r987123', $ip_address, $hours, 8545);
            return $output;
          }else if ($records > 20 && $records <= 40){
            return false;
          }else if ($records > 40 && $records <= 60){
            return false;
          }
        }else if($server_type == "ETH"){
          if($records <= 100){
            $output = $this->sshConnect($wallet_address, $transaction_token, $type, $plan_name, $server_type, '142.132.154.225', 22, 'root', 'RentN0dePriv4teS3rv3r987123', $ip_address, $hours, 8545);
            return $output;
          }else if ($records > 100 && $records <= 200){
            return false;
          }else if ($records > 200 && $records <= 300){
            return false;
          }
        }
      }else if($type == "metamask"){
        if($server_type == "CRO"){
          if($records <= 200){
            $output = $this->sshConnect($wallet_address, $transaction_token, $type, $plan_name, $server_type, '5.9.22.103', 22, 'root', 'RentN0dePriv4teS3rv3r987123', $ip_address, $hours, 8000);
            return $output;
          }else if ($records > 200 && $records <= 400){
            return false;
          }else if ($records > 400 && $records <= 600){
            return false;
          }
        }else if($server_type == "BSC"){
          if($records <= 200){
            $output = $this->sshConnect($wallet_address, $transaction_token, $type, $plan_name, $server_type, '142.132.197.253', 22, 'root', 'RentN0dePriv4teS3rv3r987123', $ip_address, $hours, 8000);
            return $output;
          }else if ($records > 200 && $records <= 400){
            return false;
          }else if ($records > 400 && $records <= 600){
            return false;
          }
        }else if($server_type == "ETH"){
          if($records <= 200){
            $output = $this->sshConnect($wallet_address, $transaction_token, $type, $plan_name, $server_type, '142.132.206.202', 22, 'root', 'RentN0dePriv4teS3rv3r987123', $ip_address, $hours, 8000);
            return $output;
          }else if ($records > 200 && $records <= 400){
            return false;
          }else if ($records > 400 && $records <= 600){
            return false;
          }
        }
      }
    }


      public function sshConnect($wallet_address, $transaction_token, $type, $plan_name, $server_type, $server_ip, $port, $user, $password, $ip_address, $hours, $whitelist_port)
    {
      $connection = ssh2_connect($server_ip, $port);
      ssh2_auth_password($connection, $user, $password);
      if($type == "metamask"){
        $run = "./whitelist-metamask.sh ".$ip_address." ".$hours;
      }else{
        $run = "./whitelist.sh ".$ip_address." ".$hours;
      }
      $stream = ssh2_exec($connection, $run);
      $output['ws'] = "ws://".$server_ip.":".$whitelist_port;
      if($type == "metamask"){
        if($server_type == "CRO"){
          $output['http'] = CRO_URL;
        }else if($server_type == "BSC"){
          $output['http'] = BSC_URL;
        }else if($server_type == "ETH"){
          $output['http'] = ETH_URL;
        }
      }else{
        $output['http'] = "https://".$server_ip.":".$whitelist_port;
      }
      $remaining_hours = $this->order_model->checkMyLastOrder($wallet_address, $type, $server_type, $ip_address) + $hours;
      $output['valid_till'] = new DateTime('now +'.$remaining_hours.' hours');
      // Insert Records
      $insert['wallet_address'] = $wallet_address;
      $insert['transaction_token'] = $transaction_token;
      $insert['type'] = $type;
      $insert['plan_name'] = $plan_name;
      $insert['server_type'] = $server_type;
      $insert['ip_address'] = $ip_address;
      $insert['hours'] = $this->getHours($type, $plan_name);
      $insert['created_at'] = date("Y-m-d H:i:s");
      $this->order_model->submitWhitelist($insert);
      return $output;
    }

      public function removeWhitelist()
    {
      // $result = $this->db->select('*')->from("tbl_whitelists")->where(array("status"=> 1))->get()->result();
    }

    //   public function getDatetimeNow() 
    // {
    //   $tz_object = new DateTimeZone('Asia/Dubai');
    //   $datetime = new DateTime();
    //   $datetime->setTimezone($tz_object);
    //   return $datetime->format('Y\-m\-d\ h:i:s');
    // }

    
    
}