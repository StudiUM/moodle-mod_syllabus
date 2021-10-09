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
 * Sessionscalendar class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Sessionscalendar class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sessionscalendar extends rubric {

    /**
     * Build elements for rubric.
     */
    public function build_form_rubric() {
        global $OUTPUT;

        // Set the nbrepeat.
        $title = get_string('sessionscalendar', 'mod_syllabus');
        $this->form->addElement('hidden', 'nbrepeatsessioncal', null,
            array('data-morethanone' => 'true', 'data-tabname' => $title));
        $this->form->setType('nbrepeatsessioncal', PARAM_INT);
        if ($this->customdata['nbrepeat']['nbrepeatsessioncal'] !== null) {
            $nbrepeat = $this->customdata['nbrepeat']['nbrepeatsessioncal'];
        } else {
            $nbrepeat = $this->syllabus->count_sessionscalendar();
        }
        $this->form->setDefault('nbrepeatsessioncal', $nbrepeat);

        $textareaoptions = self::TEXTAREAOPTIONS;

        // Add css border if items in calendar.
        if ($nbrepeat == 0) {
            $this->form->addElement('html', '<div class="syllabus_repeated_items_block" id="calendarsession">');
        } else {
            $this->form->addElement('html', '<div class="syllabus_repeated_items_block greyborder" id="calendarsession">');
        }

        $deletelabel = get_string('deletethisline', 'mod_syllabus');
        $action = '<i class="icon fa fa-trash fa-fw " title="' . $deletelabel . '" aria-label="' . $deletelabel . '"></i>';
        $link = \html_writer::link('#', $action,
                ['class' => 'deleteline', 'data-id' => 'calendarsession', 'data-repeat' => 'nbrepeatsessioncal',
                'role' => 'button']);

        for ($i = 0; $i < $nbrepeat; $i++) {
            $this->build_sessioncalendar_item($i, $link, $textareaoptions);
        }

        // Hidden for adding line.
        $this->build_sessioncalendar_item('newindex', $link, $textareaoptions, true);
        $this->form->addElement('html', '</div>');

        $this->form->addElement('html', '<div class="text-right">');
        $this->form->addElement('html', $this->button_add_html('calendarsession', 'nbrepeatsessioncal'));
        $this->form->addElement('html', '</div>');
    }

    /**
     * Build session calendar item.
     *
     * @param string $index
     * @param string $linkdelete
     * @param string $textareaoptions
     * @param boolean $hidden
     * @return string HTML
     */
    protected function build_sessioncalendar_item($index, $linkdelete, $textareaoptions, $hidden = false) {
        $syllabustype = $this->syllabus->get('syllabustype');
        $class = ($hidden) ? "class='hidden syllabus_repeated_item'" : "class='syllabus_repeated_item'";
        $this->form->addElement('html', "<div $class>");

        $startyearopt = ['startyear' => date('Y', strtotime('-1 year'))];
        $this->form->addElement('date_selector', 'calendarsession_date[' . $index . ']',
            get_string('sessionscalendar_dates', 'mod_syllabus'), $startyearopt);
        $this->form->addHelpButton('calendarsession_date[' . $index . ']', 'sessionscalendar_dates', 'mod_syllabus');
        $this->form->addRule('calendarsession_date[' . $index . ']', get_string('required'), 'required', null, 'server');

        $this->form->addElement('textarea', 'calendarsession_title[' . $index . ']',
            get_string('sessionscalendar_titles', 'mod_syllabus'), ['cols' => 64, 'rows' => 1]);
        $this->form->setType('calendarsession_title', PARAM_TEXT);
        $this->form->addHelpButton('calendarsession_title[' . $index . ']', 'sessionscalendar_titles', 'mod_syllabus');

        $this->form->addElement('textarea', 'calendarsession_content[' . $index . ']',
            get_string('sessionscalendar_contents', 'mod_syllabus'), array_merge($textareaoptions, self::REQUIREDOPTIONS));
        $this->form->setType('calendarsession_content', PARAM_TEXT);
        $this->form->addHelpButton('calendarsession_content[' . $index . ']', 'sessionscalendar_contents', 'mod_syllabus');
        $this->form->addRule('calendarsession_content[' . $index . ']', get_string('required'), 'required', null, 'server');

        $this->form->addElement('textarea', 'calendarsession_activity[' . $index . ']',
            get_string('sessionscalendar_activities', 'mod_syllabus'), array_merge($textareaoptions, self::REQUIREDOPTIONS));
        $this->form->setType('calendarsession_activity', PARAM_TEXT);
        $this->form->addRule('calendarsession_activity[' . $index . ']', get_string('required'), 'required', null, 'server');
        if ($syllabustype == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $this->form->addHelpButton('calendarsession_activity[' . $index . ']',
                'sessionscalendar_activities_cmp', 'mod_syllabus');
        } else {
            $this->form->addHelpButton('calendarsession_activity[' . $index . ']',
                'sessionscalendar_activities', 'mod_syllabus');
        }

        $this->form->addElement('textarea', 'calendarsession_readingandworks[' . $index . ']',
            get_string('sessionscalendar_readingandworks', 'mod_syllabus'), $textareaoptions);
        $this->form->setType('calendarsession_readingandworks', PARAM_TEXT);
        $this->form->addHelpButton('calendarsession_readingandworks[' . $index . ']',
            'sessionscalendar_readingandworks', 'mod_syllabus');

        $this->form->addElement('textarea', 'calendarsession_formativeevaluations[' . $index . ']',
            get_string('sessionscalendar_formativeevaluations', 'mod_syllabus'), $textareaoptions);
        $this->form->setType('calendarsession_formativeevaluations', PARAM_TEXT);
        if ($syllabustype == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $this->form->addHelpButton('calendarsession_formativeevaluations[' . $index . ']',
                'sessionscalendar_formativeevaluations_cmp', 'mod_syllabus');
        } else {
            $this->form->addHelpButton('calendarsession_formativeevaluations[' . $index . ']',
                'sessionscalendar_formativeevaluations', 'mod_syllabus');
        }

        $this->form->addElement('textarea', 'calendarsession_evaluations[' . $index . ']',
            get_string('sessionscalendar_evaluations', 'mod_syllabus'), $textareaoptions);
        $this->form->setType('calendarsession_evaluations', PARAM_TEXT);
        $this->form->addHelpButton('calendarsession_evaluations[' . $index . ']',
            'sessionscalendar_evaluations', 'mod_syllabus');

        $this->form->addElement('html', '<div class="text-right">');
        $this->form->addElement('html', $linkdelete);
        $this->form->addElement('html', '</div>');

        $this->form->addElement('html', '</div>');
    }
}