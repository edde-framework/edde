import {e3} from "./e3";
import {Listen} from "./decorator";
import {IHtmlElement} from "./dom";

export interface IClientClass {
	attach(htmlElement: IHtmlElement): IHtmlElement;

	attachHtml(html: string): IHtmlElement;

	getElement(): IHtmlElement;
}

export abstract class AbstractClientClass implements IClientClass {
	/**
	 * layer is bound to some dom node
	 */
	protected element: IHtmlElement;

	public attach(htmlElement: IHtmlElement): IHtmlElement {
		const dom = (this.element = htmlElement).getElement();
		e3.$$(this, (name: string, value: any[]) => name.indexOf('::NativeListenerList/', 0) !== -1 ? e3.$(value, (listener: { event: string, handler: string }) => dom.addEventListener(listener.event, event => (<any>this)[listener.handler].call(this, event), false)) : null);
		return this.element;
	}

	public attachHtml(html: string): IHtmlElement {
		return this.attach(e3.html(html));
	}

	public getElement(): IHtmlElement {
		return this.element;
	}
}

export abstract class AbstractButton extends AbstractClientClass {
	@Listen.ToNative('click')
	public onClick(event?: any): void {
	}
}
