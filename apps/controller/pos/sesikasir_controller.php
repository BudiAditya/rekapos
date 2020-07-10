<?php
class SesiKasirController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;
    private $userUid;

    protected function Initialize() {
        require_once(MODEL . "pos/sesikasir.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
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
        $settings["columns"][] = array("name" => "a.kasir", "display" => "Kasir", "width" => 60);
        $settings["columns"][] = array("name" => "a.session_no", "display" => "No. Sesi", "width" => 100);
        $settings["columns"][] = array("name" => "a.open_time", "display" => "Buka", "width" => 100);
        $settings["columns"][] = array("name" => "a.close_time", "display" => "Tutup", "width" => 100);
        $settings["columns"][] = array("name" => "format(a.tunai_open,0)", "display" => "+Awal", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.tunai_masuk_jual,0)", "display" => "+Tunai", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.tunai_keluar_retur,0)", "display" => "-Retur", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.kk_jual,0)", "display" => "Kartu Kredit", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.kd_jual,0)", "display" => "Kartu Debit", "width" => 70, "align" => "right");
        //$settings["columns"][] = array("name" => "format(a.poin_jual,0)", "display" => "Tukar Poin", "width" => 70, "align" => "right");
        //$settings["columns"][] = array("name" => "format(a.voucher_jual,0)", "display" => "Voucher", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format((a.tunai_open + a.tunai_masuk_jual) - a.tunai_keluar_retur ,0)", "display" => "Kas Akhir", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.total_trx,0)", "display" => "Total Trx", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.total_qty,0)", "display" => "Total QTY", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.return_trx,0)", "display" => "Retur Trx", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.return_qty,0)", "display" => "Retur QTY", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "a.status", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "a.post_time", "display" => "Posted", "width" => 80);

        $settings["filters"][] = array("name" => "a.open_time", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.kasir", "display" => "Kasir");
        $settings["filters"][] = array("name" => "a.status", "display" => "Status");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Sesi Kasir";
            if ($acl->CheckUserAccess("pos.sesikasir", "edit")) {
                $settings["actions"][] = array("Text" => "Approval", "Url" => "pos.sesikasir/approve/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Transaksi Kasir terlebih dahulu sebelum proses approval.\nPERHATIAN: Pilih tepat 1 data penjualan",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("pos.sesikasir", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "pos.sesikasir/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Transaksi Kasir terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data penjualan","Confirm" => "");
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "pos.sesikasir/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
            }
        } else {
            $settings["from"] = "vw_t_pos_session AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.session_status < 3 And a.cabang_id = " . $this->userCabangId ." And year(a.open_time) = ".$this->trxYear." And month(a.open_time) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

    public function approve ($id = 0){
        if ($id == null || $id == 0){
            $this->persistence->SaveState("error", "Belum ada data yang dipilih!");
            redirect_url("pos.sesikasir");
        }
        if (count($this->postData) > 0) {
            $kasKasir = $this->GetPostValue("TunaiKasir");
            $selisihKas = $this->GetPostValue("SelisihKas");
            $ket = $this->GetPostValue("Keterangan");
            $approvedById = $this->userUid;
            $sesi = new SesiKasir();
            $rs = $sesi->Approve($id,$kasKasir,$selisihKas,$ket,$approvedById);
            if ($rs > -1){
                $this->persistence->SaveState("info", sprintf("Proses Approval Sesi Kasir: %s berhasil!",$id));
                redirect_url("pos.sesikasir");
            } else {
                $this->Set("error", "Proses Approval gagal.. Message: " . $this->connector->GetErrorMessage());
            }
        }else {
            if ($id > 0) {
                $sesi = new SesiKasir();
                $master = $sesi->LoadSesiKasir($id);
            }
        }
        $this->Set("master",$master);
    }

    public function view ($id = 0){
        require_once (MODEL ."master/user_admin.php");
        if ($id > 0){
            $sesi = new SesiKasir();
            $master = $sesi->LoadSesiKasir($id);
        }else{
            $this->persistence->SaveState("error", "Belum ada data yang dipilih!");
            redirect_url("pos.sesikasir");
        }
        $this->Set("master",$master);
    }
}


// End of File: invoice_controller.php
