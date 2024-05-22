<?php 
$module_name = 'updates';
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
    <?php $project_updates = $crud->getData("SELECT updates.*,admin.display_name,admin.admin_image  from updates left join admin on updates.added_by=admin.id where project_id=".$pid." order by added_date DESC");?>
    <?php if($addFlag){?>
        <div class="module addUpdateModule tabmodule">
            <div class="module-head">
                <h3>Add Update <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addUpdateModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
            </div>
            <div class="module-body">
                <form class="form-horizontal row-fluid ajaxForm" name="addUpdateForm" method="post"  enctype="multipart/form-data" action="ajax_project_updates_add.php?pid=<?php echo $pid;?>">
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Update</label>
                        <div class="controls">
                            <textarea  name="details" class="span12 tip" required=""></textarea>
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
                            <input type="submit" name="addUpdateButton" value="Add Update" class="btn btn-primary submitbtn">
                            <input type="hidden" name="addUpdate" value="addUpdate">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php }?>
    <div class="actions">
        <?php if($addFlag){?><a href="javascript:;" class="btn btn-primary showModule pull-right" data-target="addUpdateModule"><i class="icon-plus"></i>Add New Update</a><?php }?>

        <a href="javascript:;" class="btn btn-small refresh">
            <i class="icon-refresh shaded"></i> Refresh Updates
        </a>
    </div>
    <?php if($project_updates != false && count($project_updates)>0){?>
        <div class="stream-list">
            <?php foreach($project_updates as $row){
                $user_image=$row['admin_image'];
                if(empty($user_image)){
                     $user_image='images/user.png';
                }

                $update_images = explode(',',$row['images']);
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
                                    <?php if($editFlag){?><a class="ajaxEdit" href="ajax_project_updates_edit.php?editUpdate=<?php echo $row['id']?>"><i class="icon-edit"></i></a><?php }?>
                                    <?php if($deleteFlag){?><a class="ajaxDelete" href="ajax_project_updates_delete.php?deleteUpdate=<?php echo $row['id']?>"><i class="icon-remove-sign"></i></a><?php }?>
                                </span>
                            </h5>
                            <div class="stream-text"><?php echo $row['details'];?></div>
                            
                            <?php 
                            if(count($update_images)>0){
                                foreach ($update_images as $key => $value) {
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
        <center>No Update Found<br><br></center>
    <?php }?>
<?php }?>