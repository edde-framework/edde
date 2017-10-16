import {e3} from "./e3";
import {IHashMap, IHashMapCollection} from "./collection";

export interface IPromise {
	/**
	 * execute success handlers or register a new handler
	 *
	 * @param {(value?: any) => void} callback
	 */
	success(callback: (value?: any) => void): IPromise;

	onSuccess(value?: any): IPromise;

	/**
	 * execute fail handlers or register a new handler
	 *
	 * @param {(value?: any) => void} callback
	 */
	fail(callback: (value?: any) => void): IPromise;

	onFail(value?: any): IPromise;

	/**
	 * executed always, regardless of an error state or register a new handler
	 *
	 * @param {(value?: any) => void} callback
	 */
	always(callback: (value?: any) => void): IPromise;

	onAlways(value?: any): IPromise;
}


export class AbstractPromise implements IPromise {
	protected promiseList: IHashMapCollection<(value?: any) => void> = e3.hashMapCollection<(value?: any) => void>();
	protected resultList: IHashMap<any> = e3.hashMap<any>();

	protected register(name: string, callback: (value?: any) => void): IPromise {
		if (this.resultList.has(name)) {
			callback(this.resultList.get(name));
			return this;
		}
		this.promiseList.add(name, <(value?: any) => void>callback);
		return this;
	}

	protected execute(name: string, value?: any): IPromise {
		this.resultList.set(name, value);
		this.promiseList.each(name, (callback) => callback(value));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public success(callback: (value?: any) => void): IPromise {
		return this.register('success', callback);
	}

	/**
	 * @inheritDoc
	 */
	public onSuccess(value?: any): IPromise {
		return this.execute('success', value);
	}

	/**
	 * @inheritDoc
	 */
	public fail(callback: (value?: any) => void): IPromise {
		return this.register('fail', callback);
	}

	/**
	 * @inheritDoc
	 */
	public onFail(value?: any): IPromise {
		return this.execute('fail', value);
	}

	/**
	 * @inheritDoc
	 */
	public always(callback: (value?: any) => void): IPromise {
		return this.register('always', callback);
	}

	/**
	 * @inheritDoc
	 */
	public onAlways(value?: any): IPromise {
		return this.execute('always', value);
	}
}
