Feature: Search
  In order to find products dinosaurs love
  As a website user
  I need to be able to search for products

  Background:
    Given I am on "/"

  Scenario Outline: Search for a product
    When I fill in "searchTerm" with "<term>"
    And I press "search_submit"
    Then I should see "<result>"

    Examples:
      | term    | result              |
      | Samsung | Samsung Galaxy S II |
      | XBox    | No products found   |
