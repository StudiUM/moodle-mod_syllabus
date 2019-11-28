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
 * Generalinformation class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Generalinformation class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generalinformation extends rubric {

    /**
     * Build elements for rubric.
     */
    public function build_form_rubric() {

        $this->form->addElement('html', $this->fieldset_html_start('cours', 'Cours'));
        $this->form->addElement('text', 'title', get_string('title', 'mod_syllabus'), array('size' => '64'));
        $this->form->setType('title', PARAM_TEXT);

        $this->form->addElement('text', 'creditnb', get_string('creditnb', 'mod_syllabus'), array('size' => '8'));
        $this->form->setType('creditnb', PARAM_TEXT);
        $this->form->addElement('text', 'idnumber', get_string('idnumber', 'mod_syllabus'), array('size' => '16'));
        $this->form->setType('idnumber', PARAM_TEXT);

        $this->form->addElement('text', 'moodlecourseurl', get_string('moodlecourseurl', 'mod_syllabus'), array('size' => '64'));
        $this->form->setType('moodlecourseurl', PARAM_TEXT);

        $this->form->addElement('text', 'facultydept', get_string('facultydept', 'mod_syllabus'), array('size' => '64'));
        $this->form->setType('facultydept', PARAM_TEXT);

        $this->form->addElement('text', 'trimester', get_string('trimester', 'mod_syllabus'), array('size' => '8'));
        $this->form->setType('trimester', PARAM_TEXT);

        $this->form->addElement('text', 'courseyear', get_string('courseyear', 'mod_syllabus'), array('size' => '8'));
        $this->form->setType('courseyear', PARAM_TEXT);

        $this->form->freeze(['title', 'idnumber', 'facultydept', 'trimester', 'courseyear']);

        $radio = array();
        $radio[] = $this->form->createElement('radio', 'trainingtype', null, get_string('campusbased', 'mod_syllabus'),
                \mod_syllabus\syllabus::TRAINING_TYPE_CAMPUSBASED);
        $radio[] = $this->form->createElement('radio', 'trainingtype', null, get_string('online', 'mod_syllabus'),
                \mod_syllabus\syllabus::TRAINING_TYPE_ONLINE);
        $radio[] = $this->form->createElement('radio', 'trainingtype', null, get_string('hybrid', 'mod_syllabus'),
                \mod_syllabus\syllabus::TRAINING_TYPE_HYBDRID);
        $radio[] = $this->form->createElement('radio', 'trainingtype', null, get_string('bimodal', 'mod_syllabus'),
                \mod_syllabus\syllabus::TRAINING_TYPE_BIMODAL);
        $this->form->addGroup($radio, 'trainingtype', get_string('trainingtype', 'mod_syllabus'), ' ', false);
        $this->form->setDefault('trainingtype', \mod_syllabus\syllabus::TRAINING_TYPE_CAMPUSBASED);

        $this->form->addElement('textarea', 'courseconduct', get_string('courseconduct', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('courseconduct', PARAM_TEXT);

        $this->form->addElement('textarea', 'weeklyworkload', get_string('weeklyworkload', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('weeklyworkload', PARAM_TEXT);
        $this->form->addElement('html', $this->fieldset_html_end());

        // Course description.
        $this->form->addElement('html', $this->fieldset_html_start('desccours', get_string('coursedesc', 'mod_syllabus')));
        $this->form->addElement('editor', 'simpledescription',
                get_string('simpledescription', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('simpledescription', PARAM_CLEANHTML);
        $this->form->addElement('editor', 'detaileddescription',
                get_string('detaileddescription', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('detaileddescription', PARAM_CLEANHTML);
        $this->form->addElement('editor', 'placeinprogram', get_string('placeinprogram', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('placeinprogram', PARAM_CLEANHTML);
        $this->form->addElement('html', $this->fieldset_html_end());
    }
}