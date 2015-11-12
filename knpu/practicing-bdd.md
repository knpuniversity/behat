# Practicing BDD: Plan, then Build

One of the most beautiful things about Behat is having the opportunity to do
behavior driven development. This is when you write the feature first, the scenarios
second and *then* you code it. You *plan* the behavior, and *then* make it come to
life.

So far... we haven't really been doing that. We have a existing site and we've been
writing features and scenarios to describe how it *already* behaves. That's ok, and
sometimes you'll do that in your real development life. But now it's time to do BDD
correctly.

## Step 1: Describe the Scenario

In the "Add a new product" scenario we're describing a feature that does *not* exist
on the site yet. We planned this scenario earlier by planning - basically imagining -
what the best behavior should be. And oops, I just noticed a typo. This should be:

[[[ code('a6733607eb') ]]]

## Step 2: Execute Behat

With that fixed, let's start to bring this feature to life by running *just* this
scenario:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

Our mission is clear: code *just* enough to fix a failure, then re-execute Behat
and repeat until it's all green. The first failure is from "When I click 'New Product'".
That makes sense: that link doesn't exist. But there is something else going on too.
Add an "And print last response" line and try again:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

Of course: we also haven't logged in yet. Copy that line from the other scenario:

[[[ code('46a95df1ce') ]]]

We didn't think of this during the design phase, but clearly we need to be logged
in. Try things again:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

## Step 3: Add (a little) Code to make the Step Pass

Same failure, but now for the right reason: we're missing that link. Time to add it.

Open up the template for this page - `list.html.twig`. A link would look real nice
up top. Don't add the `href` yet: just put in the text "New Product" and make it
look nice with some CSS classes and a little icon:

[[[ code('0c157ab999') ]]]

Other than some easy-win styling, I want to do as little work as possible to get
each step of the scenario to pass.

Refresh: there's the button. It doesn't go anywhere yet, try it:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

## Step 4: Repeat until Green!

This time, it *did* click "New Product", but fails because it doesn't see any fields
called "Name". No surprise: the link doesn't have an `href` and we don't even have
a new products page.

To get this step to pass, I guess we need to create that. In `ProductAdminController`,
make a new `public function newAction()` and set its URL to `/admin/products/new`.
Name the route `product_new` so we can link to it. Inside the method, render a template
called `product/new.html.twig`:

[[[ code('06c33f5ef5') ]]]

Easy enough!

In the `product/` directory create that template: `new.html.twig`. Extend the base
layout - `layout.html.twig` - and add add a `body` block. Add a `form` tag and make
it submit right back to this same URL with `method="POST"`:

[[[ code('9c389fab72') ]]]

I am not going to use Symfony's form system for this because (a) we don't have to
and (b) my only goal is to get these tests passing. If you *did* want to use Symfony's
form system, this is when you would start doing that!

To keep this moving, I'll paste in a bunch of code that creates the three fields
we're referencing in the scenario: `Name`, `Price` and `Description`:

[[[ code('8a46fd1a12') ]]]

The important thing is that those names match up with the label text and that each
label has a `for` attribute that points to the `id` of its field. This is how Mink
can find the label by text and then find its field.

At the bottom, we have a save button that matches the second to last step:

[[[ code('ad844e4eab') ]]]

Ok, try running the scenario again!

```bash
./vendor/bin/behat features/product_admin.feature:19
```

It's still failing in the same spot. This *might* seem weird, but if you debug this,
you'll see that I forgot to fill in the "New Products" href:

[[[ code('ae6f5c14f5') ]]]

My bad!

Run Behat again:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

We got further - it filled out and submitted the form, but didn't see the
"Product Created FTW!" flash message. Time to add form processing logic.

Add Symfony's `Request` object as an argument with a type hint. Inside of `newAction()`
add a simple `if ($request->isMethod('POST'))`:

[[[ code('2cd1fb65d2') ]]]

To be super lazy, what if we cheated by *not* saving the product and *only* showing
that flash message? The site already has some flash messaging functionality, so add
the message that the step is looking for: `$this->addFlash('success', 'Product created FTW!')`.
Finish by redirecting the user to the product page:

[[[ code('ee853f839b') ]]]

Run Behat:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

## BDD for Fixing Bugs

It passes, even though the product isn't saved. Ok, don't be a big jerk and make
your tests purposefully pass like this without coding the real functionality. But
sometimes, you'll write a scenario and discover later that there's a bug because
you forgot to test one part of the behavior. In this case I would improve my scenario
before fixing the bug by adding:

[[[ code('6c3f512bcb') ]]]

With this, the scenario won't pass *unless* the product actually shows up on the
product list. This is BDD: add a step to the scenario, watch it fail, and *then*
code until it passes.

To fix this failure, add `$product = new Product();` with code to set the name of
the product. Copy that and repeat for price and description:

[[[ code('fc4c546243') ]]]

This is missing validation, so you would do more work than this in real life,
*maybe* with a scenario that guarantees that validation works.

Finish this with `$em = $this->getDoctrine()->getManager();`, then persist and flush:

[[[ code('084b343999') ]]]

Bug fixed! Try it:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

It's green! In fact, we can go to the browser, refresh and see the Veloci-chew toy
for $20.

But there's a problem: it says that the author is "anonymous". This should be "admin"
since I created it under that user. That's definitely a bug: we forgot to set the
author in `ProductAdminController`. Ok, we know how to fix bugs using BDD: add a
step to prove the bug:

[[[ code('592adb51ac') ]]]

This is safe because there should only be the *1* product in the list. Back over
to the terminal and run the test:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

Yes! The new step fails, phew!

In `ProductAdminController`, set the author when a product is created:
`$product->setAuthor($this->getUser());`:

[[[ code('c72f8d1c88') ]]]

Run Behat again:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

Fixed!

And that folks, is behavior driven development. It's useful, and a bucket of fun.
It forces you to design the behavior of your code. But it also helps you know when
you're finished. If the scenario is green, stop coding and over-perfecting things.
And yes, *someone* will need to add a designer's touch for a really nice UI, But from
a behavioral perspective, this feature does what it needs to do. So move onto what's
next.
