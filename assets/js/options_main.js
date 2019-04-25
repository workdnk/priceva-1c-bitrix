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