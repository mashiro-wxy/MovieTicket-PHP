<?php


namespace app\index\service;
use app\index\model\CinemaInfo;
use app\index\model\FilmSchedule;
use app\index\model\MovieInfo;
use think\Db;

class CinemaService{
    public function getCinemaRank(){
        $cinema = new CinemaInfo();
        $result = $cinema::all(["score" >= 8 ]);
        $array = array();
        foreach ($result as $item){
            array_push($array, $item->toArray());
        }
        return $array;
    }
    public function getCinemaName($cinema_id){
        $cinema = new CinemaInfo();
        $result = $cinema::get(['cinema_id' => $cinema_id]);
        return $result->toArray();
    }
    //根据电影的id。找到电影院列表。需要通过排片计划表才能找到。
    public function getCinemaList($movie_id, $date){
        //1:根据电影id和时间找到排片计划表面的room_id;
        //根据room_id找到cinema_id在找出电影院信息
        $sqlTxt = "SELECT * from cinema_info where cinema_id in " .
                    "(SELECT DISTINCT cinema_id from screening_room where screening_room_id in " .
                    "(SELECT screening_room_id from film_schedule WHERE " .
                    " movie_id = " . $movie_id . "  and " .
                    " schedule_date = '". $date ."'))";
        $result = Db::query($sqlTxt);

        return $result;
    }


}