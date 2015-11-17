# Finding inside HTML Tables

To finish the scenario, we need a `Then` that's able to look for a check mark on
a specific row. The check mark icon itself is an element with a `fa fa-check` class.

There is no built in definition to find elements inside of a specific row. So let's
describe this using natural language. How about:

[[[ code('b5e1a9c227') ]]]

Execute Behat to get that definition printed out for us:

```bash
./vendor/bin/behat features/product_admin.feature:21
```

When you're feeling *really* lazy, you can add a `--append-snippets` flag and Behat
will put the definitions inside of the `FeatureContext` class for you:

```bash
./vendor/bin/behat features/product_admin.feature:21 --append-snippets
```

Change `arg1` to be `rowText`:

[[[ code('d7f998cd64') ]]]

Ok, this is a bit harder. First, we need to find a row that contains that `$rowText`
and *then* look inside of just *that* element to see if it has a `fa-check` class in it.

Start by finding via CSS, `$row = $this->getPage()->find('css')`. For the selector,
use `table tr:` and then the `contains` pseudo selector that looks for some text inside.
Pass `%s` and set the value using `sprintf()`:

[[[ code('ee059f0baa') ]]]

`$row` will now be the first `tr` containing this text, or null. It's not perfect:
if `$rowText` had some bad characters in it, the selector would fail. But this is
my test so I'll be lazy until I can't. Add `assertNotNull()` function:

[[[ code('e3aa726b1b') ]]]

Finally, assert that the row's HTML has a `fa-check` class inside of it with
`assertContains()`:

[[[ code('f83db91ed0') ]]]

Moment of truth:

```bash
./vendor/bin/behat features/product_admin.feature:21
```

We're green! Now, let's get even harder.
