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
			public function getUrl(): IUrl;

			/**
			 * return resource's path
			 *
			 * @return string
			 */
			public function getPath(): string;

			/**
			 * return an extension of a resource if available
			 *
			 * @return string|null
			 */
			public function getExtension(): ?string;

			/**
			 * return firendy name of this resource; this can be arbitrary string
			 *
			 * @return string
			 */
			public function getName(): string;

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
			public function get(): string;
		}
