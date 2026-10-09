<?php

namespace app\components;

class Request
{
    /** @var array<string, mixed> */
    private readonly array $query;

    /** @var array<string, mixed> */
    private readonly array $post;

    public function __construct(?array $query = null, ?array $post = null)
    {
        $this->query = $query ?? $_GET;
        $this->post = $post ?? $_POST;
    }

    /**
     * @return array<string, mixed>|mixed
     */
    public function getQuery(?string $param = null): mixed
    {
        if ($param === null) {
            return $this->query;
        }

        return $this->query[$param] ?? null;
    }

    /**
     * @return array<string, mixed>|mixed
     */
    public function getPost(?string $param = null): mixed
    {
        if ($param === null) {
            return $this->post;
        }

        return $this->post[$param] ?? null;
    }
}
