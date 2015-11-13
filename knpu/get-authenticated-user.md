# When *I* do Something: Handling the Current User

Time for a challenge! Whenever you have products in the admin area, it either shows
the name of the user that created it - like `admin` - or `anonymous` if it was created
via some other method without an author. Right now, our admin area lists "anonymous"
next to every product. The reason is simple: we're not setting the author when we
create the products in `FeatureContext`.

I want to test that this table *does* show the correct author when its set. Create
a new scenario to describe this:

[[[ code('4c40dd7480') ]]]

Instead of just saying there are five products I'll say:

[[[ code('5847ac3fba') ]]]

This *is* new language that will need a step definition. To save time, we can go
directly to the products page:

[[[ code('6edea384d7') ]]]

Since "I" - some admin user - will be the author of the products, they should all
show "admin": none will say "Anonymous". And we will *only* have these 5 products
because we're clearing the database between each scenario to keep things independent.

Run just this new scenario by using its line number:

```bash
./vendor/bin/behat features/web/product_admin.feature:13
```

Great - copy the `iAuthorProducts()` function code and paste it into our handy `FeatureContext`
class - near the other product function:

[[[ code('a2d4b5fdea') ]]]

These two functions will be similar, so we should reuse the logic. Copy the internals
of `thereAreProducts`, make a new `private function createProducts()`. Pass it `$count`
as an argument and also an optional User object which will be the author for those products:

[[[ code('8bbd967c08') ]]]

Now, add an if statement that says, if `$author` is passed then, `$product->setAuthor()`:

[[[ code('954b443f9e') ]]]

I already have that relationship setup with in Doctrine. Great!

In `thereAreProducts()`, change the body of this function to `$this->createProducts($count);`:

[[[ code('95e36de658') ]]]

Do the same thing in `iAuthorProducts()` for now:

[[[ code('acd4d6cbd4') ]]]

Clearly, this is still not setting the author. But I want to see if it executes first
and then we'll worry about setting the author.

## Who is "I" in a Scenario?

Cool! It runs... and fails because anonymous *is* still shown on the page. The question
now is: how do we get the current user? The step says "I author". But who is "I" in this
case? In `product_admin.feature`:

[[[ code('88c42ae944') ]]]

You can see that "I" is whomever we logged in as. We didn't specify what the username
should be for that user, but whoever is logged in is who "I" represents.

When we worked with the `ls` scenarios earlier, we needed to share the command output
string between the steps of a scenario. In this case, we have a similar need: we
need to share the user object from the step where we log in, with the step where
"I" author some products. To share data between steps, create a new `private $currentUser;`:

[[[ code('100d047bcd') ]]]

In `iAmLoggedInAsAnAdmin()`, add `$this->currentUser = $this->thereIsAnAdminUserWithPassword()`:

[[[ code('9d15e806ff') ]]]

Click to open that function. It creates the `User` object of course, but now we need
to also make sure it returns that:

[[[ code('d7766a9278') ]]]

And that's it! This login step will cause the `currentUser` property to be set and
in `iAuthorProducts()` we can access that and pass it into `createProducts()` so that
each product us authored by us:

[[[ code('15862f69e1') ]]]

It's pretty common to want to know *who* is logged in, so you'll likely want to use
this in your project.

And hey it even passes! Now you can continue to write scenarios in terms of actions
that "I" take and we will actually know who "I" is.
