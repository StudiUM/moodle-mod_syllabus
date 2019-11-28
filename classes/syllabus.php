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
 * @copyright  2019 Université de Montréal
 * @author     2019 David Ligne <david.ligne@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus;

defined('MOODLE_INTERNAL') || die();

use \core\persistent;

/**
 * Class for loading/storing syllabus from the DB.
 *
 * @copyright  2019 Université de Montréal
 * @author     2019 David Ligne <david.ligne@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class syllabus extends persistent {

    /** Table name for syllabus persistency */
    const TABLE = 'syllabus';

    /** Syllabus types */
    const SYLLABUS_TYPE_OBJECTIVES = 1;

    /** Syllabus types */
    const SYLLABUS_TYPE_COMPETENCIES = 2;

    /** Training types */
    const TRAINING_TYPE_CAMPUSBASED = 1;

    /** Training types */
    const TRAINING_TYPE_ONLINE = 2;

    /** Training types */
    const TRAINING_TYPE_HYBDRID = 3;

    /** Training types */
    const TRAINING_TYPE_BIMODAL = 4;

    /** @var array Fields with editor. */
    protected static $fieldswitheditor = [
        'simpledescription',
        'detaileddescription',
        'placeinprogram',
        'educationalintentions',
        'learningobjectives',
        'policyothers',
        'integrityothers',
        'mandatoryresourcedocuments',
        'librarybooks',
        'equipment',
        'additionalresourcedocuments',
        'websites',
        'guides',
        'additionalresourceothers',
        'supportsuccessothers'
    ];

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'syllabustype' => array(
                'choices' => array(
                    self::SYLLABUS_TYPE_OBJECTIVES,
                    self::SYLLABUS_TYPE_COMPETENCIES,
                ),
                'type' => PARAM_INT,
                'default' => self::SYLLABUS_TYPE_OBJECTIVES,
            ),
            'course' => array(
                'type' => PARAM_INT,
            ),
            'name' => array(
                'type' => PARAM_TEXT
            ),
            'title' => array(
                'type' => PARAM_TEXT
            ),
            'intro' => array(
                'type' => PARAM_RAW
            ),
            'introformat' => array(
                'type' => PARAM_INT
            ),
            'creditnb' => array(
                'type' => PARAM_TEXT
            ),
            'idnumber' => array(
                'type' => PARAM_TEXT
            ),
            'moodlecourseurl' => array(
                'type' => PARAM_URL
            ),
            'facultydept' => array(
                'type' => PARAM_TEXT
            ),
            'trimester' => array(
                'type' => PARAM_TEXT
            ),
            'courseyear' => array(
                'type' => PARAM_TEXT
            ),
            'trainingtype'  => array(
                'choices' => array(
                    self::TRAINING_TYPE_CAMPUSBASED,
                    self::TRAINING_TYPE_ONLINE,
                    self::TRAINING_TYPE_HYBDRID,
                    self::TRAINING_TYPE_BIMODAL
                ),
                'type' => PARAM_INT,
                'default' => self::TRAINING_TYPE_CAMPUSBASED,
            ),
            'courseconduct' => array(
                'type' => PARAM_TEXT
            ),
            'weeklyworkload' => array(
                'type' => PARAM_TEXT
            ),
            'simpledescription' => array(
                'type' => PARAM_CLEANHTML,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'detaileddescription' => array(
                'type' => PARAM_CLEANHTML,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'placeinprogram' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'educationalintentions' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'learningobjectives' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'evaluationabsence' => array(
                'type' => PARAM_TEXT
            ),
            'workdeposits' => array(
                'type' => PARAM_TEXT
            ),
            'authorizedmaterial' => array(
                'type' => PARAM_TEXT
            ),
            'languagequality' => array(
                'type' => PARAM_TEXT
            ),
            'successthreshold' => array(
                'type' => PARAM_TEXT
            ),
            'registrationmodification' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'resignationdeadline' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'trimesterend' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'teachingevaluation' => array(
                'type' => PARAM_TEXT
            ),
            'courseregistration' => array(
                'type' => PARAM_TEXT
            ),
            'notetaking' => array(
                'type' => PARAM_TEXT
            ),
            'mandatoryresourcedocuments' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'librarybooks' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'equipment' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'additionalresourcedocuments' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'websites' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'guides' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'additionalresourceothers' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'writtencommunicationcenter' => array(
                'type' => PARAM_TEXT
            ),
            'successstudentcenter' => array(
                'type' => PARAM_TEXT
            ),
            'sourcequote' => array(
                'type' => PARAM_TEXT
            ),
            'udemlibraries' => array(
                'type' => PARAM_TEXT
            ),
            'studentswithdisabilities' => array(
                'type' => PARAM_TEXT
            ),
            'supportsuccessothers' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'studyregulations' => array(
                'type' => PARAM_TEXT
            ),
            'disabilitypolicy' => array(
                'type' => PARAM_TEXT
            ),
            'policyothers' => array(
                'type' => PARAM_CLEANHTML,
            ),
            'integritysite' => array(
                'type' => PARAM_TEXT
            ),
            'regulationsexplained' => array(
                'type' => PARAM_TEXT
            ),
            'integrityothers' => array(
                'type' => PARAM_CLEANHTML,
            )
        );
    }

    /**
     * Gets all the formatted properties.
     *
     * Formatted properties are properties which have a format associated with them.
     *
     * @return array Keys are property names, values are property format names.
     */
    public static function get_custom_formatted_properties() {
        $properties = static::$fieldswitheditor;

        $formatted = array();
        foreach ($properties as $property) {
            $propertyformat = $property . 'format';
            $formatted[$property] = $propertyformat;
        }

        return $formatted;
    }
}