import {AbstractControl} from "../../edde/control";
import {IHtmlElement} from "../../edde/dom";
import {e3} from "../../edde/e3";
import {Listen} from "../../edde/decorator";
import {IElement} from "../../edde/protocol";

export class EmailInput extends AbstractControl {
	protected input: IHtmlElement;
	protected icon: IHtmlElement;
	protected iconElement: IHtmlElement;
	protected hint: string = '';
	protected help: IHtmlElement;

	public constructor() {
		super('email-input');
	}

	public setHint(hint: string): EmailInput {
		this.hint = hint;
		return this;
	}

	public build(): IHtmlElement {
		const html = [
			this.input = e3.html('<input class="input" type="email" placeholder="Email">'),
			e3.html(`
				<span class="icon is-small is-left">
					<i class="fa fa-envelope"></i>
				</span>
			`),
			this.icon = e3.html('<span class="icon is-small is-right is-hidden"></span>').attach(this.iconElement = e3.html('<i class="fa"></i>')),
		];
		html.push(this.help = e3.html(`<p class="help">${this.hint}</p>`));
		return e3.html('<p class="control has-icons-left has-icons-right"></p>').attachList(html);
	}

	public getValue(): string {
		return (<HTMLInputElement>this.input.getElement()).value;
	}

	public isValid(): boolean {
		return /^[a-z0-9_.-]+@[a-z0-9_.-]+\.[a-z0-9]{1,6}$/i.test(this.getValue());
	}

	public validate(): boolean {
		const isValid = this.isValid();
		this.input.toggleClass('is-primary', isValid);
		this.input.toggleClass('is-danger', isValid === false);
		this.icon.removeClass('is-hidden');
		this.iconElement.toggleClass('fa-check', isValid);
		this.iconElement.toggleClass('fa-warning', isValid === false);
		this.help.toggleClass('is-danger', isValid === false);
		this.help.text(<string>(isValid === false ? 'email looks like it is not an email, oops!' : this.hint));
		e3.emit('email-input/is-valid', {'valid': isValid});
		return isValid;
	}

	@Listen.ToNative('keyup')
	public onKeypress() {
		this.validate();
	}

	@Listen.To('register-view/register', 50)
	public eventRegisterViewRegister(element: IElement) {
		if (this.validate() === false) {
			element.setMeta('cancel', true);
			return;
		}
		element.setMeta('email', this.getValue());
	}
}
