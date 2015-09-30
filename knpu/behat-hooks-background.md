# Behat Hooks Background

Watch out Behat experts coming through here! Seriously, we've covered it, Behat
reads the steps, finds a function using this nice little pattern, calls the 
function, and then goes out to lunch. That's all that Behat does, any other
features are just extras.

Let's dive into some of those extras! This extra creates the "john" and "hammond"
files inside this directory and it doesn't even clean them up. What a terrible roommate.

Let's have this go into sandboxed directories, we'll use the construct function since
that is called before the scenario. Type `mkdir('test');` and `chdir('test');`. 

Over in the terminal I'll delete the "john" and "hammond" files so we can have a fresh
start at this. Rerun Behat for our ls scenario, everything still passes and hey look
there's a little test directory and "john" and "hammond" are inside of that. Cool, no 
expenses spared here.

Ready for the problem? Rerun that test one more time, and errors are starting to appear
that say "mkdir(): file exists". This error didn't break our test but it does highlight
the problem that we don't have any cleanup. After our tests run these files stick around.

We need to run some code after every scenario. Behat has a system called "hooks" where 
you can make a function inside of here called "hooks" which allows you to have a function
that is called before or after your scenario, entire test suite or individual steps.

Create a new public function inside of here called `public function moveOutOfTestDir()`.
This will be our cleanup function. Inside there use `chdir();` to go up one directory,
`if (is_dir('test'))` exists, which it should, then we'll run a nice little command to
remove that. Now we need to get Behat to actually call this after every scenario, which 
we can do by adding `@AfterScenario`, that's it! 

Let's give this a try, first time we run this we should get the warning since our clean
up function hasn't been called yet to clean up the files. When we run it again there
are no warnings! And if we run ls we see that there is no test directory. 

We can do this same thing with the `mkdir();` and `chdir();`. Create a new
`public function moveIntoTestDir` and we can make it even a bit more resistant by checking
to see if the test directory is even there and only create it if we need to. Above this
add `@beforeScenario`. This is basically the same as `construct` but with some subtle differences.
`@beforeScenario` is the proper place to put it. 

When we run things now, everything looks really nice, this ls thing is going to be a success!

Bonus feature #1, the hook system. Bonus feature #2 has nothing to do with Behat at all, it actually
has to do with PHPUnit. Our first step will be to install PHPUnit with `composer require phpunit/phpunit --dev`
that will add itself under a new require-dev section here. 

Full disclosure, I should have done all the Behat and Mink stuff inside of the require-dev, it is
a better place for it since we only need them while we're developing.

I installed PHPUnit because it has really nice assert functions that we can get ahold of. To get access
to them we just need to add a require statement in our `FeatureContext.php` file, `require_once` then
count up a couple of directories and find `vendor/phpunit/phpunit/src/Framework/Assert/Functions.php`.
Requiring this file gives you access to all of PHPUnit's assert functions as a flat function. Down here
we'll say `assertContains()` and give it the needle which is the `$string`, the haystack which is
`$this->output` and finally our helpful message which I'll just cut and paste. And remove the rest of this
if statement here. 

Again, run the test! Beautiful, it looks just like it did before. 

To show you the final important extra for Behat create another scenario for Linus' ls feature. This time
we'll say:

    Scenario: List 1 file and 1 directory

Since ls should be able to do both. I'll copy all the steps from our first scenario and just edit the second
line to:

    And there is a dir named "ingen"

And update the final line to:

    And I should see "ingen" in the output

Man what a great looking scenario, let's run it! As expected it now says there's one missing step definition. 
Just like before copy the helpful PHP code that prints out into `FeatureContext`. Remove the throw exception line,
and update the `arg1`'s to `dir`. Inside the function add `mkdir($dir)` to actually make that directory. Simple!

Back to the terminal to rerun the tests. It works, isn't that nice?! Once you're done celebrating you may start
to notice the duplication we have in here. There are two ways to clean this up. The most important way is with
`Background:` If every single scenario in your feature starts with the same line then you should move that up
to the `Background`. 

Now, I'll change the first line of `And` in each of these scenarios to `Given`. I don't have to do this, but
it reads much clearer. Now Behat will run that `Background` line before each individual scenario and you'll
even see that. The `Background` is read up here, but it actually is running before this scenario and this
scenario. We know this because if it didn't these tests wouldn't be passing. 

Second, when you have duplication that's not on the first line of all of your scenarios, like the 
"Then I should see...." you'll be using the scenario outline feature. It's a little less commonly used and
we'll dive into that a bit later. Just note that it is possible.

Not only do you know how Behat works but you even know all of its top extra features -- check you out!


