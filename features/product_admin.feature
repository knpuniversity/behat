Feature: Product admin panel
  In order to maintain the products shown on the site
  As an admin
  I need to be able to add/edit/delete products

  Scenario: List available products
    Given there are 5 products
    And I am on "/admin"
    When I click "Products"
    Then I should see 5 products

  Scenario: Add a new product
    Given I am on "/admin/products"
    When I click "New Product"
    And I fill in "Name" with "Veloci-chew toy"
    And I fill in "Price" with "20"
    And I fill in "Description" with "Have your velociraptor chew on this instead!"
    And I press "Save"
    Then I should see "Product created FTW!"
