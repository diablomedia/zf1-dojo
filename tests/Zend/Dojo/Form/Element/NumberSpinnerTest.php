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
 * Test class for Zend_Dojo_Form_Element_NumberSpinner.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_NumberSpinnerTest extends PHPUnit\Framework\TestCase
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
        $element = new Zend_Dojo_Form_Element_NumberSpinner(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'NumberSpinner',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testDefaultTimeoutAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getDefaultTimeout());
        $this->assertArrayNotHasKey('defaultTimeout', $this->element->dijitParams);
        $this->element->setDefaultTimeout(20);
        $this->assertEquals(20, $this->element->getDefaultTimeout());
        $this->assertEquals(20, $this->element->dijitParams['defaultTimeout']);
    }

    public function testTimeoutChangeRateAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getTimeoutChangeRate());
        $this->assertArrayNotHasKey('timeoutChangeRate', $this->element->dijitParams);
        $this->element->setTimeoutChangeRate(20);
        $this->assertEquals(20, $this->element->getTimeoutChangeRate());
        $this->assertEquals(20, $this->element->dijitParams['timeoutChangeRate']);
    }

    public function testLargeDeltaAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getLargeDelta());
        $this->assertArrayNotHasKey('largeDelta', $this->element->dijitParams);
        $this->element->setLargeDelta(20);
        $this->assertEquals(20, $this->element->getLargeDelta());
        $this->assertEquals(20, $this->element->dijitParams['largeDelta']);
    }

    public function testSmallDeltaAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getSmallDelta());
        $this->assertArrayNotHasKey('smallDelta', $this->element->dijitParams);
        $this->element->setSmallDelta(20);
        $this->assertEquals(20, $this->element->getSmallDelta());
        $this->assertEquals(20, $this->element->dijitParams['smallDelta']);
    }

    public function testIntermediateChangesAccessorsShouldProxyToDijitParams()
    {
        $this->assertFalse($this->element->getIntermediateChanges());
        $this->assertArrayNotHasKey('intermediateChanges', $this->element->dijitParams);
        $this->element->setIntermediateChanges(true);
        $this->assertTrue($this->element->getIntermediateChanges());
        $this->assertTrue($this->element->dijitParams['intermediateChanges']);
    }

    public function testRangeMessageAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getRangeMessage());
        $this->assertArrayNotHasKey('rangeMessage', $this->element->dijitParams);
        $this->element->setRangeMessage('foo');
        $this->assertEquals('foo', $this->element->getRangeMessage());
        $this->assertEquals('foo', $this->element->dijitParams['rangeMessage']);
    }

    public function testMinAccessorsShouldProxyToConstraintsDijitParam()
    {
        $this->assertNull($this->element->getMin());
        $this->assertArrayNotHasKey('constraints', $this->element->dijitParams);
        $this->element->setMin(5);
        $this->assertEquals(5, $this->element->getMin());
        $this->assertEquals(5, $this->element->dijitParams['constraints']['min']);
    }

    public function testMaxAccessorsShouldProxyToConstraintsDijitParam()
    {
        $this->assertNull($this->element->getMax());
        $this->assertArrayNotHasKey('constraints', $this->element->dijitParams);
        $this->element->setMax(5);
        $this->assertEquals(5, $this->element->getMax());
        $this->assertEquals(5, $this->element->dijitParams['constraints']['max']);
    }

    public function testShouldRenderNumberSpinnerDijit()
    {
        $html = $this->element->render();
        $this->assertStringContainsString('dojoType="dijit.form.NumberSpinner"', $html);
    }

    /**
     * @group ZF-4638
     */
    public function testRenderingShouldOutputMinAndMaxConstraints()
    {
        $this->element->setMin(5)
                      ->setMax(10);
        $html = $this->element->render();
        // Note that ' is converted to &#39; in Zend_View_Helper_HtmlElement::_htmlAttribs() (line 116)
        $html = str_replace('&#39;', "'", $html);
        $this->assertMatchesRegularExpression('/\'min\':\s*5/', $html, $html);
        $this->assertMatchesRegularExpression('/\'max\':\s*10/', $html, $html);
    }

    public function testSmallAndLargeDeltaCanBeSetAsDecimalValues()
    {
        $this->element->setSmallDelta(20.5);
        $this->assertEquals(20.5, $this->element->getSmallDelta());

        $this->element->setLargeDelta(50.5);
        $this->assertEquals(50.5, $this->element->getLargeDelta());
    }

    public function testMinAndMaxValuesCanBeSetAsDecimalValues()
    {
        $this->element->setMin(20.5);
        $this->assertEquals(20.5, $this->element->getMin());

        $this->element->setMax(50.5);
        $this->assertEquals(50.5, $this->element->getMax());
    }
}
