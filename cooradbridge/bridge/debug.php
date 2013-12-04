<?php
    require_once('../../../config.php');
	//print_r($USER);

	require_once($CFG->dirroot.'/mod/cooradbridge/locallib.php');
	$config = get_config('cooradbridge');
	
	if (isset($_GET['courseid'])) $_SESSION['courseid'] = $_GET['courseid'];
	if (isset($_GET['coursemoduleid'])) $_SESSION['coursemoduleid'] = $_GET['coursemoduleid'];
	if (isset($_GET['contextmoduleid'])) $_SESSION['contextmoduleid'] = $_GET['contextmoduleid'];
	if (isset($_GET['contextcourseid'])) $_SESSION['contextcourseid'] = $_GET['contextcourseid'];
	if (isset($_GET['roleid'])) $_SESSION['roleid'] = $_GET['roleid'];
	
	if (isset($_GET['cooradapp'])) $_SESSION['cooradapp'] = $_GET['cooradapp'];
	//chdir('...\gm\deploy\moodle');
	chdir($config->cooraddeploypath.$_SESSION['cooradapp']);
	
	require_once("debug.php");
?>

<script>
	$('#cooradframe', top.document).css('height', $('#container').height() + 40);
	//alert($('#cooradframe', top.document).css('height'));
</script>