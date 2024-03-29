<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test class for Zend_Dojo_Form_Element_RadioButton.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_RadioButtonTest extends PHPUnit\Framework\TestCase
{
    protected $view;
    protected $element;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp(): void
    {
        Zend_Registry::_unsetInstance();
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative();

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown(): void
    {
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getElement()
    {
        $element = new Zend_Dojo_Form_Element_RadioButton(
            'foo',
            array(
                'value'        => 'bar',
                'label'        => 'RadioButton',
                'class'        => 'someclass',
                'style'        => 'width: 100px;',
                'multiOptions' => array(
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                    'baz' => 'Baz',
                ),
            )
        );
        return $element;
    }

    public function testShouldAllowSpecifyingSeparatorText()
    {
        $this->element->setSeparator('<br />');
        $this->assertEquals('<br />', $this->element->getSeparator());
    }

    public function testAddingAnOptionShouldResetOptionsToArrayIfScalar()
    {
        $this->element->options = 'foo';
        $this->element->addMultiOption('bar', 'baz');
        $this->assertIsArray($this->element->options);
    }

    public function testAddMultiOptionsShouldPassKeyValueArraysAsIndividualOptions()
    {
        $this->element->addMultiOptions(array(
            array('key' => 'foo', 'value' => 'bar'),
            array('key' => 'bar', 'value' => 'baz'),
        ));
        $this->assertEquals('bar', $this->element->getMultiOption('foo'));
        $this->assertEquals('baz', $this->element->getMultiOption('bar'));
    }

    public function testShouldAllowRemovingIndividualOptions()
    {
        $this->element->removeMultiOption('bar');
        $this->assertNull($this->element->getMultiOption('bar'));
    }

    public function testOptionsShouldBeTranslatable()
    {
        $translations = array(
            'Foo' => 'This is Foo',
            'Bar' => 'This is Bar',
            'Baz' => 'This is Baz',
        );
        $translate = new Zend_Translate('array', $translations, 'en');
        $this->element->setTranslator($translate);

        $this->element->setMultiOptions(array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ));

        $html = $this->element->render();
        foreach ($translations as $string) {
            $this->assertStringContainsString($string, $html, $html);
        }
    }

    public function testShouldRenderRadioButtonDijit()
    {
        $html = $this->element->render();
        $this->assertStringContainsString('dojoType="dijit.form.RadioButton"', $html);
    }

    public function testPassingValueShouldMarkThatValueCheckedWhenRendering()
    {
        $html = $this->element->render();
        if (!preg_match('/(<input[^>]*(id="foo-bar")[^>]*>)/', $html, $matches)) {
            $this->fail('Did not find radio option matching bar');
        }
        $this->assertStringContainsString('checked="checked"', $matches[1]);
    }

    /**#+
     * @group ZF-3286
     */
    public function testShouldRegisterInArrayValidatorByDefault()
    {
        $this->assertTrue($this->element->registerInArrayValidator());
    }

    public function testShouldAllowSpecifyingWhetherOrNotToUseInArrayValidator()
    {
        $this->testShouldRegisterInArrayValidatorByDefault();
        $this->element->setRegisterInArrayValidator(false);
        $this->assertFalse($this->element->registerInArrayValidator());
        $this->element->setRegisterInArrayValidator(true);
        $this->assertTrue($this->element->registerInArrayValidator());
    }

    public function testInArrayValidatorShouldBeRegisteredAfterValidation()
    {
        $options = array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        );
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->element->isValid('test');
        $validator = $this->element->getValidator('InArray');
        $this->assertInstanceOf(Zend_Validate_InArray::class, $validator);
    }

    public function testShouldNotValidateIfValueIsNotInArray()
    {
        $options = array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        );
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->assertFalse($this->element->isValid('test'));
    }
    /**#@-*/
}
