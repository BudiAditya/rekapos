<?php
require_once("loyalty_detail.php");
class Loyalty extends EntityBase {
	public $Id;
	public $EntityId = 1;
	public $CabangId = 0;
	public $LoyaltyCode;
	public $ProgramName;
	public $StartDate;
	public $EndDate;
	public $Lstatus = 0;
    public $CreatebyId;
    public $UpdatebyId;

    /** @var LoyaltyDetail[] */
    public $Details = array();

	public function __construct($id = null) {
		parent::__construct();
        $this->connector = ConnectorManager::GetPool("member");
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->EntityId = $row["entity_id"];
		$this->LoyaltyCode = $row["loyalty_code"];
		$this->ProgramName = $row["program_name"];
        $this->StartDate = strtotime($row["start_date"]);
        $this->EndDate = strtotime($row["end_date"]);
        $this->Lstatus = $row["l_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function FormatStartDate($format = HUMAN_DATE) {
        return is_int($this->StartDate) ? date($format, $this->StartDate) : date($format, strtotime(date('Y-m-d')));
    }

    public function FormatEndDate($format = HUMAN_DATE) {
        return is_int($this->EndDate) ? date($format, $this->EndDate) : date($format, strtotime(date('Y-m-d')));
    }

    public function LoadDetails() {
        if ($this->Id == null) {
            return $this->Details;
        }
        $detail = new LoyaltyDetail();
        $this->Details = $detail->LoadByLoyaltyId($this->Id);
        return $this->Details;
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.loyalty_code") {
		$this->connector->CommandText = "SELECT a.* FROM m_loyalty_master AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Loyalty();
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
		$this->connector->CommandText = "SELECT a.* FROM m_loyalty_master AS a WHERE a.id = ?id";
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
		$this->connector->CommandText = 'INSERT INTO m_loyalty_master(entity_id, cabang_id, loyalty_code, program_name, start_date, end_date, l_status, createby_id, create_time) VALUES(?entity_id, ?cabang_id, ?loyalty_code, ?program_name, ?start_date, ?end_date, ?l_status,?createby_id,now())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?loyalty_code", $this->LoyaltyCode);
        $this->connector->AddParameter("?program_name", $this->ProgramName);
        $this->connector->AddParameter("?start_date", $this->StartDate);
        $this->connector->AddParameter("?end_date", $this->EndDate);
        $this->connector->AddParameter("?l_status", $this->Lstatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $rx = $this->Id;
        }
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_loyalty_master SET entity_id = ?entity_id, cabang_id = ?cabang_id, loyalty_code = ?loyalty_code, program_name = ?program_name, start_date = ?start_date, end_date = ?end_date, l_status = ?l_status, updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?loyalty_code", $this->LoyaltyCode);
        $this->connector->AddParameter("?program_name", $this->ProgramName);
        $this->connector->AddParameter("?start_date", $this->StartDate);
        $this->connector->AddParameter("?end_date", $this->EndDate);
        $this->connector->AddParameter("?l_status", $this->Lstatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Void($id) {
		$this->connector->CommandText = 'UPDATE m_loyalty_master SET l_status = 0,updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

    public function Delete($id) {
        $this->connector->CommandText = 'Delete From m_loyalty_master Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetLoyaltyDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'LPR';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->StartDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }
}
