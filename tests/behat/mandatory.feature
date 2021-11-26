@mod @mod_syllabus @javascript
Feature: Mandatory and non mandatory fields
  As a teacher
  In order to complete my syllabus
  Some fields should be mandatory and others are not
  And as a student, everything should appear correctly

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

  Scenario: Mandatory fields always appear, the others appear if filled.
    Given I am on the "TestSyllabus" "syllabus activity" page
    # Nothing modified yet, but mandatory fields should be shown anyway.
    # Sub-rubrics and pre-filled fields are not verified here, there are other tests for that.
    # Information générale.
    When the syllabus "Titre" field under "Information générale" should contain "Course1"
    Then the syllabus should not contain the "Déroulement du cours" field under "Information générale"
    And the syllabus should not contain the "Charge de travail hebdomadaire" field under "Information générale"
    And the syllabus "Description détaillée" field under "Information générale" should contain ""
    And the syllabus "Place du cours dans le programme" field under "Information générale" should contain ""
    # Apprentissages visés.
    And the syllabus "Objectifs généraux" field under "Apprentissages visés" should contain ""
    And the syllabus "Objectifs d'apprentissage" field under "Apprentissages visés" should contain ""
    # Calendrier des séances.
    And I should not see "Calendrier des séances"
    # Évaluations.
    And I should not see "Évaluations"
    # Rappels.
    And the syllabus "Enregistrement des cours" field under "Rappels" should contain ""
    And the syllabus should not contain the "Modification de l'inscription" field under "Rappels"
    And the syllabus should not contain the "Date limite d'abandon" field under "Rappels"
    And the syllabus should not contain the "Fin du trimestre" field under "Rappels"
    And the syllabus should not contain the "Évaluation de l'enseignement" field under "Rappels"
    And the syllabus should not contain the "Prise de notes et activités d'apprentissage avec ordinateurs, tablettes ou téléphones intelligents" field under "Rappels"
    # Ressources.
    And the syllabus "Documents" field under "Ressources obligatoires" should contain ""
    And the syllabus "Ouvrages en réserve" field under "Ressources obligatoires" should contain ""
    And the syllabus should not contain the "Équipement (matériel)" field under "Ressources obligatoires"
    And the syllabus should not contain the "Documents" field under "Ressources complémentaires"
    And the syllabus should not contain the "Sites Internet" field under "Ressources complémentaires"
    And the syllabus should not contain the "Guides" field under "Ressources complémentaires"
    And the syllabus should not contain the "Autres" field under "Ressources complémentaires"
    And the syllabus should not contain the "Autres" field under "Soutien à la réussite"
    # Cadres et politiques.
    And the syllabus should not contain the "Autres" field under "Règlements et politiques"
    And the syllabus should not contain the "Autres" field under "Intégrité, fraude et plagiat"
    # Test the popup.
    And I navigate to "Draw up" in current page administration
    And I click on "Save and continue" "button"
    And I should see "Information générale" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should see "Apprentissages visés" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should see "Calendrier des séances" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should see "Évaluations" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should see "Ressources" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should not see "Rappels" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should not see "Cadres règlementaires" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I click on "//div[contains(@class,'modal-content')]//button[contains(.,'Cancel')]" "xpath_element"

    # Fill some of the fields (mostly the mandatory ones plus a couple of others to use when testing the student).
    And I set the following fields to these values:
      | creditnb                    | 3                 |
      | teacher_availability[0]     | Monday 9am-5pm    |
      | detaileddescription[text]   | Test detailed     |
      | placeinprogram[text]        | Test place        |
      | idnumber                    | ABCD-A-A21        |
      | trimester                   | Autumn            |
      | courseyear                  | 2021              |
    And I click on "Apprentissages visés" "link"
    And I set the following fields to these values:
      | educationalintentions[text]       | Test educational  |
      | learningobjectives[text]          | Test objectives   |
    And I click on "Rappels" "link"
    And I set the following fields to these values:
      | teachingevaluation          | Test evaluation   |
    And I click on "Ressources" "link"
    And I set the following fields to these values:
      | mandatoryresourcedocuments[text]  | Test documents    |
      | librarybooks[text]                | Test books        |
    And I click on "Cadres règlementaires et politiques institutionnelles" "link"
    And I set the following fields to these values:
      | integrityothers[text]             | Test integrity    |
    # The popup still appears, but not all fields are still required
    And I click on "Save and continue" "button"
    And "//div[contains(@class,'modal-content')]" "xpath_element" should exist
    And I should see "Calendrier des séances" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should see "Évaluations" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should not see "Information générale" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should not see "Apprentissages visés" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should not see "Rappels" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should not see "Ressources" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I should not see "Cadres règlementaires" in the "//div[contains(@class,'modal-content')]" "xpath_element"
    And I click on "//div[contains(@class,'modal-content')]//button[contains(.,'Cancel')]" "xpath_element"

    # Fill the last fields
    And I click on "Calendrier des séances" "link"
    And I click on subrubric "calendarsession" add button
    And I set the following fields to these values:
      | calendarsession_content[0]                | Content abc     |
      | calendarsession_activity[0]               | Activity abc    |
    And I click on "Évaluations" "link"
    And I click on subrubric "assessmentcalendar" add button
    And I set the following fields to these values:
      | assessmentcalendar_activities[0]              | Activity jkl    |
      | assessmentcalendar_learningobjectives[0]      | Objectives jkl  |
      | assessmentcalendar_weightings[0]              | Weightings jkl  |
    And I click on "Save and continue" "button"
    And "//div[contains(@class,'modal-content')]" "xpath_element" should not exist

    # Now test as a student and check everything appears correctly.
    And I log out
    And I log in as "student1"
    And I am on "Course1" course homepage
    And I am on the "TestSyllabus" "syllabus activity" page
    And the syllabus "Titre" field under "Information générale" should contain "Course1"
    And the syllabus block "1" under "Enseignant" should contain "Monday 9am-5pm" value for "Disponibilités" field
    And the syllabus "Objectifs généraux" field under "Apprentissages visés" should contain ""
    And the syllabus block "1" under "Calendrier des séances" should contain "Content abc" value for "Contenus" field
    And the syllabus block "1" under "Calendrier des évaluations" should contain "Activity jkl" value for "Activité" field
    And the syllabus "Évaluation de l'enseignement" field under "Rappels" should contain "Test evaluation"
    And the syllabus "Documents" field under "Ressources obligatoires" should contain "Test documents"
    And the syllabus "Autres" field under "Intégrité, fraude et plagiat" should contain "Test integrity"