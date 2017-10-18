import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {RegisterButton} from "./RegisterButton";

export class MainBarControl extends AbstractControl {
	public constructor() {
		super('main-bar-control');
	}

	public build(): IHtmlElement {
		return e3.html(`
			<nav class="navbar is-white">
				<div class="container">
					<div class="navbar-brand">
						<a class="navbar-item">
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
					<div class="navbar-menu">
						<div class="navbar-end">
							<span class="navbar-item">
								<div class="field is-grouped">
									${new RegisterButton().build()}
									<p class="control">
										<span class="button is-primary">
											<span class="icon"><i class="fa fa-lock"/></span>
											<span>Login</span>
										</span>
									</p>
								</div>
							</span>
						</div>
					</div>
				</div>
			</nav>
		`);
	}
}
