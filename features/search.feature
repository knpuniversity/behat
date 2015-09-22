Feature: Search
  In order to find products dinosaurs love
  As a website user
  I need to be able to search for products

  Background:
    Given I am on "/"

  Scenario: Search for a word that exists
    When I fill in "searchTerm" with "Samsung"
    And I press "search_submit"
    Then I should see "Samsung Galaxy S II"

  Scenario: Search for a word that does not exist
    When I fill in "searchTerm" with "XBox"
    And I press "search_submit"
    Then I should see "No products found"
