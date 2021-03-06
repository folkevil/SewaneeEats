@extends('admin.main.admin_dashboard_layout')

@section('head')
    <title>Schedule</title>
@stop

<style>
    .heading {
        font-size: 24px;

    }
</style>

@section('body')
    <div class="clearfix"></div>

    <div class="container" id="new-shift-container">
        <a href="{{ route('showCreateShift')  }}">
            <button class="btn btn-dark" type="button">Add a New Shift</button>
        </a>
        <div>
            <p class="heading">
                @if(empty($shift))
                    The are currently no shifts for the schedule
                @else
                    Schedule for
                    week {{ $start_of_week->toDayDateTimeString() . ' - ' . $end_of_week->toDayDateTimeString() }}
                @endif
            </p>
            <div id="container">
                <div id="left">
                    @foreach($days_of_week as $dow)
                        @include('admin.schedule.couriers_managers_for_shift',
                            ['shift_day' => $dow])
                    @endforeach
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>

    <script>
      $('.btn-assign-workers').each(function () {
        var assignWorkersButton = $(this);
        assignWorkersButton.on('click', function () {
          toggleUnassigned(assignWorkersButton, assignWorkersButton.data('shift-id'));
        })
      });
      function toggleUnassigned(assignedWorkersButton, shiftId) {
        var unassignedDiv = $('#unassigned-' + shiftId);
        if (!unassignedDiv.is(':visible')) {
          assignedWorkersButton.text('Hide unassigned workers');
          unassignedDiv.show();
        } else {
          assignedWorkersButton.text('Add Workers To Shift');
          unassignedDiv.hide();
        }
      }
    </script>
@stop