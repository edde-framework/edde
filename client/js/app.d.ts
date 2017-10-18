declare module "edde/collection" {
	export interface ILoop<T> {
		count: number;
		loop: boolean;
		item: any | null;
		value: T | null;
		key: number | string | null;
	}

	export interface ICollection<T> {
		add(item: T): ICollection<T>;

		each<U extends ILoop<T>>(callback: (this: U, value: T, index: number) => any | boolean): U;

		subEach<U extends ILoop<T>>(callback: (this: U, value: T, index: number) => any | boolean, start?: number, length?: number): U;

		subCollection(start?: number, length?: number): ICollection<T>;

		reverse(): ICollection<T>;

		toArray(): Array<T>;

		getCount(): number;

		index(index: number): T | null;

		first(): T | null;

		last(): T | null;

		isEmpty(): boolean;

		clear(): ICollection<T>;

		removeBy(callback: (item: T) => boolean, name?: string): ICollection<T>;

		copy(copy: ICollection<T>): ICollection<T>;

		replace(replace: ICollection<T>): ICollection<T>;

		sort(sort?: (alpha: any, beta: any) => number): ICollection<T>;
	}

	export interface IHashMap<T> {
		set(name: string | number, item: T): IHashMap<T>;

		put(put: {}): IHashMap<T>;

		has(name: string): boolean;

		get(name: string, value?: any): T;

		remove(name: string): IHashMap<T>;

		isEmpty(): boolean;

		toObject(): Object;

		each<U extends ILoop<T>>(callback: (this: U, key: string | number, value: T) => any | boolean): U;

		first(): T | null;

		last(): T | null;

		getCount(): number;

		clear(): IHashMap<T>;

		copy(copy: IHashMap<T>): IHashMap<T>;

		replace(replace: IHashMap<T>): IHashMap<T>;

		fromCollection(collection: ICollection<T>, key: (value: T) => string): IHashMap<T>;
	}

	export interface IHashMapCollection<T> {
		add(name: string, item: T): IHashMapCollection<T>;

		has(name: string): boolean;

		sort(name: string, sort?: (alpha: T, beta: T) => number): IHashMapCollection<T>;

		toArray(name: string): Array<T>;

		toCollection(name: string): ICollection<T>;

		each<U extends ILoop<T>>(name: string, callback: (this: U, value: T, index: string | number) => any | boolean): U;

		eachCollection<U extends ILoop<ICollection<T>>>(callback: (this: U, name: string, collection: ICollection<T>) => any | boolean): U;

		remove(name: string): IHashMapCollection<T>;

		removeBy(callback: (item: T) => boolean, name?: string): IHashMapCollection<T>;

		clear(): IHashMapCollection<T>;
	}

	export class Collection<T> implements ICollection<T> {
		protected collection: T[];

		constructor(collection?: T[]);

		add(item: T): ICollection<T>;

		each<U extends ILoop<T>>(callback: (this: U, value: T, index: number) => any | boolean): U;

		subEach<U extends ILoop<T>>(callback: (this: U, value: T, index: number) => any | boolean, start?: number, length?: number): U;

		subCollection(start?: number, length?: number): ICollection<T>;

		reverse(): ICollection<T>;

		toArray(): T[];

		getCount(): number;

		index(index: number): T | null;

		first(): T | null;

		last(): T | null;

		isEmpty(): boolean;

		clear(): ICollection<T>;

		removeBy(callback: (item: T) => boolean, name?: string): ICollection<T>;

		copy(copy: ICollection<T>): ICollection<T>;

		replace(replace: ICollection<T>): ICollection<T>;

		sort(sort?: (alpha: any, beta: any) => number): ICollection<T>;
	}

	export class HashMap<T> implements IHashMap<T> {
		protected hashMap: any;

		constructor(hashMap?: Object);

		set(name: string | number, item: T): IHashMap<T>;

		put(put: Object): IHashMap<T>;

		has(name: string): boolean;

		get(name: string, value?: any): T;

		remove(name: string): IHashMap<T>;

		isEmpty(): boolean;

		toObject(): Object;

		each<U extends ILoop<T>>(callback: (this: U, key: string | number, value: T) => any | boolean): U;

		first(): T | null;

		last(): T | null;

		getCount(): number;

		clear(): IHashMap<T>;

		copy(copy: IHashMap<T>): IHashMap<T>;

		replace(replace: IHashMap<T>): IHashMap<T>;

		fromCollection(collection: ICollection<T>, key: (value: T) => string): IHashMap<T>;
	}

	export class HashMapCollection<T> implements IHashMapCollection<T> {
		protected hashMap: IHashMap<ICollection<T>>;

		add(name: string, item: T): IHashMapCollection<T>;

		has(name: string): boolean;

		sort(name: string, sort?: (alpha: T, beta: T) => number): IHashMapCollection<T>;

		toArray(name: string): T[];

		toCollection(name: string): ICollection<T>;

		each<U extends ILoop<T>>(name: string, callback: (this: U, value: T, index: string | number) => any | boolean): U;

		eachCollection<U extends ILoop<ICollection<T>>>(callback: (this: U, name: string, collection: ICollection<T>) => any | boolean): U;

		remove(name: string): IHashMapCollection<T>;

		removeBy(callback: (item: T) => boolean, name?: string): IHashMapCollection<T>;

		clear(): IHashMapCollection<T>;
	}
}
declare module "edde/dom" {
	import {Collection, ICollection, IHashMap, IHashMapCollection, ILoop} from "edde/collection";
	import {AbstractConverter, IContent, IConverterManager} from "edde/converter";
	import {AbstractPromise, IPromise} from "edde/promise";
	import {INode, Node} from "edde/node";
	import {IEventBus} from "edde/event";
	import {IElement, IElementQueue, IProtocolService, IRequestService} from "edde/protocol";
	import {IHtmlElement, IHtmlElementCollection, ISelector} from "edde/dom";
	import {IAjax} from "edde/ajax";
	import {IJobManager} from "edde/job";
	import {AbstractControl, IControlFactory} from "edde/control";

	export interface IAbstractHtmlElement<T> {
		event(name: string, callback: (event: any) => void): T;

		html(html: string): T;

		attr(name: string, value: string): T;

		attrList(attrList: Object): T;

		toggleClass(name: string | string[], toggle?: boolean): T;

		toggleClassList(nameList: string[], toggle?: boolean): T;

		addClass(name: string | string[]): T;

		addClassList(nameList: string[]): T;

		hasClass(name: string): boolean;

		hasClassList(nameList: string[]): boolean;

		removeClass(name: string): T;

		removeClassList(nameList: string[]): T;

		remove(): T;
	}

	export interface IHtmlElement extends IAbstractHtmlElement<IHtmlElement> {
		getElement(): HTMLElement;

		getId(): string;

		getName(): string;

		data(name: string, value?: any): any;

		text(text: string): IHtmlElement;

		getInnerHtml(): string;

		getOuterHtml(): string;

		clear(): IHtmlElement;

		getOffsetHeight(): number;

		getOffsetWidth(): number;

		getClientHeight(): number;

		getClientWidth(): number;

		getParentList(root?: HTMLElement): ICollection<IHtmlElement>;

		collection(selector: string): IHtmlElementCollection;

		attach(child: IHtmlElement): IHtmlElement;

		attachHtml(html: string): IHtmlElement;

		attachList(elementList: (IHtmlElement | null)[]): IHtmlElement;

		attachTo(parent: IHtmlElement): IHtmlElement;

		position(top: string, left: string): IHtmlElement;
	}

	export interface IHtmlElementCollection extends IAbstractHtmlElement<IHtmlElementCollection> {
		each<U extends ILoop<HTMLElement>>(callback: (this: U, node: IHtmlElement) => any | boolean): U;

		getCount(): number;

		index(index: number): IHtmlElement | null;
	}

	export interface ISelector {
		each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: <U>(htmlElement: IHtmlElement) => any | boolean): U;

		getCount(root: HTMLElement): number;

		index(root: HTMLElement, index: number): IHtmlElement | null;
	}

	export class HtmlElement implements IHtmlElement {
		protected element: HTMLElement;

		constructor(element: HTMLElement);

		getElement(): HTMLElement;

		getId(): string;

		getName(): string;

		event(name: string, callback: (event: any) => void): IHtmlElement;

		data(name: string, value?: any): any;

		toggleClass(name: string, toggle?: boolean): IHtmlElement;

		toggleClassList(nameList: string[], toggle?: boolean): IHtmlElement;

		addClass(name: string): IHtmlElement;

		addClassList(nameList: string[]): IHtmlElement;

		hasClass(name: string): boolean;

		hasClassList(nameList: string[]): boolean;

		removeClass(name: string): IHtmlElement;

		removeClassList(nameList: string[]): IHtmlElement;

		html(html: string): IHtmlElement;

		text(text: string): IHtmlElement;

		attr(name: string, value: string): IHtmlElement;

		attrList(attrList: Object): IHtmlElement;

		getInnerHtml(): string;

		getOuterHtml(): string;

		clear(): IHtmlElement;

		getOffsetHeight(): number;

		getOffsetWidth(): number;

		getClientHeight(): number;

		getClientWidth(): number;

		getParentList(root?: HTMLElement): ICollection<IHtmlElement>;

		collection(selector: string): IHtmlElementCollection;

		attach(child: IHtmlElement): IHtmlElement;

		attachHtml(html: string): IHtmlElement;

		attachList(elementList: (IHtmlElement | null)[]): IHtmlElement;

		attachTo(parent: IHtmlElement): IHtmlElement;

		position(top: string, left: string): IHtmlElement;

		remove(): IHtmlElement;

		className(name: string): string;
	}

	export class HtmlElementCollection implements IHtmlElementCollection {
		protected root: HTMLElement;
		protected selector: ISelector;

		constructor(root: HTMLElement, selector: string);

		event(name: string, callback: (event: any) => void): IHtmlElementCollection;

		addClass(name: string): IHtmlElementCollection;

		addClassList(nameList: string[]): IHtmlElementCollection;

		hasClass(name: string): boolean;

		hasClassList(nameList: string[]): boolean;

		removeClass(name: string): IHtmlElementCollection;

		removeClassList(nameList: string[]): IHtmlElementCollection;

		toggleClass(name: string, toggle?: boolean): IHtmlElementCollection;

		toggleClassList(nameList: string[], toggle?: boolean): IHtmlElementCollection;

		attr(name: string, value: string): IHtmlElementCollection;

		attrList(attrList: Object): IHtmlElementCollection;

		html(html: string): IHtmlElementCollection;

		each<U extends ILoop<HTMLElement>>(callback: (this: U, node: IHtmlElement) => any | boolean): U;

		getCount(): number;

		index(index: number): IHtmlElement | null;

		remove(): IHtmlElementCollection;
	}

	export abstract class AbstractSelector implements ISelector {
		protected selector: string;

		constructor(selector: string);

		getCount(root: HTMLElement): number;

		index(root: HTMLElement, index: number): IHtmlElement | null;

		abstract each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: (this: U, htmlElement: IHtmlElement) => any | boolean): U;
	}

	export class SimpleClassSelector extends AbstractSelector {
		each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: <U>(htmlElement: IHtmlElement) => (any | boolean)): U;
	}

	export class SimpleIdSelector extends AbstractSelector {
		each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: (htmlElement: IHtmlElement) => (any | boolean)): U;
	}

	export class SimpleSelector extends AbstractSelector {
		protected selectorList: string[][] | string;

		constructor(selector: string);

		each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: (this: U, htmlElement: IHtmlElement) => any | boolean): U;
	}
}
declare module "edde/converter" {
	export interface IContent<T> {
		getContent(): T;

		getMime(): string;
	}

	export interface IConverter {
		getMimeList(): string[];

		convert<S, T>(content: S, mime: string, target: string | null): IContent<T>;

		content<S, T>(content: IContent<S>, target: string | null): IContent<T>;
	}

	export interface IConvertable<S, T> {
		getContent(): IContent<S>;

		getTarget(): string | null;

		convert(): IContent<T>;
	}

	export interface IConverterManager {
		registerConverter(converter: IConverter): IConverterManager;

		convert<S, T>(content: S, mime: string, targetList: string[] | null): IConvertable<S, T>;

		content<S, T>(content: IContent<S>, targetList?: string[] | null): IConvertable<S, T>;
	}

	export class Content<T> implements IContent<T> {
		protected content: T;
		protected mime: string;

		constructor(content: T, mime: string);

		getContent(): T;

		getMime(): string;
	}

	export abstract class AbstractConverter implements IConverter {
		protected mimeList: string[];

		protected register(source: string[], target: string[]): void;

		getMimeList(): string[];

		content<S, T>(content: IContent<S>, target: string | null): IContent<T>;

		abstract convert<S, T>(content: S, mime: string, target: string | null): IContent<T>;
	}

	export class Convertable<S, T> implements IConvertable<S, T> {
		protected converter: IConverter;
		protected content: IContent<S>;
		protected target: string | null;
		protected result: IContent<T>;

		constructor(converter: IConverter, content: IContent<S>, target: string | null);

		getContent(): IContent<S>;

		getTarget(): string | null;

		convert(): IContent<T>;
	}

	export class ConverterManager implements IConverterManager {
		protected converterList: IHashMap<IConverter>;

		registerConverter(converter: IConverter): IConverterManager;

		convert<S, T>(content: S, mime: string, targetList: string[] | null): IConvertable<S, T>;

		content<S, T>(content: IContent<S>, targetList: string[] | null): IConvertable<S, T>;
	}

	export class PassConverter extends AbstractConverter {
		convert<S, T>(content: S, mime: string, target: string | null): IContent<T>;
	}

	export class JsonConverter extends AbstractConverter {
		constructor();

		convert<S, T>(content: S, mime: string, target: string | null): IContent<T>;
	}
}
declare module "edde/node" {
	export interface IAbstractNode {
		setParent(parent: IAbstractNode): IAbstractNode;

		detach(): IAbstractNode;

		getParent(): IAbstractNode | null;

		isRoot(): boolean;

		isChild(): boolean;

		addNode(node: IAbstractNode): IAbstractNode;

		addNodeList(nodeList: IAbstractNode[]): IAbstractNode;

		getNodeList(): ICollection<IAbstractNode>;

		getNodeCount(): number;

		each<U extends ILoop<IAbstractNode>>(callback: (this: U, node: IAbstractNode) => any | boolean): U;
	}

	export interface INode extends IAbstractNode {
		setName(name: string): INode;

		getName(): string | null;

		setValue(value: any): INode;

		getValue(): any;

		getAttributeList(): IHashMap<any>;

		setAttribute(name: string, value: any): INode;

		getAttribute(name: string, value?: any): any;

		getMetaList(): IHashMap<any>;

		setMeta(name: string, value: any): INode;

		getMeta(name: string, value?: any): any;
	}

	export abstract class AbstractNode implements IAbstractNode {
		protected parent: IAbstractNode | null;
		protected nodeList: ICollection<IAbstractNode>;

		constructor(parent: IAbstractNode | null);

		setParent(parent: IAbstractNode): IAbstractNode;

		detach(): IAbstractNode;

		getParent(): IAbstractNode | null;

		isRoot(): boolean;

		isChild(): boolean;

		addNode(node: IAbstractNode): IAbstractNode;

		addNodeList(nodeList: IAbstractNode[]): IAbstractNode;

		getNodeList(): ICollection<IAbstractNode>;

		getNodeCount(): number;

		each<U extends ILoop<IAbstractNode>>(callback: (this: U, node: IAbstractNode) => any | boolean): U;
	}

	export class Node extends AbstractNode implements INode {
		protected name: string | null;
		protected value: any;
		protected attributeList: IHashMap<any>;
		protected metaList: IHashMap<any>;

		constructor(name?: string | null, value?: any);

		setName(name: string): INode;

		getName(): string | null;

		setValue(value: any): INode;

		getValue(): any;

		getAttributeList(): IHashMap<any>;

		setAttribute(name: string, value: any): INode;

		getAttribute(name: string, value?: any): any;

		getMetaList(): IHashMap<any>;

		setMeta(name: string, value: any): INode;

		getMeta(name: string, value?: any): any;
	}

	export class NodeConverter extends AbstractConverter {
		constructor();

		convert<S, T>(content: S, mime: string, target: string | null): IContent<T>;
	}
}
declare module "edde/promise" {
	export interface IPromise {
		success(callback: (value?: any) => void): IPromise;

		onSuccess(value?: any): IPromise;

		fail(callback: (value?: any) => void): IPromise;

		onFail(value?: any): IPromise;

		always(callback: (value?: any) => void): IPromise;

		onAlways(value?: any): IPromise;
	}

	export class AbstractPromise implements IPromise {
		protected promiseList: IHashMapCollection<(value?: any) => void>;
		protected resultList: IHashMap<any>;

		protected register(name: string, callback: (value?: any) => void): IPromise;

		protected execute(name: string, value?: any): IPromise;

		success(callback: (value?: any) => void): IPromise;

		onSuccess(value?: any): IPromise;

		fail(callback: (value?: any) => void): IPromise;

		onFail(value?: any): IPromise;

		always(callback: (value?: any) => void): IPromise;

		onAlways(value?: any): IPromise;
	}
}
declare module "edde/protocol" {
	export interface IElement extends INode {
		getType(): string;

		isType(type: string): boolean;

		getId(): string;

		async(async: boolean): IElement;

		isAsync(): boolean;

		setReference(element: IElement): IElement;

		hasReference(): boolean;

		getReference(): string | null;

		data(data: {}): IElement;

		addElement(name: string, element: IElement): IElement;

		addElementCollection(name: string, collection: ICollection<IElement>): IElement;

		getElementNode(name: string): INode | null;

		getElementList(name: string): ICollection<IElement>;

		getReferenceBy(id: string): IElement | null;

		getReferenceList(id: string): ICollection<IElement>;
	}

	export interface IElementQueue extends ICollection<IElement> {
		queue(element: IElement): IElementQueue;

		createPacket(): IElement;
	}

	export interface IProtocolService {
		execute(element: IElement): any;
	}

	export interface IRequestService {
		setAccept(accept: string): IRequestService;

		setTarget(target: string): IRequestService;

		request(element: IElement, callback?: (element: IElement) => void): IPromise;
	}

	export class ProtocolService implements IProtocolService {
		protected eventBus: IEventBus;
		protected handleList: IHashMap<(element: IElement) => void>;

		constructor(eventBus: IEventBus);

		execute(element: IElement): any;

		handlePacket(element: IElement): IElement;

		handleEvent(element: IElement): void;
	}

	export class RequestService implements IRequestService {
		protected url: string;
		protected accept: string;
		protected target: string;

		constructor(url: string);

		setAccept(accept: string): IRequestService;

		setTarget(target: string): IRequestService;

		request(element: IElement, callback?: (element: IElement) => void): IPromise;
	}

	export class ElementConverter extends AbstractConverter {
		constructor();

		convert<S, T>(content: S, mime: string, target: string | any): IContent<T>;
	}
}
declare module "edde/element" {
	export class ProtocolElement extends Node implements IElement {
		constructor(type?: string, id?: string);

		getType(): string;

		isType(type: string): boolean;

		getId(): string;

		async(async: boolean): IElement;

		isAsync(): boolean;

		setReference(element: IElement): IElement;

		hasReference(): boolean;

		getReference(): string | null;

		data(data: {}): IElement;

		addElement(name: string, element: IElement): IElement;

		addElementCollection(name: string, collection: ICollection<IElement>): IElement;

		getElementNode(name: string): INode | null;

		getElementList(name: string): ICollection<IElement>;

		getReferenceBy(id: string): IElement | null;

		getReferenceList(id: string): ICollection<IElement>;
	}

	export class EventElement extends ProtocolElement {
		constructor(event: string);
	}

	export class PacketElement extends ProtocolElement {
		constructor(origin: string, id?: string);

		element(element: IElement): PacketElement;

		reference(element: IElement): PacketElement;
	}

	export class ErrorElement extends ProtocolElement {
		constructor(code: number, message: string);

		setException(exception: string): ErrorElement;
	}

	export class RequestElement extends ProtocolElement {
		constructor(request: string);
	}

	export class MessageElement extends ProtocolElement {
		constructor(request: string);
	}

	export class ResponseElement extends ProtocolElement {
		constructor();
	}

	export class ElementQueue extends Collection<IElement> implements IElementQueue {
		queue(element: IElement): IElementQueue;

		createPacket(): IElement;
	}
}
declare module "edde/event" {
	export interface IEventBus {
		listen(event: string | null, handler: (element: IElement) => void, weight: number, context?: Object, scope?: string): IEventBus;

		register(instance: Object, scope?: string): IEventBus;

		remove(scope?: string): IEventBus;

		event(element: IElement): IEventBus;

		emit(event: string, data: Object): IEventBus;
	}

	export interface IListener {
		event: string | null;
		handler: (element: IElement) => void;
		weight: number;
		context: Object | null;
		scope: string | null;
		cancelable: boolean;
	}

	export class EventBus implements IEventBus {
		protected listenerList: IHashMapCollection<IListener>;

		listen(event: string | null, handler: (element: IElement) => void, weight?: number, context?: Object, scope?: string, cancelable?: boolean): IEventBus;

		register(instance: any, scope?: string): IEventBus;

		remove(scope?: string): IEventBus;

		event(event: IElement): IEventBus;

		emit(event: string, data?: Object): IEventBus;
	}
}
declare module "edde/ajax" {
	export interface IAjaxHandler extends IPromise {
		serverFail(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

		onServerFail(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

		clientFail(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

		onClientFail(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

		timeout(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

		onTimeout(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

		error(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

		onError(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;
	}

	export interface IAjax {
		setAccept(accept: string): IAjax;

		setAsync(async: boolean): IAjax;

		setTimeout(timeout: number): IAjax;

		getAjaxHandler(): IAjaxHandler;

		execute<T>(content?: IContent<T>): IAjaxHandler;
	}

	export class AjaxHandler extends AbstractPromise implements IAjaxHandler {
		serverFail(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

		onServerFail(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

		clientFail(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

		onClientFail(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

		timeout(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

		onTimeout(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

		error(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

		onError(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;
	}

	export class Ajax implements IAjax {
		protected url: string;
		protected method: string;
		protected accept: string;
		protected async: boolean;
		protected timeout: number;
		protected ajaxHandler: IAjaxHandler;

		constructor(url: string);

		setMethod(method: string): IAjax;

		setAccept(accept: string): IAjax;

		setAsync(async: boolean): IAjax;

		setTimeout(timeout: number): IAjax;

		getAjaxHandler(): IAjaxHandler;

		execute<T>(content: IContent<T>): IAjaxHandler;
	}
}
declare module "edde/job" {
	export interface IJobManager {
		queue(element: IElement): IJobManager;

		execute(): IJobManager;
	}

	export class JobManager implements IJobManager {
		protected jobQueue: ICollection<IElement>;
		protected queueId: any;

		queue(element: IElement): IJobManager;

		execute(): IJobManager;
	}
}
declare module "edde/decorator" {
	export class Listen {
		static To(event: string | null, weight?: number, cancelable?: boolean): (target: any, property: string) => void;

		static ToNative(event: string): (target: any, property: string) => void;
	}
}
declare module "edde/control" {
	export interface IControl {
		attach(element: IHtmlElement): IHtmlElement;

		attachHtml(html: string): IHtmlElement;

		attachTo(root: IHtmlElement): IControl;

		build(): IHtmlElement;

		render(): IHtmlElement;

		getElement(): IHtmlElement | null;

		update(element: IElement): IControl;

		isListening(): boolean;
	}

	export interface IControlFactory {
	}

	export abstract class AbstractControl implements IControl {
		protected name: string;
		protected element: IHtmlElement | null;

		constructor(name: string);

		attach(element: IHtmlElement): IHtmlElement;

		attachHtml(html: string): IHtmlElement;

		attachTo(root: IHtmlElement): IControl;

		render(): IHtmlElement;

		getElement(): IHtmlElement | any;

		update(element: IElement): IControl;

		isListening(): boolean;

		abstract build(): IHtmlElement;
	}

	export class ControlFactory implements IControlFactory {
		eventControlCreate(element: IElement): void;
	}
}
declare module "edde/e3" {
	export class e3 {
		protected static eventBus: IEventBus;
		protected static protocolService: IProtocolService;
		protected static requestService: IRequestService;
		protected static elementQueue: IElementQueue;
		protected static jobManager: IJobManager;
		protected static converterManager: IConverterManager;
		protected static controlFactory: IControlFactory;
		protected static classList: IHashMap<any>;
		protected static heartbeatId: any;

		version(): string;

		static EventBus(): IEventBus;

		static ProtocolService(): IProtocolService;

		static RequestService(url?: string): IRequestService;

		static ElementQueue(): IElementQueue;

		static JobManager(): IJobManager;

		static ConverterManager(): IConverterManager;

		static ControlFactory(): IControlFactory;

		static Event(event: string, data?: Object): IElement;

		static Message(message: string, data?: Object): IElement;

		static Element(type: string): IElement;

		static Node(name?: string | null, value?: any): INode;

		static collection<T>(collection?: T[]): ICollection<T>;

		static $<T, U extends ILoop<T>>(collection: T[], callback: (this: U, value: T, index: number) => any | boolean): U;

		static collectionEach<T, U extends ILoop<T>>(collection: T[], callback: (this: U, value: T, index: number) => any | boolean): U;

		static hashMap<T>(hashMap?: Object): IHashMap<T>;

		static $$<T, U extends ILoop<T>>(hashMap: Object, callback: (this: U, name: string, value: T) => any | boolean): U;

		static hashMapEach<T, U extends ILoop<T>>(hashMap: Object, callback: (this: U, key: string, value: T) => any | boolean): U;

		static hashMapCollection<T>(): IHashMapCollection<T>;

		static El(name: string, classList?: string[], factoryDocument?: Document): IHtmlElement;

		static Text(text: string, factoryDocument?: Document): Text;

		static el(element: HTMLElement): IHtmlElement;

		static html(html: string): IHtmlElement;

		static Selector(selector: string): ISelector;

		static reflow(callback: () => any): any;

		static selector(selector: string, root?: HTMLElement): IHtmlElementCollection;

		static listen(event: string | null, handler: (event: IElement) => void, weight?: number, context?: Object, scope?: string): IEventBus;

		static listener<T>(instance: T, scope?: string): T;

		static unlisten(scope?: string): IEventBus;

		static event(event: IElement): IEventBus;

		static emit(event: string, data?: Object): IEventBus;

		static request(element: IElement, callback?: (element: IElement) => void): IPromise;

		static execute(element: IElement): any;

		static job(element?: IElement): IJobManager;

		static create<T>(create: string, parameterList?: any[], singleton?: boolean): T;

		static queue(element: IElement): IElementQueue;

		static toNode<T extends INode>(object: Object, node?: T | null, factory?: ((name?: string) => T)): T;

		static fromNode(root: INode): Object;

		static convert<S, T>(content: S, mime: string, targetList: string[]): IContent<T>;

		static toJsonNode(root: INode): string;

		static nodeFromJson<T extends INode>(json?: string | null, factory?: (name?: string) => T): T;

		static elementFromJson(json: string | null): IElement;

		static elementFromObject(object: Object): IElement;

		static tree(root: INode, callback: (node: INode) => any | boolean): any | boolean;

		static ajax(url: string): IAjax;

		static packet(selector?: string, root?: HTMLElement): void;

		static heartbeat(interval?: number): typeof e3 | undefined;

		static extend(...objectList: any[]): Object;

		static formEncode(object: any, prefix?: string): string;

		static getInstanceName(instance: any): string;

		static isScalar(value: any): boolean;

		static isArray(value: any): boolean;

		static isObject(value: any): boolean;

		static isIterable(value: any): boolean;

		static guid(glue?: string, a?: any, b?: any): any;
	}
}
declare module "app/app" {
}
declare module "app/loader/LoaderView" {
	export class LoaderView extends AbstractControl {
		constructor();

		build(): IHtmlElement;
	}
}
declare module "app/login/LoginView" {
	export class LoginView extends AbstractControl {
		constructor();

		build(): IHtmlElement;
	}
}
declare module "edde/client" {
	export interface IClientClass {
		attach(htmlElement: IHtmlElement): IHtmlElement;

		attachHtml(html: string): IHtmlElement;

		getElement(): IHtmlElement;
	}

	export abstract class AbstractClientClass implements IClientClass {
		protected element: IHtmlElement;

		attach(htmlElement: IHtmlElement): IHtmlElement;

		attachHtml(html: string): IHtmlElement;

		getElement(): IHtmlElement;
	}

	export abstract class AbstractButton extends AbstractClientClass {
		onClick(event?: any): void;
	}
}
declare module "app/index/MainBarControl" {
	export class MainBarControl extends AbstractControl {
		constructor();

		build(): IHtmlElement;
	}
}
declare module "app/index/RegisterButton" {
	export class RegisterButton extends AbstractControl {
		build(): IHtmlElement;

		onClick(): void;
	}
}
declare module "app/index/IndexView" {
	export class IndexView extends AbstractControl {
		build(): IHtmlElement;
	}
}
