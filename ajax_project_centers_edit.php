<?php 
$module_name = 'centers';
include('classes/config.php');
authenticate_ajax();



$editFlag=authorizeAccess($module_name,'edit');

if($editFlag && isset($_GET['editCenter']) && $_GET['editCenter']>0){

    $center_id=intval($crud->escape_string($_GET['editCenter']));
    $editcenter_details=$crud->getData("select * from centers where id = $center_id");
    $editcenter_details = $editcenter_details[0];
    ?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3>Edit APO <span style="float:right"><a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <form class="form-horizontal row-fluid ajaxForm" name="editCentersForm" method="post"  enctype="multipart/form-data" action="ajax_project_centers_update.php?updateCenter=<?php echo $center_id?>">
                <div class="control-group">
                    <label class="control-label" for="basicinput">Title</label>
                    <div class="controls">
                        <input type="text" name="title" class="span12 tip" value="<?php echo $editcenter_details['title']?>" required="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Address</label>
                    <div class="controls">
                        <input type="text" name="address" class="span12 tip" required="" value="<?php echo @$editcenter_details['address']?>">
                    </div>
                </div>       
                <div class="control-group">
                    <label class="control-label" for="basicinput">Focal Person</label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on">Name</span><input name="focal_person" class="span8" type="text" placeholder="Name" value="<?php echo @$editcenter_details['focal_person']?>">       
                        </div>
                        <div class="input-prepend">
                            <span class="add-on">Email</span><input name="focal_person_email" class="span8" type="text" placeholder="Email" value="<?php echo @$editcenter_details['focal_person_email']?>">       
                        </div>
                        <div class="input-prepend">
                            <span class="add-on">Contact</span><input name="focal_person_phone" class="span8" type="text" placeholder="Phone" value="<?php echo @$editcenter_details['focal_person_phone']?>">       
                        </div>
                    </div>
                </div>                                                                                    
                <div class="control-group">
                    <label class="control-label" for="basicinput">Hardware</label>
                    <div class="controls">
                        <div class="input-prepend span3">
                            <span class="add-on">Workstaions</span><input name="workstaions" class="span6" type="number" placeholder="0" value="<?php echo isset($editcenter_details['workstaions'])?$editcenter_details['workstaions']:0; ?>">       
                        </div>
                        <div class="input-prepend span3">
                            <span class="add-on">Laptops</span><input name="laptops" class="span6" type="number" placeholder="0" value="<?php echo isset($editcenter_details['laptops'])?$editcenter_details['laptops']:0; ?>">       
                        </div>
                        <div class="input-prepend span3">
                            <span class="add-on">Printers</span><input name="printers" class="span6" type="number" placeholder="0" value="<?php echo isset($editcenter_details['printers'])?$editcenter_details['printers']:0; ?>">       
                        </div>
                        <div class="input-prepend span3">
                            <span class="add-on">Scanners</span><input name="scanners" class="span6" type="number" placeholder="0" value="<?php echo isset($editcenter_details['scanners'])?$editcenter_details['scanners']:0; ?>">       
                        </div>
                    </div>
                </div>                            
                <div class="control-group">
                    <label class="control-label" for="basicinput">Total Staff</label>
                    <div class="controls">
                        <input type="text" name="total_staff" class="span12 tip" value="<?php echo @$editcenter_details['total_staff']?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Description</label>
                    <div class="controls">
                        <textarea  name="description" class="span12 tip"><?php echo @$editcenter_details['description']?></textarea>
                    </div>
                </div>  
                <div class="control-group Pin">
                    <label class="control-label" for="basicinput">Pin</label>
                    <div class="controls">
                        <div id="centers_pinmap_edit"  style="height: 450px;"></div>
                        <input type="hidden" name="latitude" id="elatitude" value="<?php echo @$editcenter_details['latitude']?>">
                        <input type="hidden" name="longitude" id="elongitude" value="<?php echo @$editcenter_details['longitude']?>">
                        <div style="margin-top:20px">
                            <a class="btn btn-primary openfiledialog" href="javascript:;">Get Location from Image</a>
                            <span class="img_latlong_msg"></span>
                            <span style="display: none"><input type="file" name="readinfo" class="readinfo" data-form="edit"></span>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Featured Image</label>
                    <div class="controls">
                        <?php 
                            if(!empty($editcenter_details['fimage'])){
                                echo '<a href="'.htmlentities($editcenter_details['fimage']).'" target="_blank">View Featured Image</a>';
                            }
                        ?>
                        <div class="fileswrapper" style="margin:0 0 20px 0;">
                            <input  name="fimage" type="file"  accept="image/*">
                            <input type="hidden" name="prev_fimage" value="<?php echo $editcenter_details['fimage'];?>">
                        </div>
                    </div>
                </div>                                                    
                <div class="control-group">
                    <label class="control-label" for="basicinput">Images<br><?php if(!empty($editcenter_details['images'])){?><small>Check to remove the image/file.</small><?php }?></label>
                    <div class="controls">
                        <?php 
                            if(!empty($editcenter_details['images'])){
                                $vimg = explode(',', $editcenter_details['images']);
                                foreach ($vimg as $key => $value) {

                                    echo '<label class="checkbox inline"><input type="checkbox" name="rem_img[]" value="'.htmlentities($value).'" title="Check to remove the image/file."> <a href="'.htmlentities($value).'" target="_blank">View Image/File</a> </label>';
                                }
                            }
                        ?>                                                            
                        <div class="fileswrapper" style="margin: 20px 0;">
                            <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                        </div>
                        <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                        <input  name="prev_images" type="hidden"  value="<?php echo $editcenter_details['images'];?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" name="editCentersButton" value="Update Center" class="btn btn-primary submitbtn">
                        <input type="hidden" name="editCenter" value="editCenter">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
            uluru = {lat: <?php echo $editcenter_details['latitude']?>, lng: <?php echo $editcenter_details['longitude']?>};
            map = new google.maps.Map(document.getElementById('centers_pinmap_edit'), {zoom: 16, center: uluru,mapTypeId: 'satellite'});
            marker = new google.maps.Marker({position: uluru, map: map, draggable: true});

            // add an event "onDrag"
            google.maps.event.addListener(marker, 'dragend', function() {
                document.getElementById("elatitude").value = marker.getPosition().lat();
                document.getElementById("elongitude").value = marker.getPosition().lng();
            });
    </script>
<?php }