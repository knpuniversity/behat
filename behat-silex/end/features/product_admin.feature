Feature: Product admin
  In order to manage the content on my site
  As an admin
  I need to be able to add, edit and delete products

  @list
  Scenario: Seeing a list of existing products
    Given I am logged in as an admin
    And the following products exist:
      | name                      | is published |
      | Tickle-me Raptor          | yes          |
      | When Dinosaurs Attack DVD | yes          |
      | The Veggie-dino cookbook  | no           |
    And I am on "/admin"
    When I follow "Products"
    Then I should see 2 rows in the table
    And I should see "Tickle-me Raptor"
    But I should not see "The Veggie-dino cookbook"

  @javascript
  Scenario: Add a new product via the dialog
    Given I am logged in as an admin
    And I am on "/products"
    When I follow "New Product"
    And I wait for the dialog to appear
    And I fill in the following:
      | Name  | Raptor teddy-bear |
      | Price | 5.99              |
    And I press "Save"
    Then I should see "Product created"