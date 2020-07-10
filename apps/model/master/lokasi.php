<?php
class Lokasi extends EntityBase {
	public $Id;
	public $IsDeleted = 0;
    public $Kode;
	public $Keterangan;
	public $CreatebyId = 0;
	public $UpdatebyId = 0;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
        $this->IsDeleted = $row["is_deleted"];
		$this->Kode = $row["kode"];
		$this->Keterangan = $row["keterangan"];
		$this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function LoadAll($orderBy = "a.kode") {
		$this->connector->CommandText = "SELECT a.* FROM m_lokasi AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Lokasi();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_lokasi AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_lokasi AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByKode($kode) {
        $this->connector->CommandText = "SELECT a.* FROM m_lokasi AS a WHERE a.kode = ?kode";
        $this->connector->AddParameter("?kode", $kode);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_lokasi (kode,keterangan,createby_id,create_time) VALUES (?kode,?keterangan,?createby_id,now())';
		$this->connector->AddParameter("?kode", $this->Kode);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_lokasi SET kode = ?kode, keterangan = ?keterangan, updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?kode", $this->Kode);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
        $this->connector->CommandText = "Delete From m_lokasi WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        $this->connector->CommandText = "'UPDATE m_lokasi SET is_deleted = 1 WHERE id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

}

