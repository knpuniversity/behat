Feature: Authentication
  In order to gain access to the site management area
  As an admin
  I need to be able to login and logout

  Scenario: Logging in
    Given there is an admin user "admin2" with password "admin"
    And I am on "/"
    When I follow "Login"
    And I fill in "Username" with "admin2"
    And I fill in "Password" with "admin"
    And I press "Login"
    Then I should see "Logout"
