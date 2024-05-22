<?php 
$module_name = 'expenses';
include('classes/config.php');
authenticate_ajax();

$editFlag=authorizeAccess($module_name,'edit');
$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['editExpenses']) && isset($_GET['updateExpenses']) && $_GET['updateExpenses']>0)
{
    if($editFlag)
    {
        $exp_id=intval($crud->escape_string($_GET['updateExpenses']));

        $title=$crud->escape_string($_POST['title']);
        $cost=$crud->escape_string($_POST['cost']);
        $head_id=$crud->escape_string($_POST['head_id']);
        $details=$crud->escape_string($_POST['details']);

        $status=$crud->escape_string($_POST['status']);
        $release_date=$crud->escape_string($_POST['release_date']);
        
        
        $prev_images=$crud->escape_string($_POST['prev_images']);  
        if(isset($_POST['rem_img']))
        {
            foreach($_POST['rem_img'] as $remove_img)
            {
                @unlink($remove_img);
                $prev_images = str_replace($remove_img.',', '', $prev_images);
                $prev_images = str_replace(','.$remove_img, '', $prev_images);
                $prev_images = str_replace($remove_img, '', $prev_images);
            }
        }


        if($status == 'Released')
        {
            $msg = $validation->check_empty($_POST, array(array('title','Expense Title'),array('cost','Cost'),array('head_id','Expense Head'),array('release_date','Expense Release Date')));
            
        }
        else
        {
           $msg = $validation->check_empty($_POST, array(array('title','Expense Title'),array('cost','Cost'),array('head_id','Expense Head'))); 
        }

        
        if($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Please correct the following errors:<br>'.$msg.'
                            </div>';
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
                        @$mimetype = mime_content_type($file['tmp_name']);


                        $expensions= array("jpg","jpeg","png","mp3","mp4","pdf","doc","docx","xls","xlsx","ppt","pptx");
                        //array("jpeg","jpg","png");
                        $mimes = array('image/jpeg', 'image/png','audio/mpeg','video/mp4','audio/mp4','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation');


                        if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes)) //,"mp4","mp3","webm"
                        {
                            $folder_name='uploads/project_images';
                                
                            $imagefile =$folder_name."/expense_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                            if(move_uploaded_file($file["tmp_name"],$imagefile))
                            {
                                $imagefiles[] = $imagefile;
                            }
                        }
                    }

                    if(empty($prev_images))
                        $imgstr=implode(',', $imagefiles);
                    else
                        $imgstr=$prev_images.",".implode(',', $imagefiles);
                }
                else
                {
                    $imgstr=$prev_images;
                }
            }
            else
            {
                $imgstr=$prev_images;
            }

            $head_id = explode(':', $head_id);
            $head_id = $head_id[0];

            $updb='';
            if(!empty($release_date))
                $updb = ",release_date='$release_date'";

            $sql="UPDATE expenses set title='$title',cost='$cost',details='$details',status='$status',head_id='$head_id',images='$imgstr',updated_by='".$_SESSION['id']."' ".$updb." where id=".$exp_id;
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                
                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project APO updated !!
                                    </div>';


                $crud->log('APO('.$result.') updated',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Unable to edit APO.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  You do not have access to edit APO.
                                </div>';
    }
}
echo json_encode($return);