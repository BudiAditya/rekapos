<?php
class PromoAll extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $CabangId = 1;
	public $KodePromo;
	public $NamaPromo;
	public $TypePromo = 0;
	public $StartDate;
	public $EndDate;
	public $StartTime;
	public $EndTime;
	public $KodeBarang;
	public $HargaBarang = 0;
	public $KodeBonus;
	public $HargaBonus = 0;
	public $TandaOperator = '>=';
	public $Qty1 = 0;
	public $Qty2 = 0;
	public $IsKelipatan = 0;
    public $ItemAmtMinimal = 0;
    public $IsItemAmtKelipatan = 0;
    public $SaleAmtMinimal = 0;
    public $IsSaleAmtKelipatan = 0;
	public $PctDiskon = 0;
	public $AmtDiskon = 0;
	public $QtyBonus = 0;
	public $AmtPoint = 0;
	public $PromoStatus = 1;
    public $CreatebyId;
    public $UpdatebyId;
    public $CreateTime;
    public $UpdateTime;
    public $IsMemberOnly = 0;

    //tambahan
    public $NamaBarang;
    public $SatuanBarang;
    public $NamaBonus;
    public $SatuanBonus;

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
		$this->CabangId = $row["cabang_id"];
        $this->KodePromo = $row["kode_promo"];
		$this->NamaPromo = $row["nama_promo"];
		$this->TypePromo = $row["type_promo"];
        $this->StartDate = strtotime($row["start_date"]);
        $this->EndDate = strtotime($row["end_date"]);
        $this->StartTime = $row["start_time"];
        $this->EndTime = $row["end_time"];
        $this->KodeBarang = $row["kode_barang"];
        $this->KodeBonus = $row["kode_bonus"];
        $this->HargaBarang = $row["harga_barang"];
        $this->HargaBonus = $row["harga_bonus"];
        $this->TandaOperator = $row["tanda_operator"];
        $this->Qty1 = $row["qty1"];
        $this->Qty2 = $row["qty2"];
        $this->IsKelipatan = $row["is_kelipatan"];
        $this->ItemAmtMinimal = $row["item_amt_minimal"];
        $this->IsItemAmtKelipatan = $row["is_item_amt_kelipatan"];
        $this->SaleAmtMinimal = $row["sale_amt_minimal"];
        $this->IsSaleAmtKelipatan = $row["is_sale_amt_kelipatan"];
        $this->PctDiskon = $row["pct_diskon"];
        $this->AmtDiskon = $row["amt_diskon"];
        $this->QtyBonus = $row["qty_bonus"];
        $this->AmtPoint = $row["amt_point"];
        $this->PromoStatus = $row["promo_status"];
        $this->IsMemberOnly = $row["is_member_only"];
        $this->CreatebyId = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
        $this->NamaBarang = $row["nama_barang"];
        $this->NamaBonus = $row["nama_bonus"];
        $this->SatuanBarang = $row["satuan_barang"];
        $this->SatuanBonus = $row["satuan_bonus"];
	}

    public function FormatStartDate($format = HUMAN_DATE) {
        return is_int($this->StartDate) ? date($format, $this->StartDate) : date($format, strtotime(date('Y-m-d')));
    }

    public function FormatEndDate($format = HUMAN_DATE) {
        return is_int($this->EndDate) ? date($format, $this->EndDate) : date($format, strtotime(date('Y-m-d')));
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.kode_promo", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.* FROM vw_m_promo AS a ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.* FROM vw_m_promo AS a WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new PromoAll();
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
		$this->connector->CommandText = "SELECT a.* FROM vw_m_promo AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByType($type,$tgl,$cbi = 0) {
	    if ($cbi > 0){
	        $sql = "SELECT a.* FROM vw_m_promo AS a WHERE a.cabang_id = ?cbi And a.promo_status = 1 And a.type_promo = ?type And date(?tgl) Between a.start_date And a.end_date Limit 1";
        }else{
            $sql = "SELECT a.* FROM vw_m_promo AS a WHERE a.promo_status = 1 And a.type_promo = ?type And date(?tgl) Between a.start_date And a.end_date Limit 1";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?type", $type);
        $this->connector->AddParameter("?tgl", $tgl);
        $this->connector->AddParameter("?cbi", $cbi);
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
}
