# Practice: Find Elements, Login with 1 Step and Debug

Look at the Product Admin Feature. When we built this earlier, we were planning the
feature and learning how to write really nice scenarios. *Now* we know that most
of the language we used matches the built-in definitions that Mink gives us for free.

Time to make these pass! Run *just* the "List available products" scenario on line 6.
To do that type, `./vendor/bin/behat` point to the file and then add `:6`:

```bash
./vendor/bin/behat features/product_admin.feature:6
```

The number 6 is the line where the scenario starts. This prints out some missing
step definitions, so go head and copy and paste them into the `FeatureContext` class:

[[[ code('06882cb3d7') ]]]

## Say what you need, but not more

For the `thereAreProducts()` function, change the variable to `count` and create a
for loop:

[[[ code('ccbd9ffa4b') ]]]

Inside, create some products and put some dummy data on each one:

[[[ code('81f5c1361f') ]]]

Why dummy data? The definition says that we need 5 products: but it doesn't say what
those products are called or how much they cost, because we don't care about that for
this scenario. The point is: only include details in your scenario that you actually
care about.

We'll need the entity manager in a lot of places, so create a
`private function getEntityManager()` and `return $this->getContainer()->get()`
and pass it the service name that points directly to the entity manager:

[[[ code('53f870450e') ]]]

Perfect! 

Back up in `thereAreProducts()`, add `$em = $this->getEntityManager();` and the usual
`$em->persist($product);` and an `$em->flush();` at the bottom. This is easy stuff
now that we've got Symfony booted:

[[[ code('2b6399a2b8') ]]]

## Using "I Click" to be more Natural

Go to the next method - `iClick()` - and update the argument to `$linkText`:

[[[ code('be808d98a5') ]]]

We want this to work just like the built-in "I follow" function. In fact, the only reason
we're not just re-using that language is that nobody talks like that: we click things.

Anyways, the built-in functionality finds the link by its text, not a CSS selector.
To use the named selector, add `$this->getPage()->findLink()`, pass it `$linkText`
and then call `click();` on that. Oh heck, let's be even lazier: just say, `->clickLink();`
and be done with it:

[[[ code('32c6bbe2c5') ]]]

This looks for a link inside of page and then clicks it. 

Finally, in `iShouldSeeProducts()`, we're asserting that a certain number of products
are shown on the page:

[[[ code('e292985d0c') ]]]

In other words, once we get into the Admin section, we're looking for the number of rows
in the product table.

There aren't any special classes to help find *this* table, but there's only one
on the page, so find it via the `table` class:

[[[ code('ce9e3a5c3d') ]]]

Next, use `assertNotNull()` in case it doesn't exist:

[[[ code('ca7850695b') ]]]

Now, use `assertCount()` and pass it `intval($count)` as the first argument:

[[[ code('ecacd83263') ]]]

For the second argument, we need to find *all* of the `<tr>` elements inside
of the table's `<tbody>`. Remember, once you find an element, you can search *inside*
of it with `find()` or `$table->findAll()` to return an array of elements instead
of just one. And don't forget that the first argument is still `css`: PhpStorm is
yelling at me because I like to forget this. Ok, let's try that out!

```bash
./vendor/bin/behat features/product_admin.feature:6
```

## Debugging Failures!

Ok, it gets *further* but still fails. It says:

> Link "Products" not found

It's trying to find a link with the word "Products" but isn't having much luck. I
wonder why? We need to debug! Right before the error, add:

    And print last response

Run that one again:

```bash
./vendor/bin/behat features/product_admin.feature:6
```

Scroll up... up... up... all the way up to the top. Ahhh of course! We're on the
login page. We forgot to login, so we're getting kicked back here.

## Logging in... in one Step!

We already did all that login stuff in `authentication.feature`, and I'm tempted
to copy and paste all of those lines to the top of this scenario:

[[[ code('21fe58d69c') ]]]

But, it would be pretty lame to need to put *all* of this at the top of pretty much
every scenario. You know what would be cooler? To just say:

[[[ code('2d374c76a7') ]]]

Ooo another new step definition will be needed! Rerun the test and copy the function
that behat so thoughtfully provides for us. As usual, put this in `FeatureContext`:

[[[ code('f7b217d0f4') ]]]

Using Mink, we'll do *all* the steps needed to login. First, go to the login page.
Normally you'd say `$this->getSession()->visit('/login')`. But don't! Instead, wrap
`/login` in a call to `$this->visitPath()`:

[[[ code('8e41ace2cf') ]]]

This prefixes `/login` - which isn't a full URL - with our base URL: `http://localhost:8000`.

Once we're on the login page, we need to fill out the username and password fields
and press the button. We could find this stuff with CSS, but the named selector is
a lot easier. Say `$this->getPage()->findField('Username')->setValue()`. Ah, let's
be lazier and do this all at once with `fillField()`. Pass this the label for the
field - `Username` - and the value to fill in:

[[[ code('a94b813ab4') ]]]

But hold on: before we fill in the rest, don't we need to make sure that this user
exists in the database? Absolutely, and fortunately, we already have a function that
creates a user: `thereIsAnAdminUserWithPassword()`. Call that from our function and
pass it the usual `admin` / `admin`:

[[[ code('ad64d9b42c') ]]]

Finish by filling in the password field and pressing the button. For that, there's
another shortcut: instead of `findButton()` then `press()`, use `pressButton('Login')`:

[[[ code('d8d10787fd') ]]]

This reproduces the steps from the login scenario, so that should be it! Run it!

```bash
./vendor/bin/behat features/product_admin.feature:6
```

We're in great shape.
