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
 * Class for exporting Sessions Calendar data.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Mélissa De Cristofaro <melissa.de.cristofaro@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_syllabus\external;
defined('MOODLE_INTERNAL') || die();

/**
 * Class for exporting Sessions Calendar data.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Mélissa De Cristofaro <melissa.de.cristofaro@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sessionscalendar_exporter extends \core\external\persistent_exporter {

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
        $fields = array('title', 'content', 'activity', 'readingandworks', 'formativeevaluations', 'evaluations');
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
        return 'mod_syllabus\\calendarsession';
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
            'iseven_content' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_activity' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_readingandworks' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_formativeevaluations' => array(
                'type' => PARAM_BOOL
            ),
            'iseven_evaluations' => array(
                'type' => PARAM_BOOL
            )
        );
    }

    /**
     * Returns other contact properties :
     *  iseven_title - boolean
     *  iseven_content - boolean
     *  iseven_activity - boolean
     *  iseven_readingandworks - boolean
     *  iseven_formativeevaluations - boolean
     *  iseven_evaluations - boolean
     *
     * @param  renderer_base $output
     * @return array
     */
    protected function get_other_values(\renderer_base $output) {
        $otherproperties = new \stdClass();

        $otherproperties->iseven_title = false;
        $otherproperties->iseven_content = false;
        $otherproperties->iseven_activity = false;
        $otherproperties->iseven_readingandworks = false;
        $otherproperties->iseven_formativeevaluations = false;
        $otherproperties->iseven_evaluations = false;

        // Title is not mandatory, but content and activity are.
        if (!empty($this->persistent->get('title'))) {
            $otherproperties->iseven_content = true;
            $otherproperties->iseven_readingandworks = true;
        } else {
            $otherproperties->iseven_activity = true;
        }

        // Title and readingandworks are not mandatory : if they are both empty or both filled, formativeevaluations is odd.
        // If only one of them is filled, formativeevaluations is even.
        if ((empty($this->persistent->get('title')) && !empty($this->persistent->get('readingandworks')))
            || (!empty($this->persistent->get('title')) && empty($this->persistent->get('readingandworks')))) {
                $otherproperties->iseven_formativeevaluations = true;
        }

        if (($otherproperties->iseven_formativeevaluations && empty($this->persistent->get('formativeevaluations')))
            || !$otherproperties->iseven_formativeevaluations && !empty($this->persistent->get('formativeevaluations'))) {
            $otherproperties->iseven_evaluations = true;
        }

        return (array) $otherproperties;
    }
}
