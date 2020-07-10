<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $purchase Purchase */
?>
<head>
    <title>REKASYS - Edit Pembelian/Penerimaan Barang</title>
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
            bpurchase-bottom:1px solid #ccc;
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
    <script type="text/javascript">
        $( function() {
            //var addetail = ["aItemCode", "aQty","aPrice", "aDiscFormula", "aDiscAmount", "aIsFree", "aSubTotal"];
            //BatchFocusRegister(addetail);
            //var addmaster = ["CabangId", "GrnDate","ReceiptDate","SupplierId", "SalesName", "GrnDescs", "PaymentType","CreditTerms","bUpdate", "bKembali"];
            //BatchFocusRegister(addmaster);
            var supId,gudangId;
            supId = "<?php print($purchase->SupplierId) ?>";
            gudangId = "<?php print($purchase->GudangId) ?>";
            $("#GrnDate").customDatePicker({ showOn: "focus" });
            $("#ReceiptDate").customDatePicker({ showOn: "focus" });
            $("#aExpDate").customDatePicker({ showOn: "focus" });
            $('#SupplierId').combogrid({
                panelWidth:600,
                url: "<?php print($helper->site_url("master.contacts/getjson_contacts/2/".$userCompId));?>",
                idField:'id',
                textField:'contact_name',
                mode:'remote',
                fitColumns:true,
                columns:[[
                    {field:'contact_code',title:'Kode',width:30},
                    {field:'contact_name',title:'Nama Supplier',width:100},
                    {field:'address',title:'Alamat',width:100},
                    {field:'city',title:'Kota',width:60},
                    {field:'contactlevel',title:'Level',width:20},
                    {field:'credit_terms',title:'Terms',width:20}
                ]],
                onSelect: function(index,row){
                    var sid = row.id;
                    console.log(sid);
                    supId = cid;
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
                    var urz = "<?php print($helper->site_url('ap.purchase/getjson_polists/'.$userCabId.'/'));?>"+cid;
                    $('#dExPoNo').combogrid('grid').datagrid('load',urz);
                }
            });

            $('#aItemSearch').combogrid({
                panelWidth:600,
                url: "<?php print($helper->site_url("ap.purchase/getitemprices_json"));?>",
                idField:'bid',
                textField:'bkode',
                mode:'remote',
                fitColumns:true,
                columns:[[
                    {field:'bkode',title:'Kode',width:50},
                    {field:'bnama',title:'Nama Barang',width:150},
                    {field:'bsatkecil',title:'Satuan',width:40},
                    {field:'bhargabeli',title:'Harga',width:40,align:'right'}
                ]],
                onSelect: function(index,row){
                    var bid = row.bid;
                    console.log(bid);
                    var bkode = row.bkode;
                    console.log(bkode);
                    var bnama = row.bnama;
                    console.log(bnama);
                    var satuan = row.bsatkecil;
                    console.log(satuan);
                    var harga = row.bhargabeli;
                    console.log(harga);
                    var qty = 0;
                    console.log(qty);
                    $('#aItemId').val(bid);
                    $('#aItemCode').val(bkode);
                    $('#aItemDescs').val(bnama);
                    $('#aSatuan').val(satuan);
                    $('#aPrice').val(harga);
                    $('#aDiscFormula').val(0);
                    $('#aDiscAmount').val(0);
                    $('#aQty').val(1);
                    hitDetail();
                    //$('#aQty').focus();
                }
            });

            $("#bAdDetail").click(function(e){
                $('#aItemId').val('');
                $('#aItemCode').val('');
                $('#aItemDescs').val('');
                $('#aSatuan').val('');
                $('#aPrice').val(0);
                $('#aQty').val(0);
                $('#aDiscFormula').val('0');
                $('#aDiscAmount').val(0);
                $('#aIsFree').val(0);
                $('#aSubTotal').val(0);
                //$('#aTaxCode1').val("NOTAX|0");
                $('#aTaxCode').val("NOTAX");
                $('#aTaxPct').val(0);
                $('#aTaxAmount').val(0);
                newItem();
            });

            $("#aItemCode").change(function(e){
                //$ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$hrg_beli.'|'.$hrg_jual;
                var cbi = $("#CabangId").val();
                var itc = $("#aItemCode").val();
                var url = "<?php print($helper->site_url("ap.purchase/getitemprices_plain/"));?>"+cbi+"/"+itc;
                //alert (url);
                if (itc != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aItemId').val(dtx[1]);
                                $('#aItemDescs').val(dtx[2]);
                                $('#aSatuan').val(dtx[3]);
                                $('#aPrice').val(dtx[4]);
                                $('#aDiscFormula').val(0);
                                $('#aDiscAmount').val(0);
                                $('#aQty').val(1);
                                hitDetail();
                                $('#aQty').focus();
                            }else{
                                $('#aItemId').val(0);
                                $('#aItemDescs').val('');
                                $('#aSatuan').val('');
                                $('#aPrice').val(0);
                                $('#aDiscFormula').val(0);
                                $('#aDiscAmount').val(0);
                                $('#aQty').val(0);
                                alert('[1] Data Harga Barang ini tidak ditemukan!');
                                $("#aItemCode").val('');
                                $("#aItemCode").focus();
                            }
                        }else{
                            alert('[2] Data Harga Barang ini tidak ditemukan!');
                        }
                    });
                }
            });

            $("#aQty").change(function(e){
                var stk = Number($('#aQtyStock').val());
                var qty = $('#aQty').val();
                hitDetail();
            });

            $("#aPrice").change(function(e){
                hitDetail();
            });

            $("#aTaxCode1").change(function(e){
                hitDetail();
            });

            $("#aDiscFormula").change(function(e){
                hitDetail();
            });

            $("#aDiscAmount").change(function(e){
                var subTotal = (Number($("#aQty").val()) * Number($("#aPrice").val()));
                var discAmount = Number($('#aDiscAmount').val());
                var totalDetail = subTotal - (discAmount * Number($("#aQty").val()));
                $('#aSubTotal').val(totalDetail);
            });

            $('#aIsFree').change(function () {
                if (this.checked){
                    $('#aIsFree').val(1);
                }else{
                    $('#aIsFree').val(0);
                }
                hitDetail();
            });

            $("#Disc1Pct").change(function(e){
                hitMaster();
            });

            $("#TaxPct").change(function(e){
                hitMaster();
            });

            $("#OtherCostsAmount").change(function(e){
                hitMaster();
            });

            $("#bUpdate").click(function(){
                if (confirm('Apakah data input sudah benar?')){
                    $('#frmMaster').submit();
                }
            });

            $("#bTambah").click(function(){
                if (confirm('Buat GRN baru?')){
                    location.href="<?php print($helper->site_url("ap.purchase/add")); ?>";
                }
            });

            $("#bHapus").click(function(){
                if (confirm('Anda yakin akam membatalkan pembelian ini?')){
                    location.href="<?php print($helper->site_url("ap.purchase/void/").$purchase->Id); ?>";
                }
            });

            $("#bCetakPdf").click(function(){
                if (confirm('Cetak PDF Bukti Pembelian ini?')){
                    window.open("<?php print($helper->site_url("ap.purchase/grn_print/grn/?&id[]=").$purchase->Id); ?>");
                }
            });

            $("#bKembali").click(function(){
                location.href = "<?php print($helper->site_url("ap.purchase")); ?>";
            });

            $("#Disc2Pct").change(function(e){
                hitDiskon();
            });

            $("#Disc2Amount").change(function(e){
                hitPersen();
            });

            $("#bUpdateDiskon").click(function(){
                var d2a = Number($("#Disc2Amount").val());
                var d2p = Number($("#Disc2Pct").val());
                var url = "<?php print($helper->site_url("ap.purchase/update_diskon/".$purchase->Id)); ?>"+'/'+d2p+'/'+d2a;
                if (d2a > 0){
                    $.get(url, function(data, status) {
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success') {
                            location.reload();
                        }
                    });
                }
            });
        });

        function hitDiskon() {
            var bsh = Number($("#BaseHitung").val());
            var d2p = Number($("#Disc2Pct").val());
            var d2a = 0;
            if (bsh > 0 && d2p > 0 && d2a == 0) {
                d2a = Math.round(bsh * (d2p/100),0);
            }
            $("#Disc2Amount").val(d2a);
            $("#TotalAmount").val(bsh - d2a);
        }

        function hitPersen() {
            var bsh = Number($("#BaseHitung").val());
            var d2p = 0;
            var d2a = Number($("#Disc2Amount").val());
            if (bsh > 0 && d2a > 0 && d2p == 0) {
                d2p = Math.round((d2a/bsh) * 100,2);
            }
            $("#Disc2Pct").val(d2p);
            $("#TotalAmount").val(bsh - d2a);
        }

        function hitDetail(){
            //var subTotal = (Number($("#aQty").val()) * Number($("#aPrice").val()));
            //var discAmount = hitDiscFormula(Number($("#aPrice").val()),$("#aDiscFormula").val());
            //var totalDetail = subTotal - (discAmount * Number($("#aQty").val()));
            //$('#aDiscAmount').val(discAmount);
            //$('#aSubTotal').val(totalDetail);
            var isFree = Number($("#aIsFree").val());
            var txd = $("#aTaxCode1").val().split('|');
            var txa = 0;
            var subTotal = 0;
            var dpp = 0;
            var discAmount = 0;
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

        function hitMaster(){
            var bam = Number($("#BaseAmount").val().replace(/,/g,""));
            var dpc = Number($("#Disc1Pct").val().replace(/,/g,""));
            var tpc = Number($("#TaxPct").val().replace(/,/g,""));
            var oca = Number($("#OtherCostsAmount").val().replace(/,/g,""));
            var dam = 0;
            var tam = 0;
            var dpp = 0;
            if (bam > 0 && dpc > 0 ){
                dam = Math.round(bam * (dpc/100),0);
                $("#Disc1Amount").val(dam);
            }else{
                $("#Disc1Amount").val(0);
            }
            dpp = bam - dam;
            $("#DppAmount").val(dpp);
            if (dpp > 0 && tpc > 0 ){
                tam = Math.round(dpp * (tpc/100),0);
                $("#TaxAmount").val(tam);
            }else{
                $("#TaxAmount").val(0);
            }
            $("#TotalAmount").val(dpp+tam+oca);
        }
        function fdeldetail(dta){
            var dtz = dta.replace(/\"/g,"\\\"")
            var dtx = dtz.split('|');
            var id = dtx[0];
            var kode = dtx[1];
            var barang = dtx[2];
            var urx = '<?php print($helper->site_url("ap.purchase/delete_detail/"));?>'+id;
            if (confirm('Hapus Data Detail Barang \nKode: '+kode+ '\nNama: '+barang+' ?')) {
                $.get(urx, function(data){
                    //alert(data);
                    location.reload();
                });
            }
        }

        function feditdetail(dta){
            //$dtx = $detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs).'|'.$detail->ItemId.'|'.$detail->PurchaseQty.'|'.$detail->ReturnQty.'|'.$detail->SatBesar.'|'.$detail->Price.'|'.$detail->DiscFormula.'|'.$detail->IsFree.'|'.$detail->ExPoNo.'|'.$detail->TaxCode.'|'.$detail->TaxPct.'|'.$detail->TaxAmount.'|'.$detail->BatchNo.'|'.$detail->ExpDate;
            var dtx = dta.split('|');
            $('#aId').val(dtx[0]);
            $('#aItemId').val(dtx[3]);
            $('#aItemCode').val(dtx[1]);
            $('#aItemDescs').val(dtx[2]);
            $('#aSatuan').val(dtx[6]);
            $('#aPrice').val(dtx[7]);
            $('#aQty').val(dtx[4]);
            $('#aDiscFormula').val(dtx[8]);
            $('#aIsFree').val(dtx[9]);
            $('#aExPoNo').val(dtx[10]);
            $('#aTaxCode1').val(dtx[11]+'|'+dtx[12]);
            $('#aTaxCode').val(dtx[11]);
            $('#aTaxPct').val(dtx[12]);
            $('#aTaxAmount').val(dtx[13]);
            $('#aBatchNo').val(dtx[14]);
            $('#aExpDate').val(dtx[15]);
            if (Number(dtx[9]) == 0){
                $('#aIsFree').attr("checked",false);
            }else{
                $('#aIsFree').attr("checked",true);
            }
            hitDetail();
            $('#dlg').dialog('open').dialog('setTitle','Edit Detail Barang yang diterima');
            url= "<?php print($helper->site_url("ap.purchase/edit_detail/".$purchase->Id));?>";
        }

        function newItem(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Detail Barang yang diterima');
            //$('#fm').form('clear');
            url= "<?php print($helper->site_url("ap.purchase/add_detail/".$purchase->Id));?>";
            $('#aItemCode').focus();
        }

        function saveDetail(){
            var aitd = Number($('#aItemId').val());
            var aqty = Number($('#aQty').val());
            var astt = Number($('#aSubTotal').val());
            var aisf = 0;
            if ($('#aIsFree').is(':checked')){
                aisf = 1;
            }
            if ((aitd > 0 && aqty > 0 && astt > 0) || (aitd > 0 && aqty > 0 && aisf > 0)){
                $('#fm').form('submit',{
                    url: url,
                    onSubmit: function(){
                        return $(this).form('validate');
                    },
                    success: function(result){
                        var result = eval('('+result+')');
                        if (result.errorMsg){
                            $.messager.show({
                                title: 'Error',
                                msg: result.errorMsg
                            });
                        } else {
                            location.reload();
                            $('#dlg').dialog('close');		// close the dialog
                        }
                    }
                });
            }else{
                alert('Data tidak valid!');
            }
        }

        function hitDiscFormula(nAmount,dFormula) {
            var retVal = 0;
            if (nAmount > 0 && dFormula != '' && dFormula != '0') {
                var aFormula = dFormula.split('+');
                var nDiscount = 0;
                var pDiscount = 0;
                for (var i = 0; i < aFormula.length; i++) {
                    pDiscount = aFormula[i];
                    nDiscount = Math.round(nAmount * (pDiscount) / 100, 0);
                    retVal += nDiscount;
                    nAmount -= retVal;
                }
            }
            return retVal;
        }

    </script>
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
<div id="p" class="easyui-panel" title="Edit Pembelian/Penerimaan Barang" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form id="frmMaster" action="<?php print($helper->site_url("ap.purchase/edit/".$purchase->Id)); ?>" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
            <tr>
                <td>Cabang</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($purchase->CabangCode != null ? $purchase->CabangCode : $userCabCode); ?>" disabled/>
                    <input type="hidden" id="CabangId" name="CabangId" value="<?php print($purchase->CabangId == null ? $userCabId : $purchase->CabangId);?>"/>
                </td>
                <td>Tanggal</td>
                <td><input type="text" size="12" id="GrnDate" name="GrnDate" value="<?php print($purchase->FormatGrnDate(JS_DATE));?>" <?php print($itemsCount == 0 ? 'required' : 'readonly');?>/></td>
                <td>Diterima</td>
                <td><input type="text" size="12" id="ReceiptDate" name="ReceiptDate" value="<?php print($purchase->FormatReceiptDate(JS_DATE));?>" <?php print($itemsCount == 0 ? 'required' : 'readonly');?>/></td>
                <td>No. GRN</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="GrnNo" name="GrnNo" value="<?php print($purchase->GrnNo != null ? $purchase->GrnNo : '-'); ?>" readonly/></td>
            </tr>
            <tr>
                <td>Supplier</td>
                <td><input class="easyui-combogrid" id="SupplierId" name="SupplierId" style="width: 250px" value="<?php print($purchase->SupplierId);?>" required/></td>
                <td>Salesman</td>
                <td><b><input type="text" class="f1 easyui-textbox" id="SalesName" name="SalesName" style="width: 150px" maxlength="50" value="<?php print($purchase->SalesName != null ? $purchase->SalesName : '-'); ?>"/></b></td>
                <td>Status</td>
                <td><select class="easyui-combobox" id="GrnStatus1" name="GrnStatus1" style="width: 150px" disabled>
                        <option value="0" <?php print($purchase->GrnStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                        <option value="1" <?php print($purchase->GrnStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                        <option value="2" <?php print($purchase->GrnStatus == 2 ? 'selected="selected"' : '');?>>2 - Closed</option>
                        <option value="3" <?php print($purchase->GrnStatus == 3 ? 'selected="selected"' : '');?>>3 - Batal</option>
                    </select>
                    <input type="hidden" id="GrnStatus" name="GrnStatus" value="<?php print($purchase->GrnStatus);?>"/>
                </td>
                <td>Ex PO No.</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="ExPoNo" name="ExPoNo" value="<?php print($purchase->ExPoNo != null ? $purchase->ExPoNo : '-'); ?>"/></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td><b><input type="text" class="f1 easyui-textbox" id="GrnDescs" name="GrnDescs" style="width: 250px" value="<?php print($purchase->GrnDescs != null ? $purchase->GrnDescs : '-'); ?>" required/></b></td>
                <td>Gudang *</td>
                <td>
                    <?php if ($itemsCount == 0){?>
                    <select class="easyui-combobox" id="GudangId" name="GudangId" style="width: 150px">
                    <?php }else{ ?>
                    <input type="hidden" name="GudangId" id="GudangId" value="<?php print($purchase->GudangId);?>"/>
                    <select class="easyui-combobox" id="GudangId1" name="GudangId1" style="width: 150px" disabled>
                    <?php } ?>
                        <option value="">- Pilih Gudang -</option>
                        <?php
                        /** @var $gudang Warehouse[]*/
                        foreach ($gudangs as $gudang) {
                            if ($gudang->Id == $purchase->GudangId) {
                                printf('<option value="%d" selected="selected">%s</option>', $gudang->Id, $gudang->WhCode);
                            }else{
                                printf('<option value="%d">%s</option>', $gudang->Id, $gudang->WhCode);
                            }
                        }
                        ?>
                        ?>
                    </select>
                </td>
                <td>Cara Bayar</td>
                <td><select id="PaymentType" name="PaymentType" required>
                        <option value="1" <?php print($purchase->PaymentType == 1 ? 'selected="selected"' : '');?>>Kredit</option>
                        <option value="0" <?php print($purchase->PaymentType == 0 ? 'selected="selected"' : '');?>>Tunai</option>
                    </select>
                    &nbsp
                    Kredit
                    <input type="text" id="CreditTerms" name="CreditTerms" size="2" maxlength="5" value="<?php print($purchase->CreditTerms != null ? $purchase->CreditTerms : 0); ?>" style="text-align: right" required/>&nbsphr</td>
                <td colspan="3" class="blink" style="color: orangered"><b>*Tanggal & Gudang tidak boleh diubah setelah detail diinput*</b></td>
            </tr>
            <tr>
                <td colspan="7">
                    <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma; widows: 100%;">
                        <tr>
                            <th colspan="13">DETAIL BARANG YANG DIBELI/DITERIMA</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>QTY</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Bonus</th>
                            <th>Jumlah</th>
                            <th>Diskon</th>
                            <th>Pajak</th>
                            <th>Total</th>
                            <th>Batch No</th>
                            <th>Exp Date</th>
                        </tr>
                        <?php
                        $counter = 0;
                        $total = 0;
                        $dta = null;
                        $dtx = null;
                        foreach($purchase->Details as $idx => $detail) {
                            $counter++;
                            print("<tr>");
                            printf('<td class="right">%s.</td>', $counter);
                            printf('<td>%s</td>', $detail->ItemCode);
                            printf('<td nowrap="nowrap">%s</td>', $detail->ItemDescs);
                            printf('<td class="right">%s</td>', number_format($detail->PurchaseQty,0));
                            printf('<td>%s</td>', $detail->SatBesar);
                            printf('<td class="right">%s</td>', number_format($detail->Price,0));
                            if($detail->IsFree == 0){
                                print("<td class='center'><input type='checkbox' disabled></td>");
                                printf('<td class="right">%s</td>', number_format($detail->Price * $detail->PurchaseQty,0));
                            }else{
                                print("<td class='center'><input type='checkbox' checked='checked' disabled></td>");
                                print("<td class='right'>0</td>");
                            }
                            printf('<td class="right">%s</td>', number_format($detail->DiscAmount,0));
                            printf('<td class="right">%s</td>', number_format($detail->TaxAmount,0));
                            printf('<td class="right">%s</td>', number_format($detail->SubTotal + $detail->TaxAmount-$detail->DiscAmount,0));
                            printf("<td nowrap='nowrap'>%s</td>",$detail->BatchNo);
                            printf("<td nowrap='nowrap'>%s</td>",$detail->FormatExpDate(JS_DATE));
                            print("<td class='center' nowrap='nowrap'>");
                            $dta = addslashes($detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs));
                            $dtx = $detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs).'|'.$detail->ItemId.'|'.$detail->PurchaseQty.'|'.$detail->ReturnQty.'|'.$detail->SatBesar.'|'.$detail->Price.'|'.$detail->DiscFormula.'|'.$detail->IsFree.'|'.$detail->ExPoNo.'|'.$detail->TaxCode.'|'.$detail->TaxPct.'|'.$detail->TaxAmount.'|'.$detail->BatchNo.'|'.date('d-m-Y',$detail->ExpDate);
                            printf('&nbsp<img src="%s" alt="Edit barang" title="Edit barang" style="cursor: pointer" onclick="return feditdetail(%s);"/>',$bedit,"'".$dtx."'");
                            printf('&nbsp<img src="%s" alt="Hapus barang" title="Hapus barang" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dta."'");
                            print("</td>");
                            print("</tr>");
                            $total += $detail->SubTotal;
                        }
                        ?>
                        <tr class="bold">
                            <td colspan="7" align="right">Total Pembelian:</td>
                            <td class="right"><?php print($purchase->BaseAmount != null ? number_format($purchase->BaseAmount,0) : 0); ?></td>
                            <td class="right"><?php print($purchase->Disc1Amount != null ? number_format($purchase->Disc1Amount,0) : 0); ?></td>
                            <td class="right"><?php print($purchase->TaxAmount != null ? number_format($purchase->TaxAmount,0) : 0); ?></td>
                            <td class="right"><?php print($purchase->TotalAmount != null ? number_format($purchase->TotalAmount + $purchase->Disc2Amount,0) : 0); ?></td>
                            <td colspan="2" class="center" nowrap="nowrap"><?php
                                if ($acl->CheckUserAccess("ap.purchase", "add")) {
                                    printf('<img src="%s" alt="GRN Baru" title="Buat invoice baru" id="bTambah" style="cursor: pointer;"/>', $baddnew);
                                }
                                ?>
                                &nbsp;
                                <?php
                                if ($acl->CheckUserAccess("ap.purchase", "delete")) {
                                    printf('<img src="%s" alt="Hapus Grn" title="Proses hapus invoice" id="bHapus" style="cursor: pointer;"/>',$bdelete);
                                }
                                ?>
                                &nbsp;
                                <?php
                                if ($acl->CheckUserAccess("ap.purchase", "print")) {
                                    printf('<img src="%s" id="bCetakPdf" alt="Cetak Bukti Pembelian" title="Proses cetak bukti pembelian" style="cursor: pointer;"/>',$bpdf);
                                }
                                ?>
                                &nbsp;
                                <?php
                                printf('<img src="%s" id="bKembali" alt="Daftar Grn" title="Kembali ke daftar invoice" style="cursor: pointer;"/>',$bkembali);
                                ?>
                            </td>
                            <td class='center'><?php printf('<img src="%s" alt="Tambah Barang" title="Tambah barang" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></td>
                        </tr>
                        <tr>
                            <td colspan="7" align="right">Diskon Tambahan :</td>
                            <td nowrap="nowrap">
                                <input type="hidden" id="BaseHitung" name="BaseHitung" value="<?php print(($purchase->BaseAmount - $purchase->Disc1Amount) + $purchase->TaxAmount);?>"/>
                                <input type="text"  class="right bold" name="Disc2Pct" id="Disc2Pct" value="<?php print($purchase->Disc2Pct);?>" size="5" maxlength="4"> %
                            </td>
                            <td><input type="text"  class="right bold" name="Disc2Amount" id="Disc2Amount" value="<?php print($purchase->Disc2Amount);?>" size="10"></td>
                            <td>Nett</td>
                            <td><input type="text"  class="right bold" name="TotalAmount" id="TotalAmount" value="<?php print($purchase->TotalAmount);?>" size="12"></td>
                            <td colspan="3"><span id="bUpdateDiskon" style="cursor: pointer;"><b>[Klik utk Update Diskon]</b></span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2018 - 2019 PT. Reka Sistem Teknologi
</div>
<!-- Form Add Grn Detail -->
<div id="dlg" class="easyui-dialog" style="width:800px;height:300px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="right bold">Cari Data :</td>
                <td colspan="7"><input id="aItemSearch" name="aItemSearch" style="width: 600px"/></td>
            </tr>
            <tr>
                <td class="right">Kode Barang :</td>
                <td colspan="2"><input type="text" id="aItemCode" name="aItemCode" size="20" value="" required/></td>
                <td colspan="5"><span style="color: red" class="blink"><b>**Ketik Kode Barang atau Scan BarCode agar lebih cepat**</b></span>
                    <input type="hidden" id="aItemId" name="aItemId" value="0"/>
                    <input type="hidden" id="aId" name="aId" value="0"/>
                    <input type="hidden" id="aExPoNo" name="aExPoNo" value=""/>
                    <input type="hidden" id="aQtyStock" name="aQtyStock" value="0"/>
                </td>

            </tr>
            <tr>
                <td class="right">Nama Barang :</td>
                <td colspan="3"><input type="text" id="aItemDescs" name="aItemDescs" size="50" value="" disabled/></td>
                <td class="right">Satuan :</td>
                <td><input type="text" id="aSatuan" name="aSatuan" size="5" value="" disabled/></td>
            </tr>
            <tr>
                <td class="right">QTY :</td>
                <td><input class="right" type="text" id="aQty" name="aQty" size="5" value="0"/>
                    &nbsp; Bonus &nbsp; <input class="right" type="checkbox" id="aIsFree" name="aIsFree" value="0"/>
                </td>
                <td class="right">Harga :</td>
                <td><input class="right" type="text" id="aPrice" name="aPrice" size="12" value="0"/></td>
                <td class="right">Jumlah :</td>
                <td><input class="right" type="text" id="aSubTotal" name="aSubTotal" size="15" value="0" readonly/></td>
            </tr>
            <tr>
                <td class="right">Diskon :</td>
                <td><input class="right" type="text" id="aDiscFormula" name="aDiscFormula" size="5" value="0"/>&nbsp;%</td>
                <td class="right">Nilai Diskon :</td>
                <td><input class="right" type="text" id="aDiscAmount" name="aDiscAmount" size="12" value="0"/></td>
                <td class="right">DPP :</td>
                <td><input class="right" type="text" id="aDpp" name="aDpp" size="15" value="0" readonly/></td>
            </tr>
            <tr>
                <td class="right">Jenis Pajak :</td>
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
                <td class="right">Pajak :</td>
                <td><input class="right" type="text" id="aTaxAmount" name="aTaxAmount" size="12" value="0"/></td>
                <td class="right">Total :</td>
                <td><input class="right" type="text" id="aTotal" name="aTotal" size="15" value="0" readonly/></td>
            </tr>
            <tr>
                <td class="right">Expire Date :</td>
                <td><input type="text" id="aExpDate" name="aExpDate" size="12" value=""/></td>
                <td class="right">Batch No :</td>
                <td colspan="3"><input type="text" id="aBatchNo" name="aBatchNo" size="40" value=""/></td>
            </tr>
        </table>
    </form>

</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDetail()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
</body>
</html>
