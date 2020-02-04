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
}
