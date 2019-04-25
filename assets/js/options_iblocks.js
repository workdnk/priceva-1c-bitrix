// Обычные товары
function showIblocksIfSimpleProductEnable() {
    var form = BX('options'),
        select_SIMPLE_PRODUCT_ENABLE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'SIMPLE_PRODUCT_ENABLE'}
        }, true)[0];
    BX.bind(select_SIMPLE_PRODUCT_ENABLE, 'bxchange', check_showIblocksIfSimpleProductEnable)
}

function check_showIblocksIfSimpleProductEnable() {
    var form = BX('options'),
        select_SIMPLE_PRODUCT_ENABLE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'SIMPLE_PRODUCT_ENABLE'}
        }, true)[0],
        select_IBLOCK_TYPE_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'IBLOCK_TYPE_ID'}
        }, true)[0],
        select_IBLOCK_MODE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'IBLOCK_MODE'}
        }, true)[0],
        select_IBLOCK_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'IBLOCK_ID'}
        }, true)[0];

    if (select_SIMPLE_PRODUCT_ENABLE.value === 'YES') {
        BX.adjust(select_IBLOCK_TYPE_ID, {props: {disabled: false}});
        BX.adjust(select_IBLOCK_MODE, {props: {disabled: false}});
        check_showIBlocksIfOneIBlock();
    } else {
        BX.adjust(select_IBLOCK_TYPE_ID, {props: {disabled: true}});
        BX.adjust(select_IBLOCK_MODE, {props: {disabled: true}});
        BX.adjust(select_IBLOCK_ID, {props: {disabled: true}});

        select_IBLOCK_TYPE_ID.value = 0;
        select_IBLOCK_MODE.value = 0;
        select_IBLOCK_ID.value = 0;

        var length = select_IBLOCK_ID.options.length;
        for (var i = 0; i < length; i++) {
            if (select_IBLOCK_ID.options[i].value != 0) {
                select_IBLOCK_ID.options[i] = null;
            }
        }
    }
}

function showIBlocksIfOneIBlock() {
    var form = BX('options'),
        select_IBLOCK_MODE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'IBLOCK_MODE'}
        }, true);
    BX.bind(select_IBLOCK_MODE[0], 'bxchange', check_showIBlocksIfOneIBlock)
}

function check_showIBlocksIfOneIBlock() {
    var form = BX('options'),
        select_IBLOCK_MODE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'IBLOCK_MODE'}
        }, true)[0],
        select_IBLOCK_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'IBLOCK_ID'}
        }, true)[0];

    if (select_IBLOCK_MODE.value === 'ONE') {
        BX.adjust(select_IBLOCK_ID, {props: {disabled: false}});
    } else {
        BX.adjust(select_IBLOCK_ID, {props: {disabled: true}});
        select_IBLOCK_ID.value = 0;
    }
}

function loadTypesInfoblocks() {
    var form = BX('options'),
        select_IBLOCK_TYPE_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'IBLOCK_TYPE_ID'}
        }, true);
    BX.bind(select_IBLOCK_TYPE_ID[0], 'bxchange', check_loadTypesInfoblocks)
}

function check_loadTypesInfoblocks() {
    var form = BX('options'),
        select_IBLOCK_TYPE_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'IBLOCK_TYPE_ID'}
        }, true)[0];

    BX.ajax.runAction('priceva:connector.api.ajax.getIblocks', {
        method: 'POST',
        data: {iblock_type_id: select_IBLOCK_TYPE_ID.value}
    })
        .then(function (response) {
            var select_IBLOCK_ID = BX.findChildren(form, {
                tag: 'select',
                attribute: {name: 'IBLOCK_ID'}
            }, true)[0];

            var length = select_IBLOCK_ID.options.length;
            for (var i = 0; i < length; i++) {
                if (select_IBLOCK_ID.options[i].value != 0) {
                    select_IBLOCK_ID.options[i] = null;
                }
            }
            /**
             * @type Array hash
             */
            var hash = response.data.iblocks;

            for (var j = 0 in hash) {
                var o = document.createElement('OPTION');
                o.innerHTML = hash[j];
                o.value = j;
                select_IBLOCK_ID.appendChild(o);
            }
        });
}

// Торговые предложения

function showIblocksIfTradeOffersEnable() {
    var form = BX('options'),
        select_TRADE_OFFERS_ENABLE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_ENABLE'}
        }, true)[0];
    BX.bind(select_TRADE_OFFERS_ENABLE, 'bxchange', check_showIblocksIfTradeOffersEnable)
}

function check_showIblocksIfTradeOffersEnable() {
    var form = BX('options'),
        select_TRADE_OFFERS_ENABLE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_ENABLE'}
        }, true)[0],
        select_TRADE_OFFERS_IBLOCK_TYPE_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_IBLOCK_TYPE_ID'}
        }, true)[0],
        select_TRADE_OFFERS_IBLOCK_MODE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_IBLOCK_MODE'}
        }, true)[0],
        select_TRADE_OFFERS_IBLOCK_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_IBLOCK_ID'}
        }, true)[0];

    if (select_TRADE_OFFERS_ENABLE.value === 'YES') {
        BX.adjust(select_TRADE_OFFERS_IBLOCK_TYPE_ID, {props: {disabled: false}});
        BX.adjust(select_TRADE_OFFERS_IBLOCK_MODE, {props: {disabled: false}});
        check_showTradeOffersIBlocksIfOneIBlock();
    } else {
        BX.adjust(select_TRADE_OFFERS_IBLOCK_TYPE_ID, {props: {disabled: true}});
        BX.adjust(select_TRADE_OFFERS_IBLOCK_MODE, {props: {disabled: true}});
        BX.adjust(select_TRADE_OFFERS_IBLOCK_ID, {props: {disabled: true}});

        select_TRADE_OFFERS_IBLOCK_TYPE_ID.value = 0;
        select_TRADE_OFFERS_IBLOCK_MODE.value = 0;
        select_TRADE_OFFERS_IBLOCK_ID.value = 0;

        var length = select_TRADE_OFFERS_IBLOCK_ID.options.length;
        for (var i = 0; i < length; i++) {
            if (select_TRADE_OFFERS_IBLOCK_ID.options[i].value != 0) {
                select_TRADE_OFFERS_IBLOCK_ID.options[i] = null;
            }
        }
    }
}

function showTradeOffersIBlocksIfOneIBlock() {
    var form = BX('options'),
        select_TRADE_OFFERS_IBLOCK_MODE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_IBLOCK_MODE'}
        }, true);
    BX.bind(select_TRADE_OFFERS_IBLOCK_MODE[0], 'bxchange', check_showTradeOffersIBlocksIfOneIBlock)
}

function check_showTradeOffersIBlocksIfOneIBlock() {
    var form = BX('options'),
        select_TRADE_OFFERS_IBLOCK_MODE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_IBLOCK_MODE'}
        }, true)[0],
        select_TRADE_OFFERS_IBLOCK_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_IBLOCK_ID'}
        }, true)[0];

    if (select_TRADE_OFFERS_IBLOCK_MODE.value === 'ONE') {
        BX.adjust(select_TRADE_OFFERS_IBLOCK_ID, {props: {disabled: false}});
    } else {
        BX.adjust(select_TRADE_OFFERS_IBLOCK_ID, {props: {disabled: true}});
        select_TRADE_OFFERS_IBLOCK_ID.value = 0;
    }
}

function loadTradeOffersTypesInfoblocks() {
    var form = BX('options'),
        select_TRADE_OFFERS_IBLOCK_TYPE_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_IBLOCK_TYPE_ID'}
        }, true);
    BX.bind(select_TRADE_OFFERS_IBLOCK_TYPE_ID[0], 'bxchange', check_loadTradeOffersTypesInfoblocks)
}

function check_loadTradeOffersTypesInfoblocks() {
    var form = BX('options'),
        select_TRADE_OFFERS_IBLOCK_TYPE_ID = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'TRADE_OFFERS_IBLOCK_TYPE_ID'}
        }, true)[0];

    BX.ajax.runAction('priceva:connector.api.ajax.getIblocks', {
        method: 'POST',
        data: {iblock_type_id: select_TRADE_OFFERS_IBLOCK_TYPE_ID.value}
    })
        .then(function (response) {
            var select_TRADE_OFFERS_IBLOCK_ID = BX.findChildren(form, {
                tag: 'select',
                attribute: {name: 'TRADE_OFFERS_IBLOCK_ID'}
            }, true)[0];

            var length = select_TRADE_OFFERS_IBLOCK_ID.options.length;
            for (var i = 0; i < length; i++) {
                if (select_TRADE_OFFERS_IBLOCK_ID.options[i].value != 0) {
                    select_TRADE_OFFERS_IBLOCK_ID.options[i] = null;
                }
            }
            /**
             * @type Array hash
             */
            var hash = response.data.iblocks;

            for (var j = 0 in hash) {
                var o = document.createElement('OPTION');
                o.innerHTML = hash[j];
                o.value = j;
                select_TRADE_OFFERS_IBLOCK_ID.appendChild(o);
            }
        });
}