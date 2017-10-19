import {IHtmlElement} from "./dom";
import {IElement} from "./protocol";
import {e3} from "./e3";
import {Listen} from "./decorator";
import {ICollection, IHashMap} from "./collection";

export interface IControl {
	/**
	 * use the given control; this should build the control and attach the given control to current one;
	 * when the parent control is mounted, all dependant controls should be mounted too
	 */
	use(control: IControl): IHtmlElement;

	/**
	 * attach this control to the given element; this should trigger render if control wasn't rendered yet
	 */
	attachTo(root: IHtmlElement): IControl;

	/**
	 * create an html element for this control (just once event per multiple calls)
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

	/**
	 * show this control
	 */
	show(): IControl;

	/**
	 * hide this control
	 */
	hide(): IControl;
}

export interface IControlFactory {
}

/**
 * View is the whole one page with all contents; this implementation
 * is responsible for managing views and listening to events related to
 * showing/hiding view and theirs's eventual workflow.
 */
export interface IViewManager {
}

export abstract class AbstractControl implements IControl {
	protected name: string;
	protected element: IHtmlElement | null;
	protected rendered: boolean = false;
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
	public attachTo(root: IHtmlElement): IControl {
		root.attach(this.render());
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public render(): IHtmlElement {
		if (this.element && this.rendered) {
			return this.element;
		}
		const element = this.create();
		this.rendered = true;
		const dom = (this.element = element).getElement();
		e3.$$(this, (name: string, value: any[]) => name.indexOf('::NativeListenerList/', 0) !== -1 ? e3.$(value, (listener: { event: string, handler: string }) => dom.addEventListener(listener.event, event => (<any>this)[listener.handler].call(this, event), false)) : null);
		if (this.isListening()) {
			e3.listener(this);
		}
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

	/**
	 * @inheritDoc
	 */
	public isListening(): boolean {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public show(): IControl {
		(<IHtmlElement>this.element).removeClass('is-hidden');
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public hide(): IControl {
		(<IHtmlElement>this.element).addClass('is-hidden');
		return this;
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

export class ViewManager implements IViewManager {
	protected registerList: IHashMap<IElement> = e3.hashMap();
	protected viewList: IHashMap<IControl> = e3.hashMap();
	protected current: IControl;

	@Listen.To('view/register', 0)
	public eventViewRegister(element: IElement) {
		this.registerList.set(element.getMeta('view'), element);
	}

	@Listen.To('view/change', 0)
	public eventViewChange(element: IElement) {
		const view = element.getMeta('view');
		if (this.viewList.has(view) === false) {
			this.viewList.set(view, e3.emit('control/create', this.registerList.get(view).getMetaList().toObject()).getMeta('instance'));
		}
		if (this.current) {
			this.current.hide();
		}
		this.current = this.viewList.get(view);
		this.current.show();
	}
}