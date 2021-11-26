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
 * Step definitions for Syllabus.
 *
 * @package    mod_syllabus
 * @category   test
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2020 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Step definitions for Syllabus.
 *
 * @package    mod_syllabus
 * @category   test
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2020 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_syllabus extends behat_base {
    /**
     * Checks that the specified syllabus block under the specified title contains the specified value as identifier.
     *
     * @Given the identifier of syllabus block :nb under :title should contain :value
     * @param int $nb
     * @param string $title
     * @param string $value
     */
    public function the_identifier_of_syllabus_block_under_should_contain($nb, $title, $value) {
        $xpath = "//*[(self::h3 or self::h4) and contains(.,'$title')]/following::div[contains(@class,'greyborder')][$nb]/div[1]";
        $this->execute("behat_general::assert_element_contains_text",
            array($value, $xpath, "xpath_element")
        );
    }

    /**
     * Checks that the specified syllabus block under the specified title contains the specified value for the specified field.
     *
     * @Given the syllabus block :nb under :title should contain :value value for :field field
     * @param int $nb
     * @param string $title
     * @param string $value
     * @param string $field
     */
    public function the_syllabus_block_under_should_contain_for_field($nb, $title, $value, $field) {
        // The $field can contain apostrophes.
        $xpath = "//*[(self::h3 or self::h4) and contains(.,'$title')]/following::div[contains(@class,'greyborder')][$nb]/div[2]".
            "/div[contains(.,\"$field\")]/div[2]";
        $this->execute("behat_general::assert_element_contains_text",
            array($value, $xpath, "xpath_element")
        );
    }

    /**
     * Checks that the specified syllabus field under the specified title contains the specified value.
     *
     * @Given the syllabus :field field under :title should contain :value
     * @param string $field
     * @param string $title
     * @param string $value
     */
    public function the_syllabus_field_under_should_contain($field, $title, $value) {
        // The $field can contain apostrophes.
        $xpath = "//*[(self::h3 or self::h4) and contains(.,'$title')]/following::div[contains(@class,'greyborder')]".
            "/div[contains(.,\"$field\")]/div[2]";
        $this->execute("behat_general::assert_element_contains_text",
            array($value, $xpath, "xpath_element")
        );
    }

    /**
     * Checks that the specified syllabus block under the specified title does not contain the specified field.
     *
     * @Given the syllabus block :nb under :title should not contain the :field field
     * @param int $nb
     * @param string $title
     * @param string $field
     */
    public function the_syllabus_block_under_should_not_contain_the_field($nb, $title, $field) {
        // The $field can contain apostrophes.
        $xpath = "//*[(self::h3 or self::h4) and contains(.,'$title')]/following::div[contains(@class,'greyborder')][$nb]/div[2]".
            "/div[contains(.,\"$field\")]";
        $this->execute("behat_general::should_not_exist", array($xpath, "xpath_element"));
    }

    /**
     * Checks that the syllabus does not contain the specified field under the specified title.
     *
     * @Given the syllabus should not contain the :field field under :title
     * @param string $field
     * @param string $title
     */
    public function the_syllabus_should_not_contain_the_field_under($field, $title) {
        // The $field can contain apostrophes.
        $xpath = "//*[(self::h3 or self::h4) and contains(.,'$title')]/following::div[contains(@class,'greyborder')]".
            "/div[contains(.,\"$field\")]";
        $this->execute("behat_general::should_not_exist", array($xpath, "xpath_element"));
    }

    /**
     * Clicks on the Add button of the specified subrubric.
     *
     * @Given I click on subrubric :subrubric add button
     * @param string $subrubric
     */
    public function i_click_on_subrubric_add_button($subrubric) {
        $xpath = "//a[./@href][@data-id='$subrubric' and contains(normalize-space(string(.)), '".get_string('add')."')]";
        $this->execute("behat_general::i_click_on", [$xpath, 'xpath_element']);
    }

    /**
     * Clicks on the Delete button of the specified subrubric.
     *
     * @Given I click on subrubric :subrubric :nb delete button
     * @param string $subrubric
     * @param int $nb
     */
    public function i_click_on_subrubric_delete_button($subrubric, $nb) {
        // Click on delete icon. Some subrubrics are tables, others are not.
        $deletetitle = get_string('deletethisline', 'mod_syllabus');
        if ($subrubric == 'teacher' || $subrubric == 'contact') {
            $xpath = "//table[@id='$subrubric']/tbody/tr[$nb]/td/a[./@href]/i[@title='".$deletetitle."']";
        } else {
            $xpath = "//div[@id='$subrubric']/div[$nb]//a[./@href]/i[@title='".$deletetitle."']";
        }

        $this->execute("behat_general::i_click_on", [$xpath, 'xpath_element']);
        // Click on confirm delete button.
        $xpath = "//div[contains(@class,'modal-dialog')]//button[text()='".get_string('delete')."']";
        $this->execute("behat_general::i_click_on", [$xpath, 'xpath_element']);
    }

    /**
     * Open/close a section of the form.
     *
     * @Given I toggle the :section section
     * @param string $section
     */
    public function i_toggle_the_section($section) {
        $xpath = "//fieldset[contains(@class,'collapsible')]/legend/a[contains(.,'$section')]";
        $this->execute('behat_general::i_click_on', array($xpath, "xpath_element"));
    }
}
