<?php

/**
 * Defines backup_cooradbridge_activity_task class
 *
 * @package     mod_cooradbridge
 * @category    backup
 * @copyright   2013 Cristian Natale <cristian.natale@coorad.org>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/cooradbridge/backup/moodle2/backup_cooradbridge_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the Coorad instance
 */
class backup_cooradbridge_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the cooradbridge.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_cooradbridge_activity_structure_step('cooradbridge_structure', 'cooradbridge.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of cooradbridges
        $search="/(".$base."\/mod\/cooradbridge\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@PAGEINDEX*$2@$', $content);

        // Link to cooradbridge view by moduleid
        $search="/(".$base."\/mod\/cooradbridge\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@PAGEVIEWBYID*$2@$', $content);

        return $content;
    }
}
