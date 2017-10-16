import {PacketElement} from "./element";
import {e3} from "./e3";
import {ICollection, IHashMap} from "./collection";
import {IPromise} from "./promise";
import {INode} from "./node";
import {IEventBus} from "./event";
import {AbstractConverter, Content, IContent} from "./converter";

/**
 * Protocol element implementation.
 */
export interface IElement extends INode {
	/**
	 * index the type of an element
	 */
	getType(): string;

	/**
	 * is the element of the given type (event, request, ...)
	 *
	 * @param type
	 */
	isType(type: string): boolean;

	/**
	 * index the element id
	 */
	getId(): string;

	/**
	 * set the async flag for this element
	 *
	 * @param async
	 */
	async(async: boolean): IElement;

	/**
	 * should be this element processed asynchronously?
	 */
	isAsync(): boolean;

	/**
	 * this element would point to the given element as a reference (something like this.reference -> Element)
	 *
	 * @param element
	 */
	setReference(element: IElement): IElement;

	/**
	 * has this element reference (is referencing to some other element?)
	 */
	hasReference(): boolean;

	/**
	 * return referenced element id (if there is some)
	 */
	getReference(): string | null;

	/**
	 * set the object as current data of an Element
	 *
	 * @param data
	 */
	data(data: {}): IElement;

	/**
	 * add element to the element list of the given name
	 *
	 * @param name
	 * @param element
	 */
	addElement(name: string, element: IElement): IElement;

	addElementCollection(name: string, collection: ICollection<IElement>): IElement;

	/**
	 * index element node
	 *
	 * @param name
	 */
	getElementNode(name: string): INode | null;

	/**
	 * index list of elements of the given node name
	 *
	 * @param name
	 */
	getElementList(name: string): ICollection<IElement>;

	/**
	 * index element by the given reference id
	 *
	 * @param id
	 */
	getReferenceBy(id: string): IElement | null;

	/**
	 * index collection of elements referencing to the given id
	 *
	 * @param id
	 */
	getReferenceList(id: string): ICollection<IElement>;
}

/**
 * General element queue for execution and maintaining relations of current
 * set of elements.
 */
export interface IElementQueue extends ICollection<IElement> {
	/**
	 * enqueue the given element
	 *
	 * @param element
	 */
	queue(element: IElement): IElementQueue;

	/**
	 * create packet with current payload
	 */
	createPacket(): IElement;
}

/**
 * Definition of general protocol service and it's behavior; this should be compatible with
 * protocol defined by Edde Framework here:
 * https://github.com/edde-framework/edde-framework/wiki/the-protocol-specification
 */
export interface IProtocolService {
	/**
	 * execute element in this protocol handler
	 *
	 * @param element
	 */
	execute(element: IElement): any;
}

export interface IRequestService {
	setAccept(accept: string): IRequestService;

	setTarget(target: string): IRequestService;

	/**
	 * execute request/message and index a Promise as a response
	 *
	 * @param element
	 * @param callback
	 */
	request(element: IElement, callback?: (element: IElement) => void): IPromise;
}

export class ProtocolService implements IProtocolService {
	protected eventBus: IEventBus;

	protected handleList: IHashMap<(element: IElement) => void> = e3.hashMap<(element: IElement) => void>({
		'packet': this.handlePacket,
		'event': this.handleEvent,
	});

	public constructor(eventBus: IEventBus) {
		this.eventBus = eventBus;
	}

	public execute(element: IElement): any {
		return this.handleList.get(element.getType()).call(this, element);
	}

	public handlePacket(element: IElement): IElement {
		const packet: PacketElement = new PacketElement('client');
		packet.setReference(element);
		packet.reference(element);
		element.getElementList('elements').each(node => {
			const response: any = this.execute(node);
			if (response) {
				packet.element(response.setReference(node));
				packet.reference(node);
			}
		});
		return packet;
	}

	public handleEvent(element: IElement): void {
		this.eventBus.event(element);
	}
}

export class RequestService implements IRequestService {
	/**
	 * protocol endpoint url
	 */
	protected url: string;
	protected accept: string = 'application/json';
	protected target: string = 'application/json';

	public constructor(url: string) {
		this.url = url;
	}

	public setAccept(accept: string): IRequestService {
		this.accept = accept;
		return this;
	}

	/**
	 * set the content conversion target (node to ... json, xml, ...)
	 */
	public setTarget(target: string): IRequestService {
		this.target = target;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public request(element: IElement, callback?: (element: IElement) => void): IPromise {
		const ajax = e3.ajax(this.url);
		ajax.setAccept(this.accept)
			.getAjaxHandler()
			.error(xmlHttpRequest => e3.emit('request-service/error', {'request': xmlHttpRequest, 'element': element}))
			.timeout(xmlHttpRequest => e3.emit('request-service/timeout', {'request': xmlHttpRequest, 'element': element}))
			.fail(xmlHttpRequest => e3.emit('request-service/fail', {'request': xmlHttpRequest, 'element': element}))
			.success(xmlHttpRequest => {
				const packet: IElement = e3.elementFromJson(xmlHttpRequest.responseText);
				e3.emit('request-service/success', {'request': xmlHttpRequest, 'element': element, 'packet': packet});
				let response;
				callback && (response = packet.getReferenceBy(element.getId())) ? callback(response) : null;
				e3.job(packet).execute();
			});
		return ajax.execute(e3.convert<INode, string>(e3.ElementQueue().createPacket().addElement('elements', element), 'node', [this.target]));
	}
}

export class ElementConverter extends AbstractConverter {
	public constructor() {
		super();
		this.register(['node'], ['application/x-www-form-urlencoded+edde/protocol']);
	}

	public convert<S, T>(content: S, mime: string, target: string | any): IContent<T> {
		switch (target) {
			case 'application/x-www-form-urlencoded+edde/protocol':
				return new Content<T>(<any>e3.formEncode(e3.convert<INode, Object>(<any>content, 'node', ['object']).getContent()), 'application/x-www-form-urlencoded');
		}
		throw new Error('Cannot convert [' + mime + '] to [' + target + '] in element converter.');
	}
}
