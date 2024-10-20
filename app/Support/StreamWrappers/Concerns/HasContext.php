<?php

/** @noinspection MethodVisibilityInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\StreamWrappers\Concerns;

/**
 * @mixin \App\Support\StreamWrappers\StreamWrapper
 */
trait HasContext
{
    /** @var null|resource */
    public $context;

    /**
     * 必须在 `__construct`、`dir_opendir`、`stream_open` 方法中提前调用该方法，
     * 才能在之后无上下文的方法(如 mkdir、url_stat...)中调用该方法获取到上下文。
     *
     * @see static::__construct()
     * @see static::dir_opendir()
     * @see static::stream_open()
     *
     * @return resource
     */
    protected function getContext(): mixed
    {
        $this->context ??= stream_context_get_default() ?? stream_context_create();
        $options = stream_context_get_options($this->context) and stream_context_set_default($options);

        return $this->context;
    }

    protected function getContextOption(string $key, mixed $default = null): mixed
    {
        return $this->getContextOptions()[$key] ?? $default;
    }

    protected function getContextOptions(): array
    {
        return $this->getGlobalContextOption(static::name());
    }

    protected function getGlobalContextOption(string $wrapper): array
    {
        return $this->getGlobalContextOptions()[$wrapper] ?? [];
    }

    protected function getGlobalContextOptions(): array
    {
        return stream_context_get_options($this->getContext());
    }

    protected function setContextOption(string $key, mixed $value): static
    {
        stream_context_set_option($this->getContext(), static::name(), $key, $value);

        return $this;
    }

    protected function setContextOptions(array $contextOptions): static
    {
        foreach ($contextOptions as $key => $value) {
            $this->setContextOption($key, $value);
        }

        return $this;
    }

    // protected function setContextOption(string $key, mixed $value): static
    // {
    //     return $this->setContextOptions([$key => $value]);
    // }

    // protected function setContextOptions(array $contextOptions): static
    // {
    //     return $this->setGlobalContextOption(static::name(), $contextOptions);
    // }

    protected function setGlobalContextOption(string $wrapper, array $contextOptions): static
    {
        $globalContextOptions = $this->getGlobalContextOptions();
        $globalContextOptions[$wrapper] = $contextOptions;

        return $this->setGlobalContextOptions($globalContextOptions);
    }

    protected function setGlobalContextOptions(array $contextOptions): static
    {
        stream_context_set_options($this->getContext(), $contextOptions);

        return $this;
    }

    protected function mergeContextOption(string $key, mixed $value): static
    {
        return $this->mergeContextOptions([$key => $value]);
    }

    protected function mergeContextOptions(array $contextOptions): static
    {
        return $this->mergeGlobalContextOption(static::name(), $contextOptions);
    }

    protected function mergeGlobalContextOption(string $wrapper, array $contextOptions): static
    {
        return $this->mergeGlobalContextOptions([$wrapper => $contextOptions]);
    }

    protected function mergeGlobalContextOptions(array $contextOptions): static
    {
        $this->setGlobalContextOptions(array_merge_recursive($this->getGlobalContextOptions(), $contextOptions));

        return $this;
    }

    protected function replaceContextOption(string $key, mixed $value): static
    {
        return $this->replaceContextOptions([$key => $value]);
    }

    protected function replaceContextOptions(array $contextOptions): static
    {
        return $this->replaceGlobalContextOption(static::name(), $contextOptions);
    }

    protected function replaceGlobalContextOption(string $wrapper, array $contextOptions): static
    {
        return $this->replaceGlobalContextOptions([$wrapper => $contextOptions]);
    }

    protected function replaceGlobalContextOptions(array $contextOptions): static
    {
        $this->setGlobalContextOptions(array_replace_recursive($this->getGlobalContextOptions(), $contextOptions));

        return $this;
    }

    protected function addContextOption(string $key, mixed $value): static
    {
        return $this->addContextOptions([$key => $value]);
    }

    protected function addContextOptions(array $contextOptions): static
    {
        return $this->addGlobalContextOption(static::name(), $contextOptions);
    }

    protected function addGlobalContextOption(string $wrapper, array $contextOptions): static
    {
        return $this->addGlobalContextOptions([$wrapper => $contextOptions]);
    }

    protected function addGlobalContextOptions(array $contextOptions): static
    {
        $this->setGlobalContextOptions(array_replace_recursive($contextOptions, $this->getGlobalContextOptions()));

        return $this;
    }
}
