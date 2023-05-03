<!DOCTYPE html>
<html>
<head>
<title>ASTAR8XX Cosmic Calendar</title>
</head>
<body>
<style>
.main-container{
	text-align:center;
	width:100%;
	margin: 100px auto;
}
    table {
        background-color: #007eff14;
        width: 100%;
    }

    td {
        width: 40px;
        height: 40px;
        text-align: center;
    }

    .day {
        font-weight: bolder;
        background-color: lightpink;
    }

    .starhight {
        margin-bottom: 0px;
        line-height: 0;
    }

    .holiday {
        background-color: lightblue;
        padding-top: 10px;
    }

    .ion-eye {
        color: green;
    }

    #field {
        font-weight: bolder;
        text-align: center;
    }
    form{
        width: 80%;
    }
    .form-control
    {
        align-self: center;
        width: 80%;
    }
</style>
<div class="main-container" >
    <div class="xs-pd-20-10 pd-ltr-20">
        <div class="page-header">

            </div>
        </div>
            <div class="row">
			<h2 style="text-align:center;">Cosmic Calendar</h2>
                <table align="center" border="1">
                    <tr>
                        <td colspan="7"><b>OCT 2022</b></td>
                    </tr>

                    <tr class="day">
                        <td>Sun</td>
                        <td>Mon</td>
                        <td>Tue</td>
                        <td>Wed</td>
                        <td>Thu</td>
                        <td>Fri</td>
                        <td>Sat</td>
                    </tr>
                    <tr>
                        @for($x = 1; $x<=$offsets; $x++) <td>
                            </td>
                            @endfor
                            @for($i = 1; $i<=$date_fav; $i++) 
                            <?php
                                $favstars = $favdatekey[$i];
                                $unfavstars = $unfavdatekey[$i];
                                ?>
                                <td class="holiday" id='{{$i}}' name="{{$i}}">
                                <span class='starhight'>

                                    @if($favstars[0] == 1)
                                    <img src="{{ asset('images/star2.png') }}">
                                    @elseif($unfavstars[0] == 1)
                                    <img src="{{ asset('images/star-red.png') }}">
                                    @else
                                    <img src="{{ asset('images/white-star.png') }}">
                                    @endif
                                    @if($favstars[1] == 1)
                                    <img src="{{ asset('images/star2.png') }}">
                                    @elseif($unfavstars[1] == 1)
                                    <img src="{{ asset('images/star-red.png') }}">
                                    @else
                                    <img src="{{ asset('images/white-star.png') }}">
                                    @endif
                                    @if($favstars[2] == 1)
                                    <img src="{{ asset('images/star2.png') }}">
                                    @elseif($unfavstars[2] == 1)
                                    <img src="{{ asset('images/star-red.png') }}">
                                    @else
                                    <img src="{{ asset('images/white-star.png') }}">
                                    @endif
                                </span>
                                <p>{{$i}}</p>
                                </td>


                                @if(($i + $offsets) % 7 == 0)
                    </tr>
                    @endif
                    @endfor
                </table>
                <br><br>
                <div id="field"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function show(a) {
        document.getElementById('field').innerHTML = document.getElementById(a.id)
            .attributes["name"].value;

        setTimeout(function() {
            document.getElementById('field').innerHTML = "";
        }, 5000);
    }
</script>

</body>
</html>