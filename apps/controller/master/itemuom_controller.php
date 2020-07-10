<?php
class ItemUomController extends AppController {
	private $userUid;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/itemuom.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.sid", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.skode", "display" => "Kode", "width" => 50);
        $settings["columns"][] = array("name" => "a.snama", "display" => "Satuan", "width" => 200);

		$settings["filters"][] = array("name" => "a.skode", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.snama", "display" => "Satuan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Satuan Barang";

			if ($acl->CheckUserAccess("master.itemuom", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.itemuom/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.itemuom", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.itemuom/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih itemuom terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu itemuom.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.itemuom", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.itemuom/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih itemuom terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu itemuom.",
					"Confirm" => "Apakah anda mau menghapus data itemuom yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "m_satuan AS a";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(ItemUom $itemuom) {

		return true;
	}

	public function add() {
        $log = new UserAdmin();
        $itemuom = new ItemUom();
        if (count($this->postData) > 0) {
            $itemuom->Skode = $this->GetPostValue("Skode");
            $itemuom->Snama = $this->GetPostValue("Snama");
            $itemuom->CreatebyId = $this->userUid;
            if ($this->ValidateData($itemuom)) {
                if ($itemuom->Insert()>0) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.itemuom', 'Add New Item Satuan -> Jenis: ' . $itemuom->Skode . ' - ' . $itemuom->Snama, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Data Satuan: %s (%s) sudah berhasil disimpan", $itemuom->Snama, $itemuom->Skode));
                    redirect_url("master.itemuom");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.itemuom', 'Add New Item Satuan -> Jenis: ' . $itemuom->Skode . ' - ' . $itemuom->Snama, '-', 'Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $this->Set("itemuom", $itemuom);
	}

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.itemuom");
        }
        $log = new UserAdmin();
        $itemuom = new ItemUom();
        if (count($this->postData) > 0) {
            $itemuom->Sid = $id;
            $itemuom->Skode = $this->GetPostValue("Skode");
            $itemuom->Snama = $this->GetPostValue("Snama");
            $itemuom->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateData($itemuom)) {
                if ($itemuom->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.itemuom', 'Update Item Satuan -> Jenis: ' . $itemuom->Skode . ' - ' . $itemuom->Snama, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data satuan: %s (%s) sudah berhasil disimpan", $itemuom->Snama, $itemuom->Skode));
                    redirect_url("master.itemuom");
                } else {
                    $this->Set("error", "Gagal pada saat merubah data satuan. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $itemuom = $itemuom->LoadById($id);
            if ($itemuom == null || $itemuom->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.itemuom");
            }
        }
        $this->Set("itemuom", $itemuom);
	}

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.itemuom");
        }
        $log = new UserAdmin();
        $itemuom = new ItemUom();
        $itemuom = $itemuom->LoadById($id);
        if ($itemuom == null || $itemuom->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.itemuom");
        }
        if ($itemuom->Hapus($id)) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Delete Item Satuan -> Jenis: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Success');
            $this->persistence->SaveState("info", sprintf("Satuan Barang: %s (%s) sudah dihapus", $itemuom->Snama, $itemuom->Skode));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Delete Item Satuan -> Jenis: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis kontak: %s (%s). Error: %s", $itemuom->Snama, $itemuom->Skode, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.itemuom");
	}
}

// End of file: itemuom_controller.php
