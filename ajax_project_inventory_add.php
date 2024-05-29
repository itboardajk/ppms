<?php
$module_name = 'inventory';
include('classes/config.php');
authenticate_ajax();

$pid = intval($crud->escape_string($_GET['pid']));
//$paged = (isset($_GET['paged']) && $_GET['paged']>1)?$_GET['paged']:1;

$addFlag = authorizeAccess($module_name, 'add');

$return = array('msg' => '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>', 'status' => false);
// dd($_POST);
if (isset($_POST['addInventory'])) {
    if ($addFlag) {
        $data = array();
        if (!empty($_POST['item'])) {
            $data['item_id'] = $crud->escape_string($_POST['item']);
        }
        if ($_POST['item'] == 'other') {

            if (!empty($_POST['other_item'])) {
                $items_array= array(
                    'item_name'=>$crud->escape_string($_POST['other_item']),
                    'item_icon'=>htmlspecialchars('<i class="icon-building"></i>'),
                    'created_by'=>$_SESSION['id'],
                    'created_at'=> date('Y-m-d H:i:s'),
                );
                $data['item_id']= $generalmodal->insert_array('inventory_items', $items_array);
            }
        }
        if (!empty($_POST['make'])) {
            $data['make'] = $crud->escape_string($_POST['make']);
        }
        if (!empty($_POST['model'])) {
            $data['model'] = $crud->escape_string($_POST['model']);
        }
        if (!empty($_POST['procurement_date'])) {
            $data['purchase_date'] = $crud->escape_string($_POST['procurement_date']);
        }
        if (!empty($_POST['quantity'])) {
            $data['quantity'] = $crud->escape_string($_POST['quantity']);
            $data['current_stock'] = $crud->escape_string($_POST['quantity']);
        }
        if (!empty($_POST['serial_no'])) {
            $data['serial_no']=implode(',', $_POST['serial_no']);
        }
        if (!empty($_POST['description'])) {
            $data['description']=$crud->escape_string($_POST['description']);
        }
        $data['transaction_by'] = $_SESSION['id'];
        $data['transaction_type'] = 'StockIn';
        $data['department_id'] = $_SESSION['department_id'];
        $data['project_id'] = $pid;
        // dd($data);

        $msg = $validation->check_empty($_POST, array(array('item', 'Items'), array('procurement_date', 'Procurement Date'), array('quantity', 'Quantity'), array('make', 'Make'), array('model', 'Model')));

        if ($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Please correct the following errors:<br>' . $msg . '
                                </div>';
        } else {
            $imagefiles = array();
            $fimage = '';
            if (!empty($_FILES['fimage'])) {
                $file = $_FILES['fimage'];
                $file_name = $file['name'];
                @$file_ext = strtolower(end(explode('.', $file_name)));
                @$mimetype = mime_content_type($_FILES['fimage']['tmp_name']);


                $expensions = array("jpeg", "jpg", "png");
                $mimes = array('image/jpeg',  'image/png');

                if (!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext, $expensions) && in_array($mimetype, $mimes)) {
                    $folder_name = 'uploads/project_images';

                    $imagefile = $folder_name . "/inventory_" . date('YmdHis') . '_' . rand(1, 1000000) . '.' . $file_ext;
                    if (move_uploaded_file($file["tmp_name"], $imagefile)) {
                        $fimage = $imagefile;
                    }
                }
            }
            $result= $generalmodal->insert_array('inventory_transactions', $data);
            // $result = $crud->insert_and_get_id($sql);

            if ($result != false) {
                $return['status'] = true;
                $return['msg'] = '<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project inventory created !!
                                    </div>';

                $crud->log('Inventory(' . $result . ') Added', $_SESSION['id']);
            } else {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 Unable to create new inventory.
                                </div>';
            }
        }
    } else {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to add inventory
                                </div>';
    }
} else if (isset($_POST['stockoutInventory'])) {
    if ($addFlag) {
        $data = array();
        $data['item_id'] = $crud->escape_string($_POST['item']);
        $data['delivered_by'] = $crud->escape_string($_POST['delivered_by']);
        $data['delivered_by_designation'] = $crud->escape_string($_POST['delivered_by_designation']);
        $data['delivered_to_departments'] = $crud->escape_string($_POST['delivered_to_departments']);
        $data['delivered_to'] = $crud->escape_string($_POST['delivered_to']);
        $data['delivered_to_designation'] = $crud->escape_string($_POST['delivered_to_designation']);
        $data['delivered_on_date'] = $crud->escape_string($_POST['delivered_on_date']);
        $data['quantity'] = $crud->escape_string($_POST['quantity']);
        $data['current_stock'] =  $crud->escape_string($_POST['quantity']);
        $data['serial_no'] = implode(',', $_POST['serial_no']);
        $data['approval_authority'] = $crud->escape_string($_POST['approval_authority']);
        $data['transaction_by'] = $_SESSION['id'];
        $data['parent_id'] = $_POST['transaction_id'];
        $data['transaction_type'] = 'StockOut';
        $data['department_id'] = $_SESSION['department_id'];
        $data['project_id'] = $pid;

        /** -----Serial No -----------   **/

        $serial_no= $_POST['serial_no'];
        $all_serialno= explode(',',$_POST['all_serialno']);
        $unique_serialno = array_diff($all_serialno, $serial_no);
        $unique_serialno = array_unique($unique_serialno);
        $transactionData=array();
        $transactionData['serial_no']=implode(',', $unique_serialno);
        $transactionData['current_stock']= intval($_POST['current_stock'])-intval($_POST['quantity']);
        $msg = $validation->check_empty($_POST, array(array('delivered_by', 'Delivered By'), array('delivered_by_designation', 'Delivered By Designation'), array('delivered_to_departments', 'Delivered To Departments'), array('delivered_to', 'Delivered To'), array('delivered_to_designation', 'Delivered To Designation')));
        if ($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Please correct the following errors:<br>' . $msg . '
                                </div>';
        } else {
            $result= $generalmodal->insert_array('inventory_transactions', $data);

            if ($result != false) {
                $return['status'] = true;
                $return['msg'] = '<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project inventory created !!
                                    </div>';

                
                $crud->log('Inventory(' . $result . ') Stockout By', $_SESSION['id']);
                $condition = "id = ".$_POST['transaction_id'].""; 
                $result= $generalmodal->update_array('inventory_transactions', $transactionData, $condition);
            } else {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 Unable to create new inventory.
                                </div>';
            }
        }
    }  else {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                 You do not have access to add inventory
                                </div>';
    }
}

echo json_encode($return);
