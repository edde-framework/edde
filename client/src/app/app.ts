import {e3} from "../edde/e3";
import {LoaderView} from "./loader/LoaderView";

/**
 * setup request service
 */
e3.RequestService();
e3.ProtocolService();

/**
 * hardcoded loader view will display initial loader before everything other will be available;
 * attachTo also do the rendering
 */
new LoaderView().attachTo(e3.el(document.body));
