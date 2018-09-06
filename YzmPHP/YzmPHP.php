<?php
/**
 * 框架入口
 */
class YzmPHP
{
    //框架运行方法
    public function run(){
        $this->init_config();
        spl_autoload_register(array($this,'load'));
        // 自定义错误处理
        set_error_handler(array($this,'AppError'));
        // 自定义异常处理
        set_exception_handler(array($this,'AppException'));
        if(isset($_SERVER['REQUEST_URI'])){
            $url = $_SERVER['REQUEST_URI'];

            $_arr = explode('/',$url);

            $action = ucfirst($_arr[1]).'Action';

            if ($url=='/') {
                $action = 'IndexAction';
            }
        }
        

        if(isset($_REQUEST['mod'])){
            $action = ucfirst($_REQUEST['mod']).'Action';
        }

        $actionObj = new $action;
        $objClass = isset($_arr[2])?$_arr[2]:'index';

        if(isset($_REQUEST['action'])){
            $objClass = isset($_REQUEST['action'])?$_REQUEST['action']:'index';
        }

        $actionObj->call($actionObj,$objClass);
    }
    private function load($className){
        $data = self::core_file();
        if(isset($data[$className])){
            $path = $data[$className];
        }else if(strpos($className,'Action')!=false){
            $_str = str_replace('Action','',$className);
            $path = APP_PATH."/App/Action/{$_str}.action.php";
        }else if(strpos($className,'Model')!=false){
            $_str = str_replace('Model','',$className);
            $path = APP_PATH."/App/Model/{$_str}.model.php";
        }else{
            throw new Exception("没有找到对应的{$className}");
        }
        require $path;
    }
    public function AppError($errno,$errstr,$errfile,$errline){
        $error = '报错误时间：【'.date('Y-m-d H:i:s')."】<br>\t\t";
        $error .= '错误编号：'.$errno."<br>\t\t";
        $error .= '错误信息：'.$errstr."<br>\t\t";
        $error .= '所在的文件：'.$errfile."<br>\t\t";
        $error .= '错误行数：第'.$errline."行<br>\t\t";
        $error .= '请求的地址：'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].PHP_EOL.PHP_EOL;
        file_put_contents('Log/'.date('Y-m-d').'_error.log',$error,FILE_APPEND);
        if(APP_DEBUG == true){
            die($error);
        }
        exit;
    } 
    public function AppException($e)
    {
        file_put_contents('Log/'.date('Y-m-d').'_error_exception.log',$e->__toString().PHP_EOL,FILE_APPEND);
        if(APP_DEBUG == true){
            die($e->__toString());
        }
        exit();
    }

    private function init_config(){
        // 引入公用自定义函数
        $global = APP_PATH.'App/Util/include/global.php';
        require $global;
        // 引入基本配置文件
        $path = APP_PATH.'Config/config.php';
        if (!file_exists($path)) {
            die("配置文件不存在！");
        }
        require $path;

        // 配置数据库
        if (isset($config['mysql'])) {
            extract($config['mysql']);
            define('MYSQL_HOST',$host);
            define('MYSQL_DB',$dbname);
            define('MYSQL_USER',$mysql_user);
            define('MYSQL_PWD',$mysql_pwd);
        }

        // 配置memcache缓存
        if (isset($config['mem'])){
            extract($config['mem']);
            define('MEM_HOST',$host);
            define('MEM_PORT',$port);
        }

        // 配置redis缓存
        if (isset($config['redis'])){
            extract($config['redis']);
            define('REDIS_HOST',$host);
            define('REDIS_PORT',$port);
        }

    }

    public function core_file(){
        $_arr = array(
            'Action'            =>Lib.'/core/action.class.php',
            'ActionMiddleware'  =>APP_PATH."App/Util/ActionMiddleware.php",
            'Input'             =>Lib.'/core/Input.php',
            'YzmDbPdo'          =>Lib.'/core/YzmDbPdo.php',
            'Model'             =>Lib.'/core/Model.php',
            'MmCache'           =>Lib.'/core/MmCache.php',
            'YzmRedis'          =>Lib.'/core/YzmRedis.php',
        );
        return $_arr;
    }
}