$(document).ready(function(){
    $('#viewInsights').change(function(){
        $('#insightsResponse').html('<img src="img/loading.gif">');
        $.ajax({
            type: 'POST',
            url: 'insights.php',
            data: {
                pageID : $('#viewInsights').val()
            },
            success: function(response) {
                $('#insightsResponse').html(response);
                $('#saveInsightsDb').on('click',function(){
                    $('#dbDataResponse').html('<img src="img/loading.gif">');
                    $.ajax({
                        type: 'POST',
                        url: 'db_handler.php',
                        data: {
                            db_data : $('#dbData').val(),
                            page_id : $('#viewInsights').val()
                        },
                        success: function(response) {
                            $('#dbDataResponse').html(response);
                        }
                    });
                });
                $('#compareInsights').on('click',function(){
                    $('#compareInsightsContainer').html('<img src="img/loading.gif">');
                    $.ajax({
                        type: 'POST',
                        url: 'db_handler.php',
                        data: {
                            compare_entry : $('#compareDates').val()
                        },
                        success: function(response) {
                            var compareFirstTop = $('#insightsResponse').position().top;
                            $('#compareInsightsContainer').css('top',compareFirstTop);
                            $('#compareInsightsContainer').html(response);
                        }
                    })
                })
            }
        });
        return false;
    });
    $('#searchbox').change(function(){
        var searchbox = $(this).val();
        var dataString = 'searchword=' + searchbox;
        if(searchbox !== ""){
            $.ajax({
                type: 'POST',
                url: 'search.php',
                data: dataString,
                success: function(response){
                    $('#display').html(response);
                    $('.publicPage').on('click',function(){
                        var thisBtn = $(this);
                        thisBtn.next().html('<img src="img/loading.gif">');
                        $.ajax({
                            type: 'POST',
                            url: 'public_pages.php',
                            data: {
                                public_page_id : thisBtn.prev().val()
                            },
                            success: function(response){
                                thisBtn.next().html(response);
                            }
                        });
                        return false;
                    })
                }
            });
        }
        return false;
    });
});