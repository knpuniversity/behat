# Clicking a Row in a Table (i.e. Complex Selectors)

Time for a real challenge! Deleting products, it's actually a bit harder than you
would think. Here comes some curve balls -- eh eh like that baseball pun?

We'll need a delete button to get rid of individual products. We're practicing BDD
so we'll start with a scenario.

    Scenario: Deleting a product

In order to delete a product we'll at least need one in our database. In fact, we should
add two products and check to see if we delete the second one that the first is unaffected.

Let's start with the `Given` as up here in the show published/unpublished scenario with
a slight difference.

    Given the following products exist:
   | name |

We'll only care about the name field for this. Let's create product Bar and another Foo1. 
Man, those dinos can't wait to get a hold of Bar and Foo1! Now why am I not marking either
of these as published or unpublished? Because it doesn't have anything to do with successful
deletion of products. 

    When I go to "/admin/prodcuts" 

And this is where it gets tricky, we'll have two rows in our table that both have 'delete' buttons
but I want to focus in on the one in the second row. So let's add another step here to do that.

    And I click "Delete" in the "Foo1" row

After that it would be nice to see a flash message that confirms that the product was deleted and
Foo1 will no longer appear in this list of products. 

    Then I should see "The product was deleted"
    And I should not see "Foo1"

We also should just double check that Bar wasn't removed in this process.

    But I should see "Bar"

This is the first time we've seen `But` it has the same functionality as `And` which extends
a `Then`, `When` or `Given` and sound natural. 

Let's give this a try, line 42. It prints out our step function and I'll paste that into `FeatureContext`.
`arg1` should be `linkText` and `arg2` should be `rowText`. This isn't our first time looking for a row
based on the text inside of it. That happened down here when we were looking for the check mark. 

Make a new `private function findRowByText` so we can reuse that functionality without duplication. 
Pass it the `$linkText`, copy these two lines right here and `return $row`. This should make life a little
bit easier. `$this->findRowByText($rowText);` and put that up here as well. Consider the row found!

Next, we want to find the link, and it's not a css selector it's the name of the text, what a user would
see on the site.  To do this write `$row->findLink()` and give it the `$linkText`. I'll repeat this one more
time for fun, the three things you can find by text are: links, buttons and fields. Use the `$findLink`, `$findButton`
and `$findField` on the page or individual elements to drill down to find things. Add `assertNotNull($link, 'Could not find link', .$linkText);` in case something goes wrong. Finally we have the individual element and we will click it. 

We haven't done any coding yet because we're just trying to get this scenario to work first. Let's run our scenario,
and it does fail but not in the way that I expected. It says "undefined index is published FeatureContext line 110".
That's happening because we don't have the 'is published' column in our little table here. On line 110, we're assuming
it's always there. That's fine, I usually start with assuming things are there and then when my steps need to become
more flexible I'll add more code to make it happen. Now we want `isset` `is published` , so if it's both then we set it
to published.

Rerun this now. Cool it fails this time with "Undefined variable: rowText line 256", hmm that sounds a bit like
I made a mistake. Ah yes, because I meant to call this `$rowText`. Now we've got the proper failure because there
is no link called `Delete` so let's fill in the functionality for that. Remember, do as little work as possible
to do this.

We'll need an endpoint of `deleteAction`, a route of `/admin/products/delete/{id}` and we'll name it `product_delete`.
We could get fancy and have all kinds of `@Method`s that say that this will only response to `POST` or `DELETE` requests.
In our case I'm going to keep this nice and simple. Typically I would add `$id` in my `deleteAction`, instead I'm going
to go the lazy route and type hint the product allowing Symfony to query for it for me.

Now remove the product, flush it, set a success flash message that matches what we wrote in our test and finally
redirect back to the product list route. To wrap this up we just need something that points here, so inside of
our `list.html.twig` template column called Actions and inside of here I should POST to delete things. To do
that I'll create a small form instead of just plugging in a link tag. Make the form point to the `product_delete`
action and have the `method="POST"`. Instead of having fields it'll just contain a button whose text is
"Delete". And of course add some CSS classes to make it look nice. Perfect!

In theory this should work and our test should be passing, so let's give that a try to find out. 
Hmmm it fails in the same spot. "And I click "Delete" in the "Foo1" row". Either something is wrong with 
the way we wrote the code, there's an error on the page or we're not even on the right page. Just looking
at our terminal we can't really tell what is going wrong. 

Let's add some debugging lines to figure this out. Since it's failing in this "I click" line let's hold
command and click into it to see how its step definition looks. Var dump this row here, this will check
that we are actually finding the row that we had expected. The other thing we can do is temporarily make
this an `@javascript` scenario and break. Let's try it now.

Ah-ha! In this case we have an exception on our page, we had no idea! So, I suppose our next step would be to fix
that. When I generate this URL I need to actually pass it an id. I'll keep the debugging stuff in for now, and try
the test again. And it stops right here since we have the I break line in the scenario. But we haven't had an
error and we can see our delete button so let's go ahead and press enter to keep this moving. 

And there's our failure, our test could not find "Delete" to click in the "Foo1" row. This is from something subtle
that I mentioned earlier, links and buttons are not the same. We click links but we press buttons. What I want to
say in this scenario is that I press delete instead of 'click'. 

More importantly inside of our `FeatureContext` we need to say `findButton`, change this from `click` to `press`,
`$link` to `$button` and `$linkText` to `$buttonText`. And that should solve all 99 of our problems. I even
have enough confidence to remove our `@javascript` and the I break line. Rerun the test! Beautiful, it passes!

You can always say `$findButton`, but normally what you actually want to do is press the button. So we can clean
up this code a bit by changing `findButton` to `pressButton`. This also applies to `clickLink` and `fillField`.

We can make this even shorter by adding `pressButton($buttonText)` to the end of the first line here. Rerun 
to see if we cleaned up too much or not. 

Hey look at that, it still passes!

If you think it's cool that you can write the scenario before you actually code, drill down to multiple levels
using Mink you and Behat are going to be very good friends.
