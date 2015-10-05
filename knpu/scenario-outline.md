# Scenario Outline

Add a second scenario to our file here, this will test searching for a product
that is not found. Let's start by copying and pasting the one we've already created.

   Scenario: Searching for a product that does not exist

We will have the same `Given` in each of these scenarios so let's move those to a
`Background`. It's ok not to have any `Given`'s in the scenario and start with
`When` instead. Change the search term in the `When` line in our new scenario to
"xbox". 
