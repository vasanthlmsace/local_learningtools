@local @local_learningtools @ltool @ltool_information

Feature: Check the information ltool workflow.

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

  @javascript
  Scenario: Check the course information tool.
    Given I log in as "student1"
    And I am on site homepage
    And I click on FAB button
    Then "#ltoolinformation-info" "css_element" should not be visible
    When I am on "Course 1" course homepage
    And I click on FAB button
    Then "#ltoolinformation-info" "css_element" should be visible
    And I click on "#ltoolinformation-info" "css_element"
    And I should see "Course Information" in the ".modal-title" "css_element"
    Then I logout
