<?php 
$module_name = 'updates';
include('classes/config.php');
authenticate_ajax();



$editFlag=authorizeAccess($module_name,'edit');

if($editFlag && isset($_GET['editUpdate']) && $_GET['editUpdate']>0){

    $update_id=intval($crud->escape_string($_GET['editUpdate']));
    $editUpdate_details=$crud->getData("select * from updates where id = $update_id");
    $editUpdate_details = $editUpdate_details[0];
    ?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3>Edit Update <span style="float:right"><a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <form class="form-horizontal row-fluid ajaxForm" name="editUpdatesForm" method="post"  enctype="multipart/form-data" action="ajax_project_updates_update.php?updateUpdate=<?php echo $update_id?>">
                <div class="control-group">
                    <label class="control-label" for="basicinput">Update</label>
                    <div class="controls">
                        <textarea  name="details" class="span12 tip" required=""><?php echo $editUpdate_details['details'];?></textarea>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="basicinput">Images<br><?php if(!empty($editUpdate_details['images'])){?><small>Check to remove the image/file.</small><?php }?></label>
                    <div class="controls">
                        <?php 
                            if(!empty($editUpdate_details['images'])){
                                $vimg = explode(',', $editUpdate_details['images']);
                                foreach ($vimg as $key => $value) {

                                    echo '<label class="checkbox inline"><input type="checkbox" name="rem_img[]"  value="'.htmlentities($value).'" title="Check to remove the image/file."> <a href="'.htmlentities($value).'" target="_blank">View Image/File</a> </label>';
                                }
                            }
                        ?>                                                            
                        <div class="fileswrapper" style="margin: 20px 0;">
                            <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                        </div>
                        <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                        <input  name="prev_images" type="hidden"  value="<?php echo $editUpdate_details['images'];?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" name="editUpdatesButton" value="Update" class="btn btn-primary submitbtn">
                        <input type="hidden" name="editUpdates" value="editUpdates">
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }