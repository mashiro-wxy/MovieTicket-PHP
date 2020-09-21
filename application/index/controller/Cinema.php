<?php


namespace app\index\controller;
use app\index\model\CinemaInfo;
use app\index\service\CinemaService;
use app\index\service\MovieService;

use think\Controller;
use think\Db;
use think\Request;

class Cinema extends Controller{
    private $cinemaService = null;
    private $movieService = null;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->cinemaService = new CinemaService();
        $this->movieService = new MovieService();
    }

    public function get_cinema_list_by_movie($movie_id, $date){
        //得到要选择的电影的基本信息，并放入网页缓存
        $a_movie_info = $this->movieService->getAMovieInfo($movie_id);
        $this->assign("a_movie_info", $a_movie_info);

        //result的值是电影院的列表显示,并放入网页缓存
        $result = $this->cinemaService->getCinemaList($movie_id, $date);
        $this->assign("cinema_list", $result);
        //把当前日期往后推3天构成数组放入网页缓存
        $dates = ['one' => date("y-m-d"),
                  'two' => date("y-m-d", strtotime('+1 day')),
                  'three' => date("y-m-d", strtotime('+2 day')),
                  'four' => date("y-m-d", strtotime('+3 day'))];
        $this->assign("dates", $dates);

        return $this->fetch("../view/choose_date_cinema");
    }
}