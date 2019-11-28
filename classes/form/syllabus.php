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
    protected static $fieldstoremove = array('saveandcontinue', 'saveandpreview', 'saveandreturntocourse', 'cancel');

    /** @var array $tabs The array of form tabs. */
    protected $tabs = [
            ['id' => 'generalinformation', 'subrubric' => true],
            ['id' => 'learningtargeted', 'subrubric' => false],
            ['id' => 'sessionscalendar', 'subrubric' => true],
            ['id' => 'assessments', 'subrubric' => true],
            ['id' => 'reminders', 'subrubric' => true],
            ['id' => 'resources', 'subrubric' => true],
            ['id' => 'regfrmwkinstitpolicies', 'subrubric' => true],
        ];

    /**
     * Define the form - called by parent constructor.
     */
    public function definition() {
        global $PAGE;

        $this->_form->addElement('html', \html_writer::start_div('syllabus'));
        $this->_form->addElement('html', $this->get_tabs_header_html());
        $this->get_tabs_content_html();
        $this->_form->addElement('html', \html_writer::end_div());

        $buttonarray = array();
        $buttonarray[] = $this->_form->createElement('submit', 'saveandcontinue', get_string('saveandcontinue', 'mod_syllabus'));
        $buttonarray[] = $this->_form->createElement('submit', 'saveandpreview', get_string('saveandpreview', 'mod_syllabus'));
        $buttonarray[] = $this->_form->createElement('submit',
                'saveandreturntocourse', get_string('saveandreturntocourse', 'mod_syllabus'));
        $buttonarray[] = $this->_form->createElement('cancel');
        $this->_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $this->_form->closeHeaderBefore('buttonar');

        $PAGE->requires->js_call_amd('mod_syllabus/syllabusform', 'init');
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
            if ($key === 0 ) {
                $classlink .= " active";
            }
            $attributes = ['data-toggle' => 'tab', 'role' => 'tab', 'class' => $classlink];
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
            if ($key === 0 ) {
                $classdiv .= " active";
            }
            $attributes = ['id' => $tab['id'], 'role' => 'tabpanel'];
            $this->_form->addElement('html', \html_writer::start_div($classdiv, $attributes));
            if ($tab['subrubric'] === true) {
                $this->_form->addElement('html', $this->get_collapseexpand_html());
            }
            // Build elements.
            $class = "mod_syllabus\\output\\" .$tab['id'];
            $objectreflection = new \ReflectionClass($class);
            $rubric = $objectreflection->newInstanceArgs([$persistence, $this->_form]);
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
        $html .= \html_writer::link('#', get_string('expandall'), ['role' => 'button',
            'class' => 'collapseexpand expand-all', 'title' => get_string('clicktohideshow')]);
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
     * Return submitted data buttons if properly submitted or returns NULL if
     * there is no submitted data.
     *
     * @return array submitted data; empty if not submitted or cancelled
     */
    public function get_data_buttons() {
        $mform =& $this->_form;
        $buttons = [];
        if (!$this->is_cancelled() and $this->is_submitted()) {
            $data = $mform->exportValues();
            foreach (static::$fieldstoremove as $field) {
                if (isset($data[$field]) && $data[$field]) {
                    $buttons[$field] = $data[$field];
                }
            }
        }
        return $buttons;
    }
}
