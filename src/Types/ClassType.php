<?php

namespace PhpPlus\Core\Types;

use ReflectionClass;

/**
 * Represents a standard PHP class type definition.
 */
final class ClassType extends ObjectType implements NonTrivialTypeInterface
{
    use NonTrivialTypeTrait;

    /**
     * Constructs a new instance of the {@see self} class representing the class name
     * passed in.
     * 
     * @param string    $name       The name of the class to represent.
     * @param bool      $autoload   Whether or not to call autoload by default when checking that
     *                              the class exists.
     * 
     * @throws ClassNotFoundException The class name passed in is not defined.
     */
    public function __construct(private string $name, bool $autoload = true)
    {
        if (!class_exists($name, $autoload)) {
            throw new ClassNotFoundException("class \"{$name}\" does not exist");
        }
    }

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

    /**
     * Calls the constructor for the class this type represents.
     * 
     * @param mixed ...$args The arguments to call the constructor on.
     * 
     * @return object   An instance of the class this type represents resulting from the
     *                  constructor call.
     */
    public function new(...$args): object
    {
        $class = $this->name;
        return new $class(...$args);
    }

    public function has($item): bool { return $item instanceof $this->name; }

    /**
     * Compares the two classes passed in, returning a nullable integer representing the
     * relationship between the two classes.
     * 
     * @param string $class1 The first class name to compare.
     * @param string $class2 The second class name to compare.
     * 
     * @return int|null An integer describing the subtype relationship between the classes, or
     *                  `null` if the classes are not comparable.
     */
    public static function compareClasses(string $class1, string $class2): ?int
    {
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
