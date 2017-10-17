import {Collection, HashMap, HashMapCollection, ICollection, IHashMap, IHashMapCollection, ILoop} from "./collection";
import {HtmlElement, HtmlElementCollection, IHtmlElement, IHtmlElementCollection, ISelector, SimpleClassSelector, SimpleIdSelector, SimpleSelector} from "./dom";
import {EventBus, IEventBus} from "./event";
import {ElementQueue, EventElement, MessageElement, ProtocolElement} from "./element";
import {INode, Node, NodeConverter} from "./node";
import {ElementConverter, IElement, IElementQueue, IProtocolService, IRequestService, ProtocolService, RequestService} from "./protocol";
import {Ajax, IAjax} from "./ajax";
import {IJobManager, JobManager} from "./job";
import {IPromise} from "./promise";
import {ConverterManager, IContent, IConverterManager, JsonConverter} from "./converter";
import {ControlFactory, IControlFactory} from "./control";

declare function require(module: string): any;

/**
 * This is a core of the library: it's like $ in jQuery.
 *
 * e3 stands for e(dde) :).
 */
export class e3 {
	protected static eventBus: IEventBus;
	protected static protocolService: IProtocolService;
	protected static requestService: IRequestService;
	protected static elementQueue: IElementQueue;
	protected static jobManager: IJobManager;
	protected static converterManager: IConverterManager;
	protected static controlFactory: IControlFactory;
	protected static classList: IHashMap<any> = e3.hashMap();
	protected static heartbeatId: any;

	public version() {
		return '4.0.0.0';
	}

	public static EventBus(): IEventBus {
		return this.eventBus ? this.eventBus : this.eventBus = new EventBus();
	}

	public static ProtocolService(): IProtocolService {
		return this.protocolService ? this.protocolService : this.protocolService = new ProtocolService(this.EventBus());
	}

	public static RequestService(url?: string): IRequestService {
		return this.requestService ? this.requestService : this.requestService = new RequestService(url || this.el(document.body).data('protocol'));
	}

	public static ElementQueue(): IElementQueue {
		return this.elementQueue ? this.elementQueue : this.elementQueue = new ElementQueue();
	}

	public static JobManager(): IJobManager {
		return this.jobManager ? this.jobManager : this.jobManager = new JobManager();
	}

	public static ConverterManager(): IConverterManager {
		if (this.converterManager) {
			return this.converterManager;
		}
		this.converterManager = new ConverterManager();
		this.converterManager.registerConverter(new JsonConverter());
		this.converterManager.registerConverter(new NodeConverter());
		this.converterManager.registerConverter(new ElementConverter());
		return this.converterManager;
	}

	public static ControlFactory(): IControlFactory {
		return this.controlFactory ? this.controlFactory : this.controlFactory = e3.listener(new ControlFactory());
	}

	public static Event(event: string, data: Object = {}): IElement {
		return new EventElement(event).data(data);
	}

	public static Message(message: string, data: Object = {}): IElement {
		return new MessageElement(message).data(data);
	}

	public static Element(type: string): IElement {
		return new ProtocolElement(type);
	}

	public static Node(name: string | null = null, value: any = null): INode {
		return new Node(name, value);
	}

	public static collection<T>(collection: T[] = []): ICollection<T> {
		return new Collection<T>(collection);
	}

	public static $<T, U extends ILoop<T>>(collection: T[], callback: (this: U, value: T, index: number) => any | boolean): U {
		return new Collection<T>(collection).each<U>(callback);
	}

	public static collectionEach<T, U extends ILoop<T>>(collection: T[], callback: (this: U, value: T, index: number) => any | boolean): U {
		return this.$<T, U>(collection, callback);
	}

	public static hashMap<T>(hashMap: Object = {}): IHashMap<T> {
		return new HashMap<T>(hashMap);
	}

	public static $$<T, U extends ILoop<T>>(hashMap: Object, callback: (this: U, name: string, value: T) => any | boolean): U {
		return this.hashMap<T>(hashMap).each<U>(callback);
	}

	public static hashMapEach<T, U extends ILoop<T>>(hashMap: Object, callback: (this: U, key: string, value: T) => any | boolean): U {
		return this.$$<T, U>(hashMap, callback);
	}

	public static hashMapCollection<T>(): IHashMapCollection<T> {
		return new HashMapCollection<T>();
	}

	public static El(name: string, classList: string[] = [], factoryDocument?: Document): IHtmlElement {
		return new HtmlElement((factoryDocument || document).createElement(name)).addClassList(classList);
	}

	public static Text(text: string, factoryDocument?: Document): Text {
		return (factoryDocument || document).createTextNode(text);
	}

	public static el(element: HTMLElement): IHtmlElement {
		return new HtmlElement(element);
	}

	public static html(html: string): IHtmlElement {
		const node = document.createElement('div');
		node.innerHTML = html;
		return e3.el(<HTMLElement>node.firstChild);
	}

	public static Selector(selector: string): ISelector {
		if (selector.match(/^\.[a-zA-Z0-9_-]+$/)) {
			return new SimpleClassSelector(selector.substr(1));
		} else if (selector.match(/^#[a-zA-Z0-9_-]+$/)) {
			return new SimpleIdSelector(selector.substr(1));
		}
		return new SimpleSelector(selector);
	}

	/**
	 * despite it's name, this method is trying to avoid more reflows than necessary by detaching document element
	 *
	 * method for optimized document update: it does at least two reflows - one when detached, one when attached; it should be used for
	 * some heavier operations, which does not require size computations; when this technique is not available, callback is called directly
	 */
	public static reflow(callback: () => any): any {
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
		} catch (e) {
			return callback();
		}
	}

	public static selector(selector: string, root?: HTMLElement): IHtmlElementCollection {
		return new HtmlElementCollection(root || document.body, selector);
	}

	public static listen(event: string | null, handler: (event: IElement) => void, weight: number = 100, context?: Object, scope?: string): IEventBus {
		return this.EventBus().listen(event, handler, weight, context, scope);
	}

	public static listener<T>(instance: T, scope?: string): T {
		this.EventBus().register(instance, scope);
		return instance;
	}

	public static unlisten(scope?: string): IEventBus {
		return this.EventBus().remove(scope);
	}

	public static event(event: IElement): IEventBus {
		return this.EventBus().event(event);
	}

	public static emit(event: string, data: Object = {}) {
		return this.EventBus().emit(event, data);
	}

	public static request(element: IElement, callback?: (element: IElement) => void): IPromise {
		return this.RequestService().request(element, callback);
	}

	public static execute(element: IElement): any {
		return e3.ProtocolService().execute(element);
	}

	public static job(element?: IElement): IJobManager {
		return element ? this.JobManager().queue(element) : this.JobManager().execute();
	}

	public static create<T>(create: string, parameterList: any[] = [], singleton: boolean = false): T {
		if (singleton === true && this.classList.has(create)) {
			return this.classList.get(create);
		}
		try {
			let module = create.split(':');
			const constructor = require(module[0])[module[1]];
			const instance = parameterList.length > 0 ? ((callback: any, parameterList: any[]): any => {
				const constructor = callback;

				class Constructor {
					public constructor() {
						constructor.apply(this, parameterList);
					}
				}

				Constructor.prototype = constructor.prototype;
				return new Constructor;
			})(constructor, parameterList) : new constructor;
			singleton ? this.classList.set(create, instance) : null;
			return instance;
		} catch (e) {
			throw new Error("Cannot create [" + create + "]:\n" + e);
		}
	}

	/**
	 * enqueue the given packet; when request service will be executed, enqueued element are taken to "the other side"
	 *
	 * @param {IElement} element
	 * @returns {IElementQueue}
	 */
	public static queue(element: IElement): IElementQueue {
		return this.ElementQueue().queue(element);
	}

	public static toNode<T extends INode>(object: Object, node?: T | null, factory?: ((name?: string) => T)): T {
		const callback = factory || ((name?: string): any => {
			return new Node(name ? name : null);
		});
		let root: T = node || callback();
		e3.$$(object, (name: string, value: any) => {
			if (name === '::name') {
				root.setName(value);
			} else if (name === '::value') {
				root.setValue(value);
			} else if (name === '::attr') {
				root.getAttributeList().put(value);
			} else if (name === '::meta') {
				root.getMetaList().put(value);
			} else if (e3.isObject(value)) {
				root.addNode(this.toNode(value, callback(name), factory));
			} else if (e3.isArray(value)) {
				e3.$(value, value2 => root.addNode(this.toNode(value2, callback(name), factory)));
			} else {
				root.setAttribute(name, value);
			}
		});
		if (root.getName() === null && root.getNodeCount() === 1) {
			return <T>(<T>root.getNodeList().first()).detach();
		}
		return root;
	}

	public static fromNode(root: INode): Object {
		const attributeList = root.getAttributeList();
		const metaList = root.getMetaList();
		const value = root.getValue();
		let object: any = {};
		if (value) {
			object['::value'] = value;
		}
		if (attributeList.isEmpty() === false) {
			object = e3.extend(object, attributeList.toObject());
		}
		if (metaList.isEmpty() === false) {
			object['::meta'] = metaList.toObject();
		}
		const nodeList: IHashMapCollection<Object> = e3.hashMapCollection<Object>();
		root.each((node: INode) => nodeList.add(node.getName() || '<node>', this.fromNode(node)));
		nodeList.eachCollection((name, collection) => object[name] = collection.getCount() === 1 ? collection.first() : collection.toArray());
		if (root.isRoot()) {
			const rootObject: any = {};
			rootObject[root.getName() || '<root>'] = object;
			return rootObject;
		}
		return object;
	}

	public static convert<S, T>(content: S, mime: string, targetList: string[]): IContent<T> {
		return this.ConverterManager().convert<S, T>(<any>content, mime, targetList).convert();
	}

	public static toJsonNode(root: INode): string {
		return JSON.stringify(this.fromNode(root))
	}

	public static nodeFromJson<T extends INode>(json?: string | null, factory?: (name?: string) => T): T {
		return this.toNode<T>(JSON.parse(json || '{}'), null, factory);
	}

	public static elementFromJson(json: string | null): IElement {
		return this.nodeFromJson(json, (name?: string): IElement => {
			return new ProtocolElement(name);
		})
	}

	public static elementFromObject(object: Object): IElement {
		return this.toNode(object, null, (name?: string): IElement => {
			return new ProtocolElement(name);
		});
	}

	public static tree(root: INode, callback: (node: INode) => any | boolean): any | boolean {
		root.each((node: INode) => this.tree(node, callback));
		return callback(root);
	}

	public static ajax(url: string): IAjax {
		return new Ajax(url);
	}

	public static packet(selector?: string, root?: HTMLElement) {
		this.el(root || document.body).collection(selector || '.packet').each(node => this.job(this.elementFromJson(node.getInnerHtml())));
		this.job();
	}

	public static heartbeat(interval: number = 3000) {
		if (this.heartbeatId) {
			return this;
		}
		const heartbeat = () => this.request(e3.Event('protocol/heartbeat')).always(() => this.heartbeatId = setTimeout(heartbeat, interval));
		this.heartbeatId = setTimeout(heartbeat, interval);
		this.listen('heartbeat/stop', () => clearTimeout(this.heartbeatId), 0);
	}

	public static extend(...objectList: any[]): Object {
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

	public static formEncode(object: any, prefix?: string): string {
		let list: string[] = [];
		const encode = (key: string, value: any, prefix?: string) => {
			const name = prefix ? prefix + '[' + key + ']' : key;
			list[list.length] = this.isScalar(value) === false ? this.formEncode(value, name) : (encodeURIComponent(name) + '=' + encodeURIComponent(value == null ? '' : value));
		};
		this.isArray(object) ? this.$<string, any>(object, (value, index) => encode(String(index), value, prefix)) : this.$$<string, any>(object, (key, value) => encode(key, value, prefix));
		return list.join('&').replace(/%20/g, '+');
	}

	public static getInstanceName(instance: any): string {
		return (instance.constructor.name ? instance.constructor.name : ("" + instance.constructor).split("function ")[1].split("(")[0]);
	}

	public static isScalar(value: any): boolean {
		switch (typeof value) {
			case 'string':
			case 'number':
			case 'boolean':
				return true;
		}
		return false;
	}

	public static isArray(value: any): boolean {
		if (this.isScalar(value)) {
			return false;
		}
		return value && value.length !== undefined && this.getInstanceName(value) === 'Array';
	}

	public static isObject(value: any): boolean {
		if (this.isScalar(value)) {
			return false;
		}
		return value && typeof value === 'object' && this.isArray(value) === false;
	}

	public static isIterable(value: any): boolean {
		if (this.isScalar(value)) {
			return false;
		}
		return value && value.hasOwnProperty('length') && value.hasOwnProperty(0) && value.hasOwnProperty(value.length - 1);
	}

	public static guid(glue: string = '-', a?: any, b?: any) {
		for (b = a = ''; a++ < 36; b += a * 51 & 52 ? (a ^ 15 ? 8 ^ Math.random() * (a ^ 20 ? 16 : 4) : 4).toString(16) : glue) ;
		return b;
	}
}
