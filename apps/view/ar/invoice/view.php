<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $invoice Invoice */ /** @var $sales Karyawan[] */
?>
<head>
<title>REKASYS | View Nota Penjualan (Invoicing)</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>

<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>

<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>

<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>

<script type="text/javascript" src="<?php print($helper->path("public/js/sweetalert.min.js")); ?>"></script>

<style scoped>
    .f1{
        width:200px;
    }
</style>
<style type="text/css">
    #fd{
        margin:0;
        padding:5px 10px;
    }
    .ftitle{
        font-size:14px;
        font-weight:bold;
        padding:5px 0;
        margin-bottom:10px;
        border-bottom:1px solid #ccc;
    }
    .fitem{
        margin-bottom:5px;
    }
    .fitem label{
        display:inline-block;
        width:100px;
    }
    .numberbox .textbox-text{
        text-align: right;
        color: blue;
    }
</style>
</head>
<body>
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
<div id="p" class="easyui-panel" title="View Nota Penjualan" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td>Cabang</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($invoice->CabangCode != null ? $invoice->CabangCode : $userCabCode); ?>" disabled/>
                <input type="hidden" id="CabangId" name="CabangId" value="<?php print($invoice->CabangId == null ? $userCabId : $invoice->CabangId);?>"/>
            </td>
            <td>Tanggal</td>
            <td><input type="text" size="12" id="InvoiceDate" name="InvoiceDate" value="<?php print($invoice->FormatInvoiceDate(JS_DATE));?>" readonly/></td>
            <td>No. Invoice</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="InvoiceNo" name="InvoiceNo" value="<?php print($invoice->InvoiceNo != null ? $invoice->InvoiceNo : '-'); ?>" readonly/></td>
            <td>Status</td>
            <td><select class="easyui-combobox" id="InvoiceStatus" name="InvoiceStatus" style="width: 100px" disabled>
                    <option value="0" <?php print($invoice->InvoiceStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($invoice->InvoiceStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($invoice->InvoiceStatus == 2 ? 'selected="selected"' : '');?>>2 - Approved</option>
                    <option value="3" <?php print($invoice->InvoiceStatus == 3 ? 'selected="selected"' : '');?>>3 - Terbayar</option>
                    <option value="4" <?php print($invoice->InvoiceStatus == 3 ? 'selected="selected"' : '');?>>4 - Batal</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Customer</td>
            <td><input class="easyui-combogrid" id="CustomerId" name="CustomerId" style="width: 250px" value="<?php print($invoice->CustomerId); ?>" disabled/></td>
            <td>Salesman</td>
            <td><select class="easyui-combobox" id="SalesId" name="SalesId" style="width: 150px" disabled>
                    <option value="">- Pilih Salesman -</option>
                    <?php
                    foreach ($sales as $salesman) {
                        if ($salesman->Id == $invoice->SalesId) {
                            printf('<option value="%d" selected="selected">%s</option>', $salesman->Id, $salesman->Nama);
                        } else {
                            printf('<option value="%d">%s</option>', $salesman->Id, $salesman->Nama);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Ex. SO No</td>
            <td><input class="easyui-combogrid" id="ExSoNo" name="ExSoNo" style="width: 150px" value="<?php print($invoice->ExSoNo); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td><b><input type="text" class="f1 easyui-textbox" id="InvoiceDescs" name="InvoiceDescs" style="width: 250px" value="<?php print($invoice->InvoiceDescs != null ? $invoice->InvoiceDescs : '-'); ?>" readonly/></b></td>
            <td>Gudang</td>
            <td><select class="easyui-combobox" id="GudangId" name="GudangId" style="width: 150px" disabled>
                    <?php
                    foreach ($gudangs as $gudang) {
                        if ($gudang->Id == $invoice->GudangId) {
                            printf('<option value="%d" selected="selected">%s</option>', $gudang->Id, $gudang->Kode);
                        } else {
                            printf('<option value="%d">%s</option>', $gudang->Id, $gudang->Kode);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Cara Bayar</td>
            <td><select class="easyui-combobox" id="PaymentType" name="PaymentType" disabled>
                    <option value="1" <?php print($invoice->PaymentType == 1 ? 'selected="selected"' : '');?>>Kredit</option>
                    <option value="0" <?php print($invoice->PaymentType == 0 ? 'selected="selected"' : '');?>>Tunai</option>
                </select>
                &nbsp
                Kredit
                <input type="text" id="CreditTerms" name="CreditTerms" size="2" maxlength="5" value="<?php print($invoice->CreditTerms != null ? $invoice->CreditTerms : 0); ?>" style="text-align: right" readonly/>&nbsphari</td>
        </tr>
        <tr>
            <td colspan="9">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th colspan="12">DETAIL BARANG</th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>S/O No.</th>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Bonus</th>
                        <th>Jumlah</th>
                        <th>Diskon</th>
                        <th>Pajak</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    $counter = 0;
                    $total = 0;
                    $dta = null;
                    $dtx = null;
                    foreach($invoice->Details as $idx => $detail) {
                        $counter++;
                        print("<tr>");
                        printf('<td class="right">%s.</td>', $counter);
                        printf('<td>%s</td>', $detail->ExSoNo);
                        printf('<td>%s</td>', $detail->ItemCode);
                        printf('<td>%s</td>', $detail->ItemDescs);
                        printf('<td class="right">%s</td>', number_format($detail->Qty,0));
                        printf('<td>%s</td>', $detail->SatKecil);
                        printf('<td class="right">%s</td>', number_format($detail->Price,0));
                        if($detail->IsFree == 0){
                            print("<td class='center'><input type='checkbox' disabled></td>");
                        }else{
                            print("<td class='center'><input type='checkbox' checked='checked' disabled></td>");
                        }
                        printf('<td class="right">%s</td>', number_format($detail->SubTotal,0));
                        printf('<td class="right">%s</td>', number_format($detail->DiscAmount,0));
                        printf('<td class="right">%s</td>', number_format($detail->TaxAmount,0));
                        printf('<td class="right">%s</td>', number_format($detail->SubTotal+$detail->TaxAmount-$detail->DiscAmount,0));
                        print("</tr>");
                        $total += $detail->SubTotal;
                    }
                    ?>
                    <tr class="bold">
                        <td colspan="6">
                            <?php
                            if ($acl->CheckUserAccess("ar.invoice", "add")) {
                                printf('<img src="%s" alt="Invoice Baru" title="Buat invoice baru" id="bTambah" style="cursor: pointer;"/>&nbsp;&nbsp;',$baddnew);
                            }
                            if ($acl->CheckUserAccess("ar.invoice", "delete")) {
                                printf('<img src="%s" alt="Hapus Invoice" title="Proses hapus invoice" id="bHapus" style="cursor: pointer;"/>&nbsp;&nbsp;',$bdelete);
                            }
                            if ($acl->CheckUserAccess("ar.invoice", "print")) {
                                printf('<img src="%s" id="bCetak" alt="Cetak Invoice" title="Proses cetak invoice" style="cursor: pointer;"/>&nbsp;&nbsp;',$bcetak);
                            }
                            if ($invoice->InvoiceStatus == 1){
                                printf('<img src="%s" alt="Edit Invoice" title="Proses edit invoice" id="bEdit" style="cursor: pointer;"/>&nbsp;&nbsp;',$bedit);
                                print('<button id="bApproveSpecial"><b>*Approval Invoice*</b></button>&nbsp;&nbsp;');
                            }
                            printf('<img src="%s" id="bKembali" alt="Daftar Invoice" title="Kembali ke daftar invoice" style="cursor: pointer;"/>',$bkembali);
                            ?>
                        </td>
                        <td colspan="2" align="right">Total Penjualan:</td>
                        <td class="right"><?php print(number_format($invoice->BaseAmount));?></td>
                        <td class="right"><?php print(number_format($invoice->Disc1Amount));?></td>
                        <td class="right"><?php print(number_format($invoice->TaxAmount));?></td>
                        <td class="right"><?php print(number_format($invoice->TotalAmount));?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016 - 2018  PT. Rekasystem Technology
</div>
<div id="mdApproval" class="easyui-dialog" style="width:410px;height:200px;padding:3px 3px"
     closed="true" buttons="#dlg-buttons">
    <form id="frmApprove" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td colspan="2">Invoice <b><?php print($invoice->InvoiceNo);?></b> disetujui oleh:</td>
            </tr>
            <tr>
                <td class="bold right">User ID</td>
                <td><input type="text" class="bold" id="userId" name="userId" size="20" required/></td>
            </tr>
            <tr>
                <td class="bold right">Password</td>
                <td><input type="password" class="bold" id="userPasswd" name="userPasswd" size="20" required/></td>
            </tr>
            <tr>
                <td class="bold right">Alasan</td>
                <td><input type="text" class="bold" id="approveReason" name="approveReason" size="40" maxlength="50" required/></td>
            </tr>
        </table>
    </form>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" id="btApprove" onclick="saveApproval()" style="width:90px">Setuju</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" id="btClose" onclick="javascript:$('#mdApproval').dialog('close')" style="width:90px">Batal</a>
</div>
<script type="text/javascript">
    $( function() {
        var aprdetail = ["userId","userPasswd","approveReason","btApprove","btClose"];
        BatchFocusRegister(aprdetail);
        $('#CustomerId').combogrid({
            panelWidth:600,
            url: "<?php print($helper->site_url("master.contacts/getjson_contacts/1"));?>",
            idField:'id',
            textField:'contact_name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'contact_code',title:'Kode',width:30},
                {field:'contact_name',title:'Nama Customer',width:100},
                {field:'address',title:'Alamat',width:100},
                {field:'city',title:'Kota',width:60}
            ]]
        });

        $("#bTambah").click(function(){
            if (confirm('Buat invoice baru?')){
                location.href="<?php print($helper->site_url("ar.invoice/add/0")); ?>";
            }
        });

        $("#bEdit").click(function(){
            if (confirm('Anda yakin akan mengubah invoice ini?')){
                location.href="<?php print($helper->site_url("ar.invoice/add/").$invoice->Id); ?>";
            }
        });

        $("#bHapus").click(function(){
            if (confirm('Anda yakin akan membatalkan invoice ini?')){
                location.href="<?php print($helper->site_url("ar.invoice/void/").$invoice->Id); ?>";
            }
        });

        $("#bCetak").click(function(){
            var ivs = "<?php print($invoice->InvoiceStatus);?>";
            if (Number(ivs) == 2) {
                window.open("<?php print($helper->site_url("ar.invoice/printhtml/") . $invoice->Id); ?>");
            }
        });

        $("#bKembali").click(function(){
            location.href="<?php print($helper->site_url("ar.invoice")); ?>";
        });

        $("#bApproveSpecial").click(function(){
            $('#mdApproval').dialog('open').dialog('setTitle','Proses Approval Invoice');
            $('#userId').val("");
            $('#userPasswd').val("");
            $('#approveReason').val("");
            $('#userId').focus();
        });
    });

    function saveApproval(){
        var auid = $("#userId").val();
        var aupw = $("#userPasswd").val();
        var arsn = $("#approveReason").val();
        var aiid = "<?php print($invoice->Id);?>";
        if (auid == ''){
            swal({
                title: "Perhatian",
                text: "Data User ID harus diisi!",
                icon: "warning",
            });
            $("#userId").focus();
        }else if (aupw == ''){
            swal({
                title: "Perhatian",
                text: "Password harus diisi!",
                icon: "warning",
            });
            $("#userPasswd").focus();
        }else if (arsn == ''){
            swal({
                title: "Perhatian",
                text: "Alasan persetujuan harus diisi!",
                icon: "warning",
            });
            $("#approveReason").focus();
        }else{
            //swal("Proses Persetujuan..");
            var url = "<?php print($helper->site_url("ar.invoice/approvespecial")); ?>";
            $.post(url,{
                invoiceId: aiid,
                userId: auid,
                userPasswd: aupw,
                approveReason: arsn
            }).done(function (data)
            {
                var dtx = data.split('|');
                var rst = dtx[0];
                var jns = Number(dtx[1]);
                var msg = null;
                if (rst == 'OK'){
                    swal({
                        title: "Selamat",
                        text: "Proses Persetujuan invoice berhasil!",
                        icon: "success",
                        buttons: true,
                    })
                        .then((willDelete) => {
                        if (willDelete) {
                            location.href="<?php print($helper->site_url("ar.invoice")); ?>";
                        }else{
                            location.reload();
                        }
                    });
                }else{
                    switch(jns) {
                        case 1:
                            msg = "User ID tidak valid!";
                            break;
                        case 2:
                            msg = "User Password salah!";
                            break;
                        case 3:
                            msg = "Hak Akses -approval- tidak diijinkan!";
                            break;
                        case 4:
                            msg = "Invoice tidak valid!";
                            break;
                        default:
                            //msg = data+" - Tidak ada data yang diproses!";
                            msg = "Tidak ada data yang diproses!";
                    }
                    swal({
                        title: "Perhatian",
                        text: "Maaf, "+msg,
                        icon: "warning",
                    });
                }
            });
        }
    }

    function openWindow() {
        thisWindow = window.open('<?php print($helper->site_url("ar.invoice/printdirect/").$invoice->Id); ?>', "_blank", "toolbar=no,scrollbars=no,resizable=no,top=300,left=300,width=300,height=100");
    }

    function closeWindow(){
        thisWindow.close();
    }

</script>
</body>
</html>
