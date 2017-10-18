import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";

export class RegisterView extends AbstractControl {
	public constructor() {
		super('register-view');
	}

	public build(): IHtmlElement {
		return e3.html('uyaa!');
	}
}
