<?php

/**
 * Test: Nette\DI\Resolver::autowireArguments()
 */

declare(strict_types=1);

use Nette\DI\Resolver;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class Test
{
}


// class
Assert::equal(
	[new Test],
	Resolver::autowireArguments(
		new ReflectionFunction(function (Test $arg) {}),
		[],
		fn($type) => $type === Test::class ? new Test : null,
	),
);

// nullable class
Assert::equal(
	[new Test],
	Resolver::autowireArguments(
		new ReflectionFunction(function (?Test $arg) {}),
		[],
		fn($type) => $type === Test::class ? new Test : null,
	),
);

// nullable optional class
Assert::equal(
	[new Test],
	Resolver::autowireArguments(
		new ReflectionFunction(function (?Test $arg = null) {}),
		[],
		fn($type) => $type === Test::class ? new Test : null,
	),
);

// nullable optional scalar
Assert::equal(
	[],
	Resolver::autowireArguments(
		new ReflectionFunction(function (?int $arg = null) {}),
		[],
		fn($type) => $type === Test::class ? new Test : null,
	),
);

// optional arguments + positional
Assert::equal(
	['b' => 'new'],
	Resolver::autowireArguments(
		new ReflectionFunction(function ($a = 1, $b = 2) {}),
		[1 => 'new'],
		function () {},
	),
);

// optional arguments + named
Assert::equal(
	['b' => 'new'],
	Resolver::autowireArguments(
		new ReflectionFunction(function ($a = 1, $b = 2) {}),
		['b' => 'new'],
		function () {},
	),
);

// optional arguments + variadics
Assert::equal(
	['args' => ['new1', 'new2']],
	Resolver::autowireArguments(
		new ReflectionFunction(function ($a = 1, ...$args) {}),
		[1 => 'new1', 2 => 'new2'],
		function () {},
	),
);

// optional arguments + variadics
Assert::equal(
	['new', 'new1', 'new2'],
	Resolver::autowireArguments(
		new ReflectionFunction(function ($a = 1, ...$args) {}),
		['a' => 'new', 1 => 'new1', 2 => 'new2'],
		function () {},
	),
);

// variadics as items
Assert::equal(
	[1, 2, 3],
	Resolver::autowireArguments(
		new ReflectionFunction(function (...$args) {}),
		[1, 2, 3],
		function () {},
	),
);

// variadics as array
Assert::equal(
	[1, 2, 3],
	Resolver::autowireArguments(
		new ReflectionFunction(function (...$args) {}),
		['args' => [1, 2, 3]],
		function () {},
	),
);

// named parameter intentionally overwrites the indexed one (due to overwriting in the configuration)
Assert::equal(
	[2],
	Resolver::autowireArguments(
		new ReflectionFunction(function ($a) {}),
		[1, 'a' => 2],
		function () {},
	),
);

// optional union
Assert::same(
	[],
	Resolver::autowireArguments(
		new ReflectionFunction(function (stdClass|int $x = 1) {}),
		[],
		function () {},
	),
);

// named variadics
Assert::equal(
	['a' => 1, 'b' => 2, 'c' => 3],
	Resolver::autowireArguments(
		new ReflectionFunction(function (...$args) {}),
		['a' => 1, 'b' => 2, 'c' => 3],
		function () {},
	),
);
