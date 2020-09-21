<?php
namespace app\index\service;
use app\index\model;
use app\index\model\UserInfo;
use think\Db;
class UserService{

    public function checkLogin($user_nickname, $pwd){
        $user = new model\UserInfo();
        //等同于sql语句：select * form user_info where user_nickname=$user_nickname and pwd = $pwd
        $result = $user::get(["user_nickname"=> $user_nickname, "pwd"=> $pwd]);

        return $result;
    }
}