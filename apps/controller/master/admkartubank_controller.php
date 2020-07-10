<?php
class AdmKartuBankController extends AppController {
	private $userUid;
	private $userEntityId;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/admkartubank.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userEntityId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.nama_kartu", "display" => "Nama Kartu", "width" => 150);
        $settings["columns"][] = array("name" => "if(a.jns_kartu = 1,'Debit','Kredit')", "display" => "Jenis Kartu", "width" => 80);
        $settings["columns"][] = array("name" => "a.nama_bank", "display" => "Bank", "width" => 200);
        $settings["columns"][] = array("name" => "format(a.minimal,0)", "display" => "Minimum", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.by_admin_pct,1)", "display" => "Admin (%)", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.by_admin,0)", "display" => "Admin (Rp)", "width" => 80, "align" => "right");

		$settings["filters"][] = array("name" => "if(a.jns_kartu = 1,'Debit','Kredit')", "display" => "Jenis Kartu");
        $settings["filters"][] = array("name" => "a.nama_kartu", "display" => "Nama Kartu");
        $settings["filters"][] = array("name" => "a.nama_bank", "display" => "Nama Bank");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Biaya Admin Kartu Debit & Kredit";

			if ($acl->CheckUserAccess("master.admkartubank", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.admkartubank/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.admkartubank", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.admkartubank/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih admkartubank terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu admkartubank.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.admkartubank", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.admkartubank/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih admkartubank terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu admkartubank.",
					"Confirm" => "Apakah anda mau menghapus data admkartubank yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "m_kartu_bank AS a";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(AdmKartuBank $admkartubank) {

		return true;
	}

	public function add() {
        $log = new UserAdmin();
        $admkartubank = new AdmKartuBank();
        if (count($this->postData) > 0) {
            $admkartubank->EntityId = $this->userEntityId;
            $admkartubank->JnsKartu = $this->GetPostValue("JnsKartu");
            $admkartubank->NamaKartu = $this->GetPostValue("NamaKartu");
            $admkartubank->NamaBank = $this->GetPostValue("NamaBank");
            $admkartubank->Minimal = $this->GetPostValue("Minimal");
            $admkartubank->ByAdmin = $this->GetPostValue("ByAdmin");
            $admkartubank->ByAdminPct = $this->GetPostValue("ByAdminPct");
            $admkartubank->CreatebyId = $this->userUid;
            if ($this->ValidateData($admkartubank)) {
                if ($admkartubank->Insert()>0) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.admkartubank', 'Add New Admin Kartu -> Jenis: ' . $admkartubank->JnsKartu . ' - ' . $admkartubank->NamaKartu, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Data Satuan: %s (%s) sudah berhasil disimpan", $admkartubank->NamaKartu, $admkartubank->JnsKartu));
                    redirect_url("master.admkartubank");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.admkartubank', 'Add New Admin Kartu -> Jenis: ' . $admkartubank->JnsKartu . ' - ' . $admkartubank->NamaKartu, '-', 'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $this->Set("admkartubank", $admkartubank);
	}

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.admkartubank");
        }
        $log = new UserAdmin();
        $admkartubank = new AdmKartuBank();
        if (count($this->postData) > 0) {
            $admkartubank->EntityId = $this->userEntityId;
            $admkartubank->JnsKartu = $this->GetPostValue("JnsKartu");
            $admkartubank->NamaKartu = $this->GetPostValue("NamaKartu");
            $admkartubank->NamaBank = $this->GetPostValue("NamaBank");
            $admkartubank->Minimal = $this->GetPostValue("Minimal");
            $admkartubank->ByAdmin = $this->GetPostValue("ByAdmin");
            $admkartubank->ByAdminPct = $this->GetPostValue("ByAdminPct");
            $admkartubank->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateData($admkartubank)) {
                if ($admkartubank->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.admkartubank', 'Update Admin Kartu -> Jenis: ' . $admkartubank->JnsKartu . ' - ' . $admkartubank->NamaKartu, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data satuan: %s (%s) sudah berhasil disimpan", $admkartubank->NamaKartu, $admkartubank->JnsKartu));
                    redirect_url("master.admkartubank");
                } else {
                    $this->Set("error", "Gagal pada saat merubah data satuan. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $admkartubank = $admkartubank->LoadById($id);
            if ($admkartubank == null) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.admkartubank");
            }
        }
        $this->Set("admkartubank", $admkartubank);
	}

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.admkartubank");
        }
        $log = new UserAdmin();
        $admkartubank = new AdmKartuBank();
        $admkartubank = $admkartubank->LoadById($id);
        if ($admkartubank == null) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.admkartubank");
        }
        if ($admkartubank->Delete($id)) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.admkartubank','Delete Admin Kartu -> Jenis: '.$admkartubank->JnsKartu.' - '.$admkartubank->NamaKartu,'-','Success');
            $this->persistence->SaveState("info", sprintf("Satuan Barang: %s (%s) sudah dihapus", $admkartubank->NamaKartu, $admkartubank->JnsKartu));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.admkartubank','Delete Admin Kartu -> Jenis: '.$admkartubank->JnsKartu.' - '.$admkartubank->NamaKartu,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis kontak: %s (%s). Error: %s", $admkartubank->NamaKartu, $admkartubank->JnsKartu, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.admkartubank");
	}
}

// End of file: admkartubank_controller.php
