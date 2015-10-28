# Scenario Outline

Add a second scenario to search: searching for a product that is *not* found.
I'm lazy, so I'll copy the first scenario.

[[[ code('cdee53aaba') ]]]

The `Given` is the same in both, so we can move that to a `Background`:

[[[ code('9a4224cf43') ]]]

It *is* ok not to have any `Given`'s in the scenario and start directly with
`When`. I'll change the search term to "XBox" - none of those in the dino store.
This is really going to bum out any dino's that are looking to play Ghost Recon.

When there are no matches - the site says "No Products Found". Cool, let's plug
that into our `Then` line:

[[[ code('721e4a5509') ]]]

Run this:

```bash
./vendor/bin/behat
```

We didn't use any new language, so everything runs *and* passes!

## Scenario Outlines

To remove duplication, we can take things a step further with a Scenario Outline.
It looks like this. Copy one of the scenarios and change it to start with
`Scenario Outline:`.

The search term is different in the scenarios, so change it to `<term>`.
For the "should see" part, replace that with `<result>`:

[[[ code('2a2f36a437') ]]]

Now, we have two variable parts. To fill those in, add an `Examples` section at the end
with a little table that has `term` and `result` columns. For the first row, use `Samsung`
for the `term` and `Samsung Galaxy` for the `result`. The second row should be `XBox`
and the `No products found` message:

[[[ code('7ed8e7bb11') ]]]

Now we remove both of the scenarios -- crazy right? You'll see this 
[table format](http://docs.behat.org/en/v3.0/guides/1.gherkin.html#tables)
again later. The table doesn't need to be pretty, but I like to reformat it
with `cmd + option + L`.

Time to try this out.

Perfect! It only prints out the Scenario Outline once. But below that, Behat prints
both examples in green as it executes each of them. Senario Outlines are the best
way to reduce duplication that goes past just the first `Given` line.
