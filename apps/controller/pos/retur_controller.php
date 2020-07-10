<?php
class ReturController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        //require_once(MODEL . "ar/invoice.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
        $this->trxMonth = $this->persistence->LoadState("acc_month");
        $this->trxYear = $this->persistence->LoadState("acc_year");
    }

    public function index() {
        $router = Router::GetInstance();
        $settings = array();
        $settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
         $settings["columns"][] = array("name" => "a.rtn_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.rtn_no", "display" => "No. Retur", "width" => 105);
        $settings["columns"][] = array("name" => "a.cust_name", "display" => "Customer/Member", "width" => 150);
        $settings["columns"][] = array("name" => "a.user_id", "display" => "User", "width" => 100);
        $settings["columns"][] = array("name" => "format(a.qty_retur,0)", "display" => "Qty Retur", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.rtn_amount,0)", "display" => "Nilai Retur", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.rtn_status = 0,'Draft',if(a.rtn_status = 1,'Process',if(a.rtn_status = 2,'Posted','Void')))", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "a.post_time", "display" => "Posted", "width" => 100);

        $settings["filters"][] = array("name" => "a.rtn_no", "display" => "No. Retur");
        $settings["filters"][] = array("name" => "a.rtn_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.cust_name", "display" => "Customer");
        $settings["filters"][] = array("name" => "a.user_id", "display" => "User");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Retur Penjualan";
            if ($acl->CheckUserAccess("pos.retur", "delete")) {
                $settings["actions"][] = array("Text" => "Void", "Url" => "pos.retur/void/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("pos.retur", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "pos.retur/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Retur terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data penjualan","Confirm" => "");
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "pos.retur/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
            }
        } else {
            $settings["from"] = "vw_pos_return_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.cabang_id = " . $this->userCabangId ." And year(a.rtn_date) = ".$this->trxYear." And month(a.rtn_date) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }
}


// End of File: invoice_controller.php
