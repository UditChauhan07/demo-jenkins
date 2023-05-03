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
                            <h4>Chat</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white border-radius-4 box-shadow mb-30">
                <div class="row no-gutters">
                    <div class="col-lg-4 col-md-5 col-sm-12">
                        <div class="chat-list bg-light-gray unsethover">
                            <div class="chat-search">
                                <span class="ti-search"></span>
                                <input type="text" placeholder="Search Contact">
                            </div>
                            <div class="notification-list chat-notification-list customscroll">
                                <ul class="chatlist">
                                    @foreach($message_lists as $message_list)
                                    <?php
                                        $messageid = $message_list->id;
                                        if($message_list->is_seen == 0){
                                            $classname = "newmessage";
                                        }else{
                                            $classname = "oldmessage";
                                        }
                                    ?>
                                    <li class="chatBoxContant{{$messageid}} {{$classname}}">
                                        <a onclick="return getMessage('{{$message_list->id}}');" class="clickbtn" data-name="{{$message_list->user->id}}">
                                            <img src="{{asset('images/dummy.jpg')}}" class="roundimg" alt="">
                                            <div class="userdetail">
                                            <h3 class="clearfix">{{$message_list->user->name}}</h3>
                                            <span class="timeformat">{{$message_list->created_at->format('d-M-Y')}}</span>
                                            </div>
                                            <p>{!! $message_list->message !!}</p>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="col-lg-8 col-md-7 col-sm-12">
                        <div class="chat-detail">
                            <div class="chat-profile-header clearfix chatHeader">
                                <div class="left userchatheader" id="userheader">
                                   <div id="defaulchat"><h5 class="defaultmessage">No Message Found</h5></div> 
                                </div>
                            </div>
                            <div class="chat-box">
                                <div class="chat-desc scroll customscroll">
                                    <div class="userchatbody">

                                    </div>
                                </div>
                                <div class="chat-footer">
                                    <form>
                                        @csrf
                                        <div class="userchatfooter">
                                    
                                    </div>
                                    </form>
                                </div>
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
        $('.defaulchat').hide();
        var id = id;
        $('.chatlist .chatBoxContant'+id).removeClass('newmessage');
        $('.chatlist .chatBoxContant'+id).addClass('oldmessage');
        $.ajax({
            type: 'POST',
            url: "{{route('chat.chat')}}",
            data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                    },
            success: function(data) {
                var html='';
                html += '<div class="clearfix chatcontainer">';
                html += '<div class="chat-profile-photo profilePhoto">';
                html += '<img src="{{asset('images/dummy.jpg')}}" alt="">';
                html += '</div><div class="chat-profile-name">';
                html += '<h3>'+data.user.name+'</h3>';
                html += '<span>'+data.user.dob+'</span>';                     
                html += '<span>'+data.user.email+'</span></div></div>';

                var html1 = "";
                html1 += '<ul class="autoscroller">';
                $.each(data.message_lists, function(k, v) {
                    var id1 = "{{Auth::user()->id}}"
                if(v.sender_id == id1){
                html1 += '<li class="clearfix admin_chat"><span class="chat-img">';
                html1 += '<img src="{{asset('images/dummy.jpg')}}" alt=""> </span>';
                html1 += '<div class="chat-body clearfix messagediv">';
                var message1 = v.message;
                var messagetime = new Date(v.created_at);
                html1 += '<p class="messagelist" style="text-align: left;">'+message1.replace(/<\/?[^>]+(>|$)/g, "")+'</p>';
                html1 += '<div class="chat_time">'+messagetime.toDateString()+' '+messagetime.toLocaleTimeString()+'</div>';
                html1 += '</div></li>';
                }else{
                    var message2 = v.message;
                    var messagetime = new Date(v.created_at);
                html1 +='<li class="clearfix"><span class="chat-img" style="align-self:left;">';
                html1 += '<img src="{{asset('images/dummy.jpg')}}" alt="">';
                html1 += '</span><div class="chat-body clearfix messagediv">';
                html1 += '<p style="text-align: left;">'+message2.replace(/<\/?[^>]+(>|$)/g, "")+'</p>';
                html1 += '<div class="chat_time">'+messagetime.toDateString()+' '+messagetime.toLocaleTimeString()+'</div></div></li>'
                }
                });
                html1 += '</ul>';

                var html2 ='';
                html2 += '<div class="file-upload" id="chatinput"><a href="#"><i class="fa fa-paperclip"></i></a></div>';
                html2 += '<input type="hidden" id="userid" name="userid" value="'+data.user.id+'">';
                html2 += '<div class="chat_text_area">';
                html2 += '<textarea placeholder="Type your message…" id="questionanswer" name="questionanswer"></textarea></div>';
                html2 += '<div class="chat_send">';
                html2 += '<a class="btn btn-link" onclick="return getAnswer();" ><i class="icon-copy ion-paper-airplane"></i></a></div>';

                $(".userchatheader").html(html);
                $(".userchatbody").html(html1);
                $(".userchatfooter").html(html2);
                scrolldown()
            }
        });
    }
</script>
<script>
    function getAnswer(){
        var userid = $('#userid').val();
        var questionanswer = $('#questionanswer').val();
     $.ajax({
            type: 'POST',
            url: "{{url('askquestion')}}",
            data: {
                    "_token": "{{ csrf_token() }}",
                    "userid": userid,
                    "questionanswer":questionanswer
                    },
            success: function(data) {
                console.log(data);
                if(data.status == 'success'){
                    getMessage(userid);
                    $('#questionanswer').val('');
                }
                
            }
    });
}   
</script>

<script>
    function scrolldown() {
        $('#autoscroller').animate({
	    		scrollTop: $('#chatinput').scrollHeight}, "slow");
        }
</script>

@include('includes.footer')