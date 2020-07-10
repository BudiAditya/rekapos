<?php
class Tax extends EntityBase {
	public $Id;
	public $TaxCode;
	public $TaxName;
	public $TaxRate = 0;
	public $TaxMode = 1;
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
		$this->TaxCode = $row["tax_code"];
		$this->TaxName = $row["tax_name"];
        $this->TaxRate = $row["tax_rate"];
        $this->TaxMode = $row["tax_mode"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.tax_code") {
		$this->connector->CommandText = "SELECT a.* FROM m_pajak AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Tax();
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
		$this->connector->CommandText = "SELECT a.* FROM m_pajak AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByTaxCode($code) {
        $this->connector->CommandText = "SELECT a.* FROM m_pajak AS a WHERE a.tax_code = ?code";
        $this->connector->AddParameter("?code", $code);
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
		$this->connector->CommandText = 'INSERT INTO m_pajak(tax_code,tax_name,tax_rate,tax_mode,createby_id,create_time) VALUES(?tax_code,?tax_name,?tax_rate,?tax_mode,?createby_id,now())';
		$this->connector->AddParameter("?tax_code", $this->TaxCode);
        $this->connector->AddParameter("?tax_name", $this->TaxName);
        $this->connector->AddParameter("?tax_rate", $this->TaxRate);
        $this->connector->AddParameter("?tax_mode", $this->TaxMode);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_pajak SET tax_code = ?tax_code, tax_name = ?tax_name, tax_rate = ?tax_rate, tax_mode = ?tax_mode, updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
		$this->connector->AddParameter("?tax_code", $this->TaxCode);
        $this->connector->AddParameter("?tax_name", $this->TaxName);
        $this->connector->AddParameter("?tax_rate", $this->TaxRate);
        $this->connector->AddParameter("?tax_mode", $this->TaxMode);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = 'Delete From m_pajak WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
	}

}
