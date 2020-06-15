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
 * Structure step to restore one syllabus activity.
 *
 * @package   mod_syllabus
 * @category  backup
 * @copyright 2019 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Structure step to restore one syllabus activity.
 *
 * @package   mod_syllabus
 * @category  backup
 * @copyright 2019 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_syllabus_activity_structure_step extends restore_activity_structure_step {

    /**
     * Function that will return the structure to be processed by this restore_step.
     * Must return one array of @restore_path_element elements
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('syllabus', '/activity/syllabus');
        $paths[] = new restore_path_element('syllabus_calendarsession', '/activity/syllabus/calendarsessions/calendarsession');
        $paths[] = new restore_path_element('syllabus_contact', '/activity/syllabus/contacts/contact');
        $paths[] = new restore_path_element('syllabus_evaluation', '/activity/syllabus/evaluations/evaluation');
        $paths[] = new restore_path_element('syllabus_teacher', '/activity/syllabus/teachers/teacher');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process syllabus.
     *
     * @param stdClass $data
     */
    protected function process_syllabus($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Fill some fields from course information.
        $data = syllabus_fill_course_data($data);

        // Insert the syllabus record.
        $newitemid = $DB->insert_record('syllabus', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process syllabus calendar session.
     *
     * @param stdClass $data
     */
    protected function process_syllabus_calendarsession($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->syllabusid = $this->get_new_parentid('syllabus');

        $newitemid = $DB->insert_record('syllabus_calendarsession', $data);
    }

    /**
     * Process syllabus contact.
     *
     * @param stdClass $data
     */
    protected function process_syllabus_contact($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->syllabusid = $this->get_new_parentid('syllabus');

        $newitemid = $DB->insert_record('syllabus_contact', $data);
    }

    /**
     * Process syllabus evaluation.
     *
     * @param stdClass $data
     */
    protected function process_syllabus_evaluation($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->syllabusid = $this->get_new_parentid('syllabus');

        $newitemid = $DB->insert_record('syllabus_evaluation', $data);
    }

    /**
     * Process syllabus teacher.
     *
     * @param stdClass $data
     */
    protected function process_syllabus_teacher($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->syllabusid = $this->get_new_parentid('syllabus');

        $newitemid = $DB->insert_record('syllabus_teacher', $data);
    }

    /**
     * After execute function.
     */
    protected function after_execute() {
        // Add syllabus related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_syllabus', 'intro', null);
        $this->add_related_files('mod_syllabus', 'versionnotes', null);

        // Generate new pdf.
        $context = context_module::instance($this->task->get_moduleid());
        $syllabuspersistent = new \mod_syllabus\syllabus($this->task->get_activityid());
        $pdfmanager = new \mod_syllabus\pdfmanager($context, $syllabuspersistent);
        $pdfmanager->generate();
    }
}
