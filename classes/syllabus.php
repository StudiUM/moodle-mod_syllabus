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

use coding_exception;
use context_course;
use context_user;
use comment;
use lang_string;

/**
 * Class for loading/storing syllabus from the DB.
 *
 * @copyright  2019 David Ligne <david.ligne@umontreal.ca>
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

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'syllabustype' => array(
                'choices' => array(
                    self::SYLLABUS_TYPE_OBJECTIVES,
                    self::SYLLABUS_TYPE_COMPETENCIES,
                ),
                'type' => PARAM_INT,
                'default' => self::SYLLABUS_TYPE_OBJECTIVES,
            ),
            'courseid' => array(
                'type' => PARAM_INT
            ),
            'title' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'creditnb' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'idnumber' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'moodlecourseurl' => array(
                'type' => PARAM_URL,
                'null' => NULL_ALLOWED
            ),
            'facultydept' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'trimester' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'courseyear' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
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
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'weeklyworkload' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'simpledescription' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'detaileddescription' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'placeinprogram' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'educationalintentions' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'learningobjectives' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'evaluationabsence' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'workdeposits' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'authorizedmaterial' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'languagequality' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'successthreshold' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
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
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'courseregistration' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'notetaking' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'mandatoryresourcedocuments' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'librarybooks' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'equipment' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'additionalresourcedocuments' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'websites' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'guides' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'additionalresourceothers' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'writtencommunicationcenter' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'successstudentcenter' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'sourcequote' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'udemlibraries' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'studentswithdisabilities' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'supportsuccessothers' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'studyregulations' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'disabilitypolicy' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'policyothers' => array(
                'type' => PARAM_TEXT
            ),
            'integritysite' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'regulationsexplained' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ),
            'integrityothers' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            )
        );
    }
}