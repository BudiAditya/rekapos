<?php
class TaxController extends AppController {
	private $userUid;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/tax.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.tax_code", "display" => "Kode", "width" => 80);
		$settings["columns"][] = array("name" => "a.tax_name", "display" => "Nama Pajak", "width" => 200);
        $settings["columns"][] = array("name" => "a.tax_rate", "display" => "Tarif (%)", "width" => 80);

		$settings["filters"][] = array("name" => "a.tax_code", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.tax_name", "display" => "Nama Pajak");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Pajak";

			if ($acl->CheckUserAccess("master.tax", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.tax/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.tax", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.tax/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih data terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu data.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.tax", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.tax/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih data terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu tax.",
					"Confirm" => "Apakah anda mau menghapus data gudang yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 2;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "m_pajak AS a";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(Tax $tax) {

		return true;
	}

	public function add() {
        $tax = new Tax();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $tax->TaxCode = $this->GetPostValue("TaxCode");
            $tax->TaxName = $this->GetPostValue("TaxName");
            $tax->TaxRate = $this->GetPostValue("TaxRate");
            $tax->TaxMode = $this->GetPostValue("TaxMode");
            if ($this->ValidateData($tax)) {
                $tax->CreatebyId = $this->userUid;
                $rs = $tax->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.tax','Add New Item TaxCode -> TaxCode: '.$tax->TaxCode.' - '.$tax->TaxName,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Data TaxCode: %s (%s) sudah berhasil disimpan", $tax->TaxName, $tax->TaxCode));
                    redirect_url("master.tax");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.tax','Add New Item TaxCode -> TaxCode: '.$tax->TaxCode.' - '.$tax->TaxName,'-','Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $this->Set("tax", $tax);
	}

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.tax");
        }
        $log = new UserAdmin();
        $tax = new Tax();
        if (count($this->postData) > 0) {
            $tax->Id = $id;
            $tax->TaxCode = $this->GetPostValue("TaxCode");
            $tax->TaxName = $this->GetPostValue("TaxName");
            $tax->TaxRate = $this->GetPostValue("TaxRate");
            $tax->TaxMode = $this->GetPostValue("TaxMode");
            if ($this->ValidateData($tax)) {
                $tax->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $tax->Update($id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.tax','Update Item TaxCode -> TaxCode: '.$tax->TaxCode.' - '.$tax->TaxName,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data satuan: %s (%s) sudah berhasil disimpan", $tax->TaxName, $tax->TaxCode));
                    redirect_url("master.tax");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.tax','Update Item TaxCode -> TaxCode: '.$tax->TaxCode.' - '.$tax->TaxName,'-','Failed');
                    $this->Set("error", "Gagal pada saat merubah data satuan. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $tax = $tax->LoadById($id);
            if ($tax == null) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.tax");
            }
        }
        $this->Set("tax", $tax);
	}

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.tax");
        }
        $log = new UserAdmin();
        $tax = new Tax();
        $tax = $tax->LoadById($id);
        if ($tax == null) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.tax");
        }
        $rs = $tax->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.tax','Delete Item TaxCode -> TaxCode: '.$tax->TaxCode.' - '.$tax->TaxName,'-','Success');
            $this->persistence->SaveState("info", sprintf("TaxCode Barang: %s (%s) sudah dihapus", $tax->TaxName, $tax->TaxCode));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.tax','Delete Item TaxCode -> TaxCode: '.$tax->TaxCode.' - '.$tax->TaxName,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis kontak: %s (%s). Error: %s", $tax->TaxName, $tax->TaxCode, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.tax");
	}
}

// End of file: tax_controller.php
