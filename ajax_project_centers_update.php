<?php 
$module_name = 'centers';
include('classes/config.php');
authenticate_ajax();

$editFlag=authorizeAccess($module_name,'edit');
$return=array('msg'=>'<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  No Naughty Bussiness.
                                </div>','status'=>false);

if(isset($_POST['editCenter']) && isset($_GET['updateCenter']) && $_GET['updateCenter']>0)
{
    if($editFlag)
    {
		$center_id=intval($crud->escape_string($_GET['updateCenter']));
        
        $title=$crud->escape_string($_POST['title']);
        $address=$crud->escape_string($_POST['address']);
        $description=$crud->escape_string($_POST['description']);

        $focal_person=$crud->escape_string($_POST['focal_person']);
        $focal_person_email=$crud->escape_string($_POST['focal_person_email']);
        $focal_person_phone=$crud->escape_string($_POST['focal_person_phone']);

        $workstaions=(isset($_POST['workstaions']) && $_POST['workstaions']>0)?$crud->escape_string($_POST['workstaions']):0;
        $laptops=(isset($_POST['laptops']) && $_POST['laptops']>0)?$crud->escape_string($_POST['laptops']):0;
        $printers=(isset($_POST['printers']) && $_POST['printers']>0)?$crud->escape_string($_POST['printers']):0;
        $scanners=(isset($_POST['scanners']) && $_POST['scanners']>0)?$crud->escape_string($_POST['scanners']):0;

        $total_staff=(isset($_POST['total_staff']) && $_POST['total_staff']>0)?$crud->escape_string($_POST['total_staff']):0;
        $latitude=$crud->escape_string($_POST['latitude']);
        $longitude=$crud->escape_string($_POST['longitude']);     

        $prev_images=$crud->escape_string($_POST['prev_images']);        

        $msg = $validation->check_empty($_POST, array(array('title','Center Title'),array('address','Address'),array('focal_person','Focal Person'))); 
               
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

            $imgstr='';
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
                                
                            $imagefile =$folder_name."/center_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
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

            $fimage='';
            if(!empty($_FILES['fimage']))
            {
                $file = $_FILES['fimage']; 
                $file_name = $file['name'];
                @$file_ext=strtolower(end(explode('.',$file_name)));
                @$mimetype = mime_content_type($_FILES['fimage']['tmp_name']);


                $expensions= array("jpeg","jpg","png");
                $mimes = array('image/jpeg',  'image/png');
                
                if(!empty($file_ext) && !empty($file_name)  && !empty($mimetype) && in_array($file_ext,$expensions) && in_array($mimetype,$mimes) ){
                    $folder_name='uploads/project_images';
                        
                    $imagefile =$folder_name."/center_".date('YmdHis').'_'.rand(1, 1000000).'.'.$file_ext;
                    if(move_uploaded_file($file["tmp_name"],$imagefile))
                    {
                        $fimage = $imagefile;

                        $prev_fimage=$crud->escape_string($_POST['prev_fimage']);
                        if(!empty($prev_fimage))
                        {
                            @unlink($prev_fimage);
                        }
                    }
                }
            }

            $updb='';
            if(!empty($fimage))
                $updb .= ",fimage='$fimage'";



            $sql="UPDATE centers set title='$title',address='$address',description='$description',focal_person='$focal_person',focal_person_email='$focal_person_email',focal_person_phone='$focal_person_phone',workstaions='$workstaions',laptops='$laptops',printers='$printers',scanners='$scanners',total_staff='$total_staff',latitude='$latitude',longitude='$longitude',images='$imgstr',updated_by='".$_SESSION['id']."' $updb where id=".$center_id;
            
             
            $result = $crud->execute($sql);

            if($result != false)
            {
                
                $return['status']=true;
                $return['msg']='<div class="alert alert-success alert-dismissible fade in" role="alert">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                      Project center updated !!
                                    </div>';


                $crud->log('Center('.$result.') updated',$_SESSION['id']);
            }
            else
            {
                $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  Unable to edit center.
                                </div>';
            }
        }
    }
    else
    {
        $return['msg'] = '<div class="alert alert-danger alert-dismissible fade in" role="alert">
                                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  You do not have access to edit center.
                                </div>';
    }
}
echo json_encode($return);