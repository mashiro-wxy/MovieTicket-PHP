<?php


namespace app\index\service;

use think\Db;
class FilmScheduleService{
    //得到了排片计划表。
    public function getScheduleList($movie_id, $date, $cinema_id){
        $sqlTxt = "SELECT f.schedule_id, f.schedule_date, f.schedule_time," .
                  " f.screening_room_id, f.movie_id, f.price, f.actual_price," .
                  " s.screening_room_name " .
                  " FROM film_schedule f " .
                  " INNER JOIN screening_room s ON " .
                  " f.screening_room_id = s.screening_room_id " .
                  " WHERE f.movie_id=" . $movie_id . " AND f.schedule_date=" . $date .
                  " AND f.screening_room_id IN ".
                  " (SELECT screening_room_id from screening_room " .
                  " WHERE cinema_id = " . $cinema_id .")";
        //echo $sqlTxt;
        $result = Db::query($sqlTxt);
        return $result;
    }
    //根据schedule_id获取该场次的基本信息，包括场次id，电影id，放映厅id，票价和实际票价等
    public function getAScheduleInfo($schedule_id){
        $sqlTxt = "select * from film_schedule where schedule_id = " . $schedule_id;
        $result = Db::query($sqlTxt);

        return $result;
    }
}