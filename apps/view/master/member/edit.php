<!DOCTYPE HTML>
<html>
<head>
    <title>REKAPOS - Ubah Data Member</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var $member Member */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
    <legend><span class="bold">UBAH DATA MEMBER</span></legend>
    <form action="<?php print($helper->site_url("master.member/edit/".$member->Id)); ?>" method="post">
        <table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0;">
            <tr>
                <td class="bold right"><label for="NoMember">NOMOR MEMBER :</label></td>
                <td><input type="text" class="bold" id="NoMember" name="NoMember" value="<?php print($member->NoMember); ?>" style="width: 150px" maxlength="12" placeholder="-AUTO-" readonly/></td>
                <td class="bold right"><label for="TglDaftar">TGL DAFTAR :</label></td>
                <td><input type="text" class="bold" id="TglDaftar" name="TglDaftar" value="<?php print($member->FormatTglDaftar(JS_DATE)); ?>" style="width: 100px" maxlength="10" readonly/></td>
                <td class="bold right"><label for="ExpDate" class="bold">TGL EXPIRE :</label></td>
                <td><input type="text" class="bold" id="ExpDate" name="ExpDate" value="<?php print($member->FormatExpDate(JS_DATE)); ?>" style="width: 130px" required/>*</td>
            </tr>
            <tr>
                <td class="bold right"><label for="NoIdCard">NOMOR KTP / SIM :</label></td>
                <td><input type="text" class="bold" id="NoIdCard" name="NoIdCard" value="<?php print($member->NoIdCard); ?>" style="width: 150px" readonly/>*</td>
                <td class="bold right"><label for="ExpIdCard">MASA BERLAKU :</label></td>
                <td><input type="text" class="bold" id="ExpIdCard" name="ExpIdCard" value="<?php print($member->FormatExpIdCard(JS_DATE)); ?>" style="width: 100px" maxlength="10"/>*</td>
                <td class="bold right"><label for="NoHp" class="bold">NOMOR HP :</label></td>
                <td><input type="text" class="bold" id="NoHp" name="NoHp" value="<?php print($member->NoHp); ?>" style="width: 130px" required/>*</td>
            </tr>
            <tr>
                <td class="bold right"><label for="Nama">NAMA LENGKAP :</label></td>
                <td colspan="2"><input type="text" class="bold" id="Nama" name="Nama" value="<?php print($member->Nama); ?>" style="width: 250px" required onkeyup="this.value = this.value.toUpperCase();"/>*</td>
                <td class="bold right"><label for="Jkelamin">GENDER :</label></td>
                <td><select name="Jkelamin" id="Jkelamin" class="bold" style="width: 100px" required>
                        <option value="L" <?php print($member->Jkelamin == 'L' ? 'selected="selected"' : '');?>> LAKI - LAKI </option>
                        <option value="P" <?php print($member->Jkelamin == 'P' ? 'selected="selected"' : '');?>> PEREMPUAN </option>
                    </select>*
                </td>
                <td class="bold right"><label for="TglDaftar">TGL LAHIR :</label></td>
                <td><input type="text" class="bold" id="TglLahir" name="TglLahir" value="<?php print($member->FormatTglLahir(JS_DATE)); ?>" style="width: 100px" maxlength="10" required/>*</td>
                <td class="bold right"><label for="TglDaftar">T4 LAHIR :</label></td>
                <td><input type="text" class="bold" id="T4Lahir" name="T4Lahir" value="<?php print($member->T4Lahir); ?>" style="width: 150px"/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Alamat">ALAMAT LENGKAP :</label></td>
                <td colspan="2"><input type="text" class="bold" id="Alamat" name="Alamat" value="<?php print($member->Alamat); ?>" style="width: 250px" required/>*</td>
                <td class="bold right"><label for="RtRw">RT/RW/LK :</label></td>
                <td><input type="text" class="bold" id="RtRw" name="RtRw" value="<?php print($member->RtRw); ?>" style="width: 100px"/></td>
                <td class="bold right"><label for="Desa">DESA :</label></td>
                <td><input type="text" class="bold" id="Desa" name="Desa" value="<?php print($member->Desa); ?>" style="width: 100px"/></td>
                <td class="bold right"><label for="Desa">KECAMATAN :</label></td>
                <td><input type="text" class="bold" id="Kecamatan" name="Kecamatan" value="<?php print($member->Kecamatan); ?>" style="width: 150px"/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Pekerjaan">PEKERJAAN :</label></td>
                <td colspan="3"><input type="text" class="bold" id="Pekerjaan" name="Pekerjaan" value="<?php print($member->Pekerjaan); ?>" style="width: 150px" required onkeyup="this.value = this.value.toUpperCase();"/>
                    &nbsp;
                    <label class="bold" for="Agama">AGAMA :</label>
                    &nbsp;
                    <select name="Agama" id="Agama" class="bold" required>
                        <option value="KRISTEN" <?php print($member->Agama == 'KRISTEN' ? 'selected="selected"' : '');?>> KRISTEN </option>
                        <option value="KATOLIK" <?php print($member->Agama == 'KATOLIK' ? 'selected="selected"' : '');?>> KATOLIK </option>
                        <option value="ISLAM" <?php print($member->Agama == 'ISLAM' ? 'selected="selected"' : '');?>> ISLAM </option>
                        <option value="BUDHA" <?php print($member->Agama == 'BUDHA' ? 'selected="selected"' : '');?>> BUDHA </option>
                        <option value="HINDU" <?php print($member->Agama == 'HINDU' ? 'selected="selected"' : '');?>> HINDU </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2"><b><u>PENDAFTARAN AWAL:</u></b></td>
            </tr>
            <tr>
                <td class="bold right"><label for="NoStrukDaftar">NOMOR STRUK :</label></td>
                <td><input type="text" class="bold" id="NoStrukDaftar" name="NoStrukDaftar" value="<?php print($member->NoStrukDaftar); ?>" style="width: 150px" disabled/>*</td>
                <td class="bold right"><label for="NilaiBelanjaDaftar">NILAI BELANJA :</label></td>
                <td><input type="text" class="bold right" id="NilaiBelanjaDaftar" name="NilaiBelanjaDaftar" value="<?php print($member->NilaiBelanjaDaftar); ?>" style="width: 100px" disabled/></td>
                <td class="bold right"><label for="PoinAktif">JUMLAH POIN :</label></td>
                <td><input type="text" class="bold right" id="PoinAktif" name="PoinAktif" value="<?php print($member->PoinAktif); ?>" style="width: 100px" disabled/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="StatusMember">STATUS :</label></td>
                <td><select name="StatusMember" id="StatusMember" class="bold" style="width: 150px" required>
                        <option value="0" <?php print($member->StatusMember == 0 ? 'selected="selected"' : '');?>> 0 - Tidak Aktif </option>
                        <option value="1" <?php print($member->StatusMember == 1 ? 'selected="selected"' : '');?>> 1 - Aktif </option>
                    </select>*
                </td>
                <td class="bold right"><label for="KodePromoDaftar">KODE PROMO :</label></td>
                <td><input type="text" class="bold" id="KodePromoDaftar" name="KodePromoDaftar" value="<?php print($member->KodePromoDaftar); ?>" style="width: 100px" disabled/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="2"><button type="submit" class="button">UPDATE DATA</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.member")); ?>">DAFTAR MEMBER</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
<script type="text/javascript">
    $( function() {
        $("#TglDaftar").customDatePicker({ showOn: "focus" });
        $("#ExpDate").customDatePicker({ showOn: "focus" });
        $("#ExpIdCard").customDatePicker({ showOn: "focus" });
        $("#TglLahir").customDatePicker({ showOn: "focus" });
    });
</script>
</body>
</html>
