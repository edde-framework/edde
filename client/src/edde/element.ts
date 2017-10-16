import {INode, Node} from "./node";
import {Collection, ICollection} from "./collection";
import {e3} from "./e3";
import {IElement, IElementQueue} from "./protocol";

export class ProtocolElement extends Node implements IElement {
	public constructor(type?: string, id?: string) {
		super(type || null);
		id ? this.setAttribute('id', id) : this.getId();
	}

	/**
	 * @inheritDoc
	 */
	public getType(): string {
		const name = this.getName();
		if (name) {
			return name;
		}
		throw "There is an element [" + e3.getInstanceName(this) + "] without type! This is quite strange, isn't it?";
	}

	/**
	 * @inheritDoc
	 */
	public isType(type: string): boolean {
		return this.getType() === type;
	}

	/**
	 * @inheritDoc
	 */
	public getId(): string {
		let id = this.getAttribute('id', false);
		if (id === false) {
			this.setAttribute('id', id = e3.guid());
		}
		return id;
	}

	/**
	 * @inheritDoc
	 */
	public async(async: boolean): IElement {
		this.setAttribute('async', async);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public isAsync(): boolean {
		return this.getAttribute('async', false);
	}

	/**
	 * @inheritDoc
	 */
	public setReference(element: IElement): IElement {
		this.setAttribute('reference', element.getId());
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public hasReference(): boolean {
		return this.getAttribute('reference', false) !== false;
	}

	/**
	 * @inheritDoc
	 */
	public getReference(): string | null {
		return this.getAttribute('reference', null);
	}

	/**
	 * @inheritDoc
	 */
	public data(data: {}): IElement {
		this.metaList.put(data);
		return this;
	}

	/**
	 * @inheritdoc
	 */
	public addElement(name: string, element: IElement): IElement {
		let node: INode | null = null;
		if ((node = this.getElementNode(name)) === null || node.getName() !== name) {
			this.addNode(node = new ProtocolElement(name));
		}
		node.addNode(element);
		return this;
	}

	public addElementCollection(name: string, collection: ICollection<IElement>): IElement {
		collection.each(element => this.addElement(name, element));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getElementNode(name: string): INode | null {
		let node: INode | null = null;
		this.nodeList.each((current: INode) => {
			if (current.getName() === name) {
				node = current;
				return false;
			}
		});
		return node;
	}

	/**
	 * @inheritDoc
	 */
	public getElementList(name: string): ICollection<IElement> {
		const node = this.getElementNode(name);
		return node ? <ICollection<IElement>>node.getNodeList() : e3.collection<IElement>();
	}

	/**
	 * @inheritDoc
	 */
	public getReferenceBy(id: string): IElement | null {
		return this.getReferenceList(id).first();
	}

	/**
	 * @inheritDoc
	 */
	public getReferenceList(id: string): ICollection<IElement> {
		const collection = e3.collection<IElement>();
		if (this.hasReference() && this.getReference() === id) {
			collection.add(this);
		}
		e3.tree(this, (element: IElement) => {
			if (element.hasReference() && element.getReference() === id) {
				collection.add(element);
			}
		});
		return collection;
	}
}

export class EventElement extends ProtocolElement {
	public constructor(event: string) {
		super('event');
		this.setAttribute('event', event);
	}
}


export class PacketElement extends ProtocolElement {
	public constructor(origin: string, id?: string) {
		super('packet', id);
		this.setAttribute('version', '1.2');
		this.setAttribute('origin', origin);
	}

	public element(element: IElement): PacketElement {
		this.addElement('elements', element);
		return this;
	}

	public reference(element: IElement): PacketElement {
		this.addElement('references', element);
		return this;
	}
}

export class ErrorElement extends ProtocolElement {
	public constructor(code: number, message: string) {
		super('error');
		this.setAttribute('code', code);
		this.setAttribute('message', message);
	}

	/**
	 * @inheritDoc
	 */
	public setException(exception: string): ErrorElement {
		this.setAttribute('exception', exception);
		return this;
	}
}

export class RequestElement extends ProtocolElement {
	public constructor(request: string) {
		super('request');
		this.setAttribute('request', request);
	}
}

export class MessageElement extends ProtocolElement {
	public constructor(request: string) {
		super('message');
		this.setAttribute('request', request);
	}
}

export class ResponseElement extends ProtocolElement {
	public constructor() {
		super('response');
	}
}

export class ElementQueue extends Collection<IElement> implements IElementQueue {
	/**
	 * @inheritDoc
	 */
	public queue(element: IElement): IElementQueue {
		this.add(element);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public createPacket(): IElement {
		const packet = new PacketElement('client').addElementCollection('elements', this);
		this.clear();
		return packet;
	}
}
