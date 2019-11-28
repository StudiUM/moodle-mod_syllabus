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
 * Assessments class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Assessments class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assessments extends rubric {

    /**
     * Build elements for rubric.
     */
    public function build_form_rubric() {
        $this->form->addElement('html',
                $this->fieldset_html_start('rulesassessments', get_string('rulesassessments', 'mod_syllabus')));
        $label = get_string('evaluationabsence', 'mod_syllabus');

        if ($this->syllabus->get('syllabustype') == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $label = get_string('evaluationabsence_cmp', 'mod_syllabus');
        }
        $this->form->addElement('textarea', 'evaluationabsence', $label, self::TEXTAREAOPTIONS);
        $this->form->setType('evaluationabsence', PARAM_TEXT);

        $this->form->addElement('textarea', 'workdeposits',
                get_string('workdeposits', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('workdeposits', PARAM_TEXT);

        $this->form->addElement('textarea', 'authorizedmaterial',
                get_string('authorizedmaterial', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('authorizedmaterial', PARAM_TEXT);

        $this->form->addElement('textarea', 'languagequality',
                get_string('languagequality', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('languagequality', PARAM_TEXT);

        $this->form->addElement('textarea', 'successthreshold',
                get_string('successthreshold', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('successthreshold', PARAM_TEXT);

        $this->form->addElement('html', $this->fieldset_html_end());
    }
}