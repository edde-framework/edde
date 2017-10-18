import {e3} from "../edde/e3";

/**
 * setup request service
 */
e3.RequestService();
e3.ProtocolService();
e3.ControlFactory();
/**
 * view manager responsible for listening
 * to view related events and reacting on them
 */
e3.ViewManager();

const body = e3.el(document.body);

e3.hashMap({
	'index-view': 'app/index/IndexView:IndexView',
	'register-view': 'app/login/RegisterView:RegisterView',
	'login-view': 'app/login/LoginView:LoginView',
}).each((view, control) => e3.emit('view/register', {
	'view': view,
	'control': control,
	'root': body
}));
/**
 * switch to a new created view
 */
e3.emit('view/change', {
	'view': 'index-view',
});
