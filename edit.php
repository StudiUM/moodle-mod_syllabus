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
 * Edit syllabus module instance
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('cmid', PARAM_INT);

$cm = get_coursemodule_from_id('syllabus', $id, 0, true, MUST_EXIST);
$context = context_module::instance($cm->id, MUST_EXIST);
$syllabus = $DB->get_record('syllabus', array('id' => $cm->instance), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);

$PAGE->set_url('/mod/syllabus/edit.php', array('cmid' => $cm->id));
$PAGE->set_title($course->shortname . ': ' . $syllabus->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($syllabus);

$data = new stdClass();
$data->id = $cm->id;

$mform = new \mod_syllabus\form\edit_form(null, array('data' => $data));

if ($mform->is_cancelled()) {
    redirect($redirecturl);

} else if ($formdata = $mform->get_data()) {
    // Ici traiter le formulaire.

    $params = array(
        'context' => $context,
        'objectid' => $syllabus->id
    );
    $event = \mod_syllabus\event\syllabus_updated::create($params);
    $event->add_record_snapshot('syllabus', $syllabus);
    $event->trigger();

    redirect($redirecturl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($syllabus->name));

$mform->display();

echo $OUTPUT->footer();
