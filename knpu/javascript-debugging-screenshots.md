# Debugging and Taking Screenshots with JavaScript

What about debugging in JavaScript? Change your scenario so it fails: change the
"Name" field to look for a non-existent "Product Name":

[[[ code('0f867dd297') ]]]

Run it:

```bash
./vendor/bin/behat features/product_admin.feature:20
```

Here's the problem: you can try to watch the browser, but it happens so quickly that
it's hard to see what went wrong. In the terminal, the error tells us that there isn't
a field called "Product Name", but with nothing else to help. Was there an error on
the page? Are we on the wrong page? Is the field calling something else? Why won't
someone tell us what's going on!?

Let me show you the master debugging tool. Google for [behatch contexts](https://github.com/Behatch/contexts).
This is an open source library that has a bunch of useful contexts - classes like
`FeatureContext` and `MinkContext` with free definitions. For example, this has a
[BrowserContext](https://github.com/Behatch/contexts/blob/master/src/Context/BrowserContext.php)
you could bring into your project to gain a bunch of useful definitions.

## Pausing Selenium

I don't use this library directly, but I do steal from it. The `DebugContext` class
has one of my favorite definitions: `iPutABreakPoint()`. Copy that and drop it into
our `FeatureContext` file:

[[[ code('e28851a97e') ]]]

Or you could even create your own `DebugContext` if you wanted to organize things a bit.
Shorten this to "I break". To use this, add this language directly *above* the "Product Name"
step that's failing:

[[[ code('0eabe02db2') ]]]

The "I" part of this language is optional. Head back to the terminal to try this:

```bash
./vendor/bin/behat features/product_admin.feature:20
```

This time, the modal pops open, and the browser freezes. The terminal just says:
"Press [RETURN] to continue..." That's right: it's *waiting* for us to look at the
page and debug the issue. Once we know what the problem is, hit enter to let it finish.
This is my *favorite* way to debug!

## Taking Screenshots

But there are more cool things, like `iSaveAScreenshotIn()`. Copy that definition and
paste it into `FeatureContext`. Change the language to "I save a screenshot to" and
remove `$this->screenshotDir` thing since we don't have that. To save screenshots to
the root of your project, replace it with `__DIR__'/../../'`:

[[[ code('42735cf5d8') ]]]

In the scenario add:

[[[ code('50edb2a054') ]]]

Run it!

```bash
./vendor/bin/behat features/product_admin.feature:20
```

The modal opens and it still fails at the "New Product" step. But *now* we have a
fancy new `shot.png` file at the root of the project that shows exactly what things
looked like when the test failed. Woah.

## Saving Screenshots on Failure

If you're using continuous integration to run your tests - which you should be! - 
this can help you figure out *why* a test failed, which is normally pretty hard
to debug. By using the hook system - something like `@AfterScenario` -  you could
automatically save a screenshot on every failure. Check out our blog post about that:
[Behat on CircleCI with Failure Screenshots](/blog/circle-ci-behat-screenshots).

Anyways, remove this line and change "Product Name" back to "Name" so that the scenario
passes again:

[[[ code('d702441737') ]]]
