<?php /**
 * @var $this Cake\View\View
 * @var $polar
 * @var $range
 * @var $totalHits
 * @var $totalFlights
 * @var $totalPositions
 * @var $aircraftSeen
 * @var $maxDistance
 * @var $hitsData
 * @var $positionsData
 * @var $today
 * @var $barChart
 * @var $binnedCount
 * @var $contactsData
 * @var $totalDistances
 * @var $score
 * @var $emergencies
 * @var $maxDistanceDirection
 * @var $maxDistanceFlight
 * @var $binned
 * @var $thirtyBarChart
 */ ?>
<h1>Home</h1>
<h2>Viewing stats for <?= $today ?></h2>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>

<div style="float: right">
    <?php echo $this->Form->create(null, ['id' => 'js-dateChangeForm']);
    $extra = (isset($today)) ? 'value="'.$today.'"' : '';
    echo '<input name="date" type="date" id="js-dateChange" min= "2018-05-03" max="' . date('Y-m-d') . '" ' . $extra . '/>' ;//$this->Form->control('date', ['type' => 'date', 'id' => 'js-dateChange', 'min' => '2018-05-03', 'max' => date('Y-m-d')]);
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
        <div class="stats-col pull-right stats-col-value" id="maximum-distance"><?= $maxDistance ?>nm in the <?= $maxDistanceDirection ?> direction (<?= $maxDistanceFlight ?>) <?php if($binnedCount): ?><br /> (Flights binned: <?= implode(', ', $binned) ?>)<?php endif; ?></div>
        <div class="clearfix"></div>
    </div>
    <div class="stats-row pull-right no-border">
        <div class="stats-col pull-left title">Hits reported</div>
        <div class="stats-col pull-right stats-col-value" id="hits-stats"><?= $totalHits ?></div>
        <div class="clearfix"></div>
    </div>
    <div class="stats-row pull-right no-border">
        <div class="stats-col pull-left title">Average distance</div>
        <div class="stats-col pull-right stats-col-value" id="hits-stats"><?= number_format($totalDistances/$totalPositions, 2) ?></div>
        <div class="clearfix"></div>
    </div>
    <div class="stats-row pull-right no-border">
        <div class="stats-col pull-left title">Score</div>
        <div class="stats-col pull-right stats-col-value" id="hits-stats"><?= number_format($score, 0) ?></div>
        <div class="clearfix"></div>
    </div>
    <?php if($emergencies):?>
    <div class="stats-row pull-right no-border">
        <div class="stats-col pull-left title">Emergencies</div>
        <div class="stats-col pull-right stats-col-value" id="hits-stats"><?= count($emergencies);
        echo "<ul>";
        foreach($emergencies as $flight) {
            echo "<li>$flight</li>";
        }
        echo "</ul>";
        ?></div>
        <div class="clearfix"></div>
    </div>
    <?php endif; ?>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div class="stats-data">
    <div class="stats-box" id="directions"></div>
</div>
<div class="clearfix"></div>
<div class="stats-data">
    <div class="stats-box" id="weekly"></div>
    <div class="stats-box" id="histogram"></div>
</div>
<div class="stats-data">
    <div class="stats-box" id="contacts"></div>
</div>
<div class="stats-data" style="width: 100%; height: 600px">
    <div class="stats-box" id="thirty" style="width: 100%; height: 600px"></div>
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
<?php $column = array_column($barChart, 'dayOfYear');
    array_walk($column, function(&$value) {
        $value -= 1;
        $value = \DateTime::createFromFormat('Y-m-d', '2018-01-01')->add(new \DateInterval("P{$value}D"))->format('D');
    });

    $thrityCol = array_column($thirtyBarChart, 'dayOfYear');
    array_walk($thrityCol, function(&$value) {
        $value -= 1;
        $value = \DateTime::createFromFormat('Y-m-d', '2018-01-01')->add(new \DateInterval("P{$value}D"))->format('jS M');
    });
?>
<script>

    var directionsPolar200PData = <?= json_encode($polar['200+']) ?>;
    var directionsPolar200Data = <?= json_encode($polar['200']) ?>;
    var directionsPolar100Data = <?= json_encode($polar['100']) ?>;
    var directionsPolar50Data = <?= json_encode($polar['50']) ?>;
    var directionsRangeData = <?= json_encode($range) ?>;

    var weeklyCategories = <?= json_encode($column); ?>;
    var weeklyAircraft = <?= json_encode(array_map('intval', array_column($barChart, 'hits'))) ?>;
    var weeklyPositions = <?= json_encode(array_map('intval', array_column($barChart, 'positions'))) ?>;


    var monthlyCategories = <?= json_encode($thrityCol); ?>;
    var monthlyAircraft = <?= json_encode(array_map('intval', array_column($thirtyBarChart, 'hits'))) ?>;
    var monthlyPositions = <?= json_encode(array_map('intval', array_column($thirtyBarChart, 'positions'))) ?>;


    var histogramHits = <?= json_encode($hitsData) ?>;
    var histogramData = <?= json_encode($positionsData) ?>;

    var dateForCharts = '<?= $today; ?>';

    var contactsData = <?= json_encode($contactsData) ?>;

    $('#js-dateChange').on('change', function() {
        $('#js-dateChangeForm').submit();
    });

</script>
<script src="/js/graphs.js"></script>
