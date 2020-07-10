<!DOCTYPE HTML>
<html>
<head>
	<title>REKAPOS - Input Loyalty Program</title>
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
	<legend><span class="bold">Input Data Loyalty Program</span></legend>
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td class="bold right"><label for="StartDate">Mulai Tgl :</label></td>
            <td><input type="text" id="StartDate" name="StartDate" value="<?php print($loyalty->FormatStartDate(JS_DATE)); ?>" size="10" required/></td>
            <td class="bold right"><label for="EndDate">S/D Tgl :</label></td>
            <td><input type="text" id="EndDate" name="EndDate" value="<?php print($loyalty->FormatEndDate(JS_DATE)); ?>" size="10" required/></td>
            <td class="bold right"><label for="LoyaltyCode">Kode :</label></td>
            <td><input type="text" id="LoyaltyCode" name="LoyaltyCode" value="<?php print($loyalty->LoyaltyCode); ?>" size="20" readonly placeholder="AUTO"/></td>
        </tr>
        <tr>
            <td class="bold right"><label for="ProgramName">Nama Program :</label></td>
            <td colspan="4"><input type="text" id="ProgramName" name="ProgramName" value="<?php print($loyalty->ProgramName); ?>" size="50" required placeholder="Diisi Nama Program Loyalty Berhadiah"/>
                &nbsp;
                <label class="bold right" for="Lstatus">Status :</label></td>
            <td><select name="Lstatus" id="Lstatus" required>
                    <option value="0" <?php print($loyalty->Lstatus == 0 ? 'selected="selected"' : '');?>> 0 - Non-Aktif </option>
                    <option value="1" <?php print($loyalty->Lstatus == 1 ? 'selected="selected"' : '');?>> 1 - Aktif </option>
                </select>
                <?php
                if ($acl->CheckUserAccess("ar.invoice", "edit") && $loyalty->LoyaltyCode <> '') {
                    printf('<img src="%s" alt="Update" title="Update" id="bUpdate" onclick="updateMaster()" style="cursor: pointer;"/>', $bsubmit);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th colspan="5">RINCIAN HADIAH</th>
                        <?php if ($acl->CheckUserAccess("ar.invoice", "add")) { ?>
                            <th rowspan="2" class='center'><?php printf('<img src="%s" alt="Tambah Hadiah" title="Tambah Hadiah Detail" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></th>
                        <?php }else{ ?>
                            <th rowspan="2">Action</th>
                        <?php } ?>
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
                        printf('<td><img src="%s" alt="Hapus" title="Hapus" style="cursor: pointer" onclick="return delDetail(%d);"/></td>',$bclose,$detail->Id);
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
<!-- Form Add/Edit Invoice Detail -->
<div id="dlg" class="easyui-dialog" style="width:600px;height:150px;padding:5px 5px" closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="bold right">Nama Hadiah :</td>
                <td colspan="3"><input name="aHadiah" id="aHadiah" class="bold" size="40" required></td>
                <td class="bold right">QTY :</td>
                <td><input name="aQty" id="aQty" class="bold right" size="3" value="1" required></td>
            </tr>
            <tr>
                <td class="bold right">Minimum Poin :</td>
                <td><input name="aMinPoin" id="aMinPoin" class="bold right" size="8" value="0" required></td>
                <td class="bold right">Nilai Hadiah :</td>
                <td><input name="aNilai" id="aNilai" class="bold right" size="13" value="0" required>
                    <input name="aLoyaltyCode" id="aLoyaltyCode" type="hidden">
                    <input type="hidden" id="aId" name="aId" value="0"/>
                    <input type="hidden" id="aMode" name="aMode" value="0"/>
                </td>
            </tr>
        </table>
    </form>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDetail()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
<script type="text/javascript">
    $( function() {
        //datepicker
        $("#StartDate").customDatePicker({ showOn: "focus" });
        $("#EndDate").customDatePicker({ showOn: "focus" });

        $("#bAdDetail").click(function(e){
            var prn = $("#ProgramName").val();
            if (prn == ''){
                alert("Nama Program belum diisi!");
                $("#ProgramName").focus();
            }else{
                newItem();
            }
        });

    });

    function newItem(){
        $('#dlg').dialog('open').dialog('setTitle','Input Detail Hadiah');
        $('#fm').form('clear');
        $('#aHadiah').val('');
        $('#aQty').val(1);
        $('#aMinPoin').val(0);
        $('#aNilai').val(0);
        $('#aMode').val(1); //add new
        $('#aHadiah').focus();
    }

    function updateMaster() {
        //master
        var std = $("#StartDate").val();
        var end = $("#EndDate").val();
        var kde = $("#LoyaltyCode").val();
        var npr = $("#ProgramName").val();
        var sts = $("#Lstatus").val();
        var urm = "<?php print($helper->site_url("master.loyalty/add_master/".$loyalty->Id));?>";
        $.post(urm, {
            LoyaltyCode: kde,
            StartDate: std,
            EndDate: end,
            ProgramName: npr,
            Lstatus: sts
        }).done(function (data) {
            var rst = data.split('|');
            if (rst[0] == 'OK') {
               alert("Update Data berhasil!");
               location.reload();
            }
        });
    }
    
    function saveDetail() {
        //validasi
        //master
        var std = $("#StartDate").val();
        var end = $("#EndDate").val();
        var kde = $("#LoyaltyCode").val();
        var npr = $("#ProgramName").val();
        var sts = $("#Lstatus").val();
        var urm = "<?php print($helper->site_url("master.loyalty/add_master/".$loyalty->Id));?>";
        //detail
        var hdh = $("#aHadiah").val();
        var qty = $("#aQty").val();
        var mpn = $("#aMinPoin").val();
        var nhd = $("#aNilai").val();
        if (hdh != '' && qty > 0 && mpn > 0 && nhd > 0){
            if (confirm("Apakah data sudah benar?")){
                //if (kde == '' || kde == null){
                    //master belum tersave
                    //proses simpan dan update master
                    $.post(urm, {
                        LoyaltyCode: kde,
                        StartDate: std,
                        EndDate: end,
                        ProgramName: npr,
                        Lstatus: sts
                    }).done(function (data) {
                        var rst = data.split('|');
                        var lyi = rst[2];
                        var lyc = rst[3];
                        if (rst[0] == 'OK') {
                            var urd = "<?php print($helper->site_url("master.loyalty/add_detail/"));?>"+lyi;
                            $.post(urd, {
                                LoyaltyId: lyi,
                                LoyaltyCode: lyc,
                                Hadiah: hdh,
                                MinPoin: mpn,
                                Qty: qty,
                                Nilai: nhd
                            }).done(function (data) {
                                var rsx = data.split('|');
                                if (rsx[0] == 'OK') {
                                    location.href = "<?php print($helper->site_url("master.loyalty/add/")); ?>" + lyi;
                                } else {
                                    alert(data);
                                }
                            });
                        }
                    });
                //}
            }
        }else{
            alert("Data tidak valid!");
        }
    }

    function delDetail(id) {
        var urx = "<?php print($helper->site_url("master.loyalty/del_detail/"));?>"+id;
        if (confirm('Hapus Data Hadiah ini?')) {
            $.get(urx, function(data){
                alert(data);
                location.reload();
            });
        }
    }
</script>
</body>
</html>
