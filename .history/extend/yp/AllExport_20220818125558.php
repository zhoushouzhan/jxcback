<?php

namespace yp;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;

class Allexport
{
    public function toxlsx($header = [], $dataList = [])
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('student');
        $keyA = ord("A");
        $colum = chr($keyA);


        foreach ($header as $k => $v) {

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', '1');
        }



        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=003.xlsx");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
