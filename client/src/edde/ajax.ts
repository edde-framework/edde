import {AbstractPromise, IPromise} from "./promise";
import {IContent} from "./converter";

export interface IAjaxHandler extends IPromise {
	serverFail(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

	/**
	 * called in case of 5xx code
	 *
	 * @param xmlHttpRequest
	 */
	onServerFail(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

	clientFail(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

	/**
	 * called when 4xx code
	 *
	 * @param xmlHttpRequest
	 */
	onClientFail(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

	timeout(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

	onTimeout(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;

	error(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler;

	onError(xmlHttpRequest: XMLHttpRequest): IAjaxHandler;
}

export interface IAjax {
	setAccept(accept: string): IAjax;

	/**
	 * set async flag
	 *
	 * @param async
	 */
	setAsync(async: boolean): IAjax;

	setTimeout(timeout: number): IAjax;

	/**
	 * return hooks bound to this ajax request
	 */
	getAjaxHandler(): IAjaxHandler;

	/**
	 * run, Forest, run!
	 */
	execute<T>(content?: IContent<T>): IAjaxHandler;
}

export class AjaxHandler extends AbstractPromise implements IAjaxHandler {
	/**
	 * @inheritDoc
	 */
	public serverFail(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler {
		this.register('server-fail', callback);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public onServerFail(xmlHttpRequest: XMLHttpRequest): IAjaxHandler {
		return <IAjaxHandler>this.execute('server-fail', xmlHttpRequest);
	}

	/**
	 * @inheritDoc
	 */
	public clientFail(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler {
		this.register('client-fail', callback);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public onClientFail(xmlHttpRequest: XMLHttpRequest): IAjaxHandler {
		return <IAjaxHandler>this.execute('client-fail', xmlHttpRequest);
	}

	/**
	 * @inheritDoc
	 */
	public timeout(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler {
		this.register('timeout', callback);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public onTimeout(xmlHttpRequest: XMLHttpRequest): IAjaxHandler {
		return <IAjaxHandler>this.execute('timeout', xmlHttpRequest);
	}

	/**
	 * @inheritDoc
	 */
	public error(callback: (xmlHttpRequest: XMLHttpRequest) => void): IAjaxHandler {
		this.register('error', callback);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public onError(xmlHttpRequest: XMLHttpRequest): IAjaxHandler {
		return <IAjaxHandler>this.execute('error', xmlHttpRequest);
	}
}

export class Ajax implements IAjax {
	protected url: string;
	protected method: string;
	protected accept: string;
	protected async: boolean;
	protected timeout: number = 10000;
	protected ajaxHandler: IAjaxHandler;

	public constructor(url: string) {
		this.url = url;
		this.method = 'post';
		this.accept = 'application/json';
		this.async = true;
		this.ajaxHandler = new AjaxHandler();
	}

	public setMethod(method: string): IAjax {
		this.method = method;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public setAccept(accept: string): IAjax {
		this.accept = accept;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public setAsync(async: boolean): IAjax {
		this.async = async;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public setTimeout(timeout: number): IAjax {
		this.timeout = timeout;
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public getAjaxHandler(): IAjaxHandler {
		return this.ajaxHandler;
	}

	/**
	 * @inheritDoc
	 */
	public execute<T>(content: IContent<T>): IAjaxHandler {
		const xmlHttpRequest = new XMLHttpRequest();
		try {
			let timeoutId: any = null;
			xmlHttpRequest.onreadystatechange = () => {
				switch (xmlHttpRequest.readyState) {
					/**
					 * UNSENT
					 *
					 * The XMLHttpRequest client has been created, but the open() method
					 * hasn't been called yet.
					 */
					case 0:
						break;
					/**
					 * OPENED
					 *
					 * open() method has been invoked. During this state, the request headers
					 * can be set using the setRequestHeader() method and the send() method can
					 * be called which will initiate the fetch.
					 */
					case 1:
						content ? xmlHttpRequest.setRequestHeader('Content-Type', content.getMime()) : null;
						xmlHttpRequest.setRequestHeader('Accept', this.accept);
						timeoutId = setTimeout(() => {
							xmlHttpRequest.abort();
							this.ajaxHandler.onTimeout(xmlHttpRequest);
							this.ajaxHandler.onFail(xmlHttpRequest);
							this.ajaxHandler.onAlways(xmlHttpRequest);
						}, this.timeout);
						break;
					/**
					 * HEADERS_RECEIVED
					 *
					 * send() has been called, and headers and status are available.
					 */
					case 2:
						break;
					/**
					 * LOADING
					 *
					 * Response's body is being received. If responseType is "text" or
					 * empty string, responseText will have the partial text response as it loads.
					 */
					case 3:
						clearTimeout(timeoutId);
						timeoutId = null;
						break;
					/**
					 * DONE
					 *
					 * The fetch operation is complete. This could mean that either the
					 * data transfer has been completed successfully or failed.
					 */
					case 4:
						try {
							if (xmlHttpRequest.status >= 200 && xmlHttpRequest.status <= 299) {
								this.ajaxHandler.onSuccess(xmlHttpRequest);
							} else if (xmlHttpRequest.status >= 400 && xmlHttpRequest.status <= 499) {
								this.ajaxHandler.onClientFail(xmlHttpRequest);
								this.ajaxHandler.onFail(xmlHttpRequest);
							} else if (xmlHttpRequest.status >= 500 && xmlHttpRequest.status <= 599) {
								this.ajaxHandler.onServerFail(xmlHttpRequest);
								this.ajaxHandler.onFail(xmlHttpRequest);
							}
						} catch (e) {
							this.ajaxHandler.onError(xmlHttpRequest);
							this.ajaxHandler.onFail(xmlHttpRequest);
						}
						this.ajaxHandler.onAlways(xmlHttpRequest);
						break;
				}

			};
			xmlHttpRequest.open(this.method.toUpperCase(), this.url, this.async);
			xmlHttpRequest.send(content ? content.getContent() : null);
		} catch (e) {
			this.ajaxHandler.onError(xmlHttpRequest);
			this.ajaxHandler.onFail(xmlHttpRequest);
			this.ajaxHandler.onAlways(xmlHttpRequest);
		}
		return this.ajaxHandler;
	}
}
