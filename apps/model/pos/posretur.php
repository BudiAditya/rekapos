<?php
/**
 * Created by PhpStorm.
 * User: BudiAditya
 * Date: 06/04/2019
 * Time: 15:46
 */

class PosRetur extends EntityBase {

    public function __construct($id = null) {
        parent::__construct();
        $this->connector = ConnectorManager::GetPool("member");
    }

    public function Load4Reports($cabangId = 0, $kondisi = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_pos_return_master_mix AS a";
        $sql.= " WHERE (a.rtn_date BETWEEN ?startdate And ?enddate) And a.rtn_status <> 3";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        $sql.= " Order By a.rtn_date,a.rtn_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsDetail($cabangId = 0, $kondisi = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.*,b.ex_trx_no,b.item_code,b.item_name AS item_descs,b.qty_keluar AS qty,b.harga AS price,b.sub_total,b.kondisi FROM vw_pos_return_master_mix AS a Join vw_pos_return_detail_mix AS b On a.rtn_no = b.rtn_no";
        $sql.= " WHERE a.rtn_date BETWEEN ?startdate and ?enddate And a.rtn_status <> 3";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($kondisi > -1){
            $sql.= " and b.kondisi = ".$kondisi;
        }
        $sql.= " Order By a.rtn_date,a.rtn_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsRekapItem($cabangId = 0, $kondisi = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,b.item_name,b.satuan,b.harga AS price, coalesce(sum(if(b.kondisi = 1 Or b.kondisi = 0,b.qty_retur,0)),0) as qty_bagus,coalesce(sum(if(b.kondisi = 2,b.qty_retur,0)),0) as qty_rusak,coalesce(sum(if(b.kondisi = 3,b.qty_retur,0)),0) as qty_expire,coalesce(sum(b.sub_total),0) as sum_total";
        $sql.= " FROM vw_pos_return_master_mix AS a Join vw_pos_return_detail_mix AS b On a.rtn_no = b.rtn_no";
        $sql.= " WHERE a.rtn_date BETWEEN ?startdate and ?enddate And a.rtn_status <> 3";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($kondisi > 0){
            $sql.= " and b.kondisi = ".$kondisi;
        }
        $sql.= " Group By b.item_code,b.item_name,b.satuan,b.harga Order By b.item_name,b.item_code,b.harga";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadPosReturnMaster($id){
        $sql = "Select a.* From vw_pos_return_master_mix AS a Where a.id = $id";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs->FetchAssoc();
    }

    public function LoadPosReturnDetail($id){
        $sql = "Select a.*";
        $sql.= " From vw_pos_return_detail_mix AS a JOIN vw_pos_return_master_mix AS b ON a.rtn_no = b.rtn_no";
        $sql.= " Where a.qty_retur > 0 And b.id = $id Order By a.id";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }
}