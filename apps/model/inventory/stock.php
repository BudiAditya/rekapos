<?php
class Stock extends EntityBase {
    public $Id;
    public $CabangId;
    public $KdCabang;
    public $NmCabang;
    public $WarehouseId;
    public $WarehouseCode;
    public $WarehouseName;
    public $ItemId;
    public $ItemCode;
    public $ItemName;
    public $QtyStock;
    public $SatBesar;
    public $SatKecil;
    public $IsiSat;
    public $CreatebyId;
    public $UpdatebyId;
    public $BarCode;

    public function __construct($id = null) {
        parent::__construct();
        if (is_numeric($id)) {
            $this->LoadById($id);
        }
    }

    public function FillProperties(array $row) {
        $this->Id = $row["id"];
        $this->CabangId = $row["cabang_id"];
        $this->KdCabang = $row["kode"];
        $this->NmCabang = $row["cabang"];
        $this->WarehouseId = $row["warehouse_id"];
        $this->WarehouseCode = $row["wh_code"];
        $this->WarehouseName = $row["wh_name"];
        $this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
        $this->ItemName = $row["bnama"];
        $this->QtyStock = $row["qty_stock"];
        $this->SatBesar = $row["bsatbesar"];
        $this->SatKecil = $row["bsatkecil"];
        $this->IsiSat = $row["bisisatkecil"];
        $this->BarCode = $row["bar_code"];
    }

    public function LoadAll($orderBy = "a.item_code") {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_stockcenter AS a ORDER BY $orderBy";
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Stock();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function Load4Excel($entityId,$cabangId,$userLevel,$orderBy = "a.item_code") {
        $sql = "SELECT a.* FROM vw_ic_stockcenter AS a Where a.cabang_id = $cabangId ORDER BY $orderBy";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Stock();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_stockcenter AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

   public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_stockcenter AS a WHERE a.cabang_id = ?cabang_id ORDER BY a.item_code";
        $this->connector->AddParameter("?cabang_id", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Stock();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_stockcenter AS a WHERE a.id = ?id";
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
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_stockcenter AS a WHERE a.cabang_id = ?cabangId And a.item_code = ?itemCode";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?itemCode", $itemCode, "char");
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByKodeGudang($gudangId,$itemCode) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_stockcenter AS a WHERE a.warehouse_id = ?gudangId And a.item_code = ?itemCode";
        $this->connector->AddParameter("?gudangId", $gudangId);
        $this->connector->AddParameter("?itemCode", $itemCode, "char");
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function FindByBarcodeGudang($gudangId,$barCode) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_stockcenter AS a WHERE a.warehouse_id = ?gudangId And a.bar_code = ?barCode";
        $this->connector->AddParameter("?gudangId", $gudangId);
        $this->connector->AddParameter("?barCode", $barCode, "char");
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function CheckStock($cabang_id = 0, $warehouse_id = 0,$item_code) {
        $sqx = null;
        $sqty = 0;
        $sqx = "SELECT coalesce(sum(a.qty_stock),0) as qtystock FROM vw_ic_stockcenter AS a WHERE a.item_code = ?item_code";
        if ($cabang_id > 0){
            $sqx.= " And a.cabang_id = ?cabang_id";
        }
        if ($warehouse_id > 0){
            $sqx.= " And a.warehouse_id = ?warehouse_id";
        }
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?cabang_id", $cabang_id);
        $this->connector->AddParameter("?warehouse_id", $warehouse_id);
        $this->connector->AddParameter("?item_code", $item_code, "char");
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return $sqty;
        }
        $row = $rs->FetchAssoc();
        $sqty = $row["qtystock"];
        return $sqty;
    }

    public function GetStockHistory($startDate = null, $endDate = null){
        $sqx = null;
        // create card temp table1
        $sqx = 'CREATE TEMPORARY TABLE `tmp_card` (
                `trx_date`  date NOT NULL ,
                `trx_type`  varchar(50),
                `trx_url`  varchar(50),
                `relasi`  varchar(50),
                `price`  int(11) NOT NULL DEFAULT 0,
                `awal`  decimal(11,2) NOT NULL DEFAULT 0,
                `masuk`  decimal(11,2) NOT NULL DEFAULT 0,
                `keluar`  decimal(11,2) NOT NULL DEFAULT 0,
                `koreksi`  decimal(11,2) NOT NULL DEFAULT 0,
                `saldo`  decimal(11,2) NOT NULL DEFAULT 0,
                `notes` varchar(250))';
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteNonQuery();
        // create card temp table2
        $sqx = 'CREATE TEMPORARY TABLE `tmp_card1` (
                `seq_no` int(1) NOT NULL DEFAULT 0,
                `trx_date`  date NOT NULL ,
                `trx_type`  varchar(50),
                `trx_url`  varchar(50),
                `relasi`  varchar(50),
                `price`  int(11) NOT NULL DEFAULT 0,
                `awal`  decimal(11,2) NOT NULL DEFAULT 0,
                `masuk`  decimal(11,2) NOT NULL DEFAULT 0,
                `keluar`  decimal(11,2) NOT NULL DEFAULT 0,
                `koreksi`  decimal(11,2) NOT NULL DEFAULT 0,
                `saldo`  decimal(11,2) NOT NULL DEFAULT 0,
                `notes` varchar(250))';
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteNonQuery();
        // get saldo awal
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,awal,relasi,price) Select a.op_date,'Saldo Awal','inventory.awal',a.op_qty,'-',0 From t_ic_saldoawal as a";
        $sqx.= " Where a.item_code = ?item_code And a.cabang_id = ?cabang_id And a.warehouse_id = ?gudang_id";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        // get pembelian
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,masuk,relasi)";
        $sqx.= " Select a.grn_date,concat('Pembelian - ',a.grn_no),concat('ap.purchase/view/',a.id),b.price,b.purchase_qty,concat(a.supplier_name,' (',a.supplier_code,')')";
        $sqx.= " From vw_ap_purchase_master as a Join t_ap_purchase_detail as b On a.grn_no = b.grn_no And a.cabang_id = b.cabang_id";
        $sqx.= " Where b.item_code = ?item_code and b.cabang_id = ?cabang_id and b.gudang_id = ?gudang_id and a.is_deleted = 0 and a.grn_status <> 3";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        // get transfer masuk
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,masuk,relasi)";
        $sqx.= " Select a.npb_date,concat('Pengiriman - ',a.npb_no),concat('inventory.transfer/view/',a.id),0,b.qty,concat('Dari Cabang - ',a.cabang_code)";
        $sqx.= " From vw_ic_transfer_master a Join t_ic_transfer_detail b On a.cabang_id = b.cabang_id And a.npb_no = b.npb_no";
        $sqx.= " Where b.item_code = ?item_code and a.to_cabang_id = ?cabang_id and a.to_warehouse_id = ?gudang_id and a.is_deleted = 0 and a.npb_status <> 3";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        // get return ex penjualan
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,masuk,relasi)";
        $sqx.= " Select a.rj_date,concat('Return - ',a.rj_no),concat('ar.arreturn/view/',a.id),0,b.qty_retur,concat(a.customer_name,' (',a.customer_code,')')";
        $sqx.= " From vw_ar_return_master a Join t_ar_return_detail b On a.cabang_id = b.cabang_id And a.rj_no = b.rj_no";
        $sqx.= " Where b.item_code = ?item_code and a.cabang_id = ?cabang_id and b.gudang_id = ?gudang_id and a.is_deleted = 0 and a.rj_status <> 3";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        //if ($this->WarehouseCode == 'Display') {
            // get return ex penjualan retail
            $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,masuk,relasi)";
            $sqx .= " Select date(a.rtn_time),concat('Return - ',a.rtn_no),'-',0,b.qty_retur,'Cash'";
            $sqx .= " From t_pos_return_master a Join t_pos_return_detail b On a.rtn_no = b.rtn_no";
            $sqx .= " Where b.item_code = ?item_code and a.cabang_id = ?cabang_id and b.is_posted = 1 and a.rtn_status <> 3";
            $this->connector->CommandText = $sqx;
            $this->connector->AddParameter("?item_code", $this->ItemCode, "varchar");
            $this->connector->AddParameter("?cabang_id", $this->CabangId);
            $rs = $this->connector->ExecuteNonQuery();
        //}

        // get barang masuk hasil produksi
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,masuk,relasi)";
        $sqx.= " Select a.assembly_date,concat('Produksi - ',a.assembly_no),concat('inventory.assembly/view/',a.id),0,a.qty,'Hasil Produksi'";
        $sqx.= " From t_ic_assembly_master as a Where a.item_code = ?item_code and a.cabang_id = ?cabang_id and a.warehouse_id = ?gudang_id and a.is_deleted = 0 and a.assembly_status <> 3";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        // get barang keluar untuk produksi
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,keluar,relasi,notes)";
        $sqx.= " Select b.assembly_date,concat('Produksi - ',b.assembly_no),concat('inventory.assembly/view/',b.id),0,a.qty,'Untuk Produksi',a.item_note";
        $sqx.= " From t_ic_assembly_detail as a Join t_ic_assembly_master as b On a.cabang_id = b.cabang_id And a.assembly_no = b.assembly_no";
        $sqx.= " Where a.item_code = ?item_code and a.cabang_id = ?cabang_id and b.warehouse_id = ?gudang_id and b.is_deleted = 0 and b.assembly_status <> 3";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        // get penjualan
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,keluar,relasi,notes)";
        $sqx.= " Select a.invoice_date,concat('Penjualan - ',a.invoice_no),concat('ar.invoice/view/',a.id),b.price,b.qty,concat(a.customer_name,' (',a.customer_code,')'),b.item_note";
        $sqx.= " From vw_ar_invoice_master as a Join t_ar_invoice_detail as b On a.invoice_no = b.invoice_no And a.cabang_id = b.cabang_id";
        $sqx.= " Where b.item_code = ?item_code and b.cabang_id = ?cabang_id and b.gudang_id = ?gudang_id and a.is_deleted = 0 and a.invoice_status <> 3";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        //if ($this->WarehouseCode == 'Display') {
            // get penjualan retail
            $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,keluar,relasi)";
            $sqx .= " Select date(a.waktu),concat('Penjualan - ',a.trx_no),'-',b.harga,b.qty_keluar,'Cash'";
            $sqx .= " From t_pos_master as a Join t_pos_detail as b On a.trx_no = b.trx_no";
            $sqx .= " Where b.item_code = ?item_code and a.cabang_id = ?cabang_id and a.trx_status <> 3";
            $this->connector->CommandText = $sqx;
            $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
            $this->connector->AddParameter("?cabang_id", $this->CabangId);
            $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
            $rs = $this->connector->ExecuteNonQuery();
        //}

        // get transfer keluar
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,keluar,relasi)";
        $sqx.= " Select a.npb_date,concat('Pengiriman - ',a.npb_no),concat('inventory.transfer/view/',a.id),0,b.qty,concat('Ke Cabang - ',a.to_cabang_code)";
        $sqx.= " From vw_ic_transfer_master a Join t_ic_transfer_detail b On a.cabang_id = b.cabang_id And a.npb_no = b.npb_no";
        $sqx.= " Where b.item_code = ?item_code and a.cabang_id = ?cabang_id and a.warehouse_id = ?gudang_id and a.is_deleted = 0 and a.npb_status <> 3";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        // get return ex pembelian
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,price,keluar,relasi)";
        $sqx.= " Select a.rb_date,concat('Return - ',a.rb_no),concat('ap.apreturn/view/',a.id),0,b.qty_retur,concat(a.supplier_name,' (',a.supplier_code,')')";
        $sqx.= " From vw_ap_return_master a Join t_ap_return_detail b On a.cabang_id = b.cabang_id And a.rb_no = b.rb_no";
        $sqx.= " Where b.item_code = ?item_code and a.cabang_id = ?cabang_id and b.gudang_id = ?gudang_id and a.is_deleted = 0 and a.rb_status <> 3";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        // get koreksi
        $sqx = "Insert Into `tmp_card` (trx_date,trx_type,trx_url,koreksi,relasi)";
        $sqx.= " Select a.corr_date,concat('Koreksi - ',a.corr_no),'inventory.correction',a.corr_qty,a.corr_reason";
        $sqx.= " From t_ic_stockcorrection as a";
        $sqx.= " Where a.item_id = ?item_id and a.cabang_id = ?cabang_id and a.warehouse_id = ?gudang_id and a.corr_status = 1";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->WarehouseId);
        $rs = $this->connector->ExecuteNonQuery();

        //filter data
        $sqx = "Insert Into tmp_card1 (seq_no,trx_date,trx_type,saldo)";
        $sqx.= " Select 0,'".date('Y-m-d',$startDate)."','Saldo lalu...',coalesce(sum((a.awal+a.masuk+a.koreksi)-a.keluar),0) From tmp_card a Where a.trx_date < '".date('Y-m-d',$startDate)."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteNonQuery();
        $sqx = "Insert Into tmp_card1 (seq_no,trx_date,trx_type,trx_url,relasi,price,awal,masuk,keluar,koreksi,saldo,notes)";
        $sqx.= " Select 1,trx_date,trx_type,trx_url,relasi,price,awal,masuk,keluar,koreksi,saldo,notes From tmp_card a Where a.trx_date >= '".date('Y-m-d',$startDate)."' And a.trx_date <= '".date('Y-m-d',$endDate)."'";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteNonQuery();
        // try get all tmp card data
        $sqx = "Select * From tmp_card1 Order By trx_date,seq_no";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function GetJSonItemStock($gudangId = 0,$filter,$sort = 'b.bnama',$order = 'ASC') {
        $sql = "SELECT a.id,a.cabang_id,a.item_id,a.item_code,a.qty_stock,b.bnama AS item_name,b.bsatbesar AS sat_besar,b.bsatkecil AS sat_kecil,b.bisisatkecil AS isisat_kecil";
        $sql.= " FROM t_ic_stockcenter AS a INNER JOIN m_barang AS b ON a.item_code = b.bkode";
        $sql.= " Where a.warehouse_id = $gudangId";
        if ($filter != null){
            $sql.= " And (a.item_code Like '%$filter%' Or a.bnama Like '%$filter%')";
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

    public function GetMutasiStock($cabId = 0,$whId = 0, $startDate = null, $endDate = null, $isDisplay = false){
        $sqx = null;
        // create previous mutasi temp table
        $sqx = 'CREATE TEMPORARY TABLE `tmp_prev` (
                `item_code`  varchar(50) NOT NULL ,
                `awal`  decimal(11,2) NOT NULL DEFAULT 0,
                `beli`  decimal(11,2) NOT NULL DEFAULT 0,
                `xin`  decimal(11,2) NOT NULL DEFAULT 0,
                `rjual`  decimal(11,2) NOT NULL DEFAULT 0,
                `asyin`  decimal(11,2) NOT NULL DEFAULT 0,
                `jual`  decimal(11,2) NOT NULL DEFAULT 0,
                `xout`  decimal(11,2) NOT NULL DEFAULT 0,
                `rbeli`  decimal(11,2) NOT NULL DEFAULT 0,
                `asyout`  decimal(11,2) NOT NULL DEFAULT 0,
                `koreksi`  decimal(11,2) NOT NULL DEFAULT 0)';
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteNonQuery();
        // create request mutasi temp table
        $sqx = 'CREATE TEMPORARY TABLE `tmp_mutasi` (
                `item_code`  varchar(50) NOT NULL ,
                `awal`  decimal(11,2) NOT NULL DEFAULT 0,
                `beli`  decimal(11,2) NOT NULL DEFAULT 0,
                `xin`  decimal(11,2) NOT NULL DEFAULT 0,
                `rjual`  decimal(11,2) NOT NULL DEFAULT 0,
                `asyin`  decimal(11,2) NOT NULL DEFAULT 0,
                `jual`  decimal(11,2) NOT NULL DEFAULT 0,
                `xout`  decimal(11,2) NOT NULL DEFAULT 0,
                `rbeli`  decimal(11,2) NOT NULL DEFAULT 0,
                `asyout`  decimal(11,2) NOT NULL DEFAULT 0,
                `koreksi`  decimal(11,2) NOT NULL DEFAULT 0)';
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteNonQuery();
        // get saldo awal
        $sqx = "Insert Into `tmp_prev` (item_code,awal) Select a.item_code,sum(a.op_qty) From t_ic_saldoawal as a";
        $sqx.= " Where a.op_date < ?startDate and a.warehouse_id = ?whId Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get pembelian lalu
        $sqx = "Insert Into `tmp_prev` (item_code,beli) Select a.item_code,sum(a.purchase_qty) From t_ap_purchase_detail as a";
        $sqx.= " Join t_ap_purchase_master as b On a.cabang_id = b.cabang_id And a.grn_no = b.grn_no";
        $sqx.= " Where b.grn_status <> 3 And b.grn_date < ?startDate and a.gudang_id = ?whId Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get transfer masuk lalu
        $sqx = "Insert Into `tmp_prev` (item_code,xin) Select a.item_code,sum(a.qty) From t_ic_transfer_detail as a";
        $sqx.= " Join t_ic_transfer_master as b On a.cabang_id = b.cabang_id And a.npb_no = b.npb_no";
        $sqx.= " Where b.npb_status <> 3 and b.npb_date < ?startDate and b.to_warehouse_id = ?whId Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get return ex penjualan lalu
        $sqx = "Insert Into `tmp_prev` (item_code,rjual) Select a.item_code,sum(a.qty_retur) From t_ar_return_detail as a";
        $sqx.= " Join t_ar_return_master as b On a.cabang_id = b.cabang_id And a.rj_no = b.rj_no";
        $sqx.= " Where b.rj_status <> 3 and b.rj_date < ?startDate and a.gudang_id = ?whId And b.is_deleted = 0 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        //if ($isDisplay){
            // get return ex penjualan retail lalu
            $sqx = "Insert Into `tmp_prev` (item_code,rjual) Select a.item_code,sum(a.qty_retur) From t_pos_return_detail as a";
            $sqx.= " Join t_pos_return_master as b On a.rtn_no = b.rtn_no";
            $sqx.= " Where b.rtn_status <> 3 and date(b.rtn_time) < ?startDate and b.cabang_id = ?cabId And a.is_posted = 1 Group By a.item_code";
            $this->connector->CommandText = $sqx;
            $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
            $this->connector->AddParameter("?cabId", $cabId);
            $rs = $this->connector->ExecuteNonQuery();
        //}

        // get stock in ex production/assemby lalu
        $sqx = "Insert Into `tmp_prev` (item_code,asyin) Select a.item_code,sum(a.qty) From t_ic_assembly_master as a";
        $sqx.= " Where (a.assembly_date < ?startDate) and a.warehouse_id = ?whId And a.is_deleted = 0 and a.assembly_status <>3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get stock out ex production/assemby lalu
        $sqx = "Insert Into `tmp_prev` (item_code,asyout) Select a.item_code,sum(a.qty) From t_ic_assembly_detail as a";
        $sqx.= " Join t_ic_assembly_master as b On a.cabang_id = b.cabang_id And a.assembly_no = b.assembly_no";
        $sqx.= " Where (b.assembly_date < ?startDate) and b.warehouse_id = ?whId And b.is_deleted = 0 and b.assembly_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get penjualan lalu
        $sqx = "Insert Into `tmp_prev` (item_code,jual) Select a.item_code,sum(a.qty) From t_ar_invoice_detail as a";
        $sqx.= " Join t_ar_invoice_master as b On a.cabang_id = b.cabang_id And a.invoice_no = b.invoice_no";
        $sqx.= " Where b.invoice_date < ?startDate and a.gudang_id = ?whId And b.is_deleted = 0 and b.invoice_status <>3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        //if ($isDisplay){
            // get penjualan retail lalu
            $sqx = "Insert Into `tmp_prev` (item_code,jual) Select a.item_code,sum(a.qty_keluar) From t_pos_detail as a";
            $sqx.= " Join t_pos_master as b On a.trx_no = b.trx_no";
            $sqx.= " Where date(b.waktu) < ?startDate and b.cabang_id = ?cabId And a.is_posted = 1 and b.trx_status <>3 Group By a.item_code";
            $this->connector->CommandText = $sqx;
            $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
            $this->connector->AddParameter("?cabId", $cabId);
            $rs = $this->connector->ExecuteNonQuery();
        //}

        // get transfer keluar lalu
        $sqx = "Insert Into `tmp_prev` (item_code,xout) Select a.item_code,sum(a.qty) From t_ic_transfer_detail as a";
        $sqx.= " Join t_ic_transfer_master as b On a.cabang_id = b.cabang_id And a.npb_no = b.npb_no";
        $sqx.= " Where b.npb_date < ?startDate and b.warehouse_id = ?whId and b.npb_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get return ex pembelian lalu
        $sqx = "Insert Into `tmp_prev` (item_code,rbeli) Select a.item_code,sum(a.qty_retur) From t_ap_return_detail as a";
        $sqx.= " Join t_ap_return_master as b On a.cabang_id = b.cabang_id And a.rb_no = b.rb_no";
        $sqx.= " Where b.rb_date < ?startDate and a.gudang_id = ?whId And b.is_deleted = 0 and b.rb_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get koreksi lalu
        $sqx = "Insert Into `tmp_prev` (item_code,koreksi)";
        $sqx.= " Select a.item_code,sum(a.corr_qty) From t_ic_stockcorrection as a";
        $sqx.= " Where a.corr_date < ?startDate and a.warehouse_id = ?whId and a.corr_status = 1 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

       // get saldo awal dari transaksi sebelumnya
        $sqx = "Insert Into `tmp_mutasi` (item_code,awal) Select a.item_code,sum((a.awal+a.beli+a.xin+a.rjual+a.asyin)-(a.jual+a.xout+a.rbeli+a.asyout)+a.koreksi) From tmp_prev as a Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteNonQuery();

        // get pembelian
        $sqx = "Insert Into `tmp_mutasi` (item_code,beli) Select a.item_code,sum(a.purchase_qty) From t_ap_purchase_detail as a";
        $sqx.= " Join t_ap_purchase_master as b On a.cabang_id = b.cabang_id And a.grn_no = b.grn_no";
        $sqx.= " Where b.grn_date BETWEEN ?startDate and ?endDate and a.gudang_id = ?whId and b.grn_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get transfer masuk
        $sqx = "Insert Into `tmp_mutasi` (item_code,xin) Select a.item_code,sum(a.qty) From t_ic_transfer_detail as a";
        $sqx.= " Join t_ic_transfer_master as b On a.cabang_id = b.cabang_id And a.npb_no = b.npb_no";
        $sqx.= " Where b.npb_date BETWEEN ?startDate and ?endDate and b.to_warehouse_id = ?whId and b.npb_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get return ex penjualan
        $sqx = "Insert Into `tmp_mutasi` (item_code,rjual) Select a.item_code,sum(a.qty_retur) From t_ar_return_detail as a";
        $sqx.= " Join t_ar_return_master as b On a.cabang_id = b.cabang_id And a.rj_no = b.rj_no";
        $sqx.= " Where b.rj_date BETWEEN ?startDate and ?endDate and a.gudang_id = ?whId And b.is_deleted = 0 And b.rj_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        //if ($isDisplay){
            // get return ex penjualan retail
            $sqx = "Insert Into `tmp_mutasi` (item_code,rjual) Select a.item_code,sum(a.qty_retur) From t_pos_return_detail as a";
            $sqx.= " Join t_pos_return_master as b On a.rtn_no = b.rtn_no";
            $sqx.= " Where b.rtn_status <> 3 and date(b.rtn_time) BETWEEN ?startDate and ?endDate  and b.cabang_id = ?cabId And a.is_posted = 1 Group By a.item_code";
            $this->connector->CommandText = $sqx;
            $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
            $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
            $this->connector->AddParameter("?cabId", $cabId);
            $rs = $this->connector->ExecuteNonQuery();
        //}

        // get stock in ex production/assemby
        $sqx = "Insert Into `tmp_mutasi` (item_code,asyin) Select a.item_code,sum(a.qty) From t_ic_assembly_master as a";
        $sqx.= " Where (a.assembly_date BETWEEN ?startDate and ?endDate) and a.warehouse_id = ?whId And a.is_deleted = 0 And a.assembly_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get stock out ex production/assemby
        $sqx = "Insert Into `tmp_mutasi` (item_code,asyout) Select a.item_code,sum(a.qty) From t_ic_assembly_detail as a";
        $sqx.= " Join t_ic_assembly_master as b On a.cabang_id = b.cabang_id And a.assembly_no = b.assembly_no";
        $sqx.= " Where (b.assembly_date BETWEEN ?startDate and ?endDate) and b.warehouse_id = ?whId And b.is_deleted = 0 And b.assembly_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get penjualan
        $sqx = "Insert Into `tmp_mutasi` (item_code,jual) Select a.item_code,sum(a.qty) From t_ar_invoice_detail as a";
        $sqx.= " Join t_ar_invoice_master as b On a.cabang_id = b.cabang_id And a.invoice_no = b.invoice_no";
        $sqx.= " Where b.invoice_date BETWEEN ?startDate and ?endDate and a.gudang_id = ?whId And b.is_deleted = 0 And b.invoice_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        //if ($isDisplay){
            // get penjualan
            $sqx = "Insert Into `tmp_mutasi` (item_code,jual) Select a.item_code,coalesce(sum(a.qty_keluar),0) From t_pos_detail as a";
            $sqx.= " Join t_pos_master as b On a.trx_no = b.trx_no";
            $sqx.= " Where date(b.waktu) BETWEEN ?startDate and ?endDate and b.cabang_id = ?cabId And a.is_posted = 1 and b.trx_status <>3 Group By a.item_code";
            $this->connector->CommandText = $sqx;
            $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
            $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
            $this->connector->AddParameter("?cabId", $cabId);
            $rs = $this->connector->ExecuteNonQuery();
        //}

        // get transfer keluar
        $sqx = "Insert Into `tmp_mutasi` (item_code,xout) Select a.item_code,sum(a.qty) From t_ic_transfer_detail as a";
        $sqx.= " Join t_ic_transfer_master as b On a.cabang_id = b.cabang_id And a.npb_no = b.npb_no";
        $sqx.= " Where b.npb_date BETWEEN ?startDate and ?endDate and b.warehouse_id = ?whId and b.npb_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get return ex pembelian
        $sqx = "Insert Into `tmp_mutasi` (item_code,rbeli) Select a.item_code,sum(a.qty_retur) From t_ap_return_detail as a";
        $sqx.= " Join t_ap_return_master as b On a.cabang_id = b.cabang_id And a.rb_no = b.rb_no";
        $sqx.= " Where b.rb_date BETWEEN ?startDate and ?endDate and a.gudang_id = ?whId And b.is_deleted = 0 And b.rb_status <> 3 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // get koreksi
        $sqx = "Insert Into `tmp_mutasi` (item_code,koreksi)";
        $sqx.= " Select a.item_code,sum(a.corr_qty) From t_ic_stockcorrection as a";
        $sqx.= " Where a.corr_date BETWEEN ?startDate and ?endDate and a.warehouse_id = ?whId and a.corr_status = 1 Group By a.item_code";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?startDate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?endDate", date('Y-m-d', $endDate));
        $this->connector->AddParameter("?whId", $whId);
        $rs = $this->connector->ExecuteNonQuery();

        // try get all tmp card data
        $sqx = "Select a.item_code, b.bnama as item_name, b.bsatbesar as satuan, sum(a.awal) as sAwal, sum(a.beli) as sBeli, sum(a.asyin) as sAsyin, sum(a.xin) as sXin, sum(a.rjual) as sRjual, sum(a.asyout) as sAsyout, sum(a.jual) as sJual, sum(a.xout) as sXout, sum(a.rbeli) as sRbeli, sum(a.koreksi) as sKoreksi ";
        $sqx.= " From tmp_mutasi as a Join m_barang as b On a.item_code = b.bkode Group By a.item_code Order By a.item_code,b.bnama,b.bsatbesar";
        $this->connector->CommandText = $sqx;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function getStockMinus($entityId,$cabId){
        $sql = "Select a.id,a.kode as cabang_code,a.item_code,a.bnama as item_name,a.qty_stock,a.bsatbesar as satuan From vw_ic_stockcenter a Where a.qty_stock < 0";
        if ($entityId > 0){
            $sql.= " And a.entity_id = $entityId";
        }
        if ($cabId > 0){
            $sql.= " And a.cabang_id = $cabId";
        }
        $sql.= " Order By a.kode,a.item_code, a.bnama";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4Reports($entityId = 0,$cabangId = 0, $jenisProduk = "-", $supplierCode = null){
        $sql = "Select a.item_code,a.bnama,a.bsatbesar,a.hrg_beli,a.hrg_jual,sum(a.qty_stock) as qty_stock,a.supplier_name, coalesce(sum(b.po_qty),0) as po_qty, coalesce(sum(c.so_qty),0) as so_qty";
        $sql.= " From vw_ic_stockcenter as a Left Join vw_ap_po_outstanding_qty as b On a.item_code = b.item_code And a.cabang_id = b.cabang_id";
        $sql.= " Left Join vw_ar_so_outstanding_qty as c On a.item_code = c.item_code And a.cabang_id = c.cabang_id";
        $sql.= " Where a.item_id > 0";
        if ($entityId > 0){
            $sql.= " And a.entity_id = ".$entityId;
        }
        if ($cabangId > 0){
            $sql.= " And a.cabang_id = ".$cabangId;
        }
        if ($jenisProduk != "-"){
            $sql.= " And a.bjenis = '".$jenisProduk."'";
        }
        if ($supplierCode != null){
            $sql.= " And a.supplier_code = '".$supplierCode."'";
        }
        $sql.= " Group By a.item_code,a.bnama,a.bsatbesar,a.hrg_beli,a.hrg_jual";
        if ($supplierCode <> null){
            $sql.= ',a.supplier_name';
        }
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Insert() {
        $sql = 'INSERT INTO t_ic_stockcenter (cabang_id,item_id,item_code,qty_stock,createby_id,create_time)';
        $sql.= ' VALUES(?cabang_id,?item_id,?item_code,?qty_stock,?createby_id,now())';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode,"char");
        $this->connector->AddParameter("?qty_stock", $this->QtyStock);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function Delete($id) {
        $this->connector->CommandText = 'Delete From t_ic_stockcenter Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function RecountStock($cabangId = 0){
        $sqx = null;
        $rec = 0;
        set_time_limit(600);
        $sqx = "SELECT fc_ic_stockautocorrection(?cabang_id) as recAffected;";
        $this->connector->CommandText = $sqx;
        $this->connector->AddParameter("?cabang_id", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return $rec;
        }
        $row = $rs->FetchAssoc();
        $rec = $row["recAffected"];
        return $rec;
    }
}

