<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>REKASYS - Create Label</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript">

    </script>

</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br/>
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th colspan="9"><b>CREATE LABEL</b></th>
        </tr>
        <tr class="center">
            <th>No.</th>
            <th>PLU</th>
            <th>Bar Code</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Harga</th>
            <th>Type Label</th>
            <th>Qty Label</th>
            <th>Pilih</th>
        </tr>
        <?php
        $nmr = 0;
        foreach ($aitems as $items){
            $nmr++;
            print("<tr>");
            printf("<td>%d</td>",$nmr);
            printf("<td>%s</td>",$items[1]);
            printf("<td>%s</td>",$items[2]);
            printf("<td>%s</td>",$items[3]);
            printf("<td>%s</td>",$items[4]);
            printf("<td align='right'>%s</td>",number_format($items[5],0));
            printf("<td><select class='bold' name='ltype[]'><option value='1'>3 Kolom Kecil</option><option value='2'>3 Kolom Besar</option></select></td>");
            printf("<td><input class='bold right' type='text' name='lqty[]' value='12' size='3'></td>");
            printf("<td><input type='checkbox' name='lpilih[]' value='1' checked><input type='hidden' name='litemid[]' value='%d'></td>",$items[0]);
            print("<tr>");
        }
        ?>
        <tr>
            <td colspan="9" align="right"><button type="submit" formaction="<?php print($helper->site_url("inventory.createlabel/lblprint")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
</body>
</html>
