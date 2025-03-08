<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see https://github.com/Laragear/Meta
 */
class Discover
{
    /**
     * Project path where all discoveries will be done.
     */
    protected string $projectPath;

    /**
     * If the discovery should be recursive.
     */
    protected bool $recursive = false;

    /**
     * If the method filtering should also take into account invokable classes.
     */
    protected bool $invokable = false;

    /**
     * List of filters to iterate on each discovered class.
     *
     * @var array|array<null>
     */
    protected array $filters = ['class' => null, 'method' => null, 'property' => null, 'using' => null];

    /**
     * Create a new Discover instance.
     */
    final public function __construct(
        protected Application $app,
        protected string $path = '',
        protected string $basePath = '',
        protected string $baseNamespace = '',
    ) {
        $this->projectPath = $this->app->basePath();

        if (! $this->baseNamespace) {
            $this->baseNamespace = $this->app->getNamespace();
        }

        if (! $this->basePath) {
            $this->basePath = (string) Str::of($this->app->path())->after($this->projectPath)->trim(\DIRECTORY_SEPARATOR);
        }
    }

    /**
     * Changes the base location and root namespace to discover files.
     *
     * @return $this
     */
    public function atNamespace(string $baseNamespace, ?string $basePath = null): static
    {
        $this->baseNamespace = Str::finish(ucfirst($baseNamespace), '\\');
        $this->basePath = trim($basePath ?: $baseNamespace, '\\');

        return $this;
    }

    /**
     * Search of files recursively.
     *
     * @return $this
     */
    public function recursively(): static
    {
        $this->recursive = true;

        return $this;
    }

    /**
     * Filter classes that are instances of the given classes or interfaces.
     *
     * @return $this
     */
    public function instanceOf(string ...$classes): static
    {
        $this->filters['class'] = static function (\ReflectionClass $class) use ($classes): bool {
            foreach ($classes as $comparable) {
                if (! $class->isSubclassOf($comparable)) {
                    return false;
                }
            }

            return true;
        };

        return $this;
    }

    /**
     * Filter classes implementing the given public methods.
     *
     * @return $this
     */
    public function withMethod(string ...$methods): static
    {
        $this->filters['method'] = function (\ReflectionClass $class) use ($methods): bool {
            if ($this->invokable && ! \in_array('__invoke', $methods, true)) {
                $methods[] = '__invoke';
            }

            foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (Str::is($methods, $method->getName())) {
                    return true;
                }
            }

            return false;
        };

        return $this;
    }

    /**
     * Filter classes implementing the given method using a callback for the \ReflectionMethod object.
     *
     * @param  \Closure(\ReflectionMethod):bool  $callback
     * @return $this
     */
    public function withMethodReflection(string $method, \Closure $callback): static
    {
        $this->filters['method'] = static fn (\ReflectionClass $class): bool => $class->hasMethod($method) && $callback($class->getMethod($method));

        return $this;
    }

    /**
     * Adds the classes that are invokable when filtering by methods.
     *
     * @return $this
     */
    public function orInvokable(): static
    {
        $this->invokable = true;

        return $this;
    }

    /**
     * Filters classes implementing the given public properties.
     *
     * @return $this
     */
    public function withProperty(string ...$properties): static
    {
        $this->filters['property'] = static function (\ReflectionClass $class) use ($properties): bool {
            foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                if (\in_array($property->name, $properties, true)) {
                    return true;
                }
            }

            return false;
        };

        return $this;
    }

    /**
     * Filter the classes for those using the given traits, recursively.
     *
     * @return $this
     */
    public function using(string ...$traits): static
    {
        $this->filters['using'] = static function (\ReflectionClass $class) use ($traits): bool {
            foreach (class_uses_recursive($class->getName()) as $trait) {
                if (Str::is($traits, $trait)) {
                    return true;
                }
            }

            return false;
        };

        return $this;
    }

    /**
     * Filter the classes for those using the given traits, without inheritance.
     *
     * @return $this
     */
    public function parentUsing(string ...$traits): static
    {
        $this->filters['using'] = static function (\ReflectionClass $class) use ($traits): bool {
            foreach ($class->getTraitNames() as $trait) {
                if (Str::is($traits, $trait)) {
                    return true;
                }
            }

            return false;
        };

        return $this;
    }

    /**
     * Returns a Collection for all the classes found.
     *
     * @return \Illuminate\Support\Collection<string, \ReflectionClass>
     */
    public function all(): Collection
    {
        $classes = new Collection;

        $filters = array_filter($this->filters);

        foreach ($this->listAllFiles() as $file) {
            // Try to get the class from the file. If we can't then it's not a class file.
            try {
                $reflection = new \ReflectionClass($this->classFromFile($file));
            } catch (\ReflectionException) {
                continue;
            }

            // If the class cannot be instantiated (like abstract, traits or interfaces), continue.
            if (! $reflection->isInstantiable()) {
                continue;
            }

            // Preemptively pass this class. Now it's left for the filters to keep allowing it.
            $passes = true;

            foreach ($filters as $callback) {
                if (! $passes = $callback($reflection)) {
                    break;
                }
            }

            if ($passes) {
                $classes->put($reflection->name, $reflection);
            }
        }

        return $classes;
    }

    /**
     * Create a new instance of the discoverer.
     */
    public static function in(string $dir): static
    {
        return new static(app(), $dir);
    }

    /**
     * Builds the finder instance to locate the files.
     *
     * @return \Illuminate\Support\Collection<int, \Symfony\Component\Finder\SplFileInfo>
     */
    protected function listAllFiles(): Collection
    {
        return new Collection(
            $this->recursive
                ? $this->app->make(Filesystem::class)->allFiles($this->buildPath())
                : $this->app->make(Filesystem::class)->files($this->buildPath())
        );
    }

    /**
     * Build the path to search for files.
     */
    protected function buildPath(): string
    {
        return (string) Str::of($this->path)
            ->when($this->path, static fn (Stringable $string): Stringable => $string->start(\DIRECTORY_SEPARATOR))
            ->prepend($this->basePath)
            ->start(\DIRECTORY_SEPARATOR)
            ->prepend($this->projectPath);
    }

    /**
     * Extract the class name from the given file path.
     */
    protected function classFromFile(SplFileInfo $file): string
    {
        return (string) Str::of($file->getRealPath())
            ->after($this->projectPath)
            ->trim(\DIRECTORY_SEPARATOR)
            ->beforeLast('.php')
            ->ucfirst()
            ->replace(
                [\DIRECTORY_SEPARATOR, ucfirst($this->basePath.'\\')],
                ['\\', $this->baseNamespace],
            );
    }
}
