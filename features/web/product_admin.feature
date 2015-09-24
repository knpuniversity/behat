Feature: Product admin panel
  In order to maintain the products shown on the site
  As an admin
  I need to be able to add/edit/delete products

  Background:
    Given I am logged in as an admin

  Scenario: List available products
    Given there are 5 products
    And there is 1 product
    And I am on "/admin"
    When I click "Products"
    Then I should see 6 products

  Scenario: Products show owner
    Given I author 5 products
    When I go to "/admin/products"
    # no products will be anonymous
    Then I should not see "Anonymous"

  Scenario: Show published/unpublished
    Given the following products exist:
      | name | is published |
      | Foo1 | yes          |
      | Foo2 | no           |
    When I go to "/admin/products"
    Then the "Foo1" row should have a check mark

  Scenario: Deleting a product
    Given the following product exists:
      | name |
      | Bar  |
      | Foo1 |
    When I go to "/admin/products"
    And I press "Delete" in the "Foo1" row
    Then I should see "The product was deleted"
    And I should not see "Foo1"
    But I should see "Bar"

  @javascript
  Scenario: Add a new product
    Given I am on "/admin/products"
    When I click "New Product"
    And I wait for the modal to load
    And I fill in "Name" with "Veloci-chew toy"
    And I fill in "Price" with "20"
    And I fill in "Description" with "Have your velociraptor chew on this instead!"
    And I press "Save"
    Then I should see "Product created FTW!"
    And I should see "Veloci-chew toy"
    # verify that we are the owner of the toy
    And I should not see "Anonymous"
