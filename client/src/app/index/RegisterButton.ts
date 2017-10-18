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
			<p class="control">
				<span class="button">
					<span class="icon"><i class="fa fa-user-circle"></i></span>
					<span>Register</span>
				</span>
			</p>
		`);
	}

	@Listen.ToNative('click')
	public onClick() {
		e3.emit('view/change', {
			'view': 'register-view',
		});
	}
}
