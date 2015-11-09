# Controlling the Database

Eventually, you'll need to insert test data at the beginning of your
scenarios. And that's when things get tricky. So, let's learn to do
this right.

First: never assume that there is data in the database at the beginning of a scenario.
Instead, you should put any data you need there intentionally with a `Given`. 

Go to the top of this scenario and add:

[[[ code('cad91eec8d') ]]]

I'm inventing this language - it describes something that needs to be setup using
natural language. Change the `Given` line below this to `And` for readability:

[[[ code('c006d7de91') ]]]

Run it again: it should print out the step definition for this new language.

```bash
./vendor/bin/behat features/web/authentication.feature
```

And it does! Copy that and put it into our `FeatureContext` class. Change `:arg1`
to `:username` and `:arg2` to `:password`. Update the arguments to match:

[[[ code('69a871dda0') ]]]

To fill this in, we want to insert a new user in the database with this username and
password. If Symfony were loaded, that would be really easy: I'd create a User
object, set some data then persist and flush it. So let's do that! Even if you're
not using Symfony, you'll use the same basic process to bootstrap your application
to get access to all of your normal useful objects and functions. 

## Bootstrapping Symfony in FeatureContext

We only need to boot Symfony once at the beginning of the test suite. Afterwards
all of our scenarios will be able to access Symfony's container. Make a new
`public function bootstrapSymfony()`:

[[[ code('7db161e9a8') ]]]

And inside, we'll do exactly what its name says.

We'll need a couple of require statements for `autoload.php` and the `AppKernel.php` class:

[[[ code('51198ff16b') ]]]

Then, it's as easy as `$kernel = new AppKernel();`. Pass it the environment - `test` -
and the debug value - `true` - so we can see errors. Finish with `$kernel->boot();`:

[[[ code('7d9a8314f9') ]]]

Congrats - you just bootstrapped your Symfony app.

What we really want is access to the service container. To get that, create a new
`private static $container;` property:

[[[ code('b48a7907a9') ]]]

Then in the method, set that with `self::$container = $kernel->getContainer();`:

[[[ code('4e65f60b6c') ]]]

Now, as long as we call `bootstrapSymfony()` first, we'll have access to the container.
Oh, and update the method to be a `public static function`:

[[[ code('ecf15325f4') ]]]

I'm making this all static because it allows us to have one container across all of our
different scenarios. Because remember, each scenario gets its own context instances.

We could call this method manually, but that's not fancy! Remember the hook system?
We used `@BeforeScenario` and `@AfterScenario` before, but there are other hooks,
like `@BeforeSuite`. Let's use that!

[[[ code('30d74cbe9c') ]]]

Behat will call this method one time, even if we're testing 10 features and 100 scenarios.

## Saving a New User

Inside of the`thereIsAnAdminUserWithPassword()` step definition, let's go to work!
I already have a `User` entity setup in the project, so we can say `$user = new User()`.
Then set the username and the "plainPassword":

[[[ code('cc7213d2ab') ]]]

I have a Doctrine listener already setup that will encode the password automatically.
Which is good: it's well-known that raptors can smell un-encoded passwords...

In this app, to make this an "admin" user, we need to give the user `ROLE_ADMIN`:

[[[ code('4c81a8d91e') ]]]

Now the moment of truth: we need the entity manager. Cool! Grab it with
`$em = self::$container->get('doctrine')` (to get the Doctrine service) and then
`->getManager();`:

[[[ code('fb96fd3486') ]]]

It's easy from here: `$em->persist($user);` and `$em->flush();`:

[[[ code('dae7557c9c') ]]]

And that should do it!

We already have a user called `admin` in the database, so let's test this using `admin2`.
Give it a go!

```bash
./vendor/bin/behat features/web/authentication.feature
```

This should not work, since this user isn't in the database yet... unless it's
created by the code we just wrote! Brilliant!

This is huge: we're guaranteeing there's an `admin2` user by bootstrapping our app
and being dangerous with all of our useful services.
