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

        $this->form->addElement('html', \html_writer::start_div('',
            array_merge(self::REQUIREDOPTIONS, array('data-editorfield' => 'mandatoryresourcedocuments'))));
        $this->form->addElement('editor', 'mandatoryresourcedocuments', get_string('mandatoryresourcedocuments', 'mod_syllabus'),
            self::EDITOROPTIONS);
        $this->form->setType('mandatoryresourcedocuments', PARAM_CLEANHTML);
        $this->form->addRule('mandatoryresourcedocuments', get_string('required'), 'required', null, 'server');
        $this->form->addElement('html', \html_writer::end_div());

        $this->form->addElement('html', \html_writer::start_div('',
            array_merge(self::REQUIREDOPTIONS, array('data-editorfield' => 'librarybooks'))));
        $this->form->addElement('editor', 'librarybooks', get_string('librarybooks', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('librarybooks', PARAM_CLEANHTML);
        $this->form->addRule('librarybooks', get_string('required'), 'required', null, 'server');
        $this->form->addElement('html', \html_writer::end_div());

        $this->form->addElement('editor', 'equipment', get_string('equipment', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('equipment', PARAM_CLEANHTML);

        $this->form->addElement('html', $this->fieldset_html_end());

        // Additionalresource.
        $this->form->addElement('html',
                $this->fieldset_html_start('additionalresources', get_string('additionalresources', 'mod_syllabus')));
        $this->form->addElement('editor', 'additionalresourcedocuments',
                get_string('additionalresourcedocuments', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('additionalresourcedocuments', PARAM_CLEANHTML);

        $this->form->addElement('editor', 'websites',
                get_string('websites', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('websites', PARAM_CLEANHTML);

        $this->form->addElement('editor', 'guides', get_string('guides', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('guides', PARAM_CLEANHTML);

        $this->form->addElement('editor', 'additionalresourceothers',
                get_string('additionalresourceothers', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('additionalresourceothers', PARAM_CLEANHTML);

        $this->form->addElement('html', $this->fieldset_html_end());

        // Supportsuccess.
        $urloptionsrequired = array_merge(self::URLINPUTOPTIONS, self::REQUIREDOPTIONS);

        $this->form->addElement('html',
                $this->fieldset_html_start('supportsuccess', get_string('supportsuccess', 'mod_syllabus')));

        $url = get_string('writtencommunicationcenterdefault', 'mod_syllabus');
        $text = get_string('writtencommunicationcenter', 'mod_syllabus');
        $this->form->addElement('static', 'writtencommunicationcenter', $text,
                "<a href='$url' title='$text' target='_blank'>" . $url . "</a>");

        $url = get_string('successstudentcenterdefault', 'mod_syllabus');
        $text = get_string('successstudentcenter', 'mod_syllabus');
        $this->form->addElement('static', 'successstudentcenter', $text,
                "<a href='$url' title='$text' target='_blank'>" . $url . "</a>");

        $url = get_string('sourcequotedefault', 'mod_syllabus');
        $text = get_string('sourcequote', 'mod_syllabus');
        $this->form->addElement('static', 'sourcequote', $text, "<a href='$url' title='$text' target='_blank'>".$url."</a>");

        $url = get_string('udemlibrariesdefault', 'mod_syllabus');
        $text = get_string('udemlibraries', 'mod_syllabus');
        $this->form->addElement('static', 'udemlibraries', $text, "<a href='$url' title='$text' target='_blank'>".$url."</a>");

        $url = get_string('studentswithdisabilitiesdefault', 'mod_syllabus');
        $text = get_string('studentswithdisabilities', 'mod_syllabus');
        $this->form->addElement('static', 'studentswithdisabilities', $text,
                "<a href='$url' title='$text' target='_blank'>" . $url . "</a>");

        $this->form->addElement('editor', 'supportsuccessothers',
                get_string('supportsuccessothers', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('supportsuccessothers', PARAM_CLEANHTML);

        $this->form->addElement('html', $this->fieldset_html_end());
    }
}