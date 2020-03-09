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
 * Keeps track of upgrades to the workshop module
 *
 * @package    mod_syllabus
 * @category   upgrade
 * @copyright  2020 Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Performs upgrade of the database structure and data
 *
 * Workshop supports upgrades from version 1.9.0 and higher only. During 1.9 > 2.0 upgrade,
 * there are significant database changes.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_syllabus_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020031000) {

        // Define field writtencommunicationcenter to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('writtencommunicationcenter');

        // Conditionally launch drop field writtencommunicationcenter.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field successstudentcenter to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('successstudentcenter');

        // Conditionally launch drop field successstudentcenter.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field sourcequote to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('sourcequote');

        // Conditionally launch drop field sourcequote.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field udemlibraries to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('udemlibraries');

        // Conditionally launch drop field udemlibraries.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field studentswithdisabilities to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('studentswithdisabilities');

        // Conditionally launch drop field studentswithdisabilities.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field studyregulations to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('studyregulations');

        // Conditionally launch drop field studyregulations.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field disabilitypolicy to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('disabilitypolicy');

        // Conditionally launch drop field disabilitypolicy.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field integritysite to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('integritysite');

        // Conditionally launch drop field integritysite.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field regulationsexplained to be dropped from syllabus.
        $table = new xmldb_table('syllabus');
        $field = new xmldb_field('regulationsexplained');

        // Conditionally launch drop field regulationsexplained.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Syllabus savepoint reached.
        upgrade_mod_savepoint(true, 2020031000, 'syllabus');
    }

    return true;
}
