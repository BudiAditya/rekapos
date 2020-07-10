<?php

class LoyaltyDetail extends EntityBase {
	public $Id;
	public $LoyaltyId = 0;
    public $LoyaltyCode;
	public $MinPoin;
    public $Hadiah;
    public $Qty = 0;
	public $Nilai = 0;


    public function __construct($id = null) {
        parent::__construct();
        $this->connector = ConnectorManager::GetPool("member");
        if (is_numeric($id)) {
            $this->FindById($id);
        }
    }

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
        $this->LoyaltyId = $row["loyalty_id"];
		$this->MinPoin = $row["min_poin"];
        $this->LoyaltyCode = $row["loyalty_code"];
        $this->Hadiah = $row["hadiah"];
        $this->Qty = $row["qty"];
        $this->Nilai = $row["nilai"];
    }

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* From m_loyalty_detail a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByLoyaltyId($loyaltyId = 0) {
        $this->connector->CommandText = "SELECT a.* From m_loyalty_detail a WHERE a.loyalty_id = ?loyaltyId Order By a.min_poin";
        $this->connector->AddParameter("?loyaltyId", $loyaltyId);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new LoyaltyDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByLoyaltyCode($code) {
        $this->connector->CommandText = "SELECT a.* From m_loyalty_detail a WHERE a.loyalty_code = ?code";
        $this->connector->AddParameter("?code", $code);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* From m_loyalty_detail a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

    public function Insert() {
        $this->connector->CommandText = 'INSERT INTO m_loyalty_detail(loyalty_id,loyalty_code, min_poin, hadiah, qty, nilai) VALUES(?loyalty_id,?loyalty_code, ?min_poin, ?hadiah, ?qty, ?nilai)';
        $this->connector->AddParameter("?loyalty_id", $this->LoyaltyId);
        $this->connector->AddParameter("?loyalty_code", $this->LoyaltyCode);
        $this->connector->AddParameter("?min_poin", $this->MinPoin);
        $this->connector->AddParameter("?hadiah", $this->Hadiah);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?nilai", $this->Nilai);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $rx = $this->Id;
        }
        return $rs;
    }

    public function Update($id) {
        $this->connector->CommandText = 'UPDATE m_loyalty_detail SET loyalty_id = ?loyalty_id,loyalty_code = ?loyalty_code, min_poin = ?min_poin, hadiah = ?hadiah, qty = ?qty, nilai = ?nilai WHERE id = ?id';
        $this->connector->AddParameter("?loyalty_id", $this->LoyaltyId);
        $this->connector->AddParameter("?loyalty_code", $this->LoyaltyCode);
        $this->connector->AddParameter("?min_poin", $this->MinPoin);
        $this->connector->AddParameter("?hadiah", $this->Hadiah);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?nilai", $this->Nilai);
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function Delete($id) {
        $this->connector->CommandText = 'Delete From m_loyalty_detail Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

}
// End of File: estimasi_detail.php
