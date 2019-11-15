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

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process syllabus
     *
     * @param stdClass $data
     */
    protected function process_syllabus($data) {
    }

    /**
     * After execute function.
     */
    protected function after_execute() {
    }
}
