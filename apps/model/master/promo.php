<?php
class Promo extends EntityBase {
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
				$temp = new Promo();
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

    public function FindByType($type,$tgl) {
        $this->connector->CommandText = "SELECT a.* FROM vw_m_promo AS a WHERE a.promo_status = 1 And a.type_promo = ?type And date(?tgl) Between a.start_date And a.end_date Limit 1";
        $this->connector->AddParameter("?type", $type);
        $this->connector->AddParameter("?tgl", $tgl);
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
		$this->connector->CommandText = 'INSERT INTO m_promo(is_member_only,item_amt_minimal,is_item_amt_kelipatan,sale_amt_minimal,is_sale_amt_kelipatan,cabang_id, kode_promo, nama_promo, type_promo, start_date, end_date, start_time, end_time, kode_barang, harga_barang, kode_bonus, harga_bonus, tanda_operator, qty1, qty2, is_kelipatan, pct_diskon, amt_diskon, qty_bonus, amt_point, promo_status, createby_id, create_time) VALUES(?is_member_only,?item_amt_minimal,?is_item_amt_kelipatan,?sale_amt_minimal,?is_sale_amt_kelipatan,?cabang_id, ?kode_promo, ?nama_promo, ?type_promo, ?start_date, ?end_date, ?start_time, ?end_time, ?kode_barang, ?harga_barang, ?kode_bonus, ?harga_bonus, ?tanda_operator, ?qty1, ?qty2, ?is_kelipatan, ?pct_diskon, ?amt_diskon, ?qty_bonus, ?amt_point, ?promo_status,?createby_id,now())';
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?kode_promo", $this->KodePromo, "varchar");
        $this->connector->AddParameter("?nama_promo", $this->NamaPromo);
        $this->connector->AddParameter("?type_promo", $this->TypePromo);
        $this->connector->AddParameter("?start_date", $this->StartDate);
        $this->connector->AddParameter("?end_date", $this->EndDate);
        $this->connector->AddParameter("?start_time", $this->StartTime);
        $this->connector->AddParameter("?end_time", $this->EndTime);
        $this->connector->AddParameter("?kode_barang", $this->KodeBarang, "varchar");
        $this->connector->AddParameter("?harga_barang", $this->HargaBarang);
        $this->connector->AddParameter("?kode_bonus", $this->KodeBonus, "varchar");
        $this->connector->AddParameter("?harga_bonus", $this->HargaBonus);
        $this->connector->AddParameter("?tanda_operator", $this->TandaOperator);
        $this->connector->AddParameter("?qty1", $this->Qty1);
        $this->connector->AddParameter("?qty2", $this->Qty2);
        $this->connector->AddParameter("?is_kelipatan", $this->IsKelipatan);
        $this->connector->AddParameter("?pct_diskon", $this->PctDiskon);
        $this->connector->AddParameter("?amt_diskon", $this->AmtDiskon);
        $this->connector->AddParameter("?qty_bonus", $this->QtyBonus);
        $this->connector->AddParameter("?amt_point", $this->AmtPoint);
        $this->connector->AddParameter("?promo_status", $this->PromoStatus);
        $this->connector->AddParameter("?is_member_only", $this->IsMemberOnly);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?item_amt_minimal", $this->ItemAmtMinimal);
        $this->connector->AddParameter("?is_item_amt_kelipatan", $this->IsItemAmtKelipatan);
        $this->connector->AddParameter("?sale_amt_minimal", $this->SaleAmtMinimal);
        $this->connector->AddParameter("?is_sale_amt_kelipatan", $this->IsSaleAmtKelipatan);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $rs = $this->Id;
        }
		return $rs;
	}

	public function Update($id) {
	    $sqx = 'UPDATE m_promo a 
SET 
a.cabang_id = ?cabang_id,
a.kode_promo = ?kode_promo, 
a.nama_promo = ?nama_promo, 
a.type_promo = ?type_promo,
a.start_date = ?start_date,
a.end_date = ?end_date,
a.start_time = ?start_time,
a.end_time = ?end_time,
a.kode_barang = ?kode_barang,
a.harga_barang = ?harga_barang,
a.kode_bonus = ?kode_bonus,
a.harga_bonus = ?harga_bonus,
a.tanda_operator =  ?tanda_operator,
a.qty1 = ?qty1,
a.qty2 = ?qty2,
a.is_kelipatan = ?is_kelipatan,
a.pct_diskon = ?pct_diskon,
a.amt_diskon = ?amt_diskon,
a.qty_bonus = ?qty_bonus,
a.amt_point = ?amt_point,
a.promo_status = ?promo_status,
a.updateby_id = ?updateby_id, 
a.update_time = now(),
a.item_amt_minimal = ?item_amt_minimal,
a.is_item_amt_kelipatan = ?is_item_amt_kelipatan,
a.sale_amt_minimal = ?sale_amt_minimal,
a.is_sale_amt_kelipatan = ?is_sale_amt_kelipatan,
a.is_member_only = ?is_member_only
WHERE id = ?id';
		$this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?kode_promo", $this->KodePromo, "varchar");
        $this->connector->AddParameter("?nama_promo", $this->NamaPromo);
        $this->connector->AddParameter("?type_promo", $this->TypePromo);
        $this->connector->AddParameter("?start_date", $this->StartDate);
        $this->connector->AddParameter("?end_date", $this->EndDate);
        $this->connector->AddParameter("?start_time", $this->StartTime);
        $this->connector->AddParameter("?end_time", $this->EndTime);
        $this->connector->AddParameter("?kode_barang", $this->KodeBarang, "varchar");
        $this->connector->AddParameter("?harga_barang", $this->HargaBarang);
        $this->connector->AddParameter("?kode_bonus", $this->KodeBonus, "varchar");
        $this->connector->AddParameter("?harga_bonus", $this->HargaBonus);
        $this->connector->AddParameter("?tanda_operator", $this->TandaOperator);
        $this->connector->AddParameter("?qty1", $this->Qty1);
        $this->connector->AddParameter("?qty2", $this->Qty2);
        $this->connector->AddParameter("?is_kelipatan", $this->IsKelipatan);
        $this->connector->AddParameter("?pct_diskon", $this->PctDiskon);
        $this->connector->AddParameter("?amt_diskon", $this->AmtDiskon);
        $this->connector->AddParameter("?qty_bonus", $this->QtyBonus);
        $this->connector->AddParameter("?amt_point", $this->AmtPoint);
        $this->connector->AddParameter("?promo_status", $this->PromoStatus);
        $this->connector->AddParameter("?is_member_only", $this->IsMemberOnly);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?item_amt_minimal", $this->ItemAmtMinimal);
        $this->connector->AddParameter("?is_item_amt_kelipatan", $this->IsItemAmtKelipatan);
        $this->connector->AddParameter("?sale_amt_minimal", $this->SaleAmtMinimal);
        $this->connector->AddParameter("?is_sale_amt_kelipatan", $this->IsSaleAmtKelipatan);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = 'UPDATE m_promo a SET a.is_deleted = 1,a.updateby_id = ?updateby_id, a.update_time = now() WHERE a.id = ?id';
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

    public function Hapus($id) {
        $this->connector->CommandText = 'Delete From m_promo Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetPromoDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'SPR';
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

    public function LoadTypePromo() {
        $this->connector->CommandText = "SELECT a.* FROM sys_status_code AS a WHERE a.`key` = 'type_promo' Order By a.code";
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }
}
