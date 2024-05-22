<?php 
$module_name = 'deliverables';
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
    <?php $project_deliverable = $crud->getData("SELECT deliverables.* from deliverables where project_id=".$pid." order by added_date ASC");?>
    <?php if($addFlag){?>
        <div class="module addDeliverableModule tabmodule">
            <div class="module-head">
                <h3>Add New Deliverable <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addDeliverableModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
            </div>
            <div class="module-body">
                <form class="form-horizontal row-fluid ajaxForm" name="addUpdateForm" method="post"  enctype="multipart/form-data" action="ajax_project_deliverables_add.php?pid=<?php echo $pid;?>">
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Deliverable</label>
                        <div class="controls">
                            <input type="text"  name="title" class="span12 tip" required="" value="">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" name="addDeliverableButton" value="Add Deliverable" class="btn btn-primary submitbtn">
                            <input type="hidden" name="addDeliverable" value="addDeliverable">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php }?>
    <div class="actions">
        <?php if($addFlag){?><a href="javascript:;" class="btn btn-primary showModule pull-right" data-target="addDeliverableModule"><i class="icon-plus"></i>Add New Deliverable</a><?php }?>

        <a href="javascript:;" class="btn btn-small refresh">
            <i class="icon-refresh shaded"></i> Refresh Deliverables
        </a>
    </div>
    <?php if($project_deliverable != false && count($project_deliverable)>0){?>
        <ul class="widget widget-usage unstyled">
            <?php foreach($project_deliverable as $row){
                $completed = $row['status'];// / 100) * 100;
                ?>
                <li>
                    <p <?php if($completed==100){?>style="margin: 0;"<?php }?>>
                        <a href="ajax_project_deliverables_view.php?viewDeliverable=<?php echo htmlentities($row['id'])?>" class="ajaxEdit" title="View Deliverable" <?php if($completed==100){?>style="color:#307312"<?php }?>>
                            <?php if($completed==100){?><i class="icon-check" style="margin-right: 8px; "></i><?php }?>
                            <strong><?php echo $row['title']?></strong>
                            <small title="Overall Weight">(<i class="icon-plus-sign"></i><?php echo $row['weight']?>%)</small>
                        </a>
                        <?php if($completed<100){?><span class="pull-right small muted"><?php echo $completed?>%</span><?php }?>
                    </p>
                    <?php if($completed<100){?>
                        <div class="progress tight"><div class="bar" style="width: <?php echo $completed?>%;"></div></div>
                    <?php }?>
                </li>                
            <?php }?>
        </ul>
        <!--/.stream-list-->
    <?php }else{?>
        <center>No Deliverable Found<br><br></center>
    <?php }?>
<?php }?>