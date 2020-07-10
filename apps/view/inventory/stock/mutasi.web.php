<!DOCTYPE HTML>
<html>
<head>
    <title>Rekasys - Mutasi Stock Barang</title>
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
    <legend><b>Mutasi Stock Barang</b></legend>
    <form id="frm" action="<?php print($helper->site_url("inventory.stock/mutasi")); ?>" method="post">
        <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <td>Gudang</td>
                <td>
                    <select name="whId" class="text2" id="whId" required>
                        <?php
                            foreach ($gudangs as $cab) {
                                if ($cab->Id == $whId) {
                                    printf('<option value="%d" selected="selected"> %s </option>', $cab->Id, $cab->WhCode);
                                } else {
                                    printf('<option value="%d"> %s </option>', $cab->Id, $cab->WhCode);
                                }
                            }
                        ?>

                    </select>
                </td>
                <td>Dari Tgl :</td>
                <td><input type="text" class="text2" maxlength="10" size="10" id="startDate" name="startDate" value="<?php print(is_int($startDate) ? date(JS_DATE,$startDate) : null);?>" /></td>
                <td>Sampai Tgl :</td>
                <td><input type="text" class="text2" maxlength="10" size="10" id="endDate" name="endDate" value="<?php print(is_int($endDate) ? date(JS_DATE,$endDate) : null);?>" /></td>
                <td>Output :</td>
                <td><select id="outPut" name="outPut">
                    <option value="0" <?php print($outPut == 0 ? 'Selected="Selected"' : '');?>>HTML</option>
                    <option value="1" <?php print($outPut == 1 ? 'Selected="Selected"' : '');?>>Excel</option>
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
                <th rowspan="2">No.</th>
                <th rowspan="2">Kode Barang</th>
                <th rowspan="2">Nama Barang</th>
                <th rowspan="2">Satuan</th>
                <th rowspan="2">Awal</th>
                <th colspan="4">Masuk</th>
                <th colspan="4">Keluar</th>
                <th rowspan="2">Koreksi</th>
                <th rowspan="2">Saldo</th>
            </tr>
            <tr>
                <th>Pembelian</th>
                <th>Produksi</th>
                <th>Kiriman</th>
                <th>Retur</th>
                <th>Penjualan</th>
                <th>Produksi</th>
                <th>Dikirim</th>
                <th>Retur</th>
            </tr>
            <?php
            if($mstock != null){
                $nmr = 0;
                $awl = 0;
                $mbl = 0;
                $mxi = 0;
                $mrj = 0;
                $kjl = 0;
                $kxo = 0;
                $krb = 0;
                $kor = 0;
                $ain = 0;
                $aot = 0;
                $sld = 0;
                $ssl = 0;
                while ($row = $mstock->FetchAssoc()) {
                    $nmr++;
                    print('<tr>');
                    printf('<td class="center">%d</td>',$nmr);
                    printf('<td>%s</td>',$row["item_code"]);
                    printf('<td nowrap="nowrap">%s</td>',$row["item_name"]);
                    printf('<td>%s</td>',$row["satuan"]);
                    printf('<td class="right">%s</td>',decFormat($row["sAwal"],0));
                    printf('<td class="right">%s</td>',$row["sBeli"] > 0 ? decFormat($row["sBeli"]) : '');
                    printf('<td class="right">%s</td>',$row["sAsyin"] > 0 ? decFormat($row["sAsyin"]) : '');
                    printf('<td class="right">%s</td>',$row["sXin"] > 0 ? decFormat($row["sXin"]) : '');
                    printf('<td class="right">%s</td>',$row["sRjual"] > 0 ? decFormat($row["sRjual"]) : '');
                    printf('<td class="right">%s</td>',$row["sJual"] > 0 ? decFormat($row["sJual"]) : '');
                    printf('<td class="right">%s</td>',$row["sAsyout"] > 0 ? decFormat($row["sAsyout"]) : '');
                    printf('<td class="right">%s</td>',$row["sXout"] > 0 ? decFormat($row["sXout"]) : '');
                    printf('<td class="right">%s</td>',$row["sRbeli"] > 0 ? decFormat($row["sRbeli"]) : '');
                    printf('<td class="right">%s</td>',$row["sKoreksi"] <> 0 ? decFormat($row["sKoreksi"]) : '');
                    $sld = ($row["sAwal"] + $row["sBeli"] + $row["sAsyin"] + $row["sXin"] + $row["sRjual"]) - ($row["sJual"] + $row["sAsyout"] + $row["sXout"] + $row["sRbeli"]) + $row["sKoreksi"];
                    printf('<td class="right">%s</td>',decFormat($sld));
                    print('</tr>');
                    $awl+= $row["sAwal"];
                    $mbl+= $row["sBeli"];
                    $mxi+= $row["sXin"];
                    $mrj+= $row["sRjual"];
                    $kjl+= $row["sJual"];
                    $kxo+= $row["sXout"];
                    $krb+= $row["sRbeli"];
                    $kor+= $row["sKoreksi"];
                    $ain+= $row["sAsyin"];
                    $aot+= $row["sAsyout"];
                    $ssl+= $sld;
                }
                printf('<tr>');
                printf('<td class="bold right" colspan="4">Total Mutasi</td>');
                printf('<td class="bold right">%s</td>',decFormat($awl,2));
                printf('<td class="bold right">%s</td>',decFormat($mbl,2));
                printf('<td class="bold right">%s</td>',decFormat($ain,2));
                printf('<td class="bold right">%s</td>',decFormat($mxi,2));
                printf('<td class="bold right">%s</td>',decFormat($mrj,2));
                printf('<td class="bold right">%s</td>',decFormat($kjl,2));
                printf('<td class="bold right">%s</td>',decFormat($aot,2));
                printf('<td class="bold right">%s</td>',decFormat($kxo,2));
                printf('<td class="bold right">%s</td>',decFormat($krb,2));
                printf('<td class="bold right">%s</td>',decFormat($kor,2));
                printf('<td class="bold right">%s</td>',decFormat($ssl,2));
                printf('</tr>');
            }
            ?>
        </table>
    </form>
</fieldset>
</body>
</html>
