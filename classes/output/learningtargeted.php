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
 * Learningtargeted class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Learningtargeted class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class learningtargeted extends rubric {

    /**
     * Build elements for rubric.
     */
    public function build_form_rubric() {
        $label = get_string('educationalintentions', 'mod_syllabus');
        if ($this->syllabus->get('syllabustype') == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $label = get_string('educationalintentions_cmp', 'mod_syllabus');
        }
        $this->form->addElement('html', \html_writer::start_div('',
            array_merge(self::REQUIREDOPTIONS, array('data-editorfield' => 'educationalintentions'))));
        $this->form->addElement('editor', 'educationalintentions', $label, self::EDITOROPTIONS);
        $this->form->setType('educationalintentions', PARAM_CLEANHTML);
        $this->form->addRule('educationalintentions', get_string('required'), 'required', null, 'server');
        $this->form->addElement('html', \html_writer::end_div());

        $this->form->addElement('html', \html_writer::start_div('',
            array_merge(self::REQUIREDOPTIONS, array('data-editorfield' => 'learningobjectives'))));
        $this->form->addElement('editor', 'learningobjectives',
                get_string('learningobjectives', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('learningobjectives', PARAM_CLEANHTML);
        $this->form->addRule('learningobjectives', get_string('required'), 'required', null, 'server');
        $this->form->addElement('html', \html_writer::end_div());
    }
}