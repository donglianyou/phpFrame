<?php 
/**
 * 核心控制器类
 */
class Action
{
    public $input;
    public function before()
    {
        $input = new Input();
        $this->input = $input->parse();
        
    }
    public function after()
    {
        
    }
    public function call($actionObj,$action){
        $this->before();
        $actionObj->$action();
        $this->after();
    }
    public function display($view,$data=array())
    {
        extract($data);
        require APP_PATH.'App/View/'.$view;
    }
    public function redirect($url,$time = 5)
    {
        echo '<script>setTimeout(window.location.href="'.$url.'",'.$time.'*1000);</script>';
    }
}