<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
<title>REKASYS - View Retur Penjualan</title>
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
<div id="p" class="easyui-panel" title="View Retur Penjualan" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td class="right">Rtn No :</td>
            <td class="bold"><?php print($master["rtn_no"]);?></td>
            <td class="right">Tanggal :</td>
            <td class="bold"><?php print($master["rtn_date"]);?></td>
        </tr>
        <tr>
            <td class="right">Customer :</td>
            <td class="bold"><?php print($master["cust_name"]);?></td>
            <td class="right">Kasir :</td>
            <td class="bold"><?php print($master["kasir"]);?></td>
        </tr>
        <tr>
            <td class="right">Terminal :</td>
            <td class="bold">-</td>
            <td class="right">Session :</td>
            <td class="bold"><?php print($master["session_no"]);?></td>
        </tr>
        <tr>
            <td colspan="4">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th>No.</th>
                        <th>Ex. Struk</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                    <?php
                    $nmr = 1;
                    $total = 0;
                    while ($row = $details->FetchAssoc()) {
                        print("<tr>");
                        printf("<td>%d</td>",$nmr);
                        printf("<td>%s</td>",$row["ex_trx_no"]);
                        printf("<td>%s</td>",$row["item_code"]);
                        printf("<td>%s</td>",$row["item_name"]);
                        printf("<td class='right'>%s</td>",number_format($row["qty_retur"],0));
                        printf("<td>%s</td>",$row["satuan"]);
                        printf("<td class='right'>%s</td>", number_format($row["harga"],0));
                        printf("<td class='right'>%s</td>",$row["sub_total"] > 0 ? number_format($row["sub_total"],0) : '');
                        if ($row["kondisi"] == 1){
                            $kondisi = 'Bagus';
                        }elseif ($row["kondisi"] == 2) {
                            $kondisi = 'Rusak';
                        }else{
                            $kondisi = 'Expire';
                        }
                        printf("<td>%s</td>",$kondisi);
                        print("</tr>");
                        $total+= $row["sub_total"];
                        $nmr++;
                    }
                    print("<tr>");
                    print("<td class='right' colspan='7'>Total Retur :</td>");
                    printf("<td class='right'>%s</td>",number_format($total,0));
                    print("<td>&nbsp;</td>");
                    print("</tr>");
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4"><a href="<?php print($helper->site_url("pos.posretur")); ?>">Daftar Retur Penjualan</a></td>
        </tr>
    </table>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2018 - 2019 PT. Reka Sistem Teknologi
</div>
</body>
</html>
