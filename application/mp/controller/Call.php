<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------


namespace app\mp\controller;
use think\facade\Config;
use think\facade\Request;

class Call
{
    private $addon;
    private $col;
    private $act;
    private $adParam;
    public function __construct()
    {
        $param = Request::param();
        $this->adParam = $param;
        session('addonRule', $param);
        $this->addon = $param['addon'];
        $this->col = $param['col'];
        $this->act = $param['act'];

    }
 /**
 * 应用调起
 * @author geeson myrhzq@qq.com
 */
    public function run(Request $request)
    {
        if ($this->addon && $this->col && $this->act) {
            session('addonName', $this->addon);
            $filename = ADDON_PATH . $this->addon . '/controller/' . ucfirst($this->col) . '.php';
            if(file_exists($commonFile=ADDON_PATH.$this->addon .'/Common.php')){
               include $commonFile;
            }
            Config::set('template.tpl_replace_string.__ADDONSTATIC__','/addons/'.$this->addon.'/static/');
            if (file_exists($filename)) {
                include_once ADDON_PATH.$this->addon.'/controller/'. ucfirst($this->col).'.php';
                $class = '\addons\\' . $this->addon . '\controller\\' . ucfirst($this->col);
                if (class_exists($class)) {
                    $model = new $class;
                    if (!method_exists($model, $this->act)) {
                        abort(500, lang($this->act.'方法不存在'));
                    }
                    return call_user_func_array([$model, $this->act],[]);
                } else {
                    abort(500, lang($class.'不存在'));
                }
            } else {
                abort(500, lang($this->col.'控制器不存在'));
            }
        }else{
            abort(500, lang($this->addon.'找不到此应用'));
        }
    }

}