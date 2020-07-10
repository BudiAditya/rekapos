<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>REKASYS - Rekapitulasi Member</title>
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
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th colspan="3"><b>Laporan Rekapitulasi Member</b></th>
        </tr>
        <tr class="center">
            <th>Status</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select id="Status" name="Status" required>
                    <option value="0" <?php print($Status == 0 ? 'selected="selected"' : '');?>>0 - Non-Aktif</option>
                    <option value="1" <?php print($Status == 1 ? 'selected="selected"' : '');?>>1 - Aktif</option>
                </select>
            </td>
            <td>
                <select id="Output" name="Output" required>
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>>0 - Web Html</option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>>1 - Excel</option>
                </select>
            </td>
            <td><button type="submit" formaction="<?php print($helper->site_url("master.member/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php
if ($Reports != null){ ?>
    <h3>Laporan Member</h3>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>No Kartu</th>
            <th>Atas Nama</th>
            <th>Alamat</th>
            <th>Tgl Daftar</th>
            <th>Tgl Expire</th>
            <th>Poin Aktif</th>
            <th>Status</th>
        </tr>
        <?php
        $nmr = 0;
        while ($row = $Reports->FetchAssoc()) {
            $nmr++;
            print("<tr valign='Top'>");
            printf("<td>%s</td>", $nmr);
            printf("<td>%s</td>", $row["no_member"]);
            printf("<td>%s</td>", $row["nama"]);
            printf("<td>%s</td>", $row["alamat"]);
            printf("<td>%s</td>", date('d-m-Y', strtotime($row["tgl_daftar"])));
            printf("<td>%s</td>", date('d-m-Y', strtotime($row["exp_date"])));
            printf("<td align='right'>%s</td>", number_format($row["poin_aktif"], 0));
            if ($row["status_member"] == 1){
                print("<td>Aktif</td>");
            }else{
                print("<td>Non-Aktif</td>");
            }
            print("</tr>");
        }
        ?>
    </table>
<?php } ?>
</body>
</html>
