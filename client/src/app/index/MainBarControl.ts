import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";

export class MainBarControl extends AbstractControl {
	public constructor() {
		super('main-bar-control');
	}

	public build(): IHtmlElement {
		return e3.html('<nav class="navbar is-white"><div class="container">' +
			'<div class="navbar-brand">' +
			'<a class="navbar-item">' +
			'<div class="field is-grouped">' +
			'<p class="control"><img src="/img/logo.png"></p>' +
			'<p class="control"><span>Edde Framework</span></p>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'<div class="navbar-menu">' +
			'<div class="navbar-end">' +
			'<span class="navbar-item">' +
			'<div class="field is-grouped">' +
			'</div>' +
			'</span>' +
			'</div>' +
			'</div>' +
			'</nav>');
	}
}
