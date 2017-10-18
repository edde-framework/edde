import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {MainBarControl} from "./MainBarControl";

export class IndexView extends AbstractControl {
	public build(): IHtmlElement {
		return new MainBarControl().build();
	}
}
