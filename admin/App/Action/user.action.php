<?php 
/**
  * 用户控制器
  */
 class UserAction extends ActionMiddleware
 {
    //  显示用户
     public function index()
     {
         $model = new userModel();
         $data = $model->findAll();
         $this->display('user/index.html',$data);
     }
    //  查看用户详情
     public function view(){
        extract($this->input);
        $user_id = isset($user_id)?$user_id:0;
        $model = new userModel();
        $data = $model->find($user_id);
        $this->display('user/view.html',array(
            'data'=>$data
        ));
     }
    //  添加用户
     public function add()
     {
         extract($this->input);
         $username = isset($username)?$username:'';
         $password = isset($password)?$password:'';
         $statu = isset($statu)?$statu:'';
         $time = time();
         if($isPost){
            $model = new userModel();
            $data = array(
                'username' =>$username,
                'password' =>$password,
                'statu' =>$statu,
                'time' =>$time
            );

            $last_id = $model->add($data);
            if($last_id>0){
                $this->redirect('/user/index/');
            }
         }
         $this->display("user/add.html");
     }

    //  编辑用户
    public function edit()
    {
        extract($this->input);
        $user_id = isset($user_id)?$user_id:0;
        $username = isset($username)?$username:'';
        $password = isset($password)?$password:'';
        $statu = isset($statu)?$statu:'';
        $time = time();
        $model = new userModel();
        if($isPost){
            $arr = array(
                'username' =>$username,
                'password' =>$password,
                'statu' =>$statu,
                'time' =>$time
            );
            $where = "where id='{$user_id}'";
            $rs = $model->edit($arr,$where);
            if ($rs) {
                $this->redirect('/user/index/');
            }
        }
        $data = $model->find($user_id);
        $this->display('user/edit.html',array(
            'data' =>$data
        ));
    }

    // 删除用户
    public function del()
    {
        extract($this->input);
        $user_id = isset($user_id)?$user_id:0;
        if($user_id>0){
            $model = new userModel();
            $where = "where id='{$user_id}'";
            $rs = $model->del($where);
            if ($rs) {
                $this->redirect('/user/index/');
            }
        }
    }

 }