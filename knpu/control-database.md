# Controlling the Database

Eventually, you'll need to insert test data at the beginning of your
scenarios. And that's when things get tricky. So, let's learn to do
this right.

First: never assume that there is data in the database at the beginning of a scenario.
Instead, you should put any data you need there intentionally with a `Given`. 

Go to the top of this scenario and add:

    Given there is an admin user "admin" with password "admin"

I'm inventing this language - it describes something that needs to be setup using
natural language. Change the `Given` line below this to `And` for readability. 

Run it again: it should print out the step definition for this new language.

```bash
./vendor/bin/behat features/web/authentication.feature
```

And it does! Copy that and put it into our `FeatureContext` class. Change `:arg1`
to "username" `:arg2` to ":password". Update the arguments to match.

To fill this in, we want to insert a new user in the database with the username and
password. If I Symfony were loaded, that would be really easy: I'd create a User
object, set some data then persist and flush it. So let's do that! Even if you're
not using Symfony, you'll use the same basic process to bootstrap your application
to get access to all of your normal useful objects and functions. 

## Bootstrapping Symfony in FeatureContext

Now, we only need to boot Symfony once at the beginning of the test suite. Afterwards
all of our scenarios will be able to access Symfony's container. Make a new
`public function bootstrapSymfony()` and inside, we'll do exactly what its name says.
We'll need a  couple of require statements for `autoload.php` and the `AppKernel.php`
class. Then, it's as easy as `$kernel = new AppKernel();`. Pass it the environment -
`test` - and the debug value - `true` - so we can see errors. Finish with
`$kernel->boot();`. Congrats - you just bootstrapped your Symfony app. 

What we really want is access to the service container. To get that, create a new
`private static $container;` property. Then in the method, set that with
`self::container = $kernel->getContainer();`. Now, as long as we call `boostrapSymfony()`
first, we'll have access to the container. Oh, and update the method to be a
`public static function`: I'm making this all static because it allows us to have
one container across all of our different scenarios. Because remember, each scenario
gets its own context instances.

We could call this method manually, but that's not fancy! Remember the hook system?
We used `@BeforeScenario` and `@AfterScenario` before, but there are other hooks,
like `@BeforeSuite`. Let's use that! Behat will call this method one time, even if
we're testing 10 features and 100 scenarios.

## Saving a New User

Inside of the`ThereIsAnAdminUserWithPassword` step definition,let's go to work!
I already have a `User` entity setup in the project, so we can say `$user = new User()`.
Then set the username and the "plainPassword": I have a Doctrine listener already
setup that will encode the password automatically. Which is good: it's well-known
that raptors can smell un-encoded passwords...

In this app, to make this an "admin" user, we need to give the user `ROLE_ADMIN`.
Now the moment of truth: we need the entity manager. Cool! Grab it with
`$em = self::$container->get('doctrine')` (to get the Doctrine service) and then
`->getManager();`. It's easy from here: `$em->persist($user);` and `$em->flush();`.
And that should do it!

We already have a user called `admin` in the database, so let's test this using `admin2`.
Give it a go!

```bash
./vendor/bin/behat features/web/authentication.feature
```

This should not work, since this user isn't in the database yet... unless it's
created by the code we just wrote! Brilliant!

This is huge: we're guaranteeing there's an admin2 user by bootstrapping our app
and being dangerous with all of our useful services.
