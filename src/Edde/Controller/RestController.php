<?php
declare(strict_types=1);

namespace Edde\Controller;

use Edde\Content\JsonContent;
use Edde\Http\IResponse;
use Edde\Http\Response;
use Edde\Service\Utils\StringUtils;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Provides helpful methods around implementing REST service.
 */
class RestController extends HttpController {
    use StringUtils;

    /**
     * @inheritdoc
     *
     * @throws ReflectionException
     */
    public function __call(string $name, $arguments) {
        $response = new Response();
        $response->setCode(IResponse::R400_BAD_REQUEST);
        if ($match = $this->stringUtils->match($name, '~^action(?<method>[a-z]+)$~i', true)) {
            $response->setContent(new JsonContent(sprintf('Requested method [%s] is not allowed.', strtoupper($match['method']))));
            $response->setCode(IResponse::R400_NOT_ALLOWED);
            $response->header('Allow', implode(', ', $this->getAllowedMethods()));
        }
        return $response->execute();
    }

    /**
     * @return string[]
     *
     * @throws ReflectionException
     */
    protected function getAllowedMethods(): array {
        $allowed = [];
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (strpos($name = $reflectionMethod->getName(), 'action') === 0 && strlen($name) > 6) {
                $allowed[] = strtoupper(substr($name, 6));
            }
        }
        return $allowed;
    }

    /**
     * @throws ReflectionException
     */
    public function actionOptions() {
        $response = new Response();
        $response->headers([
            'Access-Control-Allow-Methods' => implode(', ', $this->getAllowedMethods()),
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Headers' => $this->requestService->getHeaders()->get('Access-Control-Request-Headers', '*'),
        ]);
        $response->execute();
    }
}
