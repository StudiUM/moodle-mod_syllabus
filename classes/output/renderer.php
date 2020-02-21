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
 * Renderer class for mod_syllabus
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * Renderer class for mod_syllabus plugin.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /** @var stdClass $exporteddata Exported data ready to be used in templates. */
    protected $exporteddata = null;

    /**
     * Prepare the data to export to templates.
     *
     * @param view_syllabus_page $page
     * @return stdClass
     */
    protected function get_exporteddata(view_syllabus_page $page) {
        if (is_null($this->exporteddata)) {
            $this->exporteddata = $page->export_for_template($this);
        }
        return $this->exporteddata;
    }

    /**
     * Defer to template for a specific section.
     *
     * @param view_syllabus_page $page
     * @param string $section Name of the section (also name of the template file).
     * @return string html for the page
     */
    public function render_section(view_syllabus_page $page, $section) {
        $data = $this->get_exporteddata($page);
        return parent::render_from_template('mod_syllabus/'.$section, $data);
    }

    /**
     * Defer to template for a specific section of the PDF file.
     *
     * @param view_syllabus_page $page
     * @param string $section Name of the section (add "_pdf" to get the name of the template file).
     * @return string html for the page
     */
    public function render_section_pdf(view_syllabus_page $page, $section) {
        $data = $this->get_exporteddata($page);
        return parent::render_from_template('mod_syllabus/'.$section."_pdf", $data);
    }

    /**
     * Returns the default value for the versionnotes field.
     *
     * @param boolean $plain
     * @return string html
     */
    public function render_versionnotesdefault($plain) {
        $data = array('plaineditor' => $plain);
        return parent::render_from_template('mod_syllabus/versionnotesdefault', $data);
    }
}
