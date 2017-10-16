export interface ILoop<T> {
	count: number;
	loop: boolean;
	item: any | null;
	value: T | null;
	key: number | string | null;
}

export interface ICollection<T> {
	/**
	 * add a new item to the collection
	 *
	 * @param item
	 */
	add(item: T): ICollection<T>;

	/**
	 * run the given callback over all items in collection
	 *
	 * @param callback
	 */
	each<U extends ILoop<T>>(callback: (this: U, value: T, index: number) => any | boolean): U;

	/**
	 * run callback only over a part of the collection
	 */
	subEach<U extends ILoop<T>>(callback: (this: U, value: T, index: number) => any | boolean, start?: number, length?: number): U;

	subCollection(start?: number, length?: number): ICollection<T>;

	/**
	 * reverse the internal collection
	 */
	reverse(): ICollection<T>;

	/**
	 * index collection as an array
	 */
	toArray(): Array<T>;

	/**
	 * return number of items (basically .length)
	 */
	getCount(): number;

	/**
	 * retrieve item based on an index
	 *
	 * @param index
	 */
	index(index: number): T | null;

	/**
	 * index first item
	 */
	first(): T | null;

	/**
	 * index last item
	 */
	last(): T | null;

	/**
	 * is the collection empty?
	 */
	isEmpty(): boolean;

	/**
	 * clear current collection
	 */
	clear(): ICollection<T>;

	/**
	 * remove items by the given callback
	 *
	 * @param {(item: T) => boolean} callback
	 * @param {string} name
	 */
	removeBy(callback: (item: T) => boolean, name?: string): ICollection<T>;

	/**
	 * copy the given collection to this
	 */
	copy(copy: ICollection<T>): ICollection<T>;

	/**
	 * replace current collection by the given one
	 */
	replace(replace: ICollection<T>): ICollection<T>;

	/**
	 * sort internal array by the given sort
	 *
	 * @param {(alpha: any, beta: any) => number} sort
	 */
	sort(sort?: (alpha: any, beta: any) => number): ICollection<T>;
}

export interface IHashMap<T> {
	/**
	 * add a new element to array of the given name
	 *
	 * @param name
	 * @param item
	 */
	set(name: string | number, item: T): IHashMap<T>;

	/**
	 * put inside an object (rewrite all values)
	 *
	 * @param put
	 */
	put(put: {}): IHashMap<T>;

	/**
	 * is there the given key (even undefined/null)?
	 *
	 * @param name
	 */
	has(name: string): boolean;

	/**
	 * retrieve the given item
	 *
	 * @param name
	 * @param value
	 */
	get(name: string, value?: any): T;

	/**
	 * remove the given item
	 *
	 * @param {string} name
	 */
	remove(name: string): IHashMap<T>;

	/**
	 * is the collection empty?
	 */
	isEmpty(): boolean;

	/**
	 * convert a collection to an object
	 */
	toObject(): Object;

	/**
	 * call callback over all items of the given name
	 */
	each<U extends ILoop<T>>(callback: (this: U, key: string | number, value: T) => any | boolean): U;

	/**
	 * index the first item
	 */
	first(): T | null;

	/**
	 * index the last item
	 */
	last(): T | null;

	/**
	 * return current number of items
	 */
	getCount(): number;

	/**
	 * clear current hashmap
	 */
	clear(): IHashMap<T>;

	/**
	 * copy the given hash map into current one; current content is preserved
	 */
	copy(copy: IHashMap<T>): IHashMap<T>;

	/**
	 * replace current list by the copy of the given list
	 */
	replace(replace: IHashMap<T>): IHashMap<T>;

	/**
	 * fill the hashmap from the collection; key callback is used to index a key
	 */
	fromCollection(collection: ICollection<T>, key: (value: T) => string): IHashMap<T>;
}

/**
 * Hash map collection is storing items under a named item.
 */
export interface IHashMapCollection<T> {
	/**
	 * add a new element to array of the given name
	 *
	 * @param name
	 * @param item
	 */
	add(name: string, item: T): IHashMapCollection<T>;

	/**
	 * is the given collection present?
	 *
	 * @param {string} name
	 */
	has(name: string): boolean;

	/**
	 * @param name
	 * @param {(alpha: any, beta: any) => number} sort
	 */
	sort(name: string, sort?: (alpha: T, beta: T) => number): IHashMapCollection<T>;

	/**
	 * return array of items of the given name
	 *
	 * @param name
	 */
	toArray(name: string): Array<T>;

	/**
	 * index collection of the given name or return all root names (names of array)
	 *
	 * @param name
	 */
	toCollection(name: string): ICollection<T>;

	/**
	 * call callback over all items of the given name
	 *
	 * @param name
	 * @param callback
	 */
	each<U extends ILoop<T>>(name: string, callback: (this: U, value: T, index: string | number) => any | boolean): U;

	/**
	 * explicit method to traverse collections
	 *
	 * @param {(name: string, collection: ICollection<T>) => (void|boolean)} callback
	 */
	eachCollection<U extends ILoop<ICollection<T>>>(callback: (this: U, name: string, collection: ICollection<T>) => any | boolean): U;

	remove(name: string): IHashMapCollection<T>;

	/**
	 * remove items by the given callback; if name is not provided, all colletion are rotated
	 *
	 * @param {(item: T) => boolean} callback
	 * @param {string} name
	 */
	removeBy(callback: (item: T) => boolean, name?: string): IHashMapCollection<T>;

	/**
	 * clear collection
	 */
	clear(): IHashMapCollection<T>;
}


export class Collection<T> implements ICollection<T> {
	protected collection: T[];

	public constructor(collection: T[] = []) {
		this.collection = collection;
	}

	/**
	 * @inheritDoc
	 */
	public add(item: T): ICollection<T> {
		const array = this.toArray();
		array[array.length] = item;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public each<U extends ILoop<T>>(callback: (this: U, value: T, index: number) => any | boolean): U {
		const context: U = <U>{
			count: -1,
			loop: false,
			item: null,
			value: null,
			key: null,
		};
		const array = this.toArray();
		const length = array.length;
		for (context.count = 0; context.count < length; context.key = context.count++) {
			context.loop = true;
			if (callback.call(context, context.value = array[context.count], context.count) === false) {
				break;
			}
		}
		return context;
	}

	/**
	 * @inheritDoc
	 */
	public subEach<U extends ILoop<T>>(callback: (this: U, value: T, index: number) => any | boolean, start?: number, length?: number): U {
		return this.subCollection(start, length).each<U>(callback);
	}

	/**
	 * @inheritDoc
	 */
	public subCollection(start?: number, length?: number): ICollection<T> {
		if (!this.collection) {
			return new Collection<T>();
		}
		const collectionLength = this.collection.length;
		start = start || 0;
		length = start + (length || collectionLength);
		const items = [];
		for (let i = start; i < length && i < collectionLength; i++) {
			items[items.length] = this.collection[i];
		}
		return new Collection<T>(items);
	}

	/**
	 * @inheritDoc
	 */
	public reverse(): ICollection<T> {
		this.collection = this.toArray().reverse();
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public toArray(): T[] {
		return this.collection ? this.collection : this.collection = [];
	}

	/**
	 * @inheritDoc
	 */
	public getCount(): number {
		return this.toArray().length;
	}

	/**
	 * @inheritDoc
	 */
	public index(index: number): T | null {
		if (!this.collection || index >= this.collection.length) {
			return null;
		}
		return this.collection[index];
	}

	/**
	 * @inheritDoc
	 */
	public first(): T | null {
		return this.collection && this.collection.length > 0 ? this.collection[0] : null;
	}

	/**
	 * @inheritDoc
	 */
	public last(): T | null {
		return this.collection && this.collection.length > 0 ? this.collection[this.collection.length - 1] : null;
	}

	/**
	 * @inheritDoc
	 */
	public isEmpty(): boolean {
		return this.getCount() === 0;
	}

	/**
	 * @inheritDoc
	 */
	public clear(): ICollection<T> {
		this.collection = [];
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public removeBy(callback: (item: T) => boolean, name?: string): ICollection<T> {
		let collection: T[] = [];
		this.each((value: T) => {
			if (callback(value) !== false) {
				collection[collection.length] = value;
			}
		});
		this.collection = collection;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public copy(copy: ICollection<T>): ICollection<T> {
		this.collection = this.toArray().concat(copy.toArray());
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public replace(replace: ICollection<T>): ICollection<T> {
		this.collection = replace.toArray();
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public sort(sort?: (alpha: any, beta: any) => number): ICollection<T> {
		this.collection = this.toArray().sort(sort);
		return this;
	}
}

export class HashMap<T> implements IHashMap<T> {
	protected hashMap: any;

	public constructor(hashMap: Object = {}) {
		this.hashMap = hashMap;
	}

	/**
	 * @inheritDoc
	 */
	public set(name: string | number, item: T): IHashMap<T> {
		this.hashMap[name] = item;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public put(put: Object): IHashMap<T> {
		this.hashMap = put;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public has(name: string): boolean {
		return this.hashMap.hasOwnProperty(name);
	}

	/**
	 * @inheritDoc
	 */
	public get(name: string, value?: any): T {
		return this.hashMap.hasOwnProperty(name) ? this.hashMap[name] : value;
	}

	/**
	 * @inheritDoc
	 */
	public remove(name: string): IHashMap<T> {
		this.hashMap[name] = null;
		delete this.hashMap[name];
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public isEmpty(): boolean {
		const hasOwnProperty = Object.prototype.hasOwnProperty;
		if (this.hashMap == null) {
			return true
		} else if (this.hashMap.length > 0) {
			return false
		} else if (this.hashMap.length === 0) {
			return true
		} else if (typeof this.hashMap !== "object") {
			return false;
		}
		for (const key in this.hashMap) {
			if (hasOwnProperty.call(this.hashMap, key)) {
				return false
			}
		}
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public toObject(): Object {
		return this.hashMap;
	}

	/**
	 * @inheritDoc
	 */
	public each<U extends ILoop<T>>(callback: (this: U, key: string | number, value: T) => any | boolean): U {
		const context: U = <U> {
			count: -1,
			loop: false,
			item: null,
			value: null,
			key: null,
		};
		if (!this.hashMap) {
			return context;
		}
		for (const key in this.hashMap) {
			context.loop = true;
			context.count++;
			if (callback.call(context, context.key = key, context.value = this.hashMap[key]) === false) {
				break;
			}
		}
		return context;
	}

	/**
	 * @inheritDoc
	 */
	public first(): T | null {
		return this.each(() => false).value;
	}

	/**
	 * @inheritDoc
	 */
	public last(): T | null {
		return this.each(() => true).value;
	}

	/**
	 * @inheritDoc
	 */
	public getCount(): number {
		return this.each(() => true).count + 1;
	}

	/**
	 * @inheritDoc
	 */
	public clear(): IHashMap<T> {
		this.hashMap = {};
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public copy(copy: IHashMap<T>): IHashMap<T> {
		copy.each((k, v) => this.set(k, v));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public replace(replace: IHashMap<T>): IHashMap<T> {
		this.hashMap = replace.toObject();
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public fromCollection(collection: ICollection<T>, key: (value: T) => string): IHashMap<T> {
		collection.each(value => this.set(key(value), value));
		return this;
	}
}

export class HashMapCollection<T> implements IHashMapCollection<T> {
	protected hashMap: IHashMap<ICollection<T>> = new HashMap<ICollection<T>>();

	/**
	 * @inheritDoc
	 */
	public add(name: string, item: T): IHashMapCollection<T> {
		if (this.hashMap.has(name) === false) {
			this.hashMap.set(name, new Collection<T>());
		}
		this.hashMap.get(name).add(item);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public has(name: string): boolean {
		return this.hashMap.has(name);
	}

	/**
	 * @inheritDoc
	 */
	public sort(name: string, sort?: (alpha: T, beta: T) => number): IHashMapCollection<T> {
		this.toCollection(name).sort(sort);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public toArray(name: string): T[] {
		return this.hashMap.get(name, new Collection()).toArray();
	}

	/**
	 * @inheritDoc
	 */
	public toCollection(name: string): ICollection<T> {
		return this.hashMap.get(name, new Collection<T>());
	}

	/**
	 * @inheritDoc
	 */
	public each<U extends ILoop<T>>(name: string, callback: (this: U, value: T, index: string | number) => any | boolean): U {
		return this.toCollection(name).each<U>(callback);
	}

	/**
	 * @inheritDoc
	 */
	public eachCollection<U extends ILoop<ICollection<T>>>(callback: (this: U, name: string, collection: ICollection<T>) => any | boolean): U {
		return this.hashMap.each<U>(callback);
	}

	/**
	 * @inheritDoc
	 */
	public remove(name: string): IHashMapCollection<T> {
		this.hashMap.remove(name);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public removeBy(callback: (item: T) => boolean, name?: string): IHashMapCollection<T> {
		if (name) {
			this.toCollection(name).removeBy(callback);
			return this;
		}
		this.hashMap.each((_: any, item: ICollection<T>) => item.removeBy(callback));
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public clear(): IHashMapCollection<T> {
		this.hashMap = new HashMap<ICollection<T>>();
		return this;
	}
}
