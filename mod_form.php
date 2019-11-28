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
        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));
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
        $this->standard_intro_elements();
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
