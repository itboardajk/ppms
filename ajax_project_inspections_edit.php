<?php 
$module_name = 'inspections';
include('classes/config.php');
authenticate_ajax();



$editFlag=authorizeAccess($module_name,'edit');

if($editFlag && isset($_GET['editInspection']) && $_GET['editInspection']>0){

    $inspection_id=intval($crud->escape_string($_GET['editInspection']));
    $editInspection_details=$crud->getData("select * from inspections where id = $inspection_id");
    $editInspection_details = $editInspection_details[0];
    ?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3>Edit Inspection <span style="float:right"><a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <form class="form-horizontal row-fluid ajaxForm" name="editInspectionsForm" method="post"  enctype="multipart/form-data" action="ajax_project_inspections_update.php?updateInspection=<?php echo $inspection_id?>">
                <div class="control-group">
                    <label class="control-label" for="basicinput">Inspection Team</label>
                    <div class="controls">
                        <input type="text"  name="team" class="span12 tip" value="<?php echo $editInspection_details['team']?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Status</label>
                    <div class="controls">
                        <select name="status" class="span8 tip">
                            <option value="Inprocess" <?php if($editInspection_details['team']=='Inprocess'){echo 'selected="selected"';} ?>>Inprocess</option>
                            <option value="Completed" <?php if($editInspection_details['team']=='Completed'){echo 'selected="selected"';} ?>>Completed</option>
                            <option value="Not Started Yet" <?php if($editInspection_details['team']=='Not Started Yet'){echo 'selected="selected"';} ?>>Not Started Yet</option>                                                                
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Details</label>
                    <div class="controls">
                        <textarea  name="details" class="span12 tip" required=""><?php echo $editInspection_details['details'];?></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="basicinput">Images<br><?php if(!empty($editInspection_details['images'])){?><small>Check to remove the image/file.</small><?php }?></label>
                    <div class="controls">
                        <?php 
                            if(!empty($editInspection_details['images'])){
                                $vimg = explode(',', $editInspection_details['images']);
                                foreach ($vimg as $key => $value) {

                                    echo '<label class="checkbox inline"><input type="checkbox" name="rem_img[]"  value="'.htmlentities($value).'" title="Check to remove the image/file."> <a href="'.htmlentities($value).'" target="_blank">View Image/File</a> </label>';
                                }
                            }
                        ?>                                                            
                        <div class="fileswrapper" style="margin: 20px 0;">
                            <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                        </div>
                        <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                        <input  name="prev_images" type="hidden"  value="<?php echo $editInspection_details['images'];?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" name="editInspectionsButton" value="Update Inspection" class="btn btn-primary submitbtn">
                        <input type="hidden" name="editInspections" value="editInspections">
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }