# Clicking a Row in a Table (i.e. Complex Selectors)

Time for a real challenge! Deleting products, it's actually a bit harder than you
might think. Here comes some curve balls -- eh eh like that baseball pun?

We need a delete button to remove individual products. BDD Time! Start with the
scenario:

[[[ code('275ab4a5e7') ]]]

To delete a product, we need to start with one in the database. In fact, if we start
with two products, we can delete the second and check that the first is unaffected.

Add a `Given` that's similar to the one from the "show published/unpublished" scenario,
but with a slight difference:

[[[ code('25f6893a3e') ]]]

Since we *only* care about the name, we don't need to bother with adding an "is published"
row: keep things minimal! Create a product Bar and another Foo1. Man, those dinos
can't wait to get a hold of such interesting product names!

[[[ code('e854243229') ]]]

This is where it gets tricky: we'll have two rows in our table that both have 'delete'
buttons, but I only want to click "Delete" on the *second* row. Add another step
to do that:

[[[ code('9e44ecc47f') ]]]

Then, it would be super to see a flash message that confirms that the product was
deleted. Make sure Foo1 no longer appears in this list of products. And double check
that Bar was *not* also deleted:

[[[ code('366b80cfe2') ]]]

This is the first time we've seen `But`. But, it has the same functionality as `And`:
it extends a `Then`, `When` or `Given` and sounds natural. 

Try it! Run just this scenario:

```bash
./vendor/bin/behat features/product_admin.feature:42
```

Copy the new function into `FeatureContext`:

[[[ code('bd6821ad9c') ]]]

Change `arg1` to `linkText` and `arg2` to `rowText`:

[[[ code('dc54d205f9') ]]]

This isn't the first time we've looked for a row by finding text inside of it.
Let's re-use some code.

Make a new `private function findRowByText()` and give it a `$linkText` argument:

[[[ code('25c26c14e5') ]]]

Copy the two lines that find the row and `return $row`. That'll make life a little
bit easier:

[[[ code('8db9684586') ]]]

Now use `$this->findRowByText($rowText);` in the original method and also in the
new definition:

[[[ code('78be4d76b8') ]]]

Consider the row found!

## Finding Links and Buttons in a Row

To find the link, we don't want to use css: `$linkText` is the *name* of the text:
what a user would see on the site. Instead, use `$row->findLink()` and pass it
`$linkText`:

[[[ code('662afdc9c2') ]]]

I'll repeat this one more time for fun. you can find *three* things by
their text: links, buttons and fields. Use `findLink()`, `findButton()` and `findField()`
on the page *or* individual elements to drill down to find things. Add
`assertNotNull($link, 'Could not find link '.$linkText);` in case something 
goes wrong. Finally click that link!

[[[ code('a5a8562db0') ]]]

We haven't done any coding yet, but the scenario is done. Run it!

```bash
./vendor/bin/behat features/product_admin.feature:42
```

It fails... but not in the way that I expected. It says

> Undefined index: is published in `FeatureContext` line 110.

That's happening because - this time - we don't have the 'is published' column in
our table. But on line 110, we're assuming it's always there:

[[[ code('2924f7a7e3') ]]]

That's fine: I like to start lazy and assume everything is there. When I need the
steps to be more flexible, I'll add more code. Add an `isset('is published')` so
if it's set *and* equals yes, we'll publish it:

[[[ code('2c27f066f8') ]]]

Rerun this now.

```bash
./vendor/bin/behat features/product_admin.feature:42
```

It fails with:

> Undefined variable: `rowText` in `FeatureContext` line 256.

Hmm that sounds like a Ryan mistake. Yep: I meant to call this variable `$rowText`:

[[[ code('8e1ab0564c') ]]]

Now we've got the proper failure: there is no link called `Delete`.

Let's code for this! Remember, do as little work as possible.

## Coding the Delete

Add a new `deleteAction()` and a route of `/admin/products/delete/{id}`. Name
it `product_delete`. We could get fancy and add an `@Method` annotation that say
that this will only match `POST` or `DELETE` requests. Let's keep it simple for now:

[[[ code('0a9678d2a1') ]]]

And instead of adding `$id` as an argument to `deleteAction()`, I'll be even lazier
and type hint the `Product` so that Symfony queries for it for me.

Now, remove the `$product`, flush it, set a success flash message that matches what's
in the scenario and finally redirect back to the product list route:

[[[ code('1a2627ad6b') ]]]

To add the delete link, find `list.html.twig` and add a column called Actions. Since
you should POST to delete things, add a small form in each row, instead of a link
tag. Make the form point to the `product_delete` route and add `method="POST"`. And
instead of having fields, it only needs a submit button whose text is "Delete".
Add some CSS classes to make it look nice - don't get too lazy on me:

[[[ code('e85520dda4') ]]]

Perfect!

Try it!

```bash
./vendor/bin/behat features/product_admin.feature:42
```

## Click/Follow Links, Press Buttons

Hmmm, it fails in the *same* spot:

> And I click "Delete" in the "Foo1" row.

Either something is wrong with the way we wrote the code, there's an error on the
page or we're not even on the right page. Right now, we can't tell.

Since it's failing on the "I click" line, hold command and click to see its step
definition function. Var dump the `$row` variable to make sure we're finding the
row we expected:

[[[ code('559aba5c7c') ]]]

The other thing we can do is temporarily make this an `@javascript` scenario and
add a `break`:

[[[ code('49f511e2a2') ]]]

Try it again:

```bash
./vendor/bin/behat features/product_admin.feature:42
```

Ah-ha! We have an exception on our page and had no idea! I forgot to pass the `id`
when generating the URL:

[[[ code('f4463bafe8') ]]]

Keep the debugging stuff in and try again:

```bash
./vendor/bin/behat features/product_admin.feature:42
```

It stops again, but no error this time: the delete button looks fine. Press enter
to keep this moving. 

But it still fails! The test could not find a "Delete" link to click in the "Foo1"
row. The cause is subtle: links and buttons are not the same. We click links but
we press buttons. In the scenario I should say I *press* Delete instead of click:
 
[[[ code('b3f23328ff') ]]]

More importantly, inside of our `FeatureContext`, update to use `findButton()` and
change the action from `click` to `press`.

[[[ code('42e5d6587b') ]]]

For clarity, change `$link` to `$button` and `$linkText` to `$buttonText`.

This should solve all 99 of our problems. I even have enough confidence to remove
`@javascript` and the "break" line. Rerun the test!

```bash
./vendor/bin/behat features/product_admin.feature:42
```

Finally green!

Clean up the code a bit more by changing `findButton()` to `pressButton()`:

[[[ code('d8659c3a96') ]]]

Remember, this shortcut also works with `clickLink()` and `fillField()`.

```bash
./vendor/bin/behat features/product_admin.feature:42
```

And it still passes. You just won the Behat and Mink World Series! I know, *terrible*
baseball joke - but the world series is on right now.
