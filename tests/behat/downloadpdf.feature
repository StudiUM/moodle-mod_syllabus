@mod @mod_syllabus @javascript
Feature: PDF file
  As a teacher or a student
  The syllabus should be available as a PDF file

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
    When I log in as "teacher1"
    And I am on "Course1" course homepage with editing mode on
    And I add a "Syllabus" to section "1" and I fill the form with:
      | Name | TestSyllabus |
      | Description | Test description |

  Scenario: Download the pdf as a teacher and as a student
    Given I am on the "TestSyllabus" "syllabus activity" page
    Then I should see "Download as PDF"
    And following "Download as PDF" should download between "1000" and "500000" bytes
    And I log out
    And I log in as "student1"
    And I am on "Course1" course homepage
    And I am on the "TestSyllabus" "syllabus activity" page
    And I should see "Download as PDF"
    And following "Download as PDF" should download between "1000" and "500000" bytes