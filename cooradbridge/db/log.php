<?php

/**
 * Definition of log events
 *
 * @package    mod_cooradbridge
 * @category   log
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'cooradbridge', 'action'=>'view', 'mtable'=>'cooradbridge', 'field'=>'name'),
    array('module'=>'cooradbridge', 'action'=>'view all', 'mtable'=>'cooradbridge', 'field'=>'name'),
    array('module'=>'cooradbridge', 'action'=>'update', 'mtable'=>'cooradbridge', 'field'=>'name'),
    array('module'=>'cooradbridge', 'action'=>'add', 'mtable'=>'cooradbridge', 'field'=>'name'),
);