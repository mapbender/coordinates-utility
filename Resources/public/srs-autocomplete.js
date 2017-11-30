$( ".srs-autocomplete input" )
    .on( "keydown", function( event ) {
        // don't navigate away from the field on tab when selecting an item
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
            event.preventDefault();
        }

        disableCopyPaste( event );
    })
    .autocomplete({
        source:function( request, response ) {
            $.getJSON( Routing.generate('srs_autocomplete'), {
                term: extractLast( request.term )
            }, response );
        },
        appendTo: "#srs-autocomplete-variants",
        minLength: 2,
        search: function() {
            var term = extractLast( this.value );
            if ( term.length < this.minLength ) {
                return false;
            }

        },
        focus: function() {
            // prevent value inserted on focus
            return false;
        },
        select: function( event, ui ) {
            var terms = split( this.value );
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push( ui.item.value );
            // add placeholder to get the comma-and-space at the end
            terms.push( "" );
            this.value = terms.join( ", " );
            return false;
        }
    });

/**
 * Split string by comma
 *
 * @param val
 * @returns {Array|*}
 */
function split( val ) {
    return val.split( /,\s*/ );
}

/**
 * Extract last element from the autocomplete field
 *
 * @param term
 * @returns {T}
 */
function extractLast( term ) {
    return split( term ).pop();
}

/**
 * Disable copy&paste to ensure valid SRSs
 *
 * @param event
 */
function disableCopyPaste( event ) {
    var ctrl_c = false;//(event.ctrlKey === true && event.which == '67');
    var ctrl_v = (event.ctrlKey === true && event.which == '86');

    if ( ctrl_c || ctrl_v ) {
        event.preventDefault();
    }
}