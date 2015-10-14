# Behat Hooks Background

Behat experts coming through! Seriously, we've covered it: Behat reads the steps,
finds a function using this nice little pattern, calls that function, and then goes
out to lunch. That's all that Behat does, plus a few nice extras.

Let's dive into some of those extras! This scenario creates the "john" and "hammond"
files inside this directory but doesn't even clean up afterwards. What a terrible roommate.

Let's first put these into a temporary directory. We'll use the `__construct` function
because that's called before each scenario. Type `mkdir('test');` and `chdir('test');`:

[[[ code('2068e109ff') ]]]

Over in the terminal, delete the "john" and "hammond" files so we can have a fresh
start at this. Rerun Behat for our ls scenario:

```bash
vendor/bin/behat features/ls.feature
```

Everything still passes and hey look there's a little `test/` directory and
the "john" and "hammond" are inside of that. Cool.

Ready for the problem? Rerun that test one more time. Now, errors show up that say:

> mkdir(): file exists.

This error didn't break our test but it does highlight the problem that we don't have
any cleanup. After our tests run these files stick around.

We need to run some code after every scenario. Behat has a system called "hooks" where 
you can make a function inside of your context and tell Behat to call it before
or after your scenario, entire test suite or individual steps.

Create a new public function inside called `public function moveOutOfTestDir()`:

[[[ code('154e3fe2dd') ]]]

This will be our cleanup function. Use `chdir('..');` to go up one directory. Then,
if the `test/` directory exists - which it should - then we'll run a command to remove that:

[[[ code('a7efbd6be5') ]]]

To get Behat to actually call this after every scenario, add an `@AfterScenario` annotation
above the method:

[[[ code('ea7134fbf5') ]]]

That's it!

Let's give this a try:

```bash
vendor/bin/behat features/ls.feature
```

The first time we run this we still get the warning since our
clean up function hasn't been called yet. But when we run it again, the warnings
are gone! And if we run `ls`, we see that there is no test directory. 

We can do this same thing with the `mkdir();` and `chdir();` stuff. Create a new
`public function moveIntoTestDir()`:

[[[ code('d40f499c94') ]]]

And we can make it even a bit more resistant by checking to see if the test directory
is already there and only create it if we need to. Above this, add `@BeforeScenario`:

[[[ code('4d06d42a48') ]]]

This is basically the same as putting the code in `__construct()`, but with some
subtle differences. `@BeforeScenario` is the proper way to do this.

When we run things now, everything looks really nice. I think this `ls` command
is going to be a success!

## PHPUnit Assert Functions

So bonus feature #1 is the hook system. And bonus feature #2, has nothing to do
with Behat at all. It actually comes from PHPUnit. Our first step will be to install
PHPUnit with `composer require phpunit/phpunit --dev`. That will add it under a new
`require-dev` section in `composer.json`:

[[[ code('3a43011aac') ]]]

Full disclosure, I should have put all the Behat and Mink stuff inside of the `require-dev` too:
it is a better place for it since we only need them while we're developing.

I installed PHPUnit because it has really nice assert functions that we can get
a hold of. To get access to them we just need to add a require statement in our
`FeatureContext.php` file, `require_once` then count up a couple of directories and find
`vendor/phpunit/phpunit/src/Framework/Assert/Functions.php`:

[[[ code('16793205d0') ]]]

Requiring this file gives you access to all of PHPUnit's assert functions as flat functions.
Down in the `iShouldSeeInTheOutput()` method, use `assertContains()`, give it the needle
which is `$string` and the haystack which is `$this->output`. Finally, add our helpful message
which I'll just cut and paste. Remove the rest of the original if statement:

[[[ code('4b6504a0f1') ]]]

Run the test again!

```bash
vendor/bin/behat features/ls.feature
```

Beautiful, it looks just like it did before. 

## Using Background

To show you the final important extra for Behat, create another scenario for Linus' `ls` feature.
This time we'll say:

[[[ code('d0242072f1') ]]]

I'll copy all the steps from our first scenario and just edit the second line to:

[[[ code('ed44fd2d56') ]]]

And update the final line to:

[[[ code('956fe0a599') ]]]

Man what a great looking scenario, let's run it!

```bash
vendor/bin/behat features/ls.feature
```

As expected it now says there's one missing step definition. Copy the PHP code that prints
out into `FeatureContext`. Remove the throw exception line, and update the `arg1`'s to `dir`:

[[[ code('94c81b87f3') ]]]

Inside the function use `mkdir($dir)` to actually make that directory:

[[[ code('7342101bd8') ]]]

Simple!

Back to the terminal to rerun the tests. It works! And that was easy. Once you're
done celebrating you may start to notice the duplication we have in the scenarios.
There are two ways to clean this up. The most important way is with `Background:`:

[[[ code('d4b5294e7b') ]]]

If every single scenario in your feature starts with the same lines then you should
move that up into a new `Background` section.

Now, I'll change the first line of `And` in each of these scenarios to `Given`:

[[[ code('07b27a854a') ]]]

I don't have to do this, but it reads better to me. Now Behat will run that
`Background` line before each individual scenario and you'll even see that:

```bash
vendor/bin/behat features/ls.feature
```

The `Background` is read up here, but it actually is running before the top scenario and the
bottom one. We know this because if it didn't, these tests wouldn't be passing. 

Second, when you have duplication that's not on the first line of all of your scenarios
like the  "Then I should see...." you may want to use scenario outlines. It's a little
less commonly used but we'll dive into that a bit later.

Ok, not only do you know how Behat works but you even know all of its top extra
features -- check you out!
