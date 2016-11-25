@extends('layouts.app')

@section('title', 'User Calendar')

@section('content')

<div class="">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title"><h3>User Calendar <small>Click to add/edit events</small></h3></div>
                <div class="x_content">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- calendar modal -->
<div id="CalenderModalNew" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">New Calendar Entry</h4>
            </div>
            <div class="modal-body">
                <div id="testmodal" style="padding: 5px 20px;">
                    <form id="antoform" class="form-horizontal calender" role="form">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">User</label>
                            <div class="col-sm-9">
                                <span class="form-control" disabled>{{ auth()->user()->name }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Title</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="title" name="title">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" style="height:55px;" id="descr" name="descr"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <label for="is_allday"><input id="is_allday" type="checkbox" name="is_allday"> All Day Event?</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary antosubmit">Save</button>
            </div>
        </div>
    </div>
</div>
<div id="CalenderModalEdit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel2">Edit Calendar Entry</h4>
            </div>
            <form id="antoform2" class="form-horizontal calender" role="form">
                <div class="modal-body">
                    <div id="testmodal2" style="padding: 5px 20px;">
                        <input type="hidden" name="id2" id="id2">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">User</label>
                            <div class="col-sm-9">
                                <span class="form-control" disabled id="user2"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Title</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="title2" name="title2">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" style="height:55px;" id="descr2" name="descr2"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left antodelete2" title="Delete Event"><i class="fa fa-times"></i></button>
                    <button type="button" class="btn btn-default antoclose2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary antosubmit2">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="fc_create" data-toggle="modal" data-target="#CalenderModalNew"></div>
<div id="fc_edit" data-toggle="modal" data-target="#CalenderModalEdit"></div>
<!-- /calendar modal -->

@endsection

@section('ext_css')
{!! Html::style(url('assets/css/plugins/fullcalendar.min.css')) !!}
@endsection

@section('ext_js')
{!! Html::script(url('assets/js/plugins/moment.min.js')) !!}
{!! Html::script(url('assets/js/plugins/fullcalendar.min.js')) !!}
@endsection

@section('script')
<script>
    (function() {
        var date = new Date(),
        d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear(),
        started,
        categoryClass;

        var calendar = $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            height: 550,
            selectable: true,
            selectHelper: true,
            droppable: false,
            editable: false,
            // eventLimit: true,
            slotLabelFormat: 'HH:mm',
            slotDuration: '01:00:00',
            events: {
                url: "{{ route('api.events.index') }}",
                type: "GET",
                error: function() {
                    alert('there was an error while fetching events!');
                }
            },
            eventRender: function(calEvent, element) {
                // element.find('.fc-content').prepend('<strong style="display:block">' + calEvent.user + '</strong>');
            },
            select: function(start, end, allDay) {
                $('#fc_create').click();

                started = start;
                ended = end;

                $(".antosubmit").on("click", function() {
                    var title = $("#title").val();
                    var body = $("#descr").val();
                    var is_allday = $("#is_allday").is(':checked');

                    if (title) {
                        $.ajax({
                            url: "{{ route('api.events.store') }}",
                            method: "POST",
                            data: { title: title, body: body, start: started.format("YYYY-MM-DD HH:mm:ss"), end: ended.format("YYYY-MM-DD HH:mm:ss"), is_allday: is_allday },
                            success: function(response){
                                if(response.message == 'event.created') {
                                    calendar.fullCalendar('renderEvent', {
                                        id: response.data.id,
                                        title: title,
                                        body: body,
                                        start: started.format("YYYY-MM-DD HH:mm"),
                                        end: ended.format("YYYY-MM-DD HH:mm"),
                                        user: "{{ auth()->user()->name }}",
                                        user_id: "{{ auth()->id() }}",
                                        allDay: is_allday,
                                        editable: true
                                    }, true);
                                }
                            },
                            error: function(e){
                                alert('Error processing your request: '+e.responseText);
                            }
                        });

                    }

                    $('#title').val('');
                    $('#descr').val('');

                    calendar.fullCalendar('unselect');

                    $('.antoclose').click();

                    return false;
                });
            },
            eventClick: function(calEvent, jsEvent, view) {

                if (calEvent.editable) {
                    $('#user2').text(calEvent.user);
                    $('#title2').val(calEvent.title);
                    $('#descr2').val(calEvent.body);
                    $('#CalenderModalEdit').modal();
                }
                else {
                    $('#user3').text(calEvent.user);
                    $('#title3').html(calEvent.title);
                    $('#descr3').html(calEvent.body);
                    $('#CalenderModalView').modal();
                }

                $(".antodelete2").off("click").on("click", function() {
                    var confirmBox = confirm('Delete this event?');

                    if (confirmBox) {
                        $.ajax({
                            url: "{{ route('api.events.destroy') }}",
                            method: "DELETE",
                            beforeSend: function(xhr){
                                xhr.setRequestHeader('Authorization', 'Bearer ' + "{{ auth()->user()->api_token }}");
                            },
                            data: { id: calEvent.id },
                            success: function(response){
                                if(response.message == 'event.deleted')
                                    calendar.fullCalendar('removeEvents', calEvent.id);
                                    console.log(calEvent);
                            },
                            error: function(e){
                                alert('Error processing your request: '+e.responseText);
                            }
                        });

                    }

                    $('#CalenderModalEdit').modal('hide');
                    $('#CalenderModalView').modal('hide');
                });

                $("#antoform2").off("submit").on("submit", function() {
                    calEvent.title = $("#title2").val();
                    calEvent.body = $("#descr2").val();

                    $.ajax({
                        url: "{{ route('api.events.update') }}",
                        method: "PATCH",
                        data: { id: calEvent.id, title: calEvent.title, body: calEvent.body },
                        success: function(response){
                            if(response.message == 'event.updated')
                                $('#calendar').fullCalendar('updateEvent',calEvent);
                                console.log(calEvent);
                        },
                        error: function(e){
                            alert('Error processing your request: '+e.responseText);
                        }
                    });

                    $('#CalenderModalEdit').modal('hide');
                    $('#CalenderModalView').modal('hide');

                    return false;
                });

                calendar.fullCalendar('unselect');
            },
            eventDrop: function(calEvent, delta, revertFunc) {
                var start = calEvent.start.format('YYYY-MM-DD HH:mm:ss');
                var end = calEvent.end ? calEvent.end.format('YYYY-MM-DD HH:mm:ss') : null;
                $.ajax({
                    url: "{{ route('api.events.reschedule') }}",
                    method: "PATCH",
                    data: { id: calEvent.id, start: start, end: end },
                    success: function(response){
                        if(response.message != 'event.rescheduled')
                            revertFunc();
                    },
                    error: function(e){
                        revertFunc();
                        alert('Error processing your request: '+e.responseText);
                    }
                });
            },
            eventResize: function(calEvent, delta, revertFunc) {
                var start = calEvent.start.format('YYYY-MM-DD HH:mm:ss');
                var end = calEvent.end.format('YYYY-MM-DD HH:mm:ss');
                $.ajax({
                    url: "{{ route('api.events.reschedule') }}",
                    method: "PATCH",
                    data: { id: calEvent.id, start: start, end: end },
                    success: function(response){
                        if(response.message != 'event.rescheduled')
                            revertFunc();
                    },
                    error: function(e){
                        revertFunc();
                        alert('Error processing your request: '+e.responseText);
                    }
                });

            }
        });
})();
</script>
@endsection