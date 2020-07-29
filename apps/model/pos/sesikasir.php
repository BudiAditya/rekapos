<?php
/**
 * Created by PhpStorm.
 * User: BudiAditya
 * Date: 06/04/2019
 * Time: 15:46
 */

class SesiKasir extends EntityBase {

    public function __construct($id = null) {
        parent::__construct();
        $this->connector = ConnectorManager::GetPool("member");
    }

    public function Load4Reports($cabangId = 0, $kasirId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_pos_session_mix AS a";
        $sql.= " WHERE a.open_time BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($kasirId > 0){
            $sql.= " and a.kasir_id = ".$kasirId;
        }
        $sql.= " Order By a.open_time,a.session_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadSesiKasir($id){
        $sql = "Select a.* From vw_pos_session_mix AS a Where a.id = $id";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs->FetchAssoc();
    }

    public function LoadSesiKasirTrx($id){
        $sql = "Select a.*,b.trx_no,b.waktu,b.sub_total,b.diskon_nilai,b.total_transaksi,b.bayar_tunai,b.bayar_kk,b.bayar_kd From vw_pos_session_mix AS a JOIN vw_pos_master_mix AS b ON a.session_no = b.session_no Where a.id = $id Order By b.waktu";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Approve($id,$kasKasir,$selisihKas,$ket,$approveById){
        $sql = "Update vw_pos_session_mix a Set a.session_status = 3, a.tunai_kasir = ?kasKasir, a.selisih_kas = ?selisihKas, a.keterangan = ?ket, a.approvedby_id = ?approveById, a.approved_time = now() Where a.id = ?id";
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