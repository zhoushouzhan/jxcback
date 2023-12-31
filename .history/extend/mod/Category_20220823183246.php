<?php
namespace mod;
trait Category {


                //下拉菜单
                public static function getSelected($pid, $ids)
                {
                    $data = self::whereRaw("FIND_IN_SET($pid,path)")->select();

                    $ids = explode(',', $pid . ',' . $ids);
                    $selectNode = [];
                    foreach ($ids as $id) {
                        $item = [];
                        foreach ($data as $k => $v) {
                            if ($v['pid'] == $id) {
                                $item[] = $v;
                            }
                        }
                        if ($item)
                            $selectNode[] = $item;
                    }
                    return $selectNode;
                }
                }
?>