<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
<title>REKASYS - View Transaksi Penjualan</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>

<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>

<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>

<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>

<style scoped>
    .f1{
        width:200px;
    }
</style>
<style type="text/css">
    #fd{
        margin:0;
        padding:5px 10px;
    }
    .ftitle{
        font-size:14px;
        font-weight:bold;
        padding:5px 0;
        margin-bottom:10px;
        border-bottom:1px solid #ccc;
    }
    .fitem{
        margin-bottom:5px;
    }
    .fitem label{
        display:inline-block;
        width:100px;
    }
    .numberbox .textbox-text{
        text-align: right;
        color: blue;
    }
</style>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php }
?>
<br />
<div id="p" class="easyui-panel" title="View Transaksi Penjualan" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td class="right">Trx No :</td>
            <td class="bold"><?php print($master["trx_no"]);?></td>
            <td class="right">Tanggal :</td>
            <td class="bold"><?php print($master["trx_time"]);?></td>
        </tr>
        <tr>
            <td class="right">Customer :</td>
            <td class="bold"><?php print($master["cust_name"]);?></td>
            <td class="right">Kasir :</td>
            <td class="bold"><?php print($master["kasir"]);?></td>
        </tr>
        <tr>
            <td class="right">Terminal :</td>
            <td class="bold"><?php print($master["no_terminal"]);?></td>
            <td class="right">Session :</td>
            <td class="bold"><?php print($master["session_no"]);?></td>
        </tr>
        <tr>
            <td colspan="4">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Dsc %</th>
                        <th>Dsc Nilai</th>
                        <th>Jumlah</th>
                    </tr>
                    <?php
                    $nmr = 1;
                    $total = 0;
                    while ($row = $details->FetchAssoc()) {
                        print("<tr>");
                        printf("<td>%d</td>",$nmr);
                        printf("<td>%s</td>",$row["item_code"]);
                        printf("<td>%s</td>",$row["item_name"]);
                        printf("<td class='right'>%s</td>",number_format($row["qty_keluar"],0));
                        printf("<td>%s</td>",$row["satuan"]);
                        printf("<td class='right'>%s</td>",$row["is_bonus"] == 1 ? 'Bonus' : number_format($row["harga"],0));
                        printf("<td class='right'>%s</td>",$row["diskon_persen"] > 0 ? "-".number_format($row["diskon_persen"],0) : '');
                        printf("<td class='right'>%s</td>",$row["diskon_nilai"] > 0 ? "-".number_format($row["diskon_nilai"],0) : '');
                        printf("<td class='right'>%s</td>",$row["sub_total"] > 0 ? number_format($row["sub_total"],0) : '');
                        print("</tr>");
                        $total+= $row["sub_total"];
                        $nmr++;
                    }
                    if ($master["diskon_nilai"] > 0){
                        print("<tr>");
                        print("<td class='right' colspan='8'>Sub Total :</td>");
                        printf("<td class='right'>%s</td>",number_format($total,0));
                        print("</tr>");
                        print("<tr>");
                        printf("<td class='right' colspan='8'>Diskon %s :</td>",number_format($master["diskon_persen"],0).'%');
                        printf("<td class='right'>-%s</td>",number_format($master["diskon_nilai"],0));
                        print("</tr>");
                        print("<tr>");
                        $total = $total - $master["diskon_nilai"];
                        print("<td class='right' colspan='8'>Total Belanja :</td>");
                        printf("<td class='right'>%s</td>",number_format($total,0));
                        print("</tr>");
                    }else{
                        print("<tr>");
                        print("<td class='right' colspan='8'>Total Belanja :</td>");
                        printf("<td class='right'>%s</td>",number_format($total,0));
                        print("</tr>");
                    }
                    print("<tr>");
                    if ($master["cara_bayar"] == 1) {
                        print("<td class='right' colspan='8'>Pembayaran Cash :</td>");
                        printf("<td class='right'>%s</td>",number_format($master["bayar_tunai"],0));
                    }elseif ($master["cara_bayar"] == 3){
                        printf("<td class='right' colspan='8'>CC %s No. %s An. %s %s%s :</td>",strtoupper($master["bank"]),$master["no_kartu"],$master["nama_pemilik"],' + Admin '.number_format($master["admin_persen"],1),'% = Rp. '.number_format($master["admin_nilai"],0));
                        printf("<td class='right'>%s</td>",number_format($master["bayar_kk"],0));
                    }elseif ($master["cara_bayar"] == 4){
                        printf("<td class='right' colspan='8'>DC %s No. %s An. %s %s%s :</td>",strtoupper($master["bank"]),$master["no_kartu"],$master["nama_pemilik"],' + Admin '.number_format($master["admin_persen"],1),'% = Rp. '.number_format($master["admin_nilai"],0));
                        printf("<td class='right'>%s</td>",number_format($master["bayar_kd"],0));
                    }else{
                        print("<td class='right' colspan='8'>N/A :</td>");
                    }
                    print("</tr>");
                    if ($master["jumlah_bayar"] - $total > 0){
                        print("<tr>");
                        print("<td class='right' colspan='8'>Kembalian :</td>");
                        printf("<td class='right'>%s</td>",number_format($master["jumlah_bayar"] - $total,0));
                        print("</tr>");
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4"><a href="<?php print($helper->site_url("pos.transaksi")); ?>">Daftar Transaksi Penjualan</a></td>
        </tr>
    </table>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2018 - 2019 PT. Reka Sistem Teknologi
</div>
</body>
</html>
