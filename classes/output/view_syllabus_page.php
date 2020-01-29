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
 * Class containing the View Syllabus Page.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_syllabus\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;
use mod_syllabus\external\syllabus_exporter;

/**
 * Class containing the View Syllabus Page.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_syllabus_page implements renderable, templatable {

    /** @var syllabus $syllabus The syllabus instance. */
    protected $syllabus = null;

    /** @var context $context The context. */
    protected $context = null;

    /**
     * Construct this renderable.
     *
     * @param syllabus $syllabus The syllabus instance.
     * @param context $context The context instance.
     */
    public function __construct($syllabus, $context) {
        $this->syllabus = $syllabus;
        $this->context = $context;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $relateds = [
            'context' => $this->context
        ];

        $exporter = new syllabus_exporter($this->syllabus, $relateds);
        $record = $exporter->export($output);

        return $record;
    }
}
