<?php


namespace app\index\service;
use app\index\model\OrderInfo;
use app\index\model\OrderItem;

use think\Db;
class OrderService{
    public function getSaledSeatList($schedule_id){
        $sqlTxt = "select seat_x_y from order_item where schedule_id = " . $schedule_id;
        $result = Db::query($sqlTxt);
        $sale_seats = array();
        foreach ($result as $item){
            array_push($sale_seats, $item["seat_x_y"]);
        }
        //var_dump($sale_seats);
        return implode('|',$sale_seats);
    }

    public function insertOrder($order, $order_item_list){
        $flag = 0;
        $order_info = new OrderInfo();
        Db::startTrans();
        try {
            $order_info->data($order);
            $flag = $order_info->save();
            $order_id = $order_info->order_id;
            //请同学们参考thinkphp，把自增长的主键id作为外键放入到order_item里面去。
            for($i = 0; $i < count($order_item_list); $i++){
                $order_item = new OrderItem();
                //$order_item->order_id = $order_id;
                $order_item_list[$i]['order_id'] = $order_id;
                $order_item->data($order_item_list[$i]);
                $order_item->save();
            }
            Db::commit();
        }catch (Exception $ex){
            Db::rollback();
        }
        return $flag;
    }

    public function payOrder($order_id){
        Db::startTrans();
        try {
            //1支付。
            //2:修改订单状态，把order_status改成已经支付状态。相当于update.
            $order_info = new OrderInfo();
            $order_info->save(['order_status' => 1],['order_id' => $order_id]);
            Db::commit();
        }catch (Exception $ex){
            Db::rollback();
        }
    }
}