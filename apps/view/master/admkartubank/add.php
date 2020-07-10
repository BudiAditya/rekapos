<!DOCTYPE HTML>
<html>
<head>
	<title>REKAPOS - Input Admin Kartu Debit & Kredit</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var $admkartubank AdmKartuBank */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
	<legend><span class="bold">Input Admin Kartu Debit & Kredit</span></legend>
	<form action="<?php print($helper->site_url("master.admkartubank/add")); ?>" method="post">
		<table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0;">
            <tr>
                <td class="bold right"><label for="JnsKartu">Jenis Kartu :</label></td>
                <td><select name="JnsKartu" id="JnsKartu" class="bold" required>
                        <option value="">- Pilih Jenis -</option>
                        <option value="1" <?php print($admkartubank->JnsKartu == 1 ? 'selected="selected"' : '');?>> 1 - Debit </option>
                        <option value="2" <?php print($admkartubank->JnsKartu == 2 ? 'selected="selected"' : '');?>> 2 - Kredit </option>
                    </select>

                </td>
            </tr>
			<tr>
				<td class="bold right"><label for="NamaKartu">Nama Kartu :</label></td>
				<td><input type="text" class="bold" id="NamaKartu" name="NamaKartu" value="<?php print($admkartubank->NamaKartu); ?>" size="30" onkeyup="this.value = this.value.toUpperCase();" required/></td>
			</tr>
            <tr>
                <td class="bold right"><label for="NamaBank">Nama Bank :</label></td>
                <td><input type="text" class="bold" id="NamaBank" name="NamaBank" value="<?php print($admkartubank->NamaBank); ?>" onkeyup="this.value = this.value.toUpperCase();" size="30" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Minimal">Minimal Belanja :</label></td>
                <td><input type="text" class="bold right" id="Minimal" name="Minimal" value="<?php print($admkartubank->Minimal); ?>" size="10" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="ByAdminPct">Biaya Admin :</label></td>
                <td><input type="text" class="bold right" id="ByAdminPct" name="ByAdminPct" value="<?php print($admkartubank->ByAdminPct); ?>" size="3" required/>%
                &nbsp;
                Atau Rp.
                &nbsp;
                <input type="text" class="bold right" id="ByAdmin" name="ByAdmin" value="<?php print($admkartubank->ByAdmin); ?>" size="10" required/></td>
            </tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="submit" class="button">Simpan Data</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.admkartubank")); ?>" class="button">Daftar Admin Kartu</a>
                </td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
