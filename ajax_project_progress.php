<?php 
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));

$query="select *  from projects   where id=".$pid;
$project = $crud->getData($query);
$project=$project[0];
?>
<p>
    <strong>Phisical Progress</strong> <span class="pull-right small muted"><?php echo $project['completed_percentage']?>%</span>
</p>
<div class="progress tight">
    <div class="bar <?php echo $project['bar_class']?>" style="width: <?php echo $project['completed_percentage']?>%;">
    </div>
</div>
<p>
</p>
<div id="barchart_material" style="height: 500px;"></div>

<script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Allocation', 'Expenses Releases', 'Expenses Booked'],
          ['2014-15', 1000, 400, 200],
          ['2015-16', 1170, 460, 250],
          ['2016-17', 660, 1120, 300],
          ['2017-18', 1030, 540, 350]
        ]);

        var options = {
          chart: {
            title: 'Financial Progress',
            subtitle: 'Allocation, and Expenses',
          },
          bars: 'veritcal', // Required for Material Bar Charts.
          series: {
            0: { axis: 'Allocation' }, // Bind series 0 to an axis named 'Allocation'.
          },
          axes: {
            y: {
              Allocation: {label: 'millions'}, // Bottom x-axis.
            }
          }
        };

        var chart = new google.charts.Bar(document.getElementById('barchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
    }
</script>
