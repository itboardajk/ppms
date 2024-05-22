<?php
$module_name = 'projects';
include('classes/config.php');
authenticate();

    $viewFlag=authorizeAccess($module_name,'view');
    $addFlag=authorizeAccess($module_name,'add');
    $editFlag=authorizeAccess($module_name,'edit');
    $deleteFlag=authorizeAccess($module_name,'delete');

    //Updates Access
    $updates_viewFlag=authorizeAccess('updates','view');
    $updates_addFlag=authorizeAccess('updates','add');
    $updates_editFlag=authorizeAccess('updates','edit');
    $updates_deleteFlag=authorizeAccess('updates','delete');

    //Expenses Access
    $expenses_viewFlag=authorizeAccess('expenses','view');
    $expenses_addFlag=authorizeAccess('expenses','add');
    $expenses_editFlag=authorizeAccess('expenses','edit');
    $expenses_deleteFlag=authorizeAccess('expenses','delete');

    //Head Access
    $heads_viewFlag=authorizeAccess('heads','view');
    $heads_addFlag=authorizeAccess('heads','add');
    $heads_editFlag=authorizeAccess('heads','edit');
    $heads_deleteFlag=authorizeAccess('heads','delete');

    //APOs Access
    $apo_viewFlag=authorizeAccess('apo','view');
    $apo_addFlag=authorizeAccess('apo','add');
    $apo_editFlag=authorizeAccess('apo','edit');
    $apo_deleteFlag=authorizeAccess('apo','delete');

    //Inspections Access
    $inspections_viewFlag=authorizeAccess('inspections','view');
    $inspections_addFlag=authorizeAccess('inspections','add');
    $inspections_editFlag=authorizeAccess('inspections','edit');
    $inspections_deleteFlag=authorizeAccess('inspections','delete');

    //Centers Access
    $centers_viewFlag=authorizeAccess('centers','view');
    $centers_addFlag=authorizeAccess('centers','add');
    $centers_editFlag=authorizeAccess('centers','edit');
    $centers_deleteFlag=authorizeAccess('centers','delete');

    //Cycles Access
    $cycles_viewFlag=authorizeAccess('project_cycles','view');
    $cycles_addFlag=authorizeAccess('project_cycles','add');
    $cycles_editFlag=authorizeAccess('project_cycles','edit');
    $cycles_deleteFlag=authorizeAccess('project_cycles','delete');


if(!$viewFlag){header("location:{$site_url}/dashboard.php");exit();}


$pid=intval($crud->escape_string($_GET['view']));

if(isset($_POST['addUpdate']))
{
    if($updates_addFlag)
    {
        $details=$crud->escape_string($_POST['details']);
        //$completed_percentage=$crud->escape_string($_POST['completed_percentage']);
        $msg = $validation->check_empty($_POST, array(array('details','Update Details')));


        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
            $attachment_error='';
            $imagefiles=array();
            if(!empty($_FILES['filesToUpload']))
            {
                $file_ary = reArrayFiles($_FILES['filesToUpload']);
                if(!empty($file_ary[0]["name"]))
                {
                    $imageLinks='';
                    foreach($file_ary as $file)
                    {
                      
                        $file_name = $file['name'];
                        @$file_ext=strtolower(end(explode('.',$file_name)));
                        if(!empty($file_ext) && !empty($file_name) && in_array($file_ext,array("mp4","png","jpg","jpeg","jfif","gif","doc","docx","xls","xlsx","pdf"))) //,"mp4","mp3","webm"
                        {
                            $folder_name='uploads/project_images';
                                
                            $imagefile =$folder_name."/update_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }
                }
            }

            $sql="INSERT INTO updates(details,images,added_by,project_id) values('$details','".implode(",",$imagefiles)."','".$_SESSION['id']."','$pid')";
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {
                $sucmsg="Update Created !!";
                unset($_POST);
                $crud->log('Update('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to create new update.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to add update';
    }
}
else if(isset($_POST['addHead']))
{
    if($heads_addFlag)
    {
        $head=$crud->escape_string($_POST['head']);
        $parent_head=$crud->escape_string($_POST['parent_head']);
        $code=$crud->escape_string($_POST['code']);
        $unit=$crud->escape_string($_POST['unit']);
        $quantity=$crud->escape_string($_POST['quantity']);
        $cost=$crud->escape_string($_POST['cost']);
        $sort_order=$crud->escape_string($_POST['sort_order']);
    
        if($parent_head==0)
        {
            $unit='';
            $quantity='';
            $cost='0';
        }
        else
        {
           $code=''; 
        }

        $msg = $validation->check_empty($_POST, array(array('head','Head')));


        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {

            $cost = (empty($cost))?0:$cost;


            $sql="INSERT INTO heads(head,parent_head,unit,quantity,cost,sort_order,added_by,project_id,code) values('$head','$parent_head','$unit','$quantity','$cost','$sort_order','".$_SESSION['id']."','$pid','$code')";
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {
                $sucmsg="Head Created !!";
                unset($_POST);
                $crud->log('Head('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to create new head.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to add head';
    }
}
else if(isset($_POST['editHead']) && isset($_GET['editHead']) && $_GET['editHead']>0)
{
    if($heads_editFlag)
    {
        $head_id=$crud->escape_string($_GET['editHead']);

        $head=$crud->escape_string($_POST['head']);
        $parent_head=$crud->escape_string($_POST['parent_head']);
        $code=$crud->escape_string($_POST['code']);
        $unit=$crud->escape_string($_POST['unit']);
        $quantity=$crud->escape_string($_POST['quantity']);
        $cost=$crud->escape_string($_POST['cost']);
        $sort_order=$crud->escape_string($_POST['sort_order']);
        
        if($parent_head==0)
        {
            $unit='';
            $quantity='';
            $cost='0';
        }
        else
        {
           $code=''; 
        }

        $msg = $validation->check_empty($_POST, array(array('head','Head')));


        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {

            $sql="UPDATE heads set head='$head',parent_head='$parent_head',unit='$unit',quantity='$quantity',cost='$cost',sort_order='$sort_order',updated_by='".$_SESSION['id']."',code='$code' where id=".$head_id;
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                $sucmsg="Head Updated !!";
                unset($_POST);
                $crud->log('Head('.$result.') updated',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to edit head.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to edit head';
    }
}
else if(isset($_POST['addapo']))
{
    if($apo_addFlag)
    {
        $apo=$crud->escape_string($_POST['apo']);
        $allocation=$crud->escape_string($_POST['allocation']);

        $msg = $validation->check_empty($_POST, array(array('apo','APO'),array('allocation','Allocation')));


        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
            $progress=0;
            if(isset($_POST['prog_qty']))
                $progress=1;


            $sql="INSERT INTO apo(apo,allocation,progress,added_by,project_id) values('$apo','$allocation','$progress','".$_SESSION['id']."','$pid')";
             
            $apo_id = $crud->insert_and_get_id($sql);

            if($apo_id != false)
            {

                foreach($_POST['subheads'] as $subhead_id)
                {
                    @$apo_qty=$crud->escape_string($_POST['apo_qty'][$subhead_id]);
                    @$apo_allocation=$crud->escape_string($_POST['apo_allocation'][$subhead_id]);
                    
                    if(isset($_POST['prog_qty']))
                    {
                        @$prog_qty=$crud->escape_string($_POST['prog_qty'][$subhead_id]);
                        @$prog_expences=$crud->escape_string($_POST['prog_expences'][$subhead_id]);
                        @$prog_status=$crud->escape_string($_POST['prog_status'][$subhead_id]);
                    }
                    else
                    {
                        $prog_qty=-1;
                        $prog_expences=-1;
                        $prog_status=-1;
                    }

                    $isql="INSERT INTO apo_heads(apo_id,head_id,prog_qty,prog_expences,prog_status,quantity,revised) values('$apo_id','$subhead_id','$prog_qty','$prog_expences','$prog_status','$apo_qty','$apo_allocation')";
                    $crud->execute($isql);

                }   

                $sucmsg="APO Created !!";
                unset($_POST);
                $crud->log('APO('.$apo_id.') Added',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to create new APO.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to add APO';
    }
}
else if(isset($_POST['editapo']) && isset($_GET['editAPO']) && $_GET['editAPO']>0)
{
    if($apo_editFlag)
    {
        $apo=$crud->escape_string($_POST['apo']);
        $allocation=$crud->escape_string($_POST['allocation']);

        $apo_id=intval($crud->escape_string($_GET['editAPO']));
        $msg = $validation->check_empty($_POST, array(array('apo','APO'),array('allocation','Allocation')));



        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {

            $sql="UPDATE apo set apo='$apo',allocation='$allocation',updated_by='".$_SESSION['id']."' where id=".$apo_id;
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                $crud->execute("DELETE FROM apo_heads WHERE apo_id=".$apo_id);

                foreach($_POST['subheads'] as $subhead_id)
                {
                    @$apo_qty=$crud->escape_string($_POST['apo_qty'][$subhead_id]);
                    @$apo_allocation=$crud->escape_string($_POST['apo_allocation'][$subhead_id]);
                    
                    if(isset($_POST['prog_qty']))
                    {
                        @$prog_qty=$crud->escape_string($_POST['prog_qty'][$subhead_id]);
                        @$prog_expences=$crud->escape_string($_POST['prog_expences'][$subhead_id]);
                        @$prog_status=$crud->escape_string($_POST['prog_status'][$subhead_id]);
                    }
                    else
                    {
                        $prog_qty=-1;
                        $prog_expences=-1;
                        $prog_status=-1;
                    }

                    $isql="INSERT INTO apo_heads(apo_id,head_id,prog_qty,prog_expences,prog_status,quantity,revised) values('$apo_id','$subhead_id','$prog_qty','$prog_expences','$prog_status','$apo_qty','$apo_allocation')";
                    $crud->execute($isql);

                }   

                $sucmsg="APO Updated !!";
                unset($_POST);
                $crud->log('APO('.$result.') updated',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to edit APO.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to edit APO';
    }
}
else if(isset($_POST['addExpense']))
{
    if($expenses_addFlag)
    {
        $title=$crud->escape_string($_POST['title']);
        $cost=$crud->escape_string($_POST['cost']);
        $head_id=$crud->escape_string($_POST['head_id']);
        $details=$crud->escape_string($_POST['details']);

        $status=$crud->escape_string($_POST['status']);
        $release_date=$crud->escape_string($_POST['release_date']);
        if($status == 'Released')
        {
            $msg = $validation->check_empty($_POST, array(array('title','Expense Title'),array('cost','Cost'),array('head_id','Expense Head'),array('release_date','Expense Release Date')));
        }
        else
        {
           $msg = $validation->check_empty($_POST, array(array('title','Expense Title'),array('cost','Cost'),array('head_id','Expense Head'))); 
        }
        

        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
            $attachment_error='';
            $imagefiles=array();
            if(!empty($_FILES['filesToUpload']))
            {
                $file_ary = reArrayFiles($_FILES['filesToUpload']);
                if(!empty($file_ary[0]["name"]))
                {
                    $imageLinks='';
                    foreach($file_ary as $file)
                    {
                      
                        $file_name = $file['name'];
                        @$file_ext=strtolower(end(explode('.',$file_name)));
                        if(!empty($file_ext) && !empty($file_name) && in_array($file_ext,array("mp4","png","jpg","jpeg","jfif","gif","doc","docx","xls","xlsx","pdf","txt","ppt","pptx"))) //,"mp4","mp3","webm"
                        {
                            $folder_name='uploads/project_images';
                                
                            $imagefile =$folder_name."/expense_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }
                }
            }
            $head_id = explode(':', $head_id);
            $head_id = $head_id[0];
            if($status == 'Released')
            {
                $sql="INSERT INTO expenses(title,cost,details,status,release_date,head_id,images,added_by,project_id) values('$title','$cost','$details','$status','$release_date','$head_id','".implode(",",$imagefiles)."','".$_SESSION['id']."','$pid')";
            }
            else
            {
                $sql="INSERT INTO expenses(title,cost,details,status,head_id,images,added_by,project_id) values('$title','$cost','$details','$status','$head_id','".implode(",",$imagefiles)."','".$_SESSION['id']."','$pid')";
            }
            
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {
                $sucmsg="Expense Created !!";
                unset($_POST);
                $crud->log('Expense('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to create new expense.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to add expense';
    }
}
else if(isset($_POST['editExpense']) && isset($_GET['editExpense']) && $_GET['editExpense']>0)
{
    if($expenses_editFlag)
    {
        $exp_id=intval($crud->escape_string($_GET['editExpense']));

        $title=$crud->escape_string($_POST['title']);
        $cost=$crud->escape_string($_POST['cost']);
        $head_id=$crud->escape_string($_POST['head_id']);
        $details=$crud->escape_string($_POST['details']);

        $status=$crud->escape_string($_POST['status']);
        $release_date=$crud->escape_string($_POST['release_date']);
        
        $Prev_Images='';
        if(isset($_POST['rem_img']))
            $Prev_Images = implode(',', $_POST['rem_img']);


        if($status == 'Released')
        {
            $msg = $validation->check_empty($_POST, array(array('title','Expense Title'),array('cost','Cost'),array('head_id','Expense Head'),array('release_date','Expense Release Date')));
        }
        else
        {
           $msg = $validation->check_empty($_POST, array(array('title','Expense Title'),array('cost','Cost'),array('head_id','Expense Head'))); 
        }
        

        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
            $attachment_error='';
            $imagefiles=array();
            if(!empty($_FILES['filesToUpload']))
            {
                $file_ary = reArrayFiles($_FILES['filesToUpload']);
                if(!empty($file_ary[0]["name"]))
                {
                    $imageLinks='';
                    foreach($file_ary as $file)
                    {
                      
                        $file_name = $file['name'];
                        @$file_ext=strtolower(end(explode('.',$file_name)));
                        if(!empty($file_ext) && !empty($file_name) && in_array($file_ext,array("mp4","png","jpg","jpeg","jfif","gif","doc","docx","xls","xlsx","pdf","txt","ppt","pptx"))) //,"mp4","mp3","webm"
                        {
                            $folder_name='uploads/project_images';
                                
                            $imagefile =$folder_name."/expense_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }

                    if(empty($Prev_Images))
                        $imgstr=implode(',', $imagefiles);
                    else
                        $imgstr=$Prev_Images.",".implode(',', $imagefiles);
                }
                else
                {
                    $imgstr=$Prev_Images;
                }
            }
            else
            {
                $imgstr=$Prev_Images;
            }

            $head_id = explode(':', $head_id);
            $head_id = $head_id[0];

            $updb='';
            if(!empty($release_date))
                $updb = ",release_date='$release_date'";

            $sql="UPDATE expenses set title='$title',cost='$cost',details='$details',status='$status',release_date='$release_date',head_id='$head_id',images='$imgstr',updated_by='".$_SESSION['id']."' ".$updb." where id=".$exp_id;
            
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                $sucmsg="Expense Updated !!";
                unset($_POST);
                $crud->log('Expense('.$result.') Updated',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to update expense.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to update expense';
    }
}
else if(isset($_POST['addCenter']))
{
    if($expenses_addFlag)
    {
        $title=$crud->escape_string($_POST['title']);
        $address=$crud->escape_string($_POST['address']);
        $description=$crud->escape_string($_POST['description']);

        $focal_person=$crud->escape_string($_POST['focal_person']);
        $focal_person_email=$crud->escape_string($_POST['focal_person_email']);
        $focal_person_phone=$crud->escape_string($_POST['focal_person_phone']);
        
        $workstaions=$crud->escape_string($_POST['workstaions']);
        $laptops=$crud->escape_string($_POST['laptops']);
        $printers=$crud->escape_string($_POST['printers']);
        $scanners=$crud->escape_string($_POST['scanners']);

        $total_staff=$crud->escape_string($_POST['total_staff']);
        $latitude=$crud->escape_string($_POST['latitude']);
        $longitude=$crud->escape_string($_POST['longitude']);      

       $msg = $validation->check_empty($_POST, array(array('title','Center Title'),array('address','Address'),array('focal_person','Focal Person'))); 
       
        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
            $attachment_error='';
            $imagefiles=array();
            if(!empty($_FILES['filesToUpload']))
            {
                $file_ary = reArrayFiles($_FILES['filesToUpload']);
                if(!empty($file_ary[0]["name"]))
                {
                    $imageLinks='';
                    foreach($file_ary as $file)
                    {
                      
                        $file_name = $file['name'];
                        @$file_ext=strtolower(end(explode('.',$file_name)));
                        if(!empty($file_ext) && !empty($file_name) && in_array($file_ext,array("mp4","png","jpg","jpeg","jfif","gif","doc","docx","xls","xlsx","pdf","txt","ppt","pptx"))) //,"mp4","mp3","webm"
                        {
                            $folder_name='uploads/project_images';
                                
                            $imagefile =$folder_name."/center_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }
                }
            }
            $sql="INSERT INTO centers(title,address,description,focal_person,focal_person_email,focal_person_phone,workstaions,laptops,printers,scanners,total_staff,latitude,longitude,images,added_by,project_id) values('$title','$address','$description','$focal_person','$focal_person_email','$focal_person_phone','$workstaions','$laptops','$printers','$scanners','$total_staff','$latitude','$longitude','".implode(",",$imagefiles)."','".$_SESSION['id']."','$pid')";
            
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {
                $sucmsg="Center Created !!";
                unset($_POST);
                $crud->log('Center('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to create new center.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to add center';
    }
}
else if(isset($_POST['editCenter']) && isset($_GET['editCenter']) && $_GET['editCenter']>0)
{
    if($expenses_editFlag)
    {
        $center_id=intval($crud->escape_string($_GET['editCenter']));
        
        $title=$crud->escape_string($_POST['title']);
        $address=$crud->escape_string($_POST['address']);
        $description=$crud->escape_string($_POST['description']);

        $focal_person=$crud->escape_string($_POST['focal_person']);
        $focal_person_email=$crud->escape_string($_POST['focal_person_email']);
        $focal_person_phone=$crud->escape_string($_POST['focal_person_phone']);
        
        $workstaions=$crud->escape_string($_POST['workstaions']);
        $laptops=$crud->escape_string($_POST['laptops']);
        $printers=$crud->escape_string($_POST['printers']);
        $scanners=$crud->escape_string($_POST['scanners']);

        $total_staff=$crud->escape_string($_POST['total_staff']);
        $latitude=$crud->escape_string($_POST['latitude']);
        $longitude=$crud->escape_string($_POST['longitude']);        

        $msg = $validation->check_empty($_POST, array(array('title','Center Title'),array('address','Address'),array('focal_person','Focal Person'))); 
               
        $Prev_Images='';
        if(isset($_POST['rem_img']))
            $Prev_Images = implode(',', $_POST['rem_img']);

        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
            $attachment_error='';
            $imagefiles=array();
            if(!empty($_FILES['filesToUpload']))
            {
                $file_ary = reArrayFiles($_FILES['filesToUpload']);
                if(!empty($file_ary[0]["name"]))
                {
                    $imageLinks='';
                    foreach($file_ary as $file)
                    {
                      
                        $file_name = $file['name'];
                        @$file_ext=strtolower(end(explode('.',$file_name)));
                        if(!empty($file_ext) && !empty($file_name) && in_array($file_ext,array("mp4","png","jpg","jpeg","jfif","gif","doc","docx","xls","xlsx","pdf","txt","ppt","pptx"))) //,"mp4","mp3","webm"
                        {
                            $folder_name='uploads/project_images';
                                
                            $imagefile =$folder_name."/center_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }

                    if(empty($Prev_Images))
                        $imgstr=implode(',', $imagefiles);
                    else
                        $imgstr=$Prev_Images.",".implode(',', $imagefiles);
                }
                else
                {
                    $imgstr=$Prev_Images;
                }
            }
            else
            {
                $imgstr=$Prev_Images;
            }


            $sql="UPDATE centers set title='$title',address='$address',description='$description',focal_person='$focal_person',focal_person_email='$focal_person_email',focal_person_phone='$focal_person_phone',workstaions='$workstaions',laptops='$laptops',printers='$printers',scanners='$scanners',total_staff='$total_staff',latitude='$latitude',longitude='$longitude',images='$imgstr',updated_by='".$_SESSION['id']."'  where id=".$center_id;
            
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                $sucmsg="Center Updated !!";
                unset($_POST);
                $crud->log('Center('.$result.') Updated',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to update center.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to update center';
    }
}
else if(isset($_POST['addInspection']))
{
    if($inspections_addFlag)
    {
        $details=$crud->escape_string($_POST['details']);
        $team=$crud->escape_string($_POST['team']);
        $status=$crud->escape_string($_POST['status']);
        //$completed_percentage=$crud->escape_string($_POST['completed_percentage']);
        $msg = $validation->check_empty($_POST, array(array('details','Inspection Details'),array('team','Inspection Team')));


        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
            $attachment_error='';
            $imagefiles=array();
            if(!empty($_FILES['filesToUpload']))
            {
                $file_ary = reArrayFiles($_FILES['filesToUpload']);
                if(!empty($file_ary[0]["name"]))
                {
                    $imageLinks='';
                    foreach($file_ary as $file)
                    {
                      
                        $file_name = $file['name'];
                        @$file_ext=strtolower(end(explode('.',$file_name)));
                        if(!empty($file_ext) && !empty($file_name) && in_array($file_ext,array("mp4","png","jpg","jpeg","jfif","gif","doc","docx","xls","xlsx","pdf"))) //,"mp4","mp3","webm"
                        {
                            $folder_name='uploads/project_images';
                                
                            $imagefile =$folder_name."/inspection_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }
                }
            }

            $sql="INSERT INTO inspections(details,status,team,images,added_by,project_id) values('$details','$status','$team','".implode(",",$imagefiles)."','".$_SESSION['id']."','$pid')";
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {
                $sucmsg="Inspection Created !!";
                unset($_POST);
                $crud->log('Inspection('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to create new inspection.';
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to add inspection';
    }
}
else if(isset($_GET['deleteHead']) && $_GET['deleteHead']>0)
{

	if($heads_deleteFlag)
	{
		$id=intval($crud->escape_string($_GET['deleteHead']));

		$query = "delete from heads where id=$id or parent_head=$id";
		$result = $crud->execute($query);

		$sucmsg="Project head deleted !!";
		$crud->log('Project head('.$id.') deleted',$_SESSION['id']);
	}
	else
	{
		$errmsg = 'You do not have access to delete project head';
	}
}
else if(isset($_POST['addCycle']))
{
	if($cycles_addFlag)
    {
        $title=$crud->escape_string($_POST['title']);

        $msg = $validation->check_empty($_POST, array(array('title','Project Cycle'),));


        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
        	$details = $_POST['details'];
        	/*$newdetails=array();
        	foreach ($details as $key => $value) {
        		if(is)
        		$newdetails[$crud->escape_string($key)]=$crud->escape_string($value);
        	}*/
            $sql="INSERT INTO project_cycles(title,defaults,added_by,project_id) values('$title','".json_encode($details)."','".$_SESSION['id']."','$pid')";
             
            $result = $crud->insert_and_get_id($sql);

            if($result != false)
            {
                $sucmsg=$title." Created !!";
                unset($_POST);
                $crud->log($title.'('.$result.') Added',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to create new '.$title;
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to add '.$title;
    }
}
else if(isset($_POST['updateCycle']))
{
	if($cycles_addFlag)
    {

		$cycle_id=intval($crud->escape_string($_POST['cycle_id']));
        $title=$crud->escape_string($_POST['title']);

        $msg = $validation->check_empty($_POST, array(array('title','Project Cycle'),));


        if($msg != null) {
            $errmsg = 'Please correct the following errors:<br>'.$msg;
        }    
        else {
        	$details = $_POST['details'];
        	/*$newdetails=array();
        	foreach ($details as $key => $value) {
        		$newdetails[$crud->escape_string($key)]=$crud->escape_string($value);
        	}*/

            $sql="UPDATE project_cycles set title='$title',defaults='".json_encode($details)."',updated_by='".$_SESSION['id']."' where id=".$cycle_id;
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                $sucmsg=$title." Updated !!";
                unset($_POST);
                $crud->log($title.'('.$result.') updated',$_SESSION['id']);
            }
            else
            {
                $errmsg = 'Unable to update '.$title;
            }
        }
    }
    else
    {
        $errmsg = 'You do not have access to update '.$title;
    }
}
else if(isset($_GET['deleteAPO']) && $_GET['deleteAPO']>0)
{

    if($heads_deleteFlag)
    {
        $id=intval($crud->escape_string($_GET['deleteAPO']));

        $query = "delete from apo where id=$id";
        $result = $crud->execute($query);
        $query = "delete from apo_heads where apo_id=$id";
        $result = $crud->execute($query);

        $sucmsg="APO  deleted !!";
        $crud->log('APO('.$id.') deleted',$_SESSION['id']);
    }
    else
    {
        $errmsg = 'You do not have access to delete APO.';
    }
}


$query="select *  from projects   where id=".$pid;
$project = $crud->getData($query);
$project=$project[0];

$Attachments = explode(',',$project['images']);
$featured_image="";
if(count($Attachments)>0){
    foreach ($Attachments as $key => $value) {
        if(empty($featured_image))
        {

            $ext = strtolower($value);
            if(endsWith($ext,'.jpg') || endsWith($ext,'.jpeg')  || endsWith($ext,'.png')  || endsWith($ext,'.gif') ||  endsWith($ext,'.jfif')){
                $featured_image = $value;
                break;
            }
        }

    }
}
if(empty($featured_image))
    $featured_image = "images/project.png";


$project_updates = $crud->getData("SELECT updates.*,admin.display_name,admin.admin_image  from updates left join admin on updates.added_by=admin.id where project_id=".$pid." order by added_date DESC");


$heads_details=$crud->getData("SELECT * from heads where project_id = $pid  and parent_head=0 order by sort_order asc");

$heads_sum = $crud->getData("SELECT SUM(cost) as used from heads where project_id = $pid");

$apos_details=$crud->getData("SELECT * from apo where project_id = $pid order by id DESC");


$expenses_details = $crud->getData("SELECT expenses.*,heads.head from expenses left join heads on expenses.head_id=heads.id where expenses.project_id=".$pid);

$inspections_details = $crud->getData("SELECT inspections.*,admin.display_name,admin.admin_image  from inspections left join admin on inspections.added_by=admin.id where project_id=".$pid." order by added_date DESC");



$centers_details=$crud->getData("SELECT * from centers where project_id = $pid order by id DESC");

$default_cycles_details=$crud->getData("SELECT * FROM `project_cycles` where project_id is NULL ORDER BY id ASC ");
$cycles_details=$crud->getData("SELECT * from `project_cycles` where project_id = $pid order by id ASC");

$my_cycles=array();
if($cycles_details != false && count($cycles_details)>0)
{
	foreach ($cycles_details as $row) {
		$my_cycles[$row['title']] = $row;
	}
	
}

if(!isset($_POST['latitude']))
{
    $_POST['latitude']='34.359210920164855';
    $_POST['longitude']='73.473524646604564';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Project Details | <?php echo  $site_title?></title>
    <?php include_once('include/head.php');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.21/datatables.min.css"/>
	<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" /> -->
  <script src="https://cdn.tiny.cloud/1/iclvnebsn2aw7x8c8huypa3s7aaapadca31bpipiphyeev4a/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">
			<?php include('include/sidebar.php');?>				
			<div class="span9">
					<div class="content">
					      <?php if(!empty(@$errmsg)){?>
					        <div class="alert alert-danger alert-dismissible fade in" role="alert">
					          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
					          <?php echo $errmsg;?>
					        </div>
					      <?php } else if(!empty(@$sucmsg)){?>
					        <div class="alert alert-success alert-dismissible fade in" role="alert">
					          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
					          <?php echo $sucmsg;?>
					        </div>
					      <?php }?>
	                    <div class="module">
	                    	<div class="module-body">
                                <div class="profile-head media">
                                    <a href="<?php echo $featured_image?>" target="_blank" class="media-avatar pull-left">
                                        <img src="<?php echo $featured_image?>">
                                    </a>
                                    <div class="media-body">
                                        <h4>
                                            <?php echo $project["title"]?> <span class="cell-status"><b class="<?php echo $project["status"]?>"><?php echo $project["status"]?></b></span><br>
                                            <small><?php echo date("d M, Y", strtotime($project["start_date"]))?> -- <?php echo date("d M, Y", strtotime($project["end_date"]));?><?php if(!empty($project["revision_date"]) && $project["revision_date"]!='0000-00-00'){ echo ' -- '.date("d M, Y", strtotime($project["revision_date"]));}?></small>
                                        </h4>
                                        <p class="profile-brief"><?php echo $project["details"]?><br>
                                        <?php
                                        if(count($Attachments)>0){
                                            foreach ($Attachments as $key => $value) {
                                                echo '<a href="'.htmlentities($value).'" target="_blank">View Attachment</a> ';
                                            }
                                        }

                                        ?></p>

                                        <div class="profile-details">
                                            <div class="pull-left">

                                                <a href="javascript:;" class="btn btn-inverse <?php if($heads_viewFlag){ echo 'showModule';}?>" data-target="headModule"><i class="icon-money"></i> <?php echo number_format($project["budget"],6)?> </a>
                                            </div>
                                            <div class="pull-right">
                                                <?php if($updates_addFlag){?><a href="javascript:;" class="btn btn-success showModule" data-target="addUpdate"><i class="icon-plus"></i>Update</a><?php }?>
                                                <?php if($inspections_addFlag){?><a href="javascript:;" class="btn btn-danger showModule" data-target="addInspection"><i class="icon-plus"></i>Inspection</a><?php }?>
                                                <?php if($heads_addFlag && count($apos_details)<1){?><a href="javascript:;" class="btn btn-inverse showModule" data-target="addHead"><i class="icon-plus"></i>Head</a><?php }?>
                                                <?php if($apo_addFlag){?><a href="javascript:;" class="btn btn-warning showModule" data-target="addapo"><i class="icon-plus"></i>APO</a><?php }?>
                                                <?php if($expenses_addFlag){?> <a href="javascript:;" class="btn btn-info showModule" data-target="addExpense"><i class="icon-plus"></i>Expenses</a><?php }?>                                                
                                                <?php if($centers_addFlag){?> <a href="javascript:;" class="btn btn-info showModule" data-target="addCenter"><i class="icon-plus"></i>Centers/Branches</a><?php }?>
                                                <?php if($editFlag){?><a href="projects.php?view=<?php echo $pid;?>" class="btn btn-primary "><i class="icon-edit"></i> Edit </a><?php }?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($updates_addFlag){?>
                                        <div class="module addUpdate" style="display: none;margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Add Update <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addUpdate"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                                <form class="form-horizontal row-fluid" name="addUpdate" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid;?>">
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Update</label>
                                                        <div class="controls">
                                                            <textarea  name="details" class="span12 tip"><?php echo @$_POST['details']?></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Images</label>
                                                        <div class="controls">
                                                            <div class="fileswrapper" style="margin: 20px 0;">
                                                                <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*" required="">
                                                            </div>
                                                            <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="addUpdate" class="btn btn-primary">Add Update</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($heads_addFlag){?>
                                        <div class="module addHead" style="display: none;margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Add Head <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addHead"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                                <?php if($heads_sum[0]['used']<$project["budget"]){?>
                                                    <form class="form-horizontal row-fluid" name="addHead" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid?>">
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
                                                                <input type="number" name="cost" class="span8 tip"  value=""  min="0" max="<?php echo $project["budget"] - $heads_sum[0]['used']; ?>">
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
                                                                <button type="submit" name="addHead" class="btn btn-primary">Create Head</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                <?php }else{?>
                                                    <div>You have allocated whole amount.</div>
                                                <?php }?>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($apo_addFlag){?>
                                        <div class="module addapo" style="display: none;margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Add APO <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addapo"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>                   
                                            <div class="module-body">
                                                <?php if($heads_sum[0]['used']==$project["budget"]){?>
                                                    <form class="form-horizontal row-fluid" name="addapo" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid?>">
                                                        <div class="control-group">
                                                            <label class="control-label" for="basicinput">APO</label>
                                                            <div class="controls">

                                                                <input type="number" name="apo" class="span8 tip" required="" value="" placeholder="2020">
                                                            </div>
                                                        </div>
                                                        <div class="control-group">
                                                            <label class="control-label" for="basicinput">Allocation</label>
                                                            <div class="controls">
                                                                <input type="number" name="allocation" class="span8 tip" required="" value="" min="0" max="<?php echo $project["budget"]?>">
                                                            </div>
                                                        </div>
                                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped  display" width="100%" style="margin: 20px 0">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="5">PC-1 Project Inputs</th>
                                                                    <?php if(count($apos_details)>0){?><th colspan="3">Progress up/to 6/<?php echo $apos_details[0]['apo']+1?></th><?php }?>
                                                                    <th colspan="2">APO Plan Inputs</th>
                                                                </tr>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Head</th>
                                                                    <th>Unit</th>
                                                                    <th>Qty</th>
                                                                    <th>Cost</th>
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
                                                                $query=$crud->getData("select * from heads  where project_id = $pid  and parent_head=0");

                                                                $cnt=1;
                                                                foreach($query as $row)
                                                                {
                                                                ?>
                                                                    <tr>
                                                                        <td><?php echo htmlentities($cnt);?></td>
                                                                        <td colspan="9"><?php echo htmlentities($row['head']);?></td>
                                                                        
                                                                    </tr>    
                                                                    <?php  $subquery=$crud->getData("select * from heads where parent_head = ".$row['id']);
                                                                    $subcnt=1;
                                                                    foreach($subquery as $subrow)
                                                                    {
                                                                    ?>                              
                                                                    <tr>
                                                                        <td><?php echo htmlentities($cnt).'.'.htmlentities($subcnt);?><input type="hidden" name="subheads[]" value="<?php echo $subrow['id']?>"></td>
                                                                        <td><?php echo htmlentities($subrow['head']);?></td>
                                                                        <td><?php echo htmlentities($subrow['unit']);?></td>
                                                                        <td class="allocated_qty"><?php echo htmlentities($subrow['quantity']);?></td>
                                                                        <td class="allocated_price"><?php echo htmlentities($subrow['cost']);?></td>

                                                                        <?php if(count($apos_details)>0){?>
                                                                            <td><input type="number" name="prog_qty[<?php echo $subrow['id']?>]" style="width:50px" min="0" max="<?php echo $subrow['quantity'];?>"></td>
                                                                            <td><input type="number" name="prog_expences[<?php echo $subrow['id']?>]" style="width:100px"  min="0" max="<?php echo $subrow['cost'];?>"></td>
                                                                            <td><input type="text" name="prog_status[<?php echo $subrow['id']?>]" style="width:50px"></td>
                                                                        <?php }?>

                                                                        <td><input type="number" name="apo_qty[<?php echo $subrow['id']?>]" style="width:50px" min="0" max="<?php echo $subrow['quantity'];?>" class="calcQty"></td>
                                                                        <td><input type="number" name="apo_allocation[<?php echo $subrow['id']?>]" style="width:100px" min="0" max="<?php echo $subrow['cost'];?>" class="calcPrice"></td>
                                                                    </tr> 
                                                                    <?php $subcnt++;}?>  
                                                                <?php $cnt++; } ?>
                                                            </tbody>
                                                        </table>

                                                        
                                                        <div class="control-group">
                                                            <div class="controls">
                                                                <button type="submit" name="addapo" class="btn btn-primary">Create APO</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                <?php }else{?>
                                                    <div>Your still have <b>Rs.<?php echo number_format($project["budget"] - $heads_sum[0]['used'],6)?></b> for headwise allocation. Please allocate whole amount before creating APO.</div>
                                                <?php }?>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($apo_editFlag && isset($_GET['editAPO']) && $_GET['editAPO']>0 ){

                                        $apo_id=intval($crud->escape_string($_GET['editAPO']));
                                        $editapos_details=$crud->getData("select * from apo where id = $apo_id");
                                        $editapos_details = $editapos_details[0];
                                        ?>
                                        <div id="editapo" class="module editapo" style="margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Edit APO <span style="float:right"><a href="print_apo.php?apo_id=<?php echo $apo_id?>" target="_blank"><i class="icon-print"></i> Print</a> | <a href="javascript:;" class="hideModule"  data-target="editapo"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                                
                                                <form class="form-horizontal row-fluid" name="editapo" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid?>&editAPO=<?php echo $apo_id?>">
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">APO</label>
                                                        <div class="controls">
                                                            <input type="number" name="apo" class="span8 tip" required="" value="<?php echo $editapos_details['apo']?>" placeholder="2020">
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Allocation</label>
                                                        <div class="controls">
                                                            <input type="number" name="allocation" class="span8 tip" required="" value="<?php echo $editapos_details['allocation']?>" min="0" max="<?php echo $project["budget"]?>">
                                                        </div>
                                                    </div>
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
                                                            $query=$crud->getData("select * from heads  where project_id = $pid  and parent_head=0 order by sort_order asc");

                                                            $cnt=1;
                                                            foreach($query as $row)
                                                            {
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo htmlentities($cnt);?></td>
                                                                    <td colspan="9"><?php echo htmlentities($row['head']);?></td>
                                                                    
                                                                </tr>    
                                                                <?php  $subquery=$crud->getData("select heads.*,apo_heads.prog_qty,apo_heads.prog_expences,apo_heads.prog_status,apo_heads.quantity as apo_qty,apo_heads.revised as apo_revised from heads left join apo_heads on heads.id=apo_heads.head_id where  heads.project_id = $pid  and heads.parent_head = ".$row['id']." and apo_heads.apo_id=".$editapos_details['id']." order by heads.sort_order asc");
                                                                $subcnt=1;
                                                                foreach($subquery as $subrow)
                                                                {
                                                                ?>                              
                                                                <tr>
                                                                    <td><?php echo htmlentities($cnt).'.'.htmlentities($subcnt);?><input type="hidden" name="subheads[]" value="<?php echo $subrow['id']?>"></td>
                                                                    <td><?php echo htmlentities($subrow['head']);?></td>
                                                                    <td><?php echo htmlentities($subrow['unit']);?></td>
                                                                    <td class="allocated_qty"><?php echo htmlentities($subrow['quantity']);?></td>
                                                                    <td class="allocated_price"><?php echo htmlentities($subrow['cost']);?></td>

                                                                    <?php if($editapos_details['progress']==1){?>
                                                                        <td><input type="number" name="prog_qty[<?php echo $subrow['id']?>]" style="width:50px" min="0" max="<?php echo $subrow['quantity'];?>" value="<?php echo $subrow['prog_qty']?>"></td>
                                                                        <td><input type="number" name="prog_expences[<?php echo $subrow['id']?>]" style="width:100px"  min="0" max="<?php echo $subrow['prog_expences'];?>"  value="<?php echo $subrow['prog_qty']?>"></td>
                                                                        <td><input type="text" name="prog_status[<?php echo $subrow['id']?>]" style="width:50px"  value="<?php echo $subrow['prog_status']?>"></td>
                                                                    <?php }?>

                                                                    <td><input type="number" name="apo_qty[<?php echo $subrow['id']?>]" style="width:50px" min="0" max="<?php echo $subrow['quantity'];?>"  value="<?php echo $subrow['apo_qty']?>" class="calcQty"></td>
                                                                    <td><input type="number" name="apo_allocation[<?php echo $subrow['id']?>]" style="width:100px" min="0" max="<?php echo $subrow['cost'];?>"  value="<?php echo $subrow['apo_revised']?>" class="calcPrice"></td>
                                                                </tr> 
                                                                <?php $subcnt++;}?>  
                                                            <?php $cnt++; } ?>
                                                        </tbody>
                                                    </table>

                                                    
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="editapo" class="btn btn-primary">Update APO</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($heads_editFlag && isset($_GET['editHead']) && $_GET['editHead']>0){

                                        $head_id=intval($crud->escape_string($_GET['editHead']));
                                        $edithead_details=$crud->getData("select * from heads where id = $head_id");
                                        $edithead_details = $edithead_details[0];
                                        ?>
                                        <div class="module editHead" style="margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Edit Head <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="editHead"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                            	<form class="form-horizontal row-fluid" name="editHead" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid?>&editHead=<?php echo $head_id?>">
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

                                                                <?php $query=$crud->getData("select * from heads where project_id = {$pid} and parent_head=0");
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
                                                            <input type="number" name="cost" id="cost" class="span8 tip"  value="<?php echo $edithead_details['cost']?>" min="0" max="<?php echo ($project["budget"] - $heads_sum[0]['used']) + $edithead_details['cost']; ?>">
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
                                                            <button type="submit" name="editHead" class="btn btn-primary">Update Head</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($inspections_addFlag){?>
                                        <div class="module addInspection" style="display: none;margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Add Inspection <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addInspection"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                                <form class="form-horizontal row-fluid" name="addInspection" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid;?>">
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Inspection Team</label>
                                                        <div class="controls">
                                                            <input type="text"  name="team" class="span12 tip"><?php echo @$_POST['team']?>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Status</label>
                                                        <div class="controls">
                                                            <select name="status" class="span8 tip">
                                                                <option value="Inprocess">Inprocess</option>
                                                                <option value="Completed">Completed</option>
                                                                <option value="Not Started Yet">Not Started Yet</option>                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Details</label>
                                                        <div class="controls">
                                                            <textarea  name="details" class="span12 tip"><?php echo @$_POST['details']?></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Images</label>
                                                        <div class="controls">
                                                            <div class="fileswrapper" style="margin: 20px 0;">
                                                                <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*" required="">
                                                            </div>
                                                            <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="addInspection" class="btn btn-primary">Add Inspection</button>
                                                        </div>
                                                    </div>
                                                    
                                                </form>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($expenses_addFlag){?>
                                        <div class="module addExpense" style="display: none;margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Add Expense <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addExpense"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                                <form class="form-horizontal row-fluid" name="addExpense" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid;?>">
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
                                                                            $quer = "select heads.*,apo_heads.revised as apo_revised from heads left join apo_heads on heads.id=apo_heads.head_id where  heads.project_id = $pid  and heads.parent_head = ".$head['id']." and apo_heads.apo_id=".$apos_details[0]['id']." order by heads.sort_order asc";  
                                                                            $subquery=$crud->getData($quer);
                                                                            $subcnt=1;
                                                                            foreach($subquery as $subrow)
                                                                            {?>
                                                                                <option value="<?php echo $subrow['id'].':'.$subrow['apo_revised']?>"><?php echo $subrow['head']?></option>
                                                                            <?php }?>
                                                                    </optgroup>
                                                                <?php }?>
                                                            </select>
                                                            <small class="head_allocation"></small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Cost</label>
                                                        <div class="controls">
                                                            <input type="number" name="cost" class="span12 tip" required="" value="<?php echo @$_POST['cost']?>">
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
                                                            <div class="fileswrapper" style="margin: 20px 0;">
                                                                <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                                                            </div>
                                                            <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="addExpense" class="btn btn-primary">Add Expense</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($expenses_editFlag && isset($_GET['editExpense']) && $_GET['editExpense']>0){


                                        $expense_id=intval($crud->escape_string($_GET['editExpense']));
                                        $editexpense_details=$crud->getData("select * from expenses where id = $expense_id");
                                        $editexpense_details = $editexpense_details[0];

                                        ?>
                                        <div class="module editExpense" id="editExpense" style="margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Update Expense <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="editExpense"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                                <form class="form-horizontal row-fluid" name="editExpense" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid;?>&editExpense=<?php echo $expense_id?>">
                                                    
                                                    <div clas<div class="control-group">
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
                                                                            foreach($subquery as $subrow)
                                                                            {?>
                                                                                <option value="<?php echo $subrow['id'].':'.$subrow['apo_revised']?>" <?php if(@$editexpense_details['head_id']==$subrow['id']){echo 'selected="selected"';}?> ><?php echo $subrow['head']?></option>
                                                                            <?php }?>
                                                                    </optgroup>
                                                                <?php }?>
                                                            </select>
                                                            <small class="head_allocation"></small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Cost</label>
                                                        <div class="controls">
                                                            <input type="number" name="cost" class="span12 tip" required="" value="<?php echo @$editexpense_details['cost']?>">
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
                                                        <label class="control-label" for="basicinput">Images</label>
                                                        <div class="controls">
                                                            <?php 
                                                                if(!empty($editexpense_details['images'])){
                                                                    $vimg = explode(',', $editexpense_details['images']);
                                                                    foreach ($vimg as $key => $value) {

                                                                        echo '<label class="checkbox inline"><input type="checkbox" name="rem_img[]" checked="" value="'.htmlentities($value).'" title="Uncheck to remove the image/file."> <a href="'.htmlentities($value).'" target="_blank">View Image/File</a> </label>';
                                                                    }
                                                                }
                                                            ?>                                                            
                                                            <div class="fileswrapper" style="margin: 20px 0;">
                                                                <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                                                            </div>
                                                            <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                                                        </div>
                                                    </div>s="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="editExpense" class="btn btn-primary">Update Expense</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($centers_addFlag){?>
                                        <div class="module addCenter" style="display: none;margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Add Center <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="addCenter"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                                <form class="form-horizontal row-fluid" name="addCenter" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid;?>">
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Title</label>
                                                        <div class="controls">
                                                            <input type="text" name="title" class="span12 tip" value="<?php echo @$_POST['title']?>" required="">
                                                        </div>
                                                    </div>                                                    
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Address</label>
                                                        <div class="controls">
                                                            <input type="text" name="address" class="span12 tip" required="" value="<?php echo @$_POST['address']?>">
                                                        </div>
                                                    </div>       
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Focal Person</label>
                                                        <div class="controls">
                                                            <div class="input-prepend">
                                                                <span class="add-on">Name</span><input name="focal_person" class="span8" type="text" placeholder="prepend" value="<?php echo @$_POST['focal_person']?>">       
                                                            </div>
                                                            <div class="input-prepend">
                                                                <span class="add-on">Email</span><input name="focal_person_email" class="span8" type="text" placeholder="prepend" value="<?php echo @$_POST['focal_person_email']?>">       
                                                            </div>
                                                            <div class="input-prepend">
                                                                <span class="add-on">Contact</span><input name="focal_person_phone" class="span8" type="text" placeholder="prepend" value="<?php echo @$_POST['focal_person_phone']?>">       
                                                            </div>
                                                        </div>
                                                    </div>                                                                                         
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Hardware</label>
                                                        <div class="controls">
                                                            <div class="input-prepend span3">
                                                                <span class="add-on">Workstaions</span><input name="workstaions" class="span6" type="number" placeholder="prepend" value="<?php echo @$_POST['workstaions']?>">       
                                                            </div>
                                                            <div class="input-prepend span3">
                                                                <span class="add-on">Laptops</span><input name="laptops" class="span6" type="number" placeholder="prepend" value="<?php echo @$_POST['laptops']?>">       
                                                            </div>
                                                            <div class="input-prepend span3">
                                                                <span class="add-on">Printers</span><input name="printers" class="span6" type="number" placeholder="prepend" value="<?php echo @$_POST['printers']?>">       
                                                            </div>
                                                            <div class="input-prepend span3">
                                                                <span class="add-on">Scanners</span><input name="scanners" class="span6" type="number" placeholder="prepend" value="<?php echo @$_POST['scanners']?>">       
                                                            </div>
                                                        </div>
                                                    </div>                                 
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Total Staff</label>
                                                        <div class="controls">
                                                            <input type="number" name="total_staff" class="span12 tip" value="<?php echo @$_POST['total_staff']?>">
                                                        </div>
                                                    </div>    
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Description</label>
                                                        <div class="controls">
                                                            <textarea  name="description" class="span12 tip"><?php echo @$_POST['description']?></textarea>
                                                        </div>
                                                    </div>  
                                                    <div class="control-group Pin">
                                                        <label class="control-label" for="basicinput">Pin</label>
                                                        <div class="controls">
                                                            <div id="pinmap_add"  style="height: 450px;"></div>
                                                            <input type="hidden" name="latitude" id="latitude" value="<?php echo @$_POST['latitude']?>">
                                                            <input type="hidden" name="longitude" id="longitude" value="<?php echo @$_POST['longitude']?>">
                                                            <div style="margin-top:20px">
                                                                <a class="btn btn-primary openfiledialog" href="javascript:;">Get Location from Image</a>
                                                                <span class="img_latlong_msg"></span>
                                                                <span style="display: none"><input type="file" name="readinfo" class="readinfo" data-form="add"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Images</label>
                                                        <div class="controls">
                                                            <div class="fileswrapper" style="margin: 20px 0;">
                                                                <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                                                            </div>
                                                            <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="addCenter" class="btn btn-primary">Add Center</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <?php if($centers_editFlag && isset($_GET['editCenter']) && $_GET['editCenter']>0){


                                        $center_id=intval($crud->escape_string($_GET['editCenter']));
                                        $editcenter_details=$crud->getData("select * from centers where id = $center_id");
                                        $editcenter_details = $editcenter_details[0];

                                        ?>
                                        <div class="module editCenter" id="editCenter" style="margin-top: 20px;">
                                            <div class="module-head">
                                                <h3>Update Center <span style="float:right"><a href="javascript:;" class="hideModule"  data-target="editCenter"><i class="icon-remove-circle"></i> Close</a></span></h3>
                                            </div>
                                            <div class="module-body">
                                                <form class="form-horizontal row-fluid" name="editCenter" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid;?>&editCenter=<?php echo $center_id?>">
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Title</label>
                                                        <div class="controls">
                                                            <input type="text" name="title" class="span12 tip" value="<?php echo $editcenter_details['title']?>" required="">
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Address</label>
                                                        <div class="controls">
                                                            <input type="text" name="address" class="span12 tip" required="" value="<?php echo @$editcenter_details['address']?>">
                                                        </div>
                                                    </div>       
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Focal Person</label>
                                                        <div class="controls">
                                                            <div class="input-prepend">
                                                                <span class="add-on">Name</span><input name="focal_person" class="span8" type="text" placeholder="prepend" value="<?php echo @$editcenter_details['focal_person']?>">       
                                                            </div>
                                                            <div class="input-prepend">
                                                                <span class="add-on">Email</span><input name="focal_person_email" class="span8" type="text" placeholder="prepend" value="<?php echo @$editcenter_details['focal_person_email']?>">       
                                                            </div>
                                                            <div class="input-prepend">
                                                                <span class="add-on">Contact</span><input name="focal_person_phone" class="span8" type="text" placeholder="prepend" value="<?php echo @$editcenter_details['focal_person_phone']?>">       
                                                            </div>
                                                        </div>
                                                    </div>                                                                                    
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Hardware</label>
                                                        <div class="controls">
                                                            <div class="input-prepend span3">
                                                                <span class="add-on">Workstaions</span><input name="workstaions" class="span6" type="number" placeholder="prepend" value="<?php echo @$editcenter_details['workstaions']?>">       
                                                            </div>
                                                            <div class="input-prepend span3">
                                                                <span class="add-on">Laptops</span><input name="laptops" class="span6" type="number" placeholder="prepend" value="<?php echo @$editcenter_details['laptops']?>">       
                                                            </div>
                                                            <div class="input-prepend span3">
                                                                <span class="add-on">Printers</span><input name="printers" class="span6" type="number" placeholder="prepend" value="<?php echo @$editcenter_details['printers']?>">       
                                                            </div>
                                                            <div class="input-prepend span3">
                                                                <span class="add-on">Scanners</span><input name="scanners" class="span6" type="number" placeholder="prepend" value="<?php echo @$editcenter_details['scanners']?>">       
                                                            </div>
                                                        </div>
                                                    </div>                            
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Total Staff</label>
                                                        <div class="controls">
                                                            <input type="text" name="total_staff" class="span12 tip" value="<?php echo @$editcenter_details['total_staff']?>">
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Description</label>
                                                        <div class="controls">
                                                            <textarea  name="description" class="span12 tip"><?php echo @$editcenter_details['description']?></textarea>
                                                        </div>
                                                    </div>  
                                                    <div class="control-group Pin">
                                                        <label class="control-label" for="basicinput">Pin</label>
                                                        <div class="controls">
                                                            <div id="pinmap_edit"  style="height: 450px;"></div>
                                                            <input type="hidden" name="latitude" id="elatitude" value="<?php echo @$editcenter_details['latitude']?>">
                                                            <input type="hidden" name="longitude" id="elongitude" value="<?php echo @$editcenter_details['longitude']?>">
                                                            <div style="margin-top:20px">
                                                                <a class="btn btn-primary openfiledialog" href="javascript:;">Get Location from Image</a>
                                                                <span class="img_latlong_msg"></span>
                                                                <span style="display: none"><input type="file" name="readinfo" class="readinfo" data-form="edit"></span>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="control-group">
                                                        <label class="control-label" for="basicinput">Images</label>
                                                        <div class="controls">
                                                            <?php 
                                                                if(!empty($editcenter_details['images'])){
                                                                    $vimg = explode(',', $editcenter_details['images']);
                                                                    foreach ($vimg as $key => $value) {

                                                                        echo '<label class="checkbox inline"><input type="checkbox" name="rem_img[]" checked="" value="'.htmlentities($value).'" title="Uncheck to remove the image/file."> <a href="'.htmlentities($value).'" target="_blank">View Image/File</a> </label>';
                                                                    }
                                                                }
                                                            ?>                                                            
                                                            <div class="fileswrapper" style="margin: 20px 0;">
                                                                <input  name="filesToUpload[]" type="file"  accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">
                                                            </div>
                                                            <a href="javascript:;" class="addmorefile">+ Add Another File</a>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="editCenter" class="btn btn-primary">Update Center</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                                <ul class="profile-tab nav nav-tabs">
                                    <?php $first="";?>
                                    <?php if($updates_viewFlag){?><li class="<?php if(empty($first)){$first='1'; echo 'active';}?>"><a href="#updates" data-toggle="tab">Updates</a></li><?php }?>
                                    <?php if($inspections_viewFlag){?><li class="<?php if(empty($first)){$first='1'; echo 'active';}?>"><a href="#inspections" data-toggle="tab">Inspection</a></li><?php }?>
                                    <?php if($heads_viewFlag){?><li class="<?php if(empty($first)){$first='1'; echo 'active';}?>"><a href="#heads" data-toggle="tab">Heads</a></li><?php }?>
                                    <?php if($apo_viewFlag){?><li class="<?php if(empty($first)){$first='1'; echo 'active';}?>"><a href="#apo" data-toggle="tab">APOs</a></li><?php }?>
                                    <?php if($expenses_viewFlag){?><li class="<?php if(empty($first)){$first='1'; echo 'active';}?>"><a href="#expenses" data-toggle="tab">Expenses</a></li><?php }?>
                                    <?php if($centers_viewFlag){?><li class="<?php if(empty($first)){$first='1'; echo 'active';}?>"><a href="#centers" data-toggle="tab">Branches/Centers</a></li><?php }?>
                                    <?php if($cycles_viewFlag){?><li class="<?php if(empty($first)){$first='1'; echo 'active';}?>"><a href="#cycles" data-toggle="tab">Project Cycles</a></li><?php }?>
                                </ul>
                                <div class="profile-tab-content tab-content">
                                    <?php $first="";?>
                                    <?php if($updates_viewFlag){?>
                                        <div class="tab-pane fade <?php if(empty($first)){$first='1'; echo 'active in';}?>" id="updates">
                                            <?php 
                                            if($project_updates != false && count($project_updates)>0){?>
                                                <div class="stream-list">
                                                    <?php foreach($project_updates as $row){
                                                        $user_image=$row['admin_image'];
                                                        if(empty($user_image)){
                                                             $user_image='images/user.png';
                                                        }

                                                        $update_images = explode(',',$row['images']);
                                                    ?>
                                                        <div class="media stream">
                                                            <a href="#" class="media-avatar medium pull-left">
                                                                <img src="<?php echo $user_image;?>">
                                                            </a>
                                                            <div class="media-body">
                                                                <div class="stream-headline">
                                                                    <h5 class="stream-author">
                                                                        <?php echo $row['display_name'];?> <small><b class="feed-date"  data-toggle="tooltip" title="<?php echo $row["added_date"];?>"><?php echo date("d M, Y", strtotime($row["added_date"]));?></b></small>
                                                                    </h5>
                                                                    <div class="stream-text"><?php echo $row['details'];?></div>
                                                                    
                                                                    <?php 
                                                                    if(count($update_images)>0){
                                                                        foreach ($update_images as $key => $value) {
                                                                            if(!empty(trim($value))){
                                                                                $ext = strtolower($value);
                                                                                if(endsWith($ext,'.jpg') || endsWith($ext,'.jpeg')  || endsWith($ext,'.png')  || endsWith($ext,'.gif') ||  endsWith($ext,'.jfif')){                                                                            ?>
                                                                                    <div class="stream-attachment photo">
                                                                                        <div class="responsive-photo">
                                                                                            <a href="<?php echo $value;?>" target="_blank"><img src="<?php echo $value;?>" alt=""></a>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php  } else if(endsWith($ext,'.mp4') || endsWith($ext,'.mpeg') || endsWith($ext,'.webm') || endsWith($ext,'.avi') || endsWith($ext,'.flv') || endsWith($ext,'.wmv')){?>
                                                                                    <div class="stream-attachment video">
                                                                                        <div class="responsive-video">
                                                                                            <video width="100%" height="auto" controls>
                                                                                              <source src="<?php echo trim($value);?>" type="video/mp4">
                                                                                            Your browser does not support the video tag.
                                                                                            </video> 
                                                                                        </div>
                                                                                    </div>
                                                                                <?php }else{?>
                                                                                    <a target="_blank" href="<?php echo trim($value);?>">View File</a> 
                                                                                <?php }
                                                                            }
                                                                        }
                                                                    }?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                </div>
                                                <!--/.stream-list-->
                                            <?php }else{?><div>No Update Found<br><br></div><?php }?>
                                        </div>
                                    <?php }?>
                                    <?php if($inspections_viewFlag){?>
                                        <div class="tab-pane fade <?php if(empty($first)){$first='1'; echo 'active in';}?>" id="inspections"><?php 
                                           
                                            if($inspections_details != false && count($inspections_details)>0){?>
                                                <div class="stream-list">
                                                    <?php foreach($inspections_details as $row){
                                                        $user_image=$row['admin_image'];
                                                        if(empty($user_image)){
                                                             $user_image='images/user.png';
                                                        }

                                                        $inspection_images = explode(',',$row['images']);
                                                        ?>
                                                        <div class="media stream">
                                                            <a href="#" class="media-avatar medium pull-left">
                                                                <img src="<?php echo $user_image;?>">
                                                            </a>
                                                            <div class="media-body">
                                                                <div class="stream-headline">
                                                                    <h5 class="stream-author">
                                                                        <?php echo $row['display_name'];?> <small><b class="feed-date"  data-toggle="tooltip" title="<?php echo $row["added_date"];?>"><?php echo date("d M, Y", strtotime($row["added_date"]));?></b></small>
                                                                    </h5>
                                                                    <div class="stream-text"><b>Inspection Team: <?php echo $row['team'];?></b><span style="margin-left:20px" class="btn btn-small btn-inverse"> <?php echo $row['status'];?> </span></div>
                                                                    <div class="stream-text"><?php echo $row['details'];?></div>
                                                                    
                                                                    <?php 
                                                                    if(count($inspection_images)>0){
                                                                        foreach ($inspection_images as $key => $value) {
                                                                            if(!empty(trim($value))){
                                                                                $ext = strtolower($value);
                                                                                if(endsWith($ext,'.jpg') || endsWith($ext,'.jpeg')  || endsWith($ext,'.png')  || endsWith($ext,'.gif') ||  endsWith($ext,'.jfif')){                                                                            ?>
                                                                                    <div class="stream-attachment photo">
                                                                                        <div class="responsive-photo">
                                                                                            <a href="<?php echo $value;?>" target="_blank"><img src="<?php echo $value;?>" alt=""></a>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php  } else if(endsWith($ext,'.mp4') || endsWith($ext,'.mpeg') || endsWith($ext,'.webm') || endsWith($ext,'.avi') || endsWith($ext,'.flv') || endsWith($ext,'.wmv')){?>
                                                                                    <div class="stream-attachment video">
                                                                                        <div class="responsive-video">
                                                                                            <video width="100%" height="auto" controls>
                                                                                              <source src="<?php echo trim($value);?>" type="video/mp4">
                                                                                            Your browser does not support the video tag.
                                                                                            </video> 
                                                                                        </div>
                                                                                    </div>
                                                                                <?php }else{?>
                                                                                    <a target="_blank" href="<?php echo trim($value);?>">View File</a> 
                                                                                <?php }
                                                                            }
                                                                        }
                                                                    }?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                </div>
                                            <?php }else{?><div>No Inspection Found<br><br></div><?php }?>
                                        </div>
                                    <?php }?>
                                    <?php if($heads_viewFlag){?>
                                        <div class="tab-pane fade <?php if(empty($first)){$first='1'; echo 'active in';}?>" id="heads">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>code</th>
                                                        <th>Budget Input Description</th>
                                                        <th>Unit</th>
                                                        <th>Qty</th>
                                                        <th>Cost</th>
                                                        <?php if(count($apos_details)<1){?><th>Action</th><?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 

                                                    $cnt=1;
                                                    $grand_total=0;
                                                    foreach($heads_details as $row)
                                                    {  $subquery=$crud->getData("select * from heads where parent_head = ".$row['id'].'  order by sort_order asc');
                                                        ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt);?></td>
                                                            <td rowspan="<?php echo count($subquery)+1 ?>"><?php echo htmlentities($row['code']);?></td>
                                                             <td colspan="4"  id="ph_head_<?php echo htmlentities($row['id'])?>" style="background: #ddd"><?php echo htmlentities($row['head']);?></td>
                                                             
                                                            <?php if(count($apos_details)<1){?>
                                                                <td>
                                                                    <?php if($heads_editFlag){?><a href="project_details.php?view=<?php echo $pid?>&editHead=<?php echo htmlentities($row['id'])?>#editHead"><i class="icon-edit"></i></a><?php }?>
                                                                    <?php if($heads_deleteFlag){?><a href="project_details.php?view=<?php echo $pid?>&deleteHead=<?php echo htmlentities($row['id'])?>" onclick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a><?php }?>
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
                                                            <td id="ph_cost_<?php echo htmlentities($subrow['id'])?>"><?php echo htmlentities($subrow['cost']);?></td>
                                                            <?php if(count($apos_details)<1){?>
                                                                <td>
                                                                    <?php if($heads_editFlag){?><a href="project_details.php?view=<?php echo $pid?>&editHead=<?php echo htmlentities($subrow['id'])?>#editHead"><i class="icon-edit"></i></a><?php }?>
                                                                    <?php if($heads_deleteFlag){?><a href="project_details.php?view=<?php echo $pid?>&deleteHead=<?php echo htmlentities($subrow['id'])?>" onclick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a><?php }?>
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
                                        </div> 
                                    <?php }?>
                                    <?php if($apo_viewFlag){?>
                                        <div class="tab-pane fade <?php if(empty($first)){$first='1'; echo 'active in';}?>" id="apo">
                                            <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped  display" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>APO</th>
                                                        <th>Allocation</th>
                                                        <!-- <th>Remaining</th> -->
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php 
                                                    
                                                    $cnt=1;
                                                    foreach($apos_details as $row)
                                                    {
                                                    ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt);?></td>
                                                            <td><?php echo htmlentities($row['apo']).'-'.(($row['apo']+1) - 2000);?></td>
                                                            <td><?php echo number_format($row['allocation'],2);?></td>
                                                            <!-- <td><?php echo  number_format($project["budget"]-$row['allocation'],2)?></td> -->
                                                            <td>
                                                                <?php if($apo_editFlag){?><a href="project_details.php?view=<?php echo $pid?>&editAPO=<?php echo htmlentities($row['id'])?>#editapo"><i class="icon-edit"></i></a><?php }?>
                                                                 <a href="print_apo.php?apo_id=<?php echo $row['id'] ?>" target="_blank"><i class="icon-print"></i></a> 
                                                                <?php if($apo_deleteFlag){?><a href="project_details.php?view=<?php echo $pid?>&deleteAPO=<?php echo htmlentities($row['id'])?>" onclick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a><?php }?>
                                                            </td>
                                                        </tr>    
                                                    <?php $cnt++; } ?>
                                                </tbody>
                                            </table>
                                        </div> 
                                    <?php }?>
                                    <?php if($expenses_viewFlag){?>
                                        <div class="tab-pane fade <?php if(empty($first)){$first='1'; echo 'active in';}?>" id="expenses"><?php 
                                            if($expenses_details != false && count($expenses_details)>0){?> 
                                                <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="2%">#</th>
                                                            <th width="55%">Title</th>
                                                            <th width="3%">Expense</th>
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
                                                                    <?php if($apo_editFlag){?>
                                                                        <a href="project_details.php?view=<?php echo $pid?>&editExpense=<?php echo htmlentities($row['id'])?>#editExpense"><?php echo htmlentities($row['title']);?></a>
                                                                    <?php }else{?>
                                                                        <?php echo htmlentities($row['title']);?>
                                                                    <?php }?>
                                                                </td>
                                                                <td><?php echo htmlentities($row['cost']);?></td>
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
                                            <?php }else{?><div>No Expense Found<br><br></div><?php }?>
                                        </div> 
                                    <?php }?>
                                    <?php if($centers_viewFlag){?>
                                        <div class="tab-pane fade <?php if(empty($first)){$first='1'; echo 'active in';}?>" id="centers"><?php 
                                            if($centers_details != false && count($centers_details)>0){?> 
                                                <div class="alert">
                                                    <strong><?php echo count($centers_details); ?></strong> Branches/Centers Found.<span style="float:right"><a href="centers-onmap.php?project_id=<?php echo $pid?>" target="_blank">View All On Map</a></span>
                                                </div>
                                                <div class="stream-list">
                                                    <?php foreach($centers_details as $row){
                                                        $center_dimg='';
                                                        $center_imgs=array();

                                                        $center_images=$row['images'];
                                                        if(empty($center_images)){
                                                             $center_dimg='images/center.png';
                                                        }
                                                        else
                                                        {
                                                            $center_images = explode(',',$center_images);
                                                            foreach ($center_images as  $img) {
                                                                if(empty($center_dimg))
                                                                     $center_dimg=$img;
                                                                else
                                                                     $center_imgs[]=$img;
                                                            }
                                                        }

                                                        ?>
                                                        <div class="media stream">
                                                            <a href="<?php echo $center_dimg;?>" class="media-avatar medium pull-left" target="_blank">
                                                                <img src="<?php echo $center_dimg;?>">
                                                            </a>

                                                            <div class="media-body">
                                                                <div class="stream-headline">
                                                                    <h5 class="stream-author">
                                                                        <a href="project_details.php?view=<?php echo $pid?>&editCenter=<?php echo $row["id"];?>#editCenter"><?php echo $row['title'];?></a> <small><?php echo $row["address"];?></small>
                                                                    </h5>

                                                                    <div class="stream-text"><p><b>Incharge:</b> <?php echo $row['focal_person'];?><?php 
                                                                        if(!empty($row['focal_person_phone']))
                                                                        {
                                                                            echo '<a href="tel:'.$row['focal_person_phone'].'" style="margin-left:20px"><i class="icon-phone-sign"></i> '.$row['focal_person_phone'].'</a>';
                                                                        }
                                                                        if(!empty($row['focal_person_email']))
                                                                        {
                                                                            echo '<a href="mailto:'.$row['focal_person_email'].'" style="margin-left:20px"><i class="icon-envelope-alt"></i> '.$row['focal_person_email'].'</a>';
                                                                        }
                                                                        
                                                                        ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="stream-text"><p><?php echo $row['description'];?></p></div>
                                                                    <div class="btn-controls">
                                                                        <div class="btn-box-row row-fluid">
                                                                            <a href="javascript:;" class="btn-box small span2" title="Staff" style="background-color: #f9f9f9;">
                                                                                <i class="icon-group"></i>
                                                                                <b><?php echo (!empty($row['total_staff']))?$row['total_staff']:'0';?></b>
                                                                                <p class="text-muted" style="margin-bottom: 0px;">Total Staff</p>
                                                                            </a>
                                                                            <a href="javascript:;" class="btn-box small span2" title="Workstations" style="background-color: #f9f9f9;">
                                                                                <i class="icon-desktop"></i>
                                                                                <b><?php echo (!empty($row['workstaions']))?$row['workstaions']:'0';?></b>
                                                                                <p class="text-muted" style="margin-bottom: 0px;">Workstations</p>
                                                                            </a>
                                                                            <a href="javascript:;" class="btn-box small span2" title="Laptops" style="background-color: #f9f9f9;">
                                                                                <i class="icon-laptop"></i>
                                                                                <b><?php echo (!empty($row['laptops']))?$row['laptops']:'0';?></b>
                                                                                <p class="text-muted" style="margin-bottom: 0px;">Laptops</p>
                                                                            </a>
                                                                            <a href="javascript:;" class="btn-box small span2" title="Printer" style="background-color: #f9f9f9;">
                                                                                <i class="icon-print"></i>
                                                                                <b><?php echo (!empty($row['printers']))?$row['printers']:'0';?></b>
                                                                                <p class="text-muted" style="margin-bottom: 0px;">Printers</p>
                                                                            </a>
                                                                            <a href="javascript:;" class="btn-box small span2" title="Scanner" style="background-color: #f9f9f9;">
                                                                                <i class="icon-camera"></i>
                                                                                <b><?php echo (!empty($row['scanners']))?$row['scanners']:'0';?></b>
                                                                                <p class="text-muted" style="margin-bottom: 0px;">Scanner</p>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <?php 
                                                                    if(count($center_imgs)>0){?>
                                                                        <button class="accordion">Images</button>
                                                                        <div class="panel">
                                                                        <?php foreach ($center_imgs as $value) {
                                                                            if(!empty(trim($value))){
                                                                                $ext = strtolower($value);
                                                                                if(endsWith($ext,'.jpg') || endsWith($ext,'.jpeg')  || endsWith($ext,'.png')  || endsWith($ext,'.gif') ||  endsWith($ext,'.jfif')){?>
                                                                                    <div class="stream-attachment photo" style="width: 47%;">
                                                                                        <div class="responsive-photo">
                                                                                            <a href="<?php echo $value;?>" target="_blank"><img src="<?php echo $value;?>" alt=""></a>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php  } else if(endsWith($ext,'.mp4') || endsWith($ext,'.mpeg') || endsWith($ext,'.webm') || endsWith($ext,'.avi') || endsWith($ext,'.flv') || endsWith($ext,'.wmv')){?>
                                                                                    <div class="stream-attachment video"  style="width: 47%;">
                                                                                        <div class="responsive-video">
                                                                                            <video width="100%" height="auto" controls>
                                                                                              <source src="<?php echo trim($value);?>" type="video/mp4">
                                                                                            Your browser does not support the video tag.
                                                                                            </video> 
                                                                                        </div>
                                                                                    </div>
                                                                                <?php }else{?>
                                                                                    <a target="_blank" href="<?php echo trim($value);?>">View File</a> 
                                                                                <?php }
                                                                            }
                                                                        }?>
                                                                        </div>
                                                                    <?php }?>
                                                                    

                                                                    <button class="accordion">View On map</button>
                                                                    <div class="panel">
                                                                      <div class="center_map" style="height: 400px" id="center_map_<?php echo $row['id'];?>" data-lat="<?php echo $row['latitude'];?>" data-long="<?php echo $row['longitude'];?>"></div>
                                                                    </div>
                                                                    
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }?>
                                                </div>
                                            <?php }else{?><div>No Branches/Centers Found<br><br></div><?php }?>
                                        </div> 
                                    <?php }?>
                                    <?php if($cycles_viewFlag){?>
                                        <div class="tab-pane fade <?php if(empty($first)){$first='1'; echo 'active in';}?>" id="cycles">
                                        	<?php foreach($default_cycles_details as $row){?>
	                                        	<button class="accordion"><?php echo $row['title'];?></button>
												<div class="panel">
													<form class="form-horizontal row-fluid" name="cycles" method="post"  enctype="multipart/form-data" action="project_details.php?view=<?php echo $pid;?>">
														
														<?php if(isset($my_cycles[$row['title']])){
															$blocks = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $my_cycles[$row['title']]['defaults']),true);
															//var_dump($blocks);
															?>
															<ol>
																<?php echo cycle_levels_simple($blocks)?>
															</ol>
															<?php if($cycles_editFlag){?>


                                                                <a href="javascript:;" class="btn btn-primary showModule" data-target="editCycle-<?php echo $row['title']?>" style="margin:20px"><i class="icon-edit"></i> Edit <?php echo $row['title']?></a>
                                                                <a href="project_cycles_print.php?pid=<?php echo $pid?>&cycle=<?php echo $row['title']?>" class="btn btn-success" target="_blank" style="margin:20px"><i class="icon-print"></i> Print <?php echo $row['title']?></a>
																<div class="editCycle-<?php echo $row['title']?>" style="display: none;margin-top: 20px;">
																	<ol>
																		<?php echo cycle_levels($blocks,$row['title'])?>
																	</ol>
																	<input type="hidden" name="cycle_id" value="<?php echo $my_cycles[$row['title']]['id']?>">
																	<input type="hidden" name="title" value="<?php echo $my_cycles[$row['title']]['title']?>">

																	<input type="submit" name="updateCycle" value="Update <?php echo $my_cycles[$row['title']]['title']?>" class="btn btn-primary" style="margin:20px">
																	<input type="button" name="closeedit" value="Cancle" class="btn btn-danger hideModule"  data-target="editCycle-<?php echo $row['title']?>" style="margin:20px">
																</div>
															<?php }?>
														<?php }else{
															if($cycles_addFlag)
															{
																$blocks = json_decode($row['defaults'],true);?>
																<ol>
																	<?php echo cycle_levels($blocks,$row['title'])?>
																</ol>

																<input type="submit" name="addCycle" value="Add <?php echo $row['title']?>" class="btn btn-primary" style="margin:20px">
																<input type="hidden" name="title" value="<?php echo $row['title']?>">
															<?php }else{ echo '<center>No '.$row['title'].' added yet & you dont have access to it.</center>';}?>	
														<?php }?>														
													</form>
												</div>
											<?php }?>                                            
                                        </div> 
                                    <?php }?>
                                </div>
                            </div>
						</div>	
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

    <?php include('include/footer.php');?>

    <?php include_once('include/foot.php');?>
	
	
 
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.21/datatables.min.js"></script>
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script> -->
	
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzj-NP-Vj8dqq7X9V9iO3-WY89kDquPOI&libraries=places&callback=initMap"></script>

    <script src="scripts/exif.min.js" type="text/javascript"></script>
    <script  type="text/javascript">    
	    var acc = document.getElementsByClassName("accordion");
	    var i;

	    for (i = 0; i < acc.length; i++) {
	      acc[i].addEventListener("click", function() {
	        /* Toggle between adding and removing the "active" class,
	        to highlight the button that controls the panel */
	        this.classList.toggle("active");

	        /* Toggle between hiding and showing the active panel */
	        var panel = this.nextElementSibling;
	        if (panel.style.display === "block") {
	          panel.style.display = "none";
	        } else {
	          panel.style.display = "block";
	        }
	      });
	    }  

        var uluru, map, marker,uluru2, map2, marker2;
        function initMap() {
          // The location of Uluru
          // The map, centered at Uluru
          <?php if(isset($_GET['editCenter']) && $_GET['editCenter']>0){

            ?>
                uluru = {lat: <?php echo $editcenter_details['latitude']?>, lng: <?php echo $editcenter_details['longitude']?>};
                map = new google.maps.Map(document.getElementById('pinmap_edit'), {zoom: 16, center: uluru,mapTypeId: 'satellite'});
                marker = new google.maps.Marker({position: uluru, map: map, draggable: true});

                // add an event "onDrag"
                google.maps.event.addListener(marker, 'dragend', function() {
                    document.getElementById("elatitude").value = marker.getPosition().lat();
                    document.getElementById("elongitude").value = marker.getPosition().lng();
                });
            <?php }?>

            uluru2 = {lat: <?php echo @$_POST['latitude']?>, lng: <?php echo @$_POST['longitude']?>};
            map2 = new google.maps.Map(document.getElementById('pinmap_add'), {zoom: 16, center: uluru2,mapTypeId: 'satellite'});
            marker2 = new google.maps.Marker({position: uluru2, map: map2, draggable: true});

            document.getElementById("latitude").value = marker2.getPosition().lat();
            document.getElementById("longitude").value = marker2.getPosition().lng();

            // add an event "onDrag"
            google.maps.event.addListener(marker2, 'dragend', function() {
                    document.getElementById("latitude").value = marker2.getPosition().lat();
                    document.getElementById("longitude").value = marker2.getPosition().lng();
            });



            $(".center_map").each(function(){
                
                uluru3 = {lat: $(this).data('lat'), lng: $(this).data('long')};
                map3 = new google.maps.Map(document.getElementById($(this).attr('id')), {zoom: 16, center: uluru3,mapTypeId: 'satellite'});
                marker3 = new google.maps.Marker({position: uluru3, map: map3});

            });
        }
        /*function readURL(input) {
            //reading the Uploading file
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.readAsDataURL(input.files[0]);
            }
        }*/
        function generate_lat_lang(imageData='',file_wrapper){
            //geting cordinates of latitude
            var latDegree = imageData.exifdata.GPSLatitude[0].numerator;
            var latMinute = imageData.exifdata.GPSLatitude[1].numerator;
            var latSecond = imageData.exifdata.GPSLatitude[2].numerator/imageData.exifdata.GPSLatitude[2].denominator;
            

            //geting cordinates of longitude
            var lonDegree = imageData.exifdata.GPSLongitude[0].numerator;
            var lonMinute = imageData.exifdata.GPSLongitude[1].numerator;
            var lonSecond = imageData.exifdata.GPSLongitude[2].numerator/imageData.exifdata.GPSLongitude[2].denominator;
                            
            if(file_wrapper.find('.readinfo').data('form') == 'add')
            {
                document.getElementById("latitude").value = (latDegree + (latMinute/60) + (latSecond/3600)).toFixed(8);
                document.getElementById("longitude").value = (lonDegree + (lonMinute/60) + (lonSecond/3600)).toFixed(8);   

                var position =  new google.maps.LatLng(document.getElementById("latitude").value, document.getElementById("longitude").value);

                marker2.setPosition(position); 
                map2.setCenter(marker2.getPosition());  
            }
            else
            {
                document.getElementById("elatitude").value = (latDegree + (latMinute/60) + (latSecond/3600)).toFixed(8);
                document.getElementById("elongitude").value = (lonDegree + (lonMinute/60) + (lonSecond/3600)).toFixed(8);   

                var position =  new google.maps.LatLng(document.getElementById("elatitude").value, document.getElementById("elongitude").value);
                
                marker.setPosition(position); 
                map.setCenter(marker.getPosition());      
            }
            file_wrapper.find(".img_latlong_msg").html('Position found, and updated.');

        }
		$(document).ready(function() {
            $('.datatable-1').dataTable( {
               /* initComplete: function () {
                    this.api().columns().every( function () {
                        var column = this;
                        var select = $('<select style="width:100%"><option value=""></option></select>')
                            .appendTo( $(column.header()) )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
         
                                column
                                    .search( val ? '^'+val+'$' : '', true, false )
                                    .draw();
                            } );
         
                        column.data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        } );
                    } );
                },*/
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
 
                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
 
                    // Total over all pages
                    total = api
                        .column( 2 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
         
                    // Total over this page
                    pageTotal = api
                        .column( 2, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    

                    $( api.column( 0 ).footer() ).html(
                        'Page Total: '+pageTotal+'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Grand Total: '+ total
                    );
                    
                }
            });

			/*$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
            */

			//$(".multi-select").select2();

            $('.openfiledialog').click(function(){
                $(this).parents('.Pin').find(".readinfo").trigger("click");
            });

            $(".readinfo").change(function(el) {
                //readURL(this)
                var file_wrapper = $(this).parents('.Pin');

                file_wrapper.find(".img_latlong_msg").html('There is no geo location information with this image. Please upload GeoTagged Image');

                EXIF.getData(el.target.files[0], function() {
                 
                   EXIF.getData(this,()=>{
                        //console.log(this)
                        if(Object.keys(this.exifdata).length > 0){
                            generate_lat_lang(this,file_wrapper);
                        }
                    });
                });
            });

            var updates=false, inspections=false, heads=false, apo=false, expenses=false, centers=false, cycles=false;

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
              var target = $(e.target).attr("href") // activated tab
              //alert(target);
            });
		});

		$(function(){
		    $('.addmorefile').click(function(){
		      $(this).parents(".controls").find('.fileswrapper').append('<input  name="filesToUpload[]" type="file" accept=".mp4|.pdf|.xls|.xlsx|.doc|.docx|image/*">');
		    });

		    $(".expense_status").change(function(){
                if($(this).val()=='Booked')
                {
                    $(".release_date").hide();
                }
                else
                {
                    $(".release_date").show();
                }
            });

            $(".head_expense").change(function(){
                if($(this).val()=='')
                {
                    $(this).parent().find(".head_allocation").html("");
                }
                else
                {
                    var res = $(this).val().split(":");
                    console.log(res);
                    $(this).parent().find(".head_allocation").html("<b>Total Allocation: </b>"+res[1]);
                }
            });
            $(".calcQty").blur(function(){
                $enteredQty = $(this).val();
                if($enteredQty>0)
                {
                    $qty = parseInt($(this).parents('tr').find(".allocated_qty").text());
                    $price = parseInt($(this).parents('tr').find(".allocated_price").text());

                    if($qty>0 && $price>0)
                    {
                        $qtyperitem = $price / $qty;
                        $(this).parents('tr').find(".calcPrice").val($qtyperitem * $enteredQty);
                    }
                    else
                    {
                        $(this).parents('tr').find(".calcPrice").val(0);
                    }
                }
                else
                {
                    $(this).parents('tr').find(".calcPrice").val(0);
                }
               
                


                
            });
		    $(".headchange").change(function(){
		    	if($(this).val()!='0')
		    	{
		    		$(this).parents('form').find(".h_with_root").hide();
                    $(this).parents('form').find(".h_with_child").show();
		    	}
		    	else
		    	{
		    		$(this).parents('form').find(".h_with_root").show();
                    $(this).parents('form').find(".h_with_child").hide();
		    	}

		    });
		});
	</script>
	<script type="text/javascript">
		$(function(){
			tinymce.init({
			    selector:'.tinyeditor',
			    plugins: 'print preview fullpage paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons autoresize',
			    menubar: false,
			    autoresize_bottom_margin: 20,
			    min_height:100,
			    //toolbar_sticky: true,
			    //toolbar_drawer: 'sliding',
			    
			    images_upload_url: 'upload-editor-images.php',
			    images_upload_base_path: 'https://ajk.gov.pk/ppms/',
			    images_upload_credentials: true,

			    content_css : "/ppms/css/editor.css",
			    toolbar: 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist  | forecolor backcolor | code preview fullscreen |  fontselect fontsizeselect formatselect | removeformat pagebreak | charmap emoticons |  insertfile image media template link anchor codesample | ltr rtl ',
		   		/* images_upload_handler: function (blobInfo, success, failure) {
			      var xhr, formData;
			      xhr = new XMLHttpRequest();
			      xhr.withCredentials = false;
			      xhr.open('POST', 'postAcceptor.php');
			      xhr.onload = function() {
			        var json;

			        if (xhr.status != 200) {
			        failure('HTTP Error: ' + xhr.status);
			        return;
			        }
			        json = JSON.parse(xhr.responseText);

			        if (!json || typeof json.location != 'string') {
			        failure('Invalid JSON: ' + xhr.responseText);
			        return;
			        }
			        success(json.location);
			      };
			      formData = new FormData();
			      formData.append('file', blobInfo.blob(), fileName(blobInfo));
			      xhr.send(formData);
			    },*/
			});
		});
	</script>
</body>
</html>