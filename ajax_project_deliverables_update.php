<?php 
$module_name = 'deliverables';
include('classes/config.php');
authenticate_ajax();

$editFlag=authorizeAccess($module_name,'edit');
$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['editDeliverable']) && isset($_GET['updateDeliverable']) && $_GET['updateDeliverable']>0)
{
    if($editFlag)
    {
		$deliverable_id=intval($crud->escape_string($_GET['updateDeliverable']));

        $title=$crud->escape_string($_POST['title']);
        $weight=$crud->escape_string($_POST['weight']);
        $status=$crud->escape_string($_POST['status']);
        $details=$crud->escape_string($_POST['details']);

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
        

        $editDeliverable_details=$crud->getData("select * from deliverables where id = $deliverable_id");
        $editDeliverable_details = $editDeliverable_details[0];

        $editDeliverable_weight=$crud->getData("select SUM(weight) as total from deliverables where project_id = ".$editDeliverable_details['project_id']." and id<>$deliverable_id");
        $editDeliverable_weight = $editDeliverable_weight[0];

        $wmsg = null;
        if(($editDeliverable_weight['total']+$weight)>100)
        {
            $wmsg = 'Deliverable Weight is not valid.';
        }

        $msg = $validation->check_empty($_POST, array(array('title','Deliverable Title'),array('weight','Deliverable Weight')));

        if($msg != null) {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Please correct the following errors:<br>'.$msg.'
                            </div>';
        } 
        else if($wmsg != null)
        {
            $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                              Please correct the following errors:<br>'.$wmsg.'
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
                                
                            $imagefile =$folder_name."/deliverable_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
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


            $sql="UPDATE deliverables set title='$title',weight='$weight',status='$status',details='$details',images='$imgstr',updated_by='".$_SESSION['id']."'  where id=".$deliverable_id;
            
            
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                
                $project_complete=$crud->getData("select SUM((status/100)*weight) as total from deliverables where project_id = ".$editDeliverable_details['project_id']);
                $project_complete = $project_complete[0];

                $crud->execute("UPDATE projects set completed_percentage='".$project_complete['total']."' where id=".$editDeliverable_details['project_id']);

                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project deliverable updated !!
                                    </div>';


                $crud->log('Deliverable('.$result.') updated',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Unable to edit deliverable.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  You do not have access to edit deliverables.
                                </div>';
    }
}
echo json_encode($return);