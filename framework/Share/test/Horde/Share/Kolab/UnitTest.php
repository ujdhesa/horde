<?php
/**
 * Unit testing for the Kolab driver.
 *
 * PHP version 5
 *
 * @category   Horde
 * @package    Share
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link       http://pear.horde.org/index.php?package=Share
 */

/**
 * Prepare the test setup.
 */
require_once dirname(__FILE__) . '/../Autoload.php';

/**
 * Unit testing for the Kolab driver.
 *
 * Copyright 2011 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @category   Horde
 * @package    Share
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link       http://pear.horde.org/index.php?package=Share
 */
class Horde_Share_Kolab_UnitTest
extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!interface_exists('Horde_Kolab_Storage')) {
            $this->markTestSkipped('The Kolab_Storage package seems to be unavailable.');
        }
    }

    public function testGetStorage()
    {
        $storage = $this->getMock('Horde_Kolab_Storage_List');
        $driver = $this->_getDriver();
        $driver->setStorage($storage);
        $this->assertSame($storage, $driver->getStorage());
    }

    /**
     * @expectedException Horde_Share_Exception
     */
    public function testStorageMissing()
    {
        $driver = $this->_getDriver();
        $driver->getStorage();
    }

    public function testListArray()
    {
        $this->assertInternalType(
            'array',
            $this->_getCompleteDriver()->listShares('john')
        );
    }

    public function testGetTypeString()
    {
        $driver = new Horde_Share_Kolab(
            'mnemo', 'john', new Horde_Perms(), new Horde_Group_Test()
        );        
        $this->assertInternalType('string', $driver->getType());
    }

    public function testMnemoSupport()
    {
        $driver = new Horde_Share_Kolab(
            'mnemo', 'john', new Horde_Perms(), new Horde_Group_Test()
        );        
        $this->assertEquals('note', $driver->getType());
    }

    public function testKronolithSupport()
    {
        $driver = new Horde_Share_Kolab(
            'kronolith', 'john', new Horde_Perms(), new Horde_Group_Test()
        );        
        $this->assertEquals('event', $driver->getType());
    }

    public function testTurbaSupport()
    {
        $driver = new Horde_Share_Kolab(
            'turba', 'john', new Horde_Perms(), new Horde_Group_Test()
        );        
        $this->assertEquals('contact', $driver->getType());
    }

    public function testNagSupport()
    {
        $driver = new Horde_Share_Kolab(
            'nag', 'john', new Horde_Perms(), new Horde_Group_Test()
        );        
        $this->assertEquals('task', $driver->getType());
    }

    /**
     * @expectedException Horde_Share_Exception
     */
    public function testSupportException()
    {
        $driver = new Horde_Share_Kolab(
            'NOTSUPPORTED', 'john', new Horde_Perms(), new Horde_Group_Test()
        );        
    }

    public function testListIds()
    {
        $this->assertEquals(
            array('INBOX%2FCalendar'),
            array_keys(
                $this->_getPrefilledDriver()->listShares('john')
            )
        );
    }

    /**
     * @expectedException Horde_Share_Exception
     */
    public function testUndefinedId()
    {
        $object = new Horde_Share_Object_Kolab(null, new Horde_Group_Mock());
        $object->getId();
    }

    /**
     * @expectedException Horde_Share_Exception
     */
    public function testUndefinedName()
    {
        $object = new Horde_Share_Object_Kolab(null, new Horde_Group_Mock());
        $object->getName();
    }

    public function testUndefinedPermissionId()
    {
        $object = new Horde_Share_Object_Kolab(null, new Horde_Group_Mock());
        $this->assertType(
            'string',
            $object->getPermissionId()
        );
    }

    public function testIdFromName()
    {
        $share = $this->_getCompleteDriver();
        $object = $share->newShare('john', 'IGNORED');
        $object->set('name', 'test');
        $this->assertEquals('INBOX%2Ftest', $object->getId());
    }

    public function testObjectId()
    {
        $object = new Horde_Share_Object_Kolab('test', new Horde_Group_Mock());
        $this->assertEquals('test', $object->getId());
    }

    public function testObjectName()
    {
        $object = new Horde_Share_Object_Kolab('test', new Horde_Group_Mock());
        $this->assertEquals('test', $object->getName());
    }

    public function testGetShare()
    {
        $this->assertEquals(
            'INBOX%2FCalendar',
            $this->_getPrefilledDriver()->getShare('internal_id')->getId()
        );
    }

    public function testExistsById()
    {
        $this->assertTrue(
            $this->_getPrefilledDriver()->exists('INBOX%2FCalendar')
        );
    }

    public function testExistsByName()
    {
        $this->assertTrue(
            $this->_getPrefilledDriver()->exists('internal_id')
        );
    }

    public function testDoesNotExists()
    {
        $this->assertFalse(
            $this->_getPrefilledDriver()->exists('DOES_NOT_EXIST')
        );
    }

    public function testIdExists()
    {
        $this->assertTrue(
            $this->_getPrefilledDriver()->idExists('INBOX%2FCalendar')
        );
    }

    public function testIdDoesNotExists()
    {
        $this->assertFalse(
            $this->_getPrefilledDriver()->idExists('DOES_NOT_EXIST')
        );
    }

    public function testGetShareById()
    {
        $this->assertEquals(
            'INBOX%2FCalendar',
            $this->_getPrefilledDriver()
            ->getShareById('INBOX%2FCalendar')
            ->getId()
        );
    }

    /**
     * @expectedException Horde_Exception_NotFound
     */
    public function testMissingShare()
    {
        $this->_getPrefilledDriver()->getShareById('DOES_NOT_EXIST');
    }

    public function testShareOwner()
    {
        $this->assertEquals(
            'john',
            $this->_getPrefilledDriver()
            ->getShareById('INBOX%2FCalendar')
            ->get('owner')
        );
    }

    public function testShareName()
    {
        $this->assertEquals(
            'Calendar',
            $this->_getPrefilledDriver()
            ->getShareById('INBOX%2FCalendar')
            ->get('name')
        );
    }

    public function testShareDescription()
    {
        $this->assertEquals(
            'DESCRIPTION',
            $this->_getPrefilledDriver()
            ->getShareById('INBOX%2FCalendar')
            ->get('desc')
        );
    }

    public function testShareShareName()
    {
        $this->assertEquals(
            'internal_id',
            $this->_getPrefilledDriver()
            ->getShareById('INBOX%2FCalendar')
            ->get('share_name')
        );
    }

    public function testNewShare()
    {
        $this->assertEquals(
            'john',
            $this->_getPrefilledDriver()
            ->newShare('john', 'IGNORE')
            ->get('owner')
        );
    }

    /**
     * @expectedException Horde_Share_Exception
     */
    public function testAddShareWithoutName()
    {
        $share = $this->_getPrefilledDriver();
        $object = $share->newShare(null, 'IGNORED');
        $share->addShare($object);
    }

    public function testAddShare()
    {
        $share = $this->_getPrefilledDriver();
        $object = $share->newShare('john', 'IGNORED');
        $object->set('name', 'Test');
        $share->addShare($object);
        $this->assertEquals(
            'INBOX%2FTest',
            $share->getShareById('INBOX%2FTest')->getId()
        );
    }

    public function testShareAddedToList()
    {
        $share = $this->_getPrefilledDriver();
        $object = $share->newShare('john', 'IGNORED');
        $object->set('name', 'Test');
        $share->addShare($object);
        $this->assertContains(
            'INBOX%2FTest',
            array_keys($share->listShares('john'))
        );
    }

    public function testDeleteShare()
    {
        $share = $this->_getPrefilledDriver();
        $object = $share->newShare('john', 'IGNORED');
        $object->set('name', 'Test');
        $share->addShare($object);
        $share->removeShare($object);
        $this->assertNotContains(
            'INBOX%2FTest',
            array_keys($share->listShares('john'))
        );
    }

    public function testGetPermission()
    {
        $share = $this->_getPrefilledDriver();
        $object = $share->newShare('john', 'IGNORED');
        $object->set('name', 'Test');
        $share->addShare($object);
        $this->assertEquals(
            30,
            $share->getShareById('INBOX%2FTest')->getPermission()->getCreatorPermissions()
        );
    }

    public function testSetPermission()
    {
        $share = $this->_getPrefilledDriver();
        $object = $share->newShare('john', 'IGNORED');
        $object->set('name', 'Test');
        $object->addUserPermission('tina', Horde_Perms::SHOW);
        $share->addShare($object);
        $this->assertTrue(
            $share->getShareById('INBOX%2FTest')
            ->hasPermission('tina', Horde_Perms::SHOW)
        );
    }

    public function testNewShareName()
    {
        $share = $this->_getCompleteDriver();
        $object = $share->newShare('john', 'NAME');
        $object->set('name', 'test');
        $this->assertEquals('NAME', $object->get('share_name'));
    }

    private function _getPrefilledDriver()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $driver = $this->_getDriver('kronolith');
        $this->storage = $factory->createFromParams(
            array(
                'driver' => 'mock',
                'params' => array(
                    'username' => 'john',
                    'data'   => array(
                        'user/john/Calendar' => array(
                            'annotations' => array(
                                '/shared/vendor/kolab/folder-type' => 'event.default',
                                '/shared/comment' => 'DESCRIPTION',
                                '/shared/vendor/horde/share-params' => base64_encode(serialize(array('share_name' => 'internal_id'))),
                            ),
                            'permissions' => array(
                                'john' => 'alrid'
                            )
                        ),
                    ),
                ),
                'cache'  => new Horde_Cache(
                    new Horde_Cache_Storage_Mock()
                ),
            )
        );
        $list = $this->storage->getList();
        $this->storage->addListQuery($list, Horde_Kolab_Storage_List::QUERY_SHARE);
        $list->synchronize();
        $driver->setStorage($list);
        return $driver;
    }

    private function _getCompleteDriver()
    {
        $factory = new Horde_Kolab_Storage_Factory();
        $driver = $this->_getDriver();
        $this->storage = $factory->createFromParams(
            array(
                'driver' => 'mock',
                'params' => array(
                    'username' => 'john',
                    'data'   => array(
                        'user/john' => array(
                            'permissions' => array(
                                'john' => 'alrid'
                            )
                        )
                    ),
                ),
                'cache'  => new Horde_Cache(
                    new Horde_Cache_Storage_Mock()
                ),
            )
        );
        $list = $this->storage->getList();
        $this->storage->addListQuery($list, Horde_Kolab_Storage_List::QUERY_SHARE);
        $list->synchronize();
        $driver->setStorage($list);
        return $driver;
    }

    private function _getDriver($app = 'mnemo')
    {
        return new Horde_Share_Kolab(
            $app, 'john', new Horde_Perms(), new Horde_Group_Test()
        );
    }
}