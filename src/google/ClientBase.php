<?php

namespace Google;

abstract class ClientBase
{
    abstract public function setScopes(array $scopes): void;
    abstract public function create();
}
