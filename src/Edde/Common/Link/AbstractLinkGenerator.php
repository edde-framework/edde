<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Link\ILink;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Common\AbstractObject;
	use Edde\Common\Strings\StringUtils;

	abstract class AbstractLinkGenerator extends AbstractObject implements ILinkGenerator, ILazyInject {
		use LazyRequestTrait;

		protected function match(string $control, string $action) {
			$simpleRegexp = '[a-z0-9-]+';
			$parameterList = null;
			if (($match = StringUtils::match($action, '~^handle=(?<handleHandle>[a-z0-9-]+)$~')) !== null) {
				$parameterList['handle'] = $control . '.' . $match['handleHandle'];
			} else if (($match = StringUtils::match($action, '~^' . $simpleRegexp . '$~')) !== null) {
				$parameterList['action'] = $control . '.' . $action;
			} else if ($action[0] === '$') {
				$parameterList['handle'] = $control . '.' . substr($action, 1);
			} else if (strpos($action, '@$') === 0) {
				$current = $this->request->getAction();
				$parameterList['action'] = $current[0] . '.' . $this->request->getActionName();
				$parameterList['handle'] = $control . '.' . substr($action, 2);
			}
			return $parameterList;
		}

		/**
		 * shorthand for translating generate ILink to values
		 *
		 * @param mixed $generate
		 * @param array $parameterList
		 *
		 * @return array
		 */
		protected function list($generate, array $parameterList) {
			return $generate instanceof ILink ? [
				$generate->getLink(),
				$generate->getParameterList(),
			] : [
				$generate,
				$parameterList,
			];
		}
	}
