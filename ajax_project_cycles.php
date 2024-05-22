<?php 
$module_name = 'project_cycles';
include('classes/config.php');
authenticate_ajax();

$pid=intval($crud->escape_string($_GET['pid']));

$viewFlag=authorizeAccess($module_name,'view');
$addFlag=authorizeAccess($module_name,'add');
$editFlag=authorizeAccess($module_name,'edit');
$deleteFlag=authorizeAccess($module_name,'delete');

if($viewFlag){
    if(isset($_GET['cycle']) && !empty($_GET['cycle']))
    {
        $cycle = $_GET['cycle'];
        $cycles_details=$crud->getData("SELECT * from `project_cycles` where title='".$cycle."' and project_id = $pid order by id ASC");
        
        $my_cycles=array();
        if($cycles_details != false && count($cycles_details)>0)
        {
            foreach ($cycles_details as $row) {
                $my_cycles[$row['title']] = $row;
            }
            
        }
        ?>

        
        <?php if(isset($my_cycles[$cycle])){

            $blocks = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $my_cycles[$cycle]['defaults']),true);
            //var_dump($blocks);
            if($my_cycles[$cycle]['type']=='Auto')
            {
                echo '<ol>'.cycle_levels_simple($blocks).'</ol>';

                $auth_details=$crud->getData("SELECT * from authorities where type='PC' and ref_id = ".$my_cycles[$cycle]['id']." order by sort_order ASC");
                echo '<div class="module"><div class="module-head"><h3>Authorities</h3></div><div class="module-body">';
                foreach($auth_details as $auth)
                {
                    $class='';
                    if($auth['status']==1)
                        $class='alert-success';
                    else if($auth['status']==2)
                        $class='alert-error';
                    echo '<div class="small alert '.$class.'"><strong>'.$auth['label'].':</strong> ';
                    echo ''.$auth['designation'].'';
                    echo '('.$auth['name'].')';
                    echo '<span class="pull-right">';
                    if(!empty($auth['signed_date']))
                        echo '<small>'.date("d M, Y", strtotime($auth['signed_date'])).'</small>';                                    
                    echo '</span></div>';
                }   
                echo '</div></div>';

            }
            else
            {?>
                <br><br><br><center><a  href="<?php echo $blocks['url']?>">Downlaod <?php echo $cycle?></a></center><br><br><br>
            <?php }?>

            <?php if($editFlag && $my_cycles[$cycle]['level']<1){?>


                <a href="javascript:;" class="btn btn-primary showModule" data-target="editCycle-<?php echo $cycle?>" style="margin:20px"><i class="icon-edit"></i> Edit <?php echo $cycle?></a>
                <a class="btn btn-danger ajaxDelete" style="margin:20px" href="ajax_project_cycles_delete.php?deleteCycle=<?php echo $my_cycles[$cycle]['id']?>"><i class="icon-remove-sign"></i> Delete <?php echo $cycle?></a>


                <div class="editCycle-<?php echo $cycle?>" style="display: none;margin-top: 20px;">
                    <form class="form-horizontal row-fluid ajaxForm" name="cycles" method="post"  enctype="multipart/form-data" action="ajax_project_cycles_update.php?pid=<?php echo $pid;?>&cycle=<?php echo $cycle;?>">
                        <?php if($my_cycles[$cycle]['type']=='Auto'){?>
                                <ol>
                                    <?php echo cycle_levels($blocks,$cycle)?>
                                </ol>
                            <div class="control-group">
                            </div>
                            <div class="control-group" id="authorites">
                                <label class="control-label" for="basicinput">Authorities<br><a href="javascript:;" class="newAuth"><small>Add New</small></a></label>
                                <div class="controls">
                                    <div class="sort_authorities">
                                        <?php 
                                        $auth_details=$crud->getData("SELECT * from authorities where type='PC' and ref_id = ".$my_cycles[$cycle]['id']." order by sort_order ASC");

                                        $query="select * from departments order by title ASC";
                                        $departments = $crud->getData($query);
                                        $departments_tree=parseTree($departments);

                                        $first_authority=0;
                                        if($auth_details != false && count($auth_details)>0)
                                        {
                                            foreach($auth_details as $auth)
                                            {

                                                $dep_users=$crud->getData("select admin.*,roles.title as roleName,roles.parent_id from admin left join roles on admin.role=roles.id where admin.department_id=".$auth['dep_id']." order by roles.sort_order ASC, roles.title ASC");
                                                
                                                if($dep_users != false && count($dep_users)>0)
                                                {
                                                    $dep_users_tree=parseTreeAdmin($dep_users);
                                                }

                                                if($first_authority == 0)
                                                {
                                                    $first_authority = $auth['admin_id'];
                                                }
                                                ?>

                                                <div class="autority row" style="margin-left: 0;margin-top: 20px;">
                                                    <div class="span1" style="text-align: center;">Delete:<br><input type="checkbox" name="auth_del[]" value="<?php echo $auth['id']?>"></div>
                                                    <div class="span3"><input type="hidden" name="auth_id[]" value="<?php echo $auth['id']?>">
                                                        Label:<br><input type="text" name="label[]" class="span12" value="<?php echo $auth['label'];?>">
                                                    </div>
                                                    <div class="span4">
                                                        Department:<br><select name="auth_dep[]" class="span12 auth_dep"><option value="">Select Department</option>
                                                            <?php 
                                                            
                                                            if($departments != false && count($departments)>0)
                                                            {
                                                                printTree($departments_tree,$auth['dep_id']);
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="span4">
                                                        User:<br><select name="auth_user[]" class="span12 auth_user"><option value="">Select User</option>
                                                            <?php 
                                                            if($dep_users_tree != false && count($dep_users_tree)>0)
                                                            {
                                                                printTreeAdmin($dep_users_tree,$auth['admin_id']);
                                                            }

                                                            ?></select> 
                                                    </div>
                                                </div>
                                                <?php 
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php }else{?>
                            <div class="control-group">
                                <label class="control-label" for="basicinput">Upload <?php echo $cycle;?></label>
                                <div class="controls">
                                    <div class="fileswrapper" style="margin: 0 0 20px 0;">
                                        <input  name="filesToUpload" type="file"  accept="application/msword,application/pdf">
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                        <input type="hidden" name="cycle_id" value="<?php echo $my_cycles[$cycle]['id']?>">
                        <input type="hidden" name="pc_type" value="<?php echo $my_cycles[$cycle]['type']?>">
                        <input type="submit" name="updateCyclesButton" value="Update <?php echo $cycle?>" class="btn btn-primary submitbtn">
                        <input type="hidden" name="updateCycle" value="updateCycle">
                        <input type="button" name="closeedit" value="Cancle" class="btn btn-danger hideModule"  data-target="editCycle-<?php echo $cycle?>" style="margin:20px">
                        <?php 
                            $auth_details=$crud->getData("SELECT * from authorities where type='PC' and ref_id = ".$my_cycles[$cycle]['id']." order by sort_order ASC");

                            if(count($auth_details)>1){ 
                                if($first_authority == $_SESSION['id']){
                                    if(isset($_SESSION['asign']) && !empty($_SESSION['asign']) && file_exists($_SESSION['asign'])){
                                        echo '<input type="button" name="initiateCycleButton" value="Sign & Move Forward" class="btn btn-success otherAjax" data-url="ajax_project_cycles_update.php?updateCycle='.$my_cycles[$cycle]['id'].'&initiateCycle=1&cycle='.$cycle.'" data-reloadtab="true">';
                                    }
                                    else{
                                        echo '<div class="alert alert-error" style="display: inline-block;margin-left: 20px;"><a href="profile.php" >Upload your Signature</a> to initiate the '.$cycle.'. Make sure to save your changes.</div>';
                                    }
                                }
                                else{
                                    echo '<div class="alert alert-error" style="display: inline-block;margin-left: 20px;">Only first authority can initiate the '.$cycle.'.</div>';
                                }
                            }
                            else{
                                echo '<div class="alert alert-error" style="display: inline-block;margin-left: 20px;">There should be 2 or more authorties to initiate the '.$cycle.'.</div>';
                            }
                        ?>

                    </form>
                </div>
            <?php }?>
        <?php }else{
            if($addFlag)
            {
                $default_cycles_details=$crud->getData("SELECT * FROM `project_cycles` where title='".$cycle."' and project_id is NULL ORDER BY id ASC ");
                $row = $default_cycles_details[0];

                $blocks = json_decode($row['defaults'],true);?>

                <form class="form-horizontal row-fluid ajaxForm" name="cycles" method="post"  enctype="multipart/form-data" action="ajax_project_cycles_add.php?pid=<?php echo $pid;?>&cycle=<?php echo $cycle;?>">


                    <div class="control-group">
                        <label class="control-label" for="basicinput">Type</label>
                        <div class="controls">
                            <div class="input-group">
                                <label class="radio inline">
                                    <input type="radio" name="pc_type" id="pc_type_auto" value="Auto" checked="">
                                    Auto
                                </label>
                                <label class="radio inline">
                                    <input type="radio" name="pc_type" id="pc_type_manual" value="Manual">
                                    Manual
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="pc_auto">
                        <ol>
                            <?php echo cycle_levels($blocks,$cycle)?>
                        </ol>
                    </div>
                    <div class="pc_manual" style="display: none;">
                        
                        <div class="control-group">
                            <label class="control-label" for="basicinput">Upload <?php echo $cycle;?></label>
                            <div class="controls">
                                <div class="fileswrapper" style="margin: 0 0 20px 0;">
                                    <input  name="filesToUpload" type="file"  accept="application/msword,application/pdf">
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="submit" name="addCyclesButton" value="Add <?php echo $cycle?>" class="btn btn-primary submitbtn">
                    <input type="hidden" name="addCycle" value="addCycle">
                </form>
            <?php }else{ echo '<center>No '.$cycle.' added yet & you dont have access to add '.$cycle.'.</center>';}?>   
        <?php }?>  
        <div class="auth_form" style="display: none">
            <div class="autority row" style="margin-left: 0;margin-top: 20px;">
                <div class="span1" style="text-align: center;"></div>
                <div class="span3">Label:<br><input type="text" name="label[]" class="span12"><input type="hidden" name="auth_id[]" value=""></div>
                <div class="span4">
                    Department:<br><select name="auth_dep[]" class="span12 auth_dep"><option value="">Select Department</option>
                        <?php 
                        $query="select * from departments order by title ASC";
                        $nodes = $crud->getData($query);
                        if($nodes != false && count($nodes)>0)
                        {
                            $tree=parseTree($nodes);
                            printTree($tree);
                        }
                        ?>
                    </select>
                </div>
                <div class="span4">
                    User:<br><select name="auth_user[]" class="span12 auth_user"><option value="">Select User</option><option value="">Select department to load users</option></select> 
                </div>
            </div>
        </div>    

        <script type="text/javascript">
            tinymce.init({
                selector:"#<?php echo $cycle;?> .tinyeditor",
                plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons autoresize',
                menubar: false,
                autoresize_bottom_margin: 20,
                min_height:100,
                //toolbar_sticky: true,
                //toolbar_drawer: 'sliding',
                
                images_upload_url: 'upload-editor-images.php',
                images_upload_base_path: 'https://ppms.ajk.gov.pk/',
                images_upload_credentials: true,

                content_css : "/css/editor.css",
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

            $('input[type=radio][name=pc_type]').change(function() {
                //alert(this.value );
                var auto = $(this).parents('form').find('.pc_auto');
                var manual = $(this).parents('form').find('.pc_manual');

                if (this.value == 'Auto') {
                    auto.show();
                    manual.hide();
                }
                else if (this.value == 'Manual') {
                    manual.show();
                    auto.hide();
                    
                }
            });


            $( ".sort_authorities" ).sortable({
                 //appendTo: document.body      
                 placeholder: "ui-state-highlight"
            });
            $( ".sort_authorities" ).disableSelection();

            $('.newAuth').click(function(){
                $(this).parents('form').find( ".sort_authorities" ).append($('.auth_form').html());
            });

        </script>  
    <?php }
    else
    {?>

        <div class="actions center">
            <a href="javascript:;" class="btn btn-small refresh">
                <i class="icon-refresh shaded"></i> Refresh Project Cycles
            </a>
        </div>
        <?php 
        $default_cycles_details=$crud->getData("SELECT * FROM `project_cycles` where project_id is NULL ORDER BY id ASC ");
        $cycles_details=$crud->getData("SELECT * from `project_cycles` where project_id = $pid order by id ASC");
        
        $my_cycles=array();
        if($cycles_details != false && count($cycles_details)>0)
        {
            foreach ($cycles_details as $row) {
                $my_cycles[$row['title']] = $row;
            }
            
        }

        foreach($default_cycles_details as $row){?>
            <button class="accordion"  data-id="<?php echo $row['title'];?>" data-url="ajax_project_cycles.php?pid=<?php echo $pid?>&cycle=<?php echo $row['title'];?>">
                <?php echo $row['title'];?>
                <?php if(isset($my_cycles[$row['title']])){
                    if($my_cycles[$row['title']]['type']=='Auto'){?><span class="pull-right"><a target="_blank"  href="project_cycles_print.php?pid=<?php echo $pid?>&cycle=<?php echo $row['title']?>&cycle_id=<?php echo $my_cycles[$row['title']]['id'];?>">View</a> | <a target="_blank" href="project_cycles_print.php?pid=<?php echo $pid?>&cycle=<?php echo $row['title']?>&cycle_id=<?php echo $my_cycles[$row['title']]['id'];?>&action=print">Print</a></span>
                    <?php }else{ 
                        //var_dump($my_cycles[$row['title']]['defaults']);
                        $defaults = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $my_cycles[$row['title']]['defaults']),true);
                        //var_dump($defaults);
                        ?>
                        <span class="pull-right"><a  href="<?php echo $defaults['url']?>">Download</a></span>
                    <?php }?>
                <?php }?>
            </button>
            <div class="panel" id="<?php echo $row['title'];?>" data-loaded="false">
                <div class="acc_content"></div>
                <div class="media stream loading_cycle">
                    <a href="javascript:;"><i class="icon-refresh shaded rotate"></i>Loading</a>
                </div>
            </div>
        <?php }?>  
    <?php }?>
<?php }?>