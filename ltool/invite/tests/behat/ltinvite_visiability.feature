@local @local_learningtools @ltool @ltool_invite

Feature: Check the invite ltool workflow.

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
  Scenario: Create invite to users.
    Given I log in as "teacher1"
    And I am on site homepage
    And I click on FAB button
    Then "#ltoolinvite-info" "css_element" should not be visible
    When I am on "Course 1" course homepage
    And I click on FAB button
    Then "#ltoolinvite-info" "css_element" should be visible
    And I click on "#ltoolinvite-info" "css_element"
    And I should see "Invite Users" in the ".modal-title" "css_element"
    And I set the field "inviteusers" to "student1@test.com"
    And I press "Invite Now"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Enrolled users" in current page administration
    Then I should see "Student User 1"

  @javascript
  Scenario: Create user and to invite the course.
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Learning Tools > Learning Tools Invite" in site administration
    And I set the following fields to these values:
      | Do not create users | 0 |
    And I press "Save changes"
    And I log out
    Then I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I click on FAB button
    Then "#ltoolinvite-info" "css_element" should be visible
    And I click on "#ltoolinvite-info" "css_element"
    And I should see "Invite Users" in the ".modal-title" "css_element"
    And I set the field "inviteusers" to "demouser1@test.com"
    And I press "Invite Now"
    And I navigate to "Users > Enrolled users" in current page administration
    Then I should see "demouser1@test.com"
    And  I log out
    Then I log in as "admin"
    And I navigate to "Users > Browse list of users" in site administration
    Then I should see "demouser1@test.com"
    And  I log out

  @javascript
  Scenario: Does not create user and to invite the course.
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > Learning Tools > Learning Tools Invite" in site administration
    And I set the following fields to these values:
      | Do not create users | 1 |
    And I press "Save changes"
    And I log out
    Then I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I click on FAB button
    Then "#ltoolinvite-info" "css_element" should be visible
    And I click on "#ltoolinvite-info" "css_element"
    And I should see "Invite Users" in the ".modal-title" "css_element"
    And I set the field "inviteusers" to "demouser2@test.com"
    And I press "Invite Now"
    And I navigate to "Users > Enrolled users" in current page administration
    Then I should not see "demouser2@test.com"
    And  I log out
    Then I log in as "admin"
    And I navigate to "Users > Browse list of users" in site administration
    Then I should not see "demouser2@test.com"
