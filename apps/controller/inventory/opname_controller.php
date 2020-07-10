<?php

class OpnameController extends AppController {
	private $userUid;
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "inventory/opname.php");
        require_once(MODEL . "master/user_admin.php");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
        // index script here
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/warehouse.php");
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
        $loader = new Warehouse();
        $gudangs = $loader->LoadByCabangId($this->userCabangId);
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("gudangs", $gudangs);
	}

	private function ValidateData(Opname $opname) {
		return true;
	}

	public function get_data(){
        /*Default request pager params dari jeasyUI*/
        $cabangId = $this->userCabangId;
        $opname = new Opname();
        $offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
        $sfield = isset($_POST['sfield']) ? strval($_POST['sfield']) : '';
        $scontent = isset($_POST['scontent']) ? strval($_POST['scontent']) : '';
        $offset = ($offset-1)*$limit;
        $data   = $opname->GetData($cabangId,$offset,$limit,$sfield,$scontent,$sort,$order);
        echo json_encode($data); //return nya json
    }

    public function save() {
        require_once(MODEL . "master/items.php");
        require_once(MODEL . "master/warehouse.php");
        $opname = new Opname();
        $log = new UserAdmin();
        $opname->ItemId = $this->GetPostValue("aItemId");
        $items = new Items($opname->ItemId);
        if ($items == null){
            echo json_encode(array('errorMsg'=>'Data Barang tidak ditemukan'));
        }else{
            $opname->ItemCode = $items->Bkode;
            $opname->BarCode = $items->Bbarcode;
            $opname->CabangId = $this->userCabangId;
            $opname->WarehouseId = $this->GetPostValue("aGudangId");
            $opname->OpnTime = $this->GetPostValue("aOpnTime");
            $opname->OpnQty = $this->GetPostValue("aOpnQty");
            $opname->IpAddress = $_SERVER['REMOTE_ADDR'];
            $opname->OpnStatus = 0;
            if ($this->ValidateData($opname)) {
                $opname->OpnNo = $opname->GetOpnameDocNo();
                $opname->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $opname->Insert();
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.opname','Add Stock Opname - Item Code: '.$opname->ItemCode.' = '.$opname->OpnQty,$opname->OpnNo,'Success');
                    echo json_encode(array(
                        'id' => $rs,
                        'item_id' => $opname->ItemId,
                        'item_code' => $opname->ItemCode
                    ));
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.opname','Add Stock Opname - Item Code: '.$opname->ItemCode.' = '.$opname->OpnQty,$opname->OpnNo,'Failed');
                    echo json_encode(array('errorMsg'=>'Gagal proses simpan data..'));
                }
            }
        }
    }

    public function hapus($id = null) {
        $log = new UserAdmin();
        $opname = new Opname();
        $opname = $opname->LoadById($id);
        if ($opname == null) {
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
        $rs = $opname->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.opname','Delete Stock Opname - Date: '.date('Y-m-d',$opname->OpnTime).' Item Code: '.$opname->ItemCode.' = '.$opname->OpnQty,$opname->OpnNo,'Success');
            echo json_encode(array('success'=>true));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.opname','Delete Stock Opname - Date: '.date('Y-m-d',$opname->OpnTime).' Item Code: '.$opname->ItemCode.' = '.$opname->OpnQty,$opname->OpnNo,'Failed');
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/warehouse.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sGudangId = $this->GetPostValue("GudangId");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $opname = new Opname();
            if ($sJnsLaporan == 1) {
                $reports = $opname->Load4Reports($this->userCabangId, $sGudangId, $sStartDate, $sEndDate);
            }else{
                $reports = $opname->LoadRekap4Reports($this->userCabangId, $sGudangId, $sStartDate, $sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sGudangId = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sJnsLaporan = 1;
            $sOutput = 0;
            $reports = null;
        }
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
        $loader = new Warehouse();
        $gudang = $loader->LoadByEntityId($this->userCompanyId);
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("CabangId",$sCabangId);
        $this->Set("GudangId",$sGudangId);
        $this->Set("cabangs", $cabang);
        $this->Set("gudangs", $gudang);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
        $this->Set("company_name", $company->CompanyName);
    }
}

// End of file: koreksi_controller.php
