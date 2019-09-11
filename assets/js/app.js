var $ = require('jquery');
require('bootstrap');

require('../css/global.scss');

import * as YASQE from "yasgui-yasqe";
import 'yasgui-yasqe/dist/yasqe.css';

$(document).ready(function() {
    YASQE.defaults.syntaxErrorCheck = false;
    YASQE.defaults.createShareLink = null;
    var display = YASQE.fromTextArea($('#rdf-display')[0]);


    $('#rdf-accordion').collapse({
        // toggle: true
    }).on('shown.bs.collapse', function () {
        display.refresh();
    });

    $('.rdf-iri').each(function(index, predicate) {
        $.getJSON("/object", {'iri': $(this).attr('data-iri')}, function (data) {
            var label = data.label;
            console.log(label);

            $(predicate).children('.rdf-label').html(label);
        });
    });
});