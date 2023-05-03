@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h2>User Messages</h2>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <?php $predictiondate = ''; 
                        if(isset($_GET['date'])){
                        $predictiondate = $_GET['date'];
                        }?>
                            <div class="datesearch">
                                <div class="dropdown" style="width: 40%; float:right;">
                                    @if($predictiondate != '')
                                    <input class="form-control dateselected" id="messagefilter" value="{{$predictiondate}}" name="filterdate" placeholder="Select Date" type="date">
                                    @else
                                    <input class="form-control dateselected" id="messagefilter" onchange="return filter();" name="filterdate" placeholder="Select Date" type="date">
                                    @endif
                                </div>
                                <div>
                                    <span class="btn btn-danger" onclick="return clearsearch();">CLEAR</span>
                                </div>
                            </div>
					</div>
                </div>
            </div>
            <div class="bg-white border-radius-4 box-shadow mb-30">
                <div class="row no-gutters">
                    <div class="col-lg-4 col-md-5 col-sm-12">
                        <div class="chat-list bg-light-gray userDetails">
                            <div class="chat-search">
                                <span class="ti-search"></span>
                                <input type="text" id="searchinput" onkeyup="return filter();" name="searchinput" placeholder="Search Contact">
                            </div>
                            <div class="notification-list chat-notification-list customscroll">
                                <div class="msguserlist">
                                    <ul class="chatlist">
                                        @foreach($message_lists as $message_list)
                                        <?php
                                        $messageid = $message_list->id;
                                        ?>
                                        <li class="chatBoxContant{{$message_list->user->id}}">
                                            <a onclick="return getMessage('{{$message_list->user->id}}');" class="clickbtn" data-name="{{$message_list->user->id}}">
                                                @if($message_list->user->profile_pic != null)
                                                <img src="{{url('/profile_pic/'.$message_list->user->profile_pic)}}" class="roundimg" alt="">
                                                @else
                                                <img src="{{asset('images/dummy.jpg')}}" class="roundimg" alt="">
                                                @endif
                                                <div class="userdetail">
                                                    <h3 class="clearfix">{{ucwords($message_list->user->name)}}</h3>
                                                    <span class="timeformat">{{$message_list->created_at->format('M d, Y')}}</span>
                                                </div>
                                                @foreach($unseen_msgCountList as $unseen_msgCount)
                                                <?php
                                                $dobFormate = date('M d, Y', strtotime($message_list->user->dob));
                                                if ($message_list->user->id == $unseen_msgCount['userid']) {
                                                    $unseenMsgCount = $unseen_msgCount['count'];
                                                }
                                                ?>
                                                @endforeach
                                                @if($unseenMsgCount != 0)
                                                <p class="timeformat" id="messageId{{$message_list->user->id}}">DOB: {{$dobFormate}} | DOJ: {{$message_list->user->created_at->format('M d, Y')}}
                                                    <span class='msgcount latestmsgcount{{$message_list->user->id}}'>{{$unseenMsgCount}}</span></p>
                                                <!-- <p class="timeformat" id="latestmsg{{$message_list->user->id}}"></p> -->
                                                @else
                                                <p class="timeformat" id="messageId{{$message_list->user->id}}">DOB: {{$dobFormate}} | DOJ: {{$message_list->user->created_at->format('M d, Y')}}</p>
                                                <!-- <p class="timeformat" id="latestmsg{{$message_list->user->id}}"></p> -->
                                                @endif
                                            </a>
                                        </li>
                                        <hr class="margin">
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-7 col-sm-12">
                        <div class="chat-detail">
                            <div class="chat-profile-header clearfix padding">
                                <div class="left userchatheader">
                                    <div id="defaulchat">
                                        <h5 class="defaultmessage">No Message Found</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-box">
                                <div class="chat-desc customscroll">
                                    <div class="chatupdate">
                                        <ul class="userchatbody">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    @if(Auth::user()->id == 6)
                                <div class="chat-footer userchatfooter">
                            
                                </div>
                                @else
                                @endif
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
    function getMessage(id) {
        $('.chatlist li').removeClass('newmessage');
        $('.chatBoxContant' + id).addClass('newmessage');
        var id = id;
        $.ajax({
            type: 'POST',
            url: "{{route('chat.chat')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id
            },
            success: function(data) {
                var html = '';
                html += '<div class="clearfix chatcontainer">';
                html += '<div class="chat-profile-photo profilePhoto">';
                if (data.user.profile_pic != null) {
                    var pic = data.user.profile_pic;
                    html += '<img src="{{url('/profile_pic/')}}/' + pic + '" alt="">';
                } else {
                    html += '<img src="{{asset('/images/dummy.jpg')}}" alt="">';
                }
                var username = data.user.name.split(' ');
                for (let i = 0; i < username.length; i++) {
                    username[i] = username[i][0].toUpperCase() + username[i].substr(1);
                }
                var dobsplit = new Date(data.user.dob);
                var joiningdate = new Date(data.user.created_at);
                var joiningday = joiningdate.getDate();
                var joiningmonth = joiningdate.getMonth();
                var joiningyear = joiningdate.getFullYear();
                var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                html += '</div><div class="chat-profile-name userprofile">';
                html += '<h3>' + username.join(" ") + '</h3>';
                html += '<span> DOB: ' + monthNames[dobsplit.getMonth()]+ ' ' + dobsplit.getDate() + ', ' + dobsplit.getFullYear() + '</span>';
                html += '<span> Email: ' + data.user.email + '</span>';
                html += '<span> Joining Date: ' + monthNames[joiningmonth]+ ' ' + joiningday +', ' + joiningyear + '</span></div></div>';

                var html1 = "";
                var id1 = "{{Auth::user()->id}}";
                $.each(data.message_lists, function(k, v) {
                    if (v.sender_id == id1) {
                        html1 += '<li class="clearfix admin_chat"><span class="chat-img">';
                        if (v.user.profile_pic != null) {
                            var adminpic1 = v.user.profile_pic;
                            html1 += '<img src="{{url('/profile_pic/')}}/' + adminpic1 + '" alt=""> ';
                        } else {
                            html1 += '<img src="{{asset('/images/dummy.jpg')}}" alt="">';
                        }
                        html1 += '</span><div class="chat-body clearfix">';
                        var message1 = v.message;
                        var messagetime = new Date(v.created_at);
                        var splitmsgdate = messagetime.toDateString().split(" ");
                        var msgdate = splitmsgdate[1] + ' ' + splitmsgdate[2] + ',' + splitmsgdate[3];
                        var splitmsgtime = messagetime.toLocaleTimeString().split(':');
                        var msgtime = splitmsgtime[0] + ':' + splitmsgtime[1] + splitmsgtime[2].slice(2);
                        html1 += '<p >' + message1.replace(/<\/?[^>]+(>|$)/g, "") + '</p>';
                        html1 += '<div class="chat_time">' + msgdate+ ' ' + msgtime + '</div>';
                        html1 += '</li>';
                    } else {
                        var message2 = v.message;
                        var messagetime = new Date(v.created_at);
                        var splitmsgdate = messagetime.toDateString().split(" ");
                        var msgdate = splitmsgdate[1] + ' ' + splitmsgdate[2] + ',' + splitmsgdate[3];
                        var splitmsgtime = messagetime.toLocaleTimeString().split(':');
                        var msgtime = splitmsgtime[0] + ':' + splitmsgtime[1] + splitmsgtime[2].slice(2);
                        html1 += '<li class="clearfix"><span class="chat-img">';
                        if (v.user.profile_pic != null) {
                            var adminpic2 = v.user.profile_pic;
                            html1 += '<img src="{{url('/profile_pic/')}}/' + adminpic2 + '" alt="">';
                        } else {
                            html1 += '<img src="{{url('/images/dummy.jpg')}}" alt="">';
                        }
                        html1 += '</span><div class="chat-body clearfix messagediv">';
                        if(id1 != 6){
                            html1 += '<div class="chat_time">'+v.user.name+'</div>'
                        }
                        html1 += '<p style="display: inline-block">' + message2.replace(/<\/?[^>]+(>|$)/g, "") + '</p>';
                        html1 += '<div class="chat_time">' + msgdate+ ' ' + msgtime + '</div></li>'
                    }
                });

                var html2 = '';
                html2 += '<div class="file-upload" ><a href="#"><i class="icon-copy dw dw-message iconsize"></i></a></div>';
                html2 += '<input type="hidden" id="userid" name="userid" value="' + data.user.id + '">';
                html2 += '<div class="chat_text_area">';
                html2 += '<textarea placeholder="Type your message…" id="questionanswer" name="questionanswer"></textarea></div>';
                html2 += '<div class="chat_send">';
                html2 += '<button class="btn btn-link" onclick="return getAnswer();"><i class="icon-copy ion-paper-airplane"></i></button></div>';
                
                $('.userchatheader').empty();
                $(".userchatheader").html(html);
                $(".userchatbody").html(html1);
                if(id1 == 6){
                    $(".userchatfooter").html(html2);
                    $('.latestmsgcount'+id).hide();
                }
                $("#unMsgCount").load(location.href+" #unMsgCount>*","");
                $("#unMsgCount").load(location.href+" #unMsgCount>*","");
                $("#styles").load(location.href+" #styles>*","");
                if(id1 == 6){
                $(".chatupdate").addClass("messageBody");
                }else{
                $(".chatupdate").addClass("messageBody1");
                }
            }
        });
    }
</script>
<script>
    function getAnswer() {
        var userid = $('#userid').val();
        var questionanswer = $('#questionanswer').val();
        $.ajax({
            type: 'POST',
            url: "{{url('askquestion')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "userid": userid,
                "questionanswer": questionanswer
            },
            success: function(data) {
                var userId = data.userId;
                if (data.status == 'success') {
                    getMessage(userId);
                    $('#questionanswer').val('');
                }

            }
        });
    }

    function clearsearch()
    {
        $('#messagefilter').val('');
        filter();
    }
</script>
<script>
    $(document).ready(function(){
        let inputfilter = "<?php echo $predictiondate; ?>";
         if(inputfilter.length > 0){
            $.ajax({
                type: 'POST',
                url: "{{url('filteruser')}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "inputfilter": inputfilter,
                },
                success: function(data) 
                {
                    var msglist = data.message_list;
                    var unseenmsgcount = data.unseen_msgCountList;
                    var userlist = '';
                    userlist += '<ul class="chatlist">';
                        $.each(msglist, function(k, v) {
                        userlist += '<li class="chatBoxContant{{$message_list->user->id}}">';
                        userlist += '<a onclick="return getMessage('+v.user.id+');" class="clickbtn" data-name="{{$message_list->user->id}}">';
                        if(v.user.profile_pic != null){
                            userlist += '<img src="{{url('/profile_pic/')}}/'+v.user.profile_pic+'" class="roundimg" alt="">';
                        }else{
                            userlist += '<img src="{{url('/images/dummy.jpg')}}" class="roundimg" alt="">';
                        }
                        var userdob = new Date(v.user.dob);
                        var splitdob = userdob.toDateString().split(" ");
                        var userdobdate = splitdob[1] + ' ' + splitdob[2] + ',' + splitdob[3];
                        var userjoindate = new Date(v.user.created_at);
                        var splituserjoindate = userjoindate.toDateString().split(" ");
                        var userjoining = splituserjoindate[1] + ' ' + splituserjoindate[2] + ',' + splituserjoindate[3];
                        var messagetime = new Date(v.created_at);
                        var splitmsgdate = messagetime.toDateString().split(" ");
                        var msgdate = splitmsgdate[1] + ' ' + splitmsgdate[2] + ',' + splitmsgdate[3];
                        userlist += '<div class="userdetail">';
                        userlist += '<h3 class="clearfix">'+v.user.name+'</h3>';
                        userlist += '<span class="timeformat">'+msgdate+'</span></div>';
                        userlist += '<p class="timeformat">DOB: '+userdobdate+' | DOJ: '+userjoining+'>';
                        $.each(unseenmsgcount, function(k1, v1) {
                        if (v.user.id == v1['userid']) {
                            var msgCount = v1['count'];
                        }
                        if(msgCount != null){
                            if(msgCount == 0){
                            userlist += '';
                            }else{
                                userlist +=  '<span class="msgcount latestmsgcount'+v.user.id+'">'+sgCount+'</span>';
                            }
                        }else{
                        userlist += '';
                        }
                        });
                        userlist += '</p></a></li><hr class="margin">';
                        });
                        userlist += '</ul>';
                    $(".msguserlist").html(userlist);
                }
            });
        }
    });
</script>
<script>
    function filter(){
        let input = $('#searchinput').val();
        let inputfilter = $('#messagefilter').val();
            $.ajax({
                type: 'POST',
                url: "{{url('searchuser')}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "input" : input,
                    "inputfilter": inputfilter,
                },
                success: function(data) 
                {
                    console.log(data);
                    var msglist = data.message_list;
                    var unseenmsgcount = data.unseen_msgCountList;
                    var userlist = '';
                    userlist += '<ul class="chatlist">';
                    if(msglist.length != 0){
                        $.each(msglist, function(k, v) {
                        userlist += '<li class="chatBoxContant{{$message_list->user->id}}">';
                        userlist += '<a onclick="return getMessage('+v.user.id+');" class="clickbtn" data-name="{{$message_list->user->id}}">';
                        if(v.user.profile_pic != null){
                            userlist += '<img src="{{url('/profile_pic/')}}/'+v.user.profile_pic+'" class="roundimg" alt="">';
                        }else{
                            userlist += '<img src="{{url('/images/dummy.jpg')}}" class="roundimg" alt="">';
                        }
                        var userdob = new Date(v.user.dob);
                        var splitdob = userdob.toDateString().split(" ");
                        var userdobdate = splitdob[1] + ' ' + splitdob[2] + ',' + splitdob[3];
                        var userjoindate = new Date(v.user.created_at);
                        var splituserjoindate = userjoindate.toDateString().split(" ");
                        var userjoining = splituserjoindate[1] + ' ' + splituserjoindate[2] + ',' + splituserjoindate[3];
                        var messagetime = new Date(v.created_at);
                        var splitmsgdate = messagetime.toDateString().split(" ");
                        var msgdate = splitmsgdate[1] + ' ' + splitmsgdate[2] + ',' + splitmsgdate[3];
                        userlist += '<div class="userdetail">';
                        userlist += '<h3 class="clearfix">'+v.user.name+'</h3>';
                        userlist += '<span class="timeformat">'+msgdate+'</span></div>';
                        userlist += '<p class="timeformat">DOB: '+userdobdate+' | DOJ: '+userjoining;
                        $.each(unseenmsgcount, function(k1, v1) {
                        if (v.user.id == v1['userid']) {
                            var msgCount = v1['count'];
                        }
                        if(msgCount != null){
                            if(msgCount == 0){
                            userlist += '';
                            }else{
                                userlist += '<span class="msgcount latestmsgcount'+v.user.id+'">'+msgCount+'</span>';
                            }
                        }else{
                        userlist += '';
                        }
                        });
                        userlist += '</p></a></li><hr class="margin">';
                        });
                    }else{
                        userlist += '<li class="chatBoxContant">';
                        userlist += '<div class="userdetail1" style="background-color: #ecf0f4; padding-top: 5px;">';
                        userlist += '<span class="clearfix" style="margin-left: 80px;">No Data Found</span></div></li>';

                    }
                    userlist += '</ul>';
                    $(".msguserlist").html(userlist);
                }
            });
    };
</script>
@include('includes.footer')