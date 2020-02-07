<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Syllabus configuration form.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Syllabus configuration form
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_syllabus_mod_form extends moodleform_mod {

    /**
     * Define form elements.
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form;
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // Name.
        $mform->addElement('text', 'name', get_string('name'), array('size' => '64', 'maxlength' => 255));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->setDefault('name', get_string('modulename', 'mod_syllabus'));

        // Syllabus type.
        $radio = array();
        $radio[] = $mform->createElement('radio', 'syllabustype',
                null, get_string('syllabusobjectives', 'mod_syllabus'), \mod_syllabus\syllabus::SYLLABUS_TYPE_OBJECTIVES);
        $radio[] = $mform->createElement('radio', 'syllabustype',
                null, get_string('syllabuscompetencies', 'mod_syllabus'), \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES);
        $mform->addGroup($radio, 'syllabustype', get_string('syllabustype', 'mod_syllabus'), ' ', false);
        $mform->setDefault('syllabustype', \mod_syllabus\syllabus::SYLLABUS_TYPE_OBJECTIVES);
        $mform->addHelpButton('syllabustype', 'syllabustype', 'mod_syllabus');
        $this->standard_intro_elements();

        $mform->addElement('editor', 'versionnoteseditor', get_string('versionnotes', 'mod_syllabus'), null,
                            syllabus_get_editor_options($this->context));
        $mform->setType('versionnoteseditor', PARAM_RAW);
        $mform->addHelpButton('versionnoteseditor', 'versionnotes', 'mod_syllabus');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Prepares the form before data are set.
     *
     * Additional wysiwyg editor are prepared here, the introeditor is prepared automatically by core.
     *
     * @param array $data to be set
     */
    public function data_preprocessing(&$data) {
        global $PAGE;

        if ($this->current->instance) {
            // Editing an existing syllabus - let us prepare the added editor element (intro done automatically).
            $draftitemid = file_get_submitted_draft_itemid('versionnotes');
            $data['versionnoteseditor']['text'] = file_prepare_draft_area($draftitemid, $this->context->id,
                                'mod_syllabus', 'versionnotes', 0,
                                syllabus_get_editor_options($this->context),
                                $data['versionnotes']);
            $data['versionnoteseditor']['format'] = $data['versionnotesformat'];
            $data['versionnoteseditor']['itemid'] = $draftitemid;

        } else {
            // Adding a new syllabus instance.
            $draftitemid = file_get_submitted_draft_itemid('versionnotes');
            file_prepare_draft_area($draftitemid, null, 'mod_syllabus', 'versionnotes', 0); // No context yet, itemid not used.
            $data['versionnoteseditor'] = array('text' => '', 'format' => editors_get_preferred_format(), 'itemid' => $draftitemid);
            // Set the default value with empty table.
            $plain = false;
            if (get_class(editors_get_preferred_editor()) == 'textarea_texteditor') {
                $plain = true;
            }
            $output = $PAGE->get_renderer('mod_syllabus');
            $data['versionnoteseditor']['text'] = $output->render_versionnotesdefault($plain);
        }
    }
}
