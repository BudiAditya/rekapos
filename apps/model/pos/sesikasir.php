<?php
/**
 * Created by PhpStorm.
 * User: BudiAditya
 * Date: 06/04/2019
 * Time: 15:46
 */

class SesiKasir extends EntityBase {

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

    public function LoadSesiKasir($id){
        $sql = "Select a.* From vw_t_pos_session AS a Where a.id = $id";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs->FetchAssoc();
    }

    public function LoadSesiKasirTrx($id){
        $sql = "Select a.*,b.trx_no,b.waktu,b.sub_total,b.diskon_nilai,b.total_transaksi,b.bayar_tunai,b.bayar_kk,b.bayar_kd From vw_t_pos_session AS a JOIN t_pos_master AS b ON a.session_no = b.session_no Where a.id = $id Order By b.waktu";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Approve($id,$kasKasir,$selisihKas,$ket,$approveById){
        $sql = "Update t_pos_session a Set a.session_status = 3, a.tunai_kasir = ?kasKasir, a.selisih_kas = ?selisihKas, a.keterangan = ?ket, a.approvedby_id = ?approveById, a.approved_time = now() Where a.id = ?id";
        $this->connector->AddParameter("?kasKasir", $kasKasir);
        $this->connector->AddParameter("?selisihKas", $selisihKas);
        $this->connector->AddParameter("?ket", $ket);
        $this->connector->AddParameter("?approveById", $approveById);
        $this->connector->AddParameter("?id", $id);
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

}