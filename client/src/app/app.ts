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

/**
 * register default view
 */
e3.emit('view/register', {
	'view': 'index-view',
	'control': 'app/index/IndexView:IndexView',
	'root': e3.el(document.body)
});
/**
 * switch to a new created view
 */
e3.emit('view/change', {
	'view': 'index-view',
});
