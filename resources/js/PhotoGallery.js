/* eslint-disable */

function photoGallery() {
    var elGallery = $('.js-photo-gallery'),
        elMainImage = elGallery.find('.js-main-image'),
        elthumbImage = elGallery.find('.js-thumb-image'),
        elthumb360view = elGallery.find('.js-thumb-360view');

    elGallery.on('click', '.js-thumb-image', function(){
        var target = $(this),
            elthumbLists = elGallery.find('li'),
            elthumbList = target.parent('li');

        elMainImage.attr('src', target.attr('href'));
        elthumbLists.removeClass('active');
        elthumbList.addClass('active');

        return false;
    });

    elGallery.on('click', '.js-thumb-360view', function(){
        alert('todo: doplnit reseni pro 360 pohled');
        return false;
    });
}

$(function(){

    photoGallery();

});
