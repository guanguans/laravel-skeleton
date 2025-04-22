<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Providers;

use App\Support\Attributes\Autowired;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class AutowiredServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->forever();
    }

    /**
     * @see https://github.com/spring-projects/spring-framework/blob/main/spring-beans/src/main/java/org/springframework/beans/factory/annotation/Autowired.java
     *
     * @noinspection PhpExpressionResultUnusedInspection
     */
    private function forever(): void
    {
        $this->app->resolving(static function (mixed $object, Application $app): void {
            if (
                !\is_object($object)
                || !str($object::class)->is(config('services.autowired.only'))
                || str($object::class)->is(config('services.autowired.except'))
            ) {
                return;
            }

            $reflectionObject = new \ReflectionObject($object);

            foreach ($reflectionObject->getProperties() as $reflectionProperty) {
                if (
                    !$reflectionProperty->isDefault()
                    || $reflectionProperty->isStatic()
                    || [] === ($attributes = $reflectionProperty->getAttributes(Autowired::class))
                ) {
                    continue;
                }

                /** @var Autowired $autowired */
                $autowired = $attributes[0]->newInstance();
                $property = "{$reflectionObject->getName()}::\${$reflectionProperty->getName()}";
                $propertyType = value(static function () use ($autowired, $reflectionProperty, $property): string {
                    if ($autowired->propertyType) {
                        return $autowired->propertyType;
                    }

                    $reflectionPropertyType = $reflectionProperty->getType();

                    if ($reflectionPropertyType instanceof \ReflectionNamedType && !$reflectionPropertyType->isBuiltin()) {
                        return $reflectionPropertyType->getName();
                    }

                    throw new \LogicException(\sprintf(
                        "Attribute [%s] of property [$property] miss a argument [propertyType], or property [$property] mustn't be a built-in named type.",
                        Autowired::class,
                    ));
                });

                try {
                    $reflectionProperty->isPublic() or $reflectionProperty->setAccessible(true);
                    $reflectionProperty->setValue($object, $app->make($propertyType, $autowired->parameters));
                } catch (\Throwable $throwable) {
                    throw new \TypeError(
                        "Type [$propertyType] of property [$property] resolve failed [{$throwable->getMessage()}].",
                        $throwable->getCode(),
                        $throwable
                    );
                }
            }
        });
    }
}
