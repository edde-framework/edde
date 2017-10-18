import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {MainBarControl} from "../index/MainBarControl";
import {e3} from "../../edde/e3";

export class LoginView extends AbstractControl {
	public build(): IHtmlElement {
		return e3.html('<div class="view is-hidden"></div>').attachList([
			this.use(new MainBarControl()),
		]);
	}
}
