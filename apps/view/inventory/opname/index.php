<!DOCTYPE HTML>
<html>
<head>
    <title>REKASYS - Opname Stock Barang</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>
    <script type="text/javascript">
        $(function(){
            //var addetail = ["aItemCode", "aOpnTime","aMaxDisc", "aHrgBeli", "aMarkup1", "aHrgJual1", "aMarkup2", "aHrgJual2", "aMarkup3", "aHrgJual3", "aMarkup4", "aHrgJual4", "aMarkup5", "aHrgJual5", "aMarkup6", "aHrgJual6"];
            //BatchFocusRegister(addetail);
            //$("#aOpnTime").customDatePicker({ showOn: "focus" });
            $('#dg').datagrid({
                url: "<?php print($helper->site_url("inventory.opname/get_data"));?>",
                pageList: [10,15,30,50],
                height: 'auto',
                scrollbarSize: 0
            });

            $("#aItemCode").change(function(e){
                getDataByKode();
            });

            $("#aBarCode").change(function(e){
                getDataByBarCode();
            });
        });

        function getDataByKode() {
            //$ret = "OK|".$stock->ItemId.'|'.$stock->ItemName.'|'.$stock->SatKecil.'|'.$stock->QtyStock;
            var itc = $("#aItemCode").val();
            var gdi = $("#aGudangId").combobox("getValue");
            var url = "<?php print($helper->site_url("inventory.stock/getitemstock_plain/"));?>"+gdi+'/'+itc;
            if (itc != ''){
                if (gdi == 0){
                    alert('Gudang/Lokasi barang belum diisi!');
                }else {
                    $('#aSysQty').val(0);
                    $.get(url, function (data, status) {
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success') {
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK') {
                                $('#aItemId').val(dtx[1]);
                                $('#aItemDescs').val(dtx[2]);
                                $('#aSatuan').val(dtx[3]);
                                $('#aOpnQty').val(dtx[4]);
                                $('#aBarCode').val(dtx[5]);
                            }else{
                                alert("Kode Barang tidak terdaftar!");
                            }
                        }
                    });
                }
            }
        }

        function getDataByBarCode() {
            //$ret = "OK|".$stock->ItemId.'|'.$stock->ItemName.'|'.$stock->SatKecil.'|'.$stock->QtyStock;
            var itc = $("#aBarCode").val();
            var gdi = $("#aGudangId").combobox("getValue");
            var url = "<?php print($helper->site_url("inventory.stock/getitemstock_plainbybarcode/"));?>"+gdi+'/'+itc;
            if (itc != ''){
                if (gdi == 0){
                    alert('Gudang/Lokasi barang belum diisi!');
                }else {
                    $('#aSysQty').val(0);
                    $.get(url, function (data, status) {
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success') {
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK') {
                                $('#aItemId').val(dtx[1]);
                                $('#aItemDescs').val(dtx[2]);
                                $('#aSatuan').val(dtx[3]);
                                $('#aOpnQty').val(dtx[4]);
                                $('#aItemCode').val(dtx[5]);
                            }else{
                                alert("Barcode barang tidak terdaftar!");
                            }
                        }
                    });
                }
            }
        }

        function newOpname(){
            $('#dlg').dialog('open').dialog('setTitle','Input Data Opname Stock');
            //$('#fm').form('clear');
            url= "<?php print($helper->site_url("inventory.opname/save"));?>";
        }

        function saveOpname(){
            var aitd = Number($('#aItemId').val());
            if (aitd > 0 ){
                $('#fm').form('submit',{
                    url: url,
                    onSubmit: function(){
                        return $(this).form('validate');
                    },
                    success: function(result){
                        //var result = eval('('+result+')');
                        //if (result.errorMsg){
                        //    $.messager.show({
                        //        title: 'Error',
                        //        msg: result.errorMsg
                        //    });
                        //} else {
                            location.reload();
                            $('#dlg').dialog('close');		// close the dialog
                            $('#dg').datagrid('reload');	// reload the user data
                        //}
                    }
                });
            }else{
                alert('Data tidak valid!');
            }
        }

        function destroyOpname(){
            var row = $('#dg').datagrid('getSelected');
            var url= "<?php print($helper->site_url("inventory.opname/hapus/"));?>"+row.id;
            if (row){
                $.messager.confirm('Confirm','Anda yakin akan menghapus data ini?',function(r){
                    if (r){
                        $.post(url,{id:row.id},function(result){
                            if (result.success){
                                $('#dg').datagrid('reload');	// reload the user data
                            } else {
                                $.messager.show({	// show error message
                                    title: 'Error',
                                    msg: result.errorMsg
                                });
                            }
                        },'json');
                    }
                });
            }
        }

        function doSearch(){
            $('#dg').datagrid('load',{
                sfield: $('#sfield').val(),
                scontent: $('#scontent').val()
            });
        }

        function doClear(){
            $('#sfield').val('');
            $('#scontent').val('');
            doSearch();
        }

        function viewLaporan() {
            var url = "<?php print($helper->site_url("inventory.opname/report"));?>";
            window.open(url,'_blank');
        }
    </script>
    <style type="text/css">
        #fm{
            margin:0;
            padding:10px 30px;
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
            width:80px;
        }
        .fitem input{
            width:160px;
        }
    </style>
</head>

<body>
<?php include(VIEW . "main/menu.php");
date_default_timezone_set('Asia/Shanghai');
$crDate = date('Y-m-d h:i:s', time());
//$crDate = date(JS_DATE, strtotime(date('Y-m-d Hms')));
?>
<div align="left">
    <table id="dg" title="Daftar Opname Stock Barang" class="easyui-datagrid" style="width:100%;height:500px"
           toolbar="#toolbar"
           pagination="true"
           rownumbers="true"
           fitColumns="true"
           striped="true"
           singleSelect="true"
           showHeader="true"
           showFooter="true"
        >
        <thead>
        <tr>
            <th field="wh_code" width="15">Lokasi</th>
            <th field="opn_time" width="25">Tanggal</th>
            <th field="opn_no" width="20">No. Bukti</th>
            <th field="item_code" width="20" sortable="true">Kode Barang</th>
            <th field="bar_code" width="30" sortable="true">Bar Code</th>
            <th field="bnama" width="60" sortable="true">Nama Barang</th>
            <th field="bsatbesar" width="10">Satuan</th>
            <th field="opn_qty" width="20" sortable="true" align="right">Stock</th>
            <th field="o_status" width="20" sortable="true">Status</th>
        </tr>
        </thead>
    </table>
</div>
<div id="toolbar" style="padding:3px">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newOpname()">Baru</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyOpname()">Hapus</a>
    &nbsp|&nbsp
    <span>Filter Data:</span>
    <select id="sfield" style="line-height:15px;border:1px solid #ccc">
        <option value=""></option>
        <option value="wh_code">Lokasi</option>
        <option value="item_code">Kode Barang</option>
        <option value="bar_code">Bar Code</option>
        <option value="bnama">Nama Barang</option>
        </select>
    <span>Isi:</span>
    <input id="scontent" size="20" maxlength="50"  style="line-height:15px;border:1px solid #ccc">
    <a href="#" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="doSearch()">Cari</a>
    <a href="#" class="easyui-linkbutton" iconCls="icon-clear" plain="true" onclick="doClear()">Clear</a>
    &nbsp|&nbsp
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="viewLaporan()">Laporan</a>
</div>

<div id="dlg" class="easyui-dialog" style="width:620px;height:240px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="bold right">Gudang/Lokasi</td>
                <td><select name="aGudangId" class="easyui-combobox" id="aGudangId" style="width: 150px" required>
                        <?php
                            print('<option value="0"></option>');
                            foreach ($gudangs as $cab) {
                                printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->CabCode, $cab->WhCode);
                            }
                        ?>
                    </select>
                </td>
                <td class="bold right">Waktu</td>
                <td><input type="text" class="bold" size="15" id="aOpnTime" name="aOpnTime" value="<?php print($crDate);?>" required/></td>
            </tr>
            <tr>
                <td class="bold right">Kode Barang</td>
                <td>
                    <input type="text" class="bold" id="aItemCode" name="aItemCode" style="width: 150px" required/>
                    <input type="hidden" id="aItemId" name="aItemId" value="0"/>
                    <input type="hidden" id="aId" name="aId" value="0"/>
                </td>
            </tr>
            <tr>
                <td class="bold right">Bar Code</td>
                <td><input type="text" class="bold" id="aBarCode" name="aBarCode" style="width: 150px" required/></td>
            </tr>
            <tr>
                <td class="bold right">Nama Barang</td>
                <td colspan="3"><input type="text" class="bold" id="aItemDescs" name="aItemDescs" size="50" disabled/></td>
            </tr>
            <tr>
                <td class="bold right">Stock Qty</td>
                <td class="bold"><input class="bold right" type="text" id="aOpnQty" name="aOpnQty" size="5" value="0" required/>
                    <input type="text" class="bold" id="aSatuan" name="aSatuan" size="5" disabled/>
                </td>
            </tr>
        </table>
    </form>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveOpname()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
</body>
</html>
