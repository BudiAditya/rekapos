<!DOCTYPE HTML>
<html>
<head>
    <title>REKAPOS - Input Poin Member</title>
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
    <legend><span class="bold">PENGINPUTAN POIN MANUAL</span></legend>
    <form action="<?php print($helper->site_url("master.member/addpoin/".$member->Id)); ?>" method="post">
        <table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0;">
            <tr>
                <td class="bold right"><label for="Nama">NAMA LENGKAP :</label></td>
                <td colspan="3"><input type="text" class="bold" id="Nama" name="Nama" value="<?php print($member->Nama); ?>" style="width: 400px" disabled/></td>
                <td class="bold right"><label for="StatusMember">STATUS :</label></td>
                <td><select name="StatusMember" id="StatusMember" class="bold" style="width: 130px" disabled>
                        <option value="0" <?php print($member->StatusMember == 0 ? 'selected="selected"' : '');?>> 0 - Tidak Aktif </option>
                        <option value="1" <?php print($member->StatusMember == 1 ? 'selected="selected"' : '');?>> 1 - Aktif </option>
                    </select>*
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="NoMember">NOMOR KARTU :</label></td>
                <td><input type="text" class="bold" id="NoMember" name="NoMember" value="<?php print($member->NoMember); ?>" style="width: 150px" disabled/></td>
                <td class="bold right"><label for="TglDaftar">TGL DAFTAR :</label></td>
                <td><input type="text" class="bold" id="TglDaftar" name="TglDaftar" value="<?php print($member->FormatTglDaftar(JS_DATE)); ?>" style="width: 100px" maxlength="10" disabled/></td>
                <td class="bold right"><label for="ExpDate" class="bold">TGL EXPIRE :</label></td>
                <td><input type="text" class="bold" id="ExpDate" name="ExpDate" value="<?php print($member->FormatExpDate(JS_DATE)); ?>" style="width: 130px" disabled/>*</td>
            </tr>
            <tr>
                <td class="bold right"><label for="NoIdCard">NOMOR KTP / SIM :</label></td>
                <td><input type="text" class="bold" id="NoIdCard" name="NoIdCard" value="<?php print($member->NoIdCard); ?>" style="width: 150px" disabled/>*</td>
                <td class="bold right"><label for="ExpIdCard">MASA BERLAKU :</label></td>
                <td><input type="text" class="bold" id="ExpIdCard" name="ExpIdCard" value="<?php print($member->FormatExpIdCard(JS_DATE)); ?>" style="width: 100px" maxlength="10" disabled/></td>
                <td class="bold right"><label for="NoHp" class="bold">NOMOR HP :</label></td>
                <td><input type="text" class="bold" id="NoHp" name="NoHp" value="<?php print($member->NoHp); ?>" style="width: 130px" disabled/>*</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><b><u>INFORMASI STRUK BELANJA:</u></b></td>
            </tr>
            <tr>
                <td class="bold right"><label for="NoStruk">NOMOR STRUK :</label></td>
                <td><input type="text" class="bold" id="NoStruk" name="NoStruk" value="" style="width: 150px" required/>*</td>
                <td class="bold right"><label for="NilaiBelanja">NILAI BELANJA :</label></td>
                <td><input type="text" class="bold right" id="NilaiBelanja" name="NilaiBelanja" value="0" style="width: 100px" readonly/></td>
                <td class="bold right"><label for="Poin">JUMLAH POIN :</label></td>
                <td><input type="text" class="bold right" id="Poin" name="Poin" value="0" style="width: 100px" readonly/>
                    <input type="hidden" name="CabangId" id="CabangId" value="0"/>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="KodePromo">KODE PROMO :</label></td>
                <td><input type="text" class="bold" id="KodePromo" name="KodePromo" value="" style="width: 150px" readonly/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="2"><button type="submit" class="button">SIMPAN DATA</button>
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

        //Deteksi nomor struk dan ambil nilainya
        $("#NoStruk").change(function(e){
            var tgd = $("#TglDaftar").val();
            var url = "<?php print($helper->site_url("master.member/getposdatapoin/"));?>"+this.value+'/'+tgd;
            $.get(url, function(data, status){
                if (status == 'success') {
                    var dtx = data.split('|');
                    if (dtx[0] == 'OK') {
                        $("#NilaiBelanja").val(dtx[1])
                        $("#Poin").val(dtx[2])
                        $("#KodePromo").val(dtx[3])
                        $("#CabangId").val(dtx[4])
                    }else{
                        if(dtx[1] == 1){
                            alert("Data Struk sudah terpakai!");
                        }else if (dtx[1] == 2){
                            alert("Data Promo tidak ditemukan atau belum disetting!");
                        }else if (dtx[1] == 3){
                            alert("Nilai Belanja tidak cukup untuk mendapat poin!");
                        }else{
                            alert("Data Struk tidak ditemukan!");
                        }
                        $("#NoStruk").val('');
                        $("#NilaiBelanja").val(0)
                        $("#Poin").val(0)
                        $("#KodePromo").val('')
                    }
                }
            })
        });
    });
</script>
</body>
</html>
