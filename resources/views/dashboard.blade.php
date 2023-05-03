@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')
<style>
    .compChart {
        display: inline-flex;
        width: 100%;
        justify-content: space-between;
    }

    .loading {
        height: 0;
        width: 0;
        padding: 15px;
        border: 6px solid #ccc;
        border-right-color: #888;
        border-radius: 22px;
        -webkit-animation: rotate 1s infinite linear;
        /* left, top and position just for the demo! */
        position: absolute;
        left: 50%;
        top: 50%;
    }

    @-webkit-keyframes rotate {

        /* 100% keyframe for  clockwise. 
     use 0% instead for anticlockwise */
        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    .loaderdiv{
        width: 959px;
        height: 400px;
    }

    .compatibility{
        width: 959px;
        height: 400px;
    }

</style>
<div class="main-container">
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="xs-pd-20-10 pd-ltr-20">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Dashboard</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row clearfix progress-box">
            <div class="col-lg-3 col-md-6 col-sm-12 mb-30">
                <div class="card-box pd-30 height-100-p">
                    <div class="progress-box text-center">
                        <h5 class="text-blue padding-top-10 h5"> Total User</h5>
                        <span class="d-block">{{$alluserCount}} Users <i class="fa fa-line-chart text-blue"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-30">
                <div class="card-box pd-30 height-100-p">
                    <div class="progress-box text-center">
                        <h5 class="text-light-green padding-top-10 h5"> Active Users </h5>
                        <span class="d-block"> {{$activeuserCount}} Users<i class="fa text-light-green fa-line-chart"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-30">
                <div class="card-box pd-30 height-100-p">
                    <div class="progress-box text-center">
                        <h5 class="text-light-orange padding-top-10 h5">Daily Prediction</h5>
                        <span class="d-block">{{$like_preditionCount}} Like<i class="fa text-light-orange fa-line-chart"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 mb-30">
                <div class="card-box pd-30 height-100-p">
                    <div class="progress-box text-center">
                        <h5 class="text-light-orange padding-top-10 h5">Daily Prediction</h5>
                        <span class="d-block">{{$dislike_preditionCount}} Dislike<i class="fa text-light-orange fa-line-chart"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                <div class="card-box pd-30 height-100-p">
                    <div class="compChart">
                        <h4 class="mb-30 h4">User Prediction Record</h4>
                        <div class="col-sm-2 col-md-2">
                            <select class="custom-select col-12" name="favunfavdays" id="favunfavdays">
                                <option value="week">Weekly</option>
                                <option value="month">Monthly</option>
                            </select>
                        </div>
                    </div>
                    <div class="loaderdiv">
                        <div class="loading"></div>
                    </div>
                    <div id="compliance-trend" class="compliance-trend dates">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                <div class="card-box pd-30 height-100-p">
                    <div class="compChart">
                        <h4 class="mb-30 h4">Compatibility User Record</h4>
                        <div class="col-sm-2 col-md-2">
                            <select class="custom-select col-12" name="compdays" id="compdays">
                                <option value="week">Weekly</option>
                                <option value="month">Monthly</option>
                            </select>
                        </div>
                    </div>
                    <div class="compatibility">
                        <div class="loading"></div>
                    </div>
                    <div id="chart" class="chart"></div>
                </div>
            </div>
        </div>
        <div class="footer-wrap pd-20 mb-20 card-box">
            ASTART8 - Designed By <a href="https://www.designersx.us/" target="_blank">DesignersX</a>
        </div>
    </div>
</div>
<script src="{{ asset('js/highcharts.js') }}"></script>
<script src="{{ asset('js/highcharts-more.js') }}"></script>
<script src="{{ asset('js/jquery.knob.min.js') }}"></script>
<script src="{{ asset('js/jquery-jvectormap-2.0.3.min.js') }}"></script>
<script src="{{ asset('js/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('js/dashboard2.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>
    $('.loaderdiv').hide();
    $('.compatibility').hide();
    let totalusers = @json($totalusers);
    let datelist = @json($datelist);
    let greenstarlist = @json($greenstarlist);
    let redstarlist = @json($redstarlist);
    let neutrallist = @json($neutrallist);
    let filter_date = @json($filter_date);
    let carcompCheckCount = @json($carcompCheckCounts);
    let businesscompCheckCount = @json($businesscompCheckCounts);
    let propertycompCheckCount = @json($propertycompCheckCounts);
    let otherpersioncompCheckCount = @json($otherpersioncompCheckCounts);
    let relationcompCheckCount = @json($relationcompCheckCounts);

    let yaxiscount = 300;
    if(totalusers > 250)
    {
        let dividevalue = totalusers / 100;
        let yaxiscal = Math.round(dividevalue);
        yaxiscount = 200 * yaxiscal;
    }
    Highcharts.chart('compliance-trend', {
        chart: {
            type: 'column'
        },
        colors: ['#16a704', '#eb230e', '#f5ee11'],
        title: {
            text: ''
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: datelist,
            crosshair: true,
            lineWidth: 1,
            lineColor: '#979797',
            labels: {
                style: {
                    fontSize: '10px',
                    color: '#5a5a5a'
                }
            },
        },
        yAxis: {
            min: 0,
            max: yaxiscount,
            gridLineWidth: 0,
            lineWidth: 1,
            lineColor: '#979797',
            title: {
                text: ''
            },
            stackLabels: {
                enabled: false,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            enabled: true
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                },
                borderWidth: 0
            }
        },
        series: [{
            name: 'Green Star',
            maxPointWidth: 10,
            data: greenstarlist
        }, {
            name: 'Red Star',
            maxPointWidth: 10,
            data: redstarlist
        }, {
            name: 'Nutral',
            maxPointWidth: 10,
            data: neutrallist
        }]
    });

    Highcharts.chart('chart', {
        chart: {
            type: 'line'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: filter_date,
            labels: {
                style: {
                    color: '#1b00ff',
                    fontSize: '12px',
                }
            }
        },
        yAxis: {
            labels: {
                formatter: function() {
                    return this.value;
                },
                style: {
                    color: '#1b00ff',
                    fontSize: '14px'
                }
            },
            title: {
                text: ''
            },
        },
        credits: {
            enabled: false
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },
        plotOptions: {
            spline: {
                marker: {
                    radius: 10,
                    lineColor: '#1b00ff',
                    lineWidth: 2
                }
            }
        },
        legend: {
            align: 'center',
            x: 0,
            y: 0
        },
        series: [{
                name: 'Car Compatibility Check',
                color: '#00789c',
                marker: {
                    symbol: 'circle'
                },
                data: carcompCheckCount
            },
            {
                name: 'Business Compatibility Check',
                color: '#ef1fdf',
                marker: {
                    symbol: 'circle'
                },
                data: businesscompCheckCount
            },
            {
                name: 'Property Compatibility Check',
                color: '#ff686b',
                marker: {
                    symbol: 'circle'
                },
                data: propertycompCheckCount
            },
            {
                name: 'Relation Compatibility Check',
                color: '#16a704',
                marker: {
                    symbol: 'circle'
                },
                data: relationcompCheckCount
            },
            {
                name: 'Other Compatibility Check',
                color: '#f5ee11',
                marker: {
                    symbol: 'circle'
                },
                data: otherpersioncompCheckCount
            }
        ]
    });
</script>
<script>
    $('#favunfavdays').change(function() {
        var favunfavday = $('#favunfavdays').val();
        $('.loaderdiv').show();
        $('.compliance-trend').hide();
        $.ajax({
            type: 'POST',
            url: "{{url('favunfavfilter')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "favunfavdates": favunfavday,
            },
            success: function(data) {
                console.log(data);
                var datelist = data.datelist;
                var greenstarlist = data.greenstarlist;
                var redstarlist = data.redstarlist;
                var neutrallist = data.neutrallist;
                Highcharts.chart('compliance-trend', {
                    chart: {
                        type: 'column'
                    },
                    colors: ['#16a704', '#eb230e', '#f5ee11'],
                    title: {
                        text: ''
                    },
                    credits: {
                        enabled: false
                    },
                    xAxis: {
                        categories: datelist,
                        crosshair: true,
                        lineWidth: 1,
                        lineColor: '#979797',
                        labels: {
                            style: {
                                fontSize: '10px',
                                color: '#5a5a5a'
                            }
                        },
                    },
                    yAxis: {
                        min: 0,
                        max: 300,
                        gridLineWidth: 0,
                        lineWidth: 1,
                        lineColor: '#979797',
                        title: {
                            text: ''
                        },
                        stackLabels: {
                            enabled: false,
                            style: {
                                fontWeight: 'bold',
                                color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                            }
                        }
                    },
                    legend: {
                        enabled: true
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}'
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: false,
                                color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                            },
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: 'Green Star',
                        maxPointWidth: 10,
                        data: greenstarlist
                    }, {
                        name: 'Red Star',
                        maxPointWidth: 10,
                        data: redstarlist
                    }, {
                        name: 'Nutral',
                        maxPointWidth: 10,
                        data: neutrallist
                    }]
                });

                $('.loaderdiv').hide();
                $('.compliance-trend').show();

            }
        });
    });
</script>
<script>
    $('#compdays').change(function() {
        var compdays = $('#compdays').val();
        $('.compatibility').show();
        $('.chart').hide();
        $.ajax({
            type: 'POST',
            url: "{{url('compfilter')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "compdays": compdays,
            },
            success: function(data) {
                console.log(data);
                var dates = data.dates;
                var carcompCheckCount = data.carcompCheckCounts;
                var businesscompCheckCount = data.businesscompCheckCounts;
                var propertycompCheckCount = data.propertycompCheckCounts;
                var relationcompCheckCount = data.relationcompCheckCounts;
                var otherpersioncompCheckCount = data.otherpersioncompCheckCounts;
                Highcharts.chart('chart', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: dates,
                        labels: {
                            style: {
                                color: '#1b00ff',
                                fontSize: '12px',
                            }
                        }
                    },
                    yAxis: {
                        labels: {
                            formatter: function() {
                                return this.value;
                            },
                            style: {
                                color: '#1b00ff',
                                fontSize: '14px'
                            }
                        },
                        title: {
                            text: ''
                        },
                    },
                    credits: {
                        enabled: false
                    },
                    tooltip: {
                        crosshairs: true,
                        shared: true
                    },
                    plotOptions: {
                        spline: {
                            marker: {
                                radius: 10,
                                lineColor: '#1b00ff',
                                lineWidth: 2
                            }
                        }
                    },
                    legend: {
                        align: 'center',
                        x: 0,
                        y: 0
                    },
                    series: [{
                            name: 'Car Compatibility Check',
                            color: '#00789c',
                            marker: {
                                symbol: 'circle'
                            },
                            data: carcompCheckCount
                        },
                        {
                            name: 'Business Compatibility Check',
                            color: '#ef1fdf',
                            marker: {
                                symbol: 'circle'
                            },
                            data: businesscompCheckCount
                        },
                        {
                            name: 'Property Compatibility Check',
                            color: '#ff686b',
                            marker: {
                                symbol: 'circle'
                            },
                            data: propertycompCheckCount
                        },
                        {
                            name: 'Relation Compatibility Check',
                            color: '#16a704',
                            marker: {
                                symbol: 'circle'
                            },
                            data: relationcompCheckCount
                        },
                        {
                            name: 'Other Compatibility Check',
                            color: '#f5ee11',
                            marker: {
                                symbol: 'circle'
                            },
                            data: otherpersioncompCheckCount
                        }
                    ]
                });
                $('.compatibility').hide();
                $('.chart').show();
            }
        });
    });
</script>

@include('includes.footer')