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
