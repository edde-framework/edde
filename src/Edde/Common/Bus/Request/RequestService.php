<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus\Request;

	use Edde\Api\Bus\IElement;
	use Edde\Api\Bus\Request\IRequestService;
	use Edde\Api\Utils\Inject\StringUtils;
	use Edde\Common\Bus\AbstractHandler;
	use Edde\Common\Bus\Error;
	use Edde\Exception\Container\FactoryException;
	use Edde\Inject\Container\Container;
	use Edde\Inject\Crypt\RandomService;

	class RequestService extends AbstractHandler implements IRequestService {
		use Container;
		use StringUtils;
		use RandomService;

		/** @inheritdoc */
		public function canHandle(IElement $element): bool {
			return $element->getType() === 'request';
		}

		/** @inheritdoc */
		public function execute(IElement $element): ?IElement {
			$this->validate($element);
			try {
				/**
				 * ugly hack to convert input string to form of foo°foo-service which could be later
				 * converted to Foo°FooService and ° could be replaced by "\" leading to Foo\FooService
				 */
				$service = str_replace(['.', '-'], ['°', '~'], $element->getAttribute('service'));
				$service = str_replace('°', '\\', $this->stringUtils->toCamelCase($service));
				$method = $this->stringUtils->toCamelHump($element->getAttribute('method'));
				/** @var $response IElement */
				if (($response = $this->container->create($service, [], __METHOD__)->{$method}($element)) instanceof IElement) {
					return $response->setReference($element->getUuid());
				}
				return null;
			} catch (FactoryException $exception) {
				return (new Error(
					sprintf('Cannot call requested service [%s::%s].', $element->getAttribute('service'), $element->getAttribute('method')),
					$this->randomService->uuid()
				))->setReference($element->getUuid());
			}
		}
	}
