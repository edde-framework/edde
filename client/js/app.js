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
			this.event(new element_2.EventElement(event).data(data));
			return this;
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
	e3_10.e3.emit('control/create', {
		'control': 'app/index/IndexView:IndexView',
		'root': e3_10.e3.el(document.body)
	});
});
define("edde/client", ["require", "exports", "edde/e3", "edde/decorator"], function (require, exports, e3_11, decorator_2) {
	"use strict";
	exports.__esModule = true;
	var AbstractClientClass = (function () {
		function AbstractClientClass() {
		}

		AbstractClientClass.prototype.attach = function (htmlElement) {
			var _this = this;
			var dom = (this.element = htmlElement).getElement();
			e3_11.e3.$$(this, function (name, value) {
				return name.indexOf('::NativeListenerList/', 0) !== -1 ? e3_11.e3.$(value, function (listener) {
					return dom.addEventListener(listener.event, function (event) {
						return _this[listener.handler].call(_this, event);
					}, false);
				}) : null;
			});
			return this.element;
		};
		AbstractClientClass.prototype.attachHtml = function (html) {
			return this.attach(e3_11.e3.html(html));
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
			decorator_2.Listen.ToNative('click')
		], AbstractButton.prototype, "onClick");
		return AbstractButton;
	}(AbstractClientClass));
	exports.AbstractButton = AbstractButton;
});
define("app/index/RegisterButton", ["require", "exports", "edde/control", "edde/e3", "edde/decorator"], function (require, exports, control_2, e3_12, decorator_3) {
	"use strict";
	exports.__esModule = true;
	var RegisterButton = (function (_super) {
		__extends(RegisterButton, _super);

		function RegisterButton() {
			return _super.call(this, 'register-button') || this;
		}

		RegisterButton.prototype.build = function () {
			return e3_12.e3.html("\n\t\t\t<p class=\"control\">\n\t\t\t\t<span class=\"button\">\n\t\t\t\t\t<span class=\"icon\"><i class=\"fa fa-user-circle\"></i></span>\n\t\t\t\t\t<span>Register</span>\n\t\t\t\t</span>\n\t\t\t</p>\n\t\t");
		};
		RegisterButton.prototype.onClick = function () {
			alert('yapee!');
		};
		__decorate([
			decorator_3.Listen.ToNative('click')
		], RegisterButton.prototype, "onClick");
		return RegisterButton;
	}(control_2.AbstractControl));
	exports.RegisterButton = RegisterButton;
});
define("app/index/LoginButton", ["require", "exports", "edde/control", "edde/e3"], function (require, exports, control_3, e3_13) {
	"use strict";
	exports.__esModule = true;
	var LoginButton = (function (_super) {
		__extends(LoginButton, _super);

		function LoginButton() {
			return _super.call(this, 'login-button') || this;
		}

		LoginButton.prototype.build = function () {
			return e3_13.e3.html("\n\t\t\t<p class=\"control\">\n\t\t\t\t<span class=\"button is-primary\">\n\t\t\t\t\t<span class=\"icon\"><i class=\"fa fa-lock\"></i></span>\n\t\t\t\t\t<span>Login</span>\n\t\t\t\t</span>\n\t\t\t</p>\n\t\t");
		};
		return LoginButton;
	}(control_3.AbstractControl));
	exports.LoginButton = LoginButton;
});
define("app/index/MainBarControl", ["require", "exports", "edde/control", "edde/e3", "app/index/RegisterButton", "app/index/LoginButton"], function (require, exports, control_4, e3_14, RegisterButton_1, LoginButton_1) {
	"use strict";
	exports.__esModule = true;
	var MainBarControl = (function (_super) {
		__extends(MainBarControl, _super);

		function MainBarControl() {
			return _super.call(this, 'main-bar-control') || this;
		}

		MainBarControl.prototype.build = function () {
			return e3_14.e3.html('<nav class="navbar is-white"></nav>').attach(e3_14.e3.html('<div class="container"></div>').attachList([
				e3_14.e3.html("\n\t\t\t\t\t<div class=\"navbar-brand\">\n\t\t\t\t\t\t<a class=\"navbar-item\" href=\"/\">\n\t\t\t\t\t\t\t<div class=\"field is-grouped\">\n\t\t\t\t\t\t\t\t<p class=\"control\">\n\t\t\t\t\t\t\t\t\t<img src=\"/img/logo.png\"/>\n\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t<p class=\"control\">\n\t\t\t\t\t\t\t\t\t<span>Edde Framework</span>\n\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</a>\n\t\t\t\t\t</div>\n\t\t\t\t"),
				e3_14.e3.html('<div class="navbar-menu"></div>').attach(e3_14.e3.html('<div class="navbar-end"></div>').attach(e3_14.e3.html('<span class="navbar-item">').attach(e3_14.e3.html('<div class="field is-grouped"></div>').attachList([
					this.use(new RegisterButton_1.RegisterButton()),
					this.use(new LoginButton_1.LoginButton()),
				]))))
			]));
		};
		return MainBarControl;
	}(control_4.AbstractControl));
	exports.MainBarControl = MainBarControl;
});
define("app/index/IndexView", ["require", "exports", "edde/control", "edde/e3", "app/index/MainBarControl"], function (require, exports, control_5, e3_15, MainBarControl_1) {
	"use strict";
	exports.__esModule = true;
	var IndexView = (function (_super) {
		__extends(IndexView, _super);

		function IndexView() {
			return _super !== null && _super.apply(this, arguments) || this;
		}

		IndexView.prototype.build = function () {
			return e3_15.e3.El('div').attachList([
				this.use(new MainBarControl_1.MainBarControl()),
				e3_15.e3.html("\n\t\t\t\t<section class=\"hero is-small is-bold is-info\">\n\t\t\t\t\t<div class=\"hero-body\">\n\t\t\t\t\t\t<div class=\"container\">\n\t\t\t\t\t\t\t<div class=\"columns is-vcentered\">\n\t\t\t\t\t\t\t\t<div class=\"column\">\n\t\t\t\t\t\t\t\t\t<p class=\"title\">Welcome to Edde Framework</p>\n\t\t\t\t\t\t\t\t\t<p class=\"subtitle\">...epic, fast and modern Framework</p>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</section>\n\t\t\t")
			]);
		};
		return IndexView;
	}(control_5.AbstractControl));
	exports.IndexView = IndexView;
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiLi4vc3JjL2VkZGUvY29sbGVjdGlvbi50cyIsIi4uL3NyYy9lZGRlL2RvbS50cyIsIi4uL3NyYy9lZGRlL2NvbnZlcnRlci50cyIsIi4uL3NyYy9lZGRlL25vZGUudHMiLCIuLi9zcmMvZWRkZS9wcm9taXNlLnRzIiwiLi4vc3JjL2VkZGUvcHJvdG9jb2wudHMiLCIuLi9zcmMvZWRkZS9lbGVtZW50LnRzIiwiLi4vc3JjL2VkZGUvZXZlbnQudHMiLCIuLi9zcmMvZWRkZS9hamF4LnRzIiwiLi4vc3JjL2VkZGUvam9iLnRzIiwiLi4vc3JjL2VkZGUvZGVjb3JhdG9yLnRzIiwiLi4vc3JjL2VkZGUvY29udHJvbC50cyIsIi4uL3NyYy9lZGRlL2UzLnRzIiwiLi4vc3JjL2FwcC9hcHAudHMiLCIuLi9zcmMvZWRkZS9jbGllbnQudHMiLCIuLi9zcmMvYXBwL2luZGV4L1JlZ2lzdGVyQnV0dG9uLnRzIiwiLi4vc3JjL2FwcC9pbmRleC9Mb2dpbkJ1dHRvbi50cyIsIi4uL3NyYy9hcHAvaW5kZXgvTWFpbkJhckNvbnRyb2wudHMiLCIuLi9zcmMvYXBwL2luZGV4L0luZGV4Vmlldy50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0lBa1FBO1FBR0Msb0JBQW1CLFVBQW9CO1lBQXBCLDJCQUFBLEVBQUEsZUFBb0I7WUFDdEMsSUFBSSxDQUFDLFVBQVUsR0FBRyxVQUFVLENBQUM7UUFDOUIsQ0FBQztRQUtNLHdCQUFHLEdBQVYsVUFBVyxJQUFPO1lBQ2pCLElBQU0sS0FBSyxHQUFHLElBQUksQ0FBQyxPQUFPLEVBQUUsQ0FBQztZQUM3QixLQUFLLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxHQUFHLElBQUksQ0FBQztZQUMzQixNQUFNLENBQUMsSUFBSSxDQUFDO1FBQ2IsQ0FBQztRQUtNLHlCQUFJLEdBQVgsVUFBZ0MsUUFBNkQ7WUFDNUYsSUFBTSxPQUFPLEdBQVM7Z0JB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
