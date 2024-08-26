<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Traits;

use Symfony\Component\OptionsResolver\OptionsResolver;

trait Configurable
{
    protected array $options;

    public function configure(array $options, \Closure $closure): self
    {
        $this->options = $this->configureOptions($options, $closure);

        return $this;
    }

    public function configureOptions(array $options, \Closure $closure): array
    {
        $resolver = new OptionsResolver();

        $closure($resolver);

        return $resolver->resolve($options);
    }
}
