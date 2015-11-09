# Building a Login Scenario

Open up `authentication.feature`, because, you can't do much until you
login. Let's add a scenario:

[[[ code('82516911da') ]]]

We remember from running `behat -dl`:

```bash
./vendor/bin/behat -dl
```

That we have a lot of built in language already. Let's save some effort and describe
the login process using these:

[[[ code('486cd08e9d') ]]]

We want to start on the homepage. PhpStorm tries to help by auto-completing this
step, but when I hit tab, it prints it with extra stuff. When you use the `-dl`
option, the "Given I am on" ends with a bunch of crazy regex. Anytime you see
regex like this, it's just forming a wildcard: something where you can pass any
value, surrounded by quotes.

Oh, and one other thing: even though each line starts with `Given`, `When` and
`Then` in the definition list, that first word doesn't matter. We could actually
say:

    Then I am on "/"

It doesn't sound right in English, but technically, it would still run.

Alright! Given I am on "/". Next, I will click "Login". The built-in definition for
that is "I follow". There's no built-in definition for "I click" but we'll add one
later since that's how most people actually talk.

But for now let's add:

[[[ code('1506ced3d2') ]]]

Remember, these all use the named selector, so we're using "Login" because that's the
text of the link.

On the login page, we need to fill in these two fields. Again, because of the named
selector, we'll target these by the labels "Username" and "Password". There are a few
definitions for fields, byt the one I like is "When I fill in field with value". So:

[[[ code('578445fea9') ]]]

Yes I know, that isn't the right password - this won't work yet. It's cool though.

Finally, press the login button with:

[[[ code('af59a089e0') ]]]

Notice that you *follow* a link but you *press* a button. Then we need to find something
to assert - some sign that this worked. Login on the browser. Hmm, nothing says
"Congratulations, you're in!"... but our login button *did* change to "Logout". Let's
look for that with: 

[[[ code('c88d3499f5') ]]]

Good work team. Let's run this!

```bash
./vendor/bin/behat features/web/authentication.feature
```

## Debugging Failed Scenarios

It runs... and fails:

> The text "Logout" was not found anywhere in the text of the current page.

I *happen* to know this is because the password is wrong. But let's pretend that
we *didn't* know that, and we're staring at this message wondering what the heck
is going on.

Debugging tip number 1: right before the failing step, use a step definition called:

[[[ code('eb92324591') ]]]

Now run this again. It still fails, but first it prints out the entire page's code. Yes,
this *is* ugly - I have another debugging tip later. There are two important things: first,
we're still on the login page for some reason. And second, if you scan down, you'll see
the error message: "invalid credentials".

***TIP
You can also *see* the failed page by using `Then show last response`:

[[[ code('2cae1ecf50') ]]]

You'll just need to configure the `show_cmd` in `behat.yml`. On OSX, I use `show %s`,
but using `firefox %s` is common on other systems:

[[[ code('5f7179f0b0') ]]]

You can even set the `show_auto` setting to `true` to automatically open a browser
on failures.
***

Let's remove our debug line and update this to the correct password which is
"admin". And now let's rerun it:

```bash
./vendor/bin/behat features/web/authentication.feature
```

It's alive!

But we have a *big* problem: We're assuming that there will *always* be an `admin`
user in the database with password `admin`. What if there isn't? What if the intern
deleted that or your dropped your database locally? Ah! Everything would start failing.

We can do better.
