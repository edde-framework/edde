import {IHtmlElement} from "./dom";
import {IElement} from "./protocol";
import {e3} from "./e3";
import {Listen} from "./decorator";
import {ICollection} from "./collection";

export interface IControl {
	/**
	 * use the given control; this should build the control and attach the given control to current one;
	 * when the parent control is mounted, all dependant controls should be mounted too
	 *
	 * @param {IControl} control
	 * @returns {IHtmlElement}
	 */
	use(control: IControl): IHtmlElement;

	/**
	 * mount a control to specified html element; this should happen just
	 * once when an element is renedered/created
	 *
	 * @param {IHtmlElement} element
	 * @returns {IHtmlElement}
	 */
	mount(element: IHtmlElement): IHtmlElement;

	/**
	 * attach this control to the given element; this should trigger render if control wasn't rendered yet
	 */
	attachTo(root: IHtmlElement): IControl;

	/**
	 * create an html element for this control (just once event per multiple calls)
	 *
	 * @returns {IHtmlElement}
	 */
	create(): IHtmlElement;

	/**
	 * create html element of this control; this method should build this control just once
	 */
	build(): IHtmlElement;

	/**
	 * render the given control
	 */
	render(): IHtmlElement;

	/**
	 * return control's element or null if it was not rendered yet
	 */
	getElement(): IHtmlElement | null;

	/**
	 * this method is responsible of updating this component to the "current" state
	 */
	update(element: IElement): IControl;

	/**
	 * if the control is listening, it will be registered as a listener when attached
	 */
	isListening(): boolean;
}

export interface IControlFactory {
}

export abstract class AbstractControl implements IControl {
	protected name: string;
	protected element: IHtmlElement | null;
	protected isMounted: boolean = false;
	protected controlList: ICollection<IControl> = e3.collection();

	public constructor(name: string) {
		this.name = name;
		this.element = null;
	}

	/**
	 * @inheritDoc
	 */
	public use(control: IControl): IHtmlElement {
		this.controlList.add(control);
		return control.create();
	}

	/**
	 * @inheritDoc
	 */
	public mount(element: IHtmlElement): IHtmlElement {
		this.isMounted = true;
		const dom = (this.element = element).getElement();
		e3.$$(this, (name: string, value: any[]) => name.indexOf('::NativeListenerList/', 0) !== -1 ? e3.$(value, (listener: { event: string, handler: string }) => dom.addEventListener(listener.event, event => (<any>this)[listener.handler].call(this, event), false)) : null);
		if (this.isListening()) {
			e3.listener(this);
		}
		return this.element;
	}

	/**
	 * @inheritDoc
	 */
	public attachTo(root: IHtmlElement): IControl {
		root.attach(this.render());
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public render(): IHtmlElement {
		if (this.element && this.isMounted) {
			return this.element;
		}
		/**
		 * create method already assigne element to this.element
		 * @type {IHtmlElement}
		 */
		const element = this.create();
		this.mount(element);
		this.controlList.each(control => control.render());
		return element;
	}

	/**
	 * @inheritDoc
	 */
	public create(): IHtmlElement {
		return this.element ? this.element : this.element = this.build();
	}

	/**
	 * @inheritDoc
	 */
	public getElement(): IHtmlElement | any {
		return this.element;
	}

	/**
	 * @inheritDoc
	 */
	public update(element: IElement): IControl {
		return this;
	}

	public isListening(): boolean {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public abstract build(): IHtmlElement;
}

/**
 * This class is listening for control creation requests.
 */
export class ControlFactory implements IControlFactory {
	@Listen.To('control/create', 0)
	public eventControlCreate(element: IElement) {
		element.setMeta('instance', e3.create<IControl>(element.getMeta('control')).attachTo(element.getMeta('root')));
	}
}
