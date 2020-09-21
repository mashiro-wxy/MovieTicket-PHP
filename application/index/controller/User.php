<?php

namespace app\index\controller;
use app\index\service\CinemaService;
use app\index\service\MovieService;
use app\index\service\UserService;
use think\Controller;
use app\index\model;
use app\index\model\UserInfo;
use think\Db;
use think\Request;
use think\Session;

class User extends Controller{
    private $movieService;
    private $cinemaService;
    private $userService;
    public function __construct(Request $request = null){
        parent::__construct($request);
        $this->userService = new UserService();
        $this->movieService = new MovieService();
        $this->cinemaService = new CinemaService();
    }

    public function login(){
        return $this->fetch("../view/login");
    }
    public function check_login($user_nickname, $pwd){

        $result = $this->userService->checkLogin($user_nickname, $pwd);
        if ($result == null)
            return $this->redirect("login");
        else{
            //存入session
            Session::start();//Session::set(["user_nickname"=> $user_nickname, "pwd"=>$pwd]);
            Session::set("user_nickname", ["user_nickname"=> $user_nickname, "pwd"=>$pwd]);

            return $this->redirect("show_main", 302);
        }
    }
    public function show_main(){
        //1:得到最热推荐的单部影片
        $hot_movie = $this->movieService->getHotMovie();

        //2:得到热映清单
        $movie_status = 1;
        $movie_type = "全部";
        $movie_list = $this->movieService->getMovieList($movie_status, $movie_type);

        //赋值到网页
        $this->assign("a_hot_movie", $hot_movie);
        $this->assign("hot_movie_list", $movie_list);
        $this->assign("if", 0);

        return $this->fetch("../view/main");
        //return $this->redirect("show_main_page_info", 302);
    }
}