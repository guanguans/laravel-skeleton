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
$var_name;
$object::$property_name;
class Foo{public int $property_name;}
$object->method_name();
$object->property_name;
Foo::method_name();
class Foo{public function method_name(){}}
class class_name{}
class Foo{public $property_name;}

\functionName();
function functionName(){}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
$varName;
$object::$propertyName;
class Foo{public int $propertyName;}
$object->methodName();
$object->propertyName;
Foo::methodName();
class Foo{public function methodName(){}}
class ClassName{}
class Foo{public $propertyName;}

\function_name();
function function_name(){}
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
            Node\Expr\Variable::class,
            Node\Identifier::class,
            Node\VarLikeIdentifier::class,
        ];
    }

    /**
     * @param  Node\Expr\Variable|Node\Identifier|Node\VarLikeIdentifier|Name|Name\FullyQualified|Name\Relative  $node
     */
    public function refactor(Node $node)
    {
        if ($this->shouldCamelizedName($node)) {
            return $this->rename($node, static fn (string $name): string => Str::camel($name));
        }

        if ($this->shouldSerpentinizedLowerName($node)) {
            return $this->rename($node, static fn (string $name): string => Str::lower(Str::snake($name)));
        }

        // if ($this->shouldSerpentinizedUpperName($node)) {
        //     return $this->rename($node, static fn (string $name): string => Str::upper(Str::snake($name)));
        // }

        return null;
    }

    /**
     * @param  Node\Expr\Variable|Node\Identifier|Node\VarLikeIdentifier|Name|Name\FullyQualified|Name\Relative  $node
     */
    protected function rename(Node $node, callable $callback): Node
    {
        if (property_exists($node, 'parts')) {
            $node->parts[count($node->parts) - 1] = $callback($node->getLast());

            return $node;
        }

        if (property_exists($node, 'name')) {
            if (is_string($node->name)) {
                $node->name = $callback($node->name);

                return $node;
            }
        }

        return $node;
    }

    /**
     * @param  Node\Expr\Variable|Node\Identifier|Node\VarLikeIdentifier|Name|Name\FullyQualified|Name\Relative  $node
     */
    protected function shouldCamelizedName(Node $node): bool
    {
        // $varName
        if ($node instanceof Node\Expr\Variable) {
            return true;
        }

        $parent = $node->getAttribute('parent');
        if ($node instanceof Node\VarLikeIdentifier) {
            $classes = [
                // $object::$propertyName
                Node\Expr\StaticPropertyFetch::class,
                // class Class{public int $propertyName;}
                Node\Stmt\PropertyProperty::class,
            ];

            foreach ($classes as $class) {
                if ($parent instanceof $class) {
                    return true;
                }
            }
        }

        if ($node instanceof Node\Identifier) {
            $classes = [
                // $object->methodName()
                Node\Expr\MethodCall::class,
                // $object->propertyName
                Node\Expr\PropertyFetch::class,
                // Class::methodName()
                Node\Expr\StaticCall::class,
                // class Class{public function methodName(){}}
                Node\Stmt\ClassMethod::class,
                // class ClassName{}
                Node\Stmt\Class_::class,
                // class Class{public $propertyName;}
                Node\Stmt\Property::class,
            ];

            foreach ($classes as $class) {
                if ($parent instanceof $class) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  Node\Expr\Variable|Node\Identifier|Node\VarLikeIdentifier|Name|Name\FullyQualified|Name\Relative  $node
     */
    protected function shouldSerpentinizedLowerName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        // \function_name()
        if ($node instanceof Name && $parent instanceof Node\Expr\FuncCall) {
            return true;
        }

        if ($node instanceof Node\Identifier) {
            $classes = [
                // function function_name(){}
                Node\Stmt\Function_::class,

            ];

            foreach ($classes as $class) {
                if ($parent instanceof $class) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  Node\Expr\Variable|Node\Identifier|Node\VarLikeIdentifier|Name|Name\FullyQualified|Name\Relative  $node
     */
    protected function shouldSerpentinizedUpperName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        // CONST_NAME
        if ($node instanceof Name && $parent instanceof Node\Expr\ConstFetch) {
            if (! in_array($node->getLast(), ['null', 'true', 'false', 'class'], true)) {
                return true;
            }
        }

        if ($node instanceof Node\Identifier) {
            $classes = [
                // Class::CONST_NAME
                Node\Expr\ClassConstFetch::class,

            ];

            foreach ($classes as $class) {
                if ($parent instanceof $class) {
                    return true;
                }
            }
        }

        return false;
    }
}
