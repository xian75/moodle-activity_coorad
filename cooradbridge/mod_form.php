<?php
/**
 * Coorad configuration form
 *
 * @package    mod
 * @subpackage cooradbridge
 * @copyright  2013 Cristian Natale <cristian.natale@coorad.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/cooradbridge/locallib.php');
require_once($CFG->libdir.'/filelib.php');

class mod_cooradbridge_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $config = get_config('cooradbridge');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $this->add_intro_editor($config->requiremodintro);

        //-------------------------------------------------------
        $mform->addElement('header', 'contentsection', get_string('contentheader', 'cooradbridge'));
        
		//$mform->addElement('editor', 'cooradbridge', get_string('content', 'cooradbridge'), null, cooradbridge_get_editor_options($this->context));
        //$mform->addElement('text', 'content', get_string('content'), array('size'=>'80'));
        
		$cooradAppArray = scandir($config->cooraddeploypath);
		$cooradAppHashedArray = array();
		foreach($cooradAppArray as $cooradAppItem) {
			if ($cooradAppItem != '.' && $cooradAppItem != '..') $cooradAppHashedArray[$cooradAppItem] = $cooradAppItem;
		}
		$mform->addElement('select', 'content', get_string('content'), $cooradAppHashedArray);

		
		
		$mform->addRule('cooradbridge', get_string('required'), 'required', null, 'client');
		
		//echo $config->cooraddeploypath.' '.$config->cooradmodbridgepath;
        
		//-------------------------------------------------------
        $mform->addElement('header', 'optionssection', get_string('optionsheader', 'cooradbridge'));

		$mform->addElement('advcheckbox', 'contentformat', get_string('contentformat', 'cooradbridge'));
        $mform->setDefault('contentformat', $config->contentformat);
        $mform->setAdvanced('contentformat', $config->contentformat_adv);

        if ($this->current->instance) {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions), $this->current->display);
        } else {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions));
        }
        if (count($options) == 1) {
            $mform->addElement('hidden', 'display');
            $mform->setType('display', PARAM_INT);
            reset($options);
            $mform->setDefault('display', key($options));
        } else {
            $mform->addElement('select', 'display', get_string('displayselect', 'cooradbridge'), $options);
            $mform->setDefault('display', $config->display);
            $mform->setAdvanced('display', $config->display_adv);
        }

        if (array_key_exists(RESOURCELIB_DISPLAY_POPUP, $options)) {
            $mform->addElement('text', 'popupwidth', get_string('popupwidth', 'cooradbridge'), array('size'=>3));
            if (count($options) > 1) {
                $mform->disabledIf('popupwidth', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupwidth', PARAM_INT);
            $mform->setDefault('popupwidth', $config->popupwidth);
            $mform->setAdvanced('popupwidth', $config->popupwidth_adv);

            $mform->addElement('text', 'popupheight', get_string('popupheight', 'cooradbridge'), array('size'=>3));
            if (count($options) > 1) {
                $mform->disabledIf('popupheight', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupheight', PARAM_INT);
            $mform->setDefault('popupheight', $config->popupheight);
            $mform->setAdvanced('popupheight', $config->popupheight_adv);
        }

        $mform->addElement('advcheckbox', 'printheading', get_string('printheading', 'cooradbridge'));
        $mform->setDefault('printheading', $config->printheading);
        $mform->setAdvanced('printintro', $config->printheading_adv);
        $mform->addElement('advcheckbox', 'printintro', get_string('printintro', 'cooradbridge'));
        $mform->setDefault('printintro', $config->printintro);
        $mform->setAdvanced('printintro', $config->printintro_adv);

        // add legacy files flag only if used
        if (isset($this->current->legacyfiles) and $this->current->legacyfiles != RESOURCELIB_LEGACYFILES_NO) {
            $options = array(RESOURCELIB_LEGACYFILES_DONE   => get_string('legacyfilesdone', 'cooradbridge'),
                             RESOURCELIB_LEGACYFILES_ACTIVE => get_string('legacyfilesactive', 'cooradbridge'));
            $mform->addElement('select', 'legacyfiles', get_string('legacyfiles', 'cooradbridge'), $options);
            $mform->setAdvanced('legacyfiles', 1);
        }

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();

        //-------------------------------------------------------
        $mform->addElement('hidden', 'revision');
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
    }

    function data_preprocessing(&$default_values) {
        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('cooradbridge');
            $default_values['cooradbridge']['format'] = $default_values['contentformat'];
			
            $default_values['cooradbridge']['text']   = file_prepare_draft_area($draftitemid, $this->context->id, 'mod_cooradbridge', 'content', 0, cooradbridge_get_editor_options($this->context), $default_values['content']);
            $default_values['cooradbridge']['itemid'] = $draftitemid;
        }
        if (!empty($default_values['displayoptions'])) {
            $displayoptions = unserialize($default_values['displayoptions']);
            if (isset($displayoptions['printintro'])) {
                $default_values['printintro'] = $displayoptions['printintro'];
            }
            if (isset($displayoptions['printheading'])) {
                $default_values['printheading'] = $displayoptions['printheading'];
            }
            if (!empty($displayoptions['popupwidth'])) {
                $default_values['popupwidth'] = $displayoptions['popupwidth'];
            }
            if (!empty($displayoptions['popupheight'])) {
                $default_values['popupheight'] = $displayoptions['popupheight'];
            }
        }
    }
}

