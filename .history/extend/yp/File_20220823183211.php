<?php

/**
 * @Author: Administrator
 * @Date:   2021-08-04 18:59:59
 * @Last Modified by:   Administrator
 * @Last Modified time: 2021-10-10 11:15:53
 * 文件创建类
 */

namespace yp;

use think\facade\Db;

class File
{
    private $id;
    private $mod;
    private $modFile;
    private $controllerFile;
    private $baseFileName;
    public function __construct($data = [])
    {
        $this->mod = $data;

        $this->baseFileName = $this->mod['name'];
        //基名称首字母大写
        $this->modName = ucfirst($this->baseFileName);
        //模型文件
        $this->modFile = base_path() . 'common/model/' . $this->modName . '.php';
        $this->modTraitFile = root_path() . 'extend/mod/' . $this->modName . '.php';
    }
    //创建模型文件
    public function createModFile()
    {
        $modFileCode = "<?php\r\n";
        $modFileCode .= "namespace app\common\model;\r\n";
        $modFileCode .= "use think\Model;\r\n";
        if ($this->mod['type'] == 'classic') {
            $modFileCode .= "use think\model\concern\SoftDelete;\r\n";
        }
        $modFileCode .= "class {$this->modName} extends Model {\r\n";
        $modFileCode .= "use \mod\\" . $this->modName . ";\r\n";
        $modFileCode .= "//自定义内容\r\n\r\n";
        if ($this->mod['type'] == 'classic') {
            $modFileCode .= "use SoftDelete;\r\n";
            $modFileCode .= "protected \$deleteTime = 'delete_time';\r\n";
            $modFileCode .= "protected \$defaultSoftDelete = 0;\r\n";
        }
        $modFileCode .= "}\r\n?>";
        if (!file_exists($this->modFile)) {
            file_put_contents($this->modFile, $modFileCode);
        }
        $this->createTraitFile();
    }
    public function createTraitFile()
    {
        $modFileCode = "<?php\r\n";
        $modFileCode .= "namespace mod;\r\n";
        $modFileCode .= "trait {$this->modName} {\r\n\r\n";
        foreach ($this->mod['modcolumn'] as $value) {
            $modFileCode .= $this->getColAttr($value);
        }
        $modFileCode .= "}\r\n?>";
        file_put_contents($this->modTraitFile, $modFileCode);
    }
    //删除模型文件
    public function removeMod()
    {
        if (file_exists($this->modFile)) {
            unlink($this->modFile);
        }
        if (file_exists($this->modTraitFile)) {
            unlink($this->modTraitFile);
        }
    }
    //设置事件
    public function getEvent($col, $t)
    {
        $attrCode = '';
        switch ($t) {
            case 'onAfterRead':
                //查询后事件
                $attrCode = $this->onAfterRead($col);
                break;
            case 'onBeforeInsert':
                //新增前事件
                $attrCode = $this->onBeforeInsert($col);
                break;
            case 'onAfterInsert':
                //新增后事件
                $attrCode = $this->onAfterInsert($col);
                break;
            case 'onBeforeUpdate':
                //更新前事件
                $attrCode = $this->onBeforeUpdate($col);
                break;
            case 'onAfterUpdate':
                //更新后事件
                $attrCode = $this->onAfterUpdate($col);
                break;
            case 'onBeforeWrite':
                //新增或更新前事件
                $attrCode = $this->onBeforeWrite($col);
                break;
            case 'onAfterWrite':
                //新增或更新后事件
                $attrCode = $this->onAfterWrite($col);
                break;
            case 'onBeforeDelete':
                //删除前事件
                $attrCode = $this->onBeforeDelete($col);
                break;
            case 'onAfterDelete':
                //删除后事件
                $attrCode = $this->onAfterDelete($col);
                break;
            case 'onBeforeRestore':
                //恢复前事件
                $attrCode = $this->onBeforeRestore($col);
                break;
            case 'onAfterRestore':
                //恢复后事件
                $attrCode = $this->onAfterRestore($col);
                break;
        }
        return $attrCode;
    }
    //事件开始
    private function onAfterRead($col)
    {
        $type = $col['formItem'];
        $name = $col['name'];
        return '';
    }
    private function onBeforeInsert($col)
    {
        return '';
    }
    private function onAfterInsert($col)
    {
        $name = ucfirst($col['name']);
        $codeStr = '';
        return $codeStr;
    }
    private function onBeforeUpdate($col)
    {
        $name = ucfirst($col['name']);
        $codeStr = '';
        return $codeStr;
    }
    private function onAfterUpdate($col)
    {
        return '';
    }
    private function onBeforeWrite($col)
    {
        return '';
    }
    private function onAfterWrite($col)
    {

        $name = $col['name'];
        $codeStr = '';
        switch ($col['formItem']) {
                //绑定标题图
            case 'thumb':
                $codeStr = "
				if (isset(\$data['$name'])) {
					Files::bindInfo(\$data['$name']['id'], \$data['id'], \$data['category_id'], '$this->baseFileName', '$name');
				}
			";
                break;
                //绑定相册
            case 'photo':
                $codeStr = "
				if (isset(\$data['$name'])) {
					Files::bindInfo(\$data['{$name}_post'], \$data['id'], \$data['category_id'], '$this->baseFileName', '$name');
				}
			";
                break;
                //绑定附件
            case 'files':
                $codeStr = "
				if (isset(\$data['$name'])) {
					\$ids = array_column(\$data['$name'], 'id');
					Files::bindInfo(\$ids, \$data['id'], \$data['category_id'], '$this->baseFileName', '$name');
				}
			";
                break;
                //编辑器绑定附件
            case 'editor':
                $codeStr = "
				if (isset(\$data['$name'])) {
					Files::bindEditor(\$data['id'], \$data['category_id'], '$this->baseFileName', '$name',\$data['$name']);
				}
			";
                break;
        }

        return $codeStr;
    }
    private function onBeforeDelete($col)
    {
        return '';
    }
    private function onAfterDelete($col)
    {
        return '';
    }
    private function onBeforeRestore($col)
    {
        return '';
    }
    private function onAfterRestore($col)
    {
        return '';
    }
    //事件结束
    //解析特殊字段
    public function getColAttr($col)
    {
        extract($col);
        $ucname = ucfirst($name);

        $attrCode = '';
        switch ($formitem) {
            case 'datetime':
                $attrCode .= "
//关联{$comment}
public function set{$ucname}Attr(\$value, \$data) {
	return strtotime(\$value);
}
public function get{$ucname}Attr(\$value, \$data) {
    \$value=\$value?\$value:time();
	return date('Y-m-d H:i:s',\$value);
}
			";
                break;
            case 'select':
                $attrCode .= "
                //下拉菜单
                public static function getSelected(\$pid, \$ids)
                {
                    \$data = self::whereRaw(\"FIND_IN_SET(\$pid,path)\")->select();

                    \$ids = explode(',', \$pid . ',' . \$ids);
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
                ";

                break;

            default:


                break;
        }

        return $attrCode;
    }

    //创建控制器文件
    public function createControllerFile()
    {
        //A类模型控制器
        $modname = ucfirst($this->baseFileName);
        if ($this->mod['mt'] == 1) {
            $modid = $this->mod['id'];
            $controllerFileCode = "<?php
declare (strict_types = 1);
namespace app\\admin\\controller;
use app\\common\model\\{$modname} as {$this->baseFileName}Model;
use app\\common\\model\\Tb;
use think\\facade\\Db;
use think\\facade\\Validate;
use think\\facade\\View;
class {$this->baseFileName} extends Base {
	protected function initialize() {
		parent::initialize();
		\$this->tb = Tb::where('name', '{$this->baseFileName}')->find();
		\$this->cols = \$this->tb->cols;
		\$this->rules = \$this->tb->colrule;
		\$this->table = new {$this->baseFileName}Model();
		\$this->mod = new \\app\\admin\\controller\\Ypmod(\$this->app);

		View::assign('mod', \$this->tb);
	}

	public function set{$this->baseFileName}() {
		\$data = \$this->table->find(1);
		\$form = app('form', ['$modid', \$data]); //表单对象
		View::assign('form', \$form->getForm());
		return view('form');
	}

	public function save() {
		if (\$this->request->isPost()) {
			\$data=\$this->request->param();
			\$data = \$this->tb->saveData(\$data); //组合字段内容
			//验证规则
			\$colrule = array_column(Db::name('colrule')->select()->toArray(), null, 'id');
			//获取单列
			\$cols = array_column(\$this->cols, null, 'name');
			//表单验证
			if (empty(\$this->rules)) {
				\$this->error('缺少验证规则，至少要有一个必填项！');
			} else {
				foreach (\$this->rules as \$key => \$value) {
					\$ruleStr = [];
					foreach (\$value as \$k => \$v) {
						\$ruleStr[] = \$colrule[\$v]['rule'];
					}
					\$rules[\$key] = implode('\|', \$ruleStr);
				}
				foreach (\$this->rules as \$key => \$value) {
					foreach (\$value as \$k => \$v) {
						\$msgs[\$key . '.' . \$colrule[\$v]['rule']] = str_replace(\"{col}\", \$cols[\$key]['comment'], \$colrule[\$v]['msg']);
					}
				}
				\$validate = Validate::rule(\$rules)->message(\$msgs);
				if (!\$validate->check(\$data)) {
					\$this->error(\$validate->getError());
				} else {
					//保存信息
					if (\$this->table->save(\$data)) {
						\$this->success('保存成功', (string) url('set{$this->baseFileName}'));
					} else {
						\$this->error('保存失败');
					}
				}
			}
		}
	}

	public function update() {
		\$data = \$this->tb->saveData(\$this->request->param()); //组合字段内容
		//验证规则
		\$colrule = array_column(Db::name('colrule')->select()->toArray(), null, 'id');
		//获取单列
		\$cols = array_column(\$this->cols, null, 'name');
		//表单验证
		foreach (\$this->rules as \$key => \$value) {
			\$ruleStr = [];
			foreach (\$value as \$k => \$v) {
				\$ruleStr[] = \$colrule[\$v]['rule'];
			}
			\$rules[\$key] = implode('|', \$ruleStr);
		}
		foreach (\$this->rules as \$key => \$value) {
			foreach (\$value as \$k => \$v) {
				\$msgs[\$key . '.' . \$colrule[\$v]['rule']] = str_replace(\"{col}\", \$cols[\$key]['comment'], \$colrule[\$v]['msg']);
			}
		}
		\$validate = Validate::rule(\$rules)->message(\$msgs);
		if (!\$validate->check(\$data)) {
			\$this->error(\$validate->getError());
		} else {
			//保存信息
			if (\$this->table::update(\$data)) {
				\$this->success('更新成功', (string) url('set{$this->baseFileName}'));
			} else {
				\$this->error('更新失败');
			}
		}
	}
}
?>";
        } else {
            //B类模型控制器
            $controllerFileCode = "<?php
declare (strict_types = 1);
namespace app\\admin\\controller;
use app\\common\model\\{$modname} as {$this->baseFileName}Model;
use app\\common\\model\\Tb;
use think\\facade\\Db;
use think\\facade\\Validate;
use think\\facade\\View;
class {$this->baseFileName} extends Base {
	protected function initialize() {
		parent::initialize();
		\$this->tb = Tb::where('name', '{$this->baseFileName}')->find();
		\$this->cols = \$this->tb->cols;
		\$this->rules = \$this->tb->colrule;
		\$this->table = new {$this->baseFileName}Model();
		\$this->ypmod = new \\app\\admin\\controller\\Ypmod(\$this->app);
		//是否支持搜索
		\$searchCol = \$this->tb->getSearch();
		//是否有审核
		\$this->enabled = \$this->tb->getEnabled();
		View::assign('listv', \$this->tb->listv());
		View::assign('colspan', count(\$this->tb->listv()) + 2);
		View::assign('searchCol', \$searchCol);
		View::assign('enabled', \$this->enabled);
		View::assign('mod', \$this->tb);
	}

	public function index(\$page = 1, \$keyboard = '', \$limit = 15) {
		\$map = \$this->tb->keymap(\$this->request->param());
		\$dataList = \$this->table->where(\$map)->paginate(\$limit, false, ['page' => \$page]);
		View::assign('dataList', \$dataList);
		View::assign('count', \$dataList->total());
		return view('');
	}

	public function add() {
		\$form = app('form', [\$this->tb->id]); //表单对象
		View::assign('form', \$form->getForm());
		return view('form');
	}

	public function save() {
		if (\$this->request->isPost()) {
			\$data=\$this->request->param();
			\$data = \$this->tb->saveData(\$data); //组合字段内容
			//验证规则
			\$colrule = array_column(Db::name('colrule')->select()->toArray(), null, 'id');
			//获取单列
			\$cols = array_column(\$this->cols, null, 'name');
			//表单验证
			if (empty(\$this->rules)) {
				\$this->error('缺少验证规则，至少要有一个必填项！');
			} else {
				foreach (\$this->rules as \$key => \$value) {
					\$ruleStr = [];
					foreach (\$value as \$k => \$v) {
						\$ruleStr[] = \$colrule[\$v]['rule'];
					}
					\$rules[\$key] = implode('\|', \$ruleStr);
				}
				foreach (\$this->rules as \$key => \$value) {
					foreach (\$value as \$k => \$v) {
						\$msgs[\$key . '.' . \$colrule[\$v]['rule']] = str_replace(\"{col}\", \$cols[\$key]['comment'], \$colrule[\$v]['msg']);
					}
				}
				\$validate = Validate::rule(\$rules)->message(\$msgs);
				if (!\$validate->check(\$data)) {
					\$this->error(\$validate->getError());
				} else {
					//保存信息
					if (\$this->table->save(\$data)) {
						\$this->success('保存成功', (string) url('index'));
					} else {
						\$this->error('保存失败');
					}
				}
			}
		}
	}
	public function edit(\$id) {
		\$r = \$this->table::find(\$id); //查询数据
		\$r = \$this->tb->editData(\$r); //组合字段内容
		\$form = app('form', [\$this->tb->id,\$r]); //表单对象
		View::assign('form', \$form->getForm());
		View::assign('r', \$r);
		return view('form');
	}
	public function update(\$id=0) {
		if (\$this->request->isPost()) {
			\$data = \$this->tb->saveData(\$this->request->param()); //组合字段内容
			//验证规则
			\$colrule = array_column(Db::name('colrule')->select()->toArray(), null, 'id');
			//获取单列
			\$cols = array_column(\$this->cols, null, 'name');
			//表单验证
			foreach (\$this->rules as \$key => \$value) {
				\$ruleStr = [];
				foreach (\$value as \$k => \$v) {
					\$ruleStr[] = \$colrule[\$v]['rule'];
				}
				\$rules[\$key] = implode('\|', \$ruleStr);
			}
			foreach (\$this->rules as \$key => \$value) {
				foreach (\$value as \$k => \$v) {
					\$msgs[\$key . '.' . \$colrule[\$v]['rule']] = str_replace(\"{col}\", \$cols[\$key]['comment'], \$colrule[\$v]['msg']);
				}
			}
			\$validate = Validate::rule(\$rules)->message(\$msgs);
			if (!\$validate->check(\$data)) {
				\$this->error(\$validate->getError());
			} else {
				if (\$this->table::update(\$data)) {
					\$this->success('更新成功', (string) url('index'));
				} else {
					\$this->error('更新失败');
				}
			}

		}
	}
	public function delete(\$id) {
		if (is_array(\$id)) {
			\$id = array_map('intval', \$id);
		}
		if (\$id) {
			if (\$this->table->destroy(\$id)) {
				\$this->success('删除成功');
			} else {
				\$this->error('删除失败');
			}
		} else {
			\$this->error('请选择需要删除的条目');
		}
	}
}
?>";
        }
        file_put_contents($this->controllerFile, $controllerFileCode);
    }

    //删除控制器文件
    public function removeControllerFile()
    {
        if (file_exists($this->controllerFile)) {
            unlink($this->controllerFile);
        }
    }

    //创建模板文件
    public function createView()
    {
        if ($this->mod['menu'] == 1) {
            $indexTemp = file_get_contents(APP_PATH . 'admin/view/public/indexTemp.html');
        } else {
            $indexTemp = file_get_contents(APP_PATH . 'admin/view/public/indexTemp2.html');
        }

        $viewTemp = file_get_contents(APP_PATH . 'admin/view/public/viewTemp.html');
        $formTemp = file_get_contents(APP_PATH . 'admin/view/public/formTemp.html');
        $ckeditor = ''; //编辑器
        foreach ($this->mod['cols'] as $key => $value) {
            $type = $value['formItem'];
            //加载编辑器配置文件
            if ($value['formItem'] == 'editor') {
                $ckeditor .= '
    $(function() {
        CKEDITOR.replace(\'' . $value['name'] . '\',{
            customConfig : \'__STATIC__/src/ckeditor/ypconfig.js?v={:time()}\'
        });
    });
';
            }
        }
        $formTemp .= '{block name="script"}<script>' . $ckeditor . '</script>{/block}';
        MkDirs($this->viewPath, 0755, true);
        if ($this->mod['mt'] == 1) {
            file_put_contents($this->formTemp, $formTemp);
        } else {
            file_put_contents($this->indexTemp, $indexTemp);
            file_put_contents($this->viewTemp, $viewTemp);
            file_put_contents($this->formTemp, $formTemp);
        }
    }
    //删除模板文件
    public function removeViewFile()
    {
        removeDir($this->viewPath, true);
    }
}
