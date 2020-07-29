<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php /** @var $kasirs UserAdmin[] */ ?>
<head>
	<title>REKASYS - Rekapitulasi Penjualan Kasir</title>
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
            <th colspan="4"><b>Rekapitulasi Sesi Penjualan Kasir</b></th>
            <th>Jenis Laporan:</th>
            <th colspan="2"><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>>1 - Rekapitulasi</option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>>2 - Detail Struk</option>
                    <option value="3" <?php print($JnsLaporan == 3 ? 'selected="selected"' : '');?>>3 - Item Terjual</option>
                </select>
            </th>
        </tr>
        <tr class="center">
            <th>Cabang</th>
            <th>Kasir</th>
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select name="CabangId" class="text2" id="CabangId" required>
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
                <select id="KasirId" name="KasirId" style="width: 150px" required>
                    <option value="0">- Semua Kasir -</option>
                    <?php
                    foreach ($kasirs as $kasir) {
                        if ($KasirId == $kasir->UserUid){
                            printf('<option value="%d" selected="selected">%s (%s)</option>',$kasir->UserUid,$kasir->UserId,$kasir->UserName);
                        }else{
                            printf('<option value="%d">%s (%s)</option>',$kasir->UserUid,$kasir->UserId,$kasir->UserName);
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
            <td><button type="submit" formaction="<?php print($helper->site_url("pos.sesikasir/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    ?>
    <h3>Rekapitulasi Sessi Penjualan Kasir</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>No. Sesi</th>
            <th>Buka</th>
            <th>Tutup</th>
            <th>Nama Kasir</th>
            <th>Kas Awal</th>
            <th>Tunai</th>
            <th>K.Kredit</th>
            <th>K.Debit</th>
            <th>Sub Total</th>
            <th>Retur</th>
            <th>Jumlah Kas</th>
        </tr>
        <?php
            $nmr = 1;
            $url = null;
            $sts = null;
            $ivn = null;
            $kds = null;
            $topen = 0;
            $tmjual = 0;
            $tkkjual = 0;
            $tkdjual = 0;
            $tjual = 0;
            $tretur = 0;
            $tkas = 0;
            while ($row = $Reports->FetchAssoc()) {
                $url = $helper->site_url("pos.sesikasir/view/" . $row["id"]);
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td nowrap='nowrap'>%s</td>", $row["cabang_code"]);
                printf("<td nowrap='nowrap'><a href= '%s' target='_blank'>%s</a></td>", $url, $row["session_no"]);
                printf("<td nowrap='nowrap'>%s</td>", date('d-m-Y H:i:s', strtotime($row["open_time"])));
                printf("<td nowrap='nowrap'>%s</td>", date('d-m-Y H:i:s', strtotime($row["close_time"])));
                printf("<td nowrap='nowrap'>%s</td>", $row["kasir"]);
                printf("<td align='right'>%s</td>", number_format($row["tunai_open"], 0));
                printf("<td align='right'>%s</td>", number_format($row["tunai_masuk_jual"], 0));
                printf("<td align='right'>%s</td>", number_format($row["kk_jual"], 0));
                printf("<td align='right'>%s</td>", number_format($row["kd_jual"], 0));
                printf("<td align='right'>%s</td>", number_format($row["tunai_masuk_jual"]+$row["kk_jual"]+$row["kd_jual"], 0));
                printf("<td align='right'>%s</td>", number_format($row["tunai_keluar_retur"], 0));
                printf("<td align='right'>%s</td>", number_format($row["tunai_open"]+$row["tunai_masuk_jual"]-$row["tunai_keluar_retur"], 0));
                $topen += $row["tunai_open"];
                $tmjual += $row["tunai_masuk_jual"];
                $tkkjual += $row["kk_jual"];
                $tkdjual += $row["kd_jual"];
                $tjual += $row["tunai_masuk_jual"]+$row["kk_jual"]+$row["kd_jual"];
                $tretur += $row["tunai_keluar_retur"];
                $tkas += $row["tunai_open"]+$row["tunai_masuk_jual"]-$row["tunai_keluar_retur"];
                $nmr++;
            }
        print("<tr class='bold'>");
        print("<td colspan='6' align='right'>Total Penjualan</td>");
        printf("<td align='right'>%s</td>",number_format($topen,0));
        printf("<td align='right'>%s</td>",number_format($tmjual,0));
        printf("<td align='right'>%s</td>",number_format($tkkjual,0));
        printf("<td align='right'>%s</td>",number_format($tkdjual,0));
        printf("<td align='right'>%s</td>",number_format($tjual,0));
        printf("<td align='right'>%s</td>",number_format($tretur,0));
        printf("<td align='right'>%s</td>",number_format($tkas,0));
        print("</tr>");
        ?>
    </table>
<?php }?>
</body>
</html>
