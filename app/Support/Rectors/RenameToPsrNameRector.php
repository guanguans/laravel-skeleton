<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Rectors;

use Illuminate\Support\Str;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

class RenameToPsrNameRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var array<string> */
    protected array $except = [
        '*::*',
        'class',
        'false',
        'null',
        'self',
        'static',
        'stdClass',
        'true',

        /**
         * @see https://www.php.net/manual/zh/reserved.variables.php
         */
        'GLOBALS',
        '_COOKIE',
        '_ENV',
        'HTTP_ENV_VARS',
        '_FILES',
        'HTTP_POST_FILES',
        '_GET',
        'HTTP_GET_VARS',
        '_POST',
        'HTTP_POST_VARS',
        '_REQUEST',
        '_SERVER',
        'HTTP_SERVER_VARS',
        '_SESSION',
        'HTTP_SESSION_VARS',
        'HTTP_RAW_POST_DATA',
        'http_response_header',
        'php_errormsg',

        /**
         * @see https://www.php.net/streamwrapper
         */
        'dir_closedir',
        'dir_opendir',
        'dir_readdir',
        'dir_rewinddir',
        'stream_cast',
        'stream_close',
        'stream_eof',
        'stream_flush',
        'stream_lock',
        'stream_metadata',
        'stream_open',
        'stream_read',
        'stream_seek',
        'stream_set_option',
        'stream_stat',
        'stream_tell',
        'stream_truncate',
        'stream_write',
        'unlink',
        'url_stat',

        /**
         * @see https://www.php.net/manual/zh/class.php-user-filter.php
         */
        'php_user_filter',
    ];

    /**
     * @throws PoorDocumentationException
     * @throws ShouldNotHappenException
     */
    #[\Override]
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Rename to psr name',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
                        // lower snake
                        function functionName(){}
                        functionName();
                        call_user_func('functionName');
                        call_user_func_array('functionName', []);
                        function_exists('functionName');

                        // ucfirst camel
                        class class_name{}
                        enum enum_name{}
                        enum Enum{case case_name;}
                        interface interface_name{}
                        trait trait_name{}
                        class Foo extends class_name implements interface_name{}
                        class_name::$property;
                        class_name::CONST;
                        class_name::method();
                        enum Enum implements interface_name{}
                        use class_name;
                        use trait_name;
                        class_alias('class_name', 'alias_class_name');
                        class_exists('class_name');
                        class_implements('class_name');
                        class_parents('class_name');
                        class_uses('class_name');
                        enum_exists('enum_name');
                        get_class_methods('class_name');
                        get_class_vars('class_name');
                        get_parent_class('class_name');
                        interface_exists('interface_name');
                        is_subclass_of('class_name', 'parent_class_name');
                        trait_exists('trait_name', true);

                        // upper snake
                        class Foo{public const constName = 'const';}
                        Foo::constName;
                        define('constName', 'const');
                        defined('constName');
                        constant('constName');
                        constant('Foo::constName');
                        constName;

                        // lcfirst camel
                        $var_name;
                        $object->method_name();
                        $object->property_name;
                        call_user_method('method_name', $object);
                        call_user_method_array('method_name', $object);
                        class Foo{public $property_name;}
                        class Foo{public function method_name(){}}
                        class Foo{public int $property_name;}
                        Foo::$property_name;
                        Foo::method_name();
                        method_exists($object, 'method_name');
                        property_exists($object, 'property_name');
                        CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
                        // lower snake
                        function function_name(){}
                        function_name();
                        call_user_func('function_name');
                        call_user_func_array('function_name');
                        function_exists('function_name');

                        // ucfirst camel
                        class ClassName{}
                        enum EnumName{}
                        enum Enum{case CaseName;}
                        interface InterfaceName{}
                        trait TraitName{}
                        class Foo extends ClassName implements InterfaceName{}
                        ClassName::$property;
                        ClassName::CONST;
                        ClassName::method();
                        enum Enum implements InterfaceName{}
                        use ClassName;
                        use TraitName;
                        class_alias('ClassName', 'AliasClassName');
                        class_exists('ClassName');
                        class_implements('ClassName');
                        class_parents('ClassName');
                        class_uses('ClassName');
                        enum_exists('EnumName');
                        get_class_methods('ClassName');
                        get_class_vars('ClassName');
                        get_parent_class('ClassName');
                        interface_exists('InterfaceName');
                        is_subclass_of('ClassName', 'ParentClassName');
                        trait_exists('TraitName', true);

                        // upper snake
                        class Foo{public const CONST_NAME = 'const';}
                        Foo::CONST_NAME;
                        define('CONST_NAME', 'const');
                        defined('CONST_NAME');
                        constant('CONST_NAME');
                        constant('Foo::CONST_NAME');
                        CONST_NAME;

                        // lcfirst camel
                        $varName
                        $object->methodName();
                        $object->propertyName;
                        class Foo{public $propertyName;}
                        class Foo{public function methodName(){}}
                        class Foo{public int $propertyName;}
                        Foo::$propertyName;
                        Foo::methodName();
                        call_user_method('methodName', $object);
                        call_user_method_array('methodName', $object);
                        method_exists($object, 'methodName');
                        property_exists($object, 'propertyName');
                        CODE_SAMPLE
                    ,
                    ['exceptName']
                ),
            ]
        );
    }

    #[\Override]
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
            Node\Expr\Variable::class,
            Node\Identifier::class,
            Node\Name::class,
        ];
    }

    /**
     * @param  Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier|Node\Name  $node
     */
    #[\Override]
    public function refactor(Node $node)
    {
        try {
            if ($this->shouldLowerSnakeName($node)) {
                return $this->rename($node, static fn (string $name): string => Str::lower(Str::snake($name)));
            }

            if ($this->shouldUcfirstCamelName($node)) {
                return $this->rename($node, static fn (string $name): string => Str::ucfirst(Str::camel($name)));
            }

            if ($this->shouldUpperSnakeName($node)) {
                return $this->rename($node, static fn (string $name): string => Str::upper(Str::snake($name)));
            }

            if ($this->shouldLcfirstCamelName($node)) {
                return $this->rename($node, static fn (string $name): string => Str::lcfirst(Str::camel($name)));
            }
        } catch (\RuntimeException) {
            // skip
        }
    }

    /**
     * @param  iterable<string>|string  $patterns
     */
    public function isMatches(string $value, iterable|string $patterns): bool
    {
        if (! is_iterable($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            $pattern = (string) $pattern;
            if ($pattern === $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');
            $pattern = str_replace('\*', '.*', $pattern);
            if (preg_match('#^'.$pattern.'\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }

    #[\Override]
    public function configure(array $configuration): void
    {
        Assert::allStringNotEmpty($configuration);
        $this->except = [...$this->except, ...$configuration];
    }

    /**
     * @param  Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier|Node\Name  $node
     */
    protected function rename(Node $node, callable $renamer): Node
    {
        $renamer = fn (string $name): string => $renamer((function (string $name): string {
            throw_if($this->isMatches($name, $this->except), new \RuntimeException("The name[$name] is skipped."));

            if (ctype_upper(preg_replace('/[^a-zA-Z]/', '', $name))) {
                return mb_strtolower($name, 'UTF-8');
            }

            return $name;
        })($name));

        if ($node instanceof Node\Name) {
            $node->getParts()[\count($node->getParts()) - 1] = $renamer($node->getParts()[\count($node->getParts()) - 1]);

            return $node;
        }

        if (
            $this->isSubclasses($node, [
                Node\Expr\Variable::class,
                Node\Identifier::class,
            ])
        ) {
            $node->name = $renamer($node->name);

            return $node;
        }

        if ($node instanceof Node\Expr\FuncCall) {
            if (
                $this->isNames($node, [
                    'call_user_func',
                    'call_user_func_array',
                    'call_user_method',
                    'call_user_method_array',
                    'class_alias',
                    'class_exists',
                    'class_implements',
                    'class_parents',
                    'class_uses',
                    'constant',
                    'define',
                    'defined',
                    'enum_exists',
                    'function_exists',
                    'get_class_methods',
                    'get_class_vars',
                    'get_parent_class',
                    'interface_exists',
                    'is_subclass_of',
                    'trait_exists',
                ])
                && $this->hasFuncCallIndexStringArg($node, 0)
            ) {
                $node->args[0]->value->value = $renamer($node->args[0]->value->value);
            }

            if (
                $this->isNames($node, [
                    'class_alias',
                    'is_subclass_of',
                    'method_exists',
                    'property_exists',
                ])
                && $this->hasFuncCallIndexStringArg($node, 1)
            ) {
                $node->args[1]->value->value = $renamer($node->args[1]->value->value);
            }
        }

        return $node;
    }

    /**
     * @param  Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier|Node\Name  $node
     */
    protected function shouldLowerSnakeName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        // function function_name(){}
        if ($node instanceof Node\Identifier && $parent instanceof Node\Stmt\Function_) {
            return true;
        }

        // function_name();
        if ($node instanceof Node\Name && $parent instanceof Node\Expr\FuncCall) {
            return true;
        }

        return $node instanceof Node\Expr\FuncCall
        && $this->isNames($node, [
            // call_user_func('function_name');
            'call_user_func',
            // call_user_func_array('function_name');
            'call_user_func_array',
            // function_exists('function_name');
            'function_exists',
        ])
        && $this->hasFuncCallIndexStringArg($node, 0);
    }

    /**
     * @param  Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier|Node\Name  $node
     */
    protected function shouldUcfirstCamelName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        if (
            $node instanceof Node\Identifier
            && $this->isSubclasses($parent, [
                // interface InterfaceName{}
                Node\Stmt\Interface_::class,
                // class ClassName{}
                Node\Stmt\Class_::class,
                // trait TraitName{}
                Node\Stmt\Trait_::class,
                // enum EnumName{}
                Node\Stmt\Enum_::class,
                // enum Enum{case CaseName;}
                Node\Stmt\EnumCase::class,
            ])
        ) {
            return true;
        }

        if (
            $node instanceof Node\Name
            && ! $this->isName($node, 'stdClass')
            && $this->isSubclasses($parent, [
                // class Foo extends ClassName implements InterfaceName{}
                Node\Stmt\Class_::class,
                // enum Enum implements InterfaceName{}
                Node\Stmt\Enum_::class,
                // use ClassName;
                Node\Stmt\UseUse::class,
                // use TraitName;
                Node\Stmt\TraitUse::class,
                // ClassName::CONST;
                Node\Expr\ClassConstFetch::class,
                // ClassName::$property;
                Node\Expr\StaticPropertyFetch::class,
                // ClassName::method();
                Node\Expr\StaticCall::class,
            ])
        ) {
            return true;
        }

        if ($node instanceof Node\Expr\FuncCall) {
            if (
                $this->isNames($node, [
                    // class_alias('ClassName', 'AliasClassName');
                    'class_alias',
                    // class_exists('ClassName');
                    'class_exists',
                    // class_implements('ClassName');
                    'class_implements',
                    // class_parents('ClassName');
                    'class_parents',
                    // class_uses('ClassName');
                    'class_uses',
                    // enum_exists('EnumName');
                    'enum_exists',
                    // get_class_methods('ClassName');
                    'get_class_methods',
                    // get_class_vars('ClassName');
                    'get_class_vars',
                    // get_parent_class('ClassName');
                    'get_parent_class',
                    // interface_exists('InterfaceName');
                    'interface_exists',
                    // is_subclass_of('ClassName', 'ParentClassName');
                    'is_subclass_of',
                    // trait_exists('TraitName', true);
                    'trait_exists',
                ])
                && $this->hasFuncCallIndexStringArg($node, 0)
            ) {
                return true;
            }

            if (
                $this->isNames($node, [
                    // class_alias('ClassName', 'AliasClassName');
                    'class_alias',
                    // is_subclass_of('ClassName', 'ParentClassName');
                    'is_subclass_of',
                ])
                && $this->hasFuncCallIndexStringArg($node, 1)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier|Node\Name  $node
     */
    protected function shouldUpperSnakeName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        if (
            $node instanceof Node\Identifier
            && ! $this->isName($node, 'class')
            && $this->isSubclasses($parent, [
                // class Foo{public const CONST_NAME = 'const';}
                Node\Const_::class,
                // Foo::CONST_NAME;
                Node\Expr\ClassConstFetch::class,
            ])
        ) {
            return true;
        }

        if (
            $node instanceof Node\Expr\FuncCall
            && $this->isNames($node, [
                // define('CONST_NAME', 'const');
                'define',
                // defined('CONST_NAME');
                'defined',
                // constant('Foo::CONST_NAME');
                'constant',
            ])
            && $this->hasFuncCallIndexStringArg($node, 0)
        ) {
            return true;
        }

        // CONST_NAME;
        return $node instanceof Node\Name
        && ! $this->isNames($node, ['null', 'true', 'false'])
        && $parent instanceof Node\Expr\ConstFetch;
    }

    /**
     * @param  Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier|Node\Name  $node
     */
    protected function shouldLcfirstCamelName(Node $node): bool
    {
        // $varName;
        if ($node instanceof Node\Expr\Variable && \is_string($node->name)) {
            return true;
        }

        if (
            $node instanceof Node\Identifier
            && $this->isSubclasses($node->getAttribute('parent'), [
                // class Foo{public $propertyName;}
                Node\Stmt\Property::class,
                // class Foo{public int $propertyName;}
                Node\Stmt\PropertyProperty::class,
                // class Foo{public function methodName(){}}
                Node\Stmt\ClassMethod::class,
                // $object->propertyName;
                Node\Expr\PropertyFetch::class,
                // Foo::$propertyName;
                Node\Expr\StaticPropertyFetch::class,
                // $object->methodName();
                Node\Expr\MethodCall::class,
                // Foo::methodName();
                Node\Expr\StaticCall::class,
            ])
        ) {
            return true;
        }

        if ($node instanceof Node\Expr\FuncCall) {
            if (
                $this->isNames($node, [
                    // call_user_method('methodName', $object);
                    'call_user_method',
                    // call_user_method_array('methodName', $object);
                    'call_user_method_array',
                ])
                && $this->hasFuncCallIndexStringArg($node, 0)
            ) {
                return true;
            }

            if (
                $this->isNames($node, [
                    // method_exists($object, 'methodName');
                    'method_exists',
                    // property_exists($object, 'propertyName');
                    'property_exists',
                ])
                && $this->hasFuncCallIndexStringArg($node, 1)
            ) {
                return true;
            }
        }

        return false;
    }

    protected function isSubclasses($object, array $classes): bool
    {
        if (! \is_object($object)) {
            return false;
        }

        foreach ($classes as $class) {
            if ($object instanceof $class) {
                return true;
            }
        }

        return false;
    }

    protected function hasFuncCallIndexStringArg(Node\Expr\FuncCall $funcCall, int $index): bool
    {
        return isset($funcCall->args[$index])
            && $funcCall->args[$index]->name === null
            && $funcCall->args[$index]->value instanceof Node\Scalar\String_;
    }

    protected function hasFuncCallNameStringArg(Node\Expr\FuncCall $funcCall, string $name): bool
    {
        foreach ($funcCall->args as $arg) {
            if (
                $arg->name instanceof Node\Identifier
                && $arg->name->name === $name
                && $arg->value instanceof Node\Scalar\String_
            ) {
                return true;
            }
        }

        return false;
    }
}
