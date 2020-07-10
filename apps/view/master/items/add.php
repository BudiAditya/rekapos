<!DOCTYPE HTML>
<html>
<head>
	<title>REKAPOS - Entry Master Barang</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            //var elements = ["Bkode","Bbarcode","Bnama","Submit"];
            //BatchFocusRegister(elements);
            $("#Bkode").change(function () {
                //check existing code
                var url = "<?php print($helper->site_url("master.items/checkcode/")); ?>" + this.value;
                $.get(url, function (data) {
                    if(data != 0){
                       alert("Maaf, Kode Barang: ["+$("#Bkode").val()+"] sudah terpakai..\nNama Barang: "+data);
                       $("#Bkode").val('');
                       $("#Bkode").focus();
                    }else{
                       $("#Bbarcode").val($("#Bkode").val());
                       $("#Bnama").focus();
                    }
                });
            });

            $("#Bkelompok").change(function () {
                //get new PLU
                var url = "<?php print($helper->site_url("master.items/getAutoPLU/")); ?>" + this.value;
                //alert(url);
                $.get(url, function (data){
                    $("#Bkode").val(data);
                    $("#Bbarcode").val(data);
                });
            });

            $("#Bbarcode").change(function () {
                //get new PLU
                var bcd = this.value;
                var url = "<?php print($helper->site_url("master.items/checkBarCode/")); ?>" + bcd;
                $.get(url, function (data){
                    if (data != '-'){
                        alert("Kode Barcode: "+ bcd +" sudah terpakai barang lain!");
                        $("#Bbarcode").val('');
                    }
                });
            });
        });
    </script>
</head>

<body>
<?php /** @var $items Items */ /** @var $itemjenis ItemJenis[] */  /** @var $itemdivisi ItemDivisi[] */ /** @var $itemgroups ItemKelompok[] */ /** @var $itemuoms ItemUom[] */ /** @var $suppliers Contacts[] */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
	<legend><span class="bold">Entry Data Master Barang</span></legend>
	<form action="<?php print($helper->site_url("master.items/add")); ?>" method="post">
		<table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0 auto;" align="left">
            <tr>
                <td class="bold right"><label for="Bkelompok">KELOMPOK :</label></td>
                <td colspan="2"><select id="Bkelompok" name="Bkelompok" required style="height: 20px;width: 250px">
                        <option value="">-- Pilih Kelompok --</option>
                        <?php
                        foreach ($itemgroups as $itemgroup) {
                            if ($items->Bkelompok == $itemgroup->Kode) {
                                printf('<option value="%s" selected="selected">%s</option>', $itemgroup->Kode, $itemgroup->Kelompok);
                            } else {
                                printf('<option value="%s">%s</option>', $itemgroup->Kode, $itemgroup->Kelompok);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td class="bold right"><label for="Bjenis">JENIS :</label></td>
                <td><select id="Bjenis" name="Bjenis" required style="height: 20px;width: 100px">
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
                <td colspan="4" style="color: red" class="blink"><b>**Mohon Re-Check DATA BARANG dulu agar tidak terjadi data ganda**</b></td>
            </tr>
			<tr>
				<td class="bold right"><label for="Bkode">KODE / PLU :</label></td>
				<td><input type="text" id="Bkode" class="bold" name="Bkode" value="<?php print($items->Bkode); ?>" style="width: 120px" maxlength="50" required/></td>
                <td class="bold right"><label for="Bbarcode">BAR CODE :</label></td>
                <td colspan="2"><input type="text" class="bold" id="Bbarcode" name="Bbarcode" value="<?php print($items->Bbarcode); ?>" style="width: 146px"  maxlength="50" required/></td>
                <td class="bold right"><label for="KelompokId">LOKASI :</label></td>
                <td colspan="3"><select id="KelompokId" name="KelompokId" required style="height: 20px;width: 200px">
                        <option value="">-- Pilih Lokasi --</option>
                        <?php
                        /** @var $lokasis Lokasi[] */
                        foreach ($lokasis as $lokasi) {
                            if ($items->KelompokId == $lokasi->Id) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $lokasi->Id, $lokasi->Kode, $lokasi->Keterangan);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $lokasi->Id, $lokasi->Kode, $lokasi->Keterangan);
                            }
                        }
                        ?>
                    </select>
                    <span style="color: red" class="blink"><b>*BARU*</b></span>
                </td>
			</tr>
            <tr>
                <td class="bold right"><label for="Bnama">NAMA BARANG :</label></td>
                <td colspan="4"><input type="text" class="bold" id="Bnama" name="Bnama" value="<?php print(htmlspecialchars($items->Bnama)); ?>" style="width: 410px"  maxlength="150" onkeyup="this.value = this.value.toUpperCase();" required/></td>
                <td class="bold right"><label for="Bdivisi">MERK :</label></td>
                <td colspan="3"><select id="Bdivisi" name="Bdivisi" required style="height: 20px;width: 200px">
                        <option value="">-- Pilih Merk --</option>
                        <?php
                        foreach ($itemdivisi as $divisi) {
                            if ($items->Bdivisi == $divisi->Kode) {
                                printf('<option value="%s" selected="selected">%s</option>', $divisi->Kode, $divisi->Divisi);
                            } else {
                                printf('<option value="%s">%s</option>',$divisi->Kode, $divisi->Divisi);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bnama">KETERANGAN :</label></td>
                <td colspan="4"><input type="text" class="bold" id="Bketerangan" name="Bketerangan" value="<?php print(htmlspecialchars($items->Bketerangan)); ?>" style="width: 410px" required/></td>
                <td class="bold right"><label for="Bsupplier">SUPPLIER :</label></td>
                <td colspan="3"><select id="Bsupplier" name="Bsupplier" required style="height: 20px;width: 200px">
                        <option value="">-- Pilih Supplier --</option>
                        <?php
                        foreach ($suppliers as $supplier) {
                            if ($items->Bsupplier == $supplier->ContactCode) {
                                printf('<option value="%s" selected="selected">%s</option>', $supplier->ContactCode, $supplier->ContactName);
                            } else {
                                printf('<option value="%s">%s</option>',$supplier->ContactCode, $supplier->ContactName);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bsatkecil">SATUAN ECER :</label></td>
                <td><select id="Bsatkecil" name="Bsatkecil" required style="height: 20px;width: 120px">
                    <option value=""></option>
                    <?php
                    foreach ($itemuoms as $satuan) {
                        if ($items->Bsatkecil == $satuan->Skode) {
                            printf('<option value="%s" selected="selected">%s</option>', $satuan->Skode, $satuan->Skode);
                        } else {
                            printf('<option value="%s">%s</option>',$satuan->Skode, $satuan->Skode);
                        }
                    }
                    ?>
                    </select>
                </td>
                <td class="bold right"><label for="Bsatbesar">SATUAN GROSIR :</label></td>
                <td colspan="2"><select id="Bsatbesar" name="Bsatbesar" required style="height: 20px;width: 120px">
                        <option value=""></option>
                        <?php
                        foreach ($itemuoms as $satuan) {
                            if ($items->Bsatbesar == $satuan->Skode) {
                                printf('<option value="%s" selected="selected">%s</option>', $satuan->Skode, $satuan->Skode);
                            } else {
                                printf('<option value="%s">%s</option>',$satuan->Skode, $satuan->Skode);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td class="bold right"><label for="Bisisatkecil">ISI KEMASAN :</label></td>
                <td><input type="number" id="Bisisatkecil" class="bold right" name="Bisisatkecil" value="<?php print($items->Bisisatkecil); ?>" style="width: 50px" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bhargajual1">HARGA ECERAN :</label></td>
                <td><input type="number" id="Bhargajual1" class="bold right" name="Bhargajual1" value="<?php print($items->Bhargajual1); ?>" style="width: 120px" required/></td>
                <td class="bold right"><label for="Bhargajual2">HARGA GROSIR :</label></td>
                <td colspan="2"><input type="number" id="Bhargajual2" class="bold right" name="Bhargajual2" value="<?php print($items->Bhargajual2); ?>" style="width: 120px" required/></td>
                <td class="bold right"><label for="Bminstock">STOK MINIMUM :</label></td>
                <td><input type="number" id="Bminstock" class="bold right" name="Bminstock" value="<?php print($items->Bminstock); ?>" style="width: 50px" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Bhargabeli">HARGA BELI :</label></td>
                <td><input type="number" id="Bhargabeli" class="bold right" name="Bhargabeli" value="<?php print($items->Bhargabeli); ?>" style="width: 120px"/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="7" class="bold">
                    <input type="checkbox" name="Bisaktif" id="Bisaktif" value="1" <?php print($items->Bisaktif ? 'checked="checked"' : ''); ?> />&nbsp;Produk Aktif
                    &nbsp;&nbsp;
                    <input type="checkbox" name="IsSale" id="IsSale" value="1" <?php print($items->IsSale ? 'checked="checked"' : ''); ?> />&nbsp;Untuk Dijual
                    &nbsp;&nbsp;
                    <input type="checkbox" name="IsPurchase" id="IsPurchase" value="1" <?php print($items->IsPurchase ? 'checked="checked"' : ''); ?> />&nbsp;Produk Dibeli
                    &nbsp;&nbsp;
                    <input type="checkbox" name="IsStock" id="IsStock" value="1" <?php print($items->IsStock ? 'checked="checked"' : ''); ?> />&nbsp;Produk DiStok
                    &nbsp;&nbsp;
                    <input type="checkbox" name="IsTimbang" id="IsTimbang" value="1" <?php print($items->IsTimbang ? 'checked="checked"' : ''); ?> />&nbsp;Produk DiTimbang
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="4"><button type="submit" id="Submit" class="button">Simpan Data</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.items")); ?>">Daftar Barang</a>
                </td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
