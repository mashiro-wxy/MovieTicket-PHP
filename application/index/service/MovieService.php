<?php


namespace app\index\service;
use app\index\model\CinemaInfo;
use app\index\model\OrderInfo;
use app\index\model\OrderItem;
use app\index\model\FilmSchedule;
use app\index\model\ScreeningRoom;
use app\index\model\MovieInfo;
class MovieService{
    public function getMovieList($movie_status, $movie_type){

        if ($movie_type == "全部")
            $result = MovieInfo::all(['movie_status' => $movie_status]);
        else
            $result = MovieInfo::all(['movie_status' => $movie_status, 'type' => $movie_type]);
        //var_dump($result);
        $movie_list = array();
        foreach ($result as $item){//如果数据不为空，把太转换成二维数组，在返回
            array_push($movie_list, $item->toArray());
        }
        return $movie_list;
    }
    //得到当前最热或者主推的影片。如何设置hot是管理员的功能，这边不用管，只取就行了
    public function getHotMovie(){
        $result = MovieInfo::get(['hot' => 1]);
        //var_dump($result);
        return $result->toArray();
    }
    //得到当前电影票房排行榜
    public function getMovieRank(){
        $movie = new MovieInfo();
        $result = $movie::all();
        $array = array();
        foreach ($result as $value)
            array_push($array, $value->toArray());
        return $array;
    }

    //根据movie_id得到该部电影在界面上显示的基本信息。
    public function getAMovieInfo($movie_id){
        $result = MovieInfo::get(['movie_id' => $movie_id]);
        return $result->toArray();
    }
    public function RecordsOfConsumption($user_id){
        $order_info = OrderInfo::all(['user_id' => $user_id]);
        $order_info_list = array();
        foreach ($order_info as $item){//如果数据不为空，把太转换成二维数组，在返回
            array_push($order_info_list, $item->toArray());
        }
        $order_item = array();
        for ($i = 0; $i < count($order_info_list); $i++){
            $order = OrderItem::all(['order_id' => $order_info_list[$i]['order_id']]);
            $order_list = array();
            $order_list["order_info"] = $order_info_list[$i];
            $order_item_list = array();
            foreach ($order as $item){//如果数据不为空，把太转换成二维数组，在返回
                array_push($order_item_list, $item->toArray());
            }
            for ($j = 0; $j < count($order_item_list); $j++){
                $seat_x_y = $order_item_list[$j]['seat_x_y'];
                $var = explode("_", $seat_x_y);
                $order_item_list[$j]['seat_x'] = intval($var[0]);
                $order_item_list[$j]['seat_y'] = intval($var[1]);
            }
            $order_list["seat"] = $order_item_list;
            $movie_info = MovieInfo::get(['movie_id' => $order_info_list[$i]['movie_id']]);
            $movie_info_list = $movie_info->toArray();
            $order_list['movie_info'] = $movie_info_list;
            $schedule_id = FilmSchedule::get(['schedule_id' => $order_info_list[$i]['schedule_id']]);
            $schedule_id_list = $schedule_id->toArray();

            $screening_room = ScreeningRoom::get(['screening_room_id' => $schedule_id_list['screening_room_id']]);
            $screening_room_list = $screening_room->toArray();

            $order_list['screening_room'] = $screening_room_list;
            $cinema_info = CinemaInfo::get(['cinema_id' => $screening_room['cinema_id']]);
            $cinema_info_list = $cinema_info->toArray();

            $order_list['cinema_info'] = $cinema_info_list;
            array_push($order_item, $order_list);
        }
        return $order_item;
    }
}