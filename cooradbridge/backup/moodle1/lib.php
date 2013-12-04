<?php
/**
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage cooradbridge
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Coorad conversion handler. This resource handler is called by moodle1_mod_resource_handler
 */
class moodle1_mod_cooradbridge_handler extends moodle1_resource_successor_handler {

    /** @var moodle1_file_manager instance */
    protected $fileman = null;

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     * Called by moodle1_mod_resource_handler::process_resource()
     */
    public function process_legacy_resource(array $data, array $raw = null) {

        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid, 'resource');
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // convert the legacy data onto the new cooradbridge record
        $cooradbridge                       = array();
        $cooradbridge['id']                 = $data['id'];
        $cooradbridge['name']               = $data['name'];
        $cooradbridge['intro']              = $data['intro'];
        $cooradbridge['introformat']        = $data['introformat'];
        $cooradbridge['content']            = $data['alltext'];

        if ($data['type'] === 'html') {
            // legacy Resource of the type Web cooradbridge
            $cooradbridge['contentformat'] = FORMAT_HTML;

        } else {
            // legacy Resource of the type Plain text cooradbridge
            $cooradbridge['contentformat'] = (int)$data['reference'];

            if ($cooradbridge['contentformat'] < 0 or $cooradbridge['contentformat'] > 4) {
                $cooradbridge['contentformat'] = FORMAT_MOODLE;
            }
        }

        $cooradbridge['legacyfiles']        = RESOURCELIB_LEGACYFILES_ACTIVE;
        $cooradbridge['legacyfileslast']    = null;
        $cooradbridge['revision']           = 1;
        $cooradbridge['timemodified']       = $data['timemodified'];

        // populate display and displayoptions fields
        $options = array('printheading' => 0, 'printintro' => 0);
        if ($data['popup']) {
            $cooradbridge['display'] = RESOURCELIB_DISPLAY_POPUP;
            $rawoptions = explode(',', $data['popup']);
            foreach ($rawoptions as $rawoption) {
                list($name, $value) = explode('=', trim($rawoption), 2);
                if ($value > 0 and ($name == 'width' or $name == 'height')) {
                    $options['popup'.$name] = $value;
                    continue;
                }
            }
        } else {
            $cooradbridge['display'] = RESOURCELIB_DISPLAY_OPEN;
        }
        $cooradbridge['displayoptions'] = serialize($options);

        // get a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_cooradbridge');

        // convert course files embedded into the intro
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $cooradbridge['intro'] = moodle1_converter::migrate_referenced_files($cooradbridge['intro'], $this->fileman);

        // convert course files embedded into the content
        $this->fileman->filearea = 'content';
        $this->fileman->itemid   = 0;
        $cooradbridge['content'] = moodle1_converter::migrate_referenced_files($cooradbridge['content'], $this->fileman);

        // write cooradbridge.xml
        $this->open_xml_writer("activities/cooradbridge_{$moduleid}/cooradbridge.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'cooradbridge', 'contextid' => $contextid));
        $this->write_xml('cooradbridge', $cooradbridge, array('/cooradbridge/id'));
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml for migrated resource file.
        $this->open_xml_writer("activities/cooradbridge_{$moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}
