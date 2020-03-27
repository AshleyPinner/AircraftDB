$(function() {
    'use strict';
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
            text: dateForCharts,
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
                    return e[this.value];
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
            data: directionsPolar200PData,
        color: "#0b5d8b",
        pointPlacement: "between",
        tooltip: {headerFormat: "", pointFormat: "Positions reported: {point.hits}"}
    }, {
        type: "column",
        name: "200nm",
        data: directionsPolar200Data,
        color: "#0b5d8b",
        pointPlacement: "between",
        tooltip: {headerFormat: "", pointFormat: "Positions reported: {point.hits}"}
}, {
        type: "column",
            name: "100nm",
            data: directionsPolar100Data,
        color: "#0b5d8b",
            pointPlacement: "between",
            tooltip: {headerFormat: "", pointFormat: "Positions reported: {point.hits}"}
    }, {
        type: "column",
            name: "50nm",
            data: directionsPolar50Data,
        color: "#0b5d8b",
            pointPlacement: "between",
            tooltip: {headerFormat: "", pointFormat: "Positions reported: {point.hits}"}
    }, {
        type: "line",
            name: "Range [nm]",
            data: directionsRangeData,
        pointPlacement: "between"
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
            categories: weeklyCategories,
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
                    if (this.y > 0) {
                        return this.y;
                    }
                }
            }
        }
    },
    series: [{
        showInLegend: !1,
        type: "column",
        name: "Aircraft",
        data: weeklyAircraft,
        color: {
        linearGradient: {
            x1: 0,
                y1: 0,
                x2: 0,
                y2: 1
        },
        stops: [[0, Highcharts.Color("#64bef1").setOpacity(0.6).get("rgba")], [1, Highcharts.Color("#64bef1").setOpacity(0.2).get("rgba")]]
    }
}, {
        showInLegend: !1,
            type: "column",
            name: "Positions",
            data: weeklyPositions,
        color: {
            linearGradient: {
                x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
            },
            stops: [[0, Highcharts.Color("#f8c023").setOpacity(0.6).get("rgba")], [1, Highcharts.Color("#f8c023").setOpacity(0.2).get("rgba")]]
        }
    }]
}); //*/
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
            text: Date.parse(dateForCharts),
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
                pointStart: Date.parse(dateForCharts),
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
                pointStart: Date.parse(dateForCharts),
                pointPlacement: "between",
                threshold: null
            },
            column: {
                borderWidth: 0,
                pointWidth: 10,
                shadow: !1,
                pointInterval: 36e5,
                pointStart: Date.parse(dateForCharts),
                pointPlacement: "between",
                threshold: null,
                dataLabels: {
                    enabled: !0,
                    color: "#ffffff",
                    style: {fontSize: "8px", fontWeight: "normal"},
                    formatter: function () {
                        if (this.y > 0) {
                            return this.y;
                        }
                    }
                }
            }
        },
        series: [
            {
                type: "area",
                name: "Hits",
                data: histogramHits,
        color: "#64bef1"
    }, {
        type: "line",
        name: "Positions",
        data: histogramData,
        color: "#f8c023"
}
    ]
});

    $("#contacts").highcharts({
        chart: {backgroundColor: "#282727", marginBottom: 35, marginTop: 70},
        credits: {enabled: !1},
        legend: {
            borderWidth: 0,
            itemStyle: {color: "#ffffff", textDecoration: "none", textTransform: "uppercase"},
            itemHoverStyle: {color: "#ffffff", textDecoration: "underline"},
            align: "left",
            verticalAlign: "top",
            layout: "horizontal",
            x: -10,
            y: 20
        },
        title: {text: "Histogram", align: "left", x: 0, style: {color: "#FFFFFF"}},
        subtitle: {
            text: Date.parse(dateForCharts),
            align: "right",
            y: 15,
            style: {color: "#6D6D6D"}
        },
        xAxis: {
            title: {enabled: !1, text: "Distance", style: {color: "#a4a1a1", fontWeight: "normal"}},
            categories: ["<50nm", "50-100nm", "100-150nm", "150-200nm", "200>"],
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
        tooltip: {headerFormat: "", pointFormat: "Contacts: {point.y}"},
        plotOptions: {
            columnrange: {dataLabels: {enabled: !1}},
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
                threshold: null
            },
            column: {
                borderWidth: 0,
                pointWidth: 35,
                shadow: !1,
                threshold: null,
                dataLabels: {
                    enabled: !0,
                    style: {color: "#a4a1a1", fontWeight: "normal", fontSize: "10px"},
                    formatter: function () {
                        if (this.y > 0) {
                            return this.y;
                        }
                    }
                }
            }
        },
        series: [{
            showInLegend: !1,
            type: "column",
            name: "Contacts",
            data: contactsData,
            color: {
                linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                stops: [[0, Highcharts.Color("#64bef1").setOpacity(.6).get("rgba")], [1, Highcharts.Color("#64bef1").setOpacity(.2).get("rgba")]]
            }
        }]
    });

    $("#thirty").highcharts({
        chart: {
            backgroundColor: "#282727",
            marginBottom: 45,
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
            text: 'Last 30 days',
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
                text: "Date",
                style: {
                    color: "#a4a1a1",
                    fontWeight: "normal"
                }
            },
            categories: monthlyCategories,
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
                pointWidth: 20,
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
                        if (this.y > 0) {
                            return this.y;
                        }
                    }
                }
            }
        },
        series: [{
            showInLegend: !1,
            type: "column",
            name: "Aircraft",
            data: monthlyAircraft,
            color: {
                linearGradient: {
                    x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
                },
                stops: [[0, Highcharts.Color("#64bef1").setOpacity(0.6).get("rgba")], [1, Highcharts.Color("#64bef1").setOpacity(0.2).get("rgba")]]
            }
        }, {
            showInLegend: !1,
            type: "column",
            name: "Positions",
            data: monthlyPositions,
            color: {
                linearGradient: {
                    x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
                },
                stops: [[0, Highcharts.Color("#f8c023").setOpacity(0.6).get("rgba")], [1, Highcharts.Color("#f8c023").setOpacity(0.2).get("rgba")]]
            }
        }]
    });

});

