import {e3} from "../edde/e3";

/**
 * setup request service
 */
e3.RequestService();
e3.ProtocolService();
e3.ControlFactory();

/**
 * hardcoded loader view will display initial loader before everything other will be available;
 * attachTo also do the rendering
 */
e3.emit('control/create', {
	'control': 'app/index/IndexView:IndexView',
	'root': e3.el(document.body)
});
