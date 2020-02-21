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
 * Class for exporting teacher data.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Mélissa De Cristofaro <melissa.de.cristofaro@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_syllabus\external;
defined('MOODLE_INTERNAL') || die();

/**
 * Class for exporting teacher data.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Mélissa De Cristofaro <melissa.de.cristofaro@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class teacher_exporter extends \core\external\persistent_exporter {

    /**
     * Returns the specific class the persistent should be an instance of.
     *
     * @return string
     */
    protected static function define_class() {
        return 'mod_syllabus\\teacher';
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
     * Returns the definition of other contact propreties.
     *
     * @return array
     */
    public static function define_other_properties() {
        return array(
            'iseven_title' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_contactinformation' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_availability' => array(
                'type' => PARAM_BOOL
            ),
            'is_last' => array(
                'type' => PARAM_BOOL
            )
        );
    }

    /**
     * Returns other contact properties :
     *  iseven_title - boolean
     *  iseven_contactinformation - boolean
     *  iseven_availability - boolean
     *  is_last - boolean
     *
     * @param  renderer_base $output
     * @return array
     */
    protected function get_other_values(\renderer_base $output) {
        $otherproperties = new \stdClass();

        $otherproperties->iseven_title = false;
        $otherproperties->iseven_contactinformation = false;
        $otherproperties->iseven_availability = false;
        // This property will be changed later for the last element.
        $otherproperties->is_last = false;

        // Only title is not mandatory, the other fields are always shown.
        if (empty($this->persistent->get('title'))) {
            $otherproperties->iseven_availability = true;
        } else {
            $otherproperties->iseven_contactinformation = true;
        }

        return (array) $otherproperties;
    }
}
