<?php
class Correction extends EntityBase {
	public $Id;
	public $CabangId;
	public $WarehouseId;
    public $CorrNo;
	public $ItemId;
    public $ItemCode;
    public $CorrDate;   
    public $CorrQty = 0;
    public $CorrReason;
    public $CorrStatus;
    public $SysQty = 0;
    public $WhsQty = 0;
    public $Hpp = 0;
    public $CreatebyId;
    public $UpdatebyId;
    public $ReffNo;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->CabangId = $row["cabang_id"];
        $this->WarehouseId = $row["warehouse_id"];
        $this->CorrNo = $row["corr_no"];
        $this->CorrDate = strtotime($row["corr_date"]);
		$this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
        $this->CorrReason = $row["corr_reason"];
        $this->SysQty = $row["sys_qty"];
        $this->WhsQty = $row["whs_qty"];
        $this->CorrQty = $row["corr_qty"];
        $this->Hpp = $row["hpp"];
        $this->CorrStatus = $row["corr_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->ReffNo = $row["reff_no"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($cabangId = 0,$orderBy = "a.cabang_id, a.item_code") {
        if ($cabangId == 0){
            $this->connector->CommandText = "SELECT a.* FROM t_ic_stockcorrection AS a ORDER BY $orderBy";
        }else{
            $this->connector->CommandText = "SELECT a.* FROM t_ic_stockcorrection AS a Where a.cabang_id = $cabangId ORDER BY $orderBy";
        }
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Correction();
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
		$this->connector->CommandText = "SELECT a.* FROM t_ic_stockcorrection AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByKode($cabangId,$itemCode) {
        $this->connector->CommandText = "SELECT a.* FROM t_ic_stockcorrection AS a WHERE a.cabang_id = ?cabangId And a.item_code = ?itemCode";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?itemCode", $itemCode);
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
        $sql = 'INSERT INTO t_ic_stockcorrection (reff_no,hpp,warehouse_id,cabang_id,corr_no,corr_date,item_id,item_code,corr_reason,sys_qty,whs_qty,corr_qty,corr_status,createby_id,create_time)';
        $sql.= ' VALUES(?reff_no,?hpp,?warehouse_id,?cabang_id,?corr_no,?corr_date,?item_id,?item_code,?corr_reason,?sys_qty,?whs_qty,?corr_qty,?corr_status,?createby_id,now())';
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?warehouse_id", $this->WarehouseId);
        $this->connector->AddParameter("?corr_no", $this->CorrNo,"char");
        $this->connector->AddParameter("?corr_date", $this->CorrDate);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode,"char");
        $this->connector->AddParameter("?corr_reason", $this->CorrReason);
        $this->connector->AddParameter("?sys_qty", $this->SysQty);
        $this->connector->AddParameter("?whs_qty", $this->WhsQty);
        $this->connector->AddParameter("?corr_qty", $this->CorrQty);
        $this->connector->AddParameter("?hpp", $this->Hpp);
        $this->connector->AddParameter("?corr_status", $this->CorrStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?reff_no", $this->ReffNo);
        $rs = $this->connector->ExecuteNonQuery();
        $ret = 0;
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $ret = $this->Id;
            $this->connector->CommandText = "SELECT fc_ic_stockcorrection_post($ret) As valresult;";
            $rs = $this->connector->ExecuteQuery();
            $row = $rs->FetchAssoc();
            return strval($row["valresult"]);
        }
		return $ret;
	}

	public function Delete($id) {
        $this->connector->CommandText = "SELECT fc_ic_stockcorrection_unpost($id) As valresult;";
        $rs = $this->connector->ExecuteQuery();
        //$row = $rs->FetchAssoc();
		$this->connector->CommandText = 'Delete From t_ic_stockcorrection Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function Void($id) {
        $this->connector->CommandText = "SELECT fc_ic_stockcorrection_unpost($id) As valresult;";
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        $this->connector->CommandText = 'Update t_ic_stockcorrection a Set a.corr_status = 3 Where a.id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetData($cabangId = 0,$offset,$limit,$field,$search='',$sort = 'a.item_code',$order = 'ASC') {
        $sql = "SELECT a.* FROM vw_ic_stockcorrection as a Where a.item_id > 0 ";
        if ($cabangId > 0){
            $sql.= " And cabang_id = ".$cabangId;
        }
        if ($search !='' && $field !=''){
            $sql.= " And $field Like '%{$search}%' ";
        }else{
            $sql.= " And a.corr_status < 2";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order Limit {$offset},{$limit}";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $rows = array();
        if ($rs != null) {
            $i = 0;
            while ($row = $rs->FetchAssoc()) {
                $rows[$i]['id'] = $row['id'];
                $rows[$i]['cabang_id'] = $row['cabang_id'];
                $rows[$i]['cabang_code'] = $row['cabang_code'];
                $rows[$i]['corr_date'] = left($row['corr_date'],10);
                $rows[$i]['corr_no'] = $row['corr_no'];
                $rows[$i]['corr_reason'] = $row['corr_reason'];
                $rows[$i]['item_id'] = $row['item_id'];
                $rows[$i]['item_code'] = $row['item_code'];
                $rows[$i]['bbarcode'] = $row['bbarcode'];
                $rows[$i]['sys_qty'] = number_format($row['sys_qty']);
                $rows[$i]['whs_qty'] = number_format($row['whs_qty']);
                $rows[$i]['corr_qty'] = number_format($row['corr_qty']);
                $rows[$i]['bnama'] = $row['bnama'];
                $rows[$i]['bsatbesar'] = $row['bsatbesar'];
                $rows[$i]['bsatkecil'] = $row['bsatkecil'];
                $rows[$i]['warehouse_id'] = $row['warehouse_id'];
                $rows[$i]['wh_code'] = $row['wh_code'];
                $rows[$i]['hpp'] = $row['hpp'];
                $rows[$i]['reff_no'] = $row['reff_no'];
                $i++;
            }
        }
        //data hasil query yang dikirim kembali dalam format json
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetCorrectionDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'ICR';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->CorrDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function Load4Reports($cabangId = 0,$gudangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_ic_stockcorrection AS a WHERE a.corr_status <> 3 and Date(a.corr_date) BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($gudangId > 0){
            $sql.= " and a.warehouse_id = ".$gudangId;
        }
        $sql.= " Order By a.corr_date,a.corr_no,a.item_code,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadRekap4Reports($cabangId = 0,$gudangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.cabang_code,a.wh_code,a.item_code,a.bnama,a.bsatkecil,a.hpp,a.harga_beli,a.harga_jual,sum(a.corr_qty) as qty FROM vw_ic_stockcorrection AS a";
        $sql.= " WHERE a.corr_status <> 3 and Date(a.corr_date) BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($gudangId > 0){
            $sql.= " and a.warehouse_id = ".$gudangId;
        }
        $sql.= " Group By a.cabang_code,a.wh_code,a.item_code,a.bnama,a.bsatkecil,a.hpp,a.harga_beli,a.harga_jual";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }
}
