import {e3} from "./e3";
import {ICollection, IHashMap, ILoop} from "./collection";
import {AbstractConverter, Content, IContent} from "./converter";

export interface IAbstractNode {
	/**
	 * set/unset current parent
	 *
	 * @param parent
	 */
	setParent(parent: IAbstractNode): IAbstractNode;

	/**
	 * detach this node from it's current parent
	 */
	detach(): IAbstractNode;

	/**
	 * index the parent or null if the node is root
	 */
	getParent(): IAbstractNode | null;

	/**
	 * is this node a root?
	 */
	isRoot(): boolean;

	/**
	 * is this node child (basically hasParent()?)
	 */
	isChild(): boolean;

	/**
	 * add a child node
	 *
	 * @param node
	 */
	addNode(node: IAbstractNode): IAbstractNode;

	addNodeList(nodeList: IAbstractNode[]): IAbstractNode;

	/**
	 * return current list of nodes
	 */
	getNodeList(): ICollection<IAbstractNode>;

	/**
	 * return number of nodes
	 */
	getNodeCount(): number;

	/**
	 * foreach all nodes in this node
	 *
	 * @param callback
	 */
	each<U extends ILoop<IAbstractNode>>(callback: (this: U, node: IAbstractNode) => any | boolean): U;
}

/**
 * General node tree support (this node is not node from DOM tree). It's
 * general purpose node similar to XML node.
 */
export interface INode extends IAbstractNode {
	/**
	 * set node name
	 *
	 * @param name
	 */
	setName(name: string): INode;

	/**
	 * index the name of a node
	 */
	getName(): string | null;

	/**
	 * set node value
	 *
	 * @param value
	 */
	setValue(value: any): INode;

	/**
	 * index value of a node
	 */
	getValue(): any;

	/**
	 * return attribute list of a node
	 */
	getAttributeList(): IHashMap<any>;

	/**
	 * set value of the given attribute
	 *
	 * @param name
	 * @param value
	 */
	setAttribute(name: string, value: any): INode;

	/**
	 * index attribute value with eventual default value
	 *
	 * @param name
	 * @param value
	 */
	getAttribute(name: string, value?: any): any;

	/**
	 * return metadata of a node
	 */
	getMetaList(): IHashMap<any>;

	/**
	 * set meta data
	 *
	 * @param name
	 * @param value
	 */
	setMeta(name: string, value: any): INode;

	/**
	 * retrieve meta data
	 *
	 * @param name
	 * @param value
	 */
	getMeta(name: string, value?: any): any;
}

export abstract class AbstractNode implements IAbstractNode {
	protected parent: IAbstractNode | null;
	protected nodeList: ICollection<IAbstractNode> = e3.collection<IAbstractNode>();

	public constructor(parent: IAbstractNode | null) {
		this.parent = parent;
	}

	/**
	 * @inheritDoc
	 */
	public setParent(parent: IAbstractNode): IAbstractNode {
		this.parent = parent;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public detach(): IAbstractNode {
		this.parent = null;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getParent(): IAbstractNode | null {
		return this.parent;
	}

	/**
	 * @inheritDoc
	 */
	public isRoot(): boolean {
		return this.parent === null;
	}

	/**
	 * @inheritDoc
	 */
	public isChild(): boolean {
		return this.parent !== null;
	}

	/**
	 * @inheritDoc
	 */
	public addNode(node: IAbstractNode): IAbstractNode {
		this.nodeList.add(node);
		node.setParent(this);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public addNodeList(nodeList: IAbstractNode[]): IAbstractNode {
		e3.$(nodeList, node => this.addNode(node));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getNodeList(): ICollection<IAbstractNode> {
		return this.nodeList;
	}

	/**
	 * @inheritDoc
	 */
	public getNodeCount(): number {
		return this.nodeList.getCount();
	}

	/**
	 * @inheritDoc
	 */
	public each<U extends ILoop<IAbstractNode>>(callback: (this: U, node: IAbstractNode) => any | boolean): U {
		return this.nodeList.each<U>(function (node) {
			return callback.call(this, node);
		});
	}
}

export class Node extends AbstractNode implements INode {
	protected name: string | null;
	protected value: any;
	protected attributeList: IHashMap<any> = e3.hashMap<any>();
	protected metaList: IHashMap<any> = e3.hashMap<any>();

	public constructor(name: string | null = null, value: any = null) {
		super(null);
		this.name = name;
		this.value = value;
	}

	/**
	 * @inheritDoc
	 */
	public setName(name: string): INode {
		this.name = name;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getName(): string | null {
		return this.name;
	}

	/**
	 * @inheritDoc
	 */
	public setValue(value: any): INode {
		this.value = value;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getValue(): any {
		return this.value;
	}

	/**
	 * @inheritDoc
	 */
	public getAttributeList(): IHashMap<any> {
		return this.attributeList;
	}

	/**
	 * @inheritDoc
	 */
	public setAttribute(name: string, value: any): INode {
		this.attributeList.set(name, value);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getAttribute(name: string, value?: any): any {
		return this.attributeList.get(name, value);
	}

	/**
	 * @inheritDoc
	 */
	public getMetaList(): IHashMap<any> {
		return this.metaList;
	}

	/**
	 * @inheritDoc
	 */
	public setMeta(name: string, value: any): INode {
		this.metaList.set(name, value);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getMeta(name: string, value?: any): any {
		return this.getMetaList().get(name, value);
	}
}

export class NodeConverter extends AbstractConverter {
	public constructor() {
		super();
		this.register(['object'], ['node']);
		this.register(['node'], ['object']);
		this.register(['application/json'], ['node']);
		this.register(['node'], ['application/json']);
	}

	public convert<S, T>(content: S, mime: string, target: string | null): IContent<T> {
		switch (target) {
			case 'node':
				switch (mime) {
					case 'application/json':
						return new Content<T>(<any>e3.nodeFromJson(<any>content), 'node');
					case 'object':
						return new Content<T>(<any>e3.toNode(content), 'node');
				}
				break;
			case 'application/json':
				return new Content<T>(<any>e3.toJsonNode(<any>content), 'application/json');
			case 'object':
				return new Content<T>(<any>e3.fromNode(<any>content), 'object');
		}
		throw new Error('Cannot convert [' + mime + '] to [' + target + '] in node converter.');
	}
}
