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
 * Define all the backup steps that will be used by the backup_syllabus_activity_task
 *
 * @package   mod_syllabus
 * @category  backup
 * @copyright 2019 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define all the backup steps that will be used by the backup_syllabus_activity_task
 *
 * @package   mod_syllabus
 * @category  backup
 * @copyright 2019 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_syllabus_activity_structure_step extends backup_activity_structure_step {
    /**
     * Function that will return the structure to be processed by this restore_step.
     * Must return one array of @restore_path_element elements
     */
    protected function define_structure() {
        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.

        $syllabus = new backup_nested_element('syllabus', array('id'), array(
            'usermodified', 'timecreated', 'timemodified', 'name', 'intro', 'introformat', 'syllabustype', 'title', 'creditnb',
            'idnumber', 'moodlecourseurl', 'facultydept', 'trimester', 'courseyear', 'trainingtype', 'courseconduct',
            'weeklyworkload', 'simpledescription', 'detaileddescription', 'placeinprogram', 'educationalintentions',
            'learningobjectives', 'evaluationabsence', 'workdeposits', 'authorizedmaterial', 'languagequality', 'successthreshold',
            'registrationmodification', 'resignationdeadline', 'trimesterend', 'teachingevaluation', 'courseregistration',
            'notetaking', 'mandatoryresourcedocuments', 'librarybooks', 'equipment', 'additionalresourcedocuments', 'websites',
            'guides', 'additionalresourceothers', 'writtencommunicationcenter', 'successstudentcenter', 'sourcequote',
            'udemlibraries', 'studentswithdisabilities', 'supportsuccessothers', 'studyregulations', 'disabilitypolicy',
            'policyothers', 'integritysite', 'regulationsexplained', 'integrityothers'));

        $calendarsessions = new backup_nested_element('calendarsessions');

        $calendarsession = new backup_nested_element('calendarsession', array('id'), array(
            'date', 'title', 'content', 'activity', 'readingandworks', 'formativeevaluations', 'evaluations'));

        $contacts = new backup_nested_element('contacts');

        $contact = new backup_nested_element('contact', array('id'), array(
            'name', 'duty', 'contactinformation', 'availability'));

        $evaluations = new backup_nested_element('evaluations');

        $evaluation = new backup_nested_element('evaluation', array('id'), array(
            'activities', 'learningobjectives', 'evaluationcriteria', 'evaluationdate', 'weightings'));

        $teachers = new backup_nested_element('teachers');

        $teacher = new backup_nested_element('teacher', array('id'), array(
            'name', 'title', 'contactinformation', 'availability'));

        // Build the tree.
        $syllabus->add_child($calendarsessions);
        $calendarsessions->add_child($calendarsession);

        $syllabus->add_child($contacts);
        $contacts->add_child($contact);

        $syllabus->add_child($evaluations);
        $evaluations->add_child($evaluation);

        $syllabus->add_child($teachers);
        $teachers->add_child($teacher);

        // Define sources.
        $syllabus->set_source_table('syllabus', array('id' => backup::VAR_ACTIVITYID));
        $calendarsession->set_source_table('syllabus_calendarsession', array('syllabusid' => backup::VAR_PARENTID));
        $contact->set_source_table('syllabus_contact', array('syllabusid' => backup::VAR_PARENTID));
        $evaluation->set_source_table('syllabus_evaluation', array('syllabusid' => backup::VAR_PARENTID));
        $teacher->set_source_table('syllabus_teacher', array('syllabusid' => backup::VAR_PARENTID));

        // Define file annotations.
        $syllabus->annotate_files('mod_syllabus', 'intro', null); // This file area does not have an itemid.

        // Return the root element (syllabus), wrapped into standard activity structure.
        return $this->prepare_activity_structure($syllabus);
    }
}
