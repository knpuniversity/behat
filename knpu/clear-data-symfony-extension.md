# The SymfonyExtension & Clearing Data Between Scenarios

Change the user and pass back to match the original user in the database: "admin"
and "admin":

[[[ code('40b5daac0b') ]]]

*Now* rerun the scenario:

```bash
./vendor/bin/behat features/web/authentication.feature
```

Boom! This time it explodes!

> Integrity constraint violation: UNIQUE constraint failed: user.username

We already have a user called "admin" in the database... and since I made that a
unique column, creating *another* user in `Given` is putting a stop to our party.

## Clearing the Database Before each Scenario

Important point: you should start every scenario with a blank database. Well, that's
not 100% true. What I want to say is: you should start every scenario with a predictable
database. Some projects have look-up tables - like a "product status" table with rows
for in stock, out of stock, back ordered, etc. I really hate these, but anyways,
sometimes there are tables that *need* to be filled in for anything to work. You'll
want to empty the database before each scenario... except for any lookup tables.

Since *we* don't have any of these pesky look-up guys, we can empty everything before
every scenario. To do this, we'll of course, use hooks.

Create a new `public function clearData()`:

[[[ code('664a9b2c3b') ]]]

Clearing data now is pretty easy, since we have access to the entity manager via
`self::container->get('doctrine')->getManager();`:

[[[ code('73379cb924') ]]]

Now we can issue DELETE queries on the two entities that we care about so far:
product and user. I'll use `$em->createQuery('DELETE FROM AppBundle:Product')->execute();`:

[[[ code('ef81ad1396') ]]]

Copy and paste that line and change "Product" to "User":

[[[ code('17d499e619') ]]]

Oh and make sure that says "Product" and not "Products". Activate all of this with the
`@BeforeScenario` annotation:

[[[ code('ff2e48a1ed') ]]]
 
Try it all again:

```bash
./vendor/bin/behat features/web/authentication.feature
```

Perfect! We can run this over and over because it's clearing out the data first.

## The Symfony2Extension

And, surprise! There's an easier way to bootstrap Symfony and clear out the database.
I always like taking the long way first so we can see how things work.
 
First, install a new library called `behat/symfony2-extension` with `--dev` so it
goes into my require `dev` section:

```bash
composer require behat/symfony2-extension --dev
```

An `extension` in Behat is a plugin. We're already using the `MinkExtension`:

[[[ code('27daea06b7') ]]]
 
Activate the new plugin in `behat.yml`: `Behat\Symfony2Extension:`:

[[[ code('f5e71d6cc0') ]]]

And as luck would have it, it doesn't need any configuration. It looks like we still
need to wait for it to finish installing in the terminal... there we go!  

The most important thing the Symfony2 Extension gives you is, access to Symfony's
container... but wait, we already have that? Well, this just makes it easier.

Remove the `private static $container;` property and the `bootstrapSymfony()` function.
Instead of these, we'll use a PHP 5.4 trait called `KernelDictionary`:

[[[ code('a636d6da19') ]]]

This gives us two new functions, `getKernel()`, but more importantly `getContainer()`:

[[[ code('cc77eab4d3') ]]]

It takes care of all of the booting of the kernel stuff for us, and it even reboots
the kernel between each scenario so they don't run into each other. That's important
because remember, each scenario should be completely independent of the others.
 
Search for the old `self::$container` code. Change it to `$this->getContainer()`:

[[[ code('c1941ef181') ]]]

You see that PhpStorm all of a sudden auto-completes the methods on the services
we fetch because it recognizes this as the container and so knows that this returns
the entity manager. 
 
Let's try things again!

```bash
./vendor/bin/behat features/web/authentication.feature
```

Still works! But now with less effort. If you have multiple context classes, you
can use the `KernelDictionary` on all of them to get access to the container.
 
## Clearing the Database Easily
 
OK, so what about clearing the database? It'll be a huge pain to add more and more
manual queries. Fortunately Doctrine gives us a better way: a `Purger`. Create a new
variable called `$purger` and set it to a `new ORMPurger()`. Pass it the entity manager:

[[[ code('4a1ce99a60') ]]]

After that, type `$purger->purge();`, and that's it:

[[[ code('61566dcc53') ]]]
 
This will go through each entity and clear out all of your data. If it's working,
then our tests should pass:

```bash
./vendor/bin/behat features/web/authentication.feature
```

And they do! Same functionality and a lot less code. For bigger databases with lots
of lookup tables, it may be too much to clear every table and re-add all the data
you need. In those cases, trying experimenting with creating a SQL file that populates
the database and executing that before each scenario. Or, populate an SQLite file
with whatever you want to start with, then copy this and use it as your database
before each test. That's a super-fast way to roll back to your known data set.
