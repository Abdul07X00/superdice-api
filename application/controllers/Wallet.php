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

        public function index()
    {
        $data = array(
            'page' => 'Home',
            'title' => 'Home',
            'page_name' => 'home'
        );
        $this->display($data);
    }

      public function walletLoad()
    {
      $this->checkRequiredFields(array('wallet_address','network'));
      $wallets = $this->wallet_model->getWallets($this->jsonData('wallet_address',true), $this->jsonData('network',true));
      if ($wallets) {
        // Register Bonus Amount 10$
        $exist = $this->wallet_model->existTransaction($this->jsonData('wallet_address',true));
        if(!$exist){
          $this->transaction($this->jsonData('wallet_address',true),$this->jsonData('wallet_address',true),"deposit", 0,0, "ETHEREUM", "USDT",20, "add");
        }
        $result = array(
            'success' => true,
            'data' => $wallets
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
      $this->getTransactionStatus($this->jsonData('network',true), $this->jsonData('txn_token',true));
      $exist = $this->wallet_model->existTransaction($this->jsonData('txn_token',true));
      if($exist){
        $result = array(
          'success' => false,
          'message' =>"invalid transaction"
        );
        echo json_encode($result);
        exit;
      }
      $data['wallet_address'] = $this->jsonData('wallet_address',true);
      $data['txn_token'] = $this->jsonData('txn_token',true);
      $data['method'] = "deposit";
      $data['network'] = $this->jsonData('network',true);
      $data['currency'] = $this->jsonData('currency',true);
      $transaction = $this->transaction($this->jsonData('wallet_address',true),$this->jsonData('txn_token',true),"deposit", 0,0, $this->jsonData('network',true), $this->jsonData('currency',true),$this->jsonData('amount',true), "add");
      $result = array(
        'success' => true,
        'message' =>"Transaction has been successful"
      );
      echo json_encode($result);
      exit;
    }

      public function withdraw()
    {
      $this->checkRequiredFields(array('wallet_address','network','currency','amount'));
      $data['wallet_address'] = $this->jsonData('wallet_address',true);
      $data['txn_token'] = "withdraw_txn";
      $data['method'] = "withdraw";
      $data['network'] = $this->jsonData('network',true);
      $data['currency'] = $this->jsonData('currency',true);
      $transaction = $this->transaction($this->jsonData('wallet_address',true),"withdraw_txn","withdraw", 0,0, $this->jsonData('network',true), $this->jsonData('currency',true),$this->jsonData('amount',true), "minus");
      $result = array(
        'success' => true,
        'message' =>"Withdrawal has been successful"
      );
      echo json_encode($result);
      exit;
    }

      public function withdrawRequest()
    {
      $this->checkRequiredFields(array('wallet_address','network','currency'));
      $data['status'] = 2;
      $this->wallet_model->updateTransaction($data, $this->jsonData('wallet_address',true), $this->jsonData('network',true), $this->jsonData('currency',true));
      $data['wallet_address'] = $this->jsonData('wallet_address',true);
      $data['network'] = $this->jsonData('network',true);
      $data['currency'] = $this->jsonData('currency',true);
      $data['amount'] = $this->jsonData('amount',true);
      $data['status'] = 1;
      $this->wallet_model->insertWithdrawRequest($data);
      $result = array(
        'success' => true,
        'message' =>"Withdraw request has been submitted, Within 30 min will process the request."
      );
      echo json_encode($result);
      exit;
    }

      public function loadTransactions()
    {
      $this->checkRequiredFields(array('wallet_address'));
      $notifications = $this->wallet_model->getTransactions($this->jsonData('wallet_address',true));
      if ($notifications) {
        $result = array(
            'success' => true,
            'data' => $notifications
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

      public function boardLoad()
    {
      $board_id = $this->jsonData('board_id',true);
      $wallet_address = $this->jsonData('wallet_address',true);
      $board = $this->wallet_model->createBoard($board_id, $wallet_address);
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

      public function bet()
    {
      $this->checkRequiredFields(array('wallet_address','board_id','network','currency','amount','side'));
      $lastWallet = $this->wallet_model->getLastWalletHistory($this->jsonData('wallet_address',true), $this->jsonData('network',true), $this->jsonData('currency',true));
      $existBoard = $this->wallet_model->existBoard($this->jsonData('board_id',true));
      if(@$lastWallet->new_amount >= $this->jsonData('amount',true) && $existBoard){
        $transaction = $this->transaction($this->jsonData('wallet_address',true),"txn_token","bet", $this->jsonData('board_id',true), $this->jsonData('side',true), $this->jsonData('network',true), $this->jsonData('currency',true),$this->jsonData('amount',true), "minus");
        if($transaction){
          $betArr['board_id'] = $this->jsonData('board_id',true);
          $betArr['wallet_address'] = $this->jsonData('wallet_address',true);
          $betArr['network'] = $this->jsonData('network',true);
          $betArr['currency'] = $this->jsonData('currency',true);
          $betArr['amount'] = $this->jsonData('amount',true);
          $betArr['side'] = $this->jsonData('side',true);
          $existSameBet = $this->wallet_model->existSameBet($betArr);
          if($existSameBet){
            $betArr['amount'] = $existSameBet->amount + $this->jsonData('amount',true);
            $this->wallet_model->updateBet($existSameBet->id, $betArr);
          }else{
            $this->wallet_model->submitBet($betArr);
          }
          $result = array(
            'success' => true,
            'message' =>"Successfully placed bet"
          );
          echo json_encode($result);
          exit;
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
      $this->checkRequiredFields(array('board_id'));
      $drawn = $this->shuffleDice();
      $existBoard = $this->wallet_model->getBoard($this->jsonData('board_id',true));
      if($existBoard){
        if(!$existBoard->drawn){
          $data["drawn"] = json_encode($drawn);
          $this->wallet_model->updateBoard($data, $this->jsonData('board_id',true));
          $existBets = $this->wallet_model->existBets($this->jsonData('board_id',true));
          if($existBets){
            foreach($existBets as $bet){
              if(in_array($bet->side, $drawn))
                {
                  $timesDraw = $this->numberOfExistDrawn($bet->side, $drawn);
                  $draw_amount = ($bet->amount * $timesDraw) + $bet->amount;
                  $transaction = $this->transaction($bet->wallet_address,"txn_token","earned", $this->jsonData('board_id',true), $bet->side, $bet->network, $bet->currency, $draw_amount, "add");
                }
            }
          }
        }
        $board = $this->wallet_model->getBoard($this->jsonData('board_id',true));
        if($this->jsonData('wallet_address',true)){
          $board->bets = $this->wallet_model->getUserBoardbets($this->jsonData('board_id',true), $this->jsonData('wallet_address',true));
        }
        $board->drawn = json_decode($board->drawn);
        $result = array(
          'success' => true,
          'data' => $board
        );
        echo json_encode($result);
        exit;
      }else{
        $result = array(
          'success' => false,
          'message' => "Board Expired"
        );
        echo json_encode($result);
        exit;
      }
    }

      public function numberOfExistDrawn($side, $drawn)
    {
          $i = 0;
          foreach($drawn as $row){
            if($row == $side) $i++;
          }
          return $i;
    }

      public function transaction($wallet_address, $txn_token, $method, $board_id, $side, $network, $currency, $amount, $operation)
    {
      $lastWallet = $this->wallet_model->getLastWalletHistory($wallet_address, $network, $currency);
      if($operation == "minus" && $lastWallet->new_amount < $amount){
        $result = array(
          'success' => false,
          'message' => "Insufficient balance"
        );
        echo json_encode($result);
        exit;
      }
      $data['wallet_address'] = $wallet_address;
      $data['txn_token'] = $txn_token;
      $data['method'] = $method;
      $data['board_id'] = $board_id;
      $data['side'] = $side;
      $data['network'] = $network;
      $data['currency'] = $currency;
      $data['last_amount'] = @$lastWallet->new_amount?@$lastWallet->new_amount:0;
      $data['new_amount'] = $operation == "add" ? (@$lastWallet->new_amount + $amount) : (@$lastWallet->new_amount - $amount);
      $transaction = $this->wallet_model->submitTransaction($data);
      return true;
    }


    
    
}