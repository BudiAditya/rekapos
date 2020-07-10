<?php
class Member extends EntityBase {
	public $Id;
	public $IsDeleted = 0;
	public $EntityId = 1;
	public $TglDaftar;
	public $NoMember;
	public $NoIdCard;
	public $T4Lahir;
	public $TglLahir;
	public $Jkelamin;
	public $Nama;
	public $Alamat;
	public $RtRw;
	public $Desa;
	public $Kecamatan;
	public $Propinsi;
	public $Agama;
	public $Pekerjaan;
	public $ExpIdCard;
    public $StatusMember = 0;
    public $ExpDate;
    public $NoHp;
    public $PoinAktif = 0;
	public $PoinReimburse = 0;
	public $PoinHangus = 0;
	public $NoStrukDaftar;
	public $NilaiBelanjaDaftar = 0;
	public $KodePromoDaftar = "-";
    public $CreatebyId = 0;
    public $UpdatebyId = 0;
    public $ApprovebyId = 0;
    public $CreateTime;
    public $UpdateTime;
    public $ApproveTime;
    //helper
    public $CabangId = 0;

	public function __construct($id = null) {
		parent::__construct();
        $this->connector = ConnectorManager::GetPool("member");
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
        $this->Id = $row["id"];
        $this->IsDeleted = $row["is_deleted"] == 1;
        $this->EntityId = $row["entity_id"];
        $this->TglDaftar = strtotime($row["tgl_daftar"]);
        $this->NoMember = $row["no_member"];
        $this->NoIdCard = $row["no_idcard"];
        $this->T4Lahir = $row["t4_lahir"];
        $this->TglLahir = strtotime($row["tgl_lahir"]);
        $this->Jkelamin = $row["jkelamin"];
        $this->Nama = $row["nama"];
        $this->Alamat = $row["alamat"];
        $this->RtRw = $row["rt_rw"];
        $this->Desa = $row["desa"];
        $this->Kecamatan = $row["kecamatan"];
        $this->Propinsi = $row["propinsi"];
        $this->Agama = $row["agama"];
        $this->Pekerjaan = $row["pekerjaan"];
        $this->ExpIdCard = strtotime($row["exp_idcard"]);
        $this->StatusMember = $row["status_member"];
        $this->ExpDate = strtotime($row["exp_date"]);
        $this->NoHp = $row["no_hp"];
        $this->PoinAktif = $row["poin_aktif"];
        $this->PoinReimburse = $row["poin_reimburse"];
        $this->PoinHangus = $row["poin_hangus"];
        $this->NoStrukDaftar = $row["no_struk_daftar"];
        $this->NilaiBelanjaDaftar = $row["nilai_belanja_daftar"];
        $this->KodePromoDaftar = $row["kode_promo_daftar"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->ApprovebyId = $row["approveby_id"];
        $this->CreateTime = strtotime($row["create_time"]);
        $this->UpdateTime = strtotime($row["update_time"]);
        $this->ApproveTime = strtotime($row["approve_time"]);
	}

    public function FormatTglDaftar($format = HUMAN_DATE) {
        return is_int($this->TglDaftar) ? date($format, $this->TglDaftar) : date($format, strtotime(date('Y-m-d')));
    }

    public function FormatExpIdCard($format = HUMAN_DATE) {
        return is_int($this->ExpIdCard) ? date($format, $this->ExpIdCard) : null;
    }

    public function FormatExpDate($format = HUMAN_DATE) {
        return is_int($this->ExpDate) ? date($format, $this->ExpDate) : null;
    }

    public function FormatTglLahir($format = HUMAN_DATE) {
        return is_int($this->TglLahir) ? date($format, $this->TglLahir) : null;
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.no_member") {
		$this->connector->CommandText = "SELECT a.* FROM m_member AS a Where a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Member();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Location
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_member AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByNoMember($noMember) {
        $this->connector->CommandText = "SELECT a.* FROM m_member AS a WHERE a.no_member = ?nom";
        $this->connector->AddParameter("?nom", $noMember);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByNoHp($noHp) {
        $this->connector->CommandText = "SELECT a.* FROM m_member AS a WHERE a.no_hp = ?noh";
        $this->connector->AddParameter("?noh", $noHp);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByIdCard($noId) {
        $this->connector->CommandText = "SELECT a.* FROM m_member AS a WHERE a.no_idcard = ?nid";
        $this->connector->AddParameter("?nid", $noId);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

	/**
	 * @param int $id
	 * @return Location
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_member(kode_promo_daftar,entity_id, tgl_daftar, no_member, no_idcard, nama, t4_lahir, tgl_lahir, jkelamin, rt_rw, desa, kecamatan, propinsi, agama, pekerjaan, status_member, poin_aktif, poin_reimburse, poin_hangus, no_struk_daftar, nilai_belanja_daftar, createby_id, create_time, alamat, exp_date, no_hp, exp_idcard) VALUES(?kode_promo_daftar,?entity_id, ?tgl_daftar, ?no_member, ?no_idcard, ?nama, ?t4_lahir, ?tgl_lahir, ?jkelamin, ?rt_rw, ?desa, ?kecamatan, ?propinsi, ?agama, ?pekerjaan, ?status_member, ?poin_aktif, ?poin_reimburse, ?poin_hangus, ?no_struk_daftar, ?nilai_belanja_daftar, ?createby_id, now(), ?alamat, ?exp_date, ?no_hp, ?exp_idcard)';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?tgl_daftar", $this->TglDaftar);
        $this->connector->AddParameter("?no_member", $this->NoMember, "char");
        $this->connector->AddParameter("?no_idcard", $this->NoIdCard, "char");
        $this->connector->AddParameter("?t4_lahir", $this->T4Lahir);
        $this->connector->AddParameter("?tgl_lahir", $this->TglLahir);
        $this->connector->AddParameter("?jkelamin", $this->Jkelamin);
        $this->connector->AddParameter("?nama", $this->Nama);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?rt_rw", $this->RtRw);
        $this->connector->AddParameter("?desa", $this->Desa);
        $this->connector->AddParameter("?kecamatan", $this->Kecamatan);
        $this->connector->AddParameter("?propinsi", $this->Propinsi);
        $this->connector->AddParameter("?agama", $this->Agama);
        $this->connector->AddParameter("?pekerjaan", $this->Pekerjaan);
        $this->connector->AddParameter("?exp_idcard", $this->ExpIdCard);
        $this->connector->AddParameter("?status_member", $this->StatusMember);
        $this->connector->AddParameter("?exp_date", $this->ExpDate);
        $this->connector->AddParameter("?no_hp", $this->NoHp, "char");
        $this->connector->AddParameter("?poin_aktif", $this->PoinAktif);
        $this->connector->AddParameter("?poin_reimburse", $this->PoinReimburse);
        $this->connector->AddParameter("?poin_hangus", $this->PoinHangus);
        $this->connector->AddParameter("?no_struk_daftar", $this->NoStrukDaftar);
        $this->connector->AddParameter("?nilai_belanja_daftar", $this->NilaiBelanjaDaftar);
        $this->connector->AddParameter("?kode_promo_daftar", $this->KodePromoDaftar);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $rs = $this->Id;
            $rs = $this->UpdateStrukPoin();
        }
		return $rs;
	}

	public function Update($id) {
	    $sqx = 'UPDATE m_member a 
SET 
a.entity_id = ?entity_id
, a.tgl_daftar = ?tgl_daftar
, a.no_member = ?no_member
, a.no_idcard = ?no_idcard
, a.nama = ?nama
, a.t4_lahir = ?t4_lahir
, a.tgl_lahir = ?tgl_lahir
, a.jkelamin = ?jkelamin
, a.rt_rw = ?rt_rw
, a.desa = ?desa
, a.kecamatan = ?kecamatan
, a.propinsi = ?propinsi
, a.agama = ?agama
, a.pekerjaan = ?pekerjaan
, a.status_member = ?status_member
, a.updateby_id = ?updateby_id
, a.update_time = now()
, a.alamat = ?alamat
, a.exp_date = ?exp_date
, a.no_hp = ?no_hp
, a.exp_idcard = ?exp_idcard
WHERE id = ?id';
		$this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?tgl_daftar", $this->TglDaftar);
        $this->connector->AddParameter("?no_member", $this->NoMember, "char");
        $this->connector->AddParameter("?no_idcard", $this->NoIdCard, "char");
        $this->connector->AddParameter("?t4_lahir", $this->T4Lahir);
        $this->connector->AddParameter("?tgl_lahir", $this->TglLahir);
        $this->connector->AddParameter("?jkelamin", $this->Jkelamin);
        $this->connector->AddParameter("?nama", $this->Nama);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?rt_rw", $this->RtRw);
        $this->connector->AddParameter("?desa", $this->Desa);
        $this->connector->AddParameter("?kecamatan", $this->Kecamatan);
        $this->connector->AddParameter("?propinsi", $this->Propinsi);
        $this->connector->AddParameter("?agama", $this->Agama);
        $this->connector->AddParameter("?pekerjaan", $this->Pekerjaan);
        $this->connector->AddParameter("?exp_idcard", $this->ExpIdCard);
        $this->connector->AddParameter("?status_member", $this->StatusMember);
        $this->connector->AddParameter("?exp_date", $this->ExpDate);
        $this->connector->AddParameter("?no_hp", $this->NoHp, "char");
        //$this->connector->AddParameter("?poin_aktif", $this->PoinAktif);
        //$this->connector->AddParameter("?poin_reimburse", $this->PoinReimburse);
        //$this->connector->AddParameter("?poin_hangus", $this->PoinHangus);
        //$this->connector->AddParameter("?no_struk_daftar", $this->NoStrukDaftar);
        //$this->connector->AddParameter("?nilai_belanja_daftar", $this->NilaiBelanjaDaftar);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Void($id) {
		$this->connector->CommandText = 'UPDATE m_member a SET a.is_deleted = 1,a.updateby_id = ?updateby_id, a.update_time = now() WHERE a.id = ?id';
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

    public function Delete($id) {
        $this->connector->CommandText = 'Delete From m_member Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetMemberNo($entityId = 0, $regDate = null){
        $sql = 'Select fc_m_getautonumber(?eti,?tdf) As valout;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?eti", $entityId);
        $this->connector->AddParameter("?tdf", $regDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function GetDataPos($noTrx = null) {
        $this->connector->CommandText = "SELECT a.* FROM vw_t_pos_master AS a WHERE a.trx_no = ?txn And a.trx_status < 3";
        $this->connector->AddParameter("?txn", $noTrx);
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function GetDataPromoPoin($tglDaftar = null) {
        $this->connector->CommandText = "SELECT a.* FROM vw_m_promo AS a WHERE a.promo_status = 1 And a.type_promo = 9 And date(?tdr) Between a.start_date And a.end_date";
        $this->connector->AddParameter("?tdr", $tglDaftar);
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function UpdateStrukPoin(){
        if ($this->CabangId == 1) {
            $this->connector->CommandText = "Update db_rekapos_erdita1.t_pos_master a Set a.jum_poin = ?jum_poin, a.cust_code = ?cscode, a.kode_promo = ?kode_promo_daftar Where a.trx_no = ?nst";
        }elseif ($this->CabangId == 2){
            $this->connector->CommandText = "Update db_rekapos_erdita2.t_pos_master a Set a.jum_poin = ?jum_poin, a.cust_code = ?cscode, a.kode_promo = ?kode_promo_daftar Where a.trx_no = ?nst";
        }else{
            return 0;
        }
        $this->connector->AddParameter("?jum_poin", $this->PoinAktif);
        $this->connector->AddParameter("?cscode", $this->NoMember,"char");
        $this->connector->AddParameter("?nst", $this->NoStrukDaftar,"char");
        $this->connector->AddParameter("?kode_promo_daftar", $this->KodePromoDaftar);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function RekapPoin($entityId = 0){
        $sqx = "Update m_member a Join (Select c.cust_code,sum(c.jum_poin) as sum_poin From vw_t_pos_master c Group By c.cust_code) b";
        $sqx.= " On a.no_member = b.cust_code Set a.poin_aktif = b.sum_poin Where a.status_member = 1 And a.entity_id = ".$entityId;
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs > 0) {
            $rs = mysqli_affected_rows();
        }
        return $rs;
    }

    public function Load4Reports($entityId = 0,$status = 1){
        $this->connector->CommandText = "SELECT a.* FROM m_member AS a WHERE a.entity_id = ?eti And a.status_member = ?status";
        $this->connector->AddParameter("?eti", $entityId);
        $this->connector->AddParameter("?status", $status);
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function HistoryPoin($noMember){
        $this->connector->CommandText = "SELECT a.waktu,a.trx_no,a.total_transaksi,a.jum_poin,a.kode_promo FROM vw_t_pos_master AS a WHERE a.cust_code = ?nom And a.jum_poin > 0 Order By a.waktu,a.trx_no";
        $this->connector->AddParameter("?nom", $noMember);
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function UpdatePoin($id,$nomember,$nostruk,$kdpromo,$poin,$cbi){
        //update poin member
        $out = 0;
        $sql = "Update m_member AS a Set a.poin_aktif = a.poin_aktif + ?jum_poin Where a.id = $id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?jum_poin", $poin);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $out++;
            if ($cbi == 1) {
                $this->connector->CommandText = "Update db_rekapos_erdita1.t_pos_master a Set a.jum_poin = ?jum_poin, a.cust_code = ?cscode, a.kode_promo = ?kode_promo Where a.trx_no = ?nst";
            }elseif ($cbi == 2){
                $this->connector->CommandText = "Update db_rekapos_erdita2.t_pos_master a Set a.jum_poin = ?jum_poin, a.cust_code = ?cscode, a.kode_promo = ?kode_promo Where a.trx_no = ?nst";
            }else{
                return $out;
            }
            $this->connector->AddParameter("?jum_poin", $poin);
            $this->connector->AddParameter("?cscode", $nomember, "char");
            $this->connector->AddParameter("?nst", $nostruk, "char");
            $this->connector->AddParameter("?kode_promo", $kdpromo);
            $rs = $this->connector->ExecuteNonQuery();
            $out++;
        }
        return $out;
    }
}
