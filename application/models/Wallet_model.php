<?php if(!defined('BASEPATH')){exit('No direct script access allowed');}
    class Wallet_model extends CI_Model{

            public function createBoard()
        {
            $board = $this->db->select('*')->from("tbl_boards")->order_by("id","desc")->get()->row();
            if(@$board->drawn){
                $data['drawn'] = "";
                $data['status'] = 1;
                $this->db->insert('tbl_boards', $data);
                $board_id = $this->db->insert_id();
                $board = $this->db->select('*')->from("tbl_boards")->where("id", $board_id)->get()->row();
            }
            if($board){
                $board->bets = $this->db->select('*')->from("tbl_board_bets")->where("board_id", $board->id)->get()->result();
            }
            return $board;
        }

            public function updateBoard($data)
        {
            
        }

            public function existBoard($board_id="")
        {
            if($board_id){
                $res = $this->db->select('*')->from("tbl_boards")->where(array("id"=> $board_id, "drawn"=> ""))->get()->row();
            }else{
                $res = $this->db->select('*')->from("tbl_boards")->where(array("drawn"=> ""))->order_by("id","desc")->get()->row();
            }
            return $res;
        }

            public function existBet($board_id)
        {
            $bets = $this->db->select('*')->from("tbl_board_bets")->where("board_id", $board_id)->get()->result();
            return $bets;
        }

            public function getLastWalletHistory($wallet_address, $network, $currency)
        {
            $transaction = $this->db->select('*')->from("tbl_transactions")->where(array("wallet_address"=> $wallet_address, "network"=> $network, "currency"=> $currency))->order_by("created_at", "desc")->get()->row();
            return $transaction;
        }

            public function existTransaction($txn_token)
        {
            if($txn_token){
                $res = $this->db->select('*')->from("tbl_transactions")->where(array("txn_token"=> $txn_token))->get()->row();
                if($res){
                    $result = array(
                        'success' => false,
                        'message' =>"txn_token not valid or exist"
                    );
                    echo json_encode($result);
                    exit;
                }
            }
            return;
        }

            public function submitTransaction($data)
        {
            $res = $this->db->insert('tbl_transactions',$data);
            if($res) {
                return $this->db->insert_id();
            }else{
                return false;
            }
        }

            public function submitBet($data)
        {
            $res = $this->db->insert('tbl_board_bets',$data);
            if($res) {
                return $this->db->insert_id();
            }else{
                return false;
            }
        }

        

        //     public function getWhitelistRecords($server_type)
        // {
        //     $total = $this->db->select('count(*) as total')->from("tbl_whitelists")->where(array("server_type"=> $server_type))->get()->row()->total;
        //     return $total;
        // }
        

        


        
    }
    ?>