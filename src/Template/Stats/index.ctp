<h1>Home</h1>
<h2>Viewing stats for <?= $today ?></h2>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>

<div style="float: right">
    <?php echo $this->Form->create(null, ['id' => 'js-dateChangeForm']);
    echo $this->Form->control('date', ['type' => 'date', 'id' => 'js-dateChange', 'min' => '2018-05-03', 'max' => date('Y-m-d')]);
    echo $this->Form->end() ?>
    <div class="clearfix"></div>
</div>

<div class="feed-stats-sub">
    <div class="stats-row pull-left">
        <div class="stats-col pull-left title">Unique Aircraft seen</div>
        <div class="stats-col pull-right stats-col-value" id="ac-stats"><?= $aircraftSeen ?></div>
        <div class="clearfix"></div>
    </div>
    <div class="stats-row pull-right">
        <div class="stats-col pull-left title">Positions reported</div>
        <div class="stats-col pull-right stats-col-value" id="points-stats"><?= $totalPositions ?></div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    <div class="stats-row pull-left no-border">
        <div class="stats-col pull-left title">Maximum distance</div>
        <div class="stats-col pull-right stats-col-value" id="maximum-distance"><?= $maxDistance ?>nm</div>
        <div class="clearfix"></div>
    </div>
    <div class="stats-row pull-right no-border">
        <div class="stats-col pull-left title">Hits reported</div>
        <div class="stats-col pull-right stats-col-value" id="hits-stats"><?= $totalFlights ?></div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
</div>
<div class="stats-data">
    <div class="stats-box" id="directions" data-highcharts-chart="1"></div>
    <div class="stats-box" id="histogram" data-highcharts-chart="2"></div>
    <div class="stats-box" id="weekly" data-highcharts-chart="3"></div>
    <div class="clearfix"></div>
</div>

</div>
<style>
    .highcharts-legend-item {
        text-transform: uppercase;
    }
    .highcharts-text-outline {
        stroke-width: 0px;
    }
    .stats-data>div:nth-child(odd) {
        float: left;
    }

    .stats-box {
        width: 49%;
        height: 400px;
        margin: 10px 0;
        padding: 10px;
        background-color: #282727;
        font-family: "Lucida Grande", "Lucida Sans Unicode", Arial, Helvetica, sans-serif;
        color: #fff;
    }
</style>
<script>
    var e = {0: "N", 45: "NE", 90: "E", 135: "SE", 180: "S", 225: "SW", 270: "W", 315: "NW"};
    $('#directions').highcharts({
            chart: {backgroundColor: "#282727", polar: true},
            legend: {
                borderWidth: 0,
                itemStyle: {color: "#ffffff", textDecoration: "underline"},
                itemHoverStyle: {color: "#ffffff", textDecoration: "underline"},
                align: "left",
                verticalAlign: "top",
                layout: "horizontal"
            },
            title: {text: "Polar plot", align: "left", x: 0, style: {color: "#FFFFFF"}},
            subtitle: {
                text: '<?= $today ?>',
                align: "right",
                y: 15,
                style: {color: "#6D6D6D"}
            },
            pane: {startAngle: 0, endAngle: 360},
            xAxis: {
                gridLineColor: "#424242",
                lineColor: "#424242",
                tickInterval: 45,
                min: 0,
                max: 360,
                labels: {
                    formatter: function () {
                        return e[this.value]
                    }, style: {color: "#ffffff"}
                }
            },
            yAxis: {gridLineColor: "#424242", lineColor: "#424242", min: 0, labels: {enabled: !1}},
            plotOptions: {
                series: {pointStart: -22.5, pointInterval: 45},
                column: {pointPadding: 0, groupPadding: 0, stacking: "normal", borderWidth: 1, borderColor: "#282727"},
                line: {
                    color: "#f8c023",
                    lineWidth: 1,
                    marker: {
                        fillColor: Highcharts.Color("#282727").setOpacity(1).get("rgba"),
                        lineWidth: 1,
                        lineColor: "#f8c023",
                        symbol: "circle",
                        radius: 2
                    }
                }
            },
            tooltip: {headerFormat: "", pointFormat: "Range {point.y}nm"},
            series: [{
                type: "column",
                name: ">200nm",
                data: <?= json_encode($polar['200+']) ?>,
                color: "#0b5d8b",
                pointPlacement: "between",
                tooltip: {headerFormat: "", pointFormat: "Positions reported: {point.hits}"}
            }, {
                type: "column",
                name: "200nm",
                data: <?= json_encode($polar['200']) ?>,
                color: "#0b5d8b",
                pointPlacement: "between",
                tooltip: {headerFormat: "", pointFormat: "Positions reported: {point.hits}"}
            }, {
                type: "column",
                name: "100nm",
                data: <?= json_encode($polar['100']) ?>,
                color: "#0b5d8b",
                pointPlacement: "between",
                tooltip: {headerFormat: "", pointFormat: "Positions reported: {point.hits}"}
            }, {
                type: "column",
                name: "50nm",
                data: <?= json_encode($polar['50']) ?>,
                color: "#0b5d8b",
                pointPlacement: "between",
                tooltip: {headerFormat: "", pointFormat: "Positions reported: {point.hits}"}
            }, {
                type: "line",
                name: "Range [nm]",
                data: <?= json_encode($range) ?>,
                pointPlacement: "between"
            }
        ]
    });

    $("#histogram").highcharts({
        chart: {backgroundColor: "#282727", marginBottom: 35, marginTop: 120},

        legend: {
            borderWidth: 0,
            itemStyle: {color: "#ffffff", textDecoration: "none", textTransform: "uppercase"},
            itemHoverStyle: {color: "#ffffff", textDecoration: "underline"},
            align: "left",
            verticalAlign: "top",
            layout: "horizontal",
            x: -10,
            y: 0
        },
        title: {text: "Hits and positions reported", align: "left", x: 0, style: {color: "#FFFFFF"}},
        subtitle: {
            text: '<?= $today ?>',
            align: "right",
            y: 15,
            style: {color: "#6D6D6D"}
        },
        xAxis: {
            type: "datetime",
            title: {text: "UTC", style: {color: "#a4a1a1", fontWeight: "normal"}, align: "low", x: -50, y: -18},
            labels: {style: {color: "#ffffff", fontSize: "10px"}}
        },
        yAxis: {
            min: 0,
            title: {
                text: "Contacts",
                style: {color: "#a4a1a1", fontWeight: "normal"},
                align: "high",
                offset: 0,
                rotation: 0,
                x: 8,
                y: -15
            },
            gridLineWidth: 1,
            gridLineColor: "#3e3d3d",
            minorGridLineColor: "#3e3d3d",
            plotLines: [{value: 0, width: 1, color: "#808080"}],
            labels: {style: {color: "#ffffff", fontSize: "10px"}}
        },
        tooltip: {shared: !0},
        plotOptions: {
            columnrange: {dataLabels: {enabled: !0}},
            area: {
                fillColor: {
                    linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [[0, "#41667c"], [1, Highcharts.Color("#41667c").setOpacity(0).get("rgba")]]
                },
                lineColor: "#64bef1",
                lineWidth: 1,
                marker: {enabled: !1},
                shadow: !1,
                states: {hover: {lineWidth: 1}},
                pointInterval: 36e5,
                pointStart: Date.parse('<?= $today ?>'),
                threshold: null
            },
            line: {
                color: "#f8c023",
                lineWidth: 1,
                marker: {
                    fillColor: Highcharts.Color("#282727").setOpacity(1).get("rgba"),
                    lineWidth: 1,
                    lineColor: "#f8c023",
                    symbol: "circle",
                    radius: 2
                },
                shadow: !1,
                states: {hover: {lineWidth: 1}},
                pointInterval: 36e5,
                pointStart: Date.parse('<?= $today ?>'),
                pointPlacement: "between",
                threshold: null
            },
            column: {
                borderWidth: 0,
                pointWidth: 10,
                shadow: !1,
                pointInterval: 36e5,
                pointStart: Date.parse('<?= $today ?>'),
                pointPlacement: "between",
                threshold: null,
                dataLabels: {
                    enabled: !0,
                    color: "#ffffff",
                    style: {fontSize: "8px", fontWeight: "normal"},
                    formatter: function () {
                        if (this.y > 0)return this.y
                    }
                }
            }
        },
        series: [
            {
                type: "area",
                name: "Hits",
                data: <?= json_encode($hitsData) ?>,
                color: "#64bef1"
            }, {
                type: "line",
                name: "Positions",
                data: <?= json_encode($positionsData) ?>,
                color: "#f8c023"
            }
        ]
    });

    $("#weekly").highcharts({
        chart: {
            backgroundColor: "#282727",
            marginBottom: 35,
            marginTop: 70
        },
        legend: {
            borderWidth: 0,
            itemStyle: {
                color: "#ffffff",
                textDecoration: "none",
                textTransform: "uppercase"
            },
            itemHoverStyle: {
                color: "#ffffff",
                textDecoration: "underline"
            },
            align: "left",
            verticalAlign: "top",
            layout: "horizontal",
            x: -10,
            y: 20
        },
        title: {
            text: "Aircraft seen",
            align: "left",
            x: 0,
            style: {
                color: "#FFFFFF"
            }
        },
        subtitle: {
            text: 'Last 7 days',
            useHTML: !0,
            align: "right",
            x: -15,
            y: 15,
            style: {
                color: "#6D6D6D"
            }
        },
        xAxis: {
            title: {
                enabled: !1,
                text: "Day of week",
                style: {
                    color: "#a4a1a1",
                    fontWeight: "normal"
                }
            },
            categories: <?php $column = array_column($barChart, 'dayOfYear');  array_walk($column, function(&$value) {
                $value = \DateTime::createFromFormat('Y-m-d', '2018-01-01')->add(new \DateInterval("P{$value}D"))->format('D');

        }); echo json_encode($column);  ?>,
            labels: {
                style: {
                    color: "#ffffff",
                    fontSize: "10px"
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: "Aircraft",
                style: {
                    color: "#a4a1a1",
                    fontWeight: "normal"
                },
                align: "high",
                offset: 0,
                rotation: 0,
                x: 8,
                y: -15
            },
            gridLineWidth: 1,
            gridLineColor: "#3e3d3d",
            minorGridLineColor: "#3e3d3d",
            plotLines: [{
                value: 0,
                width: 1,
                color: "#808080"
            }],
            labels: {
                style: {
                    color: "#ffffff",
                    fontSize: "10px"
                }
            }
        },
        tooltip: {
            headerFormat: "",
            pointFormat: "{series.name}: {point.y}"
        },
        plotOptions: {
            columnrange: {
                dataLabels: {
                    enabled: !1
                }
            },
            area: {
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [[0, "#41667c"], [1, Highcharts.Color("#41667c").setOpacity(0).get("rgba")]]
                },
                lineColor: "#64bef1",
                lineWidth: 1,
                marker: {
                    enabled: !1
                },
                shadow: !1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            },
            column: {
                borderWidth: 0,
                pointWidth: 35,
                shadow: false,
                threshold: null,
                dataLabels: {
                    enabled: !0,
                    style: {
                        color: "#a4a1a1",
                        fontWeight: "normal",
                        fontSize: "10px",
                        strokeWidth: "0px"
                    },
                    formatter: function() {
                        if (this.y > 0)
                            return this.y
                    }
                }
            }
        },
        series: [{
            showInLegend: !1,
            type: "column",
            name: "Aircraft",
            data: <?= json_encode(array_map('intval', array_column($barChart, 'hits'))) ?>,
            color: {
                linearGradient: {
                    x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
                },
                stops: [[0, Highcharts.Color("#64bef1").setOpacity(.6).get("rgba")], [1, Highcharts.Color("#64bef1").setOpacity(.2).get("rgba")]]
            }
        }, {
            showInLegend: !1,
            type: "column",
            name: "Positions",
            data: <?= json_encode(array_map('intval', array_column($barChart, 'positions'))) ?>,
            color: {
                linearGradient: {
                    x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
                },
                stops: [[0, Highcharts.Color("#f8c023").setOpacity(.6).get("rgba")], [1, Highcharts.Color("#f8c023").setOpacity(.2).get("rgba")]]
            }
        }]
    }); //*/


    $('#js-dateChange').on('change', function() {
        $('#js-dateChangeForm').submit();
    });

    function dateFromDay(year, day){
        var date = new Date(year, 0); // initialize a date in `year-01-01`
        return new Date(date.setDate(day)); // add the number of days
    }
</script>

