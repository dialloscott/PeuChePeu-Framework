<?php
namespace Tests\App\Registration\RegistrationController;

use App\Auth\Table\UserTable;
use App\Registration\Controller\RegistrationController;
use Core\Database\Database;
use Tests\ControllerTestCase;

class RegistrationControllerTest extends ControllerTestCase {

    /**
     * @var RegistrationController
     */
    protected $controller;

    public function setUp()
    {
        $this->makeController(RegistrationController::class);
    }

    public function makeTable () {
        $table = $this->getMockBuilder(UserTable::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([])
            ->getMock();

        $table->method('getTable')->willReturn('fake');

        $database = $this->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([])
            ->getMock();

        $table->method('getDatabase')->willReturn($database);

        return $table;
    }

    public function testRenderTheView () {
        $request = $this->makeRequest('GET');
        $this->shouldRender(
            '@registration/register',
            $this->logicalNot($this->arrayHasKey('errors'))
        );
        $this->controller->register($request, $this->makeTable());
    }

    public function testWithNotUniqueUsername () {
        $request = $this->makeRequest('POST', [
            'username' => 'John doe',
            'email'    => 'john@doe.fr',
            'password' => 'password',
            'password_confirm' => 'password'
        ]);
        $table = $this->makeTable();
        $table->getDatabase()->method('fetchColumn')->willReturn(4);

        $this->shouldRender('@registration/register');
        $this->controller->register($request, $table);
    }

    public function testWithBadEmail () {
        $request = $this->makeRequest('POST', [
            'username' => 'John doe',
            'email'    => 'johndoe.fr',
            'password' => 'password',
            'password_confirm' => 'password'
        ]);
        $table = $this->makeTable();
        $table->getDatabase()->method('fetchColumn')->willReturn(false);

        $this->shouldRender('@registration/register', $this->arrayHasKey('errors'));
        $this->controller->register($request, $table);
    }

    public function testWithGoodParams () {
        $request = $this->makeRequest('POST', [
            'username' => 'John doe',
            'email'    => 'john@doe.fr',
            'password' => 'password',
            'password_confirm' => 'password'
        ]);
        $table = $this->makeTable();
        $table->getDatabase()->method('fetchColumn')->willReturn(false);

        $this->shouldRedirect('auth.login');
        $this->controller->register($request, $table);
    }

}