## Chapter 1: install

no challenges

## Chapter 2: 

### Question 1

You've assembled a crack team for a brand new project:
a site where paleontologists and other dinosaur scientists
can send each other important scientific messages, along
with the usual cat photos.

Each scientist will need to register to gain access, and
you - knowing that using BDD will create a better product -
are writing a registration feature. Fill in the 4 feature
lines in `registration.feature`.

ANSWER:

Feature: Registration
  In order to send and receive messages from scientists I know
  As a scientist
  I can register for a new account

## Question 2

For the messaging feature, you and the intern (Bob) are in
a heated debate over how to phrase the business value.
Which if the following is the *best* business value for the
message feature:

A) In order to type messages and click to send them to scientists
B) In order to communicate with other scientists
C) In order to exchange information and resources with other scientists
D) In order to send cat videos

Answer: (C)

Explanation:
Business value is subjective. But to help, forget about the technology
and think about the problem that your user role (scientist) wants to
solve. Both "communicate" and "exchange information and resources"
are pretty good business values, but I like the second, because it
contains a few more details about what the scientists actually
want to do.

## Chapter 3: Scenarios

### Question 1

After writing the feature for registration, the whole team
(even Bob the intern!) is excited about BDD! Let's write the
first scenario for our feature where we register successfully.
After some conversation, you basically want it to work like this:

1) You start on the homepage
2) You click a link called "Register"
3) You fill out an "Email" field and a "Password" field
4) You press a "Register" button
5) You see some text on the page, like "Hi Dr. Paleontology Person! Welcome!"

Add a scenario that represents this, and compare your answer
to ours. The most important thing is that you're using natural
language.

**Answer**

Scenario: Successfully register
  Given I am on "/"
  When I click "Register"
  And I fill in "Email" with "dr_dino@example.com"
  And I fill in "Password" with "r00000ar"
  And I press "Register"
  Then I should see "Hi Dr. Paleontology Person! Welcome!"

### Question 2

Check out the following scenario. What's wrong here?

Scenario: Sending a message
  Given there is a dr_dino@example.com" user in the database
  And I am logged in as "dr_dino@example.com"
  And I am on "/"
  When I click "Send Message"
  And I fill in the message box with "Hello everyone!"
  And I press "Send"
  Then a message from "dr_dino@example.com" should be sent 

A) Actually, this scenario is perfect!
B) In `Given`, you can only do things that the user can do. So,
   you can't magically "add a user to the database"
C) In `Given`, you can't take user action. Saying "I am logged in as"
   implies that the user will go to the login page and login. This
   is user action and so should be under `When`.
D) In `Then`, the user cannot see that a "message should be sent".
   The user would see some sort of a message, like "Your message has been sent"
   and we should look for this.

## Chapter 4 - behat

### Question 1

    Linus just told us about an edge case with the ls
    command: it does *not* show files starting with a `.`
    by default. To test for this, we've added two new
    scenarios that make sure that a `.dino` file only
    shows up with the the `-a` option.

    Execute behat from the command line, copy in the
    new step definition, and then fill in the contents
    so that our scenario passes.

***Starting Files***

```ls.feature
Feature: ls
  In order to see the directory structure
  As a UNIX user
  I need to be able to list the current directory's contents
```

```features/bootstrap/FeatureContext.php
<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

class FeatureContext implements Context, SnippetAcceptingContext
{
    private $output;

    /**
     * @Given I have a file named :filename
     */
    public function iHaveAFileNamed($filename)
    {
        touch($filename);
    }

    /**
     * @When I run :command
     */
    public function iRun($command)
    {
        $this->output = shell_exec($command);
    }

    /**
     * @Then I should see :string in the output
     */
    public function iShouldSeeInTheOutput($string)
    {
        if (strpos($this->output, $string) === false) {
            throw new \Exception(sprintf('Did not see "%s" in output "%s"', $string, $this->output));
        }
    }
}
```

### Question 2

The intern Bob is writing some scenarios that describe
the behavior of the account area of our paleontology app.
Do you see any problems with the second scenario?

```gherkin
  Scenario: Register
    Given I am on "/"
    When I fill in "Email" with "dr_dino@example.com"
    And I fill in "password" with "roar"
    And I press "Register"
    Then I should see "You're registered!"

  Scenario: View my account area
    Given I am on "/login"
    When I fill in "Email" with "dr_dino@example.com"
    And I fill in "password" with "roar"
    And I press "Login"
    And I click "My Account"
    Then I should see "My Account Information"
```

A) The second scenario shouldn't start on `/login`, it
should start on `/` and then you should click a "Login" link

B) Scenario 2 uses the `dr_dino@example.com` user
from scenario 1, but each scenario should act completely independent
of each other.

C) The second scenario shouldn't need to repeat the email
address and password, since it is already in the first scenario.

Correct: (B)

Explanation: Each scenario should act completely independent of other
scenarios. Right now, in order for scenario 2 to pass, you *must* run
scenario 1 first. This makes your scenarios very fragile and difficult
to debug. Instead, the second scenario should make sure that the
`dr_dino@example.com` user is in the database via a Given statement.
We'll talk more about how to do this soon.

## Chapter 5 - hooks-background


### Question 1

As a challenge, create a new function called `afterStepHook()` that
is called after *every* individual step in a scenario. Inside of
this, just execute `var_dump('After Step!');`

**Hint** See http://docs.behat.org/en/v3.0/guides/3.hooks.html
for the annotation needed to do things after every step.

***starting files

```ls.feature
Feature: ls
  In order to see the directory structure
  As a UNIX user
  I need to be able to list the current directory's contents

  Scenario: List 2 files in a directory
    Given I have a file named "john"
    And I have a file named "hammond"
    When I run "ls"
    Then I should see "john" in the output
    And I should see "hammond" in the output
```

```features/bootstrap/FeatureContext.php
<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

class FeatureContext implements Context, SnippetAcceptingContext
{
    private $output;

    /**
     * @Given I have a file named :filename
     */
    public function iHaveAFileNamed($filename)
    {
        touch($filename);
    }

    /**
     * @When I run :command
     */
    public function iRun($command)
    {
        $this->output = shell_exec($command);
    }

    /**
     * @Then I should see :string in the output
     */
    public function iShouldSeeInTheOutput($string)
    {
        if (strpos($this->output, $string) === false) {
            throw new \Exception(sprintf('Did not see "%s" in output "%s"', $string, $this->output));
        }
    }
}
```

### Question 2

Whenever you have a hook function, you're actually passed
an `$event` argument object that contains information about
what's happening inside Behat at this moment. The exact object
depends on which hook you're using (see [Behat Hooks](http://docs.behat.org/en/v3.0/guides/3.hooks.html#hooks)].
In `afterStepHook()`, add an `$event` argument and use it
to figure out if the step that was just executed passed or
failed. Replace `var_dump('After Step')` with `var_dump($isPassed)`
where `$isPassed` is equal to whether or not the previous
step passed/failed.

***Starting Files

* ls.feature - same as previous challenge
* FeatureContext - same as correct answer of previous challenge

## Chapter 6 - mink

TODO
- important objects 1-3
- visiting urls, printing status code, page->getText()

## Chapter 6b - finding elements

TODO
- $page->find('css'...)
- drill down 2 times
- introduce findAll()?
- the named selector
- taking action on an element - like click()
- using the Selenium2Driver
