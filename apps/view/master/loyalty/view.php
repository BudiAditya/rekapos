<!DOCTYPE HTML>
<html>
<head>
	<title>REKAPOS - Data Loyalty Program</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/sweetalert.min.js")); ?>"></script>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>

    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>
</head>

<body>
<?php /** @var $loyalty Loyalty */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php }
$badd = base_url('public/images/button/').'add.png';
$bsave = base_url('public/images/button/').'accept.png';
$bcancel = base_url('public/images/button/').'cancel.png';
$bview = base_url('public/images/button/').'view.png';
$bedit = base_url('public/images/button/').'edit.png';
$bdelete = base_url('public/images/button/').'delete.png';
$bclose = base_url('public/images/button/').'close.png';
$bsearch = base_url('public/images/button/').'search.png';
$bkembali = base_url('public/images/button/').'back.png';
$bcetak = base_url('public/images/button/').'printer.png';
$bsubmit = base_url('public/images/button/').'ok.png';
$baddnew = base_url('public/images/button/').'create_new.png';
$bpdf = base_url('public/images/button/').'pdf.png';
?>
<br />
<fieldset>
	<legend><span class="bold">Data Loyalty Program</span></legend>
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td class="bold right"><label for="StartDate">Mulai Tgl :</label></td>
            <td><input type="text" id="StartDate" name="StartDate" value="<?php print($loyalty->FormatStartDate(JS_DATE)); ?>" size="10" disabled/></td>
            <td class="bold right"><label for="EndDate">S/D Tgl :</label></td>
            <td><input type="text" id="EndDate" name="EndDate" value="<?php print($loyalty->FormatEndDate(JS_DATE)); ?>" size="10" disabled  /></td>
            <td class="bold right"><label for="LoyaltyCode">Kode :</label></td>
            <td><input type="text" id="LoyaltyCode" name="LoyaltyCode" value="<?php print($loyalty->LoyaltyCode); ?>" size="20" disabled placeholder="AUTO"/></td>
        </tr>
        <tr>
            <td class="bold right"><label for="ProgramName">Nama Program :</label></td>
            <td colspan="4"><input type="text" id="ProgramName" name="ProgramName" value="<?php print($loyalty->ProgramName); ?>" size="50" disabled placeholder="Diisi Nama Program Loyalty Berhadiah"/>
                &nbsp;
                <label class="bold right" for="Lstatus">Status :</label></td>
            <td><select name="Lstatus" id="Lstatus" disabled>
                    <option value="0" <?php print($loyalty->Lstatus == 0 ? 'selected="selected"' : '');?>> 0 - Non-Aktif </option>
                    <option value="1" <?php print($loyalty->Lstatus == 1 ? 'selected="selected"' : '');?>> 1 - Aktif </option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th colspan="5">RINCIAN HADIAH</th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th width="200px">Nama dan Jenis Hadiah</th>
                        <th width="50px">QTY</th>
                        <th width="80px">Min Poin</th>
                        <th width="100px">Nilai Hadiah</th>
                    </tr>
                    <?php
                    $counter = 0;
                    $total = 0;
                    $dta = null;
                    foreach($loyalty->Details as $idx => $detail) {
                        $counter++;
                        print("<tr class='bold'>");
                        printf("<td>%d</td>",$counter);
                        printf("<td>%s</td>",$detail->Hadiah);
                        printf("<td align='center'>%s</td>",number_format($detail->Qty,0));
                        printf("<td align='right'>%s</td>",number_format($detail->MinPoin,0));
                        printf("<td align='right'>%s</td>",number_format($detail->Nilai,0));
                        print("</tr>");
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <td><a href="<?php print($helper->site_url("master.loyalty")); ?>" >Daftar Program Loyalty</a></td>
        </tr>
    </table>
</fieldset>
</body>
</html>
