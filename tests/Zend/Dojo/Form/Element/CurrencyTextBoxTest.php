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
 * Test class for Zend_Dojo_Form_Element_CurrencyTextBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_CurrencyTextBoxTest extends PHPUnit\Framework\TestCase
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
        $element = new Zend_Dojo_Form_Element_CurrencyTextBox(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'CurrencyTextBox',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testShouldExtendNumberTextBox()
    {
        $this->assertTrue($this->element instanceof Zend_Dojo_Form_Element_NumberTextBox);
    }

    public function testCurrencyAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getCurrency());
        $this->assertNull($this->element->getDijitParam('currency'));
        $this->element->setCurrency('USD');
        $this->assertEquals('USD', $this->element->getCurrency());
        $this->assertEquals('USD', $this->element->getDijitParam('currency'));
    }

    public function testFractionalAccessorsShouldProxyToConstraints()
    {
        $this->assertFalse($this->element->getFractional());
        $this->assertArrayNotHasKey('constraints', $this->element->dijitParams);
        $this->element->setFractional(true);
        $this->assertTrue($this->element->getFractional());
        $this->assertEquals('true', $this->element->dijitParams['constraints']['fractional']);
    }

    public function testSymbolAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getSymbol());
        $this->assertFalse($this->element->hasConstraint('symbol'));
        $this->element->setSymbol('USD');
        $this->assertEquals('USD', $this->element->getSymbol());
        $this->assertEquals('USD', $this->element->getConstraint('symbol'));
    }

    public function testSymbolMutatorShouldCastToStringAndUppercaseAndLimitTo3Chars()
    {
        $this->element->setSymbol('usdollar');
        $this->assertEquals('USD', $this->element->getSymbol());
        $this->assertEquals('USD', $this->element->getConstraint('symbol'));
    }

    /**
     */
    public function testSymbolMutatorShouldRaiseExceptionWhenFewerThan3CharsProvided()
    {
        $this->expectException(\Zend_Form_Element_Exception::class);

        $this->element->setSymbol('$');
    }

    public function testShouldRenderCurrencyTextBoxDijit()
    {
        $html = $this->element->render();
        $this->assertStringContainsString('dojoType="dijit.form.CurrencyTextBox"', $html);
    }
}
