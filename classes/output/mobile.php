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
 * Mobile output class for syllabus.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die();

use context_module;

/**
 * Mobile output class for syllabus.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Returns the syllabus course view for the mobile app.
     * @param  array $args Arguments from tool_mobile_get_content WS
     *
     * @return array       HTML, javascript and otherdata
     */
    public static function mobile_course_view($args) {
        global $OUTPUT, $DB, $PAGE;

        $args = (object) $args;
        $cm = get_coursemodule_from_id('syllabus', $args->cmid);

        // Capabilities check.
        require_login($args->courseid , false , $cm, true, true);

        $context = context_module::instance($cm->id);

        require_capability ('mod/syllabus:view', $context);

        $syllabuspersistent = new \mod_syllabus\syllabus($cm->instance);
        $output = $PAGE->get_renderer('mod_syllabus');
        $syllabuspage = new \mod_syllabus\output\view_syllabus_page($syllabuspersistent, $context);
        $data = $syllabuspage->export_for_template($output);
        $data->courseid = $args->courseid;
        $data->cmid = $args->cmid;

        // Pdf file.
        $pdfmanager = new \mod_syllabus\pdfmanager($context, $syllabuspersistent);
        $pdfile = $pdfmanager->getpdffile();
        $pdf = new \stdClass();
        // Files param.
        $files = [];
        if (!empty($pdfile)) {
            $pdfurl = $pdfmanager->getpdflink($pdfile);
            $pdf->filesize = $pdfile->get_filesize();
            $pdf->fileurl = $pdfurl->out();
            $pdf->filename = $pdfile->get_filename();
            $pdf->mimetype = $pdfile->get_mimetype();
            $pdf->timemodified = $pdfile->get_timemodified();
            $data->haspdf = true;
            $files = [$pdf];
        } else {
            $data->haspdf = false;
        }
        $data->pdf = $pdf;

        return array(
            'templates' => array(
                array(
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_syllabus/mobile_view_page', $data),
                ),
            ),
            'javascript' => '',
            'otherdata' => '',
            'files' => $files
        );
    }
}
