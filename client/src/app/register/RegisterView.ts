import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {MainBarControl} from "../index/MainBarControl";

export class RegisterView extends AbstractControl {
	public constructor() {
		super('register-view');
	}

	public build(): IHtmlElement {
		return e3.html('<div class="view is-hidden"></div>').attachList([
			this.use(new MainBarControl()),
			e3.html(`
				<section class="hero is-small is-bold is-info">
					<div class="hero-body">
						<div class="container">
							<div class="columns is-vcentered">
								<div class="column">
									<p class="title">New User registration</p>
								</div>
							</div>
						</div>
					</div>
				</section>
			`)
		]);
	}
}
