<?php

namespace Kathenae\SSG;

use Closure;

class Route
{
    private string $method;
    private string $pattern;
    private string $originalPattern;
    private Closure $handler;

    // SSG attributes
    private ?Closure $staticParams;

    public function __construct(string $method, string $pattern, Closure $handler)
    {
        $this->method = strtoupper($method);
        $this->originalPattern = $pattern;
        $this->pattern = $this->compilePattern($pattern);
        $this->handler = $handler;
    }

    private function compilePattern(string $pattern): string
    {
        // Convert {param} placeholders to named regex groups
        return '#^' . preg_replace(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            '(?P<$1>[^/]+)',
            $pattern
        ) . '$#';
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getOriginalPattern(): string
    {
        return $this->originalPattern;
    }

    public function matches(string $method, string $uri): ?array
    {
        if ($this->method !== strtoupper($method)) {
            return null;
        }

        if (preg_match($this->pattern, $uri, $matches)) {
            // Return only named parameters
            return array_filter(
                $matches,
                fn($key) => is_string($key),
                ARRAY_FILTER_USE_KEY
            );
        }

        return null;
    }

    /**
     * Handle the given request
     * 
     * @param \Kathenae\SSG\Request $request
     */
    public function handle(Request $request)
    {
        return ($this->handler)($request);
    }

    /**
     * Enable static site generation for this route
     * @param \Closure|null $params closure function that returns list of possible route params
     * @return static
     */
    public function withSsg(Closure $params = null)
    {
        $this->staticParams = $params;
        if ($params == null) {
            $this->staticParams = fn() => [];
        }
        return $this;
    }

    /**
     * Is static site generation enabled for this route
     * 
     * @return bool
     */
    public function isSsgEnabled()
    {
        return $this->staticParams != null;
    }

    /**
     * Get the static site generation route params defined on this route
     */
    public function getSsgParams()
    {
        if (!$this->staticParams) {
            return [];
        }

        $params = ($this->staticParams)();
        return $params;
    }
}
