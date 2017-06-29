<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class ControllerTestCase extends TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $controller;

    protected function makeController (string $namespace) {
        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([])
            ->getMock();

        $this->controller = $this->getMockBuilder($namespace)
            ->setConstructorArgs([$this->container])
            ->setMethods(['flash', 'render', 'redirect'])
            ->getMock();
    }

    protected function makeRequest (string $method = 'GET', $params = []): \PHPUnit_Framework_MockObject_MockObject {
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([])
            ->getMock();

        $request->method('getMethod')->willReturn($method);
        $request->method('getParsedBody')->willReturn($params);
        return $request;
    }

    public function shouldRender(...$params): void
    {
        $method = $this->controller->expects($this->once())
            ->method('render');

        call_user_func_array([$method, 'with'], $params);
    }

    public function shouldRedirect(...$params): void
    {
        $method = $this->controller->expects($this->once())
            ->method('redirect');

        call_user_func_array([$method, 'with'], $params);
    }

}