<!DOCTYPE HTML>
<?php /** @var $stock Stock */ ?>
<html>
<head>
    <title>Rekasys - Kartu Stock Barang</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            //var elements = ["CabangId", "OpDate","ItemType", "ItemId", "PartId", "OpQty", "OpPrice"];
            //BatchFocusRegister(elements);
            $("#startDate").customDatePicker({ showOn: "focus" });
            $("#endDate").customDatePicker({ showOn: "focus" });
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

<fieldset>
    <legend><b>Kartu Stock Barang</b></legend>
    <form id="frm" action="<?php print($helper->site_url("inventory.stock/card/".$stock->Id)); ?>" method="post">
        <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <td>Gudang :</td>
                <td class="bold"><?php printf('%s - %s',$stock->KdCabang,strtoupper($stock->WarehouseCode)) ?></td>
                <td>Nama Barang :</td>
                <td class="bold"><?php print($stock->ItemName); ?></td>
                <td align="right">Kode :</td>
                <td class="bold"><?php print($stock->ItemCode); ?></td>
                <td>Satuan :</td>
                <td class="bold"><?php print($stock->SatBesar); ?></td>
            </tr>
            <tr>
                <td>Dari Tgl :</td>
                <td><input type="text" class="text2" maxlength="10" size="10" id="startDate" name="startDate" value="<?php print(is_int($startDate) ? date(JS_DATE,$startDate) : null);?>" /></td>
                <td>Sampai Tgl :</td>
                <td><input type="text" class="text2" maxlength="10" size="10" id="endDate" name="endDate" value="<?php print(is_int($endDate) ? date(JS_DATE,$endDate) : null);?>" /></td>
                <td>Output :</td>
                <td><select id="outPut" name="outPut">
                        <option value="0" <?php print($outPut == 0 ? 'Selected="Selected"' : '');?>> HTML</option>
                        <option value="1" <?php print($outPut == 1 ? 'Selected="Selected"' : '');?>> Excel</option>
                    </select>
                </td>
                <td colspan="4" class="left">
                    <button type="submit">Proses</button>
                    <a href="<?php print($helper->site_url("inventory.stock")); ?>">Daftar Stock</a>
                </td>
            </tr>
        </table>
        <br>
        <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Transaksi</th>
                <th>Relasi</th>
                <th>Keterangan</th>
                <th>Awal</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Koreksi</th>
                <th>Saldo</th>
            </tr>
            <?php
            if($stkcard != null){
                $saldo = 0;
                $trxdate = null;
                $nmr = 0;
                while ($row = $stkcard->FetchAssoc()) {
                    $nmr++;
                    print('<tr>');
                    printf('<td class="center">%d</td>',$nmr);
                    if($trxdate <> $row["trx_date"]){
                        printf('<td>%s</td>',$row["trx_date"]);
                    }else{
                        print('<td>-</td>');
                    }
                    if ($nmr == 1){
                        $saldo = $row["saldo"];
                        printf('<td>%s</td>',$row["trx_type"]);
                    }else{
                        $saldo = ($saldo + $row["awal"] + $row["masuk"] + $row["koreksi"]) - $row["keluar"];
                        printf('<td>%s</td>',$row["trx_type"]);
                    }
                    printf('<td>%s</td>',$row["relasi"]);
                    printf('<td>%s</td>',$row["notes"]);
                    printf('<td class="right">%s</td>', $row["awal"] > 0 ? decFormat($row["awal"]) : '');
                    printf('<td class="right">%s</td>', $row["masuk"] > 0 ? decFormat($row["masuk"]) : '');
                    printf('<td class="right">%s</td>', $row["keluar"] > 0 ? decFormat($row["keluar"]) : '');
                    printf('<td class="right">%s%s</td>', $row["koreksi"] > 0 ? '+' : '', $row["koreksi"] <> 0 ? decFormat($row["koreksi"]) : '');
                    printf('<td class="right">%s</td>', decFormat($saldo));
                    print('</tr>');
                    $trxdate = $row["trx_date"];
                }
            }
            ?>
        </table>
    </form>
</fieldset>
</body>
</html>
