<?php
/**
 * Created by PhpStorm.
 * User: BudiAditya
 * Date: 06/04/2019
 * Time: 15:46
 */

class Transaksi extends EntityBase {

    public function Load4Reports($cabangId = 0, $trxStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_pos_master AS a";
        $sql.= " WHERE a.tanggal BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($trxStatus > -1){
            $sql.= " and a.trx_status = ".$trxStatus;
        }else{
            $sql.= " and a.trx_status <> 3 ";
        }
        $sql.= " Order By a.tanggal,a.trx_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsDetail($cabangId = 0, $trxStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.*,b.item_code,c.bnama AS item_descs,b.qty_keluar AS qty,b.harga AS price,b.diskon_persen,b.diskon_nilai,b.sub_total FROM vw_pos_master AS a Join t_pos_detail AS b On a.trx_no = b.trx_no Join m_barang AS c ON b.item_code = c.bkode";
        $sql.= " WHERE a.tanggal BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($trxStatus > -1){
            $sql.= " and a.trx_status = ".$trxStatus;
        }else{
            $sql.= " and a.trx_status <> 3 ";
        }
        $sql.= " Order By a.tanggal,a.trx_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsRekapItem($cabangId = 0, $trxStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,c.bnama AS item_descs,b.satuan,b.harga AS price, coalesce(sum(b.qty_keluar),0) as sum_qty,coalesce(sum(b.sub_total),0) as sum_total, 0 as sum_tax";
        $sql.= " FROM vw_pos_master AS a Join t_pos_detail AS b On a.trx_no = b.trx_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.tanggal BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($trxStatus > -1){
            $sql.= " and a.trx_status = ".$trxStatus;
        }else{
            $sql.= " and a.trx_status <> 3 ";
        }
        $sql.= " Group By b.item_code,c.bnama,b.satuan,b.harga Order By c.bnama,b.item_code,b.harga";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadPosMaster($id){
        $sql = "Select a.*,b.bank,b.no_kartu,b.nama_pemilik,b.admin_persen,b.admin_nilai From vw_pos_master AS a LEFT JOIN t_pos_trxkartu AS b ON a.trx_no = b.trx_no Where a.id = $id";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs->FetchAssoc();
    }

    public function LoadPosDetail($id){
        $sql = "Select a.*,b.bnama as item_name,b.bbarcode";
        $sql.= " From t_pos_detail AS a JOIN m_barang AS b ON a.item_code = b.bkode JOIN t_pos_master AS c ON a.trx_no = c.trx_no";
        $sql.= " Where a.qty_keluar > 0 And c.id = $id Order By a.id";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
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

}