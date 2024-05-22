<?php 
$module_name = 'centers';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));

$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');

if($viewFlag){
    $centers_details=$crud->getData("SELECT * from centers where project_id = $pid order by id DESC");
    ?>
    <?php if($addFlag){?>
        <div class="module addCentersModule tabmodule">
            <div class="module-head">
                <h3>Add Branch/Center <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addCentersModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
            </div>
            <div class="module-body">
                
                <form class="form-horizontal row-fluid ajaxForm" name="addCentersForm" method="post"  enctype="multipart/form-data" action="ajax_project_centers_add.php?pid=<?php echo $pid;?>">
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Title</label>
                        <div class="controls">
                            <input type="text" name="title" class="span12 tip" value="<?php echo @$_POST['title']?>" required="">
                        </div>
                    </div>                                                    
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Address</label>
                        <div class="controls">
                            <input type="text" name="address" class="span12 tip" required="" value="<?php echo @$_POST['address']?>">
                        </div>
                    </div>       
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Focal Person</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">Name</span><input name="focal_person" class="span8" type="text" placeholder="prepend" value="<?php echo @$_POST['focal_person']?>">       
                            </div>
                            <div class="input-prepend">
                                <span class="add-on">Email</span><input name="focal_person_email" class="span8" type="text" placeholder="prepend" value="<?php echo @$_POST['focal_person_email']?>">       
                            </div>
                            <div class="input-prepend">
                                <span class="add-on">Contact</span><input name="focal_person_phone" class="span8" type="text" placeholder="prepend" value="<?php echo @$_POST['focal_person_phone']?>">       
                            </div>
                        </div>
                    </div>                                                                                         
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Hardware</label>
                        <div class="controls">
                            <div class="input-prepend span3">
                                <span class="add-on">Workstaions</span><input name="workstaions" class="span6" type="number" placeholder="prepend" value="<?php echo isset($_POST['workstaions'])?$_POST['workstaions']:0?>">       
                            </div>
                            <div class="input-prepend span3">
                                <span class="add-on">Laptops</span><input name="laptops" class="span6" type="number" placeholder="prepend" value="<?php echo isset($_POST['laptops'])?$_POST['laptops']:0 ?>">       
                            </div>
                            <div class="input-prepend span3">
                                <span class="add-on">Printers</span><input name="printers" class="span6" type="number" placeholder="prepend" value="<?php echo isset($_POST['printers'])?$_POST['printers']:0?>">       
                            </div>
                            <div class="input-prepend span3">
                                <span class="add-on">Scanners</span><input name="scanners" class="span6" type="number" placeholder="prepend" value="<?php echo isset($_POST['scanners'])?$_POST['scanners']:0?>">       
                            </div>
                        </div>
                    </div>                                 
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Total Staff</label>
                        <div class="controls">
                            <input type="number" name="total_staff" class="span12 tip" value="<?php echo @$_POST['total_staff']?>">
                        </div>
                    </div>    
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Description</label>
                        <div class="controls">
                            <textarea  name="description" class="span12 tip"><?php echo @$_POST['description']?></textarea>
                        </div>
                    </div>  
                    <div class="control-group Pin">
                        <label class="control-label" for="basicinput">Pin</label>
                        <div class="controls">
                            <div id="centers_pinmap_add"  style="height: 450px;"></div>
                            <input type="hidden" name="latitude" id="latitude" value="<?php echo @$_POST['latitude']?>">
                            <input type="hidden" name="longitude" id="longitude" value="<?php echo @$_POST['longitude']?>">
                            <div style="margin-top:20px">
                                <a class="btn btn-primary openfiledialog" href="javascript:;">Get Location from Image</a>
                                <span class="img_latlong_msg"></span>
                                <span style="display: none"><input type="file" name="readinfo" class="readinfo" data-form="add"></span>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Featured Image</label>
                        <div class="controls">
                            <div class="fileswrapper" style="margin:0 0 20px 0;">
                                <input  name="fimage" type="file"  accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Images</label>
                        <div class="controls">
                            <div class="fileswrapper" style="margin: 0 0 20px 0;">
                                <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                            </div>
                            <a href="javascript:;" class="addmorefile" data-types=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">+ Add Another File</a>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" name="addCentersButton" value="Add Center" class="btn btn-primary submitbtn">
                            <input type="hidden" name="addCenter" value="addCenter">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php }?>
    <div class="actions">
        <?php if($addFlag){?><a href="javascript:;" class="btn btn-primary showModule pull-right" data-target="addCentersModule"><i class="icon-plus"></i>Add New Branch/Center</a><?php }?>

        <a href="javascript:;" class="btn btn-small refresh">
            <i class="icon-refresh shaded"></i> Refresh Branches/Centers
        </a>
    </div>
    <?php if($centers_details != false && count($centers_details)>0){?>
        <div class="alert">
            <strong><?php echo count($centers_details); ?></strong> Branches/Centers Found.<span style="float:right"><a href="centers-onmap.php?project_id=<?php echo $pid?>" target="_blank">View All On Map</a></span>
        </div>
        <div class="stream-list">
            <?php foreach($centers_details as $row){
                $center_dimg=$row['fimage'];
                if(empty($center_dimg)){
                     $center_dimg='images/center.jpg';
                }                

                $center_images = explode(',',$row['images']);

                ?>
                <div class="media stream">
                    <a href="<?php echo $center_dimg;?>" class="media-avatar medium pull-left" target="_blank">
                        <img src="<?php echo $center_dimg;?>">
                    </a>

                    <div class="media-body">
                        <div class="stream-headline">
                            <h5 class="stream-author">
                                <?php echo htmlentities($row['title']);?>                                
                                <small><?php echo $row["address"];?></small>

                                <span class="pull-right">
                                    <?php if($editFlag){?><a class="ajaxEdit" href="ajax_project_centers_edit.php?editCenter=<?php echo $row['id']?>"><i class="icon-edit"></i></a><?php }?>
                                    <?php if($deleteFlag){?><a class="ajaxDelete" href="ajax_project_centers_delete.php?deleteCenter=<?php echo $row['id']?>"><i class="icon-remove-sign"></i></a><?php }?>
                                </span>
                            </h5>

                            <div class="stream-text"><p><b>Incharge:</b> <?php echo $row['focal_person'];?><?php 
                                if(!empty($row['focal_person_phone']))
                                {
                                    echo '<a href="tel:'.$row['focal_person_phone'].'" style="margin-left:20px"><i class="icon-phone-sign"></i> '.$row['focal_person_phone'].'</a>';
                                }
                                if(!empty($row['focal_person_email']))
                                {
                                    echo '<a href="mailto:'.$row['focal_person_email'].'" style="margin-left:20px"><i class="icon-envelope-alt"></i> '.$row['focal_person_email'].'</a>';
                                }
                                
                                ?>
                                </p>
                            </div>
                            <div class="stream-text"><p><?php echo $row['description'];?></p></div>
                            <div class="btn-controls">
                                <div class="btn-box-row row-fluid">
                                    <a href="javascript:;" class="btn-box small span2" title="Staff" style="background-color: #f9f9f9;">
                                        <i class="icon-group"></i>
                                        <b><?php echo (!empty($row['total_staff']))?$row['total_staff']:'0';?></b>
                                        <p class="text-muted" style="margin-bottom: 0px;">Total Staff</p>
                                    </a>
                                    <a href="javascript:;" class="btn-box small span2" title="Workstations" style="background-color: #f9f9f9;">
                                        <i class="icon-desktop"></i>
                                        <b><?php echo (!empty($row['workstaions']))?$row['workstaions']:'0';?></b>
                                        <p class="text-muted" style="margin-bottom: 0px;">Workstations</p>
                                    </a>
                                    <a href="javascript:;" class="btn-box small span2" title="Laptops" style="background-color: #f9f9f9;">
                                        <i class="icon-laptop"></i>
                                        <b><?php echo (!empty($row['laptops']))?$row['laptops']:'0';?></b>
                                        <p class="text-muted" style="margin-bottom: 0px;">Laptops</p>
                                    </a>
                                    <a href="javascript:;" class="btn-box small span2" title="Printer" style="background-color: #f9f9f9;">
                                        <i class="icon-print"></i>
                                        <b><?php echo (!empty($row['printers']))?$row['printers']:'0';?></b>
                                        <p class="text-muted" style="margin-bottom: 0px;">Printers</p>
                                    </a>
                                    <a href="javascript:;" class="btn-box small span2" title="Scanner" style="background-color: #f9f9f9;">
                                        <i class="icon-camera"></i>
                                        <b><?php echo (!empty($row['scanners']))?$row['scanners']:'0';?></b>
                                        <p class="text-muted" style="margin-bottom: 0px;">Scanner</p>
                                    </a>
                                </div>
                            </div>
                            <?php 
                            if(count($center_images)>0){?>
                                <button class="accordion">Images</button>
                                <div class="panel">
                                <?php foreach ($center_images as $value) {
                                    if(!empty(trim($value))){
                                        $ext = strtolower($value);
                                        if(endsWith($ext,'.jpg') || endsWith($ext,'.jpeg')  || endsWith($ext,'.png')  || endsWith($ext,'.gif') ||  endsWith($ext,'.jfif')){?>
                                            <div class="stream-attachment photo" style="width: 47%;">
                                                <div class="responsive-photo">
                                                    <a href="<?php echo $value;?>" target="_blank"><img src="<?php echo $value;?>" alt=""></a>
                                                </div>
                                            </div>
                                        <?php  } else if(endsWith($ext,'.mp4') || endsWith($ext,'.mpeg') || endsWith($ext,'.webm') || endsWith($ext,'.avi') || endsWith($ext,'.flv') || endsWith($ext,'.wmv')){?>
                                            <div class="stream-attachment video"  style="width: 47%;">
                                                <div class="responsive-video">
                                                    <video width="100%" height="auto" controls>
                                                      <source src="<?php echo trim($value);?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                    </video> 
                                                </div>
                                            </div>
                                        <?php }else{?>
                                            <a target="_blank" href="<?php echo trim($value);?>">View File</a> 
                                        <?php }
                                    }
                                }?>
                                </div>
                            <?php }?>
                            

                            <button class="accordion">View On map</button>
                            <div class="panel">
                              <div class="centers_map" style="height: 400px" id="centers_map_<?php echo $row['id'];?>" data-lat="<?php echo $row['latitude'];?>" data-long="<?php echo $row['longitude'];?>"></div>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
            <?php }?>
            <script type="text/javascript">
                $(".centers_map").each(function(){
                    uluru3 = {lat: $(this).data('lat'), lng: $(this).data('long')};
                    map3 = new google.maps.Map(document.getElementById($(this).attr('id')), {zoom: 16, center: uluru3,mapTypeId: 'satellite'});
                    marker3 = new google.maps.Marker({position: uluru3, map: map3});
                });
            </script>
        </div>
    <?php }else{?>
        <center>No Branch/Center Found<br><br></center>
    <?php }?>
            <script type="text/javascript">
                uluru2 = {lat: 34.359210920164855, lng: 73.473524646604564};
                map2 = new google.maps.Map(document.getElementById('centers_pinmap_add'), {zoom: 16, center: uluru2,mapTypeId: 'satellite'});
                marker2 = new google.maps.Marker({position: uluru2, map: map2, draggable: true});

                document.getElementById("latitude").value = marker2.getPosition().lat();
                document.getElementById("longitude").value = marker2.getPosition().lng();

                // add an event "onDrag"
                google.maps.event.addListener(marker2, 'dragend', function() {
                        document.getElementById("latitude").value = marker2.getPosition().lat();
                        document.getElementById("longitude").value = marker2.getPosition().lng();
                });
            </script>
<?php }?>