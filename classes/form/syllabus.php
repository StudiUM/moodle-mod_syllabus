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
 * Edit syllabus content form.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_syllabus\form;

use core\form\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Edit syllabus content form.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class syllabus extends persistent {

    /** @var \mod_syllabus\syllabus persistent class for form */
    protected static $persistentclass = 'mod_syllabus\\syllabus';

    /** @var array Fields to remove when getting the final data. */
    protected static $fieldstoremove = array('saveandcontinue', 'saveandpreview', 'saveandreturntocourse', 'cancel', 'rubric');

    /** @var array $tabs The array of form tabs. */
    protected $tabs = [
            ['id' => 'generalinformation', 'subrubric' => true],
            ['id' => 'learningtargeted', 'subrubric' => false],
            ['id' => 'sessionscalendar', 'subrubric' => false],
            ['id' => 'assessments', 'subrubric' => true],
            ['id' => 'reminders', 'subrubric' => true],
            ['id' => 'resources', 'subrubric' => true],
            ['id' => 'regfrmwkinstitpolicies', 'subrubric' => true],
        ];

    /** @var string $currenttab The current tab. */
    protected $currenttab = '';

    /**
     * Define the form - called by parent constructor.
     */
    public function definition() {
        global $PAGE;

        $this->_form->addElement('hidden', 'rubric');
        $this->_form->setType('rubric', PARAM_TEXT);
        $this->_form->setDefault('rubric', 'generalinformation');

        $this->currenttab = $this->_customdata['rubric'];

        $this->_form->addElement('html', \html_writer::start_div('syllabus'));
        $this->_form->addElement('html', $this->get_tabs_header_html());
        $this->get_tabs_content_html();
        $this->_form->addElement('html', \html_writer::end_div());

        $buttonarray = array();
        $attrsubmit = array('data-submitbtn' => 'true');
        $buttonarray[] = $this->_form->createElement('button', 'saveandcontinue',
            get_string('saveandcontinue', 'mod_syllabus'), $attrsubmit);
        $buttonarray[] = $this->_form->createElement('button', 'saveandpreview',
            get_string('saveandpreview', 'mod_syllabus'), $attrsubmit);
        $buttonarray[] = $this->_form->createElement('button',
            'saveandreturntocourse', get_string('saveandreturntocourse', 'mod_syllabus'), $attrsubmit);
        $buttonarray[] = $this->_form->createElement('cancel');
        $this->_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $this->_form->closeHeaderBefore('buttonar');

        $this->_form->addElement('hidden', 'typeofsubmit');
        $this->_form->setType('typeofsubmit', PARAM_TEXT);
        $this->_form->setDefault('typeofsubmit', '');

        $PAGE->requires->js_call_amd('mod_syllabus/syllabusform', 'init',
                [
                    'configdatepicker' => $this->get_config_for_datepicker(),
                    'configmanageline' => [
                        ['name' => 'nbrepeatassessmentcal', 'identifier' => 'assessmentcalendar'],
                        ['name' => 'nbrepeatsessioncal', 'identifier' => 'calendarsession'],
                        ['name' => 'nbrepeatteachers', 'identifier' => 'teacher'],
                        ['name' => 'nbrepeatcontacts', 'identifier' => 'contact']
                    ],
                    'fieldfortypeofsubmit' => 'typeofsubmit'
                ]);
    }

    /**
     * Build tabs header.
     *
     * @return string $html
     */
    protected function get_tabs_header_html() {
        $html = '';
        $html .= \html_writer::start_tag('ul', ['id' => 'nav-tabs', 'class' => 'nav nav-tabs', 'role' => 'tablist']);
        foreach ($this->tabs as $key => $tab) {
            $html .= \html_writer::start_tag('li', ['class' => 'nav-item']);
            $classlink = "nav-link";
            if ($this->currenttab === $tab['id']) {
                $classlink .= " active";
            }
            $attributes = ['data-toggle' => 'tab', 'role' => 'tab', 'class' => $classlink, 'data-tab' => $tab['id']];
            $title = get_string($tab['id'], 'mod_syllabus');
            $html .= \html_writer::link('#' . $tab['id'], $title, $attributes);
            $html .= \html_writer::end_tag('li');
        }
        $html .= \html_writer::end_tag('ul');
        return $html;
    }

    /**
     * Build tabs content.
     */
    protected function get_tabs_content_html() {
        $persistence = $this->get_persistent();
        $this->_form->addElement('html', \html_writer::start_div('tab-content'));
        foreach ($this->tabs as $key => $tab) {
            $classdiv = "tab-pane fade in";
            if ($this->currenttab === $tab['id']) {
                $classdiv .= " active";
            }
            $title = get_string($tab['id'], 'mod_syllabus');
            $attributes = ['id' => $tab['id'], 'role' => 'tabpanel', 'data-tabname' => $title];
            $this->_form->addElement('html', \html_writer::start_div($classdiv, $attributes));
            if ($tab['subrubric'] === true) {
                $this->_form->addElement('html', $this->get_collapseexpand_html());
            }
            // Build elements.
            $class = "mod_syllabus\\output\\" .$tab['id'];
            $objectreflection = new \ReflectionClass($class);
            $rubric = $objectreflection->newInstanceArgs([$persistence, $this->_form, $this->_customdata]);
            $rubric->build_form_rubric();

            $this->_form->addElement('html', \html_writer::end_div());
        }
        $this->_form->addElement('html', \html_writer::end_div());
    }

    /**
     * Get HTML for collapse/expand button.
     *
     * @return string HTML collapseexpand button
     */
    protected function get_collapseexpand_html() {
        $html = \html_writer::start_div('colexpall-syllabus');
        $html .= \html_writer::start_div('collapsible-actions');
        $html .= \html_writer::link('#', get_string('collapseall'), ['role' => 'button',
            'class' => 'collapseexpand collapse-all', 'title' => get_string('clicktohideshow')]);
        $html .= \html_writer::end_div();
        $html .= \html_writer::end_div();

        return $html;
    }

    /**
     * Get the default data.
     *
     * This is the data that is prepopulated in the form at it loads, we automatically
     * fetch all the properties of the persistent however some needs to be converted
     * to map the form structure.
     *
     * Extend this class if you need to add more conversion.
     *
     * @return stdClass
     */
    protected function get_default_data() {
        $data = $this->get_persistent()->to_record();
        $properties = \mod_syllabus\syllabus::get_custom_formatted_properties();
        $allproperties = \mod_syllabus\syllabus::properties_definition();

        foreach ($data as $field => $value) {
            // Clean data if it is to be displayed in a form.
            if (isset($allproperties[$field]['type'])) {
                $data->$field = clean_param($data->$field, $allproperties[$field]['type']);
            }

            if (isset($properties[$field])) {
                $data->$field = array(
                    'text' => $data->$field,
                    'format' => FORMAT_HTML
                );
                unset($data->{$properties[$field]});
            }
        }

        // Set sessions calendar data.
        $sessionscalendar = $this->get_persistent()->get_sessionscalendar();
        $i = 0;
        foreach ($sessionscalendar as $record) {
            $date = "calendarsession_date[$i]";
            $data->$date = $record->get('date');
            $title = "calendarsession_title[$i]";
            $data->$title = $record->get('title');
            $content = "calendarsession_content[$i]";
            $data->$content = $record->get('content');
            $activity = "calendarsession_activity[$i]";
            $data->$activity = $record->get('activity');
            $readingandworks = "calendarsession_readingandworks[$i]";
            $data->$readingandworks = $record->get('readingandworks');
            if ($data->syllabustype === \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
                $formativeevaluations = "calendarsession_formativeevaluations[$i]";
                $data->$formativeevaluations = $record->get('formativeevaluations');
            }
            $evaluations = "calendarsession_evaluations[$i]";
            $data->$evaluations = $record->get('evaluations');
            $i++;
        }

        // Set assessments calendar data.
        $assessmentscalendar = $this->get_persistent()->get_assessmentscalendar();
        $i = 0;
        foreach ($assessmentscalendar as $record) {
            $date = "assessmentcalendar_evaluationdate[$i]";
            $data->$date = $record->get('evaluationdate');
            $activities = "assessmentcalendar_activities[$i]";
            $data->$activities = $record->get('activities');
            $evaluationcriteria = "assessmentcalendar_evaluationcriteria[$i]";
            $data->$evaluationcriteria = $record->get('evaluationcriteria');
            $weightings = "assessmentcalendar_weightings[$i]";
            $data->$weightings = $record->get('weightings');
            if ($data->syllabustype === \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
                $learningobjectives = "assessmentcalendar_learningobjectives[$i]";
                $data->$learningobjectives = $record->get('learningobjectives');
            }
            $i++;
        }

        // Set teachers data.
        $teachers = $this->get_persistent()->get_teachers();
        $i = 0;
        foreach ($teachers as $record) {
            $name = "teacher_name[$i]";
            $data->$name = $record->get('name');
            $title = "teacher_title[$i]";
            $data->$title = $record->get('title');
            $contactinformation = "teacher_contactinformation[$i]";
            $data->$contactinformation = $record->get('contactinformation');
            $availability = "teacher_availability[$i]";
            $data->$availability = $record->get('availability');
            $i++;
        }

        // Set contacts data.
        $contacts = $this->get_persistent()->get_contacts();
        $i = 0;
        foreach ($contacts as $record) {
            $name = "contact_name[$i]";
            $data->$name = $record->get('name');
            $title = "contact_duty[$i]";
            $data->$title = $record->get('duty');
            $contactinformation = "contact_contactinformation[$i]";
            $data->$contactinformation = $record->get('contactinformation');
            $availability = "contact_availability[$i]";
            $data->$availability = $record->get('availability');
            $i++;
        }

        return $data;
    }

    /**
     * Convert some fields.
     *
     * @param  stdClass $data The whole data set.
     * @return stdClass The amended data set.
     */
    protected static function convert_fields(\stdClass $data) {
        $properties = \mod_syllabus\syllabus::get_custom_formatted_properties();

        foreach ($data as $field => $value) {
            // Replace formatted properties.
            if (isset($properties[$field])) {
                $data->$field = $data->{$field}['text'];
            }
        }

        return $data;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if
     * there is no submitted data.
     *
     * @return array submitted data; empty if not submitted or cancelled
     */
    public function get_all_data() {
        $mform =& $this->_form;
        if (!$this->is_cancelled() and $this->is_submitted()) {
            return $mform->exportValues();
        }
        return [];
    }

    /**
     * Get config for date picker.
     *
     * @return array datepicker config.
     */
    protected function get_config_for_datepicker() {
        $calendar = \core_calendar\type_factory::get_calendar_instance();
        $defaulttimezone = date_default_timezone_get();

        $config = array(array(
            'firstdayofweek'    => $calendar->get_starting_weekday(),
            'mon'               => date_format_string(strtotime("Monday"), '%a', $defaulttimezone),
            'tue'               => date_format_string(strtotime("Tuesday"), '%a', $defaulttimezone),
            'wed'               => date_format_string(strtotime("Wednesday"), '%a', $defaulttimezone),
            'thu'               => date_format_string(strtotime("Thursday"), '%a', $defaulttimezone),
            'fri'               => date_format_string(strtotime("Friday"), '%a', $defaulttimezone),
            'sat'               => date_format_string(strtotime("Saturday"), '%a', $defaulttimezone),
            'sun'               => date_format_string(strtotime("Sunday"), '%a', $defaulttimezone),
            'january'           => date_format_string(strtotime("January 1"), '%B', $defaulttimezone),
            'february'          => date_format_string(strtotime("February 1"), '%B', $defaulttimezone),
            'march'             => date_format_string(strtotime("March 1"), '%B', $defaulttimezone),
            'april'             => date_format_string(strtotime("April 1"), '%B', $defaulttimezone),
            'may'               => date_format_string(strtotime("May 1"), '%B', $defaulttimezone),
            'june'              => date_format_string(strtotime("June 1"), '%B', $defaulttimezone),
            'july'              => date_format_string(strtotime("July 1"), '%B', $defaulttimezone),
            'august'            => date_format_string(strtotime("August 1"), '%B', $defaulttimezone),
            'september'         => date_format_string(strtotime("September 1"), '%B', $defaulttimezone),
            'october'           => date_format_string(strtotime("October 1"), '%B', $defaulttimezone),
            'november'          => date_format_string(strtotime("November 1"), '%B', $defaulttimezone),
            'december'          => date_format_string(strtotime("December 1"), '%B', $defaulttimezone)
        ));
        return $config;
    }
}
