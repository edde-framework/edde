import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {MainBarControl} from "./MainBarControl";

export class IndexView extends AbstractControl {
	public build(): IHtmlElement {
		return e3.El('div').attachList([
			this.use(new MainBarControl()),
			e3.html(`
				<section class="hero is-small is-bold is-info">
					<div class="hero-body">
						<div class="container">
							<div class="columns is-vcentered">
								<div class="column">
									<p class="title">Welcome to Edde Framework</p>
									<p class="subtitle">...epic, fast and modern Framework</p>
								</div>
							</div>
						</div>
					</div>
				</section>
			`)
		]);
	}
}
