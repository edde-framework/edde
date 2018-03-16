<?php
	declare(strict_types=1);
	namespace Edde\Bus;

	use Edde\Container\ContainerException;
	use Edde\Element\Error;
	use Edde\Element\IElement;
	use Edde\Inject\Container\Container;
	use Edde\Inject\Crypt\RandomService;
	use Edde\Inject\Utils\StringUtils;

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
			} catch (ContainerException $exception) {
				return (new Error(
					sprintf('Cannot call requested service [%s::%s].', $element->getAttribute('service'), $element->getAttribute('method')),
					$this->randomService->uuid()
				))->setReference($element->getUuid());
			}
		}
	}
