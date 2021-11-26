@mod @mod_syllabus @javascript @_file_upload
Feature: Settings
  As a teacher 
  I can fill in some  settings, including file upload
  As a student
  I can see the description, including images

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Sam1      | Student1 | student1@example.com |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname  | shortname | idnumber   | summary             |
      | Course1   | c1        | c1         | Summary of course 1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | c1     | student        |
      | teacher1 | c1     | editingteacher |
    And I log in as "teacher1"

  Scenario: Settings as a teacher and as a student
    Given I follow "Manage private files"
    And I upload "mod/syllabus/tests/fixtures/moodle_logo1.jpg" file to "Files" filemanager
    And I upload "mod/syllabus/tests/fixtures/moodle_logo2.jpg" file to "Files" filemanager
    And I click on "Save changes" "button"
    When I am on "Course1" course homepage with editing mode on
    And I add a "Syllabus" to section "1"
    And I set the following fields to these values:
      | Name          | TestSyllabus    |
      | syllabustype  | By competencies |
    # In the description.
    And I click on "Insert or edit image" "button" in the "//*[contains(.,'Description')]/following::div[1][@data-fieldtype='editor']" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo1.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "Moodle logo in description"
    And I click on "Save image" "button"
    # There are leftovers of the popup so you need quit the page and come back (problem occurs in behat only).
    And I click on "Save and return to course" "button"
    And I am on the "TestSyllabus" "syllabus activity" page
    And I navigate to "Edit settings" in current page administration
    # In the version notes.
    And I click on "Insert or edit image" "button" in the "//*[contains(.,'Version notes')]/following::div[1][@data-fieldtype='editor']" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo2.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "Moodle logo in version notes"
    And I click on "Save image" "button"
    # Save the form and check results : description is visible, but not version notes.
    And I click on "Save and return to course" "button"
    And I am on the "TestSyllabus" "syllabus activity" page
    Then "//img[contains(@src, 'moodle_logo1.jpg') and @alt='Moodle logo in description']" "xpath_element" should exist
    And "//img[contains(@src, 'moodle_logo2.jpg') and @alt='Moodle logo in version notes']" "xpath_element" should not exist
    # When editing, the teacher should see both images.
    And I navigate to "Edit settings" in current page administration
    Then "//img[contains(@src, 'moodle_logo1.jpg') and @alt='Moodle logo in description']" "xpath_element" should exist
    And "//img[contains(@src, 'moodle_logo2.jpg') and @alt='Moodle logo in version notes']" "xpath_element" should exist
    # Test that the student see the description and the image, but not the version notes.
    And I log out
    And I log in as "student1"
    And I am on "Course1" course homepage
    And I am on the "TestSyllabus" "syllabus activity" page
    And "//img[contains(@src, 'moodle_logo1.jpg') and @alt='Moodle logo in description']" "xpath_element" should exist
    And "//img[contains(@src, 'moodle_logo2.jpg') and @alt='Moodle logo in version notes']" "xpath_element" should not exist