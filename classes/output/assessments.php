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
        global $OUTPUT;

        $title = get_string('assessments', 'mod_syllabus');
        // Subrubric assessment calendar.
        $this->form->addElement('html',
                $this->fieldset_html_start('assessmentcalendar', get_string('assessmentcalendar', 'mod_syllabus')));
        // Set the nbrepeat.
        $this->form->addElement('hidden', 'nbrepeatassessmentcal', null,
                array('data-morethanone' => 'true', 'data-tabname' => $title));
        $this->form->setType('nbrepeatassessmentcal', PARAM_INT);
        if ($this->customdata['nbrepeat']['nbrepeatassessmentcal'] !== null) {
            $nbrepeat = $this->customdata['nbrepeat']['nbrepeatassessmentcal'];
        } else {
            $nbrepeat = $this->syllabus->count_assessmentscalendar();
        }
        $this->form->setDefault('nbrepeatassessmentcal', $nbrepeat);

        // Add css border if items in calendar.
        if ($nbrepeat == 0) {
            $this->form->addElement('html', '<div class="syllabus_repeated_items_block" id="assessmentcalendar">');
        } else {
            $this->form->addElement('html', '<div class="syllabus_repeated_items_block greyborder" id="assessmentcalendar">');
        }

        $deletelabel = get_string('deletethisline', 'mod_syllabus');
        $action = '<i class="icon fa fa-trash fa-fw " title="' . $deletelabel . '" aria-label="' . $deletelabel . '"></i>';
        $link = \html_writer::link('#', $action, ['class' => 'deleteline',
            'data-id' => "assessmentcalendar", 'data-repeat' => 'nbrepeatassessmentcal', 'role' => 'button']);

        for ($i = 0; $i < $nbrepeat; $i++) {
            $this->build_assessmentscalendar_item($i, $link);
        }

        // Hidden for adding line.
        $this->build_assessmentscalendar_item('newindex', $link, true);
        $this->form->addElement('html', '</div>');

        $this->form->addElement('html', '<div class="text-right">');
        $this->form->addElement('html', $this->button_add_html('assessmentcalendar', 'nbrepeatassessmentcal'));
        $this->form->addElement('html', '</div>');

        $this->form->addElement('html', $this->fieldset_html_end());

        // Subrubric rules assessment.
        $syllabustype = $this->syllabus->get('syllabustype');
        $this->form->addElement('html',
                $this->fieldset_html_start('rulesassessments', get_string('rulesassessments', 'mod_syllabus')));
        $label = get_string('evaluationabsence', 'mod_syllabus');
        $this->form->addElement('textarea', 'evaluationabsence', $label, self::TEXTAREAOPTIONS);
        $this->form->setType('evaluationabsence', PARAM_TEXT);
        $this->form->addHelpButton('evaluationabsence', 'evaluationabsence', 'mod_syllabus');

        $this->form->addElement('textarea', 'workdeposits',
                get_string('workdeposits', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('workdeposits', PARAM_TEXT);
        $this->form->addHelpButton('workdeposits', 'workdeposits', 'mod_syllabus');

        $this->form->addElement('textarea', 'authorizedmaterial',
                get_string('authorizedmaterial', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('authorizedmaterial', PARAM_TEXT);
        $this->form->addHelpButton('authorizedmaterial', 'authorizedmaterial', 'mod_syllabus');

        $this->form->addElement('textarea', 'languagequality',
                get_string('languagequality', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('languagequality', PARAM_TEXT);
        $this->form->addHelpButton('languagequality', 'languagequality', 'mod_syllabus');

        $this->form->addElement('textarea', 'successthreshold',
                get_string('successthreshold', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('successthreshold', PARAM_TEXT);
        $this->form->addHelpButton('successthreshold', 'successthreshold', 'mod_syllabus');

        $this->form->addElement('html', $this->fieldset_html_end());
    }

    /**
     * Build assessments calendar item.
     *
     * @param string $index
     * @param string $linkdelete
     * @param boolean $hidden
     * @return string HTML
     */
    protected function build_assessmentscalendar_item($index, $linkdelete, $hidden = false) {
        $syllabustype = $this->syllabus->get('syllabustype');
        $class = ($hidden) ? "class='hidden syllabus_repeated_item'" : "class='syllabus_repeated_item'";
        $this->form->addElement('html', "<div $class>");

        $startyearopt = array_merge(['startyear' => date('Y', strtotime('-1 year'))], self::REQUIREDOPTIONS);
        $this->form->addElement('date_selector', 'assessmentcalendar_evaluationdate[' . $index . ']',
            get_string('assessmentcalendar_dates', 'mod_syllabus'), $startyearopt);
        $this->form->addHelpButton('assessmentcalendar_evaluationdate[' . $index . ']', 'assessmentcalendar_dates', 'mod_syllabus');
        $this->form->addRule('assessmentcalendar_evaluationdate[' . $index . ']',
            get_string('required'), 'required', null, 'server');

        $this->form->addElement('textarea', 'assessmentcalendar_activities[' . $index . ']',
            get_string('assessmentcalendar_activities', 'mod_syllabus'), array_merge(self::TEXTAREAOPTIONS, self::REQUIREDOPTIONS));
        $this->form->setType('assessmentcalendar_activities', PARAM_TEXT);
        $this->form->addRule('assessmentcalendar_activities[' . $index . ']', get_string('required'), 'required', null, 'server');
        if ($syllabustype == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $this->form->addHelpButton('assessmentcalendar_activities[' . $index . ']',
                'assessmentcalendar_activities_cmp', 'mod_syllabus');
        } else {
            $this->form->addHelpButton('assessmentcalendar_activities[' . $index . ']',
                'assessmentcalendar_activities', 'mod_syllabus');
        }

        $this->form->addElement('textarea', 'assessmentcalendar_learningobjectives[' . $index . ']',
            get_string('assessmentcalendar_learningobjectives', 'mod_syllabus'),
            array_merge(self::TEXTAREAOPTIONS, self::REQUIREDOPTIONS));
        $this->form->setType('assessmentcalendar_learningobjectives', PARAM_TEXT);
        $this->form->addRule('assessmentcalendar_learningobjectives[' . $index . ']',
            get_string('required'), 'required', null, 'server');
        if ($syllabustype == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $this->form->addHelpButton('assessmentcalendar_learningobjectives[' . $index . ']',
                'assessmentcalendar_learningobjectives_cmp', 'mod_syllabus');
        } else {
            $this->form->addHelpButton('assessmentcalendar_learningobjectives[' . $index . ']',
                'assessmentcalendar_learningobjectives', 'mod_syllabus');
        }

        $this->form->addElement('textarea', 'assessmentcalendar_evaluationcriteria[' . $index . ']',
            get_string('assessmentcalendar_evaluationcriteria', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('assessmentcalendar_evaluationcriteria', PARAM_TEXT);
        if ($syllabustype == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $this->form->addHelpButton('assessmentcalendar_evaluationcriteria[' . $index . ']',
                'assessmentcalendar_evaluationcriteria_cmp', 'mod_syllabus');
        } else {
            $this->form->addHelpButton('assessmentcalendar_evaluationcriteria[' . $index . ']',
                'assessmentcalendar_evaluationcriteria', 'mod_syllabus');
        }

        $this->form->addElement('textarea', 'assessmentcalendar_weightings[' . $index . ']',
            get_string('assessmentcalendar_weightings', 'mod_syllabus'),
            array_merge(self::TEXTAREAOPTIONS, self::REQUIREDOPTIONS));
        $this->form->setType('assessmentcalendar_weightings', PARAM_TEXT);
        $this->form->addHelpButton('assessmentcalendar_weightings[' . $index . ']',
            'assessmentcalendar_weightings', 'mod_syllabus');
        $this->form->addRule('assessmentcalendar_weightings[' . $index . ']',
            get_string('required'), 'required', null, 'server');

        $this->form->addElement('html', '<div class="text-right">');
        $this->form->addElement('html', $linkdelete);
        $this->form->addElement('html', '</div>');

        $this->form->addElement('html', '</div>');
    }
}
