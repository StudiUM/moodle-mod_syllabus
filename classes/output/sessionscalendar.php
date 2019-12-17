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

        // Set the nbrepeat.
        $this->form->addElement('hidden', 'nbrepeatsessioncal');
        $this->form->setType('nbrepeatsessioncal', PARAM_INT);
        if ($this->customdata['nbrepeat']['nbrepeatsessioncal'] !== null) {
            $nbrepeat = $this->customdata['nbrepeat']['nbrepeatsessioncal'];
        } else {
            $nbrepeat = $this->syllabus->count_sessionscalendar();
        }
        $this->form->setDefault('nbrepeatsessioncal', $nbrepeat);

        $textareaoptions = ['cols' => 22, 'rows' => 4];
        $table = \html_writer::start_tag('table', ['class' => 'generaltable fullwidth managedates', 'id' => 'calendarsession']);
        $table .= \html_writer::start_tag('thead');
        $table .= \html_writer::start_tag('tr');

        $headers = ['dates', 'titles', 'contents', 'activities', 'readingandworks', 'formativeevaluations', 'evaluations'];
        if ($this->syllabus->get('syllabustype') != \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            unset($headers[5]);
        }

        foreach ($headers as $header) {
            $table .= \html_writer::start_tag('th');
            $table .= get_string('sessionscalendar_' . $header, 'mod_syllabus');
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
        $link = \html_writer::link('#', $action,
                ['class' => 'deleteline', 'data-id' => 'calendarsession', 'data-repeat' => 'nbrepeatsessioncal']);

        for ($i = 0; $i < $nbrepeat; $i++) {
            $this->build_sessioncalendar_line($i, $link, $textareaoptions);
        }

        // Hidden for adding line.
        $this->build_sessioncalendar_line('newindex', $link, $textareaoptions, true);

        $this->form->addElement('html', '</tbody>');
        $this->form->addElement('html', '</table>');

        $this->form->addElement('html', '<div class="text-right">');
        $this->form->addElement('html', $this->button_add_html('calendarsession', 'nbrepeatsessioncal'));
        $this->form->addElement('html', '</div>');
    }

    /**
     * Build session calendar line (tr).
     *
     * @param string $index
     * @param string $linkdelete
     * @param string $textareaoptions
     * @param boolean $hidden
     * @return string HTML
     */
    protected function build_sessioncalendar_line($index, $linkdelete, $textareaoptions, $hidden = false) {
        $class = ($hidden) ? "class='hidden'" : '';
        $this->form->addElement('html', "<tr $class>");
        $startyearopt = ['startyear' => date('Y', strtotime('-1 year'))];
        $this->form->addElement('html', '<td>');
        $this->form->addElement('date_selector', 'calendarsession_date[' . $index . ']', '', $startyearopt);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareadate">');
        $this->form->addElement('textarea', 'calendarsession_title[' . $index . ']', '', $textareaoptions);
        $this->form->setType('titredate', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareadate">');
        $this->form->addElement('textarea', 'calendarsession_content[' . $index . ']', '', $textareaoptions);
        $this->form->setType('contenu', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareadate">');
        $this->form->addElement('textarea', 'calendarsession_activity[' . $index . ']', '', $textareaoptions);
        $this->form->setType('activite', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareadate">');
        $this->form->addElement('textarea', 'calendarsession_readingandworks[' . $index . ']', '', $textareaoptions);
        $this->form->setType('lecturetravaux', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        if ($this->syllabus->get('syllabustype') == \mod_syllabus\syllabus::SYLLABUS_TYPE_COMPETENCIES) {
            $this->form->addElement('html', '<td class="textareadate">');
            $this->form->addElement('textarea', 'calendarsession_formativeevaluations[' . $index . ']', '', $textareaoptions);
            $this->form->setType('evalform', PARAM_TEXT);
            $this->form->addElement('html', '</td>');
        }

        $this->form->addElement('html', '<td class="textareadate">');
        $this->form->addElement('textarea', 'calendarsession_evaluations[' . $index . ']', '', $textareaoptions);
        $this->form->setType('eval', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td>');
        $this->form->addElement('html', $linkdelete);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '</tr>');
    }
}