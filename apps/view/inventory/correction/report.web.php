<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>REKASYS - Rekapitulasi Stock Opname</title>
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
<br/>
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th colspan="2"><b>Rekapitulasi Koreksi Stock</b></th>
            <th>Jenis Laporan:</th>
            <th colspan="2"><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>>1 - Laporan Koreksi Detail (Tanpa Harga)</option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>>2 - Laporan Koreksi Detail (HPP)</option>
                    <option value="3" <?php print($JnsLaporan == 3 ? 'selected="selected"' : '');?>>3 - Laporan Koreksi Detail (Harga Jual)</option>
                    <option value="4" <?php print($JnsLaporan == 4 ? 'selected="selected"' : '');?>>4 - Rekap Koreksi Stock (Tanpa Harga)</option>
                    <option value="5" <?php print($JnsLaporan == 5 ? 'selected="selected"' : '');?>>5 - Rekap Koreksi Stock (HPP)</option>
                    <option value="6" <?php print($JnsLaporan == 6 ? 'selected="selected"' : '');?>>6 - Rekap Koreksi Stock (Harga Jual)</option>
                </select>
            </th>
        </tr>
        <tr class="center">
            <th>Cabang/Gudang</th>
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select name="GudangId" class="text2" id="GudangId" required>
                   <option value="0">- Semua Gudang -</option>
                    <?php
                    /** @var $cab Warehouse[]*/
                    foreach ($gudangs as $cab) {
                        if ($cab->Id == $GudangId) {
                            printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->CabCode, $cab->WhCode);
                        } else {
                            printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->CabCode, $cab->WhCode);
                        }
                    }
                    ?>
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
            <td><button type="submit" formaction="<?php print($helper->site_url("inventory.correction/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    if ($JnsLaporan < 4){
    ?>
    <h3>Laporan Koreksi Stock</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Cabang/Gudang</th>
            <th>Tanggal</th>
            <th>No.Koreksi</th>
            <th>Keterangan</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>System</th>
            <th>Riil</th>
            <th>Koreksi</th>
            <?php
            if ($JnsLaporan == 2){
                print('<th>HPP</th>');
                print('<th>Jumlah</th>');
            }elseif ($JnsLaporan == 3){
                print('<th>Harga Jual</th>');
                print('<th>Jumlah</th>');
            }
            ?>
        </tr>
        <?php
        $nmr = 0;
        $tQty = 0;
        $tCor = 0;
        $crn = null;
        while ($row = $Reports->FetchAssoc()) {
            if ($crn <> $row["corr_no"]) {
                $nmr++;
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>", $row["cabang_code"]." - ".$row["wh_code"]);
                printf("<td nowrap='nowrap'>%s</td>", date('d-m-Y', strtotime($row["corr_date"])));
                printf("<td>%s</td>", $row["corr_no"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["corr_reason"]);
            }else{
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
            }
            printf("<td nowrap='nowrap'>%s</td>", $row["item_code"]);
            printf("<td nowrap='nowrap'>%s</td>", $row["bnama"]);
            printf("<td nowrap='nowrap'>%s</td>", $row["bsatkecil"]);
            printf("<td align='right'>%s</td>", number_format($row["sys_qty"], 0));
            printf("<td align='right'>%s</td>", number_format($row["whs_qty"], 0));
            printf("<td align='right' class='bold'>%s%s</td>",$row["corr_qty"] > 0 ? '+' : '',number_format($row["corr_qty"], 0));
            if ($JnsLaporan == 2){
                printf("<td align='right'>%s</td>", number_format($row["hpp"], 0));
                printf("<td align='right'>%s</td>", number_format(round($row["hpp"] * $row["corr_qty"],0), 0));
                $tCor+= round($row["hpp"] * $row["corr_qty"],0);
            }elseif ($JnsLaporan == 3){
                printf("<td align='right'>%s</td>", number_format($row["harga_jual"], 0));
                printf("<td align='right'>%s</td>", number_format(round($row["harga_jual"] * $row["corr_qty"],0), 0));
                $tCor+= round($row["harga_jual"] * $row["corr_qty"],0);
            }
            print("</tr>");
            $tQty+= $row["corr_qty"];
            $crn = $row["corr_no"];
        }
        print("<tr>");
        print("<td colspan='10' align='right' class='bold'>Total Koreksi</td>");
        printf("<td align='right'>%s%s</td>",$tQty > 0 ? '+' : '',number_format($tQty,0));
        if ($JnsLaporan == 2){
            printf("<td>&nbsp;</td>");
            printf("<td align='right'><b>%s</b></td>", number_format($tCor, 0));
        }elseif ($JnsLaporan == 3){
            printf("<td>&nbsp;</td>");
            printf("<td align='right'><b>%s</b></td>", number_format($tCor, 0));
        }
        print("</tr>");
        ?>
    </table>
<?php }else{ ?>
        <h3>Rekapitulasi Koreksi Stock</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Cabang/Gudang</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Qty</th>
                <?php
                if ($JnsLaporan == 5){
                    print('<th>HPP</th>');
                    print('<th>Jumlah</th>');
                }elseif ($JnsLaporan == 6){
                    print('<th>Harga Jual</th>');
                    print('<th>Jumlah</th>');
                }
                ?>
            </tr>
            <?php
            $nmr = 0;
            $sqty = 0;
            $tCor = 0;
            $cbs = null;
            while ($row = $Reports->FetchAssoc()) {
                if ($cbs <> $row['cabang_code'].$row['wh_code']) {
                    $nmr++;
                    print("<tr valign='Top'>");
                    printf("<td>%s</td>", $nmr);
                    printf("<td>%s</td>", $row['cabang_code'].' - '.$row['wh_code']);
                }else{
                    print("<td>&nbsp;</td>");
                    print("<td>&nbsp;</td>");
                }
                printf("<td>%s</td>",$row['item_code']);
                printf("<td>%s</td>",$row['bnama']);
                printf("<td>%s</td>",$row['bsatkecil']);
                printf("<td align='right'>%s</td>",number_format($row['qty'],0));
                if ($JnsLaporan == 5){
                    printf("<td align='right'>%s</td>", number_format($row["hpp"], 0));
                    printf("<td align='right'>%s</td>", number_format(round($row["hpp"] * $row["qty"],0), 0));
                    $tCor+= round($row["hpp"] * $row["qty"],0);
                }elseif ($JnsLaporan == 6){
                    printf("<td align='right'>%s</td>", number_format($row["harga_jual"], 0));
                    printf("<td align='right'>%s</td>", number_format(round($row["harga_jual"] * $row["qty"],0), 0));
                    $tCor+= round($row["harga_jual"] * $row["qty"],0);
                }
                print("</tr>");
                $sqty+= $row['qty'];
                $cbs = $row['cabang_code'].$row['wh_code'];
            }
            print("<tr>");
            print("<td colspan='5' align='right'>Total.....</td>");
            printf("<td align='right'>%s</td>",number_format($sqty,0));
            if ($JnsLaporan == 5){
                printf("<td>&nbsp;</td>");
                printf("<td align='right'><b>%s</b></td>", number_format($tCor, 0));
            }elseif ($JnsLaporan == 6){
                printf("<td>&nbsp;</td>");
                printf("<td align='right'><b>%s</b></td>", number_format($tCor, 0));
            }
            print("</tr>");
            ?>
        </table>
<?php }} ?>
</body>
</html>
