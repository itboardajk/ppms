<?php 
$module_name = 'APO';
include('classes/config.php');
authenticate_ajax();



$editFlag=authorizeAccess($module_name,'edit');

if($editFlag && isset($_GET['editAPO']) && $_GET['editAPO']>0){

    $apo_id=intval($crud->escape_string($_GET['editAPO']));

    $editapos_details=$crud->getData("select * from apo where id = $apo_id");
    $editapos_details = $editapos_details[0];


    $pid = $editapos_details['project_id'];

    $query="select *  from projects   where id=".$pid;
    $project = $crud->getData($query);
    $project=$project[0];
    ?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3>Edit APO <span style="float:right"><a href="#authorites"><i class="icon-user"></i> Manage Authorities</a> | <a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <form class="form-horizontal row-fluid ajaxForm" name="editAPOForm" method="post"  enctype="multipart/form-data" action="ajax_project_apo_update.php?updateAPO=<?php echo $apo_id?>">
              
                <div class="control-group">
                    <label class="control-label" for="basicinput">APO</label>
                    <div class="controls">
                        <input type="text" name="apo" class="span8 tip" required="" value="<?php echo $editapos_details['apo']?>" placeholder="2020">
                    </div>
                </div>
                <div class="control-group">
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped  display" width="100%" style="margin: 20px 0">
                        <thead>
                            <tr>
                                <th colspan="5">PC-1 Project Inputs</th>
                                <?php if($editapos_details['progress']==1){?><th colspan="3">Progress up/to 6/<?php echo $editapos_details['apo'];?></th><?php }?>
                                <th colspan="2">APO Plan Inputs</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Head</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>Cost</th>
                                <?php if($editapos_details['progress']==1){?>
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
                                <?php  $subquery=$crud->getData("select heads.*,apo_heads.prog_qty,apo_heads.prog_expences,apo_heads.prog_status,apo_heads.quantity as apo_qty,apo_heads.revised as apo_revised from heads left join apo_heads on heads.id=apo_heads.head_id where  heads.project_id = $pid  and heads.parent_head = ".$row['id']." and apo_heads.apo_id=".$editapos_details['id']." order by heads.sort_order asc,heads.id asc");
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

                                    <?php if($editapos_details['progress']==1){?>
                                        <td><input type="number" name="prog_qty[<?php echo $subrow['id']?>]" style="width:50px" min="0" max="<?php echo $subrow['quantity'];?>" value="<?php echo $subrow['prog_qty']?>"></td>
                                        <td><input type="number" name="prog_expences[<?php echo $subrow['id']?>]" style="width:100px"  min="0" max="<?php echo $subrow['cost'];?>"  value="<?php echo $subrow['prog_expences']+0?>" step="any"></td>
                                        <td><input type="text" name="prog_status[<?php echo $subrow['id']?>]" style="width:50px"  value="<?php echo $subrow['prog_status']?>"></td>
                                    <?php }?>

                                    <td><input type="number" name="apo_qty[<?php echo $subrow['id']?>]" style="width:50px" min="0" max="<?php echo $subrow['quantity'];?>"  value="<?php echo $subrow['apo_qty']?>" class="calcQty"></td>
                                    <td><input type="number" name="apo_allocation[<?php echo $subrow['id']?>]" style="width:100px" min="0" max="<?php echo $subrow['cost'];?>"  value="<?php echo $subrow['apo_revised']+0?>" class="calcPrice"  step="any"></td>
                                </tr> 
                                <?php $subcnt++;}?>  
                            <?php $cnt++; } ?>
                        </tbody>
                    </table>
                </div>   
                <div class="control-group">
                    <label class="control-label" for="basicinput">Allocation</label>
                    <div class="controls">
                        <input type="number" name="allocation" class="total_alloc tip" required="" value="<?php echo $editapos_details['allocation']+0?>" min="0" max="<?php echo $project["budget"]?>"  step="any" readonly="" >
                    </div>
                </div>   
                <div class="control-group">
                    <label class="control-label" for="basicinput">Upload Signed APO</label>
                    <div class="controls">
                        <input  name="filesToUpload" type="file"  accept="application/msword,application/pdf"> <?php if(!empty($editapos_details['file'])){?><a  href="<?php echo $editapos_details['file']?>">Downlaod APO</a><?php }?>
                            <input type="hidden" name="prev_fimage" value="<?php echo $editapos_details['file'];?>">

                    </div>
                </div>             
                <div class="control-group" id="authorites">
                    <label class="control-label" for="basicinput">Authorities<br><a href="javascript:;" onclick="newAuth();"><small>Add New</small></a></label>
                    <div class="controls">
                        <div class="sort_authorities">
                            <?php 

                            $auth_details=$crud->getData("SELECT * from authorities where type='APO' and ref_id = ".$apo_id." order by sort_order ASC");

                            $query="select * from departments order by title ASC";
                            $departments = $crud->getData($query);
                            $departments_tree=parseTree($departments);

                            $first_authority=0;
                            if($auth_details != false && count($auth_details)>0)
                            {
                                foreach($auth_details as $auth)
                                {

                                    $dep_users=$crud->getData("select admin.*,roles.title as roleName,roles.parent_id from admin left join roles on admin.role=roles.id where admin.department_id=".$auth['dep_id']." order by roles.sort_order ASC, roles.title ASC");
                                    
                                    if($dep_users != false && count($dep_users)>0)
                                    {
                                        $dep_users_tree=parseTreeAdmin($dep_users);
                                    }

                                    if($first_authority == 0)
                                    {
                                        $first_authority = $auth['admin_id'];
                                    }
                                    ?>

                                    <div class="autority row" style="margin-left: 0;margin-top: 20px;">
                                        <div class="span1" style="text-align: center;">Delete:<br><input type="checkbox" name="auth_del[]" value="<?php echo $auth['id']?>"></div>
                                        <div class="span3"><input type="hidden" name="auth_id[]" value="<?php echo $auth['id']?>">
                                            Label:<br><input type="text" name="label[]" class="span12" value="<?php echo $auth['label'];?>">
                                        </div>
                                        <div class="span4">
                                            Department:<br><select name="auth_dep[]" class="span12 auth_dep"><option value="">Select Department</option>
                                                <?php 
                                                
                                                if($departments != false && count($departments)>0)
                                                {
                                                    printTree($departments_tree,$auth['dep_id']);
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="span4">
                                            User:<br><select name="auth_user[]" class="span12 auth_user"><option value="">Select User</option>
                                                <?php 
                                                if($dep_users_tree != false && count($dep_users_tree)>0)
                                                {
                                                    printTreeAdmin($dep_users_tree,$auth['admin_id']);
                                                }

                                                ?></select> 
                                        </div>
                                    </div>
                                    <?php 
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" name="editAPOButton" value="Update APO" class="btn btn-primary submitbtn">
                        <?php 
                            if(count($auth_details)>1){ 
                                if($first_authority == $_SESSION['id']){
                                    if(isset($_SESSION['asign']) && !empty($_SESSION['asign']) && file_exists($_SESSION['asign'])){
                                        echo '<input type="button" name="initiatAPOButton" value="Sign & Move Forward" class="btn btn-success otherAjax" data-url="ajax_project_apo_update.php?updateAPO='.$apo_id.'&initiateAPO=1" data-reloadtab="true">';
                                    }
                                    else{
                                        echo '<div class="alert alert-error" style="display: inline-block;margin-left: 20px;"><a href="profile.php" >Upload your Signature</a> to initiate the APO. Make sure to save your changes.</div>';
                                    }
                                }
                                else{
                                    echo '<div class="alert alert-error" style="display: inline-block;margin-left: 20px;">Only first authority can initiate the APO.</div>';
                                }
                            }
                            else{
                                echo '<div class="alert alert-error" style="display: inline-block;margin-left: 20px;">There should be 2 or more authorties to initiate the APO.</div>';
                            }
                        ?>
                        <input type="hidden" name="editAPO" value="editAPO">
                    </div>
                </div>
            </form>
            <div class="auth_form" style="display: none">
                <div class="autority row" style="margin-left: 0;margin-top: 20px;">
                    <div class="span1" style="text-align: center;"></div>
                    <div class="span3">Label:<br><input type="text" name="label[]" class="span12"><input type="hidden" name="auth_id[]" value=""></div>
                    <div class="span4">
                        Department:<br><select name="auth_dep[]" class="span12 auth_dep"><option value="">Select Department</option>
                            <?php 
                            $query="select * from departments order by title ASC";
                            $nodes = $crud->getData($query);
                            if($nodes != false && count($nodes)>0)
                            {
                                $tree=parseTree($nodes);
                                printTree($tree);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="span4">
                        User:<br><select name="auth_user[]" class="span12 auth_user"><option value="">Select User</option><option value="">Select department to load users</option></select> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $( ".sort_authorities" ).sortable({
             //appendTo: document.body      
             placeholder: "ui-state-highlight"
        });
        $( ".sort_authorities" ).disableSelection();

        function newAuth(){
            $( ".sort_authorities" ).append($('.auth_form').html());
        }
    </script>
<?php }