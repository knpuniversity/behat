Feature: Search
  In order to find a word definition
  As a website user
  I am able to search for a word

  Background:
    Given I am on "/wiki/MainPage"

  Scenario Outline:
    When I fill in the search box with "<search>"
    And I press the search button
    Then I should see "<expectation>"

    Examples:
      | search             | expectation                    |
      | Velociraptor       | an enlarged sickle-shaped claw |
      | Tyrannosaurus Bill | Search results                 |

  @javascript
  Scenario: Searching for a page with autocompletion
    When I fill in the search box with "Tyran"
    And I wait for the suggestions box to appear
    Then I should see "Tyrannosaurus"