<?php 
session_start();
//error_reporting(0);
ini_set('display_errors', TRUE);
error_reporting(E_ALL);
//$site_uri[count($site_uri)-1]
$site_url="";
$site_name = 'Project Progress Monitoring System';
$department_name='Information Technology Board';
$site_title = $site_name;
$_my_access = array();
$_my_projects_condition='';

require 'classes/PHPMailer/src/Exception.php';
require 'classes/PHPMailer/src/PHPMailer.php';
require 'classes/PHPMailer/src/SMTP.php';


include_once("classes/validation.php");
include_once("classes/crud.php");
include_once("classes/emails.php");
include_once("classes/generalModal.php");

 
$crud = new Crud();
$generalmodal = new GeneralModal();
$validation = new Validation();

if(isset($_GET['sucmsg']) && !empty($_GET['sucmsg']))
{
    $sucmsg = $_GET['sucmsg'];
}

if(isset($_GET['errmsg']) && !empty($_GET['errmsg']))
{
    $errmsg = $_GET['errmsg'];
}
if(isset($_SESSION['ppmsRole']) && $_SESSION['ppmsRole']>0)
{
    //echo $_SESSION['ppmsRole'];
    $query="select accesses from roles where id=".$_SESSION['ppmsRole'];
    $roles = $crud->getData($query);

    $_my_access = json_decode($roles[0]['accesses'],true);
}
if(isset($_SESSION['jurisdiction']) && $_SESSION['jurisdiction'] == 'All')
{
    $_my_projects_condition = "1=1";
}
else if(isset($_SESSION['jurisdiction']) && $_SESSION['jurisdiction'] == 'Departmental & Sub-Departmentals' )
{
    $_my_projects_condition = " projects.department_id IN  (".implode(',', $_SESSION['sub_departments_id']).")";
}
else if(isset($_SESSION['jurisdiction']) && $_SESSION['jurisdiction'] == 'Departmental')
{
    $_my_projects_condition = " projects.department_id='".$_SESSION['department_id']."'";
}
else if(isset($_SESSION['jurisdiction']) && $_SESSION['jurisdiction'] == 'Created')
{
    $_my_projects_condition = " projects.added_by='".$_SESSION['id']."'";
}

$districts = array('1'=>'Muzaffarabad', '2'=>'Neelum Valley', '3'=>'Hattian Bala', '4'=>'Poonch', '5'=>'Mirpur', '6'=>'Bagh', '7'=>'Bhimber', '8'=>'Sudhanoti', '9'=>'Kotli','10'=>'Haveli');

$gcount=1;
function getSubDepartments($did = 0) {
    global $crud;
    $return = array();
    # Traverse the tree and search for direct children of the root
    $query="select id from departments where parent_id=".$did;
    $sds = $crud->getData($query);
    if($sds != false && count($sds)>0)
        foreach($sds as $sd) {
            $return[]=$sd['id'];
            $subs =  getSubDepartments($sd['id']);
            if($subs != null)
                foreach ($subs as $value)
                    $return[]=$value;                
        }
    return empty($return) ? null : $return;    
}

function parseTree($tree=array(), $root = 0) {
    $return = array();
    # Traverse the tree and search for direct children of the root
    foreach($tree as $index=>$child) {
        # A direct child is found
        if($child['parent_id'] == $root) {
            # Remove item from tree (we don't need to traverse this again)
            unset($tree[$index]);
            # Append the child into result array and parse its children
            $return[] = array(
                'id' => $child['id'],
                'name' => $child['title'],
                'children' => parseTree($tree, $child['id'])
            );
        }
    }
    return empty($return) ? null : $return;    
}

function printTree($tree,$selected='',$spaces="")
{
    foreach($tree as $child)
    {
        ?><option value="<?php echo $child['id']?>" <?php if($selected!='' && $selected== $child['id']){ echo 'selected="selected"';}?>><?php echo $spaces.$child['name']?></option><?php
        if($child['children']!=null)
            printTree($child['children'],$selected,$spaces.'&nbsp;&nbsp;&nbsp;');
    }
}
function printTreeTable($tree,$spaces="",$module_name)
{
    global $gcount;
    foreach($tree as $child)
    {?>
        <tr>
            <td><?php echo htmlentities($gcount++);?></td>
            <td><?php echo $spaces.' '.htmlentities($child['name']);?></td>
            <td style="text-align: center">
                <?php if(authorizeAccess($module_name,'edit')){?>
                    <a href="<?php echo $module_name;?>.php?view=<?php echo htmlentities($child['id'])?>"><i class="icon-edit"></i></a>
                <?php }?>
                <?php if(authorizeAccess($module_name,'delete')){?>
                    <a href="<?php echo $module_name;?>.php?delete=<?php echo htmlentities($child['id'])?>" onclick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a>
                <?php }?>
            </td>
        </tr><?php
        if($child['children']!=null)
        {
            printTreeTable($child['children'],$spaces.'---',$module_name);
        }
    }
}
function parseTreeAdmin($tree, $root = 0) {
    $return = array();
    # Traverse the tree and search for direct children of the root
    foreach($tree as $index=>$child) {
        # A direct child is found
        if($child['parent_id'] == $root) {
            # Remove item from tree (we don't need to traverse this again)
            unset($tree[$index]);
            # Append the child into result array and parse its children
            $return[] = array(
                'id' => $child['id'],
                'name' => $child['roleName'].' ('.$child['display_name'].')',
                'role'  => $child['role'],
                'children' => parseTreeAdmin($tree,  $child['role'])
            );
        }
    }
    return empty($return) ? null : $return;    
}
function printTreeAdmin($tree,$selected='',$spaces="")
{
    foreach($tree as $child)
    {?>
        <option value="<?php echo $child['id']?>" <?php if(!empty($selected) &&  $child['id']== $selected){ echo 'selected="selected"';}?>>
             <?php echo $spaces.$child['name']?>
        </option><?php
        if($child['children']!=null)
            printTreeAdmin($child['children'],$selected,$spaces.'&nbsp;&nbsp;&nbsp;');
    }
}

function normalizeString ($str = '')
{
    $str = strip_tags($str); 
    $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
    $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
    $str = strtolower($str);
    $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
    $str = htmlentities($str, ENT_QUOTES, "utf-8");
    $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
    $str = str_replace(' ', '-', $str);
    $str = rawurlencode($str);
    $str = str_replace('%', '-', $str);
    return $str;
}
function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

function authenticate()
{
    global $site_url;
    $site_uri = explode("/",$_SERVER['REQUEST_URI']);
    if(@strlen($_SESSION['alogin'])==0 || !isset($_SESSION['ppms']) || $_SESSION['ppms']!='ppms')
    {   
        $_SESSION['myreffer'] = $site_uri[count($site_uri)-1];
        header("location:{$site_url}index.php");
        exit;
    }
}


function authenticate_ajax()
{
    global $site_url;
    $site_uri = explode("/",$_SERVER['REQUEST_URI']);
    if(@strlen($_SESSION['alogin'])==0 || !isset($_SESSION['ppms']) || $_SESSION['ppms']!='ppms')
    {   
        $_SESSION['myreffer'] = $site_uri[count($site_uri)-1];?>
            <script type="text/javascript">
                window.location.href = "<?php echo $site_url; ?>index.php";
            </script>
        <?php 
    }
}
function authorizeAccess($module,$access){
    //var_dump($_SESSION['accesses']);
    global $_my_access;
    if($_my_access=='all')
        return true;
    else if(array_key_exists($module,$_my_access))
        if(in_array($access,$_my_access[$module]))
            return true;
    return false;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function get_image_location($image = ''){
    $exif = exif_read_data($image, 0, true);
    //echo '<pre>';
    //var_dump($exif);
    
    if($exif && isset($exif['GPS'])){
        $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
        $GPSLatitude    = $exif['GPS']['GPSLatitude'];
        $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
        $GPSLongitude   = $exif['GPS']['GPSLongitude'];
        
        $lat_degrees = count($GPSLatitude) > 0 ? gps2Num($GPSLatitude[0]) : 0;
        $lat_minutes = count($GPSLatitude) > 1 ? gps2Num($GPSLatitude[1]) : 0;
        $lat_seconds = count($GPSLatitude) > 2 ? gps2Num($GPSLatitude[2]) : 0;
        
        $lon_degrees = count($GPSLongitude) > 0 ? gps2Num($GPSLongitude[0]) : 0;
        $lon_minutes = count($GPSLongitude) > 1 ? gps2Num($GPSLongitude[1]) : 0;
        $lon_seconds = count($GPSLongitude) > 2 ? gps2Num($GPSLongitude[2]) : 0;
        
        $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
        $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;
        
        $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
        $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));

        return array('latitude'=>$latitude, 'longitude'=>$longitude);
    }else{
        return false;
    }
}

function gps2Num($coordPart){
    $parts = explode('/', $coordPart);
    if(count($parts) <= 0)
    return 0;
    if(count($parts) == 1)
    return $parts[0];
    return floatval($parts[0]) / floatval($parts[1]);
}

function cycle_levels($blocks,$title)
{
    $out='';
    foreach ($blocks as $key => $value) {
        $out.='<li>';
            $out.='<h5 style="margin-top: 20px">'.$key.'</h5>';
             if(is_array($value)){
                $out.= '<ol>';
                    $out.=cycle_levels($value,$title);
                $out.='</ol>';
             }
             else{
                $out.='<div style="position: relative;"><textarea id="'.str_replace(' ', '', $title).'-'.str_replace(' ', '', $key).'" name="details['.$key.']" class="span12 tinyeditor">'.$value.'</textarea></div>';
             }  
        $out.='</li>';
    }
    return $out;
}
function cycle_levels_simple($blocks)
{
    $out='';
    foreach ($blocks as $key => $value) {
        $out.='<li>';
            $out.='<h5>'.$key.'</h5>';
            if(is_array($value)){
                $out.='<ol>';
                    $out.=cycle_levels_simple($value);
                $out.='</ol>';
            }else{
                $out.='<p>'.$value.'</p>';
            }
        $out.='</li>';
    }
    return $out;
}
function dd($data, $d=true){
    echo '<pre>';
    print_r($data);
    if($d){
        die;
    }
}
function displayLimitedListItems($data, $limit = 5)
{
    if ($data === '') {
        echo "Null";
        return;
    }

    $dataArray = explode(",", $data);
    $totalItems = count($dataArray);
    
    echo "<ul class='serial-list'>";
    for ($i = 0; $i < $totalItems; $i++) {
        $style = $i >= $limit ? 'style="display: none;"' : ''; // Hide items after the limit
        echo "<li $style>$dataArray[$i]</li>";
    }
    echo "</ul>";
    
    if ($totalItems > $limit) {
        // Add a "Read More" link with an onClick event to toggle list items
        echo '<a href="#" onclick="toggleListItems(this, ' . 10 . ')">Show More..</a>';
    }

    // Add JavaScript for toggling list items
    echo '<script>
        function toggleListItems(link, limit) {
            // Get the parent ul element
            var ul = link.previousElementSibling;
            // Find all list items in the ul
            var items = ul.getElementsByTagName("li");
            // Toggle the visibility of items beyond the limit
            var isShowingMore = link.textContent.trim() === "Show More..";
            
            for (var i = limit; i < items.length; i++) {
                items[i].style.display = isShowingMore ? "list-item" : "none";
            }
            
            // Change the link text based on the current state
            link.textContent = isShowingMore ? "Show Less" : "Show More";
        }
    </script>';
}