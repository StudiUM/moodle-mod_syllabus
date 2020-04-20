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
 * Regfrmwkinstitpolicies class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Regfrmwkinstitpolicies class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class regfrmwkinstitpolicies extends rubric {

    /**
     * Build elements for rubric.
     */
    public function build_form_rubric() {
        // Rules.
        $this->form->addElement('html', $this->fieldset_html_start('rulespolicy', get_string('rulespolicy', 'mod_syllabus')));

        $this->form->addElement('static', 'studyregulations', get_string('studyregulations', 'mod_syllabus'),
            get_string('studyregulationsdefault', 'mod_syllabus'));
        $this->form->addElement('static', 'disabilitypolicy', get_string('disabilitypolicy', 'mod_syllabus'),
            get_string('disabilitypolicydefault', 'mod_syllabus'));

        $this->form->addElement('editor', 'policyothers', get_string('policyothers', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('policyothers', PARAM_CLEANHTML);
        $this->form->addHelpButton('policyothers', 'policyothers', 'mod_syllabus');

        $this->form->addElement('html', $this->fieldset_html_end());

        // Integrity.
        $this->form->addElement('html', $this->fieldset_html_start('integrity', get_string('integrity', 'mod_syllabus')));

        $url = get_string('integritysitedefault', 'mod_syllabus');
        $text = get_string('integritysite', 'mod_syllabus');
        $this->form->addElement('static', 'integritysite', $text, "<a href='$url' title='$text' target='_blank'>".$url."</a>");

        $url = get_string('regulationsexplaineddefault', 'mod_syllabus');
        $text = get_string('regulationsexplained', 'mod_syllabus');
        $this->form->addElement('static', 'regulationsexplained', $text,
                "<a href='$url' title='$text' target='_blank'>" . $url . "</a>");

        $this->form->addElement('editor', 'integrityothers', get_string('integrityothers', 'mod_syllabus'), self::EDITOROPTIONS);
        $this->form->setType('integrityothers', PARAM_CLEANHTML);
        $this->form->addHelpButton('integrityothers', 'integrityothers', 'mod_syllabus');

        $this->form->addElement('html', $this->fieldset_html_end());
    }
}