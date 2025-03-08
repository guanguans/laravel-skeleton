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
        $resolver = new OptionsResolver;

        $closure($resolver);

        return $resolver->resolve($options);
    }
}
