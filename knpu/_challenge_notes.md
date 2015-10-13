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
