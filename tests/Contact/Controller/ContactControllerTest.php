<?php

namespace Tests\App\Contact\Controller;

use App\Contact\Controller\ContactController;
use PHPUnit\Framework\TestCase;
use Tests\ControllerTestCase;

class ContactControllerTest extends ControllerTestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mailer;

    public function setUp()
    {
        parent::setUp();
        $this->makeController(ContactController::class);
        $this->mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
    }

    public function testRenderTheRightView () {
        $this->controller->expects($this->once())
            ->method('render')
            ->with('@contact/contact', $this->logicalNot($this->arrayHasKey('errors')));

        $this->controller->contact($this->makeRequest(), $this->mailer);
    }

    public function testCatchErrors () {
        $this->controller->expects($this->once())
            ->method('render')
            ->with('@contact/contact', $this->arrayHasKey('errors'));

        $request = $this->makeRequest('POST', [
            'email' => 'Hello',
            'name'  => 'Some right content for the test purpose but it won\'t work sinc',
            'message'  => 'Some right content for the test purpose but it won\'t work sinc'
        ]);

        $this->controller->contact($request, $this->mailer);
    }

    public function testLetPassValidRequest () {
        $this->controller->expects($this->once())->method('redirect');
        $this->mailer->expects($this->once())->method('send');

        $request = $this->makeRequest('POST', [
            'name' => 'John Doe',
            'email'  => 'email@demo.fr',
            'message'  => 'Some right content for the test purpose but it won\'t work sinc'
        ]);

        $this->controller->contact($request, $this->mailer);
    }

}
