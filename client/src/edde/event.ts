import {EventElement} from "./element";
import {e3} from "./e3";
import {IHashMapCollection} from "./collection";
import {IElement} from "./protocol";

/**
 * Simple and small implementation of an EventBus
 */
export interface IEventBus {
	/**
	 * register listener to the given event name
	 */
	listen(event: string | null, handler: (element: IElement) => void, weight: number, context?: Object, scope?: string): IEventBus;

	/**
	 * register all event methods in event bus
	 *
	 * @param instance
	 * @param scope
	 */
	register(instance: Object, scope?: string): IEventBus;

	/**
	 * remove all listeners/by scope
	 *
	 * @param {string} scope
	 */
	remove(scope?: string): IEventBus;

	/**
	 * send the given event
	 *
	 * @param {IElement} element
	 */
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
	protected listenerList: IHashMapCollection<IListener> = e3.hashMapCollection();

	/**
	 * @inheritDoc
	 */
	public listen(event: string | null, handler: (element: IElement) => void, weight: number = 100, context?: Object, scope?: string, cancelable?: boolean): IEventBus {
		event = event || '::proxy';
		this.listenerList.add(event, {
			'event': event,
			'handler': handler,
			'weight': weight,
			'context': context || null,
			'scope': scope || null,
			'cancelable': cancelable || true,
		});
		this.listenerList.sort(event, (alpha, beta): number => beta.weight - alpha.weight);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public register(instance: any, scope?: string): IEventBus {
		e3.$$(instance, (name, value: any[]) => name.indexOf('::ListenerList/', 0) !== -1 ? e3.$(value, listener => this.listen(listener.event, (listener.context || instance)[<any>listener.handler], listener.weight, listener.context || instance, listener.scope || scope, listener.cancelable)) : null);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public remove(scope?: string): IEventBus {
		scope ? this.listenerList.removeBy(listener => listener.scope !== scope) : this.listenerList.clear();
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public event(event: IElement): IEventBus {
		this.listenerList.each(event.getAttribute('event') || '', listener => listener.cancelable && event.getMeta('cancel', false) === true ? null : listener.handler.call(listener.context || listener.handler, event));
		this.listenerList.each('::proxy', listener => listener.cancelable && event.getMeta('cancel', false) === true ? null : listener.handler.call(listener.context || listener.handler, event));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public emit(event: string, data: Object = {}): IEventBus {
		this.event(new EventElement(event).data(data));
		return this;
	}
}
