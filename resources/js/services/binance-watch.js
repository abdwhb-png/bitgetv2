// window.DEBUG = false;

import defOpts from "./configs/binanceWatchDefOptions";
import Options from "./modules/options";
import Ajax from "./modules/ajax";
import Binance from "./modules/binance";
import Coincap from "./modules/coincap";
import Alarms from "./modules/alarms";
import History from "./modules/history";
import Notify from "./modules/notify";
import News from "./modules/news";
import Messenger from "./modules/messenger";
import Router from "./modules/router";
import Bus from "./modules/bus";
import Sorter from "./modules/sorter";
import Scroller from "./modules/scroller";
import Tooltip from "./modules/tooltip";
import store from "./modules/store";
import sentiment from "./modules/sentiment";
import utils from "./modules/utils";

export function binanceWatch() {
    const _options = new Options(defOpts);
    const _ajax = new Ajax();
    const _binance = new Binance();
    const _coincap = new Coincap();
    const _alarms = new Alarms();
    const _history = new History();
    const _notify = new Notify();
    const _news = new News();
    const _messenger = new Messenger();
    const _bus = new Bus();
    const _sorter = new Sorter();
    const _scroller = new Scroller();
    const _tooltip = new Tooltip();

    return {
        _options,
        _ajax,
        _binance,
        _coincap,
        _alarms,
        _history,
        _notify,
        _news,
        _messenger,
        _bus,
        _sorter,
        _scroller,
        _tooltip,
    };
}
