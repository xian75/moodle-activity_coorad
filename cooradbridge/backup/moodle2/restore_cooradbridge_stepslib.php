<?php

/**
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2013 Cristian Natale <cristian.natale@coorad.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_cooradbridge_activity_task
 */

/**
 * Structure step to restore one cooradbridge activity
 */
class restore_cooradbridge_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('cooradbridge', '/activity/cooradbridge');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_cooradbridge($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the cooradbridge record
        $newitemid = $DB->insert_record('cooradbridge', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add cooradbridge related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_cooradbridge', 'intro', null);
        $this->add_related_files('mod_cooradbridge', 'content', null);
    }
}
