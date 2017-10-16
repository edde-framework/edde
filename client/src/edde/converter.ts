import {e3} from "./e3";
import {IHashMap} from "./collection";

/**
 * General type to attach mime type to some type.
 */
export interface IContent<T> {
	/**
	 * return pure content
	 */
	getContent(): T;

	/**
	 * return mime (it could be generally any string) type of the content
	 */
	getMime(): string;
}

/**
 * Converter is responsible for general data conversion from type A to B (for example object to json, ...).
 */
export interface IConverter {
	/**
	 * index list of supported mime types (or generic identifiers); they should be used only as alias (for example application/json, text/json, ...) and not for
	 * logical differentiating of types; in other words - all mime list must be compatible with all (internally supported) targets (not only combinations)
	 */
	getMimeList(): string[];

	/**
	 * do conversion from input pure content to IContent; IContent on output has real mime of the content
	 */
	convert<S, T>(content: S, mime: string, target: string | null): IContent<T>;

	/**
	 * convert the given content
	 */
	content<S, T>(content: IContent<S>, target: string | null): IContent<T>;
}

export interface IConvertable<S, T> {
	/**
	 * return subject content
	 */
	getContent(): IContent<S>;

	/**
	 * return target mime type; if target is not specified, source should not be converted
	 */
	getTarget(): string | null;

	/**
	 * try to convert an input
	 */
	convert(): IContent<T>;
}

export interface IConverterManager {
	/**
	 * register a converter
	 */
	registerConverter(converter: IConverter): IConverterManager;

	/**
	 * magical method for generic data conversion; ideologically it is based on a mime type conversion, but identifiers can be arbitrary
	 */
	convert<S, T>(content: S, mime: string, targetList: string[] | null): IConvertable<S, T>;

	content<S, T>(content: IContent<S>, targetList?: string[] | null): IConvertable<S, T>;
}

export class Content<T> implements IContent<T> {
	protected content: T;
	protected mime: string;

	public constructor(content: T, mime: string) {
		this.content = content;
		this.mime = mime;
	}

	public getContent(): T {
		return this.content;
	}

	public getMime(): string {
		return this.mime;
	}
}

export abstract class AbstractConverter implements IConverter {
	protected mimeList: string[] = [];

	protected register(source: string[], target: string[]) {
		e3.$(source, src => e3.$(target, tgt => this.mimeList[this.mimeList.length] = src + '|' + tgt));
	}

	public getMimeList(): string[] {
		return this.mimeList;
	}

	public content<S, T>(content: IContent<S>, target: string | null): IContent<T> {
		return this.convert<S, T>(content.getContent(), content.getMime(), target);
	}

	public abstract convert<S, T>(content: S, mime: string, target: string | null): IContent<T>;
}

export class Convertable<S, T> implements IConvertable<S, T> {
	protected converter: IConverter;
	protected content: IContent<S>;
	protected target: string | null = null;
	protected result: IContent<T>;

	public constructor(converter: IConverter, content: IContent<S>, target: string | null) {
		this.converter = converter;
		this.content = content;
		this.target = target;
	}

	public getContent(): IContent<S> {
		return this.content;
	}

	public getTarget(): string | null {
		return this.target;
	}

	public convert(): IContent<T> {
		if (this.result) {
			return this.result;
		}
		return this.result = this.converter.content(this.content, this.target);
	}
}

export class ConverterManager implements IConverterManager {
	protected converterList: IHashMap<IConverter> = e3.hashMap<IConverter>();

	public registerConverter(converter: IConverter): IConverterManager {
		e3.$(converter.getMimeList(), mime => this.converterList.set(mime, converter));
		return this;
	}

	public convert<S, T>(content: S, mime: string, targetList: string[] | null): IConvertable<S, T> {
		return this.content(new Content<S>(content, mime), targetList);
	}

	public content<S, T>(content: IContent<S>, targetList: string[] | null): IConvertable<S, T> {
		if (targetList === null) {
			return new Convertable<S, T>(new PassConverter(), content, content.getMime());
		}
		const mime = content.getMime();
		let convertable = null;
		e3.$(targetList, target => {
			const id = mime + '|' + target;
			if (mime === target) {
				convertable = new Convertable<S, T>(new PassConverter(), content, content.getMime());
				return false;
			} else if (this.converterList.has(id)) {
				convertable = new Convertable<S, T>(this.converterList.get(id), content, target);
				return false;
			}
		});
		if (convertable) {
			return convertable;
		}
		throw new Error('Cannot convert [' + mime + '].');
	}
}

export class PassConverter extends AbstractConverter {
	public convert<S, T>(content: S, mime: string, target: string | null): IContent<T> {
		return new Content<T>(<any>content, mime);
	}
}

export class JsonConverter extends AbstractConverter {
	public constructor() {
		super();
		this.register([
			'object',
		], [
			'application/json',
			'json',
		]);
		this.register([
			'application/json',
			'json',
		], [
			'object',
		]);
	}

	public convert<S, T>(content: S, mime: string, target: string | null): IContent<T> {
		switch (target) {
			case 'application/json':
			case 'json':
				return new Content<T>(<any>JSON.stringify(content), 'application/json');
			case 'object':
				return new Content<T>(<any>JSON.parse(<any>content), 'application/javascript');
		}
		throw new Error('Cannot convert [' + mime + '] to [' + target + '] in json converter.');
	}
}
