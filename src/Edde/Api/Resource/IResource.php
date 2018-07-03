<?php
	declare(strict_types=1);

	namespace Edde\Api\Resource;

	use Edde\Api\Url\IUrl;
	use IteratorAggregate;

	/**
	 * General interface describing resource "somewhere"; it can be file, url, any resource.
	 */
	interface IResource extends IteratorAggregate {
		/**
		 * return resource's location; it can even be on local filesystem
		 *
		 * @return IUrl
		 */
		public function getUrl();

		/**
		 * return relative path; if there is no base dir either in a resource or specified in parameter, exception should be thrown; if base dir is not subset of path, exception should be thrown
		 *
		 * @param string|null $base
		 *
		 * @return string
		 */
		public function getRelativePath(string $base = null);

		/**
		 * @param string $base
		 *
		 * @return IResource
		 */
		public function setBase(string $base): IResource;

		/**
		 * return base path if set
		 *
		 * @return string|null
		 */
		public function getBase();

		/**
		 * return firendy name of this resource; this can be arbitrary string
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return mime type of this resource
		 *
		 * @return string
		 */
		public function getMime(): string;

		/**
		 * is this resource available? (file exists, ...)
		 *
		 * @return bool
		 */
		public function isAvailable(): bool;

		/**
		 * return whole content of the URL of this Resource
		 *
		 * @return string
		 */
		public function get();

		/**
		 * @return \Iterator|\Traversable
		 */
		public function getIterator();
	}
