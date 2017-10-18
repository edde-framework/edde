import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {Listen} from "../../edde/decorator";

export class RegisterCancelButton extends AbstractControl {
	public constructor() {
		super('register-cancel');
	}

	public build(): IHtmlElement {
		return e3.html(`
			<div class="control">
				<button class="button is-text">Cancel</button>
			</div>
		`);
	}

	@Listen.ToNative('click')
	public onClick() {
		e3.emit('view/change', {
			'view': 'index-view'
		});
	}
}
