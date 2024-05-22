<?php

include('classes/config.php');

$crud->log('Logged out from system');



$_SESSION['alogin']="";
$_SESSION['ppms']='';
$_SESSION['jurisdiction']='';
$_SESSION['sub_departments_id']='';

session_unset();

//session_destroy();

?>

<script language="javascript">

document.location="index.php";

</script>

