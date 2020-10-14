class Scroll_content {

    constructor(site_url, page_content, limit, type=null, query=null) {
        this.site_url = site_url;
        this.page_content = page_content;
        this.type = type;
        this.query = query;
        this.limit = limit;
        $('.ajax-loader').hide();
    }
        
    getCursorXY(e) {
        document.getElementById('cursorX').value = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
        document.getElementById('cursorY').value = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
    }
    windowOnScroll() {
        window.addEventListener("scroll", function() {
            var pagination = document.getElementById("pagination");
            var elementTarget = document.getElementById("post-list_items");
            var load_more  = document.getElementById("load_more");
            var bounding = load_more.getBoundingClientRect();

            if(this.type =="search") {

                var url_get= this.site_url+'/?action=json&page='+pagination.value+'&limit='+ this.limit + '&q='+ this.query+'&page_content='+this.page_content;

            }
            else if(this.type =="category") {
                var url_get = this.site_url+'/?action=json&page='+pagination.value+'&limit='+ this.limit + '&category='+ this.query+'&page_content='+this.page_content;
            }
            else {
                var url_get = this.site_url+'/?action=json&page='+pagination.value+'&limit='+ this.limit + '&page_content='+this.page_content;

            }
            
            // Log the results
            //console.log(bounding);
            if (bounding.top >= 0 && bounding.left >= 0 && bounding.right <= (window.innerWidth || document.documentElement.clientWidth) && bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight)) {
                $.ajax({
                    url:  url_get,
                    type: "get",
                    async: false,
                    beforeSend: function ()
                    {
                        $('.ajax-loader').show();
                        $('.load_more').hide();
                    },
                    success: function (data) {
                        pagination.value = parseInt(pagination.value,10) +1;

                        var json_data = JSON.parse(data);
                        
                        var txt1;
                        
                        for(var i = 0; i < json_data.length; i++) {
                            var object = json_data[i];
                                                    
                            var $item;
                            
                            if(object.type_array == 1) {
                                
                                //<!-- Small Card With Image -->
                                $item = $('<div class="card card_small_with_image grid-item"><img class="card-img-top" style="min-height:150px;" src="' + object.thumb + '" alt=""><div class="card-body"><div class="card-title card-title-small"><a href="' + object.url + '">' + object.title + '</a></div><small class="post_meta"><a href="' + object.url + '">' + object.author + '</a><span>' + object.published + '</span></small></div></div>');

                            }
                            else if(object.type_array == 2) {
                                
                                //<!-- Small Card Without Image -->
                                $item = $('<div class="card card_default card_small_no_image grid-item"><div class="card-body"><div class="card-title card-title-small"><a href="' + object.url + '">' + object.title + '</a></div><small class="post_meta"><a href="' + object.url + '">' + object.author + '</a><span>' + object.published + '</span></small></div></div>');

                            }
                            else if(object.type_array == 3) {

                                //<!-- Small Card With Image -->
                                $item = $('<div class="card card_small_with_image grid-item"><img class="card-img-top"  style="min-height:150px;" src="' + object.thumb + '" alt=""><div class="card-body"><div class="card-title card-title-small"><a href="' + object.url + '">' + object.title + '</a></div><small class="post_meta"><a href="' + object.url + '">' + object.author + '</a><span>' + object.published + '</span></small></div></div>');

                            }
                            else if(object.type_array == 4) {

                                //<!-- Small Card With Background -->
                                $item = $('<div class="card card_default card_small_with_background grid-item"><div class="card_background" style="background-image:url(' + object.thumb + ')"></div><div class="card-body"><div class="card-title card-title-small"><a href="' + object.url + '">' + object.title + '</a></div><small class="post_meta"><a href="' + object.url + '">' + object.author + '</a><span>' + object.published + '</span></small></div></div>');

                            }
                            else if(object.type_array == 5) {

                                //<!-- Small Card With Image -->
                                $item = $('<div class="card card_small_with_image grid-item"><img class="card-img-top"  style="min-height:150px;" src="' + object.thumb + '" alt=""><div class="card-body"><div class="card-title card-title-small"><a href="' + object.url + '">' + object.title + '</a></div><small class="post_meta"><a href="' + object.url + '">' + object.author + '</a><span>' + object.published + '</span></small></div></div>');

                            }
                            else if(object.type_array == 6) {

                                //<!-- Small Card Without Image -->
                                $item = $('<div class="card card_default card_small_no_image grid-item"><div class="card-body"><div class="card-title card-title-small"><a href="' + object.url + '">' + object.title + '</a></div><small class="post_meta"><a href="' + object.url + '">' + object.author + '</a><span>' + object.published + '</span></small></div></div>');

                            }
                            else if(object.type_array == 7) {
                                
                                //<!-- Small Card With Background -->
                                $item = $('<div class="card card_default card_small_with_background grid-item"><div class="card_background" style="background-image:url(' + object.thumb + ')"></div><div class="card-body"><div class="card-title card-title-small"><a href="' + object.url + '">' + object.title + '</a></div><small class="post_meta"><a href="' + object.url + '">' + object.author + '</a><span>' + object.published + '</span></small></div></div>');

                            }
                            
                            //https://masonry.desandro.com/methods.html
                            
                            //$grid.masonry('reloadItems')
                                
                            $('#post-list_items').prepend($item).masonry( 'appended', $item );
                            
                            //$('#post-list_items').masonry('appended', $item)
                            
                                                
                        }
                        
                        
                        setTimeout(function() {
                            $('.ajax-loader').hide();
                            $('.load_more').show();
                        }, 1000);
        
                        
                    }
            });       
            }
            
        });
    }

}