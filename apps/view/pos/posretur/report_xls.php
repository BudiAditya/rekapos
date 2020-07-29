<?php
$phpExcel = new PHPExcel();
$headers = array(
    'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="rekap-pos-return.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Rekasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Rekasystem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Retur Penjualan Tunai");
//helper for styling
$center = array("alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
$right = array("alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
$allBorders = array("borders" => array("allborders" => array("style" => PHPExcel_Style_Border::BORDER_THIN)));
$idrFormat = array("numberformat" => array("code" => '_([$-421]* #,##0_);_([$-421]* (#,##0);_([$-421]* "-"??_);_(@_)'));
// OK mari kita bikin ini cuma bisa di read-only
//$password = "" . time();
//$sheet->getProtection()->setSheet(true);
//$sheet->getProtection()->setPassword($password);

// FORCE Custom Margin for continous form
/*
$sheet->getPageMargins()->setTop(0)
    ->setRight(0.2)
    ->setBottom(0)
    ->setLeft(0.2)
    ->setHeader(0)
    ->setFooter(0);
*/
$row = 1;
$sheet->setCellValue("A$row",$company_name);
// Hmm Reset Pointer
$sheet->getStyle("A1");
$sheet->setShowGridlines(false);
$row++;
if ($JnsLaporan < 3) {
    $sheet->setCellValue("A$row","REKAPITULASI RETUR PENJUALAN TUNAI");
    $row++;
    $sheet->setCellValue("A$row","Dari Tgl. ".date('d-m-Y',$StartDate)." - ".date('d-m-Y',$EndDate));
    $row++;
    $sheet->setCellValue("A$row","No.");
    $sheet->setCellValue("B$row","Cabang");
    $sheet->setCellValue("C$row","Tanggal");
    $sheet->setCellValue("D$row","No. Bukti");
    $sheet->setCellValue("E$row","Keterangan");
    $sheet->setCellValue("F$row","Nilai Retur");
    if ($JnsLaporan == 2) {
        $sheet->setCellValue("G$row", 'Ex.Trx');
        $sheet->setCellValue("H$row", 'Kode Barang');
        $sheet->setCellValue("I$row", 'Nama Barang');
        $sheet->setCellValue("J$row", 'QTY');
        $sheet->setCellValue("K$row", 'Harga');
        $sheet->setCellValue("L$row", 'Jumlah');
        $sheet->getStyle("A$row:L$row")->applyFromArray(array_merge($center, $allBorders));
    }else {
        $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($center, $allBorders));
    }
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        $sts = null;
        $ivn = null;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            if ($ivn <> $rpt["rtn_no"]) {
                $nmr++;
                $sma = false;
            } else {
                $sma = true;
            }
            if (!$sma) {
                $sheet->setCellValue("A$row", $nmr);
                $sheet->getStyle("A$row")->applyFromArray($center);
                $sheet->setCellValue("B$row", $rpt["cabang_code"]);
                $sheet->setCellValue("C$row", date('d-m-Y', strtotime($rpt["rtn_date"])));
                $sheet->setCellValue("D$row", $rpt["rtn_no"]);
                $sheet->setCellValue("E$row", $rpt["rtn_descs"]);
                $sheet->setCellValue("F$row", $rpt["rtn_amount"]);
                $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
            }
            if ($JnsLaporan == 2) {
                $sheet->setCellValue("G$row", $rpt['ex_trx_no']);
                $sheet->setCellValueExplicit("H$row", $rpt['item_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("I$row", $rpt['item_descs']);
                $sheet->setCellValue("J$row", $rpt['qty_retur']);
                $sheet->setCellValue("K$row", $rpt['price']);
                $sheet->setCellValue("L$row", $rpt['sub_total']);
                $sheet->getStyle("G$row:L$row")->applyFromArray(array_merge($allBorders));
            }
            $ivn = $rpt["rtn_no"];
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "TOTAL RETUR");
        $sheet->mergeCells("A$row:E$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
        $sheet->getStyle("F$str:F$row")->applyFromArray($idrFormat);
        if ($JnsLaporan == 2) {
            $sheet->mergeCells("G$row:K$row");
            $sheet->getStyle("A$row:L$row")->applyFromArray(array_merge($allBorders));
            $sheet->setCellValue("L$row", "=SUM(L$str:L$edr)");
            $sheet->getStyle("K$str:L$row")->applyFromArray($idrFormat);
        } else {
            $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
        }
        $row++;
    }
}else{
        // rekap item yang terjual
        $sheet->setCellValue("A$row", "REKAPITULASI ITEM RETUR PENJUALAN TUNAI");
        $row++;
        $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
        $row++;
        $sheet->setCellValue("A$row", "No.");
        $sheet->setCellValue("B$row", "Kode Barang");
        $sheet->setCellValue("C$row", "Nama Barang");
        $sheet->setCellValue("D$row", "Satuan");
        $sheet->setCellValue("E$row", "Bagus");
        $sheet->setCellValue("F$row", "Rusak");
        $sheet->setCellValue("G$row", "Expire");
        $sheet->setCellValue("H$row", "Nilai Retur");
        $sheet->getStyle("A$row:H$row")->applyFromArray(array_merge($center, $allBorders));
        $nmr = 0;
        $str = $row;
        if ($Reports != null) {
            while ($rpt = $Reports->FetchAssoc()) {
                $row++;
                $nmr++;
                $sheet->setCellValue("A$row", $nmr);
                $sheet->setCellValueExplicit("B$row", $rpt['item_code'],PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("C$row", $rpt['item_name']);
                $sheet->setCellValue("D$row", $rpt['satuan']);
                $sheet->setCellValue("E$row", $rpt['qty_bagus']);
                $sheet->setCellValue("F$row", $rpt['qty_rusak']);
                $sheet->setCellValue("G$row", $rpt['qty_expire']);
                $sheet->setCellValue("H$row", $rpt['sum_total']);
                $sheet->getStyle("A$row:H$row")->applyFromArray(array_merge($allBorders));
            }
            $edr = $row;
            $row++;
            $sheet->setCellValue("A$row", "T O T A L");
            $sheet->mergeCells("A$row:D$row");
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("E$row","=SUM(E$str:E$edr)");
            $sheet->setCellValue("F$row","=SUM(F$str:F$edr)");
            $sheet->setCellValue("G$row","=SUM(G$str:G$edr)");
            $sheet->setCellValue("H$row","=SUM(H$str:H$edr)");
            $sheet->getStyle("E$str:H$row")->applyFromArray($idrFormat);
            $sheet->getStyle("A$row:H$row")->applyFromArray(array_merge($allBorders));
        }
}
// Flush to client

foreach ($headers as $header) {
    header($header);
}

// Hack agar client menutup loading dialog box... (Ada JS yang checking cookie ini pada common.js)
$writer->save("php://output");

// Garbage Collector
$phpExcel->disconnectWorksheets();
unset($phpExcel);
ob_flush();
