function initRatings(){    
    $('.ratings_stars').live('mouseover', function() {  
        $(this).prevAll().andSelf().addClass('ratings_over');  
        $(this).nextAll().removeClass('ratings_vote');  
    });
    
    $('.ratings_stars').live('mouseout', function() {  
        $(this).prevAll().andSelf().removeClass('ratings_over');  
        set_votes($(this).parent(), false);  
    });
        
    $('.ratings_stars').live('click', function() {   
        var widget = $(this).parent();  
      
        var clicked_data = {  
            rating : $(this).data('rating'),  
            id : widget.data('imdbid')  
        };  
                
        $.post(
            '/ajax/vote',  
            clicked_data,  
            function(data) {  
                if( data.error ){
                    widget.data( 'rating', widget.data('rating') );  
                } else {
                    widget.data( 'rating', data.rating );  
                }
                set_votes(widget, true, data.html);
                
                try {
                    loadSuggestions(1, true, widget);
                    
                } catch(err) {
                    // Handle error(s) here
                }
            },  
            'json'  
        );
    });  
}

function setAllVotes(){
    $('.rate_widget').each( function(i){ 
        set_votes($(this));
    });
}

function set_votes(widget, refresh, html) {        
    var rating = widget.data('rating');
    
    widget.find('.star_' + rating).prevAll().andSelf().addClass('ratings_vote');  
    widget.find('.star_' + rating).nextAll().removeClass('ratings_vote');

    if( refresh ){
        if( widget.closest('table').is('#filmsTable') ){
            var td = widget.closest('td');
            var aPos = oTable.fnGetPosition( td[0] );        
            oTable.fnUpdate( rating, aPos[0], 6 );
            oTable.fnUpdate( html, aPos[0], 7 );
        }
    }
    

}  