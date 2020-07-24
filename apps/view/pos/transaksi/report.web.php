<!DOCTYPE HTML>
<html>
<?php $userName = AclManager::GetInstance()->GetCurrentUser()->RealName; ?>
<head>
	<title>REKASYS - Rekapitulasi Nota/Invoice/Piutang</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#StartDate").customDatePicker({ showOn: "focus" });
            $("#EndDate").customDatePicker({ showOn: "focus" });
        });
    </script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<h3>REKAPITULASI PENJUALAN</h3>
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th>Cabang/Outlet</th>
            <th>Jenis laporan</th>
            <th>Status Penjualan</th>
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select name="CabangId" class="text2" id="CabangId" required style="width:200px">
                    <option value="0">--Semua Cabang--</option>
                <?php
                    //printf('<option value="%d">%s - %s</option>', $userCabId, $userCabCode, $userCabName);
                    while ($rs = $mixcabangs->FetchAssoc()){
                        if ($rs["id"] == $CabangId) {
                            printf('<option value="%d" selected="selected">%s</option>', $rs["id"], $rs["nama_outlet"]);
                        }else{
                            printf('<option value="%d">%s</option>',$rs["id"],$rs["nama_outlet"]);
                        }
                    }
                ?>
                </select>
            </td>
            <td><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>>1 - Rekap Per Transaksi</option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>>2 - Rekap Detail Transaksi</option>
                    <option value="3" <?php print($JnsLaporan == 3 ? 'selected="selected"' : '');?>>3 - Rekap Item Terjual</option>
                </select>
            </td>
            <td>
                <select id="Status" name="Status" required>
                    <option value="-1" <?php print($Status == -1 ? 'selected="selected"' : '');?>> - Semua Status -</option>
                    <option value="1" <?php print($Status == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="3" <?php print($Status == 3 ? 'selected="selected"' : '');?>>0 - Void</option>
                </select>
            </td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="StartDate" name="StartDate" value="<?php printf(date('d-m-Y',$StartDate));?>"/></td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="EndDate" name="EndDate" value="<?php printf(date('d-m-Y',$EndDate));?>"/></td>
            <td>
                <select id="Output" name="Output" required>
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>>0 - Web Html</option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>>1 - Excel</option>
                </select>
            </td>
            <td>
                <button type="submit" formaction="<?php print($helper->site_url("pos.transaksi/report")); ?>"><b>Proses</b></button>
                <input type="button" class="button" onclick="printDiv('printArea')" value="Print"/>
            </td>
        </tr>
    </table>
</form>
<!-- start web report -->
<div id="printArea">
<?php  if ($Reports != null){
    if ($JnsLaporan < 3){
    ?>
        <h3>Rekapitulasi Transaksi Penjualan</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Cabang</th>
                <th>Tanggal</th>
                <th>No. Trx</th>
                <th>Kasir</th>
                <?php
                if ($JnsLaporan == 1) {
                    print("
                        <th>Customer</th>
                        <th>Tunai</th>
                        <th>Kartu Kredit</th>
                        <th>Kartu Debit</th>
                        <th>Jumlah</th>
                    ");
                }else{
                    print("<th nowrap='nowrap'>Kode Barang</th>");
                    print("<th nowrap='nowrap'>Nama Barang</th>");
                    print("<th>QTY</th>");
                    print("<th>Harga</th>");
                    print("<th>Disc(%)</th>");
                    print("<th>Discount</th>");
                    print("<th>Jumlah</th>");
                }
                ?>
            </tr>
            <?php
                $nmr = 0;
                $total = 0;
                $tunai = 0;
                $kk = 0;
                $kd = 0;
                $url = null;
                $ivn = null;
                $sma = false;
                $subtotal = 0;
                while ($row = $Reports->FetchAssoc()) {
                    if ($ivn <> $row["trx_no"]){
                        $nmr++;
                        $sma = false;
                    }else{
                        $sma = true;
                    }
                    if (!$sma) {
                        $url = $helper->site_url("pos.transaksi/view/" . $row["id"]);
                        print("<tr valign='Top'>");
                        printf("<td>%s</td>", $nmr);
                        printf("<td nowrap='nowrap'>%s</td>", $row["cabang_code"]);
                        printf("<td nowrap='nowrap'>%s</td>", date('d-m-Y', strtotime($row["tanggal"])));
                        printf("<td><a href= '%s' target='_blank'>%s</a></td>", $url, $row["trx_no"]);
                        printf("<td nowrap='nowrap'>%s</td>", $row["kasir"]);
                        if ($JnsLaporan == 1){
                            printf("<td nowrap='nowrap'>%s</td>", $row["cust_name"]);
                            printf("<td align='right'>%s</td>", number_format($row["bayar_tunai"], 0));
                            printf("<td align='right'>%s</td>", number_format($row["bayar_kk"], 0));
                            printf("<td align='right'>%s</td>", number_format($row["bayar_kd"], 0));
                            printf("<td align='right'>%s</td>", number_format($row["total_transaksi"], 0));
                            print("</tr>");
                        }
                        $tunai+= $row["bayar_tunai"];
                        $kk+= $row["bayar_kk"];
                        $kd+= $row["bayar_kd"];
                        $total+= $row["total_transaksi"];
                    }
                    if ($JnsLaporan == 2){
                        if ($sma) {
                            print("</tr>");
                            print("<td colspan='5'>&nbsp;</td>");
                        }
                        printf("<td nowrap='nowrap'>%s</td>", $row['item_code']);
                        printf("<td nowrap='nowrap'>%s</td>", $row['item_descs']);
                        printf("<td align='right'>%s</td>", number_format($row['qty'], 0));
                        printf("<td align='right' >%s</td>", number_format($row['price'], 0));
                        printf("<td align='right'>%s</td>", $row['diskon_persen']);
                        printf("<td align='right'>%s</td>", number_format($row['diskon_nilai'], 0));
                        printf("<td align='right'>%s</td>", number_format($row['sub_total'], 0));
                        print("</tr>");
                        $subtotal+= $row['sub_total'];
                    }
                    $ivn = $row["trx_no"];
                }
            print("<tr>");
            if ($JnsLaporan == 1) {
                print("<td colspan='6' align='right'>Total Transaksi</td>");
                printf("<td align='right'>%s</td>", number_format($tunai, 0));
                printf("<td align='right'>%s</td>", number_format($kk, 0));
                printf("<td align='right'>%s</td>", number_format($kd, 0));
                printf("<td align='right'>%s</td>", number_format($total, 0));
            }else{
                print("<td colspan='11' align='right'>Total Transaksi</td>");
                printf("<td align='right'>%s</td>", number_format($subtotal, 0));
            }
            print("</tr>");
            ?>
        </table>
<?php }elseif ($JnsLaporan == 3){ ?>
        <h3>Rekapitulasi Item Terjual</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>QTY</th>
                <th>Satuan</th>
                <th>Nilai Penjualan</th>
            </tr>
            <?php
            $nmr = 0;
            $sqty = 0;
            $snilai = 0;
            while ($row = $Reports->FetchAssoc()) {
                $nmr++;
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>",$row['item_code']);
                printf("<td>%s</td>",$row['item_descs']);
                printf("<td align='right'>%s</td>",number_format($row['price'],0));
                printf("<td align='right'>%s</td>",number_format($row['sum_qty'],0));
                printf("<td>%s</td>",$row['satuan']);
                printf("<td align='right'>%s</td>",number_format($row['sum_total']+$row['sum_tax'],0));
                print("</tr>");
                $sqty+= $row['sum_qty'];
                $snilai+= $row['sum_total']+$row['sum_tax'];
            }
            print("<tr>");
            print("<td colspan='4' align='right'>Total.....</td>");
            printf("<td align='right'>%s</td>",number_format($sqty,0));
            printf("<td>&nbsp;</td>");
            printf("<td align='right'>%s</td>",number_format($snilai,0));
            print("</tr>");
            ?>
        </table>
<!-- end web report -->
<?php }elseif ($JnsLaporan == 4){ ?>
        <h3>REKAPITULASI PER OUTLET - <?php print($userCabName);?></h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Outlet</th>
                <th>Nama Outlet</th>
                <th>Alamat</th>
                <th>Salesman</th>
                <th>QTY</th>
                <th>Jumlah</th>
            </tr>
            <?php
            $nmr = 1;
            $tDpp = 0;
            $tPpn = 0;
            $tOtal = 0;
            $subTotal = 0;
            $tTerbayar = 0;
            $tSisa = 0;
            $tQty = 0;
            $url = null;
            $ivn = null;
            $sma = false;
            while ($row = $Reports->FetchAssoc()) {
                $url = $helper->site_url("ar.invoice/view/" . $row["id"]);
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>", date('d-m-Y', strtotime($row["invoice_date"])));
                printf("<td><a href= '%s' target='_blank'>%s</a></td>", $url, $row["trx_no"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["customer_code"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["customer_name"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["customer_address"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["sales_name"]);
                printf("<td align='right'>%s</td>", number_format($row["sum_qty"], 0));
                printf("<td align='right'>%s</td>", number_format($row["total_amount"], 0));
                print("</tr>");
                $tDpp+= $row["base_amount"];
                $tPpn+= $row["tax_amount"];
                $tOtal+= $row["total_amount"];
                $tTerbayar+= $row["paid_amount"];
                $tSisa+= $row["balance_amount"];
                $tQty+= $row["sum_qty"];
                $nmr++;
            }
            print("<tr>");
            print("<td colspan='7' align='right'>Total </td>");
            printf("<td align='right'>%s</td>",number_format($tQty,0));
            printf("<td align='right'>%s</td>",number_format($tOtal,0));
            print("</tr>");
            ?>
        </table>
    <?php }elseif ($JnsLaporan == 5){ ?>
        <h3>REKAPITULASI PER PRODUK - <?php print($userCabName);?></h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Satuan</th>
                <th>QTY</th>
                <th>Terkirim</th>
                <th>Tidak Terkirim</th>
                <th>Selisih</th>
            </tr>
            <?php
            $nmr = 0;
            $sqty = 0;
            $snilai = 0;
            while ($row = $Reports->FetchAssoc()) {
                $nmr++;
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>",$row['item_code']);
                printf("<td>%s</td>",$row['item_descs']);
                printf("<td>%s</td>",$row['satuan']);
                printf("<td align='right'>%s</td>",number_format($row['sum_qty'],0));
                print('<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>');
                print("</tr>");
                $sqty+= $row['sum_qty'];
                $snilai+= $row['sum_total'];
            }
            print("<tr>");
            print("<td colspan='4' align='right'>Total.....</td>");
            printf("<td align='right'>%s</td>",number_format($sqty,0));
            print('<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>');
            print("</tr>");
            ?>
        </table>
<?php }} ?>
</div>
<br>
<?php if($Reports != null){ ?>
    <?php
    print('<i>* Printed by: '.$userName.'  - Time: '.date('d-m-Y h:i:s').' *</i>');
    ?>
<?php } ?>
<script type="text/javascript">
    function printDiv(divName) {
        //if (confirm('Print Invoice ini?')) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
        //}
    }
</script>
</body>
</html>
