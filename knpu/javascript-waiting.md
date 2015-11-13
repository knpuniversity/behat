# Master JavaScript with Waits & Debugging

Let's talk JavaScript and the complications it can cause. Right now everything runs
using the Goutte driver and background cURL requests. This means that if your behavior
relies on JavaScript, it'll fail. We need to use Selenium - or Zombie.js - another
supported JavaScript driver - for these scenarios.

I want this "New Product" button to *not* load a whole new page, but instead open up
a modal. I cheated and already did most of the work for this. Inside of `list.html.twig`,
add the class `js-add-new-product` to the "New Product" link:

[[[ code('7da86201e3') ]]]

This triggers some JavaScript that I have at the bottom of the template:

[[[ code('376303e622') ]]]

Next, make the `new.html.twig` template only return a partial page by removing
the extends and Twig block tags:

[[[ code('49f688d49c') ]]]

This will now only be loaded via AJAX. There are cooler ways to make this all work,
but for the purposes of Behat, they all face the same complications that you'll see.

Now click the "New Product" button. It opens up in a modal and it even saves my $34
Foo product. Simple!

Since we just modified our code, we should rerun our tests to make sure that everything
still works. Run the new product scenario:

```bash
./vendor/bin/behat features/product_admin.feature:19
```

It works! Wait... it shouldn't! It relies on JavaScript! In reality, the test clicked
to this URL, went to an ugly, but functional page, filled out the form and hit "Save".
That's kind of ok, it *did* test the form's functionality. But in reality, clicking
the link opens a modal, so that should happen in the test too. Add `@javascript`
to the top of the scenario:

[[[ code('6650e15543') ]]]

That changed bumped our scenario down one line. Put the new line into the terminal
and run it:

```bash
./vendor/bin/behat features/product_admin.feature:20
```

Just make sure that the Selenium server is still running in the background. Watch
closely. Well, don't watch that silly Firefox error. We log in, go to the products
page, clicks the "New Product" button, fill in the form fields, and hit "Save". It's
perfect.

## Waiting for things to Happen

Reality check: if I re-ran this 5 times, I bet it would fail at least once. Let me
show you why. In `ProductAdminController` pretend this isn't such a fast ajax request.
Add a `sleep(1)` to fake the time it would take to do some logic.

Re-run things:

```bash
./vendor/bin/behat features/product_admin.feature:20
```

We log in, click the button... and the browser closes! We didn't see it fill out
any fields. In the terminal, it failed at:

[[[ code('b429c2f663') ]]]

Because the "Name" field wasn't found. What is this madness?

Here's the secret: if you click a link or submit a form and it causes a full page
refresh, Mink and Selenium will wait for that page refresh. But, if you do something
in JavaScript, Selenium does *not* wait. It clicks the "New Products" link and *immediately*
looks for the "Name" field. If that field isn't there almost instantly, it fails.
We have to make Selenium wait after clicking the link.

To make this happen, add a new step like:

[[[ code('8d4dd5c715') ]]]

Run Behat so it'll generate the missing definition. Selenium pops open the browser,
then fails. Copy the new definition into `FeatureContext`:

[[[ code('36336d7361') ]]]

Yes!

### Waiting the Wrong Way

Now, how do we wait for things? Well, there is a right way and a wrong way. Wrong
way first! Add `$this->getSession()->wait(5000);` to wait for 5 seconds:

[[[ code('b96cd1363c') ]]]

That should be overkill since the controller sleeps for just 1 second. Try this out
anyways to see if it passes:

```bash
./vendor/bin/behat features/product_admin.feature:20
```

The test logs us in, clicks the button 1...2...3...4...5, then it finally fills in
the fields. It passed, but took too long. If you litter your test suite with wait
statements like this, your tests will start to crawl. And you know what? You'll just
stop running them, the fences will go down and guests will get eaten by dinosaurs
in your park. Do you want your guests to be eaten? No. I didn't think so. So let's
look at the right way to do this. 

### Waiting the Right Way

The second argument to `wait()` is a JavaScript expression that will run on your page
every 100 milliseconds. As soon as it equates to true, Mink will stop waiting and
move onto the next step. I'm using Bootstrap's modal, and when it opens, an element
with the class `modal` becomes visible. In your browser's console, try running
`$('.modal:visible').length`. Because the modal is open, that returns one. Now close
it: it returns zero. Pass this as the second argument to `wait()`:

[[[ code('e52c14598f') ]]]

This now says: "Wait until this JavaScript expression is true or wait a maximum of
5 seconds." Why am I allowed to use jQuery here? Because this runs on your page and
you have access to any JavaScript loaded by your app.

Run it again:

```bash
./vendor/bin/behat features/product_admin.feature:20
```

This time it starts filling out the fields a lot faster. The most important thing
for testing in JavaScript is mastering proper waits. I see people mess this up by
using the "bad way" all the time.
