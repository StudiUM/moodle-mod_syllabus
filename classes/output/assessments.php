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
        // Subrubric assessment calendar.
        $this->form->addElement('html',
                $this->fieldset_html_start('assessmentcalendar', get_string('assessmentcalendar', 'mod_syllabus')));
        // Set the nbrepeat.
        $this->form->addElement('hidden', 'nbrepeatassessmentcal');
        $this->form->setType('nbrepeatassessmentcal', PARAM_INT);
        if ($this->customdata['nbrepeat']['nbrepeatassessmentcal'] !== null) {
            $nbrepeat = $this->customdata['nbrepeat']['nbrepeatassessmentcal'];
        } else {
            $nbrepeat = $this->syllabus->count_assessmentscalendar();
        }
        $this->form->setDefault('nbrepeatassessmentcal', $nbrepeat);

        $textareaoptions = ['cols' => 22, 'rows' => 4];
        $table = \html_writer::start_tag('table', ['class' => 'generaltable fullwidth managedates', 'id' => 'assessmentcalendar']);
        $table .= \html_writer::start_tag('thead');
        $table .= \html_writer::start_tag('tr');

        $headers = ['dates', 'activities', 'learningobjectives', 'evaluationcriteria', 'weightings'];
        if ($this->syllabus->get('syllabustype') != \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            unset($headers[2]);
        }

        foreach ($headers as $header) {
            $table .= \html_writer::start_tag('th');
            $table .= get_string('assessmentcalendar_' . $header, 'mod_syllabus');
            $table .= \html_writer::end_tag('th');
        }

        $table .= \html_writer::start_tag('th');
        $table .= \html_writer::end_tag('th');
        $table .= \html_writer::end_tag('tr');
        $table .= \html_writer::end_tag('thead');

        $table .= \html_writer::start_tag('tbody');

        $this->form->addElement('html', $table);
        $deletelabel = get_string('delete');
        $action = '<i class="icon fa fa-trash fa-fw " title="' . $deletelabel . '" aria-label="' . $deletelabel . '"></i>';
        $link = \html_writer::link('#', $action, ['class' => 'deleteline',
            'data-id' => "assessmentcalendar", 'data-repeat' => 'nbrepeatassessmentcal']);

        for ($i = 0; $i < $nbrepeat; $i++) {
            $this->build_assessmentscalendar_line($i, $link, $textareaoptions);
        }

        // Hidden for adding line.
        $this->build_assessmentscalendar_line('newindex', $link, $textareaoptions, true);

        $this->form->addElement('html', '</tbody>');
        $this->form->addElement('html', '</table>');

        $this->form->addElement('html', '<div class="text-right">');
        $this->form->addElement('html', $this->button_add_html('assessmentcalendar', 'nbrepeatassessmentcal'));
        $this->form->addElement('html', '</div>');
        $this->form->addElement('html', $this->fieldset_html_end());

        // Subrubric rules assessment.
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

    /**
     * Build assessments calendar line (tr).
     *
     * @param string $index
     * @param string $linkdelete
     * @param string $textareaoptions
     * @param boolean $hidden
     * @return string HTML
     */
    protected function build_assessmentscalendar_line($index, $linkdelete, $textareaoptions, $hidden = false) {
        $class = ($hidden) ? "class='hidden'" : '';
        $this->form->addElement('html', "<tr $class>");
        $startyearopt = ['startyear' => date('Y', strtotime('-1 year'))];
        $this->form->addElement('html', '<td>');
        $this->form->addElement('date_selector', 'assessmentcalendar_evaluationdate[' . $index . ']', '', $startyearopt);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareadate">');
        $this->form->addElement('textarea', 'assessmentcalendar_activities[' . $index . ']', '', $textareaoptions);
        $this->form->setType('assessmentcalendar_activities', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        if ($this->syllabus->get('syllabustype') == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $this->form->addElement('html', '<td class="textareadate">');
            $this->form->addElement('textarea', 'assessmentcalendar_learningobjectives[' . $index . ']', '', $textareaoptions);
            $this->form->setType('assessmentcalendar_learningobjectives', PARAM_TEXT);
            $this->form->addElement('html', '</td>');
        }

        $this->form->addElement('html', '<td class="textareadate">');
        $this->form->addElement('textarea', 'assessmentcalendar_evaluationcriteria[' . $index . ']', '', $textareaoptions);
        $this->form->setType('assessmentcalendar_evaluationcriteria', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareadate">');
        $this->form->addElement('textarea', 'assessmentcalendar_weightings[' . $index . ']', '', $textareaoptions);
        $this->form->setType('assessmentcalendar_weightings', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td>');
        $this->form->addElement('html', $linkdelete);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '</tr>');
    }
}