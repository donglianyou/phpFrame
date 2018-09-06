<?php
/**
 * 首页控制器
 */
class IndexAction extends ActionMiddleware
{
    public function index(){
        //$userId = $this->input['user_id'];
        $name = 'Yzm';
        $age = 121;
        $data = array(
            'name'=>$name,
            'age'=>$age,
        );
        $model = new YzmDbPdo();
        $sql = "select * from class";
        $_data = $model->getRows($sql);
        echo ("<pre>");
        print_r($_data);
        $this->display('index/index.html',$data);
    }
    public function age()
    {
        echo "age";
    }
    public function mem()
    {
        //setVar('age','21');
        //echo getVar("age");
        // delVar("name");
        phpinfo();
    }
    public function redis()
    {
        $redis = R();
        //$redis->set("name","good");
        //$redis->lpush('sql','select * from user');
        //echo $redis->get('name');
        $sql = $redis->rpop('sql');
        dump($sql);
    }
}