function showDownloadsIfPriceva() {
    var form = BX('options');
    var select_SYNC_DOMINANCE = BX.findChildren(form, {
        tag: 'select',
        attribute: {name: 'SYNC_DOMINANCE'}
    }, true);
    BX.bind(select_SYNC_DOMINANCE[0], 'bxchange', check_showDownloadsIfPriceva)
}

function check_showDownloadsIfPriceva() {
    var form = BX('options'),
        select_SYNC_DOMINANCE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'SYNC_DOMINANCE'}
        }, true),
        select_DOWNLOAD_AT_TIME = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'DOWNLOAD_AT_TIME'}
        }, true);

    var s = select_DOWNLOAD_AT_TIME[0],
        d = select_SYNC_DOMINANCE[0];

    if (d.value !== '0') {
        BX.adjust(s, {props: {disabled: false}});
    } else {
        BX.adjust(s, {props: {disabled: true}});
        s.value = 0;
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
                if (select_IBLOCK_ID.options[i].value !== 0) {
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

            var select_TRADE_OFFERS_ENABLE = BX.findChildren(form, {
                tag: 'select',
                attribute: {name: 'TRADE_OFFERS_ENABLE'}
            }, true)[0];

            if (select_IBLOCK_TYPE_ID.value !== 'catalog') {
                select_TRADE_OFFERS_ENABLE.value = 'NO';
                BX.adjust(select_TRADE_OFFERS_ENABLE, {props: {disabled: true}});
            } else {
                BX.adjust(select_TRADE_OFFERS_ENABLE, {props: {disabled: false}});
            }
        });
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

function showClientCodeIfClientCode() {
    var form = BX('options');
    var select_SYNC_FIELD = BX.findChildren(form, {
        tag: 'select',
        attribute: {name: 'SYNC_FIELD'}
    }, true);
    BX.bind(select_SYNC_FIELD[0], 'bxchange', check_showClientCodeIfClientCode)
}

function check_showClientCodeIfClientCode() {
    var form = BX('options'),
        select_SYNC_FIELD = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'SYNC_FIELD'}
        }, true),
        select_CLIENT_CODE = BX.findChildren(form, {
            tag: 'select',
            attribute: {name: 'CLIENT_CODE'}
        }, true);

    var s = select_CLIENT_CODE[0],
        d = select_SYNC_FIELD[0];

    if (d.value === 'client_code') {
        BX.adjust(s, {props: {disabled: false}});
    } else {
        BX.adjust(s, {props: {disabled: true}});
        s.value = 0;
    }
}

debugger;
priceva_func_filter.forEach(function (element) {
        debugger;
        BX.ready(element);
    }
);