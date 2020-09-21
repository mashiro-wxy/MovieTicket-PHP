<?php


namespace app\index\service;

use think\Db;

class ScreeningRoomService{
    public function getAScreeningRoomInfo($screening_room_id){
        $sqlTxt = "select * from screening_room where screening_room_id = " . $screening_room_id;
        $result = Db::query($sqlTxt);

        return $result;
    }
}