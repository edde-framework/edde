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
define("edde/protocol", ["require", "exports", "edde/element", "edde/e3", "edde/converter"], function (require, exports, element_1, e3_4, converter_2) {
	"use strict";
	exports.__esModule = true;
	var ProtocolService = (function () {
		function ProtocolService(eventBus) {
			this.handleList = e3_4.e3.hashMap({
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
			var ajax = e3_4.e3.ajax(this.url);
			ajax.setAccept(this.accept)
				.getAjaxHandler()
				.error(function (xmlHttpRequest) {
					return e3_4.e3.emit('request-service/error', {'request': xmlHttpRequest, 'element': element});
				})
				.timeout(function (xmlHttpRequest) {
					return e3_4.e3.emit('request-service/timeout', {'request': xmlHttpRequest, 'element': element});
				})
				.fail(function (xmlHttpRequest) {
					return e3_4.e3.emit('request-service/fail', {'request': xmlHttpRequest, 'element': element});
				})
				.success(function (xmlHttpRequest) {
					var packet = e3_4.e3.elementFromJson(xmlHttpRequest.responseText);
					e3_4.e3.emit('request-service/success', {'request': xmlHttpRequest, 'element': element, 'packet': packet});
					var response;
					callback && (response = packet.getReferenceBy(element.getId())) ? callback(response) : null;
					e3_4.e3.job(packet).execute();
				});
			return ajax.execute(e3_4.e3.convert(e3_4.e3.ElementQueue().createPacket().addElement('elements', element), 'node', [this.target]));
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
					return new converter_2.Content(e3_4.e3.formEncode(e3_4.e3.convert(content, 'node', ['object']).getContent()), 'application/x-www-form-urlencoded');
			}
			throw new Error('Cannot convert [' + mime + '] to [' + target + '] in element converter.');
		};
		return ElementConverter;
	}(converter_2.AbstractConverter));
	exports.ElementConverter = ElementConverter;
});
define("edde/element", ["require", "exports", "edde/node", "edde/collection", "edde/e3"], function (require, exports, node_1, collection_1, e3_5) {
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
			throw "There is an element [" + e3_5.e3.getInstanceName(this) + "] without type! This is quite strange, isn't it?";
		};
		ProtocolElement.prototype.isType = function (type) {
			return this.getType() === type;
		};
		ProtocolElement.prototype.getId = function () {
			var id = this.getAttribute('id', false);
			if (id === false) {
				this.setAttribute('id', id = e3_5.e3.guid());
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
			return node ? node.getNodeList() : e3_5.e3.collection();
		};
		ProtocolElement.prototype.getReferenceBy = function (id) {
			return this.getReferenceList(id).first();
		};
		ProtocolElement.prototype.getReferenceList = function (id) {
			var collection = e3_5.e3.collection();
			if (this.hasReference() && this.getReference() === id) {
				collection.add(this);
			}
			e3_5.e3.tree(this, function (element) {
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
define("edde/event", ["require", "exports", "edde/element", "edde/e3"], function (require, exports, element_2, e3_6) {
	"use strict";
	exports.__esModule = true;
	var EventBus = (function () {
		function EventBus() {
			this.listenerList = e3_6.e3.hashMapCollection();
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
			e3_6.e3.$$(instance, function (name, value) {
				return name.indexOf('::ListenerList/', 0) !== -1 ? e3_6.e3.$(value, function (listener) {
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
			this.event(new element_2.EventElement(event).data(data));
			return this;
		};
		return EventBus;
	}());
	exports.EventBus = EventBus;
});
define("edde/job", ["require", "exports", "edde/e3"], function (require, exports, e3_7) {
	"use strict";
	exports.__esModule = true;
	var JobManager = (function () {
		function JobManager() {
			this.jobQueue = e3_7.e3.collection();
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
			var jobQueue = e3_7.e3.collection().copy(this.jobQueue);
			this.jobQueue.clear();
			this.queueId = setTimeout(function () {
				jobQueue.each(function (element) {
					return e3_7.e3.execute(element);
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
define("edde/e3", ["require", "exports", "edde/collection", "edde/dom", "edde/event", "edde/element", "edde/node", "edde/protocol", "edde/ajax", "edde/job", "edde/converter"], function (require, exports, collection_2, dom_1, event_1, element_3, node_2, protocol_1, ajax_1, job_1, converter_3) {
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
			node.innerHTML = html;
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
define("edde/promise", ["require", "exports", "edde/e3"], function (require, exports, e3_8) {
	"use strict";
	exports.__esModule = true;
	var AbstractPromise = (function () {
		function AbstractPromise() {
			this.promiseList = e3_8.e3.hashMapCollection();
			this.resultList = e3_8.e3.hashMap();
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
define("edde/client", ["require", "exports", "edde/e3", "edde/decorator"], function (require, exports, e3_9, decorator_1) {
	"use strict";
	exports.__esModule = true;
	var AbstractClientClass = (function () {
		function AbstractClientClass() {
		}

		AbstractClientClass.prototype.attach = function (htmlElement) {
			var _this = this;
			var dom = (this.element = htmlElement).getElement();
			e3_9.e3.$$(this, function (name, value) {
				return name.indexOf('::NativeListenerList/', 0) !== -1 ? e3_9.e3.$(value, function (listener) {
					return dom.addEventListener(listener.event, function (event) {
						return _this[listener.handler].call(_this, event);
					}, false);
				}) : null;
			});
			return this.element;
		};
		AbstractClientClass.prototype.attachHtml = function (html) {
			return this.attach(e3_9.e3.html(html));
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
			decorator_1.Listen.ToNative('click')
		], AbstractButton.prototype, "onClick");
		return AbstractButton;
	}(AbstractClientClass));
	exports.AbstractButton = AbstractButton;
});
define("edde/control", ["require", "exports", "edde/e3"], function (require, exports, e3_10) {
	"use strict";
	exports.__esModule = true;
	var AbstractControl = (function () {
		function AbstractControl(name) {
			this.name = name;
			this.element = null;
		}

		AbstractControl.prototype.attach = function (element) {
			var _this = this;
			var dom = (this.element = element).getElement();
			e3_10.e3.$$(this, function (name, value) {
				return name.indexOf('::NativeListenerList/', 0) !== -1 ? e3_10.e3.$(value, function (listener) {
					return dom.addEventListener(listener.event, function (event) {
						return _this[listener.handler].call(_this, event);
					}, false);
				}) : null;
			});
			if (this.isListening()) {
				e3_10.e3.listener(this);
			}
			return this.element;
		};
		AbstractControl.prototype.attachHtml = function (html) {
			return this.attach(e3_10.e3.html(html));
		};
		AbstractControl.prototype.attachTo = function (root) {
			root.attach(this.render());
			return this;
		};
		AbstractControl.prototype.render = function () {
			return this.element ? this.element : this.attach(this.element = this.build());
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
		return AbstractControl;
	}());
	exports.AbstractControl = AbstractControl;
});
define("app/loader/LoaderView", ["require", "exports", "edde/control", "edde/e3"], function (require, exports, control_1, e3_11) {
	"use strict";
	exports.__esModule = true;
	var LoaderView = (function (_super) {
		__extends(LoaderView, _super);

		function LoaderView() {
			return _super.call(this, 'loader-view') || this;
		}

		LoaderView.prototype.build = function () {
			return e3_11.e3.html('<div>loader!</div>');
		};
		return LoaderView;
	}(control_1.AbstractControl));
	exports.LoaderView = LoaderView;
});
define("app/app", ["require", "exports", "edde/e3", "app/loader/LoaderView"], function (require, exports, e3_12, LoaderView_1) {
	"use strict";
	exports.__esModule = true;
	e3_12.e3.RequestService();
	e3_12.e3.ProtocolService();
	new LoaderView_1.LoaderView().attachTo(e3_12.e3.el(document.body));
});
define("app/login/LoginView", ["require", "exports", "edde/control", "edde/e3"], function (require, exports, control_2, e3_13) {
	"use strict";
	exports.__esModule = true;
	var LoginView = (function (_super) {
		__extends(LoginView, _super);

		function LoginView() {
			return _super.call(this, 'login-view') || this;
		}

		LoginView.prototype.build = function () {
			return e3_13.e3.html('<div>login view!</div>');
		};
		return LoginView;
	}(control_2.AbstractControl));
	exports.LoginView = LoginView;
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiLi4vc3JjL2VkZGUvY29sbGVjdGlvbi50cyIsIi4uL3NyYy9lZGRlL2RvbS50cyIsIi4uL3NyYy9lZGRlL2NvbnZlcnRlci50cyIsIi4uL3NyYy9lZGRlL25vZGUudHMiLCIuLi9zcmMvZWRkZS9wcm90b2NvbC50cyIsIi4uL3NyYy9lZGRlL2VsZW1lbnQudHMiLCIuLi9zcmMvZWRkZS9ldmVudC50cyIsIi4uL3NyYy9lZGRlL2pvYi50cyIsIi4uL3NyYy9lZGRlL2UzLnRzIiwiLi4vc3JjL2VkZGUvcHJvbWlzZS50cyIsIi4uL3NyYy9lZGRlL2FqYXgudHMiLCIuLi9zcmMvZWRkZS9kZWNvcmF0b3IudHMiLCIuLi9zcmMvZWRkZS9jbGllbnQudHMiLCIuLi9zcmMvZWRkZS9jb250cm9sLnRzIiwiLi4vc3JjL2FwcC9sb2FkZXIvTG9hZGVyVmlldy50cyIsIi4uL3NyYy9hcHAvYXBwLnRzIiwiLi4vc3JjL2FwcC9sb2dpbi9Mb2dpblZpZXcudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7OztJQWtRQTtRQUdDLG9CQUFtQixVQUFvQjtZQUFwQiwyQkFBQSxFQUFBLGVBQW9CO1lBQ3RDLElBQUksQ0FBQyxVQUFVLEdBQUcsVUFBVSxDQUFDO1FBQzlCLENBQUM7UUFLTSx3QkFBRyxHQUFWLFVBQVcsSUFBTztZQUNqQixJQUFNLEtBQUssR0FBRyxJQUFJLENBQUMsT0FBTyxFQUFFLENBQUM7WUFDN0IsS0FBSyxDQUFDLEtBQUssQ0FBQyxNQUFNLENBQUMsR0FBRyxJQUFJLENBQUM7WUFDM0IsTUFBTSxDQUFDLElBQUksQ0FBQztRQUNiLENBQUM7UUFLTSx5QkFBSSxHQUFYLFVBQWdDLFFBQTZEO1lBQzVGLElBQU0sT0FBTyxHQUFTO2dCQUNyQixLQUFLLEVBQUUsQ0FBQyxDQUFDO2dCQUNULElBQUksRUFBRSxLQUFLO2dCQUNYLElBQUksRUFBRSxJQUFJO2dCQUNWLEtB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
