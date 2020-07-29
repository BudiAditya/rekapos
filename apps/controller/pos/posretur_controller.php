<?php
class PosReturController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "pos/posretur.php");
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
            if ($acl->CheckUserAccess("pos.posretur", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "pos.posretur/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Retur terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data penjualan","Confirm" => "");
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "pos.posretur/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
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

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sKondisi = $this->GetPostValue("Kondisi");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            // ambil data yang diperlukan
            $posretur = new PosRetur();
            if ($sJnsLaporan == 1){
                $reports = $posretur->Load4Reports($sCabangId,$sKondisi,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 2){
                $reports = $posretur->Load4ReportsDetail($sCabangId,$sKondisi,$sStartDate,$sEndDate);
            }else{
                $reports = $posretur->Load4ReportsRekapItem($sCabangId,$sKondisi,$sStartDate,$sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sKondisi = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sOutput = 0;
            $sJnsLaporan = 1;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        if ($this->userLevel > 3){
            $cabang = $loader->LoadByEntityId($this->userCompanyId);
        }else{
            $cabang = $loader->LoadById($this->userCabangId);
            $cabCode = $cabang->Kode;
            $cabName = $cabang->Cabang;
        }
        // kirim ke view
        $this->Set("cabangs", $cabang);
        $this->Set("CabangId",$sCabangId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Kondisi",$sKondisi);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
        $this->Set("JnsLaporan",$sJnsLaporan);
        //load mix cabangs
        $loader = new Cabang();
        $mixcabangs = $loader->LoadMixCabang($this->userCompanyId);
        $this->Set("mixcabangs", $mixcabangs);
    }

    public function view ($id = 0){
        if ($id > 0){
            $transaksi = new PosRetur();
            $master = $transaksi->LoadPosReturnMaster($id);
            $details = $transaksi->LoadPosReturnDetail($id);
        }else{
            $this->persistence->SaveState("error", "Belum ada data yang dipilih!");
            redirect_url("pos.posretur");
        }
        $this->Set("master",$master);
        $this->Set("details",$details);
    }
}


// End of File: invoice_controller.php
