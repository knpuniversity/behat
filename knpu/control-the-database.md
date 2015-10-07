# Control the Database

We need more scenarios! The first one I want to work on is for authentication. 
Because, you can't do much until you login. This already exists so we need to
describe the functionality of logging in. Let's add a scenario,

    Scenario: Logging in

We remember from running our Behat -dl option that we have a lot of built in functionality
already, so if we can use this and save ourselves the effort having to code stuff, let's
do that! First thing we want to do is say,

    Given I am on 

We want to start on the homepage, PhpStorm is helping me with autocomplete. If I hit tab
it does some weird stuff, like adding this extra line up there, but using autocompelte is
still a nice thing to do. Over in our terminal we can find the "Given I am on" which is followed
up with all this craziness right here, which is actually just a wildcard. So, everytime you see
" craziness and another " that's a wildcard. 

Real quick note, even though there's `Given`, `When` and `Then` in front of these it doesn't actually
matter how you use them. I could actually say,

    Then I am on "/"

It doesn't make sense from an English grammar perspective, but it would still run.

Alright! Given I am on "/", the next thing I should do is click 'login', the built in definition for
that is "I follow", there's no built in definition for 'click' but we'll add one later since that's
how most people actually talk in reference to links on websites.

For now let's add,

     When I follow "Login"

Remember, these all use the named selector, so we use 'Login' because the name that renders on the frontend
of our site for this link is 'Login', so that's how it's going to be found. 

Our next step in logging in is to fill in these two fields on the login page. Again, we'll use the named
selector to refer to the labels username and password and it will find the field associted with them. There
are a couple of definitions to use, but the one I like is "When I fill in field with <value>". I'll
use an and here,

     And I fill in "Username" with "admin"
     And I fill in "Password" with "adminpass"

That isn't the right password so it won't quite work yet. And finally we need to actually press the login button.

     And I press "Login"

Notice that you follow a link but you press a button, they're not exactly the same thing. And for our example
here we're working with a button. Then we need to find something to assert, so let's plug the username and password
and login to see what we should expect under normal conditions. Nothing says "Congratulations, you're in!" 
but our login button did change to "Logout" so let's use a built in definition for "Then I should see <text>". 

     Then I should see "Logout"

That's a solid looking scenario, let's run it! `./vendor/bin/behat features/web/authentication.feature`. It
runs but it also fails with the comment: "The text "Logout" was not found anywhere in the text of the current
page." This is happening because this isn't the right password, but let's pretend that we didn't know that. 
Debugging tip number 1, right before the failing step you can use another built in step definition called

     And print last response

So if we run this again it is still going to fail, but above it prints out the entire page's code which I know
is ugly. This is javascript down here but there are two important things you can see. First, we're still on
the login page for some reason and if we scan down a little bit here we will see the error message for
"invalid credentials". This way of debugging is not perfect, but it is really easy.

Let's remove our debug line and update this to the correct password which is "admin". And now let's rerun it!
Cool, it passes! But we have a big problem here. We're assuming that there is already an admin user in the database
with password admin. What if there isn't? What if that cleared out or was changed? All of our tests would start failing.
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

To prove it since we already have 
