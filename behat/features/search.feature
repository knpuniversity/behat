Feature: Search
  In order to find a word definition
  As a website user
  I am able to search for a word

  @javascript
  Scenario: Search for a word that exists
    Given I am on "/wiki/MainPage"
    When I fill in "search" with "Velociraptor"
    And I press "searchButton"
    Then I should see "an enlarged sickle-shaped claw"