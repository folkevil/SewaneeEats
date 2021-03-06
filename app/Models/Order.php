<?php

namespace App\Models;

use App\Contracts\HasItems;
use App\CustomClasses\ShoppingCart\CartItem;
use App\CustomClasses\ShoppingCart\ItemType;
use App\CustomClasses\ShoppingCart\RestaurantOrderCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model implements HasItems
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = "orders";


    // gets the courier types that can deliver this order

    public static function getWeeklySpecialOrders()
    {
        $orders = Order::all();
        $ret_orders = [];
        foreach ($orders as $order) {
            if ($order->hasOrderType(RestaurantOrderCategory::WEEKLY_SPECIAL)) {
                $ret_orders[] = $order;
            }
        }
        return $ret_orders;
    }

    /**
     * @param $order_type integer constant from class RestaurantOrderCategory
     * @return boolean returns true if the order has the given $order_type
     * false otherwise
     */
    public function hasOrderType($order_type)
    {
        $order_types = json_decode($this->order_types, true);
        foreach ($order_types as $ot) {
            if ($ot == $order_type) {
                return true;
            }
        }
        return false;
    }

    public function getCourierTypes()
    {
        return json_decode($this->courier_types, true);
    }

    // returns array of CartItem that contains the menu items for this order

    public function menuItemOrders()
    {
        return $this->hasMany('App\Models\MenuItemOrder', 'order_id', 'id');
    }

    public function toOnDemandRestBuckets()
    {
        $items = [];
        foreach ($this->menuItemOrders as $item) {
            if ($item->item->restaurant->isSellerType(RestaurantOrderCategory::ON_DEMAND)) {
                $items[$item->item->restaurant->name][] = $item;
            }
        }
        return $items;
    }

    public function items()
    {
        $items = [];
        $i = 0;
        foreach ($this->menuItemOrders as $menu_item_order) {
            $menu_item = $menu_item_order->item;
            if ($menu_item->getSellerEntity()->getSellerType() == RestaurantOrderCategory::ON_DEMAND
                || $menu_item->getSellerEntity()->getSellerType() == RestaurantOrderCategory::WEEKLY_SPECIAL
            ) {
                $items[] = new CartItem($menu_item->id, ItemType::RESTAURANT_ITEM);
                $items[$i]->setExtras($menu_item_order->accessories);
                $items[$i]->setInstructions($menu_item_order->special_instructions);
            } else if ($menu_item->getSellerEntity()->getSellerType() == RestaurantOrderCategory::EVENT) {
                $items[] = new CartItem($menu_item->id, ItemType::EVENT_ITEM);
            }
        }
        return $items;
    }

    public function restaurants()
    {
        return $this->belongsToMany('App\Models\Restaurant', 'restaurants_orders',
            'order_id', 'restaurant_id');
    }

    public function orderRestsToString()
    {
        $rests = $this->getRestaurants();
        $str = "";
        $rest_len = count($rests);
        for ($i = 0; $i < $rest_len; $i++) {
            $str .= ($i != $rest_len - 1) ? $rests[$i] . "," : $rests[$i];
        }
        return $str;
    }

    public function getRestaurants()
    {
        $rest_ids = [];
        $restaurants = [];
        foreach ($this->menuItemOrders as $item_order) {
            $rest = $item_order->item->restaurant;
            if (!in_array($rest->id, $rest_ids)) {
                $restaurants[] = $rest;
                $rest_ids[] = $rest->id;
            }
        }
        return $restaurants;
    }

    public function orderPriceInfo()
    {
        return $this->hasOne('App\Models\OrderPriceInfo', 'order_id');
    }

    public function couriers()
    {
        return $this->belongsToMany('App\User', 'couriers_orders',
            'order_id', 'courier_id')->withPivot('courier_payment')->withTimestamps();
    }

    public function getCourier()
    {
        $couriers = $this->couriers;
        if (count($couriers) >= 1) {
            return $couriers[0];
        }
        return null;
    }

    public function scopeUndelivered($query)
    {
        return $query->where('is_delivered', false);
    }

    public function scopeBeingProcessed($query, $bool)
    {
        return $query->where('is_being_processed', $bool);
    }

    public function scopeDelivered($query)
    {
        return $query->where('is_delivered', true);
    }

    public function scopeCancelled($query)
    {
        return $query->where('is_cancelled', true);
    }

    public function hasCourier()
    {
        return count($this->couriers) == 1;
    }

    /**
     * @param $courier_type integer constant from class CourierTypes
     * @return boolean returns true if the order has the given $courier_type
     * false otherwise
     */
    public function hasCourierType($courier_type)
    {
        $courier_types = json_decode($this->courier_types, true);
        foreach ($courier_types as $ct) {
            // check all the courier types that can fulfill this order
            // and return true if $courier_type matches
            if ($ct == $courier_type) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return int the time in minutes since the order was received
     */
    public function timeSinceOrdering()
    {
        $order_received = new Carbon($this->created_at);
        return Carbon::now()->diffInMinutes($order_received);
    }

    public function showCourierTypes()
    {
        $courier_types = json_decode($this->courier_types);
        \Log::info($courier_types);
    }

    /**
     * Returns a queury that will retrieve all orders that are pending
     * WARNING: This includes all weekly special orders too
     * @param $query
     * @return mixed
     */
    public function scopePending($query)
    {
        // must be undelivered, not being processed,
        // not paid for yet, not cancelled (implies not refunded)
        //
        return $query->where([
            'is_delivered' => false,
            'is_being_processed' => false,
            'was_refunded' => false,
            'is_cancelled' => false,
        ]);
    }

    /**
     * @param $payment_type integer constant from the class PaymentTypes
     * @return boolean true if the given $payment_type is the same as the
     * order's payment type, false otherwise
     */
    public function wasPaidWith($payment_type)
    {
        return $this->payment_type == $payment_type;
    }

    public function scopeRefunded($query)
    {
        return $query->where('was_refunded', true);
    }

    public function scopeNotPaidFor($query)
    {
        return $query->where('is_paid_for', false);
    }

    public function scopePaidFor($query)
    {
        return $query->where('is_paid_for', true);
    }

    public function scopeCountable($query)
    {
        return $query->where(['is_cancelled' => false, 'was_refunded' => false]);
    }
}
