<?php

namespace Tests\Unit\Controllers\OrderFlow;

use App\CustomClasses\Availability\TimeRangeType;
use App\CustomClasses\ShoppingCart\RestaurantOrderCategory;
use App\CustomClasses\ShoppingCart\ShoppingCart;
use App\Http\Controllers\SellerEntityController;
use App\Models\Restaurant;
use App\Models\TimeRange;
use Carbon\Carbon;
use Tests\TestCase;

class SellerEntityControllerTest extends TestCase
{

    public function showsThatWeAreClosed()
    {

    }

    /**
     * @test
     * Assert that if a menu item in the cart expires that the user is redirected and a
     * message is flashed pertaining to the auto removed items
     */
    public function itFlashesBecameUnavailableOnAutoCartRemoval()
    {
        $cart_mock = $this->mock(ShoppingCart::class);
        $cart_mock->shouldReceive('checkMenuItemAndRestaurantAvailabilityAndDelete')
            ->once()->andReturn(['blah']);
        $seller_entity_controller = new SellerEntityController($cart_mock);
        $this->assertSessionHas('became_unavailable', ['blah']);
    }

    /**
     * @test
     */
    public function redirectsOnOnDemandRestaurantNonAvailability()
    {
        $this->makeShiftNow();
        $cart_mock = $this->mock(ShoppingCart::class);
        $cart_mock->shouldReceive('checkMenuItemAndRestaurantAvailabilityAndDelete')
            ->once()->andReturn(null);
        $seller_entity_controller = new SellerEntityController($cart_mock);
        $rest_mock = $this->mock(Restaurant::class);
        $rest_mock->shouldReceive('findOrFail')
            ->once()->andReturn($rest_mock);
        // we have an on demand restaurant
        $rest_mock->shouldReceive('isSellerType')
            ->with(RestaurantOrderCategory::ON_DEMAND)->andReturn(true);
        $rest_mock->shouldReceive('isAvailableNow')
            ->andReturn(false);
        $showMenuRet = $seller_entity_controller->showMenu($rest_mock, 1);
        $this->assertSessionHas('status_bad', 'Sorry this restaurant is not available right now');
        // expect that we redirect back
        self::assertInstanceOf('\Illuminate\Http\RedirectResponse', $showMenuRet);
    }

    private function makeShiftNow()
    {
        \Artisan::call('migrate');
        // make it so that shift now is true
        $rest = factory(Restaurant::class)->create(['seller_type' => RestaurantOrderCategory::ON_DEMAND]);
        // make a time_range that is available now
        $time_range = factory(TimeRange::class)->create([
            'start_dow' => Carbon::now()->subHours(3)->format('l'),
            'end_dow' => Carbon::now()->addHour(4)->format('l'),
            'start_hour' => Carbon::now()->subHours(3)->hour,
            'end_hour' => Carbon::now()->addHours(4)->hour,
            'time_range_type' => TimeRangeType::SHIFT
        ]);
    }

    /**
     * @test
     * The controller will redirect when the restaurant the user requested
     * is not available, tests for weekly special restaurants
     */
    public function redirectsOnWeelySpecialRestaurantNonAvailability()
    {
        $cart_mock = $this->mock(ShoppingCart::class);
        $cart_mock->shouldReceive('checkMenuItemAndRestaurantAvailabilityAndDelete')
            ->once()->andReturn(['blah']);
        $seller_entity_controller = new SellerEntityController($cart_mock);
        $rest_mock = $this->mock(Restaurant::class);
        $rest_mock->shouldReceive('findOrFail')
            ->once()->andReturn($rest_mock);
        // we have a weekly special restaurant
        $rest_mock->shouldReceive('isSellerType')
            ->with(RestaurantOrderCategory::ON_DEMAND)->andReturn(false);
        $rest_mock->shouldReceive('isAvailableNow')
            ->andReturn(false);
        $showMenuRet = $seller_entity_controller->showMenu($rest_mock, 1);
        $this->assertSessionHas('status_bad', 'Sorry this restaurant is not available right now');
        // expect that we redirect back
        self::assertInstanceOf('\Illuminate\Http\RedirectResponse', $showMenuRet);
    }

    public function stuff()
    {

    }
}