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
			`),
			e3.html(`
				<section class="section">
					<div class="columns">
						<div class="column is-offset-one-quarter is-half">
							<div class="field">
								<p class="control has-icons-left has-icons-right">
									<input class="input" type="email" placeholder="Email">
									<span class="icon is-small is-left">
										<i class="fa fa-envelope"></i>
									</span>
									<span class="icon is-small is-right"><i class="fa fa-check"></i></span>
								</p>
							        <p class="help">later you will use this to login</p>
							</div>
							<div class="field">
								<p class="control has-icons-left">
									<input class="input" type="password" placeholder="Password">
									<span class="icon is-small is-left">
										<i class="fa fa-lock"></i>
									</span>
								</p>
								<p class="help">think about something long and safe</p>
							</div>
							<div class="field">
								<p class="control has-icons-left has-icons-right">
									<input class="input" type="password" placeholder="Type again">
									<span class="icon is-small is-left">
										<i class="fa fa-lock"></i>
									</span>
									<span class="icon is-small is-right"><i class="fa fa-check"></i></span>
								</p>
							</div>
							<div class="field is-grouped is-grouped-right">
								<div class="control">
									<button class="button is-text">Cancel</button>
								</div>
								<div class="control">
									<button class="button is-primary">Register</button>
								</div>
							</div>
						</div>
					</div>
				</section>
			`),
		]);
	}
}
