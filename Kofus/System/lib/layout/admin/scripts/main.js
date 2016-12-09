$(function () {
    $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
    $('.tree li.parent_li > span').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > i').addClass('glyphicon-menu-right').removeClass('glyphicon-menu-down');
        } else {
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find(' > i').addClass('glyphicon-menu-down').removeClass('glyphicon-menu-right');
        }
        e.stopPropagation();
    });
    
    $('input.datepicker').datepicker({
    	language: $('input.datepicker').data('language'),
    	todayHighlight: true,
    	autoclose: true
    });
    !function(a){a.fn.datepicker.dates.de={days:["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag","Sonntag"],daysShort:["Son","Mon","Die","Mit","Don","Fre","Sam","Son"],daysMin:["So","Mo","Di","Mi","Do","Fr","Sa","So"],months:["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"],monthsShort:["Jan","Feb","Mär","Apr","Mai","Jun","Jul","Aug","Sep","Okt","Nov","Dez"],today:"Heute",clear:"Löschen",weekStart:1,format:"dd.mm.yyyy"}}(jQuery);    
    
    
    $('select.node-select').select2({
    	placeholder: {id: 0, text: 'Bitte mind. 3 Zeichen eintippen...'},
    	theme: 'bootstrap',
    	language: {inputTooLong:function(e){var t=e.input.length-e.maximum;return"Bitte "+t+" Zeichen weniger eingeben"},inputTooShort:function(e){var t=e.minimum-e.input.length;return"Bitte noch "+t+" Zeichen eingeben"},loadingMore:function(){return"Lade mehr Ergebnisse…"},maximumSelected:function(e){var t="Sie können nur "+e.maximum+" Eintr";return e.maximum===1?t+="ag":t+="äge",t+=" auswählen",t},noResults:function(){return"Keine Übereinstimmungen gefunden"},searching:function(){return"Suche…"}},
    	minimumInputLength: 3,
    	ajax: {

    		dataType: 'json',
    		delay: 250,
    		data: function(params) {
    			return {q: params.term, page: params.page };
    		},
    		//cache: true
    	},
    });    
    
    /* $('input.switch').bootstrapSwitch({}); */
});

