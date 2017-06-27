<?php
class AdminBlogControllerTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var \App\Blog\Controller\Admin\BlogController
     */
    private $controller;

    public function setUp()
    {
        $this->uploader = $this->getMockBuilder(\App\Blog\PostUpload::class)
            ->disableOriginalConstructor()
            ->setMethods(['upload', 'delete'])
            ->getMock();

        $this->controller = $this->getMockBuilder(\App\Blog\Controller\Admin\BlogController::class)
            ->disableOriginalConstructor()
            ->setMethods(['render', 'redirect', 'flash'])
            ->getMock();

        $this->table = $this->getMockBuilder(\App\Blog\Table\PostTable::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([])
            ->getMock();

        $this->table->method('findOrFail')->willReturn(new \App\Blog\PostEntity());

        $this->entity = new \App\Blog\PostEntity();
        $this->entity->id = 2;
        $this->table->method('find')->willReturn($this->entity);
    }

    public function makeRequest (string $method = 'GET', array $params = [], array $files = []) {
        $request = $this->getMockBuilder(\Psr\Http\Message\ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([])
            ->getMock();
        $request
            ->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);
        $request
            ->expects($this->any())
            ->method('getParsedBody')
            ->willReturn($params);
        $request
            ->expects($this->any())
            ->method('getUploadedFiles')
            ->willReturn($files);

        return $request;
    }

    public function makeFile () {
        $file = $this->getMockBuilder(\Slim\Http\UploadedFile::class)
            ->setConstructorArgs(['/tmp/demo.jpg', 'demo.jpg', 'image/jpeg', 2000])
            ->setMethods(['move'])
            ->getMock();

        return $file;
    }

    public function testEditWithBadParams () {
        $this->controller->expects($this->once())
            ->method('render')
            ->with('@blog/admin/edit');

        $this->controller->edit(3, $this->makeRequest('PUT'), $this->table, $this->uploader);
    }

    public function testEditWithGoodParams () {
        $this->controller->expects($this->once())
            ->method('redirect')
            ->with('blog.admin.index');

        $file = $this->makeFile();

        // Le fichier doit être uploadé
        $this->uploader
            ->expects($this->once())
            ->method('upload')
            ->with($file);

        // LA talbe doit être mis à jour
        $this->table->expects($this->once())
            ->method('update');

        $params = [
            'name' => 'Post title',
            'content' => 'Some fake content for test here it is it is a demonstration',
            'created_at' => date('Y-m-d H:i:s'),
            'slug' => 'azeeaz-azeaze'
        ];

        $this->controller->edit(3, $this->makeRequest('PUT', $params, ['image' => $file]), $this->table, $this->uploader);
    }

}