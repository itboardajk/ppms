<?php 
$module_name = 'deliverables';
include('classes/config.php');
authenticate_ajax();



$viewFlag=authorizeAccess($module_name,'view');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');

if($viewFlag && isset($_GET['viewDeliverable']) && $_GET['viewDeliverable']>0){

    $deliverable_id=intval($crud->escape_string($_GET['viewDeliverable']));
    $editUpdate_details=$crud->getData("select * from deliverables where id = $deliverable_id");
    $editUpdate_details = $editUpdate_details[0];

    ?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3><?php echo $editUpdate_details['title'];?> <span style="float:right"><a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <?php echo $editUpdate_details['details'];?><br>
            <?php 
                if(!empty($editUpdate_details['images'])){
                    echo '<b>Attavhments:</b> ';
                    $vimg = explode(',', $editUpdate_details['images']);
                    $count=1;
                    foreach ($vimg as $key => $value) {
                        if($count>1)
                            echo ' | ';
                        echo '<a href="'.htmlentities($value).'" target="_blank">View Image/File</a>';
                        $count++;
                    }
                }
            ?>
            <br>
            <ul class="widget widget-usage unstyled">
                <li>
                    <p><strong>Weight</strong><span class="pull-right small muted"><?php echo $editUpdate_details['weight'];?>%</span></p>
                    <div class="progress tight"><div class="bar" style="width: <?php echo $editUpdate_details['weight'];?>%;"></div></div>
                </li>                
                <li>
                    <p><strong>Status</strong><span class="pull-right small muted"><?php echo $editUpdate_details['status'];?>%</span></p>
                    <div class="progress tight"><div class="bar" style="width: <?php echo $editUpdate_details['status'];?>%;"></div></div>
                </li>                

            </ul>
            <br>


            <?php if($editFlag || $deleteFlag){?>
                <?php if($editFlag){?><a href="ajax_project_deliverables_edit.php?editDeliverable=<?php echo htmlentities($editUpdate_details['id'])?>" class="ajaxEdit btn btn-primary" title="Edit"><i class="icon-edit"></i> Edit</a><?php }?>
                <?php if($deleteFlag){?><a href="ajax_project_deliverables_delete.php?deleteDeliverable=<?php echo htmlentities($editUpdate_details['id'])?>" class="ajaxDelete btn btn-danger" title="Delete"><i class="icon-remove-sign"></i> Delete</a><?php }?>

            <?php }?>
        </div>
    </div>
<?php }