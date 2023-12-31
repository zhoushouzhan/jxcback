<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-18 11:44:39
 * @LastEditTime: 2022-08-18 20:40:37
 * @FilePath: \web\extend\yp\Ypexcel.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace yp;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;

class Ypexcel
{
    public function toxlsx($header = [], $dataList = [])
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator('ypcms'); //设置创建者
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('article');
        $keyA = ord("A");
        $colum = chr($keyA);

        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(32);
        foreach ($header as $k => $v) {
            $colum = $this->getCol($k);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v['alias']);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getFont()->setBold(true);
        }
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
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
    public function formxlsx($filepath, $cols)
    {
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load($filepath, $encode = 'utf-8');
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($j = 3; $j <= $highestRow; $j++) {
        }
        return $
    }
}
