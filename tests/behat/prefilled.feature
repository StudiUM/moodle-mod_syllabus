@mod @mod_syllabus @javascript
Feature: Prefilled fields
  As a teacher
  In order to complete my syllabus
  Some fields should be pre-filled

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname  | shortname | idnumber   | summary             |
      | Course1   | c1        | c1         | Summary of course 1 |
      | Course2   | c2        | TEST-A-E20 | Summary of course 2 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |
      | teacher1 | c2     | editingteacher |

  Scenario: Course 1 is not official - some fields are pre-filled but can be modified.
    Given I log in as "teacher1"
    And I am on "Course1" course homepage with editing mode on
    And I add a "Syllabus" to section "1" and I fill the form with:
      | Name | TestSyllabus |
      | Description | Test description |
    When I follow "TestSyllabus"
    And I navigate to "Draw up" in current page administration
    Then the following fields match these values:
      | Titre | Course1 |
      | Sigle | c1 |
      | Faculté / École / Département | Miscellaneous |
      | Trimestre | |
      | Année | |
      | Description simple | Summary of course 1 |
    And the "Titre" "field" should be enabled
    And the "Sigle" "field" should be enabled
    And the "Faculté / École / Département" "field" should be enabled
    And the "Trimestre" "field" should be enabled
    And the "Année" "field" should be enabled
    And the "Description simple" "field" should be enabled

  Scenario: Course 2 is official - some fields are pre-filled and disabled.
    Given I log in as "teacher1"
    And I am on "Course2" course homepage with editing mode on
    And I add a "Syllabus" to section "1" and I fill the form with:
      | Name | TestSyllabus |
      | Description | Test description |
    When I follow "TestSyllabus"
    And I navigate to "Draw up" in current page administration
    Then the following fields match these values:
      | Titre | Course2 |
      | Sigle | TEST-A-E20 |
      | Faculté / École / Département | Miscellaneous |
      | Trimestre | Été |
      | Année | 2020 |
    And the "Titre" "field" should be disabled
    And the "Sigle" "field" should be disabled
    And the "Faculté / École / Département" "field" should be disabled
    And the "Trimestre" "field" should be disabled
    And the "Année" "field" should be disabled
    # The Simple description field is shown as regular text.
    And I should see "Summary of course 2"
    And "Description simple" "field" should not exist

