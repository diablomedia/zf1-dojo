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
 * Test class for Zend_Dojo_View_Helper_CustomDijit
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_CustomDijitTest extends PHPUnit\Framework\TestCase
{
    protected $view;
    protected $helper;

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

        $this->view = $this->getView();
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

    /**
     */
    public function testHelperShouldRaiseExceptionIfNoDojoTypePassed()
    {
        $this->expectException(\Zend_Dojo_View_Exception::class);

        $this->view->customDijit('foo');
    }

    public function testHelperInDeclarativeModeShouldGenerateDivWithPassedDojoType()
    {
        $content = $this->view->customDijit('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $this->assertStringContainsString('dojoType="custom.Dijit"', $content);
    }

    public function testHelperInDeclarativeModeShouldRegisterDojoTypeAsModule()
    {
        $content = $this->view->customDijit('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $dojo    = $this->view->dojo();
        $modules = $dojo->getModules();
        $this->assertContains('custom.Dijit', $modules);
    }

    public function testHelperInProgrammaticModeShouldRegisterDojoTypeAsModule()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $content = $this->view->customDijit('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $dojo    = $this->view->dojo();
        $modules = $dojo->getModules();
        $this->assertContains('custom.Dijit', $modules);
    }

    public function testHelperInProgrammaticModeShouldGenerateDivWithoutPassedDojoType()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $content = $this->view->customDijit('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $this->assertStringNotContainsString('dojoType="custom.Dijit"', $content);
    }

    public function testHelperShouldAllowCapturingContent()
    {
        $this->view->customDijit()->captureStart('foo', array('dojoType' => 'custom.Dijit'));
        echo "Captured content started\n";
        $content = $this->view->customDijit()->captureEnd('foo');
        $this->assertStringContainsString(">Captured content started\n<", $content);
    }

    public function testUsesDefaultDojoTypeWhenPresent()
    {
        $helper = new Zend_Dojo_View_Helper_CustomDijitTest_FooContentPane();
        $helper->setView($this->view);
        $content = $helper->fooContentPane('foo');
        $this->assertStringContainsString('dojoType="foo.ContentPane"', $content);
    }

    public function testCapturingUsesDefaultDojoTypeWhenPresent()
    {
        $helper = new Zend_Dojo_View_Helper_CustomDijitTest_FooContentPane();
        $helper->setView($this->view);
        $helper->fooContentPane()->captureStart('foo');
        echo "Captured content started\n";
        $content = $helper->fooContentPane()->captureEnd('foo');
        $this->assertStringContainsString(">Captured content started\n<", $content);
        $this->assertStringContainsString('dojoType="foo.ContentPane"', $content);
    }

    /**
     * @group ZF-7890
     */
    public function testHelperShouldAllowSpecifyingRootNode()
    {
        $content = $this->view->customDijit('foo', 'content', array(
            'dojoType' => 'custom.Dijit',
            'rootNode' => 'select',
        ));
        $this->assertStringContainsString('<select', $content);
    }
}

class Zend_Dojo_View_Helper_CustomDijitTest_FooContentPane extends Zend_Dojo_View_Helper_CustomDijit
{
    protected $_defaultDojoType = 'foo.ContentPane';

    public function fooContentPane($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        return $this->customDijit($id, $value, $params, $attribs);
    }
}
