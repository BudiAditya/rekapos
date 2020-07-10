<?php
class PromoController extends AppController {
	private $userUid;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/promo.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.start_date", "display" => "Mulai", "width" => 60);
        $settings["columns"][] = array("name" => "a.end_date", "display" => "Akhir", "width" =>60);
        $settings["columns"][] = array("name" => "a.kode_promo", "display" => "Kode", "width" => 90);
        $settings["columns"][] = array("name" => "a.nama_promo", "display" => "Nama Promo", "width" => 280);
        $settings["columns"][] = array("name" => "upper(a.tpromo)", "display" => "Jenis", "width" => 150);
        $settings["columns"][] = array("name" => "a.kode_barang", "display" => "Kode Barang", "width" => 70);
        $settings["columns"][] = array("name" => "a.nama_barang", "display" => "Nama Barang", "width" => 200);
        $settings["columns"][] = array("name" => "format(a.qty1,0)", "display" => "Min QTY", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.item_amt_minimal,0)", "display" => "Min Nilai Barang", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.sale_amt_minimal,0)", "display" => "Min Total Belanja", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.promo_status = 1,'Aktif','Non-Aktif')", "display" => "Status", "width" => 50);

		$settings["filters"][] = array("name" => "a.kode_promo", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.nama_promo", "display" => "Nama Promo");
        $settings["filters"][] = array("name" => "a.type_promo", "display" => "Jenis");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Promo Penjualan";

			if ($acl->CheckUserAccess("master.promo", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.promo/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.promo", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.promo/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih promo terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu promo.",
					"Confirm" => "");
			}
            if ($acl->CheckUserAccess("master.promo", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "master.promo/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Mohon memilih promo terlebih dahulu sebelum proses view.\nPERHATIAN: Mohon memilih tepat satu promo.",
                    "Confirm" => "");
            }
			if ($acl->CheckUserAccess("master.promo", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.promo/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih promo terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu promo.",
					"Confirm" => "Apakah anda mau menghapus data promo yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
            $settings["from"] = "vw_m_promo AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.promo_status = 1 And a.is_deleted = 0 And a.cabang_id = ".$this->userCabangId;
            } else {
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = ".$this->userCabangId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(Promo $promo) {
        //validasi data disini
        if (($promo->TypePromo > 0 && $promo->TypePromo < 4) || $promo->TypePromo == 11 || $promo->TypePromo == 12) {
            if ($promo->KodeBarang == "") {
                $this->Set("error", "Kode Barang belum diisi..");
                return false;
            }
            if ($promo->Qty1 < 1) {
                $this->Set("error", "QTY Minimal belum diisi..");
                return false;
            }
        }elseif (($promo->TypePromo > 3 && $promo->TypePromo < 6) || $promo->TypePromo == 13 || $promo->TypePromo == 14) {
            if ($promo->KodeBarang == "") {
                $this->Set("error", "Kode Barang belum diisi..");
                return false;
            }
            if ($promo->ItemAmtMinimal < 1) {
                $this->Set("error", "Belanja Barang Minimal belum diisi..");
                return false;
            }
        }elseif (($promo->TypePromo > 6 && $promo->TypePromo < 11) || $promo->TypePromo == 15 || $promo->TypePromo == 16) {
            if ($promo->SaleAmtMinimal < 1) {
                $this->Set("error", "Total Belanja Minimal belum diisi..");
                return false;
            }
        }
		return true;
	}

	public function add() {
        $log = new UserAdmin();
        $promo = new Promo();
        if (count($this->postData) > 0) {
            $promo->CabangId = $this->userCabangId;
            $promo->TypePromo = $this->GetPostValue("TypePromo");
            $promo->KodePromo = $this->GetPostValue("KodePromo");
            $promo->NamaPromo = $this->GetPostValue("NamaPromo");
            $promo->StartDate = $this->GetPostValue("StartDate");
            $promo->StartTime = $this->GetPostValue("StartTime");
            $promo->EndDate = $this->GetPostValue("EndDate");
            $promo->EndTime = $this->GetPostValue("EndTime");
            $promo->KodeBarang = $this->GetPostValue("KodeBarang");
            $promo->KodeBonus = $this->GetPostValue("KodeBonus");
            $promo->HargaBarang = $this->GetPostValue("HargaBarang");
            $promo->HargaBonus = $this->GetPostValue("HargaBonus");
            $promo->Qty1 = $this->GetPostValue("Qty1");
            if (isset($this->postData["IsKelipatan"]) && $promo->Qty1 > 0) {
                $promo->IsKelipatan = 1;
            }else{
                $promo->IsKelipatan = 0;
            }
            $promo->ItemAmtMinimal = $this->GetPostValue("ItemAmtMinimal");
            if (isset($this->postData["IsItemAmtKelipatan"]) && $promo->ItemAmtMinimal > 0) {
                $promo->IsItemAmtKelipatan = 1;
            }else{
                $promo->IsItemAmtKelipatan = 0;
            }
            $promo->SaleAmtMinimal = $this->GetPostValue("SaleAmtMinimal");
            if (isset($this->postData["IsSaleAmtKelipatan"]) && $promo->SaleAmtMinimal > 0) {
                $promo->IsSaleAmtKelipatan = 1;
            }else{
                $promo->IsSaleAmtKelipatan = 0;
            }
            $promo->PctDiskon = $this->GetPostValue("PctDiskon");
            $promo->AmtDiskon = $this->GetPostValue("AmtDiskon");
            $promo->AmtPoint = $this->GetPostValue("AmtPoint");
            $promo->QtyBonus = $this->GetPostValue("QtyBonus");
            $promo->PromoStatus = $this->GetPostValue("PromoStatus");
            $promo->IsMemberOnly = $this->GetPostValue("IsMemberOnly");
            $promo->CreatebyId = $this->userUid;
            if ($this->ValidateData($promo)) {
                $promo->KodePromo = $promo->GetPromoDocNo();
                if ($promo->Insert()>0) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.promo', 'Add New Promo -> Kode: ' . $promo->KodePromo . ' - ' . $promo->NamaPromo, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Data Promo: %s (%s) sudah berhasil disimpan", $promo->NamaPromo, $promo->KodePromo));
                    redirect_url("master.promo");
                } else {
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $loader = new Promo();
        $tpromo = $loader->LoadTypePromo();
        $this->Set("tpromo", $tpromo);
        $this->Set("promo", $promo);
	}

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.promo");
        }
        $log = new UserAdmin();
        $promo = new Promo();
        if (count($this->postData) > 0) {
            $promo->Id = $id;
            $promo->CabangId = $this->userCabangId;
            $promo->TypePromo = $this->GetPostValue("TypePromo");
            $promo->KodePromo = $this->GetPostValue("KodePromo");
            $promo->NamaPromo = $this->GetPostValue("NamaPromo");
            $promo->StartDate = $this->GetPostValue("StartDate");
            $promo->StartTime = $this->GetPostValue("StartTime");
            $promo->EndDate = $this->GetPostValue("EndDate");
            $promo->EndTime = $this->GetPostValue("EndTime");
            $promo->KodeBarang = $this->GetPostValue("KodeBarang");
            $promo->KodeBonus = $this->GetPostValue("KodeBonus");
            $promo->HargaBarang = $this->GetPostValue("HargaBarang");
            $promo->HargaBonus = $this->GetPostValue("HargaBonus");
            $promo->Qty1 = $this->GetPostValue("Qty1");
            if (isset($this->postData["IsKelipatan"]) && $promo->Qty1 > 0) {
                $promo->IsKelipatan = 1;
            }else{
                $promo->IsKelipatan = 0;
            }
            $promo->ItemAmtMinimal = $this->GetPostValue("ItemAmtMinimal");
            if (isset($this->postData["IsItemAmtKelipatan"]) && $promo->ItemAmtMinimal > 0) {
                $promo->IsItemAmtKelipatan = 1;
            }else{
                $promo->IsItemAmtKelipatan = 0;
            }
            $promo->SaleAmtMinimal = $this->GetPostValue("SaleAmtMinimal");
            if (isset($this->postData["IsSaleAmtKelipatan"]) && $promo->SaleAmtMinimal > 0) {
                $promo->IsSaleAmtKelipatan = 1;
            }else{
                $promo->IsSaleAmtKelipatan = 0;
            }
            $promo->PctDiskon = $this->GetPostValue("PctDiskon");
            $promo->AmtDiskon = $this->GetPostValue("AmtDiskon");
            $promo->AmtPoint = $this->GetPostValue("AmtPoint");
            $promo->QtyBonus = $this->GetPostValue("QtyBonus");
            $promo->PromoStatus = $this->GetPostValue("PromoStatus");
            $promo->IsMemberOnly = $this->GetPostValue("IsMemberOnly");
            $promo->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateData($promo)) {
                if ($promo->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.promo', 'Update Promo -> Jenis: ' . $promo->KodePromo . ' - ' . $promo->NamaPromo, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data promo: %s (%s) sudah berhasil disimpan", $promo->NamaPromo, $promo->KodePromo));
                    redirect_url("master.promo");
                } else {
                    $this->Set("error", "Gagal pada saat merubah data promo. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $promo = $promo->LoadById($id);
            if ($promo == null || $promo->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.promo");
            }
        }
        $loader = new Promo();
        $tpromo = $loader->LoadTypePromo();
        $this->Set("tpromo", $tpromo);
        $this->Set("promo", $promo);
	}

    public function view($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses view.");
            redirect_url("master.promo");
        }
        $log = new UserAdmin();
        $promo = new Promo();
        $promo = $promo->LoadById($id);
        if ($promo == null || $promo->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.promo");
        }
        $loader = new Promo();
        $tpromo = $loader->LoadTypePromo();
        $this->Set("tpromo", $tpromo);
        $this->Set("promo", $promo);
    }

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.promo");
        }
        $log = new UserAdmin();
        $promo = new Promo();
        $promo = $promo->LoadById($id);
        if ($promo == null || $promo->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.promo");
        }
        if ($promo->Hapus($id)) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.promo','Delete Promo -> Jenis: '.$promo->KodePromo.' - '.$promo->NamaPromo,'-','Success');
            $this->persistence->SaveState("info", sprintf("Promo: %s (%s) sudah dihapus", $promo->NamaPromo, $promo->KodePromo));
        } else {
            $this->persistence->SaveState("error", sprintf("Gagal menghapus Promo: %s (%s). Error: %s", $promo->NamaPromo, $promo->KodePromo, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.promo");
	}

    public function getitemprices_plain($bkode){
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $items Items  */
            $items = new Items();
            $items = $items->LoadByKode($bkode);
            if ($items != null){
                $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatkecil.'|'.$items->Bhargabeli.'|'.$items->Bhargajual1.'|'.$items->Bbarcode.'|'.$items->Bisaktif;
            }
        }
        print $ret;
    }

    public function getitemprices_plain_bcode($bcode){
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bcode != null || $bcode != ''){
            /** @var $items Items  */
            $items = new Items();
            $items = $items->FindByBarCode($bcode);
            $setprice = null;
            if ($items != null){
                $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatkecil.'|'.$items->Bhargabeli.'|'.$items->Bhargajual1.'|'.$items->Bkode.'|'.$items->Bisaktif;
            }
        }
        print $ret;
    }
}

// End of file: promo_controller.php
