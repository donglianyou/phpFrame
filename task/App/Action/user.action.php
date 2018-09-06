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
         dump($data);
     }
 }