@extends('admin.main.admin_dashboard_layout')

@section('head')
    <title>
        {{ $rest->name }}
        @if($rest->isSellerType($on_demand_seller_type))
            Open Times
        @else
            Payment Time Frame
        @endif
    </title>
@stop

@section('body')
    <a href="{{ route('adminListRestaurants',['RestaurantId' => $rest->id]) }}">
        <button class="btn btn-dark" type="button">Back to Restaurant Listing</button>
    </a>
    <h3>Viewing
        @if($rest->isSellerType($on_demand_seller_type))
            Open Times
        @else
            Payment Time Frame
        @endif
        for {{ $rest->name }}</h3>
    @foreach($day_of_week_names as $day_of_week)
        @if(count($resource->getResourceTimeRangesByDay($day_of_week)) != 0)
            <h4>
                @if($rest->isSellerType($on_demand_seller_type))
                    Open Times
                @else
                    Payment Time Frame
                @endif
                starting {{ $day_of_week }}</h4>
            <ul>
                @foreach($resource->getResourceTimeRangesByDay($day_of_week) as $time_range)
                    <li>
                        {{ $time_range->getDayDateTimeString() }} |
                        <a href="{{ route('showUpdateOpenTime',
                    ['open_time_id' => $time_range->id,'rest_id' => $rest->id]) }}">
                            <button class="btn btn-dark" type="submit">Update time</button>
                        </a>
                        <form action="{{ url()->to(parse_url(route('deleteOpenTime',[]),PHP_URL_PATH),[],env('APP_ENV') !== 'local') }}"
                              method="post" style="display: inline;">
                            {{ csrf_field() }}
                            <input name="open_time_id" type="hidden" value="{{ $time_range->id }}">
                            <button class="btn btn-danger">Delete Restaurant Open Time</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    @endforeach
@stop