<?php

/**
 * Coorad module version information
 *
 * @package    mod
 * @subpackage cooradbridge
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/cooradbridge/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // Coorad instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$cooradbridge = $DB->get_record('cooradbridge', array('id'=>$p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('cooradbridge', $cooradbridge->id, $cooradbridge->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('cooradbridge', $id)) {
        print_error('invalidcoursemodule');
    }
    $cooradbridge = $DB->get_record('cooradbridge', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/cooradbridge:view', $context);

add_to_log($course->id, 'cooradbridge', 'view', 'view.php?id='.$cm->id, $cooradbridge->id, $cm->id);

// Update 'viewed' state if required by completion system
require_once($CFG->libdir . '/completionlib.php');
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/cooradbridge/view.php', array('id' => $cm->id));

$options = empty($cooradbridge->displayoptions) ? array() : unserialize($cooradbridge->displayoptions);

if ($inpopup and $cooradbridge->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname.': '.$cooradbridge->name);
    if (!empty($options['printheading'])) {
        $PAGE->set_heading($cooradbridge->name);
    } else {
        $PAGE->set_heading('');
    }
    echo $OUTPUT->header();

} else {
    $PAGE->set_title($course->shortname.': '.$cooradbridge->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($cooradbridge);
    echo $OUTPUT->header();

    if (!empty($options['printheading'])) {
        echo $OUTPUT->heading(format_string($cooradbridge->name), 2, 'main', 'cooradbridgeheading');
    }
}

if (!empty($options['printintro'])) {
    if (trim(strip_tags($cooradbridge->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'cooradbridgeintro');
        echo format_module_intro('cooradbridge', $cooradbridge, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($cooradbridge->content, 'pluginfile.php', $context->id, 'mod_cooradbridge', 'content', $cooradbridge->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;
$content = format_text($content, 0 /*$cooradbridge->contentformat*/, $formatoptions);

$role_assignments = $DB->get_record('role_assignments', array('userid'=>$USER->id, 'contextid'=>$context->id));
$roleid = $role_assignments->roleid;

//echo $context->path;
$contextPathCourseId = 0;
$contextPath = explode('/', $context->path);
if (count($contextPath) > 1) $contextPathCourseId = $contextPath[count($contextPath) - 2];
if ($roleid == '') {
	$role_assignments = $DB->get_record('role_assignments', array('userid'=>$USER->id, 'contextid'=>$contextPathCourseId));
	$roleid = $role_assignments->roleid;
}
if (is_siteadmin($USER)) $roleid = 1;

$moodleparams = '&courseid='.$course->id.'&coursemoduleid='.$id.'&contextmoduleid='.$context->id.'&contextcourseid='.$contextPathCourseId.'&roleid='.$roleid;
//print_r($cm);

cooradbridge_display_frame($cooradbridge, $cm, $course, $moodleparams);



function cooradbridge_display_frame($cooradbridge, $cm, $course, $moodleparams) {
    global $CFG, $PAGE, $OUTPUT;

	$config = get_config('cooradbridge');
	//echo $config->cooraddeploypath.' '.$config->cooradmodbridgepath;

    $mimetype = resourcelib_guess_url_mimetype($cooradbridge->content);
    if (!$cooradbridge->contentformat)
		$fullurl  = $config->cooradmodbridgepath.'?cooradapp='.$cooradbridge->content.$moodleparams; //url_get_full_url($url, $cm, $course);
	else $fullurl  = $config->cooradmodbridgepath.'debug.php?cooradapp='.$cooradbridge->content.$moodleparams; //url_get_full_url($url, $cm, $course);
    $title    = $cooradbridge->name;

    $link = html_writer::tag('a', $fullurl, array('href'=>str_replace('&amp;', '&', $fullurl)));
    $clicktoopen = get_string('clicktoopen', 'cooradbridge', $link);
    $moodleurl = new moodle_url($fullurl);

    $extension = resourcelib_get_extension($cooradbridge->content/*$url->externalurl*/);

    $mediarenderer = $PAGE->get_renderer('core', 'media');
    $embedoptions = array(
        core_media::OPTION_TRUSTED => true,
        core_media::OPTION_BLOCK => true
    );

	$code = resourcelib_embed_general($fullurl, $title, $clicktoopen, $mimetype);

	$code = preg_replace('/object>/', 'iframe>', preg_replace('/<object[^>]/', '<iframe id="cooradframe" src="'.$fullurl.'" type="text/html" style="width: 100%; height: 400px; border: 0px;">', $code));
    echo $code;
	
    echo $OUTPUT->footer();
    die;
}

