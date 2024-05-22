<?php 
$module_name = 'inspections';
include('classes/config.php');
authenticate_ajax();

$editFlag=authorizeAccess($module_name,'edit');
$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['editInspections']) && isset($_GET['updateInspection']) && $_GET['updateInspection']>0)
{
    if($editFlag)
    {
		$inspection_id=intval($crud->escape_string($_GET['updateInspection']));
        $details=$crud->escape_string($_POST['details']);
        $team=$crud->escape_string($_POST['team']);
        $status=$crud->escape_string($_POST['status']);
        //$completed_percentage=$crud->escape_string($_POST['completed_percentage']);
        $msg = $validation->check_empty($_POST, array(array('details','Inspection Details'),array('team','Inspection Team')));


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
                                
                            $imagefile =$folder_name."/inspection_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
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


            $sql="UPDATE inspections set details='$details',team='$team',status='$status',images='$imgstr',updated_by='".$_SESSION['id']."'  where id=".$inspection_id;
            
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                
                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project inspection updated !!
                                    </div>';


                $crud->log('Inspection('.$result.') updated',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Unable to edit inspection.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  You do not have access to edit inspection.
                                </div>';
    }
}
echo json_encode($return);