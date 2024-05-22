<?php 
$module_name = 'inspections';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');
?>
<?php if($viewFlag){?>
    <?php $inspections_details = $crud->getData("SELECT inspections.*,admin.display_name,admin.admin_image  from inspections left join admin on inspections.added_by=admin.id where project_id=".$pid." order by added_date DESC");?>
    <?php if($addFlag){?>
        <div class="module addInspectionModule tabmodule">
            <div class="module-head">
                <h3>Add Inspection <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addInspectionModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
            </div>
            <div class="module-body">
                <form class="form-horizontal row-fluid ajaxForm" name="addUpdateForm" method="post"  enctype="multipart/form-data" action="ajax_project_inspections_add.php?pid=<?php echo $pid;?>">
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Inspection Team</label>
                        <div class="controls">
                            <input type="text"  name="team" class="span12 tip" value="<?php echo @$_POST['team']?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Status</label>
                        <div class="controls">
                            <select name="status" class="span8 tip">
                                <option value="Inprocess">Inprocess</option>
                                <option value="Completed">Completed</option>
                                <option value="Not Started Yet">Not Started Yet</option>                                                                
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Details</label>
                        <div class="controls">
                            <textarea  name="details" class="span12 tip"><?php echo @$_POST['details']?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="basicinput">Images</label>
                        <div class="controls">
                            <div class="fileswrapper" style="margin: 0 0 20px 0;">
                                <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                            </div>
                            <a href="javascript:;" class="addmorefile"  data-types=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">+ Add Another File</a>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" name="addInspectionButton" value="Add Inspection" class="btn btn-primary submitbtn">
                            <input type="hidden" name="addInspection" value="addInspection">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php }?>
    <div class="actions">
        <?php if($addFlag){?><a href="javascript:;" class="btn btn-primary showModule pull-right" data-target="addInspectionModule"><i class="icon-plus"></i>Add New Inspection</a><?php }?>

        <a href="javascript:;" class="btn btn-small refresh">
            <i class="icon-refresh shaded"></i> Refresh Inspections
        </a>
    </div>
    <?php if($inspections_details != false && count($inspections_details)>0){?>
        <div class="stream-list">
            <?php foreach($inspections_details as $row){
                $user_image=$row['admin_image'];
                if(empty($user_image)){
                     $user_image='images/user.png';
                }

                $inspection_images = explode(',',$row['images']);
                ?>
                <div class="media stream">
                    <a href="#" class="media-avatar medium pull-left">
                        <img src="<?php echo $user_image;?>">
                    </a>
                    <div class="media-body">
                        <div class="stream-headline">
                            <h5 class="stream-author">
                                <?php echo $row['display_name'];?> <small><b class="feed-date"  data-toggle="tooltip" title="<?php echo $row["added_date"];?>"><?php echo date("d M, Y", strtotime($row["added_date"]));?></b></small>
                                <span class="pull-right">
                                    <?php if($editFlag){?><a class="ajaxEdit" href="ajax_project_inspections_edit.php?editInspection=<?php echo $row['id']?>"><i class="icon-edit"></i></a><?php }?>
                                    <?php if($deleteFlag){?><a class="ajaxDelete" href="ajax_project_inspections_delete.php?deleteInspection=<?php echo $row['id']?>"><i class="icon-remove-sign"></i></a><?php }?>
                                </span>
                            </h5>
                            <div class="stream-text"><b>Inspection Team: <?php echo $row['team'];?></b><span style="margin-left:20px" class="btn btn-small btn-inverse"> <?php echo $row['status'];?> </span></div>
                            <div class="stream-text"><?php echo $row['details'];?></div>
                            
                            <?php 
                            if(count($inspection_images)>0){
                                foreach ($inspection_images as $key => $value) {
                                    if(!empty(trim($value))){
                                        $ext = strtolower($value);
                                        if(endsWith($ext,'.jpg') || endsWith($ext,'.jpeg')  || endsWith($ext,'.png')  || endsWith($ext,'.gif') ||  endsWith($ext,'.jfif')){                                                                            ?>
                                            <div class="stream-attachment photo">
                                                <div class="responsive-photo">
                                                    <a href="<?php echo $value;?>" target="_blank"><img src="<?php echo $value;?>" alt=""></a>
                                                </div>
                                            </div>
                                        <?php  } else if(endsWith($ext,'.mp4') || endsWith($ext,'.mpeg') || endsWith($ext,'.webm') || endsWith($ext,'.avi') || endsWith($ext,'.flv') || endsWith($ext,'.wmv')){?>
                                            <div class="stream-attachment video">
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
                                }
                            }?>
                        </div>
                    </div>
                </div>
            <?php }?>
        </div>
        <!--/.stream-list-->
    <?php }else{?>
        <center>No Inspection Found<br><br></center>
    <?php }?>
<?php }?>