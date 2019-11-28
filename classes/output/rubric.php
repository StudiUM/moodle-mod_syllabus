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
 * Interface for syllabus rubric.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Interface for syllabus rubric.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class rubric {

    /** @var syllabus $syllabus Syllabus persistence. */
    protected $syllabus;

    /** @var moodleform $form the syllabus form. */
    protected $form;

    /** @var array The text area options. */
    const TEXTAREAOPTIONS = ['cols' => 64, 'rows' => 3];

    /**
     * Constructor for rubric.
     *
     * @param Syllabus $syllabus
     * @param moodleform $form
     */
    public function __construct($syllabus, $form) {
        $this->syllabus = $syllabus;
        $this->form = $form;
    }

    /**
     * Build elements for rubric.
     */
    public abstract function build_form_rubric();

    /**
     * Get HTML fieldset start.
     *
     * @param string $id
     * @param string $label
     * @param boolean $collapsed
     * @return string HTML fieldset
     */
    public function fieldset_html_start($id, $label, $collapsed = true) {
        $classcollapsed = ' collapsed';
        $ariaexpanded = 'true';
        if (!$collapsed) {
            $classcollapsed = '';
            $ariaexpanded = 'false';
        }
        $html = \html_writer::start_tag('fieldset', ['class' => 'clearfix collapsible' . $classcollapsed, 'id' => 'id_' . $id]);
        $html .= \html_writer::start_tag('legend', ['class' => 'ftoggler']);
        $html .= \html_writer::link('#', $label, ['class' => 'fheader',
                'role' => 'button',
                'aria-controls' => 'id_' . $id,
                'aria-expanded' => $ariaexpanded
            ]);
        $html .= \html_writer::end_tag('legend');
        $html .= \html_writer::start_div('fcontainer clearfix');

        return $html;
    }

    /**
     * Get HTML fieldset end.
     *
     * @return string
     */
    public function fieldset_html_end() {
        $html = \html_writer::end_div();
        $html .= \html_writer::end_tag('fieldset');

        return $html;
    }
}