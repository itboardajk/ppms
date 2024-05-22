<?php 
$module_name = 'heads';
include('classes/config.php');
authenticate_ajax();



$editFlag=authorizeAccess($module_name,'edit');

if($editFlag && isset($_GET['editHead']) && $_GET['editHead']>0){

    $head_id=intval($crud->escape_string($_GET['editHead']));

    $edithead_details=$crud->getData("select * from heads where id = $head_id");
    $edithead_details = $edithead_details[0];


    $pid = $edithead_details['project_id'];
    $heads_sum = $crud->getData("SELECT SUM(cost) as used from heads where project_id = $pid");


    $query="select *  from projects   where id=".$pid;
    $project = $crud->getData($query);
    $project=$project[0];
    ?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3>Edit Head <span style="float:right"><a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <form class="form-horizontal row-fluid ajaxForm" name="editHeadForm" method="post"  enctype="multipart/form-data" action="ajax_project_heads_update.php?updateHead=<?php echo $head_id?>">
                <div class="control-group">
                    <label class="control-label" for="basicinput">Head</label>
                    <div class="controls">
                        <input type="text" name="head" id="head" class="span8 tip" required="" value="<?php echo $edithead_details['head']?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Parent</label>
                    <div class="controls">
                        <select name="parent_head" id="parent_head" class="headchange">
                            <option value="0">Main Head(No Parent)</option>

                            <?php $query=$crud->getData("select * from heads where project_id = {$pid} and parent_head=0 and id <> {$head_id}");
                            foreach($query as $row){?>
                                <option value="<?php echo $row['id']?>" <?php if($edithead_details['parent_head']==$row['id']){ echo 'selected="selected"';}?>><?php echo $row['head']?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="control-group h_with_root" style="<?php if($edithead_details['parent_head']!=0){echo 'display: none';}?>">
                    <label class="control-label" for="basicinput">Code</label>
                    <div class="controls">
                        <input type="text" name="code" id="code" class="span8 tip" value="<?php echo @$edithead_details['code']?>">
                    </div>
                </div>
                <div class="control-group h_with_child" style="<?php if($edithead_details['parent_head']==0){echo 'display: none';}?>">
                    <label class="control-label" for="basicinput">Unit</label>
                    <div class="controls">
                        <input type="text" name="unit" id="unit" class="span8 tip" value="<?php echo @$edithead_details['unit']?>">
                    </div>
                </div>
                <div class="control-group h_with_child" style="<?php if($edithead_details['parent_head']==0){echo 'display: none';}?>">
                    <label class="control-label" for="basicinput">Quantity</label>
                    <div class="controls">
                        <input type="number" name="quantity" id="quantity" class="span8 tip"  value="<?php echo @$edithead_details['quantity']?>">
                    </div>
                </div>
                <div class="control-group h_with_child" style="<?php if($edithead_details['parent_head']==0){echo 'display: none';}?>">
                    <label class="control-label" for="basicinput">Cost</label>
                    <div class="controls">
                        <input type="number" name="cost" id="cost" class="span8 tip"  value="<?php echo $edithead_details['cost']+0?>" min="0" max="<?php echo ($project["budget"] - $heads_sum[0]['used']) + $edithead_details['cost']; ?>" step='any'>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Order</label>
                    <div class="controls">
                        <input type="number" name="sort_order" id="sort_order" class="span8 tip" value="<?php echo @$edithead_details['sort_order']?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" name="editHeadButton" value="Update Head" class="btn btn-primary submitbtn">
                        <input type="hidden" name="editHead" value="editHead">
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }