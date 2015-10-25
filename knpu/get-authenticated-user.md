# When *I* do Something: Handling the Current User

Time for a challenge! Whenever you have products in the admin area, it either shows
the name of the user that created it - like `admin` - or `anonymous` if it was created
via some other method without an author. Right now, our admin area lists "anonymous"
next to every product. The reason is simple: we're not setting the author when we
create the products in `FeatureContext`.

I want to test that this table *does* show the correct author when its set. Create
a new scenario to describe this:

     Scenario: Products show author
       Given I am logged in as an admin

Instead of just saying there are five products I'll say:

     And I author 5 products 

This *is* new language that will need a step definition. To save time, we can go
directly to the products page,

     When I go to "/admin/products"
     Then I should not see "Anonymous"

Since "I" - some admin user - will be the author of the products, they should all
show "admin": none will say "Anonymous". And we will *only* have these 5 products
because we're clearing the database between each scenario to keep things independent.

Run just this new scenario by using its line number.

Great - copy the `iAuthorProducts` function code and paste it into our handy `FeatureContext`
class - near the other product function. These two functions will be similar, so
we should reuse the logic. Copy the internals of `thereAreProducts`, make a new
`private function createProducts`. Pass it `$count` as an argument and also an optional
User object which will be the author for those products. Now, add an if statement
that says, if `$author` is passed then, `$product->setAuthor()`. I already have that
relationship setup with in Doctrine. Great!

In `thereAreProducts`, change the body of this function to `$this->createProducts($count);`.
Do the same thing in `iAuthorProducts` for now. Clearly, this is still not setting
the author. But I want to see if it executes first and then we'll worry about setting
the author.


## Who is "I" in a Scenario?

Cool! It runs... and fails because anonymous *is* still shown on the page. The question
now is: how do we get the current user? The step says "I author". But who is "I" in this
case? In `product_admin.feature`, you can see that "I" is whomever we logged in as.
We didn't specify what the username should be for that user, but whoever is logged
in is who "I" represents.

When we worked with the `ls` scenarios earlier, we needed to share the command output
string between the steps of a scenario. In this case, we have a similar need: we
need to share the user object from the step where we log in, with the step where
"I" author some products. To share data between steps, create a new `private $currentUser;`.

In `iAmLoggedInAsAdmin`, add `$this->currentUser = $this->thereIsAnAdminUserWithPassword()`.
Click to open that function. It creates the `User` object of course, but now we need
to also make sure it returns that.

And that's it! This login step will cause the `currentUser` property to be set and
in `iAuthorProducts` we can access that and pass it into `createProducts()` so that
each product us authored by us. It's pretty common to want to know *who* is logged
in, so you'll likely want to use this in your project.

And hey it even passes! Now you can continue to write scenarios in terms of actions
that "I" take and we will actually know who "I" is.
