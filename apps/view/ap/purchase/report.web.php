<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php /** @var $suppliers Contacts[] */ ?>

<head>
	<title>REKASYS - Rekapitulasi Pembelian Barang dan Hutang</title>
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
<br />
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th colspan="5"><b>Rekapitulasi Pembelian Barang dan Hutang</b></th>
            <th>Jenis Laporan:</th>
            <th colspan="2"><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>>1 - Rekap Per Invoice</option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>>2 - Rekap Invoice Detail</option>
                    <option value="3" <?php print($JnsLaporan == 3 ? 'selected="selected"' : '');?>>3 - Rekap Item Pembelian</option>
                </select>
            </th>
        </tr>
        <tr class="center">
            <th>Cabang</th>
            <th>Supplier</th>
            <th>GRN Status</th>
            <th>Status Lunas</th>
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select name="CabangId" class="text2" id="CabangId" style="width: 150px" required>
                    <option value="0">-- Semua Cabang --</option>
                    <?php
                    while ($rs = $mixcabangs->FetchAssoc()) {
                        if ($rs["id"] == $CabangId) {
                            printf('<option value="%d" selected="selected">%s</option>', $rs["id"], $rs["nama_outlet"]);
                        } else {
                            printf('<option value="%d">%s</option>', $rs["id"], $rs["nama_outlet"]);
                        }
                    }
                    ?>
                </select>
            </td>            
            <td>
                <select id="ContactsId" name="ContactsId" style="width: 150px" required>
                    <option value="0">- Semua Supplier -</option>
                    <?php
                    foreach ($suppliers as $supplier) {
                        if ($ContactsId == $supplier->Id){
                            printf('<option value="%d" selected="selected"> %s - %s </option>',$supplier->Id,$supplier->ContactCode,$supplier->ContactName);
                        }else{
                            printf('<option value="%d"> %s - %s</option>',$supplier->Id,$supplier->ContactCode,$supplier->ContactName);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="Status" name="Status" required>
                    <option value="-1" <?php print($Status == -1 ? 'selected="selected"' : '');?>> - Semua Status -</option>
                    <option value="0" <?php print($Status == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($Status == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($Status == 2 ? 'selected="selected"' : '');?>>2 - Approved</option>
                    <option value="3" <?php print($Status == 3 ? 'selected="selected"' : '');?>>3 - Batal</option>
                </select>
            </td>
            <td>
                <select id="PaymentStatus" name="PaymentStatus" required>
                    <option value="-1" <?php print($PaymentStatus == -1 ? 'selected="selected"' : '');?>> - Semua Status -</option>
                    <option value="0" <?php print($PaymentStatus == 0 ? 'selected="selected"' : '');?>>0 - Belum Lunas</option>
                    <option value="1" <?php print($PaymentStatus == 1 ? 'selected="selected"' : '');?>>1 - Lunas</option>
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
            <td><button type="submit" formaction="<?php print($helper->site_url("ap.purchase/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    if ($JnsLaporan < 3){
    ?>
    <h3>Rekapitulasi Pembelian/Penerimaan Barang</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>Tanggal</th>
            <th>No. GRN</th>
            <th>Nama Supplier</th>
            <?php
            if ($JnsLaporan == 1) {
                print('
                    <th>Keterangan</th>
                    <th>JTP</th>
                    <th>Jumlah</th>
                    <th>Diskon</th>
                    <th>Retur</th>
                    <th>Terbayar</th>
                    <th>Outstanding</th>
                    ');
            }else{
                print("<th nowrap='nowrap'>Kode Barang</th>");
                print("<th nowrap='nowrap'>Nama Barang</th>");
                print("<th>QTY</th>");
                print("<th>Harga</th>");
                print("<th>Disc(%)</th>");
                print("<th>Discount</th>");
                print("<th>DPP</th>");
                print("<th>PPN</th>");
                print("<th>Jumlah</th>");
            }
            ?>
        </tr>
        <?php
        $nmr = 0;
        $tTotal = 0;
        $tDiskon = 0;
        $tReturn = 0;
        $tTerbayar = 0;
        $tSisa = 0;
        $url = null;
        $sma = false;
        $ivn = null;
        $tdpp = 0;
        $tppn = 0;
        while ($row = $Reports->FetchAssoc()) {
            if ($ivn <> $row["grn_no"]){
                $nmr++;
                $sma = false;
            }else{
                $sma = true;
            }
            if (!$sma) {
                $url = $helper->site_url("ap.purchase/view/" . $row["id"]);
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td nowrap='nowrap'>%s</td>", $row["cabang_code"]);
                printf("<td nowrap='nowrap'>%s</td>", date('d-m-Y', strtotime($row["grn_date"])));
                printf("<td nowrap='nowrap'><a href= '%s' target='_blank'>%s</a></td>", $url, $row["grn_no"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["supplier_name"]);
                if ($JnsLaporan == 1){
                    printf("<td nowrap='nowrap'>%s</td>", $row["grn_descs"]);
                    printf("<td nowrap='nowrap'>%s</td>", date('d-m-Y', strtotime($row["due_date"])));
                    printf("<td align='right'>%s</td>", number_format($row["base_amount"], 0));
                    printf("<td align='right'>%s</td>", number_format($row["disc1_amount"], 0));
                    printf("<td align='right'>%s</td>", number_format($row["return_amount"], 0));
                    printf("<td align='right'>%s</td>", number_format($row["paid_amount"], 0));
                    printf("<td align='right'>%s</td>", number_format($row["balance_amount"], 0));
                    print("</tr>");
                }
                $tTotal += $row["base_amount"];
                $tDiskon += $row["disc1_amount"];
                $tReturn += $row["return_amount"];
                $tTerbayar += $row["paid_amount"];
                $tSisa += $row["balance_amount"];
            }
            if ($JnsLaporan == 2){
                if ($sma) {
                    print("</tr>");
                    print("<td colspan='5'>&nbsp;</td>");
                }
                printf("<td nowrap='nowrap'>%s</td>",$row['item_code']);
                printf("<td nowrap='nowrap'>%s</td>",$row['item_descs']);
                printf("<td align='right'>%s</td>",number_format($row['qty'],0));
                printf("<td align='right' >%s</td>",$row['is_free'] == 0 ? number_format($row['price'],0) : '*Bonus');
                printf("<td align='right'>%s</td>",$row['disc_formula']);
                printf("<td align='right'>%s</td>",number_format($row['disc_amount'],0));
                printf("<td align='right'>%s</td>",number_format($row['sub_total']-$row['disc_amount'],0));
                printf("<td align='right'>%s</td>",number_format($row['dtax_amount'],0));
                printf("<td align='right'>%s</td>",number_format($row['sub_total']+$row['dtax_amount'],0));
                print("</tr>");
                $tdpp+= $row['sub_total']-$row['disc_amount'];
                $tppn+= $row['dtax_amount'];
            }
            $ivn = $row["grn_no"];
        }
        print("<tr>");
        print("<td colspan='7' align='right'>Total Invoice</td>");
        if ($JnsLaporan == 1) {
            printf("<td align='right'>%s</td>", number_format($tTotal, 0));
            printf("<td align='right'>%s</td>", number_format($tDiskon, 0));
            printf("<td align='right'>%s</td>", number_format($tReturn, 0));
            printf("<td align='right'>%s</td>", number_format($tTerbayar, 0));
            printf("<td align='right'>%s</td>", number_format($tSisa, 0));
        }else{
            print("<td colspan='4'>&nbsp;</td>");
            printf("<td align='right'>%s</td>",number_format($tdpp,0));
            printf("<td align='right'>%s</td>",number_format($tppn,0));
            printf("<td align='right'>%s</td>",number_format($tdpp+$tppn,0));
        }
        print("</tr>");
        ?>
    </table>
<?php }else{ ?>
        <h3>Rekapitulasi Item Pembelian/Penerimaan Barang</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Qty</th>
                <th>Nilai Pembelian</th>
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
                printf("<td align='right'>%s</td>",number_format($row['sum_total']+$row['sum_tax'],0));
                print("</tr>");
                $sqty+= $row['sum_qty'];
                $snilai+= $row['sum_total']+$row['sum_tax'];
            }
            print("<tr>");
            print("<td colspan='4' align='right'>Total.....</td>");
            printf("<td align='right'>%s</td>",number_format($sqty,0));
            printf("<td align='right'>%s</td>",number_format($snilai,0));
            print("</tr>");
            ?>
        </table>
<?php }} ?>
</body>
</html>
