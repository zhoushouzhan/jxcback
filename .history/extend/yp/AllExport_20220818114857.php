<?php

namespace yp;

use PHPExcel_IOFactory;

class Allexport
{
    public function toXLSX()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
    }
}
