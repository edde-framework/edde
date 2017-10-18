import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {MainBarControl} from "../index/MainBarControl";
import {EmailInput} from "./EmailInput";
import {RegisterCancelButton} from "./RegisterCancelButton";
import {RegisterButton} from "./RegisterButton";

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
									<p class="subtitle">...welcome to the community!</p>
								</div>
							</div>
						</div>
					</div>
				</section>
			`),
			e3.html('<section class="section"></section>').attach(e3.html('<div class="columns"></div>').attach(e3.html('<div class="column is-offset-4 is-4">').attachList([
				e3.html('<div class="field">').attachList([
					this.use(new EmailInput()),
					e3.html('<p class="help">later you will use this to login</p>'),
				]),
				e3.html('<div class="field">').attachList([
					e3.html(`
						<p class="control has-icons-left">
							<input class="input" type="password" placeholder="Password">
							<span class="icon is-small is-left">
								<i class="fa fa-lock"></i>
							</span>
						</p>
					`),
					e3.html('<p class="help">think about something long and safe</p>'),
				]),
				e3.html('<div class="field">').attachList([
					e3.html(`
						<p class="control has-icons-left has-icons-right">
							<input class="input" type="password" placeholder="Type again">
							<span class="icon is-small is-left">
								<i class="fa fa-lock"></i>
							</span>
							<span class="icon is-small is-right"><i class="fa fa-check"></i></span>
						</p>
					`),
					e3.html('<p class="help">...are you sure about you password :)?</p>'),
				]),
				e3.html('<div class="field is-grouped is-grouped-right">').attachList([
					this.use(new RegisterCancelButton()),
					this.use(new RegisterButton()),
				]),
			]))),
		]);
	}
}
