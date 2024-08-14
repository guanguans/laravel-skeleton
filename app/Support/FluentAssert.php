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

use Webmozart\Assert\Assert;

/**
 * @method self string($message = '')
 * @method self stringNotEmpty($message = '')
 * @method self integer($message = '')
 * @method self integerish($message = '')
 * @method self positiveInteger($message = '')
 * @method self float($message = '')
 * @method self numeric($message = '')
 * @method self natural($message = '')
 * @method self boolean($message = '')
 * @method self scalar($message = '')
 * @method self object($message = '')
 * @method self resource($type = null, $message = '')
 * @method self isCallable($message = '')
 * @method self isArray($message = '')
 * @method self isTraversable($message = '')
 * @method self isArrayAccessible($message = '')
 * @method self isCountable($message = '')
 * @method self isIterable($message = '')
 * @method self isInstanceOf($class, $message = '')
 * @method self notInstanceOf($class, $message = '')
 * @method self isInstanceOfAny(array $classes, $message = '')
 * @method self isAOf($class, $message = '')
 * @method self isNotA($class, $message = '')
 * @method self isAnyOf(array $classes, $message = '')
 * @method self isEmpty($message = '')
 * @method self notEmpty($message = '')
 * @method self null($message = '')
 * @method self notNull($message = '')
 * @method self true($message = '')
 * @method self false($message = '')
 * @method self notFalse($message = '')
 * @method self ip($message = '')
 * @method self ipv4($message = '')
 * @method self ipv6($message = '')
 * @method self email($message = '')
 * @method self uniqueValues($message = '')
 * @method self eq($expect, $message = '')
 * @method self notEq($expect, $message = '')
 * @method self same($expect, $message = '')
 * @method self notSame($expect, $message = '')
 * @method self greaterThan($limit, $message = '')
 * @method self greaterThanEq($limit, $message = '')
 * @method self lessThan($limit, $message = '')
 * @method self lessThanEq($limit, $message = '')
 * @method self range($min, $max, $message = '')
 * @method self oneOf(array $values, $message = '')
 * @method self inArray(array $values, $message = '')
 * @method self contains($subString, $message = '')
 * @method self notContains($subString, $message = '')
 * @method self notWhitespaceOnly($message = '')
 * @method self startsWith($prefix, $message = '')
 * @method self notStartsWith($prefix, $message = '')
 * @method self startsWithLetter($message = '')
 * @method self endsWith($suffix, $message = '')
 * @method self notEndsWith($suffix, $message = '')
 * @method self regex($pattern, $message = '')
 * @method self notRegex($pattern, $message = '')
 * @method self unicodeLetters($message = '')
 * @method self alpha($message = '')
 * @method self digits($message = '')
 * @method self alnum($message = '')
 * @method self lower($message = '')
 * @method self upper($message = '')
 * @method self length($length, $message = '')
 * @method self minLength($min, $message = '')
 * @method self maxLength($max, $message = '')
 * @method self lengthBetween($min, $max, $message = '')
 * @method self fileExists($message = '')
 * @method self file($message = '')
 * @method self directory($message = '')
 * @method self readable($message = '')
 * @method self writable($message = '')
 * @method self classExists($message = '')
 * @method self subclassOf($class, $message = '')
 * @method self interfaceExists($message = '')
 * @method self implementsInterface($interface, $message = '')
 * @method self propertyExists($property, $message = '')
 * @method self propertyNotExists($property, $message = '')
 * @method self methodExists($method, $message = '')
 * @method self methodNotExists($method, $message = '')
 * @method self keyExists($key, $message = '')
 * @method self keyNotExists($key, $message = '')
 * @method self validArrayKey($message = '')
 * @method self count($number, $message = '')
 * @method self minCount($min, $message = '')
 * @method self maxCount($max, $message = '')
 * @method self countBetween($min, $max, $message = '')
 * @method self isList($message = '')
 * @method self isNonEmptyList($message = '')
 * @method self isMap($message = '')
 * @method self isNonEmptyMap($message = '')
 * @method self uuid($message = '')
 * @method self throws($class = 'Exception', $message = '')
 * @method self nullOrString($message = '')
 * @method self allString($message = '')
 * @method self allNullOrString($message = '')
 * @method self nullOrStringNotEmpty($message = '')
 * @method self allStringNotEmpty($message = '')
 * @method self allNullOrStringNotEmpty($message = '')
 * @method self nullOrInteger($message = '')
 * @method self allInteger($message = '')
 * @method self allNullOrInteger($message = '')
 * @method self nullOrIntegerish($message = '')
 * @method self allIntegerish($message = '')
 * @method self allNullOrIntegerish($message = '')
 * @method self nullOrPositiveInteger($message = '')
 * @method self allPositiveInteger($message = '')
 * @method self allNullOrPositiveInteger($message = '')
 * @method self nullOrFloat($message = '')
 * @method self allFloat($message = '')
 * @method self allNullOrFloat($message = '')
 * @method self nullOrNumeric($message = '')
 * @method self allNumeric($message = '')
 * @method self allNullOrNumeric($message = '')
 * @method self nullOrNatural($message = '')
 * @method self allNatural($message = '')
 * @method self allNullOrNatural($message = '')
 * @method self nullOrBoolean($message = '')
 * @method self allBoolean($message = '')
 * @method self allNullOrBoolean($message = '')
 * @method self nullOrScalar($message = '')
 * @method self allScalar($message = '')
 * @method self allNullOrScalar($message = '')
 * @method self nullOrObject($message = '')
 * @method self allObject($message = '')
 * @method self allNullOrObject($message = '')
 * @method self nullOrResource($type = null, $message = '')
 * @method self allResource($type = null, $message = '')
 * @method self allNullOrResource($type = null, $message = '')
 * @method self nullOrIsCallable($message = '')
 * @method self allIsCallable($message = '')
 * @method self allNullOrIsCallable($message = '')
 * @method self nullOrIsArray($message = '')
 * @method self allIsArray($message = '')
 * @method self allNullOrIsArray($message = '')
 * @method self nullOrIsTraversable($message = '')
 * @method self allIsTraversable($message = '')
 * @method self allNullOrIsTraversable($message = '')
 * @method self nullOrIsArrayAccessible($message = '')
 * @method self allIsArrayAccessible($message = '')
 * @method self allNullOrIsArrayAccessible($message = '')
 * @method self nullOrIsCountable($message = '')
 * @method self allIsCountable($message = '')
 * @method self allNullOrIsCountable($message = '')
 * @method self nullOrIsIterable($message = '')
 * @method self allIsIterable($message = '')
 * @method self allNullOrIsIterable($message = '')
 * @method self nullOrIsInstanceOf($class, $message = '')
 * @method self allIsInstanceOf($class, $message = '')
 * @method self allNullOrIsInstanceOf($class, $message = '')
 * @method self nullOrNotInstanceOf($class, $message = '')
 * @method self allNotInstanceOf($class, $message = '')
 * @method self allNullOrNotInstanceOf($class, $message = '')
 * @method self nullOrIsInstanceOfAny($classes, $message = '')
 * @method self allIsInstanceOfAny($classes, $message = '')
 * @method self allNullOrIsInstanceOfAny($classes, $message = '')
 * @method self nullOrIsAOf($class, $message = '')
 * @method self allIsAOf($class, $message = '')
 * @method self allNullOrIsAOf($class, $message = '')
 * @method self nullOrIsNotA($class, $message = '')
 * @method self allIsNotA($class, $message = '')
 * @method self allNullOrIsNotA($class, $message = '')
 * @method self nullOrIsAnyOf($classes, $message = '')
 * @method self allIsAnyOf($classes, $message = '')
 * @method self allNullOrIsAnyOf($classes, $message = '')
 * @method self nullOrIsEmpty($message = '')
 * @method self allIsEmpty($message = '')
 * @method self allNullOrIsEmpty($message = '')
 * @method self nullOrNotEmpty($message = '')
 * @method self allNotEmpty($message = '')
 * @method self allNullOrNotEmpty($message = '')
 * @method self allNull($message = '')
 * @method self allNotNull($message = '')
 * @method self nullOrTrue($message = '')
 * @method self allTrue($message = '')
 * @method self allNullOrTrue($message = '')
 * @method self nullOrFalse($message = '')
 * @method self allFalse($message = '')
 * @method self allNullOrFalse($message = '')
 * @method self nullOrNotFalse($message = '')
 * @method self allNotFalse($message = '')
 * @method self allNullOrNotFalse($message = '')
 * @method self nullOrIp($message = '')
 * @method self allIp($message = '')
 * @method self allNullOrIp($message = '')
 * @method self nullOrIpv4($message = '')
 * @method self allIpv4($message = '')
 * @method self allNullOrIpv4($message = '')
 * @method self nullOrIpv6($message = '')
 * @method self allIpv6($message = '')
 * @method self allNullOrIpv6($message = '')
 * @method self nullOrEmail($message = '')
 * @method self allEmail($message = '')
 * @method self allNullOrEmail($message = '')
 * @method self nullOrUniqueValues($message = '')
 * @method self allUniqueValues($message = '')
 * @method self allNullOrUniqueValues($message = '')
 * @method self nullOrEq($expect, $message = '')
 * @method self allEq($expect, $message = '')
 * @method self allNullOrEq($expect, $message = '')
 * @method self nullOrNotEq($expect, $message = '')
 * @method self allNotEq($expect, $message = '')
 * @method self allNullOrNotEq($expect, $message = '')
 * @method self nullOrSame($expect, $message = '')
 * @method self allSame($expect, $message = '')
 * @method self allNullOrSame($expect, $message = '')
 * @method self nullOrNotSame($expect, $message = '')
 * @method self allNotSame($expect, $message = '')
 * @method self allNullOrNotSame($expect, $message = '')
 * @method self nullOrGreaterThan($limit, $message = '')
 * @method self allGreaterThan($limit, $message = '')
 * @method self allNullOrGreaterThan($limit, $message = '')
 * @method self nullOrGreaterThanEq($limit, $message = '')
 * @method self allGreaterThanEq($limit, $message = '')
 * @method self allNullOrGreaterThanEq($limit, $message = '')
 * @method self nullOrLessThan($limit, $message = '')
 * @method self allLessThan($limit, $message = '')
 * @method self allNullOrLessThan($limit, $message = '')
 * @method self nullOrLessThanEq($limit, $message = '')
 * @method self allLessThanEq($limit, $message = '')
 * @method self allNullOrLessThanEq($limit, $message = '')
 * @method self nullOrRange($min, $max, $message = '')
 * @method self allRange($min, $max, $message = '')
 * @method self allNullOrRange($min, $max, $message = '')
 * @method self nullOrOneOf($values, $message = '')
 * @method self allOneOf($values, $message = '')
 * @method self allNullOrOneOf($values, $message = '')
 * @method self nullOrInArray($values, $message = '')
 * @method self allInArray($values, $message = '')
 * @method self allNullOrInArray($values, $message = '')
 * @method self nullOrContains($subString, $message = '')
 * @method self allContains($subString, $message = '')
 * @method self allNullOrContains($subString, $message = '')
 * @method self nullOrNotContains($subString, $message = '')
 * @method self allNotContains($subString, $message = '')
 * @method self allNullOrNotContains($subString, $message = '')
 * @method self nullOrNotWhitespaceOnly($message = '')
 * @method self allNotWhitespaceOnly($message = '')
 * @method self allNullOrNotWhitespaceOnly($message = '')
 * @method self nullOrStartsWith($prefix, $message = '')
 * @method self allStartsWith($prefix, $message = '')
 * @method self allNullOrStartsWith($prefix, $message = '')
 * @method self nullOrNotStartsWith($prefix, $message = '')
 * @method self allNotStartsWith($prefix, $message = '')
 * @method self allNullOrNotStartsWith($prefix, $message = '')
 * @method self nullOrStartsWithLetter($message = '')
 * @method self allStartsWithLetter($message = '')
 * @method self allNullOrStartsWithLetter($message = '')
 * @method self nullOrEndsWith($suffix, $message = '')
 * @method self allEndsWith($suffix, $message = '')
 * @method self allNullOrEndsWith($suffix, $message = '')
 * @method self nullOrNotEndsWith($suffix, $message = '')
 * @method self allNotEndsWith($suffix, $message = '')
 * @method self allNullOrNotEndsWith($suffix, $message = '')
 * @method self nullOrRegex($pattern, $message = '')
 * @method self allRegex($pattern, $message = '')
 * @method self allNullOrRegex($pattern, $message = '')
 * @method self nullOrNotRegex($pattern, $message = '')
 * @method self allNotRegex($pattern, $message = '')
 * @method self allNullOrNotRegex($pattern, $message = '')
 * @method self nullOrUnicodeLetters($message = '')
 * @method self allUnicodeLetters($message = '')
 * @method self allNullOrUnicodeLetters($message = '')
 * @method self nullOrAlpha($message = '')
 * @method self allAlpha($message = '')
 * @method self allNullOrAlpha($message = '')
 * @method self nullOrDigits($message = '')
 * @method self allDigits($message = '')
 * @method self allNullOrDigits($message = '')
 * @method self nullOrAlnum($message = '')
 * @method self allAlnum($message = '')
 * @method self allNullOrAlnum($message = '')
 * @method self nullOrLower($message = '')
 * @method self allLower($message = '')
 * @method self allNullOrLower($message = '')
 * @method self nullOrUpper($message = '')
 * @method self allUpper($message = '')
 * @method self allNullOrUpper($message = '')
 * @method self nullOrLength($length, $message = '')
 * @method self allLength($length, $message = '')
 * @method self allNullOrLength($length, $message = '')
 * @method self nullOrMinLength($min, $message = '')
 * @method self allMinLength($min, $message = '')
 * @method self allNullOrMinLength($min, $message = '')
 * @method self nullOrMaxLength($max, $message = '')
 * @method self allMaxLength($max, $message = '')
 * @method self allNullOrMaxLength($max, $message = '')
 * @method self nullOrLengthBetween($min, $max, $message = '')
 * @method self allLengthBetween($min, $max, $message = '')
 * @method self allNullOrLengthBetween($min, $max, $message = '')
 * @method self nullOrFileExists($message = '')
 * @method self allFileExists($message = '')
 * @method self allNullOrFileExists($message = '')
 * @method self nullOrFile($message = '')
 * @method self allFile($message = '')
 * @method self allNullOrFile($message = '')
 * @method self nullOrDirectory($message = '')
 * @method self allDirectory($message = '')
 * @method self allNullOrDirectory($message = '')
 * @method self nullOrReadable($message = '')
 * @method self allReadable($message = '')
 * @method self allNullOrReadable($message = '')
 * @method self nullOrWritable($message = '')
 * @method self allWritable($message = '')
 * @method self allNullOrWritable($message = '')
 * @method self nullOrClassExists($message = '')
 * @method self allClassExists($message = '')
 * @method self allNullOrClassExists($message = '')
 * @method self nullOrSubclassOf($class, $message = '')
 * @method self allSubclassOf($class, $message = '')
 * @method self allNullOrSubclassOf($class, $message = '')
 * @method self nullOrInterfaceExists($message = '')
 * @method self allInterfaceExists($message = '')
 * @method self allNullOrInterfaceExists($message = '')
 * @method self nullOrImplementsInterface($interface, $message = '')
 * @method self allImplementsInterface($interface, $message = '')
 * @method self allNullOrImplementsInterface($interface, $message = '')
 * @method self nullOrPropertyExists($property, $message = '')
 * @method self allPropertyExists($property, $message = '')
 * @method self allNullOrPropertyExists($property, $message = '')
 * @method self nullOrPropertyNotExists($property, $message = '')
 * @method self allPropertyNotExists($property, $message = '')
 * @method self allNullOrPropertyNotExists($property, $message = '')
 * @method self nullOrMethodExists($method, $message = '')
 * @method self allMethodExists($method, $message = '')
 * @method self allNullOrMethodExists($method, $message = '')
 * @method self nullOrMethodNotExists($method, $message = '')
 * @method self allMethodNotExists($method, $message = '')
 * @method self allNullOrMethodNotExists($method, $message = '')
 * @method self nullOrKeyExists($key, $message = '')
 * @method self allKeyExists($key, $message = '')
 * @method self allNullOrKeyExists($key, $message = '')
 * @method self nullOrKeyNotExists($key, $message = '')
 * @method self allKeyNotExists($key, $message = '')
 * @method self allNullOrKeyNotExists($key, $message = '')
 * @method self nullOrValidArrayKey($message = '')
 * @method self allValidArrayKey($message = '')
 * @method self allNullOrValidArrayKey($message = '')
 * @method self nullOrCount($number, $message = '')
 * @method self allCount($number, $message = '')
 * @method self allNullOrCount($number, $message = '')
 * @method self nullOrMinCount($min, $message = '')
 * @method self allMinCount($min, $message = '')
 * @method self allNullOrMinCount($min, $message = '')
 * @method self nullOrMaxCount($max, $message = '')
 * @method self allMaxCount($max, $message = '')
 * @method self allNullOrMaxCount($max, $message = '')
 * @method self nullOrCountBetween($min, $max, $message = '')
 * @method self allCountBetween($min, $max, $message = '')
 * @method self allNullOrCountBetween($min, $max, $message = '')
 * @method self nullOrIsList($message = '')
 * @method self allIsList($message = '')
 * @method self allNullOrIsList($message = '')
 * @method self nullOrIsNonEmptyList($message = '')
 * @method self allIsNonEmptyList($message = '')
 * @method self allNullOrIsNonEmptyList($message = '')
 * @method self nullOrIsMap($message = '')
 * @method self allIsMap($message = '')
 * @method self allNullOrIsMap($message = '')
 * @method self nullOrIsNonEmptyMap($message = '')
 * @method self allIsNonEmptyMap($message = '')
 * @method self allNullOrIsNonEmptyMap($message = '')
 * @method self nullOrUuid($message = '')
 * @method self allUuid($message = '')
 * @method self allNullOrUuid($message = '')
 * @method self nullOrThrows($class = 'Exception', $message = '')
 * @method self allThrows($class = 'Exception', $message = '')
 * @method self allNullOrThrows($class = 'Exception', $message = '')
 *
 * @mixin \Webmozart\Assert\Assert
 */
class FluentAssert
{
    public function __construct(protected $value) {}

    public static function __callStatic($name, $arguments): self
    {
        \call_user_func_array([Assert::class, $name], $arguments);

        return self::create($arguments[0]);
    }

    public function __call($name, $arguments): self
    {
        array_unshift($arguments, $this->value);

        \call_user_func_array([Assert::class, $name], $arguments);

        return $this;
    }

    public static function create($value): self
    {
        return new self($value);
    }

    /**
     * @internal this method is not part of this class and should not be used directly
     *
     * @throws \ReflectionException
     *
     * @noinspection DebugFunctionUsageInspection
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    final public static function updateDocComment(): void
    {
        $methods = (new \ReflectionClass(Assert::class))->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods = array_filter(
            $methods,
            static fn (\ReflectionMethod $method): bool => $method->isStatic() && ! str_starts_with($method->getName(), '__')
        );

        $rawDocComment = array_map(static function (\ReflectionMethod $method): string {
            $parameters = $method->getParameters();
            unset($parameters[0]);

            $arguments = array_map(static function (\ReflectionParameter $parameter): string {
                $type = ($type = $parameter->getType()?->getName()) ? "$type " : '';

                $defaultValue = static function ($value): string {
                    if (null === $value) {
                        return 'null';
                    }

                    if ([] === $value) {
                        return '[]';
                    }

                    return var_export($value, true);
                };

                return $parameter->isDefaultValueAvailable()
                    ? \sprintf('%s$%s = %s', $type, $parameter->getName(), $defaultValue($parameter->getDefaultValue()))
                    : \sprintf('%s$%s', $type, $parameter->getName());
            }, $parameters);

            return \sprintf(' * @method self %s(%s)', $method->getName(), implode(', ', $arguments));
        }, $methods);

        $docComment = \sprintf(
            <<<'docComment'
                /**
                %s
                 *
                 * @mixin \Webmozart\Assert\Assert
                 */
                docComment
            ,
            implode(PHP_EOL, $rawDocComment)
        );

        file_put_contents(
            __FILE__,
            str_replace((new \ReflectionClass(static::class))->getDocComment(), $docComment, file_get_contents(__FILE__))
        );

        echo 'Done.';

        exit(0);
    }
}
