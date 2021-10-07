@local @local_learningtools @ltool @ltool_resumecourse

Feature: Check the Resume course ltool workflow.
  Background: Create users to check the visbility.
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | student1 | Student   | User 1   | student1@test.com  |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 0        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |

  @javascript
  Scenario: Check the resume course tool.
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I follow on "dashboard"
    And I click on FAB button
    Then "#ltoolresumecourse-info" "css_element" Should be visible
    And I click on "#ltoolresumecourse-info" "css_element"
    Then I should see "Quiz 1"
