<?php

namespace yp;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;

class Allexport
{
    public function toxlsx()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties();
        $keyA = ord("A");
        $colum = chr($keyA);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', '1');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=003.xlsx");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
