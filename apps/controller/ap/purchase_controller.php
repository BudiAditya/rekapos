<?php
class PurchaseController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "ap/purchase.php");
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
        $settings["columns"][] = array("name" => "a.cabang_code", "display" => "Cabang", "width" => 50);
        $settings["columns"][] = array("name" => "a.grn_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.grn_no", "display" => "No. Purchase", "width" => 80);
        $settings["columns"][] = array("name" => "a.supplier_name", "display" => "Nama Supplier", "width" => 150);
        $settings["columns"][] = array("name" => "a.grn_descs", "display" => "Keterangan", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.payment_type = 0,'Cash','Credit')", "display" => "Cara Bayar", "width" => 50);
        $settings["columns"][] = array("name" => "a.due_date", "display" => "JTP", "width" => 60);
        $settings["columns"][] = array("name" => "format(a.base_amount,0)", "display" => "Nilai Pembelian", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.return_amount,0)", "display" => "Retur", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.disc1_amount + a.disc2_amount,0)", "display" => "Diskon", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.paid_amount,0)", "display" => "Terbayar", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.balance_amount,0)", "display" => "OutStanding", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "a.admin_name", "display" => "Admin", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.grn_status = 0,'Draft',if(a.grn_status = 1,'Posted',if(a.grn_status = 2,'Approved','Void')))", "display" => "Status", "width" => 50);

        $settings["filters"][] = array("name" => "a.grn_no", "display" => "No. Purchase");
        $settings["filters"][] = array("name" => "a.grn_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.supplier_name", "display" => "Nama Supplier");
        $settings["filters"][] = array("name" => "if(a.grn_status = 0,'Draft',if(a.grn_status = 1,'Posted',if(a.grn_status = 2,'Approved','Void')))", "display" => "Status");
        $settings["filters"][] = array("name" => "a.cabang_code", "display" => "Kode Cabang");

        $settings["def_filter"] = 0;
        $settings["def_purchase"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Pembelian Barang";

            if ($acl->CheckUserAccess("ap.purchase", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "ap.purchase/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("ap.purchase", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "ap.purchase/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Purchase terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data rekonsil",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("ap.purchase", "delete")) {
                $settings["actions"][] = array("Text" => "Void", "Url" => "ap.purchase/void/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("ap.purchase", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "ap.purchase/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Purchase terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data rekonsil","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ap.purchase", "print")) {
                $settings["actions"][] = array("Text" => "Print Bukti", "Url" => "ap.purchase/grn_print","Class" => "bt_pdf", "ReqId" => 2, "Confirm" => "Cetak Bukti Pembelian yang dipilih?");
            }

            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ap.purchase", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "ap.purchase/report", "Class" => "bt_report", "ReqId" => 0);
            }

            if ($acl->CheckUserAccess("ap.purchase", "approve")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Approve Pembelian", "Url" => "ap.purchase/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Pembelian terlebih dahulu sebelum proses approval.",
                    "Confirm" => "Apakah anda menyetujui data pembelian yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
                $settings["actions"][] = array("Text" => "Batal Approve", "Url" => "ap.purchase/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Pembelian terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Apakah anda mau membatalkan approval data pembelian yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }

        } else {
            $settings["from"] = "vw_ap_purchase_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId ." And year(a.grn_date) = ".$this->trxYear;//." And month(a.grn_date) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	/* Untuk entry data estimasi perbaikan dan penggantian spare part */
	public function add() {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/warehouse.php");
        $loader = null;
        $log = new UserAdmin();
		$purchase = new Purchase();
        $purchase->CabangId = $this->userCabangId;
        if (count($this->postData) > 0) {
			$purchase->CabangId = $this->GetPostValue("CabangId");
            $purchase->GudangId = $this->GetPostValue("GudangId");
			$purchase->GrnDate = $this->GetPostValue("GrnDate");
            $purchase->ReceiptDate = $this->GetPostValue("ReceiptDate");
            $purchase->GrnNo = $this->GetPostValue("GrnNo");
            $purchase->GrnDescs = $this->GetPostValue("GrnDescs");
            $purchase->SupplierId = $this->GetPostValue("SupplierId");
            $purchase->SalesName = $this->GetPostValue("SalesName");
            $purchase->ExPoNo = $this->GetPostValue("ExPoNo");
            if ($this->GetPostValue("GrnStatus") == null || $this->GetPostValue("GrnStatus") == 0){
                $purchase->GrnStatus = 1;
            }else{
                $purchase->GrnStatus = $this->GetPostValue("GrnStatus");
            }
            $purchase->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if($this->GetPostValue("PaymentType") == null){
                $purchase->PaymentType = 0;
            }else{
                $purchase->PaymentType = $this->GetPostValue("PaymentType");
            }
            if($this->GetPostValue("CreditTerms") == null){
                $purchase->CreditTerms = 0;
            }else{
                $purchase->CreditTerms = $this->GetPostValue("CreditTerms");
            }
            $purchase->BaseAmount = 0;
            $purchase->Disc1Pct = 0;
            $purchase->Disc1Amount = 0;
            $purchase->Disc2Pct = 0;
            $purchase->Disc2Amount = 0;
            $purchase->TaxPct = 10;
            $purchase->TaxAmount = 0;
            $purchase->OtherCosts = '-';
            $purchase->OtherCostsAmount = 0;
            $purchase->PaidAmount = 0;
            if ($this->ValidateMaster($purchase)) {
                if ($purchase->GrnNo == null || $purchase->GrnNo == "-" || $purchase->GrnNo == ""){
                    $purchase->GrnNo = $purchase->GetGrnDocNo();
                }
                $rs = $purchase->Insert();
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->Set("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Add New Purchase',$purchase->GrnNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Add New Purchase',$purchase->GrnNo,'Success');

                    redirect_url("ap.purchase/edit/".$purchase->Id);
                }
			}
		}
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        if ($cabang->CabType == 2){
            $this->persistence->SaveState("error", "Maaf Cabang %s dalam mode Gudang, tidak boleh digunakan untuk transaksi!",$cabang->Kode);
            redirect_url("ap.purchase");
        }
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $loader = new Warehouse();
        $gudangs = $loader->LoadByCabangId($this->userCabangId);
        //kirim ke view
        $this->Set("gudangs", $gudangs);
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("purchase", $purchase);
	}

	private function ValidateMaster(Purchase $purchase) {
        if ($purchase->SupplierId == 0 || $purchase->SupplierId == null || $purchase->SupplierId == ''){
            $this->Set("error", "Supplier tidak boleh kosong!");
            return false;
        }
        if ($purchase->PaymentType == 1 && $purchase->CreditTerms == 0){
            $this->Set("error", "Lama kredit belum diisi!");
            return false;
        }
        if ($purchase->GudangId == 0 || $purchase->GudangId == ""){
            $this->Set("error", "Gudang tujuan belum dipilih!");
            return false;
        }
		return true;
	}

    public function edit($purchaseId = null) {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/warehouse.php");
        require_once(MODEL . "master/tax.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $log = new UserAdmin();
        $purchase = new Purchase();
        if (count($this->postData) > 0) {
            $purchase->Id = $purchaseId;
            $purchase->CabangId = $this->GetPostValue("CabangId");
            $purchase->GudangId = $this->GetPostValue("GudangId");
            $purchase->GrnDate = $this->GetPostValue("GrnDate");
            $purchase->ReceiptDate = $this->GetPostValue("ReceiptDate");
            $purchase->GrnNo = $this->GetPostValue("GrnNo");
            $purchase->GrnDescs = $this->GetPostValue("GrnDescs");
            $purchase->SupplierId = $this->GetPostValue("SupplierId");
            $purchase->SalesName = $this->GetPostValue("SalesName");
            if ($this->GetPostValue("GrnStatus") == null || $this->GetPostValue("GrnStatus") == 0){
                $purchase->GrnStatus = 1;
            }else{
                $purchase->GrnStatus = $this->GetPostValue("GrnStatus");
            }
            $purchase->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if($this->GetPostValue("PaymentType") == null){
                $purchase->PaymentType = 0;
            }else{
                $purchase->PaymentType = $this->GetPostValue("PaymentType");
            }
            if($this->GetPostValue("CreditTerms") == null){
                $purchase->CreditTerms = 0;
            }else{
                $purchase->CreditTerms = $this->GetPostValue("CreditTerms");
            }
            $purchase->Disc1Pct = $this->GetPostValue("Disc1Pct");
            $purchase->TaxPct = $this->GetPostValue("TaxPct");
            $purchase->OtherCosts = $this->GetPostValue("OtherCosts");
            $purchase->OtherCostsAmount = str_replace(",","",$this->GetPostValue("OtherCostsAmount"));
            if ($this->ValidateMaster($purchase)) {
                $rs = $purchase->Update($purchase->Id);
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->persistence->SaveState("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Update Purchase',$purchase->GrnNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Update Purchase',$purchase->GrnNo,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Purchase/Nota No.: '%s' Tanggal: %s telah berhasil diubah..", $purchase->GrnNo, $purchase->GrnDate));
                    redirect_url("ap.purchase/edit/".$purchase->Id);
                }
            }
        }else{
            $purchase = $purchase->LoadById($purchaseId);
            if($purchase == null){
               $this->persistence->SaveState("error", "Maaf Data Purchase dimaksud tidak ada pada database. Mungkin sudah dihapus!");
               redirect_url("ap.purchase");
            }
            if($purchase->GrnStatus == 2){
                $this->persistence->SaveState("error", sprintf("Maaf Data Purchase No. %s sudah berstatus -TERBAYAR-",$purchase->GrnNo));
                redirect_url("ap.purchase");
            }
            if($purchase->GrnStatus == 3){
                $this->persistence->SaveState("error", sprintf("Maaf Data Purchase No. %s sudah berstatus -VOID-",$purchase->GrnNo));
                redirect_url("ap.purchase/view/".$purchaseId);
            }
            if ($purchase->CreatebyId <> AclManager::GetInstance()->GetCurrentUser()->Id && $this->userLevel == 1){
                $this->persistence->SaveState("error", sprintf("Maaf Anda tidak boleh mengubah data ini!",$purchase->GrnNo));
                redirect_url("ap.purchase");
            }
        }
        // load details
        $purchase->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        if ($cabang->CabType == 2){
            $this->persistence->SaveState("error", "Maaf Cabang %s dalam mode Gudang, tidak boleh digunakan untuk transaksi!",$cabang->Kode);
            redirect_url("ap.purchase");
        }
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $loader = new Warehouse();
        $gudangs = $loader->LoadByCabangId($this->userCabangId);
        $loader = new Tax();
        $taxs = $loader->LoadAll();
        //kirim ke view
        $this->Set("taxs", $taxs);
        $this->Set("gudangs", $gudangs);
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("purchase", $purchase);
        $this->Set("acl", $acl);
        $this->Set("itemsCount", $this->GrnItemsCount($purchaseId));
    }

	public function view($purchaseId = null) {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/warehouse.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $purchase = new Purchase();
        $purchase = $purchase->LoadById($purchaseId);
        if($purchase == null){
            $this->persistence->SaveState("error", "Maaf Data Purchase dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ap.purchase");
        }
        // load details
        $purchase->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $loader = new Warehouse();
        $gudangs = $loader->LoadByCabangId($this->userCabangId);
        //kirim ke view
        $this->Set("gudangs", $gudangs);
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("purchase", $purchase);
        $this->Set("acl", $acl);
	}

    public function delete($purchaseId) {
        // Cek datanya
        $ExPoNo = null;
        $log = new UserAdmin();
        $purchase = new Purchase();
        $purchase = $purchase->FindById($purchaseId);
        if($purchase == null){
            $this->Set("error", "Maaf Data Purchase dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ap.purchase");
        }
        // periksa status po
        $ExPoNo = $purchase->ExPoNo;
        if($purchase->GrnStatus < 2){
            $purchase->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($purchase->Delete($purchaseId,$ExPoNo) == 1) {
                $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Delete Purchase',$purchase->GrnNo,'Success');
                $this->persistence->SaveState("info", sprintf("Data Purchase No: %s sudah berhasil dihapus", $purchase->GrnNo));
            }else{
                $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Delete Purchase',$purchase->GrnNo,'Failed');
                $this->persistence->SaveState("error", sprintf("Maaf, Data Purchase No: %s gagal dihapus", $purchase->GrnNo));
            }
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf, Data Purchase No: %s sudah berstatus -TERBAYAR-", $purchase->GrnNo));
        }
        redirect_url("ap.purchase");
    }

    public function void($purchaseId) {
        // Cek datanya
        $ExPoNo = null;
        $log = new UserAdmin();
        $purchase = new Purchase();
        $purchase = $purchase->FindById($purchaseId);
        if($purchase == null){
            $this->Set("error", "Maaf Data Purchase dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ap.purchase");
        }
        if($purchase->GrnStatus == 3){
            $this->persistence->SaveState("error", sprintf("Maaf Data Purchase No. %s sudah berstatus -VOID-",$purchase->GrnNo));
            redirect_url("ap.purchase");
        }
        $ExPoNo = $purchase->ExPoNo;
        // periksa status po
        if($purchase->GrnStatus < 2){
            $purchase->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($purchase->Void($purchaseId,$ExPoNo) == 1) {
                $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Delete Purchase',$purchase->GrnNo,'Success');
                $this->persistence->SaveState("info", sprintf("Data Purchase No: %s sudah berhasil batalkan", $purchase->GrnNo));
            }else{
                $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Delete Purchase',$purchase->GrnNo,'Failed');
                $this->persistence->SaveState("error", sprintf("Maaf, Data Purchase No: %s gagal dibatalkan", $purchase->GrnNo));
            }
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf, Data Purchase No: %s sudah berstatus -TERBAYAR-", $purchase->GrnNo));
        }
        redirect_url("ap.purchase");
    }

	public function add_detail($purchaseId = null) {
        require_once(MODEL . "master/items.php");
        $log = new UserAdmin();
        $purchase = new Purchase($purchaseId);
        $purchasedetail = new PurchaseDetail();
        $purchasedetail->GrnId = $purchaseId;
        $purchasedetail->GrnNo = $purchase->GrnNo;
        $purchasedetail->CabangId = $purchase->CabangId;
        $items = null;
        if (count($this->postData) > 0) {
            $purchasedetail->ItemId = $this->GetPostValue("aItemId");
            $purchasedetail->ExPoNo = $this->GetPostValue("aExPoNo");
            $purchasedetail->PurchaseQty = $this->GetPostValue("aQty");
            $purchasedetail->ReturnQty = 0;
            $purchasedetail->Price = $this->GetPostValue("aPrice");
            if ($this->GetPostValue("aDiscFormula") == ''){
                $purchasedetail->DiscFormula = 0;
            }else{
                $purchasedetail->DiscFormula = $this->GetPostValue("aDiscFormula");
            }
            $purchasedetail->DiscAmount = $this->GetPostValue("aDiscAmount");
            $purchasedetail->SubTotal = $this->GetPostValue("aSubTotal");
            $purchasedetail->IsFree = $this->GetPostValue("aIsFree");
            $purchasedetail->TaxCode = $this->GetPostValue("aTaxCode");
            $purchasedetail->TaxPct = $this->GetPostValue("aTaxPct");
            $purchasedetail->TaxAmount = $this->GetPostValue("aTaxAmount");
            $purchasedetail->BatchNo = $this->GetPostValue("aBatchNo");
            $purchasedetail->ExpDate = $this->GetPostValue("aExpDate");
            $purchasedetail->GudangId = $purchase->GudangId;
            $items = new Items($purchasedetail->ItemId);
            if ($items != null){
                $purchasedetail->ItemCode = $items->Bkode;
                $purchasedetail->ItemDescs = $items->Bnama;
                $purchasedetail->Lqty = 0;
                $purchasedetail->Sqty = 0;
                // insert ke table
                $rs = $purchasedetail->Insert()== 1;
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Add Purchase detail -> Item Code: '.$purchasedetail->ItemCode.' = '.$purchasedetail->PurchaseQty,$purchase->GrnNo,'Success');
                    echo json_encode(array());
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Add Purchase detail -> Item Code: '.$purchasedetail->ItemCode.' = '.$purchasedetail->PurchaseQty,$purchase->GrnNo,'Failed');
                    echo json_encode(array('errorMsg'=>'Some errors occured.'));
                }
            }else{
                echo json_encode(array('errorMsg'=>'Data barang tidak ditemukan!'));
            }
        }
	}

    public function edit_detail($purchaseId = null) {
        require_once(MODEL . "master/items.php");
        $log = new UserAdmin();
        $purchase = new Purchase($purchaseId);
        $purchasedetail = new PurchaseDetail();
        $purchasedetail->GrnId = $purchaseId;
        $purchasedetail->GrnNo = $purchase->GrnNo;
        $purchasedetail->CabangId = $purchase->CabangId;
        $items = null;
        if (count($this->postData) > 0) {
            $purchasedetail->Id = $this->GetPostValue("aId");
            $purchasedetail->ExPoNo = $this->GetPostValue("aExPoNo");
            $purchasedetail->ItemId = $this->GetPostValue("aItemId");
            $purchasedetail->PurchaseQty = $this->GetPostValue("aQty");
            $purchasedetail->ReturnQty = $this->GetPostValue("rQty");
            $purchasedetail->Price = $this->GetPostValue("aPrice");
            if ($this->GetPostValue("aDiscFormula") == ''){
                $purchasedetail->DiscFormula = 0;
            }else{
                $purchasedetail->DiscFormula = $this->GetPostValue("aDiscFormula");
            }
            $purchasedetail->DiscAmount = $this->GetPostValue("aDiscAmount");
            $purchasedetail->SubTotal = $this->GetPostValue("aSubTotal");
            $purchasedetail->IsFree = $this->GetPostValue("aIsFree");
            $purchasedetail->TaxCode = $this->GetPostValue("aTaxCode");
            $purchasedetail->TaxPct = $this->GetPostValue("aTaxPct");
            $purchasedetail->TaxAmount = $this->GetPostValue("aTaxAmount");
            $purchasedetail->BatchNo = $this->GetPostValue("aBatchNo");
            $purchasedetail->ExpDate = $this->GetPostValue("aExpDate");
            $purchasedetail->GudangId = $purchase->GudangId;
            $items = new Items($purchasedetail->ItemId);
            if ($items != null){
                $purchasedetail->ItemCode = $items->Bkode;
                $purchasedetail->ItemDescs = $items->Bnama;
                // insert ke table
                $rs = $purchasedetail->Update($purchasedetail->Id);
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Update Purchase detail -> Item Code: '.$purchasedetail->ItemCode.' = '.$purchasedetail->PurchaseQty,$purchase->GrnNo,'Success');
                    echo json_encode(array());
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Update Purchase detail -> Item Code: '.$purchasedetail->ItemCode.' = '.$purchasedetail->PurchaseQty,$purchase->GrnNo,'Failed');
                    echo json_encode(array('errorMsg'=>'Some errors occured.'));
                }
            }else{
                echo json_encode(array('errorMsg'=>'Data barang tidak ditemukan!'));
            }
        }
    }

    public function delete_detail($id) {
        // Cek datanya
        $log = new UserAdmin();
        $purchasedetail = new PurchaseDetail();
        $purchasedetail = $purchasedetail->FindById($id);
        if ($purchasedetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($purchasedetail->Delete($id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Delete Purchase detail -> Item Code: '.$purchasedetail->ItemCode.' = '.$purchasedetail->PurchaseQty,$purchasedetail->GrnNo,'Success');
            printf("Data Detail Purchase ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Delete Purchase detail -> Item Code: '.$purchasedetail->ItemCode.' = '.$purchasedetail->PurchaseQty,$purchasedetail->GrnNo,'Failed');
            printf("Maaf, Data Detail Purchase ID: %d gagal dihapus!",$id);
        }
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sContactsId = $this->GetPostValue("ContactsId");
            $sStatus = $this->GetPostValue("Status");
            $sPaymentStatus = $this->GetPostValue("PaymentStatus");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $purchase = new Purchase();
            if ($sJnsLaporan == 1) {
                $reports = $purchase->Load4Reports($this->userCompanyId, $sCabangId, $sContactsId, $sStatus, $sPaymentStatus, $sStartDate, $sEndDate);
            }elseif ($sJnsLaporan == 2){
                $reports = $purchase->Load4ReportsDetail($this->userCompanyId, $sCabangId, $sContactsId, $sStatus, $sPaymentStatus, $sStartDate, $sEndDate);
            }else{
                $reports = $purchase->Load4ReportsRekapItem($this->userCompanyId, $sCabangId, $sContactsId, $sStatus, $sPaymentStatus, $sStartDate, $sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sContactsId = 0;
            $sStatus = -1;
            $sPaymentStatus = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sJnsLaporan = 1;
            $sOutput = 0;
            $reports = null;
        }
        $supplier = new Contacts();
        $supplier = $supplier->LoadByType(2);
        $company = new Company($this->userCompanyId);
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
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("suppliers",$supplier);
        $this->Set("CabangId",$sCabangId);
        $this->Set("ContactsId",$sContactsId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Status",$sStatus);
        $this->Set("PaymentStatus",$sPaymentStatus);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
        $this->Set("company_name", $company->CompanyName);
        //load mix cabangs
        $loader = new Cabang();
        $mixcabangs = $loader->LoadMixCabang($this->userCompanyId);
        $this->Set("mixcabangs", $mixcabangs);
    }

    public function getjson_grnlists($cabangId,$supplierId){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $purchases = new Purchase();
        $grnlists = $purchases->GetJSonGrns($cabangId,$supplierId,$filter);
        echo json_encode($grnlists);
    }

    public function getjson_grnitems($grnId = 0){
        $purchases = new Purchase();
        $itemlists = $purchases->GetJSonGrnItems($grnId);
        echo json_encode($itemlists);
    }

    public function GrnItemsCount($grnId){
        $purchases = new Purchase();
        $rows = $purchases->GetGrnItemCount($grnId);
        return $rows;
    }

    //proses cetak bukti pembelian
    public function grn_print($doctype = 'grn') {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Harap pilih data yang akan dicetak !");
            redirect_url("ap.purchase");
            return;
        }
        $report = array();
        foreach ($ids as $id) {
            $grn = new Purchase();
            $grn = $grn->LoadById($id);
            $grn->LoadDetails();
            $report[] = $grn;
        }

        $this->Set("doctype", $doctype);
        $this->Set("report", $report);
    }

    public function getitemprices_json($order="a.bnama"){
        require_once(MODEL . "master/items.php");
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new Items();
        $itemlists = $setprice->GetJSonItems($this->userCompanyId,$this->userCabangId,$filter,$order);
        echo json_encode($itemlists);
    }

    public function getitemprices_plain($cabangId,$bkode){
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $items Items  */
            $items = new Items();
            $items = $items->FindByKode($bkode);
            $hrg_beli = 0;
            $hrg_jual = 0;
            if ($items == null){
                $items = new Items();
                $items = $items->FindByBarCode($bkode);
            }
            if ($items != null){
                $hrg_beli = $items->Bhargabeli;
                $hrg_jual = $items->Bhargajual1;
                if($hrg_beli == null){
                    $hrg_beli = 0;
                }
                if($hrg_jual == null){
                    $hrg_jual = 0;
                }
                $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatkecil.'|'.$hrg_beli.'|'.$hrg_jual;
            }
        }
        print $ret;
    }

    public function approve() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di Approve !");
            redirect_url("ap.purchase");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $purchase = new Purchase();
            $log = new UserAdmin();
            $purchase = $purchase->FindById($id);
            /** @var $purchase Purchase */
            // process purchase
            if($purchase->GrnStatus == 1){
                $rs = $purchase->Approve($purchase->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Approve Pembelian',$purchase->GrnNo,'Success');
                    $infos[] = sprintf("Data Pembelian No: '%s' (%s) telah berhasil di-approve.", $purchase->GrnNo, $purchase->GrnDescs);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Approve Pembelian',$purchase->GrnNo,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses approve Data Pembelian: '%s'. Message: %s", $purchase->GrnNo, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Pembelian No.%s sudah berstatus -Approved- !",$purchase->GrnNo);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ap.purchase");
    }

    public function unapprove() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ap.purchase");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $purchase = new Purchase();
            $log = new UserAdmin();
            $purchase = $purchase->FindById($id);
            /** @var $purchase Purchase */
            // process invoice
            if($purchase->GrnStatus == 2){
                $rs = $purchase->Unapprove($purchase->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Un-approve Pembelian',$purchase->GrnNo,'Success');
                    $infos[] = sprintf("Approval Data Pembelian No: '%s' (%s) telah berhasil di-batalkan.", $purchase->GrnNo, $purchase->GrnDescs);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.purchase','Un-approve Pembelian',$purchase->GrnNo,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses pembatalan Data Pembelian: '%s'. Message: %s", $purchase->GrnNo, $this->connector->GetErrorMessage());
                }
            }else{
                if ($purchase->GrnStatus == 1){
                    $errors[] = sprintf("Data Pembelian No.%s masih berstatus -POSTED- !",$purchase->GrnNo);
                }else{
                    $errors[] = sprintf("Data Pembelian No.%s masih berstatus -DRAFT- !",$purchase->GrnNo);
                }
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ap.purchase");
    }

    public function getjson_polists($cabangId,$supplierId){
        require_once (MODEL . "ap/order.php");
        //$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $order = new Order();
        $polists = $order->GetActivePoList($cabangId,$supplierId);
        echo json_encode($polists);
    }

    public function getjson_poitems($poNo,$gdId){
        require_once (MODEL . "ap/order.php");
        //$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $order = new Order();
        $poitems = $order->GetItemPoItems($poNo,$gdId);
        echo json_encode($poitems);
    }

    public function update_diskon($purId,$p2P,$p2A){
        $pur = new Purchase();
        $pur = $pur->UpdateDiskon($purId,$p2P,$p2A);
        if ($pur > -1){
            print("OK");
        }else{
            print("ER");
        }
    }
}


// End of File: estimasi_controller.php
