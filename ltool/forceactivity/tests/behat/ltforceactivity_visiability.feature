@local @local_learningtools @ltool @ltool_forceactivity

Feature: Check the Force activity ltool workflow.

  Background: Create users to check the visbility.
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | student1 | Student   | User 1   | student1@test.com  |
      | student2 | Student   | User 2   | student2@test.com  |
      | teacher1 | Teacher   | User 1   | teacher1@test.com  |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 0        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
  @javascript
  Scenario: Test the force activity workflow.
    Given I log in as "teacher1"
    And I am on site homepage
    And I click on FAB button
    Then "#ltoolforceactivity-info" "css_element" should not be visible
    When I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name          | Quiz 1 |
      | Description         | Quiz 1 description |
      | Completion tracking | Students can manually mark the activity as completed |
    #And "Student User 1" user has not completed "Quiz 1" activity
    And I am on "Course 1" course homepage
    And I click on FAB button
    Then "#ltoolforceactivity-info" "css_element" should be visible
    And I click on "#ltoolforceactivity-info" "css_element"
    And I should see "Force activity" in the ".modal-title" "css_element"
    And I set the following fields to these values:
      | Course activity | Quiz 1 |
      | Message | Test info message |
    And I press "Save changes"
    And I should see "Successfully added the force activity in the course"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I click on FAB button
    Then "#ltoolforceactivity-info" "css_element" should not be visible
    And I visit forceactivity page "Course 1" "Quiz 1"
    Then I toggle the manual completion state of "Quiz 1"
    And the manual completion button of "Quiz 1" is displayed as "Done"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "Student User 1" user has completed "Quiz 1" activity
