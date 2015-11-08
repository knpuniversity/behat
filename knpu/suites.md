# Context Organization and Behat Suites

When Behat loads, it reads step definitions from `FeatureContext` and `MinkContext`
because of the `behat.yml` setup:

[[[ code('fa2929a0ac') ]]]

This is a really powerful idea: instead of having one giant context class, we
can break things down into as many small, organized pieces. We might have one
context for dealing with adding users to the database and another for the API.
If you look at *our* `FeatureContext`, we already have two very different ideas
mixed together: some functions interact with the terminal and others help deal
with a web page.

This is *begging* to be split into 2 classes. Let's copy `FeatureContext` and
create a new file called `CommandLineProcessContext`. Update the class name and
get rid of anything in here that doesn't help do things with the command line:

[[[ code('f46120d7d9') ]]]

In `FeatureContext`, do the opposite: remove all the things that have nothing to
do with working on a web site. Delete these functions and our before and after
scenario hooks:

[[[ code('bdfbf5880c') ]]]

That's a lot clearer.

Of course to keep our tests passing, we need to tell Behat about our new context:

[[[ code('88dffb8a99') ]]]

If we run `behat` now:

```bash
$ ./vendor/bin/behat
```

It should run all of our features: the ls *and* web stuff. It does, and it works!
Ignore the undefined functions - those are from `product_admin.feature`: we haven't
finished that yet.

## Multiple Suites

But we can go further. in `behat.yml`, check out the `suites` key. Currently, we
have one "suite" called `default`:

[[[ code('89371b96d7') ]]]

But you could have many. What's a suite? It's a combination of a set of feature files
and the contexts that should be used for them. Think about it: the `ls.feature` is the
only feature that needs `CommandLineProcessContext`. And every other feature *only* needs
`FeatureContext` and `MinkContext`. This is the perfect use-case for a second suite that
I'm going to call `commands`. In this case, only add the `CommandLineProcessContext`:

[[[ code('a1e52f68ea') ]]]

Remove that from the `default` suite:

[[[ code('13eb71d4b0') ]]]

When you execute Behat, it uses the `default` suite unless you tell it which one to use
with the `--suite` option. Try it with `--suite=commands` and then run `ls.feature`:

```bash
$ ./vendor/bin/behat --suite=commands features/ls.feature
```

Or you can use the `-dl` option to see only the definition lists associated with the
contexts in that suite:

```bash
$ ./vendor/bin/behat --suite=commands features/ls.feature -dl
```

Without `--suite`, we see definitions for the `default` suite:

```bash
$ ./vendor/bin/behat -dl
```

And yes, we can go *even* further by telling each suites which features belong to them.
Under the `features/` directory, create two new directories called `commands` and `web`
Let's organize: move `ls.feature` into `commands/` and the other four features into `web/`.
Now, add a `paths` key to the `default` suite and set it to `[%paths.base%/features/web]`:

[[[ code('fa4bc2afea') ]]]

`%paths.base%` is a shortcut to the root of the project. For the `commands` suite, do
the same thing to point to `commands/`:

[[[ code('04cef3a658') ]]]

Now, if you run the `default` suite:

```bash
$ ./vendor/bin/behat
```

Behat knows to only execute the features in the `web/` directory. With `--suite=commands`,
it *only* runs the features inside of `commands/`:

```bash
$ ./vendor/bin/behat --suite=commands
```

So if you have two very different things that are being tested, consider separating
them into different suites entirely. But at the very least, use multiple contexts to
keep organized and stay sane.
