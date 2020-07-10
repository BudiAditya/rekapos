<?php
class MemberController extends AppController {
	private $userUid;
    private $userCabangId;
    private $userCompanyId;

	protected function Initialize() {
		require_once(MODEL . "master/member.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $this->connector = ConnectorManager::GetPool("member");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.no_member", "display" => "No Kartu", "width" => 100);
        $settings["columns"][] = array("name" => "a.nama", "display" => "Atas Nama", "width" =>200);
        $settings["columns"][] = array("name" => "a.alamat", "display" => "Alamat", "width" => 300);
        $settings["columns"][] = array("name" => "a.no_hp", "display" => "No HP", "width" => 100);
        $settings["columns"][] = array("name" => "format(a.poin_aktif,0)", "display" => "Poin Aktif", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "a.tgl_daftar", "display" => "Tgl Daftar", "width" => 90);
        $settings["columns"][] = array("name" => "a.exp_date", "display" => "Tgl Akhir", "width" => 90);
        $settings["columns"][] = array("name" => "if(a.status_member = 1,'Aktif','Non-Aktif')", "display" => "Status", "width" => 50);

		$settings["filters"][] = array("name" => "a.no_member", "display" => "Kode");
        $settings["filters"][] = array("name" => "a.nama", "display" => "Nama Member");
        $settings["filters"][] = array("name" => "a.no_hp", "display" => "No HP");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Member";

			if ($acl->CheckUserAccess("master.member", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.member/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.member", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.member/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih member terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu member.",
					"Confirm" => "");
			}
            if ($acl->CheckUserAccess("master.member", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "master.member/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Mohon memilih member terlebih dahulu sebelum proses view.\nPERHATIAN: Mohon memilih tepat satu member.",
                    "Confirm" => "");
            }
			if ($acl->CheckUserAccess("master.member", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.member/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih member terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu member.",
					"Confirm" => "Apakah anda mau menghapus data member yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("master.member", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "master.member/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("master.member", "view")) {
                $settings["actions"][] = array("Text" => "Rekap Poin Member", "Url" => "master.member/rekap","Target"=>"_blank","Class" => "bt_process", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("master.member", "edit")) {
                $settings["actions"][] = array("Text" => "Input Poin (Manual)", "Url" => "master.member/addpoin/%s", "Class" => "bt_create_new", "ReqId" => 1,
                    "Error" => "Mohon memilih member terlebih dahulu sebelum proses penginputan poin.\nPERHATIAN: Mohon memilih tepat satu data member.",
                    "Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            $settings["actions"][] = array("Text" => "History Poin Member", "Url" => "master.member/history/%s", "Class" => "bt_report", "ReqId" => 1,
                "Error" => "Mohon memilih member terlebih dahulu sebelum proses view.\nPERHATIAN: Mohon memilih tepat satu data member.",
                "Confirm" => "");
			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
            $settings["dBasePool"] = "member";
            $settings["from"] = "m_member AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.is_deleted = 0 And a.entity_id = ".$this->userCompanyId;
            } else {
                $settings["where"] = "a.entity_id = ".$this->userCompanyId;
            }
		}
		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
        $log = new UserAdmin();
        $member = new Member();
        if (count($this->postData) > 0) {
            $member->EntityId = $this->userCompanyId;
            $member->TglDaftar = $this->GetPostValue("TglDaftar");
            $member->ExpDate = $this->GetPostValue("ExpDate");
            $member->NoIdCard = $this->GetPostValue("NoIdCard");
            $member->ExpIdCard = $this->GetPostValue("ExpIdCard");
            $member->NoHp = $this->GetPostValue("NoHp");
            $member->Nama = $this->GetPostValue("Nama");
            $member->Alamat = $this->GetPostValue("Alamat");
            $member->TglLahir = $this->GetPostValue("TglLahir");
            $member->T4Lahir = $this->GetPostValue("T4Lahir");
            $member->RtRw = $this->GetPostValue("RtRw");
            $member->Desa = $this->GetPostValue("Desa");
            $member->Kecamatan = $this->GetPostValue("Kecamatan");
            $member->Agama = $this->GetPostValue("Agama");
            $member->Jkelamin = $this->GetPostValue("Jkelamin");
            $member->Pekerjaan = $this->GetPostValue("Pekerjaan");
            $member->NoStrukDaftar = $this->GetPostValue("NoStrukDaftar");
            $member->NilaiBelanjaDaftar = $this->GetPostValue("NilaiBelanjaDaftar");
            $member->KodePromoDaftar = $this->GetPostValue("KodePromoDaftar");
            $member->PoinAktif = $this->GetPostValue("PoinAktif");
            $member->StatusMember = $this->GetPostValue("StatusMember");
            $member->CabangId = $this->GetPostValue("CabangId");
            $member->CreatebyId = $this->userUid;
            if ($this->ValidateAddData($member)) {
                $member->NoMember = $member->GetMemberNo($this->userCompanyId,$member->TglDaftar);
                if ($member->Insert()>0) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.member', 'Add New Member -> No: ' . $member->NoMember . ' - ' . $member->Nama, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Data Member: %s (%s) sudah berhasil disimpan", $member->Nama, $member->NoMember));
                    redirect_url("master.member");
                } else {
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $this->Set("member", $member);
	}

    private function ValidateAddData(Member $member) {
        //validasi data disini
        $data = new Member();
        $data = $data->FindByIdCard($member->NoIdCard);
        if ($data != null){
            $this->Set("error", "Nomor KTP/SIM: " .$member->NoIdCard." sudah terdaftar!");
            return false;
        }
        $data = new Member();
        $data = $data->FindByNoHp($member->NoIdCard);
        if ($data != null){
            $this->Set("error", "Nomor HP: " .$member->NoHp." sudah terdaftar!");
            return false;
        }

        if ($member->NilaiBelanjaDaftar == 0 || $member->PoinAktif == 0){
            $this->Set("error", "Nilai Struk Belanja tidak valid!");
            return false;
        }

        return true;
    }

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.member");
        }
        $log = new UserAdmin();
        $member = new Member();
        if (count($this->postData) > 0) {
            $member->Id = $id;
            $member->EntityId = $this->userCompanyId;
            $member->NoMember = $this->GetPostValue("NoMember");
            $member->TglDaftar = $this->GetPostValue("TglDaftar");
            $member->ExpDate = $this->GetPostValue("ExpDate");
            $member->NoIdCard = $this->GetPostValue("NoIdCard");
            $member->ExpIdCard = $this->GetPostValue("ExpIdCard");
            $member->NoHp = $this->GetPostValue("NoHp");
            $member->Nama = $this->GetPostValue("Nama");
            $member->Alamat = $this->GetPostValue("Alamat");
            $member->TglLahir = $this->GetPostValue("TglLahir");
            $member->T4Lahir = $this->GetPostValue("T4Lahir");
            $member->RtRw = $this->GetPostValue("RtRw");
            $member->Desa = $this->GetPostValue("Desa");
            $member->Kecamatan = $this->GetPostValue("Kecamatan");
            $member->Agama = $this->GetPostValue("Agama");
            $member->Jkelamin = $this->GetPostValue("Jkelamin");
            $member->Pekerjaan = $this->GetPostValue("Pekerjaan");
            //$member->NoStrukDaftar = $this->GetPostValue("NoStrukDaftar");
            //$member->NilaiBelanjaDaftar = $this->GetPostValue("NilaiBelanjaDaftar");
            //$member->PoinAktif = $this->GetPostValue("PoinAktif");
            $member->StatusMember = $this->GetPostValue("StatusMember");
            $member->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateEditData($member)) {
                if ($member->Update($id)) {
                    $log = $log->UserActivityWriter($this->userCabangId, 'master.member', 'Update Member -> No: ' . $member->NoMember . ' - ' . $member->Nama, '-', 'Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data member: %s (%s) sudah berhasil disimpan", $member->Nama, $member->NoMember));
                    redirect_url("master.member");
                }
            }
        }else{
            $member = $member->LoadById($id);
            if ($member == null || $member->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.member");
            }
        }
        $this->Set("member", $member);
	}

    public function addpoin($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penginputan poin manual.");
            redirect_url("master.member");
        }
        $log = new UserAdmin();
        $nst = null;
        $nbl = 0;
        $pin = 0;
        $kpr = null;
        $cbi = 0;
        if (count($this->postData) > 0) {
            $member = new Member();
            $member = $member->LoadById($id);
            $nst = $this->GetPostValue("NoStruk");
            $nbl = $this->GetPostValue("NilaiBelanja");
            $pin = $this->GetPostValue("Poin");
            $kpr = $this->GetPostValue("KodePromo");
            $cbi = $this->GetPostValue("CabangId");
            if ($member->UpdatePoin($id,$member->NoMember,$nst,$kpr,$pin,$cbi)) {
                $log = $log->UserActivityWriter($this->userCabangId, 'master.member', 'Update Poin Member -> No: ' . $member->NoMember . ' - ' . $member->Nama, '-', 'Success');
                $this->persistence->SaveState("info", sprintf("Penambahan Poin member: %s (%s) sudah berhasil disimpan", $member->Nama, $member->NoMember));
                redirect_url("master.member");
            }else{
                $this->persistence->SaveState("error", "Maaf, Penambahan Poin member gagal!");
                redirect_url("master.member");
            }
        }else{
            $member = new Member();
            $member = $member->LoadById($id);
            if ($member == null || $member->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.member");
            }
        }
        $this->Set("member", $member);
    }

    private function ValidateEditData(Member $member) {
        //validasi data disini

        return true;
    }

    public function view($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses view.");
            redirect_url("master.member");
        }
        $member = new Member();
        $member = $member->LoadById($id);
        if ($member == null || $member->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.member");
        }
        $this->Set("member", $member);
    }

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.member");
        }
        $log = new UserAdmin();
        $member = new Member();
        $member = $member->LoadById($id);
        if ($member == null || $member->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.member");
        }
        if ($member->Void($id)) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.member','Delete Member -> No: '.$member->NoMember.' - '.$member->Nama,'-','Success');
            $this->persistence->SaveState("info", sprintf("Member: %s (%s) sudah dihapus", $member->Nama, $member->NoMember));
        }
		redirect_url("master.member");
	}

	public function getposdata($noTrx,$tglDaftar){
	    require_once (MODEL . "master/promoall.php");
        $data = new Member();
        $data = $data->GetDataPos($noTrx);
        $dtx = "ER|0";
        $ntr = 0;
        $mbl = 0;
        $npn = 0;
        $pin = 0;
        $sbl = 0;
        $kpr = null;
        $cbi = 0;
        $tglDaftar = date('Y-m-d', strtotime($tglDaftar));
        if ($data != null){
            $row = $data->FetchAssoc();
            $ntr = $row["total_transaksi"];
            $cbi = $row["cabang_id"];
            if ($row["jum_poin"] == 0) {
                //need promo kartu member
                $promo = new PromoAll();
                $promo = $promo->FindByType(10, $tglDaftar);
                if ($promo != null) {
                    $mbl = $promo->SaleAmtMinimal;
                    $pin = $promo->AmtPoint;
                    $kpr = $promo->KodePromo;
                    if ($ntr >= $mbl) {
                        $promox = new PromoAll();
                        $promox = $promox->FindByType(9, $tglDaftar);
                        if ($promox != null){
                            $sbl = $ntr - $mbl;
                            if ($pin == 0) {
                                $pin = round(($sbl / $promox->SaleAmtMinimal), 0) * $promox->AmtPoint;
                            }
                            $npn = round($sbl / $pin, 0);
                            if ($promo->IsSaleAmtKelipatan == 1) {
                                $pin = floor($ntr / $npn);
                            }
                        }
                        $dtx = "OK|" . $ntr . "|" . $pin . "|" . $kpr . "|" . $cbi;
                    } else {
                        $dtx = "ER|3";
                    }
                } else {
                    $dtx = "ER|2";
                }
            }else{
                $dtx = "ER|1";
            }
        }
        print($dtx);
    }

    public function getposdatapoin($noTrx,$tglDaftar){
        require_once (MODEL . "master/promoall.php");
        $data = new Member();
        $data = $data->GetDataPos($noTrx);
        $dtx = "ER|0";
        $ntr = 0;
        $mbl = 0;
        $npn = 0;
        $pin = 0;
        $sbl = 0;
        $cbi = 0;
        $kpr = null;
        $tglDaftar = date('Y-m-d', strtotime($tglDaftar));
        if ($data != null){
            $row = $data->FetchAssoc();
            $ntr = $row["total_transaksi"];
            $cbi = $row["cabang_id"];
            if ($row["jum_poin"] == 0) {
                //need promo kartu member
                $promo = new PromoAll();
                $promo = $promo->FindByType(9, $tglDaftar);
                if ($promo != null) {
                    $mbl = $promo->SaleAmtMinimal;
                    $pin = $promo->AmtPoint;
                    $kpr = $promo->KodePromo;
                    if ($ntr >= $mbl) {
                       $pin = floor($ntr / $mbl) * $pin;
                       $dtx = "OK|" . $ntr . "|" . $pin . "|" . $kpr . "|" . $cbi;
                    } else {
                        $dtx = "ER|3";
                    }
                } else {
                    $dtx = "ER|2";
                }
            }else{
                $dtx = "ER|1";
            }
        }
        print($dtx);
    }

    public function report(){
        // report rekonsil process
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sStatus = $this->GetPostValue("Status");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $member = new Member();
            $reports = $member->Load4Reports($this->userCompanyId,$sStatus);
        }else{
            $sJnsLaporan = 1;
            $sStatus = 1;
            $sOutput = 0;
            $reports = null;
        }
        $this->Set("Status",$sStatus);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
    }

    public function history($id){
        // report rekonsil process
       $member = new Member($id);
       $reports = null;
       if ($member != null){
           $data = new Member();
           $reports = $data->HistoryPoin($member->NoMember);
       }else{
           $this->persistence->SaveState("error", "Maaf data member tidak ditemukan atau sudah dihapus!");
           redirect_url("master.member");
       }
        $this->Set("member",$member);
        $this->Set("reports",$reports);
    }

    public function rekap(){
        set_time_limit(600);
        $data = new Member();
        $data = $data->RekapPoin($this->userCompanyId);
        if ($data > 0) {
            $this->persistence->SaveState("info", sprintf("%s Data Poin Member diproses..", $data));
        }else{
            $this->persistence->SaveState("info", sprintf("Data Poin Member sudah cocok.."));
        }
        redirect_url("master.member");
    }
}

// End of file: member_controller.php
