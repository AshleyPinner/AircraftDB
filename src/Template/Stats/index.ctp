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
        <div class="stats-col pull-right stats-col-value" id="maximum-distance"><?= $maxDistance ?>nm <?php if($binnedCount): ?> (Flights binned: <?= $binnedCount ?>)<?php endif; ?></div>
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
    <div class="stats-box" id="directions"></div>

</div>
<div class="clearfix"></div>
<div class="stats-data">
    <div class="stats-box" id="weekly"></div>
    <div class="stats-box" id="histogram"></div>
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
<?php $column = array_column($barChart, 'dayOfYear');  array_walk($column, function(&$value) {
    $value = \DateTime::createFromFormat('Y-m-d', '2018-01-01')->add(new \DateInterval("P{$value}D"))->format('D');

}); ?>
<script>

    var directionsPolar200PData = <?= json_encode($polar['200+']) ?>;
    var directionsPolar200Data = <?= json_encode($polar['200']) ?>;
    var directionsPolar100Data = <?= json_encode($polar['100']) ?>;
    var directionsPolar50Data = <?= json_encode($polar['50']) ?>;
    var directionsRangeData = <?= json_encode($range) ?>;

    var weeklyCategories = <?= json_encode($column); ?>;
    var weeklyAircraft = <?= json_encode(array_map('intval', array_column($barChart, 'hits'))) ?>;
    var weeklyPositions = <?= json_encode(array_map('intval', array_column($barChart, 'positions'))) ?>;

    var histogramHits = <?= json_encode($hitsData) ?>;
    var histogramData = <?= json_encode($positionsData) ?>;

    var dateForCharts = '<?= $today; ?>';

    $('#js-dateChange').on('change', function() {
        $('#js-dateChangeForm').submit();
    });

</script>
<script src="/js/graphs.js"></script>
