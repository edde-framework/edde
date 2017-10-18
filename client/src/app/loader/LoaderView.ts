import {AbstractControl} from "../../edde/control";
import {e3} from "../../edde/e3";
import {IHtmlElement} from "../../edde/dom";

export class LoaderView extends AbstractControl {
	public constructor() {
		super('loader-view');
	}

	/**
	 * @inheritDoc
	 */
	public build(): IHtmlElement {
		return e3.html('<div class="columns"><div class="column"><div class="loader"></div></div></div>');
	}
}
