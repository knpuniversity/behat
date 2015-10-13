# Behat

Ok, the basics of Gherkin, writing features and scenarios, are now behind us.
Now, how does this "Behat" thing fit in?

Imagine we've gone back in time 25 years and Linus Torvalds, the Yoda of Linux,
comes to us and says:

> I would love your help in building the `ls` command.

Yep, the `ls` command right here in the terminal. Since you're an awesome
dev, you reply:

> I would be happy to help you Linus, and I'll use Gherkin to describe the feature 
  and the scenarios for the `ls` command. I'm really into Behavior Driven Development.


## Writing Scenario #1

Create a new `ls.feature` file:

[[[ code('e91e223c34') ]]]

I'll save some time by copying the feature description in since we've already covered
this pretty well. On to the scenario! The first scenario might be:

[[[ code('929c83e687') ]]]

And now we'll go through our `Given`, `When`, and `Then` lines. Since we need to list two files
we'll need to create those first:

[[[ code('538c1300dc') ]]]

The user action would be actually running the `ls` command:

[[[ code('016054a5a6') ]]]

Finally, we'll actually test that the "john" and "hammond" files both appear in the output:

[[[ code('87f25330d4') ]]]

Writing this scenario has two purposes. First, it lets us plan how our feature should behave.
And that's what we've been talking about. Second, we want the scenario to be executed as a test
to prove whether or not we have successfully created this behavior. To do that, we'll run
`./vendor/bin/behat` in our terminal and point it at `features/ls.feature` and let's see
what happens! 

## The Essence of Behat: Matching Scenario lines with Functions

Ahh, so it says our scenario was undefined and something about our `FeatureContext` having
missing steps. And it even gives us some PHP code. Copy these three functions, open
up the `FeatureContext` class that we generated and paste them there:

[[[ code('3e45a3abfc') ]]]

So what does Behat really do? Simply, it reads each line of our scenario, looks for a matching
function inside of `FeatureContext` and calls it. In this case, it'll read
"There is a file named "john", find that it matches this annotation here
and then execute its function.

What's really cool is that because we surrounded john with quotes, it recognized that as a wildcard.
In the annotation, we have `:arg1` which means it matches anything surrounded in quotes or a number.

## Filling in the Definitions/Functions

Our job is just to make these functions do what they say they will do. I'll change this to `:filename`
and update the argument to `$filename`, just because that's more descriptive:

[[[ code('b77292aaf2') ]]]

In this function, how do we create a file? How about we use `touch($filename);`:

[[[ code('4da5e241a1') ]]]

For `iRun()` update the `arg1`'s to `command`. There are lots of ways to run commands, but we'll use
`shell_exec($command);`:

[[[ code('3d2ef00de4') ]]]

## Sharing Data inside your Scenario (between "Steps")

Lastly, in `iShouldSeeInTheOutput()`, update the `arg1` to `string`:

[[[ code('ee77e46396') ]]]

And now we're stuck... we don't have the return value from `shell_exec()` above. Good news,
there is a really nice trick for this. Whenever you need to share data between functions
in `FeatureContext`, you'll just create a new private property. At the top of the class
let's create a `private $output` property and update the `iRun` function to
`$this->output = shell_exec($command);`:

[[[ code('a17e5dd541') ]]]

Behat doesn't care about the new property: this is just us being good object oriented programmers
and sharing things inside a class.

### Lifetime of a Scenario

This works because every scenario gets its own `FeatureContext` object. When we have
more scenarios later, Behat will instantiate a fresh `FeatureContext` object before
calling each one. So we can set any private properties that we want on top, and
only just this scenario will have access to it. 

## Failing!

Now in `iShouldSeeInTheOutput()` method `if (strpos($this->output, $string) === false))` then
we have a problem and we want this step to fail:

[[[ code('a80e33a3c1') ]]]

How do you fail in Behat? By throwing an exception:  `throw new \Exception()` and print 
a really nice message here of "Did not see '%s' in the output '%s'. Finish that line up
with `$string, $this->output`:

[[[ code('a59164d5ae') ]]]

Ok let's give this a try!

Re-run our last Behat command in the terminal, and this time it's green! And if you run
the `ls` command here you can see the "john" and "hammond" files listed. 

## What are Definitions and Steps?

Back to our scenario. Get out some pen and paper: we need to review some terminology!
Every line in here is called a "step":

[[[ code('f10dd3d336') ]]]

And the function that a step connects to is called a "step definition":

[[[ code('69731e3a07') ]]]

This is important in helping you understand Behat's documentation.

Oh, and the 4 feature lines above: those aren't parsed by Behat:

[[[ code('ac2992dc59') ]]]

We only write those to go through the exercise of thinking about our business value.

Now, you see in our test it says "5 steps (5 passed)", which means that each step could
fail. The rule is really simple, if the definition function for a step throws an exception,
it's a failure. If there's no exception it passes.

Head back to the step that looks for the "hammond" file and add the number 2 to the
end of the file name so it fails. Running the scenario in our terminal now shows us 4
steps passed and 1 failed. 
