<!DOCTYPE HTML>
<html>
<head>
    <title>REKAPOS - Edit Master Barang</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var elements = ["Bbarcode","Bnama","Submit"];
            BatchFocusRegister(elements);
        });
    </script>
</head>

<body>
<?php /** @var $items Items */ /** @var $itemjenis ItemJenis[] */  /** @var $itemdepts ItemDivisi[] */ /** @var $itemgroups ItemKelompok[] */ /** @var $itemuoms ItemUom[] */ /** @var $suppliers Contacts[] */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
    <legend><span class="bold">Edit Data Master Barang</span></legend>
    <form action="<?php print($helper->site_url("master.items/edit/".$items->Bid)); ?>" method="post">
        <input type="hidden" id="Bkode" name="Bkode" value="<?php print($items->Bkode); ?>"/>
        <table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0 auto;" align="left">
            <tr>
                <td class="bold right"><label for="Bkode1">Kode Barang :</label></td>
                <td><input type="text" id="Bkode1" name="Bkode1" value="<?php print($items->Bkode); ?>" size="20" maxlength="30" disabled/></td>
                <td class="bold right"><label for="Bbarcode">Bar Code :</label></td>
                <td><input type="text" id="Bbarcode" name="Bbarcode" value="<?php print($items->Bbarcode); ?>" size="20" maxlength="30" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bnama">Nama Barang :</label></td>
                <td colspan="3"><input type="text" id="Bnama" name="Bnama" value="<?php print(htmlspecialchars($items->Bnama)); ?>" size="70" maxlength="100" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bjenis">Jenis Barang :</label></td>
                <td colspan="3"><select id="Bjenis" name="Bjenis" required>
                        <option value="">-- Pilih Jenis --</option>
                        <?php
                        foreach ($itemjenis as $ijenis) {
                            if ($items->Bjenis == $ijenis->JnsBarang) {
                                printf('<option value="%s" selected="selected">%s</option>', $ijenis->JnsBarang, $ijenis->JnsBarang);
                            } else {
                                printf('<option value="%s">%s</option>',$ijenis->JnsBarang, $ijenis->JnsBarang);
                            }
                        }
                        ?>
                        </select>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bdivisi">Divisi :</label></td>
                <td colspan="3"><select id="Bdivisi" name="Bdivisi" required>
                        <option value="">-- Pilih Divisi --</option>
                        <?php
                        foreach ($itemdepts as $itemdept) {
                            if ($items->Bdivisi == $itemdept->Divisi) {
                                printf('<option value="%s" selected="selected">%s</option>', $itemdept->Divisi, $itemdept->Divisi);
                            } else {
                                printf('<option value="%s">%s</option>',$itemdept->Divisi, $itemdept->Divisi);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bkelompok">Kelompok :</label></td>
                <td colspan="3"><select id="Bkelompok" name="Bkelompok" required>
                        <option value="">-- Pilih Kelompok --</option>
                        <?php
                        foreach ($itemgroups as $itemgroup) {
                            if ($items->Bkelompok == $itemgroup->Kelompok) {
                                printf('<option value="%s" selected="selected">%s</option>', $itemgroup->Kelompok, $itemgroup->Kelompok);
                            } else {
                                printf('<option value="%s">%s</option>', $itemgroup->Kelompok, $itemgroup->Kelompok);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bsupplier">Supplier :</label></td>
                <td colspan="3"><select id="Bsupplier" name="Bsupplier" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php
                        foreach ($suppliers as $supplier) {
                            if ($items->Bsupplier == $supplier->ContactCode) {
                                printf('<option value="%s" selected="selected">%s - %s</option>', $supplier->ContactCode, $supplier->ContactCode, $supplier->ContactName);
                            } else {
                                printf('<option value="%s">%s - %s</option>', $supplier->ContactCode, $supplier->ContactCode, $supplier->ContactName);
                            }
                        }
                        ?>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bsatbesar">Satuan Besar:</label></td>
                <td><select id="Bsatbesar" name="Bsatbesar" required>
                        <?php
                        foreach ($itemuoms as $itemuom) {
                            if ($items->Bsatbesar == $itemuom->Skode) {
                                printf('<option value="%s" selected="selected">%s - %s</option>', $itemuom->Skode, $itemuom->Skode, $itemuom->Snama);
                            } else {
                                printf('<option value="%s">%s - %s</option>', $itemuom->Skode, $itemuom->Skode, $itemuom->Snama);
                            }
                        }
                        ?>
                </td>
                <td><label for="Bisisatkecil">Isi :</label>
                    <input type="text" id="Bisisatkecil" name="Bisisatkecil" value="<?php print($items->Bisisatkecil == null ? 1 : $items->Bisisatkecil); ?>" size="3" maxlength="5" required style="text-align: right"/>
                    &nbsp;
                    <label for="Bsatkecil">Satuan Kecil :</label></td>
                <td><select id="Bsatkecil" name="Bsatkecil" required>
                        <?php
                        foreach ($itemuoms as $itemuom) {
                            if ($items->Bsatkecil == $itemuom->Skode) {
                                printf('<option value="%s" selected="selected">%s - %s</option>', $itemuom->Skode, $itemuom->Skode, $itemuom->Snama);
                            } else {
                                printf('<option value="%s">%s - %s</option>', $itemuom->Skode, $itemuom->Skode, $itemuom->Snama);
                            }
                        }
                        ?>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bminstock">Stock Minimum :</label></td>
                <td><input type="text" id="Bminstock" name="Bminstock" value="<?php print($items->Bminstock == null ? 0 : $items->Bminstock); ?>" size="3" maxlength="5" required style="text-align: right"/>
                    &nbsp
                    <input type="checkbox" name="Bisallowmin" id="Bisallowmin" value="1" <?php print($items->Bisallowmin ? 'checked="checked"' : ''); ?> />&nbsp;Boleh Minus</td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bnama">Keterangan :</label></td>
                <td colspan="3"><input type="text" id="Bketerangan" name="Bketerangan" value="<?php print($items->Bketerangan); ?>" size="70" maxlength="100"/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bisaktif">Status :</label></td>
                <td><select id="Bisaktif" name="Bisaktif" required>
                        <option value="1" <?php print($items->Bisaktif == "1" ? 'selected="selected"' : ''); ?>>Aktif</option>
                        <option value="0" <?php print($items->Bisaktif == "0" ? 'selected="selected"' : ''); ?>>Non-Aktif</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3"><button type="submit" id="Submit" class="button">Update Data</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.items")); ?>">Daftar Barang</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
