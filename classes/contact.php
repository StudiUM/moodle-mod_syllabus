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
 * Class for loading/storing contact from the DB.
 *
 * @copyright  2019 David Ligne <david.ligne@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contact extends persistent {

    /** Table name for contact persistency */
    const TABLE = 'syllabus_contact';

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
            'duty' => array(
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
     * Count the number of contacts for a syllabus.
     *
     * @param  int $syllabusid The syllabus ID
     * @return int
     */
    public static function count_records_for_syllabus($syllabusid) {
        $filters = array('syllabusid' => $syllabusid);
        return self::count_records($filters);
    }

    /**
     * Get contacts  for a syllabus.
     *
     * @param  int $syllabusid The syllabus ID
     * @return contact[] array of contact
     */
    public static function list_contacts_for_syllabus($syllabusid) {
        $filters = array('syllabusid' => $syllabusid);
        return self::get_records($filters);
    }

    /**
     * Update contacts for a syllabus.
     *
     * @param  syllabus $syllabus The syllabus
     * @param  array $data data form
     */
    public static function update_contacts($syllabus, $data) {
        global $DB;
        $filters = array('syllabusid' => $syllabus->get('id'));
        $DB->delete_records(static::TABLE, $filters);

        $nbrecords = $data['nbrepeatcontacts'];
        if ($nbrecords > 0) {
            for ($i = 0; $i < $nbrecords; $i++) {
                $record = new \stdClass();
                $record->name = $data['contact_name'][$i];
                $record->duty = $data['contact_duty'][$i];
                $record->contactinformation = $data['contact_contactinformation'][$i];
                $record->availability = $data['contact_availability'][$i];
                $record->syllabusid = $syllabus->get('id');
                $contact = new contact(0, $record);
                $contact->create();
            }
        }
    }
}