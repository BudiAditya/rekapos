<!DOCTYPE HTML>
<html>
<head>
    <title>REKAPOS - View Promo Penjualan</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var $promo Promo */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
    <legend><span class="bold">VIEW PROMO PENJUALAN</span></legend>
    <table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0;">
        <tr>
            <td class="bold right"><label for="TypePromo">JENIS PROMO :</label></td>
            <td><select id="TypePromo" name="TypePromo" disabled style="height: 20px;width: 150px">
                    <option value=""></option>
                    <?php
                    while ($type = $tpromo->FetchAssoc()){
                        if ($promo->TypePromo == $type["code"]) {
                            printf("<option value='%d' selected='selected'> %d - %s </option>", $type["code"], $type["code"], strtoupper($type["short_desc"]));
                        }else {
                            printf("<option value='%d'> %d - %s </option>", $type["code"], $type["code"], strtoupper($type["short_desc"]));
                        }
                    }
                    ?>
                </select>
            </td>
            <td class="bold right"><label for="KodePromo">KODE :</label></td>
            <td><input type="text" class="bold" id="KodePromo" name="KodePromo" value="<?php print($promo->KodePromo); ?>" style="width: 100px" readonly placeholder="AUTO"/></td>
            <td class="bold right"><label for="StartDate">MULAI TGL :</label></td>
            <td><input type="text" id="StartDate" name="StartDate" value="<?php print($promo->FormatStartDate(JS_DATE)); ?>" style="width: 100px" maxlength="10"/></td>
            <td class="bold right"><label for="StartTime">JAM :</label></td>
            <td><input type="text" class="bold" id="StartTime" name="StartTime" value="<?php print($promo->StartTime); ?>" style="width: 50px" maxlength="5" placeholder="HH:MM"/></td>
            <td class="bold right"><label for="PromoStatus">STATUS :</label></td>
            <td><select id="PromoStatus" name="PromoStatus" disabled style="height: 20px;">
                    <option value="0" <?php print($promo->PromoStatus == 0 ? 'selected="selected"' : '');?>> NON-AKTIF </option>
                    <option value="1" <?php print($promo->PromoStatus == 1 ? 'selected="selected"' : '');?>> AKTIF </option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="bold right"><label for="NamaPromo">NAMA PROMO :</label></td>
            <td colspan="3"><input type="text" class="bold" id="NamaPromo" name="NamaPromo" value="<?php print($promo->NamaPromo); ?>" style="width: 318px" onkeyup="this.value = this.value.toUpperCase();" required/></td>
            <td class="bold right"><label for="EndDate">BERAKHIR TGL :</label></td>
            <td><input type="text" id="EndDate" name="EndDate" value="<?php print($promo->FormatEndDate(JS_DATE)); ?>" style="width: 100px" maxlength="10"/></td>
            <td class="bold right"><label for="EndTime">JAM :</label></td>
            <td><input type="text" class="bold" id="EndTime" name="EndTime" value="<?php print($promo->EndTime); ?>" style="width: 50px" maxlength="5" placeholder="HH:MM"/></td>
        </tr>
        <tr>
            <td class="bold right"><label for="KodeBarang">KODE / BARCODE :</label></td>
            <td colspan="7"><input type="text" class="bold" id="KodeBarang" name="KodeBarang" value="<?php print($promo->KodeBarang); ?>" style="width: 150px"/>
                <input type="text" class="bold" id="NamaBarang" name="NamaBarang" value="<?php print($promo->NamaBarang); ?>" style="width: 318px" readonly placeholder="Nama Barang Promo"/>
                <input type="text" class="bold" id="Satuan" name="Satuan" value="<?php print($promo->SatuanBarang); ?>" style="width: 70px" readonly placeholder="Satuan"/>
                <input type="text" class="bold right" id="HargaBarang" name="HargaBarang" value="<?php print($promo->HargaBarang); ?>" style="width: 100px" readonly placeholder="Harga"/>
                <input type="hidden" id="HrgBeli" name="HrgBeli" value="0">
            </td>
        </tr>
        <tr>
            <td class="bold right"><label for="Qty1">QTY MINIMAL :</label></td>
            <td colspan="3">
                <input type="number" class=" bold right" id="Qty1" name="Qty1" value="<?php print($promo->Qty1); ?>" style="width: 150px" maxlength="5"/>
                <input type="checkbox" name="IsKelipatan" id="IsKelipatan" value="1" <?php print($promo->IsKelipatan ? 'checked="checked"' : ''); ?> />&nbsp;Berlaku Kelipatan
            </td>
            <td class="bold right"><label for="IsMemberOnly">BERLAKU UNTUK :</label></td>
            <td colspan="3"><select id="IsMemberOnly" name="IsMemberOnly" required style="height: 20px;">
                    <option value="0" <?php print($promo->IsMemberOnly == 0 ? 'selected="selected"' : '');?>> U M U M </option>
                    <option value="1" <?php print($promo->IsMemberOnly == 1 ? 'selected="selected"' : '');?>> KHUSUS MEMBER </option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="bold right"><label for="ItemAmtMinimal">BELANJA BARANG MINIMAL :</label></td>
            <td colspan="7">
                <input type="number" class=" bold right" id="ItemAmtMinimal" name="ItemAmtMinimal" value="<?php print($promo->ItemAmtMinimal); ?>" style="width: 150px" maxlength="5"/>
                <input type="checkbox" name="IsItemAmtKelipatan" id="IsItemAmtKelipatan" value="1" <?php print($promo->IsItemAmtKelipatan ? 'checked="checked"' : ''); ?> />&nbsp;Berlaku Kelipatan
            </td>
        </tr>
        <tr>
            <td class="bold right"><label for="SaleAmtMinimal">TOTAL BELANJA MINIMAL :</label></td>
            <td colspan="7">
                <input type="number" class=" bold right" id="SaleAmtMinimal" name="SaleAmtMinimal" value="<?php print($promo->SaleAmtMinimal); ?>" style="width: 150px" maxlength="10"/>
                <input type="checkbox" name="IsSaleAmtKelipatan" id="IsSaleAmtKelipatan" value="1" <?php print($promo->IsSaleAmtKelipatan ? 'checked="checked"' : ''); ?> />&nbsp;Berlaku Kelipatan
            </td>
        </tr>
        <tr>
            <td colspan="2"><b><u>DISKON/BONUS/POINT :</u></b></td>
        </tr>
        <tr>
            <td class="bold right"><label for="PctDiskon">D I S K O N :</label></td>
            <td colspan="7">
                <input type="number" class=" bold right" id="PctDiskon" name="PctDiskon" value="<?php print($promo->PctDiskon); ?>" style="width: 50px" maxlength="5" readonly/>
                &nbsp; % Rp.
                <input type="number" class=" bold right" id="AmtDiskon" name="AmtDiskon" value="<?php print($promo->AmtDiskon); ?>" style="width: 100px" maxlength="10" readonly/>
            </td>
        </tr>
        <tr>
            <td class="bold right"><label for="PctDiskon">POINT MEMBER :</label></td>
            <td colspan="7"><input type="number" class=" bold right" id="AmtPoint" name="AmtPoint" value="<?php print($promo->AmtPoint); ?>" style="width: 50px" maxlength="5" readonly/></td>
        </tr>
        <tr>
            <td class="bold right"><label for="KodeBonus">KODE BONUS :</label></td>
            <td colspan="7"><input type="text" class="bold" id="KodeBonus" name="KodeBonus" value="<?php print($promo->KodeBonus); ?>" style="width: 150px" readonly/>
                <input type="text" class="bold" id="NamaBonus" name="NamaBonus" value="<?php print($promo->NamaBonus); ?>" style="width: 318px" readonly placeholder="Nama Barang Bonus"/>
                <input type="text" class="bold" id="SatuanBonus" name="SatuanBonus" value="<?php print($promo->SatuanBonus); ?>" style="width: 70px" readonly placeholder="Satuan"/>
                <input type="text" class="bold right" id="HargaBonus" name="HargaBonus" value="<?php print($promo->HargaBonus); ?>" style="width: 100px" readonly placeholder="Harga"/>
            </td>
            <td class="bold right"><label for="QtyBonus">QTY :</label></td>
            <td><input type="number" class=" bold right" id="QtyBonus" name="QtyBonus" value="<?php print($promo->QtyBonus); ?>" style="width: 50px" maxlength="5" readonly/></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3"><a href="<?php print($helper->site_url("master.promo")); ?>">DAFTAR PROMO</a></td>
        </tr>
    </table>
</fieldset>
<script type="text/javascript">
    $( function() {
        /*declare variable
         1	Bonus by Qty Item
         2	Diskon by Qty Item
         3	Poin by Qty Item
         4	Bonus by Nilai Item
         5	Diskon by Nilai Item
         6	Poin by Nilai Item
         7	Bonus by Nilai Belanja
         8	Diskon by Nilai Belanja
         9	Poin by Nilai Belanja
         10 Promo pembuatan Kartu Member*/
        var tpr,kdb,hgj,hgb,pcd,amd;
        $("#StartDate").customDatePicker({ showOn: "focus" });
        $("#EndDate").customDatePicker({ showOn: "focus" });
    });
</script>
</body>
</html>
