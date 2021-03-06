@mod @mod_syllabus @javascript
Feature: Duplicate and fill subrubrics
  As a teacher
  In order to complete my syllabus
  I need to be able to duplicate the subrubrics of some sections

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course1   | c1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |

  Scenario: Test all duplicable sub-rubrics
    Given I log in as "teacher1"
    And I am on "Course1" course homepage with editing mode on
    And I add a "Syllabus" to section "1" and I fill the form with:
      | Name | TestSyllabus |
      | Description | Test description |
    When I am on the "TestSyllabus" "syllabus activity" page
    And I navigate to "Draw up" in current page administration
    # Teachers.
    Then "//input[@id='id_teacher_name_0' and contains(@value,'Terry1 Teacher1')]" "xpath_element" should exist
    And "//textarea[@id='id_teacher_contactinformation_0' and contains(.,'teacher1@example.com')]" "xpath_element" should exist
    And I click on subrubric "teacher" add button
    And I set the following fields to these values:
      | teacher_title[0]         | First teacher title  |
      | teacher_availability[0]  | Monday 9am-5pm       |
      | teacher_name[1]          | Teacher 2            |
      | teacher_availability[1]  | Monday to Wednesday  |
    # Contacts.
    And I click on subrubric "contact" add button
    And I click on subrubric "contact" add button
    And I set the following fields to these values:
      | contact_name[0]                 | Assistant 1               |
      | contact_duty[0]                 | Assistant 1 duty          |
      | contact_contactinformation[0]   | Assistant 1 contact info  |
      | contact_availability[0]         | Assistant 1 availability  |
      | contact_name[1]                 | Assistant 2               |
    # Sessions calendar.
    And I click on "Calendrier des s??ances" "link"
    And I click on subrubric "calendarsession" add button
    And I click on subrubric "calendarsession" add button
    And I click on subrubric "calendarsession" add button
    And I set the following fields to these values:
      | calendarsession_date[0][day]              | 22              |
      | calendarsession_date[0][month]            | July            |
      | calendarsession_title[0]                  | Title abc       |
      | calendarsession_content[0]                | Content abc     |
      | calendarsession_activity[0]               | Activity abc    |
      | calendarsession_readingandworks[0]        | Reading abc     |
      | calendarsession_formativeevaluations[0]   | Formative abc   |
      | calendarsession_date[1][day]              | 8              |
      | calendarsession_date[1][month]            | June            |
      | calendarsession_content[1]                | Content def     |
      | calendarsession_date[2][day]              | 22              |
      | calendarsession_date[2][month]            | June            |
      | calendarsession_title[2]                  | Title ghi       |
      | calendarsession_activity[2]               | Activity ghi    |
      | calendarsession_readingandworks[2]        | Reading ghi     |
      | calendarsession_evaluations[2]            | Evaluations ghi |
    # Assessment calendar.
    And I click on "??valuations" "link"
    And I click on subrubric "assessmentcalendar" add button
    And I click on subrubric "assessmentcalendar" add button
    And I set the following fields to these values:
      | assessmentcalendar_evaluationdate[0][day]     | 20              |
      | assessmentcalendar_evaluationdate[0][month]   | July            |
      | assessmentcalendar_activities[0]              | Activity jkl    |
      | assessmentcalendar_learningobjectives[0]      | Objectives jkl  |
      | assessmentcalendar_evaluationcriteria[0]      | Criteria jkl    |
      | assessmentcalendar_weightings[0]              | Weightings jkl  |
      | assessmentcalendar_evaluationdate[1][day]     | 20              |
      | assessmentcalendar_evaluationdate[1][month]   | June            |
    # Save changes.
    And I click on "Save and preview" "button"
    And I click on "Save changes" "button"
    And I should see "TestSyllabus" in the "//h2" "xpath_element"
    # Check if teachers appear correctly.
    And the identifier of syllabus block "1" under "Enseignant" should contain "Terry1 Teacher1"
    And the syllabus block "1" under "Enseignant" should contain "First teacher title" value for "Titre" field
    And the syllabus block "1" under "Enseignant" should contain "teacher1@example.com" value for "Coordonn??es" field
    And the syllabus block "1" under "Enseignant" should contain "Monday 9am-5pm" value for "Disponibilit??s" field
    And the identifier of syllabus block "2" under "Enseignant" should contain "Teacher 2"
    And the syllabus block "2" under "Enseignant" should contain "Monday to Wednesday" value for "Disponibilit??s" field
    And the syllabus block "2" under "Enseignant" should not contain the "Titre" field
    # Check if contacts appear correctly.
    And the identifier of syllabus block "1" under "Personne-ressource" should contain "Assistant 1"
    And the syllabus block "1" under "Personne-ressource" should contain "Assistant 1 duty" value for "Responsabilit??" field
    And the syllabus block "1" under "Personne-ressource" should contain "Assistant 1 contact info" value for "Coordonn??es" field
    And the syllabus block "1" under "Personne-ressource" should contain "Assistant 1 availability" value for "Disponibilit??s" field
    And the identifier of syllabus block "2" under "Personne-ressource" should contain "Assistant 2"
    And the syllabus block "2" under "Personne-ressource" should not contain the "Responsabilit??" field
    And the syllabus block "2" under "Personne-ressource" should not contain the "Coordonn??es" field
    And the syllabus block "2" under "Personne-ressource" should not contain the "Disponibilit??s" field
    # Check if session calendar appears correctly.
    And the identifier of syllabus block "1" under "Calendrier des s??ances" should contain "8 June"
    And the syllabus block "1" under "Calendrier des s??ances" should not contain the "Titre" field
    And the syllabus block "1" under "Calendrier des s??ances" should contain "Content def" value for "Contenus" field
    And the syllabus block "1" under "Calendrier des s??ances" should contain "" value for "Activit??s" field
    And the syllabus block "1" under "Calendrier des s??ances" should not contain the "Lectures et travaux" field
    And the syllabus block "1" under "Calendrier des s??ances" should not contain the "??valuation formative" field
    And the syllabus block "1" under "Calendrier des s??ances" should not contain the "??valuation" field
    And the identifier of syllabus block "2" under "Calendrier des s??ances" should contain "22 June"
    And the syllabus block "2" under "Calendrier des s??ances" should contain "Title ghi" value for "Titre" field
    And the syllabus block "2" under "Calendrier des s??ances" should contain "" value for "Contenus" field
    And the syllabus block "2" under "Calendrier des s??ances" should contain "Activity ghi" value for "Activit??s" field
    And the syllabus block "2" under "Calendrier des s??ances" should contain "Reading ghi" value for "Lectures et travaux" field
    And the syllabus block "2" under "Calendrier des s??ances" should contain "Evaluations ghi" value for "??valuation" field
    And the syllabus block "2" under "Calendrier des s??ances" should not contain the "??valuation formative" field
    And the identifier of syllabus block "3" under "Calendrier des s??ances" should contain "22 July"
    And the syllabus block "3" under "Calendrier des s??ances" should contain "Title abc" value for "Titre" field
    And the syllabus block "3" under "Calendrier des s??ances" should contain "Content abc" value for "Contenus" field
    And the syllabus block "3" under "Calendrier des s??ances" should contain "Activity abc" value for "Activit??s" field
    And the syllabus block "3" under "Calendrier des s??ances" should contain "Reading abc" value for "Lectures et travaux" field
    And the syllabus block "3" under "Calendrier des s??ances" should contain "Formative abc" value for "??valuation formative" field
    # Check if assessments appear correctly.
    And the identifier of syllabus block "1" under "Calendrier des ??valuations" should contain "20 June"
    And the syllabus block "1" under "Calendrier des ??valuations" should contain "" value for "Activit??" field
    And the syllabus block "1" under "Calendrier des ??valuations" should contain "" value for "Objectifs d'apprentissage vis??s" field
    And the syllabus block "1" under "Calendrier des ??valuations" should not contain the "Crit??res d'??valuation" field
    And the syllabus block "1" under "Calendrier des ??valuations" should contain "" value for "Pond??ration" field
    And the identifier of syllabus block "2" under "Calendrier des ??valuations" should contain "20 July"
    And the syllabus block "2" under "Calendrier des ??valuations" should contain "Activity jkl" value for "Activit??" field
    And the syllabus block "2" under "Calendrier des ??valuations" should contain "Objectives jkl" value for "Objectifs d'apprentissage vis??s" field
    And the syllabus block "2" under "Calendrier des ??valuations" should contain "Criteria jkl" value for "Crit??res d'??valuation" field
    And the syllabus block "2" under "Calendrier des ??valuations" should contain "Weightings jkl" value for "Pond??ration" field
    # Test de delete buttons.
    And I navigate to "Draw up" in current page administration
    And I click on subrubric "teacher" "2" delete button
    And I click on subrubric "contact" "1" delete button
    And I click on "Calendrier des s??ances" "link"
    And I click on subrubric "calendarsession" "2" delete button
    And I click on "??valuations" "link"
    And I click on subrubric "assessmentcalendar" "1" delete button
    And I click on "Save and preview" "button"
    And I click on "Save changes" "button"
    # Check if everything was deleted correctly.
    And I should not see "Teacher 2"
    And the identifier of syllabus block "1" under "Enseignant" should contain "Teacher1"
    And I should not see "Assistant 1"
    And the identifier of syllabus block "1" under "Personne-ressource" should contain "Assistant 2"
    And I should not see "Title ghi"
    And the identifier of syllabus block "1" under "Calendrier des s??ances" should contain "8 June"
    And the identifier of syllabus block "2" under "Calendrier des s??ances" should contain "22 July"
    And the identifier of syllabus block "1" under "Calendrier des ??valuations" should contain "20 July"
    And the syllabus block "1" under "Personne-ressource" should not contain the "Disponibilit??s" field