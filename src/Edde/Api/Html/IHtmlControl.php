<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Control\IControl;

	/**
	 * Specialized control used for html rendering
	 */
	interface IHtmlControl extends IControl {
		/**
		 * set the given tag to this control; pair if the given tag is in a pair (div, span, ...)
		 *
		 * @param string $tag
		 * @param bool $pair
		 *
		 * @return IHtmlControl
		 */
		public function setTag(string $tag, bool $pair = true): IHtmlControl;

		/**
		 * return name of tag for this control; it can be null if only children of this control should be rendered
		 *
		 * @return string|null
		 */
		public function getTag(): string;

		/**
		 * set text value of this control (for example <span>$text</span>)
		 *
		 * @param string $text
		 *
		 * @return $this
		 */
		public function setText(string $text);

		/**
		 * return text or empty string
		 *
		 * @return string
		 */
		public function getText(): string;

		/**
		 * tells if this control is a paired tag
		 *
		 * @return bool
		 */
		public function isPair(): bool;

		/**
		 * set a html id
		 *
		 * @param string $id
		 *
		 * @return $this
		 */
		public function setId(string $id);

		/**
		 * retrieve current html id of this control
		 *
		 * @return string
		 */
		public function getId(): string;

		/**
		 * set single html attribute
		 *
		 * @param string $attribute
		 * @param string $value
		 *
		 * @return $this
		 */
		public function setAttribute($attribute, $value);

		/**
		 * set list of attributes
		 *
		 * @param array $attributeList
		 *
		 * @return IHtmlControl
		 */
		public function setAttributeList(array $attributeList): IHtmlControl;

		/**
		 * @param array $attributeList
		 *
		 * @return IHtmlControl
		 */
		public function addAttributeList(array $attributeList): IHtmlControl;

		/**
		 * return attribute by the given namw
		 *
		 * @param string $name
		 * @param mixed $default
		 *
		 * @return mixed
		 */
		public function getAttribute(string $name, $default = '');

		/**
		 * add single html attribute to an array (for example class)
		 *
		 * @param string $attribute
		 * @param string $value
		 *
		 * @return $this
		 */
		public function addAttribute(string $attribute, $value);

		/**
		 * has the given html attribute value? (is present and is not null)
		 *
		 * @param string $attribute
		 *
		 * @return bool
		 */
		public function hasAttribute($attribute);

		/**
		 * return current html attribute list
		 *
		 * @return string[]
		 */
		public function getAttributeList(): array;

		/**
		 * set a given data to control (data-* attribute)
		 *
		 * @param string $name
		 * @param mixed $data
		 *
		 * @return IHtmlControl
		 */
		public function data(string $name, $data): IHtmlControl;

		/**
		 * retrieve the given data from control
		 *
		 * @param string $name
		 * @param null $default
		 *
		 * @return string|mixed
		 */
		public function getData(string $name, $default = null);

		/**
		 * set the given css class
		 *
		 * @param string $class
		 *
		 * @return $this
		 */
		public function addClass(string $class);

		/**
		 * toggle class presence
		 *
		 * @param string $class
		 * @param bool|null $enable
		 *
		 * @return IHtmlControl
		 */
		public function toggleClass(string $class, bool $enable = null): IHtmlControl;

		/**
		 * is the given class present in this control?
		 *
		 * @param string $class
		 *
		 * @return bool
		 */
		public function hasClass(string $class);

		/**
		 * return current list of classes
		 *
		 * @return string[]
		 */
		public function getClassList();

		/**
		 * @return IHtmlControl[]
		 */
		public function getControlList(): array;

		/**
		 * execute output rendering of this control - return string version of this control
		 *
		 * @param int $indent indent modifier when "parent" control is not rendered
		 *
		 * @return string
		 */
		public function render(int $indent = 0): string;

		/**
		 * load client script based on a current class name
		 *
		 * @param string $class
		 * @param string $file
		 *
		 * @return IHtmlControl
		 */
		public function javascript(string $class, string $file = null): IHtmlControl;

		/**
		 * @deprecated
		 *
		 * @param string|null $file
		 *
		 * @return IHtmlControl
		 * @internal param null|string $class
		 */
		public function stylesheet(string $file = null): IHtmlControl;

		/**
		 * return all invalid (dirty) controls
		 *
		 * @return array|IHtmlControl[]
		 */
		public function invalidate(): array;

		/**
		 * remove all controls with a given id; it's useful to ensure that the given id is not in control tree
		 *
		 * @param string $id
		 *
		 * @return IHtmlControl
		 */
		public function remove(string $id): IHtmlControl;

		/**
		 * replace existing control with the given one by id; exception should be thrown if there is no id
		 *
		 * @param IHtmlControl $htmlControl
		 *
		 * @return IHtmlControl
		 */
		public function replace(IHtmlControl $htmlControl): IHtmlControl;

		/**
		 * @return IHtmlControl[]
		 */
		public function getIterator();
	}
