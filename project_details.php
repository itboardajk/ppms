<?php

$module_name = 'projects';
include('classes/config.php');
authenticate();

    $viewFlag=authorizeAccess($module_name,'view');
    $addFlag=authorizeAccess($module_name,'add');
    $editFlag=authorizeAccess($module_name,'edit');
    $deleteFlag=authorizeAccess($module_name,'delete');
    //Deliverables Access
    $deliverables_viewFlag=authorizeAccess('deliverables','view');
    //Updates Access
    $updates_viewFlag=authorizeAccess('updates','view');
    
    //Expenses Access
    $expenses_viewFlag=authorizeAccess('expenses','view');
    
    //Head Access
    $heads_viewFlag=authorizeAccess('heads','view');
    
    //APOs Access
    $apo_viewFlag=authorizeAccess('apo','view');
    
    //Inspections Access
    $inspections_viewFlag=authorizeAccess('inspections','view');
    
    //Centers Access
    $centers_viewFlag=authorizeAccess('centers','view');
    
    //Inventory Access
    $inventory_viewFlag=authorizeAccess('inventory','view');
    
    //Cycles Access
    $cycles_viewFlag=authorizeAccess('project_cycles','view');
    

if(!$viewFlag){header("location:{$site_url}dashboard.php");exit();}


$pid=intval($crud->escape_string($_GET['view']));


$query="select *  from projects   where  $_my_projects_condition and id=".$pid;
$project = $crud->getData($query);
if(count($project)<1)
{
    header("location:{$site_url}projects.php");exit();
}
$project=$project[0];


$featured_image=$project['fimage'];
if(empty($featured_image))
    $featured_image = "images/project.png";



$deliverables_count = $crud->getData("SELECT COUNT(*) as total from deliverables where project_id = $pid");
$updates_count = $crud->getData("SELECT COUNT(*) as total from updates where project_id = $pid");
$inspections_count = $crud->getData("SELECT COUNT(*) as total from inspections where project_id = $pid");
$heads_count = $crud->getData("SELECT COUNT(*) as total from heads where parent_head=0 and project_id = $pid");
$apo_count = $crud->getData("SELECT COUNT(*) as total from apo where project_id = $pid");
$centers_count = $crud->getData("SELECT COUNT(*) as total from centers where project_id = $pid");
$cycles_count = $crud->getData("SELECT COUNT(*) as total from project_cycles where project_id = $pid");

$total_allocation=0;
$total_allocation = $crud->getData("SELECT SUM(allocation) as total from apo where project_id = $pid");
if(count($total_allocation)>0)
    $total_allocation = $total_allocation[0]['total'];

$allocation_percent = 0 ;
if($project['budget']>0 && $total_allocation>0 )
    $allocation_percent = ($total_allocation / $project['budget']) * 100 ;


$exps = $crud->getData("SELECT SUM(cost) as total,status from expenses where project_id = $pid group by status");
$expenses=array('Booked'=>0,'Released'=>0);
foreach ($exps as $row) {
    if($row['total']>0)
        $expenses[$row['status']]=$row['total'];
}

$total_expenses = $expenses['Booked']+$expenses['Released'];

$total_remaining = $total_allocation - $total_expenses;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Project Details | <?php echo  $site_title?></title>
    <?php include_once('include/head.php');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.21/datatables.min.css"/>
	<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" /> -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.css"/>
  <script src="https://cdn.tiny.cloud/1/iclvnebsn2aw7x8c8huypa3s7aaapadca31bpipiphyeev4a/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">		
			<div class="span12">
					<div class="content">
					      <?php if(!empty(@$errmsg)){?>
					        <div class="alert alert-danger alert-dismissible fade in" role="alert">
					          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
					          <?php echo $errmsg;?>
					        </div>
					      <?php } else if(!empty(@$sucmsg)){?>
					        <div class="alert alert-success alert-dismissible fade in" role="alert">
					          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
					          <?php echo $sucmsg;?>
					        </div>
					      <?php }?>
	                    <div class="module">
	                    	<div class="module-body">
                                <div class="profile-head media">
                                    <a href="<?php echo $featured_image?>" target="_blank" class="media-avatar pull-left">
                                        <img src="<?php echo $featured_image?>">
                                    </a>
                                    <div class="media-body">
                                        <h4><?php echo $project["title"]?>
                                            <div class="pull-right">
                                                <?php if($editFlag){?><a href="projects.php?view=<?php echo $pid;?>" class="btn btn-primary "><i class="icon-edit"></i> Edit </a><?php }?>
                                            </div>
                                        </h4> 

                                        <div class="profile-brief"><?php echo $project["details"]?></div>

                                        <div class="profile-details">
                                            <span style="margin-right: 20px;"><b>Budget:</b> <?php echo $project["budget"]+0 ?> m</span>
                                            <?php if(!empty($project["start_date"]) && $project["start_date"]!='0000-00-00'){
                                                    echo '<span style="margin-right: 20px;">'; 
                                                        echo '<b>Project Tenure:</b> '.date("d M, Y", strtotime($project["start_date"]));
                                                        if(!empty($project["end_date"]) && $project["end_date"]!='0000-00-00'){
                                                            echo ' -- '.date("d M, Y", strtotime($project["end_date"]));
                                                        }
                                                    echo '</span>';
                                                }
                                            ?></span>
                                            <?php

                                                if(!empty($project["revision_date"]) && $project["revision_date"]!='0000-00-00'){ 
                                                    echo '<span style="margin-right: 20px;"><b>Revised Till:</b> '.date("d M, Y", strtotime($project["revision_date"])).'</span>';
                                                }
                                            ?>
                                            <span style="margin-right: 20px;"><b>Status:</b> <?php echo $project["status"]?></span> 
                                            
                                            <?php
                                                $Attachments = array_filter(explode(',',$project['images']));
                                                if(count($Attachments)>0){
                                                    echo '<span style="margin-right: 20px;"><b>Attachments:</b>';
                                                        foreach ($Attachments as $key => $value) {
                                                            echo '<a href="'.htmlentities($value).'" target="_blank">View Attachment</a> ';
                                                        }
                                                    echo '</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="detail_icons">
                                <a href="javascript:;" data-target="#progress" class="btn-box active small" ><i class="icon-dashboard"></i><b><?php echo $project['completed_percentage']?>%</b><p class="text-muted">Progress</p></a>
                                <?php if($deliverables_viewFlag){?>
                                    <a href="javascript:;" data-target="#deliverables" class="btn-box small deliverables_anchor" ><i class="icon-check"></i><b><?php echo $deliverables_count[0]['total']?></b><p class="text-muted">Deliverables</p></a>
                                <?php }?>                                
                                <?php if($updates_viewFlag){?>
                                    <a href="javascript:;" data-target="#updates" class="btn-box small" ><i class="icon-camera"></i><b><?php echo $updates_count[0]['total']?></b><p class="text-muted">Updates</p></a>
                                <?php }?>
                                <?php if($inspections_viewFlag){?>
                                    <a href="javascript:;" data-target="#inspections" class="btn-box small" ><i class="icon-user-md"></i><b><?php echo $inspections_count[0]['total']?></b><p class="text-muted">Inspection</p></a>
                                <?php }?>
                                <?php if($heads_viewFlag){?>
                                    <a href="javascript:;" data-target="#heads" class="btn-box small" ><i class="icon-sitemap"></i><b><?php echo $heads_count[0]['total']?></b><p class="text-muted">Heads</p></a>
                                <?php }?>
                                <?php if($apo_viewFlag){?>
                                    <a href="javascript:;" data-target="#apo" class="btn-box small apo_anchor" ><i class="icon-screenshot"></i><b><?php echo $apo_count[0]['total']?></b><p class="text-muted">APOs</p></a>
                                <?php }?>
                                <?php if($expenses_viewFlag){?>
                                    <a href="javascript:;" data-target="#expenses" class="btn-box small expences_anchor" ><i class="icon-money"></i><b><?php echo $total_expenses?>m</b><p class="text-muted">Expenses</p></a>
                                <?php }?>
                                <?php if($centers_viewFlag){?>
                                    <a href="javascript:;" data-target="#centers" class="btn-box small" ><i class="icon-building"></i><b><?php echo $centers_count[0]['total']?></b><p class="text-muted">Branches/Centers</p></a>
                                <?php }?>
                                <?php if($inventory_viewFlag){?>
                                    <a href="javascript:;" data-target="#inventory" class="btn-box small" ><i class="icon-building"></i><b><?php echo $centers_count[0]['total']?></b><p class="text-muted">Project Inventory</p></a>
                                <?php }?>
                                <?php if($cycles_viewFlag){?>
                                    <a href="javascript:;" data-target="#cycles" class="btn-box small" ><i class="icon-file-alt"></i><b><?php echo $cycles_count[0]['total']?></b><p class="text-muted">Project Cycles</p></a>
                                <?php }?>
                            </div>
                            <div class="details_contents">
                                <div class="details_content" id="progress" data-url="#" data-loaded="true" style="display: block;">
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <p>
                                                <a href="javascript:;" onclick="$('.deliverables_anchor').trigger('click');"><strong>Phisical Progress</strong></a> <strong class="pull-right muted"><?php echo $project['completed_percentage']?>%</strong>
                                            </p>
                                            <div class="progress">
                                                <div class="bar bar-success" style="width: <?php echo $project['completed_percentage']?>%;"><?php echo $project['completed_percentage']?>%</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-fluid">
                                        <div class="span12" style="margin-top: 30px;">
                                            <p>
                                                <a href="javascript:;" onclick="$('.apo_anchor').trigger('click');"><strong>Allocation Progress</strong></a> <strong class="pull-right muted"><?php echo $total_allocation+0; echo 'm / '; echo $project['budget']+0; echo 'm';?></strong>
                                            </p>
                                            <div class="progress">
                                                <div class="bar bar-primary" style="width: <?php echo $allocation_percent?>%;"><?php echo round($allocation_percent)?>%</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-fluid">
                                        <?php if($total_allocation>0){

                                            $rel_per = number_format(($expenses['Released']/$total_allocation)*100);
                                            $book_per = number_format(($expenses['Booked']/$total_allocation)*100);
                                            $rem_per = number_format(($total_remaining/$total_allocation)*100);
                                            ?>
                                            <div class="span12" style="margin-top: 30px;">
                                                <p>
                                                    <a href="javascript:;" onclick="$('.expences_anchor').trigger('click');"><strong>Finance Usage Progress</strong></a> <strong class="pull-right "><span class="muted"><?php  echo $total_expenses+0; echo 'm / '; echo $total_allocation+0; echo 'm';?></span></strong>
                                                </p>
                                                <div class="progress" style="overflow: visible; text-align: right;">
                                                    <?php if($expenses['Released']>0){?>
                                                        <div class="bar bar-danger" role="progressbar" style="width:<?php echo $rel_per?>%">
                                                        <?php echo $expenses['Released']+0?>m<br><span style="color:#777">Expenses(<?php echo $rel_per?>%)</span>
                                                        </div>
                                                    <?php }?>
                                                    <?php if($expenses['Booked']>0){?>
                                                        <div class="bar bar-warning" role="progressbar" style="width:<?php echo $book_per?>%">
                                                        <?php echo $expenses['Booked']+0?>m<br><span style="color:#777">Booked(<?php echo $book_per?>%)</span>
                                                        </div>
                                                    <?php }?>
                                                    <?php if($total_remaining>0){?>
                                                        <!-- <div class="bar bar-success" role="progressbar" style="width:<?php echo number_format(($total_remaining/$total_allocation)*100)?>%">
                                                        </div> -->

                                                        <?php echo $total_remaining+0?>m<br><span style="color:#777">Remaining(<?php echo $rem_per;?>%)</span>
                                                    <?php }?>
                                                </div>
                                            </div> 
                                        <?php }?>
                                    </div>
                                </div>
                                <?php if($deliverables_viewFlag){?>
                                    <div class="details_content" id="deliverables" data-url="ajax_project_deliverables.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="#"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div>
                                <?php }?>                                
                                <?php if($updates_viewFlag){?>
                                    <div class="details_content" id="updates" data-url="ajax_project_updates.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="#"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div>
                                <?php }?>
                                <?php if($inspections_viewFlag){?>
                                    <div class="details_content" id="inspections" data-url="ajax_project_inspections.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="javascript:;"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div>
                                <?php }?>
                                <?php if($heads_viewFlag){?>
                                    <div class="details_content" id="heads" data-url="ajax_project_heads.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="javascript:;"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div> 
                                <?php }?>
                                <?php if($apo_viewFlag){?>
                                    <div class="details_content" id="apo" data-url="ajax_project_apo.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="javascript:;"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div> 
                                <?php }?>
                                <?php if($expenses_viewFlag){?>
                                    <div class="details_content" id="expenses" data-url="ajax_project_expenses.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="javascript:;"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div> 
                                <?php }?>
                                <?php if($centers_viewFlag){?>
                                    <div class="details_content" id="centers" data-url="ajax_project_centers.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="javascript:;"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div> 
                                <?php }?>
                                <?php if($inventory_viewFlag){?>
                                    <div class="details_content" id="inventory" data-url="ajax_project_inventory.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="javascript:;"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div> 
                                <?php }?>
                                <?php if($cycles_viewFlag){?>
                                    <div class="details_content" id="cycles" data-url="ajax_project_cycles.php" data-loaded="false">
                                        <div class="ajax_result"></div>
                                        <div class="tab_content"></div>
                                        <div class="media stream loading">
                                            <a href="javascript:;"><i class="icon-refresh shaded rotate"></i>Loading</a>
                                        </div>
                                    </div> 
                                <?php }?>
                            </div>
						</div>	
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

    <?php include('include/footer.php');?>

    <?php include_once('include/foot.php');?>
	
	
 
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.21/datatables.min.js"></script>
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script> -->
	
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5r4DeoecbY8F8KEOCONRATBwovo04Rlw&libraries=places"></script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="scripts/exif.min.js" type="text/javascript"></script>
    <script  type="text/javascript">  
        var uluru, map, marker,uluru2, map2, marker2;
        /*function readURL(input) {
            //reading the Uploading file
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.readAsDataURL(input.files[0]);
            }
        }*/
        function generate_lat_lang(imageData='',file_wrapper){
            //geting cordinates of latitude
            var latDegree = imageData.exifdata.GPSLatitude[0].numerator;
            var latMinute = imageData.exifdata.GPSLatitude[1].numerator;
            var latSecond = imageData.exifdata.GPSLatitude[2].numerator/imageData.exifdata.GPSLatitude[2].denominator;
            

            //geting cordinates of longitude
            var lonDegree = imageData.exifdata.GPSLongitude[0].numerator;
            var lonMinute = imageData.exifdata.GPSLongitude[1].numerator;
            var lonSecond = imageData.exifdata.GPSLongitude[2].numerator/imageData.exifdata.GPSLongitude[2].denominator;
                            
            if(file_wrapper.find('.readinfo').data('form') == 'add')
            {
                document.getElementById("latitude").value = (latDegree + (latMinute/60) + (latSecond/3600)).toFixed(8);
                document.getElementById("longitude").value = (lonDegree + (lonMinute/60) + (lonSecond/3600)).toFixed(8);   

                var position =  new google.maps.LatLng(document.getElementById("latitude").value, document.getElementById("longitude").value);

                marker2.setPosition(position); 
                map2.setCenter(marker2.getPosition());  
            }
            else
            {
                document.getElementById("elatitude").value = (latDegree + (latMinute/60) + (latSecond/3600)).toFixed(8);
                document.getElementById("elongitude").value = (lonDegree + (lonMinute/60) + (lonSecond/3600)).toFixed(8);   

                var position =  new google.maps.LatLng(document.getElementById("elatitude").value, document.getElementById("elongitude").value);
                
                marker.setPosition(position); 
                map.setCenter(marker.getPosition());      
            }
            file_wrapper.find(".img_latlong_msg").html('Position found, and updated.');
        }

		$(document).ready(function() {
            $('.datatable-1').dataTable( {
               /* initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        var select = $('<select style="width:100%"><option value=""></option></select>')
                            .appendTo( $(column.header()) )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
         
                                column
                                    .search( val ? '^'+val+'$' : '', true, false )
                                    .draw();
                            } );
         
                        column.data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        } );
                    } );
                },*/
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
 
                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
 
                    // Total over all pages
                    total = api
                        .column( 2 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
         
                    // Total over this page
                    pageTotal = api
                        .column( 2, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    

                    $( api.column( 0 ).footer() ).html(
                        'Page Total: '+pageTotal+'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Grand Total: '+ total
                    );
                    
                }
            });
           


			/*$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
            */

			//$(".multi-select").select2();

            

            

            $('.detail_icons a').click(function (e) {
                $('.detail_icons .btn-box').removeClass('active');
              $('.details_contents .details_content').hide();
              $(this).addClass("active");
              var target = $(this).data("target");
              //console.log(e);
              $('.details_contents '+target).show();

              if( ! $(target).data('loaded') )
                  loadtabedcontent(target);
            });
            $( "body" )
                .delegate(".refresh","click", function(e){
                    e.preventDefault();
                    var target = $(this).parents('.details_content').attr("id");
                    loadtabedcontent("#"+target);
                })
                .delegate(".accordion","click",function(e){
                    $(this).toggleClass("active");

                    /* Toggle between hiding and showing the active panel */
                    var panel = $(this).next();
                    if (panel.is(':visible')) {
                      panel.hide();
                    } else {
                      panel.show();
                    }

                    var id = $(this).data('id');
                    var url = $(this).data('url');
                    var loaded = $('#'+id).data('loaded');

                    if(!loaded)
                    {
                        loadCycle(id,url);
                    }
                })
                .delegate(".openfiledialog","click",function(e){

                    $(this).parents('.Pin').find(".readinfo").trigger("click");
                })        
                .delegate(".auth_dep","change",function(e){
                    var mthis = $(this);
                    var dep_id=mthis.val();
                    if(dep_id!='')
                    {
                        $.ajax({
                          type: "POST",
                          url: "get_depusers.php",
                          data:'did='+dep_id,
                          beforeSend:function(){
                            mthis.parents(".autority").find('.auth_user').html('<option>Loading...</option>');
                          },
                          success: function(data){
                            if(data=='')
                              data='<option>No user found.</option>';   
                            mthis.parents(".autority").find('.auth_user').html(data);
                          }
                        });    
                    }
                    else
                    {
                        mthis.parents('.autority').find('.auth_user').html('<option>Select User</option><option>Select department to load users</option>');
                    }
                                         
                })        
                .delegate(".readinfo","change",function(e){
                    var file_wrapper = $(this).parents('.Pin');

                    file_wrapper.find(".img_latlong_msg").html('There is no geo location information with this image. Please select GeoTagged image.');

                    EXIF.getData(e.target.files[0], function() {
                     
                       EXIF.getData(this,()=>{
                            //console.log(this)
                            if(Object.keys(this.exifdata).length > 0){
                                generate_lat_lang(this,file_wrapper);
                            }
                        });
                    });
                })
                .delegate(".calcQty","blur", function(e){
                    $enteredQty = $(this).val();
                    if($enteredQty>0)
                    {
                        $qty = parseFloat($(this).parents('tr').find(".allocated_qty").text());
                        $price = parseFloat($(this).parents('tr').find(".allocated_price").text());

                        if($qty>0 && $price>0)
                        {
                            $qtyperitem = $price / $qty;
                            $val = $qtyperitem * $enteredQty;

                            $(this).parents('tr').find(".calcPrice").val( $val.toFixed(6));
                        }
                        else
                        {
                            $(this).parents('tr').find(".calcPrice").val(0);
                        }
                    }
                    else
                    {
                        $(this).parents('tr').find(".calcPrice").val(0);
                    }  
                    sumtotal();
                })
                .delegate(".calcPrice","blur", function(e){
                    sumtotal();                    
                })
                .delegate(".expense_status","change", function(e){
                    if($(this).val()=='Booked')
                    {
                        $(".release_date").hide();
                    }
                    else
                    {
                        $(".release_date").show();
                    }
                })
                .delegate(".head_expense","change", function(e){
                    if($(this).val()=='')
                    {
                        $(this).parent().find(".head_allocation").html("");
                    }
                    else
                    {
                        var res = $(this).val().split(":");
                        console.log(res);
                        $(this).parent().find(".head_allocation").html("<b>Total Allocation: </b>"+res[1]+" ");
                    }
                })
                .delegate(".headchange","change", function(e){
                    if($(this).val()!='0')
                    {
                        $(this).parents('form').find(".h_with_root").hide();
                        $(this).parents('form').find(".h_with_child").show();
                    }
                    else
                    {
                        $(this).parents('form').find(".h_with_root").show();
                        $(this).parents('form').find(".h_with_child").hide();
                    }
                })
                .delegate(".otherAjax","click", function(e){
                    e.preventDefault();
                    var source = $(this);
                    var url = $(this).data('url');
                    var reload = $(this).data('reloadtab');
                    var tab_id = '#'+$(this).parents('.details_content').attr('id');
                    var ajax_result = source.parents('.details_content').find('.ajax_result');
                    
                    $.ajax({
                        dataType: 'json',
                        type: "GET",
                        url: url,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){
                            source.attr('disabled',true);
                        },
                        success: function(data)
                        {
                            
                            ajax_result.html(data.msg);
                            source.removeAttr('disabled');

                            if(data.status && reload)
                            {
                                loadtabedcontent(tab_id);
                            }
                        },
                        error:function(jqXHR, textStatus, errorThrown){
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            ajax_result.html('<div class="alert alert-danger alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Something went wrong! please try again.</div>');
                            source.removeAttr('disabled');
                        }
                    });
                })
                .delegate(".ajaxForm","submit", function(e){
                    e.preventDefault();

                    var formTag = $(this);
                    var formObj=$(this)[0];
                    var formData = new FormData(formObj);
                    var ajax_result = formTag.parents('.details_content').find('.ajax_result');
                    var tab_id = '#'+formTag.parents('.details_content').attr('id');
                    var submitbtn =  formTag.find('.submitbtn');
                    var url = formTag.attr('action');
                    $.ajax({
                        dataType: 'json',
                        type: "POST",
                        url: url,
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){
                            submitbtn.attr('disabled',true);
                        },
                        success: function(data)
                        {
                            
                            ajax_result.html(data.msg);
                            submitbtn.removeAttr('disabled');

                            if(data.status)
                            {
                                formObj.reset();
                                loadtabedcontent(tab_id);
                            }
                        },
                        error:function(jqXHR, textStatus, errorThrown){
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            ajax_result.html('<div class="alert alert-danger alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Something went wrong! please try again.</div>');
                            submitbtn.removeAttr('disabled');
                        }
                    });
                })
                .delegate(".backFromEdit","click", function(e){
                    var aTag = $(this);
                    var tab_id = '#'+aTag.parents('.details_content').attr('id');
                    loadtabedcontent(tab_id);
                })
                .delegate(".ajaxEdit","click", function(e){
                    e.preventDefault();

                    var aTag = $(this);
                    var content_div = aTag.parents('.tab_content');
                    var ajax_result = aTag.parents('.details_content').find('.ajax_result');

                    $.ajax({
                        url: aTag.attr('href'),
                        beforeSend:function(){
                            content_div.addClass('disabledDiv');
                        },
                        success: function(data)
                        {
                            content_div.html(data);
                            content_div.removeClass('disabledDiv');
                        },
                        error:function(jqXHR, textStatus, errorThrown){

                            ajax_result.html('<div class="alert alert-danger alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Something went wrong! please try again.</div>');
                            content_div.removeClass('disabledDiv');
                        },
                    });
                })
                .delegate(".ajaxDelete","click", function(e){
                    e.preventDefault();
                    if(confirm('Are you sure you want to delete?'))
                    {
                        var aTag = $(this);
                        var tab_id = '#'+aTag.parents('.details_content').attr('id');
                        var content_div = aTag.parents('.tab_content');
                        var ajax_result = aTag.parents('.details_content').find('.ajax_result');
                        $.ajax({
                            dataType: 'json',
                            type: "POST",
                            url: aTag.attr('href'),
                            processData: false,
                            contentType: false,
                            beforeSend:function(){
                                content_div.addClass('disabledDiv');
                            },
                            success: function(data)
                            {
                                ajax_result.html(data.msg);
                                content_div.removeClass('disabledDiv');

                                if(data.status)
                                {
                                    loadtabedcontent(tab_id);
                                }
                            },
                            error:function(jqXHR, textStatus, errorThrown){

                                ajax_result.html('<div class="alert alert-danger alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Something went wrong! please try again.</div>');
                                content_div.removeClass('disabledDiv');
                            },
                        });
                    }
                });
		});
        function loadtabedcontent(tab_id)
        {
            //alert(tab_id);
            var ajax_url = $(tab_id).data('url');
            $.ajax({
                type: "GET",
                url: ajax_url+"?pid=<?php echo $pid?>",//&action=load&paged=1
                beforeSend:function(){
                  $(tab_id+' .tab_content').hide();
                  $(tab_id+' .loading').show();
                },
                success: function(data){
                    $(tab_id).data('loaded',true);
                  $(tab_id+' .tab_content').html(data);

                  $(tab_id+' .tab_content').show();

                  $(tab_id+' .loading').hide();
                }
            });  
        }
        function loadCycle(id,url)
        {
            $.ajax({
                type: "GET",
                url: url,//&action=load&paged=1
                beforeSend:function(){
                  $('#'+id+' .acc_content').html('');
                  $('#'+id+' .loading_cycle').show();
                },
                success: function(data){
                    $('#'+id).data('loaded',true);

                    $('#'+id+' .acc_content').html(data);

                    $('#'+id+' .loading_cycle').hide();
                }
            });  
        }
        function sumtotal(){
            var total = 0;
            $(".calcPrice").each(function(){
                if(  $(this).val()>0 )
                    total += parseFloat($(this).val());
            });

            $(".total_alloc").val(total.toFixed(6));
        }
	</script>
</body>
</html>