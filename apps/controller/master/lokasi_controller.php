<?php
class LokasiController extends AppController {
	private $userUid;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/lokasi.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.kode", "display" => "Lokasi", "width" => 50);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 300);

		$settings["filters"][] = array("name" => "a.kode", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Lokasi Barang";

			if ($acl->CheckUserAccess("master.lokasi", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.lokasi/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.lokasi", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.lokasi/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih lokasi terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu lokasi.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.lokasi", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.lokasi/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih lokasi terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu lokasi.",
					"Confirm" => "Apakah anda mau menghapus data lokasi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "m_lokasi AS a";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(Lokasi $lokasi) {

		return true;
	}

	public function add() {
        $log = new UserAdmin();
        $lokasi = new Lokasi();
        if (count($this->postData) > 0) {
            $lokasi->Kode = $this->GetPostValue("Kode");
            $lokasi->Keterangan = $this->GetPostValue("Keterangan");
            $lokasi->CreatebyId = $this->userUid;
            if ($this->ValidateData($lokasi)) {
                if ($lokasi->Insert()>0) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.lokasi', 'Add New Item Satuan -> Jenis: ' . $lokasi->Kode . ' - ' . $lokasi->Keterangan, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Data Satuan: %s (%s) sudah berhasil disimpan", $lokasi->Keterangan, $lokasi->Kode));
                    redirect_url("master.lokasi");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.lokasi', 'Add New Item Satuan -> Jenis: ' . $lokasi->Kode . ' - ' . $lokasi->Keterangan, '-', 'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $this->Set("lokasi", $lokasi);
	}

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.lokasi");
        }
        $log = new UserAdmin();
        $lokasi = new Lokasi();
        if (count($this->postData) > 0) {
            $lokasi->Id = $id;
            $lokasi->Kode = $this->GetPostValue("Kode");
            $lokasi->Keterangan = $this->GetPostValue("Keterangan");
            $lokasi->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateData($lokasi)) {
                if ($lokasi->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.lokasi', 'Update Item Satuan -> Jenis: ' . $lokasi->Kode . ' - ' . $lokasi->Keterangan, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data satuan: %s (%s) sudah berhasil disimpan", $lokasi->Keterangan, $lokasi->Kode));
                    redirect_url("master.lokasi");
                } else {
                    $this->Set("error", "Gagal pada saat merubah data satuan. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $lokasi = $lokasi->LoadById($id);
            if ($lokasi == null || $lokasi->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.lokasi");
            }
        }
        $this->Set("lokasi", $lokasi);
	}

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.lokasi");
        }
        $log = new UserAdmin();
        $lokasi = new Lokasi();
        $lokasi = $lokasi->LoadById($id);
        if ($lokasi == null || $lokasi->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.lokasi");
        }
        if ($lokasi->Delete($id)) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.lokasi','Delete Item Satuan -> Jenis: '.$lokasi->Kode.' - '.$lokasi->Keterangan,'-','Success');
            $this->persistence->SaveState("info", sprintf("Satuan Barang: %s (%s) sudah dihapus", $lokasi->Keterangan, $lokasi->Kode));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.lokasi','Delete Item Satuan -> Jenis: '.$lokasi->Kode.' - '.$lokasi->Keterangan,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis kontak: %s (%s). Error: %s", $lokasi->Keterangan, $lokasi->Kode, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.lokasi");
	}
}

// End of file: lokasi_controller.php
