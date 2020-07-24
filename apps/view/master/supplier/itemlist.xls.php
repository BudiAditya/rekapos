<?php
$phpExcel = new PHPExcel();
$headers = array(
  'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="daftar-barang-distributor.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Rekasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Rekasystem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Daftar Barang Distributor");
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
$sheet->setCellValue("A$row",'ERDITA MART');
// Hmm Reset Pointer
$sheet->getStyle("A1");
$sheet->setShowGridlines(false);
$row++;
// rekap item yang terjual
$sheet->setCellValue("A$row", "DAFTAR BARANG DISTRIBUTOR");
$row++;
$sheet->setCellValue("A$row", "DISTRIBUTOR : " . $dtSupplier->ContactName . '(' . $dtSupplier->ContactCode . ')');
$row++;
$sheet->setCellValue("A$row", "No.");
$sheet->setCellValue("B$row", "Kode");
$sheet->setCellValue("C$row", "Bar Code");
$sheet->setCellValue("D$row", "Nama Barang");
$sheet->setCellValue("E$row", "Satuan");
$sheet->setCellValue("F$row", "Stok");
$sheet->setCellValue("G$row", "Order");
$sheet->setCellValue("H$row", "Harga");
$sheet->getStyle("A$row:H$row")->applyFromArray(array_merge($center, $allBorders));
$nmr = 0;
$str = $row;
foreach ($dtItems as $items) {
    $row++;
    $nmr++;
    $sheet->setCellValue("A$row", $nmr);
    $sheet->setCellValueExplicit("B$row", $items->Bkode,PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValueExplicit("C$row", $items->Bbarcode,PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValue("D$row", $items->Bnama);
    $sheet->setCellValue("E$row", $items->Bsatbesar);
    $sheet->setCellValue("F$row", '');
    $sheet->setCellValue("G$row", '');
    $sheet->setCellValue("H$row", '');
    $sheet->getStyle("A$row:H$row")->applyFromArray(array_merge($allBorders));
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
