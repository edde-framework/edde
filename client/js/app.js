var __extends = (this && this.__extends) || (function () {
	var extendStatics = Object.setPrototypeOf ||
		({__proto__: []} instanceof Array && function (d, b) {
			d.__proto__ = b;
		}) ||
		function (d, b) {
			for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
		};
	return function (d, b) {
		extendStatics(d, b);

		function __() {
			this.constructor = d;
		}

		d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
	};
})();
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
	var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
	if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
	else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
	return c > 3 && r && Object.defineProperty(target, key, r), r;
};
define("edde/collection", ["require", "exports"], function (require, exports) {
	"use strict";
	exports.__esModule = true;
	var Collection = (function () {
		function Collection(collection) {
			if (collection === void 0) {
				collection = [];
			}
			this.collection = collection;
		}

		Collection.prototype.add = function (item) {
			var array = this.toArray();
			array[array.length] = item;
			return this;
		};
		Collection.prototype.each = function (callback) {
			var context = {
				count: -1,
				loop: false,
				item: null,
				value: null,
				key: null
			};
			var array = this.toArray();
			var length = array.length;
			for (context.count = 0; context.count < length; context.key = context.count++) {
				context.loop = true;
				if (callback.call(context, context.value = array[context.count], context.count) === false) {
					break;
				}
			}
			return context;
		};
		Collection.prototype.subEach = function (callback, start, length) {
			return this.subCollection(start, length).each(callback);
		};
		Collection.prototype.subCollection = function (start, length) {
			if (!this.collection) {
				return new Collection();
			}
			var collectionLength = this.collection.length;
			start = start || 0;
			length = start + (length || collectionLength);
			var items = [];
			for (var i = start; i < length && i < collectionLength; i++) {
				items[items.length] = this.collection[i];
			}
			return new Collection(items);
		};
		Collection.prototype.reverse = function () {
			this.collection = this.toArray().reverse();
			return this;
		};
		Collection.prototype.toArray = function () {
			return this.collection ? this.collection : this.collection = [];
		};
		Collection.prototype.getCount = function () {
			return this.toArray().length;
		};
		Collection.prototype.index = function (index) {
			if (!this.collection || index >= this.collection.length) {
				return null;
			}
			return this.collection[index];
		};
		Collection.prototype.first = function () {
			return this.collection && this.collection.length > 0 ? this.collection[0] : null;
		};
		Collection.prototype.last = function () {
			return this.collection && this.collection.length > 0 ? this.collection[this.collection.length - 1] : null;
		};
		Collection.prototype.isEmpty = function () {
			return this.getCount() === 0;
		};
		Collection.prototype.clear = function () {
			this.collection = [];
			return this;
		};
		Collection.prototype.removeBy = function (callback, name) {
			var collection = [];
			this.each(function (value) {
				if (callback(value) !== false) {
					collection[collection.length] = value;
				}
			});
			this.collection = collection;
			return this;
		};
		Collection.prototype.copy = function (copy) {
			this.collection = this.toArray().concat(copy.toArray());
			return this;
		};
		Collection.prototype.replace = function (replace) {
			this.collection = replace.toArray();
			return this;
		};
		Collection.prototype.sort = function (sort) {
			this.collection = this.toArray().sort(sort);
			return this;
		};
		return Collection;
	}());
	exports.Collection = Collection;
	var HashMap = (function () {
		function HashMap(hashMap) {
			if (hashMap === void 0) {
				hashMap = {};
			}
			this.hashMap = hashMap;
		}

		HashMap.prototype.set = function (name, item) {
			this.hashMap[name] = item;
			return this;
		};
		HashMap.prototype.put = function (put) {
			this.hashMap = put;
			return this;
		};
		HashMap.prototype.has = function (name) {
			return this.hashMap.hasOwnProperty(name);
		};
		HashMap.prototype.get = function (name, value) {
			return this.hashMap.hasOwnProperty(name) ? this.hashMap[name] : value;
		};
		HashMap.prototype.remove = function (name) {
			this.hashMap[name] = null;
			delete this.hashMap[name];
			return this;
		};
		HashMap.prototype.isEmpty = function () {
			var hasOwnProperty = Object.prototype.hasOwnProperty;
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
			for (var key in this.hashMap) {
				if (hasOwnProperty.call(this.hashMap, key)) {
					return false;
				}
			}
			return true;
		};
		HashMap.prototype.toObject = function () {
			return this.hashMap;
		};
		HashMap.prototype.each = function (callback) {
			var context = {
				count: -1,
				loop: false,
				item: null,
				value: null,
				key: null
			};
			if (!this.hashMap) {
				return context;
			}
			for (var key in this.hashMap) {
				context.loop = true;
				context.count++;
				if (callback.call(context, context.key = key, context.value = this.hashMap[key]) === false) {
					break;
				}
			}
			return context;
		};
		HashMap.prototype.first = function () {
			return this.each(function () {
				return false;
			}).value;
		};
		HashMap.prototype.last = function () {
			return this.each(function () {
				return true;
			}).value;
		};
		HashMap.prototype.getCount = function () {
			return this.each(function () {
				return true;
			}).count + 1;
		};
		HashMap.prototype.clear = function () {
			this.hashMap = {};
			return this;
		};
		HashMap.prototype.copy = function (copy) {
			var _this = this;
			copy.each(function (k, v) {
				return _this.set(k, v);
			});
			return this;
		};
		HashMap.prototype.replace = function (replace) {
			this.hashMap = replace.toObject();
			return this;
		};
		HashMap.prototype.fromCollection = function (collection, key) {
			var _this = this;
			collection.each(function (value) {
				return _this.set(key(value), value);
			});
			return this;
		};
		return HashMap;
	}());
	exports.HashMap = HashMap;
	var HashMapCollection = (function () {
		function HashMapCollection() {
			this.hashMap = new HashMap();
		}

		HashMapCollection.prototype.add = function (name, item) {
			if (this.hashMap.has(name) === false) {
				this.hashMap.set(name, new Collection());
			}
			this.hashMap.get(name).add(item);
			return this;
		};
		HashMapCollection.prototype.has = function (name) {
			return this.hashMap.has(name);
		};
		HashMapCollection.prototype.sort = function (name, sort) {
			this.toCollection(name).sort(sort);
			return this;
		};
		HashMapCollection.prototype.toArray = function (name) {
			return this.hashMap.get(name, new Collection()).toArray();
		};
		HashMapCollection.prototype.toCollection = function (name) {
			return this.hashMap.get(name, new Collection());
		};
		HashMapCollection.prototype.each = function (name, callback) {
			return this.toCollection(name).each(callback);
		};
		HashMapCollection.prototype.eachCollection = function (callback) {
			return this.hashMap.each(callback);
		};
		HashMapCollection.prototype.remove = function (name) {
			this.hashMap.remove(name);
			return this;
		};
		HashMapCollection.prototype.removeBy = function (callback, name) {
			if (name) {
				this.toCollection(name).removeBy(callback);
				return this;
			}
			this.hashMap.each(function (_, item) {
				return item.removeBy(callback);
			});
			return this;
		};
		HashMapCollection.prototype.clear = function () {
			this.hashMap = new HashMap();
			return this;
		};
		return HashMapCollection;
	}());
	exports.HashMapCollection = HashMapCollection;
});
define("edde/dom", ["require", "exports", "edde/e3"], function (require, exports, e3_1) {
	"use strict";
	exports.__esModule = true;
	var HtmlElement = (function () {
		function HtmlElement(element) {
			this.element = element;
		}

		HtmlElement.prototype.getElement = function () {
			return this.element;
		};
		HtmlElement.prototype.getId = function () {
			return this.element.getAttribute('id') || '';
		};
		HtmlElement.prototype.getName = function () {
			return this.element.nodeName.toLowerCase();
		};
		HtmlElement.prototype.event = function (name, callback) {
			var _this = this;
			this.element.addEventListener(name, function (event) {
				return callback.call(_this, event);
			}, false);
			return this;
		};
		HtmlElement.prototype.data = function (name, value) {
			return this.element.getAttribute('data-' + name) || value;
		};
		HtmlElement.prototype.toggleClass = function (name, toggle) {
			var hasClass = this.hasClass(name);
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
		};
		HtmlElement.prototype.toggleClassList = function (nameList, toggle) {
			var _this = this;
			e3_1.e3.$(nameList, function (item) {
				return _this.toggleClass(item, toggle);
			});
			return this;
		};
		HtmlElement.prototype.addClass = function (name) {
			if (this.hasClass(name)) {
				return this;
			}
			this.element.className += ' ' + name;
			this.element.className = this.className(this.element.className);
			return this;
		};
		HtmlElement.prototype.addClassList = function (nameList) {
			var _this = this;
			e3_1.e3.$(nameList, function (item) {
				return _this.addClass(item);
			});
			return this;
		};
		HtmlElement.prototype.hasClass = function (name) {
			return this.element.className !== undefined && (' ' + this.element.className + ' ').indexOf(' ' + name + ' ') !== -1;
		};
		HtmlElement.prototype.hasClassList = function (nameList) {
			var _this = this;
			var hasClass = false;
			e3_1.e3.$(nameList, function (item) {
				hasClass = true;
				if (_this.hasClass(item) === false) {
					hasClass = false;
					return false;
				}
			});
			return hasClass;
		};
		HtmlElement.prototype.removeClass = function (name) {
			this.element.className = this.className(this.element.className.replace(name, ''));
			return this;
		};
		HtmlElement.prototype.removeClassList = function (nameList) {
			var _this = this;
			e3_1.e3.$(nameList, function (item) {
				return _this.removeClass(item);
			});
			return this;
		};
		HtmlElement.prototype.html = function (html) {
			this.element.innerHTML = html;
			return this;
		};
		HtmlElement.prototype.text = function (text) {
			this.clear();
			this.element.appendChild(e3_1.e3.Text(text));
			return this;
		};
		HtmlElement.prototype.attr = function (name, value) {
			this.element.setAttribute(name, value);
			return this;
		};
		HtmlElement.prototype.attrList = function (attrList) {
			var _this = this;
			e3_1.e3.$$(attrList, function (name, value) {
				return _this.attr(name, value);
			});
			return this;
		};
		HtmlElement.prototype.removeAttribute = function (name) {
			this.element.removeAttribute(name);
			return this;
		};
		HtmlElement.prototype.getInnerHtml = function () {
			return this.element.innerHTML;
		};
		HtmlElement.prototype.getOuterHtml = function () {
			return this.element.outerHTML;
		};
		HtmlElement.prototype.clear = function () {
			this.html('');
			return this;
		};
		HtmlElement.prototype.getOffsetHeight = function () {
			return this.element.offsetHeight;
		};
		HtmlElement.prototype.getOffsetWidth = function () {
			return this.element.offsetWidth;
		};
		HtmlElement.prototype.getClientHeight = function () {
			return this.element.clientHeight;
		};
		HtmlElement.prototype.getClientWidth = function () {
			return this.element.clientWidth;
		};
		HtmlElement.prototype.getParentList = function (root) {
			var elementList = [this];
			var parent = this.element.parentNode;
			while (parent) {
				if (parent === root) {
					break;
				}
				elementList[elementList.length] = new HtmlElement(parent);
				parent = parent.parentNode;
			}
			return e3_1.e3.collection(elementList);
		};
		HtmlElement.prototype.collection = function (selector) {
			return e3_1.e3.selector(selector, this.element);
		};
		HtmlElement.prototype.attach = function (child) {
			this.element.appendChild(child.getElement());
			return this;
		};
		HtmlElement.prototype.attachHtml = function (html) {
			return this.attach(e3_1.e3.html(html));
		};
		HtmlElement.prototype.attachList = function (elementList) {
			var _this = this;
			e3_1.e3.$(elementList, function (element) {
				return element ? _this.attach(element) : null;
			});
			return this;
		};
		HtmlElement.prototype.attachTo = function (parent) {
			parent.attach(this);
			return this;
		};
		HtmlElement.prototype.position = function (top, left) {
			this.element.style.top = top;
			this.element.style.left = left;
			return this;
		};
		HtmlElement.prototype.remove = function () {
			this.element.parentNode ? this.element.parentNode.removeChild(this.element) : null;
			this.element = null;
			return this;
		};
		HtmlElement.prototype.className = function (name) {
			return (name.match(/[^\x20\t\r\n\f]+/g) || []).join(' ');
		};
		return HtmlElement;
	}());
	exports.HtmlElement = HtmlElement;
	var HtmlElementCollection = (function () {
		function HtmlElementCollection(root, selector) {
			this.root = root;
			this.selector = e3_1.e3.Selector(selector);
		}

		HtmlElementCollection.prototype.event = function (name, callback) {
			this.each(function (element) {
				return element.event(name, callback);
			});
			return this;
		};
		HtmlElementCollection.prototype.addClass = function (name) {
			this.each(function (element) {
				return element.addClass(name);
			});
			return this;
		};
		HtmlElementCollection.prototype.addClassList = function (nameList) {
			this.each(function (element) {
				return element.addClassList(nameList);
			});
			return this;
		};
		HtmlElementCollection.prototype.hasClass = function (name) {
			var hasClass = false;
			this.each(function (element) {
				return (hasClass = element.hasClass(name)) !== false;
			});
			return hasClass;
		};
		HtmlElementCollection.prototype.hasClassList = function (nameList) {
			var hasClass = false;
			this.each(function (element) {
				return (hasClass = element.hasClassList(nameList)) !== false;
			});
			return hasClass;
		};
		HtmlElementCollection.prototype.removeClass = function (name) {
			this.each(function (element) {
				return element.removeClass(name);
			});
			return this;
		};
		HtmlElementCollection.prototype.removeClassList = function (nameList) {
			this.each(function (element) {
				return element.removeClassList(nameList);
			});
			return this;
		};
		HtmlElementCollection.prototype.toggleClass = function (name, toggle) {
			this.each(function (element) {
				return element.toggleClass(name, toggle);
			});
			return this;
		};
		HtmlElementCollection.prototype.toggleClassList = function (nameList, toggle) {
			this.each(function (element) {
				return element.toggleClassList(nameList, toggle);
			});
			return this;
		};
		HtmlElementCollection.prototype.attr = function (name, value) {
			this.each(function (element) {
				return element.attr(name, value);
			});
			return this;
		};
		HtmlElementCollection.prototype.attrList = function (attrList) {
			this.each(function (element) {
				return element.attrList(attrList);
			});
			return this;
		};
		HtmlElementCollection.prototype.removeAttribute = function (name) {
			this.each(function (element) {
				return element.removeAttribute(name);
			});
			return this;
		};
		HtmlElementCollection.prototype.html = function (html) {
			this.each(function (element) {
				return element.html(html);
			});
			return this;
		};
		HtmlElementCollection.prototype.each = function (callback) {
			return this.selector.each(this.root, callback);
		};
		HtmlElementCollection.prototype.getCount = function () {
			return this.selector.getCount(this.root);
		};
		HtmlElementCollection.prototype.index = function (index) {
			return this.selector.index(this.root, index);
		};
		HtmlElementCollection.prototype.remove = function () {
			this.each(function (element) {
				return element.remove();
			});
			return this;
		};
		return HtmlElementCollection;
	}());
	exports.HtmlElementCollection = HtmlElementCollection;
	var AbstractSelector = (function () {
		function AbstractSelector(selector) {
			this.selector = selector;
		}

		AbstractSelector.prototype.getCount = function (root) {
			var count = 0;
			this.each(root, function () {
				return count++;
			});
			return count;
		};
		AbstractSelector.prototype.index = function (root, index) {
			var count = 0;
			return this.each(root, function (htmlElement) {
				this.item = htmlElement;
				return count++ !== index;
			}).item;
		};
		return AbstractSelector;
	}());
	exports.AbstractSelector = AbstractSelector;
	var SimpleClassSelector = (function (_super) {
		__extends(SimpleClassSelector, _super);

		function SimpleClassSelector() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		SimpleClassSelector.prototype.each = function (root, callback) {
			var self = this;
			return e3_1.e3.$(root.getElementsByTagName('*'), function (element) {
				var htmlElement = new HtmlElement(element);
				if (htmlElement.hasClass(self.selector)) {
					return callback.call(this, htmlElement);
				}
			});
		};
		return SimpleClassSelector;
	}(AbstractSelector));
	exports.SimpleClassSelector = SimpleClassSelector;
	var SimpleIdSelector = (function (_super) {
		__extends(SimpleIdSelector, _super);

		function SimpleIdSelector() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		SimpleIdSelector.prototype.each = function (root, callback) {
			var self = this;
			return e3_1.e3.$(root.getElementsByTagName('*'), function (element) {
				var htmlElement = new HtmlElement(element);
				if (htmlElement.getId() === self.selector) {
					return callback.call(this, htmlElement);
				}
			});
		};
		return SimpleIdSelector;
	}(AbstractSelector));
	exports.SimpleIdSelector = SimpleIdSelector;
	var SimpleSelector = (function (_super) {
		__extends(SimpleSelector, _super);

		function SimpleSelector(selector) {
			var _this = _super.call(this, selector) || this;
			_this.selectorList = [];
			var selectorList = selector.split(/\s+?/);
			for (var _i = 0, selectorList_1 = selectorList; _i < selectorList_1.length; _i++) {
				var filter = selectorList_1[_i];
				if (filter === '') {
					continue;
				}
				var match = filter.match(/#[a-zA-Z0-9_-]+|\.[a-zA-Z0-9_-]+|[a-zA-Z0-9_-]+/g);
				match ? _this.selectorList[_this.selectorList.length] = match : null;
			}
			if (_this.selectorList.length !== selectorList.length) {
				throw new Error('Invalid selector [' + selector + ']');
			}
			return _this;
		}

		SimpleSelector.prototype.each = function (root, callback) {
			var self = this;
			return e3_1.e3.$(root.getElementsByTagName('*'), function (element) {
				var selectorListLength = self.selectorList.length;
				var nodeList = e3_1.e3.el(element).getParentList(root).reverse().toArray();
				if (selectorListLength > 1 && nodeList.length < selectorListLength) {
					return;
				}
				var filterId = 0;
				var count = 0;
				var matched;
				for (var _i = 0, nodeList_1 = nodeList; _i < nodeList_1.length; _i++) {
					var htmlElement = nodeList_1[_i];
					matched = false;
					var match = true;
					for (var _a = 0, _b = self.selectorList[filterId]; _a < _b.length; _a++) {
						var id = _b[_a];
						var first = id.charAt(0);
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
		};
		return SimpleSelector;
	}(AbstractSelector));
	exports.SimpleSelector = SimpleSelector;
});
define("edde/converter", ["require", "exports", "edde/e3"], function (require, exports, e3_2) {
	"use strict";
	exports.__esModule = true;
	var Content = (function () {
		function Content(content, mime) {
			this.content = content;
			this.mime = mime;
		}

		Content.prototype.getContent = function () {
			return this.content;
		};
		Content.prototype.getMime = function () {
			return this.mime;
		};
		return Content;
	}());
	exports.Content = Content;
	var AbstractConverter = (function () {
		function AbstractConverter() {
			this.mimeList = [];
		}

		AbstractConverter.prototype.register = function (source, target) {
			var _this = this;
			e3_2.e3.$(source, function (src) {
				return e3_2.e3.$(target, function (tgt) {
					return _this.mimeList[_this.mimeList.length] = src + '|' + tgt;
				});
			});
		};
		AbstractConverter.prototype.getMimeList = function () {
			return this.mimeList;
		};
		AbstractConverter.prototype.content = function (content, target) {
			return this.convert(content.getContent(), content.getMime(), target);
		};
		return AbstractConverter;
	}());
	exports.AbstractConverter = AbstractConverter;
	var Convertable = (function () {
		function Convertable(converter, content, target) {
			this.target = null;
			this.converter = converter;
			this.content = content;
			this.target = target;
		}

		Convertable.prototype.getContent = function () {
			return this.content;
		};
		Convertable.prototype.getTarget = function () {
			return this.target;
		};
		Convertable.prototype.convert = function () {
			if (this.result) {
				return this.result;
			}
			return this.result = this.converter.content(this.content, this.target);
		};
		return Convertable;
	}());
	exports.Convertable = Convertable;
	var ConverterManager = (function () {
		function ConverterManager() {
			this.converterList = e3_2.e3.hashMap();
		}

		ConverterManager.prototype.registerConverter = function (converter) {
			var _this = this;
			e3_2.e3.$(converter.getMimeList(), function (mime) {
				return _this.converterList.set(mime, converter);
			});
			return this;
		};
		ConverterManager.prototype.convert = function (content, mime, targetList) {
			return this.content(new Content(content, mime), targetList);
		};
		ConverterManager.prototype.content = function (content, targetList) {
			var _this = this;
			if (targetList === null) {
				return new Convertable(new PassConverter(), content, content.getMime());
			}
			var mime = content.getMime();
			var convertable = null;
			e3_2.e3.$(targetList, function (target) {
				var id = mime + '|' + target;
				if (mime === target) {
					convertable = new Convertable(new PassConverter(), content, content.getMime());
					return false;
				}
				else if (_this.converterList.has(id)) {
					convertable = new Convertable(_this.converterList.get(id), content, target);
					return false;
				}
			});
			if (convertable) {
				return convertable;
			}
			throw new Error('Cannot convert [' + mime + '].');
		};
		return ConverterManager;
	}());
	exports.ConverterManager = ConverterManager;
	var PassConverter = (function (_super) {
		__extends(PassConverter, _super);

		function PassConverter() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		PassConverter.prototype.convert = function (content, mime, target) {
			return new Content(content, mime);
		};
		return PassConverter;
	}(AbstractConverter));
	exports.PassConverter = PassConverter;
	var JsonConverter = (function (_super) {
		__extends(JsonConverter, _super);

		function JsonConverter() {
			var _this = _super.call(this) || this;
			_this.register([
				'object',
			], [
				'application/json',
				'json',
			]);
			_this.register([
				'application/json',
				'json',
			], [
				'object',
			]);
			return _this;
		}

		JsonConverter.prototype.convert = function (content, mime, target) {
			switch (target) {
				case 'application/json':
				case 'json':
					return new Content(JSON.stringify(content), 'application/json');
				case 'object':
					return new Content(JSON.parse(content), 'application/javascript');
			}
			throw new Error('Cannot convert [' + mime + '] to [' + target + '] in json converter.');
		};
		return JsonConverter;
	}(AbstractConverter));
	exports.JsonConverter = JsonConverter;
});
define("edde/node", ["require", "exports", "edde/e3", "edde/converter"], function (require, exports, e3_3, converter_1) {
	"use strict";
	exports.__esModule = true;
	var AbstractNode = (function () {
		function AbstractNode(parent) {
			this.nodeList = e3_3.e3.collection();
			this.parent = parent;
		}

		AbstractNode.prototype.setParent = function (parent) {
			this.parent = parent;
			return this;
		};
		AbstractNode.prototype.detach = function () {
			this.parent = null;
			return this;
		};
		AbstractNode.prototype.getParent = function () {
			return this.parent;
		};
		AbstractNode.prototype.isRoot = function () {
			return this.parent === null;
		};
		AbstractNode.prototype.isChild = function () {
			return this.parent !== null;
		};
		AbstractNode.prototype.addNode = function (node) {
			this.nodeList.add(node);
			node.setParent(this);
			return this;
		};
		AbstractNode.prototype.addNodeList = function (nodeList) {
			var _this = this;
			e3_3.e3.$(nodeList, function (node) {
				return _this.addNode(node);
			});
			return this;
		};
		AbstractNode.prototype.getNodeList = function () {
			return this.nodeList;
		};
		AbstractNode.prototype.getNodeCount = function () {
			return this.nodeList.getCount();
		};
		AbstractNode.prototype.each = function (callback) {
			return this.nodeList.each(function (node) {
				return callback.call(this, node);
			});
		};
		return AbstractNode;
	}());
	exports.AbstractNode = AbstractNode;
	var Node = (function (_super) {
		__extends(Node, _super);

		function Node(name, value) {
			if (name === void 0) {
				name = null;
			}
			if (value === void 0) {
				value = null;
			}
			var _this = _super.call(this, null) || this;
			_this.attributeList = e3_3.e3.hashMap();
			_this.metaList = e3_3.e3.hashMap();
			_this.name = name;
			_this.value = value;
			return _this;
		}

		Node.prototype.setName = function (name) {
			this.name = name;
			return this;
		};
		Node.prototype.getName = function () {
			return this.name;
		};
		Node.prototype.setValue = function (value) {
			this.value = value;
			return this;
		};
		Node.prototype.getValue = function () {
			return this.value;
		};
		Node.prototype.getAttributeList = function () {
			return this.attributeList;
		};
		Node.prototype.setAttribute = function (name, value) {
			this.attributeList.set(name, value);
			return this;
		};
		Node.prototype.getAttribute = function (name, value) {
			return this.attributeList.get(name, value);
		};
		Node.prototype.getMetaList = function () {
			return this.metaList;
		};
		Node.prototype.setMeta = function (name, value) {
			this.metaList.set(name, value);
			return this;
		};
		Node.prototype.getMeta = function (name, value) {
			return this.getMetaList().get(name, value);
		};
		return Node;
	}(AbstractNode));
	exports.Node = Node;
	var NodeConverter = (function (_super) {
		__extends(NodeConverter, _super);

		function NodeConverter() {
			var _this = _super.call(this) || this;
			_this.register(['object'], ['node']);
			_this.register(['node'], ['object']);
			_this.register(['application/json'], ['node']);
			_this.register(['node'], ['application/json']);
			return _this;
		}

		NodeConverter.prototype.convert = function (content, mime, target) {
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
		};
		return NodeConverter;
	}(converter_1.AbstractConverter));
	exports.NodeConverter = NodeConverter;
});
define("edde/promise", ["require", "exports", "edde/e3"], function (require, exports, e3_4) {
	"use strict";
	exports.__esModule = true;
	var AbstractPromise = (function () {
		function AbstractPromise() {
			this.promiseList = e3_4.e3.hashMapCollection();
			this.resultList = e3_4.e3.hashMap();
		}

		AbstractPromise.prototype.register = function (name, callback) {
			if (this.resultList.has(name)) {
				callback(this.resultList.get(name));
				return this;
			}
			this.promiseList.add(name, callback);
			return this;
		};
		AbstractPromise.prototype.execute = function (name, value) {
			this.resultList.set(name, value);
			this.promiseList.each(name, function (callback) {
				return callback(value);
			});
			return this;
		};
		AbstractPromise.prototype.success = function (callback) {
			return this.register('success', callback);
		};
		AbstractPromise.prototype.onSuccess = function (value) {
			return this.execute('success', value);
		};
		AbstractPromise.prototype.fail = function (callback) {
			return this.register('fail', callback);
		};
		AbstractPromise.prototype.onFail = function (value) {
			return this.execute('fail', value);
		};
		AbstractPromise.prototype.always = function (callback) {
			return this.register('always', callback);
		};
		AbstractPromise.prototype.onAlways = function (value) {
			return this.execute('always', value);
		};
		return AbstractPromise;
	}());
	exports.AbstractPromise = AbstractPromise;
});
define("edde/protocol", ["require", "exports", "edde/element", "edde/e3", "edde/converter"], function (require, exports, element_1, e3_5, converter_2) {
	"use strict";
	exports.__esModule = true;
	var ProtocolService = (function () {
		function ProtocolService(eventBus) {
			this.handleList = e3_5.e3.hashMap({
				'packet': this.handlePacket,
				'event': this.handleEvent
			});
			this.eventBus = eventBus;
		}

		ProtocolService.prototype.execute = function (element) {
			return this.handleList.get(element.getType()).call(this, element);
		};
		ProtocolService.prototype.handlePacket = function (element) {
			var _this = this;
			var packet = new element_1.PacketElement('client');
			packet.setReference(element);
			packet.reference(element);
			element.getElementList('elements').each(function (node) {
				var response = _this.execute(node);
				if (response) {
					packet.element(response.setReference(node));
					packet.reference(node);
				}
			});
			return packet;
		};
		ProtocolService.prototype.handleEvent = function (element) {
			this.eventBus.event(element);
		};
		return ProtocolService;
	}());
	exports.ProtocolService = ProtocolService;
	var RequestService = (function () {
		function RequestService(url) {
			this.accept = 'application/json';
			this.target = 'application/json';
			this.url = url;
		}

		RequestService.prototype.setAccept = function (accept) {
			this.accept = accept;
			return this;
		};
		RequestService.prototype.setTarget = function (target) {
			this.target = target;
			return this;
		};
		RequestService.prototype.request = function (element, callback) {
			var ajax = e3_5.e3.ajax(this.url);
			ajax.setAccept(this.accept)
				.getAjaxHandler()
				.error(function (xmlHttpRequest) {
					return e3_5.e3.emit('request-service/error', {'request': xmlHttpRequest, 'element': element});
				})
				.timeout(function (xmlHttpRequest) {
					return e3_5.e3.emit('request-service/timeout', {'request': xmlHttpRequest, 'element': element});
				})
				.fail(function (xmlHttpRequest) {
					return e3_5.e3.emit('request-service/fail', {'request': xmlHttpRequest, 'element': element});
				})
				.success(function (xmlHttpRequest) {
					var packet = e3_5.e3.elementFromJson(xmlHttpRequest.responseText);
					e3_5.e3.emit('request-service/success', {'request': xmlHttpRequest, 'element': element, 'packet': packet});
					var response;
					callback && (response = packet.getReferenceBy(element.getId())) ? callback(response) : null;
					e3_5.e3.job(packet).execute();
				});
			return ajax.execute(e3_5.e3.convert(e3_5.e3.ElementQueue().createPacket().addElement('elements', element), 'node', [this.target]));
		};
		return RequestService;
	}());
	exports.RequestService = RequestService;
	var ElementConverter = (function (_super) {
		__extends(ElementConverter, _super);

		function ElementConverter() {
			var _this = _super.call(this) || this;
			_this.register(['node'], ['application/x-www-form-urlencoded+edde/protocol']);
			return _this;
		}

		ElementConverter.prototype.convert = function (content, mime, target) {
			switch (target) {
				case 'application/x-www-form-urlencoded+edde/protocol':
					return new converter_2.Content(e3_5.e3.formEncode(e3_5.e3.convert(content, 'node', ['object']).getContent()), 'application/x-www-form-urlencoded');
			}
			throw new Error('Cannot convert [' + mime + '] to [' + target + '] in element converter.');
		};
		return ElementConverter;
	}(converter_2.AbstractConverter));
	exports.ElementConverter = ElementConverter;
});
define("edde/element", ["require", "exports", "edde/node", "edde/collection", "edde/e3"], function (require, exports, node_1, collection_1, e3_6) {
	"use strict";
	exports.__esModule = true;
	var ProtocolElement = (function (_super) {
		__extends(ProtocolElement, _super);

		function ProtocolElement(type, id) {
			var _this = _super.call(this, type || null) || this;
			id ? _this.setAttribute('id', id) : _this.getId();
			return _this;
		}

		ProtocolElement.prototype.getType = function () {
			var name = this.getName();
			if (name) {
				return name;
			}
			throw "There is an element [" + e3_6.e3.getInstanceName(this) + "] without type! This is quite strange, isn't it?";
		};
		ProtocolElement.prototype.isType = function (type) {
			return this.getType() === type;
		};
		ProtocolElement.prototype.getId = function () {
			var id = this.getAttribute('id', false);
			if (id === false) {
				this.setAttribute('id', id = e3_6.e3.guid());
			}
			return id;
		};
		ProtocolElement.prototype.async = function (async) {
			this.setAttribute('async', async);
			return this;
		};
		ProtocolElement.prototype.isAsync = function () {
			return this.getAttribute('async', false);
		};
		ProtocolElement.prototype.setReference = function (element) {
			this.setAttribute('reference', element.getId());
			return this;
		};
		ProtocolElement.prototype.hasReference = function () {
			return this.getAttribute('reference', false) !== false;
		};
		ProtocolElement.prototype.getReference = function () {
			return this.getAttribute('reference', null);
		};
		ProtocolElement.prototype.data = function (data) {
			this.metaList.put(data);
			return this;
		};
		ProtocolElement.prototype.addElement = function (name, element) {
			var node = null;
			if ((node = this.getElementNode(name)) === null || node.getName() !== name) {
				this.addNode(node = new ProtocolElement(name));
			}
			node.addNode(element);
			return this;
		};
		ProtocolElement.prototype.addElementCollection = function (name, collection) {
			var _this = this;
			collection.each(function (element) {
				return _this.addElement(name, element);
			});
			return this;
		};
		ProtocolElement.prototype.getElementNode = function (name) {
			var node = null;
			this.nodeList.each(function (current) {
				if (current.getName() === name) {
					node = current;
					return false;
				}
			});
			return node;
		};
		ProtocolElement.prototype.getElementList = function (name) {
			var node = this.getElementNode(name);
			return node ? node.getNodeList() : e3_6.e3.collection();
		};
		ProtocolElement.prototype.getReferenceBy = function (id) {
			return this.getReferenceList(id).first();
		};
		ProtocolElement.prototype.getReferenceList = function (id) {
			var collection = e3_6.e3.collection();
			if (this.hasReference() && this.getReference() === id) {
				collection.add(this);
			}
			e3_6.e3.tree(this, function (element) {
				if (element.hasReference() && element.getReference() === id) {
					collection.add(element);
				}
			});
			return collection;
		};
		return ProtocolElement;
	}(node_1.Node));
	exports.ProtocolElement = ProtocolElement;
	var EventElement = (function (_super) {
		__extends(EventElement, _super);

		function EventElement(event) {
			var _this = _super.call(this, 'event') || this;
			_this.setAttribute('event', event);
			return _this;
		}

		return EventElement;
	}(ProtocolElement));
	exports.EventElement = EventElement;
	var PacketElement = (function (_super) {
		__extends(PacketElement, _super);

		function PacketElement(origin, id) {
			var _this = _super.call(this, 'packet', id) || this;
			_this.setAttribute('version', '1.2');
			_this.setAttribute('origin', origin);
			return _this;
		}

		PacketElement.prototype.element = function (element) {
			this.addElement('elements', element);
			return this;
		};
		PacketElement.prototype.reference = function (element) {
			this.addElement('references', element);
			return this;
		};
		return PacketElement;
	}(ProtocolElement));
	exports.PacketElement = PacketElement;
	var ErrorElement = (function (_super) {
		__extends(ErrorElement, _super);

		function ErrorElement(code, message) {
			var _this = _super.call(this, 'error') || this;
			_this.setAttribute('code', code);
			_this.setAttribute('message', message);
			return _this;
		}

		ErrorElement.prototype.setException = function (exception) {
			this.setAttribute('exception', exception);
			return this;
		};
		return ErrorElement;
	}(ProtocolElement));
	exports.ErrorElement = ErrorElement;
	var RequestElement = (function (_super) {
		__extends(RequestElement, _super);

		function RequestElement(request) {
			var _this = _super.call(this, 'request') || this;
			_this.setAttribute('request', request);
			return _this;
		}

		return RequestElement;
	}(ProtocolElement));
	exports.RequestElement = RequestElement;
	var MessageElement = (function (_super) {
		__extends(MessageElement, _super);

		function MessageElement(request) {
			var _this = _super.call(this, 'message') || this;
			_this.setAttribute('request', request);
			return _this;
		}

		return MessageElement;
	}(ProtocolElement));
	exports.MessageElement = MessageElement;
	var ResponseElement = (function (_super) {
		__extends(ResponseElement, _super);

		function ResponseElement() {
			return _super.call(this, 'response') || this;
		}

		return ResponseElement;
	}(ProtocolElement));
	exports.ResponseElement = ResponseElement;
	var ElementQueue = (function (_super) {
		__extends(ElementQueue, _super);

		function ElementQueue() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		ElementQueue.prototype.queue = function (element) {
			this.add(element);
			return this;
		};
		ElementQueue.prototype.createPacket = function () {
			var packet = new PacketElement('client').addElementCollection('elements', this);
			this.clear();
			return packet;
		};
		return ElementQueue;
	}(collection_1.Collection));
	exports.ElementQueue = ElementQueue;
});
define("edde/event", ["require", "exports", "edde/element", "edde/e3"], function (require, exports, element_2, e3_7) {
	"use strict";
	exports.__esModule = true;
	var EventBus = (function () {
		function EventBus() {
			this.listenerList = e3_7.e3.hashMapCollection();
		}

		EventBus.prototype.listen = function (event, handler, weight, context, scope, cancelable) {
			if (weight === void 0) {
				weight = 100;
			}
			event = event || '::proxy';
			this.listenerList.add(event, {
				'event': event,
				'handler': handler,
				'weight': weight,
				'context': context || null,
				'scope': scope || null,
				'cancelable': cancelable || true
			});
			this.listenerList.sort(event, function (alpha, beta) {
				return beta.weight - alpha.weight;
			});
			return this;
		};
		EventBus.prototype.register = function (instance, scope) {
			var _this = this;
			e3_7.e3.$$(instance, function (name, value) {
				return name.indexOf('::ListenerList/', 0) !== -1 ? e3_7.e3.$(value, function (listener) {
					return _this.listen(listener.event, (listener.context || instance)[listener.handler], listener.weight, listener.context || instance, listener.scope || scope, listener.cancelable);
				}) : null;
			});
			return this;
		};
		EventBus.prototype.remove = function (scope) {
			scope ? this.listenerList.removeBy(function (listener) {
				return listener.scope !== scope;
			}) : this.listenerList.clear();
			return this;
		};
		EventBus.prototype.event = function (event) {
			this.listenerList.each(event.getAttribute('event') || '', function (listener) {
				return listener.cancelable && event.getMeta('cancel', false) === true ? null : listener.handler.call(listener.context || listener.handler, event);
			});
			this.listenerList.each('::proxy', function (listener) {
				return listener.cancelable && event.getMeta('cancel', false) === true ? null : listener.handler.call(listener.context || listener.handler, event);
			});
			return this;
		};
		EventBus.prototype.emit = function (event, data) {
			if (data === void 0) {
				data = {};
			}
			var element = new element_2.EventElement(event).data(data);
			this.event(element);
			return element;
		};
		return EventBus;
	}());
	exports.EventBus = EventBus;
});
define("edde/ajax", ["require", "exports", "edde/promise"], function (require, exports, promise_1) {
	"use strict";
	exports.__esModule = true;
	var AjaxHandler = (function (_super) {
		__extends(AjaxHandler, _super);

		function AjaxHandler() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		AjaxHandler.prototype.serverFail = function (callback) {
			this.register('server-fail', callback);
			return this;
		};
		AjaxHandler.prototype.onServerFail = function (xmlHttpRequest) {
			return this.execute('server-fail', xmlHttpRequest);
		};
		AjaxHandler.prototype.clientFail = function (callback) {
			this.register('client-fail', callback);
			return this;
		};
		AjaxHandler.prototype.onClientFail = function (xmlHttpRequest) {
			return this.execute('client-fail', xmlHttpRequest);
		};
		AjaxHandler.prototype.timeout = function (callback) {
			this.register('timeout', callback);
			return this;
		};
		AjaxHandler.prototype.onTimeout = function (xmlHttpRequest) {
			return this.execute('timeout', xmlHttpRequest);
		};
		AjaxHandler.prototype.error = function (callback) {
			this.register('error', callback);
			return this;
		};
		AjaxHandler.prototype.onError = function (xmlHttpRequest) {
			return this.execute('error', xmlHttpRequest);
		};
		return AjaxHandler;
	}(promise_1.AbstractPromise));
	exports.AjaxHandler = AjaxHandler;
	var Ajax = (function () {
		function Ajax(url) {
			this.timeout = 10000;
			this.url = url;
			this.method = 'post';
			this.accept = 'application/json';
			this.async = true;
			this.ajaxHandler = new AjaxHandler();
		}

		Ajax.prototype.setMethod = function (method) {
			this.method = method;
			return this;
		};
		Ajax.prototype.setAccept = function (accept) {
			this.accept = accept;
			return this;
		};
		Ajax.prototype.setAsync = function (async) {
			this.async = async;
			return this;
		};
		Ajax.prototype.setTimeout = function (timeout) {
			this.timeout = timeout;
			return this;
		};
		Ajax.prototype.getAjaxHandler = function () {
			return this.ajaxHandler;
		};
		Ajax.prototype.execute = function (content) {
			var _this = this;
			var xmlHttpRequest = new XMLHttpRequest();
			try {
				var timeoutId_1 = null;
				xmlHttpRequest.onreadystatechange = function () {
					switch (xmlHttpRequest.readyState) {
						case 0:
							break;
						case 1:
							content ? xmlHttpRequest.setRequestHeader('Content-Type', content.getMime()) : null;
							xmlHttpRequest.setRequestHeader('Accept', _this.accept);
							timeoutId_1 = setTimeout(function () {
								xmlHttpRequest.abort();
								_this.ajaxHandler.onTimeout(xmlHttpRequest);
								_this.ajaxHandler.onFail(xmlHttpRequest);
								_this.ajaxHandler.onAlways(xmlHttpRequest);
							}, _this.timeout);
							break;
						case 2:
							break;
						case 3:
							clearTimeout(timeoutId_1);
							timeoutId_1 = null;
							break;
						case 4:
							try {
								if (xmlHttpRequest.status >= 200 && xmlHttpRequest.status <= 299) {
									_this.ajaxHandler.onSuccess(xmlHttpRequest);
								}
								else if (xmlHttpRequest.status >= 400 && xmlHttpRequest.status <= 499) {
									_this.ajaxHandler.onClientFail(xmlHttpRequest);
									_this.ajaxHandler.onFail(xmlHttpRequest);
								}
								else if (xmlHttpRequest.status >= 500 && xmlHttpRequest.status <= 599) {
									_this.ajaxHandler.onServerFail(xmlHttpRequest);
									_this.ajaxHandler.onFail(xmlHttpRequest);
								}
							}
							catch (e) {
								_this.ajaxHandler.onError(xmlHttpRequest);
								_this.ajaxHandler.onFail(xmlHttpRequest);
							}
							_this.ajaxHandler.onAlways(xmlHttpRequest);
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
		};
		return Ajax;
	}());
	exports.Ajax = Ajax;
});
define("edde/job", ["require", "exports", "edde/e3"], function (require, exports, e3_8) {
	"use strict";
	exports.__esModule = true;
	var JobManager = (function () {
		function JobManager() {
			this.jobQueue = e3_8.e3.collection();
		}

		JobManager.prototype.queue = function (element) {
			this.jobQueue.add(element);
			return this;
		};
		JobManager.prototype.execute = function () {
			var _this = this;
			if (this.jobQueue.isEmpty()) {
				return this;
			}
			if (this.queueId) {
				setTimeout(function () {
					return _this.execute();
				}, 300);
				return this;
			}
			var jobQueue = e3_8.e3.collection().copy(this.jobQueue);
			this.jobQueue.clear();
			this.queueId = setTimeout(function () {
				jobQueue.each(function (element) {
					return e3_8.e3.execute(element);
				});
				jobQueue.clear();
				_this.queueId = null;
				_this.execute();
			}, 0);
			return this;
		};
		return JobManager;
	}());
	exports.JobManager = JobManager;
});
define("edde/decorator", ["require", "exports"], function (require, exports) {
	"use strict";
	exports.__esModule = true;
	var Listen = (function () {
		function Listen() {
		}

		Listen.To = function (event, weight, cancelable) {
			if (weight === void 0) {
				weight = 100;
			}
			if (cancelable === void 0) {
				cancelable = true;
			}
			return function (target, property) {
				var name = '::ListenerList/' + event + '::' + property;
				(target[name] = target[name] || []).push({
					'event': event,
					'handler': property,
					'weight': weight,
					'context': null,
					'scope': null,
					'cancelable': cancelable
				});
			};
		};
		Listen.ToNative = function (event) {
			return function (target, property) {
				var name = '::NativeListenerList/' + event + '::' + property;
				(target[name] = target[name] || []).push({
					'event': event,
					'handler': property
				});
			};
		};
		return Listen;
	}());
	exports.Listen = Listen;
});
define("edde/control", ["require", "exports", "edde/e3", "edde/decorator"], function (require, exports, e3_9, decorator_1) {
	"use strict";
	exports.__esModule = true;
	var AbstractControl = (function () {
		function AbstractControl(name) {
			this.rendered = false;
			this.controlList = e3_9.e3.collection();
			this.name = name;
			this.element = null;
		}

		AbstractControl.prototype.use = function (control) {
			this.controlList.add(control);
			return control.create();
		};
		AbstractControl.prototype.attachTo = function (root) {
			root.attach(this.render());
			return this;
		};
		AbstractControl.prototype.render = function () {
			var _this = this;
			if (this.element && this.rendered) {
				return this.element;
			}
			var element = this.create();
			this.rendered = true;
			var dom = (this.element = element).getElement();
			e3_9.e3.$$(this, function (name, value) {
				return name.indexOf('::NativeListenerList/', 0) !== -1 ? e3_9.e3.$(value, function (listener) {
					return dom.addEventListener(listener.event, function (event) {
						return _this[listener.handler].call(_this, event);
					}, false);
				}) : null;
			});
			if (this.isListening()) {
				e3_9.e3.listener(this);
			}
			this.controlList.each(function (control) {
				return control.render();
			});
			return element;
		};
		AbstractControl.prototype.create = function () {
			return this.element ? this.element : this.element = this.build();
		};
		AbstractControl.prototype.getElement = function () {
			return this.element;
		};
		AbstractControl.prototype.update = function (element) {
			return this;
		};
		AbstractControl.prototype.isListening = function () {
			return true;
		};
		AbstractControl.prototype.show = function () {
			this.element.removeClass('is-hidden');
			return this;
		};
		AbstractControl.prototype.hide = function () {
			this.element.addClass('is-hidden');
			return this;
		};
		return AbstractControl;
	}());
	exports.AbstractControl = AbstractControl;
	var ControlFactory = (function () {
		function ControlFactory() {
		}

		ControlFactory.prototype.eventControlCreate = function (element) {
			element.setMeta('instance', e3_9.e3.create(element.getMeta('control')).attachTo(element.getMeta('root')));
		};
		__decorate([
			decorator_1.Listen.To('control/create', 0)
		], ControlFactory.prototype, "eventControlCreate");
		return ControlFactory;
	}());
	exports.ControlFactory = ControlFactory;
	var ViewManager = (function () {
		function ViewManager() {
			this.registerList = e3_9.e3.hashMap();
			this.viewList = e3_9.e3.hashMap();
		}

		ViewManager.prototype.eventViewRegister = function (element) {
			this.registerList.set(element.getMeta('view'), element);
		};
		ViewManager.prototype.eventViewChange = function (element) {
			var view = element.getMeta('view');
			if (this.viewList.has(view) === false) {
				this.viewList.set(view, e3_9.e3.emit('control/create', this.registerList.get(view).getMetaList().toObject()).getMeta('instance'));
			}
			if (this.current) {
				this.current.hide();
			}
			this.current = this.viewList.get(view);
			this.current.show();
		};
		__decorate([
			decorator_1.Listen.To('view/register', 0)
		], ViewManager.prototype, "eventViewRegister");
		__decorate([
			decorator_1.Listen.To('view/change', 0)
		], ViewManager.prototype, "eventViewChange");
		return ViewManager;
	}());
	exports.ViewManager = ViewManager;
});
define("edde/e3", ["require", "exports", "edde/collection", "edde/dom", "edde/event", "edde/element", "edde/node", "edde/protocol", "edde/ajax", "edde/job", "edde/converter", "edde/control"], function (require, exports, collection_2, dom_1, event_1, element_3, node_2, protocol_1, ajax_1, job_1, converter_3, control_1) {
	"use strict";
	exports.__esModule = true;
	var e3 = (function () {
		function e3() {
		}

		e3.prototype.version = function () {
			return '4.0.0.0';
		};
		e3.EventBus = function () {
			return this.eventBus ? this.eventBus : this.eventBus = new event_1.EventBus();
		};
		e3.ProtocolService = function () {
			return this.protocolService ? this.protocolService : this.protocolService = new protocol_1.ProtocolService(this.EventBus());
		};
		e3.RequestService = function (url) {
			return this.requestService ? this.requestService : this.requestService = new protocol_1.RequestService(url || this.el(document.body).data('protocol'));
		};
		e3.ElementQueue = function () {
			return this.elementQueue ? this.elementQueue : this.elementQueue = new element_3.ElementQueue();
		};
		e3.JobManager = function () {
			return this.jobManager ? this.jobManager : this.jobManager = new job_1.JobManager();
		};
		e3.ConverterManager = function () {
			if (this.converterManager) {
				return this.converterManager;
			}
			this.converterManager = new converter_3.ConverterManager();
			this.converterManager.registerConverter(new converter_3.JsonConverter());
			this.converterManager.registerConverter(new node_2.NodeConverter());
			this.converterManager.registerConverter(new protocol_1.ElementConverter());
			return this.converterManager;
		};
		e3.ControlFactory = function () {
			return this.controlFactory ? this.controlFactory : this.controlFactory = e3.listener(new control_1.ControlFactory());
		};
		e3.ViewManager = function () {
			return this.viewManager ? this.viewManager : this.viewManager = e3.listener(new control_1.ViewManager());
		};
		e3.Event = function (event, data) {
			if (data === void 0) {
				data = {};
			}
			return new element_3.EventElement(event).data(data);
		};
		e3.Message = function (message, data) {
			if (data === void 0) {
				data = {};
			}
			return new element_3.MessageElement(message).data(data);
		};
		e3.Element = function (type) {
			return new element_3.ProtocolElement(type);
		};
		e3.Node = function (name, value) {
			if (name === void 0) {
				name = null;
			}
			if (value === void 0) {
				value = null;
			}
			return new node_2.Node(name, value);
		};
		e3.collection = function (collection) {
			if (collection === void 0) {
				collection = [];
			}
			return new collection_2.Collection(collection);
		};
		e3.$ = function (collection, callback) {
			return new collection_2.Collection(collection).each(callback);
		};
		e3.collectionEach = function (collection, callback) {
			return this.$(collection, callback);
		};
		e3.hashMap = function (hashMap) {
			if (hashMap === void 0) {
				hashMap = {};
			}
			return new collection_2.HashMap(hashMap);
		};
		e3.$$ = function (hashMap, callback) {
			return this.hashMap(hashMap).each(callback);
		};
		e3.hashMapEach = function (hashMap, callback) {
			return this.$$(hashMap, callback);
		};
		e3.hashMapCollection = function () {
			return new collection_2.HashMapCollection();
		};
		e3.El = function (name, classList, factoryDocument) {
			if (classList === void 0) {
				classList = [];
			}
			return new dom_1.HtmlElement((factoryDocument || document).createElement(name)).addClassList(classList);
		};
		e3.Text = function (text, factoryDocument) {
			return (factoryDocument || document).createTextNode(text);
		};
		e3.el = function (element) {
			return new dom_1.HtmlElement(element);
		};
		e3.html = function (html) {
			var node = document.createElement('div');
			node.innerHTML = html.trim();
			return e3.el(node.firstChild);
		};
		e3.Selector = function (selector) {
			if (selector.match(/^\.[a-zA-Z0-9_-]+$/)) {
				return new dom_1.SimpleClassSelector(selector.substr(1));
			}
			else if (selector.match(/^#[a-zA-Z0-9_-]+$/)) {
				return new dom_1.SimpleIdSelector(selector.substr(1));
			}
			return new dom_1.SimpleSelector(selector);
		};
		e3.reflow = function (callback) {
			try {
				var documentElement = document.documentElement;
				var parentNode = documentElement.parentNode;
				var nextSibling = documentElement.nextSibling;
				var result = undefined;
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
		};
		e3.selector = function (selector, root) {
			return new dom_1.HtmlElementCollection(root || document.body, selector);
		};
		e3.listen = function (event, handler, weight, context, scope) {
			if (weight === void 0) {
				weight = 100;
			}
			return this.EventBus().listen(event, handler, weight, context, scope);
		};
		e3.listener = function (instance, scope) {
			this.EventBus().register(instance, scope);
			return instance;
		};
		e3.unlisten = function (scope) {
			return this.EventBus().remove(scope);
		};
		e3.event = function (event) {
			return this.EventBus().event(event);
		};
		e3.emit = function (event, data) {
			if (data === void 0) {
				data = {};
			}
			return this.EventBus().emit(event, data);
		};
		e3.request = function (element, callback) {
			return this.RequestService().request(element, callback);
		};
		e3.execute = function (element) {
			return e3.ProtocolService().execute(element);
		};
		e3.job = function (element) {
			return element ? this.JobManager().queue(element) : this.JobManager().execute();
		};
		e3.create = function (create, parameterList, singleton) {
			if (parameterList === void 0) {
				parameterList = [];
			}
			if (singleton === void 0) {
				singleton = false;
			}
			if (singleton === true && this.classList.has(create)) {
				return this.classList.get(create);
			}
			try {
				var module_1 = create.split(':');
				var constructor = require(module_1[0])[module_1[1]];
				var instance = parameterList.length > 0 ? (function (callback, parameterList) {
					var constructor = callback;
					var Constructor = (function () {
						function Constructor() {
							constructor.apply(this, parameterList);
						}

						return Constructor;
					}());
					Constructor.prototype = constructor.prototype;
					return new Constructor;
				})(constructor, parameterList) : new constructor;
				singleton ? this.classList.set(create, instance) : null;
				return instance;
			}
			catch (e) {
				throw new Error("Cannot create [" + create + "]:\n" + e);
			}
		};
		e3.queue = function (element) {
			return this.ElementQueue().queue(element);
		};
		e3.toNode = function (object, node, factory) {
			var _this = this;
			var callback = factory || (function (name) {
				return new node_2.Node(name ? name : null);
			});
			var root = node || callback();
			e3.$$(object, function (name, value) {
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
					root.addNode(_this.toNode(value, callback(name), factory));
				}
				else if (e3.isArray(value)) {
					e3.$(value, function (value2) {
						return root.addNode(_this.toNode(value2, callback(name), factory));
					});
				}
				else {
					root.setAttribute(name, value);
				}
			});
			if (root.getName() === null && root.getNodeCount() === 1) {
				return root.getNodeList().first().detach();
			}
			return root;
		};
		e3.fromNode = function (root) {
			var _this = this;
			var attributeList = root.getAttributeList();
			var metaList = root.getMetaList();
			var value = root.getValue();
			var object = {};
			if (value) {
				object['::value'] = value;
			}
			if (attributeList.isEmpty() === false) {
				object = e3.extend(object, attributeList.toObject());
			}
			if (metaList.isEmpty() === false) {
				object['::meta'] = metaList.toObject();
			}
			var nodeList = e3.hashMapCollection();
			root.each(function (node) {
				return nodeList.add(node.getName() || '<node>', _this.fromNode(node));
			});
			nodeList.eachCollection(function (name, collection) {
				return object[name] = collection.getCount() === 1 ? collection.first() : collection.toArray();
			});
			if (root.isRoot()) {
				var rootObject = {};
				rootObject[root.getName() || '<root>'] = object;
				return rootObject;
			}
			return object;
		};
		e3.convert = function (content, mime, targetList) {
			return this.ConverterManager().convert(content, mime, targetList).convert();
		};
		e3.toJsonNode = function (root) {
			return JSON.stringify(this.fromNode(root));
		};
		e3.nodeFromJson = function (json, factory) {
			return this.toNode(JSON.parse(json || '{}'), null, factory);
		};
		e3.elementFromJson = function (json) {
			return this.nodeFromJson(json, function (name) {
				return new element_3.ProtocolElement(name);
			});
		};
		e3.elementFromObject = function (object) {
			return this.toNode(object, null, function (name) {
				return new element_3.ProtocolElement(name);
			});
		};
		e3.tree = function (root, callback) {
			var _this = this;
			root.each(function (node) {
				return _this.tree(node, callback);
			});
			return callback(root);
		};
		e3.ajax = function (url) {
			return new ajax_1.Ajax(url);
		};
		e3.packet = function (selector, root) {
			var _this = this;
			this.el(root || document.body).collection(selector || '.packet').each(function (node) {
				return _this.job(_this.elementFromJson(node.getInnerHtml()));
			});
			this.job();
		};
		e3.heartbeat = function (interval) {
			var _this = this;
			if (interval === void 0) {
				interval = 3000;
			}
			if (this.heartbeatId) {
				return this;
			}
			var heartbeat = function () {
				return _this.request(e3.Event('protocol/heartbeat')).always(function () {
					return _this.heartbeatId = setTimeout(heartbeat, interval);
				});
			};
			this.heartbeatId = setTimeout(heartbeat, interval);
			this.listen('heartbeat/stop', function () {
				return clearTimeout(_this.heartbeatId);
			}, 0);
		};
		e3.extend = function () {
			var objectList = [];
			for (var _i = 0; _i < arguments.length; _i++) {
				objectList[_i] = arguments[_i];
			}
			var hasOwnProperty = Object.prototype.hasOwnProperty;
			objectList[0] = objectList[0] || {};
			for (var _a = 0, objectList_1 = objectList; _a < objectList_1.length; _a++) {
				var object = objectList_1[_a];
				if (object) {
					for (var key in object) {
						if (hasOwnProperty.call(object, key)) {
							objectList[0][key] = object[key];
						}
					}
				}
			}
			return objectList[0];
		};
		e3.formEncode = function (object, prefix) {
			var _this = this;
			var list = [];
			var encode = function (key, value, prefix) {
				var name = prefix ? prefix + '[' + key + ']' : key;
				list[list.length] = _this.isScalar(value) === false ? _this.formEncode(value, name) : (encodeURIComponent(name) + '=' + encodeURIComponent(value == null ? '' : value));
			};
			this.isArray(object) ? this.$(object, function (value, index) {
				return encode(String(index), value, prefix);
			}) : this.$$(object, function (key, value) {
				return encode(key, value, prefix);
			});
			return list.join('&').replace(/%20/g, '+');
		};
		e3.getInstanceName = function (instance) {
			return (instance.constructor.name ? instance.constructor.name : ("" + instance.constructor).split("function ")[1].split("(")[0]);
		};
		e3.isScalar = function (value) {
			switch (typeof value) {
				case 'string':
				case 'number':
				case 'boolean':
					return true;
			}
			return false;
		};
		e3.isArray = function (value) {
			if (this.isScalar(value)) {
				return false;
			}
			return value && value.length !== undefined && this.getInstanceName(value) === 'Array';
		};
		e3.isObject = function (value) {
			if (this.isScalar(value)) {
				return false;
			}
			return value && typeof value === 'object' && this.isArray(value) === false;
		};
		e3.isIterable = function (value) {
			if (this.isScalar(value)) {
				return false;
			}
			return value && value.hasOwnProperty('length') && value.hasOwnProperty(0) && value.hasOwnProperty(value.length - 1);
		};
		e3.guid = function (glue, a, b) {
			if (glue === void 0) {
				glue = '-';
			}
			for (b = a = ''; a++ < 36; b += a * 51 & 52 ? (a ^ 15 ? 8 ^ Math.random() * (a ^ 20 ? 16 : 4) : 4).toString(16) : glue)
				;
			return b;
		};
		e3.classList = e3.hashMap();
		return e3;
	}());
	exports.e3 = e3;
});
define("app/app", ["require", "exports", "edde/e3"], function (require, exports, e3_10) {
	"use strict";
	exports.__esModule = true;
	e3_10.e3.RequestService();
	e3_10.e3.ProtocolService();
	e3_10.e3.ControlFactory();
	e3_10.e3.ViewManager();
	var body = e3_10.e3.el(document.body);
	e3_10.e3.hashMap({
		'index-view': 'app/index/IndexView:IndexView',
		'register-view': 'app/login/RegisterView:RegisterView',
		'login-view': 'app/login/LoginView:LoginView'
	}).each(function (view, control) {
		return e3_10.e3.emit('view/register', {
			'view': view,
			'control': control,
			'root': body
		});
	});
	e3_10.e3.emit('view/change', {
		'view': 'index-view'
	});
});
define("app/index/RegisterButton", ["require", "exports", "edde/control", "edde/e3", "edde/decorator"], function (require, exports, control_2, e3_11, decorator_2) {
	"use strict";
	exports.__esModule = true;
	var RegisterButton = (function (_super) {
		__extends(RegisterButton, _super);

		function RegisterButton() {
			return _super.call(this, 'register-button') || this;
		}

		RegisterButton.prototype.build = function () {
			return e3_11.e3.html("\n\t\t\t<p class=\"control\">\n\t\t\t\t<span class=\"button\">\n\t\t\t\t\t<span class=\"icon\"><i class=\"fa fa-user-circle\"></i></span>\n\t\t\t\t\t<span>Register</span>\n\t\t\t\t</span>\n\t\t\t</p>\n\t\t");
		};
		RegisterButton.prototype.onClick = function () {
			e3_11.e3.emit('view/change', {
				'view': 'register-view'
			});
		};
		__decorate([
			decorator_2.Listen.ToNative('click')
		], RegisterButton.prototype, "onClick");
		return RegisterButton;
	}(control_2.AbstractControl));
	exports.RegisterButton = RegisterButton;
});
define("app/index/LoginButton", ["require", "exports", "edde/control", "edde/e3", "edde/decorator"], function (require, exports, control_3, e3_12, decorator_3) {
	"use strict";
	exports.__esModule = true;
	var LoginButton = (function (_super) {
		__extends(LoginButton, _super);

		function LoginButton() {
			return _super.call(this, 'login-button') || this;
		}

		LoginButton.prototype.build = function () {
			return e3_12.e3.html("\n\t\t\t<p class=\"control\">\n\t\t\t\t<span class=\"button is-primary\">\n\t\t\t\t\t<span class=\"icon\"><i class=\"fa fa-lock\"></i></span>\n\t\t\t\t\t<span>Login</span>\n\t\t\t\t</span>\n\t\t\t</p>\n\t\t");
		};
		LoginButton.prototype.onClick = function () {
			e3_12.e3.emit('view/change', {
				'view': 'login-view'
			});
		};
		__decorate([
			decorator_3.Listen.ToNative('click')
		], LoginButton.prototype, "onClick");
		return LoginButton;
	}(control_3.AbstractControl));
	exports.LoginButton = LoginButton;
});
define("app/index/MainBarControl", ["require", "exports", "edde/control", "edde/e3", "app/index/RegisterButton", "app/index/LoginButton"], function (require, exports, control_4, e3_13, RegisterButton_1, LoginButton_1) {
	"use strict";
	exports.__esModule = true;
	var MainBarControl = (function (_super) {
		__extends(MainBarControl, _super);

		function MainBarControl() {
			return _super.call(this, 'main-bar-control') || this;
		}

		MainBarControl.prototype.build = function () {
			return e3_13.e3.html('<nav class="navbar is-white"></nav>').attach(e3_13.e3.html('<div class="container"></div>').attachList([
				e3_13.e3.html("\n\t\t\t\t\t<div class=\"navbar-brand\">\n\t\t\t\t\t\t<a class=\"navbar-item\" href=\"/\">\n\t\t\t\t\t\t\t<div class=\"field is-grouped\">\n\t\t\t\t\t\t\t\t<p class=\"control\">\n\t\t\t\t\t\t\t\t\t<img src=\"/img/logo.png\"/>\n\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t<p class=\"control\">\n\t\t\t\t\t\t\t\t\t<span>Edde Framework</span>\n\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</a>\n\t\t\t\t\t</div>\n\t\t\t\t"),
				e3_13.e3.html('<div class="navbar-menu"></div>').attach(e3_13.e3.html('<div class="navbar-end"></div>').attach(e3_13.e3.html('<span class="navbar-item">').attach(e3_13.e3.html('<div class="field is-grouped"></div>').attachList([
					this.use(new RegisterButton_1.RegisterButton()),
					this.use(new LoginButton_1.LoginButton()),
				]))))
			]));
		};
		return MainBarControl;
	}(control_4.AbstractControl));
	exports.MainBarControl = MainBarControl;
});
define("app/index/IndexView", ["require", "exports", "edde/control", "edde/e3", "app/index/MainBarControl"], function (require, exports, control_5, e3_14, MainBarControl_1) {
	"use strict";
	exports.__esModule = true;
	var IndexView = (function (_super) {
		__extends(IndexView, _super);

		function IndexView() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		IndexView.prototype.build = function () {
			return e3_14.e3.html('<div class="view is-hidden"></div>').attachList([
				this.use(new MainBarControl_1.MainBarControl()),
				e3_14.e3.html("\n\t\t\t\t<section class=\"hero is-small is-bold is-info\">\n\t\t\t\t\t<div class=\"hero-body\">\n\t\t\t\t\t\t<div class=\"container\">\n\t\t\t\t\t\t\t<div class=\"columns is-vcentered\">\n\t\t\t\t\t\t\t\t<div class=\"column\">\n\t\t\t\t\t\t\t\t\t<p class=\"title\">Welcome to Edde Framework</p>\n\t\t\t\t\t\t\t\t\t<p class=\"subtitle\">...epic, fast and modern Framework</p>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</section>\n\t\t\t")
			]);
		};
		return IndexView;
	}(control_5.AbstractControl));
	exports.IndexView = IndexView;
});
define("edde/client", ["require", "exports", "edde/e3", "edde/decorator"], function (require, exports, e3_15, decorator_4) {
	"use strict";
	exports.__esModule = true;
	var AbstractClientClass = (function () {
		function AbstractClientClass() {
		}

		AbstractClientClass.prototype.attach = function (htmlElement) {
			var _this = this;
			var dom = (this.element = htmlElement).getElement();
			e3_15.e3.$$(this, function (name, value) {
				return name.indexOf('::NativeListenerList/', 0) !== -1 ? e3_15.e3.$(value, function (listener) {
					return dom.addEventListener(listener.event, function (event) {
						return _this[listener.handler].call(_this, event);
					}, false);
				}) : null;
			});
			return this.element;
		};
		AbstractClientClass.prototype.attachHtml = function (html) {
			return this.attach(e3_15.e3.html(html));
		};
		AbstractClientClass.prototype.getElement = function () {
			return this.element;
		};
		return AbstractClientClass;
	}());
	exports.AbstractClientClass = AbstractClientClass;
	var AbstractButton = (function (_super) {
		__extends(AbstractButton, _super);

		function AbstractButton() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		AbstractButton.prototype.onClick = function (event) {
		};
		__decorate([
			decorator_4.Listen.ToNative('click')
		], AbstractButton.prototype, "onClick");
		return AbstractButton;
	}(AbstractClientClass));
	exports.AbstractButton = AbstractButton;
});
define("app/login/EmailInput", ["require", "exports", "edde/control", "edde/e3", "edde/decorator"], function (require, exports, control_6, e3_16, decorator_5) {
	"use strict";
	exports.__esModule = true;
	var EmailInput = (function (_super) {
		__extends(EmailInput, _super);

		function EmailInput() {
			var _this = _super.call(this, 'email-input') || this;
			_this.hint = '';
			return _this;
		}

		EmailInput.prototype.setHint = function (hint) {
			this.hint = hint;
			return this;
		};
		EmailInput.prototype.build = function () {
			var html = [
				this.input = e3_16.e3.html('<input class="input" type="email" placeholder="Email">'),
				e3_16.e3.html("\n\t\t\t\t<span class=\"icon is-small is-left\">\n\t\t\t\t\t<i class=\"fa fa-envelope\"></i>\n\t\t\t\t</span>\n\t\t\t"),
				this.icon = e3_16.e3.html('<span class="icon is-small is-right is-hidden"></span>').attach(this.iconElement = e3_16.e3.html('<i class="fa"></i>')),
			];
			html.push(this.help = e3_16.e3.html("<p class=\"help\">" + this.hint + "</p>"));
			return e3_16.e3.html('<p class="control has-icons-left has-icons-right"></p>').attachList(html);
		};
		EmailInput.prototype.onKeypress = function () {
			var isValid = this.isValid();
			this.input.toggleClass('is-primary', isValid);
			this.input.toggleClass('is-danger', isValid === false);
			this.icon.removeClass('is-hidden');
			this.iconElement.toggleClass('fa-check', isValid);
			this.iconElement.toggleClass('fa-warning', isValid === false);
			this.help.toggleClass('is-danger', isValid === false);
			this.help.text((isValid === false ? 'email looks like it is not an email, oops!' : this.hint));
			e3_16.e3.emit('email-input/is-valid', {'valid': isValid});
		};
		EmailInput.prototype.isValid = function () {
			return /^[a-z0-9_.-]+@[a-z0-9_.-]+\.[a-z0-9]{1,6}$/i.test(this.input.getElement().value);
		};
		__decorate([
			decorator_5.Listen.ToNative('keyup')
		], EmailInput.prototype, "onKeypress");
		return EmailInput;
	}(control_6.AbstractControl));
	exports.EmailInput = EmailInput;
});
define("app/login/RegisterCancelButton", ["require", "exports", "edde/control", "edde/e3", "edde/decorator"], function (require, exports, control_7, e3_17, decorator_6) {
	"use strict";
	exports.__esModule = true;
	var RegisterCancelButton = (function (_super) {
		__extends(RegisterCancelButton, _super);

		function RegisterCancelButton() {
			return _super.call(this, 'register-cancel') || this;
		}

		RegisterCancelButton.prototype.build = function () {
			return e3_17.e3.html("\n\t\t\t<div class=\"control\">\n\t\t\t\t<button class=\"button is-text\">Cancel</button>\n\t\t\t</div>\n\t\t");
		};
		RegisterCancelButton.prototype.onClick = function () {
			alert('Do the registration!');
		};
		__decorate([
			decorator_6.Listen.ToNative('click')
		], RegisterCancelButton.prototype, "onClick");
		return RegisterCancelButton;
	}(control_7.AbstractControl));
	exports.RegisterCancelButton = RegisterCancelButton;
});
define("app/login/RegisterButton", ["require", "exports", "edde/control", "edde/e3", "edde/decorator"], function (require, exports, control_8, e3_18, decorator_7) {
	"use strict";
	exports.__esModule = true;
	var RegisterButton = (function (_super) {
		__extends(RegisterButton, _super);

		function RegisterButton() {
			return _super.call(this, 'register-button') || this;
		}

		RegisterButton.prototype.build = function () {
			return e3_18.e3.html('<div class="control">').attach(this.button = e3_18.e3.html('<button class="button is-primary" disabled>Register</button>'));
		};
		RegisterButton.prototype.onClick = function () {
			alert('Do the registration!');
		};
		RegisterButton.prototype.eventEmailInputIsValid = function (element) {
			this.button.attr('disabled', 'disabled');
			if (element.getMeta('valid', false)) {
				this.button.removeAttribute('disabled');
			}
		};
		__decorate([
			decorator_7.Listen.ToNative('click')
		], RegisterButton.prototype, "onClick");
		__decorate([
			decorator_7.Listen.To('email-input/is-valid')
		], RegisterButton.prototype, "eventEmailInputIsValid");
		return RegisterButton;
	}(control_8.AbstractControl));
	exports.RegisterButton = RegisterButton;
});
define("app/login/PasswordInput", ["require", "exports", "edde/control", "edde/e3"], function (require, exports, control_9, e3_19) {
	"use strict";
	exports.__esModule = true;
	var PasswordInput = (function (_super) {
		__extends(PasswordInput, _super);

		function PasswordInput(placeholder) {
			var _this = _super.call(this, 'password-input') || this;
			_this.placeholder = placeholder;
			return _this;
		}

		PasswordInput.prototype.build = function () {
			return e3_19.e3.html('<p class="control has-icons-left has-icons-right"></p>').attachList([
				this.input = e3_19.e3.html("<input class=\"input\" type=\"password\" placeholder=\"" + this.placeholder + "\">"),
				e3_19.e3.html("\n\t\t\t\t<span class=\"icon is-small is-left\">\n\t\t\t\t\t<i class=\"fa fa-lock\"></i>\n\t\t\t\t</span>\n\t\t\t"),
				this.tick = e3_19.e3.html('<span class="icon is-small is-right is-hidden"><i class="fa fa-check"></i></span>'),
			]);
		};
		return PasswordInput;
	}(control_9.AbstractControl));
	exports.PasswordInput = PasswordInput;
});
define("app/login/RegisterView", ["require", "exports", "edde/control", "edde/e3", "app/index/MainBarControl", "app/login/EmailInput", "app/login/RegisterCancelButton", "app/login/RegisterButton", "app/login/PasswordInput"], function (require, exports, control_10, e3_20, MainBarControl_2, EmailInput_1, RegisterCancelButton_1, RegisterButton_2, PasswordInput_1) {
	"use strict";
	exports.__esModule = true;
	var RegisterView = (function (_super) {
		__extends(RegisterView, _super);

		function RegisterView() {
			return _super.call(this, 'register-view') || this;
		}

		RegisterView.prototype.build = function () {
			return e3_20.e3.html('<div class="view is-hidden"></div>').attachList([
				this.use(new MainBarControl_2.MainBarControl()),
				e3_20.e3.html("\n\t\t\t\t<section class=\"hero is-small is-bold is-info\">\n\t\t\t\t\t<div class=\"hero-body\">\n\t\t\t\t\t\t<div class=\"container\">\n\t\t\t\t\t\t\t<div class=\"columns is-vcentered\">\n\t\t\t\t\t\t\t\t<div class=\"column\">\n\t\t\t\t\t\t\t\t\t<p class=\"title\">New User registration</p>\n\t\t\t\t\t\t\t\t\t<p class=\"subtitle\">...welcome to the community!</p>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</section>\n\t\t\t"),
				e3_20.e3.html('<section class="section"></section>').attach(e3_20.e3.html('<div class="columns"></div>').attach(e3_20.e3.html('<div class="column is-offset-4 is-4">').attachList([
					e3_20.e3.html('<div class="field">').attachList([
						this.use(new EmailInput_1.EmailInput().setHint('later you will use this to login')),
					]),
					e3_20.e3.html('<div class="field">').attachList([
						this.use(new PasswordInput_1.PasswordInput('Password')),
						e3_20.e3.html('<p class="help">think about something long and safe</p>'),
					]),
					e3_20.e3.html('<div class="field">').attachList([
						this.use(new PasswordInput_1.PasswordInput('Password once again')),
						e3_20.e3.html('<p class="help">...are you sure about you password :)?</p>'),
					]),
					e3_20.e3.html('<div class="field is-grouped is-grouped-right">').attachList([
						this.use(new RegisterCancelButton_1.RegisterCancelButton()),
						this.use(new RegisterButton_2.RegisterButton()),
					]),
				]))),
			]);
		};
		return RegisterView;
	}(control_10.AbstractControl));
	exports.RegisterView = RegisterView;
});
define("app/login/LoginView", ["require", "exports", "edde/control", "app/index/MainBarControl", "edde/e3"], function (require, exports, control_11, MainBarControl_3, e3_21) {
	"use strict";
	exports.__esModule = true;
	var LoginView = (function (_super) {
		__extends(LoginView, _super);

		function LoginView() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		LoginView.prototype.build = function () {
			return e3_21.e3.html('<div class="view is-hidden"></div>').attachList([
				this.use(new MainBarControl_3.MainBarControl()),
				e3_21.e3.html("\n\t\t\t\t<section class=\"hero is-small is-bold is-info\">\n\t\t\t\t\t<div class=\"hero-body\">\n\t\t\t\t\t\t<div class=\"container\">\n\t\t\t\t\t\t\t<div class=\"columns is-vcentered\">\n\t\t\t\t\t\t\t\t<div class=\"column\">\n\t\t\t\t\t\t\t\t\t<p class=\"title\">Welcome back!</p>\n\t\t\t\t\t\t\t\t\t<p class=\"subtitle\">...please, authenticate!</p>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</section>\n\t\t\t"),
			]);
		};
		return LoginView;
	}(control_11.AbstractControl));
	exports.LoginView = LoginView;
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiLi4vc3JjL2VkZGUvY29sbGVjdGlvbi50cyIsIi4uL3NyYy9lZGRlL2RvbS50cyIsIi4uL3NyYy9lZGRlL2NvbnZlcnRlci50cyIsIi4uL3NyYy9lZGRlL25vZGUudHMiLCIuLi9zcmMvZWRkZS9wcm9taXNlLnRzIiwiLi4vc3JjL2VkZGUvcHJvdG9jb2wudHMiLCIuLi9zcmMvZWRkZS9lbGVtZW50LnRzIiwiLi4vc3JjL2VkZGUvZXZlbnQudHMiLCIuLi9zcmMvZWRkZS9hamF4LnRzIiwiLi4vc3JjL2VkZGUvam9iLnRzIiwiLi4vc3JjL2VkZGUvZGVjb3JhdG9yLnRzIiwiLi4vc3JjL2VkZGUvY29udHJvbC50cyIsIi4uL3NyYy9lZGRlL2UzLnRzIiwiLi4vc3JjL2FwcC9hcHAudHMiLCIuLi9zcmMvYXBwL2luZGV4L1JlZ2lzdGVyQnV0dG9uLnRzIiwiLi4vc3JjL2FwcC9pbmRleC9Mb2dpbkJ1dHRvbi50cyIsIi4uL3NyYy9hcHAvaW5kZXgvTWFpbkJhckNvbnRyb2wudHMiLCIuLi9zcmMvYXBwL2luZGV4L0luZGV4Vmlldy50cyIsIi4uL3NyYy9lZGRlL2NsaWVudC50cyIsIi4uL3NyYy9hcHAvbG9naW4vRW1haWxJbnB1dC50cyIsIi4uL3NyYy9hcHAvbG9naW4vUmVnaXN0ZXJDYW5jZWxCdXR0b24udHMiLCIuLi9zcmMvYXBwL2xvZ2luL1JlZ2lzdGVyQnV0dG9uLnRzIiwiLi4vc3JjL2FwcC9sb2dpbi9QYXNzd29yZElucHV0LnRzIiwiLi4vc3JjL2FwcC9sb2dpbi9SZWdpc3RlclZpZXcudHMiLCIuLi9zcmMvYXBwL2xvZ2luL0xvZ2luVmlldy50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0lBa1FBO1FBR0Msb0JBQW1CLFVBQW9CO1lBQXBCLDJCQUFBLEVBQUEsZUFBb0I7WUFDdEMsSUFBSSxDQUFDLFVBQVUsR0FBRyxVQUFVLENBQUM7UUFDOUIs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
