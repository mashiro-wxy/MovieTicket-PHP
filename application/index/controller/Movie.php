<?php


namespace app\index\controller;

use think\Controller;
use app\index\service\MovieService;
use think\Request;

class Movie extends Controller{
    private $movieService;
    public function __construct(Request $request = null){
        parent::__construct($request);
        $this->movieService = new MovieService();
    }
    //根据传入的状态：正在热映还是即将上映 1表示热，0表示即将
    public function get_movie_list($movie_status){
        $result = $this->movieService->getMovieList($movie_status);
        //var_dump($result);
        return $result;//适用于ajax。移动端
        //$this->assign("movie_list", $result);
        //return $this->fetch("../view/main");//适用于pc端。
    }
}