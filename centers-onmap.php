<?php include("classes/config.php"); authenticate();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Branches/Centers | <?php echo @$site_title?></title>
  

  <link rel="apple-touch-icon" sizes="57x57" href="images/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="images/favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="images/favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="images/favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="images/favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="images/favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="images/favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="images/favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="images/favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="images/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">
  <link rel="manifest" href="images/favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="images/favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  

  <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
  <link type="text/css" href="css/theme.css" rel="stylesheet">
  <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
  <link type="text/css" href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
</head>
<body>
  <?php include('include/header.php');?>

  <div id="points" style="height: calc(100vh - 65px);"></div>
            
  <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
  <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
  <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>  
  <script src="scripts/common.js"></script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5r4DeoecbY8F8KEOCONRATBwovo04Rlw&callback=initMap"></script>
  <script>        
    function initMap() {
       <?php 


        $pid=intval($crud->escape_string($_GET['project_id']));

        $centers_details=$crud->getData("select * from centers where project_id = $pid order by id DESC");


       if(count($centers_details)>0)
       {


        ?>            
        var locations = [<?php 
          $count=0; 
          $lat=0;
          $long=0; 
          foreach($centers_details as $row){ 
            $popup_info='';

            if(!empty($row['images'])){ 
              $img = explode(',', $row['images']);
              $popup_info="<img src='".$img[0]."' style='width:100%' >"; 
            }
            $popup_info.="<h3 style='margin:20px 0 0'>".$row['title']." <small>".$row['address']."</small></h3>";
            
            $popup_info .= "<p><b>Incharge:</b> ".$row['focal_person'];
            if(!empty($row['focal_person_phone']))
            {
                 $popup_info .= "<a href='tel:".$row['focal_person_phone']."' style='margin-left:20px'><i class='icon-phone-sign'></i> ".$row['focal_person_phone']."</a>";
            }
            if(!empty($row['focal_person_email']))
            {
                 $popup_info .= "<a href='mailto:".$row['focal_person_email']."' style='margin-left:20px'><i class='icon-envelope-alt'></i> ".$row['focal_person_email']."</a>";
            }
            $popup_info .= " </p>";
            
            $popup_info .= "<p>".nl2br(str_replace(array("\r\n", "\r", "\n"), "<br />", $row['description']))."</p>";
            $popup_info .= "<div class='btn-controls'><div class='btn-box-row row-fluid'><a href='javascript:;' class='btn-box small span2' title='Staff' style='background-color: #f9f9f9;'><i class='icon-group'></i><b>".((!empty($row['total_staff']))?$row['total_staff']:'0')."</b><p class='text-muted' style='margin-bottom: 0px;'>Total Staff</p></a><a href='javascript:;' class='btn-box small span2' title='Workstations' style='background-color: #f9f9f9;'><i class='icon-desktop'></i><b>".((!empty($row['workstaions']))?$row['workstaions']:'0')."</b><p class='text-muted' style='margin-bottom: 0px;'>Workstations</p></a><a href='javascript:;' class='btn-box small span2' title='Laptops' style='background-color: #f9f9f9;'><i class='icon-laptop'></i><b>".((!empty($row['laptops']))?$row['laptops']:'0')."</b><p class='text-muted' style='margin-bottom: 0px;'>Laptops</p></a><a href='javascript:;' class='btn-box small span2' title='Printer' style='background-color: #f9f9f9;'><i class='icon-print'></i><b>".((!empty($row['printers']))?$row['printers']:'0')."</b><p class='text-muted' style='margin-bottom: 0px;'>Printers</p></a><a href='javascript:;' class='btn-box small span2' title='Scanner' style='background-color: #f9f9f9;'><i class='icon-camera'></i><b>".((!empty($row['scanners']))?$row['scanners']:'0')."</b><p class='text-muted' style='margin-bottom: 0px;''>Scanner</p></a></div></div>";

            echo '["'.$row['title'].'", '.$row['latitude'].','.$row['longitude'].', "'.$popup_info.'"],'; 
            $lat += $row['latitude'];$long += $row['longitude'];    $count++;
          }?>];

        var uluru = {lat: <?php echo $lat / $count ;?>, lng: <?php echo $long / $count ;?>};
        var map = new google.maps.Map(document.getElementById('points'), {zoom: 9, center: uluru});
        
        var infowindow = new google.maps.InfoWindow();
        var marker, i;

        for (i = 0; i < locations.length; i++) {  
          marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
          });

          google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
              infowindow.setContent(locations[i][3]);
              infowindow.open(map, marker);
            }
          })(marker, i));
        }
      <?php }else{?>
        $('#points').html('No record found');
      <?php }?>
    }


  </script>
</body>
</html>