@local @local_learningtools @ltool @ltool_email

Feature: Check the email ltool workflow.

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
      | student1 | C1 | student        |
      | student1 | C1 | student        |

  @javascript
  Scenario: Check to the visible of email tool.
    Given I log in as "teacher1"
    And I am on site homepage
    And I click on FAB button
    Then "#ltoolemail-info" "css_element" should not be visible
    And I follow "Course 1"
    And I click on FAB button
    Then "#ltoolemail-info" "css_element" should be visible
    And I click on "#ltoolemail-info" "css_element"
    And I should see "Send the email to course participants"
    And I set the following fields to these values:
      | Subject | testuser@gmail.com |
      | Message | Example test message |
      | Recipients | Student |
    And I press "Save changes"
    Then I should see "Successfully sent the mail to users"
    And I log out
