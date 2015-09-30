# Behat

The basics of Gherkin, writing features and scenarios, are now behind us.
Now, how does this "Behat" thing fit in?

Imagine we've gone back in time 25 years and Linus Torvalds, the father of
Linux, comes to us and says: "I would love your help in building the ls command,
please." Yep, the ls command right here in the terminal. Since you're an awesome
dev, you reply "I would be happy to help you Linus, and I'll use Gherkin to describe
the feature and the scenarios for the ls command. I'm really into Behavior Driven Development."

Create a new `ls.feature` file, I'll save some time by copying the feature description in since
we've already covered this pretty well. On to the scenario! The first scenario might be:

    Scenario: List 2 files in a directory

And now we'll go through our `Given`, `When`, and `Then` lines. Since we need to list two files
we'll need to have those to start.

    Given there is a file named "john" 
    And there is a file named "hammond"

The user action would be actually running the ls command. 

    When I run "ls"

Finally, we'll actually test that the "john" and "hammond" files both appear in the output. 

    Then I should see "john" in the output 
    And I should see "hammond" in the output

Writing this scenario has two purposes, first, planning how our feature should look which
we've covered in the past chapters. Second, we want the scenario to be executed as a test
to prove whether or not we have successully created this behavior. To do that, we'll run
`./vendor/bin/behat` in our terminal and point it at `features/ls.feature` and let's see
what happens! 

Ahh, so it says our scenario was undefined and something about our feature context having
missing steps. And it even gives us some PHP code. Copy these three functions here, open
up the `FeatureContext.php` class that we generated and paste them there. 

Now we're looking at what Behat is, so what does it do? Simply, it is able to read scenarios
and then looks for a matching function inside of `FeatureContext.php` and calls it. In this
case, it read "There is a file named "john" and finds that it matches this annotation here
and executes its function. 

What's really cool is that because we surrounded john with quotes it recognized that as a wildcard
and inside of here we have `:arg1` which means it matches anything surrounded in quotes or a number.

Our job is just to make these functions do what they say they will do. I'll change this `:filename`
and update this one to `$filename`. In this function, how do we create a file inside of PHP? How
about we use `touch($filename);`. 

For `iRun` update the `arg1`'s to `command`. There are lots of ways to run commands, but we'll use
`shell_exec($command);`.

Lastly, `iShouldSeeInTheOutput`, update the args to `string`. And now we are stuck... we don't have
the return value from `shell_exec()` above. Good news, there is a really nice trick for this, whenever
you need to share data between functions in this file you'll just create a new private property. At
the top of the file let's create a `private property $output;`
