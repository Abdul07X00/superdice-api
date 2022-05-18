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

      public function load()
    {
      $board = $this->wallet_model->createBoard();
      if ($board) {
        $result = array(
            'success' => true,
            'data' => $board
        );
        echo json_encode($result);
        exit;
      } else {
              $result = array(
                  'success' => false,
                  'message' =>"Unexpected error occured, Server too busy"
              );
              echo json_encode($result);
              exit;
      }
    }

      public function deposit()
    {
      $this->checkRequiredFields(array('wallet_address','txn_token','network','currency','amount'));
      $this->wallet_model->existTransaction($this->jsonData('txn_token',true));
      $data['wallet_address'] = $this->jsonData('wallet_address',true);
      $data['txn_token'] = $this->jsonData('txn_token',true);
      $data['method'] = "deposit";
      $data['network'] = $this->jsonData('network',true);
      $data['currency'] = $this->jsonData('currency',true);
      $this->transaction($data, $this->jsonData('amount',true), "add");
    }

      public function bet()
    {
      $this->checkRequiredFields(array('wallet_address','board_id','network','currency','amount','side'));
      $lastWallet = $this->wallet_model->getLastWalletHistory($this->jsonData('wallet_address',true), $this->jsonData('network',true), $this->jsonData('currency',true));
      $existBoard = $this->wallet_model->existBoard($this->jsonData('board_id',true));
      if(@$lastWallet->new_amount >= $this->jsonData('amount',true) && $existBoard){
        $data['wallet_address'] = $this->jsonData('wallet_address',true);
        $data['txn_token'] = "";
        $data['method'] = "bet";
        $data['network'] = $this->jsonData('network',true);
        $data['currency'] = $this->jsonData('currency',true);
        $data['board_id'] = $this->jsonData('board_id',true);
        $transaction = $this->transaction($data, $this->jsonData('amount',true), "minus");
        if($transaction){
          $betArr['board_id'] = $this->jsonData('board_id',true);
          $betArr['wallet_address'] = $this->jsonData('wallet_address',true);
          $betArr['network'] = $this->jsonData('network',true);
          $betArr['currency'] = $this->jsonData('currency',true);
          $betArr['amount'] = $this->jsonData('amount',true);
          $betArr['side'] = $this->jsonData('side',true);
          $this->wallet_model->submitBet($betArr);
        }
      }else{
        $result = array(
          'success' => false,
          'message' =>"No balance or Board Doesn't exist"
        );
        echo json_encode($result);
        exit;
      }
    }

      public function rollDice()
    {
      $draw = $this->shuffleDice();
      $existBoard = $this->wallet_model->existBoard();
      if($existBoard){
        $existBet = $this->wallet_model->existBet($existBoard->id);
        if($existBet){
          $data["draw"] = $draw;
          $this->wallet_model->updateBoard($data);
        }
      }
    }

      public function transaction($data, $amount, $operation)
    {
      $lastWallet = $this->wallet_model->getLastWalletHistory($data['wallet_address'], $data['network'], $data['currency']);
      $data['last_amount'] = @$lastWallet->new_amount?@$lastWallet->new_amount:0;
      $data['new_amount'] = $operation == "add" ? (@$lastWallet->new_amount + $amount) : (@$lastWallet->new_amount - $amount);
      $transaction = $this->wallet_model->submitTransaction($data);
      return true;
    }


    
    
}