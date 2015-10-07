# When *I* do Something: Handling the Current User

Login is tested, let's now head over to the Product Admin Feature. We built
this out when we were just thinking about making really nice scenarios. Now we know
that most of the language that I used inside of here was because I am familiar with
the built in definition list. 

Let's get these scenarios passing! I'll start with running just the "List available products"
scenario on line 6. To do that type `.vendor/bin/behat` point to the file and then add `:6`. 
The number, in our case 6, has to be the line that the scenario starts on, and it runs only that
scenario.

We're getting a print out of our missing step definitions, so go head and copy those and paste
them in the `FeatureContext` class. 

For the `thereAreProducts` function change the variable to `count` and create a for loop and
inside of here create some products. And set some data on here. Notice, we said there are 5
products, we didn't say what those products are called because we don't care which is fine. Only
include info in your scenario that you actually care about. Because we don't have any detailed
information here we can create some random data. Since we'll need to get the entity manager
a lot let's create a `private function getEntityManager()` `return $this->getContainer()->`
there's actually a service that represents the entity manager. Perfect! 

Now back up in our `thereAreProducts` function type `$em = $this->getEntityManager();` and
the usual `$em->persist($product);` and an `$em->flush();` at the bottom. Easy stuff now that
we've got Symfony booted. 

Next thing, `iClick`, update the variable here to `LinkText` because we want this to work just like
the built in "I follow" function. Except that using the phrase "I follow" is not how anyone would naturally
speak in terms of clicking a link. So, we want to use "I click" instead. This uses the actual link text,
not a CSS selector. To use the named selector we say `$this->getPage()->findLink()` because this is a link
and pass it the `$LinkText` and then call `click();` on that. Or even shorter, instead of `findLink()` we
can just say, `->clickLink();` and pass it `$LinkText`. This looks for a link inside of page and then clicks
it. 

And down in `iShouldSeeProducts` we're looking for a certain number of products, which means once we get
into the Admin section we're just looking for the number of rows in this table here. There aren't any 
special classes on this table, so we'll just use the `table` class. 

First, let's find the table with the `table` class and use the `assertNotNull`, and now we want to count
the rows inside of the tbody. Do that with `assertCount(intval($count))` which will make sure `$count` is
an integer. Now we need to count the number of `<tr>`'s that are inside of this `<tbody>`. To do this we'll
need another `find()` inside the table for these `<tr>` tags and count them. Remember, one you find a specific
element it still has a find method on it which you can use to find more elements. In our example we'll use
`$table->findAll()` which will find all of the `<tr>` tags instead of just one and return them in an array. 

Don't forget that this first argument is still `css` since that's how we're searching. And the same thing up here,
where PhpStorm is yelling at me. Ok, let's try that out!

Hmm it gets a little futher but still doesn't pass, it's error says "link "Products" not found". It's trying
to find a link with the word "products" and isn't having much luck. Back to our handy debugging technique, 
right before the error we'll add,

    And print last response

Let's run that one again. Scroll up all the way to the top, ahhh of course! We're on the login page. We 
need to be logged in before we try and go to the products page. We've already handled the login process
in the `Authentication.feature` file. What we really want to do is copy and paste all of these lines here.
But, it would be really lame to have to put all of that infront of all of my scenarios. Instead, I just
want to say "I'm logged in as an admin". So, update this scenario to have the line,

    And I am logged in as an admin

Ooo another new step definition will be needed! We'll use this to do all of the work necessary to login. 
Rerun the test and grab the step definition code that behat so thoughtfully provides for us and paste
it into our `FeatureContext` class. 

Using Mink we'll do all the same steps as we did to login. First thing, go to the login page. Normally you'd
say `$this->getSession()->visit('/login')` but the problem with that is that it will go to the page /login
but we need behat to prefix that with our base URL. When you want to use the session to go to a page 
you'll use a shortcut called `visitPath` which will handle the prefixing. 

Once we're on the login page we need to fill out the username and password fields and press the button.
This is all using the named selector so we can say `$this->getPage()->findField('Username')->setValue()`
or we can use a shortcut to do all of that at once, which is `fillField`. Give this the `locater` which is
the `Username` and the value that we want to input, admin. 

Before this we of course want to make sure that we have a user created that this login will actually work for.
We have a function already that creates a user, `thereIsAnAdminUserWithPassword`, so let's call that down here.
And we'll use this for our usual admin/admin. And we'll fill the password field. And here's the shortcut, we could
say `findButton` but instead we'll say `pressButton('Login')`. This basically matches what we have over here so
that should be it! Run it, hey we're in good shape here!

Time for a challenge! Notice that whenever you have products it shows the author as 'admin' which is the user
that created it or 'anonymous' if it was created via another method. If we refresh right now the author
is always listed as 'anonymous' because when we create the product we aren't setting the author. I now want to
test that this table does show the correct author. Let's get a new scenario started for this. 

    Scenario: Products show author
    Given I am logged in as an admin

Instead of just saying there are five products I'll say,

    And I author 5 products 

This is a new bit of language here which will need a step definition.




