<?php
if ($output == "2") {
    require_once(LIBRARY . "PHPExcel.php");
    include("itemlist.xls.php");
} else {
    include("itemlist.web.php");
}