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
 * Mandatory public API of syllabus module
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * List of features supported in Syllabus module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function syllabus_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param array $data the data submitted from the reset course.
 * @return array status array
 */
function syllabus_reset_userdata($data) {
    return array();
}

/**
 * Add syllabus instance.
 * @param object $data
 * @param object $mform
 * @return int new syllabus instance id
 */
function syllabus_add_instance($data, $mform) {
    global $DB, $USER, $CFG;
    require_once($CFG->dirroot.'/user/lib.php');
    $cmid        = $data->coursemodule;
    // Fill course data.
    $data = syllabus_fill_course_data($data);
    $data->trainingtype = \mod_syllabus\syllabus::TRAINING_TYPE_CAMPUSBASED;
    $data->usermodified = $USER->id;

    $cmid = $data->coursemodule;
    $context = context_module::instance($cmid);

    if ($draftitemid = $data->versionnoteseditor['itemid']) {
        $data->versionnotes = file_save_draft_area_files($draftitemid, $context->id, 'mod_syllabus', 'versionnotes',
                0, syllabus_get_editor_options($context), $data->versionnoteseditor['text']);
        $data->versionnotesformat = $data->versionnoteseditor['format'];
    }

    // Prefill static data.
    $data = \mod_syllabus\syllabus::prefill($data);
    $data->id = $DB->insert_record('syllabus', $data);
    // Add editingteachers.
    if ($id = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'))) {
        $rawdata = user_get_participants($data->course, 0, 0, $id, 0, -1, '');
        foreach ($rawdata as $user) {
            $record = new \stdClass();
            $record->name = fullname($user);
            $record->contactinformation = $user->email;
            $record->syllabusid = $data->id;
            $teacher = new \mod_syllabus\teacher(0, $record);
            $teacher->create();
        }
        $rawdata->close();
    }
    // Add teachers (contacts).
    $teachers = [];
    if ($id = $DB->get_field('role', 'id', array('shortname' => 'teacher'))) {
        $rawdata = user_get_participants($data->course, 0, 0, $id, 0, -1, '');
        foreach ($rawdata as $user) {
            $record = new \stdClass();
            $record->name = fullname($user);
            $record->contactinformation = $user->email;
            $record->syllabusid = $data->id;
            $contact = new \mod_syllabus\contact(0, $record);
            $contact->create();
        }
        $rawdata->close();
    }

    // We need to use context now, so we need to make sure all needed info is already in db.
    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'syllabus', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update syllabus instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function syllabus_update_instance($data, $mform) {
    global $CFG, $DB;

    $cmid        = $data->coursemodule;
    $context = context_module::instance($cmid);

    if ($draftitemid = $data->versionnoteseditor['itemid']) {
        $data->versionnotes = file_save_draft_area_files($draftitemid, $context->id, 'mod_syllabus', 'versionnotes',
                0, syllabus_get_editor_options($context), $data->versionnoteseditor['text']);
        $data->versionnotesformat = $data->versionnoteseditor['format'];
    }

    $data->timemodified = time();
    $data->id           = $data->instance;
    $DB->update_record('syllabus', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'syllabus', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Delete syllabus instance.
 * @param int $id
 * @return bool true
 */
function syllabus_delete_instance($id) {
    global $DB;

    if (!$syllabus = $DB->get_record('syllabus', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('syllabus', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'syllabus', $syllabus->id, null);

    $DB->delete_records('syllabus', array('id' => $syllabus->id));

    return true;
}

/**
 * Given a coursemodule object, this function returns the extra
 * information needed to print this activity in various places.
 *
 * If syllabus needs to be displayed inline we store additional information
 * in customdata, so functions {@link syllabus_cm_info_dynamic()} and
 * {@link syllabus_cm_info_view()} do not need to do DB queries
 *
 * @param cm_info $cm
 * @return cached_cm_info info
 */
function syllabus_get_coursemodule_info($cm) {
    global $DB;
    if (!($syllabus = $DB->get_record('syllabus', array('id' => $cm->instance),
            'id, name'))) {
        return null;
    }
    $cminfo = new cached_cm_info();
    $cminfo->name = $syllabus->name;
    $cminfo->customdata = null;
    return $cminfo;
}

/**
 * Sets dynamic information about a course module
 *
 * This function is called from cm_info when displaying the module
 * mod_syllabus can be displayed inline on course page and therefore have no course link
 *
 * @param cm_info $cm
 */
function syllabus_cm_info_dynamic(cm_info $cm) {
    if ($cm->customdata) {
        // The field 'customdata' is not empty IF AND ONLY IF we display contens inline.
        $cm->set_no_view_link();
    }
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $syllabus     syllabus object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 */
function syllabus_view($syllabus, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $syllabus->id
    );

    $event = \mod_syllabus\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('syllabus', $syllabus);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Extends the settings navigation with the syllabus settings

 * This function is called when the context for the page is a syllabus module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $syllabusnode {@link navigation_node}
 */
function syllabus_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $syllabusnode=null) {
    global $PAGE;

    if (has_capability('mod/syllabus:addinstance', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/syllabus/edit.php', array('cmid' => $PAGE->cm->id));
        $syllabusnode->add(get_string('enterdata', 'syllabus'), $url, settings_navigation::TYPE_SETTING);
    }
}

/**
 * Get course session.
 *
 * @param string $idnumber
 * @return string Session
 */
function udem_get_session($idnumber) {
    global $CFG;
    $matches = array();
    $trimestre = array('H' => 'winter', 'E' => 'summer', 'A' => 'fall');
    if (preg_match('/([\-][AHE][0-9]{2})$/', $idnumber, $matches)) {
        $session = substr($matches[1], -3, 1);
        return $trimestre[$session];
    }
    return null;
}

/**
 * Returns the option for the editor to use in the activity parameters form.
 * @param context $context
 * @return array
 */
function syllabus_get_editor_options($context) {
    global $CFG;
    return array('subdirs' => 1, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => -1, 'changeformat' => 1, 'context' => $context,
        'noclean' => 1, 'trusttext' => 0);
}

/**
 * Serve the files.
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function mod_syllabus_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    // Only serve the file if the user can access the course and course module.
    require_login($course, false, $cm);

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_syllabus/$filearea/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Fill some initial data from the course, in the syllabus instance.
 * @param stdClass $data
 * @return stdClass
 */
function syllabus_fill_course_data($data) {
    $course = get_course($data->course);
    $category = \core_course_category::get($course->category);
    $data->title = $course->fullname;
    $data->idnumber = $course->idnumber;
    $data->facultydept = $category->get_nested_name(false);
    if ($date = udem_get_session_date($course->idnumber)) {
        $d = new \DateTime();
        $d->setTimestamp($date);
        $data->courseyear = $d->format('Y');
    }
    if ($session = udem_get_session($course->idnumber)) {
        $data->trimester = get_string($session, 'mod_syllabus');
    }
    $data->simpledescription = $course->summary;
    $urlcourse = new \moodle_url("/course/view.php", array('id' => $data->course));
    $data->moodlecourseurl = $urlcourse->out();

    return $data;
}
