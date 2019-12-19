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
 * Class for loading/storing teacher from the DB.
 *
 * @copyright  2019 David Ligne <david.ligne@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class teacher extends persistent {

    /** Table name for teacher persistency */
    const TABLE = 'syllabus_teacher';

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
            'name' => array(
                'type' => PARAM_TEXT
            ),
            'title' => array(
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'contactinformation' => array(
                'type' => PARAM_TEXT
            ),
            'availability' => array(
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED,
            )
        );
    }

    /**
     * Count the number of teachers for a syllabus.
     *
     * @param  int $syllabusid The syllabus ID
     * @return int
     */
    public static function count_records_for_syllabus($syllabusid) {
        $filters = array('syllabusid' => $syllabusid);
        return self::count_records($filters);
    }

    /**
     * Get teachers  for a syllabus.
     *
     * @param  int $syllabusid The syllabus ID
     * @return teacher[] array of teacher
     */
    public static function list_teachers_for_syllabus($syllabusid) {
        $filters = array('syllabusid' => $syllabusid);
        return self::get_records($filters);
    }


    /**
     * Update teachers for a syllabus.
     *
     * @param  syllabus $syllabus The syllabus
     * @param  array $data data form
     */
    public static function update_teachers($syllabus, $data) {
        global $DB;
        $filters = array('syllabusid' => $syllabus->get('id'));
        $DB->delete_records(static::TABLE, $filters);

        $nbrecords = $data['nbrepeatteachers'];
        if ($nbrecords > 0) {
            for ($i = 0; $i < $nbrecords; $i++) {
                $record = new \stdClass();
                $record->name = $data['teacher_name'][$i];
                $record->title = $data['teacher_title'][$i];
                $record->contactinformation = $data['teacher_contactinformation'][$i];
                $record->availability = $data['teacher_availability'][$i];
                $record->syllabusid = $syllabus->get('id');
                $teacher = new teacher(0, $record);
                $teacher->create();
            }
        }
    }
}