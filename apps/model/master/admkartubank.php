<?php
class AdmKartuBank extends EntityBase {
	public $Id;
	public $EntityId;
	public $JnsKartu;
	public $NamaKartu;
	public $NamaBank;
	public $Minimal = 0;
	public $ByAdminPct = 0;
	public $ByAdmin = 0;
    public $CreatebyId;
    public $UpdatebyId;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
        $this->EntityId = $row["entity_id"];
		$this->NamaKartu = $row["nama_kartu"];
		$this->NamaBank = $row["nama_bank"];
        $this->JnsKartu = $row["jns_kartu"];
        $this->Minimal = $row["minimal"];
        $this->ByAdmin = $row["by_admin"];
        $this->ByAdminPct = $row["by_admin_pct"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.nama_kartu") {
		$this->connector->CommandText = "SELECT a.* FROM m_kartu_bank AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new AdmKartuBank();
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
		$this->connector->CommandText = "SELECT a.* FROM m_kartu_bank AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
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
		$this->connector->CommandText = 'INSERT INTO m_kartu_bank(entity_id, jns_kartu, nama_kartu, nama_bank, minimal, by_admin_pct, by_admin, createby_id, create_time) VALUES(?entity_id, ?jns_kartu, ?nama_kartu, ?nama_bank, ?minimal, ?by_admin_pct, ?by_admin, ?createby_id, now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?jns_kartu", $this->JnsKartu);
        $this->connector->AddParameter("?nama_kartu", $this->NamaKartu);
        $this->connector->AddParameter("?nama_bank", $this->NamaBank);
        $this->connector->AddParameter("?minimal", $this->Minimal);
        $this->connector->AddParameter("?by_admin", $this->ByAdmin);
        $this->connector->AddParameter("?by_admin_pct", $this->ByAdminPct);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $rs = $this->Id;
        }
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_kartu_bank SET entity_id = ?entity_id,jns_kartu = ?jns_kartu,nama_kartu = ?nama_kartu,nama_bank = ?nama_bank,minimal = ?minimal,by_admin_pct = ?by_admin_pct,by_admin = ?by_admin,updateby_id = ?updateby_id,update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?jns_kartu", $this->JnsKartu);
        $this->connector->AddParameter("?nama_kartu", $this->NamaKartu);
        $this->connector->AddParameter("?nama_bank", $this->NamaBank);
        $this->connector->AddParameter("?minimal", $this->Minimal);
        $this->connector->AddParameter("?by_admin", $this->ByAdmin);
        $this->connector->AddParameter("?by_admin_pct", $this->ByAdminPct);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
        $this->connector->CommandText = 'Delete From m_kartu_bank Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }
}
