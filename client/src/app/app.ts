import {e3} from "../edde/e3";
import {LoaderView} from "./loader/LoaderView";

/**
 * setup request service
 */
e3.RequestService();
e3.ProtocolService();

new LoaderView().attachTo(e3.el(document.body));
