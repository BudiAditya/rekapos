<?php
class StockController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;

    protected function Initialize() {
        require_once(MODEL . "inventory/stock.php");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
    }

    public function index() {
        $router = Router::GetInstance();
        $settings = array();

        $settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 50);
        $settings["columns"][] = array("name" => "a.wh_code", "display" => "Lokasi", "width" => 80);
        $settings["columns"][] = array("name" => "a.item_code", "display" => "Kode", "width" => 50);
        $settings["columns"][] = array("name" => "a.bar_code", "display" => "Bar Code", "width" => 100);
        $settings["columns"][] = array("name" => "a.bnama", "display" => "Nama Produk", "width" =>300);
        $settings["columns"][] = array("name" => "format(a.qty_stock,2)", "display" => "Qty Stock", "width" => 70, "align" => "right");
        $settings["columns"][] = array("name" => "a.bsatkecil", "display" => "Satuan", "width" =>100);
        $settings["columns"][] = array("name" => "a.supplier_name", "display" => "Supplier", "width" =>250);

        $settings["filters"][] = array("name" => "a.item_code", "display" => "PLU");
        $settings["filters"][] = array("name" => "a.bar_code", "display" => "Bar Code");
        $settings["filters"][] = array("name" => "a.bnama", "display" => "Nama Produk");
        $settings["filters"][] = array("name" => "a.supplier_name", "display" => "Supplier");
        $settings["filters"][] = array("name" => "a.kode", "display" => "Kode Cabang");

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Stock Barang";
            if ($acl->CheckUserAccess("inventory.stock", "view")) {
                $settings["actions"][] = array("Text" => "Kartu Stock", "Url" => "inventory.stock/card/%s", "Class" => "bt_view", "ReqId" => 1,"Confirm" => "Tampilkan Kartu Stock item yang dipilih?");
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Mutasi Stock", "Url" => "inventory.stock/mutasi", "Class" => "bt_report", "ReqId" => 0,"Confirm" => "Tampilkan Mutasi Stock?");
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Daftar Stock Produk", "Url" => "inventory.stock/stock_list/xls", "Class" => "bt_excel", "ReqId" => 0);
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Laporan Stock Produk", "Url" => "inventory.stock/report", "Class" => "bt_report", "ReqId" => 0);
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Periksa & Hitung Ulang Stock", "Url" => "inventory.stock/recount", "Class" => "bt_edit", "ReqId" => 0,"Confirm" => "Mulai Proses hitung ulang stock?");
            }
            $settings["def_order"] = 2;
            $settings["def_filter"] = 0;
            $settings["singleSelect"] = true;
        } else {
            $settings["from"] = "vw_ic_stockcenter as a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.cabang_id = " . $this->userCabangId;
            }
        }
        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

    public function getStockQty($cabangId = 0,$itemCode){
        $sqty = 0;
        $stock = new Stock();
        $sqty = $stock->CheckStock($cabangId,$itemCode);
        print(number_format($sqty,0));
    }

    public function card($id = null){
        require_once(MODEL . "master/company.php");
        // proses pembuatan kartu stock
        $stock = new Stock($id);
        if ($stock == null){
            $this->persistence->SaveState("error", "Maaf Data Transaksi barang dimaksud tidak ada pada database!");
            redirect_url("inventory.stock");
        }

        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        if (count($this->postData) > 0) {
            $startDate =  strtotime($this->GetPostValue("startDate"));
            $endDate = strtotime($this->GetPostValue("endDate"));
            $outPut = $this->GetPostValue("outPut");
        }else{
            $startDate = mktime(0, 0, 0, $month, 1, $year);
            $endDate = time();
            $outPut = 0;
        }
        $stkcard = $stock->GetStockHistory($startDate,$endDate);
        $this->Set("startDate",$startDate);
        $this->Set("endDate",$endDate);
        $this->Set("outPut",$outPut);
        $this->Set("stock",$stock);
        $this->Set("stkcard",$stkcard);
        $company = new Company($this->userCompanyId);
        $this->Set("company_name", $company->CompanyName);
    }

    public function mutasi(){
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/warehouse.php");
        // proses pembuatan mutasi stock
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $mstock = null;
        if (count($this->postData) > 0) {
            $cabangId =  $this->userCabangId;
            $whId =  $this->GetPostValue("whId");
            $startDate =  strtotime($this->GetPostValue("startDate"));
            $endDate = strtotime($this->GetPostValue("endDate"));
            $outPut = $this->GetPostValue("outPut");
            //$gdg = new Warehouse($whId);
            //$isd = false;
            //if ($gdg->WhCode == 'Display'){
                $isd = true;
            //}
            $mstock = new Stock();
            $mstock = $mstock->GetMutasiStock($this->userCabangId,$whId,$startDate,$endDate,$isd);
        }else{
            $cabangId = $this->userCabangId;
            $whId = $this->userCabangId;
            $startDate = mktime(0, 0, 0, $month, 1, $year);
            $endDate = time();
            $outPut = 0;
            $mstock = null;
        }
        //load data cabang
        $company = new Company($this->userCompanyId);
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        if ($this->userLevel > 3){
            $cabang = $loader->LoadByEntityId($this->userCompanyId);
            $cab = new Cabang();
            $cab = $cab->LoadById($cabangId);
            $cabCode = $cab->Kode;
            $cabName = $cab->Cabang;
        }else{
            $cabang = $loader->LoadById($this->userCabangId);
            $cabCode = $cabang->Kode;
            $cabName = $cabang->Cabang;
        }
        $loader = new Warehouse();
        $gudangs = $loader->LoadByCabangId($this->userCabangId);
        $this->Set("whId",$whId);
        $this->Set("cabangId",$cabangId);
        $this->Set("startDate",$startDate);
        $this->Set("endDate",$endDate);
        $this->Set("outPut",$outPut);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
        $this->Set("cabangs",$cabang);
        $this->Set("gudangs",$gudangs);
        $this->Set("mstock",$mstock);
        $this->Set("company_name", $company->CompanyName);
    }



    public function getitemstock_plain($cbi,$bkode){
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $stock Stock */
            $stock = new Stock();
            $stock = $stock->FindByKodeGudang($cbi,$bkode);
            if ($stock != null){
                $ret = "OK|".$stock->ItemId.'|'.$stock->ItemName.'|'.$stock->SatKecil.'|'.$stock->QtyStock.'|'.$stock->BarCode;
            }else{
                require_once (MODEL . "master/items.php");
                $items = new Items();
                $items = $items->FindByKode($bkode);
                if ($items != null){
                    $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatkecil.'|0|'.$items->Bbarcode;
                }
            }
        }
        print $ret;
    }

    public function getitemstock_plainbybarcode($cbi,$bkode){
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $stock Stock */
            $stock = new Stock();
            $stock = $stock->FindByBarcodeGudang($cbi,$bkode);
            if ($stock != null){
                $ret = "OK|".$stock->ItemId.'|'.$stock->ItemName.'|'.$stock->SatKecil.'|'.$stock->QtyStock.'|'.$stock->ItemCode;
            }else{
                require_once (MODEL . "master/items.php");
                $items = new Items();
                $items = $items->FindByBarCode($bkode);
                if ($items != null){
                    $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatkecil.'|0|'.$items->Bkode;
                }
            }
        }
        print $ret;
    }

    public function getitemstock($whi,$bkode){
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $stock Stock */
            $stock = new Stock();
            $stock = $stock->FindByKodeGudang($whi,$bkode);
            if ($stock != null){
                $ret = "OK|".$stock->ItemId.'|'.$stock->ItemName.'|'.$stock->SatKecil.'|'.$stock->QtyStock;
            }
        }
        print $ret;
    }

    public function getitemstock_json($gudang_id = 0,$order="a.item_code"){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $stock = new Stock();
        $itemlists = $stock->GetJSonItemStock($gudang_id,$filter,'b.bnama');
        echo json_encode($itemlists);
    }

    public function stocknotes($entityId,$cabId){
        $stock = new Stock();
        if ($this->userLevel > 3){
            $rstock = $stock->getStockMinus(0,0);
        }elseif ($this->userLevel > 1 && $this->userLevel < 4){
            $rstock = $stock->getStockMinus($entityId,0);
        }else{
            $rstock = $stock->getStockMinus(0,$cabId);
        }
        $notes = null;
        $notes = "<?xml version='1.0' encoding='UTF-8' ?>";
        $notes.= "<rss version='2.0'>";
        $notes.= "<channel>";
        $notes.= "<title>RSS Title</title>";
        $notes.= "<description>This is an example of an RSS feed</description>";
        $notes.= "<link>http://eraditya.com</link>";
        $notes.= "<lastBuildDate>Mon, 06 Sep 2010 00:01:00 +0000 </lastBuildDate>";
        $notes.= "<pubDate>Sun, 06 Sep 2009 16:20:00 +0000</pubDate>";
        $notes.= "<ttl>1800</ttl>";
        if ($rstock->GetNumRows() > 0){
            $news = null;
            while ($row = $rstock->FetchAssoc()) {
                $news = $row['cabang_code']." =>";
                $news.= " [".$row['item_code']."]";
                $news.= " ".$row['item_name'];
                $news.= " = ".$row['qty_stock'];
                $news.= " ".$row['satuan'];
                $notes.= "<item>";
                $notes.= "<title>$news</title>";
                $notes.= "<description>Stock Produk Minus</description>";
                $notes.= "<link>".base_url("inventory.stock/card/".$row['id'])."</link>";
                $notes.= "<guid isPermaLink='true'>1234</guid>";
                $notes.= "<pubDate>Sun, 06 Sep 2009 16:20:00 +0000</pubDate>";
                $notes.= "</item>";
            }
        }else{
            $notes.= "<item>";
            $notes.= "<title>Everything is OK... Have a Nice Day!</title>";
            $notes.= "<description>No Problem found</description>";
            $notes.= "<link>".base_url("main")."</link>";
            $notes.= "<guid isPermaLink='true'>1234</guid>";
            $notes.= "<pubDate>Sun, 06 Sep 2009 16:20:00 +0000</pubDate>";
            $notes.= "</item>";
        }
        $notes.= "</channel>";
        $notes.= "</rss>";
        echo $notes;
    }

    public function stock_list($output){
        require_once(MODEL . "master/company.php");
        $company = new Company();
        $company = $company->LoadById($this->userCompanyId);
        $compname = $company->CompanyName;
        $items = new Stock();
        $items = $items->Load4Excel($this->userCompanyId,$this->userCabangId,$this->userLevel);
        $this->Set("items", $items);
        $this->Set("output", $output);
        $this->Set("company_name", $compname);
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/itemjenis.php");
        require_once(MODEL . "master/contacts.php");
        $loader = null;
        $sCompanyId = $this->userCompanyId;
        $sCabangId = 0;
        $sReportType = 0;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sJenisProduk = $this->GetPostValue("JenisProduk");
            $sSupplierCode = $this->GetPostValue("SupplierCode");
            $sTypeHarga = $this->GetPostValue("TypeHarga");
            $sReportType = $this->GetPostValue("ReportType");
            $sOutput = $this->GetPostValue("Output");
            if ($sCabangId <> $this->userCabangId){
                $this->persistence->SaveState("error", "Maaf Anda tidak boleh mengakses Laporan Stock cabang ini!");
                redirect_url("inventory.stock");
            }
            // ambil data yang diperlukan
            $stock = new Stock();
            $reports = $stock->Load4Reports($sCompanyId,$sCabangId,$sJenisProduk,$sSupplierCode);
        }else{
            $sJenisProduk = "-";
            $sSupplierCode = null;
            $sTypeHarga = 0;
            $sOutput = 0;
            $sReportType = 0;
            $reports = null;
        }
        $company = new Company($this->userCompanyId);
        $cabCode = null;
        $cabName = null;
        $scabCode = null;
        $cabang = new Cabang();
        $cabang = $cabang->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $scabCode = $cabCode;

        $jenis = new ItemJenis();
        $jenis = $jenis->LoadAll();
        // kirim ke view
        $this->Set("company_name", $company->CompanyName);
        $this->Set("cabangs", $cabang);
        $this->Set("jenis", $jenis);
        $this->Set("output",$sOutput);
        $this->Set("reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userJenisProduk",$sJenisProduk);
        $this->Set("userSupplierCode",$sSupplierCode);
        $this->Set("userTypeHarga",$sTypeHarga);
        $this->Set("userReportType",$sReportType);
        $this->Set("userLevel",$this->userLevel);
        $this->Set("scabangId",$sCabangId);
        $this->Set("scabangCode",$scabCode);
    }

    public function recount(){
        set_time_limit(600);
        $stock = new Stock();
        $stock = $stock->RecountStock($this->userCabangId);
        if ($stock > 0) {
            $this->persistence->SaveState("info", sprintf("%s Data Stock berhasil diproses..", $stock));
        }else{
            $this->persistence->SaveState("info", sprintf("Data Stock sudah cocok.."));
        }
        redirect_url("inventory.stock");
    }
}
