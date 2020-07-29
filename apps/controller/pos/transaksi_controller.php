<?php
class TransaksiController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "pos/transaksi.php");
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
        //$settings["columns"][] = array("name" => "a.entity_cd", "display" => "Entity", "width" => 30);
        $settings["columns"][] = array("name" => "a.trx_time", "display" => "Waktu", "width" => 100);
        $settings["columns"][] = array("name" => "a.trx_no", "display" => "No. Transaksi", "width" => 105);
        $settings["columns"][] = array("name" => "a.cust_name", "display" => "Customer/Member", "width" => 150);
        $settings["columns"][] = array("name" => "a.kasir", "display" => "Kasir", "width" => 100);
        $settings["columns"][] = array("name" => "format(a.qty_keluar,0)", "display" => "QTY", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.jum_poin,0)", "display" => "Poin", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.total_transaksi,0)", "display" => "Nilai Penjualan", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.bayar_tunai,0)", "display" => "Bayar Tunai", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.bayar_kk,0)", "display" => "Kartu Kredit", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.bayar_kd,0)", "display" => "Kartu Debit", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.bayar_voucher,0)", "display" => "Bayar Voucher", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.bayar_poin,0)", "display" => "Bayar Poin", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.trx_status = 0,'Draft',if(a.trx_status = 1,'Paid',if(a.trx_status = 2,'Posted','Void')))", "display" => "Status", "width" => 40);
        $settings["columns"][] = array("name" => "a.post_time", "display" => "Posted Time", "width" => 100);

        $settings["filters"][] = array("name" => "a.trx_no", "display" => "No. Transaksi");
        $settings["filters"][] = array("name" => "a.tanggal", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.customer_name", "display" => "Nama Customer");
        $settings["filters"][] = array("name" => "a.kasir", "display" => "Kasir");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 1;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Transaksi Penjualan";
            //if ($acl->CheckUserAccess("pos.transaksi", "edit")) {
            //    $settings["actions"][] = array("Text" => "Retur", "Url" => "pos.transaksi/posretur/%s", "Class" => "bt_edit", "ReqId" => 1,
            //        "Error" => "Maaf anda harus memilih Data Penjualan terlebih dahulu sebelum proses posretur.\nPERHATIAN: Pilih tepat 1 data penjualan",
            //        "Confirm" => "");
            //}
            //if ($acl->CheckUserAccess("pos.transaksi", "delete")) {
            //    $settings["actions"][] = array("Text" => "Void", "Url" => "pos.transaksi/void/%s", "Class" => "bt_delete", "ReqId" => 1);
            //}
            if ($acl->CheckUserAccess("pos.transaksi", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "pos.transaksi/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Penjualan terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data penjualan","Confirm" => "");
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "pos.transaksi/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
            }
        } else {
            $settings["from"] = "vw_pos_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.cabang_id = " . $this->userCabangId ." And year(a.tanggal) = ".$this->trxYear." And month(a.tanggal) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

    public function profit(){
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
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $trx = new Transaksi();
            if ($sJnsLaporan == 1){
                $reports = $trx->Load4ProfitTransaksi($this->userCompanyId,$sCabangId,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 2){
                $reports = $trx->Load4ProfitTanggal($this->userCompanyId,$sCabangId,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 3){
                $reports = $trx->Load4ProfitBulan($this->userCompanyId,$sCabangId,$sStartDate,$sEndDate);
            }else{
                $reports = $trx->Load4ProfitItem($this->userCompanyId,$sCabangId,$sStartDate,$sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sJnsLaporan = 1;
            $sOutput = 0;
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
        $this->Set("Output",$sOutput);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("Reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
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
            $sStatus = $this->GetPostValue("Status");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $trx = new Transaksi();
            if ($sJnsLaporan == 1){
                $reports = $trx->Load4Reports($sCabangId,$sStatus,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 2) {
                $reports = $trx->Load4ReportsDetail($sCabangId, $sStatus, $sStartDate, $sEndDate);
            }elseif ($sJnsLaporan == 3) {
                $reports = $trx->Load4ReportsRekapItem($sCabangId,$sStatus,$sStartDate,$sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sStatus = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sJnsLaporan = 1;
            $sOutput = 0;
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
            $cab = new Cabang($this->userCabangId);
            $cabCode = $cab->Kode;
            $cabName = $cab->Cabang;
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
        $this->Set("Status",$sStatus);
        $this->Set("Output",$sOutput);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("Reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
        //load cabang mix
        $loader = new Cabang();
        $mixcabangs = $loader->LoadMixCabang($this->userCompanyId);
        $this->Set("mixcabangs", $mixcabangs);
    }

    public function view ($id = 0){
        if ($id > 0){
            $transaksi = new Transaksi();
            $master = $transaksi->LoadPosMaster($id);
            $details = $transaksi->LoadPosDetail($id);
        }else{
            $this->persistence->SaveState("error", "Belum ada data yang dipilih!");
            redirect_url("pos.transaksi");
        }
        $this->Set("master",$master);
        $this->Set("details",$details);
    }
}


// End of File: invoice_controller.php
