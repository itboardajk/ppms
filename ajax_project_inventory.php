<?php
$module_name = 'inventory';
include('classes/config.php');
authenticate_ajax();

$pid = intval($crud->escape_string($_GET['pid']));

$viewFlag = authorizeAccess($module_name, 'view');
$addFlag = authorizeAccess($module_name, 'add');
$editFlag = authorizeAccess($module_name, 'edit');
$deleteFlag = authorizeAccess($module_name, 'delete');

if ($viewFlag) {
    $transaction_type = 'stockIn';
    $inventory_items = $crud->getData("SELECT it.*,
     SUM(CASE WHEN t.transaction_type = 'stockIn' THEN t.quantity ELSE 0 END)  as total_stock_in,
     SUM(CASE WHEN t.transaction_type = 'stockOut' THEN t.quantity ELSE 0 END)  as total_stock_out  
    from inventory_items it  join inventory_transactions t on t.item_id = it.item_id 
    where t.project_id = $pid
    GROUP BY it.item_id
    ORDER BY it.item_id DESC
    "
    );
    $inventory_details = $crud->getData("SELECT * from inventory_transactions t join inventory_items it on t.item_id = it.item_id where t.project_id = $pid  order by it.item_id DESC");
    $items= $crud->getData("SELECT * from inventory_items order by item_id DESC");
    // dd($inventory_items);

?>
    <?php if ($addFlag) { ?>
        <div class="module addCentersModule tabmodule">
            <div class="module-head">
                <h3>Add Inventory <span style="float:right"><a href="javascript:;" class="hideModule" data-target="addCentersModule"><i class="icon-remove-circle"></i> Close</a></span></h3>
            </div>
            <div class="module-body">

                <form class="form-horizontal row-fluid ajaxForm" name="addInventoryForm" method="post" onsubmit="validateform(event, this, 'addinventoryform')" enctype="multipart/form-data" action="ajax_project_inventory_add.php?pid=<?php echo $pid; ?>">
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Items</label>
                        <div class="controls">
                            <select class="span12 tip" onchange="selectOnchange(event)" name="item">
                                <option>Chose Items</option>
                                <?php foreach ($items as $item) { ?>
                                    <option value="<?=$item['item_id']?>"><?=$item['item_name']?></option>
                               <?php } ?>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="controls otherItems" style="display: none;">
                            <input type="text" name="other_item" class="span12 tip" placeholder="Other Items" value="">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Make</label>
                        <div class="controls">
                            <input type="text" name="make" class="span12 tip" required="" value="<?php echo @$_POST['make'] ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Model</label>
                        <div class="controls">
                            <input type="text" name="model" class="span12 tip" required="" value="<?php echo @$_POST['model'] ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Procurement Date</label>
                        <div class="controls">
                            <input type="date" name="procurement_date" class="span12 tip" required="" value="<?php echo @$_POST['make'] ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Number Of Items</label>
                        <div class="controls">
                            <input type="text" name="quantity" class="span12 tip" required="" value="<?php echo @$_POST['make'] ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Serial No</label>
                        <div class="controls">
                            <select name="serial_no[]" id="multiValueInput" class="select2 span12 tip" multiple required>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="basicinput">Description</label>
                        <div class="controls">
                            <textarea name="description" class="span12 tip"><?php echo @$_POST['description'] ?></textarea>
                        </div>
                    </div>
                    <!-- <div class="control-group">
                        <label class="control-label" for="basicinput">Featured Image</label>
                        <div class="controls">
                            <div class="fileswrapper" style="margin:0 0 20px 0;">
                                <input name="fimage" type="file" accept="image/*">
                            </div>
                        </div>
                    </div> -->
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" name="addCentersButton" value="Add Inventory" class="btn btn-primary submitbtn">
                            <input type="hidden" name="addInventory" value="addInventory">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>
    <div class="actions">
        <?php if ($addFlag) { ?><a href="javascript:;" class="btn btn-primary showModule pull-right" data-target="addCentersModule"><i class="icon-plus"></i>Add New Inventory</a><?php } ?>

        <a href="javascript:;" class="btn btn-small refresh">
            <i class="icon-refresh shaded"></i> Refresh Inventory
        </a>
    </div>
    <?php if ($inventory_details != false && count($inventory_details) > 0) { ?>
        <div class="alert">
            <!-- <strong><?php echo count($inventory_details); ?></strong> Branches/Centers Found.<span style="float:right"><a href="centers-onmap.php?project_id=<?php echo $pid ?>" target="_blank">View All On Map</a></span> -->
        </div>
        <div class="stream-list">
            <div class="media stream">
                <div class="media-body inventory-body" style="min-width: 96%;">
                    <div class="stream-headline">
                        <div class="btn-controls">
                            <div class="btn-box-row row-fluid">
                                <?php foreach ($inventory_items as $key => $item) { ?>
                                <a href="javascript:;" class="btn-box small span2" title="Workstations" style="background-color: #f9f9f9;">
                                    <?= htmlspecialchars_decode($item['item_icon']);  ?>
                                    <b><?= intval($item['total_stock_in'])-intval($item['total_stock_out']) ?></b>
                                    <p class="text-muted" style="margin-bottom: 0px;"><?= $item['item_name'] ?></p>
                                    <div>
                                        <span><i class="icon-arrow-up stocks-icon"></i><?= $item['total_stock_in'] ?></span>
                                        <span><i class="icon-arrow-down stocks-icon"></i> <?= $item['total_stock_out'] ?></span>
                                    </div>
                                </a>
                                <?php } ?>
                            </div>
                        </div>


                        <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped  display" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Items</th>
                                    <th>Item Detals</th>
                                    <th>Stock</th>
                                    <th>Serial No</th>
                                    <th>Stock In | Out</th>
                                    <th>Procurement Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $cnt=1;
                            foreach ($inventory_details as $key => $inventory) {
                                ?>  
                                <?php if($inventory['transaction_type']=='stockIn'): ?> 
                                    <tr>
                                        <td><?=$cnt++?></td>
                                        <td><?=$inventory['item_name']?></td>
                                        <td>
                                            <b>Make:</b> <?=$inventory['make']?> <br>
                                            <b>Model:</b> <?=$inventory['model']?> <br>
                                        </td>
                                        <td><?=$inventory['quantity']?></td>
                                        <td>
                                        <?=displayLimitedListItems($inventory['serial_no'])?>
                                        </td>
                                        <td><?=($inventory['transaction_type']=='stockIn')? 'Stock In' : (($inventory['transaction_type']=='stockOut')? 'Stock Out':'') ?></td>
                                        <td>01 May 2024</td>
                                        <td>
                                            <a href="ajax_inventory_out.php?inventoryout=<?=$inventory['id']?>" class="ajaxEdit" title="Edit"><i class="icon-arrow-up"></i></a>
                                            <!-- <a href="print_apo.php?apo_id=34&amp;action=print" target="_blank" title="Print"><i class="icon-print"></i></a>
                                            <a href="print_apo.php?apo_id=34&amp;action=pdf" target="_blank" title="PDF"><i class="icon-book"></i></a>
                                            <a href="ajax_project_apo_delete.php?deleteAPO=34" class="ajaxDelete" title="Delete"><i class="icon-remove-sign"></i></a> -->
                                        </td>
                                    </tr>
                                <?php elseif($inventory['transaction_type']=='stockOut'): ?>
                                    <tr class="stockout">
                                        <td><?=$cnt++?></td>
                                        <td><?=$inventory['item_name']?></td>
                                        <td>
                                            <b>Delivered To Departmnets:</b> <?=$inventory['delivered_to']?> <br>
                                            <b>Recived By:</b> <?=$inventory['delivered_to']?> <br>
                                            <b>Delivered on Date:</b> <?=$inventory['delivered_on_date']?> <br>
                                            <hr>
                                            <b>Delivered BY:</b> <?=$inventory['delivered_by']?> <br>
                                            <b>Approved By:</b> <?=$inventory['approval_authority']?> <br>

                                        </td>
                                        <td><?=$inventory['quantity']?></td>
                                        <td>
                                        <?=displayLimitedListItems($inventory['serial_no'])?>
                                        </td>
                                        <td><?=($inventory['transaction_type']=='stockIn')? 'Stock In' : (($inventory['transaction_type']=='stockOut')? 'Stock Out':'') ?></td>
                                        <td>01 May 2024</td>
                                        <td>
                                            <!-- <a href="ajax_inventory_out.php?inventoryout=<?=$inventory['id']?>" class="ajaxEdit" title="Edit"><i class="icon-arrow-up"></i></a> -->
                                            <!-- <a href="print_apo.php?apo_id=34&amp;action=print" target="_blank" title="Print"><i class="icon-print"></i></a>
                                            <a href="print_apo.php?apo_id=34&amp;action=pdf" target="_blank" title="PDF"><i class="icon-book"></i></a>
                                            <a href="ajax_project_apo_delete.php?deleteAPO=34" class="ajaxDelete" title="Delete"><i class="icon-remove-sign"></i></a> -->
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="panel">
                            <div class="centers_map" style="height: 400px" id="centers_map_<?php echo $row['id']; ?>" data-lat="<?php echo $row['latitude']; ?>" data-long="<?php echo $row['longitude']; ?>"></div>
                        </div>


                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $(".centers_map").each(function() {
                    uluru3 = {
                        lat: $(this).data('lat'),
                        lng: $(this).data('long')
                    };
                    map3 = new google.maps.Map(document.getElementById($(this).attr('id')), {
                        zoom: 16,
                        center: uluru3,
                        mapTypeId: 'satellite'
                    });
                    marker3 = new google.maps.Marker({
                        position: uluru3,
                        map: map3
                    });
                });
            </script>
        </div>
    <?php } else { ?>
        <center>No Inventory Stock Found<br><br></center>
    <?php } ?>
    <script>
        function attachOnChangeToInputs() {
            // Get all the input elements on the page
            var inputs = document.getElementsByTagName('input');
            var selects = document.getElementsByTagName('select');

            // Iterate over each input field
            for (var i = 0; i < inputs.length; i++) {
                // Attach the onchange event handler to the current input field
                inputs[i].addEventListener('change', handleInputChange);
                inputs[i].addEventListener('input', handleInputInput);
            }
            // Iterate over each input field
            for (var i = 0; i < selects.length; i++) {
                // Attach the onchange event handler to the current input field
                selects[i].addEventListener('change', handleSelectChange);
            }
        }

        function selectOnchange(event) {
            let input = event.target;
            let value = input.value;
            if (input.name == 'item') {
                if (input.value == 'other') {
                    input.parentNode.nextElementSibling.style.display = 'block'
                } else {
                    input.parentNode.nextElementSibling.style.display = 'none'
                }
            }
        }

        function validateform(event, form, id) {
            event.preventDefault();
            var inputs = form.querySelectorAll('input');
            var selects = form.querySelectorAll('select');
            var isValid = true;
            selects.forEach(function(select) {
                if (select.value == '') {
                    var other_services = form.querySelector('input[name="other_services"]');
                    isValid = false;
                    field_name = formatFieldName(select.name)
                    removeErrorComponent(select);
                    select.classList.add('error');
                    createErrorComponent(select, '' + field_name + 'field required.', '');
                } else {
                    select.classList.remove('error');
                    removeErrorComponent(select);
                }
            })
            if (isValid) {
                // console.log(isValid)
                // event.target.submit();
                // form.submit();
            }
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
<?php } ?>