<?php 
$module_name = 'expenses';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');

$apos_details=$crud->getData("SELECT * from apo where project_id = $pid order by id DESC");
$heads_details=$crud->getData("SELECT * from heads where project_id = $pid  and parent_head=0 order by sort_order asc, id asc");

?>
<?php if($viewFlag){?>
    <?php $expenses_details = $crud->getData("SELECT expenses.*,heads.head from expenses left join heads on expenses.head_id=heads.id where expenses.project_id=".$pid);?>
    <?php if($addFlag){?>
        <div class="module addExpensesModule tabmodule">
            <div class="module-head">
                <h3>Add Update <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addExpensesModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
            </div>
            <div class="module-body">
                <?php if($apos_details != false && count($apos_details)>0){?>
                    <form class="form-horizontal row-fluid ajaxForm" name="addExpensesForm" method="post"  enctype="multipart/form-data" action="ajax_project_expenses_add.php?pid=<?php echo $pid;?>">
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Title</label>
                            <div class="controls">
                                <input type="text" name="title" class="span12 tip" value="<?php echo @$_POST['title']?>" required="">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Head</label>
                            <div class="controls">
                                <select name="head_id" class="head_expense" required="">
                                    <option value="">Please select</option>
                                    <?php foreach($heads_details as $head){?>
                                        <optgroup label="<?php echo $head['head']?>">
                                            <?php
                                                $quer = "select heads.*,apo_heads.revised as apo_revised from heads left join apo_heads on heads.id=apo_heads.head_id where  heads.project_id = $pid  and heads.parent_head = ".$head['id']." and apo_heads.apo_id=".$apos_details[0]['id']." order by heads.sort_order asc, heads.id asc";  
                                                $subquery=$crud->getData($quer);
                                                $subcnt=1;
                                                foreach($subquery as $subrow)
                                                {?>
                                                    <option value="<?php echo $subrow['id'].':'.($subrow['apo_revised']+0)?>"><?php echo $subrow['head']?></option>
                                                <?php }?>
                                        </optgroup>
                                    <?php }?>
                                </select>
                                <small class="head_allocation"></small>
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Expense(m)</label>
                            <div class="controls">
                                <input type="number" name="cost" class="span12 tip" required="" value="<?php echo @$_POST['cost']?>" step="any">
                            </div>
                        </div>    
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Status</label>
                            <div class="controls">
                                <select name="status" class="expense_status">
                                    <option value="Booked">Booked</option>
                                    <option value="Released" <?php if(@$_POST['status']=='Released'){echo 'selected="selected"';}?>>Released</option>                   
                                </select>
                            </div>
                        </div>                                               
                        <div class="control-group release_date" style="display: none">
                            <label class="control-label" for="basicinput">Release Date</label>
                            <div class="controls">
                                <input type="date" name="release_date" class="span12 tip" value="<?php echo @$_POST['release_date']?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Description</label>
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
                                <input type="submit" name="addExpensesButton" value="Add Expenses" class="btn btn-primary submitbtn">
                                <input type="hidden" name="addExpenses" value="addExpenses">
                            </div>
                        </div>
                    </form>
                <?php }else{?>
                    <div>You havnot defined any APO Yet. Please create an APO to add expenses.</div>
                <?php }?>
            </div>
        </div>
    <?php }?>
    <div class="actions">
        <?php if($addFlag){?><a href="javascript:;" class="btn btn-primary showModule pull-right" data-target="addExpensesModule"><i class="icon-plus"></i>Add New Expense</a><?php }?>

        <a href="javascript:;" class="btn btn-small refresh">
            <i class="icon-refresh shaded"></i> Refresh Expenses
        </a>
    </div>
    <?php if($expenses_details != false && count($expenses_details)>0){?>
        <div class="stream-list">
            <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered" width="100%">
                <thead>
                    <tr>
                        <th width="2%">#</th>
                        <th width="55%">Title</th>
                        <th width="3%">Expense(m)</th>
                        <th width="20%">Head</th>
                        <th width="10%">Status</th>
                        <th width="10%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                    $cnt=1;
                    foreach($expenses_details as $row){?>
                        <tr>
                            <td><?php echo htmlentities($cnt);?></td>
                            <td>
                                <?php if($editFlag){?>
                                    <a class="ajaxEdit" href="ajax_project_expenses_edit.php?editExpenses=<?php echo htmlentities($row['id'])?>"><?php echo htmlentities($row['title']);?></a>
                                <?php }else{?>
                                    <?php echo htmlentities($row['title']);?>
                                <?php }?>
                            </td>
                            <td><?php echo htmlentities($row['cost']+0);?></td>
                            <td><?php echo htmlentities($row['head']);?></td>
                            <td><?php echo htmlentities($row['status']); if($row['status']=='Released'){ echo '<small> on '.date("d M, Y", strtotime($row["release_date"])).'</small>';}?></td>
                            <td><?php echo date("d M, Y", strtotime($row["added_date"]));?>
                            </td>
                        </tr>
                    <?php $cnt++; }?>
                </tbody>        
                <tfoot>
                    <tr>
                        <th colspan="6"  style="text-align:center"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!--/.stream-list-->
    <?php }else{?>
        <center>No expense found.<br><br></center>
    <?php }?>
<?php }?>