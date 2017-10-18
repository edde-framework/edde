import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";

export class PasswordInput extends AbstractControl {
	protected placeholder: string;
	protected input: IHtmlElement;
	protected tick: IHtmlElement;

	public constructor(placeholder: string) {
		super('password-input');
		this.placeholder = placeholder;
	}

	public build(): IHtmlElement {
		return e3.html('<p class="control has-icons-left has-icons-right"></p>').attachList([
			this.input = e3.html(`<input class="input" type="password" placeholder="${this.placeholder}">`),
			e3.html(`
				<span class="icon is-small is-left">
					<i class="fa fa-lock"></i>
				</span>
			`),
			this.tick = e3.html('<span class="icon is-small is-right is-hidden"><i class="fa fa-check"></i></span>'),
		]);
	}
}
