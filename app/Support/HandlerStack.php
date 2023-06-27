<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Creates a composed Guzzle handler function by stacking middlewares on top of
 * an HTTP handler function.
 *
 * ```php
 * use App\Support\HandlerStack;
 * use Illuminate\Support\Stringable;
 *
 * $handlerStack = HandlerStack::create();
 *
 * $handlerStack->setHandler(function ($passable){
 *     return Str::of($passable);
 * });
 *
 * $handlerStack->push(function (callable $handler){
 *     return function ($passable) use ($handler){
 *         $passable = ucfirst($passable);
 *
 *         // @var \Stringable $stringable
 *         $stringable = $handler($passable);
 *
 *         return $stringable->title();
 *     };
 * }, 'ucfirst');
 *
 * $handlerStack->push(function (callable $handler){
 *     return function ($passable) use ($handler){
 *         $passable = ucwords($passable);
 *
 *         // @var \Stringable $stringable
 *         $stringable = $handler($passable);
 *
 *         return $stringable->finish('!');
 *     };
 * }, 'ucwords');
 *
 * dump((string)$handlerStack);
 * // dump($handlerStack->call('hello world'));
 * dd($handlerStack('hello world'));
 * ```
 *
 * @see https://github.com/guzzle/guzzle/blob/master/src/HandlerStack.php
 *
 * @final
 */
class HandlerStack implements \Stringable
{
    /** @var null|(callable(mixed): mixed) */
    private $handler;

    /** @var array{(callable(callable(mixed): mixed): callable), (null|string)}[] */
    private array $stack = [];

    /** @var null|(callable(mixed): mixed) */
    private $cached;

    /**
     * @param  null|(callable(mixed): mixed)  $handler underlying HTTP handler
     */
    public function __construct(?callable $handler = null)
    {
        $this->handler = $handler ?: static fn ($passable) => $passable;
    }

    /**
     * Invokes the handler stack as a composed handler.
     *
     * @param  mixed  $passable
     */
    public function __invoke($passable): mixed
    {
        $handler = $this->resolve();

        return $handler($passable);
    }

    /**
     * Dumps a string representation of the stack.
     */
    public function __toString(): string
    {
        $depth = 0;
        $stack = [];

        if (null !== $this->handler) {
            $stack[] = '0) Handler: '.$this->debugCallable($this->handler);
        }

        $result = '';
        foreach (array_reverse($this->stack) as $tuple) {
            ++$depth;
            $str = "{$depth}) Name: '{$tuple[1]}', ";
            $str .= 'Function: '.$this->debugCallable($tuple[0]);
            $result = "> {$str}\n{$result}";
            $stack[] = $str;
        }

        foreach (array_keys($stack) as $k) {
            $result .= "< {$stack[$k]}\n";
        }

        return $result;
    }

    /**
     * Creates a default handler stack that can be used by clients.
     *
     * The returned handler will wrap the provided handler or use the most
     * appropriate default handler for your system. The returned HandlerStack has
     * support for cookies, redirects, HTTP error exceptions, and preparing a body
     * before sending.
     *
     * The returned handler stack can be passed to a client in the "handler"
     * option.
     */
    public static function create(): self
    {
        /** @noinspection PhpParamsInspection */
        return new self(...\func_get_args());
    }

    /**
     * Invokes the handler stack as a composed handler.
     *
     * @param  mixed  $passable
     */
    public function call($passable): mixed
    {
        return $this($passable);
    }

    /**
     * Set the HTTP handler that actually returns a promise.
     *
     * @param  callable(mixed): mixed  $handler accepts a request and array of options and returns a Promise
     */
    public function setHandler(callable $handler): void
    {
        $this->handler = $handler;
        $this->cached = null;
    }

    /**
     * Returns true if the builder has a handler.
     */
    public function hasHandler(): bool
    {
        return null !== $this->handler;
    }

    /**
     * Unshift a middleware to the bottom of the stack.
     *
     * @param  callable(callable): callable  $middleware Middleware function
     * @param  string  $name name to register for this middleware
     */
    public function unshift(callable $middleware, ?string $name = null): void
    {
        array_unshift($this->stack, [$middleware, $name]);
        $this->cached = null;
    }

    /**
     * Push a middleware to the top of the stack.
     *
     * @param  callable(callable): callable  $middleware Middleware function
     * @param  string  $name name to register for this middleware
     */
    public function push(callable $middleware, string $name = ''): void
    {
        $this->stack[] = [$middleware, $name];
        $this->cached = null;
    }

    /**
     * Add a middleware before another middleware by name.
     *
     * @param  string  $findName Middleware to find
     * @param  callable(callable): callable  $middleware Middleware function
     * @param  string  $withName name to register for this middleware
     */
    public function before(string $findName, callable $middleware, string $withName = ''): void
    {
        $this->splice($findName, $withName, $middleware, true);
    }

    /**
     * Add a middleware after another middleware by name.
     *
     * @param  string  $findName Middleware to find
     * @param  callable(callable): callable  $middleware Middleware function
     * @param  string  $withName name to register for this middleware
     */
    public function after(string $findName, callable $middleware, string $withName = ''): void
    {
        $this->splice($findName, $withName, $middleware, false);
    }

    /**
     * Remove a middleware by instance or name from the stack.
     *
     * @param  callable|string  $remove middleware to remove by instance or name
     */
    public function remove($remove): void
    {
        if (! \is_string($remove) && ! \is_callable($remove)) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a callable or string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }

        $this->cached = null;
        $idx = \is_callable($remove) ? 0 : 1;
        $this->stack = array_values(array_filter(
            $this->stack,
            static fn ($tuple) => $tuple[$idx] !== $remove
        ));
    }

    /**
     * Compose the middleware and handler into a single callable function.
     *
     * @return callable(mixed): mixed
     */
    public function resolve(): callable
    {
        if (null === $this->cached) {
            if (($prev = $this->handler) === null) {
                throw new \LogicException('No handler has been specified');
            }

            foreach (array_reverse($this->stack) as $fn) {
                /** @var callable(mixed): mixed $prev */
                $prev = $fn[0]($prev);
            }

            $this->cached = $prev;
        }

        return $this->cached;
    }

    private function findByName(string $name): int
    {
        foreach ($this->stack as $k => $v) {
            if ($v[1] === $name) {
                return $k;
            }
        }

        throw new \InvalidArgumentException("Middleware not found: $name");
    }

    /**
     * Splices a function into the middleware list at a specific position.
     */
    private function splice(string $findName, string $withName, callable $middleware, bool $before): void
    {
        $this->cached = null;
        $idx = $this->findByName($findName);
        $tuple = [$middleware, $withName];

        if ($before) {
            if (0 === $idx) {
                array_unshift($this->stack, $tuple);
            } else {
                $replacement = [$tuple, $this->stack[$idx]];
                array_splice($this->stack, $idx, 1, $replacement);
            }
        } elseif ($idx === \count($this->stack) - 1) {
            $this->stack[] = $tuple;
        } else {
            $replacement = [$this->stack[$idx], $tuple];
            array_splice($this->stack, $idx, 1, $replacement);
        }
    }

    /**
     * Provides a debug string for a given callable.
     *
     * @param  callable|string  $fn function to write as a string
     */
    private function debugCallable($fn): string
    {
        if (\is_string($fn)) {
            return "callable({$fn})";
        }

        if (\is_array($fn)) {
            return \is_string($fn[0])
                ? "callable({$fn[0]}::{$fn[1]})"
                : "callable(['".\get_class($fn[0])."', '{$fn[1]}'])";
        }

        return 'callable('.spl_object_hash($fn).')';
    }
}
