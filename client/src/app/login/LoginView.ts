import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {MainBarControl} from "../index/MainBarControl";
import {e3} from "../../edde/e3";

export class LoginView extends AbstractControl {
	public build(): IHtmlElement {
		return e3.html('<div class="view is-hidden"></div>').attachList([
			this.use(new MainBarControl()),
			e3.html(`
				<section class="hero is-small is-bold is-info">
					<div class="hero-body">
						<div class="container">
							<div class="columns is-vcentered">
								<div class="column">
									<p class="title">Welcome back!</p>
									<p class="subtitle">...please, authenticate!</p>
								</div>
							</div>
						</div>
					</div>
				</section>
			`),
		]);
	}
}
