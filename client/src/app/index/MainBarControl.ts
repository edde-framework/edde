import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {RegisterButton} from "./RegisterButton";
import {LoginButton} from "./LoginButton";

export class MainBarControl extends AbstractControl {
	public constructor() {
		super('main-bar-control');
	}

	public build(): IHtmlElement {
		return e3.html('<nav class="navbar is-white"></nav>').attach(
			e3.html('<div class="container"></div>').attachList([
				e3.html(`
					<div class="navbar-brand">
						<a class="navbar-item" href="/">
							<div class="field is-grouped">
								<p class="control">
									<img src="/img/logo.png"/>
								</p>
								<p class="control">
									<span>Edde Framework</span>
								</p>
							</div>
						</a>
					</div>
				`),
				e3.html('<div class="navbar-menu"></div>').attach(
					e3.html('<div class="navbar-end"></div>').attach(
						e3.html('<span class="navbar-item">').attach(
							e3.html('<div class="field is-grouped"></div>').attachList([
								this.use(new RegisterButton()),
								this.use(new LoginButton()),
							]))))
			]));
	}
}
