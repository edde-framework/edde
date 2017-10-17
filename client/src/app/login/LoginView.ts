import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";

export class LoginView extends AbstractControl {
	public constructor() {
		super('login-view');
	}

	/**
	 * @inheritDoc
	 */
	public build(): IHtmlElement {
		return e3.html('<div>login view!</div>');
	}
}
