<?php 
$module_name = 'expenses';
include('classes/config.php');
authenticate_ajax();



$editFlag=authorizeAccess($module_name,'edit');

if($editFlag && isset($_GET['editExpenses']) && $_GET['editExpenses']>0){
    $expense_id=intval($crud->escape_string($_GET['editExpenses']));
    $editexpense_details=$crud->getData("select * from expenses where id = $expense_id");
    $editexpense_details = $editexpense_details[0];

    $pid = $editexpense_details['project_id'];

    $heads_details=$crud->getData("SELECT * from heads where project_id = $pid  and parent_head=0 order by sort_order asc");

    $apos_details=$crud->getData("SELECT * from apo where project_id = $pid order by id DESC");
    ?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3>Edit Expenses <span style="float:right"><a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <form class="form-horizontal row-fluid ajaxForm" name="editExpensesForm" method="post"  enctype="multipart/form-data" action="ajax_project_expenses_update.php?updateExpenses=<?php echo $expense_id?>">
                <div class="control-group">
                    <label class="control-label" for="basicinput">Title</label>
                    <div class="controls">
                        <input type="text" name="title" class="span12 tip" value="<?php echo $editexpense_details['title']?>" required="">
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
                                        $quer = "select heads.*,apo_heads.revised as apo_revised from heads left join apo_heads on heads.id=apo_heads.head_id where  heads.project_id = $pid  and heads.parent_head = ".$head['id']." and apo_heads.apo_id=".$apos_details[0]['id']." order by heads.sort_order asc";  
                                        $subquery=$crud->getData($quer);
                                        $subcnt=1;
                                        $alloc=0;
                                        foreach($subquery as $subrow)
                                        {?>
                                            <option value="<?php echo $subrow['id'].':'.($subrow['apo_revised']+0)?>" <?php if(@$editexpense_details['head_id']==$subrow['id']){echo 'selected="selected"';}$alloc=$subrow['apo_revised'];?> ><?php echo $subrow['head']?></option>
                                        <?php }?>
                                </optgroup>
                            <?php }?>
                        </select>
                        <small class="head_allocation"><b>Total Allocation: </b><?php echo $alloc+0?></small>
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label" for="basicinput">Expense(m)</label>
                    <div class="controls">
                        <input type="number" name="cost" class="span12 tip" required="" value="<?php echo @$editexpense_details['cost']+0?>" step="any">
                    </div>
                </div>        
                <div class="control-group">
                    <label class="control-label" for="basicinput">Status</label>
                    <div class="controls">
                        <select name="status" class="expense_status">
                            <option value="Booked">Booked</option>
                            <option value="Released" <?php if(@$editexpense_details['status']=='Released'){echo 'selected="selected"';}?>>Released</option>                   
                        </select>
                    </div>
                </div>                                                 
                <div class="control-group release_date" <?php if($editexpense_details['status']=='Booked'){?>style="display: none"<?php }?>>
                    <label class="control-label" for="basicinput">Release Date</label>
                    <div class="controls">
                        <input type="date" name="release_date" class="span12 tip" value="<?php echo @$editexpense_details['release_date']?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Description</label>
                    <div class="controls">
                        <textarea  name="details" class="span12 tip"><?php echo @$editexpense_details['details']?></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="basicinput">Images<br><?php if(!empty($editexpense_details['images'])){?><small>Check to remove the image/file.</small><?php }?></label>
                    <div class="controls">
                        <?php 
                            if(!empty($editexpense_details['images'])){
                                $vimg = explode(',', $editexpense_details['images']);
                                foreach ($vimg as $key => $value) {

                                    echo '<label class="checkbox inline"><input type="checkbox" name="rem_img[]"   value="'.htmlentities($value).'" title="Check to remove the image/file."> <a href="'.htmlentities($value).'" target="_blank">View Image/File</a> </label>';
                                }
                            }
                        ?>                                                            
                        <div class="fileswrapper" style="margin: 20px 0;">
                            <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                        </div>
                        <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                        <input  name="prev_images" type="hidden"  value="<?php echo $editexpense_details['images'];?>">
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="controls">
                        <input type="submit" name="editExpensesButton" value="Update Expenses" class="btn btn-primary submitbtn">
                        <input type="hidden" name="editExpenses" value="editExpenses">
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php }