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
        $objPHPExcel->getProperties()->setCreator('ypcms'); //设置创建者
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('article');
        $keyA = ord("A");
        $colum = chr($keyA);
        foreach ($header as $k => $v) {
            $colum = $this->getCol($k);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v['alias']);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum . '1')->setBold(true);
        }
        foreach ($dataList as $index => $item) {
            foreach ($header as $k => $v) {
                $colum = $this->getCol($k);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . ($index + 2), $item[$v['name']]);
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=" . date("Y-m-d His") . ".xlsx");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    public function getCol($number = 0)
    {
        $index = 'A';
        for ($i = 1; $i <= $number; ++$i) {
            ++$index;
        }
        return $index;
    }
}
