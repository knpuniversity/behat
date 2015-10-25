# Gherkin Tables: Given I have the following:

To show off a nice Behat feature, add a line to our scenario:

    And there is 1 product

We now have one step that adds 5 products and another - almost identical that adds
one more product. But, the language isn't *quite* the same, so PhpStorm highlights
it as an undefined step. How can we use this language but have it re-use the definition
we already have?

One trick is to add a second annotation statement to that definition. And that would
make it work. But there's a better way that's new to Behat3: conditional language.

## Using this/that Conditional Language

Update the annotation to `There is/are :count product(s)` with the 's' inside of
parentheses. Now, change the end of the scenario to look for 6 products. Run just
this scenario:

```bash
./vendor/bin/behat features/product_admin.feature:6
```

The new language matches both steps and we're passing. While we're here, add
a proper `Background` and move the login step there. Remove the duplicated line from
each of scenario.

## Using Gherkin TableNodes

Next, I want to add a test for this "Is published" flag: if a product is *not* published,
it has a little 'x' icon. If it *is* published it has a '✓'. I want to make sure
these are showing up correctly. Right now, all of the products are unpublished.

Woo! Time for a new scenario! 

    Scenario: Show published/unpublished

But this time, we can't just say "Given 5 products exist" because we need to control
the published flag. But we can use a new trick, add:

    Given the following products exist:

End the line with a colon and below, build a table just like we did earlier with
[scenario outlines](scenario-outline).Give it two headers: "name" and "is published".
I'm making these headers up: you'll see how I use them in a second. Call the first
product Foo1 and make it published. Call the second Foo2 and make it *not* published.
Ok, keep going on the scenario,

    When I go to "/admin/products"

I'll stop here for now and add the missing `Then` line that looks for the published
flag later. Try this by running only this scenario:

```bash
./vendor/bin/behat features/product_admin.feature:21
```

Copy the new function into `FeatureContext`. 

## The TableNode Object

Ah, but this looks different: it saw that the step ended with a colon and passed
us a `TableNode` object that represents the data in the table after it. Let's iterate
over the object and dump each row out to see what happens. Science! Rerun the test.

```bash
./vendor/bin/behat features/product_admin.feature:21
```

Each row is printed as an associative array using the header and the value for that
row. How cool is that?

To save some time, I'll copy some code that creates `Product` objects. This adds
some duplication to my FeatureContext - shame on me. In a real project, I want you
to be a little more careful.

In this scenario, we won't give each product an author because we don't need that
for what we're testing. Call `setName()` passing it `$row['name']`. Then
`if ($row['is published'] == 'yes')`, add `$product->setIsPublished(true);`.

The `is published` with a space in the middle is on purpose: I want a human to be
able to read the scenarios. And other than super geeks like you and I, `is published`
reads a lot better than `is_published`. And, "yes" for published is more expressive
than putting a 1 or 0. In `FeatureContext`, we translate all of that to code.

Fetch the entity manager and flush the changes at the bottom. Great!

Cool this passes! But don't get too excited: we don't have any Then statements yet.
