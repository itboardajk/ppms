<?php
$module_name = 'APO';
include('classes/config.php');
authenticate_ajax();



$editFlag = authorizeAccess($module_name, 'edit');

if ($editFlag && isset($_GET['inventoryout']) && $_GET['inventoryout'] > 0) {

    $transaction_id = intval($crud->escape_string($_GET['inventoryout']));
    $inventory_details = $crud->getData("select * from inventory_transactions where id = $transaction_id");
    // dd($transaction_id);
    $inventory_details = $inventory_details[0];
    $serial_no= explode(',',$inventory_details['serial_no']);
    
    $pid = $inventory_details['project_id'];

    $inventoryItems = $crud->getData("select *  from inventory_items   where item_id=" . $inventory_details['item_id']);
    $inventoryItem = $inventoryItems[0];

    $query = "select *  from projects   where id=" . $pid;
    $project = $crud->getData($query);
    $project = $project[0];
?>
    <div class="module" style="margin-top: 20px;">
        <div class="module-head">
            <h3>Inventory Stock Out <span style="float:right"> | <a href="javascript:;" class="backFromEdit"><i class="icon-remove-circle"></i> Back</a></span></h3>
        </div>
        <div class="module-body">
            <form class="form-horizontal row-fluid ajaxForm" name="addInventoryForm" method="post" onsubmit="validateform(event, this, 'addinventoryform')" enctype="multipart/form-data" action="ajax_project_inventory_add.php?pid=<?php echo $pid; ?>">
            <div class="control-group">
                <div class="btn-box small span12 active"  style="background-color: #29b7d3; color: #fff">
                    <?=$inventoryItem['item_icon']?>
                    <b>Current Stocks: <?=isset($inventory_details['current_stock'])?$inventory_details['current_stock']:''?></b>
                    <p class="text-muted" style="margin-bottom: 0px; color:#fff">Stocks Out <?=$inventoryItem['item_name']?></p>
                </div>      
            </div>    
            <fieldset>
                    <legend>Delivered By Details:</legend>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Delivered By Name</label>
                        <div class="controls">
                            <input type="text" name="delivered_by" class="span12 tip" required="">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Delivered By Designation</label>
                        <div class="controls">
                            <input type="text" name="delivered_by_designation" class="span12 tip" required="" value="<?php echo @$_POST['make'] ?>">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Delivered To Details:</legend>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Delivered To Departmnets</label>
                        <div class="controls">
                            <input type="text" name="delivered_to_departments" class="span12 tip" required="" >
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Delivered To Name / Assigned to</label>
                        <div class="controls">
                            <input type="text" name="delivered_to" class="span12 tip" required="" >
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Delivered To Designation</label>
                        <div class="controls">
                            <input type="text" name="delivered_to_designation" class="span12 tip" required="" >
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Delivered on Date</label>
                        <div class="controls">
                            <input type="date" name="delivered_on_date" class="span12 tip" required="" >
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Items Details:</legend>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Number of Items</label>
                        <div class="controls">
                            <input type="number" name="quantity" max="<?=$inventory_details['current_stock']?>" class="span12 tip" required="" >
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Serial No</label>
                        <div class="controls">
                            <select name="serial_no[]" id="multiValueInput" autocomplete="off" class="select2 form-control"  multiple>
                                <?php foreach ($serial_no as $no) {
                                   echo '<option value="'.$no.'">'.$no.'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Approval Authority</label>
                        <div class="controls">
                            <input type="text" name="approval_authority" class="span12 tip" required="">
                        </div>
                    </div>
                </fieldset>
                <div class="control-group" style="margin-top: 6px;">
                    <div class="controls">
                        <input type="submit" name="addCentersButton" value="Confirm Stock Out" class="btn btn-primary submitbtn">
                        <input type="hidden" name="stockoutInventory" value="stockoutInventory">
                        <input type="hidden" name="item" value="<?=$inventory_details['item_id']?>">
                        <input type="hidden" name="transaction_id" value="<?=$inventory_details['id']?>">
                        <input type="hidden" name="all_serialno" value="<?=$inventory_details['serial_no']?>">
                        <input type="hidden" name="current_stock" value="<?=$inventory_details['current_stock']?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        $(".sort_authorities").sortable({
            //appendTo: document.body      
            placeholder: "ui-state-highlight"
        });
        $(".sort_authorities").disableSelection();

        function newAuth() {
            $(".sort_authorities").append($('.auth_form').html());
        }
        $(document).ready(function() {
            // Initialize Select2 with tags option
            $('#multiValueInput').select2({
                tags: true,
                placeholder: 'Enter unique serial numbers...',
                tokenSeparators: [',', ' ']
            });
        })
    </script>
<?php }
