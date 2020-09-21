<?php


namespace app\index\controller;


use app\index\service\CinemaService;
use app\index\service\FilmScheduleService;
use app\index\service\ScreeningRoomService;
use app\index\service\OrderService;
use app\index\service\MovieService;
use think\Controller;
use think\Request;

class FilmSchedule extends Controller {
    private $filmScheduleService;
    private $screeningRoomService;
    private $movieService;
    private $orderService;
    private $cinemaService;

    public function __construct(Request $request = null){
        parent::__construct($request);
        $this->filmScheduleService = new FilmScheduleService();
        $this->screeningRoomService = new ScreeningRoomService();
        $this->movieService = new MovieService();
        $this->orderService = new OrderService();
        $this->cinemaService = new CinemaService();
    }

    public function get_schedule_list($movie_id, $order_date, $cinema_id){


        $result = $this->filmScheduleService
            ->getScheduleList($movie_id, $order_date, $cinema_id);
        return $result;
    }

    //传入参数schedule_id。获取movie_id， screening_room_id, price等。
    //用来把数据传给显示页面（包含）座位显示，已经销售的座位显示。
    //把已售座位信息取出来放入到网页端缓存
    public function show_room_seat($schedule_id){
        //获取movie_id， screening_room_id, price信息，并且把它送入网页端
        $schedule_info = $this->filmScheduleService->getAScheduleInfo($schedule_id);
        //var_dump($schedule_info);
        //根据放映厅_id获取座位信息.
        $screening_room_id = $schedule_info[0]["screening_room_id"];
        $room_info = $this->screeningRoomService->getAScreeningRoomInfo($screening_room_id);
        //var_dump($room_info);
        $seats = $room_info[0]["seat"];
        //var_dump($seats);
        $seat_2 = array();
        $arr_seats  = explode("#", $seats);
        //var_dump($arr_seats);
        foreach ($arr_seats as $value){
            $row = null;
            for($i=0;$i<strlen($value);$i++){
                $row[] = $value[$i];
            }
            array_push($seat_2, $row);
        }
        //var_dump($seat_2);

        $movie_id = $schedule_info[0]["movie_id"];
        $movie_info = $this->movieService->getAMovieInfo($movie_id);
        //var_dump($movie_info);
        $saled_seats = $this->orderService->getSaledSeatList($schedule_id);
        //ar_dump($saled_seats);
        //把上述数据传入到网页端。
        $cinema = $this->cinemaService->getCinemaName($room_info[0]["cinema_id"]);
        $this->assign("time", $schedule_info[0]["schedule_time"]);
        $this->assign("date", $schedule_info[0]["schedule_date"]);
        $this->assign("cinema_name", $cinema["cinema_name"]);
        $this->assign("schedule_id", $schedule_info[0]["schedule_id"]);
        $this->assign("room_name", $room_info[0]["screening_room_name"]);
        $this->assign("movie_info", $movie_info);
        $this->assign("seats", $seat_2);
        $this->assign("actual_price", $schedule_info[0]["actual_price"]);
        $this->assign("saled_seats", $saled_seats);

        return $this->fetch("../view/show_seats");
    }
    //获取计划id，获取座位列表。总金额。和单价金额。
    public function order_ticket($schedule_id, $seats, $order_money, $order_item_money, $movie_id, $time){
        //初始化订单总表对象. 初始化订单明细对象。譬如票和座位
        $seat_list = explode("|", $seats);
        //1:初始化订单总表信息
        $user_id = 1;//应该从session里面获取user_id的信息
        $order = ['user_id' => $user_id, 'order_money' => $order_money,
                  'schedule_id' => $schedule_id,
                  'order_status' => 0,
                  'order_time' => $time,
                  'movie_id' => $movie_id];

        //2：初始化订单明细表信息
        $order_item_list = array();
        for ($i = 0; $i <count($seat_list); $i++){
            $order_item = ['order_item_money' => $order_item_money,
                            'schedule_id' => $schedule_id,
                            'seat_x_y' => $seat_list[$i],
                            'order_id' => 0];
            array_push($order_item_list, $order_item);
        }
        //3：调用相应的函数进行插入订单的操作。
        $flag = $this->orderService->insertOrder($order, $order_item_list);
        if ($flag == 0 || $flag == false){
            //说明插入出错了。跳转到错误的界面去提示
        }
        else{
            //成功考虑跳转会主界面
            $this->redirect('main_page/show_main_page_info');
        }
    }


}