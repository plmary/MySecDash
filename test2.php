<?php 
include( 'Constants.inc.php' );
require_once CHEMIN_APPLICATION . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$activeWorkSheet = $spreadsheet->getActiveSheet();
$activeWorkSheet->setCellValue('A1', 'Hello Wolrd!');

$write = new Xlsx($spreadsheet);
$write->save('Rapports/hello world.xlsx');
?>
