<?php 
$module_name = 'deliverables';
include('classes/config.php');
authenticate_ajax();



$editFlag=authorizeAccess($module_name,'edit');

if($editFlag && isset($_GET['editDeliverable']) && $_GET['editDeliverable']>0){

    $deliverable_id=intval($crud->escape_string($_GET['editDeliverable']));
    $editDeliverable_details=$crud->getData("select * from deliverables where id = $deliverable_id");
    $editDeliverable_details = $editDeliverable_details[0];

    //echo "select SUM(weight) as total from deliverables where project_id = ".$editDeliverable_details['project_id']." and id<>$deliverable_id";
    $editDeliverable_weight=$crud->getData("select SUM(weight) as total from deliverables where project_id = ".$editDeliverable_details['project_id']." and id<>$deliverable_id");
    $editDeliverable_weight = $editDeliverable_weight[0];

    ?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3>Edit Deliverable <span style="float:right"><a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <form class="form-horizontal row-fluid ajaxForm" name="editDeliverableForm" method="post"  enctype="multipart/form-data" action="ajax_project_deliverables_update.php?updateDeliverable=<?php echo $deliverable_id?>">
                <div class="control-group">
                    <label class="control-label" for="basicinput">Title</label>
                    <div class="controls">
                        <textarea  name="title" class="span12 tip" required=""><?php echo $editDeliverable_details['title'];?></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Weight<br><small>Overall weight percentage of this deliverable.</small></label>
                    <div class="controls">
                        <input type="range" id="weight" name="weight"  class="span10" min="0" max="<?php echo (100 - $editDeliverable_weight['total']);?>" value="<?php echo @$editDeliverable_details['weight']?>" oninput="this.nextElementSibling.value = this.value+'%'"><output><?php echo $editDeliverable_details['weight'];?>%</output> 
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Status<br><small>Completion percentage</small></label>
                    <div class="controls">
                        <input type="range" id="status" name="status" class="span10" min="0" max="100" value="<?php echo @$editDeliverable_details['status']?>" oninput="this.nextElementSibling.value = this.value+'%'"><output><?php echo $editDeliverable_details['status'];?>%</output>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Details</label>
                    <div class="controls">
                        <textarea  name="details" class="span12 tip"><?php echo $editDeliverable_details['details'];?></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="basicinput">Images<br><?php if(!empty($editDeliverable_details['images'])){?><small>Check to remove the image/file.</small><?php }?></label>
                    <div class="controls">
                        <?php 
                            if(!empty($editDeliverable_details['images'])){
                                $vimg = explode(',', $editDeliverable_details['images']);
                                foreach ($vimg as $key => $value) {

                                    echo '<label class="checkbox inline"><input type="checkbox" name="rem_img[]"   value="'.htmlentities($value).'" title="Check to remove the image/file."> <a href="'.htmlentities($value).'" target="_blank">View Image/File</a> </label>';
                                }
                            }
                        ?>                                                            
                        <div class="fileswrapper" style="margin: 20px 0;">
                            <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                        </div>
                        <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                        <input  name="prev_images" type="hidden"  value="<?php echo $editDeliverable_details['images'];?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" name="editDeliverableButton" value="Update Deliverable" class="btn btn-primary submitbtn">
                        <input type="hidden" name="editDeliverable" value="editDeliverable">
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }