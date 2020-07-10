<?php
class InvoiceController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "ar/invoice.php");
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
        //$settings["columns"][] = array("name" => "a.cabang_code", "display" => "Cabang", "width" => 80);
        $settings["columns"][] = array("name" => "a.invoice_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.invoice_no", "display" => "No. Invoice", "width" => 80);
        $settings["columns"][] = array("name" => "a.customer_name", "display" => "Nama Customer", "width" => 150);
        $settings["columns"][] = array("name" => "a.sales_name", "display" => "Salesman", "width" => 100);
        $settings["columns"][] = array("name" => "a.invoice_descs", "display" => "Keterangan", "width" => 150);
        $settings["columns"][] = array("name" => "if(a.payment_type = 0,'Cash','Credit')", "display" => "Cara Bayar", "width" => 60);
        $settings["columns"][] = array("name" => "a.due_date", "display" => "JTP", "width" => 60);
        $settings["columns"][] = array("name" => "format(a.total_amount,0)", "display" => "Nilai Penjualan", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.return_amount,0)", "display" => "Retur", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.paid_amount,0)", "display" => "Terbayar", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.balance_amount,0)", "display" => "OutStanding", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "a.admin_name", "display" => "Admin", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.invoice_status = 0,'Draft',if(a.invoice_status = 1,'Posted',if(a.invoice_status = 2,'Approved','Void')))", "display" => "Status", "width" => 40);

        $settings["filters"][] = array("name" => "a.invoice_no", "display" => "No. Invoice");
        $settings["filters"][] = array("name" => "a.cabang_code", "display" => "Kode Cabang");
        $settings["filters"][] = array("name" => "a.invoice_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.customer_name", "display" => "Nama Customer");
        $settings["filters"][] = array("name" => "a.sales_name", "display" => "Nama Sales");
        $settings["filters"][] = array("name" => "a.admin_name", "display" => "Nama Admin");
        $settings["filters"][] = array("name" => "if(a.invoice_status = 0,'Draft',if(a.invoice_status = 1,'Posted',if(a.invoice_status = 2,'Approved','Void')))", "display" => "Status");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 2;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Transaksi Penjualan";

            if ($acl->CheckUserAccess("ar.invoice", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "ar.invoice/add/0", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("ar.invoice", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "ar.invoice/add/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Invoice terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data invoice",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("ar.invoice", "delete")) {
                $settings["actions"][] = array("Text" => "Void", "Url" => "ar.invoice/void/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("ar.invoice", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "ar.invoice/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Invoice terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data invoice","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.invoice", "print")) {
                //$settings["actions"][] = array("Text" => "Print Invoice", "Url" => "ar.invoice/printhtml/%s","Target"=>"_blank","Class" => "bt_print", "ReqId" => 1, "Confirm" => "Cetak Invoice yang dipilih?");
                $settings["actions"][] = array("Text" => "Print Invoice", "Url" => "ar.invoice/invoice_print/invoice","Target"=>"_blank","Class" => "bt_print", "ReqId" => 2, "Confirm" => "Cetak Invoice yang dipilih?");
                //$settings["actions"][] = array("Text" => "Print D/O", "Url" => "ar.invoice/invoice_print/do","Class" => "bt_pdf", "ReqId" => 2, "Confirm" => "Cetak D/O Invoice yang dipilih?");
                //$settings["actions"][] = array("Text" => "Print Surat Jalan", "Url" => "ar.invoice/invoice_print/suratjalan","Class" => "bt_pdf", "ReqId" => 2, "Confirm" => "Cetak Surat Jalan Invoice yang dipilih?");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.invoice", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "ar.invoice/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.invoice", "approve")) {
                $settings["actions"][] = array("Text" => "Approve Invoice", "Url" => "ar.invoice/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Invoice terlebih dahulu sebelum proses approval.",
                    "Confirm" => "Apakah anda menyetujui data invoice yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
                $settings["actions"][] = array("Text" => "Batal Approve", "Url" => "ar.invoice/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Invoice terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Apakah anda mau membatalkan approval data invoice yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
        } else {
            $settings["from"] = "vw_ar_invoice_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.cabang_id = " . $this->userCabangId ." And year(a.invoice_date) = ".$this->trxYear." And month(a.invoice_date) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	/* entry data penjualan*/
    public function add($invoiceId = 0) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/warehouse.php");
        require_once(MODEL . "master/tax.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $invoice = new Invoice();
        $custCreditLimit = 0;
        $maxInvOutstanding = 0;
        if ($invoiceId > 0 ) {
            $invoice = $invoice->LoadById($invoiceId);
            if ($invoice == null) {
                $this->persistence->SaveState("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
                redirect_url("ar.invoice");
            }
            if ($invoice->PaidAmount > 0 && $invoice->PaymentType == 1) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah terbayar. Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            if ($invoice->QtyReturn($invoiceId) > 0) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s ada item yg diretur. Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            if ($invoice->InvoiceStatus == 2) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Approve- Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            if ($invoice->InvoiceStatus == 3) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Void- Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            if ($invoice->CreatebyId <> AclManager::GetInstance()->GetCurrentUser()->Id && $this->userLevel == 1){
                $this->persistence->SaveState("error", sprintf("Maaf Anda tidak boleh mengubah data ini!",$invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            $customer = new Contacts();
            $customer = $customer->LoadById($invoice->CustomerId);
            $custCreditLimit = $customer->CreditLimit;
        }
        // load details
        $invoice->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadById($this->userCabangId);
        if ($cabang->CabType == 2){
            $this->persistence->SaveState("error", "Maaf Cabang %s dalam mode Gudang, tidak boleh digunakan untuk transaksi!",$cabang->Kode);
            redirect_url("ar.invoice");
        }
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $cabRPM = $cabang->RawPrintMode;
        $cabRPN = $cabang->RawPrinterName;
        $cabAlMin = $cabang->AllowMinus;
        $loader = new Karyawan();
        $sales = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Warehouse();
        $gudangs = $loader->LoadByCabangId($this->userCabangId);
        $loader = new Tax();
        $taxs = $loader->LoadAll();
        //kirim ke view
        $this->Set("taxs", $taxs);
        $this->Set("gudangs", $gudangs);
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabAlMin", $cabAlMin);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("userCabRpm", $cabRPM);
        $this->Set("userCabRpn", $cabRPN);
        $this->Set("sales", $sales);
        $this->Set("cabangs", $cabang);
        $this->Set("invoice", $invoice);
        $this->Set("creditLimit", $custCreditLimit);
        $this->Set("maxInvOutstanding", $maxInvOutstanding);
        $this->Set("acl", $acl);
        $this->Set("itemsCount", $this->InvoiceItemsCount($invoiceId));
        $router = Router::GetInstance();
        $this->Set("userIpAdd",$router->IpAddress);
    }

    public function proses_master($invoiceId = 0) {
        $invoice = new Invoice();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $invoice->Id = $invoiceId;
            $invoice->CabangId = $this->GetPostValue("CabangId");
            $invoice->GudangId = $this->GetPostValue("GudangId");
            $invoice->InvoiceDate = date('Y-m-d',strtotime($this->GetPostValue("InvoiceDate")));
            $invoice->InvoiceNo = $this->GetPostValue("InvoiceNo");
            $invoice->InvoiceDescs = $this->GetPostValue("InvoiceDescs");
            $invoice->CustomerId = $this->GetPostValue("CustomerId");
            $invoice->CustLevel = $this->GetPostValue("CustLevel") == null ? 0 : $this->GetPostValue("CustLevel");
            $invoice->SalesId = $this->GetPostValue("SalesId");
            $invoice->ExSoNo = $this->GetPostValue("ExSoNo");
            if ($this->GetPostValue("InvoiceStatus") == null || $this->GetPostValue("InvoiceStatus") == 0){
                $invoice->InvoiceStatus = 1;
            }else{
                $invoice->InvoiceStatus = $this->GetPostValue("InvoiceStatus");
            }
            $invoice->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $invoice->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if($this->GetPostValue("PaymentType") == null){
                $invoice->PaymentType = 0;
                $invoice->InvoiceStatus = 1;
            }else{
                $invoice->PaymentType = $this->GetPostValue("PaymentType");
                if ($invoice->PaymentType == 0){
                    $invoice->InvoiceStatus = 1;
                }
            }
            if($this->GetPostValue("CreditTerms") == null){
                $invoice->CreditTerms = 0;
            }else{
                $invoice->CreditTerms = $this->GetPostValue("CreditTerms");
            }
            $invoice->BaseAmount = $this->GetPostValue("BaseAmount") == null ? 0 : $this->GetPostValue("BaseAmount");
            $invoice->Disc1Pct = $this->GetPostValue("Disc1Pct") == null ? 0 : $this->GetPostValue("Disc1Pct");
            $invoice->Disc1Amount = $this->GetPostValue("Disc1Amount") == null ? 0 : $this->GetPostValue("Disc1Amount");
            $invoice->Disc2Pct = 0;
            $invoice->Disc2Amount = 0;
            $invoice->PaidAmount = 0;
            $invoice->TaxPct = $this->GetPostValue("TaxPct") == null ? 0 : $this->GetPostValue("TaxPct");
            $invoice->TaxAmount = $this->GetPostValue("TaxAmount") == null ? 0 : $this->GetPostValue("TaxAmount");
            $invoice->OtherCosts = $this->GetPostValue("OtherCosts") == null ? 0 : $this->GetPostValue("OtherCosts");
            $invoice->OtherCostsAmount = str_replace(",","",$this->GetPostValue("OtherCostsAmount") == null ? 0 : $this->GetPostValue("OtherCostsAmount"));
            $invoice->InvoiceType = $this->GetPostValue("InvoiceType");
            if ($invoice->Id == 0) {
                $invoice->InvoiceNo = $invoice->GetInvoiceDocNo();
                $rs = $invoice->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add New Invoice',$invoice->InvoiceNo,'Success');
                    printf("OK|A|%d|%s",$invoice->Id,$invoice->InvoiceNo);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add New Invoice',$invoice->InvoiceNo,'Failed');
                    printf("ER|A|%d",$invoice->Id);
                }
            }else{
                $rs = $invoice->Update($invoice->Id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Update Invoice',$invoice->InvoiceNo,'Success');
                    printf("OK|U|%d|%s",$invoice->Id,$invoice->InvoiceNo);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Update Invoice',$invoice->InvoiceNo,'Failed');
                    printf("ER|U|%d",$invoice->Id);
                }
            }
        }else{
            printf("ER|X|%d",$invoiceId);
        }
    }

	private function ValidateMaster(Invoice $invoice) {
        if ($invoice->CustomerId == 0 || $invoice->CustomerId == null || $invoice->CustomerId == ''){
            $this->Set("error", "Customer tidak boleh kosong!");
            return false;
        }
        if ($invoice->SalesId == 0 || $invoice->SalesId == null || $invoice->SalesId == ''){
            $this->Set("error", "Salesman tidak boleh kosong!");
            return false;
        }
        if ($invoice->PaymentType == 1 && $invoice->CreditTerms == 0){
            $this->Set("error", "Lama kredit belum diisi!");
            return false;
        }
		return true;
	}

	public function view($invoiceId = null) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $invoice = new Invoice();
        $invoice = $invoice->LoadById($invoiceId);
        if($invoice == null){
            $this->persistence->SaveState("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.invoice");
        }
        // load details
        $invoice->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $cabRPM = $cabang->RawPrintMode;
        $cabRPN = $cabang->RawPrinterName;
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
        $loader = new Cabang();
        $gudangs = $loader->LoadByType($this->userCompanyId,1,"<>");
        //kirim ke view
        $this->Set("gudangs", $gudangs);
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("sales", $sales);
        $this->Set("cabangs", $cabang);
        $this->Set("invoice", $invoice);
        $this->Set("acl", $acl);
        $this->Set("userCabRpm", $cabRPM);
        $this->Set("userCabRpn", $cabRPN);
        $router = Router::GetInstance();
        $this->Set("userIpAdd",$router->IpAddress);
	}

    public function delete($invoiceId) {
        // Cek datanya
        $invoice = new Invoice();
        $log = new UserAdmin();
        $invoice = $invoice->FindById($invoiceId);
        if($invoice == null){
            $this->Set("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.invoice");
        }
        /** @var $invoice Invoice */
        if($invoice->PaidAmount > 0 && $invoice->PaymentType == 1){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah terbayar. Tidak boleh dihapus..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->QtyReturn($invoiceId) > 0){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s ada item yg diretur. Tidak boleh dihapus..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->InvoiceStatus == 2){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Approve- Tidak boleh dihapus..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if ($invoice->Delete($invoiceId) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice',$invoice->InvoiceNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Invoice No: %s sudah berhasil dihapus", $invoice->InvoiceNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice',$invoice->InvoiceNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Invoice No: %s gagal dihapus", $invoice->InvoiceNo));
        }
        redirect_url("ar.invoice");
    }

    public function void($invoiceId) {
        // Cek datanya
        $invoice = new Invoice();
        $log = new UserAdmin();
        $invoice = $invoice->FindById($invoiceId);
        if($invoice == null){
            $this->Set("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.invoice");
        }
        /** @var $invoice Invoice */
        if($invoice->PaidAmount > 0 && $invoice->PaymentType == 1){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah terbayar. Tidak boleh dibatalkan..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->QtyReturn($invoiceId) > 0){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s ada item yg diretur. Tidak boleh dibatalkan..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->InvoiceStatus == 2){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Approve- Tidak boleh dibatalkan..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->InvoiceStatus == 3){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Void- Tidak boleh dibatalkan..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if ($invoice->Void($invoiceId,$invoice->InvoiceNo) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice',$invoice->InvoiceNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Invoice No: %s sudah berhasil dibatalkan", $invoice->InvoiceNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice',$invoice->InvoiceNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Invoice No: %s gagal dibatalkan", $invoice->InvoiceNo));
        }
        redirect_url("ar.invoice");
    }


	public function add_detail($invoiceId = null) {
        require_once(MODEL . "master/items.php");
        $log = new UserAdmin();
        $invoice = new Invoice($invoiceId);
        $invdetail = new InvoiceDetail();
        $invdetail->InvoiceId = $invoiceId;
        $invdetail->InvoiceNo = $invoice->InvoiceNo;
        $invdetail->CabangId = $invoice->CabangId;
        $invdetail->GudangId = $invoice->GudangId;
        $items = null;
        $is_item_exist = false;
        if (count($this->postData) > 0) {
            $invdetail->ItemId = $this->GetPostValue("aItemId");
            $invdetail->Qty = $this->GetPostValue("aQty");
            $invdetail->Price = $this->GetPostValue("aPrice");
            if ($this->GetPostValue("aDiscFormula") == ''){
                $invdetail->DiscFormula = 0;
            }else{
                $invdetail->DiscFormula = $this->GetPostValue("aDiscFormula");
            }
            $invdetail->DiscAmount = $this->GetPostValue("aDiscAmount");
            $invdetail->SubTotal = $this->GetPostValue("aSubTotal");
            $invdetail->ItemHpp = $this->GetPostValue("aItemHpp");
            $invdetail->ItemNote = $this->GetPostValue("aItemNote");
            $invdetail->IsFree = $this->GetPostValue("aIsFree");
            $invdetail->ExSoNo = $this->GetPostValue("aSoNo");
            $invdetail->TaxCode = $this->GetPostValue("aTaxCode");
            $invdetail->TaxPct = $this->GetPostValue("aTaxPct");
            $invdetail->TaxAmount = $this->GetPostValue("aTaxAmount");
            // periksa apa sudah ada item dengan harga yang sama, kalo ada gabungkan saja
            $invdetail_exists = new InvoiceDetail();
            $invdetail_exists = $invdetail_exists->FindDuplicate($invdetail->CabangId,$invdetail->InvoiceId,$invdetail->ItemId,$invdetail->Price,$invdetail->DiscFormula,$invdetail->DiscAmount,$invdetail->IsFree,$invdetail->ExSoNo);
            if ($invdetail_exists != null){
                // proses penggabungan disini
                /** @var $invdetail_exists InvoiceDetail */
                $is_item_exist = true;
                $invdetail->Qty+= $invdetail_exists->Qty;
                $invdetail->DiscAmount+= $invdetail_exists->DiscAmount;
                $invdetail->SubTotal+= $invdetail_exists->SubTotal;
            }
            $items = new Items($invdetail->ItemId);
            if ($items != null){
                $invdetail->ItemCode = $items->Bkode;
                $invdetail->ItemDescs = $items->Bnama;
                $invdetail->Lqty = 0;
                $invdetail->Sqty = 0;
                // insert ke table
                if ($is_item_exist){
                    // sudah ada item yg sama gabungkan..
                    $rs = $invdetail->Update($invdetail_exists->Id);
                    if ($rs > 0) {
                        $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Merge Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Success');
                        print('OK|Proses simpan update berhasil!');
                    } else {
                        $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Merge Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Failed');
                        print('ER|Gagal proses update data!');
                    }
                }else {
                    // item baru simpan
                    $rs = $invdetail->Insert() == 1;
                    if ($rs > 0) {
                        $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Success');
                        print('OK|Proses simpan data berhasil!');
                    } else {
                        $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Failed');
                        print('ER|Gagal proses simpan data!');
                    }
                }
            }else{
                print('ER|Data barang tidak ditemukan!');
            }
        }
	}

    public function edit_detail($invoiceId = null) {
        require_once(MODEL . "master/items.php");
        $log = new UserAdmin();
        $invoice = new Invoice($invoiceId);
        $invdetail = new InvoiceDetail();
        $invdetail->InvoiceId = $invoiceId;
        $invdetail->InvoiceNo = $invoice->InvoiceNo;
        $invdetail->CabangId = $invoice->CabangId;
        $invdetail->GudangId = $invoice->GudangId;
        $items = null;
        if (count($this->postData) > 0) {
            $invdetail->Id = $this->GetPostValue("aId");
            $invdetail->ItemId = $this->GetPostValue("aItemId");
            $invdetail->Qty = $this->GetPostValue("aQty");
            $invdetail->Price = $this->GetPostValue("aPrice");
            if ($this->GetPostValue("aDiscFormula") == ''){
                $invdetail->DiscFormula = 0;
            }else{
                $invdetail->DiscFormula = $this->GetPostValue("aDiscFormula");
            }
            $invdetail->DiscAmount = $this->GetPostValue("aDiscAmount");
            $invdetail->SubTotal = $this->GetPostValue("aSubTotal");
            $invdetail->ItemHpp = $this->GetPostValue("aItemHpp");
            $invdetail->ItemNote = $this->GetPostValue("aItemNote");
            $invdetail->IsFree = $this->GetPostValue("aIsFree");
            $invdetail->ExSoNo = $this->GetPostValue("aSoNo");
            $invdetail->TaxCode = $this->GetPostValue("aTaxCode");
            $invdetail->TaxPct = $this->GetPostValue("aTaxPct");
            $invdetail->TaxAmount = $this->GetPostValue("aTaxAmount");
            $items = new Items($invdetail->ItemId);
            if ($items != null){
                $invdetail->ItemCode = $items->Bkode;
                $invdetail->ItemDescs = $items->Bnama;
                $invdetail->Lqty = 0;
                $invdetail->Sqty = 0;
                // insert ke table
                $rs = $invdetail->Update($invdetail->Id);
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Edit Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Success');
                    print('OK|Proses update data berhasil!');
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Edit Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Failed');
                    print('ER|Gagal update data!');
                }
            }else{
                print('ER|Data barang tidak ditemukan!');
            }
        }
    }


    public function delete_detail($id) {
        // Cek datanya
        $invdetail = new InvoiceDetail();
        $log = new UserAdmin();
        $invdetail = $invdetail->FindById($id);
        if ($invdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($invdetail->Delete($id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invdetail->InvoiceNo,'Success');
            printf("Data Detail Invoice ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invdetail->InvoiceNo,'Failed');
            printf("Maaf, Data Detail Invoice ID: %d gagal dihapus!",$id);
        }
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sContactsId = $this->GetPostValue("ContactsId");
            $sSalesId = $this->GetPostValue("SalesId");
            $sStatus = $this->GetPostValue("Status");
            $sPaymentStatus = $this->GetPostValue("PaymentStatus");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $invoice = new Invoice();
            if ($sJnsLaporan == 1){
                $reports = $invoice->Load4Reports($this->userCompanyId,$sCabangId,$sContactsId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 2) {
                $reports = $invoice->Load4ReportsDetail($this->userCompanyId, $sCabangId, $sContactsId, $sSalesId, $sStatus, $sPaymentStatus, $sStartDate, $sEndDate);
            }elseif ($sJnsLaporan == 3) {
                $reports = $invoice->Load4ReportsRekapItem($this->userCompanyId,$sCabangId,$sContactsId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 4){
                $reports = $invoice->Load4Reports($this->userCompanyId,$sCabangId,$sContactsId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
            }else{
                $reports = $invoice->Load4ReportsRekapItem1($this->userCompanyId,$sCabangId,$sContactsId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sContactsId = 0;
            $sSalesId = 0;
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
        $customer = new Contacts();
        $customer = $customer->LoadAll();
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
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
        $this->Set("customers",$customer);
        $this->Set("sales",$sales);
        $this->Set("CabangId",$sCabangId);
        $this->Set("ContactsId",$sContactsId);
        $this->Set("SalesId",$sSalesId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Status",$sStatus);
        $this->Set("PaymentStatus",$sPaymentStatus);
        $this->Set("Output",$sOutput);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("Reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
    }

    public function getInvoiceItemRows($id){
        $invoice = new Invoice();
        $rows = $invoice->GetInvoiceItemRow($id);
        print($rows);
    }

    public function InvoiceItemsCount($id){
        $invoice = new Invoice();
        $rows = $invoice->GetInvoiceItemRow($id);
        return $rows;
    }

    public function createTextInvoice($id){
        $invoice = new Invoice($id);
        if ($invoice <> null){
            $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            fwrite($myfile, $invoice->CompanyName);
            fwrite($myfile, "\n".'FAKTUR PENJUALAN');
            fclose($myfile);
        }
    }

    public function getjson_invoicelists($cabangId,$customerId){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $invoices = new Invoice();
        $invlists = $invoices->GetJSonInvoices($cabangId,$customerId,$filter);
        echo json_encode($invlists);
    }

    public function getjson_invoiceitems($invoiceId = 0){
        $invoices = new Invoice();
        $itemlists = $invoices->GetJSonInvoiceItems($invoiceId);
        echo json_encode($itemlists);
    }

    public function approve() {
        require_once (MODEL . "master/contacts.php");
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.invoice");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $invoice = new Invoice();
            $log = new UserAdmin();
            $invoice = $invoice->FindById($id);
            /** @var $invoice Invoice */
            // process invoice
            if($invoice->InvoiceStatus == 1){
                $customer = new Contacts();
                $customer = $customer->FindById($invoice->CustomerId);
                if ($customer == null){
                    $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Approve Invoice', $invoice->InvoiceNo, 'Failed');
                    $errors[] = sprintf("Data Invoice No.%s gagal di-approve - Error: Data customer tidak valid!",$invoice->InvoiceNo);
                }else {
                    //check outstanding customer, jika masih ada tunda approval biasa
                    /** @var $customer Contacts */
                    if ($customer->MaxInvOutstanding > 0 && $customer->QtyInvOutstanding > 0){
                        $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Approve Invoice', $invoice->InvoiceNo, 'Failed');
                        $errors[] = sprintf("Data Invoice No.%s gagal di-approve - Error: Customer over outstanding!",$invoice->InvoiceNo);
                    }else {
                        $rs = $invoice->Approve($invoice->Id, $uid);
                        if ($rs) {
                            $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Approve Invoice', $invoice->InvoiceNo, 'Success');
                            $infos[] = sprintf("Data Invoice No.: '%s' (%s) telah berhasil di-approve.", $invoice->InvoiceNo, $invoice->InvoiceDescs);
                        } else {
                            $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Approve Invoice', $invoice->InvoiceNo, 'Failed');
                            $errors[] = sprintf("Maaf, Gagal proses approve Data Invoice: '%s'. Message: %s", $invoice->InvoiceNo, $this->connector->GetErrorMessage());
                        }
                    }
                }
            }else{
                $errors[] = sprintf("Data Invoice No.%s sudah berstatus -Approved- !",$invoice->InvoiceNo);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ar.invoice");
    }

    public function unapprove() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.invoice");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $invoice = new Invoice();
            $log = new UserAdmin();
            $invoice = $invoice->FindById($id);
            /** @var $invoice Invoice */
            // process invoice
            if($invoice->InvoiceStatus == 2){
                if ($invoice->PaidAmount > 0 && $invoice->PaymentType == 1) {
                    $errors[] = sprintf("Data Invoice No.%s sudah terbayar !", $invoice->InvoiceNo);
                }elseif($invoice->QtyReturn($invoice->Id) > 0){
                    $errors[] = sprintf("Data Invoice No.%s ada item yg diretur !", $invoice->InvoiceNo);
                }else {
                    $rs = $invoice->Unapprove($invoice->Id, $uid);
                    if ($rs) {
                        $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Un-approve Invoice', $invoice->InvoiceNo, 'Success');
                        $infos[] = sprintf("Data Invoice No.: '%s' (%s) telah berhasil di-batalkan.", $invoice->InvoiceNo, $invoice->InvoiceDescs);
                    } else {
                        $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Un-approve Invoice', $invoice->InvoiceNo, 'Failed');
                        $errors[] = sprintf("Maaf, Gagal proses pembatalan Data Invoice: '%s'. Message: %s", $invoice->InvoiceNo, $this->connector->GetErrorMessage());
                    }
                }
            }else{
                if ($invoice->InvoiceStatus == 1) {
                    $errors[] = sprintf("Data Invoice No.%s masih berstatus -POSTED- !", $invoice->InvoiceNo);
                }elseif ($invoice->InvoiceStatus == 3){
                    $errors[] = sprintf("Data Invoice No.%s sudah berstatus -VOID- !",$invoice->InvoiceNo);
                }else{
                    $errors[] = sprintf("Data Invoice No.%s masih berstatus -DRAFT- !",$invoice->InvoiceNo);
                }
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ar.invoice");
    }

    public function approvespecial(){
        require_once (MODEL . "master\user_acl.php");
        $retval = null;
        $infos = array();
        $errors = array();
        //special approval for customer over outstanding
        if (count($this->postData) > 0) {
            $iid = $this->GetPostValue("invoiceId");
            $uid = $this->GetPostValue("userId");
            $upw = $this->GetPostValue("userPasswd");
            $rsn = $this->GetPostValue("approveReason");
            $usi = 0;
            //cek user validation
            $user = new UserAdmin();
            $user = $user->FindByUserId($uid);
            if ($user == null){
                //user tidak valid
                $retval = "ER|1";
            }else {
                $usi = $user->UserUid;
                if (md5($upw) == $user->UserPwd1) {
                    //cek hak akses dulu
                    $oke = false;
                    if ($user->UserLvl < 4){
                        $uacl = new UserAcl();
                        $uacl = $uacl->LoadAclByController($usi,$this->userCabangId,'ar.invoice');
                        if ((strpos($uacl->Rights,"6") == true) || (strpos($uacl->Rights,"9") == true)){
                            $oke = true;
                        }
                    }else{
                        $oke = true;
                    }
                    if ($oke) {
                        $invoice = new Invoice();
                        $invoice = $invoice->FindById($iid);
                        if ($invoice == null || $invoice->InvoiceStatus <> 1) {
                            //invoice tidak valid
                            $retval = "ER|4";
                        } else {
                            $log = new UserAdmin();
                            $rs = $invoice->Approve($iid, $usi, 2, $rsn);
                            if ($rs) {
                                $retval = "OK|1";
                                $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Approve Invoice', $invoice->InvoiceNo, 'Success');
                            } else {
                                $retval = "ER|5";
                                $log = $log->UserActivityWriter($this->userCabangId, 'ar.invoice', 'Approve Invoice', $invoice->InvoiceNo, 'Failed');
                            }
                        }
                    }else{
                        //hak akses ditolak
                        $retval = "ER|3";
                    }
                }else{
                    //password salah
                    $retval = "ER|2";
                }
            }
        }else{
            //no data posted
            $retval = "ER|0";
        }
        print ($retval);
    }

    public function getitempricestock_json($level,$cabangId){
        require_once(MODEL . "master/setprice.php");
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new SetPrice();
        $itemlists = $setprice->GetJSonItemPriceStock($level,$cabangId,$filter);
        echo json_encode($itemlists);
    }

    public function getitemstock_json($gudangId){
        require_once(MODEL . "master/setprice.php");
        require_once(MODEL . "master/cabang.php");
        //$cabang = new Cabang($cabangId);
        $allowMinus = 1;
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new SetPrice();
        $itemlists = $setprice->GetJSonItemStock($allowMinus,$gudangId,$filter);
        echo json_encode($itemlists);
    }

    public function getitempricestock_plain($cabangId,$bkode,$level){
        require_once(MODEL . "master/setprice.php");
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $items Items  */
            $setprice = new SetPrice();
            $setprice = $setprice->FindByKode($cabangId,$bkode);
            $items = null;
            if ($setprice != null){
                /** @var $setprice SetPrice */
                $ret = "OK|".$setprice->ItemId.'|'.$setprice->ItemName.'|'.$setprice->Satuan.'|'.$setprice->QtyStock.'|'.$setprice->HrgBeli;
                if ($level == -1 && $setprice->HrgBeli > 0){
                    $ret.= '|'.$setprice->HrgBeli;
                }elseif($level == 1 && $setprice->HrgJual11 > 0){
                    $ret.= '|'.$setprice->HrgJual11;
                }elseif($level == 2 && $setprice->HrgJual12 > 0){
                    $ret.= '|'.$setprice->HrgJual12;
                }elseif($level == 3 && $setprice->HrgJual13 > 0){
                    $ret.= '|'.$setprice->HrgJual13;
                }elseif($level == 4 && $setprice->HrgJual14 > 0){
                    $ret.= '|'.$setprice->HrgJual14;
                }elseif($level == 5 && $setprice->HrgJual15 > 0){
                    $ret.= '|'.$setprice->HrgJual15;
                }else{
                    $ret.= '|'.$setprice->HrgJual6;
                }
            }
        }
        print $ret;
    }

    public function getDiscPrivileges($resId){
        require_once(MODEL . "master/user_privileges.php");
        $userId = AclManager::GetInstance()->GetCurrentUser()->Id;
        $rst = -1;
        $privileges = new UserPrivileges();
        $privileges = $privileges->FindByResourceId($userId,$resId);
        if ($privileges != null){
            /** @var $privileges UserPrivileges */
            $rst = $privileges->MaxDiscount;
        }
        print $rst;
    }

    public function getStockQty($gudangId = 0,$itemCode){
        require_once(MODEL . "inventory/stock.php");
        $sqty = 0;
        $stock = new Stock();
        $sqty = $stock->CheckStock($this->userCabangId,$gudangId,$itemCode);
        print(number_format($sqty,0));
    }

    public function getPriceByAreaQty($itemCode,$custLevel,$itemQty){
        require_once(MODEL . "master/setprice.php");
        $price = new SetPrice();
        $price = $price->FindByKode($this->userCabangId,$itemCode);
        $vprice = 0;
        $bprice = 0;
        $result = '0|0';
        if ($price != null){
            $bprice = $price->HrgBeli;
            if ($itemQty >= $price->MinQty1) {
                if ($custLevel == 1) {
                    $vprice = $price->HrgJual11;
                } elseif ($custLevel == 2) {
                    $vprice = $price->HrgJual12;
                } elseif ($custLevel == 3) {
                    $vprice = $price->HrgJual13;
                } elseif ($custLevel == 4) {
                    $vprice = $price->HrgJual14;
                } elseif ($custLevel == 5) {
                    $vprice = $price->HrgJual15;
                } elseif ($custLevel == 6) {
                    $vprice = $price->HrgJual16;
                }
            }
            if ($itemQty >= $price->MinQty2 && $price->MinQty2 > $price->MinQty1){
                if ($custLevel == 1){
                    $vprice = $price->HrgJual21;
                }elseif ($custLevel == 2){
                    $vprice = $price->HrgJual22;
                }elseif ($custLevel == 3){
                    $vprice = $price->HrgJual23;
                }elseif ($custLevel == 4){
                    $vprice = $price->HrgJual24;
                }elseif ($custLevel == 5){
                    $vprice = $price->HrgJual25;
                }elseif ($custLevel == 6){
                    $vprice = $price->HrgJual26;
                }
            }
            if ($itemQty >= $price->MinQty3 && $price->MinQty3 > $price->MinQty2){
                if ($custLevel == 1){
                    $vprice = $price->HrgJual31;
                }elseif ($custLevel == 2){
                    $vprice = $price->HrgJual32;
                }elseif ($custLevel == 3){
                    $vprice = $price->HrgJual33;
                }elseif ($custLevel == 4){
                    $vprice = $price->HrgJual34;
                }elseif ($custLevel == 5){
                    $vprice = $price->HrgJual35;
                }elseif ($custLevel == 6){
                    $vprice = $price->HrgJual36;
                }
            }
            if ($itemQty >= $price->MinQty4 && $price->MinQty4 > $price->MinQty3){
                if ($custLevel == 1){
                    $vprice = $price->HrgJual41;
                }elseif ($custLevel == 2){
                    $vprice = $price->HrgJual42;
                }elseif ($custLevel == 3){
                    $vprice = $price->HrgJual43;
                }elseif ($custLevel == 4){
                    $vprice = $price->HrgJual44;
                }elseif ($custLevel == 5){
                    $vprice = $price->HrgJual45;
                }elseif ($custLevel == 6){
                    $vprice = $price->HrgJual46;
                }
            }
            if ($itemQty >= $price->MinQty5 && $price->MinQty5 > $price->MinQty4){
                if ($custLevel == 1){
                    $vprice = $price->HrgJual51;
                }elseif ($custLevel == 2){
                    $vprice = $price->HrgJual52;
                }elseif ($custLevel == 3){
                    $vprice = $price->HrgJual53;
                }elseif ($custLevel == 4){
                    $vprice = $price->HrgJual54;
                }elseif ($custLevel == 5){
                    $vprice = $price->HrgJual55;
                }elseif ($custLevel == 6){
                    $vprice = $price->HrgJual56;
                }
            }
            if ($itemQty >= $price->MinQty6 && $price->MinQty6 > $price->MinQty5){
                if ($custLevel == 1) {
                    $vprice = $price->HrgJual61;
                } elseif ($custLevel == 2) {
                    $vprice = $price->HrgJual62;
                } elseif ($custLevel == 3) {
                    $vprice = $price->HrgJual63;
                } elseif ($custLevel == 4) {
                    $vprice = $price->HrgJual64;
                } elseif ($custLevel == 5) {
                    $vprice = $price->HrgJual65;
                } elseif ($custLevel == 6) {
                    $vprice = $price->HrgJual66;
                }
            }
        }
        $result = $bprice.'|'.$vprice;
        print($result);
    }

    public function getItemPrice($itemCode,$level = 0){
        require_once(MODEL . "master/setprice.php");
        $price = new SetPrice();
        $price = $price->GetItemPrice($itemCode,$level,$this->userCabangId);
        print($price);
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
            $invoice = new Invoice();
            if ($sJnsLaporan == 1){
                $reports = $invoice->Load4ProfitTransaksi($this->userCompanyId,$sCabangId,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 2){
                $reports = $invoice->Load4ProfitTanggal($this->userCompanyId,$sCabangId,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 3){
                $reports = $invoice->Load4ProfitBulan($this->userCompanyId,$sCabangId,$sStartDate,$sEndDate);
            }else{
                $reports = $invoice->Load4ProfitItem($this->userCompanyId,$sCabangId,$sStartDate,$sEndDate);
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

    public function getjson_solists($cabangId,$customerId){
        require_once (MODEL . "ar/order.php");
        //$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $order = new Order();
        $solists = $order->GetActiveSoList($cabangId,$customerId);
        echo json_encode($solists);
    }

    public function getjson_soitems($soNo,$gdId){
        require_once (MODEL . "ar/order.php");
        //$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $order = new Order();
        $soitems = $order->GetItemSoItems($soNo,$gdId);
        echo json_encode($soitems);
    }

    public function prosesSalesOrder($invId,$invNo,$soNo){
        //proses transfer data dari sales order
        //print('Test OK! '.$invId.' - '.$invNo.' - '.$soNo);
        $inv = new Invoice();
        $hsl = $inv->PostSoDetail2Invoice($invId,$invNo,$soNo);
        if ($hsl > 0){
            print("OK");
        }else{
            print("ER");
        }
    }

    public function getSumOutstanding($customerId = 0){
        $sumOut = 0;
        $invoice = new Invoice();
        $sumOut = $invoice->GetSumOutstandingInvoices($this->userCabangId,$customerId);
        print($sumOut);
    }

    public function getQtyOutstanding($customerId = 0){
        $qtyOut = 0;
        $invoice = new Invoice();
        $qtyOut = $invoice->GetQtyOutstandingInvoices($this->userCabangId,$customerId);
        print($qtyOut);
    }

    //direct printing
    public function printhtml($invId,$paperType = 0,$prtName = null){
        require_once (MODEL . "master/cabang.php");
        require_once (MODEL . "master/contacts.php");
        $invoice = new Invoice($invId);
        if ($invoice->InvoiceStatus == 2) {
            $invoice->LoadDetails();
            $cabang = new Cabang($invoice->CabangId);
            $customer = new Contacts($invoice->CustomerId);
            $this->Set("invoice", $invoice);
            $this->Set("cabang", $cabang);
            $this->Set("customer", $customer);
        }else{
            redirect_url("ar.invoice");
        }
    }

    //proses cetak form invoice
    public function invoice_print($doctype = 'invoice') {
        require_once (MODEL . "master/cabang.php");
        require_once (MODEL . "master/contacts.php");
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Harap pilih data yang akan dicetak !");
            redirect_url("ar.invoice");
            return;
        }
        $jdt = 0;
        $errors = array();
        $report = array();
        foreach ($ids as $id) {
            $inv = new Invoice();
            $inv = $inv->LoadById($id);
            /** @var $inv Invoice */
            if ($inv != null) {
                if ($inv->InvoiceStatus == 2) {
                    $jdt++;
                    $inv->LoadDetails();
                    $report[] = $inv;
                }
            }
        }
        if ($jdt == 0){
            $errors[] = sprintf("Data Invoice yg dipilih tidak memenuhi syarat!");
            redirect_url("ar.invoice");
        }
        $cabang = new Cabang($this->userCabangId);
        $this->Set("cabang", $cabang);
        $this->Set("doctype", $doctype);
        $this->Set("report", $report);
    }
}


// End of File: invoice_controller.php
