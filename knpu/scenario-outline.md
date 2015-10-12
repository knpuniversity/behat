# Scenario Outline

Add a second scenario to search: searching for a product that is *not* found.
I'm lazy, so I'll copy the first scenario.

   Scenario: Searching for a product that does not exist

The `Given` is the same in both, so we can move that to a `Background`.
It *is* ok not to have any `Given`'s in the scenario and start directly with
`When`. I'll change the search term to "xbox" - none of those in the dino store.
This is really going to bum out any dino's that are looking to play Ghost Recon.

When there are no matches - the site says "No Products Found". Cool, let's plug
that into our `Then` line. Run this:

We didn't use any new language, so everything runs *and* passes!

## Scenario Outlines

To remove duplication, we can take things a step further with a Scenario Outline.
It looks like this. Copy one of the scenarios and change it to start with
`Scenario Outline:`. The search term is different in the scenarios, so
change it to `<term>`.

For the "should see" part, replace that with `<result>`. Now, we have two
variable parts. To fill those in, add an `Examples` section at the end with
a little table that has `term` and `result` columns. For the first row, use
`Samsung` for the `term` and `Samsung Galaxy` for the `result`. The second
row should be `Xbox` and the `No products found` message.

Now we remove both of the scenarios -- crazy right? You'll see this [table format](table)
again later. The table doesn't need to be pretty, but I like to reformat it with command+option+L.

Time to try this out.

Perfect! It only prints out the Scenario Outline once. But below that, Behat prints
both examples in green as it executes each of them. Senario Outlines are the best
way to reduce duplication that goes past just the first `Given` line.
