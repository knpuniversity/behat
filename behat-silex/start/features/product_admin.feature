Feature: Product admin
  In order to manage the content on my site
  As an admin
  I need to be able to add, edit and delete products

  Scenario: Seeing a list of existing products
    Given I am logged in as an admin
    And there are 5 products
    And I am on "/admin"
    When I follow "Products"
    Then I should see 5 rows in the table
