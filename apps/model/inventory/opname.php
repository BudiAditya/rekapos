<?php
class Opname extends EntityBase {
	public $Id;
	public $CabangId;
	public $WarehouseId;
    public $OpnNo;
	public $ItemId;
    public $ItemCode;
    public $BarCode;
    public $OpnTime;   
    public $OpnQty = 0;
    public $OpnStatus;
    public $IpAddress;
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
		$this->CabangId = $row["cabang_id"];
        $this->WarehouseId = $row["warehouse_id"];
        $this->OpnNo = $row["opn_no"];
        $this->OpnTime = strtotime($row["opn_time"]);
		$this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
        $this->BarCode = $row["bar_code"];
        $this->OpnQty = $row["opn_qty"];
        $this->OpnStatus = $row["opn_status"];
        $this->IpAddress = $row["ip_address"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($cabangId = 0,$orderBy = "a.cabang_id, a.item_code") {
        if ($cabangId == 0){
            $this->connector->CommandText = "SELECT a.* FROM t_ic_stockopname AS a ORDER BY $orderBy";
        }else{
            $this->connector->CommandText = "SELECT a.* FROM t_ic_stockopname AS a Where a.cabang_id = $cabangId ORDER BY $orderBy";
        }
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Opname();
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
		$this->connector->CommandText = "SELECT a.* FROM t_ic_stockopname AS a WHERE a.id = ?id";
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
        $this->connector->CommandText = "SELECT a.* FROM t_ic_stockopname AS a WHERE a.cabang_id = ?cabangId And a.item_code = ?itemCode";
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
        $sql = 'INSERT INTO t_ic_stockopname (cabang_id, warehouse_id, opn_no, opn_time, item_id, item_code, bar_code, opn_qty, opn_status, ip_address, createby_id, create_time)';
        $sql.= ' VALUES(?cabang_id, ?warehouse_id, ?opn_no, ?opn_time, ?item_id, ?item_code, ?bar_code, ?opn_qty, ?opn_status, ?ip_address, ?createby_id,now())';
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?warehouse_id", $this->WarehouseId);
        $this->connector->AddParameter("?opn_no", $this->OpnNo,"char");
        $this->connector->AddParameter("?opn_time", $this->OpnTime);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode,"char");
        $this->connector->AddParameter("?bar_code", $this->BarCode);
        $this->connector->AddParameter("?opn_qty", $this->OpnQty);
        $this->connector->AddParameter("?ip_address", $this->IpAddress);
        $this->connector->AddParameter("?opn_status", $this->OpnStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        $ret = 0;
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $ret = $this->Id;
        }
		return $ret;
	}

	public function Delete($id) {
        $this->connector->CommandText = 'Delete From t_ic_stockopname Where id = ?id And opn_status = 0';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function Void($id) {
        $this->connector->CommandText = 'Update t_ic_stockopname a Set a.opn_status = 3 Where a.id = ?id and a.opn_status = 0';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetData($cabangId = 0,$offset,$limit,$field,$search='',$sort = 'a.item_code',$order = 'ASC') {
        $sql = "SELECT a.* FROM vw_ic_stockopname as a Where a.opn_status = 0 ";
        if ($cabangId > 0){
            $sql.= " And cabang_id = ".$cabangId;
        }
        if ($search !='' && $field !=''){
            $sql.= " And $field Like '%{$search}%' ";
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
                $rows[$i]['opn_time'] = $row['opn_time'];
                $rows[$i]['opn_no'] = $row['opn_no'];
                $rows[$i]['item_id'] = $row['item_id'];
                $rows[$i]['item_code'] = $row['item_code'];
                $rows[$i]['bar_code'] = $row['bar_code'];
                $rows[$i]['opn_qty'] = number_format($row['opn_qty']);
                $rows[$i]['bnama'] = $row['bnama'];
                $rows[$i]['bsatbesar'] = $row['bsatbesar'];
                $rows[$i]['bsatkecil'] = $row['bsatkecil'];
                $rows[$i]['warehouse_id'] = $row['warehouse_id'];
                $rows[$i]['wh_code'] = $row['wh_code'];
                $rows[$i]['o_status'] = $row['o_status'];
                $rows[$i]['opn_status'] = $row['opn_status'];
                $i++;
            }
        }
        //data hasil query yang dikirim kembali dalam format json
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetOpnameDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'IOP';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->OpnTime);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function Load4Reports($cabangId = 0,$gudangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_ic_stockcorrection AS a WHERE a.opn_status <> 3 and a.opn_time BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($gudangId > 0){
            $sql.= " and a.warehouse_id = ".$gudangId;
        }
        $sql.= " Order By a.opn_time,a.opn_no,a.item_code,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadRekap4Reports($cabangId = 0,$gudangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.cabang_code,a.wh_code,a.item_code,a.bnama,a.bsatkecil,sum(a.opn_qty) as qty FROM vw_ic_stockcorrection AS a";
        $sql.= " WHERE a.opn_status <> 3 and a.opn_time BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($gudangId > 0){
            $sql.= " and a.warehouse_id = ".$gudangId;
        }
        $sql.= " Group By a.cabang_code,a.wh_code,a.item_code,a.bnama,a.bsatkecil";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }
}
