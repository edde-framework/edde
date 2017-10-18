import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {Listen} from "../../edde/decorator";

export class RegisterButton extends AbstractControl {
	public constructor() {
		super('register-button');
	}

	public build(): IHtmlElement {
		return e3.html(`
			<div class="control">
				<button class="button is-primary">Register</button>
			</div>
		`);
	}

	@Listen.ToNative('click')
	public onClick() {
		alert('Do the registration!');
	}
}
