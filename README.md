# Bag2 Meta Programming (`Bag2\Meta`)

Library for meta programming.

## Classes

### `FunctionDispacher`

**NOTICE**: The API for this class will maybe change.

```php
<?php declare(strict_types=1);

use Bag2\Meta\FunctionDispatcher;

function target_func(int $id, string $name)
{
    // ...
}

$user_input = ['name' => 'foo', 'id' => '123'];

$dispatcher = FunctionDispatcher::fromCallable('target_func');
$dispatcher->dispatch($user_input);
```
