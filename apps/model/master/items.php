<?php
class Items extends EntityBase {
	public $Bid;
	public $IsDeleted = false;
	public $Bkode;
	public $Bnama;
    public $Bketerangan;
    public $Bjenis = 'BARANG';
    public $Bdivisi;
    public $Bkelompok;
    public $Bsupplier;
    public $Bbarcode;
    public $Bsatbesar;
    public $Bsatkecil;
    public $Bisisatkecil = 1;
    public $Bisaktif = 1;
    public $Bdnama;
    public $Bgnama;
    public $Bsnama;
    public $CreatebyId;
    public $UpdatebyId;
    public $Bhargabeli = 0;
    public $Bhargajual1 = 0;
    public $Bhargajual2 = 0;
    public $Bqtystock = 0;
    public $Bminstock = 0;
    public $Bisallowmin = 0;
    public $CabangId = 0;
    public $ItemLevel = 2;
    public $IsSale = 1;
    public $IsPurchase = 1;
    public $IsStock = 1;
    public $IsTimbang = 0;
    public $Bnama2;
    public $Bnamaskt;
    public $KelompokId = 0;

	public function __construct($bid = null) {
		parent::__construct();
		if (is_numeric($bid)) {
			$this->FindById($bid);
		}
	}

	public function FillProperties(array $row) {
		$this->Bid = $row["bid"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->Bkode = $row["bkode"];
		$this->Bnama = $row["bnama"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->Bketerangan = $row["bketerangan"];
        $this->Bjenis = $row["bjenis"];
        $this->Bdivisi = $row["bdivisi"];
        $this->Bkelompok = $row["bkelompok"];
        $this->Bsupplier = $row["bsupplier"];
        $this->Bbarcode = $row["bbarcode"];
        $this->Bsatbesar = $row["bsatbesar"];
        $this->Bsatkecil = $row["bsatkecil"];
        $this->Bisisatkecil = $row["bisisatkecil"];
        $this->Bisaktif = $row["bisaktif"];
        $this->Bdnama = $row["bdivisi"];
        $this->Bgnama = $row["bkelompok"];
        $this->Bsnama = $row["bsnama"];
        $this->Bhargabeli = $row["bhargabeli"];
        $this->Bhargajual1 = $row["bhargajual1"];
        $this->Bhargajual2 = $row["bhargajual2"];
        $this->Bqtystock = $row["bqtystock"];
        $this->Bminstock = $row["bminstock"];
        $this->Bisallowmin = $row["bisallowmin"];
        $this->CabangId = $row["cabang_id"];
        $this->Bnama2 = $row["bnama2"];
        $this->Bnamaskt = $row["bnamaskt"];
        $this->IsSale = $row["is_sale"];
        $this->IsPurchase = $row["is_purchase"];
        $this->IsStock = $row["is_stock"];
        $this->IsTimbang = $row["is_timbang"];
        $this->KelompokId = $row["kelompok_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($entityId,$cabangId,$orderBy = "a.bkode", $includeDeleted = false) {
        $sqx = "SELECT a.* FROM vw_m_barang AS a Where a.bisaktif = 1";
		if ($includeDeleted) {
			$sqx.= " And a.is_deleted = 0";
		}
        $sqx.= " And Not (a.item_level = 1 And a.entity_id <> $entityId) And Not (a.item_level = 2 And a.cabang_id <> $cabangId)";
        $sqx.= " ORDER BY $orderBy;";
        $this->connector->CommandText = $sqx;
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Items();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadItemList($entityId,$cabangId,$itemStatus = 1,$orderBy = "a.bkode", $includeDeleted = false) {
        $sqx = "SELECT a.* FROM vw_m_barang AS a ";
        if ($itemStatus == -1){
            $sqx.= "Where a.bisaktif > -1";
        }else{
            $sqx.= "Where a.bisaktif = $itemStatus";
        }
        if ($includeDeleted) {
            $sqx.= " And a.is_deleted = 0";
        }
        $sqx.= " And Not (a.item_level = 1 And a.entity_id <> $entityId) And Not (a.item_level = 2 And a.cabang_id <> $cabangId)";
        $sqx.= " ORDER BY $orderBy;";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Items();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadBySupplierId($suppId = 0,$itemStatus = 1,$orderBy = "a.bkode", $includeDeleted = false) {
        $sqx = "SELECT a.* FROM vw_m_barang AS a Join m_contacts b ON a.bsupplier = b.contact_code";
        if ($itemStatus == -1){
            $sqx.= " Where a.bisaktif > -1";
        }else{
            $sqx.= " Where a.bisaktif = $itemStatus";
        }
        if ($includeDeleted) {
            $sqx.= " And a.is_deleted = 0";
        }
        if ($suppId > 0){
            $sqx.= " And b.id = ".$suppId;
        }
        $sqx.= " ORDER BY $orderBy;";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Items();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	/**
	 * @param int $bid
	 * @return Location
	 */
	public function FindById($bid) {
		$this->connector->CommandText = "SELECT a.* FROM vw_m_barang AS a WHERE a.bid = ?bid";
		$this->connector->AddParameter("?bid", $bid);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByKode($bkode) {
        $this->connector->CommandText = "SELECT a.* FROM vw_m_barang AS a WHERE a.bkode = ?bkode";
        $this->connector->AddParameter("?bkode", $bkode);
        $rs = $this->connector->ExecuteQuery();

        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByBarCode($bcode) {
        $this->connector->CommandText = "SELECT a.* FROM vw_m_barang AS a WHERE a.bbarcode = ?bcode";
        $this->connector->AddParameter("?bcode", $bcode);
        $rs = $this->connector->ExecuteQuery();

        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

	/**
	 * @param int $bid
	 * @return Location
	 */
	public function LoadById($bid) {
		return $this->FindById($bid);
	}

    public function LoadByKode($bkode) {
        return $this->FindByKode($bkode);
    }

	public function Insert() {
        $sql = 'INSERT INTO m_barang(kelompok_id,bhargajual2,is_purchase,is_sale,is_stock,is_timbang,cabang_id,item_level,bhargajual1,bhargabeli,bkode,bnama,bketerangan,bjenis,bdivisi,bkelompok,bsupplier,bbarcode,bsatbesar,bsatkecil,bisisatkecil,bisaktif,createby_id,create_time,bminstock,bisallowmin,updateby_id,update_time)';
        $sql.= ' VALUES(?kelompok_id,?bhargajual2,?is_purchase,?is_sale,?is_stock,?is_timbang,?cabang_id,?item_level,?bhargajual1,?bhargabeli,?bkode,?bnama,?bketerangan,?bjenis,?bdivisi,?bkelompok,?bsupplier,?bbarcode,?bsatbesar,?bsatkecil,?bisisatkecil,?bisaktif,?createby_id,now(),?bminstock,?bisallowmin,?createby_id,now())';
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?bhargajual1", $this->Bhargajual1);
        $this->connector->AddParameter("?bhargajual2", $this->Bhargajual2);
        $this->connector->AddParameter("?bhargabeli", $this->Bhargabeli);
        $this->connector->AddParameter("?bkode", $this->Bkode, "char");
        $this->connector->AddParameter("?bbarcode", $this->Bbarcode,"char");
        $this->connector->AddParameter("?bnama", $this->Bnama, "char");
        $this->connector->AddParameter("?bketerangan", $this->Bketerangan);
        $this->connector->AddParameter("?bjenis", $this->Bjenis);
        $this->connector->AddParameter("?bdivisi", $this->Bdivisi);
        $this->connector->AddParameter("?bkelompok", $this->Bkelompok,"char");
        $this->connector->AddParameter("?bsupplier", $this->Bsupplier);
        $this->connector->AddParameter("?bsatbesar", $this->Bsatbesar);
        $this->connector->AddParameter("?bsatkecil", $this->Bsatkecil);
        $this->connector->AddParameter("?bisisatkecil", $this->Bisisatkecil);
        $this->connector->AddParameter("?bisaktif", $this->Bisaktif);
        $this->connector->AddParameter("?bminstock", $this->Bminstock);
        $this->connector->AddParameter("?bisallowmin", $this->Bisallowmin);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?item_level", $this->ItemLevel);
        $this->connector->AddParameter("?is_timbang", $this->IsTimbang);
        $this->connector->AddParameter("?is_stock", $this->IsStock);
        $this->connector->AddParameter("?is_sale", $this->IsSale);
        $this->connector->AddParameter("?is_purchase", $this->IsPurchase);
        $this->connector->AddParameter("?kelompok_id", $this->KelompokId);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		$rs = $this->connector->ExecuteNonQuery();
        $rcn = 0;
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Bid = (int)$this->connector->ExecuteScalar();
            // check apakan data stocknya ada? jika belum isi stock di cabang input = 0 dulu
            $sql = "Select * From t_ic_stockcenter a Where a.item_code = '".$this->Bkode."' And a.cabang_id = ".$this->CabangId;
            $this->connector->CommandText = $sql;
            $rcn = $this->connector->ExecuteQuery()->GetNumRows();
            if ($rcn == 0){
                $sql = "Insert Into t_ic_stockcenter (cabang_id,item_id,item_code,qty_stock)";
                $sql.= " Values(".$this->CabangId.",".$this->Bid.",'".$this->Bkode."',0)";
                $this->connector->CommandText = $sql;
                $rs = $this->connector->ExecuteNonQuery();
            }
        }
        return $rs;
	}

	public function Update($bid) {
	    $sqx = 'UPDATE m_barang 
SET cabang_id = ?cabang_id, 
item_level = ?item_level, 
bhargabeli = ?bhargabeli, 
bhargajual1 = ?bhargajual1,
bhargajual2 = ?bhargajual2,
bkode = ?bkode, 
bnama = ?bnama,
bketerangan = ?bketerangan,
bjenis = ?bjenis,
bdivisi = ?bdivisi,
bkelompok = ?bkelompok,
bsupplier = ?bsupplier,
bbarcode = ?bbarcode,
bsatbesar = ?bsatbesar,
bsatkecil = ?bsatkecil,
bisisatkecil = ?bisisatkecil,
bisaktif = ?bisaktif, 
bminstock = ?bminstock, 
bisallowmin = ?bisallowmin, 
is_sale = ?is_sale,
is_purchase = ?is_purchase,
is_stock = ?is_stock,
is_timbang = ?is_timbang,
kelompok_id = ?kelompok_id,
updateby_id = ?updateby_id, 
update_time = now()
WHERE bid = ?bid';
		$this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?bhargajual1", $this->Bhargajual1);
        $this->connector->AddParameter("?bhargajual2", $this->Bhargajual2);
        $this->connector->AddParameter("?bhargabeli", $this->Bhargabeli);
        $this->connector->AddParameter("?bkode", $this->Bkode, "char");
        $this->connector->AddParameter("?bbarcode", $this->Bbarcode,"char");
        $this->connector->AddParameter("?bnama", $this->Bnama, "char");
        $this->connector->AddParameter("?bketerangan", $this->Bketerangan);
        $this->connector->AddParameter("?bjenis", $this->Bjenis);
        $this->connector->AddParameter("?bdivisi", $this->Bdivisi);
        $this->connector->AddParameter("?bkelompok", $this->Bkelompok, "char");
        $this->connector->AddParameter("?bsupplier", $this->Bsupplier);
        $this->connector->AddParameter("?bsatbesar", $this->Bsatbesar);
        $this->connector->AddParameter("?bsatkecil", $this->Bsatkecil);
        $this->connector->AddParameter("?bisisatkecil", $this->Bisisatkecil);
        $this->connector->AddParameter("?bisaktif", $this->Bisaktif);
        $this->connector->AddParameter("?bminstock", $this->Bminstock);
        $this->connector->AddParameter("?bisallowmin", $this->Bisallowmin);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?item_level", $this->ItemLevel);
        $this->connector->AddParameter("?is_timbang", $this->IsTimbang);
        $this->connector->AddParameter("?is_stock", $this->IsStock);
        $this->connector->AddParameter("?is_sale", $this->IsSale);
        $this->connector->AddParameter("?is_purchase", $this->IsPurchase);
        $this->connector->AddParameter("?kelompok_id", $this->KelompokId);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?bid", $bid);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($bid) {
		$this->connector->CommandText = 'UPDATE m_barang SET is_deleted = 1, bisaktif = 0, updateby_id = ?updateby_id, update_time = now() WHERE bid = ?bid';
		$this->connector->AddParameter("?bid", $bid);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

    public function NonAktifkan($bid) {
        $this->connector->CommandText = 'UPDATE m_barang SET bisaktif = 0, updateby_id = ?updateby_id, update_time = now() WHERE bid = ?bid';
        $this->connector->AddParameter("?bid", $bid);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        return $this->connector->ExecuteNonQuery();
    }

    public function Aktifkan($bid) {
        $this->connector->CommandText = 'UPDATE m_barang SET bisaktif = 1, updateby_id = ?updateby_id, update_time = now() WHERE bid = ?bid';
        $this->connector->AddParameter("?bid", $bid);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetDataGrid($entityId = 0,$cabangId = 0,$offset,$limit,$field,$search='',$sort = 'a.skode',$order = 'ASC') {
        $sql = "SELECT a.* FROM vw_m_barang as a Where a.is_deleted = 0 and a.bisaktif = 1";
        if ($search !='' && $field !=''){
            $sql.= "And $field Like '%{$search}%' ";
        }
        $sql.= " And Not (a.item_level = 1 And a.entity_id <> $entityId) And Not (a.item_level = 2 And a.cabang_id <>$cabangId)";
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= "Order By $sort $order Limit {$offset},{$limit}";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $rows = array();
        if ($rs != null) {
            $i = 0;
            while ($row = $rs->FetchAssoc()) {
                $rows[$i]['sid'] = $row['sid'];
                $rows[$i]['skode'] = $row['skode'];
                $rows[$i]['snama'] = $row['snama'];
                $i++;
            }
        }
        //data hasil query yang dikirim kembali dalam format json
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetJSonItems($entityId,$cabangId,$filter = null,$sort = 'a.bnama',$order = 'ASC') {
        $sql = "SELECT a.bid, a.bkode, a.bbarcode, a.bnama, a.bsatbesar, a.bsatkecil, a.bqtystock, a.bhargabeli, a.bhargajual1 FROM vw_m_barang as a Where a.is_deleted = 0 And a.bisaktif = 1";
        if ($filter != null){
            $sql.= " And (a.bkode Like '%$filter%' Or a.bbarcode Like '%$filter%' Or a.bnama Like '%$filter%')";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function DeleteProcess() {
        $this->connector->CommandText = "Delete From m_barang WHERE bkode = ?item_code";
        $this->connector->AddParameter("?item_code",$this->Bkode);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetAutoPLU($kdDept){
        // function untuk menggenerate kode PLU
        $lkode = strlen($kdDept);
        $plu = $kdDept.str_pad(1,6-$lkode,'0', STR_PAD_LEFT);
        $this->connector->CommandText = "SELECT coalesce(max(a.bkode),0) AS lplu FROM m_barang a WHERE LEFT(a.bkode,?lkode) = ?kode";
        $this->connector->AddParameter("?lkode", $lkode);
        $this->connector->AddParameter("?kode", $kdDept);
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            if ($row["lplu"] != "0"){
                $plu = $row["lplu"];
                $num = right($plu,6-$lkode);
                $num++;
                $plu = $kdDept.str_pad($num,6-$lkode,'0', STR_PAD_LEFT);
            }
        }
        return $plu;
    }

    public function GetAutoPLU1($kdDept){
        // function untuk menggenerate kode PLU
        $lkode = strlen($kdDept);
        $plu = $kdDept.str_pad(1,6-$lkode,'0', STR_PAD_LEFT);
        $this->connector->CommandText = "SELECT coalesce(max(a.bkelompok),0) AS lplu FROM m_barang a WHERE LEFT(a.bkelompok,?lkode) = ?kode";
        $this->connector->AddParameter("?lkode", $lkode);
        $this->connector->AddParameter("?kode", $kdDept);
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            if ($row["lplu"] != "0"){
                $plu = $row["lplu"];
                $num = right($plu,6-$lkode);
                $num++;
                $plu = $kdDept.str_pad($num,6-$lkode,'0', STR_PAD_LEFT);
            }
        }
        return $plu;
    }
}
