<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>REKASYS - History Poin Member</title>
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
<br/>
<!-- start web report -->
<?php
/** @var $member Member */
if ($reports != null){
    print("<h3>History Poin Member</h3>");
    printf("<h3>%s - %s</h3>",$member->NoMember,$member->Nama); ?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>No Transaksi</th>
            <th>Nilai</th>
            <th>Poin</th>
            <th>Kode Promo</th>
        </tr>
        <?php
        $nmr = 0;
        $tpn = 0;
        while ($row = $reports->FetchAssoc()) {
            $nmr++;
            print("<tr valign='Top'>");
            printf("<td>%s</td>", $nmr);
            printf("<td>%s</td>", date('d-m-Y', strtotime($row["waktu"])));
            printf("<td>%s</td>", $row["trx_no"]);
            printf("<td class='right'>%s</td>", number_format($row["total_transaksi"],0));
            printf("<td class='right'>%s</td>", number_format($row["jum_poin"],0));
            printf("<td>%s</td>", $row["kode_promo"]);
            print("</tr>");
            $tpn+= $row["jum_poin"];
        }
        print("<tr>");
        print("<td colspan='4' class='right'>Total Poin</td>");
        printf("<td class='right'>%s</td>", number_format($tpn,0));
        print("<td>&nbsp;</td>");
        print("<tr>");
        ?>
    </table>
    <a href="<?php print($helper->site_url("master.member")); ?>">DAFTAR MEMBER</a>
<?php } ?>
</body>
</html>
