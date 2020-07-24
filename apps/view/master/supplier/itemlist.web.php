<!DOCTYPE HTML>
<html>
<?php $userName = AclManager::GetInstance()->GetCurrentUser()->RealName; ?>
<head>
	<title>REKASYS - Daftar Barang Distributor</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<h3>DAFTAR BARANG DISTRIBUTOR</h3>
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th>Supplier/Distributor :</th>
            <th><?=$dtSupplier->ContactName.'('.$dtSupplier->ContactCode.')';?></th>
            <th>Output : </th>
            <th><select name="output">
                    <option value="1" <?php print($output == 1 ? 'selected="selected"' : '');?>>1 - Web</option>
                    <option value="2" <?php print($output == 2 ? 'selected="selected"' : '');?>>2 - Excel</option>
                </select>
            </th>
            <th>
                <button type="submit" formaction="<?php print($helper->site_url("master.supplier/itemlist/".$dtSupplier->Id)); ?>"><b>Proses</b></button>
                <input type="button" class="button" onclick="printDiv('printArea')" value="Print"/>
            </th>
        </tr>
    </table>
</form>
<br>
<!-- start web report -->
<div id="printArea">
<?php  if ($dtItems != null){ ?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Kode</th>
                <th>Barcode</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
            </tr>
            <?php
            $nmr = 0;
            $sqty = 0;
            $snilai = 0;
            /** @var $dtItems Items[] */
            foreach ($dtItems as $items) {
                $nmr++;
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>",$items->Bkode);
                printf("<td>%s</td>",$items->Bbarcode);
                printf("<td>%s</td>",$items->Bnama);
                printf("<td>%s</td>",$items->Bsatbesar);
                print("</tr>");
            }
            ?>
        </table>
        <br>
<?php
    print('<i>* Printed by: '.$userName.'  - Time: '.date('d-m-Y h:i:s').' *</i>');
} ?>
</div>
<script type="text/javascript">
    function printDiv(divName) {
        //if (confirm('Print Invoice ini?')) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
        //}
    }
</script>
</body>
</html>
