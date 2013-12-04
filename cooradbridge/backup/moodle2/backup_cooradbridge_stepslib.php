<?php

/**
 * @package    mod
 * @subpackage cooradbridge
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define all the backup steps that will be used by the backup_cooradbridge_activity_task
 */

/**
 * Define the complete cooradbridge structure for backup, with file and id annotations
 */
class backup_cooradbridge_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $cooradbridge = new backup_nested_element('cooradbridge', array('id'), array(
            'name', 'intro', 'introformat', 'content', 'contentformat',
            'legacyfiles', 'legacyfileslast', 'display', 'displayoptions',
            'revision', 'timemodified'));

        // Build the tree
        // (love this)

        // Define sources
        $cooradbridge->set_source_table('cooradbridge', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $cooradbridge->annotate_files('mod_cooradbridge', 'intro', null); // This file areas haven't itemid
        $cooradbridge->annotate_files('mod_cooradbridge', 'content', null); // This file areas haven't itemid

        // Return the root element (cooradbridge), wrapped into standard activity structure
        return $this->prepare_activity_structure($cooradbridge);
    }
}
