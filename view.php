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
 * Syllabus module main user interface
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/repository/lib.php");

// Course module ID.
$id = optional_param('id', 0, PARAM_INT);
// Syllabus instance id.
$s  = optional_param('sy', 0, PARAM_INT);
$forceview = optional_param('forceview', 0, PARAM_INT);

// Two ways to specify the module.
if ($s) {
    $syllabus = $DB->get_record('syllabus', array('id' => $s), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('syllabus', $syllabus->id, $syllabus->course, true, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('syllabus', $id, 0, true, MUST_EXIST);
    $syllabus = $DB->get_record('syllabus', array('id' => $cm->instance), '*', MUST_EXIST);
}
// Syllabus just created, redirect to edit page.
if (empty($syllabus->timemodified) && $forceview) {
    $redirecturl = new moodle_url('/mod/syllabus/edit.php?', ['cmid' => $cm->id]);
    redirect($redirecturl);
}
$syllabuspersistent = new \mod_syllabus\syllabus($syllabus->id);

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

$PAGE->set_url('/mod/syllabus/view.php', array('id' => $cm->id));
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/syllabus:view', $context);

$params = array(
    'context' => $context,
    'objectid' => $syllabus->id
);
$event = \mod_syllabus\event\course_module_viewed::create($params);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('syllabus', $syllabus);
$event->trigger();

$PAGE->set_title($course->shortname . ': '. $syllabus->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($syllabus);

$output = $PAGE->get_renderer('mod_syllabus');

echo $output->header();

echo $output->heading(format_string($syllabus->name), 2);

echo $output->container_start('syllabus');

$pdfmanager = new \mod_syllabus\pdfmanager($context, $syllabuspersistent);
$pdfurl = $pdfmanager->getpdflink();

// View action buttons.
echo html_writer::start_tag('div', ['class' => 'view_action_buttons']);
if (!empty($pdfurl)) {
    $link = \html_writer::link($pdfurl, get_string('downloadpdf', 'syllabus'), ['class' => 'btn btn-secondary']);
    echo \html_writer::span($link, 'actionbuttons');
}
if (has_capability('mod/syllabus:addinstance', $context)) {
    $link = \html_writer::link(new moodle_url('/mod/syllabus/edit.php?', ['cmid' => $PAGE->cm->id]),
            get_string('enterdata', 'syllabus'), ['class' => 'btn btn-secondary']);
    echo \html_writer::span($link, 'actionbuttons');
}
echo html_writer::end_tag('div');

// Syllabus HTML View.
$syllabuspage = new \mod_syllabus\output\view_syllabus_page($syllabuspersistent, $context);
echo $output->render_section($syllabuspage, 'syllabus');

echo $output->container_end();

echo $output->footer();
