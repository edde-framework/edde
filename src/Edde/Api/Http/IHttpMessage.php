<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Http message implementation; message parsing can be heavy, so it is useful to make it deffered.
	 */
	interface IHttpMessage {
		/**
		 * return parsed input list of headers
		 *
		 * @return IHeaderList
		 */
		public function getHeaderList(): IHeaderList;

		/**
		 * return content type from input headers
		 *
		 * @param string $default
		 *
		 * @return null|string
		 */
		public function getContentType(string $default = ''): string;

		/**
		 * content id of multipart message (must have Content-ID header)
		 *
		 * @return string|null
		 */
		public function getContentId();

		/**
		 * return array of content id's
		 *
		 * @return string[]
		 */
		public function getContentList(): array;

		/**
		 * return array of messages if this message is mutliparted
		 *
		 * @return array
		 */
		public function getMessageList(): array;

		/**
		 * return message by the given content id
		 *
		 * @param string $contentId
		 *
		 * @return IHttpMessage
		 */
		public function getMessage(string $contentId): IHttpMessage;

		/**
		 * return message body
		 *
		 * @return string
		 */
		public function getBody(): string;
	}
