<?php

require_once("invoice_detail.php");

class Invoice extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $InvoiceStatusCodes = array(
		0 => "DRAFT",
		1 => "POSTED",
        2 => "APPROVED",
		3 => "VOID"
	);

    public static $CollectStatusCodes = array(
        0 => "ON HOLD",
        1 => "ON PROCESS",
        2 => "PAID",
        3 => "VOID"
    );

	public $Id;
    public $IsDeleted = false;
    public $EntityId;
    public $AreaId;
    public $EntityCode;
    public $CompanyName;
    public $CabangId;
    public $CabangCode;
    public $OutletName;
	public $InvoiceNo;
	public $InvoiceDate;
    public $CustomerId = 0;
    public $CustomerCode;
    public $CustomerName;
    public $SalesId = 0;
    public $SalesName;
	public $InvoiceDescs;
	public $ExSoNo;
	public $BaseAmount = 0;
    public $Disc1Pct = 0;
    public $Disc1Amount = 0;
    public $Disc2Pct = 0;
    public $Disc2Amount = 0;
    public $TaxPct = 0;
	public $TaxAmount = 0;
    public $OtherCosts = 0;
    public $OtherCostsAmount = 0;
    public $TotalAmount = 0;
	public $PaidAmount = 0;
    public $BalanceAmount = 0;
    public $CreditTerms = 0;
    public $DueDate;
    public $InvoiceStatus = 0;
	public $CreatebyId = 0;
	public $CreateTime;
	public $UpdatebyId = 0;
	public $UpdateTime;
    public $PaymentType = 0;
    public $CollectStatus = 0;
    public $CustLevel = 0;
    public $CustomerAddress;
    public $CustomerCity;
    public $TotalHpp = 0;
    public $GudangId = 0;
    public $GudangCode;
    public $AdminName;
    public $PrintCount = 0;
    public $InvoiceType = 1;
    public $ApproveTime;
    public $ApprovebyId = 0;
    public $ApproveStatus = 0;
    public $ApproveReason = '-';

	/** @var InvoiceDetail[] */
	public $Details = array();

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
        $this->Id = $row["id"];
        $this->IsDeleted = $row["is_deleted"] == 1;
        $this->EntityCode = $row["entity_cd"];
        $this->EntityId = $row["entity_id"];
        $this->AreaId = $row["area_id"];
        $this->CompanyName = $row["company_name"];
        $this->CabangId = $row["cabang_id"];
        $this->CabangCode = $row["cabang_code"];
        $this->InvoiceNo = $row["invoice_no"];
        $this->InvoiceDate = strtotime($row["invoice_date"]);
        $this->CustomerId = $row["customer_id"];
        $this->CustomerCode = $row["customer_code"];
        $this->CustomerName = $row["customer_name"];
        $this->SalesId = $row["sales_id"];
        $this->SalesName = $row["sales_name"];
        $this->InvoiceDescs = $row["invoice_descs"];
        $this->ExSoNo = $row["ex_so_no"];
        $this->BaseAmount = $row["base_amount"];
        $this->Disc1Pct = $row["disc1_pct"];
        $this->Disc1Amount = $row["disc1_amount"];
        $this->Disc2Pct = $row["disc2_pct"];
        $this->Disc2Amount = $row["disc2_amount"];
        $this->TaxPct = $row["tax_pct"];
        $this->TaxAmount = $row["tax_amount"];
        $this->OtherCosts = $row["other_costs"];
        $this->OtherCostsAmount = $row["other_costs_amount"];
        $this->TotalAmount = $row["total_amount"];
        $this->PaidAmount = $row["paid_amount"];
        $this->BalanceAmount = $row["balance_amount"];
        $this->CreditTerms = $row["credit_terms"];
        $this->DueDate = strtotime($row["due_date"]);
        $this->InvoiceStatus = $row["invoice_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
        $this->PaymentType = $row["payment_type"];
        $this->CollectStatus = $row["collect_status"];
        $this->CustLevel = $row["cust_level"];
        $this->CustomerAddress = $row["customer_address"];
        $this->CustomerCity = $row["customer_city"];
        $this->TotalHpp = $row["total_hpp"];
        $this->GudangId = $row["gudang_id"];
        $this->GudangCode = $row["gudang_code"];
        $this->AdminName = $row["admin_name"];
        $this->PrintCount = $row["print_count"];
        $this->OutletName = $row["nama_outlet"];
        $this->InvoiceType = $row["invoice_type"];
        $this->ApprovebyId = $row["approveby_id"];
        $this->ApproveTime = $row["approve_time"];
        $this->ApproveStatus = $row["approve_status"];
        $this->ApproveReason = $row["approve_reason"];
	}

	public function FormatInvoiceDate($format = HUMAN_DATE) {
		return is_int($this->InvoiceDate) ? date($format, $this->InvoiceDate) : date($format, strtotime(date('Y-m-d')));
	}

    public function FormatDueDate($format = HUMAN_DATE) {
        return is_int($this->DueDate) ? date($format, $this->DueDate) : null;
    }

	/**
	 * @return InvoiceDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new InvoiceDetail();
		$this->Details = $detail->LoadByInvoiceId($this->Id);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return Invoice
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ar_invoice_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ar_invoice_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByInvoiceNo($invNo,$cabangId) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ar_invoice_master AS a WHERE a.invoice_no = ?invNo And a.cabang_id = ?cabangId";
		$this->connector->AddParameter("?invNo", $invNo);
        $this->connector->AddParameter("?cabangId", $cabangId);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ar_invoice_master AS a WHERE a.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Invoice();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ar_invoice_master AS a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Invoice();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    //$reports = $invoice->Load4Reports($sCabangId,$sCustomerId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
    public function Load4Reports($entityId, $cabangId = 0, $customerId = 0, $salesId = 0, $invoiceStatus = -1, $paymentStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_ar_invoice_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($invoiceStatus > -1){
            $sql.= " and a.invoice_status = ".$invoiceStatus;
        }else{
            $sql.= " and a.invoice_status <> 3 ";
        }
        if ($paymentStatus == 0){
            $sql.= " and (a.balance_amount) > 0";
        }elseif ($paymentStatus == 1){
            $sql.= " and (a.balance_amount) = 0";
        }
        if ($customerId > 0){
            $sql.= " and a.customer_id = ".$customerId;
        }
        if ($salesId > 0){
            $sql.= " and a.sales_id = ".$salesId;
        }
        $sql.= " Order By a.invoice_date,a.invoice_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsDetail($entityId, $cabangId = 0, $customerId = 0, $salesId = 0, $invoiceStatus = -1, $paymentStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.*,b.item_code,b.item_descs,b.qty,b.price,b.disc_formula,b.disc_amount,b.sub_total FROM vw_ar_invoice_master AS a Join t_ar_invoice_detail b On a.invoice_no = b.invoice_no";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($invoiceStatus > -1){
            $sql.= " and a.invoice_status = ".$invoiceStatus;
        }else{
            $sql.= " and a.invoice_status <> 3 ";
        }
        if ($paymentStatus == 0){
            $sql.= " and (a.balance_amount) > 0";
        }elseif ($paymentStatus == 1){
            $sql.= " and (a.balance_amount) = 0";
        }
        if ($customerId > 0){
            $sql.= " and a.customer_id = ".$customerId;
        }
        if ($salesId > 0){
            $sql.= " and a.sales_id = ".$salesId;
        }
        $sql.= " Order By a.invoice_date,a.invoice_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsRekapItem($entityId, $cabangId = 0, $customerId = 0, $salesId = 0, $invoiceStatus = -1, $paymentStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,b.item_descs,c.bsatkecil as satuan,b.price, coalesce(sum(b.qty),0) as sum_qty,coalesce(sum(b.sub_total),0) as sum_total, sum(Case When a.tax_pct > 0 Then Round(b.sub_total * (a.tax_pct/100),0) Else 0 End) as sum_tax";
        $sql.= " FROM vw_ar_invoice_master AS a Join t_ar_invoice_detail AS b On a.invoice_no = b.invoice_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($invoiceStatus > -1){
            $sql.= " and a.invoice_status = ".$invoiceStatus;
        }else{
            $sql.= " and a.invoice_status <> 3 ";
        }
        if ($paymentStatus == 0){
            $sql.= " and (a.balance_amount) > 0";
        }elseif ($paymentStatus == 1){
            $sql.= " and (a.balance_amount) = 0";
        }
        if ($customerId > 0){
            $sql.= " and a.customer_id = ".$customerId;
        }
        if ($salesId > 0){
            $sql.= " and a.sales_id = ".$salesId;
        }
        $sql.= " Group By b.item_code,b.item_descs,c.bsatkecil,b.price Order By b.item_descs,b.item_code,b.price";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsRekapItem1($entityId, $cabangId = 0, $customerId = 0, $salesId = 0, $invoiceStatus = -1, $paymentStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,b.item_descs,c.bsatkecil as satuan, coalesce(sum(b.qty),0) as sum_qty,coalesce(sum(b.sub_total),0) as sum_total, sum(Case When a.tax_pct > 0 Then Round(b.sub_total * (a.tax_pct/100),0) Else 0 End) as sum_tax";
        $sql.= " FROM vw_ar_invoice_master AS a Join t_ar_invoice_detail AS b On a.invoice_no = b.invoice_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($invoiceStatus > -1){
            $sql.= " and a.invoice_status = ".$invoiceStatus;
        }else{
            $sql.= " and a.invoice_status <> 3 ";
        }
        if ($paymentStatus == 0){
            $sql.= " and (a.balance_amount) > 0";
        }elseif ($paymentStatus == 1){
            $sql.= " and (a.balance_amount) = 0";
        }
        if ($customerId > 0){
            $sql.= " and a.customer_id = ".$customerId;
        }
        if ($salesId > 0){
            $sql.= " and a.sales_id = ".$salesId;
        }
        $sql.= " Group By b.item_code,b.item_descs,c.bsatkecil Order By b.item_descs,b.item_code";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function GetUnpaidInvoices($cabangId = 0,$customerId = 0,$invoiceNo = null) {
        $sql = "SELECT a.* FROM vw_ar_invoice_master AS a";
        $sql.= " Where a.invoice_status > 0 and a.is_deleted = 0 and a.balance_amount > 0 And a.invoice_no = ?invoiceNo";
        if ($cabangId > 0){
            $sql.= " And a.cabang_id = ?cabangId";
        }
        if ($customerId > 0){
            $sql.= " And a.customer_id = ?customerId";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?customerId", $customerId);
        $this->connector->AddParameter("?invoiceNo", $invoiceNo);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function Insert() {
        $sql = "INSERT INTO t_ar_invoice_master (invoice_type,gudang_id, cabang_id, invoice_no, invoice_date, customer_id, sales_id, invoice_descs, ex_so_no, base_amount, disc1_pct, disc1_amount, disc2_pct, disc2_amount, tax_pct, tax_amount, other_costs, other_costs_amount, paid_amount, payment_type, credit_terms, invoice_status, createby_id, create_time, cust_level)";
        $sql.= "VALUES(?invoice_type, ?gudang_id, ?cabang_id, ?invoice_no, ?invoice_date, ?customer_id, ?sales_id, ?invoice_descs, ?ex_so_no, ?base_amount, ?disc1_pct, ?disc1_amount, ?disc2_pct, ?disc2_amount, ?tax_pct, ?tax_amount, ?other_costs, ?other_costs_amount, ?paid_amount, ?payment_type, ?credit_terms, ?invoice_status, ?createby_id, now(), ?cust_level)";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?gudang_id", $this->GudangId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?invoice_no", $this->InvoiceNo, "char");
		$this->connector->AddParameter("?invoice_date", $this->InvoiceDate);
        $this->connector->AddParameter("?customer_id", $this->CustomerId);
        $this->connector->AddParameter("?sales_id", $this->SalesId);
		$this->connector->AddParameter("?invoice_descs", $this->InvoiceDescs);
        $this->connector->AddParameter("?ex_so_no", $this->ExSoNo);
        $this->connector->AddParameter("?base_amount", str_replace(",","",$this->BaseAmount));
        $this->connector->AddParameter("?disc1_pct", str_replace(",","",$this->Disc1Pct));
        $this->connector->AddParameter("?disc1_amount", str_replace(",","",$this->Disc1Amount));
        $this->connector->AddParameter("?disc2_pct", str_replace(",","",$this->Disc2Pct));
        $this->connector->AddParameter("?disc2_amount", str_replace(",","",$this->Disc2Amount));
        $this->connector->AddParameter("?tax_pct", str_replace(",","",$this->TaxPct));
        $this->connector->AddParameter("?tax_amount", str_replace(",","",$this->TaxAmount));
        $this->connector->AddParameter("?other_costs", str_replace(",","",$this->OtherCosts));
        $this->connector->AddParameter("?other_costs_amount", str_replace(",","",$this->OtherCostsAmount));
        $this->connector->AddParameter("?paid_amount", str_replace(",","",$this->PaidAmount));
        $this->connector->AddParameter("?payment_type", $this->PaymentType);
        $this->connector->AddParameter("?credit_terms", $this->CreditTerms);
        $this->connector->AddParameter("?invoice_status", $this->InvoiceStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?cust_level", $this->CustLevel);
        $this->connector->AddParameter("?invoice_type", $this->InvoiceType);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_ar_invoice_master SET
	cabang_id = ?cabang_id
	, gudang_id = ?gudang_id
	, invoice_no = ?invoice_no
	, invoice_date = ?invoice_date
	, customer_id = ?customer_id
	, sales_id = ?sales_id
	, invoice_descs = ?invoice_descs
	, ex_so_no = ?ex_so_no
	, base_amount = ?base_amount
	, disc1_pct = ?disc1_pct
	, disc1_amount = ?disc1_amount
	, disc2_pct = ?disc2_pct
	, disc2_amount = ?disc2_amount
	, tax_pct = ?tax_pct
	, tax_amount = ?tax_amount
	, other_costs = ?other_costs
	, other_costs_amount = ?other_costs_amount
	, paid_amount = ?paid_amount
	, payment_type = ?payment_type
	, credit_terms = ?credit_terms
	, invoice_status = ?invoice_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
	, cust_level = ?cust_level
	, invoice_type = ?invoice_type
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->GudangId);
        $this->connector->AddParameter("?invoice_no", $this->InvoiceNo, "char");
        $this->connector->AddParameter("?invoice_date", $this->InvoiceDate);
        $this->connector->AddParameter("?customer_id", $this->CustomerId);
        $this->connector->AddParameter("?sales_id", $this->SalesId);
        $this->connector->AddParameter("?invoice_descs", $this->InvoiceDescs);
        $this->connector->AddParameter("?ex_so_no", $this->ExSoNo);
        $this->connector->AddParameter("?base_amount", str_replace(",","",$this->BaseAmount));
        $this->connector->AddParameter("?disc1_pct", str_replace(",","",$this->Disc1Pct));
        $this->connector->AddParameter("?disc1_amount", str_replace(",","",$this->Disc1Amount));
        $this->connector->AddParameter("?disc2_pct", str_replace(",","",$this->Disc2Pct));
        $this->connector->AddParameter("?disc2_amount", str_replace(",","",$this->Disc2Amount));
        $this->connector->AddParameter("?tax_pct", str_replace(",","",$this->TaxPct));
        $this->connector->AddParameter("?tax_amount", str_replace(",","",$this->TaxAmount));
        $this->connector->AddParameter("?other_costs", str_replace(",","",$this->OtherCosts));
        $this->connector->AddParameter("?other_costs_amount", str_replace(",","",$this->OtherCostsAmount));
        $this->connector->AddParameter("?paid_amount", str_replace(",","",$this->PaidAmount));
        $this->connector->AddParameter("?payment_type", $this->PaymentType);
        $this->connector->AddParameter("?credit_terms", $this->CreditTerms);
        $this->connector->AddParameter("?invoice_status", $this->InvoiceStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?cust_level", $this->CustLevel);
        $this->connector->AddParameter("?invoice_type", $this->InvoiceType);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1){
            $this->RecalculateInvoiceMaster($id);
        }
        return $rs;
	}

	public function Delete($id) {
        $rsx = null;
        //ubah status SO jika ada
        $this->connector->CommandText = "Update t_ar_so_master a Join t_ar_invoice_master b On a.so_no = b.ex_so_no Set a.so_status = 1 Where b.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rsx = $this->connector->ExecuteNonQuery();
        //unpost stock dulu
        $this->connector->CommandText = "SELECT fc_ar_invoicemaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus invoicenya
		$this->connector->CommandText = "Delete From t_ar_invoice_master WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id,$ino) {
        //fc_ar_invoicemaster_unpost
        $rsx = null;
        $rsz = null;
        //ubah status SO jika ada
        $this->connector->CommandText = "Update t_ar_so_master a Join t_ar_invoice_master b On a.so_no = b.ex_so_no Set a.so_status = 1 Where b.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rsx = $this->connector->ExecuteNonQuery();
        //unpost stock dulu
        $this->connector->CommandText = "SELECT fc_ar_invoicemaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus invoicenya
        $this->connector->CommandText = "Update t_ar_invoice_master a Set a.invoice_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rsz =  $this->connector->ExecuteNonQuery();
        //update so status
        $this->connector->CommandText = "SELECT fc_ar_so_checkstatus_by_invoice('".$ino."') As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        return $rsz;
    }

    public function GetInvoiceDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'INV';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->InvoiceDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function Approve($id = 0, $uid = 0, $apsts = 1, $aprsn = '-'){
        $this->connector->CommandText = "SELECT fc_ar_invoice_approve(?id,?uid,?ast,?ars) As valresult;";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?uid", $uid);
        $this->connector->AddParameter("?ast", $apsts);
        $this->connector->AddParameter("?ars", $aprsn);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function Unapprove($id = 0, $uid = 0){
        $this->connector->CommandText = "SELECT fc_ar_invoice_unapprove(?id,?uid) As valresult;";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?uid", $uid);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function QtyReturn($invoiceId){
        $sql = "Select coalesce(sum(a.qty_return),0) as qreturn From t_ar_invoice_detail a Where a.invoice_id = ?invoiceId;";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["qreturn"]);
    }

    public function RecalculateInvoiceMaster($invoiceId){
        $sql = 'Update t_ar_invoice_master a Set a.base_amount = 0, a.tax_amount = 0, a.disc1_amount = 0 Where a.id = ?invoiceId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_invoice_master a
Join (Select c.invoice_id, sum(c.sub_total) As sumPrice, sum(c.qty * c.item_hpp) as sumHpp From t_ar_invoice_detail c Group By c.invoice_id) b
On a.id = b.invoice_id Set a.base_amount = b.sumPrice, a.disc1_amount = if(a.disc1_pct > 0,round(b.sumPrice * (a.disc1_pct/100),0),0), a.total_hpp = b.sumHpp Where a.id = ?invoiceId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_invoice_master a Set a.tax_amount = if(a.tax_pct > 0 And (a.base_amount - a.disc1_amount) > 0,round((a.base_amount - a.disc1_amount)  * (a.tax_pct/100),0),0) Where a.id = ?invoiceId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_invoice_master a Set a.paid_amount = (a.base_amount - a.disc1_amount) + a.tax_amount + a.other_costs_amount Where a.id = ?invoiceId And a.payment_type = 0;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetInvoiceItemRow($invoiceId){
        $this->connector->CommandText = "Select count(*) As valresult From t_ar_invoice_detail as a Where a.invoice_id = ?invoiceId;";
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function GetJSonInvoices($cabangId,$customerId) {
        $sql = "SELECT a.id,a.invoice_no,a.invoice_date,a.tax_pct FROM t_ar_invoice_master as a Where a.invoice_status <> 3 And a.is_deleted = 0 And a.cabang_id = ".$cabangId." And a.customer_id = ".$customerId;
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By a.invoice_no Asc";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetJSonInvoiceItems($invoiceId = 0) {
        $sql = "SELECT a.id,a.item_id,a.item_code,a.item_descs,a.qty - a.qty_return as qty_jual,b.bsatbesar as satuan,round(a.sub_total/a.qty,0) as price,a.tax_pct,c.gudang_id FROM t_ar_invoice_detail AS a";
        $sql.= " JOIN m_barang AS b ON a.item_code = b.bkode JOIN t_ar_invoice_master AS c ON a.invoice_id = c.id Where (a.qty - a.qty_return) > 0 And a.invoice_id = ".$invoiceId;
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By a.invoice_no Asc";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function UpdatePrintCounter($invoiceId = 0,$userId = 0){
        $sql = "Update t_ar_invoice_master a Set a.print_count = a.print_count +1,a.lastprintby_id = $userId,a.lastprint_time = now() Where a.id = $invoiceId;";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function Load4ProfitTransaksi($entityId, $cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.cabang_code,a.invoice_date,a.invoice_no,a.customer_name,a.invoice_descs,a.total_amount,a.real_total_hpp as total_hpp,a.total_return FROM vw_ar_invoice_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_status <> 3 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Order By a.invoice_date,a.invoice_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ProfitTanggal($entityId, $cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.cabang_id,a.cabang_code,a.invoice_date,sum(a.total_amount) as sumSale,sum(a.real_total_hpp) as sumHpp,sum(a.total_return) as sumReturn FROM vw_ar_invoice_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_status <> 3 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Group By a.invoice_date";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ProfitBulan($entityId, $cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.cabang_id,a.cabang_code,Year(a.invoice_date) as tahun,Month(a.invoice_date) as bulan,sum(a.total_amount) as sumSale,sum(a.real_total_hpp) as sumHpp,sum(a.total_return) as sumReturn FROM vw_ar_invoice_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_status <> 3 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Group By Year(a.invoice_date),Month(a.invoice_date)";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ProfitDetail($entityId, $cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.*,b.item_code,b.item_descs,b.qty,b.price,b.disc_formula,b.disc_amount,b.sub_total,b.item_hpp FROM vw_ar_invoice_master AS a Join t_ar_invoice_detail b On a.invoice_no = b.invoice_no";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_status <> 3 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Order By a.invoice_date,a.invoice_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ProfitItem($entityId, $cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,b.item_descs,c.bsatkecil as satuan,coalesce(sum(b.qty),0) as sum_qty,coalesce(sum(b.sub_total),0) as sum_total,coalesce(sum(b.qty_return * (b.sub_total/b.qty)),0) as sum_return,coalesce(sum((b.qty - b.qty_return) * b.item_hpp),0) as sum_hpp";
        $sql.= " FROM vw_ar_invoice_master AS a Join t_ar_invoice_detail AS b On a.invoice_no = b.invoice_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.invoice_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Group By b.item_code,b.item_descs,c.bsatkecil Order By b.item_descs,b.item_code";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    //function post so detail into sales invoice
    public function PostSoDetail2Invoice($id,$invno,$sono){
        $nrt = 0;
        $sql = "Update t_ar_invoice_master a Join t_ar_so_master b On a.ex_so_no = b.so_no";
        $sql.= " Set a.base_amount = b.base_amount, a.tax_pct = b.tax_pct, a.tax_amount = b.tax_amount, a.disc1_pct = b.disc1_pct, a.disc1_amount = b.disc1_amount, a.disc2_pct = b.disc2_pct, a.disc2_amount = b.disc2_amount, a.other_costs = b.other_costs, a.other_costs_amount = b.other_costs_amount";
        $sql.= " Where a.id = $id";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteNonQuery();
        $sql = "Insert Into t_ar_invoice_detail (ex_so_no,invoice_id,cabang_id,invoice_no,item_id,item_code,item_descs,qty,price,disc_formula,disc_amount,sub_total)";
        $sql.= " Select a.so_no,$id,a.cabang_id,'".$invno."',a.item_id,a.item_code,a.item_descs,a.order_qty-a.send_qty,a.price,a.disc_formula,a.disc_amount,a.sub_total From t_ar_so_detail AS a Where a.so_no = '".$sono."' And a.order_qty > a.send_qty Order By a.id";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs){
            $nrt = 1;
            #Post detailnya
            $this->connector->CommandText = "SELECT fc_ar_invoicedetail_all_post($id) As valresult;";
            if ($this->connector->ExecuteQuery()) {
                $nrt = 2;
            }
            /* -- revised 2018-08-17 --
            #Update SO Qty received
            $this->connector->CommandText = "Update t_ar_so_detail AS a Set a.send_qty = a.order_qty Where a.so_no = '".$sono."'";
            if ($this->connector->ExecuteQuery()){
                $nrt = 3;
            }
            #Update PO status
            $sql = "Update t_ar_so_master AS a Set a.so_status = 2 Where a.so_no = '".$sono."'";
            $this->connector->CommandText = $sql;
            $this->connector->ExecuteNonQuery();
            if ($this->connector->ExecuteQuery()){
                $nrt = 4;
            }
            */
            #Autohecking sales order sended qty and update so status
            $this->connector->CommandText = "SELECT fc_ar_so_checkstatus('".$sono."') As valresult;";
            if ($this->connector->ExecuteQuery()) {
                $nrt = 3;
            }
        }
        return $nrt;
    }

    public function UpdateCustomerOutstanding($cabangId = 0,$customerId = 0) {
        $this->connector->CommandText = "SELECT fcArUpdateCustomerOutstanding($cabangId,$customerId) As valresult;";
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

}


// End of File: estimasi.php
