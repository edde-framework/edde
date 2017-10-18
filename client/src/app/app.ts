import {e3} from "../edde/e3";

/**
 * setup request service
 */
e3.RequestService();
e3.ProtocolService();
e3.ControlFactory();

e3.emit('control/create', {
	'control': 'app/index/IndexView:IndexView',
	'root': e3.el(document.body)
});
