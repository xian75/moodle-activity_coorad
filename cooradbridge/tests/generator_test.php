<?php

/**
 * PHPUnit data generator tests
 *
 * @package    mod_cooradbridge
 * @category   phpunit
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * PHPUnit data generator testcase
 *
 * @package    mod_cooradbridge
 * @category   phpunit
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_cooradbridge_generator_testcase extends advanced_testcase {
    public function test_generator() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('cooradbridge'));

        /** @var mod_cooradbridge_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_cooradbridge');
        $this->assertInstanceOf('mod_cooradbridge_generator', $generator);
        $this->assertEquals('cooradbridge', $generator->get_modulename());

        $generator->create_instance(array('course'=>$SITE->id));
        $generator->create_instance(array('course'=>$SITE->id));
        $cooradbridge = $generator->create_instance(array('course'=>$SITE->id));
        $this->assertEquals(3, $DB->count_records('cooradbridge'));

        $cm = get_coursemodule_from_instance('cooradbridge', $cooradbridge->id);
        $this->assertEquals($cooradbridge->id, $cm->instance);
        $this->assertEquals('cooradbridge', $cm->modname);
        $this->assertEquals($SITE->id, $cm->course);

        $context = context_module::instance($cm->id);
        $this->assertEquals($cooradbridge->cmid, $context->instanceid);
    }
}
