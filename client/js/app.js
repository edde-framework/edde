var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
	var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
	if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
	else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
	return c > 3 && r && Object.defineProperty(target, key, r), r;
};
define("edde/collection", ["require", "exports"], function (require, exports) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class Collection {
		constructor(collection = []) {
			this.collection = collection;
		}

		add(item) {
			const array = this.toArray();
			array[array.length] = item;
			return this;
		}

		each(callback) {
			const context = {
				count: -1,
				loop: false,
				item: null,
				value: null,
				key: null,
			};
			const array = this.toArray();
			const length = array.length;
			for (context.count = 0; context.count < length; context.key = context.count++) {
				context.loop = true;
				if (callback.call(context, context.value = array[context.count], context.count) === false) {
					break;
				}
			}
			return context;
		}

		subEach(callback, start, length) {
			return this.subCollection(start, length).each(callback);
		}

		subCollection(start, length) {
			if (!this.collection) {
				return new Collection();
			}
			const collectionLength = this.collection.length;
			start = start || 0;
			length = start + (length || collectionLength);
			const items = [];
			for (let i = start; i < length && i < collectionLength; i++) {
				items[items.length] = this.collection[i];
			}
			return new Collection(items);
		}

		reverse() {
			this.collection = this.toArray().reverse();
			return this;
		}

		toArray() {
			return this.collection ? this.collection : this.collection = [];
		}

		getCount() {
			return this.toArray().length;
		}

		index(index) {
			if (!this.collection || index >= this.collection.length) {
				return null;
			}
			return this.collection[index];
		}

		first() {
			return this.collection && this.collection.length > 0 ? this.collection[0] : null;
		}

		last() {
			return this.collection && this.collection.length > 0 ? this.collection[this.collection.length - 1] : null;
		}

		isEmpty() {
			return this.getCount() === 0;
		}

		clear() {
			this.collection = [];
			return this;
		}

		removeBy(callback, name) {
			let collection = [];
			this.each((value) => {
				if (callback(value) !== false) {
					collection[collection.length] = value;
				}
			});
			this.collection = collection;
			return this;
		}

		copy(copy) {
			this.collection = this.toArray().concat(copy.toArray());
			return this;
		}

		replace(replace) {
			this.collection = replace.toArray();
			return this;
		}

		sort(sort) {
			this.collection = this.toArray().sort(sort);
			return this;
		}
	}

	exports.Collection = Collection;

	class HashMap {
		constructor(hashMap = {}) {
			this.hashMap = hashMap;
		}

		set(name, item) {
			this.hashMap[name] = item;
			return this;
		}

		put(put) {
			this.hashMap = put;
			return this;
		}

		has(name) {
			return this.hashMap.hasOwnProperty(name);
		}

		get(name, value) {
			return this.hashMap.hasOwnProperty(name) ? this.hashMap[name] : value;
		}

		remove(name) {
			this.hashMap[name] = null;
			delete this.hashMap[name];
			return this;
		}

		isEmpty() {
			const hasOwnProperty = Object.prototype.hasOwnProperty;
			if (this.hashMap == null) {
				return true;
			}
			else if (this.hashMap.length > 0) {
				return false;
			}
			else if (this.hashMap.length === 0) {
				return true;
			}
			else if (typeof this.hashMap !== "object") {
				return false;
			}
			for (const key in this.hashMap) {
				if (hasOwnProperty.call(this.hashMap, key)) {
					return false;
				}
			}
			return true;
		}

		toObject() {
			return this.hashMap;
		}

		each(callback) {
			const context = {
				count: -1,
				loop: false,
				item: null,
				value: null,
				key: null,
			};
			if (!this.hashMap) {
				return context;
			}
			for (const key in this.hashMap) {
				context.loop = true;
				context.count++;
				if (callback.call(context, context.key = key, context.value = this.hashMap[key]) === false) {
					break;
				}
			}
			return context;
		}

		first() {
			return this.each(() => false).value;
		}

		last() {
			return this.each(() => true).value;
		}

		getCount() {
			return this.each(() => true).count + 1;
		}

		clear() {
			this.hashMap = {};
			return this;
		}

		copy(copy) {
			copy.each((k, v) => this.set(k, v));
			return this;
		}

		replace(replace) {
			this.hashMap = replace.toObject();
			return this;
		}

		fromCollection(collection, key) {
			collection.each(value => this.set(key(value), value));
			return this;
		}
	}

	exports.HashMap = HashMap;

	class HashMapCollection {
		constructor() {
			this.hashMap = new HashMap();
		}

		add(name, item) {
			if (this.hashMap.has(name) === false) {
				this.hashMap.set(name, new Collection());
			}
			this.hashMap.get(name).add(item);
			return this;
		}

		has(name) {
			return this.hashMap.has(name);
		}

		sort(name, sort) {
			this.toCollection(name).sort(sort);
			return this;
		}

		toArray(name) {
			return this.hashMap.get(name, new Collection()).toArray();
		}

		toCollection(name) {
			return this.hashMap.get(name, new Collection());
		}

		each(name, callback) {
			return this.toCollection(name).each(callback);
		}

		eachCollection(callback) {
			return this.hashMap.each(callback);
		}

		remove(name) {
			this.hashMap.remove(name);
			return this;
		}

		removeBy(callback, name) {
			if (name) {
				this.toCollection(name).removeBy(callback);
				return this;
			}
			this.hashMap.each((_, item) => item.removeBy(callback));
			return this;
		}

		clear() {
			this.hashMap = new HashMap();
			return this;
		}
	}

	exports.HashMapCollection = HashMapCollection;
});
define("edde/dom", ["require", "exports", "edde/e3"], function (require, exports, e3_1) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class HtmlElement {
		constructor(element) {
			this.element = element;
		}

		getElement() {
			return this.element;
		}

		getId() {
			return this.element.getAttribute('id') || '';
		}

		getName() {
			return this.element.nodeName.toLowerCase();
		}

		event(name, callback) {
			this.element.addEventListener(name, (event) => callback.call(this, event), false);
			return this;
		}

		data(name, value) {
			return this.element.getAttribute('data-' + name) || value;
		}

		toggleClass(name, toggle) {
			let hasClass = this.hasClass(name);
			if (toggle === true && hasClass === false) {
				this.addClass(name);
			}
			else if (toggle === true && hasClass) {
			}
			else if (toggle === false && hasClass === false) {
			}
			else if (toggle === false && hasClass) {
				this.removeClass(name);
			}
			else if (hasClass) {
				this.removeClass(name);
			}
			else if (hasClass === false) {
				this.addClass(name);
			}
			return this;
		}

		toggleClassList(nameList, toggle) {
			e3_1.e3.$(nameList, (item) => this.toggleClass(item, toggle));
			return this;
		}

		addClass(name) {
			if (this.hasClass(name)) {
				return this;
			}
			this.element.className += ' ' + name;
			this.element.className = this.className(this.element.className);
			return this;
		}

		addClassList(nameList) {
			e3_1.e3.$(nameList, (item) => this.addClass(item));
			return this;
		}

		hasClass(name) {
			return this.element.className !== undefined && (' ' + this.element.className + ' ').indexOf(' ' + name + ' ') !== -1;
		}

		hasClassList(nameList) {
			let hasClass = false;
			e3_1.e3.$(nameList, item => {
				hasClass = true;
				if (this.hasClass(item) === false) {
					hasClass = false;
					return false;
				}
			});
			return hasClass;
		}

		removeClass(name) {
			this.element.className = this.className(this.element.className.replace(name, ''));
			return this;
		}

		removeClassList(nameList) {
			e3_1.e3.$(nameList, item => this.removeClass(item));
			return this;
		}

		html(html) {
			this.element.innerHTML = html;
			return this;
		}

		text(text) {
			this.clear();
			this.element.appendChild(e3_1.e3.Text(text));
			return this;
		}

		attr(name, value) {
			this.element.setAttribute(name, value);
			return this;
		}

		attrList(attrList) {
			e3_1.e3.$$(attrList, (name, value) => this.attr(name, value));
			return this;
		}

		getInnerHtml() {
			return this.element.innerHTML;
		}

		getOuterHtml() {
			return this.element.outerHTML;
		}

		clear() {
			this.html('');
			return this;
		}

		getOffsetHeight() {
			return this.element.offsetHeight;
		}

		getOffsetWidth() {
			return this.element.offsetWidth;
		}

		getClientHeight() {
			return this.element.clientHeight;
		}

		getClientWidth() {
			return this.element.clientWidth;
		}

		getParentList(root) {
			let elementList = [this];
			let parent = this.element.parentNode;
			while (parent) {
				if (parent === root) {
					break;
				}
				elementList[elementList.length] = new HtmlElement(parent);
				parent = parent.parentNode;
			}
			return e3_1.e3.collection(elementList);
		}

		collection(selector) {
			return e3_1.e3.selector(selector, this.element);
		}

		attach(child) {
			this.element.appendChild(child.getElement());
			return this;
		}

		attachHtml(html) {
			return this.attach(e3_1.e3.html(html));
		}

		attachList(elementList) {
			e3_1.e3.$(elementList, element => element ? this.attach(element) : null);
			return this;
		}

		attachTo(parent) {
			parent.attach(this);
			return this;
		}

		position(top, left) {
			this.element.style.top = top;
			this.element.style.left = left;
			return this;
		}

		remove() {
			this.element.parentNode ? this.element.parentNode.removeChild(this.element) : null;
			this.element = null;
			return this;
		}

		className(name) {
			return (name.match(/[^\x20\t\r\n\f]+/g) || []).join(' ');
		}
	}

	exports.HtmlElement = HtmlElement;

	class HtmlElementCollection {
		constructor(root, selector) {
			this.root = root;
			this.selector = e3_1.e3.Selector(selector);
		}

		event(name, callback) {
			this.each(element => element.event(name, callback));
			return this;
		}

		addClass(name) {
			this.each(element => element.addClass(name));
			return this;
		}

		addClassList(nameList) {
			this.each(element => element.addClassList(nameList));
			return this;
		}

		hasClass(name) {
			let hasClass = false;
			this.each(element => (hasClass = element.hasClass(name)) !== false);
			return hasClass;
		}

		hasClassList(nameList) {
			let hasClass = false;
			this.each(element => (hasClass = element.hasClassList(nameList)) !== false);
			return hasClass;
		}

		removeClass(name) {
			this.each(element => element.removeClass(name));
			return this;
		}

		removeClassList(nameList) {
			this.each(element => element.removeClassList(nameList));
			return this;
		}

		toggleClass(name, toggle) {
			this.each(element => element.toggleClass(name, toggle));
			return this;
		}

		toggleClassList(nameList, toggle) {
			this.each(element => element.toggleClassList(nameList, toggle));
			return this;
		}

		attr(name, value) {
			this.each(element => element.attr(name, value));
			return this;
		}

		attrList(attrList) {
			this.each(element => element.attrList(attrList));
			return this;
		}

		html(html) {
			this.each(element => element.html(html));
			return this;
		}

		each(callback) {
			return this.selector.each(this.root, callback);
		}

		getCount() {
			return this.selector.getCount(this.root);
		}

		index(index) {
			return this.selector.index(this.root, index);
		}

		remove() {
			this.each(element => element.remove());
			return this;
		}
	}

	exports.HtmlElementCollection = HtmlElementCollection;

	class AbstractSelector {
		constructor(selector) {
			this.selector = selector;
		}

		getCount(root) {
			let count = 0;
			this.each(root, () => count++);
			return count;
		}

		index(root, index) {
			let count = 0;
			return this.each(root, function (htmlElement) {
				this.item = htmlElement;
				return count++ !== index;
			}).item;
		}
	}

	exports.AbstractSelector = AbstractSelector;

	class SimpleClassSelector extends AbstractSelector {
		each(root, callback) {
			const self = this;
			return e3_1.e3.$(root.getElementsByTagName('*'), function (element) {
				const htmlElement = new HtmlElement(element);
				if (htmlElement.hasClass(self.selector)) {
					return callback.call(this, htmlElement);
				}
			});
		}
	}

	exports.SimpleClassSelector = SimpleClassSelector;

	class SimpleIdSelector extends AbstractSelector {
		each(root, callback) {
			const self = this;
			return e3_1.e3.$(root.getElementsByTagName('*'), function (element) {
				const htmlElement = new HtmlElement(element);
				if (htmlElement.getId() === self.selector) {
					return callback.call(this, htmlElement);
				}
			});
		}
	}

	exports.SimpleIdSelector = SimpleIdSelector;

	class SimpleSelector extends AbstractSelector {
		constructor(selector) {
			super(selector);
			this.selectorList = [];
			const selectorList = selector.split(/\s+?/);
			for (let filter of selectorList) {
				if (filter === '') {
					continue;
				}
				const match = filter.match(/#[a-zA-Z0-9_-]+|\.[a-zA-Z0-9_-]+|[a-zA-Z0-9_-]+/g);
				match ? this.selectorList[this.selectorList.length] = match : null;
			}
			if (this.selectorList.length !== selectorList.length) {
				throw new Error('Invalid selector [' + selector + ']');
			}
		}

		each(root, callback) {
			const self = this;
			return e3_1.e3.$(root.getElementsByTagName('*'), function (element) {
				const selectorListLength = self.selectorList.length;
				const nodeList = e3_1.e3.el(element).getParentList(root).reverse().toArray();
				if (selectorListLength > 1 && nodeList.length < selectorListLength) {
					return;
				}
				let filterId = 0;
				let count = 0;
				let matched;
				for (let htmlElement of nodeList) {
					matched = false;
					let match = true;
					for (let id of self.selectorList[filterId]) {
						const first = id.charAt(0);
						if (first === '#') {
							match = match && htmlElement.getId() === id.substr(1);
						}
						else if (first === '.') {
							match = match && htmlElement.hasClass(id.substr(1));
						}
						else {
							match = match && htmlElement.getName() === id;
						}
						if (match === false) {
							break;
						}
					}
					if (match) {
						filterId = Math.min(++filterId, selectorListLength - 1);
						count++;
						matched = true;
					}
				}
				if (matched && count >= selectorListLength) {
					return callback.call(this, nodeList[nodeList.length - 1]);
				}
			});
		}
	}

	exports.SimpleSelector = SimpleSelector;
});
define("edde/converter", ["require", "exports", "edde/e3"], function (require, exports, e3_2) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class Content {
		constructor(content, mime) {
			this.content = content;
			this.mime = mime;
		}

		getContent() {
			return this.content;
		}

		getMime() {
			return this.mime;
		}
	}

	exports.Content = Content;

	class AbstractConverter {
		constructor() {
			this.mimeList = [];
		}

		register(source, target) {
			e3_2.e3.$(source, src => e3_2.e3.$(target, tgt => this.mimeList[this.mimeList.length] = src + '|' + tgt));
		}

		getMimeList() {
			return this.mimeList;
		}

		content(content, target) {
			return this.convert(content.getContent(), content.getMime(), target);
		}
	}

	exports.AbstractConverter = AbstractConverter;

	class Convertable {
		constructor(converter, content, target) {
			this.target = null;
			this.converter = converter;
			this.content = content;
			this.target = target;
		}

		getContent() {
			return this.content;
		}

		getTarget() {
			return this.target;
		}

		convert() {
			if (this.result) {
				return this.result;
			}
			return this.result = this.converter.content(this.content, this.target);
		}
	}

	exports.Convertable = Convertable;

	class ConverterManager {
		constructor() {
			this.converterList = e3_2.e3.hashMap();
		}

		registerConverter(converter) {
			e3_2.e3.$(converter.getMimeList(), mime => this.converterList.set(mime, converter));
			return this;
		}

		convert(content, mime, targetList) {
			return this.content(new Content(content, mime), targetList);
		}

		content(content, targetList) {
			if (targetList === null) {
				return new Convertable(new PassConverter(), content, content.getMime());
			}
			const mime = content.getMime();
			let convertable = null;
			e3_2.e3.$(targetList, target => {
				const id = mime + '|' + target;
				if (mime === target) {
					convertable = new Convertable(new PassConverter(), content, content.getMime());
					return false;
				}
				else if (this.converterList.has(id)) {
					convertable = new Convertable(this.converterList.get(id), content, target);
					return false;
				}
			});
			if (convertable) {
				return convertable;
			}
			throw new Error('Cannot convert [' + mime + '].');
		}
	}

	exports.ConverterManager = ConverterManager;

	class PassConverter extends AbstractConverter {
		convert(content, mime, target) {
			return new Content(content, mime);
		}
	}

	exports.PassConverter = PassConverter;

	class JsonConverter extends AbstractConverter {
		constructor() {
			super();
			this.register([
				'object',
			], [
				'application/json',
				'json',
			]);
			this.register([
				'application/json',
				'json',
			], [
				'object',
			]);
		}

		convert(content, mime, target) {
			switch (target) {
				case 'application/json':
				case 'json':
					return new Content(JSON.stringify(content), 'application/json');
				case 'object':
					return new Content(JSON.parse(content), 'application/javascript');
			}
			throw new Error('Cannot convert [' + mime + '] to [' + target + '] in json converter.');
		}
	}

	exports.JsonConverter = JsonConverter;
});
define("edde/node", ["require", "exports", "edde/e3", "edde/converter"], function (require, exports, e3_3, converter_1) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class AbstractNode {
		constructor(parent) {
			this.nodeList = e3_3.e3.collection();
			this.parent = parent;
		}

		setParent(parent) {
			this.parent = parent;
			return this;
		}

		detach() {
			this.parent = null;
			return this;
		}

		getParent() {
			return this.parent;
		}

		isRoot() {
			return this.parent === null;
		}

		isChild() {
			return this.parent !== null;
		}

		addNode(node) {
			this.nodeList.add(node);
			node.setParent(this);
			return this;
		}

		addNodeList(nodeList) {
			e3_3.e3.$(nodeList, node => this.addNode(node));
			return this;
		}

		getNodeList() {
			return this.nodeList;
		}

		getNodeCount() {
			return this.nodeList.getCount();
		}

		each(callback) {
			return this.nodeList.each(function (node) {
				return callback.call(this, node);
			});
		}
	}

	exports.AbstractNode = AbstractNode;

	class Node extends AbstractNode {
		constructor(name = null, value = null) {
			super(null);
			this.attributeList = e3_3.e3.hashMap();
			this.metaList = e3_3.e3.hashMap();
			this.name = name;
			this.value = value;
		}

		setName(name) {
			this.name = name;
			return this;
		}

		getName() {
			return this.name;
		}

		setValue(value) {
			this.value = value;
			return this;
		}

		getValue() {
			return this.value;
		}

		getAttributeList() {
			return this.attributeList;
		}

		setAttribute(name, value) {
			this.attributeList.set(name, value);
			return this;
		}

		getAttribute(name, value) {
			return this.attributeList.get(name, value);
		}

		getMetaList() {
			return this.metaList;
		}

		setMeta(name, value) {
			this.metaList.set(name, value);
			return this;
		}

		getMeta(name, value) {
			return this.getMetaList().get(name, value);
		}
	}

	exports.Node = Node;

	class NodeConverter extends converter_1.AbstractConverter {
		constructor() {
			super();
			this.register(['object'], ['node']);
			this.register(['node'], ['object']);
			this.register(['application/json'], ['node']);
			this.register(['node'], ['application/json']);
		}

		convert(content, mime, target) {
			switch (target) {
				case 'node':
					switch (mime) {
						case 'application/json':
							return new converter_1.Content(e3_3.e3.nodeFromJson(content), 'node');
						case 'object':
							return new converter_1.Content(e3_3.e3.toNode(content), 'node');
					}
					break;
				case 'application/json':
					return new converter_1.Content(e3_3.e3.toJsonNode(content), 'application/json');
				case 'object':
					return new converter_1.Content(e3_3.e3.fromNode(content), 'object');
			}
			throw new Error('Cannot convert [' + mime + '] to [' + target + '] in node converter.');
		}
	}

	exports.NodeConverter = NodeConverter;
});
define("edde/protocol", ["require", "exports", "edde/element", "edde/e3", "edde/converter"], function (require, exports, element_1, e3_4, converter_2) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class ProtocolService {
		constructor(eventBus) {
			this.handleList = e3_4.e3.hashMap({
				'packet': this.handlePacket,
				'event': this.handleEvent,
			});
			this.eventBus = eventBus;
		}

		execute(element) {
			return this.handleList.get(element.getType()).call(this, element);
		}

		handlePacket(element) {
			const packet = new element_1.PacketElement('client');
			packet.setReference(element);
			packet.reference(element);
			element.getElementList('elements').each(node => {
				const response = this.execute(node);
				if (response) {
					packet.element(response.setReference(node));
					packet.reference(node);
				}
			});
			return packet;
		}

		handleEvent(element) {
			this.eventBus.event(element);
		}
	}

	exports.ProtocolService = ProtocolService;

	class RequestService {
		constructor(url) {
			this.accept = 'application/json';
			this.target = 'application/json';
			this.url = url;
		}

		setAccept(accept) {
			this.accept = accept;
			return this;
		}

		setTarget(target) {
			this.target = target;
			return this;
		}

		request(element, callback) {
			const ajax = e3_4.e3.ajax(this.url);
			ajax.setAccept(this.accept)
				.getAjaxHandler()
				.error(xmlHttpRequest => e3_4.e3.emit('request-service/error', {'request': xmlHttpRequest, 'element': element}))
				.timeout(xmlHttpRequest => e3_4.e3.emit('request-service/timeout', {'request': xmlHttpRequest, 'element': element}))
				.fail(xmlHttpRequest => e3_4.e3.emit('request-service/fail', {'request': xmlHttpRequest, 'element': element}))
				.success(xmlHttpRequest => {
					const packet = e3_4.e3.elementFromJson(xmlHttpRequest.responseText);
					e3_4.e3.emit('request-service/success', {'request': xmlHttpRequest, 'element': element, 'packet': packet});
					let response;
					callback && (response = packet.getReferenceBy(element.getId())) ? callback(response) : null;
					e3_4.e3.job(packet).execute();
				});
			return ajax.execute(e3_4.e3.convert(e3_4.e3.ElementQueue().createPacket().addElement('elements', element), 'node', [this.target]));
		}
	}

	exports.RequestService = RequestService;

	class ElementConverter extends converter_2.AbstractConverter {
		constructor() {
			super();
			this.register(['node'], ['application/x-www-form-urlencoded+edde/protocol']);
		}

		convert(content, mime, target) {
			switch (target) {
				case 'application/x-www-form-urlencoded+edde/protocol':
					return new converter_2.Content(e3_4.e3.formEncode(e3_4.e3.convert(content, 'node', ['object']).getContent()), 'application/x-www-form-urlencoded');
			}
			throw new Error('Cannot convert [' + mime + '] to [' + target + '] in element converter.');
		}
	}

	exports.ElementConverter = ElementConverter;
});
define("edde/element", ["require", "exports", "edde/node", "edde/collection", "edde/e3"], function (require, exports, node_1, collection_1, e3_5) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class ProtocolElement extends node_1.Node {
		constructor(type, id) {
			super(type || null);
			id ? this.setAttribute('id', id) : this.getId();
		}

		getType() {
			const name = this.getName();
			if (name) {
				return name;
			}
			throw "There is an element [" + e3_5.e3.getInstanceName(this) + "] without type! This is quite strange, isn't it?";
		}

		isType(type) {
			return this.getType() === type;
		}

		getId() {
			let id = this.getAttribute('id', false);
			if (id === false) {
				this.setAttribute('id', id = e3_5.e3.guid());
			}
			return id;
		}

		async(async) {
			this.setAttribute('async', async);
			return this;
		}

		isAsync() {
			return this.getAttribute('async', false);
		}

		setReference(element) {
			this.setAttribute('reference', element.getId());
			return this;
		}

		hasReference() {
			return this.getAttribute('reference', false) !== false;
		}

		getReference() {
			return this.getAttribute('reference', null);
		}

		data(data) {
			this.metaList.put(data);
			return this;
		}

		addElement(name, element) {
			let node = null;
			if ((node = this.getElementNode(name)) === null || node.getName() !== name) {
				this.addNode(node = new ProtocolElement(name));
			}
			node.addNode(element);
			return this;
		}

		addElementCollection(name, collection) {
			collection.each(element => this.addElement(name, element));
			return this;
		}

		getElementNode(name) {
			let node = null;
			this.nodeList.each((current) => {
				if (current.getName() === name) {
					node = current;
					return false;
				}
			});
			return node;
		}

		getElementList(name) {
			const node = this.getElementNode(name);
			return node ? node.getNodeList() : e3_5.e3.collection();
		}

		getReferenceBy(id) {
			return this.getReferenceList(id).first();
		}

		getReferenceList(id) {
			const collection = e3_5.e3.collection();
			if (this.hasReference() && this.getReference() === id) {
				collection.add(this);
			}
			e3_5.e3.tree(this, (element) => {
				if (element.hasReference() && element.getReference() === id) {
					collection.add(element);
				}
			});
			return collection;
		}
	}

	exports.ProtocolElement = ProtocolElement;

	class EventElement extends ProtocolElement {
		constructor(event) {
			super('event');
			this.setAttribute('event', event);
		}
	}

	exports.EventElement = EventElement;

	class PacketElement extends ProtocolElement {
		constructor(origin, id) {
			super('packet', id);
			this.setAttribute('version', '1.1');
			this.setAttribute('origin', origin);
		}

		element(element) {
			this.addElement('elements', element);
			return this;
		}

		reference(element) {
			this.addElement('references', element);
			return this;
		}
	}

	exports.PacketElement = PacketElement;

	class ErrorElement extends ProtocolElement {
		constructor(code, message) {
			super('error');
			this.setAttribute('code', code);
			this.setAttribute('message', message);
		}

		setException(exception) {
			this.setAttribute('exception', exception);
			return this;
		}
	}

	exports.ErrorElement = ErrorElement;

	class RequestElement extends ProtocolElement {
		constructor(request) {
			super('request');
			this.setAttribute('request', request);
		}
	}

	exports.RequestElement = RequestElement;

	class MessageElement extends ProtocolElement {
		constructor(request) {
			super('message');
			this.setAttribute('request', request);
		}
	}

	exports.MessageElement = MessageElement;

	class ResponseElement extends ProtocolElement {
		constructor() {
			super('response');
		}
	}

	exports.ResponseElement = ResponseElement;

	class ElementQueue extends collection_1.Collection {
		queue(element) {
			this.add(element);
			return this;
		}

		createPacket() {
			const packet = new PacketElement('client').addElementCollection('elements', this);
			this.clear();
			return packet;
		}
	}

	exports.ElementQueue = ElementQueue;
});
define("edde/event", ["require", "exports", "edde/element", "edde/e3"], function (require, exports, element_2, e3_6) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class EventBus {
		constructor() {
			this.listenerList = e3_6.e3.hashMapCollection();
		}

		listen(event, handler, weight = 100, context, scope, cancelable) {
			event = event || '::proxy';
			this.listenerList.add(event, {
				'event': event,
				'handler': handler,
				'weight': weight,
				'context': context || null,
				'scope': scope || null,
				'cancelable': cancelable || true,
			});
			this.listenerList.sort(event, (alpha, beta) => beta.weight - alpha.weight);
			return this;
		}

		register(instance, scope) {
			e3_6.e3.$$(instance, (name, value) => name.indexOf('::ListenerList/', 0) !== -1 ? e3_6.e3.$(value, listener => this.listen(listener.event, (listener.context || instance)[listener.handler], listener.weight, listener.context || instance, listener.scope || scope, listener.cancelable)) : null);
			return this;
		}

		remove(scope) {
			scope ? this.listenerList.removeBy(listener => listener.scope !== scope) : this.listenerList.clear();
			return this;
		}

		event(event) {
			this.listenerList.each(event.getAttribute('event') || '', listener => listener.cancelable && event.getMeta('cancel', false) === true ? null : listener.handler.call(listener.context || listener.handler, event));
			this.listenerList.each('::proxy', listener => listener.cancelable && event.getMeta('cancel', false) === true ? null : listener.handler.call(listener.context || listener.handler, event));
			return this;
		}

		emit(event, data = {}) {
			this.event(new element_2.EventElement(event).data(data));
			return this;
		}
	}

	exports.EventBus = EventBus;
});
define("edde/job", ["require", "exports", "edde/e3"], function (require, exports, e3_7) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class JobManager {
		constructor() {
			this.jobQueue = e3_7.e3.collection();
		}

		queue(element) {
			this.jobQueue.add(element);
			return this;
		}

		execute() {
			if (this.jobQueue.isEmpty()) {
				return this;
			}
			if (this.queueId) {
				setTimeout(() => this.execute(), 300);
				return this;
			}
			const jobQueue = e3_7.e3.collection().copy(this.jobQueue);
			this.jobQueue.clear();
			this.queueId = setTimeout(() => {
				jobQueue.each(element => e3_7.e3.execute(element));
				jobQueue.clear();
				this.queueId = null;
				this.execute();
			}, 0);
			return this;
		}
	}

	exports.JobManager = JobManager;
});
define("edde/e3", ["require", "exports", "edde/collection", "edde/dom", "edde/event", "edde/element", "edde/node", "edde/protocol", "edde/ajax", "edde/job", "edde/converter"], function (require, exports, collection_2, dom_1, event_1, element_3, node_2, protocol_1, ajax_1, job_1, converter_3) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class e3 {
		version() {
			return '3.1.2.78';
		}

		static EventBus() {
			return this.eventBus ? this.eventBus : this.eventBus = new event_1.EventBus();
		}

		static ProtocolService() {
			return this.protocolService ? this.protocolService : this.protocolService = new protocol_1.ProtocolService(this.EventBus());
		}

		static RequestService(url) {
			return this.requestService ? this.requestService : this.requestService = new protocol_1.RequestService(url || this.el(document.body).data('protocol'));
		}

		static ElementQueue() {
			return this.elementQueue ? this.elementQueue : this.elementQueue = new element_3.ElementQueue();
		}

		static JobManager() {
			return this.jobManager ? this.jobManager : this.jobManager = new job_1.JobManager();
		}

		static ConverterManager() {
			if (this.converterManager) {
				return this.converterManager;
			}
			this.converterManager = new converter_3.ConverterManager();
			this.converterManager.registerConverter(new converter_3.JsonConverter());
			this.converterManager.registerConverter(new node_2.NodeConverter());
			this.converterManager.registerConverter(new protocol_1.ElementConverter());
			return this.converterManager;
		}

		static Event(event, data = {}) {
			return new element_3.EventElement(event).data(data);
		}

		static Message(message, data = {}) {
			return new element_3.MessageElement(message).data(data);
		}

		static Element(type) {
			return new element_3.ProtocolElement(type);
		}

		static Node(name = null, value = null) {
			return new node_2.Node(name, value);
		}

		static collection(collection = []) {
			return new collection_2.Collection(collection);
		}

		static $(collection, callback) {
			return new collection_2.Collection(collection).each(callback);
		}

		static collectionEach(collection, callback) {
			return this.$(collection, callback);
		}

		static hashMap(hashMap = {}) {
			return new collection_2.HashMap(hashMap);
		}

		static $$(hashMap, callback) {
			return this.hashMap(hashMap).each(callback);
		}

		static hashMapEach(hashMap, callback) {
			return this.$$(hashMap, callback);
		}

		static hashMapCollection() {
			return new collection_2.HashMapCollection();
		}

		static El(name, classList = [], factoryDocument) {
			return new dom_1.HtmlElement((factoryDocument || document).createElement(name)).addClassList(classList);
		}

		static Text(text, factoryDocument) {
			return (factoryDocument || document).createTextNode(text);
		}

		static el(element) {
			return new dom_1.HtmlElement(element);
		}

		static html(html) {
			const node = document.createElement('div');
			node.innerHTML = html;
			return e3.el(node.firstChild);
		}

		static Selector(selector) {
			if (selector.match(/^\.[a-zA-Z0-9_-]+$/)) {
				return new dom_1.SimpleClassSelector(selector.substr(1));
			}
			else if (selector.match(/^#[a-zA-Z0-9_-]+$/)) {
				return new dom_1.SimpleIdSelector(selector.substr(1));
			}
			return new dom_1.SimpleSelector(selector);
		}

		static reflow(callback) {
			try {
				const documentElement = document.documentElement;
				const parentNode = documentElement.parentNode;
				const nextSibling = documentElement.nextSibling;
				let result = undefined;
				if (parentNode) {
					parentNode.removeChild(documentElement);
					result = callback();
					parentNode.insertBefore(documentElement, nextSibling);
				}
				return result;
			}
			catch (e) {
				return callback();
			}
		}

		static selector(selector, root) {
			return new dom_1.HtmlElementCollection(root || document.body, selector);
		}

		static listen(event, handler, weight = 100, context, scope) {
			return this.EventBus().listen(event, handler, weight, context, scope);
		}

		static listener(instance, scope) {
			this.EventBus().register(instance, scope);
			return instance;
		}

		static unlisten(scope) {
			return this.EventBus().remove(scope);
		}

		static event(event) {
			return this.EventBus().event(event);
		}

		static emit(event, data = {}) {
			return this.EventBus().emit(event, data);
		}

		static request(element, callback) {
			return this.RequestService().request(element, callback);
		}

		static execute(element) {
			return e3.ProtocolService().execute(element);
		}

		static job(element) {
			return element ? this.JobManager().queue(element) : this.JobManager().execute();
		}

		static create(create, parameterList = [], singleton = false) {
			if (singleton === true && this.classList.has(create)) {
				return this.classList.get(create);
			}
			try {
				let module = create.split(':');
				const constructor = require(module[0])[module[1]];
				const instance = parameterList.length > 0 ? ((callback, parameterList) => {
					const constructor = callback;

					class Constructor {
						constructor() {
							constructor.apply(this, parameterList);
						}
					}

					Constructor.prototype = constructor.prototype;
					return new Constructor;
				})(constructor, parameterList) : new constructor;
				singleton ? this.classList.set(create, instance) : null;
				return instance;
			}
			catch (e) {
				throw new Error("Cannot create [" + create + "]:\n" + e);
			}
		}

		static queue(element) {
			return this.ElementQueue().queue(element);
		}

		static toNode(object, node, factory) {
			const callback = factory || ((name) => {
				return new node_2.Node(name ? name : null);
			});
			let root = node || callback();
			e3.$$(object, (name, value) => {
				if (name === '::name') {
					root.setName(value);
				}
				else if (name === '::value') {
					root.setValue(value);
				}
				else if (name === '::attr') {
					root.getAttributeList().put(value);
				}
				else if (name === '::meta') {
					root.getMetaList().put(value);
				}
				else if (e3.isObject(value)) {
					root.addNode(this.toNode(value, callback(name), factory));
				}
				else if (e3.isArray(value)) {
					e3.$(value, value2 => root.addNode(this.toNode(value2, callback(name), factory)));
				}
				else {
					root.setAttribute(name, value);
				}
			});
			if (root.getName() === null && root.getNodeCount() === 1) {
				return root.getNodeList().first().detach();
			}
			return root;
		}

		static fromNode(root) {
			const attributeList = root.getAttributeList();
			const metaList = root.getMetaList();
			const value = root.getValue();
			let object = {};
			if (value) {
				object['::value'] = value;
			}
			if (attributeList.isEmpty() === false) {
				object = e3.extend(object, attributeList.toObject());
			}
			if (metaList.isEmpty() === false) {
				object['::meta'] = metaList.toObject();
			}
			const nodeList = e3.hashMapCollection();
			root.each((node) => nodeList.add(node.getName() || '<node>', this.fromNode(node)));
			nodeList.eachCollection((name, collection) => object[name] = collection.getCount() === 1 ? collection.first() : collection.toArray());
			if (root.isRoot()) {
				const rootObject = {};
				rootObject[root.getName() || '<root>'] = object;
				return rootObject;
			}
			return object;
		}

		static convert(content, mime, targetList) {
			return this.ConverterManager().convert(content, mime, targetList).convert();
		}

		static toJsonNode(root) {
			return JSON.stringify(this.fromNode(root));
		}

		static nodeFromJson(json, factory) {
			return this.toNode(JSON.parse(json || '{}'), null, factory);
		}

		static elementFromJson(json) {
			return this.nodeFromJson(json, (name) => {
				return new element_3.ProtocolElement(name);
			});
		}

		static elementFromObject(object) {
			return this.toNode(object, null, (name) => {
				return new element_3.ProtocolElement(name);
			});
		}

		static tree(root, callback) {
			root.each((node) => this.tree(node, callback));
			return callback(root);
		}

		static ajax(url) {
			return new ajax_1.Ajax(url);
		}

		static packet(selector, root) {
			this.el(root || document.body).collection(selector || '.packet').each(node => this.job(this.elementFromJson(node.getInnerHtml())));
			this.job();
		}

		static heartbeat(interval = 3000) {
			if (this.heartbeatId) {
				return this;
			}
			const heartbeat = () => this.request(e3.Event('protocol/heartbeat')).always(() => this.heartbeatId = setTimeout(heartbeat, interval));
			this.heartbeatId = setTimeout(heartbeat, interval);
			this.listen('heartbeat/stop', () => clearTimeout(this.heartbeatId), 0);
		}

		static extend(...objectList) {
			const hasOwnProperty = Object.prototype.hasOwnProperty;
			objectList[0] = objectList[0] || {};
			for (let object of objectList) {
				if (object) {
					for (let key in object) {
						if (hasOwnProperty.call(object, key)) {
							objectList[0][key] = object[key];
						}
					}
				}
			}
			return objectList[0];
		}

		static formEncode(object, prefix) {
			let list = [];
			const encode = (key, value, prefix) => {
				const name = prefix ? prefix + '[' + key + ']' : key;
				list[list.length] = this.isScalar(value) === false ? this.formEncode(value, name) : (encodeURIComponent(name) + '=' + encodeURIComponent(value == null ? '' : value));
			};
			this.isArray(object) ? this.$(object, (value, index) => encode(String(index), value, prefix)) : this.$$(object, (key, value) => encode(key, value, prefix));
			return list.join('&').replace(/%20/g, '+');
		}

		static getInstanceName(instance) {
			return (instance.constructor.name ? instance.constructor.name : ("" + instance.constructor).split("function ")[1].split("(")[0]);
		}

		static isScalar(value) {
			switch (typeof value) {
				case 'string':
				case 'number':
				case 'boolean':
					return true;
			}
			return false;
		}

		static isArray(value) {
			if (this.isScalar(value)) {
				return false;
			}
			return value && value.length !== undefined && this.getInstanceName(value) === 'Array';
		}

		static isObject(value) {
			if (this.isScalar(value)) {
				return false;
			}
			return value && typeof value === 'object' && this.isArray(value) === false;
		}

		static isIterable(value) {
			if (this.isScalar(value)) {
				return false;
			}
			return value && value.hasOwnProperty('length') && value.hasOwnProperty(0) && value.hasOwnProperty(value.length - 1);
		}

		static guid(glue = '-', a, b) {
			for (b = a = ''; a++ < 36; b += a * 51 & 52 ? (a ^ 15 ? 8 ^ Math.random() * (a ^ 20 ? 16 : 4) : 4).toString(16) : glue)
				;
			return b;
		}
	}

	e3.classList = e3.hashMap();
	exports.e3 = e3;
});
define("edde/promise", ["require", "exports", "edde/e3"], function (require, exports, e3_8) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class AbstractPromise {
		constructor() {
			this.promiseList = e3_8.e3.hashMapCollection();
			this.resultList = e3_8.e3.hashMap();
		}

		register(name, callback) {
			if (this.resultList.has(name)) {
				callback(this.resultList.get(name));
				return this;
			}
			this.promiseList.add(name, callback);
			return this;
		}

		execute(name, value) {
			this.resultList.set(name, value);
			this.promiseList.each(name, (callback) => callback(value));
			return this;
		}

		success(callback) {
			return this.register('success', callback);
		}

		onSuccess(value) {
			return this.execute('success', value);
		}

		fail(callback) {
			return this.register('fail', callback);
		}

		onFail(value) {
			return this.execute('fail', value);
		}

		always(callback) {
			return this.register('always', callback);
		}

		onAlways(value) {
			return this.execute('always', value);
		}
	}

	exports.AbstractPromise = AbstractPromise;
});
define("edde/ajax", ["require", "exports", "edde/promise"], function (require, exports, promise_1) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class AjaxHandler extends promise_1.AbstractPromise {
		serverFail(callback) {
			this.register('server-fail', callback);
			return this;
		}

		onServerFail(xmlHttpRequest) {
			return this.execute('server-fail', xmlHttpRequest);
		}

		clientFail(callback) {
			this.register('client-fail', callback);
			return this;
		}

		onClientFail(xmlHttpRequest) {
			return this.execute('client-fail', xmlHttpRequest);
		}

		timeout(callback) {
			this.register('timeout', callback);
			return this;
		}

		onTimeout(xmlHttpRequest) {
			return this.execute('timeout', xmlHttpRequest);
		}

		error(callback) {
			this.register('error', callback);
			return this;
		}

		onError(xmlHttpRequest) {
			return this.execute('error', xmlHttpRequest);
		}
	}

	exports.AjaxHandler = AjaxHandler;

	class Ajax {
		constructor(url) {
			this.timeout = 10000;
			this.url = url;
			this.method = 'post';
			this.accept = 'application/json';
			this.async = true;
			this.ajaxHandler = new AjaxHandler();
		}

		setMethod(method) {
			this.method = method;
			return this;
		}

		setAccept(accept) {
			this.accept = accept;
			return this;
		}

		setAsync(async) {
			this.async = async;
			return this;
		}

		setTimeout(timeout) {
			this.timeout = timeout;
			return this;
		}

		getAjaxHandler() {
			return this.ajaxHandler;
		}

		execute(content) {
			const xmlHttpRequest = new XMLHttpRequest();
			try {
				let timeoutId = null;
				xmlHttpRequest.onreadystatechange = () => {
					switch (xmlHttpRequest.readyState) {
						case 0:
							break;
						case 1:
							content ? xmlHttpRequest.setRequestHeader('Content-Type', content.getMime()) : null;
							xmlHttpRequest.setRequestHeader('Accept', this.accept);
							timeoutId = setTimeout(() => {
								xmlHttpRequest.abort();
								this.ajaxHandler.onTimeout(xmlHttpRequest);
								this.ajaxHandler.onFail(xmlHttpRequest);
								this.ajaxHandler.onAlways(xmlHttpRequest);
							}, this.timeout);
							break;
						case 2:
							break;
						case 3:
							clearTimeout(timeoutId);
							timeoutId = null;
							break;
						case 4:
							try {
								if (xmlHttpRequest.status >= 200 && xmlHttpRequest.status <= 299) {
									this.ajaxHandler.onSuccess(xmlHttpRequest);
								}
								else if (xmlHttpRequest.status >= 400 && xmlHttpRequest.status <= 499) {
									this.ajaxHandler.onClientFail(xmlHttpRequest);
									this.ajaxHandler.onFail(xmlHttpRequest);
								}
								else if (xmlHttpRequest.status >= 500 && xmlHttpRequest.status <= 599) {
									this.ajaxHandler.onServerFail(xmlHttpRequest);
									this.ajaxHandler.onFail(xmlHttpRequest);
								}
							}
							catch (e) {
								this.ajaxHandler.onError(xmlHttpRequest);
								this.ajaxHandler.onFail(xmlHttpRequest);
							}
							this.ajaxHandler.onAlways(xmlHttpRequest);
							break;
					}
				};
				xmlHttpRequest.open(this.method.toUpperCase(), this.url, this.async);
				xmlHttpRequest.send(content ? content.getContent() : null);
			}
			catch (e) {
				this.ajaxHandler.onError(xmlHttpRequest);
				this.ajaxHandler.onFail(xmlHttpRequest);
				this.ajaxHandler.onAlways(xmlHttpRequest);
			}
			return this.ajaxHandler;
		}
	}

	exports.Ajax = Ajax;
});
define("edde/decorator", ["require", "exports"], function (require, exports) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class Listen {
		static To(event, weight = 100, cancelable = true) {
			return (target, property) => {
				const name = '::ListenerList/' + event + '::' + property;
				(target[name] = target[name] || []).push({
					'event': event,
					'handler': property,
					'weight': weight,
					'context': null,
					'scope': null,
					'cancelable': cancelable,
				});
			};
		}

		static ToNative(event) {
			return (target, property) => {
				const name = '::NativeListenerList/' + event + '::' + property;
				(target[name] = target[name] || []).push({
					'event': event,
					'handler': property,
				});
			};
		}
	}

	exports.Listen = Listen;
});
define("edde/client", ["require", "exports", "edde/e3", "edde/decorator"], function (require, exports, e3_9, decorator_1) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class AbstractClientClass {
		attach(htmlElement) {
			const dom = (this.element = htmlElement).getElement();
			e3_9.e3.$$(this, (name, value) => name.indexOf('::NativeListenerList/', 0) !== -1 ? e3_9.e3.$(value, (listener) => dom.addEventListener(listener.event, event => this[listener.handler].call(this, event), false)) : null);
			return this.element;
		}

		attachHtml(html) {
			return this.attach(e3_9.e3.html(html));
		}

		getElement() {
			return this.element;
		}
	}

	exports.AbstractClientClass = AbstractClientClass;

	class AbstractButton extends AbstractClientClass {
		onClick(event) {
		}
	}

	__decorate([
		decorator_1.Listen.ToNative('click')
	], AbstractButton.prototype, "onClick", null);
	exports.AbstractButton = AbstractButton;
});
define("edde/control", ["require", "exports", "edde/e3"], function (require, exports, e3_10) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});

	class AbstractControl {
		constructor(name) {
			this.name = name;
			this.element = null;
		}

		attach(element) {
			const dom = (this.element = element).getElement();
			e3_10.e3.$$(this, (name, value) => name.indexOf('::NativeListenerList/', 0) !== -1 ? e3_10.e3.$(value, (listener) => dom.addEventListener(listener.event, event => this[listener.handler].call(this, event), false)) : null);
			if (this.isListening()) {
				e3_10.e3.listener(this);
			}
			return this.element;
		}

		attachHtml(html) {
			return this.attach(e3_10.e3.html(html));
		}

		attachTo(root) {
			root.attach(this.render());
			return this;
		}

		render() {
			return this.element ? this.element : this.attach(this.element = this.build());
		}

		getElement() {
			return this.element;
		}

		update(element) {
			return this;
		}

		isListening() {
			return true;
		}
	}

	exports.AbstractControl = AbstractControl;
});
define("app/app", ["require", "exports", "edde/e3"], function (require, exports, e3_11) {
	"use strict";
	Object.defineProperty(exports, "__esModule", {value: true});
	e3_11.e3.$([], () => {
	});
	alert('dfkljdskfj');
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiLi4vc3JjL2VkZGUvY29sbGVjdGlvbi50cyIsIi4uL3NyYy9lZGRlL2RvbS50cyIsIi4uL3NyYy9lZGRlL2NvbnZlcnRlci50cyIsIi4uL3NyYy9lZGRlL25vZGUudHMiLCIuLi9zcmMvZWRkZS9wcm90b2NvbC50cyIsIi4uL3NyYy9lZGRlL2VsZW1lbnQudHMiLCIuLi9zcmMvZWRkZS9ldmVudC50cyIsIi4uL3NyYy9lZGRlL2pvYi50cyIsIi4uL3NyYy9lZGRlL2UzLnRzIiwiLi4vc3JjL2VkZGUvcHJvbWlzZS50cyIsIi4uL3NyYy9lZGRlL2FqYXgudHMiLCIuLi9zcmMvZWRkZS9kZWNvcmF0b3IudHMiLCIuLi9zcmMvZWRkZS9jbGllbnQudHMiLCIuLi9zcmMvZWRkZS9jb250cm9sLnRzIiwiLi4vc3JjL2FwcC9hcHAudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7O0lBa1FBO1FBR0MsWUFBbUIsYUFBa0IsRUFBRTtZQUN0QyxJQUFJLENBQUMsVUFBVSxHQUFHLFVBQVUsQ0FBQztRQUM5QixDQUFDO1FBS00sR0FBRyxDQUFDLElBQU87WUFDakIsTUFBTSxLQUFLLEdBQUcsSUFBSSxDQUFDLE9BQU8sRUFBRSxDQUFDO1lBQzdCLEtBQUssQ0FBQyxLQUFLLENBQUMsTUFBTSxDQUFDLEdBQUcsSUFBSSxDQUFDO1lBQzNCLE1BQU0sQ0FBQyxJQUFJLENBQUM7UUFDYixDQUFDO1FBS00sSUFBSSxDQUFxQixRQUE2RDtZQUM1RixNQUFNLE9BQU8sR0FBUztnQkFDckIsS0FBSyxFQUFFLENBQUMsQ0FBQztnQkFDVCxJQUFJLEVBQUUsS0FBSztnQkFDWCxJQUFJLEVBQUUsSUFBSTtnQkFDVixLQUFLLEVBQUUsSUFBSTtnQkFDWCxHQUFHLEVBQUUsSUFBSTthQUNULENBQUM7WUFDRixNQUFNLEtBQUssR0FBRyxJQUFJLENBQUMsT0FBTyxFQUFFLENBQUM7WUFDN0IsTUFBTSxNQUFNLEdB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QUMsUUFBUSxDQUFDLE9BQU8sSUFBSSxRQUFRLENBQUMsQ0FBTSxRQUFRLENBQUMsT0FBTyxDQUFDLEVBQUUsUUFBUSxDQUFDLE1BQU0sRUFBRSxRQUFRLENBQUMsT0FBTyxJQUFJLFFBQVEsRUFBRSxRQUFRLENBQUMsS0FBSyxJQUFJLEtBQUssRUFBRSxRQUFRLENBQUMsVUFBVSxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsSUFBSSxDQUFDLENBQUM7WUFDclMsTUFBTSxDQUFDLElBQUksQ0FBQztRQUNiLENBQUM7UUFLTSxNQUFNLENBQUMsS0FBYztZQUMzQixLQUFLLENBQUMsQ0FBQyxDQUFDLElBQUksQ0FBQyxZQUFZLENBQUMsUUFBUSxDQUFDLFFBQVEsQ0FBQyxFQUFFLENBQUMsUUFBUSxDQUFDLEtBQUssS0FBSyxLQUFLLENBQUMsQ0FBQyxDQUFDLENBQUMsSUFBSSxDQUFDLFlBQVksQ0FBQyxLQUFLLEVBQUUsQ0FBQztZQUNyRyxNQUFNLENBQUMsSUFBSSxDQUFDO1FBQ2IsQ0FBQztRQUtNLEtBQUssQ0FBQyxLQUFlO1lBQzNCLElBQUksQ0FBQyxZQUFZLENBQUMsSUFBSSxDQUFDLEtBQUssQ0FBQyxZQUFZLENBQUMsT0FBTyxDQUFDLElBQUksRUFBRSxFQUFFLFFBQVEsQ0FBQyxFQUFFLENBQUMsUUFBUSxDQUFDLFVBQVUsSUFBSSxLQUFLLENBQUMsT0FBTyxDQUFDLFFBQVEsRUFBRSxLQUFLLENBQUMsS0FBSyxJQUFJLENBQUMsQ0FBQyxDQUFDLElBQUksQ0FBQyxDQUFDLENBQUMsUUFBUSxDQUFDLE9BQU8sQ0FBQyxJQUFJLENBQUMsUUFBUSxDQUFDLE9BQU8sSUFBSSxRQUFRLENBQUMsT0FBTyxFQUFFLEtBQUssQ0FBQyxDQUFDLENBQUM7WUFDbE4sSUFBSSxDQUFDLFlBQVksQ0FBQyxJQUFJLENBQUMsU0FBUyxFQUFFLFFBQVEsQ0FBQyxFQUFFLENBQUMsUUFBUSxDQUFDLFVBQVUsSUFBSSxLQUFLLENBQUMsT0FBTyxDQUFDLFFBQVEsRUFBRSxLQUFLLENBQUMsS0FBSyxJQUFJLENB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
