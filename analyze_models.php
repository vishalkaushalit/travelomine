<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modelsPath = app_path('Models');
$files = scandir($modelsPath);
$report = [];

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $className = 'App\\Models\\' . pathinfo($file, PATHINFO_FILENAME);
        if (class_exists($className)) {
            $reflection = new ReflectionClass($className);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            $relations = [];
            foreach ($methods as $method) {
                if ($method->class === $className && $method->getNumberOfParameters() == 0) {
                    try {
                        $returnType = $method->getReturnType();
                        $returnTypeName = $returnType ? $returnType->getName() : null;
                        
                        // Check if return type is a relation
                        if ($returnTypeName && strpos($returnTypeName, 'Illuminate\Database\Eloquent\Relations') !== false) {
                            $relations[$method->getName()] = ['type' => class_basename($returnTypeName), 'has_return_type' => true];
                        } else {
                            // Try invoking to see if it returns a relation
                            // Some methods might have side effects, but usually relations don't.
                            // To be safe, let's just parse the docblock or source code.
                        }
                    } catch (\Exception $e) {}
                }
            }
            
            // source parsing fallback
            $content = file_get_contents($modelsPath . '/' . $file);
            preg_match_all('/public function\s+([a-zA-Z0-9_]+)\s*\(\)/', $content, $matches);
            foreach ($matches[1] as $methodName) {
                if (!isset($relations[$methodName])) {
                    if (preg_match('/function\s+' . $methodName . '\s*\(\).*?(return\s+\$this->(hasMany|belongsTo|hasOne|belongsToMany|morphTo|morphMany|morphOne|hasManyThrough)[^;]+;)/s', $content, $relMatch)) {
                        $relations[$methodName] = ['type' => $relMatch[2], 'has_return_type' => false];
                    }
                }
            }
            $report[$className] = $relations;
        }
    }
}

echo json_encode($report, JSON_PRETTY_PRINT);
