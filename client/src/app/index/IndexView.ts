import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";

export class IndexView extends AbstractControl {
	public build(): IHtmlElement {
		const registerButton =
			`<p class="control">
				<span class="button">
					<span class="icon"><i class="fa fa-user-circle"/></span>
					<span>Register</span>
				</span>
			</p>`;
		return e3.html(
			`<nav class="navbar is-white">
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
									${registerButton}
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
			</section>`);
	}
}
