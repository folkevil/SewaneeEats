<?php

namespace App\Jobs;

use App\CustomClasses\ShoppingCart\PaymentType;
use App\CustomClasses\ShoppingCart\RestaurantOrderCategory;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderRequestEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     * @param $order Order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        $managers = [];
        $m_name = "";
        if (env('APP_ENV') === "local") {
            // fake managers
            $managers = [
                'kandeta0@sewanee.edu'
            ];
            $m_name = [
                "Test Manager"
            ];
        } else if (env('APP_ENV') === "production") {
            // real managers
            $managers = [
                'kandeta0@sewanee.edu'
            ];
            $m_name = [
                'Tariro Kandemiri'
            ];
        }
        // SEND TO MANAGER
        $on_demand_order_type = RestaurantOrderCategory::ON_DEMAND;
        $weekly_order_type = RestaurantOrderCategory::WEEKLY_SPECIAL;
        $venmo_payment_type = PaymentType::VENMO_PAYMENT;
        $order = $this->order;
        $subject = "New Order Request!";
        if ($this->order->payment_type == PaymentType::VENMO_PAYMENT) {
            $subject = "New Venmo Order Request!";
        }
        // SEND TO SewaneeEats
        if (env('APP_ENV') === "production" || env('APP_ENV') == "staging") {

            $mailer->send('emails.new_order_to_manager',
                compact('order', 'weekly_order_type', 'venmo_payment_type', 'on_demand_order_type'), function ($message) use ($managers, $m_name, $subject) {
                $message->from('sewaneeeats@gmail.com');
                $message->to('sewaneeeats@gmail.com', 'SewaneeEats')->subject($subject);
            });
        }
        // send to manager
        for ($i = 0; $i < count($managers); $i++) {
            $mailer->send('emails.new_order_to_manager',
                compact('order', 'weekly_order_type', 'venmo_payment_type', 'on_demand_order_type'),
                function ($message) use ($managers, $m_name, $i, $subject) {
                    $message->from('sewaneeeats@gmail.com');
                    $message->to($managers[$i], $m_name[$i])->subject($subject);
                });
        }

        // SEND TO CUSTOMER
        $mailer->send('emails.new_order_to_customer', [
            'order' => $this->order,
            'on_demand_order_type' => $on_demand_order_type,
            'weekly_order_type' => $weekly_order_type,
            'venmo_payment_type' => $venmo_payment_type
        ],
            function ($message) {
                $message->from('sewaneeeats@gmail.com');
                $message->to($this->order->email_of_customer)->subject('SewaneeEats Order Confirmation');
            });
    }
}
