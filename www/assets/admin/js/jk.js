$(document).ready(function () {
    $.nette.init();
    Nette.addError = function (a, b) {
        var nextDiv = $(a).next("div");
        //For summernote editor
        if (nextDiv.hasClass("note-editor")) {
            a = nextDiv;
            $(a).inputClassError();
            $(a).errorMessageAfterElement(b);
            $(window).scrollTop(nextDiv.offset().top - 100);
        } else {
            errorProcess(a, b);
        }
    };
    $(function () {
        //init grido
        $('.grido').grido({
            ajax: false
        });
    });
    $('.select2').select2({
        placeholder: "Začněte psát zde.."
    });
    $(".fancybox").fancybox();
    $(".sortable").sortable();

    //delete a file
    $('a.delete-file').click(function (e) {
        e.preventDefault();
        if (confirm('Opravdu si přejete smazat soubor?') === true) {
            var href = $(this).attr('href');
            var wrapper = $(this).parent('.file-wrapper');
            $.nette.ajax({
                url: href
            }).done(function (r) {
                if (r.code === 0) {
                    wrapper.remove();
                    return;
                }
                alert(r.message);
            });
        }
    });
		
    //delete a file
    $('a.delete-birthday-discount').click(function (e) {
        e.preventDefault();
        if (confirm('Opravdu si přejete nastavit vyčerpání slevy?') === true) {
            var href = $(this).attr('href');
            var wrapper = $(this).parent('.delete-wrapper');
            $.nette.ajax({
                url: href
            }).done(function (r) {
								wrapper.html(r.message);
            });
        }
    });		
		
});