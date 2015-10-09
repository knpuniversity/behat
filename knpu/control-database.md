# Controlling the Database

Eventually, you'll need to insert test data at the beginning of your
scenarios. And that's when things get tricky. So, let's learn to do
this right.

You should never assume there is data in the database, instead you should intentionally put it there with a `Given`. 

I'll go to the top of this scenario and add,

     Given there is an admin user "admin" with password "admin"

I've made up the language for this step. Now change the `Given` line below this to `And` for readability. 
Let's run it again and it should print out the step definition for my new made up step, and it does!
Copy that and drop it into our `FeatureContext` class. Change `arg1` to "username" `arg2` to "password"
and again "username" and "password" down here. 

In this `Given` here it's our chance to have alot of control. We want to put this user into our database.
If I had Symfony boostrapped that would be really easy, I would create my entity, persist it and flush.
Let's do that! Even if you're not using Symfony you'll go through basically the same process to bootstrap
your application to get access to all of your normal useful objects and functions. 

What we sort of want is for Symfony to boot once at the beginning of the test suite. So, we'll boot Symfony
one time and then afterwards in all of our scenarios we can access Symfony's container. I'll make a
new `public function bootstrapSymfony()` and inside of here we'll do just that. We'll need a 
couple of require statements for `autoload.php` and the `appKernel.php` class and then `$kernel = new AppKernel();`
And pass it the environment which is `test` and debug `true` so we can see errors. `$kernel->boot();`.
That's everything  you need to boostrap a Symfony application. 

What we really want is access to the service container. Create a new `private static $container;` property
Down here I'll set that with `self::container = $kernel->getContainer();`. If we called this boostrap Symfony
function then well have a static function called container, and let's update this to be a `public static function`.
I'm making this all static only because it allows us to have one container across all of our different scenarios.

We could call this boostrap Symfony function manually but instead remember the hook system? We're going to use
that again. Before we used `@BeforeScenario` and `@AfterScenario`, but there's other ones like before test suite
and after test suite. So here type `@BeforeSuite`, that will run one time even if we're testing 10 features and 
100 different scenarios and make sure the container is actually set. 

Inside of our `ThereIsAnAdminUserWithPassword` step definition we can go to work creating our user. 
`$user = new User();` I already have a user entity setup in this project and it's pretty standard,
set the username and set the password. I have the mechanism already to encode the password when we
save this. We don't want the velociraptors hacking the site to take down the fences. In my app to make
a user an admin set the user's role to `ROLE_ADMIN`. Now we just need the entity manager which we can get 
with `$em = self::$container->get('doctrine')` and to get the Doctrine service out call `->getManager();` on it.
From here it's `$em->persist($user);` `$em->flush();`. And that should do it!

To prove it, since we already have an admin user in the database let's change this to `admin2` and give it a go.
This should not work, since this user isn't in the database yet... well unless it's created by the code we
just wrote. Brilliant!

This is huge, we're guaranteeing there's an admin user and to do that we bootstrapped our application which makes
us dangerous since we have access to all of our useful services.
