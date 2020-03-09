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
 * Class for exporting evaluations data.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Mélissa De Cristofaro <melissa.de.cristofaro@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_syllabus\external;
defined('MOODLE_INTERNAL') || die();

/**
 * Class for exporting evaluations data.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Mélissa De Cristofaro <melissa.de.cristofaro@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class evaluation_exporter extends \core\external\persistent_exporter {

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param \core\persistent $persistent
     * @param array $related
     */
    public function __construct($persistent, $related = array()) {
        parent::__construct($persistent, $related);

        // Keep the line returns and URLs in some fields.
        $regexurl = '#(http|https)://[a-z0-9._/-]+#i';
        $replacementurl = '<a href="$0" target="_blank">$0</a>';
        $fields = array('activities', 'learningobjectives', 'evaluationcriteria', 'weightings');
        foreach ($fields as $field) {
            $this->data->{$field} = str_replace("\n", "<br>", $this->data->{$field});
            $this->data->{$field} = preg_replace($regexurl, $replacementurl, $this->data->{$field});
        }
    }

    /**
     * Returns the specific class the persistent should be an instance of.
     *
     * @return string
     */
    protected static function define_class() {
        return 'mod_syllabus\\evaluation';
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
            'iseven_activities' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_learningobjectives' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_evaluationcriteria' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_weightings' => array(
                'type' => PARAM_BOOL
            ),
            'is_last' => array(
                'type' => PARAM_BOOL
            )
        );
    }

    /**
     * Returns other contact properties :
     *  iseven_activities - boolean
     *  iseven_learningobjectives - boolean
     *  iseven_evaluationcriteria - boolean
     *  iseven_weightings - boolean
     *  is_last - boolean
     *
     * @param  renderer_base $output
     * @return array
     */
    protected function get_other_values(\renderer_base $output) {
        $otherproperties = new \stdClass();

        $otherproperties->iseven_activities = false;
        $otherproperties->iseven_learningobjectives = true;
        $otherproperties->iseven_evaluationcriteria = false;
        $otherproperties->iseven_weightings = true;
        // This property will be changed later for the last element.
        $otherproperties->is_last = false;

        // Evaluationcriteria is not mandatory, all others are.
        if (empty($this->persistent->get('evaluationcriteria'))) {
            $otherproperties->iseven_weightings = false;
        }

        return (array) $otherproperties;
    }
}
