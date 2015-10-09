# Gherkin TableNodes

Let's get a little more complex with our products and show off a couple more
features about Behat itself. 

Let's show off a feature first by adding a line to our scenario,

    And there is 1 product

The idea being that this line creates 5 products and this line creates another product. 
PhpStorm is highlighting it because it is an undefined step since it doesn't quite match
this statement here. One trick is to add a second annotation statement to that definition.
And that would make it work. Or, we can use this really cool feature that's new to Behat3
where you make part of a sentence conditional. 

We'll update this to `There is/are :count product(s)` with the 's' inside of paranthesis. 
And this will also work. Let's change this step to be '6' and let's run line 6 to see if
this scenario will still pass. Excellent!

While I'm here, since we need to be logged in on each of these scenarios go ahead and add
that as a proper `Background`. And remove the duplicated line from each of our scenarios.

What I want to do now is add a test for this Is published flag. If a product is unpublished it
has a little 'x' icon and if it is published it will have a '✓' and I want to make sure that
these are showing up correctly. By default right now all of the products are unpublished.

Woo! Time for a new scenario! 

    Scenario: Show published/unpublished

This time we don't want to say "Given 5 products exist" because this time we actually care
to have some of them published and some unpublished. So, let's see a little trick here by
saying

    Given the following products exist:

Make sure you end that line in a colon, and below it we can build a table similar to the one
we saw earlier in the scenario outline. Give it two headers, name and is published, I'm just
making these up and you'll see how I use these in a second. We'll call the first product
Foo1 that is published and Foo2 that is not published. Let's keep going on our scenario,

    When I go to "/admin/products"

We'll leave it here for now but we are missing the `Then` line that asserts if the published/unpublished
are rendering up correctly. Let's just give this a try by running line 21 in our terminal. Copy our
new function and paste it into `FeatureContext`. 

Notice what it did here, it detected that we had a colon at the end of our step with a table below it
and passes this `TableNode` object which represents what we have here in the table. What we can do
is iterate over the object itself and dump each row out to see what happens. Science! Rerun the test.

When it prints each row is an associative array of those headers and the value for each header, how cool is that?

To save some time I'm going to copy some product some code, this is bad duplication, please don't do this
in your project.

In this scenario we won't give it an author because we really don't need that for what we are testing.
The two fields we have right now are Name for which we'll put `$row['name']` and then if `is published`
is set to yes we'll of course want to publish it since products are not published by default. 

`if ($row['is published'] == 'yes')` then `$product->setIsPublished(true);` now why is space published?
Because I want this to be read by humans, who wouldn't naturally say is_published. Also, yes for published
is better than a 1 and then we can go into `FeatureContext` and parse all that out. 

Let's make sure we have our entity manager and down at the bottom we will flush the changes. Great!

Cool this passes! But that's probably because there aren't any `Then` lines to fail on. Let's add one.
This is a little tricky because we want to check things row by row, not just whether or not there is
a checkmark icon, but that it is in this specific row. The checkmark icon itself is a `fa-check` class.

There is no built in definition to find a checkmark inside of a row, this is custom to us. So let's describe
this in as natural of a way as possible. 

    Then the "Foo1" row should have a check 

Cool! Rerun things so we can get that definition printed out for us. If you are feeling lazy and know you are
running the test to just get definitions you can add a `--append-snippets` and it will put the definitions
for you inside of your `FeatureContext` class for you. 

Go ahead and change `arg1` here to be `rowText`. This is a bit harder, what we need to do first is find a row
that contains that text and then look inside that row's HTML to see if it has a `fa-check` in it. We'll start
with the CSS, `$row = $this->getPage()->find('css',)` from here we can do something crazy like `table tr:` and
then throwing in the contains pseudo selector to look for that row text inside of here wihich I'll just put as
a wildcard, `%s` using the `sprintf` function. 

This code here should find the first `tr` matching that or none. This isn't perfect, if the `$rowText` had
some bad characters in it it wouldn't work. But this is my test so I won't worry about that. 
`assertNotNull($row, 'Could not find a row with text '.rowText)`. Finally down here we have the row element
and we want to assert that it has the `fa-check` class inside of it. `assertContains('fa-check', $row->getHTML(),'Did not find the check in the row');` this is some advanced Mink. 

Let's give it a try. It passes! We've gone one more step into being able to pull of whatever complex steps
we can imagine.

