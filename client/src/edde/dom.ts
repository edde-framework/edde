import {e3} from "./e3";
import {ICollection, ILoop} from "./collection";

/**
 * I don't like strange names, but in this case the idea is to
 * collect methods same for single element and for element collection (addClass, event, ...).
 */
export interface IAbstractHtmlElement<T> {
	/**
	 * attach event listener to DOM node
	 */
	event(name: string, callback: (event: any) => void): T;

	/**
	 * replace current inner html of the element
	 *
	 * @param {string} html
	 */
	html(html: string): T;

	/**
	 * set attribute of the element
	 */
	attr(name: string, value: string): T;

	/**
	 * set list of attribute of the element
	 */
	attrList(attrList: Object): T;

	/**
	 * toggle class by state or by the given flag
	 */
	toggleClass(name: string | string[], toggle?: boolean): T;

	/**
	 * toggle all classes or toggle them against the flag
	 */
	toggleClassList(nameList: string[], toggle?: boolean): T;

	/**
	 * add css class
	 */
	addClass(name: string | string[]): T;

	/**
	 * add given array of classes to a node
	 */
	addClassList(nameList: string[]): T;

	/**
	 * has this node given css class?
	 */
	hasClass(name: string): boolean;

	/**
	 * has this node all given class names?
	 */
	hasClassList(nameList: string[]): boolean;

	/**
	 * remove given css class
	 */
	removeClass(name: string): T;

	/**
	 * remove list of classes
	 */
	removeClassList(nameList: string[]): T;

	/**
	 * remove the given element
	 */
	remove(): T;
}

export interface IHtmlElement extends IAbstractHtmlElement<IHtmlElement> {
	/**
	 * return current html element
	 */
	getElement(): HTMLElement;

	/**
	 * return node id if provided
	 *
	 * @returns {string}
	 */
	getId(): string;

	/**
	 * return tag name
	 *
	 * @returns {string}
	 */
	getName(): string;

	/**
	 * return data attribute
	 */
	data(name: string, value?: any): any;

	/**
	 * set text content of an element
	 *
	 * @param {string} text
	 * @returns {IHtmlElement}
	 */
	text(text: string): IHtmlElement;

	/**
	 * return inner html
	 */
	getInnerHtml(): string;

	/**
	 * return whole (outer) html, including "this" element
	 */
	getOuterHtml(): string;

	/**
	 * clear contents of the node (drop children, text, ...)
	 */
	clear(): IHtmlElement;

	getOffsetHeight(): number;

	getOffsetWidth(): number;

	getClientHeight(): number;

	getClientWidth(): number;

	/**
	 * return list of parents of the given node
	 *
	 * @param root
	 */
	getParentList(root?: HTMLElement): ICollection<IHtmlElement>;

	collection(selector: string): IHtmlElementCollection;

	attach(child: IHtmlElement): IHtmlElement;

	attachHtml(html: string): IHtmlElement;

	attachList(elementList: (IHtmlElement | null)[]): IHtmlElement;

	attachTo(parent: IHtmlElement): IHtmlElement;

	position(top: string, left: string): IHtmlElement;
}

export interface IHtmlElementCollection extends IAbstractHtmlElement<IHtmlElementCollection> {
	/**
	 * run callback over collection
	 *
	 * @param callback
	 */
	each<U extends ILoop<HTMLElement>>(callback: (this: U, node: IHtmlElement) => any | boolean): U;

	/**
	 * index node cound
	 */
	getCount(): number;

	/**
	 * index node by index
	 *
	 * @param {number} index
	 */
	index(index: number): IHtmlElement | null;
}

export interface ISelector {
	each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: <U>(htmlElement: IHtmlElement) => any | boolean): U;

	/**
	 * index number of elements of this selector
	 */
	getCount(root: HTMLElement): number;

	/**
	 * retrieve node by an index
	 *
	 * @param root
	 * @param {number} index
	 */
	index(root: HTMLElement, index: number): IHtmlElement | null;
}

/**
 * There is not so much free names, but this class encapsulates real DOM element. This is NOT an Element.
 */
export class HtmlElement implements IHtmlElement {
	/**
	 * DOM element this node is bound to
	 */
	protected element: HTMLElement;

	public constructor(element: HTMLElement) {
		this.element = element;
	}

	/**
	 * @inheritDoc
	 */
	public getElement(): HTMLElement {
		return this.element;
	}

	/**
	 * @inheritDoc
	 */
	public getId(): string {
		return this.element.getAttribute('id') || '';
	}

	/**
	 * @inheritDoc
	 */
	public getName(): string {
		return this.element.nodeName.toLowerCase();
	}

	/**
	 * @inheritDoc
	 */
	public event(name: string, callback: (event: any) => void): IHtmlElement {
		this.element.addEventListener(name, (event) => callback.call(this, event), false);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public data(name: string, value?: any): any {
		return this.element.getAttribute('data-' + name) || value;
	}

	/**
	 * @inheritDoc
	 */
	public toggleClass(name: string, toggle?: boolean): IHtmlElement {
		let hasClass = this.hasClass(name);
		if (toggle === true && hasClass === false) {
			this.addClass(name);
		} else if (toggle === true && hasClass) {
		} else if (toggle === false && hasClass === false) {
		} else if (toggle === false && hasClass) {
			this.removeClass(name);
		} else if (hasClass) {
			this.removeClass(name);
		} else if (hasClass === false) {
			this.addClass(name);
		}
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public toggleClassList(nameList: string[], toggle?: boolean): IHtmlElement {
		e3.$(nameList, (item: string) => this.toggleClass(item, toggle));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public addClass(name: string): IHtmlElement {
		if (this.hasClass(name)) {
			return this;
		}
		this.element.className += ' ' + name;
		this.element.className = this.className(this.element.className);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public addClassList(nameList: string[]): IHtmlElement {
		e3.$(nameList, (item: string) => this.addClass(item));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public hasClass(name: string): boolean {
		return this.element.className !== undefined && (' ' + this.element.className + ' ').indexOf(' ' + name + ' ') !== -1;
	}

	/**
	 * @inheritDoc
	 */
	public hasClassList(nameList: string[]): boolean {
		let hasClass = false;
		e3.$(nameList, item => {
			hasClass = true;
			if (this.hasClass(item) === false) {
				hasClass = false;
				return false;
			}
		});
		return hasClass;
	}

	/**
	 * @inheritDoc
	 */
	public removeClass(name: string): IHtmlElement {
		this.element.className = this.className(this.element.className.replace(name, ''));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public removeClassList(nameList: string[]): IHtmlElement {
		e3.$(nameList, item => this.removeClass(item));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public html(html: string): IHtmlElement {
		this.element.innerHTML = html;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public text(text: string): IHtmlElement {
		this.clear();
		this.element.appendChild(e3.Text(text));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public attr(name: string, value: string): IHtmlElement {
		this.element.setAttribute(name, value);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public attrList(attrList: Object): IHtmlElement {
		e3.$$(attrList, (name, value) => this.attr(name, <string>value));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getInnerHtml(): string {
		return this.element.innerHTML;
	}

	/**
	 * @inheritDoc
	 */
	public getOuterHtml(): string {
		return this.element.outerHTML;
	}

	/**
	 * @inheritDoc
	 */
	public clear(): IHtmlElement {
		this.html('');
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getOffsetHeight(): number {
		return this.element.offsetHeight;
	}

	/**
	 * @inheritDoc
	 */
	public getOffsetWidth(): number {
		return this.element.offsetWidth;
	}

	/**
	 * @inheritDoc
	 */
	public getClientHeight(): number {
		return this.element.clientHeight;
	}

	/**
	 * @inheritDoc
	 */
	public getClientWidth(): number {
		return this.element.clientWidth;
	}

	/**
	 * @inheritDoc
	 */
	public getParentList(root?: HTMLElement): ICollection<IHtmlElement> {
		let elementList: IHtmlElement[] = [this];
		let parent: HTMLElement | null = <HTMLElement>this.element.parentNode;
		while (parent) {
			if (parent === root) {
				break;
			}
			elementList[elementList.length] = new HtmlElement(<HTMLElement>parent);
			parent = <HTMLElement>parent.parentNode;
		}
		return e3.collection(elementList);
	}

	/**
	 * @inheritDoc
	 */
	public collection(selector: string): IHtmlElementCollection {
		return e3.selector(selector, this.element);
	}

	/**
	 * @inheritDoc
	 */
	public attach(child: IHtmlElement): IHtmlElement {
		this.element.appendChild(child.getElement());
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public attachHtml(html: string): IHtmlElement {
		return this.attach(e3.html(html));
	}

	/**
	 * @inheritDoc
	 */
	public attachList(elementList: (IHtmlElement | null)[]): IHtmlElement {
		e3.$(elementList, element => element ? this.attach(element) : null);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public attachTo(parent: IHtmlElement): IHtmlElement {
		parent.attach(this);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public position(top: string, left: string): IHtmlElement {
		this.element.style.top = top;
		this.element.style.left = left;
		return this;
	}

	public remove(): IHtmlElement {
		this.element.parentNode ? this.element.parentNode.removeChild(this.element) : null;
		(<any>this.element) = null;
		return this;
	}

	/**
	 * cleanup and rejoin the given value with space
	 *
	 * @param name
	 * @returns {string}
	 */
	public className(name: string): string {
		return (name.match(/[^\x20\t\r\n\f]+/g) || []).join(' ');
	}
}

export class HtmlElementCollection implements IHtmlElementCollection {
	/**
	 * root element of the collection
	 */
	protected root: HTMLElement;
	/**
	 * selector of this collection
	 */
	protected selector: ISelector;

	public constructor(root: HTMLElement, selector: string) {
		this.root = root;
		this.selector = e3.Selector(selector);
	}

	/**
	 * @inheritDoc
	 */
	public event(name: string, callback: (event: any) => void): IHtmlElementCollection {
		this.each(element => element.event(name, callback));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public addClass(name: string): IHtmlElementCollection {
		this.each(element => element.addClass(name));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public addClassList(nameList: string[]): IHtmlElementCollection {
		this.each(element => element.addClassList(nameList));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public hasClass(name: string): boolean {
		let hasClass = false;
		this.each(element => (hasClass = element.hasClass(name)) !== false);
		return hasClass;
	}

	/**
	 * @inheritDoc
	 */
	public hasClassList(nameList: string[]): boolean {
		let hasClass = false;
		this.each(element => (hasClass = element.hasClassList(nameList)) !== false);
		return hasClass;

	}

	/**
	 * @inheritDoc
	 */
	public removeClass(name: string): IHtmlElementCollection {
		this.each(element => element.removeClass(name));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public removeClassList(nameList: string[]): IHtmlElementCollection {
		this.each(element => element.removeClassList(nameList));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public toggleClass(name: string, toggle?: boolean): IHtmlElementCollection {
		this.each(element => element.toggleClass(name, toggle));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public toggleClassList(nameList: string[], toggle?: boolean): IHtmlElementCollection {
		this.each(element => element.toggleClassList(nameList, toggle));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public attr(name: string, value: string): IHtmlElementCollection {
		this.each(element => element.attr(name, value));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public attrList(attrList: Object): IHtmlElementCollection {
		this.each(element => element.attrList(attrList));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public html(html: string): IHtmlElementCollection {
		this.each(element => element.html(html));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public each<U extends ILoop<HTMLElement>>(callback: (this: U, node: IHtmlElement) => any | boolean): U {
		return this.selector.each(this.root, callback);
	}

	/**
	 * @inheritDoc
	 */
	public getCount(): number {
		return this.selector.getCount(this.root);
	}

	/**
	 * @inheritDoc
	 */
	public index(index: number): IHtmlElement | null {
		return this.selector.index(this.root, index);
	}

	/**
	 * @inheritDoc
	 */
	public remove(): IHtmlElementCollection {
		this.each(element => element.remove());
		return this;
	}
}

export abstract class AbstractSelector implements ISelector {
	protected selector: string;

	public constructor(selector: string) {
		this.selector = selector;
	}

	/**
	 * @inheritDoc
	 */
	public getCount(root: HTMLElement): number {
		let count = 0;
		this.each(root, () => count++);
		return count;
	}

	/**
	 * @inheritDoc
	 */
	public index(root: HTMLElement, index: number): IHtmlElement | null {
		let count = 0;
		return this.each(root, function (htmlElement) {
			this.item = htmlElement;
			return count++ !== index;
		}).item;
	}

	public abstract each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: (this: U, htmlElement: IHtmlElement) => any | boolean): U;
}

export class SimpleClassSelector extends AbstractSelector {
	public each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: <U>(htmlElement: IHtmlElement) => (any | boolean)): U {
		const self = this;
		/**
		 * function instead of fat arrow is used because the loop has special context which "cannot" be changed
		 */
		return e3.$(<any>root.getElementsByTagName('*'), function (element: HTMLElement) {
			const htmlElement = new HtmlElement(element);
			if (htmlElement.hasClass(self.selector)) {
				return callback.call(this, htmlElement);
			}
		});
	}
}

export class SimpleIdSelector extends AbstractSelector {
	public each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: (htmlElement: IHtmlElement) => (any | boolean)): U {
		const self = this;
		/**
		 * function instead of fat arrow is used because the loop has special context which "cannot" be changed
		 */
		return e3.$(<any>root.getElementsByTagName('*'), function (element: HTMLElement) {
			const htmlElement = new HtmlElement(element);
			if (htmlElement.getId() === self.selector) {
				return callback.call(this, htmlElement);
			}
		});
	}
}

export class SimpleSelector extends AbstractSelector {
	/**
	 * string representation of the selector
	 */
	protected selectorList: string[][] | string;

	public constructor(selector: string) {
		super(selector);
		this.selectorList = [];
		/**
		 * initial break by spaces (this works in really simple cases, but this selector engine is... simple)
		 * @type {string[]}
		 */
		const selectorList = selector.split(/\s+?/);
		/**
		 * initial "parse" of parts of a selector
		 */
		for (let filter of selectorList) {
			if (filter === '') {
				continue;
			}
			/**
			 * break selector down by "known" styles - an id, an element name and a class
			 * @type {RegExpMatchArray}
			 */
			const match = filter.match(/#[a-zA-Z0-9_-]+|\.[a-zA-Z0-9_-]+|[a-zA-Z0-9_-]+/g);
			match ? this.selectorList[this.selectorList.length] = match : null;
		}
		if (this.selectorList.length !== selectorList.length) {
			throw new Error('Invalid selector [' + selector + ']');
		}
	}

	/**
	 * @inheritDoc
	 */
	public each<U extends ILoop<HTMLElement>>(root: HTMLElement, callback: (this: U, htmlElement: IHtmlElement) => any | boolean): U {
		const self = this;
		/**
		 * function instead of fat arrow is used because the loop has special context which "cannot" be changed
		 */
		return e3.$(<any>root.getElementsByTagName('*'), function (element: HTMLElement) {
			const selectorListLength = self.selectorList.length;
			/**
			 * matching is done by looking a way up to parent (root)
			 */
			const nodeList = e3.el(element).getParentList(root).reverse().toArray();
			/**
			 * optimization by depth; length of one is special case which could match "everything"
			 */
			if (selectorListLength > 1 && nodeList.length < selectorListLength) {
				return;
			}
			let filterId = 0;
			let count = 0;
			let matched;
			/**
			 * run through all parent elements
			 */
			// nodeList.each(htmlElement => {
			for (let htmlElement of nodeList) {
				matched = false;
				let match = true;
				/**
				 * go through all selector parts (selector contains an array of strings!)
				 */
				for (let id of <string[]>self.selectorList[filterId]) {
					/**
					 * some shitty browsers do not support index access, so this should work
					 * @type {string}
					 */
					const first = id.charAt(0);
					if (first === '#') {
						/**
						 * an id match
						 */
						match = match && htmlElement.getId() === id.substr(1);
					} else if (first === '.') {
						/**
						 * a class match
						 */
						match = match && htmlElement.hasClass(id.substr(1));
					} else {
						/**
						 * an element name match
						 */
						match = match && htmlElement.getName() === id;
					}
					/**
					 * no match, break the loop
					 */
					if (match === false) {
						break;
					}
				}
				/**
				 * if there is a match, it's possible to choose next part of a selector list (div.foo .bar => div.foo, then .bar)
				 */
				if (match) {
					/**
					 * this trick keeps last selector active
					 */
					filterId = Math.min(++filterId, selectorListLength - 1);
					count++;
					matched = true;
				}
			}
			// });
			/**
			 * so if there is a match, execute callback, yapee!
			 */
			if (matched && count >= selectorListLength) {
				return callback.call(this, nodeList[nodeList.length - 1]);
			}
		});
	}
}
