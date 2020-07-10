<!DOCTYPE HTML>
<html>
<head>
	<title>REKASYS - Ubah Data Pajak</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function () {
                var elements = ["TaxCode", "TaxName", "TaxRate", "TaxMode","Update"];
                BatchFocusRegister(elements);
        });
    </script>
</head>

<body>
<?php /** @var $tax Tax */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
	<legend><span class="bold">Ubah Data Pajak</span></legend>
	<form action="<?php print($helper->site_url("master.tax/edit/".$tax->Id)); ?>" method="post">
		<table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0;">
			<tr>
				<td class="bold right"><label for="TaxCode">Kode :</label></td>
				<td><input type="text" id="TaxCode" name="TaxCode" value="<?php print($tax->TaxCode); ?>" size="15" required/></td>
			</tr>
            <tr>
                <td class="bold right"><label for="TaxName">Pajak :</label></td>
                <td><input type="text" id="TaxName" name="TaxName" value="<?php print($tax->TaxName); ?>" size="30" required/></td>
            </tr>
			<tr>
				<td class="bold right"><label for="TaxRate">Tarif (%) :</label></td>
				<td><input type="text" id="TaxRate" name="TaxRate" value="<?php print($tax->TaxRate); ?>" size="30" required/></td>
			</tr>
            <tr>
                <td class="bold right"><label for="TaxMode">Mode :</label></td>
                <td><select name="TaxMode" id="TaxMode" required>
                        <option value="1" <?php print($tax->TaxMode == 1 ? 'selected="selected"' : '');?>> 1 - Menambah </option>
                        <option value="2" <?php print($tax->TaxMode == 2 ? 'selected="selected"' : '');?>> 2 - Mengurang </option>
                    </select>
                </td>
            </tr>
			<tr>
				<td>&nbsp;</td>
				<td><button id="Update" type="submit" class="button">Update</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.tax")); ?>" class="button">Batal</a>
                </td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
