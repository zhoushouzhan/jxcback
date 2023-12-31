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
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
    }
}
