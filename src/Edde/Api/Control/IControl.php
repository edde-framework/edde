<?php
	declare(strict_types = 1);

	namespace Edde\Api\Control;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Node\INode;

	/**
	 * Control is general element for transfering incoming request into the internal system service and for
	 * generating response.
	 */
	interface IControl extends IDeffered, IEventBus, \IteratorAggregate {
		/**
		 * return node of this control
		 *
		 * @return INode
		 */
		public function getNode(): INode;

		/**
		 * return root control
		 *
		 * @return IControl
		 */
		public function getRoot(): IControl;

		/**
		 * has this control some children?
		 *
		 * @return bool
		 */
		public function isLeaf(): bool;

		/**
		 * return parent or null of this control is root
		 *
		 * @return IControl|null
		 */
		public function getParent();

		/**
		 * remove this control from control tree
		 *
		 * @return IControl
		 */
		public function disconnect(): IControl;

		/**
		 * add the new control to hierarchy of this control
		 *
		 * @param IControl $control
		 *
		 * @return IControl
		 */
		public function addControl(IControl $control): IControl;

		/**
		 * called when this control is attached to the given one
		 *
		 * @param IControl $control
		 *
		 * @return IControl
		 */
		public function attached(IControl $control): IControl;

		/**
		 * @param IControl[] $controlList
		 *
		 * @return IControl
		 */
		public function addControlList(array $controlList): IControl;

		/**
		 * return first level of controls (the same result as self::getNodeList())
		 *
		 * @return IControl[]
		 */
		public function getControlList(): array;

		/**
		 * return all invalid (dirty) controls
		 *
		 * @return array|IControl[]
		 */
		public function invalidate(): array;

		/**
		 * mark control as dirty; this should change state of all child controls
		 *
		 * @param bool $dirty
		 *
		 * @return IControl
		 */
		public function dirty(bool $dirty = true): IControl;

		/**
		 * is this control dirty?
		 *
		 * @return bool
		 */
		public function isDirty(): bool;

		/**
		 * optional method for control update (could change internal state of this or other controls)
		 *
		 * @return IControl
		 */
		public function update(): IControl;

		/**
		 * fill internal properties with given one (stdClass is preffered way)
		 *
		 * @param \Traversable|\stdClass|array $fill
		 *
		 * @return IControl
		 */
		public function fill($fill): IControl;

		/**
		 * execute the given method in this controls
		 *
		 * @param string $method
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function handle(string $method, array $parameterList);

		/**
		 * this method should be used for internal control creation
		 *
		 * @param string $control
		 * @param array ...$parameterList
		 *
		 * @return IControl
		 */
		public function createControl(string $control, ...$parameterList): IControl;

		/**
		 * @return IControl[]
		 */
		public function getIterator();
	}
