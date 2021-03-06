<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Relates menu items to orders
        // was menu_items_orders changed to orders_items
        Schema::create('menu_items_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('menu_item_id')->unsigned()->nullable();
            $table->integer('event_item_id')->unsigned()->nullable();
            $table->string('special_instructions')->nullable();
            $table->boolean('was_refunded'); // individual menu items can be refunded

            // either add create json array of accessories for this menu_item_order
            // or create a table joining a menu_item_order with
            // the accessories table in a many-to-many relationship

            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade');
            $table->foreign('menu_item_id')
                ->references('id')->on('menu_items');
            $table->foreign('event_item_id')
                ->references('id')->on('event_items');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_items_orders');
    }
}
