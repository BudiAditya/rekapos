<?php
class LoyaltyController extends AppController {
	private $userUid;
    private $userCabangId;
    private $userCompanyId;

	protected function Initialize() {
		require_once(MODEL . "master/loyalty.php");
        require_once(MODEL . "master/user_admin.php");
        $this->connector = ConnectorManager::GetPool("member");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.loyalty_code", "display" => "Kode", "width" => 150);
        $settings["columns"][] = array("name" => "a.program_name", "display" => "Nama Program", "width" => 350);
        $settings["columns"][] = array("name" => "a.start_date", "display" => "Mulai Tgl", "width" => 100);
        $settings["columns"][] = array("name" => "a.end_date", "display" => "S/D Tgl", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.l_status = 1,'Aktif','Non-Aktif')", "display" => "Status", "width" => 80);

		$settings["filters"][] = array("name" => "a.loyalty_code", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.program_name", "display" => "Nama Program");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Loyalty Program";

			if ($acl->CheckUserAccess("master.loyalty", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.loyalty/add/0", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.loyalty", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.loyalty/add/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih loyalty terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu data.",
					"Confirm" => "");
			}
            if ($acl->CheckUserAccess("master.loyalty", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "master.loyalty/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Mohon memilih loyalty terlebih dahulu sebelum proses view.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "");
            }
			if ($acl->CheckUserAccess("master.loyalty", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.loyalty/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih loyalty terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu data.",
					"Confirm" => "Apakah anda mau menghapus data loyalty yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("master.loyalty", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "master.loyalty/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
            }
			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
            $settings["dBasePool"] = "member";
			$settings["from"] = "m_loyalty_master AS a ";
            $settings["where"] = "a.entity_id = ".$this->userCompanyId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(Loyalty $loyalty) {
		return true;
	}

	public function add($id = 0){
        $acl = AclManager::GetInstance();
        $loyalty = new Loyalty();
        if ($id > 0){
            $loyalty = $loyalty->LoadById($id);
        }
        // load details
        $loyalty->LoadDetails();
        //sent to view
        $this->Set("loyalty", $loyalty);
        $this->Set("acl", $acl);
    }

    public function view($id = 0){
        $loyalty = new Loyalty();
        $loyalty = $loyalty->LoadById($id);
        // load details
        $loyalty->LoadDetails();
        //sent to view
        $this->Set("loyalty", $loyalty);
    }

    public function add_master($id = 0) {
        $loyalty = new Loyalty();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $loyalty->Id = $id;
            $loyalty->CabangId = $this->userCabangId;
            $loyalty->EntityId = $this->userCompanyId;
            $loyalty->StartDate = date('Y-m-d',strtotime($this->GetPostValue("StartDate")));
            $loyalty->EndDate = date('Y-m-d',strtotime($this->GetPostValue("EndDate")));
            $loyalty->ProgramName = $this->GetPostValue("ProgramName");
            $loyalty->LoyaltyCode = $this->GetPostValue("LoyaltyCode");
            $loyalty->Lstatus = $this->GetPostValue("Lstatus");
            if ($loyalty->Id == 0) {
                $loyalty->LoyaltyCode = $loyalty->GetLoyaltyDocNo();
                $loyalty->CreatebyId = $this->userUid;
                $rs = $loyalty->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.loyalty','Add New Program',$loyalty->LoyaltyCode,'Success');
                    printf("OK|A|%d|%s",$loyalty->Id,$loyalty->LoyaltyCode);
                }else{
                    printf("ER|A|%d",$loyalty->Id);
                }
            }else{
                $loyalty->UpdatebyId = $this->userUid;
                $rs = $loyalty->Update($loyalty->Id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.loyalty','Update Program',$loyalty->LoyaltyCode,'Success');
                    printf("OK|U|%d|%s",$loyalty->Id,$loyalty->LoyaltyCode);
                }else{
                    printf("ER|U|%d",$loyalty->Id);
                }
            }
        }else{
            printf("ER|X|%d",$id);
        }
    }

    public function add_detail($loyaltyId = 0) {
        $loyalty = new Loyalty($loyaltyId);
        $detail = new LoyaltyDetail();
        $detail->LoyaltyId = $loyaltyId;
        $detail->LoyaltyCode = $loyalty->LoyaltyCode;
        if (count($this->postData) > 0) {
            $detail->Hadiah = $this->GetPostValue("Hadiah");
            $detail->MinPoin = $this->GetPostValue("MinPoin");
            $detail->Qty = $this->GetPostValue("Qty");
            $detail->Nilai = $this->GetPostValue("Nilai");
            $rs = $detail->Insert();
            if ($rs == 1) {
                printf("OK|A|%d",$detail->Id);
            }else{
                printf("ER|A|%d",$loyaltyId);
            }
        }else{
            printf("ER|X|%d",$loyaltyId);
        }
    }

    public function del_detail($id = 0) {
        $detail = new LoyaltyDetail();
        $rs = $detail->Delete($id);
        if ($rs == 1) {
            print("OK|Hapus data berhasil!");
        }else{
            print("ER|Hapus data gagal!");
        }
    }

    public function report(){
        print("<h3>Sorry, Report under programming!</h3>");
    }
}

// End of file: loyalty_controller.php
