import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";

export class EmailInput extends AbstractControl {
	public constructor() {
		super('email-input');
	}

	public build(): IHtmlElement {
		return e3.html(`
			<p class="control has-icons-left has-icons-right">
				<input class="input" type="email" placeholder="Email">
				<span class="icon is-small is-left">
					<i class="fa fa-envelope"></i>
				</span>
				<span class="icon is-small is-right"><i class="fa fa-check"></i></span>
			</p>
		`);
	}
}
