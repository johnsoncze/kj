/* eslint-disable */

function expandList() {
    var elList = $('.js-expand-list');

    elList.on('click', '.js-toggle-switch', function(){
        var target = $(this),
            list = target.parents('.js-toggle'),
            content = list.find('.js-toggle-content');

        list.toggleClass('Expand-item--open');

        return false;
    });
}

$(function(){

    expandList();

});
