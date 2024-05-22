<?php 
$module_name = 'apo';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));

$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');

$heads_sum = $crud->getData("SELECT SUM(cost) as used from heads where project_id = $pid");

$query="select *  from projects   where id=".$pid;
$project = $crud->getData($query);
$project=$project[0];

if($viewFlag){
	$apos_details=$crud->getData("SELECT * from apo where project_id = $pid order by id DESC");
	?>
    <?php if($addFlag){?>
        <div class="module addAPOModule tabmodule">
            <div class="module-head">
                <h3>Add APO <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addAPOModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
            </div>
            <div class="module-body">
                <?php if($heads_sum[0]['used']==$project["budget"]){?>
                    <form class="form-horizontal row-fluid ajaxForm" name="addAPOForm" method="post"  enctype="multipart/form-data" action="ajax_project_apo_add.php?pid=<?php echo $pid;?>">
                        <div class="control-group">
                            <label class="control-label" for="basicinput">APO</label>
                            <div class="controls">
                                <select name="apo"><?php
                                    $year = date("Y",strtotime("-10 years"));
                                    $year_short = date("y",strtotime("-9 years"));
                                    for($x=0;$x<10;$x++)
                                    {
                                        $year++;
                                        $year_short++;
                                        echo '<option value="'.$year.'-'.$year_short.'">'.$year.'-'.$year_short.'</option>';
                                    }
                                ?></select>
                            </div>
                        </div>
                        <div class="control-group">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped  display" width="100%" style="margin: 20px 0">
                                <thead>
                                    <tr>
                                        <th colspan="5">PC-1 Project Inputs</th>
                                        <?php if(count($apos_details)>0){?><th colspan="3">Progress up/to 6/<?php 

                                        @$till=end(explode('-',$apos_details[0]['apo']));
                                        echo $till?></th><?php }?>
                                        <th colspan="2">APO Plan Inputs</th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Head</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th>Cost(m)</th>
                                        <?php if(count($apos_details)>0){?>
                                            <th>Qty</th>
                                            <th>Exp.</th>
                                            <th>Status</th>
                                        <?php }?>
                                        <th>Qty</th>
                                        <th>Allocation</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php 
                                    //$query=$crud->getData("select h.*,apo.id as apo_id, apo.apo, apo.allocation, apo.prog_qty, apo.prog_expences, apo.prog_status, apo.quantity, apo.revised  from heads h left join apo on h.id=apo.head_id  where h.project_id = $pid  and h.parent_head=0");
                                    $query=$crud->getData("select * from heads  where project_id = $pid  and parent_head=0 order by sort_order asc,id asc");

                                    $cnt=1;
                                    foreach($query as $row)
                                    {
                                    ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt);?></td>
                                            <td colspan="9"><?php echo htmlentities($row['head']);?></td>
                                            
                                        </tr>    
                                        <?php  $subquery=$crud->getData("select * from heads where parent_head = ".$row['id']." order by sort_order asc,id asc");
                                        $subcnt=1;
                                        foreach($subquery as $subrow)
                                        {
                                        ?>                              
                                        <tr>
                                            <td><?php echo htmlentities($cnt).'.'.htmlentities($subcnt);?><input type="hidden" name="subheads[]" value="<?php echo $subrow['id']?>"></td>
                                            <td><?php echo htmlentities($subrow['head']);?></td>
                                            <td><?php echo htmlentities($subrow['unit']);?></td>
                                            <td class="allocated_qty"><?php echo htmlentities($subrow['quantity']);?></td>
                                            <td class="allocated_price"><?php echo htmlentities($subrow['cost']+0);?></td>

                                            <?php if(count($apos_details)>0){?>
                                                <td><input type="number" name="prog_qty[<?php echo $subrow['id']?>]" style="width:90%;min-width:50px" min="0" max="<?php echo $subrow['quantity'];?>"></td>
                                                <td><input type="number" name="prog_expences[<?php echo $subrow['id']?>]" style="width:90%;min-width:100px"  min="0" max="<?php echo $subrow['cost'];?>"  step="any"></td>
                                                <td><input type="text" name="prog_status[<?php echo $subrow['id']?>]" style="width:90%;min-width:50px"></td>
                                            <?php }?>

                                            <td><input type="number" name="apo_qty[<?php echo $subrow['id']?>]" style="width:90%;min-width:50px" min="0" max="<?php echo $subrow['quantity'];?>" class="calcQty"></td>
                                            <td><input type="number" name="apo_allocation[<?php echo $subrow['id']?>]" style="width:90%;min-width:100px" min="0" max="<?php echo $subrow['cost']+0;?>" class="calcPrice"  step="any"></td>
                                        </tr> 
                                        <?php $subcnt++;}?>  
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Allocation</label>
                            <div class="controls">
                                <input type="number" name="allocation" class="tip total_alloc" readonly="" required="" value="" min="0" max="<?php echo $project["budget"]?>" step="any">
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <input type="submit" name="addAPOButton" value="Add APO" class="btn btn-primary submitbtn">
                                <input type="hidden" name="addAPO" value="addAPO">
                            </div>
                        </div>
                    </form>
                <?php }else{?>
                    <div>Your still have <b>Rs.<?php echo number_format($project["budget"] - $heads_sum[0]['used'],6)?></b> for headwise allocation. Please allocate whole amount before creating APO.</div>
                <?php }?>
            </div>
        </div>
    <?php }?>
    <div class="actions">
        <?php if($addFlag){?><a href="javascript:;" class="btn btn-primary showModule pull-right" data-target="addAPOModule"><i class="icon-plus"></i>Add New APO</a><?php }?>

        <a href="javascript:;" class="btn btn-small refresh">
            <i class="icon-refresh shaded"></i> Refresh APOs
        </a>
    </div>
    <?php if($apos_details != false && count($apos_details)>0){?>
        <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped  display" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>APO</th>
                    <th>Status & Authorities</th>
                    <th>Allocation(m)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <?php 
                
                $cnt=1;
                foreach($apos_details as $row)
                {
                    $auth_details=$crud->getData("SELECT * from authorities where type='APO' and ref_id = ".$row['id']." order by sort_order ASC");
                ?>
                    <tr>
                        <td><?php echo htmlentities($cnt);?></td>
                        <td><a href="print_apo.php?apo_id=<?php echo $row['id'] ?>" target="_blank"><?php echo htmlentities($row['apo']);?></a></td>
                        <td><?php 
                            foreach($auth_details as $auth)
                            {
                                $class='';
                                if($auth['status']==1)
                                    $class='alert-success';
                                else if($auth['status']==2)
                                    $class='alert-error';
                                echo '<div class="small alert '.$class.'"><strong>'.$auth['label'].':</strong> ';
                                echo ''.$auth['designation'].'';
                                echo '('.$auth['name'].')';
                                echo '<span class="pull-right">';
                                if(!empty($auth['signed_date']))
                                    echo '<small>'.date("d M, Y", strtotime($auth['signed_date'])).'</small>';                                    
                                echo '</span></div>';
                            }           
                        ?></td>
                        <td><?php echo $row['allocation']+0;?></td>
                        <td>
                            <?php if($editFlag && $row['level']==0){?><a href="ajax_project_apo_edit.php?editAPO=<?php echo htmlentities($row['id'])?>" class="ajaxEdit" title="Edit"><i class="icon-edit"></i></a><?php }?>
                             <a href="print_apo.php?apo_id=<?php echo $row['id'] ?>&action=print" target="_blank" title="Print"><i class="icon-print"></i></a> 
                             <a href="print_apo.php?apo_id=<?php echo $row['id'] ?>&action=pdf" target="_blank" title="PDF"><i class="icon-book"></i></a> 
                            <?php if(!empty($row['file'])){?><a href="<?php echo htmlentities($row['file'])?>" class="Download" title="Download"><i class="icon-download"></i></a><?php }?>
                            <?php if($deleteFlag  && $row['level']==0){?><a href="ajax_project_apo_delete.php?deleteAPO=<?php echo htmlentities($row['id'])?>" class="ajaxDelete" title="Delete"><i class="icon-remove-sign"></i></a><?php }?>
                        </td>
                    </tr>    
                <?php $cnt++; } ?>
            </tbody>
        </table>
    <?php }else{?>
        <center>No APO Found<br><br></center>
    <?php }?>
<?php }?>