<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Tests\Extension\Core\Type;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Exception\FormException;

class FileTypeTest extends TypeTestCase
{
    public function testFormBuilderIfEntityHasFile()
    {
        $this->factory->createBuilder('file')
            ->getForm()
            ->setData($this->createUploadedFileMock('abcdef', 'original.jpg', true))
        ;
    }

    public function testFormBuilderIfEntityHasEmptyFile()
    {
        $entity = new \StdClass;
        $entity->imageFile = $this->createUploadedFileMock('abcdef', 'original.jpg', true);

        try {
            $form = $this->factory->createBuilder('form', $entity)
                ->add('imageFile', 'file')
                ->getForm();

            $form->bind(array('imageFile' => null));
        } catch (FormException $e) {
            $this->fail();
        }
    }

    public function testDontPassValueToView()
    {
        $form = $this->factory->create('file');
        $form->bind(array(
            'file' => $this->createUploadedFileMock('abcdef', 'original.jpg', true),
        ));
        $view = $form->createView();

        $this->assertEquals('', $view->vars['value']);
    }

    private function createUploadedFileMock($name, $originalName, $valid)
    {
        $file = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $file
            ->expects($this->any())
            ->method('getBasename')
            ->will($this->returnValue($name))
        ;
        $file
            ->expects($this->any())
            ->method('getClientOriginalName')
            ->will($this->returnValue($originalName))
        ;
        $file
            ->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue($valid))
        ;

        return $file;
    }
}
