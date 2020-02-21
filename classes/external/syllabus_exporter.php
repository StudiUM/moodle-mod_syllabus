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
 * Class for exporting syllabus data.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_syllabus\external;
defined('MOODLE_INTERNAL') || die();

/**
 * Class for exporting syllabus data.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class syllabus_exporter extends \core\external\persistent_exporter {

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param \core\persistent $persistent
     * @param array $related
     */
    public function __construct($persistent, $related = array()) {
        parent::__construct($persistent, $related);

        $this->data->intro = file_rewrite_pluginfile_urls(
            $this->persistent->get('intro'),
            'pluginfile.php',
            $related['context']->id,
            'mod_syllabus',
            'intro',
            $this->persistent->get('id')
        );

        $this->data->versionnotes = file_rewrite_pluginfile_urls(
            $this->persistent->get('versionnotes'),
            'pluginfile.php',
            $related['context']->id,
            'mod_syllabus',
            'versionnotes',
            $this->persistent->get('id')
        );
    }

    /**
     * Returns the specific class the persistent should be an instance of.
     *
     * @return string
     */
    protected static function define_class() {
        return 'mod_syllabus\\syllabus';
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context'
        ];
    }

    /**
     * Returns the definition of other syllabus properties.
     *
     * @return array
     */
    public static function define_other_properties() {
        return array(
            'hasteacher' => array(
                'type' => PARAM_BOOL
            ),
            'teacherslist' => array(
                'type' => teacher_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'hascontact' => array(
                'type' => PARAM_BOOL
            ),
            'contactslist' => array(
                'type' => contact_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'trainingtypename' => array(
                'type' => PARAM_TEXT
            ),
            'hassessions' => array(
                'type' => PARAM_BOOL
            ),
            'sessionslist' => array(
                'type' => sessionscalendar_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'hasevaluation' => array(
                'type' => PARAM_BOOL
            ),
            'evaluationslist' => array(
                'type' => evaluation_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'hasimportantdates' => array(
                'type' => PARAM_BOOL
            ),
            'hasadditionalresources' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_evaluationabsence' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_workdeposits' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_authorizedmaterial' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_languagequality' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_successthreshold' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_registrationmodification' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_resignationdeadline' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_trimesterend' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_teachingevaluation' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_additionalresourcedocuments' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_websites' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_guides' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_additionalresourceothers' => array(
                'type' => PARAM_BOOL
            )
        );
    }

    /**
     * Returns other syllabus properties:
     * hasteacher - boolean
     * teacherslist - array
     * hascontact - boolean
     * contactslist - array
     * trainingtypename - string
     * hassessions - boolean
     * sessionslist - array
     * hasevaluation - boolean
     * evaluationslist - array
     * hasimportantdates - bool
     * hasadditionalresources - bool
     *
     * @param  renderer_base $output
     * @return array
     */
    protected function get_other_values(\renderer_base $output) {
        $othersyllabusproperties = new \stdClass();

        $othersyllabusproperties->hasteacher = false;
        $othersyllabusproperties->teacherslist = array();
        // Verifying if we have teachers.
        $nbteacher = $this->persistent->count_teachers();
        if ($nbteacher > 0) {
            $othersyllabusproperties->hasteacher = true;

            // Fetching teachers list to export.
            $teacherslist = array();
            $relateds = [
                'context' => $this->related['context']
            ];
            $teacherslist = $this->persistent->get_teachers();
            foreach ($teacherslist as $index => $teacher) {
                $exporter = new teacher_exporter($teacher, $relateds);
                $exportedteacher = $exporter->export($output);
                if ($index == count($teacherslist) - 1) {
                    $exportedteacher->is_last = true;
                }
                $othersyllabusproperties->teacherslist[] = $exportedteacher;
            }
        }

        $othersyllabusproperties->hascontact = false;
        $othersyllabusproperties->contactslist = array();
        // Verifying if we have contacts.
        $nbcontact = $this->persistent->count_contacts();
        if ($nbcontact > 0) {
            $othersyllabusproperties->hascontact = true;

            // Fetching contacts list to export.
            $contactslist = array();
            $relateds = [
                'context' => $this->related['context']
            ];
            $contactslist = $this->persistent->get_contacts();
            foreach ($contactslist as $index => $contact) {
                $exporter = new contact_exporter($contact, $relateds);
                $exportedcontact = $exporter->export($output);
                if ($index == count($contactslist) - 1) {
                    $exportedcontact->is_last = true;
                }
                $othersyllabusproperties->contactslist[] = $exportedcontact;
            }
        }

        // Fetching the training type name.
        $othersyllabusproperties->trainingtypename = $this->persistent->get_trainingtypename();

        $othersyllabusproperties->hassessions = false;
        $othersyllabusproperties->sessionslist = array();
        // Verifying if we have sessions.
        $nbsession = $this->persistent->count_sessionscalendar();
        if ($nbsession > 0) {
            $othersyllabusproperties->hassessions = true;

            // Fetching sessions calendar list to export.
            $sessionslist = array();
            $relateds = [
                'context' => $this->related['context']
            ];
            $sessionslist = $this->persistent->get_sessionscalendar();
            foreach ($sessionslist as $session) {
                $exporter = new sessionscalendar_exporter($session, $relateds);
                $othersyllabusproperties->sessionslist[] = $exporter->export($output);
            }
        }

        $othersyllabusproperties->hasevaluation = false;
        $othersyllabusproperties->evaluationslist = array();
        // Verifying if we have evaluations.
        $nbevaluation = $this->persistent->count_assessmentscalendar();
        if ($nbevaluation > 0) {
            $othersyllabusproperties->hasevaluation = true;

            // Fetching evaluations list to export.
            $evaluationslist = array();
            $relateds = [
                'context' => $this->related['context']
            ];
            $evaluationslist = $this->persistent->get_assessmentscalendar();
            foreach ($evaluationslist as $index => $evaluation) {
                $exporter = new evaluation_exporter($evaluation, $relateds);
                $exportedevaluation = $exporter->export($output);
                if ($index == count($evaluationslist) - 1) {
                    $exportedevaluation->is_last = true;
                }
                $othersyllabusproperties->evaluationslist[] = $exportedevaluation;
            }
        }

        // Verifying which fields appear as odd/even in the Evaluation - Rules Assessments section.
        $fields = array('evaluationabsence', 'workdeposits', 'authorizedmaterial', 'languagequality', 'successthreshold');
        $fieldiseven = false;
        foreach ($fields as $field) {
            $varname = "iseven_".$field;
            if (!empty($this->persistent->get($field))) {
                $othersyllabusproperties->$varname = $fieldiseven;
                $fieldiseven = !$fieldiseven;
            } else {
                // Initialise to default value.
                $othersyllabusproperties->$varname = false;
            }
        }

        // Verifying if we have important dates in the reminder section.
        $othersyllabusproperties->hasimportantdates = true;
        if (is_null($this->persistent->get('registrationmodification')) &&
            is_null($this->persistent->get('resignationdeadline')) &&
            is_null($this->persistent->get('trimesterend')) &&
            empty($this->persistent->get('teachingevaluation'))) {
                $othersyllabusproperties->hasimportantdates = false;
        }

        // Verifying which fields appear as odd/even in the Reminders - Important dates section (all non mandatory fields).
        $fields = array('registrationmodification', 'resignationdeadline', 'trimesterend', 'teachingevaluation');
        $fieldiseven = false;
        foreach ($fields as $field) {
            $varname = "iseven_".$field;
            if (!empty($this->persistent->get($field))) {
                $othersyllabusproperties->$varname = $fieldiseven;
                $fieldiseven = !$fieldiseven;
            } else {
                // Initialise to default value.
                $othersyllabusproperties->$varname = false;
            }
        }

        // Verifying if we have additional resources in the resources section.
        $othersyllabusproperties->hasadditionalresources = true;
        if (empty($this->persistent->get('additionalresourcedocuments')) &&
            empty($this->persistent->get('websites')) &&
            empty($this->persistent->get('guides')) &&
            empty($this->persistent->get('additionalresourceothers'))) {
                $othersyllabusproperties->hasadditionalresources = false;
        }

        // Verifying which fields appear as odd/even in the Resources - Complementary resources (all non mandatory fields).
        $fields = array('additionalresourcedocuments', 'websites', 'guides', 'additionalresourceothers');
        $fieldiseven = false;
        foreach ($fields as $field) {
            $varname = "iseven_".$field;
            if (!empty($this->persistent->get($field))) {
                $othersyllabusproperties->$varname = $fieldiseven;
                $fieldiseven = !$fieldiseven;
            } else {
                // Initialise to default value.
                $othersyllabusproperties->$varname = false;
            }
        }

        return (array) $othersyllabusproperties;
    }
}
