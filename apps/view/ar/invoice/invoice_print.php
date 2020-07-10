<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
require_once (LIBRARY . "gen_functions.php");
$userName = AclManager::GetInstance()->GetCurrentUser()->RealName;
/** @var $invoice Invoice */
/** @var $cabang Cabang */
/** @var $customer Contacts */
?>
<head>
    <title>REKASYS | Print Nota Penjualan (Invoicing)</title>
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
        .pagebreak { page-break-before: always; } /* page-break-after works, as well */
    </style>
</head>
<body style="background-color:white;">
<?php //include(VIEW . "main/menu.php"); ?>
<div align="right">
    <input type="button" class="button" onclick="printDiv('printInvoice')" value="Print Invoice" />
    <a href="<?php print($helper->site_url("ar.invoice")); ?>">Daftar Invoice</a>
</div>
<div id="printInvoice">
<?php
foreach ($report as $idx => $invoice) {
?>
    <div class="pagebreak"> </div>
    <table cellpadding="1" cellspacing="1" width="750" bgcolor="white">
        <tr>
            <td valign="top">
                <table cellpadding="1" cellspacing="1" width="400">
                    <tr>
                        <td colspan="3">
                            <b><?php print($cabang->NamaCabang);?></b>
                            <br>
                            <?php print($cabang->Alamat.' - '.$cabang->Kota);?>
							<br>
                            <?php print('Tlp: '.$cabang->Notel);?>
                            <br>
                            NPWP: <?php print($cabang->Npwp);?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="25%">Kepada Yth :</td>
                        <td><?php print($invoice->CustomerName. ' ('.$invoice->CustomerCode.')');?></td>
                    </tr>
                    <tr>
                        <td width="25%">&nbsp;</td>
                        <td><?php print($invoice->CustomerAddress);?></td>
                    </tr>
                    <tr>
                        <td width="25%">&nbsp;</td>
                        <td><?php print($invoice->CustomerCity);?></td>
                    </tr>
                    <tr>
                        <td width="25%">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td valign="top">
                <table cellpadding="1" cellspacing="1" width="350">
                    <tr>
                        <td colspan="3"><b>PROFORMA INVOICE</b></td>
                    </tr>
                    <tr>
                        <td width="20%">Nomor</td>
                        <td>:</td>
                        <td><?php print($invoice->InvoiceNo);?></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><?php print($invoice->FormatInvoiceDate(JS_DATE));?></td>
                    </tr>
                    <tr>
                        <td>JTP</td>
                        <td>:</td>
                        <td><?php
                            if ($invoice->CreditTerms > 0) {
                                print($invoice->FormatDueDate(JS_DATE) . ' (' . $invoice->CreditTerms . ' hari)');
                            }else{
                                print('CASH');
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Salesman</td>
                        <td>:</td>
                        <td><?php print($invoice->SalesName);?></td>
                    </tr>
                    <tr>
                        <td>S/O No.</td>
                        <td>:</td>
                        <td><?php print($invoice->ExSoNo);?></td>
                    </tr>
                    <tr>
                        <td>Gudang</td>
                        <td>:</td>
                        <td><?php print($invoice->GudangCode);?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table cellpadding="2" cellspacing="2" width="750" class="tableBorder" bgcolor="white">
        <tr align="center">
            <td width="7%">KODE</td>
            <td width="38%">NAMA PRODUK</td>
            <td colspan="2" width="10%">QTY</td>
            <td width="10%">HARGA</td>
            <td width="10%">DISKON</td>
            <td width="10%">PAJAK</td>
            <td width="15%">JUMLAH</td>
        </tr>
        <?php
        $qjns = 0;
        $qqty = 0;
        foreach($invoice->Details as $idx => $detail) {
            print('<tr>');
            printf('<td> %s</td>',$detail->ItemCode);
            printf('<td> %s</td>',$detail->ItemDescs);
            printf('<td align="right">%s &nbsp;</td>',number_format($detail->Qty));
            printf('<td> %s</td>',left($detail->SatKecil,3));
            if ($detail->IsFree == 0) {
                printf('<td align="right">%s</td>', number_format($detail->Price));
                if ($detail->DiscAmount > 0) {
                    printf('<td align="right">%s</td>', number_format($detail->DiscAmount));
                }else{
                    print('<td>&nbsp;</td>');
                }
                if ($detail->TaxAmount > 0) {
                    printf('<td align="right">%s</td>', number_format($detail->TaxAmount));
                }else{
                    print('<td>&nbsp;</td>');
                }
                printf('<td align="right">%s</td>', number_format($detail->SubTotal));
            }else{
                print('<td>* Bonus *</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>');
            }
            print('</tr>');
            $qjns++;
            $qqty+= $detail->Qty;
        }
        print('<tr>');
        printf('<td>&nbsp;</td><td colspan="5">Total: %s Satuan * %s Jenis *</td><td class="center">TOTAL</td><td class="right">%s</td>',$qqty,$qjns,number_format($invoice->TotalAmount));
        print('</tr>');
        print('<tr>');
        printf('<td colspan="8" valign="middle">
        <u>Catatan :</u>
        <br>
        - Barang sudah diterima dengan baik dan cukup.
        <br>
        - Barang yang sudah diterima tidak boleh dikembalikan.
        <br>
        - Pembayaran dengan cek-giro dianggap Lunas setelah dapat dicairkan.
        <br>
        - Faktur Asli merupakan bukti sah penagihan pelunasan.
        <br>
        - Pembayaran via transfer bank ke: &nbsp;%s
        </td>',$cabang->Norek);
        print('</tr>');
        print('<tr>');
        print('<td colspan="5">
        Cap dan Tanda Tangan
        <br>
        <br>
        <br>       
        <br>
        ------------------ &nbsp;&nbsp;&nbsp; ----------------- &nbsp;&nbsp;&nbsp; ----------------- &nbsp;&nbsp;&nbsp; ------------------
        <br>
        &nbsp;&nbsp;Toko/Pembeli &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Otorisasi &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Pengiriman
        </td>');
        printf('<td valign="top" colspan="3">#%s#</td>',ucwords(terbilang($invoice->TotalAmount)).' Rupiah');
        print('</tr>');
        print('<tr>');
        printf('<td colspan="7" align="right" style="border-left: 0px;border-right: 0px;border-bottom: 0px"><sub><i>Input by: %s - Printed by: %s - Time: %s</i></sub></td>',$invoice->AdminName,$userName,date('d-m-Y h:i:s'));
        print('</tr>');
        ?>
    </table>
<?php } ?>
</div>
<script type="text/javascript">
    function printDiv(divName) {
        //if (confirm('Print Invoice ini?')) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
        //}
    }
</script>
</body>
</html>
