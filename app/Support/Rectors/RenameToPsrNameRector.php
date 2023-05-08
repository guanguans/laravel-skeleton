<?php

namespace App\Support\Rectors;

use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\Node\Name;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameToPsrNameRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Rename to psr name',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
function functionName(){}
functionName();

class class_name{}
class_name::CONST;
class_name::$property;
class_name::method();

class Foo{public const const_name = 'const';}
Foo::const_name;
define('const_name', 'const');
const_name;

$var_name;
class Foo{public $property_name;}
class Foo{public int $property_name;}
class Foo{public function method_name(){}}
$object->property_name;
Foo::$property_name;
$object->method_name();
Foo::method_name();
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
function function_name(){}
function_name();

class ClassName{}
ClassName::CONST;
ClassName::$property;
ClassName::method();

class Foo{public const CONST_NAME = 'const';}
Foo::CONST_NAME;
define('CONST_NAME', 'const');
CONST_NAME;

$varName;
class Foo{public $propertyName;}
class Foo{public int $propertyName;}
class Foo{public function methodName(){}}
$object->propertyName;
Foo::$propertyName;
$object->methodName();
Foo::methodName();
CODE_SAMPLE
                ),
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Name::class,
            Node\Expr\FuncCall::class,
            Node\Expr\Variable::class,
            Node\Identifier::class,
        ];
    }

    /**
     * @param  Name|Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier  $node
     */
    public function refactor(Node $node)
    {
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

        return null;
    }

    /**
     * @param  Name|Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier  $node
     */
    protected function rename(Node $node, callable $callback): Node
    {
        $preprocessor = static function (string $value): string {
            if (ctype_upper(preg_replace('/[^a-zA-Z]/', '', $value))) {
                return mb_strtolower($value, 'UTF-8');
            }

            return $value;
        };

        if ($node instanceof Name) {
            $node->parts[count($node->parts) - 1] = $callback($preprocessor($node->getLast()));

            return $node;
        }

        if ($this->isSubclasses($node, [
            Node\Expr\Variable::class,
            Node\Identifier::class,
        ])) {
            $node->name = $callback($preprocessor($node->name));

            return $node;
        }

        if ($node instanceof Node\Expr\FuncCall) {
            if ($this->isName($node, 'define') && isset($node->args[0])) {
                $node->args[0]->value->value = $callback($preprocessor($node->args[0]->value->value));

                return $node;
            }
        }

        return $node;
    }

    /**
     * @param  Name|Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier  $node
     */
    protected function shouldLowerSnakeName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        if ($node instanceof Node\Identifier) {
            if ($this->isSubclasses($parent, [
                // function function_name(){}
                Node\Stmt\Function_::class,
            ])) {
                return true;
            }
        }

        if ($node instanceof Node\Name) {
            if ($this->isSubclasses($parent, [
                // function_name();
                Node\Expr\FuncCall::class,
            ])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Name|Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier  $node
     */
    protected function shouldUcfirstCamelName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        if ($node instanceof Node\Identifier) {
            if ($this->isSubclasses($parent, [
                // class ClassName{}
                Node\Stmt\Class_::class,
            ])) {
                return true;
            }
        }

        if ($node instanceof Node\Name && ! $this->isNames($node, ['stdClass'])) {
            if ($this->isSubclasses($parent, [
                // ClassName::CONST;
                Node\Expr\ClassConstFetch::class,
                // ClassName::$property;
                Node\Expr\StaticPropertyFetch::class,
                // ClassName::method();
                Node\Expr\StaticCall::class,
            ])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Name|Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier  $node
     */
    protected function shouldUpperSnakeName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        if ($node instanceof Node\Identifier && ! $this->isNames($node, ['class'])) {
            if ($this->isSubclasses($parent, [
                // class Foo{public const CONST_NAME = 'const';}
                Node\Const_::class,
                // Foo::CONST_NAME;
                Node\Expr\ClassConstFetch::class,
            ])) {
                return true;
            }
        }

        if ($node instanceof Node\Expr\FuncCall && $this->isName($node, 'define')) {
            // define('CONST_NAME', 'const');
            return true;
        }

        if ($node instanceof Name && ! $this->isNames($node, ['null', 'true', 'false'])) {
            if ($this->isSubclasses($parent, [
                // CONST_NAME;
                Node\Expr\ConstFetch::class,
            ])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Name|Node\Expr\FuncCall|Node\Expr\Variable|Node\Identifier  $node
     */
    protected function shouldLcfirstCamelName(Node $node): bool
    {
        // $varName;
        if ($node instanceof Node\Expr\Variable) {
            return true;
        }

        $parent = $node->getAttribute('parent');
        if ($node instanceof Node\Identifier) {
            if ($this->isSubclasses($parent, [
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
            ])) {
                return true;
            }
        }

        return false;
    }

    protected function isSubclasses($object, array $classes): bool
    {
        if (! is_object($object)) {
            return false;
        }

        foreach ($classes as $class) {
            if ($object instanceof $class) {
                return true;
            }
        }

        return false;
    }
}
