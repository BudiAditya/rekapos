<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
<title>REKASYS - Approval Transaksi Kasir</title>
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
<div id="p" class="easyui-panel" title="Approval Transaksi Kasir" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form action="<?php print($helper->site_url("pos.sesikasir/approve/".$master["id"])); ?>" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
            <tr>
                <td class="right">Nama Kasir :</td>
                <td class="bold"><?php print($master["kasir"]);?></td>
                <td class="right">Session No :</td>
                <td class="bold"><?php print($master["session_no"]);?></td>
                <td class="right">Terminal ID :</td>
                <td class="bold"><?php print($master["id_terminal"].' ('.$master["no_terminal"].')');?></td>
            </tr>
            <tr>
                <td class="right">Jam Buka :</td>
                <td class="bold"><?php print($master["open_time"]);?></td>
                <td class="right">Jam Tutup :</td>
                <td class="bold"><?php print($master["close_time"]);?></td>
                <td class="right">Status :</td>
                <td class="bold"><?php print($master["session_status"]);?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" class="right"><u>TRANSAKSI TUNAI</u></td>
                <td>&nbsp;</td>
                <td colspan="2" class="right"><u>TRANSAKSI NON-TUNAI</u></td>
            </tr>
            <tr>
                <td class="right">Kas Awal :</td>
                <td class="bold right"><?php print(number_format($master["tunai_open"],0));?>+</td>
                <td>&nbsp;</td>
                <td class="right">Kartu Debit :</td>
                <td class="bold right"><?php print(number_format($master["kd_jual"],0));?></td>
            </tr>
            <tr>
                <td class="right">Kas Masuk :</td>
                <td class="bold right"><?php print(number_format($master["tunai_masuk_kas"],0));?>+</td>
                <td>&nbsp;</td>
                <td class="right">Kartu Kredit :</td>
                <td class="bold right"><?php print(number_format($master["kk_jual"],0));?></td>
            </tr>
            <tr>
                <td class="right">Penjualan Tunai :</td>
                <td class="bold right"><?php print(number_format($master["tunai_masuk_jual"],0));?>+</td>
                <td><?php print(number_format($master["total_trx"],0));?>&nbsp;Struk</td>
            </tr>
            <tr>
                <td class="right">Kas Keluar :</td>
                <td class="bold right"><?php print(number_format($master["tunai_keluar_kas"],0));?>-</td>
            </tr>
            <tr>
                <td class="right">Retur Tunai :</td>
                <td class="bold right"><?php print(number_format($master["tunai_keluar_retur"],0));?>-</td>
                <td><?php print(number_format($master["return_trx"],0));?>&nbsp;Struk</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="right">Total Penjualan Tunai :</td>
                <td class="bold"><input type="number" class="right" name="TotalTunai" id="TotalTunai" size="20" value="<?php print(($master["tunai_open"]+$master["tunai_masuk_kas"]+$master["tunai_masuk_jual"])-($master["tunai_keluar_kas"]+$master["tunai_keluar_retur"]));?>" readonly/></td>
            </tr>
            <tr>
                <td class="right">Total Tunai Kasir :</td>
                <td class="bold"><input type="number" class="right" name="TunaiKasir" id="TunaiKasir" size="20" value="<?php print($master["tunai_kasir"]);?>"></td>
                <td>&nbsp;</td>
                <td class="right">Disetujui Oleh :</td>
                <td class="bold"><?php print($master["user_name"]);?></td>
            </tr>
            <tr>
                <td class="right">Selisih Uang Tunai :</td>
                <td class="bold"><input type="number" class="right" name="SelisihKas" id="SelisihKas" size="20" value="<?php print($master["selisih_kas"]);?>" readonly></td>
                <td>&nbsp;</td>
                <td class="right">Tgl Disetujui :</td>
                <td class="bold"><?php print($master["approved_time"] == null ? date("Y-m-d H:i:s") : $master["approved_time"]);?></td>
            </tr>
            <tr>
                <td class="right">Keterangan :</td>
                <td colspan="4"><input type="text" class="bold" name="Keterangan" id="Keterangan" size="50" value="<?php print($master["keterangan"]);?>"/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="4"><input type="submit" id="submit" value="APPROVE"/>&nbsp;&nbsp;<a href="<?php print($helper->site_url("pos.sesikasir")); ?>">Daftar Sesi Kasir</a></td>
            </tr>
        </table>
    </form>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2018 - 2019 PT. Reka Sistem Teknologi
</div>
<script type="text/javascript">
    $(document).ready(function() {
        //hitung selisih
        $("#TunaiKasir").change(function(e){
            var ttn = $("#TotalTunai").val();
            var tks = this.value;
            var sls = tks - ttn;
            $("#SelisihKas").val(sls);
            $("#Keterangan").focus();
        });
    });
</script>
</body>
</html>
