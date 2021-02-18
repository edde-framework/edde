<?php
declare(strict_types=1);

namespace Edde;

use function is_readable;

/**
 * Simple autoloader class.
 */
class Autoloader {
    /** @var string */
    protected $namespace;
    /** @var string */
    protected $path;
    /** @var string|null */
    protected $root;

    /**
     * @param string $namespace
     * @param string $path
     * @param string $root
     */
    public function __construct(string $namespace, string $path, string $root = null) {
        $this->namespace = $namespace;
        $this->path = $path;
        $this->root = $root;
    }

    public function load(string $class): bool {
        $file = str_replace([
            $this->namespace,
            '\\',
        ], [
            $this->root,
            '/',
        ], $this->path . '/' . $class . '.php');
        /**
         * it's strange, but this is a performance boost
         */
        if (is_readable($file) === false) {
            return false;
        }
        /** @noinspection PhpIncludeInspection */
        include_once $file;
        return true;
    }

    /**
     * simple autoloader based on namespaces and correct class names
     *
     * @param string $namespace
     * @param string $path
     * @param bool   $root loader is in the root of autoloaded sources
     *
     * @return callable
     */
    static public function register($namespace, $path, $root = true): Autoloader {
        spl_autoload_register([
            $self = new self(
                $namespace .= '\\',
                $path,
                $root ? null : $namespace
            ),
            'load',
        ], true, true);
        return $self;
    }
}
