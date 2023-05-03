<div class="left-side-bar">
<div class="brand-logo">
        <a href="{{url('/home')}}">
            <img src="{{asset('img/Logo.png')}}" alt="" class="light-logo">
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <a href="{{url('/home')}}" class="dropdown-toggle no-arrow">
                    <span class="micon dw dw-house-1"></span><span class="mtext">Home</span>
                </a>
                <?php
                $role_id = Illuminate\Support\Facades\Auth::user()->id;
                ?>
                @if($role_id == 6)
                <li class="dropdown">
                    <a href="{{ route('chat.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-chat3"></span><span class="mtext">Chat</span>
                    </a>
                </li>
                @can('dailyprediction-list')
                <li class="dropdown">
                    <a href="{{ route('dailyprediction.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">Daily Prediction</span>
                    </a>
                </li>
                @endcan
                @else
                @can('role-list')
                <li class="dropdown">
                    <a href="{{route('roles.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-edit2"></span><span class="mtext">Roles</span>
                    </a>
                </li>
                @endcan
                @can('module-list')
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon dw dw-monitor-1"></span><span class="mtext">Systems</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('systems.index')}}">System Types</a></li>
                        <li><a href="{{ route('modules.index')}}">Module Types</a></li>
                    </ul>
                </li>
                @endcan
                @can('master_number-list')
                <li class="dropdown">
                    <a href="{{route('master_numbers.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-list1"></span><span class="mtext">Master Numbers</span>
                    </a>
                </li>
                @endcan
                @can('phythaname-list')
                <li class="dropdown">
                    <a href="{{ route('namereading.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">Name Reading</span>
                    </a>
                </li>
                @endcan
                @can('phythadob-list')
                <li class="dropdown">
                    <a href="{{ route('dobreading.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">DOB Reading</span>
                    </a>
                </li>
                @endcan
                @can('lucky_parameter-list')
                <li class="dropdown">
                    <a href="{{route('luckiest_parameters.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Luckiest Parameter</span>
                    </a>
                </li>
                @endcan
                @can('primary_number-list')
                <li class="dropdown">
                    <a href="{{route('primaryno_types.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Primary Number</span>
                    </a>
                </li>
                @endcan
                @can('magicbox-list')
                <li class="dropdown">
                    <a href="{{ route('magicbox.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">Magic Box</span>
                    </a>
                </li>
                @endcan
                @can('elemental-list')
                <li class="dropdown">
                    <a href="{{ route('elementalno.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">Elemental Numbers</span>
                    </a>
                </li>
                @endcan
                @can('destinyno-list')
                <li class="dropdown">
                    <a href="{{ route('destinyno.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">Destiny Numbers</span>
                    </a>
                </li>
                @endcan
                @can('video-list')
                <li class="dropdown">
                    <a href="{{ route('videos.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">Videos</span>
                    </a>
                </li>
                @endcan
                @can('lifecoach-list')
                <li class="dropdown">
                    <a href="{{ route('lifecoach_descriptions.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">Lifecoach Descriptions</span>
                    </a>
                </li>
                @endcan
                @can('cosmiccalender-list')
                <li class="dropdown">
                    <a href="{{ route('cosmiccelender')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-name"></span><span class="mtext">Cosmic Celender</span>
                    </a>
                </li>
                @endcan
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon dw dw-monitor-1"></span><span class="mtext">Health</span>
                    </a>
                    <ul class="submenu">
                        @can('healthreading-list')
                        <li><a href="{{ route('healthreading.index')}}">Health Reading</a></li>
                        @endcan
                        @can('hprecaution-list')
                        <li><a href="{{ route('healthprecaution.index')}}">Health Precautions</a></li>
                        @endcan
                        @can('suggmonth-list')
                        <li><a href="{{ route('healthsuggestion.index')}}">Health Suggestion</a></li>
                        @endcan
                        @can('healthcycle-list')
                        <li><a href="{{ route('healthcycle.index')}}">Health Cycle</a></li>
                        @endcan
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon dw dw-monitor-1"></span><span class="mtext">Personal</span>
                    </a>
                    <ul class="submenu">
                        @can('persyear-list')
                        <li><a href="{{ route('personalyear.index')}}">Personal Year</a></li>
                        @endcan
                        @can('persmonth-list')
                        <li><a href="{{ route('personalmonth.index')}}">Personal Month</a></li>
                        @endcan
                        @can('persweek-list')
                        <li><a href="{{ route('personalweek.index')}}">Personal Week</a></li>
                        @endcan
                        @can('persday-list')
                        <li><a href="{{ route('personalday.index')}}">Personal Day</a></li>
                        @endcan
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon dw dw-monitor-1"></span><span class="mtext">Universal</span>
                    </a>
                    <ul class="submenu">
                        @can('persyear-list')
                        <li><a href="{{ route('universalyear.index')}}">Universal Year</a></li>
                        @endcan
                        @can('persmonth-list')
                        <li><a href="{{ route('universalmonth.index')}}">Universal Month</a></li>
                        @endcan
                        @can('persday-list')
                        <li><a href="{{ route('universalday.index')}}">Universal Day</a></li>
                        @endcan
                    </ul>
                </li>
                @can('fav_unfav-list')
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon dw dw-monitor-1"></span><span class="mtext">Fav Unfav</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{route('fav_parameters.index')}}" >Fav Parameters</a></li>
                        <li><a href="{{route('unfav_parameters.index')}}" >Unfav Parameters</a></li>
                    </ul>
                </li>
                @endcan
                @can('zodic_sign-list')
                <li class="dropdown">
                    <a href="{{route('zodic_signs.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Zodic Signs</span>
                    </a>
                </li>
                @endcan
                @can('planet_number-list')
                <li class="dropdown">
                    <a href="{{route('planet_numbers.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Planet Number</span>
                    </a>
                </li>
                @endcan
                @can('life_cycle-list')
                <li class="dropdown">
                    <a href="{{route('life_cycles.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Life Cycle</span>
                    </a>
                </li>
                @endcan
                @can('life_changes-list')
                <li class="dropdown">
                    <a href="{{route('life_changes.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Life Changes</span>
                    </a>
                </li>
                @endcan
                @can('compatible_partner-list')
                <li class="dropdown">
                    <a href="{{route('compatible_partners.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-user-13"></span><span class="mtext">Compatible Partner</span>
                    </a>
                </li>
                @endcan
                @can('partner_relation-list')
                <li class="dropdown">
                    <a href="{{route('partner_relationships.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Partner Relationship</span>
                    </a>
                </li>
                @endcan
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon dw dw-monitor-1"></span><span class="mtext">Parenting</span>
                    </a>
                    <ul class="submenu">
                        @can('bparenting-list')
                        <li><a href="{{ route('basicparenting.index')}}">Basic Reading</a></li>
                        @endcan
                        @can('dparenting-list')
                        <li><a href="{{ route('detailparenting.index')}}">Detailed Reading</a></li>
                        @endcan
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon dw dw-monitor-1"></span><span class="mtext">Money Matters</span>
                    </a>
                    <ul class="submenu">
                        @can('basicmoney-list')
                        <li><a href="{{ route('basicmoney.index')}}">Basic Matters</a></li>
                        @endcan
                        @can('detailmoney-list')
                        <li><a href="{{ route('detailedmoney.index')}}">Detailed Matters</a></li>
                        @endcan
                    </ul>
                </li>
                @can('compatibility_percentage-list')
                <li class="dropdown">
                    <a href="{{route('compatibility_percentage.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Compatibility Scale</span>
                    </a>
                </li>
                @endcan
                <!-- <li class="dropdown">
                    <a href="{{route('commons.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">common</span>
                    </a>
                </li> -->
                @can('invoice-list')
                <li class="dropdown">
                    <a href="{{url('invoice')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Invoice</span>
                    </a>
                </li>
                @endcan
                @can('possesion-list')
                <li class="dropdown">
                    <a href="{{route('possesions.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Possesion</span>
                    </a>
                </li>
                @endcan
                @can('compatibility_desc-list')
                <li class="dropdown">
                    <a href="{{route('compatibility_description.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Compatibility Description</span>
                    </a>
                </li>
                @endcan
                @can('user-list')
                <li class="dropdown">
                    <a href="{{route('users.index')}}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Users</span>
                    </a>
                </li>
                @endcan
                <!-- <li class="dropdown">
                    <a href="{{ url('import') }}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-library"></span><span class="mtext">Import</span>
                    </a>
                </li> -->
                @endif
            </ul>
        </div>
    </div>
</div>
