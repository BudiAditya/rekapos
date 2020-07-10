<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $invoice Invoice */ /** @var $sales Karyawan[] */
$counter = 0;
?>
<head>
    <title>REKASYS | Entry Nota Penjualan (Invoicing)</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
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
<div id="p" class="easyui-panel" title="Entry Nota Penjualan" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td>Cabang</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($invoice->CabangCode != null ? $invoice->CabangCode : $userCabCode); ?>" disabled/></td>
            <td>Tanggal</td>
            <td><input type="text" size="12" id="InvoiceDate" name="InvoiceDate" value="<?php print($invoice->FormatInvoiceDate(JS_DATE));?>" <?php print($itemsCount == 0 ? 'required' : 'readonly');?>/></td>
            <td>No. Invoice</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="InvoiceNo" name="InvoiceNo" value="<?php print($invoice->InvoiceNo != null ? $invoice->InvoiceNo : '-'); ?>" readonly/></td>
            <td>Status</td>
            <td><select class="easyui-combobox" id="InvoiceStatus" name="InvoiceStatus" style="width: 100px">
                    <option value="0" <?php print($invoice->InvoiceStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($invoice->InvoiceStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($invoice->InvoiceStatus == 2 ? 'selected="selected"' : '');?>>2 - Approved</option>
                    <option value="3" <?php print($invoice->InvoiceStatus == 3 ? 'selected="selected"' : '');?>>3 - Terbayar</option>
                    <option value="4" <?php print($invoice->InvoiceStatus == 4 ? 'selected="selected"' : '');?>>4 - Batal</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Customer</td>
            <td><input class="easyui-combogrid" id="CustomerId" name="CustomerId" style="width: 250px" value="<?php print($invoice->CustomerId); ?>" autofocus/>
                <input type="hidden" id="CabangId" name="CabangId" value="<?php print($invoice->CabangId == null ? $userCabId : $invoice->CabangId);?>"/>
                <input type="hidden" id="CustLevel" name="CustLevel" value="<?php print($invoice->CustLevel);?>"/>
                <input type="hidden" id="CreditLimit" name="CreditLimit" value="<?php print($creditLimit);?>"/>
                <input type="hidden" id="MaxInvOutstanding" name="MaxInvOutstanding" value="<?php print($maxInvOutstanding);?>"/>
            </td>
            <td>Salesman</td>
            <td><select class="easyui-combobox" id="SalesId" name="SalesId" style="width: 150px">
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
            <td><input class="easyui-combogrid" id="aExSoNo" name="aExSoNo" style="width: 150px" value="<?php print($invoice->ExSoNo); ?>"/>
                <input type="hidden" id="ExSoNo" name="ExSoNo" value="<?php print($invoice->ExSoNo); ?>"/>
                <input type="hidden" id="SoNilai" name="SoNilai"/>
            </td>
            <td>Jenis</td>
            <td><select class="easyui-combobox" id="InvoiceType" name="InvoiceType" style="width: 100px">
                    <option value="1" <?php print($invoice->InvoiceType == 1 ? 'selected="selected"' : '');?>>1 - Barang</option>
                    <option value="2" <?php print($invoice->InvoiceType == 2 ? 'selected="selected"' : '');?>>2 - Jasa</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td><b><input type="text" class="f1 easyui-textbox" id="InvoiceDescs" name="InvoiceDescs" style="width: 250px" value="<?php print($invoice->InvoiceDescs != null ? $invoice->InvoiceDescs : '-'); ?>" required/></b></td>
            <td>Gudang</td>
            <td>
                <?php if ($itemsCount == 0){?>
                    <select class="easyui-combobox" id="GudangId" name="GudangId" style="width: 150px" required>
                <?php }else{ ?>
                    <input type="hidden" name="GudangId" id="GudangId" value="<?php print($invoice->GudangId);?>"/>
                    <select class="easyui-combobox" id="GudangId" name="GudangId" style="width: 150px" disabled>
                <?php } ?>
                    <option value="">- Pilih Gudang -</option>
                        <?php
                        foreach ($gudangs as $gudang) {
                            if ($gudang->Id == $invoice->GudangId) {
                                printf('<option value="%d" selected="selected">%s</option>', $gudang->Id, $gudang->WhCode);
                            }else {
                                printf('<option value="%d">%s</option>', $gudang->Id, $gudang->WhCode);
                            }
                        }
                        ?>
                    </select>
            </td>
            <td>Cara Bayar</td>
            <td><select id="PaymentType" name="PaymentType" required>
                    <option value="1" <?php print($invoice->PaymentType == 1 ? 'selected="selected"' : '');?>>Kredit</option>
                    <option value="0" <?php print($invoice->PaymentType == 0 ? 'selected="selected"' : '');?>>Tunai</option>
                </select>
                &nbsp
                Kredit
                <input type="text" id="CreditTerms" name="CreditTerms" size="2" maxlength="5" value="<?php print($invoice->CreditTerms != null ? $invoice->CreditTerms : 0); ?>" style="text-align: right" required/>&nbsphari</td>
        </tr>
        <tr>
            <td colspan="9">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th colspan="12">DETAIL BARANG YANG DIJUAL</th>
                        <th rowspan="2">Action</th>
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
                        print("<tr class='bold'>");
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
                        print("<td class='center'>");
                        $dtx = addslashes($detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs).'|'.$detail->ItemId.'|'.$detail->Qty.'|'.$detail->SatBesar.'|'.$detail->Price.'|'.$detail->DiscFormula.'|'.$detail->DiscAmount.'|'.$detail->SubTotal.'|'.$detail->ItemNote.'|'.$detail->IsFree.'|'.$detail->ItemHpp.'|'.$detail->ExSoNo.'|'.$detail->TaxCode.'|'.$detail->TaxPct.'|'.$detail->TaxAmount);
                        printf('&nbsp<img src="%s" alt="Edit barang" title="Edit barang" style="cursor: pointer" onclick="return feditdetail(%s,%s);"/>',$bedit,"'".$dtx."'",$invoice->CustomerId);
                        printf('&nbsp<img src="%s" alt="Hapus barang" title="Hapus barang" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dtx."'");
                        print("</td>");
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
                            printf('<img src="%s" id="bKembali" alt="Daftar Invoice" title="Kembali ke daftar invoice" style="cursor: pointer;"/>',$bkembali);
                            ?>
                        </td>
                        <td colspan="2" align="right">Total Penjualan:</td>
                        <td class="right"><?php print(number_format($invoice->BaseAmount));?></td>
                        <td class="right"><?php print(number_format($invoice->Disc1Amount));?></td>
                        <td class="right"><?php print(number_format($invoice->TaxAmount));?></td>
                        <td class="right"><?php print(number_format($invoice->TotalAmount));?></td>
                        <?php if ($acl->CheckUserAccess("ar.invoice", "add")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Tambah Produk" title="Tambah Produk Detail" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016 - 2018  PT. Rekasystem Technology
</div>
<!-- Form Add/Edit Invoice Detail -->
<div id="dlg" class="easyui-dialog" style="width:800px;height:300px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="right">Ex Sales Order No:</td>
                <td colspan="7"><input class="easyui-combogrid" id="dExSoNo" name="dExSoNo" style="width:600px"/></td>
            </tr>
            <tr>
                <td class="right">Cari Data Produk:</td>
                <td colspan="7"><input class="easyui-combogrid" id="aItemSearch" name="aItemSearch" style="width:600px"/></td>
            </tr>
            <tr>
                <td class="right">Kode Produk:</td>
                <td colspan="7">
                    <input type="text" id="aItemCode" name="aItemCode" size="15" value="" required/>
                    <input type="hidden" id="aItemId" name="aItemId" value="0"/>
                    <input type="hidden" id="aId" name="aId" value="0"/>
                    <input type="hidden" id="aQtyStock" name="aQtyStock" value="0"/>
                    <input type="hidden" id="aItemHpp" name="aItemHpp" value="0"/>
                    <input type="hidden" id="aMode" name="aMode" value="0"/>
                    <input type="hidden" id="aQta" name="aQta" value="0"/>
                    <input type="hidden" id="aQtyOrder" name="aQtyOrder" value="0"/>
                    <input type="hidden" id="aIsStock" name="aIsStock" value="1"/>
                    <input type="hidden" id="aSoNo" name="aSoNo" value=""/>
                    <span style="color: red" class="blink"><b>**Ketik Kode Produk atau Scan BarCode agar lebih cepat**</b></span>
                </td>
            </tr>
            <tr>
                <td class="right">Nama Produk:</td>
                <td colspan="3"><input type="text" id="aItemDescs" name="aItemDescs" size="55" value="" disabled/></td>
                <td class="right">Satuan:</td>
                <td><input type="text" id="aSatuan" name="aSatuan" size="5" value="" disabled/></td>
            </tr>
            <tr>
                <td class="right">QTY:</td>
                <td><input class="right" type="text" id="aQty" name="aQty" size="5" value="0"/>
                    &nbsp; Bonus &nbsp; <input class="right" type="checkbox" id="aIsFree" name="aIsFree" value="0"/>
                </td>
                <td class="right">Harga:</td>
                <td><input class="right" type="text" id="aPrice" name="aPrice" size="12" value="0"/></td>
                <td class="right">Jumlah:</td>
                <td><input class="right bold" type="text" id="aSubTotal" name="aSubTotal" size="15" value="0" readonly/></td>
            </tr>
            <tr>
                <td class="right">Diskon Formula:</td>
                <td><input class="right" type="text" id="aDiscFormula" name="aDiscFormula" size="5" value="0"/>&nbsp;%</td>
                <td class="right">Nilai Diskon:</td>
                <td><input class="right" type="text" id="aDiscAmount" name="aDiscAmount" size="12" value="0"/></td>
                <td class="right">DPP:</td>
                <td><input class="right bold" type="text" id="aDpp" name="aDpp" size="15" value="0" readonly/></td>
            </tr>
            <tr>
                <td class="right">Jenis Pajak:</td>
                <td><select name="aTaxCode1" id="aTaxCode1" required>
                        <?php
                        /** @var $taxs Tax[] */
                        $dtx = null;
                        foreach ($taxs as $pajak){
                            $dtx = $pajak->TaxCode."|".$pajak->TaxRate;
                            if ($pajak->TaxRate == 0){
                                printf('<option value="%s" selected="selected"> %s </option>',$dtx,$pajak->TaxCode);
                            }else{
                                printf('<option value="%s"> %s </option>',$dtx,$pajak->TaxCode);
                            }
                        }
                        ?>
                    </select>
                    <input type="hidden" name="aTaxCode" id="aTaxCode" value="">
                    <input type="hidden" name="aTaxPct" id="aTaxPct" value="0">
                </td>
                <td class="right">Pajak:</td>
                <td><input class="right" type="text" id="aTaxAmount" name="aTaxAmount" size="12" value="0"/></td>
                <td class="right">Total:</td>
                <td><input class="right bold" type="text" id="aTotal" name="aTotal" size="15" value="0" readonly/></td>
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
        var userCabId,custId,custLevel,salesId,invoiceId,userCompId,userLevel,allowMinus,sumOutStanding, qtyOutStanding;
        userCabId = "<?php print($invoice->CabangId > 0 ? $invoice->CabangId : $userCabId);?>";
        custId = "<?php print($invoice->CustomerId);?>";
        custLevel = "<?php print($invoice->CustLevel > 0 ? $invoice->CustLevel : 0);?>";
        salesId = "<?php print($invoice->SalesId);?>";
        gudangId = "<?php print($invoice->GudangId > 0 ? $invoice->GudangId : 1);?>";
        invoiceId = "<?php print($invoice->Id);?>";
        userCompId = "<?php print($invoice->EntityId > 0 ? $invoice->EntityId : $userCompId);?>";
        userLevel = "<?php print($userLevel);?>";
        allowMinus = "<?php print($userCabAlMin);?>";
        sumOutStanding = 0;
        qtyOutStanding = 0;
        //var addetail = ["aItemSearch", "aItemCode", "aQty","aPrice", "aDiscFormula", "aDiscAmount", "aSubTotal", "bSaveDetail"];
        //BatchFocusRegister(addetail);
        //var addmaster = ["CabangId", "InvoiceDate","CustomerId", "SalesId", "InvoiceDescs", "PaymentType","CreditTerms","BaseAmount","Disc1Pct","Disc1Amount","TaxPct","TaxAmount","OtherCosts","OtherCostsAmount","TotalAmount","bUpdate","bKembali"];
        //BatchFocusRegister(addmaster);
        $("#InvoiceDate").customDatePicker({ showOn: "focus" });
        $('#GudangId').combobox({
            onChange: function(data){
                console.log(data);
                gudangId = data;
                var urz = "<?php print($helper->site_url("ar.invoice/getitemstock_json/"));?>"+gudangId;
                $('#aItemSearch').combogrid('grid').datagrid('load',urz);
            }
        });

        $('#CustomerId').combogrid({
            panelWidth:600,
            url: "<?php print($helper->site_url("master.contacts/getjson_contacts/1/".$userCompId));?>",
            idField:'id',
            textField:'contact_name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'contact_code',title:'Kode',width:40},
                {field:'contact_name',title:'Nama Customer',width:100},
                {field:'address',title:'Alamat',width:100},
                {field:'city',title:'Kota',width:60},
                {field:'contactlevel',title:'Level',width:20},
                {field:'credit_terms',title:'Terms',width:20}
            ]],
            onSelect: function(index,row){
                var urz = "";
                var cid = row.id;
                console.log(cid);
                custId = cid;
                var ctn = row.contact_name;
                var lvl = row.contactlevel;
                console.log(lvl);
                $('#CustLevel').val(lvl);
                var crt = row.credit_terms;
                console.log(crt);
                $('#CreditTerms').val(crt);
                var crl = row.creditlimit;
                console.log(crl);
                $('#CreditLimit').val(crl);
                custLevel = lvl;
                if (crt > 0){
                    $('#PaymentType').val(1);
                }else{
                    $('#PaymentType').val(0);
                }
                var mio = row.max_inv_outstanding;
                console.log(mio);
                $('#MaxInvOutstanding').val(mio);
                var ctd = row.credittodate;
                var qio = row.qty_inv_outstanding;
                if ((mio > 0) && (ctd > 0)){
                    swal({
                        title: "Perhatian:",
                        text: "Customer: "+ctn+" memiliki "+qio+" Outstanding Invoice, Senilai "+formatRupiah(ctd,"Rp.")+"\n\n Apakah tetap dilanjutkan?\n(* Nanti akan diperlukan approval khusus)",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                        .then((willLanjut) => {
                        if (!willLanjut) {
                            location.reload();
                        }else{
                            $("#SalesId").focus();
                        }
                    });
                }
                //var urz = "<?php //print($helper->site_url("ar.invoice/getitemstock_json/"));?>"+lvl+'/'+gudangId;
                //$('#aItemSearch').combogrid('grid').datagrid('load',urz);
                urz = "<?php print($helper->site_url('ar.invoice/getjson_solists/'.$userCabId.'/'));?>"+cid;
                $('#aExSoNo').combogrid('grid').datagrid('load',urz);
            }
        });

        $('#CustomerId').change(function(e){
            alert(this.value);
        });


        $('#aExSoNo').combogrid({
            panelWidth:350,
            url: "<?php print($helper->site_url('ar.order/getjson_solists/'.$userCabId));?>",
            idField:'so_no',
            textField:'so_no',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'so_no',title:'S/O No',width:55},
                {field:'so_date',title:'Tanggal',width:40},
                {field:'nilai',title:'Nilai Order',width:50,align:'right'}
            ]],
            onSelect: function(index,row){
                var idi = row.id;
                console.log(idi);
                var son = row.so_no;
                console.log(son);
                $("#ExSoNo").val(son);
                var snl = row.nilai;
                snl = snl.replace(/,/g,"");
                console.log(snl);
                $("#SoNilai").val(snl);
                if (son != '' && Number(snl) > 0) {
                    if (confirm('Proses Sales Order No: ' + son + ' ?')) {
                        prosesSalesOrder();
                    }
                }
            }
        });

        $('#dExSoNo').combogrid({
            panelWidth:600,
            url: "<?php print($helper->site_url('ar.order/getjson_solists/'.$userCabId));?>",
            idField:'so_no',
            textField:'so_no',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'so_no',title:'S/O No',width:55},
                {field:'so_date',title:'Tanggal',width:40},
                {field:'so_descs',title:'Keterangan',width:100},
                {field:'nilai',title:'Nilai Order',width:50,align:'right'}
            ]],
            onSelect: function(index,row){
                var idi = row.id;
                console.log(idi);
                var son = row.so_no;
                console.log(son);
                $("#aSoNo").val(son);
                if (son != '') {
                    var urz = "<?php print($helper->site_url('ar.invoice/getjson_soitems/'));?>"+son+'/'+gudangId;
                    $('#aItemSearch').combogrid('grid').datagrid('load',urz);
                }
            }
        });

        $('#aItemSearch').combogrid({
            panelWidth:700,
            url: "<?php print($helper->site_url("ar.invoice/getitemstock_json/"));?>"+gudangId,
            idField:'item_id',
            textField:'item_name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'item_code',title:'Kode',width:80},
                {field:'item_name',title:'Nama Produk',width:220},
                {field:'satuan',title:'Satuan',width:40},
                {field:'qty_stock',title:'Stock',width:40,align:'right'},
                {field:'qty_order',title:'Order',width:40,align:'right'},
                {field:'price',title:'Harga',width:40,align:'right'}
            ]],
            onSelect: function(index,row){
                var bid = row.item_id;
                console.log(bid);
                var bkode = row.item_code;
                console.log(bkode);
                var bnama = row.item_name;
                console.log(bnama);
                var satuan = row.satuan;
                console.log(satuan);
                var bqstock = row.qty_stock;
                console.log(bqstock);
                var bqorder = row.qty_order;
                console.log(bqorder);
                var isstock = row.is_stock;
                console.log(isstock);
                var issale = row.is_sale;
                console.log(issale);
                var bprice = row.price;
                console.log(bprice);
                var bqty = 1;
                if (bqorder > 0){
                    bqty = bqorder;
                }
                $('#aItemId').val(bid);
                $('#aItemCode').val(bkode);
                $('#aItemDescs').val(bnama);
                $('#aSatuan').val(satuan);
                $('#aQtyStock').val(bqstock);
                $('#aQtyOrder').val(bqorder);
                $('#aDiscFormula').val(0);
                $('#aDiscAmount').val(0);
                $('#aPrice').val(bprice);
                $('#aItemHpp').val(0);
                getItemPrice(bkode,custLevel,bqty);
                if(bqstock >= 0 || allowMinus == 1){
                    if (bqorder > 0){
                        $('#aQty').val(bqorder);
                    }else{
                        $('#aQty').val(1);
                    }
                    $('#aQty').focus();
                    hitDetail();
                }else{
                    $('#aQty').val(0);
                    //$('#aQty').focus();
                    if (isstock == 1) {
                        alert('Maaf, Stock tidak cukup!');
                    }
                }
            }
        });

        $("#aItemCode").change(function(e){
            //$ret = "OK|".$setprice->ItemId.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$setprice->QtyStock.'|'.$setprice->HrgBeli.'|'.$setprice->HrgJual1;
            var itc = $("#aItemCode").val();
            var lvl = $("#CustLevel").val();
            var cbi = $('#GudangId').combobox('getValue');
            var url = "<?php print($helper->site_url("ar.invoice/getitempricestock_plain/"));?>"+cbi+"/"+itc+"/"+lvl;
            if (itc != ''){
                $.get(url, function(data, status){
                    //alert("Data: " + data + "\nStatus: " + status);
                    if (status == 'success'){
                        var dtx = data.split('|');
                        if (dtx[0] == 'OK'){
                            $('#aItemId').val(dtx[1]);
                            $('#aItemDescs').val(dtx[2]);
                            $('#aSatuan').val(dtx[3]);
                            $('#aItemHpp').val(dtx[5]);
                            $('#aPrice').val(dtx[6]);
                            $('#aDiscFormula').val(0);
                            $('#aDiscAmount').val(0);
                            $('#aQtyStock').val(Number(dtx[4]));
                            if ((Number(dtx[4]) >= 0) || (allowMinus == 1)){
                                if ($('#aQty').val()=='' || Number($('#aQty').val())==0){
                                    $('#aQty').val(1);
                                }
                                hitDetail();
                                $('#aQty').focus();
                            }else{
                                $('#aQty').val(0);
                                alert('Maaf, Stock tidak cukup!');
                                $('#aQty').focus();
                            }
                        }else{
                            alert('Data Harga Produk ini tidak ditemukan!');
                        }
                    }else{
                        alert('Data Harga Produk ini tidak ditemukan!');
                    }
                });
            }
        });

        $("#aQty").change(function(e){
            var txm = Number($('#aMode').val());
            var stk = Number($('#aQtyStock').val());
            var qty = Number($('#aQty').val());
            var qta = Number($('#aQta').val());
            var itc = $("#aItemCode").val();
            if (qty > 0){
                getItemPrice(itc,custLevel,qty);
            }
            if (txm == 1){
                var gdi = $('#GudangId').combobox('getValue');
                var cbi = userCabId;
                var url = "<?php print($helper->site_url("ar.invoice/getStockQty/"));?>"+cbi+"/"+gdi+"/"+itc;
                $.get(url, function(data, status){
                    $('#aQtyStock').val(data);
                    stk = Number($('#aQtyStock').val());
                    stk = stk + qta;
                    if (stk > 0 && stk >= qty) {
                        hitDetail();
                    }else if (stk >= 0 && stk < qty){
                        if (confirm('Maaf, Stock tidak mencukupi!\nApakah tetap diproses?')){
                            hitDetail();
                        }else{
                            $('#aQty').val(0);
                        }
                    }else{
                        if (allowMinus == 1){
                            if (confirm('Maaf, Stock tidak mencukupi!\nApakah tetap diproses?')){
                                hitDetail();
                            }else{
                                $('#aQty').val(0);
                            }
                        }else {
                            alert('Maaf, Stock barang ini kosong!');
                            $('#aQty').val(0);
                        }
                    }
                });
            }else{
                if (stk > 0 && stk >= qty) {
                    hitDetail();
                }else if (stk >= 0 && stk < qty){
                    if (confirm('Maaf, Stock tidak mencukupi!\nApakah tetap diproses?')){
                        hitDetail();
                    }else{
                        $('#aQty').val(0);
                    }
                }else{
                    if (allowMinus == 1){
                        if (confirm('Maaf, Stock tidak mencukupi!\nApakah tetap diproses?')){
                            hitDetail();
                        }else{
                            $('#aQty').val(0);
                        }
                    }else {
                        alert('Maaf, Stock barang ini kosong!');
                        $('#aQty').val(0);
                    }
                }
            }
        });

        $("#aPrice").change(function(e){
            hitDetail();
        });

        $("#aDiscFormula").change(function(e){
            hitDetail();
        });

        $('#aIsFree').change(function () {
            if (this.checked){
                $('#aIsFree').val(1);
            }else{
                $('#aIsFree').val(0);
            }
            hitDetail();
        });

        $("#aTaxCode1").change(function(e){
            hitDetail();
        });

        $("#bTambah").click(function(){
            if (confirm('Buat invoice baru?')){
                location.href="<?php print($helper->site_url("ar.invoice/add")); ?>";
            }
        });

        $("#bHapus").click(function(){
            if (confirm('Anda yakin akan membatalkan invoice ini?')){
                location.href="<?php print($helper->site_url("ar.invoice/void/").$invoice->Id); ?>";
            }
        });

        $("#bCetakPdf").click(function(){
            var ivs = "<?php print($invoice->InvoiceStatus);?>";
            if (Number(ivs) == 2) {
                if (confirm('Cetak PDF invoice ini?')) {
                    window.open("<?php print($helper->site_url("ar.invoice/invoice_print/invoice/?&id[]=") . $invoice->Id); ?>");
                }
            }
        });

        $("#bKembali").click(function(){
            location.href="<?php print($helper->site_url("ar.invoice")); ?>";
        });

        $("#aItemSearch").keyup(function(event){
            if(event.keyCode == 13){
                $("#aQty").focus();
            }
        });

        $("#aSubTotal").keyup(function(event){
            if(event.keyCode == 13){
                $("#bSaveDetail").click();
            }
        });

        $("#bAdDetail").click(function(e){
            var csi = $("#CustomerId").combogrid("getValue");
            var sli = $("#SalesId").combobox("getValue");
            var gdi = $("#GudangId").combobox("getValue");
            var oke = 0;
            if (csi == 0 || csi == "" || csi == null){
                alert("Customer belum dipilih!");
                $("#CustomerId").focus();
            }else{
                if (sli == 0 || sli == "" || sli == null) {
                    alert("Salesman belum dipilih!");
                    $("#SalesId").focus();
                }else{
                    if (gdi == 0 || gdi == "" || gdi == null) {
                        alert("Gudang belum dipilih!");
                        $("#GudangId").focus();
                    }else{
                        oke = 1;
                    }
                }
            }
            if (oke == 1) {
                $('#aMode').val(0);
                $('#aItemId').val('');
                $('#aItemCode').val('');
                $('#aItemDescs').val('');
                $('#aItemNote').val('');
                $('#aSatuan').val('');
                $('#aSoNo').val('');
                $('#aPrice').val(0);
                $('#aQty').val(0);
                $('#aQtyOrder').val(0);
                $('#aDiscFormula').val('0');
                $('#aDiscAmount').val(0);
                $('#aSubTotal').val(0);
                $('#aIsFree').val(0);
                $('#aItemHpp').val(0);
                $('#aTaxCode').val("NOTAX");
                $('#aTaxPct').val(0);
                $('#aTaxAmount').val(0);
                newItem(custId);
            }
        });
    });

    function getItemPrice(itemCode,custLevel,qtySale){
        var url = "<?php print($helper->site_url("ar.invoice/getPriceByAreaQty/"));?>" + itemCode + "/" + custLevel + "/" + qtySale;
        $.get(url, function(data, status) {
            var dtz = data.split('|');
            $('#aPrice').val(dtz[1]);
            $('#aItemHpp').val(dtz[0]);
            hitDetail();
        });
    }

    function newItem(cid){
        $('#dlg').dialog('open').dialog('setTitle','Tambah Detail Produk yang dijual');
        //$('#fm').form('clear');
        var urz = "<?php print($helper->site_url('ar.invoice/getjson_solists/'.$userCabId.'/'));?>"+cid;
        $('#dExSoNo').combogrid('grid').datagrid('load',urz);
        url= "<?php print($helper->site_url("ar.invoice/add_detail/".$invoice->Id));?>";
        $('#aItemCode').focus();
    }

    function feditdetail(dta,cid){
        //$dtx = addslashes($detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs).'|'.$detail->ItemId.'|'.$detail->Qty.'|'.$detail->SatBesar.'|'.$detail->Price.'|'.$detail->DiscFormula.'|'.$detail->DiscAmount.'|'.$detail->SubTotal.'|'.$detail->ItemNote.'|'.$detail->IsFree.'|'.$detail->ItemHpp.'|'.$detail->ExSoNo.'|'.$detail->TaxCode.'|'.$detail->TaxPct.'|'.$detail->TaxAmount);
        var dtx = dta.split('|');
        $('#aMode').val(1);
        $('#aId').val(dtx[0]);
        $('#aItemId').val(dtx[3]);
        $('#aItemCode').val(dtx[1]);
        $('#aItemDescs').val(dtx[2]);
        $('#aSatuan').val(dtx[5]);
        $('#aPrice').val(dtx[6]);
        $('#aQty').val(dtx[4]);
        $('#aQta').val(dtx[4]);
        $('#aDiscFormula').val(dtx[7]);
        $('#aDiscAmount').val(dtx[8]);
        $('#aSubTotal').val(dtx[9]);
        $('#aItemNote').val(dtx[10]);
        $('#aIsFree').val(dtx[11]);
        $('#aItemHpp').val(dtx[12]);
        $('#aSoNo').val(dtx[13]);
        $('#aTaxCode').val(dtx[14]);
        $('#aTaxPct').val(dtx[15]);
        $('#aTaxAmount').val(dtx[16]);
        var isf = dtx[11];
        console.log(isf);
        if (isf == "1"){
            $('#aIsFree').attr("checked",true);
        }else{
            $('#aIsFree').attr("checked",false);
        }
        $("#aDpp").val(Number(dtx[9])-Number(dtx[8]))
        $("#aTotal").val((Number(dtx[9])-Number(dtx[8]))+Number(dtx[16]))
        var urz = "<?php print($helper->site_url('ar.invoice/getjson_solists/'.$userCabId.'/'));?>"+cid;
        $('#dExSoNo').combogrid('grid').datagrid('load',urz);
        $('#dlg').dialog('open').dialog('setTitle','Edit Detail Produk yang dijual');
        url= "<?php print($helper->site_url("ar.invoice/edit_detail/".$invoice->Id));?>";
    }

    function saveDetail(){
        //validasi master
        userCabId = "<?php print($invoice->CabangId > 0 ? $invoice->CabangId : $userCabId);?>";
        invoiceId = "<?php print($invoice->Id == null ? 0 : $invoice->Id);?>";
        salesId = $("#SalesId").combobox('getValue');
        gudangId = $("#GudangId").combobox('getValue');
        custId = $("#CustomerId").combobox('getValue');
        ivcType = $("#InvoiceType").combobox('getValue');
        var txm = Number($('#aMode').val());
        var aid = Number($('#aId').val());
        var aitd = Number($('#aItemId').val());
        var aqty = Number($('#aQty').val());
        var astt = Number($('#aSubTotal').val());
        var ahpp = Number($('#aItemHpp').val());
        var aint = $('#aItemNote').val();
        var asno = $('#aSoNo').val();
        //$('#checkbox').is(':checked');
        var aisf = 0;
        if ($('#aIsFree').is(':checked')){
            aisf = 1;
        }
        var oke = true;
        if ((userCabId > 0 && custId > 0 && salesId > 0) && ((aitd > 0 && aqty > 0 && astt > 0) || (aitd > 0 && aqty > 0 && aisf > 0))){
            // check credit limit disini
            var creditlimit = Number($('#CreditLimit').val());
            // credit limit = 0 berarti tidak terbatas limitnya
            if ($("#PaymentType").val()==1 && creditlimit > 0) {
                var urx = "<?php print($helper->site_url("master.contacts/get_credittodate/")); ?>" + custId;
                $.get(urx, function (data) {
                    var creditodate = data;
                    if ((creditlimit - creditodate - astt) <= 0){
                        alert('Maaf, Transaksi ini sudah melebihi limit kredit!');
                        oke = false;
                        location.href = "<?php print($helper->site_url("ar.invoice/add/")); ?>" + invoiceId;
                    }else{
                        if (confirm('Apakah data input sudah benar?')) {
                            var url = "<?php print($helper->site_url("ar.invoice/proses_master/")); ?>" + invoiceId;
                            //proses simpan dan update master
                            $.post(url, {
                                CabangId: userCabId,
                                GudangId: gudangId,
                                InvoiceDate: $("#InvoiceDate").val(),
                                InvoiceNo: $("#InvoiceNo").val(),
                                InvoiceDescs: $("#InvoiceDescs").val(),
                                CustomerId: custId,
                                CustLevel: $("#CustLevel").val(),
                                SalesId: salesId,
                                PaymentType: $("#PaymentType").val(),
                                CreditTerms: $("#CreditTerms").val(),
                                /*
                                BaseAmount: $("#BaseAmount").val(),
                                Disc1Pct: $("#Disc1Pct").val(),
                                Disc1Amount: $("#Disc1Amount").val(),
                                TaxPct: $("#TaxPct").val(),
                                TaxAmount: $("#TaxAmount").val(),
                                OtherCosts: $("#OtherCosts").val(),
                                OtherCostsAmount: $("#OtherCostsAmount").val(),
                                */
                                ExSoNo: $("#ExSoNo").val(),
                                InvoiceType: ivcType
                            }).done(function (data) {
                                var rst = data.split('|');
                                if (rst[0] == 'OK') {
                                    //validasi detail
                                    var aivi = rst[2];
                                    if ((aitd > 0 && aqty > 0 && astt > 0)||(aitd > 0 && aqty > 0 && aisf > 0)) {
                                        //proses simpan detail
                                        if (txm == 0){
                                            var urz = "<?php print($helper->site_url("ar.invoice/add_detail/")); ?>" + aivi;
                                        }else{
                                            var urz = "<?php print($helper->site_url("ar.invoice/edit_detail/")); ?>" + aivi;
                                        }
                                        $.post(urz, {
                                            aId: aid,
                                            aItemId: aitd,
                                            aQty: aqty,
                                            aPrice: Number($('#aPrice').val()),
                                            aDiscFormula: $('#aDiscFormula').val(),
                                            aDiscAmount: $('#aDiscAmount').val(),
                                            aSubTotal: astt,
                                            aItemHpp: ahpp,
                                            aItemNote: aint,
                                            aIsFree: aisf,
                                            aSoNo: asno,
                                            aTaxCode: $('#aTaxCode').val(),
                                            aTaxPct: $('#aTaxPct').val(),
                                            aTaxAmount: $('#aTaxAmount').val()
                                        }).done(function (data) {
                                            var rsx = data.split('|');
                                            if (rsx[0] == 'OK') {
                                                location.href = "<?php print($helper->site_url("ar.invoice/add/")); ?>" + aivi;
                                            } else {
                                                alert(data);
                                            }
                                        });
                                    } else {
                                        alert('[E2] Data Detail tidak valid!');
                                        location.href = "<?php print($helper->site_url("ar.invoice/add/")); ?>" + aivi;
                                    }
                                }
                            });
                        }
                    }
                });
            }else {
                if (confirm('Apakah data input sudah benar?')) {
                    var url = "<?php print($helper->site_url("ar.invoice/proses_master/")); ?>" + invoiceId;
                    //proses simpan dan update master
                    $.post(url, {
                        CabangId: userCabId,
                        GudangId: gudangId,
                        InvoiceDate: $("#InvoiceDate").val(),
                        InvoiceNo: $("#InvoiceNo").val(),
                        InvoiceDescs: $("#InvoiceDescs").val(),
                        CustomerId: custId,
                        CustLevel: $("#CustLevel").val(),
                        SalesId: salesId,
                        PaymentType: $("#PaymentType").val(),
                        CreditTerms: $("#CreditTerms").val(),
                        /*
                        BaseAmount: $("#BaseAmount").val(),
                        Disc1Pct: $("#Disc1Pct").val(),
                        Disc1Amount: $("#Disc1Amount").val(),
                        TaxPct: $("#TaxPct").val(),
                        TaxAmount: $("#TaxAmount").val(),
                        OtherCosts: $("#OtherCosts").val(),
                        OtherCostsAmount: $("#OtherCostsAmount").val(),
                        */
                        ExSoNo: $("#ExSoNo").val(),
                        InvoiceType: ivcType
                    }).done(function (data) {
                        var rst = data.split('|');
                        if (rst[0] == 'OK') {
                            //validasi detail
                            var aivi = rst[2];
                            if ((aitd > 0 && aqty > 0 && astt > 0)||(aitd > 0 && aqty > 0 && aisf > 0)) {
                                //proses simpan detail
                                if (txm == 0){
                                    var urz = "<?php print($helper->site_url("ar.invoice/add_detail/")); ?>" + aivi;
                                }else{
                                    var urz = "<?php print($helper->site_url("ar.invoice/edit_detail/")); ?>" + aivi;
                                }
                                $.post(urz, {
                                    aId: aid,
                                    aItemId: aitd,
                                    aQty: aqty,
                                    aPrice: Number($('#aPrice').val()),
                                    aDiscFormula: $('#aDiscFormula').val(),
                                    aDiscAmount: $('#aDiscAmount').val(),
                                    aSubTotal: astt,
                                    aItemHpp: ahpp,
                                    aItemNote: aint,
                                    aIsFree: aisf,
                                    aSoNo: asno,
                                    aTaxCode: $('#aTaxCode').val(),
                                    aTaxPct: $('#aTaxPct').val(),
                                    aTaxAmount: $('#aTaxAmount').val()
                                }).done(function (data) {
                                    var rsx = data.split('|');
                                    if (rsx[0] == 'OK') {
                                        location.href = "<?php print($helper->site_url("ar.invoice/add/")); ?>" + aivi;
                                    } else {
                                        alert(data);
                                    }
                                });
                            } else {
                                alert('[E3] Data Detail tidak valid!');
                                location.href = "<?php print($helper->site_url("ar.invoice/add/")); ?>" + aivi;
                            }
                        }
                    });
                }
            }
        }else{
            alert('[E1] Data Input tidak valid!');
        }
    }

    function prosesSalesOrder(){
        //validasi master
        userCabId = "<?php print($invoice->CabangId > 0 ? $invoice->CabangId : $userCabId);?>";
        invoiceId = "<?php print($invoice->Id == null ? 0 : $invoice->Id);?>";
        salesId = $("#SalesId").combobox('getValue');
        gudangId = $("#GudangId").combobox('getValue');
        custId = $("#CustomerId").combobox('getValue');
        ivcType = $("#InvoiceType").combobox('getValue');
        var oke = true;
        var urx = null;
        var noOrder = $('#ExSoNo').val();
        var creditLimit = Number($('#CreditLimit').val());
        var nilaiOrder = Number($('#SoNilai').val());
        if (userCabId > 0 && custId > 0 && salesId > 0 && nilaiOrder && noOrder != ''){
            // check credit limit disini
            // credit limit = 0 berarti tidak terbatas limitnya
            if ($("#PaymentType").val()==1 && creditLimit > 0) {
                urx = "<?php print($helper->site_url("master.contacts/get_credittodate/")); ?>" + custId;
                $.get(urx, function (data) {
                    var crediTodate = data;
                    if ((creditLimit - crediTodate - nilaiOrder) <= 0){
                        alert('Maaf, Transaksi ini sudah melebihi limit kredit!');
                        oke = false;
                        location.href = "<?php print($helper->site_url("ar.invoice/add/")); ?>" + invoiceId;
                    }else{
                        var url = "<?php print($helper->site_url("ar.invoice/proses_master/")); ?>" + invoiceId;
                        //proses simpan dan update master
                        $.post(url, {
                            CabangId: userCabId,
                            GudangId: gudangId,
                            InvoiceDate: $("#InvoiceDate").val(),
                            InvoiceNo: $("#InvoiceNo").val(),
                            InvoiceDescs: $("#InvoiceDescs").val(),
                            CustomerId: custId,
                            CustLevel: $("#CustLevel").val(),
                            SalesId: salesId,
                            PaymentType: $("#PaymentType").val(),
                            CreditTerms: $("#CreditTerms").val(),
                            BaseAmount: $("#BaseAmount").val(),
                            Disc1Pct: $("#Disc1Pct").val(),
                            Disc1Amount: $("#Disc1Amount").val(),
                            TaxPct: $("#TaxPct").val(),
                            TaxAmount: $("#TaxAmount").val(),
                            OtherCosts: $("#OtherCosts").val(),
                            OtherCostsAmount: $("#OtherCostsAmount").val(),
                            ExSoNo: $("#ExSoNo").val(),
                            InvoiceType: ivcType
                        }).done(function (data) {
                            var rst = data.split('|');
                            if (rst[0] == 'OK') {
                                //proses copy detail sales order disini
                                var invid = rst[1];
                                urx = "<?php print($helper->site_url("ar.invoice/prosesSalesOrder/")); ?>" + rst[2] + '/' + rst[3] + '/' +noOrder;
                                $.get(urx, function (data) {
                                    //alert(data);
                                    location.href = "<?php print($helper->site_url("ar.invoice/add/")); ?>" + invid;
                                });
                            }
                        });
                    }
                });
            }else {
                var url = "<?php print($helper->site_url("ar.invoice/proses_master/")); ?>" + invoiceId;
                //proses simpan dan update master
                $.post(url, {
                    CabangId: userCabId,
                    GudangId: gudangId,
                    InvoiceDate: $("#InvoiceDate").val(),
                    InvoiceNo: $("#InvoiceNo").val(),
                    InvoiceDescs: $("#InvoiceDescs").val(),
                    CustomerId: custId,
                    CustLevel: $("#CustLevel").val(),
                    SalesId: salesId,
                    PaymentType: $("#PaymentType").val(),
                    CreditTerms: $("#CreditTerms").val(),
                    BaseAmount: $("#BaseAmount").val(),
                    Disc1Pct: $("#Disc1Pct").val(),
                    Disc1Amount: $("#Disc1Amount").val(),
                    TaxPct: $("#TaxPct").val(),
                    TaxAmount: $("#TaxAmount").val(),
                    OtherCosts: $("#OtherCosts").val(),
                    OtherCostsAmount: $("#OtherCostsAmount").val(),
                    ExSoNo: $("#ExSoNo").val(),
                    InvoiceType: ivcType
                }).done(function (data) {
                    var rst = data.split('|');
                    if (rst[0] == 'OK') {
                        //proses copy detail sales order disini
                        var invid = rst[2];
                        urx = "<?php print($helper->site_url("ar.invoice/prosesSalesOrder/")); ?>" + rst[2] + '/' + rst[3] + '/' +noOrder;
                        $.get(urx, function (data) {
                            //alert(data);
                            location.href = "<?php print($helper->site_url("ar.invoice/add/")); ?>" + invid;
                        });
                    }
                });
            }
        }else{
            alert('[E1] Data Input tidak valid!');
        }
    }

    function hitDetail(){
        var txd = $("#aTaxCode1").val().split('|');
        var isFree = Number($("#aIsFree").val());
        var subTotal = 0;
        var discAmount = 0;
        var dpp = 0;
        var txa = 0;
        var totalDetail = 0;
        if (isFree == 0){
            subTotal = (Number($("#aQty").val()) * Number($("#aPrice").val()));
            discAmount = hitDiscFormula(subTotal,$("#aDiscFormula").val());
            dpp = subTotal - discAmount;
            if (dpp > 0 && Number(txd[1]) > 0){
                txa = Math.round(dpp * (Number(txd[1])/100));
            }
            totalDetail = dpp + txa;
        }
        $('#aDiscAmount').val(discAmount);
        $('#aTaxCode').val(txd[0]);
        $('#aTaxPct').val(txd[1]);
        $('#aTaxAmount').val(txa);
        $('#aDpp').val(dpp);
        $('#aSubTotal').val(subTotal);
        $('#aTotal').val(totalDetail);
    }

    function fdeldetail(dta){
        var dtz = dta.replace(/\"/g,"\\\"")
        var dtx = dtz.split('|');
        var id = dtx[0];
        var kode = dtx[1];
        var barang = dtx[2];
        var urx = '<?php print($helper->site_url("ar.invoice/delete_detail/"));?>'+id;
        if (confirm('Hapus Data Detail Produk \nKode: '+kode+ '\nNama: '+barang+' ?')) {
            $.get(urx, function(data){
                alert(data);
                location.reload();
            });
        }
    }

    function hitDiscFormula(nAmount,dFormula){
        var retVal = 0;
        if (nAmount > 0 && dFormula != '' && dFormula != '0'){
            var aFormula = dFormula.split('+');
            var nDiscount = 0;
            var pDiscount = 0;
            for (var i = 0;i < aFormula.length; i++){
                pDiscount = aFormula[i];
                nDiscount = Math.round(nAmount * (pDiscount)/100,0);
                retVal+= nDiscount;
                nAmount-= retVal;
            }
        }
        return retVal;
    }

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix){
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split   		= number_string.split(','),
            sisa     		= split[0].length % 3,
            rupiah     		= split[0].substr(0, sisa),
            ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if(ribuan){
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
</script>
</body>
</html>
