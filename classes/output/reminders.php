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
 * Reminders class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Reminders class for syllabusform.
 *
 * @package    mod_syllabus
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reminders extends rubric {

    /**
     * Build elements for rubric.
     */
    public function build_form_rubric() {

        $this->form->addElement('html', $this->fieldset_html_start('importantdates', get_string('importantdates', 'mod_syllabus')));
        $startyearopt = ['startyear' => date('Y', strtotime('-1 year')), 'optional' => true];
        $this->form->addElement('date_selector', 'registrationmodification',
                get_string('registrationmodification', 'mod_syllabus'), $startyearopt);

        $this->form->addElement('date_selector', 'resignationdeadline', get_string('resignationdeadline', 'mod_syllabus'),
                $startyearopt);

        $this->form->addElement('date_selector', 'trimesterend', get_string('trimesterend', 'mod_syllabus'), $startyearopt);

        $this->form->addElement('textarea', 'teachingevaluation',
                get_string('teachingevaluation', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('teachingevaluation', PARAM_TEXT);

        $this->form->addElement('html', $this->fieldset_html_end());

        // Technolgies use.
        $this->form->addElement('html', $this->fieldset_html_start('techuses', get_string('techuses', 'mod_syllabus')));

        $this->form->addElement('textarea', 'courseregistration', get_string('courseregistration', 'mod_syllabus'),
            array_merge(self::TEXTAREAOPTIONS, self::REQUIREDOPTIONS));
        $this->form->setType('courseregistration', PARAM_TEXT);
        $this->form->addRule('courseregistration', get_string('required'), 'required', null, 'server');

        $this->form->addElement('textarea', 'notetaking',
                get_string('notetaking', 'mod_syllabus'), self::TEXTAREAOPTIONS);
        $this->form->setType('notetaking', PARAM_TEXT);

        $this->form->addElement('html', $this->fieldset_html_end());
    }
}