<?php
namespace Core\Http;

use Core\Validator;
use Psr\Http\Message\ServerRequestInterface;

class Request {

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $errors = [];

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->errors = $this->getValidator()->getErrors();
    }

    public function isValid () {
        return empty($this->errors);
    }

    protected function getValidator(): Validator
    {
        return new Validator($this->getParams());
    }

    protected function getParams (): array {
        return array_merge($this->request->getParsedBody(), $this->request->getUploadedFiles());
    }

    protected function getFilteredParams (): array {
        return $this->getParams();
    }

}