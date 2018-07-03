<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;
	use Edde\Common\Node\NodeUtils;

	/**
	 * Json converter from json encoded string to "something".
	 */
	class JsonConverter extends AbstractConverter {
		/**
		 * Objective: shoot yourself in the foot using a computer language.
		 *
		 * Perl: You shoot yourself in the foot.
		 *
		 * PHP: You make a website of yourself shooting yourself in the foot.
		 *
		 * Java: Your foot shoots itself while it waits for the team of programmers to finish the code.
		 *
		 * C++: You accidentally create a dozen instances of yourself and shoot them all in the foot. Providing emergency medical assistance is impossible since you can't tell which are bitwise copies and which are just pointing at others and saying, "That's me, over there."
		 *
		 * Visual Basic: You shoot Steve Jobs in the foot.
		 *
		 * FORTRAN: You shoot yourself in each toe, iteratively, until you run out of toes, then you read in the next foot and repeat. If you run out of bullets, you continue with the attempts to shoot yourself anyways because you have no exception-handling capability.
		 *
		 * Pascal: The compiler won't let you shoot yourself in the foot.
		 *
		 * LISP: You shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds...
		 *
		 * BASIC: Shoot yourself in the foot with a water pistol.
		 *
		 * Unix:
		 *
		 * % ls
		 * foot.c foot.h foot.o toe.c toe.o
		 * % rm * .o
		 * rm:.o no such file or directory
		 * % ls
		 * %Concurrent Euclid: You shoot yourself in somebody else's foot.
		 *
		 * Assembler: You try to shoot yourself in the foot, only to discover you must first invent the gun, the bullet, the trigger, and your foot.
		 */
		public function __construct() {
			$this->register([
				'json',
				'array',
				'object',
				\stdClass::class,
				'text/plain',
			], [
				'json',
				'application/json',
				'text/json',
				'*/*',
				'text/html',
			]);
			$this->register([
				'application/json',
				'text/json',
				'stream+application/json',
			], [
				'array',
				'object',
				\stdClass::class,
				'node',
				INode::class,
			]);
		}

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			switch ($mime) {
				/** @noinspection PhpMissingBreakStatementInspection */
				case 'stream+application/json':
					$content = file_get_contents($content);
				case 'application/json':
				case 'text/json':
					$this->unsupported($content, $target, $content instanceof IFile || is_string($content));
					$content = $content instanceof IFile ? $content->get() : $content;
					switch ($target) {
						case 'array':
							return new Content(json_decode($content, true), 'array');
						case 'object':
						case \stdClass::class:
							return new Content(json_decode($content), \stdClass::class);
						case 'node':
						case INode::class:
							return new Content(NodeUtils::toNode(json_decode($content)), INode::class);
					}
					break;
				case 'json':
				case 'array':
				case 'object':
				case \stdClass::class:
				case 'text/plain':
					switch ($target) {
						case 'json':
						case 'application/json':
						case 'text/json':
						case '*/*':
							return new Content(json_encode($content), 'application/json');
						case 'text/html':
							return new Content($content, 'text/html');
					}
					break;
			}
			return $this->exception($mime, $target);
		}
	}
