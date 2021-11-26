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
 * Class for loading/storing calendar sessions from the DB.
 *
 * @copyright  2019 David Ligne <david.ligne@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendarsession extends persistent {

    /** Table name for calendarsession persistency */
    const TABLE = 'syllabus_calendarsession';

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
            'date' => array(
                'type' => PARAM_INT
            ),
            'title' => array(
                'type' => PARAM_CLEANHTML
            ),
            'content' => array(
                'type' => PARAM_CLEANHTML
            ),
            'activity' => array(
                'type' => PARAM_CLEANHTML
            ),
            'readingandworks' => array(
                'type' => PARAM_CLEANHTML
            ),
            'formativeevaluations' => array(
                'type' => PARAM_CLEANHTML,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'evaluations' => array(
                'type' => PARAM_CLEANHTML
            )
        );
    }

    /**
     * Count the number of sessions calendar for a syllabus.
     *
     * @param  int $syllabusid The syllabus ID
     * @return int
     */
    public static function count_records_for_syllabus($syllabusid) {
        $filters = array('syllabusid' => $syllabusid);
        return self::count_records($filters);
    }

    /**
     * Get sessions calendar for a syllabus.
     *
     * @param  int $syllabusid The syllabus ID
     * @return calendarsession[] array of calendarsession
     */
    public static function list_sessionscalendar_for_syllabus($syllabusid) {
        $filters = array('syllabusid' => $syllabusid);
        return self::get_records($filters, 'date');
    }

    /**
     * Update sessions calendar for a syllabus.
     *
     * @param  syllabus $syllabus The syllabus
     * @param  array $data data form
     */
    public static function update_sessionscalendar($syllabus, $data) {
        global $DB;
        $filters = array('syllabusid' => $syllabus->get('id'));
        $DB->delete_records(static::TABLE, $filters);

        $nbrecords = $data['nbrepeatsessioncal'];
        if ($nbrecords > 0) {
            for ($i = 0; $i < $nbrecords; $i++) {
                $record = new \stdClass();
                $record->date = $data['calendarsession_date'][$i];
                $record->title = $data['calendarsession_title'][$i];
                $record->content = $data['calendarsession_content'][$i];
                $record->activity = $data['calendarsession_activity'][$i];
                $record->readingandworks = $data['calendarsession_readingandworks'][$i];
                $record->formativeevaluations = $data['calendarsession_formativeevaluations'][$i];
                $record->evaluations = $data['calendarsession_evaluations'][$i];
                $record->syllabusid = $syllabus->get('id');
                $sessioncal = new calendarsession(0, $record);
                $sessioncal->create();
            }
        }
    }
}
