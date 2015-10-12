<?php

namespace Challenges\BDDFeatures;

use KnpU\ActivityRunner\Activity\CodingChallenge\CodingContext;
use KnpU\ActivityRunner\Activity\CodingChallenge\CorrectAnswer;
use KnpU\ActivityRunner\Activity\CodingChallengeInterface;
use KnpU\ActivityRunner\Activity\CodingChallenge\CodingExecutionResult;
use KnpU\ActivityRunner\Activity\CodingChallenge\FileBuilder;
use KnpU\ActivityRunner\Activity\Exception\GradingException;

class RegistrationFeatureCoding implements CodingChallengeInterface
{
    /**
     * @return string
     */
    public function getQuestion()
    {
        return <<<EOF
You've assembled a crack team for a brand new project:
a site where paleontologists and other dinosaur scientists
can send each other important scientific messages, along
with the usual cat photos.

Each scientist will need to register to gain access, and
you - knowing that using BDD will create a better product -
are writing a registration feature. Fill in the 4 feature
lines in `registration.feature`.
EOF;
    }

    public function getFileBuilder()
    {
        $fileBuilder = new FileBuilder();
        $fileBuilder
            ->addFileContents('registration.feature', <<<EOF
EOF
            )
            ->setEntryPointFilename('registration.feature')
        ;

        return $fileBuilder;
    }

    public function getExecutionMode()
    {
        return self::EXECUTION_MODE_GHERKIN;
    }

    public function setupContext(CodingContext $context)
    {
    }

    public function grade(CodingExecutionResult $result)
    {
        $input = $result->getInputFileContents('registration.feature');

        $lines = explode("\n", $input);

        $this->assertLineStartsWith(
            $lines,
            'Feature:',
            'a short feature description',
            0,
            false
        );

        $this->assertLineStartsWith(
            $lines,
            'In order to',
            'your *business* value',
            1,
            true
        );

        $this->assertLineStartsWith(
            $lines,
            'As a',
            '*who* will benefit from the feature',
            2,
            true
        );

        $this->assertLineStartsWith(
            $lines,
            'I need to be able to',
            'a short description of what the user will do with the feature',
            3,
            true
        );
    }

    private function assertLineStartsWith(array $lines, $expectedText, $descriptionOfLine, $lineNumber, $requiresSpaces)
    {
        if (!isset($lines[$lineNumber])) {
            throw new GradingException(sprintf(
                'I don\'t see line #%s - the one  with `%s`',
                $lineNumber+1,
                $expectedText
            ));
        }
        $line = trim($lines[$lineNumber]);
        // check that the line starts with this AND there is something after this text
        if (strpos($line, $expectedText) !== 0 || $line == $expectedText) {
            throw new GradingException(sprintf(
                'Be sure your line #%s starts with `%s` followed by %s.',
                $lineNumber+1,
                $expectedText,
                $descriptionOfLine
            ));
        }

        if ($requiresSpaces) {
            if (substr($lines[$lineNumber], 0, 2) !== '  ') {
                throw new GradingException('Each line *under* `Feature` should be indented at least two spaces. This just for readability');
            }
        }
    }

    public function configureCorrectAnswer(CorrectAnswer $correctAnswer)
    {
        $correctAnswer
            ->setFileContents('registration.feature', <<<EOF
Feature: Registration
  In order to send and receive messages from scientists I know
  As a scientist
  I can register for a new account
EOF
            )
        ;
    }
}
