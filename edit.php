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
$rubric = optional_param('rubric', 'generalinformation', PARAM_TEXT);
$nbrepeatsessioncal = optional_param('nbrepeatsessioncal', null, PARAM_INT);
$nbrepeateval = optional_param('nbrepeatassessmentcal', null, PARAM_INT);

$cm = get_coursemodule_from_id('syllabus', $id, 0, true, MUST_EXIST);
$context = context_module::instance($cm->id, MUST_EXIST);
require_capability('mod/syllabus:addinstance', $context);

$syllabus = $DB->get_record('syllabus', array('id' => $cm->instance), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$url = new moodle_url("/mod/syllabus/edit.php", array('cmid' => $cm->id));
$redirecturl = new moodle_url("/mod/syllabus/view.php", array('id' => $cm->id));
require_login($course, false, $cm);

$PAGE->set_url('/mod/syllabus/edit.php', array('cmid' => $cm->id));
$PAGE->set_title($course->shortname . ': ' . $syllabus->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($syllabus);

// Set up the form.
$syllabuspersistent = new \mod_syllabus\syllabus($syllabus->id);
$formoptions = [
    'persistent' => $syllabuspersistent,
    'rubric' => $rubric,
    'nbrepeat' => [
        'nbrepeatsessioncal' => $nbrepeatsessioncal,
        'nbrepeatassessmentcal' => $nbrepeateval
    ]
];

$mform = new \mod_syllabus\form\syllabus($url, $formoptions);

// Form cancelled.
if ($mform->is_cancelled()) {
    redirect($redirecturl);
}
$alldata = $mform->get_all_data();
// Get form data.
$data = $mform->get_submitted_data();
if ($data) {
    // Update syllabus.
    $persistantdata = $syllabuspersistent->clean_record($data);
    $syllabuspersistent->from_record($persistantdata);
    $syllabuspersistent->update();

    $params = array(
        'context' => $context,
        'objectid' => $syllabus->id
    );
    // Update sessions calendar.
    \mod_syllabus\calendarsession::update_sessionscalendar($syllabuspersistent, $alldata);

    // Update assessments calendar.
    \mod_syllabus\evaluation::update_evaluations($syllabuspersistent, $alldata);

    $event = \mod_syllabus\event\syllabus_updated::create($params);
    $event->add_record_snapshot('syllabus', $syllabus);
    $event->trigger();
    if (isset($alldata['saveandpreview'])) {
        redirect($redirecturl);
    } else if (isset($alldata['saveandreturntocourse'])) {
        $redirecturlcourse = new moodle_url("/course/view.php", array('id' => $course->id));
        redirect($redirecturlcourse);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($syllabus->name));

$mform->display();

echo $OUTPUT->footer();
