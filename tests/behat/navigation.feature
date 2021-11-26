@mod @mod_syllabus @javascript
Feature: Navigation
  As a teacher
  In order to complete my syllabus
  I need to use the navigation buttons and links

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname  | shortname | idnumber   | summary             |
      | Course1   | c1        | c1         | Summary of course 1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |
    When I log in as "teacher1"
    And I am on "Course1" course homepage with editing mode on
    And I add a "Syllabus" to section "1" and I fill the form with:
      | Name | TestSyllabus |
      | Description | Test description |

  Scenario: Collapsible sections.
    Given I am on the "TestSyllabus" "syllabus activity" page
    When I navigate to "Draw up" in current page administration
    Then I should see "Nombre de crédits"
    And I should see "teacher1@example.com"
    And I should see "Responsabilité"
    And I should see "Description simple"
    # Close the sections.
    And I toggle the "Cours" section
    And I should not see "Nombre de crédits"
    And I toggle the "Enseignant" section
    And I should not see "teacher1@example.com"
    And I toggle the "Personne-ressource" section
    And I should not see "Responsabilité"
    And I toggle the "Description du cours" section
    And I should not see "Description simple"
    # Re-open the sections.
    And I toggle the "Cours" section
    And I should see "Nombre de crédits"
    And I toggle the "Enseignant" section
    And I should see "teacher1@example.com"
    And I toggle the "Personne-ressource" section
    And I should see "Responsabilité"
    And I toggle the "Description du cours" section
    And I should see "Description simple"
    # In another section, test the Collapse all / Expand all links.
    And I click on "Rappels" "link"
    And I should see "Date limite"
    And I should see "Prise de notes"
    And I click on "//div[@id='reminders']//a[./@href][contains(.,'Collapse all')]" "xpath_element"
    And I should not see "Date limite"
    And I should not see "Prise de notes"
    And I click on "//div[@id='reminders']//a[./@href][contains(.,'Expand all')]" "xpath_element"
    And I should see "Date limite"
    And I should see "Prise de notes"

  Scenario: Buttons.
    Given I am on the "TestSyllabus" "syllabus activity" page
    # Save and continue button.
    When I navigate to "Draw up" in current page administration
    And I set the field "Charge de travail hebdomadaire" to "Text for weekly workload."
    And I click on "Apprentissages visés" "link"
    And I set the field "Objectifs généraux" to "Text for educational intentions."
    And I click on "Save and continue" "button"
    And I click on "Save changes" "button"
    # Cancel button.
    And I set the field "Objectifs d'apprentissage" to "Text for learning objectives."
    And I click on "Rappels" "link"
    And I set the field "Enregistrement des cours" to "Text for course registration."
    And I click on "Cancel" "button"
    # Should see fields that were saved but not those that were cancelled.
    Then I should see "Text for weekly workload."
    And I should see "Text for educational intentions."
    And I should not see "Text for learning objectives.."
    And I should not see "Text for course registration."
    # Save and preview button.
    And I navigate to "Draw up" in current page administration
    And I set the field "Place du cours dans le programme" to "Text for place in program."
    And I click on "Save and preview" "button"
    And I click on "Save changes" "button"
    And I should see "Text for place in program."
    # Save and return to course button.
    And I navigate to "Draw up" in current page administration
    And I set the field "Déroulement du cours" to "Text for course conduct."
    And I click on "Save and return to course" "button"
    And I click on "Save changes" "button"
    And I should see "Course1"
    And I should see "Topic 1"
    And I should see "TestSyllabus" in the "Topic 1" "section"
