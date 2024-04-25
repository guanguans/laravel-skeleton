<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Collection;

/**
 * Simple Node model trait.
 *
 * Simple tree implementation, for advanced implementation see:
 * October\Rain\Database\Traits\NestedNode
 *
 * SimpleNode is the bare minimum needed for tree functionality, the
 * methods defined here should be implemented by all "tree" traits.
 *
 * Usage:
 *
 * Model table must have parent_id table column.
 * In the model class definition:
 *
 *   use \October\Rain\Database\Traits\SimpleNode;
 *
 * General access methods:
 *
 *   $model->getChildren(); // Returns children of this node
 *   $model->getChildCount(); // Returns number of all children.
 *   $model->getAllChildren(); // Returns all children of this node
 *   $model->getAllRoot(); // Returns all root level nodes (eager loaded)
 *   $model->getAll(); // Returns everything in correct order.
 *
 * Query builder methods:
 *
 *   $query->listsNested(); // Returns an indented array of key and value columns.
 *
 * You can change the sort field used by declaring:
 *
 *   const PARENT_ID = 'my_parent_column';
 *
 * @see https://github.com/octobercms/library/blob/3.x/src/Database/Traits/Nullable.php
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait SimpleNode
{
    public function getRoot($parentCode = null)
    {
        if ($this->parent) {
            if ($this->parent->code === $parentCode) {
                return $this;
            }

            return $this->parent->getRoot($parentCode);
        }

        return $this;
    }

    /**
     * Get the parent that owns the node.
     */
    public function parent()
    {
        return $this->belongsTo(static::class, $this->getParentColumnName());
    }

    /**
     * Get the children belongs to the parent node.
     */
    public function children()
    {
        $orderBy = 'id';

        if (property_exists($this, 'orderField')) {
            $orderBy = $this->orderField;
        }

        return $this->hasMany(static::class, $this->getParentColumnName())->orderBy($orderBy);
    }

    public function isAChild(): bool
    {
        return ! empty($this->{$this->getParentColumnName()});
    }

    /**
     * Returns all nodes and children.
     */
    public function getAll(): Collection
    {
        $collection = [];

        foreach ($this->getAllRoot() as $rootNode) {
            $collection[] = $rootNode;
            $collection += $rootNode->getAllChildren()->getDictionary();
        }

        return new Collection($collection);
    }

    /**
     * Returns a list of all root nodes, eager loaded.
     */
    public function getAllRoot(): mixed
    {
        return $this->get()->toNested();
    }

    public function scopeRoot($query)
    {
        return $query->where($this->getParentColumnName(), 0);
    }

    public function isRoot(): bool
    {
        return $this->{$this->getParentColumnName()} === 0;
    }

    /**
     * @param  null  $status
     */
    public function getAllChildren($status = null): Collection
    {
        $result = [];
        $children = $this->getChildren($status);

        foreach ($children as $child) {
            $result[] = $child;

            $childResult = $child->getAllChildren($status);

            foreach ($childResult as $subChild) {
                $result[] = $subChild;
            }
        }

        return new Collection($result);
    }

    /**
     * @param  null  $status
     */
    public function getChildren($status = null): mixed
    {
        if ($status) {
            return $this->children()->where('status', $status)->get();
        }

        return $this->children;
    }

    /**
     * @param  null  $status
     */
    public function hasChildren($status = null): bool
    {
        return \count($this->getAllChildren($status)) > 0;
    }

    /**
     * Returns number of all children below it.
     */
    public function getChildCount(): int
    {
        return \count($this->getAllChildren());
    }

    /**
     * Gets an array with values of a given column. Values are indented according to their depth.
     *
     * @param  string  $column  Array values
     * @param  string  $key  Array keys
     * @param  string  $indent  Character to indent depth
     */
    public function scopeListsNested(mixed $query, string $column, ?string $key = null, string $indent = '&nbsp;&nbsp;&nbsp;'): array
    {
        // Recursive helper function
        $buildCollection = static function ($items, $depth = 0) use (&$buildCollection, $column, $key, $indent): array {
            $result = [];

            $indentString = str_repeat($indent, $depth);

            foreach ($items as $item) {
                if ($key !== null) {
                    $result[$item->{$key}] = $indentString.$item->{$column};
                } else {
                    $result[] = $indentString.$item->{$column};
                }

                // Add the children
                $childItems = $item->getChildren();

                if ($childItems->count() > 0) {
                    $result += $buildCollection($childItems, $depth + 1);
                }
            }

            return $result;
        };

        // Build a nested collection
        $rootItems = $query->get()->toNested();

        return $buildCollection($rootItems);
    }

    /**
     * Get parent column name.
     */
    public function getParentColumnName(): string
    {
        /** @noinspection PhpUndefinedClassConstantInspection */
        return \defined('static::PARENT_ID') ? static::PARENT_ID : 'parent_id';
    }

    /**
     * Get fully qualified parent column name.
     */
    public function getQualifiedParentColumnName(): string
    {
        return $this->getTable().'.'.$this->getParentColumnName();
    }

    /**
     * Get value of the model parent_id column.
     */
    public function getParentId(): int
    {
        return $this->getAttribute($this->getParentColumnName());
    }
}
