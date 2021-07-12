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

    /** @var array $customdata the form custom data. */
    protected $customdata;

    /** @var array The text area options. */
    const TEXTAREAOPTIONS = ['cols' => 64, 'rows' => 3];

    /** @var array editor options. */
    const EDITOROPTIONS = ['rows' => 10];

    /** @var array input options. */
    const INPUTOPTIONS = ['size' => '64'];

    /** @var array url input options. */
    const URLINPUTOPTIONS = ['size' => '50'];

    /** @var array required field options */
    const REQUIREDOPTIONS = ['data-required' => 'true'];

    /**
     * Constructor for rubric.
     *
     * @param Syllabus $syllabus
     * @param moodleform $form
     * @param array $customdata
     */
    public function __construct($syllabus, $form, $customdata) {
        $this->syllabus = $syllabus;
        $this->form = $form;
        $this->customdata = $customdata;
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
    public function fieldset_html_start($id, $label, $collapsed = false) {
        if ($collapsed) {
            $classcollapsed = ' collapsed';
            $ariaexpanded = 'false';
        } else {
            $classcollapsed = '';
            $ariaexpanded = 'true';
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

    /**
     * Get HTML add button.
     *
     * @param string $dataid
     * @param string $datarepeat
     * @return string HTML
     */
    public function button_add_html($dataid, $datarepeat = '') {
        $linkcontent = \html_writer::start_tag('i', ['class' => 'icon fa fa-plus-square fa-fw']);
        $linkcontent .= \html_writer::end_tag('i');
        $linkcontent .= ' ' . get_string('add');
        return \html_writer::link('#', $linkcontent, [
                    'class' => 'btn btn-secondary add addline',
                    'role' => 'button',
                    'data-id' => $dataid,
                    'data-repeat' => $datarepeat
                ]);
    }
}