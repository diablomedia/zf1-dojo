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
 * Test class for Zend_Dojo_Form_Decorator_DijitElement.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Decorator_DijitElementTest extends PHPUnit\Framework\TestCase
{
    protected $view;
    protected $decorator;
    protected $element;
    protected $errors;

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

        $this->errors    = array();
        $this->view      = $this->getView();
        $this->decorator = new Zend_Dojo_Form_Decorator_DijitElement();
        $this->element   = $this->getElement();
        $this->element->setView($this->view);
        $this->decorator->setElement($this->element);
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
        $element = new Zend_Dojo_Form_Element_TextBox(
            'foo',
            array(
                'value'      => 'some text',
                'label'      => 'TextBox',
                'trim'       => true,
                'propercase' => true,
                'class'      => 'someclass',
                'style'      => 'width: 100px;',
            )
        );
        return $element;
    }

    /**
     * Handle an error (for testing notices)
     *
     * @param  int $errno
     * @param  string $errstr
     * @return void
     */
    public function handleError($errno, $errstr)
    {
        $this->errors[] = $errstr;
    }

    public function testRetrievingElementAttributesShouldOmitDijitParams()
    {
        $attribs = $this->decorator->getElementAttribs();
        $this->assertIsArray($attribs);
        $this->assertArrayNotHasKey('dijitParams', $attribs);
        $this->assertArrayNotHasKey('propercase', $attribs);
        $this->assertArrayNotHasKey('trim', $attribs);
    }

    public function testRetrievingDijitParamsShouldOmitNormalAttributes()
    {
        $params = $this->decorator->getDijitParams();
        $this->assertIsArray($params);
        $this->assertArrayNotHasKey('class', $params);
        $this->assertArrayNotHasKey('style', $params);
        $this->assertArrayNotHasKey('value', $params);
        $this->assertArrayNotHasKey('label', $params);
    }

    public function testRenderingShouldEnableDojo()
    {
        $html = $this->decorator->render('');
        $this->assertTrue($this->view->dojo()->isEnabled());
    }

    public function testRenderingShouldTriggerErrorWhenDuplicateDijitDetected()
    {
        $this->view->dojo()->addDijit('foo', array('dojoType' => 'dijit.form.TextBox'));

        $handler = set_error_handler(array($this, 'handleError'));
        $html    = $this->decorator->render('');
        restore_error_handler();
        $this->assertNotEmpty($this->errors, var_export($this->errors, 1));
        $found = false;
        foreach ($this->errors as $error) {
            if (strstr($error, 'Duplicate')) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testShouldAllowAddingAndRetrievingIndividualDijitParams()
    {
        $this->assertNull($this->decorator->getDijitParam('bogus'));
        $this->decorator->setDijitParam('bogus', 'value');
        $this->assertEquals('value', $this->decorator->getDijitParam('bogus'));
    }

    /**
     */
    public function testRenderingShouldThrowExceptionWhenNoViewObjectRegistered()
    {
        $this->expectException(\Zend_Form_Decorator_Exception::class);

        $element = new Zend_Dojo_Form_Element_TextBox(
            'foo',
            array(
                'value'      => 'some text',
                'label'      => 'TextBox',
                'trim'       => true,
                'propercase' => true,
                'class'      => 'someclass',
                'style'      => 'width: 100px;',
            )
        );
        $this->decorator->setElement($element);
        $html = $this->decorator->render('');
    }

    public function testRenderingShouldCreateDijit()
    {
        $html = $this->decorator->render('');
        $this->assertStringContainsString('dojoType="dijit.form.TextBox"', $html);
    }

    public function testRenderingShouldSetRequiredDijitParamWhenElementIsRequired()
    {
        $this->element->setRequired(true);
        $html = $this->decorator->render('');
        $this->assertStringContainsString('required="', $html);
    }

    /**
     * @group ZF-7660
     */
    public function testRenderingShouldRenderRequiredFlagAlways()
    {
        $this->element->setRequired(false);
        $html = $this->decorator->render('');
        $this->assertStringContainsString('required="false"', $html, $html);
    }
}
