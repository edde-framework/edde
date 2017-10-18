import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {Listen} from "../../edde/decorator";
import {IElement} from "../../edde/protocol";

export class RegisterButton extends AbstractControl {
	protected button: IHtmlElement;

	public constructor() {
		super('register-button');
	}

	public build(): IHtmlElement {
		return e3.html('<div class="control">').attach(this.button = e3.html('<button class="button is-primary" disabled>Register</button>'));
	}

	@Listen.ToNative('click')
	public onClick() {
		this.button.addClass('is-loading');
		e3.emit('register-view/register');
	}

	@Listen.To('email-input/is-valid')
	public eventEmailInputIsValid(element: IElement) {
		this.button.attr('disabled', 'disabled');
		this.button.removeClass('is-loading');
		if (element.getMeta('valid', false)) {
			this.button.removeAttribute('disabled');
		}
	}
}
