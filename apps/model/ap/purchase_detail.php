<?php

class PurchaseDetail extends EntityBase {
	public $Id;
    public $CabangId;
	public $GrnId;
    public $GrnNo;
	public $ItemDescs;
    public $ItemCode;
    public $ItemId;
    public $Lqty = 0;
    public $Sqty = 0;
	public $PurchaseQty = 0;
    public $ReturnQty = 0;
	public $Price = 0;
    public $DiscFormula = 0;
    public $DiscAmount = 0;
    public $SubTotal = 0;
    public $SatBesar;
    public $SatKecil;
    public $IsFree = 0;
    public $ExPoNo;
    public $TaxCode;
    public $TaxPct = 0;
    public $TaxAmount = 0;
    public $BatchNo;
    public $ExpDate;
    public $GudangId = 0;

	// Helper Variable;
	public $MarkedForDeletion = false;


	public function FillProperties(array $row) {
		$this->Id = $row["id"];        
		$this->GrnId = $row["grn_id"];
        $this->CabangId = $row["cabang_id"];
        $this->GrnNo = $row["grn_no"];
        $this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
		$this->ItemDescs = $row["item_descs"];                
        $this->Lqty = $row["l_qty"];
        $this->Sqty = $row["s_qty"];
		$this->PurchaseQty = $row["purchase_qty"];
        $this->ReturnQty = $row["return_qty"];
		$this->Price = $row["price"];
        $this->DiscFormula = $row["disc_formula"];
        $this->DiscAmount = $row["disc_amount"];
        $this->SubTotal = $row["sub_total"];
        $this->SatBesar = $row["bsatbesar"];
        $this->SatKecil = $row["bsatkecil"];
        $this->IsFree = $row["is_free"];
        $this->ExPoNo = $row["ex_po_no"];
        $this->TaxCode = $row["tax_code"];
        $this->TaxPct = $row["tax_pct"];
        $this->TaxAmount = $row["tax_amount"];
        $this->BatchNo = $row["batch_no"];
        $this->ExpDate = strtotime($row["exp_date"]);
        $this->GudangId = $row["gudang_id"];
	}

    public function FormatExpDate($format = HUMAN_DATE) {
        return is_int($this->ExpDate) ? date($format, $this->ExpDate) : null;
    }

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_purchase_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_purchase_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByGrnId($grnId, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_purchase_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.grn_id = ?grnId ORDER BY $orderBy";
		$this->connector->AddParameter("?grnId", $grnId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new PurchaseDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByGrnNo($grnNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_purchase_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.grn_no = ?grnNo ORDER BY $orderBy";
        $this->connector->AddParameter("?grnNo", $grnNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new PurchaseDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ap_purchase_detail(gudang_id,ex_po_no,is_free,grn_id, cabang_id, grn_no, item_id, item_code, item_descs, l_qty, s_qty, purchase_qty, return_qty, price, disc_formula, disc_amount, sub_total, tax_code, tax_pct, tax_amount, batch_no, exp_date)
VALUES(?gudang_id,?ex_po_no,?is_free,?grn_id, ?cabang_id, ?grn_no, ?item_id, ?item_code, ?item_descs, ?l_qty, ?s_qty, ?purchase_qty, ?return_qty, ?price, ?disc_formula, ?disc_amount, ?sub_total, ?tax_code, ?tax_pct, ?tax_amount, ?batch_no, ?exp_date)";
		$this->connector->AddParameter("?grn_id", $this->GrnId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?grn_no", $this->GrnNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
		$this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?l_qty", $this->Lqty);
        $this->connector->AddParameter("?s_qty", $this->Sqty);
		$this->connector->AddParameter("?purchase_qty", $this->PurchaseQty);
        $this->connector->AddParameter("?return_qty", $this->ReturnQty);
		$this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?disc_formula", $this->DiscFormula);
        $this->connector->AddParameter("?disc_amount", $this->DiscAmount);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?is_free", $this->IsFree);
        $this->connector->AddParameter("?ex_po_no", $this->ExPoNo);
        $this->connector->AddParameter("?ex_po_no", $this->ExPoNo);
        $this->connector->AddParameter("?tax_code", $this->TaxCode);
        $this->connector->AddParameter("?tax_pct", $this->TaxPct);
        $this->connector->AddParameter("?tax_amount", $this->TaxAmount);
        $this->connector->AddParameter("?batch_no", $this->BatchNo);
        $this->connector->AddParameter("?exp_date", $this->FormatExpDate(SQL_DATETIME));
        $this->connector->AddParameter("?gudang_id", $this->GudangId);
		$rs = $this->connector->ExecuteNonQuery();
        $rsx = null;
        $did = 0;
        if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $did = $this->Id;
            //update harga beli
            $this->connector->CommandText = "Update m_barang AS a Set a.bhargabeli = ?price, a.bhpp = ?price, a.update_time = now() Where a.bkode = ?bkode";
            $this->connector->AddParameter("?price", $this->Price);
            $this->connector->AddParameter("?bkode", $this->ItemCode);
            $rsx = $this->connector->ExecuteNonQuery();
            //tambah stock
            $this->connector->CommandText = "SELECT fc_ap_purchasedetail_post($did) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            //update po status (jika ada)
            $this->connector->CommandText = "SELECT fc_ap_po_checkstatus('".$this->ExPoNo."') As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            //update grn
            $this->UpdateGrnMaster($this->GrnId);
		}
		return $rs;
	}

	public function Update($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ap_purchasedetail_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        $this->connector->CommandText =
"UPDATE t_ap_purchase_detail SET
	  grn_id = ?grn_id
	, cabang_id = ?cabang_id
	, grn_no = ?grn_no
	, item_descs = ?item_descs
	, purchase_qty = ?purchase_qty
	, return_qty = ?return_qty
	, price = ?price
	, sub_total = ?sub_total
	, item_code = ?item_code
	, item_id = ?item_id
	, l_qty = ?l_qty
	, s_qty = ?s_qty
	, disc_formula = ?disc_formula
	, disc_amount = ?disc_amount
	, is_free = ?is_free
	, ex_po_no = ?ex_po_no
	, tax_code = ?tax_code
	, tax_pct = ?tax_pct
	, tax_amount = ?tax_amount
	, batch_no = ?batch_no
	, exp_date = ?exp_date
	, gudang_id = ?gudang_id
WHERE id = ?id";
        $this->connector->AddParameter("?grn_id", $this->GrnId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?grn_no", $this->GrnNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?l_qty", $this->Lqty);
        $this->connector->AddParameter("?s_qty", $this->Sqty);
        $this->connector->AddParameter("?purchase_qty", $this->PurchaseQty);
        $this->connector->AddParameter("?return_qty", $this->ReturnQty);
        $this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?disc_formula", $this->DiscFormula);
        $this->connector->AddParameter("?disc_amount", $this->DiscAmount);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?is_free", $this->IsFree);
        $this->connector->AddParameter("?ex_po_no", $this->ExPoNo);
        $this->connector->AddParameter("?tax_code", $this->TaxCode);
        $this->connector->AddParameter("?tax_pct", $this->TaxPct);
        $this->connector->AddParameter("?tax_amount", $this->TaxAmount);
        $this->connector->AddParameter("?batch_no", $this->BatchNo);
        $this->connector->AddParameter("?exp_date", $this->FormatExpDate(SQL_DATETIME));
        $this->connector->AddParameter("?gudang_id", $this->GudangId);
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            //update harga beli
            $this->connector->CommandText = "Update m_barang AS a Set a.bhargabeli = ?price, a.bhpp = ?price, a.update_time = now() Where a.bkode = ?bkode";
            $this->connector->AddParameter("?price", $this->Price);
            $this->connector->AddParameter("?bkode", $this->ItemCode);
            $rsx = $this->connector->ExecuteNonQuery();
            //potong stock lagi
            $this->connector->CommandText = "SELECT fc_ap_purchasedetail_post($id) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            //update po status (jika ada)
            $this->connector->CommandText = "SELECT fc_ap_po_checkstatus('".$this->ExPoNo."') As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            //update grn master
            $this->UpdateGrnMaster($this->GrnId);
        }
        return $rs;
	}

	public function Delete($id) {
        //unpost stock dulu
        $rsx = null;
        $pno = $this->ExPoNo;
        $this->connector->CommandText = "SELECT fc_ap_purchasedetail_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //hapus detail
		$this->connector->CommandText = "DELETE FROM t_ap_purchase_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            //update so status (jika ada)
            $this->connector->CommandText = "SELECT fc_ap_po_checkstatus('".$pno."') As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            //update GRN master
            $this->UpdateGrnMaster($this->GrnId);
        }
        return $rs;
	}

    public function UpdateGrnMaster($grnId){
        $sql = 'Update t_ap_purchase_master a Set a.base_amount = 0, a.tax_amount = 0, a.disc1_amount = 0 Where a.id = ?grnId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grnId", $grnId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_purchase_master a
Join (Select c.grn_id, sum(c.sub_total) As sumPrice, sum(c.disc_amount) As sumDiscount, sum(c.tax_amount) As sumTax From t_ap_purchase_detail c Group By c.grn_id) b
On a.id = b.grn_id Set a.base_amount = b.sumPrice, a.disc1_amount = b.sumDiscount, a.tax_amount = b.sumTax Where a.id = ?grnId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grnId", $grnId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_purchase_master a Set a.paid_amount = (a.base_amount - a.disc1_amount) + a.tax_amount + a.other_costs_amount Where a.id = ?grnId And a.payment_type = 0;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grnId", $grnId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }
}
// End of File: estimasi_detail.php
