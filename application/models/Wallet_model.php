<?php if(!defined('BASEPATH')){exit('No direct script access allowed');}
    class Wallet_model extends CI_Model{

            public function submitWhitelist($data)
        {
            $res = $this->db->insert('tbl_whitelists',$data);
            if($res) {
                return $this->db->insert_id();
            }else{
                return false;
            }
        }

            public function getWhitelistRecords($server_type)
        {
            $total = $this->db->select('count(*) as total')->from("tbl_whitelists")->where(array("server_type"=> $server_type))->get()->row()->total;
            return $total;
        }

            public function existTransaction($transaction_token)
        {
            $res = $this->db->select('*')->from("tbl_whitelists")->where(array("transaction_token"=> $transaction_token))->get()->row();
            if($res){
                $result = array(
                    'success' => false,
                    'message' =>"Transaction was succesfull"
                );
                echo json_encode($result);
                exit;
            }
            return;
        }

            public function checkMyLastOrder($wallet_address, $type, $server_type, $ip_address)
        {
            $now = new DateTime(date("Y-m-d H:i:s"));
            $finished_hours = 0;
            $remaining_hours = 0;
            $result = $this->db->select('*')->from("tbl_whitelists")->where(array("wallet_address"=> $wallet_address, "type"=> $type, "server_type"=> $server_type, "ip_address"=> $ip_address))->get()->result();
            if($result){
                foreach($result as $row){
                    $pastDate = new DateTime($row->created_at);
                    $diff = $now->diff($pastDate);
                    $hours = $diff->h;
                    $finished_hours = $hours + ($diff->days*24);
                    $remaining_hours = $row->hours - $finished_hours;
                }
            }
            return $remaining_hours - $finished_hours;
        }


        
    }
    ?>