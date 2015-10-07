# The SymfonyExtension and Clearing Data Between Scenarios

# Clear Data Symfony Extension

Let's change this back to "admin" "admin", rerun it and see what happens. Boom! This time
it explodes! "Integrity constraint violation Unique constraint failed user.username". We
already have a user called "admin" in the database and I made that a unique field, which
is a good thing, but now because of this `Given` we're trying to create another one.

An important thing to note about Behat is that you should start every scenario with a blank
database. Well, that's not 100% true, what I want to say is that you should start every scenario
with a predictable database. Some projects have enormous databases with lots of look up tables
that need to be filled in, which is fine, but you need to make sure that every scenario starts with
that same clean mostly empty set of data.

 We don't have any look up tables so I want to start every scenario with a completely empty database.
 To do that we can use the before scenario hook and clear out our tables. Make a new `public function clearData`.
 And clearing data now is pretty easy since we have access to the entity manager via 
 `self::container->get('Doctrine')->getManager();`. And now we can issue two queries on the two entities that
 we care about so far which are product and user. I'll use `$em->createQuery('DELETE FROM AppBundle:Products')->execute();`.
 Copy and paste that line and change "Product" to "User". Oh and make sure that says "Product" and
 not "Products". Activate all of this with the `@BeforeScenario` annotation. 
 
 Try it all again, perfect! We can run this over and over because it's clearing out the data
 beforehand. We're clearing data and bootstrapping Symfony, and good news! There's an easier
 way to do both of these things. I always like taking the long way first.
 
 To see the simpler methods, first install a new library called `behat/symfony2-extension` with `--dev`
 so it goes into my require dev. When you hear `extension` in Behat that means a plugin. We're already
 using the `MinkExtension`. 
 
 Activate the `Behat\Symfony2Extension:` And as luck would have it, it doesn't need any configuration.
 Looks like we still need to wait for it to finish installing in the terminal. There we go! 
 
 The biggest thing the Symfony2 Extension gives you is, access to Symfony's container...but we already have that?
 Well, it does it in a slightly easier way. We can get rid of this `private static $container;` line 
 and the `bootstrapSymfony` function. Instead of these we'll use a PHP5.4 trait
 
