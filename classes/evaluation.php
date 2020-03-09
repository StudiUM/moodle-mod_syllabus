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
 * Class for syllabus persistence.
 *
 * @package    mod_syllabus
 * @copyright  2019 David Ligne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus;

defined('MOODLE_INTERNAL') || die();

use \core\persistent;

/**
 * Class for loading/storing evaluation from the DB.
 *
 * @copyright  2019 David Ligne <david.ligne@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class evaluation extends persistent {

    /** Table name for evaluation persistency */
    const TABLE = 'syllabus_evaluation';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'syllabusid' => array(
                'type' => PARAM_INT,
            ),
            'evaluationdate' => array(
                'type' => PARAM_INT
            ),
            'activities' => array(
                'type' => PARAM_CLEANHTML
            ),
            'learningobjectives' => array(
                'type' => PARAM_CLEANHTML,
                'default' => null,
                'null' => NULL_ALLOWED
            ),
            'evaluationcriteria' => array(
                'type' => PARAM_CLEANHTML
            ),
            'weightings' => array(
                'type' => PARAM_CLEANHTML
            )
        );
    }

    /**
     * Count the number of assessments calendar for a syllabus.
     *
     * @param  int $syllabusid The syllabus ID
     * @return int
     */
    public static function count_records_for_syllabus($syllabusid) {
        $filters = array('syllabusid' => $syllabusid);
        return self::count_records($filters);
    }

    /**
     * Get assessments calendar for a syllabus.
     *
     * @param  int $syllabusid The syllabus ID
     * @return evaluation[] array of evaluation
     */
    public static function list_evaluations_for_syllabus($syllabusid) {
        $filters = array('syllabusid' => $syllabusid);
        return self::get_records($filters, 'evaluationdate');
    }

    /**
     * Update assessments calendar for a syllabus.
     *
     * @param  syllabus $syllabus The syllabus
     * @param  array $data data form
     */
    public static function update_evaluations($syllabus, $data) {
        global $DB;
        $filters = array('syllabusid' => $syllabus->get('id'));
        $DB->delete_records(static::TABLE, $filters);

        $nbrecords = $data['nbrepeatassessmentcal'];
        if ($nbrecords > 0) {
            for ($i = 0; $i < $nbrecords; $i++) {
                $record = new \stdClass();
                $record->evaluationdate = $data['assessmentcalendar_evaluationdate'][$i];
                $record->activities = $data['assessmentcalendar_activities'][$i];
                $record->evaluationcriteria = $data['assessmentcalendar_evaluationcriteria'][$i];
                $record->weightings = $data['assessmentcalendar_weightings'][$i];
                if ($syllabus->get('syllabustype') == syllabus::SYLLABUS_TYPE_COMPETENCIES) {
                    $record->learningobjectives = $data['assessmentcalendar_learningobjectives'][$i];
                }
                $record->syllabusid = $syllabus->get('id');
                $sessioncal = new evaluation(0, $record);
                $sessioncal->create();
            }
        }
    }
}