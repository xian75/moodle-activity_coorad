<?php

/**
 * List of all cooradbridges in course
 *
 * @package    mod
 * @subpackage cooradbridge
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_cooradbridgelayout('incourse');

add_to_log($course->id, 'cooradbridge', 'view all', "index.php?id=$course->id", '');

$strcooradbridge         = get_string('modulename', 'cooradbridge');
$strcooradbridges        = get_string('modulenameplural', 'cooradbridge');
$strsectionname  = get_string('sectionname', 'format_'.$course->format);
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/cooradbridge/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strcooradbridges);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strcooradbridges);
echo $OUTPUT->header();

if (!$cooradbridges = get_all_instances_in_course('cooradbridge', $course)) {
    notice(get_string('thereareno', 'moodle', $strcooradbridges), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($cooradbridges as $cooradbridge) {
    $cm = $modinfo->cms[$cooradbridge->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($cooradbridge->section !== $currentsection) {
            if ($cooradbridge->section) {
                $printsection = get_section_name($course, $cooradbridge->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $cooradbridge->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($cooradbridge->timemodified)."</span>";
    }

    $class = $cooradbridge->visible ? '' : 'class="dimmed"'; // hidden modules are dimmed

    $table->data[] = array (
        $printsection,
        "<a $class href=\"view.php?id=$cm->id\">".format_string($cooradbridge->name)."</a>",
        format_module_intro('cooradbridge', $cooradbridge, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();
