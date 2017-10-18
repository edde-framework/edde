import {AbstractControl} from "../../edde/control";
import {e3} from "../../edde/e3";
import {IHtmlElement} from "../../edde/dom";

export class LoginButton extends AbstractControl {
	public constructor() {
		super('login-button');
	}

	public build(): IHtmlElement {
		return e3.html(`
			<p class="control">
				<span class="button is-primary">
					<span class="icon"><i class="fa fa-lock"></i></span>
					<span>Login</span>
				</span>
			</p>
		`);
	}
}
