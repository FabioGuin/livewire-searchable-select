<?php

namespace FabioGuin\LivewireSearchableSelect\Tests\Feature;

use FabioGuin\LivewireSearchableSelect\Tests\TestCase;

class SelectSearchableInputMountTest extends TestCase
{
    /** @test */
    public function testMountSetsInputRelatedProperties()
    {
        $property = 'testProperty';
        $inputPlaceholder = 'testPlaceholder';
        $inputExtraClasses = 'testClasses';

        $this->component->mount(
            $property,
            [],
            '',
            '',
            0,
            $inputPlaceholder,
            '',
            null,
            null,
            null,
            $inputExtraClasses
        );

        $this->assertEquals($property, $this->component->property);
        $this->assertEquals($inputPlaceholder, $this->component->inputPlaceholder);
        $this->assertEquals($inputExtraClasses, $this->component->inputExtraClasses);
    }

    public function testMountSetsSearchRelatedProperties()
    {
        $searchMinChars = 5;
        $searchLimitResults = 20;
        $searchColumns = ['column1', 'column2'];

        $this->component->mount(
            '',
            $searchColumns,
            '',
            '',
            $searchMinChars,
            null,
            '',
            $searchLimitResults
        );

        $this->assertEquals($searchMinChars, $this->component->searchMinChars);
        $this->assertEquals($searchLimitResults, $this->component->searchLimitResults);
        $this->assertEquals($searchColumns, $this->component->searchColumns);
    }

    public function testMountSetsDataProperties()
    {
        $optionText = 'testOptionText';
        $optionValueColumn = 'testOptionValueColumn';
        $modelApp = 'testModelApp';

        $this->component->mount(
            '',
            [],
            $optionText,
            $optionValueColumn,
            0,
            null,
            $modelApp
        );

        $this->assertEquals($optionText, $this->component->optionText);
        $this->assertEquals($optionValueColumn, $this->component->optionValueColumn);
        $this->assertEquals($modelApp, $this->component->modelApp);
    }

    public function testMountSetsActiveValue()
    {
        $activeOptionText = 'testActiveOptionText';
        $activeOptionValue = 'testActiveOptionValue';

        $this->component->mount(
            '',
            [],
            '',
            '',
            0,
            null,
            '',
            null,
            $activeOptionText,
            $activeOptionValue
        );

        $this->assertEquals($activeOptionText, $this->component->activeOptionText);
        $this->assertEquals($activeOptionValue, $this->component->activeOptionValue);

        $this->component->getValueOption($activeOptionValue, $activeOptionText);

        $this->assertEquals($activeOptionText, $this->component->searchTherm);
        $this->assertTrue($this->component->isSelected);
        $this->assertNull($this->component->message);
    }
}
