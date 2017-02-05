<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{

    protected $table = "restaurants";

    public function shiftExists($day, $shift)
    {
        if (!$this->hours_open)
            return false;
        if (!$this->hours_open[$day][$shift])
            return false;
        return true;
    }

    // has many menu items
    public function menuItems()
    {
        return $this->hasMany('App\Models\MenuItem',
            'restaurant_id', 'id');
    }
}
