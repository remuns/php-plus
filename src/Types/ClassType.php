<?php

namespace PhpPlus\Core\Types;

use ReflectionClass;

/**
 * Represents a standard PHP class type definition.
 */
final class ClassType extends ObjectType implements NonTrivialTypeInterface
{
    use NonTrivialTypeTrait;

    public function __construct(private string $name) { }

    public function compare(Type $other): ?int
    {
        if ($other->typeIndicator() == Type::OBJECT_INDICATOR) {
            return $other == BaseObjectType::value() ?
                    -1 :
                    self::compareClasses($this->name, $other->name);
        } else {
            return $other->handleTrivialCompareCases();
        }
    }

    public function baseType(): PrimitiveTypeInterface { return BaseObjectType::value(); }

    /**
     * Checks whether or not this type represents a type that is defined.
     * @return bool
     */
    public function isDefined(): bool { return class_exists($this->name); }

    /**
     * Gets the name of this class.
     * @return string
     */
    public function name(): string { return $this->name; }

    public function __toString(): string { return $this->name; }

    /**
     * Gets a ReflectionClass instance representing this class.
     * @return ReflectionClass
     */
    public function reflect(): ReflectionClass
    {
        return new ReflectionClass($this->name);
    }

    public function has($item): bool { return $item instanceof $this->name; }

    /**
     * Compares the two classes passed in, returning a nullable integer representing the
     * relationship between the two classes.
     * 
     * @param string $class1 The first class name to compare.
     * @param string $class2 The second class name to compare.
     * 
     * @return int|null An integer describing the subtype relationship between the classes, OR
     *                  null if they are not comparable, OR null if either class is non-existent.
     */
    public static function compareClasses(string $class1, string $class2): ?int
    {
        // Don't allow meaningless comparisons if either class doesn't exist
        if (!(class_exists($class1) && class_exists($class2))) {
            return null;
        }

        if ($class1 == $class2) {
            return 0;
        } elseif (is_subclass_of($class1, $class2, allow_string: true)) {
            return -1;
        } elseif (is_subclass_of($class2, $class1, allow_string: true)) {
            return 1;
        } else {
            return null;
        }
    }
}
