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
 * Resources class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Resources class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resources extends rubric {

    /**
     * Build elements for rubric.
     */
    public function build_form_rubric() {
        // Mandatoryresources.
        $this->form->addElement('html',
                $this->fieldset_html_start('mandatoryresources', get_string('mandatoryresources', 'mod_syllabus')));
        $this->form->addElement('editor', 'mandatoryresourcedocuments',
                get_string('mandatoryresourcedocuments', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('mandatoryresourcedocuments', PARAM_CLEANHTML);

        $this->form->addElement('editor', 'librarybooks', get_string('librarybooks', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('librarybooks', PARAM_CLEANHTML);

        $this->form->addElement('editor', 'equipment', get_string('equipment', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('equipment', PARAM_CLEANHTML);

        $this->form->addElement('html', $this->fieldset_html_end());

        // Additionalresource.
        $this->form->addElement('html',
                $this->fieldset_html_start('additionalresources', get_string('additionalresources', 'mod_syllabus')));
        $this->form->addElement('editor', 'additionalresourcedocuments',
                get_string('additionalresourcedocuments', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('additionalresourcedocuments', PARAM_CLEANHTML);

        $this->form->addElement('editor', 'websites',
                get_string('websites', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('websites', PARAM_CLEANHTML);

        $this->form->addElement('editor', 'guides', get_string('guides', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('guides', PARAM_CLEANHTML);

        $this->form->addElement('editor', 'additionalresourceothers',
                get_string('additionalresourceothers', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('additionalresourceothers', PARAM_CLEANHTML);

        $this->form->addElement('html', $this->fieldset_html_end());

        // Supportsuccess.
        $this->form->addElement('html',
                $this->fieldset_html_start('supportsuccess', get_string('supportsuccess', 'mod_syllabus')));

        $this->form->addElement('text', 'writtencommunicationcenter',
                get_string('writtencommunicationcenter', 'mod_syllabus'), array('size' => '50'));
        $this->form->setType('writtencommunicationcenter', PARAM_URL);

        $this->form->addElement('text', 'successstudentcenter',
                get_string('successstudentcenter', 'mod_syllabus'), array('size' => '50'));
        $this->form->setType('successstudentcenter', PARAM_URL);

        $this->form->addElement('text', 'sourcequote', get_string('sourcequote', 'mod_syllabus'), array('size' => '50'));
        $this->form->setType('sourcequote', PARAM_URL);

        $this->form->addElement('text', 'udemlibraries', get_string('udemlibraries', 'mod_syllabus'), array('size' => '50'));
        $this->form->setType('udemlibraries', PARAM_URL);

        $this->form->addElement('text', 'studentswithdisabilities',
                get_string('studentswithdisabilities', 'mod_syllabus'), array('size' => '50'));
        $this->form->setType('studentswithdisabilities', PARAM_URL);

        $this->form->addElement('editor', 'supportsuccessothers',
                get_string('supportsuccessothers', 'mod_syllabus'), array('rows' => 10));
        $this->form->setType('supportsuccessothers', PARAM_CLEANHTML);

        $this->form->addElement('html', $this->fieldset_html_end());
    }
}