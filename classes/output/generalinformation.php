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
 * Generalinformation class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Generalinformation class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generalinformation extends rubric {

    /**
     * Build elements for rubric.
     */
    public function build_form_rubric() {

        $this->form->addElement('html', $this->fieldset_html_start('cours', 'Cours'));
        $this->form->addElement('text', 'title', get_string('title', 'mod_syllabus'), self::INPUTOPTIONS);
        $this->form->setType('title', PARAM_TEXT);

        $this->form->addElement('text', 'creditnb', get_string('creditnb', 'mod_syllabus'), array('size' => '8'));
        $this->form->setType('creditnb', PARAM_TEXT);
        $this->form->addElement('text', 'idnumber', get_string('idnumber', 'mod_syllabus'), array('size' => '16'));
        $this->form->setType('idnumber', PARAM_TEXT);

        $this->form->addElement('text', 'moodlecourseurl', get_string('moodlecourseurl', 'mod_syllabus'), self::URLINPUTOPTIONS);
        $this->form->setType('moodlecourseurl', PARAM_URL);

        $this->form->addElement('text', 'facultydept', get_string('facultydept', 'mod_syllabus'), self::INPUTOPTIONS);
        $this->form->setType('facultydept', PARAM_TEXT);

        $this->form->addElement('text', 'trimester', get_string('trimester', 'mod_syllabus'), array('size' => '8'));
        $this->form->setType('trimester', PARAM_TEXT);

        $this->form->addElement('text', 'courseyear', get_string('courseyear', 'mod_syllabus'), array('size' => '8'));
        $this->form->setType('courseyear', PARAM_TEXT);

        $this->form->freeze(['title', 'idnumber', 'moodlecourseurl', 'facultydept', 'trimester', 'courseyear']);

        $radio = array();
        $radio[] = $this->form->createElement('radio', 'trainingtype', null, get_string('campusbased', 'mod_syllabus'),
                \mod_syllabus\syllabus::TRAINING_TYPE_CAMPUSBASED);
        $radio[] = $this->form->createElement('radio', 'trainingtype', null, get_string('online', 'mod_syllabus'),
                \mod_syllabus\syllabus::TRAINING_TYPE_ONLINE);
        $radio[] = $this->form->createElement('radio', 'trainingtype', null, get_string('hybrid', 'mod_syllabus'),
                \mod_syllabus\syllabus::TRAINING_TYPE_HYBDRID);
        $radio[] = $this->form->createElement('radio', 'trainingtype', null, get_string('bimodal', 'mod_syllabus'),
                \mod_syllabus\syllabus::TRAINING_TYPE_BIMODAL);
        $this->form->addGroup($radio, 'trainingtype', get_string('trainingtype', 'mod_syllabus'), ' ', false);
        $this->form->setDefault('trainingtype', \mod_syllabus\syllabus::TRAINING_TYPE_CAMPUSBASED);

        $this->form->addElement('textarea', 'courseconduct', get_string('courseconduct', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('courseconduct', PARAM_TEXT);

        $this->form->addElement('textarea', 'weeklyworkload', get_string('weeklyworkload', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('weeklyworkload', PARAM_TEXT);
        $this->form->addElement('html', $this->fieldset_html_end());

        // Teacher.
        // Set the nbrepeat.
        $this->form->addElement('hidden', 'nbrepeatteachers');
        $this->form->setType('nbrepeatteachers', PARAM_INT);
        if ($this->customdata['nbrepeat']['nbrepeatteachers'] !== null) {
            $nbrepeat = $this->customdata['nbrepeat']['nbrepeatteachers'];
        } else {
            $nbrepeat = $this->syllabus->count_teachers();
        }
        $this->form->setDefault('nbrepeatteachers', $nbrepeat);
        $this->form->addElement('html',
                $this->fieldset_html_start('teacher', get_string('teacher', 'mod_syllabus')));
        $textareaoptions = ['cols' => 22, 'rows' => 4];
        $table = \html_writer::start_tag('table', ['class' => 'generaltable fullwidth managedates', 'id' => 'teacher']);
        $table .= \html_writer::start_tag('thead');
        $table .= \html_writer::start_tag('tr');

        $headers = ['name', 'title', 'contactinformation', 'availability'];
        foreach ($headers as $header) {
            $table .= \html_writer::start_tag('th');
            $table .= get_string('teacher_' . $header, 'mod_syllabus');
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
            'data-id' => "teacher", 'data-repeat' => 'nbrepeatteachers']);

        for ($i = 0; $i < $nbrepeat; $i++) {
            $this->build_teacher_line($i, $link, $textareaoptions);
        }

        // Hidden for adding line.
        $this->build_teacher_line('newindex', $link, $textareaoptions, true);

        $this->form->addElement('html', '</tbody>');
        $this->form->addElement('html', '</table>');

        $this->form->addElement('html', '<div class="text-right">');
        $this->form->addElement('html', $this->button_add_html('teacher', 'nbrepeatteachers'));
        $this->form->addElement('html', '</div>');

        $this->form->addElement('html', $this->fieldset_html_end());

        // Contact.
        // Set the nbrepeat.
        $this->form->addElement('hidden', 'nbrepeatcontacts');
        $this->form->setType('nbrepeatcontacts', PARAM_INT);
        if ($this->customdata['nbrepeat']['nbrepeatcontacts'] !== null) {
            $nbrepeat = $this->customdata['nbrepeat']['nbrepeatcontacts'];
        } else {
            $nbrepeat = $this->syllabus->count_contacts();
        }
        $this->form->setDefault('nbrepeatcontacts', $nbrepeat);
        $this->form->addElement('html',
                $this->fieldset_html_start('contact', get_string('contact', 'mod_syllabus')));
        $textareaoptions = ['cols' => 22, 'rows' => 4];
        $table = \html_writer::start_tag('table', ['class' => 'generaltable fullwidth managedates', 'id' => 'contact']);
        $table .= \html_writer::start_tag('thead');
        $table .= \html_writer::start_tag('tr');

        $headers = ['name', 'duty', 'contactinformation', 'availability'];
        foreach ($headers as $header) {
            $table .= \html_writer::start_tag('th');
            $table .= get_string('contact_' . $header, 'mod_syllabus');
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
            'data-id' => "contact", 'data-repeat' => 'nbrepeatcontacts']);

        for ($i = 0; $i < $nbrepeat; $i++) {
            $this->build_contact_line($i, $link, $textareaoptions);
        }

        // Hidden for adding line.
        $this->build_contact_line('newindex', $link, $textareaoptions, true);

        $this->form->addElement('html', '</tbody>');
        $this->form->addElement('html', '</table>');

        $this->form->addElement('html', '<div class="text-right">');
        $this->form->addElement('html', $this->button_add_html('contact', 'nbrepeatcontacts'));
        $this->form->addElement('html', '</div>');

        $this->form->addElement('html', $this->fieldset_html_end());

        // Course description.
        $this->form->addElement('html', $this->fieldset_html_start('desccours', get_string('coursedesc', 'mod_syllabus')));
        $this->form->addElement('editor', 'simpledescription',
                get_string('simpledescription', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('simpledescription', PARAM_CLEANHTML);
        $this->form->disabledIf('simpledescription', null);

        $this->form->addElement('editor', 'detaileddescription',
                get_string('detaileddescription', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('detaileddescription', PARAM_CLEANHTML);
        $this->form->addElement('editor', 'placeinprogram', get_string('placeinprogram', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('placeinprogram', PARAM_CLEANHTML);
        $this->form->addElement('html', $this->fieldset_html_end());
    }

    /**
     * Build teacher line (tr).
     *
     * @param string $index
     * @param string $linkdelete
     * @param string $textareaoptions
     * @param boolean $hidden
     * @return string HTML
     */
    protected function build_teacher_line($index, $linkdelete, $textareaoptions, $hidden = false) {
        $class = ($hidden) ? "class='hidden'" : '';
        $this->form->addElement('html', "<tr $class>");

        $this->form->addElement('html', '<td class="personinputname">');
        $this->form->addElement('text', 'teacher_name[' . $index . ']', '', ['class' => 'personinputname']);
        $this->form->setType('teacher_name', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareaperson">');
        $this->form->addElement('textarea', 'teacher_title[' . $index . ']', '', $textareaoptions);
        $this->form->setType('teacher_title', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareaperson">');
        $this->form->addElement('textarea', 'teacher_contactinformation[' . $index . ']', '', $textareaoptions);
        $this->form->setType('teacher_contactinformation', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareaperson">');
        $this->form->addElement('textarea', 'teacher_availability[' . $index . ']', '', $textareaoptions);
        $this->form->setType('teacher_availability', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td>');
        $this->form->addElement('html', $linkdelete);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '</tr>');
    }

    /**
     * Build contact line (tr).
     *
     * @param string $index
     * @param string $linkdelete
     * @param string $textareaoptions
     * @param boolean $hidden
     * @return string HTML
     */
    protected function build_contact_line($index, $linkdelete, $textareaoptions, $hidden = false) {
        $class = ($hidden) ? "class='hidden'" : '';
        $this->form->addElement('html', "<tr $class>");

        $this->form->addElement('html', '<td class="personinputname">');
        $this->form->addElement('text', 'contact_name[' . $index . ']', '');
        $this->form->setType('contact_name', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareaperson">');
        $this->form->addElement('textarea', 'contact_duty[' . $index . ']', '', $textareaoptions);
        $this->form->setType('contact_title', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareaperson">');
        $this->form->addElement('textarea', 'contact_contactinformation[' . $index . ']', '', $textareaoptions);
        $this->form->setType('contact_contactinformation', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td class="textareaperson">');
        $this->form->addElement('textarea', 'contact_availability[' . $index . ']', '', $textareaoptions);
        $this->form->setType('contact_availability', PARAM_TEXT);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '<td>');
        $this->form->addElement('html', $linkdelete);
        $this->form->addElement('html', '</td>');

        $this->form->addElement('html', '</tr>');
    }
}