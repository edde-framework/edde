<?php
declare(strict_types=1);

namespace Edde\Factory;

use Edde\Container\IAutowire;
use Edde\Container\IContainer;
use ReflectionClass;
use ReflectionMethod;

/**
 * A bus full of Nuns falls of a cliff and they all die.
 * They arrive at the gates of heaven and meet St. Peter. St. Peter says to them "Sisters, welcome to Heaven. In a moment I will let you all though the pearly gates, but before I may do that, I must ask each of you a single question. Please form a single-file line." And they do so.
 * St. Peter turns to the first Nun in the line and asks her "Sister, have you ever touched a penis?"
 * The Sister Responds "Well... there was this one time... that I kinda sorta... touched one with the tip of my pinky finger..."
 * St. Peter says "Alright Sister, now dip the tip of your pinky finger in the Holy Water, and you may be admitted." and she did so.
 * St. Peter now turns to the second nun and says "Sister, have you ever touched a penis?" "Well.... There was this one time... that I held one for a moment..."
 * "Alright Sister, now just wash your hands in the Holy Water, and you may be admitted" and she does so.
 * Now at this, there is a noise, a jostling in the line. It seems that one nun is trying to cut in front of another! St. Peter sees this and asks the Nun "Sister Susan, what is this? There is no rush!"
 * Sister Susan responds "Well if I'm going to have to gargle this stuff, I'd rather do it before Sister Mary sticks her ass in it!"
 */
class ClassFactory extends AbstractFactory {
    /** @var IReflection[] */
    static protected $reflectionCache = [];

    /** @inheritdoc */
    public function canHandle(IContainer $container, string $dependency): bool {
        return class_exists($dependency) && interface_exists($dependency) === false;
    }

    /** @inheritdoc */
    public function getReflection(IContainer $container, string $dependency): IReflection {
        if (isset(self::$reflectionCache[$dependency])) {
            return self::$reflectionCache[$dependency];
        }
        $injects = [];
        $reflectionClass = new ReflectionClass($dependency);
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $parameterReflectionClass = $reflectionMethod->getDeclaringClass();
            if ($parameterReflectionClass->implementsInterface(IAutowire::class)) {
                $injects = array_merge($injects, $this->getParams($parameterReflectionClass, $reflectionMethod, 'inject'));
            }
        }
        return self::$reflectionCache[$dependency] = new Reflection(
            $injects,
            array_reverse(array_merge([$dependency], (new ReflectionClass($dependency))->getInterfaceNames()))
        );
    }

    /** @inheritdoc */
    public function factory(IContainer $container, array $params, IReflection $dependency, string $name = null) {
        if (empty($params)) {
            return new $name();
        }
        return new $name(...$params);
    }

    /**
     * @param ReflectionClass  $reflectionClass
     * @param ReflectionMethod $reflectionMethod
     * @param string           $method
     *
     * @return array
     *
     * @throws FactoryException
     */
    protected function getParams(ReflectionClass $reflectionClass, ReflectionMethod $reflectionMethod, string $method) {
        $params = [];
        if (strlen($name = $reflectionMethod->getName()) > strlen($method) && strpos($name, $method, 0) === 0) {
            if ($reflectionMethod->isPublic() === false) {
                throw new FactoryException(sprintf('Method [%s::%s()] must be public.', $reflectionClass->getName(), $reflectionMethod->getName()));
            }
            foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                if ($reflectionClass->hasProperty($name = $reflectionParameter->getName()) === false) {
                    throw new FactoryException(sprintf('Class [%s] must have property [$%s] of the same name as parameter in method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
                } else if (($class = $reflectionParameter->getClass()) === null) {
                    throw new FactoryException(sprintf('Class [%s] must have property [$%s] with class type hint in method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
                }
                $reflectionProperty = $reflectionClass->getProperty($name);
                $reflectionProperty->setAccessible(true);
                $params[] = new Parameter($reflectionProperty->getName(), $class->getName());
            }
        }
        return $params;
    }
}
