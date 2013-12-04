<?php

/**
 * Coorad module admin settings and defaults
 *
 * @package    mod
 * @subpackage cooradbridge
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(
                                                           RESOURCELIB_DISPLAY_FRAME,
                                                           RESOURCELIB_DISPLAY_POPUP));
	
    $defaultdisplayoptions = array(
								   RESOURCELIB_DISPLAY_FRAME,
								   RESOURCELIB_DISPLAY_POPUP);

	// COORAD deploy path
    $settings->add(new admin_setting_configtext('cooradbridge/cooraddeploypath', get_string('cooraddeploypathexplain', 'cooradbridge'), '', 'C:/www/gm/deploy/'));
	// COORAD bridge module context path
    $settings->add(new admin_setting_configtext('cooradbridge/cooradmodbridgepath', get_string('cooradmodbridgepath', 'cooradbridge'), '', '/moodle/mod/cooradbridge/bridge/'));

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configcheckbox('cooradbridge/requiremodintro',
        get_string('requiremodintro', 'admin'), get_string('configrequiremodintro', 'admin'), 1));
    $settings->add(new admin_setting_configmultiselect('cooradbridge/displayoptions',
        get_string('displayoptions', 'cooradbridge'), get_string('configdisplayoptions', 'cooradbridge'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('cooradbridgemodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox_with_advanced('cooradbridge/contentformat',
        get_string('contentformat', 'cooradbridge'), get_string('contentformatexplain', 'cooradbridge'),
        array('value'=>0, 'adv'=>true)));
		
    $settings->add(new admin_setting_configcheckbox_with_advanced('cooradbridge/printheading',
        get_string('printheading', 'cooradbridge'), get_string('printheadingexplain', 'cooradbridge'),
        array('value'=>1, 'adv'=>false)));
    $settings->add(new admin_setting_configcheckbox_with_advanced('cooradbridge/printintro',
        get_string('printintro', 'cooradbridge'), get_string('printintroexplain', 'cooradbridge'),
        array('value'=>0, 'adv'=>false)));
    $settings->add(new admin_setting_configselect_with_advanced('cooradbridge/display',
        get_string('displayselect', 'cooradbridge'), get_string('displayselectexplain', 'cooradbridge'),
        array('value'=>RESOURCELIB_DISPLAY_FRAME, 'adv'=>true), $displayoptions));
    $settings->add(new admin_setting_configtext_with_advanced('cooradbridge/popupwidth',
        get_string('popupwidth', 'cooradbridge'), get_string('popupwidthexplain', 'cooradbridge'),
        array('value'=>620, 'adv'=>true), PARAM_INT, 7));
    $settings->add(new admin_setting_configtext_with_advanced('cooradbridge/popupheight',
        get_string('popupheight', 'cooradbridge'), get_string('popupheightexplain', 'cooradbridge'),
        array('value'=>450, 'adv'=>true), PARAM_INT, 7));
}
