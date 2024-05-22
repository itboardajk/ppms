<?php 
$module_name = 'heads';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));

$query="select *  from projects   where id=".$pid;
$project = $crud->getData($query);
$project=$project[0];

$heads_sum = $crud->getData("SELECT SUM(cost) as used from heads where project_id = $pid");
$apos_details=$crud->getData("SELECT * from apo where project_id = $pid order by id DESC");

$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');
?>
<?php if($viewFlag){?>
    <?php $heads_details=$crud->getData("SELECT * from heads where project_id = $pid  and parent_head=0 order by sort_order asc, id ASC");?>
    <?php if($addFlag){?>
        <div class="module addHeadModule tabmodule">
            <div class="module-head">
                <h3>Add Head <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addHeadModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
            </div>
            <div class="module-body">
                <form class="form-horizontal row-fluid ajaxForm" name="addHeadForm" method="post"  enctype="multipart/form-data" action="ajax_project_heads_add.php?pid=<?php echo $pid;?>">
                    
                    <?php if($project["budget"]>0 && $heads_sum[0]['used']<$project["budget"]){?>
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Head</label>
                            <div class="controls">
                                <input type="text" name="head" class="span8 tip" required="" value="">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Parent</label>
                            <div class="controls">
                                <select name="parent_head" class="headchange">
                                    <option value="0">Main Head(No Parent)</option>

                                    <?php $query=$crud->getData("select * from heads where project_id = {$pid} and parent_head=0");
                                    foreach($query as $row){?>
                                        <option value="<?php echo $row['id']?>"><?php echo $row['head']?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group h_with_root">
                            <label class="control-label" for="basicinput">Code</label>
                            <div class="controls">
                                <input type="text" name="code" id="code" class="span8 tip" value="">
                            </div>
                        </div>
                        <div class="control-group h_with_child" style="display: none">
                            <label class="control-label" for="basicinput">Unit</label>
                            <div class="controls">
                                <input type="text" name="unit" class="span8 tip" value="">
                            </div>
                        </div>
                        <div class="control-group h_with_child" style="display: none">
                            <label class="control-label" for="basicinput">Quantity</label>
                            <div class="controls">
                                <input type="number" name="quantity" class="span8 tip"  value="">
                            </div>
                        </div>
                        <div class="control-group h_with_child" style="display: none">
                            <label class="control-label" for="basicinput">Cost</label>
                            <div class="controls">
                                <?php //echo $project["budget"] .'-'. $heads_sum[0]['used'];?>
                                <input type="number" name="cost" class="span8 tip"  value=""  min="0" max="<?php echo $project["budget"] - $heads_sum[0]['used']; ?>" step="any">
                                <span class="help-inline">Maximum: <?php echo $project["budget"] - $heads_sum[0]['used']; ?> </span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Order</label>
                            <div class="controls">
                                <input type="number" name="sort_order" class="span8 tip" value="1">
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <input type="submit" name="addHeadButton" value="Add Head" class="btn btn-primary submitbtn">
                                <input type="hidden" name="addHead" value="addHead">
                            </div>
                        </div>
                    <?php }else if($project["budget"]==0 ){?>
                        <center>Please set your projects' budget first.</center>
                    <?php }else{?>
                        <center>You have allocated whole amount.</center>
                    <?php }?>
                </form>
            </div>
        </div>
    <?php }?>
    <div class="actions">
        <?php if($addFlag){?><a href="javascript:;" class="btn btn-primary showModule pull-right" data-target="addHeadModule"><i class="icon-plus"></i>Add New Head</a><?php }?>

        <a href="javascript:;" class="btn btn-small refresh">
            <i class="icon-refresh shaded"></i> Refresh Heads
        </a>
    </div>
    <?php if($heads_details != false && count($heads_details)>0){?>
        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>code</th>
                    <th>Budget Input Description</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th>Cost(m)</th>
                    <?php if(count($apos_details)<1){?><th>Action</th><?php }?>
                </tr>
            </thead>
            <tbody>
                <?php 

                $cnt=1;
                $grand_total=0;
                foreach($heads_details as $row)
                {  $subquery=$crud->getData("select * from heads where parent_head = ".$row['id'].'  order by sort_order asc,id asc');
                    ?>
                    <tr>
                        <td><?php echo htmlentities($cnt);?></td>
                        <td rowspan="<?php echo count($subquery)+1 ?>"><?php echo htmlentities($row['code']);?></td>
                         <td colspan="4"  id="ph_head_<?php echo htmlentities($row['id'])?>" style="background: #ddd"><?php echo htmlentities($row['head']);?></td>
                         
                        <?php if(count($apos_details)<1){?>
                            <td>
                                <?php if($editFlag){?><a href="ajax_project_heads_edit.php?editHead=<?php echo htmlentities($row['id'])?>" class="ajaxEdit"><i class="icon-edit"></i></a><?php }?>
                                <?php if($deleteFlag){?><a href="ajax_project_heads_delete.php?deleteHead=<?php echo htmlentities($row['id'])?>" class="ajaxDelete"><i class="icon-remove-sign"></i></a><?php }?>
                            </td>
                        <?php }?>
                    </tr>
                    <?php  
                   
                    $subcnt=1;
                    $sub_total=0;
                    foreach($subquery as $subrow)
                    {
                    ?>                              
                    <tr>
                        <td><?php echo htmlentities($cnt).'.'.htmlentities($subcnt);?></td>
                        <td id="ph_head_<?php echo htmlentities($subrow['id'])?>"><?php echo htmlentities($subrow['head']);?></td>
                        <td id="ph_unit_<?php echo htmlentities($subrow['id'])?>"><?php echo htmlentities($subrow['unit']);?></td>
                        <td id="ph_quantity_<?php echo htmlentities($subrow['id'])?>"><?php echo htmlentities($subrow['quantity']);?></td>
                        <td id="ph_cost_<?php echo htmlentities($subrow['id'])?>"><?php echo htmlentities($subrow['cost']+0);?></td>
                        <?php if(count($apos_details)<1){?>
                            <td>
                                <?php if($editFlag){?><a href="ajax_project_heads_edit.php?editHead=<?php echo htmlentities($subrow['id'])?>" class="ajaxEdit"><i class="icon-edit"></i></a><?php }?>
                                <?php if($deleteFlag){?><a href="ajax_project_heads_delete.php?deleteHead=<?php echo htmlentities($subrow['id'])?>" class="ajaxDelete"><i class="icon-remove-sign"></i></a><?php }?>
                            </td>
                        <?php }?>
                    </tr> 
                    <?php 

                    $sub_total += $subrow['cost'];
                    $subcnt++;}?>  
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Sub Total</td>
                        <td></td>
                        <td></td>
                        <td><?php echo $sub_total; $grand_total += $sub_total?></td>
                        <?php if(count($apos_details)<1){?><td></td><?php }?>
                    </tr>
                <?php $cnt++; } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <th>Grand Total</th>
                        <td></td>
                        <td></td>
                        <td><?php echo $grand_total;?></td>
                        <?php if(count($apos_details)<1){?><td></td><?php }?>
                    </tr>
            </tbody>
        </table>
    <?php }else{?>
        <center>No Head Found<br><br></center>
    <?php }?>
<?php }?>