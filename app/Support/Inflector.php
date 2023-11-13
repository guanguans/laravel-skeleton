<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * @see          https://cakephp.org CakePHP(tm) Project
 * @since         0.2.9
 *
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Support;

/**
 * Pluralize and singularize English words.
 *
 * Inflector pluralizes and singularizes English nouns.
 * Used by CakePHP's naming conventions throughout the framework.
 *
 * @see https://book.cakephp.org/4/en/core-libraries/inflector.html
 */
class Inflector
{
    /**
     * Plural inflector rules
     */
    protected static array $plural = [
        '/(s)tatus$/i' => '\1tatuses',
        '/(quiz)$/i' => '\1zes',
        '/^(ox)$/i' => '\1\2en',
        '/([m|l])ouse$/i' => '\1ice',
        '/(matr|vert)(ix|ex)$/i' => '\1ices',
        '/(x|ch|ss|sh)$/i' => '\1es',
        '/([^aeiouy]|qu)y$/i' => '\1ies',
        '/(hive)$/i' => '\1s',
        '/(chef)$/i' => '\1s',
        '/(?:([^f])fe|([lre])f)$/i' => '\1\2ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '\1a',
        '/(p)erson$/i' => '\1eople',
        '/(?<!u)(m)an$/i' => '\1en',
        '/(c)hild$/i' => '\1hildren',
        '/(buffal|tomat)o$/i' => '\1\2oes',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin)us$/i' => '\1i',
        '/us$/i' => 'uses',
        '/(alias)$/i' => '\1es',
        '/(ax|cris|test)is$/i' => '\1es',
        '/s$/' => 's',
        '/^$/' => '',
        '/$/' => 's',
    ];

    /**
     * Singular inflector rules
     */
    protected static array $singular = [
        '/(s)tatuses$/i' => '\1\2tatus',
        '/^(.*)(menu)s$/i' => '\1\2',
        '/(quiz)zes$/i' => '\\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias)(es)*$/i' => '\1',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
        '/([ftw]ax)es/i' => '\1',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/ouses$/' => 'ouse',
        '/([^a])uses$/' => '\1us',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1\2ovie',
        '/(s)eries$/i' => '\1\2eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/(drive)s$/i' => '\1',
        '/([le])ves$/i' => '\1f',
        '/([^rfoa])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(p)eople$/i' => '\1\2erson',
        '/(m)en$/i' => '\1an',
        '/(c)hildren$/i' => '\1\2hild',
        '/(n)ews$/i' => '\1\2ews',
        '/eaus$/' => 'eau',
        '/^(.*us)$/' => '\\1',
        '/s$/i' => '',
    ];

    /**
     * Irregular rules
     */
    protected static array $irregular = [
        'atlas' => 'atlases',
        'beef' => 'beefs',
        'brief' => 'briefs',
        'brother' => 'brothers',
        'cafe' => 'cafes',
        'child' => 'children',
        'cookie' => 'cookies',
        'corpus' => 'corpuses',
        'cow' => 'cows',
        'criterion' => 'criteria',
        'ganglion' => 'ganglions',
        'genie' => 'genies',
        'genus' => 'genera',
        'graffito' => 'graffiti',
        'hoof' => 'hoofs',
        'loaf' => 'loaves',
        'man' => 'men',
        'money' => 'monies',
        'mongoose' => 'mongooses',
        'move' => 'moves',
        'mythos' => 'mythoi',
        'niche' => 'niches',
        'numen' => 'numina',
        'occiput' => 'occiputs',
        'octopus' => 'octopuses',
        'opus' => 'opuses',
        'ox' => 'oxen',
        'penis' => 'penises',
        'person' => 'people',
        'sex' => 'sexes',
        'soliloquy' => 'soliloquies',
        'testis' => 'testes',
        'trilby' => 'trilbys',
        'turf' => 'turfs',
        'potato' => 'potatoes',
        'hero' => 'heroes',
        'tooth' => 'teeth',
        'goose' => 'geese',
        'foot' => 'feet',
        'foe' => 'foes',
        'sieve' => 'sieves',
        'cache' => 'caches',
    ];

    /**
     * Words that should not be inflected
     */
    protected static array $uninflected = [
        '.*[nrlm]ese', '.*data', '.*deer', '.*fish', '.*measles', '.*ois',
        '.*pox', '.*sheep', 'people', 'feedback', 'stadia', '.*?media',
        'chassis', 'clippers', 'debris', 'diabetes', 'equipment', 'gallows',
        'graffiti', 'headquarters', 'information', 'innings', 'news', 'nexus',
        'pokemon', 'proceedings', 'research', 'sea[- ]bass', 'series', 'species', 'weather',
    ];

    /**
     * Method cache array.
     */
    protected static array $cache = [];

    /**
     * The initial state of Inflector so reset() works.
     */
    protected static array $initialState = [];

    /**
     * Clears Inflectors inflected value caches. And resets the inflection
     * rules to the initial values.
     */
    public static function reset(): void
    {
        if (empty(static::$initialState)) {
            static::$initialState = get_class_vars(self::class);

            return;
        }
        foreach (static::$initialState as $key => $val) {
            if ('_initialState' !== $key) {
                static::${$key} = $val;
            }
        }
    }

    /**
     * Adds custom inflection $rules, of either 'plural', 'singular',
     * 'uninflected' or 'irregular' $type.
     *
     * ### Usage:
     *
     * ```
     * Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
     * Inflector::rules('irregular', ['red' => 'redlings']);
     * Inflector::rules('uninflected', ['dontinflectme']);
     * ```
     *
     * @param  string  $type the type of inflection, either 'plural', 'singular',
     *                     or 'uninflected'
     * @param  array  $rules array of rules to be added
     */
    public static function rules(string $type, array $rules, bool $reset = false): void
    {
        $var = '_'.$type;

        if ($reset) {
            static::${$var} = $rules;
        } elseif ('uninflected' === $type) {
            static::$uninflected = array_merge(
                $rules,
                static::$uninflected
            );
        } else {
            static::${$var} = $rules + static::${$var};
        }

        static::$cache = [];
    }

    /**
     * Return $word in plural form.
     *
     * @param  string  $word Word in singular
     * @return string Word in plural
     *
     * @see https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-plural-singular-forms
     */
    public static function pluralize(string $word): string
    {
        if (isset(static::$cache['pluralize'][$word])) {
            return static::$cache['pluralize'][$word];
        }

        if (! isset(static::$cache['irregular']['pluralize'])) {
            $words = array_keys(static::$irregular);
            static::$cache['irregular']['pluralize'] = '/(.*?(?:\\b|_))('.implode('|', $words).')$/i';

            $upperWords = array_map('ucfirst', $words);
            static::$cache['irregular']['upperPluralize'] = '/(.*?(?:\\b|[a-z]))('.implode('|', $upperWords).')$/';
        }

        if (
            preg_match(static::$cache['irregular']['pluralize'], $word, $regs)
            || preg_match(static::$cache['irregular']['upperPluralize'], $word, $regs)
        ) {
            static::$cache['pluralize'][$word] = $regs[1].substr($regs[2], 0, 1).
                                                 substr(static::$irregular[strtolower($regs[2])], 1);

            return static::$cache['pluralize'][$word];
        }

        if (! isset(static::$cache['uninflected'])) {
            static::$cache['uninflected'] = '/^('.implode('|', static::$uninflected).')$/i';
        }

        if (preg_match(static::$cache['uninflected'], $word, $regs)) {
            static::$cache['pluralize'][$word] = $word;

            return $word;
        }

        foreach (static::$plural as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                static::$cache['pluralize'][$word] = preg_replace($rule, $replacement, $word);

                return static::$cache['pluralize'][$word];
            }
        }

        return $word;
    }

    /**
     * Return $word in singular form.
     *
     * @param  string  $word Word in plural
     * @return string Word in singular
     *
     * @see https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-plural-singular-forms
     */
    public static function singularize(string $word): string
    {
        if (isset(static::$cache['singularize'][$word])) {
            return static::$cache['singularize'][$word];
        }

        if (! isset(static::$cache['irregular']['singular'])) {
            $wordList = array_values(static::$irregular);
            static::$cache['irregular']['singular'] = '/(.*?(?:\\b|_))('.implode('|', $wordList).')$/i';

            $upperWordList = array_map('ucfirst', $wordList);
            static::$cache['irregular']['singularUpper'] = '/(.*?(?:\\b|[a-z]))('.
                                                           implode('|', $upperWordList).
                                                           ')$/';
        }

        if (
            preg_match(static::$cache['irregular']['singular'], $word, $regs)
            || preg_match(static::$cache['irregular']['singularUpper'], $word, $regs)
        ) {
            $suffix = array_search(strtolower($regs[2]), static::$irregular, true);
            $suffix = $suffix ? substr($suffix, 1) : '';
            static::$cache['singularize'][$word] = $regs[1].substr($regs[2], 0, 1).$suffix;

            return static::$cache['singularize'][$word];
        }

        if (! isset(static::$cache['uninflected'])) {
            static::$cache['uninflected'] = '/^('.implode('|', static::$uninflected).')$/i';
        }

        if (preg_match(static::$cache['uninflected'], $word, $regs)) {
            static::$cache['pluralize'][$word] = $word;

            return $word;
        }

        foreach (static::$singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                static::$cache['singularize'][$word] = preg_replace($rule, $replacement, $word);

                return static::$cache['singularize'][$word];
            }
        }
        static::$cache['singularize'][$word] = $word;

        return $word;
    }

    /**
     * Returns the input lower_case_delimited_string as a CamelCasedString.
     *
     * @param  string  $string String to camelize
     * @param  string  $delimiter the delimiter in the input string
     * @return string camelizedStringLikeThis
     *
     * @see https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-camelcase-and-under-scored-forms
     */
    public static function camelize(string $string, string $delimiter = '_'): string
    {
        $cacheKey = __FUNCTION__.$delimiter;

        $result = static::cache($cacheKey, $string);

        if (false === $result) {
            $result = str_replace(' ', '', static::humanize($string, $delimiter));
            static::cache($cacheKey, $string, $result);
        }

        return $result;
    }

    /**
     * Returns the input CamelCasedString as an underscored_string.
     *
     * Also replaces dashes with underscores
     *
     * @param  string  $string CamelCasedString to be "underscorized"
     * @return string underscore_version of the input string
     *
     * @see https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-camelcase-and-under-scored-forms
     */
    public static function underscore(string $string): string
    {
        return static::delimit(str_replace('-', '_', $string), '_');
    }

    /**
     * Returns the input CamelCasedString as an dashed-string.
     *
     * Also replaces underscores with dashes
     *
     * @param  string  $string the string to dasherize
     * @return string Dashed version of the input string
     */
    public static function dasherize(string $string): string
    {
        return static::delimit(str_replace('_', '-', $string), '-');
    }

    /**
     * Returns the input lower_case_delimited_string as 'A Human Readable String'.
     * (Underscores are replaced by spaces and capitalized following words.)
     *
     * @param  string  $string String to be humanized
     * @param  string  $delimiter the character to replace with a space
     * @return string Human-readable string
     *
     * @see https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-human-readable-forms
     */
    public static function humanize(string $string, string $delimiter = '_'): string
    {
        $cacheKey = __FUNCTION__.$delimiter;

        $result = static::cache($cacheKey, $string);

        if (false === $result) {
            $result = explode(' ', str_replace($delimiter, ' ', $string));
            foreach ($result as &$word) {
                $word = mb_strtoupper(mb_substr($word, 0, 1)).mb_substr($word, 1);
            }
            $result = implode(' ', $result);
            static::cache($cacheKey, $string, $result);
        }

        return $result;
    }

    /**
     * Expects a CamelCasedInputString, and produces a lower_case_delimited_string
     *
     * @param  string  $string String to delimit
     * @param  string  $delimiter the character to use as a delimiter
     * @return string delimited string
     */
    public static function delimit(string $string, string $delimiter = '_'): string
    {
        $cacheKey = __FUNCTION__.$delimiter;

        $result = static::cache($cacheKey, $string);

        if (false === $result) {
            $result = mb_strtolower(preg_replace('/(?<=\\w)([A-Z])/', $delimiter.'\\1', $string));
            static::cache($cacheKey, $string, $result);
        }

        return $result;
    }

    /**
     * Returns corresponding table name for given model $className. ("people" for the model class "Person").
     *
     * @param  string  $className Name of class to get database table name for
     * @return string Name of the database table for given class
     *
     * @see https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-table-and-class-name-forms
     */
    public static function tableize(string $className): string
    {
        $result = static::cache(__FUNCTION__, $className);

        if (false === $result) {
            $result = static::pluralize(static::underscore($className));
            static::cache(__FUNCTION__, $className, $result);
        }

        return $result;
    }

    /**
     * Returns Cake model class name ("Person" for the database table "people".) for given database table.
     *
     * @param  string  $tableName Name of database table to get class name for
     * @return string Class name
     *
     * @see https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-table-and-class-name-forms
     */
    public static function classify(string $tableName): string
    {
        $result = static::cache(__FUNCTION__, $tableName);

        if (false === $result) {
            $result = static::camelize(static::singularize($tableName));
            static::cache(__FUNCTION__, $tableName, $result);
        }

        return $result;
    }

    /**
     * Returns camelBacked version of an underscored string.
     *
     * @param  string  $string string to convert
     * @return string in variable form
     *
     * @see https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-variable-names
     */
    public static function variable(string $string): string
    {
        $result = static::cache(__FUNCTION__, $string);

        if (false === $result) {
            $camelized = static::camelize(static::underscore($string));
            $replace = strtolower(substr($camelized, 0, 1));
            $result = $replace.substr($camelized, 1);
            static::cache(__FUNCTION__, $string, $result);
        }

        return $result;
    }

    /**
     * Cache inflected values, and return if already available
     *
     * @param  string  $type Inflection type
     * @param  string  $key Original value
     * @param  false|string  $value Inflected value
     * @return false|string inflected value on cache hit or false on cache miss
     */
    protected static function cache(string $type, string $key, bool|string $value = false): bool|string
    {
        $key = '_'.$key;
        $type = '_'.$type;
        if (false !== $value) {
            static::$cache[$type][$key] = $value;

            return $value;
        }
        if (! isset(static::$cache[$type][$key])) {
            return false;
        }

        return static::$cache[$type][$key];
    }
}
