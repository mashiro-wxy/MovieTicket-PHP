<?php


namespace app\index\controller;

use app\index\model\CinemaInfo;
use app\index\model\OrderInfo;
use app\index\model\OrderItem;
use app\index\model\FilmSchedule;
use app\index\model\ScreeningRoom;
use app\index\model\UserInfo;
use app\index\service\MovieService;
use app\index\service\CinemaService;
use app\index\model\MovieInfo;
use think\Controller;
use think\Request;

class MainPage extends Controller {
    private $movieService;
    private $cinemaService;
    public function __construct(Request $request = null){
        parent::__construct($request);
        $this->movieService = new MovieService();
        $this->cinemaService = new CinemaService();
    }
    public function test(){
        echo "aaabbb";
    }
    public function get_movie_list($movie_type){
        $movie_status = 1;
        $result = $this->movieService->getMovieList($movie_status, $movie_type);
        //var_dump($result);
        return $result;
    }
    public function show_person(){
        $user_id = 1;
        $user = UserInfo::get(['user_id' => $user_id]);
        $user = $user->toArray();
        $user_name = $user['user_nickname'];
        $order_item = $this->movieService->RecordsOfConsumption($user_id);

        $this->assign("order_list", array_reverse($order_item));
        $this->assign("name", $user_name);
        return $this->fetch("../view/person");
    }
    public function show_main_page_info(){
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
    }
}